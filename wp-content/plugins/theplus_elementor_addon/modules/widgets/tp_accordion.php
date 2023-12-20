<?php 
/*
Widget Name: Accordion/FAQ
Description: Toggle of faq/accordion.
Author: Theplus
Author URI: https://posimyth.com
*/
namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class ThePlus_Accordion extends Widget_Base {

	public $TpDoc = THEPLUS_TPDOC;
		
	public function get_name() {
		return 'tp-accordion';
	}

    public function get_title() {
        return esc_html__('Accordion', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-lightbulb-o theplus_backend_icon';
    }

	public function get_custom_help_url() {
		$DocUrl = $this->TpDoc . "accordion";

		return esc_url($DocUrl);
	}

    public function get_categories() {
        return array('plus-tabbed');
    }
	
	public function get_keywords() {
		return [ 'accordion', 'tabs', 'toggle' ];
	}
	
    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'tab_title',
			[
				'label' => esc_html__( 'Title & Content', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Accordion Title' , 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'content_source',
			[
				'label' => wp_kses_post( "Content Source <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-accordion-widget-settings-overview/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'content',
				'options' => [
					'content'  => esc_html__( 'Content', 'theplus' ),
					'page_template' => esc_html__( 'Page Template', 'theplus' ),
				],
			]
		);
		$repeater->add_control(
		'tab_content',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Accordion Content', 'theplus' ),
				'show_label' => false,
				'dynamic' => [
					'active'   => true,
				],
				'condition'    => [
					'content_source' => [ 'content' ],
				],
			]
		);
		$repeater->add_control(
			'content_template',
			[
				'label'       => esc_html__( 'Elementor Templates', 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => theplus_get_templates(),
				'label_block' => 'true',
				'condition'   => ['content_source' => "page_template"],
			]
		);
		$repeater->add_control(
			'backend_preview_template',[
				'label'   => esc_html__( 'Backend Visibility', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),	
				'description' => esc_html__( 'Note : If disabled, Template will not visible/load in the backend for better page loading performance.', 'theplus' ),
				'separator' => 'after',
			]
		);
		$repeater->add_control(
			'display_icon',[
				'label'   => esc_html__( 'Show Icon', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'icon_style',
			[
				'label' => esc_html__( 'Icon Font', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'font_awesome',
				'options' => [
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'font_awesome_5'  => esc_html__( 'Font Awesome 5', 'theplus' ),
					'icon_mind' => esc_html__( 'Icons Mind', 'theplus' ),
				],
				'condition' => [
					'display_icon' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'icon_fontawesome',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-download',
				'condition' => [
					'display_icon' => 'yes',
					'icon_style' => 'font_awesome',
				],
			]
		);
		$repeater->add_control(
			'icon_fontawesome_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'solid',
				],
				'condition' => [
					'display_icon' => 'yes',
					'icon_style' => 'font_awesome_5',
				],	
			]
		);
		$repeater->add_control(
			'icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'iconsmind-Download-2',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'display_icon' => 'yes',
					'icon_style' => 'icon_mind',
				],
			]
		);
		$repeater->add_control(
			'tab_hashid',
			[
				'label' => wp_kses_post( "Unique ID <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "anchor-link-specific-accordion-item/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [
					'active' => true,
				],
				'title' => __( 'Note : Use this option to give anchor id to individual accordion.', 'theplus' ),
				'description' => 'Note : Use this option to give anchor id to individual accordion.',
				'label_block' => false,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'tabs',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'tab_title' => esc_html__( 'Accordion #1', 'theplus' ),
						'tab_content' => esc_html__( 'I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'theplus' ),
					],
					[
						'tab_title' => esc_html__( 'Accordion #2', 'theplus' ),
						'tab_content' => esc_html__( 'I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'theplus' ),
					],
				],
				'title_field' => '{{{ tab_title }}}',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'icon_content_section',
			[
				'label' => esc_html__( 'Icon Option', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'display_icon',[
				'label'   => esc_html__( 'Show Icon', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),	
			]
		);
		$this->add_control(
			'icon_style',
			[
				'label' => esc_html__( 'Icon Font', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'font_awesome',
				'options' => [
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'font_awesome_5'  => esc_html__( 'Font Awesome 5', 'theplus' ),
					'icon_mind' => esc_html__( 'Icons Mind', 'theplus' ),
				],
				'condition' => [
					'display_icon' => 'yes',
				],
			]
		);
		$this->add_control(
			'icon_fontawesome',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-plus',
				'condition' => [
					'display_icon' => 'yes',
					'icon_style' => 'font_awesome',
				],
			]
		);
		$this->add_control(
			'icon_fontawesome_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'solid',
				],
				'condition' => [
					'display_icon' => 'yes',
					'icon_style' => 'font_awesome_5',
				],	
			]
		);
		$this->add_control(
			'icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'iconsmind-Add',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'display_icon' => 'yes',
					'icon_style' => 'icon_mind',
				],
			]
		);
		$this->add_control(
			'icon_fontawesome_active',
			[
				'label' => esc_html__( 'Active Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-minus',
				'condition' => [
					'display_icon' => 'yes',
					'icon_style' => 'font_awesome',
				],
			]
		);
		$this->add_control(
			'icon_fontawesome_5_active',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-minus',
					'library' => 'solid',
				],
				'condition' => [
					'display_icon' => 'yes',
					'icon_style' => 'font_awesome_5',
				],	
			]
		);
		$this->add_control(
			'icons_mind_active',
			[
				'label' => esc_html__( 'Active Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'iconsmind-Add',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'display_icon' => 'yes',
					'icon_style' => 'icon_mind',
				],
			]
		);
		$this->add_control(
			'title_html_tag',
			[
				'label' => esc_html__( 'Title HTML Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
				],
				'default' => 'div',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'extra_content_section',
			[
				'label' => esc_html__( 'Special Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'active_accordion',
			[
				'label' => wp_kses_post( "Active Accordion <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'options' => theplus_get_numbers(),
			]
		);
		$this->add_control(
			'on_hover_accordion',[
				'label' => wp_kses_post( "On Hover Accordion <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-accordion-on-hover/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'separator' => 'before',
			]
		);
		$this->add_control(
			'horizontal_accordion',[
				'label' => wp_kses_post( "Horizontal Accordion <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-horizontal-accordion/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'separator' => 'before',
				'condition'    => [
					'on_hover_accordion!' => 'yes',
				],
			]
		);
		$this->add_control(
			'horizontal_accordion_layout',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'tp_hal_1',
				'options' => [
					'tp_hal_1'  => esc_html__( 'Layout 1', 'theplus' ),
					'tp_hal_2' => esc_html__( 'Layout 2', 'theplus' ),
				],
				'condition'    => [
					'on_hover_accordion!' => 'yes',
					'horizontal_accordion' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
            'horizontal_accordion_min_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 700,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 300,
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-acc-hori' => 'min-height: {{SIZE}}{{UNIT}};max-height: {{SIZE}}{{UNIT}}',
				],
				'condition'    => [
					'on_hover_accordion!' => 'yes',
					'horizontal_accordion' => 'yes',
				],
            ]
        );
		$this->add_control(
			'horizontal_accordion_title_width',
			[
				'label' => esc_html__( 'Title Width', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 300,
				'step' => 10,
				'default' => 60,
				'condition'    => [
					'on_hover_accordion!' => 'yes',
					'horizontal_accordion' => 'yes',
				],
			]
		);
		$this->add_control(
			'horizontal_accordion_open_speed',
			[
				'label' => esc_html__( 'Open Speed', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 3000,
				'step' => 10,
				'default' => 400,
				'condition'    => [
					'on_hover_accordion!' => 'yes',
					'horizontal_accordion' => 'yes',
				],
			]
		);
		$this->add_control(
			'horizontal_accordion_close_speed',
			[
				'label' => esc_html__( 'Close Speed', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 3000,
				'step' => 10,
				'default' => 400,
				'condition'    => [
					'on_hover_accordion!' => 'yes',
					'horizontal_accordion' => 'yes',
				],
			]
		);
		$this->add_control(
			'expand_collapse_accordion',[
				'label' => wp_kses_post( "Expand/Collapse Content Button <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "expand-close-elementor-accordion-button/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'separator' => 'before',
				'description' => 'Note : If enabled, You will get button option to expand/collapse content of all accordions together.',
				'condition'    => [
					'horizontal_accordion!' => 'yes',
				],
			]
		);
		$this->add_control(
			'eca_position',
			[
				'label' => esc_html__( 'Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'ecabefore',
				'options' => [
					'ecabefore'  => esc_html__( 'Before', 'theplus' ),
					'ecaafter' => esc_html__( 'After', 'theplus' ),
				],
				'condition' => [
					'horizontal_accordion!' => 'yes',
					'expand_collapse_accordion' => 'yes',
				],
			]
		);
		$this->add_control(
			'eca_colall',
			[
				'label' => esc_html__( 'Collapse', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Collapse All', 'theplus' ),
				'condition' => [
					'horizontal_accordion!' => 'yes',
					'expand_collapse_accordion' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .tp-toggle-accordion.active:before' => 'content: "{{VALUE}}";',
				],
			]
		);
		$this->add_control(
			'eca_expall',
			[
				'label' => esc_html__( 'Expand', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Expand All', 'theplus' ),
				'condition' => [
					'horizontal_accordion!' => 'yes',
					'expand_collapse_accordion' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .tp-toggle-accordion:before' => 'content: "{{VALUE}}";',
				],
			]
		);
		$this->add_responsive_control(
			'eca_align',
			[
				'label'   => esc_html__( 'Alignment', 'theplus' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'flex-start',
				'options' => [
					'flex-start'    => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tp-aec-button' => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					'horizontal_accordion!' => 'yes',
					'expand_collapse_accordion' => 'yes',
				],
			]
		);		
		$this->add_control(
			'accordion_scroll_top',[
				'label'   => esc_html__( 'Scroll Top', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'description' => 'Note : If enabled, When you click on accordion, It will scroll to top automatically.',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'act_offset',
			[
				'label' => esc_html__( 'Offset', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 500,
				'step' => 10,
				'default' => 0,
				'condition'    => [
					'accordion_scroll_top' => 'yes',
				],
			]
		);
		$this->add_control(
			'act_speed',
			[
				'label' => esc_html__( 'Speed', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 2000,
				'step' => 10,
				'default' => 500,
				'condition'    => [
					'accordion_scroll_top' => 'yes',
				],
			]
		);
		$this->add_control(
			'accordion_stager',[
				'label'   => esc_html__( 'Stagger Animation', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'separator' => 'before',
			]
		);
		$this->add_control(
			'accordion_stager_visi_delay',
			[
				'label' => esc_html__( 'Visibility Delay', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10000,
				'step' => 100,
				'default' => 500,
				'condition'    => [
					'accordion_stager' => 'yes',
				],
			]
		);
		$this->add_control(
			'accordion_stager_gap',
			[
				'label' => esc_html__( 'Gap', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 50,
				'max' => 5000,
				'step' => 100,
				'default' => 500,
				'condition'    => [
					'accordion_stager' => 'yes',
				],
			]
		);
		$this->add_control(
			'search_accordion',[
				'label' => wp_kses_post( "Search <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-accordion-search/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'separator' => 'before',
			]
		);
		$this->add_control(
			'search_text_highlight',
			[
				'label' => wp_kses_post( "Search Text Highlight", 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => '',
				'condition'    => [
					'search_accordion' => 'yes',
				],
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'separator' => 'before',
			]
		);
		
		$this->add_control(
			'search_accordion_length',
			[
				'label' => esc_html__( 'Length', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 15,
				'step' => 1,
				'default' => 3,
				'condition'    => [
					'search_accordion' => 'yes',
				],
			]
		);
		$this->add_control(
			'search_accordion_placeholder',
			[
				'label' => esc_html__( 'Placeholder', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Enter Search' , 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
				'condition'    => [
					'search_accordion' => 'yes',
				],
				'label_block' => true,
			]
		);
		$this->add_control(
			'search_accordion_icon',
			[
				'label' => esc_html__( 'Search Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-search',
					'library' => 'solid',
				],
				'condition' => [
					'search_accordion' => 'yes',
				],	
			]
		);
		$this->add_control(
			'slider_accordion',[
				'label' => wp_kses_post( "Slider/Pagination <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-accordion-pagination/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'separator' => 'before',
				'description' => 'Note: By enabling this option, You will be able to divide accordions in multiple slides/section with previous and next options.',
				'condition'    => [
					'horizontal_accordion!' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_accordion_show',
			[
				'label' => esc_html__( 'Slide Per Page', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 3,
				'max' => 20,
				'step' => 1,
				'default' => 3,
				'condition'    => [
					'horizontal_accordion!' => 'yes',
					'slider_accordion' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_accordion_icon_prev',
			[
				'label' => esc_html__( 'Prev. Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-arrow-left',
					'library' => 'solid',
				],
				'condition'    => [
					'horizontal_accordion!' => 'yes',
					'slider_accordion' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_accordion_text_prev',
			[
				'label' => esc_html__( 'Previous Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'condition'    => [
					'horizontal_accordion!' => 'yes',
					'slider_accordion' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_accordion_icon_nxt',
			[
				'label' => esc_html__( 'Next Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-arrow-right',
					'library' => 'solid',
				],
				'condition'    => [
					'horizontal_accordion!' => 'yes',
					'slider_accordion' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_accordion_text_nxt',
			[
				'label' => esc_html__( 'Next Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'condition'    => [
					'horizontal_accordion!' => 'yes',
					'slider_accordion' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_accordion_align',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'tpsaleft' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'tpsacenter' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'tpsaright' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default' => 'tpsaleft',
				'condition'    => [
					'horizontal_accordion!' => 'yes',
					'slider_accordion' => 'yes',
				],
				'label_block' => false,
				'toggle' => true,
			]
		);
		
		/*autoplay*/
		$this->add_control(
			'tabs_autoplay',
			[
				'label' => wp_kses_post( "Autoplay <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-accordion-autoplay/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'tabs_autoplaypause',
			[
				'label' => esc_html__( 'Play/Pause Button', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'tabs_autoplay' => 'yes',
				],
			]
		);
		$this->add_control(
			'autoplayicon',
			[
				'label' => esc_html__( 'Play Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-play',
					'library' => 'solid',
				],
				'condition' => [
					'tabs_autoplay' => 'yes',
					'tabs_autoplaypause' => 'yes',
				],	
			]
		);
		$this->add_control(
			'autopauseicon',
			[
				'label' => esc_html__( 'Pause Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-pause',
					'library' => 'solid',
				],
				'condition' => [
					'tabs_autoplay' => 'yes',
					'tabs_autoplaypause' => 'yes',
				],	
			]
		);
		$this->add_responsive_control(
			'tabs_autoplay_duration',
			[
				'label' => esc_html__( 'Duration', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'default' => 5,
				'selectors' => [
					'{{WRAPPER}} .tp-tab-playloop .plus-tab-header.active:after' => 'transition: transform {{VALUE}}000ms ease-in;',
				],
				'condition' => [
					'tabs_autoplay' => 'yes',
				],
			]
		);
		$this->add_control(
			'tabs_autoplay_border_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'condition' => [
					'tabs_autoplay' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'tabs_autoplay_border_size',
			[
				'label' => esc_html__( 'Border Size', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'default' => 3,
				'selectors' => [
					'{{WRAPPER}} .tp-tab-playloop .plus-tab-header:after' => 'border-bottom: solid {{VALUE}}px {{tabs_autoplay_border_color.VALUE}};',
				],
				'condition' => [
					'tabs_autoplay' => 'yes',
				],
			]
		);
		$this->add_control(
			'schema_accordion',[
				'label' => wp_kses_post( "SEO Schema Markup <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-accordion-schema-markup/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'separator' => 'before',
				'description' => 'Note : By enabling this option, Your content of Accordions will be converted in to Structured data based on Google Search engine. It will be considered as <a rel="noopener noreferrer" target="_blank" href="https://developers.google.com/search/docs/advanced/structured-data/faqpage">FAQ Schema</a>',
			]
		);
		$this->add_control(
			'connection_unique_id',
			[
				'label' => wp_kses_post( "Carousel Connection ID <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "carousel-widgets-remotesync-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'description' => 'Note : This option is to connect Accordions with Anything Carousel widget. Use same id both places for deep connection.',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_toggle_style_icon',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'display_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Start', 'theplus' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'End', 'theplus' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => is_rtl() ? 'right' : 'left',
				'toggle' => false,
				'label_block' => false,
				'condition' => [
					'display_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion .elementor-tab-title .elementor-accordion-icon i:before' => 'color: {{VALUE}};',					
					'{{WRAPPER}} .elementor-accordion .elementor-tab-title .elementor-accordion-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'display_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_active_color',
			[
				'label' => esc_html__( 'Active Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion .elementor-tab-title.active .elementor-accordion-icon i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-accordion .elementor-tab-title.active .elementor-accordion-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'display_icon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_space',
			[
				'label' => esc_html__( 'Gap', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion .elementor-accordion-icon.elementor-accordion-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-accordion .elementor-accordion-icon.elementor-accordion-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'display_icon' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'toggle_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper.elementor-accordion .elementor-tab-title .elementor-accordion-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .theplus-accordion-wrapper.elementor-accordion .elementor-tab-title .elementor-accordion-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'display_icon' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header',
			]
		);
		$this->add_control(
			'title_align',
			[
				'label' => esc_html__( 'Title Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'text-left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'text-center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'text-right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'text-left',
				'label_block' => false,
			]
		);
		$this->add_responsive_control(
            'title_gap',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gap', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 250,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 68,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header' => 'line-height: {{SIZE}}{{UNIT}};',
				],				
				'condition' => [
					'horizontal_accordion' => 'yes',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_title_style' );
		$this->start_controls_tab(
			'tab_title_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'title_color_option',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Classic', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'label_block' => false,
				'default' => 'solid',
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header' => 'color: {{VALUE}}',
				],
				'condition' => [
					'title_color_option' => 'solid',
				],
			]
		);
		$this->add_control(
            'title_gradient_color1',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition' => [
					'title_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_gradient_color1_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'title_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_gradient_color2',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition' => [
					'title_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_gradient_color2_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
					],
				'render_type' => 'ui',
				'condition' => [
					'title_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_gradient_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Gradient Style', 'theplus'),
                'default' => 'linear',
                'options' => theplus_get_gradient_styles(),
				'condition' => [
					'title_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_gradient_angle', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{title_gradient_color1.VALUE}} {{title_gradient_color1_control.SIZE}}{{title_gradient_color1_control.UNIT}}, {{title_gradient_color2.VALUE}} {{title_gradient_color2_control.SIZE}}{{title_gradient_color2_control.UNIT}});-webkit-background-clip: text;-webkit-text-fill-color: transparent;',
				],
				'condition'    => [
					'title_color_option' => 'gradient',
					'title_gradient_style' => ['linear']
				],
				'of_type' => 'gradient',
			]
        );
		$this->add_control(
            'title_gradient_position', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Position', 'theplus'),
				'options' => theplus_get_position_options(),
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{title_gradient_color1.VALUE}} {{title_gradient_color1_control.SIZE}}{{title_gradient_color1_control.UNIT}}, {{title_gradient_color2.VALUE}} {{title_gradient_color2_control.SIZE}}{{title_gradient_color2_control.UNIT}});-webkit-background-clip: text;-webkit-text-fill-color: transparent;',
				],
				'condition' => [
					'title_color_option' => 'gradient',
					'title_gradient_style' => 'radial',
			],
			'of_type' => 'gradient',
			]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_title_active',
			[
				'label' => esc_html__( 'Active', 'theplus' ),
			]
		);
		$this->add_control(
			'title_active_color_option',
			[
				'label' => esc_html__( 'Title Active Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Classic', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'label_block' => false,
				'default' => 'solid',
			]
		);
		$this->add_control(
			'title_active_color',
			[
				'label' => esc_html__( 'Active Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#3351a6',
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header.active' => 'color: {{VALUE}}',
				],
				'condition' => [
					'title_active_color_option' => 'solid',
				],
			]
		);
		$this->add_control(
            'title_active_gradient_color1',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition' => [
					'title_active_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_active_gradient_color1_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'title_active_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_active_gradient_color2',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition' => [
					'title_active_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_active_gradient_color2_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
					],
				'render_type' => 'ui',
				'condition' => [
					'title_active_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_active_gradient_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Gradient Style', 'theplus'),
                'default' => 'linear',
                'options' => theplus_get_gradient_styles(),
				'condition' => [
					'title_active_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_active_gradient_angle', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header.active' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{title_active_gradient_color1.VALUE}} {{title_active_gradient_color1_control.SIZE}}{{title_active_gradient_color1_control.UNIT}}, {{title_active_gradient_color2.VALUE}} {{title_active_gradient_color2_control.SIZE}}{{title_active_gradient_color2_control.UNIT}});-webkit-background-clip: text;-webkit-text-fill-color: transparent;',
				],
				'condition'    => [
					'title_active_color_option' => 'gradient',
					'title_active_gradient_style' => ['linear']
				],
				'of_type' => 'gradient',
			]
        );
		$this->add_control(
            'title_active_gradient_position', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Position', 'theplus'),
				'options' => theplus_get_position_options(),
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header.active' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{title_active_gradient_color1.VALUE}} {{title_active_gradient_color1_control.SIZE}}{{title_active_gradient_color1_control.UNIT}}, {{title_active_gradient_color2.VALUE}} {{title_active_gradient_color2_control.SIZE}}{{title_active_gradient_color2_control.UNIT}});-webkit-background-clip: text;-webkit-text-fill-color: transparent;',
				],
				'condition' => [
					'title_active_color_option' => 'gradient',
					'title_active_gradient_style' => 'radial',
			],
			'of_type' => 'gradient',
			]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'loop_icon_heading',
			[
				'label' => esc_html__( 'Icon Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'loop_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .plus-accordion-header .accordion-icon-prefix' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .theplus-accordion-wrapper .plus-accordion-header .accordion-icon-prefix svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'loop_icon_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Width', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 250,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 35,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .plus-accordion-header .accordion-icon-prefix' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}} ;text-align: center;',
				],
            ]
        );
		$this->add_responsive_control(
            'loop_icon_indent',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Space/Indent', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .plus-accordion-header .accordion-icon-prefix,{{WRAPPER}} .theplus-accordion-wrapper .plus-accordion-header .accordion-icon-prefix svg' => 'margin-right: {{SIZE}}{{UNIT}}',			
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_loop_icon_style' );
		$this->start_controls_tab(
			'tab_loop_icon_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'loop_icon_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .plus-accordion-header .accordion-icon-prefix' => 'color: {{VALUE}};-webkit-text-fill-color: {{VALUE}};',
					'{{WRAPPER}} .theplus-accordion-wrapper .plus-accordion-header .accordion-icon-prefix svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'loop_icon_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .theplus-accordion-wrapper .plus-accordion-header .accordion-icon-prefix',				
			]
		);
		$this->add_responsive_control(
			'loop_icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper .plus-accordion-header .accordion-icon-prefix' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'loop_icon_box_shadow',
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper .plus-accordion-header .accordion-icon-prefix',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_loop_icon_hover',
			[
				'label' => esc_html__( 'Active', 'theplus' ),
			]
		);
		$this->add_control(
			'loop_icon_hover_color',
			[
				'label' => esc_html__( 'Active Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .plus-accordion-header.active .accordion-icon-prefix' => 'color: {{VALUE}};-webkit-text-fill-color: {{VALUE}};',
					'{{WRAPPER}} .theplus-accordion-wrapper .plus-accordion-header.active .accordion-icon-prefix svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'loop_icon_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .theplus-accordion-wrapper .plus-accordion-header.active .accordion-icon-prefix',
			]
		);
		$this->add_responsive_control(
			'loop_icon_hover_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper .plus-accordion-header.active .accordion-icon-prefix' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'loop_icon_hover_box_shadow',
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper .plus-accordion-header.active .accordion-icon-prefix',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->end_controls_section();

		/*Highlight Background*/
		$this->start_controls_section(
            'highlight_background',
            [
                'label' => esc_html__('Text Highlight Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'search_text_highlight' => 'yes',
				]
			]
        );
		$this->add_group_control(
				Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper .highlight',
			]
	    );

		$this->start_controls_tabs(
			'style_tabs'
		);
		
		$this->start_controls_tab(
			'text_highlight_tab',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);

		$this->add_control(
			'highlight_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFF33',
				'condition' => [
					'search_text_highlight' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .highlight' => 'background-color: {{value}};',
				],
			]
		);
		$this->add_control(
			'highlight_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'condition' => [
					'search_text_highlight' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .highlight' => 'color: {{value}};',
				],
			]
		);

		
		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover_highlight_tab',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);

		$this->add_control(
			'hover_bg_color',
			[
				'label' => esc_html__( 'Highlight Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFF33',
				'condition' => [
					'search_text_highlight' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .highlight:hover' => 'background-color: {{value}};',
				],
				
				
			]
		);
		$this->add_control(
			'hover_text_color',
			[
				'label' => esc_html__( 'Highlight Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff0000',
				'condition' => [
					'search_text_highlight' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .highlight:hover' => 'color: {{value}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->end_controls_section();
        /*Highlight Background end*/
		
		/*Title style*/
		$this->start_controls_section(
            'section_accordion_styling',
            [
                'label' => esc_html__('Title Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
			]
        );
		$this->add_control(
			'accordion_title_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .theplus-accordion-wrapper.elementor-accordion .elementor-tab-title .elementor-accordion-icon.elementor-accordion-icon-right' => 'right: {{RIGHT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'accordion_space',
			[
				'label' => esc_html__( 'Accordion Between Space', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_control(
			'box_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		
		$this->add_control(
			'border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_border_style' );
		$this->start_controls_tab(
			'tab_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_border_active',
			[
				'label' => esc_html__( 'Active', 'theplus' ),
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'box_border_active_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header.active' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'border_active_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header.active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->start_controls_tabs( 'tabs_background_style' );
		$this->start_controls_tab(
			'tab_background_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'box_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header',
				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_background_active',
			[
				'label' => esc_html__( 'Active', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'box_active_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header.active',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_shadow_style' );
		$this->start_controls_tab(
			'tab_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_shadow_active',
			[
				'label' => esc_html__( 'Active', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_active_shadow',
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header.active',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'mbbf',
			[
				'label' => esc_html__( 'Backdrop Filter', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'mbbf_blur',
			[
				'label' => esc_html__( 'Blur', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 1,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'condition'    => [
					'mbbf' => 'yes',
				],
			]
		);
		$this->add_control(
			'mbbf_grayscale',
			[
				'label' => esc_html__( 'Grayscale', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-header' => '-webkit-backdrop-filter:grayscale({{mbbf_grayscale.SIZE}})  blur({{mbbf_blur.SIZE}}{{mbbf_blur.UNIT}}) !important;backdrop-filter:grayscale({{mbbf_grayscale.SIZE}})  blur({{mbbf_blur.SIZE}}{{mbbf_blur.UNIT}}) !important;',
				 ],
				'condition'    => [
					'mbbf' => 'yes',
				],
			]
		);
		$this->end_popover();
		$this->end_controls_section();
		/*Title style*/
		/*desc style*/
		$this->start_controls_section(
            'section_desc_styling',
            [
                'label' => esc_html__('Content', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
			]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-content .plus-content-editor',
			]
		);
		$this->add_control(
			'desc_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-content .plus-content-editor,{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-content .plus-content-editor p' => 'color: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_content_bg_styling',
            [
                'label' => esc_html__('Content Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
			]
        );
		$this->add_responsive_control(
			'content_accordion_margin',
			[
				'label' => esc_html__( 'Content Margin Space', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'content_accordion_padding',
			[
				'label' => esc_html__( 'Content Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'content_border_options',
			[
				'label' => esc_html__( 'Border Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'content_box_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'content_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-content' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'content_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'content_box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-content' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'content_box_border' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'content_box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-content' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'content_box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'content_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'content_box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'content_background_options',
			[
				'label' => esc_html__( 'Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'content_box_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-content',
				'separator' => 'after',
				
			]
		);
		$this->add_control(
			'content_shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_box_shadow',
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-content',
			]
		);
		$this->add_control(
			'cbf',
			[
				'label' => esc_html__( 'Backdrop Filter', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'cbf_blur',
			[
				'label' => esc_html__( 'Blur', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 1,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'condition'    => [
					'cbf' => 'yes',
				],
			]
		);
		$this->add_control(
			'cbf_grayscale',
			[
				'label' => esc_html__( 'Grayscale', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper .theplus-accordion-item .plus-accordion-content' => '-webkit-backdrop-filter:grayscale({{cbf_grayscale.SIZE}})  blur({{cbf_blur.SIZE}}{{cbf_blur.UNIT}}) !important;backdrop-filter:grayscale({{cbf_grayscale.SIZE}})  blur({{cbf_blur.SIZE}}{{cbf_blur.UNIT}}) !important;',
				 ],
				'condition'    => [
					'cbf' => 'yes',
				],
			]
		);
		$this->end_popover();
		$this->end_controls_section();
		/*desc style*/
		
		/*paly/pause*/
		$this->start_controls_section(
			'section_autoplay_buttton_style',
			[
				'label' => esc_html__( 'Autoplay Button', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'tabs_autoplay' => 'yes',
					'tabs_autoplaypause' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'autoplay_buttton_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%'],				
				'selectors' => [
					'{{WRAPPER}} .tp-tab-playpause-button .tp-tab-play-pause-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
            'autoplay_buttton_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 12,
				],		
				'render_type' => 'ui',				
				'selectors' => [
					'{{WRAPPER}} .tp-tab-playpause-button .tp-tab-play-pause-wrap .tp-tab-play-pause i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-tab-playpause-button .tp-tab-play-pause-wrap .tp-tab-play-pause svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->start_controls_tabs( 'autoplay_buttton_tabs' );
		$this->start_controls_tab(
			'autoplay_buttton_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'autoplay_buttton_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-tab-playpause-button .tp-tab-play-pause-wrap .tp-tab-play-pause i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-tab-playpause-button .tp-tab-play-pause-wrap .tp-tab-play-pause svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'autoplay_buttton_BG',
				'label' => esc_html__( 'Background Type', 'theplus' ),
			    'types' => [ 'classic', 'gradient' ],
			    'selector' => '{{WRAPPER}} .tp-tab-playpause-button .tp-tab-play-pause-wrap', 
			]
		);
		 $this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'autoplay_buttton_Border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-tab-playpause-button .tp-tab-play-pause-wrap',
				]
	    );
		$this->add_responsive_control(
			'autoplay_buttton_BRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-tab-playpause-button .tp-tab-play-pause-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'autoplay_buttton_Shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-tab-playpause-button .tp-tab-play-pause-wrap',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'autoplay_buttton_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'autoplay_buttton_color_h',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-tab-playpause-button .tp-tab-play-pause-wrap:hover .tp-tab-play-pause i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-tab-playpause-button .tp-tab-play-pause-wrap:hover .tp-tab-play-pause svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'autoplay_buttton_BG_h',
				'label' => esc_html__( 'Background Type', 'theplus' ),
			    'types' => [ 'classic', 'gradient' ],
			    'selector' => '{{WRAPPER}} .tp-tab-playpause-button .tp-tab-play-pause-wrap:hover', 
			]
		);
		 $this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'autoplay_buttton_Border_h',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-tab-playpause-button .tp-tab-play-pause-wrap:hover',
				]
	    );
		$this->add_responsive_control(
			'autoplay_buttton_BRadius_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-tab-playpause-button .tp-tab-play-pause-wrap:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'autoplay_buttton_Shadow_h',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-tab-playpause-button .tp-tab-play-pause-wrap:hover',
			]
		);
		$this->end_controls_tab();
	    $this->end_controls_tabs();
		$this->end_controls_section();
		/*paly/pause*/

		/*slider style*/
		$this->start_controls_section(
            'section_slider_acr_styling',
            [
                'label' => esc_html__('Slider/Pagination', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [
					'horizontal_accordion!' => 'yes',
					'slider_accordion' => 'yes',
				],
			]
        );
		$this->add_responsive_control(
			'sliacc_next_prev_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next,{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);
		$this->add_control(
			'sliacc_next_heading',
			[
				'label' => esc_html__( 'Next', 'theplus' ),
				'type' => Controls_Manager::HEADING,				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sliacc_next_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next',
			]
		);
		$this->add_responsive_control(
            'sliacc_next_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 250,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}  .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next i' => 'font-size:{{SIZE}}{{UNIT}}',
					'{{WRAPPER}}  .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next svg' => 'width: {{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'sliacc_next_icon_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],			
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}  .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next .tpas-icon' => 'margin-left:{{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_sliacc_next' );
		$this->start_controls_tab(
			'tab_sliacrn_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'sliacrn_n_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next' => 'color: {{VALUE}};',
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'sliacrn_n_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'sliacrn_n_brd',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next',
			]
		);
		$this->add_responsive_control(
			'sliacrn_n_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'sliacrn_n_shadow',
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_sliacrn_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'sliacrn_h_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'sliacrn_h_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'sliacrn_h_brd',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next:hover',
			]
		);
		$this->add_responsive_control(
			'sliacrn_h_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'sliacrn_h_shadow',
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-next:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		$this->add_control(
			'sliacc_previous_heading',
			[
				'label' => esc_html__( 'Previous', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sliacc_prev_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev',
			]
		);
		$this->add_responsive_control(
            'sliacc_prev_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 250,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}  .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev i' => 'font-size:{{SIZE}}{{UNIT}}',
					'{{WRAPPER}}  .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev svg' => 'width: {{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'sliacc_p_icon_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],			
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}  .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev .tpas-icon' => 'margin-right:{{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_sliacc_p_next' );
		$this->start_controls_tab(
			'tab_sliacrn_n_p',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'sliacrn_n_color_p',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev' => 'color: {{VALUE}};',
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'sliacrn_n_bg_p',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'sliacrn_n_brd_p',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev',
			]
		);
		$this->add_responsive_control(
			'sliacrn_n_br_p',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'sliacrn_n_shadow_p',
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_sliacrn_h_p',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'sliacrn_h_color_p',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'sliacrn_h_bg_p',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'sliacrn_h_brd_p',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev:hover',
			]
		);
		$this->add_responsive_control(
			'sliacrn_h_br_p',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'sliacrn_h_shadow_p',
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-prev:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'sliacc_currentslide_heading',
			[
				'label' => esc_html__( 'Current Slide', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'sliacc_cs_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-active-slide' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sliacc_cs_typo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-active-slide',
			]
		);
		$this->add_control(
			'sliacc_cs_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-active-slide' => 'color: {{VALUE}};',
				],				
			]
		);
		$this->add_control(
			'sliacc_totalslide_heading',
			[
				'label' => esc_html__( 'Total Slide', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'sliacc_ts_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-total-slide' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sliacc_ts_typo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-total-slide',
			]
		);
		$this->add_control(
			'sliacc_ts_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-total-slide' => 'color: {{VALUE}};',
				],				
			]
		);
		$this->add_control(
			'sliacc_seprator_heading',
			[
				'label' => esc_html__( 'Seprator', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'sliacc_seprator_f',
			[
				'label' => esc_html__( 'Seprator', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '/',
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);
		$this->add_responsive_control(
			'sliacc_sep_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-sep-slide' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);
		$this->add_responsive_control(
			'sliacc_sep_size',
			[
				'label' => esc_html__( 'Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-sep-slide' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'sliacc_sep_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper.tp-accr-slider .tp-aec-slide-page .tpasp-sep-slide' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*slider style*/
		
		/*search*/
		$this->start_controls_section(
            'section_seach_styling',
            [
                'label' => esc_html__('Search Accordion', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'search_accordion' => 'yes',
				],
			]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sa_input_typography',
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tpacsearchinput',
			]
		);
		$this->add_responsive_control(
			'sa_input_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tp-acr-search-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tp-acr-search-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'sa_input_icon_offset',
			[
				'label' => esc_html__( 'Icon Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tp-acr-search-icon' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'sa_input_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tpacsearchinput::-webkit-input-placeholder' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'sa_input_inner_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tpacsearchinput' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'sa_input_inner_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'tabs_sa_input_field_style' );
		$this->start_controls_tab(
			'tab_sa_input_field_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'sa_input_field_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tpacsearchinput' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'sa_input_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tp-acr-search-icon' => 'color: {{VALUE}};',					
					'{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tp-acr-search-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'sa_input_field_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tpacsearchinput',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'sa_input__border_n',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tpacsearchinput',
			]
		);
		$this->add_responsive_control(
			'sa_input_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tpacsearchinput' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'sa_input_shadow_n',
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tpacsearchinput',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_sa_input_field_focus',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_control(
			'sa_input_field_focus_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tpacsearchinput:focus' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'sa_input_icon_color_h',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container:hover .tp-acr-search-icon' => 'color: {{VALUE}};',					
					'{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container:hover .tp-acr-search-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'sa_input_field_focus_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tpacsearchinput:focus',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'sa_input__border_f',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tpacsearchinput:focus',
			]
		);
		$this->add_responsive_control(
			'sa_input_border_radius_f',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tpacsearchinput:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'sa_input_shadow_f',
				'selector' => '{{WRAPPER}} .theplus-accordion-wrapper #accordion_search_bar_container .tpacsearchinput:focus',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*search*/
		
		/*Expand/Collapse style*/
		$this->start_controls_section(
            'section_eca_styling',
            [
                'label' => esc_html__('Expand/Collapse Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'expand_collapse_accordion' => 'yes',
				],
			]
        );
		$this->add_responsive_control(
			'eca_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-aec-button .tp-toggle-accordion' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'eca_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-aec-button .tp-toggle-accordion' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eca_typography',
				'selector' => '{{WRAPPER}} .tp-aec-button .tp-toggle-accordion',
			]
		);
		$this->start_controls_tabs( 'tabs_eca_main' );
		$this->start_controls_tab(
			'tab_eca_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'tab_eca_color_n',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-aec-button .tp-toggle-accordion' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tab_eca_background_n',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-aec-button .tp-toggle-accordion',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_eca_border_n',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-aec-button .tp-toggle-accordion',
			]
		);
		$this->add_responsive_control(
			'tab_eca_br_n',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-aec-button .tp-toggle-accordion' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tab_eca_shadow_n',
				'selector' => '{{WRAPPER}} .tp-aec-button .tp-toggle-accordion',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_eca_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'tab_eca_color_h',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-aec-button .tp-toggle-accordion:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tab_eca_background_h',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-aec-button .tp-toggle-accordion:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_eca_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-aec-button .tp-toggle-accordion:hover',
			]
		);
		$this->add_responsive_control(
			'tab_eca_br_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-aec-button .tp-toggle-accordion:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tab_eca_shadow_h',
				'selector' => '{{WRAPPER}} .tp-aec-button .tp-toggle-accordion:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Expand/Collapse style*/
		
		/*Hover Animation style*/
		$this->start_controls_section(
            'section_hover_styling',
            [
                'label' => esc_html__('Hover Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
			]
        );
		$this->add_control(
			'hover_style',
			[
				'label'   => esc_html__( 'Hover Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'hover-style-1' => esc_html__( 'Style 1', 'theplus' ),
					'hover-style-2' => esc_html__( 'Style 2', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#8072fc',
				'selectors'  => [
					'{{WRAPPER}} .theplus-accordion-wrapper.hover-style-1 .plus-accordion-header:before,{{WRAPPER}} .theplus-accordion-wrapper.hover-style-2 .plus-accordion-header:before' => 'background: {{VALUE}};',
				],
				'condition' => [
					'hover_style' => ['hover-style-1','hover-style-2']
				],
			]
		);
		$this->end_controls_section();
		/*Adv tab*/
		$this->start_controls_section(
            'section_plus_extra_adv',
            [
                'label' => esc_html__('Plus Extras', 'theplus'),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );
		$this->end_controls_section();
		/*Adv tab*/

		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';
		include THEPLUS_PATH. 'modules/widgets/theplus-needhelp.php';
	}

    protected function render() {
		$settings = $this->get_settings_for_display();
		$templates = Theplus_Element_Load::elementor()->templates_manager->get_source( 'local' )->get_items();
		
		$title_align=$settings["title_align"];
		$id_int = substr( $this->get_id_int(), 0, 3 );
		$on_hover_accordion= ($settings['on_hover_accordion']=='yes') ? 'hover' : 'accordion';
		$uid=uniqid("accordion");
		
		$search_accordion = isset($settings['search_accordion']) ? $settings['search_accordion'] : '';
		
		$search_text_highlight = !empty($settings['search_text_highlight']) ? true : false;
       
		$accordiannew='';
		if( !empty($search_text_highlight) ){
			$highlight_data = array( 'search_text_highlight' => (bool) $search_text_highlight);
			$accordiannew = 'data-accordiannew="' . htmlspecialchars( wp_json_encode( $highlight_data, true ), ENT_QUOTES, 'UTF-8' ) . '"';
		}

		$search_accordion_length = !empty($settings['search_accordion_length']) ? $settings['search_accordion_length'] : 3;
		
		$accordion_stager = isset($settings['accordion_stager']) ? $settings['accordion_stager'] : '';
		$accordion_stager_visi_delay = !empty($settings['accordion_stager_visi_delay']) ? $settings['accordion_stager_visi_delay'] : 500;
		$accordion_stager_gap = !empty($settings['accordion_stager_gap']) ? $settings['accordion_stager_gap'] : 500;
		
		$accordion_scroll_top = isset($settings['accordion_scroll_top']) ? $settings['accordion_scroll_top'] : '';
		
		$act_speed = !empty($settings['act_speed']) ? $settings['act_speed'] : 500;
		$act_offset = !empty($settings['act_offset']) ? $settings['act_offset'] : 0;
		
		$search_accordion_placeholder = !empty($settings['search_accordion_placeholder']) ? $settings['search_accordion_placeholder'] : '';
		
		$slider_accordion = isset($settings['slider_accordion']) ? $settings['slider_accordion'] : '';
		$slider_accordion_show = !empty($settings['slider_accordion_show']) ? $settings['slider_accordion_show'] : 3;
		
		$markupSch = isset($settings['schema_accordion']) ? $settings['schema_accordion'] : false;		
		$mainschema = $schemaAttr = $schemaAttr1 = $schemaAttr2 = $schemaAttr3 = '';

		if(isset($markupSch) && $markupSch=='yes') {
			$mainschema = 'itemscope itemtype="https://schema.org/FAQPage"';
			$schemaAttr = 'itemscope itemprop="mainEntity" itemtype="https://schema.org/Question"';
			$schemaAttr1 = 'itemprop="name"';
			$schemaAttr2 = 'itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer"';
			$schemaAttr3 = 'itemprop="text"';
		}

		$connect_carousel =$row_bg_conn='';
		if(!empty($settings["connection_unique_id"])){
			$connect_carousel="tpca_".$settings["connection_unique_id"];
			$uid="tptab_".$settings["connection_unique_id"];
			$row_bg_conn = ' data-row-bg-conn="bgcarousel'.esc_attr($settings["connection_unique_id"]).'"';
		}
		
		$sattr=$sattrclasss=$searchicon='';
		if($search_accordion==='yes' && !empty($search_accordion_length)){
			$sattr .=' data-search-accr="'.$search_accordion.'"';
			$sattr .=' data-search-attr-len="'.$search_accordion_length.'"';
			$sattrclasss .= ' tp-seachaccr';
			
			if(!empty($settings['search_accordion_icon'])){
				ob_start();
				\Elementor\Icons_Manager::render_icon( $settings['search_accordion_icon'], [ 'aria-hidden' => 'true' ]);
				$searchicon = ob_get_contents();
				ob_end_clean();
			}
		}
		
		if($accordion_stager==='yes' && !empty($accordion_stager_visi_delay) && !empty($accordion_stager_gap)){
			$sattr .=' data-stager-vd-accr="'.$accordion_stager_visi_delay.'"';
			$sattr .=' data-stager-gap-accr="'.$accordion_stager_gap.'"';
			$sattrclasss .= ' tp-stageraccr';
		}
		
		if($accordion_scroll_top==='yes' && !empty($act_speed)){
			$sattrclasss .= ' tp-scrolltopacc';
			$sattr .=' data-scroll-top-accr="'.$accordion_scroll_top.'"';
			$sattr .=' data-scroll-top-speed-accr="'.$act_speed.'"';
			$sattr .=' data-scroll-top-offset-accr="'.$act_offset.'"';
		}
		
		if($slider_accordion==='yes' && !empty($slider_accordion_show)){
			$sattrclasss .= ' tp-accr-slider';
		}
		
		$horizontal_accordion = isset($settings['horizontal_accordion']) ? $settings['horizontal_accordion'] : 'no';
			if(isset($horizontal_accordion) && $horizontal_accordion==='yes' && $on_hover_accordion!='yes'){
				$ha_title_width = !empty($settings['horizontal_accordion_title_width']) ? $settings['horizontal_accordion_title_width'] : '';
				$ha_open_speed = !empty($settings['horizontal_accordion_open_speed']) ? $settings['horizontal_accordion_open_speed'] : 400;
				$ha_close_speed = !empty($settings['horizontal_accordion_close_speed']) ? $settings['horizontal_accordion_close_speed'] : 400;
				$horizontal_accordion_layout = !empty($settings['horizontal_accordion_layout']) ? $settings['horizontal_accordion_layout'] : '';
		
				$sattrclasss .= ' tp-acc-hori '.esc_attr($horizontal_accordion_layout);
				$sattr .=' data-hori-title-width="'.esc_attr($ha_title_width).'"';
				$sattr .=' data-hori-open-speed="'.esc_attr($ha_open_speed).'"';
				$sattr .=' data-hori-close-speed="'.esc_attr($ha_close_speed).'"';
			}			
			
			
		if(isset($settings['tabs_autoplay']) && $settings['tabs_autoplay']=='yes'){
			$tabs_autoplay_duration = !empty($settings['tabs_autoplay_duration']) ? $settings['tabs_autoplay_duration'] : 5;
			$sattrclasss .= ' tp-tab-playloop';
			
			if(isset($settings['tabs_autoplaypause']) && $settings['tabs_autoplaypause']=='yes'){
				$sattrclasss .= ' tp-tab-playpause-button';
			}
			
			$sattr .= ' data-tab-autoplay="yes"';
			$sattr .= ' data-tab-autoplay-duration="'.esc_attr($tabs_autoplay_duration).'"';
		}
		
		/*--Plus Extra ---*/
		$PlusExtra_Class = "plus-accordion-widget";
		include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';

		/*--OnScroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';
		
		echo $before_content;
		?>
		<div class="theplus-accordion-wrapper elementor-accordion <?php echo esc_attr($sattrclasss); ?> <?php echo esc_attr($settings['hover_style']); ?> <?php echo esc_attr($animated_class); ?>" id="<?php echo esc_attr($uid); ?>" data-accordion-id="<?php echo esc_attr($uid); ?>" data-connection="<?php echo esc_attr($connect_carousel); ?>" data-accordion-type="<?php echo esc_attr($on_hover_accordion); ?>" data-heightlight-text<?php echo $animation_attr; ?> <?php echo $row_bg_conn; ?> <?php echo $accordiannew; ?> <?php echo $sattr; ?> role="tablist" <?php echo $mainschema; ?>> 
		
		<?php
			if($search_accordion==='yes' && !empty($search_accordion_length)){ ?>
				<div id="accordion_search_bar_container"> <?php
					if(!empty($searchicon)){
						echo '<span class="tp-acr-search-icon">'.$searchicon.'</span>';
					}
					?>
					
					<input type="search" id="tpsb<?php echo esc_attr($uid); ?>" class="tpacsearchinput" placeholder="<?php echo esc_html($search_accordion_placeholder); ?>" value="" onkeyup="this.setAttribute('value', this.value);">                
				</div> <?php
			}
			
			$expand_collapse_accordion = isset($settings['expand_collapse_accordion']) ? $settings['expand_collapse_accordion'] : 'no';
			
			$saip=$satp=$sain=$satn='';
			$slider_accordion = isset($settings['slider_accordion']) ? $settings['slider_accordion'] : 'no';
			$slider_accordion_align = !empty($settings['slider_accordion_align']) ? $settings['slider_accordion_align'] : '';
			if($slider_accordion==='yes'){
				if(!empty($settings['slider_accordion_icon_prev'])){
					ob_start();
					\Elementor\Icons_Manager::render_icon( $settings['slider_accordion_icon_prev'], [ 'aria-hidden' => 'true' ]);
					$saip = '<span class="tpas-icon">'.ob_get_contents().'</span>';
					ob_end_clean();					
				}
				
				if(!empty($settings['slider_accordion_text_prev'])){
					$satp='<span class="tpas-text">'.$settings['slider_accordion_text_prev'].'</span>';
				}
				
				if(!empty($settings['slider_accordion_icon_nxt'])){
					ob_start();
					\Elementor\Icons_Manager::render_icon( $settings['slider_accordion_icon_nxt'], [ 'aria-hidden' => 'true' ]);
					$sain = '<span class="tpas-icon">'.ob_get_contents().'</span>';
					ob_end_clean();
				}
				
				if(!empty($settings['slider_accordion_text_nxt'])){
					$satn='<span class="tpas-text">'.$settings['slider_accordion_text_nxt'].'</span>';
				}
			}
			
			
			$eca_position = !empty($settings['eca_position']) ? $settings['eca_position'] : 'ecabefore';
			if($expand_collapse_accordion==='yes' && $eca_position==='ecabefore'){ ?>
				<div class="tp-aec-button"><a href="javascript:void(0)" class="tp-toggle-accordion active"></a></div> <?php
			}
			
			$ij=1;
			$mj=1;
			$totalloop = 0;
			foreach ( $settings['tabs'] as $index => $item ){
				$totalloop++;
			}

			foreach ( $settings['tabs'] as $index => $item ) :
				$tab_count = $index + 1;
				if($settings["active_accordion"]==$tab_count || $settings["active_accordion"]=='all-open'){
					$active_default='active-default';
				}else if($settings["active_accordion"]==0){
					$active_default='0';
				}else{
					$active_default='no';
				}
				
				if(!empty($item['tab_hashid'])){
					$tab_title_id = trim( $item['tab_hashid'] );
					$tab_content_id = 'tab-content-'.trim( $item['tab_hashid'] );
				}else{
					$tab_title_id = 'elementor-tab-title-' . $id_int . $tab_count;
					$tab_content_id = 'elementor-tab-content-' . $id_int . $tab_count;
				}
				$tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );

				$tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );
				
				$lz2 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['box_background_image'],$settings['box_active_background_image']) : '';
				
				$this->add_render_attribute( $tab_title_setting_key, [
					'id' => $tab_title_id,
					'class' => [ 'elementor-tab-title', 'plus-accordion-header', $active_default, $title_align, $lz2 ],
					'data-tab' => $tab_count,
					'aria-controls' => $tab_content_id,
					// 'role' => 'tab',
					// 'tabindex' => $id_int . $tab_count,
				] );

				$lz3 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['content_box_background_image']) : '';
				$this->add_render_attribute( $tab_content_setting_key, [
					'id' => $tab_content_id,
					'class' => [ 'elementor-tab-content', 'elementor-clearfix', 'plus-accordion-content', $active_default , $lz3],
					'data-tab' => $tab_count,
					'role' => 'tabpanel',
					'aria-labelledby' => $tab_title_id,
				] );

				$this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );
				
				$accordion_toggle_icon='';
				
				$accslideactive= '';				
				if(isset($slider_accordion) && $slider_accordion=='yes' && !empty($slider_accordion_show)){
					if($tab_count%$slider_accordion_show==1){
						$sliderclass ='';
						if($ij===1){
							$sliderclass = ' tpaccractive';
						}
					?>
						<div class="tp-accr-list-slider <?php echo $sliderclass; ?>" data-tabslide="<?php echo $mj; ?>"> <?php
						$ij++;
						$mj++;
					}
				}
				
				if(!empty($item['tab_title'])){
				?>
				<div class="theplus-accordion-item" <?php echo $schemaAttr; ?> role="tab">
					<<?php echo theplus_validate_html_tag($settings['title_html_tag']); ?> <?php echo $this->get_render_attribute_string( $tab_title_setting_key ); echo $schemaAttr1; ?>>
						<?php if ( $settings['display_icon']=='yes' ) : ?>
							<?php 
								if($settings['icon_style']=='font_awesome'){
									$icons=$settings['icon_fontawesome'];
									$icons_active=$settings['icon_fontawesome_active'];
								}else if($settings['icon_style']=='icon_mind'){
									$icons=$settings['icons_mind'];
									$icons_active=$settings['icons_mind_active'];
								}else if(!empty($settings['icon_style']) && $settings['icon_style']=='font_awesome_5'){
									ob_start();
									\Elementor\Icons_Manager::render_icon( $settings['icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
									$icons = ob_get_contents();
									ob_end_clean();

									ob_start();
									\Elementor\Icons_Manager::render_icon( $settings['icon_fontawesome_5_active'], [ 'aria-hidden' => 'true' ]);
									$icons_active = ob_get_contents();
									ob_end_clean();
								}else{
									$icons=$icons_active='';
								}
							?>
							<?php if(!empty($icons) && !empty($icons_active)){ 
								$accordion_toggle_icon='<span class="elementor-accordion-icon elementor-accordion-icon-'.esc_attr( $settings['icon_align'] ).'" aria-hidden="true">';									
									if(!empty($settings['icon_style']) && $settings['icon_style']=='font_awesome_5'){										
										$accordion_toggle_icon .='<span class="elementor-accordion-icon-closed">'.$icons.'</span>';
										$accordion_toggle_icon .='<span class="elementor-accordion-icon-opened">'.$icons_active.'</span>';
									}else{
										$accordion_toggle_icon .='<i class="elementor-accordion-icon-closed '.esc_attr( $icons ).'"></i>';
										$accordion_toggle_icon .='<i class="elementor-accordion-icon-opened '.esc_attr( $icons_active ).'"></i>';
									}
									
								$accordion_toggle_icon .='</span>';
							} ?>
						<?php endif; ?>
						<?php if(!empty($settings['icon_align']) && $settings['icon_align']=='left'){
							echo $accordion_toggle_icon;
						} ?>
						<?php
							if ( !empty($item['display_icon']) && $item['display_icon']=='yes' ) : 								
								if($item['icon_style']=='font_awesome'){
									$icons_loop=$item['icon_fontawesome'];
								}else if($item['icon_style']=='icon_mind'){
									$icons_loop=$item['icons_mind'];								
								}else if($item['icon_style']=='font_awesome_5'){					
									ob_start();
									\Elementor\Icons_Manager::render_icon( $item['icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
									$icons_loop = ob_get_contents();
									ob_end_clean();
								}else{									
									$icons_loop='';
								}
								if(!empty($icons_loop) && !empty($icons_loop)){
									$lz1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['loop_icon_background_image'],$settings['loop_icon_hover_background_image']) : '';
									
									if(!empty($item['icon_style']) && $item['icon_style']=='font_awesome_5'){ ?>								
											<span class="accordion-icon-prefix <?php echo esc_attr($lz1); ?>"><span class="plus-icon-accordion"><?php echo $icons_loop; ?></span></span> <?php
										}else{
								?>
									<span class="accordion-icon-prefix <?php echo esc_attr($lz1); ?>"><i class="plus-icon-accordion <?php echo esc_attr( $icons_loop ); ?>"></i></span> 
									<?php }
										} endif; ?>
						
						 <?php echo '<span style="width:100%">'.$item['tab_title'].'</span>'; ?>
						<?php if(!empty($settings['icon_align']) && $settings['icon_align']=='right'){
							echo $accordion_toggle_icon;
						} ?>
					</<?php echo theplus_validate_html_tag($settings['title_html_tag']); ?>>
					
					<?php if(($item['content_source']=='content' && !empty($item['tab_content'])) || ($item["content_source"]=='page_template' && !empty($item['content_template']))){ ?>
						<div <?php echo $this->get_render_attribute_string( $tab_content_setting_key );  echo $schemaAttr2 ?>>
							<?php if($item['content_source']=='content' && !empty($item['tab_content'])){
								echo '<div class="plus-content-editor" '.$schemaAttr3.'>'.$this->parse_text_editor( $item['tab_content'] ).'</div>';
							}
							if(\Elementor\Plugin::$instance->editor->is_edit_mode() && $item["content_source"]=='page_template' && !empty($item['content_template'])){
								if(!empty($item["backend_preview_template"]) && $item["backend_preview_template"]=='yes'){
									echo '<div class="plus-content-editor" '.$schemaAttr3.'>'.Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display( $item['content_template'] ).'</div>';
								}else{
									$get_template_name='';
									$get_template_id=$item['content_template'];
									if(!empty($templates) && !empty($get_template_id)){
										foreach($templates as $value){
											if($value["template_id"]==$get_template_id){
												$get_template_name=$value["title"];
											}
										}
									}
									echo '<div class="tab-preview-template-notice"><div class="preview-temp-notice-heading">Selected Template : <b>"'.esc_attr($get_template_name).'"</b></div><div class="preview-temp-notice-desc"><b>Note :</b> We have turn off visibility of template in the backend due to performance improvements. This will be visible perfectly on the frontend.</div></div>';
								}
							}else if($item["content_source"]=='page_template' && !empty($item['content_template'])){
								echo '<div class="plus-content-editor" '.$schemaAttr3.'>'.Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display( $item['content_template'] ).'</div>';
							}
							?>
													
						</div> 
					<?php } ?>					
				</div>
				<?php
				}
				if(isset($slider_accordion) && $slider_accordion=='yes' && !empty($slider_accordion_show)){
						
					if( $tab_count % $slider_accordion_show == 0 || $tab_count == $totalloop) { ?>
						</div> <?php
					}
					
				}
			
			endforeach; 

			if($expand_collapse_accordion==='yes' && $eca_position==='ecaafter'){ ?>
				<div class="tp-aec-button"><a href="javascript:void(0)" class="tp-toggle-accordion active"></a></div> <?php
			}

			/*playpausebutton*/
			if(isset($settings['tabs_autoplay']) && $settings['tabs_autoplay']=='yes' && isset($settings['tabs_autoplaypause']) && $settings['tabs_autoplaypause']=='yes'){
				$iconsPlay=$iconsPause='';
				if(!empty($settings['autopauseicon'])){
					ob_start();
					\Elementor\Icons_Manager::render_icon( $settings['autopauseicon'], [ 'aria-hidden' => 'true' ]);
					$iconsPlay = ob_get_contents();
					ob_end_clean();
				}
				if(!empty($settings['autoplayicon'])){
					ob_start();
					\Elementor\Icons_Manager::render_icon( $settings['autoplayicon'], [ 'aria-hidden' => 'true' ]);
					$iconsPause = ob_get_contents();
					ob_end_clean();
				} ?>
				
				<div class="tp-tab-play-pause-wrap"><div class="tp-tab-play-pause tpplay active"><?php echo $iconsPlay; ?></div><div class="tp-tab-play-pause tppause"><?php echo $iconsPause; ?></div></div>
				<?php
			}
			if($slider_accordion==='yes' && !empty($slider_accordion_show)){
				if($tab_count > $slider_accordion_show){
				$sliacc_seprator_f = !empty($settings['sliacc_seprator_f']) ? $settings['sliacc_seprator_f'] : '';
			?>
				<div class="tp-aec-slide-page <?php echo $slider_accordion_align; ?>">
					<span class="tpasp-prev tpas-disabled"><?php echo $saip.$satp; ?></span>
					<span class="tpasp-active-slide">1</span> <?php
					if(!empty($sliacc_seprator_f)){ ?>
						<span class="tpasp-sep-slide"><?php echo $sliacc_seprator_f; ?></span>	
					<?php 
					} ?>
					<span class="tpasp-total-slide">1</span>
					<span class="tpasp-next"><?php echo $satn.$sain; ?></span>
				</div>
			<?php
				}
			}
			?>
		</div> <?php
				
		echo $after_content;
	}

	protected function content_template() {
	
	}
}