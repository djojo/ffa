<?php

require_once(__DIR__ . '/../../../config.php');

function startDailyStats($date)
{
    //on formate la date
    $realdate = date('Y-m-d', $date);
    $timestart = strtotime($realdate . ' -2 days'); //le timestamp à minuit l'avant veille
    $timeend = strtotime($realdate . ' -1 day'); // Convertir la date en timestamp à minuit la veille
    $timeday = strtotime(date("Y-m-d 12:00:00", $timeend));

    //on calcule les stats globales
    calculateGlobalStats($timeday, $timestart, $timeend);

    //on calcule les stats des formations
    calculateCourseStats($timeday, $timestart, $timeend);

    //on calcule les stats des sessions
    calculateSessionsStats($timeday, $timestart, $timeend);
}

function calculateSessionsStats($timeday, $timestart, $timeend)
{
    global $DB;

    //on supprime les stats des sessions
    $DB->delete_records('smartch_stats_session', ['datecreated' => $timeday]);

    //on va chercher toutes les sessions actives
    // $allcourses = $DB->get_records_sql('SELECT * 
    // FROM mdl_course c
    // WHERE c.format != "site"', null);
    // foreach ($allcourses as $course) {

    //     $coursestat = new stdClass();
    //     $coursestat->datecreated = $timeday;
    //     $coursestat->courseid = $course->id;

    //     //le nombre de sessions en cours
    //     $allsessions = $DB->get_records_sql('SELECT DISTINCT ss.id
    //     FROM mdl_smartch_session ss
    //     JOIN mdl_groups g ON g.id = ss.groupid
    //     WHERE g.courseid = ' . $course->id . '
    //     AND ss.startdate < ' . $timestart . '
    //     AND ss.enddate > ' . $timeend . '', null);
    //     $coursestat->countsessions = count($allsessions);

    //     //le nombre d'utilisateurs qui se sont connectés au cours
    //     $usersconnected = $DB->get_records_sql('SELECT DISTINCT al.userid
    //     FROM mdl_smartch_activity_log al
    //     WHERE al.course = ' . $course->id . '
    //     AND al.timestart > ' . $timestart . '
    //     AND al.timestart < ' . $timeend . '', null);
    //     $coursestat->countusersconnected = count($usersconnected);

    //     //le temps passé sur le cours
    //     $dailytotaltimespent = 0;
    //     $dailylogs = $DB->get_records_sql('SELECT al.id, al.timespent
    //     FROM mdl_smartch_activity_log al
    //     WHERE al.course = ' . $course->id . '
    //     AND al.timestart > ' . $timestart . '
    //     AND al.timestart < ' . $timeend . '', null);
    //     foreach ($dailylogs as $dailylog) {
    //         $dailytotaltimespent += $dailylog->timespent;
    //     }
    //     $coursestat->timespent = $dailytotaltimespent;

    //     //on enregistre les stats du cours
    //     $DB->insert_record('smartch_stats_course', $coursestat);
    // }
}

function calculateCourseStats($timeday, $timestart, $timeend)
{
    global $DB;

    //on supprime les stats des cours
    $DB->delete_records('smartch_stats_course', ['datecreated' => $timeday]);

    //on va chercher tous les cours
    $allcourses = $DB->get_records_sql('SELECT * 
    FROM mdl_course c
    WHERE c.format != "site"', null);
    foreach ($allcourses as $course) {

        $coursestat = new stdClass();
        $coursestat->datecreated = $timeday;
        $coursestat->courseid = $course->id;

        //le nombre de sessions en cours
        $allsessions = $DB->get_records_sql('SELECT DISTINCT ss.id
        FROM mdl_smartch_session ss
        JOIN mdl_groups g ON g.id = ss.groupid
        WHERE g.courseid = ' . $course->id . '
        AND ss.startdate < ' . $timestart . '
        AND ss.enddate > ' . $timeend . '', null);
        $coursestat->countsessions = count($allsessions);

        //le nombre d'utilisateurs qui se sont connectés au cours
        $usersconnected = $DB->get_records_sql('SELECT DISTINCT al.userid
        FROM mdl_smartch_activity_log al
        WHERE al.course = ' . $course->id . '
        AND al.timestart > ' . $timestart . '
        AND al.timestart < ' . $timeend . '', null);
        $coursestat->countusersconnected = count($usersconnected);

        //le nombre de formateurs qui se sont connectés
        $teachersconnected = $DB->get_records_sql('SELECT DISTINCT al.userid
        FROM mdl_smartch_activity_log al
        JOIN mdl_user u ON u.id = al.userid
        JOIN mdl_role_assignments ra ON ra.userid = u.id
        JOIN mdl_role r ON r.id = ra.roleid
        WHERE al.course = ' . $course->id . '
        AND (r.shortname = "smalleditingteacher"
        OR r.shortname = "editingteacher"
        OR r.shortname = "noneditingteacher"
        OR r.shortname = "teacher")
        AND al.timestart > ' . $timestart . '
        AND al.timestart < ' . $timeend . '', null);
        $coursestat->countteacherconnected = count($teachersconnected);

        //le nombre d'etudiants qui se sont connectés
        $studentsconnected = $DB->get_records_sql('SELECT DISTINCT al.userid
        FROM mdl_smartch_activity_log al
        JOIN mdl_user u ON u.id = al.userid
        JOIN mdl_role_assignments ra ON ra.userid = u.id
        JOIN mdl_role r ON r.id = ra.roleid
        WHERE al.course = ' . $course->id . '
        AND r.shortname = "student"
        AND al.timestart > ' . $timestart . '
        AND al.timestart < ' . $timeend . '', null);
        $coursestat->countstudentconnected = count($studentsconnected);

        //le temps passé sur le cours
        $dailytotaltimespent = 0;
        $dailylogs = $DB->get_records_sql('SELECT al.id, al.timespent
        FROM mdl_smartch_activity_log al
        WHERE al.course = ' . $course->id . '
        AND al.timestart > ' . $timestart . '
        AND al.timestart < ' . $timeend . '', null);
        foreach ($dailylogs as $dailylog) {
            $dailytotaltimespent += $dailylog->timespent;
        }
        $coursestat->timespent = $dailytotaltimespent;

        //on enregistre les stats du cours
        $DB->insert_record('smartch_stats_course', $coursestat);
    }
}

function calculateGlobalStats($timeday, $timestart, $timeend)
{
    global $DB;

    //on supprime les stats
    $DB->delete_records('smartch_stats_global', ['datecreated' => $timeday]);

    $globalstat = new stdClass();
    $globalstat->datecreated = $timeday;

    //le nombre d'utilisateurs
    $allusers = $DB->get_records('user', null);
    $globalstat->countusers = count($allusers);

    //le nombre de formations
    $allcourses = $DB->get_records_sql('SELECT * 
    FROM mdl_course c
    WHERE c.format != "site"', null);
    $globalstat->countcourses = count($allcourses);

    //le nombre de sessions
    $allsessions = $DB->get_records_sql('SELECT * 
    FROM mdl_smartch_session', null);
    $globalstat->countsessions = count($allsessions);

    //le nombre d'utilisateurs qui se sont connectés
    $usersconnected = $DB->get_records_sql('SELECT DISTINCT al.userid
    FROM mdl_smartch_activity_log al
    WHERE al.timestart > ' . $timestart . '
    AND al.timestart < ' . $timeend . '', null);
    $globalstat->countusersconnected = count($usersconnected);

    //le nombre de formateurs qui se sont connectés
    $teachersconnected = $DB->get_records_sql('SELECT DISTINCT al.userid
    FROM mdl_smartch_activity_log al
    JOIN mdl_user u ON u.id = al.userid
    JOIN mdl_role_assignments ra ON ra.userid = u.id
    JOIN mdl_role r ON r.id = ra.roleid
    WHERE (r.shortname = "smalleditingteacher"
    OR r.shortname = "editingteacher"
    OR r.shortname = "noneditingteacher"
    OR r.shortname = "teacher")
    AND al.timestart > ' . $timestart . '
    AND al.timestart < ' . $timeend . '', null);
    $globalstat->countteacherconnected = count($teachersconnected);

    //le nombre d'etudiants qui se sont connectés
    $studentsconnected = $DB->get_records_sql('SELECT DISTINCT al.userid
    FROM mdl_smartch_activity_log al
    JOIN mdl_user u ON u.id = al.userid
    JOIN mdl_role_assignments ra ON ra.userid = u.id
    JOIN mdl_role r ON r.id = ra.roleid
    WHERE r.shortname = "student"
    AND al.timestart > ' . $timestart . '
    AND al.timestart < ' . $timeend . '', null);
    $globalstat->countstudentconnected = count($studentsconnected);

    //le temps passé global
    $dailytotaltimespent = 0;
    $dailylogs = $DB->get_records_sql('SELECT al.id, al.timespent
    FROM mdl_smartch_activity_log al
    WHERE al.timestart > ' . $timestart . '
    AND al.timestart < ' . $timeend . '', null);
    foreach ($dailylogs as $dailylog) {
        $dailytotaltimespent += $dailylog->timespent;
    }
    $globalstat->timespent = $dailytotaltimespent;

    $globalstat->countteacher = 0;
    //on enregistre les stats globales
    $DB->insert_record('smartch_stats_global', $globalstat);
    var_dump($globalstat);
}


global $DB;

if (isset($_GET['date'])) {
    $date = $_GET['date'];
} else {
    $date = null; // or provide a default value
}


echo '<form action="" method="get">';
echo '<input class="form-control" type="date" name="date"/>';
echo '<input class="smartch_btn" type="submit" value="Submit"/>';
echo '</form>';

if (!empty($date)) {

    $dateActuelle = new DateTime($date);
    $dateActuelle->setTime(12, 0, 0);
    $timestamp = $dateActuelle->getTimestamp();

    // //on va chercher les stats du jour
    // $stats = $DB->get_records_sql('SELECT * 
    // FROM mdl_smartch_stats
    // WHERE datecreated = ' . $timestamp, null);

    //on supprime les stats
    // $DB->delete_records('smartch_stats_global', ['datecreated' => $timestamp]);
    // $DB->delete_records('smartch_stats_course', ['datecreated' => $timestamp]);
    // $DB->delete_records('smartch_stats_session', ['datecreated' => $timestamp]);

    echo '<h1>' . $date . ' ' . $timestamp . '</h1>';
    startDailyStats($timestamp);


    $dateInitiale = new DateTime($date);
    // Soustraire un jour
    $dateModifiee = $dateInitiale->sub(new DateInterval('P1D'));
    // Afficher la date modifiée
    echo $dateModifiee->format('Y-m-d');

    $url = new moodle_url('/theme/remui/views/dailytask.php?date=' . $dateModifiee->format('Y-m-d'));

    echo '<script>
        window.onload = function() {
            setTimeout(function() {
                window.location.href = "' . $url . '";
            }, 5000); 
        }
    </script>';
}
