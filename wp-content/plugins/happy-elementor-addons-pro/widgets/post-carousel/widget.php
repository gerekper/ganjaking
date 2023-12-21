<?php
/**
 * Post Carousel widget class
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Icons_Manager;
use Happy_Addons_Pro\Traits\Lazy_Query_Builder;

defined( 'ABSPATH' ) || die();

class Post_Carousel extends Base {

	use Lazy_Query_Builder;

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Post Carousel', 'happy-addons-pro' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-flip-card1';
	}

	public function get_keywords() {
		return [ 'post', 'carousel', 'blog' ];
	}

	/**
	 * Register widget content controls
	 */
	protected function register_content_controls() {
		$this->__layout_content_controls();
		$this->__query_content_controls();
		$this->__settings_content_controls();
	}

	protected function __layout_content_controls() {

		$this->start_controls_section(
			'_section_post_layout',
			[
				'label' => __( 'Layout', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'post_carousel_layout_type',
			[
				'label' => __( 'Type', 'happy-addons-pro' ),
				'label_block' => true,
				'type' => Controls_Manager::SELECT,
				'default' => 'carousel',
                'options' => [
                    'carousel' => __('Carousel', 'happy-addons-pro'),
                    'remote_carousel' => __('Remote Carousel', 'happy-addons-pro'),
                ],
                'description' => __('Select layout type', 'happy-addons-pro')
			]
		);
        $this->add_control(
			'post_carousel_rcc_unique_id',
			[
				'label' => __( 'Unique ID', 'happy-addons-pro' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Enter remote carousel unique id', 'happy-addons-pro' ),
                'description' => __('Input carousel ID that you want to remotely connect', 'happy-addons-pro'),
                'condition' => [ 'post_carousel_layout_type' => 'remote_carousel' ]
			]
		);

		$this->add_control(
			'post_category',
			[
				'label'        => __( 'Show Badge', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-addons-pro' ),
				'label_off'    => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_featured_image',
			[
				'label'        => __( 'Show Featured Image', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-addons-pro' ),
				'label_off'    => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'post_image',
				'default'   => 'large',
				'exclude'   => [
					'custom',
				],
				'condition' => [
					'show_featured_image' => 'yes',
				],
			]
		);

		$this->add_control(
			'excerpt_show',
			[
				'label'        => __( 'Show Excerpt', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-addons-pro' ),
				'label_off'    => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'type'      => Controls_Manager::NUMBER,
				'label'     => __( 'Excerpt Length', 'happy-addons-pro' ),
				'default'   => 15,
				'condition' => [
					'excerpt_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'active_meta',
			[
				'type'        => Controls_Manager::SELECT2,
				'label'       => __( 'Active Meta', 'happy-addons-pro' ),
				'description' => __( 'Select to show and unselect to hide', 'happy-addons-pro' ),
				'label_block' => true,
				'multiple'    => true,
				'default'     => ['author', 'date'],
				'options'     => [
					'author' => __( 'Author', 'happy-addons-pro' ),
					'date'   => __( 'Date', 'happy-addons-pro' ),
				],
			]
		);

		$this->add_control(
			'post_author_meta',
			[
				'label'       => __( 'Author Avatar', 'happy-addons-pro' ),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'image',
				'toggle'      => false,
				'condition'   => [
					'active_meta' => 'author',
				],
				'options'     => [
					'none'  => __( 'None', 'happy-addons-pro' ),
					'image' => __( 'Image', 'happy-addons-pro' ),
					'icon'  => __( 'Icon', 'happy-addons-pro' ),
				],
			]
		);

		$this->add_control(
			'author_name_show',
			[
				'label'        => __( 'Author Name', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-addons-pro' ),
				'label_off'    => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'active_meta' => 'author',
				],
			]
		);

		$this->add_control(
			'author_icon',
			[
				'label'                  => __( 'Author Icon', 'happy-addons-pro' ),
				'type'                   => Controls_Manager::ICONS,
				'label_block'            => false,
				'skin'                   => 'inline',
				'exclude_inline_options' => [ 'svg' ],
				'default'                => [
					'value'   => 'hm hm-user-male',
					'library' => 'happy-icons',
				],
				'condition'              => [
					'post_author_meta' => 'icon',
					'active_meta'      => 'author',
				],
			]
		);

		$this->add_control(
			'date_icon',
			[
				'label'       => __( 'Date Icon', 'happy-addons-pro' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
				'default'     => [
					'value'   => 'hm hm-calendar2',
					'library' => 'happy-icons',
				],
				'condition'   => [
					'active_meta' => 'date',
				],
			]
		);

		$this->add_control(
			'layout',
			[
				'label'        => __( 'Layout', 'happy-addons-pro' ),
				'description'  => __( 'Make sure that <strong>Query > With Featured Image</strong> is enabled.', 'happy-addons-pro' ),
				'label_block'  => false,
				'type'         => Controls_Manager::CHOOSE,
				'default'      => 'under_image',
				'prefix_class' => 'ha-layout-',
				'toggle'       => false,
				'separator'    => 'before',
				'options'      => [
					'under_image' => [
						'title' => __( 'Content Under Image', 'happy-addons-pro' ),
						'icon'  => 'eicon-menu-bar',
					],
					'over_image'  => [
						'title' => __( 'Content Over Image', 'happy-addons-pro' ),
						'icon'  => 'eicon-clone',
					],
				],
			]
		);

		$this->add_control(
			'author_meta_position',
			[
				'label'                => __( 'Author Meta Position', 'happy-addons-pro' ),
				'label_block'          => false,
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'after_title',
				'options'              => [
					'after_title'   => __( 'After Title', 'happy-addons-pro' ),
					'after_content' => __( 'After Content', 'happy-addons-pro' ),
				],
				'selectors_dictionary' => [
					'after_title'   => 'flex-direction: column',
					'after_content' => 'flex-direction: column-reverse',
				],
				'prefix_class'         => 'ha-author-meta-',
				'selectors'            => [
					'{{WRAPPER}} .ha-posts-carousel__content-text' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'imge_position',
			[
				'label'                => __( 'Image Position', 'happy-addons-pro' ),
				'label_block'          => false,
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'under_image',
				'options'              => [
					'top'    => [
						'title' => __( 'Top', 'happy-addons-pro' ),
						'icon'  => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'happy-addons-pro' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'condition'            => [
					'layout' => 'under_image',
				],
				'selectors_dictionary' => [
					'top'    => 'flex-direction: column',
					'bottom' => 'flex-direction: column-reverse',
				],
				'selectors'            => [
					'{{WRAPPER}} .ha-posts-carousel' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_position',
			[
				'label'        => __( 'Content Position', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'top_left',
				'prefix_class' => 'ha-content-position-',
				'condition'    => [
					'layout' => 'over_image',
				],
				'options'      => [
					'top_left'      => __( 'Top Left', 'happy-addons-pro' ),
					'top_center'    => __( 'Top Center', 'happy-addons-pro' ),
					'top_right'     => __( 'Top Right', 'happy-addons-pro' ),
					'bottom_left'   => __( 'Bottom Left', 'happy-addons-pro' ),
					'bottom_center' => __( 'Bottom Center', 'happy-addons-pro' ),
					'bottom_right'  => __( 'Bottom Right', 'happy-addons-pro' ),
					'center_left'   => __( 'Center Left', 'happy-addons-pro' ),
					'center_center' => __( 'Center Center', 'happy-addons-pro' ),
					'center_right'  => __( 'Center Right', 'happy-addons-pro' ),
				],
			]
		);

		$this->add_control(
			'date_position',
			[
				'label'                => __( 'Date Position', 'happy-addons-pro' ),
				'label_block'          => false,
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'block',
				'toggle'               => false,
				'options'              => [
					'inline' => [
						'title' => __( 'Inline', 'happy-addons-pro' ),
						'icon'  => 'eicon-navigation-horizontal',
					],
					'block'  => [
						'title' => __( 'Block', 'happy-addons-pro' ),
						'icon'  => 'eicon-menu-bar',
					],
				],
				'selectors_dictionary' => [
					'inline' => 'flex-direction: row',
					'block'  => 'flex-direction: column',
				],
				'prefix_class'         => 'ha-date-position-',
				'selectors'            => [
					'{{WRAPPER}} .ha-posts-carousel__meta-author-name' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_alignment',
			[
				'label'        => __( 'Content Alignment', 'happy-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'flex-start' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'     => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'   => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'toggle'       => true,
				'prefix_class' => 'ha-content-',
				'selectors'    => [
					'{{WRAPPER}} .ha-posts-carousel__meta' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .ha-posts-carousel__content-wrap' => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'image_alignment',
			[
				'label'     => __( 'Image Alignment', 'happy-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'toggle'    => true,
				'condition' => [
					'feature_image' => 'yes',
					'layout'        => 'under_image',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__image' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __query_content_controls() {

		$this->start_controls_section(
			'_post_query',
			[
				'label' => __( 'Query', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->register_query_controls();

		$this->add_control(
			'post_per_page',
			[
				'label'   => __( 'Post Per Page', 'happy-addons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'step'    => 1,
				'max'     => 10000,
				'default' => 5,
			]
		);

		$this->end_controls_section();
	}

	protected function __settings_content_controls() {

		$this->start_controls_section(
			'_section_settings',
			[
				'label' => __( 'Carousel Settings', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'animation_speed',
			[
				'label'              => __( 'Animation Speed', 'happy-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 0,
				'step'               => 10,
				'max'                => 10000,
				'default'            => 800,
				'description'        => __( 'Slide speed in milliseconds', 'happy-addons-pro' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'              => __( 'Autoplay?', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => __( 'Yes', 'happy-addons-pro' ),
				'label_off'          => __( 'No', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'              => __( 'Autoplay Speed', 'happy-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 100,
				'step'               => 100,
				'max'                => 10000,
				'default'            => 2000,
				'description'        => __( 'Autoplay speed in milliseconds', 'happy-addons-pro' ),
				'condition'          => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'loop',
			[
				'label'              => __( 'Infinite Loop?', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => __( 'Yes', 'happy-addons-pro' ),
				'label_off'          => __( 'No', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'vertical',
			[
				'label'              => __( 'Vertical Mode?', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => __( 'Yes', 'happy-addons-pro' ),
				'label_off'          => __( 'No', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
				'style_transfer'     => true,
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'              => __( 'Navigation', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => [
					'none'  => __( 'None', 'happy-addons-pro' ),
					'arrow' => __( 'Arrow', 'happy-addons-pro' ),
					'dots'  => __( 'Dots', 'happy-addons-pro' ),
					'both'  => __( 'Arrow & Dots', 'happy-addons-pro' ),
				],
				'default'            => 'arrow',
				'frontend_available' => true,
				'style_transfer'     => true,
			]
		);

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label'              => __( 'Slides To Show', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => [
					1 => __( '1 Slide', 'happy-addons-pro' ),
					2 => __( '2 Slides', 'happy-addons-pro' ),
					3 => __( '3 Slides', 'happy-addons-pro' ),
					4 => __( '4 Slides', 'happy-addons-pro' ),
					5 => __( '5 Slides', 'happy-addons-pro' ),
					6 => __( '6 Slides', 'happy-addons-pro' ),
				],
				'desktop_default'    => 2,
				'tablet_default'     => 2,
				'mobile_default'     => 1,
				'frontend_available' => true,
				'style_transfer'     => true,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget style controls
	 */
	protected function register_style_controls() {
		$this->__items_style_controls();
		$this->__image_style_controls();
		$this->__badge_style_controls();
		$this->__content_style_controls();
		$this->__arrow_style_controls();
		$this->__dots_style_controls();
	}

	protected function __items_style_controls() {

		$this->start_controls_section(
			'_section_common_style',
			[
				'label' => __( 'Carousel Item', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'carousel_item_heght',
			[
				'label'     => __( 'Height', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 200,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'carousel_item_spacing',
			[
				'label'      => __( 'Item Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-posts-carousel-slick' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'carousel_item_border',
				'condition' => [
					'layout' => 'under_image',
				],
				'selector'  => '{{WRAPPER}} .ha-posts-carousel',
			]
		);

		$this->add_responsive_control(
			'carousel_item_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'condition'  => [
					'layout' => 'under_image',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-posts-carousel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'item_box_shadow',
				'selector'  => '{{WRAPPER}} .ha-posts-carousel',
				'condition' => [
					'layout' => 'under_image',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'carousel_item_background',
				'types'     => [ 'classic', 'gradient' ],
				'exclude'   => [ 'image' ],
				'selector'  => '{{WRAPPER}} .ha-posts-carousel',
				'condition' => [
					'layout' => 'under_image',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __image_style_controls() {

		$this->start_controls_section(
			'_section_feature_image',
			[
				'label' => __( 'Image', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'feature_image_note',
			[
				'label'     => false,
				'type'      => Controls_Manager::RAW_HTML,
				'raw'       => __( '<strong>Image</strong> is Switched off on "Query"', 'happy-addons-pro' ),
				'condition' => [
					'posts_only_with_featured_image!' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'feature_image_spacing',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'condition'  => [
					'layout' => 'under_image',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-posts-carousel__image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'feature_image_inner_spacing',
			[
				'label'      => __( 'Inner Spacing', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'condition'  => [
					'layout' => 'over_image',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-posts-carousel__content-position' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'feature_image_width',
			[
				'label'     => __( 'Width', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 2000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__feature-img img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'feature_image_height',
			[
				'label'     => __( 'Height', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 2000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__feature-img img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'feature_image_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-posts-carousel__feature-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_animation',
			[
				'label'        => __( 'Hover Animation', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-addons-pro' ),
				'label_off'    => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'ha-image-animation-',
				'condition'    => [
					'layout' => 'under_image',
				],
			]
		);

		$this->add_control(
			'image_overlay_color',
			[
				'label'     => __( 'Hover Overlay Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__image.ha-image-link a:hover .ha-posts-carousel__image-overlay' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-posts-carousel__feature-img:hover .ha-posts-carousel__image-overlay' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.ha-layout-over_image .ha-posts-carousel__feature-img .ha-posts-carousel__image-overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __badge_style_controls() {

		$this->start_controls_section(
			'_section_category_style',
			[
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'category_note',
			[
				'label'     => false,
				'type'      => Controls_Manager::RAW_HTML,
				'raw'       => __( '<strong>Badge</strong> is Switched off on "Layout"', 'happy-addons-pro' ),
				'condition' => [
					'post_category!' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'category_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-posts-carousel__meta-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'category_spacing',
			[
				'label'     => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__meta-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'category_border',
				'selector' => '{{WRAPPER}} .ha-posts-carousel__meta-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-posts-carousel__meta-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_box_shadow',
				'selector' => '.ha-posts-carousel__meta-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ha-posts-carousel__meta-category a',
			]
		);

		$this->start_controls_tabs( '_category_button' );

		$this->start_controls_tab(
			'_tab_category_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'category_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__meta-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__meta-category a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_category_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'category_hover_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__meta-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_hover_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__meta-category a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_hover_border_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'category_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__meta-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function __content_style_controls() {

		$this->start_controls_section(
			'_section_content_style',
			[
				'label' => __( 'Content', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding_under_image',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'condition'  => [
					'layout' => 'under_image',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-posts-carousel__content-position' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding_over_image',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'condition'  => [
					'layout' => 'over_image',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-posts-carousel__content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'condition'  => [
					'layout' => 'over_image',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-posts-carousel__content-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'content_over_image_background',
				'types'     => [ 'classic', 'gradient' ],
				'exclude'   => [ 'image' ],
				'selector'  => '{{WRAPPER}} .ha-posts-carousel__content-wrap',
				'condition' => [
					'layout' => 'over_image',
				],
			]
		);

		$this->add_control(
			'_heading_title',
			[
				'label'     => __( 'Title', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'     => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-posts-carousel__title, {{WRAPPER}} .ha-posts-carousel__title a',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-posts-carousel__title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => __( 'Hover Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'_heading_author_meta',
			[
				'label'     => __( 'Author Avatar', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'author_meta_space',
			[
				'label'     => __( 'Right Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__meta-author-img' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'author_icon_size',
			[
				'label'     => __( 'Icon/Image Size', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 5,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__meta-author-img i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-posts-carousel__meta-author-img svg' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-posts-carousel__meta-author-img img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'author_meta_icon_color',
			[
				'label'     => __( 'Icon Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'post_author_meta' => 'icon',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__meta-author-img i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-posts-carousel__meta-author-img svg' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'_heading_author_name',
			[
				'label'     => __( 'Author Name', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'author_name_note',
			[
				'label'     => false,
				'type'      => Controls_Manager::RAW_HTML,
				'raw'       => __( '<strong>Author Name</strong> is Switched off on "Layout"', 'happy-addons-pro' ),
				'condition' => [
					'author_name_show!' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'author_name_space',
			[
				'label'     => __( 'Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.ha-date-position-block .ha-posts-carousel__meta-author-name a' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-date-position-inline .ha-posts-carousel__meta-author-name a' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'author_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-posts-carousel__meta-author-name a',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'author_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__meta-author-name a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'author_hover_color',
			[
				'label'     => __( 'Hover Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__meta-author-name a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'_heading_date',
			[
				'label'     => __( 'Date', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'date_note',
			[
				'label'     => false,
				'type'      => Controls_Manager::RAW_HTML,
				'raw'       => __( '<strong>Date</strong> is not selected in "Layout > Active Meta"', 'happy-addons-pro' ),
				'condition' => [
					'active_meta!' => 'date',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'date_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-posts-carousel__meta-date',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'date_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__meta-date' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'_heading_content_excerpt',
			[
				'label'     => __( 'Excerpt', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'content_excerpt_note',
			[
				'label'     => false,
				'type'      => Controls_Manager::RAW_HTML,
				'raw'       => __( '<strong>Excerpt</strong> switched off on "Layout"', 'happy-addons-pro' ),
				'condition' => [
					'excerpt_show!' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'content_excerpt_spacing',
			[
				'label'     => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 5,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.ha-author-meta-after_content .ha-posts-carousel__content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_excerpt_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-posts-carousel__content',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'content_excerpt_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-posts-carousel__content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __arrow_style_controls() {

		$this->start_controls_section(
			'_section_style_arrow',
			[
				'label' => __( 'Navigation - Arrow', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'arrow_position_toggle',
			[
				'label'        => __( 'Position', 'happy-addons-pro' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __( 'None', 'happy-addons-pro' ),
				'label_on'     => __( 'Custom', 'happy-addons-pro' ),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_control(
			'arrow_sync_position',
			[
				'label'        => __( 'Sync Position', 'happy-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'options'      => [
					'yes' => [
						'title' => __( 'Yes', 'happy-addons-pro' ),
						'icon'  => 'eicon-sync',
					],
					'no'  => [
						'title' => __( 'No', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-stretch',
					],
				],
				'condition'    => [
					'arrow_position_toggle' => 'yes',
				],
				'default'      => 'no',
				'toggle'       => false,
				'prefix_class' => 'ha-arrow-sync-',
			]
		);

		$this->add_control(
			'sync_position_alignment',
			[
				'label'                => __( 'Alignment', 'happy-addons-pro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'condition'            => [
					'arrow_position_toggle' => 'yes',
					'arrow_sync_position'   => 'yes',
				],
				'default'              => 'center',
				'toggle'               => false,
				'selectors_dictionary' => [
					'left'   => 'left: 0',
					'center' => 'left: 50%',
					'right'  => 'left: 100%',
				],
				'selectors'            => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_position_y',
			[
				'label'      => __( 'Vertical', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition'  => [
					'arrow_position_toggle' => 'yes',
				],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 1000,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_position_x',
			[
				'label'      => __( 'Horizontal', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition'  => [
					'arrow_position_toggle' => 'yes',
				],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 1200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}.ha-arrow-sync-no .slick-prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-arrow-sync-no .slick-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-arrow-sync-yes .slick-next, {{WRAPPER}}.ha-arrow-sync-yes .slick-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_spacing',
			[
				'label'      => __( 'Space between Arrows', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition'  => [
					'arrow_position_toggle' => 'yes',
					'arrow_sync_position'   => 'yes',
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 40,
				],
				'selectors'  => [
					'{{WRAPPER}}.ha-arrow-sync-yes .slick-next' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_popover();

		$this->add_responsive_control(
			'arrow_size',
			[
				'label'      => __( 'Box Size', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => 5,
						'max' => 70,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .slick-prev' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-next' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_font_size',
			[
				'label'      => __( 'Icon Size', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => 2,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .slick-prev' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-next' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'arrow_border',
				'selector' => '{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next',
			]
		);

		$this->add_responsive_control(
			'arrow_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->start_controls_tabs( '_tabs_arrow' );

		$this->start_controls_tab(
			'_tab_arrow_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'arrow_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_arrow_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'arrow_hover_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_border_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'arrow_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function __dots_style_controls() {

		$this->start_controls_section(
			'_section_style_dots',
			[
				'label' => __( 'Navigation - Dots', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'dots_nav_position_y',
			[
				'label'      => __( 'Vertical Position', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .slick-dots' => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dots_nav_spacing',
			[
				'label'      => __( 'Space Between', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .slick-dots li' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2); margin-left: calc({{SIZE}}{{UNIT}} / 2);',
				],
			]
		);

		$this->add_responsive_control(
			'dots_nav_align',
			[
				'label'       => __( 'Alignment', 'happy-addons-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'toggle'      => true,
				'selectors'   => [
					'{{WRAPPER}} .slick-dots' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->start_controls_tabs( '_tabs_dots' );
		$this->start_controls_tab(
			'_tab_dots_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'dots_nav_size',
			[
				'label'      => __( 'Size', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .slick-dots li button:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dots_nav_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_dots_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'dots_nav_hover_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:hover:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_dots_active',
			[
				'label' => __( 'Active', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'dots_nav_active_size',
			[
				'label'      => __( 'Size', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .slick-dots li.slick-active button:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dots_nav_active_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-dots .slick-active button:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->post_carousel( $settings );
	}

	protected function post_carousel( $settings ) {
		$args                = $this->get_query_args();
		$args['numberposts'] = $settings['post_per_page'];

		$posts = get_posts( $args );

		if ( empty( $posts ) ) {
			if ( is_admin() ) {
				return printf( '<div class="ha-posts-carousel-error">%s</div>', __( 'Nothing Found. Please Add/Select Posts.', 'happy-addons-pro' ) );
			}
		}

		$harcc_uid = !empty($settings['post_carousel_rcc_unique_id']) && $settings['post_carousel_layout_type'] == 'remote_carousel' ? 'harccuid_' . $settings['post_carousel_rcc_unique_id'] : '';
		?>
		<div data-ha_rcc_uid="<?php echo esc_attr( $harcc_uid ); ?>" class="ha-posts-carousel-wrapper">
			<?php foreach ( $posts as $post ) : ?>
				<div class="ha-posts-carousel-slick slick-slide">
				<div class="ha-posts-carousel">

					<?php
					// if ( array_key_exists( 'meta_key', $args ) && $args['meta_key'] == '_thumbnail_id' ) :
					if ( has_post_thumbnail( $post->ID ) && 'yes' == $settings['show_featured_image'] ) :
						?>
						<div class="ha-posts-carousel__image ha-image-link">
							<div class="ha-posts-carousel__feature-img">
								<a href="<?php echo esc_url( get_the_permalink( $post->ID ) ); ?>">
									<span class="ha-posts-carousel__image-overlay"></span>
									<?php echo get_the_post_thumbnail( $post->ID, $settings['post_image_size'] ); ?>
								</a>
							</div>
						</div>
					<?php endif; ?>

					<div class="ha-posts-carousel__content-position">
					<div class="ha-posts-carousel__content-wrap">
						<?php
						if ( $settings['post_category'] == 'yes' ) :
							$categories = get_the_category( $post->ID );
							if ( ! empty( $categories ) ) :
								?>
								<div class="ha-posts-carousel__meta-category">
									<a href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>">
										<?php echo esc_html( $categories[0]->name ); ?>
									</a>
								</div>
								<?php
							endif;
						endif;
						?>

						<div class="ha-posts-carousel__title">
							<a href="<?php echo esc_url( get_the_permalink( $post->ID ) ); ?>">
								<?php echo esc_html( get_the_title( $post->ID ) ); ?>
							</a>
						</div>

						<div class="ha-posts-carousel__content-text">
							<div class="ha-posts-carousel__meta">
								<?php
								$author_nicename = get_the_author_meta( 'display_name', $post->post_author );
								$author_link     = get_the_author_meta( 'user_url', $post->post_author );

								if ( $settings['post_author_meta'] == 'icon' && ! empty( $settings['author_icon']['value'] ) ) :
									?>
									<div class="ha-posts-carousel__meta-author-img">
										<?php Icons_Manager::render_icon( $settings['author_icon'], [ 'aria-hidden' => 'true' ] ); ?>
									</div>
								<?php elseif ( $settings['post_author_meta'] == 'image' ) : ?>
									<div class="ha-posts-carousel__meta-author-img">
										<a href="<?php echo esc_url( $author_link ); ?>">
											<img src="<?php echo get_avatar_url( $post->post_author, ['size' => '45'] ); ?>" alt="<?php echo esc_attr( $author_nicename ); ?>" class="ha-posts-carousel__meta-image">
										</a>
									</div>
									<?php
								else :
									null;
								endif;
								?>

								<div class="ha-posts-carousel__meta-author-name">
									<?php if ( $settings['author_name_show'] == 'yes' ) : ?>
										<a href="<?php echo esc_url( $author_link ); ?>">
											<?php echo esc_html( $author_nicename ); ?>
										</a>
									<?php endif; ?>

									<div class="ha-posts-carousel__meta-date">
										<?php
										if ( isset( $settings['date_icon'] ) ) :
											if ( ! empty( $settings['date_icon']['value'] ) ) :
												?>
												<?php Icons_Manager::render_icon( $settings['date_icon'], [ 'aria-hidden' => 'true' ] ); ?>
											<?php endif; ?>
											<span><?php echo esc_html( get_the_date( get_option( 'date_format' ), $post->ID ) ); ?></span>
										<?php endif; ?>
									</div>
								</div>
							</div>

							<?php if ( $settings['excerpt_show'] == 'yes' ) : ?>
							<div class="ha-posts-carousel__content">
								<?php echo ha_pro_get_excerpt( $post->ID, $settings['excerpt_length'] ); ?>
							</div>
							<?php endif; ?>
						</div>

					</div>
					</div>

				</div>
				</div>
			<?php endforeach; ?>

		</div>
		<?php
	}

}
