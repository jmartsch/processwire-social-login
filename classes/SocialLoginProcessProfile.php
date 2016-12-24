<?php namespace ProcessWire;

/**
 * Process SocialLogin Profile
 *
 * Add the fields you want to be configurable once the user has logged in.
 */

class SocialLoginProcessProfile extends ProcessProfile
{

    public function __construct($fields = array())
    {
        $this->set('profileFields', $fields);
    }

    public function init()
    {
        return parent::init();
    }

    public function process()
    {
        $this->user = $this->wire('user');
        $this->wire('processHeadline', $this->_("Profile:") . ' ' . $this->user->name); // Primary Headline (precedes the username)

        $this->form = $this->buildForm();

        if ($this->input->post->submit_save_profile) {
            $this->processInput($form);
            $this->session->redirect("./");
        } else {
            return $this->form;
        }
    }

    /**
     * Build the form fields for adding a page
     */
    protected function buildForm()
    {
        $form = $this->modules->get('InputfieldForm');

        $form->attr('id', 'SocialLoginProcessProfile');
        $form->attr('action', './');
        $form->attr('method', 'post');
        $form->attr('enctype', 'multipart/form-data');
//        $form->attr('autocomplete', 'off');

        if (!empty($this->profileFields)) {
            foreach ($this->profileFields as $field) {
                $field = $this->user->fields->getFieldContext($field);
                $inputfield = $field->getInputfield($this->user);
                $inputfield->value = $this->user->get($field->name);

                // We want to prevent to render a Radio field which consists
                // of just one choice: for example, if there is just one language
                // enabled, there is no need to show this unchangeable language??
                // if ( $field->inputfield == 'InputfieldRadios' ) {
                // 	$field_attr  = $inputfield->getAttributes();
                // 	$field_value = $field_attr['value']->data['title'];
                // 	if ( ! is_object( $field_value ) ) {
                // 		continue;
                // 	}
                // }

                if ($field->type instanceof FieldtypeFile) {
                    $inputfield->noAjax = true;
                }

                $form->add($inputfield);
            }

            $field = $this->modules->get('InputfieldSubmit');
            $field->attr('id+name', 'submit_save_profile');
            $field->addClass('head_button_clone InputfieldLabelHidden');
            $form->add($field);
        } else {
            echo __('Error: Login\'s form is not defined. Maybe you need to add/save the fields in the module\'s configuration page.');
        }

        return $form;
    }
}