<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Get course stats service
 *
 * @package   theme_remui
 * @copyright (c) 2023 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_remui\external;

defined('MOODLE_INTERNAL') || die;

use external_function_parameters;
use external_value;
use core_course_list_element;
use moodle_url;
use stdClass;

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/completionlib.php');
// require_once('/../../views/utils.php');
// require_once($CFG->dirroot . '/course/lib.php');
// require_once('./smartch_functions.php');

/**
 * Get course stats trait
 * @copyright (c) 2022 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait get_smartch_my_courses
{
    /**
     * Describes the parameters for get_smartch_my_courses
     * @return external_function_parameters
     */
    public static function get_smartch_my_courses_parameters()
    {
        return new external_function_parameters(
            array()
        );
    }


    /**
     * Save order of sections in array of configuration format
     * @param  int $courseid Course id
     * @return boolean       true
     */
    public static function get_smartch_my_courses()
    {
        global $DB, $CFG, $USER;
        // Validation for context is needed.
        $context = \context_system::instance();
        self::validate_context($context);

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

            require_once($CFG->dirroot . '/theme/remui/views/utils.php');
            //on va chercher la progression de l'apprenant
            $el['prog'] = getCompletionPourcent($course->id, $USER->id);

           //on recupère le champs personnalisé diplome
           $el['type'] = getCourseCustomSelect($course->id, "type");
           //on recupère le champs personnalisé duration
           $el['duration'] = getCourseCustom($course->id, "duration");
            

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
                $imgcourse = $CFG->wwwroot . '/theme/remui/pix/background.jpg';
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

                        // if(true){
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
                    //on va chercher la/les session(s) et on met les dates à la place de la catégorie
                    $allsessions = $DB->get_records_sql('SELECT DISTINCT ss.id, ss.startdate, ss.enddate
                    FROM mdl_groups g
                    JOIN mdl_groups_members gm ON gm.groupid = g.id
                    JOIN mdl_smartch_session ss ON ss.groupid = g.id
                    WHERE gm.userid = ' . $USER->id . ' AND g.courseid = ' . $course->id . '
                    AND ss.startdate > 1578309539
                    ORDER BY ss.startdate ASC', null);
                    
                    //si il y a plusieurs session avec ce cours
                    if (count($allsessions) > 1) {
                        //on regarde le role 
                        if($rolename == "student"){

                            
                            $multiplesession = true;
                            //on créer autant de vignette que de sessions
                            foreach($allsessions as $onesession){
                                
                                //on check que ce ne soit pas une erreur d'ajout de session en bdd
                                if($onesession->startdate != 0 && $onesession->enddate != 0){
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
                                        $el['url'] = $CFG->wwwroot . "/theme/remui/views/courses/details.php?id=" . $course->id . "&return=dashboard";    
                                        array_push($mycourses, $el);
                                    }
                                }
                                
                                
                            }
                        } else {
                            //on affiche la catégorie 
                            $el['category'] = $DB->get_record('course_categories', ['id' => $course->category])->name;
                        }
                        
                    } else {
                        //sinon il y a une seule session avec ce cours
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
                        
                    }
                }
            }

            // displaycourse = si la session n'est pas terminé
            if(!$multiplesession && $displaycourse){
                $el['id'] = $course->id;
                $el['freecategory'] = $freecategory;
                $el['url'] = $CFG->wwwroot . "/theme/remui/views/courses/details.php?id=" . $course->id . "&return=dashboard";    
                array_push($mycourses, $el);
            }
        }

        $data['rolename'] = $rolename;
        $data['mycourses'] = $mycourses;

        return json_encode($data);
    }

    /**
     * Describes the get_smartch_my_courses return value
     * @return external_value
     */
    public static function get_smartch_my_courses_returns()
    {
        return new external_value(PARAM_RAW, 'Courses of a user in JSON Format');
    }
}
