<?php

// require_once(__DIR__ . '/../../../config.php');

// global $DB;

// require_once($CFG->dirroot . '/theme/remui/views/utils.php');
// require_once($CFG->dirroot . '/theme/remui/classes/form/cohort_create.php');

// isAdmin();
// isPortailRH();

// // $type = optional_param('type', null, PARAM_TEXT);


// $context = context_system::instance();

// $PAGE->set_url(new moodle_url('/theme/remui/views/createcohort.php'));
// $PAGE->set_context(\context_system::instance());
// $PAGE->set_title('Créer un nouveau groupe');

// echo  '<style>
//     .collapsible-actions{
//         display:none !important;
//     }
//     #page.drawers .main-inner {
//         margin-top: 150px;
//         margin-bottom: 3.5rem;
//     }
//     .fff-course-box-info-details{
//         top:-100px;
//         position:absolute;
//     }
//     div[role=main] {
//         margin-top: 0 !important;
//     }
// </style>';

// echo $OUTPUT->header();

// echo '<a href="' . new moodle_url('/theme/remui/views/cohorts.php') . '" style="font-size:0.8rem;cursor: pointer; display: flex; align-items: center; position: absolute; top: 120px;">
// <svg width="8" height="15" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
//     <path fill-rule="evenodd" clip-rule="evenodd" d="M5.70711 0.292893C6.09763 0.683417 6.09763 1.31658 5.70711 1.70711L2.41421 5L5.70711 8.29289C6.09763 8.68342 6.09763 9.31658 5.70711 9.70711C5.31658 10.0976 4.68342 10.0976 4.29289 9.70711L0.292893 5.70711C-0.0976311 5.31658 -0.0976311 4.68342 0.292893 4.29289L4.29289 0.292893C4.68342 -0.0976311 5.31658 -0.0976311 5.70711 0.292893Z" fill="white"/>
// </svg>
// <div class="ml-4 FFF-White FFARegular">Retour</div>
// </a>';

// echo html_writer::start_div('container');


// // $mform = new edit();
// $mform = new create();

// $content = '<div class="row">';
// $content .= '<div class="col-md-12">';
// $content .= '<h1 style="margin-bottom:50px;letter-spacing:1px;" class="smartch_title FFABold FFF-Blue">Créer un nouveau groupe</h1>';
// $content .= '</div>';
// $content .= '</div>';

// echo $content;

// if ($mform->is_cancelled()) {
//     // Go back to index.php page
//     redirect($CFG->wwwroot . '/theme/remui/views/cohorts.php');
// } else if ($fromform = $mform->get_data()) {

//     // var_dump($fromform);

//     $newcohort = new stdClass();
//     $newcohort->name = $fromform->name;
//     $newcohort->contextid = 1;
//     $newcohort->descriptionformat = 1;
//     $newcohort->visible = 1;
//     $newcohort->timecreated = time();
//     $newcohort->timemodified = time();

//     //on créer la cohorte
//     $createdcohortid = $DB->insert_record('cohort', $newcohort);

//     if($fromform->courseid != "none" && $fromform->courseid){
//         syncCohortWithCourse($createdcohortid, $fromform->courseid);
//     }
    
//     // Go to course page
//     redirect($CFG->wwwroot . '/theme/remui/views/cohort.php?cohortid=' . $createdcohortid);
// }


// $mform->display();

// // $url = new moodle_url('/');
// // $url_string = $url->out(true);  // false indique de ne pas inclure les paramètres d'URL s'il y en a


// // var_dump($url_string);

// echo html_writer::end_div();

// echo $OUTPUT->footer();
