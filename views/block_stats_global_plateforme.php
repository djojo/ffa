<?php


// =========== TIME SPENT + SESSION FILTERS================================= 
$filtersqlcourse = "";
$filtersqlcoursecategory = "";
if ($courseid) {
    // $filter = " AND c.";
    $filtersqlcoursecategory = ' AND c.id = ' . $courseid . ' ';
    $filtersqlcourse = ' AND g.courseid = ' . $courseid . ' ';
} else if ($categoryid) {
    $filtersqlcoursecategory = ' AND c.category = ' . $categoryid . ' ';
    $filtersqlcourse = ' AND c.category = ' . $categoryid . ' ';
}

//on va chercher le temps passé sur la période
$totaltimespent = 0;
$periodlogs = $DB->get_records_sql('SELECT al.id, al.timespent
FROM mdl_smartch_activity_log al
JOIN mdl_course c ON al.course = c.id
WHERE al.timestart > ' . $startdatetimestamp . '
AND al.timestart < ' . $enddatetimestamp . '
' . $filtersqlcoursecategory . '', null);
foreach ($periodlogs as $periodlog) {
    $totaltimespent += $periodlog->timespent;
}

//on va chercher le nombre d'apprenants
if ($courseid) {
    //on va chercher le nombre d'apprenants du cours
    $students = $DB->get_records_sql('SELECT DISTINCT ue.userid /* Distinct because one user can be enrolled multiple times */
    FROM mdl_enrol e
    JOIN mdl_user_enrolments ue ON ue.enrolid = e.id AND ue.timestart
    JOIN mdl_course c ON e.courseid = c.id AND ue.timestart
    WHERE c.id = ' . $courseid . '', null);
} else if ($categoryid) {
    //on va chercher le nombre d'apprenants de la catégorie
    $students = $DB->get_records_sql('SELECT DISTINCT ue.userid /* Distinct because one user can be enrolled multiple times */
    FROM mdl_enrol e
    JOIN mdl_user_enrolments ue ON ue.enrolid = e.id AND ue.timestart
    JOIN mdl_course c ON e.courseid = c.id AND ue.timestart
    WHERE c.category = ' . $categoryid . '', null);
} else {
    $students = $DB->get_records_sql('SELECT DISTINCT u.id
    FROM mdl_user u 
    JOIN mdl_role_assignments ra ON ra.userid = u.id
    JOIN mdl_role r ON r.id = ra.roleid
    WHERE r.shortname = "student"', null);
}

//BETWEEN ' . $startdatetimestamp . ' AND ' . $enddatetimestamp . '






//on va chercher le nombre de formateurs
$teachers = $DB->get_records_sql('SELECT DISTINCT u.id
    FROM mdl_user u
    JOIN mdl_role_assignments ra ON ra.userid = u.id
    JOIN mdl_role r ON r.id = ra.roleid
    WHERE (r.shortname = "smalleditingteacher"
    OR r.shortname = "editingteacher"
    OR r.shortname = "nonteacher"
    OR r.shortname = "teacher")', null);

//on va chercher le nombre de session en cours sur la période
$allsessions = $DB->get_records_sql('SELECT DISTINCT ss.id
FROM mdl_smartch_session ss
JOIN mdl_groups g ON g.id = ss.groupid
JOIN mdl_course c ON c.id = g.courseid
-- termine dans la période
WHERE (ss.enddate > ' . $startdatetimestamp . '
AND ss.enddate < ' . $enddatetimestamp . ')
-- commence dans la période
OR (ss.startdate > ' . $startdatetimestamp . '
AND ss.startdate < ' . $enddatetimestamp . ')
' . $filtersqlcourse . '
', null);

$content .=  '<div class="col-md-3" style="color:#00315a;text-align:center;margin:50px 0;">';
$content .= '<svg style="width:60px;margin:20px 0;color:#00315a;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
  </svg>';

$content .=  '<h3 style="color:#00315a;">' . count($students) . ' Apprenants</h3>';
$content .=  '</div>';

if ($courseid) {
    //les activités complétés
    $completed = $DB->get_record_sql('SELECT count(cc.id) as count
FROM mdl_course_modules_completion cc
JOIN mdl_user u ON u.id = cc.userid
JOIN mdl_role_assignments AS ra ON ra.userid = cc.userid
JOIN mdl_user_enrolments AS ue ON ra.userid = ue.userid 
JOIN mdl_role AS r ON ra.roleid = r.id
JOIN mdl_course_modules cm ON cm.id = cc.coursemoduleid 
JOIN mdl_course c ON c.id = cm.course
WHERE cc.completionstate = 1 
AND cc.timemodified > ' . $startdatetimestamp . '
AND cc.timemodified < ' . $enddatetimestamp . '
AND cm.course = ' . $courseid . '
' . $filtercompletion . '
', null);

    $content .=  '<div class="col-md-3" style="color:#00315a;text-align:center;margin:50px 0;border-right: 2px solid #00315a;;">';

    $content .= '<svg style="width:60px;margin:20px 0;color:#00315a;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
    </svg>
    ';

    $content .=  '<h3 style="color:#00315a;">' . $completed->count . ' Activités complétés</h3>';
    $content .=  '</div>';
} else if ($categoryid) {
    //les activités complétés
    $completed = $DB->get_record_sql('SELECT count(cc.id) as count
FROM mdl_course_modules_completion cc
JOIN mdl_user u ON u.id = cc.userid
JOIN mdl_role_assignments AS ra ON ra.userid = cc.userid
JOIN mdl_user_enrolments AS ue ON ra.userid = ue.userid 
JOIN mdl_role AS r ON ra.roleid = r.id
JOIN mdl_course_modules cm ON cm.id = cc.coursemoduleid 
JOIN mdl_course c ON c.id = cm.course
WHERE cc.completionstate = 1 
AND c.category = ' . $categoryid . '
' . $filtercompletion . '
', null);

    $content .=  '<div class="col-md-3" style="color:#00315a;text-align:center;margin:50px 0; border-right: 2px solid;">';

    $content .= '<svg style="width:60px;margin:20px 0;color:#00315a;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
    </svg>
    ';
    $content .=  '<h3 style="color:#00315a;">' . $completed->count . ' Activités complétés</h3>';
    $content .=  '</div>';
} else {


    $content .=  '<div class="col-md-3" style="color:#00315a;text-align:center;margin:50px 0;">';
    $content .= '<svg style="width:60px;margin:20px 0;color:#00315a;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
    </svg>
    ';

    $content .=  '<h3 style="color:#00315a;">' . count($teachers) . ' Formateurs</h3>';
    $content .=  '</div>';
}

$content .=  '<div class="col-md-3" style="color:#00315a;text-align:center;margin:50px 0;">';
$content .= '<svg style="width:60px;margin:20px 0;color:#00315a;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>
    ';
$content .=  '<h3 style="color:#00315a;">' . convert_to_string_time($totaltimespent) . ' passées</h3>';
$content .=  '</div>';

$content .=  '<div class="col-md-3" style="color:#00315a;text-align:center;margin:50px 0;">';
$content .= '<svg style="width:60px;margin:20px 0;color:#00315a;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
  </svg>
    ';
$content .=  '<h3 style="color:#00315a;">' . count($allsessions) . ' sessions en cours</h3>';
$content .=  '</div>';


// displayChartBar('Utilisateurs connectés sur ' . $course->fullname, $jsonDays, $jsonData);
// $content .= displayChartLine('Temps passé sur ' . $course->fullname, $jsonDays, $jsonData);
