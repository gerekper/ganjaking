<?php
/**
 * UAEL Instagram Feed.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\InstagramFeed\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Instagram_Feed.
 */
class Instagram_Feed extends Common_Widget {

	/**
	 * Instagram access token.
	 *
	 * @since 1.36.0
	 * @access public
	 * @var null
	 */
	private $insta_access_token = null;

	/**
	 * Instagram api url.
	 *
	 * @since 1.36.0
	 * @access public
	 * @var string
	 */
	private $insta_api_url = 'https://www.instagram.com/';

	/**
	 * Instagram graph api url.
	 *
	 * @since 1.36.0
	 * @access public
	 * @var string
	 */
	private $insta_graph_api_url = 'https://graph.instagram.com/';

	/**
	 * Retrieve Widget name.
	 *
	 * @return string Widget name.
	 * @since 1.36.0
	 * @access public
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Instagram_Feed' );
	}

	/**
	 * Retrieve Widget title.
	 *
	 * @return string Widget title.
	 * @since 1.36.0
	 * @access public
	 */
	public function get_title() {
		return parent::get_widget_title( 'Instagram_Feed' );
	}

	/**
	 * Retrieve Widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.36.0
	 * @access public
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Instagram_Feed' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @return string Widget keywords.
	 * @since 1.36.0
	 * @access public
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Instagram_Feed' );
	}

	/**
	 * Get Script Depends.
	 *
	 * @return array scripts.
	 * @since 1.36.0
	 * @access public
	 */
	public function get_script_depends() {
		return array(
			'uael-frontend-script',
			'uael-isotope',
			'imagesloaded',
			'uael-slick',
		);
	}

	/**
	 * Register widget controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return void
	 */
	protected function register_controls() {
		// Content tab.
		$this->register_layout_controls();
		$this->register_additional_controls();

		// Style tab.
		$this->register_style_controls();

		// Content tab: Helpful section.
		$this->register_helpful_information();
	}

	/**
	 * Register widget layout related controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return void
	 */
	protected function register_layout_controls() {
		$this->start_controls_section(
			'uae_insta_section_layout',
			array(
				'label' => __( 'Layout Settings', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		if ( ! $this->is_instagram_integration_active() ) {
			$widget_list = UAEL_Helper::get_widget_list();
			$admin_link  = $widget_list['Instagram_Feed']['setting_url'];

			$this->add_control(
				'uae_insta_feed_error',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'It seems that Instagram Feed API keys are not configured. To use the widget please integrate the API keys from <a href="%s" target="_blank" rel="noopener">here</a>', 'uael' ), $admin_link ),
					'content_classes' => 'elementor-panel-alert elementor-alert elementor-panel-alert-warning',
				)
			);
		}

		$this->add_control(
			'uae_insta_layout_type',
			array(
				'label'              => __( 'Layout Type', 'uael' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'grid',
				'options'            => array(
					'grid'    => __( 'Grid', 'uael' ),
					'masonry' => __( 'Masonry', 'uael' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'uae_insta_grid_cols',
			array(
				'label'              => __( 'Columns', 'uael' ),
				'type'               => Controls_Manager::SELECT,
				'label_block'        => false,
				'default'            => '3',
				'tablet_default'     => '3',
				'mobile_default'     => '2',
				'options'            => array(
					'1' => __( '1', 'uael' ),
					'2' => __( '2', 'uael' ),
					'3' => __( '3', 'uael' ),
					'4' => __( '4', 'uael' ),
					'5' => __( '5', 'uael' ),
					'6' => __( '6', 'uael' ),
					'7' => __( '7', 'uael' ),
					'8' => __( '8', 'uael' ),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-instagram-feed-grid #uael-instafeed-{{ID}}' => 'display: grid; grid-template-columns: repeat({{VALUE}}, 1fr);',
					'{{WRAPPER}} .uael-instagram-feed-masonry .uael-feed-item' => 'width: calc( 100% / {{VALUE}} )',
				),
				'render_type'        => 'template',
				'frontend_available' => true,
				'condition'          => array(
					'uae_insta_layout_type' => array( 'grid', 'masonry' ),
				),
			)
		);

		$this->add_control(
			'uae_insta_img_square',
			array(
				'label'        => __( 'Square Images', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'condition'    => array(
					'uae_insta_layout_type' => array( 'grid' ),
				),
			)
		);

		$this->add_control(
			'uae_insta_imag_count',
			array(
				'label'      => __( 'Image Count', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array( 'size' => 3 ),
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => '',
			)
		);

		$this->add_control(
			'uae_insta_img_resolution',
			array(
				'label'   => __( 'Image Resolution', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'thumbnail'           => __( 'Thumbnail (150x150)', 'uael' ),
					'low_resolution'      => __( 'Low Resolution (320x320)', 'uael' ),
					'standard_resolution' => __( 'Standard Resolution (640x640)', 'uael' ),
					'high'                => __( 'High Resolution (original)', 'uael' ),
				),
				'default' => 'low_resolution',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget additional options related controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return void
	 */
	protected function register_additional_controls() {
		$this->start_controls_section(
			'uae_insta_section_additional',
			array(
				'label' => __( 'Additional Settings', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'uae_insta_cache_options_heading',
			array(
				'label' => __( 'Cache', 'uael' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'uae_insta_cache_timeout',
			array(
				'label'       => esc_html__( 'Cache Timeout', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Feed will be cached for the selected time duration and refreshed after that.', 'uael' ),
				'default'     => 'hour',
				'options'     => array(
					'none'   => esc_html__( 'None', 'uael' ),
					'minute' => esc_html__( 'Minute', 'uael' ),
					'hour'   => esc_html__( 'Hour', 'uael' ),
					'day'    => esc_html__( 'Day', 'uael' ),
					'week'   => esc_html__( 'Week', 'uael' ),
				),
			)
		);

		$this->add_control(
			'uae_insta_show_more_options_heading',
			array(
				'label'     => __( 'Content', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'uae_insta_show_caption',
			array(
				'label'        => __( 'Caption', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'uae_insta_caption_length',
			array(
				'label'     => __( 'Caption Length', 'uael' ),
				'type'      => Controls_Manager::NUMBER,
				'dynamic'   => array(
					'active' => true,
				),
				'default'   => 30,
				'condition' => array(
					'uae_insta_show_caption' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_content_visibility',
			array(
				'label'     => __( 'Show Content', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'always',
				'options'   => array(
					'always' => __( 'Always', 'uael' ),
					'hover'  => __( 'On Hover', 'uael' ),
					'below'  => __( 'Below', 'uael' ),
				),
				'condition' => array(
					'uae_insta_show_caption' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_img_lightbox',
			array(
				'label'        => __( 'Lightbox Effect', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'uae_insta_img_link',
			array(
				'label'        => __( 'Image Link', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'condition'    => array(
					'uae_insta_img_lightbox!' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_show_profile_link',
			array(
				'label'        => __( 'Instagram Profile Link', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'uae_insta_profile_title_position',
			array(
				'label'        => __( 'Position', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'above'   => __( 'Above', 'uael' ),
					'overlay' => __( 'Overlay', 'uael' ),
					'below'   => __( 'Below', 'uael' ),
				),
				'default'      => 'below',
				'prefix_class' => 'uael-insta-profile-link-position-',
				'condition'    => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_profile_link_title',
			array(
				'label'     => __( 'Link Title', 'uael' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Follow @Instagram', 'uael' ),
				'condition' => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_profile_url',
			array(
				'label'       => __( 'Instagram Profile URL', 'uael' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://www.your-link.com',
				'default'     => array(
					'url' => '#',
				),
				'condition'   => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_profile_title_icon',
			array(
				'label'            => __( 'Title Icon', 'uael' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'uae_insta_title_icon',
				'recommended'      => array(
					'fa-brands'  => array(
						'instagram',
					),
					'fa-regular' => array(
						'user',
						'user-circle',
					),
					'fa-solid'   => array(
						'user',
						'user-circle',
						'user-check',
						'user-graduate',
						'user-md',
						'user-plus',
						'user-tie',
					),
				),
				'default'          => array(
					'value'   => 'fab fa-instagram',
					'library' => 'fa-brands',
				),
				'condition'        => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_profile_title_icon_position',
			array(
				'label'     => __( 'Icon Position', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'before_title' => __( 'Before Title', 'uael' ),
					'after_title'  => __( 'After Title', 'uael' ),
				),
				'default'   => 'before_title',
				'condition' => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget style related controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return void
	 */
	protected function register_style_controls() {

		$this->register_layout_style_controls();
		$this->register_images_style_controls();
		$this->register_content_style_controls();
		$this->register_overlay_style_controls();

	}

	/**
	 * Register widget layout style related controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return void
	 */
	protected function register_layout_style_controls() {
		$this->start_controls_section(
			'uae_insta_layout_styles_heading',
			array(
				'label'     => __( 'Layout', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'uae_insta_layout_type' => array( 'grid', 'masonry' ),
				),
			)
		);

		$this->add_responsive_control(
			'uae_insta_columns_gap',
			array(
				'label'              => __( 'Columns Gap', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => '10',
					'unit' => 'px',
				),
				'size_units'         => array( 'px', '%' ),
				'range'              => array(
					'px' => array(
						'max' => 100,
					),
				),
				'tablet_default'     => array(
					'unit' => 'px',
				),
				'mobile_default'     => array(
					'unit' => 'px',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-instafeed-grid .uael-feed-item' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .uael-instafeed-grid' => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2); margin-right: calc(-{{SIZE}}{{UNIT}}/2);',
				),
				'render_type'        => 'template',
				'frontend_available' => true,
				'condition'          => array(
					'uae_insta_layout_type' => array( 'grid', 'masonry' ),
				),
			)
		);

		$this->add_responsive_control(
			'uae_insta_rows_gap',
			array(
				'label'              => __( 'Rows Gap', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => '10',
					'unit' => 'px',
				),
				'size_units'         => array( 'px', '%' ),
				'range'              => array(
					'px' => array(
						'max' => 100,
					),
				),
				'tablet_default'     => array(
					'unit' => 'px',
				),
				'mobile_default'     => array(
					'unit' => 'px',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-instafeed-grid .uael-feed-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'render_type'        => 'template',
				'frontend_available' => true,
				'condition'          => array(
					'uae_insta_layout_type' => array( 'grid', 'masonry' ),
				),
				'separator'          => 'after',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget images style related controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return void
	 */
	protected function register_images_style_controls() {
		$this->start_controls_section(
			'uae_insta_img_styles_heading',
			array(
				'label' => __( 'Images', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'uae_insta_img_style_tabs' );

		$this->start_controls_tab(
			'uae_insta_image_normal_tab',
			array(
				'label' => __( 'Normal', 'uael' ),
			)
		);

		$this->add_control(
			'uae_insta_img_grayscale',
			array(
				'label'        => __( 'Grayscale Image', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'uae_insta_img_border',
				'label'       => __( 'Border', 'uael' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .uael-instagram-feed .uael-if-img',
			)
		);

		$this->add_control(
			'uae_insta_img_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-instagram-feed .uael-if-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'uae_insta_image_hover_tab',
			array(
				'label' => __( 'Hover', 'uael' ),
			)
		);

		$this->add_control(
			'uae_insta_img_grayscale_hover',
			array(
				'label'        => __( 'Grayscale Image', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'uae_insta_img_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-instagram-feed .uael-feed-item:hover .uael-if-img' => 'border-color: {{VALUE}};',
				),
				'separator' => 'after',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register widget content style related controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return void
	 */
	protected function register_content_style_controls() {
		$this->start_controls_section(
			'uae_insta_content_styles_heading',
			array(
				'label'     => __( 'Content', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'uae_insta_show_caption' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'uae_insta_content_typography',
				'label'     => __( 'Typography', 'uael' ),
				'selector'  => '{{WRAPPER}} .uael-feed-item .uael-overlay-container',
				'condition' => array(
					'uae_insta_show_caption' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_likes_comments_caption_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-feed-item .uael-overlay-container' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'uae_insta_show_caption' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_content_vertical_align',
			array(
				'label'                => __( 'Vertical Align', 'uael' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'toggle'               => false,
				'default'              => 'bottom',
				'options'              => array(
					'top'    => array(
						'title' => __( 'Top', 'uael' ),
						'icon'  => 'eicon-v-align-top',
					),
					'middle' => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'uael' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors_dictionary' => array(
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				),
				'selectors'            => array(
					'{{WRAPPER}} .uael-overlay-container' => 'justify-content: {{VALUE}};',
				),
				'conditions'           => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'uae_insta_content_visibility',
							'operator' => '!==',
							'value'    => 'below',
						),
						array(
							'name'     => 'uae_insta_show_caption',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'uae_insta_content_horizontal_align',
			array(
				'label'                => __( 'Horizontal Align', 'uael' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'toggle'               => false,
				'default'              => 'center',
				'options'              => array(
					'left'   => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				),
				'selectors'            => array(
					'{{WRAPPER}} .uael-overlay-container' => 'align-items: {{VALUE}};',
				),
				'condition'            => array(
					'uae_insta_show_caption' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_content_text_align',
			array(
				'label'     => __( 'Text Align', 'uael' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .uael-overlay-container' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'uae_insta_show_caption' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'uae_insta_content_padding',
			array(
				'label'              => __( 'Padding', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', 'em', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .uael-overlay-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'frontend_available' => true,
				'condition'          => array(
					'uae_insta_show_caption' => 'yes',
				),
				'separator'          => 'after',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'uae_insta_profile_link_styles_heading',
			array(
				'label'     => __( 'Profile Link', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_profile_link_title_verticle_position',
			array(
				'label'        => __( 'Verticle Align', 'uael' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'default'      => 'bottom',
				'options'      => array(
					'top'    => array(
						'title' => __( 'Top', 'uael' ),
						'icon'  => 'eicon-v-align-top',
					),
					'middle' => array(
						'title' => __( 'Middle', 'uael' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'uael' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'prefix_class' => 'uael-insta-title-',
				'condition'    => array(
					'uae_insta_show_profile_link'      => 'yes',
					'uae_insta_profile_title_position' => 'overlay',
				),
			)
		);

		$this->add_control(
			'uae_insta_profile_link_title_horizontal_position',
			array(
				'label'                => __( 'Horizontal Align', 'uael' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'toggle'               => false,
				'default'              => 'center',
				'options'              => array(
					'left'   => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				),
				'selectors'            => array(
					'{{WRAPPER}}.uael-insta-profile-link-position-above .uael-instagram-feed-title-wrap,
					{{WRAPPER}}.uael-insta-profile-link-position-below .uael-instagram-feed-title-wrap' => 'align-self: {{VALUE}}',
				),
				'condition'            => array(
					'uae_insta_show_profile_link'      => 'yes',
					'uae_insta_profile_title_position' => array( 'above', 'below' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'uae_insta_profile_link_title_typography',
				'label'     => __( 'Typography', 'uael' ),
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'selector'  => '{{WRAPPER}} .uael-instagram-feed-title',
				'condition' => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'uae_insta_profile_link_title_style_tabs' );

		$this->start_controls_tab(
			'uae_insta_profile_link_title_normal_tab',
			array(
				'label'     => __( 'Normal', 'uael' ),
				'condition' => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_profile_link_title_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-instagram-feed-title-wrap a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-instagram-feed-title-wrap .uael-icon svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_profile_link_title_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-instagram-feed-title-wrap' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'uae_insta_profile_link_title_border',
				'label'       => __( 'Border', 'uael' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .uael-instagram-feed-title-wrap',
				'condition'   => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_profile_link_title_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-instagram-feed-title-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'uae_insta_profile_link_title_hover_tab',
			array(
				'label'     => __( 'Hover', 'uael' ),
				'condition' => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_profile_link_title_color_hover',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-instagram-feed-title-wrap a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-instagram-feed-title-wrap a:hover .uael-icon svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_profile_link_title_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-instagram-feed-title-wrap:hover' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'uae_insta_profile_link_title_border_hover',
				'label'       => __( 'Border', 'uael' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .uael-instagram-feed-title-wrap:hover',
				'condition'   => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_profile_link_title_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-instagram-feed-title-wrap:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'uae_insta_profile_link_title_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-instagram-feed-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'uae_insta_profile_link_title_icon_heading',
			array(
				'label'     => __( 'Icon', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'uae_insta_show_profile_link' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'uae_insta_profile_link_title_icon_spacing',
			array(
				'label'              => __( 'Spacing', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array( 'size' => 4 ),
				'range'              => array(
					'px' => array(
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					),
				),
				'size_units'         => array( 'px' ),
				'selectors'          => array(
					'{{WRAPPER}} .uael-instagram-feed .uael-insta-icon-before_title' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-instagram-feed .uael-insta-icon-after_title' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'frontend_available' => true,
				'condition'          => array(
					'uae_insta_show_profile_link' => 'yes',
				),
				'separator'          => 'after',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget overlay style related controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return void
	 */
	protected function register_overlay_style_controls() {
		$this->start_controls_section(
			'uae_insta_overlay_styles_heading',
			array(
				'label' => __( 'Overlay', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'uae_insta_overlay_blend_mode',
			array(
				'label'     => __( 'Blend Mode', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'normal',
				'options'   => array(
					'normal'      => __( 'Normal', 'uael' ),
					'multiply'    => __( 'Multiply', 'uael' ),
					'screen'      => __( 'Screen', 'uael' ),
					'overlay'     => __( 'Overlay', 'uael' ),
					'darken'      => __( 'Darken', 'uael' ),
					'lighten'     => __( 'Lighten', 'uael' ),
					'color-dodge' => __( 'Color Dodge', 'uael' ),
					'color'       => __( 'Color', 'uael' ),
					'hue'         => __( 'Hue', 'uael' ),
					'hard-light'  => __( 'Hard Light', 'uael' ),
					'soft-light'  => __( 'Soft Light', 'uael' ),
					'difference'  => __( 'Difference', 'uael' ),
					'exclusion'   => __( 'Exclusion', 'uael' ),
					'saturation'  => __( 'Saturation', 'uael' ),
					'luminosity'  => __( 'Luminosity', 'uael' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-instagram-feed .uael-overlay-container' => 'mix-blend-mode: {{VALUE}};',
				),
			)
		);

		$this->start_controls_tabs( 'uae_insta_overlay_style_tabs' );

		$this->start_controls_tab(
			'uae_insta_overlay_normal_tab',
			array(
				'label' => __( 'Normal', 'uael' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'uae_insta_img_overlay',
				'label'    => __( 'Overlay', 'uael' ),
				'types'    => array( 'classic', 'gradient' ),
				'exclude'  => array(
					'image',
				),
				'selector' => '{{WRAPPER}} .uael-instagram-feed .uael-overlay-container',
			)
		);

		$this->add_control(
			'uae_insta_img_overlay_margin',
			array(
				'label'     => __( 'Margin', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-instagram-feed .uael-overlay-container' => 'top: {{SIZE}}px; bottom: {{SIZE}}px; left: {{SIZE}}px; right: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'uae_insta_img_overlay_opacity',
			array(
				'label'      => __( 'Overlay Opacity', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'size_units' => '',
				'selectors'  => array(
					'{{WRAPPER}} .uael-instagram-feed .uael-overlay-container' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'uae_insta_overlay_hover_tab',
			array(
				'label' => __( 'Hover', 'uael' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'uae_insta_img_overlay_hover',
				'label'    => __( 'Overlay', 'uael' ),
				'types'    => array( 'classic', 'gradient' ),
				'exclude'  => array(
					'image',
				),
				'selector' => '{{WRAPPER}} .uael-instagram-feed .uael-feed-item:hover .uael-overlay-container',
			)
		);

		$this->add_control(
			'uae_insta_img_overlay_opacity_hover',
			array(
				'label'      => __( 'Overlay Opacity', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'size_units' => '',
				'selectors'  => array(
					'{{WRAPPER}} .uael-instagram-feed .uael-feed-item:hover .uael-overlay-container' => 'opacity: {{SIZE}};',
				),
				'separator'  => 'after',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		$help_link_1 = UAEL_DOMAIN . 'docs/instagram-feed-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

		if ( parent::is_internal_links() ) {
			$this->start_controls_section(
				'uae_insta_section_helpful_info',
				array(
					'label' => __( 'Helpful Information', 'uael' ),
				)
			);

			$this->add_control(
				'uae_insta_help_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . $help_link_1 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Returns instagram access token stored in integration settings.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return mixed|null
	 */
	protected function uae_get_insta_access_token() {
		$uae_options = UAEL_Helper::get_integrations_options();

		if ( ! $this->insta_access_token ) {
			$access_token = $uae_options['instagram_app_token'];
			if ( $access_token ) {
				$this->insta_access_token = $access_token;
			}
		}

		return $this->insta_access_token;
	}

	/**
	 * Returns instagram feed media endpoint url.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return string
	 */
	protected function uae_get_feed_endpoint() {
		return $this->insta_graph_api_url . 'me/media/';
	}

	/**
	 * Returns instagram user me node endpoint url.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return string
	 */
	protected function uae_get_user_endpoint() {
		return $this->insta_graph_api_url . 'me/';
	}

	/**
	 * Returns instagram user media endpoint url.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return string
	 */
	protected function uae_get_user_media_endpoint() {
		return $this->insta_graph_api_url . '%s/media/';
	}

	/**
	 * Returns instagram user endpoint url.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return string
	 */
	protected function uae_get_media_endpoint() {
		return $this->insta_graph_api_url . '%s/';
	}

	/**
	 * Returns instagram user media endpoint url with related arguments.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @param string $user_id user id.
	 *
	 * @return string
	 */
	protected function uae_get_user_media_url( $user_id ) {
		$url = sprintf( $this->uae_get_user_media_endpoint(), $user_id );
		$url = add_query_arg(
			array(
				'access_token' => $this->uae_get_insta_access_token(),
				'fields'       => 'id,like_count',
			),
			$url
		);
		return $url;
	}

	/**
	 * Returns instagram media endpoint url with related arguments.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @param string $media_id media id.
	 *
	 * @return string
	 */
	protected function uae_get_media_url( $media_id ) {
		$url = sprintf( $this->uae_get_media_endpoint(), $media_id );
		$url = add_query_arg(
			array(
				'access_token' => $this->uae_get_insta_access_token(),
				'fields'       => 'id,media_type,media_url,timestamp,like_count',
			),
			$url
		);
		return $url;
	}

	/**
	 * Returns user medias.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @param string $user_id user id.
	 *
	 * @return array|mixed|\WP_Error
	 */
	protected function uae_get_insta_user_media( $user_id ) {
		$result = $this->uae_get_insta_remote( $this->uae_get_user_media_url( $user_id ) );
		return $result;
	}

	/**
	 * Returns user medias.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @param string $media_id user id.
	 *
	 * @return array|mixed|\WP_Error
	 */
	protected function uae_get_insta_media( $media_id ) {
		$result = $this->uae_get_insta_remote( $this->uae_get_media_url( $media_id ) );
		return $result;
	}

	/**
	 * Returns api url based on images source.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return string
	 */
	protected function uae_get_fetch_url() {
		$url = $this->uae_get_feed_endpoint();
		$url = add_query_arg(
			array(
				'fields'       => 'id,media_type,media_url,thumbnail_url,permalink,caption,likes',
				'limit'        => 100,
				'access_token' => $this->uae_get_insta_access_token(),
			),
			$url
		);

		return $url;
	}

	/**
	 * Returns instagram feed thumbnails.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @param mixed $post posts data.
	 *
	 * @return false[]
	 */
	protected function uae_get_insta_feed_thumbnail_data( $post ) {
		$thumbnail = array(
			'thumbnail' => false,
			'low'       => false,
			'standard'  => false,
			'high'      => false,
		);

		if ( ! empty( $post['images'] ) && is_array( $post['images'] ) ) {
			$data = $post['images'];

			$thumbnail['thumbnail'] = array(
				'src'           => $data['thumbnail']['url'],
				'config_width'  => $data['thumbnail']['width'],
				'config_height' => $data['thumbnail']['height'],
			);

			$thumbnail['low'] = array(
				'src'           => $data['low_resolution']['url'],
				'config_width'  => $data['low_resolution']['width'],
				'config_height' => $data['low_resolution']['height'],
			);

			$thumbnail['standard'] = array(
				'src'           => $data['standard_resolution']['url'],
				'config_width'  => $data['standard_resolution']['width'],
				'config_height' => $data['standard_resolution']['height'],
			);

			$thumbnail['high'] = $thumbnail['standard'];
		}

		return $thumbnail;
	}

	/**
	 * Returns instagram feed api response data.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @param mixed $response posts data.
	 *
	 * @return array|void
	 */
	protected function uae_get_insta_feed_response_data( $response ) {
		$settings = $this->get_settings_for_display();

		if ( ! array_key_exists( 'data', $response ) ) { // Avoid PHP notices.
			return;
		}

		$response_posts = $response['data'];

		if ( empty( $response_posts ) ) {
			return array();
		}

		$return_data  = array();
		$images_count = ! empty( $settings['uae_insta_imag_count']['size'] ) ? $settings['uae_insta_imag_count']['size'] : 5;
		$posts        = array_slice( $response_posts, 0, $images_count, true );

		foreach ( $posts as $post ) {
			$_post = array();

			$_post['id']       = $post['id'];
			$_post['link']     = $post['permalink'];
			$_post['caption']  = '';
			$_post['image']    = 'VIDEO' === $post['media_type'] ? $post['thumbnail_url'] : $post['media_url'];
			$_post['comments'] = ! empty( $post['comments_count'] ) ? $post['comments_count'] : 0;
			$_post['likes']    = ! empty( $post['likes_count'] ) ? $post['likes_count'] : 0;

			$_post['thumbnail'] = $this->uae_get_insta_feed_thumbnail_data( $post );

			if ( ! empty( $post['caption'] ) ) {
				$_post['caption'] = wp_html_excerpt( $post['caption'], (int) $this->get_settings_for_display( 'uae_insta_caption_length' ), '&hellip;' );
			}

			$return_data[] = $_post;
		}

		return $return_data;
	}

	/**
	 * Returns instagram tag thumbnails.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @param mixed $post posts data.
	 *
	 * @return false[]
	 */
	protected function uae_get_insta_tags_thumbnail_data( $post ) {
		$post = $post['node'];

		$thumbnail = array(
			'thumbnail' => false,
			'low'       => false,
			'standard'  => false,
			'high'      => false,
		);

		if ( is_array( $post['thumbnail_resources'] ) && ! empty( $post['thumbnail_resources'] ) ) {
			foreach ( $post['thumbnail_resources'] as $key => $resources_data ) {

				if ( 150 === $resources_data['config_width'] ) {
					$thumbnail['thumbnail'] = $resources_data;
					continue;
				}

				if ( 320 === $resources_data['config_width'] ) {
					$thumbnail['low'] = $resources_data;
					continue;
				}

				if ( 640 === $resources_data['config_width'] ) {
					$thumbnail['standard'] = $resources_data;
					continue;
				}
			}
		}

		if ( ! empty( $post['display_url'] ) ) {
			$thumbnail['high'] = array(
				'src'           => $post['display_url'],
				'config_width'  => $post['dimensions']['width'],
				'config_height' => $post['dimensions']['height'],
			);
		}

		return $thumbnail;
	}

	/**
	 * Returns instagram image resolution.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return string
	 */
	protected function uae_get_insta_image_size() {
		$settings = $this->get_settings_for_display();

		$size = $settings['uae_insta_img_resolution'];

		switch ( $size ) {
			case 'thumbnail':
				return 'thumbnail';
			case 'low_resolution':
				return 'low';
			case 'standard_resolution':
				return 'standard';
			default:
				return 'low';
		}
	}

	/**
	 * Returns instagram fetch data returned by api.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @param string $url api url.
	 *
	 * @return array|mixed|\WP_Error
	 */
	protected function uae_get_insta_remote( $url ) {
		$response = wp_remote_get(
			$url,
			array(
				'timeout'   => 60,
				'sslverify' => false,
			)
		);

		if ( is_wp_error( $response ) ) {
			return;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$result        = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $response_code ) {
			$message = is_array( $result ) && isset( $result['error']['message'] ) ? $result['error']['message'] : __( 'No posts found', 'uael' );

			return new \WP_Error( $response_code, $message );
		}

		if ( ! is_array( $result ) ) {
			return new \WP_Error( 'error', __( 'Data Error', 'uael' ) );
		}

		return $result;
	}

	/**
	 * Returns transient name used to store instagram cache data.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return string
	 */
	protected function uae_get_transient_key() {
		$settings                 = $this->get_settings_for_display();
		$uae_insta_caption_length = isset( $settings['uae_insta_caption_length'] ) ? $settings['uae_insta_caption_length'] : 30;
		$images_count             = $settings['uae_insta_imag_count']['size'];

		return sprintf(
			'uael_instagram_posts_count_%s_caption_%s',
			$images_count,
			$uae_insta_caption_length
		);
	}

	/**
	 * Returns instagram posts data from cache or api
	 *
	 * @since 1.36.0
	 * @access protected
	 * @param array $settings widget settings.
	 *
	 * @return array|mixed|void|\WP_Error
	 */
	protected function uae_get_insta_posts( $settings ) {
		$transient_key = md5( $this->uae_get_transient_key() );

		$data = get_transient( $transient_key );

		if ( ! empty( $data ) && 'none' !== $settings['uae_insta_cache_timeout'] && array_key_exists( 'thumbnail_resources', $data[0] ) ) {
			return $data;
		}

		$response = $this->uae_get_insta_remote( $this->uae_get_fetch_url() );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$data = $this->uae_get_insta_feed_response_data( $response );

		if ( empty( $data ) ) {
			return array();
		}

		set_transient( $transient_key, $data, strtoupper( $settings['uae_insta_cache_timeout'] ) . '_IN_SECONDS' );

		return $data;
	}

	/**
	 * Checks if the instagram api integration is enabled.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return bool
	 */
	protected function is_instagram_integration_active() {
		$uae_options      = UAEL_Helper::get_integrations_options();
		$insta_app_id     = isset( $uae_options['instagram_app_id'] ) ? $uae_options['instagram_app_id'] : '';
		$insta_app_secret = isset( $uae_options['instagram_app_secret'] ) ? $uae_options['instagram_app_secret'] : '';
		$insta_app_token  = isset( $uae_options['instagram_app_token'] ) ? $uae_options['instagram_app_token'] : '';

		if ( ! empty( $insta_app_id ) && ! empty( $insta_app_secret ) && ! empty( $insta_app_token ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Render Before After output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return void
	 */
	protected function render() {
		$settings          = $this->get_settings_for_display();
		$is_editor         = \Elementor\Plugin::instance()->editor->is_edit_mode();
		$insta_integration = $this->is_instagram_integration_active();
		$widget_id         = $this->get_id();

		if ( $is_editor && ! $insta_integration ) {
			?>
			<div class="uael-builder-msg elementor-alert elementor-alert-warning">
				<span class="elementor-alert-title"><?php echo esc_html__( 'Instagram Feed - ID ', 'uael' ); ?><?php echo esc_html( $widget_id ); ?></span>
				<span class="elementor-alert-description"><?php echo esc_html__( 'Please configure Instagram Feed API keys.', 'uael' ); ?><br>
					<?php echo esc_html__( 'Navigate to WP Dashboard -> Settings -> UAE -> Instagram Feed -> Settings link.', 'uael' ); ?>
				</span>
			</div>
			<?php
			return;
		}

		if ( ! $is_editor && ! $insta_integration ) {
			return;
		}

		$layout = '';
		if ( 'grid' === $settings['uae_insta_layout_type'] ) {
			$layout = 'grid';
		} else {
			$layout = 'masonry';
		}

		$this->add_render_attribute(
			'uael-insta-feed-wrap',
			'class',
			array(
				'uael-instagram-feed',
				'clearfix',
				'uael-instagram-feed-' . $layout,
				'uael-instagram-feed-' . $settings['uae_insta_content_visibility'],
			)
		);

		if ( ( 'grid' === $settings['uae_insta_layout_type'] || 'masonry' === $settings['uae_insta_layout_type'] ) && $settings['uae_insta_grid_cols'] ) {
			$this->add_render_attribute( 'uael-insta-feed-wrap', 'class', 'uael-instagram-feed-grid-' . $settings['uae_insta_grid_cols'] );
		}

		if ( 'yes' === $settings['uae_insta_img_grayscale'] ) {
			$this->add_render_attribute( 'uael-insta-feed-wrap', 'class', 'uael-instagram-feed-gray' );
		}

		if ( 'yes' === $settings['uae_insta_img_grayscale_hover'] ) {
			$this->add_render_attribute( 'uael-insta-feed-wrap', 'class', 'uael-instagram-feed-hover-gray' );
		}

		if ( 'masonry' !== $settings['uae_insta_layout_type'] && 'yes' === $settings['uae_insta_img_square'] ) {
			$this->add_render_attribute( 'uael-insta-feed-wrap', 'class', 'uael-if-square-images' );
		}

		$this->add_render_attribute( 'uael-insta-feed-container', 'class', 'uael-instafeed' );

		$this->add_render_attribute(
			'uael-insta-feed',
			array(
				'id'    => 'uael-instafeed-' . esc_attr( $this->get_id() ),
				'class' => 'uael-instafeed-grid',
			)
		);

		$this->add_render_attribute( 'uael-insta-feed-inner', 'class', 'uael-insta-feed-inner' );

		if ( ! empty( $settings['uae_insta_profile_url']['url'] ) ) {
			$this->add_link_attributes( 'uael-insta-profile-link', $settings['uae_insta_profile_url'] );
		}

		$this->render_api_images();

	}

	/**
	 * Render instagram posts images.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return void
	 */
	protected function render_api_images() {
		$settings  = $this->get_settings_for_display();
		$is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();

		$gallery = $this->uae_get_insta_posts( $settings );

		if ( ( empty( $gallery ) || is_wp_error( $gallery ) ) && $is_editor ) {
			$message = is_wp_error( $gallery ) ? $gallery->get_error_message() : esc_html__( 'No Posts Found', 'uael' );

			echo wp_kses_post( $message );

			return;
		}
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-insta-feed-wrap' ) ); ?>>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-insta-feed-inner' ) ); ?>>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-insta-feed-container' ) ); ?>>
					<?php $this->get_insta_profile_link(); ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-insta-feed' ) ); ?>>
						<?php
						foreach ( $gallery as $index => $item ) {
							$item_key = $this->get_repeater_setting_key( 'item', 'insta_images', $index );
							$this->add_render_attribute( $item_key, 'class', 'uael-feed-item' );
							?>
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( $item_key ) ); ?>>
								<div class="uael-feed-item-inner">
									<?php $this->render_image_thumbnail( $item, $index ); ?>
								</div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Renders profile link title icon.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return void
	 */
	protected function render_title_icon() {
		$settings = $this->get_settings_for_display();

		if ( ! isset( $settings['uae_insta_title_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default.
			$settings['uae_insta_title_icon'] = 'fa fa-instagram';
		}

		$has_icon = ! empty( $settings['uae_insta_title_icon'] );

		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['uae_insta_title_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}

		if ( ! $has_icon && ! empty( $settings['uae_insta_profile_title_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['uae_insta_profile_title_icon'] );
		$is_new   = ! isset( $settings['uae_insta_title_icon'] ) && Icons_Manager::is_migration_allowed();

		if ( $has_icon ) {
			?>
			<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'title-icon' ) ); ?>>
				<?php
				if ( $is_new || $migrated ) {
					Icons_Manager::render_icon( $settings['uae_insta_profile_title_icon'], array( 'aria-hidden' => 'true' ) );
				} elseif ( ! empty( $settings['uae_insta_title_icon'] ) ) {
					?>
					<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'i' ) ); ?>></i>
					<?php
				}
				?>
			</span>
			<?php
		}
	}

	/**
	 * Outputs profile link.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return void
	 */
	protected function get_insta_profile_link() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'title-icon', 'class', 'uael-insta-icon uael-insta-icon-' . $settings['uae_insta_profile_title_icon_position'] );

		if ( 'yes' === $settings['uae_insta_show_profile_link'] && $settings['uae_insta_profile_link_title'] ) {
			?>
			<span class="uael-instagram-feed-title-wrap">
				<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-insta-profile-link' ) ); ?>>
					<span class="uael-instagram-feed-title">
						<?php
						if ( 'before_title' === $settings['uae_insta_profile_title_icon_position'] ) {
							$this->render_title_icon();
						}

						echo esc_attr( $settings['uae_insta_profile_link_title'] );

						if ( 'after_title' === $settings['uae_insta_profile_title_icon_position'] ) {
							$this->render_title_icon();
						}
						?>
					</span>
				</a>
			</span>
			<?php
		}
	}

	/**
	 * Render instagram posts images thumbnails.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @param mixed $item posts data.
	 * @param int   $index index of each post in posts data.
	 *
	 * @return void
	 */
	protected function render_image_thumbnail( $item, $index ) {
		$settings        = $this->get_settings_for_display();
		$thumbnail_url   = $this->get_insta_image_url( $item, $this->uae_get_insta_image_size() );
		$thumbnail_alt   = isset( $item['caption'] ) ? $item['caption'] : '';
		$thumbnail_title = isset( $item['caption'] ) ? $item['caption'] : '';
		$image_key       = $this->get_repeater_setting_key( 'image', 'insta', $index );
		$link_key        = $this->get_repeater_setting_key( 'link', 'image', $index );
		$item_link       = '';

		$this->add_render_attribute( $image_key, 'src', $thumbnail_url );

		if ( '' !== $thumbnail_alt ) {
			$this->add_render_attribute( $image_key, 'alt', $thumbnail_alt );
		}

		if ( '' !== $thumbnail_title ) {
			$this->add_render_attribute( $image_key, 'title', $thumbnail_title );
		}

		if ( 'yes' === $settings['uae_insta_img_lightbox'] ) {

			$item_link = $this->get_insta_image_url( $item, 'high' );

			$this->add_render_attribute(
				$link_key,
				array(
					'data-elementor-open-lightbox'      => 'yes',
					'data-elementor-lightbox-title'     => $thumbnail_alt,
					'data-elementor-lightbox-slideshow' => 'uael-ig-' . $this->get_id(),
				)
			);

		} elseif ( 'yes' === $settings['uae_insta_img_link'] ) {
			$item_link = $item['link'];

			$this->add_render_attribute( $link_key, 'target', '_blank' );
		}

		$this->add_render_attribute( $link_key, 'href', $item_link );

		$image_html  = '<div class="uael-if-img">';
		$image_html .= '<div class="uael-overlay-container uael-media-overlay">';
		if ( 'yes' === $settings['uae_insta_show_caption'] ) {
			$image_html .= '<div class="uael-insta-caption">' . $thumbnail_alt . '</div>';
		}
		$image_html .= '</div>';
		$image_html .= '<img ' . $this->get_render_attribute_string( $image_key ) . '/>';
		$image_html .= '</div>';

		if ( 'yes' === $settings['uae_insta_img_lightbox'] || 'yes' === $settings['uae_insta_img_link'] ) {
			$image_html = '<a ' . $this->get_render_attribute_string( $link_key ) . '>' . $image_html . '</a>';
		}

		echo wp_kses_post( $image_html );
	}

	/**
	 * Returns posts image link based on image resolution.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @param mixed  $item posts data.
	 * @param string $size posts image size.
	 *
	 * @return string
	 */
	protected function get_insta_image_url( $item, $size = 'high' ) {
		$thumbnail = isset( $item['thumbnail'] ) ? $item['thumbnail'] : '';
		$image_url = '';

		if ( ! empty( $thumbnail[ $size ] ) ) {
			$image_url = $thumbnail[ $size ]['src'];
		} else {
			$image_url = isset( $item['image'] ) ? $item['image'] : '';
		}

		return $image_url;
	}

	/**
	 * Render Before After Slider widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.36.0
	 * @access protected
	 * @return void
	 */
	protected function content_template() {}
}
