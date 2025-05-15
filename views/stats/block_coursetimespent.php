<?php

$filterfrom = ' WHERE sc.datecreated > ' . $startdatetimestamp . ' AND sc.datecreated < ' . $enddatetimestamp . ' ';

//on va chercher les stats des cours
$allcourses = $DB->get_records_sql('SELECT * 
    FROM mdl_course c
    WHERE c.format != "site"
    AND visible = 1
    ORDER BY fullname ASC', null);

$arraycourses = [];
$arraycoursestimespent = [];

foreach ($allcourses as $course) {
    $coursetimespent = 0;
    $sqlstats = 'SELECT * 
    FROM mdl_smartch_stats_course sc
    ' . $filterfrom . '
    AND sc.courseid = ' . $course->id . '
    ORDER BY sc.datecreated ASC';

    //On va chercher les stats des étudiants
    $stats = $DB->get_records_sql($sqlstats, null);

    foreach ($stats as $stat) {
        $coursetimespent += $stat->timespent;
    }

    //On converti en heures
    $coursetimespent = floor($coursetimespent / 3600);

    array_push($arraycoursestimespent, $coursetimespent);
    array_push($arraycourses, $course->fullname);
}

$jsonTimespent = json_encode($arraycoursestimespent);
$jsonData = json_encode($arraycourses);

$size = count($arraycourses) * 40;

$content .= displayChartLineHorizontalMultipleInside('Temps passé par formation', $jsonTimespent, $jsonData, $size);
