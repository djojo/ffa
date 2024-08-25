<?php

require_once("$CFG->libdir/formslib.php");

class create extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $CFG, $DB, $PAGE;

        $context = $PAGE->context;

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('text', 'fullname', 'Nom de la formation');
        $mform->setType('fullname', PARAM_TEXT);
        $mform->addRule('fullname', null, 'required', null, 'client');

        $mform->addElement('text', 'shortname', 'Nom abrégé de la formation');
        $mform->setType('shortname', PARAM_TEXT);
        $mform->addRule('shortname', null, 'required', null, 'client');

        $mform->addElement(
            'editor',
            'summary',
            'Courte description de la formation',
            null,
            array('context' => $context)
        )->setValue(array('text' => ""));
        $mform->setType('summary', PARAM_RAW);

        $sectionsoptions = [];
        for ($i = 1; $i <= 20; $i++) {
            $sectionsoptions[$i] = $i;
        }
        $mform->addElement('select', 'nbsection', "Nombre de sections", $sectionsoptions);


        $categories = $DB->get_records_sql('SELECT * 
        FROM mdl_course_categories', NULL);
        $catoptions = [];
        foreach($categories as $cat){
            $catoptions[$cat->id] = $cat->name;
        }
        $mform->addElement('select', 'categoryid', "Catégorie", $catoptions);
        
        // $mform->addElement('text', 'coursetype', "Type de formation");
        // $mform->addElement('text', 'coursediplome', "Diplôme");
        $mform->addElement('text', 'courseduration', "Durée de la formation (h)");
        $mform->setType('courseduration', PARAM_TEXT);

        // $coursetypeoptions = array(
        //     'short' => 'Formation courte',
        //     'long' => 'Formation longue'
        // );
        // $mform->addElement('select', 'subscribemethod', "Méthode d'inscription", $coursetypeoptions);

        // $typeoptions = array(
        //     'classe' => 'Mode classe',
        //     'ampitheatre' => 'Mode Ampithéatre'
        // );
        // $mform->addElement('select', 'subscribemethod', "Méthode d'inscription", $typeoptions);

        $typeoptions = array(
            '0' => 'Non publié',
            '1' => 'Publié'
        );
        $mform->addElement('select', 'visible', "Visibilité", $typeoptions);

        //on va chercher les départements
        // $cohorts = $DB->get_records_sql('SELECT c.*
        // FROM mdl_cohort c', null);

        $cohortscontent = "";
        // $cohortscontent .= '<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>';
        // $cohortscontent .= '
        
        // <style>
        // .dropdown {
        //     position: relative;
        //     font-size: 14px;
        //     color: #333;
        //     z-index: 10;
        //     width: 220px;
          
        //     .dropdown-list {
        //       padding: 12px;
        //       background: #fff;
        //       position: absolute;
        //       top: 50px;
        //       min-width: 300px;
        //       left: 2px;
        //       right: 2px;
        //       box-shadow: 0 1px 2px 1px rgba(0, 0, 0, .15);
        //       transform-origin: 50% 0;
        //       transform: scale(1, 0);
        //       transition: transform .15s ease-in-out .15s;
        //       max-height: 66vh;
        //       overflow-y: scroll;
        //     }
            
        //     .dropdown-option {
        //       display: block;
        //       padding: 8px 12px;
        //       opacity: 0;
        //       transition: opacity .15s ease-in-out;
        //     }
            
        //     .dropdown-label {
        //       display: block;
        //       height: 50px;
        //       background: #fff;
        //       border: 1px solid #ccc;
        //       padding: 16px 12px;
        //       line-height: 1;
        //       cursor: pointer;
        //       font-size: 1rem;
        //       color: #4c5a73;
              
        //       &:before {
        //         content: "⌄";
        //         float: right;
        //       }
        //     }
            
        //     &.on {
        //      .dropdown-list {
        //         transform: scale(1, 1);
        //         transition-delay: 0s;
                
        //         .dropdown-option {
        //           opacity: 1;
        //           transition-delay: .2s;
        //         }
        //       }
              
        //       .dropdown-label:before {
        //         content: "⌃";
        //       }
        //     }
            
        //     [type="checkbox"] {
        //       position: relative;
        //       top: -1px;
        //       margin-right: 4px;
        //     }
        //   }
        // </style>

        // <div class="form-group row  fitem  ">
        //   <div class="col-lg-3 col-md-4 col-form-label p-0">
        //     <div class="d-flex align-items-center flex-gap-1 inner">
        //           <label class="edw-form-label d-inline word-break m-0  " for="id_subscribemethod">
        //             Inscrire des groupes
        //           </label>
        //     </div>
        //   </div>
        //   <div class="col-lg-9 col-md-8 checkbox p-0">
        //     <div class="form-check d-flex align-items-center p-0">
        //         <div class="d-flex align-items-center align-self-start">
        //           <div class="dropdown" data-control="checkbox-dropdown">
        //             <label class="dropdown-label">Sélectionnez</label>
                    
        //             <div class="dropdown-list">
        //               <a href="#" data-toggle="check-all" class="dropdown-option">
        //                 Tous les départements  
        //               </a>';
            
        //               foreach($cohorts as $cohort){
        //                 // $mform->addElement('checkbox', 'cohort'.$cohort->id, $cohort->name);
        //                 $cohortscontent .= '
                        
        //                 <label class="dropdown-option">
        //                     <input class="form-check-input" type="checkbox" name="cohort'.$cohort->id.'" value="'.$cohort->id.'" />
        //                     '.$cohort->name.'
        //                 </label>';
        //               }
                      
    //                   $cohortscontent .= '   
    //                 </div>
    //               </div>
    //             </div>
    //         </div>
    //     </div>
    // </div>

        
    //   <script>
    //   (function($) {
    //     var CheckboxDropdown = function(el) {
    //       var _this = this;
    //       this.isOpen = false;
    //       this.areAllChecked = false;
    //       this.$el = $(el);
    //       this.$label = this.$el.find(\'.dropdown-label\');
    //       this.$checkAll = this.$el.find(\'[data-toggle="check-all"]\').first();
    //       this.$inputs = this.$el.find(\'[type="checkbox"]\');
          
    //       this.onCheckBox();
          
    //       this.$label.on(\'click\', function(e) {
    //         e.preventDefault();
    //         _this.toggleOpen();
    //       });
          
    //       this.$checkAll.on(\'click\', function(e) {
    //         e.preventDefault();
    //         _this.onCheckAll();
    //       });
          
    //       this.$inputs.on(\'change\', function(e) {
    //         _this.onCheckBox();
    //       });
    //     };
        
    //     CheckboxDropdown.prototype.onCheckBox = function() {
    //       this.updateStatus();
    //     };
        
    //     CheckboxDropdown.prototype.updateStatus = function() {
    //       var checked = this.$el.find(\':checked\');
          
    //       this.areAllChecked = false;
    //       this.$checkAll.html(\'Inscrire tous les groupes\');
          
    //       if(checked.length <= 0) {
    //         this.$label.html(\'Inscrire des groupes\');
    //       }
    //       else if(checked.length === 1) {
    //         this.$label.html(checked.parent(\'label\').text());
    //       }
    //       else if(checked.length === this.$inputs.length) {
    //         this.$label.html(\'Inscrire tous les groupes\');
    //         this.areAllChecked = true;
    //         this.$checkAll.html(\'Tout désélectionner\');
    //       }
    //       else {
    //         if(checked.length == 1){
    //           this.$label.html(checked.length + \' Sélectionné\');
    //         } else {
    //           this.$label.html(checked.length + \' Sélectionnés\');
    //         }
    //       }
    //     };
        
    //     CheckboxDropdown.prototype.onCheckAll = function(checkAll) {
    //       if(!this.areAllChecked || checkAll) {
    //         this.areAllChecked = true;
    //         this.$checkAll.html(\'Uncheck All\');
    //         this.$inputs.prop(\'checked\', true);
    //       }
    //       else {
    //         this.areAllChecked = false;
    //         this.$checkAll.html(\'Check All\');
    //         this.$inputs.prop(\'checked\', false);
    //       }
          
    //       this.updateStatus();
    //     };
        
    //     CheckboxDropdown.prototype.toggleOpen = function(forceOpen) {
    //       var _this = this;
          
    //       if(!this.isOpen || forceOpen) {
    //          this.isOpen = true;
    //          this.$el.addClass(\'on\');
    //         $(document).on(\'click\', function(e) {
    //           if(!$(e.target).closest(\'[data-control]\').length) {
    //            _this.toggleOpen();
    //           }
    //         });
    //       }
    //       else {
    //         this.isOpen = false;
    //         this.$el.removeClass(\'on\');
    //         $(document).off(\'click\');
    //       }
    //     };
        
    //     var checkboxesDropdowns = document.querySelectorAll(\'[data-control="checkbox-dropdown"]\');
    //     for(var i = 0, length = checkboxesDropdowns.length; i < length; i++) {
    //       new CheckboxDropdown(checkboxesDropdowns[i]);
    //     }
    //   })(jQuery);
    //   </script>
    //   ';
    //     $mform->addElement('html', $cohortscontent);


        // foreach($cohorts as $cohort){
        //     $mform->addElement('checkbox', 'cohort'.$cohort->id, $cohort->name);
        //     // $cohortscontent .= '
            
        //     // <label class="dropdown-option">
        //     //     <input class="form-check-input" type="checkbox" id="id_cohort'.$cohort->id.'" name="cohort'.$cohort->id.'" value="'.$cohort->id.'" />
        //     //     '.$cohort->name.'
        //     // </label>';
        //   }

    //     $mform->addElement('html', '<div class="form-group row  fitem  ">
    //     <div class="col-lg-3 col-md-4 col-form-label pb-0 pt-0">
    //     </div>
    //     <div class="col-lg-9 col-md-8 checkbox p-0">
    //         <div class="form-check d-flex align-items-center p-0">
    //             <input type="checkbox" name="test" class="form-check-input " value="1" id="id_test">
    //                 <label for="id_test" class="mb-0 ml-2 text-paragraph">
    //                     test
    //                 </label>
    //             <div class="ml-2 d-flex align-items-center align-self-start">
                    
    //             </div>
    //         </div>
    //         <div class="form-control-feedback invalid-feedback" id="id_error_test">
                
    //         </div>
    //     </div>
    // </div>');
        
        // $mform->addElement('checkbox', 'ratingtime', get_string('ratingtime', 'forum'));

        // $mform->addElement('html', '<input type="text" name="coucuoououou" value="dzqdzq2" />');
        // $mform->addElement('html', '<input type="checkbox" name="coucuoououou" value="dzqdzq2" />');

        // $mform->addElement('text', 'coucuoououou', 'coucuoououou');
        // $mform->setType('coucuoououou', PARAM_TEXT);
        
        // $mform->addElement(
        //     'filepicker',
        //     'image',
        //     'Image',
        //     null,
        //     array(
        //         'subdirs' => 0, 'areamaxbytes' => 11111111111111, 'maxfiles' => 1,
        //         'accepted_types' => array('.png', '.jpg', '.jpeg')
        //     )
        // );

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
