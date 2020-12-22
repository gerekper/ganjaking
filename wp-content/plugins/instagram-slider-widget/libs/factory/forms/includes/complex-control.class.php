<?php
	/**
	 * The file contains the base class for all complex controls.
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
	if( !class_exists('Wbcr_FactoryForms436_ComplexControl') ) {
		/**
		 * The base class for all controls.
		 *
		 * @since 1.0.0
		 */
		abstract class Wbcr_FactoryForms436_ComplexControl extends Wbcr_FactoryForms436_Control {

			/**
			 * Is this element a complex control?
			 *
			 * @since 1.0.0
			 * @var bool
			 */
			public $is_complex_control = true;

			/**
			 * Contains a set of internal controls.
			 *
			 * @since 1.0.0
			 * @var Wbcr_FactoryForms436_Control[]
			 */
			public $inner_controls = array();

			/**
			 * Sets a provider for the control.
			 *
			 * @since 1.0.0
			 * @param Wbcr_IFactoryForms436_ValueProvider $provider
			 * @return void
			 */
			public function setProvider($provider)
			{
				$this->provider = $provider;

				foreach($this->inner_controls as $control) {
					$control->setProvider($provider);
				}
			}

			/**
			 * Returns a control name used to save data with a provider.
			 *
			 * The method can return if the control have several elements.
			 *
			 * @since 1.0.0
			 * @return array|string|null A control name.
			 */
			public function getName()
			{
				$names = array();

				foreach($this->inner_controls as $control) {
					$inner_names = $control->getName();
					if( is_array($inner_names) ) {
						$names = array_merge($names, $inner_names);
					} else $names[] = $inner_names;
				}

				return $names;
			}

			/**
			 * Returns an array of value to save received after submission of a form.
			 *
			 * @see getSubmitValue
			 *
			 * The array has the following format:
			 * array(
			 *    'control-name1' => 'value1',
			 *    'control-name2__sub-name1' => 'value2'
			 *    'control-name2__sub-name2' => 'value3'
			 * )
			 *
			 * @since 1.0.0
			 * @return array
			 */
			public function getValuesToSave()
			{
				$values = array();

				foreach($this->inner_controls as $control) {
					$inner_values = $control->getValuesToSave();
					if( is_array($inner_values) ) {
						$values = array_merge($values, $inner_values);
					} else $values[] = $inner_values;
				}

				return $values;
			}

			/**
			 * Returns an initial value of control that is used to render the control first time.
			 *
			 * @since 1.0.0
			 * @param null $index
			 * @param bool $multiple
			 * @return array
			 */
			public function getValue($index = null, $multiple = false)
			{

				$values = array();
				foreach($this->inner_controls as $control) {
					$inner_values = array_merge($values, $control->getValue());
					if( is_array($inner_values) ) {
						$values = array_merge($values, $inner_values);
					} else $values[] = $inner_values;
				}

				if( $index !== null ) {
					return $values[$index];
				} else {
					return $values;
				}
			}
		}
	}
