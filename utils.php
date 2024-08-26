<?php

require_once(__DIR__ . '/../../config.php');
require_once('./views/utils.php');

function smartchSendSubscriptionEmail($userid, $courseid) {

    global $DB;

    //On va chercher le cours
    $course = $DB->get_record('course', ['id' => $courseid]);

    //On va chercher le user
    $user = $DB->get_record('user', ['id' => $userid]);

    //on créer l'utilisateur d'envoi
    $senduser = new stdClass();
    $senduser->email = getConfigValueByKey("contact_mail");
    $senduser->firstname = "Portail";
    $senduser->lastname = "Formation FFA";
    $senduser->maildisplay = true;
    $senduser->mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML/Text emails.
    $senduser->id = -99; // Moodle User ID. If it is for someone who is not a Moodle user, use an invalid ID like -99.
    $senduser->firstnamephonetic = "Portail";
    $senduser->lastnamephonetic = "Formation FFA";
    $senduser->middlename = "";
    $senduser->alternatename = "";

    //on créer le texte
    $contentemail = "<div>Vous avez été inscrit à la formation ".$course->fullname."</div>";

    //on format à partir du template
    $contentmail = smartchFormatEmail($contentemail, $user);

    //on envoi le mail
    email_to_user($user, $senduser, "Inscription à la formation ".$course->fullname, $contentmail, $contentmail);

}