<?php

use OAuth2\Storage\Moodle;

require_once(__DIR__ . '/../../../../config.php');
require_once('../utils.php');

require_login();

global $USER, $DB, $CFG;

$content = '';

//On va chercher le rôle le plus haut de l'utilisateur
$rolename = getMainRole();

isAdmin();
// var_dump($rolename);
// die();

$configid = optional_param('configid', null, PARAM_INT);
$action = optional_param('action', null, PARAM_TEXT);
$configvalue = optional_param('configvalue', null, PARAM_TEXT);


if($action == "delete" && $configid){
    $DB->delete_records('smartch_config', ['id' => $configid]);
} else if($action == "update" && $configid && $configvalue){
    $config = $DB->get_record('smartch_config', ['id' => $configid]);
    $config->config_value = $configvalue;
    $DB->update_record('smartch_config', $config);
}

$newconfigkey = optional_param('newconfigkey', null, PARAM_TEXT);
$newconfigvalue = optional_param('newconfigvalue', null, PARAM_TEXT);

if($newconfigkey){
    $newconfig = new stdClass();
    $newconfig->config_key = $newconfigkey;
    $newconfig->config_value = $newconfigvalue;
    $DB->insert_record('smartch_config', $newconfig);
    redirect( new moodle_url('/theme/remui/views/config/index.php'));
}

// $inputconfigkey = optional_param('inputconfigkey', null, PARAM_TEXT);
// $inputconfigvalue = optional_param('inputconfigvalue', null, PARAM_TEXT);


// if($inputconfigkey && $inputconfigvalue){
//     $configtochange = $DB->get_record_sql('SELECT * 
//     FROM mdl_smartch_config sc
//     WHERE sc.config_key = "'.$inputconfigkey.'"', null);
//     $configtochange->config_value = $inputconfigvalue;
//     $DB->update_record('smartch_config', $configtochange);
// }

$portailtype = optional_param('portailtype', null, PARAM_TEXT);
if($portailtype){
    $portail = $DB->get_record_sql('SELECT * 
    FROM mdl_smartch_config sc
    WHERE sc.config_key = "portail"', null);

    if(!empty($portail)){
        //On modifie le portail
        $portail->config_value = $portailtype;
        $DB->update_record('smartch_config', $portail);
    }
}

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/config/index.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Configuration");


echo  '<style>
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
    'url' => new moodle_url('/my'),
    'textcontent' => 'Retour'
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);

//le titre
$content .= '<h3 class="FFF-title1" style="margin-top: 80px;">
    <span class="FFABold FFF-White" style="letter-spacing:1px;">Configuration</span>
</h3>';


//on va chercher la config
$configportail = getConfigPortail();

$content .= '<div class="row" style="background:white;padding:50px 20px;border-radius:5px 5px 0 0;">';
$content .= '<div class="col-md-12">';

$content .= '<div>';

$content .= '<table class="smartch_table">';
$content .= '<tbody>';


// $content .= '<form method="POST" action=""  style="text-align:center;">';
// $content .= '<tr style="margin:10px 0;">';
// $content .= '<td>
//     <div style="margin:10px 0;">Changer le portail</div>
// </td>';
// $content .= '<td>';

// $content .= '<select class="smartch_select" name="portailtype">';
// if($configportail == "portailformation"){
//     $content .= '<option selected value="portailformation">Portail Formation</option>';
//     $content .= '<option value="portailrh">Portail RH</option>';
// } else{
//     $content .= '<option value="portailformation">Portail Formation</option>';
//     $content .= '<option selected value="portailrh">Portail RH</option>';
// }

// $content .= '</select>';

// $content .= '</td>';

// $content .= '<td><input class="smartch_btn" type="submit" value="Mettre à jour"></td>';
// $content .= '</tr>';
// $content .= '</form>';

//on va chercher la liste des keys values
$keyvalues = $DB->get_records_sql('SELECT * FROM mdl_smartch_config sc', null);
foreach($keyvalues as $keyvalue){
    if($keyvalue->config_key != "portail"){
        //FORM pour update une key/value
        $content .= '<form method="POST" action=""  style="text-align:center;">';
        $content .= '<tr style="margin:10px 0;">';
        $content .= '<td>'.$keyvalue->config_key.'</td>';
        $content .= '<td>
                <input type="hidden" name="configid" value="'.$keyvalue->id.'"/>
                <input type="hidden" name="action" value="update"/>
                <textarea rows="1" class="form-control" name="configvalue">'.$keyvalue->config_value.'</textarea>
            </td>';
        $content .= '<td>';
        $content .= '<input class="smartch_btn mr-2" type="submit" value="Mettre à jour">';
        $content .= '<a href="'.new moodle_url('/theme/remui/views/config/index.php').'?action=delete&configid='.$keyvalue->id.'" class="smartch_btn">Supprimer</a>';
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '</form>';
    }
}


// FORM pour ajouter une KEY/VALUE
$content .= '<form method="POST" action=""  style="text-align:center;">';
$content .= '<tr style="margin:10px 0;">';
$content .= '<td>
    <div style="margin:10px 0;">Ajouter une nouvelle clé de configuration : </div>
    <input class="form-control" type="text" name="newconfigkey" placeholder="clé" value=""/>
</td>';
$content .= '<td>
<div style="margin:10px 0;">La valeur de la clé de configuration : </div>
<textarea rows="1" style="width:100%;" class="form-control" name="newconfigvalue" placeholder="valeur"></textarea></td>';
$content .= '<td><input class="smartch_btn" type="submit" value="Ajouter"></td>';
$content .= '</tr>';
$content .= '</form>';


$content .= '</tbody>';
$content .= '</table>';





$content .= '</div>';



$content .= '</div>';
$content .= '</div>';



echo $content;

echo $OUTPUT->footer();
