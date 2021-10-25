<?php
/**
 * Class: Premium_Facebook_Feed
 * Name: Facebook Feed
 * Slug: premium-facebook-feed
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Facebook_Feed
 */
class Premium_Facebook_Feed extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-facebook-feed';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return sprintf( '%1$s %2$s', Helper_Functions::get_prefix(), __( 'Facebook Feed', 'premium-addons-pro' ) );
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
		return 'pa-pro-facebook-feed';
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'premium-elements' );
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
		return array( 'fb', 'profile', 'account', 'post', 'page', 'social' );
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS script handles.
	 */
	public function get_style_depends() {
		return array(
			'premium-addons',
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
		return array(
			'social-dot',
			'jquery-socialfeed',
			'isotope-js',
			'pa-slick',
			'imagesloaded',
			'premium-pro',
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
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Register Facebook Feed controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'access_credentials_section',
			array(
				'label' => __( 'Access Credentials', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'type',
			array(
				'label'   => __( 'Source', 'premium-addons-pro' ),
				'type'    => Controls_Manager::HIDDEN,
				'options' => array(
					'user' => __( 'User', 'premium-addons-pro' ),
					'page' => __( 'Page', 'premium-addons-pro' ),
				),
				'default' => 'page',
			)
		);

		$this->add_control(
			'facebook_login',
			array(
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<form onsubmit="connectFbInit(this);" action="javascript:void(0);" data-type="feed"><input type="submit" value="Log in with Facebook" class="elementor-button" style="background-color: #3b5998; color: #fff;"></form>',
				'label_block' => true,
				'condition'   => array(
					'type' => 'page',
				),
			)
		);

		$this->add_control(
			'login_notice',
			array(
				'raw'             => '<strong>' . __( 'Please note!', 'premium-addons-pro' ) . '</strong> ' . __( 'You need to select only one page per widget.', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'render_type'     => 'ui',
				'condition'       => array(
					'type' => 'page',
				),
			)
		);

		$this->add_control(
			'account_id',
			array(
				'label'       => __( 'ID', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'access_token',
			array(
				'label'   => __( 'Access Token', 'premium-addons-pro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'page_settings',
			array(
				'label' => __( 'Layout', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'layout_style',
			array(
				'label'       => __( 'Style', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Choose the layout style for the posts', 'premium-addons-pro' ),
				'options'     => array(
					'list'    => __( 'List', 'premium-addons-pro' ),
					'masonry' => __( 'Grid', 'premium-addons-pro' ),
				),
				'default'     => 'masonry',
			)
		);

		$this->add_control(
			'equal_height_switcher',
			array(
				'label'     => __( 'Equal Height', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'column_number!' => '100%',
					'layout_style'   => 'masonry',
				),

			)
		);

		$this->add_responsive_control(
			'column_number',
			array(
				'label'           => __( 'Posts/Row', 'premium-addons-pro' ),
				'type'            => Controls_Manager::SELECT,
				'options'         => array(
					'100%'    => __( '1 Column', 'premium-addons-pro' ),
					'50%'     => __( '2 Columns', 'premium-addons-pro' ),
					'33.33%'  => __( '3 Columns', 'premium-addons-pro' ),
					'25%'     => __( '4 Columns', 'premium-addons-pro' ),
					'20%'     => __( '5 Columns', 'premium-addons-pro' ),
					'16.667%' => __( '6 Columns', 'premium-addons-pro' ),
				),
				'desktop_default' => '33.33%',
				'tablet_default'  => '50%',
				'mobile_default'  => '100%',
				'render_type'     => 'template',
				'condition'       => array(
					'layout_style' => 'masonry',
				),
				'selectors'       => array(
					'{{WRAPPER}} .premium-social-feed-element-wrap' => 'width: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'   => __( 'Direction', 'premium-addons-pro' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'ltr' => array(
						'title' => __( 'Left to Right', 'premium-addons-pro' ),
						'icon'  => 'fa fa-chevron-circle-right',
					),
					'rtl' => array(
						'title' => __( 'Right to Left', 'premium-addons-pro' ),
						'icon'  => 'fa fa-chevron-circle-left',
					),
				),
				'default' => 'ltr',
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Content Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-right',
					),
					'justify' => array(
						'title' => __( 'Justify', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-text, {{WRAPPER}} .premium-feed-element-read-more' => 'text-align: {{VALUE}}',
				),
				'default'   => 'left',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'layout_settings',
			array(
				'label' => __( 'Advanced Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'post_number',
			array(
				'label'       => __( 'Posts/Account', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => false,
				'description' => __( 'How many posts will be shown for each account', 'premium-addons-pro' ),
				'default'     => 5,
			)
		);

		$this->add_control(
			'content_length',
			array(
				'label'   => __( 'Post Length', 'premium-addons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 200,
			)
		);

		$this->add_control(
			'posts_media',
			array(
				'label'     => __( 'Show Post Media', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => 'Show',
				'label_off' => 'Hide',
				'default'   => 'yes',
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'      => __( 'Post Media Height', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 500,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'condition'  => array(
					'posts_media' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-social-feed-element img.attachment' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'image_fit',
			array(
				'label'     => __( 'Image Fit', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'cover'   => __( 'Cover', 'premium-addons-for-elementor' ),
					'fill'    => __( 'Fill', 'premium-addons-for-elementor' ),
					'contain' => __( 'Contain', 'premium-addons-for-elementor' ),
				),
				'default'   => 'fill',
				'selectors' => array(
					'{{WRAPPER}} .premium-social-feed-element img.attachment' => 'object-fit: {{VALUE}}',
				),
				'separator' => 'after',
				'condition' => array(
					'posts_media' => 'yes',
				),
			)
		);

		$this->add_control(
			'admin_posts',
			array(
				'label'        => __( 'Show Admin Posts Only', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => __( 'Enable this to show only the posts that are posted by page admins', 'premium-addons-pro' ),
				'return_value' => 'true',
			)
		);

		$this->add_control(
			'show_avatar',
			array(
				'label'     => __( 'Show Avatar', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'block' => __( 'Show', 'premium-addons-pro' ),
					'none'  => __( 'Hide', 'premium-addons-pro' ),
				),
				'default'   => 'block',
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-author-img'   => 'display: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'show_profile_name',
			array(
				'label'     => __( 'Show Profile Name', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'block' => __( 'Show', 'premium-addons-pro' ),
					'none'  => __( 'Hide', 'premium-addons-pro' ),
				),
				'default'   => 'block',
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-author'   => 'display: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'show_date',
			array(
				'label'     => __( 'Show Date', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'block' => __( 'Show', 'premium-addons-pro' ),
					'none'  => __( 'Hide', 'premium-addons-pro' ),
				),
				'default'   => 'block',
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-date'   => 'display: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'show_content',
			array(
				'label'     => __( 'Show Feed Content', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'block' => __( 'Show', 'premium-addons-pro' ),
					'none'  => __( 'Hide', 'premium-addons-pro' ),
				),
				'default'   => 'block',
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-text'   => 'display: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'read',
			array(
				'label'     => __( 'Show Read More', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'inline-block' => __( 'Show', 'premium-addons-pro' ),
					'none'         => __( 'Hide', 'premium-addons-pro' ),
				),
				'default'   => 'inline-block',
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-read-more'   => 'display: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'read_text',
			array(
				'label'     => __( 'Read More Text', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'default'   => 'Read More →',
				'condition' => array(
					'read' => 'inline-block',
				),
			)
		);

		$this->add_control(
			'show_icon',
			array(
				'label'     => __( 'Show Facebook Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'inline-block' => __( 'Show', 'premium-addons-pro' ),
					'none'         => __( 'Hide', 'premium-addons-pro' ),
				),
				'default'   => 'inline-block',
				'selectors' => array(
					'{{WRAPPER}} .premium-social-icon' => 'display: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'carousel',
			array(
				'label' => __( 'Carousel', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'feed_carousel',
			array(
				'label' => __( 'Carousel', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'carousel_play',
			array(
				'label'     => __( 'Auto Play', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'feed_carousel' => 'yes',
				),
			)
		);

		$this->add_control(
			'carousel_autoplay_speed',
			array(
				'label'       => __( 'Autoplay Speed', 'premium-addons-pro' ),
				'description' => __( 'Autoplay Speed means at which time the next slide should come. Set a value in milliseconds (ms)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 5000,
				'condition'   => array(
					'feed_carousel' => 'yes',
					'carousel_play' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'carousel_arrows_pos',
			array(
				'label'      => __( 'Arrows Position', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -10,
						'max' => 10,
					),
				),
				'condition'  => array(
					'feed_carousel' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-facebook-feed-wrapper a.carousel-arrow.carousel-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-facebook-feed-wrapper a.carousel-arrow.carousel-prev' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-pro' ),
			)
		);

		$doc1_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/facebook-feed-widget-tutorial/', 'editor-page', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc1_url, __( 'Getting started »', 'premium-addons-for-elementor' ) ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'post_box_style',
			array(
				'label' => __( 'Post Box', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'post_box' );

		$this->start_controls_tab(
			'post_box_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'post_box_background',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-social-feed-element' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'post_box_border',
				'selector' => '{{WRAPPER}} .premium-social-feed-element',
			)
		);

		$this->add_control(
			'post_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-social-feed-element' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'post_box_shadow',
				'selector' => '{{WRAPPER}} .premium-social-feed-element',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'post_box_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'post_box_background_hover',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-social-feed-element:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'post_box_border_hover',
				'selector' => '{{WRAPPER}} .premium-social-feed-element:hover',
			)
		);

		$this->add_control(
			'post_box_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-social-feed-element:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'post_box_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-social-feed-element:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'post_box_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .list-layout .premium-social-feed-element' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .premium-social-feed-element-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'post_box_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-social-feed-element' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_style',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'facebook_feed_content_typography',
				'scheme'    => Typography::TYPOGRAPHY_1,
				'selector'  => '{{WRAPPER}} .premium-feed-element-text',
				'condition' => array(
					'show_content' => 'block',
				),
			)
		);

		$this->add_control(
			'content_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-text' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'show_content' => 'block',
				),
			)
		);

		$this->add_control(
			'links_color',
			array(
				'label'     => __( 'Links Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-text a' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'show_content' => 'block',
				),
			)
		);

		$this->add_control(
			'links_hover_color',
			array(
				'label'     => __( 'Links Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-text a:hover' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'show_content' => 'block',
				),
			)
		);

		$this->add_responsive_control(
			'facebook_feed_content_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-feed-element-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'show_content' => 'block',
				),
			)
		);

		$this->add_control(
			'facebook_feed_read_more_heading',
			array(
				'label'     => __( 'Read More', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'read' => 'inline-block',
				),
			)
		);

		$this->add_control(
			'read_more_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-read-more' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'read' => 'inline-block',
				),
			)
		);

		$this->add_control(
			'read_more_color_hover',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_3,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-read-more:hover' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'read' => 'inline-block',
				),
			)
		);

		$this->add_control(
			'read_more_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-read-more' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'read' => 'inline-block',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'facebook_feed_read_more_typography',
				'scheme'    => Typography::TYPOGRAPHY_1,
				'selector'  => '{{WRAPPER}} .premium-feed-element-read-more',
				'condition' => array(
					'read' => 'inline-block',
				),
			)
		);

		$this->add_responsive_control(
			'read_more_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-feed-read-more-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'read' => 'inline-block',
				),
			)
		);

		$this->add_responsive_control(
			'read_more_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-feed-element-read-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'read' => 'inline-block',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'avatar_style',
			array(
				'label'     => __( 'Avatar', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_avatar' => 'block',
				),
			)
		);

		$this->add_responsive_control(
			'avatar_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-social-feed-element .media-object ' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'avatar_border',
				'selector' => '{{WRAPPER}} .premium-feed-element-author-img img',
			)
		);

		$this->add_control(
			'avatar_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-feed-element-author-img img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'avatar_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-feed-element-author-img img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'icon_style',
			array(
				'label'     => __( 'Facebook Icon', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_icon' => 'inline-block',
				),
			)
		);

		$this->add_control(
			'facebook_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-social-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'facebook_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-social-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-social-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style',
			array(
				'label'     => __( 'Author', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_profile_name' => 'block',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-author a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-author:hover a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-feed-element-author a',
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-feed-element-author' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'date_style',
			array(
				'label'     => __( 'Date', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_date' => 'block',
				),
			)
		);

		$this->add_control(
			'date_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-date a' => 'color: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'date_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-date:hover a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'date_typography',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-feed-element-date a',
			)
		);

		$this->add_responsive_control(
			'date_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-feed-element-date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'carousel_style',
			array(
				'label'     => __( 'Carousel', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'feed_carousel' => 'yes',
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => __( 'Arrrow Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-facebook-feed-wrapper .slick-arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-facebook-feed-wrapper .slick-arrow i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'arrow_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-facebook-feed-wrapper .slick-arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-facebook-feed-wrapper .slick-arrow' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'arrow_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-facebook-feed-wrapper .slick-arrow' => 'padding: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'general_style',
			array(
				'label' => __( 'Container', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'container_background',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-facebook-feed-wrapper' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'container_box_border',
				'selector' => '{{WRAPPER}} .premium-facebook-feed-wrapper',
			)
		);

		$this->add_control(
			'container_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-facebook-feed-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'container_box_shadow',
				'selector' => '{{WRAPPER}} .premium-facebook-feed-wrapper',
			)
		);

		$this->add_responsive_control(
			'container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-facebook-feed-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-facebook-feed-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Facebook Feed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( empty( $settings['access_token'] ) ) { ?>
			<div class="premium-error-notice">
				<?php echo esc_html( __( 'Please fill the required fields: User/Page ID & Access Token', 'premium-addons-pro' ) ); ?>
			</div>
			<?php
			return;
		} elseif ( 'Invalid License Key' === $settings['access_token'] ) {
			?>
			<div class="premium-error-notice">
				<?php echo esc_html( __( 'Please activate your license to get the access token', 'premium-addons-pro' ) ); ?>
			</div>
			<?php
			return;
		}

		$layout_class = 'list' === $settings['layout_style'] ? 'list-layout' : 'grid-layout';

		$template = 'list' === $settings['layout_style'] ? 'list-template.php' : 'grid-template.php';

		$direction = $settings['direction'];

		$account_id = preg_replace( '/[!@]/', '', $settings['account_id'] );

		$account_id = ( 'user' === $settings['type'] ? '@' : '!' ) . $account_id;

		if ( 'yes' === $settings['feed_carousel'] ) {

			$this->add_render_attribute(
				'facebook',
				array(
					'data-carousel' => $settings['feed_carousel'],
					'data-play'     => $settings['carousel_play'],
					'data-speed'    => $settings['carousel_autoplay_speed'],
					'data-rtl'      => is_rtl(),
				)
			);

		}

		$facebook_settings = array(
			'accounts'   => esc_html( $account_id ),
			'limit'      => $settings['post_number'],
			'accessTok'  => esc_html( $settings['access_token'] ),
			'length'     => $settings['content_length'],
			'showMedia'  => $settings['posts_media'],
			'layout'     => $layout_class,
			'readMore'   => esc_html( $settings['read_text'] ),
			'template'   => plugins_url( '/templates/', __FILE__ ) . $template,
			'adminPosts' => $settings['admin_posts'],
		);

		if ( 'yes' === $settings['equal_height_switcher'] ) {

			$this->add_render_attribute( 'facebook-inner', 'class', 'premium-social-feed-even' );

			$facebook_settings['even'] = true;

		}

		$this->add_render_attribute(
			'facebook',
			array(
				'class'         => array(
					'premium-facebook-feed-wrapper',
					$direction,
				),
				'data-settings' => wp_json_encode( $facebook_settings ),
			)
		);

		$this->add_render_attribute(
			'facebook-inner',
			array(
				'id'    => 'premium-social-feed-container-' . $this->get_id(),
				'class' => array(
					'premium-social-feed-container',
					$layout_class,
				),
			)
		);

		$feed_number = 1;

		if ( 'masonry' === $settings['layout_style'] ) {
			$feed_number = intval( 100 / substr( $settings['column_number'], 0, strpos( $settings['column_number'], '%' ) ) );
		}

		$this->add_render_attribute( 'facebook', 'data-col', $feed_number );

		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'facebook' ) ); ?>>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'facebook-inner' ) ); ?>></div>
			<div class="premium-loading-feed">
				<div class="premium-loader"></div>
			</div>
		</div>
		<?php

	}
}
