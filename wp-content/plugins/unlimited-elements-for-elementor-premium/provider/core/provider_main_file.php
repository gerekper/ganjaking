<?php 

try{
	
	//-------------------------------------------------------------
	
	//load core plugins
	
	$pathCorePlugins = dirname(__FILE__)."/plugins/";
			
	$pathUnlimitedElementsPlugin = $pathCorePlugins."unlimited_elements/plugin.php";
		require_once $pathUnlimitedElementsPlugin;
	
	$pathCreateAddonsPlugin = $pathCorePlugins."create_addons/plugin.php";
		require_once $pathCreateAddonsPlugin;
	
	if(is_admin()){		//load admin part
		
		do_action(GlobalsProviderUC::ACTION_RUN_ADMIN);
		
		
	}else{		//load front part
		
		do_action(GlobalsProviderUC::ACTION_RUN_FRONT);
		
	}

	
	}catch(Exception $e){
		$message = $e->getMessage();
		$trace = $e->getTraceAsString();
		echo "Error: <b>".$message."</b>";
		
		if(GlobalsUC::SHOW_TRACE == true)
			dmp($trace);
	}
	
	
?>