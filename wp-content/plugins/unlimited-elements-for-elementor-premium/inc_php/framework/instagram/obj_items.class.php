<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class InstaObjUserUCItemsUC{
	
	protected $isInited = false;
	protected $istag = false;
	protected $status;
	protected $totalItems;
	protected $numItems;
	protected $arrItems = array();
	protected $isMoreAvaliable = false;
	protected $lastID = null;
	private $username;
	private $userID;
	private $objUser;
	private $profileImage;
	
	
	/**
	 * construct the class
	 */
	public function __construct(){
		
		$this->objUser = new InstaObjUserUC();
		
	}
	
	
	/**
	 * validate inited
	 */
	private function validateInited(){
		
		if($this->isInited == false)
			UniteFunctionsUC::throwError("the items object not inited");
		
	}
	
	private function _GETTERS(){}
	
	
	/**
	 * get items
	 */
	public function getItems(){
		
		$this->validateInited();
		return($this->arrItems);
	}
	
	/**
	 * get last ID
	 */
	public function getLastID(){
		
		$this->validateInited();
		
		return($this->lastID);
	}
	
	
	/**
	 * get user id
	 */
	public function getUserID(){
		
		$this->validateInited();
		
		return($this->userID);
	}
	
	/**
	 * get username text
	 */
	public function getUsernameText(){
		
		$name = $this->username;
		
		if(!empty($this->objUser))
			$name = $this->objUser->name;
		
		if(!$name)
			$name = $this->username;
		
		return($name);
	}
	
	
	/**
	 * get username
	 */
	public function getUsername(){
		
		if($this->istag == false)
			$username = "@".$this->username;
		else
			$username = "#".$this->username;
		
		return($username);
	}
	
	
	/**
	 * get profile image
	 */
	public function getProfileImage(){
		
		if(!empty($this->profileImage))
			return($this->profileImage);
		
		if(!$this->objUser)
			return(false);
		
		$profileImage = $this->objUser->urlProfileImage;
		
		if(!$profileImage)
			return(false);
		
		return($profileImage);
	}
	
	
	/**
	 * get if more available
	 */
	public function getIsMoreAvaliable(){
		
		return($this->isMoreAvaliable);
	}
	
	
	/**
	 * get link to page
	 */
	public function getLink(){
		
		if($this->istag == false)
			$link = "https://www.instagram.com/".$this->username;
		else
			$link = "https://www.instagram.com/explore/tags/".$this->username;
		
		return($link);
	}
	
	
	/**
	 * get page data
	 */
	public function getArrPageData(){
		
		$this->validateInited();
		
		$arr = array();
		$arr["name"] = $this->objUser->name;
		$arr["username"] = $this->getUsername();
		$arr["biography"] = $this->objUser->biography;
		$arr["image_profile"] = $this->objUser->urlProfileImage;
		$arr["num_followers"] = HelperInstaUC::convertNumberToText($this->objUser->numFollowedBy);
		$arr["num_following"] = HelperInstaUC::convertNumberToText($this->objUser->numFollows);
		$arr["num_posts"] = HelperInstaUC::convertNumberToText($this->objUser->numPosts);
		
		$arr["url_external"] = $this->objUser->externalUrl;
		$arr["link"] = $this->getLink();
		
		return($arr);
	}
	
	
	private function ___________SETTERS___________(){}
	
	
	/**
	 * parse items from api
	 */
	private function parseItems($items){
		
		$this->arrItems = array();
		
		if(empty($items))
			return(false);
		
		if(is_array($items) == false)
			return(false);
		
		foreach($items as $item){
						
			$objItem = new InstaObjItemUC();
			$objItem->init($item);
			
			$this->arrItems[] = $objItem;
		}
		
	}
	
	/**
	 * parse item new api
	 */
	private function parseItemsNewApi($arrNodes){
		
		$arrItems = array();
		foreach($arrNodes as $item){
			
			$objItem = new InstaObjItemUC();
			$objItem->initNewAPI($item);
						
			$arrItems[] = $objItem;
		}

		$this->arrItems = $arrItems;
		
	}
	
	/**
	 * set if it's user or tag
	 */
	public function setIsTag(){
		$this->istag = true;
	}
	
	
	/**
	 * init by api response
	 */
	public function init($apiResponse, $username){
		
		$this->username = $username;
		
		$this->status = UniteFunctionsUC::getVal($apiResponse, "status");
		
		if($this->status != "ok"){
			dmp("status not ok!!!");
			dmp($apiResponse);
			exit();
		}
		
		$moreAvailable = UniteFunctionsUC::getVal($apiResponse, "more_available");
		$this->isMoreAvaliable = UniteFunctionsUC::strToBool($moreAvailable);
		
		$items = UniteFunctionsUC::getVal($apiResponse, "items");
		$this->parseItems($items);
		
		$this->numItems = count($this->arrItems);
		
		//init user
		if($this->numItems == 0)
			$this->objUser = null;
		else{
			$firstItem = $this->arrItems[0];
			$this->objUser = $firstItem->itemUser;
			if(!$this->objUser)
				$this->objUser = null;
		}
		
		//set last ID
		if($this->numItems > 0)
			$this->lastID = $this->arrItems[$this->numItems-1]->getID();
		
		$this->isInited = true;
	}
	
	/**
	 * init from graph ql api
	 */
	public function initApiGraphQL($arrItemsData, $arrUserData){
		
		$arrData = UniteFunctionsUC::getVal($arrItemsData, "data");
		
		if(empty($arrData))
			return(null);
		
		$arrUser = UniteFunctionsUC::getVal($arrData, "user");
		
		if(empty($arrUser))
			return(null);
		
		$arrMedia = UniteFunctionsUC::getVal($arrUser, "edge_owner_to_timeline_media");

		if(empty($arrMedia))
			return(null);
		
		$arrEdges = UniteFunctionsUC::getVal($arrMedia, "edges");
		
		if(empty($arrEdges))
			return(null);
		
		//$keys = array_keys($arrEdges);
		$this->parseItemsNewApi($arrEdges);
		
		$this->totalItems = 0;
		
		//init obj user
		$this->userID = UniteFunctionsUC::getVal($arrUserData, "pk");
		$this->username = UniteFunctionsUC::getVal($arrUserData, "username");
		$this->objUser->initByNew($arrUserData);
		
		$this->isInited = true;
		
	}
	
	/**
	 * init new API
	 */
	public function initNewAPI($apiResponse){
		
		$arrInstance = null;
				
		if(isset($apiResponse["entry_data"])){
			$apiResponse = $apiResponse["entry_data"];
			$apiResponse = $apiResponse["ProfilePage"][0];
		}
		if(isset($apiResponse["graphql"]))
			$apiResponse = $apiResponse["graphql"];
		
					
		if(isset($apiResponse["user"]))
			$arrInstance = $apiResponse["user"];
		else
			if(isset($apiResponse["tag"]))
				$arrInstance = $apiResponse["tag"];
		
		//init user
		if(!empty($apiResponse["user"])){
			$this->userID = UniteFunctionsUC::getVal($arrInstance, "id");
			$this->username = UniteFunctionsUC::getVal($arrInstance, "username");
			$this->objUser->initByNew($arrInstance);
		}
		
		if(empty($arrInstance))
			UniteFunctionsUC::throwError("Server error - instance items not found");
				
		$arrTopPosts = UniteFunctionsUC::getVal($arrInstance, "top_posts");
		$arrMedia = UniteFunctionsUC::getVal($arrInstance, "edge_owner_to_timeline_media");

		
		$arrNodes = array();
		if(!empty($arrTopPosts))
			$arrNodes = $arrTopPosts["nodes"];
		
		if(!empty($arrMedia)){
			$arrMediaNodes = $arrMedia["edges"];
			foreach($arrMediaNodes as $node)
				$arrNodes[] = $node;
		}
		
		if(empty($arrNodes))
			UniteFunctionsUC::throwError("No items found");
		
		$this->parseItemsNewApi($arrNodes);
		
		//get total items
		$this->totalItems = UniteFunctionsUC::getVal($arrMedia, "count");
		if(empty($this->totalItems))
			$this->totalItems = 0;
		
		$arrPageInfo = UniteFunctionsUC::getVal($arrMedia, "page_info");
		
		$this->isMoreAvaliable = false;
		
		if(!empty($arrPageInfo)){
			$hasNext = UniteFunctionsUC::getVal($arrPageInfo, "has_next_page");
			$hasNext = UniteFunctionsUC::strToBool($hasNext);
			$this->isMoreAvaliable = $hasNext;
			if($hasNext == true)
				$this->lastID = UniteFunctionsUC::getVal($arrPageInfo, "end_cursor");
		}
		
		
		$this->isInited = true;
	}
	
	/**
	 * init from official api
	 */
	public function initOfficialAPI($arrItemsData, $arrUserData){
		
		$this->userID = UniteFunctionsUC::getVal($arrUserData, "id");
		$this->username = UniteFunctionsUC::getVal($arrUserData, "username");
		$this->isInited = true;
		
		$this->isMoreAvaliable = false;
		
		if(empty($arrItemsData))
			UniteFunctionsUC::throwError("No Items Found");
		
		foreach($arrItemsData as $item){
			
			$objItem = new InstaObjItemUC();
			$objItem->initOfficialAPI($item);
			
			$this->arrItems[] = $objItem;
		}
		
		$this->numItems = count($this->arrItems);
		
	}
	
	
	/**
	 * print the data
	 */
	public function printData(){
		
		$this->validateInited();

		dmp("num items: ".$this->numItems);
		
		dmp("---------------");
		
		foreach($this->arrItems as $key => $item){
			
			dmp($key);
			
			$item->printData();
			
		}
		
		
	}
	
	
}