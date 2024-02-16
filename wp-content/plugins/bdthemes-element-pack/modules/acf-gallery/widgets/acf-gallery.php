<?php

namespace ElementPack\Modules\AcfGallery\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

use ElementPack\Utils;
use ElementPack\Modules\AcfGallery\Skins;
use ElementPack\Traits\Global_Mask_Controls;

use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Acf_Gallery extends Module_Base {

	use Global_Mask_Controls;

	public $lightbox_slide_index;

	public function get_name() {
		return 'bdt-acf-gallery';
	}

	public function get_title() {
		return BDTEP . esc_html__('ACF Gallery', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-acf-gallery';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['acf-gallery', 'acf', 'gallery', 'photo', 'image'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-acf-gallery', 'ep-font'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['imagesloaded', 'ep-justified-gallery', 'tilt', 'ep-scripts'];
		} else {
			return ['imagesloaded', 'ep-justified-gallery', 'tilt', 'ep-acf-gallery'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/se7BovYbDok';
	}

	public function register_skins() {
		$this->add_skin(new Skins\Skin_Hidden($this));
		$this->add_skin(new Skins\Skin_Carousel($this));
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_gallery',
			[
				'label' => esc_html__('Image Gallery', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
            'field',
            [
                'label' => __('Gallery Field', 'bdthemes-element-pack'),
                'dynamic' => ['active' => false],
                'type'    => Dynamic_Select::TYPE,
                'label_block' => true,
                'placeholder' => __('Type and select the gallery field...', 'bdthemes-element-pack'),
                'query_args'  => [
                    'query'        => 'acf',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				'condition' => ['_skin!' => 'bdt-hidden'],
			]
		);

		$this->add_control(
			'image_mask_popover',
			[
				'label'        => esc_html__('Image Mask', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'render_type'  => 'template',
				'return_value' => 'yes',
				'condition' => ['_skin!' => 'bdt-hidden'],
			]
		);

		//Global Image Mask Controls
		$this->register_image_mask_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_custom_gallery_layout',
			[
				'label'     => esc_html__('Gallery Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'grid_type',
			[
				'label'   => esc_html__('Gallery Mode', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'normal' => [
						'title' => esc_html__('Normal', 'bdthemes-element-pack'),
						'icon'  => 'eicon-gallery-grid',
					],
					'masonry' => [
						'title' => esc_html__('Masonry', 'bdthemes-element-pack'),
						'icon'  => 'eicon-gallery-masonry',
					],
					'justified' => [
						'title' => esc_html__('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-gallery-justified',
					],
				],
				'default' => 'normal',
				'condition' => [
					'_skin' => '',
				],
			]
		);



		$this->add_responsive_control(
			'item_ratio',
			[
				'label'   => esc_html__('Image Height', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 265,
				],
				'range' => [
					'px' => [
						'min'  => 50,
						'max'  => 500,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-thumbnail img' => 'height: {{SIZE}}px',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name'     => '_skin',
							'operator' => 'in',
							'value'    => ['', 'bdt-carousel']
						],
						[
							'name'     => 'grid_type',
							'operator' => '==',
							'value'    => 'normal'
						],
					]
				]
			]
		);

		$this->add_control(
			'gallery_item_height',
			[
				'label'   => esc_html__('Image Height', 'bdthemes-element-pack'),
				'description'   => esc_html__('Some times image height not exactly same because of auto row adjustment.', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 260,
				],
				'range' => [
					'px' => [
						'min'  => 50,
						'max'  => 500,
						'step' => 5,
					],
				],
				'condition' => [
					'grid_type' => 'justified'
				]
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__('Columns', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'default'        => '4',
				'tablet_default' => '3',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'condition' => [
					'_skin' => ''
				]
			]
		);

		$this->add_responsive_control(
			'carousel_columns',
			[
				'label'          => esc_html__('Columns', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'default'        => '4',
				'tablet_default' => '3',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'condition' => [
					'_skin' => 'bdt-carousel'
				]
			]
		);

		$this->add_responsive_control(
			'row_column_gap',
			[
				'label'   => esc_html__('Item Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'condition' => [
					'grid_type' => 'justified'
				],
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'   => esc_html__('Column Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],  
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery.bdt-grid'     => is_rtl() ? 'margin-right: -{{SIZE}}px' : 'margin-left: -{{SIZE}}px',
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery.bdt-grid > *' => is_rtl() ? 'padding-right: {{SIZE}}px' : 'padding-left: {{SIZE}}px',
				],
				'condition' => [
					'_skin!' => 'bdt-hidden',
					'grid_type!' => 'justified'
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'   => esc_html__('Row Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery.bdt-grid'     => 'margin-top: -{{SIZE}}px',
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery.bdt-grid > *' => 'margin-top: {{SIZE}}px',
				],
				'condition' => [
					'_skin' => '',
					'grid_type!' => 'justified'
				],
			]
		);

		$this->add_control(
			'show_lightbox',
			[
				'label'     => esc_html__('Show Lightbox', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				// 'separator' => 'before',
			]
		);

		$this->add_control(
			'link_type',
			[
				'label'   => esc_html__('Link Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => [
					'icon'  => esc_html__('Icon', 'bdthemes-element-pack'),
					'text'  => esc_html__('Text', 'bdthemes-element-pack'),
					'image' => esc_html__('Image', 'bdthemes-element-pack'),
				],
				'condition' => [
					'show_lightbox' => 'yes',
					'_skin!'		=> 'bdt-hidden',
				]
			]
		);

		$this->add_control(
			'ep_gallery_link_icon',
			[
				'label'       => esc_html__('Link Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::ICONS,
				'condition' => [
					'show_lightbox' => 'yes',
					'link_type'		=> 'icon',
					'_skin!'		=> 'bdt-hidden',
				],
				'skin' => 'inline',
				'label_block' => false
			]
		);

		$this->add_control(
			'show_caption',
			[
				'label'       => esc_html__('Show Caption', 'bdthemes-element-pack'),
				'description' => esc_html__('Make sure you set the caption in gallery images when you insert.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
				'condition' => ['_skin!' => 'bdt-hidden'],
			]
		);

		$this->add_control(
			'caption_all_time',
			[
				'label'     => esc_html__('Caption all Time', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'show_caption' => 'yes',
					'_skin!' => 'bdt-hidden',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout_additional',
			[
				'label'     => esc_html__('Additional', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'overlay_content_alignment',
			[
				'label'   => esc_html__('Overlay Content Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery .bdt-overlay' => 'text-align: {{VALUE}}',
				],
				'condition' => [
					'show_lightbox' => 'yes',
					'show_caption'  => 'yes',
				],
			]
		);

		$this->add_control(
			'overlay_content_position',
			[
				'label'       => esc_html__('Overlay Content Vertical Position', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'top' => [
						'title' => esc_html__('Top', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__('Middle', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__('Bottom', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary' => [
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'default'   => 'middle',
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery .bdt-overlay' => 'justify-content: {{VALUE}}',
				],
				'condition' => [
					'show_lightbox' => 'yes',
					'show_caption'  => 'yes',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'caption_position',
			[
				'label'     => esc_html__('Caption Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => element_pack_position(),
				'condition' => [
					'show_caption'     => 'yes',
					'caption_all_time' => 'yes',
				],
			]
		);

		$this->add_control(
			'advanced_spotlite_mode',
			[
				'label' => esc_html__('Spotlite Mode', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'tilt_show',
			[
				'label' => esc_html__('Tilt Effect', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				'condition' => [
					'_skin!'            => 'bdt-hidden',
					'caption_all_time!' => 'yes',
				],
			]
		);

		$this->add_control(
			'tilt_scale',
			[
				'label' => esc_html__('Tilt Scale', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 2,
						'step' => 0.1,
					],
				],
				'condition' => [
					'tilt_show' => 'yes',
					'caption_all_time!' => 'yes',
				],
				'separator' => 'after',
			]
		);


		$this->add_control(
			'lightbox_link_type',
			[
				'label'   => esc_html__('Lightbox Link Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'simple_text',
				'options' => [
					'simple_text' => esc_html__('Text', 'bdthemes-element-pack'),
					'link_icon'   => esc_html__('Icon', 'bdthemes-element-pack'),
					'link_image'  => esc_html__('Image', 'bdthemes-element-pack'),
				],
				'condition' => ['_skin' => 'bdt-hidden'],
			]
		);

		$this->add_control(
			'link_image',
			[
				'label'   => esc_html__('Link Image', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => ['lightbox_link_type' => 'link_image'],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'link_image_size',
				'condition' => ['lightbox_link_type' => 'link_image'],
			]
		);

		$this->add_control(
			'gallery_link_text',
			[
				'label'       => esc_html__('Link Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Open Gallery', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Link Text', 'bdthemes-element-pack'),
				'condition'   => ['_skin' => 'bdt-hidden', 'lightbox_link_type' => 'simple_text'],
			]
		);

		$this->add_control(
			'gallery_link_icon',
			[
				'label'       => esc_html__('Link Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'fa-solid',
				],
				'conditions' => [
					'terms' => [
						[
							'name'     => '_skin',
							'value'    => 'bdt-hidden',
						],
						[
							'name'     => 'lightbox_link_type',
							'value'    => 'link_icon',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'gallery_link_align',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'elementor-align%s-',
				'condition' => ['_skin' => 'bdt-hidden'],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'lightbox_animation',
			[
				'label'   => esc_html__('Lightbox Animation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide' => esc_html__('Slide', 'bdthemes-element-pack'),
					'fade'  => esc_html__('Fade', 'bdthemes-element-pack'),
					'scale' => esc_html__('Scale', 'bdthemes-element-pack'),
				],
				'condition' => [
					'show_lightbox' => 'yes',
				]
			]
		);

		$this->add_control(
			'lightbox_autoplay',
			[
				'label'   => esc_html__('Lightbox Autoplay', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_lightbox' => 'yes',
				]
			]
		);

		$this->add_control(
			'lightbox_pause',
			[
				'label'   => esc_html__('Lightbox Pause on Hover', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_lightbox' => 'yes',
					'lightbox_autoplay' => 'yes'
				],

			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_carousel_settins',
			[
				'label'     => esc_html__('Carousel Settings', 'bdthemes-element-pack'),
				'condition' => [
					'_skin' => 'bdt-carousel',
				],
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => esc_html__('Auto Play', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay_interval',
			[
				'label'     => esc_html__('Autoplay Interval', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 7000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'   => esc_html__('Pause on Hover', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'loop',
			[
				'label'   => esc_html__('Loop', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'center_slide',
			[
				'label' => esc_html__('Center Slide', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'slide_sets',
			[
				'label' => esc_html__('Slide Sets', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_navigation',
			[
				'label'     => esc_html__('Navigation', 'bdthemes-element-pack'),
				'condition' => [
					'_skin' => 'bdt-carousel',
				],
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'   => esc_html__('Navigation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'arrows',
				'options' => [
					'both'   => esc_html__('Arrows and Dots', 'bdthemes-element-pack'),
					'arrows' => esc_html__('Arrows', 'bdthemes-element-pack'),
					'dots'   => esc_html__('Dots', 'bdthemes-element-pack'),
					'none'   => esc_html__('None', 'bdthemes-element-pack'),
				],
				'prefix_class' => 'bdt-navigation-type-',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'both_position',
			[
				'label'     => esc_html__('Arrows and Dots Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => element_pack_navigation_position(),
				'condition' => [
					'navigation' => 'both',
				],
			]
		);

		$this->add_control(
			'arrows_position',
			[
				'label'     => esc_html__('Arrows Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => element_pack_navigation_position(),
				'condition' => [
					'navigation' => 'arrows',
				],
			]
		);

		$this->add_control(
			'dots_position',
			[
				'label'     => esc_html__('Dots Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom-center',
				'options'   => element_pack_pagination_position(),
				'condition' => [
					'navigation' => 'dots',
				],
			]
		);

		$this->add_control(
			'nav_arrows_icon',
			[
				'label'   => esc_html__('Arrows Icon', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '0',
				'options' => [
					'0' => esc_html__('Default', 'bdthemes-element-pack'),
					'1' => esc_html__('Style 1', 'bdthemes-element-pack'),
					'2' => esc_html__('Style 2', 'bdthemes-element-pack'),
					'3' => esc_html__('Style 3', 'bdthemes-element-pack'),
					'4' => esc_html__('Style 4', 'bdthemes-element-pack'),
					'5' => esc_html__('Style 5', 'bdthemes-element-pack'),
					'6' => esc_html__('Style 6', 'bdthemes-element-pack'),
					'7' => esc_html__('Style 7', 'bdthemes-element-pack'),
					'8' => esc_html__('Style 8', 'bdthemes-element-pack'),
					'9' => esc_html__('Style 9', 'bdthemes-element-pack'),
					'10' => esc_html__('Style 10', 'bdthemes-element-pack'),
					'11' => esc_html__('Style 11', 'bdthemes-element-pack'),
					'12' => esc_html__('Style 12', 'bdthemes-element-pack'),
					'13' => esc_html__('Style 13', 'bdthemes-element-pack'),
					'14' => esc_html__('Style 14', 'bdthemes-element-pack'),
					'15' => esc_html__('Style 15', 'bdthemes-element-pack'),
					'16' => esc_html__('Style 16', 'bdthemes-element-pack'),
					'17' => esc_html__('Style 17', 'bdthemes-element-pack'),
					'18' => esc_html__('Style 18', 'bdthemes-element-pack'),
					'circle-1' => esc_html__('Style 19', 'bdthemes-element-pack'),
					'circle-2' => esc_html__('Style 20', 'bdthemes-element-pack'),
					'circle-3' => esc_html__('Style 21', 'bdthemes-element-pack'),
					'circle-4' => esc_html__('Style 22', 'bdthemes-element-pack'),
					'square-1' => esc_html__('Style 23', 'bdthemes-element-pack'),
				],
				'condition' => [
					'navigation' => ['both', 'arrows'],
				],
			]
		);

		$this->add_control(
			'hide_arrow_on_mobile',
			[
				'label'     => __('Hide Arrow on Mobile ?', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_design_layout',
			[
				'label'     => esc_html__('Items', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => ['_skin!' => 'bdt-hidden'],
			]
		);

		$this->start_controls_tabs('tabs_item_controls');

		$this->start_controls_tab(
			'tab_item_style',
			[
				'label' => __('Normal', 'bdthemes-element-pack')
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-ep-advanced-image-gallery-thumbnail',
			]
		);

		$this->add_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item .bdt-ep-advanced-image-gallery-inner, {{WRAPPER}} .bdt-ep-advanced-image-gallery-thumbnail, {{WRAPPER}} .bdt-ep-advanced-image-gallery .bdt-overlay, {{WRAPPER}} .bdt-slider-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_spotlite_divider',
			[
				'type'    => Controls_Manager::DIVIDER,
				'condition' => [
					'advanced_spotlite_mode' => 'yes',
				],
			]
		);

		$this->add_control(
			'spotlite_mode_heading',
			[
				'label'     => esc_html__('Spotlite Mood', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'advanced_spotlite_mode' => 'yes',
				],
			]
		);

		$this->add_control(
			'spotlite_mode_color',
			[
				'label'     => esc_html__('Outer Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-spotlite-mode:hover .bdt-ep-advanced-image-gallery-item:not(:hover):after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'advanced_spotlite_mode' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_overlay_style',
			[
				'label' => __('Overlay', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'overlay_animation',
			[
				'label'   => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => element_pack_transition_options(),
			]
		);

		$this->add_control(
			'overlay_blur_effect',
			[
				'label' => esc_html__('Glassmorphism', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),

			]
		);

		$this->add_control(
			'overlay_blur_level',
			[
				'label'       => __('Blur Level', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'default'     => [
					'size' => 5
				],
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item .bdt-overlay-default' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'overlay_blur_effect' => 'yes'
				]
			]
		);

		$this->add_control(
			'overlay_background',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item .bdt-overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'overlay_gap',
			[
				'label' => esc_html__('Gap', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item .bdt-overlay' => 'margin: {{SIZE}}px',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_caption',
			[
				'label'     => esc_html__('Caption', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_caption' => 'yes',
				],
			]
		);

		$this->add_control(
			'caption_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-caption' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'caption_background',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-caption'
			]
		);

		$this->add_responsive_control(
			'caption_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'before',
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'caption_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-caption' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'caption_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-caption'
			]
		);

		$this->add_control(
			'caption_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'after',
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'caption_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-caption'
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'caption_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-caption',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__('Link Style', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_lightbox' => 'yes',
					'link_type!'    => 'image',
				],
			]
		);

		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-link span, {{WRAPPER}} .bdt-ep-advanced-image-gallery-item-link, {{WRAPPER}} .bdt-ep-advanced-image-gallery-skin-hidden .bdt-hidden-gallery-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-link svg, {{WRAPPER}} .bdt-ep-advanced-image-gallery-skin-hidden .bdt-hidden-gallery-button svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-link' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-link',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-link, {{WRAPPER}} .bdt-ep-advanced-image-gallery-skin-hidden .bdt-hidden-gallery-button img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-link, {{WRAPPER}} .bdt-ep-advanced-image-gallery-item-link span',
				'condition' => [
					'show_lightbox' => 'yes',
					'lightbox_link_type!' => 'link_image',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-link',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-link:hover span, {{WRAPPER}} .bdt-ep-advanced-image-gallery-item-link:hover, {{WRAPPER}} .bdt-ep-advanced-image-gallery-skin-hidden .bdt-hidden-gallery-button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-link:hover svg, {{WRAPPER}} .bdt-ep-advanced-image-gallery-skin-hidden .bdt-hidden-gallery-button:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-link:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-image-gallery-item-link:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'      => esc_html__('Navigation', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'name'     => '_skin',
							'value'    => 'bdt-carousel',
						],
						[
							'name'     => 'navigation',
							'operator' => '!=',
							'value'    => 'none',
						],
					],
				],
			]
		);

		$this->add_control(
			'arrows_heading',
			[
				'label'     => esc_html__('Arrows', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev i,
					{{WRAPPER}} .bdt-navigation-next i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev:hover i,
					{{WRAPPER}} .bdt-navigation-next:hover i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->add_control(
			'arrows_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev,
					{{WRAPPER}} .bdt-navigation-next' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->add_control(
			'arrows_hover_background',
			[
				'label'     => esc_html__('Hover Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev:hover,
					{{WRAPPER}} .bdt-navigation-next:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->add_control(
			'arrows_size',
			[
				'label' => esc_html__('Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev i,
					{{WRAPPER}} .bdt-navigation-next i' => 'font-size: {{SIZE || 25}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->add_control(
			'arrows_space',
			[
				'label' => esc_html__('Space Between Arrows', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-navigation-next' => 'margin-left: {{SIZE}}px;',
				],
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'arrows_border',
				'selector'  => '{{WRAPPER}} .bdt-navigation-prev,
				{{WRAPPER}} .bdt-navigation-next',
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navigation-prev,
					{{WRAPPER}} .bdt-navigation-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navigation-prev,
					{{WRAPPER}} .bdt-navigation-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'arrows_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-navigation-prev,
				{{WRAPPER}} .bdt-navigation-next',
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->add_control(
			'hr_1',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation' => ['dots', 'both'],
				],
			]
		);

		$this->add_control(
			'dots_heading',
			[
				'label'     => esc_html__('Dots', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation' => ['dots', 'both'],
				],
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider-dotnav a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => ['dots', 'both'],
				],
			]
		);

		$this->add_control(
			'active_dot_color',
			[
				'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider-dotnav.bdt-active a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => ['dots', 'both'],
				],
			]
		);

		$this->add_control(
			'dots_size',
			[
				'label' => esc_html__('Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider-dotnav a' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => ['dots', 'both'],
				],
			]
		);

		$this->add_control(
			'hr_05',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'navi_offset_heading',
			[
				'label'     => __('Offset', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'hr_6',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_responsive_control(
			'arrows_ncx_position',
			[
				'label'   => esc_html__('Horizontal Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'     => 'arrows_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-carousel-arrows-ncx: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'arrows_ncy_position',
			[
				'label'   => esc_html__('Vertical Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-carousel-arrows-ncy: {{SIZE}}px;'
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'     => 'arrows_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_acx_position',
			[
				'label'   => esc_html__('Horizontal Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => -60,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-navigation-next' => 'right: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'  => 'arrows_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'dots_nnx_position',
			[
				'label'   => esc_html__('Horizontal Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'dots',
						],
						[
							'name'     => 'dots_position',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-carousel-dots-nnx: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'dots_nny_position',
			[
				'label'   => esc_html__('Vertical Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-carousel-dots-nny: {{SIZE}}px;'
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'dots',
						],
						[
							'name'     => 'dots_position',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'both_ncx_position',
			[
				'label'   => esc_html__('Horizontal Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'     => 'both_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-carousel-both-ncx: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'both_ncy_position',
			[
				'label'   => esc_html__('Vertical Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-carousel-both-ncy: {{SIZE}}px;'
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'     => 'both_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'both_cx_position',
			[
				'label'   => esc_html__('Arrows Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => -60,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-navigation-next' => 'right: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'  => 'both_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'both_cy_position',
			[
				'label'   => esc_html__('Dots Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-dots-container' => 'transform: translateY({{SIZE}}px);',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'  => 'both_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->end_controls_section();
	}

	public function render_header() {

		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		$this->add_render_attribute('advanced-image-gallery', 'id', 'bdt-avdg-' . esc_attr($id));
		$this->add_render_attribute('advanced-image-gallery', 'class', ['bdt-ep-advanced-image-gallery', 'bdt-ep-advanced-image-gallery-skin-default']);

		if ('yes' == $settings['advanced_spotlite_mode']) {
			$this->add_render_attribute('advanced-image-gallery', 'class', ['bdt-spotlite-mode']);
		}

		if ('justified' == $settings['grid_type']) {
			$this->add_render_attribute('advanced-image-gallery', 'class', 'jgallery');

			if ($settings['gallery_item_height']['size']) {
				$this->add_render_attribute('advanced-image-gallery', 'data-jgallery-jfheight', esc_attr($settings['gallery_item_height']['size']));
			}

			if ($settings['row_column_gap']['size']) {
				$this->add_render_attribute('advanced-image-gallery', 'data-jgallery-itemgap', esc_attr($settings['row_column_gap']['size']));
			}
		} else {
			$this->add_render_attribute('advanced-image-gallery', 'data-bdt-grid', '');
			$this->add_render_attribute('advanced-image-gallery', 'class', ['bdt-grid', 'bdt-grid-small']);

			$columns_mobile = isset($settings['columns_mobile']) ? $settings['columns_mobile'] : 1;
			$columns_tablet = isset($settings['columns_tablet']) ? $settings['columns_tablet'] : 3;
			$columns 		 = isset($settings['columns']) ? $settings['columns'] : 4;

			$this->add_render_attribute('advanced-image-gallery', 'class', 'bdt-child-width-1-' . esc_attr($columns_mobile));
			$this->add_render_attribute('advanced-image-gallery', 'class', 'bdt-child-width-1-' . esc_attr($columns_tablet) . '@s');
			$this->add_render_attribute('advanced-image-gallery', 'class', 'bdt-child-width-1-' . esc_attr($columns) . '@m');
		}

		if ('masonry' == $settings['grid_type']) {
			$this->add_render_attribute('advanced-image-gallery', 'data-bdt-grid', 'masonry: true');
		}

		if ($settings['caption_all_time']) {
			$this->add_render_attribute('advanced-image-gallery', 'class', 'bdt-caption-all-time-yes');
		}


		if ($settings['show_lightbox'] or 'bdt-hidden' === $settings['_skin']) {
			$this->add_render_attribute('advanced-image-gallery', 'data-bdt-lightbox', 'animation: ' . $settings['lightbox_animation'] . ';');
			if ($settings['lightbox_autoplay']) {
				$this->add_render_attribute('advanced-image-gallery', 'data-bdt-lightbox', 'autoplay: 500;');

				if ($settings['lightbox_pause']) {
					$this->add_render_attribute('advanced-image-gallery', 'data-bdt-lightbox', 'pause-on-hover: true;');
				}
			}
		}

		$this->add_render_attribute(
			[
				'advanced-image-gallery' => [
					'data-settings' => [
						wp_json_encode([
							'id'		=> '#bdt-avdg-' . $this->get_id(),
							'tiltShow'  => (isset($settings['tilt_show']) && $settings['tilt_show'] == 'yes') ? true : false
						]),
					],
				],
			]
		);

		?>
		<div <?php echo $this->get_render_attribute_string('advanced-image-gallery'); ?>>
			<?php
		}

		private function render_gallery_image($settings, $images) {

			$image_mask = $settings['image_mask_popover'] == 'yes' ? ' bdt-image-mask' : '';

			$this->add_render_attribute('advanced-image-gallery-item', 'class', ['bdt-ep-advanced-image-gallery-item', 'bdt-transition-toggle']);

			$this->add_render_attribute('advanced-image-gallery-inner', 'class', 'bdt-ep-advanced-image-gallery-inner' . $image_mask);

			if ($settings['tilt_show']) {
				$this->add_render_attribute('advanced-image-gallery-inner', 'data-tilt', '');
				if ($settings['tilt_scale']['size']) {
					$this->add_render_attribute('advanced-image-gallery-inner', 'data-tilt-scale', $settings['tilt_scale']['size']);
				}
			}

			foreach ($images as $index => $image) : ?>

				<div <?php echo $this->get_render_attribute_string('advanced-image-gallery-item'); ?>>
					<div <?php echo $this->get_render_attribute_string('advanced-image-gallery-inner'); ?>>
						<?php
						$this->render_thumbnail($image);

						if ($settings['show_lightbox'] or ($settings['show_caption'] and 'yes' !== $settings['caption_all_time'])) :
							$this->render_overlay($image);
						endif;

						?>
					</div>
					<?php if ($settings['show_caption'] and 'yes' == $settings['caption_all_time']) : ?>
						<?php $this->render_caption($image); ?>
					<?php endif; ?>
				</div>

			<?php endforeach;
		}

		public function render_footer() {
			?>
		</div>
	<?php
		}

		protected function render() {
			$settings = $this->get_settings_for_display();
			$id       = $this->get_id();

			// ACF - Gallery fields.
			$gallery_field = get_field( $settings['field'] );
			if (empty($gallery_field)) {
				return;
			}

			$this->render_header();
			$this->render_gallery_image($settings, $gallery_field);
			$this->render_footer();
		}

		public function render_thumbnail($image) {
			$settings = $this->get_settings_for_display();
			echo '<div class="bdt-ep-advanced-image-gallery-thumbnail bdt-transition-toggle">';
			print(wp_get_attachment_image(
				$image['id'],
				$settings['thumbnail_size'],
				false,
				[
					'class' => 'jgalleryImage',
					'alt'   => esc_attr($image['alt'])
				]
			));
			echo '</div>';
		}

		public function render_caption($text) {
			$image_caption = get_post($text['id']);
			$settings      = $this->get_settings_for_display();

			$this->add_render_attribute('caption', 'class', 'bdt-ep-advanced-image-gallery-item-caption bdt-display-inline-block', true);

			if ($settings['caption_all_time']) {
				$this->add_render_attribute('caption', 'class', ('' != $settings['caption_position']) ? 'bdt-position-' . $settings['caption_position'] : 'bdt-caption-position-default');
			}

		if (!empty($image_caption->post_excerpt)) : ?>
			<div>
				<div <?php echo $this->get_render_attribute_string('caption'); ?>>
					<?php echo wp_kses_post($image_caption->post_excerpt); ?>
				</div>
			</div>
		<?php endif;
		}

		public function render_overlay($content) {
			$settings                  = $this->get_settings_for_display();
			$image_caption = get_post($content['id']);

			$this->add_render_attribute('overlay-settings', 'class', ['bdt-position-cover', 'bdt-overlay', 'bdt-overlay-default'], true);

			if ($settings['overlay_animation']) {
				$this->add_render_attribute('overlay-settings', 'class', 'bdt-transition-' . $settings['overlay_animation']);
			}
			$animation = $settings['button_hover_animation'] ? 'elementor-animation-' . $settings['button_hover_animation'] : '';

		?>
		<div <?php echo $this->get_render_attribute_string('overlay-settings'); ?>>
			<div class="bdt-ep-advanced-image-gallery-content">
				<div class="bdt-ep-advanced-image-gallery-content-inner">

					<?php $this->add_render_attribute(
						[
							'overlay-lightbox-attr' => [
								'class' => [
									('image' == $settings['link_type']) ? 'bdt-position-cover' : 'bdt-ep-advanced-image-gallery-item-link',
									'elementor-clickable',
									'icon-type-' . $settings['link_type'],
									$animation,
								],
								'data-elementor-open-lightbox' => 'no',
								'data-caption'                 => $image_caption->post_excerpt,
							],
						],
						'',
						'',
						true
					);

					$image_url = wp_get_attachment_image_src($content['id'], 'full');

					if (!$image_url) {
						$this->add_render_attribute('overlay-lightbox-attr', 'href', $content['url'], true);
					} else {
						$this->add_render_attribute('overlay-lightbox-attr', 'data-href', $image_url[0], true);
					}

					?>
					<?php if ('yes' == $settings['show_lightbox']) : ?>
						<div class="bdt-flex-inline bdt-ep-advanced-image-gallery-item-link-wrapper">
							<a <?php echo $this->get_render_attribute_string('overlay-lightbox-attr'); ?>>
								<?php if ('icon' == $settings['link_type']) : ?>

									<?php if ($settings['ep_gallery_link_icon']['value']) : ?>
										<span><?php Icons_Manager::render_icon($settings['ep_gallery_link_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']); ?></span>
									<?php else : ?>
										<span>
											<i class="ep-icon-plus-2"></i>
										</span>
									<?php endif; ?>

								<?php elseif ('text' == $settings['link_type']) : ?>
									<span class="bdt-text"><?php echo esc_html_x('ZOOM', 'ACF Gallery String', 'bdthemes-element-pack'); ?></span>
								<?php endif; ?>
							</a>
						</div>
					<?php endif; ?>

					<?php if ($settings['show_caption'] and 'yes' != $settings['caption_all_time']) : ?>
						<?php $this->render_caption($content); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
<?php
		}

		public function link_only($content) {
			$settings      = $this->get_settings_for_display();
			$image_caption = get_post($content['id']);
			$animation = $settings['button_hover_animation'] ? 'elementor-animation-' . $settings['button_hover_animation'] : '';

			$this->add_render_attribute(
				[
					'lightbox-attributes' => [
						'class' => [
							'elementor-clickable',
							'icon-type-' . $settings['link_type'],
							$animation,
						],
						'data-elementor-open-lightbox' => 'no',
						'data-caption'                 => $image_caption->post_excerpt,
					],
				],
				'',
				'',
				true
			);

			$image_url = wp_get_attachment_image_src($content['id'], 'full');

			if (!$image_url) {
				$this->add_render_attribute('lightbox-attributes', 'href', $content['url'], true);
			} else {
				$this->add_render_attribute('lightbox-attributes', 'href', $image_url[0], true);
			}

			$this->lightbox_slide_index++;

			if (1 === $this->lightbox_slide_index) {
				$this->add_render_attribute('lightbox-attributes', 'class', ['bdt-ep-advanced-image-gallery-item-link', 'bdt-hidden-gallery-button']);
				echo '<a ' . $this->get_render_attribute_string('lightbox-attributes') . '>';

				if ('simple_text' == $settings['lightbox_link_type']) {
					echo '<span>' . $settings['gallery_link_text'] . '</span>';
				} elseif ('link_icon' == $settings['lightbox_link_type']) {
					Icons_Manager::render_icon($settings['gallery_link_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
				} else {
					$link_image_src = Group_Control_Image_Size::get_attachment_image_src($settings['link_image']['id'], 'link_image_size', $settings);
					$link_image_src = ($link_image_src) ? $link_image_src : $settings['link_image']['url'];
					echo '<img src=' . esc_url($link_image_src) . ' alt="' . get_the_title() . '">';
				}
				echo '</a>';
			} else {
				$this->add_render_attribute('lightbox-attributes', 'class', 'bdt-hidden');
				echo '<a ' . $this->get_render_attribute_string('lightbox-attributes') . '></a>';
			}
		}
	}
