<?php
require_once(__DIR__ . '/../../../config.php');

$imagepath = required_param('path', PARAM_RAW_TRIMMED); // Nettoyer et obtenir le chemin de l'image
$fullpath = $CFG->dataroot . '/' . $imagepath;

if (file_exists($fullpath) && is_readable($fullpath)) {
    $fileMime = mime_content_type($fullpath);
    header("Content-Type: " . $fileMime);
    // header('Cache-Control: no-cache, must-revalidate');
    // header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date passée
    // header('Pragma: no-cache');
    readfile($fullpath);
    exit;
} else {
    // Gérer l'erreur si le fichier n'existe pas ou n'est pas lisible
    header("HTTP/1.0 404 Not Found");
    // echo "Fichier non trouvé ou accès refusé";
    exit;
}
