

<?php


require_once(__DIR__ . '/../../../config.php');

global $USER;


global $PAGE, $USER, $DB;


// Validation for context is needed.
// $context = \context_system::instance();
// self::validate_context($context);
$coursepercentage = new \core_completion\progress();

$coursescompleted = 0;
$activitiescomplete = 0;
$activitiesdue = 0;
$totalactivities = 0;

$querycourses = 'SELECT c.id, c.fullname FROM mdl_course c
            JOIN mdl_role_assignments ra ON ra.userid = ' . $USER->id . '
            JOIN mdl_context ct ON ct.id = ra.contextid AND c.id = ct.instanceid
            JOIN mdl_role r ON r.id = ra.roleid
            WHERE c.format != "site" AND c.visible = 1';
$courses = $DB->get_records_sql($querycourses, null);



foreach ($courses as $key => $course) {
    global $DB;
    $activities = $DB->get_records_sql("SELECT cm.id as id, activity.summary as summary,
            activity.activityname, c.id AS courseid, c.fullname AS coursename,
            cm.instance AS activityid, m.id as activitytypeid, m.name AS activitytype, cm.section as moduleid
            FROM mdl_course_modules cm
            JOIN mdl_course c ON c.id = cm.course
            JOIN mdl_modules m ON m.id = cm.module
            LEFT JOIN (
                SELECT a.id, a.name AS activityname, 'scorm' AS activitytype, a.intro AS summary
                FROM mdl_scorm a
                UNION
                SELECT a.id, a.name AS activityname, 'forum' AS activitytype, a.intro AS summary
                FROM mdl_forum a
                UNION
                SELECT a.id, a.name AS activityname, 'label' AS activitytype, a.intro AS summary
                FROM mdl_label a
                UNION
                SELECT a.id, a.name AS activityname, 'url' AS activitytype, a.intro AS summary
                FROM mdl_url a
                UNION
                SELECT a.id, a.name AS activityname, 'page' AS activitytype, a.intro AS summary
                FROM mdl_page a
                UNION
                SELECT a.id, a.name AS activityname, 'quiz' AS activitytype, a.intro AS summary
                FROM mdl_quiz a
                UNION
                SELECT a.id, a.name AS activityname, 'data' AS activitytype, a.intro AS summary
                FROM mdl_data a
                UNION
                SELECT a.id, a.name AS activityname, 'assign' AS activitytype, a.intro AS summary
                FROM mdl_assign a
                UNION
                SELECT a.id, a.name AS activityname, 'folder' AS activitytype, a.intro AS summary
                FROM mdl_folder a
                UNION
                SELECT a.id, a.name AS activityname, 'resource' AS activitytype, a.intro AS summary
                FROM mdl_resource a
                UNION
                SELECT a.id, a.name AS activityname, 'lesson' AS activitytype, a.intro AS summary
                FROM mdl_lesson a
                UNION
                SELECT a.id, a.name AS activityname, 'feedback' AS activitytype, a.intro AS summary
                FROM mdl_feedback a
                UNION
                SELECT a.id, a.name AS activityname, 'bigbluebuttonbn' AS activitytype, a.intro AS summary
                FROM mdl_bigbluebuttonbn a
                UNION
                SELECT a.id, a.name AS activityname, 'book' AS activitytype, a.intro AS summary
                FROM mdl_book a
                UNION
                SELECT a.id, a.name AS activityname, 'face2face' AS activitytype, a.intro AS summary
                FROM mdl_face2face a

            ) activity ON activity.id = cm.instance AND activity.activitytype = m.name
            WHERE activity.activitytype != 'folder'
            AND activity.activitytype != 'face2face'
            AND activity.activitytype != 'forum'
            AND c.id = " . $course->id, null);

    $totalactivities = $totalactivities + count($activities);

    foreach ($activities as $activity) {
        $query = 'SELECT cmc.id, cmc.completionstate
                    FROM mdl_course_modules_completion cmc

                    WHERE cmc.userid = ' . $USER->id . ' AND cmc.coursemoduleid = ' . $activity->id;
        $arr = $DB->get_records_sql($query, null);
        $arrobject = reset($arr);
        if ($arrobject) {
            if ($arrobject->completionstate >= 1) {
                // L'activité est complétée
                $activitiescomplete++;
            }
        }
    }
}

//on va chercher la session du cours
$groups = $DB->get_records_sql('SELECT g.id, g.name FROM mdl_groups g
        JOIN mdl_groups_members gm ON gm.groupid = g.id
        WHERE gm.userid = ' . $USER->id . ' AND g.courseid = ' . $course->id, null);


if (count($groups) > 0) {
    $group = reset($groups);
    // $displaysessionid = $group->id;

    $session = $DB->get_record('smartch_session', ['groupid' => $group->id]);

    if ($session) {
        //On va chercher les plannings
        $plannings = $DB->get_records_sql('SELECT DISTINCT sp.id, sp.sectionid, sp.startdate, sp.enddate, sp.geforplanningid
                FROM mdl_smartch_planning sp
                JOIN mdl_smartch_session ss ON ss.id = sp.sessionid
                JOIN mdl_groups g ON g.id = ss.groupid
                JOIN mdl_course c ON c.id = g.courseid
                WHERE c.id = ' . $course->id . ' AND sp.sessionid = ' . $session->id . '
                ORDER BY sp.startdate ASC', null);

        foreach ($plannings as $planning) {
            $totalactivities++;
            if ($planning->startdate < time()) {
                $activitiescomplete++;
            }
        }
    }
}

//on calcule
if ($totalactivities == 0) {
    $activitiesprogress = '0%';
} else {
    $activitiesprogress = ceil($activitiescomplete / $totalactivities * 100);
}

$stats['coursesenrolled'] = count($courses);
$stats['coursescompleted'] = $coursescompleted;
$stats['activitiescomplete'] = $activitiescomplete;
$stats['statsgeneralprogress'] = $activitiesprogress;


// $stats['coursesenrolled'] = 2;
// $stats['coursescompleted'] = 3;
// $stats['activitiescomplete'] = 4;
// $stats['statsgeneralprogress'] = 5;

var_dump($stats);
