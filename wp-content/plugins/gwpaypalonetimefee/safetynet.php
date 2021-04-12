<?php

if(!class_exists('GWPerk')) {
    
    include_once(ABSPATH . '/wp-admin/includes/plugin.php');
    
    if( ( is_network_admin() && is_plugin_active_for_network(plugin_basename($gw_perk_file)) ) || 
        ( !is_network_admin() && function_exists('is_plugin_active') && is_plugin_active(plugin_basename($gw_perk_file)) ) ) { 
            add_action('after_plugin_row_' . plugin_basename($gw_perk_file), 'after_perk_plugin_row', 10, 2); 
        }
    
    if(!function_exists('after_perk_plugin_row')) { 
        function after_perk_plugin_row($plugin_file, $plugin_data) { 
            echo '</tr><tr class="plugin-update-tr gwp-plugin-notice"><td colspan="5" class="plugin-update"><style type="text/css">#' . sanitize_title( $plugin_data['Name'] ) . ' td, #' . sanitize_title( $plugin_data['Name'] ) . ' th { border-bottom: 0; }</style><div class="update-message" style="background-color: #ffebe8;">' . sprintf(__('This plugin requires Gravity Perks. Activate it now or %1$spurchase it today!%2$s', 'gravityperks'), '<a href="' . $plugin_data['PluginURI'] . '">', '</a>') . '</div></td>';
        }
    }
    
    return false;
}

?>