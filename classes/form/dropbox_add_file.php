<?php

require_once("$CFG->libdir/formslib.php");

class edit extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $DB, $USER;

        $mform = $this->_form; // Don't forget the underscore!

        // $plateforme = getPlateforme();

        // $id = $this->_customdata['variables']['id'];
        // $mform->addElement('hidden', 'id', $id);
        // $mform->setType('id', PARAM_INT); // Set the data type to integer

        // var_dump($moduleid);
        // die();

        $teamid = $this->_customdata['variables']['teamid'];
        $mform->addElement('hidden', 'teamid', $teamid);
        $mform->setType('teamid', PARAM_INT); // Set the data type to integer

        $mform->addElement(
            'filemanager',
            'attachments',
            'Fichiers disponibles',
            null,
            [
                'subdirs' => 0,
                'maxbytes' => 20485760,
                'areamaxbytes' => 50485760,
                'maxfiles' => 50,
                'accepted_types' => ['*'],
                'return_types' => FILE_INTERNAL | FILE_EXTERNAL,
            ]
        );

        // $roleshortname = getUserRole($USER->id)->shortname;

        // display save if user is responsable pedagogique
        if (hasResponsablePedagogiqueRole()){
            $this->add_action_buttons(false, "Mettre à jour le dépôt");
        }

    }

    //Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }
}
