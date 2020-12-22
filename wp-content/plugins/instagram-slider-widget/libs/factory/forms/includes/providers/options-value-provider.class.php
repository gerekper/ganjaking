<?php
	/**
	 * The file contains the class of Factory Option Value Provider.
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

	if( !class_exists('Wbcr_FactoryForms436_OptionsValueProvider') ) {

		/**
		 * Factory Options Value Provider
		 *
		 * This provide stores form values in the wordpress options.
		 *
		 * @since 1.0.0
		 */
		class Wbcr_FactoryForms436_OptionsValueProvider implements Wbcr_IFactoryForms436_ValueProvider {

			/**
			 * A prefix that will be added to all option names.
			 *
			 * @since 1.0.0
			 * @var string
			 */
			public $scope;

			/**
			 * Values to save $optionName => $optionValue
			 *
			 * @since 1.0.0
			 * @var mixed[]
			 */
			private $values = array();

			/**
			 * Creates a new instance of an options value provider.
			 */
			public function __construct(Wbcr_Factory439_Plugin $plugin)
			{
				$this->plugin = $plugin;
			}

			/**
			 * @since 1.0.0
			 */
			public function init()
			{
				// nothing to do
			}

			/**
			 * @since 1.0.0
			 */
			public function saveChanges()
			{
				foreach((array)$this->values as $option_name => $option_value) {
					$this->plugin->updatePopulateOption($option_name, $option_value);
				}
			}

			public function getValue($name, $default = null, $multiple = false)
			{
				$value = $this->plugin->getPopulateOption($name, $default);

				if( $value === 'true' || $value === true ) {
					$value = 1;
				}
				if( $value === 'false' || $value === false ) {
					$value = 0;
				}

				return $value;
			}

			/**
			 * @param string $name
			 * @param mixed $value
			 */
			public function setValue($name, $value)
			{
				$value = empty($value)
					? $value
					: stripslashes($value);

				$this->values[$name] = $value;
			}
		}
	}