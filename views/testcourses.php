<?php

require_once(__DIR__ . '/../../../config.php');

global $PAGE, $USER, $DB;

$userid = optional_param('userid', null, PARAM_INT);

// Validation for context is needed.
// $context = \context_system::instance();
// self::validate_context($context);

$mycourses = array();

//On va chercher le rôle le plus haut de l'utilisateur
$rolename = "";
$assignments = $DB->get_records('role_assignments', ['userid' => $USER->id]);
foreach ($assignments as $assignment) {
    $role = $DB->get_record('role', ['id' => $assignment->roleid]);
    //on renvoi le rôle le plus haut
    if ($role->shortname == "super-admin") {
        $rolename = "super-admin";
    } else if ($role->shortname == "manager") {
        if ($rolename != "super-admin") {
            $rolename = "manager";
        }
    } else if ($role->shortname == "smalleditingteacher") {
        if ($rolename != "super-admin" && $rolename != "manager") {
            $rolename = "smalleditingteacher";
        }
    } else if ($role->shortname == "editingteacher") {
        if ($rolename != "super-admin" && $rolename != "manager" && $rolename != "smalleditingteacher") {
            $rolename = "editingteacher";
        }
    } else if ($role->shortname == "teacher") {
        if ($rolename != "super-admin" && $rolename != "manager" && $rolename != "smalleditingteacher" && $rolename != "editingteacher") {
            $rolename = "teacher";
        }
    } else if ($role->shortname == "noneditingteacher") {
        if ($rolename != "super-admin" && $rolename != "manager" && $rolename != "teacher" && $rolename != "smalleditingteacher" && $rolename != "editingteacher") {
            $rolename = "noneditingteacher";
        }
    } else if ($role->shortname == "student") {
        if ($rolename != "super-admin" && $rolename != "manager" && $rolename != "teacher" && $rolename != "noneditingteacher" && $rolename != "smalleditingteacher" && $rolename != "editingteacher") {
            $rolename = "student";
        }
    }
}


$allsessionsgeo = $DB->get_records_sql('SELECT DISTINCT g.id as groupid, ss.id, ss.startdate, ss.enddate
                    FROM mdl_groups g
                    JOIN mdl_groups_members gm ON gm.groupid = g.id
                    JOIN mdl_smartch_session ss ON ss.groupid = g.id
                    WHERE gm.userid = ' . $USER->id . '', null);
                    
var_dump($allsessionsgeo);
die();


if ($rolename == "super-admin" || $rolename == "manager") {
    //on va chercher tous les cours
    $querycourses = 'SELECT c.id, c.fullname, c.category FROM mdl_course c
    WHERE c.format != "site" AND c.visible = 1';
} else {
    //on va chercher les cours de l'utilisateur
    $querycourses = 'SELECT c.id, c.fullname, c.category FROM mdl_course c
    JOIN mdl_role_assignments ra ON ra.userid = ' . $USER->id . '
    JOIN mdl_context ct ON ct.id = ra.contextid AND c.id = ct.instanceid
    JOIN mdl_role r ON r.id = ra.roleid
    WHERE c.format != "site" AND c.visible = 1
    ORDER BY
    CASE 
        WHEN c.fullname LIKE "%Aide%" THEN 0
        ELSE 1
    END,
    c.fullname DESC';
}
$courses = $DB->get_records_sql($querycourses, null);

//on regarde si on est en formation gratuite
$freecat = $DB->get_record_sql('SELECT * from mdl_course_categories WHERE name = "Formation gratuite"', null);

foreach ($courses as $course) {

    $multiplesession = false;
    $displaycourse = true;
    $certification = false;
    $el['notavailable'] = false;

    $imgcourse = "";
    //On va chercher l'image du cours
    $course2 = new core_course_list_element($course);
    foreach ($course2->get_course_overviewfiles() as $file) {
        if ($file->is_valid_image()) {
            $imagepath = '/' . $file->get_contextid() .
                '/' . $file->get_component() .
                '/' . $file->get_filearea() .
                $file->get_filepath() .
                $file->get_filename();
            $imageurl = file_encode_url(
                $CFG->wwwroot . '/pluginfile.php',
                $imagepath,
                false
            );

            $imgcourse = $imageurl;
            break;
        }
    }
    if ($imgcourse == "") {
        $imgcourse = $CFG->wwwroot . '/theme/remui/pix/background.jpeg';
    }
    $el['img'] = $imgcourse;
    
    //si on est sur une formation gratuite
    if ($course->category == $freecat->id) {
        $freecategory = true;
    } else {
        $freecategory = false;
    }

    $el['fullname'] = $course->fullname;

    //on va chercher le type de cours
    $category = $DB->get_record('course_categories', ['id' => $course->category]);
    if ($category->name == "Formation gratuite") {
        if ($rolename == "super-admin" || $rolename == "manager" || $rolename == "smalleditingteacher") {
            $el['category'] = "";
        } else{
            $el['date1'] = '';
            $el['date2'] = '';
        }
    } else {
        if ($rolename == "super-admin" || $rolename == "manager" || $rolename == "smalleditingteacher") {
            //on affiche la catégorie
            $el['category'] = $DB->get_record('course_categories', ['id' => $course->category])->name;
        } else {
            // STARTDATE
            //on va chercher le champs perso type de certification
            $diplomeresult = $DB->get_record_sql('
            SELECT cd.value 
            FROM mdl_customfield_data cd
            JOIN mdl_customfield_field cf ON cf.id = cd.fieldid
            WHERE cd.instanceid = ' . $course->id . ' AND cf.shortname = "diplome"', null);
            if ($diplomeresult) {
                $diplome = $diplomeresult->value;

                if(trim($diplome) == "Certifications Fédérales"){

                    //on va chercher l'enrollement dans la formation
                    // $enrol = $DB->get_record_sql('SELECT * 
                    // FROM mdl_enrol
                    // WHERE courseid =  ' . $course->id . '
                    // AND customint1 = ' . $USER->id, null);

                    //on grise la formation si elle n'a pas commencé
                    $certification = true;
                    

                }
            }


            // DATES
            //on va chercher la session et on met les dates à la place de la catégorie
            $allsessions = $DB->get_records_sql('SELECT DISTINCT ss.id, ss.startdate, ss.enddate
            FROM mdl_groups g
            JOIN mdl_groups_members gm ON gm.groupid = g.id
            JOIN mdl_smartch_session ss ON ss.groupid = g.id
            WHERE gm.userid = ' . $USER->id . ' AND g.courseid = ' . $course->id, null);
            echo "<br/>----SESSION(S)---<br/>";
            
            if (count($allsessions) > 1) {
                //on regarde le role 
                if($rolename == "student"){
                    $multiplesession = true;
                    //on créer autant de vignette que de sessions
                    foreach($allsessions as $onesession){
                        
                        $el['notavailable'] = false;
                        $displaycourse = true;

                        if($certification){
                            
                            if($onesession->enddate < time()){
                                //si la session de la certification est terminé
                                $displaycourse = false;
                                $el['date1'] = '';
                                $el['date2'] = '';
                            } else if($onesession->startdate < time()){
                                //si la session de la certification a commencé
                                $el['notavailable'] = false;
                                $el['date1'] = 'Du  ' . userdate($onesession->startdate, '%d/%m/%Y');
                                $el['date2'] = 'Au ' . userdate($onesession->enddate, '%d/%m/%Y');
                            } else {
                                $el['notavailable'] = true;
                                if($onesession->startdate){
                                    $el['date1'] = 'À partir du  ' . userdate($onesession->startdate, '%d/%m/%Y');
                                } else {
                                    $el['date1'] = 'Date manquante';
                                }
                                $el['date2'] = '';
                            }
                            
                        } else if ($onesession) {
                            $el['date1'] = 'Du  ' . userdate($onesession->startdate, '%d/%m/%Y');
                            $el['date2'] = 'Au ' . userdate($onesession->enddate, '%d/%m/%Y');
                        } else {
                            $el['date1'] = '';
                            $el['date2'] = '';
                        }

                        //On ajoute la vignette pour la certif
                        if($displaycourse){
                            //si la session n'est pas terminé
                            $el['id'] = $course->id;
                            $el['freecategory'] = $freecategory;
                            $el['url'] = $CFG->wwwroot . "/theme/remui/views/formation.php?id=" . $course->id . "&return=dashboard";    
                            array_push($mycourses, $el);
                        }

                        echo json_encode($el);
                        
                    }
                } else {
                    //on affiche la catégorie 
                    $el['category'] = $DB->get_record('course_categories', ['id' => $course->category])->name;
                }
                
            } else {
                $session = reset($allsessions);
                if($certification){
                    if ($session->enddate < time()){
                        //si la session de la certif est terminé
                        $displaycourse = false;
                    } else if($session->startdate < time()){
                        //si la session a  commencé
                        $el['notavailable'] = false;
                        $el['date1'] = 'Du  ' . userdate($session->startdate, '%d/%m/%Y');
                        $el['date2'] = 'Au ' . userdate($session->enddate, '%d/%m/%Y');
                    } else {
                        $el['notavailable'] = true;
                        if($session->startdate){
                            $el['date1'] = 'À partir du  ' . userdate($session->startdate, '%d/%m/%Y');
                        } else {
                            $el['date1'] = 'Date manquante';
                        }
                        $el['date2'] = '';
                    }
                } else if ($session) {
                    $el['date1'] = 'Du  ' . userdate($session->startdate, '%d/%m/%Y');
                    $el['date2'] = 'Au ' . userdate($session->enddate, '%d/%m/%Y');
                } else {
                    $el['date1'] = '';
                    $el['date2'] = '';
                }

                echo json_encode($el);
                
            }

            echo "<br/>-------<br/>";
        }
    }

    // displaycourse = si la session n'est pas terminé
    if(!$multiplesession && $displaycourse){
        $el['id'] = $course->id;
        $el['freecategory'] = $freecategory;
        $el['url'] = $CFG->wwwroot . "/theme/remui/views/formation.php?id=" . $course->id . "&return=dashboard";    
        array_push($mycourses, $el);
    }
}

$data['rolename'] = $rolename;
$data['mycourses'] = $mycourses;

echo "<br/>/////////////////////////////<br/>";
echo json_encode($data);
