<?php

require_once(__DIR__ . '/../../../../config.php');
require_once('../utils.php');

require_login();

global $USER, $DB, $CFG;

$group = null;
$session = null;
$completion = '';

//On va chercher le rôle le plus haut de l'utilisateur
$rolename = getMainRole();

$sent = optional_param('sent', false, PARAM_BOOL);
$courseid = required_param('id', PARAM_INT);
$userid = optional_param('userid', '', PARAM_INT);
$sectionid = optional_param('sectionid', null, PARAM_INT);


if (!$userid) {
    $userid = $USER->id;
}

if (!$courseid) {
    redirect($CFG->wwwroot . '/');
}

$course = $DB->get_record('course', ['id' => $courseid]);
$category = $DB->get_record('course_categories', ['id' => $course->category]);




$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/courses/details.php', ['id'=>$courseid]));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title($course->fullname);

echo $OUTPUT->header();

echo '<style>
.main-inner {
    margin-top: 0px !important;
}
#topofscroll{
    margin-top:0px !important;
}
@media screen and (max-width: 830px) {
    #topofscroll{
        padding-top:50px !important;
    }
}
</style>';



// echo html_writer::start_div('container');
// echo '<script>alert("'.$rolename.'");</script>';
if (!$rolename) {
    $rolename = "student";
}


$content = "";

// on check si l'utilisateur est enrollé
if ($rolename == "student" && !checkIfUserIsEnrolled($courseid, $userid)) {
    //on récupère l'id de la formation gratuite
    // $catfree = $DB->get_record_sql('SELECT * from mdl_course_categories WHERE name = "Formation gratuite"', null);
    // if ($course->category == $catfree->id) {
    //     //on va vers la page d'inscription
    //     redirect(new moodle_url('/theme/remui/views/subscribe.php?courseid=' . $courseid));
    // } else {
        //on redirige vers l'accueil
        redirect(new moodle_url('/'));
    // }
}

//le bouton de retour
$templatecontextheader = (object)[
    'url' => new moodle_url('/my'),
    'textcontent' => 'Retour au tableau de bord'
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);

//le titre
$content .= '<h3 class="FFF-title1" style="margin-top: 80px;">
    <span class="FFABold FFF-White" style="letter-spacing:1px;">'.$course->fullname.'</span>
</h3>';

require_once('./details_info.php');

require_once('./details_modules.php');

echo $content;

echo $OUTPUT->footer();



//on divise les écrans en fonction des rôles
// if ($rolename == "super-admin" || $rolename == "manager") {
//     //pour super admin et admin formateur
//     require_once('./formation_admin.php');
// } else if ($rolename == "teacher" || $rolename == "smalleditingteacher" || $rolename == "editingteacher") {
//     //pour responsable pédagogique et formateur
//     require_once('./formation_formateur.php');
// } else if ($rolename == "student") {
//     require_once('./formation_student.php');
// } else {
//     require_once('./formation_student.php');
// }