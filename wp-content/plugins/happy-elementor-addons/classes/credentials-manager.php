<?php

namespace Happy_Addons\Elementor;

defined('ABSPATH') || die();

class Credentials_Manager {
	const CREDENTIALS_DB_KEY = 'happyaddons_credentials';

	/**
	 * Initialize
	 */
	public static function init() {

		// if (is_admin()) {
		// 	$screen = get_current_screen();

		// 	if ($screen->id == "dashboard") {

		// 		if (is_admin() && is_user_logged_in() && ha_is_adminbar_menu_enabled()) {
		// 			include_once HAPPY_ADDONS_DIR_PATH . 'classes/admin-bar.php';
		// 		}

		// 		if (is_admin() && is_user_logged_in() && ha_is_happy_clone_enabled()) {
		// 			include_once HAPPY_ADDONS_DIR_PATH . 'classes/clone-handler.php';
		// 		}

		// 	}
		// }

		// $credentials = self::get_credentials();
	}

	public static function get_credentials_map() {
		$credentials_map = [];

		$local_credentials_map = self::get_local_credentials_map();
		$credentials_map = array_merge($credentials_map, $local_credentials_map);

		return apply_filters('happyaddons_get_credentials_map', $credentials_map);
	}

	public static function get_saved_credentials() {
		return get_option(self::CREDENTIALS_DB_KEY, []);
	}

	public static function save_credentials($credentials = []) {
		update_option(self::CREDENTIALS_DB_KEY, $credentials);
	}

	/**
	 * Get the pro credentials map for dashboard only
	 *
	 * @return array
	 */
	public static function get_pro_credentials_map() {
		return [
			'advanced_data_table' => [
				'title' => __('Advanced Data Table', 'happy-elementor-addons'),
				'icon' => 'hm hm-data-table',
				'fiels' => [
					[
						'label' => esc_html__('Google API Key. ', 'happy-elementor-addons'),
						'type' => 'text',
						'name' => 'api_key',
						'help' => [
							'instruction' => esc_html__('Get API Key', 'happy-elementor-addons'),
							'link' => 'https://console.developers.google.com/'
						],
					],
					[
						'label' => esc_html__('Google Sheet ID. ', 'happy-elementor-addons'),
						'type' => 'text',
						'name' => 'sheet_id',
						'help' => [],
					],
					[
						'label' => esc_html__('Google Sheets Range. Ex: A1:D5 ', 'happy-elementor-addons'),
						'type' => 'text',
						'name' => 'sheet_range',
						'help' => [],
					],
				],
				'is_pro' => true,
			],
			'facebook_feed' => [
				'title' => __('Facebook Feed', 'happy-elementor-addons'),
				'icon' => 'hm hm-facebook',
				'fiels' => [
					[
						'label' => esc_html__('Page ID. ', 'happy-elementor-addons'),
						'type' => 'text',
						'name' => 'page_id',
						'help' => [
							'instruction' => esc_html__('Get Page ID', 'happy-elementor-addons'),
							'link' => 'https://developers.facebook.com/apps/'
						],
					],
					[
						'label' => esc_html__('Access Token. ', 'happy-elementor-addons'),
						'type' => 'text',
						'name' => 'access_token',
						'help' => [
							'instruction' => esc_html__('Get Access Token.', 'happy-elementor-addons'),
							'link' => 'https://developers.facebook.com/apps/'
						],
					],
				],
				'is_pro' => true,
			],
			'instagram' => [
				'title' => __('Instagram', 'happy-elementor-addons'),
				'icon' => 'hm hm-instagram',
				'fiels' => [
					[
						'label' => esc_html__('Access Token. ', 'happy-elementor-addons'),
						'type' => 'text',
						'name' => 'access_token',
						'help' => [
							'instruction' => esc_html__('Get Access Token', 'happy-elementor-addons'),
							'link' => 'https://developers.facebook.com/docs/instagram-basic-display-api/getting-started'
						],
					],
				],
				'is_pro' => true,
			],
		];
	}

	/**
	 * Get the free credentials map
	 *
	 * @return array
	 */
	public static function get_local_credentials_map() {
		return [
			'mailchimp' => [
				'title' => __('MailChimp', 'happy-elementor-addons'),
				'icon' => 'hm hm-mail-chimp',
				'fiels' => [
					[
						'label' => esc_html__('Enter API Key. ', 'happy-elementor-addons'),
						'type' => 'text',
						'name' => 'api',
						'help' => [
							'instruction' => esc_html__('Get your api key here', 'happy-elementor-addons'),
							'link' => 'https://admin.mailchimp.com/account/api/'
						],
					],
				],
				'demo' => 'https://happyaddons.com/mailchimp/',
				'is_pro' => false,
			],
			'twitter_feed' => [
				'title' => __('Twitter Feed', 'happy-elementor-addons'),
				'icon' => 'hm hm-twitter-feed',
				'fiels' => [
					[
						'label' => esc_html__('User Name. (Use @ sign with your Twitter user name)', 'happy-elementor-addons'),
						'type' => 'text',
						'name' => 'user_name',
					],
					[
						'label' => esc_html__('Consumer Key', 'happy-elementor-addons'),
						'type' => 'text',
						'name' => 'consumer_key',
						'help' => [
							'instruction' => esc_html__('Get Consumer Key', 'happy-elementor-addons'),
							'link' => 'https://apps.twitter.com/app/'
						],
					],
					[
						'label' => esc_html__('Consumer Secret', 'happy-elementor-addons'),
						'type' => 'text',
						'name' => 'consumer_secret',
						'help' => [
							'instruction' => esc_html__('Get Consumer Secret', 'happy-elementor-addons'),
							'link' => 'https://apps.twitter.com/app/'
						],
					],
				],
				// 'help' => 'https://happyaddons.com/mailchimp/',
				'is_pro' => false,
			],
		];
	}
}

Credentials_Manager::init();
