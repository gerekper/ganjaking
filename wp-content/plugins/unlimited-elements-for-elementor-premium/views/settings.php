<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

require	HelperUC::getPathViewObject("settings_view.class");

class UniteCreatorViewGeneralSettings extends UniteCreatorSettingsView{
		
	
	/**
	 * draw additional tabs
	 */
	protected function drawAdditionalTabs(){
		?>
		
		<a data-contentid="uc_tab_change_log" class="" href="javascript:void(0)" onfocus="this.blur()"> <?php esc_html_e("Change Log", "unlimited-elements-for-elementor") ?></a>
		
		<?php 
	}
	
	
	/**
	 * function for override
	 */
	protected function drawAdditionalTabsContent(){
		
		$textChangeLog = HelperHtmlUC::getVersionText();
		
		?>
		<div id="uc_tab_change_log" style="display:none" class="uc-tab-content">
			<div class="uc-change-log-wrapper">
			<pre>
				<?php echo UniteProviderFunctionsUC::escCombinedHtml($textChangeLog)?>
			</pre>
			</div>
		</div>
		
		<?php 
	}
	
	
	/**
	 * constructor
	 */
	public function __construct(){
		
		$this->headerTitle = esc_html__("General Settings", "unlimited-elements-for-elementor");
		$this->saveAction = "update_general_settings";
		
		//set settings
		$operations = new UCOperations();
		$this->objSettings = $operations->getGeneralSettingsObject();
		
		$this->display();
	}
	
	
	
}

$filepathViewSettingsProvider = GlobalsUC::$pathProviderViews."general_settings.php";

if(isset($filepathViewSettingsProvider)){
	require $filepathViewSettingsProvider;
		
	new UniteCreatorViewGeneralSettingsProvider();
}else{
	
	new UniteCreatorViewGeneralSettings();
}
	
