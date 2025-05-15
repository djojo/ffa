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

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/completionlib.php');
// require_once($CFG->dirroot . '/course/lib.php');
// require_once('./smartch_functions.php');

/**
 * Get course stats trait
 * @copyright (c) 2022 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait get_smartch_my_formations
{
    /**
     * Describes the parameters for get_smartch_my_formations
     * @return external_function_parameters
     */
    public static function get_smartch_my_formations_parameters()
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
    public static function get_smartch_my_formations()
    {
        global $DB, $CFG;
        // Validation for context is needed.
        $context = \context_system::instance();
        self::validate_context($context);



        $categories = $DB->get_records_sql('SELECT * from mdl_course_categories WHERE depth = 2');
        $parcours = array();
        foreach ($categories as $category) {
            //on va chercher le premier cours de la catégorie UO (non visible)
            $courses = $DB->get_records_sql('SELECT * from mdl_course WHERE category = ' . $category->id . ' AND visible = 0');
            $course = reset($courses);
            $el['fullname'] = $course->fullname;
            $el['id'] = $category->id;
            $el['url'] = $CFG->wwwroot . "/theme/remui/views/formation.php?id=" . $course->id;

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
                    $el['img'] = $imageurl;
                    // Use the first image found.
                    break;
                }
            }

            array_push($parcours, $el);
        }

        // $out = array_values($courses);
        return json_encode($parcours);
    }

    /**
     * Describes the get_smartch_my_courses return value
     * @return external_value
     */
    public static function get_smartch_my_formations_returns()
    {
        return new external_value(PARAM_RAW, 'Courses of a user in JSON Format');
    }
}
