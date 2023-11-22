<?php

namespace ElementPack\Modules\WrapperLink;

use Elementor\Controls_Manager;
use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base{

	public function __construct()
	{
		parent::__construct();
		$this->add_actions();
	}

	public function get_name()
	{
		return 'bdt-wrapper-link';
	}

	public function register_section($element)
	{

		if ('section' === $element->get_name() || 'column' === $element->get_name() || 'container' === $element->get_name()) {
			$tabs = Controls_Manager::TAB_LAYOUT;
		} else {
			$tabs = Controls_Manager::TAB_CONTENT;
		}

		$element->start_controls_section(
			'section_element_pack_wrapper_link',
			[
				'tab'   => $tabs,
				'label' => BDTEP_CP . esc_html__('Wrapper Link', 'bdthemes-element-pack'),
			]
		);

		$element->end_controls_section();
	}


	public function register_controls($widget, $args)
	{
		$widget->add_control(
			'element_pack_wrapper_link',
			[
				'label'              => esc_html__('Link', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::URL,
				'placeholder'        => esc_html__('https://example.com', 'bdthemes-element-pack'),
				'show_external'      => true,
				'default'            => ['url' => ''],
				'dynamic'            => ['active' => true],
				'render_type'        => 'none',
			]
		);
	}


	public function wrapper_link_before_render($widget)
	{
		$element_link = $widget->get_settings_for_display('element_pack_wrapper_link');

		if ($element_link && !empty($element_link['url'])) {
			$widget->add_render_attribute(
				'_wrapper',
				[
					'data-ep-wrapper-link' => json_encode($element_link),
					'style' => 'cursor: pointer',
					'class' => 'bdt-element-link'
				]
			);
		}
	}

	public function enqueue_scripts()
	{
		wp_enqueue_script('ep-wrapper-link');
	}

	public function should_script_enqueue($widget)
	{
		$element_link = $widget->get_settings_for_display('element_pack_wrapper_link');

		if ($element_link && !empty($element_link['url'])) {
			$this->enqueue_scripts();
		}
	}

	protected function add_actions()
	{

		// Add container settings
		add_action('elementor/element/container/section_layout_container/after_section_end', [$this, 'register_section']);
		add_action('elementor/element/container/section_element_pack_wrapper_link/before_section_end', [$this, 'register_controls'], 10, 2);
		add_action('elementor/frontend/container/after_render', [$this, 'should_script_enqueue']);


		// Add section settings
		add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'register_section']);
		add_action('elementor/element/section/section_element_pack_wrapper_link/before_section_end', [$this, 'register_controls'], 10, 2);

		// Add column settings
		add_action('elementor/element/column/section_advanced/after_section_end', [$this, 'register_section']);
		add_action('elementor/element/column/section_element_pack_wrapper_link/before_section_end', [$this, 'register_controls'], 10, 2);

		// Add widget settings
		add_action('elementor/element/common/_section_style/after_section_end', [$this, 'register_section']);
		add_action('elementor/element/common/section_element_pack_wrapper_link/before_section_end', [$this, 'register_controls'], 10, 2);


		add_action('elementor/frontend/before_render', [$this, 'wrapper_link_before_render'], 10, 1);

		add_action('elementor/frontend/before_render', [$this, 'should_script_enqueue']);
	}
}
