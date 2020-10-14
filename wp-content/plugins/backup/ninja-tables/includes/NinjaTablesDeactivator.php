<?php namespace NinjaTables\Classes;

/**
 * Fired during plugin deactivation
 *
 * @link       https://wpmanageninja.com
 * @since      1.0.0
 *
 * @package    Wp_table_data_press
 * @subpackage Wp_table_data_press/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Wp_table_data_press
 * @subpackage Wp_table_data_press/includes
 * @author     Shahjahan Jewel <cep.jewel@gmail.com>
 */
class NinjaTablesDeActivator {

    private static $apiUrl = 'https://wpmanageninja.com/?wpmn_api=product_users';

    /**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
    {
        // check opted in users
        $leadSatus = get_option( '_ninja_table_lead_options', array() );
        if(!empty($leadSatus['lead_optin_status']) && $leadSatus['lead_optin_status'] == 'yes') {
            $currentUser = wp_get_current_user();
            $data = array(
                'first_name' => $currentUser->first_name,
                'last_name' => $currentUser->last_name,
                'display_name' => $currentUser->display_name,
                'email' => $currentUser->user_email,
                'site_url' => site_url(),
                'request_from' => self::get_request_from(),
                'plugins' => self::getPluginsInfo(),
                'ninja_doing_action' => 'deactivate'
            );
            wp_remote_post(self::$apiUrl, array(
                'method' => 'POST',
                'sslverify' => false,
                'body' => $data
            ));
        }
	}

    // Function to get the client IP address
    private static function get_request_from() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }


    private static function getPluginsInfo()
    {
        $activePlugins = get_option('active_plugins', array());
        $inActivePlugins = array();
        $all_plugins = get_plugins();
        foreach ($all_plugins as $pluginName => $plugin) {
            if(!in_array($pluginName, $activePlugins)) {
                $inActivePlugins[] = $pluginName;
            }
        }

        return array(
            'actives' => $activePlugins,
            'inactives' => $inActivePlugins
        );
    }

}
