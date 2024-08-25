<?php

// require_once(__DIR__ . '/../../../config.php');
// require_once('./utils.php');

// require_login();

// global $USER, $DB, $CFG;

// $messagedeco = "";
// $content = "";

// //On va chercher le rôle le plus haut de l'utilisateur
// $rolename = getMainRole();

// $email = optional_param('email', '', PARAM_TEXT);
// $codesso = optional_param('codesso', '', PARAM_TEXT);


// if ($codesso & $email) {


//     //On va chercher le compte avec le code SSO
//     $user = $DB->get_record('user', ['idnumber' => $codesso]);

//     if ($user) {
//         $messagedeco .= "<h5>On a trouvé le compte avec le code SSO :</h5>";
//         $messagedeco .= "<h5>email : " . $user->email . "</h5>";
//         $messagedeco .= "<h5>username : " . $user->username . "</h5>";

//         //on va chercher l'utilisateur avec le mail migré
//         $users = $DB->get_records_sql('SELECT * FROM mdl_user
//             WHERE lower(email) LIKE "%' . $email . '%"', null);
//         $userdegage = reset($users);

//         if ($userdegage) {
//             $messagedeco .= "<h5>On a trouvé l'utilisateur à dégager :</h5>";
//             $messagedeco .= "<h5>email : " . $userdegage->email . "</h5>";
//             $messagedeco .= "<h5>username : " . $userdegage->username . "</h5>";


//             //on garde l'username de l'utilisateur
//             $username = $userdegage->username;

//             //on vérifie qu'il n'existe pas déjà un user avec le username
//             // $existusernametomove = $DB->get_record('user', ['username' => 'migrated_' . $userdegage->username]);
//             // if ($existusernametomove) {
//             //     //On change son username
//             //     $guid = generateGUID();
//             //     //on change l'usename de l'ancien utilisateur migré
//             //     $existusernametomove->deleted = 1;
//             //     $existusernametomove->email = 'migrated_' . $guid . '_' . $userdegage->email;
//             //     $existusernametomove->username = 'migrated_' . $guid . '_' . $userdegage->username;
//             //     $DB->update_record('user', $existusernametomove);
//             // }

//             //on archive l'utilisateur trouvé
//             if ($userdegage->deleted == 0) {
//                 $messagedeco .= "<h5>On archive le compte</h5>";
//                 $userdegage->deleted = 1;
//                 // $userdegage->email = 'migrated2_' . $userdegage->email;
//                 $userdegage->username = 'migrated2_' . $userdegage->username;
//                 $DB->update_record('user', $userdegage);
//             } else {
//                 $messagedeco .= "<h5>Le compte est déjà archivé</h5>";
//             }

//             //on update l'autre compte avec le username
//             $usernameexists = $DB->get_records_sql('SELECT * FROM mdl_user
//             WHERE username = "' . $username . '"', null);
//             $usernameexist = reset($usernameexists);

//             if (!$usernameexist) {
//                 $user->username = $username;
//                 $DB->update_record('user', $user);
//                 $messagedeco .= "<h5>Compte updaté avec le username " . $username . "</h5>";
//             } else {
//                 $messagedeco .= "<h5>Un compte existe déjà avec le username " . $username . "</h5>";
//             }



//             //on met à jour ses paramètres d'authentification
//             $authuser = $DB->get_record('auth_oauth2_linked_login', ['userid' => $userdegage->id]);
//             $messagedeco .= "<h5>On cherche l'auth associé via le userid...</h5>";
//             if ($authuser) {
//                 $authuser->userid = $user->id;
//                 $DB->update_record('auth_oauth2_linked_login', $authuser);
//                 //on déconnecte l'utilisateur
//                 $messagedeco .= "<h5>On a trouvé l'auth associé et le compte est réparé !</h5>";
//             } else {

//                 $messagedeco .= "<h5>On a pas trouvé l'auth associé, on essaye via le username...</h5>";
//                 //il faut trouver la ligne autrement (via le username)
//                 $authusers2 = $DB->get_records_sql('SELECT * 
//                     FROM mdl_auth_oauth2_linked_login
//                     WHERE username LIKE "%' . $username . '%"', null);
//                 $authuser2 = reset($authusers2);

//                 if ($authuser2) {
//                     $messagedeco .= "<h5>On a trouvé l'auth associé et le compte est réparé !</h5>";
//                     $authuser->userid = $user->id;
//                     $DB->update_record('auth_oauth2_linked_login', $authuser);
//                 }
//             }
//         } else {
//             $messagedeco .= "<h5>On a pas trouvé l'utilisateur à dégager...</h5>";
//         }
//     } else {
//         $messagedeco .= "<h5>On a pas trouvé le compte avec le code SSO...</h5>";
//     }
// }


// $context = context_system::instance();
// $PAGE->set_url(new moodle_url('/theme/remui/views/sso.php'));
// $PAGE->set_context(\context_system::instance());
// $PAGE->set_title("Réparer un compte");

// echo $OUTPUT->header();


// $content .= "<style>

// img.smartch_background_header{
//     display:none;
// }
// #smartch-header{
//     display:none;
// }

// </style>";

// $logsrepair = "";
// if (!empty($messagedeco)) {
//     $logsrepair = '<div style="padding: 10px; margin: 10px 0; border: 1px solid; border-radius: 10px;">' . $messagedeco . '</div>';
// }


// $content .= '<div id="page" style="background-image:url(\'' . new moodle_url('/theme/remui/pix/background-header.png') . '\');margin: 0;background-size: cover;height:100vh;width:100vw;top: 0;text-align: center;left: 0;position: fixed;">

// <div style="background: white; position: fixed; top: 50%; padding: 30px 80px; border-radius: 15px; min-width: 350px; left: 50%; transform: translate(-50%, -50%);">

// <img style="width:50px;" src="' . new moodle_url('/theme/remui/pix/logoffa.svg') . '" />

// <h2 class="FFABold" style="margin: 30px 0;text-transform:uppercase; color:#004685;letter-spacing:2px;padding:0 20px;">Réparer un compte</h2>

// <form action="' . new moodle_url('/theme/remui/views/repairsso.php') . '" method="post">
//     ' . $logsrepair . '
    
//     <h5>Entrez l\'email du compte à réparer</h5>
//     <div style="margin:20px;" >
//         <input style="max-width: 300px; margin: 30px auto;" class="form-control" name="email" />
//     </div>
//     <h5>Entrez le code SSO du compte</h5>
//     <div style="margin:10px;" >
//         <input type="text" style="max-width: 300px; margin: 10px auto;" class="form-control" name="codesso" autocomplete="off" />
//     </div>
//     <div><input class="smartch_btn" type="submit" value="Envoyer" /></div>
// </form>
// </div>

// </div>    ';



// echo $content;

// echo $OUTPUT->footer();
