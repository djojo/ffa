<?php

$totalteam = count($groups);

if ($totalteam > 1) {
    $s = 's';
}

$content .= '<h3 class="FFF-title1" style="display: flex;align-items: center;margin-top:50px;">
    
<svg style="opacity: 0.3;" id="leftteamicon" class="fff-icon" onclick="moveIconTeam(\'prev\')" width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path fill-rule="evenodd" clip-rule="evenodd" d="M22.7071 11.2929C23.0976 11.6834 23.0976 12.3166 22.7071 12.7071L16.4142 19L22.7071 25.2929C23.0976 25.6834 23.0976 26.3166 22.7071 26.7071C22.3166 27.0976 21.6834 27.0976 21.2929 26.7071L14.2929 19.7071C13.9024 19.3166 13.9024 18.6834 14.2929 18.2929L21.2929 11.2929C21.6834 10.9024 22.3166 10.9024 22.7071 11.2929Z" fill="#00315a"/>
    <rect x="1" y="1" width="36" height="36" rx="18" stroke="#00315a" stroke-width="2"/>
</svg>


<svg id="rightteamicon" class="fff-icon" onclick="moveIconTeam(\'next\')" width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.2929 26.7071C14.9024 26.3166 14.9024 25.6834 15.2929 25.2929L21.5858 19L15.2929 12.7071C14.9024 12.3166 14.9024 11.6834 15.2929 11.2929C15.6834 10.9024 16.3166 10.9024 16.7071 11.2929L23.7071 18.2929C24.0976 18.6834 24.0976 19.3166 23.7071 19.7071L16.7071 26.7071C16.3166 27.0976 15.6834 27.0976 15.2929 26.7071Z" fill="#00315a"/>
    <rect x="1" y="1" width="36" height="36" rx="18" stroke="#00315a" stroke-width="2"/>
</svg>

<span class="FFABlack FFF-Blue" style="margin-right:10px;">' . $totalteam . '</span><span style="letter-spacing:1px;" class="FFABlack FFF-Gold">groupe' . $s . '</span>  
</h3>';

//on va chercher les équipes
// $groups = $DB->get_records('groups', ['courseid' => $courseid]);
// $groups = $DB->get_records_sql('SELECT * FROM mdl_groups WHERE courseid = ' . $courseid . ' LIMIT 0, 4', null);





// $teams = array();

if ($totalteam == 0) {
    $content .= nothingtodisplay("Vous n'avez pas de groupe sur ce parcours");
}

$counterequipe = 0;

$content .= '<div class="fff-my-courses-caroussel">';
$content .= '<div class="fff-my-courses-caroussel-items" id="fff-teams" >';

$counter = 1;
foreach ($groups as $team) {



    if ($counterequipe == 0) {
        //on créé un nouvelle bande de 4
        $content .= '<div class="blockscroll">';
        $content .= '<div class="row">';
    }


    //on va chercher les membres de l'équipe
    // $teamates = $DB->get_records('groups_members', ['groupid' => $team->id], '', '*', 0, 6);

    $querysixmates = '
    SELECT u.id, u.firstname, u.lastname, r.shortname, r.id as roleid
       FROM mdl_role_assignments AS ra 
       LEFT JOIN mdl_user_enrolments AS ue ON ra.userid = ue.userid 
       LEFT JOIN mdl_role AS r ON ra.roleid = r.id 
       LEFT JOIN mdl_context AS c ON c.id = ra.contextid 
       LEFT JOIN mdl_enrol AS e ON e.courseid = c.instanceid AND ue.enrolid = e.id 
       LEFT JOIN mdl_user u ON u.id = ue.userid
		LEFT JOIN mdl_groups_members gm ON u.id = gm.userid
       WHERE gm.groupid = ' . $team->id . ' 
       AND e.courseid = ' . $courseid . '
       AND r.shortname = "student"
       ORDER BY u.lastname ASC
       LIMIT 0, 6
       ';
    $teamates = $DB->get_records_sql($querysixmates, null);

    $queryallmates = '
    SELECT u.id
       FROM mdl_role_assignments AS ra 
       LEFT JOIN mdl_user_enrolments AS ue ON ra.userid = ue.userid 
       LEFT JOIN mdl_role AS r ON ra.roleid = r.id 
       LEFT JOIN mdl_context AS c ON c.id = ra.contextid 
       LEFT JOIN mdl_enrol AS e ON e.courseid = c.instanceid AND ue.enrolid = e.id 
       LEFT JOIN mdl_user u ON u.id = ue.userid
		LEFT JOIN mdl_groups_members gm ON u.id = gm.userid
       WHERE gm.groupid = ' . $team->id . ' 
       AND e.courseid = ' . $courseid . '
       AND r.shortname = "student"
       ORDER BY u.lastname ASC
       
       ';
    $allmates = $DB->get_records_sql($queryallmates, null);
    $totalmates = count($allmates);
    $el['total'] = $totalmates;
    $el['teamates'] = $teamates;

    if ($team->id == $groupid) {
        $selectedcolor = '#3CAFE3';
    } else {
        $selectedcolor = 'transparent';
    }


    $content .= '<div class="col-md-6" style="border: 3px solid ' . $selectedcolor . ';padding: 0px 20px 20px 20px; margin-bottom: 20px; border-radius: 15px;">';

    $teamurl =  new moodle_url('/theme/remui/views/adminteam.php?return=course&teamid=' . $team->id);
    // $teamurl =  new moodle_url('/theme/remui/views/formation.php?id=' . $course->id) . '&groupid=' . $team->id . '#equipe';

    if ($el['total'] > 1) {
        $nbmembres = $el['total'] . ' membres';
    } else {
        $nbmembres = $el['total'] . ' membre';
    }

    //On va chercher la session du groupe
    $session = $DB->get_record_sql('SELECT ss.id, ss.startdate, ss.enddate
    FROM mdl_groups g
    JOIN mdl_smartch_session ss ON ss.groupid = g.id
    WHERE g.id = ' . $team->id, null);
    $datesession = 'Du  ' . userdate($session->startdate, '%d/%m/%Y') . ' au ' . userdate($session->enddate, '%d/%m/%Y');

    $content .= '
        <div class="row">
            <div class="col-md-12" style="display: flex;justify-content: space-between;padding: 20px 0;min-height: 60px;">

                <div style="max-width: 70%;">
                    <a href="' . $teamurl . '"><span class="fff-title-team">' . extraireNomEquipe($team->name) . '</span></a>
                    <div style="font-size: 0.8rem;">' . $datesession . '</div>
                </div>
                <div>
                    <span style="margin-right:10px;">' . $nbmembres . '</span>';
                    //si on est sur culture fédérale
                    $portail = getConfigPortail();
                    if($portail == "portailrh"){
                        //on va chercher la cohort
                        $cohort = $DB->get_record_sql('SELECT * 
                        FROM mdl_enrol
                        WHERE customint2 = ' . $team->id . '
                        AND courseid = ' . $courseid, null);
                        $content .= '<svg onclick="window.location.href=\'' . new moodle_url('/theme/remui/views/cohortmessage.php?cohortid=' . $cohort->customint1) . '\'" style="cursor:pointer;" width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 4L8.8906 9.2604C9.5624 9.70827 10.4376 9.70827 11.1094 9.2604L19 4M3 15H17C18.1046 15 19 14.1046 19 13V3C19 1.89543 18.1046 1 17 1H3C1.89543 1 1 1.89543 1 3V13C1 14.1046 1.89543 15 3 15Z" stroke="#0B427C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>';
                    }
                $content .= '</div>
            </div>
        </div>'; //end row header team


    $content .= '<div class="row equipemembers" style="min-height: 170px;">'; //teamates

    if (count($teamates) == 0) {
        $content .= '<h3 class="no_member_to_display">Il n\'y a pas de membre dans ce groupe...</h3>';
    }

    $counterteam = 0;

    foreach ($el['teamates'] as $mate) {

        $counterteam++;
        if ($counterteam == 4) {
            $counterteam = 0;
        }
        $user = $DB->get_record('user', ['id' => $mate->id]);
        if ($user) {
            // $courseprog = getCourseProgression($user->id, $courseid);
            $courseprog = getCompletionPourcent($courseid, $user->id);
        } else {
            $courseprog = 0;
        }

        if ($user->id == $userid) {
            $selectedcolor = '#3CAFE3';
        } else {
            $selectedcolor = 'transparent';
        }

        $urluser = new moodle_url('/theme/remui/views/adminteam.php?return=course&teamid=' . $team->id) . '&userid=' . $user->id . '#selected-' . $user->id;
        // $urluser = new moodle_url('/theme/remui/views/formation.php?return=course&id=' . $courseid . '&userid=' . $user->id) . '#equipe\

        $content .= '<div class="col-sm-12 col-md-6 col-lg-4" style="border: 3px solid ' . $selectedcolor . ';padding: 10px; margin-bottom: 20px; border-radius: 15px;">
                    <div onclick="window.location.href=\'' . $urluser . '\'" style="cursor:pointer;display: flex;justify-content: space-between;width: 100%;"> 
                        <div>
                            <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="56" height="56" rx="4.2" fill="#EDF2F7"/>
                                <path d="M34.5354 20.2707C34.5354 23.6857 31.767 26.4541 28.3521 26.4541C24.9371 26.4541 22.1688 23.6857 22.1688 20.2707C22.1688 16.8558 24.9371 14.0874 28.3521 14.0874C31.767 14.0874 34.5354 16.8558 34.5354 20.2707Z" stroke="#CBD5E0" stroke-width="3.09167" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M28.3521 31.0916C22.3759 31.0916 17.5312 35.9362 17.5312 41.9124H39.1729C39.1729 35.9362 34.3283 31.0916 28.3521 31.0916Z" stroke="#CBD5E0" stroke-width="3.09167" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div style="margin-left: 10px;width: 100%;">';
        $matenamestring =  $user->firstname . '<br>' . $user->lastname;
        if (strlen($user->lastname) > 15 || strlen($user->firstname) > 15 || strlen($user->firstname . $user->lastname) > 30) {
            $content .= '<div style="line-height: 17px;" class="matename FFARegular" style="height: 50px;">
                                ' . $matenamestring . '
                            </div>';
        } else {
            $content .= '<div class="matename FFARegular" style="height: 50px;">
                                ' . $matenamestring . '
                            </div>';
        }

        $content .= '<div class="smartch_progress_bar_box" style="width: 100%;">
                                <div class="smartch_progress_bar_mini">
                                    <div class="smartch_progress_bar_number"></div>
                                    <div class="smartch_progress_bar_gain" style="width:' . $courseprog . '% !important;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>'; //sm 12
    }

    $content .= '</div>'; //row teamates

    $content .= '
        <div class="row" style="margin-top:20px;">
            <div class="col-md-12">
                <a class="smartch_btn" href="' . new moodle_url('/theme/remui/views/adminteam.php?return=course&teamid=' . $team->id . '#group') . '">Voir plus</a>
            </div>
        </div>';

    $content .= '</div>'; //end md6 block equipe

    // $content .= "<h4>" . $counter . "/ " . $totalteam . " test - " . $counterequipe . "</h4>";

    $counterequipe++;
    if ($counterequipe == 4 || $totalteam == $counter) {
        $content .= '</div>'; //row
        $content .= '</div>'; //row block scroll
        $counterequipe = 0;
    }
    $counter++;
}

$content .= '</div>'; //fff-my-courses-caroussel-items
$content .= '</div>'; //fff-my-courses-caroussel

// le BUG !!!!
// if ($totalteam != 0) {
//     $content .= '</div>';
// }

echo '<script>
    var positionNextIconTeam = 0;

    var nbgroups = ' . $totalteam . ';
    var maxright = Math.floor(nbgroups/4);

    function moveIconTeam(move){
        
        if(move == "next"){
            //alert("next")
            if(positionNextIconTeam<maxright){
                positionNextIconTeam++;
                document.getElementById(\'fff-teams\').scrollBy({top: 0, left: 500, behavior: \'smooth\'});
            } else {
                //alert(" on est à la fin")
            }
        } else if(positionNextIconTeam > 0){
            //alert("prev")
            document.getElementById(\'fff-teams\').scrollBy({top: 0, left: -500, behavior: \'smooth\'});
            positionNextIconTeam--;
        }
        if(positionNextIconTeam == 0){
            document.getElementById(\'leftteamicon\').style.opacity=0.3; 
        } else {
            document.getElementById(\'leftteamicon\').style.opacity=1; 
        }
        
        if(positionNextIconTeam == maxright){
            document.querySelector("#rightteamicon").style.opacity = 0.3;
        } else {
            document.querySelector("#rightteamicon").style.opacity = 1;
        }
        
    }

    
    if(positionNextIconTeam == maxright){
        //alert("on est déjà à la fin")
        window.addEventListener("load", function() { 
            document.querySelector("#rightteamicon").style.opacity = 0.3;
        }, false);
        
    }
    
    
    
    
</script>';
