<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_Maintenance_Addon
 * @subpackage Revslider_Maintenance_Addon/admin
 * @author     ThemePunch <info@themepunch.com>
 */

if(!defined('ABSPATH')) exit();

class Revslider_Maintenance_Addon_Admin {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		if(isset($_GET["page"]) && $_GET["page"]=="revslider"){
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/revslider-maintenance-addon-admin.js', array( 'jquery','revbuilder-admin', 'jquery-ui-core', 'jquery-ui-datepicker' ), $this->version, false );
			wp_localize_script( $this->plugin_name, 'revslider_maintenance_addon', $this->get_var() );
		}
	}

	/**
	 * Saves Values for this Add-On
	 */
	public function save_maintenance() {
		if(isset($_REQUEST['data']['revslider_maintenance_form'])){
			update_option( "revslider_maintenance_addon", $_REQUEST['data']['revslider_maintenance_form'] );
			return 1;
		}else{
			return 0;
		}

	}

	/**
	 * Load Values for this Add-On
	 */
	public function values_maintenance() {
		$revslider_maintenance_addon_values = array();
		parse_str(get_option('revslider_maintenance_addon'), $revslider_maintenance_addon_values);
		$return = json_encode($revslider_maintenance_addon_values);
		return array("message" => "Data found", "data"=>$return);
	}

	/**
	 * Change Enable Status of this Add-On
	 */
	private function change_addon_status($enabled) {
		update_option( "revslider_maintenance_enabled", $enabled );
	}

	/**
	 * Perform Ajax Actions as called from RevSlider Core
	 * @since    2.0.0
	 */
	public function do_ajax($return,$action) {
		switch ($action) {
			case 'wp_ajax_enable_revslider-maintenance-addon':
				$this->change_addon_status( 1 );
				return  __('maintenance AddOn enabled', 'revslider-maintenance-addon');
			break;
			case 'wp_ajax_disable_revslider-maintenance-addon':
				$this->change_addon_status( 0 );
				return  __('maintenance AddOn disabled', 'revslider-maintenance-addon');
			break;
			case 'wp_ajax_get_values_revslider-maintenance-addon':
				$return = $this->values_maintenance();
				if(empty($return)) $return = true;
				return $return;
			break;
			case 'wp_ajax_save_values_revslider-maintenance-addon':
				$return = $this->save_maintenance();
				if(empty($return) || !$return){
					return  __('Configuration could not be saved', 'revslider-maintenance-addon');
				}else{
					return  __('Maintenance Configuration saved', 'revslider-maintenance-addon');
				}
			break;
			default:
				return $return;
			break;
		}
	}

	/**
	 * Returns the global JS variable
	 * @since    2.0.0
	 */
	public function get_var($var='',$slug='revslider-maintenance-addon') {
		if($slug == 'revslider-maintenance-addon'){
			return array(
				'enabled' => get_option('revslider_maintenance_enabled'),
				'bricks' => array(
					'active'  =>  __('Active','revslider-maintenance-addon'),
					'maintenance'  =>  __('Maintenance','revslider-maintenance-addon'),
					'settings' =>  __('Settings','revslider-maintenance-addon'),
					'configuration' =>  __('Configuration','revslider-maintenance-addon'),
					'maintenancecontent' =>  __('Content from','revslider-maintenance-addon'),
					'slider' => __('Slider','revslider-maintenance-addon'),
					'page' => __('Page','revslider-maintenance-addon'),
					'pagetitle' => __('Page Title','revslider-maintenance-addon'),
					'save' => __('Save Configration','revslider-maintenance-addon'),
					'entersometitle' => __('Enter Some Title','revslider-maintenance-addon'),
					'loadvalues' => __('Loading Maintenance Add-On Configration','revslider-maintenance-addon'),
					'savevalues' => __('Saving Maintenance Add-On Configration','revslider-maintenance-addon'),
					'usetimer' => __('Use Timer','revslider-maintenance-addon'),
					'enddate' => __('End Date','revslider-maintenance-addon'),
					'hour' => __('Hour','revslider-maintenance-addon'),
					'minute' => __('Min.','revslider-maintenance-addon'),
					'enddateform' => __('YYYY-MM-DD','revslider-maintenance-addon'),
					'autodeactivate' => __('Auto Disable','revslider-maintenance-addon'),
					'timersettings' => __('Timer Settings','revslider-maintenance-addon'),
					'earlier' => __('Earlier','revslider-maintenance-addon'),
					'later' => __('Later','revslider-maintenance-addon'),
					'remaining_days' => __('Remaining Days','revslider-maintenance-addon'),
					'remaining_hours' => __('Remaining Hours','revslider-maintenance-addon'),
					'remaining_minutes' => __('Remaining Minutes','revslider-maintenance-addon'),
					'remaining_seconds' => __('Remaining Seconds','revslider-maintenance-addon'),
					'title_placholder' => __('Enter Some Title','revslider-maintenance-addon')
				)
			);
		}else{
			return $var;
		}
	}

}
