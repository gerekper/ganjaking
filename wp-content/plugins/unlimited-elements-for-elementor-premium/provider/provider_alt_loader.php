<?php


/**
 * return if addon creator plugin exists and active
 */
function UCIsAddonLibraryPluginExists(){
	
	$alPlugin = "addon-library/addonlibrary.php";
	$alPlugin2 = "unlimited-addons-for-wpbakery-page-builder/unlimited_addons.php";
	
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	
	$arrPlugins = get_plugins();
	
	if(isset($arrPlugins[$alPlugin]) == true){
		$isActive = is_plugin_active($alPlugin);
		
		return($isActive);
	}
	
	if(isset($arrPlugins[$alPlugin2]) == true)
		return(true);
		
	$isActive = is_plugin_active($alPlugin);
	
	return(false);
}


if(UCIsAddonLibraryPluginExists()){
	
	require_once dirname(__FILE__)."/views/compatability_message.php";
	
}else{

	
	try{
		
		require_once $currentFolder.'/includes.php';
		
		HelperUC::validatePluginStartup();
				
		require_once  GlobalsUC::$pathProvider."core/provider_main_file.php";
				
	}catch(Exception $e){
		
		$ucStandAloneErrorMessage = $e->getMessage();
		$filePathViewStandAlone = dirname(__FILE__)."/views/stand_alone_broken_error.php";
		
		require $filePathViewStandAlone;	
		
	}
	
}

