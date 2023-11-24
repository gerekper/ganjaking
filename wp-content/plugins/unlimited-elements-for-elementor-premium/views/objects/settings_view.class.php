<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorSettingsView{
	
	const SETTINGS_KEY_GENERAL_SETTINGS = "general_settings";
	
	protected $showHeader = true;
	protected $headerTitle = null;
	protected $saveAction = null;
	
	protected $isModeCustomSettings = false;	//any custom settings
	protected $customSettingsKey = null;
	protected $customSettingsXmlFile = null;
	
	protected $objSettings = null;
	protected $textButton = null;
	
	
	/**
	 * function for override
	 */
	protected function drawAdditionalTabs(){}
	
	protected function drawAdditionalTabsContent(){}
	
	
	/**
	 * draw save settings button
	 */
	protected function drawSaveSettingsButton($prefix){
		
		$buttonText = $this->textButton;
		if(empty($buttonText))
			$buttonText = esc_html__("Save Settings", "unlimited-elements-for-elementor");
		
		$addParams = "";
		if($this->isModeCustomSettings == true)
			$addParams = " data-settingskey='{$this->customSettingsKey}'";
		
		
		?>
			<div class="uc-button-action-wrapper">
			
				<a id="<?php echo esc_attr($prefix)?>_button_save_settings" data-prefix="<?php echo esc_attr($prefix)?>" <?php echo UniteProviderFunctionsUC::escAddParam($addParams)?> class="unite-button-primary uc-button-save-settings" href="javascript:void(0)"><?php echo esc_html($buttonText)?></a>
				
				<div style="padding-top:6px;">
					
					<span id="<?php echo esc_attr($prefix)?>_loader_save" class="loader_text" style="display:none"><?php esc_html_e("Saving...", "unlimited-elements-for-elementor")?></span>
					<span id="<?php echo esc_attr($prefix)?>_message_saved" class="unite-color-green" style="display:none"></span>
					
				</div>
			</div>
			
			<div class="unite-clear"></div>
			
			<div id="<?php echo esc_attr($prefix)?>_save_settings_error" class="unite_error_message" style="display:none"></div>
		
		<?php 
	}
	
	
	/**
	 * validate that the view is inited
	 */
	private function validateInited(){
		
		if(empty($this->headerTitle))
			UniteFunctionsUC::throwError("Please init the header title variable");
		
		if($this->isModeCustomSettings == true){
			UniteFunctionsUC::validateNotEmpty($this->customSettingsKey, "Custom settings key");
			UniteFunctionsUC::validateNotEmpty($this->customSettingsXmlFile, "Custom settings xml file");
		}
		
		if(empty($this->saveAction))
			UniteFunctionsUC::throwError("Please init the save action");
		
		if(empty($this->objSettings))
			UniteFunctionsUC::throwError("Please init the settings object");
				
	}
	
	/**
	 * modify custom settings - function for override
	 */
	protected function modifyCustomSettings($settings){
		
		return($settings);
	}
	
	
	/**
	 * init the custom mode
	 */
	protected function initCustomMode(){
		
		$this->saveAction = "save_custom_settings";
		
		UniteFunctionsUC::validateNotEmpty($this->customSettingsXmlFile,"xml file( customSettingsXmlFile variable)");
		
		$this->objSettings = new UniteCreatorSettings();
		$this->objSettings->loadXMLFile($this->customSettingsXmlFile);
		
		$arrValues = HelperUC::$operations->getCustomSettingsValues($this->customSettingsKey);
		
		if(!empty($arrValues))
			$this->objSettings->setStoredValues($arrValues);
		
		$this->objSettings = $this->modifyCustomSettings($this->objSettings);
		
	}
	
	/**
	 * add scripts
	 */
	protected function addScripts(){
		
		HelperUC::addScript("unitecreator_admin_generalsettings", "unitecreator_admin_generalsettings");
		
	}
	
	/**
	 * display settings
	 */
	protected function display(){
		
		$this->addScripts();
		
		if($this->isModeCustomSettings == true)
			$this->initCustomMode();
		
		$this->validateInited();
				
		//show header
		if($this->showHeader == true){
			$headerTitle = $this->headerTitle;
			require HelperUC::getPathTemplate("header");
		}else
			require HelperUC::getPathTemplate("header_missing");
		
		
		$objSettings = $this->objSettings;
		
		//get saps
		$arrSaps = $objSettings->getArrSaps();
	
		$formID = "uc_general_settings";
	
		$objOutput = new UniteSettingsOutputWideUC();
		$objOutput->init($objSettings);
		$objOutput->setFormID($formID);
		
		$randomString = UniteFunctionsUC::getRandomString(5, true);
		
		require HelperUC::getPathTemplate("settings");
	}
	
	
}