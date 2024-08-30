<?php

// $filterfrom = ' WHERE datecreated > ' . $startdate . ' 
// AND datecreated < ' . $enddate . ' ';



$filterfrom = ' WHERE datecreated > ' . $startdatetimestamp . ' AND datecreated < ' . $enddatetimestamp . ' ';


$arrayusersconnected = [];
$arraystudentconnected = [];
$arrayteacherconnected = [];
$arraydays = [];

if ($courseid) {
    $sqlstats = 'SELECT sc.* 
    FROM mdl_smartch_stats_course sc
    JOIN mdl_course c ON c.id = sc.courseid
    ' . $filterfrom . '
    AND c.id = ' . $courseid . '
    ORDER BY datecreated ASC';
    $title = $selectedcourse->fullname;
} else if($categoryid) {
    $sqlstats = 'SELECT sc.* 
    FROM mdl_smartch_stats_course sc
    JOIN mdl_course c ON c.id = sc.courseid
    ' . $filterfrom . '
    AND c.visible = 1
    AND c.category = ' . $categoryid . '
    ORDER BY datecreated ASC';
    $title = $selectedcategory->name;
}


// echo $sqlstats;

//On va chercher les stats des étudiants
$stats = $DB->get_records_sql($sqlstats, null);

// var_dump($stats);

foreach ($stats as $stat) {
    //On parcours va chercher les logs dans cette tranche de temps
    $userconnected = $stat->countusersconnected;
    array_push($arrayusersconnected, $userconnected);
    $studentconnected = $stat->countstudentconnected;
    array_push($arraystudentconnected, $studentconnected);
    $teacherconnected = $stat->countteacherconnected;
    array_push($arrayteacherconnected, $teacherconnected);
    // $day = userdate($stat->datecreated, get_string('strftimedate'));
    $day = date("Y-m-d", $stat->datecreated);
    array_push($arraydays, $day);
    // array_push($arraytime, floor(rand(0, 180)));
}

$alldata = [];

// $data1['name'] = 'Utilisateurs connectés';
// $data1['data'] = $arrayusersconnected;
// array_push($alldata, $data1);
$data2['name'] = 'Apprenants connectés';
$data2['data'] = $arraystudentconnected;
array_push($alldata, $data2);
$data3['name'] = 'Formateurs connectés';
$data3['data'] = $arrayteacherconnected;
array_push($alldata, $data3);


$jsonDays = json_encode($arraydays);
$jsonData = json_encode($alldata);
// $jsonData2 = json_encode($arraystudentconnected);
// $jsonData3 = json_encode($arrayteacherconnected);


// displayChartBar('Utilisateurs connectés', $jsonDays, $jsonData);
// $content .= displayChartLine('Utilisateurs connectés', $jsonDays, $jsonData);
$content .= displayChartBarMultiple('Utilisateurs connectés pour ' . $title, $jsonDays, $jsonData);
