<?php

/**
* EnergyPlus Settings
*
* Settings
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class EnergyPlus_Settings extends EnergyPlus {

	public static $theme_list =  array ('one', 'one-shadow', 'console');

	/**
	* Starts everything
	*
	* @return void
	*/

	public static function run() {

		wp_enqueue_script("energyplus-nested-sortable",  EnergyPlus_Public . "3rd/nested-sortable.js", array(), EnergyPlus_Version);
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_style( "energyplus-iconpicker", EnergyPlus_Public . '3rd/iconpicker/css/bootstrap-iconpicker.min.css' );
		wp_enqueue_script( 'energyplus-iconpicker-js', EnergyPlus_Public . "3rd/iconpicker/js/bootstrap-iconpicker.bundle.min.js", array( ), EnergyPlus_Version, true );

		wp_enqueue_script( 'energyplus-settings', EnergyPlus_Public . "js/energyplus-settings.js", array( 'wp-color-picker' ), EnergyPlus_Version, true );

		self::route();
	}


	/**
	* Router for sub pages
	*
	* @return void
	*/
	private static function route()	{

		switch (EnergyPlus_Helpers::get('panel'))	{

			case 'panels':
			self::panels();
			break;

			case 'woocommerce':
			self::woocommerce();
			break;

			default:
			self::index();
			break;
		}
	}

	/**
	* Main function
	*
	* @return null
	*/

	public static function index()	{

		/*
		* Themes
		*/

		if ( "1" === EnergyPlus::option('feature-own_themes')) {
			$own = true;
			$current_user = wp_get_current_user();
			$theme  = EnergyPlus::option('theme-' . intval( $current_user->ID ));
			if ( !$theme ) {
				$theme= EnergyPlus::option('theme', 'one');
			}
		} else {
			$own = false;
			$theme = EnergyPlus::option('theme', 'one');
		}

		$themes = array(
			'list'     => self::$theme_list,
			'selected' => $theme
		);

		$colors = self::colors();

		if ('custom' !== EnergyPlus::option('colors', array('key'=>'ffffff'), 'get', $own)['key']) {
			$colors['custom'] = array(
				'key'=> 'custom',
				'header-background' => '#ffffff',
				'header-icons' => '#cacaca',
				'header-text' => '#000000',
				'content-background' => ('#f5f5f5'),
				'content-borders-1' => wc_hex_darker('#f5f5f5', 5),
				'content-borders-2' => '#f5f2f2',
				'header-top' => 'transparent',
				'header-hover' => 'red',
				'header-more' => '#353535',
				'content-btnA' => '#efefef',
				'primary-buttons' => '#dc3545',
			);
		} else {
			$colors['custom'] = EnergyPlus::option('colors', array('key'=>'ffffff'), 'get', $own);
			$colors['custom']['header-top'] = wc_hex_darker($colors['custom']['header-background'], 5);
			$colors['custom']['header-text'] = $colors['custom']['header-icons'];
		}


		$settings_logo = EnergyPlus::option("feature-logo", 0);
		echo EnergyPlus_View::run('settings/general',  array(
			'themes'        => $themes,
			'colors'				=> $colors,
			'colors_selected'				=> EnergyPlus::option('colors', array('key'=>'ffffff'), 'get', $own),
			'settings_logo' => $settings_logo
		));

	}

	/**
	* Selects active pages on EnergyPlus
	*
	* @return void
	*/

	public static function panels()	{

		if (false === EnergyPlus_Admin::is_admin(null)) {
			esc_html_e("Restricted Area.", 'energyplus');
			wp_die();
		}

		switch (EnergyPlus_Helpers::get('action')){

			default:
			$menu = EnergyPlus::option('menu', array());

			if (!isset($menu['energyplus-reactors'])) {
				$menu['energyplus-reactors'] = array( 'title' => __('Reactors', 'energyplus'), 'segment' => 'reactors', 'icon' => 'dashicons-share-alt', 'order' => 6, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1));
			}

			$others =  $GLOBALS[ 'menu' ];

			$next_index = 9999;

			foreach ($others AS $other)
			{
				++$next_index;

				if ( isset( $other[5] ) && $other[5] !== "toplevel_page_energyplus" && !isset( $menu[ $other[5] ] ))
				{
					$menu[ $other[5] ] = array('title'=> sanitize_text_field(strtok($other[0],'<')), 'icon' => $other[6], 'active' => 1, "admin_link" => $other[2], 'other'=>true, 'roles'=>array('administrator' =>1, 'shop_manager'=>1) );
				} else {
				}
			}

			foreach ($menu AS $m_k=>$m_v) {
				++$next_index;

				if (!isset($menu[$m_k]['order'])) {
					$menu[$m_k]['order'] = $next_index;
				}

				if (!isset($menu[$m_k]['roles'])) {
					$menu[$m_k]['roles'] = array('administrator' =>1, 'shop_manager'=>1);
				}

			}

			EnergyPlus::option('menu', $menu, 'set');

			array_multisort(array_map(function($element) {
				return $element['order'];
			}, $menu), SORT_ASC, $menu);


			// Add descriptions to panels
			/*	$menu["energyplus-dashboard"]["description"] = __("Main page of your e-commerce Admin Panel. You can view your site activity. You have also chance to select what you want to see with drag-and-drop widget system", "energyplus");
			$menu["energyplus-orders"]["description"]    = __("Manage your orders.", "energyplus");
			$menu["energyplus-products"]["description"]  = __("Manage your products.", "energyplus");
			$menu["energyplus-coupons"]["description"]   = __("Manage your coupons.", "energyplus");
			$menu["energyplus-customers"]["description"] = __("Manage your customers.", "energyplus");
			$menu["energyplus-comments"]["description"]  = __("Manage your reviews.", "energyplus");
			$menu["energyplus-reports"]["description"]   = __("Reports for your e-commerce", "energyplus");
			$menu["energyplus-reactors"]["description"]   = __("It allows you to add additional features to Energy+ (Note: This module cannot be disabled)", "energyplus");
			*/
			echo EnergyPlus_View::run('settings/panels',  array( 'menu' => $menu ));
			break;
		}
	}

	/**
	* Redirect to Woocommerce Settings
	*
	* @return void
	*/

	public static function woocommerce() {

		echo EnergyPlus_View::run('settings/woocommerce',  array());

	}

	/**
	* Ajax router
	*
	* @return void
	*/

	public static function ajax(){

		EnergyPlus_Helpers::ajax_nonce(TRUE);

		$section = EnergyPlus_Helpers::post('section', 'default');

		switch ($section){
			/* Select theme */
			case "themes":
			$new_theme = EnergyPlus_Helpers::post('theme', 0);

			if (in_array($new_theme, self::$theme_list)){
				if ( "1" === EnergyPlus::option('feature-own_themes')) {
					$current_user = wp_get_current_user();
					EnergyPlus::option('theme-' . intval( $current_user->ID ), sanitize_key($new_theme), 'set');
				} else {
					EnergyPlus::option('theme', EnergyPlus_Helpers::clean($new_theme), 'set');
				}
				wp_die();
			}
			break;

			/* Modes are different views for EnergyPlus pages */
			case "modes":

			$panels = array('energyplus-orders', 'energyplus-products', 'energyplus-customers', 'energyplus-coupons', 'energyplus-comments');

			$mode  = absint( EnergyPlus_Helpers::post( 'state', 0 ));
			$panel = EnergyPlus_Helpers::post( 'panel', 0 );

			if (!in_array( $panel, $panels )) {
				wp_die( -2 );
			}

			EnergyPlus::option('mode-' . sanitize_key($panel), sanitize_key($mode), 'set');
			break;

			/* Set EnergyPlus features */
			case "features":

			$allowed = array('auto' => 1, 'use-administrator' =>1, 'use-shop_manager' =>1,  'pulse'=>1, 'logo' => 1, 'badge' => 1, 'own_themes' => 1, 'sounds_notification' => 0, 'sounds_product' => 0, 'sounds_checkout' => 0, 'goals-daily' =>1, 'goals-weekly' =>1, 'goals-monthly' =>1, 'goals-yearly' =>1, 'refresh'=>0);

			$state   = 'true' === EnergyPlus_Helpers::post( 'state', 'false' ) ? "1" : "0"  ;
			$feature = EnergyPlus_Helpers::post( 'feature', 0 );

			if (EnergyPlus_Helpers::post( 'val' )) {
				if (in_array($feature, array('goals-daily', 'goals-weekly', 'goals-monthly', 'goals-yearly'))) {
					$state = floatval(EnergyPlus_Helpers::post( 'val' ));
				} elseif (in_array($feature, array('refresh'))) {
					$state = intval(EnergyPlus_Helpers::post( 'val' ));
				} elseif (in_array($feature, array('badge'))) {
					$state = intval(EnergyPlus_Helpers::post( 'val' ));
				} else {
					$state = sanitize_text_field(EnergyPlus_Helpers::post( 'val' ));
				}
			}

			if (!in_array( $feature, array_keys($allowed) )) {
				wp_die( -2 );
			}

			if (isset($allowed[$feature]) && $allowed[$feature] === 1 && !EnergyPlus_Admin::is_admin( 'administrator' )) {
				wp_die ( 'Not allowed' );
			}

			EnergyPlus::option('feature-' . sanitize_key($feature), EnergyPlus_Helpers::clean($state), 'set');

			break;

			case 'colors':

			$own =   ( "1" === EnergyPlus::option('feature-own_themes') )? true : false;

			$colors = array();
			foreach ($_POST['val'] AS $k=>$v) {
				$colors[sanitize_key($k)] = sanitize_text_field($v);
			}

			if ('custom' === $colors['key']) {
				$colors['header-top' ] = wc_hex_darker(wc_format_hex($colors['header-background']), 10);
				$colors['header-more' ] = wc_hex_darker(wc_format_hex($colors['header-background']), 60);
				$colors['header-text' ] = wc_format_hex($colors['header-icons']);

			}

			EnergyPlus::option('colors', $colors, 'set', $own);

			echo json_encode(array(
				"status" => "success",
			));
			wp_die();

			break;

			case 'reset-menu':

			delete_option('energyplus_menu');

			echo json_encode(array(
				"return" => "success",
			));
			wp_die();
			break;

			case 'reorder':

			$menu = EnergyPlus::option('menu', array());

			$index = 0;

			foreach ($_POST['ids'] AS $k=>$v) {
				if (isset($menu[str_replace('energyplus-menu-', '', $v)])) {
					$menu[str_replace('energyplus-menu-', '', $v)]['order'] = $index;
				}
				++$index;
			}

			EnergyPlus::option('menu', $menu, 'set');

			echo json_encode(array(
				"status" => "success",
			));
			wp_die();

			break;

			case 'icon':

			$panel = esc_attr(EnergyPlus_Helpers::post('panel', 0));
			$icon = esc_attr(EnergyPlus_Helpers::post('icon', ''));

			$menu = EnergyPlus::option('menu');
			if (isset($menu[$panel])) {
				$menu[$panel]['icon'] = $icon;
			}

			EnergyPlus::option('menu', $menu, 'set');

			echo json_encode(array(
				"status" => 1,
			));
			wp_die();
			break;
		}
	}

	/**
	* Set panels active or deactive
	*
	* @since  1.0.0
	*/

	public static function ajax_panels_active(){
		$panel = EnergyPlus_Helpers::post('panel', 0);
		$state = EnergyPlus_Helpers::post('state', false);

		$menu = EnergyPlus::option('menu');

		if (!isset($menu['energyplus-reactors'])) {
			$menu['energyplus-reactors'] = array( 'title' => __('Reactors', 'energyplus'), 'segment' => 'reactors', 'icon' => 'dashicons-share-alt', 'order' => 6, 'active' => 1);
		}

		if ("-1" === $panel){

			$title  = EnergyPlus_Helpers::post('title', 'New');
			$parent = EnergyPlus_Helpers::post('parent', '0');
			$url    = $_POST['url'];
			$uniqid = "0-" . md5($title . uniqid() );

			$url = esc_url_raw( urldecode( $url ) );

			if (!filter_var($url, FILTER_VALIDATE_URL)) {
				EnergyPlus_Ajax::error(__('Not a valid URL', 'energyplus'));
				wp_die();
			}

			if (!in_array( $parent, array('0', 'energyplus-orders', 'energyplus-products', 'energyplus-customers', 'energyplus-reports', 'energyplus-coupons', 'energyplus-comments') )) {
				wp_die( -3 );
			}


			if ('0' !== $parent && '00' !== $parent) {
				$menu[ $uniqid ] = array('title'=> $title, 'icon' => '//', 'active' => 1, "admin_link" => $url, 'parent'=>$parent );
			} else if ('00' === $parent) {
				$menu[ $uniqid ] = array('title'=> $title, 'icon' => '//', 'active' => 1, "admin_link" => $url, 'order' => time(), 'target'=>'_blank' );
			} else {
				$menu[ $uniqid ] = array('title'=> $title, 'icon' => '//', 'active' => 1, "admin_link" => $url, 'order' => time() );
			}

			EnergyPlus::option('menu', $menu, 'set');

			echo json_encode(array(
				"return" => "success",
				"anchor" => $uniqid
			));
			wp_die();

		}

		if (isset($menu[$panel])){
			if ("-2" === $state)	{
				unset($menu[$panel]);

			}
			else {
				$role =  sanitize_key(EnergyPlus_Helpers::post('for', 'sm'));
				$state = ('true' === $state) ? 1 : 0;

				if ('energyplus-reactors' !== $panel) {

					if (!isset($menu[$panel]['roles'])) {
						$menu[$panel]['roles'] = array('administrator' =>1, 'shop_manager'=>1);
					}

					$menu[$panel]['roles'][$role] = $state;
				}	 else {
					EnergyPlus_Ajax::error(esc_html__('Reactors can not be disabled and only seen by Admins'));
					wp_die();
				}
			}
		}

		//print_r($menu);

		EnergyPlus::option('menu', $menu, 'set');

		EnergyPlus_Ajax::success();
	}

	/**
	* Default colors
	*
	* @since  1.0.4
	*/

	public static function colors() {
		$colors = array(

			'ffffff' => array(
				'key'=> 'ffffff',
				'title-menu-a' => '#a7a7ae',
				'header-background' => '#ffffff',
				'header-icons' => '#cccccc',
				'header-text' => '#000000',
				'header-top' => wc_hex_darker('#f5f5f5', 5),
				'header-hover' => '#dc3545',
				'header-more' => '#353535',
				'primary-buttons' => '#dc3545',
			),

			'353535' => array(
				'key'=> '353535',
				'header-background' => '#282828',
				'header-icons' =>'#a0a0a0',
				'header-text' => '#a0a0a0',
				'header-more'=>'#353535',
				'header-top' => wc_hex_lighter('#282828', 10),
				'header-hover' => '#dc3545',
				'primary-buttons' => '#dc3545',
			),

			'cf4944' => array(
				'key'=> 'cf4944',
				'header-background' => '#cf4944',
				'header-icons' => '#f3f1f1',
				'header-text' =>'#f3f1f1',
				'header-top' => wc_hex_lighter('#cf4944', 10),
				'header-more' => '#cf4944',
				'header-hover' => '#dd823b',
				'primary-buttons' => '#a51c29',
			),

			'f3dec9' => array(
				'key'=> 'f3dec9',
				'header-background' => '#efc7a0',
				'header-icons' => '#ffffff',
				'header-text' => '#98683a',
				'header-top'  => wc_hex_lighter('#efc7a0', 10),
				'header-more' => wc_hex_darker('#efc7a0',10),
				'header-hover'=>'#a27141',
				'primary-buttons' => '#a27141',
			),

			'1e73be' => array(
				'key'=> '1e73be',
				'header-background' => '#1e73be',
				'header-icons' => '#ffffff',
				'header-text' => '#ffffff',
				'header-top'  => wc_hex_lighter('#1e73be', 10),
				'header-more' => wc_hex_darker('#1e73be',10),
				'header-hover'=>'#dd3333',
				'primary-buttons' => '#145293',
			),


			'f5f5f5' => array(
				'key'=> 'f5f5f5',
				'header-background' => '#f5f5f5',
				'header-icons' => '#ccc',
				'header-text' => '#ccc',
				'header-top'  => '#eaeaea',
				'header-hover'=>'#999',
				'header-more'=>'#656565',
				'header-right'=>'#eaeaea',
				'primary-buttons' => '#999',
			)
		);

		return $colors;
	}

}

?>
