<?php

namespace WBCR\Factory_439\Premium;

use Exception;
use Wbcr_Factory439_Plugin;

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

/**
 * Выполняет проверку обновлений, обновления, скачивание плагинов
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, repo: https://github.com/alexkovalevv
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, site: https://webcraftic.com
 *
 * @version       1.0
 */
abstract class Provider {

	/**
	 * @var Wbcr_Factory439_Plugin
	 */
	protected $plugin;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var bool
	 */
	private $is_install_package;

	/**
	 * Provider constructor.
	 *
	 * @param Wbcr_Factory439_Plugin $plugin
	 * @param array $settings
	 */
	public function __construct(Wbcr_Factory439_Plugin $plugin, array $settings)
	{
		$this->plugin = $plugin;
		$this->settings = $settings;

		$this->register_hooks();
	}

	/**
	 * @return array
	 */
	public function get_settings()
	{
		return $this->settings;
	}

	/**
	 * @param      $name
	 * @param bool $default
	 *
	 * @return bool|mixed
	 */
	public function get_setting($name, $default = false)
	{
		return isset($this->settings[$name]) && !empty($this->settings[$name]) ? $this->settings[$name] : $default;
	}

	/**
	 * @return bool|mixed
	 */
	public function get_price()
	{
		return $this->get_setting('price');
	}

	/**
	 * @return bool
	 */
	public function is_install_package()
	{
		if( !is_null($this->is_install_package) ) {
			return $this->is_install_package;
		}

		$premium_plugin_slug = $this->get_setting('slug');
		$premium_package = $this->get_package_data();

		if( !empty($premium_package) && !empty($premium_package['basename']) ) {
			$basename_part = explode('/', $premium_package['basename']);
			$is_valid_basename = sizeof($basename_part) === 2;

			if( $is_valid_basename && !file_exists(WP_PLUGIN_DIR . '/' . $premium_package['basename']) ) {
				$this->delete_package();
				$this->is_install_package = false;

				return false;
			}
		} else if( $premium_plugin_slug && file_exists(WP_PLUGIN_DIR . '/' . $premium_plugin_slug) ) {
			// If plugin is installed before we update the premium package in database.
			// ------------------------------------------------------------------------
			$plugins = get_plugins($plugin_folder = '');

			if( !empty($plugins) ) {
				foreach((array)$plugins as $plugin_base => $plugin) {
					$basename_parts = explode('/', $plugin_base);
					if( sizeof($basename_parts) == 2 && $basename_parts[0] == $premium_plugin_slug ) {

						$plugin_basename = $plugin_base;
						$plugin_main_file = WP_PLUGIN_DIR . '/' . $plugin_base;

						$default_headers = [
							'Version' => 'Version',
							'FrameworkVersion' => 'Framework Version'
						];

						$plugin_data = get_file_data($plugin_main_file, $default_headers, 'plugin');

						$this->update_package_data([
							'basename' => $plugin_basename,
							'version' => $plugin_data['Version'],
							'framework_version' => isset($plugin_data['FrameworkVersion']) ? $plugin_data['FrameworkVersion'] : null,
						]);

						$premium_package = $this->get_package_data();

						/**
						 * @param string $plugin_name Имя плагина
						 *
						 * @param string $package Дополнительная информация о лицензии
						 * @since 4.1.1
						 *
						 */
						do_action('wbcr/factory/premium/installed_package', $premium_package, $this->plugin->getPluginName());
					}
				}
			}
		}

		$this->is_install_package = !empty($premium_package);

		return $this->is_install_package;
	}

	/**
	 * @return bool|mixed|null
	 */
	public function get_package_data()
	{
		$premium_package = $this->plugin->getPopulateOption('premium_package');

		if( !empty($premium_package) ) {
			return wp_parse_args($premium_package, [
				'basename' => null,
				'version' => null,
				'framework_version' => null
			]);
		}

		return null;
	}

	/**
	 * @param $plugin_data
	 *
	 * @throws Exception
	 */
	public function update_package_data(array $package)
	{
		$parsed_args = wp_parse_args($package, [
			'basename' => null,
			'version' => null,
			'framework_version' => null
		]);

		if( empty($parsed_args['basename']) || empty($parsed_args['version']) ) {
			throw new Exception('You must pass the required attributes (basename, version).');
		}

		$this->plugin->updatePopulateOption('premium_package', $parsed_args);
		$this->is_install_package = true;
	}

	public function delete_package()
	{
		$this->plugin->deletePopulateOption('premium_package');
		$this->is_install_package = false;
	}

	protected function register_hooks()
	{
		/**
		 * Добавляет крон задачу на синхронизацию лицензии
		 *
		 * @param array $license_info
		 * @param string $provider
		 */
		add_action("{$this->plugin->getPluginName()}/factory/premium/license_activate", function ($provider, $license_info) {
			if( !wp_next_scheduled("{$this->plugin->getPluginName()}_license_autosync") ) {
				wp_schedule_event(time(), 'twicedaily', "{$this->plugin->getPluginName()}_license_autosync");
			}
		}, 10, 2);

		/**
		 * Удаляет крон задачу на синхронизацию лицензии, когда лицензия деактивирована
		 *
		 * @param array $license_info
		 * @param string $provider
		 */
		add_action("{$this->plugin->getPluginName()}/factory/premium/license_deactivate", function ($provider, $license_info) {
			if( wp_next_scheduled("{$this->plugin->getPluginName()}_license_autosync") ) {
				wp_clear_scheduled_hook("{$this->plugin->getPluginName()}_license_autosync");
			}
		}, 10, 2);

		/**
		 * Обработчик крон задачи на синхронизацию лицензии, выполняется 2 раза в день.
		 */
		add_action("{$this->plugin->getPluginName()}_license_autosync", function () {
			$this->sync();
		});
	}

	/**
	 * @return bool
	 */
	abstract public function is_activate();

	/**
	 * @return bool
	 */
	abstract public function is_active();

	/**
	 * @return string|null
	 */
	abstract public function get_plan();

	/**
	 * @return string|null
	 */
	abstract public function get_billing_cycle();

	/**
	 * @return \WBCR\Factory_439\Premium\Interfaces\License
	 */
	abstract public function get_license();

	/**
	 * @return string|null
	 */
	abstract public function get_package_download_url();

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	abstract public function activate($key);

	/**
	 * @return bool
	 */
	abstract public function deactivate();

	/**
	 * @return bool
	 */
	abstract public function sync();

	/**
	 * @return bool
	 */
	abstract public function has_paid_subscription();

	/**
	 * @return bool
	 */
	abstract public function cancel_paid_subscription();

}