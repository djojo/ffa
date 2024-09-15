<?php

$content .= '

<style>

img.smartch_background_header {
    height: 550px !important;
}

#page{
    background:transparent !important;
}

#topofscroll {
    background: transparent !important;
    margin-top: 0px !important;
}

</style>';



$content .= '<div class="row">';
$content .= '<div class="col-12">';
$content .= '<div class="fff-course-box-info">';

$content .= '<div class="row">';

$content .= '<div class="col-xs-12 col-md-12 col-lg-6">';

if($rolename == "student"){
    //on va chercher les groupes session de l'utilisateur
    $groups = $DB->get_records_sql('SELECT g.id, g.name FROM mdl_groups g
    JOIN mdl_groups_members gm ON gm.groupid = g.id
    WHERE gm.userid = ' . $USER->id . ' AND g.courseid = ' . $course->id, null);

    $group = null;
    //si l'utilisateur est dans un groupe
    if (count($groups) > 0) {
        $sessiondate = "";
        //on parcours les groupes
        foreach ($groups as $group) {
            $teamid = $group->id;
            //On va chercher l'intervenant
            $coach = getResponsablePedagogique($group->id, $course->id);

            //on va chercher la session 
            $session = $DB->get_record('smartch_session', ['groupid' => $group->id]);
            // var_dump($session);
            // var_dump($group->id, $onecourse->id);

            if ($session && $session->startdate && $session->enddate) {
                $sessiondate .= '<div>Session du ' . userdate($session->startdate, '%d/%m/%Y') . ' au ' . userdate($session->enddate, '%d/%m/%Y') . '</div>';
                // $sessiondate .= '<div>Intervenant : ' . $coach[0] . '</div>';
            }

        }

        // var_dump($coach);
        // die();
        //il y a un responsable pedagogique (coach)
        if($coach[1]){

        $urlmessageresponsable = new moodle_url('/theme/remui/views/users/message.php') . '?userid=' . $coach[1]->id . '&returnurl='.$PAGE->url;

        $content .= '<div class="fff-course-box-info-details">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="24" cy="24" r="24" fill="#E2E8F0"/>
                            <path d="M28 19C28 21.2091 26.2091 23 24 23C21.7909 23 20 21.2091 20 19C20 16.7909 21.7909 15 24 15C26.2091 15 28 16.7909 28 19Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M24 26C20.134 26 17 29.134 17 33H31C31 29.134 27.866 26 24 26Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div style="margin-left: 25px;">
                            <h3 class="FFABold FFF-Blue" style="font-size:16px;display:flex;align-items:center;">
                                    '.$coach[0].'
                                    <a href="'.$urlmessageresponsable.'">
                                        <svg class="ml-2" width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 4L8.8906 9.2604C9.5624 9.70827 10.4376 9.70827 11.1094 9.2604L19 4M3 15H17C18.1046 15 19 14.1046 19 13V3C19 1.89543 18.1046 1 17 1H3C1.89543 1 1 1.89543 1 3V13C1 14.1046 1.89543 15 3 15Z" stroke="#00315a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                            </h3>
                                <h5 class="FFF-Blue" style="font-size:12px;">Intervenant(e)</h5>
                        </div>
                    </div>';
        }
    }
}



$content .= '<div class="fff-course-box-info-details3">';

//on recupère le champs personnalisé duration
$duration = getCourseCustom($course->id, "duration");
if(!empty($duration)){
$content .= '<div style="display:flex;align-items:center;">
                <svg style="width:20px;" class="mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span class="mr-4 FFARegular" style="font-size:rem;">'.$duration.'h</span>
            </div>';
}

//on recupère le champs personnalisé diplome
$diplome = getCourseCustom($course->id, "type");
if(!empty($diplome)){
$content .= '<div style="display:flex;align-items:center;">
                <svg style="width:20px;" class="mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                </svg>
                <span class="mr-4 FFARegular" style="font-size:1rem;">' . $diplome . '</span>
            </div>';
}

//on va chercher la session si on est etudiant
if($rolename == "student"){
    if($sessiondate){
        $content .= '<div style="display:flex;align-items:center;">
                    <svg style="width:20px;" class="mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                    </svg>
                    <span class="mr-4 FFARegular">' . $sessiondate . '</span>
                </div>';
    }
}

//ligue
// if(false){
                
// $content .= '
//                 {{#ligue}}
//                 <div class="fff-course-box-info-details">
//                     {{! <svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
//                         <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM0 10C0 4.47715 4.47715 0 10 0C15.5228 0 20 4.47715 20 10C20 15.5228 15.5228 20 10 20C4.47715 20 0 15.5228 0 10ZM10 5C10.5523 5 11 5.44772 11 6V9.58579L13.7071 12.2929C14.0976 12.6834 14.0976 13.3166 13.7071 13.7071C13.3166 14.0976 12.6834 14.0976 12.2929 13.7071L9.29289 10.7071C9.10536 10.5196 9 10.2652 9 10V6C9 5.44772 9.44771 5 10 5Z" fill="#00315a"/>
//                     </svg> }}
//                     <svg class="mr-2 smartch_svg" width="24" height="24"  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
//                         <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
//                     </svg>

//                     <span class="mr-4 FFABold" style="font-size:12px;">{{ligue}}</span>
//                 </div>
//                 {{/ligue}}
//                ';
// }

$content .= '</div>';

            
           
                

//course summary
if($course->summary){
    $content .= '<div id="smartch_summary" class="FFARegular FFF-Blue" style="font-size: 14px; !important;max-height: 180px; overflow: hidden;">'.html_entity_decode($course->summary).'</div>';
    if(strlen(strip_tags($course->summary))> 800){
        $content .= '
        <div style="text-align:center;">
            <div id="smartch_summary_more" onclick="this.style.display=\'none\';document.querySelector(\'#smartch_summary_less\').style.display=\'block\';document.querySelector(\'#smartch_summary\').style.maxHeight=\'none\';" style="cursor:pointer;">Voir plus</div>
            <div id="smartch_summary_less" onclick="this.style.display=\'none\';document.querySelector(\'#smartch_summary_more\').style.display=\'block\';document.querySelector(\'#smartch_summary\').style.maxHeight=\'180px\';" style="cursor:pointer;display:none;">Voir moins</div>
        </div>';
    }
}

      
$content .= '</div>';//col-6


$content .= '<div class="col-xs-12 col-md-12 col-lg-6" style="margin-bottom:50px;">';
require_once('./details_stats.php');
$content .= '</div>';//col-6

$content .= '</div>'; //row

$content .= '</div>'; //fff-box-info
$content .= '</div>'; //col-12
$content .= '</div>'; //row