<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class InstaObjCommentUC{
	
	public $commentID;
	
	public $createdDateStamp;
	public $createdDate;
	
	public $text;
	public $fromUser;
	public $username;
	
	
	/**
	 * print all globals variables
	 */
	public function printVars(){
		
		$vars = get_object_vars($this);
		
		dmp($vars);
		exit();
	}
	
	
	/**
	 * get text
	 */
	public function getText(){
		
		$this->text = UniteProviderFunctionsIG::convertEmoji($this->text);
		
		return($this->text);
	}
	
	
	/**
	 * get username
	 */
	public function getUsername(){
		
		return($this->username);
	}
	
	/**
	 * init comment by array
	 */
	public function init($comment){
		
		//get date
		$this->createdDateStamp = UniteFunctionsUC::getVal($comment, "created_time");
		
		$this->createdDate = HelperInstaUC::stampToDate($this->createdDateStamp);
		
		//get text
		$this->text = UniteFunctionsUC::getVal($comment, "text");
		
		//get from user
		$fromUser = UniteFunctionsUC::getVal($comment, "from");
		
		$this->fromUser = new InstaObjUserUC();
		$this->fromUser->init($fromUser);
		
		
		//get id
		$this->commentID = UniteFunctionsUC::getVal($comment, "id");
		
	}
	
	/**
	 * init by data
	 */
	public function initByData($text, $username){
		$this->username = $username;
		$this->text = $text;
	}
	
	/**
	 * init by new API
	 */
	public function initNewAPI($data){
		
		if(isset($data["node"]))
			$data = $data["node"];
		
		$this->commentID = UniteFunctionsUC::getVal($data, "id");
		
		$dataUser = UniteFunctionsUC::getVal($data, "owner");
		if(empty($dataUser))
			$dataUser = UniteFunctionsUC::getVal($data, "user");
		
		$this->fromUser = new InstaObjUserUC();
		$this->fromUser->initByComment($dataUser);
		
		$this->username = $dataUser["username"];
		
		$this->text = UniteFunctionsUC::getVal($data, "text");
		
		$this->createdDateStamp = $data["created_at"];
		
	}
	
	
}