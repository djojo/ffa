<?php

require_once(__DIR__ . '/../../../../config.php');
require_once('../utils.php');

require_login();

global $USER, $DB, $CFG;

// Vérifier si on demande le téléchargement du CSV
if (isset($_GET['download']) && $_GET['download'] === 'csv') {
    
    // Calculer les inscriptions
    $users = $DB->get_records_sql('SELECT u.id, u.email, u.username FROM mdl_user u', null);
    
    $inscriptions = [];
    
    foreach ($users as $user) {
    
        $querycourses = 'SELECT DISTINCT c.id, c.shortname, c.fullname, c.summary, cc.name as category, e.enrolstartdate as dateadded
        FROM mdl_user_enrolments ue
        JOIN mdl_enrol e ON e.id = ue.enrolid
        JOIN mdl_course c ON c.id = e.courseid
        JOIN mdl_course_categories cc ON cc.id = c.category
        WHERE ue.userid = ' . $user->id . '
        AND c.visible = 1
        AND c.format != "site"';
    
        $courses = $DB->get_records_sql($querycourses, null);
    
        foreach ($courses as $course) {
    
            //on va chercher l'idnumber du groupe via la session de l'utilisateur sur le cours
            $group = $DB->get_record_sql('SELECT g.id, g.idnumber
            FROM mdl_groups g 
            JOIN mdl_groups_members gm ON gm.groupid = g.id
            JOIN mdl_course c ON c.id = g.courseid
            WHERE c.id = ' . $course->id . '
            AND gm.userid = ' . $user->id, null);
    
            if(!$group) {
                continue;
            }
    
            $scormScoreMoyen = 0;
            $scormScore = 0;
            $countScorms = 0;
    
            // le taux de complétion des activités 
            $progression = getCompletionPourcent($course->id, $user->id);
    
            //on va chercher toutes les activités scorm du cours
            $activities = getCourseActivitiesStatsElearning($course->id);
    
    
            //pour chaque scorm, on va chercher le score
            foreach($activities as $activity) {
                $scormScore += reportGetScormGrade($user->id, $activity->activityid);
                $countScorms++;
            }
    
            if($countScorms > 0) {
                $scormScoreMoyen = $scormScore / $countScorms;
            }
    
            $inscription = new stdClass();
            $inscription->idnumber = $course->idnumber; //id du cours
            $inscription->email = $user->email; //email de l'utilisateur
            $inscription->liccod = $user->username; //code de licence
            $inscription->evtsq = $group->idnumber; // N° groupe
            $inscription->tauxelearning = $progression; // Taux de complétion elearning
            $inscription->tauxpresence = 0; // Taux de complétion présentiel
            $inscription->tauxqcm = $scormScoreMoyen; // Taux de complétion QCM
            $inscription->qcm = "O"; // Réussite QCM ?? à voir avec David
            $inscription->date = date('Y-m-d H:i:s');
    
            array_push($inscriptions, $inscription);
        }
    }
    
    // Générer le fichier CSV
    $filename = 'export_inscriptions_' . date('Y-m-d_H-i-s') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    
    // En-têtes du CSV
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
    
    // Données
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
        }
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

