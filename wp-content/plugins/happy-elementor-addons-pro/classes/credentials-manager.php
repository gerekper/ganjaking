<?php

namespace Happy_Addons_Pro;

defined('ABSPATH') || die();

class Credentials_Manager {
	const CREDENTIALS_DB_KEY = 'happyaddons_credentials';

	/**
	 * Initialize
	 */
	public static function init() {

		add_filter('happyaddons_get_credentials_map', [__CLASS__, 'add_credentials_map']);
	}

	public static function add_credentials_map($credentials_map) {
		$credentials_map = array_merge($credentials_map, self::get_local_credentials_map());
		return $credentials_map;
	}

	/**
	 * Get the pro credentials map for dashboard only
	 *
	 * @return array
	 */
	public static function get_local_credentials_map() {
		return [
			'advanced_data_table' => [
				'title' => __('Advanced Data Table', 'happy-addons-pro'),
				'icon' => 'hm hm-data-table',
				'fiels' => [
					[
						'label' => esc_html__('Google API Key. ', 'happy-addons-pro'),
						'type' => 'text',
						'name' => 'api_key',
						'help' => [
							'instruction' => esc_html__('Get API Key', 'happy-addons-pro'),
							'link' => 'https://console.developers.google.com/'
						],
					],
					[
						'label' => esc_html__('Google Sheet ID. ', 'happy-addons-pro'),
						'type' => 'text',
						'name' => 'sheet_id',
						'help' => [],
					],
					[
						'label' => esc_html__('Google Sheets Range. Ex: A1:D5 ', 'happy-addons-pro'),
						'type' => 'text',
						'name' => 'sheet_range',
						'help' => [],
					],
				],
				'is_pro' => true,
			],
			'facebook_feed' => [
				'title' => __('Facebook Feed', 'happy-addons-pro'),
				'icon' => 'hm hm-facebook',
				'fiels' => [
					[
						'label' => esc_html__('Page ID. ', 'happy-addons-pro'),
						'type' => 'text',
						'name' => 'page_id',
						'help' => [
							'instruction' => esc_html__('Get Page ID', 'happy-addons-pro'),
							'link' => 'https://developers.facebook.com/apps/'
						],
					],
					[
						'label' => esc_html__('Access Token. ', 'happy-addons-pro'),
						'type' => 'text',
						'name' => 'access_token',
						'help' => [
							'instruction' => esc_html__('Get Access Token.', 'happy-addons-pro'),
							'link' => 'https://developers.facebook.com/apps/'
						],
					],
				],
				'is_pro' => true,
			],
			'instagram' => [
				'title' => __('Instagram', 'happy-addons-pro'),
				'icon' => 'hm hm-instagram',
				'fiels' => [
					[
						'label' => esc_html__('Access Token. ', 'happy-addons-pro'),
						'type' => 'text',
						'name' => 'access_token',
						'help' => [
							'instruction' => esc_html__('Get Access Token', 'happy-addons-pro'),
							'link' => 'https://developers.facebook.com/docs/instagram-basic-display-api/getting-started'
						],
					],
				],
				'is_pro' => true,
			],
			'google_map' => [
				'title' => __('Google Map', 'happy-addons-pro'),
				'icon' => 'hm hm-map-marker',
				'fiels' => [
					[
						'label' => esc_html__('Google Map API Key.', 'happy-addons-pro'),
						'type' => 'text',
						'name' => 'api_key',
						'help' => [
							'instruction' => esc_html__('Get API Key', 'happy-addons-pro'),
							'link' => 'https://console.developers.google.com/'
						],
					],
				],
				'is_pro' => true,
			],
		];
	}
}

Credentials_Manager::init();
