<?php

/**
 * Export automatique CSV + envoi email à Dina
 * Version corrigée pour LOCAL avec MailHog
 */

require_once(__DIR__ . '/../../../../config.php');
// require_once($CFG->libdir . '/phpmailer/moodlephpmailer.php');

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

file_put_contents($log_file, "\n=== EXPORT AUTO " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);

//  EXACTEMENT LA MÊME REQUÊTE QUE bulkexport.php
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

    $progression = isset($row->progression) && $row->progression !== null ? intval($row->progression) : 0;

    $inscription = new stdClass();
    $inscription->idnumber = $row->course_idnumber;
    $inscription->email = $row->email;
    $inscription->firstname = $row->firstname;
    $inscription->lastname = $row->lastname;
    $inscription->liccod = $row->username;
    $inscription->evtsq = $row->group_idnumber;
    $inscription->tauxelearning = $progression;

    $inscriptions[] = $inscription;
}
$recordset->close();

file_put_contents($log_file, "Found " . count($inscriptions) . " inscriptions\n", FILE_APPEND);

//  GÉNÈRE LE CSV (identique à bulkexport.php)
$filename = 'export_inscriptions_' . date('Y-m-d_H-i-s') . '.csv';
$filepath = '/tmp/' . $filename;

$file = fopen($filepath, 'w');

fputcsv($file, [
    'ID Cours',
    'Email',
    'Code Licence',
    'N° Groupe',
    'Taux E-learning',
    'Taux Présentiel',
    'Taux QCM',
    'Réussite QCM',
    'Date'
]);

foreach ($inscriptions as $inscription) {
    fputcsv($file, [
        $inscription->idnumber,
        $inscription->email,
        $inscription->liccod,
        $inscription->evtsq,
        $inscription->tauxelearning,
        'N/A',
        'N/A',
        'N/A',
        date('Y-m-d H:i:s')
    ]);
}

fclose($file);

file_put_contents($log_file, "CSV generated: $filepath\n", FILE_APPEND);

//  ENVOI PAR EMAIL AVEC LA FONCTION MOODLE (compatible MailHog)
try {
    // Détermine l'environnement
    $environment = 'PROD';
    if (strpos($CFG->wwwroot, 'ffa-uat') !== false) {
        $environment = 'UAT';
    } elseif (strpos($CFG->wwwroot, 'ffa-dev') !== false) {
        $environment = 'DEV';
    } elseif (strpos($CFG->wwwroot, 'moodle-ffa:8888') !== false) {
        $environment = 'LOCAL';
    }

    // Créer un utilisateur fictif "noreply"
    $from = new stdClass();
    $from->email = 'noreply@formation360.athle.fr';
    $from->firstname = 'Formation';
    $from->lastname = 'FFA';
    $from->maildisplay = true;
    $from->mailformat = 1;
    $from->id = -99;

    // Destinataire Dina
    $to = new stdClass();
    $to->email = 'dina.toledano@athle.fr';
    $to->firstname = 'Dina';
    $to->lastname = 'Toledano';
    $to->maildisplay = true;
    $to->mailformat = 1;
    $to->id = -98;

    $subject = "[$environment] Export FFA - " . date('d/m/Y');

    $messagetext = "Bonjour Dina,\n\n"
        . "Veuillez trouver ci-joint l'export quotidien des inscriptions.\n\n"
        . "Statistiques :\n"
        . "- Nombre d'inscriptions : " . count($inscriptions) . "\n"
        . "- Date de génération : " . date('d/m/Y à H:i:s') . "\n"
        . "- Environnement : $environment\n\n"
        . "Cordialement,\n"
        . "Système automatique Formation FFA";

    $messagehtml = nl2br($messagetext);

    //  UTILISE LA FONCTION MOODLE email_to_user avec attachement
    $attachment = $filepath;
    $attachname = $filename;

    $result = email_to_user(
        $to,
        $from,
        $subject,
        $messagetext,
        $messagehtml,
        $attachment,
        $attachname
    );

    if ($result) {
        file_put_contents($log_file, "✅ Email sent successfully to " . $to->email . "\n", FILE_APPEND);
        $success = true;
    } else {
        file_put_contents($log_file, "❌ Email failed for " . $to->email . "\n", FILE_APPEND);
        $success = false;
    }
} catch (Exception $e) {
    file_put_contents($log_file, " Error: " . $e->getMessage() . "\n", FILE_APPEND);
    $success = false;
}

// Nettoie le fichier temporaire
unlink($filepath);

$execution_time = round(microtime(true) - $start_time, 2);

file_put_contents($log_file, "Total execution time: {$execution_time}s\n", FILE_APPEND);
file_put_contents($log_file, "=== END ===\n\n", FILE_APPEND);

echo json_encode([
    'success' => $success,
    'total' => count($inscriptions),
    'filename' => $filename,
    'recipient' => $to->email,
    'environment' => $environment,
    'execution_time' => $execution_time . 's'
]);
