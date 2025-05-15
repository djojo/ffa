<?php
// // This file is part of Moodle Course Rollover Plugin
// //
// // Moodle is free software: you can redistribute it and/or modify
// // it under the terms of the GNU General Public License as published by
// // the Free Software Foundation, either version 3 of the License, or
// // (at your option) any later version.
// //
// // Moodle is distributed in the hope that it will be useful,
// // but WITHOUT ANY WARRANTY; without even the implied warranty of
// // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// // GNU General Public License for more details.
// //
// // You should have received a copy of the GNU General Public License
// // along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// /**
//  * @package     smartch
//  * @author      Geoffroy Rouaix
//  * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
//  */

// require_once(__DIR__ . '/../../../config.php');
// require_once('./utils.php');

// // defined('MOODLE_INTERNAL') || die();

// require_login();

// global $USER, $DB, $CFG;

// $params = null;
// $content = '';
// $paginationtitle = '';
// $prevurl = '';
// $nexturl = '';

// // if (!$userid) {
// //     redirect($CFG->wwwroot);
// // }

// //On va chercher l'id de l'utilisateur
// $userid = $USER->id;

// //On va chercher le rôle le plus haut de l'utilisateur
// $rolename = getMainRole();

// $role = "";
// //on affiche le rolename correctement
// if ($rolename == "student") {
//     $role = "Apprenant";
// } else if ($rolename == "manager") {
//     $role = "Administrateur Formation";
// } else if ($rolename == "smalleditingteacher") {
//     $role = "Responsable pédagogique";
// } else if ($rolename == "teacher" || $rolename == "noneditingteacher") {
//     $role = "Formateur";
// } else if ($rolename == "super-admin") {
//     $role = "Super Admin";
// }

// // $courseid = required_param('id', PARAM_INT);

// // $course = $DB->get_record('course', ['id' => $courseid]);

// $context = context_system::instance();
// $PAGE->set_url(new moodle_url('/theme/remui/views/adminuser.php'));
// $PAGE->set_context(\context_system::instance());
// $PAGE->set_title("Mon profil");

// echo $OUTPUT->header();

// // require_once($CFG->dirroot . '/theme/remui/layout/common.php');

// // echo html_writer::start_div('container');

// // $userid = optional_param('userid', '', PARAM_INT);
// $search = optional_param('search', null, PARAM_TEXT);
// $pageno = optional_param('pageno', 1, PARAM_TEXT);



// //on regarde si l'utilisateur est bien

// $no_of_records_per_page = 10;
// $offset = ($pageno - 1) * $no_of_records_per_page;

// if ($search) {
//     $querycourses = 'SELECT c.id, c.fullname FROM mdl_course c
//             JOIN mdl_role_assignments ra ON ra.userid = ' . $userid . '
//             JOIN mdl_context ct ON ct.id = ra.contextid AND c.id = ct.instanceid
//             JOIN mdl_role r ON r.id = ra.roleid
//             WHERE c.format != "site" AND c.visible = 1
//             AND lower(c.fullname) LIKE ?
//             LIMIT ' . $offset . ', ' . $no_of_records_per_page;
//     $total_pages_sql = 'SELECT c.id FROM mdl_course c
//         JOIN mdl_role_assignments ra ON ra.userid = ' . $userid . '
//         JOIN mdl_context ct ON ct.id = ra.contextid AND c.id = ct.instanceid
//         JOIN mdl_role r ON r.id = ra.roleid
//         WHERE c.format != "site" AND c.visible = 1
//         AND lower(c.fullname) LIKE ?';
// } else {
//     $querycourses = 'SELECT c.id, c.fullname FROM mdl_course c
//             JOIN mdl_role_assignments ra ON ra.userid = ' . $userid . '
//             JOIN mdl_context ct ON ct.id = ra.contextid AND c.id = ct.instanceid
//             JOIN mdl_role r ON r.id = ra.roleid
//             WHERE format != "site" AND c.visible = 1
//             LIMIT ' . $offset . ', ' . $no_of_records_per_page . '
//             ';
//     $total_pages_sql = 'SELECT c.id FROM mdl_course c
//     JOIN mdl_role_assignments ra ON ra.userid = ' . $userid . '
//     JOIN mdl_context ct ON ct.id = ra.contextid AND c.id = ct.instanceid
//     JOIN mdl_role r ON r.id = ra.roleid
//     WHERE c.format != "site" AND c.visible = 1';
// }
// $courses = $DB->get_records_sql($querycourses, ['%' . strtolower($search) . '%']);

// // var_dump($courses);
// $allcourses = $DB->get_records('course', null);

// $result = $DB->get_records_sql($total_pages_sql, ['%' . strtolower($search) . '%']);
// $total_rows = count($result);
// $total_pages = ceil($total_rows / $no_of_records_per_page);

// //le header avec bouton de retour au panneau admin
// // $templatecontextheader = (object)[
// //     'url' => new moodle_url('/theme/remui/views/adminusers.php'),
// //     'textcontent' => 'Retour aux utilisateurs'
// // ];
// // $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);


// //le profil utilisateur
// $user = $DB->get_record('user', ['id' => $userid]);


// //le css pour descendre l'image du header
// $content .= '<style>

// img.smartch_background_header {
//     height: 350px !important;
// }

// #page{
//     background:transparent !important;
// }

// #topofscroll {
//     background: transparent !important;
//     margin-top: 20px !important;
// }

// </style>
// ';


// // Le header
// $content .= '<h3 class="FFF-title1" style="margin-top:50px;">
//         <span class="FFABold FFF-White" style="letter-spacing:1px;">Mon profil</span>
//         </h3>';

// if ($user->lastaccess) {
//     $lastconnect = 'Dernier accès le ' . userdate($user->lastaccess, get_string('strftimedate'));
// } else {
//     $lastconnect = 'Jamais connecté';
// }

// $content .= '
// <div class="row">
//     <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8" style="background: white; padding: 20px 40px; border-radius: 20px 20px 0 0; min-height: 200px;">
//         <div class="fff-course-box-info-details">
//             <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
//                 <circle cx="24" cy="24" r="24" fill="#E2E8F0"/>
//                 <path d="M28 19C28 21.2091 26.2091 23 24 23C21.7909 23 20 21.2091 20 19C20 16.7909 21.7909 15 24 15C26.2091 15 28 16.7909 28 19Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
//                 <path d="M24 26C20.134 26 17 29.134 17 33H31C31 29.134 27.866 26 24 26Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
//             </svg>

//             <div class="ml-4">

//                 <h3 class="FFABold FFF-Blue" style="font-size:16px;">
//                     ' . $user->firstname . ' ' . $user->lastname . ' 
                    
//                 </h3>
//                 <h5 class="FFF-Blue" style="font-size:12px;">' . $role . '</h5>
//             </div>
//         </div>
//         <div style="cursor:pointer;" onclick="window.location.href=\'' . new moodle_url('/theme/remui/views/adminusermessage.php?userid=' . $user->id) . '\'" class="fff-course-box-info-details">
//             <svg class="mr-2" width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
//                 <path d="M1 4L8.8906 9.2604C9.5624 9.70827 10.4376 9.70827 11.1094 9.2604L19 4M3 15H17C18.1046 15 19 14.1046 19 13V3C19 1.89543 18.1046 1 17 1H3C1.89543 1 1 1.89543 1 3V13C1 14.1046 1.89543 15 3 15Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
//             </svg>
//             <span>' . $user->email . '</span>
//         </div>
        
//         <div class="fff-course-box-info-details">
//             <svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
//                 <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM0 10C0 4.47715 4.47715 0 10 0C15.5228 0 20 4.47715 20 10C20 15.5228 15.5228 20 10 20C4.47715 20 0 15.5228 0 10ZM10 5C10.5523 5 11 5.44772 11 6V9.58579L13.7071 12.2929C14.0976 12.6834 14.0976 13.3166 13.7071 13.7071C13.3166 14.0976 12.6834 14.0976 12.2929 13.7071L9.29289 10.7071C9.10536 10.5196 9 10.2652 9 10V6C9 5.44772 9.44771 5 10 5Z" fill="#00315a"/>
//             </svg>
//             <span >' . $lastconnect . '</span>
//         </div>
//     </div>

// </div>'; //row




// //tous les parcours
// $allcourses = array();

// foreach ($courses as $course) {
//     $el = new stdClass();
//     $el->fullname = $course->fullname;
//     $el->id = $course->id;
//     // $el->timecreated = $course->timecreated;
//     $el->url = $CFG->wwwroot . "/course/view.php?id=" . $course->id;

//     //On va chercher l'image du cours
//     $course2 = new core_course_list_element($course);
//     foreach ($course2->get_course_overviewfiles() as $file) {
//         if ($file->is_valid_image()) {
//             $imagepath = '/' . $file->get_contextid() .
//                 '/' . $file->get_component() .
//                 '/' . $file->get_filearea() .
//                 $file->get_filepath() .
//                 $file->get_filename();
//             $imageurl = file_encode_url(
//                 $CFG->wwwroot . '/pluginfile.php',
//                 $imagepath,
//                 false
//             );
//             $el->img = $imageurl;
//             // Use the first image found.
//             break;
//         }
//     }
//     array_push($allcourses, $el);
// }


// //le tableau des parametres pour la recherche
// // $params = array();
// // $param1['paramname1'] = "userid";
// // $param1['paramvalue1'] = $userid;
// // array_push($params, $param1);

// //barre de recherche des parcours
// $templatecontext = (object)[
//     'formurl' => new moodle_url('/theme/remui/views/users/details.php'),
//     'textcontent' => "Formations suivies",
//     'lang_search' => "Rechercher",
//     // 'params' => $params,
//     'search' => $search
// ];
// $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_search', $templatecontext);


// //La pagination
// if (count($courses) == 0) {
//     $paginationtitle .= 'Aucun résultat';
// } else if (count($courses) == 1) {
//     $paginationtitle .= '1 résultat';
// } else {
//     $paginationtitle .= $total_rows . ' résultats - page ' . $pageno . ' sur ' . $total_pages . '';
// }
// $paginationarray = range(1, $total_pages); // array(1, 2, 3)

// if (count($allcourses) != 0) {
//     //la pagination en haut
//     $templatecontextpagination = (object)[
//         'paginationtitle' => $paginationtitle,
//         'search' => $search,
//         'total_rows' => $total_rows,
//         'total_pages' => $total_pages,
//         'pageno' => $pageno,
//         'paginationarray' => array_values($paginationarray),
//         'formurl' => new moodle_url('/theme/remui/views/profile.php')
//     ];
//     $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);
// }


// if (count($allcourses) == 0) {
//     $content .= nothingtodisplay("Aucune formation");
// }

// //tous les parcours de l'utilisateur
// $content .= '<div class="row">
//         <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">';
// $count = 0;
// foreach ($allcourses as $onecourse) {

//     //on va chercher la session du cours
//     $groups = $DB->get_records_sql('SELECT g.id, g.name FROM mdl_groups g
//     JOIN mdl_groups_members gm ON gm.groupid = g.id
//     WHERE gm.userid = ' . $user->id . ' AND g.courseid = ' . $onecourse->id, null);

//     //si l'utilisateur à un groupe
//     if (count($groups) > 0) {
//         $group = reset($groups);
//         $groupid = $group->id;

//         //on va chercher les informations de session 
//         $session = $DB->get_record('smartch_session', ['groupid' => $group->id]);
//         $modulesstatus = getModulesStatus($onecourse->id, $session->id, $user->id);
//         $total = $modulesstatus[1] + $modulesstatus[0];
//         $ratio = $modulesstatus[0] . '/' . $total;
//         // var_dump($modulesstatus);
//     } else {
//         // l'ancien ration sans les sessions
//         $ratio = getCourseCompletionRatio($user->id, $onecourse->id);
//     }

//     // $content .= 'course:' . $onecourse->id;
//     // $content .= 'user:' . $user->id;

//     $courseprog = getCompletionPourcent($onecourse->id, $user->id);
//     // $courseprog = getCourseProgression($user->id, $onecourse->id);



//     $img = $onecourse->img;
//     if (!$img) {
//         $img = new moodle_url('/theme/remui/pix/screenshot.png');
//     }
//     if ($count == 0) {
//         $content .= '<div class="fff-admin-user-course-box row" style="border-radius: 15px 0 0 0;">
//             <div class="col-sm-12 col-md-4 col-lg-4 fff-admin-user-course-thumbnail">
//             <img style="border-radius: 15px 0 0 0;" src="' . $img . '" />
//         ';
//     } else {
//         $content .= '<div class="fff-admin-user-course-box row">
//             <div class="col-sm-12 col-md-4 col-lg-4 fff-admin-user-course-thumbnail">
//             <img src="' . $img . '" />
//         ';
//     }
//     $count++;

//     //on va chercher les groupes session de l'utilisateur
//     $groups = $DB->get_records_sql('SELECT g.id, g.name 
//     FROM mdl_groups g
//     JOIN mdl_groups_members gm ON gm.groupid = g.id
//     WHERE gm.userid = ' . $user->id . ' AND g.courseid = ' . $onecourse->id, null);

//     //si l'utilisateur est dans un groupe
//     if (count($groups) > 0) {
//         //on prend le premier
//         $group = reset($groups);

//         //On va chercher le responsable pédagogique
//         $coach = getResponsablePedagogique($group->id, $onecourse->id);

//         //on va chercher la session 
//         $session = $DB->get_record('smartch_session', ['groupid' => $group->id]);

//         if ($session->startdate && $session->enddate) {
//             $sessiondate = 'Du ' . userdate($session->startdate, '%d/%m/%Y') . ' au ' . userdate($session->enddate, '%d/%m/%Y');
//         }
//     } else {
//         $sessiondate = "Pas de session";
//         $coach = array("", null);
//         // $coach = array("Aucun responsable pédagogique", null);
//     }


//     $content .= '
                    
//                         <h5>' . $onecourse->fullname . '</h5>
//                     </div>
//                     <div class="col-sm-12 col-md-4 col-lg-4 fff-admin-user-course-box-col">
//                         <div>
                            
//                             <span>' . $sessiondate . '</span>
//                         </div>
//                         <div>
                            
//                             <span>' . $coach[0] . '</span>
//                         </div>
                        
//                         <div>
//                             <span >' . $ratio . ' activités terminées</span>
//                         </div>

//                         <div class="smartch_progress_bar_box">
//                             <div class="smartch_progress_bar">
//                                 <div class="smartch_progress_bar_number">' . $courseprog . '%</div>
//                                 <div class="smartch_progress_bar_gain" style="width:' . $courseprog . '% !important;"></div>
//                             </div>
//                         </div>
                        
                   
//                     </div>
//                     <div class="col-sm-12 col-md-4 col-lg-4 fff-admin-user-course-box-col" style="align-items: center; display: flex; justify-content: center;">
//                         <div>
//                             <a class="smartch_table_btn" href="' . new moodle_url('/theme/remui/views/formation.php') . '?id=' . $onecourse->id . '">Consulter</a>
//                         </div>
//                     </div>
                
//                 </div>';
// }

// $content .= '
//         </div>
//     </div>';

// //template avec tous les parcours
// // $templatecontext = (object)[
// //     'url' => new moodle_url('/'),
// //     'courses' => $allcourses
// // ];
// // $content .= $OUTPUT->render_from_template('theme_remui/smartch_admin_all_courses', $templatecontext);

// if (count($allcourses) != 0) {
//     //la pagination en bas
//     $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);
// }

// // $content .= html_writer::end_div(); //container

// echo $content;

// echo $OUTPUT->footer();

// if (count($allcourses) != 0) {
//     //pour la pagination
//     echo '<script>

// window.onload = function(){
//     var els = document.getElementsByClassName("page' . $pageno . '");
//     Array.from(els).forEach((el) => {
//         el.setAttribute("selected", "selected");
//     });
// };

// </script>';
// }
