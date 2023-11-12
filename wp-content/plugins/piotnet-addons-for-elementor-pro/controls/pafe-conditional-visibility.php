<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Conditional_Visibility extends \Elementor\Widget_Base {

    public function __construct() {
        parent::__construct();
        $this->init_control();
    }

    public function get_name() {
        return 'pafe-conditional-visibility';
    }

    public function pafe_register_controls( $element, $args ) {

        $element->start_controls_section(
            'pafe_conditional_visibility',
            [
                'label' => __( 'PAFE Conditional Visibility', 'pafe' ),
                'tab' => PAFE_Controls_Manager::TAB_PAFE,
            ]
        );

        $element->add_control(
            'pafe_conditional_visibility_enable',
            [
                'label' => __( 'Enable', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'yes',
            ]
        );

        global $wp_roles;
        $roles = $wp_roles->roles;
        $roles_array = array();
        $roles_array['all'] = 'All';
        $roles_array['non_logged_in'] = 'Non Logged';
        $roles_array['logged_in'] = 'Logged In';
        foreach ($roles as $key => $value) {
            $roles_array[$key] = $value['name'];
        }

        $element->add_control(
            'pafe_conditional_visibility_by_roles',
            [
                'label' => __( 'Visibility By User', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'yes',
                'condition' => [
                    'pafe_conditional_visibility_enable' => 'yes',
                ],
            ]
        );

        $element->add_control(
            'pafe_conditional_visibility_roles',
            [
                'label' => __( 'Set Roles', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $roles_array,
                'label_block' => true,
                'default' => [
                    'all',
                ],
                'condition' => [
                    'pafe_conditional_visibility_enable' => 'yes',
                    'pafe_conditional_visibility_by_roles' => 'yes',
                ],
            ]
        );

        /*$element->add_control(
            'pafe_conditional_visibility_by_post',
            [
                'label' => __( 'Visibility By Posts', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'yes',
                'condition' => [
                    'pafe_conditional_visibility_enable' => 'yes',
                ],
            ]
        );
        $element->add_control(
            'pafe_conditional_visibility_action_for_post',
            [
                'label' => __( 'Action', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'options' => [
                    'show' => __( 'Show', 'pafe' ),
                    'hide' => __( 'Hide', 'pafe' ),
                ],
                'default' => 'show',
                'condition' => [
                    'pafe_conditional_visibility_enable' => 'yes',
                    'pafe_conditional_visibility_by_post' => 'yes',
                ],
            ]
        );

        $repeater = new \Elementor\Repeater();
        $post_types = get_post_types( [], 'objects' );
        $post_types_array = array( '' => 'None' );
        $taxonomy = array();
        foreach ( $post_types as $post_type ) {
            $post_types_array[$post_type->name] = $post_type->label;
            $taxonomy_of_post_type = get_object_taxonomies( $post_type->name, 'names' );
            $post_type_name = $post_type->name;
            if (!empty($taxonomy_of_post_type) && $post_type_name != 'nav_menu_item' && $post_type_name != 'elementor_library' && $post_type_name != 'elementor_font' ) {
                if ($post_type_name == 'post') {
                    $taxonomy_of_post_type = array_diff( $taxonomy_of_post_type, ["post_format"] );
                }
                $taxonomy[$post_type_name] = $taxonomy_of_post_type;
            }
        }
        $taxonomy_array = array();
        foreach ($taxonomy as $key => $value) {
            foreach ($value as $key_item => $value_item) {
                $taxonomy_array[$value_item . '|' . $key] = $value_item . ' - ' . $key;
            }
        }

        $repeater->add_control(
            'pafe_conditional_visibility_taxonomy',
            [
                'label' => __( 'Taxonomy', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $taxonomy_array,
                'default' => 'category|post',
            ]
        );

        $repeater->add_control(
            'pafe_conditional_visibility_term',
            [
                'label' => __( 'Terms ID', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => 'Enter the term ID',
                'default' => '',
            ]
        );

        $repeater->add_control(
            'pafe_conditional_visibility_by_post_operators',
            [
                'label' => __( 'OR, AND Operators', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'options' => [
                    'or' => __( 'OR', 'pafe' ),
                    'and' => __( 'AND', 'pafe' ),
                ],
                'default' => 'or',
            ]
        );


        $element->add_control(
            'pafe_conditional_visibility_by_post_list',
            array(
                'type'    => Elementor\Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'title_field' => '',
                'condition' => [
                    'pafe_conditional_visibility_enable' => 'yes',
                    'pafe_conditional_visibility_by_post' => 'yes',
                ],
            )
        );*/
        $days_of_week = array('Sunday', 'Monday', 'Tuesday', 'Wednesday','Thursday','Friday', 'Saturday');

        $element->add_control(
            'pafe_conditional_visibility_by_date_and_time',
            [
                'label' => __( 'Visibility By Date And Time', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'yes',
                'condition' => [
                    'pafe_conditional_visibility_enable' => 'yes',
                ],
            ]
        );

        $element->add_control(
            'pafe_conditional_visibility_date_and_time_operators',
            [
                'label' => __( 'OR, AND Operators', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'options' => [
                    'or' => __( 'OR', 'pafe' ),
                    'and' => __( 'AND', 'pafe' ),
                ],
                'default' => 'or',
                'condition' => [
                    'pafe_conditional_visibility_enable' => 'yes',
                    'pafe_conditional_visibility_by_date_and_time' => 'yes',
                ],
            ]
        );

        $element->add_control(
            'pafe_conditional_visibility_action_for_date_and_time',
            [
                'label' => __( 'Action', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'options' => [
                    'show' => __( 'Show', 'pafe' ),
                    'hide' => __( 'Hide', 'pafe' ),
                ],
                'default' => 'show',
                'condition' => [
                    'pafe_conditional_visibility_enable' => 'yes',
                    'pafe_conditional_visibility_by_date_and_time' => 'yes',
                ],
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'pafe_conditional_visibility_set_days_of_week',
            [
                'label' => __( 'Choose Day', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $days_of_week,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'pafe_conditional_visibility_start_date',
            [
                'label' => __( 'Start Date', 'pafe' ),
                'type' => \Elementor\Controls_Manager::DATE_TIME,
                'picker_options' => [
                    'enableTime' => false,
                ]
            ]
        );

        $repeater->add_control(
            'pafe_conditional_visibility_end_date',
            [
                'label' => __( 'End Date', 'pafe' ),
                'type' => \Elementor\Controls_Manager::DATE_TIME,
                'picker_options' => [
                    'enableTime' => false,
                ]
            ]
        );

        $repeater->add_control(
            'pafe_conditional_visibility_time_start',
            [
                'label' => __( 'Time Start', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __( '', 'pafe' ),
                'placeholder' => __( 'HH:mm', 'pafe' ),
                'description' => __('It was setted in HH:mm format','pafe'),
            ]
        );

        $repeater->add_control(
            'pafe_conditional_visibility_time_end',
            [
                'label' => __( 'Time End', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __( '', 'pafe' ),
                'placeholder' => __( 'HH:mm', 'pafe' ),
                'description' => __('It was setted in HH:mm format','pafe'),
            ]
        );

        $element->add_control(
            'pafe_conditional_visibility_time_repeater',
            [
                'type' => Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '',
                'condition' => [
                    'pafe_conditional_visibility_enable' => 'yes',
                    'pafe_conditional_visibility_by_date_and_time' => 'yes',
                ],
            ]
        );

        $element->add_control(
            'pafe_conditional_visibility_by_backend',
            [
                'label' => __( 'Conditional Visibility By Custom Fields and URL Parameters, URL Contains', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => 'Yes',
                'label_off' => 'No',
                'label_block' => true,
                'return_value' => 'yes',
                'condition' => [
                    'pafe_conditional_visibility_enable' => 'yes',
                ],
            ]
        );

        $element->add_control(
            'pafe_conditional_visibility_action',
            [
                'label' => __( 'Action', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'options' => [
                    'show' => __( 'Show', 'pafe' ),
                    'hide' => __( 'Hide', 'pafe' ),
                ],
                'default' => 'show',
                'condition' => [
                    'pafe_conditional_visibility_enable' => 'yes',
                    'pafe_conditional_visibility_by_backend' => 'yes',
                ],
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'pafe_conditional_visibility_by_backend_select',
            [
                'label' => __( 'Custom Fields or URL Parameters, URL Contains', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'options' => [
                    'custom_field' => __( 'Custom Field', 'pafe' ),
                    'url_parameter' => __( 'URL Parameter', 'pafe' ),
                    'url_contains' => __( 'URL Contains', 'pafe' ),
                ],
                'default' => 'custom_field',
            ]
        );

        $repeater->add_control(
            'pafe_conditional_visibility_roles_custom_field_source',
            [
                'label' => __( 'Custom Fields', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'post_custom_field' => __( 'Post Custom Field', 'pafe' ),
                    'acf_field' => __( 'ACF Field', 'pafe' ),
                ],
                'default' => 'post_custom_field',
                'condition' => [
                    'pafe_conditional_visibility_by_backend_select' => 'custom_field',
                ],
            ]
        );

        $repeater->add_control(
            'pafe_conditional_visibility_roles_url_parameter',
            [
                'label' => __( 'URL Parameter', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => __('E.g ref, yourparam','pafe'),
                'condition' => [
                    'pafe_conditional_visibility_by_backend_select' => 'url_parameter',
                ],
            ]
        );

        $repeater->add_control(
            'pafe_conditional_visibility_roles_custom_field_key',
            [
                'label' => __( 'Custom Field Key', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'condition' => [
                    'pafe_conditional_visibility_by_backend_select' => 'custom_field',
                ],
            ]
        );

        $repeater->add_control(
            'pafe_conditional_visibility_roles_custom_field_comparison_operators',
            [
                'label' => __( 'Comparison Operators', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'options' => [
                    'not-empty' => __( 'not empty', 'pafe' ),
                    'empty' => __( 'empty', 'pafe' ),
                    '=' => __( 'equals', 'pafe' ),
                    '!=' => __( 'not equals', 'pafe' ),
                    '>' => __( '>', 'pafe' ),
                    '>=' => __( '>=', 'pafe' ),
                    '<' => __( '<', 'pafe' ),
                    '<=' => __( '<=', 'pafe' ),
                    'true' => __( 'true', 'pafe' ),
                    'false' => __( 'false', 'pafe' ),
                    'contains' => __( 'contains (ACF Checkbox)', 'pafe' ),
                ],
                'default' => 'not-empty',
                'condition' => [
                    'pafe_conditional_visibility_by_backend_select!' => 'url_contains',
                ],
            ]
        );

        $repeater->add_control(
            'pafe_conditional_visibility_roles_custom_field_type',
            [
                'label' => __( 'Type Value', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'options' => [
                    'string' => __( 'String', 'pafe' ),
                    'number' => __( 'Number', 'pafe' ),
                ],
                'default' => 'string',
                'condition' => [
                    'pafe_conditional_visibility_roles_custom_field_comparison_operators' => ['=','!=','>','>=','<','<=','contains'],
                ],
            ]
        );

        $repeater->add_control(
            'pafe_conditional_visibility_roles_custom_field_value',
            [
                'label' => __( 'Value', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => __( '50', 'pafe' ),
                'condition' => [
                    'pafe_conditional_visibility_roles_custom_field_comparison_operators' => ['=','!=','>','>=','<','<=','contains'],
                    'pafe_conditional_visibility_by_backend_select!' => 'url_contains',
                ],
            ]
        );

        $repeater->add_control(
            'pafe_conditional_visibility_roles_custom_field_value_url_contains',
            [
                'label' => __( 'Value', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => __( '/page-slug/', 'pafe' ),
                'condition' => [
                    'pafe_conditional_visibility_by_backend_select' => 'url_contains',
                ],
            ]
        );

        $repeater->add_control(
            'pafe_conditional_visibility_roles_and_or_operators',
            [
                'label' => __( 'OR, AND Operators', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'options' => [
                    'or' => __( 'OR', 'pafe' ),
                    'and' => __( 'AND', 'pafe' ),
                ],
                'default' => 'or',
            ]
        );

        $element->add_control(
            'pafe_conditional_visibility_by_backend_list',
            [
                'type' => Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ pafe_conditional_visibility_roles_url_parameter }}} {{{ pafe_conditional_visibility_roles_custom_field_key }}} {{{ pafe_conditional_visibility_roles_custom_field_comparison_operators }}} {{{ pafe_conditional_visibility_roles_custom_field_value }}}',
                'condition' => [
                    'pafe_conditional_visibility_enable' => 'yes',
                    'pafe_conditional_visibility_by_backend' => 'yes',
                ],
            ]
        );

        // Fix Elementor Form

        $repeater = new Elementor\Repeater();

        $field_types = [
            'text' => __( 'Text', 'elementor-pro' ),
            'email' => __( 'Email', 'elementor-pro' ),
            'textarea' => __( 'Textarea', 'elementor-pro' ),
            'url' => __( 'URL', 'elementor-pro' ),
            'tel' => __( 'Tel', 'elementor-pro' ),
            'radio' => __( 'Radio', 'elementor-pro' ),
            'select' => __( 'Select', 'elementor-pro' ),
            'checkbox' => __( 'Checkbox', 'elementor-pro' ),
            'acceptance' => __( 'Acceptance', 'elementor-pro' ),
            'number' => __( 'Number', 'elementor-pro' ),
            'date' => __( 'Date', 'elementor-pro' ),
            'time' => __( 'Time', 'elementor-pro' ),
            'upload' => __( 'File Upload', 'elementor-pro' ),
            'password' => __( 'Password', 'elementor-pro' ),
            'html' => __( 'HTML', 'elementor-pro' ),
            'hidden' => __( 'Hidden', 'elementor-pro' ),
        ];

        /**
         * Forms field types.
         *
         * Filters the list of field types displayed in the form `field_type` control.
         *
         * @since 1.0.0
         *
         * @param array $field_types Field types.
         */
        $field_types = apply_filters( 'elementor_pro/forms/field_types', $field_types );

        $repeater->start_controls_tabs( 'form_fields_tabs' );

        $repeater->start_controls_tab( 'form_fields_content_tab', [
            'label' => __( 'Content', 'elementor-pro' ),
        ] );

        $repeater->add_control(
            'field_type',
            [
                'label' => __( 'Type', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => $field_types,
                'default' => 'text',
            ]
        );

        $repeater->add_control(
            'field_label',
            [
                'label' => __( 'Label', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $repeater->add_control(
            'placeholder',
            [
                'label' => __( 'Placeholder', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'operator' => 'in',
                            'value' => [
                                'tel',
                                'text',
                                'email',
                                'textarea',
                                'number',
                                'url',
                                'password',
                            ],
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'required',
            [
                'label' => __( 'Required', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'true',
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'operator' => '!in',
                            'value' => [
                                'checkbox',
                                'recaptcha',
                                'recaptcha_v3',
                                'hidden',
                                'html',
                            ],
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'field_options',
            [
                'label' => __( 'Options', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
                'default' => '',
                'description' => __( 'Enter each option in a separate line. To differentiate between label and value, separate them with a pipe char ("|"). For example: First Name|f_name', 'elementor-pro' ),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'operator' => 'in',
                            'value' => [
                                'select',
                                'checkbox',
                                'radio',
                            ],
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'allow_multiple',
            [
                'label' => __( 'Multiple Selection', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'true',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'value' => 'select',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'select_size',
            [
                'label' => __( 'Rows', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::NUMBER,
                'min' => 2,
                'step' => 1,
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'value' => 'select',
                        ],
                        [
                            'name' => 'allow_multiple',
                            'value' => 'true',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'inline_list',
            [
                'label' => __( 'Inline List', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'elementor-subgroup-inline',
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'operator' => 'in',
                            'value' => [
                                'checkbox',
                                'radio',
                            ],
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'field_html',
            [
                'label' => __( 'HTML', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'value' => 'html',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_responsive_control(
            'width',
            [
                'label' => __( 'Column Width', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => [
                    '' => __( 'Default', 'elementor-pro' ),
                    '100' => '100%',
                    '80' => '80%',
                    '75' => '75%',
                    '66' => '66%',
                    '60' => '60%',
                    '50' => '50%',
                    '40' => '40%',
                    '33' => '33%',
                    '25' => '25%',
                    '20' => '20%',
                ],
                'default' => '100',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'operator' => '!in',
                            'value' => [
                                'hidden',
                                'recaptcha',
                                'recaptcha_v3',
                            ],
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'rows',
            [
                'label' => __( 'Rows', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::NUMBER,
                'default' => 4,
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'value' => 'textarea',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'recaptcha_size', [
                'label' => __( 'Size', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'default' => 'normal',
                'options' => [
                    'normal' => __( 'Normal', 'elementor-pro' ),
                    'compact' => __( 'Compact', 'elementor-pro' ),
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'value' => 'recaptcha',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'recaptcha_style',
            [
                'label' => __( 'Style', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'default' => 'light',
                'options' => [
                    'light' => __( 'Light', 'elementor-pro' ),
                    'dark' => __( 'Dark', 'elementor-pro' ),
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'value' => 'recaptcha',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'recaptcha_badge', [
                'label' => __( 'Badge', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'default' => 'bottomright',
                'options' => [
                    'bottomright' => __( 'Bottom Right', 'elementor-pro' ),
                    'bottomleft' => __( 'Bottom Left', 'elementor-pro' ),
                    'inline' => __( 'Inline', 'elementor-pro' ),
                ],
                'description' => __( 'To view the validation badge, switch to preview mode', 'elementor-pro' ),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'value' => 'recaptcha_v3',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'css_classes',
            [
                'label' => __( 'CSS Classes', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::HIDDEN,
                'default' => '',
                'title' => __( 'Add your custom class WITHOUT the dot. e.g: my-class', 'elementor-pro' ),
            ]
        );

        $repeater->end_controls_tab();

        $repeater->start_controls_tab(
            'form_fields_advanced_tab',
            [
                'label' => __( 'Advanced', 'elementor-pro' ),
                'condition' => [
                    'field_type!' => 'html',
                ],
            ]
        );

        $repeater->add_control(
            'field_value',
            [
                'label' => __( 'Default Value', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'default' => '',
                'dynamic' => [
                    'active' => true,
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'operator' => 'in',
                            'value' => [
                                'text',
                                'email',
                                'textarea',
                                'url',
                                'tel',
                                'radio',
                                'select',
                                'number',
                                'date',
                                'time',
                                'hidden',
                            ],
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'custom_id',
            [
                'label' => __( 'ID', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'description' => __( 'Please make sure the ID is unique and not used elsewhere in this form. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'elementor-pro' ),
                'render_type' => 'none',
            ]
        );

        $repeater->add_control(
            'shortcode',
            [
                'label' => __( 'Shortcode', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::RAW_HTML,
                'classes' => 'forms-field-shortcode',
                'raw' => '<input class="elementor-form-field-shortcode" readonly />',
            ]
        );

        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $element->end_controls_section();

    }

    public function should_render( $should_render, $section ) {
        $settings = $section->get_settings();

        if ( 'yes' == $section->get_settings( 'pafe_conditional_visibility_enable' ) ) {
            $visibility_roles = $section->get_settings( 'pafe_conditional_visibility_roles' );
            $condition2 = true;
            $show = $settings['pafe_conditional_visibility_action'];
            $user = wp_get_current_user();
            $user_roles = $user->roles;

            //Conditional by Posts
            /*if ( 'yes' == $settings['pafe_conditional_visibility_by_post'] ) {
                foreach ( $settings['pafe_conditional_visibility_by_post_list'] as $item ) {
                    $taxonomy_post_type = explode('|',$item['pafe_conditional_visibility_taxonomy']);
                    $terms_array = array();
                    if ( !empty($item['pafe_conditional_visibility_taxonomy']) ) {
                        $terms_array['taxonomy'] = $taxonomy_post_type[0];
                        $terms_array['hide_empty'] = false;
                    }
                    $terms = get_terms($terms_array);
                    foreach ( $terms as $term_item ) {
                        $terms_id = $term_item->term_id;
                        if ( $item['pafe_conditional_visibility_term'] == $terms_id ) {

                            $condition4 = false;
                        } else {
                            $condition4 = true;
                            break;
                        }
                    }
                }
            }*/

            if ( 'yes' == $settings['pafe_conditional_visibility_by_roles'] ) {
                $role_condition = false;
                if (in_array('all', $visibility_roles) || (in_array('logged_in', $visibility_roles) && is_user_logged_in()) || (in_array('non_logged_in', $visibility_roles) && !is_user_logged_in())) {
                    $role_condition = true;
                }
                if (isset($user_roles[0]) && in_array($user_roles[0], $visibility_roles)) {
                    $role_condition = true;
                }
                if (!$role_condition) {
                    return false;
                }
            }

            if (!empty($settings['pafe_conditional_visibility_by_backend'])) {
                if ( array_key_exists( 'pafe_conditional_visibility_by_backend_list',$settings )) {
                    $list = $settings['pafe_conditional_visibility_by_backend_list'];
                    //$show = $settings['pafe_conditional_visibility_action'];

                    if( !empty($list[0]['pafe_conditional_visibility_by_backend_select']) ) {
                        $conditionals_count = count($list);
                        $conditionals_and_or = '';
                        $error = 0;
                        $condition = false;
                        foreach ($list as $item) {
                            $conditionals_and_or = $item['pafe_conditional_visibility_roles_and_or_operators'];

                            if ($item['pafe_conditional_visibility_by_backend_select'] == 'custom_field' && !empty($item['pafe_conditional_visibility_roles_custom_field_key'])) {

                                $field_key = $item['pafe_conditional_visibility_roles_custom_field_key'];
                                $field_source = $item['pafe_conditional_visibility_roles_custom_field_source'];
                                $field_value = '';
                                $comparison = $item['pafe_conditional_visibility_roles_custom_field_comparison_operators'];
                                $comparison_value = $item['pafe_conditional_visibility_roles_custom_field_value'];
                                $id = get_the_ID();

                                if( $field_source == 'post_custom_field' ) {
                                    $field_value = get_post_meta( $id, $field_key, true );
                                } else {
                                    if (function_exists('get_field')) {
                                        $field_value = get_field($field_key,$id);
                                    }
                                }

                                if($item['pafe_conditional_visibility_roles_custom_field_type'] == 'number') {
                                    $field_value == floatval($field_value);
                                }

                                if (is_array($field_value) && $comparison == 'contains') {
                                    if (in_array($comparison_value, $field_value)) {
                                        $condition = true;
                                    } else {
                                        $error++;
                                    }
                                } else {
                                    if ($comparison == 'not-empty' && !empty($field_value) || $comparison == 'empty' && empty($field_value) || $comparison == 'true' && $field_value == true || $comparison == 'false' && $field_value == false || $comparison == '=' && $field_value == $comparison_value || $comparison == '!=' && $field_value != $comparison_value || $comparison == '>' && $field_value > $comparison_value || $comparison == '>=' && $field_value >= $comparison_value || $comparison == '<' && $field_value < $comparison_value || $comparison == '<=' && $field_value <= $comparison_value ) {
                                        $condition = true;
                                    } else {
                                        $error++;
                                    }
                                }

                            }

                            if ($item['pafe_conditional_visibility_by_backend_select'] == 'url_parameter' && !empty($item['pafe_conditional_visibility_roles_url_parameter'])) {

                                $url_parameter = $item['pafe_conditional_visibility_roles_url_parameter'];
                                $comparison = $item['pafe_conditional_visibility_roles_custom_field_comparison_operators'];
                                $comparison_value = $item['pafe_conditional_visibility_roles_custom_field_value'];
                                $field_value = '';

                                if (!empty($_GET[$url_parameter])) {
                                    $field_value = $_GET[$url_parameter];
                                }

                                if($item['pafe_conditional_visibility_roles_custom_field_type'] == 'number') {
                                    $field_value == floatval($field_value);
                                }

                                if ($comparison == 'not-empty' && !empty($field_value) || $comparison == 'empty' && empty($field_value) || $comparison == 'true' && $field_value == true || $comparison == 'false' && $field_value == false || $comparison == '=' && $field_value == $comparison_value || $comparison == '!=' && $field_value != $comparison_value || $comparison == '>' && $field_value > $comparison_value || $comparison == '>=' && $field_value >= $comparison_value || $comparison == '<' && $field_value < $comparison_value || $comparison == '<=' && $field_value <= $comparison_value ) {
                                    $condition = true;
                                } else {
                                    $error++;
                                }
                            }

                            if ($item['pafe_conditional_visibility_by_backend_select'] == 'url_contains' && !empty($item['pafe_conditional_visibility_roles_custom_field_value_url_contains'])) {

                                $url_contains = $item['pafe_conditional_visibility_roles_custom_field_value_url_contains'];
                                $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
                                $find = strpos($actual_link, $url_contains);

                                if ( $find !== false ) {
                                    $condition = true;
                                } else {
                                    $error++;
                                }
                            }
                        }

                        if ($conditionals_and_or == 'or') {
                            if ($conditionals_count <= $error) {
                                $condition2 = false;
                            }
                        }
                        if ($conditionals_and_or == 'and') {
                            if ($error != 0) {
                                $condition2 = false;
                            }
                        }
                    }
                }
            }

            //Conditional Visibility by date and time
            $time_should_render = null;

            if ( 'yes' == $settings['pafe_conditional_visibility_by_date_and_time'] ){
                $time_should_render = false;
                $repeater_for_time =  $settings['pafe_conditional_visibility_time_repeater'];
                $repeater_results = [];
                foreach ( $repeater_for_time as $repeater_item ) {
                    $repeater_results[] = $this->check_pafe_conditional_visibility_time_repeater($repeater_item);
                }
                $time_conditional = false;
                if ($settings['pafe_conditional_visibility_date_and_time_operators'] == 'and' && !in_array(false, $repeater_results)) {
                    $time_conditional = true;
                }
                if ($settings['pafe_conditional_visibility_date_and_time_operators'] == 'or' && in_array(true, $repeater_results)) {
                    $time_conditional = true;
                }

                $time_shows = $settings['pafe_conditional_visibility_action_for_date_and_time'];
                if ($time_conditional) {
                    if ($time_shows == 'show') {
                        $time_should_render = true;
                    } else if ($time_shows == 'hide') {
                        return false;
                    }
                } else {
                    if ($time_shows == 'show') {
                        return false;
                    } else if ($time_shows == 'hide') {
                        $time_should_render = true;
                    }
                }
            }

            if ((($condition2 == true && $show == 'show') || ($condition2 == false && $show == 'hide')) && ($time_should_render == null || $time_should_render == true)) {
                return $should_render;
            } else {
                return false;
            }
        } else {
            return $should_render;
        }
    }
    protected function init_control() {
        add_action( 'elementor/element/section/pafe_support_section/after_section_end',[ $this, 'pafe_register_controls' ], 10, 2 );
        add_action( 'elementor/element/container/pafe_support_section/after_section_end',[ $this, 'pafe_register_controls' ], 10, 2 );
        add_action( 'elementor/element/column/pafe_support_section/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
        add_action( 'elementor/element/common/pafe_support_section/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );

        add_filter( 'elementor/frontend/section/should_render', [ $this, 'should_render' ] , 10, 2 );
        add_filter( 'elementor/frontend/container/should_render', [ $this, 'should_render' ] , 10, 2 );
        add_filter( 'elementor/frontend/column/should_render', [ $this, 'should_render' ] , 10, 2 );
        add_filter( 'elementor/frontend/widget/should_render', [ $this, 'should_render' ] , 10, 2 );
        add_filter( 'elementor/frontend/repeater/should_render', [ $this, 'should_render' ] , 10, 2 );
    }

    private function check_pafe_conditional_visibility_time_repeater($repeater_item) {
        $date_start = strtotime($repeater_item['pafe_conditional_visibility_start_date']);
        $date_end = strtotime($repeater_item['pafe_conditional_visibility_end_date']);
        $time_end = strtotime($repeater_item['pafe_conditional_visibility_time_end']);
        $time_start = strtotime($repeater_item['pafe_conditional_visibility_time_start']);
        $current_time = strtotime(wp_date('H:i'));
        $current_date = time();
        $chosen_days = $repeater_item['pafe_conditional_visibility_set_days_of_week'];
        $get_the_current_day_of_week = date('w');

        if ( !empty($chosen_days) && !in_array($get_the_current_day_of_week, $chosen_days)) {
            return false;
        }

        if (!empty($date_start) && $current_date < $date_start) {
            return false;
        }

        if (!empty($date_end) && $current_date > $date_end) {
            return false;
        }

        if (!empty($time_start) && $current_time < $time_start) {
            return false;
        }

        if (!empty($time_end) && $current_time > $time_end) {
            return false;
        }

        return true;
    }

}
