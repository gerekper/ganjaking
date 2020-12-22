<?php

	/**
	 * Color and Opacity
	 *
	 * Main options:
	 *  name            => a name of the control
	 *  value           => a value to show in the control
	 *  default         => a default value of the control if the "value" option is not specified
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, Webcraftic Ltd
	 *
	 * @package core
	 * @since 1.0.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	if( !class_exists('Wbcr_FactoryForms436_ColorAndOpacityControl') ) {
	}

	class Wbcr_FactoryForms436_ColorAndOpacityControl extends Wbcr_FactoryForms436_ComplexControl {

		public $type = 'color-and-opacity';

		public function __construct($options, $form, $provider = null)
		{
			parent::__construct($options, $form, $provider);

			if( !isset($options['color']) ) {
				$options['color'] = array();
			}

			$options['color'] = array_merge($options['color'], array(
				'name' => $this->options['name'] . '__color',
				'default' => isset($this->options['default'])
					? $this->options['default']['color']
					: '#1e8cbe',
				'pickerTarget' => '.factory-control-' . $this->options['name'] . ' .factory-picker-target'
			));

			if( !isset($options['opacity']) ) {
				$options['opacity'] = array();
			}

			$options['opacity'] = array_merge($options['opacity'], array(
				'name' => $this->options['name'] . '__opacity',
				'default' => isset($this->options['default'])
					? $this->options['default']['opacity']
					: 100,
				'units' => '%',
				'range' => array(0, 100),
				'way' => 'slider'
			));

			$this->color = new Wbcr_FactoryForms436_ColorControl($options['color'], $form, $provider);
			$this->opacity = new Wbcr_FactoryForms436_IntegerControl($options['opacity'], $form, $provider);

			$this->innerControls = array($this->color, $this->opacity);
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
			<div <?php $this->attrs() ?>>
				<div class="factory-control-row">
					<div class="factory-color-wrap">
						<?php $this->color->html() ?>
					</div>
					<div class="factory-opacity-wrap">
						<?php $this->opacity->html() ?>
					</div>
				</div>
				<div class="factory-picker-target"></div>
			</div>
		<?php
		}
	}
