<?php
/**
 * Class: Premium_Site_Logo
 * Name: Site Logo
 * Slug: premium-site-logo
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

// PremiumAddonsPro Classes.
use PremiumAddonsPro\Includes\PAPRO_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Admin\Includes\Admin_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Premium_Behance.
 */
class Premium_Site_Logo extends Widget_Base {

	/**
	 * Text Conditions.
	 *
	 * @var text_conditions.
	 */
	private $text_conditions = array(
		'relation' => 'or',
		'terms'    => array(
			array(
				'name'     => 'logo_heading',
				'operator' => '!==',
				'value'    => '',
			),
			array(
				'name'     => 'logo_sub_heading',
				'operator' => '!==',
				'value'    => '',
			),
		),
	);

	/**
	 * Image Conditions.
	 *
	 * @var img_conditions.
	 */
	private $img_conditions = array(
		'relation' => 'or',
		'terms'    => array(
			array(
				'relation' => 'and',
				'terms'    => array(
					array(
						'name'     => 'logo_type',
						'operator' => '===',
						'value'    => 'img',
					),
					array(
						'name'     => 'image[url]',
						'operator' => '!==',
						'value'    => '',
					),
				),
			),
			array(
				'relation' => 'and',
				'terms'    => array(
					array(
						'name'     => 'logo_type',
						'operator' => '===',
						'value'    => 'svg',
					),
					array(
						'name'     => 'svg[url]',
						'operator' => '!==',
						'value'    => '',
					),
				),
			),
			array(
				'relation' => 'and',
				'terms'    => array(
					array(
						'name'     => 'logo_type',
						'operator' => '===',
						'value'    => 'lottie',
					),
					array(
						'name'     => 'lottie_url',
						'operator' => '!==',
						'value'    => '',
					),
				),
			),
		),
	);

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 2.8.4
	 * @access public
	 */
	public function check_icon_draw() {

		if ( version_compare( PREMIUM_ADDONS_VERSION, '4.9.26', '<' ) ) {
			return false;
		}

		$is_enabled = Admin_Helper::check_svg_draw( 'premium-site-logo' );
		return $is_enabled;
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-site-logo';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Site Logo', 'premium-addons-pro' );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-pro-site-logo';
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'pa', 'premium', 'site logo', 'retina', 'svg' );
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array(
			'premium-elements',
		);
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS style handles.
	 */
	public function get_style_depends() {
		return array(
			'premium-pro',
		);
	}

	/**
	 * Retrieve Widget Dependent JS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_script_depends() {

		$draw_scripts = $this->check_icon_draw() ? array(
			'pa-tweenmax',
			'pa-motionpath',
		) : array();

		return array_merge(
			$draw_scripts,
			array(
				'elementor-waypoints',
				'premium-pro',
				'lottie-js',
			)
		);
	}

	/**
	 * Widget preview refresh button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Register Site Logo controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$draw_icon = $this->check_icon_draw();

		$this->add_logo_content_controls( $draw_icon );

		$this->add_logo_style_controls( $draw_icon );
	}

	/**
	 * Add Logo Content Controls.
	 *
	 * @access private
	 * @since 2.8.12
	 *
	 * @param bool $draw_icon true if draw svg option is enabled.
	 */
	private function add_logo_content_controls( $draw_icon ) {

		$this->start_controls_section(
			'premium_site_logo_section',
			array(
				'label' => __( 'Logo', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'logo_type',
			array(
				'label'   => __( 'Type', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'img'    => __( 'Image', 'premium-addons-pro' ),
					'svg'    => __( 'SVG', 'premium-addons-pro' ),
					'lottie' => __( 'Lottie Animation', 'premium-addons-pro' ),
				),
				'default' => 'img',
			)
		);

		$this->add_control(
			'image',
			array(
				'label'       => __( 'Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'image' ),
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'logo_type' => 'img',
				),
			)
		);

		$this->add_control(
			'image_2x',
			array(
				'label'       => __( 'Retina Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'image' ),
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'logo_type' => 'img',
				),
			)
		);

		$this->add_control(
			'svg',
			array(
				'label'       => __( 'Choose SVG Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'svg' ),
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'logo_type' => 'svg',
				),
			)
		);

		$this->add_control(
			'svg_code',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'If this is set, then the SVG code will be rendered from this field, not from the media library.', 'premium-addons-pro' ),
				'label_block' => true,
				'condition'   => array(
					'logo_type' => 'svg',
				),
			)
		);

		$this->add_control(
			'lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'logo_type' => 'lottie',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'image_size',
				'default'   => 'thumbnail',
				'condition' => array(
					'logo_type' => 'img',
				),
			)
		);

		$this->add_control(
			'draw_svg',
			array(
				'label'     => __( 'Draw SVG', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_icon ? '' : 'editor-pa-control-disabled',
				'condition' => array(
					'logo_type' => 'svg',
				),
			)
		);

		if ( $draw_icon ) {

			$this->add_control(
				'path_width',
				array(
					'label'     => __( 'Path Thickness', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 0.1,
						),
					),
					'condition' => array(
						'logo_type' => 'svg',
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-logo-svg svg *' => 'stroke-width: {{SIZE}}',
					),
				)
			);

			$this->add_control(
				'svg_sync',
				array(
					'label'     => __( 'Draw All Paths Together', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'logo_type' => 'svg',
						'draw_svg'  => 'yes',
					),
				)
			);

			$this->add_control(
				'frames',
				array(
					'label'       => __( 'Speed', 'premium-addons-pro' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'Larger value means longer animation duration.', 'premium-addons-pro' ),
					'default'     => 5,
					'min'         => 1,
					'max'         => 100,
					'condition'   => array(
						'logo_type' => 'svg',
						'draw_svg'  => 'yes',
					),
				)
			);

		} elseif ( method_exists( 'PremiumAddons\Includes\Helper_Functions', 'get_draw_svg_notice' ) ) {

			Helper_Functions::get_draw_svg_notice(
				$this,
				'site logo',
				array(
					'logo_type' => 'svg',
				)
			);
		}

		$this->add_control(
			'lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'conditions'   => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'logo_type',
							'value' => 'lottie',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'  => 'logo_type',
									'value' => 'svg',
								),
								array(
									'name'  => 'draw_svg',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions'   => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'logo_type',
							'value' => 'lottie',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'  => 'logo_type',
									'value' => 'svg',
								),
								array(
									'name'  => 'draw_svg',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		if ( $draw_icon ) {

			$this->add_control(
				'start_point',
				array(
					'label'       => __( 'Start Point (%)', 'premium-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should start from.', 'premium-addons-pro' ),
					'default'     => array(
						'unit' => '%',
						'size' => 0,
					),
					'condition'   => array(
						'logo_type'       => 'svg',
						'draw_svg'        => 'yes',
						'lottie_reverse!' => 'true',
					),
				)
			);

			$this->add_control(
				'end_point',
				array(
					'label'       => __( 'End Point (%)', 'premium-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should end at.', 'premium-addons-pro' ),
					'default'     => array(
						'size' => 0,
						'unit' => '%',
					),
					'condition'   => array(
						'logo_type'      => 'svg',
						'draw_svg'       => 'yes',
						'lottie_reverse' => 'true',
					),

				)
			);

			$this->add_control(
				'svg_hover',
				array(
					'label'        => __( 'Only Play on Hover', 'premium-addons-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition'    => array(
						'logo_type' => 'svg',
						'draw_svg'  => 'yes',
					),
				)
			);

			$this->add_control(
				'svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'logo_type'   => 'svg',
						'draw_svg'    => 'yes',
						'lottie_loop' => 'true',
					),
				)
			);
		}

		$this->add_responsive_control(
			'svg_size',
			array(
				'label'       => __( 'Width', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 150,
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-lottie-animation, {{WRAPPER}} .premium-logo-svg' => 'width: {{SIZE}}{{UNIT}};',
				),
				'conditions'  => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'logo_type',
							'value' => 'svg',
						),
						array(
							'relation' => 'and', // to avoid applying width on an empty container
							'terms'    => array(
								array(
									'name'  => 'logo_type',
									'value' => 'lottie',
								),
								array(
									'name'     => 'lottie_url',
									'operator' => '!==',
									'value'    => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'svg_height',
			array(
				'label'       => __( 'Height', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 150,
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-lottie-animation, {{WRAPPER}} .premium-logo-svg' => 'height: {{SIZE}}{{UNIT}};',
				),
				'conditions'  => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'logo_type',
							'value' => 'svg',
						),
						array(
							'relation' => 'and',
							'terms'    => array( // to avoid applying height on an empty container
								array(
									'name'  => 'logo_type',
									'value' => 'lottie',
								),
								array(
									'name'     => 'lottie_url',
									'operator' => '!==',
									'value'    => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'linked_logo',
			array(
				'label' => __( 'Linked Logo', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'logo_link',
			array(
				'label'       => __( 'Link', 'textdomain' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => home_url( '/' ),
				),
				'label_block' => true,
				'condition'   => array(
					'linked_logo' => 'yes',
				),
			)
		);

		$this->add_control(
			'logo_heading',
			array(
				'label'       => __( 'Logo Title', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'default'     => get_bloginfo( 'name' ),
			)
		);

		$this->add_control(
			'logo_sub_heading',
			array(
				'label'       => __( 'Tagline', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_logo_layout_section',
			array(
				'label' => __( 'Layout', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'logo_text_display',
			array(
				'label'        => __( 'Display', 'premium-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'toggle'       => false,
				'prefix_class' => 'premium-logo-',
				'toggle'       => false,
				'options'      => array(
					'row'    => array(
						'title' => __( 'Inline', 'premium-addons-pro' ),
						'icon'  => 'eicon-ellipsis-h',
					),
					'column' => array(
						'title' => __( 'Block', 'premium-addons-pro' ),
						'icon'  => 'eicon-ellipsis-v',
					),
				),
				'default'      => 'row',
				'conditions'   => array(
					'relation' => 'and',
					'terms'    => array(
						$this->text_conditions,
						$this->img_conditions,
					),
				),
				'selectors'    => array(
					'{{WRAPPER}} .premium-site-logo-wrapper' => 'flex-direction: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label'     => __( 'Alignment', 'elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => false,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}}:not(.premium-logo-column) .premium-site-logo-wrapper' => 'justify-content: {{VALUE}}',
					'{{WRAPPER}}.premium-logo-column .premium-site-logo-wrapper' => 'align-items: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'text_alignment',
			array(
				'label'     => __( 'Text Alignment', 'elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => false,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'condition' => array(
					'logo_sub_heading!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-logo-text-wrapper' => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'text_ver_alignment',
			array(
				'label'        => __( 'Text Vertical Alignment', 'elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'toggle'       => false,
				'prefix_class' => 'premium-gbadge-',
				'options'      => array(
					'flex-start' => array(
						'title' => __( 'Top', 'elementor' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => __( 'Middle', 'elementor' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'elementor' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'default'      => 'center',
				'conditions'   => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'  => 'logo_text_display',
							'value' => 'row',
						),
						$this->text_conditions,
						$this->img_conditions,
					),
				),
				'selectors'    => array(
					'{{WRAPPER}} .premium-logo-text-wrapper' => 'justify-content: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'spacing',
			array(
				'label'       => __( 'Spacing', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units'  => array( 'px' ),
				'default'     => array(
					'unit' => 'px',
					'size' => 10,
				),
				'conditions'  => array(
					'relation' => 'and',
					'terms'    => array(
						$this->text_conditions,
						$this->img_conditions,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}}.premium-logo-row .premium-site-logo-wrapper' => '-moz-column-gap: {{SIZE}}px; -webkit-column-gap: {{SIZE}}px; column-gap: {{SIZE}}px',
					'{{WRAPPER}}.premium-logo-column .premium-site-logo-wrapper' => 'row-gap: {{SIZE}}px',
				),
			)
		);

		$this->add_responsive_control(
			'txt_spacing',
			array(
				'label'       => __( 'Text Spacing', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units'  => array( 'px' ),
				'condition'   => array(
					'logo_heading!'     => '',
					'logo_sub_heading!' => '',
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-logo-heading' => 'margin-bottom: {{SIZE}}px',
				),
			)
		);

		$this->add_control(
			'logo_position',
			array(
				'label'      => __( 'Logo Position', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SELECT,
				'default'    => '0',
				'options'    => array(
					'0' => __( 'Before Text', 'premium-addons-pro' ),
					'2' => __( 'After Text', 'premium-addons-pro' ),
				),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						$this->text_conditions,
						$this->img_conditions,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-logo-img-wrapper' => 'order: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$docs = array(
			'https://premiumaddons.com/docs/elementor-site-logo-widget-tutorial/' => __( 'Getting started »', 'premium-addons-for-elementor' ),
		);

		$doc_index = 1;
		foreach ( $docs as $url => $title ) {

			$doc_url = Helper_Functions::get_campaign_link( $url, 'editor-page', 'wp-editor', 'get-support' );

			$this->add_control(
				'doc_' . $doc_index,
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, $title ),
					'content_classes' => 'editor-pa-doc',
				)
			);

			$doc_index++;

		}

		$this->end_controls_section();
	}

	/**
	 * Add Logo Style Controls.
	 *
	 * @access private
	 * @since 2.8.12
	 *
	 * @param bool $draw_icon true if draw svg option is enabled.
	 */
	private function add_logo_style_controls( $draw_icon ) {

		$this->add_img_style_controls( $draw_icon );

		$this->add_text_style_controls();
	}

	/**
	 * Add Logo Image Style Controls.
	 *
	 * @access private
	 * @since 2.8.12
	 *
	 * @param bool $draw_icon true if draw svg option is enabled.
	 */
	private function add_img_style_controls( $draw_icon ) {

		$this->start_controls_section(
			'premium_logo_style_section',
			array(
				'label'      => __( 'Logo Image', 'premium-addons-pro' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => $this->img_conditions,
			)
		);

		$this->add_control(
			'logo_svg_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'logo_type' => 'svg',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-logo-svg svg, {{WRAPPER}} .premium-logo-svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'stroke_color',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'condition' => array(
						'logo_type' => 'svg',
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-logo-svg svg *' => 'stroke: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'svg_color',
				array(
					'label'     => __( 'After Draw Fill Color', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => false,
					'condition' => array(
						'logo_type' => 'svg',
						'draw_svg'  => 'yes',
					),
				)
			);
		}

		$this->add_control(
			'lotti_bg',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'logo_type' => 'lottie',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-logo-img-wrapper' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'logo_border',
				'selector' => '{{WRAPPER}} .premium-logo-img-wrapper',
			)
		);

		$this->add_responsive_control(
			'logo_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'condition'  => array(
					'logo_adv_radius!' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-logo-img-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};;',
				),
			)
		);

		$this->add_control(
			'logo_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'logo_adv_radius_val',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'selectors' => array(
					'{{WRAPPER}} .premium-logo-img-wrapper' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'logo_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'logo_box_shadow',
				'selector' => '{{WRAPPER}} .premium-logo-img-wrapper',
			)
		);

		$this->start_controls_tabs( 'logo_style_tabs' );

		$this->start_controls_tab(
			'logo_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_responsive_control(
			'logo_opacity',
			array(
				'label'       => __( 'Opacity', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-logo-img-wrapper' => 'opacity: {{SIZE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .premium-logo-img-wrapper',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'logo_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_responsive_control(
			'logo_opacity_hover',
			array(
				'label'       => __( 'Opacity', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-logo-img-wrapper:hover' => 'opacity: {{SIZE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .premium-logo-img-wrapper:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Add Logo Text Style Controls.
	 *
	 * @access private
	 * @since 2.8.12
	 */
	private function add_text_style_controls() {

		$this->start_controls_section(
			'premium_heading_style_section',
			array(
				'label'      => __( 'Logo Text', 'premium-addons-pro' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => $this->text_conditions,
			)
		);

		$this->add_control(
			'heading_style',
			array(
				'label'     => __( 'Logo Title', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'logo_heading!' => '',
				),
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'logo_heading!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-logo-heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'heading_typo',
				'condition' => array(
					'logo_heading!' => '',
				),
				'selector'  => '{{WRAPPER}} .premium-logo-heading',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'heading_shadow',
				'condition' => array(
					'logo_heading!' => '',
				),
				'selector'  => '{{WRAPPER}} .premium-logo-heading',
			)
		);

		$this->add_control(
			'sub_heading_style',
			array(
				'label'     => __( 'Tagline', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'logo_sub_heading!' => '',
				),
			)
		);

		$this->add_control(
			'sub_heading_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'logo_sub_heading!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-logo-sub-heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'sub_heading_typo',
				'condition' => array(
					'logo_sub_heading!' => '',
				),
				'selector'  => '{{WRAPPER}} .premium-logo-sub-heading',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'sub_heading_shadow',
				'condition' => array(
					'logo_sub_heading!' => '',
				),
				'selector'  => '{{WRAPPER}} .premium-logo-sub-heading',
			)
		);

		$this->add_control(
			'text_gen',
			array(
				'label'     => __( 'Container', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'text_bg',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-logo-text-wrapper' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'text_box_shadow',
				'selector' => '{{WRAPPER}} .premium-logo-text-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'text_border',
				'selector' => '{{WRAPPER}} .premium-logo-text-wrapper',
			)
		);

		$this->add_responsive_control(
			'text_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'condition'  => array(
					'text_adv_radius!' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-logo-text-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};;',
				),
			)
		);

		$this->add_control(
			'text_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'text_adv_radius_val',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'selectors' => array(
					'{{WRAPPER}} .premium-logo-text-wrapper' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'text_adv_radius' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'text_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-logo-text-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};;',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render Site Logo widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings();

		$widget_id = $this->get_id();

		$linked_logo = 'yes' === $settings['linked_logo'] ? true : false;

		$this->add_render_attribute( 'logo_img_container', 'class', 'premium-logo-img-wrapper' );

		if ( 'svg' === $settings['logo_type'] && 'yes' === $settings['draw_svg'] ) {

			$this->add_render_attribute(
				'logo_img_container',
				'class',
				array(
					'elementor-invisible',
					'premium-drawer-hover',
				)
			);
		}

		if ( $linked_logo ) :

			$this->add_link_attributes( 'logo_link', $settings['logo_link'] );

			if ( ! empty( $settings['logo_heading'] ) ) {

				$this->add_render_attribute( 'logo_link', 'aria-label', $settings['logo_heading'] );

			} elseif ( 'lottie' !== $settings['logo_type'] ) {

				$control_id = 'image' === $settings['logo_type'] ? 'image' : 'svg';

				$image_alt = Control_Media::get_image_alt( $settings[ $control_id ] );

				$this->add_render_attribute( 'logo_link', 'aria-label', $image_alt );

			}

			?>
			<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'logo_link' ) ); ?>>
		<?php endif; ?>

			<div class="premium-site-logo-wrapper">
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'logo_img_container' ) ); ?>>
					<?php $this->get_logo_img_html( $settings ); ?>
				</div>
				<?php if ( ! empty( $settings['logo_heading'] ) || ! empty( $settings['logo_sub_heading'] ) ) : ?>
					<div class="premium-logo-text-wrapper">
						<?php if ( ! empty( $settings['logo_heading'] ) ) : ?>
							<span class="premium-logo-heading"> <?php echo ( $settings['logo_heading'] ); ?> </span>
						<?php endif; ?>
						<?php if ( ! empty( $settings['logo_sub_heading'] ) ) : ?>
							<span class="premium-logo-sub-heading"><?php echo ( $settings['logo_sub_heading'] ); ?> </span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php if ( $linked_logo ) : ?>
		</a>
		<?php endif; ?>
		<?php
	}

	/**
	 * Get logo image HTML.
	 *
	 * @access public
	 * @since 2.8.12
	 *
	 * @param array $settings widget settings.
	 *
	 * @return string|bool logo HTML|false.
	 */
	public function get_logo_img_html( $settings ) {

		$settings = $this->get_settings();

		$type = $settings['logo_type'];

		if ( 'img' === $type ) {

			$settings['image_data'] = Helper_Functions::get_image_data( $settings['image']['id'], $settings['image']['url'], $settings['image_size_size'] );

			if ( ! $settings['image_data'] ) {
				return false;
			}

			if ( 'custom' === $settings['image_size_size'] ) {
				$settings['image_data']['image_size_custom_dimension'] = $settings['image_size_custom_dimension'];
			}

			PAPRO_Helper::get_attachment_image_html( $settings, 'image_size', 'image_data', '', true );

		} elseif ( 'lottie' === $type ) {

			$this->add_render_attribute(
				'lottie_wrapper',
				array(
					'class'               => array(
						'premium-lottie-animation',
					),
					'data-lottie-url'     => $settings['lottie_url'],
					'data-lottie-loop'    => $settings['lottie_loop'],
					'data-lottie-reverse' => $settings['lottie_reverse'],
				)
			);

			?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'lottie_wrapper' ) ); ?>></div>
			<?php
		} else {

			if ( ! empty( $settings['svg']['url'] ) ) :

				if ( ! empty( $settings['svg_code'] ) ) {
					$svg_code = $settings['svg_code'];
				} else {
					$svg_code = wp_remote_get( $settings['svg']['url'] );
				}

				if ( ! is_wp_error( $svg_code ) ) :

					$id = isset( $settings['svg']['id'] ) && ! empty( $settings['svg']['id'] ) ? 'premium-svg-' . $settings['svg']['id'] : '';

					$this->add_render_attribute(
						'svg_container',
						array(
							'id'    => esc_attr( $id ),
							'class' => 'premium-logo-svg',
						)
					);

					if ( 'yes' === $settings['draw_svg'] ) {

						$this->add_render_attribute(
							'svg_container',
							array(
								'class'            => 'premium-svg-drawer',
								'data-svg-reverse' => $settings['lottie_reverse'],
								'data-svg-loop'    => $settings['lottie_loop'],
								'data-svg-sync'    => $settings['svg_sync'],
								'data-svg-hover'   => $settings['svg_hover'],
								'data-svg-fill'    => $settings['svg_color'],
								'data-svg-frames'  => $settings['frames'],
								'data-svg-yoyo'    => $settings['svg_yoyo'],
								'data-svg-point'   => $settings['lottie_reverse'] ? $settings['end_point']['size'] : $settings['start_point']['size'],
							)
						);
					}
					?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'svg_container' ) ); ?> >
						<?php
						if ( ! empty( $settings['svg_code'] ) ) :
							$this->print_unescaped_setting( 'svg_code' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						else :
							echo wp_remote_retrieve_body( $svg_code ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						endif;
						?>

					</div>
					<?php
				endif;
			endif;
		}
	}
}
