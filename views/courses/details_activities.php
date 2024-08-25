<?php

foreach ($sections as $section) {

    $plannings = [];

    if ($session) {
        //$content .= "session: " . $session->id . $section->id;
        $plannings = getSectionPlannings($courseid, $session->id, $section->id);
        // var_dump($plannings);
        // var_dump($plannings);
        // $content .= "<div>Il y a " . count($plannings) . ' session plannings pour la section ' . $section->id . '</div> ';


        //les activitées planning déjà affiché
        $countplanning = 1;
        $allsmartchplanning = count($plannings);
        //on compte le nombre de planning de la section dans le ruban
        $sectionplannings = getSectionActivityPlannings($courseid, $session->id, $section->id);
        //le nombre d'activité planning de la section
        $countactivityplanning = count($sectionplannings);
        // $content .= "<div>Il y a " . count($sectionplannings) . ' activité plannings pour la section ' . $section->id . '</div> ';

        // $content .= '<div>' . $allsmartchplanning . ' --->' . $countactivityplanning . '</div>';

    }


    $tableau = explode(',', $section->sequence);
    $tableau = array_map('intval', $tableau);
    // $content .= "On affiche la section ";

    foreach ($tableau as $val) {
        // $content .= 'activité' . $val;
        // $activity = getSectionActivity($val);

        $targetId = $val; // L'ID que vous recherchez

        $foundActivity = null;
        $activity = null;

        foreach ($activities as $activityy) {
            // var_dump($activityy->id . '||' .  $targetId . '/////');
            if ($activityy->id == $targetId) {
                // var_dump($activityy->activityid . $targetId . '/////');
                $activity = $activityy;
                break; // Sortir de la boucle dès que l'élément est trouvé
            }
        }


        //si il y a une session donc peut etre des plannings
        if ($activity) {
            if ($activity->activitytype == "face2face") {


                if ($plannings) {

                    //on parcoure les smartch plannings de la section
                    foreach ($plannings as $planning) {
                        // $content .= '<div>On passe dans le face2face plannings</div> ';
                        //var_dump($planning);
                        // $content .= "<div>--------------</div>";
                        // $content .= '<div>section id -> ' . $sectionid . '</div> ';
                        // $content .= '<div>planning section id -> ' . $planning->sectionid . '</div> ';
                        //$content .= "<div>Planing: " . userdate($planning->startdate, '%d/%m/%Y') . ' ' . userdate($planning->startdate, '%H:%M') . '</div> ';
                        if ($planning->sectionid == $section->id) {
                            // $content .= '<div>On passe dans LA RECHERCHE</div> ';

                            $planningTrouve = $planning;
                            // $content .= "<div>-------planning du ruban -> " . userdate($planning->startdate, '%d/%m/%Y') . ' ' . userdate($planning->startdate, '%H:%M') . "-------</div>";
                            //on supprime l'objet du tableau
                            unset($plannings[$planning->id]);

                            break; // Sortir de la boucle une fois que le planning est trouvé
                        }
                    }
                    // if (isset($planningTrouve)) {
                    //     // Faites quelque chose avec $objetTrouve
                    //     //var_dump($objetTrouve);
                    // }
                    // var_dump('DUMP');
                    // $content .= "<div>Il reste " . count($plannings) . ' plannings</div> ';


                    // $content .= '<div>--------------------------</div> ';
                    // $indexToRetrieve = 1;

                    // foreach ($plannings as $key => $value) {
                    //     if ($indexToRetrieve == 0) {
                    //         $planning = $value;
                    //         break;
                    //     }
                    //     $indexToRetrieve--;
                    // }

                    // var_dump($planning);
                    if ($planningTrouve  && $countplanning <= $countactivityplanning) {

                        // $content .= '->' . $countplanning . '->' . $allsmartchplanning;
                        $countplanning++;

                        if ($planningTrouve->startdate > time()) {
                            $completion = '<div style="background:#009ce0;" class="smartch_pastille">Planifiée</div>';
                        } else if ($planningTrouve->startdate < time() && $planningTrouve->enddate > time()) {
                            $completion = '<div style="background:#E50127;" class="smartch_pastille">En cours</div>';
                        } else {
                            $completion = '<div style="background:#3CAFE3;" class="smartch_pastille">Passée</div>';
                        }

                        $planningdate = 'Session du ' . userdate($planningTrouve->startdate, '%d/%m/%Y') . ' ' . userdate($planningTrouve->startdate, '%H:%M') . ' - ' . userdate($planningTrouve->enddate, '%H:%M') . '';

                        $type = "";
                        // $type = "Activité présentielle";

                        $geforplanningid = $planningTrouve->geforplanningid;
                        if ($geforplanningid) {
                            $geforplanningid = ' - (' . $geforplanningid . ')';
                        }
                        //On va chercher les formateurs
                        $formateurs = getPlanningFormateurs($planningTrouve->id);




                        // var_dump($formateurs);
                        $content .= '<div class="module-' . $activity->moduleid . ' activity-element" id="activity-' . $activity->moduleid . '-' . $activity->activityid . '">';

                        $content .= '<div style="display: flex;">';
                        // $content .= '<div style="display:none;" class="course_activity_icon">
                        //                 <svg class="smartchactivityicon mr-4" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        //                     <path fill-rule="evenodd" clip-rule="evenodd" d="M6 0C6.55228 0 7 0.447715 7 1V2H13V1C13 0.447715 13.4477 0 14 0C14.5523 0 15 0.447715 15 1V2H17C18.6569 2 20 3.34315 20 5V17C20 18.6569 18.6569 20 17 20H3C1.34315 20 0 18.6569 0 17V5C0 3.34315 1.34315 2 3 2H5V1C5 0.447715 5.44772 0 6 0ZM5 4H3C2.44772 4 2 4.44772 2 5V17C2 17.5523 2.44772 18 3 18H17C17.5523 18 18 17.5523 18 17V5C18 4.44772 17.5523 4 17 4H15V5C15 5.55228 14.5523 6 14 6C13.4477 6 13 5.55228 13 5V4H7V5C7 5.55228 6.55228 6 6 6C5.44772 6 5 5.55228 5 5V4ZM4 9C4 8.44772 4.44772 8 5 8H15C15.5523 8 16 8.44772 16 9C16 9.55229 15.5523 10 15 10H5C4.44772 10 4 9.55229 4 9Z" fill="#fff"/>
                        //                 </svg>
                        //             </div>';
                        $content .= '<div>
                                        <div style="font-family: \'FFABold\';font-size: 16px;">' . $planningdate . '</div>
                                        <div>' . $completion . '</div>
                                        <div>' . $type . '</div>
                                        ';
                        if ($sessionadress != "") {
                            $content .= '<div >
                                            <svg class="mr-2" width="18" height="21" viewBox="0 0 18 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.9497 4.05025C11.2161 1.31658 6.78392 1.31658 4.05025 4.05025C1.31658 6.78392 1.31658 11.2161 4.05025 13.9497L8.29374 18.1932C8.68398 18.5835 9.31589 18.5836 9.70669 18.1928L13.9497 13.9497C16.6834 11.2161 16.6834 6.78392 13.9497 4.05025ZM2.63604 2.63604C6.15076 -0.87868 11.8492 -0.87868 15.364 2.63604C18.8787 6.15076 18.8787 11.8492 15.364 15.364L11.8617 18.8662C11.8303 18.8976 11.8754 18.8525 11.844 18.8839L11.1209 19.607C9.94961 20.7783 8.05137 20.7793 6.87952 19.6074L2.63604 15.364C-0.87868 11.8492 -0.87868 6.15076 2.63604 2.63604ZM9 7C7.89543 7 7 7.89543 7 9C7 10.1046 7.89543 11 9 11C10.1046 11 11 10.1046 11 9C11 7.89543 10.1046 7 9 7ZM5 9C5 6.79086 6.79086 5 9 5C11.2091 5 13 6.79086 13 9C13 11.2091 11.2091 13 9 13C6.79086 13 5 11.2091 5 9Z" fill="#00315a"/>
                                            </svg>
                                            <span style="text-transform:uppercase;font-family: \'FFARegular\';font-size: 14px;">' . $sessionadress . '</span>
                                        </div>';
                        }
                        $content .= '
                                    </div>';
                        $content .= '</div>';


                        // $content .= '
                        //                                 <div class="fff-course-box-info-details">

                        //                                     <svg class="smartchactivityicon mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        //                                         <path fill-rule="evenodd" clip-rule="evenodd" d="M6 0C6.55228 0 7 0.447715 7 1V2H13V1C13 0.447715 13.4477 0 14 0C14.5523 0 15 0.447715 15 1V2H17C18.6569 2 20 3.34315 20 5V17C20 18.6569 18.6569 20 17 20H3C1.34315 20 0 18.6569 0 17V5C0 3.34315 1.34315 2 3 2H5V1C5 0.447715 5.44772 0 6 0ZM5 4H3C2.44772 4 2 4.44772 2 5V17C2 17.5523 2.44772 18 3 18H17C17.5523 18 18 17.5523 18 17V5C18 4.44772 17.5523 4 17 4H15V5C15 5.55228 14.5523 6 14 6C13.4477 6 13 5.55228 13 5V4H7V5C7 5.55228 6.55228 6 6 6C5.44772 6 5 5.55228 5 5V4ZM4 9C4 8.44772 4.44772 8 5 8H15C15.5523 8 16 8.44772 16 9C16 9.55229 15.5523 10 15 10H5C4.44772 10 4 9.55229 4 9Z" fill="#00315a"/>
                        //                                     </svg>
                        //                                     <span>' . $planningdate . '</span>
                        //                                     <br/>
                        //                                     ' . $completion . '
                        //                                 </div>
                        //                                 ';

                        //les formateurs
                        if (count($formateurs) > 0) {
                            $content .= '<div style="display: flex;align-items: center;">';

                            $content .= '<div class="course_activity_icon" style="font-family: "FFARegular";font-size: 12px;">Formateur(s)</div>';

                            $content .= '<div class="fff-course-box-info-details">
                                            <span class="FFABold" style="font-size: 14px;">';
                            $nbf = 0;
                            $formateurid = 0;
                            foreach ($formateurs as $formateur) {
                                $nbf++;
                                if ($formateurid != $formateur->id) {
                                    $content .= $formateur->firstname . ' ' . $formateur->lastname;
                                    if (count($formateurs) != $nbf) {
                                        $content .= ', ';
                                    }
                                    $formateurid = $formateur->id;
                                }
                            };
                            $content .= '</span></div>';
                            $content .= '</div>'; //flex
                        }

                        $content .= '
                                                        <hr>
                                                    </div>';
                    }
                }
            } else {

                //pour les dossiers saint pierre &amp;...
                // var_dump($activity->activityname);
                if (!$activity->activityname) {
                    continue;
                }


                $completion = "";

                //si le rôle est etudiant
                if ($rolename == "student" || $userid) {
                    //on va chercher la complétion de l'activité
                    if ($activity->moduleid) {
                        // $completion = getActivityCompletionStatus($activity->moduleid, $activity->activityid);
                        //on ajoute l'userid si il y en a un de sélectionné
                        $completion = getActivityCompletionStatus($val, $userid, $activity->activitytype);
                    }
                }

                $displayactivity = true;
                $type = "";

                if ($activity->activitytype == "scorm" || $activity->activitytype == "h5pactivity") {
                    $type = "e-Learning";
                } else if ($activity->activitytype == "assign") {
                    $type = "devoir";
                } else if ($activity->activitytype == "resource") {
                    $type = "fichier";
                } else if ($activity->activitytype == "smartchfolder") {
                    $type = "dossier de ligue";
                    //on vire la completion
                    $completion = "";
                } else if ($activity->activitytype == "folder") {

                    $requestfolder = "SELECT COUNT(*) count
                    FROM mdl_files
                    WHERE contextid = (SELECT id FROM mdl_context WHERE contextlevel = 70 AND instanceid = " . $activity->id . ")";

                    $testfolder = $DB->get_record_sql($requestfolder, null);

                    if ($testfolder->count == 0) {
                        //le dossier est vide et on ne l'affiche pas
                        $displayactivity = false;
                    }
                    $type = "dossier";
                    //on vire la completion
                    $completion = "";
                } else if ($activity->activitytype == "feedback") {
                    $type = "sondage";
                } else if ($activity->activitytype == "quiz") {
                    $type = "test";
                } else {
                    $type = $activity->activitytype;
                }

                if ($displayactivity) {
                    $content .= '<div class="module-' . $activity->moduleid . ' activity-element" id="activity-' . $activity->moduleid . '-' . $activity->activityid . '">';

                    //l'url de l'activité
                    $urlactivity = new moodle_url('/mod/' . $activity->activitytype . '/view.php?id=' . $activity->id);

                    //si l'activité est un quiz
                    if ($activity->activitytype == "quiz"){
                        //si l'utilisateur est un étudiant
                        if($rolename == 'student'){

                            //On regarde si la formation est de type certification
                            $coursetype = getCourseType($courseid);
                            // $content .= '<h1>'.$coursetype.'</h1>';
                            if($coursetype == "Certifications Fédérales"){
                                //on reset car dans une boucle foreach
                                $usertotalsessions = [];
                                $useractualsessions = [];
                                $userattempts = [];
                                $attemptshtml = "";
                                //on regarde le nombre de session de l'apprenant
                                $usertotalsessions = getUserSessions($courseid, $USER->id);
                                //on regarde le nombre de session actuelle de l'apprenant
                                $useractualsessions = getActualUserSessions($courseid, $USER->id);
                                // var_dump(count($useractualsessions));
                                // var_dump('//////////////////////////////////////');
                                // var_dump(count($usertotalsessions));
                                // var_dump('//////////////////////////////////////');
                                //on regarde le nombre de tentative de l'apprenant
                                $userattempts = getUserQuizAttempts($activity->id, $USER->id);
                                // var_dump(count($userattempts));
                                
                                
                                
                                $attemptshtml .= '<div style="display:flex;margin:5px 0;color:white;">';
                                $attemptshtml .= '<div style="border:1px solid;padding:5px 10px;width:250px; text-align:center;background:#00315a;border-radius:5px;">Date de passage</div>';
                                $attemptshtml .= '<div style="border:1px solid;padding:5px 10px;margin-left:10px;width:130px;text-align:center;background:#00315a;border-radius:5px;">Score</div>';
                                $attemptshtml .= '</div>';
                                //on lui affiche les résultats de ses tentatives
                                foreach($userattempts as $userattempt){

                                    //le score
                                    $grade = number_format($userattempt->rawgrade, 2, '.', '');
                                    //le score max
                                    $rawgrademax = $userattempt->rawgrademax;

                                    if(!empty($rawgrademax)){
                                        $score = $grade . '/' . number_format($rawgrademax, 2, '.', '');
                                    } else {
                                        $score = $grade;
                                    }
                                    $attemptshtml .= '<div style="display:flex;margin-bottom:5px;border-bottom:1px solid;">';
                                    $attemptshtml .= '<div style="padding:5px 10px;width:250px;text-align:center;border-radius:5px;">'.userdate($userattempt->timemodified).'</div>';
                                    $attemptshtml .= '<div style="padding:5px 10px;margin-left:10px;width:130px;text-align:center;border-radius:5px;">'.$score.'</div>';
                                    $attemptshtml .= '</div>';

                                
                                //si il y a moins de tentative que de session actuelle
                                // et qu'il ya une session en cours
                                if(count($usertotalsessions) > count($userattempts) && count($useractualsessions) > 0){
                                    //on lui laisse faire une autre tentative
                                } else {
                                    $urlactivity = "";
                                }
                                // $urlactivity = new moodle_url('/mod/' . $activity->activitytype . '/view.php?id=' . $activity->id);
                                    
                            }
                                
                            }
                        }
                        
                    }

                    
                    

                    $content .= '<div style="display: flex;justify-content:space-between;">';
                    // if ($activity->activitytype != "face2face") {
                    //     $content .= '<a href="' . $urlactivity . '">';
                    // }
                    // $content .= '<div class="course_activity_icon">
                    //                         <svg style="padding:5px;" class="smartchactivityicon mr-4" width="18" height="21"  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white"">
                    //                             <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    //                             <path stroke-linecap="round" fill="white" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112z" />
                    //                         </svg>
    
                    //                     </div>';
                                        
                            $content .= '<div>';
                                            if ($activity->activitytype != "face2face") {
                                                $content .= '<a href="' . $urlactivity . '">';
                                            }
                                            $content .= '<div class="FFABold fff-name-activity">' . $activity->activityname . '</div>';
                                            if ($activity->activitytype != "face2face") {
                                                $content .= '</a>'; //flex
                                            }
                                            $content .= '<div class="smartchmoduletype" style="font-size: 0.8rem;">' . $type . '</div>';
                                    
                                        $content .= '</div>';
                                            //on affiche les tentatives
                                            if($activity->activitytype == "quiz"){
                                                $content .= $attemptshtml;
                                                //on affiche la complétion si la certification est terminé
                                                // (il y a plus ou autant de attempts que de session)
                                                if(count($useractualsessions) >= count($userattempts)){
                                                    $content .= '<div>' . $completion . '</div>';
                                                }
                                            } else {
                                                $content .= '<div>' . $completion . '</div>';
                                            }
                                            
                                            
                    
                    $content .= '</div>'; //flex
                    $content .= '<hr/>';
                    
                    $content .= '<div>';

                    $content .= '</div>';
                    $content .= '</div>';
                }
                // $content .= 'Activité de type ' . $activity->activitytype;
            }
        }
    }
}