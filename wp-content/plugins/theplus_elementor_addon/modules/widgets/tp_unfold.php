<?php 
/*
Widget Name: Unfold
Description: Content of text with unfold.
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

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Unfold extends Widget_Base {

	public $TpDoc = THEPLUS_TPDOC;
		
	public function get_name() {
		return 'tp-unfold';
	}

    public function get_title() {
        return esc_html__('Unfold', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-folder-open theplus_backend_icon';
    }

	public function get_custom_help_url() {
		$DocUrl = $this->TpDoc . "unfold";

		return esc_url($DocUrl);
	}

    public function get_categories() {
        return array('plus-essential');
    }

    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Unfold', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'content_title',
			[
				'label' => wp_kses_post( "Title <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "unfold-elementor-widget-settings-overview/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'Lorem ipsum dolor', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Title', 'theplus' ),				
			]
		);
		$this->add_control(
			'content_a_source',
			[
				'label' => wp_kses_post( "Select Source <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "add-read-more-toggle-button-to-text-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'content',
				'options' => [
					'content'  => esc_html__( 'Custom Content', 'theplus' ),
					'template' => esc_html__( 'Template', 'theplus' ),
					'innersectionbased' => esc_html__( 'Inner Section Based', 'theplus' ),
					'containerbased' => esc_html__( 'Container Based', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'containerbasednote',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => wp_kses_post( "Note : Put this widget in below or above Container of Target Container. <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "collapsible-elementor-flexbox-container/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'content_a_source' => 'containerbased',
				],	
			]
		);
		$this->add_control(
			'innersectionbasednote',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'label' => wp_kses_post( "<a class='tp-docs-link' href='" . esc_url($this->TpDoc) . 
				"toggle-an-elementor-inner-section/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> Note : Put this widget in below or above inner section of Target Section.  <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'content_a_source' => 'innersectionbased',
				],	
			]
		);

		$this->add_control(
			'content_description',
			[
				'label' => wp_kses_post( "Content <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "add-read-more-toggle-button-to-text-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which  look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there is anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source.', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Initial Text', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
				'condition'    => [
					'content_a_source' => [ 'content' ],
				],
			]
		);
		$this->add_control(
			'content_a_template',
			[
				'label' => wp_kses_post( "Elementor Templates<a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-templates-in-unfold-widget/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => theplus_get_templates(),
				'label_block' => 'true',
				'condition'   => ['content_a_source' => "template"],
			]
		);
		$this->add_control(
			'icon_type',
			[
				'label' => esc_html__( 'Icon Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => [
					'icon'  => esc_html__( 'Icon', 'theplus' ),
					'lottie' => esc_html__( 'Lottie', 'theplus' ),
				],
				'separator' => 'before',
			]
		);		
		$this->add_control(
			'content_readmore',
			[
				'label' => esc_html__( 'Expand Button Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'separator' => 'before',
				'default' => esc_html__( 'Read More', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Read More', 'theplus' ),				
			]
		);
		$this->add_control(
			'icon_position',
			[
				'label' => esc_html__( 'Icon Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'tp_ic_pos_before',
				'options' => [
					'tp_ic_pos_before'  => esc_html__( 'Before', 'theplus' ),
					'tp_ic_pos_after' => esc_html__( 'After', 'theplus' ),
				],
				'condition'   => [
					'icon_type' => ['icon','lottie']
				],
			]
		);
		$this->add_control(
			'lottieUrl',
			[
				'label' => esc_html__( 'Lottie URL', 'theplus' ),
				'type' => Controls_Manager::URL,				
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'condition' => ['icon_type' => 'lottie'],
			]
		);
		$this->add_control(
			'content_readmore_icon',
			[
				'label' => esc_html__( 'Expand Button Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-book-open',
					'library' => 'solid',
				],
				'condition'   => ['icon_type' => "icon"],
			]
		);
		$this->add_control(
			'content_readless',
			[
				'label' => esc_html__( 'Collapse Button Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'separator' => 'before',
				'default' => esc_html__( 'Read Less', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Read Less', 'theplus' ),				
			]
		);
		$this->add_control(
			'content_readless_icon',
			[
				'label' => esc_html__( 'Collapse Button Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-book',
					'library' => 'solid',
				],
				'condition'   => ['icon_type' => "icon"],
			]
		);
		$this->add_control(
            'extra_link',
            [
				'label'   => esc_html__( 'Extra Button', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'eb_text',
			[
				'label' 		=> esc_html__( 'Text', 'theplus' ),
				'type' 			=> Controls_Manager::TEXT,
				'dynamic' 		=> [ 'active' => true ],
				'default' 		=> esc_html__( 'Read More...', 'theplus' ),
				'label_block' 	=> true,
				'condition'		=> [
					'extra_link' => 'yes',
				],
			]
		);	
		$this->add_control(
			'eb_link',
			[
				'label' => __( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'theplus' ),
				'show_external' => true,
				'dynamic' => [ 
					'active' => true
				],
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
				'condition'		=> [
					'extra_link' => 'yes',
				],
			]
		);
		$this->add_control(
			'content_eb_icon',
			[
				'label' => esc_html__( 'Extra Button Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-book',
					'library' => 'solid',
				],
				'condition' => [
					'extra_link' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		
		$this->start_controls_section(
			'content_option_section',
			[
				'label' => esc_html__( 'Content Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'con_pos',
			[
				'label' => wp_kses_post( "Content Expand Direction <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "add-read-more-toggle-button-to-text-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Above Button', 'theplus' ),
					'after_button' => esc_html__( 'Below Button', 'theplus' ),
				],
			]
		);
		$this->add_responsive_control(
            'content_max_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Max Height', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-description' => 'height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'opacity_opt',
			[
				'label' => esc_html__( 'Content Overlay Options', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'co_custom',
			[
				'label' => esc_html__( 'Custom Opacity', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Enable', 'theplus' ),
				'label_off' => __( 'Disable', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper:not(.fullview) .tp-unfold-description::after' => 'top: auto;',
				],				
			]
		);
		$this->add_responsive_control(
            'co_c_min_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Opacity Height', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 80,
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper:not(.fullview) .tp-unfold-description::after' => 'min-height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [					
					'co_custom' => 'yes',
				],
            ]
        );
		$this->add_control(
			'co_c_opacity_color',
			[
				'label' => esc_html__( 'Opacity Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper:not(.fullview) .tp-unfold-description::after' => 'background: linear-gradient(rgba(255,255,255,0),{{VALUE}});',
				],
				'condition' => [
					'co_custom' => 'yes',
				],
			]
		);
		$this->add_control(
			'transition_duration',
			[
				'label' => esc_html__( 'Transition Duration (0 - 5000)', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 5000,
				'step' => 100,
				'default' => 200,
				'separator' => 'before',
			]
		);		
		$this->add_responsive_control(
			'content_align',
			[
				'label' => esc_html__( 'Toggle Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'space-around' => [
						'title' => esc_html__( 'Space Around', 'theplus' ),
						'icon' => 'eicon-text-align-justify',
					],
					'flex-start' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],					
					'space-between' => [
						'title' => esc_html__( 'Space Between', 'theplus' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => 'flex-start',
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle' => 'justify-content: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);	
		$this->add_control('unfold_scroll_top',
			[
				'label' => esc_html__( 'Scroll Top', 'theplus' ),
				'type' =>  Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'description' => 'Note : If enabled, When you click on accordion, It will scroll to title automatically.',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		
		/*style start*/
		/*title start*/
		$this->start_controls_section(
            'section_title_styling',
            [
                'label' => esc_html__('Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'title_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',				
			]
		);
		$this->add_control(
			'uf_title_tag',
			[
				'label' => esc_html__( 'Title Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'div',
				'options' => theplus_get_tags_options(),				
			]
		);
		$this->add_responsive_control(
			'title_align',
			[
				'label' => esc_html__( 'Title Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [					
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],	
				],
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-title' => 'text-align: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);	
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-title',
			]
		);
		$this->add_control(
			'title_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*title end*/
		
		/*description start*/
		$this->start_controls_section(
            'section_description_styling',
            [
                'label' => esc_html__('Description', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'description_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-description,{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-description p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',				
			]
		);
		$this->add_responsive_control(
			'description_align',
			[
				'label' => esc_html__( 'Description Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [					
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justify', 'theplus' ),
						'icon' => 'eicon-text-align-justify',
					],					
				],
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-description,{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-description p' => 'text-align: {{VALUE}}',
				],
				'condition' => [					
					'content_a_source' => 'content',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'selector' => 
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-description,{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-description p',
				'condition' => [					
					'content_a_source' => 'content',
				],
			]
		);
		$this->add_control(
			'description_color',
			[
				'label' => esc_html__( 'Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-description,{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-description p' => 'color: {{VALUE}};',
				],
				'condition' => [					
					'content_a_source' => 'content',
				],
			]
		);
		$this->end_controls_section();
		/*description end*/
		
		/*button start*/
		$this->start_controls_section(
            'section_tb_styling',
            [
                'label' => esc_html__('Toggle Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'tb_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle,
					{{WRAPPER}} .tp-unfold-wrapper.fullview .tp-unfold-last-toggle .tp-unfold-toggle-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'tb_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle,
					{{WRAPPER}} .tp-unfold-wrapper.fullview .tp-unfold-last-toggle .tp-unfold-toggle-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tb_typography',
				'selector' => '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle',
				'separator' => 'before',
			]
		);		
		$this->start_controls_tabs( 'tabs_tb' );
		$this->start_controls_tab(
			'tab_tb_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
            'tb_color',
            [
                'label' => esc_html__('Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle' => 'color:{{VALUE}};',
                ],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'tb_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tb_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'tb_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tb_shadow',
				'selector' => '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle',				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_tb_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
            'tb_color_h',
            [
                'label' => esc_html__('Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle:hover' => 'color:{{VALUE}};',
                ],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'tb_background_h',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tb_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle:hover',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'tb_br_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tb_shadow_h',
				'selector' => '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle:hover',				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		/*toggle icon start*/
		$this->add_control(
			'tb_icn_heading',
			[
				'label' => esc_html__( 'Toggle Icon Options', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'tb_icn_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',				
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'tb_icn_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],				
				'render_type' => 'ui',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle i,
					{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle svg' => 'margin-right: {{SIZE}}{{UNIT}}',
				],
				'condition'    => [
					'icon_position' => [ 'tp_ic_pos_before' ],
				],
            ]
        );
		$this->add_responsive_control(
            'tb_icn_space_left',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],				
				'render_type' => 'ui',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle i,
					{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle svg' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
				'condition'    => [
					'icon_position' => [ 'tp_ic_pos_after' ],
				],
            ]
        );
		$this->add_control(
			'tb_icn_color_n',
			[
				'label' => esc_html__( 'Normal Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle i' => 'color: {{VALUE}}','{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle svg' => 'fill: {{VALUE}}',					
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'tb_icn_color_h',
			[
				'label' => esc_html__( 'Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle:hover i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle:hover svg' => 'fill: {{VALUE}}',
					'separator' => 'after',
				],
			]
		);
		/*toggle icon end*/
		$this->add_control(
			'tb_w_heading',
			[
				'label' => esc_html__( 'Toggle Button Wrapper', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'tbw_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		/*button end*/
		/*lottie style start*/
		$this->start_controls_section(
            'section_lottie_styling',
            [
                'label' => esc_html__('Lottie', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['icon_type' => 'lottie'],
            ]
        );
		$this->add_responsive_control(
            'lottieMright',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Margin Right', 'theplus'),
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
					'size' => 10,
				],
				'render_type' => 'ui',
				'condition' => ['icon_position' => 'tp_ic_pos_before'],
            ]
        );
		$this->add_responsive_control(
            'lottieMleft',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Margin Left', 'theplus'),
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
					'size' => 10,
				],
				'render_type' => 'ui',
				'condition' => ['icon_position' => 'tp_ic_pos_after'],
            ]
        );
		$this->add_responsive_control(
			'lottieWidth',
			[
				'label' => esc_html__( 'Width', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 700,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
			]
		);
		$this->add_responsive_control(
			'lottieHeight',
			[
				'label' => esc_html__( 'Height', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 700,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
			]
		);
		$this->add_responsive_control(
			'lottieSpeed',
			[
				'label' => esc_html__( 'Speed', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
			]
		);
		$this->add_control(
			'lottieLoop',
			[
				'label' => esc_html__( 'Loop Animation', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'lottiehover',
			[
				'label' => esc_html__( 'Hover Animation', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*lottie style end*/
		/*extra button start*/
		$this->start_controls_section(
            'section_eb_styling',
            [
                'label' => esc_html__('Extra Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'etb_typography',
				'selector' => '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link',
				'separator' => 'before',
			]
		);		
		$this->start_controls_tabs( 'tabs_etb' );
		$this->start_controls_tab(
			'tab_etb_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
            'etb_color',
            [
                'label' => esc_html__('Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link' => 'color:{{VALUE}};',
                ],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'etb_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'etb_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'etb_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'etb_shadow',
				'selector' => '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link',				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_etb_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
            'etb_color_h',
            [
                'label' => esc_html__('Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link:hover' => 'color:{{VALUE}};',
                ],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'etb_background_h',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'etb_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link:hover',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'etb_br_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'etb_shadow_h',
				'selector' => '{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link:hover',				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		/*extra toggle icon start*/
		$this->add_control(
			'etb_icn_heading',
			[
				'label' => esc_html__( 'Extra Toggle Icon Options', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'etb_icn_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',				
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'etb_icn_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],					
				'render_type' => 'ui',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link i,
					{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link svg' => 'margin-right: {{SIZE}}{{UNIT}}',
				],
				'condition'    => [
					'icon_position' => [ 'tp_ic_pos_before' ],
				],
            ]
        );
		$this->add_responsive_control(
            'etb_icn_space_left',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],					
				'render_type' => 'ui',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link i,
					{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link svg' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
				'condition'    => [
					'icon_position' => [ 'tp_ic_pos_after' ],
				],
            ]
        );
		$this->add_control(
			'etb_icn_color_n',
			[
				'label' => esc_html__( 'Normal Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link i' => 'color: {{VALUE}}','{{WRAPPER}} .tp-unfold-wrapper .tp-unfold-last-toggle .tp-unfold-toggle-link svg' => 'fill: {{VALUE}}',					
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*extra button end*/
		
		/*style end*/

		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';
		include THEPLUS_PATH. 'modules/widgets/theplus-needhelp.php';
	}
	
	 protected function render() {

        $settings = $this->get_settings_for_display();
		$uid_widget=uniqid("plus");
		$content_title = $settings['content_title'];
		$content_description = $settings['content_description'];
		$title_tag = !empty($settings["uf_title_tag"]) ? $settings["uf_title_tag"] : '';
		
		$icon_position=!empty($settings['icon_position']) ? $settings['icon_position'] : 'tp_ic_pos_before';
		
		$content_readmore = !empty($settings['content_readmore']) ? $settings['content_readmore'] : '';
		$content_readless = !empty($settings['content_readless']) ? $settings['content_readless'] : '';
		$transition_duration = !empty($settings['transition_duration']) ? $settings['transition_duration'] : '200';
		
		$content_readmore_icon=$content_readless_icon=$content_eb_icon='';
		
		if(!empty($settings["content_readmore_icon"])){
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings["content_readmore_icon"], [ 'aria-hidden' => 'true' ]);
			$content_readmore_icon = ob_get_contents();
			ob_end_clean();						
		}
		
		if(!empty($settings["content_readless_icon"])){
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings["content_readless_icon"], [ 'aria-hidden' => 'true' ]);
			$content_readless_icon = ob_get_contents();
			ob_end_clean();						
		}
		if(!empty($settings["content_eb_icon"])){
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings["content_eb_icon"], [ 'aria-hidden' => 'true' ]);
			$content_eb_icon = ob_get_contents();
			ob_end_clean();						
		}
		
		$content_max_height1= !empty($settings['content_max_height']['size']) ? $settings['content_max_height']['size'] : "100";
		$content_max_heightt= !empty($settings['content_max_height_tablet']['size']) ? $settings['content_max_height_tablet']['size'] : "100";
		$content_max_heightm= !empty($settings['content_max_height_mobile']['size']) ? $settings['content_max_height_mobile']['size'] : "100";
		$content='';
		//lottie
		$lottie_icon = $settings["icon_type"];
		$lottie_content = '';
		if(!empty($lottie_icon) && $lottie_icon == 'lottie'){
			$ext = pathinfo($settings['lottieUrl']['url'], PATHINFO_EXTENSION);
			if($ext!='json'){
				$lottie_content .= '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! Please Enter Only JSON File Extension.",'theplus').'</h3>';
			}else{
				if(!empty($icon_position) && $icon_position == 'tp_ic_pos_before'){
					$lottieMright = isset($settings['lottieMright']['size']) ? $settings['lottieMright']['size'] : 10;
					$lottieMleft = isset($settings['lottieMleft']['size']) ? $settings['lottieMleft']['size'] : 0;
				}else if(!empty($icon_position) && $icon_position == 'tp_ic_pos_after'){
					$lottieMright = isset($settings['lottieMright']['size']) ? $settings['lottieMright']['size'] : 0;
					$lottieMleft = isset($settings['lottieMleft']['size']) ? $settings['lottieMleft']['size'] : 10;
				}
				$lottieWidth = isset($settings['lottieWidth']['size']) ? $settings['lottieWidth']['size'] : 20;
				$lottieHeight = isset($settings['lottieHeight']['size']) ? $settings['lottieHeight']['size'] : 20;
				$lottieSpeed = isset($settings['lottieSpeed']['size']) ? $settings['lottieSpeed']['size'] : 1;
				$lottieLoop = isset($settings['lottieLoop']) ? $settings['lottieLoop'] : 'no';
				$lottiehover = isset($settings['lottiehover']) ? $settings['lottiehover'] : 'no';
				$lottieLoopValue='';
				if(!empty($settings['lottieLoop']) && $settings['lottieLoop']=='yes'){
					$lottieLoopValue ='loop'; 
				}
				$lottieAnim='autoplay';
				if(!empty($settings['lottiehover']) && $settings['lottiehover']=='yes'){
					$lottieAnim ='hover'; 
				}
				$lottie_content .='<lottie-player src="'.esc_url($settings['lottieUrl']['url']).'" style="width: '.esc_attr($lottieWidth).'px; height: '.esc_attr($lottieHeight).'px; margin-right: '.esc_attr($lottieMright).'px; margin-left: '.esc_attr($lottieMleft).'px;" '.esc_attr($lottieLoopValue).'  speed="'.esc_attr($lottieSpeed).'" '.esc_attr($lottieAnim).'></lottie-player>';
			}
		}
		if(!empty($settings['content_a_source']) && $settings['content_a_source']=='content'){
			$content .= '<div class="tp-unfold-description" ><div class="tp-unfold-description-inner">'.wp_kses_post($content_description).'</div></div>';
		}
		if((!empty($settings['content_a_source']) && $settings['content_a_source']=='template') && !empty($settings['content_a_template'])){
			$content .= '<div class="tp-unfold-description">';
			$content .= '<div class="tp-unfold-description-inner">';
			$content .=  Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display( $settings['content_a_template'] );
			$content .= '</div>';
			$content .= '</div>';
		}
		
		$ca_class='';
		if(!empty($settings['content_align']) && $settings['content_align']=='center'){
			$ca_class='tp-ca-center';
		}
		
		$innersectionbaseddata='';
		if(!empty($settings['content_a_source']) && ($settings['content_a_source']=='innersectionbased' || $settings['content_a_source']=='containerbased' ) && !empty($settings['co_custom']) && $settings['co_custom']=='yes'){
			$innersectionbaseddata = 'data-co_custom1="'.esc_attr($settings['co_custom']).'"  data-co_c_min_height1="'.esc_attr($settings['co_c_min_height']['size']).'" data-co_c_opacity_color1="'.esc_attr($settings['co_c_opacity_color']).'"';
		}
		if(!empty($lottie_icon) && $lottie_icon == 'lottie'){
			$content_readmore_icon = $lottie_content;
			$content_readless_icon = $lottie_content;
		}

		$unfold_scroll_top = isset($settings['unfold_scroll_top']) ? $settings['unfold_scroll_top'] : '';
		$stunfoldclass = $stunfold = '';
		if( $unfold_scroll_top == 'yes' ){
			$stunfoldclass = ' tp-scrolltop-unfold';
			$stunfold = ' data-scroll-top-unfold='.$unfold_scroll_top.'';
		}

		$output = '<div class="tp-unfold-wrapper '.esc_attr($uid_widget).' '.esc_attr($ca_class).' '.esc_attr($stunfoldclass).'" data-id="'.esc_attr($uid_widget).'"  data-content_a_source="'.$settings['content_a_source'].'" data-con_pos="'.$settings['con_pos'].'" data-icon-position="'.esc_attr($icon_position).'" data-readmore="'.$content_readmore.'" data-readless="'.$content_readless.'" data-readmore-icon="'.esc_html($content_readmore_icon).'" data-readless-icon="'.esc_html($content_readless_icon).'"  data-transition-duration="'.esc_attr($transition_duration).'" data-content-max-height="'.esc_attr($content_max_height1).'" data-content-max-height-t="'.esc_attr($content_max_heightt).'" data-content-max-height-m="'.esc_attr($content_max_heightm).'" '.$innersectionbaseddata.' '.$stunfold.'>';
		
				
			$output .= '<'.esc_attr(theplus_validate_html_tag($title_tag)).' class="tp-unfold-title">'.esc_html($content_title).'</'.esc_attr(theplus_validate_html_tag($title_tag)).'>';
			if(!empty($settings['con_pos']) && $settings['con_pos']=='default'){
				$output .= $content;
			}
			
			$content_readmore_icon_before=$content_readmore_icon_after=$content_eb_icon_before=$content_eb_icon_after='';
			if(!empty($icon_position) && $icon_position=='tp_ic_pos_before'){
				$content_readmore_icon_before = $content_readmore_icon;
				$content_eb_icon_before = $content_eb_icon;
			}else if(!empty($icon_position) && $icon_position=='tp_ic_pos_after'){
				$content_readmore_icon_after = $content_readmore_icon;
				$content_eb_icon_after = $content_eb_icon;
			}
			if(!empty($lottie_icon) && $lottie_icon == 'lottie'){
                if(!empty($icon_position) && $icon_position == 'tp_ic_pos_before'){
					$content_readmore_icon_before = $lottie_content;
				}else if(!empty($icon_position) && $icon_position == 'tp_ic_pos_after'){
					$content_readmore_icon_after = $lottie_content;
				}
			}
			
			$output .= '<div class="tp-unfold-last-toggle '.$settings['content_align'].'">';
				$lz1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['tb_background_image'],$settings['tb_background_h_image']) : '';
				if(!empty($settings['content_align']) && $settings['content_align']!='flex-end'){
					$output .= '<button class="tp-unfold-toggle '.esc_attr($lz1).'">'.$content_readmore_icon_before.' '.esc_html($content_readmore).' '.$content_readmore_icon_after.'</button>';
				}
				if(!empty($settings['extra_link']) && $settings['extra_link']=='yes'){
					$eb_text = !empty($settings['eb_text']) ? $settings['eb_text'] : '';		
					$target = $settings['eb_link']['is_external'] ? ' target="_blank"' : '';
					$nofollow = $settings['eb_link']['nofollow'] ? ' rel="nofollow"' : '';
					
					$lz2 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['etb_background_image'],$settings['etb_background_h_image']) : '';
					$output .= '<a class="tp-unfold-toggle-link '.esc_attr($lz2).'" href="' .esc_url($settings['eb_link']['url']). '"' . $target . $nofollow . '>'.$content_eb_icon_before.' '.esc_html($eb_text).' '.$content_eb_icon_after.'</a>';
				}
				if(!empty($settings['content_align']) && $settings['content_align']=='flex-end'){
					$output .= '<button class="tp-unfold-toggle '.esc_attr($lz1).'">'.$content_readmore_icon_before.' '.esc_html($content_readmore).' '.$content_readmore_icon_after.'</button>';
				}
			$output .= '</div>';
			
			if(!empty($settings['con_pos']) && $settings['con_pos']=='after_button'){
				$output .= $content;
			}
			
		$output .= '</div>';
					
		echo $output;
	}
	
    protected function content_template() {
	
    }

}