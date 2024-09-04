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

require_once(__DIR__ . '/../../../../calendar/externallib.php');
// require_once('/../../views/utils.php');
// require_once($CFG->dirroot . '/course/lib.php');
// require_once('./smartch_functions.php');

/**
 * Get course stats trait
 * @copyright (c) 2022 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait get_smartch_calendar
{
    /**
     * Describes the parameters for get_smartch_my_courses
     * @return external_function_parameters
     */
    public static function get_smartch_calendar_parameters()
    {
        return new external_function_parameters(
            array(
                'timestart' => new external_value(PARAM_INT, 'timestart'),
                'timeend' => new external_value(PARAM_INT, 'timeend'),
                'groupid' => new external_value(PARAM_TEXT, 'groupid')
            )
        );
    }


    /**
     * Save order of sections in array of configuration format
     * @param  int $courseid Course id
     * @return boolean       true
     */
    public static function get_smartch_calendar($timestart = null, $timeend = null, $groupid = null)
    {
        global $DB, $CFG, $USER;

        // Validation for context is needed.
        $context = \context_system::instance();
        self::validate_context($context);

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

        $events = array();

        $filter = '';



        // if ($timestart) {
        //     $filter .= ' AND sp.startdate > ' . $timestart . ' ';
        // }
        // if ($timeend) {
        //     $filter .= ' AND sp.startdate < ' . $timeend . ' ';
        // }

        if($groupid && $groupid != "all"){
            $filter .= ' AND g.id = ' . $groupid . ' ';
        }

        //on va chercher les groupes de l'utilisateur
        $groups = $DB->get_records_sql('SELECT g.id, g.name FROM mdl_groups g JOIN mdl_groups_members gm ON gm.groupid = g.id WHERE gm.userid = ' . $USER->id, null);

        $usergroups = [];
        foreach ($groups as $group) {
            $usergroup = new stdClass();
            $usergroup->id = $group->id;
            $usergroup->name = $group->name;
            array_push($usergroups, $usergroup);
        }
        


        //On va chercher les plannings
        global $DB;
        $plannings = $DB->get_records_sql('SELECT sp.id, sp.sectionid, sp.startdate, sp.enddate, sp.geforplanningid, c.id AS courseid, c.fullname, g.name as groupname, g.id as groupid, ss.adress1, ss.adress2, ss.zip, ss.city, ss.location
        FROM mdl_smartch_planning sp
        JOIN mdl_smartch_session ss ON ss.id = sp.sessionid
        JOIN mdl_groups g ON g.id = ss.groupid
        JOIN mdl_course c ON c.id = g.courseid
        JOIN mdl_groups_members gm ON gm.groupid = g.id
        WHERE gm.userid = ' . $USER->id . '
        AND c.visible = 1
        ' . $filter . '
        ORDER BY sp.startdate', null);

        foreach ($plannings as $planning) {

            $event = new stdClass();

            //on change l'url en fonction du rôle
            if ($rolename == "smalleditingteacher") {
                $event->url = '/theme/remui/views/adminteam.php?teamid=' . $planning->groupid . '&sectionid=' . $planning->sectionid . '#modulesformation';
            } else {
                $event->url = '/theme/remui/views/formation.php?id=' . $planning->courseid . '&sectionid=' . $planning->sectionid . '#modulesformation';
            }

            $event->coursename = $planning->fullname;
            $event->title = 'Session du ' . userdate($planning->startdate, '%d/%m à %H:%M');
            // $event->title = $planning->fullname;
            // $event->title = $planning->fullname . " - " . $planning->geforplanningid;
            $event->groupname = $planning->groupname;
            $event->adress1 = $planning->adress1;
            $event->adress2 = $planning->adress2;
            $event->zip = $planning->zip;
            $event->city = $planning->city;
            $event->info = $planning->location;
            $event->actual = $rolename;

            //la matiere
            $matiereobject = $DB->get_record('course_sections', ['id' => $planning->sectionid]);
            $matiere = "";
            if ($matiereobject && $matiereobject->name) {
                $matiere = $matiereobject->name;
            } else {
                $matiere = "Généralités";
            }

            

            $event->matiere = $matiere;
            $event->start = userdate($planning->startdate, '%d/%m');
            // $event->start = date('Y-m-d\TH:i:s', $planning->startdate);
            // $event->end = userdate($planning->enddate, '%Y-%m-%dT%H:%M:%S');
            $event->end = date('Y-m-d\TH:i:s', $planning->enddate);


            //On va chercher le responsable pédagogique
            $queryresponsable = 'SELECT DISTINCT u.id, u.firstname, u.lastname 
            FROM mdl_groups g
            JOIN mdl_groups_members gm ON gm.groupid = g.id
            JOIN mdl_user u ON u.id = gm.userid
            JOIN mdl_role_assignments ra ON ra.userid = u.id
            JOIN mdl_role r ON r.id = ra.roleid
            WHERE g.id = ' . $planning->groupid . ' 
            AND r.shortname = "smalleditingteacher"';
            $findresponsable = $DB->get_records_sql($queryresponsable, null);
            $found = reset($findresponsable);
            if ($found) {
                $coach = $found->firstname . ' ' . $found->lastname;
            } else {
                $coach = "Aucun responsable pédagogique";
            }

            $event->info = '
            
            <div style="display:flex;align-items:center;">

            <div style="display:flex;align-items:center;">
                <svg style="width:20px;" class="mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span class="mr-4 FFARegular"><div>'.$coach.'</div></span>
            </div>

            <div style="display:flex;align-items:center;">
                <svg style="width:20px;" class="mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"></path>
                </svg>
                <span class="mr-4 FFARegular"><div>De '.userdate($planning->startdate, '%Hh%M').' à '.userdate($planning->enddate, '%Hh%M').'</div></span>
            </div>

            <div style="display:flex;align-items:center;">
                <svg style="width:20px;" class="mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
                <span class="mr-4 FFARegular"><div>'.$planning->adress1.'</div></span>
            </div>

            </div>
            
            ';

            array_push($events, $event);
        }

        //On formate

        // $events = " nooo";
        // array_push($mycourses, $el);

        $data['usergroups'] = $usergroups;
        $data['events'] = $events;


        // $out = array_values($courses);
        return json_encode($data);
    }

    /**
     * Describes the get_smartch_my_courses return value
     * @return external_value
     */
    public static function get_smartch_calendar_returns()
    {
        return new external_value(PARAM_RAW, 'Courses of a user in JSON Format');
    }
}
