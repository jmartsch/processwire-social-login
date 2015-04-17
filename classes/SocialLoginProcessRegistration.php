<?php

/**
 * SocialLoginProcessRegistration
 *
 */

class SocialLoginProcessRegistration extends Process {

	public function __construct( $fields = array() ) {
		$this->set( 'profileFields', $fields );
	}

	public function process() {
		$this->user = $this->wire( 'user' );
		$this->setFuel( 'processHeadline', $this->_( "Profile:" ) . ' ' . $this->user->name ); // Primary Headline (precedes the username)
		$form = $this->buildForm();

		if ( $this->input->post->submit_save_profile ) {
			$this->processInput( $form );
			$this->session->redirect( "./" );
		}
		else {
			echo $form->render();
		}
	}

	/**
	 * Build the form fields for adding a page
	 */
	protected function buildForm() {
		$form = $this->modules->get( 'InputfieldForm' );

		$form->attr( 'id', 'SocialLoginProcessRegistration' );
		$form->attr( 'action', './' );
		$form->attr( 'method', 'post' );
		$form->attr( 'enctype', 'multipart/form-data' );
		$form->attr( 'autocomplete', 'off' );

		if ( ! empty( $this->profileFields ) ) {
			foreach ( $this->profileFields as $field ) {
				$field             = $this->user->fields->getFieldContext( $field );
				$inputfield        = $field->getInputfield( $this->user );
				$inputfield->value = $this->user->get( $field->name );
				
				if ( $field->type instanceof FieldtypeFile ) {
					$inputfield->noAjax = true;
				}

				$form->add( $inputfield );
			}

			$field = $this->modules->get( 'InputfieldSubmit' );
			$field->attr( 'id+name', 'submit_save_profile' );
			$field->addClass( 'head_button_clone InputfieldLabelHidden' );
			$form->add( $field );
		}
		else {
			echo __( 'Error: Registration\'s form is not defined. Maybe you need to add/save the fields in the module\'s configuration page.' );
		}

		return $form;
	}

	/**
	 * Save the user
	 */
	protected function processInput( Inputfield $form ) {
		$errors = array();
		$required_fields = array();
		$fields = array();

		$pass2 = $this->input->post->_pass;

		foreach ( $form as $key => $f ) {
			if ( $f->name == 'submit_save_profile' ) {
				continue;
			}

			$post_value = $this->input->post->{$f->name};
			switch ( $f->type ) {
			    case 'text':
			    	$the_value = $this->sanitizer->text( $post_value );
			    	break;
			    case 'textarea':
			    	$the_value = $this->sanitizer->textarea( $post_value );
			    	break;
			    case 'email':
			    	$the_value = $this->sanitizer->email( $post_value );
			    	if ( empty( $the_value ) ) {
			    		$errors[$f->name] = __( 'Email address is required.' );
			    		continue;
			    	}
			    	if ( count( $this->users->find( "email=" . $the_value ) ) ) {
						$errors[$f->name] = sprintf( __( 'Email address "%s" already in use by another user.' ), $the_value );
						continue;
					}
					$f->required = true;

			    	break;
			    case 'checkbox':
			    	$the_value = isset( $post_value ) ? 1 : 0;
			    	break;
			    case 'password':
			    	$the_value = trim( $post_value );
			    	$f->required = true;

					// Password mismatch
					if ( $the_value != $pass2 ) {
						$errors['_pass'] = __( 'Password mismatch' );
					}

			    	break;
			    default:
			    	$the_value = $post_value;
			    	break;
			}

			$fields[$f->name] = $the_value;
			
			if ( $f->required ) {
				// Check for required fields and make sure they have a value
				
				if ( $f->type == 'checkbox' && $post_value == 0 ) {
					$errors[$f->name] = "Field required";
				}
				elseif ( $f->type == 'file' && empty($_FILES[$req]['name'][0]) ) {
					$errors[$f->name] = "Select files to upload.";
				}
				elseif ( ! strlen( $post_value ) ) {
					$errors[$f->name] = "Field required";
				}
			}
		}

	    // validate CSRF token first to check if it's a valid request
	    if ( ! $this->session->CSRF->hasValidToken() ) {
	        $errors['csrf'] = __( "Form submit was not valid, please try again." );
	    }

	    if ( ! empty( $errors ) ) {
	    	var_export($errors); // @todo - show these errors in a better way
	    	return;
	    }

		// Create user
		$u = new User();

		foreach ( $fields as $key => $value ) {
			$u->$key = $value;
		}

		// Maybe in some cases can be needed a SEO-friendly URL
		// @todo - add an option to the module to allow the choice between random path and email-sanitized path
		// or maybe other stuff like firstname-lastname or custom username.
		if ( isset( $fields['email'] ) && false ) {
			$u->name = $fields['email'];
		}
		else {
			// Random page path for the user
			$u->name = SocialLogin::passwordGen();
		}

		// @todo - maybe this should be defined from module options
		if ( isset( $this->config->usersPageIDs[1] ) ) {
			$u->parent = $this->config->usersPageIDs[1];
		}

		$u->roles->add( $this->roles->get( "guest" ) );
		$u->save();

		// @todo - send confirm email

		$this->session->message( __( 'Thank you for registering.' ) );
		$this->session->redirect( $this->config->urls->root );
	}

}