<?php

/**
 * Envoi automatique des résultats de formation vers l'API FFA
 *
 * Pour chaque inscription Moodle, appelle :
 * https://webservicesffa.athle.fr/FFa_Smartch/Maj_Resultat_Formation.aspx
 *
 * Paramètres API :
 *   dbIDCours  = course idnumber (ex: M00001)
 *   dbEmail    = email utilisateur
 *   dbLicence  = username (code licence)
 *   dbGroupe   = group idnumber (ex: 39826)
 *   dbTauxE    = taux de complétion e-learning (%)
 *   dbDate     = date au format dd/MM/yyyy HH:mm
 *
 * Déclenché par la tâche planifiée theme_remui\task\ffa_daily_export (7h/jour)
 */

require_once(__DIR__ . '/../../../../config.php');

global $DB, $CFG;

// Sécurité : token secret
$secret_token = 'ffa_export_secret_2025';
$provided_token = $_GET['token'] ?? '';

if ($provided_token !== $secret_token) {
    http_response_code(401);
    die(json_encode(['error' => 'Unauthorized']));
}

$start_time = microtime(true);
$log_file = '/tmp/ffa_auto_export.log';

// Rotation du log
if (file_exists($log_file) && filesize($log_file) > 5242880) {
    rename($log_file, $log_file . '.' . date('Ymd_His') . '.old');
}

file_put_contents($log_file, "\n=== EXPORT API FFA " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);

// Détection environnement
$environment = 'PROD';
if (strpos($CFG->wwwroot, 'ffa-uat') !== false) {
    $environment = 'UAT';
} elseif (strpos($CFG->wwwroot, 'ffa-dev') !== false) {
    $environment = 'DEV';
} elseif (strpos($CFG->wwwroot, 'moodle-ffa:8888') !== false) {
    $environment = 'LOCAL';
}

// URL de l'API FFA
$ffa_api_base = 'https://webservicesffa.athle.fr/FFa_Smartch/Maj_Resultat_Formation.aspx';

// Même requête SQL que bulkexport.php
$sql = "SELECT DISTINCT
    u.id as userid,
    u.email,
    u.username,
    u.firstname,
    u.lastname,
    c.id as courseid,
    c.idnumber as course_idnumber,
    (
        SELECT g2.idnumber
        FROM mdl_groups_members gm2
        JOIN mdl_groups g2 ON g2.id = gm2.groupid
        WHERE gm2.userid = u.id
        AND g2.courseid = c.id
        AND g2.idnumber IS NOT NULL
        AND g2.idnumber != ''
        ORDER BY gm2.timeadded DESC
        LIMIT 1
    ) as group_idnumber,
    (
        SELECT ROUND(
            (COUNT(CASE WHEN cmc.completionstate > 0 THEN 1 END) * 100.0) /
            NULLIF(COUNT(DISTINCT cm.id), 0),
            0
        )
        FROM mdl_course_modules cm
        LEFT JOIN mdl_course_modules_completion cmc
            ON cmc.coursemoduleid = cm.id
            AND cmc.userid = u.id
        WHERE cm.course = c.id
        AND cm.completion > 0
    ) as progression
FROM mdl_user u
JOIN mdl_user_enrolments ue ON ue.userid = u.id
JOIN mdl_enrol e ON e.id = ue.enrolid
JOIN mdl_course c ON c.id = e.courseid
WHERE c.visible = 1
AND c.format != 'site'
AND u.username REGEXP '^[0-9]+$'
AND EXISTS (
    SELECT 1
    FROM mdl_groups_members gm3
    JOIN mdl_groups g3 ON g3.id = gm3.groupid
    WHERE gm3.userid = u.id
    AND g3.courseid = c.id
    AND g3.idnumber IS NOT NULL
    AND g3.idnumber != ''
)
ORDER BY u.id, c.id";

file_put_contents($log_file, "Executing SQL...\n", FILE_APPEND);

$recordset = $DB->get_recordset_sql($sql);
$inscriptions = [];

foreach ($recordset as $row) {
    if (empty($row->group_idnumber)) {
        continue;
    }

    $inscription = new stdClass();
    $inscription->idnumber = $row->course_idnumber;
    $inscription->email = $row->email;
    $inscription->firstname = $row->firstname;
    $inscription->lastname = $row->lastname;
    $inscription->liccod = $row->username;
    $inscription->evtsq = $row->group_idnumber;
    $inscription->tauxelearning = isset($row->progression) && $row->progression !== null ? intval($row->progression) : 0;

    $inscriptions[] = $inscription;
}
$recordset->close();

$total = count($inscriptions);
file_put_contents($log_file, "Found {$total} inscriptions\n", FILE_APPEND);

// --- APPELS API FFA ---
$now = date('d/m/Y H:i');
$successes = [];
$errors = [];

foreach ($inscriptions as $i => $insc) {
    $params = [
        'dbIDCours' => $insc->idnumber,
        'dbEmail'   => $insc->email,
        'dbLicence' => $insc->liccod,
        'dbGroupe'  => $insc->evtsq,
        'dbTauxE'   => $insc->tauxelearning,
        'dbDate'    => $now,
    ];

    $url = $ffa_api_base . '?' . http_build_query($params, '', '&');

    // Log URL pour les 3 premiers appels (debug)
    if ($i < 3) {
        file_put_contents($log_file, "URL: {$url}\n", FILE_APPEND);
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    $num = $i + 1;

    if ($curl_error) {
        $error_msg = "[{$num}/{$total}] CURL ERROR {$insc->email} (cours {$insc->idnumber}): {$curl_error}";
        file_put_contents($log_file, $error_msg . "\n", FILE_APPEND);
        $errors[] = $error_msg;
    } elseif ($http_code >= 400) {
        $error_msg = "[{$num}/{$total}] HTTP {$http_code} {$insc->email} (cours {$insc->idnumber}): {$response}";
        file_put_contents($log_file, $error_msg . "\n", FILE_APPEND);
        $errors[] = $error_msg;
    } else {
        // L'API FFA retourne un JSON mal formé avec du JSON non-échappé dans msg :
        // { "retour": -1, "msg": "[{"retour": 1, "msg": ""}]"}
        // On extrait le retour interne via regex
        $ffa_retour = null;
        $ffa_msg = '';

        // Essayer json_decode direct d'abord (au cas où le format change)
        $ffa_result = json_decode($response, true);

        if ($ffa_result !== null) {
            // JSON valide
            if (isset($ffa_result[0]['retour'])) {
                $ffa_retour = $ffa_result[0]['retour'];
                $ffa_msg = $ffa_result[0]['msg'] ?? '';
            } elseif (isset($ffa_result['retour'])) {
                $ffa_retour = $ffa_result['retour'];
                $ffa_msg = $ffa_result['msg'] ?? '';
            }
        }

        // Si json_decode échoue (JSON mal formé), extraire via regex
        if ($ffa_retour === null) {
            // Chercher le pattern [{"retour": X, "msg": "..."}] dans la réponse
            if (preg_match('/\[\{"retour":\s*(\d+),\s*"msg":\s*"([^"]*)"\}\]/', $response, $matches)) {
                $ffa_retour = intval($matches[1]);
                $ffa_msg = $matches[2];
            // Aussi chercher {"retour": X, "msg": "..."} simple
            } elseif (preg_match('/"retour":\s*(\d+)/', $response, $matches)) {
                $ffa_retour = intval($matches[1]);
                if (preg_match('/"msg":\s*"([^"]*)"/', $response, $msg_matches)) {
                    $ffa_msg = $msg_matches[1];
                }
            }
        }

        if ($ffa_retour !== null && $ffa_retour == 1) {
            file_put_contents($log_file, "[{$num}/{$total}] OK {$insc->email} (cours {$insc->idnumber})\n", FILE_APPEND);
            $successes[] = $insc;
        } else {
            $ffa_msg_clean = trim($ffa_msg) ?: 'pas de message';
            $error_msg = "[{$num}/{$total}] API FFA retour={$ffa_retour} {$insc->email} (cours {$insc->idnumber}): {$ffa_msg_clean}";
            file_put_contents($log_file, $error_msg . "\n", FILE_APPEND);
            $errors[] = $error_msg;
        }
    }
}

file_put_contents($log_file, "API calls done: " . count($successes) . " OK, " . count($errors) . " errors\n", FILE_APPEND);

// --- EMAIL RAPPORT ---
$email_sent = false;

try {
    $from = new stdClass();
    $from->email = 'noreply@formation360.athle.fr';
    $from->firstname = 'Formation';
    $from->lastname = 'FFA';
    $from->maildisplay = true;
    $from->mailformat = 1;
    $from->id = -99;

    $nb_successes = count($successes);
    $nb_errors = count($errors);
    $status_label = empty($errors) ? "SUCCES" : "ERREURS DETECTEES";

    $subject = "[{$environment}] Rapport synchronisation Formation FFA - " . date('d/m/Y');

    $messagetext = "Bonjour,\n\n"
        . "Voici le rapport quotidien de synchronisation des resultats de formation entre la plateforme Moodle et le systeme FFA.\n\n"
        . "Statut : {$status_label}\n"
        . "-----------------------------------\n"
        . "Date d'execution  : " . date('d/m/Y H:i:s') . "\n"
        . "Environnement     : {$environment}\n"
        . "-----------------------------------\n\n"
        . "Resultats de la synchronisation :\n"
        . "- Nombre total d'inscriptions traitees : {$total}\n"
        . "- Envois reussis vers l'API FFA        : {$nb_successes}\n"
        . "- Erreurs                              : {$nb_errors}\n\n"
        . "Pour chaque inscription, les donnees suivantes ont ete transmises a la FFA :\n"
        . "  - Identifiant du cours (dbIDCours)\n"
        . "  - Email de l'utilisateur (dbEmail)\n"
        . "  - Code licence (dbLicence)\n"
        . "  - Numero de groupe (dbGroupe)\n"
        . "  - Taux de completion e-learning en % (dbTauxE)\n"
        . "  - Date de synchronisation (dbDate)\n";

    if (!empty($errors)) {
        $messagetext .= "\n--- DETAIL DES ERREURS ---\n";
        foreach ($errors as $err) {
            $messagetext .= "  - {$err}\n";
        }
        $messagetext .= "\nMerci de signaler ces erreurs a l'equipe technique.\n";
    } else {
        $messagetext .= "\nAucune erreur detectee. Toutes les donnees ont ete transmises avec succes.\n";
    }

    $messagetext .= "\n---\nCe rapport est genere automatiquement chaque jour a 7h00.\nPlateforme : Formation FFA (Moodle)\n";
    $messagehtml = nl2br(htmlspecialchars($messagetext));

    // daviddumas@smartch.fr : recoit le rapport tous les jours
    $to_david = new stdClass();
    $to_david->email = 'daviddumas@smartch.fr';
    $to_david->firstname = 'David';
    $to_david->lastname = 'Dumas';
    $to_david->maildisplay = true;
    $to_david->mailformat = 1;
    $to_david->id = -97;

    $result = email_to_user($to_david, $from, $subject, $messagetext, $messagehtml);
    if ($result) {
        file_put_contents($log_file, "Report email sent to {$to_david->email}\n", FILE_APPEND);
        $email_sent = true;
    } else {
        file_put_contents($log_file, "Report email FAILED for {$to_david->email}\n", FILE_APPEND);
    }

    // maunick@smartch.fr : recoit le rapport uniquement en cas d'erreurs
    if (!empty($errors)) {
        $to_jo = new stdClass();
        $to_jo->email = 'maunick@smartch.fr';
        $to_jo->firstname = 'Jo';
        $to_jo->lastname = 'Maunick';
        $to_jo->maildisplay = true;
        $to_jo->mailformat = 1;
        $to_jo->id = -98;

        $result = email_to_user($to_jo, $from, $subject, $messagetext, $messagehtml);
        if ($result) {
            file_put_contents($log_file, "Error report email sent to {$to_jo->email}\n", FILE_APPEND);
        } else {
            file_put_contents($log_file, "Error report email FAILED for {$to_jo->email}\n", FILE_APPEND);
        }
    }
} catch (Exception $e) {
    file_put_contents($log_file, "Report email error: " . $e->getMessage() . "\n", FILE_APPEND);
}

$execution_time = round(microtime(true) - $start_time, 2);

file_put_contents($log_file, "Total execution time: {$execution_time}s\n", FILE_APPEND);
file_put_contents($log_file, "=== END ===\n\n", FILE_APPEND);

echo json_encode([
    'success'        => empty($errors),
    'total'          => $total,
    'sent'           => count($successes),
    'errors'         => count($errors),
    'error_details'  => $errors,
    'report_emailed' => $email_sent ?? false,
    'environment'    => $environment,
    'execution_time' => $execution_time . 's',
]);
