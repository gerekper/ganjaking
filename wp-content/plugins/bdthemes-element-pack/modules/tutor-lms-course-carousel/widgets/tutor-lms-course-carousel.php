<?php

namespace ElementPack\Modules\TutorLmsCourseCarousel\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;

use ElementPack\Traits\Global_Swiper_Controls;

use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

class TutorLms_Course_Carousel extends Module_Base {

	use Global_Swiper_Controls;
	use Group_Control_Query;

	private $_query = null;

	public function get_name() {
		return 'bdt-tutor-lms-course-carousel';
	}

	public function get_title() {
		return BDTEP . esc_html__('Tutor LMS Course Carousel', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-tutor-lms-course-carousel';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['tutor', 'elearning', 'lms', 'course', 'course carousel', 'carousel'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-tutor-lms'];
		}
	}

	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
			return ['ep-tutor-lms-course-carousel'];
        }
	}

	public function on_import($element) {
		if (!get_post_type_object($element['settings']['posts_post_type'])) {
			$element['settings']['posts_post_type'] = 'post';
		}

		return $element;
	}


	public function get_query() {
		return $this->_query;
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/VYrIYQESjXs';
	}

	public function register_controls() {
		$this->register_section_controls();
	}

	private function register_section_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__('Columns', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'   => esc_html__('Column Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'         => 'thumbnail_size',
				'label'        => esc_html__('Image Size', 'bdthemes-element-pack'),
				'exclude'      => ['custom'],
				'default'      => 'medium',
				'prefix_class' => 'bdt-tutor--thumbnail-size-',
			]
		);

		$this->add_control(
			'item_ratio',
			[
				'label'   => esc_html__('Item Height', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 50,
						'max'  => 500,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course-item .bdt-tutor-course-header a img' => 'height: {{SIZE}}px',
				],
			]
		);

		$this->add_control(
			'show_meta_label',
			[
				'label'   => esc_html__('Show Meta Label', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_meta_wishlist',
			[
				'label'   => esc_html__('Show Wishlist Meta', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_rating',
			[
				'label'   => esc_html__('Show Rating', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => esc_html__('Show Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_user_clock',
			[
				'label'   => esc_html__('Show User Meta', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_author_avatar',
			[
				'label'   => esc_html__('Show Avatar', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_author_name',
			[
				'label'   => esc_html__('Show Author Name', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_category',
			[
				'label'   => esc_html__('Show Category', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_cart_btn_price',
			[
				'label'   => esc_html__('Show Price/Cart Button', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();
		//New Query Builder Settings
		$this->start_controls_section(
			'section_post_query_builder',
			[
				'label' => __('Query', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->register_query_builder_controls();

		$this->update_control(
			'posts_per_page',
			[
				'default' => 9,
			]
		);

		$this->end_controls_section();

		//Navigation Controls
		$this->start_controls_section(
			'section_content_navigation',
			[
				'label' => __('Navigation', 'bdthemes-element-pack'),
			]
		);

		//Global Navigation Controls
		$this->register_navigation_controls();

		$this->end_controls_section();

		//Global Carousel Settings Controls
		$this->register_carousel_settings_controls();

		//Style
		$this->start_controls_section(
			'section_tlms_cg_item_style',
			[
				'label' => esc_html__('Item', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tlms_cg_item_tabs');

		$this->start_controls_tab(
			'tlms_cg_item_tabs_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'tlms_cg_item_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tlms_cg_item_border',
				'selector' => '{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item',
			]
		);

		$this->add_responsive_control(
			'tlms_cg_item_radius',
			[
				'label' => __('Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item, {{WRAPPER}} .bdt-tutor-lms-course-carousel .swiper-carousel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tlms_cg_item_shadow',
				'selector' => '{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tlms_cg_item_tabs_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'tlms_cg_item_hover_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_item_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'tlms_cg_item_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tlms_cg_item_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item:hover',
			]
		);

		$this->add_responsive_control(
			'tlms_cg_item_shadow_padding',
			[
				'label'       => __('Match Padding', 'bdthemes-element-pack'),
				'description' => __('You have to add padding for matching overlaping hover shadow', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'default' => [
					'size' => 10
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tlms_cg_header_style',
			[
				'label' => esc_html__('Header', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tlms_cg_header_image_heading',
			[
				'label' => esc_html__('Image', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'tlms_cg_image_radius',
			[
				'label' => __('Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item .bdt-tutor-course-header a, {{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item .bdt-tutor-course-header a img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_heading',
			[
				'label' => esc_html__('Meta Label', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HEADING,
				'separator'	=> 'before',
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->start_controls_tabs('tlms_cg_header_meta_label_tabs');

		$this->start_controls_tab(
			'tlms_cg_header_meta_label_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_label_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level' => 'color: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_label_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level' => 'background: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tlms_cg_header_meta_label_border',
				'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level',
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'tlms_cg_header_meta_label_radius',
			[
				'label' => __('Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_label_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tlms_cg_header_meta_label_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level',
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tlms_cg_header_meta_label_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_label_hover_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level:hover' => 'color: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_label_hover_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level:hover' => 'background: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_label_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level:hover' => 'border-color: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'tlms_cg_header_meta_wishlist_heading',
			[
				'label' => esc_html__('Wishlist Meta', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HEADING,
				'separator'	=> 'before',
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tlms_cg_header_meta_wishlist_tabs');

		$this->start_controls_tab(
			'tlms_cg_header_meta_wishlist_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_wishlist_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist a' => 'color: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_wishlist_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist' => 'background: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tlms_cg_header_meta_wishlist_border',
				'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist',
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'tlms_cg_header_meta_wishlist_radius',
			[
				'label' => __('Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_wishlist_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tlms_cg_header_meta_wishlist_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist',
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tlms_cg_header_meta_wishlist_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_wishlist_hover_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist:hover a' => 'color: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_wishlist_hover_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist:hover' => 'background: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_wishlist_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist:hover' => 'border-color: {{VALUE}};',
				],
				'conditions'   => [
					'relation'	=> 'or',
					'terms' => [
						[
							'name'  => 'tlms_cg_header_meta_wishlist_border_border!',
							'value' => '',
						],
						[
							'name'  => 'show_meta_wishlist',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tlms_cg_content_area_style',
			[
				'label' => esc_html__('Content', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions'   => [
					'relation'	=> 'or',
					'terms' => [
						[
							'name'  => 'show_rating',
							'value' => 'yes',
						],
						[
							'name'  => 'show_title',
							'value' => 'yes',
						],
						[
							'name'  => 'show_user_clock',
							'value' => 'yes',
						],
						[
							'name'     => 'show_category',
							'value'    => 'yes',
						],
						[
							'name'     => 'show_author_avatar',
							'value'    => 'yes',
						],
						[
							'name'     => 'show_author_name',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-container' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('tlms_cg_content_tabs_style');

		$this->start_controls_tab(
			'tlms_cg_content_rating',
			[
				'label' => __('Rating', 'bdthemes-element-pack'),
				'condition'	=> [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_rating_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .tutor-star-rating-group' => 'color: {{VALUE}};',
				],
				'condition'	=> [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_rating_icon_indent',
			[
				'label'   => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .tutor-star-rating-group i'  => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tlms_cg_content_rating_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-rating-wrap',
				'condition'	=> [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_rating_spacing',
			[
				'label'   => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .tutor-star-rating-group'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'show_rating' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tlms_cg_content_title',
			[
				'label' => __('Title', 'bdthemes-element-pack'),
				'condition'	=> [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_title_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-title h2 a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_title_hover_color',
			[
				'label'     => __('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-title h2 a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tlms_cg_content_title_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-title h2',
			]
		);

		$this->add_control(
			'tlms_cg_content_title_spacing',
			[
				'label'   => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-title h2'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tlms_cg_content_meta',
			[
				'label' => __('User Meta', 'bdthemes-element-pack'),
				'condition'	=> [
					'show_user_clock' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_meta_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-meta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tlms_cg_content_meta_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-meta',
			]
		);

		$this->add_control(
			'tlms_cg_content_meta_spacing',
			[
				'label'   => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-meta'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tlms_cg_content_author',
			[
				'label' => __('Author', 'bdthemes-element-pack'),
				'conditions'   => [
					'relation'	=> 'or',
					'terms' => [
						[
							'name'  => 'show_author_name',
							'value' => 'yes',
						],
						[
							'name'     => 'show_category',
							'value'    => 'yes',
						],
						[
							'name'     => 'show_author_avatar',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_author_heading',
			[
				'label' => esc_html__('Avatar', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HEADING,
				'condition'	=> [
					'show_author_avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_author_avatar_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-text-avatar' => 'color: {{VALUE}} !important;',
				],
				'condition'	=> [
					'show_author_avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_author_avatar_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-text-avatar' => 'background-color: {{VALUE}} !important;',
				],
				'condition'	=> [
					'show_author_avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_author_avatar_height_width',
			[
				'label'   => esc_html__('Width', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-text-avatar'  => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'show_author_avatar' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'tlms_cg_content_author_avatar_radius',
			[
				'label' => __('Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-text-avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	=> [
					'show_author_avatar' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tlms_cg_content_author_avatar_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-text-avatar',
				'condition'	=> [
					'show_author_avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_author_avatar_spacing',
			[
				'label'   => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-text-avatar'  => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'show_author_avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_category_heading',
			[
				'label' => esc_html__('Author Name / Category', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HEADING,
				'separator'	=> 'before',
				'conditions'   => [
					'relation'	=> 'or',
					'terms' => [
						[
							'name'  => 'show_author_name',
							'value' => 'yes',
						],
						[
							'name'     => 'show_category',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_author_category_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author > div a' => 'color: {{VALUE}}',
				],
				'conditions'   => [
					'relation'	=> 'or',
					'terms' => [
						[
							'name'  => 'show_author_name',
							'value' => 'yes',
						],
						[
							'name'     => 'show_category',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_author_category_hover_color',
			[
				'label'     => __('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author > div a:hover' => 'color: {{VALUE}}',
				],
				'conditions'   => [
					'relation'	=> 'or',
					'terms' => [
						[
							'name'  => 'show_author_name',
							'value' => 'yes',
						],
						[
							'name'     => 'show_category',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_category_sub_text_color',
			[
				'label'     => __('Sub Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author > div span' => 'color: {{VALUE}}',
				],
				'conditions'   => [
					'relation'	=> 'or',
					'terms' => [
						[
							'name'  => 'show_author_name',
							'value' => 'yes',
						],
						[
							'name'     => 'show_category',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tlms_cg_content_category_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author',
				'conditions'   => [
					'relation'	=> 'or',
					'terms' => [
						[
							'name'  => 'show_author_name',
							'value' => 'yes',
						],
						[
							'name'     => 'show_category',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tlms_cg_footer_area_style',
			[
				'label' => esc_html__('Footer', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'show_cart_btn_price'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_footer_area_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-footer' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tlms_cg_footer_area_border',
				'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-footer',
			]
		);

		$this->add_control(
			'tlms_cg_footer_area_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tlms_cg_footer_price_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price',
			]
		);

		$this->add_control(
			'tlms_cg_footer_price_heading',
			[
				'label' => esc_html__('Price', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HEADING,
				'separator'	=> 'before'
			]
		);

		$this->add_control(
			'tlms_cg_footer_price_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_footer_cart_heading',
			[
				'label' => esc_html__('Cart Button', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HEADING,
				'separator'	=> 'before'
			]
		);

		$this->start_controls_tabs('tlms_cg_footer_cart_tabs');

		$this->start_controls_tab(
			'tlms_cg_footer_cart_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'tlms_cg_footer_cart_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a::before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_footer_cart_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tlms_cg_footer_cart_border',
				'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a',
			]
		);

		$this->add_responsive_control(
			'tlms_cg_footer_cart_radius',
			[
				'label' => __('Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_footer_cart_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tlms_cg_footer_cart_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'tlms_cg_footer_cart_hover_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a:hover, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a:hover:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_footer_cart_hover_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_footer_cart_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'tlms_cg_footer_cart_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//Navigation Style
		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'      => __('Navigation', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'navigation',
							'operator' => '!=',
							'value'    => 'none',
						],
						[
							'name'  => 'show_scrollbar',
							'value' => 'yes',
						],
					],
				],
			]
		);

		//Global Navigation Style Controls
		$this->register_navigation_style_controls('swiper-carousel');

		$this->end_controls_section();
	}

	public function get_taxonomies() {
		$taxonomies = get_taxonomies(['show_in_nav_menus' => true], 'objects');

		$options = ['' => ''];

		foreach ($taxonomies as $taxonomy) {
			$options[$taxonomy->name] = $taxonomy->label;
		}

		return $options;
	}

	/**
	 * Get post query builder arguments
	 */
	public function query_posts($posts_per_page) {
		$settings = $this->get_settings();

		$args = [];
		if ($posts_per_page) {
			$args['posts_per_page'] = $posts_per_page;
			$args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
		}

		$default = $this->getGroupControlQueryArgs();
		$args = array_merge($default, $args);

		$this->_query = new \WP_Query($args);
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		// TODO need to delete after v6.5
		if (isset($settings['limit']) and $settings['posts_per_page'] == 9) {
			$limit = $settings['limit'];
		} else {
			$limit = $settings['posts_per_page'];
		}
		$this->query_posts($limit);
		$wp_query = $this->get_query();

		if (!$wp_query->found_posts) {
			return;
		}

		$this->render_header();

		while ($wp_query->have_posts()) {
			$wp_query->the_post();

			$this->render_post();
		}

		$this->render_footer();

		wp_reset_postdata();
	}

	public function render_header() {
		$settings = $this->get_settings_for_display();

		//Global Function
		$this->render_swiper_header_attribute('tutor-course');

		$this->add_render_attribute('carousel', 'class', ['bdt-tutor-lms-course-carousel', 'bdt-tutor-course']);

		$this->add_render_attribute('tutor-courses-wrapper', 'class', 'swiper-wrapper');

?>
		<div <?php echo $this->get_render_attribute_string('carousel'); ?>>
			<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
				<div <?php echo $this->get_render_attribute_string('tutor-courses-wrapper'); ?>>
				<?php
			}

			public function render_thumbnail() {
				$settings = $this->get_settings_for_display();

				$course_id = get_the_ID();

				$settings['thumbnail_size'] = [
					'id' => get_post_thumbnail_id(),
				];

				$thumbnail_html      = Group_Control_Image_Size::get_attachment_image_html($settings, 'thumbnail_size');
				$placeholder_img_src = Utils::get_placeholder_image_src();
				$img_url             = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');

				if (!$thumbnail_html) {
					$thumbnail_html = '<img src="' . esc_url($placeholder_img_src) . '" alt="' . get_the_title() . '">';
				}

				?>

					<div class="bdt-tutor-course-header">
						<a href="<?php the_permalink(); ?>"> <?php echo $thumbnail_html ?> </a>
						<div class="bdt-tutor-course-loop-header-meta">
							<?php
							$is_wishlisted = tutor_utils()->is_wishlisted($course_id);
							$has_wish_list = '';
							if ($is_wishlisted) {
								$has_wish_list = 'has-wish-listed';
							}

							if ('yes' == $settings['show_meta_label']) {
								echo '<span class="bdt-tutor-course-loop-level">' . get_tutor_course_level() . '</span>';
							}

							if ('yes' == $settings['show_meta_wishlist']) {
								echo '<span class="bdt-tutor-course-wishlist"><a href="javascript:void(0);" class="tutor-icon-fav-line tutor-course-wishlist-btn ' . $has_wish_list . ' " data-course-id="' . $course_id . '"></a> </span>';
							}

							?>
						</div>
					</div>


				<?php
			}

			public function render_title() {
				$settings = $this->get_settings_for_display();

				if (!$settings['show_title']) {
					return;
				}

				?>
					<div class="bdt-tutor-course-loop-title">
						<h2>
							<a href="<?php echo get_the_permalink(); ?>">
								<?php the_title() ?>
							</a>
						</h2>
					</div>

				<?php
			}

			public function render_meta() {
				$settings = $this->get_settings_for_display();

				global $post, $authordata;
				$profile_url = tutor_utils()->profile_url($authordata->ID);
				?>

					<?php if ('yes' == $settings['show_user_clock']) : ?>
						<div class="bdt-tutor-course-loop-meta">
							<?php
							$course_duration = get_tutor_course_duration_context();
							$course_students = tutor_utils()->count_enrolled_users_by_course();
							?>
							<div class="bdt-tutor-single-loop-meta">
								<i class='tutor-icon-user'></i><span><?php echo $course_students; ?></span>
							</div>
							<?php
							if (!empty($course_duration)) { ?>
								<div class="bdt-tutor-single-loop-meta">
									<i class='tutor-icon-clock'></i> <span><?php echo $course_duration; ?></span>
								</div>
							<?php } ?>
						</div>
					<?php endif; ?>

					<div class="bdt-tutor-loop-author">

						<?php if ('yes' == $settings['show_author_avatar']) : ?>
							<div class="bdt-tutor-single-course-avatar">
								<a href="<?php echo $profile_url; ?>"> <?php echo tutor_utils()->get_tutor_avatar($post->post_author); ?></a>
							</div>
						<?php endif; ?>

						<?php if ('yes' == $settings['show_author_name']) : ?>
							<div class="bdt-tutor-single-course-author-name">
								<span><?php _e('by', 'tutor'); ?></span>
								<a href="<?php echo $profile_url; ?>"><?php echo get_the_author(); ?></a>
							</div>
						<?php endif; ?>

						<?php if ('yes' == $settings['show_category']) : ?>
							<div class="bdt-tutor-course-lising-category">
								<?php
								$course_categories = get_tutor_course_categories();
								if (!empty($course_categories) && is_array($course_categories) && count($course_categories)) {
								?>
									<span><?php esc_html_e('In', 'tutor') ?></span>
								<?php
									foreach ($course_categories as $course_category) {
										$category_name = $course_category->name;
										$category_link = get_term_link($course_category->term_id);
										echo "<a href='$category_link'>$category_name </a>";
									}
								}
								?>
							</div>
						<?php endif; ?>

					</div>

				<?php
			}

			public function render_rating() {
				$settings = $this->get_settings_for_display();

				if (!$settings['show_rating']) {
					return;
				}

				?>

					<div class="bdt-tutor-loop-rating-wrap">
						<?php
						$course_rating = tutor_utils()->get_course_rating();
						tutor_utils()->star_rating_generator($course_rating->rating_avg);
						?>
						<span class="bdt-tutor-rating-count">
							<?php
							if ($course_rating->rating_avg > 0) {
								echo apply_filters('tutor_course_rating_average', $course_rating->rating_avg);
								echo '<i>(' . apply_filters('tutor_course_rating_count', $course_rating->rating_count) . ')</i>';
							}
							?>
						</span>
					</div>

				<?php
			}

			public function render_price() {
				$settings = $this->get_settings_for_display();

				if (!$settings['show_cart_btn_price']) {
					return;
				}

				?>
					<div class="bdt-tutor-loop-course-footer">
						<div class="bdt-tutor-course-loop-price">
							<?php
							$course_id = get_the_ID();
							$enroll_btn = '<div  class="tutor-loop-cart-btn-wrap"><a href="' . get_the_permalink() . '">' . __('Get Enrolled', 'tutor') . '</a></div>';
							$price_html = '<div class="price"> ' . __('Free', 'tutor') . $enroll_btn . '</div>';
							if (tutor_utils()->is_course_purchasable()) {
								$enroll_btn = tutor_course_loop_add_to_cart(false);

								$product_id = tutor_utils()->get_course_product_id($course_id);
								$product    = wc_get_product($product_id);

								if ($product) {
									$price_html = '<div class="price"> ' . $product->get_price_html() . $enroll_btn . ' </div>';
								}
							}

							echo $price_html;
							?>
						</div>
					</div>

				<?php
			}

			public function render_desc() {
				?>
					<div class="bdt-tutor-loop-course-container">
						<?php
						$this->render_rating();
						$this->render_title();
						$this->render_meta();
						?>
					</div>

					<?php $this->render_price(); ?>
				<?php
			}

			public function render_post() {
				$settings = $this->get_settings_for_display();

				$this->add_render_attribute('tutor-course-item', 'class', 'bdt-tutor-course bdt-tutor-course-item swiper-slide', true);

				?>
					<div <?php echo $this->get_render_attribute_string('tutor-course-item'); ?>>
						<?php $this->render_thumbnail(); ?>
						<?php $this->render_desc(); ?>
					</div>
			<?php
			}
		}
