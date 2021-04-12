<?php

// Exit if accessed directly
use Instagram\Includes\WIS_Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The page Settings.
 *
 * @since 1.0.0
 */
class WIS_SettingsPage extends WIS_Page {

	/**
	 * Тип страницы
	 * options - предназначена для создании страниц с набором опций и настроек.
	 * page - произвольный контент, любой html код
	 *
	 * @var string
	 */
	public $type = 'options';

	/**
	 * The id of the page in the admin menu.
	 *
	 * Mainly used to navigate between pages.
	 *
	 * @since 1.0.0
	 * @see   FactoryPages444_AdminPage
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
		$this->id            = "settings";
		$this->page_title    = __( 'Settings of Social Slider Widget', 'instagram-slider-widget' );
		$this->menu_title    = __( 'Settings', 'instagram-slider-widget' );
		$this->menu_target   = "widgets-" . $plugin->getPluginName();
		$this->menu_icon     = '~/admin/assets/img/wis.png';
		$this->capabilitiy   = "manage_options";
		$this->template_name = "settings";

		parent::__construct( $plugin );

		$this->plugin = $plugin;
	}

	public function assets( $scripts, $styles ) {
		$this->scripts->request( 'jquery' );

		$this->scripts->request( [
			'control.checkbox',
			'control.dropdown'
		], 'bootstrap' );

		$this->styles->request( [
			'bootstrap.core',
			'bootstrap.form-group',
			'bootstrap.separator',
			'control.dropdown',
			'control.checkbox',
		], 'bootstrap' );

		wp_enqueue_style( 'wyoutube-admin-styles', WYT_PLUGIN_URL . '/admin/assets/css/wyoutube-admin.css', array(), WYT_PLUGIN_VERSION );
		wp_enqueue_script( 'wyoutube-admin-script', WYT_PLUGIN_URL . '/admin/assets/js/wyoutube-admin.js', array( 'jquery' ), WYT_PLUGIN_VERSION, true );
<<<<<<< HEAD

		$wyt = json_encode([
			'nonce'          => wp_create_nonce( 'wyt_nonce' ),
			'remove_account' => __( 'Are you sure want to delete this account?', 'yft' ),
		]);
		wp_add_inline_script( 'wyoutube-admin-script', "var wyt = $wyt;");
=======
		wp_localize_script( 'wyoutube-admin-script', 'wyt', array(
			'nonce'          => wp_create_nonce( 'wyt_nonce' ),
			'remove_account' => __( 'Are you sure want to delete this account?', 'yft' ),
		) );
		wp_localize_script( 'wyoutube-admin-script', 'add_account_nonce', array(
			'nonce' => wp_create_nonce( "addAccountByToken" ),
		) );
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c
	}

	public function indexAction() {
		wp_enqueue_style( 'wis-tabs-style', WIS_PLUGIN_URL . '/admin/assets/css/component.css', array(), WIS_PLUGIN_VERSION );
		if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) {
			switch ( $_GET['tab'] ) {
				case "instagram":
					$this->instagram();
					break;
				case "facebook":
					WIS_FacebookSlider::app()->FACEBOOK->tabAction();
					break;
				case "Youtube":
					$this->youtube();
					break;
			}
		} else {
			$this->instagram();
		}

		parent::indexAction();
	}

	/**
	 * Логика на вкладке Инстаграма
	 */
	public function instagram() {
		if ( isset( $_GET['type'] ) && $_GET['type'] == 'business' ) {
			if ( isset( $_GET['token_error'] ) ) {
			    $token_error = wp_strip_all_tags($_GET['token_error']);
				echo '<div class="notice notice-error"><p>' . $token_error . '</p></div>';
				$_SERVER['REQUEST_URI'] = str_replace( '#_', '', remove_query_arg( 'token_error' ) );
			} else {
				if ( isset( $_GET['access_token'] ) ) {
					$token                  = $_GET['access_token'];
					$result                 = WIS_InstagramSlider::app()->update_account_profiles( $token, true );
					$_SERVER['REQUEST_URI'] = remove_query_arg( 'access_token' );
					?>
                    <div id="wis_accounts_modal" class="wis_accounts_modal">
                        <div class="wis_modal_header">
                            Choose Account:
                        </div>
                        <div class="wis_modal_content">
							<?php echo $result[0]; ?>
                        </div>
                    </div>
                    <div id="wis_modal_overlay" class="wis_modal_overlay"></div>
                    <span class="wis-overlay-spinner is-active">&nbsp;</span>
					<?php
				}
			}
		} else {
			if ( isset( $_GET['token_error'] ) ) {
                $token_error = wp_strip_all_tags($_GET['token_error']);
				echo '<div class="notice notice-error"><p>' . $token_error . '</p></div>';
				$_SERVER['REQUEST_URI'] = str_replace( '#_', '', remove_query_arg( 'token_error' ) );
			} else {
				if ( isset( $_GET['access_token'] ) ) {
					$token                  = $_GET['access_token'];
					$result                 = WIS_InstagramSlider::app()->update_account_profiles( $token );
					$_SERVER['REQUEST_URI'] = str_replace( '#_', '', remove_query_arg( 'access_token' ) );
				}
			}
		}
	}

	/**
	 * Логика на вкладке Ютуба
	 */
	public function youtube() {

		if(isset($_POST['wyt_api_key']) && $_POST['wyt_api_key'] != null){
			$this->plugin->update_youtube_api_key($_POST['wyt_api_key']);

			if(isset($_POST['wyt_feed_link']) && $_POST['wyt_feed_link'] != null){

				$link = $_POST['wyt_feed_link'];
				$start_with_string = 'https://www.youtube.com/channel/';

				if(stripos($link, $start_with_string) === false) return false;

				$id = explode('/channel/', $link)[1];
				$id = explode('/', $id)[0];

				$profile = $this->plugin->youtube_api->getUserById($id)->items[0];
				$profile->snippet->channelId = $id;
				$this->plugin->update_youtube_feed( $profile );

			}
		}
	}

}
