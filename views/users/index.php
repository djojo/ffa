<?php

require_once(__DIR__ . '/../../../../config.php');
require_once('../utils.php');

require_login();

global $USER, $DB, $CFG;


$content = '';
$paginationtitle = '';
$prevurl = '';
$nexturl = '';

//On va chercher le rôle le plus haut de l'utilisateur
$rolename = getMainRole();

isStudent();

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/users/index.php'));
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

$no_of_records_per_page = 24;
$offset = ($pageno - 1) * $no_of_records_per_page;

$filtersqlsearch = "";

if (!empty($search)) {
    $search = trim($search);
    $filtersqlsearch .= ' AND (lower(u.firstname) LIKE "%' . $search . '%" 
            OR lower(u.lastname) LIKE "%' . $search . '%"
            OR lower(u.username) LIKE "%' . $search . '%"
            OR concat(lower(u.firstname) , " " , lower(u.lastname)) LIKE "%' . $search . '%"
            OR lower(u.email) LIKE "%' . $search . '%") ';
}

//on divise les requetes en fonction des rôles
if ($rolename == "super-admin" || $rolename == "manager") {
    
    $queryusers = 'SELECT * 
        FROM mdl_user u
        WHERE u.email != "root@localhost"
        AND u.deleted = 0
        '.$filtersqlsearch.'
        LIMIT ' . $offset . ', ' . $no_of_records_per_page;
    $total_pages_sql = 'SELECT COUNT(*) count 
        FROM mdl_user u
        WHERE u.email != "root@localhost"
        AND u.deleted = 0
        '.$filtersqlsearch;

    $totalusers = $DB->get_records_sql('SELECT * FROM mdl_user u
    WHERE u.email <> "root@localhost"', null);
  
} else if ($rolename == "smalleditingteacher" || $rolename == "editingteacher" || $rolename == "teacher") {

    //on prend seulement les utilisateurs de son groupe
    $queryusers = 'SELECT DISTINCT u.*
        FROM mdl_user u
        JOIN mdl_groups_members gm ON gm.userid = u.id
        WHERE email != "root@localhost"
        AND deleted = 0
        AND gm.groupid IN (
            SELECT groupid
            FROM mdl_groups_members
            WHERE userid = ' . $USER->id . '
        )
        '.$filtersqlsearch.'
        LIMIT ' . $offset . ', ' . $no_of_records_per_page;

    $total_pages_sql = 'SELECT COUNT(*) count 
        FROM mdl_user u
        JOIN mdl_groups_members gm ON gm.userid = u.id
        WHERE email != "root@localhost"
        AND deleted = 0
        AND gm.groupid IN (
            SELECT groupid
            FROM mdl_groups_members
            WHERE userid = ' . $USER->id . '
        )
        '.$filtersqlsearch;

    $totalusers = $DB->get_records_sql('SELECT DISTINCT u.*
        FROM mdl_user u
        JOIN mdl_groups_members gm ON gm.userid = u.id
        WHERE email != "root@localhost"
        AND deleted = 0
        AND gm.groupid IN (
            SELECT groupid
            FROM mdl_groups_members
            WHERE userid = ' . $USER->id . '
        )', null);
}

$users = $DB->get_records_sql($queryusers, null);



$result = $DB->get_records_sql($total_pages_sql, null);
$total_rows = reset($result)->count;
$total_pages = ceil($total_rows / $no_of_records_per_page);

var_dump($total_rows);

//le header avec bouton de retour au panneau admin
$templatecontextheader = (object)[
    'url' => new moodle_url('/theme/remui/views/adminmenu.php'),
    'textcontent' => 'Retour'
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);


//le titre
$content .= '<h3 class="FFF-title1" style="margin-top: 80px;">
    <span class="FFABold FFF-White" style="letter-spacing:1px;">Utilisateurs</span>
</h3>';

// $content .= '<div class="row" style="margin:30px 0;"></div>';

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
    'formurl' => new moodle_url('/theme/remui/views/users/index.php'),
    'textcontent' => count($totalusers) . " membres",
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
    $prevurl = new moodle_url('/theme/remui/views/users/index.php?pageno=' . $newpage) . $filter;
}

if ($pageno == $total_pages) {
    $next = false;
} else {
    $next = true;
    $newpage = $pageno + 1;
    $nexturl = new moodle_url('/theme/remui/views/users/index.php?pageno=' . $newpage) . $filter;
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
    'formurl' => new moodle_url('/theme/remui/views/users/index.php')
];

if (count($users) > 0) {
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);
}

//affichage de la table de tous les utilisateurs
$content .= '<div class="row">';
foreach ($users as $user) {

    if ($user->lastaccess == 0) {
        $lastaccess = "Jamais";
    } else {
        $lastaccess = userdate($user->lastaccess, get_string('strftimedate'));
    }

    $content .= '<div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">';

    $content .= '<a href="' . new moodle_url('/theme/remui/views/users/details.php') . '?return=users&userid=' . $user->id . '">';
    $content .= '<div style="display:flex;justify-content:space-between;padding:30px 10px;">
                        <div style="display:flex;">
                            <div style="width: 50px;">
                                <svg width="50" height="50" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="24" cy="24" r="24" fill="#E2E8F0"/>
                                    <path d="M28 19C28 21.2091 26.2091 23 24 23C21.7909 23 20 21.2091 20 19C20 16.7909 21.7909 15 24 15C26.2091 15 28 16.7909 28 19Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M24 26C20.134 26 17 29.134 17 33H31C31 29.134 27.866 26 24 26Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div style="margin-left: 10px;">
                                <div style="text-transform:capitalize;">' . $user->firstname . ' ' . $user->lastname . '</div>
                                <div>' . $user->email . '</div>
                            </div>
                        </div>
                        <div style="margin-left: 10px;min-width: 80px;">
                            <div>Dernier accès</div>
                            <div>' . $lastaccess . '</div>
                        </div>
                    </div>';
                    
                    
    // $content .= '<div>
    //                     <a class="smartch_table_btn" href="' . new moodle_url('/theme/remui/views/users/details.php') . '?return=users&userid=' . $user->id . '">
    //                         <svg style="width:20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
    //                             <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
    //                             <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
    //                         </svg>

    //                     </a>
    //                     <a class="smartch_table_btn ml-2" href="' . new moodle_url('/theme/remui/views/users/message.php') . '?userid=' . $user->id . '&returnurl='.$PAGE->url.'">
    //                         <svg style="width:20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
    //                             <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
    //                         </svg>
    //                     </a>
    //                 </div>';
    $content .= '</a>';
    $content .= '</div>';
}

$content .= '</div>';

//la pagination en bas
if (count($users) > 0) {
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);
}

// $content .= html_writer::end_div(); //container

echo $content;

echo $OUTPUT->footer();

//pour la pagination
echo '<script>

    var els = document.getElementsByClassName("page' . $pageno . '");
    Array.from(els).forEach((el) => {
        el.setAttribute("selected", "selected");
    });

</script>';
