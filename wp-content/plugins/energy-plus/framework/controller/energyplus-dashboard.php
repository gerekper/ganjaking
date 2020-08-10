<?php

/**
* EnergyPlus Dashboard
*
* Dashboard
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class EnergyPlus_Dashboard extends EnergyPlus {

	/**
	* Starts everything
	*
	* @return void
	*/

	public static function run() {

		wp_enqueue_script("energyplus-hammer",    EnergyPlus_Public . "3rd/hammer.js", array(), EnergyPlus_Version);
		wp_enqueue_script("energyplus-muuri",     EnergyPlus_Public . "3rd/muuri.js", array('energyplus-hammer'), EnergyPlus_Version);
		wp_enqueue_script("energyplus-gauge",     EnergyPlus_Public . "3rd/gauge/gauge.js", array(), EnergyPlus_Version);
		wp_enqueue_script("energyplus-chart",     EnergyPlus_Public . "3rd/chart.js", array(), EnergyPlus_Version);
		wp_enqueue_script("energyplus-dashboard", EnergyPlus_Public . "js/energyplus-dashboard.js", array('energyplus-muuri'), EnergyPlus_Version);


		self::route();
	}

	/**
	* Router for sub pages
	*
	* @return void
	*/

	private static function route()	{

		$mode = EnergyPlus::option('dashboard-type', 'default', 'get', true);

		switch (EnergyPlus_Helpers::get('action', $mode)) {

			case 'wd_settings':
			self::widget_settings();
			break;

			case 'widget_list':
			self::widget_list();
			break;

			case 'wc-admin':
			EnergyPlus::option('dashboard-type', 'wc-admin', 'set', true);
			echo EnergyPlus_View::run('dashboard/dashboard-wc-admin', array());
			break;

			case 'default':
			default:
			EnergyPlus::option('dashboard-type', '0', 'set', true);
			self::index();
			break;
		}
	}

	/**
	* Main function
	*
	* @return void
	*/

	public static function index()	{

		$widgets  = array();

		$map      = EnergyPlus::option('dashboard_widgets', array());
		$settings = EnergyPlus::option('dashboard_widgets_settings', array());

		echo EnergyPlus_View::run('dashboard/dashboard',  array( 'map' => $map, 'settings' => $settings ) );

	}

	/**
	* Widget listsadded to dashboard
	*
	* @return void
	*/

	public static function widget_list() {

		$available_widgets = EnergyPlus_Widgets::available_widgets();

		$installed         = array();
		$others            = array();

		$map               = EnergyPlus::option('dashboard_widgets', array());

		foreach ($map AS $_map)	{
			if (isset($available_widgets[$_map['type']])) {

				// $class= "Widgets__" . sanitize_key($_map['type']);

				$installed[] = array(
					'id'          => $_map['id'],
					'type'        => $_map['type'],
					'title'       => $available_widgets[$_map['type']]['title'],
					'description' => $available_widgets[$_map['type']]['description'],
					'multiple'    => $available_widgets[$_map['type']]['multiple'],
				);
			}
		}

		wp_enqueue_script("energyplus-dashboard",  EnergyPlus_Public . "js/energyplus-dashboard.js", array(), EnergyPlus_Version);

		echo EnergyPlus_View::run('dashboard/widget-list',  array( 'installed' => $installed, 'all' => $available_widgets ) );

	}

	public static function widget_settings() {

		$id  = sanitize_key(EnergyPlus_Helpers::get('id'));
		$map = EnergyPlus::option('dashboard_widgets', array());

		if (!isset($map[$id])) {
			return "Not found";
		}

		$available_widgets = EnergyPlus_Widgets::available_widgets();

		if (isset($available_widgets[$map[$id]['type']])) {
			$class= "Widgets__" . sanitize_key($map[$id]['type']);
			$class::setup($id);
		}

	}
	/**
	* Ajax router
	*
	* @since  1.0.0
	* @return EnergyPlus_Ajax
	*/

	public static function ajax() {

		$do = EnergyPlus_Helpers::post('do');
		$id = sanitize_key(EnergyPlus_Helpers::post('id'));

		switch ($do)	{

			/* Adding new widget */
			case 'add-widget':

			$available_widgets = EnergyPlus_Widgets::available_widgets();

			if (isset($available_widgets[$id])) {

				$uniq = uniqid();

				$map  = EnergyPlus::option('dashboard_widgets', array());

				if ($available_widgets[$id]['multiple'] === FALSE && array_search($id, array_column($map, 'type')) !== FALSE) {
					EnergyPlus_Ajax::error('This widget not allowed mutliple instance');
				}

				$map[$uniq]['id']   = $uniq;
				$map[$uniq]['type'] = sanitize_key($available_widgets[$id]['id']);
				$map[$uniq]['w']    = $available_widgets[$id]['w'];
				$map[$uniq]['h']    = $available_widgets[$id]['h'];


				EnergyPlus::option("dashboard_widgets", $map, 'set');
				EnergyPlus_Ajax::success('OK');

			}

			break;

			/* Deletes widget from dashboard */
			case 'delete-widget':

			$map = EnergyPlus::option('dashboard_widgets', array());

			if (isset($map[$id])) {
				unset($map[$id]);
				EnergyPlus::option("dashboard_widgets", $map, 'set');
				EnergyPlus_Ajax::success('OK');
			}
			break;
		}
	}
}

?>
