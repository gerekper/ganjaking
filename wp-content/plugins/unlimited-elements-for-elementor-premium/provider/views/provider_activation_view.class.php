<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorActivationViewProvider extends UniteCreatorActivationView{
	
	const ENABLE_STAND_ALONE = true;
	
	/**
	 * init by envato
	 */
	private function initByEnvato(){
				
		$this->textGoPro = esc_html__("Activate Blox Pro", "unlimited-elements-for-elementor");
		
		if(self::ENABLE_STAND_ALONE == true)
			$this->textGoPro = esc_html__("Activate Blox Pro - Envato", "unlimited-elements-for-elementor");
		
		$this->textPasteActivationKey = esc_html__("Paste your envato purchase code here <br> from the pro version item", "unlimited-elements-for-elementor");
		$this->textPlaceholder = esc_html__("xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx","unlimited-elements-for-elementor");
		
		$this->textLinkToBuy = null; 
		$this->urlPricing = null;
		
		$this->textDontHave = esc_html__("We used to sell this product in codecanyon.net <br> Activate from this screen only if you bought it there.","unlimited-elements-for-elementor");
		
		$this->textActivationFailed = esc_html__("You probably got your purchase code wrong", "unlimited-elements-for-elementor");
		$this->codeType = self::CODE_TYPE_ENVATO;
		$this->isExpireEnabled = false;
		
		
		if(self::ENABLE_STAND_ALONE == true){
			
			$urlRegular = HelperUC::getViewUrl("license");
			$htmlLink = HelperHtmlUC::getHtmlLink($urlRegular, esc_html__("Activate With Blox Key", "unlimited-elements-for-elementor"),"","blue-text");
			
			$this->textSwitchTo = esc_html__("Don't have Envato Activation Key? ","unlimited-elements-for-elementor").$htmlLink;
		}
		
		$this->textDontHaveLogin = null;
		
	}
	
	
	/**
	 * init by blox wp
	 */
	private function initByBloxWP(){
		
		$urlEnvato = HelperUC::getViewUrl("license","envato=true");
		$htmlLink = HelperHtmlUC::getHtmlLink($urlEnvato, esc_html__("Activate With Envato Key", "unlimited-elements-for-elementor"),"","blue-text");
		
		$this->urlPricing = "http://blox-builder.com/go-pro/";
		$this->textSwitchTo = esc_html__("Have Envato Market Activation Key? ","unlimited-elements-for-elementor").$htmlLink;
		
	}
	
	
	
	/**
	 * init the variables
	 */
	public function __construct(){
				
		parent::__construct();
		
		$this->textGoPro = esc_html__("Activate Blox Pro", "unlimited-elements-for-elementor");
		$this->writeRefreshPageMessage = false;
		
		$isEnvato = UniteFunctionsUC::getGetVar("envato", "", UniteFunctionsUC::SANITIZE_KEY);
		$isEnvato = UniteFunctionsUC::strToBool($isEnvato);
		
		if(self::ENABLE_STAND_ALONE == false)
			$isEnvato = true;
		
		if($isEnvato == true)
			$this->initByEnvato();
		else
			$this->initByBloxWP();
			
	}
	
		
}