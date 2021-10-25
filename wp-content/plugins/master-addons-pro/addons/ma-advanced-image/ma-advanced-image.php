<?php

namespace MasterAddons\Addons;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;
use \Elementor\Utils;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Text_Shadow;
use \Elementor\Group_Control_Background;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 1/3/20
 */

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Advanced_Image extends Widget_Base
{

	public function get_name()
	{
		return 'jltma-advanced-image';
	}

	public function get_title()
	{
		return esc_html__('Advanced Image', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-image';
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_keywords()
	{
		return ['image', 'advanced image', 'ribbon', 'hover', 'hover image'];
	}

	public function get_help_url()
	{
		return 'https://master-addons.com/demos/advanced-image/';
	}


	protected function _register_controls()
	{
		/*
			 * Tab: Content
			 */

		$this->start_controls_section(
			'ma_el_adv_image_section',
			array(
				'label'      => __('Image', MELA_TD),
			)
		);

		$this->add_control(
			'ma_el_adv_image',
			array(
				'label'      => __('Image', MELA_TD),
				'type'       => Controls_Manager::MEDIA,
				'show_label' => false,
				'default'    => array(
					'url' => Utils::get_placeholder_image_src()
				)
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'       => 'ma_el_adv_image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
				'separator'  => 'none',
				'default'    => 'large'
			)
		);

		$this->add_control(
			'ma_el_adv_image_link',
			array(
				'label'         => __('Image Link', MELA_TD),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://your-link.com',
				'show_external' => true
			)
		);

		$this->end_controls_section();




		$this->start_controls_section(
			'ma_el_adv_image_hover_section',
			array(
				'label'      => __('Hover Image', MELA_TD),
			)
		);

		$this->add_control(
			'ma_el_adv_image_display_hover',
			array(
				'label'        => __('Display Hover Image', MELA_TD),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('On', MELA_TD),
				'label_off'    => __('Off', MELA_TD),
				'return_value' => 'yes',
				'default'      => 'no'
			)
		);

		$this->add_control(
			'ma_el_adv_image_hover_image',
			array(
				'label'      => __('Image', MELA_TD),
				'type'       => Controls_Manager::MEDIA,
				'show_label' => false,
				'condition'  => array(
					'ma_el_adv_image_display_hover' => 'yes'
				)
			)
		);

		$this->end_controls_section();



		$this->start_controls_section(
			'ma_el_adv_image_ribbon_section',
			array(
				'label'      => __('Ribbon', MELA_TD),
			)
		);

		$this->add_control(
			'ma_el_adv_image_display_ribbon',
			array(
				'label'        => esc_html__('Diplay Ribbon', MELA_TD),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__('On', MELA_TD),
				'label_off'    => esc_html__('Off', MELA_TD),
				'return_value' => 'yes',
				'default'      => 'no'
			)
		);

		$this->add_control(
			'ma_el_adv_image_ribbon_text',
			array(
				'label'       => esc_html__('Text', MELA_TD),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'NEW',
				'condition'   => array(
					'ma_el_adv_image_display_ribbon' => 'yes'
				)
			)
		);

		$this->add_control(
			'ma_el_adv_image_ribbon_style',
			array(
				'label'       => esc_html__('Ribbon Style', MELA_TD),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'simple',
				'options'     => array(
					'simple' => esc_html__('Simple', MELA_TD),
					'corner' => esc_html__('Corner', MELA_TD),
					'cross'  => esc_html__('Cross', MELA_TD)
				),
				'condition'   => array(
					'ma_el_adv_image_display_ribbon' => 'yes'
				)
			)
		);

		$this->add_control(
			'ma_el_adv_image_ribbon_position',
			array(
				'label'       => __('Ribbon Position', MELA_TD),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'top-right',
				'options'     => array(
					'top-left'     => __('Top Left', MELA_TD),
					'top-right'    => __('Top Right', MELA_TD),
					'bottom-left'  => __('Bottom Left', MELA_TD),
					'bottom-right' => __('Bottom Right', MELA_TD)
				),
				'condition'   => array(
					'ma_el_adv_image_display_ribbon' => 'yes'
				)
			)
		);

		$this->add_responsive_control(
			'ma_el_adv_image_ribbon_thickness',
			array(
				'label'      => __('Ribbon Thickness', MELA_TD),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array('px', 'em'),
				'range' => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1
					),
					'em' => array(
						'min'  => 0,
						'max'  => 3,
						'step' => 0.1
					)
				),
				'selectors'   => array(
					'{{WRAPPER}} .jltma-ribbon-wrapper' => 'line-height: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'ma_el_adv_image_display_ribbon' => 'yes'
				)
			)
		);

		$this->end_controls_section();


		/**
		 * Tab: Settings
		 */

		$this->start_controls_section(
			'ma_el_adv_image_settings_section',
			array(
				'label' => __('Settings', MELA_TD),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$this->add_control(
			'ma_el_adv_image_settings_lightbox',
			array(
				'label'        => __('Open in lightbox', MELA_TD),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('On', MELA_TD),
				'label_off'    => __('Off', MELA_TD),
				'return_value' => 'yes',
				'default'      => 'no'
			)
		);

		$this->add_control(
			'ma_el_adv_image_settings_icon',
			array(
				'label'       => __('Iconic button', MELA_TD),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'plus',
				'options'     => array(
					'none' => __('None', MELA_TD),
					'plus' => __('Plus', MELA_TD)
				),
				'condition'   => array(
					'ma_el_adv_image_settings_lightbox' => 'yes',
					'ma_el_adv_image_display_hover!' => 'yes'
				)
			)
		);


		$this->add_responsive_control(
			'ma_el_adv_image_settings_alignment',
			array(
				'label'       => __('Alignment', MELA_TD),
				'description' => __('Image alignment in block.', MELA_TD),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => array(
					'left' => array(
						'title' => __('Left', MELA_TD),
						'icon' => 'fa fa-align-left',
					),
					'none' => array(
						'title' => __('Center', MELA_TD),
						'icon' => 'fa fa-align-center',
					),
					'right' => array(
						'title' => __('Right', MELA_TD),
						'icon' => 'fa fa-align-right',
					)
				),
				'default'     => '',
				'separator'   => 'after',
				'toggle'      => true,
				'selectors'   => array(
					'{{WRAPPER}} .jltma-advanced-image' => 'float: {{VALUE}};',
				)
			)
		);


		$this->add_control(
			'ma_el_adv_image_settings_preloadable',
			array(
				'label'        => __('Preload image', MELA_TD),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('On', MELA_TD),
				'label_off'    => __('Off', MELA_TD),
				'return_value' => 'yes',
				'default'      => 'no'
			)
		);


		$this->add_control(
			'ma_el_adv_image_settings_preload_preview',
			array(
				'label'        => __('While loading image display', MELA_TD),
				'label_block'  => true,
				'type'         => Controls_Manager::SELECT,
				'options'      => Master_Addons_Helper::jltma_get_preloadable_previews(),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'ma_el_adv_image_settings_preloadable' => 'yes'
				)
			)
		);


		$this->add_control(
			'ma_el_adv_image_settings_preload_bgcolor',
			array(
				'label'     => __('Placeholder color while loading image', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'ma_el_adv_image_settings_preloadable'     => 'yes',
					'ma_el_adv_image_settings_preload_preview' => array('no', 'simple-spinner', 'simple-spinner-light', 'simple-spinner-dark')
				)
			)
		);

		$this->add_control(
			'ma_el_adv_image_tilt',
			array(
				'label'        => __('Tilt Effect', MELA_TD),
				'description'  => __('Adds tilt effect to the image.', MELA_TD),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('On', MELA_TD),
				'label_off'    => __('Off', MELA_TD),
				'return_value' => 'yes',
				'default'      => 'no',
				'separator'    => 'before'
			)
		);


		$this->add_control(
			'ma_el_adv_image_colorized_shadow',
			array(
				'label'        => __('Colorized Shadow', MELA_TD),
				'description'  => __('Adds colorized shadow to the image. Note: This feature is not available when image hover is active.', MELA_TD),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('On', MELA_TD),
				'label_off'    => __('Off', MELA_TD),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'ma_el_adv_image_display_hover!' => 'yes'
				)
			)
		);

		$this->end_controls_section();



		/*
			 * Tab: Style
			 */

		$this->start_controls_section(
			'ma_el_adv_image_style_section',
			array(
				'label'     => __('Image', MELA_TD),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'ma_el_adv_image!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'ma_el_adv_image_max_width',
			array(
				'label'      => __('Max Width', MELA_TD),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array('px', 'em', '%'),
				'range'      => array(
					'%' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1
					),
					'em' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1
					),
					'px' => array(
						'min'  => 1,
						'max'  => 1600,
						'step' => 1
					)
				),
				'selectors'          => array(
					'{{WRAPPER}} .jltma-media-image' => 'max-width:{{SIZE}}{{UNIT}};'
				)
			)
		);

		$this->add_responsive_control(
			'ma_el_adv_image_max_height',
			array(
				'label'      => __('Max Height', MELA_TD),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array('px', 'em', '%'),
				'range'      => array(
					'%' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1
					),
					'em' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1
					),
					'px' => array(
						'min'  => 1,
						'max'  => 1600,
						'step' => 1
					)
				),
				'selectors'          => array(
					'{{WRAPPER}} .jltma-media-image' => 'max-height:{{SIZE}}{{UNIT}};'
				)
			)
		);

		$this->add_responsive_control(
			'ma_el_adv_image_border_radius',
			array(
				'label'      => __('Border radius', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', 'em', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .jltma-media-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				),
				'separator' => 'after'
			)
		);

		$this->start_controls_tabs('ma_el_adv_image_style_tabs');

		$this->start_controls_tab(
			'ma_el_adv_image_status_normal',
			array(
				'label' => __('Normal', MELA_TD)
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'ma_el_adv_image_box_shadow',
				'selector'  => '{{WRAPPER}} .jltma-media-image',
				'separator' => 'none'
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'ma_el_adv_image_border',
				'selector'  => '{{WRAPPER}} .jltma-media-image',
				'separator' => 'none'
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'ma_el_adv_image_background',
				'selector'  => '{{WRAPPER}} .jltma-media-image',
				'separator' => 'none'
			)
		);

		$this->end_controls_tab();


		$this->start_controls_tab(
			'ma_el_adv_image_status_hover',
			array(
				'label' => __('Hover', MELA_TD)
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'ma_el_adv_image_box_shadow_hover',
				'selector'  => '{{WRAPPER}} .jltma-media-image:hover',
				'separator' => 'none'
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'ma_el_adv_image_border_hover',
				'selector'  => '{{WRAPPER}} .jltma-media-image:hover',
				'separator' => 'none'
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'ma_el_adv_image_background_hover',
				'selector'  => '{{WRAPPER}} .jltma-media-image:hover',
				'separator' => 'none'
			)
		);

		$this->add_control(
			'ma_el_adv_image_transition_duration',
			array(
				'label'      => __('Transition Duration', MELA_TD),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 2000,
						'step' => 10
					)
				),
				'selectors'   => array(
					'{{WRAPPER}} .jltma-media-image' => 'transition-duration: {{SIZE}}ms;',
				)
			)
		);

		$this->add_responsive_control(
			'ma_el_adv_image_translate_y',
			array(
				'label'      => __('Vertical Move', MELA_TD),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array('px', 'em', '%'),
				'range'      => array(
					'px' => array(
						'min'  => -100,
						'max'  => 100,
						'step' => 10
					)
				),
				'selectors'   => array(
					'{{WRAPPER}} .jltma-media-image:hover' => 'transform: translateY({{SIZE}}{{UNIT}});',
				)
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'ma_el_adv_ribbon_style_section',
			array(
				'label'     => __('Ribbon', MELA_TD),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'ma_el_adv_image_display_ribbon' => 'yes'
				),
			)
		);

		$this->add_control(
			'ma_el_adv_ribbon_bg_color',
			array(
				'label' => __('Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jltma-ribbon-wrapper' => 'background-color: {{VALUE}} !important;',
				)
			)
		);

		$this->add_control(
			'ma_el_adv_ribbon_border_color',
			array(
				'label' => __('Border Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jltma-ribbon-wrapper::before' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'ma_el_adv_image_ribbon_style' => array('cross'),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __('Box Shadow', MELA_TD),
				'name'     => 'ma_el_adv_header_box_shadow',
				'selector' => '{{WRAPPER}} .jltma-ribbon-wrapper'
			)
		);

		$this->add_control(
			'ma_el_adv_ribbon_text_color',
			array(
				'label' => __('Text Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jltma-ribbon-wrapper span' => 'color: {{VALUE}} !important;',
				)
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'ma_el_adv_ribbon_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .jltma-ribbon-wrapper span'
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'ma_el_adv_ribbon_text_shadow',
				'label' => __('Text Shadow', MELA_TD),
				'selector' => '{{WRAPPER}} .jltma-ribbon-wrapper span',
			)
		);

		$this->end_controls_section();
	}




	public function ma_el_is_true($var)
	{
		if (is_bool($var)) {
			return $var;
		}

		if (is_string($var)) {
			$var = strtolower($var);
			if (in_array($var, array('yes', 'on', 'true', 'checked'))) {
				return true;
			}
		}

		if (is_numeric($var)) {
			return (bool) $var;
		}

		return false;
	}


	public function ma_el_random_token($length = 32)
	{
		$length = !is_numeric($length) ? 4 : $length;
		$length = $length < 1 ? 32 : $length;

		if (function_exists('random_bytes')) {
			return bin2hex(random_bytes($length));
		}
		if (function_exists('mcrypt_create_iv')) {
			return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
		}
		if (function_exists('openssl_random_pseudo_bytes')) {
			return bin2hex(openssl_random_pseudo_bytes($length));
		}
	}

	public function ma_el_merge_css_classes($classes = array(), $class = '')
	{

		if (empty($classes) && empty($class))
			return array();

		if (!empty($class)) {
			if (!is_array($class))
				$class = preg_split('#\s+#', $class);

			$classes = array_merge($class, $classes);
		}

		return $classes;
	}



	public function ma_el_error($error)
	{
		if (WP_DEBUG && apply_filters('jltma_trigger_error_message', true)) {
			trigger_error($error);
		}
	}

	public function ma_el_get_widget_scafold($atts, $default_atts, $shortcode_content = '')
	{

		$result = array(
			'parsed_atts'   => '',
			'widget_info'   => '',
			'widget_header' => '',
			'widget_title'  => '',
			'widget_footer' => '',
			'ajax_data'     => ''
		);

		// ----
		if (!isset($default_atts['extra_classes'])) {
			$default_atts['extra_classes'] = '';
		}
		if (!isset($default_atts['custom_el_id'])) {
			$default_atts['custom_el_id'] = '';
		}
		if (!isset($default_atts['content'])) {
			$default_atts['content'] = '';
		}
		if (empty($default_atts['universal_id'])) {
			$default_atts['universal_id'] = 'au' . $this->ma_el_random_token(4);
		}
		if (!isset($default_atts['skip_wrappers'])) {
			$default_atts['skip_wrappers'] = false;
		}
		if (!isset($default_atts['loadmore_type'])) {
			$default_atts['loadmore_type'] = '';
		}
		if (!isset($default_atts['base'])) {
			$default_atts['base'] = '';
		}
		if (!isset($default_atts['content_width'])) {
			global $jltma_content_width;
			$default_atts['content_width'] = $jltma_content_width;
		}

		// animation options
		if (!isset($default_atts['inview_transition'])) {
			$default_atts['inview_transition'] = 'none';
		}
		if (!isset($default_atts['inview_duration'])) {
			$default_atts['inview_duration'] = '';
		}
		if (!isset($default_atts['inview_delay'])) {
			$default_atts['inview_delay'] = '';
		}
		if (!isset($default_atts['inview_repeat'])) {
			$default_atts['inview_repeat'] = 'no';
		}
		if (!isset($default_atts['inview_offset'])) {
			$default_atts['inview_offset'] = '';
		}

		// What called the widget fallback function
		if (!isset($default_atts['called_from'])) {
			$default_atts['called_from'] = '';
		}

		// prevent nested query while placing a recent posts widget in the same post
		if (isset($atts['exclude']) && !empty($atts['exclude'])) {
			global $post;
			if (!empty($post->ID)) {
				$atts['exclude'] .= ',' . $post->ID;
			}
		}

		// Widget general info
		$before_widget = $after_widget  = '';
		$before_title  = $after_title   = '';

		// If widget info is passed, extract them in above variables
		if (isset($atts['widget_info'])) {
			$result['widget_info'] = $atts['widget_info'];
			extract($atts['widget_info']);
		}
		// CSS class names for section -------------

		// The default CSS classes for widget container
		// Note that 'widget-container' should be in all element
		$_css_classes = array('widget-container');

		// Parse shortcode attributes
		$parsed_atts = shortcode_atts($default_atts, $atts, __FUNCTION__);

		if (empty($parsed_atts['content'])) {
			$parsed_atts['content'] = $shortcode_content;
		}
		if (empty($parsed_atts['loadmore_per_page'])) {
			$parsed_atts['loadmore_per_page'] = !empty($parsed_atts['num']) ? $parsed_atts['num'] : 12;
		}

		$result['parsed_atts'] = $parsed_atts;

		// make the result params filterable prior to generating markup variables
		$result = apply_filters('jltma_pre_widget_scafold_params', $result, $atts, $default_atts, $shortcode_content);

		if ($result['parsed_atts']['skip_wrappers']) {
			return $result;
		}

		if (!empty($result['parsed_atts']['loadmore_type'])) {

			if (empty($result['parsed_atts']["base"])) {
				_doing_it_wrong(__FUNCTION__, 'For using ajax load more feature, "base" parameter in element default attributes is required.');
			}

			// Enqueue wp-mediaelement
			wp_enqueue_style('wp-mediaelement');
			wp_enqueue_script('wp-mediaelement');

			$ajax_args = $result['parsed_atts'];

			if (isset($ajax_args['use_wp_query']) && $ajax_args['use_wp_query']) {
				$queried_object = get_queried_object();
				if ($queried_object instanceof WP_Term) {
					$ajax_args['cat'] = $queried_object->term_id;
					$ajax_args['taxonomy_name'] = $queried_object->taxonomy;
				}
			}

			// remove redundant ajax args
			unset($ajax_args['base']);
			unset($ajax_args['base_class']);
			unset($ajax_args['use_wp_query']);

			// force the element not to render wrappers for ajax handler
			$ajax_args['skip_wrappers'] = true;

			$result['ajax_data'] = array(
				'nonce'   => wp_create_nonce('jltma_front_load_more'),
				'args'    => $ajax_args,
				'handler' => $result['parsed_atts']["base"],
				'per_page' => $parsed_atts['loadmore_per_page']
			);

			$_css_classes[] = 'jltma-ajax-type-' . $result['parsed_atts']['loadmore_type'];
			if ('infinite-scroll' === $result['parsed_atts']['loadmore_type']) {
				$_css_classes[] = 'jltma-ajax-type-scroll';
			}
		}

		// Defining extra class names --------------

		// Add extra class names to class list here - widget-{element_name}
		$_css_classes[] = $result['parsed_atts']['base_class'];

		$_css_classes[] = 'jltma-parent-' . $result['parsed_atts']['universal_id'];

		$_widget_attrs  = '';
		$_widget_styles = '';

		if (!empty($result['parsed_atts']['inview_transition']) && 'none' !== $result['parsed_atts']['inview_transition']) {
			$_css_classes[] = 'jltma-appear-watch';
			$_css_classes[] = esc_attr($result['parsed_atts']['inview_transition']);

			if (!empty($result['parsed_atts']['inview_duration']) && 600 != $result['parsed_atts']['inview_duration']) {
				$_widget_styles .= 'animation-duration:'  . esc_attr(rtrim($result['parsed_atts']['inview_duration'], 'ms')) . 'ms;';
				$_widget_styles .= 'transition-duration:' . esc_attr(rtrim($result['parsed_atts']['inview_duration'], 'ms')) . 'ms;';
			}
			if (!empty($result['parsed_atts']['inview_delay'])) {
				$_widget_styles .= 'animation-delay:'  . esc_attr(rtrim($result['parsed_atts']['inview_delay'], 'ms')) . 'ms;';
				$_widget_styles .= 'transition-delay:' . esc_attr(rtrim($result['parsed_atts']['inview_delay'], 'ms')) . 'ms;';
			}
			if (!empty($result['parsed_atts']['inview_repeat']) && 'no' !== $result['parsed_atts']['inview_repeat']) {
				$_css_classes[] = 'jltma-appear-repeat';
			}
			if (!empty($result['parsed_atts']['inview_offset'])) {
				$offset = $result['parsed_atts']['inview_offset'];
				if (false === strpos($offset, '%')) {
					$offset = trim($offset, 'px') . 'px';
				}
				$_widget_attrs .= 'data-offset="' . esc_attr($offset) . '" ';
			}
		}

		$_widget_classes = $this->ma_el_merge_css_classes($_css_classes, $result['parsed_atts']['extra_classes']);
		$_widget_classes = esc_attr(trim(join(' ', array_unique($_widget_classes))));

		// Generate the opening tags for widget or shortcode element
		if ($before_widget) {

			$result['widget_header'] .= str_replace(
				array('class="', '>', '<div'),
				array('class="' . $_widget_classes . ' ', ' style="' . $_widget_styles . '" ' . $_widget_attrs . ' >', '<section'),
				$before_widget
			);
		} elseif (!empty($result['parsed_atts']['custom_el_id'])) {
			$result['widget_header'] .= sprintf('<section id="%s" class="%s" style="%s" %s>', $result['parsed_atts']['custom_el_id'], $_widget_classes, $_widget_styles, $_widget_attrs);
		} else {
			$result['widget_header'] .= sprintf('<section class="%s" style="%s" %s>', $_widget_classes, $_widget_styles, $_widget_attrs);
		}

		// Generate the title for widget or shortcode element
		if (!empty($result['parsed_atts']['title'])) {
			if ($before_title) {
				$result['widget_title'] .= $before_title . $result['parsed_atts']['title'] . $after_title;
			} elseif (!empty($result['parsed_atts']['title'])) {
				$result['widget_title'] .= '<h3 class="widget-title">' . $result['parsed_atts']['title'] . '</h3>';
			}
		}

		// Generate the close tags for widget or shortcode element
		if ($after_widget) {
			// fix for the difference in end tag in siteorigin page builder
			$result['widget_footer'] .= str_replace('</div', '</section', $after_widget);
		} else {
			$result['widget_footer'] .= '</section><!-- widget-container -->';
		}

		// Enable filtering the result variable
		$result =  apply_filters('jltma_widget_scafold_params', $result, $atts, $default_atts, $shortcode_content);

		// Prints the javascript variable if load more is enabled
		// We can modify the ajax args using "jltma_widget_scafold_params" filter
		if (!empty($result['parsed_atts']['loadmore_type'])) {
			// echo js dependencies
			$this->ma_el_print_script_object(
				"jltma.content.loadmore." . $result['parsed_atts']['universal_id'],
				$result['ajax_data']
			);
		}

		return $result;
	}


	public function ma_el_get_all_image_sizes()
	{
		global $_wp_additional_image_sizes;

		$default_image_sizes = array('thumbnail', 'medium', 'medium_large', 'large');

		$all_image_sizes = array();

		foreach ($default_image_sizes as $size) {
			$all_image_sizes[$size] = array(
				'width'  => (int)  get_option($size . '_size_w'),
				'height' => (int)  get_option($size . '_size_h'),
				'crop'   => (bool) get_option($size . '_crop')
			);
		}

		if (!empty($_wp_additional_image_sizes)) {
			$all_image_sizes = array_merge($all_image_sizes, $_wp_additional_image_sizes);
		}

		return apply_filters('jltma_all_image_sizes', $all_image_sizes);
	}


	public function ma_el_wp_get_image_size($size_name)
	{
		$all_image_sizes = $this->ma_el_get_all_image_sizes();

		if (!empty($all_image_sizes[$size_name])) {
			return $all_image_sizes[$size_name];
		}
		echo sprintf('Invalid image size name (%s) for "%s" function.', $size_name, __FUNCTION__);
		return false;
	}


	public function ma_el_print_script_object($object_name, $object_value = array())
	{

		if (empty($object_name)) {
			_doing_it_wrong(__FUNCTION__, 'The object name cannot be empty');
			return;
		}
		// remove unespected chars
		$object_name = trim($object_name, '.');

		if (false !== strpos($object_name, '.')) {
			$script = sprintf('jltmaNS("%1$s"); %1$s=%2$s;', esc_js($object_name), wp_json_encode($object_value));
		} else {
			$script = sprintf('var %1$s=%2$s;', esc_js($object_name), wp_json_encode($object_value));
		}

		echo $script ? '<script>' . $script . '</script>' : '';
	}

	/**
	 * The default WP image sizes
	 *
	 * @return array   the list of default image sizes
	 */
	public function ma_el_base_image_sizes()
	{
		return apply_filters('ma_el_base_image_sizes', array('thumbnail', 'medium', 'medium_large', 'large'));
	}


	public function ma_el_get_the_resized_attachment_src(
		$attach_id = null,
		$width = null,
		$height = null,
		$crop = null,
		$quality = 100,
		$upscale = false
	) {
		if (is_null($attach_id)) return '';


		if (is_array($attach_id)) {
			$srcs = array();

			foreach ($attach_id as $id) {

				if (wp_attachment_is('ma_el_adv_image', $id)) {
					$srcs[$id] = wp_get_attachment_url($id, 'full'); //get img URL
				} elseif (wp_attachment_is('audio', $id)) {
					$srcs[$id] = includes_url() . 'images/media/audio.png';
				} elseif (wp_attachment_is('video', $id)) {
					$srcs[$id] = includes_url() . 'images/media/video.png';
				} elseif (0 === strpos(get_post_mime_type($id), 'text/')) {
					$srcs[$id] = includes_url() . 'images/media/file.png';
				}
			}

			return $srcs;
		}


		if (wp_attachment_is('ma_el_adv_image', $attach_id)) {
			$image_url = wp_get_attachment_url($attach_id, 'full'); //get img URL
			return $image_url ? $image_url : false;
		} elseif (wp_attachment_is('audio', $attach_id)) {
			return includes_url() . 'images/media/audio.png';
		} elseif (wp_attachment_is('video', $attach_id)) {
			return includes_url() . 'images/media/video.png';
		} elseif (0 === strpos(get_post_mime_type($attach_id), 'text/')) {
			return includes_url() . 'images/media/file.png';
		}

		return false;
	}


	function ma_el_generate_image_sizes($image_sizes)
	{
		if ('auto' === $image_sizes) {
			return $image_sizes;
		}

		$attr_sizes = '';
		foreach ($image_sizes as $element_size) {
			$attr_sizes .= !empty($element_size['min']) ? '(min-width:' . $element_size['min'] . ') ' : '';
			$attr_sizes .= !empty($element_size['min']) && !empty($element_size['max']) ? 'and ' : '';
			$attr_sizes .= !empty($element_size['max']) ? '(max-width:' . $element_size['max'] . ') ' : '';
			$attr_sizes .= !empty($element_size['width']) ? $element_size['width'] . ',' : ',';
		}
		return rtrim($attr_sizes, ',');
	}

	/**
	 * Retrieves the resized attachment image custom srcset and sizes
	 */
	function jltma_get_the_responsive_attachment($attachment_id = null, $args = array())
	{

		$defaults = array(
			'quality'         => 100,
			'attr'            => '',
			'preloadable'     => true, // Set it to "true" or "null" in order make the image ready for preloading, "true" will load the best match as well.
			'preload_preview' => true, // (true, false, 'progress-box', 'simple-spinner', 'simple-spinner-light', 'simple-spinner-dark') if true, insert a low quality placeholder until lazyloading the main image. If set to progress, display a progress animation as a placeholder.
			'preload_bgcolor' => '',   // background color while loading the image
			'upscale'         => false,
			'size'            => 'large',
			'crop'            => null,
			'add_hw'          => true,
			'add_ratio'       => true,
			'sizes'           => 'auto', // (sizes)
			'srcset'          => 'auto', // (srcset) automatically calculate the image sizes based on the 'size' param, OR 'wp' generates image srcs based on WP default image sizes

			'original_src'    => true,
			'extra_class'     => ''
		);



		$args = wp_parse_args($args, $defaults);

		// fallback for deprecated attributes
		if (isset($args['image_sizes'])) {
			$args['sizes'] = $args['image_sizes'];
			unset($args['image_sizes']);
		}
		if (isset($args['srcset_sizes'])) {
			$args['srcset'] = $args['srcset_sizes'];
			unset($args['srcset_sizes']);
		}

		extract($args);

		// Throw error if $size is not defined
		if (empty($size)) {
			$this->ma_el_error(sprintf('"size" option for "%s" function is not defined.', __FUNCTION__));
		}

		$attachment  = get_post($attachment_id);

		// get original image info
		$original_image       = wp_get_attachment_image_src($attachment_id, 'full');
		$original_image_width = $original_image['1'];

		// Check crop value
		$crop = empty($crop) ? $crop : $this->ma_el_is_true($crop);

		// Make sure there is a valid $size value passed
		if (is_array($size)) {
			if (empty($size['width']) && empty($size['height'])) {
				$size = 'medium_large';
			}
			// since the size is a custom width and height, the hard crop is required
			if (is_null($crop)) {
				$crop = true;
			}
		}

		// Get the $size dimensions
		$dimensions = $size;
		if (in_array($dimensions, array('full', 'original'))) {
			$dimensions = array('width' => $original_image['1'], 'height' => $original_image['2']);
			// prevent generating srcset if the original image size is requested
			$srcset = null;
			$sizes  = null;
		} elseif (is_string($dimensions)) {
			$dimensions = $this->ma_el_wp_get_image_size($dimensions);
			$dimensions = array('width' => $dimensions['width'], 'height' => $dimensions['height']);
		}

		// calculate the image aspect ratio
		$image_aspect_ratio = empty($dimensions['width']) || empty($dimensions['height']) ? null : $dimensions['width'] / $dimensions['height'];

		// if aspect ratio is available, turn on the upscale for improving accuracy in cropping images
		if ($image_aspect_ratio) {
			$upscale = true;
		}

		/*   Generate main image
	        /*-------------------------------------*/
		// crop the main image
		if (is_string($size)) {
			$main_image = wp_get_attachment_image_src($attachment_id, $size);
			if ($size !== 'full' && empty($main_image['3']) && in_array($size, get_intermediate_image_sizes())) {
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				wp_update_image_subsizes($attachment_id);
				$main_image = wp_get_attachment_image_src($attachment_id, $size);
			}
			$src = $main_image['0'];
		} else {
			$src = $this->ma_el_get_the_resized_attachment_src($attachment_id, $dimensions['width'], $dimensions['height'], $crop, $quality, $upscale);
		}


		if (empty($src)) return '';

		// image width of default image src
		$default_image_width = round($original_image_width > $dimensions['width'] ? $dimensions['width'] : $original_image_width);


		/*   Calculate SRCSET
	        /*-------------------------------------*/
		$attr_srcset = '';

		if (!empty($srcset)) {

			// generate src sizes based on the list of sizes
			if (is_array($srcset)) {

				foreach ($srcset as $srcset_size) {
					// width is required for each src item
					if (!$srcset_size['width'] = empty($srcset_size['width']) ? null : $srcset_size['width']) {
						continue;
					}
					// dont generate image src bigger than original image
					if ($srcset_size['width'] >= $original_image_width) {
						break;
					}
					$srcset_size['height'] = empty($srcset_size['height']) ? null : $srcset_size['height'];

					if (empty($srcset_image_url = $this->ma_el_get_the_resized_attachment_src($attachment_id, $srcset_size['width'], $srcset_size['height'], $crop, $quality, $upscale))) {
						continue;
					}
					$attr_srcset .= $srcset_image_url;
					$attr_srcset .= ' ' . round($srcset_size['width']) . 'w,';
				}

				// generate image sizes based on the default WordPress image sizes
			} elseif ('wp' == $srcset || ((is_string($size) || empty($image_aspect_ratio)) && 'auto' === $srcset)) {
				$default_image_sizes = $this->ma_el_base_image_sizes();

				foreach ($default_image_sizes as $image_size) {
					// Check if the image size is defined before
					if (!$current_image_dimensions = wp_get_attachment_image_src($attachment_id, $image_size)) {
						$this->ma_el_error(sprintf('Intermediate image size name not found in "%s" function.', __FUNCTION__));
						continue;
					}
					// dont generate image src bigger than original image
					if ($current_image_dimensions['1'] >= $original_image_width) {
						break;
					}

					if (is_array($size)) {
						if (empty($srcset_image_url = $this->ma_el_get_the_resized_attachment_src($attachment_id, $current_image_dimensions['1'], $current_image_dimensions['2'], $crop, $quality, $upscale))) {
							continue;
						}
						$attr_srcset .= $srcset_image_url;
						$attr_srcset .= ' ' . round($current_image_dimensions['1']) . 'w,';
					} else {
						$attr_srcset .=  $current_image_dimensions['0'];
						$attr_srcset .= ' ' . round($current_image_dimensions['1']) . 'w,';
					}
				}

				// automatically generate general image sizes based on the aspect ratio of the main image according the dimensions in $size
			} elseif (is_array($size) && 'auto' === $srcset && $image_aspect_ratio) {
				$default_image_sizes = $this->ma_el_base_image_sizes();

				foreach ($default_image_sizes as $image_size) {
					$current_image_width = get_option($image_size . '_size_w');

					// dont generate image src bigger than original image
					if ($current_image_width >= $original_image_width) {
						break;
					}
					if (empty($srcset_image_url = $this->ma_el_get_the_resized_attachment_src($attachment_id, $current_image_width, $current_image_width / $image_aspect_ratio, $crop, $quality, $upscale))) {
						continue;
					}
					$attr_srcset .= $srcset_image_url;
					$attr_srcset .= ' ' . round($current_image_width) . 'w,';
				}
			}


			// Add the original image src if the original size greater that large size exists
			if ($attr_srcset) {

				// Add main image to srcset too
				$attr_srcset .= $src . ' ' . $default_image_width . 'w,';

				if ($original_src) {
					if ($image_aspect_ratio) {
						$full_width  = (int) ($original_image[1] - 10);
						$full_height = (int) ($full_width / $image_aspect_ratio);
						$attr_srcset .= $this->ma_el_get_the_resized_attachment_src($attachment_id, $full_width, $full_height, true, $quality, true);
						$attr_srcset .= ' ' . round($full_width) . 'w';
					} else {
						$attr_srcset .= $original_image[0] . ' ' . $original_image[1] . 'w';
					}
				}

				$attr_srcset =  rtrim($attr_srcset, ',');
			}
		}

		/*   Add essential attribute
	        /*-------------------------------------*/

		// Check preloadable value
		$preloadable = empty($preloadable) ? $preloadable : $this->ma_el_is_true($preloadable);

		// Force to add width and height attributes if lazyloading is enabled
		if ($preloadable && $preload_preview) {
			$add_hw = true;
		}

		$html = '';
		$width_attribute  = $original_image['1'] < $dimensions['width'] ? $original_image['1'] : $dimensions['width'];
		$height_attribute = $original_image['2'] < $dimensions['height'] ? $original_image['2'] : $dimensions['height'];
		$string_size      = $width_attribute . 'x' . $height_attribute;
		$hwstring         = $add_hw ? image_hwstring($width_attribute, $height_attribute) : '';

		// default image attributes
		$default_attr  = array(
			'src'              => $src,
			'class'            => "jltma-attachment jltma-featured-image attachment-$string_size jltma-attachment-id-$attachment_id $extra_class",
			'alt'              => trim(strip_tags(get_post_meta($attachment_id, '_wp_attachment_image_alt', true))), // Use Alt field first
			'width_attr_name'  => '',
			'height_attr_name' => ''
		);

		if (empty($default_attr['alt']))
			$default_attr['alt'] = trim(strip_tags($attachment->post_excerpt)); // If not, Use the Caption
		if (empty($default_attr['alt']))
			$default_attr['alt'] = trim(strip_tags($attachment->post_title)); // Finally, use the title

		// parse the attr
		$attr = wp_parse_args($attr, $default_attr);

		// Generate 'srcset' and 'sizes' if not already present.
		if (empty($attr['srcset'])) {
			if ($attr_srcset) {
				$attr['srcset'] = $attr_srcset;
			}
		}

		if ($image_aspect_ratio) {
			$attr['data-ratio'] = round($image_aspect_ratio, 2);
		}

		$attr['data-original-w'] = $original_image_width;

		if (!empty($attr['width_attr_name']) || !empty($attr['height_attr_name']))
			$metadata = wp_get_attachment_metadata($attachment_id);

		if (!empty($attr['width_attr_name']))
			$attr[$attr['width_attr_name']] = $metadata['width'];

		if (!empty($attr['height_attr_name']))
			$attr[$attr['height_attr_name']] = $metadata['height'];


		/*   Calculate sizes
	        /*-------------------------------------*/
		if (!empty($sizes)) {
			$attr_sizes  = $this->ma_el_generate_image_sizes($sizes);

			if (empty($attr['sizes']) && $attr_sizes) {
				$attr['sizes'] = $attr_sizes;
			}
		}

		// Keep the auto sizes if null passed to $preloadable
		if (is_null($preloadable) && 'auto' === $sizes) {

			// move srcset to data-srcset
			if (!empty($attr['srcset'])) {
				$attr['data-srcset'] = $attr['srcset'];
				unset($attr['srcset']);
			}

			if (!empty($attr['src'])) {
				$attr['data-src'] = $attr['src'];
				unset($attr['src']);
			}

			$attr['sizes']  = 'auto';
		} elseif ($preloadable && 'auto' === $sizes) {

			// move srcset to data-srcset
			if (!empty($attr['srcset'])) {
				$attr['data-srcset'] = $attr['srcset'];
				unset($attr['srcset']);
			}

			$attr['sizes']  = 'auto';
		} elseif (!$preloadable && 'auto' === $sizes) {
			$auto_sizes = array(
				array('min' => '', 'max' => '479px', 'width' => '480px'),
				array('min' => '', 'max' => '767px', 'width' => '768px'),
				array('min' => '', 'max' => '1023px', 'width' => '1024px')
			);
			if ($dimensions['width']) {
				$auto_sizes[] = array('min' => '', 'max' => '', 'width' => $default_image_width . 'px');
			}
			$attr['sizes']  = $this->ma_el_generate_image_sizes($auto_sizes);
		}

		// Display a blurred preview of the main image
		if ($preloadable) {
			// add the required class name to make it visible to image size-calculation script
			$attr['class'] .= ' jltma-preload';

			if (!empty($attr['src']) && empty($attr['srcset'])) {
				$attr['data-src'] = $attr['src'];
			}

			$attr['src'] = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

			if (in_array($preload_preview, array('progress-box', 'simple-spinner', 'simple-spinner-light', 'simple-spinner-dark'))) {
				$attr['class'] .= ' jltma-' . esc_attr($preload_preview); // the class name to add style and transition to the progress animation
			} elseif ($this->ma_el_is_true($preload_preview)) {
				$preload_ratio = null === $image_aspect_ratio ? null : 40 / $image_aspect_ratio;
				$attr['src'] = $this->ma_el_get_the_resized_attachment_src($attachment_id, 40, $preload_ratio, $crop, 100, false);
				$attr['class'] .= ' jltma-has-preview'; // the class name to add style and transition to the preview image
			} else {
				$attr['class'] .= ' jltma-blank';
			}

			if (!empty($preload_bgcolor)) {
				$attr['data-bg-color'] = $preload_bgcolor;
			}
		}

		unset($attr['width_attr_name']);
		unset($attr['height_attr_name']);


		/**
		 * Filter the list of attachment image attributes.
		 *
		 * @param mixed $attr          Attributes for the image markup.
		 * @param int   $attachment_id Image attachment ID.
		 */
		$attr = apply_filters('wp_get_attachment_image_attributes', $attr, $attachment, $size);
		$attr = array_map('esc_attr', $attr);
		$html = rtrim("<img $hwstring");
		foreach ($attr as $name => $value) {
			if ($value) {
				$html .= " $name=" . '"' . $value . '"';
			}
		}
		$html .= ' />';

		return $html;
	}



	public function ma_el_attachment_caption($attach_id = null, $stripe = true)
	{

		$attachment_post = get_post($attach_id);

		if (empty($attachment_post)) {
			return '';
		}

		$caption = $attachment_post->post_excerpt;
		if (empty($caption))
			$caption = $attachment_post->post_title;
		if (empty($caption))
			$caption = get_post_meta($attach_id, '_wp_attachment_image_alt', true);
		if ($stripe) {
			$caption = strip_tags($caption);
		}

		return trim($caption);
	}

	public function jltma_get_attachment_url($attach_id, $featured_img_size = "medium")
	{
		if (is_numeric($attach_id)) {
			$image_url = wp_get_attachment_image_src($attach_id, $featured_img_size);
			return $image_url[0];
		}
		return '';
	}


	// This is the widget call back in fact the front end out put of this widget comes from this function
	public function jltma_adv_image_callback($atts, $shortcode_content = null)
	{

		// Defining default attributes
		$default_atts = array(
			'title'            => '', // header title
			'add_content'      => false,
			'content_title'    => '',
			'content_subtitle' => '',
			'tilt'             => false,
			'colorized_shadow' => false,
			'attach_id'        => '', // attachment id for main image
			'attach_id_hover'  => '', // attachment id for hover image
			'link'             => '', // link on image
			'target'           => '_self', // link target
			'nofollow'         => '', // link nofollow
			'alt'              => '', // alternative text
			'size'             => 'medium_large', // image size
			'width'            => '', // final width of image
			'height'           => '', // final height of image
			'align'            => 'alignnone',
			'icon'             => 'plus', // icon type. plus, zoom, none
			'lightbox'         => 'no', // open in lightbox or not
			'preloadable'      => '0',
			'preload_preview'  => '0',
			'preload_bgcolor'  => '',

			'image_html'       => '',

			'display_ribbon'   => '1',
			'ribbon_style'     => 'simple',
			'ribbon_position'  => 'top-right',
			'ribbon_text'      => '',

			'extra_classes'    => '', // custom css class names for this element
			'custom_el_id'     => '', // custom id attribute for this element
			'base_class'       => 'jltma-advanced-image'  // base class name for container
		);


		$result = $this->ma_el_get_widget_scafold($atts, $default_atts, $shortcode_content);

		extract($result['parsed_atts']);

		$image_primary      = '';
		$image_primary_full = '';
		$image_secondary    = '';
		$lightbox_attrs     = '';
		$image_classes      = "jltma-attachment jltma-featured-image jltma-attachment-id-$attach_id";
		$frame_classes      = '';


		if (empty($size)) {
			$size = 'medium_large';
		}
		if ('custom' == $size) {
			$size = array('width' => $width, 'height' => $height);
		}

		if ($add_content) {
			$frame_classes .= 'jltma-image-box-widget-bg-cover';
		}


		if ($this->ma_el_is_true($tilt)) {
			$frame_classes .= ' jltma-tilt-box';
		}

		if ($this->ma_el_is_true($colorized_shadow) && empty($attach_id_hover)) {
			$image_classes .= ' jltma-img-dynamic-dropshadow';
		}


		if (!empty($attach_id) && is_numeric($attach_id)) {
			$image_primary = $this->jltma_get_the_responsive_attachment(
				$attach_id,
				array(
					'quality'         => 100,
					'preloadable'     => $this->ma_el_is_true($preloadable),
					'preload_preview' => $preload_preview,
					'preload_bgcolor' => $preload_bgcolor,
					'size'            => $size,
					'crop'            => true,
					'add_hw'          => true,
					'upscale'         => false,
					'original_src'    => 'full' === $size ? true : false,
					'attr'            => array('class' => $image_classes)
				)
			);
			$image_primary_full_src = $this->jltma_get_attachment_url($attach_id, 'full');
			$image_primary_meta     = wp_get_attachment_metadata($attach_id);

			$lightbox_attrs = 'data-elementor-open-lightbox="no" ';

			// While SVG images don't have dimension, this check is required
			if (!empty($image_primary_meta['width'])) {
				$lightbox_attrs .= 'data-original-width="' . $image_primary_meta['width'] . '" ';
			}
			if (!empty($image_primary_meta['height'])) {
				$lightbox_attrs .= 'data-original-height="' . $image_primary_meta['height'] . '" ';
			}
			$lightbox_attrs     .= 'data-caption="' . $this->ma_el_attachment_caption($attach_id) . '"';
		} elseif (!empty($image_html)) {
			$image_primary = $image_html;
		}

		if (!empty($attach_id_hover) && is_numeric($attach_id_hover)) {
			$image_secondary = $this->jltma_get_the_responsive_attachment(
				$attach_id_hover,
				array(
					'quality'         => 100,
					'preloadable'     => $this->ma_el_is_true($preloadable),
					'preload_preview' => $preload_preview,
					'preload_bgcolor' => $preload_bgcolor,
					'size'            => $size,
					'crop'            => true,
					'add_hw'          => true,
					'upscale'         => false
				)
			);
		}

		$anchor_link    = $this->ma_el_is_true($lightbox) ? $image_primary_full_src : esc_attr($link);
		$anchor_class   = $this->ma_el_is_true($lightbox) ? 'jltma-lightbox-btn' : '';
		$frame_classes .= $this->ma_el_is_true($lightbox) ? ' jltma-media-frame jltma-lightbox-frame' : '';
		$target         = $target !== '_blank' ? 'target="_self"' : 'target="_blank"';
		$nofollow       = $this->ma_el_is_true($nofollow) ? ' rel="nofollow"' : '';

		// Hover Effect
		$hover_class = '';
		if (!empty($anchor_link)) {
			$hover_class = 'jltma-hover-active';
		}

		$overflow_class = '';
		if ($ribbon_style === 'corner') {
			$overflow_class = ' jltma-hidden-overflow';
		}

		// add alignment class on main element
		$result['widget_header'] = str_replace($base_class, $base_class . ' jltma-align' . $align, $result['widget_header']);

		ob_start();

		// widget header ------------------------------
		echo $result['widget_header'];

		echo $result['widget_title'];

?>

		<div class="jltma-adv-image-wrapper">
			<div class="jltma-media-image <?php echo esc_attr($hover_class);
											echo esc_attr($frame_classes);
											echo esc_attr($overflow_class); ?>">
				<?php if (!empty($anchor_link)) { ?>
					<a class="<?php echo $anchor_class; ?>" href="<?php echo $anchor_link; ?>" data-fancybox="images" <?php echo $lightbox_attrs  . ' ' . $target . ' ' . $nofollow; ?>>
					<?php } ?>

					<?php if ($this->ma_el_is_true($display_ribbon) && !empty($ribbon_text)) { ?>
						<div class="jltma-ribbon-wrapper jltma-<?php echo $ribbon_style; ?>-ribbon <?php echo $ribbon_position; ?>">
							<span><?php echo $ribbon_text; ?></span>
						</div>
					<?php } ?>


					<?php if ('plus' == $icon && empty($image_secondary)) { ?>
						<div class='jltma-hover-scale-circle-plus'>
							<span class='jltma-symbol-plus'></span>
							<span class='jltma-symbol-circle'></span>
						</div>
					<?php } ?>

					<?php if (!empty($image_secondary)) { ?>
						<div class="jltma-image-holder jltma-image-has-secondary">
							<?php echo $image_primary; ?>
							<?php echo $image_secondary; ?>
						</div>
						<?php } else {
						if ($this->ma_el_is_true($lightbox)) { ?>
							<div class="jltma-frame-mask jltma-frame-darken">
								<?php echo $image_primary; ?>
							</div>
						<?php } else {
							echo $image_primary;
						} ?>
					<?php } ?>

					<?php if (!empty($anchor_link)) { ?>
					</a>
				<?php } ?>
			</div>
		</div>


<?php
		echo $result['widget_footer'];

		return ob_get_clean();
	}


	protected function render()
	{

		$settings    = $this->get_settings_for_display();


		$link_target = $settings['ma_el_adv_image_link']['is_external'] ? '_blank' : '_self';

		$args        = array(
			'image_html'       => Group_Control_Image_Size::get_attachment_image_html($settings, 'ma_el_adv_image'),
			'attach_id'        => Master_Addons_Helper::jltma_get_array_value($settings['ma_el_adv_image'], 'id'),
			'size'             => $settings['ma_el_adv_image_size'],
			'width'            => Master_Addons_Helper::jltma_get_array_value($settings['ma_el_adv_image_custom_dimension'], 'width'),
			'height'           => Master_Addons_Helper::jltma_get_array_value($settings['ma_el_adv_image_custom_dimension'], 'height'),
			'link'             => Master_Addons_Helper::jltma_get_array_value($settings['ma_el_adv_image_link'], 'url'),
			'nofollow'         => Master_Addons_Helper::jltma_get_array_value($settings['ma_el_adv_image_link'], 'nofollow'),
			'target'           => Master_Addons_Helper::jltma_get_array_value($settings['ma_el_adv_image_link'], 'is_external', false) ? '_blank' : '_self',
			'align'            => $settings['ma_el_adv_image_settings_alignment'],

			'attach_id_hover'  => Master_Addons_Helper::jltma_get_array_value($settings['ma_el_adv_image_hover_image'], 'id'),

			'display_ribbon'   => $settings['ma_el_adv_image_display_ribbon'],
			'ribbon_text'      => $settings['ma_el_adv_image_ribbon_text'],
			'ribbon_style'     => $settings['ma_el_adv_image_ribbon_style'],
			'ribbon_position'  => $settings['ma_el_adv_image_ribbon_position'],
			'colorized_shadow' => $settings['ma_el_adv_image_colorized_shadow'],

			'lightbox'         => $settings['ma_el_adv_image_settings_lightbox'],
			'icon'             => $settings['ma_el_adv_image_settings_icon'],
			'preloadable'      => $settings['ma_el_adv_image_settings_preloadable'],
			'preload_preview'  => $settings['ma_el_adv_image_settings_preload_preview'],
			'preload_bgcolor'  => $settings['ma_el_adv_image_settings_preload_bgcolor'],
			'tilt'             => $settings['ma_el_adv_image_tilt']
		);
		echo $this->jltma_adv_image_callback($args);
	}
}
