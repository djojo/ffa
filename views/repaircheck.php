<?php

require_once(__DIR__ . '/../../../config.php');
// require_once('./utils.php');


global $DB;

$messagedeco = "";

//On va chercher le rôle le plus haut de l'utilisateur
// $rolename = getMainRole();

$email = optional_param('email', '', PARAM_TEXT);

if ($email) {

    $account = $DB->get_record('user', ['email' => $email]);
    if ($account) {
        $messagedeco .= '<h5>On a trouve le compte : ' . $account->email . ' (' . $account->id . ')</h5>';
    }


    $accountsauths = $DB->get_records_sql('SELECT * 
            FROM mdl_auth_oauth2_linked_login
            WHERE email = "' . $email . '"', null);
    $accountsauth = reset($accountsauths);
    if ($accountsauth) {
        $messagedeco .= '<h5>On a trouve l\'auth du mail : ' . $email . ' (' . $accountsauth->id . ') le vardump</h5>';
        var_dump($accountsauth);
    }
}


$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/repairstudent.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Récupération");

echo $OUTPUT->header();

$content = "";

$content .= '<a href="' . new moodle_url('/theme/remui/views/adminmenu.php') . '" style="z-index: 3;font-size:0.8rem;cursor: pointer; display: flex; align-items: center; position: absolute; top: 120px;">
<svg width="8" height="15" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.70711 0.292893C6.09763 0.683417 6.09763 1.31658 5.70711 1.70711L2.41421 5L5.70711 8.29289C6.09763 8.68342 6.09763 9.31658 5.70711 9.70711C5.31658 10.0976 4.68342 10.0976 4.29289 9.70711L0.292893 5.70711C-0.0976311 5.31658 -0.0976311 4.68342 0.292893 4.29289L4.29289 0.292893C4.68342 -0.0976311 5.31658 -0.0976311 5.70711 0.292893Z" fill="white"/>
</svg>
<div class="ml-4 FFF-White FFARegular">Retour</div>
</a>';


// </style>";
if (!empty($messagedeco)) {

    $content .= '<div id="page" style="margin: 0;background-image:url(\'' . new moodle_url('/theme/remui/pix/background-header.png') . '\');background-size: cover;height:100vh;width:100vw;top: 0;text-align: center;left: 0;position: fixed;">

<div style="background: white; position: fixed; top: 50%; padding: 30px 80px; border-radius: 15px; min-width: 350px; left: 50%; transform: translate(-50%, -50%);">

<img style="width:50px;" src="' . new moodle_url('/theme/remui/pix/logoffa.svg') . '" />

<h2 class="FFABold" style="margin: 30px 0;text-transform:uppercase; color:#004685;letter-spacing:2px;padding:0 20px;">Réparer un compte formateur</h2>

<form action="' . new moodle_url('/theme/remui/views/repaircheck.php') . '" type="post">
    <div style="padding: 10px; margin: 10px 0; border: 1px solid; border-radius: 10px;">' . $messagedeco . '</div>
    <h5>Entrez un email pour avoir des informations</h5>
    <div style="margin:20px;" >
        <input style="max-width: 300px; margin: 30px auto;" class="form-control" name="email" />
    </div>
    <div><input class="smartch_btn" type="submit" value="Envoyer" /></div>
</form>
</div>

</div>    ';
} else {
    $content .= '<div id="page" style="margin: 0;background-image:url(\'' . new moodle_url('/theme/remui/pix/background-header.png') . '\');background-size: cover;height:100vh;width:100vw;top: 0;text-align: center;left: 0;position: fixed;">

<div style="background: white; position: fixed; top: 50%; padding: 30px 80px; border-radius: 15px; min-width: 350px; left: 50%; transform: translate(-50%, -50%);">

<img style="width:50px;" src="' . new moodle_url('/theme/remui/pix/logoffa.svg') . '" />

<h2 class="FFABold" style="margin: 30px 0;text-transform:uppercase; color:#004685;letter-spacing:2px;padding:0 20px;">Réparer un compte formateur</h2>




<form action="' . new moodle_url('/theme/remui/views/repaircheck.php') . '" type="post">
    <h5>Entrez un email pour avoir des informations</h5>
    <div style="margin:20px;" >
        <input style="max-width: 300px; margin: 30px auto;" class="form-control" name="email" />
    </div>
    <div><input class="smartch_btn" type="submit" value="Envoyer" /></div>
</form>
</div>

</div>    ';
}



echo $content;

echo $OUTPUT->footer();
