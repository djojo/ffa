<?php
// This file is part of Moodle Course Rollover Plugin
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     smartch
 * @author      Geoffroy Rouaix
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once('./utils.php');

// defined('MOODLE_INTERNAL') || die();

require_login();
isAdminFormation();

global $USER, $DB, $CFG;

$content = '';
$paginationtitle = '';
$prevurl = '';
$nexturl = '';

$params = array();
$sqlfilters = '';

$contentsessions = '';
$exportdata = [];

//On va chercher le rôle le plus haut de l'utilisateur
$rolename = getMainRole();

// isAdmin();
$action = optional_param('action', '', PARAM_TEXT);
$status = optional_param('status', 'all', PARAM_TEXT);
$startdate = optional_param('startdate', null, PARAM_TEXT);
$enddate = optional_param('enddate', null, PARAM_TEXT);
$search = optional_param('search', '', PARAM_TEXT);
$pageno = optional_param('pageno', 1, PARAM_TEXT);
$courseid = optional_param('courseid', null, PARAM_INT);

$filter = '?status=' . $status;

//le tableau des parametres pour la recherche et pagination

if ($search != '') {
    $param1['paramname'] = "search";
    $param1['paramvalue'] = $search;
    array_push($params, $param1);
    $filter .= '&search=' . $search;
}

if ($courseid) {
    $param3['paramname'] = "courseid";
    $param3['paramvalue'] = $courseid;
    array_push($params, $param3);
    $filter .= '&courseid=' . $courseid;
    $sqlfilters .= ' AND c.id = ' . $courseid . ' ';
}


$param2['paramname'] = "status";
$param2['paramvalue'] = $status;
array_push($params, $param2);

$dateActuelle = new DateTime();
$startdate = $dateActuelle->format('Y-m-d');
$startdateobject = new DateTime($startdate);
$startdatetimestamp = $startdateobject->getTimestamp();

if ($status == "started") {
    $sqlfilters .= ' AND s.startdate < ' . $startdatetimestamp . ' AND s.enddate > ' . $startdatetimestamp . ' ';
} else if ($status == "tocome") {
    $sqlfilters .= ' AND s.startdate > ' . $startdatetimestamp;
} else if ($status == "finished") {
    $sqlfilters .= ' AND s.enddate < ' . $startdatetimestamp;
}


// if (!$enddate) {
//     $enddate = date('Y-m-d');
// }
// if (!$startdate) {
//     $dateActuelle = new DateTime();
//     $dateActuelle->sub(new DateInterval('P1M'));
//     $startdate = $dateActuelle->format('Y-m-d');
// }

// $filter .= '&enddate=' . $enddate;
// $filter .= '&startdate=' . $startdate;

// $startdateobject = new DateTime($startdate);
// $startdatetimestamp = $startdateobject->getTimestamp();
// $enddateobject = new DateTime($enddate);
// $enddatetimestamp = $enddateobject->getTimestamp();


// if ($startdatetimestamp && $enddatetimestamp) {
//     $sqlfilters .= ' AND s.startdate > ' . $startdatetimestamp . ' AND s.startdate < ' . $enddatetimestamp . ' ';
// }

$no_of_records_per_page = 25;
$offset = ($pageno - 1) * $no_of_records_per_page;

$limit = "";
if (!$action) {
    $limit = 'LIMIT ' . $offset . ', ' . $no_of_records_per_page;
}

if ($search != "") {
    $querygroups = 'SELECT DISTINCT g.id as id, c.id as courseid, c.shortname as coursename, g.name as groupname, g.courseid as courseid, s.startdate, s.enddate
        FROM mdl_smartch_session s
        JOIN mdl_groups g ON s.groupid = g.id
        JOIN mdl_course c ON c.id = g.courseid
        JOIN mdl_groups_members gm ON gm.groupid = g.id
        JOIN mdl_user u ON u.id = gm.userid
        WHERE (lower(g.name) LIKE "%' . $search . '%" 
        OR lower(c.shortname) LIKE "%' . $search . '%"
        OR lower(u.email) LIKE "%' . $search . '%"
        OR concat(lower(u.firstname) , " " , lower(u.lastname)) LIKE "%' . $search . '%"
        OR lower(u.firstname) LIKE "%' . $search . '%"
        OR lower(u.lastname) LIKE "%' . $search . '%")
        ' . $sqlfilters . '
        ORDER BY s.startdate DESC
        ' . $limit;
    $total_pages_sql = 'SELECT g.id, COUNT(*) count 
        FROM mdl_smartch_session s
        JOIN mdl_groups g ON s.groupid = g.id
        JOIN mdl_course c ON c.id = g.courseid
        JOIN mdl_groups_members gm ON gm.groupid = g.id
        JOIN mdl_user u ON u.id = gm.userid
        WHERE (lower(g.name) LIKE "%' . $search . '%" 
        OR lower(c.shortname) LIKE "%' . $search . '%"
        OR lower(u.email) LIKE "%' . $search . '%"
        OR concat(lower(u.firstname) , " " , lower(u.lastname)) LIKE "%' . $search . '%"
        OR lower(u.firstname) LIKE "%' . $search . '%"
        OR lower(u.lastname) LIKE "%' . $search . '%")
        ' . $sqlfilters;
} else {
    $querygroups = 'SELECT g.id as id, c.id as courseid, c.shortname as coursename, g.name as groupname, g.courseid as courseid, s.startdate, s.enddate
        FROM mdl_smartch_session s
        JOIN mdl_groups g ON s.groupid = g.id
        JOIN mdl_course c ON c.id = g.courseid
        ' . $sqlfilters . '
        ORDER BY s.startdate DESC
        ' . $limit;
    $total_pages_sql = 'SELECT COUNT(*) count 
        FROM mdl_smartch_session s
        JOIN mdl_groups g ON s.groupid = g.id
        JOIN mdl_course c ON c.id = g.courseid
        ' . $sqlfilters . '';
}

$groups = $DB->get_records_sql($querygroups, null);
$allgroups = $DB->get_records('groups', null);

$result = $DB->get_records_sql($total_pages_sql, null);
$total_rows = reset($result)->count;
$total_pages = ceil($total_rows / $no_of_records_per_page);

$titlerapport = "";
if ($status == "started") {
    $titlerapport = "Liste des sessions en cours";
} else if ($status == "finished") {
    $titlerapport = "Liste des sessions terminés";
} else if ($status == "tocome") {
    $titlerapport = "Liste des sessions à venir";
} else if ($status == "all") {
    $titlerapport = "Liste des sessions";
}

//on rajoute le nom du cours dans le tableau
$rowdata = [];
array_push($rowdata, $titlerapport);
array_push($rowdata, $total_rows . ' sessions');
array_push($exportdata, $rowdata);

$contentsessions .= '<div class="row">';
$contentsessions .= '<div class="col-md-12">';
$contentsessions .= '<table class="smartch_table">';

foreach ($groups as $group) {

    $rowdata = [];

    $contentsessions .= '<tr>';

    //on va chercher le cours du groupe
    $course = $DB->get_record('course', ['id' => $group->courseid]);

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
    $allmates = $DB->get_records_sql($queryallmates, null);

    $totalmates = count($allmates);
    $el['total'] = $totalmates;

    if ($el['total'] > 1) {
        $nbmembres = $el['total'] . ' membres';
    } else {
        $nbmembres = $el['total'] . ' membre';
    }

    $groupname = extraireNomEquipe($group->groupname);

    //on va chercher le pourcentage de complétion moyen
    $averageprog = getTeamProgress($group->courseid, $group->id);

    $contentsessions .= '<td>
                    <a href="' . new moodle_url('/theme/remui/views/adminteam.php?return=teams&teamid=' . $group->id) . '"><span class="fff-title-team">' . $groupname . '</span></a>
                </td>
                <td>
                    ' . $course->fullname . '
                </td>
                <td>
                    Session du  ' . userdate($group->startdate, '%d/%m/%Y') . ' au ' . userdate($group->enddate, '%d/%m/%Y') . '
                </td>
                <td> 
                    <div>Prog moy : ' . $averageprog[0] . ' %</div>
                    <div>Prog min : ' . $averageprog[2] . ' %</div>
                    <div>Prog max : ' . $averageprog[1] . ' %</div>
                </td>
                <td>
                    <span style="margin-right:10px;">' . $nbmembres . '</span>
                    <svg style="display:none;" onclick="window.location.href=\'' . new moodle_url('/theme/remui/views/adminteam.php?return=teams&teamid=' . $group->id) . '&message=1#sendmessageteam\'" style="cursor:pointer;" width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 4L8.8906 9.2604C9.5624 9.70827 10.4376 9.70827 11.1094 9.2604L19 4M3 15H17C18.1046 15 19 14.1046 19 13V3C19 1.89543 18.1046 1 17 1H3C1.89543 1 1 1.89543 1 3V13C1 14.1046 1.89543 15 3 15Z" stroke="#0B427C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </td>';
    $contentsessions .= '</tr>';

    //on rajoute dans le tableau pour excel
    $rowdata = [];
    array_push($rowdata, $groupname);
    array_push($rowdata, $course->fullname);
    array_push($rowdata, 'Prog moy : ' . $averageprog[0] . ', Prog min : ' . $averageprog[2] . ', Prog max : ' . $averageprog[1] . '');
    array_push($rowdata, $nbmembres);
    array_push($exportdata, $rowdata);
}
$contentsessions .= '</table>';
$contentsessions .= '</div>'; //col-12
$contentsessions .= '</div>'; //row



if ($action == "downloadcsv") {
    exportCSV('Rapport Session', $exportdata);
} else if ($action == "downloadxls") {
    exportXLS('Rapport Session', $exportdata);
}

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/sessions.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Toutes les sessions");

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

//le header avec bouton de retour au panneau admin
$templatecontextheader = (object)[
    'url' => new moodle_url('/theme/remui/views/adminmenu.php'),
    'textcontent' => 'Retour au panneau d\'administration'
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);

$content .= '<div class="row" style="margin:30px 0;"></div>';

//barre de recherche des utilisateurs de l'équipe
$templatecontext = (object)[
    'formurl' => new moodle_url('/theme/remui/views/sessions.php'),
    'textcontent' => $titlerapport,
    'params' => $params,
    'lang_search' => "Rechercher",
    'search' => $search
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_search', $templatecontext);

$content .= '<div style="margin:20px 0;display: flex; align-items: center; justify-content: space-between;">';

$content .= '<form action="" method="get" style="display: flex; align-items: center; margin:0; justify-content: space-between;">';

$content .= '<select name="courseid" onchange="this.form.submit()" style="margin-bottom:0;" class="smartch_select" >';
$content .= '<option>Toutes les formations</option>';
$courses = $DB->get_records_sql('SELECT *
                FROM mdl_course 
                WHERE visible = 1
                AND format != "site"', null);
foreach ($courses as $course) {
    if ($courseid == $course->id) {
        $content .= '<option selected value="' . $course->id . '">' . $course->fullname . '</option>';
    } else {
        $content .= '<option value="' . $course->id . '">' . $course->fullname . '</option>';
    }
}
$content .= '</select>';

$content .= '<select name="status" onchange="this.form.submit()" style="margin-bottom:0;" class="smartch_select">';
if ($status == "all") {
    $content .= '<option selected value="all">Toutes les sessions</option>';
} else {
    $content .= '<option value="all">Toutes les sessions</option>';
}
if ($status == "started") {
    $content .= '<option selected value="started">En cours</option>';
} else {
    $content .= '<option value="started">Démarré</option>';
}
if ($status == "finished") {
    $content .= '<option selected value="finished">Terminé</option>';
} else {
    $content .= '<option value="finished">Terminé</option>';
}
if ($status == "tocome") {
    $content .= '<option selected value="tocome">A venir</option>';
} else {
    $content .= '<option value="tocome">A venir</option>';
}


$content .= '</select>';
if ($search) {
    $content .= '<input value="' . $search . '" type="hidden" name="search"/>';
}
$content .= '</form>';


//les filtres pour la date
// $content .= '<form action="" method="get" style="display: flex; align-items: center; margin:0; justify-content: space-between;">';
// $content .= '<div style="max-width: 500px;display: flex; align-items: center; justify-content: space-between;">';
// $content .= '<label style="margin:0 5px;" class="concorde_label">du </label>';
// if ($search) {
//     $content .= '<input value="' . $search . '" type="hidden" name="search"/>';
// }
// $content .= '<input class="smartch_input" style="margin:0 5px;width:150px;" value="' . $startdate . '" type="date" name="startdate"/>';
// $content .= '<label style="margin:0 5px;" class="concorde_label">au</label>';
// $content .= '<input class="smartch_input" style="margin:0 5px;width:150px;" value="' . $enddate . '" type="date" name="enddate"/>';
// $content .= '<input class="smartch_btn" type="submit" value="Mettre à jour"/>';
// $content .= '</div>';
// $content .= '</form>';

//la box de téléchargement
$content .= '<div style="max-width: 500px;display: flex; align-items: center; justify-content: space-between;">';
$urlcsv = new moodle_url('/theme/remui/views/sessions.php') . '?page=sessions' . $filter . '&action=downloadcsv';
$urlxls = new moodle_url('/theme/remui/views/sessions.php') . '?page=sessions' . $filter . '&action=downloadxls';
// $urlcsv = new moodle_url('/local/pannel/users/coursereport.php?userid='.$userid.'&courseid=' . $courseid) . '&startdate=' . $startdate . '&enddate=' . $enddate . '&action=downloadcsv';
$content .= smartchDropdownDownload('', $urlxls, $urlcsv);
$content .= '</div>';

$content .= '</div>';

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
    $prevurl = new moodle_url('/theme/remui/views/sessions.php?pageno=' . $newpage) . $filter;
}

if ($pageno == $total_pages) {
    $next = false;
} else {
    $next = true;
    $newpage = $pageno + 1;
    $nexturl = new moodle_url('/theme/remui/views/sessions.php?pageno=' . $newpage) . $filter;
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
    'formurl' => new moodle_url('/theme/remui/views/sessions.php')
];
if (count($groups) > 0) {
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);
}

if (count($groups) == 0) {
    $content .= nothingtodisplay("Il n'y a pas de sessions à afficher...");
}

//les sessions
$content .= $contentsessions;

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

</script>';
