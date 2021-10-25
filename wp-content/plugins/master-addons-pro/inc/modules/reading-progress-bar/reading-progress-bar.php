<?php

namespace MasterAddons\Modules;

// Elementor Classes
use \Elementor\Plugin;
use \Elementor\Controls_Manager;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 10/12/19
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Master Addons: Content Reading Progress bar & Scroll Indicator
 */
class Extension_Reading_Progress_Bar
{

	private static $_instance = null;

	public function __construct()
	{
		add_action('elementor/documents/register_controls', [$this, 'jltma_rpb_register_controls'], 10);
		add_action('wp_footer', [$this, 'jltma_reading_progress_bar_render']);
	}

	public function jltma_rpb_register_controls($element)
	{

		$element->start_controls_section(
			'jltma_reading_progress_bar_section',
			[
				'tab' 			=> Controls_Manager::TAB_SETTINGS,
				'label' 		=> MA_EL_BADGE . esc_html__(' Reading Progress Bar', MELA_TD)
			]
		);

		$element->add_control(
			'jltma_enable_reading_progress_bar',
			[
				'type'  		=> Controls_Manager::SWITCHER,
				'label' 		=> esc_html__('Enable Reading Progress Bar', MELA_TD),
				'default' 		=> '',
				'label_on' 		=> esc_html__('Yes', MELA_TD),
				'label_off' 	=> esc_html__('No', MELA_TD),
				'return_value' 	=> 'yes'
			]
		);


		$element->add_control(
			'jltma_reading_progress_bar_position',
			[
				'label' 		=> esc_html__('Position', MELA_TD),
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'top',
				'label_block' 	=> false,
				'options' 		=> [
					'top' 		=> esc_html__('Top', MELA_TD),
					'bottom' 	=> esc_html__('Bottom', MELA_TD),
				],
				'condition' 	=> [
					'jltma_enable_reading_progress_bar' => 'yes',
				],

				'selectors' => [
					'.ma-el-page-scroll-indicator.bottom' => 'top:inherit !important; bottom:0;',
					'.ma-el-page-scroll-indicator.top' => 'top:0px;',
					'.logged-in.admin-bar .ma-el-page-scroll-indicator.top' => 'top:32px;',
				],

			]
		);


		$element->add_control(
			'jltma_reading_progress_bar_height',
			[
				'label' => esc_html__('Height', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'.ma-el-page-scroll-indicator, .ma-el-scroll-indicator' => 'height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'jltma_enable_reading_progress_bar' => 'yes',
				],
			]
		);

		$element->add_control(
			'jltma_reading_progress_bar_bg_color',
			[
				'label' => esc_html__('Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'.ma-el-page-scroll-indicator' => 'background: {{VALUE}}',
				],
				'condition' => [
					'jltma_enable_reading_progress_bar' => 'yes',
				],
			]
		);

		$element->add_control(
			'jltma_reading_progress_bar_fill_color',
			[
				'label' => esc_html__('Fill Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#007bff',
				'selectors' => [
					'ma-el-scroll-indicator' => 'background: {{VALUE}}',
				],
				'condition' => [
					'jltma_enable_reading_progress_bar' => 'yes',
				],
			]
		);

		$element->add_control(
			'jltma_reading_progress_bar_animation_speed',
			[
				'label' => esc_html__('Animation Speed', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'.ma-el-scroll-indicator' => 'transition: width {{SIZE}}ms ease;',
				],
				'condition' => [
					'jltma_enable_reading_progress_bar' => 'yes',
				],
			]
		);

		$element->end_controls_section();
	}


	public function jltma_reading_progress_bar_styles()
	{

		if (did_action('elementor/loaded')) {

			$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers('page');
			$page_settings_model = $page_settings_manager->get_model(get_the_ID());

			$jltma_r_p_b_height  			= $page_settings_model->get_settings('jltma_reading_progress_bar_height');
			$jltma_r_p_b_bg_color  			= $page_settings_model->get_settings('jltma_reading_progress_bar_bg_color');
			$jltma_r_p_b_fill_color  		= $page_settings_model->get_settings('jltma_reading_progress_bar_fill_color');
			$jltma_r_p_b_animation_speed  	= $page_settings_model->get_settings('jltma_reading_progress_bar_animation_speed');
			$jltma_rbp_position  			= $page_settings_model->get_settings('jltma_reading_progress_bar_position');

			$jltma_r_p_b_custom_css = "";

			if ($jltma_r_p_b_bg_color != "" && $jltma_r_p_b_fill_color != "") {
				$jltma_r_p_b_custom_css = ".ma-el-page-scroll-indicator{ background: {$jltma_r_p_b_bg_color};}
					.ma-el-scroll-indicator{ background: {$jltma_r_p_b_fill_color};}
					.ma-el-page-scroll-indicator, .ma-el-scroll-indicator{ height: {$jltma_r_p_b_height['size']}px;}";
			}

			if (isset($jltma_rbp_position) && $jltma_rbp_position != "") {
				if ($jltma_rbp_position == "top") {
					$jltma_r_p_b_custom_css .= '.ma-el-page-scroll-indicator{top:0px;}';
				} else {
					$jltma_r_p_b_custom_css .= '.ma-el-page-scroll-indicator{top:inherit !important; bottom:0;}';
				}
			}

			if (Plugin::instance()->editor->is_edit_mode() || Plugin::instance()->preview->is_preview_mode()) {
				if ($jltma_rbp_position == "top") {
					$jltma_r_p_b_custom_css .= '.ma-el-page-scroll-indicator{top:0px;}';
				} else {
					$jltma_r_p_b_custom_css .= '.ma-el-page-scroll-indicator{top:inherit !important; bottom:0;}';
				}
			}

			echo '<style>' . $jltma_r_p_b_custom_css . '</style>';
		}
	}


	public function jltma_reading_progress_bar_render()
	{

		$document = Plugin::instance()->documents->get(get_the_ID());

		if (!$document) return;

		$reading_progress_bar = $document->get_settings('jltma_enable_reading_progress_bar');

		if (empty($reading_progress_bar)) return;

		if (did_action('elementor/loaded')) {

			$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers('page');
			$page_settings_model = $page_settings_manager->get_model(get_the_ID());

			$scrollbar_position = $page_settings_model->get_settings('jltma_reading_progress_bar_position');
			$jltma_scroll_pos = ($scrollbar_position) ? $scrollbar_position : "";

			$jltma_reading_progress_bar_html = '<div class="ma-el-page-scroll-indicator ' . $jltma_scroll_pos . '">
				<div class="ma-el-scroll-indicator"></div>
			</div>';

			if ($page_settings_model->get_settings('jltma_enable_reading_progress_bar') == 'yes') {

				echo $jltma_reading_progress_bar_html;

				echo '<script>
					; (function ($) {
					    "use strict";
						window.onscroll = function () { scrollProgress() };

						function scrollProgress() {
					        var currentState = document.body.scrollTop || document.documentElement.scrollTop;
					        var pageHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
					        var scrollStatePercentage = (currentState / pageHeight) * 100;
					        document.querySelector(".ma-el-page-scroll-indicator > .ma-el-scroll-indicator").style.width = scrollStatePercentage + "%";
						}
					})(jQuery);
				</script>';
			} // Enable Progress Bar


			$jltma_r_p_b_height  			= $page_settings_model->get_settings('jltma_reading_progress_bar_height');
			$jltma_r_p_b_bg_color  			= $page_settings_model->get_settings('jltma_reading_progress_bar_bg_color');
			$jltma_r_p_b_fill_color  		= $page_settings_model->get_settings('jltma_reading_progress_bar_fill_color');
			$jltma_r_p_b_animation_speed  	= $page_settings_model->get_settings('jltma_reading_progress_bar_animation_speed');
			$jltma_rbp_position  			= $page_settings_model->get_settings('jltma_reading_progress_bar_position');

			$jltma_r_p_b_custom_css = "";

			if ($jltma_r_p_b_bg_color != "" && $jltma_r_p_b_fill_color != "") {
				$jltma_r_p_b_custom_css = ".ma-el-page-scroll-indicator{ background: {$jltma_r_p_b_bg_color};}
					.ma-el-scroll-indicator{ background: {$jltma_r_p_b_fill_color};}
					.ma-el-page-scroll-indicator, .ma-el-scroll-indicator{ height: {$jltma_r_p_b_height['size']}px;}";
			}

			if (isset($jltma_rbp_position) && $jltma_rbp_position != "") {
				if ($jltma_rbp_position == "top") {
					$jltma_r_p_b_custom_css .= '.ma-el-page-scroll-indicator{top:0px;}';
				} else {
					$jltma_r_p_b_custom_css .= '.ma-el-page-scroll-indicator{top:inherit !important; bottom:0;}';
				}
			}

			if (Plugin::instance()->editor->is_edit_mode() || Plugin::instance()->preview->is_preview_mode()) {
				if ($jltma_rbp_position == "top") {
					$jltma_r_p_b_custom_css .= '.ma-el-page-scroll-indicator{top:0px;}';
				} else {
					$jltma_r_p_b_custom_css .= '.ma-el-page-scroll-indicator{top:inherit !important; bottom:0;}';
				}
			}

			echo '<style>' . $jltma_r_p_b_custom_css . '</style>';
		}
	}


	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

Extension_Reading_Progress_Bar::instance();
