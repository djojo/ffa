<?php

require_once(__DIR__ . '/../../../config.php');
require_once('./utils.php');

require_login();

global $USER, $DB, $CFG;

$content = "";
$sqlfilters = "";
$filtercompletion = "";
$filter = "";

// $rolename = optional_param('rolename', 'student', PARAM_TEXT);
$courseid = optional_param('courseid', null, PARAM_INT);
$categoryid = optional_param('categoryid', null, PARAM_INT);
$startdate = optional_param('startdate', null, PARAM_TEXT);
$enddate = optional_param('enddate', null, PARAM_TEXT);
$search = optional_param('search', '', PARAM_TEXT);

$filtersqlcoursecategory = "";
$filtersqlcoursecategoryfilter = "";

if ($courseid) {
    $selectedcourse = $DB->get_record('course', ['id' => $courseid]);
    // $categoryid = null;
}

if ($categoryid) {
    $selectedcategory = $DB->get_record('course_categories', ['id' => $categoryid]);
    // $courseid = null;
    // $filtersqlcoursecategory = ' AND category = ' . $categoryid . ' ';
    $filtersqlcoursecategoryfilter = ' AND category = ' . $categoryid . ' ';

    //on regarde si on a choisi un cours
    // if (!empty($courseid)) {
    //     //on regarde si le cours est dans la category
    //     if($course->category != $categoryid){

    //     }
    // }
}


//si le rôle est différent de manager on redirige vers l'accueil
isAdminFormation();

if (!$enddate) {
    $enddate = date('Y-m-d');
}
if (!$startdate) {
    $dateActuelle = new DateTime();
    $dateActuelle->sub(new DateInterval('P1W'));
    $startdate = $dateActuelle->format('Y-m-d');
}

$filter .= '&enddate=' . $enddate;
$filter .= '&startdate=' . $startdate;

$startdateobject = new DateTime($startdate);
$startdatetimestamp = $startdateobject->getTimestamp();
$enddateobject = new DateTime($enddate);
$enddatetimestamp = $enddateobject->getTimestamp();

// if ($rolename == 'student') {
//     $sqlfilters .= ' WHERE r.shortname = "student" ';
//     $filtercompletion .= ' AND r.shortname = "student" ';
// } else {
$sqlfilters .= ' WHERE (r.shortname = "teacher"
    OR r.shortname = "smalleditingteacher"
    OR r.shortname = "editingteacher") ';
$filtercompletion .= ' AND (r.shortname = "teacher"
    OR r.shortname = "smalleditingteacher"
    OR r.shortname = "editingteacher") ';
// }

if ($startdatetimestamp && $enddatetimestamp) {
    $sqlfilters .= ' AND lo.timestart > ' . $startdatetimestamp . ' AND lo.timestart < ' . $enddatetimestamp . ' ';
    $filtercompletion .= ' AND cc.timemodified > ' . $startdatetimestamp . ' AND cc.timemodified < ' . $enddatetimestamp . ' ';
}

if ($courseid) {
    $course = $DB->get_record('course', ['id' => $courseid]);
    $sqlfilters .= ' AND lo.course = ' . $courseid . ' ';
    $filtercompletion .= ' AND c.id = ' . $courseid . ' ';
}


//on va chercher les logs
// $logs = $DB->get_records_sql('SELECT DISTINCT lo.* 
//             FROM mdl_smartch_activity_log lo
//             JOIN mdl_role_assignments AS ra ON ra.userid = lo.userid
//             JOIN mdl_user_enrolments AS ue ON ra.userid = ue.userid 
//             JOIN mdl_role AS r ON ra.roleid = r.id 
//             ' . $sqlfilters . '
//             ', null);

//on va chercher les logs
// $students = $DB->get_records_sql('SELECT COUNT(DISTINCT lo.userid) AS count
//             FROM mdl_smartch_activity_log lo
//             JOIN mdl_role_assignments AS ra ON ra.userid = lo.userid
//             JOIN mdl_user_enrolments AS ue ON ra.userid = ue.userid 
//             JOIN mdl_role AS r ON ra.roleid = r.id 
//             ' . $sqlfilters . '
//             ', null);

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/stats.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Statistiques");

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

//la bibli pour apex charts
echo '<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>';

//le header avec bouton de retour au panneau admin
$templatecontextheader = (object)[
    'url' => new moodle_url('/theme/remui/views/adminmenu.php'),
    'textcontent' => 'Retour au panneau d\'administration'
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);

$content .= '<div class="row" style="margin:30px 0;"></div>';



















// $content .= html_writer::start_div('container'); //container

// $totaltime = 0;
// foreach ($logs as $log) {
//     $totaltime += $log->timespent;
// }

$content .= '<div class="row">';
$content .= '<div class="col-md-12">';

$content .= '<div style="margin:50px 0;display:flex;align-items:self-start;justify-content:space-between;">';

$content .= '<div>';
$content .= '<h3 class="FFABold FFF-Blue">Statistiques</h3>';
if ($courseid) {
    $content .= '<h5>' . $course->fullname . '</h5>';
} else if ($categoryid) {
    $content .= '<h5>' . $selectedcategory->name . '</h5>';
} else {
    $content .= '<h5 class="FFARegular FFF-Blue">Toutes les formations</h5>';
}
$content .= '</div>';

// ========= SELECT ROLE =================
$content .= '<form action="" method="get" style="display: flex; align-items: center; margin:0; justify-content: space-between;">';
$content .= '<div style="display: flex; align-items: center; justify-content: space-between;">';

// $content .= '<select onchange="this.form.submit()" style="margin-bottom:0;display:none;width:200px;" class="smartch_select" name="rolename">';
// if ($rolename == "student") {
//     $content .= '<option selected value="student">Apprenants</option>';
//     $content .= '<option value="teacher">Formateur</option>';
// } else {
//     $content .= '<option value="student">Apprenants</option>';
//     $content .= '<option selected value="teacher">Formateur</option>';
// }
// $content .= '</select>';


// ========= SELECT CATEGORY ===========
$content .= '<select onchange="document.querySelector(\'#courseid\').val=\'\';this.form.submit()" style="margin-bottom:0;width:200px;" class="smartch_select" name="categoryid">';
$content .= '<option value="">Toutes les categories</option>';
$categories = $DB->get_records_sql('SELECT *
                FROM mdl_course_categories', null);
foreach ($categories as $category) {
    if ($categoryid == $category->id) {
        $content .= '<option selected value="' . $category->id . '">' . $category->name . '</option>';
    } else {
        $content .= '<option value="' . $category->id . '">' . $category->name . '</option>';
    }
}
$content .= '</select>';

// ========= SELECT COURSE =========
$content .= '<select id="courseid" onchange="this.form.submit()" style="margin-bottom:0;width:200px;" class="smartch_select" name="courseid">';
$content .= '<option value="">Toutes les formations</option>';
$courses = $DB->get_records_sql('SELECT *
                FROM mdl_course 
                WHERE format != "site"
                AND visible = 1
                ' . $filtersqlcoursecategoryfilter . '', null);
foreach ($courses as $course) {
    if ($courseid == $course->id) {
        $content .= '<option selected value="' . $course->id . '">' . $course->fullname . '</option>';
    } else {
        $content .= '<option value="' . $course->id . '">' . $course->fullname . '</option>';
    }
}
$content .= '</select>';

$content .= '<label style="margin:0 5px;" class="concorde_label">du </label>';
if ($search) {
    $content .= '<input value="' . $search . '" type="hidden" name="search"/>';
}
$content .= '<input class="smartch_input" style="margin:0 5px;width:150px;" value="' . $startdate . '" type="date" name="startdate"/>';
$content .= '<label style="margin:0 5px;" class="concorde_label">au</label>';
$content .= '<input class="smartch_input" style="margin:0 5px;width:150px;" value="' . $enddate . '" type="date" name="enddate"/>';
$content .= '<input class="smartch_btn" type="submit" value="Mettre à jour"/>';
$content .= '</div>';
$content .= '</form>';
$content .= '</div>';

$content .= '</div>'; // col
$content .= '</div>'; // row




// BLOCK STATS

// $todaydate = date('Y-m-d');
// // Date 7 jours avant
// $datefromtimestamp = strtotime('-7 days', strtotime($todaydate));
// $filterfrom = ' WHERE datecreated > ' . $datefromtimestamp . ' ';



$content .= '<div class="row">';

include('./block_stats_global_plateforme.php');

//on regarde si une formation est sélectionné
if ($courseid || $categoryid) {

    $filterfrom = ' WHERE datecreated > ' . $startdatetimestamp . ' AND datecreated < ' . $enddatetimestamp . ' ';

    if ($courseid) {
        $sqlstats = 'SELECT sc.* 
        FROM mdl_smartch_stats_course sc
        JOIN mdl_course c ON c.id = sc.courseid
        ' . $filterfrom . '
        AND sc.courseid = ' . $courseid . '
        ORDER BY datecreated ASC';
        $title = $selectedcourse->fullname;
    } else {
        $sqlstats = 'SELECT sc.* 
        FROM mdl_smartch_stats_course sc
        JOIN mdl_course c ON c.id = sc.courseid
        ' . $filterfrom . '
        AND c.category = ' . $categoryid . '
        ORDER BY datecreated ASC';
        $title = $selectedcategory->name;
    }

    //on affiche les stats pour une formation ou une categorie
    include('./block_courseuserconnected.php');
    include('./block_courseusertimespent.php');
} else {

    //on affiche les stats générales
    include('./block_userconnected.php');
    include('./block_usertimespent.php');
    include('./block_coursetimespent.php');
    include('./block_coursestats.php');
}




$content .= '</div>';






















// $content .= '<div class="row">';
// $content .= '<div class="col-md-12">';

// $content .= '<div style="display:flex;align-items:center;justify-content:space-between;">';
// $content .= '<h2>Statistiques formations</h2>';
// $content .= '</div>';

// $content .= '<table class="smartch_table">';
// $content .= '<thead>';
// $content .= '<th>Formation</th>';
// $content .= '<th>Nombre de sessions</th>';
// $content .= '<th>Nombre d\'apprenants</th>';
// $content .= '<th>% Prog moyen (sur 50 apprenants)</th>';
// $content .= '</thead>';

// $content .= '<tbody>';
// foreach ($courses as $course) {
//     $content .= '<tr>';
//     $content .= '<td>' . $course->fullname . '</td>';

//     //On cacule le nombre de sessions
//     $totalsession = $DB->get_records_sql('SELECT ss.id 
//     FROM mdl_smartch_session ss
//     JOIN mdl_groups g ON g.id = ss.groupid
//     WHERE g.courseid = ' . $course->id, null);
//     $content .= '<td>' . count($totalsession) . ' sessions</td>';

//     //on calcule le nombre d'etudiants
//     $students = $DB->get_record_sql('SELECT COUNT(DISTINCT userid) AS nombre_eleves
//     FROM mdl_role_assignments
//     WHERE contextid IN (SELECT id FROM mdl_context WHERE instanceid = ' . $course->id . ' AND contextlevel = 50)
//     AND roleid = (SELECT id FROM mdl_role WHERE shortname = "student")', null);
//     $content .= '<td>' . $students->nombre_eleves . '</td>';

//     //on va chercher le pourcentage de complétion moyen
//     $averageprog = getCourseAverageProgression($course->id);
//     $content .= '<td>' . $averageprog . ' %</td>';

//     $content .= '</tr>';
// }
// $content .= '</tbody>';
// $content .= '</table>';

// $content .= '</div>'; // col
// $content .= '</div>'; // row












echo $content;

echo $OUTPUT->footer();
