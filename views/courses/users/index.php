<?php

require_once(__DIR__ . '/../../../../../config.php');
require_once('../../utils.php');

require_login();

global $USER, $DB, $CFG;

$courseid = required_param('courseid', PARAM_INT);
$course = $DB->get_record('course', ['id' => $courseid]);

$content = '';
$paginationtitle = '';
$prevurl = '';
$nexturl = '';

//On va chercher le rôle le plus haut de l'utilisateur
$rolename = getMainRole();

$newroleuserid = $_POST['newroleuserid'];
$newroleid = $_POST['newroleid'];

if($newroleuserid && $newroleid){
    // Charger le contexte du cours
    $context = context_course::instance($courseid);
    //on va chercher le rôle actuel de l'utilisateur
    $oldrole = getUserRoleFromCourse($courseid, $newroleuserid);
    
    // Enlever le rôle
    role_unassign($oldrole->roleid, $newroleuserid, $context->id);
    // // Ajouter le nouveau rôle
    role_assign($newroleid, $newroleuserid, $context->id);

    $messagenotif = "Le rôle a été modifié";
}

isStudent();

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/courses/users/index.php'), ['courseid'=>$courseid]);
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Utilisateurs");

echo '<style>

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

smartchModalRole();

if ($messagenotif) {
    displayNotification($messagenotif);
}

// echo html_writer::start_div('container');

$search = optional_param('search', '', PARAM_TEXT);
$pageno = optional_param('pageno', 1, PARAM_TEXT);

//le tableau des parametres pour la recherche et pagination
$params = array();
$filter = '';

$param0['paramname'] = "courseid";
$param0['paramvalue'] = $courseid;
array_push($params, $param0);
$filter = '&courseid=' . $courseid;

if ($search != '') {
    $param1['paramname'] = "search";
    $param1['paramvalue'] = $search;
    array_push($params, $param1);
    $filter = '&search=' . $search;
}

$no_of_records_per_page = 10;
$offset = ($pageno - 1) * $no_of_records_per_page;

$filtersqlsearch = "";
$filtersqlgroup = "";

if (!empty($search)) {
    $search = trim($search);
    $filtersqlsearch .= ' AND (lower(u.firstname) LIKE "%' . $search . '%" 
            OR lower(u.lastname) LIKE "%' . $search . '%"
            OR lower(u.username) LIKE "%' . $search . '%"
            OR concat(lower(u.firstname) , " " , lower(u.lastname)) LIKE "%' . $search . '%"
            OR lower(u.email) LIKE "%' . $search . '%") ';
}

// //on divise les requetes en fonction des rôles
// if ($rolename == "super-admin" || $rolename == "manager") {

// }

if($rolename == "smalleditingteacher" || $rolename == "editingteacher" || $rolename == "teacher"){
    $filtersqlgroup .= ' AND gm.groupid IN (
        SELECT groupid
        FROM mdl_groups_members
        WHERE userid = ' . $USER->id . '
    ) ';
}

    
$queryusers = 'SELECT u.id, u.username, u.firstname, u.lastname, u.email
        FROM mdl_user u
        JOIN mdl_user_enrolments ue ON ue.userid = u.id
        JOIN mdl_enrol e ON e.id = ue.enrolid
        LEFT JOIN mdl_groups_members gm ON gm.userid = u.id
        WHERE e.courseid = ' . $courseid . '
        '.$filtersqlsearch.' '.$filtersqlgroup.'
        LIMIT ' . $offset . ', ' . $no_of_records_per_page;
$total_pages_sql = 'SELECT COUNT(DISTINCT u.id) count 
        FROM mdl_user u
        JOIN mdl_user_enrolments ue ON ue.userid = u.id
        JOIN mdl_enrol e ON e.id = ue.enrolid
        LEFT JOIN mdl_groups_members gm ON gm.userid = u.id
        WHERE e.courseid = ' . $courseid . '
            '.$filtersqlsearch.' '.$filtersqlgroup;
    
$queryallusers = 'SELECT u.id, u.username, u.firstname, u.lastname, u.email
FROM mdl_user u
JOIN mdl_user_enrolments ue ON ue.userid = u.id
JOIN mdl_enrol e ON e.id = ue.enrolid
LEFT JOIN mdl_groups_members gm ON gm.userid = u.id
WHERE e.courseid = ' . $courseid . '
'.$filtersqlgroup;
    

$users = $DB->get_records_sql($queryusers, null);
// $users = $DB->get_recordset_sql($queryusers, null);
// var_dump($users);

$allusers = $DB->get_records_sql($queryallusers, null);

$result = $DB->get_records_sql($total_pages_sql, null);
$total_rows = reset($result)->count;
$total_pages = ceil($total_rows / $no_of_records_per_page);

//le header avec bouton de retour au panneau admin
$templatecontextheader = (object)[
    'url' => new moodle_url('/theme/remui/views/courses/index.php'),
    'textcontent' => 'Retour aux parcours'
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);

//le titre
$content .= '<h3 class="FFF-title1" style="margin-top: 80px;">
    <span class="FFABold FFF-White" style="letter-spacing:1px;">'.$course->fullname.'</span>
</h3>';

//tous les utilisateurs
$allusers = array();

foreach ($users as $user) {
    $el['firstname'] = $user->firstname;
    $el['lastname'] = $user->lastname;
    $el['id'] = $user->id;
    $el['url'] = $CFG->wwwroot . "/user/view.php?id=" . $user->id;
    array_push($allusers, $el);
}

//barre de recherche des parcours
$templatecontext = (object)[
    'formurl' => new moodle_url('/theme/remui/views/courses/users/index.php'),
    'textcontent' => "Apprenants dans la formation " . $course->fullname,
    'lang_search' => "Rechercher",
    'params' => $params,
    'search' => $search
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_search', $templatecontext);


//La pagination
if (count($users) == 0) {
    $paginationtitle .= 'Aucun résultat';
} else if (count($users) == 1) {
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
    $prevurl = new moodle_url('/theme/remui/views/courses/users/index.php?pageno=' . $newpage) . $filter;
}

if ($pageno == $total_pages) {
    $next = false;
} else {
    $next = true;
    $newpage = $pageno + 1;
    $nexturl = new moodle_url('/theme/remui/views/courses/users/index.php?pageno=' . $newpage) . $filter;
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
    'formurl' => new moodle_url('/theme/remui/views/courses/users/index.php.php')
];

if (count($users) > 0) {
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);
}

//affichage de la table de tous les utilisateurs
$content .= '<div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <table class="smartch_table">
                
                <tbody>';
foreach ($users as $user) {

    // if ($user->lastaccess == 0) {
    //     $lastaccess = "Jamais";
    // } else {
    //     $lastaccess = userdate($user->lastaccess, get_string('strftimedate'));
    // }

    $content .= '<tr>
                    <td style="text-transform:capitalize;">
                        <svg width="50" height="50" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="24" cy="24" r="24" fill="#E2E8F0"/>
                            <path d="M28 19C28 21.2091 26.2091 23 24 23C21.7909 23 20 21.2091 20 19C20 16.7909 21.7909 15 24 15C26.2091 15 28 16.7909 28 19Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M24 26C20.134 26 17 29.134 17 33H31C31 29.134 27.866 26 24 26Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span style="margin-left: 10px;"><a href="' . new moodle_url('/theme/remui/views/users/details.php') . '?userid=' . $user->id . '&returnurl='.$PAGE->url.'">' . $user->firstname . ' ' . $user->lastname . '</a></span>
                    </td>
                    <td>' . $user->email . '</td>';


                    $role = getUserRoleFromCourse($courseid, $user->id);
                    if($role){
                        if($role->name){
                            $rolename = $role->name;
                        } else {
                            $rolename = $role->shortname;
                        }
                    }
                        
                    $content .= '<td>
                        <div style="display:none;">' . displayRole($user->id) . '</div>
                        <a class="crea_pagination_btn" onclick="modifyRole('.$user->id.', '.$courseid.', '.$role->roleid.');" >' . $rolename . '</a>
                    </td>';

                    $content .= '<td><a class="smartch_table_btn" href="' . new moodle_url('/theme/remui/views/users/details.php') . '?userid=' . $user->id . '&returnurl='.$PAGE->url.'">Consulter</a></td>
                </tr>';
}

$content .= '</tbody>
            </table>
        </div>
    </div>';

//la pagination en bas
if (count($users) > 0) {
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);
}

// $content .= html_writer::end_div(); //container

echo $content;

echo $OUTPUT->footer();

//pour la pagination
echo '<script>

// window.onload = function(){
//     var els = document.getElementsByClassName("page' . $pageno . '");
//     Array.from(els).forEach((el) => {
//         el.setAttribute("selected", "selected");
//     });
// };

    var els = document.getElementsByClassName("page' . $pageno . '");
    Array.from(els).forEach((el) => {
        el.setAttribute("selected", "selected");
    });

</script>';

echo '<script>
        function modifyRole(userid, courseid, actualroleid){
            let htmlrole = "";
            htmlrole += "<h3>Modifier le rôle</h3>";
            // htmlrole += "<div>Le rôle actuel est "+actualroleid+"</div>";
            // htmlrole += "<div>Rôle actuel sur la formation :</div>";
            document.querySelector("#newroleid").value = actualroleid;
            document.querySelector("#newroleuserid").value = userid;
            document.querySelector("#modal_content").innerHTML = htmlrole;
            document.querySelector(".smartch_modal_container").style.display = "flex";
        }
                            
    </script>';
