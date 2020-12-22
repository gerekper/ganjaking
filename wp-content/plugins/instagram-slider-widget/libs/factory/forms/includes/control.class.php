<?php
	/**
	 * The file contains the base class for all controls.
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

	if( !class_exists('Wbcr_FactoryForms436_Control') ) {

		/**
		 * The base class for all controls.
		 *
		 * @since 1.0.0
		 */
		abstract class Wbcr_FactoryForms436_Control extends Wbcr_FactoryForms436_FormElement {

			/**
			 * Is this element a control?
			 *
			 * @since 1.0.0
			 * @var bool
			 */
			public $is_control = true;

			/**
			 * Is this element a complex control?
			 *
			 * @since 1.0.0
			 * @var bool
			 */
			public $is_complex_control = false;

			/**
			 * A provider that is used to get values.
			 *
			 * @since 1.0.0
			 * @var Wbcr_IFactoryForms436_ValueProvider
			 */
			protected $provider = null;

			/**
			 * Create a new instance of the control.
			 *
			 * @param mixed[] $options
			 * @param FactoryForms436_Form $form
			 * @param null $provider
			 * @since 1.0.0
			 * @return void
			 */
			public function __construct($options, $form, $provider = null)
			{
				parent::__construct($options, $form);
				$this->provider = $provider;
			}

			/**
			 * Sets a provider for the control.
			 *
			 * @since 1.0.0
			 * @param IFactoryForms436_ValueProvider $provider
			 * @return void
			 */
			public function setProvider($provider)
			{
				$this->provider = $provider;
			}

			/**
			 * Returns a control name used to save data with a provider.
			 *
			 * The method can return if the control have several elements.
			 *
			 * @since 1.0.0
			 * @return string[]|string|null A control name.
			 */
			public function getName()
			{
				return isset($this->options['name'])
					? $this->options['name']
					: null;
			}

			/**
			 * Prints a control name used to save data with a provider.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function printName()
			{
				$name = $this->getName();
				if( is_array($name) ) {
					echo $name[0];
				} else echo $name;
			}

			/**
			 * Returns a control scope.
			 *
			 * @since 1.0.0
			 * @return string|null A control scope.
			 */
			public function getScope()
			{
				return isset($this->options['scope'])
					? $this->options['scope']
					: null;
			}

			/**
			 * Prints a control scope.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function printScope()
			{
				echo $this->getScope();
			}

			/**
			 * Returns a name of control on a form (scope + _ + name)
			 *
			 * @since 1.0.0
			 * @param null|string $name
			 * @return array|null|string|string[]
			 */
			public function getNameOnForm($name = null)
			{
				$scope = $this->getScope();
				$name = !$name
					? $this->getName()
					: $name;

				if( is_array($name) ) {
					$names = array();
					foreach($name as $item) {
						$names[] = empty($scope)
							? $item
							: $scope . '_' . $item;
					}

					return $names;
				}

				if( empty($scope) ) {
					return $name;
				}
				if( empty($name) ) {
					return null;
				}

				return $scope . '_' . $name;
			}

			/**
			 * Prints a control name on a form.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function printNameOnForm()
			{
				$name = $this->getNameOnForm();

				if( is_array($name) ) {
					echo $name[0];
				} else {
					echo $name;
				}
			}

			/**
			 * Returns a submit value of the control by a given name.
			 *
			 * @since 1.0.0
			 * @param string $name
			 * @param string $sub_name
			 * @return string
			 */
			public function getSubmitValue($name, $sub_name)
			{
				$name_on_form = $this->getNameOnForm($name);

				$raw_value = isset($_POST[$name_on_form])
					? $_POST[$name_on_form]
					: null;

				$value = $raw_value;

				if( is_array($value) ) {
					$value = array_map('sanitize_text_field', $value);
					$value = implode(',', $value);
				} else {
					$value = sanitize_text_field($value);
				}

				return $this->filterValue($value, $raw_value);
			}

			/**
			 * @param $value
			 * @param $raw_value
			 * @return mixed
			 */
			protected function filterValue($value, $raw_value)
			{
				$sanitize_func = $this->getOption('filter_value');

				// if the data options is a valid callback for an object method
				if( !empty($sanitize_func) && ((is_array($sanitize_func) && count($sanitize_func) == 2 && gettype($sanitize_func[0]) == 'object') || function_exists($sanitize_func)) ) {
					return call_user_func_array($sanitize_func, array($value, $raw_value));
				}

				return $value;
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
			 * @since 3.1.0
			 * @return mixed[]
			 */
			public function getValuesToSave()
			{
				$values = array();
				$name = $this->getName();

				if( is_array($name) ) {
					$i = 0;

					foreach($name as $single_name) {
						$sub_name = $this->getSubName($single_name);
						if( !$sub_name ) {
							$sub_name = $i;
							$i++;
						}
						$values[$single_name] = $this->getSubmitValue($single_name, $sub_name);
					}

					return $values;
				}

				$values[$name] = $this->getSubmitValue($name, null);

				return $values;
			}

			/**
			 * Returns an initial value of control that is used to render the control first time.
			 *
			 * @since 1.0.0
			 * @return mixed;
			 */
			public function getValue($index = null, $multiple = false)
			{
				if( isset($this->options['value']) ) {
					if( is_array($this->options['value']) ) {
						if( $index !== null ) {
							return $this->options['value'][$index];
						} else return $this->options['value'];
					} else {
						return $this->options['value'];
					}
				}

				$default = null;
				if( isset($this->options['default']) ) {
					if( is_array($this->options['default']) ) {
						if( $index !== null ) {
							$default = $this->options['default'][$index];
						} else $default = $this->options['default'];
					} else {
						$default = $this->options['default'];
					}
				}

				if( $this->provider ) {
					$name = $this->getName();

					if( is_array($name) ) {

						$values = array();
						$i = 0;

						foreach($name as $single_name) {
							$sub_name = $this->getSubName($single_name);
							if( !$sub_name ) {
								$sub_name = $i;
								$i++;
							}
							$values[$sub_name] = $this->provider->getValue($single_name, isset($default[$sub_name])
								? $default[$sub_name]
								: null);
						}

						if( $index !== null ) {
							return $values[$index];
						}

						return $values;
					} else {
						return $this->provider->getValue($this->getName(), $default, $multiple);
					}
				}

				return $default;
			}

			/**
			 * Shows the control.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function render()
			{
				$this->addCssClass('factory-from-control-' . $this->type);

				// if the control is off, then ignore it
				$off = $this->getOption('off', false);

				if( $off ) {
					return;
				}
				
				$this->beforeHtml();
				$this->html();
				$this->afterHtml();
			}

			/**
			 * A virtual method that is executed before rendering html markup of the control.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function beforeHtml()
			{
			}

			/**
			 * A virtual method that is executed after rendering html markup of the control.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function afterHtml()
			{
			}

			/**
			 * Renders the html markup for the control.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function html()
			{
			}

			/**
			 * Returns a layout option.
			 *
			 * @since 1.0.0
			 * @param string $option_name A layout option to return.
			 * @param mixed $default A default value to return if the option doesn't exist.
			 * @return mixed
			 */
			public function getLayoutOption($option_name, $default)
			{
				if( !isset($this->options['layout']) ) {
					return $default;
				}
				if( !isset($this->options['layout'][$option_name]) ) {
					return $default;
				}

				return $this->options['layout'][$option_name];
			}

			/**
			 * Splits the control name by '__' and return the right part.
			 *
			 * For example, if the $control_name is 'control__color', then returns 'color'.
			 * Throws an error if the control name cannot be splitted.
			 *
			 * @since 3.1.0
			 * @param string $control_name
			 * @return string
			 */
			protected function getSubName($control_name)
			{

				$parts = explode('__', $control_name, 2);
				if( !isset($parts[1]) ) {
					return null;
				}

				return $parts[1];
			}
		}
	}