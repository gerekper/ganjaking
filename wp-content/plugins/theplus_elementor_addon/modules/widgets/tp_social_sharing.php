<?php 
/*
Widget Name: Social Sharing
Description: Social Sharing
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Social_Sharing extends Widget_Base {
		
	public function get_name() {
		return 'tp-social-sharing';
	}

    public function get_title() {
        return esc_html__('Social Sharing', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-share-square theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }

    protected function register_controls() {
		/*Content Layout */
		$this->start_controls_section(
			'social_sharing_content_section',
			[
				'label' => esc_html__( 'Social Sharing', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'sociallayout',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal'  => esc_html__( 'Horizontal', 'theplus' ),
					'vertical' => esc_html__( 'Vertical', 'theplus' ),
					'toggle' => esc_html__( 'Toggle', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'styleHZ',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'style-4' => esc_html__( 'Style 4', 'theplus' ),
					'style-5' => esc_html__( 'Style 5', 'theplus' ),
				],
				'condition' => [
					'sociallayout' => 'horizontal',
				],	
			]
		);
		$this->add_control(
			'style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
				],
				'condition' => [
					'sociallayout' => 'vertical',
				],	
			]
		);
		$this->add_control(
			'toggleStyle',
			[
				'label' => esc_html__( 'Toggle Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
				],
				'condition' => [
					'sociallayout' => 'toggle',
				],
			]
		);
		$this->add_control(
			'hDirection',
			[
				'label' => esc_html__( 'Hover Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => [
					'left'  => esc_html__( 'Left', 'theplus' ),
					'right' => esc_html__( 'Right', 'theplus' ),
					'top'  => esc_html__( 'Top', 'theplus' ),
					'bottom' => esc_html__( 'Bottom', 'theplus' ),
				],
				'condition' => [
					'sociallayout' => 'toggle',
					'toggleStyle' => 'style-2',
				],	
			]
		);
		$this->add_control(
			'viewtype',
			[
				'label' => esc_html__( 'View Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'iconText',
				'options' => [
					'icon'  => esc_html__( 'Icon', 'theplus' ),
					'iconCount' => esc_html__( 'Icon Count', 'theplus' ),
					'text' => esc_html__( 'Text', 'theplus' ),
					'textCount' => esc_html__( 'Text Count', 'theplus' ),
					'iconText' => esc_html__( 'Icon Text', 'theplus' ),
					'iconTextCount' => esc_html__( 'Icon Text Count', 'theplus' ),
				],
				'condition' => [
					'sociallayout!' => 'toggle',
				],	
			]
		);
		$this->add_control(
			'column',
			[
				'label' => esc_html__( 'Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => [
					'auto'  => esc_html__( 'Auto', 'theplus' ),
					'1' => esc_html__( '1', 'theplus' ),
					'2' => esc_html__( '2', 'theplus' ),
					'3' => esc_html__( '3', 'theplus' ),
					'4' => esc_html__( '4', 'theplus' ),
					'5' => esc_html__( '5', 'theplus' ),
				],
				'selectors'  => [
						'{{WRAPPER}} .tp-social-list .tp-social-menu,
						{{WRAPPER}} .tp-social-list .totalcount' => 'width: calc(100%/{{VALUE}})',
				],
				'condition' => [
					'sociallayout' => 'horizontal',
				],
			]
		);
		$this->add_responsive_control(
			'alignment',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'left',
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
					'{{WRAPPER}} .tp-social-sharing' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .tp-social-list' => 'justify-content: {{VALUE}}',
				],
				'condition' => [
					'sociallayout' => 'horizontal',
					'styleHZ!' => ['style-4','style-5'],
				],
			]
		);
		$this->add_responsive_control(
			'alignmentV',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'left',
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
					'{{WRAPPER}} .tp-social-sharing' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .tp-social-list' => 'justify-content: {{VALUE}}',
				],
				'condition' => [
					'sociallayout' => 'vertical',
				],
			]
		);
		$this->add_responsive_control(
			'alignmentT',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'left',
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
					'{{WRAPPER}} .tp-social-sharing' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .tp-social-list' => 'justify-content: {{VALUE}}',
				],
				'condition' => [
					'sociallayout' => 'toggle',
					'toggleStyle' => ['style-2'],
				],
			]
		);
		$this->add_responsive_control(
			'style1Align',
			[
				'label' => esc_html__( 'Content Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'text-left',
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
				'condition' => [
					'sociallayout!' => 'toggle',
				],
			]
		);	
		$this->add_control(
            'displayCounter',
            [
				'label' => esc_html__( 'Display Counter', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'sociallayout!' => 'toggle',
				],				
			]
        );
        $this->add_control(
			'shareNumber',
			[
				'label' => esc_html__( 'Total Share', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( '1.2K', 'theplus' ),	
				'label_block' => true,
				'condition' => [
					'sociallayout!' => 'toggle',
					'displayCounter' => 'yes',
				],			
			]
		);
		$this->add_control(
			'shareLabel',
			[
				'label' => esc_html__( 'Share Label', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Share', 'theplus' ),	
				'label_block' => true,	
				'condition' => [
					'sociallayout!' => 'toggle',
					'displayCounter' => 'yes',
				],	
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'socialNtwk',[
				'label' => esc_html__( 'Social Network','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'fab fa-facebook-f',
				'options' => [
					'fab fa-amazon' => esc_html__( 'Amazon ','theplus' ),
					'fab fa-digg' => esc_html__( 'Digg ','theplus' ),
					'fab fa-delicious' => esc_html__( 'Delicious ','theplus' ),
					'far fa-envelope' => esc_html__( 'Mail','theplus' ),
					'fab fa-facebook-f' => esc_html__( 'FaceBook ','theplus' ),
					'fab fa-facebook-messenger' => esc_html__( 'Facebook Messenger ','theplus' ),
					'fab fa-get-pocket' => esc_html__( 'Pocket ','theplus' ),
					'fab fa-linkedin-in' => esc_html__( 'Linkedln ','theplus' ),
					'fab fa-odnoklassniki' => esc_html__( 'Odnoklassniki ','theplus' ),
					'fab fa-pinterest-p' => esc_html__( 'Pinterest ','theplus' ),
					'fab fa-reddit' => esc_html__( 'Reddit ','theplus' ),
					'fab fa-skype' => esc_html__( 'Skype ','theplus' ),
					'fab fa-snapchat-ghost' => esc_html__( 'Snapchat ','theplus' ),
					'fab fa-stumbleupon' => esc_html__( 'Stumbleupon ','theplus' ),
					'fab fa-telegram-plane' => esc_html__( 'Telegram ','theplus' ),
					'fab fa-tumblr' => esc_html__( 'Tumblr ','theplus' ),
					'fab fa-twitter' => esc_html__( 'Twitter ','theplus' ),
					'fab fa-viber' => esc_html__( 'Viber ','theplus' ),
					'fab fa-vk' => esc_html__( 'VK ','theplus' ),
					'fab fa-weibo' => esc_html__( 'Weibo ','theplus' ),
					'fab fa-whatsapp' => esc_html__( 'Whatsapp ','theplus' ),
					'fab fa-xing' => esc_html__( 'Xing ','theplus' ),
					'tpmsp' => esc_html__( 'Mobile Share Panel ','theplus' ),
				],
			]
		);
		$repeater->add_control(
			'tpmspIcons',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
				'condition' => [
					'socialNtwk' => 'tpmsp',
				],	
			]
		);
		$repeater->add_control(
			'tpmsp_open',
			[
				'label' => esc_html__( 'Open Mobile Share Panel', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1500,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 767,
				],
				'condition' => [					
					'socialNtwk' => 'tpmsp',
				],
			]
		);
		$repeater->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Network', 'theplus' ),	
				'label_block' => true,				
			]
		);
	    $repeater->add_control(
			'countNumber',
			[
				'label' => esc_html__( 'Count Number', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( '7', 'theplus' ),	
				'label_block' => true,							
			]
		);
		$repeater->add_control(
			'countLabel',
			[
				'label' => esc_html__( 'Count Label', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'label_block' => true,							
			]
		);
		$repeater->add_control(
			'socialBtn',
			[
				'label' => esc_html__( 'Social Button', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',	
				'label_block' => true,								
			]
		);
		$repeater->add_control(
            'customURL',
            [
				'label' => esc_html__( 'Custom URL', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'socialNtwk!' => 'tpmsp',
				],
				'separator' => 'before',	
			]
        );
        $repeater->add_control(
			'customLink',
			[
				'label' => esc_html__( 'Custom URL Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'condition' => [
					'socialNtwk!' => 'tpmsp',
					'customURL' => 'yes',
				],
				'separator' => 'after',
			]
		);
		$repeater->start_controls_tabs( 'Nml_Hvr_icon_color' );
		$repeater->start_controls_tab(
			'Nml_icon_color',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
        $repeater->add_control(
            'titleNmlColor',
            [
                'label' => esc_html__('Title Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-social-list {{CURRENT_ITEM}} .social-btn-title' => 'color:{{VALUE}};',
                ],
            ]
        );
        $repeater->add_control(
            'countNmlColor',
            [
                'label' => esc_html__('Count Number Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-social-list {{CURRENT_ITEM}} .social-count' => 'color:{{VALUE}};',
                ],
            ]
        );
        $repeater->add_control(
            'countLblNmlColor',
            [
                'label' => esc_html__('Count Label Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-social-list {{CURRENT_ITEM}} .count-label' => 'color:{{VALUE}};',
                ],
            ]
        );
        $repeater->add_control(
            'socialbtnNmlColor',
            [
                'label' => esc_html__('Social Button Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sharing-horizontal.sharing-style-4 .tp-social-list {{CURRENT_ITEM}} .social-button' => 'color:{{VALUE}};',
                ],
                'condition' => [
					'sociallayout' => 'horizontal',
					'styleHZ' => 'style-4',
				],	
            ]
        );
        $repeater->add_control(
            'iconNmlColor',
            [
                'label' => esc_html__('Icon Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-social-list {{CURRENT_ITEM}} .social-btn-icon' => 'color:{{VALUE}};',
                ],
            ]
        );
        $repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
			   'name' => 'iconNmlBG',
			   'label' => esc_html__( 'Icon Background', 'theplus' ),
			   'types' => [ 'classic', 'gradient' ],
			   'selector' => '{{WRAPPER}} .tp-social-list {{CURRENT_ITEM}} .share-btn .social-btn-icon',
			]
		);
		$repeater->add_control(
            'nmlBColor',
            [
                'label' => esc_html__('Border Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-social-list {{CURRENT_ITEM}} .share-btn' => 'border-color:{{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );
		$repeater->add_group_control(
				Group_Control_Background::get_type(),
				[
				   'name' => 'normalBG',
				   'label' => esc_html__( 'Background', 'theplus' ),
				   'types' => [ 'classic', 'gradient' ],
				   'selector' => '{{WRAPPER}} .tp-social-list {{CURRENT_ITEM}} .share-btn',
				    'separator' => 'before',
				]
		);      
        $repeater->end_controls_tab();
		$repeater->start_controls_tab(
			'Hvr_icon_color',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
         $repeater->add_control(
            'titleHvrColor',
            [
                'label' => esc_html__('Title Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-social-list {{CURRENT_ITEM}} .share-btn:hover .social-btn-title' => 'color:{{VALUE}};',
                ],
            ]
        );
        $repeater->add_control(
            'countHvrColor',
            [
                'label' => esc_html__('Count Number Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-social-list {{CURRENT_ITEM}} .share-btn:hover .social-count' => 'color:{{VALUE}};',
                ],
            ]
        );
        $repeater->add_control(
            'countLblHvrColor',
            [
                'label' => esc_html__('Count Label Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-social-list {{CURRENT_ITEM}} .share-btn:hover .count-label' => 'color:{{VALUE}};',
                ],
            ]
        );
        $repeater->add_control(
            'socialbtnHvrColor',
            [
                'label' => esc_html__('Social Button Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sharing-horizontal.sharing-style-4 .tp-social-list {{CURRENT_ITEM}} .share-btn:hover .social-button' => 'color:{{VALUE}};',
                ],
                'condition' => [
					'sociallayout' => 'horizontal',
					'styleHZ' => 'style-4',
				],	
            ]
        );
        $repeater->add_control(
            'iconHvrColor',
            [
                'label' => esc_html__('Icon Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-social-list {{CURRENT_ITEM}} .share-btn:hover .social-btn-icon' => 'color:{{VALUE}};',
                ],
            ]
        );
        $repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
			   'name' => 'iconHvrBG',
			   'label' => esc_html__( 'Icon Background', 'theplus' ),
			   'types' => [ 'classic', 'gradient' ],
			   'selector' => '{{WRAPPER}} .tp-social-list {{CURRENT_ITEM}} .share-btn:hover .social-btn-icon',
			]
		);
		$repeater->add_control(
            'hvrBColor',
            [
                'label' => esc_html__('Border Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-social-list {{CURRENT_ITEM}} .share-btn:hover' => 'border-color:{{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );
		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
			   'name' => 'hoverBG',
			   'label' => esc_html__( 'Background', 'theplus' ),
			   'types' => [ 'classic', 'gradient' ],
			   'selector' => '{{WRAPPER}} .tp-social-list {{CURRENT_ITEM}} .share-btn:hover',
			    'separator' => 'before',
			]
		);
        $repeater->end_controls_tab();
		$repeater->end_controls_tabs();		
		$this->add_control(
            'socialSharing',
            [
				'label' => esc_html__( 'Social Sharing', 'theplus' ),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'socialNtwk' => 'fab fa-facebook-f',
                        'title' => 'Facebook',                                               
                    ],
                    [
                        'socialNtwk' => 'fab fa-twitter',
                        'title' => 'Twitter',                        
                    ],						
					[
                        'socialNtwk' => 'fab fa-pinterest-p',
                        'title' => 'Pinterest',                         
                    ],	
                    [
                        'socialNtwk' => 'fab fa-linkedin-in',
                        'title' => 'Linkedln',                         
                    ],
                ],
                'separator' => 'before',
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',				
            ]
        );  
		$this->end_controls_section();
	    /* Content Layout */
	    /* Toggle Button */
		$this->start_controls_section(
			'social_sharing_toggle_content_section',
			[
				'label' => esc_html__( 'Toggle Button', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'sociallayout' => 'toggle',
				],	
			]
		);
		$this->add_control(
			'toggleText',
			[
				'label' => esc_html__( 'Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'share', 'theplus' ),	
				'label_block' => true,
				'condition' => [
					'toggleStyle' => 'style-1',
				],	
			]
		);
		$this->add_control(
			'iconStore',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,			
				'default' => [
					'value' => 'fa fa-share-alt',
					'library' => 'solid',
				],
			]
		);
		$this->end_controls_section();
	    /* Toggle Button */
        /*Title Style*/	
        $this->start_controls_section(
            'social_sharing_title_styling',
            [
                'label' => esc_html__('Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'sociallayout!' => 'toggle',
					'viewtype!' => ['icon','iconCount'],
				],	
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'titleTypo',
                'label' => esc_html__('Typography', 'theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .tp-social-list .tp-social-menu .social-btn-title',
            ]
        );
        $this->add_responsive_control(
			'titleSpace',
			[
				'label'      => esc_html__( 'Title Spacing', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%','em' ],
				'selectors'  => [
					'{{WRAPPER}} .sharing-horizontal .tp-social-list .social-btn-title,{{WRAPPER}} .sharing-vertical .tp-social-list .social-btn-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				 'separator' => 'before',
			]
		);
        $this->end_controls_section();
        /* Title Style */	
        /* Counter Style */	
        $this->start_controls_section(
            'social_sharing_counter_styling',
            [
                'label' => esc_html__('Counter', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'sociallayout!' => 'toggle',
					'viewtype' => ['iconCount','textCount','iconTextCount'],
				],	 
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'countNumTypo',
                'label' => esc_html__('Number Typography', 'theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .sharing-horizontal .tp-social-list .social-count,{{WRAPPER}} .sharing-vertical .tp-social-list .social-count',
                'separator' => 'before',
            ]
        );
        $this->add_responsive_control(
			'countNumSpace',
			[
				'label'      => esc_html__( 'Number Spacing', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%','em' ],
				'selectors'  => [
					'{{WRAPPER}} .sharing-horizontal .tp-social-list .social-count,{{WRAPPER}} .sharing-vertical .tp-social-list .social-count' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'countLblTypo',
                'label' => esc_html__('Label Typography', 'theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .sharing-horizontal .tp-social-list .count-label,{{WRAPPER}} .sharing-vertical .tp-social-list .count-label',
            ]
        );
        $this->add_responsive_control(
			'countLblSpace',
			[
				'label'      => esc_html__( 'Label Spacing', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%','em' ],
				'selectors'  => [
					'{{WRAPPER}} .sharing-horizontal  .tp-social-list .count-label,{{WRAPPER}} .sharing-vertical  .tp-social-list .count-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'socialbtnTypo',
                'label' => esc_html__('Social Button Typography', 'theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .sharing-horizontal.sharing-style-4 .tp-social-list .social-button',
                'condition' => [
					'sociallayout' => 'horizontal',
					'styleHZ' => 'style-4',
				],
            ]
        );
        $this->add_responsive_control(
			'socialbtnSpace',
			[
				'label'      => esc_html__( 'Social Button Spacing', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%','em' ],
				'selectors'  => [
					'{{WRAPPER}} .sharing-horizontal.sharing-style-4  .tp-social-list .social-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'sociallayout' => 'horizontal',
					'styleHZ' => 'style-4',
			    ],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'socialbtnBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .sharing-horizontal.sharing-style-4 .tp-social-list .social-button',
				'condition' => [
					'sociallayout' => 'horizontal',
					'styleHZ' => 'style-4',
				],	
			]
		);
		$this->add_responsive_control(
				'socialbtnBRadius',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .sharing-horizontal.sharing-style-4 .tp-social-list .social-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'sociallayout' => 'horizontal',
						'styleHZ' => 'style-4',
					],
				]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
			   'name' => 'socialbtnBG',
			   'label' => esc_html__( 'Background', 'theplus' ),
			   'types' => [ 'classic', 'gradient' ],
			   'selector' => '{{WRAPPER}} .sharing-horizontal.sharing-style-4 .tp-social-list .social-button',
			   'condition' => [
					'sociallayout' => 'horizontal',
					'styleHZ' => 'style-4',
			    ],	
			]
		);
        $this->end_controls_section();
        /* Counter Style */	
        /* Icon Style */
		$this->start_controls_section(
            'social_sharing_icon_styling',
            [
                'label' => esc_html__('Icon', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,	
                'condition' => [
					'sociallayout' => ['horizontal','vertical','toggle'],
				],
            ]
        );
        $this->add_responsive_control(
            'iconSize',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px','em','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-list .social-btn-icon' => 'font-size: {{SIZE}}{{UNIT}}',
				],
            ]
        );
        $this->add_responsive_control(
            'iconWidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Width', 'theplus'),
				'size_units' => [ 'px','em','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-list .social-btn-icon' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
        $this->add_responsive_control(
            'iconGap',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Between Gap', 'theplus'),
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
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'sociallayout' => 'toggle',
					'toggleStyle' => 'style-2',
				],
            ]
        );
        $this->end_controls_section();
        /* Icon Style */
        /* Toggle Style */
        $this->start_controls_section(
            'social_sharing_toggle_styling',
            [
                'label' => esc_html__('Toggle', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                'sociallayout' => 'toggle',
                ],	
            ]
        );
        $this->add_responsive_control(
            'toggleWidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Toggle Width', 'theplus'),
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
					'size' => 40,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .sharing-toggle.sharing-style-2 .toggle-share' => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .sharing-toggle.sharing-style-2 .tp-social-list li.tp-main-menu a.tp-share-btn' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'sociallayout' => 'toggle',
					'toggleStyle' => 'style-2',
				],
            ]
        );
        $this->add_responsive_control(
            'tglIconWidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Width', 'theplus'),
				'size_units' => [ 'px','em' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .sharing-toggle.sharing-style-1 .toggle-icon .toggle-btn' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'sociallayout' => 'toggle',
					'toggleStyle' => 'style-1',
				],
            ]
        ); 
        $this->add_responsive_control(
            'tglIconSize',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px','em' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .sharing-toggle.sharing-style-2 .tp-social-list li.tp-main-menu a.tp-share-btn,{{WRAPPER}} .sharing-toggle.sharing-style-1 .toggle-btn' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .sharing-toggle.sharing-style-2 .tp-social-list li.tp-main-menu a.tp-share-btn svg,{{WRAPPER}} .sharing-toggle.sharing-style-1 .toggle-btn svg' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}}',
				],
				'separator' => 'after',
            ]
        );
        $this->start_controls_tabs( 'Nml_Hvr_Color' );
		$this->start_controls_tab(
			'Nml_Color',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
        $this->add_control(
			'tglTitleNmlColor',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sharing-toggle.sharing-style-1 .toggle-label' => 'color: {{VALUE}};',
				],
				'condition' => [
					'sociallayout' => 'toggle',
					'toggleStyle' => 'style-1',
				],
			]
		);
		$this->add_control(
			'tglIcnNmlColor',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-social-sharing.sharing-toggle.sharing-style-2 .tp-social-list li.tp-main-menu a.tp-share-btn,{{WRAPPER}} .sharing-toggle.sharing-style-1 .toggle-icon .toggle-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-social-sharing.sharing-toggle.sharing-style-2 .tp-social-list li.tp-main-menu a.tp-share-btn svg,{{WRAPPER}} .sharing-toggle.sharing-style-1 .toggle-icon .toggle-btn svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'sociallayout' => 'toggle',
				],
			]
		);
		$this->add_control(
			'tglIcnNmlBG',
			[
				'label' => esc_html__( 'Icon Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sharing-toggle.sharing-style-1 .toggle-icon .toggle-btn' => 'background: {{VALUE}};',
				],
				'condition' => [
					'sociallayout' => 'toggle',
					'toggleStyle' => 'style-1',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
			   'name' => 'tglNmlBG',
			   'label' => esc_html__( 'Background', 'theplus' ),
			   'types' => [ 'classic', 'gradient' ],
			   'selector' => '{{WRAPPER}} .sharing-toggle.sharing-style-2 .tp-social-list li.tp-main-menu a.tp-share-btn,{{WRAPPER}} .sharing-toggle.sharing-style-1 .toggle-share',			   
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tglNmlshadow',
				'selector' => '{{WRAPPER}} .tp-social-sharing.sharing-toggle.sharing-style-2 .tp-social-list li.tp-main-menu a.tp-share-btn,
				{{WRAPPER}} .tp-social-sharing.sharing-toggle.sharing-style-1 .toggle-share',
			]
		);
        $this->end_controls_tab();
		$this->start_controls_tab(
			'Hvr_Color',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
        $this->add_control(
			'tglTitleHvrColor',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sharing-toggle.sharing-style-1 .toggle-share.menu-active .toggle-label' => 'color: {{VALUE}};',
				],
				'condition' => [
					'sociallayout' => 'toggle',
					'toggleStyle' => 'style-1',
				],
			]
		);
		$this->add_control(
			'tglIcnHvrColor',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-social-sharing.sharing-toggle.sharing-style-2 .tp-social-list.active li.tp-main-menu a.tp-share-btn,{{WRAPPER}} .sharing-toggle.sharing-style-1 .toggle-share.menu-active .toggle-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-social-sharing.sharing-toggle.sharing-style-2 .tp-social-list.active li.tp-main-menu a.tp-share-btn svg,{{WRAPPER}} .sharing-toggle.sharing-style-1 .toggle-share.menu-active .toggle-btn svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'sociallayout' => 'toggle',
				],
			]
		);
		$this->add_control(
			'tglIcnHvrBG',
			[
				'label' => esc_html__( 'Icon Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sharing-toggle.sharing-style-1 .toggle-share.menu-active .toggle-btn' => 'background: {{VALUE}};',
				],
				'condition' => [
					'sociallayout' => 'toggle',
					'toggleStyle' => 'style-1',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
			   'name' => 'tglHvrBG',
			   'label' => esc_html__( 'Background', 'theplus' ),
			   'types' => [ 'classic', 'gradient' ],
			   'selector' => '{{WRAPPER}} .sharing-toggle.sharing-style-2 .tp-social-list.active li.tp-main-menu a.tp-share-btn,{{WRAPPER}} .sharing-toggle.sharing-style-1 .toggle-share.menu-active',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tglHvrshadow',
				'selector' => '{{WRAPPER}} .tp-social-sharing.sharing-toggle.sharing-style-2 .tp-social-list.active li.tp-main-menu a.tp-share-btn,
				{{WRAPPER}} .tp-social-sharing.sharing-toggle.sharing-style-1 .toggle-share.menu-active,
				{{WRAPPER}} .tp-social-sharing.sharing-toggle.sharing-style-2 .tp-social-list li.tp-main-menu a.tp-share-btn:hover,
				{{WRAPPER}} .tp-social-sharing.sharing-toggle.sharing-style-1 .toggle-share:hover',
			]
		);
        $this->end_controls_tab();
		$this->end_controls_tabs();	
        $this->end_controls_section();
        /* Toggle Style */	
        /*Total Counter Style*/	
        $this->start_controls_section(
            'social_sharing_total_counter_styling',
            [
                'label' => esc_html__('Total Counter', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'sociallayout' => ['horizontal','vertical'],
					'displayCounter' => 'yes',
				],	
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'totalNumTypo',
                'label' => esc_html__('Number Typography', 'theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .tp-social-list .totalcount-item .total-count-number',
                'condition' => [
					'displayCounter' => 'yes',
					'shareNumber!' => '',
				],
            ]
        );
        $this->add_control(
			'totalNumColor',
			[
				'label' => esc_html__( 'Number Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-social-list .totalcount-item .total-count-number' => 'color: {{VALUE}};',
				],
				'condition' => [
					'displayCounter' => 'yes',
					'shareNumber!' => '',
				],	
				'separator' => 'after',
			]
		);
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'totalLblTypo',
                'label' => esc_html__('Label Typography', 'theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .tp-social-list .totalcount-item .total-number-label',
                'condition' => [
					'displayCounter' => 'yes',
					'shareLabel!' => '',
				],
            ]
        );
        $this->add_control(
			'totalLblColor',
			[
				'label' => esc_html__( 'Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-social-list .totalcount-item .total-number-label' => 'color: {{VALUE}};',
				],
				'condition' => [
					'displayCounter' => 'yes',
					'shareLabel!' => '',
				],	
			]
		);
		 $this->add_responsive_control(
			'totalSpace',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Space Between', 'theplus'),
				'size_units' => [ 'px','em','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-list .totalcount-item .total-number-label' => ' margin-top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'displayCounter' => 'yes',
					'shareNumber!' => '',
					'shareLabel!' => '',
				],	
				'separator' => 'before',
			]
		);
	    $this->end_controls_section();
	    /*Total Counter Style*/
		/*Background Style*/	
        $this->start_controls_section(
            'social_sharing_bg_styling',
            [
                'label' => esc_html__('Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
         $this->add_responsive_control(
            'bgWidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px','em','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-list li.tp-social-menu' => 'width: {{SIZE}}{{UNIT}}',
				],				
            ]
        );
        $this->add_responsive_control(
            'bgHeight',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'theplus'),
				'size_units' => [ 'px','em' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-list li.tp-social-menu' => 'height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-social-list .totalcount' => 'height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
        $this->add_responsive_control(
			'iconSpaceBtwn',
			[
				'label'      => esc_html__( 'Icon Space Between', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-list .tp-social-menu,{{WRAPPER}} .tp-social-list .totalcount' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'Nml_Hvr_Border' );
		$this->start_controls_tab(
			'Nml_Border',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'bgNmlBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-list .share-btn',
			]
		);
		$this->add_responsive_control(
			'bgNmlBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-list .share-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);
        $this->end_controls_tab();
		$this->start_controls_tab(
			'Hvr_Border',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),	
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'bgHvrBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-list .share-btn:hover',
			]
		);
		$this->add_responsive_control(
			'bgHvrBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-list .share-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        $this->end_controls_tab();
		$this->end_controls_tabs();	
        $this->end_controls_section();
        /*Background Style*/		
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$uid_socishre=uniqid("tp-ss");
		$socialSharing = (!empty($settings['socialSharing'])) ? $settings['socialSharing'] : [];
		$sociallayout = (!empty($settings['sociallayout'])) ? $settings['sociallayout'] : 'horizontal';
		$column = (!empty($settings['column'])) ? $settings['column'] : 'auto';
		$styleHZ = (!empty($settings['styleHZ'])) ? $settings['styleHZ'] : 'style-1 ';
		$style = (!empty($settings['style'])) ? $settings['style'] : 'style-1 ';
		$toggleStyle = (!empty($settings['toggleStyle'])) ? $settings['toggleStyle'] : 'style-1';
		$displayCounter = (!empty($settings['displayCounter'])) ? $settings['displayCounter'] : false;
		$viewtype = (!empty($settings['viewtype'])) ? $settings['viewtype'] : 'iconText';
		$hDirection = (!empty($settings['hDirection'])) ? $settings['hDirection'] : 'top';
		$shareNumber = (!empty($settings['shareNumber'])) ? $settings['shareNumber'] : '';
		$shareLabel = (!empty($settings['shareLabel'])) ? $settings['shareLabel'] : '';
		$iconStore = (!empty($settings['iconStore'])) ? $settings['iconStore'] : '';
		$toggleWidth = (!empty($settings['toggleWidth']['size'])) ? $settings['toggleWidth']['size'] : 40;
		$iconGap = (!empty($settings['iconGap']['size'])) ? $settings['iconGap']['size'] : 0;
		$toggleText = !empty($settings['toggleText']) ? $settings['toggleText'] : '';

		$p = 1;
		$selectStyle=$direction=$getCounter=$columnAuto=$loopStyle='';
		if($sociallayout!='toggle' && $sociallayout!='horizontal'){
			$selectStyle = $style ;
		}else if($sociallayout=='toggle'){
			$selectStyle = $toggleStyle ;
		}else if($sociallayout=='horizontal'){
			$selectStyle = $styleHZ ;
		}		
		if($sociallayout=='toggle' && $toggleStyle=='style-2'){
			if($hDirection == 'left'){
				$direction .= 'right';
			}else if($hDirection == 'right'){
				$direction .= 'left';
			}else if($hDirection == 'top'){
				$direction .= 'bottom';
			}else if($hDirection == 'bottom'){
				$direction .= 'top';
			}
		}		
		if($sociallayout=='horizontal' && $column!='auto'){
			$columnAuto = 'column-auto';
		}
		$post_id = get_the_ID();
		$get_link = get_the_permalink($post_id);
		$get_title = get_the_title($post_id);
		$media_url = get_the_post_thumbnail_url($post_id, 'full');
		$description = wp_strip_all_tags( get_the_excerpt(), true );
		$ShareLink = [
			'fab fa-amazon' => "https://www.amazon.com/gp/wishlist/static-add?u=".$get_link,
			'fab fa-digg' => "https://digg.com/submit?url=".$get_link,
			'fab fa-delicious' => "https://del.icio.us/save?url=".$get_link."&title=".$get_title,
			'far fa-envelope' => "mailto:?subject=".$get_title."&body=".$description."\n".$get_link,
			'fab fa-facebook-f' => "https://www.facebook.com/sharer.php?u=".$get_link,
			'fab fa-facebook-messenger' => "fb-messenger://share/?link=".$get_link,
			'fab fa-get-pocket' => "https://getpocket.com/save?url=".$get_link."&title=".$get_title,
			'fab fa-linkedin-in' => "https://www.linkedin.com/shareArticle?mini=true&url=".$get_link."&title=".$get_title,
			'fab fa-odnoklassniki' => "https://connect.ok.ru/offer?url=".$get_link."&title=".$get_title."&imageUrl=".$media_url,
			'fab fa-pinterest-p' => "https://www.pinterest.com/pin/create/button/?url=".$get_link."&media=".$media_url,
			'fab fa-reddit' => "https://reddit.com/submit?url=".$get_link."&title=".$get_title,
			'fab fa-skype' => "https://web.skype.com/share?url=".$get_link,
			'fab fa-snapchat-ghost' => "https://www.snapchat.com/scan?attachmentUrl=".$get_link,
			'fab fa-stumbleupon' => "https://www.stumbleupon.com/submit?url=".$get_link."&title=".$get_title,
			'fab fa-telegram-plane' => "https://telegram.me/share/url?url=".$get_link."&text=".$get_title,
			'fab fa-tumblr' => "https://tumblr.com/share/link?url=".$get_link,
			'fab fa-twitter' => "https://twitter.com/intent/tweet?text=".$get_title.' '.$get_link,
			'fab fa-viber' => "viber://forward?text=".$get_title." ".$get_link,
			'fab fa-vk' => "https://vkontakte.ru/share.php?url=".$get_link."&title=".$get_title."&description=".$description."&image=".$media_url,
			'fab fa-weibo' => "https://service.weibo.com/share/share.php?url=".$get_link."&title=".$get_title."&pic=".$media_url,
			//'fab fa-whatsapp' => "https://api.whatsapp.com/send?text=*".$get_title."*\n".$description."\n".$get_link,
			'fab fa-whatsapp' => "https://api.whatsapp.com/send?text=".$get_title."-\n".$get_link,
			'fab fa-xing' => "https://www.xing.com/app/user?op=share&url=".$get_link,
		];				
		$output = '<div class="tp-social-sharing sharing-'.esc_attr($sociallayout).' sharing-'.esc_attr($selectStyle).' tp-widget-'.esc_attr($uid_socishre).'">';
			$lz4 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['tglNmlBG_image'],$settings['tglHvrBG_image']) : '';
		    if($sociallayout=='toggle' && $toggleStyle=='style-1'){				
				$output .= '<div class="toggle-share '.esc_attr($lz4).'">';
					$output .= '<div class="toggle-icon">';
					 if(!empty($toggleText)){
						$output .= '<span class="toggle-label">'.esc_html($toggleText).'</span>'; 
					 }					  
					  $output .= '<div class="toggle-btn">';
					   if($iconStore){
							  ob_start();
							 \Elementor\Icons_Manager::render_icon( $iconStore, [ 'aria-hidden' => 'true' ]);
							 $output .= ob_get_contents();
							 ob_end_clean();						
						 }
					  $output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
			}
			$output .= '<ul class="tp-social-list '.esc_attr($columnAuto).' '.esc_attr($hDirection).' ">';
				if(!empty($displayCounter) && ($sociallayout=='horizontal' || $sociallayout=='vertical')){					
					$output .= '<li class="totalcount ">';
					$output .= '<span class="totalcount-item">';
						$output .= '<span class="total-count-number">'.$shareNumber.'</span>';
						$output .= '<span class="total-number-label">'.$shareLabel.'</span>';
						$output .= '</span>';
					$output .= '</li>';
				}
				if($sociallayout=='toggle' && $toggleStyle=='style-2'){
					$output .= '<li class="tp-main-menu">';						
						$output .= '<a class="tp-share-btn '.esc_attr($lz4).'">';
						 if($iconStore){
							  ob_start();
							 \Elementor\Icons_Manager::render_icon( $iconStore, [ 'aria-hidden' => 'true' ]);
							 $output .= ob_get_contents();
							 ob_end_clean();						
						 }
						$output .= '</a>';
					$output .= '</li>';
				}
				if(!empty($socialSharing)){
					$leftValue = 0;
					foreach ( $socialSharing as $network ) {
						$p++;
						$leftValue=$leftValue+$toggleWidth+$iconGap;
						$output .= '<li class="tp-social-menu  elementor-repeater-item-' . $network['_id'] . ' ">';							
							$getCustomLink = '';
							if(!empty($network['customURL']) && !empty($network['customLink']['url'])){
								$getCustomLink = $network['customLink']['url'] ;
							}else{
								$iconname= $network['socialNtwk'];
								$getCustomLink= (isset($ShareLink[$iconname])) ? $ShareLink[$iconname] : '#';
							}
							$target=$nofollow=$tf=$nf='';
							if(!empty($getCustomLink)){
								$target = (!empty($network['customLink']['target'])) ? '_blank' : ' ';
								$nofollow = (!empty($network['customLink']['nofollow'])) ? 'nofollow' : ' ';
								$tf ='target="'.esc_attr($target).'"';
								$nf ='rel="'.$nofollow.'"';
							}
							
							$lz2 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($network['normalBG_image'],$network['hoverBG_image']) : '';
							if(!empty($network['socialNtwk']) && $network['socialNtwk']=='tpmsp'){
								$output .= '<script>
								jQuery( document ).ready(function() {
								const share = document.getElementById("tp-mp-main-wrap1");
								if ( navigator.share ) {	
									jQuery( share ).on("click",function() {
										navigator.share({
										   title: document.title,
											text: document.title,
											url: window.location.href
										})
									});
								}
								});
								</script>';								
								$output .= '<div id="tp-mp-main-wrap1" class="share-btn '.$settings['style1Align'].' '.esc_attr($lz2).'">';
								
								$tpmsp_open = !empty($network['tpmsp_open']['size']) ? $network['tpmsp_open']['size'] : '';
								$output .='<style>.tp-widget-'.esc_attr($uid_socishre).' div#tp-mp-main-wrap1{display:none !important;}
								@media (max-width: '.esc_attr($tpmsp_open).'px){
									.tp-widget-'.esc_attr($uid_socishre).' div#tp-mp-main-wrap1{display:flex !important;}}</style>';
							}else{
								$output .= '<a href="'.$getCustomLink.'" '.$tf.' '.$nf.' class="share-btn '.$settings['style1Align'].' '.esc_attr($lz2).'">';
							}
							
								if($viewtype!='text' && $viewtype!='textCount'){
									$lz1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($network['iconNmlBG_image'],$network['iconHvrBG_image']) : '';
									$output .= '<span class="social-btn-icon '.esc_attr($lz1).'">';
										$mpimg ='';
										if(!empty($network['socialNtwk']) && $network['socialNtwk']=='tpmsp' && $network['tpmspIcons']){
											ob_start();
											\Elementor\Icons_Manager::render_icon( $network['tpmspIcons'], [ 'aria-hidden' => 'true' ]);
											$mpimg = ob_get_contents();
											ob_end_clean();
											
											$output .= $mpimg;
										}else{
											$output .= '<i aria-hidden="true" class="'.$network['socialNtwk'].'"></i>';
										}										
									$output .= '</span>';
								}
								if($sociallayout=='horizontal' || $sociallayout=='vertical'){
									if(!empty($network['title']) && $viewtype!='icon' && $viewtype!='iconCount'){
										$output .= '<span class="social-btn-title">'.$network['title'].'</span>';
									}
									if($viewtype=='iconCount' || $viewtype=='textCount' || $viewtype=='iconTextCount'){
										$output .= '<div class="social-count-number">';
											if(!empty($network['countNumber'])){
												$output .= '<span class="social-count">'.$network['countNumber'].'</span>';
											}
											if(!empty($network['countLabel'])){
												$output .= '<span class="count-label">'.$network['countLabel'].'</span>';
											}
											if(!empty($network['socialBtn'])){
												$lz3 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['socialbtnBG_image']) : '';
												$output .= '<span class="social-button '.esc_attr($lz3).'">'.$network['socialBtn'].'</span>';
											}
										$output .= '</div>';
									}
								}
							if(!empty($network['socialNtwk']) && $network['socialNtwk']=='tpmsp'){
								$output .= '</div>';
							}else{
								$output .= '</a>';
							}							
							
						$output .= '</li>';
						if($sociallayout=='toggle' && $toggleStyle=='style-2'){
							$loopStyle .= '.tp-widget-'.$uid_socishre.'.sharing-toggle.sharing-style-2 .tp-social-list.'.$hDirection.'.active li:nth-child('.$p.'){ '.$direction.': '.$leftValue.'px;}';
						}
					}
				}
			$output .= '</ul>';
		$output .= '</div>';
		if(!empty($loopStyle)){
		  $output .= '<style>'.$loopStyle.'</style>';
		}
		echo $output;    
    }
    protected function content_template() {
		
	}
}