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
use moodle_url;

require_once($CFG->libdir . '/completionlib.php');

/**
 * Get course stats trait
 * @copyright (c) 2022 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait get_smartch_course_info
{
    /**
     * Describes the parameters for get_course_stats
     * @return external_function_parameters
     */
    public static function get_smartch_course_info_parameters()
    {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id'),
            )
        );
    }

    /**
     * Save order of sections in array of configuration format
     * @param  int $courseid Course id
     * @return boolean       true
     */
    public static function get_smartch_course_info($courseid)
    {
        global $PAGE;
        // Validation for context is needed.
        $context = \context_course::instance($courseid);
        self::validate_context($context);
        $course = get_course($courseid);
        // $coursehandler = new \theme_remui_coursehandler();
        // $stats = $coursehandler->get_course_stats($course);
        $data = array();
        $data['course'] = $course;
        // $data['paramurl'] = new moodle_url('/course/view.php?id=' . $courseid);

        return json_encode($data);
    }

    /**
     * Describes the get_course_stats return value
     * @return external_value
     */
    public static function get_smartch_course_info_returns()
    {
        return new external_value(PARAM_RAW, 'Course infot');
        // return new \external_single_structure(
        //     array(
        //         'enrolledusers' => new external_value(PARAM_INT, 'Enrolled Users'),
        //         'completed' => new external_value(PARAM_INT, 'Students Completed'),
        //         'inprogress' => new external_value(PARAM_INT, 'Students Inprogress'),
        //         'notstarted' => new external_value(PARAM_INT, 'Students Not Started')
        //     )
        // );
    }
}
