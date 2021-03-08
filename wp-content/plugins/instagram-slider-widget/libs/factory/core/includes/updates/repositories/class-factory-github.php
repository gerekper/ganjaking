<?php

namespace WBCR\Factory_445\Updates;

// Exit if accessed directly
use Wbcr_Factory445_Plugin;

if( !defined('ABSPATH') ) {
	exit;
}

/**
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, repo: https://github.com/alexkovalevv
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, site: https://webcraftic.com
 * @copyright (c) 2018 Webraftic Ltd
 * @version       1.0
 */
class Github_Repository extends Repository {

	/**
	 * Токен доступа
	 *
	 * Требуется, если репозиторий закрытый
	 * @var string
	 * @since 4.4.1
	 */
	protected $github_authorize_token;

	/**
	 * Имя пользователя
	 * @var string
	 * @since 4.4.1
	 */
	protected $github_username;

	/**
	 * Имя репозитория в Github
	 * @var string
	 * @since 4.4.1
	 */
	protected $github_repository;

	/**
	 * Имя репозитория
	 * @var string
	 * @since 4.4.1
	 */
	protected $plugin_slug;

	/**
	 * Кешируем результаты запроса в этот параметр
	 * @var array
	 * @since 4.4.1
	 */
	private $release_info;

	/**
	 * Wordpress constructor.
	 *
	 * @param Wbcr_Factory445_Plugin $plugin
	 * @param bool $is_premium
	 * @since 4.4.1
	 */
	public function __construct(Wbcr_Factory445_Plugin $plugin, array $settings = [])
	{
		$settings = wp_parse_args($settings, [
			'github_username' => '',
			'github_authorize_token' => '',
			'github_repository' => ''
		]);

		if( empty($settings['github_username']) ) {
			throw new \Exception('You are trying to connect a github repository for plugin updates. You must enter the username of the github repository!');
		}

		$this->plugin = $plugin;
		$this->plugin_basename = $this->plugin->get_paths()->basename;
		$this->plugin_main_file = $this->plugin->get_paths()->main_file;
		$this->plugin_absolute_path = $this->plugin->get_paths()->absolute;
		$this->plugin_slug = $settings['slug'];

		$this->github_authorize_token = $settings['github_authorize_token'];
		$this->github_username = $settings['github_username'];
		$this->github_repository = empty($settings['github_repository']) ? $this->plugin_slug : $settings['github_repository'];
	}

	public function init()
	{
		//add_filter('upgrader_source_selection', array($this, 'change_source_package'), 10, 4);
	}

	/**
	 * @return bool
	 * @since 4.4.1
	 */
	public function need_check_updates()
	{
		return true;
	}

	/**
	 * Если этот репозиторий используется для обновлени премиум плагина,
	 * нужно установить true. В нашем случае Github используется только
	 * для обновлений бесплатного плагина.
	 * @return bool
	 * @since 4.4.1
	 */
	public function is_support_premium()
	{
		return false;
	}

	/**
	 * Метод получает ссылку на скачивание последнего релиза в Github
	 * @return string
	 * @since 4.4.1
	 */
	public function get_download_url()
	{
		try {
			$response = $this->get_last_release_info();

			if( empty($response['assets']) ) {
				throw new \Exception('You must upload the plugin archive to the github as a binary file.');
			}

			foreach($response['assets'] as $asset) {
				if( false !== strpos($asset['name'], $this->plugin_slug) && "application/zip" === $asset['content_type'] ) {
					if( $this->github_authorize_token ) { // Is there an access token?
						$asset['browser_download_url'] = add_query_arg('access_token', $this->github_authorize_token, $asset['browser_download_url']); // Update our zip url with token
					}

					return $asset['browser_download_url'];
				}
			}
		} catch( \Exception $e ) {
			if( defined('FACTORY_UPDATES_DEBUG') && FACTORY_UPDATES_DEBUG ) {
				throw new \Exception($e->getMessage(), $e->getCode());
			}
		}

		return null;
	}

	/**
	 * Метод получает версию последнего релиза плагина загруженного на Github
	 * @return string|null Возвращает версию 1.2.3, в случае ошибки null
	 * @since 4.4.1
	 */
	public function get_last_version()
	{
		try {
			$response = $this->get_last_release_info();

			return $response['tag_name'];
		} catch( \Exception $e ) {
			if( defined('FACTORY_UPDATES_DEBUG') && FACTORY_UPDATES_DEBUG ) {
				throw new \Exception($e->getMessage(), $e->getCode());
			}
		}

		return null;
	}

	/**
	 * Checks that the package source contains .mo and .po files.
	 *
	 * Hooked to the {@see 'upgrader_source_selection'} filter by
	 * Language_Pack_Upgrader::bulk_upgrade().
	 *
	 * @param string|\WP_Error $source The path to the downloaded package source.
	 * @param string $remote_source Remote file source location.
	 * @return string|\WP_Error The source as passed, or a WP_Error object on failure.
	 * @global \WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
	 *
	 * @since 4.4.1
	 *
	 */
	/*public function change_source_package($source, $remote_source, $upgrader, $hook_extra)
	{
		global $wp_filesystem;

		if( is_wp_error($source) ) {
			return $source;
		}

		if( !empty($hook_extra) && "plugin" === $hook_extra['type'] && "update" === $hook_extra['action'] && basename($source) === $this->plugin_slug ) {
			$new_source = $wp_filesystem->wp_content_dir() . 'upgrade/' . $this->plugin_slug;

			$wp_filesystem->move($source, $new_source);

			return $new_source;
		}

		return $source;
	}*/

	/**
	 * Метод получает информацию о последнем релизе на Github
	 *
	 * Имена релизов и тегов на Github строго должны соотвествовать версии загруженного релиза.
	 * К примеру 0.0.0 - правильное именование, v0.0.0 - будет ошибкой
	 *
	 * @return mixed
	 * @throws \Exception
	 * @since 4.4.1
	 */
	protected function get_last_release_info()
	{
		if( !empty($this->release_info) ) {
			return $this->release_info;
		}

		$request_uri = sprintf('https://api.github.com/repos/%s/%s/releases', $this->github_username, $this->github_repository); // Build URI

		if( $this->github_authorize_token ) { // Is there an access token?
			$request_uri = add_query_arg('access_token', $this->github_authorize_token, $request_uri); // Append it
		}

		$response = wp_remote_get($request_uri);

		if( is_wp_error($response) ) {
			throw new \Exception($response->get_error_message(), $response->get_error_code());
		}

		$data = @json_decode(wp_remote_retrieve_body($response), true); // Get JSON and parse it

		if( empty($data) ) {
			throw new \Exception("Failed to decode received data:" . json_last_error());
		}

		if( isset($data['message']) ) {
			throw new \Exception("It is not possible to get release information in the github repository. The repository may not exist. Received error text: " . $data['message']);
		}

		uasort($data, function ($a, $b) {
			return version_compare($b['tag_name'], $a['tag_name']);
		});

		$data = current($data); // Get the first item

		$this->release_info = $data;

		return $data;
	}
}