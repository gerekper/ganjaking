<?php
	/**
	 * The file contains the class of Tab Control Holder.
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

	if( !class_exists('Wbcr_FactoryForms436_ControlGroupItem') ) {

		/**
		 * Tab Control Holder
		 *
		 * @since 1.0.0
		 */
		class Wbcr_FactoryForms436_ControlGroupItem extends Wbcr_FactoryForms436_Holder {

			/**
			 * A holder type.
			 *
			 * @since 1.0.0
			 * @var string
			 */
			public $type = 'control-group-item';


			/**
			 * Here we should render a beginning html of the tab.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function beforeRendering()
			{
				$this->addCssClass('control-group-item');
				$this->addCssClass('factory-control-group-item-' . $this->options['name']);

				if( $this->parent->getValue() == $this->options['name'] ) {
					$this->addCssClass('current');

					foreach($this->elements as $val) {
						$val->setOption('isActive', 1);
					}
				} else {
					foreach($this->elements as $val) {
						$val->setOption('isActive', 0);
					}
				}

				?>
				<div <?php $this->attrs() ?>>
			<?php
			}

			/**
			 * Here we should render an end html of the tab.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function afterRendering()
			{
				?>
				</div>
			<?php
			}
		}
	}