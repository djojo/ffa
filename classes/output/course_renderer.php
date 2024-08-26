<?php

namespace theme_remui\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/renderer.php');

class course_renderer extends \core_course_renderer
{
    
    /**
     * 
     * modification ffa completion activity header !!
     * 
     * Rend les informations sur l'activité.
     *
     * Surcharge la méthode parente pour personnaliser l'affichage.
     *
     * @param \core_course\output\activity_information $page
     * @return string code HTML pour la page
     */

    public function render_activity_information(\core_course\output\activity_information $page) {
        // $data = $page->export_for_template($this->output);
        // return $this->output->render_from_template('core_course/activity_info', $data);
        return "";
    }

   
}
