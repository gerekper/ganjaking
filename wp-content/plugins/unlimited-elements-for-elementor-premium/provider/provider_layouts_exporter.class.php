<?php
/**
 * @package Unlimited Elements
* @author unlimited-elements.com
* @copyright (C) 2012 Unite CMS, All Rights Reserved.
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorLayoutsExporter extends UniteCreatorLayoutsExporterWork{
	
	
	/**
	 * constructor
	 */
	public function __construct(){
		$this->addonsType = "";
	}
	
	
	/**
	 * insert attachment
	 */
	protected function insertAttachmentByImage($arrImage){
		
		$filepath = $arrImage["source"];
		$filename = $arrImage["filename"];
		$filepathDest = $arrImage["dest"];
		$url = $arrImage["url"];
		
		//get filetype
		$arrType = wp_check_filetype_and_ext($filepath, $filename);
		$type = UniteFunctionsUC::getVal($arrType, "type");
		if(empty($type))
			$type = "image/jpeg";
		
		//get name
		$name_parts = pathinfo($filename);
		$name = trim( substr( $filename, 0, -(1 + strlen($name_parts['extension'])) ) );
		
		$name .= "_image";
		
		//get full url
		$urlFull = HelperUC::URLtoFull($url);
				
		//check for existing image id
		$imageID = UniteFunctionsWPUC::getAttachmentIDFromImageUrl($urlFull);
				
		if(!empty($imageID)){
		
			$urlExistingImage = UniteFunctionsWPUC::getUrlAttachmentImage($imageID);
			if($urlExistingImage == $urlFull)
				return($imageID);
		}
						
		
		//get image title
		$title = $name;
		$excerpt = "";
		
		if ( 0 === strpos( $type, 'image/' ) && $image_meta = @wp_read_image_metadata( $filepath ) ) {
			if ( trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) ) {
				$title = $image_meta['title'];
			}
		
			if ( trim( $image_meta['caption'] ) ) {
				$excerpt = $image_meta['caption'];
			}
		}
		
		if(empty($title))
			$title = $name;
		
		$attachment = array(
				'post_mime_type' => $type,
				'guid' => $urlFull,
				'post_title' => $title,
				'post_excerpt' => $excerpt,
		);
		
		
		$id = wp_insert_attachment($attachment, $filepathDest);
		if(is_wp_error($id))
			return(null);
		
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $filepathDest ) );
		
		return($id);
	}
	
	
	/**
	 * make some provider related actions after copied images
	 * add the images to attachments and change url's to id's
	 */
	protected function importLayoutImages_processCopiedImages(){
		
		foreach($this->arrImportImages as $key=>$arrImage){
			
			//get image ID
			$imageID = $this->insertAttachmentByImage($arrImage);
			if(empty($imageID))
				continue;
			
			//update image id
			$arrImage["imageid"] = $imageID;
			$this->arrImportImages[$key] = $arrImage;
		}
		
	}
	
	
	
}
	