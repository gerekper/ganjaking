<?php

namespace ElementPack\Modules\HashLink;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function __construct() {
		parent::__construct();
		$this->add_actions();
	}

	public function get_name() {
		return 'bdt-hash-link';
	}

	public function register_controls($page) {

		$page->start_controls_section(
			'element_pack_hash_link_section',
			[
				'tab'   => Controls_Manager::TAB_SETTINGS,
				'label' => BDTEP_CP . esc_html__('Hash Link', 'bdthemes-element-pack'),
			]
		);

		$page->add_control(
			'ep_hash_link_enable',
			[
				'label'       => esc_html__('Hash Link?', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
			]
		);

		$page->add_control(
			'ep_hash_link_notes',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__('Note: This feature will not work on Editor Mode. Please check on the Preview Page.', 'bdthemes-element-pack'),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'ep_hash_link_enable' => 'yes'
				],
			]
		);

		$page->add_control(
			'ep_hash_link_container',
			[
				'label'   => esc_html__('Container Class', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => '.elementor',
				'label_block' => true,
				'condition'   => [
					'ep_hash_link_enable' => 'yes'
				],
			]
		);

		$page->add_control(
			'ep_hash_link_selector',
			[
				'label'    => esc_html__('Selector', 'bdthemes-element-pack'),
				'type'     => Controls_Manager::SELECT2,
				'multiple' => true,
				'options'  => [
					'h1' => esc_html__('h1', 'bdthemes-element-pack'),
					'h2' => esc_html__('h2', 'bdthemes-element-pack'),
					'h3' => esc_html__('h3', 'bdthemes-element-pack'),
					'h4' => esc_html__('h4', 'bdthemes-element-pack'),
					'h5' => esc_html__('h5', 'bdthemes-element-pack'),
					'h6' => esc_html__('h6', 'bdthemes-element-pack'),
				],
				'condition' => [
					'ep_hash_link_enable' => 'yes'
				],
			]
		);

		$page->add_control(
			'ep_hash_link_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.ep-hash-link .ep-hash-link-inner-el' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'ep_hash_link_enable' => 'yes'
				],
			]
		);

		$page->add_control(
			'ep_hash_link_color_hover',
			[
				'label'     => esc_html__('Color Hover', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.ep-hash-link:hover .ep-hash-link-inner-el' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'ep_hash_link_enable' => 'yes'
				],
			]
		);

		$page->end_controls_section();
	}

	public function should_script_enqueue() {

		if (Plugin::instance()->editor->is_edit_mode() || Plugin::instance()->preview->is_preview_mode()) {
			return;
		}

		$document = Plugin::instance()->documents->get(get_the_ID());

		if (!$document) {
			return;
		}

		$hash_link_enable = $document->get_settings('ep_hash_link_enable');

		if ('yes' !== $hash_link_enable) {
			return;
		}

		$container = $document->get_settings('ep_hash_link_container');

		if (empty($container)) {
			$container = '.elementor';
		}

		$selector = $document->get_settings('ep_hash_link_selector');

		if (empty($selector)) {
			return;
		}

		$selector = implode(", ", $selector);

		$data = [
			'container' => $container,
			'selector'  => $selector,
			'new_tab'   => 'yes',
		];

?>
		<div id="ep-hash-link" data-settings='<?php echo wp_json_encode($data); ?>' style="display:none;"></div>
<?php
		$suffix	= defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script('ep-hash-link', BDTEP_ASSETS_URL . '/js/modules/ep-hash-link' . $suffix . '.js', ['jquery'], BDTEP_VER, true);
	}

	protected function add_actions() {
		add_action('elementor/documents/register_controls', [$this, 'register_controls'], 1, 1);
		add_action('wp_body_open', [$this, 'should_script_enqueue']);
	}
}
