<?php

namespace WBCR\Factory_439\Updates;

use Exception;
use Plugin_Installer_Skin;
use Plugin_Upgrader;
use Wbcr_Factory439_Plugin;
use Wbcr_FactoryPages438_ImpressiveThemplate;
use WP_Filesystem_Base;
use WP_Upgrader;
use WP_Upgrader_Skin;

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

require_once ABSPATH . "/wp-admin/includes/list-table.php";
/**
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, repo: https://github.com/alexkovalevv
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, site: https://webcraftic.com
 * @copyright (c) 2018 Webraftic Ltd
 * @version       1.0
 */
class Premium_Upgrader extends Upgrader {

	/**
	 * Тип апгрейдера, может быть default, premium
	 *
	 * @var string
	 */
	protected $type = 'premium';

	/**
	 * Объект таблицы со списком плагинов
	 *
	 * @var \WP_Plugins_List_Table
	 */
	protected $wp_list_table;

	/**
	 * Manager constructor.
	 *
	 * @param                        $args
	 * @param bool $is_premium
	 *
	 * @param Wbcr_Factory439_Plugin $plugin
	 *
	 * @throws Exception
	 * @since 4.1.1
	 *
	 */
	public function __construct(Wbcr_Factory439_Plugin $plugin)
	{
		parent::__construct($plugin);

		$this->plugin_basename = null;
		$this->plugin_main_file = null;
		$this->plugin_absolute_path = null;

		if( $this->plugin->premium->is_activate() && $this->plugin->premium->is_install_package() ) {
			$premium_package = $this->plugin->premium->get_package_data();

			if( $premium_package ) {
				$this->plugin_basename = $premium_package['basename'];
				$this->plugin_main_file = WP_PLUGIN_DIR . '/' . $premium_package['basename'];
				$this->plugin_absolute_path = dirname(WP_PLUGIN_DIR . '/' . $premium_package['basename']);
			}
		}

		if( !$this->repository->is_support_premium() ) {
			$settings = $this->get_settings();
			throw new Exception("Repository {$settings['repository']} does not have support premium.");
		}

		$this->init_premium_hooks();
	}

	/**
	 * @throws Exception
	 */
	protected function set_repository()
	{
		$settings = $this->get_settings();
		$this->repository = $this->get_repository($settings['repository']);

		if( $this->plugin->premium->is_activate() ) {
			$this->repository->init();
		}
	}

	/**
	 * @throws Exception
	 * @since 4.1.1
	 */
	protected function init_premium_hooks()
	{
		//parent::init_hooks();

		if( $this->need_intall_or_activate_premium() || $this->need_renew_license() || $this->need_activate_license() ) {
			// Показываем уведомление под бесплатным плагином, если требуется установить или активировать премиум пакет
			if( $this->need_intall_or_activate_premium() ) {
				$free_plugin_base = $this->plugin->get_paths()->basename;

				add_action("after_plugin_row_{$free_plugin_base}", [$this, "notice_in_plugin_row"], 100, 3);
			}

			// Если установлен премиум пакет, то показываем уведомление под премиум плагином.
			if( ($this->need_renew_license() || $this->need_activate_license()) && $this->plugin->premium->is_install_package() ) {
				$package = $this->plugin->premium->get_package_data();
				$premium_plugin_base = $package['basename'];

				add_action("after_plugin_row_{$premium_plugin_base}", [$this, "notice_in_plugin_row"], 100, 3);
			}

			add_action("admin_print_styles-plugins.php", [$this, "print_styles_for_plugin_row"]);
			add_action("wbcr/factory/admin_notices", [$this, "admin_notices_hook"], 10, 2);
			add_action('wbcr/factory/pages/impressive/print_all_notices', [
				$this,
				'install_notice_in_plugin_interface'
			], 10, 2);
		}

		add_action('admin_init', [$this, 'init_admin_actions']);

		add_action('deleted_plugin', [$this, 'delete_plugin_hook'], 10, 2);
		add_action('upgrader_process_complete', [$this, 'upgrader_process_complete_hook'], 10, 2);
	}

	/**
	 * @since 4.2.2 Fixed bug with plugins namespace (ISW-4)
	 * @since 4.1.1
	 */
	public function init_admin_actions()
	{
		$plugin_slug = $this->plugin->request->get('wfactory_premium_updates_plugin', null);

		if( isset($_GET['wfactory_premium_updates_action']) && $this->plugin_slug === $plugin_slug ) {
			$action = $this->plugin->request->get('wfactory_premium_updates_action');

			check_admin_referer("factory_premium_{$action}");
			try {
				switch( $action ) {
					case 'install':
						$this->install();
						break;
					case 'deactivate':
						$this->deactivate();
						break;
					case 'delete':
						$this->delete();
						break;
					case 'check_updates':
						$this->check_updates();
						break;
					case 'cancel_license':
						$this->plugin->premium->deactivate();

						break;
				}
			} catch( Exception $e ) {
				wp_die($e->getMessage());
			}
		}
	}

	/**
	 * Удаляет данные о пакете, если пользовать удалил премиум плагин
	 *
	 * @param $success
	 *
	 * @param $plugin_basename
	 * @since 4.1.1
	 *
	 */
	public function delete_plugin_hook($plugin_basename, $success)
	{
		if( !$this->plugin->premium->is_install_package() ) {
			return;
		}

		$package = $this->plugin->premium->get_package_data();

		if( $package['basename'] == $plugin_basename && $success ) {
			$this->plugin->premium->delete_package();
		}
	}

	/**
	 * Выводит уведомление на всех страницах админ панели Wordpress
	 *
	 * @param $notices
	 *
	 * @return array
	 * @since 4.1.1
	 *
	 */
	public function admin_notices_hook($notices, $plugin_name)
	{

		if( $plugin_name !== $this->plugin->getPluginName() || !current_user_can('update_plugins') ) {
			return $notices;
		}

		if( $this->need_intall_or_activate_premium() ) {
			$notice_text = $this->get_notice_text('please_activate_premium');

			if( !$this->plugin->premium->is_install_package() ) {
				$notice_text = $this->get_notice_text('please_install_premium');
			}

			$notices[] = [
				'id' => 'please_install_premium_for_' . $this->plugin->getPluginName(),
				'type' => 'warning',
				'dismissible' => false,
				'dismiss_expires' => 0,
				'text' => "<p><b>{$this->plugin->getPluginTitle()}:</b> " . $notice_text . '</p>'
			];
		} else if( $this->need_activate_license() ) {
			$notices[] = [
				'id' => 'need_activate_premium_for_' . $this->plugin->getPluginName(),
				'type' => 'warning',
				'dismissible' => false,
				'dismiss_expires' => 0,
				'text' => "<p><b>{$this->plugin->getPluginTitle()}:</b> " . $this->get_notice_text('need_activate_license') . '</p>'
			];
		} else if( $this->need_renew_license() ) {
			// todo: может быть перенести уведомление в премиум менеджер?
			$notices[] = [
				'id' => 'license_exired_for_' . $this->plugin->getPluginName(),
				'type' => 'warning',
				'dismissible' => false,
				'dismiss_expires' => 0,
				'text' => "<p><b>{$this->plugin->getPluginTitle()}:</b> " . $this->get_notice_text('need_renew_license') . '</p>'
			];
		}

		return $notices;
	}

	/**
	 * Выводит уведомление внутри интерфейса плагина, на всех страницах плагина.
	 *
	 * @param Wbcr_FactoryPages438_ImpressiveThemplate $obj
	 *
	 * @param Wbcr_Factory439_Plugin $plugin
	 *
	 * @return void
	 * @since 4.1.1
	 *
	 */
	public function install_notice_in_plugin_interface($plugin, $obj)
	{
		if( $plugin->getPluginName() != $this->plugin->getPluginName() ) {
			return;
		}

		$notice_text = '';

		if( $this->need_intall_or_activate_premium() ) {
			$notice_text = $this->get_notice_text('please_activate_premium');

			if( !$this->plugin->premium->is_install_package() ) {
				$notice_text = $this->get_notice_text('please_install_premium');
			}
		} else if( $this->need_activate_license() ) {
			$notice_text = $this->get_notice_text('need_activate_license');
		} else if( $this->need_renew_license() ) {
			$notice_text = $this->get_notice_text('need_renew_license');
		}

		$obj->printWarningNotice($notice_text);
	}

	/**
	 * Выводит уведомление в строке плагина (на странице плагинов),
	 * что нужно установить премиум плагин.
	 *
	 * @param array $plugin_data
	 * @param string $status
	 *
	 * @param string $plugin_file
	 *
	 * @return void
	 * @since 4.1.1
	 *
	 * @see   WP_Plugins_List_Table
	 *
	 */
	public function notice_in_plugin_row($plugin_file, $plugin_data, $status)
	{

		if( !current_user_can('update_plugins') ) {
			return;
		};

		$notice_text = '';

		if( $this->need_intall_or_activate_premium() ) {
			$notice_text = $this->get_notice_text('please_activate_premium');

			if( !$this->plugin->premium->is_install_package() ) {
				$notice_text = $this->get_notice_text('please_install_premium');
			}
		} else if( $this->need_activate_license() ) {
			$notice_text = $this->get_notice_text('need_activate_license');
		} else if( $this->need_renew_license() ) {
			$notice_text = $this->get_notice_text('need_renew_license');
		}

		if( !$this->wp_list_table ) {
			$this->wp_list_table = \_get_list_table(
				'WP_Plugins_List_Table',
				array(
					'screen' => null,
				)
			);
		}

		?>
		<tr class="plugin-update-tr active update wbcr-factory-updates">
			<td colspan="<?php echo esc_attr($this->wp_list_table->get_column_count());?>" class="plugin-update colspanchange">
				<div class="update-message notice inline notice-warning notice-alt">
					<p>
						<?php echo $notice_text; ?>
					</p>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Печатает стили для уведомления о загрузке премиум версии на странице плагинов.
	 *
	 * @return void
	 * @since 4.1.1
	 */
	public function print_styles_for_plugin_row()
	{

		if( !current_user_can('update_plugins') ) {
			return;
		}

		$plugin_base = $this->plugin->get_paths()->basename;

		if( $this->need_intall_or_activate_premium() ) {
			$message_background_color = '#f5e9f5';
			$message_border_color = '#dab9da';
		} else if( $this->need_renew_license() || $this->need_activate_license() ) {
			$message_background_color = '#ffe2e0';
			$message_border_color = '#F44336';
			if( $this->plugin->premium->is_install_package() ) {
				$package = $this->plugin->premium->get_package_data();
				$plugin_base = $package['basename'];
			}
		}

		?>
		<style>
			tr[data-plugin="<?php echo $plugin_base; ?>"] th,
			tr[data-plugin="<?php echo $plugin_base; ?>"] td {
				box-shadow: none !important;
			}

			.wbcr-factory-updates .update-message {
				background-color: <?php echo esc_attr($message_background_color); ?> !important;
				border-color: <?php echo esc_attr($message_border_color); ?> !important;
			}
		</style>
		<?php
	}


	/**
	 * Обновляет данные о премиум пакете в базе данных, после обновления плагина.
	 *
	 * @param WP_Upgrader $wp_upgrader WP_Upgrader instance.
	 * @param array $hook_extra Array of bulk item update data.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function upgrader_process_complete_hook($upgrader_object, $hook_extra)
	{
		if( !empty($hook_extra) && $hook_extra['action'] == 'update' && $hook_extra['type'] == 'plugin' ) {

			# if it isn't bulk upgrade
			if( isset($hook_extra['plugin']) && $this->plugin_basename === $hook_extra['plugin'] ) {
				$this->update_package_data();

				return;
			}

			# if it is bulk upgrade
			if( isset($hook_extra['plugins']) && in_array($this->plugin_basename, $hook_extra['plugins']) ) {
				$this->update_package_data();
			}
		}
	}

	/**
	 * @return array
	 * @since 4.1.1
	 */
	protected function get_settings()
	{
		$settings = $this->plugin->getPluginInfoAttr('license_settings');

		$updates_settings = isset($settings['updates_settings']) ? $settings['updates_settings'] : [];

		if( is_array($settings) ) {
			$updates_settings['repository'] = $settings['provider'];
			$updates_settings['slug'] = $settings['slug'];
		}

		return wp_parse_args($updates_settings, [
			'repository' => 'wordpress',
			'slug' => '',
			'maybe_rollback' => false,
			'rollback_settings' => [
				'prev_stable_version' => '0.0.0'
			]
		]);
	}

	/**
	 * @return string
	 * @since 4.1.1
	 */
	protected function get_plugin_version()
	{
		if( !$this->plugin->premium->is_install_package() ) {
			return '0.0.0';
		}

		$package = $this->plugin->premium->get_package_data();

		return $package['version'];
	}

	/**
	 * @param $args
	 *
	 * @return string
	 * @since 4.1.1
	 *
	 */
	protected function get_admin_url($args)
	{
		$url = admin_url('plugins.php', $args);

		if( $this->plugin->isNetworkActive() ) {
			$url = network_admin_url('plugins.php', $args);
		}

		return add_query_arg($args, $url);
	}

	/**
	 * @param string $action
	 *
	 * @return string
	 * @since 4.1.1
	 *
	 */
	protected function get_action_url($action)
	{
		$args = [
			'wfactory_premium_updates_action' => $action,
			'wfactory_premium_updates_plugin' => $this->plugin_slug
		];

		return wp_nonce_url($this->get_admin_url($args), "factory_premium_{$action}");
	}

	/**
	 * @return string
	 * @since 4.1.1
	 */
	protected function get_activate_premium_url()
	{
		$args = [
			'action' => 'activate',
			'plugin' => $this->plugin_basename,
		];

		return wp_nonce_url($this->get_admin_url($args), "activate-plugin_{$this->plugin_basename}");
	}

	/**
	 * Нужно установить или обновить премиум?
	 *
	 * @return bool
	 * @since 4.1.1
	 */
	protected function need_intall_or_activate_premium()
	{
		if( $this->plugin->premium->is_activate() && $this->plugin->premium->is_active() ) {
			if( $this->plugin->premium->is_install_package() && is_plugin_active($this->plugin_basename) ) {
				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * Требуется активировать лицензию?
	 *
	 * @return bool
	 * @since 4.1.1
	 */
	protected function need_activate_license()
	{
		return !$this->plugin->premium->is_activate() && $this->plugin->premium->is_install_package();
	}

	/**
	 * Нужно продлить лицензию?
	 *
	 * @return bool
	 * @since 4.1.1
	 */
	protected function need_renew_license()
	{
		return $this->plugin->premium->is_activate() && !$this->plugin->premium->is_active();
	}

	/**
	 * @throws Exception
	 * @since 4.1.1
	 */
	protected function install()
	{
		global $wp_filesystem;

		if( !current_user_can('install_plugins') ) {
			throw new Exception('Sorry, you are not allowed to install plugins on this site.', 'not_allowed_install_plugin');
		}

		if( $this->plugin->premium->is_install_package() ) {
			return;
		}

		if( !$wp_filesystem ) {
			if( !function_exists('WP_Filesystem') ) {
				require_once(ABSPATH . 'wp-admin/includes/file.php');
			}
			WP_Filesystem();
		}

		if( !WP_Filesystem(false, WP_PLUGIN_DIR) ) {
			throw new Exception('You are not allowed to edt folders/files on this site', 'not_allowed_edit_filesystem');
		} else {

			$download_url = $this->repository->get_download_url();

			/**
			 * @param string $plugin_name Имя плагина
			 *
			 * @param string $package Дополнительная информация о лицензии
			 * @since 4.1.1
			 *
			 */
			do_action('wbcr/factory/premium/install_package', $download_url, $this->plugin->getPluginName());

			// If plugin is installed before we update the premium package in database.
			// ------------------------------------------------------------------------
			//$plugins = get_plugins( $plugin_folder = '' );
			//
			//if ( ! empty( $plugins ) ) {
			//	foreach ( (array) $plugins as $plugin_base => $plugin ) {
			//		$basename_parts = explode( '/', $plugin_base );
			//		if ( sizeof( $basename_parts ) == 2 && $basename_parts[0] == $this->plugin_slug ) {
			//
			//			$this->plugin_basename      = $plugin_base;
			//			$this->plugin_main_file     = WP_PLUGIN_DIR . '/' . $plugin_base;
			//			$this->plugin_absolute_path = dirname( WP_PLUGIN_DIR . '/' . $plugin_base );
			//
			//			$this->update_package_data();
			//
			//			$package = $this->plugin->premium->get_package_data();
			//
			//			/**
			//			 * @since 4.1.1
			//			 *
			//			 * @param string $plugin_name   Имя плагина
			//			 *
			//			 * @param string $package       Дополнительная информация о лицензии
			//			 */
			//			do_action( 'wbcr/factory/premium/installed_package', $package, $this->plugin->getPluginName() );
			//
			//			return;
			//		}
			//	}
			//}
			// ------------------------------------------------------------------------

			require_once(ABSPATH . 'wp-admin/includes/file.php');
			require_once(ABSPATH . 'wp-admin/includes/misc.php');

			if( !class_exists('Plugin_Upgrader', false) ) {
				// Include required resources for the installation.
				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			}

			$skin_args = [
				'type' => 'web',
				'title' => sprintf('Installing plugin: %s', $this->plugin->getPluginTitle() . ' Premium'),
				'url' => esc_url_raw($download_url),
				'nonce' => 'install-plugin_' . $this->plugin_slug,
				'plugin' => '',
				'api' => null,
				'extra' => [
					'slug' => $this->plugin_slug
				],
			];

			require_once(ABSPATH . 'wp-admin/admin-header.php');

			if( !$this->plugin->premium->is_install_package() ) {
				$skin = new Plugin_Installer_Skin($skin_args);
			} else {
				$skin = new WP_Upgrader_Skin($skin_args);
			}

			$upgrader = new Plugin_Upgrader($skin);

			if( empty($download_url) ) {
				throw new Exception('You must pass the download url to upgrade up premium package.', "not_passed_download_url");
			}

			$install_result = $upgrader->install($download_url);

			include(ABSPATH . 'wp-admin/admin-footer.php');

			if( is_wp_error($install_result) ) {
				throw new Exception($install_result->get_error_message(), $install_result->get_error_code());
			} else if( is_wp_error($skin->result) ) {
				throw new Exception($skin->result->get_error_message(), $skin->result->get_error_code());
			} else if( is_null($install_result) ) {
				global $wp_filesystem;

				$error_code = 'unable_to_connect_to_filesystem';
				$error_message = 'Unable to connect to the filesystem. Please confirm your credentials.';

				// Pass through the error from WP_Filesystem if one was raised.
				if( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code() ) {
					$error_message = $wp_filesystem->errors->get_error_message();
				}

				throw new Exception($error_message, $error_code);
			}

			$this->plugin_basename = $upgrader->plugin_info();
			$this->plugin_main_file = WP_PLUGIN_DIR . '/' . $this->plugin_basename;
			$this->plugin_absolute_path = dirname(WP_PLUGIN_DIR . '/' . $this->plugin_basename);

			$this->update_package_data();

			$package = $this->plugin->premium->get_package_data();

			/**
			 * @param string $plugin_name Имя плагина
			 *
			 * @param string $package Дополнительная информация о лицензии
			 * @since 4.1.1
			 *
			 */
			do_action('wbcr/factory/premium/installed_package', $package, $this->plugin->getPluginName());

			die();
		}
	}

	/**
	 * @return bool
	 * @throws Exception
	 */
	protected function delete()
	{
		if( !$this->plugin->premium->is_install_package() ) {
			return false;
		}

		$package = $this->plugin->premium->get_package_data();

		/**
		 * @param string $plugin_name Имя плагина
		 *
		 * @param string $package Дополнительная информация о лицензии
		 * @since 4.1.1
		 *
		 */
		do_action('wbcr/factory/premium/delete_package', $package, $this->plugin->getPluginName());

		if( is_plugin_active($package['basename']) ) {
			if( is_multisite() && is_plugin_active_for_network($package['basename']) ) {
				deactivate_plugins($package['basename'], false, true);
			} else {
				deactivate_plugins($package['basename']);
			}
		}

		$result = delete_plugins([$package['basename']]);

		if( is_wp_error($result) ) {
			throw new Exception($result->get_error_message(), $result->get_error_code());
		}

		$this->plugin->premium->delete_package();

		/**
		 * @param string $plugin_name Имя плагина
		 *
		 * @param string $package Дополнительная информация о лицензии
		 * @since 4.1.1
		 *
		 */
		do_action('wbcr/factory/premium/deleted_package', $package, $this->plugin->getPluginName());

		return true;
	}

	/**
	 * @return bool
	 * @since 4.1.1
	 */
	protected function deactivate()
	{
		if( !$this->plugin->premium->is_install_package() || !is_plugin_active($this->plugin_basename) ) {
			return false;
		}

		$package = $this->plugin->premium->get_package_data();

		/**
		 * @param string $plugin_name Имя плагина
		 *
		 * @param string $package Дополнительная информация о лицензии
		 * @since 4.1.1
		 *
		 */
		do_action('wbcr/factory/premium/deactivate_package', $package, $this->plugin->getPluginName());

		if( is_multisite() && is_plugin_active_for_network($this->plugin_basename) ) {
			deactivate_plugins($this->plugin_basename, false, true);
		} else {
			deactivate_plugins($this->plugin_basename);
		}

		/**
		 * @param string $plugin_name Имя плагина
		 *
		 * @param string $package Дополнительная информация о лицензии
		 * @since 4.1.1
		 *
		 */
		do_action('wbcr/factory/premium/deactivated_package', $package, $this->plugin->getPluginName());

		return true;
	}

	/**
	 * @param array $plugin_data
	 *
	 * @throws Exception
	 * @since 4.1.1
	 *
	 */
	protected function update_package_data()
	{

		if( !$this->plugin_main_file ) {
			return;
		}

		$default_headers = [
			'Version' => 'Version',
			'FrameworkVersion' => 'Framework Version'
		];

		$plugin_data = get_file_data($this->plugin_main_file, $default_headers, 'plugin');

		$this->plugin->premium->update_package_data([
			'basename' => $this->plugin_basename,
			'version' => $plugin_data['Version'],
			'framework_version' => isset($plugin_data['FrameworkVersion']) ? $plugin_data['FrameworkVersion'] : null,
		]);
	}

	/**
	 * @param string $type
	 *
	 * @return string|null
	 * @since 4.1.1
	 *
	 */
	private function get_notice_text($type)
	{
		$upgrade_url = $this->get_action_url('install');
		$activate_plugin_url = $this->get_activate_premium_url();
		$cancel_license_url = $this->get_action_url('cancel_license');

		$texts = [
			'need_activate_license' => __('License activation required. A license is required to get premium plugin updates, as well as to get additional services.', 'wbcr_factory_439'),
			'need_renew_license' => __('Your license has expired. You can no longer get premium plugin updates, premium support and your access to Webcraftic services has been suspended.', 'wbcr_factory_439'),
			'please_install_premium' => sprintf(__('Congratulations, you have activated a premium license! Please install premium add-on to use pro features now.
        <a href="%s">Install</a> premium add-on or <a href="%s">cancel</a> license.', 'wbcr_factory_439'), $upgrade_url, $cancel_license_url),
			'please_activate_premium' => sprintf(__('Congratulations, you have activated a premium license! Please activate premium add-on to use pro features now.
        <a href="%s">Activate</a> premium add-on or <a href="%s">cancel</a> license.', 'wbcr_factory_439'), $activate_plugin_url, $cancel_license_url)
		];

		if( isset($texts[$type]) ) {

			/**
			 * @param string $type
			 * @param string $plugin_name
			 *
			 * @param array $messages
			 * @since 4.1.1
			 *
			 */
			return apply_filters('wbcr/factory/premium/notice_text', $texts[$type], $type, $this->plugin->getPluginName());
		}

		return null;
	}
}