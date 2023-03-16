<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorBrowser extends UniteCreatorBrowserWork{
	
	/**
	 * constructor
	 */
	public function __construct(){
		
		parent::__construct();

		$urlLicense = HelperUC::getViewUrl(GlobalsUC::VIEW_LICENSE);
		
		$this->textBuy = esc_html__("Activate Blox", "unlimited-elements-for-elementor");
		$this->textHoverProAddon = __("This addon is available<br>when blox is activated.", "unlimited-elements-for-elementor");
		$this->urlBuy = $urlLicense;
		
	}
	
}