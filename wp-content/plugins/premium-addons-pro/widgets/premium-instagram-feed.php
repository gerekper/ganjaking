<?php
/**
 * Class: Premium_Instagram_Feed
 * Name: Instagram Feed
 * Slug: premium-addon-instagram-feed
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
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;

// PremiumAddons Classes.
use PremiumAddons\Includes\Premium_Template_Tags;
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Instagram_Feed
 */
class Premium_Instagram_Feed extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-addon-instagram-feed';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return sprintf( '%1$s %2$s', Helper_Functions::get_prefix(), __( 'Instagram Feed', 'premium-addons-pro' ) );
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
			'pa-prettyphoto',
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
			'imagesloaded',
			'prettyPhoto-js',
			'isotope-js',
			'pa-instafeed',
			'pa-slick',
			'premium-pro',
		);
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_icon() {
		return 'pa-pro-instagram-feed';
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.0.0
	 * @access public
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
		return array( 'profile', 'account', 'post', 'social' );
	}

	/**
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://premiumaddons.com/support';
	}

	/**
	 * Register Instagram Feed controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_instagram_feed_general_settings_section',
			array(
				'label' => __( 'Access Credentials', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'deprecate_notice',
			array(
				'raw'             => __( '<b>Important:</b> Instagram API has been deprecated, so you will need to migrate to the new API handled by Facebook through the login button below. For further information, please check this <a href="https://www.instagram.com/developer/" target="_blank">page</a>', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
			)
		);

		$this->add_control(
			'api_version',
			array(
				'label'       => __( 'API Version:', 'premium-addons-pro' ),
				'type'        => Controls_Manager::HIDDEN,
				'options'     => array(
					'new' => __( 'Facebook New Instagram API', 'premium-addons-pro' ),
				),
				'label_block' => true,
				'default'     => 'new',
			)
		);

		$this->add_control(
			'instagram_login',
			array(
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<form onsubmit="connectInstagramInit(this);" action="javascript:void(0);" data-type="reviews"><input type="submit" value="Log in with Facebook" class="elementor-button" style="background-color: #3b5998; color: #fff;"></form>',
				'label_block' => true,
				'condition'   => array(
					'api_version' => 'new',
				),
			)
		);

		$this->add_control(
			'premium_instagram_feed_client_access_token',
			array(
				'label'       => __( 'Access Token', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => '2075884021.1677ed0.2fd28d5d3abf45d4a80534bee8376f4c',
				'label_block' => false,
				'description' => 'Get your access token from <a href="http://www.jetseotools.com/instagram-access-token/" target="_blank">here</a>',
				'condition'   => array(
					'api_version' => 'old',
				),
			)
		);

		$this->add_control(
			'new_accesstoken',
			array(
				'label'       => __( 'Access Token', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'api_version' => 'new',
				),
			)
		);

		$this->add_control(
			'new_api_notice',
			array(
				'raw'             => __( 'The amount of information given about Instagram media is greatly reduced in the new API', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'api_version' => 'new',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_instagram_feed_query_section',
			array(
				'label' => __( 'Queries', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_instagram_feed_tag_name',
			array(
				'label'       => __( 'Filter by Hashtags', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'You can separate tags by a comma, for example: sport,football,tennis', 'premium-addons-pro' ),
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_instagram_feed_link',
			array(
				'label'       => __( 'Enable Redirection', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Redirect to Photo Link on Instgram', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_instagram_feed_new_tab',
			array(
				'label'     => __( 'Open in a New Tab', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_instagram_feed_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_instagram_feed_popup',
			array(
				'label'       => __( 'Lightbox', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Modal image works only on the frontend', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_instagram_feed_link!' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_instagram_feed_popup_theme',
			array(
				'label'     => __( 'Lightbox Theme', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'pp_default'    => __( 'Default', 'premium-addons-pro' ),
					'light_rounded' => __( 'Light Rounded', 'premium-addons-pro' ),
					'dark_rounded'  => __( 'Dark Rounded', 'premium-addons-pro' ),
					'light_square'  => __( 'Light Square', 'premium-addons-pro' ),
					'dark_square'   => __( 'Dark Square', 'premium-addons-pro' ),
					'facebook'      => __( 'Facebook', 'premium-addons-pro' ),
				),
				'default'   => 'pp_default',
				'condition' => array(
					'premium_instagram_feed_link!' => 'yes',
					'premium_instagram_feed_popup' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_instagram_feed_show_likes',
			array(
				'label'     => __( 'Show Likes', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'api_version' => 'old',
				),
			)
		);

		$this->add_control(
			'premium_instagram_feed_show_comments',
			array(
				'label'     => __( 'Show Comments', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'api_version' => 'old',
				),
			)
		);

		$this->add_control(
			'premium_instagram_feed_show_caption',
			array(
				'label' => __( 'Show Caption', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_instagram_feed_caption_number',
			array(
				'label'     => __( 'Maximum Words Number', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'condition' => array(
					'premium_instagram_feed_show_caption' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_videos',
			array(
				'label'        => __( 'Show Videos On Click', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'premium_instagram_feed_link!'  => 'yes',
					'premium_instagram_feed_popup!' => 'yes',
					'api_version'                   => 'new',
				),
			)
		);

		$this->add_control(
			'premium_instagram_feed_share',
			array(
				'label' => __( 'Share Button', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_instagram_feed_layout_settings_section',
			array(
				'label' => __( 'Layout', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_instagram_feed_img_number',
			array(
				'label'   => __( 'Maximum Images Number', 'premium-addons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 0,
			)
		);

		$this->add_control(
			'premium_instagram_feed_masonry',
			array(
				'label'   => __( 'Masonry', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_responsive_control(
			'premium_instgram_feed_image_height',
			array(
				'label'      => __( 'Image Height', 'premium-addons-pro' ),
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
				'default'    => array(
					'size' => 300,
					'unit' => 'px',
				),
				'condition'  => array(
					'premium_instagram_feed_masonry!' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-img-wrap img' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_instagram_feed_column_number',
			array(
				'label'           => __( 'Images per Row', 'premium-addons-pro' ),
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
				'selectors'       => array(
					'{{WRAPPER}} .premium-insta-feed' => 'width: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'premium_instagram_feed_image_hover',
			array(
				'label'       => __( 'Hover Image Effect', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'none'    => __( 'None', 'premium-addons-pro' ),
					'zoomin'  => __( 'Zoom In', 'premium-addons-pro' ),
					'zoomout' => __( 'Zoom Out', 'premium-addons-pro' ),
					'scale'   => __( 'Scale', 'premium-addons-pro' ),
					'gray'    => __( 'Grayscale', 'premium-addons-pro' ),
					'blur'    => __( 'Blur', 'premium-addons-pro' ),
					'sepia'   => __( 'Sepia', 'premium-addons-pro' ),
					'bright'  => __( 'Brightness', 'premium-addons-pro' ),
					'trans'   => __( 'Translate', 'premium-addons-pro' ),
				),
				'default'     => 'zoomin',
				'label_block' => true,
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
					'{{WRAPPER}} .premium-instafeed-container a.carousel-arrow.carousel-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-instafeed-container a.carousel-arrow.carousel-prev' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'account_info',
			array(
				'label' => __( 'Profile Header', 'premium-addons-pro' ),
			)
		);

		// Account info controls.
		$this->add_control(
			'account_info_switcher',
			array(
				'label' => __( 'Profile Header', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_instafeed_logo_switcher',
			array(
				'label'     => __( 'Show Instagram Logo', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'account_info_switcher' => 'yes',
				),
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'premium_instafeed_user_name',
			array(
				'label'       => __( 'Username', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'EX: leap13themes', 'premium-addons-pro' ),
				'description' => __( 'account username is required to show these options successfully.', 'premium-addons-pro' ),
				'condition'   => array(
					'account_info_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'instafeed_userInfo_main_heading',
			array(
				'label'     => __( 'User Info', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'account_info_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_instafeed_profile_pic',
			array(
				'label'     => __( 'Show Profile Picture', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'account_info_switcher' => 'yes',
				),
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'premium_instafeed_username',
			array(
				'label'     => __( 'Show Username', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'account_info_switcher' => 'yes',
				),
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'premium_instafeed_verified',
			array(
				'label'       => __( 'Show Verifiy Icon', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'condition'   => array(
					'account_info_switcher'      => 'yes',
					'premium_instafeed_username' => 'yes',
				),
				'description' => __( 'Displayed only if the account is verified by Instagram.', 'premium-addons-pro' ),
				'default'     => 'yes',
			)
		);

		$this->add_control(
			'premium_instafeed_bio',
			array(
				'label'     => __( 'Show Biography', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'account_info_switcher' => 'yes',
				),
				'default'   => 'yes',
			)
		);

		$info_terms = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'name'  => 'premium_instafeed_profile_pic',
					'value' => 'yes',
				),
				array(
					'name'  => 'premium_instafeed_username',
					'value' => 'yes',
				),
				array(
					'name'  => 'premium_instafeed_bio',
					'value' => 'yes',
				),
			),
		);

		$this->add_control(
			'instafeed_user_info_display',
			array(
				'label'        => __( 'Display', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'row' => __( 'Row', 'premium-addons-pro' ),
					'col' => __( 'Column', 'premium-addons-pro' ),
				),
				'default'      => 'row',
				'prefix_class' => 'premium-insta-user-',
				'conditions'   => array(
					'terms' => array(
						array(
							'name'  => 'account_info_switcher',
							'value' => 'yes',
						),
						$info_terms,
					),
				),
			)
		);

		$this->add_responsive_control(
			'instafeed_user_info_alignment',
			array(
				'label'      => __( 'Alignment', 'premium-addons-pro' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'toggle'     => false,
				'selectors'  => array(
					'{{WRAPPER}}.premium-insta-user-row .premium-instafeed-header-upper' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}}.premium-insta-user-col .premium-instafeed-header-upper' => 'align-items: {{VALUE}};',
					'{{WRAPPER}}.premium-insta-user-col .premium-instafeed-user-activity' => 'align-items: {{VALUE}};',
				),
				'default'    => 'flex-start',
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'account_info_switcher',
							'value' => 'yes',
						),
						$info_terms,
					),
				),
			)
		);

		$this->add_responsive_control(
			'instafeed_user_info_Valignment',
			array(
				'label'      => __( 'Vertical Alignment', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
					'flex-start' => array(
						'title' => __( 'Top', 'premium-addons-for-elementor' ),
						'icon'  => 'fa fa-long-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'fa fa-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-for-elementor' ),
						'icon'  => 'fa fa-long-arrow-down',
					),
				),
				'default'    => 'center',
				'toggle'     => false,
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'premium_instafeed_profile_pic',
							'value' => 'yes',
						),
						array(
							'name'  => 'instafeed_user_info_display',
							'value' => 'row',
						),
						array(
							'name'  => 'account_info_switcher',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'premium_instafeed_username',
									'value' => 'yes',
								),
								array(
									'name'  => 'premium_instafeed_bio',
									'value' => 'yes',
								),
							),
						),

					),
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-insta-user-row .premium-instafeed-header-upper' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'instafeed_user_info_text_alignment',
			array(
				'label'      => __( 'Text Alignment', 'premium-addons-pro' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
					'start'  => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'end'    => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'toggle'     => false,
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-header-user-wrapper' => 'align-items: {{VALUE}};',
					'{{WRAPPER}} .premium-instafeed-user-activity' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .premium-instafeed-bio' => 'text-align: {{VALUE}};',

				),
				'default'    => 'start',
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'account_info_switcher',
							'value' => 'yes',
						),
						$info_terms,
					),
				),
			)
		);

		$this->add_control(
			'instafeed_userData_main_heading',
			array(
				'label'     => __( 'User Data', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'account_info_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_instafeed_followers',
			array(
				'label'     => __( 'Show Number Of Followers', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'account_info_switcher' => 'yes',
				),
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'premium_instafeed_following',
			array(
				'label'     => __( 'Show Number Of Followings', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'account_info_switcher' => 'yes',
				),
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'premium_instafeed_posts',
			array(
				'label'     => __( 'Show Number Of Posts', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'account_info_switcher' => 'yes',
				),
				'default'   => 'yes',
			)
		);

		$terms = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'name'  => 'premium_instafeed_followers',
					'value' => 'yes',
				),
				array(
					'name'  => 'premium_instafeed_following',
					'value' => 'yes',
				),
				array(
					'name'  => 'premium_instafeed_posts',
					'value' => 'yes',
				),
			),
		);

		$this->add_control(
			'instafeed_user_activity_display',
			array(
				'label'      => __( 'Display', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SELECT,
				'options'    => array(
					'inline' => __( 'Inline', 'premium-addons-pro' ),
					'block'  => __( 'Block', 'premium-addons-pro' ),
				),
				'default'    => 'inline',
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-user-activity-item span' => 'display: {{VALUE}};',
				),
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'account_info_switcher',
							'value' => 'yes',
						),
						$terms,
					),
				),
			)
		);

		$this->add_control(
			'premium_instafeed_reload',
			array(
				'label'     => __( 'Reload Data Once Every', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'hour'  => __( 'Hour', 'premium-addons-pro' ),
					'day'   => __( 'Day', 'premium-addons-pro' ),
					'week'  => __( 'Week', 'premium-addons-pro' ),
					'month' => __( 'Month', 'premium-addons-pro' ),
					'year'  => __( 'Year', 'premium-addons-pro' ),
				),
				'default'   => 'day',
				'condition' => array(
					'account_info_switcher' => 'yes',
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

		$docs = array(
			'https://premiumaddons.com/docs/instagram-feed-widget-tutorial' => __( 'Getting started »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/how-to-migrate-instagram-feed-widget-from-the-old-api-to-the-new-api' => __( 'How to migrate Instagram Feed widget to the New API »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/how-to-filter-images-by-hashtags-in-instagram-feed-widget' => __( 'How to Filter Images By Hashtags »', 'premium-addons-pro' ),
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

		$icon_spacing = is_rtl() ? 'left' : 'right';

		$this->start_controls_section(
			'premium_instgram_feed_photo_box_style',
			array(
				'label' => __( 'Photo Box', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'premium_instgram_feed_tweet_box' );

		$this->start_controls_tab(
			'premium_instgram_feed_photo_box_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_instgram_feed_photo_box_border',
				'selector' => '{{WRAPPER}} .premium-insta-img-wrap',
			)
		);

		$this->add_control(
			'premium_instgram_feed_photo_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-img-wrap' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_instgram_feed_photo_box_shadow',
				'selector' => '{{WRAPPER}} .premium-insta-img-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .premium-insta-img-wrap img',
			)
		);

		$this->add_responsive_control(
			'premium_instgram_feed_photo_box_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-img-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_instgram_feed_photo_box_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-img-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_instgram_feed_photo_box_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_instgram_feed_overlay_background',
			array(
				'label'     => __( 'Overlay Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-insta-info-wrap' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_instgram_feed_photo_box_border_hover',
				'selector' => '{{WRAPPER}} .premium-insta-feed-wrap:hover .premium-insta-img-wrap',
			)
		);

		$this->add_control(
			'premium_instgram_feed_photo_box_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-feed-wrap:hover .premium-insta-img-wrap' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_instgram_feed_photo_box_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-insta-feed-wrap:hover .premium-insta-img-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .premium-insta-feed-wrap:hover .premium-insta-img-wrap img',
			)
		);

		$this->add_responsive_control(
			'premium_instgram_feed_photo_box_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-feed-wrap:hover .premium-insta-img-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		/*End Tweet Box Section*/
		$this->end_controls_section();

		$this->start_controls_section(
			'premium_instgram_feed_photo_likes_style',
			array(
				'label'     => __( 'Likes', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_instagram_feed_show_likes' => 'yes',
					'api_version'                       => 'old',
				),
			)
		);

		$this->start_controls_tabs( 'premium_instgram_feed_likes' );

		$this->start_controls_tab(
			'premium_instgram_feed_likes_icon',
			array(
				'label' => __( 'Icon', 'premium-addons-pro' ),
			)
		);

		/*Likes Icon Color*/
		$this->add_control(
			'premium_instgram_feed_likes_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-insta-heart' => 'color: {{VALUE}};',
				),
			)
		);

		/*Likes Icon Size*/
		$this->add_responsive_control(
			'premium_instgram_feed_likes_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-heart' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_instgram_feed_likes_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-insta-heart' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_instgram_feed_likes_border',
				'selector' => '{{WRAPPER}} .premium-insta-heart',
			)
		);

		/*Container Border Radius*/
		$this->add_control(
			'premium_instgram_feed_likes_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-heart' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		/*Container Box Shadow*/
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_instgram_feed_likes_shadow',
				'selector' => '{{WRAPPER}} .premium-insta-heart',
			)
		);

		$this->add_responsive_control(
			'premium_instgram_feed_likes_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-heart' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		/*Container Padding*/
		$this->add_responsive_control(
			'premium_instgram_feed_likes_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-heart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_instgram_feed_likes_number',
			array(
				'label' => __( 'Number', 'premium-addons-pro' ),
			)
		);

		/*Likes Number Color*/
		$this->add_control(
			'premium_instgram_feed_likes_number_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-insta-likes' => 'color: {{VALUE}};',
				),
			)
		);

		/*Likes Number Typography*/
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_instgram_feed_likes_number_type',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-insta-likes',
			)
		);

		$this->add_control(
			'premium_instgram_feed_likes_number_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  .premium-insta-likes' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_instgram_feed_likes_number_border',
				'selector' => '{{WRAPPER}} .premium-insta-likes',
			)
		);

		/*Container Border Radius*/
		$this->add_control(
			'premium_instgram_feed_likes_number_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-likes' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		/*Container Box Shadow*/
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_instgram_feed_likes_number_shadow',
				'selector' => '{{WRAPPER}} .premium-insta-likes',
			)
		);

		$this->add_responsive_control(
			'premium_instgram_feed_likes_number_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-likes' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		/*Container Padding*/
		$this->add_responsive_control(
			'premium_instgram_feed_likes_number_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-likes' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_instgram_feed_photo_comments_style',
			array(
				'label'     => __( 'Comments', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_instagram_feed_show_comments' => 'yes',
					'api_version'                          => 'old',
				),
			)
		);

		$this->start_controls_tabs( 'premium_instgram_feed_comments' );

		$this->start_controls_tab(
			'premium_instgram_feed_comments_icon',
			array(
				'label' => __( 'Icon', 'premium-addons-pro' ),
			)
		);

		/*Likes Icon Color*/
		$this->add_control(
			'premium_instgram_feed_comment_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-insta-comment' => 'color: {{VALUE}};',
				),
			)
		);

		/*Likes Icon Size*/
		$this->add_responsive_control(
			'premium_instgram_feed_comment_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-comment' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_instgram_feed_comment_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-insta-comment' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_instgram_feed_comments_border',
				'selector' => '{{WRAPPER}} .premium-insta-comment',
			)
		);

		/*Likes Border Radius*/
		$this->add_control(
			'premium_instgram_feed_comment_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-comment' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		/*Likes Box Shadow*/
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_instgram_feed_comments_shadow',
				'selector' => '{{WRAPPER}} .premium-insta-comment',
			)
		);

		$this->add_responsive_control(
			'premium_instgram_feed_comments_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-comment' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		/*Likes Padding*/
		$this->add_responsive_control(
			'premium_instgram_feed_comments_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-comment' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_instgram_feed_comments_number',
			array(
				'label' => __( 'Number', 'premium-addons-pro' ),
			)
		);

		/*Likes Number Color*/
		$this->add_control(
			'premium_instgram_feed_comments_number_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-insta-comments' => 'color: {{VALUE}};',
				),
			)
		);

		/*Likes Number Typography*/
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_instgram_feed_comments_number_typo',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-insta-comments',
			)
		);

		$this->add_control(
			'premium_instgram_feed_comments_number_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  .premium-insta-comments' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_instgram_feed_comments_number_border',
				'selector' => '{{WRAPPER}} .premium-insta-comments',
			)
		);

		$this->add_control(
			'premium_instgram_feed_comments_number_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-comments' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_instgram_feed_comments_number_shadow',
				'selector' => '{{WRAPPER}} .premium-insta-comments',
			)
		);

		$this->add_responsive_control(
			'premium_instgram_feed_comments_number_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-comments' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_instgram_feed_comments_number_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-comments' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_instgram_feed_caption',
			array(
				'label'     => __( 'Caption', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_instagram_feed_show_caption' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_instgram_feed_caption_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-insta-image-caption' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_instgram_feed_caption_typo',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-insta-image-caption',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_instgram_feed_caption_shadow',
				'selector' => '{{WRAPPER}} .premium-insta-image-caption',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_instgram_feed_general_style',
			array(
				'label' => __( 'Container', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_instgram_feed_container_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-instafeed-container' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_instgram_feed_container_box_border',
				'selector' => '{{WRAPPER}} .premium-instafeed-container',
			)
		);

		$this->add_control(
			'premium_instgram_feed_container_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_instgram_feed_container_box_shadow',
				'selector' => '{{WRAPPER}} .premium-instafeed-container',
			)
		);

		$this->add_responsive_control(
			'premium_instgram_feed_container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_instgram_feed_container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_instgram_feed_spinner_style',
			array(
				'label' => __( 'Spinner', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_instgram_feed_spinner_background',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-loader' => 'border-top-color: {{VALUE}} !important',
				),
			)
		);

		$this->add_control(
			'premium_instgram_feed_circle_background',
			array(
				'label'     => __( 'Spinner Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-loader' => 'border-color: {{VALUE}};',
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

		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-container .slick-arrow i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'premium_instgram_feed_carousel' );

		$this->start_controls_tab(
			'premium_instgram_carousel_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
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
					'{{WRAPPER}} .premium-instafeed-container .slick-arrow' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .premium-instafeed-container .slick-arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_instgram_carousel_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'arrow_color_hov',
			array(
				'label'     => __( 'Arrow Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-instafeed-container .slick-arrow:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_background_hov',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-instafeed-container .slick-arrow:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'arrow_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-container .slick-arrow' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-instafeed-container .slick-arrow' => 'padding: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'profile_header_style',
			array(
				'label'     => __( 'Profile Header', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'account_info_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'instafeed_user_info_heading',
			array(
				'label'      => __( 'User Info', 'premium-addons-pro' ),
				'type'       => Controls_Manager::HEADING,
				'separator'  => 'before',
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'account_info_switcher',
							'value' => 'yes',
						),
						$info_terms,
					),
				),
			)
		);

		$this->start_controls_tabs( 'isntafeed_user_info_style_tabs' );

		$this->start_controls_tab(
			'isntafeed_avatar_tab',
			array(
				'label'     => __( 'Avatar', 'premium-addons-pro' ),
				'condition' => array(
					'premium_instafeed_profile_pic' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_instafeed_avatar_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 400,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 100,
					'unit' => 'px',
				),
				'condition'  => array(
					'premium_instafeed_profile_pic' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-header-pic-wrapper' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'premium_instafeed_avatar_border',
				'selector'  => '{{WRAPPER}} .premium-instafeed-header-pic-wrapper',
				'condition' => array(
					'premium_instafeed_profile_pic' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_instafeed_avatar_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'condition'  => array(
					'premium_instafeed_profile_pic' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-header-pic-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_instafeed_avatar_bg',
			array(
				'label'     => __( 'Background Color ', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-instafeed-header-pic-wrapper' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'premium_instafeed_profile_pic' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'name'      => 'premium_instafeed_avatar_shadow',
				'selector'  => '{{WRAPPER}} .premium-instafeed-header-pic-wrapper',
				'condition' => array(
					'premium_instafeed_profile_pic' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_instafeed_avatar_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'condition'  => array(
					'premium_instafeed_profile_pic' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-header-pic-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),

			)
		);

		$this->add_responsive_control(
			'premium_instafeed_avatar_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'condition'  => array(
					'premium_instafeed_profile_pic' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-header-pic-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'isntafeed_username_tab',
			array(
				'label'     => __( 'Username', 'premium-addons-pro' ),
				'condition' => array(
					'premium_instafeed_username' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_isntafeed_username_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-instafeed-username' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_isntafeed_username_typo',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-instafeed-username',
			)
		);

		$this->add_responsive_control(
			'premium_instafeed_username_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-username-outer' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'isntafeed_bio_tab',
			array(
				'label'     => __( 'Bio', 'premium-addons-pro' ),
				'condition' => array(
					'premium_instafeed_bio' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_isntafeed_bio_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-instafeed-bio' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_isntafeed_bio_typo',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-instafeed-bio',
			)
		);

		$this->add_responsive_control(
			'premium_instafeed_bio_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-bio' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'instafeed_user_activity_heading',
			array(
				'label'      => __( 'User Data', 'premium-addons-pro' ),
				'type'       => Controls_Manager::HEADING,
				'separator'  => 'before',
				'conditions' => array(
					'terms' => array(
						$terms,
					),
				),
			)
		);

		$this->add_control(
			'premium_isntafeed_act_color',
			array(
				'label'      => __( 'Color', 'premium-addons-pro' ),
				'type'       => Controls_Manager::COLOR,
				'scheme'     => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-user-activity-item' => 'color: {{VALUE}};',
				),
				'conditions' => array(
					'terms' => array(
						$terms,
					),
				),
			)
		);

		$this->add_control(
			'premium_isntafeed_act_bg',
			array(
				'label'      => __( 'Background color', 'premium-addons-pro' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-user-activity-item' => 'background-color: {{VALUE}};',
				),
				'conditions' => array(
					'terms' => array(
						$terms,
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'       => 'premium_isntafeed_act_typo',
				'scheme'     => Typography::TYPOGRAPHY_1,
				'selector'   => '{{WRAPPER}} .premium-instafeed-user-activity-item',
				'conditions' => array(
					'terms' => array(
						$terms,
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'       => 'premium_isntafeed_act_border',
				'selector'   => '{{WRAPPER}} .premium-instafeed-user-activity-item',
				'conditions' => array(
					'terms' => array(
						$terms,
					),
				),
			)
		);

		$this->add_control(
			'premium_isntafeed_act_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-user-activity-item' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'terms' => array(
						$terms,
					),
				),
			)
		);

		$this->add_responsive_control(
			'premium_isntafeed_act_spacing',
			array(
				'label'      => __( 'Number Spacing', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-insta-lower-item-val' => 'margin-' . $icon_spacing . ': {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'instafeed_user_activity_display',
							'value' => 'inline',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'premium_instafeed_followers',
									'value' => 'yes',
								),
								array(
									'name'  => 'premium_instafeed_following',
									'value' => 'yes',
								),
								array(
									'name'  => 'premium_instafeed_posts',
									'value' => 'yes',
								),
							),
						),
					),

				),
			)
		);

		$this->add_responsive_control(
			'premium_isntafeed_act_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-user-activity-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => array(
					'terms' => array(
						$terms,
					),
				),
			)
		);

		$this->add_responsive_control(
			'premium_isntafeed_act_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-user-activity-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => array(
					'terms' => array(
						$terms,
					),
				),
			)
		);

		$this->add_control(
			'instafeed_user_verify_heading',
			array(
				'label'     => __( 'Verified Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'premium_instafeed_verified' => 'yes',
					'premium_instafeed_username' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'instafeed_user_verify_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 20,
					'unit' => 'px',
				),
				'condition'  => array(
					'premium_instafeed_verified' => 'yes',
					'premium_instafeed_username' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .instafeed-v-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_isntafeed_vIcon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'condition' => array(
					'premium_instafeed_verified' => 'yes',
					'premium_instafeed_username' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .instafeed-v-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_isntafeed_vIcon_spacing',
			array(
				'label'      => __( 'Icon Spacing', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 6,
					'unit' => 'px',
				),
				'condition'  => array(
					'premium_instafeed_verified' => 'yes',
					'premium_instafeed_username' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .instafeed-v-icon' => 'margin-' . $icon_spacing . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'instafeed_profile_container_heading',
			array(
				'label'     => __( 'Container', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'premium_profile_container_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-instafeed-header-container' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_profile_container_border',
				'selector' => '{{WRAPPER}} .premium-instafeed-header-container',
			)
		);

		$this->add_control(
			'premium_profile_container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-header-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_profile_container_shadow',
				'selector' => '{{WRAPPER}} .premium-instafeed-header-container',
			)
		);

		$this->add_responsive_control(
			'premium_profile_container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-profile-outer-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_profile_container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-header-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_instafeed_logo_style',
			array(
				'label'     => __( 'Instagram Logo', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_instafeed_logo_switcher' => 'yes',
					'account_info_switcher'           => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_instafeed_logo_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-logo-container' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_instafeed_logo_alignment_row',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-instafeed-logo-container' => 'text-align: {{VALUE}};',
				),
				'default'   => 'center',
				'condition' => array(
					'instafeed_user_info_display' => 'col',
				),
			)
		);

		$this->add_responsive_control(
			'premium_instafeed_logo_alignment',
			array(
				'label'     => __( 'Vertical Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'fa fa-long-arrow-up',
					),
					'middle' => array(
						'title' => __( 'Middle', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'fa fa-long-arrow-down',
					),
				),
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-instafeed-logo-container i ' => 'vertical-align: {{VALUE}};',
				),
				'default'   => 'middle',
				'condition' => array(
					'instafeed_user_info_display' => 'row',
				),
			)
		);
		$this->add_responsive_control(
			'premium_instafeed_logo_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-logo-container i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_instafeed_Sb_style',
			array(
				'label'     => __( 'Share Button', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_instagram_feed_share' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_instafeed_button_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 17,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .fa.custom-fa' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_instafeed_button_typo',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-instafeed-sharer',
			)
		);

		$this->add_control(
			'premium_instafeed_button_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-instafeed-share-container' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_instafeed_button_spacing',
			array(
				'label'      => __( 'Icon Spacing', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .fa.custom-fa' => 'margin-' . $icon_spacing . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'premium_instafeed_share_buttons' );

		$this->start_controls_tab(
			'premium_instafeed_sb_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_instafeed_button_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .fa.custom-fa' => '-webkit-text-stroke-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_instafeed_button_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-instafeed-sharer' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_instafeed_sb_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_instafeed_button_Icolor_hov',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-instafeed-share-button:hover .fa.custom-fa' => '-webkit-text-stroke-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_instafeed_button_color_hov',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-instafeed-share-button:hover .premium-instafeed-sharer' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'premium_instafeed_button_border',
				'selector'  => '{{WRAPPER}} .premium-instafeed-share-container',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'premium_instafeed_button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-share-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_instafeed_button_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-share-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_instafeed_button_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-share-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_instafeed_Sl_style',
			array(
				'label'     => __( 'Share Links', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_instagram_feed_share' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_instafeed_sl_menu_bg',
			array(
				'label'     => __( 'Menu Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-instafeed-share-menu' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_instafeed_sl_typo',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-instafeed-share-text',
			)
		);

		$this->add_responsive_control(
			'premium_instafeed_sl_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 17,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-share-item i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_instafeed_sl_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-share-menu' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_instafeed_sl_spacing',
			array(
				'label'      => __( 'Icon Spacing', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 3,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-share-item i' => 'margin-' . $icon_spacing . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_instafeed_sl_spacing_ver',
			array(
				'label'      => __( 'Vertical Spacing', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-instafeed-share-item' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-instafeed-share-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'premium_instafeed_share_links' );

		$this->start_controls_tab(
			'premium_instafeed_sl_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_instafeed_sl_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-instafeed-share-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_instafeed_sl_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_instafeed_sl_color_hov',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-instafeed-share-item:hover .premium-instafeed-share-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render Instagram Feed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$hover_effect = 'premium-insta-' . $settings['premium_instagram_feed_image_hover'];

		$api_version = $settings['api_version'];

		$logo = ( 'yes' === $settings['premium_instafeed_logo_switcher'] ) ? true : false;

		$share      = ( 'yes' === $settings['premium_instagram_feed_share'] ) ? true : false;
		$share_html = '';

		if ( $share ) {

			$share_html = '<div class="premium-instafeed-share-outer"><div class="premium-instafeed-share-container">
            <span class="premium-instafeed-share-button">
                <i class="fa fa-share custom-fa" aria-hidden="true"></i>
                <span class="premium-instafeed-sharer">Share</span>
                <div class="premium-instafeed-share-menu">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{link}}" class="premium-instafeed-share-item" target="popup"
                    onclick="window.open(\'https://www.facebook.com/sharer/sharer.php?u={{link}}\',\'popup\',\'width=600,height=600\'); return false;">
                        <i class="fab fa-facebook-f if-fb"></i>
                        <span class="premium-instafeed-share-text pre-infs-fb">Share on Facebook</span>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text=tweet&url={{link}}" class="premium-instafeed-share-item" target="popup"
                    onclick="window.open(\'https://twitter.com/intent/tweet?text=tweet&url={{link}}\',\'popup\',\'width=600,height=600\'); return false;">
                        <i class="fab fa-twitter if-tw"></i>
                        <span class="premium-instafeed-share-text pre-infs-tw">Share on Twitter</span>
                    </a>
                    <a data-pin-do="buttonPin" href="https://www.pinterest.com/pin/create/button/?url={{link}}" class="premium-instafeed-share-item "  target="popup"
                    onclick="window.open(\'https://www.pinterest.com/pin/create/button/?url={{link}}\',\'popup\',\'width=600,height=600\'); return false;">
                        <i class="fab fa-pinterest-p if-pi"></i>
                        <span class="premium-instafeed-share-text pre-infs-pi">Share on Pinterest</span>
                    </a>
                </div>
            </span>
        </div></div>';
		}

		$new_tab = 'yes' === $settings['premium_instagram_feed_new_tab'] ? 'target="_blank"' : '';

		if ( 'yes' === $settings['premium_instagram_feed_link'] ) {
			$link = '<a href="{{link}}"' . $new_tab . '></a>';
		} else {
			if ( 'yes' === $settings['premium_instagram_feed_popup'] ) {
				$link = '<a href="{{image}}" data-rel="prettyPhoto[premium-insta-' . esc_attr( $this->get_id() ) . ']"></a>';
			} else {
				$link = '';
			}
		}

		if ( 'yes' === $settings['premium_instagram_feed_show_caption'] ) {
			$caption = '<p class="premium-insta-image-caption">{{caption}}</p>';
		} else {
			$caption = '';
		}

		$access_token = 'old' === $api_version ? $settings['premium_instagram_feed_client_access_token'] : $settings['new_accesstoken'];

		$tags = '';

		if ( ! empty( $settings['premium_instagram_feed_tag_name'] ) ) {
			$tags = explode( ',', $settings['premium_instagram_feed_tag_name'] );
			foreach ( $tags as $index => $tag ) {
				$tags[ $index ] = trim( $tag );
			}
		}

		$likes    = '';
		$comments = '';
		$sort     = 'none';

		$api = $settings['api_version'];

		if ( 'old' === $api ) {

			if ( 'yes' === $settings['premium_instagram_feed_show_likes'] ) {
				$likes = '<p><i class="fas fa-heart premium-insta-heart" aria-hidden="true"></i> <span  class="premium-insta-likes">{{likes}}</span></p>';
			}

			if ( 'yes' === $settings['premium_instagram_feed_show_comments'] ) {
				$comments = '<p><i class="fas fa-comment premium-insta-comment" aria-hidden="true"></i><span class="premium-insta-comments">{{comments}}</span></p>';
			}

			$sort = $settings['premium_instagram_feed_sort_by'];
		}

		$limit = ( '' !== $settings['premium_instagram_feed_img_number'] ) ? $settings['premium_instagram_feed_img_number'] : 6;

		$carousel = 'yes' === $settings['feed_carousel'] ? true : false;

		$account_info = 'yes' === $settings['account_info_switcher'] ? true : false;

		if ( $carousel ) {

			$play = 'yes' === $settings['carousel_play'] ? true : false;

			$speed = ! empty( $settings['carousel_autoplay_speed'] ) ? $settings['carousel_autoplay_speed'] : 5000;

			$this->add_render_attribute(
				'instagram',
				array(
					'data-carousel' => $carousel,
					'data-play'     => $play,
					'data-speed'    => $speed,
					'data-rtl'      => is_rtl(),
				)
			);

		}

		$instagram_settings = array(
			'api'         => $api,
			'accesstok'   => $access_token,
			'tags'        => $tags,
			'sort'        => $sort,
			'limit'       => $limit,
			'likes'       => $likes,
			'comments'    => $comments,
			'description' => $caption,
			'link'        => $link,
			'videos'      => $settings['show_videos'],
			'id'          => 'premium-instafeed-container-' . $this->get_id(),
			'masonry'     => ( 'yes' === $settings['premium_instagram_feed_masonry'] ) ? true : false,
			'theme'       => $settings['premium_instagram_feed_popup_theme'],
			'words'       => $settings['premium_instagram_feed_caption_number'],
			'share'       => $share_html,
		);

		if ( $account_info ) {

			$reload = Helper_Functions::transient_expire( $settings['premium_instafeed_reload'] );

			$account_settings = array(
				'reload'        => $reload,
				'user_name'     => $settings['premium_instafeed_user_name'],
				'profile_pic'   => ( 'yes' === $settings['premium_instafeed_profile_pic'] ) ? true : false,
				'show_username' => ( 'yes' === $settings['premium_instafeed_username'] ) ? true : false,
				'followers'     => ( 'yes' === $settings['premium_instafeed_followers'] ) ? true : false,
				'following'     => ( 'yes' === $settings['premium_instafeed_following'] ) ? true : false,
				'bio'           => ( 'yes' === $settings['premium_instafeed_bio'] ) ? true : false,
				'verify'        => ( 'yes' === $settings['premium_instafeed_verified'] ) ? true : false,
				'posts'         => ( 'yes' === $settings['premium_instafeed_posts'] ) ? true : false,
			);

			$this->add_render_attribute( 'instagram-header', 'class', 'premium-instafeed-header-container' );
		}

		if ( $logo ) {
			$this->add_render_attribute( 'instagram_logo', 'class', 'premium-instafeed-logo-container' );
		}

		$this->add_render_attribute(
			'instagram',
			array(
				'class'         => 'premium-instafeed-container',
				'data-settings' => wp_json_encode( $instagram_settings ),
			)
		);

		$this->add_render_attribute(
			'instagram_container',
			array(
				'id'    => 'premium-instafeed-container-' . $this->get_id(),
				'class' => array(
					'premium-insta-grid',
					$hover_effect,
				),
			)
		);

		$feed_number = intval( 100 / substr( $settings['premium_instagram_feed_column_number'], 0, strpos( $settings['premium_instagram_feed_column_number'], '%' ) ) );

		$this->add_render_attribute( 'instagram', 'data-col', $feed_number );

		if ( 'Invalid License Key' === $access_token ) : ?>
			<div class="premium-error-notice">
				<?php echo esc_html( __( 'Please activate your license to get the access token', 'premium-addons-pro' ) ); ?>
			</div>
		<?php elseif ( empty( $access_token ) ) : ?>
			<div class="premium-error-notice">
				<?php echo esc_html( __( 'Please fill the required fields: Access Token', 'premium-addons-pro' ) ); ?>
			</div>
		<?php else : ?>
			<?php if ( $account_info ) : ?>
			<div class="premium-instafeed-profile-outer-container">
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'instagram-header' ) ); ?>>
					<?php if ( $logo ) : ?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'instagram_logo' ) ); ?>>
							<i class="fab fa-instagram"></i>
						</div>
					<?php endif; ?>
					<?php premium_insta_profile_info( $account_settings ); ?>
				</div>
			</div>
			<?php endif; ?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'instagram' ) ); ?>>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'instagram_container' ) ); ?>></div>
				<div class="premium-loading-feed premium-show-loading">
					<div class="premium-loader"></div>
				</div>
			</div>

			<?php
		endif;
	}
}
?>
