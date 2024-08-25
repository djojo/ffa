<?php

// require_once('./utils.php');
// require_once($CFG->dirroot . '/theme/remui/classes/form/messagegroup.php');
// require_once($CFG->libdir . '/messagelib.php');

// // if ($teamid) {
// $to_form = array('variables' => array('teamid' => $group->id, 'teamname' => $group->name, 'return' => $return));
// $mform = new messagegroup(null, $to_form);

// if ($mform->is_cancelled()) {
//     //require_once('./redirections.php');
// } else if ($fromform = $mform->get_data()) {

//     //on va chercher les membres de l'équipe
//     $teamates = $DB->get_records('groups_members', ['groupid' => $fromform->teamid]);

//     foreach ($teamates as $teamate) {
//         $userfor = $DB->get_record('user', ['id' => $teamate->id]);
//         $userbase = $DB->get_record('user', ['id' => $USER->id]);

//         if ($userfor) {

//             // $message = new \core\message\message();
//             // $message->courseid          = 1;
//             // $message->component         = 'moodle';
//             // $message->name              = 'instantmessage';
//             // $message->userfrom          = $userbase;
//             // $message->userto            = $userfor;
//             // $message->subject           = $fromform->subject;
//             // $message->fullmessage       = reset($fromform->content);
//             // $message->fullmessageformat = FORMAT_MARKDOWN;
//             // $message->fullmessagehtml   = reset($fromform->content);
//             // $message->smallmessage      = reset($fromform->content); //rajouter substring
//             // $message->notification      = '0';
//             // $content = array('*' => array('header' => ' test ', 'footer' => ' test '));
//             // $message->set_additional_content('email', $content);

//             // $sink = $this->redirectEmails();
//             // $messageid = message_send($message);

//             $from = 'Portail Formation FFF';

//             $subject = 'Nouveau message de ' . $userbase->firstname . ' ' . $userbase->lastname . ' : ' . $fromform->subject;
//             $body = reset($fromform->content);


//             //on envoi un mail à l'utilisateur
//             email_to_user($userfor, $from, $subject, $body, $body);
//         }
//     }

//     // redirect('/');

//     redirect($CFG->wwwroot . '/theme/remui/views/adminteam.php?teamid=' . $fromform->teamid . '&sent=true');
// }

// $mform->display();
// }
