<?php

require_once(__DIR__ . '/../../../../config.php');
require_once('../utils.php');
require_once($CFG->dirroot . '/calendar/lib.php');

global $USER;
// Obtenir les cours de l'utilisateur pour les passer dans calendar_get_events
$user_courses = enrol_get_users_courses($USER->id);
// Extraire les IDs des cours
$courseids = array();
foreach ($user_courses as $course) {
    $courseids[] = $course->id;
}

// var_dump($courseids);

$events = calendar_get_events(time(), intval(time() + 60*60*24*30*2), $USER->id, false, $courseids);

$event = reset($events);
var_dump($events);

$sql = "
    SELECT cs.id AS sectionid
    FROM {course_modules} cm
    JOIN {course_sections} cs ON cm.section = cs.id
    WHERE cm.course = :courseid
    AND cm.instance = :instance
    AND cm.module = (
        SELECT id FROM {modules} WHERE name = :modulename
    )
";

// Exécution de la requête
$params = [
    'courseid' => $event->courseid,
    'instance' => $event->instance,
    'modulename' => $event->modulename,
];

$section = $DB->get_record_sql($sql, $params);
var_dump($section);
// foreach($events as $eventmodule){
//     $module = getModule($eventmodule->instance);

//     var_dump($module);
//     // //on change l'url en fonction du rôle
//     // $eventformated->url = new moodle_url('/').'mod/'.$eventmodule->modulename. '/view.php?id='.$module->id;
    
// }
