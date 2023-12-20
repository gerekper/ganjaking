<?php
/**
 * Widget Name: Horizontal Scroll
 * Description: Horizontal Scroll.
 * Author: Theplus
 * Author URI: https://posimyth.com
 *
 *  @package Horizontal Scroll
 */

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Core\Schemes\Color;
use TheplusAddons\Theplus_Element_Load;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Horizontal Scroll Main Elementor Class
 */
class ThePlus_Horizontal_Scroll_Advance extends Widget_Base {

	/**
	 * Link path for document.
	 *
	 * @var THEPLUS_TPDOC
	 */
	public $tp_doc = THEPLUS_TPDOC;

	/**Widget name*/
	public function get_name() {
		return 'tp-horizontal-scroll-advance';
	}

	/**Widget title*/
	public function get_title() {
		return esc_html__( 'Horizontal Scroll', 'theplus' );
	}

	/**Widget Icon*/
	public function get_icon() {
		return 'fa fa-horizontal-scroll-advance theplus_backend_icon';
	}

	/**Widget categories*/
	public function get_categories() {
		return array( 'plus-essential' );
	}

	/**Need Help URL*/
	public function get_custom_help_url() {
		$doc_url = $this->tp_doc . 'horizontal-scroll';

		return esc_url( $doc_url );
	}

	/**Widget search key words*/
	public function get_keywords() {
		return array( 'horizontal', 'horizontal scroll', 'hs', 'tp', 'scroll', 'theplus' );
	}

	/**Backend preview reload*/
	public function is_reload_preview_required() {
		return true;
	}

	/**Widget register controller*/
	protected function register_controls() {
		/*Tab Layout*/
		$this->start_controls_section(
			'Content',
			array(
				'label' => esc_html__( 'Content', 'theplus' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'fp_content_template',
			array(
				'label'       => esc_html__( 'Select Template', 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => l_theplus_get_templates(),
				'label_block' => 'true',
				'classes'     => 'tp-template-create-btn',
			)
		);
		$this->add_control(
			'liveeditor',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<a class="tp-live-editor" id="tp-live-editor-button">Edit Template</a>',
				'content_classes' => 'tp-live-editor-btn',
				'label_block'     => true,
				'condition'       => array(
					'fp_content_template!' => '0',
				),
			)
		);
		$this->add_control(
			'create',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<a class="tp-live-create" id="tp-live-create-button">Create Template</a>',
				'content_classes' => 'tp-live-create-btn',
				'label_block'     => true,
				'condition'       => array(
					'fp_content_template' => '0',
				),
			)
		);
		$this->add_control(
			'tempNotice',
			array(
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<p class="tp-controller-notice"><i>Select the page template you want to convert into a horizontal scroll. All sections on this page will slide horizontally.</i></p>',
				'label_block' => true,
			)
		);
		$this->end_controls_section();

		/**Scrolling Options*/
		$this->start_controls_section(
			'Scrolling Options',
			array(
				'label' => esc_html__( 'Scrolling Options', 'theplus' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'scroll_effects',
			array(
				'label'   => wp_kses_post( "Slide Scroll Effects <a class='tp-docs-link' href='" . esc_url( $this->tp_doc ) . "add-page-transition-effect-in-elementor-horizontal-scroll/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => array(
					'normal' => esc_html__( 'Normal', 'theplus' ),
					'skew'   => esc_html__( 'Skew', 'theplus' ),
					'scale'  => esc_html__( 'Scale', 'theplus' ),
					'bounce' => esc_html__( 'Bounce', 'theplus' ),
				),
			)
		);
		$this->add_control(
			'scroll_skew_val',
			array(
				'label'     => esc_html__( 'Skew Value', 'theplus' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 20,
						'step' => 1,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 3,
				),
				'condition' => array(
					'scroll_effects' => array( 'skew', 'bounce' ),
				),
			)
		);
		$this->add_control(
			'scroll_scale_val',
			array(
				'label'     => esc_html__( 'Scale Value', 'theplus' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0.1,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 0.9,
				),
				'condition' => array(
					'scroll_effects' => array( 'scale', 'bounce' ),
				),
			)
		);
		$this->add_control(
			'scrollNotice',
			array(
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<p class="elementor-control-field-description"><i>This will apply a scroll effect as you slide from one section to another.</i></p>',
				'label_block' => true,
			)
		);
		$this->add_control(
			'bg_transition',
			array(
				'label'     => wp_kses_post( "Background Transition <a class='tp-docs-link' href='" . esc_url( $this->tp_doc ) . "change-background-image-on-horizontal-page-scroll-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'default'   => '',
				'separator' => 'before',
			)
		);
		$repeater = new Repeater();
		$repeater->add_control(
			'slide_title',
			array(
				'label'       => esc_html__( 'Title', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'bg_color',
			array(
				'label'     => wp_kses_post( "Color <a class='tp-docs-link' href='" . esc_url( $this->tp_doc ) . "change-background-colour-on-horizontal-page-scroll-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.tp-bg-hscroll-{{ID}}.tp-bg-hscroll .bg-scroll{{CURRENT_ITEM}}' => 'background-color: {{VALUE}}',
				),
			)
		);
		$repeater->add_control(
			'image',
			array(
				'label' => esc_html__( 'Choose Image', 'plugin-name' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$repeater->add_control(
			'hs_background',
			array(
				'label'        => esc_html__( 'Backdrop Filter', 'theplus' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __( 'Default', 'theplus' ),
				'label_on'     => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			)
		);
		$repeater->start_popover();
		$repeater->add_control(
			'bg_blur',
			array(
				'label'      => esc_html__( 'Background Blur', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'max'  => 10,
						'min'  => 0,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 1,
				),
				'selectors'  => array(
					'.tp-bg-hscroll-{{ID}}.tp-bg-hscroll .bg-scroll{{CURRENT_ITEM}}:before' => '-webkit-backdrop-filter:blur({{bg_blur.SIZE}}{{bg_blur.UNIT}}) !important;backdrop-filter:blur({{bg_blur.SIZE}}{{bg_blur.UNIT}}) !important;',
				),
			)
		);
		$repeater->add_responsive_control(
			'background_overlay',
			array(
				'label'     => esc_html__( 'Background Overlay', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.tp-bg-hscroll-{{ID}}.tp-bg-hscroll .bg-scroll{{CURRENT_ITEM}}:before' => 'background-color: {{VALUE}}',
				),
			)
		);
		$repeater->end_popover();
		$this->add_control(
			'bg_transition_content',
			array(
				'label'       => esc_html__( 'Background', 'theplus' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'slide_title' => esc_html__( 'Item #1', 'theplus' ),
					),
					array(
						'slide_title' => esc_html__( 'Item #2', 'theplus' ),
					),

				),
				'condition'   => array(
					'bg_transition' => 'yes',
				),
				'title_field' => '{{{ slide_title }}}',
			)
		);
		$this->add_control(
			'bg_duration',
			array(
				'label'      => esc_html__( 'Background Transition Duration', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array(
					's' => array(
						'min'  => 0,
						'max'  => 2,
						'step' => 0.1,
					),
				),
				'default'    => array(
					'unit' => 's',
					'size' => 0.7,
				),
				'selectors'  => array(
					'.tp-bg-hscroll-{{ID}}.tp-bg-hscroll .bg-scroll' => 'transition:all {{SIZE}}{{UNIT}} linear;',
				),
				'condition'  => array(
					'bg_transition' => 'yes',
				),
			)
		);
		$this->add_control(
			'bgNotice',
			array(
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<p class="elementor-control-field-description"><i>This will apply a scroll effect as you slide from one section to another.</i></p>',
				'label_block' => true,
			)
		);
		$this->add_control(
			'fixBgtitle',
			array(
				'label'     => wp_kses_post( "Sticky Section <a class='tp-docs-link' href='" . esc_url( $this->tp_doc ) . "make-a-section-sticky-on-scroll-in-elementor-horizontal-page-scroll/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'default'   => '',
				'separator' => 'before',
			)
		);
		$this->add_control(
			'fixcontent',
			array(
				'label'       => esc_html__( 'Select Template', 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => l_theplus_get_templates(),
				'label_block' => 'true',
				'classes'     => 'tp-template-create-btn',
				'condition'   => array(
					'fixBgtitle' => 'yes',
				),
			)
		);
		$this->add_control(
			'fixliveeditor',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<a class="tp-live-editor" id="tp-live-editor-button" >Edit Template</a>',
				'content_classes' => 'tp-live-editor-btn',
				'label_block'     => true,
				'condition'       => array(
					'fixcontent!' => '0',
					'fixBgtitle'  => 'yes',
				),
			)
		);
		$this->add_control(
			'fixcreate',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<a class="tp-live-create" id="tp-live-create-button" >Create Template</a>',
				'content_classes' => 'tp-live-create-btn',
				'label_block'     => true,
				'condition'       => array(
					'fixcontent' => '0',
					'fixBgtitle' => 'yes',
				),
			)
		);
		$this->add_control(
			'fixNotice',
			array(
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<p class="elementor-control-field-description"><i>Use this to fix a specific section in place during the horizontal scroll. For example, you can make the header menu remain fixed across all sections.</i></p>',
				'label_block' => true,
			)
		);
		$this->add_control(
			'opacity_scroll',
			array(
				'label'     => esc_html__( 'Opacity Based Scroll', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'default'   => '',
				'separator' => 'before',
			)
		);
		$this->add_control(
			'opacity_val',
			array(
				'label'     => esc_html__( 'Value', 'theplus' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1,
				'step'      => 0.1,
				'default'   => 0.5,
				'condition' => array(
					'opacity_scroll' => 'yes',
				),
			)
		);
		$this->add_control(
			'opacityNotice',
			array(
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<p class="elementor-control-field-description"><i>Change the opacity of the next and previous slides as you transition from one to another.</i></p>',
				'label_block' => true,
			)
		);
		$this->add_control(
			'fullscroll',
			array(
				'label'       => esc_html__( 'Full Slide Scroll', 'theplus' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'On', 'theplus' ),
				'label_off'   => esc_html__( 'Off', 'theplus' ),
				'description' => esc_html__( 'Switch between sections with a single scroll, creating a snappy and seamless transition.', 'theplus' ),
				'default'     => '',
				'separator'   => 'before',
			)
		);
		$this->add_control(
			'rtl_scroll',
			array(
				'label'       => esc_html__( 'RTL Compatibility', 'theplus' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'On', 'theplus' ),
				'label_off'   => esc_html__( 'Off', 'theplus' ),
				'description' => esc_html__( 'Allows for a right-to-left scrolling experience in Horizontal Scroll.', 'theplus' ),
				'default'     => '',
				'separator'   => 'before',
			)
		);
		$this->add_responsive_control(
			'h_scroll_speed',
			array(
				'label'       => esc_html__( 'Scroll Speed', 'theplus' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 100,
				'step'        => 1,
				'default'     => 4,
				'description' => esc_html__( 'Manage your scrolling speed.', 'theplus' ),
				'separator'   => 'before',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'extra_options',
			array(
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_responsive_control(
			'distance_last_slide',
			array(
				'label'       => esc_html__( 'Distance after last slide', 'theplus' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 1000,
				'step'        => 10,
				'default'     => 0,
				'description' => esc_html__( 'Add the Space in px after the last slide.', 'theplus' ),
				'separator'   => 'after',
			)
		);
		$this->add_responsive_control(
			'customWidth',
			array(
				'label'       => esc_html__( 'Slides Width', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '100',
				'description' => esc_html__(
					'Use the \'|\' symbol to set custom widths for each slide within the Horizontal Scroll. For Example \'100 | 50 | 100\' add width like this for each slide.',
					'theplus'
				),
			)
		);
		$this->add_control(
			'section_id',
			array(
				'label'       => wp_kses_post( "Section ID <a class='tp-docs-link' href='" . esc_url( $this->tp_doc ) . "add-dots-navigation-in-elementor-horizontal-page-scroll/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'description' => esc_html__( 'Create an anchor link for a specific section using the \' | \' symbol to define CSS IDs', 'theplus' ),
				'separator'   => 'before',
			)
		);
		$this->add_control(
			'URLParameter',
			array(
				'label'        => wp_kses_post( "URL Parameter <a class='tp-docs-link' href='" . esc_url( $this->tp_doc ) . "link-to-a-specific-section-in-elementor-horizontal-page-scroll/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'theplus' ),
				'label_off'    => esc_html__( 'Off', 'theplus' ),
				'return_value' => 'yes',
				'description'  => esc_html__( 'Enable a shareable link for a specific section of the Horizontal Scroll based on the assigned CSS ID above.', 'theplus' ),
				'default'      => '',
				'separator'    => 'before',
			)
		);
		$this->add_control(
			'horizontal_unique_id',
			array(
				'label'       => wp_kses_post( "Unique Connection ID <a class='tp-docs-link' href='" . esc_url( $this->tp_doc ) . "add-next-previous-button-in-elementor-horizontal-page-scroll/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => esc_html__(
					'With our unique connection ID, you can easily establish remote connections for your Horizontal Scroll with next/previous buttons, dots, progress bars, and pagination using our Carousel Remote widget.
                ',
					'theplus'
				),
				'separator'   => 'before',
			)
		);
		$this->add_control(
			'responsive_width',
			array(
				'label'     => esc_html__( 'Responsive Visibility', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'default'   => 'no',
				'separator' => 'before',
			)
		);
		$this->add_control(
			'responsive',
			array(
				'label'     => esc_html__( 'Width', 'theplus' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 2500,
				'step'      => 5,
				'default'   => 300,
				'condition' => array(
					'responsive_width' => 'yes',
				),
			)
		);
		$this->add_control(
			'resNotice',
			array(
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<p class="elementor-control-field-description"><i>Specify a width value in pixels, below which you wish to disable Horizontal Scroll.</i></p>',
				'label_block' => true,
			)
		);
		$this->end_controls_section();

		include THEPLUS_PATH . 'modules/widgets/theplus-needhelp.php';
	}

	/**Widget render html*/
	protected function render() {
		$settings             = $this->get_settings_for_display();
		$id                   = $this->get_id();
		$fp_content           = ! empty( $settings['fp_content_template'] ) ? $settings['fp_content_template'] : 0;
		$fixcontent           = ! empty( $settings['fixcontent'] ) ? $settings['fixcontent'] : 0;
		$scroll_effects       = ! empty( $settings['scroll_effects'] ) ? $settings['scroll_effects'] : 'normal';
		$bg_transition        = ! empty( $settings['bg_transition'] ) ? 1 : 0;
		$opacity_scroll       = ! empty( $settings['opacity_scroll'] ) ? 1 : 0;
		$fullscroll           = ! empty( $settings['fullscroll'] ) ? 1 : 0;
		$rtl_scroll           = ! empty( $settings['rtl_scroll'] ) ? 1 : 0;
		$fix_bgtitle          = ! empty( $settings['fixBgtitle'] ) ? 1 : 0;
		$distance_last_slide  = ! empty( $settings['distance_last_slide'] ) ? $settings['distance_last_slide'] : '0';
		$distlastslide_tab    = ! empty( $settings['distance_last_slide_tablet'] ) ? $settings['distance_last_slide_tablet'] : $distance_last_slide;
		$distlastslide_mob    = ! empty( $settings['distance_last_slide_mobile'] ) ? $settings['distance_last_slide_mobile'] : $distlastslide_tab;
		$hscroll_speed        = ! empty( $settings['h_scroll_speed'] ) ? $settings['h_scroll_speed'] : 4;
		$hscroll_speed_tab    = ! empty( $settings['h_scroll_speed_tablet'] ) ? $settings['h_scroll_speed_tablet'] : $hscroll_speed;
		$hscroll_speed_mob    = ! empty( $settings['h_scroll_speed_mobile'] ) ? $settings['h_scroll_speed_mobile'] : $hscroll_speed_tab;
		$custom_width         = ! empty( $settings['customWidth'] ) ? $settings['customWidth'] : 100;
		$custom_width_tab     = ! empty( $settings['customWidth_tablet'] ) ? $settings['customWidth_tablet'] : $custom_width;
		$custom_width_mob     = ! empty( $settings['customWidth_mobile'] ) ? $settings['customWidth_mobile'] : $custom_width;
		$responsive           = ! empty( $settings['responsive_width'] ) ? 1 : 0;
		$responsive_width     = ! empty( $settings['responsive'] ) ? $settings['responsive'] : '';
		$section_id           = ! empty( $settings['section_id'] ) ? $settings['section_id'] : '';
		$horizontal_unique_id = ! empty( $settings['horizontal_unique_id'] ) ? $settings['horizontal_unique_id'] : '';
		$url_parameter        = ! empty( $settings['URLParameter'] ) ? 1 : 0;
		$finaldata            = '';

		$horizontal_data = array(
			'id'                   => $id,
			'fp_content'           => $fp_content,
			'responsive'           => $responsive,
			'responsiveWidth'      => $responsive_width,
			'section_id'           => $section_id,
			'URLParameter'         => $url_parameter,
			'horizontal_unique_id' => $horizontal_unique_id,
			'customWidth'          => $custom_width,
			'customWidthTab'       => $custom_width_tab,
			'customWidthMob'       => $custom_width_mob,
			'tempID'               => $fp_content,
		);

		if ( ! empty( $scroll_effects ) && 'normal' !== $scroll_effects ) {
			$scroll_skew_val  = ! empty( $settings['scroll_skew_val']['size'] ) ? $settings['scroll_skew_val']['size'] : 5;
			$scroll_scale_val = ! empty( $settings['scroll_scale_val']['size'] ) ? $settings['scroll_scale_val']['size'] : 0.9;
			$horizontal_data  = array_merge(
				$horizontal_data,
				array(
					'scroll_effects'   => $scroll_effects,
					'scroll_skew_val'  => $scroll_skew_val,
					'scroll_scale_val' => $scroll_scale_val,
				)
			);
		}

		if ( ! empty( $bg_transition ) ) {
			$bg_transition_content = ! empty( $settings['bg_transition_content'] ) ? $settings['bg_transition_content'] : array();
			$bg_duration           = ! empty( $settings['bg_duration']['size'] ) ? 'transition-duration:' . $settings['bg_duration']['size'] . $settings['bg_duration']['unit'] . ';' : '';
			$horizontal_data       = array_merge(
				$horizontal_data,
				array(
					'bg_transition'         => $bg_transition,
					'bg_transition_content' => $bg_transition_content,
					'bg_duration'           => $bg_duration,
				)
			);
		}

		if ( ! empty( $opacity_scroll ) ) {
			$opacity_val     = ! empty( $settings['opacity_val'] ) ? $settings['opacity_val'] : 0;
			$horizontal_data = array_merge(
				$horizontal_data,
				array(
					'opacity_scroll' => $opacity_scroll,
					'opacityVal'     => $opacity_val,
				)
			);
		}

		if ( ! empty( $fullscroll ) ) {
			$horizontal_data['tp_fullscroll'] = $fullscroll;
		}

		if ( ! empty( $rtl_scroll ) ) {
			$horizontal_data['rtl_scroll'] = $rtl_scroll;
		}

		if ( ! empty( $distance_last_slide ) ) {
			$horizontal_data['distanceLastslide'] = $distance_last_slide;
			$horizontal_data['DistlastslideTab']  = $distlastslide_tab;
			$horizontal_data['DistlastslideMob']  = $distlastslide_mob;
		}

		if ( ! empty( $hscroll_speed ) ) {
			$horizontal_data['speed']    = $hscroll_speed;
			$horizontal_data['SpeedTab'] = $hscroll_speed_tab;
			$horizontal_data['SpeedMob'] = $hscroll_speed_mob;
		}

		$finaldata = 'data-result="' . htmlspecialchars( wp_json_encode( $horizontal_data, true ), ENT_QUOTES, 'UTF-8' ) . '"';
		$output    = '';
		$get_tmpl  = Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display( $fp_content );

		if ( ! empty( $fix_bgtitle ) ) {
			$output  = '<div class="tp-fixbg tp-fixbg-' . esc_attr( $id ) . '">';
			$output .= Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display( $fixcontent );
			$output .= '</div>';
		}

		$output .= '<div id="tphs_' . esc_attr( $horizontal_unique_id ) . '" class="tp-horizontal-scroll-wrapper tp-horizontal-scroll-wrapper-' . esc_attr( $id ) . ' ' . esc_attr( $horizontal_unique_id ) . '" ' . $finaldata . ' data-active-slide="0" >';
		if ( ! empty( $bg_transition ) ) {
			$output .= '<div class="tp-bg-hscroll-' . esc_attr( $id ) . ' tp-bg-hscroll">';

			if ( ! empty( $bg_transition_content ) ) {
				foreach ( $bg_transition_content as $data ) {
					$bg_img = ! empty( $data['image']['url'] ) ? $data['image']['url'] : '';
					$r_id   = ! empty( $data['_id'] ) ? $data['_id'] : '';
					if ( ! empty( $bg_img ) ) {
						$output .= '<div class="bg-scroll elementor-repeater-item-' . esc_attr( $r_id ) . '" style="background-image:url(' . esc_attr( $bg_img ) . ')"></div>';
					} else {
						$output .= '<div class="bg-scroll elementor-repeater-item-' . esc_attr( $r_id ) . '"></div>';
					}
				}
			}

			$output .= '</div>';
		}

		if ( ! empty( $get_tmpl ) ) {
			$output .= $get_tmpl;
		} else {
			$errortitle   = esc_html__( 'No Template Selected!', 'theplus' );
			$errormassage = esc_html__( 'Please Select Template To Get The Desired Result', 'theplus' );

			$massage = theplus_get_widgetError( $errortitle, $errormassage );
			$output .= $massage;
		}

		$output .= '</div>';

		echo $output;
	}
}
