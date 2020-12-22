<?php
	/**
	 * The file contains a class that represnets an abstraction for forms.
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

	// creating a license manager for each plugin created via the factory
	add_action('wbcr_factory_forms_436_plugin_created', 'wbcr_factory_forms_436_plugin_created');

	function wbcr_factory_forms_436_plugin_created($plugin)
	{
		$plugin->forms = new Wbcr_FactoryForms436_Manager($plugin);
	}

	if( !class_exists('Wbcr_FactoryForms436_Manager') ) {

		class Wbcr_FactoryForms436_Manager {

			// ----------------------------------------------------
			// Static fields and methods
			// ----------------------------------------------------

			/**
			 * This array contains data to use a respective control.
			 *
			 * @since 1.0.0
			 * @var array
			 */
			public static $registered_controls = array();

			/**
			 * Registers a new control.
			 *
			 * @since 1.0.0
			 * @param mixed[] $item Control data having the following format:
			 *                      type      => a control type
			 *                      class     => a control php class
			 *                      include   => a path to include control code
			 * @return void
			 */
			public function registerControl($item)
			{
				self::$registered_controls[$item['type']] = $item;
				require_once $item['include'];
			}

			/**
			 * Registers a set of new controls.
			 *
			 * @see FactoryForms436_Form::registerControl()
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function registerControls($data)
			{
				foreach($data as $item)
					$this->registerControl($item);
			}

			/**
			 * This array contains holder data to use a respective control holder.
			 *
			 * @since 1.0.0
			 * @var array
			 */
			public static $registered_holders = array();

			/**
			 * Registers a new holder.
			 *
			 * @since 1.0.0
			 * @param mixed[] $item Holder data having the follwoin format:
			 *                      type      => a control holder type
			 *                      class     => a control holder php class
			 *                      include   => a path to include control holder code
			 * @return void
			 */
			public function registerHolder($item)
			{
				self::$registered_holders[$item['type']] = $item;
				require_once $item['include'];
			}

			/**
			 * Registers a set of new holder controls.
			 *
			 * @see FactoryForms436_Form::registerHolder()
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function registerHolders($data)
			{
				foreach($data as $item)
					$this->registerHolder($item);
			}

			/**
			 * This array contains custom form element data to use a respective elements.
			 *
			 * @since 1.0.0
			 * @var array
			 */
			public static $registered_custom_elements = array();

			/**
			 * Registers a new custom form element.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function registerCustomElement($item)
			{
				self::$registered_custom_elements[$item['type']] = $item;
				require_once $item['include'];
			}

			/**
			 * Registers a set of new custom form elements.
			 *
			 * @see FactoryForms436_Form::registerCustomElement()
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function registerCustomElements($data)
			{
				foreach($data as $item)
					$this->registerCustomElement($item);
			}

			/**
			 * Contains a set of layouts registered for forms.
			 *
			 * @since 1.0.0
			 * @var mixed[]
			 */
			public static $form_layouts = array();

			/**
			 * Registers a new layout for forms.
			 *
			 * @since 1.0.0
			 * @param array $data A layout data. Has the following format:
			 *                      name      => a name of the layout
			 *                      class     => a layout php class
			 *                      include   => a path to include layout code
			 * @return void
			 */
			public function registerFormLayout($data)
			{
				self::$form_layouts[$data['name']] = $data;
			}

			/**
			 * Extra propery that determines which UI is used in the admin panel.
			 *
			 * @since 1.0.0
			 * @var string
			 */
			public static $temper;

			/**
			 * A flat to register control only once.
			 *
			 * @since 3.0.7
			 * @var bool
			 */
			public static $controls_registered = false;
		}
	}
	if( !class_exists('Wbcr_FactoryForms436_Form') ) {
		/**
		 * An abstraction for forms.
		 */
		class Wbcr_FactoryForms436_Form {

			// ----------------------------------------------------
			// Object fields and methods
			// ----------------------------------------------------

			/**
			 * A value provider of the form that is used to save and load values.
			 *
			 * @since 1.0.0
			 * @var Wbcr_IFactoryForms436_ValueProvider
			 */
			private $provider;

			/**
			 * A prefix that will be used for names of input fields in the form.
			 *
			 * @since 1.0.0
			 * @var string
			 */
			public $scope;

			/**
			 * A form name that is used to call hooks and filter data.
			 *
			 * @since 1.0.0
			 * @var string
			 */
			public $name = 'default';

			/**
			 * It's not yet input controls. The array contains names of input controls and their
			 * options that are used to render and process input controls.
			 *
			 * @since 1.0.0
			 * @var mixed[]
			 */
			protected $items = array();

			/**
			 * Full set of input controls available after building the form.
			 *
			 * The array contains objects.
			 *
			 * @since 1.0.0
			 * @var array
			 */
			private $controls = array();

			/**
			 * A layout for the form.
			 *
			 * @since 1.0.0
			 * @var string
			 */
			public $form_layout;

			/**
			 * A current form layout used to render a form.
			 *
			 * @since 1.0.0
			 * @var Wbcr_FactoryForms436_FormLayout
			 */
			public $layout;

			/**
			 * Creates a new instance of a form.
			 *
			 * @since 1.0.0
			 * @param string $options Contains form options to setup.
			 */

			/**
			 * Creates a new instance of a form.
			 *
			 * @since 1.0.0
			 * @param array $options
			 * @param Wbcr_Factory439_Plugin $plugin
			 */
			public function __construct(array $options = array(), Wbcr_Factory439_Plugin $plugin)
			{
				// register controls once, when the first form is created
				if( !Wbcr_FactoryForms436_Manager::$controls_registered ) {

					do_action('wbcr_factory_forms_436_register_controls', $plugin);

					//if( !empty($plugin) ) {
					do_action('wbcr_factory_forms_436_register_controls_' . $plugin->getPluginName(), $plugin);
					//}

					Wbcr_FactoryForms436_Manager::$controls_registered = true;
				}

				$this->scope = isset($options['scope']) ? $options['scope'] : null;
				$this->name = isset($options['name']) ? $options['name'] : $this->name;
				/*$this->all_sites = isset($options['all_sites'])
					? $options['all_sites']
					: false;*/

				if( isset($options['formLayout']) ) {
					$this->form_layout = $options['formLayout'];
				} else {
					$this->form_layout = 'bootstrap-3';
				}

				Wbcr_FactoryForms436_Manager::$temper = 'flat';
			}

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
			}

			/**
			 * Adds items into the form.
			 *
			 * It's base method to use during configuration form.
			 *
			 * @since 1.0.0
			 * @param array $array An array of items.
			 */
			public function add($array)
			{
				if( (bool)count(array_filter(array_keys($array), 'is_string')) ) {
					$this->items[] = $array;
				} else {
					$this->items = array_merge($this->items, $array);
				}
			}

			/**
			 * Returns items to render.
			 *
			 * Has the follwoing hooks:
			 *  'factory_form_items' ( $formName, $items ) to filter form controls before building.
			 *
			 * @since 1.0.0
			 * @return mixed[] Items to render.
			 */
			public function getItems()
			{
				return apply_filters('wbcr_factory_439_form_items', $this->items, $this->name);
			}

			/**
			 * Returns form controls (control objects).
			 *
			 * @since 1.0.0
			 * @return Wbcr_FactoryForms436_Control[]
			 */
			public function getControls()
			{
				if( !empty($this->controls) ) {
					return $this->controls;
				}
				$this->createControls();

				return $this->controls;
			}

			/**
			 * Builds a form items to the control objects ready to use.
			 *
			 * @param null $holder
			 * @return Wbcr_FactoryForms436_Control[]
			 */

			public function createControls($holder = null)
			{
				$items = ($holder == null) ? $this->getItems() : $holder['items'];

				foreach($items as $item) {

					if( $this->isControlHolder($item) && $this->isControl($item) ) {

						$this->controls[] = $this->createControl($item);
						$this->createControls($item);
						// if a current item is a control holder
					} elseif( $this->isControlHolder($item) ) {

						$this->createControls($item);
						// if a current item is an input control
					} elseif( $this->isControl($item) ) {

						$this->controls[] = $this->createControl($item);
						// if a current item is an input control
					} elseif( $this->isCustomElement($item) ) {

						// nothing

						// otherwise, show the error
					} else {
						die('[ERROR] Invalid item.');
					}
				}

				return $this->controls;
			}

			/**
			 * Create an element.
			 *
			 * @since 1.0.0
			 * @param array $item Item data.
			 * @return Wbcr_FactoryForms436_FormElement|null A form element.
			 */
			public function createElement($item)
			{

				if( $this->isControl($item) ) {
					return $this->createControl($item);
				} elseif( $this->isControlHolder($item) ) {
					return $this->createHolder($item);
				} elseif( $this->isCustomElement($item) ) {
					return $this->createCustomElement($item);
				} else {
					printf('[ERROR] The element with the type <strong>%s</strong> was not found.', $item['type']);
					exit;
				}
			}

			/**
			 * Creates a set of elements.
			 *
			 * @since 1.0.0
			 * @param mixed[] $item Data of items.
			 * @return Wbcr_FactoryForms436_FormElement[] Created elements.
			 */
			public function createElements($items = array())
			{
				$objects = array();
				foreach($items as $item)
					$objects[] = $this->createElement($item);

				return $objects;
			}

			/**
			 * Create a control.
			 *
			 * @since 1.0.0
			 * @param array $item Item data.
			 * @return Wbcr_FactoryForms436_Control A control object.
			 */
			public function createControl($item)
			{
				$object = null;

				if( is_array($item) ) {

					$control_data = Wbcr_FactoryForms436_Manager::$registered_controls[$item['type']];

					require_once($control_data['include']);

					$options = $item;
					$options['scope'] = $this->scope;

					$object = new $control_data['class']($options, $this);
				} elseif( gettype($item) == 'object' ) {
					$object = $item;
				} else {
					die('[ERROR] Invalid input control.');
				}

				$object->setProvider($this->provider);

				return $object;
			}

			/**
			 * Create a control holder.
			 *
			 * @since 1.0.0
			 * @param array $item Item data.
			 * @return Wbcr_FactoryForms436_Holder A control holder object.
			 */
			public function createHolder($item)
			{
				$object = null;

				if( is_array($item) ) {

					$holderData = Wbcr_FactoryForms436_Manager::$registered_holders[$item['type']];
					require_once($holderData['include']);

					$object = new $holderData['class']($item, $this);
				} elseif( gettype($item) == 'object' ) {
					$object = $item;
				} else {
					die('[ERROR] Invalid control holder.');
				}

				return $object;
			}

			/**
			 * Create a custom form element.
			 *
			 * @since 1.0.0
			 * @param mixed $item Item data.
			 * @return Wbcr_FactoryForms436_FormElement A custom form element object.
			 */
			public function createCustomElement($item)
			{
				$object = null;

				if( is_array($item) ) {

					$data = Wbcr_FactoryForms436_Manager::$registered_custom_elements[$item['type']];
					require_once($data['include']);

					$options = $item;
					$object = new $data['class']($options, $this);
				} elseif( gettype($item) == 'object' ) {
					$object = $item;
				} else {
					die('[ERROR] Invalid custom form element.');
				}

				return $object;
			}

			/**
			 * Renders a form.
			 *
			 * @since 1.0.0
			 * @param mixed[] $options Options for a form layout.
			 * @return void
			 */
			public function html($options = array())
			{

				if( !isset(Wbcr_FactoryForms436_Manager::$form_layouts[$this->form_layout]) ) {
					die(sprintf('[ERROR] The form layout %s was not found.', $this->form_layout));
				}

				// include a render code
				$layout_data = Wbcr_FactoryForms436_Manager::$form_layouts[$this->form_layout];
				require_once($layout_data['include']);

				$this->connectAssets();

				if( $this->provider ) {
					$this->provider->init();
				}
				$layout = new $layout_data['class']($options, $this);
				$this->layout = $layout;
				$this->layout->render();
			}

			/**
			 * Connects assets (css and js).
			 *
			 * @since 1.0.0
			 * @param mixed[] $options Options for a form layout.
			 * @return void
			 */
			private function connectAssets()
			{

				$this->connectAssetsForItems();
				$layout_data = Wbcr_FactoryForms436_Manager::$form_layouts[$this->form_layout];

				if( $layout_data['name'] == 'default' ) {
					if( isset($layout_data['style']) ) {
						wp_enqueue_style('wbcr-factory-form-000-default-layout', $layout_data['style']);
					}
					if( isset($layout_data['script']) ) {
						wp_enqueue_script('wbcr-factory-form-000-default-layout-', $layout_data['script']);
					}
				} else {
					if( isset($layout_data['style']) ) {
						wp_enqueue_style('wbcr-factory-form-layout-' . $layout_data['name'], $layout_data['style']);
					}
					if( isset($layout_data['script']) ) {
						wp_enqueue_script('wbcr-factory-form-layout-' . $layout_data['name'], $layout_data['script']);
					}
				}
			}


			/**
			 * Connects scripts and styles of form items.
			 *
			 * @since 1.0.0
			 * @param mixed[] $items Items for which it's nessesary to connect scripts and styles.
			 * @return void
			 */
			public static function connectAssetsForItems($items = array())
			{
				foreach($items as $item)
					self::connectAssetsForItem($item);
			}

			/**
			 * Connects scripts and styles of form item.
			 *
			 * @since 1.0.0
			 * @param mixed[] $item Item for which it's nessesary to connect scripts and styles.
			 * @return void
			 */
			public static function connectAssetsForItem($item)
			{
				if( !is_array($item) ) {
					return;
				}

				$type = $item['type'];

				$haystack = array();
				if( self::isControl($type) ) {
					$haystack = Wbcr_FactoryForms436_Manager::$registered_controls;
				} elseif( self::isControlHolder($type) ) {
					$haystack = Wbcr_FactoryForms436_Manager::$registered_holders;
				}

				if( isset($haystack[$type]) ) {
					if( isset($haystack[$type]['style']) ) {
						$style = $haystack[$type]['style'];
						if( !wp_style_is($style) ) {
							wp_enqueue_style('factory-form-control-' . $type, $style);
						}
					}
					if( isset($haystack[$type]['script']) ) {
						$script = $haystack[$type]['script'];
						if( !wp_script_is($script) ) {
							wp_enqueue_script('factory-form-control-' . $type, $script, array('jquery'));
						}
					}
				}

				if( isset($item['items']) ) {
					self::connectAssetsForItem($item['items']);
				}
			}

			/**
			 * Saves form data by using a specified value provider.
			 *
			 * @since 1.0.0
			 */
			public function save()
			{
				if( !$this->provider ) {
					return null;
				}

				$controls = $this->getControls();

				foreach($controls as $control) {
					$values = $control->getValuesToSave();

					foreach($values as $key_to_save => $value_to_save) {
						$this->provider->setValue($key_to_save, $value_to_save);
					}
				}

				$this->provider->saveChanges();
			}

			/**
			 * Returns true if a given item is an input control item.
			 *
			 * @since 1.0.0
			 * @param mixed[] $item
			 * @return bool
			 */
			public static function isControl($item)
			{
				return isset(Wbcr_FactoryForms436_Manager::$registered_controls[$item['type']]);
			}

			/**
			 * Returns true if a given item is an control holder item.
			 *
			 * @since 1.0.0
			 * @param mixed[] $item
			 * @return bool
			 */
			public static function isControlHolder($item)
			{
				return isset(Wbcr_FactoryForms436_Manager::$registered_holders[$item['type']]);
			}

			/**
			 * Returns true if a given item is html markup.
			 *
			 * @since 1.0.0
			 * @param mixed[] $item
			 * @return bool
			 */
			public static function isCustomElement($item)
			{
				return isset(Wbcr_FactoryForms436_Manager::$registered_custom_elements[$item['type']]);
			}
		}
	}