<?php

/**
 * Plugin Name: Master Addons for Elementor (Pro)
 * Description: Master Addons is easy and must have Elementor Addons for WordPress Page Builder. Clean, Modern, Hand crafted designed Addons blocks.
 * Plugin URI: https://master-addons.com/all-widgets/
 * Author: Jewel Theme
 * Version: 1.6.0
 * Author URI: https://master-addons.com
 * Text Domain: mela
 * Domain Path: /languages
 */
// No, Direct access Sir !!!
if ( !defined( 'ABSPATH' ) ) {
    exit;
}



$jltma_plugin_data = get_file_data( __FILE__, array(
    'Version'     => 'Version',
    'Plugin Name' => 'Plugin Name',
    'Author'      => 'Author',
    'Description' => 'Description',
    'Plugin URI'  => 'Plugin URI',
), false );
define( 'JLTMA_NAME', $jltma_plugin_data['Plugin Name'] );
define( 'JLTMA_PLUGIN_DESC', $jltma_plugin_data['Description'] );
define( 'JLTMA_PLUGIN_AUTHOR', $jltma_plugin_data['Author'] );
define( 'JLTMA_PLUGIN_URI', $jltma_plugin_data['Plugin URI'] );
define( 'JLTMA_PLUGIN_VERSION', $jltma_plugin_data['Version'] );
define( 'JLTMA_STABLE_VER', "1.5.9" );
define( 'JLTMA_BASE', plugin_basename( __FILE__ ) );


// Instantiate Master Addons Class
if ( !class_exists( '\\MasterAddons\\Master_Elementor_Addons' ) ) {
    require_once dirname( __FILE__ ) . '/class-master-elementor-addons.php';
}
// Activation and Deactivation hooks

if ( class_exists( '\\MasterAddons\\Master_Elementor_Addons' ) ) {
    register_activation_hook( __FILE__, array( '\\MasterAddons\\Master_Elementor_Addons', 'jltma_plugin_activation_hook' ) );
    register_deactivation_hook( __FILE__, array( '\\MasterAddons\\Master_Elementor_Addons', 'jltma_plugin_deactivation_hook' ) );
}
