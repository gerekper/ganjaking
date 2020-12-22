<?php

	/**
	 * Hidden Input Control
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

	if( !class_exists('Wbcr_FactoryForms436_HiddenControl') ) {

		class Wbcr_FactoryForms436_HiddenControl extends Wbcr_FactoryForms436_Control {

			public $type = 'hidden';

			/**
			 * Shows the html markup of the control.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function html()
			{
				$value = esc_attr($this->getValue());
				$name_on_form = $this->getNameOnForm();

				$this->addHtmlAttr('id', $name_on_form);
				$this->addHtmlAttr('name', $name_on_form);
				$this->addHtmlAttr('value', $value);
				$this->addHtmlAttr('type', 'hidden');

				?>
				<input <?php $this->attrs() ?>/>
			<?php
			}
		}
	}
