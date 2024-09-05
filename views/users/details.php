<?php
require_once(__DIR__ . '/../../../../config.php');
require_once('../utils.php');

// defined('MOODLE_INTERNAL') || die();

require_login();

global $USER, $DB, $CFG;

$params = null;
$content = '';
$paginationtitle = '';
$prevurl = '';
$nexturl = '';


$sent = optional_param('sent', false, PARAM_BOOL);
$userid = optional_param('userid', '', PARAM_INT);
$courseid = optional_param('courseid', '', PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$pageno = optional_param('pageno', 1, PARAM_TEXT);

if(!$userid){
    $userid = $USER->id;
}

//On va chercher le rôle le plus haut de l'utilisateur
$rolename = getMainRole($userid);

//le profil utilisateur
$user = $DB->get_record('user', ['id' => $userid]);

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/users/details.php'), ['userid'=>$userid]);
$PAGE->set_context(\context_system::instance());
$PAGE->set_title($user->firstname . ' ' . $user->lastname);

echo $OUTPUT->header();

$no_of_records_per_page = 10;
$offset = ($pageno - 1) * $no_of_records_per_page;

if ($search != "") {
    $querycourses = 'SELECT c.id, c.fullname FROM mdl_course c
            JOIN mdl_role_assignments ra ON ra.userid = ' . $userid . '
            JOIN mdl_context ct ON ct.id = ra.contextid AND c.id = ct.instanceid
            JOIN mdl_role r ON r.id = ra.roleid
            WHERE c.format != "site" AND c.visible = 1
            AND (lower(c.fullname) LIKE "%' . $search . '%")
            LIMIT ' . $offset . ', ' . $no_of_records_per_page;
    $total_pages_sql = 'SELECT c.id, c.fullname FROM mdl_course c
        JOIN mdl_role_assignments ra ON ra.userid = ' . $userid . '
        JOIN mdl_context ct ON ct.id = ra.contextid AND c.id = ct.instanceid
        JOIN mdl_role r ON r.id = ra.roleid
        WHERE c.format != "site" AND c.visible = 1
        AND lower(c.fullname) LIKE "%' . $search . '%"';
} else {
    $querycourses = 'SELECT c.id, c.fullname FROM mdl_course c
            JOIN mdl_role_assignments ra ON ra.userid = ' . $userid . '
            JOIN mdl_context ct ON ct.id = ra.contextid AND c.id = ct.instanceid
            JOIN mdl_role r ON r.id = ra.roleid
            WHERE format != "site" AND c.visible = 1
            LIMIT ' . $offset . ', ' . $no_of_records_per_page . '
            ';
    $total_pages_sql = 'SELECT c.id, c.fullname FROM mdl_course c
    JOIN mdl_role_assignments ra ON ra.userid = ' . $userid . '
    JOIN mdl_context ct ON ct.id = ra.contextid AND c.id = ct.instanceid
    JOIN mdl_role r ON r.id = ra.roleid
    WHERE c.format != "site" AND c.visible = 1';
}
$courses = $DB->get_records_sql($querycourses, null);

$allcourses = $DB->get_records('course', null);

$results = $DB->get_records_sql($total_pages_sql, null);
$total_rows = count($results);
$total_pages = ceil($total_rows / $no_of_records_per_page);

// //le header avec bouton de retour au panneau admin
// $templatecontextheader = (object)[
//     'url' => new moodle_url('/theme/remui/views/users/details.phps.php'),
//     'textcontent' => 'Retour aux utilisateurs'
// ];
// $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);

require_once('../returns.php');

//le titre
if($userid == $USER->id){
    $content .= '<h3 class="FFF-title1" style="margin-top: 80px;">
    <span class="FFABold FFF-White" style="letter-spacing:1px;">MON PROFIL</span>
</h3>';
} else {
    $content .= '<h3 class="FFF-title1" style="margin-top: 80px;">
    <span class="FFABold FFF-White" style="letter-spacing:1px;">UTILISATEUR</span>
</h3>';
}



//le css pour descendre l'image du header
$content .= '<style>

img.smartch_background_header {
    height: 350px !important;
}

#page{
    background:transparent !important;
}

#topofscroll {
    background: transparent !important;
    margin-top: 0px !important;
}

@media screen and (max-width: 830px) {
    #topofscroll{
        margin-top:40px !important;
    }
}

</style>
';

$role = "";
//on affiche le rolename correctement
if ($rolename == "student") {
    $role = "Stagiaire";
} else if ($rolename == "manager") {
    $role = "Administrateur Formation";
} else if ($rolename == "smalleditingteacher") {
    $role = "Intervenant(e)";
} else if ($rolename == "noneditingteacher") {
    $role = "Concepteur";
} else if ($rolename == "super-admin") {
    $role = "Super Admin";
}




if (!empty($user->lastaccess)) {
    $lastconnect = '' . userdate($user->lastaccess, '%d/%m/%y');
} else {
    $lastconnect = 'Jamais connecté';
}

$roleusername = getMainRole();

$btnconnection = "";
if ($roleusername == "super-admin" || $roleusername == "manager") {
    if($userid != $USER->id){
        $urltakerole = new moodle_url("/course/loginas.php?id=1") . "&user=" . $user->id . "&sesskey=" . $USER->sesskey;
        $btnconnection = '<a href="' . $urltakerole . '" class="smartch_btn" >Me connecter sous ce profil</a>';
    }
}

$content .= '<div class="row">';
$content .= '<div class="col-12">';
$content .= '<div class="fff-course-box-info">';

$content .= '<div class="row">';
$content .= '<div class="col-6">';

$url_page_licencie = getConfigValueByKey('url_page_licencie');

$content .= '<div class="fff-course-box-info-details">';
$content .= '<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="24" cy="24" r="24" fill="#E2E8F0"/>
                <path d="M28 19C28 21.2091 26.2091 23 24 23C21.7909 23 20 21.2091 20 19C20 16.7909 21.7909 15 24 15C26.2091 15 28 16.7909 28 19Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M24 26C20.134 26 17 29.134 17 33H31C31 29.134 27.866 26 24 26Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <div class="ml-4">
                <h3 class="FFABold FFF-Blue">
                    ' . $user->firstname . ' ' . $user->lastname . ' 
                </h3>
                <h5 class="FFF-Blue">' . $role . '</h5>
            </div>
        </div>
        <div style="cursor:pointer;" onclick="window.location.href=\'' . new moodle_url('/theme/remui/views/users/message.php') . '?userid=' . $user->id . '&returnurl='.$PAGE->url . '\'" class="fff-course-box-info-details">
            <svg class="mr-2" width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 4L8.8906 9.2604C9.5624 9.70827 10.4376 9.70827 11.1094 9.2604L19 4M3 15H17C18.1046 15 19 14.1046 19 13V3C19 1.89543 18.1046 1 17 1H3C1.89543 1 1 1.89543 1 3V13C1 14.1046 1.89543 15 3 15Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>' . $user->email . '</span>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div class="fff-course-box-info-details">
                <svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM0 10C0 4.47715 4.47715 0 10 0C15.5228 0 20 4.47715 20 10C20 15.5228 15.5228 20 10 20C4.47715 20 0 15.5228 0 10ZM10 5C10.5523 5 11 5.44772 11 6V9.58579L13.7071 12.2929C14.0976 12.6834 14.0976 13.3166 13.7071 13.7071C13.3166 14.0976 12.6834 14.0976 12.2929 13.7071L9.29289 10.7071C9.10536 10.5196 9 10.2652 9 10V6C9 5.44772 9.44771 5 10 5Z" fill="#00315a"/>
                </svg>
                <span >Dernier accès : ' . $lastconnect . '</span>
            </div>';
            
            if($userid == $USER->id){
            $content .= '<a target="_blank" class="smartch_btn" href="'.$url_page_licencie.'">
                Ma page licencié Athle.fr
            </a>';
            }
            $content .= '</div>
        ' . $btnconnection . '';
$content .= '</div>'; //col-6

$content .= '<div class="col-6">';
require_once('./details_stats.php');
$content .= '</div>';//col-6

$content .= '</div>'; //row

$content .= '</div>'; //fff-course-box-info
$content .= '</div>'; //col-12
$content .= '</div>'; //row




//tous les parcours
$allcourses = array();



foreach ($courses as $course) {
    $el = new stdClass();
    $el->fullname = $course->fullname;
    $el->id = $course->id;
    // $el->timecreated = $course->timecreated;
    $el->url = $CFG->wwwroot . "/course/view.php?id=" . $course->id;

    //On va chercher l'image du cours
    $course2 = new core_course_list_element($course);
    foreach ($course2->get_course_overviewfiles() as $file) {
        if ($file->is_valid_image()) {
            $imagepath = '/' . $file->get_contextid() .
                '/' . $file->get_component() .
                '/' . $file->get_filearea() .
                $file->get_filepath() .
                $file->get_filename();
            $imageurl = file_encode_url(
                $CFG->wwwroot . '/pluginfile.php',
                $imagepath,
                false
            );
            $el->img = $imageurl;
            // Use the first image found.
            break;
        }
    }
    array_push($allcourses, $el);
}

//le tableau des parametres pour la recherche
$params = array();
$param1['paramname'] = "userid";
$param1['paramvalue'] = $userid;
array_push($params, $param1);

//barre de recherche des parcours
$templatecontext = (object)[
    'formurl' => new moodle_url('/theme/remui/views/users/details.php') . '?userid=' . $userid,
    'textcontent' => "Formations suivies",
    'lang_search' => "Rechercher",
    'params' => $params,
    'search' => $search
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_search', $templatecontext);


//La pagination
if (count($courses) == 0) {
    $paginationtitle .= 'Aucun résultat';
} else if (count($courses) == 1) {
    $paginationtitle .= '1 résultat';
} else {
    $paginationtitle .= $total_rows . ' résultats - page ' . $pageno . ' sur ' . $total_pages . '';
}
$paginationarray = range(1, $total_pages); // array(1, 2, 3)

//la pagination en haut
$templatecontextpagination = (object)[
    'paginationtitle' => $paginationtitle,
    'search' => $search,
    'total_rows' => $total_rows,
    'total_pages' => $total_pages,
    'pageno' => $pageno,
    'paginationarray' => array_values($paginationarray),
    'formurl' => new moodle_url('/theme/remui/views/users/details.php.php')
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);

if (count($courses) == 0) {
    $content .= nothingtodisplay("Aucun parcours...");
}

//tous les parcours de l'utilisateur
$content .= '<div class="row" style="padding: 0 20px;">';
foreach ($allcourses as $onecourse) {

    $content .= '<div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">';

    if ($group) {
        $content .= '<a href="' . new moodle_url('/theme/remui/views/adminteam.php') . '?teamid=' . $group->id . '&userid=' . $userid . '#selected-' . $userid . '">';
    } else {
        $content .= '<a href="' . new moodle_url('/theme/remui/views/courses/details.php') . '?id=' . $onecourse->id . '">';
    }

    //on va chercher la session du cours
    $groups = $DB->get_records_sql('SELECT g.id, g.name FROM mdl_groups g
    JOIN mdl_groups_members gm ON gm.groupid = g.id
    WHERE gm.userid = ' . $user->id . ' AND g.courseid = ' . $onecourse->id, null);

    //si l'utilisateur à un groupe
    if (count($groups) > 0) {
        $group = reset($groups);
        $groupid = $group->id;

        //on va chercher les informations de session 
        $session = $DB->get_record('smartch_session', ['groupid' => $group->id]);
        $modulesstatus = getModulesStatus($onecourse->id, $session->id, $user->id);

        $total = $modulesstatus[1] + $modulesstatus[0];
        $ratio = $modulesstatus[0] . '/' . $total;
    } else {
        // l'ancien ration sans les sessions
        $ratio = getCourseCompletionRatio($user->id, $onecourse->id);
    }

    $courseprog = getCompletionPourcent($onecourse->id, $user->id);

    $img = $onecourse->img;
    if (!$img) {
        $img = new moodle_url('/theme/remui/pix/screenshot.png');
    }
    $content .= '<div class="smartch_user_course_box" style="border-radius: 15px;">
        <div class="smartch_user_course_img">
            <img style="border-radius: 15px;" src="' . $img . '" />
        </div>
    ';

    //on va chercher les groupes session de l'utilisateur
    $groups = $DB->get_records_sql('SELECT g.id, g.name FROM mdl_groups g
    JOIN mdl_groups_members gm ON gm.groupid = g.id
    WHERE gm.userid = ' . $user->id . ' AND g.courseid = ' . $onecourse->id, null);

    $group = null;
    //si l'utilisateur est dans un groupe
    if (count($groups) > 0) {
        $sessiondate = "";
        //on parcours les groupes
        foreach ($groups as $group) {
            $teamid = $group->id;
            //On va chercher le responsable pédagogique
            $coach = getResponsablePedagogique($group->id, $onecourse->id);

            //on va chercher la session 
            $session = $DB->get_record('smartch_session', ['groupid' => $group->id]);
            // var_dump($session);
            // var_dump($group->id, $onecourse->id);

            if ($session && $session->startdate && $session->enddate) {
                $sessiondate .= '<div>Session du ' . userdate($session->startdate, '%d/%m/%Y') . ' au ' . userdate($session->enddate, '%d/%m/%Y') . '</div>';
                // $sessiondate .= '<div>Responsable pédagogique : ' . $coach[0] . '</div>';
            }

        }
    } else {
        $sessiondate = "Pas de session";
        $coach = array("Aucun responsable pédagogique", null);
    }


    //on recupère le champs personnalisé duration
    $duration = getCourseCustom($onecourse->id, "duration");
    $durationblock = "";
    if(!empty($duration)){
        $durationblock = '<div style="display:flex;align-items:center;">
                    <svg style="width:20px;" class="mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span class="mr-4 FFARegular">'.$duration.'h</span>
                </div>';
    }

    //on recupère le champs personnalisé diplome
    $diplome = getCourseCustom($onecourse->id, "type");
    $diplomeblock = "";
    if(!empty($diplome)){
        $diplomeblock = '<div style="display:flex;align-items:center;">
                    <svg style="width:20px;" class="mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                    </svg>
                    <span class="mr-4 FFARegular">'.$diplome.'</span>
                </div>';
    }


    $content .= '<div class="smartch_user_course_box_info">
                        <div>
                            <h5>' . $onecourse->fullname . '</h5>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin:10px;">
                                '.$durationblock.'
                                <div style="display:flex;align-items:center;">
                                    <svg style="width:20px;" class="mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                                    </svg>
                                    <span class="mr-4 FFARegular">' . $sessiondate . '</span>
                                </div>
                                ' . $diplomeblock . '
                            </div>
                            <div class="smartch_progress_bar_box">
                                <div class="smartch_progress_bar">
                                    <div class="smartch_progress_bar_number" style="display:none;">' . $courseprog . '%</div>
                                    <div class="smartch_progress_bar_gain" style="width:' . $courseprog . '% !important;"></div>
                                </div>
                            </div>
                        </div>
                    </div>';
   
    $content .= '
            </div>';

    $content .= '</a>';

    $content .= '</div>'; //col
}



$content .= '</div>'; //row

//template avec tous les parcours
// $templatecontext = (object)[
//     'url' => new moodle_url('/'),
//     'courses' => $allcourses
// ];
// $content .= $OUTPUT->render_from_template('theme_remui/smartch_admin_all_courses', $templatecontext);

//la pagination en bas
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);

// $content .= html_writer::end_div(); //container

if ($sent) {
    displayMessageSent();
}

echo $content;

echo $OUTPUT->footer();

//pour la pagination
echo '<script>

window.onload = function(){
    var els = document.getElementsByClassName("page' . $pageno . '");
    Array.from(els).forEach((el) => {
        el.setAttribute("selected", "selected");
    });
};

</script>';
