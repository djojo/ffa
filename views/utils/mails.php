<?php

require_once(__DIR__ . '/../../../../config.php');
require_once('../utils.php');
require_once($CFG->libdir . '/moodlelib.php');

require_login();
isAdmin();

global $CFG;

$userid = optional_param('userid', 3, PARAM_INT);
$courseid = optional_param('userid', 2, PARAM_INT);
$groupid = optional_param('groupid', 1, PARAM_INT);
$planningid = optional_param('planningid', 2, PARAM_INT);

smartchSendSubscriptionEmail($userid,$courseid);
smartchSendGroupProgressionEmail($userid,$groupid,$planningid);
smartchSendCompleteBlendedEmail($userid,$groupid,$planningid);
smartchSendCompleteElearningEmail($userid,$groupid);
smartchSendQCMEndModuleEmail($userid,$courseid);
smartchSendQCMEndModuleReminderEmail($userid,$courseid);


//Email d'inscription à une formation
//quand ? A chaque inscription à une nouvelle formation (statut = inscription validée) traitée par la plateforme
function smartchSendSubscriptionEmail($userid, $courseid) {

    global $DB;

    //On va chercher le cours
    $course = $DB->get_record('course', ['id' => $courseid]);

    //On va chercher le user
    $user = $DB->get_record('user', ['id' => $userid]);

    //on va chercher le premier groupe et la session de l'utilisateur
    $groupsession = $DB->get_record_sql('SELECT ss.*, g.name
    FROM mdl_groups_members gm
    JOIN mdl_groups g ON gm.groupid = g.id 
    JOIN mdl_smartch_session ss ON g.id = ss.groupid
    WHERE g.courseid = '.$courseid.'
    AND gm.userid = '.$userid, null);


    //on créer l'utilisateur d'envoi
    $senduser = createSendUserEmail();

    //sujet du mail 
    $subject = "Votre inscription à la formation ".$course->fullname." sur Formation 360 Athle";
    
    //on créer le texte
    $contentmail = '';

    $contentmail .= '<div class="container">';

    //header
    $contentmail .= displayHeaderMail($user->firstname.' '.$user->lastname);

    $contentmail .= '<div>';
    $contentinside = "";
    $contentinside .= "<p>Votre inscription à la formation <strong>".$course->fullname."</strong> a bien été prise en compte. <br><br> Vous pouvez dès maintenant accéder à votre espace personnel et votre contenu de formation sur <a href='".getConfigValueByKey("url_plateforme")."'>".getConfigValueByKey("url_plateforme")."</a>.</p>";
    $contentmail .= $contentinside;
    $contentmail .= '<p>Pour vous connecter, il vous suffit d\'utiliser votre compte ATHLE.</p>';

    $contentmail .= '<div>';
    $contentmail .= '<a href="'.getConfigValueByKey("url_plateforme").'" style="background:#00315A;border-radius:20px;display:inline-block;text-align:center;margin:20px auto;color:white;padding:10px 20px;text-decoration:none;">Accéder à mon compte</a>';
    $contentmail .= '</div>';

    $contentmail .= '<p>Nous vous invitons à vous connecter dès maintenant pour profiter du contenu de formation et réaliser votre partie de formation en ligne obligatoire pour valider votre module et/ou préparer le temps de face à face pédagogique. Ce contenu de formation sera accessible jusqu\'au <strong>'.userdate($groupsession->enddate, '%d/%m/%Y').'</strong>.</p>';
    
    $contentmail .= '<p>Pour toute question ou aide supplémentaire, n\'hésitez pas à contacter notre équipe d\'assistance via <a href="mailto:'.getConfigValueByKey("contact_mail").'">'.getConfigValueByKey("contact_mail").'</a>.</p>';
    
    $contentmail .= '<p>Bonne formation !</p>';

    $contentmail .= '</div>';

    $contentmail .= displayFooterMail();

    echo $contentmail;

    //on format à partir du template
    $contentmail = smartchFormatEmail($contentmail, $user);

    //on envoi le mail
    email_to_user($user, $senduser, $subject, $contentmail, $contentmail);

    // on créer une notification
    smartchCreateNotification($userid, $subject, $contentinside, "course", new moodle_url('/theme/remui/views/courses/details.php', array('id' => $course->id)));

}

//Email de progression des inscrits de sa session
// quand ? à J-5 d'une séance en face à face pédagogique
function smartchSendGroupProgressionEmail($userid, $groupid, $planningid) {

    global $DB;

    //On va chercher le groupe
    $group = $DB->get_record('groups', ['id' => $groupid]);

    //On va chercher le cours
    $course = $DB->get_record('course', ['id' => $group->courseid]);

    //On va chercher le planning
    $planning = $DB->get_record('smartch_planning', ['id' => $planningid]);

    //On va chercher le user
    $user = $DB->get_record('user', ['id' => $userid]);

    //on créer l'utilisateur d'envoi
    $senduser = createSendUserEmail();

    //sujet du mail 
    $subject = "Votre face à face pédagogique est dans 5 jours - Les progressions de votre groupe ".$group->name;
    
    //on créer le texte
    $contentmail = '';
    

    $contentmail .= '<div class="container">';

    //header
    $contentmail .= displayHeaderMail($user->firstname.' '.$user->lastname);

    $contentmail .= '<div>';
    $contentinside = "";
    $contentinside .= '<p>En  tant que formateur, vous allez animer un face à face pédagogique prévu pour le <strong>'.userdate($planning->startdate, '%d/%m/%Y').'</strong>.</p>';
    $contentinside .= '<p>Vous pouvez consulter les statistiques de progression de votre groupe <strong style="text-wrap:nowrap;">'.$group->name.'</strong> sur <a href="'.getConfigValueByKey("url_plateforme").'">'.getConfigValueByKey("url_plateforme").'</a>.</p>';
    $contentmail .= $contentinside;
    $contentmail .= '<div>';
    $contentmail .= '<a href="'.getConfigValueByKey("url_plateforme").'" style="background:#00315A;border-radius:20px;display:inline-block;text-align:center;margin:20px auto;color:white;padding:10px 20px;text-decoration:none;">Accéder à mon compte</a>';
    $contentmail .= '</div>';

    $contentmail .= '<p>Nous vous invitons à vous connecter dès maintenant pour consulter les progressions de votre groupe et contacter les stagiaires (en groupe ou individuellement) si besoin avant votre face à face pédagogique.</p>';
    
    $contentmail .= '<p>Pour toute question ou aide supplémentaire, n\'hésitez pas à contacter notre équipe d\'assistance via <a href="mailto:'.getConfigValueByKey("contact_mail").'">'.getConfigValueByKey("contact_mail").'</a>.</p>';
    
    $contentmail .= '<p>Bonne formation !</p>';

    $contentmail .= '</div>';

    $contentmail .= displayFooterMail();

    echo $contentmail;

    //on format à partir du template
    $contentmail = smartchFormatEmail($contentmail, $user);

    //on envoi le mail
    email_to_user($user, $senduser, $subject, $contentmail, $contentmail);

    // on créer une notification
    smartchCreateNotification($userid, $subject, $contentinside, "course", new moodle_url('/theme/remui/views/courses/details.php', array('id' => $course->id)));

}

//Email de rappel de compléter les e_learning (si parcours blended)
//quand ? à J-5 d'une séance en face à face pédagogique
function smartchSendCompleteBlendedEmail($userid, $groupid, $planningid) {

    global $DB;

    //On va chercher le groupe
    $group = $DB->get_record('groups', ['id' => $groupid]);

    //On va chercher le cours
    $course = $DB->get_record('course', ['id' => $group->courseid]);

    //On va chercher le planning
    $planning = $DB->get_record('smartch_planning', ['id' => $planningid]);

    //On va chercher le user
    $user = $DB->get_record('user', ['id' => $userid]);

    //on créer l'utilisateur d'envoi
    $senduser = createSendUserEmail();

    //sujet du mail 
    $subject = $course->fullname . " Votre face à face pédagogique est dans 5 jours - Vous n'avez pas complété tous vos contenus";
    
    //on créer le texte
    $contentmail = '';
    

    $contentmail .= '<div class="container">';

    //header
    $contentmail .= displayHeaderMail($user->firstname.' '.$user->lastname);

    $contentmail .= '<div>';
    $contentinside = "";
    $contentinside .= '<p>Le face à face pédagogique de votre formation <strong>'.$course->fullname.'</strong> est prévu pour le <strong>'.userdate($planning->enddate, '%d/%m/%Y').'</strong>.</p>';
    $contentinside .= "<p>Il semble que vous n’avez pas réalisé l'intégralité de votre partie de formation en ligne (e-learning) obligatoire pour valider votre module et/ou préparer le temps de face à face pédagogique. Si vous l'avez complété dans les dernières 24h, merci d'ignorer ce message.</p>";
    $contentinside .= "<p>Nous vous invitons à vous connecter sur <a href='".getConfigValueByKey("url_plateforme")."'>".getConfigValueByKey("url_plateforme")."</a> dès maintenant pour pour réaliser votre partie de formation en ligne obligatoire avant votre face à face pédagogique.</p>";
    $contentmail .= $contentinside;
    $contentmail .= '<div>';
    $contentmail .= '<a href="'.getConfigValueByKey("url_plateforme").'" style="background:#00315A;border-radius:20px;display:inline-block;text-align:center;margin:20px auto;color:white;padding:10px 20px;text-decoration:none;">Accéder à mon compte</a>';
    $contentmail .= '</div>';

    
    $contentmail .= '<p>Pour toute question ou aide supplémentaire, n\'hésitez pas à contacter notre équipe d\'assistance via <a href="mailto:'.getConfigValueByKey("contact_mail").'">'.getConfigValueByKey("contact_mail").'</a>.</p>';
    
    $contentmail .= '<p>Bonne formation !</p>';

    $contentmail .= '</div>';

    $contentmail .= displayFooterMail();

    echo $contentmail;

    //on format à partir du template
    $contentmail = smartchFormatEmail($contentmail, $user);

    //on envoi le mail
    email_to_user($user, $senduser, $subject, $contentmail, $contentmail);

    // on créer une notification
    smartchCreateNotification($userid, $subject, $contentinside, "course", new moodle_url('/theme/remui/views/courses/details.php', array('id' => $course->id)));


}
//Email de rappel de compléter les e_learning (si parcours blended)
//quand ? si complétion < 100% à J-15 de la date de fin d’accès au module (si 100% e-learning)
function smartchSendCompleteElearningEmail($userid, $groupid) {

    global $DB;

    //On va chercher le groupe
    $group = $DB->get_record('groups', ['id' => $groupid]);

    //On va chercher le cours
    $course = $DB->get_record('course', ['id' => $group->courseid]);

    //On va chercher le user
    $user = $DB->get_record('user', ['id' => $userid]);

    //on va chercher le premier groupe et la session de l'utilisateur
    $groupsession = $DB->get_record_sql('SELECT ss.*, g.name
    FROM mdl_groups_members gm
    JOIN mdl_groups g ON gm.groupid = g.id 
    JOIN mdl_smartch_session ss ON g.id = ss.groupid
    WHERE g.courseid = '.$course->id.'
    AND gm.userid = '.$userid, null);

    //on créer l'utilisateur d'envoi
    $senduser = createSendUserEmail();

    //sujet du mail 
    $subject = $course->fullname . " Votre formation arrive à expiration dans 15 jours - Vous n'avez pas complété tous vos contenus";
    
    //on créer le texte
    $contentmail = '';
    

    $contentmail .= '<div class="container">';

    //header
    $contentmail .= displayHeaderMail($user->firstname.' '.$user->lastname);

    $contentmail .= '<div>';
    $contentinside = "";
    $contentinside .= '<p>Votre formation '.$course->fullname.' est encore disponible dans votre espace Formation 360 Athle pendant 15 jours. Au <strong>'.userdate($groupsession->enddate + 86400, '%d/%m/%Y').'</strong>, elle ne sera plus disponible dans votre compte.</p>';
    $contentinside .= "<p>Il semble que vous n'avez pas complété l'intégralité du contenu de formation. Si votre formation n'est pas terminée, vous ne pourrez pas la valider et perdrez le bénéfice des travaux effectués.</p>";
    $contentinside .= "<p>Vous devrez alors vous inscrire à une nouvelle session et compléter à nouveau l'intégralité du contenu pour valider votre formation.</p>";
    $contentinside .= "<p>Nous vous invitons à vous connecter sur <a href='".getConfigValueByKey("url_plateforme")."'>".getConfigValueByKey("url_plateforme")."</a> dès maintenant pour compléter votre contenu e-learning avant la date limite.</p>";
    $contentmail .= $contentinside;
    $contentmail .= '<div>';
    $contentmail .= '<a href="'.getConfigValueByKey("url_plateforme").'" style="background:#00315A;border-radius:20px;display:inline-block;text-align:center;margin:20px auto;color:white;padding:10px 20px;text-decoration:none;">Accéder à mon compte</a>';
    $contentmail .= '</div>';

    
    $contentmail .= '<p>Pour toute question ou aide supplémentaire, n\'hésitez pas à contacter notre équipe d\'assistance via <a href="mailto:'.getConfigValueByKey("contact_mail").'">'.getConfigValueByKey("contact_mail").'</a>.</p>';
    
    $contentmail .= '<p>Bonne formation !</p>';

    $contentmail .= '</div>';

    $contentmail .= displayFooterMail();

    echo $contentmail;

    //on format à partir du template
    $contentmail = smartchFormatEmail($contentmail, $user);

    //on envoi le mail
    email_to_user($user, $senduser, $subject, $contentmail, $contentmail);

    // on créer une notification
    smartchCreateNotification($userid, $subject, $contentinside, "course", new moodle_url('/theme/remui/views/courses/details.php', array('id' => $course->id)));

}

//envoi de mail à la fin du QCM de fin de module
//quand ? à la mise à disposition du QCM de fin de module
//(règles à définir mais basé sur le statut de présence)
function smartchSendQCMEndModuleEmail($userid, $courseid) {

    global $DB;

    //On va chercher le cours
    $course = $DB->get_record('course', ['id' => $courseid]);

    //On va chercher le user
    $user = $DB->get_record('user', ['id' => $userid]);

    //on créer l'utilisateur d'envoi
    $senduser = createSendUserEmail();

    //sujet du mail 
    $subject = $course->fullname . " Votre questionnaire de fin de formation est disponible";
    
    //on créer le texte
    $contentmail = '';
    

    $contentmail .= '<div class="container">';

    //header
    $contentmail .= displayHeaderMail($user->firstname.' '.$user->lastname);

    $contentmail .= '<div>';
    
    $contentinside = "";
    $contentinside .= '<p>Votre formation '.$course->fullname.' touche à sa fin. Il est maintenant temps de remplir votre questionnaire d’évaluation de fin de formation.</p>';
    $contentinside .= "<p>Le questionnaire est disponible dès aujourd'hui et jusqu'au <strong>".userdate(time() + 24*60*60*31, '%d/%m/%Y')."</strong>. </p>";
    $contentmail .= $contentinside;
    $contentmail .= '<div>';
    $contentmail .= "<p>Nous vous invitons à vous connecter sur <a href='".getConfigValueByKey("url_plateforme")."'>".getConfigValueByKey("url_plateforme")."</a> dès maintenant pour remplir ce questionnaire.</p>";
    $contentmail .= '<a href="'.getConfigValueByKey("url_plateforme").'" style="background:#00315A;border-radius:20px;display:inline-block;text-align:center;margin:20px auto;color:white;padding:10px 20px;text-decoration:none;">Accéder à mon compte</a>';
    $contentmail .= '</div>';

    
    $contentmail .= '<p>Pour toute question ou aide supplémentaire, n\'hésitez pas à contacter notre équipe d\'assistance via <a href="mailto:'.getConfigValueByKey("contact_mail").'">'.getConfigValueByKey("contact_mail").'</a>.</p>';
    
    $contentmail .= '<p>Bonne formation !</p>';

    $contentmail .= '</div>';

    $contentmail .= displayFooterMail();

    echo $contentmail;

    //on format à partir du template
    $contentmail = smartchFormatEmail($contentmail, $user);

    //on envoi le mail
    email_to_user($user, $senduser, $subject, $contentmail, $contentmail);

    //on créer une notification
    smartchCreateNotification($userid, $subject, $contentinside, "course", new moodle_url('/theme/remui/views/courses/details.php', array('id' => $courseid)));


}


//envoi de mail rappel pour completer le QCM de fin de module
// quand ? à J+15 (à définir) après l'ouverture et sans réponse de la part du stagiaire
function smartchSendQCMEndModuleReminderEmail($userid, $courseid) {

    global $DB;

    //On va chercher le cours
    $course = $DB->get_record('course', ['id' => $courseid]);

    //On va chercher le user
    $user = $DB->get_record('user', ['id' => $userid]);

    //on créer l'utilisateur d'envoi
    $senduser = createSendUserEmail();

    //sujet du mail 
    $subject = $course->fullname . " Il est encore temps de compléter votre questionnaire de fin de formation";
    
    //on créer le texte
    $contentmail = '';
    

    $contentmail .= '<div class="container">';

    //header
    $contentmail .= displayHeaderMail($user->firstname.' '.$user->lastname);

    $contentinside = '';
    $contentinside .= '<div>';
    $contentinside .= "<p>Vous n’avez pas obtenu un résultat suffisant à votre questionnaire d’évaluation ".$course->fullname." ou vous n’avez pas effectué votre première tentative.</p>";
    $contentinside .= "<p>Préparez-vous et connectez-vous sur <a href='".getConfigValueByKey("url_plateforme")."'>".getConfigValueByKey("url_plateforme")."</a> dès que vous serez prêt dans les 30 prochains jours pour effectuer cette dernière tentative.</p>";
    $contentinside .= "<p>En cas d’échec ou sans réponse de votre part à ce questionnaire d’évaluation, votre formation sera considérée comme non complétée.</p>";
    $contentmail .= $contentinside;

    // $contentmail .= '<div>';
    // $contentmail .= "<p>Nous vous invitons à vous connecter sur  <a href='".getConfigValueByKey("url_plateforme")."'>".getConfigValueByKey("url_plateforme")."</a> dès maintenant pour remplir ce questionnaire.</p>";
    // $contentmail .= '<a href="'.getConfigValueByKey("url_plateforme").'" style="background:#00315A;border-radius:20px;display:inline-block;text-align:center;margin:20px auto;color:white;padding:10px 20px;text-decoration:none;">Accéder à mon compte</a>';
    // $contentmail .= '</div>';
    
    
    $contentmail .= '<p>Pour toute question ou aide supplémentaire, n\'hésitez pas à contacter notre équipe d\'assistance via <a href="mailto:'.getConfigValueByKey("contact_mail").'">'.getConfigValueByKey("contact_mail").'</a>.</p>';
    
    $contentmail .= '<p>Bonne formation !</p>';

    $contentmail .= '</div>';

    $contentmail .= displayFooterMail();

    echo $contentmail;

    //on format à partir du template
    $contentmail = smartchFormatEmail($contentmail, $user);

    //on envoi le mail
    email_to_user($user, $senduser, $subject, $contentmail, $contentmail);

    // //on créer une notification
    smartchCreateNotification($userid, $subject, $contentinside, "course", new moodle_url('/theme/remui/views/courses/details.php', array('id' => $courseid)));

}


function smartchCreateNotification($userid, $subject, $content, $type = "course", $url = null){
    
    // Créez une nouvelle instance de message.
    $message = new \core\message\message();
    $message->component = 'moodle';
    $message->name = 'instantmessage';
    $message->userfrom = \core_user::get_noreply_user(); // Utilisateur "noreply".
    $message->userto = $userid; // L'utilisateur qui reçoit le message (ici l'utilisateur actuel pour l'exemple).
    $message->subject = $subject;
    $message->fullmessage = $content;
    $message->fullmessageformat = FORMAT_MARKDOWN;
    $message->fullmessagehtml = '<div style="padding: 20px;">'.$content.'</div>';
    $message->smallmessage = $subject;
    $message->notification = 1; // Indique que c'est une notification.
    $message->contexturl = $url;
    $message->contexturlname = $type;

    // Envoyer le message.
    $messageid = message_send($message);

    // if ($messageid) {
    //     echo 'Notification envoyée avec succès.';
    // } else {
    //     echo 'Erreur lors de l\'envoi de la notification.';
    // }
}