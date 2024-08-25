<?php

//on va chercher la formation 
$course = $DB->get_record('course', ['id' => $courseid]);

// on check si l'utilisateur est enrollé
if (!checkIfUserIsEnrolled($courseid, $userid)) {
    //on récupère l'id de la formation gratuite
    $catfree = $DB->get_record_sql('SELECT * from mdl_course_categories WHERE name = "Formation gratuite"', null);
    if ($course->category == $catfree->id) {
        //on va vers la page d'inscription
        redirect(new moodle_url('/theme/remui/views/subscribe.php?courseid=' . $courseid));
    } else {
        //on redirige vers l'accueil
        redirect(new moodle_url('/'));
    }
}




$templatecontextheader = (object)[
    'url' => new moodle_url('/my'),
    'textcontent' => 'Retour au tableau de bord'
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);
// require_once('./returns.php');

//le context du template header pour le retour
$templatecontextheader = (object)[
    'url' => new moodle_url('/'),
    'coursename' => $course->fullname,
    'textcontent' => 'Retour au tableau de bord'

];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_course_header', $templatecontextheader);


$groups = $DB->get_records_sql('SELECT g.id, g.name FROM mdl_groups g
JOIN mdl_groups_members gm ON gm.groupid = g.id
WHERE gm.userid = ' . $USER->id . ' AND g.courseid = ' . $courseid, null);

//si l'utilisateur à un groupe
if (count($groups) > 0) {


    $group = reset($groups);
    $groupid = $group->id;

    // $content .= '<p style="color:white;">Il a un groupe...' . $group->id . '</p>';

    //on va chercher les informations de session 
    $session = $DB->get_record('smartch_session', ['groupid' => $group->id]);

    if ($session) {

        // $nbadresse = rand(1, 6);
        // if ($nbadresse == 1) {
        //     $sessionadress = '19 Rue Paul Bert, 92700 Colombes';
        // } else if ($nbadresse == 2) {
        //     $sessionadress = '111 Rue de Lorient, 35000 Rennes, France';
        // } else if ($nbadresse == 3) {
        //     $sessionadress = 'Chem. des Bruyères, 78120 Clairefontaine-en-Yvelines';
        // } else if ($nbadresse == 4) {
        //     $sessionadress = '7 Rue Henri Poincaré, 75020 Paris';
        // } else if ($nbadresse == 5) {
        //     $sessionadress = '87 Bd de Grenelle, 75015 Paris';
        // } else if ($nbadresse == 6) {
        //     $sessionadress = '90 Av. Marceau, 92400 Courbevoie';
        // }

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

        if ($sessionadress == "") {
            $hassessionadress = false;
        } else {
            $hassessionadress = true;
        }

        // $sessionadress = $session->adress1 . ', ' . $session->adress2 . ', ' . $session->zip . ',  ' . $session->city;


        //on genere le cache misere
        // $nbtimeplanning = rand(1, 4);
        // if ($nbtimeplanning == 1) {
        //     $timestampplanningstart = 1694332800;
        //     $timestampplanningend = 1702890000;
        // } else if ($nbtimeplanning == 2) {
        //     $timestampplanningstart = 1696233600;
        //     $timestampplanningend = 1710493200;
        // } else if ($nbtimeplanning == 3) {
        //     $timestampplanningstart = 1704445200;
        //     $timestampplanningend = 1715328000;
        // } else if ($nbtimeplanning == 4) {
        //     $timestampplanningstart = 1705741200;
        //     $timestampplanningend = 1713427200;
        // }
        // $sessiondate = 'Session du ' . userdate($timestampplanningstart, get_string('strftimedate')) . ' au ' . userdate($timestampplanningend, get_string('strftimedate'));

        $sessiondate = 'Du ' . userdate($session->startdate, '%d/%m/%Y') . ' au ' . userdate($session->enddate, '%d/%m/%Y');

        //On va chercher le responsable pédagogique
        $coach = getResponsablePedagogique($groupid, $courseid);

        // var_dump($coach);

        if ($coach[1]) {
            // $backurl = $_SERVER['REQUEST_URI'];
            $urlmessageresponsable = new moodle_url('/theme/remui/views/adminusermessage.php?userid=' . $coach[1]->id) . '&courseid=' . $courseid;
        }

        //le context du template du parcours
        $templatecontextcourse = (object)[
            'course' => $course,
            'urlmessageresponsable' => $urlmessageresponsable,
            'coursesummary' => html_entity_decode($course->summary),
            'session' => true,
            'teamname' => $group->name,
            'ligue' => $session->location,
            'sessionadress' => $sessionadress,
            'hassessionadress' => $hassessionadress,
            'sessiondate' => $sessiondate,
            'coach' => $coach[0],
            'iscoach' => true,
            'courseduration' => $courseduration,
            'coursetype' => $coursetype,
            'diplome' => $diplome,
            'category' => $category->name,
            'format' => "fff-course-box-info"
        ];
    } else {
        // $content .= '<p style="color:white;">Mais pas de session </p>';
        //le context du template du parcours
        $templatecontextcourse = (object)[
            'course' => $course,
            'session' => false,
            'coursesummary' => html_entity_decode($course->summary),
            'format' => "fff-course-box-info"
        ];
    }
} else {
    // $content .= '<p style="color:white;">Pas de groupe...</p>';

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

//on va chercher les stats
$sessionid;

if ($session) {
    $sessionid = $session->id;
}

$modulesstatus = getModulesStatus($courseid, $sessionid);

// var_dump($modulesstatus);

// $actsccc = getCourseActivitiesStats($courseid);

//on va chercher les logs de l'utilisateur
$logs = $DB->get_records_sql('SELECT * FROM mdl_smartch_activity_log WHERE course = ' . $courseid . ' AND userid = ' . $USER->id, null);

$timetotal = 0;
foreach ($logs as $log) {
    $timetotal += $log->timespent;
}

$timespent = convert_to_string_time($timetotal);

$templatecontextstats = (object)[
    'title1' => 'Votre ',
    'title2' => 'score',
    'timespent' => $timespent,
    'progress' => getCompletionPourcent($courseid, $USER->id),
    'modulesfinished' => $modulesstatus[0],
    'modulestocome' => $modulesstatus[1]
];
//le score de l'étudiant sur ce cours
$content .= $OUTPUT->render_from_template('theme_remui/smartch_course_your_score', $templatecontextstats);


require_once('./courses_modules.php');




