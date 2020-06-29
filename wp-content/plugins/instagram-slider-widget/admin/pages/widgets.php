<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The page Settings.
 *
 * @since 1.0.0
 */
class WIS_WidgetsPage extends WIS_Page {

	/**
	 * Тип страницы
	 * options - предназначена для создании страниц с набором опций и настроек.
	 * page - произвольный контент, любой html код
	 *
	 * @var string
	 */
	public $type;

	/**
	 * The id of the page in the admin menu.
	 *
	 * Mainly used to navigate between pages.
	 *
	 * @since 1.0.0
	 * @see   FactoryPages423_AdminPage
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Menu icon (only if a page is placed as a main menu).
	 * For example: '~/assets/img/menu-icon.png'
	 * For example dashicons: '\f321'
	 * @var string
	 */
	public $menu_icon;

	/**
	 * @var string
	 */
	public $page_menu_dashicon = 'dashicons-performance';

	/**
	 * Menu position (only if a page is placed as a main menu).
	 * @link http://codex.wordpress.org/Function_Reference/add_menu_page
	 * @var string
	 */
	public $menu_position = 58;

	/**
	 * Menu type. Set it to add the page to the specified type menu.
	 * For example: 'post'
	 * @var string
	 */
	public $menu_post_type = null;

	/**
	 * Visible page title.
	 * For example: 'License Manager'
	 * @var string
	 */
	public $page_title;

	/**
	 * Visible title in menu.
	 * For example: 'License Manager'
	 * @var string
	 */
	public $menu_title;

	/**
	 * If set, an extra sub menu will be created with another title.
	 * @var string
	 */
	public $menu_sub_title;

	/**
	 *
	 * @var
	 */
	public $page_menu_short_description;

	/**
	 * Заголовок страницы, также использует в меню, как название закладки
	 *
	 * @var bool
	 */
	public $show_page_title = true;

	/**
	 * @var int
	 */
	public $page_menu_position = 20;


	/**
	 * @param WIS_Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->id         = "widgets";
		$this->page_title = __( 'Social Slider Widgets', 'instagram-slider-widget' );
		$this->menu_title = __( 'Social Slider Widgets', 'instagram-slider-widget' );
		$this->menu_sub_title = __( 'Widgets', 'instagram-slider-widget' );;
		$this->menu_icon = '~/admin/assets/img/wis.png';
		$this->capabilitiy = "manage_options";
		$this->template_name = "widgets";

		parent::__construct( $plugin );

		$this->plugin = $plugin;
	}

	public function assets( $scripts, $styles ) {
		//Widgets scripts
		$this->scripts->request( 'admin-widgets' );

		if ( wp_is_mobile() ) {
			$this->scripts->request( 'jquery-touch-punch' );
		}

	}
}