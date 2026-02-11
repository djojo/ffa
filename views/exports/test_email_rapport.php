<?php
/**
 * Script de test : envoie uniquement le mail de rapport FFA
 * Sans appeler l'API FFA
 *
 * Usage : php theme/remui/views/exports/test_email_rapport.php
 */

define('CLI_SCRIPT', true);
require_once(__DIR__ . '/../../../../config.php');

global $CFG;

// Détection environnement
$environment = 'LOCAL';
if (strpos($CFG->wwwroot, 'ffa-uat') !== false) {
    $environment = 'UAT';
} elseif (strpos($CFG->wwwroot, 'ffa-dev') !== false) {
    $environment = 'DEV';
} elseif (strpos($CFG->wwwroot, 'formation360') !== false) {
    $environment = 'PROD';
}

echo "Environnement detecte: {$environment}\n";
echo "Envoi du mail de test...\n";

$from = new stdClass();
$from->email = 'noreply@formation360.athle.fr';
$from->firstname = 'Formation';
$from->lastname = 'FFA';
$from->maildisplay = true;
$from->mailformat = 1;
$from->id = -99;

$to = new stdClass();
$to->email = 'maunick@smartch.fr';
$to->firstname = 'Jo';
$to->lastname = 'Chef de projet';
$to->maildisplay = true;
$to->mailformat = 1;
$to->id = -98;

// Données fictives pour le test
$total = 1460;
$nb_successes = 1458;
$nb_errors = 2;

$subject = "[{$environment}] TEST - Rapport synchronisation Formation FFA - " . date('d/m/Y');

$status_label = "ERREURS DETECTEES";

$messagetext = "Bonjour,\n\n"
    . "Voici le rapport quotidien de synchronisation des resultats de formation entre la plateforme Moodle et le systeme FFA.\n\n"
    . "Statut : {$status_label}\n"
    . "-----------------------------------\n"
    . "Date d'execution  : " . date('d/m/Y H:i:s') . "\n"
    . "Environnement     : {$environment}\n"
    . "-----------------------------------\n\n"
    . "Resultats de la synchronisation :\n"
    . "- Nombre total d'inscriptions traitees : {$total}\n"
    . "- Envois reussis vers l'API FFA        : {$nb_successes}\n"
    . "- Erreurs                              : {$nb_errors}\n\n"
    . "Pour chaque inscription, les donnees suivantes ont ete transmises a la FFA :\n"
    . "  - Identifiant du cours (dbIDCours)\n"
    . "  - Email de l'utilisateur (dbEmail)\n"
    . "  - Code licence (dbLicence)\n"
    . "  - Numero de groupe (dbGroupe)\n"
    . "  - Taux de completion e-learning en % (dbTauxE)\n"
    . "  - Date de synchronisation (dbDate)\n"
    . "\n--- DETAIL DES ERREURS ---\n"
    . "  - [1/1460] CURL ERROR test@example.com (cours M00001): Connection timeout\n"
    . "  - [2/1460] HTTP 500 test2@example.com (cours M00002): Internal Server Error\n"
    . "\nMerci de signaler ces erreurs a l'equipe technique.\n"
    . "\n---\nCe rapport est genere automatiquement chaque jour a 7h00.\nPlateforme : Formation FFA (Moodle)\n";

$messagehtml = nl2br(htmlspecialchars($messagetext));

$result = email_to_user($to, $from, $subject, $messagetext, $messagehtml);

if ($result) {
    echo "Mail envoye avec succes a {$to->email}\n";
    echo "Sujet: {$subject}\n";
} else {
    echo "ECHEC de l'envoi du mail a {$to->email}\n";
}

// Envoyer aussi au chef de projet (test avec erreurs fictives)
$to_cdp = new stdClass();
$to_cdp->email = 'daviddumas@smartch.fr';
$to_cdp->firstname = 'David';
$to_cdp->lastname = 'Dumas';
$to_cdp->maildisplay = true;
$to_cdp->mailformat = 1;
$to_cdp->id = -97;

$result_cdp = email_to_user($to_cdp, $from, $subject, $messagetext, $messagehtml);

if ($result_cdp) {
    echo "Mail envoye avec succes a {$to_cdp->email}\n";
} else {
    echo "ECHEC de l'envoi du mail a {$to_cdp->email}\n";
}
