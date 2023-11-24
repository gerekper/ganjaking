<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

/**
 * actions
 */
class UniteCreatorActions extends UniteCreatorActionsWork{
	
	
	/**
	 * on update layout response, function for override
	 */
	protected function onUpdateLayoutResponse($response){
		
				
		$isUpdate = $response["is_update"];
		
		//create
		if($isUpdate == false){
			
			$layoutID = $response["layout_id"];
			
			$urlRedirect = HelperUC::getViewUrl_Layout($layoutID);
			
			HelperUC::ajaxResponseSuccessRedirect(esc_html__("Layout Created, redirecting...", "unlimited-elements-for-elementor"), $urlRedirect);
			
		}else{
			//update
			
			$message = esc_html__("Updated", "unlimited-elements-for-elementor");
			
			HelperUC::ajaxResponseSuccess($message);
		}
		
	}
	
	
}