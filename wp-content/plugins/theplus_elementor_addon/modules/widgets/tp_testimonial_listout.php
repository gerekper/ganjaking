<?php 
/*
Widget Name: Testimonial Carousel
Description: Different style of testimonial.
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
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Testimonial_ListOut extends Widget_Base {

	public $TpDoc = THEPLUS_TPDOC;

	public function get_name() {
		return 'tp-testimonial-listout';
	}

    public function get_title() {
        return esc_html__('Testimonial', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-users theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-listing');
    }
	
	public function get_custom_help_url() {
		$DocUrl = $this->TpDoc . "testimonial";

		return esc_url($DocUrl);
	}

    protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content Layout', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(4),
			]
		);
		$this->add_control(
			'layout',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'carousel',
				'options' => [
					'grid' => esc_html__( 'Grid', 'theplus' ),
					'masonry' => esc_html__( 'Masonry', 'theplus' ),
					'carousel' => esc_html__( 'Carousel', 'theplus' ),
				],
			]
		);
		$this->add_control('how_it_works_grid',
			[
				'label' => wp_kses_post( "<a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "show-testimonials-in-grid-layout-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> Learn How it works  <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'layout' => ['grid']
				],
			]
		);
		$this->add_control('how_it_works_Masonry',
			[
				'label' => wp_kses_post( "<a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "show-testimonials-in-masonry-grid-layout-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> Learn How it works  <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'layout' => ['masonry']
				],
			]
		);
		$this->add_control('how_it_works_carousel',
			[
				'label' => wp_kses_post( "<a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "add-a-testimonial-carousel-slider-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> Learn How it works  <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'layout' => ['carousel']
				],
			]
		);
		$this->add_control(
			'content_alignment_4',
			[
				'label' => esc_html__( 'Content Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'condition' => [
					'style' => 'style-4',
				],
				'label_block' => false,
				'toggle' => true,
			]
		);
		$this->add_control('style4separator',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => ['style' => 'style-4'],
			]
		);
		$this->add_control(
			'style_3_layout',
			[
				'label' => esc_html__( 'Style Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(2),
				'separator' => 'after',
				'condition' => [
					'style' => 'style-3',
				],
			]
		);
		$this->add_control(
			'tlContentFrom',
			[
				'label' => esc_html__( 'Select Source', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'tlcontent',
				'options' => [
					'tlcontent' => esc_html__( 'Post Type', 'theplus' ),
					'tlrepeater' => esc_html__( 'Repeater', 'theplus' ),
				],
			]
		);
		$this->add_control('how_it_works_Post_Type',
			[
				'label' => wp_kses_post( "<a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "add-testimonials-with-custom-post-type-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> Learn How it works  <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'tlContentFrom' => ['tlcontent']
				],
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
            'testiAuthor',
            [   
            	'label' => esc_html__( 'Testimonial Content', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Testimonial Content', 'theplus' ),
				'dynamic' => ['active'   => true],
            ]
        );
		$repeater->add_control(
			'testiTitle',
			[
				'label' => esc_html__( 'Testimonial Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => ['active' => true],
				'default' => '',
				'placeholder' => esc_html__( 'Enter Testimonial Title', 'theplus' ),
			]
		);
		$repeater->add_control(
			'testiLabel',
			[
				'label' => esc_html__( 'Author Name', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => ['active' => true],
				'default' => '',
				'placeholder' => esc_html__( 'Enter Author Name', 'theplus' ),
			]
		);
		$repeater->add_control(
			'testiDesign',
			[
				'label' => esc_html__( 'Author Designation', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => ['active' => true],
				'default' => '',
				'placeholder' => esc_html__( 'Enter Designation', 'theplus' ),
			]
		);
		$repeater->add_control(
			'testiImage',[
				'label' => esc_html__( 'Author Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => ['active'   => true],
			]
		);
		$repeater->add_control(
			'testiLogo',[
				'label' => esc_html__( 'Company Logo', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => ['active'   => true],
			]
		);
		$repeater->add_control(
			'testiLogoNote',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => 'Note : This is just for style 4.',
				'content_classes' => 'tp-widget-description',
			]
		);
		$this->add_control(
			'testiAllList',
			[
				'label' => esc_html__( 'Manage Testimonials', 'theplus' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),			
				'default' => [
					[	
						'testiAuthor' => 'I have been using the software from XYZ Business for a few weeks now and it has exceeded my expectations. It is user-friendly, efficient, and the customer support team is always available to help. Highly recommend this software!',	
						'testiTitle' => 'Outstanding Support',				
						'testiLabel' => 'Emily Thompson',
						'testiDesign' => 'CEO of CodeCraft Inc.',
					],
					[	
						'testiAuthor' => 'I have been using the software from XYZ Business for a few weeks now and it has exceeded my expectations. It is user-friendly, efficient, and the customer support team is always available to help. Highly recommend this software!',
						'testiTitle' => 'Improved Productivity',					
						'testiLabel' => 'Benjamin Reed',
						'testiDesign' => 'Founder of X Community',
					],
					[	
						'testiAuthor' => 'I have been using the software from XYZ Business for a few weeks now and it has exceeded my expectations. It is user-friendly, efficient, and the customer support team is always available to help. Highly recommend this software!',
						'testiTitle' => 'Highly recommend',					
						'testiLabel' => 'Rachel Johnson',
						'testiDesign' => 'COO of AppFinity Solutions',
					],
				],
				'title_field' => '{{{ testiLabel }}}',
				'condition' => [
					'tlContentFrom' => 'tlrepeater',
				],
			]
		);
		$this->end_controls_section();
		/*columns*/
		$this->start_controls_section(
			'columns_section',
			[
				'label' => esc_html__( 'Columns Manage', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'layout!' => ['carousel']
				],
			]
		);
		$this->add_control(
			'desktop_column',
			[
				'label' => esc_html__( 'Desktop Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => theplus_get_columns_list(),
				'condition' => [
					'layout!' => ['carousel']
				],
			]
		);
		$this->add_control(
			'tablet_column',
			[
				'label' => esc_html__( 'Tablet Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '4',
				'options' => theplus_get_columns_list(),
				'condition' => [
					'layout!' => ['carousel']
				],
			]
		);
		$this->add_control(
			'mobile_column',
			[
				'label' => esc_html__( 'Mobile Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '6',
				'options' => theplus_get_columns_list(),
				'condition' => [
					'layout!' => ['carousel']
				],
			]
		);
		$this->add_responsive_control(
			'columns_gap',
			[
				'label' => esc_html__( 'Columns Gap/Space Between', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' =>[
					'top' => "15",
					'right' => "15",
					'bottom' => "15",
					'left' => "15",				
				],
				'separator' => 'before',
				'condition' => [
					'layout!' => ['carousel']
				],
				'selectors' => [
					'{{WRAPPER}} .testimonial-list .post-inner-loop .grid-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		/*columns*/
		$this->start_controls_section(
			'content_source_section',
			[
				'label' => esc_html__( 'Content Source', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'tlContentFrom!' => 'tlrepeater',
				],
			]
		);
		$this->add_control(
			'post_category',
			[
				'type' => Controls_Manager::SELECT2,
				'label'      => esc_html__( 'Select Category', 'theplus' ),
				'default'    => '',
				'label_block' => true,
				'multiple'   => true,
				'options' => theplus_get_testimonial_categories(),
				'separator' => 'before',
			]
		);
		$this->add_control(
			'display_posts',
			[
				'label' => esc_html__( 'Maximum Posts Display', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 200,
				'step' => 1,
				'default' => 8,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'post_offset',
			[
				'label' => esc_html__( 'Offset Posts', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 50,
				'step' => 1,
				'default' => '',
				'description' => esc_html__('Hide posts from the beginning of listing.','theplus'),
			]
		);
		$this->add_control(
			'post_order_by',
			[
				'label' => esc_html__( 'Order By', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => theplus_orderby_arr(),
			]
		);
		$this->add_control(
			'post_order',
			[
				'label' => esc_html__( 'Order', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => theplus_order_arr(),
			]
		);
		
		$this->end_controls_section();
		$this->start_controls_section(
			'content_extra_options_section',
			[
				'label' => esc_html__( 'Extra Option', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'post_title_tag',
			[
				'label' => esc_html__( 'Title Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => theplus_get_tags_options(),
			]
		);
		$this->add_control(
			'display_thumbnail',
			[
				'label' => esc_html__( 'Display Image Size', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',				
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'exclude' => [ 'custom' ],
				'condition'   => [					
					'display_thumbnail' => 'yes',
				],
			]
		);
		$this->add_control(
			'caroByheight',
			[
				'label' => esc_html__( 'Content Limit By', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'Default', 'theplus' ),
					'height' => esc_html__( 'Height', 'theplus' ),
					'text-limit' => esc_html__( 'Text Limit', 'theplus' ),
				],
				'condition' => [
					'tlContentFrom' => ['tlrepeater'],
					'layout' => ['carousel']
				],
			]
		);
		$this->add_responsive_control(
            'contentHei',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => wp_kses_post( "Content Height(px) <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "set-elementor-testimonial-carousel-height/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'size_units' => [ 'px'],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'range' => [
					'px' => [
						'min'	=> 1,
						'max'	=> 500,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list .testimonial-list-content .entry-content' => 'height: {{SIZE}}{{UNIT}};overflow-y: auto; padding-right: 5px;',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
						'terms' => [
								['name' => 'layout', 'operator' => '===', 'value' => 'grid'],
							]
						],
						[
						'terms' => [
								['name' => 'layout', 'operator' => '===', 'value' => 'carousel'],
								['name' => 'caroByheight', 'operator' => '===', 'value' => 'height']
							]
						],
					]
				],
            ]
        );
		$this->add_responsive_control(
            'titleHei',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Title Height(px)', 'theplus'),
				'size_units' => [ 'px'],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'range' => [
					'px' => [
						'min'	=> 1,
						'max'	=> 500,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list .testimonial-list-content .testimonial-author-title' => 'height: {{SIZE}}{{UNIT}};overflow-y: auto; padding-right: 5px;',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
						'terms' => [
								['name' => 'layout', 'operator' => '===', 'value' => 'grid'],
							]
						],
						[
						'terms' => [
								['name' => 'layout', 'operator' => '===', 'value' => 'carousel'],
								['name' => 'caroByheight', 'operator' => '===', 'value' => 'height']
							]
						],
					]
				],
            ]
        );
		$this->add_control(
			'cntscrollOn',
			[
				'label' => esc_html__( 'Content Scroll', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'on-hover',
				'options' => [
					'on-hover' => esc_html__( 'On Hover', 'theplus' ),
					'visible' => esc_html__( 'Visible', 'theplus' ),
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
						'terms' => [
								['name' => 'layout', 'operator' => '===', 'value' => 'grid'],
							]
						],
						[
						'terms' => [
								['name' => 'layout', 'operator' => '===', 'value' => 'carousel'],
								['name' => 'caroByheight', 'operator' => '===', 'value' => 'height']
							]
						],
					]
				],
			]
		);
		$this->add_control('descByLimit',
			[
				'label' => wp_kses_post( "Excerpt Limit <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "limit-elementor-testimonial-carousel-by-text/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default', 'theplus' ),
					'letters' => esc_html__( 'By Letters', 'theplus' ),
					'words' => esc_html__( 'By Words', 'theplus' ),
				],
				'condition' => [
					'tlContentFrom' => ['tlrepeater'],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
						'terms' => [
								['name' => 'layout', 'operator' => '===', 'value' => 'masonry'],
							]
						],
						[
						'terms' => [
								['name' => 'layout', 'operator' => '===', 'value' => 'carousel'],
								['name' => 'caroByheight', 'operator' => '===', 'value' => 'text-limit']
							]
						],
					]
				],
			]
		);
		$this->add_control(
			'descLimit',
			[
				'label' => esc_html__( 'Maximum Letters/Words', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1000,
				'step' => 1,
				'default' => 30,
				'condition' => [
					'tlContentFrom' => ['tlrepeater'],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
						'terms' => [
								['name' => 'layout', 'operator' => '===', 'value' => 'masonry'],
								['name' => 'descByLimit', 'operator' => '!==', 'value' => 'default'],
							]
						],
						[
						'terms' => [
								['name' => 'layout', 'operator' => '===', 'value' => 'carousel'],
								['name' => 'caroByheight', 'operator' => '===', 'value' => 'text-limit'],
								['name' => 'descByLimit', 'operator' => '!==', 'value' => 'default'],
							]
						],
					]
				],
			]
		);	
		$this->add_control(
			'titleByLimit',
			[
				'label' => esc_html__( 'Title Limit', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default', 'theplus' ),
					'letters' => esc_html__( 'By Letters', 'theplus' ),
					'words' => esc_html__( 'By Words', 'theplus' ),
				],
				'condition' => [
					'layout' => ['carousel'],
					'tlContentFrom' => ['tlrepeater'],
					'caroByheight' => ['text-limit']
				],
			]
		);
		$this->add_control(
			'titleLimit',
			[
				'label' => esc_html__( 'Maximum Letters/Words', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1000,
				'step' => 1,
				'default' => 30,
				'condition' => [
					'layout' => ['carousel'],
					'tlContentFrom' => ['tlrepeater'],
					'caroByheight' => ['text-limit'],
					'titleByLimit!' => 'default',
				],
			]
		);	
		$this->add_control(
			'redmorTxt',
			[
				'label' => esc_html__( 'Read More', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => ['active' => true,],
				'separator' => 'before',
				'default' => esc_html__( 'Read More', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Read More', 'theplus' ),
				'condition' => [
					'tlContentFrom' => ['tlrepeater'],
					'layout' => ['masonry','carousel'],
					'caroByheight' => ['text-limit'],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
						'terms' => [
								['name' => 'descByLimit', 'operator' => '!=', 'value' => 'default']
							]
						],
						[
						'terms' => [
								['name' => 'titleByLimit', 'operator' => '!=', 'value' => 'default']
							]
						],
					]
				],
			]
		);
		$this->add_control(
			'redlesTxt',
			[
				'label' => esc_html__( 'Read Less', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => ['active' => true,],
				'default' => esc_html__( 'Read Less', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Read Less', 'theplus' ),
				'condition' => [
					'tlContentFrom' => ['tlrepeater'],
					'layout' => ['masonry','carousel'],
					'caroByheight' => ['text-limit'],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
						'terms' => [
								['name' => 'descByLimit', 'operator' => '!=', 'value' => 'default']
							]
						],
						[
						'terms' => [
								['name' => 'titleByLimit', 'operator' => '!=', 'value' => 'default']
							]
						],
					]
				],
			]
		);
		$this->end_controls_section();
		/*Post Title*/
		$this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__('Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .testimonial-list .post-content-image .post-title,{{WRAPPER}} .testimonial-list.testimonial-style-4 .post-title',
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
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list .post-content-image .post-title,{{WRAPPER}} .testimonial-list.testimonial-style-4 .post-title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_title_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'title_hover_color',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list .testimonial-list-content:hover .post-title,{{WRAPPER}} .testimonial-list.testimonial-style-4 .testimonial-list-content:hover .post-title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Post Title*/
		/*Post Extra options*/
		$this->start_controls_section(
            'section_extra_title_style',
            [
                'label' => esc_html__('Extra Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'extra_title_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .testimonial-list.testimonial-style-1 .testimonial-list-content .testimonial-author-title,{{WRAPPER}} .testimonial-list.testimonial-style-2 .testimonial-list-content .testimonial-author-title,{{WRAPPER}} .testimonial-list.testimonial-style-3 .testimonial-list-content .testimonial-author-title,{{WRAPPER}} .testimonial-list.testimonial-style-4 .testimonial-author-title',
				'condition'   => [
					'style'    => ['style-1','style-2','style-3','style-4'],
				],
			]
		);
		$this->start_controls_tabs( 'tabs_extra_title_style' );
		
		$this->start_controls_tab(
			'tab_extra_title_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition'   => [
					'style'    => ['style-1','style-2','style-3','style-4'],
				],
			]
		);
		$this->add_control(
			'extra_title_color',
			[
				'label' => esc_html__( 'Extra Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list.testimonial-style-1 .testimonial-list-content .testimonial-author-title,{{WRAPPER}} .testimonial-list.testimonial-style-2 .testimonial-list-content .testimonial-author-title,{{WRAPPER}} .testimonial-list.testimonial-style-3 .testimonial-list-content .testimonial-author-title,{{WRAPPER}} .testimonial-list.testimonial-style-4 .testimonial-author-title' => 'color: {{VALUE}}',
				],
				'condition'   => [
					'style'    => ['style-1','style-2','style-3','style-4'],
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_extra_title_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition'   => [
					'style'    => ['style-1','style-2','style-3','style-4'],
				],
			]
		);
		$this->add_control(
			'extra_title_hover_color',
			[
				'label' => esc_html__( 'Extra Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list.testimonial-style-1 .testimonial-list-content:hover .testimonial-author-title,{{WRAPPER}} .testimonial-list.testimonial-style-2 .testimonial-list-content:hover .testimonial-author-title,{{WRAPPER}} .testimonial-list.testimonial-style-3 .testimonial-list-content:hover .testimonial-author-title,{{WRAPPER}} .testimonial-list.testimonial-style-4 .testimonial-list-content:hover .testimonial-author-title' => 'color: {{VALUE}}',
				],
				'condition'   => [
					'style'    => ['style-1','style-2','style-3','style-4'],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
            'section_designation_style',
            [
                'label' => esc_html__('Designation', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'designation_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .testimonial-list.testimonial-style-1 .post-designation,{{WRAPPER}} .testimonial-list.testimonial-style-2 .post-designation,{{WRAPPER}} .testimonial-list.testimonial-style-3 .post-designation,{{WRAPPER}} .testimonial-list.testimonial-style-4 .post-designation',
			]
		);
		$this->start_controls_tabs( 'tabs_designation_style' );
		$this->start_controls_tab(
			'tab_designation_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'designation_color',
			[
				'label' => esc_html__( 'Designation Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list.testimonial-style-1 .post-designation,{{WRAPPER}} .testimonial-list.testimonial-style-2 .post-designation,{{WRAPPER}} .testimonial-list.testimonial-style-3 .post-designation,{{WRAPPER}} .testimonial-list.testimonial-style-4 .post-designation' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_designation_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'designation_hover_color',
			[
				'label' => esc_html__( 'Designation Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list.testimonial-style-1 .testimonial-list-content:hover .post-designation,{{WRAPPER}} .testimonial-list.testimonial-style-2 .testimonial-list-content:hover .post-designation,{{WRAPPER}} .testimonial-list.testimonial-style-3 .testimonial-list-content:hover .post-designation,{{WRAPPER}} .testimonial-list.testimonial-style-4 .testimonial-list-content:hover .post-designation' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Post Extra options*/
		/*Post Excerpt*/
		$this->start_controls_section(
            'section_excerpt_style',
            [
                'label' => esc_html__('Excerpt/Content', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'excerpt_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .testimonial-list .entry-content',
			]
		);
		$this->start_controls_tabs( 'tabs_excerpt_style' );
		$this->start_controls_tab(
			'tab_excerpt_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'excerpt_color',
			[
				'label' => esc_html__( 'Content Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list .entry-content,{{WRAPPER}} .testimonial-list .entry-content p' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_excerpt_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'excerpt_hover_color',
			[
				'label' => esc_html__( 'Content Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list .testimonial-list-content:hover .entry-content,{{WRAPPER}} .testimonial-list .testimonial-list-content:hover .entry-content p' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Post Excerpt*/
		/*Content Background*/
		$this->start_controls_section(
            'section_content_bg_style',
            [
                'label' => esc_html__('Content Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'content_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .testimonial-list.testimonial-style-1 .testimonial-content-text,{{WRAPPER}} .testimonial-list.testimonial-style-2 .testimonial-list-content,{{WRAPPER}} .testimonial-list.testimonial-style-3 .testimonial-list-content,{{WRAPPER}} .testimonial-list.testimonial-style-4 .testimonial-list-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'content_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .testimonial-list.testimonial-style-1 .testimonial-content-text,{{WRAPPER}} .testimonial-list.testimonial-style-2 .testimonial-list-content,{{WRAPPER}} .testimonial-list.testimonial-style-3 .testimonial-list-content,{{WRAPPER}} .testimonial-list.testimonial-style-4 .testimonial-list-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control('content_bg_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .testimonial-list.testimonial-style-1 .testimonial-content-text,{{WRAPPER}} .testimonial-list.testimonial-style-2 .testimonial-list-content,{{WRAPPER}} .testimonial-list.testimonial-style-3 .testimonial-list-content,{{WRAPPER}} .testimonial-list.testimonial-style-4 .testimonial-list-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .testimonial-list .testimonial-list-content'
			]
		);
		$this->add_responsive_control('box_bg_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'tpebl' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .testimonial-list .testimonial-list-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'tabs_content_bg_style' );
		$this->start_controls_tab(
			'tab_content_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'contnet_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .testimonial-list.testimonial-style-1 .testimonial-content-text,{{WRAPPER}} .testimonial-list.testimonial-style-2 .testimonial-list-content,{{WRAPPER}} .testimonial-list.testimonial-style-3 .testimonial-list-content,{{WRAPPER}} .testimonial-list.testimonial-style-4 .testimonial-list-content',
			]
		);
		$this->add_control(
			'down_arrow_color',
			[
				'label' => esc_html__( 'Down Arrow Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list.testimonial-style-1 .testimonial-content-text:after' => 'border-top-color: {{VALUE}}',
				],
				'condition' => [
					'style' => 'style-1',
				],
			]
		);
		$this->add_control(
			'border_bottom_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list.testimonial-style-3 .testimonial-content-text:after' => 'background: {{VALUE}}',
				],
				'condition' => [
					'style' => 'style-3',
					'style_3_layout' => 'style-1',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_content_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'content_hover_background',
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .testimonial-list.testimonial-style-1 .testimonial-list-content:hover .testimonial-content-text,{{WRAPPER}} .testimonial-list.testimonial-style-2 .testimonial-list-content:hover,{{WRAPPER}} .testimonial-list.testimonial-style-3 .testimonial-list-content:hover,{{WRAPPER}} .testimonial-list.testimonial-style-4 .testimonial-list-content:hover',
			]
		);
		$this->add_control(
			'down_arrow_hover_color',
			[
				'label' => esc_html__( 'Down Arrow Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list.testimonial-style-1 .testimonial-list-content:hover .testimonial-content-text:after' => 'border-top-color: {{VALUE}}',
				],
				'condition' => [
					'style' => 'style-1',
				],
			]
		);
		$this->add_control(
			'border_bottom_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list.testimonial-style-3 .testimonial-list-content:hover .testimonial-content-text:after' => 'background: {{VALUE}}',
				],
				'condition' => [
					'style' => 'style-3',
					'style_3_layout' => 'style-1',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'content_box_shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_content_shadow_style' );
		$this->start_controls_tab(
			'tab_content_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'content_shadow',
				'selector' => '{{WRAPPER}} .testimonial-list.testimonial-style-1 .testimonial-list-content .testimonial-content-text,{{WRAPPER}} .testimonial-list.testimonial-style-2 .testimonial-list-content,{{WRAPPER}} .testimonial-list.testimonial-style-3 .testimonial-list-content,{{WRAPPER}} .testimonial-list.testimonial-style-4 .testimonial-list-content',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_content_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'content_hover_shadow',
				'selector' => '{{WRAPPER}} .testimonial-list.testimonial-style-1 .testimonial-list-content:hover .testimonial-content-text,{{WRAPPER}} .testimonial-list.testimonial-style-2 .testimonial-list-content:hover,{{WRAPPER}} .testimonial-list.testimonial-style-3 .testimonial-list-content:hover,{{WRAPPER}} .testimonial-list.testimonial-style-4 .testimonial-list-content:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Content Background*/
		/*Read More/Less*/
		$this->start_controls_section(
            'section_readML_style',
            [
                'label' => esc_html__('Read More/Less', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'tlContentFrom' => ['tlrepeater'],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
						'terms' => [
								['name' => 'layout', 'operator' => '===', 'value' => 'masonry'],
								['name' => 'descByLimit', 'operator' => '!==', 'value' => 'default']
							]
						],
						[
						'terms' => [
								['name' => 'layout', 'operator' => '===', 'value' => 'carousel'],
								['name' => 'titleByLimit', 'operator' => '!==', 'value' => 'default']
							]
						],
					]
				],
            ]
        );
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name' => 'readTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .testimonial-list .testimonial-content-text .entry-content a.testi-readbtn,{{WRAPPER}} .testimonial-list .testimonial-content-text .entry-content a.testi-readbtn',
			]
		);
		$this->start_controls_tabs( 'tabs_readML_style' );
		$this->start_controls_tab(
			'tab_readML_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'readColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list .testimonial-list-content .entry-content a.testi-readbtn,{{WRAPPER}} .testimonial-list .testimonial-list-content .entry-content a.testi-readbtn' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_readML_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'readmhvrColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list .testimonial-list-content:hover .entry-content a.testi-readbtn,{{WRAPPER}} .testimonial-list .testimonial-list-content:hover .entry-content a.testi-readbtn' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Read More/Less*/
		/*Scroll Bar*/
		$this->start_controls_section(
			'scroll_testi_section',
			[
				'label' => esc_html__( 'Scroll Bar', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
						'terms' => [
								['name' => 'layout', 'operator' => '===', 'value' => 'grid']
							]
						],
						[
						'terms' => [
								['name' => 'layout', 'operator' => '===', 'value' => 'carousel'],
								['name' => 'caroByheight', 'operator' => '===', 'value' => 'height']
							]
						],
					]
				],
			]
		);
		$this->start_controls_tabs( 'scroll_Tl_style' );
		$this->start_controls_tab(
			'scrollTl_Bar',
			[
				'label' => esc_html__( 'Scrollbar', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'  => 'tesSclBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .testimonial-list-content .entry-content::-webkit-scrollbar,{{WRAPPER}} .testimonial-list-content .testimonial-author-title::-webkit-scrollbar',
			]
		);
		$this->add_responsive_control(
			'tesSclWidth',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .testimonial-list-content .entry-content::-webkit-scrollbar,{{WRAPPER}} .testimonial-list-content .testimonial-author-title::-webkit-scrollbar' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'scrollTl_Tmb',
			[
				'label' => esc_html__( 'Thumb', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tesThumbBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .testimonial-list-content .entry-content::-webkit-scrollbar-thumb,{{WRAPPER}} .testimonial-list-content .testimonial-author-title::-webkit-scrollbar-thumb',
			]
		);
		$this->add_responsive_control(
			'tesThumbBrs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .testimonial-list-content .entry-content::-webkit-scrollbar-thumb,{{WRAPPER}} .testimonial-list-content .testimonial-author-title::-webkit-scrollbar-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tesThumbBsw',
				'selector' => '{{WRAPPER}} .testimonial-list-content .entry-content::-webkit-scrollbar-thumb,{{WRAPPER}} .testimonial-list-content .testimonial-author-title::-webkit-scrollbar-thumb',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'scrollTl_Trk',
			[
				'label' => esc_html__( 'Track', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tesTrackBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .testimonial-list-content .entry-content::-webkit-scrollbar-track,{{WRAPPER}} .testimonial-list-content .testimonial-author-title::-webkit-scrollbar-track',
			]
		);
		$this->add_responsive_control(
			'tesTrackBRs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .testimonial-list-content .entry-content::-webkit-scrollbar-track,{{WRAPPER}} .testimonial-list-content .testimonial-author-title::-webkit-scrollbar-track' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tesTrackBsw',
				'selector' => '{{WRAPPER}} .testimonial-list-content .entry-content::-webkit-scrollbar-track,{{WRAPPER}} .testimonial-list-content .testimonial-author-title::-webkit-scrollbar-track',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Scroll Bar*/
		/*Post Featured Image*/
		$this->start_controls_section(
            'section_post_image_style',
            [
                'label' => esc_html__('Featured Image', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		
		$this->add_responsive_control(
			'featured_image_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .testimonial-list.testimonial-style-1 .testimonial-featured-image img,{{WRAPPER}} .testimonial-list.testimonial-style-2 .testimonial-featured-image img,{{WRAPPER}} .testimonial-list.testimonial-style-3 .testimonial-featured-image img,{{WRAPPER}} .testimonial-list.testimonial-style-4 .testimonial-featured-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_image_shadow_style' );
		$this->start_controls_tab(
			'tab_image_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} .testimonial-list.testimonial-style-1 .testimonial-featured-image img,{{WRAPPER}} .testimonial-list.testimonial-style-2 .testimonial-featured-image img,{{WRAPPER}} .testimonial-list.testimonial-style-3 .testimonial-featured-image img,{{WRAPPER}} .testimonial-list.testimonial-style-4 .testimonial-featured-image img',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_image_shadow_hover',
			[
				'label' => esc_html__('Hover', 'theplus'),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_hover_shadow',
				'selector' => '{{WRAPPER}} .testimonial-list.testimonial-style-1 .testimonial-list-content:hover .testimonial-featured-image img,{{WRAPPER}} .testimonial-list.testimonial-style-2 .testimonial-list-content:hover .testimonial-featured-image img,{{WRAPPER}} .testimonial-list.testimonial-style-3 .testimonial-list-content:hover .testimonial-featured-image img,{{WRAPPER}} .testimonial-list.testimonial-style-4 .testimonial-list-content:hover .testimonial-featured-image img',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*carousel option*/
		$this->start_controls_section(
            'section_carousel_options_styling',
            [
                'label' => esc_html__('Carousel Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'layout' => ['carousel']
				],
            ]
        );
		$this->add_control(
			'carousel_unique_id',
			[
				'label' => esc_html__('Unique Carousel ID', 'theplus'),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'separator' => 'after',
				'description' => esc_html__('Keep this blank or Setup Unique id for carousel which you can use with "Carousel Remote" widget.','theplus'),
			]
		);
		$this->add_control(
			'slider_direction',
			[
				'label'   => esc_html__('Slider Mode', 'theplus'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal'  => esc_html__( 'Horizontal', 'theplus' ),
					'vertical' => esc_html__( 'Vertical', 'theplus' ),
				],
			]
		);		
		$this->add_control(
            'slide_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Slide Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 10000,
						'step' => 100,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1500,
				],
            ]
        );
		
		$this->start_controls_tabs( 'tabs_carousel_style' );
		$this->start_controls_tab(
			'tab_carousel_desktop',
			[
				'label' => esc_html__('Desktop', 'theplus'),
			]
		);
		$this->add_control(
			'slider_desktop_column',
			[
				'label'   => esc_html__('Desktop Columns', 'theplus'),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => theplus_carousel_desktop_columns(),
			]
		);
		$this->add_control(
			'steps_slide',
			[
				'label'   => esc_html__('Next Previous', 'theplus'),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'description' => esc_html__( 'Select option of column scroll on previous or next in carousel.','theplus' ),
				'options' => [
					'1'  => esc_html__( 'One Column', 'theplus' ),
					'2' => esc_html__( 'All Visible Columns', 'theplus' ),
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'slider_padding',
			[
				'label' => esc_html__('Slide Padding', 'theplus'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '10',
					'bottom' => '',
					'left' => '10',
					'isLinked' => true,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-initialized .slick-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'slider_draggable',
			[
				'label' => esc_html__('Draggable', 'theplus'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'theplus'),
				'label_off' => esc_html__('Off', 'theplus'),				
				'default' => 'yes',
			]
		);
		$this->add_control(
			'multi_drag',
			[
				'label' => esc_html__('Multi Drag', 'theplus'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Enable', 'theplus'),
				'label_off' => esc_html__('Disable', 'theplus'),				
				'default' => 'no',
				'condition' => [
					'slider_draggable' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_infinite',
			[
				'label' => esc_html__('Infinite Mode', 'theplus'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'theplus'),
				'label_off' => esc_html__('Off', 'theplus'),				
				'default' => 'yes',
			]
		);
		$this->add_control(
			'slider_pause_hover',
			[
				'label' => esc_html__( 'Pause On Hover', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'slider_adaptive_height',
			[
				'label' => esc_html__( 'Adaptive Height', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'slider_animation',
			[
				'label' => esc_html__( 'Animation Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'ease',
				'options' => [
					'ease' => esc_html__( 'With Hold', 'theplus' ),
					'linear' => esc_html__( 'Continuous', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'slider_autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
            'autoplay_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Autoplay Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 500,
						'max' => 10000,
						'step' => 200,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 3000,
				],
				'condition' => [
					'slider_autoplay' => 'yes',
				],
            ]
        );
		
		$this->add_control(
			'slider_dots',
			[
				'label' => esc_html__( 'Show Dots', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'slider_dots_style',
			[
				'label' => esc_html__( 'Dots Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'style-4' => esc_html__( 'Style 4', 'theplus' ),
					'style-5' => esc_html__( 'Style 5', 'theplus' ),
					'style-6' => esc_html__( 'Style 6', 'theplus' ),
					'style-7' => esc_html__( 'Style 7', 'theplus' ),
				],
				'condition' => [
					'slider_dots' => ['yes'],
				],
			]
		);
		$this->add_control(
			'dots_border_color',
			[
				'label' => esc_html__( 'Dots Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-1 li button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-6 li button' => '-webkit-box-shadow:inset 0 0 0 8px {{VALUE}};-moz-box-shadow: inset 0 0 0 8px {{VALUE}};box-shadow: inset 0 0 0 8px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-1 li.slick-active button' => '-webkit-box-shadow:inset 0 0 0 1px {{VALUE}};-moz-box-shadow: inset 0 0 0 1px {{VALUE}};box-shadow: inset 0 0 0 1px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li button' => 'border-color:{{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick ul.slick-dots.style-3 li button' => '-webkit-box-shadow: inset 0 0 0 1px {{VALUE}};-moz-box-shadow: inset 0 0 0 1px {{VALUE}};box-shadow: inset 0 0 0 1px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-3 li.slick-active button' => '-webkit-box-shadow: inset 0 0 0 8px {{VALUE}};-moz-box-shadow: inset 0 0 0 8px {{VALUE}};box-shadow: inset 0 0 0 8px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick ul.slick-dots.style-4 li button' => '-webkit-box-shadow: inset 0 0 0 0px {{VALUE}};-moz-box-shadow: inset 0 0 0 0px {{VALUE}};box-shadow: inset 0 0 0 0px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-1 li button:before' => 'color: {{VALUE}};',
				],
				'condition' => [
					'slider_dots_style' => ['style-1','style-2','style-3','style-5'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'dots_bg_color',
			[
				'label' => esc_html__( 'Dots Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li button,{{WRAPPER}} .list-carousel-slick ul.slick-dots.style-3 li button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-4 li button:before,{{WRAPPER}} .list-carousel-slick .slick-dots.style-5 button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-7 button' => 'background: {{VALUE}};',
				],
				'condition' => [
					'slider_dots_style' => ['style-2','style-3','style-4','style-5','style-7'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'dots_active_border_color',
			[
				'label' => esc_html__( 'Dots Active Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li::after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-4 li.slick-active button' => '-webkit-box-shadow: inset 0 0 0 1px {{VALUE}};-moz-box-shadow: inset 0 0 0 1px {{VALUE}};box-shadow: inset 0 0 0 1px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-6 .slick-active button:after' => 'color: {{VALUE}};',
				],
				'condition' => [
					'slider_dots_style' => ['style-2','style-4','style-6'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'dots_active_bg_color',
			[
				'label' => esc_html__( 'Dots Active Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li::after,{{WRAPPER}} .list-carousel-slick .slick-dots.style-4 li.slick-active button:before,{{WRAPPER}} .list-carousel-slick .slick-dots.style-5 .slick-active button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-7 .slick-active button' => 'background: {{VALUE}};',					
				],
				'condition' => [
					'slider_dots_style' => ['style-2','style-4','style-5','style-7'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
            'dots_top_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Dots Top Padding', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slider.slick-dotted' => 'padding-bottom: {{SIZE}}{{UNIT}};',					
				],				
				'condition'    => [
					'slider_dots' => 'yes',
				],
            ]
        );
		$this->add_control(
			'hover_show_dots',
			[
				'label'   => esc_html__( 'On Hover Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_arrows',
			[
				'label'   => esc_html__( 'Show Arrows', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'slider_arrows_style',
			[
				'label'   => esc_html__( 'Arrows Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'style-4' => esc_html__( 'Style 4', 'theplus' ),
					'style-5' => esc_html__( 'Style 5', 'theplus' ),
					'style-6' => esc_html__( 'Style 6', 'theplus' ),
				],
				'condition'    => [
					'slider_arrows' => ['yes'],
				],
			]
		);
		$this->add_control(
			'arrows_position',
			[
				'label'   => esc_html__( 'Arrows Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top-right',
				'options' => [
					'top-right' => esc_html__( 'Top-Right', 'theplus' ),
					'bottm-left' => esc_html__( 'Bottom-Left', 'theplus' ),
					'bottom-center' => esc_html__( 'Bottom-Center', 'theplus' ),
					'bottom-right' => esc_html__( 'Bottom-Right', 'theplus' ),
				],				
				'condition'    => [
					'slider_arrows' => ['yes'],
					'slider_arrows_style' => ['style-3','style-4'],
				],
			]
		);
		$this->add_control(
			'arrow_bg_color',
			[
				'label' => esc_html__( 'Arrow Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#c44d48',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-6:before,{{WRAPPER}} .list-carousel-slick .slick-next.style-6:before' => 'background: {{VALUE}};',					
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-3','style-4','style-6'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'arrow_icon_color',
			[
				'label' => esc_html__( 'Arrow Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1:before,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-6 .icon-wrap' => 'color: {{VALUE}};',					
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-2 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-2 .icon-wrap:after,{{WRAPPER}} .list-carousel-slick .slick-next.style-2 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-next.style-2 .icon-wrap:after,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5 .icon-wrap:after,{{WRAPPER}} .list-carousel-slick .slick-next.style-5 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-next.style-5 .icon-wrap:after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-2','style-3','style-4','style-5','style-6'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'arrow_hover_bg_color',
			[
				'label' => esc_html__( 'Arrow Hover Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1:hover,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1:hover,{{WRAPPER}} .list-carousel-slick .slick-prev.style-2:hover::before,{{WRAPPER}} .list-carousel-slick .slick-next.style-2:hover::before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:hover:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:hover:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-2','style-3','style-4'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'arrow_hover_icon_color',
			[
				'label' => esc_html__( 'Arrow Hover Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#c44d48',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1:hover:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:hover:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-6:hover .icon-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-2:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-2:hover .icon-wrap::after,{{WRAPPER}} .list-carousel-slick .slick-next.style-2:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-next.style-2:hover .icon-wrap::after,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5:hover .icon-wrap::after,{{WRAPPER}} .list-carousel-slick .slick-next.style-5:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-next.style-5:hover .icon-wrap::after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-2','style-3','style-4','style-5','style-6'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control('arrow_y_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Position Y', 'theplus'),
				'size_units' => ['px','%'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .slick-nav' => 'top: {{SIZE}}{{UNIT}};',					
				],
				'condition' => [
					'slider_arrows' => 'yes',
				],
            ]
        );
		$this->add_control(
			'outer_section_arrow',
			[
				'label' => esc_html__( 'Outer Content Arrow', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition' => [
					'slider_arrows' => 'yes',
					'slider_arrows_style' => ['style-1','style-2','style-5','style-6'],
				],
			]
		);
		$this->add_control(
			'hover_show_arrow',
			[
				'label' => esc_html__( 'On Hover Arrow', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_center_mode',
			[
				'label'   => esc_html__( 'Center Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
            'center_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Padding', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'slider_center_mode' => ['yes'],
				],
            ]
        );
		$this->add_control(
			'slider_center_effects',
			[
				'label' => esc_html__( 'Center Slide Effects', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => theplus_carousel_center_effects(),
				'condition'    => [
					'slider_center_mode' => ['yes'],
				],
			]
		);
		$this->add_control(
            'scale_center_slide',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Slide Scale', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0.3,
						'max' => 2,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slide.slick-current.slick-active.slick-center,
					{{WRAPPER}} .list-carousel-slick .slick-slide.scc-animate' => '-webkit-transform: scale({{SIZE}});-moz-transform:    scale({{SIZE}});-ms-transform:     scale({{SIZE}});-o-transform:      scale({{SIZE}});transform:scale({{SIZE}});opacity:1;',
				],
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects' => 'scale',
				],
            ]
        );
		$this->add_control(
            'scale_normal_slide',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Normal Slide Scale', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0.3,
						'max' => 2,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0.8,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slide' => '-webkit-transform: scale({{SIZE}});-moz-transform:    scale({{SIZE}});-ms-transform:     scale({{SIZE}});-o-transform:      scale({{SIZE}});transform:scale({{SIZE}});transition: .3s all linear;',
				],
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects' => 'scale',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'shadow_active_slide',
				'selector' => '{{WRAPPER}} .list-carousel-slick .slick-slide.slick-current.slick-active.slick-center',
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects' => 'shadow',
				],
			]
			);
		$this->add_control(
            'opacity_normal_slide',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Normal Slide Opacity', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0.1,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0.7,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slide' => 'opacity:{{SIZE}}',
				],
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects!' => 'none',
				],
            ]
        );
		$this->add_control(
			'slider_rows',
			[
				'label' => esc_html__( 'Number Of Rows', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
            'slide_row_top_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Row Top Space', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick[data-slider_rows="2"] .slick-slide > div:last-child,{{WRAPPER}} .list-carousel-slick[data-slider_rows="3"] .slick-slide > div:nth-last-child(-n+2)' => 'padding-top:{{SIZE}}px',
				],
				'condition'    => [
					'slider_rows' => ['2','3'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_carousel_tablet',
			[
				'label' => esc_html__( 'Tablet', 'theplus' ),
			]
		);
		$this->add_control(
			'slider_tablet_column',
			[
				'label' => esc_html__( 'Tablet Columns', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => theplus_carousel_tablet_columns(),
			]
		);
		$this->add_control(
			'tablet_steps_slide',
			[
				'label' => esc_html__( 'Next Previous', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'description' => esc_html__( 'Select option of column scroll on previous or next in carousel.','theplus' ),
				'options' => [
					'1'  => esc_html__( 'One Column', 'theplus' ),
					'2' => esc_html__( 'All Visible Columns', 'theplus' ),
				],
				'separator' => 'after',
			]
		);
		
		$this->add_control(
			'slider_responsive_tablet',
			[
				'label' => esc_html__( 'Responsive Tablet', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'tablet_slider_draggable',
			[
				'label' => esc_html__( 'Draggable', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_infinite',
			[
				'label' => esc_html__( 'Infinite Mode', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
            'tablet_autoplay_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Autoplay Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 500,
						'max' => 10000,
						'step' => 200,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1500,
				],
				'condition' => [
					'slider_responsive_tablet' => 'yes',
					'tablet_slider_autoplay' => 'yes',
				],
            ]
        );
		$this->add_control(
			'tablet_slider_dots',
			[
				'label' => esc_html__( 'Show Dots', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_arrows',
			[
				'label' => esc_html__( 'Show Arrows', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_rows',
			[
				'label' => esc_html__( 'Number Of Rows', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_center_mode',
			[
				'label' => esc_html__( 'Center Mode', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
            'tablet_center_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Padding', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
					'tablet_center_mode' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_carousel_mobile',
			[
				'label' => esc_html__( 'Mobile', 'theplus' ),
			]
		);
		$this->add_control(
			'slider_mobile_column',
			[
				'label'   => esc_html__( 'Mobile Columns', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '2',
				'options' => theplus_carousel_mobile_columns(),
			]
		);
		$this->add_control(
			'mobile_steps_slide',
			[
				'label'   => esc_html__( 'Next Previous', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'description' => esc_html__( 'Select option of column scroll on previous or next in carousel.','theplus' ),
				'options' => [
					'1'  => esc_html__( 'One Column', 'theplus' ),
					'2' => esc_html__( 'All Visible Columns', 'theplus' ),
				],
				'separator' => 'after',
			]
		);
		
		$this->add_control(
			'slider_responsive_mobile',
			[
				'label'   => esc_html__( 'Responsive Mobile', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'mobile_slider_draggable',
			[
				'label'   => esc_html__( 'Draggable', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_infinite',
			[
				'label'   => esc_html__( 'Infinite Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_autoplay',
			[
				'label'   => esc_html__( 'Autoplay', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
            'mobile_autoplay_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Autoplay Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 500,
						'max' => 10000,
						'step' => 200,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1500,
				],
				'condition' => [
					'slider_responsive_mobile' => 'yes',
					'mobile_slider_autoplay' => 'yes',
				],
            ]
        );
		$this->add_control(
			'mobile_slider_dots',
			[
				'label'   => esc_html__( 'Show Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_arrows',
			[
				'label'   => esc_html__( 'Show Arrows', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_rows',
			[
				'label'   => esc_html__( 'Number Of Rows', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_center_mode',
			[
				'label'   => esc_html__( 'Center Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
            'mobile_center_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Padding', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
					'mobile_center_mode' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*carousel option*/
		
		/*post not found options*/
		$this->start_controls_section(
            'section_post_not_found_styling',
            [
                'label' => esc_html__('Post Not Found Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'tlContentFrom' => ['tlcontent']
				],
            ]
        );
		$this->add_responsive_control(
			'pnf_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .theplus-posts-not-found' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pnf_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .theplus-posts-not-found',
			]
		);
		$this->add_control(
			'pnf_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .theplus-posts-not-found' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'pnf_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .theplus-posts-not-found',
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pnf_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-posts-not-found',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'pnf_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-posts-not-found' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
		Group_Control_Box_Shadow::get_type(),
		[
			'name' => 'pnf_shadow',
			'label' => esc_html__( 'Box Shadow', 'theplus' ),
			'selector' => '{{WRAPPER}} .theplus-posts-not-found',
			'separator' => 'before',
		]
		);	
		$this->end_controls_section();
		/*post not found options*/
		
		/*Extra options*/
		$this->start_controls_section(
            'section_extra_options_styling',
            [
                'label' => esc_html__('Extra Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'messy_column',
			[
				'label' => esc_html__( 'Messy Columns', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->start_controls_tabs( 'tabs_extra_option_style' );
		$this->start_controls_tab(
			'tab_column_1',
			[
				'label' => esc_html__( '1', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_1',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 1', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_column_2',
			[
				'label' => esc_html__( '2', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_2',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 2', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_column_3',
			[
				'label' => esc_html__( '3', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_3',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 3', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_column_4',
			[
				'label' => esc_html__( '4', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_4',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 4', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_column_5',
			[
				'label' => esc_html__( '5', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_5',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 5', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_column_6',
			[
				'label' => esc_html__( '6', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_6',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 6', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		$this->end_controls_section();
		/*Extra options*/

		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';
			include THEPLUS_PATH. 'modules/widgets/theplus-needhelp.php';
	}
	
	protected function render() {
        $settings = $this->get_settings_for_display();
		$query = $this->get_query_args();
		$post_name=theplus_testimonial_post_name();
		$taxonomy_name=theplus_testimonial_post_category();
		
		$display_thumbnail=$settings['display_thumbnail'];
		$thumbnail=$settings['thumbnail_size'];
		
		$style=$settings["style"];
		$layout = !empty($settings['layout']) ? $settings['layout'] : 'carousel';
		$post_title_tag=$settings["post_title_tag"];
		$post_category=$settings['post_category'];
		
		$style_3_layout=($settings['style_3_layout']!='') ? 'layout-'.$settings['style_3_layout'] : '';
		$tlContentFrom = !empty($settings['tlContentFrom']) ? $settings['tlContentFrom'] : 'tlcontent';
		$testiAllList = !empty($settings['testiAllList']) ? $settings['testiAllList'] : [];
		
		$content_alignment_4=($settings['content_alignment_4']!='') ? 'content-'.$settings['content_alignment_4'] : '';

		$descByLimit = !empty($settings['descByLimit']) ? $settings['descByLimit'] : 'default';
		$descLimit = !empty($settings['descLimit']) ? $settings['descLimit'] : 30 ;
		$cntscrollOn = !empty($settings['cntscrollOn']) ? $settings['cntscrollOn'] : 'on-hover';
		$caroByheight = !empty($settings['caroByheight']) ? $settings['caroByheight'] : '';
		$titleByLimit = !empty($settings['titleByLimit']) ? $settings['titleByLimit'] : 'default';
		$titleLimit = !empty($settings['titleLimit']) ? $settings['titleLimit'] : 30 ;
		$redmorTxt = !empty($settings['redmorTxt']) ? $settings['redmorTxt'] : '';
		$redlesTxt = !empty($settings['redlesTxt']) ? $settings['redlesTxt'] : '';
		
		/*--On Scroll View Animation ---*/
		$animated_columns='';
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

		//columns
		$desktop_class=$tablet_class=$mobile_class='';
		if($layout!='carousel'){
			$desktop_class='tp-col-lg-'.esc_attr($settings['desktop_column']);
			$tablet_class='tp-col-md-'.esc_attr($settings['tablet_column']);
			$mobile_class='tp-col-sm-'.esc_attr($settings['mobile_column']);
			$mobile_class .=' tp-col-'.esc_attr($settings['mobile_column']);
		}

		//layout
		$layout_attr=$data_class='';
		if($layout!=''){			
			if($layout!='grid'){
				$data_class .=theplus_get_layout_list_class($layout);
				$layout_attr .=theplus_get_layout_list_attr($layout);
			}else{
				$data_class .=' list-isotope';
			}
		}else{
				$data_class .=' list-isotope';
		}
		
		if($layout == 'carousel'){
			$data_class .=' list-carousel-slick ';
		}
		$data_class .=' testimonial-'.$style;

		$readAttr = [];
		$attr = '';
		if($layout == 'masonry' || ( $layout == 'carousel' && $caroByheight == 'text-limit' )){
			
			$readAttr['readMore'] = $redmorTxt;
			$readAttr['readLess'] = $redlesTxt;
			
			$readAttr = htmlspecialchars(json_encode($readAttr), ENT_QUOTES, 'UTF-8');

			$attr = 'data-readData = \'' .$readAttr. '\'';
		}

		$output=$data_attr='';
		//carousel
		if($layout=='carousel'){
			if(!empty($settings["hover_show_dots"]) && $settings["hover_show_dots"]=='yes'){
				$data_class .=' hover-slider-dots';
			}
			if(!empty($settings["hover_show_arrow"]) && $settings["hover_show_arrow"]=='yes'){
				$data_class .=' hover-slider-arrow';
			}
			if(!empty($settings["outer_section_arrow"]) && $settings["outer_section_arrow"]=='yes' && ($settings["slider_arrows_style"]=='style-1' || $settings["slider_arrows_style"]=='style-2' || $settings["slider_arrows_style"]=='style-5' || $settings["slider_arrows_style"]=='style-6')){
				$data_class .=' outer-slider-arrow';
			}
			
			$data_attr .= $this->get_carousel_options();
			if($settings["slider_arrows_style"]=='style-3' || $settings["slider_arrows_style"]=='style-4'){
				$data_class .=' '.$settings["arrows_position"];
			}
			if(($settings["slider_rows"] > 1) || ($settings["tablet_slider_rows"] > 1) || ($settings["mobile_slider_rows"] > 1)){
				$data_class .= ' multi-row';
			}
		}
		$i=1;
		$uid=uniqid("post");
		if(!empty($settings["carousel_unique_id"])){
			$uid="tpca_".$settings["carousel_unique_id"];
			$data_attr .= ' data-carousel-bg-conn="bgcarousel'.esc_attr($settings["carousel_unique_id"]).'"';
			$data_attr .= ' data-connection="tptab_'.esc_attr($settings["carousel_unique_id"]).'"';
		}
		$data_attr .=' data-id="'.esc_attr($uid).'"';
		$data_attr .=' data-style="'.esc_attr($style).'"';

		if(!empty($tlContentFrom) && $tlContentFrom == 'tlrepeater'){
			if(!empty($testiAllList)) {
				$index=1;
				$output .= '<div id="theplus-testimonial-post-list" class="testimonial-list '.esc_attr($uid).' '.esc_attr($data_class).' '.esc_attr($style_3_layout).' '.esc_attr($animated_class).'" '.$layout_attr.' '.$data_attr.' '.$animation_attr.' data-enable-isotope="1">';
					$output .= '<div class="tp-row post-inner-loop '.esc_attr($uid).' '.esc_attr($content_alignment_4).'">';
						foreach($testiAllList as $item) {
							$testiAuthor = !empty($item['testiAuthor']) ? $item['testiAuthor'] : '';
							$testiTitle = !empty($item['testiTitle']) ? $item['testiTitle'] : '';
							$testiLabel = !empty($item['testiLabel']) ? $item['testiLabel'] : '';
							$testiDesign = !empty($item['testiDesign']) ? $item['testiDesign'] : '';
							$testiImage = !empty($item['testiImage']['url']) ? $item['testiImage']['url'] : '';
							$testiImageId = !empty($item['testiImage']['id']) ? $item['testiImage']['id'] : '';
							$testiLogo = !empty($item['testiLogo']['url']) ? $item['testiLogo']['url'] : '';
							$testiLogoId = !empty($item['testiLogo']['id']) ? $item['testiLogo']['id'] : '';
							//grid item loop
							$output .= '<div class="grid-item '.$desktop_class.' '.$tablet_class.' '.$mobile_class.' '.$animated_columns.'">';
								if(!empty($style)){
									ob_start();
										include THEPLUS_PATH. 'includes/testimonial/testimonial-' . sanitize_file_name($style) . '.php'; 
										$output .= ob_get_contents();
									ob_end_clean();
								}
							$output .='</div>';
							$index++;
						}
					$output .='</div>';

				$output .='</div>';
			}
		}else{
		
			if ( ! $query->have_posts() ) {
				$output .='<h3 class="theplus-posts-not-found">'.esc_html__( "Posts not found", "theplus" ).'</h3>';
			}else{
				$output .= '<div id="theplus-testimonial-post-list" class="testimonial-list '.esc_attr($uid).' '.esc_attr($data_class).' '.esc_attr($style_3_layout).' '.esc_attr($animated_class).'" '.$layout_attr.' '.$data_attr.' '.$animation_attr.' data-enable-isotope="1">';
				
				
					$output .= '<div class="tp-row post-inner-loop '.esc_attr($uid).' '.esc_attr($content_alignment_4).'">';
					while ( $query->have_posts() ) {
					
						$query->the_post();
						$post = $query->post;
						
						/**grid item loop*/
						$output .= '<div class="grid-item '.$desktop_class.' '.$tablet_class.' '.$mobile_class.' '.$animated_columns.'">';				
						if(!empty($style)){
							ob_start();
								include THEPLUS_PATH. 'includes/testimonial/testimonial-' . sanitize_file_name($style) . '.php'; 
								$output .= ob_get_contents();
							ob_end_clean();
						}
						$output .='</div>';
						
						$i++;
					}
					$output .='</div>';
				
				$output .='</div>';
			}
		}
		
		$css_rule =$css_messy='';
		if($settings['messy_column']=='yes'){
			$layout='carousel';
			if($layout=='carousel'){
				$desktop_column=$settings['slider_desktop_column'];
				$tablet_column=$settings['slider_tablet_column'];
				$mobile_column=$settings['slider_mobile_column'];
			}
			for($x = 1; $x <= 6; $x++){
				if(!empty($settings["desktop_column_".$x])){
					$desktop=!empty($settings["desktop_column_".$x]["size"]) ? $settings["desktop_column_".$x]["size"].$settings["desktop_column_".$x]["unit"] : '';
					$tablet=!empty($settings["desktop_column_".$x."_tablet"]["size"]) ? $settings["desktop_column_".$x."_tablet"]["size"].$settings["desktop_column_".$x."_tablet"]["unit"] : '';
					$mobile=!empty($settings["desktop_column_".$x."_mobile"]["size"]) ? $settings["desktop_column_".$x."_mobile"]["size"].$settings["desktop_column_".$x."_mobile"]["unit"] : '';
					$css_messy .= theplus_messy_columns($x,$layout,$uid,$desktop,$tablet,$mobile,$desktop_column,$tablet_column,$mobile_column);
				}
			}
			$css_rule ='<style>'.$css_messy.'</style>';
		}
		echo $output.$css_rule;
		
		wp_reset_postdata();
	}
	
    protected function content_template() {
	
    }
	
	protected function get_query_args() {
		$settings = $this->get_settings_for_display();
		$post_name=theplus_testimonial_post_name();
		$taxonomy_name=theplus_testimonial_post_category();
		
		$terms = get_terms( array('taxonomy' => $taxonomy_name, 'hide_empty' => true) );			
		$post_category=$settings['post_category'];
		$category=array();
		if ( !is_wp_error( $terms ) && !empty($terms) && !empty($post_category)){			
			foreach( $terms as $term ) {					
				if(in_array($term->term_id,$post_category)){
					$category[]=$term->slug;
				}
			}
		}
		$query_args = array(
			'post_type'           => $post_name,
			$taxonomy_name		  => $category,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => intval( $settings['display_posts'] ),
			'orderby'      =>  $settings['post_order_by'],
			'order'      => $settings['post_order'],
		);

		$offset = $settings['post_offset'];
		$offset = ! empty( $offset ) ? absint( $offset ) : 0;

		if ( $offset ) {
			$query_args['offset'] = $offset;
		}
		global $paged;
		if ( get_query_var('paged') ) {
			$paged = get_query_var('paged');
		}
		elseif ( get_query_var('page') ) {
			$paged = get_query_var('page');
		}
		else {
			$paged = 1;
		}
		$query_args['paged'] = $paged;
		
		$query = new \WP_Query( $query_args );
		
		return $query;
	}
	
	protected function get_carousel_options() {
		$settings = $this->get_settings_for_display();
		$data_slider ='';
			$slider_direction = ($settings['slider_direction']=='vertical') ? 'true' : 'false';
			$data_slider .=' data-slider_direction="'.esc_attr($slider_direction).'"';
			$data_slider .=' data-slide_speed="'.esc_attr($settings["slide_speed"]["size"]).'"';
			
			$data_slider .=' data-slider_desktop_column="'.esc_attr($settings['slider_desktop_column']).'"';
			$data_slider .=' data-steps_slide="'.esc_attr($settings['steps_slide']).'"';
			
			$slider_draggable= ($settings["slider_draggable"]=='yes') ? 'true' : 'false';
			$multi_drag= ($settings["multi_drag"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_draggable="'.esc_attr($slider_draggable).'"';
			$data_slider .=' data-multi_drag="'.esc_attr($multi_drag).'"';
			$slider_infinite= ($settings["slider_infinite"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_infinite="'.esc_attr($slider_infinite).'"';
			$slider_pause_hover= ($settings["slider_pause_hover"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_pause_hover="'.esc_attr($slider_pause_hover).'"';
			$slider_adaptive_height= ($settings["slider_adaptive_height"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_adaptive_height="'.esc_attr($slider_adaptive_height).'"';
			$slider_animation=$settings['slider_animation'];
			$data_slider .=' data-slider_animation="'.esc_attr($slider_animation).'"';
			$slider_autoplay= ($settings["slider_autoplay"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_autoplay="'.esc_attr($slider_autoplay).'"';
			$data_slider .=' data-autoplay_speed="'.esc_attr(!empty($settings["autoplay_speed"]["size"]) ? $settings["autoplay_speed"]["size"] : 3000).'"';			
			
			//tablet
			$data_slider .=' data-slider_tablet_column="'.esc_attr($settings['slider_tablet_column']).'"';
			$data_slider .=' data-tablet_steps_slide="'.esc_attr($settings['tablet_steps_slide']).'"';
			$slider_responsive_tablet=$settings['slider_responsive_tablet'];
			$data_slider .=' data-slider_responsive_tablet="'.esc_attr($slider_responsive_tablet).'"';
			if(!empty($settings['slider_responsive_tablet']) && $settings['slider_responsive_tablet']=='yes'){				
				$tablet_slider_draggable= ($settings["tablet_slider_draggable"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_draggable="'.esc_attr($tablet_slider_draggable).'"';
				$tablet_slider_infinite= ($settings["tablet_slider_infinite"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_infinite="'.esc_attr($tablet_slider_infinite).'"';
				$tablet_slider_autoplay= ($settings["tablet_slider_autoplay"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_autoplay="'.esc_attr($tablet_slider_autoplay).'"';
				$tablet_autoplay_speed= isset($settings["tablet_autoplay_speed"]["size"]) ? $settings["tablet_autoplay_speed"]["size"] : '1500';
				$data_slider .=' data-tablet_autoplay_speed="'.esc_attr($tablet_autoplay_speed).'"';
				$tablet_slider_dots= ($settings["tablet_slider_dots"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_dots="'.esc_attr($tablet_slider_dots).'"';
				$tablet_slider_arrows= ($settings["tablet_slider_arrows"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_arrows="'.esc_attr($tablet_slider_arrows).'"';
				$data_slider .=' data-tablet_slider_rows="'.esc_attr($settings["tablet_slider_rows"]).'"';
				$tablet_center_mode= ($settings["tablet_center_mode"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_center_mode="'.esc_attr($tablet_center_mode).'" ';
				$data_slider .=' data-tablet_center_padding="'.esc_attr(!empty($settings["tablet_center_padding"]["size"]) ? $settings["tablet_center_padding"]["size"] : 0).'" ';
			}
			
			//mobile 
			$data_slider .=' data-slider_mobile_column="'.esc_attr($settings['slider_mobile_column']).'"';
			$data_slider .=' data-mobile_steps_slide="'.esc_attr($settings['mobile_steps_slide']).'"';
			$slider_responsive_mobile=$settings['slider_responsive_mobile'];			
			$data_slider .=' data-slider_responsive_mobile="'.esc_attr($slider_responsive_mobile).'"';
			if(!empty($settings['slider_responsive_mobile']) && $settings['slider_responsive_mobile']=='yes'){
				$mobile_slider_draggable= ($settings["mobile_slider_draggable"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_draggable="'.esc_attr($mobile_slider_draggable).'"';
				$mobile_slider_infinite= ($settings["mobile_slider_infinite"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_infinite="'.esc_attr($mobile_slider_infinite).'"';
				$mobile_slider_autoplay= ($settings["mobile_slider_autoplay"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_autoplay="'.esc_attr($mobile_slider_autoplay).'"';
				$data_slider .=' data-mobile_autoplay_speed="'.(isset($settings["mobile_autoplay_speed"]["size"]) ? esc_attr($settings["mobile_autoplay_speed"]["size"]) : '1500').'"';
				$mobile_slider_dots= ($settings["mobile_slider_dots"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_dots="'.esc_attr($mobile_slider_dots).'"';
				$mobile_slider_arrows= ($settings["mobile_slider_arrows"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_arrows="'.esc_attr($mobile_slider_arrows).'"';
				$data_slider .=' data-mobile_slider_rows="'.esc_attr($settings["mobile_slider_rows"]).'"';
				$mobile_center_mode= ($settings["mobile_center_mode"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_center_mode="'.esc_attr($mobile_center_mode).'" ';
				$data_slider .=' data-mobile_center_padding="'.(isset($settings["mobile_center_padding"]["size"]) ? esc_attr($settings["mobile_center_padding"]["size"]) : '0').'"';
			}
			
			$slider_dots= ($settings["slider_dots"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_dots="'.esc_attr($slider_dots).'"';
			$data_slider .=' data-slider_dots_style="slick-dots '.esc_attr($settings["slider_dots_style"]).'"';
			
			
			$slider_arrows= ($settings["slider_arrows"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_arrows="'.esc_attr($slider_arrows).'"';
			$data_slider .=' data-slider_arrows_style="'.esc_attr($settings["slider_arrows_style"]).'" ';
			$data_slider .=' data-arrows_position="'.esc_attr($settings["arrows_position"]).'" ';
			$data_slider .=' data-arrow_bg_color="'.esc_attr($settings["arrow_bg_color"]).'" ';
			$data_slider .=' data-arrow_icon_color="'.esc_attr($settings["arrow_icon_color"]).'" ';
			$data_slider .=' data-arrow_hover_bg_color="'.esc_attr($settings["arrow_hover_bg_color"]).'" ';
			$data_slider .=' data-arrow_hover_icon_color="'.esc_attr($settings["arrow_hover_icon_color"]).'" ';
			
			$slider_center_mode= ($settings["slider_center_mode"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_center_mode="'.esc_attr($slider_center_mode).'" ';
			$data_slider .=' data-center_padding="'.esc_attr((!empty($settings["center_padding"]["size"])) ? $settings["center_padding"]["size"] : 0).'" ';
			$data_slider .=' data-scale_center_slide="'.esc_attr((!empty($settings["scale_center_slide"]["size"])) ? $settings["scale_center_slide"]["size"] : 1).'" ';
			$data_slider .=' data-scale_normal_slide="'.esc_attr((!empty($settings["scale_normal_slide"]["size"])) ? $settings["scale_normal_slide"]["size"] : 0.8).'" ';
			$data_slider .=' data-opacity_normal_slide="'.esc_attr((!empty($settings["opacity_normal_slide"]["size"])) ? $settings["opacity_normal_slide"]["size"] : 0.7).'" ';
			
			$data_slider .=' data-slider_rows="'.esc_attr($settings["slider_rows"]).'" ';
		return $data_slider;
	}
}
