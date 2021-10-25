<?php
/**
 * Class: Premium_Twitter_Feed
 * Name: Twitter Feed
 * Slug: premium-twitter-feed
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;


// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Twitter_Feed
 */
class Premium_Twitter_Feed extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-twitter-feed';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return sprintf( '%1$s %2$s', Helper_Functions::get_prefix(), __( 'Twitter Feed', 'premium-addons-pro' ) );
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
		return 'pa-pro-twitter-feed';
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
		return array( 'tweet', 'profile', 'account', 'tag', 'social' );
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
			'font-awesome-5-all',
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
			'codebird',
			'social-dot',
			'jquery-socialfeed',
			'isotope-js',
			'imagesloaded',
			'pa-slick',
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
		return 'https://www.youtube.com/watch?v=wsurRDuR6pg&list=PLLpZVOYpMtTArB4hrlpSnDJB36D2sdoTv';
	}

	/**
	 * Register Twitter Feed controls.
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
			'consumer_key',
			array(
				'label'       => __( 'Consumer Key', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => 'wwC72W809xRKd9ySwUzXzjkmS',
				'description' => '<a href="https://apps.twitter.com/" target="_blank">Get Consumer Key </a>by creating a new app or selecting an existing app ',
			)
		);

		$this->add_control(
			'consumer_secret',
			array(
				'label'       => __( 'Consumer Secret Key', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => 'rn54hBqxjve2CWOtZqwJigT3F5OEvrriK2XAcqoQVohzr2UA8h',
				'description' => '<a href="https://apps.twitter.com/" target="_blank">Get Consumer Secret Key </a>by creating a new app or selecting an existing app',
				'separator'   => 'after',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'account_settings_section',
			array(
				'label' => __( 'Accounts', 'premium-addons-pro' ),
			)
		);

		$acc_repeater = new Repeater();

		$acc_repeater->add_control(
			'account_id',
			array(
				'label'       => __( 'Account ID', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'Account ID is prefixed by @', 'premium-addons-pro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'account_names',
			array(
				'label'         => __( 'Accounts', 'premium-addons-pro' ),
				'type'          => Controls_Manager::REPEATER,
				'default'       => array(
					array(
						'account_id' => '@leap13themes',
					),
				),
				'fields'        => $acc_repeater->get_controls(),
				'title_field'   => '{{{ account_id }}}',
				'prevent_empty' => false,
			)
		);

		$hash_repeater = new Repeater();

		$hash_repeater->add_control(
			'hashtag',
			array(
				'label'       => __( 'Hashtag', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Hashtag is prefixed by a #', 'premium-addons-pro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'hashtags',
			array(
				'label'         => __( 'Hashtags', 'premium-addons-pro' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $hash_repeater->get_controls(),
				'title_field'   => '{{{ hashtag }}}',
				'prevent_empty' => false,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'tweets_layout_settings',
			array(
				'label' => __( 'Layout', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'layout_style',
			array(
				'label'       => __( 'Style', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Choose the layout style for the tweets', 'premium-addons-pro' ),
				'options'     => array(
					'list'    => __( 'List', 'premium-addons-pro' ),
					'masonry' => __( 'Grid', 'premium-addons-pro' ),
				),
				'default'     => 'list',
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
				'label'           => __( 'Tweets/Row', 'premium-addons-pro' ),
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
			'profile_header_settings',
			array(
				'label' => __( 'Profile Header', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'header_enable',
			array(
				'label'        => __( 'Enable Profile Header', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'prefix_class' => 'premium-twitter-profile-',
			)
		);

		$this->add_control(
			'fill_colors_notice',
			array(
				'raw'             => '<strong>' . __( 'Please note!', 'premium-addons-pro' ) . '</strong> ' . __( 'Profile Header will Show data from the first account only.', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'render_type'     => 'ui',
				'condition'       => array(
					'header_enable' => 'yes',
					'layout_style'  => 'masonry',
				),
			)
		);

		$this->add_control(
			'header_image_section',
			array(
				'label'     => __( 'Show Cover Photo', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'block' => __( 'Show', 'premium-addons-pro' ),
					'none'  => __( 'Hide', 'premium-addons-pro' ),
				),
				'default'   => 'block',
				'condition' => array(
					'header_enable' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-header-banner'   => 'display: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'cover_image_height',
			array(
				'label'      => __( 'Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
					'em' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twitter-header-banner img'    => 'height: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'header_enable'        => 'yes',
					'header_image_section' => 'block',
				),
			)
		);

		$this->add_control(
			'author_section',
			array(
				'label'     => __( 'Show Author Details', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'block' => __( 'Show', 'premium-addons-pro' ),
					'none'  => __( 'Hide', 'premium-addons-pro' ),
				),
				'default'   => 'block',
				'condition' => array(
					'header_enable' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-header-link-wrapper , {{WRAPPER}} .premium-twitter-img-container , {{WRAPPER}} .premium-twitter-feed-header-statistics '   => 'display: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'author_display_section',
			array(
				'label'        => __( 'Display', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'row'    => __( 'Inline', 'premium-addons-pro' ),
					'column' => __( 'Block', 'premium-addons-pro' ),
				),
				'default'      => 'row',
				'selectors'    => array(
					'{{WRAPPER}} .premium-twitter-feed-header-user'   => 'flex-direction: {{VALUE}}',
				),
				'prefix_class' => 'premium-twitter-author-',
				'condition'    => array(
					'author_section' => 'block',
					'header_enable'  => 'yes',
				),
			)
		);

		$this->add_control(
			'statistics_section',
			array(
				'label'     => __( 'Show User Data', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'flex' => __( 'Show', 'premium-addons-pro' ),
					'none' => __( 'Hide', 'premium-addons-pro' ),
				),
				'default'   => 'flex',
				'condition' => array(
					'header_enable' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-feed-header-statistics'   => 'display: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'follow_button_section',
			array(
				'label'     => __( 'Show Follow Button', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'block' => __( 'Show', 'premium-addons-pro' ),
					'none'  => __( 'Hide', 'premium-addons-pro' ),
				),
				'default'   => 'block',
				'condition' => array(
					'header_enable' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-feed-follow'   => 'display: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'follow_ver_pos',
			array(
				'label'     => __( 'Vertical Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'fa fa-long-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Middle', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'fa fa-long-arrow-down',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-feed-follow'   => 'align-self: {{VALUE}}',
				),
				'toggle'    => false,
				'condition' => array(
					'follow_button_section' => 'block',
					'header_enable'         => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'layout_settings',
			array(
				'label' => __( 'Tweets Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'tweets_number',
			array(
				'label'       => __( 'Tweets/Account', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'How many tweets will be shown for each account, default is 2', 'premium-addons-pro' ),
				'default'     => 2,
			)
		);

		$this->add_control(
			'tweets_length',
			array(
				'label'   => __( 'Tweet Length', 'premium-addons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 150,
			)
		);

		$this->add_control(
			'tweets_media',
			array(
				'label'     => __( 'Show Tweet Media', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => 'Show',
				'label_off' => 'Hide',
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'      => __( 'Tweet Media Height', 'premium-addons-pro' ),
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
					'tweets_media' => 'yes',
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
				'separator' => 'after',
				'selectors' => array(
					'{{WRAPPER}} .premium-social-feed-element img.attachment' => 'object-fit: {{VALUE}}',
				),
				'condition' => array(
					'tweets_media' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'tweets_avatar',
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

		$this->add_responsive_control(
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

		$this->add_responsive_control(
			'tweets_date',
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

		$this->add_responsive_control(
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

		$this->add_responsive_control(
			'tweets_read',
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
					'tweets_read' => 'inline-block',
				),
			)
		);

		$this->add_responsive_control(
			'tweets_icon',
			array(
				'label'     => __( 'Show Twitter Icon', 'premium-addons-pro' ),
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

		$this->add_responsive_control(
			'action_section',
			array(
				'label'     => __( 'Show Tweet Information', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'inline-flex' => __( 'Show', 'premium-addons-pro' ),
					'none'        => __( 'Hide', 'premium-addons-pro' ),
				),
				'default'   => 'none',
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-comments-box'   => 'display: {{VALUE}}',
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
					'{{WRAPPER}} .premium-twitter-feed-wrapper a.carousel-arrow.carousel-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-twitter-feed-wrapper a.carousel-arrow.carousel-prev' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'header_image_style',
			array(
				'label'     => __( 'Header Profile Picture', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'author_section' => 'block',
					'header_enable'  => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'heading_image_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 400,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twitter-img-container .premium-twitter-header-img' => 'width: {{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'header_image_border',
				'selector' => '{{WRAPPER}} .premium-twitter-img-container .premium-twitter-header-img',
			)
		);

		$this->add_control(
			'header_image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twitter-img-container .premium-twitter-header-img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'header_image_shadow',
				'selector' => '{{WRAPPER}} .premium-twitter-img-container .premium-twitter-header-img',
			)
		);

		$this->add_responsive_control(
			'header_image_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twitter-img-container .premium-twitter-header-img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'author_name_style',
			array(
				'label'     => __( 'Author Header', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'author_section' => 'block',
					'header_enable'  => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'a_name_typography',
				'label'    => __( 'Name Typography', 'premium-addons-pro' ),
				'selector' => '{{WRAPPER}} .premium-twitter-header-link .premium-twitter-feed-name',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'user_typography',
				'label'    => __( 'Username Typography', 'premium-addons-pro' ),
				'selector' => '{{WRAPPER}} .premium-twitter-header-link .premium-twitter-screen-name',
			)
		);

		$this->start_controls_tabs( 'author_style_tabs' );

		$this->start_controls_tab(
			'author_header_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'author_name_color',
			array(
				'label'     => __( 'Name Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-header-link .premium-twitter-feed-name' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'author_username_color',
			array(
				'label'     => __( 'Username Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_3,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-header-link .premium-twitter-screen-name' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'author_header_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'author_name_color_hover',
			array(
				'label'     => __( 'Name Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-header-link .premium-twitter-feed-name:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'author_username_color_hover',
			array(
				'label'     => __( 'Username Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-header-link .premium-twitter-screen-name:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'space_size',
			array(
				'label'       => __( 'Space Between Names', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%' ),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-twitter-screen-name' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_auther_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twitter-header-link-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'statics_style',
			array(
				'label'     => __( 'User Data', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'statistics_section' => 'flex',
					'header_enable'      => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'statics_heading_typography',
				'selector' => '{{WRAPPER}} .premium-twitter-header-count',
			)
		);

		$this->add_control(
			'statics_text_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-header-count span:last-of-type' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'count_color',
			array(
				'label'     => __( 'Count Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-header-count span:first-of-type' => 'color: {{VALUE}};',
				),
			)
		);

		$data_spacing = is_rtl() ? 'left' : 'right';

		$this->add_responsive_control(
			'space_data',
			array(
				'label'       => __( 'Space Between Data', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%' ),
				'range'       => array(
					'em' => array(
						'min' => 0,
						'max' => 25,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-twitter-header-count a span:first-of-type' => 'margin-' . $data_spacing . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'statsic_field_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twitter-header-count' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'follow_button_style',
			array(
				'label'     => __( 'Follow Button', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'follow_button_section' => 'block',
					'header_enable'         => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'btn_typography',
				'label'    => __( 'Typography', 'premium-addons-pro' ),
				'selector' => '{{WRAPPER}} .premium-twitter-feed-follow a',
			)
		);

		$this->start_controls_tabs( 'btn_tabs_style' );

		$this->start_controls_tab(
			'btn_follow_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'btn_text_header_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-feed-follow a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_text_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-feed-follow a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'btn_follow_box_shadow',
				'selector' => '{{WRAPPER}} .premium-twitter-feed-follow a',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'btn_tab_button_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'btn_hover_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-feed-follow a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_text_background_color_hover',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-feed-follow a:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'btn_text_box_shadow_hover',
				'label'    => __( 'Box Shadow', 'premium-addons-pro' ),
				'selector' => '{{WRAPPER}} .premium-twitter-feed-follow a:hover',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'btn_shadow_border',
				'label'    => __( 'Border', 'premium-addons-pro' ),
				'selector' => '{{WRAPPER}} .premium-twitter-feed-follow a',
			)
		);

		$this->add_control(
			'btn_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twitter-feed-follow a' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'btn_text_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twitter-feed-follow a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'btn_text_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twitter-feed-follow a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'header_container_style',
			array(
				'label'     => __( 'Header Container', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'header_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'header_container_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-user-cover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'header_container_box_border',
				'selector' => '{{WRAPPER}} .premium-twitter-user-cover',
			)
		);

		$this->add_control(
			'header_container_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twitter-user-cover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'header_container_box_shadow',
				'selector' => '{{WRAPPER}} .premium-twitter-user-cover',
			)
		);

		$this->add_responsive_control(
			'header_container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twitter-user-cover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twitter-user-cover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'tweet_box_style',
			array(
				'label' => __( 'Tweet Box', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tweet_box' );

		$this->start_controls_tab(
			'tweet_box_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'tweet_box_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-social-feed-element' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'tweet_box_border',
				'selector' => '{{WRAPPER}} .premium-social-feed-element',
			)
		);

		$this->add_control(
			'tweet_box_border_radius',
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
				'name'     => 'tweet_box_shadow',
				'selector' => '{{WRAPPER}} .premium-social-feed-element',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tweet_box_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'tweet_box_background_hover',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-social-feed-element:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'tweet_box_border_hover',
				'selector' => '{{WRAPPER}} .premium-social-feed-element:hover',
			)
		);

		$this->add_control(
			'tweet_box_border_radius_hover',
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
				'name'     => 'tweet_box_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-social-feed-element:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'tweet_box_margin',
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
			'tweet_box_padding',
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
				'name'      => 'twitter_feed_content_typography',
				'selector'  => '{{WRAPPER}} .premium-feed-element-text',
				'condition' => array(
					'show_content' => 'block',
				),
			)
		);

		$this->add_control(
			'content_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-feed-element-text' => 'color: {{VALUE}};',
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
			'twitter_feed_content_margin',
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
			'twitter_feed_readmore_heading',
			array(
				'label'     => __( 'Read More', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'tweets_read' => 'inline-block',
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
					'tweets_read' => 'inline-block',
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
					'tweets_read' => 'inline-block',
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
					'tweets_read' => 'inline-block',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'twitter_feed_read_more_typography',
				'selector'  => '{{WRAPPER}} .premium-feed-element-read-more',
				'condition' => array(
					'tweets_read' => 'inline-block',
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
					'tweets_read' => 'inline-block',
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
					'tweets_read' => 'inline-block',
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
					'tweets_avatar' => 'block',
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
				'label'     => __( 'Twitter Icon', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'tweets_icon' => 'inline-block',
				),
			)
		);

		$this->add_control(
			'twitter_icon_color',
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
			'twitter_icon_size',
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
					'tweets_date' => 'block',
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'date_typography',
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
			'action_style',
			array(
				'label'     => __( 'Tweet Information', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'action_section' => 'inline-flex',
				),
			)
		);

		$this->add_control(
			'action_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-comments-field ' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-twitter-comments-field svg ' => 'fill: {{VALUE}};',

				),
			)
		);

		$this->add_control(
			'action_icon_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-comments-field:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-twitter-comments-field:hover svg ' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'action_box_typography',
				'selector' => '{{WRAPPER}} .premium-twitter-comments-field',
			)
		);

		$this->add_responsive_control(
			'action_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twitter-comments-field span' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-twitter-comments-field svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'space_action',
			array(
				'label'       => __( 'Space Between Data', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%' ),
				'range'       => array(
					'em' => array(
						'min' => 0,
						'max' => 25,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-twitter-comments-field' => 'margin-' . $data_spacing . ': {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'action_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twitter-comments-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'label'     => __( 'Arrow Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-feed-wrapper .slick-arrow' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .premium-twitter-feed-wrapper .slick-arrow i' => 'font-size: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-twitter-feed-wrapper .slick-arrow' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .premium-twitter-feed-wrapper .slick-arrow' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-twitter-feed-wrapper .slick-arrow' => 'padding: {{SIZE}}{{UNIT}};',
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
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-twitter-feed-wrapper' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'container_box_border',
				'selector' => '{{WRAPPER}} .premium-twitter-feed-wrapper',
			)
		);

		$this->add_control(
			'container_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twitter-feed-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'container_box_shadow',
				'selector' => '{{WRAPPER}} .premium-twitter-feed-wrapper',
			)
		);

		$this->add_responsive_control(
			'container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twitter-feed-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-twitter-feed-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Twitter Feed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( empty( $settings['consumer_key'] ) || empty( $settings['consumer_secret'] ) ) { ?>
			<div class="premium-error-notice">
				<?php echo esc_html( __( 'Please fill the required fields: Consumer Key & Consumer Secret', 'premium-addons-pro' ) ); ?>
			</div>
			<?php
			return; }

		$layout_class = 'list' === $settings['layout_style'] ? 'list-layout' : 'grid-layout';

		$layout = sprintf( '%s-template.php', str_replace( '-layout', '', $layout_class ) );

		$direction = $settings['direction'];

		$accounts = array();

		foreach ( $settings['account_names'] as $item ) {

			if ( ! empty( $item['account_id'] ) ) {

				array_push( $accounts, $item['account_id'] );

			}
		}

		foreach ( $settings['hashtags'] as $item ) {

			if ( ! empty( $item['hashtag'] ) ) {

				array_push( $accounts, $item['hashtag'] );

			}
		}

		if ( 'yes' === $settings['feed_carousel'] ) {

			$this->add_render_attribute(
				'twitter',
				array(
					'data-carousel' => $settings['feed_carousel'],
					'data-play'     => $settings['carousel_play'],
					'data-speed'    => $settings['carousel_autoplay_speed'],
					'data-rtl'      => is_rtl(),
				)
			);

		}

		$twitter_settings = array(
			'accounts'  => $accounts,
			'limit'     => $settings['tweets_number'],
			'consKey'   => $settings['consumer_key'],
			'consSec'   => $settings['consumer_secret'],
			'length'    => $settings['tweets_length'],
			'showMedia' => $settings['tweets_media'],
			'layout'    => $layout_class,
			'readMore'  => esc_html( $settings['read_text'] ),
			'template'  => plugins_url( '/templates/', __FILE__ ) . $layout,
		);

		if ( 'yes' === $settings['equal_height_switcher'] ) {

			$this->add_render_attribute( 'twitter-inner', 'class', 'premium-social-feed-even' );

			$twitter_settings['even'] = true;

		}

		$this->add_render_attribute(
			'twitter',
			array(
				'class'         => array(
					'premium-twitter-feed-wrapper',
					$direction,
				),
				'data-settings' => wp_json_encode( $twitter_settings ),
			)
		);

		$this->add_render_attribute(
			'twitter-inner',
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

		$this->add_render_attribute( 'twitter', 'data-col', $feed_number );

		?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'twitter' ) ); ?>>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'twitter-inner' ) ); ?>></div>
				<div class="premium-loading-feed">
					<div class="premium-loader"></div>
				</div>
			</div>
		<?php
	}

}
