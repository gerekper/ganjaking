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
		$this->id         = "settings";
		$this->page_title = __( 'Settings of Social Slider Widget', 'instagram-slider-widget' );
		$this->menu_title = __( 'Settings', 'instagram-slider-widget' );
		$this->menu_target= "widgets-".$plugin->getPluginName();
		$this->menu_icon = '~/admin/assets/img/wis.png';
		$this->capabilitiy = "manage_options";
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
	}

	public function indexAction() {
		wp_enqueue_style( 'wis-tabs-style', WIS_PLUGIN_URL.'/admin/assets/css/component.css', array(), WIS_PLUGIN_VERSION );
		if(isset($_GET['tab']) && !empty($_GET['tab']))
        {
            switch($_GET['tab'])
            {
                case "instagram":
                    $this->instagram();
                    break;
                case "facebook":
                	WIS_FacebookSlider::app()->FACEBOOK->tabAction();
                    break;
                case "youtube":
	                $this->youtube();
                    break;
            }
        }
		else $this->instagram();

		parent::indexAction();
	}

	/**
	 * Логика на вкладке Инстаграма
	 */
	public function instagram() {
        if(isset( $_GET['type'] ) && $_GET['type'] == 'business') {
            if ( isset( $_GET['token_error'] ) ) {
	            echo '<div class="notice notice-error"><p>' . $_GET['token_error'] . '</p></div>';
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
        }
		else
		{
			if ( isset( $_GET['token_error'] ) ) {
				echo '<div class="notice notice-error"><p>' . $_GET['token_error'] . '</p></div>';
				$_SERVER['REQUEST_URI'] = str_replace( '#_', '', remove_query_arg( 'token_error' ));
			} else {
				if ( isset( $_GET['access_token'] ) ) {
					$token = $_GET['access_token'];
					$result = WIS_InstagramSlider::app()->update_account_profiles( $token );
					$_SERVER['REQUEST_URI'] = str_replace( '#_', '', remove_query_arg( 'access_token' ));
				}
			}
		}
	}

	/**
	 * Логика на вкладке Ютуба
	 */
	public function youtube() {
		//require_once WIS_PLUGIN_DIR . '/includes/socials/class.wis_youtube.php';
	}

}
