<?php
$content .= '<div style="border:1px solid #3cafe3;border-radius:10px;padding:20px;margin-top:30px;">';
$content .= '<h3 class="FFF-title1">
    <span class="FFABlack FFF-Blue" style="letter-spacing:1px;">Mes </span><span class="FFABlack FFF-Gold" style="letter-spacing:1px;">Chiffres</span> 
</h3>

<div class="row" id="coursescore"  style="width: 100%;position: relative;">

    <div class="col-sm-12 col-md-6 col-lg-6">
        <div class="fff-box-stats" style="border-right: 1px solid #00315a;">
            <h1>45</h1>
            <h5>Nombre de participants</h5>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-6">
        <div class="fff-box-stats">
            <h1>34%</h1>
            <h5>De progression générale</h5>
        </div>
    </div>

</div>';

$content .= '</div>';

// //on va chercher les stats
// $sessionid;

// if ($session) {
//     $sessionid = $session->id;
// }

// $modulesstatus = getModulesStatus($courseid, $sessionid);

// //on va chercher les logs de l'utilisateur
// $logs = $DB->get_records_sql('SELECT * FROM mdl_smartch_activity_log WHERE course = ' . $courseid . ' AND userid = ' . $USER->id, null);

// $timetotal = 0;
// foreach ($logs as $log) {
//     $timetotal += $log->timespent;
// }

// $timespent = convert_to_string_time($timetotal);

// $templatecontextstats = (object)[
//     'title1' => 'Mes ',
//     'title2' => 'Chiffres',
//     'timespent' => $timespent,
//     'progress' => getCompletionPourcent($courseid, $USER->id),
//     'modulesfinished' => $modulesstatus[0],
//     'modulestocome' => $modulesstatus[1]
// ];
// //le score de l'étudiant sur ce cours
// $content .= $OUTPUT->render_from_template('theme_remui/smartch_course_your_score', $templatecontextstats);



