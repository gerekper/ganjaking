<?php
function seed_cspv5_get_settings(){
    $settings = get_option('seed_cspv5_settings_content');
    if(empty($settings)){
    	require_once( SEED_CSPV5_PLUGIN_PATH.'admin/default-settings.php' );
    	$defaults = apply_filters( 'seed_cspv5_settings_default', $seed_cspv5_settings_defaults['seed_cspv5_settings_content'] );
		add_option('seed_cspv5_settings_content',$defaults);
    }
    return apply_filters( 'seed_cspv5_get_settings', $settings );
}
