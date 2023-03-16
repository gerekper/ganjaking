<?php

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteProviderFunctionsUC{
	
	private static $arrScripts = array();
	private static $arrStyles = array();
	private static $arrInlineHtml = array();
	public static $tablePrefix = null;
	public static $tablePosts = null;
	public static $tablePostMeta = null;
	public static $counterScripts = 0;
	
	
	/**
	 * init base variables of the globals
	 */
	public static function initGlobalsBase(){
		global $wpdb;
		
		$tablePrefix = $wpdb->prefix;
		
		self::$tablePrefix = $tablePrefix;
		GlobalsUC::$table_prefix = $tablePrefix;
		
		self::$tablePosts = $tablePrefix."posts";
		self::$tablePostMeta = $tablePrefix."postmeta";
				
		GlobalsUC::$table_addons = $tablePrefix.GlobalsUC::TABLE_ADDONS_NAME;
		GlobalsUC::$table_categories = $tablePrefix.GlobalsUC::TABLE_CATEGORIES_NAME;
		
		$pluginUrlAdminBase = GlobalsUC::PLUGIN_NAME;
		
		GlobalsUC::$pathPlugin = realpath(dirname(__FILE__)."/../")."/";
		
		$pluginName = basename(GlobalsUC::$pathPlugin);
		
		GlobalsUC::$path_base = ABSPATH;
				
		GlobalsUC::$pathPlugin = UniteFunctionsUC::pathToUnix(GlobalsUC::$pathPlugin);
		GlobalsUC::$path_base = UniteFunctionsUC::pathToUnix(GlobalsUC::$path_base);
		
		//protection against wrong base path (happends at some hostings subdomain)
		if(strpos(GlobalsUC::$path_base, GlobalsUC::$pathPlugin) === false){
			GlobalsUC::$path_base = realpath(GlobalsUC::$pathPlugin."../../../")."/";			
			GlobalsUC::$path_base = UniteFunctionsUC::pathToUnix(GlobalsUC::$path_base);
		}
				
		$arrUploadDir = wp_upload_dir();
		
		$uploadPath = $arrUploadDir["basedir"]."/";
		
		GlobalsUC::$path_images = $arrUploadDir["basedir"]."/";

		//set cache folder
		
		try{
			
			GlobalsUC::$path_cache = GlobalsUC::$path_images."unlimited_elements_cache/";
			UniteFunctionsUC::mkdirValidate(GlobalsUC::$path_cache, "cache folder");
			
			//create index.html
			UniteFunctionsUC::writeFile("", GlobalsUC::$path_cache."index.html");
			
		}catch(Exception $e){
		
			GlobalsUC::$path_cache = GlobalsUC::$pathPlugin."cache/";
		}
		
		GlobalsUC::$url_base = site_url()."/";
		GlobalsUC::$urlPlugin = plugins_url($pluginName)."/";
		
		GlobalsUC::$url_component_admin = admin_url()."admin.php?page=$pluginUrlAdminBase";
		GlobalsUC::$url_component_client = GlobalsUC::$url_component_admin;
		GlobalsUC::$url_component_admin_nowindow = GlobalsUC::$url_component_admin."&ucwindow=blank";
		
		GlobalsUC::$url_images = $arrUploadDir["baseurl"]."/";
		
		GlobalsUC::$url_ajax = admin_url("admin-ajax.php","relative");
		GlobalsUC::$url_ajax_full = admin_url("admin-ajax.php");
		
		GlobalsUC::$url_ajax_front = GlobalsUC::$url_ajax;
		
		GlobalsUC::$is_admin = self::isAdmin();
		
		GlobalsUC::$url_provider = GlobalsUC::$urlPlugin."provider/";
		
		GlobalsUC::$url_default_addon_icon = GlobalsUC::$url_provider."assets/images/icon_default_addon.png";
		
		GlobalsUC::$is_ssl = is_ssl();
		
		self::setAssetsPath();
		
		GlobalsUC::$url_assets_libraries = GlobalsUC::$urlPlugin."assets_libraries/";
		
		//GlobalsUC::$view_default set in admin class
		
		GlobalsUC::$url_assets_internal = GlobalsUC::$urlPlugin."assets_internal/";
		
		GlobalsUC::$layoutShortcodeName = "blox_layout";
				
		GlobalsUC::$enableWebCatalog = true;
		
		$window = UniteFunctionsUC::getGetVar("ucwindow","",UniteFunctionsUC::SANITIZE_KEY);
		if($window === "blank")
			GlobalsUC::$blankWindowMode = true;
		
	}
	
	
	/**
	 * set assets path
	*/
	public static function setAssetsPath($dirAssets = null, $returnValues = false){
		
		if(empty($dirAssets))
			$dirAssets = "ac_assets";
		
		$arrUploads = wp_upload_dir();
		
		
		$uploadsBaseDir = UniteFunctionsUC::getVal($arrUploads, "basedir");
		$uploadsBaseUrl = UniteFunctionsUC::getVal($arrUploads, "baseurl");

		//convert to ssl if needed
		if(GlobalsUC::$is_ssl == true)
			$uploadsBaseUrl = str_replace("http://", "https://", $uploadsBaseUrl);
			
		
		$urlBase = null;
		if(is_dir($uploadsBaseDir)){
			$pathBase = UniteFunctionsUC::addPathEndingSlash($uploadsBaseDir);
			$urlBase = UniteFunctionsUC::addPathEndingSlash($uploadsBaseUrl);
		}
		
		
		
		
		//make base path
		$pathAssets = $pathBase.$dirAssets."/";
		if(is_dir($pathAssets) == false)
			@mkdir($pathAssets);
		
		if(is_dir($pathAssets) == false)
			UniteFunctionsUC::throwError("Can't create folder: {$pathAssets}");
		
		//--- make url assets
		$urlAssets = $urlBase.$dirAssets."/";
		
		
		if(empty($pathAssets))
			UniteFunctionsUC::throwError("Cannot set assets path");
		
		if(empty($urlAssets))
			UniteFunctionsUC::throwError("Cannot set assets url");
			
		if($returnValues == true){
			
			$arrReturn = array();
			$arrReturn["path_assets"] = $pathAssets;
			$arrReturn["url_assets"] = $urlAssets;
			
			return($arrReturn);
		}else{
			GlobalsUC::$pathAssets = $pathAssets;
			GlobalsUC::$url_assets = $urlAssets;
		}
		
	}
	
	
	
	/**
	 * is admin function
	 */
	public static function isAdmin(){
		
		$isAdmin = is_admin();
		
		return($isAdmin);
	}
	
	public static function a________SCRIPTS_________(){}
	
	
	/**
	 * add scripts and styles framework
	 * $specialSettings - (nojqueryui)
	 */
	public static function addScriptsFramework($specialSettings = ""){
		
		UniteFunctionsWPUC::addMediaUploadIncludes();
		
		//add jquery
		self::addAdminJQueryInclude();
				
		//add jquery ui
		wp_enqueue_script("jquery-ui-core");
		wp_enqueue_script("jquery-ui-widget");
		wp_enqueue_script("jquery-ui-dialog");
		wp_enqueue_script("jquery-ui-resizable");
		wp_enqueue_script("jquery-ui-draggable");
		wp_enqueue_script("jquery-ui-droppable");
		wp_enqueue_script("jquery-ui-position");
		wp_enqueue_script("jquery-ui-selectable");
		wp_enqueue_script("jquery-ui-sortable");
		wp_enqueue_script("jquery-ui-autocomplete");
		
		
		//no jquery ui style
		if($specialSettings != "nojqueryui"){
			HelperUC::addStyle("jquery-ui.structure.min","jui-smoothness-structure","css/jui/new");
			HelperUC::addStyle("jquery-ui.theme.min","jui-smoothness-theme","css/jui/new");
		}
		
		if(function_exists("wp_enqueue_media"))
			wp_enqueue_media();
		
	}
	
	
	/**
	 * add jquery include
	 */
	public static function addAdminJQueryInclude(){
		
		wp_enqueue_script("jquery");
		
	}
	
	
	/**
	 *
	 * register script
	 */
	public static function addScript($handle, $url, $inFooter = false, $deps = array()){
	
		if(empty($url))
			UniteFunctionsUC::throwError("empty script url, handle: $handle");
		
		$version = UNLIMITED_ELEMENTS_VERSION;
		if(GlobalsUC::$inDev == true)	//add script
			$version = time();
		
		wp_register_script($handle , $url, $deps, $version, $inFooter);
		wp_enqueue_script($handle);
	}
	
	
	/**
	 *
	 * register script
	 */
	public static function addStyle($handle, $url){
				
		if(empty($url))
			UniteFunctionsUC::throwError("empty style url, handle: $handle");
		
		$version = UNLIMITED_ELEMENTS_VERSION;
		if(GlobalsUC::$inDev == true)	//add script
			$version = time();
		
		wp_register_style($handle, $url, array(), $version);
		wp_enqueue_style($handle);
			
	}
	
	
	/**
	 * print some script at some place in the page
	 * handle meanwhile inactive
	 */
	public static function printCustomScript($script, $hardCoded = false, $isModule = false, $handle = null){
		
		self::$counterScripts++;
		
		if(empty($handle))
			$handle = "script_".self::$counterScripts;
				
		if($isModule == true)
			$handle = "module_".$handle;
		
		if(isset(self::$arrScripts[$handle]))
			$handle .= "_". UniteFunctionsUC::getRandomString(5, true);
		
		if($hardCoded == false)
			self::$arrScripts[$handle] = $script;
		else{
			if($isModule == true)
				echo "<script type='module' id='{$handle}'>{$script}</script>";
			else 
				echo "<script type='text/javascript' id='{$handle}'>{$script}</script>";
			
		}
	}
	
	
	/**
	 * print custom style
	 */
	public static function printCustomStyle($style, $hardCoded = false){
			    
		if($hardCoded == false)
			self::$arrStyles[] = $style;
		else
			echo "<style type='text/css'>{$style}</style>";
		
	}
	
	
	/**
	* get all custom scrips, delete the scripts array later
	*/
	public static function getCustomScripts(){
		
	    $arrScripts = self::$arrScripts;
	    
	    self::$arrScripts = array();
	    
		return($arrScripts);
	}
	
	
	/**
	 * get custom styles, delete the styles later
	 */
	public static function getCustomStyles(){
		
	    $arrStyles = self::$arrStyles;
	    
	    self::$arrStyles = array();
	    
		return($arrStyles);
	}
	
	
	/**
	 * get url jquery include
	 */
	public static function getUrlJQueryInclude(){
						
		$url = GlobalsUC::$url_base."wp-includes/js/jquery/jquery".".js";
		
		return($url);
	}
	
	/**
	 * get jquery migrate url include
	 */
	public static function getUrlJQueryMigrateInclude(){
		
		$url = GlobalsUC::$url_base."wp-includes/js/jquery/jquery-migrate".".js";
		
		return($url);
	}
	
	
	public static function a_________SANITIZE________(){}
	
	
	/**
	 * filter variable
	 */
	public static function sanitizeVar($var, $type){
		
		switch($type){
			case UniteFunctionsUC::SANITIZE_ID:
				
				if(is_array($var))
					return(null);
				
				if(empty($var))
					return("");
				
				$var = (int)$var;
				$var = abs($var);
	
				if($var == 0)
					return("");
			
			break;
			case UniteFunctionsUC::SANITIZE_KEY:
				
				if(is_array($var))
					return(null);
				
				$var = sanitize_key($var);
			break;
			case UniteFunctionsUC::SANITIZE_TEXT_FIELD:
				$var = sanitize_text_field($var);
			break;
			case UniteFunctionsUC::SANITIZE_NOTHING:
			break;
			default:
				UniteFunctionsUC::throwError("Wrong sanitize type: " . $type);
			break;
		}
	
		return($var);
	}
	
	/**
	 * escape add html
	 */
	public static function escAddParam($html){
		
		return($html);
	}
	
	/**
	 * escape add html
	 */
	public static function escCombinedHtml($html){
		
		return($html);
	}
	
	/**
	 * escape html
	 */
	public static function escHtml($html){
		
		$html = esc_html($html);
		
		return($html);
	}
	
	public static function a_________GENERAL_________(){}
		
	
	
	/**
	 * get image url from image id
	 */
	public static function getImageUrlFromImageID($imageID){
		
		$urlImage = UniteFunctionsWPUC::getUrlAttachmentImage($imageID);
		
		return($urlImage);
	}
	
	
	/**
	 * get image url from image id
	 */
	public static function getThumbUrlFromImageID($imageID, $size = null){
		
		if(empty($imageID))
			return("");
				
		if($size == null)
			$size = UniteFunctionsWPUC::THUMB_MEDIUM;
				
		switch($size){
			case GlobalsUC::THUMB_SIZE_NORMAL:
				$size = UniteFunctionsWPUC::THUMB_MEDIUM;
			break;
			case GlobalsUC::THUMB_SIZE_LARGE:
				$size = UniteFunctionsWPUC::THUMB_LARGE;
			break;
		}
		
		$urlThumb = UniteFunctionsWPUC::getUrlAttachmentImage($imageID, $size);
		
		return($urlThumb);
	}
	
	/**
	 * get image id from url
	 * if not, return null or 0
	 */
	public static function getImageIDFromUrl($urlImage){
		
		$imageID = UniteFunctionsWPUC::getAttachmentIDFromImageUrl($urlImage);
		
		return($imageID);
	}
	
	
	/**
	 * strip slashes from ajax input data
	 */
	public static function normalizeAjaxInputData($arrData){
		
		if(!is_array($arrData))
			return($arrData);
		
		foreach($arrData as $key=>$item){
			
			if(is_string($item))
				$arrData[$key] = stripslashes($item);
			
			//second level
			if(is_array($item)){
				
				foreach($item as $subkey=>$subitem){
					if(is_string($subitem))
						$arrData[$key][$subkey] = stripslashes($subitem);
					
					//third level
					if(is_array($subitem)){

						foreach($subitem as $thirdkey=>$thirdItem){
							if(is_string($thirdItem))
								$arrData[$key][$subkey][$thirdkey] = stripslashes($thirdItem);
						}
					
					}
					
				}
			}
			
		}
		
		return($arrData);
	}
	
	
	/**
	 * put footer text line
	 */
	public static function putFooterTextLine(){
		?>
			&copy; <?php esc_html_e("All rights reserved","unlimited-elements-for-elementor")?>, <a href="https://unlimited-elements.com" target="_blank">Unlimited Elements</a>. &nbsp;&nbsp;
		<?php
	}
	
	
	/**
	 * add jquery include
	 */
	public static function addjQueryInclude($app="", $urljQuery = null){
				
		wp_enqueue_script("jquery");
	}

		
	
	/**
	 * print some custom html to the page
	 */
	public static function printInlineHtml($html){
		self::$arrInlineHtml[] = $html;
	}
	
	
	/**
	 * get custom html
	 */
	public static function getInlineHtml(){
		
		return(self::$arrInlineHtml);
	}
	
	
	/**
	 * add system contsant data to template engine
	 */
	public static function addSystemConstantData($data){
		
		$data["uc_url_home"] = get_home_url();
		$data["uc_url_blog"] = UniteFunctionsWPUC::getUrlBlog();
		
		$isWPMLExists = UniteCreatorWpmlIntegrate::isWpmlExists();
		if($isWPMLExists == true){
			
			$objWpml = new UniteCreatorWpmlIntegrate();
			$activeLanguage = $objWpml->getActiveLanguage();
			
			$data["uc_lang"] = $activeLanguage;
		}else{
							
			$data["uc_lang"] = UniteFunctionsWPUC::getLanguage();
		}
		
    	
		$isInsideEditor = UniteCreatorElementorIntegrate::$isEditMode;
				
		$data["uc_inside_editor"] = $isInsideEditor?"yes":"no";
		
		
		return($data);
	}
	
	
	
	/**
	 * put addon view add html
	 */
	public static function putAddonViewAddHtml(){
		//put nothing meanwhile
	}
	

	
	
	/**
	 * get nonce (for protection)
	 */
	public static function getNonce(){
		
		$nonce = wp_create_nonce(GlobalsUC::PLUGIN_NAME."_actions");
		
		return($nonce);
	}
	
	
	/**
	 * veryfy nonce
	 */
	public static function verifyNonce($nonce){
		
		
		if(function_exists("wp_verify_nonce") == false){
			
			dmp("verify nonce function not found. some other plugin interrupting this call");
			dmp("please find it in this trace by follow 'wp-content/plugins'");
			
			UniteFunctionsUC::showTrace();
			exit();
		}
		
		
		$verified = wp_verify_nonce($nonce, GlobalsUC::PLUGIN_NAME."_actions");
		if($verified == false)
			UniteFunctionsUC::throwError("Action security failed, please refresh the page and try again.");
		
	}
	
	
	/**
	 * put helper editor to help init other editors that has put by ajax
	 */
	public static function putInitHelperHtmlEditor($unhide = false){
		
		$style = "display:none";
		if($unhide == true)
			$style = "";
		
		
		?>
		<div style="<?php echo esc_attr($style)?>">
			
			<?php 
				wp_editor("init helper editor","uc_editor_helper");
			?>
			
		</div>
		<?php 
		
	}
	
	/**
	 * send email, throw error on fail
	 */
	public static function sendEmail($emailTo, $subject, $message){
		
		$isSent = wp_mail( $emailTo, $subject, $message);
		if($isSent == false)
			UniteFunctionsUC::throwError("The mail is not sent");	
			
		//TODO: return real message
	}
	
	
	/**
	 * set admin title
	 */
	public static function setAdminTitle($title){
		
		if(GlobalsUC::$is_admin == false)
			UniteFunctionsUC::throwError("The function works only in admin area");
		
		UniteProviderAdminUC::$adminTitle = $title;
	}
	
	/**
	 * set admin page title
	 */
	public static function setAdminPageTitle($title){
		
	}
	
	/**
	 * get post title by ID
	 */
	public static function getPostTitleByID($postID){
		
		$post = get_post($postID);
		if(empty($post))
			return("");
		
		$title = $post->post_title;
		
		return($title);
	}
	
	private static function a_________OPTIONS_________(){}
	
	
	/**
	 * get option
	 */
	public static function getOption($option, $default = false, $supportMultisite = false){
	
		if($supportMultisite == true && is_multisite())
			return(get_site_option($option, $default));
		else
			return get_option($option, $default);
	
	}
	
	/**
	 * get option
	 */
	public static function getTransient($option, $supportMultisite = false){
	    
		if($supportMultisite == true && is_multisite())
			return(get_site_transient($option));
		else
			return get_transient($option);
	
	}
	
	/**
	 * delete option
	 */
	public static function deleteOption($option, $supportMultisite = false){
		
		if($supportMultisite == true && is_multisite()){
			delete_site_option($option);
		}else
			delete_option($option);
		
	}
	
	/**
	 * update option
	 */
	public static function updateOption($option, $value, $supportMultisite = false){
	
		if($supportMultisite == true && is_multisite()){
			update_site_option($option, $value);
		}else
			update_option($option, $value);
	
	}
	
	
	
	/**
	 * update option
	 */
	public static function setTransient($option, $value, $expiration, $supportMultisite = false){
	
		if($supportMultisite == true && is_multisite()){
			set_site_transient($transient, $value, $expiration);
		}else
			set_transient($option, $value, $expiration);
			
	}
	
	private static function a_________UPDATE_PLUGIN________(){}
	
	
	/**
	 * put update plugin button
	 */
	public static function putUpdatePluginHtml($pluginName, $pluginTitle = null){
		
		$postMaxSize = ini_get( "post_max_size");
		$maxUploadSize = ini_get( "upload_max_filesize");
		
		if(empty($pluginTitle))
			$pluginTitle = esc_html__("Unlimited Elements Plugin", "unlimited-elements-for-elementor");
		else
			$pluginTitle .= " Plugin";
		
		$nonce = self::getNonce();
		
		?>
		<!-- update plugin button -->
		
		<div class="uc-update-plugin-wrapper">
			<a id="uc_button_update_plugin" class="unite-button-primary" href="javascript:void(0)" ><?php esc_html_e("Update Plugin", "unlimited-elements-for-elementor")?></a>
		</div>
		
		<!-- dialog update -->
		
		<div id="dialog_update_plugin" title="<?php esc_html_e("Update ","unlimited-elements-for-elementor")?> <?php echo esc_attr($pluginTitle)?>" style="display:none;">	
			
			<!--  
			<div class="unite-dialog-title"><?php esc_html_e("Update ","unlimited-elements-for-elementor")?> <?php echo esc_html($pluginTitle)?>:</div>	
			-->
			
			<div class="unite-dialog-desc">
				<?php esc_html_e("To update the plugin please select the plugin install package.","unlimited-elements-for-elementor") ?>		
			<br>
		
			<?php esc_html_e("The files will be overwriten", "unlimited-elements-for-elementor")?>
		
			<br> <?php esc_html_e("File example: unlimited-elements0.x.x.zip","unlimited-elements-for-elementor")?>	
				
				<br>
				<br>
				<?php esc_html_e("Post Max Size")?>: <?php echo esc_html($postMaxSize)?>	
				<br>
				<?php esc_html_e("Max Upload Size")?>: <?php echo esc_html($maxUploadSize)?>
				<br>
				<?php esc_html_e("You can change those settings in php.ini or contact your hosting provider")?>
			</div>	
						
			<br>	
		
			<form action="<?php echo GlobalsUC::$url_ajax?>" enctype="multipart/form-data" method="post">
				
				<input type="hidden" name="action" value="<?php echo esc_attr($pluginName)?>_ajax_action">		
				<input type="hidden" name="client_action" value="update_plugin">		
				<input type="hidden" name="nonce" value="<?php echo esc_attr($nonce) ?>">
				<?php esc_html_e("Choose the update file:","unlimited-elements-for-elementor")?>
				<br><br>
				
				<input type="file" name="update_file" class="unite-dialog-fileinput">		
				
				<br><br>
			
				<input type="submit" class='unite-button-primary' value="<?php esc_html_e("Update Plugin","unlimited-elements-for-elementor")?>">	
			</form>
			
		</div>

		<?php 
	}
	
	
	/**
	 * check that inner zip exists, and unpack it if do
	 	*/
	private static function updatePlugin_checkUnpackInnerZip($pathUpdate, $zipFilename){
	
		$arrFiles = UniteFunctionsUC::getFileList($pathUpdate);
	
		if(empty($arrFiles))
			return(false);
	
		//get inner file
		$filenameInner = null;
		foreach($arrFiles as $innerFile){
			if($innerFile != $zipFilename)
				$filenameInner = $innerFile;
		}
	
		if(empty($filenameInner))
			return(false);
	
		//check if internal file is zip
		$info = pathinfo($filenameInner);
		$ext = UniteFunctionsUC::getVal($info, "extension");
		if($ext != "zip")
			return(false);
	
		$filepathInner = $pathUpdate.$filenameInner;
	
		if(file_exists($filepathInner) == false)
			return(false);
	
		dmp("detected inner zip file. unpacking...");
	
		//check if zip exists
		$zip = new UniteZipUG();
	
		if(function_exists("unzip_file") == true){
			WP_Filesystem();
			$response = unzip_file($filepathInner, $pathUpdate);
		}
		else
			$zip->extract($filepathInner, $pathUpdate);
	
	}
	
	
	// --------- uploaded file code to message
/**
 * 
 * get message of upload file code
 */
  private static function uploadFileCodeToMessage($code) 
    { 
        switch ($code) { 
            case UPLOAD_ERR_INI_SIZE: 
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini"; 
                break; 
            case UPLOAD_ERR_FORM_SIZE: 
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break; 
            case UPLOAD_ERR_PARTIAL: 
                $message = "The uploaded file was only partially uploaded"; 
                break; 
            case UPLOAD_ERR_NO_FILE: 
                $message = "No file was uploaded"; 
                break; 
            case UPLOAD_ERR_NO_TMP_DIR: 
                $message = "Missing a temporary folder"; 
                break; 
            case UPLOAD_ERR_CANT_WRITE: 
                $message = "Failed to write file to disk"; 
                break; 
            case UPLOAD_ERR_EXTENSION: 
                $message = "File upload stopped by extension"; 
                break; 

            default: 
                $message = "Unknown upload error"; 
                break; 
        } 
        return $message; 
    } 	
	
	
	/**
	 *
	 * Update Plugin
	 */
	public static function updatePlugin(){
		
		$linkBack = HelperUC::getViewUrl_Default();
		$htmlLinkBack = HelperHtmlUC::getHtmlLink($linkBack, "Go Back");
		
		try{
			
			//verify nonce:
			$nonce = UniteFunctionsUC::getPostVariable("nonce","",UniteFunctionsUC::SANITIZE_NOTHING);
			self::verifyNonce($nonce);
			
			$linkBack = HelperUC::getViewUrl_Default("provider_action=run_after_update");
			$htmlLinkBack = HelperHtmlUC::getHtmlLink($linkBack, "Go Back");
			
			//check if zip exists
			$zip = new UniteZipUC();
			
			if(function_exists("unzip_file") == false){
	
				if( UniteZipUG::isZipExists() == false)
					UniteFunctionsUC::throwError("The ZipArchive php extension not exists, can't extract the update file. Please turn it on in php ini.");
			}
						
			dmp("Update in progress...");
			
			$arrFiles = UniteFunctionsUC::getVal($_FILES, "update_file");
			
			if(empty($arrFiles))
				UniteFunctionsUC::throwError("Update file don't found.");
			
			$error = UniteFunctionsUC::getVal($arrFiles, "error");
			if(!empty($error)){
				$message = self::uploadFileCodeToMessage($error);
				UniteFunctionsUC::throwError($message);
			}
				
			$filename = UniteFunctionsUC::getVal($arrFiles, "name");
	
			if(empty($filename))
				UniteFunctionsIG::throwError("Update filename not found.");
	
			$fileType = UniteFunctionsUC::getVal($arrFiles, "type");
	
			$fileType = strtolower($fileType);
	
			$arrMimeTypes = array();
			$arrMimeTypes[] = "application/zip";
			$arrMimeTypes[] = "application/x-zip";
			$arrMimeTypes[] = "application/x-zip-compressed";
			$arrMimeTypes[] = "application/octet-stream";
			$arrMimeTypes[] = "application/x-compress";
			$arrMimeTypes[] = "application/x-compressed";
			$arrMimeTypes[] = "multipart/x-zip";
	
			if(in_array($fileType, $arrMimeTypes) == false)
				UniteFunctionsUC::throwError("The file uploaded is not zip.");
	
			$filepathTemp = UniteFunctionsUC::getVal($arrFiles, "tmp_name");
			if(file_exists($filepathTemp) == false)
				UniteFunctionsUC::throwError("Can't find the uploaded file.");
			
			
			//crate temp folder
			$pathTemp = GlobalsUC::$pathPlugin."temp/";
			UniteFunctionsUC::checkCreateDir($pathTemp);
			
			//create the update folder
			$pathUpdate = $pathTemp."update_extract/";
			UniteFunctionsUC::checkCreateDir($pathUpdate);
						
			if(!is_dir($pathUpdate))
				UniteFunctionsUC::throwError("Could not create temp extract path");
						
			//remove all files in the update folder
			$arrNotDeleted = UniteFunctionsUC::deleteDir($pathUpdate, false);
	
			if(!empty($arrNotDeleted)){
				$strNotDeleted = print_r($arrNotDeleted,true);
				UniteFunctionsUC::throwError("Could not delete those files from the update folder: $strNotDeleted");
			}
						
			//copy the zip file.
			$filepathZip = $pathUpdate.$filename;
	
			$success = move_uploaded_file($filepathTemp, $filepathZip);
			if($success == false)
				UniteFunctionsUC::throwError("Can't move the uploaded file here: ".$filepathZip.".");
						
			//extract files:
			if(function_exists("unzip_file") == true){
				WP_Filesystem();
				$response = unzip_file($filepathZip, $pathUpdate);
			}
			else
				$zip->extract($filepathZip, $pathUpdate);
				
			//check for internal zip in case that cocecanyon original zip was uploaded
			self::updatePlugin_checkUnpackInnerZip($pathUpdate, $filename);
						
			//get extracted folder
			$arrFolders = UniteFunctionsUC::getDirList($pathUpdate);
			if(empty($arrFolders))
				UniteFunctionsUC::throwError("The update folder is not extracted");
	
			//get product folder
			$productFolder = null;
	
			if(count($arrFolders) == 1)
				$productFolder = $arrFolders[0];
			else{
				foreach($arrFolders as $folder){
					if($folder != "documentation")
						$productFolder = $folder;
				}
			}
				
			if(empty($productFolder))
				UniteFunctionsUC::throwError("Wrong product folder.");
	
			$pathUpdateProduct = $pathUpdate.$productFolder."/";
			
			//check some file in folder to validate it's the real one:
			$checkFilepath = $pathUpdateProduct."unitecreator_admin.php";
			
			if(file_exists($checkFilepath) == false)
				UniteFunctionsUC::throwError("Wrong update extracted folder. The file: ".$checkFilepath." not found.");
	
			//copy the plugin without the captions file.
			$pathOriginalPlugin = GlobalsUC::$pathPlugin;
	
			$arrBlackList = array();
			UniteFunctionsUC::copyDir($pathUpdateProduct, $pathOriginalPlugin,"",$arrBlackList);
	
			//delete the update
			UniteFunctionsUC::deleteDir($pathUpdate);
			
			dmp("Updated Successfully, redirecting...");
			echo "<script>location.href='$linkBack'</script>";
	
	}catch(Exception $e){
	
		//remove all files in the update folder
		if(isset($pathUpdate) && !empty($pathUpdate))
			UniteFunctionsUC::deleteDir($pathUpdate);
		
		$message = $e->getMessage();
		$message .= " <br> Please update the plugin manually via the ftp";
		echo "<div style='color:#B80A0A;font-size:18px;'><b>Update Error: </b> $message</div><br>";
		echo UniteProviderFunctionsUC::escCombinedHtml($htmlLinkBack);
		exit();
	}
	
	}
	
	
	
	
	public static function a________ACTIONS_FILTERS_______(){}
	
	
	/**
	 * add filter
	 */
	public static function addFilter($tag, $function_to_add, $priority = 10, $accepted_args = 1 ){
		add_filter($tag, $function_to_add, $priority, $accepted_args);
	}
	
	
	/**
	 * wrap shortcode
	 */
	public static function wrapShortcode($shortcode){
		$shortcode = "[".$shortcode."]";
		return($shortcode);
	}
	
	
	/**
	 * apply filters
	 */
	public static function applyFilters($func, $value){
		$args = func_get_args();
		
		return call_user_func_array("apply_filters",$args);
	}
	
	
	/**
	 * add action function
	 */
	public static function addAction($action, $func){
		$args = func_get_args();
		
		call_user_func_array("add_action", $args);
	}

	
	/**
	 * convert url to new window
	 */
	public static function convertUrlToBlankWindow($url){
		$params = "ucwindow=blank";
		
		$url = UniteFunctionsUC::addUrlParams($url, $params);
		
		return($url);
	}

	
	/**
	 * do action
	 */
	public static function doAction($tag){
		$args = func_get_args();
		
		call_user_func_array("do_action", $args);
	}
		
	
	
}
?>