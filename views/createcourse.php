<?php

// require_once(__DIR__ . '/../../../config.php');

// global $DB;

// require_once($CFG->dirroot . '/theme/remui/views/utils.php');


// require_once($CFG->dirroot . '/theme/remui/classes/form/course_create.php');

// isAdminFormation();

// // $type = optional_param('type', null, PARAM_TEXT);


// $context = context_system::instance();

// $PAGE->set_url(new moodle_url('/theme/remui/views/createcourse.php'));
// $PAGE->set_context(\context_system::instance());
// $PAGE->set_title('Créer une nouvelle formation');

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

// echo '<a href="' . new moodle_url('/theme/remui/views/adminmenu.php') . '" style="font-size:0.8rem;cursor: pointer; display: flex; align-items: center; position: absolute; top: 120px;">
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
// $content .= '<h1 style="margin-bottom:50px;letter-spacing:1px;" class="smartch_title FFABold FFF-Blue">Créer un nouveau parcours</h1>';
// $content .= '</div>';
// $content .= '</div>';

// echo $content;

// if ($mform->is_cancelled()) {
//     // Go back to index.php page
//     redirect($CFG->wwwroot . '/theme/remui/views/adminmenu.php');
// } else if ($fromform = $mform->get_data()) {

    


//     $newcourse = new stdClass();
//     $newcourse->fullname = $fromform->fullname;
//     $newcourse->shortname = $fromform->shortname;
//     $newcourse->summary = reset($fromform->summary);
//     $newcourse->summaryformat = 1;
//     $newcourse->category = $fromform->categoryid;
//     $newcourse->visible = $fromform->visible;
//     $newcourse->format = "topics"; //topics
//     $newcourse->numsections = $fromform->nbsection; //on créer des sections
//     $newcourse->newsitems = 0;
//     $newcourse->groupmode = 0;
//     $newcourse->groupmodeforce = 0;
//     $newcourse->showreports = 0;
//     $newcourse->showgrades = 0;
//     $newcourse->enablecompletion = 1;
//     $newcourse->showactivitydates = 0;
//     $newcourse->showcompletionconditions = 1;
//     // $newcourse->downloadcontent = 0;

//     //On créer le cours via l'API de moodle
//     $course = create_course($newcourse);

//     //on récupère l'image
//     // $file = $mform->get_new_filename('image');
//     // if ($file) {
//     //     $fullpath = "moooooodle/" . GUIDv4() . $file;
//     //     $success = $mform->save_file('image', $CFG->dataroot . '/' . $fullpath, true);
//     //     if (!$success) {
//     //         echo "Erreur lors de l'enregistrement de l'image...";
//     //     }
//     //     $newdocument->image = $fullpath;
//     // }


    
//     // COURSE DURATION
//     //on va chercher le field personalisé du cours
//     $fieldcourseduration = $DB->get_record_sql('
//     SELECT * 
//     FROM mdl_customfield_field cf
//     WHERE cf.shortname = "courseduration"', null);
//     if ($fieldcourseduration) {
//         //on ajoute le champs personalisé du cours
//         $customfieldcourseduration = new stdClass();
//         $customfieldcourseduration->shortname = "courseduration";
//         $customfieldcourseduration->timecreated = time();
//         $customfieldcourseduration->timemodified = time();
//         $customfieldcourseduration->charvalue = $fromform->courseduration;
//         $customfieldcourseduration->value = $fromform->courseduration;
//         $customfieldcourseduration->instanceid = $course->id;
//         $customfieldcourseduration->fieldid = $fieldcourseduration->id;
//         $customfieldcourseduration->valueformat = 0;

//         $DB->insert_record('customfield_data', $customfieldcourseduration);
//     }

//     // COURSE TYPE
//     //on va chercher le field personalisé du cours
//     // $fieldcoursetype = $DB->get_record_sql('
//     // SELECT * 
//     // FROM mdl_customfield_field cf
//     // WHERE cf.shortname = "coursetype"', null);
//     // if ($fieldcoursetype) {
//     //     //on ajoute le champs personalisé du cours
//     //     $customfieldcoursetype = new stdClass();
//     //     $customfieldcoursetype->shortname = "coursetype";
//     //     $customfieldcoursetype->timecreated = time();
//     //     $customfieldcoursetype->timemodified = time();
//     //     $customfieldcoursetype->charvalue = $fromform->coursetype;
//     //     $customfieldcoursetype->value = $fromform->coursetype;
//     //     $customfieldcoursetype->instanceid = $course->id;
//     //     $customfieldcoursetype->fieldid = $fieldcoursetype->id;
//     //     $customfieldcoursetype->valueformat = 0;

//     //     $DB->insert_record('customfield_data', $customfieldcoursetype);
//     // }

//     // COURSE DIPLOME
//     //on va chercher le field personalisé du cours
//     // $fieldcoursediplome = $DB->get_record_sql('
//     // SELECT * 
//     // FROM mdl_customfield_field cf
//     // WHERE cf.shortname = "coursediplome"', null);
//     // if ($fieldcoursediplome) {
//     //     //on ajoute le champs personalisé du cours
//     //     $customfieldcoursediplome = new stdClass();
//     //     $customfieldcoursediplome->shortname = "coursediplome";
//     //     $customfieldcoursediplome->timecreated = time();
//     //     $customfieldcoursediplome->timemodified = time();
//     //     $customfieldcoursediplome->charvalue = $fromform->coursediplome;
//     //     $customfieldcoursediplome->value = $fromform->coursediplome;
//     //     $customfieldcoursediplome->instanceid = $course->id;
//     //     $customfieldcoursediplome->fieldid = $fieldcoursediplome->id;
//     //     $customfieldcoursediplome->valueformat = 0;

//     //     $DB->insert_record('customfield_data', $customfieldcoursediplome);
//     // }

//     // METHODE D'INSCRIPTION
//     //on va chercher le field personalisé du cours
//     // $field = $DB->get_record_sql('
//     // SELECT * 
//     // FROM mdl_customfield_field cf
//     // WHERE cf.shortname = "subscribemethod"', null);

//     // if ($field) {
//     //     //on ajoute le champs personalisé du cours
//     //     $customfieldsubscribe = new stdClass();
//     //     $customfieldsubscribe->shortname = "subscribemethod";
//     //     $customfieldsubscribe->timecreated = time();
//     //     $customfieldsubscribe->timemodified = time();
//     //     $customfieldsubscribe->charvalue = $fromform->subscribemethod;
//     //     $customfieldsubscribe->value = $fromform->subscribemethod;
//     //     $customfieldsubscribe->instanceid = $course->id;
//     //     $customfieldsubscribe->fieldid = $field->id;
//     //     $customfieldsubscribe->valueformat = 0;

//     //     $DB->insert_record('customfield_data', $customfieldsubscribe);
//     // }

//     //on va chercher les départements
//     $cohorts = $DB->get_records_sql('SELECT c.*
//     FROM mdl_cohort c', null);
//     foreach($cohorts as $cohort){
//         //si on a choisi le département
//         if($cohort->id = $_POST['cohort'.$cohort->id]){
//             //On associe le departement
//             syncCohortWithCourse($cohort->id, $course->id);
//         }
//     }

//     //on inscrit le créateur du cours au cours
//     enrol_try_internal_enrol($course->id, $USER->id, 3, time());

//     // Go to course page
//     redirect($CFG->wwwroot . '/course/view.php?id=' . $course->id);
// }


// $mform->display();

// echo html_writer::end_div();

// echo $OUTPUT->footer();


// //courses to create
// // list of ( 
// //     object {
// //     fullname string   //full name
// //     shortname string   //course short name
// //     categoryid int   //category id
// //     idnumber string  Optional //id number
// //     summary string  Optional //summary
// //     summaryformat int  Default to "1" //summary format (1 = HTML, 0 = MOODLE, 2 = PLAIN or 4 = MARKDOWN)
// //     format string  Default to "topics" //course format: weeks, topics, social, site,..
// //     showgrades int  Default to "1" //1 if grades are shown, otherwise 0
// //     newsitems int  Default to "5" //number of recent items appearing on the course page
// //     startdate int  Optional //timestamp when the course start
// //     enddate int  Optional //timestamp when the course end
// //     numsections int  Optional //(deprecated, use courseformatoptions) number of weeks/topics
// //     maxbytes int  Default to "0" //largest size of file that can be uploaded into the course
// //     showreports int  Default to "0" //are activity report shown (yes = 1, no =0)
// //     visible int  Optional //1: available to student, 0:not available
// //     hiddensections int  Optional //(deprecated, use courseformatoptions) How the hidden sections in the course are displayed to students
// //     groupmode int  Default to "0" //no group, separate, visible
// //     groupmodeforce int  Default to "0" //1: yes, 0: no
// //     defaultgroupingid int  Default to "0" //default grouping id
// //     enablecompletion int  Optional //Enabled, control via completion and activity settings. Disabled,
// //                                             not shown in activity settings.
// //     completionnotify int  Optional //1: yes 0: no
// //     lang string  Optional //forced course language
// //     forcetheme string  Optional //name of the force theme
// //     courseformatoptions  Optional //additional options for particular course format
// //     list of ( 
// //     object {
// //     name string   //course format option name
// //     value string   //course format option value
// //     } 
// //     )} 
// //     )