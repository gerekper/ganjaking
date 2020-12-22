<?php

	/**
	 * WP Editor Control
	 *
	 * Main options:
	 *  name            => a name of the control
	 *  value           => a value to show in the control
	 *  default         => a default value of the control if the "value" option is not specified
	 *  tinymce         => an array of options for tinymce
	 * @link http://codex.wordpress.org/Function_Reference/wp_editor
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, Webcraftic Ltd
	 *
	 * @package factory-forms
	 * @since 1.0.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	if( !class_exists('Wbcr_FactoryForms436_WpEditorControl') ) {

		class Wbcr_FactoryForms436_WpEditorControl extends Wbcr_FactoryForms436_Control {

			public $type = 'wp-editor';

			/**
			 * Preparing html attributes and options for tinymce.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function beforeHtml()
			{

				if( empty($this->options['tinymce']) ) {
					$this->options['tinymce'] = array();
				}

				if( !isset($this->options['tinymce']['content_css']) ) {
					$this->options['tinymce']['content_css'] = FACTORY_FORMS_436_URL . '/assets/css/editor.css';
				}
			}

			/**
			 * Shows the html markup of the control.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function html()
			{
				$name_on_form = $this->getNameOnForm();

				$value = $this->getValue();

				?>
				<div class='factory-form-wp-editor'>
					<?php wp_editor($value, $name_on_form, array(
						'textarea_name' => $name_on_form,
						'wpautop' => false,
						'teeny' => true,
						'tinymce' => $this->getOption('tinymce', array())
					)); ?>
				</div>
			<?php
			}

			/**
			 * Returns a submit value of the control by a given name.
			 *
			 * @since 1.0.0
			 * @return mixed
			 */
			public function getSubmitValue($name, $subName)
			{
				$name_on_form = $this->getNameOnForm($name);

				$value = isset($_POST[$name_on_form])
					? $_POST[$name_on_form]
					: null;

				if( is_array($value) ) {
					$value = implode(',', $value);
				}

				return wp_kses_post($value);
			}
		}
	}

