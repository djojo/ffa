<?php

require_once(__DIR__ . '/../../../../config.php');
require_once('../utils.php');

require_login();

global $USER, $DB, $CFG;

// Vérifier si on demande le téléchargement du CSV
if (isset($_GET['download']) && $_GET['download'] === 'csv') {

    //  DEBUG: Fichier de log avec rotation
    $debug_file = '/tmp/bulkexport_debug.log';
    
    // Rotation si fichier > 1 MB
    if (file_exists($debug_file) && filesize($debug_file) > 1048576) {
        rename($debug_file, $debug_file . '.' . date('Ymd_His') . '.old');
    }
    
    file_put_contents($debug_file, "\n=== DEBUG " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);

    //  REQUÊTE SQL OPTIMISÉE avec calcul des taux de complétion
    $sql = "SELECT DISTINCT
        u.id as userid,
        u.email,
        u.username,
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

    file_put_contents($debug_file, "Executing SQL query...\n", FILE_APPEND);
    $start_time = microtime(true);

    //  FIX : Utilise get_recordset_sql pour ne pas perdre de lignes
    $recordset = $DB->get_recordset_sql($sql);
    $results = [];
    foreach ($recordset as $row) {
        $results[] = $row;
    }
    $recordset->close();

    $query_time = round(microtime(true) - $start_time, 2);
    file_put_contents($debug_file, "SQL executed in {$query_time}s, returned: " . count($results) . " rows\n", FILE_APPEND);

    $inscriptions = [];
    $skipped = 0;

    foreach ($results as $row) {
        if (empty($row->group_idnumber)) {
            $skipped++;
            file_put_contents($debug_file, "SKIPPED: userid={$row->userid}, email={$row->email}, course={$row->course_idnumber}\n", FILE_APPEND);
            continue;
        }

        //  Utilise la progression calculée en SQL (ou 0 si NULL)
        $progression = isset($row->progression) && $row->progression !== null ? intval($row->progression) : 0;

        $inscription = new stdClass();
        $inscription->idnumber = $row->course_idnumber;
        $inscription->email = $row->email;
        $inscription->liccod = $row->username;
        $inscription->evtsq = $row->group_idnumber;
        $inscription->tauxelearning = $progression;
        $inscription->tauxpresence = "N/A";
        $inscription->tauxqcm = "N/A";
        $inscription->qcm = "N/A";
        $inscription->date = date('Y-m-d H:i:s');

        $inscriptions[] = $inscription;
    }

    file_put_contents($debug_file, "Processed: " . count($inscriptions) . " inscriptions\n", FILE_APPEND);
    file_put_contents($debug_file, "Skipped: $skipped inscriptions\n", FILE_APPEND);
    file_put_contents($debug_file, "CSV will have: " . (count($inscriptions) + 1) . " lines (including header)\n", FILE_APPEND);

    // Générer le fichier CSV
    $filename = 'export_inscriptions_' . date('Y-m-d_H-i-s') . '.csv';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    fputcsv($output, [
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
        fputcsv($output, [
            $inscription->idnumber,
            $inscription->email,
            $inscription->liccod,
            $inscription->evtsq,
            $inscription->tauxelearning,
            $inscription->tauxpresence,
            $inscription->tauxqcm,
            $inscription->qcm,
            $inscription->date
        ]);
    }

    fclose($output);
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Export des inscriptions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .export-container {
            text-align: center;
            background: #f9f9f9;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-download {
            background-color: #007cba;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }
        .btn-download:hover {
            background-color: #005a87;
            text-decoration: none;
            color: white;
        }
        h2 { color: #333; margin-bottom: 20px; }
        p { color: #666; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="export-container">
        <h2>Export des inscriptions</h2>
        <p>Cliquez sur le bouton ci-dessous pour télécharger un fichier CSV contenant toutes les inscriptions de la plateforme.</p>
        <a href="?download=csv" class="btn-download">Télécharger le fichier CSV</a>
    </div>
</body>
</html>
