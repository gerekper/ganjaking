<?php

namespace Essential_Addons_Elementor\Pro\Extensions;

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Repeater;
use \Essential_Addons_Elementor\Classes\Helper as ControlsHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Conditional_Display {

	/**
	 * Initialize hooks
	 */
	public function __construct() {
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'register_controls' ] );
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'register_controls' ] );
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'register_controls' ] );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'register_controls' ] );
		add_filter( 'elementor/frontend/widget/should_render', [ $this, 'content_render' ], 10, 2 );
		add_filter( 'elementor/frontend/column/should_render', [ $this, 'content_render' ], 10, 2 );
		add_filter( 'elementor/frontend/section/should_render', [ $this, 'content_render' ], 10, 2 );
		add_filter( 'elementor/frontend/container/should_render', [ $this, 'content_render' ], 10, 2 );
	}

	public function register_controls( $element ) {
		$element->start_controls_section(
			'eael_conditional_logic_section',
			[
				'label' => __( '<i class="eaicon-logo"></i> Conditional Display', 'essential-addons-elementor' ),
				'tab'   => Controls_Manager::TAB_ADVANCED
			]
		);

		$element->add_control(
			'eael_cl_notice',
			[
				'raw'             => esc_html__( 'Conditional Display will take effect only on preview or live page, and not while editing in Elementor.', 'essential-addons-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition'       => [
					'eael_cl_enable' => 'yes'
				]
			]
		);

		$element->add_control(
			'eael_cl_enable',
			[
				'label'          => __( 'Enable Conditional Display', 'essential-addons-elementor' ),
				'type'           => Controls_Manager::SWITCHER,
				'default'        => '',
				'label_on'       => __( 'Yes', 'essential-addons-elementor' ),
				'label_off'      => __( 'No', 'essential-addons-elementor' ),
				'return_value'   => 'yes',
				'style_transfer' => false
			]
		);

		$element->add_control(
			'eael_cl_visibility_action',
			[
				'label'          => __( 'Visibility Action', 'essential-addons-elementor' ),
				'type'           => Controls_Manager::CHOOSE,
				'options'        => [
					'show'            => [
						'title' => esc_html__( 'Show', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-eye-solid',
					],
					'hide'            => [
						'title' => esc_html__( 'Hide', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-eye-slash-solid',
					],
					'forcefully_hide' => [
						'title' => esc_html__( 'Hide Without Condition', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-ban-solid',
					],
				],
				'default'        => 'show',
				'toggle'         => false,
				'condition'      => [
					'eael_cl_enable' => 'yes',
				],
				'style_transfer' => false
			]
		);

		$element->add_control(
			'eael_cl_action_apply_if',
			[
				'label'          => __( 'Action Applicable if', 'essential-addons-elementor' ),
				'type'           => Controls_Manager::CHOOSE,
				'options'        => [
					'all' => [
						'title' => esc_html__( 'True All Logic', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-dice-six-solid',
					],
					'any' => [
						'title' => esc_html__( 'True Any Logic', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-dice-one-solid',
					],
				],
				'default'        => 'all',
				'toggle'         => false,
				'condition'      => [
					'eael_cl_enable'             => 'yes',
					'eael_cl_visibility_action!' => 'forcefully_hide',
				],
				'style_transfer' => false
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'logic_type',
			[
				'label'   => __( 'Type', 'essential-addons-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'login_status',
				'options' => [
					'login_status' => __( 'User Status', 'essential-addons-elementor' ),
					'post_type'    => __( 'Post Type', 'essential-addons-elementor' ),
					'browser'      => __( 'Browser', 'essential-addons-elementor' ),
					'date_time'    => __( 'Date & Time', 'essential-addons-elementor' ),
					'recurring_day' => __( 'Recurring Day', 'essential-addons-elementor' ),
					'dynamic'      => __( 'Dynamic Field', 'essential-addons-elementor' ),
					'query_string' => __( 'Query String', 'essential-addons-elementor' ),
				],
			]
		);

		$repeater->add_control(
			'dynamic_field',
			[
				'label'       => esc_html__( 'Dynamic Field', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'logic_type' => 'dynamic'
				],
				'ai' => [
					'active' => false,
				],
				'description' => esc_html__( 'If Dynamic Field has multiple values please use pipe ( | ) as separator.  Please remove Before and After field texts from Advanced tab.', 'essential-addons-elementor' )
			]
		);

		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$repeater->add_control(
				'logic_operator_dynamic',
				[
					'label'      => __( 'Logic Operator', 'essential-addons-elementor' ),
					'show_label' => false,
					'type'       => Controls_Manager::CHOOSE,
					'options'    => [
						'between'     => [
							'title' => esc_html__( 'Include', 'essential-addons-elementor' ),
							'icon'  => 'eaicon-check-solid',
						],
						'not_between' => [
							'title' => esc_html__( 'Exclude', 'essential-addons-elementor' ),
							'icon'  => 'eaicon-xmark-solid',
						],
					],
					'default'    => 'between',
					'toggle'     => false,
					'condition'  => [
						'logic_type' => 'dynamic',
					]
				]
			);
		}

		$repeater->add_control(
			'login_status_operand',
			[
				'label'     => __( 'Login Status', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'logged_in'     => [
						'title' => esc_html__( 'Logged In', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-user-solid',
					],
					'not_logged_in' => [
						'title' => esc_html__( 'Not Logged In', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-user-slash-solid',
					],
				],
				'default'   => 'logged_in',
				'toggle'    => false,
				'condition' => [
					'logic_type' => 'login_status',
				]
			]
		);

		$repeater->add_control(
			'user_and_role',
			[
				'label'     => __( 'Select User Type', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'user_role' => [
						'title' => esc_html__( 'User Role', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-users-solid',
					],
					'user'      => [
						'title' => esc_html__( 'User', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-user-plus-solid',
					],
				],
				'default'   => '',
				'condition' => [
					'logic_type'           => 'login_status',
					'login_status_operand' => 'logged_in',
				]
			]
		);

		$repeater->add_control(
			'logic_operator_between',
			[
				'label'      => __( 'Logic Operator', 'essential-addons-elementor' ),
				'show_label' => false,
				'type'       => Controls_Manager::CHOOSE,
				'options'    => [
					'between'     => [
						'title' => esc_html__( 'Include', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-check-solid',
					],
					'not_between' => [
						'title' => esc_html__( 'Exclude', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-xmark-solid',
					],
				],
				'default'    => 'between',
				'toggle'     => false,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'browser',
						],
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'post_type',
						],
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'query_string',
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'logic_type',
									'operator' => '===',
									'value'    => 'login_status',
								],
								[
									'name'     => 'login_status_operand',
									'operator' => '===',
									'value'    => 'logged_in',
								],
								[
									'name'     => 'user_and_role',
									'operator' => '!==',
									'value'    => '',
								],
							],
						]
					],
				]
			]
		);

		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$repeater->add_control(
				'dynamic_operand',
				[
					'label'       => esc_html__( 'Value', 'essential-addons-elementor' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'default'     => '',
					'condition'   => [
						'logic_type' => 'dynamic'
					],
					'ai' => [
						'active' => false,
					],
				]
			);

			$repeater->add_control(
				'eael_cl_dynamic_notice',
				[
					'raw'             => __( 'Separate multiple value with the | (pipe) character. (e.g. <strong>value 1 | value 2</strong>)', 'essential-addons-elementor' ),
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-descriptor',
					'condition'       => [
						'logic_type' => 'dynamic',
					]
				]
			);
		}

		$roles = $this->get_editable_roles();

		$repeater->add_control(
			'user_role_operand_multi',
			[
				'label'       => __( 'Select User Roles', 'essential-addons-elementor' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $roles,
				'default'     => [],
				'condition'   => [
					'logic_type'           => 'login_status',
					'login_status_operand' => 'logged_in',
					'user_and_role'        => 'user_role'
				]
			]
		);

		$repeater->add_control(
			'user_operand',
			[
				'label'       => esc_html__( 'Select Users', 'essential-addons-elementor' ),
				'type'        => 'eael-select2',
				'source_name' => 'user',
				'source_type' => 'all',
				'label_block' => true,
				'multiple'    => true,
				'condition'   => [
					'logic_type'           => 'login_status',
					'login_status_operand' => 'logged_in',
					'user_and_role'        => 'user'
				]
			]
		);

		$_post_types = ControlsHelper::get_post_types();
		$post_types  = array_merge( [ '' => 'All' ], $_post_types );

		$repeater->add_control(
			'post_type_operand',
			[
				'label'       => esc_html__( 'Select Post Types', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => $post_types,
				'default'     => key( $post_types ),
				'condition'   => [
					'logic_type' => 'post_type',
				]
			]
		);

		$repeater->add_control(
			'post_operand',
			[
				'label'       => esc_html__( 'Select Any Post', 'essential-addons-elementor' ),
				'type'        => 'eael-select2',
				'source_name' => 'post_type',
				'source_type' => 'any',
				'label_block' => true,
				'multiple'    => true,
				'condition'   => [
					'logic_type'        => 'post_type',
					'post_type_operand' => ''
				]
			]
		);

		foreach ( $_post_types as $post_type_slug => $post_type_name ) {
			$repeater->add_control(
				"post_operand_{$post_type_slug}",
				[
					'label'       => esc_html__( 'Select ', 'essential-addons-elementor' ) . $post_type_name,
					'type'        => 'eael-select2',
					'source_name' => 'post_type',
					'source_type' => $post_type_slug,
					'label_block' => true,
					'multiple'    => true,
					'condition'   => [
						'logic_type'        => 'post_type',
						'post_type_operand' => $post_type_slug
					]
				]
			);
		}

		$repeater->add_control(
			'browser_operand',
			[
				'label'       => __( 'Select Browser', 'essential-addons-elementor' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->get_browser_list(),
				'default'     => key( $this->get_browser_list() ),
				'condition'   => [
					'logic_type' => 'browser',
				]
			]
		);

		$repeater->add_control(
			'date_time_logic',
			[
				'label'      => __( 'Date and time', 'essential-addons-elementor' ),
				'show_label' => false,
				'type'       => Controls_Manager::CHOOSE,
				'options'    => [
					'equal'       => [
						'title' => esc_html__( 'Is', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-equals-solid',
					],
					'not_equal'   => [
						'title' => esc_html__( 'Is Not', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-not-equal-solid',
					],
					'between'     => [
						'title' => esc_html__( 'Between', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-check-solid',
					],
					'not_between' => [
						'title' => esc_html__( 'Not Between', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-xmark-solid',
					],
				],
				'default'    => 'equal',
				'toggle'     => false,
				'condition'  => [
					'logic_type' => 'date_time',
				]
			]
		);

		$repeater->add_control(
			'single_date',
			[
				'label'          => esc_html__( 'Date', 'essential-addons-elementor' ),
				'label_block'    => false,
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'enableTime' => false,
					'altInput'   => true,
					'altFormat'  => 'M j, Y',
					'dateFormat' => 'Y-m-d'
				],
				'conditions'     => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'date_time',
						],
						[
							'name'     => 'date_time_logic',
							'operator' => '!==',
							'value'    => 'between',
						],
						[
							'name'     => 'date_time_logic',
							'operator' => '!==',
							'value'    => 'not_between',
						],
					],
				]
			]
		);

		$repeater->add_control(
			'from_date',
			[
				'label'          => esc_html__( 'From', 'essential-addons-elementor' ),
				'label_block'    => false,
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'altInput'   => true,
					'altFormat'  => 'M j, Y h:i K',
					'dateFormat' => 'Y-m-d H:i:S'
				],
				'conditions'     => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'date_time',
						],
						[
							'name'     => 'date_time_logic',
							'operator' => '!==',
							'value'    => 'equal',
						],
						[
							'name'     => 'date_time_logic',
							'operator' => '!==',
							'value'    => 'not_equal',
						],
					],
				]
			]
		);

		$repeater->add_control(
			'to_date',
			[
				'label'          => esc_html__( 'To', 'essential-addons-elementor' ),
				'label_block'    => false,
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'altInput'   => true,
					'altFormat'  => 'M j, Y h:i K',
					'dateFormat' => 'Y-m-d H:i:S'
				],
				'conditions'     => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'date_time',
						],
						[
							'name'     => 'date_time_logic',
							'operator' => '!==',
							'value'    => 'equal',
						],
						[
							'name'     => 'date_time_logic',
							'operator' => '!==',
							'value'    => 'not_equal',
						],
					],
				]
			]
		);

		$repeater->add_control(
			'recurring_day_logic',
			[
				'label'      => __( 'Recurring Day', 'essential-addons-elementor' ),
				'show_label' => false,
				'type'       => Controls_Manager::CHOOSE,
				'options'    => [
					'between'     => [
						'title' => esc_html__( 'Between', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-check-solid',
					],
					'not_between' => [
						'title' => esc_html__( 'Not Between', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-xmark-solid',
					],
				],
				'default'    => 'between',
				'toggle'     => false,
				'condition'  => [
					'logic_type' => 'recurring_day',
				]
			]
		);

        $repeater->add_control(
            'recurring_days_all',
            [
                'label'          => __( 'All Days', 'essential-addons-elementor' ),
                'type'           => Controls_Manager::SWITCHER,
                'default'        => '',
                'label_on'       => __( 'Yes', 'essential-addons-elementor' ),
                'label_off'      => __( 'No', 'essential-addons-elementor' ),
                'return_value'   => 'yes',
                'condition'   => [
                    'logic_type' => 'recurring_day',
                ]
            ]
        );

		$repeater->add_control(
			'recurring_days',
			[
				'label'       => __( 'Recurring Days', 'essential-addons-elementor' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->get_days_list(),
				'default'     => [ key( $this->get_days_list() ) ],
				'condition'   => [
					'logic_type' => 'recurring_day',
                    'recurring_days_all!' => 'yes'
				]
			]
		);

		$repeater->add_control(
			'recurring_days_heading',
			[
				'label'     => __( 'Date Duration', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'logic_type' => 'recurring_day',
				]
			]
		);

		$repeater->add_control(
			'recurring_days_duration_from',
			[
				'label'          => esc_html__( 'From', 'essential-addons-elementor' ),
				'label_block'    => false,
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'altInput'   => true,
					'altFormat'  => 'M j, Y',
					'dateFormat' => 'Y-m-d',
					'enableTime' => false,
				],
				'conditions'     => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'recurring_day',
						],
					],
				]
			]
		);

		$repeater->add_control(
			'recurring_days_duration_to',
			[
				'label'          => esc_html__( 'To', 'essential-addons-elementor' ),
				'label_block'    => false,
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'altInput'   => true,
					'altFormat'  => 'M j, Y',
					'dateFormat' => 'Y-m-d',
					'enableTime' => false,
				],
				'conditions'     => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'recurring_day',
						],
					],
				]
			]
		);

		$repeater->add_control(
			'recurring_days_heading2',
			[
				'label'     => __( 'Time Duration', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'logic_type' => 'recurring_day',
				]
			]
		);

		$repeater->add_control(
			'from_time',
			[
				'label'       => esc_html__( 'From', 'essential-addons-elementor' ),
				'label_block' => false,
                'type'           => Controls_Manager::DATE_TIME,
                'picker_options' => [
                    'altInput'   => true,
                    'altFormat'  => 'h:i K',
                    'enableTime' => true,
                    'noCalendar' => true,
                ],
				'conditions'  => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'recurring_day',
						],
					],
				]
			]
		);

		$repeater->add_control(
			'to_time',
			[
				'label'       => esc_html__( 'To', 'essential-addons-elementor' ),
				'label_block' => false,
                'type'           => Controls_Manager::DATE_TIME,
                'picker_options' => [
                    'altInput'   => true,
                    'altFormat'  => 'h:i K',
                    'enableTime' => true,
                    'noCalendar' => true,
                ],
				'conditions'  => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'recurring_day',
						],
					],
				]
			]
		);

		$repeater->add_control(
			'query_key',
			[
				'label'       => esc_html__( 'Key', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Query Key', 'essential-addons-elementor' ),
				'condition'   => [
					'logic_type' => 'query_string',
				]
			]
		);

		$repeater->add_control(
			'query_value',
			[
				'label'       => esc_html__( 'Value', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Query Value', 'essential-addons-elementor' ),
				'condition'   => [
					'logic_type' => 'query_string',
				]
			]
		);

		$element->add_control(
			'eael_cl_logics',
			[
				'label'          => __( 'Logics', 'essential-addons-elementor' ),
				'type'           => Controls_Manager::REPEATER,
				'fields'         => $repeater->get_controls(),
				'default'        => [
					[
						'logic_type'           => 'login_status',
						'login_status_operand' => 'logged_in',
					],
				],
				'style_transfer' => false,
				'title_field'    => '{{{ ea_conditional_logic_type_title(logic_type) }}}',
				'condition'      => [
					'eael_cl_enable'             => 'yes',
					'eael_cl_visibility_action!' => 'forcefully_hide',
				]
			]
		);

		$element->end_controls_section();
	}

	/**
	 * Get All editable roles and return array with simple slug|name pare
	 *
	 * @param $first_index
	 * @param $output
	 *
	 * @return array|string
	 */
	public function get_editable_roles() {
		$wp_roles       = [ '' => __( 'Select', 'essential-addons-elementor' ) ];
		$all_roles      = wp_roles()->roles;
		$editable_roles = apply_filters( 'editable_roles', $all_roles );

		foreach ( $editable_roles as $slug => $editable_role ) {
			$wp_roles[ $slug ] = $editable_role['name'];
		}

		return $wp_roles;
	}

	/**
	 * Get all browser list and return array with simple slug|name pare
	 *
	 * @return array
	 */
	public function get_browser_list() {
		return [
			'chrome'    => __( 'Google Chrome', 'essential-addons-elementor' ),
			'firefox'   => __( 'Mozilla Firefox', 'essential-addons-elementor' ),
			'safari'    => __( 'Safari', 'essential-addons-elementor' ),
			'i_safari'  => __( 'Iphone Safari', 'essential-addons-elementor' ),
			'opera'     => __( 'Opera', 'essential-addons-elementor' ),
			'edge'      => __( 'Edge', 'essential-addons-elementor' ),
			'ie'        => __( 'Internet Explorer', 'essential-addons-elementor' ),
			'mac_ie'    => __( 'Internet Explorer for Mac OS X', 'essential-addons-elementor' ),
			'netscape4' => __( 'Netscape 4', 'essential-addons-elementor' ),
			'lynx'      => __( 'Lynx', 'essential-addons-elementor' ),
			'others'    => __( 'Others', 'essential-addons-elementor' ),
		];
	}

	/**
	 * Get all days list of a week and return array with simple slug|name pare
	 *
	 * @return array
	 */
	public function get_days_list() {
		return [
			'sun' => __( 'Sunday', 'essential-addons-elementor' ),
			'mon' => __( 'Monday', 'essential-addons-elementor' ),
			'tue' => __( 'Tuesday', 'essential-addons-elementor' ),
			'wed' => __( 'Wednesday', 'essential-addons-elementor' ),
			'thu' => __( 'Thursday', 'essential-addons-elementor' ),
			'fri' => __( 'Friday', 'essential-addons-elementor' ),
			'sat' => __( 'Saturday', 'essential-addons-elementor' )
		];
	}

	/**
	 * Get current browser
	 *
	 * @return string
	 */
	public function get_current_browser() {
		global $is_lynx, $is_gecko, $is_winIE, $is_macIE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone, $is_edge;

		$browser = 'others';

		switch ( true ) {
			case $is_chrome:
				$browser = 'chrome';
				break;
			case $is_gecko:
				$browser = 'firefox';
				break;
			case $is_safari:
				$browser = 'safari';
				break;
			case $is_iphone:
				$browser = 'i_safari';
				break;
			case $is_opera:
				$browser = 'opera';
				break;
			case $is_edge:
				$browser = 'edge';
				break;
			case $is_winIE:
				$browser = 'ie';
				break;
			case $is_macIE:
				$browser = 'mac_ie';
				break;
			case $is_NS4:
				$browser = 'netscape4';
				break;
			case $is_lynx:
				$browser = 'lynx';
				break;

		}

		return $browser;
	}

	public function parse_arg( $arg ) {
		$arg = wp_parse_args( $arg, [
			'eael_cl_enable'            => '',
			'eael_cl_visibility_action' => '',
			'eael_cl_logics'            => [],
			'eael_cl_action_apply_if'   => '',
		] );

		return $arg;
	}

	/**
	 * Check all logics and return the final result
	 *
	 * @param $settings
	 *
	 * @return bool
	 */
	public function check_logics( $settings ) {
		$return                = false;
		$needed_any_logic_true = $settings['eael_cl_action_apply_if'] === 'any';
		$needed_all_logic_true = $settings['eael_cl_action_apply_if'] === 'all';

		foreach ( $settings['eael_cl_logics'] as $cl_logic ) {
			switch ( $cl_logic['logic_type'] ) {
				case 'login_status':
					$return = $cl_logic['login_status_operand'] === 'logged_in' ? is_user_logged_in() : ! is_user_logged_in();

					if ( is_user_logged_in() && $cl_logic['user_and_role'] !== '' ) {
						if ( $cl_logic['user_and_role'] === 'user_role' ) {
							$user_roles = get_userdata( get_current_user_id() )->roles;
							$operand    = $cl_logic['user_role_operand_multi'];
							$result     = array_intersect( $user_roles, $operand );
							$return     = ( $cl_logic['logic_operator_between'] === 'between' ) ? count( $result ) > 0 : count( $result ) == 0;
						} elseif ( $cl_logic['user_and_role'] === 'user' ) {
							$user    = get_current_user_id();
							$operand = array_map( 'intval', (array) $cl_logic['user_operand'] );
							$return  = $cl_logic['logic_operator_between'] === 'between' ? in_array( $user, $operand ) : ! in_array( $user, $operand );
						}
					}

					if ( $needed_any_logic_true && $return ) {
						break( 2 );
					}

					if ( $needed_all_logic_true && ! $return ) {
						break( 2 );
					}

					break;
				case 'post_type':
					$ID                = get_the_ID();
					$post_type_operand = $cl_logic['post_type_operand'];
					$operand           = empty( $post_type_operand ) ? (array) $cl_logic['post_operand'] : (array) $cl_logic["post_operand_{$post_type_operand}"];

					if ( count( $operand ) ) {
						$return = $cl_logic['logic_operator_between'] === 'between' ? in_array( $ID, $operand ) : ! in_array( $ID, $operand );
					} else {
						$post_type = get_post_type( $ID );
						$return    = $cl_logic['logic_operator_between'] === 'between' ? $post_type === $post_type_operand : $post_type !== $post_type_operand;
					}

					if ( $needed_any_logic_true && $return ) {
						break( 2 );
					}

					if ( $needed_all_logic_true && ! $return ) {
						break( 2 );
					}

					break;
				case 'browser':
					$browser = $this->get_current_browser();
					$operand = (array) $cl_logic['browser_operand'];
					$return  = $cl_logic['logic_operator_between'] === 'between' ? in_array( $browser, $operand ) : ! in_array( $browser, $operand );

					if ( $needed_any_logic_true && $return ) {
						break( 2 );
					}

					if ( $needed_all_logic_true && ! $return ) {
						break( 2 );
					}

					break;
				case 'date_time':
					$current_time = current_time( 'U' );
					$from         = ( $cl_logic['date_time_logic'] === 'equal' || $cl_logic['date_time_logic'] === 'not_equal' ) ? strtotime( "{$cl_logic['single_date']} 00:00:00" ) : strtotime( $cl_logic['from_date'] );
					$to           = ( $cl_logic['date_time_logic'] === 'equal' || $cl_logic['date_time_logic'] === 'not_equal' ) ? strtotime( "{$cl_logic['single_date']} 23:59:59" ) : strtotime( $cl_logic['to_date'] );
					$return       = $cl_logic['date_time_logic'] === 'equal' || $cl_logic['date_time_logic'] === 'between' ? $from <= $current_time && $current_time <= $to : $from >= $current_time || $current_time >= $to;

					if ( $needed_any_logic_true && $return ) {
						break( 2 );
					}

					if ( $needed_all_logic_true && ! $return ) {
						break( 2 );
					}

					break;
				case 'recurring_day':
					$current_time = current_time( 'U' );
					$from_date    = isset( $cl_logic['recurring_days_duration_from'] ) && $cl_logic['recurring_days_duration_from'] ? strtotime( "{$cl_logic['recurring_days_duration_from']} 00:00:00" ) : $current_time - WEEK_IN_SECONDS;
					$to_date      = isset( $cl_logic['recurring_days_duration_to'] ) && $cl_logic['recurring_days_duration_to'] ? strtotime( "{$cl_logic['recurring_days_duration_to']} 23:59:59" ) : $current_time + WEEK_IN_SECONDS;
					$is_today     = isset( $cl_logic['recurring_days_all'] ) && $cl_logic['recurring_days_all'] === 'yes' || in_array(strtolower(date('D')), $cl_logic['recurring_days']);
					$from_time    = isset( $cl_logic['from_time'] ) ? strtotime( $cl_logic['from_time'] ) : strtotime( '00:00:00' );
					$to_time      = isset( $cl_logic['to_time'] ) ? strtotime( $cl_logic['to_time'] ) : strtotime( '23:59:59' );

					$return = $is_today && $from_date < $current_time && $to_date > $current_time && $from_time < $current_time && $to_time > $current_time;
					$return = $cl_logic['recurring_day_logic'] === 'between' ? $return : ! $return;

					if ( $needed_any_logic_true && $return ) {
						break( 2 );
					}

					if ( $needed_all_logic_true && ! $return ) {
						break( 2 );
					}

					break;
				case 'dynamic':
					if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
						$dynamic_field = strip_tags( $cl_logic['dynamic_field'] );
						$dynamic_field = strtolower( $dynamic_field );
						$dynamic_field = explode( '|', $dynamic_field );
						$dynamic_field = array_map( 'trim', $dynamic_field );
						$value         = explode( '|', strtolower( $cl_logic['dynamic_operand'] ) );
						$value         = array_map( 'trim', $value );
						$result        = array_intersect( $dynamic_field, $value );
						$return        = ( $cl_logic['logic_operator_dynamic'] === 'between' ) ? count( $result ) > 0 : count( $result ) == 0;

						if ( $needed_any_logic_true && $return ) {
							break( 2 );
						}

						if ( $needed_all_logic_true && ! $return ) {
							break( 2 );
						}
					}

					break;
				case 'query_string':

					$return = $cl_logic['query_key'] && isset( $_GET[ $cl_logic['query_key'] ] ) && sanitize_text_field( $_GET[ $cl_logic['query_key'] ] ) === $cl_logic['query_value'];
					$return = $cl_logic['logic_operator_between'] === 'between' ? $return : ! $return;

					if ( $needed_any_logic_true && $return ) {
						break( 2 );
					}

					if ( $needed_all_logic_true && ! $return ) {
						break( 2 );
					}

					break;
			}
		}

		return $return;
	}

	public function content_render( $should_render, Element_Base $element ) {
		$settings = $element->get_settings_for_display();
		$settings = $this->parse_arg( $settings );
		
		if ( $settings['eael_cl_enable'] === 'yes' ) {
			switch ( $settings['eael_cl_visibility_action'] ) {
				case 'show':
					return $this->check_logics( $settings ) ? true : false;
					break;
				case 'hide':
					return $this->check_logics( $settings ) ? false : true;
					break;
				case 'forcefully_hide':
					return false;
			}
		}

		return $should_render;
	}

}
