<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

//no direct accees
defined ('UNLIMITED_ELEMENTS_INC') or die ('restricted aceess');

class UniteCreatorAcfIntegrate{
	
	const TYPE_SIMPLE_ARRAY = "simple_array";
	const TYPE_POST = "post";
	const TYPE_POSTS_LIST = "posts_list";
	const TYPE_URL = "url";
	const TYPE_USER = "user";
	const TYPE_REPEATER = "repeater";
	const TYPE_GALLERY = "gallery";
	const PREFIX = "cf_";
	
	const SHOW_DEBUG_FIELDS = false;
	const DEBUG_UNKNOWN_TYPE = false;

	private $outputImageSize = null;
	
	
		/**
		 * return if acf plugin activated
		 */
		public static function isAcfActive(){
			
			if(class_exists('ACF'))
				return(true);
			
			return(false);
		}
	
		/**
		 * get image title
		 */
		private function getImageFieldTitle($field){
			
			$title = UniteFunctionsUC::getVal($field, "title");
			$filename = UniteFunctionsUC::getVal($field, "filename");
			
			if(empty($title))
				$title = $filename;
				
			
			$info = pathinfo($title);
			$name = UniteFunctionsUC::getVal($info, "filename");
			
			if(!empty($name))
				$title = $name;
			
			return($title);
		}
		
		
		/**
		 * get image field data
		 */
		private function getImageFieldData($field, $key=null){
			
			
			//set the output image size
			
			$outputImageSize = null;
			
			if(!empty($this->outputImageSize))
				$outputImageSize = $this->outputImageSize;
			
				
			$imageID = UniteFunctionsUC::getVal($field, "id");

			
			$title = $this->getImageFieldTitle($field);
			
			$caption = UniteFunctionsUC::getVal($field, "caption");
			if(!empty($caption))
				$title = $caption;
			
			$description = UniteFunctionsUC::getVal($field, "description");
			$alt = UniteFunctionsUC::getVal($field, "alt");
			
			if(empty($alt))
				$alt = $title;
			
			$urlImage = UniteFunctionsUC::getVal($field, "url");
			$arrSizes = UniteFunctionsUC::getVal($field, "sizes");
			
			$width = UniteFunctionsUC::getVal($field, "width");
			$height = UniteFunctionsUC::getVal($field, "height");
			
			if(!empty($outputImageSize)){
				
				$urlImageSize = UniteFunctionsUC::getVal($arrSizes, $outputImageSize);
				
				if(!empty($urlImageSize)){
					
					$urlImage = $urlImageSize;
					$width = UniteFunctionsUC::getVal($arrSizes, $outputImageSize."-width");
					$height = UniteFunctionsUC::getVal($arrSizes, $outputImageSize."-height");
				}
				
			}
			
			
			$arrValues = array();
			
			$keyprefix = "";
			if(!empty($key))
				$keyprefix = $key."_";				
			
			if(!empty($key)){
				$arrValues[$key] = $urlImage;
				$arrValues[$keyprefix."width"] = $width;
				$arrValues[$keyprefix."height"] = $height;
			}
			else{
				$arrValues["image"] = $urlImage;
				$arrValues["image_width"] = $width;
				$arrValues["image_height"] = $height;
			}			
			
			$thumbMedium = UniteFunctionsUC::getVal($arrSizes, "medium");
			$arrValues[$keyprefix."thumb"] = $thumbMedium;
			
			$thumbMediumWidth = UniteFunctionsUC::getVal($arrSizes, "medium-width");
			$thumbMediumHeight = UniteFunctionsUC::getVal($arrSizes, "medium-width");

			$arrValues[$keyprefix."thumb_width"] = $thumbMediumWidth;
			$arrValues[$keyprefix."thumb_height"] = $thumbMediumHeight;
			
			if(empty($arrSizes))
				$arrSizes = array();
			
			foreach($arrSizes as $size => $value){
				
				if( $size == "medium")
					continue;
				
				if(is_numeric($value))
					continue;
				
				$thumbName = $keyprefix."thumb_".$size;
				$thumbName = str_replace("-", "_", $thumbName);
				
				$thumbWidth = UniteFunctionsUC::getVal($arrSizes, $size."-width");
				$thumbHeight = UniteFunctionsUC::getVal($arrSizes, $size."-height");
				
				$arrValues[$thumbName] = $value;
				$arrValues[$thumbName."_width"] = $width;
				$arrValues[$thumbName."_height"] = $height;
			}

			
			//set attributes
			
			$attributes = "";
			
			$attributes .= " src=\"{$urlImage}\"";
			
			if(!empty($alt)){
				
				$alt = esc_attr($alt);
				$attributes .= " alt=\"{$alt}\"";
			}
			
			$data[$keyprefix."_attributes_nosize"] = $attributes;
			
			if(!empty($width)){
				$attributes .= " width=\"$width\"";
				$attributes .= " height=\"$height\"";
			}
			
			
			//set other data
					
			$arrValues[$keyprefix."attributes"] = $attributes;
			$arrValues[$keyprefix."title"] = $title;
			$arrValues[$keyprefix."description"] = $description;
			$arrValues[$keyprefix."alt"] = $alt;
			$arrValues[$keyprefix."imageid"] = $imageID;
			
			
			return($arrValues);
		}
		
		/**
		 * get post data
		 */
		private function getPostData($objPost, $key, $addID = false){
			
			$arrPost = (array)$objPost;
			
			$postID = UniteFunctionsUC::getVal($arrPost, "ID");
			
			$arrValues = array();
			
			if($addID == true)
				$arrValues[$key."_id"] = $postID;
			else
				$arrValues[$key] = $postID;
			
			$arrValues[$key."_title"] = UniteFunctionsUC::getVal($arrPost, "post_title");
			$arrValues[$key."_alias"] = UniteFunctionsUC::getVal($arrPost, "post_name");
			$arrValues[$key."_content"] = UniteFunctionsUC::getVal($arrPost, "post_content");
			$arrValues[$key."_link"] = UniteFunctionsWPUC::getPermalink($objPost);
			$arrValues["put_post_add_data"] = true;
			
			return($arrValues);
		}
		
		
		/**
		 * get file field data
		 */
		private function getFileFieldData($data, $key){
			
			$title = UniteFunctionsUC::getVal($data, "title");
			$filename = UniteFunctionsUC::getVal($data, "filename");
			
			if(empty($title))
				$title = $filename;
			
			$url = UniteFunctionsUC::getVal($data, "url");
			$filesize = UniteFunctionsUC::getVal($data, "filesize");
			$filesize = size_format($filesize, 2);
			$filesize = str_replace(" ", "", $filesize);
			
			$arrValues = array();
			$arrValues[$key] = $url;
			$arrValues[$key."_title"] = $title;
			$arrValues[$key."_filename"] = $filename;
			$arrValues[$key."_size"] = $filesize;
						
			
			return($arrValues);
		}

		/**
		 * get posts list data
		 */
		private function getPostsListData($data){
			
			if(empty($data))
				return($data);
			
			$arrOutput = array();
			foreach($data as $post){
				
				$arrPost = $this->getPostData($post, "post", true);
				
				$arrOutput[] = $arrPost;
			}
			
			return($arrOutput);
		}
		
		
		/**
		 * get data type of acf values
		 */
		private function getDataType($data, $key){
			
			$type = null;
			
			//image and application types
			
			if(is_array($data)){
				$type = UniteFunctionsUC::getVal($data, "type");
				
				if(!empty($type))
					return($type);
				
				if(isset($data[0])){
					
					$item = $data[0];
					
					$itemType = gettype($item);
					
					
					if($item instanceof WP_Post)
						return(self::TYPE_POSTS_LIST);
					
					if($itemType == "array"){
										
						$itemType = UniteFunctionsUC::getVal($item, "type");
						
						if($itemType == "image" && isset($item["sizes"]))
							return(self::TYPE_GALLERY);
						
						return(self::TYPE_REPEATER);
					}
						
					if($itemType != "object")
						return(self::TYPE_SIMPLE_ARRAY);
					
				};
				

				//check for url
				if(isset($data["url"]))
					return(self::TYPE_URL);
				
				if(isset($data["user_firstname"]) && isset($data["user_lastname"]))
					return(self::TYPE_USER);
				
				if(self::DEBUG_UNKNOWN_TYPE == true){
					dmp("unknown type");
					dmp($data);
					exit();
				}
				
			}
			

			//post
			if(is_object($data)){
				
				if($data instanceof WP_Post)
					return(self::TYPE_POST);
			}
			
			if(is_string($data) || is_numeric($data) || is_bool($data))
				return("simple");
			
			return(null);
		}
		
		/**
		 * get user field data
		 */
		private function getUserData($arrUser, $key){
			
			$arrValues = array();
			$arrValues[$key."_firstname"] = UniteFunctionsUC::getVal($arrUser, "user_firstname");
			$arrValues[$key."_lastname"] = UniteFunctionsUC::getVal($arrUser, "user_lastname");
			$arrValues[$key."_displayname"] = UniteFunctionsUC::getVal($arrUser, "display_name");
			$arrValues[$key."_email"] = UniteFunctionsUC::getVal($arrUser, "user_email");
			$arrValues[$key."_avatar"] = UniteFunctionsUC::getVal($arrUser, "user_avatar");
						
			return($arrValues);
		}
		
		/**
		 * get repeater field
		 */
		private function getRepeaterField($arrData){
			
			if(empty($arrData))
				return($arrData);
			
			foreach($arrData as $index => $arrItem){
				
				//prepare new item
				$arrItemNew = array();
				$arrItemNew["item_index"] = $index+1;
				
				foreach($arrItem as $key => $value){
					$arrItemNew = self::addAcfValues($arrItemNew, $key, $value);
				}
									
				$arrData[$index] = $arrItemNew;
			}
			
			return($arrData);
						
		}
		
		
		/**
		 * get gallery field
		 */
		private function getGalleryField($data){
			
			if(empty($data))
				return(array());
							
			$arrItems = array();
			foreach($data as $index => $item){
				
				$itemData = $this->getImageFieldData($item);
				$itemData["item_index"] = $index+1;
				
				$arrItems[] = $itemData;
			}
			
						
			return($arrItems);
		}
		
		/**
		 * get link field attributes from data
		 */
		private function getLinkAttributes($data){
			
			$title = UniteFunctionsUC::getVal($data, "title");
			$target = UniteFunctionsUC::getVal($data, "target");
			
			$title = htmlspecialchars($title);
			
			$strAttr = "";
			
			if(!empty($title))
				$strAttr = "title=\"$title\"";
			
			if(!empty($target))
				$strAttr .= " target=\"$target\"";
			
			return($strAttr);
		}
		
		/**
		 * get acf type
		 */
		private function addAcfValues($arrValues, $key, $data){
			
			
			if(empty($data)){
				$arrValues[$key] = $data;
				return($arrValues);
			}
			
			if(is_string($data) == true){
				$arrValues[$key] = $data;
				
				return($arrValues);
			}
			
			$type = $this->getDataType($data, $key);
						
			switch($type){
				case "simple":		//simple type like string or boolean
					
					$arrValues[$key] = $data;
					
					return($arrValues);
				break;
				case "image":
					
					$imageData = $this->getImageFieldData($data, $key);
					
					$arrValues = array_merge($arrValues, $imageData);
					
					return($arrValues);
				break;
				case "application":
					
					$fileData = $this->getFileFieldData($data, $key);
					$arrValues = array_merge($arrValues, $fileData);
					
					return($arrValues);
				break;
				case self::TYPE_POST:
					
					$postData = $this->getPostData($data, $key);
					$arrValues = array_merge($arrValues, $postData);
					
					return($arrValues);
				break;
				case self::TYPE_SIMPLE_ARRAY:
					$strData = implode(", ", $data);
					
					$arrValues[$key] = $strData;
					$arrValues[$key."_array"] = $data;						
					
					return($arrValues);
				break;
				case self::TYPE_URL:
					$arrValues[$key] = $data["url"];
					
					$linkAttributes = $this->getLinkAttributes($data);
					$arrValues[$key."_attributes"] = $linkAttributes;
					
					$arrValues[$key."_array"] = $data;
										
					return($arrValues);
				break;
				case self::TYPE_POSTS_LIST:
					
					$arrValues[$key] = $this->getPostsListData($data);
					
				break;
				case self::TYPE_USER:
					$userData = $this->getUserData($data, $key);
					$arrValues = array_merge($arrValues, $userData);
					
				break;
				case self::TYPE_REPEATER:
					
					$arrValues[$key] = $this->getRepeaterField($data);
					
				break;
				case self::TYPE_GALLERY:
					
					$arrValues[$key] = $this->getGalleryField($data);
					
				break;
				default:		
					
					if(is_array($data))
						$arrValues[$key] = $data;
					else
						$arrValues[$key] = "";		//another object
					
					//dmp("assoc");dmp($data); exit();
					
				break;
			}
						
			return($arrValues);			
		}
	
		/**
		 * get fields objects
		 */
		private function getFieldsObjects($postID, $objName, $addPrefix = true){
			
			switch($objName){
				case "post":
					$arrObjects = get_field_objects($postID);
										
				break;
				default:
					UniteFunctionsUC::throwError("get acf fields objects function works only for post right now");
				break;
			}
			
			if($addPrefix == false)
				return($arrObjects);
			
			if(empty($arrObjects))
				return(array());

			if(is_array($arrObjects) == false)
				return(array());
			
			
			//add prefixes
			$arrOutput = array();
			foreach($arrObjects as $key => $arrObject){
				
				$key = self::PREFIX.$key;
				$arrOutput[$key] = $arrObject;
			}
			
			return($arrOutput);
		}
		
		
		/**
		 * get fields type assoc array
		 */
		private function getFieldsTypes($postID, $objName, $addPrefix = true){
			
			$arrObjects = $this->getFieldsObjects($postID, $objName, $addPrefix);
			
			$arrTypes = array();
			foreach($arrObjects as $key => $arrObject){
				
				$type = UniteFunctionsUC::getVal($arrObject, "type");
				
				$arrTypes[$key] = $type;
			}
			
			return($arrTypes);
		}
		
		/**
		 * modify get fields data, consolidate all clones
		 * clone is array inside array
		 */
		public function modifyFieldsData($arrData){
			
			if(empty($arrData))
				return($arrData);
			
			$arrOutput = array();
			
			foreach($arrData as $key => $item){

				//simple value
				if(is_array($item) == false){
					
					$arrOutput[$key] = $item;
					continue;
				}
				
				//numeric array
				if(isset($item[0])){
					$arrOutput[$key] = $item;
					continue;
				}
				
				$isAssocArray = UniteFunctionsUC::isAssocArray($item);
				
				$firstItem = UniteFunctionsUC::getArrFirstValue($item);
				
				if(is_array($firstItem) == false && $isAssocArray == false){
					$arrOutput[$key] = $item;
					continue;
				}
				
				//what's rest is assoc array, wich is clone or group:)
				
				//add them to output
				foreach($item as $itemKey => $subItem){
					
					$newKey = $key."_".$itemKey;
					$arrOutput[$newKey] = $subItem;
				}
								
			}

			
			return($arrOutput);
		}
		
		/**
		 * get single acf field
		 */
		public function getAcfFieldValue($fieldName, $objID, $objName = "post"){
			
			switch($objName){
				case "post":
					
					$value = get_field($fieldName, $objID);
										
				break;
				case "term":
					
					$termID = "term_".$postID;
					
					$value = get_field($fieldName, $objID);
					
				break;
				default:
					UniteFunctionsUC::throwError("get acf fields function works only for post and term right now");
				break;
			}
			
			$arrDataOutput = array();
			$arrDataOutput = $this->addAcfValues($arrDataOutput, $fieldName, $value);
			
			$value = UniteFunctionsUC::getVal($arrDataOutput, $fieldName);
			
			
			return($value);
			
		}
		
		
		/**
		 * get acf post fields
		 */
		public function getAcfFields($postID, $objName = "post", $addPrefix = true, $imageSize = null){
			
			$isActive = self::isAcfActive();
			
			if($isActive == false)
				return(array());
			
			if(!empty($imageSize))
				$this->outputImageSize = $imageSize;
			
			switch($objName){
				case "post":
					
					$arrData = get_fields($postID);
										
					$arrData = $this->modifyFieldsData($arrData);
										
				break;
				case "term":
					
					$termID = "term_".$postID;
										
					$arrData = get_fields($termID);
															
				break;
				case "user":
					
					$userID = "user_".$postID;
					
					$arrData = get_fields($userID);
										
				break;
				default:
					UniteFunctionsUC::throwError("get acf fields function works only for post and term and users right now");
				break;
			}
			
			if(self::SHOW_DEBUG_FIELDS == true){
				dmp($arrData);
				exit();
			}
			
			if(empty($arrData))
				$arrData = array();
						
			$arrDataOutput = array();
			foreach($arrData as $key => $value){
				
				if($addPrefix == true)
					$key = self::PREFIX.$key;
				
				$arrDataOutput = $this->addAcfValues($arrDataOutput, $key, $value);
			}
			
			//clear image size
			
			$this->outputImageSize = null;
			
			return($arrDataOutput);
		}
		
		
		
		/**
		 * get keys of acf fields
		 */
		public function getAcfFieldsKeys($postID, $objName = "post", $addPrefix = true){
			
			$arrFields = $this->getAcfFields($postID, $objName, $addPrefix);
			$arrTypes = $this->getFieldsTypes($postID, $objName, $addPrefix);
				
			$arrOutput = array();
			foreach($arrFields as $key=>$value){
								
				$fieldType = UniteFunctionsUC::getVal($arrTypes, $key);
				
				if(($fieldType == "repeater") && empty($value)){
					$arrOutput[$key] = "empty_repeater";
					continue;
				}
				
				$type = "simple";
				if(is_array($value)){
					
					$item = UniteFunctionsUC::getArrFirstValue($value);
					
					if(is_string($item))
						$type = "array";	//check if simple array or complext array (repeater)
					else{
						$type = $item;
					}
				}
				
				$arrOutput[$key] = $type;
			}
			
			
			/*
			UniteFunctionsUC::showTrace();
			dmp($arrOutput);
			exit();
			*/
			return($arrOutput);
		}
	
	
}