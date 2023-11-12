<?php
/**
 * UAEL Display Conditions feature.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\DisplayConditions;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Display_Conditions
 *
 * @package UltimateElementor\Modules\DisplayConditions
 */
class Display_Conditions {

	/**
	 * Display Conditions
	 *
	 * Holds all the conditions classes
	 *
	 * @since 1.32.0
	 * @access protected
	 *
	 * @var bool
	 */
	public static $conditions = array();

	/**
	 * Display Conditions
	 *
	 * Holds all the conditions.
	 *
	 * @since 1.32.0
	 * @access protected
	 *
	 * @var bool
	 */
	protected $conditions_result_store = array();

	/**
	 * Contain conditions class name.
	 *
	 * @since 1.32.0
	 * @access public
	 */
	public function condition_init() {
		$conditions_list = array(
			'date',
			'day',
			'role',
			'login_status',
			'browser',
			'operating_system',
			'date_range',
			'page',
			'post',
			'static_page',
			'time_span',
			'visitor_type',
			'request_parameter',
			'advanced_date',
			'geolocation',
			'acf_text',
		);

		foreach ( $conditions_list as $condition_name ) {

			$file_name = str_replace( '_', '-', strtolower( $condition_name ) );

			if ( file_exists( UAEL_MODULES_URL . 'display-conditions/conditions/' . $file_name . '.php' ) ) {
				include_once UAEL_MODULES_URL . 'display-conditions/conditions/' . $file_name . '.php';
			}

			$class_name = str_replace( '-', ' ', $condition_name );
			$class_name = str_replace( ' ', '', ucwords( $class_name ) );
			$class_name = __NAMESPACE__ . '\\Conditions\\' . $class_name;

			if ( class_exists( $class_name ) ) {
				static::$conditions[ $condition_name ] = new $class_name();
			}
		}
	}

	/**
	 * Set render function to action filter
	 *
	 * @since 1.32.0
	 */
	public function init_actions() {
		add_filter( 'elementor/frontend/section/should_render', array( $this, 'render_content' ), 10, 2 );
		add_filter( 'elementor/frontend/column/should_render', array( $this, 'render_content' ), 10, 2 );
		add_filter( 'elementor/frontend/widget/should_render', array( $this, 'render_content' ), 10, 2 );
	}

	/**
	 * Render Content base on condition result
	 *
	 * @since 1.32.0
	 * @param bool  $should_render return boolean value.
	 * @param array $element return controls.
	 * @return bool
	 */
	public function render_content( $should_render, $element ) {
		$settings = $element->get_settings();

		if ( 'yes' === $settings['display_condition_enable'] ) {
			$id    = $element->get_id();
			$to    = $settings['display_condition_to'];
			$lists = $settings['display_condition_list'];
			$this->conditions_result_store( $settings, $id, $lists );
			$check_result = $this->check_condition( $id, $settings['display_condition_relation'] );

			if ( ( 'show' === $to && $check_result ) || ( 'hide' === $to && false === $check_result ) ) {
				$should_render = true;
			} elseif ( ( 'show' === $to && false === $check_result ) || ( 'hide' === $to && $check_result ) ) {
				$should_render = false;
			}
		}

		return $should_render;
	}

	/**
	 * Key option for Repeater field.
	 *
	 * @since 1.32.0
	 */
	public static function conditions_keys() {
		$options = array();
		foreach ( static::$conditions as $key => $value ) {
			$options[ $value->get_key_name() ] = $value->get_title();
		}

		return $options;
	}

	/**
	 * Add Control Field to Display Condition
	 *
	 * @since 1.32.0
	 * @param array $element return array element.
	 * @param array $args return arguments.
	 */
	public function add_controls( $element, $args ) {
		$repeater = new Repeater();

		$element->add_control(
			'display_condition_enable',
			array(
				'label'              => __( 'Enable Conditions', 'uael' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => __( 'On', 'uael' ),
				'label_off'          => __( 'Off', 'uael' ),
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'display_condition_to',
			array(
				'label'     => __( 'To', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'show',
				'options'   => array(
					'show' => __( 'Show Element', 'uael' ),
					'hide' => __( 'Hide Element', 'uael' ),
				),
				'condition' => array(
					'display_condition_enable' => 'yes',
				),
			)
		);

		$element->add_control(
			'display_condition_relation',
			array(
				'label'     => __( 'When', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'and',
				'options'   => array(
					'and' => __( 'All Conditions Met', 'uael' ),
					'or'  => __( 'Any Condition Met', 'uael' ),
				),
				'condition' => array(
					'display_condition_enable' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'display_condition_key',
			array(
				'type'        => Controls_Manager::SELECT,
				'default'     => 'role',
				'label_block' => true,
				'options'     => static::conditions_keys(),
			)
		);

		$repeater->add_control(
			'display_condition_operator',
			array(
				'type'        => Controls_Manager::SELECT,
				'default'     => 'is',
				'label_block' => true,
				'options'     => array(
					'is'  => __( 'Is', 'uael' ),
					'not' => __( 'Is not', 'uael' ),
				),
				'condition'   => array(
					'display_condition_key!' => 'advanced_date',
				),
			)
		);

		$repeater->add_control(
			'display_condition_operator_advanced_date',
			array(
				'type'        => Controls_Manager::SELECT,
				'default'     => 'less',
				'label_block' => true,
				'options'     => array(
					'less'               => __( 'Is Less than', 'uael' ),
					'greater'            => __( 'Is Greater than', 'uael' ),
					'less_than_equal'    => __( 'Is Less than equal to', 'uael' ),
					'greater_than_equal' => __( 'Is Greater than equal to', 'uael' ),
				),
				'condition'   => array(
					'display_condition_key' => 'advanced_date',
				),
			)
		);

		$this->add_repeater_controls( $repeater );

		$element->add_control(
			'display_condition_list',
			array(
				'label'       => __( 'Conditions', 'uael' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'display_condition_key'          => 'role',
						'display_condition_operator'     => 'is',
						'display_condition_login_status' => 'subscriber',
					),
				),
				'title_field' => '<#
						if ( display_condition_key == "acf_text" ) {
							print("ACF Field");
						} else {
							print( display_condition_key.replace(/_/i, " ").split(" ").map( word => word.charAt(0).toUpperCase() + word.slice(1) ).join(" ") );
						}
					#>',
				'condition'   => array(
					'display_condition_enable' => 'yes',
				),
			)
		);

		$element->add_control(
			'display_condition_time_zone',
			array(
				'label'       => __( 'Timezone', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				// translators: 1: admin link 2: Timezone.
				'description' => __( 'You can change Server', 'uael' ) . sprintf( ' <a href="%1$s" target="_blank">%2$s</a>', admin_url() . '/options-general.php', __( 'Timezone', 'uael' ) ) . sprintf( __( ' It will fetch the time as per selected option. %1$s Learn more %2$s ', 'uael' ), '<a href=' . UAEL_DOMAIN . '/docs/display-conditions/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
				'default'     => 'local',
				'options'     => array(
					'server' => __( 'Server Timezone', 'uael' ),
					'local'  => __( 'Local Timezone', 'uael' ),
				),
				'condition'   => array(
					'display_condition_enable' => 'yes',
				),
			)
		);

		$element->add_control(
			'display-note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Note: Display conditions feature will work on the frontend.', 'uael' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition'       => array(
					'display_condition_enable' => 'yes',
				),
			)
		);
	}

	/**
	 * Add Repeater field.
	 *
	 * @since 1.32.0
	 * @param array $repeater repeater field.
	 */
	public function add_repeater_controls( $repeater ) {
		$condition = array();

		foreach ( static::$conditions as $key => $value ) {
			$key_name = $value->get_key_name();

			$repeater_field_id      = 'display_condition_' . $key_name;
			$condition[ $key_name ] = array(
				'display_condition_key' => $key_name,
			);

			if ( 'display_condition_time_span' === $repeater_field_id ) {
				$repeater->add_control(
					'display_condition_time_span_start',
					$value->get_repeater_control( $condition[ $key_name ] )
				);

				$repeater->add_control(
					'display_condition_time_span_end',
					$value->get_due_control( $condition[ $key_name ] )
				);

			} elseif ( 'display_condition_acf_text' === $repeater_field_id ) {
				$repeater->add_control(
					'display_condition_acf_text_key',
					$value->get_acf_field( $condition[ $key_name ] )
				);
				$repeater->add_control(
					'display_condition_acf_text_value',
					$value->get_repeater_control( $condition[ $key_name ] )
				);

			} elseif ( 'display_condition_request_parameter' === $repeater_field_id ) {
				$repeater->add_control(
					'display_condition_request_parameter_key',
					$value->get_repeater_control( $condition[ $key_name ] )
				);

				$repeater->add_control(
					'display_condition_request_parameter_value',
					$value->get_value_control( $condition[ $key_name ] )
				);
			} else {
				$repeater->add_control(
					$repeater_field_id,
					$value->get_repeater_control( $condition[ $key_name ] )
				);
			}
		}
	}


	/**
	 * Condition Result Store in $conditions_result_store property.
	 *
	 * @since 1.32.0
	 * @param array  $settings return settings.
	 * @param Number $section_id return section ID.
	 * @param array  $lists return lists of controls.
	 */
	protected function conditions_result_store( $settings, $section_id, $lists = array() ) {

		if ( ! $lists ) {
			return;
		}

		foreach ( $lists as $key => $list ) {
			$class = static::$conditions[ $list['display_condition_key'] ];

			if ( 'advanced_date' === $list['display_condition_key'] ) {
				$operator = $list['display_condition_operator_advanced_date'];
			} else {
				$operator = $list['display_condition_operator'];
			}

			$item_key = 'display_condition_' . $list['display_condition_key'];
			$value    = isset( $list[ $item_key ] ) ? $list[ $item_key ] : '';
			$id       = $item_key . '_' . $list['_id'];

			if ( 'time_span' === $list['display_condition_key'] ) {

				$start = 'display_condition_' . $list['display_condition_key'] . '_start';
				$end   = 'display_condition_' . $list['display_condition_key'] . '_end';

				$key_val_start = $list[ $start ];
				$key_val_end   = $list[ $end ];
				$check         = $class->time_compare_value( $settings, $operator, $key_val_start, $key_val_end );

			} elseif ( 'request_parameter' === $list['display_condition_key'] ) {

				$key        = 'display_condition_' . $list['display_condition_key'] . '_key';
				$value      = 'display_condition_' . $list['display_condition_key'] . '_value';
				$main_key   = $list[ $key ];
				$main_value = $list[ $value ];
				$check      = $class->compare_request_param( $settings, $operator, $main_key, $main_value );
			} elseif ( 'acf_text' === $list['display_condition_key'] ) {
				$key        = 'display_condition_' . $list['display_condition_key'] . '_key';
				$value      = 'display_condition_' . $list['display_condition_key'] . '_value';
				$main_key   = $list[ $key ];
				$main_value = $list[ $value ];
				$check      = $class->acf_compare_value( $settings, $operator, $main_key, $main_value );
			} else {
				$check = $class->compare_value( $settings, $operator, $value );
			}

			$this->conditions_result_store[ $section_id ][ $id ] = $check;
		}
	}

	/**
	 * Condition Check base on relation status
	 *
	 * @since 1.32.0
	 * @param Number $section_id return section ID.
	 * @param String $relation relation operator.
	 * @return bool|void
	 */
	protected function check_condition( $section_id, $relation ) {
		$result = true;
		if ( ! array_key_exists( $section_id, $this->conditions_result_store ) ) {
			return;
		}

		if ( 'or' === $relation ) {
			// if any condition true.
			$result = in_array( true, $this->conditions_result_store[ $section_id ], true ) ? true : false;
		} else {
			// if any condition not true.
			$result = in_array( false, $this->conditions_result_store[ $section_id ], true ) ? false : true;
		}

		return $result;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.32.0
	 */
	public function __construct() {

		$this->condition_init();
		$this->init_actions();
	}
}
