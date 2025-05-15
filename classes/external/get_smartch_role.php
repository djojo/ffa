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
use stdClass;

require_once($CFG->libdir . '/completionlib.php');
// require_once('../../views/utils.php');

/**
 * Get course stats trait
 * @copyright (c) 2022 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait get_smartch_role
{
    /**
     * Describes the parameters for get_smartch_info
     * @return external_function_parameters
     */
    public static function get_smartch_role_parameters()
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
    public static function get_smartch_role()
    {
        global $PAGE, $USER, $CFG;
        // Validation for context is needed.
        $context = \context_system::instance();
        self::validate_context($context);

        //On va chercher le plus haut rôle
        $rolename = "";
        global $DB, $USER;
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

        //l'url
        // $baseurl = new \moodle_url('/');
        $baseurl = $CFG->wwwroot;

        //on va chercher les cours de l'utilisateur
        // $querycourses = 'SELECT c.id, c.fullname, c.category FROM mdl_course c
        //     JOIN mdl_role_assignments ra ON ra.userid = ' . $USER->id . '
        //     JOIN mdl_context ct ON ct.id = ra.contextid AND c.id = ct.instanceid
        //     JOIN mdl_role r ON r.id = ra.roleid
        //     WHERE c.format != "site" AND c.visible = 1';
        // $courses = $DB->get_records_sql($querycourses, null);

        //on implémente les règles d'accès à la plateforme
        // if (count($courses) == 0) {
        //     $el['sso'] = true;
        // } else {
        //     $el['sso'] = false;
        // }

        // $data = new stdClass();
        $el['rolename'] = $rolename;
        $el['baseurl'] = $baseurl;


        return json_encode($el);
    }

    /**
     * Describes the get_smartch_info return value
     * @return external_value
     */
    public static function get_smartch_role_returns()
    {
        return new external_value(PARAM_RAW, 'User role in JSON Format');
        // return new \external_single_structure(
        //     array(
        //         // 'coursesenrolled' => new external_value(PARAM_INT, 'Enrolled Users'),
        //         // 'coursescompleted' => new external_value(PARAM_INT, 'Students Completed'),
        //         // 'activitiescompleted' => new external_value(PARAM_INT, 'Students Inprogress'),
        //         // 'activitiesdue' => new external_value(PARAM_INT, 'Students Not Started'),
        //         'username' => new external_value(PARAM_INT, 'inconnu')
        //     )
        // );
    }
}
