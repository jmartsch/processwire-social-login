<?php namespace ProcessWire;

/**
 * SocialLoginProcessRegistration
 *
 */

class SocialLoginProcessRegistration extends Process
{

    public function __construct($fields = array())
    {
        $this->set('profileFields', $fields);
    }

    public function process()
    {
        $this->user = $this->wire('user');
        $this->wire('processHeadline', $this->_("Profile:") . ' ' . $this->user->name); // Primary Headline (precedes the username)
        $this->form = $this->buildForm();

        if (count($this->wire('notices'))) {
            foreach ($this->wire('notices') as $notice) {
                if ($notice instanceof NoticeError) {
                    echo '<div class="wire-error callout warning"><p class="error">' . $notice->text . '</p></div>';
                } else {
                    echo '<div class="wire-notice callout notice">' . $notice->text . '</div>';
                }
            }
            $this->session->removeNotices();
        }

        if ($this->input->post->submit_save_profile) {
            $this->processInput($this->form);
            $this->session->redirect("./");
        } elseif ($this->input->get->action == 'activation') {
            /**
             * @see https://processwire.com/talk/topic/4066-activate-user-account-via-email/
             *
             * this will pull the username and activation code from the url
             * it is extremely important to clean the string
             */
            $activate_userid = $this->sanitizer->text($this->input->get->userid);
            $activate_hash = $this->sanitizer->text($this->input->get->hash);
            $this_user = $this->wire('pages')->find("template=user,id=$activate_userid, include=all")->first();

            if (is_object($this_user) && isset($this_user->id)) {
                if (strcmp($this_user->activation_key, $activate_hash) == 0 && $this_user->is(Page::statusUnpublished)) {
                    SocialLogin::showMessage(__("Thank you! Your account has been activated!"), "callout success");

                    $activate_user = $this_user;
                    $activate_user->of(false);
                    $activate_user->activation_key = "0";
                    $activate_user->removeStatus(Page::statusUnpublished);
                    $activate_user->save();
                    $this->session->forceLogin($this_user);

//                    $user = $this->session->login($this_user, $this_user->pass);
//                    if ($user) {
//                        $this->session->redirect($page->path);
//                    } else {
//                        echo __("Login failed!");
//                    }
                } else {
                    SocialLogin::showMessage(__("There was an error activating your account! Please contact us!"), 'callout warning');
                }
            } else {
                SocialLogin::showMessage(__("Sorry, but we couldn't find your account in our database!"), "callout warning");
            }

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
        $form->attr('id', 'SocialLoginProcessRegistration');
        $form->attr('action', './');
        $form->attr('method', 'post');
        $form->attr('enctype', 'multipart/form-data');
//        $form->attr( 'autocomplete', 'off' );

        if (!empty($this->profileFields)) {
            foreach ($this->profileFields as $field) {
                $field = $this->user->fields->getFieldContext($field);
                $inputfield = $field->getInputfield($this->user);
                $inputfield->value = $this->user->get($field->name);

                if ($field->type instanceof FieldtypeEmail) {
                    $inputfield->attr('required', 'required');
                }
                if ($field->type instanceof FieldtypeFile) {
                    $inputfield->noAjax = true;
                }

                $form->add($inputfield);
            }

            $field = $this->modules->get('InputfieldSubmit');
            $field->attr('id+name', 'submit_save_profile');
            $field->addClass('head_button_clone InputfieldLabelHidden waves');
            $form->add($field);
        } else {
            echo __('Error: Registration\'s form is not defined. Maybe you need to add/save the fields in the module\'s configuration page.');
        }

        return $form;
    }

    /**
     * Save the user
     */
    protected function processInput(Inputfield $form)
    {
        $thisSocialLogin = $this->modules->get('SocialLogin');
        $errors = array();
        $required_fields = array();
        $fields = array();

        $pass2 = $this->input->post->_pass;

        foreach ($form as $key => $f) {
            if ($f->name == 'submit_save_profile') {
                continue;
            }

            $post_value = $this->input->post->{$f->name};
            switch ($f->type) {
                case 'text':
                    $the_value = $this->sanitizer->text($post_value);
                    break;
                case 'textarea':
                    $the_value = $this->sanitizer->textarea($post_value);
                    break;
                case 'email':
                    $the_value = $this->sanitizer->email($post_value);
                    if (empty($the_value)) {
                        $errors[$f->name] = __('Email address is required.');
                        break 2;
                    }
                    if (count($this->users->find("email=" . $the_value))) {
                        $errors[$f->name] = sprintf(__('Email address "%s" already in use by another user.'), $the_value);
                        break 2;
                    }
                    $f->required = true;

                    break;
                case 'checkbox':
                    $the_value = isset($post_value) ? 1 : 0;
                    break;
                case 'password':
                    $the_value = trim($post_value);
                    $f->required = true;

                    if (preg_match('/[\s\t\r\n]/', $the_value)) $errors['_pass'] = __("Password contained whitespace.");
                    elseif ($the_value != $pass2) $errors['_pass'] = __("Passwords do not match.");
                    elseif (strlen($the_value) < 6) $errors['_pass'] = __("Password is less than required number of characters.");
                    elseif (!preg_match('/[a-zA-Z]/', $the_value)) $errors['_pass'] = __("Password does not contain at least one letter (a-z A-Z).");
                    elseif (!preg_match('/\d/', $the_value)) $errors['_pass'] = __("Password does not contain at least one digit (0-9).");
                    if (isset($errors['_pass'])) break 2;

                    break;
                default:
                    $the_value = $post_value;
                    break;
            }

            $fields[$f->name] = $the_value;

            if ($f->required) {
                // Check for required fields and make sure they have a value
                if (($f->type == 'checkbox' && $post_value == 0) || !strlen($post_value)) {
                    $errors[$f->name] = __("Field required");
                } elseif ($f->type == 'file' && empty($_FILES[$req]['name'][0])) {
                    $errors[$f->name] = __("Select files to upload.");
                }
            }
        }

        // validate CSRF token first to check if it's a valid request
        if (!$this->session->CSRF->hasValidToken()) {
            $errors['csrf'] = __("Form submit was not valid, please try again.");
        }

        // add errors to session and return
        if (!empty($errors)) {
            foreach ($errors as $key => $err) {
                $this->session->error($err);
            }
            return;
        }

        // Create user
        $new_user = new User();
        $new_user->of(false);

        foreach ($fields as $key => $value) {
            $new_user->$key = $value;
        }

        // Use the email as user's page path or a randomly generated string
        if (isset($fields['email']) && $thisSocialLogin->use_email_as_username) {
            $new_user->name = $this->sanitizer->pageName($fields['email']);
        } else {
            $new_user->name = SocialLogin::passwordGen();
        }

        // @todo - maybe this should be defined from module options
        if (isset($this->config->usersPageIDs[1])) {
            $new_user->parent = $this->config->usersPageIDs[1];
        }

        if ($thisSocialLogin->enabled_activation_code) {
            $new_user->addStatus(Page::statusUnpublished);

            // Generates a new random string and attach the admin defined secret key.
            $new_user->activation_key = md5(SocialLogin::passwordGen() . $thisSocialLogin->social_login_registration_secret);
        }

        $new_user->roles->add($this->roles->get("guest"));
        $new_user->save();
        $new_user->of(true);

        if ($thisSocialLogin->enabled_activation_code) {
            $activation_link = $thisSocialLogin->siteUrl . SocialLogin::registerPath . "/?action=activation&userid=" . $new_user->id . "&hash=" . $new_user->activation_key;

            // Get mail body and replace tokens
            $reg_mail_body = $thisSocialLogin->social_login_registration_email_body;
            $reg_mail_body = str_replace('{{activation-link}}', $activation_link, $reg_mail_body);

            $mail = wireMail();
            $mail->to($new_user->email);
            $mail->from($thisSocialLogin->social_login_registration_email_from);
            $mail->subject($thisSocialLogin->social_login_registration_email_subject);
            $mail->body($reg_mail_body);
            $mail->bodyHTML($reg_mail_body);
            $reg_email_sent = $mail->send();

            if ($reg_email_sent) {
                $this->session->message(__('Thank you for registering: an activation link was sent to the specified email address.'));
            }

            // log activation code to the logger
            if ($this->config->debug) {
                error_log("RESGISTERING ACTIVATION CODE: " . $activation_link);
            }
//            return $this->halt(); // prevents further rendering of the template. works only with PW 2.6.8+
        } else {
            $this->session->message(__('Thank you for registering.'));
        }

//        $this->session->redirect( $this->config->urls->root );
    }

}