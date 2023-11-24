<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class InstagramAPIUC{
	
	
	/**
	 * call API
	 */
	private function callAPI($url){
		
		$response = HelperInstaUC::getRemoteUrl($url);
		
		$arrResponse = @json_decode($response);
		if(empty($arrResponse))
			UniteFunctionsUC::throwError($response);
		
		$arrResponse = UniteFunctionsUC::convertStdClassToArray($arrResponse);
		
		
		return($arrResponse);
	}
	
	
	
	
	/**
	 * get images from user
	 */
	public function getUserData($user, $lastID = null){

		$user = HelperInstaUC::sanitizeUser($user);
		
		HelperInstaUC::validateUser($user);
		
		$url = "https://www.instagram.com/".$user."/media/";
		
		if(!empty($lastID)){
			$url .= "?max_id=".$lastID;
		}
				
		$cacheKey = "instagallery_".$user."_".$lastID;
		
		$response = HelperInstaUC::getFromCache($cacheKey);
		
		if(empty($response)){
			$response = $this->callAPI($url);
			HelperInstaUC::cacheResponse($cacheKey, $response);
		}
				
		$objItems = new InstaObjUserUCItemsUC();
		$objItems->init($response, $user);
		
		
		return($objItems);
	}
	
	
}