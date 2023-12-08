<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use Elementor\Plugin;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Rules
 *
 * Adds display condition to elements
 *
 */
class Theplus_Widgets_Rules extends Elementor\Widget_Base {

	/**
	 * Display Conditions 
	 *
	 * Holds all the rules for display on the frontend
	 *
	 * @access protected
	 *
	 * @var bool
	 */
	protected $conditions = [];

	/**
	 * Display Conditions 
	 *
	 * Holds all the rules for display on the frontend
	 *
	 * @access protected
	 *
	 * @var bool
	 */
	protected $conditions_options = [];
	public static $tmp_location = [];
	
	
	public function __construct() {
	
		$theplus_options=get_option('theplus_options');
		$plus_extras=theplus_get_option('general','extras_elements');
		
		if((isset($plus_extras) && empty($plus_extras) && empty($theplus_options)) || (!empty($plus_extras) && in_array('plus_display_rules',$plus_extras))){
			
			$this->plus_add_sections_actions();
			$this->plus_add_actions();
			
		}
		
	}
	
	/**
	 * A list of scripts that the widgets is depended in
	 *
	 **/
	public function get_script_depends() {
		return [];
	}

	public function get_name() {
		return 'plus-widgets-rules';
	}
	
	/**
	 * Is disabled by default
	 *
	 * @return bool
	 */
	public static function is_default_disabled() {
		return true;
	}
	
	
	/**
	 * Add common sections
	 *
	 *
	 * @access protected
	 */
	protected function plus_add_sections_actions() {

		//Activate sections for widgets
		add_action( 'elementor_pro/element/common/section_custom_css/after_section_end', [ $this, 'add_rules_controls' ], 10, 2 );

		//Activate sections for sections
		add_action( 'elementor/element/section/section_custom_css/after_section_end', [ $this, 'add_rules_controls' ], 10, 2 );
		
		//Activate sections for widgets if elementor pro
		add_action( 'elementor/element/common/section_custom_css_pro/after_section_end', [ $this, 'add_rules_controls' ], 10, 2 );
		
		// add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'add_rules_controls' ], 10, 2 );
		// add_action( 'elementor/element/column/_section_responsive/after_section_end', [ $this, 'add_rules_controls' ], 10, 2 );
		// add_action( 'elementor/element/common/section_custom_css_pro/after_section_end', [ $this, 'add_rules_controls' ], 10, 2 );

		$experiments_manager = Plugin::$instance->experiments;
		if($experiments_manager->is_feature_active( 'container' )){
			add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'add_rules_controls' ], 10, 2  );
		}
		
	}
	
	/**
	 * Set the Rules options array
	 *
	 *
	 * @access private
	 */
	private function set_rules_options() {

		$this->rules_options = [
			[
				'label'		=> esc_html__( 'Visitor', 'theplus' ),
				'options' 	=> [
					'authentication' 	=> esc_html__( 'Login Status', 'theplus' ),
					'role' 				=> esc_html__( 'User Role', 'theplus' ),
					'os' 				=> esc_html__( 'Operating System', 'theplus' ),
					'browser' 			=> esc_html__( 'Browser', 'theplus' ),
					'location' 			=> esc_html__( 'Location', 'theplus' ),
				],
			],
			[
				'label'			=> esc_html__( 'Date and Time', 'theplus' ),
				'options' 		=> [
					'date' 		=> esc_html__( 'Current Date', 'theplus' ),
					'time' 		=> esc_html__( 'Time of Day', 'theplus' ),
					'day' 		=> esc_html__( 'Day of Week', 'theplus' ),
					'timerange' 		=> esc_html__( 'Time Range', 'theplus' ),
				],
			],
			[
				'label'					=> esc_html__( 'Single', 'theplus' ),
				'options' 				=> [
					'page' 				=> esc_html__( 'Page', 'theplus' ),
					'post' 				=> esc_html__( 'Post', 'theplus' ),
					'static_page' 		=> esc_html__( 'Static Page', 'theplus' ),
					'post_type' 		=> esc_html__( 'Post Type', 'theplus' ),
					'term_single' 		=> esc_html__( 'Term', 'theplus' ),
				],
			],
			[
				'label'					=> esc_html__( 'Archive', 'theplus' ),
				'options' 				=> [
					'taxonomy_archive' 	=> esc_html__( 'Taxonomy', 'theplus' ),
					'term_archive' 		=> esc_html__( 'Term', 'theplus' ),
					'post_type_archive'	=> esc_html__( 'Post Type', 'theplus' ),
					'date_archive'		=> esc_html__( 'Date', 'theplus' ),
					'author_archive'	=> esc_html__( 'Author', 'theplus' ),
					'search_results'	=> esc_html__( 'Search', 'theplus' ),
				],
			],
			[
				'label'					=> esc_html__( 'Language', 'theplus' ),
				'options' 				=> [
					'site_language' 	=> esc_html__( 'Site', 'theplus' ),
					'browser_language' 	=> esc_html__( 'Browser', 'theplus' ),
				],
			],
			[
				'label'					=> esc_html__( 'URL', 'theplus' ),
				'options' 				=> [
					'url_string' 	=> esc_html__( 'String', 'theplus' ),
					'url_parameter' 	=> esc_html__( 'Parameter', 'theplus' ),
				],
			],
			[
				'label'					=> esc_html__( 'Shortcode', 'theplus' ),
				'options' 				=> [
					'tp_shortcode' 	=> esc_html__( 'Shortcode', 'theplus' ),
				],
			],
		];
		
		if(class_exists('woocommerce')) {
			$this->rules_options[] = [
				'label'					=> esc_html__( 'WooCommerce', 'theplus' ),
				'options' 				=> [
					'woo_cart_pro_cat' 	=> esc_html__( 'Woo : In Cart Product Category', 'theplus' ),
					'woo_cart_pro_tag' 	=> esc_html__( 'Woo : In Cart Product Tag', 'theplus' ),
					'woo_cart_subtotal' 	=> esc_html__( 'Woo : Cart Subtotal', 'theplus' ),
					'woo_cart_total' 	=> esc_html__( 'Woo : Cart Total', 'theplus' ),					
					'woo_cart_item' 	=> esc_html__( 'Woo : Items in Cart', 'theplus' ),	
					'woo_purchase_total' 	=> esc_html__( 'Woo : Purchase Order Total', 'theplus' ),				
					'woo_last_purchase' 	=> esc_html__( 'Woo : Purchase Date', 'theplus' ),
					'woo_first_purchase' 	=> esc_html__( 'Woo : First Purchase Date', 'theplus' ),
					'woo_last_purchase_date' 	=> esc_html__( 'Woo : Last Purchase Date', 'theplus' ),
					'woo_purchase_pro_cat' 	=> esc_html__( 'Woo : In Purchase Product Category', 'theplus' ),
					'woo_purchase_pro_name' 	=> esc_html__( 'Woo : In Purchase Product', 'theplus' ),
					'woo_purchase_item' 	=> esc_html__( 'Woo : Order(s) Placed', 'theplus' ),
					'woo_cur_pro_cat' 	=> esc_html__( 'Woo : Current Product Category', 'theplus' ),
					'woo_cur_pro_price' 	=> esc_html__( 'Woo : Current Product Price', 'theplus' ),
					'woo_cur_pro_stock' 	=> esc_html__( 'Woo : Current Product Stock', 'theplus' ),
					'woo_cart_product' 	=> esc_html__( 'Woo : Cart Product', 'theplus' ),
					'woo_po_bill_city' 	=> esc_html__( 'Woo : Last Order Billing City', 'theplus' ),
					'woo_po_bill_state' 	=> esc_html__( 'Woo : Last Order Billing State', 'theplus' ),
					'woo_po_bill_country' 	=> esc_html__( 'Woo : Last Order Billing Country', 'theplus' ),
					'woo_po_bill_postcode' 	=> esc_html__( 'Woo : Last Order Billing Postcode', 'theplus' ),
					'woo_po_ship_city' 	=> esc_html__( 'Woo : Last Order Shipping City', 'theplus' ),
					'woo_po_ship_state' 	=> esc_html__( 'Woo : Last Order Shipping State', 'theplus' ),
					'woo_po_ship_country' 	=> esc_html__( 'Woo : Last Order Shipping Country', 'theplus' ),
					'woo_po_ship_postcode' 	=> esc_html__( 'Woo : Last Order Shipping Postcode', 'theplus' ),
				],
			];
		}
		
		if( class_exists('ACF') ) {
			$this->rules_options[] = [
				'label'					=> esc_html__( 'Advanced Custom Fields', 'theplus' ),
				'options' 				=> [
					'acf_text' 	=> esc_html__( 'ACF : Text Fields', 'theplus' ),
					'acf_select' 	=> esc_html__( 'ACF : Selection', 'theplus' ),
					'acf_button_group' 	=> esc_html__( 'ACF : Button Group', 'theplus' ),
					'acf_boolean' 		=> esc_html__( 'ACF : Boolean', 'theplus' ),
					'acf_datetime'	=> esc_html__( 'ACF : Date / Time', 'theplus' ),
					'acf_post'	=> esc_html__( 'ACF : Post', 'theplus' ),
					'acf_taxonomy'		=> esc_html__( 'ACF : Taxonomy', 'theplus' ),
				],
			];
			if( class_exists('ACFE') ) {
				$this->rules_options[] = [
					'label'					=> esc_html__( 'Advanced Custom Fields: Extended', 'theplus' ),
					'options' 				=> [
						'acfe_image_selector'		=> esc_html__( 'ACFE : Image Selector', 'theplus' ),
					],
				];
			}
		}

		//toolset
		if( defined('WPCF_VERSION') ) {
			$this->rules_options[] = [
				'label'					=> esc_html__( 'Toolset', 'theplus' ),
				'options' 				=> [
					'toolset_text'		=> esc_html__( 'Text', 'theplus' ),
					'toolset_number'		=> esc_html__( 'Number', 'theplus' ),
					'toolset_radio'		=> esc_html__( 'Radio', 'theplus' ),
					'toolset_checkbox'		=> esc_html__( 'Checkbox', 'theplus' ),
					'toolset_select'		=> esc_html__( 'Select', 'theplus' ),
					'toolset_checkboxes'		=> esc_html__( 'Checkboxes', 'theplus' ),
				],
			];
		}

		//pods
		if( defined('PODS_VERSION') ) {
			$this->rules_options[] = [
				'label'					=> esc_html__( 'PODS', 'theplus' ),
				'options' 				=> [
					'pods_text'		=> esc_html__( 'Text', 'theplus' ),					
					'pods_date'		=> esc_html__( 'Date', 'theplus' ),					
					'pods_number'	=> esc_html__( 'Number', 'theplus' ),
					'pods_boolean'	=> esc_html__( 'Boolean', 'theplus' ),
				],
			];
		}
		
		//Jet_Engine
		if(class_exists('Jet_Engine')) {
			$this->rules_options[] = [
				'label'					=> esc_html__( 'Jet Engine', 'theplus' ),
				'options' 				=> [
					'jetengine_text'		=> esc_html__( 'Text', 'theplus' ),
					'jetengine_textarea'	=> esc_html__( 'Text Area', 'theplus' ),
					'jetengine_switcher'	=> esc_html__( 'Switcher', 'theplus' ),
					'jetengine_checkbox'	=> esc_html__( 'Checkbox', 'theplus' ),
					'jetengine_radio'		=> esc_html__( 'Radio', 'theplus' ),
					'jetengine_select'		=> esc_html__( 'Select', 'theplus' ),
					'jetengine_number'		=> esc_html__( 'Number', 'theplus' ),
				],
			];
		}
		
		// EDD Rules
		if ( class_exists( 'Easy_Digital_Downloads', false ) ) {
			$this->rules_options[] = [
				'label'					=> esc_html__( 'Easy Digital Downloads', 'theplus' ),
				'options' 				=> [
					'edd_cart' 			=> esc_html__( 'Cart', 'theplus' ),
				],
			];
		}
	}
	
	/**
	 * Add Controls
	 *
	 *
	 * @access public
	 */
	public function add_rules_controls( $element, $args ) {

		global $wp_roles;

		$default_start_date = date( 'Y-m-d', strtotime( '-3 day' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
		$default_end_date 	= date( 'Y-m-d', strtotime( '+3 day' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
		$default_interval 	= $default_start_date . ' to ' . $default_end_date;

		$element_type = $element->get_type();
		
		$element->start_controls_section(
			'plus_widgets_rules_section',
			[
				'label' => esc_html__( 'Plus Extras : Display Condition', 'theplus' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);
		
		$element->add_control(
			'tp_display_rules_enable',
			[
				'label'			=> esc_html__( 'Display Condition', 'theplus' ),
				'type' 			=> Controls_Manager::SWITCHER,
				'default' 		=> '',
				'label_on' 		=> esc_html__( 'Yes', 'theplus' ),
				'label_off' 	=> esc_html__( 'No', 'theplus' ),
				'return_value' 	=> 'yes',
				'frontend_available'	=> true,
			]
		);
		
		if ( 'widget' === $element_type || 'section' === $element_type || 'container' === $element_type) {
			$element->add_control(
				'tp_display_rules_output',
				[
					'label'		=> esc_html__( 'Keep HTML', 'theplus' ),
					'description' => sprintf( esc_html__( 'If enabled, It will keep HTML on front end and Section will be hidden by using Display:None CSS. If disabled, HTML content will not load.', 'theplus' ), $element_type ),
					'default'	=> 'yes',
					'type' 		=> Controls_Manager::SWITCHER,
					'label_on' 		=> esc_html__( 'Yes', 'theplus' ),
					'label_off' 	=> esc_html__( 'No', 'theplus' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true,
					'condition'	=> [
						'tp_display_rules_enable' => 'yes',
					],
				]
			);
		}

		$element->add_control(
			'tp_display_rules_relation',
			[
				'label'		=> esc_html__( 'Display When', 'theplus' ),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> 'all',
				'options' 	=> [
					'all' 		=> esc_html__( 'All Rules are True', 'theplus' ),
					'any' 		=> esc_html__( 'Any one Rule is True', 'theplus' ),
				],
				'condition'	=> [
					'tp_display_rules_enable' => 'yes',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'tp_rule_key',
			[
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> 'authentication',
				'label_block' => true,
				'groups' 	=> $this->rules_options,
			]
		);
		
		if ( class_exists( 'ACF' ) ) {
			$repeater->add_control(
				'tp_rule_acf_text_name',
				[
					'type' 			=> 'plus-query',
					'post_type' 	=> '',
					'options' 		=> [],
					'placeholder'	=> esc_html__( 'Search Fields', 'theplus' ),
					'description'	=> esc_html__( 'You need to search ACF Text fields ( text, number, range, email, url, password and text area)  by it\'s label. Keep it blank to check if the field is set or not.', 'theplus' ),
					'label_block' 	=> true,
					'multiple'		=> false,
					'query_type'	=> 'acf',
					'query_options'	=> [
						'field_type'	=> [
							'textual',
						],
						'show_field_type' => true,
						'show_type' => false,
						'show_group' => true,
					],
					'condition' 	=> [
						'tp_rule_key' => 'acf_text',
					],
				]
			);
		
			$repeater->add_control(
				'tp_rule_acf_select_name',
				[
					'type' 			=> 'plus-query',
					'post_type' 	=> '',
					'options' 		=> [],
					'placeholder'	=> esc_html__( 'Search Fields', 'theplus' ),
					'description'	=> esc_html__( 'Search ACF fields "Select", "Radio" and "Checkbox" fields by It\'s label.', 'theplus' ),
					'label_block' 	=> true,
					'multiple'		=> false,
					'query_type'	=> 'acf',
					'query_options'	=> [
						'field_type'	=> [
							'select',
						],						
						'show_field_type' => true,
						'show_type' => false,
						'show_group' => true,
					],
					'condition' 	=> [
						'tp_rule_key' => 'acf_select',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_acf_button_group_name',
				[
					'type' 			=> 'plus-query',
					'post_type' 	=> '',
					'options' 		=> [],
					'placeholder'	=> esc_html__( 'Search Fields', 'theplus' ),
					'description'	=> esc_html__( 'Search ACF fields by It\'s label.', 'theplus' ),
					'label_block' 	=> true,
					'multiple'		=> false,
					'query_type'	=> 'acf',
					'query_options'	=> [
						'field_type'	=> [
							'button_group',
						],
						'show_field_type' => true,
						'show_type' => false,
						'show_group' => true,
					],
					'condition' 	=> [
						'tp_rule_key' => 'acf_button_group',
					],
				]
			);
			
			$repeater->add_control(
				'tp_rule_acf_boolean_name',
				[
					'type' 			=> 'plus-query',
					'post_type' 	=> '',
					'options' 		=> [],
					'placeholder'	=> esc_html__( 'Search Fields', 'theplus' ),
					'description'	=> esc_html__( 'Search ACF True / False field by It\'s label.', 'theplus' ),
					'label_block' 	=> true,
					'multiple'		=> false,
					'query_type'	=> 'acf',
					'query_options'	=> [
						'field_type'	=> [
							'boolean',
						],
						'show_field_type' => false,
						'show_type' => false,
						'show_group' => true,
					],
					'condition' 	=> [
						'tp_rule_key' => 'acf_boolean',
					],
				]
			);
			
			$repeater->add_control(
				'tp_rule_acf_datetime_name',
				[
					'type' 			=> 'plus-query',
					'post_type' 	=> '',
					'options' 		=> [],
					'placeholder'	=> esc_html__( 'Search Fields', 'theplus' ),
					'description'	=> esc_html__( 'Search ACF Date/Time field by It\'s label.', 'theplus' ),
					'label_block' 	=> true,
					'multiple'		=> false,
					'query_type'	=> 'acf',
					'query_options'	=> [
						'field_type'	=> [
							'date',
						],
						'show_field_type' => true,
						'show_type' => false,
						'show_group' => true,
					],
					'condition' 	=> [
						'tp_rule_key' => 'acf_datetime',
					],
				]
			);
			
			$repeater->add_control(
				'tp_rule_acf_post_name',
				[
					'type' 			=> 'plus-query',
					'post_type' 	=> '',
					'options' 		=> [],
					'placeholder'	=> esc_html__( 'Search Fields', 'theplus' ),
					'description'	=> esc_html__( 'Search ACF "Post Object" & "Relationship" fields by It\'s label.', 'theplus' ),
					'label_block' 	=> true,
					'multiple'		=> false,
					'query_type'	=> 'acf',
					'query_options'	=> [
						'field_type'	=> [
							'post',
						],
						'show_field_type' => true,
						'show_type' => false,
						'show_group' => true,
					],
					'condition' 	=> [
						'tp_rule_key' => 'acf_post',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_acf_taxonomy_name',
				[
					'type' 			=> 'plus-query',
					'post_type' 	=> '',
					'options' 		=> [],
					'placeholder'	=> esc_html__( 'Search Fields', 'theplus' ),
					'description'	=> esc_html__( 'Search ACF "Taxonomy" fields by It\'s label.', 'theplus' ),
					'label_block' 	=> true,
					'multiple'		=> false,
					'query_type'	=> 'acf',
					'query_options'	=> [
						'field_type'	=> [
							'taxonomy',
						],
						'show_field_type' => false,
						'show_type' => false,
						'show_group' => true,
					],
					'condition' 	=> [
						'tp_rule_key' => 'acf_taxonomy',
					],
				]
			);

			if( class_exists('ACFE') ) {
				$repeater->add_control(
					'tp_rule_acfe_image_selector_name',
					[
						'type' 			=> 'plus-query',
						'post_type' 	=> '',
						'options' 		=> [],
						'placeholder'	=> esc_html__( 'Search Fields', 'theplus' ),
						'description'	=> esc_html__( 'Search ACF "Taxonomy" fields by It\'s label.', 'theplus' ),
						'label_block' 	=> true,
						'multiple'		=> false,
						'query_type'	=> 'acf',
						'query_options'	=> [
							'field_type'	=> [
								'select',
							],
							'show_field_type' => false,
							'show_type' => false,
							'show_group' => true,
						],
						'condition' 	=> [
							'tp_rule_key' => 'acfe_image_selector',
						],
					]
				);
			}	
			
		}
		
		//Jet_Engine
		if(class_exists('Jet_Engine')) {
			$repeater->add_control(
				'tp_rule_jetengine_text_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Name', 'theplus' ),
					'description'	=> esc_html__( 'You need to enter Jet Engine Text fields by it\'s name/key/ID.', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'jetengine_text',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_jetengine_textarea_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Name', 'theplus' ),
					'description'	=> esc_html__( 'You need to enter Jet Engine Textarea fields by it\'s name/key/ID.', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'jetengine_textarea',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_jetengine_switcher_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Name', 'theplus' ),
					'description'	=> esc_html__( 'You need to enter Jet Engine Switcher fields by it\'s name/key/ID.', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'jetengine_switcher',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_jetengine_checkbox_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Name', 'theplus' ),
					'description'	=> esc_html__( 'You need to enter Jet Engine Checkbox fields by it\'s name/key/ID.', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'jetengine_checkbox',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_jetengine_radio_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Name', 'theplus' ),
					'description'	=> esc_html__( 'You need to enter Jet Engine Radio fields by it\'s name/key/ID.', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'jetengine_radio',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_jetengine_select_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Name', 'theplus' ),
					'description'	=> esc_html__( 'You need to enter Jet Engine Select fields by it\'s name/key/ID.', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'jetengine_select',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_jetengine_number_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Name', 'theplus' ),
					'description'	=> esc_html__( 'You need to enter Jet Engine Number fields by it\'s name/key/ID.', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'jetengine_number',
					],
				]
			);
		}
		
		if( defined('PODS_VERSION') ) {
			$repeater->add_control(
				'tp_rule_pods_text_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Name', 'theplus' ),
					'description'	=> esc_html__( 'You need to enter Pods Text fields by it\'s name.', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'pods_text',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_pods_date_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Name', 'theplus' ),
					'description'	=> esc_html__( 'You need to enter Pods Date fields by it\'s name.', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'pods_date',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_pods_number_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Name', 'theplus' ),
					'description'	=> esc_html__( 'You need to enter Pods Number fields by it\'s name.', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'pods_number',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_pods_boolean_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Name', 'theplus' ),
					'description'	=> esc_html__( 'You need to enter Pods Boolean fields by it\'s name.', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'pods_boolean',
					],
				]
			);
		}

		if( defined('WPCF_VERSION') ) {
			$repeater->add_control(
				'tp_rule_toolset_text_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Fields', 'theplus' ),
					'description'	=> esc_html__( 'You need to enter Toolset Text fields ( text, number, range, email, url, password and text area)  by it\'s slug.', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'toolset_text',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_toolset_number_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Fields', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'toolset_number',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_toolset_radio_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Display text', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'toolset_radio',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_toolset_checkbox_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Value to store', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'toolset_checkbox',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_toolset_select_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Fields', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'toolset_select',
					],
				]
			);			
			$repeater->add_control(
				'tp_rule_toolset_checkboxes_name',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Fields', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'toolset_checkboxes',
					],
				]
			);
		}
		
		$repeater->add_control(
			'tp_rule_operator',
			[
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'is',
				'label_block' 	=> true,
				'options' 		=> [
					'is' 		=> esc_html__( 'Is', 'theplus' ),
					'not' 		=> esc_html__( 'Not', 'theplus' ),
				],
			]
		);

		$repeater->add_control(
			'tp_rule_authentication_value',
			[
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> 'authenticated',
				'label_block' => true,
				'options' 	=> [
					'authenticated' => esc_html__( 'Logged in', 'theplus' ),
				],
				'condition' => [
					'tp_rule_key' => 'authentication',
				],
			]
		);;

		$repeater->add_control(
			'tp_rule_role_value',
			[
				'type' 			=> Controls_Manager::SELECT,
				'description' 	=> esc_html__( 'Warning: This rule applies only to logged in visitors.', 'theplus' ),
				'default' 		=> 'subscriber',
				'label_block' 	=> true,
				'options' 		=> $wp_roles->get_names(),
				'condition' 	=> [
					'tp_rule_key' => 'role',
				],
			]
		);

		/*Ref
		https://www.geoplugin.com/webservices/php
		https://www.geoplugin.com/iso3166 */

		$list = [];
		$location = [
						"Andorra"
						,"United Arab Emirates"
						,"Afghanistan"
						,"Antigua and Barbuda"
						,"Anguilla"
						,"Albania"
						,"Armenia"
						,"Netherlands Antilles"
						,"Angola"
						,"Asia/Pacific Region"
						,"Antarctica"
						,"Argentina"
						,"American Samoa"
						,"Austria"
						,"Australia"
						,"Aruba"
						,"Aland Islands"
						,"Azerbaijan"
						,"Bosnia and Herzegovina"
						,"Barbados"
						,"Bangladesh"
						,"Belgium"
						,"Burkina Faso"
						,"Bulgaria"
						,"Bahrain"
						,"Burundi"
						,"Benin"
						,"Bermuda"
						,"Brunei Darussalam"
						,"Bolivia"
						,"Brazil"
						,"Bahamas"
						,"Bhutan"
						,"Bouvet Island"
						,"Botswana"
						,"Belarus"
						,"Belize"
						,"Canada"
						,"Cocos (Keeling) Islands"
						,"Congo, The Democratic Republic of the"
						,"Central African Republic"
						,"Congo"
						,"Switzerland"
						,"Cote d'Ivoire"
						,"Cook Islands"
						,"Chile"
						,"Cameroon"
						,"China"
						,"Colombia"
						,"Costa Rica"
						,"Cuba"
						,"Cape Verde"
						,"Christmas Island"
						,"Cyprus"
						,"Czech Republic"
						,"Germany"
						,"Djibouti"
						,"Denmark"
						,"Dominica"
						,"Dominican Republic"
						,"Algeria"
						,"Ecuador"
						,"Estonia"
						,"Egypt"
						,"Western Sahara"
						,"Eritrea"
						,"Spain"
						,"Ethiopia"
						,"Europe"
						,"Finland"
						,"Fiji"
						,"Falkland Islands (Malvinas)"
						,"Micronesia, Federated States of"
						,"Faroe Islands"
						,"France"
						,"Gabon"
						,"United Kingdom"
						,"Grenada"
						,"Georgia"
						,"French Guiana"
						,"Guernsey"
						,"Ghana"
						,"Gibraltar"
						,"Greenland"
						,"Gambia"
						,"Guinea"
						,"Guadeloupe"
						,"Equatorial Guinea"
						,"Greece"
						,"South Georgia and the South Sandwich Islands"
						,"Guatemala"
						,"Guam"
						,"Guinea-Bissau"
						,"Guyana"
						,"Hong Kong"
						,"Heard Island and McDonald Islands"
						,"Honduras"
						,"Croatia"
						,"Haiti"
						,"Hungary"
						,"Indonesia"
						,"Ireland"
						,"Israel"
						,"Isle of Man"
						,"India"
						,"British Indian Ocean Territory"
						,"Iraq"
						,"Iran, Islamic Republic of"
						,"Iceland"
						,"Italy"
						,"Jersey"
						,"Jamaica"
						,"Jordan"
						,"Japan"
						,"Kenya"
						,"Kyrgyzstan"
						,"Cambodia"
						,"Kiribati"
						,"Comoros"
						,"Saint Kitts and Nevis"
						,"Korea, Democratic People's Republic of"
						,"Korea, Republic of"
						,"Kuwait"
						,"Cayman Islands"
						,"Kazakhstan"
						,"Lao People's Democratic Republic"
						,"Lebanon"
						,"Saint Lucia"
						,"Liechtenstein"
						,"Sri Lanka"
						,"Liberia"
						,"Lesotho"
						,"Lithuania"
						,"Luxembourg"
						,"Latvia"
						,"Libyan Arab Jamahiriya"
						,"Morocco"
						,"Monaco"
						,"Moldova, Republic of"
						,"Montenegro"
						,"Madagascar"
						,"Marshall Islands"
						,"Macedonia"
						,"Mali"
						,"Myanmar"
						,"Mongolia"
						,"Macao"
						,"Northern Mariana Islands"
						,"Martinique"
						,"Mauritania"
						,"Montserrat"
						,"Malta"
						,"Mauritius"
						,"Maldives"
						,"Malawi"
						,"Mexico"
						,"Malaysia"
						,"Mozambique"
						,"Namibia"
						,"New Caledonia"
						,"Niger"
						,"Norfolk Island"
						,"Nigeria"
						,"Nicaragua"
						,"Netherlands"
						,"Norway"
						,"Nepal"
						,"Nauru"
						,"Niue"
						,"New Zealand"
						,"Oman"
						,"Panama"
						,"Peru"
						,"French Polynesia"
						,"Papua New Guinea"
						,"Philippines"
						,"Pakistan"
						,"Poland"
						,"Saint Pierre and Miquelon"
						,"Pitcairn"
						,"Puerto Rico"
						,"Palestinian Territory"
						,"Portugal"
						,"Palau"
						,"Paraguay"
						,"Qatar"
						,"Reunion"
						,"Romania"
						,"Serbia"
						,"Russian Federation"
						,"Rwanda"
						,"Saudi Arabia"
						,"Solomon Islands"
						,"Seychelles"
						,"Sudan"
						,"Sweden"
						,"Singapore"
						,"Saint Helena"
						,"Slovenia"
						,"Svalbard and Jan Mayen"
						,"Slovakia"
						,"Sierra Leone"
						,"San Marino"
						,"Senegal"
						,"Somalia"
						,"Suriname"
						,"Sao Tome and Principe"
						,"El Salvador"
						,"Syrian Arab Republic"
						,"Swaziland"
						,"Turks and Caicos Islands"
						,"Chad"
						,"French Southern Territories"
						,"Togo"
						,"Thailand"
						,"Tajikistan"
						,"Tokelau"
						,"Timor-Leste"
						,"Turkmenistan"
						,"Tunisia"
						,"Tonga"
						,"Turkey"
						,"Trinidad and Tobago"
						,"Tuvalu"
						,"Taiwan"
						,"Tanzania, United Republic of"
						,"Ukraine"
						,"Uganda"
						,"United States Minor Outlying Islands"
						,"United States"
						,"Uruguay"
						,"Uzbekistan"
						,"Holy See (Vatican City State)"
						,"Saint Vincent and the Grenadines"
						,"Venezuela"
						,"Virgin Islands, British"
						,"Virgin Islands, U.S."
						,"Vietnam"
						,"Vanuatu"
						,"Wallis and Futuna"
						,"Samoa"
						,"Yemen"
						,"Mayotte"
						,"South Africa"
						,"Zambia"
						,"Zimbabwe" 
						,"Anonymous Proxy"
						,"Satellite Provider"
				];

			foreach ( $location as $country ) {
				$lower = strtolower( $country );				
				$val = $country;
				$list[ $lower ] = $val;
			}
		$repeater->add_control(
			'tp_rule_location_value',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label_block' 	=> true,
				'multiple'		=> false,
				'options' 		=> $list,
				'condition' 	=> [
					'tp_rule_key' => 'location',
				],
			]
		);
		
		$repeater->add_control(
			'tp_rule_date_value',
			[
				'label'		=> esc_html__( 'In interval', 'theplus' ),
				'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
				'picker_options' => [
					'enableTime'	=> false,
					'mode' 			=> 'range',
				],
				'label_block'	=> true,
				'default' 		=> $default_interval,
				'condition' 	=> [
					'tp_rule_key' => 'date',
				],
			]
		);
		$repeater->add_control(
			'tp_rule_timerange_value',
			[
				'label'		=> esc_html__( 'Start Time', 'theplus' ),
				'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
				'picker_options' => [
					'dateFormat' 	=> "H:i",
					'enableTime' 	=> true,
					'noCalendar' 	=> true,
				],
				'label_block'	=> true,
				'default' 		=> '',
				'condition' 	=> [
					'tp_rule_key' => 'timerange',
				],
			]
		);
		$repeater->add_control(
			'tp_rule_timerange_output',
			[
				'label'		=> esc_html__( 'End Time', 'theplus' ),
				'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
				'picker_options' => [
					'dateFormat' 	=> "H:i",
					'enableTime' 	=> true,
					'noCalendar' 	=> true,
				],
				'label_block'	=> true,
				'default' 		=> '',
				'condition' 	=> [
					'tp_rule_key' => 'timerange',
				],
			]
		);
		$repeater->add_control(
			'tp_rule_time_value',
			[
				'label'		=> esc_html__( 'Before', 'theplus' ),
				'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
				'picker_options' => [
					'dateFormat' 	=> "H:i",
					'enableTime' 	=> true,
					'noCalendar' 	=> true,
				],
				'label_block'	=> true,
				'default' 		=> '',
				'condition' 	=> [
					'tp_rule_key' => 'time',
				],
			]
		);

		$repeater->add_control(
			'tp_rule_day_value',
			[
				'label'			=> esc_html__( 'Before', 'theplus' ),
				'type' 			=> Controls_Manager::SELECT2,
				'placeholder'	=> esc_html__( 'Any', 'theplus' ),
				'multiple'		=> true,
				'options' => [
					'1' => esc_html__( 'Monday', 'theplus' ),
					'2' => esc_html__( 'Tuesday', 'theplus' ),
					'3' => esc_html__( 'Wednesday', 'theplus' ),
					'4' => esc_html__( 'Thursday', 'theplus' ),
					'5' => esc_html__( 'Friday', 'theplus' ),
					'6' => esc_html__( 'Saturday', 'theplus' ),
					'7' => esc_html__( 'Sunday', 'theplus' ),
				],
				'label_block'	=> true,
				'default' 		=> 'Monday',
				'condition' 	=> [
					'tp_rule_key' => 'day',
				],
			]
		);

		$os_options = $this->get_os_opt();

		$repeater->add_control(
			'tp_rule_os_value',
			[
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> array_keys( $os_options )[0],
				'label_block' 	=> true,
				'options' 		=> $os_options,
				'condition' 	=> [
					'tp_rule_key' => 'os',
				],
			]
		);

		$browser_options = $this->get_browser_opt();

		$repeater->add_control(
			'tp_rule_browser_value',
			[
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> array_keys( $browser_options )[0],
				'label_block' 	=> true,
				'options' 		=> $browser_options,
				'condition' 	=> [
					'tp_rule_key' => 'browser',
				],
			]
		);

		$repeater->add_control(
			'tp_rule_page_value',
			[
				'type' 			=> 'plus-query',
				'default' 		=> '',
				'placeholder'	=> esc_html__( 'Any', 'theplus' ),
				'description'	=> esc_html__( 'Leave blank for any page.', 'theplus' ),
				'label_block' 	=> true,
				'multiple'		=> true,
				'query_type'	=> 'posts',
				'object_type'	=> 'page',
				'condition' 	=> [
					'tp_rule_key' => 'page',
				],
			]
		);

		$repeater->add_control(
			'tp_rule_post_value',
			[
				'type' 			=> 'plus-query',
				'default' 		=> '',
				'placeholder'	=> esc_html__( 'Any', 'theplus' ),
				'description'	=> esc_html__( 'Leave blank for any post.', 'theplus' ),
				'label_block' 	=> true,
				'multiple'		=> true,
				'query_type'	=> 'posts',
				'object_type'	=> '',
				'condition' 	=> [
					'tp_rule_key' => 'post',
				],
			]
		);

		$repeater->add_control(
			'tp_rule_static_page_value',
			[
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'home',
				'label_block' 	=> true,
				'options' 		=> [
					'home'		=> esc_html__( 'Default Homepage', 'theplus' ),
					'static'	=> esc_html__( 'Static Homepage', 'theplus' ),
					'blog'		=> esc_html__( 'Blog Page', 'theplus' ),
					'404'		=> esc_html__( '404 Page', 'theplus' ),
				],
				'condition' 	=> [
					'tp_rule_key' => 'static_page',
				],
			]
		);

		$repeater->add_control(
			'tp_rule_post_type_value',
			[
				'type' 			=> Controls_Manager::SELECT2,
				'default' 		=> '',
				'placeholder'	=> esc_html__( 'Any', 'theplus' ),
				'description'	=> esc_html__( 'Leave blank or select all for any post type.', 'theplus' ),
				'label_block' 	=> true,
				'multiple'		=> true,
				'options' 		=> $this->get_post_types_opt( true ),
				'condition' 	=> [
					'tp_rule_key' => 'post_type',
				],
			]
		);

		$repeater->add_control(
			'tp_rule_taxonomy_archive_value',
			[
				'type' 			=> Controls_Manager::SELECT2,
				'default' 		=> '',
				'placeholder'	=> esc_html__( 'Any', 'theplus' ),
				'description'	=> esc_html__( 'Leave blank or select all for any taxonomy archive.', 'theplus' ),
				'multiple'		=> true,
				'label_block' 	=> true,
				'options' 		=> $this->get_taxonomies_opt(),
				'condition' 	=> [
					'tp_rule_key' => 'taxonomy_archive',
				],
			]
		);

		$repeater->add_control(
			'tp_rule_term_archive_value',
			[
				'label' 		=> esc_html__( 'Term', 'theplus' ),
				'description'	=> esc_html__( 'Leave blank or select all for any term archive.', 'theplus' ),
				'type' 			=> 'plus-query',
				'post_type' 	=> '',
				'options' 		=> [],
				'label_block' 	=> true,
				'multiple' 		=> true,
				'query_type' 	=> 'terms',
				'include_type' 	=> true,
				'condition' 	=> [
					'tp_rule_key' => 'term_archive',
				],
			]
		);
		
		$repeater->add_control(
			'tp_rule_term_single_value',
			[
				'label' 		=> esc_html__( 'Term', 'theplus' ),
				'description'	=> esc_html__( 'Leave blank or select all for any term single.', 'theplus' ),
				'type' 			=> 'plus-query',
				'post_type' 	=> '',
				'options' 		=> [],
				'label_block' 	=> true,
				'multiple' 		=> true,
				'query_type' 	=> 'terms',
				'include_type' 	=> true,
				'condition' 	=> [
					'tp_rule_key' => 'term_single',
				],
			]
		);
		
		$repeater->add_control(
			'tp_rule_post_type_archive_value',
			[
				'type' 			=> Controls_Manager::SELECT2,
				'default' 		=> '',
				'placeholder'	=> esc_html__( 'Any', 'theplus' ),
				'description'	=> esc_html__( 'Leave blank or select all for any post type.', 'theplus' ),
				'multiple'		=> true,
				'label_block' 	=> true,
				'options' 		=> $this->get_post_types_opt(),
				'condition' 	=> [
					'tp_rule_key' => 'post_type_archive',
				],
			]
		);

		$repeater->add_control(
			'tp_rule_date_archive_value',
			[
				'type' 			=> Controls_Manager::SELECT2,
				'default' 		=> '',
				'placeholder'	=> esc_html__( 'Any', 'theplus' ),
				'description'	=> esc_html__( 'Leave blank or select all for any date based archive.', 'theplus' ),
				'multiple'		=> true,
				'label_block' 	=> true,
				'options' 		=> [
					'day'		=> esc_html__( 'Day', 'theplus' ),
					'month'		=> esc_html__( 'Month', 'theplus' ),
					'year'		=> esc_html__( 'Year', 'theplus' ),
				],
				'condition' 	=> [
					'tp_rule_key' => 'date_archive',
				],
			]
		);

		$repeater->add_control(
			'tp_rule_author_archive_value',
			[
				'type' 			=> 'plus-query',
				'default' 		=> '',
				'placeholder'	=> esc_html__( 'Any', 'theplus' ),
				'description'	=> esc_html__( 'Leave blank for all authors.', 'theplus' ),
				'multiple'		=> true,
				'label_block' 	=> true,
				'query_type'	=> 'authors',
				'condition' 	=> [
					'tp_rule_key' => 'author_archive',
				],
			]
		);

		$repeater->add_control(
			'tp_rule_search_results_value',
			[
				'type' 			=> Controls_Manager::TEXT,
				'default' 		=> '',
				'placeholder'	=> esc_html__( 'Keywords', 'theplus' ),
				'description'	=> esc_html__( 'Enter keywords, separated by commas, to condition the display on specific keywords and leave blank for any.', 'theplus' ),
				'label_block' 	=> true,
				'condition' 	=> [
					'tp_rule_key' => 'search_results',
				],
			]
		);
		
		$repeater->add_control(
			'tp_rule_site_language_value',
			[
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> '',
				'placeholder'	=> esc_html__( 'Language', 'theplus' ),
				'description'	=> esc_html__( 'Leave blank or select language.', 'theplus' ),
				'multiple'		=> false,
				'label_block' 	=> true,
				'options' 		=> $this->get_lang_opt(),
				'condition' 	=> [
					'tp_rule_key' => 'site_language',
				],
			]
		);
		$repeater->add_control(
			'tp_rule_browser_language_value',
			[
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> '',
				'placeholder'	=> esc_html__( 'Language', 'theplus' ),
				'description'	=> esc_html__( 'Leave blank or select language.', 'theplus' ),
				'multiple'		=> false,
				'label_block' 	=> true,
				'options' 		=> $this->get_bro_lang_opt(),
				'condition' 	=> [
					'tp_rule_key' => 'browser_language',
				],
			]
		);
		
		$repeater->add_control(
			'tp_rule_url_string_value',
			[
				'type' 			=> Controls_Manager::TEXT,
				'default' 		=> '',
				'placeholder'	=> __( 'Value', 'theplus' ),
				'label_block' 	=> true,
				'condition' 	=> [
					'tp_rule_key' => 'url_string',
				],
			]
		);
		
		$repeater->add_control(
			'tp_rule_url_parameter_value',
			[
				'type' 			=> Controls_Manager::TEXTAREA,
				'default' 		=> '',
				'placeholder'	=> __( 'parameter1=value', 'theplus' ),
				'label_block' 	=> true,
				'condition' 	=> [
					'tp_rule_key' => 'url_parameter',
				],
			]
		);
		
		$repeater->add_control(
			'tp_rule_tp_shortcode_value',
			[
				'type' 			=> Controls_Manager::TEXT,
				'default' 		=> '',
				'placeholder'	=> esc_html__( '[shortcode]', 'theplus' ),
				'description'	=> esc_html__( 'Enter Shortcode', 'theplus' ),
				'label_block' 	=> true,
				'condition' 	=> [
					'tp_rule_key' => 'tp_shortcode',
				],
			]
		);

		$repeater->add_control(
			'tp_rule_tp_shortcode_output',
			[
				'type' 			=> Controls_Manager::TEXT,
				'default' 		=> '',
				'description'	=> esc_html__( 'Enter Value', 'theplus' ),
				'label_block' 	=> true,
				'condition' 	=> [
					'tp_rule_key' => 'tp_shortcode',
				],
			]
		);
		if(class_exists('woocommerce')) {
			$repeater->add_control(
				'tp_rule_woo_first_purchase_value',
				[
					'label'		=> esc_html__( 'Date', 'theplus' ),
					'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
					'picker_options' => [
						'enableTime'	=> false,
						'mode' 			=> 'range',
					],
					'label_block'	=> true,
					'default'        => gmdate( 'Y/m/d' ),
					'picker_options' => array(
						'format'     => 'Y-m-d',
						'enableTime' => false,
					),
					'condition' 	=> [
						'tp_rule_key' => 'woo_first_purchase',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_woo_last_purchase_date_value',
				[
					'label'		=> esc_html__( 'Date', 'theplus' ),
					'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
					'picker_options' => [
						'enableTime'	=> false,
						'mode' 			=> 'range',
					],
					'label_block'	=> true,
					'default'        => gmdate( 'Y/m/d' ),
					'picker_options' => array(
						'format'     => 'Y-m-d',
						'enableTime' => false,
					),
					'condition' 	=> [
						'tp_rule_key' => 'woo_last_purchase_date',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_woo_last_purchase_value',
				[
					'label'		=> esc_html__( 'Current or Before', 'theplus' ),
					'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
					'picker_options' => [
						'enableTime'	=> false,
						'mode' 			=> 'range',
					],
					'label_block'	=> true,
					'default'        => gmdate( 'Y/m/d' ),
					'picker_options' => array(
						'format'     => 'Y-m-d',
						'enableTime' => false,
					),
					'condition' 	=> [
						'tp_rule_key' => 'woo_last_purchase',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_woo_cur_pro_cat_value',
				[
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> array(),
					'placeholder'	=> esc_html__( 'Any', 'theplus' ),
					'description'	=> esc_html__( 'Leave blank or select category.', 'theplus' ),
					'label_block' 	=> true,
					'multiple'		=> true,
					'options' 		=> $this->get_woo_cat(),
					'condition' 	=> [
						'tp_rule_key' => 'woo_cur_pro_cat',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_woo_cur_pro_price_value',
				[
					'type' 			=> Controls_Manager::NUMBER,
					'min'           => 0,
					'placeholder'	=> esc_html__( 'Number', 'theplus' ),
					'description'	=> esc_html__( 'Equal or Higher Price', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'woo_cur_pro_price',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_woo_cur_pro_stock_value',
				[
					'type' 			=> Controls_Manager::NUMBER,
					'min'           => 0,
					'placeholder'	=> esc_html__( 'Stock', 'theplus' ),
					'description'	=> esc_html__( 'Equal or Higher Stock', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'woo_cur_pro_stock',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_woo_cart_subtotal_value',
				[
					'type' 			=> Controls_Manager::NUMBER,
					'min'           => 0,
					'placeholder'	=> esc_html__( 'Cart Sub Total', 'theplus' ),
					'description'	=> esc_html__( 'Equal or Higher Cart Sub Total', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'woo_cart_subtotal',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_woo_cart_total_value',
				[
					'type' 			=> Controls_Manager::NUMBER,
					'min'           => 0,
					'placeholder'	=> esc_html__( 'Cart Total', 'theplus' ),
					'description'	=> esc_html__( 'Equal or Higher Cart Total', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'woo_cart_total',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_woo_purchase_total_value',
				[
					'type' 			=> Controls_Manager::NUMBER,
					'min'           => 0,
					'placeholder'	=> esc_html__( 'Purchase Total', 'theplus' ),
					'description'	=> esc_html__( 'Equal or Higher Purchase Total', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'woo_purchase_total',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_woo_items_mode',
				[
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'equal',
					'label_block' 	=> true,
					'options'		=> [
						'equal' 		=> __( 'Equal', 'theplus' ),
						'equal_or_higher' 	=> __( 'Equal or Higher', 'theplus' ),
					],
					'condition' 	=> [
						'tp_rule_key' => ['woo_cart_item','woo_purchase_item'],
					],
				]
			);

			$repeater->add_control(
				'tp_rule_woo_cart_item_value',
				[
					'type' 			=> Controls_Manager::NUMBER,
					'min'           => 0,
					'description'	=> esc_html__( 'No. of Items', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'woo_cart_item',
					],
				]
			);
			
			$repeater->add_control(
				'tp_rule_woo_purchase_item_value',
				[
					'type' 			=> Controls_Manager::NUMBER,
					'min'           => 0,					
					'description'	=> esc_html__( 'No. of Order', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'woo_purchase_item',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_woo_cart_pro_cat_value',
				[
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> array(),
					'placeholder'	=> esc_html__( 'Cart Category', 'theplus' ),
					'description'	=> esc_html__( 'Leave blank or select category.', 'theplus' ),
					'label_block' 	=> true,
					'multiple'		=> true,
					'options' 		=> $this->get_woo_cat(),
					'condition' 	=> [
						'tp_rule_key' => 'woo_cart_pro_cat',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_woo_cart_pro_tag_value',
				[
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> array(),
					'placeholder'	=> esc_html__( 'Cart Product Tag', 'theplus' ),
					'description'	=> esc_html__( 'Leave blank or select category.', 'theplus' ),
					'label_block' 	=> true,
					'multiple'		=> true,
					'options' 		=> $this->get_woo_tag(),
					'condition' 	=> [
						'tp_rule_key' => 'woo_cart_pro_tag',
					],
				]
			);
			
			$repeater->add_control(
				'tp_rule_woo_purchase_pro_cat_value',
				[
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> array(),
					'placeholder'	=> esc_html__( 'Purchased Category', 'theplus' ),
					'description'	=> esc_html__( 'Leave blank or select category.', 'theplus' ),
					'label_block' 	=> true,
					'multiple'		=> true,
					'options' 		=> $this->get_woo_cat(),
					'condition' 	=> [
						'tp_rule_key' => 'woo_purchase_pro_cat',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_woo_purchase_pro_name_value',
				[
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Purchase Product', 'theplus' ),
					'description'	=> esc_html__( 'Leave blank or select product.', 'theplus' ),
					'label_block' 	=> true,
					'multiple'		=> true,
					'options' 		=> $this->get_woo_product(),
					'condition' 	=> [
						'tp_rule_key' => 'woo_purchase_pro_name',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_woo_cart_product_value',
				[
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Cart Product', 'theplus' ),
					'description'	=> esc_html__( 'Leave blank or select product.', 'theplus' ),
					'label_block' 	=> true,
					'multiple'		=> true,
					'options' 		=> $this->get_woo_product(),
					'condition' 	=> [
						'tp_rule_key' => 'woo_cart_product',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_woo_po_bill_city_value',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> '',
					'description'	=> esc_html__( 'Enter City Name', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'woo_po_bill_city',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_woo_po_bill_state_value',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> '',
					'description'	=> esc_html__( 'e.g. CA, NY, etc. Only add single value. Full List.', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'woo_po_bill_state',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_woo_po_bill_country_value',
				[
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Country', 'theplus' ),					
					'label_block' 	=> true,
					'multiple'		=> true,
					'options' 		=> $this->tp_get_woo_country(),
					'condition' 	=> [
						'tp_rule_key' => 'woo_po_bill_country',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_woo_po_bill_postcode_value',
				[
					'type' 			=> Controls_Manager::NUMBER,
					'min'           => 0,
					'placeholder'	=> esc_html__( 'Postcode', 'theplus' ),					
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'woo_po_bill_postcode',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_woo_po_ship_city_value',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> '',
					'description'	=> esc_html__( 'Enter City Name', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'woo_po_ship_city',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_woo_po_ship_state_value',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> '',
					'description'	=> esc_html__( 'e.g. CA, NY, etc. Only add single value. Full List.', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'woo_po_ship_state',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_woo_po_ship_country_value',
				[
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Country', 'theplus' ),					
					'label_block' 	=> true,
					'multiple'		=> true,
					'options' 		=> $this->tp_get_woo_country(),
					'condition' 	=> [
						'tp_rule_key' => 'woo_po_ship_country',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_woo_po_ship_postcode_value',
				[
					'type' 			=> Controls_Manager::NUMBER,
					'min'           => 0,
					'placeholder'	=> esc_html__( 'Postcode', 'theplus' ),					
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'woo_po_ship_postcode',
					],
				]
			);
		}
		
		if( class_exists( 'ACF', false ) ){
			$repeater->add_control(
				'tp_rule_acf_text_value',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> __( 'Value', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'acf_text',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_acf_select_value',
				[
					'type' 			=> Controls_Manager::TEXTAREA,
					'default' 		=> '',
					'placeholder'	=> __( 'Choices', 'theplus' ),
					'description'	=> __( 'Enter each selected values on a separate line by enter. You may use value (e.g. zombie) or Value & Label both (e.g. zombie : Zombie). Keep it blank to check if the field is set or not.', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'acf_select',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_acf_button_group_value',
				[
					'type' 			=> Controls_Manager::TEXTAREA,
					'default' 		=> '',
					'placeholder'	=> __( 'Choices', 'theplus' ),
					'description'	=> __( 'Enter each selected values on a separate line by enter. You may use value (e.g. zombie) or Value & Label both (e.g. zombie : Zombie). Keep it blank to check if the field is set or not.', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'acf_button_group',
					],
				]
			);
			
			$repeater->add_control(
				'tp_rule_acf_boolean_value',
				[
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'true',
					'label_block' 	=> true,
					'options'		=> [
						'true' 		=> __( 'True', 'theplus' ),
						'false' 	=> __( 'False', 'theplus' ),
					],
					'condition' 	=> [
						'tp_rule_key' => 'acf_boolean',
					],
				]
			);
			
			$default_date = date( 'Y-m-d', strtotime( '-2 day' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
			$repeater->add_control(
				'tp_rule_acf_datetime_value',
				[
					'label'		=> __( 'Before Date', 'theplus' ),
					'type' 		=> Controls_Manager::DATE_TIME,
					'picker_options' => [
						'enableTime' => true,
					],
					'label_block'	=> false,
					'default' 		=> $default_date,
					'condition' 	=> [
						'tp_rule_key' => 'acf_datetime',
					],
				]
			);
			
			$repeater->add_control(
				'tp_rule_acf_post_value',
				[
					'type' 			=> 'plus-query',
					'default' 		=> '',
					'placeholder'	=> esc_html__( 'Search Posts', 'theplus' ),
					'description'	=> esc_html__( 'Leave blank for any post.', 'theplus' ),
					'label_block' 	=> true,
					'multiple'		=> true,
					'query_type'	=> 'posts',
					'object_type'	=> 'any',
					'condition' 	=> [
						'tp_rule_key' => 'acf_post',
					],
				]
			);
			
			$repeater->add_control(
				'tp_rule_acf_taxonomy_value',
				[
					'label' 		=> esc_html__( 'Search Terms', 'theplus' ),
					'description'	=> esc_html__( 'Leave blank or select all for any term.', 'theplus' ),
					'type' 			=> 'plus-query',
					'post_type' 	=> '',
					'options' 		=> [],
					'label_block' 	=> true,
					'multiple' 		=> true,
					'query_type' 	=> 'terms',
					'include_type' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'acf_taxonomy',
					],
				]
			);

			if( class_exists('ACFE') ) {
				$repeater->add_control(
					'tp_rule_acfe_image_selector_value',
					[
						'type' 			=> Controls_Manager::TEXT,
						'default' 		=> '',
						'placeholder'	=> __( 'Value', 'theplus' ),
						'label_block' 	=> true,
						'condition' 	=> [
							'tp_rule_key' => 'acfe_image_selector',
						],
					]
				);
			}
		}
		
		if(class_exists('Jet_Engine')) {
			$repeater->add_control(
				'tp_rule_jetengine_text_value',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> __( 'Value', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'jetengine_text',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_jetengine_textarea_value',
				[
					'type' 			=> Controls_Manager::TEXTAREA,
					'default' 		=> '',
					'placeholder'	=> __( 'Value', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'jetengine_textarea',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_jetengine_switcher_value',
				[
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'false',
					'label_block' 	=> true,
					'options'		=> [
						'true' 		=> __( 'Enable', 'theplus' ),
						'false' 	=> __( 'Disable', 'theplus' ),
					],
					'condition' 	=> [
						'tp_rule_key' => 'jetengine_switcher',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_jetengine_checkbox_value',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> __( 'Value', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'jetengine_checkbox',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_jetengine_radio_value',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> __( 'Value', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'jetengine_radio',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_jetengine_select_value',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> __( 'Value', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'jetengine_select',
					],
				]
			);
			$repeater->add_control(
				'tp_rule_jetengine_number_value',
				[
					'type' 			=> Controls_Manager::NUMBER,
					'min'           => 0,
					'placeholder'	=> esc_html__( 'Number', 'theplus' ),					
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'jetengine_number',
					],
				]
			);
			
		}
		
		if( defined('PODS_VERSION') ) {
			$repeater->add_control(
				'tp_rule_pods_text_value',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> __( 'Value', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'pods_text',
					],
				]
			);

			$default_date_pods = date( 'Y-m-d', strtotime( '0 day' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
			$repeater->add_control(
				'tp_rule_pods_date_value',
				[
					'label'		=> __( 'Date', 'theplus' ),
					'type' 		=> Controls_Manager::DATE_TIME,
					'picker_options' => [
						'enableTime' => false,
					],
					'label_block'	=> true,
					'default' 		=> $default_date_pods,
					'condition' 	=> [
						'tp_rule_key' => 'pods_date',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_pods_number_value',
				[
					'type' 			=> Controls_Manager::NUMBER,
					'min'           => 0,
					'placeholder'	=> esc_html__( 'Number', 'theplus' ),					
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'pods_number',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_pods_boolean_value',
				[
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'true',
					'label_block' 	=> true,
					'options'		=> [
						'true' 		=> __( 'Yes', 'theplus' ),
						'false' 	=> __( 'No', 'theplus' ),
					],
					'condition' 	=> [
						'tp_rule_key' => 'pods_boolean',
					],
				]
			);
		}

		if( defined('WPCF_VERSION') ) {
			$repeater->add_control(
				'tp_rule_toolset_text_value',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> __( 'Value', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'toolset_text',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_toolset_number_value',
				[
					'type' 			=> Controls_Manager::NUMBER,
					'min'           => 0,
					'placeholder'	=> esc_html__( 'Number', 'theplus' ),					
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'toolset_number',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_toolset_radio_value',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> __( 'Value', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'toolset_radio',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_toolset_checkbox_value',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> __( 'Value', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'toolset_checkbox',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_toolset_select_value',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> __( 'Value', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'toolset_select',
					],
				]
			);

			$repeater->add_control(
				'tp_rule_toolset_checkboxes_value',
				[
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'placeholder'	=> __( 'Value to store', 'theplus' ),
					'description'	=> __( 'Enter each selected values by comma(,). Like value1, value2', 'theplus' ),
					'label_block' 	=> true,
					'condition' 	=> [
						'tp_rule_key' => 'toolset_checkboxes',
					],
				]
			);
		}
		if ( class_exists( 'Easy_Digital_Downloads', false ) ) {
			$repeater->add_control(
				'tp_rule_edd_cart_value',
				[
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'empty',
					'label_block' 	=> true,
					'options' 		=> [
						'empty'		=> esc_html__( 'Empty', 'theplus' ),
					],
					'condition' 	=> [
						'tp_rule_key' => 'edd_cart',
					],
				]
			);
		}

		$element->add_control(
			'tp_display_rules',
			[
				'label' 	=> esc_html__( 'Rules', 'theplus' ),
				'type' 		=> Controls_Manager::REPEATER,
				'default' 	=> [
					[
						'tp_rule_key'	=> 'authentication',
						'tp_rule_operator'	=> 'is',
						'tp_rule_authentication_value' => 'authenticated',
					],
				],
				'condition'		=> [
					'tp_display_rules_enable' => 'yes',
				],
				'fields' 		=> $repeater->get_controls(),
				'title_field' 	=> 'Rule',
			]
		);
		$element->end_controls_section();
	}

	/**
	 * Get browser options for control
	 *
	 * @access protected
	 */
	protected function get_browser_opt() {
		return [
			'ie'			=> 'Internet Explorer',
			'chrome'		=> 'Google Chrome',
			'firefox'		=> 'Mozilla Firefox',
			'opera'			=> 'Opera',
			'opera_mini'	=> 'Opera Mini',
			'safari'		=> 'Safari',
		];
	}
	
	/**
	 * Get OS options for control
	 *
	 * @access protected
	 */
	protected function get_os_opt() {
		return [
			'iphone' 		=> 'iPhone',
			'android' 		=> 'Android',
			'safari'    	=> 'Safari',
			'mac_os'    	=> 'Mac OS',
			'windows' 		=> 'Windows',
			'linux'     	=> 'Linux',
			'open_bsd'		=> 'OpenBSD',
			'sun_os'    	=> 'SunOS',
			'qnx'       	=> 'QNX',
			'search_bot'	=> 'Search Bot',
			'beos'      	=> 'BeOS',
			'os2'       	=> 'OS/2',			
		];
	}

	public function get_post_types_opt( $singular = false, $any = false, $args = [] ) {
		$post_type_args = [
			'show_in_nav_menus' => true,
		];

		if ( $any ) $post_types['any'] = esc_html__( 'Any', 'theplus' );

		if ( ! function_exists( 'get_post_types' ) )
			return $post_types;

		$post_types_obj = get_post_types( $post_type_args, 'objects' );

		foreach ( $post_types_obj as $post_type => $object ) {
			$post_types[ $post_type ] = $singular ? $object->labels->singular_name : $object->label;
		}

		return $post_types;
	}
	
	public function get_taxonomies_opt() {

		$options = [];

		$taxonomies = get_taxonomies( array(
					'show_in_nav_menus' => true
				), 'objects' );

		if ( empty( $taxonomies ) ) {
			$options[ '' ] = esc_html__( 'Not found taxonomies', 'theplus' );
			return $options;
		}

		foreach ( $taxonomies as $taxonomy ) {
			$options[ $taxonomy->name ] = $taxonomy->label;
		}

		return $options;
	}
	
	public function get_bro_lang_opt(){
		return [			
			"ab" => "Abkhazian",
			"aa" => "Afar",
			"af" => "Afrikaans",
			"ak" => "Akan",
			"sq" => "Albanian",
			"am" => "Amharic",
			"ar" => "Arabic",
			"an" => "Aragonese",
			"hy" => "Armenian",
			"as" => "Assamese",
			"av" => "Avaric",
			"ae" => "Avestan",
			"ay" => "Aymara",
			"az" => "Azerbaijani",
			"bm" => "Bambara",
			"ba" => "Bashkir",
			"eu" => "Basque",
			"be" => "Belarusian",
			"bn" => "Bengali (Bangla)",
			"bh" => "Bihari",
			"bi" => "Bislama",
			"bs" => "Bosnian",
			"br" => "Breton",
			"bg" => "Bulgarian",
			"my" => "Burmese",
			"ca" => "Catalan",
			"ch" => "Chamorro",
			"ce" => "Chechen",			
			"ny" => "Chichewa, Chewa, Nyanja",
			"zh" => "Chinese",
			"zh-Hans" => "Chinese (Simplified)",
			"zh-Hant" => "Chinese (Traditional)",
			"cv" => "Chuvash",
			"kw" => "Cornish",
			"co" => "Corsican",
			"cr" => "Cree",
			"hr" => "Croatian",
			"cs" => "Czech",
			"da" => "Danish",
			"dv" => "Divehi, Dhivehi, Maldivian",
			"nl" => "Dutch",
			"dz" => "Dzongkha",
			"en" => "English",
			"eo" => "Esperanto",
			"et" => "Estonian",
			"ee" => "Ewe",
			"fo" => "Faroese",
			"fi" => "Finnish",
			"fr" => "French",
			"ff" => "Fula, Fulah, Pulaar, Pular",
			"gl" => "Galician",
			"gd" => "Gaelic (Scottish)",
			"gv" => "Gaelic (Manx)",
			"ka" => "Georgian",
			"de" => "German",
			"el" => "Greek",
			"kl" => "Greenlandic",
			"gn" => "Guarani",
			"gu" => "Gujarati",
			"ht" => "Haitian Creole",
			"ha" => "Hausa",
			"he" => "Hebrew",
			"hz" => "Herero",
			"hi" => "Hindi",
			"ho" => "Hiri Motu",
			"hu" => "Hungarian",
			"is" => "Icelandic",
			"io" => "Ido",
			"ig" => "Igbo",
			"id" => "Indonesian-1",
			"in" => "Indonesian-2",
			"ia" => "Interlingua",
			"ie" => "Interlingue",
			"iu" => "Inuktitut",
			"ik" => "Inupiak",
			"ga" => "Irish",
			"it" => "Italian",
			"ja" => "Japanese",
			"jv" => "Javanese",
			"kl" => "Kalaallisut, Greenlandic",
			"kn" => "Kannada",
			"kr" => "Kanuri",
			"ks" => "Kashmiri",
			"kk" => "Kazakh",
			"km" => "Khmer",
			"ki" => "Kikuyu",
			"rw" => "Kinyarwanda (Rwanda)",
			"rn" => "Kirundi",
			"ky" => "Kyrgyz",
			"kv" => "Komi",
			"kg" => "Kongo",
			"ko" => "Korean",
			"ku" => "Kurdish",
			"kj" => "Kwanyama",
			"lo" => "Lao",
			"la" => "Latin",
			"lv" => "Latvian (Lettish)",
			"li" => "Limburgish ( Limburger)",
			"ln" => "Lingala",
			"lt" => "Lithuanian",
			"lu" => "Luga-Katanga",
			"lg" => "Luganda, Ganda",
			"lb" => "Luxembourgish",
			"gv" => "Manx",
			"mk" => "Macedonian",
			"mg" => "Malagasy",
			"ms" => "Malay",
			"ml" => "Malayalam",
			"mt" => "Maltese",
			"mi" => "Maori",
			"mr" => "Marathi",
			"mh" => "Marshallese",
			"mo" => "Moldavian",
			"mn" => "Mongolian",
			"na" => "Nauru",
			"nv" => "Navajo",
			"ng" => "Ndonga",
			"nd" => "Northern Ndebele",
			"ne" => "Nepali",
			"no" => "Norwegian",
			"nb" => "Norwegian bokmål",
			"nn" => "Norwegian nynorsk",
			"ii" => "Nuosu",
			"oc" => "Occitan",
			"oj" => "Ojibwe",
			"cu" => "Old Church Slavonic, Old Bulgarian",
			"or" => "Oriya",
			"om" => "Oromo (Afaan Oromo)",
			"os" => "Ossetian",
			"pi" => "Pāli",
			"ps" => "Pashto, Pushto",
			"fa" => "Persian (Farsi)",
			"pl" => "Polish",
			"pt" => "Portuguese",
			"pa" => "Punjabi (Eastern)",
			"qu" => "Quechua",
			"rm" => "Romansh",
			"ro" => "Romanian",
			"ru" => "Russian",
			"se" => "Sami",
			"sm" => "Samoan",
			"sg" => "Sango",
			"sa" => "Sanskrit",
			"sr" => "Serbian",
			"sh" => "Serbo-Croatian",
			"st" => "Sesotho",
			"tn" => "Setswana",
			"sn" => "Shona",
			"ii" => "Sichuan Yi",
			"sd" => "Sindhi",
			"si" => "Sinhalese",
			"ss" => "Siswati",
			"sk" => "Slovak",
			"sl" => "Slovenian",
			"so" => "Somali",
			"nr" => "Southern Ndebele",
			"es" => "Spanish",
			"su" => "Sundanese",
			"sw" => "Swahili (Kiswahili)",
			"ss" => "Swati",
			"sv" => "Swedish",
			"tl" => "Tagalog",
			"ty" => "Tahitian",
			"tg" => "Tajik",
			"ta" => "Tamil",
			"tt" => "Tatar",
			"te" => "Telugu",
			"th" => "Thai",
			"bo" => "Tibetan",
			"ti" => "Tigrinya",
			"to" => "Tonga",
			"ts" => "Tsonga",
			"tr" => "Turkish",
			"tk" => "Turkmen",
			"tw" => "Twi",
			"ug" => "Uyghur",
			"uk" => "Ukrainian",
			"ur" => "Urdu",
			"uz" => "Uzbek",
			"ve" => "Venda",
			"vi" => "Vietnamese",
			"vo" => "Volapük",
			"wa" => "Wallon",
			"cy" => "Welsh",
			"wo" => "Wolof",
			"fy" => "Western Frisian",
			"xh" => "Xhosa",
			"yi" => "Yiddish-1",
			"ji" => "Yiddish-2",
			"yo" => "Yoruba",
			"za" => "Zhuang, Chuang",
			"zu" => "Zulu",
		];
	}
	public function get_lang_opt() {
		return [
			"af" => "Afrikaans",
			"en_US" => "English",
			"hi_IN" => "Hindi",
			"am" => "አማርኛ",
			"ar" => "العربية",
			"ary" => "العربية المغربية",
			"as" => "অসমীয়া",
			"az" => "Azərbaycan dili",
			"azb" => "گؤنئی آذربایجان",
			"bel" => "Беларуская мова",
			"bg_BG" =>"Български",
			"bn_BD" => "বাংলা",
			"bo" => ">བོད་ཡིག</",
			"bs_BA" => "Bosanski",
			"ca" => "Català",
			"ceb" => "Cebuano",
			"cs_CZ" => "Čeština",
			"cy" => "Cymraeg",
			"da_DK" => "Dansk",
			"de_CH_informal" => "Deutsch (Schweiz, Du)",
			"de_DE" => "Deutsch",
			"de_DE_formal" => "Deutsch (Sie)",
			"de_CH" => "Deutsch (Schweiz)",
			"de_AT" => "Deutsch (Österreich)",
			"dsb" => "Dolnoserbšćina",
			"dzo" => "རྫོང་ཁ",
			"el" => "Ελληνικά",
			"en_ZA" => "English (South Africa)",
			"en_CA" => "English (Canada)",
			"en_NZ" => "English (New Zealand)",
			"en_GB" => "English (UK)",
			"en_AU" => "English (Australia)",
			"eo" => "Esperanto",
			"es_ES" => "Español",
			"es_AR" => "Español de Argentina",
			"es_CO" => "Español de Colombia",
			"es_MX" => "Español de México",
			"es_DO" => "Español de República Dominicana",
			"es_CR" => "Español de Costa Rica",
			"es_PE" => "Español de Perú",
			"es_UY" => "Español de Uruguay",
			"es_CL" => "Español de Chile",
			"es_PR" => "Español de Puerto Rico",
			"es_GT" => "Español de Guatemala",
			"es_EC" => "Español de Ecuador",
			"es_VE" => "Español de Venezuela",
			"et" => "Eesti",
			"eu" => "Euskara",
			"fa_IR" => "فارسی",
			"fa_AF" => "(فارسی (افغانستان",
			"fi" => "Suomi",
			"fr_FR" => "Français",
			"fr_CA" => "Français du Canada",
			"fr_BE" => "Français de Belgique",
			"fur" => "Friulian",
			"gd" => "Gàidhlig",
			"gl_ES" => "Galego",
			"gu" => "ગુજરાતી",
			"haz" => "هزاره گی",
			"he_IL" => "בְרִית",
			"hr" => "Hrvatski",
			"hsb" => "Hornjoserbšćina",
			"hu_HU" => "Magyar",
			"hy" => "Հայերեն",
			"id_ID" => "Bahasa Indonesia",
			"is_IS" => "Íslenska",
			"it_IT" => "Italiano",
			"ja" => "日本語",
			"jv_ID" => "Basa Jawa",
			"ka_GE" => "ქართული",
			"kab" => "Taqbaylit",
			"kk" => "Қазақ тілі",
			"km" => "ភាសាខ្មែរ",
			"kn" => "ಕನ್ನಡ",
			"ko_KR" => "한국어",
			"ckb" => "كوردی&lrm;",
			"lo" => "ພາສາລາວ",
			"lt_LT" => "Lietuvių kalba",
			"lv" => "Latviešu valoda",
			"mk_MK" => "Македонски јазик",
			"ml_IN" => "മലയാളം",
			"mn" => "Монгол",
			"mr" => "मराठी",
			"ms_MY" => "Bahasa Melayu",
			"my_MM" => "ဗမာစာ",
			"nb_NO" => "Norsk bokmål",
			"ne_NP" => "नेपाली",
			"nl_NL" => "Nederlands",
			"nl_BE" => "Nederlands (België)",
			"nl_NL_formal" => "Nederlands (Formeel)",
			"nn_NO" =>  "Norsk nynorsk",
			"oci" =>  "Occitan",
			"pa_IN" =>  "ਪੰਜਾਬੀ",
			"pl_PL" =>  "Polski",
			"ps" => "پښتو",
			"pt_PT" => "Português",
			"pt_PT_ao90" => "Português (AO90)",
			"pt_AO" => "Português de Angola",
			"pt_BR" => "Português do Brasil",
			"rhg" => "Ruáinga",
			"ro_RO" => "Română",
			"ru_RU" => "Русский",
			"sah" => "Сахалыы",
			"snd" => "سنڌي",
			"si_LK" => "සිංහල",
			"sk_SK" => "Slovenčina",
			"skr" => "سرائیکی",
			"sl_SI" => "Slovenščina",
			"sq" => "Shqip",
			"sr_RS" => "Српски језик",
			"sv_SE" => "Svenska",
			"sw" => "Kiswahili",
			"szl" => "Ślōnskŏ gŏdka",
			"ta_IN" => "தமிழ்",
			"ta_LK" => "தமிழ்",
			"te" => "తెలుగు",
			"th" => "ไทย",
			"tl" => "Tagalog",
			"tr_TR" => "Türkçe",
			"tt_RU" => "Татар теле",
			"tah" => "Reo Tahiti",
			"ug_CN" => "ئۇيغۇرچە",
			"uk" => "Українська",
			"ur" => "اردو",
			"uz_UZ" => "O‘zbekcha",
			"vi" => "Tiếng Việt",
			"zh_HK" => "香港中文版",
			"zh_TW" => "繁體中文",
			"zh_CN" => "简体中文",
		];
	}

	public function get_woo_cat() {
		$categories = [];
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {			
			$categories = get_categories(array('taxonomy' => 'product_cat','hide_empty' => 0));
	
			if ( empty( $categories ) || ! is_array( $categories ) ) {
				return array();
			}
		}
		
		
		return wp_list_pluck( $categories, 'name', 'term_id' );
	}

	public function get_woo_tag() {
		$tags = [];
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$tags = get_tags(array('taxonomy' => 'product_tag','hide_empty' => 0));
	
			if ( empty( $tags ) || ! is_array( $tags ) ) {
				return array();
			}
		}		
		
		return wp_list_pluck( $tags, 'name', 'term_id' );
	}

	public function get_woo_product(){
		$values = [];
		
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$args = get_posts(
				array(
					'post_type'             => 'product',
					'posts_per_page'        => -1,
					'post_status' 			=> 'publish',
					'fields'                => array( 'ids' ),
				)
			);
			
	
			if ( ! empty( $args ) && ! is_wp_error( $args ) ) {
				foreach ( $args as $prod ) {
					$values[ $prod->ID ] = $prod->post_title;
				}
			}
		}		
		
		return $values;
		
	}

	public function tp_get_woo_country(){
		$values = [];
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$countries          = new \WC_Countries();		
			$countries          = $countries->get_countries();

			foreach ( $countries as $key => $country ) {
				$values[ $key ] = html_entity_decode( $country );
			}
		}		
		
		return $values;
		
	}

	/**
	 * Add Actions
	 *
	 *
	 * @access protected
	 */
	protected function plus_add_actions() {

		$this->set_rules_options();

		// Activate controls for widgets		
		add_action( 'elementor/element/section/section_custom_css/after_section_end', [ $this, 'add_rules_controls' ], 10, 2 );

		// Rules for widgets
		add_action( 'elementor/widget/render_content', function( $widget_content, $element ) {

			$settings = $element->get_settings();

			if ( !empty($settings[ 'tp_display_rules_enable' ]) && 'yes' === $settings[ 'tp_display_rules_enable' ] ) {

				// Set the rules
				$this->set_rules( $element->get_id(), $settings['tp_display_rules'] );

				
				if ( ! $this->display_is_visible( $element->get_id(), $settings['tp_display_rules_relation'] ) && !empty($settings['tp_display_rules_relation'])) { // Check the rules
					if ( 'yes' !== $settings['tp_display_rules_output'] ) {
						return; // And on frontend we stop the rendering of the widget
					}
				}
			}
   
			return $widget_content;
		
		}, 10, 2 );

		// Rules for widgets
		//add_action("elementor/frontend/widget/before_render", [$this, 'tp_start_section'], 10, 1);
        //add_action("elementor/frontend/widget/after_render", [$this, 'tp_end_section'], 10, 1);

		add_action( 'elementor/frontend/widget/before_render', function( $element ) {
			
			$settings = $element->get_settings();

			if ( !empty($settings[ 'tp_display_rules_enable' ]) && 'yes' === $settings[ 'tp_display_rules_enable' ] ) {

				// Set the rules
				$this->set_rules( $element->get_id(), $settings['tp_display_rules'] );

				if ( ! $this->display_is_visible( $element->get_id(), $settings['tp_display_rules_relation'] ) && !empty($settings['tp_display_rules_relation']) ) { // Check the rules
					$element->add_render_attribute( '_wrapper', 'class', 'plus-conditions--hidden' );
				}
			}

		}, 10, 1 );

		// Rules for sections
		
		add_action("elementor/frontend/section/before_render", [$this, 'tp_start_section'], 10, 1);
        add_action("elementor/frontend/section/after_render", [$this, 'tp_end_section'], 10, 1);

		$experiments_manager = Plugin::$instance->experiments;		
		if($experiments_manager->is_feature_active( 'container' )){
			add_action("elementor/frontend/container/before_render", [$this, 'tp_start_section'], 10, 1);
       		add_action("elementor/frontend/container/after_render", [$this, 'tp_end_section'], 10, 1);
		}

	}
	public function tp_start_section($element){
		$settings = $element->get_settings();
		if ( !empty($settings[ 'tp_display_rules_enable' ]) && 'yes' === $settings[ 'tp_display_rules_enable' ] ) {

			// Set the rules
			$this->set_rules( $element->get_id(), $settings['tp_display_rules'] );

			if ( ! $this->display_is_visible( $element->get_id(), $settings['tp_display_rules_relation'] ) && !empty($settings['tp_display_rules_relation']) ) { // Check the rules
				if ( 'yes' !== $settings['tp_display_rules_output'] ) {
					 echo '<!--Theplus  Hidden Section-->';
					 ob_start();
				}else{
					$element->add_render_attribute( '_wrapper', 'class', 'plus-conditions--hidden' );
				}
			}
		}
	}
	public function tp_end_section($element){
		$settings = $element->get_settings();
		if ( !empty($settings[ 'tp_display_rules_enable' ]) && 'yes' === $settings[ 'tp_display_rules_enable' ] ) {

			// Set the rules
			$this->set_rules( $element->get_id(), $settings['tp_display_rules'] );

			if ( ! $this->display_is_visible( $element->get_id(), $settings['tp_display_rules_relation'] ) && !empty($settings['tp_display_rules_relation']) ) { // Check the rules
				if ( 'yes' !== $settings['tp_display_rules_output'] ) {
					$content = ob_get_clean();
				}else{
					$element->add_render_attribute( '_wrapper', 'class', 'plus-conditions--hidden' );
				}				
			}
		}
	}
	protected function render_editor_notice( $settings ) {
		?><span><?php echo esc_html__('This widget is displayed rules condition.','theplus'); ?></span>
		<?php
	}

	/**
	 * Set rules.
	 *
	 * Sets the rules methods to all rules comparison values
	 *
	 * @access protected
	 * @static
	 *
	 * @param mixed  $rules The rules from the repeater field control
	 *
	 * @return void
	 */
	protected function set_rules( $id, $rules = [] ) {
		
		if ( ! $rules )
			return;

		foreach ( $rules as $index => $rule ) {
			$key 		= $rule['tp_rule_key'];

			$mode = !empty($rule['tp_rule_woo_items_mode']) ? $rule['tp_rule_woo_items_mode'] : '';

			$shortcodeoutput = !empty($rule['tp_rule_tp_shortcode_output']) ? $rule['tp_rule_tp_shortcode_output'] : '';
			$dateendoutput = !empty($rule['tp_rule_timerange_output']) ? $rule['tp_rule_timerange_output'] : '';
			
			$key_name =null;
			
			if ( array_key_exists( 'tp_rule_' . $key . '_name' , $rule ) ) {
				$key_name = $rule['tp_rule_' . $key . '_name'];
			}
			
			$check_is_not 	= $rule['tp_rule_operator'];
			$value 		= $rule['tp_rule_' . $key . '_value'];

			if ( method_exists( $this, 'plus_check_' . $key ) ) {
				$check = call_user_func( [ $this, 'plus_check_' . $key ], $value, $check_is_not,$key_name ,$mode, $shortcodeoutput,$dateendoutput);
				$this->conditions[ $id ][ $key . '_' . $rule['_id'] ] = $check;
			}
		}
	}

	/**
	 * Check rules.
	 *
	 * Checks for all or any rules and returns true or false
	 * @access protected
	 * @static
	 *
	 * @return bool
	 */
	protected function display_is_visible( $id, $relation ) {

		if ( ! array_key_exists( $id, $this->conditions ) )
			return;

		if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			if ( $relation === 'any' ) {
				if ( ! in_array( true, $this->conditions[ $id ] ) )
					return false;
			} else {
				if ( in_array( false, $this->conditions[ $id ] ) )
					return false;
			}
		}

		return true;
	}

	/**
	 * compare_check rules.
	 *
	 * Compare values is or not
	 *
	 * @access protected
	 * @static
	 *
	 * @param mixed  First value to compare_check.
	 * @param mixed  Second value to compare_check.
	 * @param string Comparison values.
	 *
	 * @return bool
	 */
	protected static function compare_check( $first_value, $second_value, $check_is_not ) {
		switch ( $check_is_not ) {
			case 'is':
				return $first_value == $second_value;
			case 'not':
				return $first_value != $second_value;
			default:
				return $first_value === $second_value;
		}
	}

	/**
	 * Check user login status
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string  $check_is_not  Comparison value.
	 */
	protected static function plus_check_authentication( $value, $check_is_not, $key ) {
		return self::compare_check( is_user_logged_in(), true, $check_is_not );
	}

	/**
	 * Check user role
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string  $check_is_not  Comparison value.
	 */
	protected static function plus_check_role( $value, $check_is_not, $key ) {

		$user = wp_get_current_user();
		
		return self::compare_check( is_user_logged_in() && in_array( $value, $user->roles ), true, $check_is_not );
	}
	
	/**
	 * Check time of day interval
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string  $check_is_not  Comparison value.
	 */
	protected static function plus_check_time( $value, $check_is_not, $key ) {

		$time 	= date( 'H:i', strtotime( preg_replace('/\s+/', '', $value ) ) );
		$now 	= date( 'H:i', strtotime("now") + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
		
		$display 	= false;

		if ( \DateTime::createFromFormat( 'H:i', $time ) === false ) // Make sure it's a valid DateTime format
			return;
		
		$time_ts 	= strtotime( $time );
		$now_ts 	= strtotime( $now );
		
		$display = ( $now_ts < $time_ts );

		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check date interval 
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string  $check_is_not  Comparison value.
	 */
	protected static function plus_check_date( $value, $check_is_not, $key ) {

		$between = explode( 'to' , preg_replace('/\s+/', '', $value ) );

		if ( ! is_array( $between ) || 2 !== count( $between ) ) 
			return;

		$today 	= date('Y-m-d');
		$start_date 	= $between[0];
		$end_date 	= $between[1];		

		$display 	= false;

		if ( \DateTime::createFromFormat( 'Y-m-d', $start_date ) === false || // Make sure it's a date
			 \DateTime::createFromFormat( 'Y-m-d', $end_date ) === false ) // Make sure it's a date
			return;

		$start 	= strtotime( $start_date ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		$end 	= strtotime( $end_date ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		$today_date 	= strtotime( $today ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );

		$display = ( ($today_date >= $start ) && ( $today_date <= $end ) );

		return self::compare_check( $display, true, $check_is_not );
	}

	
	/**
	 * Check date range
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_timerange( $value, $check_is_not, $key , $mode ,$shortcodeoutput,$dateendoutput) {
		$time 	= date( 'H:i', strtotime( preg_replace('/\s+/', '', $value ) ) );
		$time1 	= date( 'H:i', strtotime( preg_replace('/\s+/', '', $dateendoutput ) ) );
		$now 	= date( 'H:i', strtotime("now") + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
		
		$display 	= false;
		if ( \DateTime::createFromFormat( 'H:i', $time ) === false  && \DateTime::createFromFormat( 'H:i', $time1 ) === false ) // Make sure it's a valid DateTime format
			return;

		$time_ts 	= strtotime( $time );
		$time_ts1 	= strtotime( $time1 );
		$now_ts 	= strtotime( $now );
		
		if( $now_ts > $time_ts && $now_ts < $time_ts1){
			$display = true;
		}
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check day of week name
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string  $check_is_not  Comparison value.
	 */
	protected static function plus_check_day( $value, $check_is_not, $key ) {

		$display = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( $_value === date( 'w' ) ) {
					$display = true; break;
				}
			}
		} else { $display = $value === date( 'w' ); }

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check operating system of visitor
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_os( $value, $check_is_not, $key ) {

		$os_list = [
			'iphone'            => '(iPhone)',
			'android'           => '(Android)',
			'safari'            => '(Safari)',
			'mac_os'            => '(Mac_PowerPC)|(Macintosh)',
			'windows' 			=> 'Win16|(Windows 95)|(Win95)|(Windows_95)|(Windows 98)|(Win98)|(Windows NT 5.0)|(Windows 2000)|(Windows NT 5.1)|(Windows XP)|(Windows NT 5.2)|(Windows NT 6.0)|(Windows Vista)|(Windows NT 6.1)|(Windows 7)|(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)|Windows ME',			
			'beos'              => 'BeOS',
			'linux'             => '(Linux)|(X11)',			
			'open_bsd'          => 'OpenBSD',
			'qnx'               => 'QNX',			
			'os2'              	=> 'OS/2',
			'search_bot'        => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)',
			'sun_os'            => 'SunOS',			
		];

		return self::compare_check( preg_match('@' . $os_list[ $value ] . '@', $_SERVER['HTTP_USER_AGENT'] ), true, $check_is_not );
	}

	/**
	 * Check browser of visitor
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_browser( $value, $check_is_not, $key ) {

		$browsers_list = [
			'ie'			=> [
				'MSIE',
				'Trident',
			],
			'chrome'		=> 'Chrome',
			'firefox'		=> 'Firefox',
			'opera'			=> 'Opera',
			'opera_mini'	=> 'Opera Mini',
			'safari'		=> 'Safari',
		];

		$display = false;

		if ( $value === 'ie' ) {
			if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers_list[ $value ][0] ) || false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers_list[ $value ][1] ) ) {
				$display = true;
			}
		} else {
			if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers_list[ $value ] ) ) {
				$display = true;

				// Additional check for Chrome that returns Safari
				if ( $value === 'firefox' || $value === 'safari' ) {
					if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'Chrome' ) ) {
						$display = false;
					}
				}
			}
		}
		

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check location
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_location( $value, $check_is_not, $key ){	
		$display = false;		
		$ip = '';
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
			
		}  else {
			$ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
		}
		
		// elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		// 	$ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
		// }
		
		if( empty($ip) ){
			return;
		}

		if( !empty( self::$tmp_location ) ){
			
			if ( self::$tmp_location['status'] != 'success' ) {
				return;
			}
			
			$ountryName = strtolower( self::$tmp_location['country'] );
			
			if($ountryName == $value){
				$display = true;
			}
			
			return self::compare_check( $display, true, $check_is_not );
		}

		//$url = 'http://www.geoplugin.net/php.gp?ip=' . $ip;
		$url = 'http://ip-api.com/php/' . $ip;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, null);
		curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($curl, CURLOPT_TIMEOUT, 3);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json',));
		$data1=curl_exec($curl);		
		curl_close($curl);
		
		if(!empty($data1)){			
			$data = unserialize($data1);
			self::$tmp_location = $data;

			// if ($data['geoplugin_status']=== 404) {
			// 	return;
			// }

			// $ountryName = strtolower( $data['geoplugin_countryName'] );
			
			if ($data['status'] != 'success') {
				return;
			}

			$ountryName = strtolower( $data['country'] );
			
			if($ountryName === $value){
				$display = true;
			}
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check site language
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_site_language( $value, $check_is_not, $key ) {		
		$display = false;
		$current_lang = function_exists( 'get_locale' ) ? get_locale() : false;		
		if ( ! $current_lang || empty( $value ) ) {
			return;
		}

		if($current_lang === $value){			
			$display = true;
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check browser language
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_browser_language( $value, $check_is_not, $key ) {		
		$display = false;
		$current_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);	
		
		if ( ! $current_lang || empty( $value ) ) {
			return;
		}

		if($current_lang === $value){			
			$display = true;
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check url string
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_url_string( $value, $check_is_not, $key ) {		
		
		if (!isset($_SERVER['REQUEST_URI']) || empty($_SERVER['REQUEST_URI'])){
			return;
		}

		$url = filter_var(wp_unslash($_SERVER['REQUEST_URI']), FILTER_SANITIZE_STRING);

		if (!$url){
			return false;
		}

		$display = false !== strpos($url,$value) ? true : false;
		
		return self::compare_check($display, true, $check_is_not);
	}
	
	/**
	 * Check url parameter
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_url_parameter( $value, $check_is_not, $key ) {	
		$display = false;
		if (!isset($_SERVER['REQUEST_URI']) || empty($_SERVER['REQUEST_URI'])) {
			return;
		}

		$url = wp_parse_url(filter_var(wp_unslash( $_SERVER['REQUEST_URI']), FILTER_SANITIZE_STRING));

		if (!$url || !isset($url['query']) || empty($url['query']) ) {
			return false;
		}

		$parameters = explode( '&', $url['query'] );

		$value = explode( "\n", sanitize_textarea_field( $value ) );

		foreach ( $value as $index => $parameter ) {

			$is_strict = strpos($parameter, '=');
			
			if ( ! $is_strict ) {
				$value[$index] = $value[$index] . '=' . rawurlencode($_GET[$parameter]);
			}
		}
		if(! empty( array_intersect( $value, $parameters ) )){
			$display = true;
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check shortcode
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_tp_shortcode( $value, $check_is_not, $key , $mode ,$shortcodeoutput) {	
		$display = false;

		if (empty($shortcodeoutput)){
			return false;
		}
		
		if(strval(do_shortcode(shortcode_unautop($value)) === $shortcodeoutput)){
			$display = true;
		}

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check first purchase
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_first_purchase( $value, $check_is_not, $key ) {
		$args = array(
			'customer_id' => get_current_user_id(),
			'status'      => array( 'wc-completed' ),
			'limit'       => 1,
			'orderby'     => 'date_completed',
			'order'       => 'ASC',
		);
	
		$order = wc_get_orders( $args );
		
		$date = $order && $order[0] ? date('Y-m-d', strtotime($order[0]->get_Date_completed())) : false;
		
		$display = false;
		if($value == $date && !empty($date)){
			$display = true;
		}		  
		
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check last purchase
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_last_purchase_date( $value, $check_is_not, $key ) {
		$args = array(
			'customer_id' => get_current_user_id(),
			'status'      => array( 'wc-completed' ),
			'limit'       => 1,
			'orderby'     => 'date_completed',
			'order'       => 'DESC',
		);
	
		$order = wc_get_orders( $args );
		
		$date = $order && $order[0] ? date('Y-m-d', strtotime($order[0]->get_Date_completed())) : false;		
		$display = false;
		if(date('Y-m-d', strtotime($value)) == $date && !empty($date)){
			$display = true;
		}		  
		
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check purchase date
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_last_purchase( $value, $check_is_not, $key ) {
		$args = array(
			'customer_id' => get_current_user_id(),
			'status'      => array( 'wc-completed' ),
			'limit'       => -1,
			'orderby'     => 'date_completed',
			'order'       => 'DESC',
		);
	
		$order = wc_get_orders( $args );
		$datearray = [];
		foreach ($order as $value1) {			
			$datearray[] = $value1 ? date('Y-m-d', strtotime($value1->get_Date_completed())) : false;
		}
		
		$display = false;

		foreach ($datearray as $valuedate) {			
			if($value >= $valuedate && !empty($valuedate)){
				$display = true;
			}	
		}		  
		
		return self::compare_check( $display, true, $check_is_not );
	}
	

	/**
	 * Check current product category
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_cur_pro_cat( $value, $check_is_not, $key ) {
		$display = false;
		$id = get_queried_object_id();

		if (!$id) {
			return true;
		}

		$type = get_post_type();

		if($type !== 'product') {
			return true;
		}

		$product = wc_get_product( $id );

		$category = $product->get_category_ids();
		
		if(!empty(array_intersect((array) $value, $category))){
			$display = true;
		}

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check current product price
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_cur_pro_price( $value, $check_is_not, $key ) {
		$display = false;
		$id = get_queried_object_id();

		if (!$id) {
			return true;
		}

		$type = get_post_type();

		if($type !== 'product') {
			return true;
		}

		$product = wc_get_product( $id );

		$price = $product->get_price();			
		
		if((int)$price >= (int)$value){
			$display = true;
		}

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check current product stock
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_cur_pro_stock( $value, $check_is_not, $key ) {
		$display = false;

		if(get_the_ID()){
			$id = get_the_ID();
		}else{
			$id = get_queried_object_id();
		}
		
		if (!$id) {
			return true;
		}

		$type = get_post_type();

		if($type !== 'product') {
			return true;
		}

		$product = wc_get_product( $id );

		$qty = !empty($product->get_stock_quantity()) ? $product->get_stock_quantity() : 0;		
		
		if((int)$value === 0){			
			//$qty = $product->is_in_stock() || $product->backorders_allowed();			
			if((int)$value === $qty){
				$display = true;
			}
		}else if((int)$qty >= (int)$value){			
			$display = true;
		}

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check cart sub total
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_cart_subtotal( $value, $check_is_not, $key ) {
		$display = false;
		if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$cart = WC()->cart;

			if ( $cart->is_empty() ) {
				return false;
			}

			$sub_total = $cart->get_displayed_subtotal();

			if((int)$value <= (int)$sub_total){
				$display = true;
			}
		}		

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check cart total
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_cart_total( $value, $check_is_not, $key ) {
		$display = false;
		if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$cart = WC()->cart;

			if ( !is_null($cart) && !$cart->is_empty() ) {
				$total = $cart->total;

				if((int)$value <= (int)$total){
					$display = true;
				}
			}
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check purchase total
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_purchase_total( $value, $check_is_not, $key ) {
		$display = false;
		if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$args = array(
				'customer_id' => get_current_user_id(),
				'status'      => array( 'wc-completed' ),
				'limit'       => -1,
				'orderby'     => 'date_completed',
				'order'       => 'DESC',
			);
			
			$order = wc_get_orders( $args );
			
			$totalarray = [];
			foreach ($order as $value1) {				
				if($value1->get_status() == 'completed'){
					$totalarray[] = $value1 ? $value1->get_total() : false;
				}				
			}			
			
			if((int)$value <= (int)array_sum($totalarray) && !empty($totalarray)){				
				$display = true;
			}
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check cart item
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_cart_item( $value, $check_is_not, $key, $mode) {
		$display = false;
		
		if ($value === '') {
			return true;
		}
		$count = WC()->cart->get_cart_contents_count();

		if($mode === 'equal' && (int) $value === $count){
			$display = true;
		}else if($mode === 'equal_or_higher' && (int) $value <= $count){
			$display = true;
		}
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check purchased item
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_purchase_item( $value, $check_is_not, $key, $mode) {
		$display = false;
		if ($value === '') {
			return true;
		}
		$args = array(
			'customer_id' => get_current_user_id(),
			'status'      => array( 'wc-completed' ),
		);

		$count = count( wc_get_orders( $args ) );

		if($mode === 'equal' && (int) $value === $count){
			$display = true;
		}else if($mode === 'equal_or_higher' && (int) $value <= $count){
			$display = true;
		}

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check woo cart product category
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_cart_pro_cat( $value, $check_is_not, $key ) {
		$display = false;
		if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$cart = WC()->cart;
			$category = array();

			if ($cart->is_empty()){
				return false;
			}

			foreach ($cart->get_cart() as $cart_item_key => $item){
				$product = $item['data'];
				if ($product->is_type('variation')){
					$product = wc_get_product($product->get_parent_id());
				}

				$category = array_merge($category, $product->get_category_ids());
			}

			if(!empty(array_intersect((array) $value, $category))){
				$display = true;
			}
		}		

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check woo cart product tag
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_cart_pro_tag( $value, $check_is_not, $key ) {
		$display = false;
		if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$cart = WC()->cart;
			$tag = array();

			if ($cart->is_empty()){
				return false;
			}

			foreach ($cart->get_cart() as $cart_item_key => $item){
				$product = $item['data'];
				if ($product->is_type('variation')){
					$product = wc_get_product($product->get_parent_id());
				}				
				$tag = array_merge($tag, $product->get_tag_ids());
			}

			if(!empty(array_intersect((array) $value, $tag))){
				$display = true;
			}
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check woo purchase product category
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_purchase_pro_cat($value, $check_is_not, $key){		
		$display = false;
		if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$cart = WC()->cart;
			$category = array();

			$args = array(
				'numberposts' => -1,
				'meta_key'    => '_customer_user',
				'meta_value'  => get_current_user_id(),
				'post_type'   => wc_get_order_types(),
				'post_status' => array_keys( wc_get_is_paid_statuses() ),
			);

			$orders = get_posts($args);		
			$ids = array();
			if(isset($orders)){
				foreach($orders as $order){

					$order = wc_get_order($order->ID);
					$items = $order->get_items();
					foreach ($items as $item) {
						$id    = $item->get_product_id();
						$ids[] = $id;
					}
				}

				foreach ($ids as $id){
					$product = wc_get_product($id);

					if ($product->is_type('variation')) {
						$product = wc_get_product($product->get_parent_id());
					}

					$category = array_merge($category, $product->get_category_ids());
				}
				
				if(!empty(array_intersect((array) $value, $category))){
					$display = true;
				}
			}
		}
		
		return self::compare_check($display, true, $check_is_not);
	}

	/**
	 * Check woo purchase product
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_purchase_pro_name($value, $check_is_not, $key){		
		$display = false;
		if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$cart = WC()->cart;
			$proname = array();

			$args = array(
				'numberposts' => -1,
				'meta_key'    => '_customer_user',
				'meta_value'  => get_current_user_id(),
				'post_type'   => wc_get_order_types(),
				'post_status' => array_keys( wc_get_is_paid_statuses() ),
			);

			$orders = get_posts($args);		
			$ids = array();
			if(isset($orders)){
				foreach($orders as $order){

					$order = wc_get_order($order->ID);
					$items = $order->get_items();
					foreach ($items as $item) {
						$id    = $item->get_product_id();
						$ids[] = $id;
					}
				}

				foreach ($ids as $id){
					$product = wc_get_product($id);
					
					if ($product->is_type('variation')) {
						$product = wc_get_product($product->get_parent_id());
					}
					
					
					if(!empty($product->get_id()) && ((in_array($product->get_id(), $value)))){
						$display = true;
					}
				}
			}
		}
		
		return self::compare_check($display, true, $check_is_not);
	}

	/**
	 * Check woo cart product
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_cart_product($value, $check_is_not, $key){		
		$display = false;
		if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$cart = WC()->cart;
			$ids = [];

			if ($cart->is_empty()){
				return false;
			}

			foreach ($cart->get_cart() as $key => $item){
				$product = $item['data'];

				if ($product->is_type('variation')){
					$product = wc_get_product($product->get_parent_id());
				}
				
				array_push($ids, $product->get_id());
			}
			
			if(!empty(array_intersect((array)$value,$ids))){
				$display = true;
			}
			
			return self::compare_check( $display, true, $check_is_not );
		}		
	}

	/**
	 * Check woo billing city
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_po_bill_city($value, $check_is_not, $key){		
		$display = false;
		if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$cart = WC()->cart;
			$category = array();

			$args = array(
				'numberposts' => -1,
				'meta_key'    => '_customer_user',
				'meta_value'  => get_current_user_id(),
				'post_type'   => wc_get_order_types(),
				'post_status' => array_keys( wc_get_is_paid_statuses() ),
			);
			
			$orders = get_posts($args);		
			$ids = array();
			if(isset($orders)){
				foreach($orders as $order){
					$order = wc_get_order($order->ID);				
					$items = $order->get_billing_city();				
				}
				if(!empty($items) && !empty($value) && (strtolower($items) === strtolower($value))){
					$display = true;
				}
			}
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check woo billing state
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_po_bill_state($value, $check_is_not, $key){		
		$display = false;
		if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$cart = WC()->cart;
			$category = array();

			$args = array(
				'numberposts' => -1,
				'meta_key'    => '_customer_user',
				'meta_value'  => get_current_user_id(),
				'post_type'   => wc_get_order_types(),
				'post_status' => array_keys( wc_get_is_paid_statuses() ),
			);
			
			$orders = get_posts($args);		
			$ids = array();
			if(isset($orders)){
				foreach($orders as $order){
					$order = wc_get_order($order->ID);				
					$items = $order->get_billing_state();				
				}
				if(!empty($items) && !empty($value) && (strtolower($items) === strtolower($value))){
					$display = true;
				}
			}
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check woo billing country
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_po_bill_country($value, $check_is_not, $key){		
		$display = false;
		if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$cart = WC()->cart;
			$category = array();

			$args = array(
				'numberposts' => -1,
				'meta_key'    => '_customer_user',
				'meta_value'  => get_current_user_id(),
				'post_type'   => wc_get_order_types(),
				'post_status' => array_keys( wc_get_is_paid_statuses() ),
			);
			
			$orders = get_posts($args);		
			$ids = array();
			if(isset($orders)){
				foreach($orders as $order){
					$order = wc_get_order($order->ID);				
					$items = $order->get_billing_country();				
				}
				if(!empty($items) && !empty($value) && (in_array($items, $value))){
					$display = true;
				}
			}
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check woo billing postcode
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_po_bill_postcode($value, $check_is_not, $key){		
		$display = false;
		if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$cart = WC()->cart;
			$category = array();

			$args = array(
				'numberposts' => -1,
				'meta_key'    => '_customer_user',
				'meta_value'  => get_current_user_id(),
				'post_type'   => wc_get_order_types(),
				'post_status' => array_keys( wc_get_is_paid_statuses() ),
			);
			
			$orders = get_posts($args);		
			$ids = array();
			if(isset($orders)){
				foreach($orders as $order){
					$order = wc_get_order($order->ID);				
					$items = $order->get_billing_postcode();						
				}
				if(!empty($items) && !empty($value) && ((int)$items === $value)){
					$display = true;
				}
			}
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check woo shipping city
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_po_ship_city($value, $check_is_not, $key){		
		$display = false;
		if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$cart = WC()->cart;
			$category = array();

			$args = array(
				'numberposts' => -1,
				'meta_key'    => '_customer_user',
				'meta_value'  => get_current_user_id(),
				'post_type'   => wc_get_order_types(),
				'post_status' => array_keys( wc_get_is_paid_statuses() ),
			);
			
			$orders = get_posts($args);		
			$ids = array();
			if(isset($orders)){
				foreach($orders as $order){
					$order = wc_get_order($order->ID);				
					$items = $order->get_shipping_city();				
				}
				if(!empty($items) && !empty($value) && (strtolower($items) === strtolower($value))){
					$display = true;
				}
			}
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check woo shipping state
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_po_ship_state($value, $check_is_not, $key){		
		$display = false;
		if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$cart = WC()->cart;
			$category = array();
	
			$args = array(
				'numberposts' => -1,
				'meta_key'    => '_customer_user',
				'meta_value'  => get_current_user_id(),
				'post_type'   => wc_get_order_types(),
				'post_status' => array_keys( wc_get_is_paid_statuses() ),
			);
			
			$orders = get_posts($args);		
			$ids = array();
			if(isset($orders)){
				foreach($orders as $order){
					$order = wc_get_order($order->ID);				
					$items = $order->get_shipping_state();				
				}
				if(!empty($items) && !empty($value) && (strtolower($items) === strtolower($value))){
					$display = true;
				}
			}
		}
	
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check woo shipping country
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_po_ship_country($value, $check_is_not, $key){		
		$display = false;
		if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$cart = WC()->cart;
			$category = array();		
			$args = array(
				'numberposts' => -1,
				'meta_key'    => '_customer_user',
				'meta_value'  => get_current_user_id(),
				'post_type'   => wc_get_order_types(),
				'post_status' => array_keys( wc_get_is_paid_statuses() ),
			);
			
			$orders = get_posts($args);		
			$ids = array();
			if(isset($orders)){
				foreach($orders as $order){
					$order = wc_get_order($order->ID);				
					$items = $order->get_shipping_country();
				}
				
				if(!empty($items) && !empty($value) && (in_array($items, $value))){
					$display = true;
				}
			}
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check woo shipping postcode
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_woo_po_ship_postcode($value, $check_is_not, $key){		
		$display = false;
		if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
			$cart = WC()->cart;
			$category = array();

			$args = array(
				'numberposts' => -1,
				'meta_key'    => '_customer_user',
				'meta_value'  => get_current_user_id(),
				'post_type'   => wc_get_order_types(),
				'post_status' => array_keys( wc_get_is_paid_statuses() ),
			);
			
			$orders = get_posts($args);		
			$ids = array();
			if(isset($orders)){
				foreach($orders as $order){
					$order = wc_get_order($order->ID);				
					$items = $order->get_shipping_postcode();						
				}
				if(!empty($items) && !empty($value) && ((int)$items === $value)){
					$display = true;
				}
			}
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check current page
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_page( $value, $check_is_not, $key ) {
		$display = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_page( $_value ) ) {
					$display = true; break;
				}
			}
		} else { $display = is_page( $value ); }

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check current post
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_post( $value, $check_is_not, $key ) {
		$display = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_single( $_value ) || is_singular( $_value ) ) {
					$display = true; break;
				}
			}
		} else { $display = is_single( $value ) || is_singular( $value ); }

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check current post type
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_post_type( $value, $check_is_not, $key ) {
		
		$display = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_singular( $_value ) ) {
					$display = true; break;
				}
			}
		} else { $display = is_singular( $value ); }

		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check browser of visitors
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_static_page( $value, $check_is_not, $key ) {

		if ( $value === 'home' ) {
			return self::compare_check( ( is_front_page() && is_home() ), true, $check_is_not );
		} elseif ( $value === 'static' ) {
			return self::compare_check( ( is_front_page() && ! is_home() ), true, $check_is_not );
		} elseif ( $value === 'blog' ) {
			return self::compare_check( ( ! is_front_page() && is_home() ), true, $check_is_not );
		} elseif ( $value === '404' ) {
			return self::compare_check( is_404(), true, $check_is_not );
		}
	}
	
	/**
	 * Check current taxonomy archive
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not Comparison value.
	 */
	protected static function plus_check_taxonomy_archive( $value, $check_is_not, $key ) {
		$display = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {

				$display = self::plus_check_taxonomy_archive_type( $_value );

				if ( $display ) break;
			}
		} else { $display = self::plus_check_taxonomy_archive_type( $value ); }

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Checks taxonomy current page template
	 *
	 * @access protected
	 *
	 * @param string  $taxonomy The taxonomy to check value
	 */
	protected static function plus_check_taxonomy_archive_type( $taxonomy ) {
		
		if ( $taxonomy === 'category' ) {
			return is_category();
		} else if ( $taxonomy === 'post_tag' ) {
			return is_tag();
		} else if ( $taxonomy === '' || empty( $taxonomy ) ) {
			return is_tax() || is_category() || is_tag();
		} else {
			return is_tax( $taxonomy );
		}

		return false;
	}

	/**
	 * Check current taxonomy terms archive
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_term_archive( $value, $check_is_not, $key ) {
		$display = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {

				$display = self::plus_check_term_archive_type( $_value );

				if ( $display ) break;
			}
		} else { $display = self::plus_check_term_archive_type( $value ); }

		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check current taxonomy terms single
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_term_single( $value, $check_is_not, $key ) {
		$display = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {

				$display = self::plus_check_term_single_type( $_value );

				if ( $display ) break;
			}
		} else { $display = self::plus_check_term_single_type( $value ); }

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Checks taxonomy term current page template
	 *
	 * @access protected
	 *
	 * @param string  $taxonomy  The taxonomy to check value
	 */
	protected static function plus_check_term_archive_type( $term ) {

		if ( is_category( $term ) ) {
			return true;
		} else if ( is_tag( $term ) ) {
			return true;
		} else if ( is_tax() ) {
			if ( is_tax( get_queried_object()->taxonomy, $term ) ) {
				return true;
			}
		}

		return false;
	}
	
	/**
	 * Checks taxonomy term current page template
	 *
	 * @access protected
	 *
	 * @param string  $taxonomy  The taxonomy to check value
	 */
	protected static function plus_check_term_single_type( $term ) {

		if ( in_category( $term ) ) {
			return true;
		} else if ( is_tag( $term ) ) {
			return true;
		} else if ( is_tax() ) {
			if ( is_tax( get_queried_object()->taxonomy, $term ) ) {
				return true;
			}
		}

		return false;
	}
	
	/**
	 * Check current post type archive
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_post_type_archive( $value, $check_is_not, $key ) {
		
		$display = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_post_type_archive( $_value ) ) {
					$display = true; break;
				}
			}
		} else { $display = is_post_type_archive( $value ); }

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check current date archive
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_date_archive( $value, $check_is_not, $key ) {
		
		$display = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( self::plus_check_date_archive_type( $_value ) ) {
					$display = true; break;
				}
			}
		} else { $display = is_date( $value ); }

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Checks date type current page template
	 *
	 * @access protected
	 *
	 * @param string  $type The type of date archive to check value
	 */
	protected static function plus_check_date_archive_type( $type ) {
		
		if ( $type === 'day' ) { 
			return is_day();
		} elseif ( $type === 'month' ) { 
			return is_month();
		} elseif ( $type === 'year' ) { 
			return is_year();
		}

		return false;
	}


	/**
	 * Check current search query
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_search_results( $value, $check_is_not, $key ) {
		$display = false;

		if ( is_search() ) {

			if ( empty( $value ) ) {
				$display = true;
			} else {
				$phrase = get_search_query();

				if ( '' !== $phrase && ! empty( $phrase ) ) { 

					$keywords = explode( ',', $value ); 

					foreach ( $keywords as $index => $keyword ) {
						if ( self::tp_keyword_exists( trim( $keyword ), $phrase ) ) {
							$display = true; break;
						}
					}
				}
			}
		}

		return self::compare_check( $display, true, $check_is_not );
	}

	protected static function tp_keyword_exists( $keyword, $phrase ) {
		return strpos( $phrase, trim( $keyword ) ) !== false;
	}

	/**
	 * Check current author archive
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_author_archive( $value, $check_is_not, $key ) {
		$display = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_author( $_value ) ) {
					$display = true; break;
				}
			}
		} else {
			$display = is_author( $value ); 
		}

		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check Jet Engine Text Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_jetengine_text( $value, $check_is_not, $key ) {
		$display = false;
		if(class_exists('Jet_Engine')) {

			if(empty($key) && empty($value)){
				return true;
			}		
			$post_data = array();
			if(!isset($GLOBALS['post'])){
				return $post_data;
			}
			$post_data = $GLOBALS['post'];

			if(empty($post_data)){
				$post_data = get_post(0);
			}

			$post_id = $post_data->ID;
					
			$field_value = get_post_meta($post_id,$key,true);

			if ( $field_value === $value) {
				if ( '' === trim( $value ) ) {
					return self::compare_check( true, true, $check_is_not );
				}

				if($field_value === $value){
					$display = true;
				}
			}
		}

		return self::compare_check( $display, true, $check_is_not );
	}
	
	
	/**
	 * Check Jet Engine Textarea Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_jetengine_textarea( $value, $check_is_not, $key ) {
		$display = false;
		if(class_exists('Jet_Engine')) {

			if(empty($key) && empty($value)){
				return true;
			}		
			$post_data = array();
			if(!isset($GLOBALS['post'])){
				return $post_data;
			}
			$post_data = $GLOBALS['post'];

			if(empty($post_data)){
				$post_data = get_post(0);
			}

			$post_id = $post_data->ID;
					
			$field_value = get_post_meta($post_id,$key,true);

			if ( $field_value === $value) {
				if ( '' === trim( $value ) ) {
					return self::compare_check( true, true, $check_is_not );
				}

				if($field_value === $value){
					$display = true;
				}
			}
		}

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check Jet Engine Switcher Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_jetengine_switcher( $value, $check_is_not, $key ) {
		$display = false;
		if(class_exists('Jet_Engine')) {

			if(empty($key) && empty($value)){
				return true;
			}		
			$post_data = array();
			if(!isset($GLOBALS['post'])){
				return $post_data;
			}
			$post_data = $GLOBALS['post'];

			if(empty($post_data)){
				$post_data = get_post(0);
			}

			$post_id = $post_data->ID;
					
			$field_value = get_post_meta($post_id,$key,true);
			
			if ( $field_value === $value) {
				if ( '' === trim( $value ) ) {
					return self::compare_check( true, true, $check_is_not );
				}

				if($field_value === $value){
					$display = true;
				}
			}
		}

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check Jet Engine Checkbox Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_jetengine_checkbox( $value, $check_is_not, $key ) {
		$display = false;
		if(class_exists('Jet_Engine')) {

			if(empty($key) && empty($value)){
				return true;
			}		
			$post_data = array();
			if(!isset($GLOBALS['post'])){
				return $post_data;
			}
			$post_data = $GLOBALS['post'];

			if(empty($post_data)){
				$post_data = get_post(0);
			}

			$post_id = $post_data->ID;
					
			$field_value = get_post_meta($post_id,$key,true);
			
			if( !empty($field_value[$value] )  && $field_value[$value] === 'true'){
				$display = true;
			}

			
		}

		return self::compare_check( $display, true, $check_is_not );
	}
	
	
	/**
	 * Check Jet Engine Radio Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_jetengine_radio( $value, $check_is_not, $key ) {
		$display = false;
		if(class_exists('Jet_Engine')) {

			if(empty($key) && empty($value)){
				return true;
			}		
			$post_data = array();
			if(!isset($GLOBALS['post'])){
				return $post_data;
			}
			$post_data = $GLOBALS['post'];

			if(empty($post_data)){
				$post_data = get_post(0);
			}

			$post_id = $post_data->ID;
					
			$field_value = get_post_meta($post_id,$key,true);
			
			if ( $field_value === $value) {
				if ( '' === trim( $value ) ) {
					return self::compare_check( true, true, $check_is_not );
				}

				if($field_value === $value){
					$display = true;
				}
			}
			
		}

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check Jet Engine Select Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_jetengine_select( $value, $check_is_not, $key ) {
		$display = false;
		if(class_exists('Jet_Engine')) {

			if(empty($key) && empty($value)){
				return true;
			}		
			$post_data = array();
			if(!isset($GLOBALS['post'])){
				return $post_data;
			}
			$post_data = $GLOBALS['post'];

			if(empty($post_data)){
				$post_data = get_post(0);
			}

			$post_id = $post_data->ID;
					
			$field_value = get_post_meta($post_id,$key,true);
			
			if ( $field_value === $value) {
				if ( '' === trim( $value ) ) {
					return self::compare_check( true, true, $check_is_not );
				}

				if($field_value === $value){
					$display = true;
				}
			}
			
		}

		return self::compare_check( $display, true, $check_is_not );
	}	

	/**
	 * Check Jetengine Number Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_jetengine_number( $value, $check_is_not, $key ) {
		$display = false;
		if(class_exists('Jet_Engine')) {

			if(empty($key) && empty($value)){
				return true;
			}		
			$post_data = array();
			if(!isset($GLOBALS['post'])){
				return $post_data;
			}
			$post_data = $GLOBALS['post'];

			if(empty($post_data)){
				$post_data = get_post(0);
			}

			$post_id = $post_data->ID;
					
			$field_value = get_post_meta($post_id,$key,true);
			
			$fv = str_replace( ',', '', $field_value );
			
			if((int)$fv === (int)$value){
				$display = true;
			}
		}

		return self::compare_check( $display, true, $check_is_not );
	}
	

	/**
	 * Check PODS Text Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_pods_text( $value, $check_is_not, $key ) {
		$display = false;
		if( defined('PODS_VERSION') ) {

			if(empty($key) && empty($value)){
				return true;
			}		
			
			$field_value = pods_field_display($key);

			if ( $field_value ) {
				if ( '' === trim( $value ) ) {
					return self::compare_check( true, true, $check_is_not );
				}

				if($field_value === $value){
					$display = true;
				}
			}
		}

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check PODS Date Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_pods_date( $value, $check_is_not, $key ) {
		$display = false;
		if( defined('PODS_VERSION') ) {
			
			if(empty($key) && empty($value)){
				return;
			}		
			
			$field_value = pods_field_display($key);
			$field_value_con = date("Y-m-d", strtotime($field_value));

			if(strtotime($field_value_con) === strtotime($value)){
				$display = true;
			}
			
		}

		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check PODS Number Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_pods_number( $value, $check_is_not, $key ) {
		$display = false;
		if( defined('PODS_VERSION') ) {

			if(empty($key) && empty($value)){
				return true;
			}		
			
			$field_value = pods_field_display($key);
			$fv = str_replace( ',', '', $field_value );
			
			if((int)$fv === $value){
				$display = true;
			}
		}

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check PODS Boolean Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_pods_boolean( $value, $check_is_not, $key ) {
		$display = false;
		if( defined('PODS_VERSION') ) {

			if(empty($key) && empty($value)){
				return true;
			}		
			
			$field_value = pods_field_display($key);

			if($field_value === 'Yes' && $value === 'true'){
				$display = true;
			}
		}

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check Toolset Text Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_toolset_text( $value, $check_is_not, $key ) {
		$display = false;
		if( defined('WPCF_VERSION') ) {

			if(empty($key) && empty($value)){
				return true;
			}
			global $post;
			$field_value = (types_render_field( $key, array() ));		

			if ( $field_value ) {
				if ( '' === trim( $value ) ) {
					return self::compare_check( true, true, $check_is_not );
				}

				if($field_value === $value){
					$display = true;
				}
			}
		}

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check Toolset Number Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_toolset_number( $value, $check_is_not, $key ) {
		$display = false;
		if( defined('WPCF_VERSION') ) {

			if(empty($key) && empty($value)){
				return true;
			}

			global $post;
			$field_value = (types_render_field( $key, array() ));		

			if ( (int)$field_value ) {
				if ( '' === trim( $value ) ) {
					return self::compare_check( true, true, $check_is_not );
				}

				if((int)$field_value === (int)$value){
					$display = true;
				}
			}
		}
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check Toolset Radion Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_toolset_radio( $value, $check_is_not, $key ) {
		$display = false;
		if( defined('WPCF_VERSION') ) {

			if(empty($key) && empty($value)){
				return true;
			}

			global $post;
			$field_value = (types_render_field( $key, array() ));		
			
			if ( $field_value ) {
				if ( '' === trim( $value ) ) {
					return self::compare_check( true, true, $check_is_not );
				}

				if($field_value === $value){
					$display = true;
				}
			}
		}
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check Toolset Checkbox Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_toolset_checkbox( $value, $check_is_not, $key ) {
		$display = false;
		if( defined('WPCF_VERSION') ) {

			if(empty($key) && empty($value)){
				return true;
			}

			global $post;
			$field_value = (types_render_field( $key, array() ));
			if ( $field_value ) {
				if ( '' === trim( $value ) ) {
					return self::compare_check( true, true, $check_is_not );
				}

				if($field_value === $value){
					$display = true;
				}
			}
		}
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check Toolset Select Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_toolset_select( $value, $check_is_not, $key ) {
		$display = false;
		if( defined('WPCF_VERSION') ) {

			if(empty($key) && empty($value)){
				return true;
			}

			global $post;
			$field_value = (types_render_field( $key, array() ));
			if ( $field_value ) {
				if ( '' === trim( $value ) ) {
					return self::compare_check( true, true, $check_is_not );
				}

				if($field_value === $value){
					$display = true;
				}
			}
		}
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check Toolset Checkboxes Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_toolset_checkboxes( $value, $check_is_not, $key ) {
		$display = false;
		if( defined('WPCF_VERSION') ) {

			if(empty($key) && empty($value)){
				return true;
			}
			
			global $post;
			$field_value = (types_render_field( $key, array() ));		
			
			if($field_value === $value){
				$display = true;		
			}
		}
		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check Acf Text Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_acf_text( $value, $check_is_not, $key ) {
		$display = false;

		global $post;

		$field_value = get_field( $key );
		if ( $field_value === $value ) {
			if ( '' === trim( $value ) ) {
				return self::compare_check( true, true, $check_is_not );
			}

			$field_object = get_field_object( $key );

			switch ( $field_object['type'] ) {
				default:
					$display = $value === $field_value;
					break;
			}
		}

		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check Acf Blooean True/False
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_acf_boolean( $value, $check_is_not, $key ) {
		$display = false;
		
		$value = ( 'true' === $value ) ? true : false;
		
		global $post;
		if(get_sub_field( $key )){
			$field_value = get_sub_field( $key );
		}else{
			$field_value = get_field( $key );
		}		
		if ( $field_value ) {
			$display = $value === $field_value;
		}

		return self::compare_check( $display, true, $check_is_not );
	}
	
	
	/**
	 * Check Acf Date/Time Picker
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_acf_datetime( $value, $check_is_not, $key ) {
		$display = false;
		
		global $post;

		$field_value = get_field_object( $key );

		if ( $field_value ) {
			
			$field_format 	= $field_value['return_format'];
			$field_db_value = get_field( $key, false, false );
			
			//$field_db_value = acf_get_metadata( $post->ID, $field_value['name'] );

			$field_wp_format = 'date_time_picker' === $field_value['type'] ? 'Y-m-d H:i:s' : 'Ymd';

			$date = \DateTime::createFromFormat( $field_wp_format, $field_db_value );
			
			if ( ! $date ) { return; }

			
			$field_value_tp = strtotime( $value );
			$value_tp 		= strtotime( $field_db_value );
		
			$display = $field_value_tp < $value_tp;
		}
		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check Acf Select/Choice Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_acf_select( $value, $check_is_not, $key ) {
		$display = false;

		global $post;
		if(get_sub_field( $key )){
			$field_value = get_sub_field( $key );
		}else{
			$field_value = get_field( $key );
		}
		
		if ( $field_value ) {
			if ( ! $value || '' === trim( $value ) || empty( $value ) ) {
				return self::compare_check( true, true, $check_is_not );
			}

			$field_object 	= get_field_object( $key );
			$field_select 		= $field_object['choices'];
			$is_radio 			= 'radio' === $field_object['type'];
			$is_array 			= 'array' === $field_object['return_format'];
			$field_values 		= self::acf_select_parse_format( $field_value, $is_array, $is_radio );
			$check_values 		= acf_decode_choices( $value );

			$check_by_key 		= array_intersect_key( $field_values, $check_values );
			$check_by_value 	= array_intersect( $field_values, $check_values );

			$display = $check_by_key || $check_by_value || self::acf_label_exists_value( $field_values, $field_select, $check_values );
		}

		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check Acf Select/Choice Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_acf_button_group( $value, $check_is_not, $key ) {
		$display = false;

		global $post;
		if(get_sub_field( $key )){
			$field_value = get_sub_field( $key );
		}else{
			$field_value = get_field( $key );
		}
		
		if ( $field_value ) {
			if ( ! $value || '' === trim( $value ) || empty( $value ) ) {
				return self::compare_check( true, true, $check_is_not );
			}

			$field_object 	= get_field_object( $key );			
			$field_select 		= $field_object['choices'];
			$is_radio 			= 'button_group' === $field_object['type'];
			$is_array 			= 'array' === $field_object['return_format'];
			$field_values 		= self::acf_select_parse_format( $field_value, $is_array, $is_radio );
			$check_values 		= acf_decode_choices( $value );

			$check_by_key 		= array_intersect_key( $field_values, $check_values );
			$check_by_value 	= array_intersect( $field_values, $check_values );

			$display = $check_by_key || $check_by_value || self::acf_label_exists_value( $field_values, $field_select, $check_values );
		}
		
		return self::compare_check( $display, true, $check_is_not );
		
	}
	/**
	 * Check Acf Post Object and Relationship
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_acf_post( $values, $check_is_not, $key ) {
		$display = false;

		global $post;

		$field_value = get_field( $key );
		
		if ( $field_value ) {
			if ( ! $values || '' === $values || empty( $values ) ) {
				return self::compare_check( true, true, $check_is_not );
			}

			$values 		= (array)$values;
			$post_ids = self::parse_post_field_values( $field_value );
			$value_post_ids = array_map('intval', $values );

			$display = ! empty( array_intersect( $post_ids, $value_post_ids ) );
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Parse field values
	 *
	 * Depending on the type of field and return formats this function returns an array with the post IDs set in the field settings
	 */
	public static function parse_post_field_values( $posts ) {
		$output = [];

		if ( is_array( $posts ) ) {
			foreach ( $posts as $post ) {
				$output[] = ( is_a( $post, 'WP_Post' ) ) ? $post->ID : $post;
			}
		} else {
			$output[] = ( is_a( $posts, 'WP_Post' ) ) ? $posts->ID : $posts;
		}

		return $output;
	}
	
	/**
	 * Check Acf Text Fields
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_acfe_image_selector( $value, $check_is_not, $key ) {
		$display = false;

		global $post;

		$field_value = get_field( $key );		
		if ( $field_value ) {
			if ( '' === trim( $value ) ) {
				return self::compare_check( true, true, $check_is_not );
			}			
			$field_object = get_field_object( $key );

			switch ( $field_object['type'] ) {
				default:
					$display = $value === $field_value;
					break;
			}
		}

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check Acf Taxonomy
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_acf_taxonomy( $values, $check_is_not, $key ) {
		$display = false;

		global $post;

		$field_value = get_field( $key );
		
		if ( $field_value ) {
			if ( ! $values || '' === $values || empty( $values ) ) {
				return self::compare_check( true, true, $check_is_not );
			}

			$values 		= (array)$values;
			$term_ids = self::parse_term_field_values( $field_value );
			$value_term_ids = array_map('intval', $values );

			$display = ! empty( array_intersect( $term_ids, $value_term_ids ) );
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Parse field values
	 *
	 * Depending on the return formats and number of field values this function returns an array with the term IDs set in the field settings
	 */
	public static function parse_term_field_values( $terms ) {
		$output = [];

		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				$output[] = ( is_a( $term, 'WP_Term' ) ) ? $term->term_id : $term;
			}
		} else {
			$output[] = ( is_a( $terms, 'WP_Term' ) ) ? $terms->term_id : $terms;
		}

		return $output;
	}
	
	/**
	 * Parse array format
	 *
	 * @param array  	$values  	The array to parse
	 * @param bool  	$array  	If the return format is array
	 * @return array
	 */
	protected static function acf_select_parse_format( $values, $return_array = true, $radio = false ) {
		$output = [];

		if ( $radio ) {
			if ( $return_array ) {
				$output[ $values['value'] ] = $values['label'];
			} else {
				$output[ $values ] = $values;
			}
		} else {
			if(is_array($values)){
				foreach( $values as $index => $value ) {
					if ( $return_array ) {
						$output[ $value['value'] ] = $value['label'];
					} else {
						$output[ $value ] = $value;
					}
				}
			}else{
				if ( $return_array ) {
					$output[ $values['value'] ] = $values['label'];
				}else{
					$output[ $values ] = $values;
				}
			}
		}
		return $output;
	}
	
	/**
	 * Label Exists As Value
	 *
	 */
	protected static function acf_label_exists_value( $values, $choices, $check_values ) {
		foreach( $check_values as $index => $selected_value ) {
			if ( in_array( $index, $choices ) ) {
				$choice_key = array_search( $index, $choices );
				if ( in_array( $choice_key, $values ) ) {
					return true;
				}
			}
		}

		return false;
	}
	
	/**
	 * Check is EDD Cart is empty
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $check_is_not  Comparison value.
	 */
	protected static function plus_check_edd_cart( $value, $check_is_not, $key ) {		
		if ( ! class_exists( 'Easy_Digital_Downloads', false ) )
			return false;

		$display = empty( edd_get_cart_contents() );

		return self::compare_check( $display, true, $check_is_not );
	}
}