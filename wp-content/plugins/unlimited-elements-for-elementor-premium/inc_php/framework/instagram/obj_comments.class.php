<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

/**
 * instagram comments list class
 */
class InstaObjCommentsUC{

	private $arrComments = array();
	
	
	/**
	 * get comments array
	 */
	public function getArrComments(){
		
		return($this->arrComments);
	}
	
	
	/**
	 * get comments array from data
	 */
	private function getArrCommentsFromData($data){
		
		$arrData = @$data["graphql"]["shortcode_media"]["edge_media_to_comment"]["edges"];
		
		if(empty($arrData))
			$arrData = @$data["edge_media_to_comment"]["edges"];
		
		if(empty($arrData))
			$arrData = @$data["media"]["comments"]["nodes"];
			
		return($arrData);
	}
	
	
	/**
	 * get caption from data
	 */
	private function getCaptionFromData($data){
		
		$caption = @$data["graphql"]["shortcode_media"]["edge_media_to_caption"]["edges"][0]["node"]["text"];
		
		if(empty($caption))
			$caption = @$data["edge_media_to_caption"]["edges"][0]["node"]["text"];
		
		return($caption);
	}
	
	
	/**
	* get username from data
	 */
	private function getUsernameFromData($data){
		
		$username = @$data["owner"]["username"];
		
		if(empty($username))
			$username = @$data["graphql"]["shortcode_media"]["owner"]["username"];
		
		if(!empty($username))
			$username = "@".$username;
		
		return($username);
	}
	
	
	/**
	 * init comments by data from instagram server
	 */
	public function initByData($data){

		$arrDataComments = $this->getArrCommentsFromData($data);
		
		//create first comment
		$caption = $this->getCaptionFromData($data);
		$username = $this->getUsernameFromData($data);
		
		if(!empty($caption)){
			$objComment = new InstaObjCommentUC();
			$objComment->initByData($caption, $username);
			$this->arrComments[] = $objComment;
		}
		
		if(empty($arrDataComments) && empty($caption))
			return(false);
		
		foreach($arrDataComments as $comment){
			
			$objComment = new InstaObjCommentUC();
			$objComment->initNewAPI($comment);
			
			$this->arrComments[] = $objComment;
		}
		
		
		return($this->arrComments);
	}
	
	
	
}