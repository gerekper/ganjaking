<?php

	/**
	 * Dropdown and Colors List Control
	 *
	 * Main options:
	 *  name            => a name of the control
	 *  // see FactoryForms436_DropdownControl
	 * 'dropdown' => array(
	 *    // a callback to return items or an array of items to select
	 *      'data' => OPanda_ThemeManager::getThemes(OPanda_Items::getCurrentItemName(), 'dropdown'),
	 *      'default' => 'default',
	 *      'value' => 'value' // a value to show in the control
	 *    ),
	 * // see FactoryForms436_RadioColorsControl
	 * 'colors' => array(
	 *   // a callback to return items or an array of items to select
	 *   'data' => array(
	 *        array('default', '#75649b'),
	 *        array('black', '#222'),
	 *        array('light', '#fff3ce'),
	 *        array('forest', '#c9d4be'),
	 *   ),
	 *    'value' => 'value' // a value to show in the control
	 *    'default' => 'default', // a default value of the control if the "value" option is not specified
	 *  ),
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

	if( !class_exists('Wbcr_FactoryForms436_DropdownAndColorsControl') ) {
		
		class Wbcr_FactoryForms436_DropdownAndColorsControl extends Wbcr_FactoryForms436_ComplexControl {

			public $type = 'dropdown-and-colors';

			public function __construct($options, $form, $provider = null)
			{
				parent::__construct($options, $form, $provider);

				if( !isset($options['dropdown']) ) {
					$options['dropdown'] = array();
				}

				$options['dropdown'] = array_merge($options['dropdown'], array(
					'scope' => isset($options['scope'])
						? $options['scope']
						: 'opanda',
					'name' => $this->options['name'] . '__dropdown',
				));

				if( !isset($options['colors']) ) {
					$options['colors'] = array();
				}

				$options['colors'] = array_merge($options['colors'], array(
					'scope' => isset($options['scope'])
						? $options['scope']
						: 'opanda',
					'name' => $this->options['name'] . '__colors',
				));

				$this->dropdown = new Wbcr_FactoryForms436_DropdownControl($options['dropdown'], $form, $provider);
				$this->colors = new Wbcr_FactoryForms436_RadioColorsControl($options['colors'], $form, $provider);
				$this->inner_controls = array($this->dropdown, $this->colors);

				$colors = $this->colors->getOption('data');

				if( empty($colors) ) {
					$dropdown_value = $this->dropdown->getValue();
					$dOptions = $this->dropdown->getOption('data', array());

					foreach($dOptions as $option) {
						if( $option['value'] == $dropdown_value && isset($option['data']['colors']) ) {
							$colors_options = json_decode(htmlspecialchars_decode($option['data']['colors']));
							$this->colors->setOption('data', $colors_options);
						}
					}
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
				?>
				<script>
				</script>
				<div <?php $this->attrs() ?>>
					<div class="factory-control-row">
						<div class="factory-dropdown-wrap">
							<?php $this->dropdown->render(); ?>
						</div>
						<div class="factory-colors-wrap">
							<?php $this->colors->render(); ?>
						</div>
					</div>
					<div class="factory-picker-target"></div>
				</div>
			<?php
			}
		}
	}