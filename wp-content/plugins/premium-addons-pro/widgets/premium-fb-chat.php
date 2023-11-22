<?php
/**
 * Class: Premium_Fb_Chat
 * Name: Messenger Chat
 * Slug: premium-addon-facebook-chat
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Fb_Chat
 */
class Premium_Fb_Chat extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-addon-facebook-chat';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Messenger Chat', 'premium-addons-pro' );
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
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-pro-messenger-chat';
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
		return array( 'premium-pro' );
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
		return array( 'pa', 'premium', 'facebook', 'message' );
	}

	/**
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://www.youtube.com/watch?v=xAXD9WBCetw&list=PLLpZVOYpMtTArB4hrlpSnDJB36D2sdoTv';
	}

	/**
	 * Register Facebook Messenger Chat controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_fbchat_account_settings',
			array(
				'label' => __( 'Account Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_fbchat_app_id',
			array(
				'label'       => __( 'App ID', 'premium-addons-pro' ),
				'description' => 'Click <a href="https://developers.facebook.com/docs/apps/register" target="_blank">Here</a> to create and get App Id',
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'premium_fbchat_page_id',
			array(
				'label'       => __( 'Page ID', 'premium-addons-pro' ),
				'description' => 'You need to put your site URL in whitelisted domains in your page messenger platform settings, Click <a href="https://www.facebook.com/help/1503421039731588" target="_blank">Here</a> to know how to get your page ID',
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_fbchat_message',
			array(
				'label' => __( 'Messages', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_fbchat_login_msg',
			array(
				'label'       => __( 'Logged in Message', 'premium-addons-pro' ),
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'The greeting text that will be displayed if the user is currently logged in to Facebook. Maximum 80 characters.', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'premium_fbchat_logout_msg',
			array(
				'label'       => __( 'Logged out Message', 'premium-addons-pro' ),
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'The greeting text that will be displayed if the user is not currently logged in to Facebook. Maximum 80 characters.', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_fbchat_adv_section',
			array(
				'label' => __( 'Advanced Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_fbchat_lang',
			array(
				'label'       => __( 'Language', 'premium-addons-pro' ),
				'description' => __( 'Select language for the chat box, default is English', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'en_US',
				'options'     => array(
					'en_US' => __( 'English', 'premium-addons-pro' ),
					'fr_FR' => __( 'French', 'premium-addons-pro' ),
					'da_DK' => __( 'Danish', 'premium-addons-pro' ),
					'de_DE' => __( 'German', 'premium-addons-pro' ),
					'ja_JP' => __( 'Japanese', 'premium-addons-pro' ),
					'ko_KR' => __( 'Korean', 'premium-addons-pro' ),
					'he_IL' => __( 'Hebrew', 'premium-addons-pro' ),
					'es_ES' => __( 'Spanish', 'premium-addons-pro' ),
					'zh_TW' => __( 'Chinese', 'premium-addons-pro' ),
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_fbchat_ref',
			array(
				'label'       => __( 'Ref', 'premium-addons-pro' ),
				'description' => __( 'Optional. Custom string passed to your webhook in messaging_postbacks and messaging_referrals events.', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_fbchat_mini',
			array(
				'label'       => __( 'Minimized', 'premium-addons-pro' ),
				'description' => __( 'Specifies whether the plugin should be minimized or shown. Defaults to false on desktop and true on mobile browsers.', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_fbchat_hide_mobile',
			array(
				'label'       => __( 'Hide on Mobiles', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This will hide the messenger chat on mobile phones', 'premium-addons-pro' ),
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
			'https://premiumaddons.com/docs/facebook-messenger-chat-widget-tutorial/' => __( 'Getting started »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/how-to-create-facebook-application-for-premium-facebook-messenger-widget/' => __( 'How to create Facebook application »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/how-to-whitelist-your-domain-for-premium-messenger-chat-widget/' => __( 'How to whitelist your domain through your Facebook page settings »', 'premium-addons-pro' ),
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

		$this->start_controls_section(
			'premium_fbchat_style',
			array(
				'label' => __( 'Icon', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_fbchat_theme_color',
			array(
				'label'  => __( 'Theme Color', 'premium-addons-pro' ),
				'type'   => Controls_Manager::COLOR,
				'global' => false,
			)
		);

		$this->add_control(
			'premium_fbchat_position_select',
			array(
				'label'       => __( 'Position', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'topleft'     => __( 'Top Left', 'premium-addons-pro' ),
					'topright'    => __( 'Top Right', 'premium-addons-pro' ),
					'bottomleft'  => __( 'Bottom Left', 'premium-addons-pro' ),
					'bottomright' => __( 'Bottom Right', 'premium-addons-pro' ),
					'custom'      => __( 'Custom', 'premium-addons-pro' ),
				),
				'default'     => 'bottomright',
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_fbchat_hor_position',
			array(
				'label'     => __( 'Horizontal Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 0,
				),
				'condition' => array(
					'premium_fbchat_position_select' => 'custom',
				),
			)
		);

		$this->add_control(
			'premium_fbchat_ver_position',
			array(
				'label'     => __( 'Vertical Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 0,
				),
				'condition' => array(
					'premium_fbchat_position_select' => 'custom',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Facebook Messenger Chat widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$app_id = $settings['premium_fbchat_app_id'];

		$page_id = $settings['premium_fbchat_page_id'];

		$login_msg = $settings['premium_fbchat_login_msg'];

		$logout_msg = $settings['premium_fbchat_logout_msg'];

		$theme_color = $settings['premium_fbchat_theme_color'];

		$position = $settings['premium_fbchat_position_select'];

		$hide_mobile = 'yes' === $settings['premium_fbchat_hide_mobile'] ? true : false;

		$ref = $settings['premium_fbchat_ref'];

		$language = $settings['premium_fbchat_lang'];

		$pa_chat_settings = array(
			'appId'      => $app_id,
			'hideMobile' => $hide_mobile,
			'lang'       => $language,
		);

		$this->add_render_attribute(
			'chat',
			array(
				'class'                   => 'fb-customerchat',
				'page_id'                 => esc_attr( $page_id ),
				'theme_color'             => esc_attr( $theme_color ),
				'logged_in_greeting'      => esc_attr( $login_msg ),
				'logged_out_greeting'     => esc_attr( $logout_msg ),
				'greeting_dialog_display' => 'hide',
				'ref'                     => esc_attr( $ref ),
			)
		);

		if ( 'yes' !== $settings['premium_fbchat_mini'] ) {
			$this->add_render_attribute( 'chat', 'minimized', false );
		}

		?>

	<div id="premium-fbchat-container" class="premium-fbchat-container" data-settings='<?php echo wp_json_encode( $pa_chat_settings ); ?>'>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'chat' ) ); ?>></div>
	</div>
	<style>
		<?php if ( 'bottomleft' === $position ) : ?>
		.fb_dialog,
		.fb-customerchat * > iframe {
			left: 18pt !important;
			right: auto;
		}
		<?php elseif ( 'topleft' === $position ) : ?>
		.fb_dialog,
		.fb-customerchat * > iframe {
			left: 18pt !important;
			right: auto;
			top:18px !important;
			bottom: auto;
		}
		<?php elseif ( 'topright' === $position ) : ?>
		.fb_dialog,
		.fb-customerchat * > iframe {
			right: 18pt !important;
			left: auto;
			top:18px !important;
			bottom: auto;
		}
		<?php elseif ( 'custom' === $position ) : ?>
		.fb_dialog,
		.fb-customerchat * > iframe {
			left: <?php echo esc_attr( $settings['premium_fbchat_hor_position']['size'] . '%' ); ?> !important;
			right: auto;
			top: <?php echo esc_attr( $settings['premium_fbchat_ver_position']['size'] . '%' ); ?> !important;
			bottom: auto;
			-webkit-transform: translate(-50%,-50%);
			transform: translate(-50%,-50%);
		}
		<?php endif; ?>
	</style>

		<?php
	}
}
