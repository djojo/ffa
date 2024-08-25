<?php





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

        $sessiondate = 'Du ' . userdate($session->startdate, '%d/%m/%Y') . ' au ' . userdate($session->enddate, '%d/%m/%Y');

        //On va chercher le responsable pédagogique
        $coach = getResponsablePedagogique($groupid, $courseid);

        if ($coach[1]) {
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






