<?php
require_once($CFG->dirroot . 'config.php');
require_once($CFG->dirroot . '/course/lib.php');


function pouet()
{
    return "http://localhost:8888/smartch/site/pluginfile.php/14/course/overviewfiles/screenshot.png";
}

function get_course_image($courseel)
{
    global $CFG;
    $course = new core_course_list_element($courseel);

    $outputimage = '';
    foreach ($course->get_course_overviewfiles() as $file) {
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
            return $imageurl;
            // $outputimage = html_writer::tag(
            //     'div',
            //     html_writer::empty_tag('img', array('src' => $imageurl)),
            //     array('class' => 'courseimage')
            // );
            // Use the first image found.
            break;
        }
    }
}
