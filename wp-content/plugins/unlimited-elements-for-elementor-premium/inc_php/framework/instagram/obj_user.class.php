<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class InstaObjUserUC{
	
	public $isInited = false;
	public $username,$urlProfileImage,$id,$name,$externalUrl,$numFollows;
	public $numFollowedBy, $biography, $urlProfileImageHD, $userData, $numPosts;
	
	
	/**
	 * init user
	 */
	public function init($user){
		
		if(empty($user))
			return(false);
		
		if(is_array($user) == false)
			return(false);
		
		$this->username = UniteFunctionsUC::getVal($user, "username");
		$this->urlProfileImage = UniteFunctionsUC::getVal($user, "profile_picture");
		$this->id = UniteFunctionsUC::getVal($user, "id");
		$this->name = UniteFunctionsUC::getVal($user, "full_name");
		
		$this->isInited = true;
	}
	
		
	/**
	 * init by new API
	 */
	public function initByNew($user){
		
		
		$this->externalUrl = UniteFunctionsUC::getVal($user, "external_url");
				
		$this->name = UniteFunctionsUC::getVal($user, "full_name");
				
		$this->id = UniteFunctionsUC::getVal($user, "id");
		
		$media = UniteFunctionsUC::getVal($user, "edge_owner_to_timeline_media"); 
		$this->numPosts = UniteFunctionsUC::getVal($media, "count"); 
		
		$arrFollows = UniteFunctionsUC::getVal($user, "edge_follow");
		$this->numFollows = UniteFunctionsUC::getVal($arrFollows, "count");
				
		$arrFollowedBy = UniteFunctionsUC::getVal($user, "edge_followed_by");
		$this->numFollowedBy = UniteFunctionsUC::getVal($arrFollowedBy, "count");
				
		$this->urlProfileImage = UniteFunctionsUC::getVal($user, "profile_pic_url");
				
		$this->urlProfileImageHD = UniteFunctionsUC::getVal($user, "profile_pic_url_hd");
		
		$this->biography = UniteFunctionsUC::getVal($user, "biography");
				
		$this->username = UniteFunctionsUC::getVal($user, "username");
				
		$this->userData = $user;
		
		$this->isInited = true;
		
	}
	
	
	/**
	 * init by new API - from comment
	 */
	public function initByComment($user){
		
		$this->id = UniteFunctionsUC::getVal($user, "id");
		$this->urlProfileImage = UniteFunctionsUC::getVal($user, "profile_pic_url");
		$this->username = UniteFunctionsUC::getVal($user, "username");
		
		$this->isInited = true;
	}
	
	
}