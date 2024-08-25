<?php
// This file is part of Moodle Course Rollover Plugin
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     smartch
 * @author      Geoffroy Rouaix
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once('./utils.php');

// defined('MOODLE_INTERNAL') || die();

require_login();

$subject = optional_param('subject', "", PARAM_TEXT);
$body = optional_param('body', "", PARAM_TEXT);

global $USER, $DB, $CFG, $PAGE;

if ($subject && $body) {

    //on va chercher l'utilisateur pour le support
    // $senduser = $DB->get_record('user', ['email' => 'servicedigital.ieff@fff.fr']);
    
    //on créer l'objet user
    $senduser = new stdClass();
    $senduser->email = "servicedigital.ieff@fff.fr";
    $senduser->firstname = "Portail";
    $senduser->lastname = "Formation FFF";
    $senduser->maildisplay = true;
    $senduser->mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML/Text emails.
    $senduser->id = -99; // Moodle User ID. If it is for someone who is not a Moodle user, use an invalid ID like -99.
    $senduser->firstnamephonetic = "Portail";
    $senduser->lastnamephonetic = "Formation FFF";
    $senduser->middlename = "";
    $senduser->alternatename = "";

    if ($senduser) {
        $from = $USER->email;
        //On envoi un mail
        email_to_user($senduser, $from, $subject, $body, $body);
        $message = "message envoyé";
    } else {
        $message = "Il n'y pas de contact pour le support,<br/> veuillez contacter directement par mail <a href='mailto:servicedigital.ieff@fff.fr'>servicedigital.ieff@fff.fr</a>";
    }
}

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/support.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Contacter le support");

echo $OUTPUT->header();

$content = "";

$content .= "<style>

img.smartch_background_header{
    display:none;
}
#smartch-header{
    display:none;
}
#page-footer{
    z-index:20;
}
</style>";


// $content .= '<img class="smartch_background_header" src="https://iff-uat.smartchlab.fr/theme/remui/pix/background-header.png">';
if ($message) {
    $content .= '<div id="page" style="margin: 0;background-image:url(\'' . new moodle_url('/theme/remui/pix/background-header.png') . '\');background-size: cover;height:100vh;width:100vw;top: 0;text-align: center;left: 0;position: fixed;">

<div style="background: white; position: fixed; top: 50%; padding: 30px 80px; border-radius: 15px; min-width: 350px; left: 50%; transform: translate(-50%, -50%);">

<h2 class="FFABold" style="margin: 30px 0;text-transform:uppercase; color:#004685;letter-spacing:2px;padding:0 20px;">Contactez le support</h2>
<h5>' . $message . '</h5>
<a href="' . new moodle_url('/') . '" style="margin:20px 0;" class="smartch_btn">Retour</a>
</div>

</div>    ';
} else {

    $content .= '<div id="page" style="margin: 0;background-image:url(\'' . new moodle_url('/theme/remui/pix/background-header.png') . '\');background-size: cover;height:100vh;width:100vw;top: 0;text-align: center;left: 0;position: fixed;">

    <div style="background: white; position: fixed; top: 50%; padding: 30px 80px; border-radius: 15px; min-width: 350px; left: 50%; transform: translate(-50%, -50%);">
    
    <h2 class="FFABold" style="margin: 30px 0;text-transform:uppercase; color:#004685;letter-spacing:2px;padding:0 20px;">Contactez le support</h2>
    <form method="POST" action="' . new moodle_url('/theme/remui/views/support.php') . '">
    <select name="subject" style="padding: 0 20px;margin: 20px 0;" class="form-control">
        <option>Sujet</option>
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
