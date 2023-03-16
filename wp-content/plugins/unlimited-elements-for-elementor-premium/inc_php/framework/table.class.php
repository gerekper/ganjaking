<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


/**
 * 
 * outputs all related to table with paging
 *
 */
class UniteTableUC extends HtmlOutputBaseUC{
	
	const GET_FIELD_PAGE = "table_page";
	const GET_FIELD_INPAGE = "table_inpage";
	const GET_FIELD_SEARCH = "table_search";
	const GET_FIELD_CATEGORY = "table_category";
	const GET_FIELD_ORDER = "table_order";
	const GET_FIELD_ORDER_DIR = "table_order_dir";
	
	const GET_FIELD_OPTION = "option";
	const GET_FIELD_VIEW = "view";
	
	private $isPaging = false;
	private $isInsideTopActions = false;
	
	private $defaultOrderby;
	private $page;
	private $inPage;
	private $total;
	private $numPages;
	private $baseUrl;
	private $arrOrderFields;
	
	private $defaultInPage = 10;
		
	
	/**
	 * validate that the paging is inited
	 */
	private function validatePaging(){
		
		if($this->isPaging == false)
			UniteFunctionsUC::throwError("The paging should be available");
		
	}

	/**
	 * validate that it's inside top actions
	 */
	private function validateTopActions(){
		
		if($this->isInsideTopActions == false)
			UniteFunctionsUC::throwError("The top actions form should be started");
		
	}
	
	private function a_GETTERS(){}
	
	
	/**
	 * get page from get
	 */
	private function getPageFromGet(){
		
		$page = UniteFunctionsUC::getGetVar(self::GET_FIELD_PAGE,1,UniteFunctionsUC::SANITIZE_ID);
		$page = (int)$page;
		
		return($page);
	}
	
	/**
	 * get inpage from get
	 */
	private function getInPageFromGet(){
		$inpage = UniteFunctionsUC::getGetVar(self::GET_FIELD_INPAGE, $this->defaultInPage, UniteFunctionsUC::SANITIZE_ID);
		$inpage = (int)$inpage;
		
		return($inpage);
	}
	
	
	/**
	 * get category from get
	 */
	private function getCategoryFromGet(){
		
		$cat = UniteFunctionsUC::getGetVar(self::GET_FIELD_CATEGORY, "", UniteFunctionsUC::SANITIZE_KEY);
		
		if($cat == "all" || $cat === "")
			$cat = null;
		else 
			$cat = (int)$cat;
		
		return($cat);
	}
	
	
	/**
	 * get search value from get
	 */
	private function getSearchValueFromGet(){
				
		$search = UniteFunctionsUC::getGetVar(self::GET_FIELD_SEARCH, "", UniteFunctionsUC::SANITIZE_TEXT_FIELD);
		
		return($search);
	}
	
	/**
	 * get order value from get
	 */
	private function getOrderValueFromGet(){
		
		$order = UniteFunctionsUC::getGetVar(self::GET_FIELD_ORDER, "", UniteFunctionsUC::SANITIZE_TEXT_FIELD);
		
		if(empty($order))
			$order = $this->defaultOrderby;
		
		return($order);
	}
	
	
	/**
	 * get all available get fields
	 */
	private function getGetFieldsNames($includeBaseFields = false, $isBaseOnly = false){
		
		$fields = array();
		
		if($includeBaseFields == true){
			$fields[] = self::GET_FIELD_OPTION;
			$fields[] = self::GET_FIELD_VIEW;
			
			if($isBaseOnly == true)
				return($fields);
		}
				
		$fields[] = self::GET_FIELD_PAGE;
		$fields[] = self::GET_FIELD_INPAGE;
		$fields[] = self::GET_FIELD_SEARCH;
		$fields[] = self::GET_FIELD_CATEGORY;				
		$fields[] = self::GET_FIELD_ORDER;				
		$fields[] = self::GET_FIELD_ORDER_DIR;				
		
		return($fields);
	}
	
	
	/**
	 * get field values from get, from names array
	 */
	private function getArrFieldsValuesFromGet($fieldNames, $exceptField = null){
		
		$arrFields = array();
		
		foreach($fieldNames as $name){
		
			if(!empty($exceptField) && $name == $exceptField)
				continue;
			
			$fieldValue = UniteFunctionsUC::getGetVar($name, "", UniteFunctionsUC::SANITIZE_TEXT_FIELD);
			if(!empty($fieldValue))
				$arrFields[$name] = $fieldValue;
		}
		
		return($arrFields);
	}
	
	
	/**
	 * get array of fields from get
	 */
	private function getArrGetFields($includeBaseFields = false, $exceptField = null){
		
		$fieldNames = $this->getGetFieldsNames($includeBaseFields);
		
		$arrFields = $this->getArrFieldsValuesFromGet($fieldNames, $exceptField);
				
		return($arrFields);
	}
	
	/**
	 * get base fields obnly
	 */
	private function getArrBaseFields(){
		
		$fieldNames = $this->getGetFieldsNames(true, true);
		
		$arrFields = $this->getArrFieldsValuesFromGet($fieldNames);
		
		return($arrFields);
	}
	
	
	/**
	 * get page url
	 */
	private function getUrlPage($page = null,$exceptField=null){
		
		$arrGetFields = $this->getArrGetFields(false, $exceptField);
		
		if(!empty($page))
			$arrGetFields[self::GET_FIELD_PAGE] = $page;
						
		$urlPage = UniteFunctionsUC::addUrlParams($this->baseUrl, $arrGetFields);
				
		return($urlPage);
	}

	private function a_SETTERS(){}
	
	/**
	 * set default orderby
	 */
	public function setDefaultOrderby($orderby){
		
		$this->defaultOrderby = $orderby;		
	}
	
	private function a_GENERAL_GET(){}
	
	/**
	 * get paging options from get and default
	 */
	public function getPagingOptions(){
	
		$output = array();
		$output["page"] = $this->getPageFromGet();
		$output["inpage"] = $this->getInPageFromGet();
		$output["search"] = $this->getSearchValueFromGet();
		$output["category"] = $this->getCategoryFromGet();
		
		//take ordering
		$ordering = $this->getOrderValueFromGet();
				
		$ordering = str_replace("_desc", " desc", $ordering);
		
		$output["ordering"] = $ordering;
		
		return($output);
	}
	
	
	/**
	 * set paging data
	 */
	public function setPagingData($baseURl, $data){
	
		$this->baseUrl = $baseURl;
		
		$this->total = UniteFunctionsUC::getVal($data, "total");
		$this->page = UniteFunctionsUC::getVal($data, "page");
		$this->inPage = UniteFunctionsUC::getVal($data, "inpage");
		$this->numPages = UniteFunctionsUC::getVal($data, "num_pages");
	
		UniteFunctionsUC::validateNotEmpty($this->inPage, "in page");
		if($this->total > 0){
			UniteFunctionsUC::validateNotEmpty($this->page, "page");
			UniteFunctionsUC::validateNotEmpty($this->numPages, "num pages");
		}
	
		$this->isPaging = true;
	}
	
	
	private function a_GET_HTML(){}
	
	/**
	 * convert fields array to html hidden inputs
	 */
	private function arrFieldsToHtmlHiddenInputs($arrGetFields){
	
		$html = "";
		foreach($arrGetFields as $name=>$value)
			$html .= self::TAB3.HelperHtmlUC::getHiddenInputField($name, $value).self::BR;
	
		return($html);
	}
	
	
	/**
	 * get all hidden fields html
	 */
	private function getHtmlHiddenInputs($except_field){
	
		$arrGetFields = $this->getArrGetFields(true, $except_field);
	
		$html = $this->arrFieldsToHtmlHiddenInputs($arrGetFields);
	
		return($html);
	}
	
	
	/**
	 * get all hidden fields html
	 */
	private function getHtmlHiddenBaseInputs(){
	
		$arrGetFields = $this->getArrBaseFields();
	
		$html = $this->arrFieldsToHtmlHiddenInputs($arrGetFields);
	
		return($html);
	}
	
	
	/**
	 * put actions form end
	 */
	public function putActionsFormStart(){
		$this->validatePaging();
		
		$url = $this->baseUrl;
		$url = htmlspecialchars($url);
		
		
		$html = "";
		$html .= self::TAB2."<form method='get' name='unite-table-actions' action='{$url}'>".self::BR2;		
		$html .= $this->getHtmlHiddenBaseInputs();
		
		echo UniteProviderFunctionsUC::escCombinedHtml($html);
	
		$this->isInsideTopActions = true;
	
	}
	
	
	/**
	 * put actions form start
	 */
	public function putActionsFormEnd(){
	
		$this->validateTopActions();
	
		$html = self::TAB2."</form>".self::BR;
	
		$this->isInsideTopActions = false;
	
		echo UniteProviderFunctionsUC::escCombinedHtml($html);
	}
	
	/**
	 * get select form
	 */
	private function getHtmlFormSelect($htmlSelect, $htmlGetFields){
		
		$html = "";
		
		if($this->isInsideTopActions == false)
			$html .= "<form method='get'>";
		
		$html .= $htmlSelect;
		
		if($this->isInsideTopActions == false){
			$html .= $htmlGetFields;
			$html .= '</form>';
		}
		
		return $html;
	}
			
	
	/**
	 * get input with count content, about 10,25,50,100
	 */
	public function getHTMLInpageSelect(){
	
		$inpage = $this->getInPageFromGet();
	
		$arrNumbers = array(
				"10","25","50","100"
		);
	
		$fieldInpage = self::GET_FIELD_INPAGE;
		
		$htmlSelect = HelperHtmlUC::getHTMLSelect($arrNumbers, $inpage, "name='{$fieldInpage}' class='unite-tableitems-selectrecords' onchange='this.form.submit()'");
		$htmlGetFields = $this->getHtmlHiddenInputs($fieldInpage);
		
		$html = $this->getHtmlFormSelect($htmlSelect, $htmlGetFields);
		
		return($html);
	}
	
	
	/**
	 * put filter category input
	 */
	public function putFilterCategory(){
		
		$cat = $this->getCategoryFromGet();
				
		$objCats = new UniteCreatorCategories;
		$arrCats = $objCats->getCatsShort("all_uncat_layouts", "layout");
				
		$html = "";
		
		$fieldCat = self::GET_FIELD_CATEGORY;
		
		if($cat === "" || $cat === null)
			$cat = "all";
		
		$htmlSelect = HelperHtmlUC::getHTMLSelect($arrCats, $cat, "name='{$fieldCat}' class='unite-tableitems-category' onchange='this.form.submit()'", true);
		$htmlGetFields = $this->getHtmlHiddenInputs($fieldCat);
		
		$html = "<span class='uc-table-top-filter-title'>".esc_html__("Filter Category", "unlimited-elements-for-elementor")."</span>";
		
		$html .= $this->getHtmlFormSelect($htmlSelect, $htmlGetFields);
		
		echo UniteProviderFunctionsUC::escCombinedHtml($html);
	}
	
	
	/**
	 * get pagination html
	 */
	private function getPaginationHtml(){
		
		$this->validatePaging();
	
		$item_per_page = $this->inPage;
		$current_page = $this->page;
		$total_records = $this->total;
		$total_pages = $this->numPages;
		
		
		$isShowExtras = true;
	
		$pagination = '';
		if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
			$pagination .= '<ul class="unite-pagination class-for-pagination">';
	
			$right_links    = $current_page + 8;
			$previous       = $current_page - 1; //previous link
			$next           = $current_page + 1; //next link
			$first_link     = true; //boolean var to decide our first link
	
			//put first and previous
			if($current_page > 1 && $isShowExtras == true){
				$previous_link = ($previous==0)?1:$previous;
	
				$urlFirst = $this->getUrlPage(1);
				$urlPrev = $this->getUrlPage($previous_link);
				
				$titleFirst = esc_html__("First", "unlimited-elements-for-elementor");
				$titlePrev = esc_html__("Previous", "unlimited-elements-for-elementor");
	
				$textFirst = "";
				$textPrev = "";
	
				$pagination .= '<li class="unite-first"><a href="'.$urlFirst.'" title="'.$titleFirst.'" > &laquo; '.$textFirst.'</a></li>'; //first link
				$pagination .= '<li><a href="'.$urlPrev.'" title="'.$titlePrev.'">&lt; '.$textPrev.'</a></li>'; //previous link
	
				for($i = ($current_page-3); $i < $current_page; $i++){ //Create left-hand side links
					if($i > 0){
						$urlPage = $this->getUrlPage($i);
						$pagination .= '<li><a href="'.$urlPage.'">'.$i.'</a></li>';
					}
				}
				$first_link = false; //set first link to false
			}
	
			if($first_link){ //if current active page is first link
				$pagination .= '<li class="unite-first unite-active"><a href="javascript:void(0)">'.$current_page.'</a></li>';
			}elseif($current_page == $total_pages){ //if it's the last active link
				$pagination .= '<li class="unite-last unite-active"><a href="javascript:void(0)">'.$current_page.'</a></li>';
			}else{ //regular current link
				$pagination .= '<li class="unite-active"><a href="javascript:void(0)">'.$current_page.'</a></li>';
			}
	
			for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
				if($i<=$total_pages){
					
					$urlPage = $this->getUrlPage($i);
										
					$pagination .= '<li><a href="'.$urlPage.'">'.$i.'</a></li>';
				}
			}
	
			//show first / last
			if($current_page < $total_pages && $isShowExtras == true){
	
				//next and last pages
				$next_link = ($i > $total_pages)? $total_pages : $i;
	
				$urlNext = $this->getUrlPage($next_link);
				$urlLast = $this->getUrlPage($total_pages);
	
				$titleNext = esc_html__("Next Page", "unlimited-elements-for-elementor");
				$titleLast = esc_html__("Last Page", "unlimited-elements-for-elementor");
	
				$textNext = "";
				$textLast = "";
	
				$pagination .= "<li><a href=\"{$urlNext}\" title=\"$titleNext\" >{$textNext} &gt;</a></li>";
				$pagination .= "<li class=\"unite-last\"><a href=\"{$urlLast}\" title=\"$titleLast\" >{$textLast} &raquo; </a></li>";
			}
	
			$pagination .= '</ul>';
		}
	
		return($pagination);
	}
	
	/**
	 * draw table pagination
	 */
	public function putPaginationHtml(){
		$this->validatePaging();
	
		$html = $this->getPaginationHtml();
	
		echo UniteProviderFunctionsUC::escCombinedHtml($html);
	}
	
	/**
	 * put inpage select
	 */
	public function putInpageSelect(){
		$this->validatePaging();
		
		if($this->total <= 10)
			return("");
		
		$html = $this->getHTMLInpageSelect();
		
		echo UniteProviderFunctionsUC::escCombinedHtml($html);
	}

	/**
	 * function for search content and sorting
	 */
	public function putSearchForm($buttonText = "", $clearText = "", $putByDefault = true){
		
		//the button must be inside top actions
		$this->validateTopActions();
	
		$html = "";
			
		
		$fieldValue = $this->getSearchValueFromGet();
		$fieldValue = htmlspecialchars($fieldValue);

		if(empty($buttonText))
			$buttonText = esc_html__("Search", "unlimited-elements-for-elementor");
		
		if(empty($clearText))
			$clearText = esc_html__("Clear", "unlimited-elements-for-elementor");
		
		
		$fieldName = self::GET_FIELD_SEARCH;
		$htmlFields = $this->getHtmlHiddenInputs($fieldName);
		$urlClear = $this->getUrlPage();
		
		//is total allow to put search
		$isTotalAllow = ($this->total > 5);
		
		if($isTotalAllow == false && empty($fieldValue))
			return(false);
		
		if($putByDefault == false && empty($fieldValue))
			return(false);
		
		if($this->isInsideTopActions == false){
			$urlForm = $this->baseUrl;
			$html .= self::TAB2."<form name='unite_form_table_search' method='get' action='$urlForm'>".self::BR;
		}
		
		$html .= self::TAB2."	<input name='$fieldName' type='text' class='unite-input-medium mbottom_0 unite-cursor-text' value=\"{$fieldValue}\"/> ".self::BR;
		$html .= self::TAB2."	<button class='unite-button-primary' type='submit' value='1'>".$buttonText."</button>".self::BR;
		
		//add clear button
		if(!empty($fieldValue)){
			$urlClear = $this->getUrlPage(null, self::GET_FIELD_SEARCH);
						
			$html .= self::TAB2."	<a class='unite-button-secondary' href=\"{$urlClear}\" >". $clearText."</a>".self::BR;
		}
		
				
		if($this->isInsideTopActions == false){
			$html .= self::TAB3.$htmlFields.self::BR;
			$html .= self::TAB2."</form>".self::BR;
		}
		
		if(!empty($searchValue))
			$html .= "	<a href=\"{$url}\" class=\"unite-button-secondary\">".$clearText."</a>";
		
	
		echo UniteProviderFunctionsUC::escCombinedHtml($html);
	}
	
	

	
	
	
	
		
	
	/**
	 * put table order header - inside table th
	 */
	public function putTableOrderHeader($name, $text){
		
		$currentOrder = $this->getOrderValueFromGet();
		
		$currentOrderField = str_replace("_desc", "", $currentOrder);
		
		$isDesc = ($currentOrder != $currentOrderField);
		
		$orderForLink = $name;
		if($currentOrderField == $name && $isDesc == false)
			$orderForLink = $name."_desc";
		
		$link = $this->getUrlPage(null, self::GET_FIELD_ORDER);
		
		$link = UniteFunctionsUC::addUrlParams($link, self::GET_FIELD_ORDER."=".$orderForLink);
		
		//get text
		$signUp = "&#8743;";
		$signDown = "&#8744;";
		
		$linkText = $text;
		if($name == $currentOrderField){
			if($isDesc == true)
				$linkText .= " ".$signUp;
			else
				$linkText .= " ".$signDown;				
		}
		
		$html = "<a href='$link'>$linkText</a>";
		
		echo UniteProviderFunctionsUC::escCombinedHtml($html);
	}
	
}