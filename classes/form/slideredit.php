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

class slideredit extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $CFG, $DB, $PAGE;
        $mform = $this->_form; // Don't forget the underscore!

        $context = $PAGE->context;

        // $slider = $DB->get_record('smartch_slider', ['id' => 1]);

        // $content .= '<img style="width:200px;display:inline-block;margin:10px;" src="' . $slider->imagefixe . '" />';
        // $content .= '<img style="width:200px;display:inline-block;margin:10px;" src="' . $slider->image1 . '" />';
        // $content .= '<img style="width:200px;display:inline-block;margin:10px;" src="' . $slider->image2 . '" />';
        // $content .= '<img style="width:200px;display:inline-block;margin:10px;" src="' . $slider->image3 . '" />';

        // $mform->addElement('html', '<h1 style="margin-bottom:50px;letter-spacing:1px;" class="smartch_title FFABold FFF-Blue">Modifier le slider</h1>');
        // $mform->addElement('html', '<h5 style="margin-bottom:20px;" class="FFF-Blue">Fichiers acceptés: .png, .jpg, .jpeg</h5>');

        // $mform->addElement('header', 'fixe', 'Image fixe');

        // $mform->addElement('html', '<img style="width:200px;display:inline-block;margin:10px;" src="' . $slider->imagefixe . '" />');




        // $mform->addElement(
        //     'filemanager',
        //     'attachments',
        //     'Types de fichier acceptés',
        //     null,
        //     [
        //         'subdirs' => 0,
        //         'maxbytes' => 10485760,
        //         'areamaxbytes' => 20485760,
        //         'maxfiles' => 5,
        //         'accepted_types' => array('.png', '.jpg', '.jpeg', '.gif')
        //     ]
        // );


        // $mform->addElement(
        //     'filepicker',
        //     'imagefixe',
        //     'L\'image sur la droite qui est mise en avant',
        //     null,
        //     array(
        //         'subdirs' => 0, 'areamaxbytes' => 11111111111111, 'maxfiles' => 1,
        //         'accepted_types' => array('.png', '.jpg', '.jpeg', '.gif')
        //     )
        // );

        // $mform->addElement('header', 'slider', 'Images du slider');

        // $mform->addElement('html', '<img style="width:200px;display:inline-block;margin:10px;" src="' . $slider->image1 . '" />');

        $mform->addElement(
            'filepicker',
            'image',
            'Image',
            null,
            array(
                'subdirs' => 0, 'areamaxbytes' => 11111111111111, 'maxfiles' => 1,
                'accepted_types' => array('.png', '.jpg', '.jpeg', '.gif')
            )
        );

        $mform->addElement('html', '<h5 style="margin-bottom:20px;text-align:center;" class="FFF-Blue">Fichiers acceptés: .png, .jpg, .jpeg, .gif</h5>');


        // $mform->addElement('html', '<img style="width:200px;display:inline-block;margin:10px;" src="' . $slider->image2 . '" />');


        // $mform->addElement(
        //     'filepicker',
        //     'image2',
        //     'Image 2',
        //     null,
        //     array(
        //         'subdirs' => 0, 'areamaxbytes' => 11111111111111, 'maxfiles' => 1,
        //         'accepted_types' => array('.png', '.jpg', '.jpeg')
        //     )
        // );

        // $mform->addElement('html', '<img style="width:200px;display:inline-block;margin:10px;" src="' . $slider->image3 . '" />');

        // $mform->addElement(
        //     'filepicker',
        //     'image3',
        //     'Image 3',
        //     null,
        //     array(
        //         'subdirs' => 0, 'areamaxbytes' => 11111111111111, 'maxfiles' => 1,
        //         'accepted_types' => array('.png', '.jpg', '.jpeg')
        //     )
        // );

        // $return = $this->_customdata['variables']['return'];
        // $teamid = $this->_customdata['variables']['teamid'];
        // $courseid = $this->_customdata['variables']['courseid'];
        // $userid = $this->_customdata['variables']['userid'];


        // hidden
        // $mform->addElement('hidden', 'return', $return);
        // $mform->setType('return', PARAM_TEXT); // Set the data type to integer
        // $mform->addElement('hidden', 'teamid', $teamid);
        // $mform->setType('teamid', PARAM_INT); // Set the data type to integer
        // $mform->addElement('hidden', 'courseid', $courseid);
        // $mform->setType('courseid', PARAM_INT); // Set the data type to integer
        // $mform->addElement('hidden', 'userid', $userid);
        // $mform->setType('userid', PARAM_INT); // Set the data type to integer


        // $options = array(
        //     'size' => '300',
        //     'maxlength' => '50',
        //     'class' => '',
        //     // 'required' => true
        // );

        // $mform->addElement('text', 'subject', 'Sujet du message', $options);
        // $mform->addRule('subject', null, 'required', null, 'client');
        // $mform->setType('subject', PARAM_TEXT); // Set the data type to integer


        // $mform->addElement(
        //     'editor',
        //     'content',
        //     'Contenu',
        //     null,
        //     array('context' => $context)
        // )->setValue(array('text' => ""));
        // $mform->setType('content', PARAM_RAW);
        // $mform->addRule('content', null, 'required', null, 'client');

        $this->add_action_buttons(false, "Téléverser");
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
