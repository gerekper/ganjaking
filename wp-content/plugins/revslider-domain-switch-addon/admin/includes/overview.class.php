<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

if(!defined('ABSPATH')) exit();

class rs_domain_switch_overview extends RevSliderFunctions {
	
	public static function init(){
		if(isset($_GET['page']) && $_GET['page'] == 'revslider'){
			add_action('admin_enqueue_scripts', array('rs_domain_switch_overview', 'wb_enqueue_scripts'));
		}
	}
	
	public static function wb_enqueue_scripts(){
		if(!isset($_GET['page'])) return;
		if($_GET['page'] !== 'revslider') return;
		
		wp_register_script('revslider-domain-switch-plugin-js', RS_DOMAIN_SWITCH_PLUGIN_URL . 'admin/assets/js/revslider-domain-switch-addon-admin.js', array('jquery', 'revbuilder-admin'), RS_DOMAIN_SWITCH_VERSION);
		wp_enqueue_script('revslider-domain-switch-plugin-js');
	}
}
?>