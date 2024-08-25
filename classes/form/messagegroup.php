<?php
// This file is part of Moodle Course Rollover Plugin
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
 * @package     concorde_plugin
 * @author      Geoffroy Rouaix
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// namespace concorde_plugin\form;

// use moodleform;

require_once("$CFG->libdir/formslib.php");

class create extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $CFG, $DB, $PAGE;
        $mform = $this->_form; // Don't forget the underscore!

        $context = $PAGE->context;

        // $mform->addElement('html', '<h1 style="margin-bottom:50px;letter-spacing:1px;" class="smartch_title FFABlack FFF-Blue">Nouveau message pour le groupe ' . $this->_customdata['variables']['teamname'] . '</h1>');

        $returnurl = $this->_customdata['variables']['returnurl'];
        $groupid = $this->_customdata['variables']['groupid'];

        // hidden
        $mform->addElement('hidden', 'returnurl', $returnurl);
        $mform->setType('returnurl', PARAM_TEXT); // Set the data type to integer

        $mform->addElement('hidden', 'groupid', $groupid);
        $mform->setType('groupid', PARAM_INT); // Set the data type to integer

        // $mform->addElement('hidden', 'backurl', $backurl);

        $options = array(
            'size' => '300',
            'maxlength' => '50',
            'class' => '',
            // 'required' => true
        );
        $mform->addElement('text', 'subject', 'Sujet du message', $options);
        $mform->addRule('subject', null, 'required', null, 'client');
        $mform->setType('subject', PARAM_TEXT); // Set the data type to integer


        $mform->addElement(
            'editor',
            'content',
            'Contenu',
            null,
            array('context' => $context)
        )->setValue(array('text' => ""));
        $mform->setType('content', PARAM_RAW);
        $mform->addRule('content', null, 'required', null, 'client');

        $this->add_action_buttons(false, "Envoyer");
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
