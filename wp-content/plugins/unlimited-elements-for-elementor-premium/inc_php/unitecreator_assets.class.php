<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorAssets{
	
	const OPTION_WRAPPER_STYLE = "wrapper_style";
	const OPTION_ID = "id";
	const OPTION_CHECKBOX_ON_TYPES = "checkboxes_on_types";		//put checkbox only on certain types
	const OPTION_SHOW_ONLY_TYPES = "show_only_types";
	const OPTION_FILTER_FOLDER_NAMES = "filter_folder_names";					//filter some folder names, don't show them
	const OPTION_DISABLE_CHECKBOXES = "disable_checkboxes";
	const OPTION_PUT_ACTIVEPATH = "put_activepath";
	const OPTION_FOLDERS_ONLY = "folders_only";
	const OPTION_SINGLE_ITEM_SELECT = "single_item_select";
	const OPTION_THUMBS_VIEW = "is_thumbs_view";		//list or thumb view
	const OPTION_SHOW_FILE_EXTENTIONS = "show_file_extensions";
	
	
	const FILETYPE_ALLOWED = "allowed";
	const FILETYPE_DEFAULT = "default";
	const FILETYPE_IMAGE = "image";
	const FILETYPE_AUDIO = "audio";
	const FILETYPE_CSS = "css";
	const FILETYPE_PHP = "php";
	const FILETYPE_JS = "js";
	const FILETYPE_HTML = "html";
	const FILETYPE_DOCUMENT = "document";
	const FILETYPE_VIDEO = "video";
	const FILETYPE_ZIP = "zip";
	const FILETYPE_XML = "xml";
	
	
	protected $options = array();
	private static $serial = 0;
	private $startPath = "";
	private $customStartPath = null;
	protected $pathKey;
	protected $arrCheckedUrls = array();
	protected $isBrowerMode = false;
	protected $flagPutOnce = false;
	protected $objAddon = null;
	
	
	/**
	 * construct
	 */
	public function __construct(){
		self::$serial++;
	}
	
	
	/**
	 * set browser mode
	 */
	protected function setBrowserMode(){
		$this->isBrowerMode = true;
	}
	
	
	/**
	 * set default options
	 */
	protected function setDefaultOptions(){
		
		$defaults = array();
		$defaults[self::OPTION_PUT_ACTIVEPATH] = true;
		$defaults[self::OPTION_FOLDERS_ONLY] = false;
		$defaults[self::OPTION_DISABLE_CHECKBOXES] = false;
		$defaults[self::OPTION_SINGLE_ITEM_SELECT] = false;
		$defaults[self::OPTION_THUMBS_VIEW] = false;
		$defaults[self::OPTION_SHOW_FILE_EXTENTIONS] = true;
		
		$this->options = $defaults;
	}
	
	
	
	
	/**
	 * set array of checked urls
	 */
	protected function setCheckedUrls($arrUrls){
		$this->arrCheckedUrls = $arrUrls;
	}
	
	/**
	 * set checked files
	 */
	protected function setCheckedFiles($path, $arrFiles){
		if(empty($arrFiles))
			return(false);
		
		//support single file 
		if(is_array($arrFiles) == false)
			$arrFiles = array($arrFiles);
		
		$this->sanitizePath($path);
		
		//convert files array to urls array
		$urlDir = $this->getUrlDir($path);
		
		$arrUrls = array();
		
		foreach($arrFiles as $file){
			$url = $urlDir.$file;
			$arrUrls[] = $url;
		}
		
		$this->setCheckedUrls($arrUrls);
	}
	
	
	/**
	 * set start path
	 */
	protected function setStartPath($startPath){
		
		$startPath = UniteFunctionsUC::pathToUnix($startPath);
		$this->startPath = $startPath;
	}
	
	
	
	private function a_VALIDATIONS(){}
	
	
	/**
	 * validate inited
	 */
	protected function validateInited(){
		if(empty($this->pathKey))
			UniteFunctionsUC::throwError("assets not inited");
	}
	
	
	/**
	 * validate that path exists
	 */
	private function validateStartPath(){
	
		if(empty($this->startPath))
			UniteFunctionsUC::throwError("Path not inited");
	
	}
	
	
	/**
	 * validate that the path is under assets path
	 */
	private function validatePathUnderStartPath($path){
	
		$this->validateStartPath();
	
		$path = realpath($path);
		$realStartPath = realpath($this->startPath);
		
		$searchPos = strpos($path, $realStartPath);
		if($searchPos !== 0)
			UniteFunctionsUC::throwError("Wrong path, should be under start path only");
	
	}
	
	
	/**
	 * validate folder name
	 */
	private function validateFolderName($folderName){
	
		if(strpbrk($folderName, "\\/?%*:|\"<>") !== FALSE)
			UniteFunctionsUC::throwError("The folder name: $folderName is not valid");
	
	}
	
	
	/**
	 * validate that the filename is valid
	 */
	private function validateFilename($filename){
		
		if(strpbrk($filename, "\\/?%*:|\"<>") !== FALSE)
			UniteFunctionsUC::throwError("The file name: $filename is not valid");
		
	}
	
	
	/**
	 * validate allowed filetype
	 */
	private function validateAllowedFiletype($filename){
		
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$fileType = $this->getFileType($filename);
		
		if(empty($ext))
			UniteFunctionsUC::throwError("Files without extension don't allowed in assets");
		
		switch($fileType){
			case self::FILETYPE_PHP:
			case self::FILETYPE_DEFAULT:
				UniteFunctionsUC::throwError("File <b>$filename</b> type not allowed in assets");
			break;
		}
		
	}
	
	
	/**
	 * validate that the file is allowed for edit by type
	 */
	private function validateFileAllowedForEdit($filename){
		
		$fileType = $this->getFileType($filename);
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		
		if(empty($ext))
			UniteFunctionsUC::throwError("Can't edit files without extension");
		
		switch($fileType){
			case self::FILETYPE_DOCUMENT:
			case self::FILETYPE_HTML:
			case self::FILETYPE_XML:
			case self::FILETYPE_JS:
			case self::FILETYPE_CSS:
			break;
			default:
				UniteFunctionsUC::throwError("File <b>$filename</b> type not allowed in to edit");
			break;
		}
		
	}
	
	/**
	 * validate edit file data
	 */
	protected function validateEditFileData($path, $filename){
		$path = $this->sanitizePath($path);
		$this->validateFilename($filename);
		$this->validateFileAllowedForEdit($filename);
	
		$filepath = $path.$filename;
		
		if(file_exists($filepath) == false)
			UniteFunctionsUC::throwError("The file: $filename not exists.");
	
		return($filepath);
	}
	
	
	
	
	/**
	 * sanitize filename
	 */
	private function isFilenameValidForDelete($filename){
	
		if(strpos($filename, "..") !== false)
			return(false);
	
		if(strpos($filename, "/") !== false)
			return(false);
	
		if(strpos($filename, "\\") !== false)
			return(false);
	
		return(true);
	}
	
	
	/**
	 * validate new file creation
	 */
	private function validateCreateNewFileFolder($path, $filename, $isFile = true){
	
		$path = $this->sanitizePath($path);
	
		//validate if allowed
		if($isFile == true){
			$this->validateFilename($filename);
			$this->validateAllowedFiletype($filename);
		}else{
			$this->validateFolderName($filename);
		}
	
		$filepath = $path.$filename;
	
		//validate if already exists
		if(is_file($filepath) == true)
			UniteFunctionsUC::throwError("file with name <b>$filename</b> already exists");
	
		if(is_dir($filepath) == true)
			UniteFunctionsUC::throwError("folder <b>$filename</b> already exists");
	
		return($filepath);
	}
	
	
	
	private function a_GETTERS(){}

	
	/**
	 * get option by name
	 */
	private function getOption($name, $default = null){
		
		if(array_key_exists($name, $this->options) == false)
			return($default);
		
		$option = $this->options[$name];
		
		return($option);
	}
	
	
	/**
	 * get options for client
	 */
	protected function getArrOptionsForClient(){
		
		$arrOptionNames = array(
				self::OPTION_SINGLE_ITEM_SELECT
		);
		
		$arrOptions = array();
		foreach($arrOptionNames as $option)
			$arrOptions[$option] = $this->getOption($option);
				
		return($arrOptions);
	}
	
	
	/**
	 * get some file mime type
	 */
	private function getFileType($filename){
		
		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		
		switch($ext){
			case "php":
				return self::FILETYPE_PHP;
			break;
			case "jpg":
			case "png":
			case "jpeg":
			case "gif":
				return self::FILETYPE_IMAGE;
			break;
			case "svg":
			case "txt":
			case "doc":
			case "ini":
			case "md":
				return self::FILETYPE_DOCUMENT;
			break;
			case "html":
			case "htm":
				return self::FILETYPE_HTML;
			break;
			case "css":
				return self::FILETYPE_CSS;
			break;
			case "js":
				return(self::FILETYPE_JS);
			break;
			case "avi":
			case "mp4":
			case "ogv":
			case "webm":
				return(self::FILETYPE_VIDEO);
			break;
			case "mp3":
			case "wav":
			case "flac":
			case "ogg":
			case "webm":
				return(self::FILETYPE_AUDIO);
			break;
			case "zip":
				return(self::FILETYPE_ZIP);
			break;
			case "json":		//add allowed filetypes
				return(self::FILETYPE_ALLOWED);
			break;
			case "xml":
				return(self::FILETYPE_XML);
			break;
			default:
				return(self::FILETYPE_DEFAULT);
			break;
		}
		
	}
	
	
	/**
	 * get assets active path
	 */
	private function getActivePath($inputPath = ""){
		
		if($inputPath){
			$path = $inputPath;
			if(is_dir($path) == false)
				$path = $this->startPath;
		}
		else
			$path = $this->startPath;
				
		$path = HelperUC::pathToRelative($path, false);
		
		return($path);
	}
	
	
	/**
	 * get relative startpath
	 */
	private function getStartPathRelative(){
		return HelperUC::pathToRelative($this->startPath, false);
	}
	
	
	/**
	 * check if path is assets path
	 */
	private function isStartPath($path){
		
		if(realpath($path) == realpath($this->startPath))
			return(true);
		
		return(false);
	}

	
	/**
	 * check if path is assets path
	 */
	private function isCustomStartPath($path){
	
		if(empty($this->customStartPath))
			return(false);
		
		if(realpath($path) == realpath($this->customStartPath))
			return(true);
	
		return(false);
	}
	
	
	/**
	 * convert path dir 8to url
	 */
	protected function getUrlDir($pathDir){
		$urlDir = HelperUC::pathToRelativeUrl($pathDir);
		$urlDir = rtrim($urlDir, "/")."/";	//make sure that there is always /
		return($urlDir);
	}
	
	/**
	 * decide if need to put checkbox on file in htmldir
	 */
	private function isCheckboxOnFile($file, $isDir){
		
		if($file == "..")
			return(false);
		
		$arrExt = $this->getOption(self::OPTION_CHECKBOX_ON_TYPES);
		if(empty($arrExt))
			return(true);
		
		//in case of filtering
		if($isDir == true)
			return(false);
		
		$info = pathinfo($file);
		$ext = UniteFunctionsUC::getVal($info, "extension");
		$ext = strtolower($ext);
		
		if(array_search($ext, $arrExt) === false)
			return(false);
		
		return(true);
	}
	
	
	
	
	/**
	 * check if the file needs to show
	 */
	private function isFileToShow($filetype){
						
		$arrTypes = $this->getOption(self::OPTION_SHOW_ONLY_TYPES);
		
		if(empty($arrTypes))
			return(true);
		
		if(array_search($filetype, $arrTypes) !== false)
			return(true);
		
		return(false);
	}
	
	
	/**
	 * check if directory need to show
	 */
	private function isDirToShow($dir){
		
		$arrExcludeDirs = $this->getOption(self::OPTION_FILTER_FOLDER_NAMES);
		if(empty($arrExcludeDirs))
			return(true);
		
		if(array_search($dir, $arrExcludeDirs) !== false)
			return(false);
		
		
		return(true);
	}
	
	
	/**
	 * get filelist class
	 */
	protected function getFilelistClass(){
		
		$isThumbs = $this->getOption(self::OPTION_THUMBS_VIEW);
		if($isThumbs == true)
			$extraClass = " uc-view-thumbs";
		else
			$extraClass = " uc-view-list";
		
		$class = "uc-filelist".$extraClass;
		
		return($class);
	}
	
	
	/**
	 * get empty list
	 */
	protected function getEmptyHtmlDirList(){
		$class = $this->getFilelistClass();
		
		$html = "<div class=\"{$class}\" ></div>";
	
		return($html);
	}
	
	
	
	/**
	 * put folder content html
	 * path - relative path from assets folder
	 */
	protected function getHtmlDir($pathDir = null, $addWrapper = false){
		
		if(empty($pathDir))
			$pathDir = $this->startPath;
		
		try{
			
			$pathDir = $this->sanitizePath($pathDir);
			
		}catch(Exception $e){
			$pathDir = $this->startPath;
		}
		
		
		$isThumbsView = $this->getOption(self::OPTION_THUMBS_VIEW);
		
		$urlDir = $this->getUrlDir($pathDir);
		
		$isStartPath = $this->isStartPath($pathDir);
		
		if($isStartPath == false)
			$isStartPath = $this->isCustomStartPath($pathDir);
		
		$isFoldersOnly = $this->getOption(self::OPTION_FOLDERS_ONLY);
				
		$arrFiles = scandir($pathDir);

		$isDisableCheckboxes = $this->getOption(self::OPTION_DISABLE_CHECKBOXES);
		$isDisableCheckboxes = UniteFunctionsUC::strToBool($isDisableCheckboxes);
		
		$html = "";
		$classFilelist = $this->getFilelistClass();
		
		if($addWrapper == true)
			$html = "<div class=\"{$classFilelist}\" >";
		
		$isNoFiles = (count($arrFiles) == 2);
		
		foreach($arrFiles as $file){
			if($file == ".")
				continue;
			
			//don't show back link on assets path
			if($file == ".." && $isStartPath == true){
				if($isNoFiles == true){
					$emptyText = esc_html__("empty folder", "unlimited-elements-for-elementor");
					$html .= "<div class='uc-filelist-emptytext'>{$emptyText}</div>";
				}
				
				continue;
			}
						
			
			$filepath = $pathDir.$file;
			$fileUrl = $urlDir.$file;
			$isDir = is_dir($filepath);
			
			if($isFoldersOnly && $isDir == false)
				continue;
			
			if($isDir == true)
				$filetype = "dir";
			else
				$filetype = $this->getFileType($file);
			
			//filter files by type
			if($isDir == false){
				$toShowFile = $this->isFileToShow($filetype);
				if($toShowFile == false)
					continue;
			}else {
				$toShowDir = $this->isDirToShow($file);
				if($toShowDir == false)
					continue;
			}
						
			//check if should be checkbox
			$isSelectable = $this->isCheckboxOnFile($file, $isDir);
			$isCheckbox = $isSelectable;
			
			if($isDisableCheckboxes == true)
				$isCheckbox = false;
			
			
			$htmlChecked = "";
			$addClass = "";
			if(!empty($this->arrCheckedUrls) && array_search($fileUrl, $this->arrCheckedUrls) !== false ){
				$htmlChecked = " checked data-initchecked='true'";
				$addClass = " uc-filelist-item-selected";
			}
			
			
			if($isDir == true && $file == ".."){
				$addClass .= " uc-dir-back";
				$isSelectable = false;
			}
			
			if($isSelectable == true)
				$addClass .= " uc-filelist-selectable";
			
			$strFile = htmlspecialchars($file);
			$showFile = $file;
			
			//check maybe not show extensions
			if($isDir == false){
				$showExtensions = $this->getOption(self::OPTION_SHOW_FILE_EXTENTIONS);
				if($showExtensions === false){
					$arrInfo = pathinfo($file);
					$showFile = $arrInfo["filename"];
				}
			}
			
			$html .= "<a class='uc-filelist-item uc-type-{$filetype} {$addClass}' data-type='{$filetype}' data-file='{$strFile}' data-url='{$fileUrl}' >";
			
			if($isDisableCheckboxes == false){
				$html .= "<div class='uc-filelist-checkbox-wrapper'>";
				
				if($isCheckbox == true)
					$html .= "<input type='checkbox' class='uc-filelist-checkbox' {$htmlChecked} data-file='{$strFile}' onfocus='this.blur()'>";
				
				$html .= "</div>";
			}
			
			//add image path to icon if needed
			$iconAddHtml = "";
			if($isThumbsView == true && $filetype == self::FILETYPE_IMAGE){
				$urlThumb = HelperUC::$operations->createThumbs($fileUrl);
				$urlThumb = HelperUC::URLtoFull($urlThumb);
				$urlThumb = htmlspecialchars($urlThumb);
				
				$iconAddHtml = " style=\"background-image:url('{$urlThumb}');\"";
			}
			
			$html .= "<div class='uc-filelist-icon uc-icon-{$filetype}'{$iconAddHtml}></div>";
			$html .= "<div class='uc-filelist-filename'>{$showFile}</div>";
			$html .= "<div class='unite-clear'></div>";
			$html .= "</a>";
		}
		
		if($addWrapper == true)
			$html .= "</div>";
		
		return($html);
	}
	
	
	/**
	 * get arr exists files in current folder
	 */
	private function getArrExistsFiles($path, $arrFiles){
		
		$path = $this->sanitizePath($path);
		
		if(!is_array($arrFiles))
			UniteFunctionsUC::throwError("getArrExistsFiles error - arrFiles should be array");
		
		$arrExists = array();
		foreach($arrFiles as $file){
			$filepath = $path.$file;
			if(file_exists($filepath))
				$arrExists[] = $file;
		}
		
		return($arrExists);
	}
	
	private function a_SETTERS(){}
	
	/**
	 * get real upload path from path
	 * @param unknown_type $path
	 */
	public function sanitizePath($path){
	
		if(is_dir($path) == false){
			$path = HelperUC::pathToAbsolute($path);
		}
	
		$path = UniteFunctionsUC::realpath($path);
	
		if(empty($path) || !is_dir($path))
			UniteFunctionsUC::throwError("Wrong path given");
	
		//validate path
		$this->validatePathUnderStartPath($path);
	
		$path = UniteFunctionsUC::addPathEndingSlash($path);
	
		return($path);
	}
	
	
	/**
	 * set option
	 */
	public function setOption($option, $value){
		$this->options[$option] = $value;
	}
	
	
	/**
	 * set custom start path
	 */
	protected function setCustomStartPath($path){
		
		if(empty($path))
			return(false);
		
		$path = $this->sanitizePath($path);
				
		$path = UniteFunctionsUC::pathToUnix($path);
		
		$this->customStartPath = $path;
	}
	
	
	private function a_ACTIONS(){}
	
	
	/**
	 * delete files by array from some path
	 */
	protected function deleteFiles($path, $arrFiles){
		
		$path = $this->sanitizePath($path);
		
		//delete files
		foreach($arrFiles as $filename){
			$isValid = $this->isFilenameValidForDelete($filename);
			if($isValid == false)
				continue;
	
			$filepath = $path.$filename;
	
			if(file_exists($filepath) == false)
				continue;
	
			if(is_dir($filepath))
				UniteFunctionsUC::deleteDir($filepath);
			else
				unlink($filepath);
	
		}

		
	}
	
	
	/**
	 * create folder
	 */
	protected function createFolder($path, $folderName){
		
		$pathCreate = $this->validateCreateNewFileFolder($path, $folderName, false);
		
		@mkdir($pathCreate);

		if(is_dir($pathCreate) == false)
			UniteFunctionsUC::throwError("Can't create folder <b>{$folderName}</b>, please check parent folder permissions");
		
	}
	
	
	
	/**
	 * create file
	 */
	protected function createFile($path, $filename){
		
		$filepath = $this->validateCreateNewFileFolder($path, $filename);
		
		UniteFunctionsUC::writeFile("", $filepath);
		
		if(is_file($filepath) == false)
			UniteFunctionsUC::throwError("file <b>$filename</b> didn't created. Please check folder permissions");
		
	}
	
	
	/**
	 * rename file to new name
	 */
	protected function renameFile($path, $filename, $newFilename){
		
		$path = $this->sanitizePath($path);
		
		$filepathCurrent = $path.$filename;
		
		$this->validateFilename($filename);
		
		if(file_exists($filepathCurrent) == false)
			UniteFunctionsUC::throwError("The file: $filename not exists");
		
		$isFile = !is_dir($filepathCurrent);
		
		$filepathNew = $this->validateCreateNewFileFolder($path, $newFilename, $isFile);
		
		$success = @rename($filepathCurrent, $filepathNew);
		
		if($success == false)
			UniteFunctionsUC::throwError("The file didn't renamed");
		
	}
	
	
	/**
	 * get file content
	 */
	protected function getFileContent($path, $filename){
		
		$filepath = $this->validateEditFileData($path, $filename);
		
		$content = file_get_contents($filepath);
		
		return($content);
	}
	
	
	/**
	 * save file content
	 */
	protected function saveFileContent($path, $filename, $content){
		$filepath = $this->validateEditFileData($path, $filename);
		UniteFunctionsUC::writeFile($content, $filepath);
	}
	
	
	/**
	 * move files to path
	 * $actionOnExists - skip,overwrite,message
	 */
	protected function moveFiles($pathSource, $arrFiles, $pathTarget, $actionOnExists){
		
		if(empty($actionOnExists))
			$actionOnExists = "message";
		
		$pathSource = $this->sanitizePath($pathSource);
		$pathTarget = $this->sanitizePath($pathTarget);
		
		if(empty($arrFiles))
			UniteFunctionsUC::throwError("No files to move");
		
		if(!is_array($arrFiles))
			UniteFunctionsUC::throwError("arrFiles should be array");
			
		//show message if some files exists
		$arrExists = $this->getArrExistsFiles($pathTarget, $arrFiles);
		if(!empty($arrExists) && $actionOnExists == "message"){
			
			$numFiles = count($arrExists);
			if($numFiles == 1){
				$file = $arrFiles[0];
				$message = "The file <b> {$file} </b> exists in target folder.";
			}else{
				$message = "{$numFiles} already exists in target folder.";
			}
			
			return($message);
		}
		
		
		//actualy move files
		foreach($arrFiles as $file){
			$filepathSource = $pathSource.$file;
			$filepathTarget = $pathTarget.$file;
			
			$isTargetExists = file_exists($filepathTarget);
			
			if($isTargetExists == true){
				
				switch($actionOnExists){
					case "overwrite":
						$success = @rename($filepathSource, $filepathTarget);
					break;
					case "skip":
						$success = true;
					break;
					default:
						UniteFunctionsUC::throwError("Action on file exists not given");
					break;
				}
				
			}else{		//if file not exists, just move it
				$success = @rename($filepathSource, $filepathTarget);
			}
			
			if($success == false){
				if(is_dir($filepathSource) == true)
					UniteFunctionsUC::throwError("Can't move directory: <b> {$file} </b>");
				else
					UniteFunctionsUC::throwError("Can't move file <b> {$file} </b>");
			}
		}
		
		return(false);
	}
	
	
	/**
	 * unzip file
	 */
	protected function unzipFile($path, $filename){
		
		$path = $this->sanitizePath($path);
		$this->validateFilename($filename);
		$filepath = $path.$filename;
		UniteFunctionsUC::validateFilepath($filepath);
		
		$zip = new UniteZipUC();
		$zip->extract($filepath, $path);
		
	}
	
	
	private function a_OUTPUT(){}
	
	
	/**
	 * handle upload files
	 */
	protected function handleUploadFile($uploadPath, $arrFile){
		
		try{
			
			$this->validateStartPath();
			
			$filename = UniteFunctionsUC::getVal($arrFile, "name");
			
			$tempFilepath = UniteFunctionsUC::getVal($arrFile, "tmp_name");
	
			$destFilepath = $uploadPath."/".$filename;
			
			if(is_file($tempFilepath) == false)
				UniteFunctionsUC::throwError("wrong upload filepath!");

			$success = move_uploaded_file($tempFilepath, $destFilepath);
			
			if($success == false)
				UniteFunctionsUC::throwError("Upload Failed to: $destFilepath");
	
		}catch(Exception $e){
			http_response_code(406);
			
			echo esc_html($e->getMessage());
			
			if(GlobalsUC::SHOW_TRACE == true)
				echo($e->getTraceAsString());
		}
		
		exit();
	}
		
	
	/**
	 * put file uploader
	 */
	private function putUploadFileDialog(){
		
		$nonce = "";
		if(method_exists("UniteProviderFunctionsUC", "getNonce"))
			$nonce = UniteProviderFunctionsUC::getNonce();
		
		$addonID = null;
		if(!empty($this->objAddon))
			$addonID = $this->objAddon->getID();
		
		if($this->flagPutOnce == true)
			return(false);
		
		?>
			<div id="uc_dialog_upload_files" title="<?php esc_html_e("Upload Files", "unlimited-elements-for-elementor")?>" style="display:none">
				
				<div class="uc-dialog-upload-inner">
					
					<div class="uc-assets-activepath">
					<?php esc_html_e("Upload to folder: ", "unlimited-elements-for-elementor") ?> 
						<b>
						<span id="uc_dialogupload_activepath">some path</span>
						</b>
					</div>
					
					<form id="uc_form_dropzone" action="<?php echo GlobalsUC::$url_ajax?>" class="dropzone">
						<input type="hidden" name="action" value="<?php echo GlobalsUC::PLUGIN_NAME?>_ajax_action">
						<input type="hidden" id="uc_input_upload_path" name="upload_path" value="">
						<input type="hidden" id="uc_input_pathkey" name="pathkey" value="">
						<input type="hidden" name="client_action" value="assets_upload_files">
						
						<?php if(!empty($addonID)):?>
							<input type="hidden" name="addonID" value="<?php echo $addonID?>">
						<?php endif?>
						
						<?php if(!empty($nonce)):?>
						<input type="hidden" name="nonce" value="<?php echo esc_attr($nonce)?>">
						<?php endif?>
					</form>
					<script type="text/javascript">
						if(typeof Dropzone != "undefined")
							Dropzone.autoDiscover = false;
					</script>
					
				</div>
				
				<br><br>
			
			</div>
		
		<?php 
	}
	
	
	/**
	 * put create folder dialog
	 */
	private function putDialogCreateFolder(){

		?>
		
		<div id="uc_dialog_create_folder" title="<?php esc_html_e("Create Folder", "unlimited-elements-for-elementor")?>" style="display:none" class="unite-inputs">
			
			<div class="unite-dialog-top"></div>
			<div class="unite-inputs-label"><?php esc_html_e("Folder Name", "unlimited-elements-for-elementor")?></div>
			
			<input id="uc_dialog_create_folder_name" type="text" class="unite-input-regular" value="">
			
			<?php 
				$prefix = "uc_dialog_create_folder";
				$buttonTitle = esc_html__("Create Folder", "unlimited-elements-for-elementor");
				$loaderTitle = esc_html__("Creating Folder...", "unlimited-elements-for-elementor");
				$successTitle = esc_html__("Folder Created", "unlimited-elements-for-elementor");
				HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
			?>			
			
		</div>
		
		<?php 
	}
	
	
	/**
	 * put create file dialog
	 */
	private function putDialogCreateFile(){

		?>
		
		<div id="uc_dialog_create_file" title="<?php esc_html_e("Create File", "unlimited-elements-for-elementor")?>" style="display:none" class="unite-inputs">
			
			<div class="unite-dialog-top"></div>
			<div class="unite-inputs-label"><?php esc_html_e("File Name", "unlimited-elements-for-elementor")?></div>
			
			<input id="uc_dialog_create_file_name" type="text" class="unite-input-regular" value="">
			
			<?php 
				$prefix = "uc_dialog_create_file";
				$buttonTitle = esc_html__("Create File", "unlimited-elements-for-elementor");
				$loaderTitle = esc_html__("Creating File...", "unlimited-elements-for-elementor");
				$successTitle = esc_html__("File Created", "unlimited-elements-for-elementor");
				HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
			?>			
			
		</div>
		
		<?php 
	}
	
	/**
	 * put rename fiels dialog
	 */
	private function putDialogRenameFile(){
		?>
	
		<div id="uc_dialog_rename_file" title="<?php esc_html_e("Rename File / Folder", "unlimited-elements-for-elementor")?>" style="display:none" class="unite-inputs uc-dialog-rename-file">
			<div class="unite-dialog-top"></div>
			<div class="unite-inputs-label"><?php esc_html_e("Rename this file/folder to", "unlimited-elements-for-elementor")?>:</div>
			<input id="uc_dialog_rename_file_input" type="text" class="unite-input-regular">
	
			<?php
			$prefix = "uc_dialog_rename_file";
			$buttonTitle = esc_html__("Rename", "unlimited-elements-for-elementor");
			$loaderTitle = esc_html__("Renaming...", "unlimited-elements-for-elementor");
			$successTitle = esc_html__("File/Dir Renamed", "unlimited-elements-for-elementor");
			HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
			?>
	
		</div>
	<?php
	}
	
	
	/**
	 * put editor dialog
	 */
	private function putDialogEditFile(){
		?>
		<div id="uc_dialog_edit_file" title="<?php esc_html_e("Edit File", "unlimited-elements-for-elementor")?>" style="display:none" class="unite-inputs uc-dialog-edit-file">
			
			<div class="uc-dialog-inner">
				<div id="uc_dialog_edit_file_loader" class="uc-loader-wrapper">
					<span class="loader_text"><?php esc_html_e("Loading...", "unlimited-elements-for-elementor")?></span>
				</div>
				
				<textarea id="uc_dialog_edit_file_textarea" style="display:none"></textarea>

				<div id="uc_dialog_edit_file_loadersaving" class="unite-dialog-loader" style="display:none"><?php esc_html_e("Saving File...", "unlimited-elements-for-elementor")?></div>
				<div id="uc_dialog_edit_file_success" class="unite-dialog-success" style="display:none"><?php esc_html_e("File Saved...", "unlimited-elements-for-elementor")?></div>
				<div id="uc_dialog_edit_file_error" class="unite-dialog-error"></div>
				
			</div>
			
		</div>
		<?php 
	}
	
	
	/**
	 * put move files dialog
	 */
	private function putDialogMoveFiles(){
		
		$objAssets = new UniteCreatorAssetsWork();
		$objAssets->initByKey("folder_browser", $this->objAddon);
		$objAssets->setOption(UniteCreatorAssets::OPTION_ID, "uc_movefile_browser");
		
		?>
		<div id="uc_dialog_move_files" title="<?php esc_html_e("Move Files", "unlimited-elements-for-elementor")?>" style="display:none" class="unite-inputs uc-dialog-move-file">
			
			<div class="unite-dialog-top"></div>
			
			<div id="uc_dialog_move_label" class="unite-inputs-label" data-text="<?php esc_html_e("Move %1 files to", "unlimited-elements-for-elementor")?>">:</div>
			
			<div id="uc_dialog_move_files_url" class="unite-dialog-text-bold mtop_5 mbottom_5"></div>
			
			<div class="uc-browser-wrapper">
			
				<?php $objAssets->putHTML();?>
			
			</div>
			
			<?php 
				$prefix = "uc_dialog_move_files";
				$buttonTitle = esc_html__("Move Files", "unlimited-elements-for-elementor");
				$loaderTitle = esc_html__("Moving Files...", "unlimited-elements-for-elementor");
				$successTitle = esc_html__("Files Moved", "unlimited-elements-for-elementor");
				HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
			?>
			
			<div id="uc_dialog_move_message" class="uc-dialog-move-message" style="display:none">
				
				<div id="uc_dialog_move_message_text" class="uc-dialog-move-message-text"></div>
				
				<div class="vert_top5"></div>
				
				<a href="javascript:void(0)" class="unite-button-secondary" data-action="overwrite"><?php esc_html_e("Overwrite", "unlimited-elements-for-elementor")?></a>
				<a href="javascript:void(0)" class="unite-button-secondary" data-action="skip"><?php esc_html_e("Skip", "unlimited-elements-for-elementor")?></a>
				<a href="javascript:void(0)" class="unite-button-secondary" data-action="cancel"><?php esc_html_e("Cancel", "unlimited-elements-for-elementor")?></a>
				
			</div>
			
		</div>
		<?php
		
	}
	
	
	/**
	 * put actions dialogs
	 */
	private function putActionsDialogs(){
	
		if($this->flagPutOnce == true)
			return(false);
	
		$this->putDialogCreateFolder();
		$this->putDialogCreateFile();
		$this->putDialogEditFile();
		$this->putDialogRenameFile();
		$this->putDialogMoveFiles();
	}
	
	
	/**
	 * put actions panel
	 */
	private function putActionsPanel(){
		?>
				<div class="uc-assets-buttons-panel">
					<a class="uc-button-upload-file uc-panel-button unite-button-secondary" data-action="upload" href="javascript:void(0)" ><?php esc_html_e("Upload", "unlimited-elements-for-elementor")?></a>
					<a class="uc-button-select-all uc-panel-button unite-button-secondary button-disabled" data-action="select_all" href="javascript:void(0)"  data-textselect="<?php esc_html_e("Select All", "unlimited-elements-for-elementor")?>" data-textunselect="<?php esc_html_e("Unselect All", "unlimited-elements-for-elementor")?>" ><?php esc_html_e("Select All", "unlimited-elements-for-elementor")?></a>
					<a class="uc-button-create-folder uc-panel-button unite-button-secondary" data-action="create_folder" href="javascript:void(0)"><?php esc_html_e("Create Folder", "unlimited-elements-for-elementor")?></a>
					<a class="uc-button-create-file uc-panel-button unite-button-secondary" data-action="create_file" href="javascript:void(0)"><?php esc_html_e("Create File", "unlimited-elements-for-elementor")?></a>
					
					<a class="uc-panel-button unite-button-secondary button-disabled uc-relate-multiple" data-action="delete" href="javascript:void(0)"><?php esc_html_e("Delete", "unlimited-elements-for-elementor")?></a>
					<span class="uc-preloader-deleting loader_text mleft_5" style="display:none"><?php esc_html_e("deleting...", "unlimited-elements-for-elementor")?></span>
										
					<a class="uc-panel-button unite-button-secondary button-disabled uc-relate-single uc-relate-file" data-action="edit" href="javascript:void(0)"><?php esc_html_e("Edit", "unlimited-elements-for-elementor")?></a>
					
					<a class="uc-panel-button unite-button-secondary button-disabled uc-relate-single" data-action="rename" href="javascript:void(0)"><?php esc_html_e("Rename", "unlimited-elements-for-elementor")?></a>
					
					<a class="uc-panel-button unite-button-secondary uc-relate-multiple button-disabled" data-action="move" href="javascript:void(0)"><?php esc_html_e("Move", "unlimited-elements-for-elementor")?></a>
					
					<a class="uc-panel-button unite-button-secondary button-disabled uc-relate-special uc-relate-type-zip" data-action="unzip" href="javascript:void(0)"><?php esc_html_e("Unzip", "unlimited-elements-for-elementor")?></a>
					<span class="uc-preloader-unzip loader_round mleft_5" style="display:none"><?php esc_html_e("unzipping...", "unlimited-elements-for-elementor")?></span>
					
					<a class="uc-panel-button unite-button-secondary button-disabled uc-relate-single" data-action="view" href="javascript:void(0)"><?php esc_html_e("View", "unlimited-elements-for-elementor")?></a>
					 
				</div>
		<?php
	}
	
	
	/**
	 * put activepath bar
	 */
	private function putActivePathBar($path){
		
		$activePath = $this->getActivePath($path);
				
		?>
				<div class="uc-assets-activepath">
				
					<span class="uc-assets-activepath-inner">
						<?php esc_html_e("Active Path", "unlimited-elements-for-elementor")?>:
						 <span class="uc-pathname">../<?php echo esc_html($activePath)?></span>
					 </span>
					 
					 <span class="uc-preloader-refreshpath loader_round mleft_5" style="display:none"></span>
				</div>
		
		<?php 
	}
	
	
	
	/**
	 * put html assets
	 */
	public function putHTML($path = null, $wrapperOnly = false){
		
		try{
		
			$this->validateStartPath();
			
			$activePath = $this->getActivePath($path);
			
			$activePathData = htmlspecialchars($activePath);
			
			$startPath = $this->getStartPathRelative();
			$startPathData = htmlspecialchars($startPath); 
			
			$wrapperStyle = $this->getOption(self::OPTION_WRAPPER_STYLE, "");
			
			if(!empty($wrapperStyle))
				$wrapperStyle = "style='{$wrapperStyle}'";
			
			$id = $this->getOption(self::OPTION_ID, "");
			$id = esc_attr($id);
			if(!empty($id))
				$id = "id='{$id}' ";
			
			$putActivepath = $this->getOption(self::OPTION_PUT_ACTIVEPATH);
	
			$arrOptionsForClient = $this->getArrOptionsForClient();
										
			$jsonOptions = json_encode($arrOptionsForClient);
			$jsonOptions = htmlspecialchars($jsonOptions);
		
			}catch(Exception $e){
				$message = $e->getMessage();
				$trace = "";
				if(GlobalsUC::SHOW_TRACE == true)
					$trace = $e->getTraceAsString();
			
				$htmlError = HelperUC::getHtmlErrorMessage($message,$trace, "Assets Manager Error: ");
				?>
				<div <?php echo UniteProviderFunctionsUC::escAddParam($id)?> data-pathkey="<?php echo esc_attr($this->pathKey)?>" class="uc-assets-wrapper" <?php echo UniteProviderFunctionsUC::escAddParam($wrapperStyle)?> data-isbrowser="<?php echo esc_attr($this->isBrowerMode)?>" data-path="<?php echo esc_attr($activePathData)?>" data-startpath="<?php echo esc_attr($startPathData)?>" data-options="<?php echo esc_attr($jsonOptions)?>">
				<?php 
				echo "<div class='uc-assets-startup-error'>".$htmlError."</div>";
				echo "</div>";
				return(false);
			}
			
				
			?>
				<div <?php echo UniteProviderFunctionsUC::escAddParam($id)?>data-pathkey="<?php echo esc_attr($this->pathKey)?>" class="uc-assets-wrapper" <?php echo UniteProviderFunctionsUC::escAddParam($wrapperStyle)?> data-isbrowser="<?php echo esc_attr($this->isBrowerMode)?>" data-path="<?php echo esc_attr($activePathData)?>" data-startpath="<?php echo esc_attr($startPathData)?>" data-options="<?php echo esc_attr($jsonOptions)?>">
					
					<?php 
					try{
					
						if($putActivepath === true)
							$this->putActivePathBar($path);
					?>
					
					<?php
						if($this->isBrowerMode == false)
							$this->putActionsPanel()
					?>
									
					<div class="uc-preloader-filelist loader_text mtop_25" style="display:none">
					
						<?php esc_html_e("Loading File list...", "unlimited-elements-for-elementor")?>
					
					</div>
					
					<div class="uc-filelist-error unite-color-red mtop_10"></div>
					
					<?php 
					if($wrapperOnly == false)
						echo UniteProviderFunctionsUC::escCombinedHtml($this->getHtmlDir($path, true));
					else
						echo UniteProviderFunctionsUC::escCombinedHtml($this->getEmptyHtmlDirList());
						
					?>
				
				<?php 
							
				if($this->isBrowerMode == false){
					
					$this->putUploadFileDialog();
					$this->putActionsDialogs();
				}
				
				$this->flagPutOnce == true;
				
		}catch(Exception $e){
			$message = $e->getMessage();
			$trace = "";
			if(GlobalsUC::SHOW_TRACE == true)
				$trace = $e->getTraceAsString();
				
			$htmlError = HelperUC::getHtmlErrorMessage($message,$trace, "Assets Manager Error: ");
			
			echo "<div class='uc-assets-startup-error'>".esc_html($htmlError)."</div>";
		}
		
		?>
		
			<div class="unite-clear"></div>
		</div>
		
		<?php 
	}
	
}

?>