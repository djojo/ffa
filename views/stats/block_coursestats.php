<?php

$filterfrom = ' WHERE sc.datecreated > ' . $startdatetimestamp . ' AND sc.datecreated < ' . $enddatetimestamp . ' ';

//on va chercher les stats des cours
$allcourses = $DB->get_records_sql('SELECT * 
    FROM mdl_course c
    WHERE c.format != "site"
    AND visible = 1
    ORDER BY fullname ASC', null);

$arraycourses = [];
$arraycoursescountsessions = [];
$arraycoursescountusers = [];
$arraycoursestimespent = [];

foreach ($allcourses as $course) {
    $countusers = 0;
    $countsessions = 0;
    $counttimespent = 0;
    $sqlstats = 'SELECT * 
    FROM mdl_smartch_stats_course sc
    ' . $filterfrom . '
    AND sc.courseid = ' . $course->id . '
    ORDER BY sc.datecreated DESC LIMIT 1';

    //On va chercher les stats des étudiants
    $stat = $DB->get_record_sql($sqlstats, null);

    if ($stat) {
        // if ($stat->countusersconnected) {
        //     $countusersconnected += $stat->countusersconnected;
        // }

        // On compte le nombre d'utilisateurs inscrits au cours
        $course_id = $course->id; // Remplacez 123 par l'identifiant de votre cours
        // $role_id = 5; // Remplacez 5 par l'identifiant du rôle des utilisateurs que vous souhaitez compter

        $sql = "SELECT COUNT(DISTINCT u.id) AS nombre_inscrits
        FROM {user} u
        JOIN {role_assignments} ra ON ra.userid = u.id
        JOIN {role} r ON r.id = ra.roleid
        JOIN {context} ctx ON ctx.id = ra.contextid
        JOIN {course} c ON c.id = ctx.instanceid
        WHERE c.id = :courseid";

        $params = [
            'courseid' => $course_id
        ];

        $usercount = $DB->count_records_sql($sql, $params);

        $countusers += $usercount;

        if ($stat->countsessions) {
            $countsessions += $stat->countsessions;
        }
        if ($stat->timespent) {
            $counttimespent += $stat->timespent;
        }

        array_push($arraycoursescountsessions, $countsessions);
        array_push($arraycoursescountusers, $countusers);
        array_push($arraycoursestimespent, $counttimespent);
        array_push($arraycourses, $course->fullname);
    }
}

$jsonUsers = json_encode($arraycoursescountusers);
$jsonSessions = json_encode($arraycoursescountsessions);
$jsonTimespent = json_encode($arraycoursestimespent);

$alldata = [];

$data2['name'] = 'Utilisateurs';
$data2['data'] = $arraycoursescountusers;
array_push($alldata, $data2);
$data3['name'] = 'Sessions en cours';
$data3['data'] = $arraycoursescountsessions;
array_push($alldata, $data3);
$data4['name'] = 'Temps passé sur la période';
$data4['data'] = $arraycoursestimespent;
array_push($alldata, $data4);
// $data5['name'] = 'Sessions déma';
// $data5['data'] = $arraycoursestimespent;
// array_push($alldata, $data5);

$jsonCourse = json_encode($arraycourses);
$jsonData = json_encode($alldata);

$size = count($arraycourses) * 40;

$content .= displayChartLineHorizontal('Statistiques des formations', $jsonData, $jsonCourse, $size);
// $content .= displayChartLineHorizontalMultipleInside('Sessions par formation', $jsonData, $jsonCourse);
