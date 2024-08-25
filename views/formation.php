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

//on recupère les champs personnalisés
$diplomeresult = $DB->get_records_sql('
SELECT cd.value 
FROM mdl_customfield_data cd
JOIN mdl_customfield_field cf ON cf.id = cd.fieldid
WHERE cd.instanceid = ' . $courseid . ' AND cf.shortname = "diplome"', null);
$diplomeobject = reset($diplomeresult);
if ($diplomeobject) {
    $diplome = $diplomeobject->value;
}


$coursetyperesult = $DB->get_records_sql('
SELECT cd.value 
FROM mdl_customfield_data cd
JOIN mdl_customfield_field cf ON cf.id = cd.fieldid
WHERE cd.instanceid = ' . $courseid . ' AND cf.shortname = "coursetype"', null);
$coursetypeobject = reset($coursetyperesult);
if ($coursetypeobject) {
    $coursetype = $coursetypeobject->value;
}


$coursedurationresult = $DB->get_records_sql('
SELECT cd.value 
FROM mdl_customfield_data cd
JOIN mdl_customfield_field cf ON cf.id = cd.fieldid
WHERE cd.instanceid = ' . $courseid . ' AND cf.shortname = "courseduration"', null);
$coursedurationobject = reset($coursedurationresult);
if ($coursedurationobject) {
    $courseduration = $coursedurationobject->value;
}

// echo '<h1>' . $diplome . $courseduration . $coursetype . '</h1>';

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/formation.php'));
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

$content = "";

// require_once($CFG->dirroot . '/theme/remui/layout/common.php');

// echo html_writer::start_div('container');

if (!$rolename) {
    $rolename = "student";
}

//on va chercher les champs personnalisés
// require_once($CFG->libdir.'/datalib.php'); // Inclure les fonctions de manipulation de données

// $course_id = 123; // Remplacez par l'ID du cours

// if ($course = get_course($course_id)) {
//     $fields = get_custom_fields('user_info_field', $course_id);
//     // Maintenant, $fields contient les champs de profil personnalisés du cours
//     foreach ($fields as $field) {
//         echo "Champ : " . $field->shortname . "<br>";
//         echo "Description : " . $field->description . "<br>";
//         // ... Autres propriétés du champ
//     }
// } else {
//     echo "Le cours n'a pas été trouvé.";
// }

//on divise les écrans en fonction des rôles
if ($rolename == "super-admin" || $rolename == "manager") {
    //pour super admin et admin formateur
    require_once('./formation_admin.php');
} else if ($rolename == "teacher" || $rolename == "smalleditingteacher" || $rolename == "editingteacher") {
    //pour responsable pédagogique et formateur
    require_once('./formation_formateur.php');
} else if ($rolename == "student") {
    require_once('./formation_student.php');
} else {
    require_once('./formation_student.php');
}

// if ($sectionid) {
//     echo '<script>
//         document.querySelector("#module-block-' . $sectionid . '").click()
//     </script>';
// }


// $content .= $OUTPUT->render_from_template('theme_remui/smartch_my_courses', null);


if ($sent) {
    displayMessageSent();
}

echo $content;


// echo html_writer::end_div(); //container

//si il y a une session:
//on vérifie qu'il ait une equipe
if($rolename == "student") {
    $allsessions = $DB->get_records_sql('SELECT DISTINCT g.id, ss.startdate, ss.enddate
                    FROM mdl_groups g
                    JOIN mdl_groups_members gm ON gm.groupid = g.id
                    JOIN mdl_smartch_session ss ON ss.groupid = g.id
                    WHERE gm.userid = ' . $USER->id . ' AND g.courseid = ' . $course->id, null);
    // $firstsession = $DB->get_record_sql('SELECT ss.*
    // FROM mdl_smartch_session ss
    // JOIN mdl_groups g ON g.id = ss.groupid
    // JOIN mdl_groups_members gm ON g.id = gm.groupid 
    // WHERE gm.userid = '.$USER->id.'
    // ORDER BY startdate ASC');
    // if(count($allsessions) > 0){
    //     $teamid = reset($allsessions)->id;
    //     require_once('./team_dropbox.php');
    // }
    foreach($allsessions as $session){
        $teamid = $session->id;
        // echo $session->id . '/';
        //les dépots (caché pour l'instant)
        // require_once('./team_dropbox.php');
    }
}


echo $OUTPUT->footer();
