<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorAssetsWork extends UniteCreatorAssets{
	
	private $pathAssetsBase;
	
	
	/**
	 * init includes asset type
	 */
	private function initIncludesType($objAddon){
		
		$this->setOption(self::OPTION_ID, "uc_includes_browser");
		$this->setOption(self::OPTION_CHECKBOX_ON_TYPES, array("css","js"));
		
		$this->setBrowserMode();
		$this->setStartPath($this->pathAssetsBase);
		
	}
	
	
	/**
	 * init folder browser type
	 */
	private function initFolderBrowserType(){
		
		$this->setBrowserMode();
		
		$this->setStartPath($this->pathAssetsBase);
		
		$this->setOption(self::OPTION_PUT_ACTIVEPATH, false);
		$this->setOption(self::OPTION_FOLDERS_ONLY, true);
		$this->setOption(self::OPTION_SINGLE_ITEM_SELECT, true);
		$this->setOption(self::OPTION_FILTER_FOLDER_NAMES, array(GlobalsUC::DIR_THUMBS));
	}
	
	
	/**
	 * set image and audio types common features
	 */
	private function setImageAudioBrowserCommon(){
		
		$this->setStartPath($this->pathAssetsBase);
		$this->setBrowserMode();
		
		$this->setOption(self::OPTION_PUT_ACTIVEPATH, true);
		$this->setOption(self::OPTION_SINGLE_ITEM_SELECT, true);
		$this->setOption(self::OPTION_DISABLE_CHECKBOXES, true);
		$this->setOption(self::OPTION_FILTER_FOLDER_NAMES, array(GlobalsUC::DIR_THUMBS, GlobalsUC::DIR_THUMBS_ELFINDER));
		
	}
	
	
	/**
	 * init image browser type
	 */
	private function initImageBrowserType(){
		
		$this->setImageAudioBrowserCommon();
		
		$this->setOption(self::OPTION_SHOW_ONLY_TYPES, array(self::FILETYPE_IMAGE));
		$this->setOption(self::OPTION_THUMBS_VIEW, true);
		$this->setOption(self::OPTION_SHOW_FILE_EXTENTIONS, false);
		
	}

	
	/**
	 * init image browser type
	 */
	private function initAudioBrowserType(){
				
		$this->setImageAudioBrowserCommon();
		
		$this->setOption(self::OPTION_SHOW_ONLY_TYPES, array(self::FILETYPE_AUDIO));
		$this->setOption(self::OPTION_THUMBS_VIEW, false);
		$this->setOption(self::OPTION_SHOW_FILE_EXTENTIONS, true);
		
	}
	
	
	
	/**
	 * init assets manager
	 */
	private function initAssetsManager(){
		
		$this->setStartPath($this->pathAssetsBase);
		$this->setOption(self::OPTION_ID, "uc_assets_manager");
		$this->setOption(self::OPTION_FILTER_FOLDER_NAMES, array(GlobalsUC::DIR_THUMBS));
		
	}
	
	
	/**
	 * init the assets manager by key
	 * each key has it's own options
	 */
	public function initByKey($key, $objAddon = null){
		
		//set assets path
		$this->pathAssetsBase = GlobalsUC::$pathAssets;
		if(!empty($objAddon)){
			$this->objAddon = $objAddon;
			$this->pathAssetsBase = $objAddon->getPathAssetsBase();
		}
		
		if(empty($key))
			return(false);
		
		$this->setDefaultOptions();
		
		switch($key){
			case "includes":
				$this->initIncludesType($objAddon);
			break;
			case "assets_manager":
				$this->initAssetsManager();
			break;
			case "folder_browser":
				$this->initFolderBrowserType();
			break;
			case "image_browser":
				$this->initImageBrowserType();
			break;
			case "audio_browser":
				$this->initAudioBrowserType();
			break;
			default:
				UniteFunctionsUC::throwError("Wrong manager key: $key");
			break;
		}
		
		
		$this->pathKey = $key;
	}
	
	
	/**
	 * set path key from data
	 */
	private function initFromData($data){
				
		$key = UniteFunctionsUC::getVal($data, "pathkey");
		$addonID = UniteFunctionsUC::getVal($data, "addonID");
		
		if(!empty($addonID)){
			$this->objAddon = new UniteCreatorAddon();
			$this->objAddon->initByID($addonID);
		}
		
		$this->initByKey($key, $this->objAddon);
	}
	
	
	/**
	 * get options for client
	 */
	protected function getArrOptionsForClient(){
		
		$arrOptions = parent::getArrOptionsForClient();
		
		if(!empty($this->objAddon)){
			
			$addonID = $this->objAddon->getID();
			$arrOptions["addon_id"] = $addonID;
		}
				
		return($arrOptions);
	}
	
	
	/**
	 * get filelist from data
	 */
	public function getHtmlFilelistFromData($data){
	
		$path = UniteFunctionsUC::getVal($data, "path");
		$startPath = UniteFunctionsUC::getVal($data, "startpath");
		
		if(!empty($startPath))
			$this->setCustomStartPath($startPath);
		
		$html = $this->getHtmlDir($path);
	
		return($html);
	}
	
	
	/**
	 * delete files
	 */
	public function deleteFilesFromData($data){
	
		$path = UniteFunctionsUC::getVal($data, "path");
		
		$arrFiles = UniteFunctionsUC::getVal($data, "arrFiles");
		
		$this->deleteFiles($path, $arrFiles);
		
		$html = $this->getHtmlDir($path);
		
		return($html);
	}
	
	
	/**
	 * handle upload files
	 */
	public function handleUploadFiles(){
		
		//get upload path
		$uploadPath = UniteFunctionsUC::getPostVariable("upload_path","",UniteFunctionsUC::SANITIZE_NOTHING);
		$uploadPath = $this->sanitizePath($uploadPath);
		
		if(empty($uploadPath))
			UniteFunctionsUC::throwError("Empty upload path");
		
		//move uploaded file
		$arrFile = UniteFunctionsUC::getVal($_FILES, "file");
		
		if(empty($arrFile))
			UniteFunctionsUC::throwError("No uploaded files found");
		
		$this->handleUploadFile($uploadPath, $arrFile);
	}
	
	
	/**
	 * create folder from data
	 */
	private function createFolderFromData($data){
				
		$path = UniteFunctionsUC::getVal($data, "path");
		$folderName = UniteFunctionsUC::getVal($data, "folder_name");
		
		$this->createFolder($path, $folderName);
		
		$html = $this->getHtmlDir($path);
		
		return($html);
	}
	
	
	/**
	 * create folder from data
	 */
	private function createFileFromData($data){
	
		$path = UniteFunctionsUC::getVal($data, "path");
		$filename = UniteFunctionsUC::getVal($data, "filename");
		
		$this->createFile($path, $filename);
	
		$html = $this->getHtmlDir($path);
	
		return($html);
	}
	
	
	/**
	 * rename file from data
	 */
	private function renameFileFromData($data){
		
		$path = UniteFunctionsUC::getVal($data, "path");
		$filename = UniteFunctionsUC::getVal($data, "filename");
		$filenameNew = UniteFunctionsUC::getVal($data, "filename_new");
		
		$this->renameFile($path, $filename, $filenameNew);
		
		$html = $this->getHtmlDir($path);
		
		return($html);
	}
	
	
	/**
	 * get content from data
	 * $data
	 */
	function getContentFromData($data){
		
		$path = UniteFunctionsUC::getVal($data, "path");
		$filename = UniteFunctionsUC::getVal($data, "filename");
		
		$content = $this->getFileContent($path, $filename);
		
		return($content);
	}
	
	
	/**
	 * save file from data
	 */
	private function saveFileFromData($data){
		
		$path = UniteFunctionsUC::getVal($data, "path");
		$filename = UniteFunctionsUC::getVal($data, "filename");
		$content = UniteFunctionsUC::getVal($data, "content");
		
		$this->saveFileContent($path, $filename, $content);
		
	}
	
	
	/**
	 * move files from data
	 * @param $data
	 */
	private function moveFilesFromData($data){
		
		$pathSource = UniteFunctionsUC::getVal($data, "pathSource");
		$arrFiles = UniteFunctionsUC::getVal($data, "arrFiles");
		$pathTarget = UniteFunctionsUC::getVal($data, "pathTarget");
		$actionOnExists = UniteFunctionsUC::getVal($data, "actionOnExists");
		
		$message = $this->moveFiles($pathSource, $arrFiles, $pathTarget, $actionOnExists);
		
		if(!empty($message)){
			$response = array("done"=>false,"message"=>$message);
		}
		else{
			$html = $this->getHtmlDir($pathSource);
			$response["html"] = $html;
			$response["message"] = esc_html__("files moved successfully", "unlimited-elements-for-elementor");
		}
		return($response);
	}

	
	/**
	 * unzip files from data
	 */
	private function unzipFileFromData($data){
		
		$path = UniteFunctionsUC::getVal($data, "path");
		$filename = UniteFunctionsUC::getVal($data, "filename");
		
		$this->unzipFile($path, $filename);
				
		//set checked file
		$this->setCheckedFiles($path, $filename);
		
		$html = $this->getHtmlDir($path);
		return($html);
	}
	
	
	/**
	 * do ajax actions browser related
	 */
	private function checkAjaxActions_browser($action, $data){
		
		switch($action){
			case "assets_get_filelist":
				$this->validateInited();
				
				$htmlFilelist = $this->getHtmlFilelistFromData($data);
				HelperUC::ajaxResponseData(array("html"=>$htmlFilelist));
			break;
			default:
				return(false);
			break;
		}
	
		return(true);
	
	}
	
	
	/**
	 * do ajax actions manager related
	 */
	private function checkAjaxActions_editor($action, $data){

		switch($action){
			case "assets_upload_files":

				HelperProviderUC::verifyAdminPermission();
				
				$this->validateInited();
				
				$this->handleUploadFiles();
			break;
			case "assets_get_filelist":
				$this->validateInited();
				HelperProviderUC::verifyAdminPermission();
				
				$htmlFilelist = $this->getHtmlFilelistFromData($data);
				HelperUC::ajaxResponseData(array("html"=>$htmlFilelist));
			break;
			case "assets_delete_files":
				
				$this->validateInited();
				HelperProviderUC::verifyAdminPermission();
				
				$htmlFilelist = $this->deleteFilesFromData($data);
				HelperUC::ajaxResponseData(array("html"=>$htmlFilelist));
			break;
			case "assets_create_folder":
				$this->validateInited();
				HelperProviderUC::verifyAdminPermission();
				
				$htmlFilelist = $this->createFolderFromData($data);
				HelperUC::ajaxResponseData(array("html"=>$htmlFilelist));
			break;
			case "assets_create_file":
				$this->validateInited();
				HelperProviderUC::verifyAdminPermission();
				
				$htmlFilelist = $this->createFileFromData($data);
				HelperUC::ajaxResponseData(array("html"=>$htmlFilelist));
			break;
			case "assets_rename_file":
				$this->validateInited();
				HelperProviderUC::verifyAdminPermission();
				
				$htmlFilelist = $this->renameFileFromData($data);
				HelperUC::ajaxResponseData(array("html"=>$htmlFilelist));
			break;
			case "assets_get_file_content":
				$this->validateInited();
				HelperProviderUC::verifyAdminPermission();
				
				$content = $this->getContentFromData($data);
				HelperUC::ajaxResponseData(array("content"=>$content));
			break;
			case "assets_save_file":
				$this->validateInited();
				HelperProviderUC::verifyAdminPermission();
				
				$this->saveFileFromData($data);
				HelperUC::ajaxResponseSuccess(esc_html__("File Saved", "unlimited-elements-for-elementor"));
			break;
			case "assets_move_files":
				$this->validateInited();
				HelperProviderUC::verifyAdminPermission();
				
				$response = $this->moveFilesFromData($data);
				HelperUC::ajaxResponseData($response);
			break;
			case "assets_unzip_file":
				$this->validateInited();
				HelperProviderUC::verifyAdminPermission();
				
				$htmlFilelist = $this->unzipFileFromData($data);
				HelperUC::ajaxResponseData(array("html"=>$htmlFilelist));
			break;
			default:
				return(false);
			break;
			
			
		}
		
		return(true);
	}
	
	
	/**
	 * run ajax actions
	 */
	public function checkAjaxActions($action, $data){
				
		$this->initFromData($data);
		
		if($this->isBrowerMode == true){
			return $this->checkAjaxActions_browser($action, $data);
		}else{
			return $this->checkAjaxActions_editor($action, $data);
		}
		
	}
	
	
}