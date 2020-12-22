<?php
	/**
	 * The file contains Html Attribute Builder.
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

	if( !class_exists('Wbcr_FactoryForms436_HtmlAttributeBuilder') ) {
		/**
		 * Html Attribute Builder
		 *
		 * @since 1.0.0
		 */
		class Wbcr_FactoryForms436_HtmlAttributeBuilder {

			/**
			 * An array to store css classes.
			 *
			 * @since 1.0.0
			 * @var string[]
			 */
			protected $css_classes = array();

			/**
			 * An array to store html attributes.
			 *
			 * @since 1.0.0
			 * @var string[]
			 */
			protected $html_attrs = array();

			/**
			 * An array to store html data.
			 *
			 * @since 1.0.0
			 * @var string[]
			 */
			protected $html_data = array();

			/**
			 * Adds a new CSS class.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function addCssClass($class)
			{
				if( !is_array($class) ) {
					$this->css_classes[] = $class;
				} else {
					$this->css_classes = array_merge($this->css_classes, $class);
				}
			}

			/**
			 * Prints CSS classes.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function printCssClass()
			{
				echo implode(' ', $this->css_classes);
			}

			/**
			 * Adds a new html data item.
			 *
			 * @since 1.0.0
			 * @param string $dataKey
			 * @param string $dataValue
			 * @return void
			 */
			public function addHtmlData($dataKey, $dataValue)
			{
				$this->html_data[$dataKey] = $dataValue;
			}

			/**
			 * Prints html data items.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function printHtmlData()
			{
				foreach($this->html_data as $key => $value) {
					echo 'data-' . $key . '="' . $value . '" ';
				}
			}

			/**
			 * Adds a new html attribute.
			 *
			 * @since 1.0.0
			 * @param string $attr_name
			 * @param string $attr_value
			 * @return void
			 */
			public function addHtmlAttr($attr_name, $attr_value)
			{
				$this->html_attrs[$attr_name] = $attr_value;
			}

			/**
			 * Prints all html attributes, including css classes and data.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function printAttrs()
			{
				$attrs = $this->html_attrs;

				if( !empty($this->css_classes) ) {
					$attrs['class'] = implode(' ', $this->css_classes);
				}

				foreach($this->html_data as $data_key => $data_value) {
					$attrs['data-' . $data_key] = $data_value;
				}

				foreach($attrs as $key => $value) {
					echo $key . '="' . $value . '" ';
				}
			}
		}
	}