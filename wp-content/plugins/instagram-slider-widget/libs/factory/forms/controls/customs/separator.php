<?php
	/**
	 * Separator Markup
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

	if( !class_exists('Wbcr_FactoryForms436_Separator') ) {
		class Wbcr_FactoryForms436_Separator extends Wbcr_FactoryForms436_CustomElement {

			public $type = 'separator';

			/**
			 * Shows the html markup of the element.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function html()
			{
				?>
				<div <?php $this->attrs() ?>></div>
			<?php
			}
		}
	}
