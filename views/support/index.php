<?php

require_once(__DIR__ . '/../../../../config.php');
require_once('../utils.php');

// defined('MOODLE_INTERNAL') || die();

require_login();

$subject = optional_param('subject', "", PARAM_TEXT);
$body = optional_param('body', "", PARAM_TEXT);

global $USER, $DB, $CFG, $PAGE;

if ($subject && $body) {

    //on va chercher l'utilisateur pour le support
    // $senduser = $DB->get_record('user', ['email' => 'servicedigital.ieff@fff.fr']);
    


    $support_mail = getConfigValueByKey('support_mail');
    $contact_mail = getConfigValueByKey('contact_mail');

    if($support_mail){
        //on créer l'objet user
        $senduser = new stdClass();
        $senduser->email = $support_mail;
        $senduser->firstname = "Portail";
        $senduser->lastname = "Formation FFA";
        $senduser->maildisplay = true;
        $senduser->mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML/Text emails.
        $senduser->id = -99; // Moodle User ID. If it is for someone who is not a Moodle user, use an invalid ID like -99.
        $senduser->firstnamephonetic = "Portail";
        $senduser->lastnamephonetic = "Formation FFA";
        $senduser->middlename = "";
        $senduser->alternatename = "";
    }

    if ($senduser) {
        $from = $USER->email;
        //On envoi un mail
        email_to_user($senduser, $from, $subject, $body, $body);
        $message = "message envoyé";
    } else {
        if($contact_mail){
            $message = "Il n'y pas de contact pour le support,<br/> veuillez contacter directement par mail " . $contact_mail;
        }else{
            $message = "Désolé il n'y pas de contact pour le support configuré...";
        }
    }
}

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/support/index.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Contacter le support");

$content = "";

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

require_once('../returns.php');

//le titre
$content .= '<h3 class="FFF-title1" style="margin-top: 80px;">
    <span class="FFABold FFF-White" style="letter-spacing:1px;">Support</span>
</h3>';


// $content .= '<img class="smartch_background_header" src="https://iff-uat.smartchlab.fr/theme/remui/pix/background-header.png">';
if ($message) {
    $content .= '<div>

<div style="background: white; position: relative; padding: 30px 80px; width: 100%;">

<h5>' . $message . '</h5>
<a href="' . new moodle_url('/') . '" style="margin:20px 0;" class="smartch_btn">Retour</a>
</div>

</div>    ';
} else {

    $content .= '<div >

    <div style="background: white; position: relative; padding: 30px 80px; width: 100%;">

    <form method="POST" action="' . new moodle_url('/theme/remui/views/support/index.php') . '">
    <select name="subject" style="padding: 0 20px;margin: 20px 0;" class="form-control">
        <option>Question pour le support</option>
        <option>Rapporter un bug</option>
    </select>
    <textarea name="body" rows="4" cols="30" class="form-control" placeholder="Contenu du message">
    </textarea>
    <div style="text-align:right;">
    <button type="submit" style="margin:20px 0;" class="smartch_btn">Envoyer</button>
    </div>
    </form>
    </div>
    
    </div>    ';
}


echo $content;


echo $OUTPUT->footer();
