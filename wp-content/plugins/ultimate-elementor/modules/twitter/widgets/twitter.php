<?php
/**
 * UAEL Twitter Feed.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Twitter\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Twitter_Feed.
 */
class Twitter extends Common_Widget {

	/**
	 * Retrieve Twitter Feed Widget name.
	 *
	 * @since 1.36.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Twitter' );
	}

	/**
	 * Retrieve Twitter Feed Widget title.
	 *
	 * @since 1.36.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Twitter' );
	}

	/**
	 * Retrieve Twitter Feed Widget icon.
	 *
	 * @since 1.36.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Twitter' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.36.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Twitter' );
	}

	/**
	 * Load the related styles.
	 *
	 * @since 1.36.0
	 *
	 * @access public
	 *
	 * @return array Font styles.
	 */
	public function get_style_depends() {
		if ( Icons_Manager::is_migration_allowed() ) {
			return array(
				'elementor-icons-fa-brands',
			);
		}
		return array();
	}

	/**
	 * Retrieve the list of scripts the image carousel widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.36.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array(
			'uael-isotope',
			'imagesloaded',
			'uael-slick',
			'uael-element-resize',
			'uael-frontend-script',
			'uael-justified',
		);
	}

	/**
	 * Register Twitter Feed controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->register_account_controls();
		$this->register_layout_controls();
		$this->register_timeline_control();
		$this->register_carousel_control();
		$this->register_content_tab();
		$this->register_helpful_information();

		$this->register_spacing_style_tab();
		$this->register_timeline_style_control();
		$this->register_Carousel_style_tab();
		$this->register_content_style_tab();
		$this->register_card_style_tab();
		$this->register_image_style_tab();

	}

	/**
	 * Get twitter api
	 *
	 * @since 1.36.0
	 * @var object $integration_settings
	 */
	public static $integration_settings;

	/**
	 * Register Twitter Feed Account Controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function register_account_controls() {

		$this->start_controls_section(
			'uael_twitter_feed_account',
			array(
				'label' => __( 'Account', 'uael' ),
			)
		);

		$this->add_control(
			'uael_twitter_feed_search_by',
			array(
				'label'   => __( 'Search By', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'username',
				'options' => array(
					'username' => __( 'Twitter Handle', 'uael' ),
					'hashtag'  => __( 'Hashtag', 'uael' ),
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_username',
			array(
				'label'       => __( 'Twitter Handle', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Enter your Twitter handle WITHOUT @ sign.', 'uael' ),
				'condition'   => array(
					'uael_twitter_feed_search_by' => 'username',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_hashtag_name',
			array(
				'label'       => __( 'Hashtag', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Enter your Twitter hashtag WITHOUT # sign.', 'uael' ),
				'condition'   => array(
					'uael_twitter_feed_search_by' => 'hashtag',
				),
			)
		);

		if ( ! isset( self::$integration_settings ) ) {
			self::$integration_settings = UAEL_Helper::get_integrations_options();
		}

		if ( empty( self::$integration_settings['uael_twitter_feed_consumer_key'] ) || empty( self::$integration_settings['uael_twitter_feed_consumer_secret'] ) ) {

			$widget_list = UAEL_Helper::get_widget_list();

			$admin_link = $widget_list['Twitter']['setting_url'];

			$this->add_control(
				'twitter_err_msg',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'To display Twitter Feed, you need to configure Twitter Consumer keys. Please configure keys from <a href="%s" target="_blank" rel="noopener">here</a>.', 'uael' ), $admin_link ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				)
			);
		}

		$this->add_control(
			'uael_twitter_feed_data_cache_limit',
			array(
				'label'       => __( 'Cache Time (Hours)', 'uael' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 500,
				'default'     => 1,
				'description' => __( 'Feed will be cached for the selected time duration and refreshed after that.', 'uael' ),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register Twitter Feed General Controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function register_layout_controls() {

		$this->start_controls_section(
			'uael_twitter_feed_layout_tab',
			array(
				'label' => __( 'Layout', 'uael' ),
			)
		);

		$this->add_control(
			'uael_twitter_feed_layout',
			array(
				'label'   => esc_html__( 'Content Layout', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'grid'     => esc_html__( 'Grid', 'uael' ),
					'list'     => esc_html__( 'List', 'uael' ),
					'timeline' => esc_html__( 'Timeline', 'uael' ),
					'carousel' => esc_html__( 'Carousel', 'uael' ),
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_sort_by',
			array(
				'label'   => __( 'Sort By', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'recent-posts',
				'options' => array(
					'recent-posts'   => __( 'Newest First', 'uael' ),
					'old-posts'      => __( 'Oldest First', 'uael' ),
					'favorite_count' => __( 'Most Liked', 'uael' ),
					'retweet_count'  => __( 'Most Retweeted', 'uael' ),
				),
			)
		);

		$this->add_responsive_control(
			'uael_twitter_feed_col',
			array(
				'label'              => __( 'Columns', 'uael' ),
				'type'               => Controls_Manager::SELECT,
				'tablet_default'     => 'col-2',
				'mobile_default'     => 'col-1',
				'options'            => array(
					'col-1' => '1',
					'col-2' => '2',
					'col-3' => '3',
					'col-4' => '4',
				),
				'default'            => 'col-3',
				'frontend_available' => true,
				'condition'          => array(
					'uael_twitter_feed_layout' => array( 'grid' ),
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_post_limit',
			array(
				'label'       => esc_html__( 'Post Limit', 'uael' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => false,
				'default'     => 3,
			)
		);

		$this->add_control(
			'uael_twitter_feed_show_media',
			array(
				'label'        => esc_html__( 'Show Media ', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'yes', 'uael' ),
				'label_off'    => __( 'no', 'uael' ),
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_responsive_control(
			'uael_twitter_feed_media_size',
			array(
				'label'              => __( 'Media Size', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( '%', 'px', 'em' ),
				'range'              => array(
					'px' => array(
						'min' => 1,
						'max' => 300,
					),
				),
				'default'            => array(
					'size' => 100,
					'unit' => '%',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-twitter-feed__title img' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-twitter-feed__title video' => 'width: {{SIZE}}{{UNIT}};',
				),
				'frontend_available' => true,
				'condition'          => array(
					'uael_twitter_feed_show_media' => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_content_length',
			array(
				'label'       => esc_html__( 'Content Length', 'uael' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => false,
				'min'         => 1,
				'max'         => 1000,
				'default'     => 100,
			)
		);

		$this->add_control(
			'uael_twitter_feed_equal_height',
			array(
				'label'        => __( 'Equal Height', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'label_off'    => __( 'No', 'uael' ),
				'label_on'     => __( 'Yes', 'uael' ),
				'prefix_class' => 'uael-equal__height-',
				'description'  => __( 'Enable this to display all tweets with same height.', 'uael' ),
				'condition'    => array(
					'uael_twitter_feed_layout' => array( 'grid' ),
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register Twitter Feed General Controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function register_content_tab() {

		$this->start_controls_section(
			'uael_twitter_feed_conent',
			array(
				'label' => __( 'Content', 'uael' ),
			)
		);

		$this->add_control(
			'uael_twitter_feed_profile',
			array(
				'label'        => esc_html__( 'Profile', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'uael_twitter_feed_profile_style',
			array(
				'label'     => __( 'Profile Style', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'circle' => 'Circle',
					'square' => 'Square',
				),
				'default'   => 'circle',
				'condition' => array(
					'uael_twitter_feed_profile' => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_show_name',
			array(
				'label'        => esc_html__( 'Twitter Name', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show ', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'uael_twitter_feed_show_username',
			array(
				'label'        => esc_html__( 'Twitter Handle', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show ', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'uael_twitter_feed_twitter_logo',
			array(
				'label'        => esc_html__( 'Twitter Logo', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'uael_twitter_feed_read_more',
			array(
				'label'        => esc_html__( 'Read More', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'uael_twitter_feed_read_more_text',
			array(
				'label'       => esc_html__( 'Read More Text', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => __( 'Read More »', 'uael' ),
				'condition'   => array(
					'uael_twitter_feed_read_more' => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_link_target',
			array(
				'label'     => __( 'Link Target', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'_self'  => __( 'Open in same window', 'uael' ),
					'_blank' => __( 'Open in new window', 'uael' ),
				),
				'default'   => '_blank',
				'condition' => array(
					'uael_twitter_feed_read_more' => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_tweet_content',
			array(
				'label'        => esc_html__( 'Feed Content', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'uael_twitter_feed_date',
			array(
				'label'        => esc_html__( 'Date', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'uael_twitter_feed_tweet_info',
			array(
				'label'        => esc_html__( 'Tweet Information', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register Twitter Feed Card Controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function register_card_style_tab() {
		$this->start_controls_section(
			'uael_twitter_feed_card_style',
			array(
				'label' => esc_html__( 'Card', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'uael_twitter_feed_card_padding',
			array(
				'label'              => __( 'Padding', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', 'em', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .uael-twitter-feed .uael-twitter-feed-item .uael-twitter-feed-item-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'            => array(
					'top'      => '22',
					'right'    => '22',
					'bottom'   => '22',
					'left'     => '22',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'frontend_available' => true,
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'     => 'uael_twitter_feed_card_gradient_bg',
				'label'    => __( 'Background', 'uael' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .uael-twitter-feed-item-inner',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'uael_twitter_feed_card_border',
				'label'          => __( 'Border', 'uael' ),
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
					'width'  => array(
						'default' => array(
							'top'    => '1',
							'right'  => '1',
							'bottom' => '1',
							'left'   => '1',
						),
					),
					'color'  => array(
						'default' => '#ededed',
					),
				),
				'selector'       => '{{WRAPPER}} .uael-twitter-feed .uael-twitter-feed-item .uael-twitter-feed-item-inner',
			)
		);

		$this->add_control(
			'uael_twitter_feed_card_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => '2',
					'bottom' => '2',
					'right'  => '2',
					'left'   => '2',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-twitter-feed .uael-twitter-feed-item .uael-twitter-feed-item-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'(mobile){{WRAPPER}} .uael-twitter-feed .uael-twitter-feed-item .uael-twitter-feed-item-inner' => 'border-radius: 0px;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'uael_twitter_feed_card_shadow',
				'selector' => '{{WRAPPER}} .uael-twitter-feed .uael-twitter-feed-item .uael-twitter-feed-item-inner',
			)
		);

		$this->add_control(
			'uael_twitter_feed_content_alignment',
			array(
				'label'       => __( 'Content Alignment', 'uael' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => array(
					'left'   => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'     => 'left',
				'selectors'   => array(
					'{{WRAPPER}} .uael-twitter-feed-item,{{WRAPPER}}.uael-twitter-feed_inner-content,
					{{WRAPPER}}.uael-twitter-feed__title' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Twitter Feed Carousel Controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function register_Carousel_style_tab() {

		$this->start_controls_section(
			'uael_twitter_feed_carousel',
			array(
				'label'     => __( 'Carousel', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'uael_twitter_feed_layout' => 'carousel',
					'navigation!'              => array( 'none' ),
				),
			)
		);

		$this->add_control(
			'heading_style_arrows',
			array(
				'label'     => __( 'Arrows', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'navigation'               => array( 'arrows', 'both' ),
					'uael_twitter_feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'arrows_position',
			array(
				'label'        => __( 'Arrows Position', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'outside',
				'options'      => array(
					'inside'  => __( 'Inside', 'uael' ),
					'outside' => __( 'Outside', 'uael' ),
				),
				'prefix_class' => 'uael-img-carousel-arrow-',
				'condition'    => array(
					'navigation'               => array( 'arrows', 'both' ),
					'uael_twitter_feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'arrows_size',
			array(
				'label'     => __( 'Arrows Size', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 20,
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .slick-slider .slick-prev:before, {{WRAPPER}} .slick-slider .slick-next:before' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'navigation'               => array( 'arrows', 'both' ),
					'uael_twitter_feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'arrows_color',
			array(
				'label'     => __( 'Arrows Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slick-slider .slick-prev:before, {{WRAPPER}} .slick-slider .slick-next:before' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'navigation'               => array( 'arrows', 'both' ),
					'uael_twitter_feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'heading_style_dots',
			array(
				'label'     => __( 'Dots', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'navigation'               => array( 'dots', 'both' ),
					'uael_twitter_feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'dots_size',
			array(
				'label'     => __( 'Dots Size', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 5,
						'max' => 15,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .slick-dots li button:before' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'navigation'               => array( 'dots', 'both' ),
					'uael_twitter_feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'dots_color',
			array(
				'label'     => __( 'Dots Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'navigation'               => array( 'dots', 'both' ),
					'uael_twitter_feed_layout' => 'carousel',
				),
			)
		);

		$this->end_controls_section();
	}


	/**
	 * Register Twitter Feed Logo Controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function register_image_style_tab() {
		$this->start_controls_section(
			'uael_twitter_feed_image',
			array(
				'label'      => __( 'Image', 'uael' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'uael_twitter_feed_profile',
							'operator' => '=>',
							'value'    => 'yes',
						),
						array(
							'name'     => 'uael_twitter_feed_twitter_logo',
							'operator' => '=>',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'uael_twitter_feed_profile_size',
			array(
				'label'              => __( 'Profile Image Size', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px', 'em', '%' ),
				'range'              => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'            => array(
					'size' => 46,
					'unit' => 'px',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-twitter-feed-item-avatar img' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'          => array(
					'uael_twitter_feed_profile' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'uael_twitter_feed_twitter_logo_size',
			array(
				'label'     => __( 'Twitter Logo', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'uael_twitter_feed_twitter_logo' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'uael_twitter_feed_twitter_size',
			array(
				'label'              => __( 'Size', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px', 'em', '%' ),
				'range'              => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'            => array(
					'size' => 14,
					'unit' => 'px',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-twitter-feed-twitter-logo .fa-twitter' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
				'condition'          => array(
					'uael_twitter_feed_twitter_logo' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'uael_twitter_feed_logo_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7f7f7f',
				'selectors' => array(
					'{{WRAPPER}} i.fab.fa-twitter' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'uael_twitter_feed_twitter_logo' => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_logo_hover_color',
			array(
				'label'     => __( 'Hover Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1da1f2',
				'selectors' => array(
					'{{WRAPPER}} i.fab.fa-twitter:hover' => 'color: {{VALUE}}; opacity:1;',
				),
				'condition' => array(
					'uael_twitter_feed_twitter_logo' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Twitter Feed Spacing Controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function register_content_style_tab() {
		$this->start_controls_section(
			'uael_twitter_feed_content',
			array(
				'label' => __( 'Content', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'uael_twitter_feed_name',
			array(
				'label'     => __( 'Twitter Name', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'uael_twitter_feed_show_name' => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_name_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-twitter-feed .uael-twitter-feed-card-header-icon .uael-twitter-feed-name' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'uael_twitter_feed_show_name' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'uael_twitter_feed_name_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'condition' => array(
					'uael_twitter_feed_show_name' => 'yes',
				),
				'selector'  => '{{WRAPPER}} .uael-twitter-feed-card-header-icon .uael-twitter-feed-name-username .uael-twitter-feed-name',
			)
		);

		$this->add_control(
			'uael_twitter_feed_handler',
			array(
				'label'     => __( 'Twitter Handle', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'uael_twitter_feed_show_username' => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_handler_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-twitter-feed .uael-twitter-feed-card-header-icon .uael-twitter-feed-username' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'uael_twitter_feed_show_username' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'uael_twitter_feed_handler_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'condition' => array(
					'uael_twitter_feed_show_username' => 'yes',
				),
				'selector'  => '{{WRAPPER}} .uael-twitter-feed-card-header-icon .uael-twitter-feed-name-username .uael-twitter-feed-username',
			)
		);

		$this->add_control(
			'uael_twitter_feed_content_data',
			array(
				'label'     => __( 'Feed Content', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'uael_twitter_feed_tweet_content' => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_content_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-twitter-feed__title p' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'uael_twitter_feed_tweet_content' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'uael_twitter_feed_content_typo',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'selector'  => '{{WRAPPER}} .uael-twitter-feed__title p',
				'condition' => array(
					'uael_twitter_feed_tweet_content' => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_content_read_more',
			array(
				'label'     => __( 'Read More', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'uael_twitter_feed_read_more' => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_content_read_more_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-twitter-feed__title span a' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'uael_twitter_feed_read_more' => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_content_hover_color',
			array(
				'label'     => __( 'Hover Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-twitter-feed__title span a:hover' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'uael_twitter_feed_read_more' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'uael_twitter_feed_content_read_more_typo',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'condition' => array(
					'uael_twitter_feed_read_more' => 'yes',
				),
				'selector'  => '{{WRAPPER}} .uael-twitter-feed__title span a',
			)
		);

		$this->add_control(
			'uael_twitter_feed_content_date_info',
			array(
				'label'      => __( 'Date and Information', 'uael' ),
				'type'       => Controls_Manager::HEADING,
				'separator'  => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'uael_twitter_feed_date',
							'operator' => '=>',
							'value'    => 'yes',
						),
						array(
							'name'     => 'uael_twitter_feed_tweet_info',
							'operator' => '=>',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_content_date_info_color',
			array(
				'label'      => __( 'Color', 'uael' ),
				'type'       => Controls_Manager::COLOR,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'uael_twitter_feed_date',
							'operator' => '=>',
							'value'    => 'yes',
						),
						array(
							'name'     => 'uael_twitter_feed_tweet_info',
							'operator' => '=>',
							'value'    => 'yes',
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-twitter-feed_inner-content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'       => 'uael_twitter_feed_content_date_typo',
				'global'     => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'uael_twitter_feed_date',
							'operator' => '=>',
							'value'    => 'yes',
						),
						array(
							'name'     => 'uael_twitter_feed_tweet_info',
							'operator' => '=>',
							'value'    => 'yes',
						),
					),
				),
				'selector'   => '{{WRAPPER}} .uael-twitter-feed_inner-content',
			)
		);

		$this->add_control(
			'uael_twitter_feed_content_date_info_count',
			array(
				'label'     => __( 'Count Information', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'uael_twitter_feed_tweet_info' => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_content_date_info_count_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-twitter-feed-tweet-info b' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'uael_twitter_feed_tweet_info' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'uael_twitter_feed_content_date_typo_count',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'selector'  => '{{WRAPPER}} .uael-twitter-feed-tweet-info b',
				'condition' => array(
					'uael_twitter_feed_tweet_info' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Twitter Feed Spacing Controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function register_spacing_style_tab() {

		$this->start_controls_section(
			'uael_twitter_feed_spacing',
			array(
				'label' => esc_html__( 'Spacing', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'uael_twitter_feed_spacing_cards',
			array(
				'label' => __( 'Spacing between cards', 'uael' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'uael_twitter_feed_column_gap',
			array(
				'label'              => __( 'Columns Gap', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 20,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-twitter-feed .uael-twitter-feed-item .uael-twitter-feed-item-inner' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 ); margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .uael-twitter-feed' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				),
				'condition'          => array(
					'uael_twitter_feed_layout' => array( 'grid', 'carousel' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'uael_twitter_feed_rows_gap',
			array(
				'label'              => __( 'Rows Gap', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 20,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-twitter-feed .uael-twitter-feed-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',

				),
				'condition'          => array(
					'uael_twitter_feed_layout!' => array( 'carousel' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'hr',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'uael_twitter_feed_profile_image_margin',
			array(
				'label'              => __( 'Profile Image Spacing', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 12,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-twitter-feed-item-avatar img' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition'          => array(
					'uael_twitter_feed_profile' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'uael_twitter_feed_profile_name_handler',
			array(
				'label'              => __( 'Spacing Between Name & Handle', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 21,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-twitter-feed-name-username' => 'line-height: {{SIZE}}{{UNIT}};',
				),
				'condition'          => array(
					'uael_twitter_feed_show_name'     => 'yes',
					'uael_twitter_feed_show_username' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'uael_twitter_feed_content_top_margin',
			array(
				'label'              => __( 'Content Top Spacing', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 10,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-twitter-feed__title' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'uael_twitter_feed_media_top_margin',
			array(
				'label'              => __( 'Media Top Spacing', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 10,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-twitter-feed-image-video' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition'          => array(
					'uael_twitter_feed_show_media' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'uael_twitter_feed_content_bottom_margin',
			array(
				'label'              => __( 'Content Bottom Spacing', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 18,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-twitter-feed__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'uael_twitter_feed_date_info',
			array(
				'label'              => __( 'Spacing Between Date & Information', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 28,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-twitter-feed_inner-content' => 'line-height: {{SIZE}}{{UNIT}};',
				),
				'condition'          => array(
					'uael_twitter_feed_date'       => 'yes',
					'uael_twitter_feed_tweet_info' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

	}


	/**
	 * Register Twitter Feed Timeline Controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function register_timeline_style_control() {

		$this->start_controls_section(
			'uael_twitter_feed_Timeline',
			array(
				'label'     => __( 'Timeline', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'uael_twitter_feed_layout'     => 'timeline',
					'uael_twitter_feed_search_by!' => 'hashtag',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_timeline_label',
			array(
				'label' => __( 'Timeline', 'uael' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'uael_twitter_feed_timeline_background',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fcfcfc',
				'selectors' => array(
					'{{WRAPPER}} .uael-twitter-profile-feed' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'uael_twitter_feed_timeline_border',
				'selector'       => '{{WRAPPER}} .uael-twitter-profile-feed',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
					'width'  => array(
						'default' => array(
							'top'    => '2',
							'right'  => '2',
							'bottom' => '2',
							'left'   => '2',
						),
					),
					'color'  => array(
						'default' => '#ededed',
					),
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_timeline_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'%' => array(
						'min' => 1,
						'max' => 400,
					),
				),
				'default'    => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-twitter-profile-feed' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_timeline_profile',
			array(
				'label'     => __( 'Profile', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'header_enable'          => 'yes',
					'header_profile_section' => 'block',
				),
			)
		);

		$this->add_responsive_control(
			'heading_image_size',
			array(
				'label'              => __( 'Profile Size', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px', 'em', '%' ),
				'range'              => array(
					'px' => array(
						'min' => 1,
						'max' => 400,
					),
				),
				'default'            => array(
					'size' => 100,
					'unit' => 'px',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-twitter-feed-header-user-image-container .uael-twitter-feed-header-user-image' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'          => array(
					'header_enable'          => 'yes',
					'header_profile_section' => 'block',
				),
				'frontend_available' => true,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'uael_twitter_feed_timeline_header_image_border',
				'selector'  => '{{WRAPPER}} .uael-twitter-feed-header-user-image-container .uael-twitter-feed-header-user-image',
				'color'     => array(
					'default' => '#000',
				),
				'condition' => array(
					'header_enable'          => 'yes',
					'header_profile_section' => 'block',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_timeline_header_image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'%' => array(
						'min' => 1,
						'max' => 400,
					),
				),
				'default'    => array(
					'size' => 50,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-twitter-feed-header-user-image-container .uael-twitter-feed-header-user-image' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'header_enable'          => 'yes',
					'header_profile_section' => 'block',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'uael' ),
				'name'      => 'uael_twitter_feed_timeline_header_image_shadow',
				'selector'  => '{{WRAPPER}} .uael-twitter-feed-header-user-image-container .uael-twitter-feed-header-user-image',
				'condition' => array(
					'header_enable'          => 'yes',
					'header_profile_section' => 'block',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_timeline_button',
			array(
				'label'     => __( 'Follow Button', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'show_button_section' => 'flex',
					'header_enable'       => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'uael_twitter_feed_timeline_button_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-twitter-feed-header-user-info-follow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					'top'      => '10',
					'right'    => '10',
					'bottom'   => '10',
					'left'     => '10',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'condition'  => array(
					'show_button_section' => 'flex',
					'header_enable'       => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_twitter_feed_timeline_button_icon',
			array(
				'label'     => __( 'Icon Position', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'row'         => __( 'Left', 'uael' ),
					'row-reverse' => __( 'Right', 'uael' ),
				),
				'default'   => 'row',
				'condition' => array(
					'header_enable' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-twitter-feed-header-user-info-follow' => 'flex-direction: {{VALUE}}',
				),
				'condition' => array(
					'show_button_section' => 'flex',
					'header_enable'       => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Carousel Controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function register_carousel_control() {
		$this->start_controls_section(
			'section_caousel_options',
			array(
				'label'     => __( 'Carousel', 'uael' ),
				'type'      => Controls_Manager::SECTION,
				'condition' => array(
					'uael_twitter_feed_layout' => 'carousel',
				),
			)
		);

		$this->add_responsive_control(
			'slides_to_show',
			array(
				'label'              => __( 'Tweets to Show', 'uael' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 2,
				'tablet_default'     => 2,
				'mobile_default'     => 1,
				'condition'          => array(
					'uael_twitter_feed_layout' => 'carousel',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			array(
				'label'              => __( 'Tweets to Scroll', 'uael' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 1,
				'tablet_default'     => 1,
				'mobile_default'     => 1,
				'condition'          => array(
					'uael_twitter_feed_layout' => 'carousel',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'uael_twitter_feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => __( 'Autoplay Speed (ms)', 'uael' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => array(
					'autoplay'                 => 'yes',
					'uael_twitter_feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => __( 'Pause on Hover', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'autoplay'                 => 'yes',
					'uael_twitter_feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'infinite',
			array(
				'label'        => __( 'Infinite Loop', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'uael_twitter_feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'transition_speed',
			array(
				'label'     => __( 'Transition Speed (ms)', 'uael' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 500,
				'condition' => array(
					'uael_twitter_feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'navigation',
			array(
				'label'     => __( 'Navigation', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'both',
				'options'   => array(
					'both'   => __( 'Arrows and Dots', 'uael' ),
					'arrows' => __( 'Arrows', 'uael' ),
					'dots'   => __( 'Dots', 'uael' ),
					'none'   => __( 'None', 'uael' ),
				),
				'condition' => array(
					'uael_twitter_feed_layout' => 'carousel',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register Carousel Controls.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function register_timeline_control() {

		$this->start_controls_section(
			'uael_twitter_feed_timeline',
			array(
				'label'     => __( 'Timeline', 'uael' ),
				'type'      => Controls_Manager::SECTION,
				'condition' => array(
					'uael_twitter_feed_layout' => 'timeline',
				),
			)
		);

		$this->add_responsive_control(
			'uael_twitter_feed_timeline_height',
			array(
				'label'              => __( 'Height', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px', '%' ),
				'range'              => array(
					'px' => array(
						'min' => 1,
						'max' => 1000,
					),
				),
				'default'            => array(
					'size' => 400,
					'unit' => 'px',
				),
				'condition'          => array(
					'uael_twitter_feed_layout' => 'timeline',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-twitter-profile-feed .uael-twitter-feed' => 'height: {{SIZE}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'uael_twitter_feed_timeline_width',
			array(
				'label'              => __( 'Width', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px', '%' ),
				'range'              => array(
					'px' => array(
						'min' => 1,
						'max' => 1100,
					),
				),
				'default'            => array(
					'size' => 100,
					'unit' => '%',
				),
				'condition'          => array(
					'uael_twitter_feed_layout' => 'timeline',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-twitter-profile-feed' => 'max-width: {{SIZE}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'header_enable',
			array(
				'label'     => __( 'Profile Header', 'uael' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'label_on'  => 'Show',
				'label_off' => 'Hide',
				'separator' => 'before',
				'condition' => array(
					'uael_twitter_feed_search_by!' => 'hashtag',
				),
			)
		);

		$this->add_control(
			'header_profile_section',
			array(
				'label'     => __( 'Profile Image', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'block' => __( 'Show', 'uael' ),
					'none'  => __( 'Hide', 'uael' ),
				),
				'default'   => 'block',
				'condition' => array(
					'header_enable'                => 'yes',
					'uael_twitter_feed_search_by!' => 'hashtag',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-twitter-feed-header-user-image-container img' => 'display: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'header_image_section',
			array(
				'label'     => __( 'Cover Image', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'block' => __( 'Show', 'uael' ),
					'none'  => __( 'Hide', 'uael' ),
				),
				'default'   => 'block',
				'condition' => array(
					'header_enable'                => 'yes',
					'uael_twitter_feed_search_by!' => 'hashtag',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-twitter-header-banner img' => 'display: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'cover_image_height',
			array(
				'label'              => __( 'Cover Image Height', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px', 'em' ),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
					'em' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-twitter-header-banner img'    => 'height: {{SIZE}}{{UNIT}}',
				),
				'condition'          => array(
					'uael_twitter_feed_search_by!' => 'hashtag',
					'header_enable'                => 'yes',
					'header_image_section'         => 'block',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'author_section',
			array(
				'label'     => __( 'Author Details', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'block' => __( 'Show', 'uael' ),
					'none'  => __( 'Hide', 'uael' ),
				),
				'default'   => 'block',
				'condition' => array(
					'uael_twitter_feed_search_by!' => 'hashtag',
					'header_enable'                => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-twitter-feed-header-user-image , {{WRAPPER}} .uael-twitter-feed-header-user-info-name-wrapper'   => 'display: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'statistics_section',
			array(
				'label'     => __( 'User Data', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'flex' => __( 'Show', 'uael' ),
					'none' => __( 'Hide', 'uael' ),
				),
				'default'   => 'flex',
				'condition' => array(
					'uael_twitter_feed_search_by!' => 'hashtag',
					'header_enable'                => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-twitter-feed-header-statistics'   => 'display: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'show_button_section',
			array(
				'label'     => __( 'Follow Button', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'flex' => __( 'Show', 'uael' ),
					'none' => __( 'Hide', 'uael' ),
				),
				'default'   => 'flex',
				'condition' => array(
					'uael_twitter_feed_search_by!' => 'hashtag',
					'header_enable'                => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-twitter-feed-header-user-info-follow' => 'display: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		if ( parent::is_internal_links() ) {
			$this->start_controls_section(
				'section_helpful_info',
				array(
					'label' => __( 'Helpful Information', 'uael' ),
				)
			);

			$this->add_control(
				'help_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/twitter-feed-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Number Counter.
	 *
	 * @param array $name Twitter field names e.g. followers,following,likes etc count.
	 * @since 1.36.0
	 * @access public
	 */
	public function number_counter( $name ) {
		$count_var = $name;
		if ( $count_var > 1000000000000 ) {
			echo esc_html( round( ( $count_var / 1000000000000 ), 1 ) . 'T ' );
		} elseif ( $count_var > 1000000000 ) {
			echo esc_html( round( ( $count_var / 1000000000 ), 1 ) . 'B ' );
		} elseif ( $count_var > 1000000 ) {
			echo esc_html( round( ( $count_var / 1000000 ), 1 ) . 'M ' );
		} elseif ( $count_var > 1000 ) {
			echo esc_html( round( ( $count_var / 1000 ), 1 ) . 'K ' );
		} else {
			echo esc_html( round( $count_var ) . ' ' );
		}
	}

	/**
	 * Carousel attributes.
	 *
	 * @since 1.36.0
	 * @access public
	 */
	public function get_carousel_attr() {

		$settings = $this->get_settings_for_display();

		if ( 'carousel' !== $settings['uael_twitter_feed_layout'] ) {
			return;
		}
		$is_rtl      = is_rtl();
		$direction   = $is_rtl ? 'rtl' : 'ltr';
		$show_dots   = ( in_array( $settings['navigation'], array( 'dots', 'both' ), true ) );
		$show_arrows = ( in_array( $settings['navigation'], array( 'arrows', 'both' ), true ) );

		$slick_options = array(
			'slidesToShow'   => ( $settings['slides_to_show'] ) ? absint( $settings['slides_to_show'] ) : 4,
			'slidesToScroll' => ( $settings['slides_to_scroll'] ) ? absint( $settings['slides_to_scroll'] ) : 1,
			'autoplaySpeed'  => ( $settings['autoplay_speed'] ) ? absint( $settings['autoplay_speed'] ) : 5000,
			'autoplay'       => ( 'yes' === $settings['autoplay'] ),
			'infinite'       => ( 'yes' === $settings['infinite'] ),
			'pauseOnHover'   => ( 'yes' === $settings['pause_on_hover'] ),
			'speed'          => ( $settings['transition_speed'] ) ? absint( $settings['transition_speed'] ) : 500,
			'arrows'         => $show_arrows,
			'dots'           => $show_dots,
			'rtl'            => $is_rtl,
		);

		if ( $settings['slides_to_show_tablet'] || $settings['slides_to_show_mobile'] ) {
			$slick_options['responsive'] = array();

			if ( $settings['slides_to_show_tablet'] ) {
				$tablet_show   = absint( $settings['slides_to_show_tablet'] );
				$tablet_scroll = ( $settings['slides_to_scroll_tablet'] ) ? absint( $settings['slides_to_scroll_tablet'] ) : $tablet_show;

				$slick_options['responsive'][] = array(
					'breakpoint' => 1024,
					'settings'   => array(
						'slidesToShow'   => $tablet_show,
						'slidesToScroll' => $tablet_scroll,
					),
				);
			}

			if ( $settings['slides_to_show_mobile'] ) {
				$mobile_show   = absint( $settings['slides_to_show_mobile'] );
				$mobile_scroll = ( $settings['slides_to_scroll_mobile'] ) ? absint( $settings['slides_to_scroll_mobile'] ) : $mobile_show;

				$slick_options['responsive'][] = array(
					'breakpoint' => 767,
					'settings'   => array(
						'slidesToShow'   => $mobile_show,
						'slidesToScroll' => $mobile_scroll,
					),
				);
			}
		}

		$slick_options = apply_filters( 'uael_twitter_carousel_options', $slick_options );
		$this->add_render_attribute(
			'carousel-wrap',
			array(
				'data-twitter_carousel_settings' => wp_json_encode( $slick_options ),
			)
		);
	}

	/**
	 * Twitter Feed Avatar
	 *
	 * @param string $item retrive Twitter field.
	 * @param array  $settings retrive all settings.
	 * @since 1.36.0
	 */
	public function twitter_feed_header_avatar( $item, $settings ) {

		$tweet_profile = ( isset( $item['user']['profile_image_url_https'] ) ) ? $item['user']['profile_image_url_https'] : '';
		$tweet_name    = ( isset( $item['user']['screen_name'] ) ) ? $item['user']['screen_name'] : '';

			$item_logo = str_ireplace( '_normal', '_200x200', $tweet_profile );

			$avtar = '<a class="uael-twitter-feed-item-avatar uael-twitter-feed-item-avatar-style-' . $settings['uael_twitter_feed_profile_style'] . '" href="https://twitter.com/' . $tweet_name . '" target="_blank"><img src="' . $item_logo . '" alt="' . $tweet_name . '"></a>';
			echo wp_kses_post( $avtar );
	}

	/**
	 * Twitter Feed Logo
	 *
	 * @since 1.36.0
	 */
	public function twitter_feed_header_logo() {
		?>
		<div class="uael-twitter-feed-twitter-logo">
			<?php
				echo wp_kses_post( '<i class="fab fa-twitter"></i>' );
			?>
		</div>
		<?php
	}

	/**
	 * Twitter Feed
	 *
	 * @param array $item retrive Twitter field.
	 * @param array $settings retrive all settings.
	 * @since 1.36.0
	 */
	public function twitter_feed_header( $item, $settings ) {
		?>
		<div class="uael-twitter-feed-card-header-icon">
			<div class="uael-twitter-feed-title-name">
				<?php
				if ( 'yes' === $settings['uael_twitter_feed_profile'] ) {
						$this->twitter_feed_header_avatar( $item, $settings );
				}
				?>
				<?php $this->twitter_feed_header_name( $item, $settings ); ?>
			</div>
			<?php if ( 'yes' === $settings['uael_twitter_feed_twitter_logo'] ) { ?>
				<?php $this->twitter_feed_header_logo(); ?>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Twitter Feed
	 *
	 * @param array $item retrive Twitter field.
	 * @param array $settings retrive all settings.
	 * @since 1.36.0
	 */
	public function twitter_feed_header_name( $item, $settings ) {
		$tweet_name     = ( isset( $item['user']['screen_name'] ) ) ? $item['user']['screen_name'] : '';
		$verified       = ( isset( $item['user']['verified'] ) ) ? $item['user']['verified'] : '';
		$tweet_username = ( isset( $item['user']['screen_name'] ) ) ? $item['user']['screen_name'] : '';
		?>
		<div class="uael-twitter-feed-name-username">
			<div class="uael-twitter-feed-name">
				<?php
				if ( 'yes' === $settings['uael_twitter_feed_show_name'] ) {
					echo esc_html( $tweet_name );
					?>
					<?php
					if ( true === $verified ) {
						?>
						<span class="uael-twitter-feed-header-user-info-name-verified-container" title="Verified account">
							<svg class="uael-twitter-feed-header-user-info-name-verified" width="16" height="16" viewBox="0 0 20 20">
							<path style="fill:#1da1f2;fill-opacity:1" d="m 14.5436,18.0924 c -0.160467,0 -0.3878,-0.03 -0.682,-0.09 -0.2942,-0.06 -0.488133,-0.1102 -0.5818,-0.1506 -0.33428,0.669333 -0.78895,1.194767 -1.36401,1.5763 -0.57506,0.381533 -1.216987,0.5723 -1.92578,0.5723 -0.7087933,0 -1.3674367,-0.210833 -1.97593,-0.6325 -0.6084933,-0.421733 -1.0331,-0.9271 -1.27382,-1.5161 -0.4145733,0.160667 -0.8425233,0.241 -1.28385,0.241 -1.0030067,0 -1.8589067,-0.3782 -2.5677,-1.1346 -0.7087933,-0.756333 -1.0565033,-1.6499 -1.04313,-2.6807 -0.0134,-0.04 -0.0134,-0.08017 0,-0.1205 l 0,-0.1205 c -0.0134,-0.04013 -0.0134,-0.08028 0,-0.12044 0.0134,-0.04013 0.0134,-0.08029 0,-0.12048 C 1.27052,13.420747 0.81916333,12.942167 0.49151,12.35984 0.16383667,11.77724 0,11.147923 0,10.47189 0,9.79585 0.17719667,9.1398933 0.53159,8.50402 0.88599,7.86814 1.39084,7.3828633 2.04614,7.04819 L 1.96594,6.72691 C 1.8857,6.5261033 1.84558,6.2985267 1.84558,6.04418 1.8188467,5.93708 1.8188467,5.82329 1.84558,5.70281 1.83218,4.68541 2.1732033,3.7951833 2.86865,3.03213 3.56407,2.2690767 4.4266567,1.88755 5.45641,1.88755 c 0.4413267,0 0.8692767,0.08032 1.28385,0.24096 C 6.9943533,1.5261033 7.4156167,1.02075 8.00405,0.61245 8.5924567,0.20415 9.25443,0 9.98997,0 c 1.47108,0 2.56769,0.70950333 3.28983,2.12851 0.3544,-0.16064 0.775667,-0.24096 1.2638,-0.24096 1.003,0 1.855567,0.3748333 2.5577,1.1245 0.702133,0.7496667 1.066567,1.6465867 1.0933,2.69076 -0.01333,0.08032 -0.02,0.19411 -0.02,0.34137 l -0.1203,0.68273 c -0.02667,0.12048 -0.0668,0.2275733 -0.1204,0.32128 0.6018,0.2811267 1.089933,0.7195467 1.4644,1.31526 0.374467,0.59572 0.575067,1.2951867 0.6018,2.0984 -0.02667,0.749667 -0.2072,1.41901 -0.5416,2.00803 -0.334333,0.58902 -0.775667,1.030787 -1.324,1.3253 0.02667,0.05353 0.04,0.09369 0.04,0.12048 l 0.02,0.24094 c -0.02667,0.04 -0.02667,0.08017 0,0.1205 -0.02667,1.070933 -0.394433,1.974567 -1.1033,2.7109 -0.7088,0.736267 -1.558033,1.1044 -2.5477,1.1044"></path>
							<path style="fill:#ffffff;fill-opacity:1;" d="M 13.2598,6.58635 8.42528,11.40562 6.76028,9.71888 C 6.51956,9.5180733 6.28218,9.41767 6.04814,9.41767 5.8141067,9.41767 5.5633567,9.5180733 5.29589,9.71888 5.0952833,10 4.9983267,10.271083 5.00502,10.53213 c 0.00667,0.26104 0.11031,0.471883 0.31093,0.63253 l 2.38716,2.40964 c 0.24072,0.2008 0.5015033,0.3012 0.78235,0.3012 0.28084,0 0.5015,-0.1004 0.66198,-0.3012 l 0.0201,0 5.524341,-5.6675353 C 15.199662,7.3478056 14.827995,6.7252711 14.674931,6.5787563 14.521867,6.4322415 13.835901,6.0147732 13.2598,6.58635 z"></path>
							</svg>
						</span>
						<?php
					}
				}
				?>
			</div>
			<?php if ( 'yes' === $settings['uael_twitter_feed_show_username'] ) { ?>
				<div class="uael-twitter-feed-username">
					<?php
					echo esc_html( '@' . $tweet_username );
					?>
				</div>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Twitter Feed
	 *
	 * @param array $item retrive Twitter field.
	 * @param array $settings retrive all settings.
	 * @since 1.36.0
	 */
	public function twitter_feed_content( $item, $settings ) {

		$uael_twitter_feed_link_target = 'target="_blank"';
		if ( ! empty( $settings['uael_twitter_feed_link_target'] ) && '_self' === $settings['uael_twitter_feed_link_target'] ) {
			$uael_twitter_feed_link_target = 'target="_self"';
		}

		$screen_name  = ( isset( $item['user']['screen_name'] ) ) ? $item['user']['screen_name'] : '';
		$content_text = ( isset( $item['full_text'] ) ) ? $item['full_text'] : '';
		$str          = ( isset( $item['id_str'] ) ) ? $item['id_str'] : '';
		$delimeter    = strlen( $content_text ) > $settings['uael_twitter_feed_content_length'] ? '...' : '';
		?>
		<div class="uael-twitter-feed__title">
			<?php
			if ( 'yes' === $settings['uael_twitter_feed_tweet_content'] ) {
				$link_free_text = isset( $item['entities']['urls'][0]['url'] ) ? str_replace( $item['entities']['urls'][0]['url'], '', $content_text ) : $content_text;
				$text           = substr( $link_free_text, 0, $settings['uael_twitter_feed_content_length'] ) . $delimeter;
				?>
					<p>
						<?php echo esc_html( $text ); ?>
					</p>
				<?php
			}
			if ( 'yes' === $settings['uael_twitter_feed_read_more'] ) {
				$read_more      = ! empty( $settings['uael_twitter_feed_read_more_text'] ) ? $settings['uael_twitter_feed_read_more_text'] : __( 'Read More', 'uael' );
				$read_more_link = 'https://twitter.com/' . $screen_name . '/status/' . $str . '" ';

				echo wp_kses_post( '<span><a ' . $uael_twitter_feed_link_target . ' href="' . $read_more_link . 'class="read-more-link">' . $read_more . '</a></span>' );
			}
			$this->twitter_feed_content_media( $item, $settings );
			?>
		</div>
		<?php
	}

	/**
	 * Twitter Feed
	 *
	 * @param array $item retrive Twitter field.
	 * @param array $settings retrive all settings.
	 * @since 1.36.0
	 */
	public function twitter_feed_content_media( $item, $settings ) {
		$video_url = ( isset( $item['extended_entities']['media'][0]['video_info']['variants'][2]['url'] ) ) ? $item['extended_entities']['media'][0]['video_info']['variants'][2]['url'] : '';
		$image_url = ( isset( $item['extended_entities']['media'][0]['media_url_https'] ) ) ? $item['extended_entities']['media'][0]['media_url_https'] : '';
		?>
		<div class="uael-twitter-feed-image-video">
			<?php
			if ( isset( $item['extended_entities']['media'][0] ) && 'yes' === $settings['uael_twitter_feed_show_media'] ) {
				if ( 'photo' === $item['extended_entities']['media'][0]['type'] ) {
					?>
						<img src="<?php echo esc_url( $image_url ); ?>">
					<?php
				} elseif ( 'video' === $item['extended_entities']['media'][0]['type'] ) {
					?>
						<video width="500" controls>
							<source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
						</video>
					<?php
				}
			}
			?>
		</div>
		<?php
	}

	/**
	 * Twitter Feed
	 *
	 * @param array $item Twitter field names e.g. followers,following,likes etc count.
	 * @param array $settings retrive all settings.
	 * @since 1.36.0
	 */
	public function twitter_feed_footer( $item, $settings ) {
		$created_at    = ( isset( $item['created_at'] ) ) ? $item['created_at'] : '';
		$retweet_count = ( isset( $item['retweet_count'] ) ) ? $item['retweet_count'] : '';
		$likes_count   = ( isset( $item['favorite_count'] ) ) ? $item['favorite_count'] : '';

		?>
			<div class="uael-twitter-feed_inner-content">
				<div class="uael-twitter-feed-date">
					<?php
					if ( 'yes' === $settings['uael_twitter_feed_date'] ) {
						$returned_timestamp = strtotime( $created_at );
						echo esc_html( gmdate( 'h:i A  · ', strtotime( $created_at ) ) );
						echo esc_html( gmdate( 'd M Y', $returned_timestamp ) );
					}
					?>
				</div>
				<?php if ( 'yes' === $settings['uael_twitter_feed_tweet_info'] ) { ?>
				<div class="uael-twitter-feed-tweet-info">
					<span class="uael-twitter-feed-retweet">
						<span class="uael-twitter-feed-retweet-count">
							<b>
							<?php
								echo esc_html( $this->number_counter( $retweet_count ) );
							?>
							</b>
						</span>
						<?php esc_html_e( ' Retweet', 'uael' ); ?>
					</span>
					<span class="uael-twitter-feed-likes">
						<span class="uael-twitter-feed-likes-count">
							<b>
							<?php
								echo esc_html( $this->number_counter( $likes_count ) );
							?>
							</b>
						</span>
						<?php esc_html_e( ' likes', 'uael' ); ?>
					</span>
				</div>
				<?php } ?>
			</div>
		<?php
	}

	/**
	 * Twitter Feed fetching data using hashtag
	 *
	 * @param array $settings retrive all settings.
	 *
	 * @since 1.36.0
	 */
	public function twitter_feed_render_hashtag_items( $settings ) {

		$cache_key = $this->get_id() . '_' . $settings['uael_twitter_feed_hashtag_name'] . $settings['uael_twitter_feed_data_cache_limit'] . '_tf_cache';
		$items     = get_transient( $cache_key );
		$page_id   = get_the_id();
		$is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();

		if ( ! empty( $settings['uael_twitter_feed_hashtag_name'] ) ) {
			$twitter_hashtag         = $settings['uael_twitter_feed_hashtag_name'];
			$twitter_consumer_key    = self::$integration_settings['uael_twitter_feed_consumer_key'];
			$twitter_consumer_secret = self::$integration_settings['uael_twitter_feed_consumer_secret'];
			if ( empty( $twitter_consumer_key ) || empty( $twitter_consumer_secret ) ) {
				return;
			}

			if ( false === $items ) {

				if ( empty( $token ) ) {
					$token = $this->get_token( $page_id, $settings );
				}

				add_filter( 'https_ssl_verify', '__return_false' );

				$twitter_api_url = 'https://api.twitter.com/2/tweets/search/recent?query=%23' . urlencode( $twitter_hashtag ) . '&max_results=100';

				$response = wp_remote_get(
					$twitter_api_url,
					array(
						'headers' => array(
							'Authorization' => 'Bearer ' . $token,
						),
					)
				);

				if ( is_wp_error( $response ) ) {
					return;
				}

				$response_code = wp_remote_retrieve_response_code( $response );

				if ( 200 === $response_code ) {
					$items = json_decode( wp_remote_retrieve_body( $response ), true );
					set_transient( $cache_key, $items, $settings['uael_twitter_feed_data_cache_limit'] * HOUR_IN_SECONDS );

					if ( empty( $items['data'] ) && $is_editor ) {
						?>
						<div class="uael-builder-msg elementor-alert elementor-alert-warning">
							<span class="elementor-alert-title">
								<?php esc_html_e( 'Twitter Feed - ID ', 'uael' ); ?><?php echo esc_html( $page_id ); ?>
							</span>
							<span class="elementor-alert-description">
								<?php esc_html_e( 'To show Twitter Feed widget, please set the valid Hashtag from widget settings.', 'uael' ); ?><br>
							</span>
						</div>
						<?php
						return;
					}
				}           
			}

			if ( empty( $items ) ) {
				return;
			}
			foreach ( $items as $hashtag_item ) {

				$hashtag_item = array_splice( $hashtag_item, 0, (int) $settings['uael_twitter_feed_post_limit'], true );

				switch ( $settings['uael_twitter_feed_sort_by'] ) {
					case 'old-posts':
						usort(
							$hashtag_item,
							function ( $a, $b ) {
								if ( strtotime( $a['created_at'] ) === strtotime( $b['created_at'] ) ) {
									return 0;
								}
								return ( strtotime( $a['created_at'] ) < strtotime( $b['created_at'] ) ? -1 : 1 );
							}
						);
						break;
					case 'favorite_count':
						usort(
							$hashtag_item,
							function ( $a, $b ) {
								if ( $a['favorite_count'] === $b['favorite_count'] ) {
									return 0;
								}
								return ( $a['favorite_count'] > $b['favorite_count'] ) ? -1 : 1;
							}
						);
						break;
					case 'retweet_count':
						usort(
							$hashtag_item,
							function ( $a, $b ) {
								if ( $a['retweet_count'] === $b['retweet_count'] ) {
									return 0;
								}
								return ( $a['retweet_count'] > $b['retweet_count'] ) ? -1 : 1;
							}
						);
						break;
					default:
						$hashtag_item;
				}
					return $hashtag_item;
			}
		}
	}

	/**
	 * Get access token.
	 *
	 * @param array $page_id retrive page id.
	 * @param array $settings retrive all settings.
	 *
	 * @since 1.36.0
	 * @access public
	 */
	public function get_token( $page_id, $settings ) {
		$twitter_consumer_key    = self::$integration_settings['uael_twitter_feed_consumer_key'];
		$twitter_consumer_secret = self::$integration_settings['uael_twitter_feed_consumer_secret'];

			$credentials = base64_encode( $twitter_consumer_key . ':' . $twitter_consumer_secret ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			add_filter( 'https_ssl_verify', '__return_false' );
			$response = wp_remote_post(
				'https://api.twitter.com/oauth2/token',
				array(
					'method'      => 'POST',
					'httpversion' => '1.1',
					'blocking'    => true,
					'headers'     => array(
						'Authorization' => 'Basic ' . $credentials,
						'Content-Type'  => 'application/x-www-form-urlencoded;charset=UTF-8',
					),
					'body'        => array( 'grant_type' => 'client_credentials' ),
				)
			);
			$body     = json_decode( wp_remote_retrieve_body( $response ) );
		if ( $body ) {
			if ( ! empty( $body->access_token ) ) {
				update_option( $this->get_id() . '_' . $settings['uael_twitter_feed_username'] . '_tf_token', $body->access_token );
				$token = $body->access_token;
				return $token;
			} else {
				?>
					<div class="uael-builder-msg elementor-alert elementor-alert-warning">
						<span class="elementor-alert-title">
						<?php esc_html_e( 'Twitter Feed - ID ', 'uael' ); ?><?php echo esc_html( $page_id ); ?>
						</span>
						<span class="elementor-alert-description">
						<?php esc_html_e( 'Invalid Consumer key or Consumer Secret Key.', 'uael' ); ?><br>
						<?php esc_html_e( 'Navigate to Settings -> UAE -> Twitter Feed -> Settings.', 'uael' ); ?>
						</span>
					</div>
					<?php
					return;
			}
		}
	}

	/**
	 * Twitter Feed fetching data using username
	 *
	 * @param array $settings retrive all settings.
	 * @since 1.36.0
	 */
	public function twitter_feed_render_items( $settings ) {

		if ( ! empty( $settings['uael_twitter_feed_username'] ) ) {
			$token     = get_option( $this->get_id() . '_' . $settings['uael_twitter_feed_username'] . '_tf_token' );
			$cache_key = $this->get_id() . '_' . $settings['uael_twitter_feed_username'] . $settings['uael_twitter_feed_data_cache_limit'] . '_tf_cache';
			$items     = get_transient( $cache_key );
			$page_id   = get_the_id();
			$is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();

			$twitter_username        = $settings['uael_twitter_feed_username'];
			$twitter_consumer_key    = self::$integration_settings['uael_twitter_feed_consumer_key'];
			$twitter_consumer_secret = self::$integration_settings['uael_twitter_feed_consumer_secret'];
			if ( empty( $twitter_consumer_key ) || empty( $twitter_consumer_secret ) ) {
				return;
			}

			if ( false === $items ) {

				if ( empty( $token ) ) {
					$token = $this->get_token( $page_id, $settings );
				}

				add_filter( 'https_ssl_verify', '__return_false' );

				$twitter_url = 'https://api.twitter.com/2/tweets';
				$params      = array(
					'screen_name' => '@' . $twitter_username,
					'max_results' => 999,
					'tweet_mode'  => 'extended',
				);
				
				$request_url = add_query_arg( $params, $twitter_url );

				$response = wp_remote_get(
					$request_url,
					array(
						'httpversion' => '1.1',
						'blocking'    => true,
						'headers'     => array(
							'Authorization' => "Bearer $token",
						),
					)
				);

				if ( is_wp_error( $response ) ) {
					return;
				}

				if ( ! empty( $response['response'] ) && 200 === $response['response']['code'] ) {
					$items = json_decode( wp_remote_retrieve_body( $response ), true );
					set_transient( $cache_key, $items, $settings['uael_twitter_feed_data_cache_limit'] * MINUTE_IN_SECONDS );
				} elseif ( ! empty( $token ) && $is_editor ) {
					?>
					<div class="uael-builder-msg elementor-alert elementor-alert-warning">
						<span class="elementor-alert-title">
							<?php esc_html_e( 'Twitter Feed - ID ', 'uael' ); ?><?php echo esc_html( $page_id ); ?>
						</span>
						<span class="elementor-alert-description">
							<?php esc_html_e( 'To show Twitter Feed widget, please set valid Consumer key and Consumer Secret Key.', 'uael' ); ?><br>
							<?php esc_html_e( 'Navigate to Settings -> UAE -> Twitter Feed -> Settings.', 'uael' ); ?>
						</span>
					</div>
					<?php
					return;
				}
			}

			if ( empty( $items ) ) {
				return;
			}

			$items = array_splice( $items, 0, (int) $settings['uael_twitter_feed_post_limit'], true );

			switch ( $settings['uael_twitter_feed_sort_by'] ) {
				case 'old-posts':
					usort(
						$items,
						function ( $a, $b ) {
							if ( strtotime( $a['created_at'] ) === strtotime( $b['created_at'] ) ) {
								return 0;
							}
							return ( strtotime( $a['created_at'] ) < strtotime( $b['created_at'] ) ? -1 : 1 );
						}
					);
					break;
				case 'favorite_count':
					usort(
						$items,
						function ( $a, $b ) {
							if ( $a['favorite_count'] === $b['favorite_count'] ) {
								return 0;
							}
							return ( $a['favorite_count'] > $b['favorite_count'] ) ? -1 : 1;
						}
					);
					break;
				case 'retweet_count':
					usort(
						$items,
						function ( $a, $b ) {
							if ( $a['retweet_count'] === $b['retweet_count'] ) {
								return 0;
							}
							return ( $a['retweet_count'] > $b['retweet_count'] ) ? -1 : 1;
						}
					);
					break;
				default:
					$items;
			}
			return $items;
		}
	}

	/**
	 * Twitter Feed
	 *
	 * @param array $items retrive Twitter field.
	 * @param array $settings retrive all settings.
	 * @since 1.36.0
	 */
	public function twitter_feed_timeline( $items, $settings ) {

		if ( isset( $settings['uael_twitter_feed_username'] ) && empty( $settings['uael_twitter_feed_hashtag_name'] ) ) {
			if ( isset( $items ) && is_array( $items ) ) {
				foreach ( $items as $item ) {
					?>
					<div class="uael-twitter-user-cover">
						<?php if ( 'yes' === $settings['header_enable'] ) { ?>
							<div class="uael-twitter-feed-profile-body">
								<div class="uael-twitter-header-banner">
									<img src="<?php echo wp_kses_post( $item['user']['profile_banner_url'] ); ?>"  alt="<?php echo esc_attr( $item['user']['name'] ); ?>">
								</div>
								<div class="uael-twitter-feed-header-user">
									<a rel="nofollow" href="https://twitter.com/<?php echo esc_url( $item['user']['profile_banner_url'] ); ?>" target="_blank" class="uael-twitter-feed-header-user-image-container">
										<img class="uael-twitter-feed-header-user-image" src="
											<?php
											$profile_banner_logo = str_ireplace( '_normal', '_200x200', esc_url( $item['user']['profile_image_url_https'] ) );
											echo wp_kses_post( $profile_banner_logo );
											?>
										" alt="<?php echo esc_attr( $item['user']['name'] ); ?>" />
									</a>
									<div class="uael-twitter-feed-header-user-info">
										<div class="uael-twitter-feed-header-user-info-name-wrapper">
											<div class="uael-twitter-feed-header-user-info-name">
												<a class="uael-twitter-header-link" rel="nofollow" href="https://twitter.com/<?php $item['user']['screen_name']; ?>" title="<?php echo esc_attr( $item['user']['name'] ); ?>" target="_blank">
														<?php echo esc_html( $item['user']['name'] ); ?>
														<?php if ( true === $item['user']['verified'] ) { ?>
															<span class="uael-twitter-feed-header-user-info-name-verified-container" title="Verified account">
																<svg class="uael-twitter-feed-header-user-info-name-verified" width="16" height="16" viewBox="0 0 20 20">
																<path style="fill:#1da1f2;fill-opacity:1" d="m 14.5436,18.0924 c -0.160467,0 -0.3878,-0.03 -0.682,-0.09 -0.2942,-0.06 -0.488133,-0.1102 -0.5818,-0.1506 -0.33428,0.669333 -0.78895,1.194767 -1.36401,1.5763 -0.57506,0.381533 -1.216987,0.5723 -1.92578,0.5723 -0.7087933,0 -1.3674367,-0.210833 -1.97593,-0.6325 -0.6084933,-0.421733 -1.0331,-0.9271 -1.27382,-1.5161 -0.4145733,0.160667 -0.8425233,0.241 -1.28385,0.241 -1.0030067,0 -1.8589067,-0.3782 -2.5677,-1.1346 -0.7087933,-0.756333 -1.0565033,-1.6499 -1.04313,-2.6807 -0.0134,-0.04 -0.0134,-0.08017 0,-0.1205 l 0,-0.1205 c -0.0134,-0.04013 -0.0134,-0.08028 0,-0.12044 0.0134,-0.04013 0.0134,-0.08029 0,-0.12048 C 1.27052,13.420747 0.81916333,12.942167 0.49151,12.35984 0.16383667,11.77724 0,11.147923 0,10.47189 0,9.79585 0.17719667,9.1398933 0.53159,8.50402 0.88599,7.86814 1.39084,7.3828633 2.04614,7.04819 L 1.96594,6.72691 C 1.8857,6.5261033 1.84558,6.2985267 1.84558,6.04418 1.8188467,5.93708 1.8188467,5.82329 1.84558,5.70281 1.83218,4.68541 2.1732033,3.7951833 2.86865,3.03213 3.56407,2.2690767 4.4266567,1.88755 5.45641,1.88755 c 0.4413267,0 0.8692767,0.08032 1.28385,0.24096 C 6.9943533,1.5261033 7.4156167,1.02075 8.00405,0.61245 8.5924567,0.20415 9.25443,0 9.98997,0 c 1.47108,0 2.56769,0.70950333 3.28983,2.12851 0.3544,-0.16064 0.775667,-0.24096 1.2638,-0.24096 1.003,0 1.855567,0.3748333 2.5577,1.1245 0.702133,0.7496667 1.066567,1.6465867 1.0933,2.69076 -0.01333,0.08032 -0.02,0.19411 -0.02,0.34137 l -0.1203,0.68273 c -0.02667,0.12048 -0.0668,0.2275733 -0.1204,0.32128 0.6018,0.2811267 1.089933,0.7195467 1.4644,1.31526 0.374467,0.59572 0.575067,1.2951867 0.6018,2.0984 -0.02667,0.749667 -0.2072,1.41901 -0.5416,2.00803 -0.334333,0.58902 -0.775667,1.030787 -1.324,1.3253 0.02667,0.05353 0.04,0.09369 0.04,0.12048 l 0.02,0.24094 c -0.02667,0.04 -0.02667,0.08017 0,0.1205 -0.02667,1.070933 -0.394433,1.974567 -1.1033,2.7109 -0.7088,0.736267 -1.558033,1.1044 -2.5477,1.1044"></path>
																<path style="fill:#ffffff;fill-opacity:1;" d="M 13.2598,6.58635 8.42528,11.40562 6.76028,9.71888 C 6.51956,9.5180733 6.28218,9.41767 6.04814,9.41767 5.8141067,9.41767 5.5633567,9.5180733 5.29589,9.71888 5.0952833,10 4.9983267,10.271083 5.00502,10.53213 c 0.00667,0.26104 0.11031,0.471883 0.31093,0.63253 l 2.38716,2.40964 c 0.24072,0.2008 0.5015033,0.3012 0.78235,0.3012 0.28084,0 0.5015,-0.1004 0.66198,-0.3012 l 0.0201,0 5.524341,-5.6675353 C 15.199662,7.3478056 14.827995,6.7252711 14.674931,6.5787563 14.521867,6.4322415 13.835901,6.0147732 13.2598,6.58635 z"></path>
															</svg>
															</span>
														<?php } ?>
													</span>
												</a>
											</div>
											<div class="uael-twitter-feed-header-user-info-screen-name">
												<span class="uael-twitter-screen-name">
													<a rel="nofollow" href="https://twitter.com/<?php echo esc_html( $item['user']['screen_name'] ); ?>" target="_blank">
														<?php echo wp_kses_post( $item['user']['screen_name'] ); ?>
													</a>
												</span>
											</div>
										</div>
										<a rel="nofollow" href="https://twitter.com/intent/follow?screen_name=Eminem" target="_blank" class="uael-twitter-feed-header-user-info-follow">
											<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
												<path d="M16.07 5.388c.219 4.83-3.395 10.216-9.79 10.216A9.765 9.765 0 0 1 1 14.06a6.94 6.94 0 0 0 5.1-1.421 3.446 3.446 0 0 1-3.218-2.385c.54.103 1.07.073 1.555-.06-1.656-.33-2.8-1.82-2.763-3.41.465.258.996.412 1.56.43A3.432 3.432 0 0 1 2.17 2.628a9.788 9.788 0 0 0 7.1 3.589C8.766 4.068 10.4 2 12.624 2c.99 0 1.885.417 2.513 1.084a6.925 6.925 0 0 0 2.188-.833 3.452 3.452 0 0 1-1.515 1.9 6.9 6.9 0 0 0 1.978-.54 6.96 6.96 0 0 1-1.718 1.777z"></path>
											</svg>
											<span class="uael-twitter-feed-header-user-info-follow-label"><?php esc_html_e( 'Follow', 'uael' ); ?></span>
										</a>
									</div>
								</div>
								<div class="uael-twitter-feed-header-statistics">
									<p class="uael-twitter-header-count">
										<a href="https://twitter.com/<?php echo esc_html( $item['user']['screen_name'] ); ?>" target="_blank">
											<span>
												<b>
												<?php
													$twitter_tweets = $item['user']['statuses_count'];
													echo esc_html( $this->number_counter( $twitter_tweets ) );
												?>
												</b>
											</span>
											<span> <?php esc_html_e( 'Tweets', 'uael' ); ?> </span>
										</a>
									</p>
									<p class="uael-twitter-header-count">
										<a href="https://twitter.com/<?php echo esc_html( $item['user']['screen_name'] ); ?>/following" target="_blank">
											<span>
												<b>
												<?php
													$following = $item['user']['friends_count'];
													echo wp_kses_post( $this->number_counter( $following ) );
												?>
												</b>
											</span>
											<span> <?php esc_html_e( 'Following', 'uael' ); ?> </span>
										</a>
									</p>
										<p class="uael-twitter-header-count">
										<a href="https://twitter.com/<?php echo esc_html( $item['user']['screen_name'] ); ?>/followers" target="_blank">
											<span>
											<b>
											<?php
												$followers = $item['user']['followers_count'];
												echo wp_kses_post( $this->number_counter( $followers ) );
											?>
											</b>
											</span>
											<span> <?php esc_html_e( 'Followers', 'uael' ); ?> </span>
										</a>
									</p>
								</div>
								<div class="uael-twitter-feed-header-info">
									<div class="uael-twitter-feed-profile-icon">
									<span class="description">
										<?php echo esc_html( $item['user']['description'] ); ?>
									</span>
										<div class="uael-twitter-feed-icon">
											<span>
												<svg viewBox="3 0 18 22" aria-hidden="true" class="r-14j79pv r-4qtqp9 r-yyyyoo r-1xvli5t r-1d4mawv r-dnmrzs r-bnwqim r-1plcrui r-lrvibr"><g><path d="M12 14.315c-2.088 0-3.787-1.698-3.787-3.786S9.913 6.74 12 6.74s3.787 1.7 3.787 3.787-1.7 3.785-3.787 3.785zm0-6.073c-1.26 0-2.287 1.026-2.287 2.287S10.74 12.814 12 12.814s2.287-1.025 2.287-2.286S13.26 8.24 12 8.24z"></path><path d="M20.692 10.69C20.692 5.9 16.792 2 12 2s-8.692 3.9-8.692 8.69c0 1.902.603 3.708 1.743 5.223l.003-.002.007.015c1.628 2.07 6.278 5.757 6.475 5.912.138.11.302.163.465.163.163 0 .327-.053.465-.162.197-.155 4.847-3.84 6.475-5.912l.007-.014.002.002c1.14-1.516 1.742-3.32 1.742-5.223zM12 20.29c-1.224-.99-4.52-3.715-5.756-5.285-.94-1.25-1.436-2.742-1.436-4.312C4.808 6.727 8.035 3.5 12 3.5s7.192 3.226 7.192 7.19c0 1.57-.497 3.062-1.436 4.313-1.236 1.57-4.532 4.294-5.756 5.285z"></path></g>
												</svg>
												<span>
													<?php echo esc_html( $item['user']['location'] ); ?>
												</span>
											</span>
											<span>
												<span>
													<svg viewBox="2 0 24 22" aria-hidden="true" class="r-14j79pv r-4qtqp9 r-yyyyoo r-1xvli5t r-1d4mawv r-dnmrzs r-bnwqim r-1plcrui r-lrvibr"><g><path d="M19.708 2H4.292C3.028 2 2 3.028 2 4.292v15.416C2 20.972 3.028 22 4.292 22h15.416C20.972 22 22 20.972 22 19.708V4.292C22 3.028 20.972 2 19.708 2zm.792 17.708c0 .437-.355.792-.792.792H4.292c-.437 0-.792-.355-.792-.792V6.418c0-.437.354-.79.79-.792h15.42c.436 0 .79.355.79.79V19.71z"></path><circle cx="7.032" cy="8.75" r="1.285"></circle><circle cx="7.032" cy="13.156" r="1.285"></circle><circle cx="16.968" cy="8.75" r="1.285"></circle><circle cx="16.968" cy="13.156" r="1.285"></circle><circle cx="12" cy="8.75" r="1.285"></circle><circle cx="12" cy="13.156" r="1.285"></circle><circle cx="7.032" cy="17.486" r="1.285"></circle><circle cx="12" cy="17.486" r="1.285"></circle></g>
													</svg>
													<span>
														<?php esc_html_e( 'Joined', 'uael' ); ?>
														<?php
														$joined             = $item['user']['created_at'];
														$returned_timestamp = strtotime( $joined );
														echo esc_html( gmdate( ' · d M Y', $returned_timestamp ) );
														?>
													</span>
												</span>
											</span>
										</div>
									</div>
								</div>
							</div>
							<?php
						}
						?>
					</div>
					<?php
					break;
				}
			}
		}
	}

	/**
	 * Render Twitter Feed Warning.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @param array $settings retrive all settings.
	 * @param array $twitter_username retrive consumer key.
	 * @since 1.36.0
	 * @access protected
	 */
	public function warning( $settings, $twitter_username ) {

		$twitter_consumer_key    = self::$integration_settings['uael_twitter_feed_consumer_key'];
		$twitter_consumer_secret = self::$integration_settings['uael_twitter_feed_consumer_secret'];
		$is_editor               = \Elementor\Plugin::instance()->editor->is_edit_mode();
		$twitter_name            = ( isset( $settings['uael_twitter_feed_username'] ) ) ? __( 'Username', 'uael' ) : __( 'Hashtag', 'uael' );
		$page_id                 = get_the_id();

		if ( ( '' === $twitter_consumer_key || '' === $twitter_consumer_secret ) && $is_editor ) {
			?>
			<div class="uael-builder-msg elementor-alert elementor-alert-warning">
				<span class="elementor-alert-title">
					<?php esc_html_e( 'Twitter Feed - ID ', 'uael' ); ?><?php echo esc_html( $page_id ); ?>
				</span>
				<span class="elementor-alert-description"><?php esc_html_e( 'To show Twitter Feed widget, please set the Consumer key and Consumer Secret Key.', 'uael' ); ?><br>
					<?php esc_html_e( 'Navigate to Settings -> UAE -> Twitter Feed -> Settings.', 'uael' ); ?>
				</span>
			</div>
			<?php
			return;
		} elseif ( '' === $twitter_username && $is_editor ) {
			?>
			<div class="uael-builder-msg elementor-alert elementor-alert-warning">
				<span class="elementor-alert-title">
					<?php esc_html_e( 'Twitter Feed - ID ', 'uael' ); ?><?php echo esc_html( $page_id ); ?>
				</span>
				<span class="elementor-alert-description">
					<?php
						/* translators: %s Twitter name */
						echo esc_html( sprintf( __( 'To show Twitter Feed widget, please set the %s from widget settings.', 'uael' ), $twitter_name ) );// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					?>
					<br>
				</span>
			</div>
			<?php
			return;
		}
	}

	/**
	 * Render Twitter Feed output on the frontend.
	 *
	 * @param array $items retrive tweets.
	 * @param array $settings retrive all settings.
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function twitter_feed_username_display( $items, $settings ) {

		$this->get_carousel_attr();
		$twitter_username = $settings['uael_twitter_feed_username'];
		$mobile_column    = ( isset( $settings['uael_twitter_feed_col_mobile'] ) ) ? 'uael-twitter-feed-mobile-' . $settings['uael_twitter_feed_col_mobile'] . ' ' : '';
		$tablet_column    = ( isset( $settings['uael_twitter_feed_col_tablet'] ) ) ? 'uael-twitter-feed-tablet-' . $settings['uael_twitter_feed_col_tablet'] . ' ' : '';
		$column           = ( isset( $settings['uael_twitter_feed_col'] ) ) ? 'uael-twitter-feed-' . $settings['uael_twitter_feed_col'] . ' ' : '';
		$page_id          = get_the_id();

		$this->warning( $settings, $twitter_username );

		?>
			<div class="uael-twitter-feed">
				<?php
				$this->add_render_attribute(
					'carousel-wrap',
					array(
						'class' => 'uael-twitter-feed-' . $settings['uael_twitter_feed_layout'] . ' ' . $mobile_column . $tablet_column . $column,
					)
				);
				?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'carousel-wrap' ) ); ?>>
					<?php
					if ( isset( $items ) && is_array( $items ) ) {
						foreach ( $items as $item ) {
							?>
								<div class="uael-twitter-feed-item">
									<div class="uael-twitter-feed-item-inner">
										<div class="uael-twitter-feed-content">
											<?php
											$this->twitter_feed_header( $item, $settings );
											$this->twitter_feed_content( $item, $settings );
											$this->twitter_feed_footer( $item, $settings );
											?>
										</div>
									</div>
								</div>
							<?php
						}
					}
					?>
				</div>
			</div>
		<?php
	}

	/**
	 * Render Twitter Feed output on the frontend.
	 *
	 * @param array $items retrive tweets.
	 * @param array $settings retrive all settings.
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function twitter_feed_hashtag_display( $items, $settings ) {

		$this->get_carousel_attr();
		$twitter_username = $settings['uael_twitter_feed_hashtag_name'];
		$mobile_column    = ( isset( $settings['uael_twitter_feed_col_mobile'] ) ) ? 'uael-twitter-feed-mobile-' . $settings['uael_twitter_feed_col_mobile'] . ' ' : '';
		$tablet_column    = ( isset( $settings['uael_twitter_feed_col_tablet'] ) ) ? 'uael-twitter-feed-tablet-' . $settings['uael_twitter_feed_col_tablet'] . ' ' : '';
		$column           = ( isset( $settings['uael_twitter_feed_col'] ) ) ? 'uael-twitter-feed-' . $settings['uael_twitter_feed_col'] . ' ' : '';

		$this->warning( $settings, $twitter_username );
		?>
			<div class="uael-twitter-feed">
				<?php
				$this->add_render_attribute(
					'carousel-wrap',
					array(
						'class' => 'uael-twitter-feed-' . $settings['uael_twitter_feed_layout'] . ' ' . $mobile_column . $tablet_column . $column,
					)
				);
				?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'carousel-wrap' ) ); ?>>
					<?php
					if ( isset( $items ) && is_array( $items ) ) {

						foreach ( $items as $item ) {
							?>
								<div class="uael-twitter-feed-item">
									<div class="uael-twitter-feed-item-inner">
										<div class="uael-twitter-feed-content">
											<?php
												$this->twitter_feed_header( $item, $settings );
												$this->twitter_feed_content( $item, $settings );
												$this->twitter_feed_footer( $item, $settings );
											?>
										</div>
									</div>
								</div>
								<?php
						}
					}
					?>
				</div>
			</div>
		<?php
	}

	/**
	 * Render Twitter Feed output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.36.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
		if ( isset( $settings['uael_twitter_feed_username'] ) && empty( $settings['uael_twitter_feed_hashtag_name'] ) ) {
			$items = $this->twitter_feed_render_items( $settings );
			if ( 'timeline' === $settings['uael_twitter_feed_layout'] && ! empty( $settings['uael_twitter_feed_username'] ) ) {
				echo '<div class="uael-twitter-profile-feed">';
				$this->twitter_feed_timeline( $items, $settings );
				$this->twitter_feed_username_display( $items, $settings );
				echo '</div>';
			} else {
				$this->twitter_feed_username_display( $items, $settings );
			}
		} else {
			$items = $this->twitter_feed_render_hashtag_items( $settings );
			if ( 'timeline' === $settings['uael_twitter_feed_layout'] && ! empty( $settings['uael_twitter_feed_hashtag_name'] ) ) {
				echo '<div class="uael-twitter-profile-feed">';
				$this->twitter_feed_timeline( $items, $settings );
				$this->twitter_feed_hashtag_display( $items, $settings );
				echo '</div>';
			} else {
				$this->twitter_feed_hashtag_display( $items, $settings );
			}
		}
	}
}
