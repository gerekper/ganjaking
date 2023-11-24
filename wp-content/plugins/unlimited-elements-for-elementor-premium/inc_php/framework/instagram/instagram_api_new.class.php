<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class InstagramAPINewUC{
		
	
	private static $urlBase = "https://www.instagram.com/";
	const CACHE_RESPONSE = true;
	private $lastAPIError = "";
	const DEBUG_SERVER_REQUEST = false;
	
	
	/**
	 * get data from raw response
	 */
	private function getDataFromRawResponse($responseRaw){
		   
		$regex = '#window\._sharedData\s?=\s?(.*);<\/script>#';
		$found = preg_match($regex, $responseRaw, $arrMatches);
		if(empty($found))
			UniteFunctionsUC::throwError("no data found");
	
		if(count($arrMatches) < 2)
			UniteFunctionsUC::throwError("no data extracted");
		
		$jsonData = $arrMatches[1];
		
		return($jsonData);
	}
	
	/**
	 * get headers array
	 */
	private function getArrHeaders(){
		
		$arrHeaders = array();
        $arrHeaders[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.87 Safari/537.36";
		$arrHeaders[] = "Origin: https://www.instagram.com";
		$arrHeaders[] = "Referer: https://www.instagram.com";
		$arrHeaders[] = "Connection: close";
		$arrHeaders[] = "Cookie: ig_or=landscape-primary; ig_pr=1; ig_vh=1080; ig_vw=1920; ds_user_id=25025320";
		
		return($arrHeaders);
	}
	
	/**
	 * call api
	 */
	private function serverRequest($url, $cacheSeconds = null){
		
		$arrHeaders = $this->getArrHeaders();
		
		$response = null;
		
		if(self::DEBUG_SERVER_REQUEST == true){
			dmp("server request for url: $url");
			dmp("caching responses: ".self::CACHE_RESPONSE);
		}
		
		
		//get response from cache
		if(self::CACHE_RESPONSE == true){
			$cacheKey = $this->createCacheKey($url);
			$response = HelperInstaUC::getFromCache($cacheKey);
			
			if(self::DEBUG_SERVER_REQUEST){
				dmp("get from cache");
				if(empty($response))
					dmp("empty cached response");				
			}
			
		}

		//get respose from instagram
		if(empty($response) || self::CACHE_RESPONSE == false){
			
			
			if(self::DEBUG_SERVER_REQUEST == true){
				dmp("get remote url");
			}
			
			$responseRaw = HelperInstaUC::getRemoteUrl($url, $arrHeaders);
						
			$response = UniteFunctionsUC::jsonDecode($responseRaw);
						
			//decode raw response
			if(empty($response))	
				$response = $this->getDataFromRawResponse($responseRaw);
			
			if(self::CACHE_RESPONSE == true && !empty($response))
				HelperInstaUC::cacheResponse($cacheKey, $response, $cacheSeconds);
		}
		
		return($response);
	}
	
	
	/**
	 * do graph ql request
	 */
	private function serverGraphQLRequest($variables){
		
		$queryHash = "f2405b236d85e8296cf30347c9f08c2a";
		
        $jsonVars = json_encode($variables);
		
        $gis = md5(join(':', array("", $jsonVars)));
		
        $arrHeaders = $this->getArrHeaders();
        
        $arrHeaders["X-Csrftoken"] = "";
        $arrHeaders["X-Requested-With"] = "XMLHttpRequest";
        $arrHeaders["X-Instagram-Ajax"] = "1";
        $arrHeaders["X-Instagram-Gis"] = $gis;
        
        $params = array();
        $params["query_hash"] = $queryHash;
        $params["variables"] = $jsonVars;
        
        $url = self::$urlBase."graphql/query/";
		
        if(self::DEBUG_SERVER_REQUEST == true){
        	dmp("getting graph ql for this vars: ");
        	dmp($variables);
        }
        
        try{
        	
        	$responseRaw = null;
        	
			//get response from cache
			if(self::CACHE_RESPONSE == true){
				
				$cacheUrl = $url."&vars=".$jsonVars;
				
				$cacheKey = $this->createCacheKey($cacheUrl);
				$responseRaw = HelperInstaUC::getFromCache($cacheKey);
				
				if(self::DEBUG_SERVER_REQUEST == true){
					dmp("get from cache");
					
					if(empty($responseRaw))
						dmp("empty response");
				}
			}
        	
        	if(empty($responseRaw)){
        		
				if(self::DEBUG_SERVER_REQUEST == true)
					dmp("get from server");
				        		
        		$responseRaw = HelperInstaUC::getRemoteUrl($url, $arrHeaders, $params, false);
        		
		        if(self::CACHE_RESPONSE == true)
		        	HelperInstaUC::cacheResponse($cacheKey, $responseRaw);
        	}
        	
        	$arrResponse = UniteFunctionsUC::jsonDecode($responseRaw);
        	
        	if(empty($arrResponse))
        		UniteFunctionsUC::throwError("Wrong query response");
        	
        	$status = UniteFunctionsUC::getVal($arrResponse, "status");
        	
        	if($status == "fail"){
        		$message = UniteFunctionsUC::getVal($arrResponse, "message");
        		UniteFunctionsUC::throwError($message);
        	}
        		        	
        }catch(Exception $e){
        	$message = $e->getMessage();
        	
        	dmp("request error");
        	dmp($message);
        	exit();
        }
        
        return($arrResponse);		
	}
	
	
	/**
	 * request server and get response array
	 */
	private function requestForData($url){
		
		try{
			
			$jsonData = $this->serverRequest($url);
			
		}catch(Exception $e){
			$message = $e->getMessage();
			UniteFunctionsUC::throwError($message);
		}
		
		if(empty($jsonData))
			UniteFunctionsUC::throwError("Wrong API Response");
		
		$arrData = json_decode($jsonData, true);
		if(empty($arrData))
			UniteFunctionsUC::throwError("Wrong Response");
		
		$arrData = UniteFunctionsUC::convertStdClassToArray($arrData);
		
		return($arrData);
	}
	
	
	/**
	 * request for items
	 */
	private function requestForItems($url){
		
		$arrData = $this->requestForData($url);
		
		$objItems = new InstaObjUserUCItemsUC();
		$objItems->initNewAPI($arrData);
		
		return($objItems);
	}
	
	
	/**
	 * request for comments
	 */
	private function requestForComments($url){
		
		$arrData = $this->requestForData($url);
			
		$objComments = new InstaObjCommentsUC();
		$objComments->initByData($arrData);
		
		return($objComments);
	}
	
	
	/**
	 * request for item data
	 */
	private function requestForItemData($url){
		
		$arrData = $this->requestForData($url);
				
		$objItem = new InstaObjItemUC();
		
		$itemData = UniteFunctionsUC::getVal($arrData, "media");
		if(empty($itemData)){
			$itemData = $arrData["graphql"];
			$itemData = $itemData["shortcode_media"];
		}
		
		if(empty($itemData))
			UniteFunctionsUC::throwError("No item data found");
		
		$objItem->initNewAPI($itemData);
		
		return($objItem);
	}
	
	
	/**
	** create cache key from url
	 */
	private function createCacheKey($url){
		
		$info = parse_url($url);
		$path = UniteFunctionsUC::getVal($info, "path");
		$query = UniteFunctionsUC::getVal($info, "query");
		$key = "instagallery_".$path."_".$query;
		$key = HelperInstaUC::convertTitleToHandle($key);
		return($key);
	}
	
	
	/**
	 * create url
	 */
	private function createUrl($query, $lastID=null,$params = ""){
		
		$url = self::$urlBase.$query."/";
		
		if(!empty($lastID))
			$url .= "&max_id=".$lastID;
		
		if(!empty($params))
			$url .= "&".$params;
		
		return($url);
	}
	
	
	
	
	private function ____NEW_REQUEST______(){}
	
	/**
	 * get top search info
	 */
	private function requestTopSearch($user, $cacheSeconds){
		
		$url = self::$urlBase."web/search/topsearch/?context=blended&query=$user&count=1";
		
		$arrResponse = $this->serverRequest($url, $cacheSeconds);
				
		$users = UniteFunctionsUC::getVal($arrResponse, "users");
		
		if(empty($users))
			return(null);
		
		$arrUser = $users[0]["user"];
		
		return($arrUser);
	}
	
	
	
	/**
	 * request for graph items
	 */
	private function requestGraphItems($userID, $arrUser){
		
		$variables = array();
		$variables["id"] = $userID;
		$variables["first"] = 12;		//can be up to 50
		
		$arrItemsData = $this->serverGraphQLRequest($variables);
		
		$objItems = new InstaObjUserUCItemsUC();
		$objItems->initApiGraphQL($arrItemsData, $arrUser);
		
		//$objItems->initNewAPI($arrData);
		
		return($objItems);
	}
	
	/**
	 * test request
	 */
	private function testRequest($userID, $arrUser){
				
		//ig_user({userId}){id,username,external_url,full_name,profile_pic_url,biography,followed_by{count},follows{count},media{count},is_private,is_verified}';
		
		//followed by
		$urlFollowers = "https://www.instagram.com/graphql/query/?query_id=17851374694183129&id={$userID}&first=0&after=0";
		$urlFollowing = "https://www.instagram.com/graphql/query/?query_id=17874545323001329&id={$userID}&first=0&after=0";
		
 		$urlEserFeed = "https://www.instagram.com/graphql/query/?query_id=17861995474116400&fetch_media_item_count=12&userid={$userID}&fetch_media_item_cursor=&fetch_comment_count=4&fetch_like=10";
 		
 		$urlMedia = "https://www.instagram.com/graphql/query/?query_id=17861995474116400&fetch_media_item_count=12&fetch_media_item_cursor=&fetch_comment_count=4&fetch_like=10";
   		
		//$response = HelperInstaUC::getRemoteUrl($urlFollowers);
			
		$urlUser = urlencode("ig_user($userID){id,username,external_url,full_name,profile_pic_url,biography,followed_by{count},follows{count},media{count},is_private,is_verified}");
		$urlUser = "https://www.instagram.com/graphql/".$urlUser;
		
		dmP($urlUser);
		
		$response = HelperInstaUC::getRemoteUrl($urlMedia);
		
		dmp($response);exit();
		
		//dmP($response);exit();
		
		$variables = array();
		$variables["id"] = $userID;
		$variables["first"] = 12;
		$variables["ig_user"] = "{username}";
		
		
		$arrItemsData = $this->serverGraphQLRequest($variables);
		
		dmp($arrItemsData);
		dmp("test request");
		dmp($userID);
		dmp($arrUser);
		exit();
		
		
	}
	
	/**
	 * get images from user
	 */
	public function getUserData_new($user, $lastID = null, $userID = null){
				
		$user = HelperInstaUC::sanitizeUser($user);
		
		HelperInstaUC::validateInstance($user, "user");
			
		$url = $this->createUrl($user, $lastID);
		
		$cacheSeconds = 3600*24;	//1 day
		
		$arrUser = $this->requestTopSearch($user, $cacheSeconds);
		
		$userID = UniteFunctionsUC::getVal($arrUser, "pk");
		
		if(empty($userID))
			return(array());
		
		//$this->testRequest($userID, $arrUser);
			
		$objItems = $this->requestGraphItems($userID, $arrUser);
		
		return($objItems);
	}
	
	
	private function ____END_NEW_REQUEST______(){}
	
	
	/**
	 * get images from user
	 */
	public function getUserData($user, $lastID = null, $userID = null){
		
		$user = HelperInstaUC::sanitizeUser($user);
				
		HelperInstaUC::validateInstance($user, "user");
			
		$url = $this->createUrl($user, $lastID);
				
		$objItems = $this->requestForItems($url);
				
		return($objItems);
	}

	
	
	
	/**
	 * convert items to simple array
	 */
	private function convertItemsToSimpleArray($objItems, $maxItems = null){
		
		if($maxItems !== null){
			$maxItems = (int)$maxItems;
			if($maxItems < 1)
				$maxItems = null;
		}
		
		$arrItems = $objItems->getItems();
		
		$arrItemsData = array();
		
		foreach($arrItems as $index=>$item){
			
			if($maxItems && $index >= $maxItems)
				break;	
			
			$data = $item->getDataSimple();
			$arrItemsData[] = $data;
		}
		
		
		return($arrItemsData);
	}
	
	
	/**
	 * get items data uf it's user or tag
	 */
	public function getItemsData($mixed, $lastID=null, $userID = null, $maxItems = null){
		
		$type = "";
		if(strpos($mixed,"@") === 0)
			$type = "user";
		else
			if(strpos($mixed,"#") === 0)
				$type = "tag";
		
		if(empty($type)){
			$type = "user";
			$mixed .= "@".$mixed;
		}
		
		try{
		
			if(empty($type))
				UniteFunctionsUC::throwError("Wrong type, should be user or tag");
			
			switch($type){
				case "user":
					//$objItems = $this->getUserData($mixed, $lastID, $userID);
					$objItems = $this->getUserData_new($mixed, $lastID, $userID);
					
				break;
				case "tag":
					$objItems = $this->getTagData($mixed, $lastID, $userID);
				break;
			}
		
		
			$arrItems = $this->convertItemsToSimpleArray($objItems, $maxItems);
		
		}catch(Exception $e){
						
			throw $e;
		}
		
		$pageData = $objItems->getArrPageData();
				
		$response = array();
		$response["main"] = $pageData;
		$response["items"] = $arrItems;
		
		
		return($response);
	}
	
	
	
	
	/**
	 * get tag data
	 */
	public function getTagData($tag, $lastID=null, $userID = null){
		
		$tag = HelperInstaUC::sanitizeTag($tag);
		HelperInstaUC::validateInstance($tag,"tag");
		
		$query = "explore/tags/$tag";
		$url = $this->createUrl($query, $lastID);
		
		$objItems = $this->requestForItems($url);
		$objItems->setIsTag();
		
		return($objItems);
	}
	
	
	/**
	 * get arr comments of some item
	 */
	public function getArrComments($itemID){
		
		$query = "p/$itemID";
		$url = $this->createUrl($query);
		$objComments = $this->requestForComments($url);
		
		return($objComments);
	}
	
	
	/**
	 * get video item data
	 */
	public function getItemData($itemID){
		
		$query = "p/$itemID";
		$url = $this->createUrl($query,"","");
		
		$objItem = $this->requestForItemData($url);
		
		return($objItem);
	}
	
}