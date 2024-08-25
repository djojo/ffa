<?php

use tool_brickfield\local\areas\mod_choice\option;

require_once(__DIR__ . '/../../../config.php');
require_once('./utils.php');
require_once($CFG->dirroot . '/theme/remui/classes/form/messageuser.php');

require_login();
if(!hasResponsablePedagogiqueRole()){
    redirect('/');
};

global $USER, $DB, $CFG;

$userid = required_param('userid', PARAM_INT);
$user = $DB->get_record('user', ['id' => $userid]);

$returnurl = required_param('returnurl', PARAM_TEXT);

$to_form = array('variables' => array('userid' => $userid, 'returnurl' => $returnurl));
$mform = new create(null, $to_form);

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($fromform = $mform->get_data()) {

    //on va chercher l'utilisateur
    $user = $DB->get_record('user', ['id' => $fromform->userid]);

    if($user){

        //on va chercher l'utilisateur connecté
        $from = $DB->get_record('user', ['id' => $USER->id]);
        email_to_user($user, $from, $fromform->subject, reset($fromform->content), reset($fromform->content));
        
        redirect($fromform->returnurl.'&messagesent=ok');
    } else {
        $content .= "L'utilisateur n'existe pas...";
    }
    
}



$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/cohortmessage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Nouveau message");

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
    'textcontent' => 'Retour'
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);

$content .= '<div class="row" style="margin:50px 0;"></div>';

$content .= '<div class="row mb-5">
<div class="col-md-12">
<h4 style="letter-spacing:1px;max-width:70%;cursor:pointer;" class="FFABold FFF-Blue">Nouveau message pour '.$user->firstname.' '.$user->lastname.'</h4>
</div>
</div>';

echo $content;

echo '<div class="row">
<div class="col-md-12">';
$mform->display();
echo '</div>
</div>';


echo $OUTPUT->footer();
