<?php

use tool_brickfield\local\areas\mod_choice\option;

require_once(__DIR__ . '/../../../../config.php');
require_once('../utils.php');
require_once($CFG->dirroot . '/theme/remui/classes/form/addplanning.php');

require_login();

global $USER, $DB, $CFG;

// $to_form = array('variables' => array('userid' => $userid, 'returnurl' => $returnurl));
$mform = new create(null, null);

if ($mform->is_cancelled()) {
    redirect('/');
} else if ($fromform = $mform->get_data()) {

        //on créer un planning
        $planning = new stdClass();
        $planning->sectionid = $fromform->sectionid;
        $planning->sessionid = $fromform->sessionid;
        $planning->startdate = $fromform->startdate;
        $planning->enddate = $fromform->enddate;
        $DB->insert_record('smartch_planning', $planning);

}



$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/tests/planning.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Nouveau planning");

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
    <span class="FFABold FFF-White" style="letter-spacing:1px;">Nouveau planning</span>
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
