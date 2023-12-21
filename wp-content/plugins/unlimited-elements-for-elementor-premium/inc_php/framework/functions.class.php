<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteFunctionsUC{

	const SANITIZE_ID = "sanitize_id";		//positive number or empty
	const SANITIZE_TEXT_FIELD = "sanitize_text_field";
	const SANITIZE_KEY = "sanitize_key";
	const SANITIZE_NOTHING = "sanitize_nothing";

	private static $serial = 0;
	private static $arrCache = array();

	/**
	 * throw error
	 */
	public static function throwError($message, $code = 0){

		throw new Exception($message, $code);
	}

	/**
	 * print object data
	 */
	public static function showObjData($obj){

		echo "<pre>";

		dmp("Vars: ");
		dmp(get_object_vars($obj));

		dmp("Methods:");
		dmp(get_class_methods($obj));

		echo "</pre>";

	}


	/**
	 * throw error and show function trace
	 */
	public static function showTrace($exit = false){

		try{
			throw new Exception("Show me the trace");
		}catch(Exception $e){

			$trace = $e->getTraceAsString();
			dmp($trace);

			if($exit == true)
				exit();
		}
	}

	/**
	 * check if array is assoc
	 */
	public static function isArrayAssoc($arr){
		if(is_array($arr) == false)
			return(false);
			if (array() === $arr) return false;
			return array_keys($arr) !== range(0, count($arr) - 1);
	}

	/**
	 * get post or get variable
	 */
	public static function getPostGetVariable($name, $initVar = "", $sanitizeType = ""){

		$var = $initVar;

		if(isset($_POST[$name]))
			$var = $_POST[$name];
		elseif(isset($_GET[$name]))
			$var = $_GET[$name];

		$var = UniteProviderFunctionsUC::sanitizeVar($var, $sanitizeType);

		return ($var);
	}

	/**
	 * get post variable
	 */
	public static function getPostVariable($name, $initVar = "", $sanitizeType = ""){

		$var = $initVar;

		if(isset($_POST[$name]))
			$var = $_POST[$name];

		$var = UniteProviderFunctionsUC::sanitizeVar($var, $sanitizeType);

		return ($var);
	}

	/**
	 * get get variable
	 */
	public static function getGetVar($name, $initVar = "", $sanitizeType = ""){

		$var = $initVar;

		if(isset($_GET[$name]))
			$var = $_GET[$name];

		$var = UniteProviderFunctionsUC::sanitizeVar($var, $sanitizeType);

		return ($var);
	}

	/**
	 * get files variable
	 */
	public static function getFilesVariable($name){

		$files = array();

		if(isset($_FILES[$name]))
			$files = $_FILES[$name];

		$keys = array(
			"name",
			"type",
			"size",
			"error",
			"tmp_name",
		);

		$var = array();

		foreach($files as $key => $file){
			if(in_array($key, $keys) === false)
				continue;

			foreach($file as $name => $value){
				if(is_array($value) === true){
					foreach($value as $index => $inner){
						$var[$name][$index][$key] = $inner;
					}
				}else{
					$var[$name][0][$key] = $value;
				}
			}
		}

		return ($var);
	}


	public static function z_________ARRAYS_______(){}


	/**
	 * get value from array. if not - return alternative
	 */
	public static function getVal($arr,$key,$altVal=""){

		if(isset($arr[$key]))
			return($arr[$key]);

		return($altVal);
	}


	/**
	 * get first item value
	 */
	public static function getArrFirstValue($arr){

		if(empty($arr))
			return("");

		if(is_array($arr) == false)
			return("");

		$firstValue = reset($arr);

		return($firstValue);
	}


	/**
	 * get first not empty key from array
	 */
	public static function getFirstNotEmptyKey($arr){

		foreach($arr as $key=>$item){
			if(!empty($key))
				return($key);
		}

		return("");
	}


	/**
	 * filter array, leaving only needed fields - also array
	 *
	 */
	public static function filterArrFields($arr, $fields, $isFieldsAssoc = false){
		$arrNew = array();

		if($isFieldsAssoc == false){
			foreach($fields as $field){
				if(array_key_exists($field, $arr))
					$arrNew[$field] = $arr[$field];
			}
		}else{
			foreach($fields as $field=>$value){
				if(array_key_exists($field, $arr))
					$arrNew[$field] = $arr[$field];
			}
		}

		return($arrNew);
	}

	/**
	 * remove some of the assoc array fields
	 * fields is simple array - field1, field2, field3
	 */
	public static function removeArrItemsByKeys($arrItems, $keysToRemove){

		foreach($keysToRemove as $key){

			if(array_key_exists($key, $arrItems))
				unset($arrItems[$key]);

		}

		return($arrItems);
	}


	/**
	 * Convert std class to array, with all sons
	 */
	public static function convertStdClassToArray($d){

		if (is_object($d)) {
			$d = get_object_vars($d);
		}

		if (is_array($d)){

			return array_map(array("UniteFunctionsUC","convertStdClassToArray"), $d);
		} else {
			return $d;
		}

	}


	/**
	 *
	 * get random array item
	 */
	public static function getRandomArrayItem($arr){
		$numItems = count($arr);
		$rand = rand(0, $numItems-1);
		$item = $arr[$rand];
		return($item);
	}

	/**
	 * get different values in $arr from the default $arrDefault
	 * $arrMustKeys - keys that must be in the output
	 *
	 */
	public static function getDiffArrItems($arr, $arrDefault, $arrMustKeys = array()){

		if(gettype($arrDefault) != "array")
			return($arr);

		if(!empty($arrMustKeys))
			$arrMustKeys = UniteFunctionsUC::arrayToAssoc($arrMustKeys);

		$arrValues = array();
		foreach($arr as $key => $value){

			//treat must value
			if(array_key_exists($key, $arrMustKeys) == true){
				$arrValues[$key] = self::getVal($arrDefault, $key);
				if(array_key_exists($key, $arr) == true)
					$arrValues[$key] = $arr[$key];
				continue;
			}

			if(array_key_exists($key, $arrDefault) == false){
				$arrValues[$key] = $value;
				continue;
			}

			$defaultValue = $arrDefault[$key];
			if($defaultValue !== $value){
				$arrValues[$key] = $value;
				continue;
			}

		}


		return($arrValues);
	}

	/**
	 * convert posts array to assoc by ID
	 */
	public static function arrPostsToAssoc($arrPosts){

		if(empty($arrPosts))
			return(array());

		$arrOutput = array();
		foreach($arrPosts as $post){

			$arrOutput[$post->ID] = $post;
		}

		return($arrOutput);
	}


	/**
	 *
	 * Convert array to assoc array by some field
	 */
	public static function arrayToAssoc($arr, $field=null, $field2 = null){

		if(empty($arr))
			return(array());

		$arrAssoc = array();

		foreach($arr as $item){

			if(empty($field))
				$arrAssoc[$item] = $item;
			else{

				if(!empty($field2))
					$arrAssoc[$item[$field]] = $item[$field2];
				else{

					$arrAssoc[$item[$field]] = $item;
				}
			}
		}

		return($arrAssoc);
	}


	/**
	 *
	 * convert assoc array to array
	 */
	public static function assocToArray($assoc){

		$arr = array();
		foreach($assoc as $key=>$item){
			$arr[] = $item;
		}

		return($arr);
	}

	/**
	 *
	 * convert assoc array to array
	 */
	public static function assocToArrayKeyValue($assoc, $keyName, $valueName, $firstItem = null){

		$arr = array();
		if(!empty($firstItem))
			$arr = $firstItem;

		foreach($assoc as $item){
			if(!array_key_exists($keyName, $item))
				UniteFunctionsUC::throwError("field: $keyName not found in array");

			if(!array_key_exists($valueName, $item))
				UniteFunctionsUC::throwError("field: $valueName not found in array");

			$key = $item[$keyName];
			$value = $item[$valueName];

			$arr[$key] = $value;
		}

		return($arr);
	}

	/**
	 *
	 * convert assoc array to array
	 */
	public static function assocToArrayNames($assoc, $valueName){

		$arr = array();

		if(empty($assoc))
			return(array());

		foreach($assoc as $item){

			if(!array_key_exists($valueName, $item))
				UniteFunctionsUC::throwError("field: $valueName not found in array");

			$value = $item[$valueName];

			$arr[] = $value;
		}

		return($arr);
	}


	/**
	 *
	 * do "trim" operation on all array items.
	 */
	public static function trimArrayItems($arr){
		if(gettype($arr) != "array")
			UniteFunctionsUC::throwError("trimArrayItems error: The type must be array");

		foreach ($arr as $key=>$item)
			$arr[$key] = trim($item);

		return($arr);
	}

	/**
	 *
	 * encode array into json for client side
	 */
	public static function jsonEncodeForClientSide($arr){

		if(empty($arr))
			$arr = array();

		$json = json_encode($arr);
		$json = addslashes($json);

		$json = "'".$json."'";

		return($json);
	}


	/**
	 * encode json for html data like data-key="json"
	 */
	public static function jsonEncodeForHtmlData($arr, $dataKey=""){

		$strJson = "";
		if(!empty($arr)){
			$strJson = json_encode($arr);
			$strJson = htmlspecialchars($strJson);
		}
		if(!empty($dataKey))
			$strJson = " data-{$dataKey}=\"{$strJson}\"";

		return($strJson);
	}


	/**
	 * convert array with styles in each item to items string
	 */
	public static function arrStyleToStrStyle($arrStyle, $styleName = "", $addCss = "", $addImportant = false){

		if(empty($arrStyle) && empty($addCss))
			return("");

		$br = "\n";
		$tab = "	";

		$output = $br;

		if(!empty($styleName))
			$output .= $styleName."{".$br;

		foreach($arrStyle as $key=>$value){
			if($key == "inline_css"){
				$addCss .= $value;
				continue;
			}

			if($addImportant == true)
				$value = $value . " !important";

			$output .= $tab.$key.":".$value.";".$br;
		}

		//add additional css
		if(!empty($addCss)){
			$arrAddCss = explode($br, $addCss);
			$output .= $br;
			foreach($arrAddCss as $str){
				$output .= $tab.$str.$br;
			}
		}

		if(!empty($styleName))
			$output .= "}".$br;

		return($output);
	}


	/**
	 * convert array with styles in each item to items string
	 */
	public static function arrStyleToStrInlineCss($arrStyle, $addCss = "", $addStyleTag = true){

		$addCss = trim($addCss);

		if(empty($arrStyle) && empty($addCss))
			return("");

		$output = "";
		foreach($arrStyle as $key=>$value){
			$output .= $key.":".$value.";";
		}

		if(!empty($addCss)){

			$addCss = self::removeLineBreaks($addCss);
			$output .= $addCss;
		}

		if($addStyleTag && !empty($output))
			$output = "style=\"{$output}\"";


		return($output);
	}

	/**
	 * check if the array is accociative or not
	 */
	public static function isAssocArray($arr){
			if (array() === $arr) return false;
			return array_keys($arr) !== range(0, count($arr) - 1);
	}


	/**
	 * insert items to array
	 * array (key, text, insert_after)
	 */
	public static function insertToAssocArray($arrItems, $arrNewItems){

		$arrInsert = array();
		$arrInsertTop = array();
		$counter = 0;

		$arrOutput = array();

		//prepare insert arrays
		foreach($arrNewItems as $item){
			$insertAfter = UniteFunctionsUC::getVal($item, "insert_after");

			if($insertAfter	== "bottom")
				$insertAfter = null;

			if(empty($insertAfter)){
				$counter++;
				$insertAfter = "bottom_".$counter;
			}

			if($insertAfter == "top")
				$arrInsertTop[] = $item;
			else{

				if(isset($arrInsert[$insertAfter])){

					if(self::isAssocArray($arrInsert[$insertAfter]) == false){
						$arrInsert[$insertAfter][] = $item;		//more then 2 items
					}else{
						//second item
						$arrInsert[$insertAfter] = array($arrInsert[$insertAfter], $item);
					}

				}
				else{		//first item

					$arrInsert[$insertAfter] = $item;

				}

			}

		}


		//insert the top part
		foreach($arrInsertTop as $newItem){

			$newItemKey = $newItem["key"];
			$newItemText = $newItem["text"];

			$arrOutput[$newItemKey] = $newItemText;
		}


		//create the items with new inserted to middle
		foreach($arrItems as $key=>$item){

			$arrOutput[$key] = $item;

			//insrt the item
			if(array_key_exists($key, $arrInsert)){

				$arrNewItem = $arrInsert[$key];

				if(self::isAssocArray($arrNewItem) == false){

					foreach($arrNewItem as $newItemReal){
						$newItemKey = $newItemReal["key"];
						$newItemText = $newItemReal["text"];
						$arrOutput[$newItemKey] = $newItemText;
					}

				}else{	//single item

					$newItemKey = $arrNewItem["key"];
					$newItemText = $arrNewItem["text"];
					$arrOutput[$newItemKey] = $newItemText;

				}


				unset($arrInsert[$key]);
			}

		}

		//insert the rest to bottom
		foreach($arrInsert as $newItem){

			$newItemKey = $newItem["key"];
			$newItemText = $newItem["text"];

			$arrOutput[$newItemKey] = $newItemText;
		}


		return($arrOutput);
	}


	/**
	 * add first value to array
	 */
	public static function addArrFirstValue($arr, $text, $value = ""){

		$arr = array($value => $text) + $arr;

		return($arr);
	}


	/**
	 *
	 * convert php array to js array text
	 * like item:"value"
	 */
	public static function phpArrayToJsArrayText($arr, $tabPrefix="			"){
		$str = "";
		$length = count($arr);

		$counter = 0;
		foreach($arr as $key=>$value){
			$str .= $tabPrefix."{$key}:\"{$value}\"";
			$counter ++;
			if($counter != $length)
				$str .= ",\n";
		}

		return($str);
	}

	/**
	 * get duplicate values from array in assoc array
	 */
	public static function getArrayDuplicateValues($arrAssoc){

		if(empty($arrAssoc))
			return(array());

		$arrDuplicate = array_diff_assoc($arrAssoc, array_unique($arrAssoc));

		$arrDuplicate = array_flip($arrDuplicate);

		if(empty($arrDuplicate))
			$arrDuplicate = array();

		return($arrDuplicate);
	}

	/**
	 * iterate array recursive, run callback on every array
	 */
	public static function iterateArrayRecursive($arr, $callback){

		if(is_array($arr) == false)
			return(false);

		call_user_func($callback, $arr);

		foreach($arr as $item){

			if(is_array($item))
				self::iterateArrayRecursive($item, $callback);
		}

	}

	/**
	 * merge arrays with unique ids
	 */
	public static function mergeArraysUnique($arr1, $arr2, $arr3 = array()){

		if(empty($arr2) && empty($arr3))
			return($arr1);

		$arrIDs = array_merge($arr1, $arr2, $arr3);
		$arrIDs = array_unique($arrIDs);

		return($arrIDs);
	}

	/**
	 * modify data array for show - for DEBUG purposes
	 * convert single array like in post meta
	 */
	public static function modifyDataArrayForShow($arrData, $convertSingleArray = false){

		if(is_array($arrData) == false)
			return($arrData);

		$arrDataNew = array();
		foreach($arrData as $key=>$value){

			$key = htmlspecialchars($key);

			if(is_string($value) == true)
				$value = htmlspecialchars($value);

			$key = " $key";

			$arrDataNew[$key] = $value;

			//convert single array
			if($convertSingleArray == true && is_array($value) && count($value) == 1 && isset($value[0]))
				$arrDataNew[$key] = $value[0];

		}

		return($arrDataNew);
	}


	/**
	 * get id's array from any input
	 */
	public static function getIDsArray($input){

		if(empty($input))
			return(array());

		if(is_array($input))
			$arrInput = $input;
		else
			$arrInput = explode(",", $input);

		$arrIDs = array();
		foreach($arrInput as $id){

			if(is_string($id))
				$id = trim($id);

			if(is_object($id)){
				$arrObj = (array)$id;
				$id = self::getVal($arrObj, "ID");
			}

			if(is_numeric($id) == false || empty($id))
				continue;

			$arrIDs[] = $id;
		}


		return($arrIDs);
	}

	/**
	 * convert array to csv string
	 */
	public static function arrayToCsv($data, $delimiter = ',', $enclosure = '"', $escape_char = "\\"){

		if(is_array($data) == false)
			return("");

		//get the headers
		if(empty($data))
			return("");

		$csv = "";

			$f = fopen('php://memory', 'r+');

			$arrKeys = null;

			foreach ($data as $item) {

				//put keys
				if(empty($arrKeys)){

					$arrKeys = array_keys($item);

					fputcsv($f, $arrKeys, $delimiter, $enclosure, $escape_char);
				}

					fputcsv($f, $item, $delimiter, $enclosure, $escape_char);
			}
			rewind($f);

			$csv = stream_get_contents($f);

			return $csv;
	}

	/**
	 * clear word to first underscore from strings array
	 */
	public static function clearKeysFirstUnderscore($arrParams){

		$arrNew = array();
		if(empty($arrParams))
			return($arrNew);

		foreach($arrParams as $key => $value){

			$pos = strpos($key,"_");

			if(!empty($pos))
				$key = substr($key, $pos+1);

			$arrNew[$key] = $value;
		}

		return($arrNew);
	}


	public static function z_____________STRINGS_____________(){}


	/**
	 * add tabs to strign lines
	 */
	public static function addTabsToText($str, $tab = "	"){

		$lines = explode("\n", $str);

		foreach($lines as $index=>$line){
			$lineTrimmed = trim($line);
			if(!empty($lineTrimmed))
				$line = $tab.$line;

			$lines[$index] = $line;
		}

		$str = implode("\n", $lines);

		return($str);
	}

	/**
	 * search lower case in string
	 */
	public static function isStringContains($strContent, $strSearch){

		$searchString = trim($strSearch);
		if(empty($strSearch))
			return(true);

		$strContent = strtolower($strContent);
		$strSearch = strtolower($strSearch);

		$pos = strpos($strContent, $strSearch);

		if($pos === false)
			return(false);

		return(true);
	}


	/**
	 * remove line breaks in string
	 */
	public static function removeLineBreaks($string){

		$string = str_replace("\r", "", $string);
		$string = str_replace("\n", "", $string);

		return($string);
	}


	/**
	 * get random string
	 */
	public static function getRandomString($length = 10, $numbersOnly = false){

		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';

		if($numbersOnly === "hex")
			$characters = '0123456789abcdef';
		if($numbersOnly === true)
			$characters = '0123456789';

		$randomString = '';

		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}

		return $randomString;
	}


	/**
	 * limit string chars to max size
	 */
	public static function limitStringSize($str, $numChars, $addDots = true){

		if(function_exists("mb_strlen") == false)
			return($str);

		if(mb_strlen($str) <= $numChars)
			return($str);

		if($addDots)
			$str = mb_substr($str, 0, $numChars-3)."...";
		else
			$str = mb_substr($str, 0, $numChars);

		return($str);
	}

	/**
	 * truncate string
	 * preserve - preserve word
	 * separator - is the ending
	 */
	public static function truncateString($value, $length = 100, $preserve = true, $separator = '...', $charset="utf-8"){

		if(empty($length))
			$length = 100;

		$originalValue = $value;

		$value = strip_tags($value,"<br><em><b><strong>");

		if (mb_strlen($value, $charset) <= $length)
			return($originalValue);

		//preserve words
					if ($preserve) {

						if(function_exists("mb_strpos")){
								// If breakpoint is on the last word, return the value without separator.
								if (false === ($breakpoint = mb_strpos($value, ' ', $length, $charset))) {
									return $value;
								}
						}else{

							if (false === ($breakpoint = strpos($value, ' ', $length))) {
										return $value;
								}

						}

							$length = $breakpoint;
					}

					if(function_exists("mb_substr"))
						$value = rtrim(mb_substr($value, 0, $length, $charset)).$separator;
					else
						$value = rtrim(substr($value, 0, $length)).$separator;


					//if html errors - strip tags and trim again

				$arrErrors = UniteFunctionsUC::validateHTML($value);

				if(!empty($arrErrors)){
					$value = strip_tags($originalValue);

						if(function_exists("mb_substr"))
							$value = rtrim(mb_substr($value, 0, $length, $charset)).$separator;
						else
							$value = rtrim(substr($value, 0, $length)).$separator;

				}

				return $value;
	}



	/**
	 * convert array to xml
	 */
	public static function arrayToXML($array, $rootName, $xml = null){

		if($xml === null){
			$xml = new SimpleXMLElement("<{$rootName}/>");
			self::arrayToXML($array, $rootName, $xml);

			$strXML = $xml->asXML();

			if($strXML === false)
				UniteFunctionsUC::throwError("Wrong xml output");

			return($strXML);
		}

		//for inner elements:
		foreach($array as $key => $value){

			if(is_numeric($key))
				$key = 'item' . $key;

			if(is_array($value)){
				$node = $xml->addChild($key);
				self::arrayToXML($value,$rootName,$node);
			}
			else{
				$xml->addChild($key, htmlspecialchars($value));
			}
		}

	}


	/**
	 * format xml string
	 */
	public static function formatXmlString($xml){

		$xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);
		$token      = strtok($xml, "\n");
		$result     = '';
		$pad        = 0;
		$matches    = array();
		while ($token !== false) :
		if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) :
		$indent=0;
		elseif (preg_match('/^<\/\w/', $token, $matches)) :
		$pad--;
		$indent = 0;
		elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
		$indent=1;
		else :
		$indent = 0;
		endif;
		$line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
		$result .= $line . "\n";
		$token   = strtok("\n");
		$pad    += $indent;
		endwhile;
		return $result;
	}


	/**
	 * unserialize string if it's a string type
	 * the return will be always array
	 */
	public static function maybeUnserialize($str){

		if(empty($str))
			return(array());

		if(is_array($str))
			return($str);

		$arrOutput = @unserialize($str);

		if(empty($arrOutput))
			return(array());

		if(!is_array($arrOutput))
			UniteFunctionsUC::throwError("The unserialized string should be alwayas array type");

		return($arrOutput);
	}


	/**
	 * sanitize attribute
	 */
	public static function sanitizeAttr($strAttr){

		$strAttr = htmlspecialchars($strAttr);

		return($strAttr);
	}

	/**
	 * get sanitize types array
	 */
	public static function getArrSanitizeTypes(){

		$arrSanitize = array();
		$arrSanitize[self::SANITIZE_ID] = __("Sanitize ID", "unlimited-elements-for-elementor");
		$arrSanitize[self::SANITIZE_KEY] = __("Sanitize KEY", "unlimited-elements-for-elementor");
		$arrSanitize[self::SANITIZE_TEXT_FIELD] = __("Sanitize Text Field", "unlimited-elements-for-elementor");
		$arrSanitize[self::SANITIZE_NOTHING] = __("No Sanitize (not recomended)", "unlimited-elements-for-elementor");

		return($arrSanitize);
	}


	/**
	 * normalize size
	 */
	public static function normalizeSize($value){

		$value = (string)$value;
		$value = strtolower($value);
		if(is_numeric($value) == false)
			return($value);

		//numeric
		$value = (int)$value;

		$value .= "px";

		return($value);
	}

	/**
	 * get size and unit number
	 */
	public static function getSizeAndUnit($numSize, $defaultUnit){

		$size = $numSize;
		$unit = $defaultUnit;

		if(is_numeric($numSize)){
			$output = array("size"=>$size,"unit"=>$unit);
			return($output);
		}

		$size = strtolower($size);

		$arrUnits = array("%","em","px","vh");

		foreach($arrUnits as $unit){

			if(strpos($size, $unit) !== false){
				$unit = $unit;
				$size = str_replace($unit, "", $size);
				break;
			}
		}

		$output = array("size"=>$size,"unit"=>$unit);

		return($output);
	}


	/**
	 * check if text is encoded
	 */
	public static function isTextEncoded($content){

		if(is_string($content) == false)
			return(false);

		if(empty($content))
			return(false);

			// Check if there is no invalid character in string
			if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $content))
				return false;

			$decoded = @base64_decode($content, true);

			// Decode the string in strict mode and send the response
			if(empty($decoded))
				return false;

			// Encode and compare it to original one
			if(base64_encode($decoded) != $content)
				return false;

		return true;
	}


	/**
	 * maybe json decode
	 */
	public static function maybeJsonDecode($str){

		if(empty($str))
			return($str);

		if(is_string($str) == false)
			return($str);

		//try to json decode
		$arrJson = self::jsonDecode($str);
		if(!empty($arrJson) && is_array($arrJson))
			return($arrJson);

		return($str);
	}

	/**
	 * maybe json decode
	 */
	public static function maybeCsvDecode($str){

		$str = trim($str);

		if(empty($str))
			return($str);

		if(is_string($str) == false)
			return($str);

		//not allowed html tags

		if($str != strip_tags($str))
			return($str);

		//try to csv decode

		$arrLines = explode("\n", $str);

		if(count($arrLines) < 2)
			return($str);

		$arrKeys = array();

		$arrItems = array();

		foreach($arrLines as $line){

			$line = trim($line);

			if(empty($line))
				continue;

			$arrLine = str_getcsv($line);

			if(empty($arrLine))
				continue;


			//get the keys
			if(empty($arrKeys)){
				$arrKeys = $arrLine;

				continue;
			}

			//get the item

			if(count($arrLine) != count($arrKeys))
				continue;

			//create the item

			$item = array();

			foreach($arrKeys as $index=>$key){

				$value = $arrLine[$index];

				$key = trim($key);
				$value = trim($value);

				$item[$key] = $value;
			}


			$arrItems[] = $item;
		}

		if(empty($arrItems))
			return($str);


		return($arrItems);
	}


	/**
	 * maybe decode content
	 */
	public static function maybeDecodeTextContent($value){

		if(empty($value))
			return($value);

		if(is_string($value) == false)
			return($value);

		$isEncoded = self::isTextEncoded($value);

		if($isEncoded == false)
			return($value);

		$decoded = self::decodeTextContent($value);

		return($decoded);
	}


	/**
	 * decode string content
	 */
	public static function decodeTextContent($content){

		$content = rawurldecode(base64_decode($content));

		return($content);
	}


	/**
	 * encode content
	 */
	public static function encodeContent($content){

		if(is_array($content))
			$content = json_encode($content);

		$content = rawurlencode($content);

		$content = base64_encode($content);

		return($content);
	}


	/**
	 * decode content given from js
	 */
	public static function decodeContent($content, $convertToArray = true){

		if(empty($content))
			return($content);

		$content = rawurldecode(base64_decode($content));

		if($convertToArray == true)
			$arr = self::jsonDecode($content);
		else
			$arr = @json_decode($content);

		return $arr;
	}


	/**
	 * decode content given from js
	 */
	public static function jsonDecode($content, $outputArray = false){

		if($outputArray == true && empty($content))
			return(array());

		$arr = @json_decode($content);
		$arr = self::convertStdClassToArray($arr);

		if($outputArray == true && empty($content))
			return(array());

		return $arr;
	}


	/**
	 * clean path string
	 */
	public static function cleanPath($path){

		if(defined("DIRECTORY_SEPARATOR"))
			$ds = DIRECTORY_SEPARATOR;
		else
			$ds = "/";

		if (!is_string($path) && !empty($path)){
			self::throwError('JPath::clean: $path is not a string.');
		}

		$path = trim($path);

		if(empty($path))
			return($path);

		// Remove double slashes and backslashes and convert all slashes and backslashes to DIRECTORY_SEPARATOR
		// If dealing with a UNC path don't forget to prepend the path with a backslash.
		elseif (($ds == '\\') && ($path[0] == '\\' ) && ( $path[1] == '\\' ))
		{
			$path = "\\" . preg_replace('#[/\\\\]+#', $ds, $path);
		}
		else
		{
			$path = preg_replace('#[/\\\\]+#', $ds, $path);
		}

		return $path;
	}

	/**
	 * get numeric portion from the string, remove all except numbers
	 */
	public static function getNumberFromString($str){

		$str = preg_replace("/[^0-9]/", '', $str);

		return($str);
	}


	/**
	 * get number from string end
	 */
	public static function getNumberFromStringEnd($str){

		$matches = array();
		if (!preg_match('#(\d+)$#', $str, $matches))
			return("");

		if(!isset($matches[1]))
			return("");

		return($matches[1]);
	}


	/**
	 * get number from string end
	 */
	public static function getStringTextPortion($str){

		$num = self::getNumberFromStringEnd($str);
		if($num === "")
			return($str);

		$lastPost = strlen($str)-strlen($num);

		$textPortion = substr($str, 0, $lastPost);

		return($textPortion);
	}


	/**
	 * get serial ID, should never repeat
	 */
	public static function getSerialID($prefix){

		self::$serial++;
		$rand = self::getRandomString(5,true);
		$id = $prefix."_".$rand."_".self::$serial;

		return($id);
	}


	/**
	 * get pretty html from complicated html, strip tags except usefull
	 */
	public static function getPrettyHtml($html){

		//strip tags
		$html = preg_replace( '/<\/?div[^>]*\>/i', '', $html );
		$html = preg_replace( '/<\/?span[^>]*\>/i', '', $html );
		$html = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $html );
		$html = preg_replace( '#<style(.*?)>(.*?)</style>#is', '', $html );
		$html = preg_replace( '/<i [^>]*><\\/i[^>]*>/', '', $html );
		$html = preg_replace( '/ class=".*?"/', '', $html );

		// remove lines
		$html = preg_replace( '/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/', "\n", $html );

		$html = trim( $html );

		return $html;
	}

	/**
	 * get href from string
	 */
	public static function getHrefFromHtml($str){

		$arrFound = array();
		preg_match('/href=(["\'])([^\1]*)\1/i', $str, $arrFound);

		if(empty($arrFound))
			return(null);

		$href = self::getVal($arrFound, 2);

		return($href);
	}

	/**
	 * convert handle to title
	 */
	public static function convertHandleToTitle($handle){

		$title = str_replace("_", " ", $handle);
		$title = ucwords($title);

		return($title);
	}


	/**
	 * truncate html
	 */
	public static function truncateHTML($maxLength, $html){

				mb_internal_encoding("UTF-8");

				$printedLength = 0;
				$position = 0;
				$tags = array();

				ob_start();

				while ($printedLength < $maxLength && preg_match('{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}', $html, $match, PREG_OFFSET_CAPTURE, $position)){

						list($tag, $tagPosition) = $match[0];

						// Print text leading up to the tag.
						$str = mb_strcut($html, $position, $tagPosition - $position);

						if ($printedLength + mb_strlen($str) > $maxLength){
								print(mb_strcut($str, 0, $maxLength - $printedLength));
								$printedLength = $maxLength;
								break;
						}

						print($str);
						$printedLength += mb_strlen($str);

						if ($tag[0] == '&'){
								// Handle the entity.
								print($tag);
								$printedLength++;
						}
						else{
								// Handle the tag.
								$tagName = $match[1][0];
								if ($tag[1] == '/'){
										// This is a closing tag.

										$openingTag = array_pop($tags);
										assert($openingTag == $tagName); // check that tags are properly nested.

										print($tag);
								}
								else if ($tag[mb_strlen($tag) - 2] == '/'){
										// Self-closing tag.
										print($tag);
								}
								else{
										// Opening tag.
										print($tag);
										$tags[] = $tagName;
								}
						}

						// Continue after the tag.
						$position = $tagPosition + mb_strlen($tag);
				}

				// Print any remaining text.
				if ($printedLength < $maxLength && $position < mb_strlen($html))
						print(mb_strcut($html, $position, $maxLength - $printedLength));

				// Close any open tags.
				while (!empty($tags))
						 printf('</%s>', array_pop($tags));


				$bufferOuput = ob_get_contents();

				ob_end_clean();

				$html = $bufferOuput;

				return $html;

		}

		/**
		 * replace only first substring in string
		 */
		public static function replaceFirstSubstring($string, $strFind, $strReplace){

		$newString = substr_replace($string, $strReplace, strpos($string, $strFind), strlen($strFind));

		return($newString);
		}


	public static function z__________URLS__________(){}

	/**
	 *
	 * get url contents
	 */
	public static function getUrlContents($url,$arrPost=array(),$method = "post",$debug=false){

		$ch = curl_init();
		$timeout = 0;

		if(empty($arrPost))
			$arrPost = array();

		$strPost = '';
		foreach($arrPost as $key=>$value){
			if(!empty($strPost))
				$strPost .= "&";

			if(is_array($value))
				$value = json_encode($value);

			$value = urlencode($value);
			$strPost .= "$key=$value";
		}

		//set curl options
		if(strtolower($method) == "post"){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$strPost);
		}
		else{
			if(!empty($strPost))
				$url = self::addUrlParams($url, $strPost);
		}


		//remove me
		//Functions::addToLogFile(SERVICE_LOG_SERVICE, "url", $url);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


		$headers = array();
		$headers[] = "User-Agent:Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8";
		$headers[] = "Accept-Charset:utf-8;q=0.7,*;q=0.7";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);


		if($debug == true){
			dmp($url);
			dmp($response);
			exit();
		}

		if($response == false)
			throw new Exception("getUrlContents Request failed");

		curl_close($ch);
		return($response);
	}


	/**
	 * convert url to handle
	 *
	 */
	public static function urlToHandle($url = ''){

		// Replace all weird characters with dashes
		$url = preg_replace('/[^\w\-'. '~_\.' . ']+/u', '-', $url);

		// Only allow one dash separator at a time (and make string lowercase)
		return mb_strtolower(preg_replace('/--+/u', '-', $url), 'UTF-8');
	}


	/**
	 * add params to url
	 */
	public static function addUrlParams($url, $params, $addOnlyNewParam = false){

		if(empty($params))
			return($url);

		if(strpos($url, "?") !== false){
			if($addOnlyNewParam == true)
				return($url);

			$url .= "&";
		}
		else
			$url .= "?";

		if(is_array($params)){

			$strParams = "";
			foreach($params as $key=>$value){
				if(!empty($strParams))
					$strParams .= "&";

				$strParams .= $key."=".urlencode($value);
			}

			$params = $strParams;
		}

		$url .= $params;


		return($url);
	}


	/**
	 * convert url to https if needed
	 */
	public static function urlToSsl($url){

		$url = str_replace("http://", "https://", $url);
		$url = str_replace("HTTP://", "HTTPS://", $url);

		return($url);
	}


	/**
	 * clean url - remove double slashes
	 */
	public static function cleanUrl($url){

		$url = preg_replace('/([^:])(\/{2,})/', '$1/', $url);

		return($url);
	}

	/**
	 * get base url from any url
	 */
	public static function getBaseUrl($url, $stripPagination = false){

		$arrUrl = parse_url($url);

		$scheme = UniteFunctionsUC::getVal($arrUrl, "scheme","http");
		$host = UniteFunctionsUC::getVal($arrUrl, "host");
		$path = UniteFunctionsUC::getVal($arrUrl, "path");

		$url = "{$scheme}://{$host}{$path}";

		if($stripPagination == true){

			//strip pagination
			if(strpos($url, "/page/") !== false){
				$numPage = (get_query_var('paged')) ? get_query_var('paged') : 1;
				$url = str_replace("/page/$numPage/", "/", $url);
			}
		}

		return($url);
	}

	/**
	 * get links from some content
	 */
	public static function parseHTMLGetLinks($html){

		$htmlDom = new DOMDocument;

		@$htmlDom->loadHTML($html);

		$links = $htmlDom->getElementsByTagName('a');

		if(empty($links))
			return(array());

		if($links->length == 0)
			return(array());

		$arrLinks = array();
		foreach($links as $link){

				$linkHref = $link->getAttribute('href');
				if(strlen(trim($linkHref)) == 0)
						continue;
				if($linkHref[0] == '#')
						continue;

			$arrLinks[] = $linkHref;
		}

		return($arrLinks);
	}


	public static function z___________VALIDATIONS_________(){}

	/**
	 * validate that the value is in array
	 */
	public static function validateObjectMethod($object, $strMethod, $objectName){

		if(method_exists($object, "initByID") == false)
			UniteFunctionsUC::throwError("Object: $objectName don't have method $strMethod");

	}


	/**
	 * validate that the value is in array
	 */
	public static function validateValueInArray($value, $valueTitle, $arr){

		if(is_array($arr) == false)
			self::throwError("array of $valueTitle should be array");

		if(array_search($value, $arr) === false)
			self::throwError("wrong $value, should be: ".implode(",", $arr));

	}


	/**
	 *
	 * validate that some file exists, if not - throw error
	 */
	public static function validateFilepath($filepath,$errorPrefix=null){

		if(file_exists($filepath) == true && is_file($filepath) == true)
			return(false);

		if($errorPrefix == null)
			$errorPrefix = "File";


		$message = $errorPrefix." $filepath not exists!";

		self::throwError($message);
	}


	/**
	 *
	 * validate that some directory exists, if not - throw error
	 */
	public static function validateDir($pathDir, $errorPrefix=null){
		if(is_dir($pathDir) == true)
			return(false);

		if($errorPrefix == null)
			$errorPrefix = "Directory";
		$message = $errorPrefix." $pathDir not exists!";
		self::throwError($message);
	}

	//--------------------------------------------------------------
	//validate if some directory is writable, if not - throw a exception
	private static function validateWritable($name,$path,$strList,$validateExists = true){

		if($validateExists == true){
			//if the file/directory doesn't exists - throw an error.
			if(file_exists($path) == false)
				throw new Exception("$name doesn't exists");
		}
		else{
			//if the file not exists - don't check. it will be created.
			if(file_exists($path) == false) return(false);
		}

		if(is_writable($path) == false){
			chmod($path,0755);		//try to change the permissions
			if(is_writable($path) == false){
				$strType = "Folder";
				if(is_file($path)) $strType = "File";
				$message = "$strType $name is doesn't have a write permissions. Those folders/files must have a write permissions in order that this application will work properly: $strList";
				throw new Exception($message);
			}
		}
	}


	/**
	 *
	 * validate that some value is numeric
	 */
	public static function validateNumeric($val,$fieldName=""){
		self::validateNotEmpty($val,$fieldName);

		if(empty($fieldName))
			$fieldName = "Field";

		if(!is_numeric($val))
			self::throwError("$fieldName should be numeric ");
	}

	/**
	 *
	 * validate that some variable not empty
	 */
	public static function validateNotEmpty($val,$fieldName=""){

		if(empty($fieldName))
			$fieldName = "Field";

		if(empty($val) && is_numeric($val) == false)
			self::throwError("Field <b>$fieldName</b> should not be empty");
	}


	/**
	 * validate that the field don't have html tags
	 */
	public static function validateNoTags($val, $fieldName=""){

		if($val == strip_tags($val))
			return(true);

		if(empty($fieldName))
			$fieldName = "Field";

		self::throwError("Field <b>$fieldName</b> should not contain tags");
	}

	/**
	 * validate sign not exists
	 */
	public static function validateCharNotExists($str, $sign, $objectName){

		if(strpos($str, $sign) !== false)
			self::throwError("{$objectName} doesn't allow & signs");

	}


	/**
	 * check the php version. throw exception if the version beneath 5
	 */
	private static function validatePHPVersion(){
		$strVersion = phpversion();
		$version = (float)$strVersion;
		if($version < 5)
			self::throwError("You must have php5 and higher in order to run the application. Your php version is: $version");
	}


	/**
	 * valiadte if gd exists. if not - throw exception
	 * @throws Exception
	 */
	public static function validateGD(){
		if(function_exists('gd_info') == false)
			throw new Exception("You need PHP GD library to operation. Please turn it on in php.ini");
	}


	/**
	 * return if the variable is alphanumeric
	 */
	public static function isAlphaNumeric($val){
		$match = preg_match('/^[\w_]+$/', $val);

		if($match == 0)
			return(false);

		return(true);
	}

	/**
	 * validate id's list, allowed only numbers and commas
	 * @param $val
	 */
	public static function validateIDsList($val, $fieldName=""){

		$isValid = self::isValidIDsList($val);

		if($isValid == false)
			self::throwError("Field <b>$fieldName</b> allow only numbers and comas.");

	}


	/**
	 * validate id's list, allowed only numbers and commas
	 * @param $val
	 */
	public static function isValidIDsList($val, $fieldName=""){

		if(empty($val))
			return(true);

		$match = preg_match('/^[0-9,]+$/', $val);

		if($match == 0)
			return(false);

		return(true);
	}


	/**
	 * return if the array is id's array
	 */
	public static function isValidIDsArray($arr){

		if(is_array($arr) == false)
			return(false);

		if(empty($arr))
			return(true);

		foreach($arr as $key=>$value){

			if(is_numeric($key) == false || is_numeric($value) == false)
				return(false);
		}

		return(true);
	}


	/**
	 * validate that the value is alphanumeric
	 * underscores also alowed
	 */
	public static function validateAlphaNumeric($val, $fieldName=""){

		if(empty($fieldName))
			$fieldName = "Field";

		if(self::isAlphaNumeric($val) == false)
			self::throwError("Field <b>$fieldName</b> allow only english words, numbers and underscore.");

	}


	/**
	 * validate url alias
	 */
	public static function validateUrlAlias($alias, $fieldName=""){

		if(empty($fieldName))
			$fieldName = "Field";

		self::validateNotEmpty($alias, $fieldName);

		$url = "http://example.com/".$alias;
		$isValid = self::isUrlValid($url);

		if($isValid == false)
			self::throwError("Field <b>$fieldName</b> allow only words, numbers hypens and underscores.");

		//if(self::isAlphaNumeric($val) == false)
			//self::throwError("Field <b>$fieldName</b> allow only english words, numbers and underscore.");
	}


	/**
	 * validate email field
	 */
	public static function validateEmail($email, $fieldName="email"){

		$isValid = self::isEmailValid($email);

		if($isValid == true)
			return(false);

		self::throwError(__("The $fieldName is not valid", "unlimited-elements-for-elementor"));

	}

	/**
	 * check if the email is valid
	 */
	public static function isEmailValid($email){

		return (bool)preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $email);
	}

	/**
	 * check if the url is valid
	 */
	public static function isUrlValid($url){

		return (bool)filter_var($url, FILTER_VALIDATE_URL);
	}

		/**
		 * check if the html is valid
		 */
		public static function isHTMLValid($html){

			if(empty($html))
				return(true);

			if(class_exists("DOMDocument") == false)
				return(true);

			$dom = new DOMDocument();
		$dom->loadHTML($html);

			$isValid = $dom->validate();


			return($isValid);
		}


		/**
		 * check if html valid, get errors list
		 */
	public static function validateHTML($string){

			$start = strpos($string, '<');
			$end = strrpos($string, '>', $start);

			if($start === false)
			return(array());

		if(function_exists("libxml_clear_errors") == false)
			return(array());

		if(function_exists("simplexml_load_string") == false)
			return(array());


			if ($end !== false) {
					$string = substr($string, $start);
			} else {
					$string = substr($string, $start, strlen($string) - $start);
			}


			// xml requires one root node
			$string = "<div>$string</div>";

			libxml_use_internal_errors(true);
			libxml_clear_errors();
			simplexml_load_string($string);
		$arrErrors = libxml_get_errors();


			return $arrErrors;
	}



	public static function z________FILE_SYSTEM________(){}



	/**
	 *
	 * if directory not exists - create it
	 * @param $dir
	 */
	public static function checkCreateDir($dir){
		if(!is_dir($dir))
			mkdir($dir);
	}


	/**
	 * make directory and validate that it's exists
	 */
	public static function mkdirValidate($path, $dirName){

		if(is_dir($path) == false){
			@mkdir($path);
			if(!is_dir($path))
				UniteFunctionsUC::throwError("$dirName path: {$path} could not be created. Please check your permissions");
		}

	}


	/**
	 * get path info of certain path with all needed fields
	 */
	public static function getPathInfo($filepath){
		$info = pathinfo($filepath);

		//fix the filename problem
		if(!isset($info["filename"])){
			$filename = $info["basename"];
			if(isset($info["extension"]))
				$filename = substr($info["basename"],0,(-strlen($info["extension"])-1));
			$info["filename"] = $filename;
		}

		return($info);
	}


	/**
	 * get filename extention
	 */
	public static function getFilenameNoExtension($filepath){
		$info = self::getPathInfo($filepath);
		$filename = self::getVal($info, "filename");
		return($filename);
	}

	/**
	 * get filename extention
	 */
	public static function getFilenameExtension($filepath){
		$info = self::getPathInfo($filepath);
		$ext = self::getVal($info, "extension");
		return($ext);
	}

	/**
	 *
	 * save some file to the filesystem with some text
	 */
	public static function writeFile($str, $filepath){

		if(is_array($str))
			UniteFunctionsUC::throwError("write file should accept only string in file: ". $filepath);

		$fp = fopen($filepath,"w+");

		if($fp === false)
			UniteFunctionsUC::throwError("File $filepath could not been created. Check folder permissions");

		fwrite($fp,$str);
		fclose($fp);
	}


	/**
	 *
	 * get list of all files in the directory
	 */
	public static function getFileList($path){
		$dir = scandir($path);
		$arrFiles = array();
		foreach($dir as $file){
			if($file == "." || $file == "..") continue;
			$filepath = $path . "/" . $file;
			if(is_file($filepath)) $arrFiles[] = $file;
		}
		return($arrFiles);
	}

	/**
	 * get path size
	 */
	public static function getPathSize($path){

		if(empty($path))
			return(0);

		if(is_dir($path) == false)
			return(0);

		$arrFiles = self::getFileListTree($path);

		if(empty($arrFiles))
			return(0);

		$totalSize = 0;

		foreach($arrFiles as $pathFile){

			if(is_file($pathFile) == false)
				continue;

			$fileSize = filesize($pathFile);

			$totalSize += $fileSize;
		}

		return($totalSize);
	}

	/**
	 * get recursive file list inside folder and subfolders
	 */
	public static function getFileListTree($path, $filetype = null, $arrFiles = null){

		if(empty($arrFiles))
			$arrFiles = array();

		if(is_dir($path) == false)
			return($arrFiles);

		$path = self::addPathEndingSlash($path);

		$arrPaths = scandir($path);
		foreach($arrPaths as $file){
			if($file == "." || $file == "..")
				continue;

			$filepath = $path.$file;

			$isDir = is_dir($filepath);

			if($isDir == true){

				//add dirs
				if(is_array($filetype) && array_search("dir", $filetype) !== false || !is_array($filetype) && $filetype == "dir"){
					$arrFiles[] = $filepath;
				}

				$arrFiles = self::getFileListTree($filepath, $filetype, $arrFiles);
			}

			$info = pathinfo($filepath);

			$ext = self::getVal($info, "extension");
			$ext = strtolower($ext);

			if(!empty($filetype) && is_array($filetype) && array_search($ext, $filetype) === false){
				continue;
			}
			if(!empty($filetype) && is_array($filetype) == false && $filetype != $ext)
				continue;

			if($isDir == true)
				continue;

			$arrFiles[] = $filepath;
		}


		return($arrFiles);
	}


	/**
	 *
	 * get list of all directories in the directory
	 */
	public static function getDirList($path){
		$arrDirs = scandir($path);

		$arrFiles = array();
		foreach($arrDirs as $dir){

			if($dir == "." || $dir == "..")
				continue;

			$dirpath = $path . "/" . $dir;

			if(is_dir($dirpath))
				$arrFiles[] = $dir;
		}

		return($arrFiles);
	}


	/**
	 *
	 * clear debug file
	 */
	public static function clearDebug($filepath = "debug.txt"){

		if(file_exists($filepath))
			unlink($filepath);
	}

	/**
	 *
	 * save to filesystem the error
	 */
	public static function writeDebugError(Exception $e,$filepath = "debug.txt"){
		$message = $e->getMessage();
		$trace = $e->getTraceAsString();

		$output = $message."\n";
		$output .= $trace."\n";

		$fp = fopen($filepath,"a+");
		fwrite($fp,$output);
		fclose($fp);
	}


	//------------------------------------------------------------
	//save some file to the filesystem with some text
	public static function addToFile($str,$filepath){
		$fp = fopen($filepath,"a+");
		fwrite($fp,"---------------------\n");
		fwrite($fp,$str."\n");
		fclose($fp);
	}

	/**
	 * delete folder contents that older then some seconds from now
	 */
	public static function clearDirByTime($path, $olderThenSeconds = 60){

		self::deleteDir($path,false,array(),"",array("olderthen"=>$olderThenSeconds));
	}

	/**
	 * delete list of files
	 */
	public static function deleteListOfFiles($arrFiles){

		if(empty($arrFiles))
			return(false);

		if(is_array($arrFiles) == false)
			return(false);

		foreach($arrFiles as $filepath){

			if(file_exists($filepath) == false)
				continue;

			unlink($filepath);
		}

	}


	/**
	 *
	 * recursive delete directory or file
	 */
	public static function deleteDir($path,$deleteOriginal = true, $arrNotDeleted = array(),$originalPath = "", $params = array()){

		$olderSec = self::getVal($params, "olderthen");

		if(!empty($olderSec))
			$currentTime = time();

		if(empty($originalPath))
			$originalPath = $path;

		//in case of paths array
		if(getType($path) == "array"){
			$arrPaths = $path;

			foreach($path as $singlePath)
				$arrNotDeleted = self::deleteDir($singlePath,$deleteOriginal,$arrNotDeleted,$originalPath,$params);

			return($arrNotDeleted);
		}

		if(!file_exists($path))
			return($arrNotDeleted);

		// delete file
		if(is_file($path)){

			//check by time
			if(!empty($olderSec)){

				$filetime = filemtime($path);
				$diff = $currentTime-$filetime;

				//skip if not much time left
				if($diff < $olderSec){
					$arrNotDeleted[] = $path;
					return($arrNotDeleted);
				}
			}

			$deleted = unlink($path);
			if(!$deleted)
				$arrNotDeleted[] = $path;

			return($arrNotDeleted);
		}

		//delete directory

		$arrPaths = scandir($path);

		foreach($arrPaths as $file){
			if($file == "." || $file == "..")
				continue;
			$filepath = realpath($path."/".$file);
			$arrNotDeleted = self::deleteDir($filepath,$deleteOriginal,$arrNotDeleted,$originalPath,$params);
		}

		if($deleteOriginal == true || $originalPath != $path){

			//check by time
			if(!empty($olderSec)){

				$filetime = filemtime($path);
				$diff = $currentTime-$filetime;

				//skip if not much time left
				if($diff < $olderSec){
					$arrNotDeleted[] = $path;
					return($arrNotDeleted);
				}

			}


			$deleted = @rmdir($path);
			if(!$deleted)
				$arrNotDeleted[] = $path;
		}


		return($arrNotDeleted);
	}


	/**
	 * copy directory contents to another directory
	 */
	public static function copyDir($src, $dst) {
		$dir = opendir($src);
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					self::copyDir($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}

	/**
	 * add ending to the path
	 */
	public static function addUrlEndingSlash($url){

		$lastChar = substr($url, strlen($url)-1, 1);

		if($lastChar == '/')
			return($url);

		$url .= '/';

		return($url);
	}


	/**
	 * add ending to the path
	 */
	public static function addPathEndingSlash($path){

		$slashType = (strpos($path, '\\')===0) ? 'win' : 'unix';

		$lastChar = substr($path, strlen($path)-1, 1);

		if ($lastChar != '/' && $lastChar != '\\')
			$path .= ($slashType == 'win') ? '\\' : '/';

		return($path);
	}


	/**
	 * remove path ending slash
	 */
	public static function removePathEndingSlash($path){
		$path = rtrim($path, "/");
		$path = rtrim($path,"\\");

		return($path);
	}


	/**
	 * convert path to unix format slashes
	 */
	public static function pathToUnix($path){
		$path = str_replace('\\', '/', $path);
		$path = preg_replace('/\/+/', '/', $path); // Combine multiple slashes into a single slash

		return($path);
	}


	/**
	 * convert path to relative path, based on basepath
	 */
	public static function pathToRelative($path, $basePath){

		$path = str_replace($basePath, "", $path);
		$path = ltrim($path, '/');
		return($path);
	}

	/**
	 * join paths
	 * @param $path
	 */
	public static function joinPaths($basePath, $path){

		$newPath = $basePath."/".$path;
		$newPath = self::pathToUnix($newPath);
		return($newPath);
	}


	/**
	 * turn path to realpath
	 * output only unix format, if not found - return ""
	 * @param $path
	 */
	public static function realpath($path, $addEndingSlash = true){

		$path = realpath($path);
		if(empty($path))
			return($path);

		$path = self::pathToUnix($path);

		if(is_dir($path) && $addEndingSlash == true)
			$path .= "/";

		return($path);
	}


	/**
	 * check if path under base path
	 */
	public static function isPathUnderBase($path, $basePath){
		$path = self::pathToUnix($path);
		$basePath = self::pathToUnix($basePath);

		if(strpos($path, $basePath) === 0)
			return(true);

		return(false);
	}


	/**
	 * find free filepath for copying. adding numbers at the end
	 * check filesize, if it's the same file, then return it.
	 */
	public static function findFreeFilepath($path, $filename, $filepathSource = null){

		//check if file exists
		$filepath = $path.$filename;
		if(file_exists($filepath) == false)
			return($filename);

		//check sizes
		$checkSizes = false;
		if(!empty($filepathSource)){
			$checkSizes = true;
			$sizeSource = filesize($filepathSource);

			$sizeDest = filesize($filepath);
			if($sizeSource == $sizeDest)
				return($filename);
		}


		//prepare file data
		$info = pathinfo($filename);
		$basename = $info["filename"];
		$ext = $info["extension"];

		//make new available filename
		$counter = 0;
		$textPortion = self::getStringTextPortion($basename);
		if(empty($textPortion))
			$textPortion = $basename."_";

		do{
			$counter++;
			$filename = $textPortion.$counter.".".$ext;
			$filepath = $path.$filename;
			$isFileExists = file_exists($filepath);

			if($isFileExists == true && $checkSizes == true){
				$sizeDest = filesize($filepath);
				if($sizeSource == $sizeDest)
					return($filename);
			}

		}while($isFileExists == true);


		return($filename);
	}

	public static function z__________SESSIONS_______(){}

	/**
	 * get session var
	 */
	public static function getSessionVar($name, $base){

		if(empty($base))
			UniteFunctionsUC::throwError("Can't get session var without the base");

		if(!isset($_SESSION))
			return("");

		$arrBase = UniteFunctionsUC::getVal($_SESSION, $base);

		if(empty($arrBase))
			return("");

		$value = UniteFunctionsUC::getVal($_SESSION, $name);

		return($value);
	}

	/**
	 * set session value
	 */
	public static function setSessionVar($name, $value, $base){

		if(empty($base))
			UniteFunctionsUC::throwError("Can't set session var without the base");

		if(!isset($_SESSION[$base]))
			$_SESSION[$base] = array();

		$_SESSION[$base][$name] = $value;
	}


	/**
	 * clear session var
	 */
	public static function clearSessionVar($name, $base){


		if(!isset($_SESSION[$base]))
			return(false);

		$_SESSION[$base][$name] = null;
		unset($_SESSION[$base][$name]);
	}


	public static function z___________TIME_AND_DATE__________(){}


	/**
	 * get time ago since now
	 */
	public static function getTimeAgoString($time_stamp){

		$time_difference = strtotime('now') - $time_stamp;

		//year
		if ($time_difference >= 60 * 60 * 24 * 365.242199)
			return self::getTimeAgoStringUnit($time_stamp, 60 * 60 * 24 * 365.242199, __('years',"unlimited-elements-for-elementor"),__('year',"unlimited-elements-for-elementor"));

		//month
		if ($time_difference >= 60 * 60 * 24 * 30.4368499)
			return self::getTimeAgoStringUnit($time_stamp, 60 * 60 * 24 * 30.4368499,__('months',"unlimited-elements-for-elementor"),__('month',"unlimited-elements-for-elementor"));

		//week
		if ($time_difference >= 60 * 60 * 24 * 7)
			return self::getTimeAgoStringUnit($time_stamp, 60 * 60 * 24 * 7, __('weeks',"unlimited-elements-for-elementor"),__('week',"unlimited-elements-for-elementor"));

		//day
		if ($time_difference >= 60 * 60 * 24)
			return self::getTimeAgoStringUnit($time_stamp, 60 * 60 * 24, __('days',"unlimited-elements-for-elementor"),__('day',"unlimited-elements-for-elementor"));

		//hour
		if($time_difference >= 60 * 60)
			return self::getTimeAgoStringUnit($time_stamp, 60 * 60, __('hours',"unlimited-elements-for-elementor") ,__('hour',"unlimited-elements-for-elementor"));

		//minute
		return self::getTimeAgoStringUnit($time_stamp, 60, __('minutes',"unlimited-elements-for-elementor"),__('minute',"unlimited-elements-for-elementor"));
	}


	/**
	 * get time ago string
	 */
	private static function getTimeAgoStringUnit($time_stamp, $divisor, $strUnit, $strUnitSingle){

		$time_difference = strtotime("now") - $time_stamp;
		$time_units      = floor($time_difference / $divisor);

		settype($time_units, 'string');

		if ($time_units === '0')
			$time_units = 1;

		if($time_units == 1)
			$output = $time_units . " ".$strUnitSingle." ". __("ago","unlimited-elements-for-elementor");
		else
			$output = $time_units . " ".$strUnit." ". __("ago","unlimited-elements-for-elementor");


		return($output);
	}



	//---------------------------------------------------------------------------------------------------
	// convert timestamp to time string
	public static function timestamp2Time($stamp){
		$strTime = date("H:i",$stamp);
		return($strTime);
	}

	/**
	 * convert timestamp to date and time string
	 */
	public static function timestamp2DateTime($stamp){
		$strDateTime = date("d M Y, H:i",$stamp);
		return($strDateTime);
	}

	//---------------------------------------------------------------------------------------------------
	// convert timestamp to date string
	public static function timestamp2Date($stamp){

		if(empty($stamp))
			return("");

		$strDate = date("d M Y",$stamp);	//27 Jun 2009
		return($strDate);
	}



	public static function z___________OTHERS__________(){}
	
	
	/**
	 * load xml file, get simplexml object back. 
	 * if not loaded - print the error
	 */
	public static function loadXMLFile($filepath){
		
		if(file_exists($filepath) == false)
			UniteFunctionsUC::throwError("xml file not found: ".$filepath);

		if(function_exists("simplexml_load_file") == false)
			UniteFunctionsUC::throwError("Your php missing SimpleXML Extension. The plugin can't work without this extension because it has many xml files to load. Please enable this extension in php.ini");
		
		$showErrors = false;
		
		if(function_exists("libxml_use_internal_errors")){
			$showErrors = true;
			libxml_use_internal_errors(true);			
			libxml_clear_errors();			
		}
		
		$obj = simplexml_load_file($filepath);
		
		if(empty($obj)){
			
			$xmlString = file_get_contents($filepath);
			
			if(empty($xmlString))
				UniteFunctionsUC::throwError("xml load: No content found in: ".$filepath);
			
			$obj = simplexml_load_string($xmlString);
		}
		
		/**
		 * throw the error
		 */
		if(empty($obj)){
			
			$errorsHTML = "Wrong xml file format: $filepath <br>";
			
			if($showErrors == true){
				
				$arrErrors = libxml_get_errors();
				
				if(empty($arrErrors))
					$arrErrors = array();
								
				foreach($arrErrors as $error){
					
					$line = $error->line;
					$column = $error->column;
					$message = $error->message;
					
		        	$errorsHTML .= "$message ($line, $column)  <br>\n";
				}
				
			}
			
			UniteFunctionsUC::throwError($errorsHTML);
		}
		
		
		return($obj);
	}
	
	
	/**
	 * disable deprecated warnings in php
	 */
	public static function disableDeprecatedWarnings(){

		$errorReporting = ini_get("error_reporting");

		if(is_numeric($errorReporting))
			ini_set("error_reporting", $errorReporting & ~E_DEPRECATED);
	}


	/**
	 * get youtube video id from url, or ID
	 */
	public static function getYoutubeVideoID($url){

		preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);

		if(empty($matches))
			return($url);

		if(count($matches) < 1)
			return("");

		$videoID = $matches[1];

		return($videoID);
	}

	/**
	 * get vimeo id from url
	 */
	public static function getVimeoIDFromUrl($url){


		if(is_numeric($url))
			return($url);

		if(strpos($url, "https://") === false)
			$url = "https://".$url;

				preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $matches);

				if(empty($matches))
					return($url);

				if(count($matches) < 4)
					return($url);

				$videoID = $matches[3];

				if(is_numeric($videoID) == false)
					return($url);


				return($videoID);
	}

	/**
	 * encode svg to bg image url
	 */
	public static function encodeSVGForBGUrl($svgContent){

		if(empty($svgContent))
			return("");

		$urlBG = "data:image/svg+xml;base64,".base64_encode($svgContent);

		return($urlBG);
	}


	/**
	 * get amount of memory limit in bytes
	 */
	public static function getPHPMemoryLimit(){

		if(isset(self::$arrCache["memory_limit"]))
			return(self::$arrCache["memory_limit"]);

		$memory_limit = ini_get("memory_limit");

		$found = preg_match('/^(\d+)(.)$/', $memory_limit, $matches);

		if(!$found)
			return(null);

		$numLimit = $matches[1];
		$letter = $matches[2];

		switch($letter){
			case "M":
						$memory_limit = $numLimit * 1024 * 1024;
			break;
			case "G":
						$memory_limit = $numLimit * 1024 * 1024 * 1024;
			break;
			case "K":
						$memory_limit = $numLimit * 1024;
			break;
		}

		self::$arrCache["memory_limit"] = $memory_limit;

		return($memory_limit);
	}


	/**
	 * return if the memory running off
	 */
	public static function isEnoughtPHPMemory($mbReserve = 32){

		$limit = self::getPHPMemoryLimit();
		if(empty($limit))
			return(true);

		//left this reserve
		$reserve = $mbReserve * 1024 * 1024;
		$maxReserve = $limit*0.3;

		if($reserve > $maxReserve);
			$reserve = $maxReserve;

		$available = $limit - $reserve;

		$used = memory_get_usage();

		/*
		dmp(number_format($available));
		dmp(number_format($used));
		dmp("-----------");
		*/

		if($used > $available)
			return(false);

		return(true);
	}




	/**
	 *
	 * strip slashes from textarea content after ajax request to server
	 */
	public static function normalizeTextareaContent($content){
		if(empty($content))
			return($content);
		$content = stripslashes($content);
		$content = trim($content);
		return($content);
	}

	/**
	 * Download Image
	 */
	public function downloadImage($filepath, $filename, $mimeType=""){
		$contents = file_get_contents($filepath);
		$filesize = strlen($contents);

		if($mimeType == ""){
			$info = UniteFunctionsUC::getPathInfo($filepath);
			$ext = $info["extension"];
			$mimeType = "image/$ext";
		}

		header("Content-Type: $mimeType");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Length: $filesize");
		echo UniteProviderFunctionsUC::escCombinedHtml($contents);
		exit();
	}

	/**
	 * download text file
	 */
	public static function downloadTextFile($text, $filename){

		$filesize = strlen($text);

		header("Content-Type: text");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Length: $filesize");
		echo UniteProviderFunctionsUC::escCombinedHtml($text);
		exit();
	}

	/**
	 * Download CSV
	 */
	public static function downloadCsv($filename, $headers, $rows){

		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Type: application/octet-stream');
		header('Content-Type: text/csv');

		$handler = fopen('php://output', 'w');

		// add BOM to fix UTF-8 in Excel
		fputs($handler, chr(0xEF) . chr(0xBB) . chr(0xBF));
		fputcsv($handler, $headers);

		foreach($rows as $row){
			$fields = array();

			foreach($headers as $key => $header){
				$fields[] = $row[$key];
			}

			fputcsv($handler, $fields);
		}

		fclose($handler);
		exit;
	}

	/**
	 * send file to download
	 */
	public static function downloadFile($filepath, $filename = null){

		UniteFunctionsUC::validateFilepath($filepath,"export file");

		if(empty($filename))
			$filename = basename($filepath);

		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filepath));
		readfile($filepath);
		exit();
	}


	/**
	 *
	 * convert string to boolean
	 */
	public static function strToBool($str){
		if(is_bool($str))
			return($str);

		if(empty($str))
			return(false);

		if(is_numeric($str))
			return($str != 0);

		$str = strtolower($str);
		if($str == "true")
			return(true);

		return(false);
	}

	/**
	 * bool to str
	 */
	public static function boolToStr($bool){
		$bool = self::strToBool($bool);

		if($bool == true)
			return("true");
		else
			return("false");
	}


	//------------------------------------------------------------
	// get black value from rgb value
	public static function yiq($r,$g,$b){
		return (($r*0.299)+($g*0.587)+($b*0.114));
	}


	/**
	 * check if empty color string
	 */
	public static function isEmptyColorString($color){
		$color = trim($color);

		if(empty($color))
			return(true);

		$color = strtolower($color);
		if(strpos($color, "nan") !== false)
			return(true);

		return(false);
	}

	/**
	 * sanitize color string
	 */
	public static function sanitizeColorString($color){

		if(self::isEmptyColorString($color) == true)
			return("");

		return($color);
	}

	/**
	 * convert colors to rgb
	 */
	public static function html2rgb($color){

		if(empty($color))
			return(false);

		if ($color[0] == '#')
			$color = substr($color, 1);
		if (strlen($color) == 6)
			list($r, $g, $b) = array($color[0].$color[1],
					$color[2].$color[3],
					$color[4].$color[5]);
		elseif (strlen($color) == 3)
		list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		else
			return false;
		$r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
		return array($r, $g, $b);
	}

	/**
	 *
	 *turn some object to string
	 */
	public static function toString($obj){
		return(trim((string)$obj));
	}


	/**
	 *
	 * remove utf8 bom sign
	 * @return string
	 */
	public static function remove_utf8_bom($content){
		$content = str_replace(chr(239),"",$content);
		$content = str_replace(chr(187),"",$content);
		$content = str_replace(chr(191),"",$content);
		$content = trim($content);
		return($content);
	}


	/**
	 * print the path to this function
	 */
	public static function printPath(){

		try{
			throw new Exception("We are here");
		}catch(Exception $e){
			dmp($e->getTraceAsString());
			exit();
		}

	}

	/**
	 * return if the url coming from localhost
	 */
	public static function isLocal(){

		if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] == "localhost")
			return(true);

		return(false);
	}

	/**
	 * redirect to some url
	 */
	public static function redirectToUrl($url){

		header("location: $url");
		exit();
	}

	/**
	 * get user's user agent
	 */
	public static function getUserAgent(){

		return $_SERVER["HTTP_USER_AGENT"];
	}

	/**
	 * get user's ip address
	 */
	public static function getUserIp(){

		$keys = array(
			"HTTP_CLIENT_IP",
			"HTTP_X_FORWARDED_FOR",
			"HTTP_X_FORWARDED",
			"HTTP_X_CLUSTER_CLIENT_IP",
			"HTTP_FORWARDED_FOR",
			"HTTP_FORWARDED",
			"REMOTE_ADDR",
		);

		foreach($keys as $key){
			$value = UniteFunctionsUC::getVal($_SERVER, $key);

			if($value && filter_var($value, FILTER_VALIDATE_IP)){
				return $value;
			}
		}

		// fallback to local ip
		return "127.0.0.1";
	}

}
