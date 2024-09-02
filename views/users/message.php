<?php

use tool_brickfield\local\areas\mod_choice\option;

require_once(__DIR__ . '/../../../../config.php');
require_once('../utils.php');
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

        //on format à partir du template
        $contentmail = smartchFormatEmail(reset($fromform->content), $user);

        //pour visualiser le contenu du mail
        echo $contentmail;
        die();

        email_to_user($user, $from, $fromform->subject, $contentmail, $contentmail);
        
        redirect($fromform->returnurl.'&messagesent=ok');
    } else {
        $content .= "L'utilisateur n'existe pas...";
    }
    
}



$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/users/message.php'));
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

//le titre
$content .= '<h3 class="FFF-title1" style="margin-top: 80px;">
    <span class="FFABold FFF-White" style="letter-spacing:1px;">Nouveau message</span>
</h3>';

$content .= '<div style="background:white;padding:20px;">';
$content .= '<div class="row mb-5">
<div class="col-md-12">
<h2 style="letter-spacing:1px;max-width:70%;cursor:pointer;"  class="FFARegular FFF-Blue">Pour '.$user->firstname.' '.$user->lastname.'</h2>
</div>
</div>';

echo $content;

echo '<div class="row">
<div class="col-md-12">';
$mform->display();
echo '</div>
</div>';


echo $OUTPUT->footer();
