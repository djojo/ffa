<?php


require_once(__DIR__ . '/../../../config.php');


global $DB, $CFG;

$imageid = optional_param('imageid', null, PARAM_INT);
$positionid = optional_param('positionid', null, PARAM_INT);
$action = optional_param('action', '', PARAM_TEXT);

$positionid--;

if ($action == "change") {



    //on va chercher l'image qui a la position positionid
    $allimagesliders = $DB->get_records_sql('SELECT * 
                FROM mdl_smartch_slider s
                WHERE s.sliderarray = ' . $positionid, null);

    $imageposition = reset($allimagesliders);


    //on va chercher l'image du drag
    $imagetomove = $DB->get_record('smartch_slider', ['id' => $imageid]);

    //si il a une position
    if ($imagetomove->sliderarray) {
        $newpos = $imagetomove->sliderarray;
    } else {
        $newpos = null;
    }

    if ($imageposition) {
        //on reset la position de l'image qui était la
        $imageposition->sliderarray = $newpos;
        $DB->update_record('smartch_slider', $imageposition);
    }


    //on change la position de la nouvelle image
    $imagetomove->sliderarray = $positionid;
    $DB->update_record('smartch_slider', $imagetomove);
} else if ($action == "delete") {
    //on va chercher l'image sur la positio,
    $imagetoremoves = $DB->get_records_sql('SELECT * 
                FROM mdl_smartch_slider s
                WHERE s.sliderarray = ' . $positionid, null);

    $imagetoremove = reset($imagetoremoves);
    if ($imagetoremove) {
        //on reset la position de l'image qui était la
        $imagetoremove->sliderarray = null;
        $DB->update_record('smartch_slider', $imagetoremove);
    }
}




// $message = $action . ' id-> ' . $imageid . ' pos -> ' . $positionid;

// Envoi de la réponse en JSON
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
echo json_encode($message);
