<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class UniteCreatorTemplateEngine extends UniteCreatorTemplateEngineWork{
		
	
	/**
	 * put post date
	 */
	public function putPostDate($postID, $dateFormat = ""){
		
		$date = get_the_date($dateFormat, $postID);
		echo $date;
	}
	
	
	/**
	 * put font override
	 */
	public function putPostMeta($postID, $key){
		
		$metaValue = get_post_meta($postID, $key, true);
		
		echo $metaValue;
	}
	
	/**
	 * put font override
	 */
	public function printPostMeta($postID){
		
		$postMeta = UniteFunctionsWPUC::getPostMeta($postID);
		
		if(empty($postMeta))
			dmp("no meta for this post");
		else{
			echo "<pre>";
			$content = print_r($postMeta, true);
			$content = str_replace("[", "[ ", $content);
			$content = str_replace("]", " ]", $content);
			
			echo $content;
			echo "</pre>";
		}
		
	}
	
	
	/**
	 * get term custom fields with cache
	 */
	public function getTermCustomFields($termID){
		
		$arrCustomFields = UniteFunctionsWPUC::getTermCustomFields($termID);
		
		return($arrCustomFields);
	}
	
	
	/**
	 * get term meta
	 */
	public function getTermMeta($termID, $key = ""){
		
		if(is_numeric($termID) == false)
			return(null);
		
		if(!empty($key)){
			$value = get_term_meta($termID, $key, true);
			return($value);
		}
			
		$arrMeta = UniteFunctionsWPUC::getTermMeta($termID);
		
		return($arrMeta);
	}
	
	
	/**
	 * get post terms
	 */
	public function getPostTerms($postID, $taxonomy, $addCustomFields = false, $type = "", $maxTerms = null){
		
		$post = get_post($postID);
		
		if(empty($post)){
			return(array());
		}
		
		$arrTerms = UniteFunctionsWPUC::getPostSingleTerms($postID, $taxonomy);
		
		if($type == "last_level")
			$arrTerms = UniteFunctionsWPUC::filterTermsLastLevel($arrTerms, $taxonomy);
		
		//get single category
		if(empty($arrTerms))
			return(array());
		
		if(!empty($maxTerms) && count($arrTerms) > $maxTerms){
			
			$arrTerms = array_slice($arrTerms, 0, $maxTerms, true);
		}
			
		$arrTermsOutput = $this->objParamsProcessor->modifyArrTermsForOutput($arrTerms, $addCustomFields);
		
		return($arrTermsOutput);
	}
	
	
	/**
	 * get term meta
	 */
	public function getUserMeta($userID, $key = null){
		
		$user = get_user_by("id", $userID);
				
		if(empty($user))
			UniteFunctionsUC::throwError("user with id: $userID not found");

		if(empty($key)){
			
			dmp("getUserMeta Error. no key given. please select meta key from this keys: ");
			$arrMeta = get_user_meta($userID);
			
			$arrKeys = array_keys($arrMeta);
			
			dmp($arrKeys);
			
			return(null);
		}

		$value = get_user_meta($userID, $key, true);
		
		return($value);
	}
	
	
	/**
	 * get post meta
	 */
	public function getPostMeta($postID, $key){
		
		$metaValue = get_post_meta($postID, $key, true);
		
		return($metaValue);
	}
		
	
	/**
	 * put font override
	 */
	public function putAcfField($postID, $fieldname){
		
		if(class_exists('acf') == false)
			return(true);
		
		$value = get_field($fieldname, $postID);
		
		if(is_string($value) == false)
			return(true);
		
		echo $value;
	}
	
	
	
	/**
	 * put font override
	 */
	public function putPostTags($postID){
		
		$htmlTags = UniteFunctionsWPUC::getTagsHtmlList($postID);
		
		echo UniteProviderFunctionsUC::escCombinedHtml($htmlTags);
	}
	
	
	/**
	 * add extra functions to twig
	 */
	/*
	protected function initTwig_addExtraFunctions(){
		
		parent::initTwig_addExtraFunctions();
				
	}
	*/
	
}