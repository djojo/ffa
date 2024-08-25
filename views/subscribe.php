<?php

require_once(__DIR__ . '/../../../config.php');
require_once('./utils.php');

require_login();

global $USER, $DB, $CFG;

$courseid = required_param('courseid', PARAM_INT);
$action = optional_param('action', null, PARAM_TEXT);

if (!$userid) {
    $userid = $USER->id;
}

if (!$courseid) {
    redirect($CFG->wwwroot . '/');
}

$course = $DB->get_record('course', ['id' => $courseid]);
$category = $DB->get_record('course_categories', ['id' => $course->category]);


//on va chercher le cours, la catégorie 
// $course = $DB->get_record_sql('SELECT c.id, c.fullname, cc.name as category 
// FROM mdl_course c
// JOIN mdl_course_categories cc ON cc.id = c.category
// WHERE c.id = ' . $courseid, null);

//on regarde si le cours est dans la catégorie gratuite
if ($category->name == "Formation gratuite") {
    $coursetyperesult = $DB->get_records_sql('
        SELECT cd.value 
        FROM mdl_customfield_data cd
        JOIN mdl_customfield_field cf ON cf.id = cd.fieldid
        WHERE cd.instanceid = ' . $courseid . ' AND cf.shortname = "freecoursetype"', null);
    $coursetypeobject = reset($coursetyperesult);
    if ($coursetypeobject) {
        $res = $coursetypeobject->value;
        if ($res == 1) {
            $coursetype = "Tous publics";
            $cansubscribe = true;
        } else if ($res == 2) {
            $coursetype = "Licenciés";
            //on va chercher l'utilisateur
            $user = $DB->get_record('user', ['id' => $USER->id]);
            //on verifie que son username soit un INNO, si oui on le laisse s'inscrire
            $cansubscribe = checkIfUsernameIsINNO($user->username);
            // //on va chercher le champ perso licencié INNO
            // $userlicencieobject = $DB->get_records_sql('
            //     SELECT cd.value
            //     FROM mdl_customfield_data cd
            //     JOIN mdl_customfield_field cf ON cf.id = cd.fieldid
            //     WHERE cd.instanceid = ' . $user->id . ' AND cf.shortname = "licencie"', null);
            // $userlicencie = reset($userlicencieobject);
            // if ($userlicencie) {
            //     $inno = $userlicencie->value;
            //     if ($inno) {
            //         //il est licencié car il a un numéro INNO
            //         $cansubscribe = true;
            //     } else {
            //         //il n'est pas licencié et ne peut pas s'inscrire au cours
            //         $cansubscribe = false;
            //     }
            // }
        } else {
            $coursetype = $res;
            $cansubscribe = false;
        }
    }
} else {
    redirect(new moodle_url('/'));
}

//on regarde si l'utilisateur est déjà inscrit
$isincourse = checkIfUserIsEnrolled($courseid, $userid);
if ($isincourse) {
    redirect(new moodle_url('/theme/remui/views/formation.php?id=' . $courseid));
}

if ($action == "subscribe" && $cansubscribe == true) {
    //on va check l'id du role student
    $studentrole = $DB->get_record_sql('SELECT * 
    FROM mdl_role 
    WHERE shortname = "student"', null);

    //on l'inscrit au cours
    $success = enrol_try_internal_enrol($courseid, $userid, $studentrole->id, time());

    if ($success) {
        //On l'ajoute au groupe prévu pour ce cours
        addUserToGroupFreeCourse($courseid, $userid);

        redirect(new moodle_url('/theme/remui/views/formation.php?id=' . $courseid));
    }

    // $message .= 'Vous êtes inscrit à la formation ' . $course->fullname . ' !';
}

//on recupère les champs personnalisés
$diplomeresult = $DB->get_records_sql('
SELECT cd.value 
FROM mdl_customfield_data cd
JOIN mdl_customfield_field cf ON cf.id = cd.fieldid
WHERE cd.instanceid = ' . $courseid . ' AND cf.shortname = "diplome"', null);
$diplomeobject = reset($diplomeresult);
if ($diplomeobject) {
    $diplome = $diplomeobject->value;
}


$coursedurationresult = $DB->get_records_sql('
SELECT cd.value 
FROM mdl_customfield_data cd
JOIN mdl_customfield_field cf ON cf.id = cd.fieldid
WHERE cd.instanceid = ' . $courseid . ' AND cf.shortname = "courseduration"', null);
$coursedurationobject = reset($coursedurationresult);
if ($coursedurationobject) {
    $courseduration = $coursedurationobject->value;
}

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/formation.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title($course->fullname);

echo $OUTPUT->header();

echo '<style>
.main-inner {
    margin-top: 0px !important;
}
#topofscroll{
    margin-top:0px !important;
}
@media screen and (max-width: 830px) {
    #topofscroll{
        padding-top:50px !important;
    }
}


img.smartch_background_header {
    height: 550px !important;
}

#page{
    background:transparent !important;
}

#topofscroll {
    background: transparent !important;
    margin-top: 0px !important;
}
</style>';

$content = "";

//on va chercher la formation 
// $course = $DB->get_record('course', ['id' => $courseid]);


// on check si l'utilisateur est enrollé
// if (!checkIfUserIsEnrolled($courseid, $userid)) {
//     //on récupère l'id de la formation gratuite
//     $catfree = $DB->get_record_sql('SELECT * from mdl_course_categories WHERE name = "Formation gratuite"', null);
//     if ($course->category == $catfree->id) {
//         //on va vers la page d'inscription
//         redirect(new moodle_url('/theme/remui/views/subscribe.php?courseid=' . $courseid));
//     } else {
//         //on redirige vers l'accueil
//         redirect(new moodle_url('/'));
//     }
// }




$templatecontextheader = (object)[
    'url' => new moodle_url('/my'),
    'textcontent' => 'Retour au tableau de bord'
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);

//le context du template header pour le retour
$templatecontextheader = (object)[
    'url' => new moodle_url('/'),
    'coursename' => $course->fullname,
    'textcontent' => 'Retour au tableau de bord'

];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_course_header', $templatecontextheader);


//le context du template du parcours
// $templatecontextcourse = (object)[
//     'course' => $course,
//     'coursesummary' => html_entity_decode($course->summary),
//     'format' => "fff-course-box-info"
// ];

setlocale(LC_TIME, 'fr_FR.UTF-8');
$groupname = date('F Y');
// echo $groupname;

//la présentation du parcours
// $content .= $OUTPUT->render_from_template('theme_remui/smartch_course_info', $templatecontextcourse);

$subscribeurl = new moodle_url('/theme/remui/views/subscribe.php?courseid=' . $courseid . '&action=subscribe');


$content .=  '
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8" style="padding: 0;">
        <div class="fff-course-box-info">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div class="fff-course-box-info-details3">';
                    if($courseduration){
                        $content .=  '
                        <div>
                            <svg style="right: 1px;position:relative;"  class="mr-2 smartch_svg" width="22" height="22" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM0 10C0 4.47715 4.47715 0 10 0C15.5228 0 20 4.47715 20 10C20 15.5228 15.5228 20 10 20C4.47715 20 0 15.5228 0 10ZM10 5C10.5523 5 11 5.44772 11 6V9.58579L13.7071 12.2929C14.0976 12.6834 14.0976 13.3166 13.7071 13.7071C13.3166 14.0976 12.6834 14.0976 12.2929 13.7071L9.29289 10.7071C9.10536 10.5196 9 10.2652 9 10V6C9 5.44772 9.44771 5 10 5Z" fill="#00315a"/>
                            </svg>
                            <span class="mr-4 FFARegular" style="font-size:12px;">' . $courseduration . 'h</span>
                        </div>';
                    }
                        if($category->name){
                            $content .=  '    
                            <div>   
                                <svg class="mr-2 smartch_svg" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6 0C6.55228 0 7 0.447715 7 1V2H13V1C13 0.447715 13.4477 0 14 0C14.5523 0 15 0.447715 15 1V2H17C18.6569 2 20 3.34315 20 5V17C20 18.6569 18.6569 20 17 20H3C1.34315 20 0 18.6569 0 17V5C0 3.34315 1.34315 2 3 2H5V1C5 0.447715 5.44772 0 6 0ZM5 4H3C2.44772 4 2 4.44772 2 5V17C2 17.5523 2.44772 18 3 18H17C17.5523 18 18 17.5523 18 17V5C18 4.44772 17.5523 4 17 4H15V5C15 5.55228 14.5523 6 14 6C13.4477 6 13 5.55228 13 5V4H7V5C7 5.55228 6.55228 6 6 6C5.44772 6 5 5.55228 5 5V4ZM4 9C4 8.44772 4.44772 8 5 8H15C15.5523 8 16 8.44772 16 9C16 9.55229 15.5523 10 15 10H5C4.44772 10 4 9.55229 4 9Z" fill="#00315a"/>
                                </svg>
                                <span class="mr-4 FFARegular" style="font-size:12px;">' . $category->name . '</span>
                            </div>';
                        }
                        if($coursetype){
                            $content .=  '
                            <div>
                                <svg style="right: 3px;position:relative;" class="mr-2 smartch_svg"xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                                </svg>
                                <span class="FFARegular" style="font-size:12px;">' . $coursetype . '</span>
                            </div>';
                        }
                    
                    $content .=  '
                    </div>
                    <div>';
if ($cansubscribe) {
    $content .= '<a href="' . $subscribeurl . '" class="smartch_btn">S\'inscrire à la formation</a>';
} else {

    $licenceurl = "https://www.fff.fr/524-faq.html?thematic=associer-licence";
    // $licenceurl = "https://sso.fff.fr/oauth/v2/login";
    $content .= '<a target="_blank" href="' . $licenceurl . '" class="smartch_btn">Compléter mon profil FFF avec mon numéro de licence</a>';
}
$content .= '</div>
                </div>
            
                <h5 class="FFABold FFF-Blue" style="font-size:16px;padding:20px 0 10px 0px;">OBJECTIFS</h5>
                <div class="FFARegular FFF-Blue" style="font-size: 14px; !important;">' . $course->summary . '</div>
                

        </div>
    </div>

</div>';


// $content .= '<div style="display:flex;align-items:center;justify-content:center;">';
// $content .= '<div>';

// $content .= '<h3 style="color:#00315a;">Inscription à la formation ' . $course->fullname . '</h3>';

// $content .= '<div style="text-align:center;">';

// if ($imgcourse == "") {
//     $imgcourse = $CFG->wwwroot . '/theme/remui/pix/background.jpeg';
// }
// $content .= '<img style=" border-radius: 15px;width: 300px; height: 200px;margin:20px 0;" src="' . $imgcourse . '" />';

// if ($message) {
//     $content .= '<h5 style="margin:20px 0;">' . $message . '</h5>';
//     $courseurl = new moodle_url('/theme/remui/views/formation.php?id=' . $courseid . '');
//     $content .= '<a href="' . $courseurl . '" class="smartch_btn">Commencer la formation</a>';
// } else {
//     $content .= '<h5 style="margin:20px 0;">Voulez vous vous inscrire à cette formation ?</h5>';

//     $subscribeurl = new moodle_url('/theme/remui/views/subscribe.php?courseid=' . $courseid . '&action=subscribe');
//     $content .= '<a href="' . $subscribeurl . '" class="smartch_btn">S\'inscrire à la formation</a>';
// }

// $content .= '</div>';
// $content .= '</div>';
// $content .= '</div>';

echo $content;

echo $OUTPUT->footer();
