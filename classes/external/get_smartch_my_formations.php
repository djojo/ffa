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
 * Get smartch my formations - returns ALL published courses with metadata
 *
 * @package   theme_remui
 * @copyright (c) 2023 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_remui\external;

defined('MOODLE_INTERNAL') || die;

use external_function_parameters;
use external_value;

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/completionlib.php');

/**
 * Get smartch my formations trait
 * @copyright (c) 2023 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
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
     * Helper function: Convert numeric value to text option
     * 
     * @param int $fieldid Custom field ID
     * @param string $value Numeric value (1, 2, etc.)
     * @return string Text value from field options
     */
    public static function get_select_option_text($fieldid, $value) {
        global $DB;
        
        $field = $DB->get_record('customfield_field', array('id' => $fieldid));
        if (!$field) {
            return '';
        }
        
        $config = json_decode($field->configdata, true);
        if (!isset($config['options'])) {
            return '';
        }
        
        $options = preg_split('/\r\n|\r|\n/', $config['options']);
        $index = intval($value) - 1;
        
        if (isset($options[$index])) {
            return trim($options[$index]);
        }
        
        return '';
    }

    /**
     * Get all published courses with metadata
     * @return string JSON encoded array of courses
     */
    public static function get_smartch_my_formations()
    {
        global $DB, $CFG;

        $context = \context_system::instance();
        self::validate_context($context);

        $courses = $DB->get_records_sql(
            'SELECT * FROM {course} WHERE visible = 1 AND format != ? ORDER BY fullname ASC',
            array('site')
        );

        $parcours = array();

        foreach ($courses as $course) {
            $el = array();
            $el['fullname'] = $course->fullname;
            $el['id'] = $course->id;
            $el['url'] = $CFG->wwwroot . "/course/view.php?id=" . $course->id;

            // Retrieve custom fields
            $customfields_data = $DB->get_records_sql(
                'SELECT cd.*, cf.shortname, cf.configdata, cf.id as fieldid
                 FROM {customfield_data} cd
                 JOIN {customfield_field} cf ON cf.id = cd.fieldid
                 WHERE cd.instanceid = ?',
                array($course->id)
            );

            $el['type'] = '';
            $el['duration'] = '';

            foreach ($customfields_data as $data) {
                if ($data->shortname == 'type') {
                    $el['type'] = self::get_select_option_text($data->fieldid, $data->value);
                } elseif ($data->shortname == 'duration') {
                    $el['duration'] = $data->value;
                }
            }

            // Category
            if ($course->category > 0) {
                $category = $DB->get_record('course_categories', array('id' => $course->category));
                if ($category) {
                    $el['category'] = $category->name;
                }
            }

            // Course image
            $imgcourse = "";
            $course2 = new \core_course_list_element($course);
            foreach ($course2->get_course_overviewfiles() as $file) {
                if ($file->is_valid_image()) {
                    $imagepath = '/' . $file->get_contextid() .
                        '/' . $file->get_component() .
                        '/' . $file->get_filearea() .
                        $file->get_filepath() .
                        $file->get_filename();
                    $imageurl = new \moodle_url('/pluginfile.php' . $imagepath);
                    $imgcourse = $imageurl->out(false);
                    break;
                }
            }
            if ($imgcourse == "") {
                $imgcourse = $CFG->wwwroot . '/theme/remui/pix/background.jpg';
            }
            $el['img'] = $imgcourse;

            $parcours[] = $el;
        }

        return json_encode($parcours);
    }

    /**
     * Describes the get_smartch_my_formations return value
     * @return external_value
     */
    public static function get_smartch_my_formations_returns()
    {
        return new external_value(PARAM_RAW, 'Courses in JSON Format');
    }
}