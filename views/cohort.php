<?php


// use tool_brickfield\local\areas\mod_choice\option;

// require_once(__DIR__ . '/../../../config.php');
// require_once('./utils.php');
// // require_once($CFG->dirroot.'/enrol/cohort/lib.php');
// // require_once($CFG->dirroot.'/group/lib.php');

// require_login();
// isPortailRH();
// isAdminFormation();

// global $USER, $DB, $CFG;

// $cohortid = required_param('cohortid', PARAM_INT);
// $cohort = $DB->get_record('cohort', ['id' => $cohortid]);
// $courseid = optional_param('courseid', null, PARAM_INT);
// $startdate = optional_param('startdate', null, PARAM_TEXT);
// $enddate = optional_param('enddate', null, PARAM_TEXT);
// $action = optional_param('action', null, PARAM_TEXT);
// $messagesent = optional_param('messagesent', null, PARAM_INT);

// // var_dump($startdate);
// // die();


// if($courseid && $action == "sync"){
//     if(!$startdate || !$enddate){
//         $messagenotif = "Vous devez rentrer des dates correctes.";
//     } else {
//         $course = $DB->get_record('course', ['id'=>$courseid]);
//         //on sync la cohorte avec le cours
//         syncCohortWithCourse($cohortid, $courseid, $startdate, $enddate);
//         $messagenotif = $course->fullname . " est synchronisé avec le groupe";
//     }
// } else if($courseid && $action == "desync"){
//     $course = $DB->get_record('course', ['id'=>$courseid]);
//     //on desync la cohorte avec le cours
//     desyncCohortWithCourse($cohortid, $courseid);
//     $messagenotif = $course->fullname . " est désynchronisé avec le groupe";
    
// } else if(!empty($messagesent)){
//     $messagenotif = 'Message envoyé aux '.$messagesent.' membres du groupe';
// }

// $content = '';
// $paginationtitle = '';
// $prevurl = '';
// $nexturl = '';

// if($messagenotif){
//     displayNotification($messagenotif);
// }

// $context = context_system::instance();
// $PAGE->set_url(new moodle_url('/theme/remui/views/cohort.php'));
// $PAGE->set_context(\context_system::instance());
// $PAGE->set_title("Formations du groupe");

// echo '<style>

// #page{
//     background:transparent !important;
// }

// #topofscroll {
//     background: transparent !important;
//     margin-top: 0px !important;
// }

// @media screen and (max-width: 830px) {
//     #topofscroll{
//         margin-top:40px !important;
//     }
// }

// </style>';

// echo $OUTPUT->header();

// smartchModal();



// // echo html_writer::start_div('container');

// $search = optional_param('search', '', PARAM_TEXT);
// $pageno = optional_param('pageno', 1, PARAM_TEXT);

// //le tableau des parametres pour la recherche et pagination
// $params = array();
// $filter = '';

// // $param0['paramname'] = "courseid";
// // $param0['paramvalue'] = $courseid;
// // array_push($params, $param0);
// // $filter = '&courseid=' . $courseid;


// $param0['paramname'] = "cohortid";
// $param0['paramvalue'] = $cohortid;
// array_push($params, $param0);
// $filter = '&cohortid=' . $cohortid;

// if ($search != '') {
//     $param1['paramname'] = "search";
//     $param1['paramvalue'] = $search;
//     array_push($params, $param1);
//     $filter = '&search=' . $search;
// }

// $no_of_records_per_page = 5;
// $offset = ($pageno - 1) * $no_of_records_per_page;

// $filtersql = "";
// if (!empty($search)) {
//     $filtersql = ' AND c.fullname LIKE "%' . $search . '%"';
// }

// $querycourses = 'SELECT c.id, c.fullname as name, ss.startdate, ss.enddate
//         FROM mdl_enrol e
//         JOIN mdl_cohort co ON e.customint1 = co.id
//         JOIN mdl_course c ON c.id = e.courseid
//         JOIN mdl_smartch_session ss ON ss.groupid = e.customint2
//         WHERE co.id = ' . $cohortid . '
//         '.$filtersql.'
//         LIMIT ' . $offset . ', ' . $no_of_records_per_page . '
//         ';
// $total_pages_sql = 'SELECT COUNT(*) count 
//         FROM mdl_enrol e
//         JOIN mdl_cohort co ON e.customint1 = co.id
//         JOIN mdl_course c ON c.id = e.courseid
//         WHERE co.id = ' . $cohortid . '
//         '.$filtersql.'';


// $courses = $DB->get_records_sql($querycourses, null);

// // $allcourses = $DB->get_records('course', null);

// $result = $DB->get_records_sql($total_pages_sql, null);
// $total_rows = reset($result)->count;
// $total_pages = ceil($total_rows / $no_of_records_per_page);

// //le header avec bouton de retour au panneau admin
// $templatecontextheader = (object)[
//     'url' => new moodle_url('/theme/remui/views/cohorts.php'),
//     'textcontent' => 'Retour aux groupes'
// ];
// $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);

// $content .= '<div class="row" style="margin:50px 0;"></div>';

// require_once('./cohortmenu.php');


// //barre de recherche des parcours
// $templatecontext = (object)[
//     'formurl' => new moodle_url('/theme/remui/views/cohort.php?cohortid=' . $cohortid),
//     'textcontent' => 'Formations associés au groupe : '.$cohort->name,
//     'lang_search' => "Rechercher",
//     'params' => $params,
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


// if ($pageno == 1) {
//     $previous = false;
// } else {
//     $previous = true;
//     $newpage = $pageno - 1;
//     $prevurl = new moodle_url('/theme/remui/views/cohort.php?pageno=' . $newpage) . $filter;
// }

// if ($pageno == $total_pages) {
//     $next = false;
// } else {
//     $next = true;
//     $newpage = $pageno + 1;
//     $nexturl = new moodle_url('/theme/remui/views/cohort.php?pageno=' . $newpage) . $filter;
// }

// //la pagination en haut
// $templatecontextpagination = (object)[
//     'paginationtitle' => $paginationtitle,
//     'search' => $search,
//     'params' => $params,
//     'total_rows' => $total_rows,
//     'total_pages' => $total_pages,
//     'pageno' => $pageno,
//     'previous' => $previous,
//     'next' => $next,
//     'prevurl' => $prevurl,
//     'nexturl' => $nexturl,
//     'paginationarray' => array_values($paginationarray),
//     'formurl' => new moodle_url('/theme/remui/views/cohort.php')
// ];

// if (count($courses) > 0) {
//     $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);
// } else {
//     $content .= nothingtodisplay("Aucune formation associé...");
// }

// //affichage de la table de tous les utilisateurs
// $content .= '<div class="row">
//         <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
//             <table class="smartch_table">
//                 <thead style="display:none;">
//                     <tr>
//                         <th>Formation</th>
//                         <th>Session</th>
//                         <th>Date</th>
//                         <th>Options</th>
//                     </tr>
//                 </thead>
//                 <tbody>';
// foreach ($courses as $course) {

//     //on va chercher l'enrollement
//     $enrol = $DB->get_record_sql('SELECT * 
//     FROM mdl_enrol
//     WHERE customint1 = ' . $cohort->id . '
//     AND courseid = ' . $course->id, null);

//     $cohortgroupid = $enrol->customint2;

//     $content .= '<tr>
//                     <td style="text-transform:capitalize;">
//                         <span style="margin-left: 10px;"><a href="' . new moodle_url('/theme/remui/views/adminteam.php') . '?teamid=' . $cohortgroupid . '">' . $course->name . '</a></span>
//                     </td>
//                     <td>
//                         <a class="smartch_table_btn" href="' . new moodle_url('/theme/remui/views/adminteam.php') . '?teamid=' . $cohortgroupid . '">
//                             <svg style="width:20px;margin-right:5px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
//                                 <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
//                                 <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
//                             </svg>
//                             Voir la session</a>
//                     </td>
//                     <td>
//                         Du ' . userdate($course->startdate, get_string('strftimedate')) . ' au ' . userdate($course->enddate, get_string('strftimedate')) . '
//                     </td>
//                     <td>
//                         <a class="smartch_table_btn" onclick="deleteFromGroup(\'' . new moodle_url('/theme/remui/views/cohort.php') . '?cohortid='.$cohortid.'&courseid=' . $course->id . '&action=desync\', \'group\')">
//                             <svg style="width:20px;margin-right:5px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
//                                 <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
//                             </svg>
//                             Supprimer l\'association du cours
//                         </a>
//                     </td>
//                     <td style="width: 350px;">
//                         <a class="smartch_table_btn mr-2" href="' . new moodle_url('/theme/remui/views/cohortmessage.php') . '?cohortid='.$cohortid.'&courseid=' . $course->id . '&template=notif">
                            
//                             <svg style="width:20px;margin-right:5px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
//                                 <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
//                             </svg>
//                             Notifier
//                         </a>
//                         <a class="smartch_table_btn" href="' . new moodle_url('/theme/remui/views/cohortmessage.php') . '?cohortid='.$cohortid.'&courseid=' . $course->id . '">
//                             <svg style="width:20px;margin-right:5px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
//                                 <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
//                             </svg>
//                             Envoyer un email
//                         </a>
//                     </td>
//                 </tr>';
// }

// $content .= '</tbody>
//             </table>
//         </div>
//     </div>';

// //la pagination en bas
// if (count($courses) > 0) {
//     $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);
// }

// //on va chercher les formations qui ne sont pas lié au groupe
// $nonlinkedcourses = $DB->get_records_sql('SELECT DISTINCT c.*
// FROM mdl_course c
// JOIN mdl_enrol e ON e.courseid <> c.id
// WHERE c.fullname <> ""
// AND format != "site"', null);


// $content .= '<div class="row">';
// $content .= '<div class="col-md-12">';
// $content .= '<h4 style="letter-spacing:1px;max-width:70%;cursor:pointer;" class="FFABold FFF-Blue mt-5">Ajouter une formation au groupe : '.$cohort->name . '</h4>';
// $content .= '<form class="mt-5" action="" method="post">';
// $content .= '<div>';
// $content .= '<label class="mr-2" for="startdate">Date de début</label>';
// $content .= '<input value="'.date('Y-m-d').'" class="smartch_input mr-5" type="date" name="startdate"/>';
// $content .= '<label class="mr-2" for="startdate">Date de fin</label>';
// $content .= '<input value="'.date('Y-m-d', strtotime('+1 month')).'" class="smartch_input" type="date" name="enddate"/>';
// $content .= '</div>';

// $content .= '<div class="mt-5">';
// $content .= '<label class="mr-2" for="startdate">Choisir une formation</label>';
// $content .= '<input type="hidden" name="cohortid" value="'.$cohortid.'"/>';
// $content .= '<input type="hidden" name="action" value="sync"/>';
// $content .= '<select name="courseid" class="smartch_select">';
// foreach($nonlinkedcourses as $course) {
//     //On vérifie si il n'y a pas déjà un sync
//     $inlist = $DB->get_record_sql('SELECT * 
//     FROM mdl_course c
//     JOIN mdl_enrol e ON e.courseid = c.id 
//     WHERE e.courseid = ' . $course->id . '
//     AND e.customint1 = ' . $cohortid, null);
//     if(!$inlist){
//         $content .= '<option value="'.$course->id.'">'.$course->fullname.'</option>';
//     }
// }
// $content .= '</select>';
// $content .= '</div>';

// $content .= '<div style="text-align:left;">';
// $content .= '<button class="smartch_btn" type="submit">Ajouter</button>';
// $content .= '</div>';

// $content .= '</form>';

// $content .= '</div>'; //md12
// $content .= '</div>'; //row

// // $content .= html_writer::end_div(); //container

// echo $content;

// echo $OUTPUT->footer();

// //pour la pagination
// echo '<script>

// // window.onload = function(){
// //     var els = document.getElementsByClassName("page' . $pageno . '");
// //     Array.from(els).forEach((el) => {
// //         el.setAttribute("selected", "selected");
// //     });
// // };

//     var els = document.getElementsByClassName("page' . $pageno . '");
//     Array.from(els).forEach((el) => {
//         el.setAttribute("selected", "selected");
//     });

// </script>';

// echo '<script>
// function deleteFromGroup(url, name){
//     let text = "Voulez vous vraiment désassocier la formation du groupe ?";
//     let btntext = "Supprimer"
//     document.querySelector("#modal_title").innerHTML = text;
//     document.querySelector("#modal_btn").innerHTML = btntext;
//     document.querySelector("#modal_btn").href = url;
//     document.querySelector(".smartch_modal_container").style.display = "flex";
// }
// </script>';