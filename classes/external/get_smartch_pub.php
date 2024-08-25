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

require_once($CFG->libdir . '/completionlib.php');

/**
 * Get course stats trait
 * @copyright (c) 2022 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait get_smartch_pub
{
    /**
     * Describes the parameters for get_smartch_pub
     * @return external_function_parameters
     */
    public static function get_smartch_pub_parameters()
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
    public static function get_smartch_pub()
    {
        global $PAGE, $USER;
        // Validation for context is needed.
        $context = \context_system::instance();
        self::validate_context($context);
        // $coursepercentage = new \core_completion\progress();

        // $userinfo = array();

        // $courses = enrol_get_users_courses($USER->id);

        // $coursescount = 0;
        // $coursescompleted = 0;
        // $activitiescomplete = 0;
        // $activitiesdue = 0;
        // foreach ($courses as $key => $course) {
        //     $coursescount++;
        //     $completion = new \completion_info($course);
        //     $progresspercentvalue = $coursepercentage->get_course_progress_percentage($course, $USER->id);
        //     if ($completion->is_enabled()) {
        //         $modules = $completion->get_activities();
        //         $activitiesprogress = 0;
        //         foreach ($modules as $module) {
        //             $moduledata = $completion->get_data($module, false, $USER->id);
        //             if ($moduledata->completionstate == COMPLETION_INCOMPLETE) {
        //                 $activitiesdue++;
        //             } else {
        //                 $activitiescomplete++;
        //             }
        //         }
        //         if ($progresspercentvalue == "100") {
        //             $coursescompleted++;
        //         }
        //     }
        // }

        // $stats['coursesenrolled'] = 0;
        // $stats['coursescompleted'] = 0;
        // $stats['activitiescompleted'] = 0;
        // $stats['activitiesdue'] = 0;
        // $stats['coursesenrolled'] = $coursescount;
        // $stats['coursescompleted'] = $coursescompleted;
        // $stats['activitiescompleted'] = $activitiescomplete;
        // $stats['activitiesdue'] = $activitiesdue;

        //modification smartch dashboard
        // $stats['username'] = $USER->firstname . ' ' . $USER->lastname;
        // $userinfo['username'] = 19;
        // $userinfo['firstname'] = "pouet";

        return json_encode($USER);
    }

    /**
     * Describes the get_smartch_info return value
     * @return external_value
     */
    public static function get_smartch_pub_returns()
    {
        return new external_value(PARAM_RAW, 'User info in JSON Format');
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
