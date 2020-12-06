<?php

namespace Premmerce\PrimaryCategory;

/**
 * Class PrimaryCategory
 *
 * This is main class. It's responsible for loading an app.
 */
class PrimaryCategory
{
	const VERSION = '2.0.3';

	/**
	 * @var string
	 */
	private $pluginMainFilePath;

	/**
	 * @var ServiceContainer
	 */
	private $container;

	/**
	 * @var string
	 */
	public static $mainFilePath;

	/**
	 * PrimaryCategory constructor.
	 * @param string $pluginMainFilePath
	 * @param ServiceContainer $container
	 */
	public function __construct($pluginMainFilePath, ServiceContainer $container)
	{
		self::$mainFilePath = $pluginMainFilePath;
		$this->pluginMainFilePath = $pluginMainFilePath;
		$this->container = $container;

		add_action('plugins_loaded', [$this, 'loadTextdomain']);
		add_action('current_screen', [$this, 'init']);
		add_action('plugins_loaded', [$this, 'initEventTracker']);
	}

	/**
	 * Load translations
	 */
	public function loadTextdomain()
	{
		$moPath = trailingslashit(__DIR__ ) . '../languages/premmerce-primary-category-' . get_locale() . '.mo';
		load_textdomain('premmerce-primary-category',  $moPath);
	}

	public function initEventTracker()
    {
        $this->container->getEventsTracker();
    }

	/**
	 * Load Admin class
	 */
	public function init()
	{
        if(is_admin() && ! wp_doing_ajax()){
            $this->container->getAdmin();
		}
	}

}
