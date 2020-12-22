<?php

namespace WBCR\Factory_Adverts_117;

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

/**
 * Factory request class.
 *
 * Performs a server request, retrieves banner data and stores it in the cache.
 *
 * @author        Alexander Kovalev <alex.kovalevv@gmail.com>, Github: https://github.com/alexkovalevv
 * @author        Alexander Vitkalov <nechin.va@gmail.com>
 *
 * @since         1.0.1 Изменил имя класса и доработал его.
 * @since         1.0.0 Added
 *
 * @package       factory-adverts
 * @copyright (c) 2019 Webcraftic Ltd
 */
class Creative_Motion_API {

	/**
	 * Rest request url.
	 *
	 * Define rest request url for rest request to remote server.
	 *
	 * @since 1.2.1
	 */
	const SERVER_URL = 'https://api.cm-wp.com';

	/**
	 * Rest route path.
	 *
	 * Define rest route path for rest request.
	 *
	 * @since 1.0.0
	 */
	const REST_ROUTE = '/adverds/v1/advt';

	/**
	 * Интервал между запросами по умолчанию
	 *
	 * Значение в часах.
	 *
	 * @since 1.0.1
	 */
	const DEFAULT_REQUESTS_INTERVAL = 24;

	/**
	 * Интервал между запросами, если сервер недоступен
	 *
	 * Значение в часах.
	 *
	 * @since 1.0.1
	 */
	const SERVER_UNAVAILABLE_INTERVAL = 4;


	/**
	 * Экзепляр плагина с которым взаимодействует этот модуль
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.1
	 * @var \Wbcr_Factory439_Plugin
	 */
	private $plugin;


	/**
	 * Request constructor.
	 *
	 * Variable initialization.
	 *
	 * @param \Wbcr_Factory439_Plugin $plugin_name
	 * @since 1.0.0 Added
	 *
	 */
	public function __construct(\Wbcr_Factory439_Plugin $plugin)
	{
		$this->plugin = $plugin;
	}

	/**
	 * Get adverts content.
	 *
	 * @param $position
	 *
	 * @return string|\WP_Error
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.1
	 *
	 */
	public function get_content($position)
	{
		$data = $this->get_cache($position);

		if( is_wp_error($data) ) {
			return $data;
		}

		return strip_tags($data['content'], '<b>,<a>,<i>,<strong>,<img>,<ul>,<ol>,<li>');
	}

	/**
	 * Get data from cache.
	 *
	 * If data in the cache, not empty and not expired, then get data from cache. Or get data from server.
	 *
	 * @return mixed array(
	 *  'plugin'  => 'wbcr_insert_php',
	 *  'content' => '<p></p>',
	 *  'expires' => 1563542199,
	 * );
	 * @since  1.0.1 Полностью переписан, с перехватом api ошибок
	 * @since  1.0.0 Added
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 *
	 */
	private function get_cache($position)
	{

		if( defined('FACTORY_ADVERTS_DEBUG') && FACTORY_ADVERTS_DEBUG ) {
			return $this->do_api_request($position);
		}

		$key = $this->plugin->getPrefix() . md5($position . 'adverts_transient_');

		if( 'ru_RU' === get_locale() ) {
			$key .= 'ru_';
		}

		$cached = get_transient($key);

		if( $cached !== false ) {
			if( isset($cached['error_code']) && isset($cached['error']) ) {
				return new \WP_Error($cached['error_code'], $cached['error']);
			}

			return $cached;
		}

		$data = $this->do_api_request($position);

		if( is_wp_error($data) ) {
			set_transient($key, [
				'error' => $data->get_error_message(),
				'error_code' => $data->get_error_code()
			], self::SERVER_UNAVAILABLE_INTERVAL * HOUR_IN_SECONDS);

			return $data;
		}

		set_transient($key, $data, self::DEFAULT_REQUESTS_INTERVAL * HOUR_IN_SECONDS);

		return $data;
	}

	/**
	 * Performs rest api request.
	 *
	 * In some case on the server (Apache) in the .htaccess must be set
	 * RewriteRule ^wp-json/(.*)[?](.*) /?rest_route=/$1&$2 [L]
	 *
	 * @return mixed array(
	 *  'plugin'  => 'wbcr_insert_php',
	 *  'content' => '<p></p>',
	 *  'expires' => 1563542199,
	 * );
	 * @since 1.0.0 Added
	 *
	 * @since 1.0.1 Добавлен перехват ошибок, рефакторинг кода.
	 */
	private function do_api_request($position)
	{
		$default_result = [
			'content' => '',
			'expires' => self::DEFAULT_REQUESTS_INTERVAL * HOUR_IN_SECONDS,
		];

		$url = untrailingslashit(self::SERVER_URL) . '/wp-json' . self::REST_ROUTE;

		$ads_ID = $this->plugin->getPluginName();

		if( 'ru_RU' === get_locale() ) {
			$ads_ID .= '-ru';
		}

		$url = add_query_arg([
			'plugin' => $ads_ID,
			'position' => $position,
			'plugin_title' => $this->plugin->getPluginTitle(),
			'lang' => get_locale()
		], $url);

		$response = wp_remote_get($url);

		$code = wp_remote_retrieve_response_code($response);
		$body = wp_remote_retrieve_body($response);

		$data = @json_decode($body, true);

		if( is_wp_error($response) ) {
			return $response;
		}

		if( 200 !== $code ) {
			return new \WP_Error('http_request_error', 'Failed request to the remote server. Code: ' . $code);
		}

		return wp_parse_args($data, $default_result);
	}
}
