<?php

require_once(__DIR__ . '/../../../config.php');
require_once('./utils.php');

require_login();

global $USER, $DB, $CFG;

$content = "";

//On va chercher le rôle le plus haut de l'utilisateur
$rolename = getMainRole();

//si le rôle est différent de manager on redirige vers l'accueil
if ($rolename == "super-admin" || $rolename == "manager" || $rolename == "smalleditingteacher" || $rolename == "editingteacher") {
} else {
    redirect($CFG->wwwroot);
}

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/adminmenu.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Administration");


echo $OUTPUT->header();

//le header !!!
$templatecontextheader = (object)[
    'url' => new moodle_url('/'),
    'textcontent' => "Panneau d'administration"
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_text', $templatecontextheader);

$adminfff = false;
if ($rolename == "super-admin" || $rolename == "manager" || $rolename == "managerfree") {
    $adminfff = true;
}
// var_dump($rolename);
// var_dump($adminfff);

$portail = getConfigPortail();

if($portail == "portailformation"){
    //le menu portail formation
    $templatecontext = (object)[
        'url' => new moodle_url('/'),
        'slider' => $slider,
        'adminfff' => $adminfff
    ];
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_admin_menu', $templatecontext);
} else if($portail == "portailrh"){
    //le menu portail formation
    $templatecontext = (object)[
        'url' => new moodle_url('/'),
        'slider' => $slider,
        'adminfff' => $adminfff
    ];
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_adminrh_menu', $templatecontext);
}

echo $content;

echo $OUTPUT->footer();
