<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class InstaObjItemUC{
	
	const TYPE_VIDEO = "video";
	const TYPE_IMAGE = "image";
	const TYPE_ALBOM = "albom";
	
	const IMAGE_LOW = "low_resolution";
	const IMAGE_STANDARD = "standard_resolution";
	const IMAGE_THUMB = "thumbnail";
	
	const VIDEO_STANDART = "standard_resolution";
	const VIDEO_LOWRES = "low_resolution";
	const VIDEO_LOWBENDWIDTH = "low_bandwidth";
	
	
	public $isInited = false;
	private $item;
	public $code;
	public $locationName;
	public $arrImages;
	public $arrVideos;
	public $urlVideo;
	public $videoViews;
	public $canViewComments = false;
	public $numComments;
	public $arrComments = array();
	public $objComments;
	public $urlAltMedia;
	
	public $hasCaption = false;
	public $captionText = "";
	public $captionTextProcessed = null;
	public $objCaption;
	
	public $link;
	public $numLikes;
	public $arrLikesUsers = array();
	public $createdDateStamp;	
	public $createdDateText;	
	public $type;
	public $id;
	public $itemUser;
	public $itemUserID;
	
	
	/**
	 * validate if the item is inited
	 */
	private function validateInited(){
		
		if($this->isInited == false)
			UniteFunctionsUC::throwError("The item is not inited");
	
	}
	
	/**
	 * get image standart resolution
	 */
	public function getImageStandart(){
		
		$url = $this->arrImages[self::IMAGE_STANDARD]["url"];
		return($url);
	}
	
	/**
	 * get video standart
	 */
	public function getVideoStandart(){
		
		if(!isset($this->arrVideos[self::VIDEO_STANDART]))
			return("");
		
		$url = $this->arrVideos[self::VIDEO_STANDART]["url"];
		
		return($url);
	}
	
	/**
	 * get link
	 */
	public function getLink(){
		
		return($this->link);
	}

	
	/**
	 * process caption text
	 */
	private function processText($text){
		
		$text = preg_replace('/#(\w+)/', '<a href="https://instagram.com/explore/tags/$1" target="_blank">#$1</a>', $text);
		$text = preg_replace('/@(\w+)/', '<a href="https://instagram.com/$1" target="_blank">@$1</a>', $text);
		
		return($text);
	}
	
	
	/**
	 * get caption text
	 */
	public function getCaptionText(){
		
		if(empty($this->captionText))
			return($this->captionText);
		
		if(empty($this->captionTextProcessed))
			$this->captionTextProcessed = $this->processText($this->captionText);
		
		return($this->captionTextProcessed);
	}
	
	/**
	 * get num likes
	 */
	public function getNumLikes(){
		
		return($this->numLikes);
	}
	
	/**
	 * num comments
	 */
	public function getNumComments(){
		
		return($this->numComments);
	}
	
	/**
	 * get num comments textual
	 */
	public function getNumCommentsText(){
				
		$numComments = HelperInstaUC::convertNumberToText($this->numComments);
		return($numComments);
	}
	
	
	/**
	 * get num likes textual
	 */
	public function getNumLikesText(){
		
		$numLikes = HelperInstaUC::convertNumberToText($this->numLikes);
		return($numLikes);
		
	}
	
	
	/**
	 * get numer of video views text
	 */
	public function getNumVideoViewsText(){
		
		$numViews = HelperInstaUC::convertNumberToText($this->videoViews);
		
		return($numViews);
	}
	
	
	/**
	 * get comments array
	 */
	public function getArrComments(){
		
		if(!empty($this->arrComments))		
			return($this->arrComments);
		
		if(empty($this->objComments))
			return(array());
		
		$arrComments = $this->objComments->getArrComments();
		
		return($arrComments);
	}
	
	
	/**
	 * get likes array
	 */
	public function getArrLikes(){
		
		return($this->arrLikesUsers);
	}
	
	/**
	 * get url image low
	 */
	public function getImageLow(){
		$url = $this->arrImages[self::IMAGE_LOW]["url"];
		
		return($url);
	}
	
	
	/**
	 * get caption text
	 */
	public function getCaption(){
		
		if($this->hasCaption == false)
			return("");
		
		$text = $this->captionText;
		
		return($text);
	}
	
	
	/**
	 * get location
	 */
	public function getLocation(){
		$location = $this->locationName;
		
		return($location);
	}
	
	
	/**
	 * return if the item is video
	 */
	public function isVideo(){
		
		if($this->type == "video")
			return(true);
		else
			return(false);
		
	}
	
	
	/**
	 * get the id
	 */
	public function getID(){
		
		return($this->id);
	}
	
	/**
	 * get the code
	 */
	public function getCode(){
		
		return($this->code);
	}
	
	
	/**
	 * get time passed till now
	 */
	public function getTimePassedText(){
		
		$timeSinse = HelperInstaUC::getTimeSince($this->createdDateStamp);
		
		return($timeSinse);
	}
	
	
	/**
	 * get simple data
	 */
	public function getDataSimple(){
		
		$isVideo = $this->isVideo();
		
		$class = "";
		if($isVideo == true)
			$class = "uc-video-item";
		
		$arr = array();
		$arr["thumb"] = $this->getImageLow();
		$arr["image"] = $this->getImageStandart();
		$arr["caption"] = $this->getCaption();
		$arr["num_likes"] = $this->getNumLikesText();
		$arr["num_comments"] = $this->getNumCommentsText();
		$arr["link"] = $this->getLink();
		$arr["isvideo"] = $isVideo;
		$arr["video_class"] = $class;
		$arr["num_video_views"] = $this->getNumVideoViewsText();
		$arr["url_video"] = $this->urlVideo;
		
		return($arr);
	}
	
	
	/**
	 * get item value
	 */
	private function getVal($field){
		
		$value = UniteFunctionsUC::getVal($this->item, $field);
		
		unset($this->item[$field]);
		
		return($value);
	}
	
	
	/**
	 * parse comments
	 */
	private function parseComments(){
		
		$comments = $this->getVal("comments");
		
		$this->numComments = UniteFunctionsUC::getVal($comments, "count");
		
		$commentsData = UniteFunctionsUC::getVal($comments, "data");
		
		if(empty($commentsData))
			return(false);
		
		if(is_array($commentsData) == false)
			return(false);
		
		//get all comments
		foreach($commentsData as $comment){
			
			$objComment = new InstaObjCommentUC();
			$objComment->init($comment);
			
			$this->arrComments[] = $objComment;
		}
		
	}
	
	
	/**
	 * parse likes
	 */
	private function parseLikes(){
		
		$likes = $this->getVal("likes");

		//get num likes
		
		$numLikes = UniteFunctionsUC::getVal($likes, "count");
		if(empty($numLikes))
			$numLikes = 0;
		
		$this->numLikes = $numLikes;
		
		//get likes users
		
		$likesData = UniteFunctionsUC::getVal($likes, "data");
		
		if(empty($likesData))
			return(false);
		
		if(is_array($likesData) == false)
			return(false);
		
		foreach($likesData as $likeUser){
			
			$user = new InstaObjUserUC();
			$user->init($likeUser);
			
			$this->arrLikesUsers[] = $user;
			
		}
		
	}
	
	
	/**
	 * parse user
	 */
	private function parseUser(){
		
		$user = $this->getVal("user");
		
		if(empty($user))
			return(false);
		
		$this->itemUser = new InstaObjUserUC();
		$this->itemUser->init($user);
		
	}
	
	
	/**
	 * parse video type
	 */
	private function parseVideoRelated(){
		
		$this->videoViews = $this->getVal("video_views");
		$this->arrVideos = $this->getVal("videos");
	}
	
	
	/**
	 * parse the caption
	 */
	private function parseCaption(){
		
		$caption = $this->getVal("caption");
		
		if(empty($caption))
			return(false);
		
		$this->hasCaption = true;
		
		$this->objCaption = new InstaObjCommentUC();
		$this->objCaption->init($caption);
		
		$this->captionText = $this->objCaption->text;
		
	}
	
	
	/**
	 * init by api response
	 */
	public function init($item){
				
		//unset some vars
		unset($item["can_delete_comments"]);
		
		$this->item = $item;
		
		//code
		$this->code = $this->getVal("code");
		
		//location
		$arrLocation = $this->getVal("location");
		$this->locationName = UniteFunctionsUC::getVal($arrLocation, "name");
		
		//get images
		$this->arrImages = $this->getVal("images");
		
		//get comments
		$canViewComments = $this->getVal("can_view_comments");
		$this->canViewComments = UniteFunctionsUC::strToBool($canViewComments);
		if($this->canViewComments == true)
			$this->parseComments();
		
		//get alt media
		$this->urlAltMedia = $this->getVal("alt_media_url");
		
		//get caption
		$this->parseCaption();
		
		//link
		$this->link = $this->getVal("link");
				
		//likes
		$this->parseLikes();
		
		//created date
		$this->createdDateStamp = $this->getVal("created_time");
		$this->createdDateText = HelperInstaUC::stampToDate($this->createdDateStamp);
		
		//get type
		$this->type = $this->getVal("type");
		
		switch($this->type){
			case "image":
			break;
			case "video":
				$this->parseVideoRelated();
			break;
			default:
				throw new Error("Wrong item type: $this->type");
			break;
		}
		
		//id
		$this->id = $this->getVal("id");
		
		//user
		$this->parseUser();
		
		if(!empty($this->item))
			UniteFunctionsUC::throwError("There something else need to be parsed");

		
		$this->isInited = true;
	}
	
	/**
	 * init is video
	 */
	private function initNew_isVideo($item){
		
		$isVideo = UniteFunctionsUC::getVal($item, "is_video");
		$isVideo = UniteFunctionsUC::strToBool($isVideo);
		
		$mediaType = UniteFunctionsUC::getVal($item, "media_type");
		
		if($isVideo == true){
			$this->type = self::TYPE_VIDEO;
		}
		else{
			$this->type = self::TYPE_IMAGE;
		}
				
		if($this->type != self::TYPE_VIDEO)
			return(false);
		
		if(isset($item["video_url"])){
			$this->arrVideos[self::VIDEO_STANDART] = array();
			$this->arrVideos[self::VIDEO_STANDART]["url"] = UniteFunctionsUC::getVal($item, "video_url");
		}
		
		$this->videoViews = UniteFunctionsUC::getVal($item, "video_view_count");
		
	}
	
	/**
	 * get link from code
	 */
	private function getLinkFromCode($code){
		
		$link = "https://www.instagram.com/p/{$code}";
		
		return($link);
	}
	
	
	/**
	 * init by new API
	 */
	public function initNewAPI($item){
		
		
		if(isset($item["node"]))
			$item = $item["node"];
		
		$this->initNew_isVideo($item);
		
		$this->id = UniteFunctionsUC::getVal($item, "id");
		$this->code = UniteFunctionsUC::getVal($item, "code");
		if(empty($this->code))
			$this->code = $this->code = UniteFunctionsUC::getVal($item, "shortcode");
		
		$commentsDisabled = UniteFunctionsUC::getVal($item, "comments_disabled");
		$commentsDisabled = UniteFunctionsUC::strToBool($commentsDisabled);
		
		$this->canViewComments = !$commentsDisabled;
				
		$this->createdDateStamp = UniteFunctionsUC::getVal($item, "taken_at_timestamp");
				
		if(!empty($this->createdDateStamp))
			$this->createdDateText = HelperInstaUC::stampToDate($this->createdDateStamp);
		
		$this->captionText = UniteFunctionsUC::getVal($item, "caption");
		$this->captionText = trim($this->captionText);
		
		if(!empty($this->captionText))
			$this->hasCaption = true;
		
		$this->link = $this->getLinkFromCode($this->code);

		//init images
		
		$this->arrImages = array();
		$this->arrImages[self::IMAGE_LOW] = array();
		$this->arrImages[self::IMAGE_STANDARD] = array();
		
		$urlImageNormal = UniteFunctionsUC::getVal($item, "display_url");
		
		$this->arrImages[self::IMAGE_LOW]["url"] = UniteFunctionsUC::getVal($item, "thumbnail_src");
		$this->arrImages[self::IMAGE_STANDARD]["url"] = $urlImageNormal;
		
		$arrLikes = UniteFunctionsUC::getVal($item, "edge_liked_by");
		if(empty($arrLikes))
			$arrLikes = UniteFunctionsUC::getVal($item, "edge_media_preview_like");
		
		$this->numLikes = UniteFunctionsUC::getVal($arrLikes, "count"); 
		 		
		//init owner
		$ownerID = UniteFunctionsUC::getVal($item, "owner");
		if(!empty($ownerID))
			$this->itemUserID = $ownerID;
				
		//get comments
		$arrComments = UniteFunctionsUC::getVal($item, "comments");
		if(empty($arrComments)){
			$arrComments = UniteFunctionsUC::getVal($item, "edge_media_to_comment");
		}			
			
		$this->numComments = UniteFunctionsUC::getVal($arrComments, "count");
		
		$commentsNodes = UniteFunctionsUC::getVal($arrComments, "nodes");
		if(empty($commentsNodes))
			$commentsNodes = UniteFunctionsUC::getVal($arrComments, "edges");
		
		if(!empty($commentsNodes)){
			
			$this->objComments = new InstaObjCommentsUC();
			$this->objComments->initByData($item);
		}
		
		$this->isInited = true;
	}
	
	
	/**
	 * init item by official API
	 */
	public function initOfficialAPI($item){
		
		$mediaType = UniteFunctionsUC::getVal($item, "media_type");
				
		switch($mediaType){
			default:
			case "IMAGE":
				$this->type = self::TYPE_IMAGE;
			break;
			case "CAROUSEL_ALBUM":
				$this->type = self::TYPE_ALBOM;
			break;
			case "VIDEO":
				$this->type = self::TYPE_VIDEO;
			break;
		}
		
		$urlImage = UniteFunctionsUC::getVal($item, "media_url");
		
		if($this->type == self::TYPE_VIDEO){
			
			$url = $this->arrVideos[self::VIDEO_STANDART]["url"] = $urlImage;
			
			$urlImage = UniteFunctionsUC::getVal($item, "thumbnail_url");
			
			$this->urlVideo = UniteFunctionsUC::getVal($item, "media_url");
		}
		
		$this->arrImages[self::IMAGE_LOW]["url"] = $urlImage;
		$this->arrImages[self::IMAGE_STANDARD]["url"] = $urlImage;
		
		$this->hasCaption = true;
		$this->captionText = UniteFunctionsUC::getVal($item, "caption");
		
		$this->id = UniteFunctionsUC::getVal($item, "id");

		$this->link = UniteFunctionsUC::getVal($item, "permalink");
		
		$this->isInited = true;
		
	}
	
	
	/**
	 * print item data
	 */
	public function printData(){
		
		$this->validateInited();
		
		$str = "ID: {$this->id} <br> caption: $this->captionText ";
		
		dmp($str);
		
		dmp("---------------------");
	}
	
	
}