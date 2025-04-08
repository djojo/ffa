<?php

use core\session\redis;

require_once(__DIR__ . '/../../../../config.php');
require_once('../utils.php');

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
$groupid = optional_param('groupid', '', PARAM_INT);
$messageteam = optional_param('message', 0, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$pageno = optional_param('pageno', 1, PARAM_TEXT);


if ($action == "downloadcsv") {
    downloadCSVTeam($groupid);
} else if ($action == "downloadxls") {
    downloadXLSTeam($groupid);
} else if ($action == "downloadcsvgrade") {
    downloadCSVTeamGrade($groupid);
} else if ($action == "downloadxlsgrade") {
    downloadXLSTeamGrade($groupid);
}

//on var chercher l'équipe
$group = $DB->get_record('groups', ['id' => $groupid]);

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
$PAGE->set_url(new moodle_url('/theme/remui/views/groups/details.php', ['groupid' => $groupid]));
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
            margin-bottom: 20px;
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

//le retour
require_once('../returns.php');

//on va chercher la formation 
$course = $DB->get_record('course', ['id' => $courseid]);

//le titre
$content .= '<h3 class="FFF-title1" style="margin-top: 80px;">
    <span class="FFABold FFF-White" style="letter-spacing:1px;">' . $group->name . ' • '.$course->fullname.'</span>
</h3>';

//on regarde si on est en formation gratuite
$freecat = $DB->get_record_sql('SELECT * from mdl_course_categories WHERE name = "Formation gratuite"', null);
//si on est sur une formation gratuite
if ($course->category == $freecat->id) {
    //on ne montre pas cette page pour les admins
    if($rolename == 'teacher' || $rolename == 'smalleditingteacher' || $rolename == "editingteacher" || $rolename == "student" || $rolename == "noneditingteacher") {
        redirect(new moodle_url('/theme/remui/views/formation.php?id='.$courseid));
    }
}


//on va chercher les informations de session 
$session = $DB->get_record('smartch_session', ['groupid' => $group->id]);

$content .= '<div id="group">
</div>';


//le tableau des parametres pour la recherche
$params = array();
$param1['paramname'] = "groupid";
$param1['paramvalue'] = $groupid;
array_push($params, $param1);

//On va chercher le formateur du groupe
$coach = getFormateur($group->id);

if($session){
    $sessiondate = formatSessionDate($session);
}

$blocksessiondate = "";
if($sessiondate){
    $blocksessiondate = '<div style="display:flex;align-items:center;">
                <svg style="width:20px;" class="mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                </svg>
                <span class="mr-4 FFARegular">' . $sessiondate . '</span>
            </div>';
}

$blockcoach = "";
if($coach[1]){
    $blockcoach = '<div class="fff-course-box-info-details">
                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="24" cy="24" r="24" fill="#E2E8F0"/>
                        <path d="M28 19C28 21.2091 26.2091 23 24 23C21.7909 23 20 21.2091 20 19C20 16.7909 21.7909 15 24 15C26.2091 15 28 16.7909 28 19Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M24 26C20.134 26 17 29.134 17 33H31C31 29.134 27.866 26 24 26Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div style="margin-left: 20px;">
                        <div class="FFABold FFF-Blue" style="font-size:16px;display:flex;align-items:center;">
                                '.$coach[1]->firstname.' '.$coach[1]->lastname.'
                                <a href="' . new moodle_url('/theme/remui/views/users/message.php') . '?userid=' . $coach[1]->id . '&returnurl='.$PAGE->url.'">
                                    <svg class="ml-2" width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 4L8.8906 9.2604C9.5624 9.70827 10.4376 9.70827 11.1094 9.2604L19 4M3 15H17C18.1046 15 19 14.1046 19 13V3C19 1.89543 18.1046 1 17 1H3C1.89543 1 1 1.89543 1 3V13C1 14.1046 1.89543 15 3 15Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                        </div>
                            <h5 class="FFF-Blue" style="font-size:12px;">Intervenant(e)</h5>
                    </div>
                </div>';
    }

$content .= '<div class="row">
<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
    <div class="smartch_flex_mobile" style="margin-top:30px;">
        <div style="margin: 0 20px;">
            <div style="display:flex;align-items:center;">
                <div style="font-size:1.2rem;font-weight:bold;">'.count($teamates).' membre'.(count($teamates) > 1 ? 's' : '').'</div>
                <a href="' . new moodle_url('/theme/remui/views/groups/message.php') . '?groupid=' . $groupid . '&returnurl='.$PAGE->url.'">
                    <svg class="ml-2" width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 4L8.8906 9.2604C9.5624 9.70827 10.4376 9.70827 11.1094 9.2604L19 4M3 15H17C18.1046 15 19 14.1046 19 13V3C19 1.89543 18.1046 1 17 1H3C1.89543 1 1 1.89543 1 3V13C1 14.1046 1.89543 15 3 15Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
            <div>'.$blocksessiondate.'</div>
        </div>
        '.$blockcoach.'
        <form style="display: inline;" id="search-form" method="get" action="{{formurl}}">
            <input autocomplete="off" id="inputTeam" onkeyup="searchTeam()" class="smartch_input_search" type="text" name="search" placeholder="Rechercher un membre" value=""/>
        </form>
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

$content .= '<div class="row">
<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">';

$content .= '
        <div id="selected" class="smartch_flex_mobile" style="height: 100px;">
            <div>
                <div class="dropdown">
                    <a onclick="toggleDropdown()" class="dropbtn smartch_btn" style="color:white !important;background:#00315a;border-color:#00315a;">
                        Télécharger le rapport de progression
                        <svg style="width: 25px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15M9 12l3 3m0 0l3-3m-3 3V2.25" />
                        </svg>
                    </a>
                    <div id="myDropdown" class="dropdown-content">
                        <a target="_blank" href="' . new moodle_url('/theme/remui/views/adminreport.php?groupid=' . $groupid) . '" style="cursor:pointer;color:#00315a;display:flex;align-items:center;justify-content:center;">
                            Rapport de progression pdf
                        </a>
                        <a href="?return=' . $return . '&groupid=' . $groupid . '&action=downloadxls" style="cursor:pointer;color:#00315a;display:flex;align-items:center;justify-content:center;">
                            Rapport de progression xls
                        </a>
                        <a href="?return=' . $return . '&groupid=' . $groupid . '&action=downloadcsv" style="cursor:pointer;color:#00315a;display:flex;align-items:center;justify-content:center;">
                            Rapport de progression csv
                        </a>
                    </div>
                </div>
                <div class="dropdown">
                    <a onclick="toggleDropdown2()" class="dropbtn smartch_btn" style="color:white !important;background:#00315a;border-color:#00315a;">
                        Télécharger le carnet de note
                        <svg style="width: 25px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15M9 12l3 3m0 0l3-3m-3 3V2.25" />
                        </svg>
                    </a>
                    <div id="myDropdown2" class="dropdown-content">
                        <a target="_blank" href="' . new moodle_url('/theme/remui/views/admingrade.php?groupid=' . $groupid) . '" style="cursor:pointer;color:#00315a;display:flex;align-items:center;justify-content:center;">
                            Carnet de note pdf
                        </a>
                        <a href="?return=' . $return . '&groupid=' . $groupid . '&action=downloadxlsgrade" style="cursor:pointer;color:#00315a;display:flex;align-items:center;justify-content:center;">
                            Carnet de note xls
                        </a>
                        <a href="?return=' . $return . '&groupid=' . $groupid . '&action=downloadcsvgrade" style="cursor:pointer;color:#00315a;display:flex;align-items:center;justify-content:center;">
                            Carnet de note csv
                        </a>
                    </div>
                </div>
            </div>
        </div>';

$content .= '</div>'; //col
$content .= '</div>'; //row

$content .= '<div class="row" style="padding: 20px 10px;">';

if ($search) {
    $filtersearch = '&search=' . $search;
} else {
    $filtersearch = '';
}

foreach ($teamates as $teamate) {
    $user = $DB->get_record('user', ['id' => $teamate->id]);

    // $userprofileurl = new moodle_url('/theme/remui/views/adminuser.php?return=group&groupid=' . $groupid . '&userid=' . $teamate->userid);
    $userselectedurl = new moodle_url('/theme/remui/views/groups/details.php?return=' . $return . '&groupid=' . $groupid . '&userid=' . $teamate->id . $filtersearch . '#selected-' . $user->id);
    // $userselectedurl = new moodle_url('/theme/remui/views/users/details.php') . '?userid=' . $teamate->id . '&returnurl='.$PAGE->url;
    $reseturl = new moodle_url('/theme/remui/views/groups/details.php?return=' . $return . '&groupid=' . $groupid . $filtersearch . '#group');
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
                                    <div class="smartch_progress_bar_gain_mini" style="width:' . $courseprog . '% !important;"></div>
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

    //un apprennant est sélectionné

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
    // var_dump($modulesstatus);
    // die();
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


if ($sent) {
    displayMessageSent();
}

echo $content;


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
