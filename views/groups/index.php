<?php

require_once(__DIR__ . '/../../../../config.php');
require_once('../utils.php');

require_login();

global $USER, $DB, $CFG;

$params = null;
$content = '';
$paginationtitle = '';
$prevurl = '';
$nexturl = '';

//On va chercher le rôle le plus haut de l'utilisateur
$rolename = getMainRole();

isStudent();

$courseid = optional_param('courseid', null, PARAM_INT);

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/groups/index.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Tous les groupes");

echo '<style>

.select2-container {
    width: auto !important;
    margin-bottom: 10px !important;
}

.select2-container--default .select2-selection--single{
    border:none !important;
    
}
.select2-container--default .select2-selection--single .select2-selection__rendered{
    font-size: 2rem;
    color: #01315A !important;
    line-height: normal !important;
}

.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable{
    background: #01315A !important;
}

.select2-container .select2-selection--single{
    height: auto !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow{
    top: 4px !important;
    right: -10px !important;
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

</style>';

echo $OUTPUT->header();

echo '<script
  src="https://code.jquery.com/jquery-3.7.1.min.js"
  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
  crossorigin="anonymous"></script>';
echo '<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>';
// echo html_writer::start_div('container');

$search = optional_param('search', '', PARAM_TEXT);
$pageno = optional_param('pageno', 1, PARAM_TEXT);

//le tableau des parametres pour la recherche et pagination
$params = array();
$filter = '';

if ($search != '') {
    $param1['paramname'] = "search";
    $param1['paramvalue'] = $search;
    array_push($params, $param1);
    $filter = '&search=' . $search;
}

if ($rolename == "super-admin" || $rolename == "manager") {
    $filteradmin = '';
} else {
    if ($search != "") {
        $filteradmin = '
        AND u.id = ' . $USER->id . '';
    } else {
        $filteradmin = 'JOIN mdl_groups_members gm ON gm.groupid = g.id
        JOIN mdl_user u ON u.id = gm.userid
        WHERE u.id = ' . $USER->id . '';
    }
}

if($courseid){
    $filteradmin .= ' AND g.courseid = '. $courseid . ' ';
}



$no_of_records_per_page = 6;
$offset = ($pageno - 1) * $no_of_records_per_page;

if ($search != "") {
    $querygroups = 'SELECT DISTINCT g.id as id, c.id as courseid, c.shortname as coursename, g.name as groupname, g.courseid as courseid 
        FROM mdl_groups g
        JOIN mdl_course c ON c.id = g.courseid
        JOIN mdl_groups_members gm ON gm.groupid = g.id
        JOIN mdl_user u ON u.id = gm.userid
        WHERE (lower(g.name) LIKE "%' . $search . '%" 
        OR lower(c.shortname) LIKE "%' . $search . '%"
        OR lower(u.email) LIKE "%' . $search . '%"
        OR concat(lower(u.firstname) , " " , lower(u.lastname)) LIKE "%' . $search . '%"
        OR lower(u.firstname) LIKE "%' . $search . '%"
        OR lower(u.lastname) LIKE "%' . $search . '%")
        ' . $filteradmin . '
        ORDER BY g.id ASC
        LIMIT ' . $offset . ', ' . $no_of_records_per_page;
    $total_pages_sql = 'SELECT g.id, COUNT(*) count FROM mdl_groups g
        JOIN mdl_course c ON c.id = g.courseid
        JOIN mdl_groups_members gm ON gm.groupid = g.id
        JOIN mdl_user u ON u.id = gm.userid
        WHERE (lower(g.name) LIKE "%' . $search . '%" 
        OR lower(c.shortname) LIKE "%' . $search . '%"
        OR lower(u.email) LIKE "%' . $search . '%"
        OR concat(lower(u.firstname) , " " , lower(u.lastname)) LIKE "%' . $search . '%"
        OR lower(u.firstname) LIKE "%' . $search . '%"
        OR lower(u.lastname) LIKE "%' . $search . '%")
        ' . $filteradmin;
} else {
    $querygroups = 'SELECT g.id as id, c.id as courseid, c.shortname as coursename, g.name as groupname, g.courseid as courseid 
        FROM mdl_groups g
        JOIN mdl_course c ON c.id = g.courseid
        ' . $filteradmin . '
        LIMIT ' . $offset . ', ' . $no_of_records_per_page . '
        ';
    $total_pages_sql = 'SELECT COUNT(*) count 
        FROM mdl_groups g
        JOIN mdl_course c ON c.id = g.courseid
        ' . $filteradmin . '';
}

// echo $querygroups;
$groups = $DB->get_records_sql($querygroups, null);
// echo json_encode($groups);
$allgroups = $DB->get_records('groups', null);

$result = $DB->get_records_sql($total_pages_sql, null);
$total_rows = reset($result)->count;
$total_pages = ceil($total_rows / $no_of_records_per_page);

//le header avec bouton de retour au panneau admin
$templatecontextheader = (object)[
    'url' => new moodle_url('/'),
    'textcontent' => 'Retour'
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);

//le titre
$content .= '<h3 class="FFF-title1" style="margin-top: 80px;">
    <span class="FFABold FFF-White" style="letter-spacing:1px;">Groupes</span>
</h3>';


//le select des groupes

$content .= '<div class="row">';
$content .= '<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">';
$content .= '<form style="display: inline;" id="search-form" method="get" action="'.new moodle_url('/theme/remui/views/groups/index.php').'">';
$content .= '<div class="smartch_flex_mobile" style="justify-content: space-between;align-items: center;">';
$content .= '<select class="select2" name="courseid" onchange="this.form.submit();">';
$content .= '<option>Tous les parcours</option>';
//On va chercher toutes les formations
$allcourses = $DB->get_records_sql('SELECT * FROM mdl_course WHERE format != "site" AND visible = 1', null);
foreach ($allcourses as $onecourse) {
    if($onecourse->id == $courseid){
        $content .= '<option selected value="' . $onecourse->id . '">' . $onecourse->fullname . '</option>';
    } else {
        $content .= '<option value="' . $onecourse->id . '">' . $onecourse->fullname . '</option>';
    }
} 
$content .= '</select>';
            // <h2 style="letter-spacing:1px;max-width:70%;cursor:pointer;" onclick="location.href='{{formurl}}'" class="FFABold FFF-Blue">{{textcontent}}</h2> 
$content .= '<div>';
// $content .= '<input type="hidden" name="{{paramname}}" value="{{paramvalue}}"/>';
$content .= '<input autocomplete="off" class="smartch_input_search" type="text" name="search" placeholder="Rechercher" value="'.$search.'" autocomplete="off"/>';
$content .= '</div>';
$content .= '</div>';
$content .= '</form>';
$content .= '</div>';
$content .= '</div>';

// //barre de recherche des utilisateurs de l'équipe
// $templatecontext = (object)[
//     'formurl' => new moodle_url('/theme/remui/views/groups/index.php'),
//     'textcontent' => $selectcontent,
//     'params' => $params,
//     'lang_search' => "Rechercher",
//     'search' => $search
// ];
// $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_search', $templatecontext);


//La pagination
if (count($groups) == 0) {
    $paginationtitle .= 'Aucun résultat';
} else if (count($groups) == 1) {
    $paginationtitle .= '1 résultat';
} else {
    $paginationtitle .= $total_rows . ' résultats - page ' . $pageno . ' sur ' . $total_pages . '';
}
$paginationarray = range(1, $total_pages); // array(1, 2, 3)

if ($pageno == 1) {
    $previous = false;
} else {
    $previous = true;
    $newpage = $pageno - 1;
    $prevurl = new moodle_url('/theme/remui/views/groups/index.php?pageno=' . $newpage) . $filter;
}

if ($pageno == $total_pages) {
    $next = false;
} else {
    $next = true;
    $newpage = $pageno + 1;
    $nexturl = new moodle_url('/theme/remui/views/groups/index.php?pageno=' . $newpage) . $filter;
}

//la pagination en haut
$templatecontextpagination = (object)[
    'paginationtitle' => $paginationtitle,
    'search' => $search,
    'params' => $params,
    'total_rows' => $total_rows,
    'total_pages' => $total_pages,
    'pageno' => $pageno,
    'previous' => $previous,
    'next' => $next,
    'prevurl' => $prevurl,
    'nexturl' => $nexturl,
    'paginationarray' => array_values($paginationarray),
    'formurl' => new moodle_url('/theme/remui/views/groups/index.php')
];
if (count($groups) > 0) {
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);
}

if (count($groups) == 0) {
    $content .= nothingtodisplay("Il n'y a pas de groupe à afficher...");
}

$content .= '<div class="row" style="padding:0 30px;">';
foreach ($groups as $group) {
    //on va chercher le cours du groupe
    // $course = $DB->get_record('course', ['id' => $group->courseid]);
    //on va chercher les membres de l'équipe
    // $teamates = $DB->get_records('groups_members', ['groupid' => $group->id], '', '*', 0, 6);
    // $allmates = $DB->get_records('groups_members', ['groupid' => $group->id]);


    $querymates = '
                SELECT DISTINCT u.id as userid, u.firstname, u.lastname, r.shortname, r.id as roleid
                FROM mdl_role_assignments AS ra 
                JOIN mdl_user_enrolments AS ue ON ra.userid = ue.userid 
                JOIN mdl_role AS r ON ra.roleid = r.id 
                JOIN mdl_context AS c ON c.id = ra.contextid 
                JOIN mdl_enrol AS e ON e.courseid = c.instanceid AND ue.enrolid = e.id 
                JOIN mdl_user u ON u.id = ue.userid
                JOIN mdl_groups_members gm ON u.id = gm.userid

                WHERE gm.groupid = ' . $group->id . ' 
                AND r.shortname = "student"
                LIMIT 0, 4
                ';
    $queryallmates = '
                SELECT DISTINCT u.id as userid, u.firstname, u.lastname, r.shortname, r.id as roleid
                FROM mdl_role_assignments AS ra 
                JOIN mdl_user_enrolments AS ue ON ra.userid = ue.userid 
                JOIN mdl_role AS r ON ra.roleid = r.id 
                JOIN mdl_context AS c ON c.id = ra.contextid 
                JOIN mdl_enrol AS e ON e.courseid = c.instanceid AND ue.enrolid = e.id 
                JOIN mdl_user u ON u.id = ue.userid
                JOIN mdl_groups_members gm ON u.id = gm.userid
                WHERE gm.groupid = ' . $group->id . ' 
                AND r.shortname = "student"
                ';
    $teamates = $DB->get_records_sql($querymates, null);
    $allmates = $DB->get_records_sql($queryallmates, null);

    $totalmates = count($allmates);
    $el['total'] = $totalmates;
    $el['teamates'] = $teamates;

    if ($el['total'] > 1) {
        $nbmembres = $el['total'] . ' membres';
    } else {
        $nbmembres = $el['total'] . ' membre';
    }

    $groupname = extraireNomEquipe($group->groupname);

    $content .= '
        <div class="col-md-4" style="padding: 0 20px;margin-bottom:20px;">
        <div class="row">
            <div class="col-md-12" style="display: flex;justify-content: space-between;padding: 20px 0;">
                
                <div style="max-width: 70%;">
                    <div>
                        <a href="' . new moodle_url('/theme/remui/views/groups/details.php?return=groups&groupid=' . $group->id) . '"><span class="fff-title-team">' . $groupname . '</span></a>
                    </div>
                    <div>
                        <span style="margin-right:10px;">' . $nbmembres . '</span>
                        <svg style="display:none;" onclick="window.location.href=\'' . new moodle_url('/theme/remui/views/groups/details.php?return=groups&groupid=' . $group->id) . '&message=1#sendmessageteam\'" style="cursor:pointer;" width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 4L8.8906 9.2604C9.5624 9.70827 10.4376 9.70827 11.1094 9.2604L19 4M3 15H17C18.1046 15 19 14.1046 19 13V3C19 1.89543 18.1046 1 17 1H3C1.89543 1 1 1.89543 1 3V13C1 14.1046 1.89543 15 3 15Z" stroke="#0B427C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <a class="smartch_btn" href="' . new moodle_url('/theme/remui/views/groups/details.php?return=groups&groupid=' . $group->id) . '">Tout voir</a>

            </div>
        </div>';



    $content .= '<div>';

    if (count($teamates) == 0) {
        $content .= '<h3 class="no_member_to_display">Il n\'y a pas de membre dans ce groupe...</h3>';
    }

    foreach ($el['teamates'] as $mate) {
        $userteam = $DB->get_record('user', ['id' => $mate->userid]);
        $courseprog = getCompletionPourcent($group->courseid, $userteam->id);
        $content .= '<div style="padding: 15px 5px;">
                    <div onclick="window.location.href=\'' . new moodle_url('/theme/remui/views/users/details.php?return=groups&userid=' . $userteam->id) . '\'" style="cursor:pointer;display: flex;justify-content: space-between;width: 100%;"> 
                        <div>
                            <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="55.4841" height="56" rx="27.742" fill="#EDF2F7"/>
                                <path d="M31.666 21.4167C31.666 23.4417 29.8752 25.0833 27.666 25.0833C25.4569 25.0833 23.666 23.4417 23.666 21.4167C23.666 19.3916 25.4569 17.75 27.666 17.75C29.8752 17.75 31.666 19.3916 31.666 21.4167Z" stroke="#00315A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M27.666 27.8333C23.8 27.8333 20.666 30.7062 20.666 34.25H34.666C34.666 30.7062 31.532 27.8333 27.666 27.8333Z" stroke="#00315A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>

                        </div>
                        <div style="margin-left: 10px;width: 100%;">';
        $matenamestring =  $userteam->firstname . ' ' . $userteam->lastname;
        if (strlen($user->lastname) > 15 || strlen($user->firstname) > 15 || strlen($user->firstname . $user->lastname) > 30) {
            $content .= '<div style="line-height: 17px;" class="matename FFARegular" style="height: 50px;">
                                ' . $matenamestring . '
                            </div>';
        } else {
            $content .= '<div class="matename FFARegular" style="padding: 10px 0;">
                                ' . $matenamestring . '
                            </div>';
        }
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

    $content .= '</div>';

    $content .= '</div>';
}
$content .= '</div>';

//la pagination en bas
if (count($groups) > 0) {
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);
}

// $content .= html_writer::end_div(); //container

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

$(document).ready(function() {
        $(".select2").select2();
    });

</script>';
