<?php

/**
 * Conditional Rendering
 *
 * @author     P-THEMES
 * @package    Porto
 * @subpackage Core
 * @since      2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Automattic\Jetpack\Device_Detection;

/**
 * Porto Conditional Rendering Class
 *
 * @since 2.3.0
 */
class Porto_Conditional_Rendering {
	/**
	 * The Instance Object.
	 *
	 * @since 2.3.0
	 */
	public static $instance;

	/**
	 * The device object
	 *
	 * @since 2.3.0
	 */
	public $device;

	/**
	 * Get Post Types
	 * 
	 * @since 2.6.0
	 */
	public static $post_types;

	/**
	 * Get Shop page id
	 * 
	 * @since 2.6.0
	 */
	public static $shop_id = -2;

	/**
	 * Get the instance.
	 *
	 * @since 2.3.0
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * The Constructor.
	 *
	 * @since 2.3.0
	 */
	public function __construct() {

		if ( class_exists( 'WooCommerce' ) ) {
			self::$shop_id  = (int) get_option( 'woocommerce_shop_page_id' );
		}
		add_action( 'admin_init', array( $this, 'get_post_type' ) );
		add_action( 'elementor/element/section/section_section_additional/after_section_end', array( $this, 'add_condition_system' ), 10, 2 );
		add_action( 'elementor/element/column/section_column_additional/after_section_end', array( $this, 'add_condition_system' ), 10, 2 );

		add_action( 'elementor/element/container/section_layout_items/after_section_end', array( $this, 'add_condition_system' ), 10, 2 );
		add_filter( 'elementor/frontend/container/should_render', array( $this, 'should_render' ), 10, 2 );

		add_filter( 'elementor/frontend/section/should_render', array( $this, 'should_render' ), 10, 2 );
		add_filter( 'elementor/frontend/column/should_render', array( $this, 'should_render' ), 10, 2 );

		add_action( 'vc_after_init', array( $this, 'add_wpb_condition' ), 25 );
		add_filter( 'porto_wpb_should_render', array( $this, 'wpb_should_render' ), 10, 2 );
	}

	/**
	 * Get post types.
	 * 
	 * @since 2.6.0
	 */
	public function get_post_type() {
		self::$post_types          = get_post_types(
			array(
				'public'            => true,
				'show_in_nav_menus' => true,
			),
			'objects',
			'and'
		);
		$disabled_post_types = array( 'attachment' );
		foreach ( $disabled_post_types as $disabled ) {
			unset( self::$post_types[ $disabled ] );
		}
		foreach ( self::$post_types as $key => $p_type ) {
			self::$post_types[ $key ] = esc_html( $p_type->label );
		}
	}

	/**
	 * Add WPBakery Options
	 *
	 * @since 2.4.0
	 */
	public function add_wpb_condition() {
		if ( function_exists( 'vc_map' ) ) {
			$add_params = array(
				array(
					'param_name'  => 'condition_a',
					'heading'     => esc_html__( 'Condition A', 'porto-functionality' ),
					'description' => esc_html__( 'Select condition type.', 'porto-functionality' ),
					'type'        => 'dropdown',
					'group'       => esc_html__( 'Porto Conditional System', 'porto-functionality' ),
					'value'       => array(
						'' => '',
						esc_html__( 'Device', 'porto-functionality' ) => 'device',
						esc_html__( 'Login Status', 'porto-functionality' ) => 'login_status',
						esc_html__( 'User Role', 'porto-functionality' ) => 'user_role',
						esc_html__( 'Post / Page', 'porto-functionality' ) => 'post_page',
					),
					'std'         => '',
				),
				array(
					'param_name' => 'comparative_operator',
					'heading'    => esc_html__( 'Comparative Operator', 'porto-functionality' ),
					'type'       => 'dropdown',
					'group'      => esc_html__( 'Porto Conditional System', 'porto-functionality' ),
					'value'      => array(
						'' => '',
						esc_html__( '==', 'porto-functionality' ) => 'equal',
						esc_html__( '!=', 'porto-functionality' ) => 'not_equal',
					),
					'std'        => '',
				),
				array(
					'param_name' => 'value_device',
					'heading'    => esc_html__( 'Device', 'porto-functionality' ),
					'type'       => 'dropdown',
					'group'      => esc_html__( 'Porto Conditional System', 'porto-functionality' ),
					'value'      => array(
						'' => '',
						__( 'Desktop', 'porto-functionality' ) => 'desktop',
						__( 'Tablet & Mobile', 'porto-functionality' ) => 'tablet_mobile',
						__( 'Tablet', 'porto-functionality' ) => 'tablet',
						__( 'Mobile', 'porto-functionality' ) => 'mobile',
					),
					'dependency' => array(
						'element' => 'condition_a',
						'value'   => 'device',
					),
					'std'        => '',
				),
				array(
					'param_name' => 'value_login',
					'heading'    => esc_html__( 'Status', 'porto-functionality' ),
					'type'       => 'dropdown',
					'group'      => esc_html__( 'Porto Conditional System', 'porto-functionality' ),
					'value'      => array(
						'' => '',
						esc_html__( 'Logged In', 'porto-functionality' ) => 'login',
						esc_html__( 'Logged Out', 'porto-functionality' ) => 'logout',
					),
					'dependency' => array(
						'element' => 'condition_a',
						'value'   => 'login_status',
					),
					'std'        => '',
				),
				array(
					'param_name' => 'value_role',
					'heading'    => esc_html__( 'Role', 'porto-functionality' ),
					'type'       => 'dropdown',
					'group'      => esc_html__( 'Porto Conditional System', 'porto-functionality' ),
					'value'      => array_flip( $this->get_roles() ),
					'dependency' => array(
						'element' => 'condition_a',
						'value'   => 'user_role',
					),
					'std'        => '',
				),
				array(
					'param_name'  => 'value_page_ids',
					'type'        => 'textfield',
					'heading'     => __( 'Page/Post', 'porto-functionality' ),
					'description' => __( 'Please input comma separated post, page, product or portfolio ids.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'condition_a',
						'value'   => 'post_page',
					),
				),
				array(
					'param_name'  => 'condition_operator',
					'heading'     => esc_html__( 'Operator', 'porto-functionality' ),
					'description' => esc_html__( 'The selected value is used to operate on the conditions below.', 'porto-functionality' ),
					'type'        => 'dropdown',
					'group'       => esc_html__( 'Porto Conditional System', 'porto-functionality' ),
					'value'       => array(
						'' => '',
						esc_html__( 'And', 'porto-functionality' ) => 'and',
						esc_html__( 'Or', 'porto-functionality' ) => 'or',
					),
					'std'         => '',
				),
			);
			vc_add_param(
				'vc_section',
				array(
					'type'       => 'param_group',
					'param_name' => 'conditional_render',
					'heading'    => esc_html__( 'Conditional Render', 'porto-functionality' ),
					'params'     => $add_params,
					'group'      => esc_html__( 'Porto Conditional System', 'porto-functionality' ),
				)
			);
			vc_add_param(
				'vc_row',
				array(
					'type'       => 'param_group',
					'param_name' => 'conditional_render',
					'heading'    => esc_html__( 'Conditional Render', 'porto-functionality' ),
					'params'     => $add_params,
					'group'      => esc_html__( 'Porto Conditional System', 'porto-functionality' ),
				)
			);
			vc_add_param(
				'vc_column',
				array(
					'type'       => 'param_group',
					'param_name' => 'conditional_render',
					'heading'    => esc_html__( 'Conditional Render', 'porto-functionality' ),
					'params'     => $add_params,
					'group'      => esc_html__( 'Porto Conditional System', 'porto-functionality' ),
				)
			);
		}
	}

	/**
	 * Add Control
	 *
	 * @since 2.3.0
	 */
	public function add_condition_system( $self ) {
		$self->start_controls_section(
			'section_conditional',
			array(
				'label' => esc_html__( 'Porto Conditional System', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_LAYOUT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'condition_a',
			array(
				'label'       => esc_html__( 'Condition A', 'porto-functionality' ),
				'description' => esc_html__( 'Select condition type.', 'porto-functionality' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'device'       => esc_html__( 'Device', 'porto-functionality' ),
					'login_status' => esc_html__( 'Login Status', 'porto-functionality' ),
					'user_role'    => esc_html__( 'User Role', 'porto-functionality' ),
					'post_page'    => esc_html__( 'Post & Page', 'porto-functionality' ),
				),
			)
		);
		$repeater->add_control(
			'comparative_operator',
			array(
				'label'   => esc_html__( 'Comparative Operator', 'porto-functionality' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'equal'     => esc_html__( '==', 'porto-functionality' ),
					'not_equal' => esc_html__( '!=', 'porto-functionality' ),
				),
			)
		);
		$repeater->add_control(
			'value_device',
			array(
				'label'     => esc_html__( 'Device', 'porto-functionality' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'desktop'       => esc_html__( 'Desktop', 'porto-functionality' ),
					'tablet_mobile' => esc_html__( 'Tablet & Mobile', 'porto-functionality' ),
					'tablet'        => esc_html__( 'Tablet', 'porto-functionality' ),
					'mobile'        => esc_html__( 'Mobile', 'porto-functionality' ),
				),
				'condition' => array(
					'condition_a' => 'device',
				),
			)
		);
		$repeater->add_control(
			'value_login',
			array(
				'label'     => esc_html__( 'Status', 'porto-functionality' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'login'  => esc_html__( 'Logged In', 'porto-functionality' ),
					'logout' => esc_html__( 'Logged Out', 'porto-functionality' ),
				),
				'condition' => array(
					'condition_a' => 'login_status',
				),
			)
		);
		$repeater->add_control(
			'value_role',
			array(
				'label'     => esc_html__( 'Role', 'porto-functionality' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->get_roles(),
				'condition' => array(
					'condition_a' => 'user_role',
				),
			)
		);

		$repeater->add_control(
			'post_type',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Post Type', 'porto-functionality' ),
				'options'     => self::$post_types,
				'condition'   => array(
					'condition_a' => 'post_page',
				),
			)
		);

		$repeater->add_control(
			'value_page_ids',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Page/Post', 'porto-functionality' ),
				'options'     => '%post_type%_particularpage',
				'label_block' => true,
				'multiple'    => true,
				'condition'   => array(
					'condition_a' => 'post_page',
				),
			)
		);

		$repeater->add_control(
			'condition_operator',
			array(
				'label'       => esc_html__( 'Operator', 'porto-functionality' ),
				'description' => esc_html__( 'The selected value is used to operate on the conditions below.', 'porto-functionality' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'and' => esc_html__( 'And', 'porto-functionality' ),
					'or'  => esc_html__( 'Or', 'porto-functionality' ),
				),
			)
		);

		$self->add_control(
			'description_conditional_render',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Only when these conditions are matched, will this section be rendered.', 'porto-functionality' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$self->add_control(
			'conditional_render',
			array(
				'label'         => esc_html__( 'Conditional Render', 'porto-functionality' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'prevent_empty' => false,
				'title_field'   => '{{{ condition_a }}}',
			)
		);

		$self->end_controls_section();
	}

	/**
	 * Returns the roles.
	 *
	 * @since 2.3.0
	 */
	public function get_roles() {
		global $wp_roles;
		$roles = array();
		if ( is_array( $wp_roles->roles ) ) {
			foreach ( $wp_roles->roles as $key => $role ) {
				$roles[ $key ] = $role['name'];
			}
		}
		return $roles;
	}

	/**
	 * Get the device
	 *
	 * @since 2.3.0
	 */
	public function get_device( $is_tablet_mobile = false ) {
		if ( ! class_exists( 'Device_Detection' ) && ! defined( 'JETPACK__VERSION' ) ) {
			require_once 'jetpack-device-detection/class-device-detection.php';
			require_once 'jetpack-device-detection/class-user-agent-info.php';
		}
		$critical_mobile = ! empty( $_REQUEST['mobile_url'] );
		if ( ( $critical_mobile || Device_Detection::is_phone() ) && ! $is_tablet_mobile ) {
			return 'mobile';
		} elseif ( Device_Detection::is_tablet() && ! $is_tablet_mobile ) {
			return 'tablet';
		} elseif ( ! wp_is_mobile() ) {
			return 'desktop';
		} elseif ( wp_is_mobile() ) {
			return 'tablet_mobile';
		}
		return '';
	}

	/**
	 * Check if the element should be rendered or not in WPBakery.
	 *
	 * @since 2.4.0
	 */
	public function wpb_should_render( $should_render, $conditional_render ) {
		global $pagenow;
		if ( function_exists( 'vc_is_inline' ) && ! ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) || vc_is_inline() ) && ! $this->is_render( array( 'conditional_render' => $conditional_render ) ) ) {
			return false;
		}
		return $should_render;
	}

	/**
	 * Check if the element should be rendered or not.
	 *
	 * @since 2.3.0
	 */
	public function should_render( $should_render, $self ) {
		$atts = $self->get_settings_for_display();
		if ( function_exists( 'porto_is_elementor_preview' ) && ! porto_is_elementor_preview() && ! $this->is_render( $atts ) ) {
			return false;
		}
		return $should_render;
	}

	/**
	 * Is rendering?
	 *
	 * @since 2.3.0
	 */
	public function is_render( $atts ) {
		
		if ( ! empty( $atts['conditional_render'] ) && is_array( $atts['conditional_render'] ) ) {
			foreach ( $atts['conditional_render'] as $condition ) {
				if ( ! empty( $condition['condition_a'] ) ) {
					switch ( $condition['condition_a'] ) {
						case 'device':
							if ( ! empty( $condition['value_device'] ) ) {
								$right = $condition['value_device'];
							}
							$left = $this->get_device( isset( $right ) && 'tablet_mobile' == $right ? true : false );
							break;
						case 'login_status':
							$left = is_user_logged_in();
							if ( ! empty( $condition['value_login'] ) ) {
								$right = ( 'login' == $condition['value_login'] ? true : false );
							}
							break;
						case 'user_role':
							$left = wp_get_current_user();
							$left = ( 0 !== $left->ID ) ? $left->roles : array();
							if ( ! empty( $condition['value_role'] ) ) {
								$right = $condition['value_role'];
							}
							break;
						case 'post_page':
							if ( ! empty( $condition['value_page_ids'] ) ) {
								$left = is_array( $condition['value_page_ids'] ) ? $condition['value_page_ids'] : explode( ',', $condition['value_page_ids'] );
							}
							$right = get_the_ID();
							if ( is_home() || is_archive() ) {
								$right = get_queried_object_id();
							}
							if ( class_exists( 'WooCommerce' ) && is_shop() ) {
								$right = self::$shop_id;
							}
							if ( is_category() || is_tax() || is_tag() ) {
								$right = -1;
							}
							break;
					}
					if ( ! empty( $condition['comparative_operator'] ) ) {
						$operator = $condition['comparative_operator'];
					}
					if ( ! empty( $condition['condition_operator'] ) ) {
						$condition_operator = $condition['condition_operator'];
					}
					if ( isset( $left ) && isset( $right ) && isset( $operator ) ) {
						if ( 'equal' == $operator ) {
							if ( is_array( $left ) ) {
								$res = in_array( $right, $left );
							} else {
								$res = ( $left == $right );
							}
						} else {
							if ( is_array( $left ) ) {
								$res = ! in_array( $right, $left );
							} else {
								$res = ( $left != $right );
							}
						}
						if ( isset( $render ) ) {
							if ( isset( $prev_operator ) && 'or' == $prev_operator ) {
								$render = $render || $res;
							} else {
								$render = $render && $res;
							}
						} else {
							$render = $res;
						}
						if ( isset( $condition_operator ) ) {
							$prev_operator = $condition_operator;
						} else { // not select
							$prev_operator = 'and';
						}
					}
					unset( $left, $right, $operator );
				}
			}
		}

		return isset( $render ) ? $render : true;
	}
}

Porto_Conditional_Rendering::get_instance();
