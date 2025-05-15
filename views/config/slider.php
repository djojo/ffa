<?php

require_once(__DIR__ . '/../../../../config.php');
require_once('../utils.php');

require_once($CFG->dirroot . '/theme/remui/classes/form/slideredit.php');

// defined('MOODLE_INTERNAL') || die();

require_login();
isAdminFormation();

global $USER, $DB, $CFG;

//On va chercher le rôle le plus haut de l'utilisateur
$rolename = getMainRole();

$content = "";
$return = optional_param('return', 'adminmenu', PARAM_TEXT);
$imageid = optional_param('imageid', null, PARAM_INT);

if ($imageid) {
    //on va chercher l'image
    $imagetodelete = $DB->get_record('smartch_slider', ['id' => $imageid]);
    // $fullpath = $imagetodelete->imagefixe;
    $fullpath = $CFG->dataroot . '/' . $imagetodelete->imagefixe;
    if (file_exists($fullpath)) {
        // var_dump("delete path ->" . $fullpath);
        unlink($fullpath);
    }
    //on supprime l'image
    $DB->delete_records('smartch_slider', ['id' => $imageid]);
}

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/config/slider.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Modifier le slider");

$mform = new slideredit();

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/my.php');
} else if ($fromform = $mform->get_data()) {

    //l'image
    $file = $mform->get_new_filename('image');
    if ($file) {

        $slider = new stdClass();

        $pathfoldersmartch = $CFG->dataroot . '/smartchimages';

        //on regarde si le dossier smartch exist pour les images
        if (!file_exists($pathfoldersmartch)) {
            mkdir($pathfoldersmartch, 0777, true);
            // $content .= "<h1>Dossier " . $pathfoldersmartch . " créé.</h1>";
        } else {
            // $content .= "<h1>Le dossier " . $pathfoldersmartch . " existe déjà.</h1>";
        }

        //on supprime l'ancien fichier 
        $fullpath = $CFG->dataroot . '/' . $slider->imagefixe;
        if (file_exists($fullpath)) {
            // var_dump("delete path ->" . $fullpath);
            unlink($fullpath);
        }
        // var_dump($fullpath);
        // die();
        $fullpath = "smartchimages/" . GUIDv4() . $file;
        $success = $mform->save_file('image', $CFG->dataroot . '/' . $fullpath, true);
        if (!$success) {
            echo "Oops! something went wrong!";
        }
        //set content for image
        $slider->imagefixe = $fullpath;

        // $content .= "<h1>image " . $fullpath . " créé.</h1>";
    }

    $DB->insert_record('smartch_slider', $slider);
}

echo $OUTPUT->header();



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

    #fitem_id_image{
        display: flex;
        justify-content: center;
    }
    #fitem_id_submitbutton{
        justify-content: center;
    }
    .col-lg-3.col-md-4.col-form-label.p-0{
        display:none;
    }
    .fp-btn-choose{
        display:none;
    }
    .form-filetypes-descriptions{
        display:none;
    }
</style>';


//le header avec bouton de retour au panneau admin
$templatecontextheader = (object)[
    'url' => new moodle_url('/my'),
    'textcontent' => 'Retour'
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);

//le titre
$content .= '<h3 class="FFF-title1" style="margin-top: 80px;">
    <span class="FFABold FFF-White" style="letter-spacing:1px;">Communication</span>
</h3>';

//on va chercher toutes les images dans le slide
$allimages = $DB->get_records_sql('SELECT * 
            FROM mdl_smartch_slider s
            WHERE s.sliderarray IS NULL OR s.sliderarray = ""', null);

//on va chercher toutes les images
$allimagesliders = $DB->get_records_sql('SELECT * 
            FROM mdl_smartch_slider s
            WHERE s.sliderarray IS NOT NULL OR s.sliderarray != ""', null);


$content .= '<div style="background:white;padding:50px 20px;border-radius:5px 5px 0 0;">';

$content .= '<h3 style="color:#00315a;text-align:center;">Images du slider</h3>';

$content .= '<div style="text-align:center;">';


for ($i = 1; $i < 6; $i++) {

    $position = $i - 1;
    //on va chercher l'image qui a la position positionid
    $allimagesliders = $DB->get_records_sql('SELECT * 
FROM mdl_smartch_slider s
WHERE s.sliderarray = ' . $position, null);

    $imageposition = reset($allimagesliders);

    $content .= '<div style="color:#00315a;display:inline-block;">';

    $content .= '<div id="position-' . $i . '" ondrop="drop(event)" ondragover="allowDrop(event)" style="position: relative;width: 200px; height: 150px; margin: 10px; border: 1px solid #00315a;border-radius:15px;">';



    if ($imageposition) {
        $content .= '<img id="' . $imageposition->id . '" draggable="true" ondragstart="drag(event)" style="height: 100%; object-fit: contain;width: 100%; padding: 10px;" src="' . new moodle_url('/theme/remui/views/readimage.php?path=' . $imageposition->imagefixe) . '">';
    }

    $content .= '
                <svg onclick="deleteFrom(' . $i . ')" style="position: absolute; right: 10px; top: 10px; width: 30px; cursor: pointer;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                ';

    $content .= '</div>';


    $content .= '<div style="display: flex; align-items: center; justify-content: center;">';
    if ($i == 1) {
        $content .= '<div>image fixe</div>';
    } else {
        $counter = $i - 1;
        $content .= '<div style="border-radius: 30px; border: 1px solid #00315a; padding: 2px 10px; margin-right: 5px;">' . $counter . ' </div>';
        $content .= '<div>image</div>';
    }
    $content .= '</div>';

    $content .= '</div>';
}
$content .= '</div>';



if (count($allimages) > 0) {
    $content .= '<h3 style="color:#00315a;text-align:center;margin-top: 50px;">Images disponibles</h3>';
    $content .= '<h5 style="color:#00315a;text-align:center;margin-top: 50px;">Vous pouvez "drag and drop" les images dans le slider.</h5>';

    // $content .= '<h5 style="color:#00315a;text-align:center;margin: 30px;">Images disponnibles</h3>';
}

foreach ($allimages as $image) {
    $content .= '<div id="cadre-' . $image->id . '" style="position: relative;display:inline-block;width: 200px; height: 150px; margin: 10px; border: 1px solid #00315a;border-radius:15px;">';
    $content .= '<img id="' . $image->id . '" draggable="true" ondragstart="drag(event)" style="height: 100%; object-fit: contain;width: 100%; padding: 10px;" src="' . new moodle_url('/theme/remui/views/readimage.php?path=' . $image->imagefixe) . '" />';
    $content .= '<a href="' . new moodle_url('/theme/remui/views/config/slider.php?imageid=' . $image->id) . '"><svg style="position: absolute; right: 10px; top: 10px; width: 30px; cursor: pointer;"   xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg></a>';
    $content .= '</div>';
}

$content .= '<h3 style="color:#00315a;text-align:center;margin-top: 50px;margin-bottom:20px;">Téléverser une image</h3>';



echo $content;

$mform->display();

// $content .= html_writer::end_div(); //container

// echo $content;

$urlrequest = $CFG->wwwroot . '/theme/remui/views/config/sliderapi.php';



echo '
    <script>
        function editSlider(action, imageid, positionid) {
            // Créer une instance de l\'objet XMLHttpRequest
            var xhr = new XMLHttpRequest();
        
            // Définir l\'action à effectuer lorsque la requête est terminée (réussie ou échouée)
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // La requête est terminée avec succès (status 200)
                        // Le résultat de la requête est contenu dans xhr.responseText
                        var data = JSON.parse(xhr.responseText);
                        console.log(data); // Afficher les données dans la console (ici, on suppose que les données sont au format JSON)
                    } else {
                        // La requête a échoué (le statut n\'est pas 200)
                        console.error("La requête a échoué avec le statut : " + xhr.status);
                    }
                }
            };
        
            // Définir la méthode de requête et l\'URL de l\'API à appeler
            var apiURL = "' . $urlrequest . '?action="+action+"&imageid="+imageid+"&positionid="+positionid; // Remplacez par l\'URL de l\'API réelle
            xhr.open("GET", apiURL, true);
        
            // Facultatif : si vous envoyez des en-têtes, définissez-les ici (par exemple pour une authentification)
            
            // Envoyer la requête
            xhr.send();
        }
    </script>';

echo '<script>
function allowDrop(ev) {
  ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
    ev.preventDefault();
    let imageid = ev.dataTransfer.getData("text");
    //alert(imageid)
    let elementtomove = document.getElementById(imageid);
    let chaine = ev.target.id;
    let parties = chaine.split("-"); // Divise la chaîne en un tableau en utilisant le caractère "-"
    let positionid = parties[1];
    ev.target.appendChild(elementtomove);
    
    setTimeout(()=>{
        document.getElementById("cadre-"+imageid).remove()
    }, 1)

    

    //on appelle l\'api
    editSlider("change", imageid, positionid);
}

function deleteFrom(positionid){
    //alert(positionid)
    editSlider("delete", null, positionid);
    setTimeout(()=>{
        location.reload();
    }, 10)
    
}
</script>';

echo $OUTPUT->footer();
