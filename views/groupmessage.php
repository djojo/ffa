<?php

require_once(__DIR__ . '/../../../config.php');
require_once('./utils.php');
require_once($CFG->dirroot . '/theme/remui/classes/form/messagegroup.php');

require_login();
if(!hasResponsablePedagogiqueRole()){
    redirect('/');
};

global $USER, $DB, $CFG;

$teamid = required_param('teamid', PARAM_INT);
$group = $DB->get_record('groups', ['id' => $teamid]);

$returnurl = required_param('returnurl', PARAM_TEXT);


$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/groupmessage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Nouveau message pour " . $group->name);

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
    'url' => $returnurl,
    'textcontent' => 'Retour au groupe'
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);

$content .= '<div class="row" style="margin:50px 0;"></div>';


$content .= '<div class="row mb-5 mt-4">
<div class="col-md-12">
<h4 style="letter-spacing:1px;max-width:70%;cursor:pointer;" class="FFABold FFF-Blue">Nouveau message pour '.$group->name.'</h4>
</div>
</div>';

echo $content;


require_once('./utils.php');
require_once($CFG->dirroot . '/theme/remui/classes/form/messagegroup.php');
require_once($CFG->libdir . '/messagelib.php');

// if ($teamid) {
$to_form = array('variables' => array('teamid' => $group->id, 'teamname' => $group->name, 'returnurl' => $returnurl));
$mform = new messagegroup(null, $to_form);

if ($mform->is_cancelled()) {
    //require_once('./redirections.php');
} else if ($fromform = $mform->get_data()) {

    //on va chercher les membres de l'équipe
    $teamates = $DB->get_records('groups_members', ['groupid' => $fromform->teamid]);

    foreach ($teamates as $teamate) {
        $userfor = $DB->get_record('user', ['id' => $teamate->id]);
        $userbase = $DB->get_record('user', ['id' => $USER->id]);

        if ($userfor) {

            // $message = new \core\message\message();
            // $message->courseid          = 1;
            // $message->component         = 'moodle';
            // $message->name              = 'instantmessage';
            // $message->userfrom          = $userbase;
            // $message->userto            = $userfor;
            // $message->subject           = $fromform->subject;
            // $message->fullmessage       = reset($fromform->content);
            // $message->fullmessageformat = FORMAT_MARKDOWN;
            // $message->fullmessagehtml   = reset($fromform->content);
            // $message->smallmessage      = reset($fromform->content); //rajouter substring
            // $message->notification      = '0';
            // $content = array('*' => array('header' => ' test ', 'footer' => ' test '));
            // $message->set_additional_content('email', $content);

            // $sink = $this->redirectEmails();
            // $messageid = message_send($message);

            $from = 'Portail Formation FFF';

            $subject = 'Nouveau message de ' . $userbase->firstname . ' ' . $userbase->lastname . ' : ' . $fromform->subject;
            $body = reset($fromform->content);


            //on envoi un mail à l'utilisateur
            email_to_user($userfor, $from, $subject, $body, $body);
        }
    }

    // redirect('/');

    redirect($CFG->wwwroot . '/theme/remui/views/adminteam.php?teamid=' . $fromform->teamid . '&sent=true');
}

$mform->display();


echo $OUTPUT->footer();
