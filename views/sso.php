<?php

require_once(__DIR__ . '/../../../config.php');
require_once('./utils.php');
require_once($CFG->libdir.'/moodlelib.php');

require_login();

global $USER, $DB, $CFG;

$messagedeco = "";

//On va chercher le rôle le plus haut de l'utilisateur
$rolename = getMainRole();

$codesso = optional_param('codesso', '', PARAM_TEXT);

function testSSO($usersso, $userdegage)
{
    $chain1 = strtolower($usersso->firstname . '' . $usersso->lastname);
    $chain2 = strtolower($userdegage->firstname . '' . $userdegage->lastname);
    $lev = levenshtein($chain1, $chain2);

    // if ($lev <= 3) {
    //     return true;
    // } else {
    //     return false;
    // }
    return true;
}

if ($codesso) {
    //on va chercher l'utilisateur avec le username SSO
    $user = $DB->get_record('user', ['idnumber' => $codesso]);
    if ($user) {
        //on va chercher l'utilisateur actuel pour récupérer ses données et l'archiver ensuite
        $userdegage = $DB->get_record('user', ['id' => $USER->id]);
        if ($userdegage) {

            //On fait le test de levenshtein
            // if (testSSO($user, $userdegage)) {
            //     $validation = false;
            //     $messagedeco .= "<h5 style='color:red;margin:20px 0;'>Un problème est survenu, veuillez contacter un administrateur...</h5>";
            // } else {

            //on garde l'username de l'utilisateur
            $username = $userdegage->username;

            // $guid = generateGUID();
            // $userdegage->deleted = 1;
            // $userdegage->email = 'migrated_' . $guid . '_' . $userdegage->email;
            // $userdegage->username = 'migrated_' . $guid . '_' . $userdegage->username;
            // $DB->update_record('user', $userdegage);


            delete_user($userdegage);

            //on update l'autre
            $user->username = $username;
            $DB->update_record('user', $user);

            //on met à jour ses paramètres d'authentification
            $authuser = $DB->get_record('auth_oauth2_linked_login', ['userid' => $userdegage->id]);
            if ($authuser) {
                $authuser->userid = $user->id;
                $DB->update_record('auth_oauth2_linked_login', $authuser);
            }

            $validation = true;

            //on déconnecte l'utilisateur
            $messagedeco .= '<h2 class="FFABold" style="margin: 30px 0;text-transform:uppercase; color:#004685;letter-spacing:2px;padding:0 20px;text-transform:uppercase;">Votre compte FFF est associé ! </h2>
                <h5>Vous pouvez à présent vous déconnecter et vous connecter via SSO.</h5>';
            // }
        }
    } else {
        $messagedeco .= "<h5 style='color:red;margin:20px 0;'>Code non valide</h5>";
    }
}


$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/sso.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Oups...");

echo $OUTPUT->header();

$content = "";

$content .= "<style>

img.smartch_background_header{
    display:none;
}
#smartch-header{
    display:none;
}

</style>";

if ($validation) {

    $content .= '<div id="page" style="margin: 0;background-image:url(\'' . new moodle_url('/theme/remui/pix/background-header.png') . '\');background-size: cover;height:100vh;width:100vw;top: 0;text-align: center;left: 0;position: fixed;">

    <div style="background: white; position: fixed; top: 50%; padding: 30px 80px; border-radius: 15px; min-width: 350px; left: 50%; transform: translate(-50%, -50%);">
    
<img style="width:50px;" src="' . new moodle_url('/theme/remui/pix/logoffa.svg') . '" />
    
    
    ' . $messagedeco . '

    <div onclick="document.querySelector(\'.edw-icon-Logout\').parentNode.click()" class="smartch_btn" style="margin:10px 0;background:#D9FDD2;color:#00315a !important;border-color:#D9FDD2;">Retour à la page de connexion</div>
    
    </div>
    
    </div>    ';
} else {
    $content .= '<div id="page" style="margin: 0;background-image:url(\'' . new moodle_url('/theme/remui/pix/background-header.png') . '\');background-size: cover;height:100vh;width:100vw;top: 0;text-align: center;left: 0;position: fixed;">

<div style="background: white;max-width: 500px; position: fixed; top: 50%; padding: 30px 80px; border-radius: 15px; min-width: 350px; left: 50%; transform: translate(-50%, -50%);">

<img style="width:50px;" src="' . new moodle_url('/theme/remui/pix/logoffa.svg') . '" />

' . $messagedeco . '

<h2 class="FFF-Regular-Bold" style="margin: 30px 0;text-transform:uppercase; color:#004685;padding:0 20px;">OUPS...</h2>

<h5>Veuillez vérifier que vous êtes bien concerné par une formation en cours ou qui débute dans moins de 15 jours.</h5>
<h5>Si c\'est le cas, poursuivez votre connexion en entrant votre code personnel reçu par e-mail et en cliquant sur retour à la page de connexion ci-dessous.</h5>
<div style="margin:20px 0;display:none;">
<a href="' . new moodle_url('/theme/remui/views/support.php') . '" class="smartch_btn">Contacter le support</a>
</div>
<form style="display:flex;align-items:center;margin:20px 0;" action="' . new moodle_url('/theme/remui/views/sso.php') . '" method="post">
    <div style="margin-right:30px;">
        <input type="text" style="max-width: 200px;" class="form-control" name="codesso" autocomplete="off" />
    </div>
    <div><input class="smartch_btn" type="submit" value="Envoyer" /></div>
</form>
<div onclick="document.querySelector(\'.edw-icon-Logout\').parentNode.click()" class="smartch_btn" style="margin:10px 0;background:#C41428;border-color:#C41428;">Retour à la page de connexion</div>
</div>

</div>    ';
}

// $content .= '<div style="top: -150px;position:absolute;cursor:pointer;" onclick="location.href=' . new moodle_url('/') . '" class="fff-course-box-info-details">
// <svg width="8" height="15" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
//     <path fill-rule="evenodd" clip-rule="evenodd" d="M5.70711 0.292893C6.09763 0.683417 6.09763 1.31658 5.70711 1.70711L2.41421 5L5.70711 8.29289C6.09763 8.68342 6.09763 9.31658 5.70711 9.70711C5.31658 10.0976 4.68342 10.0976 4.29289 9.70711L0.292893 5.70711C-0.0976311 5.31658 -0.0976311 4.68342 0.292893 4.29289L4.29289 0.292893C4.68342 -0.0976311 5.31658 -0.0976311 5.70711 0.292893Z" fill="white"/>
// </svg>
// <div class="ml-4 FFF-White FFARegular">Retour à l\'accueil</div>
// </div>';

// if ($messagedeco) {
//     $content .= '<div class="row">
// <div class="col-md-12" style="text-align:center;padding: 50px;">
//     <h5>' . $messagedeco . '</h5>
// </div>
// </div>';
// } else {
//     $content .= '<div class="row">
//     <div class="col-md-12" style="text-align:center;color:#00315a;padding: 50px;">
//         <h2>ACCÈS REFUSÉ</h2>
//         <br/>
//         <h5>Vous avez une formation en cours ?</h5>
//         <a href="' . new moodle_url('/theme/remui/views/support.php') . '" class="smartch_btn">Contacter le support</a>
//         <h5>Si vous êtes formateur, vous pouvez associer votre compte en enregistrant le code reçu par email dans l\'enmplacement ci dessous.</h5>
//         <h5>Vous serez alors déconnecté et vous pourrez vous connecter sur la plateforme en utilisant vos identifiants FFF.</h5>
//         <form action="' . new moodle_url('/theme/remui/views/sso.php') . '" type="post">
//             <div style="margin:30px;" >
//                 <h5>Votre code</h5>
//                 <input style="max-width: 200px; margin: 30px auto;" class="form-control" name="codesso" />
//             </div>
//             <div><input class="smartch_btn" type="submit" value="Associer votre compte" /></div>
//         </form>
//     </div>
//     </div>';
// }





echo $content;

echo $OUTPUT->footer();
