<?php
	
	namespace ElementPack\Modules\ProductCarousel\Widgets;
	
	use ElementPack\Base\Module_Base;
	use Elementor\Group_Control_Css_Filter;
	use Elementor\Repeater;
	use Elementor\Controls_Manager;
	use Elementor\Group_Control_Box_Shadow;
	use Elementor\Group_Control_Image_Size;
	use Elementor\Group_Control_Typography;
	use Elementor\Group_Control_Text_Shadow;
	use Elementor\Group_Control_Background;
	use Elementor\Group_Control_Border;
	use Elementor\Icons_Manager;
	use ElementPack\Utils;

	use ElementPack\Traits\Global_Mask_Controls;
	use ElementPack\Traits\Global_Swiper_Controls;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	} // Exit if accessed directly
	
	class Product_Carousel extends Module_Base {

		use Global_Swiper_Controls;
    	use Global_Mask_Controls;
		
		public function get_name() {
			return 'bdt-product-carousel';
		}
		
		public function get_title() {
			return BDTEP . esc_html__( 'Product Carousel', 'bdthemes-element-pack' );
		}
		
		public function get_icon() {
			return 'bdt-wi-product-carousel bdt-new';
		}
		
		public function get_categories() {
			return [ 'element-pack' ];
		}
		
		public function get_keywords() {
			return [ 'product', 'carousel', 'client', 'logo', 'showcase' ];
		}
		
		public function get_style_depends() {
			if ( $this->ep_is_edit_mode() ) {
				return [ 'ep-styles' ];
			} else {
				return [ 'ep-font', 'ep-product-carousel' ];
			}
		}

		public function get_script_depends() {
			if ($this->ep_is_edit_mode()) {
				return ['ep-scripts'];
			} else {
				return ['ep-product-carousel'];
			}
		}
		
		public function get_custom_help_url() {
			return 'https://youtu.be/ZFpkJIctXic';
		}
		
		protected function register_controls() {
			
			$this->start_controls_section(
				'ep_section_product',
				[
					'label' => __( 'Product Items', 'bdthemes-element-pack' ),
					'tab'   => Controls_Manager::TAB_CONTENT,
				]
			);
			
			$repeater = new Repeater();
			
			$repeater->add_control(
				'image',
				[
					'label'   => __( 'Image', 'bdthemes-element-pack' ),
					'type'    => Controls_Manager::MEDIA,
					'default' => [
						'url' => Utils::get_placeholder_image_src(),
					],
				]
			);

			$repeater->add_control(
				'title',
				[
					'label'       => __( 'Title', 'bdthemes-element-pack' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => __( 'product title here', 'bdthemes-element-pack' ),
					'label_block' => true,
					'dynamic'     => [ 'active'      =>true ],
				]
			);
			
			$repeater->add_control(
				'price',
				[
					'label'       => __('Price', 'bdthemes-element-pack'),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => [
						'active' => true,
					],
					'default' => '$204',
					'label_block' => true,
				]
			);
	
			$repeater->add_control(
				'text',
				[
					'label'       => __('Text', 'bdthemes-element-pack'),
					'type'        => Controls_Manager::WYSIWYG,
					'dynamic'     => [
						'active' => true,
					],
					'default'     => __('Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bdthemes-element-pack'),
					'placeholder' => __('Enter your text', 'bdthemes-element-pack'),
				]
			);
	
			$repeater->add_control(
				'readmore_link',
				[
					'label'       => esc_html__( 'Link', 'bdthemes-element-pack' ),
					'type'        => Controls_Manager::URL,
					'dynamic'     => [ 'active' => true ],
					'placeholder' => 'http://your-link.com',
					'default'     => [
						'url' => '#',
					],
				]
			);

			$repeater->add_control(
				'rating_number',
				[
					'label' => __( 'Rating', 'bdthemes-element-pack' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'default' => [
						'size' => 4.5,
					],
					'range' => [
						'px' => [
							'min' => .5,
							'max' => 5,
							'step' => .5,
						],
					],
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$repeater->add_control(
				'rating_count',
				[
					'label'       => __('Rating Count', 'bdthemes-element-pack'),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => [
						'active' => true,
					],
					'default' => '(10,678)',
					'label_block' => true,
				]
			);

			$repeater->add_control(
				'time',
				[
					'label'       => __('Time', 'bdthemes-element-pack'),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => [
						'active' => true,
					],
					'default' => __('1 hour 10 mins', 'bdthemes-element-pack'),
					'label_block' => true,
				]
			);

			$repeater->add_control(
				'badge_text',
				[
					'label'       => __('Badge Text', 'bdthemes-element-pack'),
					'type'        => Controls_Manager::TEXT,
					'default'     => 'Sale',
					'placeholder' => 'Type Badge text',
					'dynamic'     => [
						'active' => true,
					],
				]
			);
			
			$this->add_control(
				'product_items',
				[
					'show_label'  => false,
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'title_field' => '{{{ title }}}',
					'default'     => [
						['title' => __( 'Pizza', 'bdthemes-element-pack' )],
						['title' => __( 'Burger', 'bdthemes-element-pack' )],
						['title' => __( 'Chicken', 'bdthemes-element-pack' )],
						['title' => __( 'Milkshake', 'bdthemes-element-pack' )],
						['title' => __( 'Ice Tea', 'bdthemes-element-pack' )],
						['title' => __( 'Pasta', 'bdthemes-element-pack' )],
					]
				]
			);

			$this->end_controls_section();
			
			$this->start_controls_section(
				'section_additional_settings',
				[
					'label' => __( 'Additional Settings', 'bdthemes-element-pack' ),
					'tab'   => Controls_Manager::TAB_CONTENT,
				]
			);
			
			$this->add_responsive_control(
				'columns',
				[
					'label'          => __( 'Columns', 'bdthemes-element-pack' ),
					'type'           => Controls_Manager::SELECT,
					'default'        => 3,
					'tablet_default' => 2,
					'mobile_default' => 1,
					'options'        => [
						1 => '1',
						2 => '2',
						3 => '3',
						4 => '4',
						5 => '5',
						6 => '6',
					],
				]
			);
	
			$this->add_control(
				'item_gap',
				[
					'label'   => __( 'Item Gap', 'bdthemes-element-pack' ),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
						'size' => 20,
					],
					'range'   => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
				]
			);
	
			$this->add_control(
				'item_match_height',
				[
					'label'        => __( 'Item Match Height', 'ultimate-post-kit' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'yes',
					'prefix_class' => 'bdt-item-match-height--',
					'render_type' => 'template'
				]
			);
			
			$this->add_control(
				'show_title',
				[
					'label'   => __('Show Name', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'separator' => 'before',
				]
			);
	
			$this->add_control(
				'title_tag',
				[
					'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'h3',
					'options' => element_pack_title_tags(),
					'condition' => [
						'show_title' => 'yes',
					]
				]
			);
	
			$this->add_control(
				'show_price',
				[
					'label'   => __('Show Price', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'separator' => 'before',
				]
			);
	
			$this->add_control(
				'show_time',
				[
					'label'   => __('Show Time', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'separator' => 'before',
				]
			);
	
			$this->add_control(
				'show_text',
				[
					'label'   => __('Show Text', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'separator' => 'before',
				]
			);
	
			$this->add_control(
				'readmore_link_to',
				[
					'label'   => __( 'Link to', 'bdthemes-element-pack' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'button',
					'options' => [
						'button' => __( 'Button', 'bdthemes-element-pack' ),
						'title' => __( 'Title', 'bdthemes-element-pack' ),
						'image' => __( 'Image', 'bdthemes-element-pack' ),
						'item' => __( 'Item Wrapper', 'bdthemes-element-pack' ),
					],
				]
			);

			$this->add_control(
				'show_rating',
				[
					'label'   => __('Show Rating', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'separator' => 'before'
				]
			);
	
			$this->add_control(
				'rating_type',
				[
					'label'   => __( 'Rating Type', 'bdthemes-element-pack' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'number',
					'options' => [
						'star'   => __( 'Star', 'bdthemes-element-pack' ),
						'number' => __( 'Number', 'bdthemes-element-pack' ),
					],
					'condition' => [
						'show_rating' => 'yes'
					]
				]
			);
	
			$this->add_control(
				'badge',
				[
					'label' => __('Badge', 'bdthemes-element-pack'),
					'type'  => Controls_Manager::SWITCHER,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'show_image',
				[
					'label'   => __('Show Iamge', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'separator' => 'before',
				]
			);
	
			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name'    => 'thumbnail_size',
					'default' => 'medium',
					'condition' => [
						'show_image' => 'yes'
					]
				]
			);
	
			$this->add_control(
				'image_mask_popover',
				[
					'label'        => esc_html__('Image Mask', 'bdthemes-element-pack'),
					'type'         => Controls_Manager::POPOVER_TOGGLE,
					'render_type'  => 'template',
					'return_value' => 'yes',
					'condition' => [
						'show_image' => 'yes'
					]
				]
			);
	
			//Global Image Mask Controls
			$this->register_image_mask_controls();
	
			$this->add_responsive_control(
				'text_align',
				[
					'label'     => __('Alignment', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => [
						'left'    => [
							'title' => __('Left', 'bdthemes-element-pack'),
							'icon'  => 'eicon-text-align-left',
						],
						'center'  => [
							'title' => __('Center', 'bdthemes-element-pack'),
							'icon'  => 'eicon-text-align-center',
						],
						'right'   => [
							'title' => __('Right', 'bdthemes-element-pack'),
							'icon'  => 'eicon-text-align-right',
						],
						'justify' => [
							'title' => __('Justified', 'bdthemes-element-pack'),
							'icon'  => 'eicon-text-align-justify',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-item' => 'text-align: {{VALUE}};',
					],
					'separator' => 'before'
				]
			);
	
			$this->end_controls_section();
	
			$this->start_controls_section(
				'section_content_readmore',
				[
					'label'     => esc_html__( 'Read More', 'bdthemes-element-pack' ),
					'condition' => [
						'readmore_link_to' => 'button',
					],
				]
			);

			$this->add_control(
				'readmore_text',
				[
					'label'       => esc_html__( 'Read More Text', 'bdthemes-element-pack' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Read More', 'bdthemes-element-pack' ),
					'placeholder' => esc_html__( 'Read More', 'bdthemes-element-pack' ),
				]
			);
	
			$this->add_control(
				'readmore_icon',
				[
					'label'       => esc_html__( 'Icon', 'bdthemes-element-pack' ),
					'type'        => Controls_Manager::ICONS,
					'label_block' => false,
					'skin' => 'inline'
				]
			);
	
			$this->add_control(
				'icon_align',
				[
					'label'   => esc_html__( 'Icon Position', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::CHOOSE,
					'default' => 'right',
					'toggle' => false,
					'options'   => [
						'left' => [
							'title' => __( 'Left', 'bdthemes-element-pack' ),
							'icon' => 'eicon-h-align-left',
						],
						'right' => [
							'title' => __( 'Right', 'bdthemes-element-pack' ),
							'icon' => 'eicon-h-align-right',
						],
					],
					'condition' => [
						'readmore_icon[value]!' => '',
					],
				]
			);
	
			$this->add_responsive_control(
				'icon_indent',
				[
					'label'   => esc_html__( 'Icon Spacing', 'bdthemes-element-pack' ),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
						'size' => 8,
					],
					'range' => [
						'px' => [
							'max' => 50,
						],
					],
					'condition' => [
						'readmore_icon[value]!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-readmore .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .bdt-ep-product-carousel-readmore .bdt-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
					],
				]
			);
	
			$this->end_controls_section();

			$this->start_controls_section(
				'section_content_badge',
				[
					'label'     => __('Badge', 'bdthemes-element-pack'),
					'condition' => [
						'badge' => 'yes',
					],
				]
			);
	
			$this->add_control(
				'badge_position',
				[
					'label'   => esc_html__('Position', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'top-right',
					'options' => element_pack_position(),
				]
			);
	
			$this->add_control(
				'badge_offset_toggle',
				[
					'label' => __('Offset', 'bdthemes-element-pack'),
					'type' => Controls_Manager::POPOVER_TOGGLE,
					'label_off' => __('None', 'bdthemes-element-pack'),
					'label_on' => __('Custom', 'bdthemes-element-pack'),
					'return_value' => 'yes',
				]
			);
	
			$this->start_popover();
	
			$this->add_responsive_control(
				'badge_horizontal_offset',
				[
					'label' => __('Horizontal Offset', 'bdthemes-element-pack'),
					'type'  => Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'tablet_default' => [
						'size' => 0,
					],
					'mobile_default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min'  => -300,
							'step' => 1,
							'max'  => 300,
						],
					],
					'condition' => [
						'badge_offset_toggle' => 'yes'
					],
					'render_type' => 'ui',
					'selectors' => [
						'{{WRAPPER}}' => '--ep-badge-h-offset: {{SIZE}}px;'
					],
				]
			);
	
			$this->add_responsive_control(
				'badge_vertical_offset',
				[
					'label' => __('Vertical Offset', 'bdthemes-element-pack'),
					'type'  => Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'tablet_default' => [
						'size' => 0,
					],
					'mobile_default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min'  => -300,
							'step' => 1,
							'max'  => 300,
						],
					],
					'condition' => [
						'badge_offset_toggle' => 'yes'
					],
					'render_type' => 'ui',
					'selectors' => [
						'{{WRAPPER}}' => '--ep-badge-v-offset: {{SIZE}}px;'
					],
				]
			);
	
			$this->add_responsive_control(
				'badge_rotate',
				[
					'label'   => esc_html__('Rotate', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'tablet_default' => [
						'size' => 0,
					],
					'mobile_default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min'  => -360,
							'max'  => 360,
							'step' => 5,
						],
					],
					'condition' => [
						'badge_offset_toggle' => 'yes'
					],
					'render_type' => 'ui',
					'selectors' => [
						'{{WRAPPER}}' => '--ep-badge-rotate: {{SIZE}}deg;'
					],
				]
			);
	
			$this->end_popover();
	
			$this->end_controls_section();

			//Navigation Controls
			$this->start_controls_section(
				'section_content_navigation',
				[
					'label' => __( 'Navigation', 'bdthemes-element-pack' ),
				]
			);
	
			//Global Navigation Controls
			$this->register_navigation_controls();
	
			$this->end_controls_section();
	
			//Global Carousel Settings Controls
			$this->register_carousel_settings_controls();
	
			//Style
			$this->start_controls_section(
				'section_style_carousel_items',
				[
					'label'     => esc_html__( 'Items', 'bdthemes-element-pack' ),
					'tab'       => Controls_Manager::TAB_STYLE,
				]
			);
	
			$this->add_responsive_control(
				'content_padding',
				[
					'label'      => esc_html__( 'Content Padding', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .bdt-ep-product-carousel-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
	
			$this->start_controls_tabs( 'tabs_item_style' );
	
			$this->start_controls_tab(
				'tab_item_normal',
				[
					'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
				]
			);
	
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'item_background',
					'selector'  => '{{WRAPPER}} .bdt-ep-product-carousel-item',
				]
			);
	
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'      => 'item_border',
					'selector'  => '{{WRAPPER}} .bdt-ep-product-carousel-item',
					'separator' => 'before',
				]
			);
	
			$this->add_responsive_control(
				'item_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .bdt-ep-product-carousel-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
	
			$this->add_responsive_control(
				'item_padding',
				[
					'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .bdt-ep-product-carousel-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
	
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'item_box_shadow',
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-item',
				]
			);
	
			$this->add_responsive_control(
				'item_shadow_padding',
				[
					'label'       => __( 'Match Padding', 'bdthemes-element-pack' ),
					'description' => __( 'You have to add padding for matching overlaping normal/hover box shadow when you used Box Shadow option.', 'bdthemes-element-pack' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => [
						'px' => [
							'min'  => 0,
							'step' => 1,
							'max'  => 50,
						]
					],
					'selectors'   => [
						'{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};'
					],
				]
			);
	
			$this->end_controls_tab();
	
			$this->start_controls_tab(
				'tab_item_hover',
				[
					'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
				]
			);
	
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'item_hover_background',
					'selector'  => '{{WRAPPER}} .bdt-ep-product-carousel-item:hover',
				]
			);
	
			$this->add_control(
				'item_hover_border_color',
				[
					'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'condition' => [
						'item_border_border!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-item:hover' => 'border-color: {{VALUE}};',
					],
				]
			);
	
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'item_hover_box_shadow',
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-item:hover',
				]
			);
	
			$this->end_controls_tab();
	
			$this->end_controls_tabs();
	
			$this->end_controls_section();
	
			$this->start_controls_section(
				'section_style_image',
				[
					'label' => __('Image', 'bdthemes-element-pack'),
					'tab'   => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_image' => 'yes'
					]
				]
			);
	
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'     => 'image_border',
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-image img'
				]
			);
	
			$this->add_control(
				'iamge_radius',
				[
					'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', 'em', '%'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-ep-product-carousel-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
	
			$this->add_responsive_control(
				'iamge_padding',
				[
					'label'      => __('Padding', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', 'em', '%'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-ep-product-carousel-image img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
	
			$this->add_responsive_control(
				'image_spacing',
				[
					'label'     => __('Spacing', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::SLIDER,
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);
	
			$this->add_group_control(
				Group_Control_Css_Filter::get_type(),
				[
					'name'     => 'css_filters',
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-image img',
				]
			);
	
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'img_shadow',
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-image img'
				]
			);
	
			$this->end_controls_section();
	
			$this->start_controls_section(
				'section_style_title',
				[
					'label' => __('Title', 'bdthemes-element-pack'),
					'tab'   => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_title' => 'yes',
					]
				]
			);
	
			$this->add_control(
				'title_color',
				[
					'label'     => __('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-title' => 'color: {{VALUE}};',
					],
				]
			);
	
			$this->add_responsive_control(
				'title_bottom_space',
				[
					'label'     => __('Spacing', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);
	
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'title_typography',
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-title',
				]
			);
	
			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				[
					'name' => 'title_shadow',
					'label' => __( 'Text Shadow', 'bdthemes-element-pack' ),
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-title',
				]
			);
	
			$this->end_controls_section();
	
			$this->start_controls_section(
				'section_style_price',
				[
					'label' => __('Price', 'bdthemes-element-pack'),
					'tab'   => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_price' => 'yes',
					]
				]
			);
	
			$this->add_control(
				'price_color',
				[
					'label'     => __('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-price' => 'color: {{VALUE}};',
					],
				]
			);
	
			$this->add_responsive_control(
				'price_bottom_space',
				[
					'label'     => __('Spacing', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-price' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);
	
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'price_typography',
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-price',
				]
			);
	
			$this->end_controls_section();

			
	
			$this->start_controls_section(
				'section_style_text',
				[
					'label' => __('Text', 'bdthemes-element-pack'),
					'tab'   => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_text' => 'yes',
					]
				]
			);
	
			$this->add_control(
				'text_color',
				[
					'label'     => __('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-text' => 'color: {{VALUE}};',
					],
				]
			);
	
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'text_typography',
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-text',
				]
			);
	
			$this->end_controls_section();
	
			$this->start_controls_section(
				'section_style_readmore',
				[
					'label'     => esc_html__( 'Read More', 'bdthemes-element-pack' ),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'readmore_link_to' => 'button',
					],
				]
			);
	
			$this->start_controls_tabs( 'tabs_readmore_style' );
	
			$this->start_controls_tab(
				'tab_readmore_normal',
				[
					'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
				]
			);
	
			$this->add_control(
				'readmore_color',
				[
					'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-readmore' => 'color: {{VALUE}};',
						'{{WRAPPER}} .bdt-ep-product-carousel-readmore svg' => 'fill: {{VALUE}};',
					],
				]
			);
	
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'readmore_background',
					'selector'  => '{{WRAPPER}} .bdt-ep-product-carousel-readmore',
				]
			);
	
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'        => 'readmore_border',
					'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
					'placeholder' => '1px',
					'default'     => '1px',
					'selector'    => '{{WRAPPER}} .bdt-ep-product-carousel-readmore',
					'separator'   => 'before',
				]
			);
	
			$this->add_control(
				'readmore_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .bdt-ep-product-carousel-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
	
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'readmore_box_shadow',
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-readmore',
				]
			);
	
			$this->add_responsive_control(
				'readmore_padding',
				[
					'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .bdt-ep-product-carousel-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
	
			$this->add_responsive_control(
				'readmore_margin',
				[
					'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .bdt-ep-product-carousel-readmore' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
	
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'readmore_typography',
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-readmore',
				]
			);
	
			$this->end_controls_tab();
	
			$this->start_controls_tab(
				'tab_readmore_hover',
				[
					'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
				]
			);
	
			$this->add_control(
				'readmore_hover_color',
				[
					'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-readmore:hover' => 'color: {{VALUE}};',
						'{{WRAPPER}} .bdt-ep-product-carousel-readmore:hover svg' => 'fill: {{VALUE}};',
					],
				]
			);
	
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'readmore_hover_background',
					'selector'  => '{{WRAPPER}} .bdt-ep-product-carousel-readmore:hover',
				]
			);
	
			$this->add_control(
				'readmore_hover_border_color',
				[
					'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'condition' => [
						'readmore_border_border!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-readmore:hover' => 'border-color: {{VALUE}};',
					],
				]
			);
	
			$this->add_control(
				'readmore_hover_animation',
				[
					'label' => esc_html__( 'Animation', 'bdthemes-element-pack' ),
					'type'  => Controls_Manager::HOVER_ANIMATION,
				]
			);
	
			$this->end_controls_tab();
	
			$this->end_controls_tabs();
	
			$this->end_controls_section();

			$this->start_controls_section(
				'section_style_rating',
				[
					'label'     => esc_html__('Rating', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_rating' => 'yes',
					],
				]
			);
	
			$this->add_control(
				'rating_color',
				[
					'label'     => esc_html__('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#e7e7e7',
					'selectors' => [
						'{{WRAPPER}} .epsc-rating-item' => 'color: {{VALUE}};',
					],
					'condition' => [
						'rating_type' => 'star',
					],
				]
			);
	
			$this->add_control(
				'active_rating_color',
				[
					'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#FFCC00',
					'selectors' => [
						'{{WRAPPER}} .epsc-rating[class*=" epsc-rating-0"] .epsc-rating-item:nth-child(1) i:after, {{WRAPPER}} .epsc-rating[class*=" epsc-rating-1"] .epsc-rating-item:nth-child(-n+1) i:after, {{WRAPPER}} .epsc-rating[class*=" epsc-rating-2"] .epsc-rating-item:nth-child(-n+2) i:after, {{WRAPPER}} .epsc-rating[class*=" epsc-rating-3"] .epsc-rating-item:nth-child(-n+3) i:after, {{WRAPPER}} .epsc-rating[class*=" epsc-rating-4"] .epsc-rating-item:nth-child(-n+4) i:after, {{WRAPPER}} .epsc-rating[class*=" epsc-rating-5"] .epsc-rating-item:nth-child(-n+5) i:after, .epsc-rating.epsc-rating-0-5 .epsc-rating-item:nth-child(1) i:after, {{WRAPPER}} .epsc-rating.epsc-rating-1-5 .epsc-rating-item:nth-child(2) i:after, {{WRAPPER}} .epsc-rating.epsc-rating-2-5 .epsc-rating-item:nth-child(3) i:after, {{WRAPPER}} .epsc-rating.epsc-rating-3-5 .epsc-rating-item:nth-child(4) i:after, {{WRAPPER}} .epsc-rating.epsc-rating-4-5 .epsc-rating-item:nth-child(5) i:after' => 'color: {{VALUE}};',
					],
					'condition' => [
						'rating_type' => 'star',
					],
				]
			);
	
			$this->add_control(
				'rating_number_color',
				[
					'label'     => esc_html__('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#FFCC00',
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-rating' => 'color: {{VALUE}};',
					],
					'condition' => [
						'rating_type' => 'number',
					],
				]
			);
	
			$this->add_control(
				'rating_background_color',
				[
					'label' => __( 'Background Color', 'bdthemes-element-pack' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-rating' => 'background-color: {{VALUE}};',
					],
					'condition' => [
						'rating_type' => 'number',
					],
				]
			);
	
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'rating_border',
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-rating',
					'condition' => [
						'rating_type' => 'number',
					],
				]
			);
	
			$this->add_responsive_control(
				'rating_border_radius',
				[
					'label' => __( 'Border Radius', 'bdthemes-element-pack' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-rating' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'rating_type' => 'number',
					],
				]
			);
	
			$this->add_responsive_control(
				'rating_padding',
				[
					'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', 'em', '%'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-ep-product-carousel-rating' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'rating_type' => 'number',
					],
				]
			);
	
			$this->add_responsive_control(
				'rating_margin',
				[
					'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-ep-product-carousel-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
	
			$this->add_responsive_control(
				'rating_size',
				[
					'label' => esc_html__('Size', 'bdthemes-element-pack'),
					'type'  => Controls_Manager::SLIDER,
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-rating' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);
	
			$this->add_responsive_control(
				'rating_space_between',
				[
					'label' => esc_html__('Space Between', 'bdthemes-element-pack'),
					'type'  => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 10,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-rating i + i' => 'margin-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .bdt-ep-product-carousel-rating span' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'rating_count_color',
				[
					'label'     => esc_html__('Count Text Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-rating-count' => 'color: {{VALUE}};',
					],
					'separator' => 'before'
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'rating_count_typography',
					'label'     => esc_html__('Count Text Typography', 'bdthemes-element-pack'),
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-rating-count',
				]
			);
	
			$this->end_controls_section();

			$this->start_controls_section(
				'section_style_time',
				[
					'label' => __('Time', 'bdthemes-element-pack'),
					'tab'   => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_time' => 'yes',
					]
				]
			);
	
			$this->add_control(
				'time_color',
				[
					'label'     => __('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-time' => 'color: {{VALUE}};',
					],
				]
			);
	
			$this->add_responsive_control(
				'time_bottom_space',
				[
					'label'     => __('Spacing', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-time' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);
	
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'time_typography',
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-time',
				]
			);
	
			$this->end_controls_section();

			$this->start_controls_section(
				'section_style_badge',
				[
					'label'     => __('Badge', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'badge' => 'yes',
					],
				]
			);
	
			$this->add_control(
				'badge_text_color',
				[
					'label'     => __('Text Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-ep-product-carousel-badge span' => 'color: {{VALUE}};',
					],
				]
			);
	
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'badge_background',
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-badge span',
				]
			);
	
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'        => 'badge_border',
					'placeholder' => '1px',
					'default'     => '1px',
					'selector'    => '{{WRAPPER}} .bdt-ep-product-carousel-badge span'
				]
			);
	
			$this->add_responsive_control(
				'badge_radius',
				[
					'label'      => __('Border Radius', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-ep-product-carousel-badge span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
	
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'badge_shadow',
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-badge span',
				]
			);
	
			$this->add_responsive_control(
				'badge_padding',
				[
					'label'      => __('Padding', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', 'em', '%'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-ep-product-carousel-badge span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
	
			$this->add_responsive_control(
				'badge_margin',
				[
					'label'      => __('Margin', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', 'em', '%'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-interactive-card .bdt-ep-product-carousel-badge.bdt-position-small' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
	
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'badge_typography',
					'selector' => '{{WRAPPER}} .bdt-ep-product-carousel-badge span',
				]
			);
	
			$this->end_controls_section();

			//Navigation Style
			$this->start_controls_section(
				'section_style_navigation',
				[
					'label'      => __( 'Navigation', 'bdthemes-element-pack' ),
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

		public function render_image($item, $image_key) {
			$settings = $this->get_settings_for_display();
	
			if ( ! $settings['show_image'] ) {
				return;
			}
	
			$thumb_url = Group_Control_Image_Size::get_attachment_image_src($item['image']['id'], 'thumbnail_size', $settings);
			if ( !$thumb_url ) {
				$thumb_url = $item['image']['url'];
			}

			$this->add_render_attribute( $image_key, 'class', 'bdt-ep-product-carousel-image-link bdt-position-z-index', true );
			if (!empty($item['readmore_link'])) {
				$this->add_link_attributes($image_key, $item['readmore_link']);
			}

			$image_mask = $settings['image_mask_popover'] == 'yes' ? ' bdt-image-mask' : '';
			$this->add_render_attribute('image-wrap', 'class', 'bdt-ep-product-carousel-image' . $image_mask);
	
			?>
			<div <?php echo $this->get_render_attribute_string('image-wrap'); ?>>

				<?php 
				$thumb_url = Group_Control_Image_Size::get_attachment_image_src($item['image']['id'], 'thumbnail_size', $settings);
				if (!$thumb_url) {
					printf('<img src="%1$s" alt="%2$s">', $item['image']['url'], esc_html($item['title']));
				} else {
					print(wp_get_attachment_image(
						$item['image']['id'],
						$settings['thumbnail_size_size'],
						false,
						[
							'alt' => esc_html($item['title'])
						]
					));
				}
				?>

				<?php if($settings['readmore_link_to'] == 'image') : ?>
				<a <?php echo $this->get_render_attribute_string( $image_key ); ?>></a>
				<?php endif; ?>
			</div>
			<?php
		}
	
		public function render_title($item, $title_key) {
			$settings = $this->get_settings_for_display();
	
			if ( ! $settings['show_title'] ) {
				return;
			}

			$this->add_render_attribute( $title_key, 'class', 'bdt-ep-product-carousel-title-link', true );
			if (!empty($item['readmore_link'])) {
				$this->add_link_attributes($title_key, $item['readmore_link']);
			}
	
			$this->add_render_attribute('title-wrap', 'class', 'bdt-ep-product-carousel-title', true);
	
			?>
			<?php if ( $item['title'] ) : ?>
				<<?php echo Utils::get_valid_html_tag($settings['title_tag']); ?> <?php echo $this->get_render_attribute_string('title-wrap'); ?>>
					<?php echo wp_kses($item['title'], element_pack_allow_tags('title')); ?>
					<?php if($settings['readmore_link_to'] == 'title') : ?>
					<a <?php echo $this->get_render_attribute_string( $title_key ); ?>></a>
					<?php endif; ?>
				</<?php echo Utils::get_valid_html_tag($settings['title_tag']); ?>>
			<?php endif; ?>
			<?php
		}
	
		public function render_price($item) {
			$settings = $this->get_settings_for_display();
	
			if ( ! $settings['show_price'] ) {
				return;
			}
	
			$this->add_render_attribute('price-wrap', 'class', 'bdt-ep-product-carousel-price', true);
	
			?>
			<?php if ( $item['price'] ) : ?>
				<div <?php echo $this->get_render_attribute_string('price-wrap'); ?>>
					<?php echo wp_kses($item['price'], element_pack_allow_tags('price')); ?>
				</div>
			<?php endif; ?>
			<?php
		}
	
		public function render_time($item) {
			$settings = $this->get_settings_for_display();
	
			if ( ! $settings['show_time'] ) {
				return;
			}
	
			$this->add_render_attribute('time-wrap', 'class', 'bdt-ep-product-carousel-time', true);
	
			?>
			<?php if ( $item['time'] ) : ?>
				<div <?php echo $this->get_render_attribute_string('time-wrap'); ?>>
					<i class="ep-icon-clock-o" aria-hidden="true"></i>
					<?php echo wp_kses($item['time'], element_pack_allow_tags('time')); ?>
				</div>
			<?php endif; ?>
			<?php
		}
	
		public function render_text($item) {
			$settings = $this->get_settings_for_display();
	
			if ( ! $settings['show_text'] ) {
				return;
			}
	
			?>
			<?php if ( $item['text'] ) : ?>
				<div class="bdt-ep-product-carousel-text">
					<?php echo wp_kses_post( $item['text'] ); ?>
				</div>
			<?php endif; ?>
			<?php
		}
	
		public function render_readmore($item, $readmore_key) {
			$settings = $this->get_settings_for_display();

			$this->add_render_attribute(
				[
					$readmore_key => [
						'class' => [
							'bdt-ep-product-carousel-readmore',
							$settings['readmore_hover_animation'] ? 'elementor-animation-' . $settings['readmore_hover_animation'] : '',
						],
					]
				], '', '', true
			);
			if (!empty($item['readmore_link'])) {
				$this->add_link_attributes($readmore_key, $item['readmore_link']);
			}
	
			?>
			<?php if (( ! empty( $item['readmore_link']['url'] )) && ( $settings['readmore_link_to'] == 'button' )): ?>
				<div class="bdt-ep-product-carousel-readmore-wrap">
					<a <?php echo $this->get_render_attribute_string( $readmore_key ); ?>>
						<?php echo esc_html($settings['readmore_text']); ?>
						<?php if ($settings['readmore_icon']['value']) : ?>
							<span class="bdt-button-icon-align-<?php echo esc_attr($settings['icon_align']); ?>">
								<?php Icons_Manager::render_icon( $settings['readmore_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] ); ?>
							</span>
						<?php endif; ?>
					</a>
				</div>
			<?php endif; ?>
			<?php
		}

		public function render_review_rating($item) {
			$settings = $this->get_settings_for_display();
	
			if ( !$settings['show_rating'] ) {
				return;
			}
	
			$rating_number = $item['rating_number']['size'];
	
			if (preg_match('/\./', $rating_number)) {
				$ratingValue = explode(".",$rating_number);
				$firstVal    = ( $ratingValue[0] <= 5 ) ? $ratingValue[0] : 5;
				$secondVal   = ( $ratingValue[1] < 5 ) ? 0 : 5;
			} else {
				$firstVal    = ( $rating_number <= 5 ) ? $rating_number : 5;
				$secondVal   = 0;
			}
			
			$score       = $firstVal . '-' . $secondVal;
			
	
			?>
			<div>
				<div class="bdt-ep-product-carousel-rating bdt-flex-inline bdt-flex-middle bdt-<?php echo esc_attr($settings['rating_type']) ?>">
					<?php if ( $settings['rating_type'] === 'number' ) : ?>
						<span><?php echo esc_html( $item['rating_number']['size'] ); ?></span>
						<i class="ep-icon-star-full" aria-hidden="true"></i>
					<?php else : ?>
						<span class="epsc-rating epsc-rating-<?php echo $score; ?>">
							<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
							<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
							<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
							<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
							<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
						</span>
						<?php endif; ?>
				</div>
				<span class="bdt-ep-product-carousel-rating-count"><?php echo esc_html( $item['rating_count'] ); ?></span>
			</div>
			<?php
		}

		public function render_badge($item) {
			$settings = $this->get_settings_for_display();
	
			?>
			<?php if ( $settings['badge'] and '' != $item['badge_text'] ) : ?>
				<div class="bdt-ep-product-carousel-badge bdt-position-small bdt-position-<?php echo esc_attr($settings['badge_position']); ?>">
					<span class="bdt-badge bdt-padding-small"><?php echo esc_html($item['badge_text']); ?></span>
				</div>
			<?php endif; ?>
			<?php
		}

		public function render_carosuel_item() {
			$settings = $this->get_settings_for_display();
	
			if ( empty($settings['product_items'] ) ) {
				return;
			}
	
			$this->add_render_attribute('item-wrap', 'class', 'bdt-ep-product-carousel-item swiper-slide', true);

			?>

			<?php foreach ( $settings['product_items'] as $index => $item ) : 
				
				$item_key = 'item_' . $index;
				$this->add_render_attribute( $item_key, 'class', 'bdt-ep-product-carousel-item-link bdt-position-z-index', true );

				if (!empty($item['readmore_link'])) {
					$this->add_link_attributes($item_key, $item['readmore_link']);
				}
				
				?>
				<div <?php echo $this->get_render_attribute_string('item-wrap'); ?>>
					<?php $this->render_image($item, 'image_'.$index); ?>
					<div class="bdt-ep-product-carousel-content">
						<div class="bdt-ep-product-carousel-title-price bdt-flex bdt-flex-middle bdt-flex-between">
							<?php $this->render_title($item, 'title_'.$index); ?>
							<?php $this->render_price($item); ?>
						</div>
						<?php $this->render_text($item); ?>
						<?php $this->render_readmore($item, 'link_'.$index); ?>
						<div class="bdt-ep-product-carousel-rating-time bdt-flex bdt-flex-middle bdt-flex-between">
							<?php $this->render_review_rating($item); ?>
							<?php $this->render_time($item); ?>
						</div>
					</div>
					<?php $this->render_badge($item); ?>

					<?php if($settings['readmore_link_to'] == 'item') : ?>
					<a <?php echo $this->get_render_attribute_string( $item_key ); ?>></a>
					<?php endif; ?>

				</div>
			<?php endforeach;
		}
		
		public function render_header() {
			$settings = $this->get_settings_for_display();
	
			//Global Function
			$this->render_swiper_header_attribute( 'product-carousel');
	
			$this->add_render_attribute( 'carousel', 'class', 'bdt-ep-product-carousel' );
	
			?>
			<div <?php echo $this->get_render_attribute_string( 'carousel' ); ?>>
				<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
					<div class="swiper-wrapper">
			<?php
		}
	
		public function render() {
			$this->render_header();
			$this->render_carosuel_item();
			$this->render_footer();
		}
	}
