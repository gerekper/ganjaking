<?php

namespace ElementPack\Modules\HoverBox\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use ElementPack\Utils;
use ElementPack\Modules\HoverBox\Skins;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Hover_Box extends Module_Base {

	public function get_name() {
		return 'bdt-hover-box';
	}

	public function get_title() {
		return BDTEP . esc_html__('Hover Box', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-hover-box';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['fancy', 'effects', 'toggle', 'accordion', 'hover', 'slideshow', 'slider', 'box', 'animated boxs'];
	}

	public function is_reload_preview_required() {
		return false;
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-hover-box'];
		}
	}
	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-scripts'];
		} else {
			return ['ep-hover-box'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/lWdF9-SV-2I';
	}

	protected function register_skins() {
		$this->add_skin(new Skins\Skin_Envelope($this));
		$this->add_skin(new Skins\Skin_Flexure($this));
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_tabs_item',
			[
				'label' => esc_html__('Hover Box Items', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'layout_style',
			[
				'label'   => esc_html__('Layout Style', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__('Style 01', 'bdthemes-element-pack'),
					'style-2'  => esc_html__('Style 02', 'bdthemes-element-pack'),
				],
				'condition' => [
					'_skin' => '',
				],
			]
		);


		$repeater = new Repeater();

		$repeater->start_controls_tabs('items_tabs_controls');

		$repeater->start_controls_tab(
			'tab_item_content',
			[
				'label' => esc_html__('Content', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_control(
			'selected_icon',
			[
				'label'            => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'             => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'skin' => 'inline',
				'label_block' => false
			]
		);

		$repeater->add_control(
			'hover_box_title',
			[
				'label'       => esc_html__('Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => esc_html__('Tab Title', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'hover_box_sub_title',
			[
				'label'       => esc_html__('Sub Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'hover_box_button',
			[
				'label'       => esc_html__('Button Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'label_block' => true,
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'button_link',
			[
				'label'         => esc_html__('Button Link', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'dynamic'       => ['active' => true],
				'default'       => ['url' => '#'],
				'condition'     => [
					'hover_box_button!' => ''
				]
			]
		);

		$repeater->add_control(
			'slide_image',
			[
				'label'   => esc_html__('Background Image', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => ['active' => true],
				'default' => [
					'url' => BDTEP_ASSETS_URL . 'images/gallery/item-' . rand(1, 6) . '.svg',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'tab_item_content_optional',
			[
				'label' => esc_html__('Optional', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_control(
			'title_link',
			[
				'label'         => esc_html__('Title Link', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'default'       => ['url' => ''],
				'show_external' => false,
				'dynamic'       => ['active' => true],
				'condition'     => [
					'hover_box_title!' => ''
				]
			]
		);

		$repeater->add_control(
			'hover_box_content',
			[

				'type'       => Controls_Manager::WYSIWYG,
				'dynamic'    => ['active' => true],
				'default'    => esc_html__('Box Content', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'hover_box_content_background',
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .bdt-ep-hover-box-content {{CURRENT_ITEM}}',
				'fields_options' => [
					'background' => [
						'label' => esc_html__('Background Type', 'bdthemes-element-pack') . BDTEP_NC,
					],
				],
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'ignore_element_notes',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__('Note: This option will work if the background image is empty.', 'bdthemes-element-pack'),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',

			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'hover_box',
			[
				'label'     => esc_html__('Items', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'separator' => 'before',
				'default' => [
					[
						'hover_box_sub_title'   => esc_html__('This is label', 'bdthemes-element-pack'),
						'hover_box_title'   	  => esc_html__('Hover Box One', 'bdthemes-element-pack'),
						'hover_box_content' 	  => esc_html__('Lorem ipsum dolor sit amet consect voluptate repell endus kilo gram magni illo ea animi.', 'bdthemes-element-pack'),
						'selected_icon'  => ['value' => 'far fa-laugh', 'library' => 'fa-regular'],
						'slide_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-1.svg']
					],
					[
						'hover_box_sub_title'   => esc_html__('This is label', 'bdthemes-element-pack'),
						'hover_box_title'   => esc_html__('Hover Box Two', 'bdthemes-element-pack'),
						'hover_box_content' => esc_html__('Lorem ipsum dolor sit amet consect voluptate repell endus kilo gram magni illo ea animi.', 'bdthemes-element-pack'),
						'selected_icon'  => ['value' => 'fas fa-cog', 'library' => 'fa-solid'],
						'slide_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-2.svg']
					],
					[
						'hover_box_sub_title'   => esc_html__('This is label', 'bdthemes-element-pack'),
						'hover_box_title'   => esc_html__('Hover Box Three', 'bdthemes-element-pack'),
						'hover_box_content' => esc_html__('Lorem ipsum dolor sit amet consect voluptate repell endus kilo gram magni illo ea animi.', 'bdthemes-element-pack'),
						'selected_icon'  => ['value' => 'fas fa-dice-d6', 'library' => 'fa-solid'],
						'slide_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-3.svg']
					],
					[
						'hover_box_sub_title'   => esc_html__('This is label', 'bdthemes-element-pack'),
						'hover_box_title'   => esc_html__('Hover Box Four', 'bdthemes-element-pack'),
						'hover_box_content' => esc_html__('Lorem ipsum dolor sit amet consect voluptate repell endus kilo gram magni illo ea animi.', 'bdthemes-element-pack'),
						'selected_icon'  => ['value' => 'fas fa-ring', 'library' => 'fa-solid'],
						'slide_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-4.svg']
					],
					[
						'hover_box_sub_title'   => esc_html__('This is label', 'bdthemes-element-pack'),
						'hover_box_title'   => esc_html__('Hover Box Five', 'bdthemes-element-pack'),
						'hover_box_content' => esc_html__('Lorem ipsum dolor sit amet consect voluptate repell endus kilo gram magni illo ea animi.', 'bdthemes-element-pack'),
						'selected_icon'  => ['value' => 'fas fa-adjust', 'library' => 'fa-solid'],
						'slide_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-5.svg']
					],
					[
						'hover_box_sub_title'   => esc_html__('This is label', 'bdthemes-element-pack'),
						'hover_box_title'   => esc_html__('Hover Box Six', 'bdthemes-element-pack'),
						'hover_box_content' => esc_html__('Lorem ipsum dolor sit amet consect voluptate repell endus kilo gram magni illo ea animi.', 'bdthemes-element-pack'),
						'selected_icon'  => ['value' => 'fas fa-cog', 'library' => 'fa-solid'],
						'slide_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-6.svg']
					],
				],
				'title_field' => '{{{ elementor.helpers.renderIcon( this, selected_icon, {}, "i", "panel" ) }}} {{{ hover_box_title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout_hover_box',
			[
				'label' => esc_html__('Additional Settings', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'hover_box_min_height',
			[
				'label' => esc_html__('Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'_skin!' => 'bdt-envelope',
				]
			]
		);

		$this->add_responsive_control(
			'skin_hover_box_min_height',
			[
				'label' => esc_html__('Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box, {{WRAPPER}} .bdt-ep-hover-box-item' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'_skin' => 'bdt-envelope',
				]
			]
		);

		$this->add_responsive_control(
			'hover_box_width',
			[
				'label' => esc_html__('Content Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item-wrap' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'_skin!' => 'bdt-envelope',
				]
			]
		);

		$this->add_control(
			'default_content_position',
			[
				'label'          => esc_html__('Content Position', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'default'        => 'center',
				'options' => element_pack_position(),
				'condition' => [
					'_skin!' => 'bdt-envelope',
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'content_gap',
			[
				'label'          => esc_html__('Content Gap', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'default'        => 'medium',
				'options'        => [
					'small' => esc_html__('Small', 'bdthemes-element-pack'),
					'medium' => esc_html__('Medium', 'bdthemes-element-pack'),
					'large' => esc_html__('Large', 'bdthemes-element-pack'),
				],
				'condition' => [
					'_skin!' => 'bdt-envelope',
				]
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__('Columns', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '2',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'condition' => [
					'_skin!' => 'bdt-flexure',
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'content_position',
			[
				'label'          => esc_html__('Position', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'default'        => 'bottom',
				'options'        => [
					'top' 	 => 'Top',
					'center' => 'Center',
					'bottom' => 'Bottom',
				],
				'condition' => [
					'_skin' => 'bdt-envelope',
				]
			]
		);

		$this->add_control(
			'column_gap',
			[
				'label'   => esc_html__('Column Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'small',
				'options' => [
					'small'    => esc_html__('Small', 'bdthemes-element-pack'),
					'medium'   => esc_html__('Medium', 'bdthemes-element-pack'),
					'large'    => esc_html__('Large', 'bdthemes-element-pack'),
					'collapse' => esc_html__('Collapse', 'bdthemes-element-pack'),
					'custom' => esc_html__('Custom', 'bdthemes-element-pack'),
				],
				'condition' => [
					'_skin' => '',
				]
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'   => esc_html__('Gap', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item-wrap .bdt-grid'     => 'margin-left: -{{SIZE}}px',
					'{{WRAPPER}} .bdt-ep-hover-box-item-wrap .bdt-grid > *' => 'padding-left: {{SIZE}}px',
					'{{WRAPPER}} .bdt-ep-hover-box-item-wrap .bdt-grid+.bdt-grid, {{WRAPPER}} .bdt-ep-hover-box-item-wrap .bdt-grid>.bdt-grid-margin, *+.bdt-grid-margin' => 'margin-top: {{SIZE}}px',
				],
				'condition' => [
					'column_gap' => 'custom',
					'_skin' => '',
				]
			]
		);

		$this->add_control(
			'hover_box_event',
			[
				'label'   => esc_html__('Select Event ', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'mouseover',
				'options' => [
					'click'     => esc_html__('Click', 'bdthemes-element-pack'),
					'mouseover' => esc_html__('Hover', 'bdthemes-element-pack'),
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'hover_box_active_item',
			[
				'label'       => esc_html__('Active Item', 'bdthemes-element-pack'),
				'description' => esc_html__('Set default item by inserting the item\'s numeric position (i.e. 1 or 2 or 3 or ...) The numeric position reads from the top-left corner as 1st and continues to the right side.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'	  => '1',
				'condition' => [
					'_skin!' => 'bdt-flexure',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'         => 'thumbnail_size',
				'label'        => esc_html__('Image Size', 'bdthemes-element-pack'),
				'exclude'      => ['custom'],
				'default'      => 'full',
			]
		);

		$this->add_responsive_control(
			'tabs_content_align',
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
					'justify' => [
						'title' => esc_html__('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'_skin!' => 'bdt-flexure',
				]
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => esc_html__('Show Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'title_tags',
			[
				'label'   => esc_html__('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => element_pack_title_tags(),
				'condition' => [
					'show_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_icon',
			[
				'label'   => esc_html__('Show Icon', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'_skin!' => 'bdt-flexure',
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'show_sub_title',
			[
				'label'   => esc_html__('Show Sub Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_content',
			[
				'label'   => esc_html__('Show Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'_skin!' => 'bdt-flexure',
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'text_hide_on',
			[
				'label'       => esc_html__('Text Hide On', 'bdthemes-element-pack') . BDTEP_NC,
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => false,
				'options'     => [
					'desktop' => esc_html__('Desktop', 'bdthemes-element-pack'),
					'tablet'  => esc_html__('Tablet', 'bdthemes-element-pack'),
					'mobile'  => esc_html__('Mobile', 'bdthemes-element-pack'),
				],
				'frontend_available' => true,
				'condition' => [
					'_skin!' => 'bdt-flexure',
					'show_content' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_button',
			[
				'label'   => esc_html__('Show Button', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'_skin!' => 'bdt-flexure',
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'match_height',
			[
				'label' => esc_html__('Item Match Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'condition' => [
					'_skin!' => 'bdt-flexure',
				]
			]
		);

		$this->add_control(
			'box_image_effect',
			[
				'label' => esc_html__('Image Effect?', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'box_image_effect_select',
			[
				'label'   => esc_html__('Effect', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'effect-1',
				'options' => [
					'effect-1'   => 'Effect 01',
					'effect-2'   => 'Effect 02',
					'effect-3'   => 'Effect 03',
				],
				'condition' => [
					'box_image_effect' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_navigation_arrows',
			[
				'label'   => esc_html__('Show Navigation Arrows', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => [
					'_skin' => 'bdt-envelope',
				]
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_hover_box_style',
			[
				'label' => esc_html__('Hover Box', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'hover_box_overlay_color',
			[
				'label'     => esc_html__('Overlay Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box:before'  => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'hover_box_divider_size',
			[
				'label' => esc_html__('Divider Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-skin-envelope .bdt-ep-hover-box-item-wrap>.bdt-active:before' => 'width: {{SIZE}}{{UNIT}}; left: calc(-{{SIZE}}{{UNIT}} / 2);',
				],
				'condition' => [
					'_skin' => 'bdt-envelope'
				]
			]
		);

		$this->add_control(
			'hover_box_divider_color',
			[
				'label'     => esc_html__('Divider Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-skin-envelope .bdt-ep-hover-box-item-wrap>.bdt-active:before'  => 'background: {{VALUE}};',
				],
				'condition' => [
					'_skin' => 'bdt-envelope'
				]
			]
		);

		$this->add_control(
			'box_item_heading',
			[
				'label'      => esc_html__('Item', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->start_controls_tabs('box_item_style');

		$this->start_controls_tab(
			'box_item_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'box_item_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-hover-box-item',
			]
		);

		$this->add_responsive_control(
			'box_item_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-hover-box-default .bdt-ep-hover-box-item, {{WRAPPER}} .bdt-ep-hover-box-skin-envelope .bdt-ep-hover-box-description, {{WRAPPER}} .bdt-ep-hover-box-skin-flexure .bdt-ep-hover-box-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'box_item_border',
				'selector'    => '{{WRAPPER}} .bdt-ep-hover-box .bdt-ep-hover-box-item',
			]
		);

		$this->add_responsive_control(
			'box_item_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-hover-box-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
				'condition' => [
					'box_item_radius_advanced_show!' => 'yes',
				],
			]
		);

		$this->add_control(
			'box_item_radius_advanced_show',
			[
				'label' => esc_html__('Advanced Radius', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'box_item_radius_advanced',
			[
				'label'       => esc_html__('Radius', 'bdthemes-element-pack'),
				'description' => sprintf(esc_html__('For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '75% 25% 43% 57% / 46% 29% 71% 54%', 'https://9elements.github.io/fancy-border-radius/'),
				'type'        => Controls_Manager::TEXT,
				'size_units'  => ['px', '%'],
				'default'     => '75% 25% 43% 57% / 46% 29% 71% 54%',
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-hover-box-item'     => 'border-radius: {{VALUE}}; overflow: hidden;',
				],
				'condition' => [
					'box_item_radius_advanced_show' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_item_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-hover-box-item'
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'box_item_hover',
			[
				'label' => esc_html__('hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'box_item_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-hover-box-item:hover',
			]
		);

		$this->add_control(
			'box_item_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item:hover'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_item_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_item_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-hover-box-item:hover',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'box_item_active',
			[
				'label' => esc_html__('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'box_item_active_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-hover-box-item.active',
			]
		);

		$this->add_control(
			'box_item_active_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item.active'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_item_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_item_active_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-hover-box-item.active',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon_box',
			[
				'label'      => esc_html__('Icon', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition'  => [
					'show_icon' => 'yes',
					'_skin!' => 'bdt-flexure',
				]
			]
		);

		$this->start_controls_tabs('icon_colors');

		$this->start_controls_tab(
			'icon_colors_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-icon-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-hover-box-icon-wrap svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-hover-box-icon-wrap',
				'fields_options' => [
					'background' => [
						'label' => esc_html__('Background Type', 'bdthemes-element-pack') . BDTEP_NC,
					],
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__('Icon Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'vh', 'vw'],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-icon-wrap' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'rotate',
			[
				'label'   => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'max'  => 360,
						'min'  => -360,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-icon-wrap i, {{WRAPPER}} .bdt-ep-hover-box-icon-wrap svg'   => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-icon-wrap  ' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-hover-box-icon-wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'icon_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack') . BDTEP_NC,
				'selector'    => '{{WRAPPER}} .bdt-ep-hover-box-icon-wrap',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'icon_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-hover-box-icon-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'border_radius_advanced_show!' => 'yes',
				],
			]
		);

		$this->add_control(
			'border_radius_advanced_show',
			[
				'label' => esc_html__('Advanced Radius', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'icon_border_radius_advanced',
			[
				'label'       => esc_html__('Radius', 'bdthemes-element-pack') . BDTEP_NC,
				'description' => sprintf(esc_html__('For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '30% 70% 82% 18% / 46% 62% 38% 54%', 'https://9elements.github.io/fancy-border-radius/'),
				'type'        => Controls_Manager::TEXT,
				'size_units'  => ['px', '%'],
				'separator'   => 'after',
				'default'     => '30% 70% 82% 18% / 46% 62% 38% 54%',
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-hover-box-icon-wrap'     => 'border-radius: {{VALUE}}; overflow: hidden;',
				],
				'condition' => [
					'border_radius_advanced_show' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-hover-box-icon-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_hover_color',
			[
				'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item:hover .bdt-ep-hover-box-icon-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-hover-box-item:hover .bdt-ep-hover-box-icon-wrap svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_button_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-hover-box-item:hover .bdt-ep-hover-box-icon-wrap',
				'fields_options' => [
					'background' => [
						'label' => esc_html__('Background Type', 'bdthemes-element-pack') . BDTEP_NC,
					],
				],
			]
		);

		$this->add_control(
			'icon_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item:hover .bdt-ep-hover-box-icon-wrap' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_hover_rotate',
			[
				'label'   => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'deg',
					'size' => 90
				],
				'range' => [
					'deg' => [
						'max'  => 360,
						'min'  => -360,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item:hover .bdt-ep-hover-box-icon-wrap i, {{WRAPPER}} .bdt-ep-hover-box-item:hover .bdt-ep-hover-box-icon-wrap svg'   => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_active',
			[
				'label' => esc_html__('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_active_color',
			[
				'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item.active .bdt-ep-hover-box-icon-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-hover-box-item.active .bdt-ep-hover-box-icon-wrap svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_button_active_background_color',
				'selector'  => '{{WRAPPER}} .bdt-ep-hover-box-item.active .bdt-ep-hover-box-icon-wrap',
				'fields_options' => [
					'background' => [
						'label' => esc_html__('Background Type', 'bdthemes-element-pack') . BDTEP_NC,
					],
				],
			]
		);

		$this->add_control(
			'icon_button_active_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item.active .bdt-ep-hover-box-icon-wrap' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__('Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-title, {{WRAPPER}} .bdt-ep-hover-box-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item:hover .bdt-ep-hover-box-title, {{WRAPPER}} .bdt-ep-hover-box-item:hover .bdt-ep-hover-box-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_active_color',
			[
				'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item.active .bdt-ep-hover-box-title, {{WRAPPER}} .bdt-ep-hover-box-item.active .bdt-ep-hover-box-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-hover-box-title, {{WRAPPER}} .bdt-ep-hover-box-title a',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_sub_title',
			[
				'label'     => esc_html__('Sub Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_sub_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'sub_title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-sub-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-hover-box-skin-flexure .bdt-ep-hover-box-sub-title:before' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sub_title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item:hover .bdt-ep-hover-box-sub-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-hover-box-skin-flexure .bdt-ep-hover-box-item:hover .bdt-ep-hover-box-sub-title:before' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sub_title_active_color',
			[
				'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item.active .bdt-ep-hover-box-sub-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-hover-box-skin-flexure .bdt-ep-hover-box-item.active .bdt-ep-hover-box-sub-title:before' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_title_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-sub-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .bdt-ep-hover-box-skin-flexure .bdt-ep-hover-box-sub-title' => 'margin-left: {{SIZE}}{{UNIT}}; padding-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_title_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-hover-box-sub-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_description',
			[
				'label'     => esc_html__('Text', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_content' => ['yes'],
					'_skin!' => 'bdt-flexure',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'description_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item:hover .bdt-ep-hover-box-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'description_active_color',
			[
				'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item.active .bdt-ep-hover-box-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'description_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-text' => 'padding-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-hover-box-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__('Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_button' => 'yes',
					'_skin!' => 'bdt-flexure',
				],
			]
		);

		$this->start_controls_tabs('hover_box_button_style');

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
					'{{WRAPPER}} .bdt-ep-hover-box-button a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-hover-box-button a',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-hover-box-button a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'selector'    => '{{WRAPPER}} .bdt-ep-hover-box-button a',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-hover-box-button a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'icon_border_radius_advanced_show!' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_border_radius_advanced_show',
			[
				'label' => esc_html__('Advanced Radius', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'border_radius_advanced',
			[
				'label'       => esc_html__('Radius', 'bdthemes-element-pack'),
				'description' => sprintf(esc_html__('For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '30% 70% 82% 18% / 46% 62% 38% 54%', 'https://9elements.github.io/fancy-border-radius/'),
				'type'        => Controls_Manager::TEXT,
				'size_units'  => ['px', '%'],
				'separator'   => 'after',
				'default'     => '30% 70% 82% 18% / 46% 62% 38% 54%',
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-hover-box-button a'     => 'border-radius: {{VALUE}}; overflow: hidden;',
				],
				'condition' => [
					'icon_border_radius_advanced_show' => 'yes',
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
					'{{WRAPPER}} .bdt-ep-hover-box-button a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-ep-hover-box-button a',
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
			'button_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item:hover .bdt-ep-hover-box-button a'  => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-hover-box-item:hover .bdt-ep-hover-box-button a',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item:hover .bdt-ep-hover-box-button a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_active',
			[
				'label' => esc_html__('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_active_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item.active .bdt-ep-hover-box-button a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_active_background_color',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item.active .bdt-ep-hover-box-button a' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_active_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-item.active .bdt-ep-hover-box-button a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-button' => 'padding-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation_arrows',
			[
				'label'     => esc_html__('Navigation Arrows', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_navigation_arrows' => 'yes',
					'_skin' => 'bdt-envelope',
				],
			]
		);

		$this->start_controls_tabs('hover_box_arrows_style');

		$this->start_controls_tab(
			'tab_arrows_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'arrows_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-skin-envelope .bdt-slidenav' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'arrows_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-hover-box-skin-envelope .bdt-slidenav',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'arrows_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'selector'    => '{{WRAPPER}} .bdt-ep-hover-box-skin-envelope .bdt-slidenav',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'arrows_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-hover-box-skin-envelope .bdt-slidenav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-ep-hover-box-skin-envelope .bdt-slidenav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-hover-box-skin-envelope .bdt-slidenav' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'arrows_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-ep-hover-box-skin-envelope .bdt-slidenav',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'arrows_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-hover-box-skin-envelope .bdt-slidenav',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_arrows_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-skin-envelope .bdt-slidenav:hover'  => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'arrows_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-hover-box-skin-envelope .bdt-slidenav:hover',
			]
		);

		$this->add_control(
			'arrows_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'arrows_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-hover-box-skin-envelope .bdt-slidenav:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'arrows_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-hover-box-skin-envelope .bdt-slidenav:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function activeItem($active_item, $totalItem) {
		$active_item = (int) $active_item;
		return $active_item = ($active_item <= 0 || $active_item > $totalItem ? 1 : $active_item);
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ($settings['hover_box_event']) {
			$hoverBoxEvent = $settings['hover_box_event'];
		} else {
			$hoverBoxEvent = false;
		}

		if ($settings['box_image_effect']) {
			$this->add_render_attribute('hover_box', 'class', 'bdt-ep-hover-box-img-effect bdt-' . $settings['box_image_effect_select']);
		}

		$this->add_render_attribute(
			[
				'hover_box' => [
					'id' => 'bdt-ep-hover-box-' . $this->get_id(),
					'class' => 'bdt-ep-hover-box bdt-ep-hover-box-default bdt-ep-hover-box-' . $settings['layout_style'],

					'data-settings' => [
						wp_json_encode(array_filter([
							'box_id' => 'bdt-ep-hover-box-' . $this->get_id(),
							'mouse_event' => $hoverBoxEvent,
						]))
					]
				]
			]
		);

?>
		<div <?php echo $this->get_render_attribute_string('hover_box'); ?>>

			<?php $this->box_content(); ?>
			<?php $this->box_items(); ?>

		</div>

	<?php
	}

	public function box_content() {
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

	?>

		<?php foreach ($settings['hover_box'] as $index => $item) :
			$tab_count = $index + 1;
			$tab_id    = 'bdt-box-' . $tab_count . esc_attr($id);

			$slide_image = Group_Control_Image_Size::get_attachment_image_src($item['slide_image']['id'], 'thumbnail_size', $settings);
			if (!$slide_image) {
				$slide_image = $item['slide_image']['url'];
			}
			if ($settings['_skin'] == 'bdt-flexure') {
				$this->add_render_attribute('hover-box-content', 'class', 'bdt-ep-hover-box-content', true);
			} else {
				$active_item = $this->activeItem($settings['hover_box_active_item'], count($settings['hover_box']));

				if ($tab_id    == 'bdt-box-' . $active_item . esc_attr($id)) {
					$this->add_render_attribute('hover-box-content', 'class', 'bdt-ep-hover-box-content active', true);
				} else {
					$this->add_render_attribute('hover-box-content', 'class', 'bdt-ep-hover-box-content', true);
				}
			}
			$this->add_render_attribute('hover-box-content-img', 'class', 'bdt-ep-hover-box-img elementor-repeater-item-' . esc_attr($item['_id']), true);

		?>

			<div id="<?php echo esc_attr($tab_id); ?>" <?php echo ($this->get_render_attribute_string('hover-box-content')); ?>>

				<?php if ($slide_image) : ?>
					<div class="bdt-ep-hover-box-img" style="background-image: url('<?php echo esc_url($slide_image); ?>');"></div>
				<?php else : ?>
					<div <?php echo ($this->get_render_attribute_string('hover-box-content-img')); ?>></div>
				<?php endif; ?>

			</div>
		<?php endforeach; ?>

	<?php
	}

	public function box_items() {
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		$desktop_cols = isset($settings["columns"]) ? (int)$settings["columns"] : 3;
		$tablet_cols  = isset($settings["columns_tablet"]) ? (int)$settings["columns_tablet"] : 2;
		$mobile_cols  = isset($settings["columns_mobile"]) ? (int)$settings["columns_mobile"] : 2;

		if ('yes' == $settings['match_height']) {
			$this->add_render_attribute('box-settings', 'bdt-height-match', 'target: > div > div > .bdt-ep-hover-box-item; row: false;');
		}

		$this->add_render_attribute('box-settings', 'data-bdt-ep-hover-box-items', 'connect: #bdt-box-content-' .  esc_attr($id) . ';');
		$this->add_render_attribute('box-settings', 'class', ['bdt-ep-hover-box-item-wrap', 'bdt-position-' . $settings['content_gap'], 'bdt-position-' . $settings['default_content_position']]);

		$text_hide_on_setup = '';

		if (!empty($settings['text_hide_on'])) {
			foreach ($settings['text_hide_on'] as $element) {

				if ($element == 'desktop') {
					$text_hide_on_setup .= ' bdt-desktop';
				}
				if ($element == 'tablet') {
					$text_hide_on_setup .= ' bdt-tablet';
				}
				if ($element == 'mobile') {
					$text_hide_on_setup .= ' bdt-mobile';
				}
			}
		}


	?>
		<div <?php echo ($this->get_render_attribute_string('box-settings')); ?>>
			<div class="bdt-grid bdt-grid-<?php echo esc_attr($settings['column_gap']); ?> bdt-child-width-1-<?php echo esc_attr($mobile_cols); ?> bdt-child-width-1-<?php echo esc_attr($tablet_cols); ?>@s bdt-child-width-1-<?php echo esc_attr($desktop_cols); ?>@l" data-bdt-grid>

				<?php foreach ($settings['hover_box'] as $index => $item) :

					$tab_count = $index + 1;
					$tab_id    = 'bdt-box-' . $tab_count . esc_attr($id);


					$active_item = $this->activeItem($settings['hover_box_active_item'], count($settings['hover_box']));

					if ($tab_id    == 'bdt-box-' . $active_item . esc_attr($id)) {
						$this->add_render_attribute('box-item', 'class', 'bdt-ep-hover-box-item active', true);
					} else {
						$this->add_render_attribute('box-item', 'class', 'bdt-ep-hover-box-item', true);
					}

					$this->add_render_attribute('bdt-ep-hover-box-title', 'class', 'bdt-ep-hover-box-title', true);

					$title_key = 'title_' . $index;
					$button_key = 'button_' . $index;
					$this->add_render_attribute($title_key, 'class', 'bdt-ep-hover-box-title-link', true);
					$this->add_link_attributes($title_key, isset($item['title_link']) ? $item['title_link'] : []);
					$this->add_link_attributes($button_key, isset($item['button_link']) ? $item['button_link'] : []);

				?>
					<div>
						<div <?php echo ($this->get_render_attribute_string('box-item')); ?> data-id="<?php echo esc_attr($tab_id); ?>">

							<?php if ('yes' == $settings['show_icon']) : ?>
								<a class="bdt-ep-hover-box-icon-box" href="javascript:void(0);" data-tab-index="<?php echo esc_attr($index); ?>">
									<span class="bdt-ep-hover-box-icon-wrap">
										<?php Icons_Manager::render_icon($item['selected_icon'], ['aria-hidden' => 'true']); ?>
									</span>
								</a>
							<?php endif; ?>

							<?php if ($item['hover_box_sub_title'] && ('yes' == $settings['show_sub_title'])) : ?>
								<div class="bdt-ep-hover-box-sub-title">
									<?php echo wp_kses($item['hover_box_sub_title'], element_pack_allow_tags('title')); ?>
								</div>
							<?php endif; ?>

							<?php if ($item['hover_box_title'] && ('yes' == $settings['show_title'])) : ?>
								<<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?> <?php echo $this->get_render_attribute_string('bdt-ep-hover-box-title'); ?>>

									<?php if ('' !== $item['title_link']['url']) : ?>
										<a <?php echo $this->get_render_attribute_string($title_key); ?>>
										<?php endif; ?>
										<?php echo wp_kses($item['hover_box_title'], element_pack_allow_tags('title')); ?>
										<?php if ('' !== $item['title_link']['url']) : ?>
										</a>
									<?php endif; ?>

								</<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?>>
							<?php endif; ?>

							<?php if ($item['hover_box_content'] && ('yes' == $settings['show_content'])) : ?>
								<div class="bdt-ep-hover-box-text <?php echo esc_attr($text_hide_on_setup); ?>">
									<?php echo $this->parse_text_editor($item['hover_box_content']); ?>
								</div>
							<?php endif; ?>

							<?php if ($item['hover_box_button'] && ('yes' == $settings['show_button'])) : ?>
								<div class="bdt-ep-hover-box-button">
									<a <?php echo $this->get_render_attribute_string($button_key); ?>>
										<?php echo wp_kses_post($item['hover_box_button']); ?>
									</a>
								</div>
							<?php endif; ?>

						</div>
					</div>
				<?php endforeach; ?>

			</div>
		</div>
<?php
	}
}
