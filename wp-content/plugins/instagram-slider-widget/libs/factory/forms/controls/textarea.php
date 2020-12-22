<?php

	/**
	 * Textarea Control
	 *
	 * Main options:
	 *  name            => a name of the control
	 *  value           => a value to show in the control
	 *  default         => a default value of the control if the "value" option is not specified
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

	if( !class_exists('Wbcr_FactoryForms436_TextareaControl') ) {

		class Wbcr_FactoryForms436_TextareaControl extends Wbcr_FactoryForms436_Control {

			public $type = 'textarea';

			/**
			 * Returns a submit value of the control by a given name.
			 *
			 * @since 1.0.0
			 * @return mixed
			 */
			public function getSubmitValue($name, $subName)
			{
				$name_on_form = $this->getNameOnForm($name);

				$raw_value = isset($_POST[$name_on_form])
					? $_POST[$name_on_form]
					: null;

				$value = $raw_value;

				if( is_array($value) ) {
					$value = array_map('sanitize_textarea_field', $value);
					$value = implode(',', $value);
				} else {
					$value = sanitize_textarea_field($value);
				}

				return $this->filterValue($value, $raw_value);
			}

			/**
			 * Preparing html attributes before rendering html of the control.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function beforeHtml()
			{
				$name_on_form = $this->getNameOnForm();
				$height = (int)$this->getOption('height', 100);

				$this->addCssClass('form-control');
				$this->addHtmlAttr('name', $name_on_form);
				$this->addHtmlAttr('id', $name_on_form);
				$this->addHtmlAttr('style', 'min-height:' . $height . 'px');
			}

			/**
			 * Shows the html markup of the control.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function html()
			{
				?>
				<textarea <?php $this->attrs(); ?>><?php echo esc_textarea($this->getValue()); ?></textarea>
			<?php
			}
		}
	}
