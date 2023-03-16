<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class ProviderOperationsUC extends UCOperations{

	
	/**
	 * get search text from data
	 */
	private function getSearchFromData($data){
		
		$type = UniteFunctionsUC::getVal($data, "_type");
		
		if($type != "query")
			return(null);
		
		$searchTerm = UniteFunctionsUC::getVal($data, "q");
		
		return($searchTerm);
	}
	
	/**
	 * get select 2 terms titles from array of id's or slugs
	 */
	public function getSelect2TermsTitles($data){
		
		$arrIDs = UniteFunctionsUC::getVal($data, "post_ids");
		
		if(empty($arrIDs))
			return(null);
		
		if(is_string($arrIDs)){
			$arrIDs = explode(",", $arrIDs);
		}
		
		$firstID = $arrIDs[0];
		
		//search by slugs
		if(is_numeric($firstID) == false){
			$args = array("slug"=>$arrIDs);
		}else{
			$args = array("include"=>$arrIDs);
		}
		
		$response = get_terms($args);
		
		if(empty($response))
			return(null);
		
		$output = array();
		foreach($response as $term){
			
			$termID = $term->term_id;
			$title = $term->name;
			$taxonomy = $term->taxonomy;
			
			$title .= " ($taxonomy)";
			
			$item = array();
			$item["id"] = $termID;
			$item["text"] = $title;
			
			$output[] = $item;
		}
				
		return($output);
	}
	
	
	/**
	 * get select 2 post titles from array of id's
	 */
	public function getSelect2PostTitles($data){
		
		$arrIDs = UniteFunctionsUC::getVal($data, "post_ids");
		
		$arrTypesAssoc = UniteFunctionsWPUC::getPostTypesAssoc(array(), true);
		
		if(empty($arrIDs))
			return(null);
		
		$response = UniteFunctionsWPUC::getPostTitlesByIDs($arrIDs);
		
		if(empty($response))
			return(null);
		
		$output = array();
		
		foreach($response as $record){
			
			$id = UniteFunctionsUC::getVal($record, "id");
			$title = UniteFunctionsUC::getVal($record, "title");
			$postType = UniteFunctionsUC::getVal($record, "type");
			
			$typeTitle = UniteFunctionsUC::getVal($arrTypesAssoc, $postType);
			
			if(empty($typeTitle))
				$typeTitle = $postType;
			
			$title .= " ($typeTitle)";
			
			$item = array();
			$item["id"] = $id;
			$item["text"] = $title;
			
			$output[] = $item;
		}
		
		return($output);
	}
	
	
	/**
	 * get terms list for select
	 */
	public function getTermsListForSelectFromData($data){
		
		$limit = 10;
		
		$search = $this->getSearchFromData($data);
		$taxonomy = UniteFunctionsUC::getVal($data, "taxonomy");

		$query = array();
		$query["number"] = $limit;
		$query["search"] = $search;
		$query["hide_empty"] = false;
		
		$arrTaxNames = array();
		
		$isSingleTax = true;
		
		if(!empty($taxonomy)){
			$query["taxonomy"] = $taxonomy;
		}
		
		if(is_array($taxonomy) && count($taxonomy) > 1)
			$isSingleTax = false;
		
		$response = get_terms($query);
		
		//try to get some taxonomies
		if(empty($response) && count($search) == 1){
			unset($query["search"]);
			$response = get_terms($query);
		}
		
		if(empty($response))
			return(null);
		
		
		$arrResult = array();
		foreach($response as $term){
			
			$termID = $term->term_id;
			$name = $term->name;
			$taxonomy = $term->taxonomy;
			$count = $term->count;
			
			if($taxonomy == "post_tag")
				$taxonomy = "tag";
			
			if($isSingleTax == false)
				$title = $name.", ($taxonomy, {$count} items)";
			else
				$title = $name.", ({$count} items)";
			
			$arr = array();
			$arr["id"] = $termID;
			$arr["text"] = $title;
			
			$arrResult[] = $arr;
		}

		$arrOutput = array();
		$arrOutput["results"] = $arrResult;
		$arrOutput["pagination"] = array("more"=>false);
		
		return($arrOutput);
	}
	
	
	/**
	 * get post list for select2
	 */
	public function getPostListForSelectFromData($data, $addNotSelected = false, $limit = 10){
		
		$search = $this->getSearchFromData($data);
		
		$filterPostType = UniteFunctionsUC::getVal($data, "post_type");
		
		switch($filterPostType){
			case "product":
				$arrTypesAssoc = array("product" => __("Product","unlimited-elements-for-elementor"));
			break;
			case "elementor_template":
				$arrTypesAssoc = array("elementor_library" => __("Template","unlimited-elements-for-elementor"));
			break;
			default:
				$arrTypesAssoc = UniteFunctionsWPUC::getPostTypesAssoc(array(), true);
			break;
		}
		
		$arrPostTypes = array_keys($arrTypesAssoc);
		
		$strPostTypes = implode("','", $arrPostTypes);
		$strPostTypes = "'$strPostTypes'";
		
		//prepare query
		$db = HelperUC::getDB();
		
		$tablePosts = UniteProviderFunctionsUC::$tablePosts;
		
		$search = $db->escape($search);
		
		$where = "post_type in ($strPostTypes)";
		$where .= " and post_status in ('publish','draft','private')";
		
		$isStartWord = (strlen($search) == 1);
		
		$whereStartWord = $where." and post_title like '$search%'";
		
		$whereRegular = $where." and post_title like '%$search%'";
		
		$sqlStartWord = "select * from $tablePosts where $whereStartWord limit $limit";
		
		$sql = "select * from $tablePosts where $whereRegular limit $limit";
		
		if($isStartWord == true){
			
			//start word, then regular
			$response = $db->fetchSql($sqlStartWord);
			
			if(empty($response))
				$response = $db->fetchSql($sql);
			
		}else{
		
			//regular only
			$response = $db->fetchSql($sql);
		}
		
		if(empty($response))
			return(array());
		
		$arrResult = array();
		
		//add empty value
		if($addNotSelected == true){
			$arr = array();
			$arr["id"] = 0;
			$arr["text"] = __("[please select post]", "unlimited-elements-for-elementor");
			$arrResult[] = $arr;
		}
		
		foreach($response as $post){
			
			$postID = $post["ID"];
			$postTitle = $post["post_title"];
			$postType = $post["post_type"];
			
			$postTypeTitle = UniteFunctionsUC::getVal($arrTypesAssoc, $postType);
			
			if(empty($postTypeTitle))
				$postTypeTitle = $postType;
			
			$title = $postTitle." - ($postTypeTitle)";
			
			$arr = array();
			$arr["id"] = $postID;
			$arr["text"] = $title;
			
			$arrResult[] = $arr;			
		}
		
		
		$arrOutput = array();
		$arrOutput["results"] = $arrResult;
		$arrOutput["pagination"] = array("more"=>false);
		
		return($arrOutput);
	}
	
	
	
}