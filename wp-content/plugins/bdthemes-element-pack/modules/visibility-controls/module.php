<?php

namespace ElementPack\Modules\VisibilityControls;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base
{

    public function __construct()
    {
        parent::__construct();
        $this->add_actions();
        $this->register_conditions();
    }

    public function get_name()
    {
        return 'bdt-visibility-controls';
    }

    public function register_section($element)
    {
        $element->start_controls_section(
            'section_visibility_control_controls',
            [
                'tab' => Controls_Manager::TAB_ADVANCED,
                'label' => BDTEP_CP . esc_html__('Visibility Controls', 'bdthemes-element-pack') . BDTEP_UC,
            ]
        );

        $element->end_controls_section();
    }

    protected $conditions = [];
    protected $_conditions = [];
    protected $_conditional_repeater;
    protected $conditions_options = [];

    const USER_GROUP            = 'user';
    const SYSTEM_GROUP          = 'system';
    const DATE_TIME_GROUP       = 'date_time';
    const URL_GROUP             = 'url';
    const POST_GROUP            = 'post';
    const WOOCOMMERCE_GROUP     = 'woocommerce';
    const ACF_GROUP             = 'acf';
    const MISC_GROUP             = 'misc';

    public function get_groups() {
        return [
            self::USER_GROUP => [
                'label' => __( 'User', 'bdthemes-element-pack' ),
            ],
            self::SYSTEM_GROUP => [
                'label' => __( 'System', 'bdthemes-element-pack' ),
            ],
            self::DATE_TIME_GROUP => [
                'label' => __( 'Date & Time', 'bdthemes-element-pack' ),
            ],
            self::URL_GROUP => [
                'label' => __( 'URL', 'bdthemes-element-pack' ),
            ],
            self::POST_GROUP => [
                'label' => __( 'Post', 'bdthemes-element-pack' ),
            ],
            self::WOOCOMMERCE_GROUP => [
                'label' => __( 'WooCommerce', 'bdthemes-element-pack' ),
            ],
            self::ACF_GROUP => [
                'label' => __( 'Advanced Custom Fields', 'bdthemes-element-pack' ),
            ],
            self::MISC_GROUP => [
                'label' => __( 'Misc', 'bdthemes-element-pack' ),
            ],
        ];
    }

    public function register_conditions()
    {

        $included_conditions = [
            'authentication',
            'user',
            'role',
            'post',
            'post_type',
            'static_page',
            'date',
            'date_time_before',
            'time',
            'day',
            'os',
            'browser',
            'ex_url',
            'url_parameters',
            'url_string',
            'search_engine_url',
            'visit_count',
            'session_count',
            'language',
            'country',
            'shortcode'
        ];

        if ( class_exists( 'WooCommerce' ) ) {				
            array_push($included_conditions,
                'products_in_cart',
                'categories_in_cart',
                'tags_in_cart',
                'cart_item_number',
                'cart_subtotal_price',
                'first_purchased_date',
                'purchased_date',
                'last_purchased_date',
                'purchased_item_number',
                'orders_placed',
				'single_product_price',
				'single_product_stock',
				'single_product_category',
				'single_product_downloadable',
				'single_product_virtual',
                'single_product_featured',
                'single_product_backorder',
                'single_product_onsale',
                'single_product_sold_individually',
                'single_product_type'
            );
        }
        if ( class_exists( 'ACF' ) ) {				
            array_push($included_conditions,
                'acf_boolean',
                'acf_choice',
                'acf_text'
            );
        }

        foreach ($included_conditions as $condition_name) {

            $class_name = str_replace('-', ' ', $condition_name);
            $class_name = str_replace(' ', '', ucwords($class_name));
            $class_name = __NAMESPACE__ . '\\Conditions\\' . $class_name;

            if (class_exists($class_name)) {
                if ($class_name::is_supported()) {
                    $this->_conditions[$condition_name] = $class_name::instance();
                }
            }
        }
    }

    /**
     * Get the condition name from here
     * @param null $condition_name
     * @return array|mixed|null
     */
    public function get_conditions($condition_name = null)
    {
        if ($condition_name) {
            if (isset($this->_conditions[$condition_name])) {
                return $this->_conditions[$condition_name];
            }

            return null;
        }

        return $this->_conditions;
    }

    /**
     * Set condition as per control checking
     * @param string $id widget ID
     * @param array $conditions conditions repeater items
     */
    protected function set_conditions($id, $conditions = [])
    {

        if (!$conditions) {
            return;
        }

        foreach ($conditions as $index => $condition) {

            $key            = '';
            $key            = $condition['ep_condition_key'];

            $relation       = $condition['ep_condition_operator'];
            $val            = $condition['ep_condition_' . $key . '_value'];

            $custom_page_id = $condition['ep_condition_custom_page_id'];
            $addition_operator = $condition['ep_condition_addition_operator'];
            $extra          = isset($condition['ep_condition_' . $key . '_name']) ? $condition['ep_condition_' . $key . '_name'] : ''; 

            $_condition = $this->get_conditions($key);

            if (!$_condition) {
                continue;
            }

            $_condition->set_element_id($id);

            $check = $_condition->check($relation, $val, $custom_page_id, $extra, $addition_operator);

            $this->conditions[$id][$key . '_' . $condition['_id']] = $check;
        }
    }

    public function register_controls($widget, $args)
    {

        $widget->add_control(
            'ep_display_conditions_enable',
            [
                'label' => esc_html__('Display Conditions', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__('Yes', 'bdthemes-element-pack'),
                'label_off' => esc_html__('No', 'bdthemes-element-pack'),
                'return_value' => 'yes',
                'frontend_available' => true,
            ]
        );

        $widget->add_control(
            'ep_display_conditions_to',
            [
                'label' => esc_html__('To', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'show',
                'options' => [
                    'show' => esc_html__('Show', 'bdthemes-element-pack'),
                    'hide' => esc_html__('Hide', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'ep_display_conditions_enable' => 'yes',
                ],
            ]
        );

        $widget->add_control(
            'ep_display_conditions_relation',
            [
                'label' => esc_html__('When', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'all',
                'options' => [
                    'all' => esc_html__('All conditions met', 'bdthemes-element-pack'),
                    'any' => esc_html__('Any condition met', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'ep_display_conditions_enable' => 'yes',
                ],
            ]
        );

        $this->_conditional_repeater = new Repeater();

        $this->_conditional_repeater->add_control(
            'ep_condition_key',
            [
                'label'          => __( 'Select a condition', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'authentication',
                'label_block' => true,
                'groups' => $this->get_conditions_options(),
            ]
        );

        $this->add_name_controls();

        $this->_conditional_repeater->add_control(
            'ep_condition_operator',
            [
                'type' => Controls_Manager::SELECT,
                'default' => 'is',
                'label_block' => true,
                'options' => [
                    'is' => esc_html__('Is', 'bdthemes-element-pack'),
                    'not' => esc_html__('Is not', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->_conditional_repeater->add_control(
            'ep_condition_addition_operator',
            [
                'type'        => Controls_Manager::SELECT,
                'default'     => 'equal',
                'options' => [
                    'equal' => esc_html__('Equal to', 'bdthemes-element-pack'),
                    'greater_or_equal' => esc_html__('Greater than or Equal to', 'bdthemes-element-pack'),
                ],
                'label_block' => true,
                'condition' => [
                    'ep_condition_key'               => 'orders_placed',
                ],
            ]
        );

        $this->add_value_controls();

        $this->_conditional_repeater->add_control(
            'ep_condition_custom_page_id',
            [
                'label'       => esc_html__( 'Custom Page ID', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::NUMBER,
                'default'     => '10',
                'label_block' => false,
                'condition' => [
                    'ep_condition_key'               => 'static_page',
                    'ep_condition_static_page_value' => 'custom',
                ],
            ]
        );

        $widget->add_control(
            'ep_display_conditions',
            [
                'label' => esc_html__('Conditions', 'bdthemes-element-pack'),
                'type' => Controls_Manager::REPEATER,
                'prevent_empty' => false,
                'condition' => [
                    'ep_display_conditions_enable' => 'yes',
                ],
                'fields' => $this->_conditional_repeater->get_controls(),
                'title_field' => 'Condition - <# print(ep_condition_key.replace(/_/i, " ").split(" ").map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(" ")) #>',
            ]
        );
    }

    /**
     * Add control name for repeater
     */
    private function add_name_controls()
    {
        if (!$this->_conditions) {
            return;
        }

        foreach ($this->_conditions as $_condition) {

            if (false === $_condition->get_name_control()) {
                continue;
            }

            $condition_name = $_condition->get_name();
            $ctrl_key = 'ep_condition_' . $condition_name . '_name';
            $ctrl_settings = $_condition->get_name_control();

            // Show this only if the user select this specific condition
            $ctrl_settings['condition'] = [
                'ep_condition_key' => $condition_name,
            ];

            $this->_conditional_repeater->add_control($ctrl_key, $ctrl_settings);
        }
    }

    /**
     * Add controls values from here
     */
    private function add_value_controls()
    {
        if (!$this->_conditions) {
            return;
        }

        foreach ($this->_conditions as $_condition) {

            $condition_name = $_condition->get_name();
            $ctrl_key = 'ep_condition_' . $condition_name . '_value';
            $ctrl_settings = $_condition->get_control_value();

            // Show this only if the user select this specific condition
            $ctrl_settings['condition'] = [
                'ep_condition_key' => $condition_name,
            ];

            $this->_conditional_repeater->add_control($ctrl_key, $ctrl_settings);
        }
    }

    /**
     * Get the condition options from here
     * @return array
     */
    private function get_conditions_options()
    {

		$groups = $this->get_groups(); // for grouping condtion

        foreach ($this->_conditions as $_condition) {
            $groups[ $_condition->get_group() ]['options'][ $_condition->get_name() ] = $_condition->get_title(); // for grouping condtion
        }

        return $groups;
    }

    protected function is_visible($id, $relation)
    {

        if (!array_key_exists($id, $this->conditions)) {
            return false;
        }

        if (!Plugin::$instance->editor->is_edit_mode()) {
            if ('any' === $relation) {
                if (!in_array(true, $this->conditions[$id])) {
                    return false;
                }
            } else {
                if (in_array(false, $this->conditions[$id])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Final condition renderer which will show/hide as per conditions needs
     * @param $should_render
     * @param $widget
     * @return bool|mixed
     */
    public function schedule_before_render($should_render, $widget)
    {

        $settings = $widget->get_settings();

        if (!empty($settings['ep_display_conditions_enable']) && 'yes' === $settings['ep_display_conditions_enable']) {

            $this->set_conditions($widget->get_id(), $settings['ep_display_conditions']); // set condition

            $check_conditions = $this->is_visible($widget->get_id(), $settings['ep_display_conditions_relation']); // check condition
            $to = $settings['ep_display_conditions_to'];

            if (('show' === $to && true === $check_conditions) || ('hide' === $to && false === $check_conditions)) {
                $should_render = true;
            } else if (('show' === $to && false === $check_conditions) || ('hide' === $to && true === $check_conditions)) {
                $should_render = false;
            }
        }

        return $should_render;
    }

    protected function add_actions()
    {

        add_action('elementor/element/common/_section_style/after_section_end', [$this, 'register_section']);
        add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'register_section']);

        add_action('elementor/element/common/section_visibility_control_controls/before_section_end', [$this, 'register_controls'], 10, 2);
        add_action('elementor/element/section/section_visibility_control_controls/before_section_end', [$this, 'register_controls'], 10, 2);

        add_action('elementor/frontend/section/should_render', [$this, 'schedule_before_render'], 10, 2);
        add_filter('elementor/frontend/widget/should_render', [$this, 'schedule_before_render'], 10, 2);


        add_action('elementor/element/container/section_layout/after_section_end', [$this, 'register_section']);
        add_action('elementor/element/container/section_visibility_control_controls/before_section_end', [$this, 'register_controls'], 10, 2);
        add_action('elementor/frontend/container/should_render', [$this, 'schedule_before_render'], 10, 2);

    }
}
