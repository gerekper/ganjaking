<?php
	/**
	 * Html Markup
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

	if( !class_exists('Wbcr_FactoryForms436_Html') ) {

		class Wbcr_FactoryForms436_Html extends Wbcr_FactoryForms436_CustomElement {

			public $type = 'html';

			/**
			 * Shows the html markup of the element.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function html()
			{
				$html = $this->getOption('html', '');

				// if the data options is a valid callback for an object method
				if( (is_array($html) && count($html) == 2 && gettype($html[0]) == 'object') || function_exists($html) ) {

					call_user_func($html, $this);

					return;
				}

				// if the data options is an array of values
				echo $html;
			}
		}
	}
