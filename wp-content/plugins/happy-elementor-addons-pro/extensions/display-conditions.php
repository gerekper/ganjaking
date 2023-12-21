<?php

namespace Happy_Addons_Pro\Extension\Conditions;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Display_Conditions
 *
 * @package Happy_Addons_Pro\Extension\Conditions
 */
class Display_Conditions {

	public static $cookies = 'false';

	/**
	 * Class instance property
	 * Contain Display_Conditions instance
	 * @var
	 */
	public static $_instance;

	/**
	 * Contain all existing condition class instance
	 * @var array
	 */
	public static $conditions = [];

	/**
	 * Namespace of this class
	 * @var string
	 */
	public static $namespace = __NAMESPACE__;

	/**
	 * Contain condition's result of single element
	 * @var array
	 */
	protected $conditions_store = [];

	/**
	 * Contain all existing condition class key name
	 * @var array
	 */
	public $file_key = [
		'login_status',
		'role',
		'operating_system',
		'browser',
		'date_range',
		'date',
		'time',
		'day',
		'country',
	];

	/**
	 * Condition File Include
	 * set condition's class instance as value to  $conditions property
	 */
	public function conditions_init() {
		foreach ( $this->file_key as $single_key ) {
			$file_name = str_replace( '_', '-', strtolower( $single_key ) );

			if ( file_exists( HAPPY_ADDONS_PRO_DIR_PATH . 'extensions/conditions/' . $file_name . '.php' ) ) {
				include_once HAPPY_ADDONS_PRO_DIR_PATH . 'extensions/conditions/' . $file_name . '.php';
			}

			$class_name = str_replace( '_', ' ', $single_key );
			$class_name = str_replace( ' ', '_', ucwords( $class_name ) );
			$class_name = static::$namespace . '\\' . $class_name;

			if ( class_exists( $class_name ) ) {
				static::$conditions[ $single_key ] = new $class_name();
			}
		}
	}

	/**
	 * Set render function to action filter
	 */
	public function init_actions() {
		add_filter( 'elementor/frontend/container/should_render', [ $this, 'content_render' ], 10, 2 );
		add_filter( 'elementor/frontend/section/should_render', [ $this, 'content_render' ], 10, 2 );
		add_filter( 'elementor/frontend/column/should_render', [ $this, 'content_render' ], 10, 2 );
		add_filter( 'elementor/frontend/widget/should_render', [ $this, 'content_render' ], 10, 2 );
	}

	/**
	 * Render Content base on condition result
	 *
	 * @param $should_render
	 * @param $element
	 * @return bool
	 */
	public function content_render( $should_render, $element ) {
		$settings = $element->get_settings();

		if ( 'yes' === $settings['_ha_condition_enable'] ) {
			if ( 'false' == self::$cookies ) {
				self::$cookies = 'true';
				//Localize scripts
				wp_localize_script(
					'happy-addons-pro',
					'haDisplayCondition',
					[
						'status' => self::$cookies,
					]
				);
			}

			$id    = $element->get_id();
			$to    = $settings['_ha_condition_to'];
			$lists = $settings['_ha_condition_list'];
			$this->conditions_store( $settings, $id, $lists );
			$check_result = $this->condition_check( $id, $settings['_ha_condition_relation'] );

			//$this->condition_check_result_test( $settings, $id );

			if ( ( 'show' === $to && $check_result ) || ( 'hide' === $to && false === $check_result ) ) {
				$should_render = true;
			} elseif ( ( 'show' === $to && false === $check_result ) || ( 'hide' === $to && $check_result ) ) {
				$should_render = false;
			}
		}

		return $should_render;
	}

	/**
	 * Condition Key Option For Repeater Field
	 * @return array
	 */
	public static function key_options() {
		$options = [];
		foreach ( static::$conditions as $key => $value ) {
			$options[ $value->get_key_name() ] = $value->get_title();
		}
		return $options;
	}

	/**
	 * Add Control Field to Display Condition
	 *
	 * @param $element
	 * @param $args
	 */
	public function add_controls( $element, $args ) {
		$repeater = new Repeater();

		$element->add_control(
			'_ha_condition_enable',
			[
				'label'              => __( 'Display Condition', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => __( 'On', 'happy-addons-pro' ),
				'label_off'          => __( 'Off', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
				'style_transfer' => false
			]
		);

		$element->add_control(
			'_ha_condition_to',
			[
				'label'     => __( 'To', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'show',
				'options'   => [
					'show' => __( 'Show Element', 'happy-addons-pro' ),
					'hide' => __( 'Hide Element', 'happy-addons-pro' ),
				],
				'condition' => [
					'_ha_condition_enable' => 'yes',
				],
				'style_transfer' => false
			]
		);

		$element->add_control(
			'_ha_condition_relation',
			[
				'label'     => __( 'When', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'and',
				'options'   => [
					'and' => __( 'All Conditions Met', 'happy-addons-pro' ),
					'or'  => __( 'Any Condition Met', 'happy-addons-pro' ),
				],
				'condition' => [
					'_ha_condition_enable' => 'yes',
				],
				'style_transfer' => false
			]
		);

		$element->add_control(
			'_ha_time_zone',
			[
				'label'       => __( 'Timezone', 'happy-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'You can change Server', 'happy-addons-pro' ) . sprintf( ' <a href="%1$s" target="_blank">%2$s</a>', admin_url() . '/options-general.php', __( 'Timezone', 'happy-addons-pro' ) ),
				'default'     => 'server',
				'options'     => [
					'server' => __( 'Server Timezone', 'happy-addons-pro' ),
					'local'  => __( 'Local Timezone', 'happy-addons-pro' ),
				],
				'condition'   => [
					'_ha_condition_enable' => 'yes',
				],
				'style_transfer' => false
			]
		);

		//Key
		$repeater->add_control(
			'_ha_condition_key',
			[
				'type'        => Controls_Manager::SELECT,
				'default'     => 'login_status',
				'label_block' => true,
				'options'     => static::key_options(),
				'style_transfer' => false
			]
		);
		//Operator
		$repeater->add_control(
			'_ha_condition_operator',
			[
				'type'        => Controls_Manager::SELECT,
				'default'     => 'is',
				'label_block' => true,
				'options'     => [
					'is'  => __( 'Is', 'happy-addons-pro' ),
					'not' => __( 'Is not', 'happy-addons-pro' ),
				],
				'style_transfer' => false
			]
		);

		$this->add_repeater_controls( $repeater );

		$element->add_control(
			'_ha_condition_list',
			[
				'label'       => __( 'Conditions', 'happy-addons-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'_ha_condition_key'          => 'login_status',
						'_ha_condition_operator'     => 'is',
						'_ha_condition_login_status' => 'login',
					],
				],
				'title_field' => '<# print(_ha_condition_key.replace(/_/i, " ").split(" ").map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(" ")) #>',
				'condition'   => [
					'_ha_condition_enable' => 'yes',
				],
				'style_transfer' => false
			]
		);

	}

	/**
	 * Add Repeater Control Field
	 *
	 * @param $repeater
	 */
	public function add_repeater_controls( $repeater ) {
		$condition = [];
		foreach ( static::$conditions as $key => $value ) {
			$repeater_field_id                   = '_ha_condition_' . $value->get_key_name();
			$condition[ $value->get_key_name() ] = [
				'_ha_condition_key' => $value->get_key_name(),
			];
			$repeater->add_control(
				$repeater_field_id,
				$value->get_repeater_control( $condition[ $value->get_key_name() ] )
			);
		}
	}

	/**
	 * Condition Result Store in $conditions_store property
	 *
	 * @param $section_id
	 * @param array $lists
	 */
	protected function conditions_store( $settings, $section_id, $lists = [] ) {
		if ( ! $lists ) {
			return;
		}

		foreach ( $lists as $key => $list ) {
			$class    = static::$conditions[ $list['_ha_condition_key'] ];
			$operator = $list['_ha_condition_operator'];
			$item_key = '_ha_condition_' . $list['_ha_condition_key'];
			$value    = $list[ $item_key ];
			$id       = $item_key . '_' . $list['_id'];

			$check                                        = $class->compare_value( $settings, $operator, $value );
			$this->conditions_store[ $section_id ][ $id ] = $check;
		}
	}

	/**
	 * Condition Check base on relation status
	 *
	 * @param $section_id
	 * @param $relation
	 * @return bool|void
	 */
	protected function condition_check( $section_id, $relation ) {
		$result = true;
		if ( ! array_key_exists( $section_id, $this->conditions_store ) ) {
			return;
		}

		if ( 'or' === $relation ) {
			//if any condition true
			$result = in_array( true, $this->conditions_store[ $section_id ] ) ? true : false;
		} else {
			//if any condition not true
			$result = in_array( false, $this->conditions_store[ $section_id ] ) ? false : true;
		}

		return $result;
	}

	protected function condition_check_result_test( $settings, $id ) {
		//return;
		$lists = $settings['_ha_condition_list'];
		$this->conditions_store( $settings, $id, $lists );
		$check_result = $this->condition_check( $id, $settings['_ha_condition_relation'] );

		/*var_dump( 'Final Check ', $check_result );
		echo '<br>';*/
	}

	/**
	 * @return Display_Conditions
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();

			self::$_instance->conditions_init();
			self::$_instance->init_actions();
		}

		return self::$_instance;
	}
}
