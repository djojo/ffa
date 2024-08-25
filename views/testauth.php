

<?php


require_once(__DIR__ . '/../../../config.php');

global $PAGE, $USER, $DB;

$username = optional_param('username', null, PARAM_TEXT);
$issuerid = optional_param('issuerid', null, PARAM_TEXT);

// $username = "65255c7b49e60671af752788";

//On va chercher l'utilisateur
$user = $DB->get_record_sql('SELECT * FROM mdl_user WHERE username = "' . $username . '"', null);

if ($user) {

    $userinfo = [
        'firstname' => $user->firstname,
        'lastname' => $user->lastname,
        'username' => $user->username,
        'email' => $user->email,
        'descriptions' => 'auth via smartch link auth',
    ];

    $issuer = \core\oauth2\api::get_issuer($issuerid);

    $success = \auth_oauth2\api::link_login($userinfo, $issuer, $user->id, true);

    if ($success) {
        $message = 'SUCCESS - ' . $user->firstname . ' ' . $user->lastname . ' a été linké !';
    } else {
        $message = 'ERROR - Problème lors de l\'utilisation de l\'api \auth_oauth2\api::link_login...';
    }
} else {
    $message = 'ERROR - Utilisateur introuvable via le username : ' . $username . '...';
}

echo $message;
