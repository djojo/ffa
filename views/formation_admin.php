<?php


//on va chercher la formation 
$course = $DB->get_record('course', ['id' => $courseid]);

require_once('./returns.php');

//le context du template header pour le retour
$templatecontextheader = (object)[
    'url' => new moodle_url('/theme/remui/views/adminformations.php'),
    'coursename' => $course->fullname,
    // 'coursename' => $course->fullname . ' (Vue pour le ' . $rolename . ')',
    'textcontent' => 'Retour aux parcours'

];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_course_header', $templatecontextheader);

$timeplus30 = time() + 30 * 24 * 60 * 60;
$timemoins30 = time() - 30 * 24 * 60 * 60;

//on limite volontairement à 50
$groups = $DB->get_records_sql('SELECT DISTINCT g.id, g.name 
FROM mdl_groups g
JOIN mdl_groups_members gm ON gm.groupid = g.id
-- NE PAS OUBLIER DE REMETTRE
JOIN mdl_smartch_session ss ON ss.groupid = g.id
WHERE g.courseid = ' . $courseid . '
LIMIT 0, 50 ', null);

//echo '<script>alert("' . count($groups) . '")</script>';

// $groups = $DB->get_records_sql('SELECT g.id, g.name FROM mdl_groups g
// JOIN mdl_groups_members gm ON gm.groupid = g.id
// JOIN mdl_smartch_session ss ON ss.groupid = g.id
// WHERE g.courseid = ' . $courseid . '
// AND ss.startdate > ' . $timemoins30 . '
// AND ss.startdate < ' . $timeplus30, null);


//on regarde si un utilisateur est sélectionné
$userid = optional_param('userid', null, PARAM_INT);

$groupid = optional_param('groupid', null, PARAM_INT);
// var_dump($groupid);
if ($groupid) {
    $group = $DB->get_record('groups', ['id' => $groupid]);
} else if ($userid) {
    //on va chercher le groupe de l'utilisateur séléctionné
    $groups = $DB->get_records_sql('SELECT g.id, g.name FROM mdl_groups g
    JOIN mdl_groups_members gm ON gm.groupid = g.id
    WHERE gm.userid = ' . $userid . ' AND g.courseid = ' . $courseid, null);
    $group = reset($groups);
    $groupid = $group->id; //pour le responsable pedagogique
}

//si il n'y a pas de groupe pour l'utilisateur on prend le premier
// if (count($groups) == 0) {
//     $groups = $DB->get_records_sql('SELECT g.id, g.name FROM mdl_groups g
//     JOIN mdl_groups_members gm ON gm.groupid = g.id
//     WHERE g.courseid = ' . $courseid, null);
// }
// $group = reset($groups);

//si on a au moins un groupe
if ($group) {

    //on va chercher les informations de session 
    $session = $DB->get_record('smartch_session', ['groupid' => $group->id]);

    if ($session) {
        $issession = true;
    } else{
        $issession = false;
    }

    $sessionadress = "";
    if ($session->adress1 != "") {
        $sessionadress .= $session->adress1;
    }
    if ($session->adress2 != "") {
        $sessionadress .= ', ' . $session->adress2;
    }
    if ($session->zip != "") {
        $sessionadress .= ', ' . $session->zip;
    }
    if ($session->city != "") {
        $sessionadress .= ', ' . $session->city;
    }

    if ($sessionadress != "") {
        $hassessionadress = true;
    }



    //$sessiondate = 'Session du ' . userdate($session->startdate, get_string('strftimedate')) . ' au ' . userdate($session->enddate, get_string('strftimedate'));
    $sessiondate = 'Du ' . userdate($session->startdate, '%d/%m/%Y') . ' au ' . userdate($session->enddate, '%d/%m/%Y');

    //On va chercher le responsable pédagogique
    $coach = getResponsablePedagogique($groupid, $courseid);

    if ($coach[1]) {
        // $backurl = urlencode($_SERVER['REQUEST_URI']);
        if ($coach[1]->id != $USER->id) {
            $urlmessageresponsable = new moodle_url('/theme/remui/views/adminusermessage.php?userid=' . $coach[1]->id) . '&courseid=' . $courseid;
        }
    }

    //le context du template du parcours
    $templatecontextcourse = (object)[
        'course' => $course,
        'urlmessageresponsable' => $urlmessageresponsable,
        'coursesummary' => html_entity_decode($course->summary),
        'session' => $issession,
        'teamname' => $group->name,
        'sessionadress' => $sessionadress,
        'sessiondate' => $sessiondate,
        'coach' => $coach[0],
        'courseduration' => $courseduration,
        'coursetype' => $coursetype,
        'diplome' => $diplome,
        'category' => $category->name,
        'format' => "fff-course-box-info"
    ];
} else {
    //le context du template du parcours
    $templatecontextcourse = (object)[
        'course' => $course,
        'session' => false,
        'coursesummary' => html_entity_decode($course->summary),
        'format' => "fff-course-box-info"
    ];
}


//la présentation du parcours
$content .= $OUTPUT->render_from_template('theme_remui/smartch_course_info', $templatecontextcourse);

//l'entete des équipes du parcours
// $content .= $OUTPUT->render_from_template('theme_remui/smartch_teams_header', null);

// $content .= '<h3 class="FFF-title1" id="equipe" style="display: flex;align-items: center;margin-top:50px;">
// </h3>';



//les groupes
require_once('./includes_groups.php');


if ($group) {

    //on va chercher les logs du groupe
    $logs = $DB->get_records_sql('SELECT sa.id, sa.timespent FROM mdl_smartch_activity_log sa
    JOIN mdl_groups_members gm ON gm.userid = sa.userid
    WHERE sa.course = ' . $courseid . ' AND gm.groupid =  ' . $group->id, null);

    $timetotal = 0;
    foreach ($logs as $log) {
        $timetotal += $log->timespent;
    }

    $timespent = convert_to_string_time($timetotal);

    //on va chercher les stats
    $progress = getTeamProgress($courseid, $group->id);

    $templatecontextstats = (object)[
        'timespent' => $timespent,
        'progress' => $progress[0],
        'progressmax' => $progress[1],
        'progressmin' => $progress[2]
    ];
    //les stats sur ce groupe
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_team_score', $templatecontextstats);
}


//les modules
require_once('./courses_modules.php');
