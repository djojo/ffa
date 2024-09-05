<?php

// // var_dump($teamid);

// require_once($CFG->dirroot . '/theme/remui/classes/form/dropbox_add_file.php');

// echo '<h3 id="dropbox" class="FFF-title1" style="display:none;margin:100px 0; display: flex; align-items: center;">

// <span class="FFABlack FFF-Blue" style="letter-spacing:1px;margin-right:10px;">Boîte de </span><span class="FFABlack FFF-Gold" style="letter-spacing:1px;margin-right:20px;"> dépot</span> 

// </h3>';

// echo '<style>
//     .col-lg-3.col-md-4.col-form-label.p-0 {
//         display: none;
//     }
//     #fitem_id_attachments{
//         display: flex;
//         align-items: center;
//         justify-content: center;
//     }
// </style>';

// if (!hasResponsablePedagogiqueRole()){
//     echo '<style>
//         .moodle-dialogue-base .moodle-dialogue-wrap .moodle-dialogue-hd.yui3-widget-hd > h3 {
//             display:none;
//         }
//         .fp-restrictions.p-mt-4.text-paragraph {
//             display:none;
//         }
//         .form-filetypes-descriptions.w-100 {
//             display:none;
//         }
//         .fp-restrictions {
//             display:none;
//         }
//         .fp-toolbar {
//             display:none;
//         }
//         .filemanager~p {
//             display:none;
//         }
//         .fp-toolbar.d-flex.align-items-center.flex-gap-4 {
//             display:none !important;
//         }
//         .fp-file-delete {
//             display:none !important;
//         }
//         .fp-saveas, .fp-author, .fp-license, .fp-path, .fp-select-buttons {
//             display:none !important;
//         }
//     </style>';
// }

// $to_form = array('variables' => array('teamid' => $teamid));

// $mform = new edit(null, $to_form);

// if ($mform->is_cancelled()) {
//     redirect($CFG->wwwroot . '/theme/remui/views/adminteam.php?teamid=' . $teamid. '&return=teams#dropbox');
// } else if ($fromform = $mform->get_data()) {

//     // $plateforme = getPlateforme();

//     // Now save the files in correct part of the File API.
//     file_save_draft_area_files(
//         // The $fromform->attachments property contains the itemid of the draft file area.
//         $fromform->attachments,

//         // The combination of contextid / component / filearea / itemid
//         // form the virtual bucket that file are stored in.
//         $context->id,
//         'theme_remui',
//         'attachment',
//         $teamid,

//         [
//             'subdirs' => 0,
//             'maxbytes' => $maxbytes,
//             'maxfiles' => 50,
//         ]
//     );

//     redirect($CFG->wwwroot . '/theme/remui/views/adminteam.php?teamid=' . $teamid. '&return=teams#dropbox');
// }



// //on affiche les fichiers

// // Get an unused draft itemid which will be used for this form.
// $draftitemid = file_get_submitted_draft_itemid('attachments');

// file_prepare_draft_area(
//     // The $draftitemid is the target location.
//     $draftitemid,

//     // The combination of contextid / component / filearea / itemid
//     // form the virtual bucket that files are currently stored in
//     // and will be copied from.
//     $context->id,
//     'theme_remui',
//     'attachment',
//     $teamid,
//     [
//         'subdirs' => 0,
//         'maxbytes' => $maxbytes,
//         'maxfiles' => 50,
//     ]
// );

// // Set the itemid of draft area that the files have been moved to.
// $dropbox->attachments = $draftitemid;
// $mform->set_data($dropbox);
// $mform->display();

