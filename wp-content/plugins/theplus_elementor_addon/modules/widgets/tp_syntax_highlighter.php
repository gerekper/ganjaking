<?php 
/*
Widget Name: Syntax Highlighter
Description: Syntax Highlighter
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
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class ThePlus_Syntax_Highlighter extends Widget_Base {
		
	public function get_name() {
		return 'tp-syntax-highlighter';
	}

    public function get_title() {
        return esc_html__('Syntax Highlighter', 'theplus');
    }

    public function get_icon() {
        return 'fa- tp-syntax-highlighter theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }

    protected function register_controls() {
		
		$this->start_controls_section(
			'syn_content_section',
			[
				'label' => esc_html__( 'Source Code', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'languageType',
			[
				'label' => esc_html__( 'Language', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'markup',
				'options' => [
					'markup'  => esc_html__( 'HTML Markup', 'theplus' ),
					'basic' => esc_html__( 'Basic', 'theplus' ),
					'c' => esc_html__( 'C', 'theplus' ),
					'c#' => esc_html__( 'C#', 'theplus' ),
					'cpp' => esc_html__( 'CPP', 'theplus' ),
					'css' => esc_html__( 'CSS', 'theplus' ),
					'css-extras' => esc_html__( 'CSS Extra', 'theplus' ),
					'gcode' => esc_html__( 'Gcode', 'theplus' ),
					'git' => esc_html__( 'Git', 'theplus' ),
					'http' => esc_html__( 'Http', 'theplus' ),
					'java' => esc_html__( 'Java', 'theplus' ),
					'javadoc' => esc_html__( 'Java Doc', 'theplus' ),
					'javadoclike' => esc_html__( 'Java Doc-Like', 'theplus' ),
					'javascript' => esc_html__( 'Javascript', 'theplus' ),
					'jsdoc' => esc_html__( 'JSDoc', 'theplus' ),
					'js-extras' => esc_html__( 'JS Extra', 'theplus' ),
					'js-templates' => esc_html__( 'JS Templates', 'theplus' ),
					'json' => esc_html__( 'Json', 'theplus' ),
					'jsonp' => esc_html__( 'Jsonp', 'theplus' ),
					'json5' => esc_html__( 'Json5', 'theplus' ),
					'perl' => esc_html__( 'Perl', 'theplus' ),
					'php' => esc_html__( 'Php', 'theplus' ),
					'phpdoc' => esc_html__( 'Phpdoc', 'theplus' ),
					'php-extras' => esc_html__( 'Php Extra', 'theplus' ),
					'plsql' => esc_html__( 'PL/SQL', 'theplus' ),
					'python' => esc_html__( 'Python', 'theplus' ),
					'react' => esc_html__( 'React', 'theplus' ),
					'ruby' => esc_html__( 'Ruby', 'theplus' ),
					'sas' => esc_html__( 'Sas', 'theplus' ),
					'sass' => esc_html__( 'Sass', 'theplus' ),
					'scss' => esc_html__( 'Scss', 'theplus' ),
					'scheme' => esc_html__( 'Scheme', 'theplus' ),
					'sql' => esc_html__( 'SQL', 'theplus' ),
					'vbnet' => esc_html__( 'VB.Net', 'theplus' ),
					'visual-basic' => esc_html__( 'Visual Basic', 'theplus' ),
					'wiki' => esc_html__( 'Wiki', 'theplus' ),
					'xquery' => esc_html__( 'Xquery', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'themeType',
			[
				'label' => esc_html__( 'Theme', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'prism-default',
				'options' => [
					'prism-default'  => esc_html__( 'Default', 'theplus' ),
					'prism-coy' => esc_html__( 'Coy', 'theplus' ),
					'prism-dark' => esc_html__( 'Dark', 'theplus' ),
					'prism-funky' => esc_html__( 'Funky', 'theplus' ),
					'prism-okaidia' => esc_html__( 'Okaidia', 'theplus' ),
					'prism-solarizedlight' => esc_html__( 'Solarized Light', 'theplus' ),
					'prism-tomorrownight' => esc_html__( 'Tomorrow', 'theplus' ),
					'prism-twilight' => esc_html__( 'Twilight', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'sourceCode',
			[
				'label' => esc_html__( 'Source Code', 'theplus' ),
				'type' => Controls_Manager::CODE,
				'dynamic' => ['active' => true,],
				'default' => '<h1>Welcome To Posimyth Innovation</h1>',
			]
		);
		$this->add_responsive_control(
			'synTxtAlign',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
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
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .tp-code-highlighter pre,
					{{WRAPPER}} .tp-code-highlighter pre code' => 'text-align: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		/*Source Code Section End*/
		/*Options Section End*/
		$this->start_controls_section(
			'syn_options_section',
			[
				'label' => esc_html__( 'Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'lanugaetext',
			[
				'label' => esc_html__( 'Language Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true,],
				'default' =>'',
				'placeholder' => esc_html__( 'Enter Text', 'theplus' ),	
				'label_block' => true,				
			]
		);
		$this->add_control(
			'cpybtntext',
			[
				'label' => esc_html__( 'Copy Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true,],
				'default' => esc_html__( 'Copy', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Text', 'theplus' ),	
				'label_block' => true,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'cpybtnicon',
			[
				'label' => esc_html__( 'Copy Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
			]
		);
		$this->add_control(
			'copiedbtntext',
			[
				'label' => esc_html__( 'Copied Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true,],
				'default' => esc_html__( 'Copied!', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Text', 'theplus' ),	
				'label_block' => true,				
			]
		);
		$this->add_control(
			'copiedbtnicon',
			[
				'label' => esc_html__( 'Copied Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
			]
		);
		$this->add_control(
			'cpyerrbtntext',
			[
				'label' => esc_html__( 'Copy Error Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true,],
				'default' => esc_html__( 'Error', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Text', 'theplus' ),	
				'label_block' => true,				
			]
		);
		$this->add_control(
			'lineNumber',
			[
				'label' => esc_html__( 'Line Number', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'lineHighlight',
			[
				'label' 		=> esc_html__( 'Line Highlight', 'theplus' ),
				'type' 			=> Controls_Manager::TEXT,
				'dynamic' 		=> [ 'active' => true ],
				'default' 		=> '',
				'placeholder' => esc_html__( 'Ex: 1,2,3,4-15', 'theplus' ),
				'label_block' 	=> true,
			]
		);
		$this->add_control(
			'dnloadBtn',
			[
				'label' => esc_html__( 'Download Button', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'dwnldBtnText',
			[
				'label' => esc_html__( 'Button Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => ['active' => true,],
				'default' => esc_html__( 'Download', 'theplus' ),
				'condition'		=> [
					'dnloadBtn' => 'yes',
				],			
			]
		);
		$this->add_control(
			'dwnldBtnIcon',
			[
				'label' => esc_html__( 'Button Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
			]
		);
		$this->add_control(
			'fileLink',
			[
				'label' => __( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'show_external' => true,
				'dynamic' 		=> [ 'active' => true ],
				'default' => [
					'url' => '#',
					'is_external' => true,
					'nofollow' => true,
				],
				'condition'		=> [
					'dnloadBtn' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*Options Section End*/
		/*Source Code Style Start*/
		$this->start_controls_section(
            'syn_scode_styling',
            [
                'label' => esc_html__('Source Code', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
        $this->add_responsive_control(
			'scodeMargin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .tp-code-highlighter pre' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'scodePadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .tp-code-highlighter pre' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
            'scodeHeight',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .tp-code-highlighter pre' => 'height: {{SIZE}}{{UNIT}};',	
				],
            ]
        );
		$this->end_controls_section();
		/*Source Code Style End*/
		/*Line Number Style Start*/
		$this->start_controls_section(
            'syn_lineno_styling',
            [
                'label' => esc_html__('Line Number', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'    => [
					'lineNumber' => 'yes',
				],				
            ]
        );
		$this->add_control(
			'numberColor',
			[
				'label' => esc_html__( 'Number Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .line-numbers-rows > span:before' => 'color: {{VALUE}};',
				],
				'condition'    => [
					'lineNumber' => 'yes',
				],
			]
		);
		$this->add_control(
			'bdrColor',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .line-numbers .line-numbers-rows' => 'border-right: 1px solid {{VALUE}};',
				],
				'condition'    => [
					'lineNumber' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*Line Number Style End*/
		/*Line Highlight Style Start*/
		$this->start_controls_section(
            'syn_lineHgt_styling',
            [
                'label' => esc_html__('Line Highlight', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'    => [
					'lineHighlight!' => '',
				],				
            ]
        );
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'highlightBG',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .line-highlight',
				'condition'    => [
					'lineHighlight!' => '',
				],
			]
		);
        $this->end_controls_section();
		/*Line Highlight Style End*/
		
		/*language text style start*/
		$this->start_controls_section(
            'syn_langtxt_styling',
            [
                'label' => esc_html__('Language Text', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,                			
            ]
        );        
        $this->add_responsive_control(
			'langtxtPadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'langtxtMargin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'langtxtTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span',				
			]
		);
		$this->start_controls_tabs( 'tabs_langtxt' );
		$this->start_controls_tab(
			'tab_langtxt_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'langtxtNColor',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'langtxtNmlBG',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'langtxtBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span',				
			]
		);
		$this->add_responsive_control(
			'langtxtNRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'langtxtNShadow',
				'selector' => '{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span',				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_langtxt_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),				
			]
		);
		$this->add_control(
			'langtxtColor',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'langtxtHvrBG',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span:hover',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'langtxtHBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span:hover',
			]
		);
		$this->add_responsive_control(
			'langtxtHRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'langtxtHShadow',
				'selector' => '{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*language text Style End*/
		
		/*Copy/Download Button Style Start*/
		$this->start_controls_section(
            'syn_cpdnbtn_styling',
            [
                'label' => esc_html__('Copy/Download Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,                			
            ]
        );        
        $this->add_responsive_control(
			'copyDwlBtnPadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .toolbar-item button,{{WRAPPER}} .toolbar-item a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'copyDwlBtnMargin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .toolbar-item button,{{WRAPPER}} .toolbar-item a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'copyDwlBtnTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .toolbar-item button,{{WRAPPER}} .toolbar-item a',				
			]
		);
		$this->add_control(
			'copyDwlBtnIconSize',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar a i,{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar button i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar a svg,{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar button svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_cpdnbtn' );
		$this->start_controls_tab(
			'tab_cpdnbtn_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'copyDwlBtnNColor',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .toolbar-item button,{{WRAPPER}} .toolbar-item a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'copyDwlBtniconNColor',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar a i,{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar button i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar a svg,{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar button svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'copyDwlBtnNmlBG',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .toolbar-item button,{{WRAPPER}} .toolbar-item a',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'copyDwlBtnNBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .toolbar-item button,{{WRAPPER}} .toolbar-item a',				
			]
		);
		$this->add_responsive_control(
			'copyDwlBtnNRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .toolbar-item button,{{WRAPPER}} .toolbar-item a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'copyDwlBtnNShadow',
				'selector' => '{{WRAPPER}} .toolbar-item button,{{WRAPPER}} .toolbar-item a',				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_cpdnbtn_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),				
			]
		);
		$this->add_control(
			'copyDwlBtnHColor',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .toolbar-item button:hover,{{WRAPPER}} .toolbar-item a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .toolbar-item a:hover svg,{{WRAPPER}} .toolbar-item button:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'copyDwlBtniconHColor',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar a:hover i,{{WRAPPER}} .tp-code-highlighter div.code-toolbar>.toolbar button:hover i' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'copyDwlBtnHvrBG',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .toolbar-item button:hover,{{WRAPPER}} .toolbar-item a:hover',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'copyDwlBtnHBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .toolbar-item button:hover,{{WRAPPER}} .toolbar-item a:hover',
			]
		);
		$this->add_responsive_control(
			'copyDwlBtnHRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .toolbar-item button:hover,{{WRAPPER}} .toolbar-item a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'copyDwlBtnHShadow',
				'selector' => '{{WRAPPER}} .toolbar-item button:hover,{{WRAPPER}} .toolbar-item a:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Copy/Download Button Style End*/
		
		/*scrollbar*/
		$this->start_controls_section(
            'content_scrolling_bar_section_styling',
            [
                'label' => esc_html__('Scrolling Bar', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
				
            ]
        );
		$this->add_control(
			'display_scrolling_bar',
			[
				'label' => esc_html__( 'Scrolling Bar', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),				
				'default' => 'no',
			]
		);
		
		$this->start_controls_tabs( 'tabs_scrolling_bar_style' );
		$this->start_controls_tab(
			'tab_scrolling_bar_scrollbar',
			[
				'label' => esc_html__( 'Scrollbar', 'theplus' ),
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'scroll_scrollbar_width',
			[
				'label' => esc_html__( 'ScrollBar Width', 'theplus' ),
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
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .tp-code-highlighter .code-toolbar pre::-webkit-scrollbar' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'scroll_scrollbar_height',
			[
				'label' => esc_html__( 'ScrollBar Height', 'theplus' ),
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
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .tp-code-highlighter .code-toolbar pre::-webkit-scrollbar' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_scrolling_bar_thumb',
			[
				'label' => esc_html__( 'Thumb', 'theplus' ),
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'scroll_thumb_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-code-highlighter .code-toolbar pre::-webkit-scrollbar-thumb',
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'scroll_thumb_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-code-highlighter .code-toolbar pre::-webkit-scrollbar-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',					
				],
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'scroll_thumb_shadow',
				'selector' => '{{WRAPPER}} .tp-code-highlighter .code-toolbar pre::-webkit-scrollbar-thumb',
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_scrolling_bar_track',
			[
				'label' => esc_html__( 'Track', 'theplus' ),
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'scroll_track_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-code-highlighter .code-toolbar pre::-webkit-scrollbar-track',
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'scroll_track_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-code-highlighter .code-toolbar pre::-webkit-scrollbar-track' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'scroll_track_shadow',
				'selector' => '{{WRAPPER}} .tp-code-highlighter .code-toolbar pre::-webkit-scrollbar-track',
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*scrollbar*/
	}
	
	 protected function render() {

        $settings = $this->get_settings_for_display();
		$uid_synhg = uniqid("tp-synhg");
		$languageType = !empty($settings['languageType']) ? $settings['languageType'] : 'markup';
		$themeType = !empty($settings['themeType']) ? $settings['themeType'] : 'prism-default';
		$sourceCode = !empty($settings['sourceCode']) ? $settings['sourceCode'] : '';
		$lineNumber = !empty($settings['lineNumber'] == 'yes') ? true : false;
		$lineHighlight = !empty($settings['lineHighlight']) ? $settings['lineHighlight'] : '';
		$dnloadBtn = !empty($settings['dnloadBtn'] == 'yes') ? true : false;
		$dwnldBtnText = !empty($settings['dwnldBtnText']) ? $settings['dwnldBtnText'] : '';
		$fileLink = !empty($settings['fileLink']['url']) ? $settings['fileLink']['url'] : '';
		
		$cpybtnicon=$copiedbtnicon=$cpybtniconclass=$dowbtniconclass='';
		$cpybtntext = !empty($settings['cpybtntext']) ? $settings['cpybtntext'] : '';
		
		if(!empty($settings["cpybtnicon"]["value"]) || !empty($settings["copiedbtnicon"]["value"])){	
			$cpybtniconclass = " tpcpicon";
		}
		
		if(!empty($settings["cpybtnicon"])){
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings["cpybtnicon"], [ 'aria-hidden' => 'true' ]);
			$cpybtnicon = ob_get_contents();
			ob_end_clean();
		}
		$copiedbtntext = !empty($settings['copiedbtntext']) ? $settings['copiedbtntext'] : '';
		if(!empty($settings["copiedbtnicon"])){			
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings["copiedbtnicon"], [ 'aria-hidden' => 'true' ]);
			$copiedbtnicon = ob_get_contents();
			ob_end_clean();
		}
		$cpyerrbtntext = !empty($settings['cpyerrbtntext']) ? $settings['cpyerrbtntext'] : '';
		
		$lineNumClass = '';
		if(!empty($lineNumber) && $lineNumber == 'yes'){
			$lineNumClass = 'line-numbers';
		}
		$dwnldBtnClass=$dwnldBtnIcon='';
		if(!empty($dnloadBtn) && $dnloadBtn == 'yes') {
			$dwnldBtnClass = 'data-src='.$fileLink.' data-download-link data-download-link-label='.$dwnldBtnText.'';
			
			if(!empty($settings["dwnldBtnIcon"]["value"])){
				$dowbtniconclass = " tpdowicon";
			}
			if(!empty($settings["dwnldBtnIcon"])){
				ob_start();
				\Elementor\Icons_Manager::render_icon( $settings["dwnldBtnIcon"], [ 'aria-hidden' => 'true' ]);
				$dwnldBtnIcon = ob_get_contents();
				ob_end_clean();
			}
		}
		
		$langtext = '';
		if(!empty($settings['lanugaetext'])){
			$langtext =  'data-label="'.esc_html($settings['lanugaetext']).'"';
		}
				
		$output = '<div class="tp-code-highlighter code-'.esc_attr($themeType).' tp-widget-'.esc_attr($uid_synhg).' '.esc_attr($cpybtniconclass).' '.esc_attr($dowbtniconclass).'" data-prismjs-copy="'.esc_html($cpybtntext).'"  data-copyicon="'.esc_html($cpybtnicon).'" data-prismjs-copy-success="'.esc_html($copiedbtntext).'" data-copiedbtnicon="'.esc_html($copiedbtnicon).'" data-download-text="'.esc_html($dwnldBtnText).'" data-download-iconsh="'.esc_html($dwnldBtnIcon).'">';
			$output .='<pre class="language-'.esc_attr($languageType).' '.esc_attr($lineNumClass).'" data-line="'.esc_attr($lineHighlight).'" '.esc_attr($dwnldBtnClass).' '.$langtext.' data-previewers="angle color gradient easing time">';
				$output .='<code class="language-'.esc_attr($languageType).'" data-prismjs-copy="'.$cpybtntext.'" data-prismjs-copy-error="'.$cpyerrbtntext.'" data-prismjs-copy-success="'.$copiedbtntext.'">';
					$output .= esc_html($sourceCode);
				$output .='</code>';
			$output .='</pre>';
		$output .= '</div>';
		
		echo $output;
	}
	
    protected function content_template() {
	
    }
}