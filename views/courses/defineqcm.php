<?php

require_once(__DIR__ . '/../../../../config.php');
require_once('../utils.php');

require_login();

global $USER, $DB, $CFG;

$content = '';

//On va chercher le rôle le plus haut de l'utilisateur
$rolename = getMainRole();

isStudent();

//on va chercher la config
$configportail = getConfigPortail();


$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/courses/defineqcm.php', ['id'=>$courseid]));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Définir un qcm de din de module");

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


$courseid = required_param('courseid', PARAM_INT);
$course = $DB->get_record('course', ['id'=>$courseid]);

$quizid = $_POST['quizid'];

if($quizid){
    //on va chercher le lien de la formation
    $link = $DB->get_record_sql('SELECT *
    FROM mdl_smartch_rules_qcm 
    WHERE courseid = '.$courseid, null);
    if($link){
        $link->activityid = $quizid;
        $DB->update_record('smartch_rules_qcm', $link);
    } else {
        //On créer le lien 
        $newlink = new stdClass();
        $newlink->courseid = $courseid;
        $newlink->activityid = $quizid;
        $DB->insert_record('smartch_rules_qcm', $newlink);
    }

    //On redirige vers l'index des formations
    redirect(new moodle_url('/theme/remui/views/courses/index.php'));
}

//le header avec bouton de retour à lindex
$templatecontextheader = (object)[
    'url' => new moodle_url('/theme/remui/views/courses/index.php'),
    'textcontent' => 'Retour'
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);

//le titre
$content .= '<h3 class="FFF-title1" style="margin-top: 80px; padding:10px 30px;">
    <span class="FFABold FFF-White" style="letter-spacing:1px;">QCM de fin de module</span>
</h3>';


$content .= '<div class="row" style="padding: 0 30px;">';


//on va chercher le lien de la formation
$link = $DB->get_record_sql('SELECT *
FROM mdl_smartch_rules_qcm 
WHERE courseid = '.$courseid, null);

$content .= '<form style="width:100%;" action="" method="post">';
$content .= '<div style="padding:50px 30px;background:white;width:100%;text-align:center;">';

//on va chercher tous les modules de la formation 
$modules = getCourseModulesWithType('quiz', $courseid);
// var_dump($modules);
if(count($modules) > 0){
    $content .= '<h4 style="margin:50px;">Définissez un quiz de fin de module pour la formation '.$course->fullname.'.</h4>';
    $content .= '<div>';
    $content .= '<select name="quizid" class="smartch_select">';
    foreach($modules as $module){
        if($link->activityid == $module->id){
            $content .= '<option selected value="'.$module->id.'">'.$module->activityname.'</option>';
        } else {
            $content .= '<option value="'.$module->id.'">'.$module->activityname.'</option>';
        }
    }
    $content .= '</select>';
    $content .= '</div>';
    $content .= '<input type="hidden" value="'.$courseid.'" name="courseid" />';
    $content .= '<button class="smartch_btn">Enregistrer</button>';
} else {
    $content .= '<h4>Il n\'y a pas encore de test créé dans cette formation. Créez un test et revenez configurer le qcm de fin de formation ensuite.</h4>';
}


$content .= '</div>';
$content .= '</form>';


$content .= '</div>';

echo $content;

echo $OUTPUT->footer();
