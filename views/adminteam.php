<?php

use core\session\redis;

require_once(__DIR__ . '/../../../config.php');
require_once('./utils.php');



require_login();

global $USER, $DB, $CFG;

$params = null;
$content = '';
$paginationtitle = '';

//On va chercher le rôle le plus haut de l'utilisateur
$rolename = getMainRole();

isStudent();

$action = optional_param('action', '', PARAM_TEXT);
$sent = optional_param('sent', false, PARAM_BOOL);
$userid = optional_param('userid', '', PARAM_INT);
$teamid = optional_param('teamid', '', PARAM_INT);
$messageteam = optional_param('message', 0, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$pageno = optional_param('pageno', 1, PARAM_TEXT);


if ($action == "downloadcsv") {
    downloadCSVTeam($teamid);
} else if ($action == "downloadxls") {
    downloadXLSTeam($teamid);
} else if ($action == "downloadcsvgrade") {
    downloadCSVTeamGrade($teamid);
} else if ($action == "downloadxlsgrade") {
    downloadXLSTeamGrade($teamid);
}

//on var chercher l'équipe
$group = $DB->get_record('groups', ['id' => $teamid]);

$courseid = $group->courseid;



//on va chercher toutes les activités sauf les dossiers et sessions
$activities = getCourseActivitiesStats($courseid);

$userid = optional_param('userid', null, PARAM_INT);

$no_of_records_per_page = 24;
$offset = ($pageno - 1) * $no_of_records_per_page;

if ($search != "") {
    $queryusers = '
    SELECT u.id, u.firstname, u.lastname, r.shortname, r.id as roleid
    FROM mdl_role_assignments AS ra 
    JOIN mdl_user_enrolments AS ue ON ra.userid = ue.userid 
    JOIN mdl_role AS r ON ra.roleid = r.id 
    JOIN mdl_context AS c ON c.id = ra.contextid 
    JOIN mdl_enrol AS e ON e.courseid = c.instanceid AND ue.enrolid = e.id 
    JOIN mdl_user u ON u.id = ue.userid
    JOIN mdl_groups_members gm ON u.id = gm.userid
    WHERE gm.groupid = ' . $group->id . ' 
    AND e.courseid = ' . $courseid . '
    AND r.shortname = "student"
    AND (lower(u.firstname) LIKE "%' . $search . '%" 
    OR lower(u.lastname) LIKE "%' . $search . '%"
    OR lower(u.username) LIKE "%' . $search . '%"
    OR lower(u.email) LIKE "%' . $search . '%")
    ORDER BY u.lastname ASC';
    //    --LIMIT ' . $offset . ', ' . $no_of_records_per_page;


    $total_pages_sql = '
    SELECT COUNT(u.id) count
    FROM mdl_role_assignments AS ra 
    JOIN mdl_user_enrolments AS ue ON ra.userid = ue.userid 
    JOIN mdl_role AS r ON ra.roleid = r.id 
    JOIN mdl_context AS c ON c.id = ra.contextid 
    JOIN mdl_enrol AS e ON e.courseid = c.instanceid AND ue.enrolid = e.id 
    JOIN mdl_user u ON u.id = ue.userid
    JOIN mdl_groups_members gm ON u.id = gm.userid
    WHERE gm.groupid = ' . $group->id . ' 
    AND e.courseid = ' . $courseid . '
    AND r.shortname = "student"
    AND (lower(u.firstname) LIKE "%' . $search . '%" 
    OR lower(u.lastname) LIKE "%' . $search . '%"
    OR lower(u.username) LIKE "%' . $search . '%"
    OR lower(u.email) LIKE "%' . $search . '%")';
} else {
    $queryusers = '
    SELECT u.id, u.firstname, u.lastname, r.shortname, r.id as roleid
    FROM mdl_role_assignments AS ra 
    JOIN mdl_user_enrolments AS ue ON ra.userid = ue.userid 
    JOIN mdl_role AS r ON ra.roleid = r.id 
    JOIN mdl_context AS c ON c.id = ra.contextid 
    JOIN mdl_enrol AS e ON e.courseid = c.instanceid AND ue.enrolid = e.id 
    JOIN mdl_user u ON u.id = ue.userid
    JOIN mdl_groups_members gm ON u.id = gm.userid
    WHERE gm.groupid = ' . $group->id . ' 
    AND e.courseid = ' . $courseid . '
    AND r.shortname = "student"
    ORDER BY u.lastname ASC
       ';
    //LIMIT ' . $offset . ', ' . $no_of_records_per_page . '
    $total_pages_sql = '
    SELECT COUNT(u.id) count
    FROM mdl_role_assignments AS ra 
    JOIN mdl_user_enrolments AS ue ON ra.userid = ue.userid 
    JOIN mdl_role AS r ON ra.roleid = r.id 
    JOIN mdl_context AS c ON c.id = ra.contextid 
    JOIN mdl_enrol AS e ON e.courseid = c.instanceid AND ue.enrolid = e.id 
    JOIN mdl_user u ON u.id = ue.userid
    JOIN mdl_groups_members gm ON u.id = gm.userid
    WHERE gm.groupid = ' . $group->id . ' 
    AND e.courseid = ' . $courseid . '
    AND r.shortname = "student"';
}
$teamates = $DB->get_records_sql($queryusers, null);

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/adminteam.php', ['teamid' => $teamid]));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title($group->name);

echo $OUTPUT->header();

echo '<style>

#page{
    background:transparent !important;
}

#page.drawers .main-inner {
    background: transparent !important;
    margin-top: 0px;
}

@media screen and (max-width: 830px) {
    #page.drawers .main-inner{
        margin-top:40px;
    }
}

/* Style du menu */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        /* Style des éléments du menu déroulant */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 100 !important;
        }

        /* Style des liens à l\'intérieur du menu */
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }


        /* Afficher le menu déroulant */
        .dropdown-content.show {
            display: block;
        }

        /* Changement de couleur au survol */
        .dropdown-content a:hover {background-color: #f1f1f1}

</style>';


$result = $DB->get_records_sql($total_pages_sql, $params);
$total_rows = reset($result)->count;
$total_pages = ceil($total_rows / $no_of_records_per_page);

require_once('./returns.php');

//on va chercher la formation 
$course = $DB->get_record('course', ['id' => $courseid]);

//on regarde si on est en formation gratuite
$freecat = $DB->get_record_sql('SELECT * from mdl_course_categories WHERE name = "Formation gratuite"', null);
//si on est sur une formation gratuite
if ($course->category == $freecat->id) {
    //on ne montre pas cette page pour les admins
    if($rolename == 'teacher' || $rolename == 'smalleditingteacher' || $rolename == "editingteacher" || $rolename == "student" || $rolename == "noneditingteacher") {
        redirect(new moodle_url('/theme/remui/views/formation.php?id='.$courseid));
    }
}


//On créer l'url pour le retour
$portail = getConfigPortail();

if($portail == "portailformation"){
    $backurl = new moodle_url('/theme/remui/views/adminformations.php');
} else if($portail == "portailrh"){
    //on va chercher la cohort
    $enrol = $DB->get_record_sql('SELECT * 
    FROM mdl_enrol
    WHERE customint2 = ' . $teamid, null);
    $backurl = new moodle_url('/theme/remui/views/cohort?id=.php' . $enrol->customint1);
}

//le context du template header pour le retour
$templatecontextheader = (object)[
    'url' => $backurl,
    'coursename' => $course->fullname,
    // 'coursename' => $course->fullname . ' (Vue pour le ' . $rolename . ')',
    'textcontent' => 'Retour aux parcours'

];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_course_header', $templatecontextheader);





//on va chercher les informations de session 
$session = $DB->get_record('smartch_session', ['groupid' => $group->id]);

if ($session) {
    $sessiondate = 'Session du ' . userdate($session->startdate, get_string('strftimedate')) . ' au ' . userdate($session->enddate, get_string('strftimedate'));
    $sessionadress = $session->adress1 . ', ' . $session->adress2 . ', ' . $session->zip . ',  ' . $session->city;

    //on recupère les champs personnalisés
    $diplomeobjects = $DB->get_records_sql('
     SELECT cd.value 
     FROM mdl_customfield_data cd
     JOIN mdl_customfield_field cf ON cf.id = cd.fieldid
     WHERE cd.instanceid = ' . $courseid . ' AND cf.shortname = "diplome"', null);

    $diplome = '';
    $diplomeobject = reset($diplomeobjects);
    if ($diplomeobject) {
        $diplome = $diplomeobject->value;
    }

    if (isFreeCourse($courseid)) {
        $coursetyperesult = $DB->get_records_sql('
            SELECT cd.value 
            FROM mdl_customfield_data cd
            JOIN mdl_customfield_field cf ON cf.id = cd.fieldid
            WHERE cd.instanceid = ' . $courseid . ' AND cf.shortname = "freecoursetype"', null);
        $coursetypeobject = reset($coursetyperesult);
        if ($coursetypeobject) {
            // $coursetype = 'FORMATION GRATUITE';
            $res = $coursetypeobject->value;
            if ($res == 1) {
                $diplome = "Tous publics";
            } else if ($res == 2) {
                $diplome = "Licenciés";
            } else {
                $diplome = $res;
            }
        }
    } else {
        $coursetypeobjects = $DB->get_records_sql('
     SELECT cd.value 
     FROM mdl_customfield_data cd
     JOIN mdl_customfield_field cf ON cf.id = cd.fieldid
     WHERE cd.instanceid = ' . $courseid . ' AND cf.shortname = "coursetype"', null);
        $coursetypeobject = reset($coursetypeobjects);
        $coursetype = '';
        if ($coursetypeobject) {
            $coursetype = $coursetypeobject->value;
        }
    }


    $coursedurationobjects = $DB->get_records_sql('
     SELECT cd.value 
     FROM mdl_customfield_data cd
     JOIN mdl_customfield_field cf ON cf.id = cd.fieldid
     WHERE cd.instanceid = ' . $courseid . ' AND cf.shortname = "courseduration"', null);

    $coursedurationobject = reset($coursedurationobjects);
    $courseduration = '';
    if ($coursedurationobject) {
        $courseduration = $coursedurationobject->value;
    }

    //On va chercher le responsable pédagogique
    $coach = getResponsablePedagogique($group->id, $courseid);

    $urlmessageresponsable = "";
    if ($coach[1]) {
        //$backurl = $_SERVER['REQUEST_URI'];
        if ($coach[1]->id != $USER->id) {
            $urlmessageresponsable = new moodle_url('/theme/remui/views/adminusermessage.php?userid=' . $coach[1]->id) . '&return=team&teamid=' . $teamid;
        }
    }

    //le context du template du parcours
    $templatecontextcourse = (object)[
        'course' => $course,
        'urlmessageresponsable' => $urlmessageresponsable,
        'coursesummary' => html_entity_decode($course->summary),
        'session' => true,
        'teamname' => $groupname = extraireNomEquipe($group->name),
        'sessionadress' => $sessionadress,
        'sessiondate' => $sessiondate,
        'courseduration' => $courseduration,
        'coursetype' => $coursetype,
        'diplome' => $diplome,
        'coach' => $coach[0],
        'format' => "fff-course-box-info-team"
    ];
    //la présentation du parcours
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_course_info', $templatecontextcourse);
}


$content .= '<div id="group">
</div>';


//le tableau des parametres pour la recherche
$params = array();
$param1['paramname'] = "teamid";
$param1['paramvalue'] = $teamid;
array_push($params, $param1);

$content .= '<div class="row">
<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
    <div class="smartch_flex_mobile" style="margin-top:30px;">
        <div class="smartch_flex_mobile">
            <h1 style="letter-spacing:1px;cursor:pointer;" class="smartch_title FFABold FFF-Blue">' . extraireNomEquipe($group->name) . '</h1> 
            <a class="smartch_btn ml-3" href="' . new moodle_url('/theme/remui/views/groupmessage.php') . '?teamid=' . $teamid . '&returnurl='.$PAGE->url.'">
                        <svg style="width:20px;margin-right:5px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                        Envoyer un message
                    </a>
        </div>
        <div>
            <form style="display: inline;" id="search-form" method="get" action="{{formurl}}">
                <input id="inputTeam" onkeyup="searchTeam()" class="smartch_input" type="text" name="search" placeholder="Rechercher un membre" value=""/>
            </form>
        </div>
    </div>
</div>
</div>
';

echo '<script>
function searchTeam() {
  // Declare variables
  let input = document.getElementById(\'inputTeam\');
  let filter = input.value.toUpperCase();
  //alert(filter);
  let members = document.getElementsByClassName(\'memberElement\');

  // Loop through all list items, and hide those who dont match the search query
  for (i = 0; i < members.length; i++) {
    let txtValue = members[i].textContent || members[i].innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
        members[i].parentNode.parentNode.parentNode.style.display = "";
    } else {
        members[i].parentNode.parentNode.parentNode.style.display = "none";
    }
  }
}
</script>';

//La pagination
if (count($teamates) == 0) {
    $paginationtitle .= 'Aucun membre';
} else if (count($teamates) == 1) {
    $paginationtitle .= '1 membre';
} else {
    $paginationtitle .= $total_rows . ' membres - page ' . $pageno . ' sur ' . $total_pages . '';
}
$paginationarray = range(1, $total_pages); // array(1, 2, 3)


$content .= '
        <div id="selected" class="smartch_flex_mobile" style="padding: 20px 0;height: 100px;">
            <div
                <svg onclick="goToMessage()" style="cursor:pointer;" width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 4L8.8906 9.2604C9.5624 9.70827 10.4376 9.70827 11.1094 9.2604L19 4M3 15H17C18.1046 15 19 14.1046 19 13V3C19 1.89543 18.1046 1 17 1H3C1.89543 1 1 1.89543 1 3V13C1 14.1046 1.89543 15 3 15Z" stroke="#0B427C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span style="display:none;cursor:pointer;margin-left:20px;" onclick="goToMessage()" class="fff-title-team">Envoyer un message aux ' . count($teamates) . ' membres</span>
                
            </div>
            <div>
                <div class="dropdown">
                    <a onclick="toggleDropdown()" class="dropbtn smartch_btn">
                        Télécharger le rapport de progression
                        <svg style="width: 25px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15M9 12l3 3m0 0l3-3m-3 3V2.25" />
                        </svg>
                    </a>
                    <div id="myDropdown" class="dropdown-content">
                        <a target="_blank" href="' . new moodle_url('/theme/remui/views/adminreport.php?groupid=' . $teamid) . '" style="cursor:pointer;color:#00315a;display:flex;align-items:center;justify-content:center;">
                            Rapport de progression pdf
                        </a>
                        <a href="?return=' . $return . '&teamid=' . $teamid . '&action=downloadxls" style="cursor:pointer;color:#00315a;display:flex;align-items:center;justify-content:center;">
                            Rapport de progression xlsx
                        </a>
                        <a href="?return=' . $return . '&teamid=' . $teamid . '&action=downloadcsv" style="cursor:pointer;color:#00315a;display:flex;align-items:center;justify-content:center;">
                            Rapport de progression csv
                        </a>
                    </div>
                </div>
                <div class="dropdown">
                    <a onclick="toggleDropdown2()" class="dropbtn smartch_btn">
                        Télécharger le carnet de note
                        <svg style="width: 25px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15M9 12l3 3m0 0l3-3m-3 3V2.25" />
                        </svg>
                    </a>
                    <div id="myDropdown2" class="dropdown-content">
                        <a target="_blank" href="' . new moodle_url('/theme/remui/views/admingrade.php?groupid=' . $teamid) . '" style="cursor:pointer;color:#00315a;display:flex;align-items:center;justify-content:center;">
                            Carnet de note pdf
                        </a>
                        <a href="?return=' . $return . '&teamid=' . $teamid . '&action=downloadxlsgrade" style="cursor:pointer;color:#00315a;display:flex;align-items:center;justify-content:center;">
                            Carnet de note xlsx
                        </a>
                        <a href="?return=' . $return . '&teamid=' . $teamid . '&action=downloadcsvgrade" style="cursor:pointer;color:#00315a;display:flex;align-items:center;justify-content:center;">
                            Carnet de note csv
                        </a>
                    </div>
                </div>
            </div>
        </div>';

$content .= '<div class="row" >';

if ($search) {
    $filtersearch = '&search=' . $search;
} else {
    $filtersearch = '';
}

foreach ($teamates as $teamate) {
    $user = $DB->get_record('user', ['id' => $teamate->id]);

    // $userprofileurl = new moodle_url('/theme/remui/views/adminuser.php?return=team&teamid=' . $teamid . '&userid=' . $teamate->userid);
    $userselectedurl = new moodle_url('/theme/remui/views/adminteam.php?return=' . $return . '&teamid=' . $teamid . '&userid=' . $teamate->id . $filtersearch . '#selected-' . $user->id);
    $reseturl = new moodle_url('/theme/remui/views/adminteam.php?return=' . $return . '&teamid=' . $teamid . $filtersearch . '#group');
    //on va chercher la prog si il y a des activités (cours non annulé)
    if (count($activities) > 0) {
        $courseprog = getCompletionPourcent($courseid, $user->id);
    } else {
        $courseprog = 0;
    }

    if ($user->id == $userid) {
        $selectedcolor = '#3CAFE3';
    } else {
        $selectedcolor = 'transparent';
    }
    $content .= '<div class="col-sm-12 col-md-6 col-lg-4 col-xl-3" style="border: 3px solid ' . $selectedcolor . ';padding:15px;border-radius: 15px;width: 100%;">';
    if ($user->id == $userid) {
        //on rajoute la croix de déselection
        $content .= '<svg onclick="location.href=\'' . $reseturl . '\'" style="width: 40px; padding: 5px; position: absolute; right: 10px; top: 10px; }" class="smartch_btn" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    ';
    }

    $content .= '<div id="selected-' . $user->id . '" onclick="location.href=\'' . $userselectedurl . '\'" style="cursor:pointer;display: flex;justify-content: left;"> 
                        <div>
                            <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="56" height="56" rx="4.2" fill="#EDF2F7"/>
                                <path d="M34.5354 20.2707C34.5354 23.6857 31.767 26.4541 28.3521 26.4541C24.9371 26.4541 22.1688 23.6857 22.1688 20.2707C22.1688 16.8558 24.9371 14.0874 28.3521 14.0874C31.767 14.0874 34.5354 16.8558 34.5354 20.2707Z" stroke="#CBD5E0" stroke-width="3.09167" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M28.3521 31.0916C22.3759 31.0916 17.5312 35.9362 17.5312 41.9124H39.1729C39.1729 35.9362 34.3283 31.0916 28.3521 31.0916Z" stroke="#CBD5E0" stroke-width="3.09167" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div style="margin-left: 10px;width: 100%;">
                            <div class="memberElement" style="display:none;" >' . $user->firstname . ' ' . $user->lastname . '</div>';
    $matenamestring =  $user->firstname . '<br>' . $user->lastname;
    // if (strlen($user->lastname) > 15 || strlen($user->firstname) > 15 || strlen($user->firstname . $user->lastname) > 30) {
    //     $content .= '<div class="matename" style="height: 50px;line-height: 17px;">
    //                         ' . $matenamestring . '
    //                         </div>';
    // } else {
    $content .= '<div class="matename" style="height: 50px;">
                            ' . $matenamestring . '
                            </div>';
    // }

    $content .= '<div class="smartch_progress_bar_box" style="width: 100%;">
                                <div class="smartch_progress_bar_mini">
                                    <div class="smartch_progress_bar_number"></div>
                                    <div class="smartch_progress_bar_gain" style="width:' . $courseprog . '% !important;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
}

$content .= '
        </div>
    ';


if (!$userid) {
    //on va chercher les logs du groupe
    $logs = $DB->get_records_sql('SELECT sa.id, sa.timespent FROM mdl_smartch_activity_log sa
    JOIN mdl_groups_members gm ON gm.userid = sa.userid
    WHERE sa.course = ' . $courseid . ' AND gm.groupid =  ' . $group->id, null);

    $timetotal = 0;
    foreach ($logs as $log) {
        $timetotal += $log->timespent;
    }

    $timespent = convert_to_string_time($timetotal);
    if ($session) {
        $sessionid = $session->id;
    } else {
        $sessionid = null;
    }
    //on va chercher les stats du groupe
    $progress = getTeamProgress($courseid, $group->id, $sessionid);

    $templatecontextstats = (object)[
        'timespent' => $timespent,
        'progress' => $progress[0],
        'progressmax' => $progress[1],
        'progressmin' => $progress[2]
    ];

    //les stats sur ce groupe
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_team_score', $templatecontextstats);
} else {

    //on va chercher si il y a un log
    $logs = $DB->get_records_sql('SELECT * FROM mdl_smartch_activity_log WHERE course = ' . $courseid . ' AND userid = ' . $userid, null);

    $timetotal = 0;
    foreach ($logs as $log) {
        $timetotal += $log->timespent;
    }

    $timespent = convert_to_string_time($timetotal);

    //on va chercher les stats de l'utilisateur
    $modulesstatus = getModulesStatus($courseid, $session->id, $userid);

    // $actsccc = getCourseActivitiesStats($courseid);
    $selecteduser = $DB->get_record('user', ['id' => $userid]);
    // $pourcent = $modulesstatus[0]/($modulesstatus[0]+$modulesstatus[1])*100;

    $templatecontextstats = (object)[
        'title1' => 'Score de ',
        'title2' => $selecteduser->firstname . ' ' . $selecteduser->lastname,
        'timespent' => $timespent,
        // 'progress' => $pourcent,
        'progress' => getCompletionPourcent($courseid, $selecteduser->id),
        'modulesfinished' => $modulesstatus[0],
        'modulestocome' => $modulesstatus[1]
    ];
    //le score de l'étudiant sur ce cours
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_course_your_score', $templatecontextstats);
}



// $content .= '<h3 id="sendmessageteam" class="FFF-title1" style="display:none;margin-bottom:100px; display: flex; align-items: center;">

// <span class="FFABlack FFF-Blue" style="letter-spacing:1px;margin-right:10px;">Envoyer un message </span><span class="FFABlack FFF-Gold" style="letter-spacing:1px;margin-right:20px;"> à l\'équipe</span> 

// <span style="cursor:pointer;">
//     <svg class="FFF-Gold" onclick="this.style.display=\'none\';document.getElementById(\'upmessage\').style.display=\'block\';document.getElementById(\'messageteam\').style.display=\'block\';" id="downmessage" style="width:35px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
//     <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
//     </svg>

//     <svg class="FFF-Gold" onclick="this.style.display=\'none\';document.getElementById(\'downmessage\').style.display=\'block\';document.getElementById(\'messageteam\').style.display=\'none\';" id="upmessage" style="width:35px;display:none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
//     <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
//     </svg>
// </span>


// </h3>';


if ($sent) {
    displayMessageSent();
}

echo $content;


// echo '<div id="messageteam" style="display:none;">';

// require_once('./include_message_team.php');

// echo '</div>';


$content = "";

require_once('./courses_modules.php');

echo $content;

//les dépots (caché pour l'instant)
// require_once('./team_dropbox.php');

echo $OUTPUT->footer();

echo '<script>

// Fonction pour afficher ou masquer le menu
    function toggleDropdown() {
        document.getElementById("myDropdown").classList.toggle("show");
    }
    function toggleDropdown2() {
        document.getElementById("myDropdown2").classList.toggle("show");
    }

    // Fermer le dropdown si l\'utilisateur clique en dehors de celui-ci
    window.onclick = function(event) {
      if (!event.target.matches(".dropbtn")) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
          var openDropdown = dropdowns[i];
          if (openDropdown.classList.contains("show")) {
            openDropdown.classList.remove("show");
          }
        }
      }
    }
    function goToMessage(){
        let sendmessageteam = document.getElementById("sendmessageteam");
        let top = sendmessageteam.getBoundingClientRect().top;
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        var topPositionRelativeToPage = top + scrollTop;
        window.scrollTo({
            top: topPositionRelativeToPage - 100, // La position de défilement vers le bas souhaitée
            behavior: "smooth" // Utiliser une animation fluide
        });

        let dw = document.getElementById("downmessage");
        if(dw){
            dw.style.display="none";
            document.getElementById("upmessage").style.display="block";
            document.getElementById("messageteam").style.display="block";
        }
    }
</script>';

if ($messageteam == 1) {
    echo '<script>
    
        setTimeout(() => {
            let dw = document.getElementById("downmessage");
            if(dw){
                dw.style.display="none";
                document.getElementById("upmessage").style.display="block";
                document.getElementById("messageteam").style.display="block";
            }
        }, "100");


        
    </script>';
}
