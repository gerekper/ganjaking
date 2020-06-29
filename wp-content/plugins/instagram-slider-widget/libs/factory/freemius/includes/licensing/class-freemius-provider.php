<?php

namespace WBCR\Factory_Freemius_111\Premium;

use WBCR\Factory_Freemius_111\Entities\License;
use WBCR\Factory_Freemius_111\Entities\Plugin;
use WBCR\Factory_Freemius_111\Entities\Site;
use WBCR\Factory_Freemius_111\Entities\User;
use WBCR\Factory_423\Premium\Provider as License_Provider;
use Wbcr_Factory423_Plugin;
use WBCR\Factory_Freemius_111\Api;
use WP_Error;
use Exception;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, Github: https://github.com/alexkovalevv
 * @copyright (c) 2018 Webraftic Ltd, Freemius, Inc.
 * @version       1.0
 */
final class Provider extends License_Provider {

	/**
	 * @var int
	 */
	private $plugin_id;

	/**
	 * @var string
	 */
	private $public_key;

	/**
	 * @var string
	 */
	private $slug;

	/**
	 * @var \WBCR\Factory_Freemius_111\Api
	 */
	private $site_api;

	/**
	 * @var \WBCR\Factory_Freemius_111\Api
	 */
	private $plugin_api;

	/**
	 * @var \WBCR\Factory_Freemius_111\Api
	 */
	private $user_api;

	/**
	 * @var bool
	 */
	private $is_activate_license = false;

	/**
	 * @var License|null
	 */
	private $license;

	/**
	 * @var Site|null
	 */
	private $license_site;

	/**
	 * @var User|null
	 */
	private $license_user;

	/**
	 * @var Plugin|null
	 */
	private $license_plugin;

	/**
	 * Manager constructor.
	 *
	 * @param Wbcr_Factory423_Plugin $plugin
	 *
	 * @throws Exception
	 */
	public function __construct( Wbcr_Factory423_Plugin $plugin, array $settings ) {
		parent::__construct( $plugin, $settings );

		$this->plugin_id  = $this->get_setting( 'plugin_id', null );
		$this->public_key = $this->get_setting( 'public_key', null );
		$this->slug       = $this->get_setting( 'slug', null );

		if ( empty( $this->plugin_id ) || empty( $this->public_key ) || empty( $this->slug ) ) {
			throw new Exception( 'One of required (plugin_id, public_key, slug) attrs is empty.' );
		}

		$this->init_license();
	}

	/**
	 * @return bool
	 * @throws Exception
	 */
	public function is_activate() {
		return $this->is_activate_license;
	}

	/**
	 * @return bool
	 * @throws Exception
	 */
	public function is_active() {
		if ( ! $this->is_activate_license ) {
			return false;
		}

		return $this->get_license()->is_valid();
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function get_plan() {
		if ( ! $this->is_activate_license ) {
			return null;
		}

		return $this->get_license()->get_plan();
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function get_billing_cycle() {
		if ( ! $this->is_activate_license ) {
			return null;
		}

		return $this->get_license()->get_billing_cycle();
	}

	/**
	 * @return \WBCR\Factory_Freemius_111\Entities\License|null
	 * @throws Exception
	 */
	public function get_license() {
		return $this->license;
	}

	/**
	 * @return string|null
	 * @throws \Freemius_Exception
	 * @throws Exception
	 */
	public function get_package_download_url() {

		if ( ! $this->is_activate_license ) {
			return null;
		}

		$endpoint = "/updates/latest.zip";

		$endpoint = add_query_arg( [
			'is_premium' => json_encode( true ),
			//'type'       => 'all'
		], $endpoint );

		try {
			return $this->get_api_site_scope( $this->license_site )->get_signed_url( $endpoint );
		} catch( \Freemius_Exception $e ) {
			throw new Exception( $e->getMessage(), $e->getCode() );
		}
	}

	/**
	 * @return array|mixed|string
	 * @throws \Freemius_Exception
	 * @throws Exception
	 */
	public function get_downloadable_package_info() {

		if ( ! $this->is_activate_license ) {
			return null;
		}
		try {
			$latest = $this->get_api_site_scope( $this->license_site )->call( "/updates/latest.json" );

			if ( isset( $latest->error ) ) {
				$error = $latest->error;

				if ( is_object( $error ) || is_array( $error ) ) {
					$error = var_export( $error, true );
				}

				throw new Exception( "Freemius API ERROR:" . $error );
			}
		} catch( \Freemius_Exception $e ) {
			throw new Exception( $e->getMessage(), $e->getCode() );
		}

		return $latest;
	}

	/**
	 * @param string $current_version
	 *
	 * @throws \Freemius_Exception
	 * @throws Exception
	 */
	public function get_package_updates( $current_version ) {

		if ( ! $this->is_activate_license ) {
			return null;
		}

		try {
			$updates = $this->get_api_site_scope( $this->license_site )->call( 'updates.json?version=' . $current_version, 'GET' );

			if ( isset( $updates->error ) ) {
				throw new Exception( $updates->error );
			}
		} catch( \Freemius_Exception $e ) {
			throw new Exception( $e->getMessage(), $e->getCode() );
		}

		return $updates;
	}

	/**
	 * Активирует лицензицию
	 *
	 * @param string $key
	 *
	 * @return bool|mixed
	 * @throws \Freemius_Exception
	 * @throws Exception
	 */
	public function activate( $key ) {

		$license_key = trim( rtrim( $key ) );

		if ( $this->is_activate_license ) {
			if ( $this->license->id == $license_key ) {
				$this->sync();

				return true;
			}

			$this->deactivate();
		}

		$url          = 'https://wp.freemius.com/action/service/user/install/';
		$request_body = [
			'plugin_slug'                  => $this->slug,
			'plugin_id'                    => $this->plugin_id,
			'plugin_public_key'            => $this->public_key,
			'plugin_version'               => $this->plugin->getPluginVersion(),
			'is_active'                    => true,
			'is_premium'                   => true,
			'is_uninstalled'               => false,
			'is_marketing_allowed'         => false,
			'is_disconnected'              => false,
			'user_ip'                      => $this->get_user_ip(),
			'format'                       => 'json',
			'license_key'                  => $license_key,
			'site_name'                    => get_bloginfo( 'name' ),
			'site_url'                     => get_home_url(), //site_uid
			'site_uid'                     => $this->get_unique_site_id(),
			'language'                     => get_bloginfo( 'language' ),
			'charset'                      => get_bloginfo( 'charset' ),
			'platform_version'             => get_bloginfo( 'version' ),
			'sdk_version'                  => '2.2.3',
			'programming_language_version' => phpversion()
		];

		$responce = wp_remote_post( $url, [
			'body'    => $request_body,
			'timeout' => 15,
		] );

		if ( is_wp_error( $responce ) ) {
			throw new Exception( $responce->get_error_message() );
		}

		if ( isset( $responce['response']['code'] ) && $responce['response']['code'] == 403 ) {
			return new WP_Error( 'alert-danger', 'http error' );
		}

		$responce_data = json_decode( $responce['body'] );

		if ( isset( $responce_data->error ) ) {
			throw new Exception( $responce_data->error );
		}

		$license_user = new User( $responce_data );
		$license_site = new Site( $responce_data );

		$user_api = $this->get_api_user_scope( $license_user );
		$site_api = $this->get_api_site_scope( $license_site );

		$user_licensies = $user_api->call( $this->get_plugin_endpoint() . '/licenses.json', 'GET' );

		foreach ( $user_licensies->licenses as $user_license ) {
			if ( $user_license->secret_key == $license_key ) {
				$license = new License( $user_license );
			}
		}

		$request_plan_path = $this->get_plugin_endpoint() . '/plans/' . $license->plan_id . '.json';
		$request_plan      = $user_api->call( $request_plan_path, 'GET' );

		$license->plan_title = $request_plan->title;

		$request_subscriptions_path = $this->get_license_endpoint( $license ) . '/subscriptions.json';
		$request_subscriptions      = $site_api->call( $request_subscriptions_path, 'GET' );

		if ( isset( $request_subscriptions->subscriptions ) && isset( $request_subscriptions->subscriptions[0] ) ) {
			$license->billing_cycle = $request_subscriptions->subscriptions[0]->billing_cycle;
		}

		$this->init_license( $license, $license_user, $license_site );
		$this->save_license_data();

		$plugin_name  = $this->plugin->getPluginName();
		$license_info = [
			'provider'        => 'freemius',
			'is_active'       => $this->is_active(),
			'license_key'     => $this->get_license()->get_key(),
			'expiration_time' => $this->get_license()->get_expiration_time()
		];

		/**
		 * Дейтсвие сработает после того, как лицензия будет успешно активирована
		 *
		 * @since 1.0.9 Изменил имя хука на {$plugin_name}/factory/premium/license_activate
		 * @since 1.0.0 Добавлен
		 *
		 * @param string $provider       Провайдер лицензии
		 * @param string $license_info   Дополнительная информация о лицензии
		 */
		do_action( "{$plugin_name}/factory/premium/license_activate", 'freemius', $license_info );

		return true;
	}

	/**
	 * Деактивирует лицензию
	 *
	 * @return bool
	 * @throws \Freemius_Exception
	 * @throws Exception
	 */
	public function deactivate() {
		if ( ! $this->is_activate_license ) {
			return true;
		}

		$plugin_name  = $this->plugin->getPluginName();
		$license_info = [
			'provider'        => 'freemius',
			'is_active'       => $this->is_active(),
			'license_key'     => $this->get_license()->get_key(),
			'expiration_time' => $this->get_license()->get_expiration_time()
		];

		$site_api = $this->get_api_site_scope( $this->license_site );
		$user_api = $this->get_api_user_scope( $this->license_user );

		$site_api->call( $this->get_license_endpoint( $this->license ) . '.json?license_key=' . $this->license->get_key(), 'DELETE' );
		$user_api->call( $this->get_plugin_endpoint() . '/installs.json?ids=' . $this->license_site->id, 'DELETE' );

		// todo: добавить обработку ошибок

		$this->delete_license_data();

		/**
		 * Дейтсвие сработает после того, как лицензия будет успешно деактивирована
		 *
		 * @since 1.0.9 Изменил имя хука на {$plugin_name}/factory/premium/license_deactivate
		 * @since 1.0.0 Добавлен
		 *
		 * @param string $provider       Провайдер лицензии
		 * @param string $license_info   Дополнительная информация о лицензии
		 */
		do_action( "{$plugin_name}/factory/premium/license_deactivate", 'freemius', $license_info );

		return true;
	}

	/**
	 * Синхронизирует данные текущей лицензии.
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function sync() {
		if ( ! $this->is_activate_license ) {
			return false;
		}

		$site_api = $this->get_api_site_scope( $this->license_site );
		$user_api = $this->get_api_user_scope( $this->license_user );

		$request_install = $site_api->call( '/', 'GET' );

		// Если установка не найдена или неактивна, деактивируем лицензию
		if ( isset( $request_install->error ) || ! ( isset( $request_install->is_active ) && $request_install->is_active ) ) {
			$this->deactivate();

			return true;
		}

		$use_license_key      = urlencode( $this->license->secret_key );
		$request_license_path = $this->get_license_endpoint( $this->license ) . '.json?license_key=' . $use_license_key;
		$request_license      = $site_api->call( $request_license_path, 'GET' );

		// Если лицензия не найдена или неактивна или тарифный план не совпадает с текущей установкой,
		// деактивируем лицензию.
		if ( isset( $request_license->error ) || ! ( isset( $request_license->plan_id ) && $request_license->plan_id == $request_install->plan_id ) ) {
			$this->deactivate();

			return true;
		}

		$request_subscriptions_path = $this->get_license_endpoint( $this->license ) . '/subscriptions.json';
		$request_subscriptions      = $site_api->call( $request_subscriptions_path, 'GET' );

		$request_plan_path = $this->get_plugin_endpoint() . '/plans/' . $this->license->plan_id . '.json';
		$request_plan      = $user_api->call( $request_plan_path, 'GET' );

		$this->license->plan_title = $request_plan->title;

		if ( isset( $request_subscriptions->subscriptions ) && isset( $request_subscriptions->subscriptions[0] ) ) {
			if ( ! is_null( $request_subscriptions->subscriptions[0]->next_payment ) ) {
				$this->license->billing_cycle = $request_subscriptions->subscriptions[0]->billing_cycle;
			}
		}

		$this->license->populate( $request_license );
		$this->save_license_data();

		// Обновляем информацию о сайте и сервере пользователя
		$site_api->call( '/', 'put', [
			'id'                           => $this->license_site->id,
			'uid'                          => $this->get_unique_site_id(),
			'plugin_version'               => $this->plugin->getPluginVersion(),
			'language'                     => get_bloginfo( 'language' ),
			'charset'                      => get_bloginfo( 'charset' ),
			'platform_version'             => get_bloginfo( 'version' ),
			'sdk_version'                  => '2.2.3',
			'programming_language_version' => phpversion()
		] );

		$plugin_name  = $this->plugin->getPluginName();
		$license_info = [
			'provider'        => 'freemius',
			'is_active'       => $this->is_active(),
			'license_key'     => $this->get_license()->get_key(),
			'expiration_time' => $this->get_license()->get_expiration_time()
		];

		/**
		 * Выполняется, когда синхронизация завершена успешно, без деактивации
		 *
		 * @since 1.0.9 Изменил имя хука на {$plugin_name}/factory/premium/license_sync
		 * @since 1.0.0 Добавлен
		 *
		 * @param string $license_info   Дополнительная информация о лицензии
		 */
		do_action( "{$plugin_name}/factory/premium/license_sync", $license_info );

		return true;
	}

	/**
	 * Используется ли платная подписка на обновления плагина.
	 *
	 * @return bool
	 */
	public function has_paid_subscription() {
		if ( ! $this->is_activate_license ) {
			return false;
		}

		return ! empty( $this->license->billing_cycle );
	}

	/**
	 * Отменяет платную подписку в freemius.com
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function cancel_paid_subscription() {
		if ( ! $this->is_activate_license ) {
			return false;
		}

		$site_api = $this->get_api_site_scope( $this->license_site );

		$request_subscriptions = $site_api->call( $this->get_license_endpoint( $this->license ) . '/subscriptions.json', 'GET' );

		if ( isset( $request_subscriptions->subscriptions ) && isset( $request_subscriptions->subscriptions[0] ) ) {
			$site_api->call( 'downgrade.json', 'PUT' );
			$this->license->billing_cycle = null;
			$this->save_license_data();
		}

		return true;
	}

	/**
	 * Отписывается от платной подписики на обновления
	 *
	 * @return WP_Error
	 */
	/*public function unsubscribe() {
	
	}*/

	// GETTERS SECTION
	// -----------------------------------------------------------------------------------

	/**
	 * Unique site identifier (Hash).
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.1.0
	 *
	 * @param null|int $blog_id   Since 2.0.0
	 *
	 * @return string
	 */
	protected function get_unique_site_id() {
		$key = str_replace( [ 'http://', 'https://' ], '', get_site_url() );

		$secure_auth = SECURE_AUTH_KEY;
		if ( empty( $secure_auth ) || false !== strpos( $secure_auth, ' ' ) ) {
			// Protect against default auth key.
			$secure_auth = md5( microtime() );
		}

		/**
		 * Base the unique identifier on the WP secure authentication key. Which
		 * turns the key into a secret anonymous identifier. This will help us
		 * to avoid duplicate installs generation on the backend upon opt-in.
		 *
		 * @author Vova Feldman (@svovaf)
		 * @since  1.2.3
		 */
		$unique_id = md5( $key . $secure_auth );

		return $unique_id;
	}

	/**
	 * Get client IP.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.1.2
	 *
	 * @return string|null
	 */
	protected function get_user_ip() {
		$fields = [
			'HTTP_CF_CONNECTING_IP',
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		];

		foreach ( $fields as $ip_field ) {
			if ( ! empty( $_SERVER[ $ip_field ] ) ) {
				return $_SERVER[ $ip_field ];
			}
		}

		return null;
	}

	/**
	 * @param bool $flush
	 *
	 * @return \WBCR\Factory_Freemius_111\Api
	 * @throws Exception
	 */
	private function get_api_user_scope( User $user, $flush = false ) {
		if ( ! isset( $this->user_api ) || $flush ) {
			$this->user_api = Api::instance( $this->plugin, 'user', $user->id, $user->public_key, false, $user->secret_key );
		}

		return $this->user_api;
	}

	/**
	 * @param bool $flush
	 *
	 * @return \WBCR\Factory_Freemius_111\Api
	 * @throws Exception
	 */
	private function get_api_site_scope( Site $site, $flush = false ) {
		if ( ! isset( $this->site_api ) || $flush ) {
			$this->site_api = Api::instance( $this->plugin, 'install', $site->id, $site->public_key, false, $site->secret_key );
		}

		return $this->site_api;
	}

	/**
	 * Get plugin public API scope.
	 *
	 * @return \WBCR\Factory_Freemius_111\Api
	 * @throws Exception
	 */
	private function get_api_plugin_scope() {
		if ( ! isset( $this->plugin_api ) ) {
			$this->plugin_api = Api::instance( $this->plugin, 'plugin', $this->plugin_id, $this->public_key, false );
		}

		return $this->plugin_api;
	}

	/**
	 * @param License $license
	 *
	 * @return string
	 */
	private function get_license_endpoint( License $license ) {
		return '/licenses/' . $license->id;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	private function get_plugin_endpoint() {
		return '/plugins/' . $this->plugin_id;
	}

	// END GETTERS SECTION
	// -----------------------------------------------------------------------------------

	/**
	 * @return void
	 * @throws Exception
	 */
	private function init_license( $license = null, $license_user = null, $license_site = null, $license_plugin = null ) {

		if ( $this->is_activate_license ) {
			return;
		}

		if ( $license instanceof License && $license_user instanceof User && $license_site instanceof Site ) {
			$this->license        = $license;
			$this->license_site   = $license_site;
			$this->license_user   = $license_user;
			$this->license_plugin = $license_plugin;
		} else {
			$license_data = $this->plugin->getPopulateOption( 'license', [] );

			if ( empty( $license_data ) || ! ( isset( $license_data['license'] ) && isset( $license_data['site'] ) && isset( $license_data['user'] ) ) ) {
				return;
			}

			$this->license      = new License( $license_data['license'] );
			$this->license_site = new Site( $license_data['site'] );
			$this->license_user = new User( $license_data['user'] );

			if ( isset( $license_data['plugin'] ) ) {
				$this->license_plugin = new Plugin( $license_data['plugin'] );
			}
		}

		if ( $this->license->id && $this->license_site->id && $this->license_user->id ) {
			$this->is_activate_license = true;
		}
	}

	/**
	 * Сбрасывает всю объектную информацию о лицензии
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function flush_license_data() {

		$this->is_activate_license = false;
		$this->license             = null;
		$this->license_site        = null;
		$this->license_user        = null;
		$this->license_plugin      = null;

		$this->user_api = null;
		$this->site_api = null;
	}

	/**
	 * Сбрасывает всю объектную информацию о лицензии и удаляет
	 * данные о лицензии из базы данных.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function delete_license_data() {
		$this->flush_license_data();

		$this->plugin->deletePopulateOption( 'license' );
	}

	/**
	 * Сохраняет лицензионные данные в базе данных, если данные
	 * уже есть в базе данных, метод просто обновляет их.
	 */
	private function save_license_data() {
		if ( ! $this->license || ! $this->license_site || ! $this->license_user ) {
			return;
		}

		$save_data = [
			'license' => $this->license->to_array(),
			'site'    => $this->license_site->to_array(),
			'user'    => $this->license_user->to_array()
		];

		if ( ! empty( $this->license_plugin ) ) {
			$save_data['plugin'] = $this->license_plugin->to_array();
		}

		$this->plugin->updatePopulateOption( 'license', $save_data );
	}
}