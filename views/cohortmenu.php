<?php


$content .= '<div class="row">
<div class="col-md-12">
<h1 style="letter-spacing:1px;max-width:70%;cursor:pointer;" class="smartch_title FFABold FFF-Blue">Groupe '.$cohort->name.'</h1>
</div>
</div>';

//le nombre de membres
$members = $DB->get_record_sql('SELECT COUNT(*) count 
FROM mdl_cohort co
JOIN mdl_cohort_members cm ON cm.cohortid = co.id
JOIN mdl_user u ON u.id = cm.userid
WHERE co.id = ' . $cohortid . '
AND u.deleted = 0 AND u.suspended = 0', null);

//le nombre de formations associés
$coursestotal = $DB->get_record_sql('SELECT COUNT(*) count 
FROM mdl_enrol e
JOIN mdl_cohort co ON e.customint1 = co.id
JOIN mdl_course c ON c.id = e.courseid
JOIN mdl_smartch_session ss ON ss.groupid = e.customint2
WHERE co.id = ' . $cohortid . '', null);


$content .= '<div class="row">
    <div class="col-md-12" style="display:flex;">
        <div onclick="location.href=\'' . new moodle_url('/theme/remui/views/cohortmembers.php?cohortid='.$cohortid) . '\'" class="smartch_box_link">
            <svg style="width: 40px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
            </svg>';

            if($members->count > 1){
                $content .= '<div>'.$members->count.' membres</div>';
            } else {
                $content .= '<div>'.$members->count.' membre</div>';
            }
           
        $content .= '
        </div>
        <div onclick="location.href=\'' . new moodle_url('/theme/remui/views/cohort.php?cohortid='.$cohortid) . '\'" class="smartch_box_link">
            <svg style="width: 40px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
            </svg>';

            if($coursestotal->count > 1){
                $content .= '<div>'.$coursestotal->count.' formations</div>';
            } else {
                $content .= '<div>'.$coursestotal->count.' formation</div>';
            }
           
        $content .= '</div>
        <div style="display:none;" onclick="location.href=\'' . new moodle_url('/theme/remui/views/editcohort.php?cohortid='.$cohortid) . '\'" class="smartch_box_link">
            <svg style="width: 40px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
            <div>Modifier le groupe</div>
        </div>
        <div onclick="location.href=\'' . new moodle_url('/theme/remui/views/cohortmessage.php?cohortid='.$cohortid) . '\'" class="smartch_box_link">
            <svg style="width: 40px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
            </svg>
            <div>Envoyer un message</div>
        </div>
    </div>
</div>';