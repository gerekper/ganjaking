<?php

namespace Happy_Addons_Pro;

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Settings\Manager as Settings_Manager;
use Elementor\Core\Settings\General\HA_General_Settings as General_Settings;

defined('ABSPATH') || die();

class Live_Copy {

	const OPTION_KEY = 'ha_global_options';

	const PREFIX = '_ha_live_copy_';

	static $should_script_enqueue = false;

	public static function init() {
		add_action('wp_footer', [__CLASS__, 'enqueue_scripts']);

		// TODO: Analycis the actual task of 'register_manager' methods (since Elementor V3.6.0)
		// add_action( 'elementor/init', [ __CLASS__, 'register_manager' ] );

		add_action('wp_ajax_get_section_data', [__CLASS__, 'get_section_data']);
		add_action('wp_ajax_nopriv_get_section_data', [__CLASS__, 'get_section_data']);

		add_action('elementor/frontend/section/before_render', [__CLASS__, 'should_script_enqueue']);
		add_action('elementor/frontend/container/before_render', [__CLASS__, 'should_script_enqueue']);

		// TODO: Remove if not necessary (since Elementor V3.6.0)
		// add_action( 'elementor/element/global-settings/style/after_section_end', [ __CLASS__, 'register_style_controls' ] );

		add_action('elementor/element/section/_section_happy_pro_features/after_section_start', [__CLASS__, 'register_section_controls']);
		add_action('elementor/element/container/_section_happy_pro_features/after_section_start', [__CLASS__, 'register_section_controls']);
	}

	public static function register_manager() {
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'classes/elementor-manager-general.php');

		Settings_Manager::add_settings_manager(new General_Settings());
	}

	/**
	 * Set should_script_enqueue based on live copy setting
	 *
	 * @param Element_Base $section
	 * @return void
	 */
	public static function should_script_enqueue(Element_Base $section) {
		if (self::$should_script_enqueue) {
			return;
		}

		if ('yes' == $section->get_settings_for_display('_ha_enable_live_copy')) {
			self::$should_script_enqueue = true;
			remove_action('elementor/frontend/section/before_render', [__CLASS__, 'should_script_enqueue']);
			remove_action('elementor/frontend/container/before_render', [__CLASS__, 'should_script_enqueue']);
		}
	}

	// TODO: Remove if not necessary (since Elementor V3.6.0)
	public static function register_style_controls($panel) {
		$panel->start_controls_section(
			'_ha_live_copy_button_style',
			[
				'label' => __('Live Copy Button', 'happy-addons-pro') . ha_get_section_icon(),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$panel->add_control(
			'ha_live_copy_preview',
			[
				'label' => __('Show Preview', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',

			]
		);

		$panel->add_control(
			'_ha_enable_live_copy_notice',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __('You can see live copy button in every section but it is only for preview and style purpose. You can enable live copy individually in evey section by going to section edit panel <code>Advanced > Happy Features</code>.', 'happy-addons-pro'),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => [
					'ha_live_copy_preview' => 'yes',
				]
			]
		);

		$panel->add_responsive_control(
			'_ha_live_copy_btn_padding',
			[
				'label' => __('Padding', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'separator' => 'before',
				'selectors' => [
					'.elementor .ha-live-copy-wrap .ha-live-copy-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$panel->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => '_ha_live_copy_btn_border',
				'selector' => '.elementor .ha-live-copy-wrap .ha-live-copy-btn',
			]
		);

		$panel->add_control(
			'_ha_live_copy_btn_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'.elementor .ha-live-copy-wrap .ha-live-copy-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$panel->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => '_ha_live_copy_btn_box_shadow',
				'selector' => '.elementor .ha-live-copy-wrap .ha-live-copy-btn',
			]
		);

		$panel->start_controls_tabs('_ha_live_copy_btn_tabs');

		$panel->start_controls_tab(
			'_ha_live_copy_btn_tab_normal',
			[
				'label' => __('Normal', 'happy-addons-pro'),
			]
		);

		$panel->add_control(
			'_ha_live_copy_btn_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.elementor .ha-live-copy-wrap .ha-live-copy-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$panel->add_control(
			'_ha_live_copy_btn_bg_color',
			[
				'label' => __('Background Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.elementor .ha-live-copy-wrap .ha-live-copy-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$panel->end_controls_tab();

		$panel->start_controls_tab(
			'_ha_live_copy_btn_tab_hover',
			[
				'label' => __('Hover', 'happy-addons-pro'),
			]
		);

		$panel->add_control(
			'_ha_live_copy_btn_hover_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.elementor .ha-live-copy-wrap .ha-live-copy-btn:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$panel->add_control(
			'_ha_live_copy_btn_hover_bg_color',
			[
				'label' => __('Background Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.elementor .ha-live-copy-wrap .ha-live-copy-btn:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$panel->add_control(
			'_ha_live_copy_btn_hover_border_color',
			[
				'label' => __('Border Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'_ha_live_copy_btn_border_border!' => '',
				],
				'selectors' => [
					'.elementor .ha-live-copy-wrap .ha-live-copy-btn:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$panel->end_controls_tab();
		$panel->end_controls_tabs();

		$panel->end_controls_section();
	}

	/**
	 * Register controls
	 *
	 * @param Element_Base $section
	 * @return void
	 */
	public static function register_section_controls(Element_Base $section) {
		$section->add_control(
			'_ha_enable_live_copy',
			[
				'label' => __('Enable Live Copy', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
				'render_type' => 'none'
			]
		);
	}

	protected static function add_button() {
?>
		<div id="ha-live-copy-base" class="ha-live-copy-wrap" style="display: none">
			<a class="ha-live-copy-btn" href="#" class="" target="_blank"><?php echo esc_html('Live Copy', 'happy-addons-pro'); ?></a>
		</div>
	<?php
	}

	protected static function add_inline_style() {
	?>
		<style>
			.elementor-section-wrap>.elementor-section,
			.elementor-section.elementor-top-section,
			.e-container,
			.e-con {
				position: relative
			}
			.elementor-section-wrap .ha-live-copy-wrap,
			.elementor-section.elementor-top-section .ha-live-copy-wrap,
			.e-container > .ha-live-copy-wrap,
			.e-con > .ha-live-copy-wrap {
				position: absolute;
				top: 50%;
				right: 0px;
				z-index: 99999;
				display: none;
				text-decoration: none;
				font-size: 15px;
				-webkit-transform: translateY(-50%);
				-ms-transform: translateY(-50%);
				transform: translateY(-50%)
			}
			.ha-live-copy-wrap .ha-live-copy-btn {
				display: block;
				padding: 10px 20px 10px 25px;
				border-radius: 30px 0 0 30px;
				background: #6b4ff4;
				color: #fff;
				line-height: 1;
				-webkit-transition: all 0.2s;
				transition: all 0.2s
			}

			.ha-live-copy-wrap .ha-live-copy-btn:hover {
				background: #e2498a
			}
			.elementor-section-wrap>.elementor-section.live-copy-preview .ha-live-copy-wrap,
			.elementor-section.elementor-top-section.live-copy-preview .ha-live-copy-wrap,
			.elementor-section-wrap>.elementor-section:not(.elementor-element-edit-mode):hover .ha-live-copy-wrap,
			.elementor-section.elementor-top-section:not(.elementor-element-edit-mode):hover .ha-live-copy-wrap,
			.e-container:not(.elementor-element-edit-mode):hover .ha-live-copy-wrap,
			.e-con:not(.elementor-element-edit-mode):hover .ha-live-copy-wrap {
				display: block
			}
		</style>
<?php
	}

	public static function enqueue_scripts() {
		if (ha_elementor()->preview->is_preview_mode()) {
			self::add_inline_style();
			self::add_button();
			return;
		}

		if (self::$should_script_enqueue) {
			self::add_inline_style();
			self::add_button();

			wp_enqueue_script(
				'live-copy',
				HAPPY_ADDONS_PRO_ASSETS . 'admin/js/live-copy.min.js',
				[ 'jquery' ],
				HAPPY_ADDONS_PRO_VERSION,
				true
			);

			wp_localize_script(
				'live-copy',
				'livecopy',
				[
					'storagekey' => md5('LICENSE KEY'),
					'ajax_url' => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce('get_section_data'),
				]
			);
		}
	}

	public static function get_section_data() {
		/**
		 * This check doesn't need any conditional block
		 * when 3rd parameter (die) is true.
		 */
		check_ajax_referer('get_section_data', 'nonce');

		$post_id = isset($_GET['post_id']) ? absint($_GET['post_id']) : 0;
		$section_id = isset($_GET['section_id']) ? sanitize_text_field($_GET['section_id']) : 0;
		$elType = isset($_GET['elType']) ? sanitize_text_field($_GET['elType']) : '';

		if (empty($post_id) || empty($section_id)) {
			wp_send_json_error('Incomplete request');
		}

		// $is_built_with_elementor = ha_elementor()->db->is_built_with_elementor($post_id);
		$is_built_with_elementor = ha_elementor()->documents->get( $post_id )->is_built_with_elementor();

		if (!$is_built_with_elementor) {
			wp_send_json_error('Not built with elementor');
		}

		$document = ha_elementor()->documents->get($post_id);
		$elementor_data = $document ? $document->get_elements_data() : [];
		$data = [];

		if (!empty($elementor_data)) {
			$data = wp_list_filter($elementor_data, [
				'id' => $section_id,
				'elType' => $elType,
				// 'elType' => 'section',
			]);

			$data = current($data);

			if (empty($data)) {
				wp_send_json_error('Section not found');
			}
		}

		wp_send_json_success($data);
	}

	public static function get_control_settings($settings = []) {
		$options = get_option(self::OPTION_KEY, []);

		if (!empty($options)) {
			foreach ($options as $key => $value) {
				$settings[self::PREFIX . $key] = $value;
			}
		}

		return $settings;
	}

	public static function save_control_settings($settings, $id = 0) {
		$options = [];

		foreach ($settings as $key => $value) {
			if (strpos($key, self::PREFIX) === 0) {
				$options[str_replace(self::PREFIX, '', $key)] = $value;
			}
		}

		if (!empty($options)) {
			update_option(self::OPTION_KEY, $options, 'no');
		} else {
			delete_option(self::OPTION_KEY);
		}
	}
}

Live_Copy::init();
