<?php



if($rolename == "super-admin" || $rolename == "manager" || $rolename == "smalleditingteacher") {
 
    $queryallusers = 'SELECT u.id, u.username, u.firstname, u.lastname, u.email
        FROM mdl_user u
        JOIN mdl_user_enrolments ue ON ue.userid = u.id
        JOIN mdl_enrol e ON e.id = ue.enrolid
        WHERE e.courseid = ' . $courseid . '';
    $allusers = $DB->get_records_sql($queryallusers, null);


    //on va chercher le groupe du formateur
    $groupid = optional_param('groupid', null, PARAM_INT);
    if(!$groupid){
        $group = $DB->get_record_sql('SELECT g.id, g.name FROM mdl_groups g
        JOIN mdl_groups_members gm ON gm.groupid = g.id
        WHERE gm.userid = ' . $USER->id . ' AND g.courseid = ' . $courseid, null);
        $groupid = $group->id;
    }

    if($groupid){
        $sessionprog = getTeamProgress($courseid, $groupid)[0];
    } else {
        $sessionprog = "0%";
    }
    
   

    $content .= '<div style="border:1px solid #3cafe3;border-radius:10px;padding:20px;margin-top:30px;">';
    $content .= '<h3 class="FFF-title1">
        <span class="FFABlack FFF-Blue" style="letter-spacing:1px;">Mes </span><span class="FFABlack FFF-Gold" style="letter-spacing:1px;">Chiffres</span> 
    </h3>

    <div class="row" id="coursescore"  style="width: 100%;position: relative;">

        <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="fff-box-stats" style="border-right: 1px solid #00315a;">
                <h1>'.count($allusers).'</h1>
                <h5>Nombre de participants</h5>
            </div>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="fff-box-stats">
                <h1>'.$sessionprog.'</h1>
                <h5>De progression générale</h5>
            </div>
        </div>

    </div>';

    $content .= '</div>';

} else if($rolename == "student"){

    $modulesstatus = getModulesStatus($courseid, null, $userid);
    $finished = $modulesstatus[0];
    $timespent = getTimeSpentOnCourse($USER->id, $courseid);
    $content .= '<div style="border:1px solid #3cafe3;border-radius:10px;padding:20px;margin-top:30px;">';
    $content .= '<h3 class="FFF-title1">
        <span class="FFABlack FFF-Blue" style="letter-spacing:1px;">Mes </span><span class="FFABlack FFF-Gold" style="letter-spacing:1px;">Chiffres</span> 
    </h3>

    <div class="row" id="coursescore"  style="width: 100%;position: relative;">

        <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="fff-box-stats" style="border-right: 1px solid #00315a;">
                <h1>'.$timespent.'</h1>
                <h5>Temps passé</h5>
            </div>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="fff-box-stats">
                <h1>'.$finished.'</h1>
                <h5>Activités terminées</h5>
            </div>
        </div>

    </div>';

    $content .= '</div>';
}


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



