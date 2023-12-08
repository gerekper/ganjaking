<?php 
/*
Widget Name: Coupon Code
Description: Coupon Code.
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

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Coupon_Code extends Widget_Base {
		
	public function get_name() {
		return 'tp-coupon-code';
	}

    public function get_title() {
        return esc_html__('Coupon Code', 'theplus');
    }

    public function get_icon() {
        return 'fa- tp-coupancode-icon theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }

    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'couponType',
			[
				'label' => esc_html__( 'Coupon Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'standard',
				'options' => [
					'standard'  => esc_html__( 'Standard', 'theplus' ),
					'peel' => esc_html__( 'Peel', 'theplus' ),
					'scratch' => esc_html__( 'Scratch', 'theplus' ),
					'slideOut' => esc_html__( 'Slide Out', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'standardStyle',
			[
				'label' => esc_html__( 'Standard Style', 'theplus' ),
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
					'couponType' => 'standard',
				],
			]
		);
		$this->add_control(
			'couponText',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Show Code', 'theplus' ),	
				'dynamic' 		=> [ 'active' => true ],
				'condition' => [
					'couponType' => 'standard',
				],				
			]
		);
		$this->add_control(
			'redirectLink',
			[
				'label' => __( 'Redirect Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'theplus' ),
				'show_external' => true,
				'dynamic' 		=> [ 'active' => true ],
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_responsive_control(
			'standardCntAlign',
			[
				'label' => esc_html__( 'Content Alignment', 'theplus' ),
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
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .coupon-code-inner.style-1 .coupon-text,
					{{WRAPPER}} .tp-coupon-code .coupon-code-inner.style-2 .coupon-text,
					{{WRAPPER}} .tp-coupon-code .coupon-code-inner.style-3 .coupon-text,
					{{WRAPPER}} .tp-coupon-code .coupon-code-inner.style-4 .coupon-text,
					{{WRAPPER}} .tp-coupon-code .coupon-code-inner.style-5 .coupon-text,{{WRAPPER}} .copy-style-1 span.full-code-text,{{WRAPPER}} .copy-style-2 span.full-code-text,{{WRAPPER}} .copy-style-3 span.full-code-text,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text' => 'text-align: {{VALUE}};justify-content: {{VALUE}}',
				],
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_responsive_control(
			'standardAlign',
			[
				'label' => esc_html__( 'Box Alignment', 'theplus' ),
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
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code,{{WRAPPER}} .tp-coupon-code .coupon-code-outer' => 'text-align: {{VALUE}};text-align: -webkit-{{VALUE}};',
				],
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_control(
			'StndModalCpCd_opts',
			[
				'label' => esc_html__( 'Copy Code', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'couponType' => 'standard',
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'copyCodeStyle',
			[
				'label' => esc_html__( 'Copy Code Style', 'theplus' ),
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
					'couponType' => 'standard',
				],
			]
		);
		$this->add_control(
			'couponCode',
			[
				'label' => esc_html__( 'Code', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'X246-17GT-OL57', 'theplus' ),	
				'dynamic' 		=> [ 'active' => true ],
				'condition' => [
					'couponType' => 'standard',
				],				
			]
		);
		$this->add_control(
			'actionType',
			[
				'label' => esc_html__( 'Select Action', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'click',
				'options' => [
					'click'  => esc_html__( 'Click', 'theplus' ),
					'popup' => esc_html__( 'Popup', 'theplus' ),
				],
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_control(
            'hideLink',
            [
				'label' => esc_html__( 'Hide Link', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'description' => esc_html__( 'Note : If you enable this, It will replace your URL text with masked text and open single or multiple URLs on click as per your below configuration.', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'click',
				],
			]
        );
		$this->add_control(
			'hideLinkMaskText',
			[
				'label' => esc_html__( 'Link Masking Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => ['active' => true,],
				'default' => '#',
				'placeholder' => esc_html__( 'Enter Masking Text', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'click',
					'hideLink' => 'yes',
				],
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'LinkMaskLabel',
			[
				'label' => esc_html__( 'Label', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => ['active' => true,],
				'default' => esc_html__( 'Label', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Label', 'theplus' ),
			]
		);
		$repeater->add_control(
			'LinkMaskLink',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'theplus' ),
				'show_external' => true,
				'default' => ['url' => '#',],
				'dynamic' => ['active'=> true,],				
			]
		);
		$this->add_control(
			'LinkMaskList',
			[
				'label' => esc_html__( 'Open URLs : Single/Multiple', 'theplus' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),			
				'default' => [
					[						
						'LinkMaskLabel' => 'Wordpress',
					],
				],
				'title_field' => '{{{ LinkMaskLabel }}}',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'click',
					'hideLink' => 'yes',
				],
			]
		);
		$this->add_control(
            'tabReverse',
            [
				'label' => esc_html__( 'Tab Reverse', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'description' => esc_html__( 'Note : If you choose this option Link will open in existing tab and current website will be open in new tab.', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
        );
		$this->add_control(
            'copLoop',
            [
				'label' => esc_html__( 'Loop', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',				
				'condition' => [
					'couponType' => 'standard',
				],
			]
        );
		$this->add_control(
			'popupBgImage',
			[
				'type' => Controls_Manager::MEDIA,
				'label' => esc_html__('Popup Background Image', 'theplus'),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'copLoop' => 'yes',
				],
			]
		);
		$this->add_control(
			'popupTitle',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Here is your coupon code', 'theplus' ),	
				'dynamic' 		=> [ 'active' => true ],
				'label_block' => true,
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
			]
		);
		$this->add_control(
			'popupDesc',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Use code on site', 'theplus' ),	
				'dynamic' 		=> [ 'active' => true ],
				'label_block' => true,
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
			]
		);
		$this->add_control(
			'copyBtnText',
			[
				'label' => esc_html__( 'Copy Button', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Copy Code', 'theplus' ),	
				'dynamic' 		=> [ 'active' => true ],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
			]
		);
		$this->add_control(
			'afterCopyText',
			[
				'label' => esc_html__( 'After Copy', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Copied!', 'theplus' ),	
				'dynamic' 		=> [ 'active' => true ],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
			]
		);
		$this->add_control(
			'visitBtnText',
			[
				'label' => esc_html__( 'Visit Button', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Visit Site', 'theplus' ),	
				'dynamic' 		=> [ 'active' => true ],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
			]
		);
		$this->add_control(
			'modelClsIcon',
			[
				'label' => esc_html__( 'Close Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,			
				'default' => [
					'value' => 'fa fa-times',
					'library' => 'solid',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'standardCdAlign',
			[
				'label' => esc_html__( 'Code Alignment', 'theplus' ),
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
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .coupon-code-outer' => 'text-align: {{VALUE}}',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
            'scratchWidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code.coupon-code-scratch,{{WRAPPER}} .tp-coupon-code.coupon-code-slideOut,{{WRAPPER}} .tp-coupon-code.coupon-code-peel' => 'max-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'couponType!' => 'standard',
				],
            ]
        );
        $this->add_responsive_control(
            'scratchHeight',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code.coupon-code-scratch,{{WRAPPER}} .tp-coupon-code.coupon-code-slideOut,{{WRAPPER}} .tp-coupon-code.coupon-code-peel' => 'height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'couponType!' => 'standard',
				],
            ]
        );
        $this->add_responsive_control(
            'fillPercent',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Fill Percent For Reveal', 'theplus'),
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
					'size' => 70,
				],
				'render_type' => 'ui',
				'condition' => [
					'couponType' => 'scratch',
				],
            ]
        );
        $this->add_control(
			'slideDirection',
			[
				'label' => esc_html__( 'Slide Out Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => esc_html__( 'Left', 'theplus' ),
					'right' => esc_html__( 'Right', 'theplus' ),
					'top' => esc_html__( 'Top', 'theplus' ),
					'bottom' => esc_html__( 'Bottom', 'theplus' ),
				],
				'condition' => [
					'couponType' => 'slideOut',
				],
			]
		);
		$this->add_control(
            'slideDirectionhint',
            [
				'label' => esc_html__( 'Direction Hint Icon', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'couponType' => 'slideOut',
				],
			]
        );
		$this->end_controls_section();
		/*Content End*/
		/*Front Side Start*/
		$this->start_controls_section(
			'content_front_side_section',
			[
				'label' => esc_html__( 'Front Side', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'couponType!' => 'standard',
				],
			]
		);
		$this->add_control(
			'frontContentType',
			[
				'label' => esc_html__( 'Content Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'theplus' ),
					'template' => esc_html__( 'Template', 'theplus' ),
				],
				'condition' => [
					'couponType!' => 'standard',
				],
			]
		);
		$this->add_control(
			'frontContent',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Front Content', 'theplus' ),	
				'dynamic' 		=> [ 'active' => true ],
				'label_block' => true,
				'condition' => [
					'couponType!' => 'standard',
					'frontContentType' => 'default',
				],				
			]
		);
		$this->add_control(
			'frontContentTag',
			[
				'label' => esc_html__( 'Content Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => theplus_get_tags_options(),				
				'condition' => [
					'couponType!' => 'standard',
					'frontContentType' => 'default',
				],
			]
		);
		$this->add_control(
			'frontTemp',
			[
				'label'       => esc_html__( 'Template', 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => theplus_get_templates(),
				'label_block' => 'true',
				'condition' => [
					'couponType!' => 'standard',
					'frontContentType' => 'template',
				],
			]
		);
		$this->end_controls_section();
		/*Front Side End*/
		/*Back Side Start*/
		$this->start_controls_section(
			'content_back_side_section',
			[
				'label' => esc_html__( 'Back Side', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'couponType!' => 'standard',
				],
			]
		);
		$this->add_control(
			'backContentType',
			[
				'label' => esc_html__( 'Content Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'theplus' ),
					'template' => esc_html__( 'Template', 'theplus' ),
				],
				'condition' => [
					'couponType!' => 'standard',
				],
			]
		);
		$this->add_control(
			'backTitle',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Back Title', 'theplus' ),	
				'dynamic' 		=> [ 'active' => true ],
				'label_block' => true,
				'condition' => [
					'couponType!' => 'standard',
					'backContentType' => 'default',
				],				
			]
		);
		$this->add_control(
			'backTitleTag',
			[
				'label' => esc_html__( 'Title Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => theplus_get_tags_options(),				
				'condition' => [
					'couponType!' => 'standard',
					'backContentType' => 'default',
				],
			]
		);
		$this->add_control(
			'backDesc',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Back Description', 'theplus' ),	
				'dynamic' 		=> [ 'active' => true ],
				'label_block' => true,
				'condition' => [
					'couponType!' => 'standard',
					'backContentType' => 'default',
				],				
			]
		);
		$this->add_control(
			'backTemp',
			[
				'label'       => esc_html__( 'Template', 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => theplus_get_templates(),
				'label_block' => 'true',
				'condition' => [
					'couponType!' => 'standard',
					'backContentType' => 'template',
				],
			]
		);
		$this->end_controls_section();
		/*Back Side End*/
		/*Extra Options Start*/
		$this->start_controls_section(
			'content_extra_scroll_section',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'ccd_popup_width',
			[
				'label' => esc_html__( 'Popup Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 2,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'.ccd-main-modal' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_control(
            'CdScrollOn',
            [
				'label' => esc_html__( 'On Scroll', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
        );
        $this->add_responsive_control(
            'CdScrollHgt',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'render_type' => 'ui',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'CdScrollOn'    => 'yes',
				],
            ]
        );
		$this->end_controls_section();
		/*Extra Options End*/
		/*Standard Style Start*/
		$this->start_controls_section(
            'section_standard_styling',
            [
                'label' => esc_html__('Standard', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'couponType' => 'standard',
				],				
            ]
        );
        $this->add_control(
			'Stndbtn_opts',
			[
				'label' => esc_html__( 'Button', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_responsive_control(
			'buttonPadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .coupon-btn-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'buttonTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .coupon-code-inner .coupon-text',
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_responsive_control(
            'buttonWidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width (%)', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .coupon-code-inner,{{WRAPPER}} .tp-coupon-code .copy-style-1 span.full-code-text,{{WRAPPER}} .tp-coupon-code .copy-style-2 span.full-code-text,{{WRAPPER}} .tp-coupon-code .copy-style-3 span.full-code-text,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text' => 'width: {{SIZE}}%',
				],
				'condition' => [
					'couponType' => 'standard',
				],
            ]
        );
        $this->add_control(
			'ArWBdrColor',
			[
				'label' => esc_html__( 'Arrow Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .coupon-code-inner.style-1::before' => 'border-left-color: {{VALUE}};',
					'{{WRAPPER}} .coupon-code-inner.style-1::after' => 'border-right-color: {{VALUE}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => 'style-1',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_Stndbtn' );
		$this->start_controls_tab(
			'tab_Stndbtn_Nml',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_control(
            'buttonNColor',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .coupon-code-inner .coupon-text' => 'color:{{VALUE}};',
                ],
                'condition' => [
					'couponType' => 'standard',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'btns2NmlBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .coupon-code-inner.style-2 .coupon-text',
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-2'],
				],
			]
		);
		$this->add_control(
			'btns2BdrNmlClr',
			[
				'label'  => esc_html__( 'Border Left Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .coupon-code-inner.style-2 .coupon-text::after' => 'border-left-color: {{VALUE}}',
				],
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-2'],
				],
			]
		);
		$this->add_control(
			'btnScratchNColor',
			[
				'label'  => esc_html__( 'Border Bottom Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .coupon-code-inner.style-5 .coupon-btn-link::after' => 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-5'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'buttonNmlBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .coupon-code-inner.style-1 .coupon-text,{{WRAPPER}} .coupon-code-inner.style-3 .coupon-text,{{WRAPPER}} .coupon-code-inner.style-4 .coupon-btn-link,{{WRAPPER}} .coupon-code-inner.style-5 .coupon-btn-link',
				'condition' => [
					'couponType' => 'standard',
					'standardStyle!' => ['style-2'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'btnNmlBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .coupon-btn-link,{{WRAPPER}} .coupon-code-inner.style-4 .coupon-btn-link,{{WRAPPER}} .coupon-code-inner.style-5 .coupon-btn-link',
			]
		);
		$this->add_responsive_control(
			'btnNmlBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .coupon-btn-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'btnNBShadow',
				'selector' => '{{WRAPPER}} .coupon-btn-link',				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_Stndbtn_Hvr',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_control(
            'buttonHColor',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .coupon-code-inner .coupon-btn-link:hover .coupon-text' => 'color:{{VALUE}};',
                ],
                'condition' => [
					'couponType' => 'standard',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'btns2HvrBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .coupon-code-inner.style-2 .coupon-btn-link:hover .coupon-text',
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-2'],
				],
			]
		);
		$this->add_control(
			'btns2BdrHvrClr',
			[
				'label'  => esc_html__( 'Border Left Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .coupon-code-inner.style-2 .coupon-btn-link:hover .coupon-text::after' => 'border-left-color: {{VALUE}}',
				],
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-2'],
				],
			]
		);
		$this->add_control(
			'btnScratchHColor',
			[
				'label'  => esc_html__( 'Border Bottom Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .coupon-code-inner.style-5 .coupon-btn-link:hover::after' => 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-5'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'buttonHvrBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .coupon-code-inner.style-1 .coupon-btn-link:hover .coupon-text,{{WRAPPER}} .coupon-code-inner.style-3 .coupon-btn-link:hover .coupon-text,{{WRAPPER}} .coupon-code-inner.style-4 .coupon-btn-link:hover,{{WRAPPER}} .coupon-code-inner.style-5 .coupon-btn-link:hover',
				'condition' => [
					'couponType' => 'standard',
					'standardStyle!' => ['style-2'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'btnHvrBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .coupon-btn-link:hover,{{WRAPPER}} .coupon-code-inner.style-4 .coupon-btn-link:hover,{{WRAPPER}} .coupon-code-inner.style-5 .coupon-btn-link:hover',
			]
		);
		$this->add_responsive_control(
			'btnHvrBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .coupon-btn-link:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'btnHBShadow',
				'selector' => '{{WRAPPER}} .coupon-btn-link:hover',				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'StndIcon_opts',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-4','style-5'],
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'btnIconWidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .coupon-code-inner.style-4 .coupon-icon,{{WRAPPER}} .coupon-code-inner.style-5 .coupon-icon' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-4','style-5'],
				],
            ]
        );
        $this->add_responsive_control(
            'btnIconSize',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .coupon-code-inner.style-4 .coupon-icon,{{WRAPPER}} .coupon-code-inner.style-5 .coupon-icon' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-4','style-5'],
				],
            ]
        );
        $this->start_controls_tabs( 'tabs_StndIcon' );
		$this->start_controls_tab(
			'tab_StndIcon_Nml',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-4','style-5'],
				],
			]
		);
		$this->add_control(
            'btnIconNColor',
            [
                'label' => esc_html__('Icon Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .coupon-code-inner.style-4 .coupon-icon,{{WRAPPER}} .coupon-code-inner.style-5 .coupon-icon' => 'color:{{VALUE}};',
                ],
                'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-4','style-5'],
				],
            ]
        );
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'btnIconNmlBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .coupon-code-inner.style-4 .coupon-icon,{{WRAPPER}} .coupon-code-inner.style-5 .coupon-icon',
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-4','style-5'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'btnIconNmlBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .coupon-code-inner.style-4 .coupon-icon,{{WRAPPER}} .coupon-code-inner.style-5 .coupon-icon',
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-4','style-5'],
				],
			]
		);
		$this->add_responsive_control(
			'btnIconNmlBdrad',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .coupon-code-inner.style-4 .coupon-icon,{{WRAPPER}} .coupon-code-inner.style-5 .coupon-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-4','style-5'],
				],	
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_StndIcon_Hvr',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-4','style-5'],
				],
			]
		);
		$this->add_control(
            'btnIconHColor',
            [
                'label' => esc_html__('Icon Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .coupon-code-inner.style-4 .coupon-btn-link:hover .coupon-icon,{{WRAPPER}} .coupon-code-inner.style-5 .coupon-btn-link:hover .coupon-icon' => 'color:{{VALUE}};',
                ],
                'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-4','style-5'],
				],
            ]
        );
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'btnIconHvrBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .coupon-code-inner.style-4 .coupon-btn-link:hover .coupon-icon,{{WRAPPER}} .coupon-code-inner.style-5 .coupon-btn-link:hover .coupon-icon',
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-4','style-5'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'btnIconHvrBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .coupon-code-inner.style-4 .coupon-btn-link:hover .coupon-icon,{{WRAPPER}} .coupon-code-inner.style-5 .coupon-btn-link:hover .coupon-icon',
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-4','style-5'],
				],
			]
		);
		$this->add_responsive_control(
			'btnIconHvrBdrad',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .coupon-code-inner.style-4 .coupon-btn-link:hover .coupon-icon,{{WRAPPER}} .coupon-code-inner.style-5 .coupon-btn-link:hover .coupon-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'couponType' => 'standard',
					'standardStyle' => ['style-4','style-5'],
				],	
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'StndCpyCd_opts',
			[
				'label' => esc_html__( 'Copy Code', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'couponType' => 'standard',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'cCodePadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .full-code-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cCodeTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-coupon-code span.full-code-text',
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_responsive_control(
            'cCodebuttonWidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width (%)', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .copy-style-1 .coupon-code-outer span.full-code-text,{{WRAPPER}} .tp-coupon-code .copy-style-2 .coupon-code-outer span.full-code-text,{{WRAPPER}} .tp-coupon-code .copy-style-3 .coupon-code-outer span.full-code-text,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
            ]
        );
        $this->add_control(
			'CodeArwBdrColor',
			[
				'label' => esc_html__( 'Arrow Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .copy-style-1 span.full-code-text::before' => 'border-left-color: {{VALUE}}',
					'{{WRAPPER}} .copy-style-1 span.full-code-text::after' => 'border-right-color: {{VALUE}}',
				],
				'condition' => [
					'couponType' => 'standard',
					'copyCodeStyle' => 'style-1',
					'actionType' => 'click',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_StndCpyCd' );
		$this->start_controls_tab(
			'tab_StndCpyCd_Nml',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_control(
            'cCodeNColor',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-coupon-code span.full-code-text' => 'color:{{VALUE}};',
                ],
                'condition' => [
					'couponType' => 'standard',
				],
            ]
        );
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'cCodeNmlBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-coupon-code span.full-code-text',
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cCodeNmlBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-coupon-code span.full-code-text',
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_responsive_control(
			'cCodeNmlBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-coupon-code span.full-code-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'couponType' => 'standard',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'cCodeNBShadow',
				'selector' => '{{WRAPPER}} .tp-coupon-code span.full-code-text',
				'condition' => [
					'couponType' => 'standard',
				],				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_StndCpyCd_Hvr',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_control(
            'cCodeHColor',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-coupon-code:hover span.full-code-text' => 'color:{{VALUE}};',
                ],
                'condition' => [
					'couponType' => 'standard',
				],
            ]
        );
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'cCodeHvrBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-coupon-code:hover span.full-code-text',
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cCodeHvrBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-coupon-code:hover span.full-code-text',
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_responsive_control(
			'cCodeHvrBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-coupon-code:hover span.full-code-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'couponType' => 'standard',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'cCodeHBShadow',
				'selector' => '{{WRAPPER}} .tp-coupon-code:hover span.full-code-text',
				'condition' => [
					'couponType' => 'standard',
				],				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
        $this->end_controls_section();
		/*Standard Style End*/
		/*Popup Modal Box Close Icon Style Start*/
		$this->start_controls_section(
            'section_modalboxCIcn_styling',
            [
                'label' => esc_html__('Popup Close Icon', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
            ]
        );
        $this->add_responsive_control(
			'cIconPadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'cIconMargin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
            'cIconWidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
            ]
        );
        $this->add_responsive_control(
            'cIconSize',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
            ]
        );
        $this->start_controls_tabs( 'tabs_StndClsIcon' );
		$this->start_controls_tab(
			'tab_StndClsIcon_Nml',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_control(
            'cIconNColor',
            [
                'label' => esc_html__('Icon Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn svg' => 'fill:{{VALUE}};',
                ],
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
            ]
        );
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'cIconNmlBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cIconNmlBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'cIconNmlBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'cIconNBShadow',
				'selector' => '{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_StndClsIcon_Hvr',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_control(
            'cIconHColor',
            [
                'label' => esc_html__('Icon Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn:hover' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn:hover svg' => 'fill:{{VALUE}};',
                ],
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
            ]
        );
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'cIconHvrBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn:hover',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cIconHvrBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn:hover',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'cIconHvrBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'cIconHBShadow',
				'selector' => '{{WRAPPER}} .tp-coupon-code .tp-ccd-closebtn:hover',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Popup Modal Box Close Icon Style End*/
		/*Popup Modal Box Title/Desc Style Start*/
		$this->start_controls_section(
            'section_modal_ttl_desc_styling',
            [
                'label' => esc_html__('Popup Title/Description', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
            ]
        );
        $this->add_responsive_control(
			'TtldescPadding',
			[
				'label' => esc_html__( 'Content Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .popup-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'TtldescMargin',
			[
				'label' => esc_html__( 'Content Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .popup-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_control(
			'StndMdlTtl_opts',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'titlePadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .popup-content .content-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'titleTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .popup-content .content-title',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_StndMdlTtl' );
		$this->start_controls_tab(
			'tab_StndMdlTtl_Nml',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_control(
            'titleNColor',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .popup-content .content-title' => 'color:{{VALUE}};',
                ],
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'titleNmlBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .popup-content .content-title',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'titleNmlBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .popup-content .content-title',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'titleNmlBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .popup-content .content-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'titleNmlBsw',
				'selector' => '{{WRAPPER}} .popup-content .content-title',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_StndMdlTtl_Hvr',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_control(
            'titleHColor',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .popup-content .content-title:hover' => 'color:{{VALUE}};',
                ],
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'titleHvrBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .popup-content .content-title:hover',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'titleHvrBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .popup-content .content-title:hover',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'titleHvrBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .popup-content .content-title:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'titleHvrBsw',
				'selector' => '{{WRAPPER}} .popup-content .content-title:hover',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'StndMdlDesc_opts',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'descPadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .popup-content .content-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'descAbvSpace',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Above Space', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .popup-content .content-desc' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
			]
		);	
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'descTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .popup-content .content-desc',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_StndMdlDesc' );
		$this->start_controls_tab(
			'tab_StndMdlDesc_Nml',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_control(
            'descNColor',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .popup-content .content-desc' => 'color:{{VALUE}};',
                ],
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'descNmlBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .popup-content .content-desc',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'descNmlBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .popup-content .content-desc',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'descNmlBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .popup-content .content-desc' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'descNmlBsw',
				'selector' => '{{WRAPPER}} .popup-content .content-desc',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_StndMdlDesc_Hvr',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_control(
            'descHColor',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .popup-content .content-desc:hover' => 'color:{{VALUE}};',
                ],
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'descHvrBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .popup-content .content-desc:hover',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'descHvrBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .popup-content .content-desc:hover',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'descHvrBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .popup-content .content-desc:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'descHvrBsw',
				'selector' => '{{WRAPPER}} .popup-content .content-desc:hover',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'TtlDescAlign',
			[
				'label' => esc_html__( 'Title/Desc Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [					
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
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .popup-content' => 'align-items:{{VALUE}};',
				],				
				'toggle' => true,
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*Popup Modal Box Title/Desc Style End*/
		/*Popup Modal Box Copy Code Style Start*/
		$this->start_controls_section(
            'section_modal_PopCpCd_styling',
            [
                'label' => esc_html__('Popup Copy Code', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
            ]
        );
        $this->add_responsive_control(
			'CpTxtPadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .copy-style-1 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-2 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-3 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text.tp-code-popup' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'CpTxtMargin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .copy-style-1 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-2 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-3 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text.tp-code-popup' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'PopcCodeTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .copy-style-1 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-2 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-3 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text.tp-code-popup',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
            'PopcCodebuttonWidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width (%)', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .copy-style-1 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-2 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-3 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text.tp-code-popup' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
            ]
        );
        $this->add_control(
			'PopArwBdrColor',
			[
				'label' => esc_html__( 'Arrow Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .copy-style-1 span.full-code-text.tp-code-popup::before' => 'border-left-color: {{VALUE}}',
					'{{WRAPPER}} .copy-style-1 span.full-code-text.tp-code-popup::after' => 'border-right-color: {{VALUE}}',
				],
				'condition' => [
					'couponType' => 'standard',
					'copyCodeStyle' => 'style-1',
					'actionType' => 'popup',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_PopCpyCd' );
		$this->start_controls_tab(
			'tab_PopCpyCd_Nml',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_control(
            'PopcCodeNColor',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .copy-style-1 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-2 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-3 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text.tp-code-popup' => 'color:{{VALUE}};',
                ],
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
            ]
        );
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'PopcCodeNmlBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .copy-style-1 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-2 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-3 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text.tp-code-popup',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'PopcCodeNmlBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .copy-style-1 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-2 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-3 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text.tp-code-popup',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'PopcCodeNmlBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .copy-style-1 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-2 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-3 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text.tp-code-popup' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'PopcCodeNBShadow',
				'selector' => '{{WRAPPER}} .copy-style-1 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-2 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .copy-style-3 .coupon-code-outer span.full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text.tp-code-popup,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text.tp-code-popup',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_PopCpyCd_Hvr',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_control(
            'PopcCodeHColor',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .copy-style-1 .coupon-code-outer span.full-code-text.tp-code-popup:hover,{{WRAPPER}} .copy-style-2 .coupon-code-outer span.full-code-text.tp-code-popup:hover,{{WRAPPER}} .copy-style-3 .coupon-code-outer span.full-code-text.tp-code-popup:hover,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text.tp-code-popup:hover,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text.tp-code-popup:hover' => 'color:{{VALUE}};',
                ],
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
            ]
        );
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'PopcCodeHvrBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .copy-style-1 .coupon-code-outer span.full-code-text.tp-code-popup:hover,{{WRAPPER}} .copy-style-2 .coupon-code-outer span.full-code-text.tp-code-popup:hover,{{WRAPPER}} .copy-style-3 .coupon-code-outer span.full-code-text.tp-code-popup:hover,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text.tp-code-popup:hover,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text.tp-code-popup:hover',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'PopcCodeHvrBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .copy-style-1 .coupon-code-outer span.full-code-text.tp-code-popup:hover,{{WRAPPER}} .copy-style-2 .coupon-code-outer span.full-code-text.tp-code-popup:hover,{{WRAPPER}} .copy-style-3 .coupon-code-outer span.full-code-text.tp-code-popup:hover,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text.tp-code-popup:hover,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text.tp-code-popup:hover',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'PopcCodeHvrBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .copy-style-1 .coupon-code-outer span.full-code-text.tp-code-popup:hover,{{WRAPPER}} .copy-style-2 .coupon-code-outer span.full-code-text.tp-code-popup:hover,{{WRAPPER}} .copy-style-3 .coupon-code-outer span.full-code-text.tp-code-popup:hover,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text.tp-code-popup:hover,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text.tp-code-popup:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'PopcCodeHBShadow',
				'selector' => '{{WRAPPER}} .copy-style-1 .coupon-code-outer span.full-code-text.tp-code-popup:hover,{{WRAPPER}} .copy-style-2 .coupon-code-outer span.full-code-text.tp-code-popup:hover,{{WRAPPER}} .copy-style-3 .coupon-code-outer span.full-code-text.tp-code-popup:hover,{{WRAPPER}} .tp-coupon-code .copy-style-4 .full-code-text.tp-code-popup:hover,{{WRAPPER}} .tp-coupon-code .copy-style-5 .full-code-text.tp-code-popup:hover',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Popup Modal Box Copy Code Style End*/
		/*Popup Modal Box Copy Button Style Start*/
		$this->start_controls_section(
            'section_modal_PopCpyBtn_styling',
            [
                'label' => esc_html__('Popup Copy Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'copyBtnText!' => '',
				],				
            ]
        );
        $this->add_responsive_control(
			'copyBtnPadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .copy-code-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'copyBtnMargin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .copy-code-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'copyBtnTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .copy-code-btn',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'copyBtnText!' => '',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_StndCpyBtn' );
		$this->start_controls_tab(
			'tab_StndCpyBtn_Nml',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'copyBtnText!' => '',
				],
			]
		);
		$this->add_control(
            'copyBtnNColor',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .copy-code-btn' => 'color:{{VALUE}};',
                ],
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'copyBtnText!' => '',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'copyBtnNmlBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .copy-code-btn',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'copyBtnText!' => '',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'copyBtnNmlBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .copy-code-btn',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'copyBtnText!' => '',
				],
			]
		);
		$this->add_responsive_control(
			'copyBtnNBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .copy-code-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'copyBtnText!' => '',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'copyBtnNBShadow',
				'selector' => '{{WRAPPER}} .copy-code-btn',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'copyBtnText!' => '',
				],				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_StndCpyBtn_Hvr',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'copyBtnText!' => '',
				],
			]
		);
		$this->add_control(
            'copyBtnHColor',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .copy-code-btn:hover' => 'color:{{VALUE}};',
                ],
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'copyBtnText!' => '',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'copyBtnHvrBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .copy-code-btn:hover',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'copyBtnText!' => '',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'copyBtnHvrBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .copy-code-btn:hover',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'copyBtnText!' => '',
				],
			]
		);
		$this->add_responsive_control(
			'copyBtnHBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .copy-code-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'copyBtnText!' => '',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'copyBtnHBShadow',
				'selector' => '{{WRAPPER}} .copy-code-btn:hover',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'copyBtnText!' => '',
				],				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Popup Modal Box Copy Button Style End*/
		/*Popup Modal Box Visit Button Style Start*/
		$this->start_controls_section(
            'section_modalVstBtn_styling',
            [
                'label' => esc_html__('Popup Visit Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
            ]
        );
        $this->add_responsive_control(
			'VstBtnPadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .coupon-store-visit .store-visit-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'VstBtnMargin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .coupon-store-visit .store-visit-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'visitBtnTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .coupon-store-visit .store-visit-link,{{WRAPPER}} .coupon-store-visit .store-visit-link a',
			]
		);
		$this->start_controls_tabs( 'tabs_visitBtn' );
		$this->start_controls_tab(
			'tab_visitBtn_Nml',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_control(
            'visitBtnNColor',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .coupon-store-visit .store-visit-link,{{WRAPPER}} .coupon-store-visit .store-visit-link a' => 'color:{{VALUE}};',
                ],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'visitBtnNmlBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .coupon-store-visit .store-visit-link',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'visitBtnNmlBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .coupon-store-visit .store-visit-link',
			]
		);
		$this->add_responsive_control(
			'visitBtnNBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .coupon-store-visit .store-visit-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'visitBtnNBShadow',
				'selector' => '{{WRAPPER}} .coupon-store-visit .store-visit-link',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_visitBtn_Hvr',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
				],
			]
		);
		$this->add_control(
            'visitBtnHColor',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .coupon-store-visit .store-visit-link:hover,{{WRAPPER}} .coupon-store-visit .store-visit-link:hover a' => 'color:{{VALUE}};',
                ],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'visitBtnHvrBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .coupon-store-visit .store-visit-link:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'visitBtnHvrBdr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .coupon-store-visit .store-visit-link:hover',
			]
		);
		$this->add_responsive_control(
			'visitBtnHBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .coupon-store-visit .store-visit-link:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'visitBtnHBShadow',
				'selector' => '{{WRAPPER}} .coupon-store-visit .store-visit-link:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'VstBtnAlign',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [					
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
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .popup-code-modal .coupon-store-visit' => 'align-items:{{VALUE}};',
				],				
				'toggle' => true,
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*Popup Modal Box Visit Button Style End*/
		/*Popup Modal Box Scroll Bar Style Start*/
		$this->start_controls_section(
            'section_modal_ScrlBr_styling',
            [
                'label' => esc_html__('Popup Scroll Bar', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'CdScrollOn'    => 'yes',
				],				
            ]
        );
		$this->start_controls_tabs( 'CCd_scrollC_style' );
		$this->start_controls_tab(
			'CCd_scrollC_Bar',
			[
				'label' => esc_html__( 'Scrollbar', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'CdScrollOn'    => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'CCdScrollBg',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-coupon-code .tp-code-scroll::-webkit-scrollbar',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'CdScrollOn'    => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'CCdScrollWidth',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .tp-code-scroll::-webkit-scrollbar' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'CdScrollOn'    => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'CCdscrollC_Tmb',
			[
				'label' => esc_html__( 'Thumb', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'CdScrollOn'    => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'CCdThumbBg',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-coupon-code .tp-code-scroll::-webkit-scrollbar-thumb',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'CdScrollOn'    => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'CCdThumbBrs',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-coupon-code .tp-code-scroll::-webkit-scrollbar-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'CdScrollOn'    => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'CCdThumbBsw',
				'selector' => '{{WRAPPER}} .tp-coupon-code .tp-code-scroll::-webkit-scrollbar-thumb',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'CdScrollOn'    => 'yes',
				],				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'CCdscrollC_Trk',
			[
				'label' => esc_html__( 'Track', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'CdScrollOn'    => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'CCdTrackBg',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-coupon-code .tp-code-scroll::-webkit-scrollbar-track',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'CdScrollOn'    => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'CCdTrackBRs',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-coupon-code .tp-code-scroll::-webkit-scrollbar-track' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'CdScrollOn'    => 'yes',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'CCdTrackBsw',
				'selector' => '{{WRAPPER}} .tp-coupon-code .tp-code-scroll::-webkit-scrollbar-track',
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
					'CdScrollOn'    => 'yes',
				],				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
        $this->end_controls_section();
		/*Popup Modal Box Scroll Bar Style End*/
		/*Popup Modal Box Bg Style Start*/
		$this->start_controls_section(
            'section_modalBox_styling',
            [
                'label' => esc_html__('Popup Modal Box', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],				
            ]
        );
        $this->add_responsive_control(
			'StndModalPadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .ccd-main-modal' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_responsive_control(
			'StndModalMargin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .tp-coupon-code .ccd-main-modal' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_StndModal' );
		$this->start_controls_tab(
			'tab_StndModal_Nml',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'StndModalBGNml',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-coupon-code .ccd-main-modal',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'StndModalBorderNml',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-coupon-code .ccd-main-modal',
			]
		);
		$this->add_responsive_control(
			'StndModalBRadiusNml',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-coupon-code .ccd-main-modal' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'StndModalBshadowNml',
				'selector' => '{{WRAPPER}} .tp-coupon-code .ccd-main-modal',				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_StndModal_Hvr',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'StndModalBGHvr',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-coupon-code .ccd-main-modal:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'StndModalBorderHvr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-coupon-code .ccd-main-modal:hover',
			]
		);
		$this->add_responsive_control(
			'StndModalBRadiusHvr',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-coupon-code .ccd-main-modal:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'StndModalBshadowHvr',
				'selector' => '{{WRAPPER}} .tp-coupon-code .ccd-main-modal:hover',				
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
					'{{WRAPPER}} .tp-coupon-code .ccd-main-modal' => '-webkit-backdrop-filter:grayscale({{mbbf_grayscale.SIZE}})  blur({{mbbf_blur.SIZE}}{{mbbf_blur.UNIT}}) !important;backdrop-filter:grayscale({{mbbf_grayscale.SIZE}})  blur({{mbbf_blur.SIZE}}{{mbbf_blur.UNIT}}) !important;',
				 ],
				'condition'    => [
					'mbbf' => 'yes',
				],
			]
		);
		$this->end_popover();
        $this->end_controls_section();
		/*Popup Modal Box Bg Style Start*/
		
		/*Peel/Scratch/SlideOut Style Start*/
		$this->start_controls_section(
            'section_Pl_Scrh_SldOut_styling',
            [
                'label' => esc_html__('Front Side Content', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'couponType!' => 'standard',
				],				
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'frontTtltypo',
				'label' => esc_html__( 'Title Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-coupon-code .coupon-front-content',
				'condition' => [					
					'frontContentType' => 'default',
				],
			]
		);
		$this->add_control(
            'frontTtlclr',
            [
                'label' => esc_html__('Title Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-coupon-code .coupon-front-content' => 'color:{{VALUE}};',
                ],
				'condition' => [					
					'frontContentType' => 'default',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'frontBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .coupon-front-side',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'frontBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .coupon-front-side',
			]
		);
		$this->add_responsive_control(
			'frontBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .coupon-front-side' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'frontBshadow',
				'selector' => '{{WRAPPER}} .coupon-front-side',				
			]
		);
		$this->add_control(
            'slidehinticoncolor',
            [
                'label' => esc_html__('HInt Icon Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-anim-pos-cont svg' => 'color:{{VALUE}};',
                ],
				'separator' => 'before',
				'condition' => [					
					'couponType' => 'slideOut',
					'slideDirectionhint' => 'yes',
				],
            ]
        );
		$this->end_controls_section();
		/*Peel/Scratch/SlideOut front side Style End*/
		
		/*Peel/Scratch/SlideOut Style Start*/
		$this->start_controls_section(
            'section_Pl_Scrh_SldOut_back_styling',
            [
                'label' => esc_html__('Back Side Content', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'couponType!' => 'standard',
				],				
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'backTtltypo',
				'label' => esc_html__( 'Title Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-coupon-code .coupon-back-title',
				'condition' => [					
					'backContentType' => 'default',
				],
			]
		);
		$this->add_control(
            'backTtlclr',
            [
                'label' => esc_html__('Title Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-coupon-code .coupon-back-title' => 'color:{{VALUE}};',
                ],
				'condition' => [					
					'backContentType' => 'default',
				],
            ]
        );
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'backDesctypo',
				'label' => esc_html__( 'Description Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-coupon-code .coupon-back-description',
				'condition' => [					
					'backContentType' => 'default',
				],
			]
		);
        $this->add_control(
            'backDescclr',
            [
                'label' => esc_html__('Description Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tp-coupon-code .coupon-back-description' => 'color:{{VALUE}};',
                ],
				'condition' => [					
					'backContentType' => 'default',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'backBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .coupon-back-side',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'backBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .coupon-back-side',
			]
		);
		$this->add_responsive_control(
			'backBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .coupon-back-side' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'backBshadow',
				'selector' => '{{WRAPPER}} .coupon-back-side',
			]
		);
		$this->end_controls_section();
		/*Peel/Scratch/SlideOut back side Style End*/
		
		/*Peel/Scratch/SlideOut Style Start*/
		$this->start_controls_section(
            'section_boxcontent_styling',
            [
                'label' => esc_html__('Box Content', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'couponType!' => 'standard',
				],				
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'boxcontentBG',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-coupon-code',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'boxcontentBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-coupon-code',
			]
		);
		$this->add_responsive_control(
			'boxcontentBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-coupon-code' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'boxcontentBshadow',
				'selector' => '{{WRAPPER}} .tp-coupon-code',
			]
		);
		$this->end_controls_section();
		/*Peel/Scratch/SlideOut back side Style End*/
		
		/*Extra Option Start*/
		$this->start_controls_section(
            'section_extra_opts_styling',
            [
                'label' => esc_html__('Extra Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'couponType' => 'standard',
					'actionType' => 'popup',
				],			
            ]
        );
		$this->add_control(
			'CCd_overlay_color',
			[
				'label' => esc_html__( 'Overlay Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .copy-code-wrappar::after' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'olcbf',
			[
				'label' => esc_html__( 'Overlay Backdrop Filter', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'olcbf_blur',
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
					'olcbf' => 'yes',
				],
			]
		);
		$this->add_control(
			'olcbf_grayscale',
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
					'{{WRAPPER}} .copy-code-wrappar::after' => '-webkit-backdrop-filter:grayscale({{olcbf_grayscale.SIZE}})  blur({{olcbf_blur.SIZE}}{{olcbf_blur.UNIT}}) !important;backdrop-filter:grayscale({{olcbf_grayscale.SIZE}})  blur({{olcbf_blur.SIZE}}{{olcbf_blur.UNIT}}) !important;',
				 ],
				'condition'    => [
					'olcbf' => 'yes',
				],
			]
		);
		$this->end_popover();
        $this->end_controls_section();
		/*Extra Option End*/
	}
	
	 protected function render() {

        $settings = $this->get_settings_for_display();
		$couponType = !empty($settings['couponType']) ? $settings['couponType'] :'standard';
		$standardStyle = !empty($settings['standardStyle']) ? $settings['standardStyle'] :'style-1';
		$couponText = !empty($settings['couponText']) ? $settings['couponText'] :'';
		$copyBtnText = !empty($settings['copyBtnText']) ? $settings['copyBtnText'] :'';
		$afterCopyText = !empty($settings['afterCopyText']) ? $settings['afterCopyText'] :'';
		$copyCodeStyle = !empty($settings['copyCodeStyle']) ? $settings['copyCodeStyle'] :'style-1';
		$visitBtnText = !empty($settings['visitBtnText']) ? strtoupper($settings['visitBtnText']) :'';
		$couponCode = !empty($settings['couponCode']) ? $settings['couponCode'] :'';
		$popupTitle = !empty($settings['popupTitle']) ? $settings['popupTitle'] :'';
		$popupDesc = !empty($settings['popupDesc']) ? $settings['popupDesc'] :'';
		$actionType = !empty($settings['actionType']) ? $settings['actionType'] :'click';
		$fillPercent = !empty($settings['fillPercent']['size']) ? $settings['fillPercent']['size'] :'70';
		$slideDirection = !empty($settings['slideDirection']) ? $settings['slideDirection'] :'left';		
		$frontContentType = !empty($settings['frontContentType']) ? $settings['frontContentType'] :'default';
		$frontContent = !empty($settings['frontContent']) ? $settings['frontContent'] :'';
		$frontContentTag = !empty($settings['frontContentTag']) ? $settings['frontContentTag'] : 'h3';
		$frontTemp = !empty($settings['frontTemp']) ? $settings['frontTemp'] : '0';
		$backContentType = !empty($settings['backContentType']) ? $settings['backContentType'] :'default';
		$backTitle = !empty($settings['backTitle']) ? $settings['backTitle'] : '';
		$backTitleTag = !empty($settings['backTitleTag']) ? $settings['backTitleTag'] : 'h3';
		$backDesc = !empty($settings['backDesc']) ? $settings['backDesc'] : '';
		$backTemp = !empty($settings['backTemp']) ? $settings['backTemp'] : '0';
		$redirectLink = !empty($settings['redirectLink']['url']) ? $settings['redirectLink']['url'] : '';
		$target = !empty($settings['redirectLink']['is_external']) ? '_blank' : '';
		$nofollow = !empty($settings['redirectLink']['nofollow']) ? 'nofollow' : '';
		$scrollOn = !empty($settings["CdScrollOn"]) ? $settings["CdScrollOn"] : 'no';
		$scrollHgt = !empty($settings['CdScrollHgt']['size']) ? (int)$settings['CdScrollHgt']['size'] :'';
		$cstmStdWidth = !empty($settings['buttonWidth']['size']) ? (int)$settings['buttonWidth']['size'] :'100';
		$tabReverse = isset($settings['tabReverse']) ? $settings['tabReverse'] :'no';
		$copLoop = isset($settings['copLoop']) ? $settings['copLoop'] :'no';
		$hideLink = isset($settings['hideLink']) ? $settings['hideLink'] :'no';
		$hideLinkMaskText = !empty($settings['hideLinkMaskText']) ? $settings['hideLinkMaskText'] :'#';
		$iduu=get_the_ID();
		if(!empty($copLoop) && $copLoop=='yes'){				
			$uid_ccd = 'tp-ccd'.$this->get_id().$iduu;
		}else{
			$uid_ccd = 'tp-ccd'.$this->get_id();
		}
		
		$clickAction = $visitLink = $modelClsIcon = $tabReverseClass = '';
		if($actionType == 'click') {
			$clickAction .= 'href="'.esc_url($redirectLink).'"';
			$clickAction .= ' target="'.esc_attr($target).'"';
			$clickAction .= ' nofollow="'.esc_attr($nofollow).'"';
		}else if($actionType=='popup') {
			if(!empty($tabReverse) && $tabReverse=='yes'){
				$tabReverseClass =" tp-tab-cop-rev";
				if(!empty($copLoop) && $copLoop=='yes'){
					$clickAction .= 'href="#tp-widget-'.$uid_ccd.'"';
				}else{
					$clickAction .= 'href="#tp-widget-'.$uid_ccd.'"';
				}		
			}else{
				$clickAction .= 'href="'.esc_url($redirectLink).'"';
			}
			
			$clickAction .= ' target="'.esc_attr($target).'"';
			$clickAction .= ' nofollow="'.esc_attr($nofollow).'"';
			$visitLink .= 'href="'.esc_url($redirectLink).'"';
			$visitLink .= ' target="'.esc_attr($target).'"';
			$visitLink .= ' nofollow="'.esc_attr($nofollow).'"';
		}
		$coupon_code_attr = [];
		$coupon_code_attr['id'] = $uid_ccd;
		$coupon_code_attr['couponType'] = $couponType;
		if($couponType == 'standard') {
			$coupon_code_attr['actionType'] = $actionType;
			$coupon_code_attr['copy_code_style'] = $copyCodeStyle;
			$coupon_code_attr['coupon_code'] = $couponCode;
			$coupon_code_attr['copy_btn_text'] = $copyBtnText;
			$coupon_code_attr['after_copy_text'] = $afterCopyText;
			$coupon_code_attr['cstm_stdBtn_wdth'] = $cstmStdWidth;
			if (!empty($scrollOn) && $scrollOn == 'yes') {
				$coupon_code_attr['classname'] = 'tp-code-scroll';
				$coupon_code_attr['scrollon'] = $scrollOn;
				$coupon_code_attr['sclheight'] = $scrollHgt;
			}
			if($actionType=='popup' && !empty($tabReverse) && $tabReverse=='yes') {				
				$coupon_code_attr['extlink'] = $redirectLink;
			}			
		}
		if($couponType == 'scratch') {
			$coupon_code_attr['fillPercent'] = $fillPercent;
		}
		$slide_out_class = '';
		if($couponType == 'slideOut') {
			$coupon_code_attr['slideDirection'] = $slideDirection;
			$slide_out_class = ' slide-out-'.$slideDirection;
		}
		if(!empty($settings["modelClsIcon"])){
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings["modelClsIcon"], [ 'aria-hidden' => 'true' ]);
			$modelClsIcon = ob_get_contents();
			ob_end_clean();						
		}

		$coupon_code_attr = htmlspecialchars(json_encode($coupon_code_attr), ENT_QUOTES, 'UTF-8');
		
		$output = '';
		$output .= '<div id="tp-widget-'.$uid_ccd.'" class="tp-coupon-code action-'.$actionType.' coupon-code-'.esc_attr($couponType).''.esc_attr($slide_out_class).' tp-widget-'.esc_attr($uid_ccd).' '.esc_attr($tabReverseClass).' tp-widget-'.$uid_ccd.'" data-tp_cc_settings=\'' .$coupon_code_attr. '\'>';
			if($couponType == 'standard') {
				$output .= '<div class="coupon-code-inner '.esc_attr($standardStyle).'">';
					
					$data = [];
					if($hideLink=='yes' && !empty($hideLinkMaskText)){						
						foreach($settings["LinkMaskList"] as $item) {
							$hideLinks = !empty($item["LinkMaskLink"]["url"]) ? $item["LinkMaskLink"]["url"] : '';
							$data[]= $hideLinks;
						}
						if(!empty($redirectLink)){
							$data[]= $redirectLink;
						}								
						$data = json_encode($data);
						
						$output .= '<a class="coupon-btn-link tp-hl-links" href="'.esc_attr($hideLinkMaskText).'" data-hlset=\''.$data .'\'>';						
					}else{
						$output .= '<a class="coupon-btn-link" '.$clickAction.'>';
					}					
						if($standardStyle == 'style-4' || $standardStyle == 'style-5') {
							$output .= '<span class="coupon-icon">';
								$output .= '<i class="fa fa-scissors coupon-type"></i>';
							$output .= '</span>';
						}
						$output .= '<div class="coupon-text">'.esc_html($couponText).'</div>';
						if($standardStyle != 'style-4' && $standardStyle != 'style-5') {
							$output .= '<div class="coupon-code">'.esc_html($couponCode).'</div>';
						}
					$output .= '</a>';
					if($actionType == 'popup') {
						$output .= '<div class="copy-code-wrappar" role="dialog"></div>';
						
						$popupBgImage = isset($settings['popupBgImage']) ? $settings['popupBgImage']['url'] : '';
						$ppbistyle='';
						if(!empty($copLoop) && $copLoop=='yes' && !empty($popupBgImage)){				
							$ppbistyle='style="background-image: url('.$popupBgImage.');background-position: center center;background-repeat: no-repeat;background-size: cover;"';
						}
						
						
						$output .= '<div '.$ppbistyle.' class="ccd-main-modal copy-'.$copyCodeStyle.'" role="alert">';
							if(!empty($modelClsIcon)){
								$output .= '<button class="tp-ccd-closebtn" role="button">'.$modelClsIcon.'</button>';
							}
							$output .= '<div class="popup-code-modal">';
							    if($actionType == 'popup') {
									$output .= '<div class="popup-content">';
										$output .= '<div class="content-title">'.esc_html($popupTitle).'</div>';
										$output .= '<div class="content-desc">'.esc_html($popupDesc).'</div>';
									$output .= '</div>';
							    }
								$output .= '<div class="coupon-code-outer">';
									$output .= '<span class="full-code-text">'.esc_html($couponCode).'</span>';
									$output .= '<button class="copy-code-btn">'.esc_html($copyBtnText).'</button>';
								$output .= '</div>';
								if(!empty($visitBtnText)){
									$output .= '<div class="coupon-store-visit">';
										$output .= '<a class="store-visit-link" '.$visitLink.'>'.esc_html($visitBtnText).'</a>';
									$output .= '</div>';
								}
							$output .= '</div>';
						$output .= '</div>';
					}
				$output .= '</div>';
			}else if($couponType != 'standard') {
				$output .= '<div class="coupon-front-side" id="front-side-'.esc_attr($uid_ccd).'">';
					$output .= '<div class="coupon-front-inner">';
						$slideDirectionhint = isset($settings['slideDirectionhint']) ? $settings['slideDirectionhint'] : '';
						if($slideDirectionhint== 'yes'){
							$output .= '<div class="tp-anim-pos-cont '.esc_attr($slide_out_class).'">					
								<svg version="1.0" xmlns="http://www.w3.org/2000/svg"
								 width="67.000000pt" height="34.000000pt" viewBox="0 0 67.000000 34.000000"
								 preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,34.000000) scale(0.100000,-0.100000)"
								fill="currentcolor" stroke="none"><path d="M300 250 c0 -64 -9 -86 -25 -60 -3 6 -13 10 -21 10 -29 0 -25 -30 9
								-72 19 -24 38 -53 41 -66 7 -20 14 -22 69 -22 l62 0 14 53 c23 88 24 112 7
								126 -12 10 -16 11 -16 1 0 -10 -3 -10 -15 0 -8 6 -19 9 -24 6 -5 -4 -13 -2
								-16 4 -4 6 -15 8 -26 5 -17 -6 -19 -1 -19 39 0 39 -3 46 -20 46 -18 0 -20 -7
								-20 -70z"/><path d="M91 156 l-32 -24 37 -23 c26 -16 39 -19 42 -11 2 7 17 12 33 12 24 0
								29 4 29 25 0 21 -5 25 -30 25 -16 0 -30 5 -30 10 0 16 -14 12 -49 -14z"/>
								<path d="M590 170 c0 -5 -16 -10 -35 -10 -31 0 -35 -3 -35 -25 0 -22 4 -25 34
								-25 19 0 36 -5 38 -12 3 -8 15 -4 37 11 l33 24 -29 23 c-31 25 -43 29 -43 14z"/>
								</g></svg>					
							</div>';
						}
				
						if($frontContentType == 'default' && !empty($frontContent)) {
							$output .= '<div class="coupon-inner-content">';
								$output .= '<'.theplus_validate_html_tag($frontContentTag).' class="coupon-front-content">'.$frontContent.'</'.theplus_validate_html_tag($frontContentTag).'>';
							$output .= '</div>';
						}else if($frontContentType == 'template' && (!empty($frontTemp) && $frontTemp != '0')){
							$output .='<div class="plus-content-editor">'.Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display( $frontTemp ).'</div>';
						}
					$output .= '</div>';
					$output .= '<div class="coupon-code-overlay"></div>';
				$output .= '</div>';

				$output .= '<div class="coupon-back-side">';
					$output .= '<div class="coupon-back-inner">';
						if($backContentType == 'default') {
							$output .= '<div class="coupon-back-content">';
								if(!empty($backTitle)) {
									$output .= '<'.theplus_validate_html_tag($backTitleTag).' class="coupon-back-title">'.esc_html($backTitle).'</'.theplus_validate_html_tag($backTitleTag).'>';
								}
								if(!empty($backDesc)) {
									$output .= '<p class="coupon-back-description">'.$backDesc.'</p>';
								}
							$output .= '</div>';
						}else if($backContentType == 'template' && (!empty($backTemp) && $backTemp != '0') ){
							$output .='<div class="plus-content-editor">'.Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display( $backTemp ).'</div>';
						}
					$output .= '</div>';
					$output .= '<div class="coupon-code-overlay"></div>';
				$output .= '</div>';				
			}
		$output .= '</div>';	
					
		echo $output;
	}
	
    protected function content_template() {
	
    }
}