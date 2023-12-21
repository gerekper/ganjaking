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
 * creator helper functions class
 *
 */
	class HelperUC extends UniteHelperBaseUC{

		private static $db;
		public static $operations;
		private static $arrFontPanelData;
		private static $arrAdminNotices = array();
		private static $arrStickyAdminNotice = array();
		private static $isPutAnimations = false;
		private static $arrLogMemory = array();
		private static $arrHashCache = array();
		private static $arrRunOnceCache = array();
		private static $arrLocalText = array();
		private static $arrDebug;
		private static $hasOutput = false;
		public static $arrWidgetScripts = array();

		public static function a____GENERAL____(){}

		/**
		 * heck if debug by url
		 */
		public static function isDebug(){

			$debug = UniteFunctionsUC::getGetVar("ucdebug","",UniteFunctionsUC::SANITIZE_KEY);

			if(empty($debug))
				return(false);

			$debug = UniteFunctionsUC::strToBool($debug);

			return($debug);
		}


		/**
		 * validate plugin startup functionality
		 * Enter description here ...
		 */
		public static function validatePluginStartup(){
			
			if(function_exists("simplexml_load_file") == false)
				UniteFunctionsUC::throwError("Your php missing SimpleXML Extension. The plugin can't work without this extension because it has many xml files to load. Please enable this extension in php.ini");

		}

		/**
		 * get the database
		 */
		public static function getDB(){

			if(empty(self::$db))
				self::$db = new UniteCreatorDB();

			return(self::$db);
		}

		/**
		 * set local text object for text output
		 */
		public static function setLocalText($arrText){

			self::$arrLocalText = array_merge(self::$arrLocalText, $arrText);

		}

		/**
		 * include all plugins
		 */
		public static function includeAllPlugins(){

			$objUCPlugins = new UniteCreatorPlugins();
			$objUCPlugins->initPlugins();
		}


		/**
		 * run provider function
		 */
		public static function runProviderFunc($func){

			$args = func_get_args();
			array_shift($args);

			$exists = method_exists("UniteProviderFunctionsUC",$func);

			if(!$exists)
				return(false);

			call_user_func_array(array("UniteProviderFunctionsUC",$func), $args);

		}


		/**
		 * get font panel fields
		 */
		public static function getFontPanelData(){

			if(!empty(self::$arrFontPanelData))
				return(self::$arrFontPanelData);

			require GlobalsUC::$pathSettings."font_panel_data.php";

			self::$arrFontPanelData = $arrData;

			return(self::$arrFontPanelData);
		}


		/**
		 * get text by key
		 */
		public static function getText($textKey){

			$searchKey = strtolower($textKey);

			//search local text first
			if(array_key_exists($searchKey, self::$arrLocalText))
				return(self::$arrLocalText[$textKey]);

			if(array_key_exists($searchKey, GlobalsUC::$arrServerSideText))
				return(GlobalsUC::$arrServerSideText[$textKey]);

			if(array_key_exists($searchKey, GlobalsUC::$arrClientSideText))
				return(GlobalsUC::$arrClientSideText[$textKey]);

			return($textKey);
		}

		/**
		 * put text by key
		 */
		public static function putText($textKey){

			echo self::getText($textKey);
		}


		/**
		 * get settings object by name from settings folder
		 */
		public static function getSettingsObject($settingsName, $path=null){

			$pathSettings = self::getPathSettings($settingsName, $path);

			$objSettings = new UniteCreatorSettings();
			$objSettings->loadXMLFile($pathSettings);

			return($objSettings);
		}


		/**
		 * get current admin view
		 */
		public static function getAdminView(){

			if(UniteProviderFunctionsUC::isAdmin() == false)
				return(null);

			$view = UniteCreatorAdmin::getView();

			return($view);
		}


		/**
		 *
		 * get size related css from size css array
		 */
		public static function getCssMobileSize($arrSizeCss, $cssPrefix = ""){

			$css = "";
			foreach($arrSizeCss as $size=>$cssSize){
				if(empty($cssSize))
					continue;

				$cssSize = UniteFunctionsUC::addTabsToText($cssSize,"    ");
				$cssSize = HelperHtmlUC::wrapCssMobile($cssSize, $size);

				if(!empty($css))
					$css .= "\n\n";

				$css .= $cssSize;
			}

			if(!empty($cssPrefix) && !empty($css))
				$css = $cssPrefix."\n\n".$css;

			return($css);
		}


		/**
		 * check if it's special addon type like shape or shape devider
		 */
		public static function isSpecialAddonType($type){

			switch($type){
				case GlobalsUC::ADDON_TYPE_SHAPE_DEVIDER:
				case GlobalsUC::ADDON_TYPE_SHAPES:
				case GlobalsUC::ADDON_TYPE_BGADDON:
					return(true);
				break;
			}

			return(false);
		}

		/**
		 * tells if it's layout addon type or not
		 */
		public static function isLayoutAddonType($addonType){

			$objAddonType = UniteCreatorAddonType::getAddonTypeObject($addonType);

			$isLayout = $objAddonType->isLayout;

			return($isLayout);
		}


		/**
		 * get product from request
		 */
		public static function getProductFromRequest(){

			$action = UniteFunctionsUC::getPostGetVariable("client_action", "", UniteFunctionsUC::SANITIZE_TEXT_FIELD);
			if(empty($action))
				return(null);

			$data = UniteFunctionsUC::getPostGetVariable("data", "", UniteFunctionsUC::SANITIZE_NOTHING);
			$passData = UniteFunctionsUC::getVal($data, "manager_passdata");


			if(empty($passData))
				return(null);

			$product = UniteFunctionsUC::getVal($passData, "product");


			return($product);
		}

		/**
		 * get php variables info
		 */
		public static function getPHPInfo(){

			if(function_exists("ini_get_all") == false)
				return(array());

			$arrInfo = ini_get_all();

			if(empty($arrInfo))
				return(array());

			$arrKeys = array("memory_limit", "max_execution_time", "post_max_size", "upload_max_filesize");

			$arrInfoComputed = array();
			foreach($arrKeys as $key){
				$arrValues = UniteFunctionsUC::getVal($arrInfo, $key);
				$localValue = UniteFunctionsUC::getVal($arrValues, "local_value");
				$arrInfoComputed[$key] = $localValue;
			}

			return($arrInfoComputed);
		}

		/**
		 * return true - need to run.
		 * used for run code once functionality
		 */
		public static function isRunCodeOnce($key){

			$isAlreadyRun = UniteFunctionsUC::getVal(self::$arrRunOnceCache, $key);

			if($isAlreadyRun === true){
				return(false);
			}

			self::$arrRunOnceCache[$key] = true;

			return(true);
		}

		/**
		 * get records from sql query, don't allow to run other then select queries
		 */
		public static function getFromSql($query, $arg1 = null, $arg2 = null){

			$lower = strtolower($query);
			if(strpos($lower, "select") !== 0)
				UniteFunctionsUC::throwError("get from sql error - the query should start with the word 'select'");

			$query = sprintf($query, $arg1, $arg2);

			$db = self::getDB();
			$response = $db->fetchSql($query);

			return($response);
		}

		/**
		 * check if there is permissions from query
		 * and it's logged in or local
		 */
		public static function hasPermissionsFromQuery($getvar){

			$isEnabled = UniteFunctionsUC::getGetVar($getvar,"",UniteFunctionsUC::SANITIZE_TEXT_FIELD);
			$isEnabled = UniteFunctionsUC::strToBool($isEnabled);

			if($isEnabled == false)
				return(false);

			if(GlobalsUC::$isLocal == true)
				return(true);

			if(UniteFunctionsWPUC::isCurrentUserHasPermissions() == true)
				return(true);

			return(false);
		}

		public static function a_______DEBUG________(){}


		/**
		 * add debug text
		 */
		public static function addDebug($title, $content = null){

			if(empty(self::$arrDebug))
				self::$arrDebug = array();

			$name = HelperUC::convertTitleToHandle($title);

			$item = array();
			$item["name"] = $name;
			$item["title"] = $title;
			$item["content"] = $content;

			self::$arrDebug[] = $item;
		}

		/**
		 * clear debug
		 */
		public static function clearDebug(){

			self::$arrDebug = array();
		}

		/**
		 * get debug data
		 */
		public static function getDebug(){

			return(self::$arrDebug);
		}

		/**
		 * show the debug
		 */
		public static function showDebug($type = null){

			dmp("Showing Debug");

			if(!empty($type))
				dmp("$type mode");

			$arrDebug = HelperUC::getDebug();

			if(empty($arrDebug)){
				dmp("no debug content found");
				return(false);
			}

			foreach($arrDebug as $item){

				$name = UniteFunctionsUC::getVal($item, "name");

				if($type == "query"){

					switch($name){
						case "getpostlist_values":
						case "getpostlist_param":
						case "post_filters":
						case "post_additions":
							continue(2);
						break;
					}
				}

				$title = UniteFunctionsUC::getVal($item, "title");
				$content = UniteFunctionsUC::getVal($item, "content");

				$titleOutput = $title;
				if(!empty($content))
					$titleOutput = "<b>$title:</b>";

				dmp($titleOutput);
				dmp($content);

			}


		}


		public static function a_______NOTICES________(){}


		/**
		 * add notice that will be showen on plugin pages
		 */
		public static function addAdminNotice($strNotice){
			self::$arrAdminNotices[] = $strNotice;
		}

		/**
		 * add notice that will be showen on plugin pages
		 */
		public static function getAdminNotices(){

			$arrNotices = self::$arrAdminNotices;

			self::$arrAdminNotices = array();	 //clear

			return($arrNotices);
		}


		public static function a__________MEMORY__________(){}


		/**
		 * store memory log
		 * state - start, end
		 */
		public static function logMemoryUsage($operation, $isUpdateOption = false){

			$usage = memory_get_usage();

			$diff = 0;
			if(!empty(self::$arrLogMemory)){
				$lastArrUsage = self::$arrLogMemory[count(self::$arrLogMemory)-1];
				$lastUsage = $lastArrUsage["usage"];
				$diff = $usage - $lastUsage;
			}

			$arrLogItem = array("oper"=>$operation,"usage"=>$usage,"diff"=>$diff, "time"=>time());

			//log the page
			if(empty(self::$arrLogMemory)){
				$arrLogItem["current_page"] = GlobalsUC::$current_page_url;
			}

			self::$arrLogMemory[] = $arrLogItem;

			if($isUpdateOption == true)
				UniteProviderFunctionsUC::updateOption("unite_creator_memory_usage_log", self::$arrLogMemory);

		}


		/**
		 * get last memory usage
		 */
		public static function getLastMemoryUsage(){

			$arrLog = UniteProviderFunctionsUC::getOption("unite_creator_memory_usage_log");

			return($arrLog);
		}


		public static function a_________STATE________(){}


		/**
		 * remember state
		 */
		public static function setState($name, $value){

			$optionName = "untecreator_state";

			$arrState = UniteProviderFunctionsUC::getOption($optionName);
			if(empty($arrState) || is_array($arrState) == false)
				$arrState = array();

			$arrState[$name] = $value;
			UniteProviderFunctionsUC::updateOption($optionName, $arrState);
		}


		/**
		 * get remembered state
		 */
		public static function getState($name){

			$optionName = "untecreator_state";

			$arrState = UniteProviderFunctionsUC::getOption($optionName);
			$value = UniteFunctionsUC::getVal($arrState, $name, null);

			return($value);
		}



		public static function a________URL_AND_PATH_________(){}


		/**
		 * convert url to full url
		 */
		public static function URLtoFull($url, $urlBase = null){

			if(is_numeric($url))		//protection for image id
				return($url);

			if(getType($urlBase) == "boolean")
				UniteFunctionsUC::throwError("the url base should be null or string");

			if(is_array($url))
				UniteFunctionsUC::throwError("url can't be array");

			$url = trim($url);

			if(empty($url))
				return("");

			$urlLower = strtolower($url);

			if(strpos($urlLower, "http://") !== false || strpos($urlLower, "https://") !== false)
				return($url);

			if(empty($urlBase))
				$url = GlobalsUC::$url_base.$url;
			else{

				$convertUrl = GlobalsUC::$url_base;

				//preserve old format:
				$filepath = self::pathToAbsolute($url);
				if(file_exists($filepath) == false)
					$convertUrl = $urlBase;

				$url = $convertUrl.$url;
			}

			$url = UniteFunctionsUC::cleanUrl($url);

			return($url);
		}


		/**
		 * convert some url to relative
		 */
		public static function URLtoRelative($url, $isAssets = false){

			$replaceString = GlobalsUC::$url_base;
			if($isAssets == true)
				$replaceString = GlobalsUC::$url_assets;

			//in case of array take "url" from the array
			if(is_array($url)){

				$strUrl = UniteFunctionsUC::getVal($url, "url");
				if(empty($strUrl))
					return($url);

				$url["url"] = str_replace($replaceString, "", $strUrl);

				return($url);
			}

			$url = str_replace($replaceString, "", $url);

			return($url);
		}


		/**
		 * change url to assets relative
		 */
		public static function URLtoAssetsRelative($url){

			$url = str_replace(GlobalsUC::$url_assets, "", $url);

			return($url);
		}


		/**
		 * convert url to path, if wrong path, return null
		 */
		public static function urlToPath($url){

			$urlRelative = self::URLtoRelative($url);
			$path = GlobalsUC::$path_base.$urlRelative;

			if(file_exists($path) == false)
				return(null);

			return($path);
		}


		/**
		 * convert url array to relative
		 */
		public static function arrUrlsToRelative($arrUrls, $isAssets = false){
			if(!is_array($arrUrls))
				return($arrUrls);

			foreach($arrUrls as $key=>$url){
				$arrUrls[$key] = self::URLtoRelative($url, $isAssets);
			}

			return($arrUrls);
		}


		/**
		 * convert url's array to full
		 */
		public static function arrUrlsToFull($arrUrls){
			if(!is_array($arrUrls))
				return($arrUrls);

			foreach($arrUrls as $key=>$url){
				$arrUrls[$key] = self::URLtoFull($url);
			}

			return($arrUrls);
		}


		/**
		 * strip base path part from the path
		 */
		public static function pathToRelative($path, $addDots = true){

		$realpath = realpath($path);
		if(!$realpath)
			return ($path);

		$isDir = is_dir($realpath);

		$len = strlen($realpath);
		$realBase = realpath(GlobalsUC::$path_base);

		if($realBase != "/")
			$relativePath = str_replace($realBase, "", $realpath);
		else
			$relativePath = $realpath;

		//add dots
		if($addDots == true && strlen($relativePath) != strlen($realpath))
			$relativePath = ".." . $relativePath;

		$relativePath = UniteFunctionsUC::pathToUnix($relativePath);

		if($addDots == false)
			$relativePath = ltrim($relativePath, "/");

		//add slash to end
		if($isDir == true)
			$relativePath = UniteFunctionsUC::addPathEndingSlash($relativePath);

		return $relativePath;
	}

	/**
	 * convert relative path to absolute path
	 */
	public static function pathToAbsolute($path){

		$basePath = GlobalsUC::$path_base;
		$basePath = UniteFunctionsUC::pathToUnix($basePath);

		$path = UniteFunctionsUC::pathToUnix($path);

		$realPath = UniteFunctionsUC::realpath($path, false);

		if(!empty($realPath))
			return ($path);

		if(UniteFunctionsUC::isPathUnderBase($path, $basePath)){
			$path = UniteFunctionsUC::pathToUnix($path);

			return ($path);
		}

		$path = $basePath . "/" . $path;
		$path = UniteFunctionsUC::pathToUnix($path);

		return ($path);
	}

	/**
	 * turn path to relative url
	 */
	public static function pathToRelativeUrl($path){

		$path = self::pathToRelative($path, false);

		$url = str_replace('\\', '/', $path);

		//remove starting slash
		$url = ltrim($url, '/');

		return ($url);
	}

	/**
	 * convert path to absolute url
	 */
	public static function pathToFullUrl($path){

		if(empty($path))
			return ("");

		$url = self::pathToRelativeUrl($path);

		$url = self::URLtoFull($url);

		return ($url);
	}

	/**
	 * get details of the image by the image url.
	 */
	public static function getImageDetails($urlImage){

		$info = UniteFunctionsUC::getPathInfo($urlImage);
		$urlDir = UniteFunctionsUC::getVal($info, "dirname");
		if(!empty($urlDir))
			$urlDir = $urlDir . "/";

		$arrInfo = array();
		$arrInfo["url_full"] = GlobalsUC::$url_base . $urlImage;
		$arrInfo["url_dir_image"] = $urlDir;
		$arrInfo["url_dir_thumbs"] = $urlDir . GlobalsUC::DIR_THUMBS . "/";

		$filepath = GlobalsUC::$path_base . urldecode($urlImage);
		$filepath = realpath($filepath);

		$path = dirname($filepath) . "/";
		$pathThumbs = $path . GlobalsUC::DIR_THUMBS . "/";

		$arrInfo["filepath"] = $filepath;
		$arrInfo["path"] = $path;
		$arrInfo["path_thumbs"] = $pathThumbs;

		return ($arrInfo);
	}

	/**
	 * convert title to handle
	 */
	public static function convertTitleToHandle($title, $removeNonAscii = true){

		$handle = strtolower($title);

		$handle = str_replace(array("ä", "Ä"), "a", $handle);
		$handle = str_replace(array("å", "Å"), "a", $handle);
		$handle = str_replace(array("ö", "Ö"), "o", $handle);

		if($removeNonAscii == true){
			// Remove any character that is not alphanumeric, white-space, or a hyphen
			$handle = preg_replace("/[^a-z0-9\s\_]/i", " ", $handle);
		}

		// Replace multiple instances of white-space with a single space
		$handle = preg_replace("/\s\s+/", " ", $handle);
		// Replace all spaces with underscores
		$handle = preg_replace("/\s/", "_", $handle);
		// Replace multiple underscore with a single underscore
		$handle = preg_replace("/\_\_+/", "_", $handle);
		// Remove leading and trailing underscores
		$handle = trim($handle, "_");

		return ($handle);
	}

	/**
	 * convert title to alias
	 */
	public static function convertTitleToAlias($title){

		$handle = self::convertTitleToHandle($title, false);
		$alias = str_replace("_", "-", $handle);

		return ($alias);
	}

	/**
	 * get url handle
	 */
	public static function getUrlHandle($url, $addonName = null){

		$urlNew = HelperUC::URLtoAssetsRelative($url);

		if($urlNew != $url){  //is inside assets
			$urlNew = "uc_assets_" . $urlNew;

			//make handle by file name and size
			$path = self::urlToPath($url);

			if(!empty($path)){
				$arrInfo = pathinfo($path);
				$filename = UniteFunctionsUC::getVal($arrInfo, "basename");
				$filesize = filesize($path);

				$urlNew = "ac_assets_file_" . $filename . "_" . $filesize;
			}
		}else{
			$urlNew = HelperUC::URLtoRelative($url);
			if($urlNew != $url)
				$urlNew = "uc_" . $urlNew;
			else
				$urlNew = $url;
		}

		$url = strtolower($urlNew);
		$url = str_replace("https://", "", $url);
		$url = str_replace("http://", "", $url);

		if(strpos($url, "uc_") !== 0)
			$url = "uc_" . $url;

		$handle = self::convertTitleToHandle($url);

		return ($handle);
	}

	/**
	 * convert shortcode to url assets
	 */
	public static function convertFromUrlAssets($value, $urlAssets){

		if(empty($urlAssets))
			return ($value);

		if(is_string($value) == false)
			return ($value);

		$value = str_replace("[url_assets]/", $urlAssets, $value);
		$value = str_replace("{{url_assets}}/", $urlAssets, $value);

		return ($value);
	}

	/**
	 * if the website is ssl - convert url to ssl
	 */
	public static function urlToSSLCheck($url){

		if(GlobalsUC::$is_ssl == true)
			$url = UniteFunctionsUC::urlToSsl($url);

		return ($url);
	}

	/**
	 * download file given from some url to cache folder
	 * return filepath
	 */
	public static function downloadFileToCacheFolder($urlFile){

		$info = pathinfo($urlFile);
		$filename = UniteFunctionsUC::getVal($info, "basename");
		if(empty($filename))
			UniteFunctionsUC::throwError("no file given");

		$ext = $info["extension"];

		if($ext != "zip")
			UniteFunctionsUC::throwError("wrong file given");

		$pathCache = GlobalsUC::$path_cache;
		UniteFunctionsUC::mkdirValidate($pathCache, "cache folder");

		$pathCacheImport = $pathCache . "import/";
		UniteFunctionsUC::mkdirValidate($pathCacheImport, "cache import folder");

		$filepath = $pathCacheImport . $filename;

		$content = @file_get_contents($urlFile);
		if(empty($content))
			UniteFunctionsUC::throwError("Can't dowonload file from url: $urlFile");

		UniteFunctionsUC::writeFile($content, $filepath);

		return ($filepath);
	}

	/**
	 * get url content
	 */
	public static function getFileContentByUrl($url, $filterExtention = null){

		if(!empty($filterExtention)){
			$info = pathinfo($url);
			$ext = UniteFunctionsUC::getVal($info, "extension");
			$ext = strtolower($ext);

			if($ext != $filterExtention)
				return (null);
		}

		$pathFile = self::urlToPath($url);

		if(empty($pathFile))
			return (null);

		if(file_exists($pathFile) == false)
			return (null);

		$content = @file_get_contents($pathFile);

		return ($content);
	}

 	public static function a________VIEW_TEMPLATE_____(){}

	/**
	 * get ajax url for export
	 */
	public static function getUrlAjax($action, $params = ""){

		$nonce = UniteProviderFunctionsUC::getNonce();

		$url = UniteFunctionsUC::addUrlParams(GlobalsUC::$url_ajax_full, array(
			'action' => GlobalsUC::PLUGIN_NAME . '_ajax_action',
			'client_action' => $action,
			'nonce' => $nonce,
		));

		if(!empty($params))
			$url = UniteFunctionsUC::addUrlParams($url, $params);

		return $url;
	}

	/**
	 *
	 * get url to some view.
	 */
	public static function getViewUrl($viewName, $urlParams = "", $isBlankWindow = false, $isFront = false){

		$params = "view=" . $viewName;

		if(!empty($urlParams))
			$params .= "&" . $urlParams;

		if($isFront == false)
			$link = UniteFunctionsUC::addUrlParams(GlobalsUC::$url_component_admin, $params);
		else
			$link = UniteFunctionsUC::addUrlParams(GlobalsUC::$url_component_client, $params);

		if($isBlankWindow == true)
			$link = UniteProviderFunctionsUC::convertUrlToBlankWindow($link);

		$link = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_MODIFY_URL_VIEW, $link, $viewName, $params, $isBlankWindow, $isFront);

		return ($link);
	}

	/**
	 * get addons view url
	 */
	public static function getViewUrl_Addons($type = ""){

		if(empty($type))
			return self::getViewUrl(GlobalsUC::VIEW_ADDONS_LIST);

		$params = array();
		$type = urldecode($type);
		$params = "addontype=$type";

		$urlView = self::getViewUrl(GlobalsUC::VIEW_ADDONS_LIST, $params);

		return ($urlView);
	}

	/**
	 * get addons view url
	 */
	public static function getViewUrl_Default($params = ""){

		return self::getViewUrl(GlobalsUC::$view_default, $params);
	}

	/**
	 * get addons view url
	 */
	public static function getViewUrl_TemplatesList($params = array(), $layoutType = null){

		$urlParams = "";
		if(!empty($layoutType)){
			$params["layout_type"] = $layoutType;
			$urlParams = "layout_type=" . $layoutType;
		}

		$url = self::getViewUrl(GlobalsUC::VIEW_TEMPLATES_LIST, $urlParams);

		$url = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_URL_TEMPLATES_LIST, $url, $params, $layoutType);

		return ($url);
	}

	/**
	 * get addons view url
	 */
	public static function getViewUrl_LayoutsList($params = array(), $layoutType = ""){

		$urlParams = "";
		if(!empty($layoutType)){
			$params["layout_type"] = $layoutType;
			$urlParams = "layout_type=" . $layoutType;
		}

		$url = self::getViewUrl(GlobalsUC::VIEW_LAYOUTS_LIST, $urlParams);

		$url = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_URL_LAYOUTS_LIST, $url, $params);

		return ($url);
	}

	/**
	 * get some object url
	 */
	private static function getUrlViewObject($view, $objectID, $optParams, $isBlankWindow = false){

		$params = "";
		if(!empty($objectID))
			$params = "id=$objectID";

		if(!empty($optParams)){
			if(!empty($params))
				$params .= "&";

			$params .= $optParams;
		}

		return (self::getViewUrl($view, $params, $isBlankWindow));
	}

	/**
	 * get addons view url
	 */
	public static function getViewUrl_Layout($layoutID = null, $optParams = "", $layoutType = ""){

		if(!empty($layoutType)){
			if(!empty($optParams))
				$optParams .= "&";

			$optParams .= "layout_type=" . $layoutType;
		}

		$url = self::getUrlViewObject(GlobalsUC::VIEW_LAYOUT, $layoutID, $optParams, true);

		return $url;
	}

	/**
	 * get addons view url
	 */
	public static function getViewUrl_Template($templateID = null, $templateType = "", $optParams = ""){

		UniteFunctionsUC::validateNotEmpty($templateType);

		if(!empty($optParams))
			$optParams .= "&";

		$optParams .= "layout_type=" . $templateType;

		return self::getViewUrl_Layout($templateID, $optParams);
	}

	/**
	 * get addons view url
	 */
	public static function getViewUrl_LayoutPreviewTemplate(){

		$urlTemplate = self::getViewUrl_LayoutPreview("[page]", true);

		return ($urlTemplate);
	}

	/**
	 * get addons view url
	 */
	public static function getViewUrl_LayoutPreview($layoutID, $isBlankWindow = false, $addParams = "", $isFront = true){

		if($layoutID == "[page]"){
			$urlPreviewTemplate = UniteProviderFunctionsUC::applyFilters("get_layout_preview_byid", null, $layoutID);
			if(!empty($urlPreviewTemplate))
				return ($urlPreviewTemplate);
		}

		$layoutID = (int)$layoutID;

		UniteFunctionsUC::validateNotEmpty($layoutID, "layout id");

		$urlPreview = null;
		$urlPreview = UniteProviderFunctionsUC::applyFilters("get_layout_preview_byid", $urlPreview, $layoutID);
		if(!empty($urlPreview))
			return ($urlPreview);

		$params = "id=$layoutID";

		if(!empty($addParams))
			$params .= "&" . $addParams;

		$url = self::getViewUrl(GlobalsUC::VIEW_LAYOUT_PREVIEW, $params, $isBlankWindow, $isFront);

		return ($url);
	}

	/**
	 * get layout preview url
	 */
	public static function getUrlLayoutPreviewFront($layoutID, $addParams = null, $outputMode = null){

		//add output mode
		if(!empty($outputMode)){
			if(empty($addParams))
				$addParams = "";

			if(!empty($addParams))
				$addParams .= "&";

			$addParams .= "outputmode=" . $outputMode;
		}

		$url = HelperUC::getViewUrl_LayoutPreview($layoutID, true, $addParams, true);

		$url = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_MODIFY_URL_LAYOUT_PREVIEW_FRONT, $url, $layoutID, $addParams);

		return ($url);
	}

	/**
	 * get addons view url
	 */
	public static function getViewUrl_EditAddon($addonID, $params = "", $hash = ""){

		$addonID = (int)$addonID;

		$strParams = "id={$addonID}";
		if(!empty($params))
			$strParams .= "&" . $params;

		if(!empty($hash))
			$strParams .= "#" . $hash;

		return (self::getViewUrl(GlobalsUC::VIEW_EDIT_ADDON, $strParams));
	}

	/**
	 * get addons view url
	 */
	public static function getViewUrl_TestAddon($addonID, $optParams = ""){

		$params = "id={$addonID}";
		if(!empty($optParams))
			$params .= "&" . $optParams;

		return (self::getViewUrl(GlobalsUC::VIEW_TEST_ADDON, $params));
	}

	/**
	 * get addons view url
	 */
	public static function getViewUrl_AddonDefaults($addonID, $optParams = ""){

		$params = "id={$addonID}";
		if(!empty($optParams))
			$params .= "&" . $optParams;

		return (self::getViewUrl(GlobalsUC::VIEW_ADDON_DEFAULTS, $params));
	}

	/**
	 * get filename title from some url
	 * used to get item title from image url
	 */
	public static function getTitleFromUrl($url, $defaultTitle = "item"){

		$info = pathinfo($url);
		$filename = UniteFunctionsUC::getVal($info, "filename");
		$filename = urldecode($filename);

		$title = $defaultTitle;
		if(!empty($filename))
			$title = $filename;

		return ($title);
	}

	/**
	 * get file path
	 *
	 * @param  $filena
	 */
	private static function getPathFile($filename, $path, $defaultPath, $validateName, $ext = "php"){

		if(empty($path))
			$path = $defaultPath;

		$filepath = $path . $filename . "." . $ext;
		UniteFunctionsUC::validateFilepath($filepath, $validateName);

		return ($filepath);
	}

	/**
	 * require some template from "templates" folder
	 */
	public static function getPathTemplate($templateName, $path = null){

		return self::getPathFile($templateName, $path, GlobalsUC::$pathTemplates, "Template");
	}

	/**
	 * require some template from "templates" folder
	 */
	public static function getPathView($viewName, $path = null){

		return self::getPathFile($viewName, $path, GlobalsUC::$pathViews, "View");
	}

	/**
	 * require some template from "templates" folder
	 */
	public static function getPathViewObject($viewObjectName, $path = null){

		return self::getPathFile($viewObjectName, $path, GlobalsUC::$pathViewsObjects, "View Object");
	}

	/**
	 * get settings path
	 */
	public static function getPathSettings($settingsName, $path = null){

		return self::getPathFile($settingsName, $path, GlobalsUC::$pathSettings, "Settings", "xml");
	}

	/**
	 * get path provider template
	 */
	public static function getPathTemplateProvider($templateName){

		return self::getPathFile($templateName, GlobalsUC::$pathProviderTemplates, "", "Provider Template");
	}

	/**
	 * get path provider view
	 */
	public static function getPathViewProvider($viewName){

		return self::getPathFile($viewName, GlobalsUC::$pathProviderViews, "", "Provider View");
	}

	/**
	 * get font awesome url
	 */
	public static function getUrlFontAwesome($version = null){

		if(empty($version))
			$version = "fa5";

		if($version == "fa4")
			$url = GlobalsUC::$url_assets_libraries . "font-awsome/css/font-awesome.min.css";
		else    //fa5
			$url = GlobalsUC::$url_assets_libraries . "font-awesome5/css/fontawesome-all.min.css";

		return ($url);
	}

	public static function a______SCRIPTS______(){
	}

	/**
	 * put smooth scroll include
	 */
	public static function putSmoothScrollIncludes($putJSInit = false){

		$urlSmoothScroll = GlobalsUC::$url_assets_libraries . "smooth-scroll/smooth-scroll.min.js";
		HelperUC::addScriptAbsoluteUrl($urlSmoothScroll, "smooth-scroll");

		if($putJSInit == false)
			return (false);

		$script = "
				window.addEventListener('load', function(){
					var g_ucSmoothScroll = new SmoothScroll('a[href*=\"#\"]',{speed:1000});
				});
			";

		HelperUC::putCustomScript($script);
	}

	/**
	 * add animations scripts and styles
	 */
	public static function putAnimationIncludes($animateOnly = false){

		if(self::$isPutAnimations == true)
			return (false);

		$urlAnimateCss = GlobalsUC::$url_assets_libraries . "animate/animate.css";
		self::addStyleAbsoluteUrl($urlAnimateCss, "animate");

		if($animateOnly == true)
			return (false);

		UniteProviderFunctionsUC::addAdminJQueryInclude();

		$urlWowJs = GlobalsUC::$url_assets_libraries . "animate/wow.min.js";
		self::addScriptAbsoluteUrl($urlWowJs, "wowjs");

		$script = "jQuery(document).ready(function(){new WOW().init()});";
		UniteProviderFunctionsUC::printCustomScript($script);

		self::$isPutAnimations = true;
	}

	/**
	 *
	 * register script helper function
	 *
	 * @param $scriptFilename
	 */
	public static function addScript($scriptName, $handle = null, $folder = "js", $inFooter = false){

		if($handle == null)
			$handle = GlobalsUC::PLUGIN_NAME . "-" . $scriptName;

		$url = GlobalsUC::$urlPlugin . $folder . "/" . $scriptName . ".js";

		UniteProviderFunctionsUC::addScript($handle, $url, $inFooter);
	}

	/**
	 * register script helper function
	 */
	public static function addScriptAbsoluteUrl($urlScript, $handle, $inFooter = false, $deps = array()){

		UniteProviderFunctionsUC::addScript($handle, $urlScript, $inFooter, $deps);

		if(GlobalsProviderUC::$isInsideEditor == true)
			self::$arrWidgetScripts[$handle] = $urlScript;

	}

	/**
	 *
	 * register script helper function
	 *
	 * @param $scriptFilename
	 */
	public static function addScriptAbsoluteUrl_widget($urlScript, $handle, $inFooter = false){

		if(GlobalsProviderUC::$isInsideEditor == true)
			self::$arrWidgetScripts[$handle] = $urlScript;
		else
			UniteProviderFunctionsUC::addScript($handle, $urlScript, $inFooter);

	}


	/**
	 * add remote script
	 */
	public static function addRemoteControlsScript(){

		UniteProviderFunctionsUC::addAdminJQueryInclude();

		$urlFiltersJS = GlobalsUC::$url_assets_libraries . "remote/ue-remote-controls.js";
		HelperUC::addScriptAbsoluteUrl($urlFiltersJS, "ue_remote_controls");

		$isDebug = HelperUC::hasPermissionsFromQuery("ucremotedebug");

		if($isDebug == true){
			HelperUC::putCustomScript("var ucRemoteDebugEnabled=true;", false, "remote_controls_debug");
		}
	}

	/**
	 *
	 * register style helper function
	 *
	 * @param $styleFilename
	 */
	public static function addStyle($styleName, $handle = null, $folder = "css"){

		if($handle == null)
			$handle = GlobalsUC::PLUGIN_NAME . "-" . $styleName;

		UniteProviderFunctionsUC::addStyle($handle, GlobalsUC::$urlPlugin . $folder . "/" . $styleName . ".css");
	}

	/**
	 * print custom script
	 */
	public static function putCustomScript($script, $hardCoded = false, $putOnceHandle = null){

		UniteProviderFunctionsUC::printCustomScript($script, $hardCoded, false, $putOnceHandle, true);
	}

	/**
	 * put inline style
	 */
	public static function putInlineStyle($css){

		//prevent duplicates
		if(empty($css))
			return (false);

		//allow print style only once
		$hash = md5($css);
		if(isset(self::$arrHashCache[$hash]))
			return (false);
		self::$arrHashCache[$hash] = true;

		UniteProviderFunctionsUC::printCustomStyle($css);
	}

	/**
	 *
	 * register style absolute url helper function
	 */
	public static function addStyleAbsoluteUrl($styleUrl, $handle, $deps = array()){

		UniteProviderFunctionsUC::addStyle($handle, $styleUrl, $deps);
	}

	/**
	 * output system message
	 */
	public static function outputNote($message){

		$message = esc_html($message);

		$message = "system note: <b>&nbsp;&nbsp;&nbsp;&nbsp;" . $message . "</b>";

		?>
		<div class='unite-note'><?php
			echo esc_html($message) ?></div>;
		<?php
	}

	/**
	 * output addon from storred data
	 */
	public static function outputAddonFromData($data){

		$addons = new UniteCreatorAddons();
		$objAddon = $addons->initAddonByData($data);

		$objOutput = new UniteCreatorOutput();
		$objOutput->initByAddon($objAddon);
		$html = $objOutput->getHtmlBody();
		$objOutput->processIncludes();

		echo UniteProviderFunctionsUC::escCombinedHtml($html);
	}

	/**
	 * get error message html
	 */
	public static function getHtmlErrorMessage($message, $trace = "", $prefix = null){

		if(empty($prefix))
			$prefix = HelperUC::getText("addon_library") . " Error: ";

		$message = $prefix . $message;

		$html = self::$operations->getErrorMessageHtml($message, $trace);

		return ($html);
	}

	public static function a______ASSETS_PATH______(){
	}

	/**
	 * get assets path
	 */
	public static function getAssetsPath($objAddonType = null){

		if(empty($objAddonType))
			return (GlobalsUC::$pathAssets);

		if(!empty($objAddonType->pathAssets))
			return ($objAddonType->pathAssets);

		return (GlobalsUC::$pathAssets);
	}

	/**
	 * get assets url according addons type
	 */
	public static function getAssetsUrl($objAddonType = null){

		if(empty($objAddonType))
			return (GlobalsUC::$url_assets);

		if(!empty($objAddonType->urlAssets))
			return ($objAddonType->urlAssets);

		return (GlobalsUC::$url_assets);
	}

	/**
	 * validate that path located under assets folder
	 */
	public static function validatePathUnderAssets($path, $objAddonType = null){

		$isUnderAssets = self::isPathUnderAssetsPath($path, $objAddonType);
		if(!$isUnderAssets)
			UniteFunctionsUC::throwError("The path should be under assets folder");
	}

	/**
	 * validate db tables exists
	 */
	public static function validateDBTablesExists(){

		$db = self::getDB();

		if($db->isTableExists(GlobalsUC::$table_categories . "," . GlobalsUC::$table_addons) == false)
			UniteFunctionsUC::throwError("Some DB table not exists");
	}

	/**
	 * return true if some path under base path
	 */
	public static function isPathUnderAssetsPath($path, $objAddonType = null){

		$assetsPath = self::getAssetsPath($objAddonType);

		$path = self::pathToAbsolute($path);

		$assetsPath = self::pathToAbsolute($assetsPath);

		$isUnderAssets = UniteFunctionsUC::isPathUnderBase($path, $assetsPath);

		return ($isUnderAssets);
	}

	/**
	 * is url under assets
	 */
	public static function isUrlUnderAssets($url, $objAddonType = null){

		$urlAssets = self::getAssetsUrl($objAddonType);

		$url = self::URLtoFull($url);
		$url = strtolower($url);
		if(strpos($url, $urlAssets) !== false)
			return (true);

		return (false);
	}

	/**
	 * check if some path is assets path
	 */
	public static function isPathAssets($path, $objAddonType = null){

		$assetsPath = self::getAssetsPath($objAddonType);

		$assetsPath = self::pathToAbsolute($assetsPath);

		$path = self::pathToAbsolute($path);

		if(!empty($path) && $path === $assetsPath)
			return (true);

		return (false);
	}

	/**
	 * convert path to assets relative path
	 */
	public static function pathToAssetsRelative($path, $objAddonType = null){

		$assetsPath = self::getAssetsPath($objAddonType);

		$assetsPath = self::pathToAbsolute($assetsPath);

		$path = self::pathToAbsolute($path);

		$relativePath = UniteFunctionsUC::pathToRelative($path, $assetsPath);

		return ($relativePath);
	}

	/**
	 * path to assets absolute
	 *
	 * @param $path
	 */
	public static function pathToAssetsAbsolute($path, $objAddonType = null){

		if(self::isPathUnderAssetsPath($path, $objAddonType) == true)
			return ($path);

		$assetsPath = self::getAssetsPath($objAddonType);
		$path = UniteFunctionsUC::joinPaths($assetsPath, $path);

		return ($path);
	}

	public static function a______OUTPUT_LAYOUT______(){
	}

	/**
	 * get default preview image by type
	 */
	public static function getDefaultPreviewImage($typeName){

		$filenameDefaultPreview = "preview_$typeName.jpg";
		$urlPreview = GlobalsUC::$urlPlugin . "images/" . $filenameDefaultPreview;

		return ($urlPreview);
	}

	/**
	 * check if elementor pro active
	 */
	public static function isElementorProActive(){

		if(defined("ELEMENTOR_PRO_VERSION"))
			return (true);

		return (false);
	}

	/**
	 * check if edit mode
	 */
	public static function isElementorEditMode(){

		if(isset($_GET["elementor-preview"]))
			return (true);

		$argPost = UniteFunctionsUC::getPostGetVariable("post", "", UniteFunctionsUC::SANITIZE_KEY);
		$argAction = UniteFunctionsUC::getPostGetVariable("action", "", UniteFunctionsUC::SANITIZE_KEY);

		if($argAction == "elementor_render_widget" || $argAction == "elementor_ajax" || $argAction == "unlimitedelements_ajax_action")
			return (true);

		if(!empty($argPost) && !empty($argAction))
			return (true);

		return (false);
	}



	/**
	 * start buffering widget output
	 */
	public static function startBufferingWidgetsOutput(){

		UniteCreatorOutput::$isBufferingCssActive = true;
	}

	/**
	 * output widgets css buffer
	 */
	public static function outputWidgetsCssBuffer(){

		$htmlCssIncludes = UniteCreatorOutput::$bufferCssIncludes;

		echo "\n";
		echo $htmlCssIncludes;

		$css = UniteCreatorOutput::$bufferBodyCss;
		if(!empty($css)){
			echo "\n<style type='text/css'> \n";
			echo "\n/* Unlimited Elements Css */ \n\n";
			echo $css . "\n";
			echo "</style>\n";
		}

		//clear the buffer
		UniteCreatorOutput::$bufferCssIncludes = "";
		UniteCreatorOutput::$bufferBodyCss = "";
	}

	/**
	 * output template part
	 */
	public static function outputTemplatePart($layoutType){

		try{
			$objLayouts = new UniteCreatorLayouts();
			$objLayout = $objLayouts->getActiveTempaltePart($layoutType);
		}catch(Exception $e){
			HelperHtmlUC::outputException($e);

			return (false);
		}

		if(empty($objLayout)){
			$typeTitle = $objLayouts->getLayoutTypeTitle($layoutType);
			$message = esc_html__("Template part", "unlimited-elements-for-elementor") . ": " . $typeTitle . esc_html__(" not found. Please create one in template parts page", "unlimited-elements-for-elementor");
			$html = HelperHtmlUC::getErrorMessageHtml($message);
			echo UniteProviderFunctionsUC::escCombinedHtml($html);

			return (false);
		}

		self::outputLayout($objLayout);
	}

	/**
	 * output layout
	 */
	public static function outputLayout($layoutID, $getHtml = false, $outputFullPage = false, $mode = null){

		try{
			if(is_numeric($layoutID)){
				$layoutID = UniteProviderFunctionsUC::sanitizeVar($layoutID, UniteFunctionsUC::SANITIZE_ID);

				$layout = new UniteCreatorLayout();
				$layout->initByID($layoutID);
			}else
				$layout = $layoutID;    //if object passed

			$outputLayout = new UniteCreatorLayoutOutput();
			$outputLayout->initByLayout($layout);

			if(!empty($mode))
				$outputLayout->setOutputMode($mode);

			if($getHtml == true){
				if($outputFullPage == false)
					$html = $outputLayout->getHtml();
				else
					$html = $outputLayout->getFullPreviewHtml();

				return ($html);
			}

			if($outputFullPage == false)
				$outputLayout->putHtml();
			else
				$outputLayout->putFullPreviewHtml();

			return ($outputLayout);
		}catch(Exception $e){
			if($getHtml == true){
				throw $e;
			}else
				HelperHtmlUC::outputExceptionBox($e, HelperUC::getText("addon_library") . " Error");
		}
	}

}

//init the operations
HelperUC::$operations = new UCOperations();

?>
