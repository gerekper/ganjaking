<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class rs_domain_switch_base { // extends RevSliderFunctions
	
	public function __construct(){
		if(is_admin()){
			//Updates
			require_once(RS_DOMAIN_SWITCH_PLUGIN_PATH.'admin/includes/update.class.php');
			$update_admin = new rs_domain_switch_update(RS_DOMAIN_SWITCH_VERSION);
			add_filter('pre_set_site_transient_update_plugins', array($update_admin ,'set_update_transient'));
			add_filter('plugins_api', array($update_admin ,'set_updates_api_results'), 10, 3);

			require_once(RS_DOMAIN_SWITCH_PLUGIN_PATH.'admin/includes/overview.class.php');
			$rs_overview = new rs_domain_switch_overview();
			$rs_overview->init();
			
			add_action('revslider_do_ajax', array('rs_domain_switch_base', 'do_ajax'), 10, 3);
		}
	}
	
	
	public static function do_ajax($return, $action, $data){
		if(is_admin()){
			switch ($action) {
				case 'wp_ajax_save_values_revslider-domain-switch-addon':
					$revslider_domain_switch = array();
					if(isset($data['revslider_domain_switch_form'])){
						parse_str($data['revslider_domain_switch_form'], $revslider_domain_switch);
						
						if(!isset($revslider_domain_switch['revslider-domain-switch-addon-old']) || empty($revslider_domain_switch['revslider-domain-switch-addon-old'])) return __('Old domain can not be empty');
						if(!isset($revslider_domain_switch['revslider-domain-switch-addon-new']) || empty($revslider_domain_switch['revslider-domain-switch-addon-new'])) return __('New domain can not be empty');
						
						$rso = str_replace('/', '\/', $revslider_domain_switch['revslider-domain-switch-addon-old']);
						$rsn = str_replace('/', '\/', $revslider_domain_switch['revslider-domain-switch-addon-new']);
						
						//go through all tables and replace image URLs with new names
						global $wpdb;
						
						$sql = $wpdb->prepare("UPDATE ".$wpdb->prefix . RevSliderFront::TABLE_SLIDER. " SET `params` = replace(`params`, %s, %s)", array($rso, $rsn));
						$wpdb->query($sql);
						$sql = $wpdb->prepare("UPDATE ".$wpdb->prefix . RevSliderFront::TABLE_SLIDES. " SET `params` = replace(`params`, %s, %s)", array($rso, $rsn));
						$wpdb->query($sql);
						$sql = $wpdb->prepare("UPDATE ".$wpdb->prefix . RevSliderFront::TABLE_SLIDES. " SET `layers` = replace(`layers`, %s, %s)", array($rso, $rsn));
						$wpdb->query($sql);
						$sql = $wpdb->prepare("UPDATE ".$wpdb->prefix . RevSliderFront::TABLE_STATIC_SLIDES. " SET `params` = replace(`params`, %s, %s)", array($rso, $rsn));
						$wpdb->query($sql);
						$sql = $wpdb->prepare("UPDATE ".$wpdb->prefix . RevSliderFront::TABLE_STATIC_SLIDES. " SET `layers` = replace(`layers`, %s, %s)", array($rso, $rsn));
						$wpdb->query($sql);
						
						return 'Domains successfully changed in all sliders';
					}else{
						return 'No Data Send';
					}
					break;
				default:
					return $return;
					break;
			}
		}
		return $return;
	}
	
}
?>