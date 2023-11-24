<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class UniteCreatorTemplateEngineWork{

	protected $twig;
	protected $arrTemplates = array();
	protected $arrParams = null;
	protected $arrItems = array();
	protected $addon = null;
	protected $objParamsProcessor;
	protected $isItemsFromPosts = false;

	private static $arrSetVarsCache = array();
	private static $urlBaseCache = null;
	private static $arrCollectedSchemaItems = array();

	private static $isPostIDSaved = false;
	private static $originalQueriedObject;
	private static $originalQueriedObjectID;
	private static $originalPost;


	/**
	 * init twig
	 */
	public function __construct(){

		$this->objParamsProcessor = new UniteCreatorParamsProcessor();

	}


	public function a_____CUSTOM_FUNCTIONS____(){}


	/**
	 * output some item
	 */
	private function outputItem($index, $itemParams, $templateName, $sap, $newLine = true){

		GlobalsProviderUC::$isUnderItem = true;

		$arrDynamicSettings = null;

		if($this->isItemsFromPosts == true){

			//HelperProviderUC::startDebugQueries();

			GlobalsProviderUC::$isUnderRenderPostItem = true;

			//save post id

			$arrItem = UniteFunctionsUC::getVal($itemParams, "item");

			$postType = UniteFunctionsUC::getVal($arrItem, "object_type");

			$postID = UniteFunctionsUC::getVal($arrItem, "object_id");

			GlobalsProviderUC::$lastObjectID = $postID;

			//woo commerce global object product save

			if($postType == "product" && function_exists("wc_get_product")){

				global $product;
				$product = wc_get_product(GlobalsProviderUC::$lastObjectID);
			}

			//save post to allow dynamic tags inside the item

			$post = UniteFunctionsUC::getVal(GlobalsProviderUC::$arrFetchedPostsObjectsCache, $postID);

			self::$isPostIDSaved = false;

			if(!empty($post)){

				self::$isPostIDSaved = true;

				global $wp_query;

				//backup the original querified object
				$originalQueriedObject = $wp_query->queried_object;
				self::$originalQueriedObject = $originalQueriedObject;

				$originalQueriedObjectID = $wp_query->queried_object_id;
				self::$originalQueriedObjectID = $originalQueriedObjectID;

				$originalPost = $GLOBALS['post'];
				self::$originalPost = $originalPost;

				$wp_query->queried_object = $post;
				$wp_query->queried_object_id = $postID;

				$GLOBALS['post'] = $post;

				//get dynamic settings from the widget if exists

				$arrDynamicSettings = apply_filters("ue_get_current_widget_settings",array());

			}


		}

		// handle params and html

		$params = array_merge($this->arrParams, $itemParams);

		if(!empty($arrDynamicSettings) && is_array($arrDynamicSettings)){

			$params = array_merge($params, $arrDynamicSettings);
		}


		GlobalsProviderUC::$lastItemParams = $params;

		$htmlItem = $this->twig->render($templateName, $params);

		$htmlItem = do_shortcode($htmlItem);

		if(!empty($sap)){
			if($index != 0)
				echo UniteProviderFunctionsUC::escCombinedHtml($sap);
			echo UniteProviderFunctionsUC::escCombinedHtml($htmlItem);
		}else
			echo UniteProviderFunctionsUC::escCombinedHtml($htmlItem);

		if($newLine)
			echo "\n";


		if($this->isItemsFromPosts == true){

			GlobalsProviderUC::$isUnderRenderPostItem = false;

			//restore the original queried object

			if(self::$isPostIDSaved == true){

				$wp_query->queried_object = $originalQueriedObject;
				$wp_query->queried_object_id = $originalQueriedObjectID;
				$GLOBALS['post'] = $originalPost;
			}

		}

		GlobalsProviderUC::$isUnderItem = false;


	}


	/**
	 * return saved post
	 */
	private function returnSavedPost(){

		if(self::$isPostIDSaved == false)
			return(false);

		global $wp_query;

		$wp_query->queried_object = self::$originalQueriedObject;
		$wp_query->queried_object_id = self::$originalQueriedObjectID;
		$GLOBALS['post'] = self::$originalPost;

		self::$isPostIDSaved = false;

	}


	/**
	 * put items actually
	 */
	private function putItemsWork($templateName, $sap=null, $numItem=null){

		if(empty($this->arrItems))
		 	return(false);

		if($this->isTemplateExists($templateName) == false)
			return(false);

		if($numItem !== null){
			$itemParams = UniteFunctionsUC::getVal($this->arrItems, $numItem);
			if(empty($itemParams))
				return(false);

			$this->outputItem($numItem, $itemParams, $templateName, $sap, false);

			return(false);
		}

		//if sap, then no new line
		$newLine = empty($sap);

		foreach($this->arrItems as $index => $itemParams)
			$this->outputItem($index, $itemParams, $templateName, $sap, $newLine);

	}


	/**
	 * put items. input can be saporator or number of item, or null
	 */
	public function putItems($input = null, $templateName = "item"){

		$sap = null;
		$numItem = null;
		$isGetFirst = false;

		if(is_numeric($input)){
			$numItem = $input;
			$input = null;
		}

		//parse the string input

		if(is_string($input)){

			switch($input){
				case "shuffle":		//shuffle items

					shuffle($this->arrItems);

					foreach($this->arrItems as $key => $item)
						$this->arrItems[$key][$templateName]["item_index"] = ($key+1);

				break;
				case "one_random":		//get one random item
					shuffle($this->arrItems);
					$isGetFirst = true;
				break;
				case "one_first":
					$isGetFirst = true;
				break;
				default:
					$sap = $input;
				break;
			}

		}

		//get first item
		if($isGetFirst == true && !empty($this->arrItems) && count($this->arrItems) > 1)
			$this->arrItems = array($this->arrItems[0]);


		$this->putItemsWork($templateName, $sap, $numItem);
	}


	/**
	 * get the items for iteration
	 */
	public function getItems($type = null){

		$arrItems = array();
		foreach($this->arrItems as $item){
			$item = $item["item"];
			if($type == "clean"){
				unset($item["item_repeater_class"]);
				unset($item["item_index"]);
				unset($item["item_id"]);
			}

			$arrItems[] = $item;
		}

		return($arrItems);
	}

	/**
	 * put items json for js
	 */
	public function putItemsJson($type = null){

		//modify items for output
		$arrItems = $this->getItems($type);

		//json encode
		$jsonItems = UniteFunctionsUC::jsonEncodeForClientSide($arrItems);

		echo $jsonItems;
	}

	/**
	 * put data json for js
	 */
	public function putAttributesJson($type = null, $key = null){

		$arrAttr = $this->arrParams;

		if($type == "clean")
			$arrAttr = UniteFunctionsUC::removeArrItemsByKeys($arrAttr, GlobalsProviderUC::$arrAttrConstantKeys);

		if(!empty($key))
			$arrAttr = UniteFunctionsUC::getVal($arrAttr, $key);

		$jsonAttr = UniteFunctionsUC::jsonEncodeForClientSide($arrAttr);

		echo $jsonAttr;
	}


	/**
	 * put items 2
	 */
	public function putItems2($input = null){
		$this->putItems($input, "item2");
	}

	/**
	 * put items 2
	 */
	public function putCssItems(){
		$this->putItems(null, "css_item");
	}



	/**
	 * put html items schema
	 */
	public function putSchemaItems($titleKey = "title", $contentKey = "content",$schemaType = "faq", $isCollect = false){

		if(empty($titleKey))
			$titleKey = "title";

		if(empty($contentKey))
			$contentKey = "content";

		$arrWidgetItems = $this->arrItems;

		if($isCollect == true){

			self::$arrCollectedSchemaItems = array_merge(self::$arrCollectedSchemaItems, $arrWidgetItems);

			return(false);
		}

		//output

		// combine from collected and empty the collected

		if(empty($arrWidgetItems))
			$arrWidgetItems = array();

		if(!empty(self::$arrCollectedSchemaItems)){

			$arrWidgetItems = array_merge(self::$arrCollectedSchemaItems, $arrWidgetItems);

			self::$arrCollectedSchemaItems = array();
		}

		$arrItems = HelperUC::$operations->getArrSchema($arrWidgetItems, "faq",$titleKey, $contentKey);

		if(empty($arrItems))
			return(false);

		$jsonItems = json_encode($arrItems);

		$htmlSchema = '<script type="application/ld+json">'.$jsonItems.'</script>';

		echo $htmlSchema;

		//echo htmlspecialchars($htmlSchema);	//debug

	}


	/**
	 * check and put schema items by param
	 */
	public function checkPutSchemaItems($paramName){

		$param = $this->addon->getParamByName($paramName);

		$type = UniteFunctionsUC::getVal($param, "type");

		if($type != UniteCreatorDialogParam::PARAM_SPECIAL)
			return(false);

		$arrValues = UniteFunctionsUC::getVal($param, "value");

		if(empty($arrValues))
			return(false);

		$isEnable = UniteFunctionsUC::getVal($arrValues, $paramName."_enable");
		$isEnable = UniteFunctionsUC::strToBool($isEnable);

		if($isEnable == false)
			return(false);

		$schemaType = UniteFunctionsUC::getVal($arrValues, $paramName."_type");

		$titleName = UniteFunctionsUC::getVal($param, "schema_title_name","title");
		$contentName = UniteFunctionsUC::getVal($param, "schema_content_name","content");


		//collect items
		if($schemaType === "collect"){

			$this->putSchemaItems($titleName, $contentName,"faq", true);

			return(false);
		}


		$this->putSchemaItems($titleName, $contentName);


	}


	/**
	 * put font override
	 */
	public function putFontOverride($name, $selector, $useID = false){

		$arrFonts = $this->addon->getArrFonts();

		if(empty($arrFonts))
			return(false);

		$cssSelector = "";
		if($useID == true)
			$cssSelector .= "#".$this->arrParams["uc_id"];

		if(!empty($cssSelector))
			$cssSelector .= " ".$selector;

		$fontKey = "uc_font_override_".$name;

		$arrFont = UniteFunctionsUC::getVal($arrFonts, $fontKey);

		if(empty($arrFont))
			return(false);

		$processor = new UniteCreatorParamsProcessor();
		$processor->init($this->addon);

		$css = $processor->processFont(null, $arrFont, true, $cssSelector, $fontKey);

		if(empty($css))
			return(false);

		echo UniteProviderFunctionsUC::escAddParam($css);
	}


	/**
	 * put font override
	 */
	public function putPostTags($postID){

		echo "no tag list for this platform";
	}


	/**
	 * put post meta function
	 */
	public function putPostMeta($postID, $key){

		echo "no meta for this platform";
	}

	/**
	 * print post meta function
	 */
	public function printPostMeta($postID){

		echo "no meta for this platform";
	}


	/**
	 * get term custom field
	 */
	public function getTermCustomFields($termID){

		echo "no term custom fields in this platform";

	}

	/**
	 * get term meta
	 */
	public function getTermMeta($termID, $key=""){

		echo "no term meta in this platform";

	}


	/**
	 * get post meta
	 */
	public function getPostMeta($postID, $key){

		echo "no meta for this platform";
		exit();
	}

	/**
	 * get term meta
	 */
	public function getUserMeta($userID, $key){

		echo "no user meta in this platform";

	}


	/**
	 * put font override
	 */
	public function putAcfField($postID, $fieldname){

		echo "no acf available for this platform";
	}

	/**
	 * put post date
	 */
	public function putPostDate($postID, $dateFormat){

		echo "no custom date for this platform";
	}


	/**
	 * filter uc date, clear html first, then replace the date
	 */
	public function filterUCDate($dateStamp, $format = "", $formatDateFrom = "d/m/Y"){

		//get the time ago string

		if($format === "time_ago"){
			$strTimeAgo = UniteFunctionsUC::getTimeAgoString($dateStamp);

			return($strTimeAgo);
		}

		if(empty($format))
			$format = get_option("date_format");

		if(empty($format))
			$format = "d F Y";

		$hasTags = false;
		$stamp = $dateStamp;

		//try to stip tags
		if(is_numeric($dateStamp) == false){
			$hasTags = true;
			$stamp = strip_tags($dateStamp);
			$stamp = trim($stamp);
		}

		/**
		 * convert from string
		 */
		if(is_numeric($stamp) == false){

			$hasTags = false;

			$objDate = DateTime::createFromFormat($formatDateFrom, $stamp);

			if(!empty($objDate))
				$stamp = @$objDate->getTimeStamp();
			else
				$stamp = time();
		}

		$strDate = date_i18n($format, $stamp);

		if($hasTags == true)
			$strDate = str_replace($stamp, $strDate, $dateStamp);

		return($strDate);
	}


	/**
	 * show item
	 */
	public function showItem($arrItem){
		dmp($arrItem);
	}


	/**
	 * get post get variable
	 */
	public function putPostGetVar($varName, $default=""){

		$varName = UniteProviderFunctionsUC::sanitizeVar($varName, UniteFunctionsUC::SANITIZE_KEY);

		$value = UniteFunctionsUC::getPostGetVariable($varName, $default , UniteFunctionsUC::SANITIZE_TEXT_FIELD);

		if(empty($value))
			$value = $default;

		echo UniteProviderFunctionsUC::escCombinedHtml($value);
	}


	/**
	 * convert date to type
	 */
	public function put_date_utc($strDate){

		$stamp = strtotime($strDate);

		$strUTC = gmdate('Y/m/d H:i:s', $stamp);

		echo UniteProviderFunctionsUC::escCombinedHtml($strUTC);
	}


	/**
	 * show data
	 */
	public function showData(){

		dmp("Params:");
		dmp($this->arrParams);

		dmp("Items:");
		dmp($this->arrItems);

	}


	/**
	 * show debug
	 */
	public function showDebug($type = null){

		HelperUC::showDebug();

	}

	/**
	 * get all data
	 */
	public function getData(){

		$data = $this->arrParams;

		return($data);
	}


	/**
	 * get post tags
	 */
	public function getPostTags($postID){

		$errorPrefix = "getPostTags function error: ";

		if(empty($postID))
			UniteFunctionsUC::throwError("$errorPrefix - no postID argument found");

		$arrTerms = UniteFunctionsWPUC::getPostSingleTerms($postID, "post_tag");

		if(empty($arrTerms))
			return(array());

		$objParamsProcessor = new UniteCreatorParamsProcessor();

		$arrTagsOutput = $objParamsProcessor->modifyArrTermsForOutput($arrTerms);

		return($arrTagsOutput);
	}


	/**
	 * print some variable
	 */
	public function printVar($var){

		dmp($var);
	}


	/**
	 * do some wp
	 */
	public function do_action($tag, $param = null, $param2 = null, $param3=null){

		//add debug
		if($param === null)
			HelperUC::addDebug("running action: $tag");
		else
			HelperUC::addDebug("running action: $tag",array(
			"param"=>$param,
			"param2"=>$param2,
			"param3"=>$param3,
		));

		//run action, without or with params

		if($param === null){
			do_action($tag);
			return(false);
		}

		//$param exists

		if($param2 === null){
			do_action($tag, $param);
			return(false);
		}

		if($param3 === null){
			do_action($tag, $param, $param2);
			return(false);
		}

		do_action($tag, $param, $param2, $param3);

	}


	/**
	 * get data by filters
	 */
	public function apply_filters($tag, $value = null, $param1 = null, $param2=null){

		UniteFunctionsUC::throwError("The apply_filters() function exists only in PRO version of the plugin");

	}

	/**
	 * get data by filters
	 */
	public function getByPHPFunction($funName){

		UniteFunctionsUC::throwError("The getByPHPFunction() function exists only in PRO version of the plugin. You can run any php function that return data and starting with 'get_' by it.");
	}


	/**
	 * filter truncate
	 * preserve - preserve word
	 * separator - is the ending
	 */
	public function filterTruncate($value, $length = 100, $preserve = true, $separator = '...'){

		$value = UniteFunctionsUC::truncateString($value, $length, $preserve, $separator);

        return $value;
	}

	/**
	 * run filter wp autop
	 *
	 */
	public function filterWPAutop($text, $br = true){

		return wpautop($text, $br);
	}

	/**
	 * get post terms
	 */
	public function getPostTerms($postID, $taxonomy, $addCustomFields = false, $type = "", $maxTerms = null){

		dmp("no terms in this platform");

		return(null);
	}

	/**
	 * function for override
	 */
	protected function initTwig_addExtraFunctionsPro(){
		//function for override
	}


	/**
	 * get woo child product
	 */
	public function getWooChildProducts($productID, $getCustomFields = true, $getCategory = true){

		$objWooIntegrate = UniteCreatorWooIntegrate::getInstance();
		$isActive = UniteCreatorWooIntegrate::isWooActive();

		if($isActive == false)
			return(false);

		$arrChildProductIDs = $objWooIntegrate->getChildProducts($productID);

		if(empty($arrChildProductIDs))
			return(array());

		$arrAdditions = array();
		if($getCustomFields == true)
			$arrAdditions[GlobalsProviderUC::POST_ADDITION_CUSTOMFIELDS] = true;

		if($getCategory == true)
			$arrAdditions[GlobalsProviderUC::POST_ADDITION_CATEGORY] = true;

		$objProcessor = new UniteCreatorParamsProcessor();

		$arrProducts = array();

		foreach($arrChildProductIDs as $productID){

			$arrProduct = $objProcessor->getPostData($productID, $arrAdditions);

			$arrProducts[] = $arrProduct;
		}

		return($arrProducts);
	}


	/**
	 * get post author
	 */
	public function getPostAuthor($authorID, $getMeta = false, $getAvatar = false){

		$arrUserData = UniteFunctionsWPUC::getUserDataById($authorID, $getMeta, $getAvatar);

		return($arrUserData);
	}

	/**
	 * get user data by username
	 */
	public function getUserData($username, $getMeta = false, $getAvatar = false){

		$arrUserData = UniteFunctionsWPUC::getUserDataById($username, $getMeta, $getAvatar);

		return($arrUserData);
	}


	/**
	 * get post data
	 */
	public function getPostData($postID, $getCustomFields = false, $getCategory = false){

		if(empty($postID))
			return(null);

		if(!is_numeric($postID))
			return(null);

		$arrAdditions = array();
		if($getCustomFields == true)
			$arrAdditions[GlobalsProviderUC::POST_ADDITION_CUSTOMFIELDS] = true;

		if($getCategory == true)
			$arrAdditions[GlobalsProviderUC::POST_ADDITION_CATEGORY] = true;

		$objParamsProcessor = new UniteCreatorParamsProcessor();
		$data = $objParamsProcessor->getPostData($postID, $arrAdditions);

		return($data);
	}

	/**
	 * print some variable for javascript json
	 */
	public function printJsonVar($var){

		$encoded = json_encode($var);

		echo $encoded;
	}

	/**
	 * print json html data
	 */
	public function printJsonHtmlData($var){

		$strJson = json_encode($var);
		$strJson = htmlspecialchars($strJson);

		echo $strJson;
	}


	/**
	 * put pagination
	 */
	public function putPagination($args = array()){

		$objPagination = new UniteCreatorElementorPagination();
		$objPagination->putPaginationWidgetHtml($args);
	}

	/**
	 * put listing loop
	 */
	public function putListingItemTemplate($item, $templateID){

		$this->putDynamicLoopTemplate($item, $templateID);
	}

	/**
	 * put dynamic loop template, similar to put listing template
	 */
	public function putDynamicLoopTemplate($item, $templateID){

		$widgetID = UniteFunctionsUC::getVal($this->arrParams, "uc_id");

		$objFilters = new UniteCreatorFiltersProcess();
		$isAjax = $objFilters->isFrontAjaxRequest();

		if($isAjax == true)
			$widgetID = "%uc_widget_id%";

		HelperProviderCoreUC_EL::putListingItemTemplate($item, $templateID, $widgetID);

	}



	/**
	 * number format for woocommerce
	 */
	public function filterPriceNumberFormat($price){

		if(empty($price))
			return($price);

		$type = getType($price);

		$price = number_format($price, "2");

		$price = str_replace(".00", "", $price);

		return($price);
	}


	/**
	 * number format for woocommerce
	 */
	public function filterWcPrice($price, $variationID = null){

		if(function_exists("wc_price") == false)
			return($price);

		$newPrice = wc_price($price);

		//new - exclude if the product or variation id is not given

		if(empty($variationID))
			return($newPrice);

		if($this->isItemsFromPosts == false)
			return($newPrice);

		if(empty(GlobalsProviderUC::$lastObjectID))
			return($newPrice);

		if(!empty($variationID))
			$product = wc_get_product($variationID);
		else
			$product = wc_get_product(GlobalsProviderUC::$lastObjectID);

		if(empty($product))
			return($newPrice);

		try{

			$newPrice = apply_filters("woocommerce_get_price_html",$newPrice, $product);

		}catch(Exception $e){
		}

		return($newPrice);
	}

	/**
	 * json decode
	 */
	public function filterJsonDecode($strJson){

		$arrOutput = UniteFunctionsUC::jsonDecode($strJson);

		return($arrOutput);
	}


	/**
	 * get listing item data
	 */
	public function getListingItemData($type = null, $defaultObjectID = null){

		$data = UniteFunctionsWPUC::getQueriedObject($type, $defaultObjectID);

		$data = UniteFunctionsUC::convertStdClassToArray($data);

		return($data);
	}

	/**
	 * put post image attributes
	 */
	public function putPostImageAttributes($arrPost, $thumbName, $isPutPlaceholder = false, $urlPlaceholder = ""){

		if(empty($arrPost))
			UniteFunctionsUC::throwError("No post found :(");

		$attributes = "";

		if(isset($arrPost[$thumbName]) == false)
			$thumbName = "image";

		//dmp("put dummy placeholder");exit();

		if(!empty($arrPost[$thumbName])){

			$urlImage = $arrPost[$thumbName];
			$width = UniteFunctionsUC::getVal($arrPost, $thumbName."_width");
			$height = UniteFunctionsUC::getVal($arrPost, $thumbName."_height");

			$attributes .= "src=\"{$urlImage}\"";
			if(!empty($width) && !empty($height))
				$attributes .= " width=\"{$width}\" height=\"{$height}\"";

			return($attributes);
		}

		$isPutPlaceholder = UniteFunctionsUC::strToBool($isPutPlaceholder);

		if($isPutPlaceholder == false)
			return("");

		//put placeholder

		if(!empty($urlPlaceholder)){

			dmP("put built in placeholder");
			exit();
		}

		dmp("image placeholders");
		dmp($arrPost);
		//exit();

	}

	/**
	 * output elementor template by id
	 */
	public function putElementorTemplate($templateID, $mode = null){

		HelperProviderCoreUC_EL::putElementorTemplate($templateID,$mode);

	}

	/**
	 * output various functionality
	 */
	public function ucfunc($type, $arg1 = null, $arg2= null, $arg3=null){

		switch($type){
			case "put_date_range":

				$dateRange = HelperUC::$operations->getDateRangeString($arg1, $arg2, $arg3);
				echo $dateRange;

			break;
			case "get_general_setting":

				$value = HelperProviderCoreUC_EL::getGeneralSetting($arg1);

				return($value);
			break;
			case "run_code_once":

				$isRunOnce = HelperUC::isRunCodeOnce($arg1);
				return($isRunOnce);
			break;
			case "get_from_sql":

				$response = HelperUC::getFromSql($arg1,$arg2,$arg3);

				return($response);
			break;
			case "get_loadmore_data":

				$objPagination = new UniteCreatorElementorPagination();
				$strData = $objPagination->getLoadmoreData(GlobalsProviderUC::$isInsideEditor);

				return($strData);
			break;
			case "get_last_query_data":

				$arrData = HelperUC::$operations->getLastQueryData();

				return($arrData);

			break;
			case "get_post_term":

				//arg1 - postID
				//arg2 - taxonomy
				//arg3 - term slug

				$term = HelperProviderUC::getPostTermForTemplate($arg1, $arg2, $arg3);
				return($term);
			break;
			case "is_post_has_term":

				$term = HelperProviderUC::getPostTermForTemplate($arg1, $arg2, $arg3);

				if(!empty($term))
					return("yes");
				else
					return("no");

			break;
			case "put_unite_gallery_item":

				$htmlItem = UniteCreatorUniteGallery::getUniteGalleryHtmlItem($arg1);

				echo $htmlItem;

			break;
			case "set":		//set and remember
				self::$arrSetVarsCache[$arg1] = $arg2;
			break;
			case "get":

				$var = UniteFunctionsUC::getVal(self::$arrSetVarsCache, $arg1);

				return($var);
			break;
			case "get_wc_variations":

				$productID = $arg1;

				$objWoo = UniteCreatorWooIntegrate::getInstance();
				$arrVariations = $objWoo->getProductVariations($productID);

				return($arrVariations);
			break;
			case "get_woo_gallery":
			case "get_wc_gallery":

				$productID = $arg1;

				$objWoo = UniteCreatorWooIntegrate::getInstance();
				$arrGallery = $objWoo->getProductGallery($productID);

				return($arrGallery);
			break;
			case "get_woo_image2":

				$objWoo = UniteCreatorWooIntegrate::getInstance();

				$image2 = $objWoo->getFirstGalleryImage($arg1, $arg2);	//productID , size

				return($image2);
			break;
			case "get_woo_endpoint":

				$arrEndpoints = UniteCreatorWooIntegrate::getWooEndpoint($arg1);

				return($arrEndpoints);
			break;
			case "get_woo_cart_data":

				$objWoo = UniteCreatorWooIntegrate::getInstance();

				$arrCartData = $objWoo->getCartData();

				return($arrCartData);
			break;
			case "get_unitegallery_js":

				$objUniteGallery = new UniteCreatorUniteGallery();

				$objJsSettings = $objUniteGallery->getUniteGalleryJsSettings($this->arrParams, $this->addon);

				return($objJsSettings);
			break;
			case "put_remote_parent_js":

				HelperHtmlUC::putRemoteParentJS($arg1, $arg2);

			break;
			case "get_post_custom_field":

				$postID = $arg1;
				$fieldname = $arg2;

				$value = UniteFunctionsWPUC::getPostCustomField($postID, $fieldname);

				return($value);
			break;
			case "modify_text":

				$arg1 = HelperUC::$operations->modifyTextFromWidget($arg1);

				return($arg1);
			break;
			case "get_term_image":

				//termID, meta key

				$arrImage = UniteFunctionsWPUC::getTermImage($arg1, $arg2);

				return($arrImage);
			break;
			case "get_term_custom_field":

				$termID = $arg1;
				$fieldname = $arg2;

				$value = UniteFunctionsWPUC::getTermCustomField($termID, $fieldname);

				return($value);
			break;
			case "get_post_image":

				//termID, meta key

				$arrImage = UniteFunctionsWPUC::getPostImage($arg1, $arg2);

				return($arrImage);
			break;
			case "put_post_meta_debug":

				$postID = $arg1;

				HelperUC::$operations->putPostCustomFieldsDebug($postID);

			break;
			case "put_term_meta_debug":

				$termID = $arg1;

				if(!empty($termID))
					HelperUC::$operations->putTermCustomFieldsDebug($termID);

			break;
			case "put_terms_meta_debug":

				$arrTerms = $arg1;

				HelperUC::$operations->putTermsCustomFieldsDebug($arrTerms);

			break;
			case "put_post_content":

				$this->returnSavedPost();

				$content = HelperProviderCoreUC_EL::getPostContent($arg1, $arg2);

				echo $content;
			break;
			case "get_num_comments":

				$numComments = get_comments_number($arg1);

				return($numComments);
			break;
			case "put_hide_ids_css":

				HelperHtmlUC::putHideIdsCss($arg1);

			break;
			case "get_posts":

				//$postType, $strCats, $strArgs
				$arrPosts = HelperUC::$operations->getPostsFromTwig($arg1,$arg2,$arg3);

				return($arrPosts);
			break;
			case "put_entrance_animation_css":

				$param = $this->addon->getParamByName($arg1);

				UniteCreatorEntranceAnimations::putEntranceAnimationCss($this->arrParams, $arg1, $param);

			break;
			case "put_entrance_animation_js":

				$param = $this->addon->getParamByName($arg1);

				UniteCreatorEntranceAnimations::putEntranceAnimationJs($this->arrParams, $arg1, $param);

			break;
			case "get_current_user":

				$objUser = wp_get_current_user();

				if(empty($objUser))
					return(null);

				$userData = UniteFunctionsWPUC::getUserData($objUser,$arg2,$arg3);

				return($userData);

			break;
			case "get_url_page":
			case "get_url_ajax":

				if(!empty(self::$urlBaseCache))
					return(self::$urlBaseCache);

				$urlBase = UniteFunctionsUC::getBaseUrl(GlobalsUC::$current_page_url);

				self::$urlBaseCache = $urlBase;

				return($urlBase);
			break;
			case "put_docready_start":

				$widgetID = UniteFunctionsUC::getVal($this->arrParams, "uc_id");

				HelperHtmlUC::putDocReadyStartJS($widgetID);

			break;
			case "put_docready_end":

				$widgetID = UniteFunctionsUC::getVal($this->arrParams, "uc_id");

				HelperHtmlUC::putDocReadyEndJS($widgetID);

			break;
			case "get_product_attributes":

				$objWoo = UniteCreatorWooIntegrate::getInstance();

				$arrAttributes = $objWoo->getProductAttributes($arg1);

				return($arrAttributes);

			break;
			case "get_current_term_id":

				$termID = UniteFunctionsWPUC::getCurrentTermID();

				return($termID);
			break;
			case "put_next_post_link":

				next_post_link();

			break;
			case "put_prev_post_link":

				previous_post_link();

			break;
			case "get_nextprev_post_data":

				$data = UniteFunctionsWPUC::getNextPrevPostData($arg1, $arg2);

				return($data);
			break;
			case "put_schema_items_json":

					//$arg1- titleKey, $arg2 - contentKey, $arg3 - schemaName

					$this->putSchemaItems($arg1, $arg2, $arg3);
			break;
			case "put_schema_items_json_byparam":

					$this->checkPutSchemaItems($arg1);
			break;
			case "render":		//render twig template

				$html = $this->getRenderedHtml($arg1, GlobalsProviderUC::$isUnderItem);
				echo $html;

			break;
			case "put_post_link":	//by id

				if(!empty($arg1)){
					$link = get_permalink($arg1);
					echo $link;
				}

			break;
			case "get_encoded_image":

				$content = HelperUC::$operations->getLocalFileContentsByUrl($arg1);

				if(empty($content))
					return(null);

				$encoded = base64_encode($content);

				return($encoded);
			break;
			case "put_post_type_title":

				//print the post type title from post type

				$obj = get_post_type_object($arg1);

				if(empty($obj))
					return(false);

				echo $obj->labels->singular_name;

			break;
			case "put_post_terms_string":

				if(empty($arg1))
					$arg1 = GlobalsProviderUC::$lastObjectID;

				$strTermsNames = UniteFunctionsWPUC::getPostTermsTitlesString($arg1, true);

				echo $strTermsNames;
			break;
			case "get_sort_filter_data":

				$sortFilterItems = UniteCreatorFiltersProcess::getSortFilterData($arg1, $this->arrParams);

				return($sortFilterItems);
			break;
			case "put_term_link":	//get some term link

				if(empty($url))
					return(false);

				$url = get_term_link($arg1);
				if(is_wp_error($url)){
					dmp($url);
				}
				else echo $url;
			break;
			case "put_woo_cart_html":

				$objWoo = UniteCreatorWooIntegrate::getInstance();

				$objWoo->putCartHtml($arg1);

			break;
			case "get_breakpoints":

				$arrBreakpoints = HelperProviderCoreUC_EL::getBreakpoints();

				dmp("breakpoints");
				dmp($arrBreakpoints);

			break;
			case "csv_to_json":

				$arrData = UniteFunctionsUC::maybeCsvDecode($arg1);

				if(empty($arrData))
					$arrData = array();

				$jsonData = UniteFunctionsUC::jsonEncodeForClientSide($arrData);

				echo $jsonData;
			break;
			case "validate_submit_button":

				$isInsideEditor = UniteFunctionsUC::getVal($this->arrParams, "uc_inside_editor");

				if($isInsideEditor != "yes")
					return false;

				$form = new UniteCreatorForm();
				$formErrors = $form->validateFormSettings($this->arrParams);

				if (empty($formErrors) === false) {
					$formErrors = implode("<br />- ", $formErrors);

					dmp("<span style='color:red;'>Form settings validation failed:<br />- $formErrors</span>");
				}
			break;
			default:

				$type = UniteFunctionsUC::sanitizeAttr($type);

				dmp("<span style='color:red;'>ucfunc error: unknown action <b>'$type'</b>. Please check that the plugin is at latest version.</span>");
			break;
		}

	}


	/**
	 * put test html
	 */
	public function putTestHTML($type = null, $data = null){

		dmp("put some test html");
		dmP($type);

		//unset($data["current_post"]["content"]);
		//$post = UniteFunctionsUC::getVal($data, "")
		unset($data["content"]);
		dmp($data);

	}


	/**
	 * add extra functions to twig
	 */
	protected function initTwig_addExtraFunctions(){

		//add extra functions

		$putItemsFunction = new Twig\TwigFunction('put_items', array($this,"putItems"));
		$putItemsFunction2 = new Twig\TwigFunction('put_items2', array($this,"putItems2"));
		$putItemsJsonFunction = new Twig\TwigFunction('put_items_json', array($this,"putItemsJson"));
		$putAttributesJson = new Twig\TwigFunction('put_attributes_json', array($this,"putAttributesJson"));

		$getItems = new Twig\TwigFunction('get_items', array($this,"getItems"));
		$putGetDataFunction = new Twig\TwigFunction('get_data', array($this,"getData"));

		$putCssItemsFunction = new Twig\TwigFunction('put_css_items', array($this,"putCssItems"));
		$putFontOverride = new Twig\TwigFunction('put_font_override', array($this,"putFontOverride"));
		$putPostTagsFunction = new Twig\TwigFunction('putPostTags', array($this,"putPostTags"));
		$putPostMetaFunction = new Twig\TwigFunction('putPostMeta', array($this,"putPostMeta"));
		$getPostMetaFunction = new Twig\TwigFunction('getPostMeta', array($this,"getPostMeta"));
		$getUserMeta = new Twig\TwigFunction('getUserMeta', array($this,"getUserMeta"));

		$printPostMetaFunction = new Twig\TwigFunction('printPostMeta', array($this,"printPostMeta"));

		$putACFFieldFunction = new Twig\TwigFunction('putAcfField', array($this,"putAcfField"));

		$putShowFunction = new Twig\TwigFunction('show', array($this,"showItem"));
		$putPostDateFunction = new Twig\TwigFunction('putPostDate', array($this,"putPostDate"));
		$putPostGetVar = new Twig\TwigFunction('putPostGetVar', array($this,"putPostGetVar"));
		$convertDate = new Twig\TwigFunction('put_date_utc', array($this,"put_date_utc"));
		$putShowDataFunction = new Twig\TwigFunction('showData', array($this,"showData"));
		$putShowDebug = new Twig\TwigFunction('showDebug', array($this,"showDebug"));
		$getPostTags = new Twig\TwigFunction('getPostTags', array($this,"getPostTags"));
		$getPostData = new Twig\TwigFunction('getPostData', array($this,"getPostData"));
		$putPagination = new Twig\TwigFunction('putPagination', array($this,"putPagination"));

		$putListingItemTemplate = new Twig\TwigFunction('putListingItemTemplate', array($this,"putListingItemTemplate"));
		$putDynamicLoopTemplate = new Twig\TwigFunction('putDynamicLoopTemplate', array($this,"putDynamicLoopTemplate"));

		$putElementorTemplate = new Twig\TwigFunction('putElementorTemplate', array($this,"putElementorTemplate"));

		$putPostImageAttributes = new Twig\TwigFunction('putPostImageAttributes', array($this,"putPostImageAttributes"));

		$printVar = new Twig\TwigFunction('printVar', array($this,"printVar"));
		$printJsonVar = new Twig\TwigFunction('printJsonVar', array($this,"printJsonVar"));
		$printJsonHtmlData = new Twig\TwigFunction('printJsonHtmlData', array($this,"printJsonHtmlData"));

		$doAction = new Twig\TwigFunction('do_action', array($this,"do_action"));
		$applyFilters = new Twig\TwigFunction('apply_filters', array($this,"apply_filters"));
		$getByPHPFunction = new Twig\TwigFunction('getByPHPFunction', array($this,"getByPHPFunction"));
		$ucfunc = new Twig\TwigFunction('ucfunc', array($this,"ucfunc"));

		$getPostTerms = new Twig\TwigFunction('getPostTerms', array($this,"getPostTerms"));
		$getPostAuthor = new Twig\TwigFunction('getPostAuthor', array($this,"getPostAuthor"));
		$getUserData = new Twig\TwigFunction('getUserData', array($this,"getUserData"));
		$getWooChildProducts = new Twig\TwigFunction('getWooChildProducts', array($this,"getWooChildProducts"));
		$getListingItemData = new Twig\TwigFunction('getListingItemData', array($this,"getListingItemData"));

		$printTermCustomFields = new Twig\TwigFunction('printTermCustomFields', array($this,"printTermCustomFields"));
		$getTermCustomFields = new Twig\TwigFunction('getTermCustomFields', array($this,"getTermCustomFields"));
		$getTermMeta = new Twig\TwigFunction('getTermMeta', array($this,"getTermMeta"));

		$filterTruncate = new Twig\TwigFilter("truncate", array($this, "filterTruncate"));
		$filterWPAutop = new Twig\TwigFilter("wpautop", array($this, "filterWPAutop"));
		$filterUCDate = new Twig\TwigFilter("ucdate", array($this, "filterUCDate"));
		$filterPriceNumberFormat = new Twig\TwigFilter("price_number_format", array($this, "filterPriceNumberFormat"));
		$filterWcPrice = new Twig\TwigFilter("wc_price", array($this, "filterWcPrice"));
		$filterJsonDecode = new Twig\TwigFilter("json_decode", array($this, "filterJsonDecode"));

		$putTestHtml = new Twig\TwigFunction('putTestHTML', array($this,"putTestHTML"));


		//add extra functions
		$this->twig->addFunction($putItemsFunction);
		$this->twig->addFunction($putItemsFunction2);
		$this->twig->addFunction($putCssItemsFunction);
		$this->twig->addFunction($putFontOverride);
		$this->twig->addFunction($putPostTagsFunction);

		$this->twig->addFunction($putPostMetaFunction);
		$this->twig->addFunction($getPostMetaFunction);
		$this->twig->addFunction($printPostMetaFunction);

		$this->twig->addFunction($getUserMeta);
		$this->twig->addFunction($getListingItemData);

		$this->twig->addFunction($getTermMeta);

		$this->twig->addFunction($putACFFieldFunction);
		$this->twig->addFunction($putShowFunction);
		$this->twig->addFunction($putPostDateFunction);
		$this->twig->addFunction($putPostGetVar);
		$this->twig->addFunction($convertDate);
		$this->twig->addFunction($putShowDataFunction);
		$this->twig->addFunction($putShowDebug);
		$this->twig->addFunction($putGetDataFunction);

		$this->twig->addFunction($getPostTags);
		$this->twig->addFunction($getPostData);
		$this->twig->addFunction($printVar);
		$this->twig->addFunction($printJsonVar);
		$this->twig->addFunction($printJsonHtmlData);
		$this->twig->addFunction($putPagination);
		$this->twig->addFunction($putListingItemTemplate);
		$this->twig->addFunction($putDynamicLoopTemplate);
		$this->twig->addFunction($putElementorTemplate);

		$this->twig->addFunction($getPostTerms);
		$this->twig->addFunction($getPostAuthor);
		$this->twig->addFunction($getUserData);
		$this->twig->addFunction($getWooChildProducts);
		$this->twig->addFunction($getTermCustomFields);
		$this->twig->addFunction($putItemsJsonFunction);
		$this->twig->addFunction($putAttributesJson);

		$this->twig->addFunction($getItems);
		$this->twig->addFunction($putPostImageAttributes);

		//test functions
		$this->twig->addFunction($putTestHtml);


		//add filters
		$this->twig->addFilter($filterTruncate);
		$this->twig->addFilter($filterWPAutop);
		$this->twig->addFilter($filterUCDate);
		$this->twig->addFilter($filterPriceNumberFormat);
		$this->twig->addFilter($filterWcPrice);
		$this->twig->addFilter($filterJsonDecode);


		//pro functions
		$this->twig->addFunction($doAction);
		$this->twig->addFunction($applyFilters);
		$this->twig->addFunction($getByPHPFunction);
		$this->twig->addFunction($ucfunc);

		$this->initTwig_addExtraFunctionsPro();

	}


	public function a_____OTHER_FUNCTIONS_____(){}


	/**
	 * init twig
	 */
	private function initTwig(){

		if(empty($this->arrTemplates))
			UniteFunctionsUC::throwError("No templates found");

		if(class_exists("Twig\\Loader\\ArrayLoader") == false)
			UniteFunctionsUC::throwError("Twig template engine not loaded. Please check if it collides with some other plugin that also loading twig engine.");

		$loader = new Twig\Loader\ArrayLoader($this->arrTemplates);

		$arrOptions = array();
		$arrOptions["debug"] = true;

		if(class_exists("Twig\\Environment") == false)
			UniteFunctionsUC::throwError("You have some other plugin that loaded another version of twig. It's uncompatable with unlimited elements unfortunatelly.");

		$this->twig = new Twig\Environment($loader, $arrOptions);
		$this->twig->addExtension(new Twig\Extension\DebugExtension());

		$this->initTwig_addExtraFunctions();

	}


	/**
	 * validate that not inited
	 */
	private function validateNotInited(){
		if(!empty($this->twig))
			UniteFunctionsUC::throwError("Can't add template or params when after rendered");
	}


	/**
	 * validate that all is inited
	 */
	private function validateInited(){

		if($this->arrParams === null){
			UniteFunctionsUC::throwError("Please set the params");
		}

	}


	/**
	 * return if some template exists
	 * @param $name
	 */
	public function isTemplateExists($name){

		$isExists = array_key_exists($name, $this->arrTemplates);

		return($isExists);
	}



	/**
	 * add template
	 */
	public function addTemplate($name, $html, $showError = true){

		$this->validateNotInited();

		$isExists = isset($this->arrTemplates[$name]);

		if($isExists == true){

			if($showError == false)
				return(false);

			UniteFunctionsUC::throwError("template with name: $name already exists");
		}


		$this->arrTemplates[$name] = $html;
	}


	/**
	 * add params
	 */
	public function setParams($params){

		$this->arrParams = $params;

	}

	/**
	 * set items source
	 */
	public function setItemsSource($source){

		if($source == "posts")
			$this->isItemsFromPosts = true;

	}


	/**
	 * set items
	 * @param $arrItems
	 */
	public function setArrItems($arrItems){

		$this->arrItems = $arrItems;

		$numItems = 0;
		if(is_array($arrItems))
			$numItems = count($arrItems);

		//add number of items
		$this->arrParams["uc_num_items"] = count($arrItems);

	}


	/**
	 * set fonts array
	 */
	public function setArrFonts($arrFonts){
		$this->arrFonts = $arrFonts;
	}


	/**
	 * get rendered html
	 * @param $name
	 */
	public function getRenderedHtml($name, $isInsideItems = false){

		UniteFunctionsUC::validateNotEmpty($name);
		$this->validateInited();
		if(array_key_exists($name, $this->arrTemplates) == false)
			UniteFunctionsUC::throwError("Template with name: $name not exists");

		if(empty($this->twig))
			$this->initTwig();

		$params = $this->arrParams;

		if($isInsideItems == true)
			$params = GlobalsProviderUC::$lastItemParams;

		$output = $this->twig->render($name, $params);

		return($output);
	}


	/**
	 * set addon
	 */
	public function setAddon($addon){

		$this->addon = $addon;
	}

}
