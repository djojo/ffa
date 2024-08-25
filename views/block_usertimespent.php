<?php

// $filterfrom = ' WHERE datecreated > ' . $startdate . ' 
// AND datecreated < ' . $enddate . ' ';

$filterfrom = ' WHERE datecreated > ' . $startdatetimestamp . ' AND datecreated < ' . $enddatetimestamp . ' ';


$arrayusertimespent = [];
$arraydays = [];

$sqlstats = 'SELECT * 
FROM mdl_smartch_stats_global
' . $filterfrom . '
ORDER BY datecreated ASC';

// echo $sqlstats;

//On va chercher les stats des étudiants
$stats = $DB->get_records_sql($sqlstats, null);

foreach ($stats as $stat) {
    //On parcours va chercher les logs dans cette tranche de temps
    $usertimespent = $stat->timespent;
    array_push($arrayusertimespent, $usertimespent);
    $day = date("Y-m-d", $stat->datecreated);
    array_push($arraydays, $day);
}

$alldata = [];

$data1['name'] = 'Temps passé sur la plateforme';
$data1['data'] = $arrayusertimespent;
array_push($alldata, $data1);
$jsonDays = json_encode($arraydays);
$jsonData = json_encode($alldata);
// $jsonData2 = json_encode($arraystudentconnected);
// $jsonData3 = json_encode($arrayteacherconnected);


// displayChartBar('Utilisateurs connectés', $jsonDays, $jsonData);
$content .= displayChartLine('Temps passé', $jsonDays, $jsonData);
