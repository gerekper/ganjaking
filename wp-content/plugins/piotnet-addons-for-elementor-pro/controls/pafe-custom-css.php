<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class PAFE_Custom_Css extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_controls();
	}

	public function get_name() {
		return 'pafe-custom-css';
	}

    public function init_controls() {
        add_action('elementor/element/common/_section_responsive/after_section_end', [$this, 'pafe_register_controls'], 10, 2);
        add_action('elementor/element/section/_section_responsive/after_section_end', [$this, 'pafe_register_controls'], 10, 2);
        add_action('elementor/element/column/_section_responsive/after_section_end', [$this, 'pafe_register_controls'], 10, 2);
        add_action('elementor/element/container/_section_responsive/after_section_end', [$this, 'pafe_register_controls'], 10, 2);

        add_action('elementor/element/parse_css', [$this, 'add_post_css'], 10, 2);
        add_action('elementor/css-file/post/parse', [$this, 'add_page_settings_css']);
    }

    public function pafe_register_controls($element, $section_id) {

        if (!current_user_can('edit_pages') && !current_user_can('unfiltered_html')) {
            return;
        }

        $element->start_controls_section(
            'pafe_custom_css_section',
            [
                'label' => esc_html__('Custom CSS by PAFE', 'pafe'),
                'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
            ]
        );

        $element->add_control(
            'pafe_custom_css',
            [
                'label' => esc_html__('Custom CSS', 'pafe'),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'css',
                'render_type' => 'ui',
                'separator' => 'none',
                'description' => esc_html__('Use "selector" to target the element wrapper. For example: selector {font-size: 18px} or selector .child {font-size: 20px}', 'pafe'),
            ]
        );
        
        $element->end_controls_section();
    }

    public function add_post_css($post_css, $element) {
        if ($post_css instanceof \Elementor\Core\DynamicTags\Dynamic_CSS) {
            return;
        }

        $element_settings = $element->get_settings();

        $sanitize_css = $this->parse_css($element_settings, $post_css->get_element_unique_selector($element));

        $post_css->get_stylesheet()->add_raw_css($sanitize_css);
    }

    public function add_page_settings_css($post_css) {

        $document = \Elementor\Plugin::instance()->documents->get($post_css->get_post_id());

        $element_settings = $document->get_settings();

        $sanitize_css = $this->parse_css($element_settings, $document->get_css_wrapper_selector());

        $post_css->get_stylesheet()->add_raw_css($sanitize_css);
    }

    public function parse_css($element_settings, $unique_selector) {

        if (empty($element_settings['pafe_custom_css'])) {
            return;
        }

        $custom_css = trim($element_settings['pafe_custom_css']);

        if (empty($custom_css)) {
            return;
        }

        $custom_css = str_replace('selector', $unique_selector, $custom_css);

        return wp_strip_all_tags($custom_css);
    }
  
}