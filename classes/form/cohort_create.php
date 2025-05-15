<?php

require_once("$CFG->libdir/formslib.php");

class create extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $CFG, $DB;
        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('text', 'name', 'Nom du groupe');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        
        // $courses = $DB->get_records_sql('SELECT * 
        // FROM mdl_course
        // WHERE format <> "site"
        // AND fullname != ""', NULL);

        // $courseoptions = [];
        // $courseoptions["none"] = "Aucune formation";
        // foreach($courses as $course){
        //     $courseoptions[$course->id] = $course->fullname;
        // }
        // $mform->addElement('select', 'courseid', "Synchroniser avec une formation", $courseoptions);

        $this->add_action_buttons();
    }


    //Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }


    /**
     * Process the form submission, used if form was submitted via AJAX
     *
     * This method can return scalar values or arrays that can be json-encoded, they will be passed to the caller JS.
     *
     * Submission data can be accessed as: $this->get_data()
     *
     * @return mixed
     */
    // public function process_dynamic_submission() {
    //     file_postupdate_standard_filemanager($this->get_data(), 'files',
    //         $this->get_options(), $this->get_context_for_dynamic_submission(), 'user', 'private', 0);
    //     return null;
    // }

    /**
     * Load in existing data as form defaults
     *
     * Can be overridden to retrieve existing values from db by entity id and also
     * to preprocess editor and filemanager elements
     *
     * Example:
     *     $this->set_data(get_entity($this->_ajaxformdata['id']));
     */
    // public function set_data_for_dynamic_submission(): void {
    //     $data = new \stdClass();
    //     file_prepare_standard_filemanager($data, 'files', $this->get_options(),
    //         $this->get_context_for_dynamic_submission(), 'user', 'private', 0);
    //     $this->set_data($data);
    // }

}
