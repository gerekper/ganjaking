<?php

defined('UNLIMITED_ELEMENTS_INC') or die;

class UniteCreatorViewGeneralSettingsProvider extends UniteCreatorViewGeneralSettings{

	/**
	 * draw additional tabs
	 */
	protected function drawAdditionalTabs(){
		parent::drawAdditionalTabs();
		return(false);
		
		?>
			<a data-contentid="uc_tab_developers" href="javascript:void(0)" onfocus="this.blur()"> <?php esc_html_e("Theme Developers", "unlimited-elements-for-elementor")?></a>
		<?php
		
	}
	
	
	/**
	* function for override
	*/
	protected function drawAdditionalTabsContent(){
		
		parent::drawAdditionalTabsContent();
		return(false);
		
		$randomString = UniteFunctionsUC::getRandomString(5);
		
		?>
		<div id="uc_tab_developers" class="uc-tab-content" style="display:none">
			Dear Theme Developer. <br><br>
			
			If you put the Unlimited Elements as part of your theme and want
			the addons to auto install on plugin activation or theme switch, <br>
			please create folder <b>"<?php GlobalsUC::DIR_THEME_ADDONS?>"</b> inside your theme and put the addons import zips there. <br>
			example: <b>wp-content/themes/yourtheme/<?php echo GlobalsUC::DIR_THEME_ADDONS?></b>
			
			<br><br>
			If you want to put them to another path please copy this code to your theme <b>functions.php</b> file:
			
			<br><br>
			
<textarea cols="80" rows="6" readonly onfocus="this.select()">				
/**
* Set Unlimited Elements addons install folder. 
* example: 'installs/addons' (will be wp-content/themes/installs/addons)
**/
function set_addons_install_path_<?php echo esc_html($randomString)?>($value){
	
	//change the 'yourfolder' to the folder you want
	
	return(&quot;yourfolder&quot;);
}

add_filter(&quot;blox_path_theme_addons&quot;, &quot;set_addons_install_path_<?php echo esc_html($randomString)?>&quot;);
</textarea>
			
		</div>
	
	<?php
	}
	
	
}