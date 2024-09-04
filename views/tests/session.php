<?php

use tool_brickfield\local\areas\mod_choice\option;

require_once(__DIR__ . '/../../../../config.php');
require_once('../utils.php');
require_once($CFG->dirroot . '/theme/remui/classes/form/addsession.php');

require_login();

global $USER, $DB, $CFG;

// $to_form = array('variables' => array('userid' => $userid, 'returnurl' => $returnurl));
$mform = new create(null, null);

if ($mform->is_cancelled()) {
    redirect('/');
} else if ($fromform = $mform->get_data()) {

        //on créer un planning
        $session = new stdClass();
        $session->externalid = 0;
        $session->groupid = $fromform->groupid;
        $session->location = $fromform->location;
        $session->adress1 = $fromform->adress1;
        $session->zip = $fromform->zip;
        $session->city = $fromform->city;
        $session->startdate = $fromform->startdate;
        $session->enddate = $fromform->enddate;
        $sessionid = $DB->insert_record('smartch_session', $session);

        
}



$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/tests/planning.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Nouvelle session");

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
    <span class="FFABold FFF-White" style="letter-spacing:1px;">Nouvelle session</span>
</h3>';

$content .= '<div style="background:white;padding:20px;">';
$content .= '<div class="row mb-5">
<div class="col-md-12">
<h2 style="letter-spacing:1px;max-width:70%;cursor:pointer;"  class="FFARegular FFF-Blue">Pour '.$user->firstname.' '.$user->lastname.'</h2>
</div>
</div>';

echo $content;

if($sessionid){
    echo '<h3>SESSIONID = '.$sessionid.'</h3>';
}

echo '<div class="row">
<div class="col-md-12">';
$mform->display();
echo '</div>
</div>';


echo $OUTPUT->footer();
