<?php
	/**
	 * The file contains the base class for all form element (controls, holders).
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

	if( !class_exists('Wbcr_FactoryForms436_FormElement') ) {

		/**
		 * The base class for all form element (controls, holders).
		 *
		 * Provides several methods to build html markup of an element.
		 *
		 * @since 1.0.0
		 */
		abstract class Wbcr_FactoryForms436_FormElement {

			/**
			 * A type of an elemnt.
			 *
			 * @since 1.0.0
			 * @var boolean
			 */
			protected $type = null;

			/**
			 * An html attribute builder.
			 *
			 * @since 1.0.0
			 * @var Wbcr_FactoryForms436_HtmlAttributeBuilder
			 */
			private $html_builder;

			/**
			 * Element options.
			 *
			 * @since 1.0.0
			 * @var array
			 */
			public $options = array();

			/**
			 * A parent form.
			 *
			 * @since 1.0.0
			 * @var Wbcr_FactoryForms436_Form
			 */
			protected $form;

			/**
			 * A form layout.
			 *
			 * @since 1.0.0
			 * @var Wbcr_FactoryForms436_FormLayout
			 */
			protected $layout;

			/**
			 * Is this element a control?
			 *
			 * @since 1.0.0
			 * @var bool
			 */
			public $is_control = false;

			/**
			 * Is this element a control holder?
			 *
			 * @since 1.0.0
			 * @var bool
			 */
			public $is_holder = false;

			/**
			 * Is this element a custom form element?
			 *
			 * @since 1.0.0
			 * @var bool
			 */
			public $is_custom = false;

			/**
			 * Creates a new instance of a form element.
			 *
			 * @since 1.0.0
			 * @param mixed[] $options A holder options.
			 * @param Wbcr_FactoryForms436_Form $form A parent form.
			 */
			public function __construct($options, $form)
			{
				$this->options = $options;
				$this->form = $form;
				$this->layout = $form->layout;

				$this->html_builder = new Wbcr_FactoryForms436_HtmlAttributeBuilder();

				if( isset($this->options['cssClass']) ) {
					$this->html_builder->addCssClass($this->options['cssClass']);
				}

				if( isset($this->options['htmlData']) ) {
					foreach($this->options['htmlData'] as $data_key => $data_value) {
						$this->html_builder->addHtmlData($data_key, $data_value);
					}
				}

				if( isset($this->options['htmlAttrs']) ) {
					foreach($this->options['htmlAttrs'] as $attr_key => $attr_value) {
						$this->html_builder->addHtmlAttr($attr_key, $attr_value);
					}
				}

				$this->addCssClass('factory-' . $this->type);
			}


			/**
			 * Sets options for the control.
			 *
			 * @since 1.0.0
			 * @param mixed[] $options
			 * @return void
			 */
			public function setOptions($options)
			{
				$this->options = $options;
			}

			/**
			 * Gets options of the control.
			 *
			 * @since 1.0.0
			 * @return mixed[] $options
			 */
			public function getOptions()
			{
				return $this->options;
			}

			/**
			 * Sets a new value for a given option.
			 *
			 * @since 1.0.0
			 * @param string $name An option name to set.
			 * @param mixed $value A value to set.
			 * @return void
			 */
			public function setOption($name, $value)
			{
				$this->options[$name] = $value;
			}

			/**
			 * Gets an option value or default.
			 *
			 * @since 1.0.0
			 * @param string $name An option name to get.
			 * @param mixed $default A default value
			 * @return mixed|null
			 */
			public function getOption($name, $default = null)
			{
				return isset($this->options[$name])
					? $this->options[$name]
					: $default;
			}

			/**
			 * Prints an option value or default.
			 *
			 * @since 1.0.0
			 * @param string $name An option name to get.
			 * @param mixed $default A default value
			 * @return void
			 */
			public function option($name, $default = null)
			{
				$value = $this->getOption($name, $default);
				echo $value;
			}

			/**
			 * Adds a new CSS class for the element.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function addCssClass($class)
			{
				$this->html_builder->addCssClass($class);
			}

			/**
			 * Prints CSS classes of the element.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function cssClass()
			{
				$this->html_builder->printCssClass();
			}

			/**
			 * Adds a new html attribute.
			 *
			 * @since 1.0.0
			 * @param string $data_key
			 * @param string $data_value
			 */
			protected function addHtmlData($data_key, $data_value)
			{
				$this->html_builder->addHtmlData($data_key, $data_value);
			}

			/**
			 * Adds a new html attribute.
			 *
			 * @since 1.0.0
			 * @param string $attr_name
			 * @param string $attr_value
			 * @return void
			 */
			protected function addHtmlAttr($attr_name, $attr_value)
			{
				$this->html_builder->addHtmlAttr($attr_name, $attr_value);
			}

			/**
			 * Prints all html attributes, including css classes and data.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function attrs()
			{
				$this->html_builder->printAttrs();
			}

			/**
			 * Returns an element title.
			 *
			 * @since 1.0.0
			 * @return string|bool
			 */
			public function getTitle()
			{
				if( isset($this->options['title']) ) {
					return $this->options['title'];
				}

				return false;
			}

			/**
			 * Returns true if an element has title.
			 *
			 * @since 1.0.0
			 * @return bool
			 */
			public function hasTitle()
			{
				$title = $this->getTitle();

				return !empty($title);
			}

			/**
			 * Prints an element title.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function title()
			{
				echo $this->getTitle();
			}

			/**
			 * Returns an element hint.
			 *
			 * @since 1.0.0
			 * @return string
			 */
			public function getHint()
			{
				if( isset($this->options['hint']) ) {
					return $this->options['hint'];
				}

				return false;
			}

			/**
			 * Returns true if an element has hint.
			 *
			 * @since 1.0.0
			 * @return bool
			 */
			public function hasHint()
			{
				$hint = $this->getHint();

				return !empty($hint);
			}

			/**
			 * Prints an element hint.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function hint($esc = false)
			{
				echo $esc
					? esc_html($this->getHint())
					: $this->getHint();
			}

			/**
			 * Returns an element name.
			 *
			 * @since 1.0.0
			 * @return string
			 */
			public function getName()
			{

				if( empty($this->options['name']) && !empty($this->options['title']) ) {
					$this->options['name'] = str_replace(' ', '-', $this->options['title']);
					$this->options['name'] = strtolower($this->options['name']);
				}

				if( !isset($this->options['name']) ) {
					$this->options['name'] = $this->type . '-' . rand();
				}

				return $this->options['name'];
			}

			/**
			 * Prints an element name.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function name()
			{
				echo $this->getName();
			}

			/**
			 * Returns a form name
			 *
			 * @since 1.0.0
			 * @return string
			 */
			public function getFormName()
			{
				return $this->form->name;
			}

			/**
			 * Returns an element type.
			 *
			 * @since 1.0.0
			 * @return string
			 */
			public function getType()
			{
				return $this->type;
			}

			/**
			 * Returns an element icon.
			 *
			 * @since 1.0.0
			 * @return string
			 */
			public function getIcon()
			{
				if( isset($this->options['icon']) ) {
					return $this->options['icon'];
				}

				return false;
			}

			/**
			 * Returns true if an element has a icon.
			 *
			 * @since 1.0.0
			 * @return bool
			 */
			public function hasIcon()
			{
				$icon = $this->getIcon();

				return !empty($icon);
			}

			/**
			 * Prints an element icon.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function icon()
			{
				echo $this->getIcon();
			}
		}
	}
