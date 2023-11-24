<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class BloxViewLicense{

	private $showHeader = true;
	
	
	/**
	 * put header html
	 */
	protected function putHeaderHtml(){
		
		$headerTitle = esc_html__(" License", "unlimited-elements-for-elementor");
		
		require HelperUC::getPathTemplate("header");
		
	}
	
	
	/**
	 * put the view
	 */
	public function display(){
				
		if($this->showHeader == true)
			$this->putHeaderHtml();
		else
			require HelperUC::getPathTemplate("header_missing");
		
			
		$path = HelperUC::getPathViewObject("activation_view.class");
		require_once $path;
		
		$pathProvider = GlobalsUC::$pathProviderViews."provider_activation_view.class.php";
		if(file_exists($pathProvider)){
			require_once $pathProvider;
			$objActivationView = new UniteCreatorActivationViewProvider();
			
		}else{
			$objActivationView = new UniteCreatorActivationView();
		}
		
		$webAPI = new UniteCreatorWebAPI();
		$isActive = $webAPI->isProductActive();
		
		?>
		<div class="unite-content-wrapper">
		<?php 
		
		if($isActive == true)
			$objActivationView->putHtmlDeactivate();
		else
			$objActivationView->putActivationHtml();
		
		$objActivationView->putJSInit();		
		
		?>
		</div>
		<?php 
	}
	
	
}


$objBloxViewLicense = new BloxViewLicense();
$objBloxViewLicense->display();
