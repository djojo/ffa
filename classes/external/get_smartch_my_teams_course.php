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
trait get_smartch_my_teams_course
{
    /**
     * Describes the parameters for get_smartch_my_courses
     * @return external_function_parameters
     */
    public static function get_smartch_my_teams_course_parameters()
    {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'course Id'),
            )
        );
    }


    /**
     * Save order of sections in array of configuration format
     * @param  int $courseid Course id
     * @return boolean       true
     */
    public static function get_smartch_my_teams_course($courseid)
    {
        global $DB, $CFG, $USER;
        // Validation for context is needed.
        $context = \context_system::instance();
        self::validate_context($context);

        //on va chercher le cours
        $course = $DB->get_record('course', ['id' => $courseid]);

        $timeplus30 = time() + 30 * 24 * 60 * 60;
        $timemoins30 = time() - 30 * 24 * 60 * 60;

        $groups = $DB->get_records_sql('SELECT g.id, g.name FROM mdl_groups g
        JOIN mdl_groups_members gm ON gm.groupid = g.id
        JOIN mdl_smartch_session ss ON ss.groupid = g.id
        WHERE g.courseid = ' . $courseid . '
        AND gm.userid = ' . $USER->id, null);

        // $groups = $DB->get_records_sql('SELECT g.id, g.name FROM mdl_groups g
        // JOIN mdl_groups_members gm ON gm.groupid = g.id
        // JOIN mdl_smartch_session ss ON ss.groupid = g.id
        // WHERE g.courseid = ' . $courseid . '
        // AND gm.userid = ' . $USER->id . '
        // AND ss.startdate > ' . $timemoins30 . '
        // AND ss.startdate < ' . $timeplus30, null);

        // $groups = $DB->get_records_sql('SELECT g.id, g.name FROM mdl_groups g
        // JOIN mdl_groups_members gm ON gm.groupid = g.id
        // WHERE gm.userid = ' . $USER->id . ' AND g.courseid = ' . $courseid, null);

        $data = array();
        foreach ($groups as $team) {
            //on va chercher les membres de l'équipe
            $teamates = array();
            $querymates = '
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
                LIMIT 0, 6
                ';
            $queryallmates = '
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
                ';
            $mates = $DB->get_records_sql($querymates, null);
            $allmates = $DB->get_records_sql($queryallmates, null);
            // $mates = $DB->get_records('groups_members', ['groupid' => $team->id], '', '*', 0, 6);
            // $allmates = $DB->get_records('groups_members', ['groupid' => $team->id], '', '*');
            $totalmates = count($allmates);
            foreach ($mates as $mate) {
                $user = $DB->get_record('user', ['id' => $mate->id]);
                array_push($teamates, $user);
            }
            $el['total'] = $totalmates;

            // Utilisation d'une expression régulière pour extraire le texte entre crochets et le tiret
            if (preg_match('/\]\s+(.+)/', $team->name, $matches)) {
                // $matches[1] contient le texte après les crochets
                $team->name = $matches[1];
            }

            $el['team'] = $team;
            $el['teamates'] = $teamates;
            $baseurl = $CFG->wwwroot;
            $el['url'] = $baseurl;
            $el['courseid'] = $courseid;
            $el['coursename'] = $course->fullname;


            //On va chercher la session du groupe
            $session = $DB->get_record_sql('SELECT ss.id, ss.startdate, ss.enddate
                FROM mdl_groups g
                JOIN mdl_smartch_session ss ON ss.groupid = g.id
                WHERE g.id = ' . $team->id, null);
            $el['date'] = 'Du  ' . userdate($session->startdate, '%d/%m/%Y') . ' au ' . userdate($session->enddate, '%d/%m/%Y');


            array_push($data, $el);
        }

        if (count($groups) == 0) {
            $data = "noteam";
        }

        // $out = array_values($courses);
        return json_encode($data);
    }

    /**
     * Describes the get_smartch_my_courses return value
     * @return external_value
     */
    public static function get_smartch_my_teams_course_returns()
    {
        return new external_value(PARAM_RAW, 'Teams of a user in JSON Format');
    }
}
