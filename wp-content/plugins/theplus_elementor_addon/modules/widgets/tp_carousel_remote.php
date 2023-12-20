<?php
/**
 * Widget Name: Carousel Remote
 * Description: Carousel/Switcher remote button.
 * Author: Theplus
 * Author URI: https://posimyth.com
 *
 *  @package Carousel Remote
 */

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Carousel_Remote Main Elementor Class
 */
class ThePlus_Carousel_Remote extends Widget_Base {

	/**
	 * Document Link
	 *
	 * @var tp_doc
	 */
	public $tp_doc = THEPLUS_TPDOC;

	/**
	 * Widget Name.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function get_name() {
		return 'tp-carousel-remote';
	}

	/**
	 * Widget title.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function get_title() {
		return esc_html__( 'Carousel Remote', 'theplus' );
	}

	/**
	 * Widget Icon.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function get_icon() {
		return 'fa fa-bluetooth-b theplus_backend_icon';
	}

	/**
	 * Widget categories.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function get_categories() {
		return array( 'plus-creatives' );
	}

	/**
	 * Widget search key words
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function get_keywords() {
		return array( 'carousal', 'carousal remote', 'remote', 'horizontal', 'horizontal scroll', 'switcher', 'tp', 'theplus' );
	}

	/**
	 * Widget Help url
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function get_custom_help_url() {
		$doc_url = $this->tp_doc . 'carousel-remote';

		return esc_url( $doc_url );
	}

	/**
	 * Register Carousel controls.
	 *
	 * @since 1.2.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Content', 'theplus' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'carousel_unique_id',
			array(
				'label'       => wp_kses_post( "Unique Connection ID <a class='tp-docs-link' href='" . esc_url( $this->tp_doc ) . "carousel-remote-elementor-widget-settings-overview/' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => esc_html__( 'Enter the value of ID of carousel/Switcher, which you want to remotely connect with this.', 'theplus' ),
			)
		);
		$this->add_control(
			'remote_type',
			array(
				'label'     => esc_html__( 'Remote Type', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'carousel',
				'options'   => array(
					'carousel'   => esc_html__( 'Carousel', 'theplus' ),
					'switcher'   => esc_html__( 'Switcher', 'theplus' ),
					'horizontal' => esc_html__( 'Horizontal Scroll', 'theplus' ),
				),
				'separator' => 'before',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'Nxt_Pre_section',
			array(
				'label' => esc_html__( 'Prev/Next', 'theplus' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'nxtprvbtn',
			array(
				'label'     => esc_html__( 'Next/Prev Button', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'label_on'  => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
			)
		);
		$this->add_responsive_control(
			'prev_next_left',
			array(
				'label'      => esc_html__( 'Position X', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					),
					'vw' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'vw',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}}.cr-horizontal-scroll .theplus-carousel-remote .slider-nav-next-prev' => 'left:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'remote_type' => 'horizontal',
					'nxtprvbtn'   => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'prev_next_top',
			array(
				'label'      => esc_html__( 'Position Y', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					),
					'vh' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'vh',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}}.cr-horizontal-scroll .theplus-carousel-remote .slider-nav-next-prev' => 'top:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'remote_type' => 'horizontal',
					'nxtprvbtn'   => 'yes',
				),

			)
		);
		$this->add_control(
			'nav_next_slide',
			array(
				'label'     => esc_html__( 'Button 1 Text', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Next', 'theplus' ),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_control(
			'nav_prev_slide',
			array(
				'label'     => esc_html__( 'Button 2 Text', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Prev', 'theplus' ),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'content_align',
			array(
				'label'        => esc_html__( 'Alignment', 'theplus' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'condition'    => array(
					'nxtprvbtn'    => 'yes',
					'remote_type!' => 'horizontal',
				),
				'default'      => 'left',
				'prefix_class' => 'text-%s',
			)
		);
		$this->add_control(
			'nav_icon_style',
			array(
				'label'     => esc_html__( 'Icon Style', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'style-1',
				'options'   => array(
					'none'    => esc_html__( 'None', 'theplus' ),
					'style-1' => esc_html__( 'Style 1', 'theplus' ),
					'custom'  => esc_html__( 'Custom', 'theplus' ),
				),
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_control(
			'nav_prev_icon',
			array(
				'label'     => esc_html__( 'Custom Icon 1', 'theplus' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => '',
				),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'nxtprvbtn'      => 'yes',
					'nav_icon_style' => 'custom',
					'remote_type!'   => 'horizontal',
				),
			)
		);
		$this->add_control(
			'nav_next_icon',
			array(
				'label'     => esc_html__( 'Custom Icon 2', 'theplus' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => '',
				),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'nxtprvbtn'      => 'yes',
					'nav_icon_style' => 'custom',
					'remote_type!'   => 'horizontal',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'nav_icon_thumbnail',
				'default'   => 'full',
				'separator' => 'before',
				'condition' => array(
					'nxtprvbtn'      => 'yes',
					'nav_icon_style' => 'custom',
					'remote_type!'   => 'horizontal',
				),
			)
		);
		$this->add_control(
			'prev_icon_hs',
			array(
				'label'     => esc_html__( 'Previous Icon', 'theplus' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-plus',
					'library' => 'solid',
				),
				'condition' => array(
					'nxtprvbtn'      => 'yes',
					'nav_icon_style' => 'custom',
					'remote_type'    => 'horizontal',
				),
			)
		);
			$this->add_control(
				'next_icon_hs',
				array(
					'label'            => esc_html__( 'Next Icon', 'theplus' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'icon',
					'default'          => array(
						'value'   => 'fas fa-star',
						'library' => 'fa-solid',
					),
					'condition'        => array(
						'nxtprvbtn'      => 'yes',
						'nav_icon_style' => 'custom',
						'remote_type'    => 'horizontal',
					),
				)
			);
		$this->end_controls_section();
		/*Dots Start*/
		$this->start_controls_section(
			'section_dot',
			array(
				'label'     => esc_html__( 'Dots', 'theplus' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'remote_type!' => 'switcher',
				),
			)
		);
		$this->add_control(
			'dotList',
			array(
				'label'     => esc_html__( 'Dots', 'theplus' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default'   => 'no',
			)
		);
		$this->add_responsive_control(
			'nav_dot_left',
			array(
				'label'      => esc_html__( 'Position X', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					),
					'vw' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'vw',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}}.cr-horizontal-scroll .theplus-carousel-remote .tp-carousel-dots' => 'left:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'dotList'     => 'yes',
					'remote_type' => 'horizontal',
				),
			)
		);
		$this->add_responsive_control(
			'nav_dot_top',
			array(
				'label'      => esc_html__( 'Position Y', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					),
					'vh' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'vh',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}}.cr-horizontal-scroll .theplus-carousel-remote .tp-carousel-dots' => 'top:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'dotList'     => 'yes',
					'remote_type' => 'horizontal',
				),
			)
		);
		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'label',
			array(
				'label'   => esc_html__( 'Label', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Label', 'theplus' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$repeater->add_control(
			'iconFonts',
			array(
				'label'   => esc_html__( 'Select Icon', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'font_awesome',
				'options' => array(
					'none'         => esc_html__( 'None', 'theplus' ),
					'font_awesome' => esc_html__( 'Font Awesome', 'theplus' ),
					'image'        => esc_html__( 'Image', 'theplus' ),
				),
			)
		);
		$repeater->add_control(
			'iddd',
			array(
				'label' => esc_html__( 'Section ID', 'theplus' ),
				'type'  => Controls_Manager::TEXT,
				'title' => 'Only Required for Horizontal Scroll Widget',
			)
		);
		$repeater->add_control(
			'iconName',
			array(
				'label'     => esc_html__( 'Icon Library', 'theplus' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-plus',
					'library' => 'solid',
				),
				'condition' => array(
					'iconFonts' => 'font_awesome',
				),
			)
		);
		$repeater->add_control(
			'iconImage',
			array(
				'label'      => esc_html__( 'Use Image As icon', 'theplus' ),
				'type'       => Controls_Manager::MEDIA,
				'default'    => array(
					'url' => '',
				),
				'media_type' => 'image',
				'dynamic'    => array( 'active' => true ),
				'condition'  => array(
					'iconFonts' => 'image',
				),
			)
		);
		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'iconimageSize',
				'default'   => 'full',
				'separator' => 'after',
				'condition' => array(
					'iconFonts' => 'image',
				),
			)
		);
		$repeater->start_controls_tabs( 'tabs_dot' );
		$repeater->start_controls_tab(
			'tab_dot_normal',
			array(
				'label'     => esc_html__( 'Normal', 'theplus' ),
				'condition' => array(
					'iconFonts!' => 'none',
				),
			)
		);
		$repeater->add_control(
			'doticonColor',
			array(
				'label'       => esc_html__( 'Color', 'theplus' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '',
				'render_type' => 'ui',
				'selectors'   => array(
					'{{WRAPPER}} .theplus-carousel-remote .tp-carodots-item{{CURRENT_ITEM}}' => 'color: {{VALUE}}',
				),
				'condition'   => array(
					'iconFonts!' => 'none',
				),
			)
		);
		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'        => 'dotBgtype',
				'label'       => esc_html__( 'Background', 'theplus' ),
				'types'       => array( 'classic', 'gradient' ),
				'render_type' => 'ui',
				'selector'    => '{{WRAPPER}} .theplus-carousel-remote .tp-carodots-item{{CURRENT_ITEM}}',
				'condition'   => array(
					'iconFonts!' => 'none',
				),
			)
		);
		$repeater->end_controls_tab();
		$repeater->start_controls_tab(
			'tab_dot_active',
			array(
				'label'     => esc_html__( 'Active', 'theplus' ),
				'condition' => array(
					'iconFonts!' => 'none',
				),
			)
		);
		$repeater->add_control(
			'acticonColor',
			array(
				'label'       => esc_html__( 'Color', 'theplus' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '',
				'render_type' => 'ui',
				'selectors'   => array(
					'{{WRAPPER}} .theplus-carousel-remote .tp-carodots-item{{CURRENT_ITEM}}.active' => 'color: {{VALUE}}',
				),
				'condition'   => array(
					'iconFonts!' => 'none',
				),
			)
		);
		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'        => 'actdotBgtype',
				'label'       => esc_html__( 'Background', 'theplus' ),
				'types'       => array( 'classic', 'gradient' ),
				'render_type' => 'ui',
				'selector'    => '{{WRAPPER}} .theplus-carousel-remote .tp-carodots-item{{CURRENT_ITEM}}.active',
				'condition'   => array(
					'iconFonts!' => 'none',
				),
			)
		);
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
		$this->add_control(
			'dots_coll',
			array(
				'label'       => '',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'label'    => esc_html__( 'Dot 1', 'theplus' ),
						'iconName' => 'fas fa-plus',
					),
					array(
						'label'    => esc_html__( 'Dot 2', 'theplus' ),
						'iconName' => 'fas fa-plus',
					),
					array(
						'label'    => esc_html__( 'Dot 3', 'theplus' ),
						'iconName' => 'fas fa-plus',
					),
				),
				'title_field' => '{{{ label }}}',
				'condition'   => array(
					'dotList' => 'yes',
				),
			)
		);
		$this->add_control(
			'dotLayout',
			array(
				'label'     => esc_html__( 'Layout', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'horizontal',
				'options'   => array(
					'horizontal' => esc_html__( 'Horizontal', 'theplus' ),
					'vertical'   => esc_html__( 'Vertical', 'theplus' ),
				),
				'condition' => array(
					'dotList' => 'yes',
				),
			)
		);
		$this->add_control(
			'dotstyle',
			array(
				'label'     => esc_html__( 'Active Dot Style', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'style-1',
				'options'   => array(
					'style-1' => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
				),
				'condition' => array(
					'dotList' => 'yes',
				),
			)
		);
		$this->add_control(
			'AniDuration',
			array(
				'label'     => esc_html__( 'Duration (milliseconds)', 'theplus' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 10000,
				'step'      => 100,
				'selectors' => array(
					'{{WRAPPER}} .tp-carousel-dots .style-1.active .active-border .border' => 'animation-duration: {{VALUE}}ms',
				),
				'condition' => array(
					'dotList'      => 'yes',
					'dotstyle'     => 'style-1',
					'remote_type!' => 'horizontal',
				),
			)
		);
		$this->add_control(
			'AborderColor',
			array(
				'label'     => esc_html__( 'Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'condition' => array(
					'dotList'      => 'yes',
					'dotstyle'     => 'style-1',
					'remote_type!' => 'horizontal',
				),
			)
		);
		$this->add_control(
			'tooltipDir',
			array(
				'label'     => esc_html__( 'Tooltip Direction', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top',
				'options'   => array(
					'top'    => esc_html__( 'Top', 'theplus' ),
					'bottom' => esc_html__( 'Bottom', 'theplus' ),
				),
				'condition' => array(
					'dotList'   => 'yes',
					'dotLayout' => 'horizontal',
					'dotstyle'  => 'style-2',
				),
			)
		);
		$this->add_control(
			'vtooltipDir',
			array(
				'label'     => esc_html__( 'Tooltip Direction', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => array(
					'left'  => esc_html__( 'Left', 'theplus' ),
					'right' => esc_html__( 'Right', 'theplus' ),
				),
				'condition' => array(
					'dotList'   => 'yes',
					'dotLayout' => 'vertical',
					'dotstyle'  => 'style-2',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'progress_bar',
			array(
				'label'     => esc_html__( 'Progress Bar', 'theplus' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'remote_type' => 'horizontal',
				),
			)
		);
		$this->add_control(
			'progressBar',
			array(
				'label'     => esc_html__( 'Progress Bar', 'theplus' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default'   => '',
				'condition' => array(
					'remote_type' => 'horizontal',
				),
			)
		);
		$this->add_control(
			'pbLayout',
			array(
				'label'     => esc_html__( 'Layout', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'horizontal',
				'options'   => array(
					'horizontal' => esc_html__( 'Horizontal', 'theplus' ),
					'vertical'   => esc_html__( 'Vertical', 'theplus' ),
				),
				'condition' => array(
					'progressBar' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'Pbar_offset_top',
			array(
				'label'      => esc_html__( 'Offset', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'vh','px' ),
				'range'      => array(
					'vh' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'vh',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}}.cr-horizontal-scroll .progress-container' => 'top: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar' => 'yes',
					'pbLayout'    => 'horizontal',
				),
			)
		);
		$this->add_responsive_control(
			'Pbar_offset_left',
			array(
				'label'      => esc_html__( 'Offset', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'vw','px' ),
				'range'      => array(
					'vw' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'vw',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}}.cr-horizontal-scroll .progress-container.vertical' => 'left: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar' => 'yes',
					'pbLayout'    => 'vertical',
				),
			)
		);
		$this->end_controls_section();

		/**Paginate horizontal*/
		$this->start_controls_section(
			'paginate_hs',
			array(
				'label'     => esc_html__( 'Paginate', 'theplus' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'remote_type' => 'horizontal',
				),
			)
		);
		$this->add_control(
			'pagination',
			array(
				'label'     => esc_html__( 'Paginate', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'default'   => '',
				'separator' => 'before',
			)
		);
		$this->add_control(
			'pagination_position_y',
			array(
				'label'     => esc_html__( 'Position Y', 'theplus' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'pg_top',
			array(
				'label'      => esc_html__( 'Offset', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'vh', 'px' ),
				'range'      => array(
					'vh' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'vh',
					'size' => 30,
				),
				'selectors'  => array(
					'{{WRAPPER}}.cr-horizontal-scroll .tp-hscroll-pagination' => 'top:{{SIZE}}{{UNIT}};bottom:auto;',
				),
				'condition'  => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_control(
			'pagination_position_x',
			array(
				'label'     => esc_html__( 'Position X', 'theplus' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'pg_right',
			array(
				'label'      => esc_html__( 'Offset Right', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'vw', 'px' ),
				'range'      => array(
					'vw' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'vw',
					'size' => 30,
				),
				'selectors'  => array(
					'{{WRAPPER}}.cr-horizontal-scroll .tp-hscroll-pagination' => 'left:{{SIZE}}{{UNIT}};right:auto;',
				),
				'condition'  => array(
					'pagination' => 'yes',
				),
			)
		);

		$this->end_controls_section();
		/*Prev/Next style start*/
		$this->start_controls_section(
			'section_PrevNext_styling',
			array(
				'label'     => esc_html__( 'Prev/Next', 'theplus' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'nxtprvbtn'    => 'yes',
					'remote_type!' => 'horizontal',
				),
			)
		);
		$this->add_control(
			'section_Icon_styling',
			array(
				'label'     => esc_html__( 'Icon', 'theplus' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'nxtprvbtn'       => 'yes',
					'nav_icon_style!' => 'none',
				),
			)
		);
		$this->add_responsive_control(
			'icon_size',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => esc_html__( 'Icon Size', 'theplus' ),
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 14,
				),
				'render_type' => 'ui',
				'selectors'   => array(
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev a.custom-nav-remote > span.nav-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev a.custom-nav-remote > span.nav-icon img' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'icon_space',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => esc_html__( 'Icon Space', 'theplus' ),
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 40,
						'step' => 1,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 5,
				),
				'render_type' => 'ui',
				'selectors'   => array(
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev a.custom-nav-remote.nav-prev-slide > span.nav-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev a.custom-nav-remote.nav-next-slide > span.nav-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->start_controls_tabs( 'tabs_icon_style' );
		$this->start_controls_tab(
			'tab_icon_normal',
			array(
				'label' => esc_html__( 'Normal', 'theplus' ),
			)
		);
		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev a.custom-nav-remote > span.nav-icon' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_icon_hover',
			array(
				'label' => esc_html__( 'Hover/Active', 'theplus' ),
			)
		);
		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev .custom-nav-remote:hover > span.nav-icon,{{WRAPPER}} .theplus-carousel-remote.remote-switcher .slider-nav-next-prev .custom-nav-remote.active  > span.nav-icon' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'section_styling',
			array(
				'label'     => esc_html__( 'Button Style', 'theplus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'button_between_space',
			array(
				'label'      => esc_html__( 'Gap/Space', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 5,
				),
				'selectors'  => array(
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev a.custom-nav-remote.nav-prev-slide' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev a.custom-nav-remote.nav-next-slide' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'nav_inner_padding',
			array(
				'label'      => esc_html__( 'Padding', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => '10',
					'right'  => '20',
					'bottom' => '10',
					'left'   => '20',
				),
				'selectors'  => array(
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev .custom-nav-remote' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'content_typography',
				'label'    => esc_html__( 'Typography', 'theplus' ),
				'global'   => array(
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev .custom-nav-remote',
			)
		);
		$this->start_controls_tabs( 'tabs_nav_style' );
		$this->start_controls_tab(
			'tab_nav_normal',
			array(
				'label' => esc_html__( 'Normal', 'theplus' ),
			)
		);
		$this->add_control(
			'nav_color',
			array(
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev .custom-nav-remote' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'box_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev .custom-nav-remote',

			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_nav_hover',
			array(
				'label' => esc_html__( 'Hover/Active', 'theplus' ),
			)
		);
		$this->add_control(
			'nav_hover_color',
			array(
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev .custom-nav-remote:hover,{{WRAPPER}} .theplus-carousel-remote.remote-switcher .slider-nav-next-prev .custom-nav-remote.active' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'box_hover_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev .custom-nav-remote:hover,{{WRAPPER}} .theplus-carousel-remote.remote-switcher .slider-nav-next-prev .custom-nav-remote.active',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'box_border',
			array(
				'label'     => esc_html__( 'Box Border', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default'   => 'no',
			)
		);
		$this->add_control(
			'button_border_style',
			array(
				'label'     => esc_html__( 'Border Style', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => array(
					'none'   => esc_html__( 'None', 'theplus' ),
					'solid'  => esc_html__( 'Solid', 'theplus' ),
					'dotted' => esc_html__( 'Dotted', 'theplus' ),
					'dashed' => esc_html__( 'Dashed', 'theplus' ),
					'groove' => esc_html__( 'Groove', 'theplus' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev .custom-nav-remote' => 'border-style: {{VALUE}};',
				),
				'condition' => array(
					'box_border' => 'yes',
				),
			)
		);
		$this->start_controls_tabs( 'tabs_border_style' );
		$this->start_controls_tab(
			'tab_border_normal',
			array(
				'label' => esc_html__( 'Normal', 'theplus' ),
			)
		);
		$this->add_control(
			'box_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#252525',
				'selectors' => array(
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev .custom-nav-remote' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'box_border' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'box_border_width',
			array(
				'label'      => esc_html__( 'Border Width', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				),
				'selectors'  => array(
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev .custom-nav-remote' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'box_border' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev .custom-nav-remote' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev .custom-nav-remote',
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_border_hover',
			array(
				'label' => esc_html__( 'Hover/Active', 'theplus' ),
			)
		);
		$this->add_control(
			'box_border_hover_color',
			array(
				'label'     => esc_html__( 'Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#252525',
				'selectors' => array(
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev .custom-nav-remote:hover,{{WRAPPER}} .theplus-carousel-remote.remote-switcher .slider-nav-next-prev .custom-nav-remote.active' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'box_border' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'border_hover_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev .custom-nav-remote:hover,{{WRAPPER}} .theplus-carousel-remote.remote-switcher .slider-nav-next-prev .custom-nav-remote.active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_hover_shadow',
				'selector' => '{{WRAPPER}} .theplus-carousel-remote .slider-nav-next-prev .custom-nav-remote:hover,{{WRAPPER}} .theplus-carousel-remote.remote-switcher .slider-nav-next-prev .custom-nav-remote.active',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Prev/Next style End*/
		/**Prev - next horizontal start*/
		$this->start_controls_section(
			'Navigation_icon_style',
			array(
				'label'     => esc_html__( 'Navigation Icon', 'theplus' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_control(
			'prevIconstyling',
			array(
				'label' => esc_html__( 'Previous Icon', 'theplus' ),
				'type'  => Controls_Manager::HEADING,
			)
		);
		$this->add_responsive_control(
			'prevIcon_space',
			array(
				'label'      => esc_html__( 'Space (px)', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-prev-slide .nav-icon' => 'margin-right:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'prevIcon_size',
			array(
				'label'      => esc_html__( 'Size (px)', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-prev-slide .nav-icon' => 'font-size:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->start_controls_tabs( 'prevStyle_tabs' );

		$this->start_controls_tab(
			'prev_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'theplus' ),
			)
		);

		$this->add_control(
			'prev_icon_color',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-prev-slide .nav-icon' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'prev_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'theplus' ),
			)
		);

		$this->add_control(
			'prev_icon_hover',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-prev-slide:hover .nav-icon' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'nextIconstyling',
			array(
				'label'     => esc_html__( 'Next Icon', 'theplus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_responsive_control(
			'nextIcon_space',
			array(
				'label'      => esc_html__( 'Space (px)', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-next-slide .nav-icon' => 'margin-left:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'nextIcon_size',
			array(
				'label'      => esc_html__( 'Size (px)', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-next-slide .nav-icon' => 'font-size:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		// */
		$this->end_controls_tab();

		$this->start_controls_tabs( 'nextStyle_tabs' );

		$this->start_controls_tab(
			'next_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'theplus' ),
			)
		);

		$this->add_control(
			'next_icon_color',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-next-slide .nav-icon' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'nexthover_tab',
			array(
				'label' => esc_html__( 'Hover', 'theplus' ),
			)
		);

		$this->add_control(
			'next_icon_hover',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-next-slide:hover .nav-icon' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section(
			'Navigation_arrow_style',
			array(
				'label'     => esc_html__( 'Navigation Button', 'theplus' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_control(
			'prev_styling',
			array(
				'label' => esc_html__( 'Previous Button', 'theplus' ),
				'type'  => Controls_Manager::HEADING,
			)
		);
		$this->add_responsive_control(
			'prev_arrow_position',
			array(
				'label'      => esc_html__( 'Position', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'vw', 'px' ),
				'range'      => array(
					'vw' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'vw',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-prev-slide' => 'left:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pretypography',
				'selector'  => '{{WRAPPER}} .slider-nav-next-prev .nav-prev-slide .prev-text',
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->start_controls_tabs( 'pre_style_tabs' );
		$this->start_controls_tab(
			'nav_pre_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'theplus' ),
			)
		);
		$this->add_control(
			'pre_arr_color',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-prev-slide .prev-text' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_control(
			'pre_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-prev-slide' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'normal_pre_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .slider-nav-next-prev .nav-prev-slide',
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'pre_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'theplus' ),
			)
		);
		$this->add_control(
			'pre_arr_hover',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-prev-slide:hover .prev-text' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_control(
			'pre_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-prev-slide:hover' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'hov_pre_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .slider-nav-next-prev .nav-prev-slide:hover',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'preborder',
				'label'     => esc_html__( 'Border Type', 'theplus' ),
				'selector'  => '{{WRAPPER}} .slider-nav-next-prev .nav-prev-slide',
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'pre_bg_border',
			array(
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-prev-slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'pre_padding',
			array(
				'label'      => esc_html__( 'Padding', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-prev-slide .prev-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);

		$this->add_control(
			'next_styling',
			array(
				'label'     => esc_html__( 'Next Button', 'theplus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_responsive_control(
			'next_arrow_position',
			array(
				'label'      => esc_html__( 'Position', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'vw', 'px' ),
				'range'      => array(
					'vw' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'vw',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-next-slide' => 'left:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'nexttypography',
				'selector'  => '{{WRAPPER}} .slider-nav-next-prev .nav-next-slide .next-text',
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->start_controls_tabs( 'next_style_tabs' );
		$this->start_controls_tab(
			'nav_next_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'theplus' ),
			)
		);
		$this->add_control(
			'next_arr_color',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-next-slide .next-text' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_control(
			'next_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-next-slide' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'normal_next_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .slider-nav-next-prev .nav-next-slide',
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'next_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'theplus' ),
			)
		);
		$this->add_control(
			'next_arr_hover',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-next-slide:hover .next-text' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_control(
			'next_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-next-slide:hover' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'hov_next_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .slider-nav-next-prev .nav-next-slide:hover',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'nextborder',
				'label'     => esc_html__( 'Border Type', 'theplus' ),
				'selector'  => '{{WRAPPER}} .slider-nav-next-prev .nav-next-slide',
				'condition' => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'next_bg_border',
			array(
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-next-slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'next_padding',
			array(
				'label'      => esc_html__( 'Padding', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .slider-nav-next-prev .nav-next-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'nxtprvbtn' => 'yes',
				),
			)
		);
		$this->end_controls_section();
		/*General style start*/
		$this->start_controls_section(
			'section_general_styling',
			array(
				'label'     => esc_html__( 'Dots', 'theplus' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'remote_type' => array( 'carousel', 'horizontal' ),
					'dotList'     => 'yes',
				),
			)
		);
		$this->add_control(
			'section_dots_styling',
			array(
				'label'     => esc_html__( 'Dots', 'theplus' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'dotList' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'dotsSize',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => esc_html__( 'Size', 'theplus' ),
				'size_units'  => array( 'px','%' ),
				'range'       => array(
					'px' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
				),
				'render_type' => 'ui',
				'selectors'   => array(
					'{{WRAPPER}} .tp-carousel-dots .tp-carodots-item' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				),
			)
		);
		$this->add_responsive_control(
			'dotsGap',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => esc_html__( 'Gap', 'theplus' ),
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					),
				),
				'render_type' => 'ui',
				'selectors'   => array(
					'{{WRAPPER}} .tp-carousel-dots.dot-vertical .tp-carodots-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tp-carousel-dots.dot-horizontal .tp-carodots-item' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'dotsIconSize',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => esc_html__( 'Icon Size', 'theplus' ),
				'size_units'  => array( 'px','%' ),
				'range'       => array(
					'px' => array(
						'min'  => 1,
						'max'  => 300,
						'step' => 1,
					),
					'%' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
				),
				'render_type' => 'ui',
				'selectors'   => array(
					'{{WRAPPER}} .tp-carodots-item .tp-dots i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tp-carousel-dots .tp-carodots-item >div>svg:first-child' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'dotsImageSize',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => esc_html__( 'Image Size', 'theplus' ),
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min'  => 1,
						'max'  => 300,
						'step' => 1,
					),
				),
				'render_type' => 'ui',
				'separator'   => 'after',
				'selectors'   => array(
					'{{WRAPPER}} .tp-carodots-item .tp-dots img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->start_controls_tabs( 'tabs_dotsb_style' );
		$this->start_controls_tab(
			'tab_dotsb_normal',
			array(
				'label' => esc_html__( 'Normal', 'theplus' ),
			)
		);
		$this->add_responsive_control(
			'dotsbr',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => esc_html__( 'Border Radius', 'theplus' ),
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min'  => 1,
						'max'  => 50,
						'step' => 1,
					),
				),
				'render_type' => 'ui',
				'selectors'   => array(
					'{{WRAPPER}} .tp-carousel-dots .tp-carodots-item' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tp-carousel-dots .tp-carodots-item img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_dotsb_hover',
			array(
				'label' => esc_html__( 'Active', 'theplus' ),
			)
		);
		$this->add_responsive_control(
			'dotsbra',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => esc_html__( 'Border Radius', 'theplus' ),
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min'  => 1,
						'max'  => 50,
						'step' => 1,
					),
				),
				'render_type' => 'ui',
				'selectors'   => array(
					'{{WRAPPER}} .tp-carousel-dots .tp-carodots-item.active' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tp-carousel-dots .tp-carodots-item.active img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'section_tooltip_styling',
			array(
				'label'     => esc_html__( 'Tooltip Style', 'theplus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'dotList'  => 'yes',
					'dotstyle' => 'style-2',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'tttypography',
				'selector'  => '{{WRAPPER}} .tp-carodots-item .tooltip-txt',
				'condition' => array(
					'dotList'  => 'yes',
					'dotstyle' => 'style-2',
				),
			)
		);
		$this->add_control(
			'ttcolor',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tp-carodots-item .tooltip-txt' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'dotList'  => 'yes',
					'dotstyle' => 'style-2',
				),
			)
		);
		$this->add_control(
			'ttbgcolor',
			array(
				'label'     => esc_html__( 'Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tp-carodots-item .tooltip-txt' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .tp-carodots-item .tooltip-txt:after' => 'border-right-color: {{VALUE}};',
				),
				'condition' => array(
					'dotList'  => 'yes',
					'dotstyle' => 'style-2',
				),
			)
		);
		$this->add_responsive_control(
			'ttwidth',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => esc_html__( 'Width', 'theplus' ),
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min'  => 1,
						'max'  => 300,
						'step' => 1,
					),
				),
				'render_type' => 'ui',
				'selectors'   => array(
					'{{WRAPPER}} .tp-carodots-item .tooltip-txt' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'dotList'  => 'yes',
					'dotstyle' => 'style-2',
				),
			)
		);
		$this->add_responsive_control(
			'ttoffset',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => esc_html__( 'Offset', 'theplus' ),
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min'  => -250,
						'max'  => 250,
						'step' => 1,
					),
				),
				'render_type' => 'ui',
				'selectors'   => array(
					'{{WRAPPER}} .tp-carousel-dots .style-2 .tooltip-top .tooltip-txt' => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tp-carousel-dots .style-2 .tooltip-bottom .tooltip-txt' => 'bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tp-carousel-dots .style-2 .tooltip-right .tooltip-txt' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tp-carousel-dots .style-2 .tooltip-left .tooltip-txt' => 'left: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'dotList'  => 'yes',
					'dotstyle' => 'style-2',
				),
			)
		);
		$this->end_controls_section();
		/**General Style end*/
		/**Pagination Start*/
		$this->start_controls_section(
			'section_pagination_style',
			array(
				'label'     => esc_html__( 'Paginate', 'theplus' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'remote_type!' => array( 'switcher', 'horizontal' ),
				),
			)
		);
		$this->add_control(
			'showpagi',
			array(
				'label'     => esc_html__( 'Pagination', 'theplus' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default'   => '',
			)
		);
		$this->add_control(
			'sliderInd',
			array(
				'label'     => esc_html__( 'Total Slides', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '3',
				'options'   => array(
					'1'  => esc_html__( '1', 'theplus' ),
					'2'  => esc_html__( '2', 'theplus' ),
					'3'  => esc_html__( '3', 'theplus' ),
					'4'  => esc_html__( '4', 'theplus' ),
					'5'  => esc_html__( '5', 'theplus' ),
					'6'  => esc_html__( '6', 'theplus' ),
					'7'  => esc_html__( '7', 'theplus' ),
					'8'  => esc_html__( '8', 'theplus' ),
					'9'  => esc_html__( '9', 'theplus' ),
					'10' => esc_html__( '10', 'theplus' ),
					'11' => esc_html__( '11', 'theplus' ),
					'12' => esc_html__( '12', 'theplus' ),
					'13' => esc_html__( '13', 'theplus' ),
					'14' => esc_html__( '14', 'theplus' ),
					'15' => esc_html__( '15', 'theplus' ),
				),
				'condition' => array(
					'showpagi' => 'yes',
				),
			)
		);
		$this->start_controls_tabs( 'tabs_pagination' );
		$this->start_controls_tab(
			'tab_pagination_total',
			array(
				'label'     => esc_html__( 'Total', 'theplus' ),
				'condition' => array(
					'showpagi' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'noTypo',
				'label'     => esc_html__( 'Typography', 'theplus' ),
				'global'    => array(
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector'  => '{{WRAPPER}} .theplus-carousel-remote .carousel-pagination li.pagination-list-in.total,
				{{WRAPPER}} .theplus-carousel-remote .carousel-pagination li.pagination-list-in.separator',
				'condition' => array(
					'showpagi' => 'yes',
				),
			)
		);
		$this->add_control(
			'noColor',
			array(
				'label'       => esc_html__( 'Color', 'theplus' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '',
				'render_type' => 'ui',
				'selectors'   => array(
					'{{WRAPPER}} .theplus-carousel-remote .carousel-pagination li.pagination-list-in.total,
				{{WRAPPER}} .theplus-carousel-remote .carousel-pagination li.pagination-list-in.separator' => 'color: {{VALUE}}',
				),
				'condition'   => array(
					'showpagi' => 'yes',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_pagination_active',
			array(
				'label'     => esc_html__( 'Active', 'theplus' ),
				'condition' => array(
					'showpagi' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'ActnoTypo',
				'label'     => esc_html__( 'Typography', 'theplus' ),
				'global'    => array(
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector'  => '{{WRAPPER}} .theplus-carousel-remote .carousel-pagination li.pagination-list-in.active',
				'condition' => array(
					'showpagi' => 'yes',
				),
			)
		);
		$this->add_control(
			'ActnoColor',
			array(
				'label'       => esc_html__( 'Color', 'theplus' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '',
				'render_type' => 'ui',
				'selectors'   => array(
					'{{WRAPPER}} .theplus-carousel-remote .carousel-pagination li.pagination-list-in.active' => 'color: {{VALUE}}',
				),
				'condition'   => array(
					'showpagi' => 'yes',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'sepColor',
			array(
				'label'       => esc_html__( 'Seprator Color', 'theplus' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '',
				'render_type' => 'ui',
				'selectors'   => array(
					'{{WRAPPER}} .theplus-carousel-remote .carousel-pagination li.pagination-list-in.separator' => 'color: {{VALUE}}',
				),
				'condition'   => array(
					'showpagi' => 'yes',
				),
			)
		);
		$this->end_controls_section();
		/**Pagination End*/
		/**Progress bar style*/
		$this->start_controls_section(
			'progress_bar_style',
			array(
				'label'     => esc_html__( 'Progress Bar', 'theplus' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'progressBar' => 'yes',
				),
			)
		);
		$this->add_control(
			'progress_styles',
			array(
				'label'     => esc_html__( 'Styles', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'style-1',
				'options'   => array(
					'style-1' => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
				),
				'separator' => 'before',
			)
		);
		$this->add_responsive_control(
			'pb_container_width',
			array(
				'label'      => esc_html__( 'Width (vw)', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'vw' ),
				'range'      => array(
					'vw' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'vw',
					'size' => 100,
				),
				'selectors'  => array(
					'{{WRAPPER}} .progress-container.horizontal' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar' => 'yes',
					'pbLayout'    => 'horizontal',
				),
			)
		);
		$this->add_responsive_control(
			'pb_container_height',
			array(
				'label'      => esc_html__( 'Height (vh)', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'vh' ),
				'range'      => array(
					'vh' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'vh',
					'size' => 100,
				),
				'selectors'  => array(
					'{{WRAPPER}} .progress-container.vertical' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar' => 'yes',
					'pbLayout'    => 'vertical',
				),
			)
		);
		$this->add_responsive_control(
			'pb_left',
			array(
				'label'      => esc_html__( 'left', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					),
					'vw' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .progress-container.horizontal' => 'left: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar' => 'yes',
					'pbLayout'    => 'horizontal',
				),
			)
		);
		$this->add_responsive_control(
			'pb_top',
			array(
				'label'      => esc_html__( 'Top', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					),
					'vh' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .progress-container.vertical' => 'Top: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar' => 'yes',
					'pbLayout'    => 'vertical',
				),
			)
		);
		$this->start_controls_tabs( 'progressbar_style' );
		$this->start_controls_tab(
			'container_style',
			array(
				'label' => esc_html__( 'Background', 'theplus' ),
			)
		);
		$this->add_control(
			'background',
			array(
				'label'     => esc_html__( 'Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .progress-container' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'progressBar' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'bg_height',
			array(
				'label'      => esc_html__( 'height (px)', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 5,
				),
				'selectors'  => array(
					'{{WRAPPER}} .progress-container.horizontal' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar' => 'yes',
					'pbLayout'    => 'horizontal',
				),
			)
		);
		$this->add_responsive_control(
			'bg_width',
			array(
				'label'      => esc_html__( 'width (px)', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 5,
				),
				'selectors'  => array(
					'{{WRAPPER}} .progress-container.vertical' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar' => 'yes',
					'pbLayout'    => 'vertical',
				),
			)
		);
		$this->add_responsive_control(
			'bg_border',
			array(
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .progress-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar' => 'yes',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'progress_style',
			array(
				'label'     => esc_html__( 'Fill', 'theplus' ),
				'condition' => array(
					'progressBar' => 'yes',
				),
			)
		);
		$this->add_control(
			'progress_fill',
			array(
				'label'     => esc_html__( 'Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .progress-container.horizontal .tp-horizontal-scroll-progress-bar,{{WRAPPER}} .progress-container.vertical .tp-horizontal-scroll-progress-bar' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'progressBar' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'pb_height',
			array(
				'label'      => esc_html__( 'height (px)', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 5,
				),
				'selectors'  => array(
					'{{WRAPPER}} .progress-container.horizontal .tp-horizontal-scroll-progress-bar' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar' => 'yes',
					'pbLayout'    => 'horizontal',
				),
			)
		);
		$this->add_responsive_control(
			'pb_width',
			array(
				'label'      => esc_html__( 'width (px)', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 5,
				),
				'selectors'  => array(
					'{{WRAPPER}} .progress-container.vertical .tp-horizontal-scroll-progress-bar' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar' => 'yes',
					'pbLayout'    => 'vertical',
				),
			)
		);
		$this->add_responsive_control(
			'pb_border',
			array(
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .tp-horizontal-scroll-progress-bar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar' => 'yes',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'progress_toggle',
			array(
				'label'     => esc_html__( 'Progress Toggle', 'theplus' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'progressBar'     => 'yes',
					'progress_styles' => 'style-2',
				),
				'separator' => 'before',
			)
		);
		$this->add_control(
			'tooltip_position',
			array(
				'label'     => esc_html__( 'Position', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom',
				'options'   => array(
					'bottom' => esc_html__( 'Bottom', 'theplus' ),
					'top'    => esc_html__( 'Top', 'theplus' ),
				),
				'condition' => array(
					'progressBar'     => 'yes',
					'progress_styles' => 'style-2',
					'pbLayout'        => 'horizontal',
				),
			)
		);
		$this->add_control(
			'tooltip_positionV',
			array(
				'label'     => esc_html__( 'Position', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'right',
				'options'   => array(
					'left'  => esc_html__( 'Left', 'theplus' ),
					'right' => esc_html__( 'Right', 'theplus' ),
				),
				'condition' => array(
					'progressBar'     => 'yes',
					'progress_styles' => 'style-2',
					'pbLayout'        => 'vertical',
				),
			)
		);
		$this->add_responsive_control(
			'tooltipOffset',
			array(
				'label'      => esc_html__( 'Offset', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .progress-container.horizontal .tp-horizontal-scroll-progress-bar .tp-progress-tooltip.style-2' => 'top:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar'      => 'yes',
					'progress_styles'  => 'style-2',
					'tooltip_position' => 'bottom',
					'pbLayout'         => 'horizontal',
				),
			)
		);
		$this->add_responsive_control(
			'tooltipOffsetleft',
			array(
				'label'      => esc_html__( 'Offset', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .progress-container.vertical .tp-horizontal-scroll-progress-bar .tp-progress-tooltip.style-2' => 'left:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar'       => 'yes',
					'progress_styles'   => 'style-2',
					'tooltip_positionV' => 'right',
					'pbLayout'          => 'vertical',
				),
			)
		);
		$this->add_responsive_control(
			'tooltipOffsettop',
			array(
				'label'      => esc_html__( 'Offset', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 40,
				),
				'selectors'  => array(
					'{{WRAPPER}} .tp-horizontal-scroll-progress-bar .tp-progress-tooltip.style-2' => 'top:-{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar'      => 'yes',
					'progress_styles'  => 'style-2',
					'tooltip_position' => 'top',
					'pbLayout'         => 'horizontal',
				),
			)
		);
		$this->add_responsive_control(
			'tooltipOffsetright',
			array(
				'label'      => esc_html__( 'Offset', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 50,
				),
				'selectors'  => array(
					'{{WRAPPER}} .progress-container.vertical .tp-horizontal-scroll-progress-bar .tp-progress-tooltip.style-2' => 'left:-{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar'       => 'yes',
					'progress_styles'   => 'style-2',
					'tooltip_positionV' => 'left',
					'pbLayout'          => 'vertical',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pb_toggle_typography',
				'label'     => esc_html__( 'Typography', 'theplus' ),
				'scheme'    => Typography::TYPOGRAPHY_3,
				'selector'  => '{{WRAPPER}} .tp-horizontal-scroll-progress-bar .tp-progress-tooltip.style-2',
				'condition' => array(
					'progressBar'     => 'yes',
					'progress_styles' => 'style-2',
				),
			)
		);
		$this->add_control(
			'pbToggle_bgColor',
			array(
				'label'     => esc_html__( 'Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tp-horizontal-scroll-progress-bar .tp-progress-tooltip.style-2, .tp-horizontal-scroll-progress-bar .tp-progress-tooltip.style-2:before' => 'background: {{VALUE}}',
				),
				'condition' => array(
					'progressBar'     => 'yes',
					'progress_styles' => 'style-2',
				),
			)
		);
		$this->add_control(
			'pbToggleColor',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tp-horizontal-scroll-progress-bar .tp-progress-tooltip.style-2' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'progressBar'     => 'yes',
					'progress_styles' => 'style-2',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'pbTooltip_shadow',
				'selector'  => '{{WRAPPER}} .tp-horizontal-scroll-progress-bar .tp-progress-tooltip.style-2, .tp-horizontal-scroll-progress-bar .tp-progress-tooltip.style-2:before',
				'condition' => array(
					'progressBar'     => 'yes',
					'progress_styles' => 'style-2',
				),
			)
		);
		$this->add_responsive_control(
			'pbtootltip_padding',
			array(
				'label'      => esc_html__( 'Padding', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .tp-horizontal-scroll-progress-bar .tp-progress-tooltip.style-2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'progressBar'     => 'yes',
					'progress_styles' => 'style-2',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pb_toggle_typography3',
				'label'     => esc_html__( 'Typography', 'theplus' ),
				'scheme'    => Typography::TYPOGRAPHY_3,
				'selector'  => '{{WRAPPER}} .tp-horizontal-scroll-progress-bar.style-3',
				'condition' => array(
					'progressBar'     => 'yes',
					'progress_styles' => 'style-3',
				),
				'separator' => 'before',
			)
		);
		$this->add_control(
			'pbToggle_bgColor3',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tp-horizontal-scroll-progress-bar.style-3' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'progressBar'     => 'yes',
					'progress_styles' => 'style-3',
				),
			)
		);
		$this->end_controls_section();

		/**Pagination style*/
		$this->start_controls_section(
			'pagination_style',
			array(
				'label'     => esc_html__( 'Paginate', 'theplus' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pagination'  => 'yes',
					'remote_type' => 'horizontal',
				),
			)
		);
		$this->add_control(
			'paginateSeparator',
			array(
				'label'     => esc_html__( 'Separator Layout', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'none'    => esc_html__( 'None', 'theplus' ),
					'default' => esc_html__( 'Default', 'theplus' ),
					'custom'  => esc_html__( 'Custom', 'theplus' ),
				),
				'default'   => 'default',
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_control(
			'separatorIcon',
			array(
				'label'     => esc_html__( 'Separator Icon', 'theplus' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-plus',
					'library' => 'solid',
				),
				'condition' => array(
					'pagination'        => 'yes',
					'paginateSeparator' => 'custom',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pn_typography',
				'selector'  => '{{WRAPPER}} .tp-hscroll-pagination .hscroll-pagination-slides',
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_control(
			'pn_color',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tp-hscroll-pagination .hscroll-pagination-slides' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'background',
				'label'     => esc_html__( 'Background Type', 'theplus' ),
				'types'     => array( 'classic', 'gradient' ),
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .tp-hscroll-pagination',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'border',
				'label'    => esc_html__( 'Border Type', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-hscroll-pagination',
			)
		);
		$this->add_responsive_control(
			'pg_border',
			array(
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .tp-hscroll-pagination' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'pg_padding',
			array(
				'label'      => esc_html__( 'Padding', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .tp-hscroll-pagination' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'pg_box_shadow',
				'label'     => esc_html__( 'Box Shadow', 'theplus' ),
				'selector'  => '{{WRAPPER}} .tp-hscroll-pagination',
				'separator' => 'after',
			)
		);
		$this->start_controls_tabs( 'number_style' );
		$this->start_controls_tab(
			'normal_style',
			array(
				'label' => esc_html__( 'Normal', 'theplus' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'ts_typography',
				'selector'  => '{{WRAPPER}} .tp-hscroll-pagination .hs_total_slides',
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_control(
			'total_slides',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tp-hscroll-pagination .hs_total_slides' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_control(
			'total_slide_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tp-hscroll-pagination .hs_total_slides' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'tsborder',
				'label'    => esc_html__( 'Border Type', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-hscroll-pagination .hs_total_slides',
			)
		);
		$this->add_responsive_control(
			'ts_border',
			array(
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .tp-hscroll-pagination .hs_total_slides' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'ts_padding',
			array(
				'label'      => esc_html__( 'Padding', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .tp-hscroll-pagination .hs_total_slides' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'ts_box_shadow',
				'label'     => esc_html__( 'Box Shadow', 'theplus' ),
				'selector'  => '{{WRAPPER}} .tp-hscroll-pagination .hs_total_slides',
				'separator' => 'after',
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'active_style',
			array(
				'label' => esc_html__( 'Active', 'theplus' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'cs_typography',
				'selector'  => '{{WRAPPER}} .tp-hscroll-pagination .hs-current-slides',
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_control(
			'current_slide',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tp-hscroll-pagination .hs-current-slides' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_control(
			'current_slide_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tp-hscroll-pagination .hs-current-slides' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'csborder',
				'label'    => esc_html__( 'Border Type', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-hscroll-pagination .hs-current-slides',
			)
		);
		$this->add_responsive_control(
			'cs_border',
			array(
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .tp-hscroll-pagination .hs-current-slides' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'cs_padding',
			array(
				'label'      => esc_html__( 'Padding', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .tp-hscroll-pagination .hs-current-slides' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'cs_box_shadow',
				'label'     => esc_html__( 'Box Shadow', 'theplus' ),
				'selector'  => '{{WRAPPER}} .tp-hscroll-pagination .hs-current-slides',
				'separator' => 'after',
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'separator_style',
			array(
				'label' => esc_html__( 'Separator', 'theplus' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 's_typography',
				'selector'  => '{{WRAPPER}} .tp-hscroll-pagination .hs_separator',
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_control(
			'separator_color',
			array(
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tp-hscroll-pagination .hs_separator' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_control(
			'separator_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tp-hscroll-pagination .hs_separator' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'pg_spacing',
			array(
				'label'      => esc_html__( 'Space Between (px)', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => '',
				),
				'selectors'  => array(
					'{{WRAPPER}} .tp-hscroll-pagination .hs-current-slides' => 'margin-right:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tp-hscroll-pagination .hs_total_slides' => 'margin-left:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'remote_type' => 'horizontal',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'sborder',
				'label'    => esc_html__( 'Border Type', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-hscroll-pagination .hs_separator',
			)
		);
		$this->add_responsive_control(
			's_border',
			array(
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .tp-hscroll-pagination .hs_separator' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			's_padding',
			array(
				'label'      => esc_html__( 'Padding', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .tp-hscroll-pagination .hs_separator' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'pagination' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 's_box_shadow',
				'label'     => esc_html__( 'Box Shadow', 'theplus' ),
				'selector'  => '{{WRAPPER}} .tp-hscroll-pagination .hs_separator',
				'separator' => 'after',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'pagination_style_css',
			array(
				'label'     => esc_html__( 'Pagination Styles', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'fadeIn'            => esc_html__( 'FadeIn', 'theplus' ),
					'fadeInDown'        => esc_html__( 'FadeInDown', 'theplus' ),
					'fadeInUp'          => esc_html__( 'FadeInUp', 'theplus' ),
					'flipInX'           => esc_html__( 'FlipInX', 'theplus' ),
					'flipInY'           => esc_html__( 'FlipInY', 'theplus' ),
					'rotateInDownRight' => esc_html__( 'RotateInDownRight', 'theplus' ),
					'rotateInUpRight'   => esc_html__( 'RotateInUpRight', 'theplus' ),
					'zoomIn'            => esc_html__( 'ZoomIn', 'theplus' ),
					'rollIn'            => esc_html__( 'RollIn', 'theplus' ),
					'bounceIn'          => esc_html__( 'BounceIn', 'theplus' ),
				),
				'default'   => 'fadeIn',
				'condition' => array(
					'pagination' => 'yes',
				),
				'separator' => 'before',
			)
		);
		$this->end_controls_section();
		/*Adv tab*/
		$this->start_controls_section(
			'section_plus_extra_adv',
			array(
				'label' => esc_html__( 'Plus Extras', 'theplus' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);
		$this->end_controls_section();
		/*Adv tab*/

		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH . 'modules/widgets/theplus-widget-animation.php';
		include THEPLUS_PATH . 'modules/widgets/theplus-needhelp.php';
	}

	/**
	 * Render carousel remote widget load.
	 *
	 * @since 1.2.0
	 * @access protected
	 */
	protected function render() {

		$settings          = $this->get_settings_for_display();
		$button_link       = ! empty( $settings['button_link'] ) ? $settings['button_link'] : '';
		$progressBar       = ! empty( $settings['progressBar'] ) ? 1 : 0;
		$pagination        = ! empty( $settings['pagination'] ) ? 1 : 0;
		$pagination_style  = ! empty( $settings['pagination_style_css'] ) ? $settings['pagination_style_css'] : '';
		$paginateSeparator = ! empty( $settings['paginateSeparator'] ) ? $settings['paginateSeparator'] : '';
		$separatorIcon     = ! empty( $settings['separatorIcon'] ) ? $settings['separatorIcon'] : '';
		$progress_styles   = ! empty( $settings['progress_styles'] ) ? $settings['progress_styles'] : '';
		$tooltip_position  = ! empty( $settings['tooltip_position'] ) ? $settings['tooltip_position'] : '';
		$tooltip_positionV = ! empty( $settings['tooltip_positionV'] ) ? $settings['tooltip_positionV'] : '';
		$pbLayout          = ! empty( $settings['pbLayout'] ) ? $settings['pbLayout'] : '';
		$remote_type       = $settings['remote_type'];

		$id = $this->get_id();
		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH . 'modules/widgets/theplus-widget-animation-attr.php';

		/*--Plus Extra ---*/
		$PlusExtra_Class = '';
		include THEPLUS_PATH . 'modules/widgets/theplus-widgets-extra.php';
		/*--Plus Extra ---*/

		$nav_next = '';
		$nav_prev = '';

		$nav_next_text  = '';
		$nav_prev_text  = '';
		$nav_next_slide = $settings['nav_next_slide'];
		$nav_prev_slide = $settings['nav_prev_slide'];

		$carousel_unique_id = $settings['carousel_unique_id'];

		if ( ! empty( $nav_next_slide ) ) {
			$nav_next_text = '<span class="next-text">' . esc_html( $nav_next_slide ) . '</span>';
		}

		if ( ! empty( $nav_prev_slide ) ) {
			$nav_prev_text = '<span class="prev-text">' . esc_html( $nav_prev_slide ) . '</span>';
		}

		if ( 'none' === $settings['nav_icon_style'] ) {
			$nav_prev = $nav_prev_text;
			$nav_next = $nav_next_text;
		} elseif ( 'style-1' === $settings['nav_icon_style'] ) {
			$nav_prev = '<span class="nav-icon"><i class="fa fa-angle-left" aria-hidden="true"></i></span>' . $nav_prev_text;
			$nav_next = $nav_next_text . '<span class="nav-icon"><i class="fa fa-angle-right" aria-hidden="true"></i></span>';
		} elseif ( 'custom' === $settings['nav_icon_style'] ) {
			if ( 'horizontal' === $remote_type ) {
				if ( ! empty( $settings['prev_icon_hs'] ) ) {
					$nav_prev .= '<span class="nav-icon">';
						ob_start();
						\Elementor\Icons_Manager::render_icon( $settings['prev_icon_hs'], array( 'aria-hidden' => 'true' ) );
						$nav_prev .= ob_get_contents();
						ob_end_clean();
					$nav_prev .= '</span>' . $nav_prev_text;
				}
				if ( ! empty( $settings['next_icon_hs'] ) ) {
					$nav_next .= $nav_next_text . '<span class="nav-icon">';
						ob_start();
						\Elementor\Icons_Manager::render_icon( $settings['next_icon_hs'], array( 'aria-hidden' => 'true' ) );
						$nav_next .= ob_get_contents();
						ob_end_clean();
					$nav_next .= '</span>';
				}
			} else {
				$nav_prev_icon = '';
				$nav_next_icon = '';
				if ( ! empty( $settings['nav_prev_icon']['url'] ) ) {
					$nav_prev_iconid = $settings['nav_prev_icon']['id'];
					$nav_prev_icon   = tp_get_image_rander( $nav_prev_iconid, $settings['nav_icon_thumbnail_size'] );
				}

				if ( ! empty( $settings['nav_next_icon']['url'] ) ) {
					$nav_next_iconid = $settings['nav_next_icon']['id'];
					$nav_next_icon   = tp_get_image_rander( $nav_next_iconid, $settings['nav_icon_thumbnail_size'] );
				}

				$nav_prev = '<span class="nav-icon">' . $nav_prev_icon . '</span>' . $nav_prev_text;
				$nav_next = $nav_next_text . '<span class="nav-icon">' . $nav_next_icon . '</span>';
			}
		}

		$active_class = '';
		if ( 'switcher' === $remote_type ) {
			$active_class = 'active';
		}

		$uid  = uniqid( 'remote' );
		$da   = '';
		$daid = '';
		if ( ! empty( $settings['dotList'] ) && 'yes' === $settings['dotList'] || 'horizontal' === $remote_type ) {
			$da   = 'data-connection="tpca_' . esc_attr( $carousel_unique_id ) . '" data-tab-id="tptab_' . esc_attr( $carousel_unique_id ) . '" data-extra-conn="tpex-' . esc_attr( $carousel_unique_id ) . '"';
			$daid = 'id="tptab_' . esc_attr( $carousel_unique_id ) . '"';
		}

		/**Horizontal scroll connection*/
		$horizontal_data = array(
			'u_id'     => $carousel_unique_id,
			'widgetid' => $this->get_id(),
			'paginate' => $pagination,
		);

		if ( 'horizontal' === $remote_type ) {
			$finaldata = '';

			if ( $pagination ) {
				$horizontal_data['pagination_style'] = $pagination_style;
				$horizontal_data['separatorIcon']    = $separatorIcon;

				$horizontal_data['paginateSeparator'] = $paginateSeparator;
			}

			if ( $progressBar ) {
				$horizontal_data['progressBar'] = $progressBar;
				$horizontal_data['pbLayout']    = $pbLayout;

				$horizontal_data['progress_styles'] = $progress_styles;
			}

			$horizontal_data['rType'] = $remote_type;
		}

		$finaldata = 'data-remotedata="' . htmlspecialchars( json_encode( $horizontal_data, true ), ENT_QUOTES, 'UTF-8' ) . '"';

		$carousel_remote = '<div ' . $daid . ' class="theplus-carousel-remote remote-' . esc_attr( $remote_type ) . ' ' . $animated_class . ' ' . esc_attr( $uid ) . '" data-id="' . esc_attr( $uid ) . '" data-remote="' . esc_attr( $remote_type ) . '"  ' . $da . ' ' . $animation_attr . ' ' . $finaldata . '>';

		if ( empty( $settings['nxtprvbtn'] ) && 'yes' !== $settings['nxtprvbtn'] ) {
			$carousel_remote .= '';
		} else {
			$carousel_remote .= '<div class="slider-nav-next-prev">';

				$cnavll = function_exists( 'tp_has_lazyload' ) ? tp_bg_lazyLoad( $settings['box_background_image'], $settings['box_hover_background_image'] ) : '';

				$carousel_remote .= '<a href="#" class="custom-nav-remote ' . $cnavll . ' nav-prev-slide ' . esc_attr( $active_class ) . '" data-id="tpca_' . esc_attr( $carousel_unique_id ) . '" data-nav="' . esc_attr( 'prev' ) . '">' . $nav_prev . '</a>';
				$carousel_remote .= '<a href="#" class="custom-nav-remote ' . $cnavll . ' nav-next-slide" data-id="tpca_' . esc_attr( $carousel_unique_id ) . '" data-nav="' . esc_attr( 'next' ) . '">' . $nav_next . '</a>';

			$carousel_remote .= '</div>';
		}

		if ( ! empty( $settings['dotList'] ) && 'yes' === $settings['dotList'] ) {
			if ( ! empty( $settings['dots_coll'] ) ) {
				$index            = 0;
				$carousel_remote .= '<div class="tp-carousel-dots dot-' . $settings['dotLayout'] . '">';

				foreach ( $settings['dots_coll'] as $index => $item ) {
					$ps_count = $index;
					$ttpos    = '';
					if ( ! empty( $settings['dotLayout'] ) && 'horizontal' === $settings['dotLayout'] ) {
						$ttpos = $settings['tooltipDir'];
					} elseif ( ! empty( $settings['dotLayout'] ) && 'vertical' === $settings['dotLayout'] ) {
						$ttpos = $settings['vtooltipDir'];
					}

					$ia = 'inactive';
					if ( 0 === $index ) {
						$ia = 'active';
					}

					$carodots_ll = function_exists( 'tp_has_lazyload' ) ? tp_bg_lazyLoad( $item['dotBgtype_image'], $item['actdotBgtype_image'] ) : '';

					$carousel_remote .= '<div class="tp-carodots-item elementor-repeater-item-' . esc_attr( $item['_id'] ) . ' ' . esc_attr( $settings['dotstyle'] ) . ' ' . esc_attr( $ia ) . ' ' . $carodots_ll . '" data-tab="' . esc_attr( $ps_count ) . '"  data-scrollid="' . esc_attr( $item['iddd'] ) . '">';
					$carousel_remote .= '<div class="tp-dots tooltip-' . esc_attr( $ttpos ) . '">';

					$icons = '';
					if ( $item['iconFonts'] && 'font_awesome' === $item['iconFonts'] && ! empty( $item['iconName'] ) ) {
						ob_start();
							\Elementor\Icons_Manager::render_icon( $item['iconName'], array( 'aria-hidden' => 'true' ) );
							$faicon = ob_get_contents();
						ob_end_clean();

						$icons = $faicon;
					} elseif ( $item['iconFonts'] && 'image' === $item['iconFonts'] && ! empty( $item['iconImage'] ) ) {
						$iconImage = $item['iconImage']['id'];
						$icons     = tp_get_image_rander( $iconImage, $item['iconimageSize_size'] );
					}

					$carousel_remote .= $icons;

					if ( ! empty( $item['label'] ) ) {
						$carousel_remote .= '<span class="tooltip-txt">' . esc_html( $item['label'] ) . '</span>';
						$carousel_remote .= '<svg height="32" data-v-d3e9c2e8="" width="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" svg-inline="" role="presentation" focusable="false" tabindex="-1" class="active-border"><path data-v-d3e9c2e8="" d="M14.7974701,0 C16.6202545,0 19.3544312,0 23,0 C26.8659932,0 30,3.13400675 30,7 L30,23 C30,26.8659932 26.8659932,30 23,30 L7,30 C3.13400675,30 0,26.8659932 0,23 L0,7 C0,3.13400675 3.13400675,0 7,0 L14.7602345,0" transform="translate(1.000000, 1.000000)" fill="none" stroke="' . esc_attr( $settings['AborderColor'] ) . '" stroke-width="2" class="border"></path></svg>';
					}

					$carousel_remote .= '</div>';
					$carousel_remote .= '</div>';
				}

				$carousel_remote .= '</div>';
			}
		}

		if ( ! empty( $settings['showpagi'] ) && 'yes' === $settings['showpagi'] && 'horizontal' !== $remote_type ) {
			$carousel_remote         .= '<div class="carousel-pagination">';
				$carousel_remote     .= '<ul class="pagination-list">';
					$carousel_remote .= '<li class="pagination-list-in active"> 01 </li>';
					$carousel_remote .= '<li class="pagination-list-in separator"> / </li>';
					$carousel_remote .= '<li class="pagination-list-in total"> 0' . esc_html( $settings['sliderInd'] ) . ' </li>';
				$carousel_remote     .= '</ul>';
			$carousel_remote         .= '</div>';
		}

		if ( 'horizontal' === $remote_type && ! empty( $pagination ) ) {
			$carousel_remote .= '<div class="tp-hscroll-pagination"></div>';
		}

		if ( ! empty( $progressBar ) ) {
			$carousel_remote     .= '<div class="progress-container progress-container-' . $id . ' ' . $progress_styles . ' ' . $pbLayout . '" >';
				$carousel_remote .= '<div class="tp-horizontal-scroll-progress-bar tp-hscroll-progress-bar-' . $id . ' ' . $progress_styles . '  ' . $pbLayout . '">';

			if ( 'style-2' === $progress_styles ) {
				$carousel_remote .= '<div class="tp-progress-tooltip tp-progress-tooltip-' . $id . ' ' . $progress_styles . ' ' . $tooltip_position . ' ' . $tooltip_positionV . '"></div>';
			}

				$carousel_remote .= '</div>';
			$carousel_remote     .= '</div>';

		}

		$carousel_remote .= '</div>';

		echo $before_content . $carousel_remote . $after_content;
	}

	/**
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.2.0
	 * @access protected
	 */
	protected function content_template() {
	}
}
