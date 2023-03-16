<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class InstagramAPIOfficialUC{
	
	const URL_REFRESH = "https://graph.instagram.com/refresh_access_token";
	const URL_AUTHORIZE = "https://api.instagram.com/oauth/authorize";
	const APP_CLIENT_ID = "301063367606985";
	const URL_APP_CONNECT = "https://unlimited-elements.com/instagram-connect/connect.php";
	
	private $accessToken;
	private $userID;
	private $limit = 30;
	private $maxItems = 30;
	
	const CACHE_RESPONSE = true;
	private $lastAPIError = "";
	const DEBUG_SERVER_REQUEST = false;
	
	
	/**
	 * check if raw response is error one.
	 */
	private function isErrorRawResponse($response){
		
		if(empty($response))
			return(true);
			
		$arrResponse = UniteFunctionsUC::jsonDecode($response);

		if(empty($arrResponse))
			return(true);

		if(is_array($arrResponse) == false)
			return(false);
		
		if(isset($arrResponse["error"]))
			return(true);
				
		return(false);
	}
	
	/**
	 * write debug line
	 */
	private function debug($text){
		
		if(self::DEBUG_SERVER_REQUEST == false)
			return(false);
		
		dmp($text);
	}
	
	
	/**
	 * call api
	 */
	private function serverRequest($url, $cacheSeconds = null){
		
		$response = null;
				
		if(self::DEBUG_SERVER_REQUEST == true){
			dmp("---------------");
			dmp("server request for url: $url");
			dmp("caching responses: ".self::CACHE_RESPONSE);
		}
		
		//get response from cache
		
		$isNoCache = UniteFunctionsUC::getGetVar("ucnocache","",UniteFunctionsUC::SANITIZE_TEXT_FIELD);
		$isNoCache = UniteFunctionsUC::strToBool($isNoCache);
		
		if(UniteFunctionsWPUC::isCurrentUserHasPermissions() == false)
			$isNoCache = false;
		
		if($isNoCache === true){
			dmp("loading instagram without cache...");
		}
		
		if(self::CACHE_RESPONSE == true && $isNoCache !== true){
						
			$cacheKey = $this->createCacheKey($url);
			
			$response = HelperInstaUC::getFromCache($cacheKey);
			
			//don't get reponses that are error
			$isError = $this->isErrorRawResponse($response);
			if($isError == true){
				$response = null;
				$this->debug("skip caching error response");
			}
			
			//debug
			$this->debug("get from cache: $cacheKey");
			
			if(empty($response))
				$this->debug("empty cached response");				
			
		}
				
		//get respose from instagram
		if(empty($response) || self::CACHE_RESPONSE == false){
			
			$this->debug("get remote url");
			
			$response = UniteFunctionsUC::getUrlContents($url, null, "get");
			
			//write debug line
			
			if(self::DEBUG_SERVER_REQUEST == true){
				$strResponse = json_encode($response);
				$len = strlen($strResponse);
				
				$this->debug("response ok length: $len");
			}
			
			//cache the response
			
			if(self::CACHE_RESPONSE == true && !empty($response)){
				
				$this->debug("cache the response: $cacheKey");
								
				$isError = $this->isErrorRawResponse($response);
				
				if($isError == false)
					HelperInstaUC::cacheResponse($cacheKey, $response, $cacheSeconds);
				else
					$this->debug("don't cache error response");
			}
			
			
		}
		
		
		return($response);
	}
	
	
	
	/**
	** create cache key from url
	 */
	private function createCacheKey($url){
		
		$info = parse_url($url);
		$path = UniteFunctionsUC::getVal($info, "path");
		$query = UniteFunctionsUC::getVal($info, "query");
		
		parse_str($query, $params);
				
		if(!isset($params["access_token"]))
			UniteFunctionsUC::throwError("access token not found");
		
		$arrParamsForUrl = array();
		
		if(isset($params["fields"]))
			$arrParamsForUrl["fields"] = $params["fields"];
				
		if(isset($params["limit"]))
			$arrParamsForUrl["limit"] = $params["limit"];
		
		if(isset($params["after"]))
			$arrParamsForUrl["after"] = md5($params["after"]);
		
		$query = http_build_query($arrParamsForUrl);
		
		$key = "inst_".$path."_".$query;
		$key = HelperInstaUC::convertTitleToHandle($key);
		
		return($key);
	}
	
	
	private function ____NEW_REQUEST______(){}
	
	/**
	 * request the server and cache the response.
	 * response the data as array
	 */
	private function requestForData($urlRequest){
		
		try{
			
			$jsonData = $this->serverRequest($urlRequest);
			
		}catch(Exception $e){
			$message = $e->getMessage();
			UniteFunctionsUC::throwError($message);
		}
		
		if(empty($jsonData))
			UniteFunctionsUC::throwError("Wrong API Response");
		
		$arrData = json_decode($jsonData, true);
		if(empty($arrData))
			UniteFunctionsUC::throwError("Wrong API Response");
		
		$arrData = UniteFunctionsUC::convertStdClassToArray($arrData);
		
		//check for errors:
		$error = UniteFunctionsUC::getVal($arrData, "error");
		if(empty($error))
			return($arrData);
					
		if(!empty($error)){
			$message = UniteFunctionsUC::getVal($error, "message");
			if(empty($message))
				$message = "instagram api error";
			
			if(GlobalsUC::$is_admin == false)
				$message = "instagram api error";
			
			UniteFunctionsUC::throwError($message);
		}
		
		return($arrData);
	}
	
	/**
	 * get requst url for graph api
	 */
	private function getUrlRequest($type, $fields){
		
		$userID = $this->userID;
		$accessToken = $this->accessToken;
		
		$baseURL = "https://graph.instagram.com/";
		
		switch($type){
			case "user":
				$urlRequest = "{$baseURL}{$userID}?fields={$fields}";
			break;
			case "media":
				$limit = $this->limit;
				$urlRequest = "{$baseURL}{$userID}/media?limit={$limit}&fields={$fields}";
			break;
			default:
				UniteFunctionsUC::throwError("Wrong request type: $type");
			break;
		}
		
		$since = "";
		
		$urlRequest .= "&access_token={$accessToken}&since=";
		
		return($urlRequest);
	}
	
	
	/**
	 * request new graph
	 */
	private function requestGraphNew($type, $fields){
		
		$urlRequest = $this->getUrlRequest($type, $fields);
		
		$arrData = $this->requestForData($urlRequest);
		
		return($arrData);
	}
	
	
	/**
	 * request for user
	 * Enter description here ...
	 */
	private function requestUser(){
		
		$fields = array(
			"id",
			"media_count",
			"username",
			"account_type",
		);
		
		$strFields = implode(",", $fields);
		
		$response = $this->requestGraphNew("user", $strFields);
		
		return($response);
	}
	
	/**
	 * request for media
	 */
	private function requestMedia(){
		
		$fields = "media_url,thumbnail_url,caption,id,media_type,timestamp,username,comments_count,like_count,permalink,children{media_url,id,media_type,timestamp,permalink,thumbnail_url}";
		//$fields = "media_url,thumbnail_url,caption,media_type";
		
		$data = array();
		$count = 2;
		
		$urlNext = $this->getUrlRequest("media", $fields);
		
		$arrDataCombined = array();
		
		$maxRequest = 3;
		
		do{
			
			$response = $this->requestForData($urlNext);
			
			$data = UniteFunctionsUC::getVal($response, "data");
			if(empty($data))
				$data = array();
			
			if(!empty($data))
				$arrDataCombined = array_merge($arrDataCombined, $data);
				
			$numItems = count($arrDataCombined);
			
			if($numItems >= $this->maxItems)
				return($arrDataCombined);
			
			$paging = UniteFunctionsUC::getVal($response, "paging");
			$urlNext = UniteFunctionsUC::getVal($paging, "next");
			
			$maxRequest--;
			if($maxRequest <= 0)
				$urlNext = null;
			
			if($numItems >= $this->maxItems)	//for insurance
				$urlNext = null;
			
		}while(!empty($urlNext));
		
		
		return($arrDataCombined);
	}
	
	
	/**
	 * init the access data
	 */
	private function initAccessData(){
		
		$arrData = HelperInstaUC::getInstagramSavedAccessData();
		
		$this->accessToken = UniteFunctionsUC::getVal($arrData, "access_token");
		$this->userID = UniteFunctionsUC::getVal($arrData, "user_id");
		
		if(empty($this->accessToken) || empty($this->userID))
			UniteFunctionsUC::throwError("Wrong access data");
				
	}
	
	
	/**
	 * get images from user
	 */
	private function getUserData_new($user, $lastID = null, $userID = null){
		
		$user = HelperInstaUC::sanitizeUser($user);
		
		HelperInstaUC::validateInstance($user, "user");
		
		$this->initAccessData();
		
		$arrUserData = $this->requestUser();
		
		$arrItemsData = $this->requestMedia();
				
		$objItems = new InstaObjUserUCItemsUC();
		$objItems->initOfficialAPI($arrItemsData, $arrUserData);
		
		return($objItems);
	}
	
	
	/**
	 * renew the token
	 */
	public function renewToken($currentToken){
		
		$url = self::URL_REFRESH;
		$url .= "?grant_type=ig_refresh_token";
		$url .= "&access_token=".$currentToken;
		
		$response = UniteFunctionsUC::getUrlContents($url, null, "get");
		
		$arrResponse = UniteFunctionsUC::jsonDecode($response);
		
		if(empty($arrResponse))
			UniteFunctionsUC::throwError("Refresh token failed!");
		
		$newAccessToken = UniteFunctionsUC::getVal($arrResponse, "access_token");
		
		if(empty($newAccessToken))
			UniteFunctionsUC::throwError("Refresh token failed!!!");
		
		return($arrResponse);
	}
	
	private function ____END_NEW_REQUEST______(){}
	
	
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
		
		//renew here
		HelperInstaUC::checkRenewAccessToken_onceInAWhile();
		
		
		$pageData = $objItems->getArrPageData();
				
		$response = array();
		$response["main"] = $pageData;
		$response["items"] = $arrItems;
		
		
		return($response);
	}
			
	
}