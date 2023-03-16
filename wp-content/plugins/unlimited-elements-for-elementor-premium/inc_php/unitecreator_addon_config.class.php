<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorAddonConfig extends HtmlOutputBaseUC{
	
	
	private $startWithAddon = false;
	private $isPreviewMode = false;
	private $startAddon;
	private $selectedTab = null;
	private $isSourceAddon = false;
	private $isFontsPanelEnabled = true;
	
	
	/**
	 * construct the object
	 */
	public function __construct(){
	
	}
	
	
	/**
	 * validate start addon
	 */
	private function valdiateStartAddon(){
		
		if($this->startWithAddon == false)
			UniteFunctionsUC::throwError("No start addon found");
	
	}
	
	
	/**
	 * get preview html
	 */
	private function getHtmlPreview(){
		
		$html = "";
		
		//preview
		$html .= self::TAB2."<div class='uc-addon-config-preview' style='display:none'>".self::BR;
		$html .= 	self::TAB3."<div class='uc-addon-config-preview-title'>Preview".self::BR;
		$html .= 	self::TAB3."</div>".self::BR;
		
		$html .= 	self::TAB3."<div class='uc-preview-content'>".self::BR;
		
		$html .= 	self::TAB4."<iframe class='uc-preview-iframe'>".self::BR;
		$html .= 	self::TAB4."</iframe>".self::BR;
		
		$html .= 	self::TAB3."</div>".self::BR;
		
		$html .= 	self::TAB2."</div>".self::BR;
		
		return($html);
	}
	
	
	/**
	 * get item settings html
	 */
	private function getHtmlSettings($putMode = false){
		
		$html = "";
		
		$html .= 	self::TAB3."<div class='uc-addon-config-settings unite-settings'>".self::BR;
		
		if($putMode == true){
			echo UniteProviderFunctionsUC::escCombinedHtml($html);
			$html = "";
		}
		
		if($this->startWithAddon == true){
			
			$params = array();
			if($this->isSourceAddon == true)
				$params["source"] = "addon";
			
			if($this->isFontsPanelEnabled == false)
				$params["disable_fonts"] = true;
			
			if($putMode == true)
			
				$this->startAddon->putHtmlConfig(false,$params);
			else{
				
				$htmlConfig = $this->startAddon->getHtmlConfig(false,false,$params);
				$html .= $htmlConfig;
			}
		}
		
		$html .= self::TAB3."</div>".self::BR;	//settings
		
		
		if($putMode == true)
			echo UniteProviderFunctionsUC::escCombinedHtml($html);
		else		
			return($html);
	}
	
	
	
	
	
	private function a___________OTHERS____________(){}
	
	
	/**
	 * get tab html
	 */
	private function getHtmlTab($name, $text){
	    
	    $selectedClass = "";
	    
	    if(empty($this->selectedTab)){
	        $this->selectedTab = $name;
	        $selectedClass = " uc-tab-selected";
	    }
	    
	    $html = self::TAB3."<a href='javascript:void(0)' data-name='{$name}' onfocus='this.blur()' class='uc-addon-config-tab {$selectedClass}'>".$text."</a>".self::BR;
	    
	    return($html);
	}	
	
	
	/**
	 * put html frame of the config
	 */
	public function getHtmlFrame($putMode = false){
		
		$title = esc_html__("Addon Title", "unlimited-elements-for-elementor");
		$this->valdiateStartAddon();
		
		$addHtml = "";
		$title = $this->startAddon->getTitle(true);
		$title .= " - ".esc_html__("Config", "unlimited-elements-for-elementor");
		
		$titleSmall = $this->startAddon->getTitle(true);
		
		$addonName = $this->startAddon->getNameByType();
		$addonID = $this->startAddon->getID();
		$addonType = $this->startAddon->getType();
		
		$enableFontsPanel = true;
		
		$options = $this->startAddon->getOptions();
		$urlIcon = $this->startAddon->getUrlIcon();
				
		$options["title"] = $this->startAddon->getTitle();
		$options["url_icon"] = $urlIcon;
		$options["addon_name"] = $addonName;
		$options["addon_id"] = $addonID;
		$options["addon_type"] = $addonType;
		$options["admin_labels"] = $this->startAddon->getAdminLabels();
		
		$strOptions = UniteFunctionsUC::jsonEncodeForHtmlData($options,"options");
		
		$addonName = esc_attr($addonName);
		$addonType = esc_attr($addonType);
		
		$addHtml .= " data-name=\"{$addonName}\" data-addontype=\"{$addonType}\" {$strOptions} ";
		
		$html = "";
		
		//settings
		$html .= self::TAB. "<div id='uc_addon_config' class='uc-addon-config' {$addHtml}>".self::BR;
		
		//set preview style
		$styleConfigTable = "";
		if($this->isPreviewMode == true)
			$styleConfigTable = "style='display:none'";

		
		$html .= self::TAB."<div class='uc-addon-config-table'>".self::BR;
				
		//put settings
		if($putMode == true){
			echo UniteProviderFunctionsUC::escCombinedHtml($html);
			$html = "";
			$this->getHtmlSettings(true);
		}else{
			$html .= $this->getHtmlSettings();
		}
		
		$html .= self::TAB."</div>";	
		
		//end config table
		
		
		//end preview table
		$html .= $this->getHtmlPreview();
		
		$html .= self::TAB."</div>".self::BR;	//main wrapper
		
		if($putMode == true)
			echo UniteProviderFunctionsUC::escCombinedHtml($html);
		else
			return($html);
	}
	
	
	/**
	 * put html frame
	 */
	public function putHtmlFrame(){
		$this->getHtmlFrame(true);
	}
	
	
	
	/**
	 * set to start with preview
	 */
	public function startWithPreview($isPreview){
		
		$this->isPreviewMode = $isPreview;
	}
	
	
	/**
	 * set addon as image source
	 */
	public function setSourceAddon(){
		$this->isSourceAddon = true;
	}
	
	
	/**
	 * set start addon
	 */
	public function setStartAddon(UniteCreatorAddon $objAddon){
		$this->startWithAddon = true;
		
		UniteFunctionsUC::validateNotEmpty($objAddon, "addon");
		
		$this->startAddon = $objAddon;
		
	}
	
	/**
	 * disable fonts panel in the config
	 */
	public function disableFontsPanel(){
		
		$this->isFontsPanelEnabled = false;
		
	}
		
	
}