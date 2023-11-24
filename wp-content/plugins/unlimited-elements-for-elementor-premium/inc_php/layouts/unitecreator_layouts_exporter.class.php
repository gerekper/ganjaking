<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorLayoutsExporterWork extends UniteCreatorExporterBase{
	
	const KEY_LOCAL = "[uc_local]";
	const IMAGES_IMPORT_FOLDER = "unlimited_elements";
	
	protected $addonsType = "", $objLayoutType;
	
	protected $arrExportImages = array();
	protected $arrCacheImageFilenames = array();
	
	protected $objLayout;
	protected $arrAddons;
	
	//export
	protected $pathExportLayouts;
	protected $pathCopyLayout;
	protected $pathExportLayout;
	protected $pathExportLayoutAddons;
	protected $pathExportLayoutBGAddons;
	protected $pathExportLayoutImages;
	protected $pathExportZip;
	protected $filenameZip;
	protected $exportedFilename;
	protected $isLayoutsFolderInited;
	protected $isOutputCatFolder = false;
	
	//import
	protected $lastImportID, $pathImportLayout, $pathImportLayoutImages;
	protected $pathImportLayoutAddons, $arrImportImages, $importParams, $arrImportedAddonNames;
	protected $pathImportLayoutBGAddons;
	
	//common
	protected $fieldsParamsImages = array("page_image", "preview_image", "custom_preview_image");
	protected $fieldGeneratedImage = "preview_image";
	
	
	/**
	 * constructor
	 */
	public function _construct(){
		
		parent::__construct();
	}
	
	
	private function a________EXPORT______(){}
	
	
	/**
	 * validate inited
	 */
	protected function validateInited(){
		
		if(empty($this->objLayout))
			UniteFunctionsUC::throwError("The layout object is not inited");
	}
	
	/**
	 * init by layout
	 */
	public function initByLayout(UniteCreatorLayout $objLayout){
		
		$this->objLayout = $objLayout;
		$this->objLayoutType = $objLayout->getObjLayoutType();
		
	}
	
	
	/**
	 * prepare layout file
	 */
	protected function putLayoutFile($strLayout = null, $filename = null){
		
		if(empty($strLayout) || empty($filename)){
						
			$record = $this->objLayout->getRecordForExport();
			
			$strLayout = json_encode($record);			
			$filename = "layout.txt";
		}
		
		$filepath = $this->pathExportLayout.$filename;
		
		UniteFunctionsUC::writeFile($strLayout, $filepath);
	}
	
	
	/**
	 * create layout category copy path
	 * return created path
	 */
	private function createCategoryCopyPath(){
				
		$catName = $this->objLayout->getCatNameForExport();
				
		$arrCat = $this->objLayout->getCategory();
		
		$catID = UniteFunctionsUC::getVal($arrCat, "id");
		
		if(empty($catName))
			UniteFunctionsUC::throwError("Category should not be empty");
		
		$catID = UniteFunctionsUC::getVal($arrCat, "id");
		if(empty($catID))
			UniteFunctionsUC::throwError("Category should have id");
				
		$path = $this->pathExportLayouts.$catName."/";
		
		UniteFunctionsUC::mkdirValidate($path, "Copy Layout Cat Folders");
		
		return($path);		
	}
	
	/**
	 * prepare export folders - layout
	 */
	protected function prepareExportFolders_layout(){
		
		UniteFunctionsUC::validateDir($this->pathExportLayouts, "Export Layouts");
		
		//make layout folder
		$this->pathExportLayout = $this->pathExportLayouts."template_".UniteFunctionsUC::getRandomString(10)."/";
		UniteFunctionsUC::mkdirValidate($this->pathExportLayout, "Export Layout");
		
		//make inner paths
		$this->pathExportLayoutAddons = $this->pathExportLayout."addons/";
		UniteFunctionsUC::mkdirValidate($this->pathExportLayoutAddons, "Layout Addons");

		$this->pathExportLayoutBGAddons = $this->pathExportLayout."bg_addons/";
		
		$this->pathExportLayoutImages = $this->pathExportLayout."images/";
		UniteFunctionsUC::mkdirValidate($this->pathExportLayoutImages, "Layout Images");
		
		//set path copy layout, with category folder or without
		$this->pathCopyLayout = $this->pathExportLayouts;
		if($this->isOutputCatFolder == true)
			$this->pathCopyLayout = $this->createCategoryCopyPath();
		
	}
	
	
	/**
	 * clear export layout data
	 */
	protected function clearExportLayoutData(){
		
		$this->arrExportImages = array();
		$this->arrCacheImageFilenames = array();
		$this->pathExportLayout = "";
		$this->pathExportLayoutAddons = "";
		$this->pathExportLayoutBGAddons = "";		
		$this->pathExportLayoutImages = "";
		$this->pathExportLayouts = "";
		
	}
	
	
	/**
	 * prepare export folders - layout
	 */
	protected function prepareExportFolders_layouts(){
		
		$this->prepareExportFolders_globalExport();
		
		//make layouts folder
		$this->pathExportLayouts = $this->pathExport."layouts/";
		UniteFunctionsUC::mkdirValidate($this->pathExportLayouts, "Layouts");
		
		//clean folder
		if($this->isLayoutsFolderInited == false)
			UniteFunctionsUC::deleteDir($this->pathExportLayouts, false);
		
		//create index.html
		UniteFunctionsUC::writeFile("", $this->pathExportLayouts."index.html");
		
		$this->isLayoutsFolderInited = true;
	}
	
	
	/**
	 * put layout addons
	 */
	protected function putLayoutAddons($arrAddons = null, $isBGAddons = false){
		
		if(empty($arrAddons))
			return(false);
		
		$pathExport = $this->pathExportLayoutAddons;
		$addonsType = $this->addonsType;
		
		if($isBGAddons == true && !empty($arrAddons)){
			
			UniteFunctionsUC::mkdirValidate($this->pathExportLayoutBGAddons, "Layout Background Addons");
			
			$pathExport = $this->pathExportLayoutBGAddons;
			$addonsType = GlobalsUC::ADDON_TYPE_BGADDON;
		}
		
		foreach($arrAddons as $addon){
			
			$name = $addon->getName();
								
			$objAddonExporter = new UniteCreatorExporter();
			
			if(!empty($addonsType))
				$addon->setType($addonsType);
			
			$objAddonExporter->initByAddon($addon);
			$objAddonExporter->export($pathExport, true);
		}
		
	}
	
	/**
	 * get export prefix
	 */
	protected function getExportPrefix(){
		
		$prefix = "template_";
		if(!empty($this->addonsType))
			$prefix = "template_".$this->addonsType."_";
		else{
			$exportPrefix = $this->objLayoutType->exportPrefix;
			
			if(!empty($exportPrefix))
				$prefix = $exportPrefix;
		}
		
		return($prefix);
	}
	
	
	/**
	 * prepare export file zip
	 */
	protected function prepareExportZip($title = null){
		
		$prefix = "";
		
		if(empty($title)){
			$title = $this->objLayout->getTitle();
			$prefix = $this->getExportPrefix();
		}
		
		$handle = HelperUC::convertTitleToHandle($title);
		
		
		UniteFunctionsUC::validateDir($this->pathCopyLayout,"copy layout path");
		
		//get unique filepath
		$filename = "{$prefix}{$handle}";
		$filenameZip = $filename.".zip";
		
		
		$filepath = $this->pathCopyLayout.$filenameZip;
		
		
		if(file_exists($filepath)){
			$counter = 0;
			do{
				$counter++;
				$filename = "{prefix}{$handle}{$counter}";
				$filenameZip = $filename.".zip";
				
				$filepath = $this->pathCopyLayout.$filenameZip;
				
				$fileExists = file_exists($filepath);
	
			}while($fileExists == false);
		}
	
		$this->exportedFilename = $filename;
		$this->filenameZip = $filenameZip;
		
		//actual zip
		$zip = new UniteZipUC();
		$zip->makeZip($this->pathExportLayout, $filepath);
	
		if(file_exists($this->pathExportLayout) == false)
			UniteFunctionsUC::throwError("zip file {$filepath} could not be created");
	
		$this->pathExportZip = $filepath;
	}
	
	
	/**
	 * download export file
	 */
	protected function downloadExportFile(){
	
		UniteFunctionsUC::downloadFile($this->pathExportZip);
	}
	
	
	/**
	 * get export file data
	 */
	protected function getExportedFileData(){
		
		$filepath = $this->pathExportZip;
		$urlFile = HelperUC::pathToFullUrl($filepath);
		$filename = $this->filenameZip;
		
		$filesize = filesize($filepath);
		$sizeFormatted = size_format($filesize, 1);
		$sizeFormatted = str_replace(" ", "", $sizeFormatted);
		
		$output = array();
		$output["filepath"] = $filepath;
		$output["urlfile"] = $urlFile;
		$output["filename"] = $filename;
		$output["exported_filename"] = $this->exportedFilename;
		$output["filesize"] = $filesize;
		$output["filesize_formatted"] = $sizeFormatted;
		
		return($output);
	}
		
	/**
	 * delete export layout folder after the zip is done
	 */
	public function deleteExportLayoutFolder(){
		
		if(is_dir($this->pathExportLayout))
			UniteFunctionsUC::deleteDir($this->pathExportLayout, true);
			
	}
	
	/**
	 * set output with category folders
	 */
	public function setOutputWithCats(){
		$this->isOutputCatFolder = true;
	}
	
	/**
	 * export layout file
	 */
	public function export($isReturnData = false){
		
		try{
	
			$this->validateInited();
			
			$this->prepareExportFolders_layouts();
			$this->prepareExportFolders_layout();
			
			$this->objLayout->cleanLayoutSettingsBeforeExport();
			
			$this->putLayoutAddons();
			
			$this->putLayoutImages();
			
			$this->putLayoutFile();
			
			$this->prepareExportZip();
			
			$this->deleteExportLayoutFolder();
			
			if($isReturnData == true){
				$arrData = $this->getExportedFileData();
				
				return($arrData);
			}
			
			$this->downloadExportFile();
			exit();
	
		}catch(Exception $e){
	
			$prefix = "Export Template Error: ";
			if(!empty($this->objLayout)){
				$title = $this->objLayout->getTitle();
				$prefix = "Export Template (<b>$title</b>) Error: ";
	
			}
	
			$message = $prefix.$e->getMessage();
	
			echo esc_html($message);
			
			if(GlobalsUC::SHOW_TRACE == true)			
				dmp($e->getTraceAsString());
			
			exit();
		}
	
	}
	
	private function a__________EXPORT_IMAGES_________(){}
	
	/**
	 * get save filename, image should not exists
	 */
	private function processConfigImage_getSaveFilename($pathImage, $forceSaveFilename = null){
		
		$info = pathinfo($pathImage);
		$filename = UniteFunctionsUC::getVal($info, "basename");
		if(empty($filename))
			return(null);
		
		if(empty($forceSaveFilename)){
			$isFileExists = array_key_exists($filename, $this->arrCacheImageFilenames);
			if($isFileExists == false){
				$this->arrCacheImageFilenames[$filename] = true;
				return($filename);		
			}				
		}
		
		$saveFilename = $filename;
		
		$basename = $info["filename"];
		$ext = $info["extension"];
		
		if(!empty($forceSaveFilename)){
			$saveFilename = $forceSaveFilename.".".$ext;
			
			$this->arrCacheImageFilenames[$saveFilename] = true;
			return($saveFilename);
		}
		
		$counter = 0;
		$textPortion = UniteFunctionsUC::getStringTextPortion($basename);
		if(empty($textPortion))
			$textPortion = $basename."_";
		
		do{
			$counter++;
			$saveFilename = $textPortion.$counter.".".$ext;
			$isFileExists = array_key_exists($saveFilename, $this->arrCacheImageFilenames);
		
		}while($isFileExists == true);
		
		$this->arrCacheImageFilenames[$saveFilename] = true;
		
		
		return($saveFilename);
	}
	
	
	/**
	 * process config image
	 * return processed image array or null
	 */
	protected function processConfigImage($urlImage, $forceSaveFilename = null){
		
		if(is_numeric($urlImage))
			$urlImage = UniteProviderFunctionsUC::getImageUrlFromImageID($urlImage);
		
		if(empty($urlImage))
			return(null);
			
		//--- just some protection
		
		if(strpos($urlImage, self::KEY_LOCAL) !== false)
		    return(null);
		
		$urlImage = HelperUC::URLtoFull($urlImage);
		$pathImage = HelperUC::urlToPath($urlImage);
		
		if(empty($pathImage))
			return null;
			
		if(file_exists($pathImage) == false || is_file($pathImage) == false)
			return null;
		
		$handlePath = HelperUC::convertTitleToHandle($pathImage, false);
		
		if(isset($this->arrExportImages[$handlePath])){
			$saveFilename = $this->arrExportImages[$handlePath]["save_filename"];
			
			return($saveFilename);
		}
		
		$saveFilename = $this->processConfigImage_getSaveFilename($pathImage, $forceSaveFilename);
		
		if(empty($saveFilename))
			return(null);
		
		$arrImage = array();
		$arrImage["save_filename"] = $saveFilename;
		$arrImage["url"] = $urlImage;
		$arrImage["path"] = $pathImage;
				
		$this->arrExportImages[$handlePath] = $arrImage;
		
		return($saveFilename);
	}
	
	
	
	/**
	 * prepare images fields for export
	 * modifyType - import / export
	 */
	private function modifyAddons_exportImport($addonData, $modifyType){
		
		$name = UniteFunctionsUC::getVal($addonData, "name");
		$addonType = $this->objLayout->getAddonType();
		
		$config = UniteFunctionsUC::getVal($addonData, "config");
		if(empty($config))
			$config = array();
		
		$items = UniteFunctionsUC::getVal($addonData, "items");
		
		$objAddon = new UniteCreatorAddon();
		$objAddon->initByMixed($name, $addonType);
		$objAddon->setParamsValues($config);
		
		if(!empty($items))
			$objAddon->setArrItems($items);
		
		//process config
		$arrConfigImages = $objAddon->getProcessedMainParamsImages();
		foreach($arrConfigImages as $key=>$urlImage){
			
			switch($modifyType){
				case "export":
					$localFilename = $this->processConfigImage($urlImage);
					if(!empty($localFilename))
						$arrConfigImages[$key] = self::KEY_LOCAL.$localFilename;
				break;
				case "import":
					
					$urlImage = $this->processConfigImage_import($urlImage);
					$arrConfigImages[$key] = $urlImage;
					
				break;
				default:
					UniteFunctionsUC::throwError("Wrong modify type: $modifyType");
				break;
			}
			
		}
		
		if(!empty($arrConfigImages)){
			$config = array_merge($config, $arrConfigImages);
			$addonData["config"] = $config;
		}
		
		if(empty($items))
			return($addonData);
		
		//process items
		$arrItemsImages = $objAddon->getProcessedItemsData(UniteCreatorParamsProcessor::PROCESS_TYPE_SAVE, false, "uc_image");
		
		foreach($arrItemsImages as $itemKey => $itemImage){
			
			if(empty($itemImage))
				continue;
			
			
			foreach($itemImage as $key=>$urlImage){
				
				switch($modifyType){
					case "export":
						
						$localFilename = $this->processConfigImage($urlImage);
						if(!empty($localFilename))
							$itemImage[$key] = self::KEY_LOCAL.$localFilename;
					break;	
					case "import":
						
						$urlImage = $this->processConfigImage_import($urlImage);
						$itemImage[$key] = $urlImage;
										
					break;
					default:
						UniteFunctionsUC::throwError("Wrong modify type: $modifyType");
					break;
				}
				
			}
			
			$items[$itemKey] = array_merge($items[$itemKey], $itemImage);
		}
		
		$addonData["items"] = $items;
		
		return($addonData);
	}
	
	
	/**
	 * modify addons - export images
	 */
	public function modifyAddons_exportImages($addonData){
		
		$addonData = $this->modifyAddons_exportImport($addonData, "export");
		
		return($addonData);
	}
	
	
	/**
	 * modify settings image for export
	 */
	public function modifySettinsImagesForExport($arrSettings, $arrImageKeys){
		
		foreach($arrImageKeys as $key){
			
		    if(array_key_exists($key, $arrSettings) == false)
				continue;
			
			$urlImage = UniteFunctionsUC::getVal($arrSettings, $key);
			if(empty($urlImage))
				continue;
			
			//some protection from local predefined
			if(strpos($urlImage,self::KEY_LOCAL) !== false){
			    $arrSettings[$key] = "";
			     continue;
			}
			
			$localFilename = $this->processConfigImage($urlImage);
				
			if(empty($localFilename))
				continue;
			
			
			$arrSettings[$key] = self::KEY_LOCAL.$localFilename;
		}
					
				
		return($arrSettings);
	}
	
	
	/**
	 * modify settings - export images
	 */
	public function modifySettings_exportImages($arrSettings, $type){

		$arrImageKeys = array("bg_image_url");
		$arrSettings = $this->modifySettinsImagesForExport($arrSettings, $arrImageKeys);
				
		return($arrSettings);
	}
	
	
	/**
	 * modify params - export images
	 */
	public function modifyParams_exportImages($arrParams){
		
		$arrImageKeys = $this->fieldsParamsImages;
		$arrParams = $this->modifySettinsImagesForExport($arrParams, $arrImageKeys);
		
		return($arrParams);
	}
	
	
	/**
	 * add featured image to layout if avialable
	 */
	protected function addLayoutPreviewImage(){
				
		$layoutID = $this->objLayout->getID();
		
		$layoutNew = new UniteCreatorLayout();
		$layoutNew->initByID($layoutID);
		
		$urlPageImage = $layoutNew->getPreviewImage();
		
		if(empty($urlPageImage))
			return(false);
		
		$this->processConfigImage($urlPageImage, "layout_preview_image_uc");
	}
	
	
	/**
	 * 	 * locate local images, modify the url as "[local]name" prefix, change image filenames if same files
	 *   return array of image paths and names
	 */
	private function putLayoutImages(){
		
		$this->arrExportImages = array();
		$this->arrCacheImageFilenames = array();
		
		//modify addons data
		$exportFunc = array($this,"modifyAddons_exportImages");
		$this->objLayout->modifyGridDataAddons($exportFunc);
		
		//modify settings objects for BG images
		
		$exportFunc = array($this,"modifySettings_exportImages");
		$this->objLayout->modifyLayoutElementsSettings($exportFunc);
		
		//modify params for images
		$exportFunc = array($this,"modifyParams_exportImages");
		$this->objLayout->modifyParams($exportFunc);
		
		//add layout image if avaialble
		$this->addLayoutPreviewImage();
		
		//copy images
		foreach($this->arrExportImages as $arrImage){
			$sourceFilepath = $arrImage["path"];
			
			if(is_file($sourceFilepath) == false)
				UniteFunctionsUC::throwError("Image file: $sourceFilepath not found!");
			
			$filename = $arrImage["save_filename"];
			$destFilepath = $this->pathExportLayoutImages.$filename;
			
			copy($sourceFilepath, $destFilepath);
		}
		
	}
	
	private function a_____________IMPORT_____________(){}

	
	/**
	 * get import layout catID
	 */
	private function getImportLayoutCatID($arrLayout){

		$objCategories = new UniteCreatorCategories();
		
		$catID = null;
				
		//force category
		$forceCatID = UniteFunctionsUC::getVal($this->importParams, "force_to_cat");
		if(!empty($forceCatID)){
			$objCategories->validateCatExist($forceCatID);
			
			return($forceCatID);
		}
				
		//create or get category by title
		
		$catTitle = UniteFunctionsUC::getVal($arrLayout, "catname");
		if(empty($catTitle))
			return($catID);
		
		$objLayoutType = $this->objLayout->getObjLayoutType();
			
		$catType = $objLayoutType->typeNameCategory;
		$catID = $objCategories->getCreateCatByTitle($catTitle, $catType);
			
		return($catID);
	}
	
	
	/**
	 * import layout by content
	 */
	private function importLayoutByContent($content, $layoutID = null){
		
		$objLayouts = new UniteCreatorLayouts();
		
		if(empty($this->objLayout))
			$this->objLayout = new UniteCreatorLayout();
		
		$arrLayout = @json_decode($content);
		
		if(empty($arrLayout))
			UniteFunctionsUC::throwError("Wrong file format");
		
		$arrLayout = UniteFunctionsUC::convertStdClassToArray($arrLayout);
		
		$arrLayoutOriginal = $arrLayout;
		
		//set layout type
		$layoutType = UniteFunctionsUC::getVal($arrLayout, "type");
		$this->objLayout->setLayoutType($layoutType);
		
		$objType = UniteCreatorAddonType::getAddonTypeObject($layoutType, true);
		
		if(empty($layoutID)){	//new layout
			
			$title = UniteFunctionsUC::getVal($arrLayout, "title");
			$title = $objLayouts->getUniqueTitle($title);
			
			$name = null;	//will generate
			
			if($objType->isBasicType == false)
				$name = UniteFunctionsUC::getVal($arrLayout, "name");
			
			$params = UniteFunctionsUC::getVal($arrLayout, "params");
			
			$description = "";
						
			//set layout category
			$catID = $this->getImportLayoutCatID($arrLayout);
			
			$this->lastImportID = $this->objLayout->createSmall($title, $name, $description, $catID, $this->importParams);
			
			$arrUpdate = array();
			$arrUpdate["layout_data"] = UniteFunctionsUC::getVal($arrLayout, "layout_data");
			$this->objLayout->updateLayoutInDB($arrUpdate);
			
			$arrParams = UniteFunctionsUC::getVal($arrLayout, "params");
			
			if(!empty($arrParams))
				$this->objLayout->updateParams($arrParams);
			
				
		}else{  //existing layout
			
			$arrUpdate = array();
			$arrUpdate["import_title"] = UniteFunctionsUC::getVal($arrLayout, "title");
			$arrUpdate["layout_data"] = UniteFunctionsUC::getVal($arrLayout, "layout_data");
			
			$this->objLayout->initByID($layoutID);
			$this->objLayout->updateLayoutInDB($arrUpdate);
			
			$this->lastImportID = $layoutID;
			
		}
		
		UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_AFTER_IMPORT_LAYOUT_FILE, $arrLayoutOriginal, $this->lastImportID);
		
	}
	
	
	/**
	 * import layout txt file
	 */
	private function importTxtFile($filepath, $layoutID = null){
		
		$content = file_get_contents($filepath);
		
		if(empty($content))
			UniteFunctionsUC::throwError("layout file content don't found");
		
		$this->importLayoutByContent($content, $layoutID);
		
		$this->addLog("Layout imported");
	}
	
	
	/**
	 * prepare import folders for unzipping
	 */
	protected function prepareImportFolders(){
		
		//prepare import folder
		$this->prepareImportFolders_globalImport();		
		
		//prepare base folder
		$pathImportBase = $this->pathImport."single_layout/";
		UniteFunctionsUC::mkdirValidate($pathImportBase, "import layout base");
		
		UniteFunctionsUC::deleteDir($pathImportBase, false);
		
		//create index.html
		UniteFunctionsUC::writeFile("", $pathImportBase."index.html");
		
		//create layout path
		
		self::$serial++;
		
		$this->pathImportLayout = $pathImportBase."layout_".self::$serial."_".UniteFunctionsUC::getRandomString(10)."/";
		
		UniteFunctionsUC::mkdirValidate($this->pathImportLayout, "import layout");
		
		//don't create those paths
		$this->pathImportLayoutAddons = $this->pathImportLayout."addons/";
		$this->pathImportLayoutBGAddons = $this->pathImportLayout."bg_addons/";
		
		$this->pathImportLayoutImages = $this->pathImportLayout."images/";
		
	}
	
	
	/**
	 * unpack import addon from temp file
	 */
	protected function extractImportLayoutFile($filepath){
		
		$zip = new UniteZipUC();
		$extracted = $zip->extract($filepath, $this->pathImportLayout);
		
		if($extracted == false)
			UniteFunctionsUC::throwError("The import layout zip didn't extracted");
	
	}
	
	/**
	 * import layout txt
	 * $layoutID = existign layotu to import
	 */
	protected function importLayoutTxtFromZip($layoutID = null){
		
		$filepathLayout = $this->pathImportLayout."layout.txt";
		UniteFunctionsUC::validateFilepath($filepathLayout,"layout.txt");
		
		$content = file_get_contents($filepathLayout);
		
		$this->importLayoutByContent($content, $layoutID);
		
		$this->objLayout->initByID($this->lastImportID);
	}
	
	
	/**
	 * import layout addons
	 */
	public function importLayoutAddons($overwriteAddons = false){
				
		$exporterAddons = new UniteCreatorExporter();
		
		//crate temp cat
		$exporterAddons->setMustImportAddonType($this->addonsType);
		$logText = $exporterAddons->importAddonsFromFolder($this->pathImportLayoutAddons, null, $overwriteAddons);
		
		$this->arrImportedAddonNames = $exporterAddons->getArrImportedAddonNames();
		
		return($logText);
	}
	
	/**
	 * import background addons
	 * Enter description here ...
	 */
	public function importLayoutBGAddons($overwriteAddons = false){
				
		if(is_dir($this->pathImportLayoutBGAddons) == false)
			return(false);
		
		$exporterAddons = new UniteCreatorExporter();
		$exporterAddons->setMustImportAddonType(GlobalsUC::ADDON_TYPE_BGADDON);
		$exporterAddons->importAddonsFromFolder($this->pathImportLayoutBGAddons, null, $overwriteAddons);
		
	}
	
	
	/**
	 * import zip file
	 * $layoutID - import to layout
	 */
	protected function importZipFile($filepath, $layoutID = null, $overwriteAddons = false){
	
		try{
						
			$this->objLayout = new UniteCreatorLayout();
						
			$this->prepareImportFolders();
			$this->extractImportLayoutFile($filepath);
			$this->importLayoutTxtFromZip($layoutID);
			$logText = $this->importLayoutAddons($overwriteAddons);
			$this->importLayoutImages();
			
			$layoutTitle = $this->objLayout->getTitle(true);
			if(empty($layoutTitle))
				$layoutTitle = "Layout";
			
			$this->addLog("{$layoutTitle} imported");
			
		}catch(Exception $e){
			
			$isLayoutInited = $this->objLayout->isInited();
						
			$layoutTitle = "";
			
			if($isLayoutInited){
				$this->objLayout->delete();
				$layoutTitle = $this->objLayout->getTitle(true);
			}
			
			$this->addLog("Layout $layoutTitle not imported");
				
			throw $e;
		}
	
	}
	
	
	/**
	 * import layout
	 * layoutID to import to
	 */
	public function import($arrFile, $layoutID=null, $overwriteAddons = false, $params = array()){
		
		$this->importParams = $params;
		
		if(is_string($arrFile))
			$filepath = $arrFile;
		else
			$filepath = UniteFunctionsUC::getVal($arrFile, "tmp_name");
		
		if(empty($filepath))
			UniteFunctionsUC::throwError("layout filepath not found");
		
		//get extension
		if(is_array($arrFile)){
			$filename = UniteFunctionsUC::getVal($arrFile, "name");
			$info = pathinfo($filename);
		}else{
			$info = pathinfo($filepath);
		}
		
		$ext = UniteFunctionsUC::getVal($info, "extension");
		$ext = strtolower($ext);
		
		switch($ext){
			case "txt":
				$this->importTxtFile($filepath, $layoutID);
				break;
			case "zip":
				$this->importZipFile($filepath, $layoutID, $overwriteAddons);
				break;
			default:
				UniteFunctionsUC::throwError("Wrong file: $filename");
			break;
		}
		
		return($this->lastImportID);
	}
	
	
	private function a____________IMPORT_IMAGES___________(){}
	
	
	/**
	 * get arr images to import
	 */
	protected function importLayoutImages_getSourceImages(){
		
		$arrImages = array();
		if(is_dir($this->pathImportLayoutImages) == false)
			return(false);
		
		$arrFiles = scandir($this->pathImportLayoutImages);
		foreach($arrFiles as $file){
			if($file == ".." || $file == ".")
				continue;
		
			//filter only images
			$info = pathinfo($file);
			$ext = UniteFunctionsUC::getVal($info, "extension");
			$ext = strtolower($ext);
			
			$isExtOK = true;
			
			switch($ext){
				case "jpg":
				case "png":
				case "jpeg":
				case "gif":
				case "svg":
					break;
				default:
					$isExtOK = false;
				break;
			}
			
			if($isExtOK == false)
				continue;
		
			$filepath = $this->pathImportLayoutImages.$file;
		
			$arrImages[$file] = array(
								 "filename"=>$file,
								 "source"=>$filepath
							);
		}
		
		$this->arrImportImages = $arrImages;
	}
	
	
	/**
	 * get destanation image filepath
	 */
	private function importLayoutImages_getDestFilepath($path, $filename, $filepathSource){
		
		$filepathDest = $path.$filename;
		
		if(!file_exists($filepathDest))
			return($filepathDest);
		
		//sizes not the same - find name
		$newFilename = UniteFunctionsUC::findFreeFilepath($path, $filename, $filepathSource);
		
		$destFilepath = $path.$newFilename;
		
		return($destFilepath);
	}
	
	
	/**
	 * copy images
	 */
	protected function importLayoutImages_copyImages(){
		
		$pathDest = GlobalsUC::$path_images.self::IMAGES_IMPORT_FOLDER."/";		
		UniteFunctionsUC::checkCreateDir($pathDest);
				
		
		//copy images, if not exists, change names
		foreach($this->arrImportImages as $key => $arrImage){
			
			$copyPathDest = $pathDest;
			
			$filename = $key;
			$source = $arrImage["source"];
			$destFilepath = $this->importLayoutImages_getDestFilepath($copyPathDest, $filename, $source);
			
			if(!file_exists($destFilepath)){
				$success = copy($source, $destFilepath);
				if($success == false)
					UniteFunctionsUC::throwError("file: $filename could not be copied to path: $copyPathDest");
			}
			
			$arrImage["dest"] = $destFilepath;
			
			$arrImage["url"] = HelperUC::pathToRelativeUrl($destFilepath);
			$arrImage["urlfull"] = HelperUC::pathToFullUrl($destFilepath);
			
			$this->arrImportImages[$key] = $arrImage;
		}
				
	}
	
	
	/**
	 * make some provider related actions after copied images
	 * function for override
	 */
	protected function importLayoutImages_processCopiedImages(){}
	
	
	/**
	 * get local image data, or null
	 */
	protected function getImportedImageData($urlImage){
		
		$pos = strpos($urlImage, self::KEY_LOCAL);
		$isLocal = ($pos !== false);
		if($isLocal == false)
			return(null);
		
		//get filename
		$localEnd = $pos + strlen(self::KEY_LOCAL);
		
		$filename = substr($urlImage, $localEnd);
		$filename = trim($filename);
		
		$arrImage = UniteFunctionsUC::getVal($this->arrImportImages, $filename);
		
		return($arrImage);
	}
	
	
	/**
	 * replace local image to url image
	 */
	protected function processConfigImage_import($urlImage){
		
		$pos = strpos($urlImage, self::KEY_LOCAL);
		$isLocal = ($pos !== false);
		if($isLocal == false)
			return($urlImage);
		
		//get filename
		$localEnd = $pos + strlen(self::KEY_LOCAL);
		
		$filename = substr($urlImage, $localEnd);
		$filename = trim($filename);
		
		$arrImage = UniteFunctionsUC::getVal($this->arrImportImages, $filename);
		
		if(empty($arrImage))
			UniteFunctionsUC::throwError("Local image: $filename not found");
		
		//check if exists image id
		$imageID = UniteFunctionsUC::getVal($arrImage, "imageid");
		
		if(!empty($imageID))
			return($imageID);
		
		$url = UniteFunctionsUC::getVal($arrImage, "url");
		
		UniteFunctionsUC::validateNotEmpty($url,"url for image: $filename");
		
		return($url);
	}
	
	/**
	 * process settings import images
	 */
	protected function processSettingsImportImages($arrSettings, $arrImageKeys){
		
		foreach($arrImageKeys as $key){
			
			$urlImage = UniteFunctionsUC::getVal($arrSettings, $key);
			if(empty($urlImage))
				continue;
			
			$urlImage = $this->processConfigImage_import($urlImage);
			$arrSettings[$key] = $urlImage;
		}
		
		return($arrSettings);
	}
	
	
	/**
	 * import images in layout settings
	 */
	public function modifySettings_importImages($arrSettings, $type){
		
		$arrImageKeys = array("bg_image_url");
		
		$arrSettings = $this->processSettingsImportImages($arrSettings, $arrImageKeys);
		
		return($arrSettings);
	}
	
		
	
	/**
	 * import images in layout params
	 */
	public function modifyParams_importImages($arrParams){
		
		$arrImageKeys = $this->fieldsParamsImages;
		
		$arrParams = $this->processSettingsImportImages($arrParams, $arrImageKeys);
		
		return($arrParams);
	}

	
	/**
	 * modify addons - export images
	 */
	public function modifyAddons_importImages($addonData){
	
		$addonData = $this->modifyAddons_exportImport($addonData, "import");
		
		return($addonData);
	}
	
	
	/**
	 * update layout after images copy
	 */
	protected function importLayoutImages_updateLayout(){
		
		$this->objLayout->validateInited();
		
		$importFunc = array($this,"modifyAddons_importImages");
		
		$importFuncSettings = array($this,"modifySettings_importImages");
		
		$importFuncParams = array($this,"modifyParams_importImages");
		
		//modify addon data
		$this->objLayout->modifyGridDataAddons($importFunc);
		$this->objLayout->modifyLayoutElementsSettings($importFuncSettings);
		$this->objLayout->updateGridData();
		
		
		//update params
		$this->objLayout->modifyParams($importFuncParams);
		$this->objLayout->updateInternalParamsInDB();
		
	}
	
	
	/**
	 * import images
	 */
	protected function importLayoutImages(){
		
		$this->arrImportImages = array();
		
		//get source images array
		$this->importLayoutImages_getSourceImages();
		
		if(empty($this->arrImportImages))
			return(false);
		
		//copy images to dest folder
		$this->importLayoutImages_copyImages();
		
		$this->importLayoutImages_processCopiedImages();
		
		//update layout
		$this->importLayoutImages_updateLayout();
		
		UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_AFTER_IMPORT_LAYOUT_FILE_IMAGES, $this->lastImportID, $this->arrImportImages);
		
	}
	
	
	
	
}