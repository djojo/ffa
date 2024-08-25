<?php

require_once(__DIR__ . '/../../../config.php');

// ID de l'instance de l'activité
$instanceid = 123; // Remplacez par l'ID de votre instance d'activité

// Récupérer le module de cours (activité) en fonction de l'instance
$coursemodule = get_coursemodule_from_instance('activity_type', $instanceid, $courseid);

if ($coursemodule) {
    // Obtenir le statut de l'activité
    $completionstatus = $coursemodule->completion;

    if ($completionstatus == COMPLETION_COMPLETE) {
        // L'activité est complétée
        echo "L'activité est complétée.";
    } elseif ($completionstatus == COMPLETION_INCOMPLETE) {
        // L'activité est incomplète
        echo "L'activité est incomplète.";
    } else {
        // Le statut de complétion de l'activité est inconnu ou non applicable
        echo "Le statut de complétion de l'activité n'est pas défini.";
    }
} else {
    // L'activité n'a pas été trouvée
    echo "L'activité n'a pas été trouvée.";
}
