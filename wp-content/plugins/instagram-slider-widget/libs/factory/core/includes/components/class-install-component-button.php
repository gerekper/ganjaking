<?php

namespace WBCR\Factory_436\Components;

/**
 * This file groups the settings for quick setup
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>
 * @copyright (c) 16.09.2017, Webcraftic
 * @version       1.0
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

class Install_Button {

	protected $type;
	protected $plugin_slug;

	protected $classes = [
		'button',
		'wfactory-436-process-button'
	];
	protected $data = [];
	protected $base_path;

	protected $action;

	protected $url;

	/**
	 * @param string $group_name
	 *
	 * @throws \Exception
	 * @since  4.3.3
	 */
	public function __construct(\Wbcr_Factory436_Plugin $plugin, $type, $plugin_slug)
	{
		if( empty($type) || !is_string($plugin_slug) ) {
			throw new \Exception('Empty type or plugin_slug attribute.');
		}

		$this->plugin = $plugin;
		$this->type = $type;
		$this->plugin_slug = $plugin_slug;

		if( $this->type == 'wordpress' || $this->type == 'creativemotion' ) {
			if( strpos(rtrim(trim($this->plugin_slug)), '/') !== false ) {
				$this->base_path = $this->plugin_slug;
				$base_path_parts = explode('/', $this->base_path);
				if( sizeof($base_path_parts) === 2 ) {
					$this->plugin_slug = $base_path_parts[0];
				}
			} else {
				$this->base_path = $this->get_plugin_base_path_by_slug($this->plugin_slug);
			}

			$this->build_wordpress();
		} else if( $this->type == 'internal' ) {
			$this->build_internal();
		} else {
			throw new \Exception('Invalid button type.');
		}

		// Set default data
		$this->add_data('storage', $this->type);
		$this->add_data('i18n', \WbcrFactoryClearfy227_Helpers::getEscapeJson($this->get_i18n()));
		$this->add_data('wpnonce', wp_create_nonce('updates'));
	}

	// Удалить, через несколько версий!
	// Добавлено в 4.3.3
	//--------------------------------------------------------

	/**
	 * todo: для совместимости со старыми плагинами
	 * @return string
	 * @throws \Exception
	 * @since  4.3.3
	 */
	public function getLink()
	{
		return $this->get_link();
	}

	/**
	 * todo: для совместимости со старыми плагинами
	 * @return string
	 * @since  4.3.3
	 */
	public function getButton()
	{
		return $this->get_button();
	}

	/**
	 * Print an install a link
	 * todo: для совместимости со старыми плагинами
	 * @throws \Exception
	 * @since  4.3.3
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	public function renderLink()
	{
		echo $this->get_link();
	}

	/**
	 * Print an install button
	 * todo: для совместимости со старыми плагинами
	 *
	 * @throws \Exception
	 * @since  4.3.3
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	public function renderButton()
	{
		$this->render_button();
	}

	/**
	 * todo: для совместимости со старыми плагинами
	 * @param $class
	 *
	 * @throws \Exception
	 * @since  4.3.3
	 */
	public function addClass($class)
	{
		$this->add_class($class);
	}

	/**
	 * todo: для совместимости со старыми плагинами
	 * @param $class
	 *
	 * @return bool
	 * @throws \Exception
	 * @since  4.3.3
	 */
	public function removeClass($class)
	{
		return $this->remove_class($class);
	}

	//--------------------------------------------------------


	/**
	 * @return bool
	 * @since  4.3.3
	 */
	public function is_plugin_activate()
	{
		if( ($this->type == 'wordpress' || $this->type == 'creativemotion') && $this->is_plugin_install() ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';

			return is_plugin_active($this->base_path);
		} else if( $this->type == 'internal' ) {
			$preinsatall_components = $this->plugin->getPopulateOption('deactive_preinstall_components', []);

			return !in_array($this->plugin_slug, $preinsatall_components);
		}

		return false;
	}

	/**
	 * @return bool
	 * @since  4.3.3
	 */
	public function is_plugin_install()
	{
		if( $this->type == 'wordpress' || $this->type == 'creativemotion' ) {
			if( empty($this->base_path) ) {
				return false;
			}

			// Check if the function get_plugins() is registered. It is necessary for the front-end
			// usually get_plugins() only works in the admin panel.
			if( !function_exists('get_plugins') ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$plugins = get_plugins();

			if( isset($plugins[$this->base_path]) ) {
				return true;
			}
		} else if( $this->type == 'internal' ) {
			return true;
		}

		return false;
	}

	/**
	 * @param $class
	 *
	 * @throws \Exception
	 * @since  4.3.3
	 */
	public function add_class($class)
	{
		if( !is_string($class) ) {
			throw new \Exception('Attribute class must be a string.');
		}
		$this->classes[] = $class;
	}

	/**
	 * @param $class
	 *
	 * @return bool
	 * @throws \Exception
	 * @since  4.3.3
	 */
	public function remove_class($class)
	{
		if( !is_string($class) ) {
			throw new \Exception('Attribute class must be a string.');
		}
		$key = array_search($class, $this->classes);
		if( isset($this->classes[$key]) ) {
			unset($this->classes[$key]);

			return true;
		}

		return false;
	}

	/**
	 * @param $name
	 * @param $value
	 *
	 * @throws \Exception
	 * @since  4.3.3
	 */
	public function add_data($name, $value)
	{
		if( !is_string($name) || !is_string($value) ) {
			throw new \Exception('Attributes name and value must be a string.');
		}

		$this->data[$name] = $value;
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 * @throws \Exception
	 * @since  4.3.3
	 */
	public function remove_data($name)
	{
		if( !is_string($name) ) {
			throw new \Exception('Attribute name must be a string.');
		}

		if( isset($this->data[$name]) ) {
			unset($this->data[$name]);

			return true;
		}

		return false;
	}

	/**
	 * Print an install button
	 *
	 * @throws \Exception
	 * @since  4.3.3
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	public function render_button()
	{
		echo $this->get_button();
	}

	/**
	 * @return string
	 * @since  4.3.3
	 */
	public function get_button()
	{
		$i18n = $this->get_i18n();

		$button = '<a href="#" class="' . implode(' ', $this->get_classes()) . '" ' . implode(' ', $this->get_data()) . '>' . $i18n[$this->action] . '</a>';

		return $button;
	}

	/**
	 * @return string
	 * @throws \Exception
	 * @since  4.3.3
	 */
	public function get_link()
	{
		$this->remove_class('button');
		$this->remove_class('button-default');
		$this->remove_class('button-primary');

		//$this->add_class('link');
		$this->add_class('button-link');

		return $this->get_button();
	}

	/**
	 * Print an install a link
	 *
	 * @throws \Exception
	 * @since  4.3.3
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	public function render_link()
	{
		echo $this->get_link();
	}

	/**
	 * @return array
	 * @since  4.3.3
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	protected function get_data()
	{
		$data_to_print = [];

		foreach((array)$this->data as $key => $value) {
			$data_to_print[$key] = 'data-' . esc_attr($key) . '="' . esc_attr($value) . '"';
		}

		return $data_to_print;
	}

	/**
	 * @return array
	 * @since  4.3.3
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	protected function get_classes()
	{
		return array_map('esc_attr', $this->classes);
	}

	/**
	 * @throws \Exception
	 * @since  4.3.3
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	protected function build_wordpress()
	{
		if( ('wordpress' === $this->type || 'creativemotion' === $this->type) && !empty($this->base_path) ) {

			$this->action = 'install';

			if( $this->is_plugin_install() ) {
				$this->action = 'deactivate';
				if( !$this->is_plugin_activate() ) {
					$this->action = 'activate';
				}
			}

			$this->add_data('plugin-action', $this->action);
			$this->add_data('slug', $this->plugin_slug);
			$this->add_data('plugin', $this->base_path);

			if( $this->action == 'activate' ) {
				$this->add_class('button-primary');
			} else {
				$this->add_class('button-default');
			}
		}
	}

	/**
	 * Configurate button of internal components
	 *
	 * @throws \Exception
	 * @since  4.3.3
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	protected function build_internal()
	{
		if( $this->type != 'internal' ) {
			return;
		}

		$this->action = 'activate';

		if( $this->is_plugin_activate() ) {
			$this->action = 'deactivate';
		}

		$this->add_data('plugin-action', $this->action);
		$this->add_data('plugin', $this->plugin_slug);

		if( $this->action == 'activate' ) {
			$this->add_class('button-primary');
		} else {
			$this->add_class('button-default');
		}
	}

	/**
	 * Internalization for action buttons
	 *
	 * @return array
	 * @since  4.3.3
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	protected function get_i18n()
	{
		return [
			'activate' => __('Activate', 'wbcr_factory_436'),
			'install' => __('Install', 'wbcr_factory_436'),
			'deactivate' => __('Deactivate', 'wbcr_factory_436'),
			'delete' => __('Delete', 'wbcr_factory_436'),
			'loading' => __('Please wait...', 'wbcr_factory_436'),
			'preparation' => __('Preparation...', 'wbcr_factory_436'),
			'read' => __('Read more', 'wbcr_factory_436')
		];
	}


	/**
	 * Allows you to get the base path to the plugin in the directory wp-content/plugins/
	 *
	 * @param $slug - slug for example "clearfy", "hide-login-page"
	 *
	 * @return int|null|string - "clearfy/clearfy.php"
	 */
	protected function get_plugin_base_path_by_slug($slug)
	{
		// Check if the function get_plugins() is registered. It is necessary for the front-end
		// usually get_plugins() only works in the admin panel.
		if( !function_exists('get_plugins') ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();

		foreach($plugins as $base_path => $plugin) {
			if( strpos($base_path, rtrim(trim($slug))) !== false ) {
				return $base_path;
			}
		}

		return null;
	}
}

