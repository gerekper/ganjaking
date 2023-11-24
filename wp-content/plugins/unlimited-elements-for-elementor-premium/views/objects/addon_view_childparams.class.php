<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class UniteCreatorAddonViewChildParams{
	
	const PARAM_PREFIX = "[param_prefix]";
	const PARAM_NAME = "[param_name]";
	const PARENT_NAME = "[parent_name]";
	
	
	/**
	 * create child param
	 */
	protected function createChildParam($param, $type = null, $addParams = false){
		
		$arr = array("name"=>$param, "type"=>$type);
				
		if(!empty($addParams))
			$arr = array_merge($arr, $addParams);
		
		return($arr);
	}
	
	
	
	/**
	 * create add param
	 */
	private function createAddParam($param = null,$addParams = array()){
		
		if(empty($addParams)){
			
			$addParams = array(
				"rawvisual"=>true,
			);
			
			if(!empty($param)){
				if($param == "|raw")
					$param = self::PARENT_NAME."|raw";
				else
					$param = self::PARENT_NAME."_".$param;
			}
			
		}
		
		$type = null;
		
		$arr = array("name"=>$param, "type"=>$type);
		$arr = array_merge($arr, $addParams);
		
		return($arr);
	}
	
	/**
	 * create child param
	 */
	protected function createChildParam_code($key, $text, $noslashes = false, $rawvisual = true){
		
	    $arguments = array(
		    	"raw_insert_text" => $text, 
		    	"rawvisual"=>$rawvisual,
	    	);		
		
	    if($noslashes == true)
	    	 $arguments["noslashes"] = true;
	    
	    	
	    $arr = $this->createChildParam($key, null, $arguments);		
		
		return($arr);
	}
	
	/**
	 * get code example php params
	 */
	protected function getCodeExamplesParams_php($arrParams){
		
			$key = "Run PHP Action (pro)";
			$text = "
			
{# This functionality exists only in the PRO version #}
{# run any wp action, and any custom PHP function. Use add_action to create the actions. \n The function support up to 3 custom params #}
\n
{{ do_action('some_action') }}
{{ do_action('some_action','param1','param2','param3') }}
";
		
	//-------- data from php -------------
			
			$arrParams[] = $this->createChildParam_code($key, $text);
		
			$key = "Data From PHP Filter(pro)";
			$text = "
{# This functionality exists only in the PRO version #}			
{# apply any WordPress filters, and any custom PHP function. Use apply_filters to create the actions. \n The function support up to 2 custom params #}
\n
{% set myValue = apply_filters('my_filter') %}
{% set myValue = apply_filters('my_filter',value, param2, param3) %}

";
			$arrParams[] = $this->createChildParam_code($key, $text);

	//-------- run php get functoin -------------
			
		
			$key = "getByPHPFunction(pro)";
			$text = "
{# Run any custom php function that starts with \"get_\". #}
{# Can take any number of arguments. Look the examples #}

{% set postData = getByPHPFunction('get_post') %}
{% set postMeta = getByPHPFunction('get_post_meta',15) %}
			
";
			$arrParams[] = $this->createChildParam_code($key, $text);
			
		
		return($arrParams);
	}
	
	/**
	 * get post child params
	 */
	public function getChildParams_codeExamples(){
		
		$arrParams = array();
		
		//----- show data --------
		
		$key = "showData()";
		$text = "
{# This function will show all data in that you can use #} \n 
{{showData()}}
";
		
		$arrParams[] = $this->createChildParam_code($key, $text);
		
		//---- show debug -----
		
		$key = "showDebug()";
		$text = "
{# This function show you some debug (with post list for example) #} \n 
{{showDebug()}}
";
		
		$arrParams[] = $this->createChildParam_code($key, $text);

		//---- printVar -----
		
		$key = "printVar()";
		$text = "
{# This function will print any variable #} \n	
{{printVar(somevar)}}
";
		
		$arrParams[] = $this->createChildParam_code($key, $text);

		//---- print json variable -----
		
		$key = "printJsonVar()";
		$text = "
{# This function will print json encoded variable for javascript use #} \n	
{{printJsonVar(somevar)}}
";
		
		//---- print json html data -----
		
		$key = "printJsonHtmlData()";
		$text = "
{# This function will print json html data json encoded and special chars convert for data-something=\"\" in html #} \n	
{{printJsonHtmlData(somevar)}}
";
		
		$arrParams[] = $this->createChildParam_code($key, $text);
		
		
		
		//------ if empty ------
		
		$key = "IF Empty";
		$text = "
{% if some_attribute is empty %}
	<!-- put your empty case html -->   
{% else %} 
	<!-- put your not empty html here -->   
{% endif %}	
";
		
		$arrParams[] = $this->createChildParam_code($key, $text);
		
		//----- simple if ------
		
		$key = "IF";
		$text = "
{% if some_attribute == \"some_value\" %}
	<!-- put your html here -->   
{% endif %}	
";
		$arrParams[] = $this->createChildParam_code($key, $text);
		
		
		//----- if else ------
		
		$key = "IF - Else";
		$text = "
{% if product_stock == 0 %}
	<!-- not available html -->   
{% else %}
	<!-- available html -->
{% endif %}
";
		$arrParams[] = $this->createChildParam_code($key, $text);
		
		
		//----- complex if ------
		
		$key = "IF - Else - Elseif";
		$text = "
{% if product_stock == 0 %}
	<!-- put your 0 html here -->   
{% elseif product_stock > 0 and product_stock < 20 %}
	<!-- put your 0-20 html here -->   
{% elseif product_stock >= 20 %}
	<!-- put your >20 html here -->   
{% endif %}	
";

		$arrParams[] = $this->createChildParam_code($key, $text);


		//----- for in (loop) ------
		
		$key = "For In (loop)";
		$text = "
{% for product in woo_products %}
	
	<!-- use attributes inside the product, works if the product is array -->   
	<span> {{ product.title }} </span>
	<span> {{ product.price }} </span>
	
{% endfor %}	
";
		
		$arrParams[] = $this->createChildParam_code($key, $text);
		
		//----- html output raw filter ------
		
		$key = "HTML Output - |raw";
		$text = "
{# use the raw filter for printing attribute with html tags#}
{{ some_attribute|raw }}
";

		//----- Json Decode ------
		
		$key = "JSON Decode";
		$text = "
	  {% set arr = jsonvar|json_decode %}
	  {{arr.yourkey}}
";
		
		
		$arrParams[] = $this->createChildParam_code($key, $text);

		//----- truncate text filter ------
		
		$key = "Truncate Text Filter - |truncate";
		$text = "
{# use the truncate filter for lower the text length. arguments are: (numchars, preserve words(true|false), separator=\"...\")#}
{{ some_attribute|truncate }}
{{ some_attribute|truncate(50) }}
{{ some_attribute|truncate(100, false) }}
{{ some_attribute|truncate(150, true, \"...\") }}
";
		
		$arrParams[] = $this->createChildParam_code($key, $text);

		$key = "Date Functions";
		$text = "
{# use the ucdate filter to convert timestamps to dates preserving wordpress format#}
{{ your_timestamp|ucdate(\"m/d/Y\") }}

{# also, use the ucdate filter to convert strings from one format to another #}
{{ your_date_string|ucdate(\"m/d/Y\",\"d-m-Y\") }}


{# to show data from - to range like 4-5 Mar 2021 use this function#}
{{ucfunc(\"put_date_range\",1617187095,1619187095)}}

";

		$arrParams[] = $this->createChildParam_code($key, $text);
		
		
		//----- default value ------
		
		$key = "Default Value";
		$text = "
{# use the default value filter in case that no default value provided (like in acf fields) #}
{{ cf_attribute|default('text in case that not defined') }}
";

		$arrParams[] = $this->createChildParam_code($key, $text);

		//----- get listing item data ------
		
		$key = "getListingItemData()";
		$text = "
{# This function gets the data if the widget inside template that are item in any listing of any plugin \n
   For type the only option for now is: \"user\", and for ID, default user ID.
 #} \n

{% set listingData = getListingItemData(type=\"\", default_id=\"\") %}

{{printVar(listingData)}}
";
		
		$arrParams[] = $this->createChildParam_code($key, $text);
		
		//----- get user data ------
		
		$arrParams = $this->getCodeExamplesParams_php($arrParams);

		$key = "getUserData()";
		$text = "
{# Use this function this way:  getUserData(username, getMeta=true/false, getAvatar=true/false) #}
{# for current logged in user use username: 'loggedin_user' #}

\n
{% set userData = getUserData('admin',true, true) %}
{{printVar(userData)}}

";
		
		$arrParams[] = $this->createChildParam_code($key, $text);

		//----- get user data ------
		

		$key = "getPostData()";
		$text = "
{# Use this function this way:  getPostData(postID, getCustomFields=true/false (optional), getCategories=true/false (optional) ) #}
\n
{% set postData = getPostData(15) %}   {# put a real post id here #}
{{printVar(postData)}}

{% set postData2 = getPostData(15, true, true) %}   
{{printVar(postData2)}}


";
		
		$arrParams[] = $this->createChildParam_code($key, $text);
		

		//----- get data from sql query ------
		
		$key = "get_from_sql()";
		$text = "
{# Get data from sql query. recieve up to 2 arguments, sprintf format, that can be attributes as well #}

{%set items = ucfunc(\"get_from_sql\", \"select * from %s limit %s\", \"wp_terms\", \"10\") %}

{{printVar(items)}};

";
		
		$arrParams[] = $this->createChildParam_code($key, $text);
		
		//----- run code once ------
		
		$key = "run_code_once()";
		$text = "

{# This function to allow to run any code only once per page. if you put 10 widgets for example, this output put only once. #}
{# each not repeatable code should have it's own key #}

{% if ucfunc(\"run_code_once\", \"yourkey\") == true %}

	This text you will see once! You will not see this text, in another widget on the page.

{% endif %}
";
		
		$arrParams[] = $this->createChildParam_code($key, $text);

		//----- set and get ------
		
		$key = "set(), get()";
		$text = "
{# remember variable in html editor, and use it in other editors, via get #}

{{ ucfunc(\"set\",\"my-var\", var) }} 

{% set var2 = ucfunc(\"get\",\"my-var\") %}
";
		
		$arrParams[] = $this->createChildParam_code($key, $text);
		
		//----- last query data ------
		
		$key = "get_last_query_data()";
		$text = "
{# get last post query data #}

{% set query_data = ucfunc(\"get_last_query_data\") %}

{{query_data.total_posts}}

{{printVar(query_data)}}
";
		
		$arrParams[] = $this->createChildParam_code($key, $text);
		
		//----- get current user ------

		$key = "get_current_user()";
		$text = "
{# get the current logged in user. If not logged in then return null. argument 2 - get also current user meta #}

{% set user = ucfunc(\"get_current_user\", true) %}

{% if user is empty %}
		no user loggedin
{% else %}

  {{user.name}}

{{printVar(user)}}

{% endif %}
";
		
		$arrParams[] = $this->createChildParam_code($key, $text);
		
		//----- get term image ------
		
		$key = "get_term_image()";
		$text = "
{# get the image fields out of the term.  ucfunc(\"get_term_image\",id,metakey) #}
{# if id is null - get from current term #}

{% set image = ucfunc(\"get_term_image\",10,\"attachment_id\") %}

{% if image is not empty %}
	{{printVar(image)}}

{% else %}

	no image found

{% endif %}
";
		
		$arrParams[] = $this->createChildParam_code($key, $text);

		//----- get custom field ------
		
		$key = "get_custom_field()";
		$text = "
{# get post or term custom field. use when you have term or post id #}

{% set postMetaValue = ucfunc(\"get_post_custom_field\",post_id,\"fieldname\") %}

{% set termMetaValue = ucfunc(\"get_term_custom_field\",term_id,\"fieldname\") %}

{# also you can debug the fields #}

{{ucfunc(\"put_terms_meta_debug\",taxonomy)}}

{{ucfunc(\"put_post_meta_debug\",taxonomy)}}


";
		
		$arrParams[] = $this->createChildParam_code($key, $text);
		

		//----- hide id's in css ------

		$key = "put_hide_ids_css()";
		$text = "
{# this used from item css tab. argument it's the id's attribute #}

{{ ucfunc(\"put_hide_ids_css\", arg) }}
";
		
		$arrParams[] = $this->createChildParam_code($key, $text);

		//----- put items schema ------

		$key = "put_schema_items_json()";
		$text = "
{# the faq schema used in content items widgets, good for SEO #}

{{ ucfunc(\"put_schema_items_json\") }}
";

		$arrParams[] = $this->createChildParam_code($key, $text);
		
		
		
		//----- output ------
		
		return($arrParams);
	}
	
	

	/**
	 * get post child params
	 */
	public function getChildParams_codeExamplesJS(){
		
		$arrParams = array();
		
		//----- show data --------
		
		$key = "jQuery(document).ready()";
		$text = " 
jQuery(document).ready(function(){

	/* put your code here, a must wrapper for every jQuery enabled widget */

});
		";
		$arrParams[] = $this->createChildParam_code($key, $text);
		
		
		//----- code wrapper for popup --------
		
		$key = "Code Wrapper for Elementor Popup";
		$text = "{{ ucfunc(\"put_docready_start\") }}
		
      	
      	/* Your code should start from this function 
        	make sure you have some element with {{uc_id}} in html, example: <div id=\"{{uc_id}}\">
        */ 
        
    	console.log(\"This code works!\"); 
    	
{{ ucfunc(\"put_docready_end\") }}";
		$arrParams[] = $this->createChildParam_code($key, $text);
		
		
		//----- putItemsJson() --------
		
		
		$key = "putItemsJson()";
		$text = "
var strJson = {{put_items_json()}};
var arrItems = JSON.parse(strJson);

console.log(arrItems);
		";
		
		$arrParams[] = $this->createChildParam_code($key, $text);

		
		//----- putAttrebutesJson() --------
		
		$key = "putAttributesJson()";
		$text = "\n{# the options:  put_attributes_json(\"clean\"(optional),\"key\"(optional)) #}	\n
		
var strJsonAttr = {{put_attributes_json()}};
var arrAttributes = JSON.parse(strJsonAttr);

console.log(arrAttributes);


//example with a single attribute

var strJsonAttrSpecific = {{put_attributes_json(null, \"key\")}};
var arrAttribute = JSON.parse(strJsonAttrSpecific);

console.log(arrAttribute);

		";
		
		$arrParams[] = $this->createChildParam_code($key, $text);

		//----- putItemsJson() --------
		
		
		$key = "csvToJson()";
		$text = "
{# converts csv attribute to javascript json output #}

var json = {{ucfunc(\"csv_to_json\",yourattribute)}};
		";
		
		$arrParams[] = $this->createChildParam_code($key, $text);
		
		
		return($arrParams);
	}
	
	
	/**
	 * create category child params
	 */
	public function createChildParams_category($arrParams){
		
		$arrParams[] = $this->createChildParam("category_id");
		$arrParams[] = $this->createChildParam("category_name");
		$arrParams[] = $this->createChildParam("category_slug");
		$arrParams[] = $this->createChildParam("category_link");
		$arrParams[] = $this->createChildParam("category_image");
		
		//create categories array foreach
		
		$strCode = "";
		$strCode .= "{% for cat in [param_prefix].categories %}\n";

		$strCode .= "	<span> {{cat.id}} , {{cat.name}} , {{cat.slug}} , {{cat.description}}, {{cat.link}} </span> <br>\n\n ";
		
		$strCode .= "	{# also you can use category custom fields #} \n";
		$strCode .= "	{% set custom_fields = getTermCustomFields(cat.id) %} \n";
		$strCode .= "	{{custom_fields.cf_fieldname}} \n\n";
		
		$strCode .= "{% endfor %}\n";

		
	    $arrParams[] = $this->createChildParam("categories", null, array("raw_insert_text" => $strCode));		
		
	    $arrParams = $this->getChildParams_termCustomFields($arrParams);
	    $arrParams = $this->getChildParams_termMeta($arrParams);
		
		return($arrParams);
	}
	
	/**
	 * create child param with underscore
	 */
	protected function createChildParam_underscore($key){
		
		$value = "{{".self::PARAM_PREFIX."_$key}}";
		
		if(empty($key))
			$value = "{{".self::PARAM_PREFIX."}}";
		
		$param = $this->createChildParam($value, null, array(
		    	"raw_insert_text" => $value, 
		    	"rawvisual"=>true,
		));
		
		return($param);
	}
	
	/**
	 * add image child params
	 */
	public function getChildParams_image(){
		
		$arrParams = array();
		
		$arrParams = $this->addPostImageChildParams($arrParams, true);
		
		
		return($arrParams);
	}
	
	
	private function __________POST_FIELDS_________(){}
		
	/**
	 * get post add data code
	 */
	protected function getPostItemAddDataCode(){
		
		$str = "\n{# get additional data , the function defenition is:  getPostData(postID, getCustomFields [true/false] , getCategory [true/false])  #}\n\n";
		$str .= "{% set postData = getPostData(item.post_id, false, false) %}\n\n";
		$str .= "{{postData[\"image\"]}}\n\n";
		$str .= "{{printVar(postData)}}\n\n";
		
		return($str);
	}
	
	
	/**
	 * get flexible content value code
	 */
	private function getStrCodeItemFlexibleContent($arrValues, $childKey){
		
		if(!is_array($arrValues))
			return("");
		
		$strCode = "";
		
		foreach($arrValues as $key => $item){
									
			if(is_array($item) == false)
				return(false);
				
			$layoutType = UniteFunctionsUC::getVal($item, "acf_fc_layout");
			
			if(empty($layoutType))
				return("");
			
			$addText = "";
				
			if($key > 0)
				$addText = "else";
				
			$strCode .= "		{% {$addText}if {$childKey}.acf_fc_layout == \"$layoutType\" %}\n";
			
			unset($item["acf_fc_layout"]);
			unset($item["item_index"]);
			$exampleField = UniteFunctionsUC::getFirstNotEmptyKey($item);
			
			if(!empty($exampleField)){
				$strCode .= "			$layoutType flex type\n";
				$strCode .= "			{{ {$childKey}.{$exampleField} }}\n";
				
			}
			else 
				$strCode .= "		<!-- write your code here -->";
			
		}
			
		$strCode .= "		{% endif %}\n\n";
		
		return($strCode);
	}
	
	
	/**
	 * get array inside repeater code
	 */
	private function getCustomFieldRepeaterArrayText($arrValues, $key){
		
		if(empty($arrValues))
			return("");
			
		if(is_array($arrValues) == false)
			return($arrValues);
		
		if(isset($arrValues[0]) == false){
			$text = "		{{printVar([param_prefix].{$key})}}";
			
			return($text);
		}
			
		$strCode = "";
		$strCode .= "	{% for child_item in item.$key %}\n\n";
		
		$strCode .= "		{{printVar(child_item)}}\n\n";
		
		$strCode .= $this->getStrCodeItemFlexibleContent($arrValues, "child_item");
		
		//check for flexible content
		$strCode .= "	{% endfor %}\n";
		
		return($strCode);
	}
	
	/**
	 * get custom field key text
	 */
	private function getCustomFieldKeyText($type, $key){
		
		//complex code (repeater) 
		
		if(is_array($type)){
			
			$strCode = "";
			$strCode .= "{% for item in [param_prefix].{$key} %}\n";
			
			$typeKeys = array_keys($type);
			
			foreach($type as $postItemKey => $value){
								
				if($postItemKey == "put_post_add_data"){
					$strCode .= $this->getPostItemAddDataCode();
					continue;
				}
				
				if(is_array($value)){		//array inside the repeater
										
					$childText = $this->getCustomFieldRepeaterArrayText($value, $postItemKey);
					
					$strCode .= "<div>\n {$childText} \n</div>\n";
					
					continue;
				}
								
				$strCode .= "<span> {{item.$postItemKey}} </span>\n";
				
			}
			
			$strCode .= "{% endfor %}\n";
			
			return($strCode);
		}
		
		//--- simple array code 
		
		if($type == "array"){
						
			$strCode = "";
			$strCode .= "{% for item in [param_prefix].{$key} %}\n";
			$strCode .= "<span> {{item}} </span>\n";
			$strCode .= "{% endfor %}\n";
			
			return($strCode);
		}
		
		if($type == "empty_repeater"){
			
			$strText = "<!-- Please add some values to this field repeater in demo post in order to see the fields here -->";
			
		    return($strText);
		}
		
		return(null);
	
	}
	
	/**
	 * add custom fields
	 */
	protected function addCustomFieldsParams($arrParams, $postID){
		
		if(empty($postID))
			return($arrParams);
		
		$isAcfExists = UniteCreatorAcfIntegrate::isAcfActive();
		
		$prefix = "cf_";
			
		//take from pods
		$isPodsExists = UniteCreatorPodsIntegrate::isPodsExists();
		
		$takeFromACF = true;
		if($isPodsExists == true){
			$arrMetaKeys = UniteFunctionsWPUC::getPostMetaKeys_PODS($postID);
			if(!empty($arrMetaKeys))
				$takeFromACF = false;
		}
		
		//take from toolset
		$isToolsetExists = UniteCreatorToolsetIntegrate::isToolsetExists();
		if($isToolsetExists == true){
			
			$objToolset = new UniteCreatorToolsetIntegrate();
			$arrMetaKeys = $objToolset->getPostFieldsKeys($postID);
			$takeFromACF = false;
		}
		
		
		//acf custom fields
		if($isAcfExists == true && $takeFromACF == true){
			
			$arrMetaKeys = UniteFunctionsWPUC::getAcfFieldsKeys($postID);
			$title = "acf field";
			
			if(empty($arrMetaKeys))
				return($arrParams);
			
			$firstKey = UniteFunctionsUC::getFirstNotEmptyKey($arrMetaKeys);
			
			foreach($arrMetaKeys as $key=>$type){
				
				$customText = $this->getCustomFieldKeyText($type, $key);
				
				if(!empty($customText)){		//complex param
				    $arrParams[] = $this->createChildParam($key, null, array("raw_insert_text"=>$customText));				    
				}
				else	//simple param
					$arrParams[] = $this->createChildParam($key);
			}
			
			
		}else{	//regular custom fields
			
			//should be $arrMetaKeys from pods
			
			if(empty($arrMetaKeys))
				$arrMetaKeys = UniteFunctionsWPUC::getPostMetaKeys($postID, "cf_");
			
			$title = "custom field";
			
			if(empty($arrMetaKeys))
				return($arrParams);
			
			$firstKey = $arrMetaKeys[0];
				
			foreach($arrMetaKeys as $key)
				$arrParams[] = $this->createChildParam($key);
			
		}
		
		//add functions
		$arrParams[] = $this->createChildParam("$title example with default",null,array("raw_insert_text"=>"{{ [param_prefix].$firstKey|default('default text') }}"));
				
		
		return($arrParams);
	}
	
	/**
	 * add put post meta function params
	 */
	private function getChildParams_post_addPostMeta($arrParams){
		
		$strText = "{# Put post meta value #} \n\n";
		
		$strText .= "{{putPostMeta([param_prefix].id,\"metakey\")}} \n\n";
		
		$strText .= "{# Set variable with post meta value, and use it later #} \n\n";
		
		$strText .= "{% set myField = getPostMeta([param_prefix].id,\"metakey\") %} \n";
		$strText .= "{{myField}} \n\n";
		
		$strText .= "{# Print all post meta data #} \n\n";
		
		$strText .= "{{printPostMeta([param_prefix].id)}} \n";
		
		$arrParams[] = $this->createChildParam("putPostMeta", null, array("raw_insert_text"=>$strText));
		
		return($arrParams);
	}

	
	/**
	 * add put post meta function params
	 */
	private function getChildParams_post_getImageFromMeta($arrParams){

		$strText = "{# get image data from post meta field arg1: postID, arg2: post meta name #} \n\n";
		
		$strText .= "{% set image = ucfunc(\"get_post_image\",[param_prefix].id,\"myimageid\") %} \n\n";
		
		$strText .= "{{image.thumb}} \n\n";
		
		$strText .= "{# to print the return data #} \n";
		$strText .= "{{printVar(image)}} \n\n";
		
		$strText .= "{# to debug the meta field write: \"debug\" in place of meta field #} \n";
		$strText .= "{# set image = ucfunc(\"get_post_image\",current_post.id,\"debug\") #}\n\n";
		
		$arrParams[] = $this->createChildParam("getImageFromMeta", null, array("raw_insert_text"=>$strText));
		
		return($arrParams);
	}
	
	
	/**
	 * put html data
	 */
	private function getChildParams_post_putHtmlData($arrParams){
		
		$strText = "\n<div data-postdata=\"{{printJsonHtmlData([param_prefix])}}\"></div>";
		
		$arrParams[] = $this->createChildParam("printJsonHtmlData", null, array("raw_insert_text"=>$strText));
		
		return($arrParams);
	}

	/**
	 * get number of post comments
	 */
	private function getChildParams_post_numComments($arrParams){
		
		$strText = "\n";
		$strText .= "{% set num_comments = ucfunc(\"get_num_comments\", [param_prefix].id) %} \n\n";
		$strText .= "{{num_comments}} \n\n";
		
		$arrParams[] = $this->createChildParam("num_comments", null, array("raw_insert_text"=>$strText));
		
		return($arrParams);
	}
	
	
	/**
	 * get custom fields text
	 */
	private function getTermCustomFieldsText($field = null, $linePrefix = ""){
		
		if(empty($field))
			$field = "[param_prefix].category_id";
				
		$strText = $linePrefix."{% set custom_fields = getTermCustomFields($field) %} \n\n";
						
		$strText .= $linePrefix."{{custom_fields.cf_fieldname}} \n\n";
		
		$strText .= $linePrefix."{{printVar(custom_fields)}} \n\n";
				
		return($strText);
	}
	
	/**
	 * add put post meta function params
	 */
	private function getChildParams_termCustomFields($arrParams){
		
		$strText = $this->getTermCustomFieldsText();
		
		$arrParams[] = $this->createChildParam("categoryCustomFields", null, array("raw_insert_text"=>$strText));
		
		return($arrParams);
	}
	
	/**
	 * add term meta function output
	 */
	private function getChildParams_termMeta($arrParams, $field = null, $linePrefix = ""){
		
		if(empty($field))
			$field = "[param_prefix].category_id";
		
		$strText = $linePrefix."{% set meta_fields = getTermMeta($field) %} \n\n";
		
		$strText .= $linePrefix."{{meta_fields.fieldname}} \n\n";
				
		$strText .= $linePrefix."{{printVar(meta_fields)}} \n\n";
		
		$arrParams[] = $this->createChildParam("categoryMetaFields", null, array("raw_insert_text"=>$strText));
		
		return($arrParams);
	}
	
	
	/**
	 * add post terms function
	 */
	private function getChildParams_post_addTerms($arrParams){
		
		$strCode = "";
		$strCode .= "{# to show all terms for debug use: {{ ucfunc(\"put_post_terms_string\") }} #}\n\n";
		$strCode .= "{# for get with custom fields write \"true\" in 3-th attribute: getPostTerms([param_prefix].id, \"post_tag\", true) #}\n\n";

		$strCode .= "{% set terms = getPostTerms([param_prefix].id, \"post_tag\", false) %}\n\n";
		
		$strCode .= "{% for term in terms %}\n\n";
		$strCode .= "	{{term.id}}, {{term.name}}, {{term.slug}}\n";
		$strCode .= "	{{printVar(term)}}\n\n";

		$strCode .= "	{# also you can use term meta fields #} \n";
		$strCode .= "	{% set meta_fields = getTermMeta(term.id) %} \n";
		$strCode .= "	{{meta_fields.fieldname}} \n\n";
		
		$strCode .= "{% endfor %}\n\n";
		
		//single term
		
		$strCode .= "{# get single term. arg1 - posid, arg2 - taxonomy, arg3 - slug #}\n";
		
		$strCode .= "{% set single_term = ucfunc(\"get_post_term\",item.posts.id,\"category\",\"termslug\") %}\n\n";
		
		$strCode .= "{# return \"yes\" / \"no\" string #} \n\n";
		$strCode .= "{% set isExists = ucfunc(\"is_post_has_term\",item.posts.id,\"category\",\"single_term\") %} \n\n";
		
		$strCode .= "{{printVar(single_term)}} \n";
		$strCode .= "{{isExists}} \n\n";
		
		
	    $arrParams[] = $this->createChildParam("putPostTerms", null, array("raw_insert_text"=>$strCode));
		
		return($arrParams);
	}

	
	/**
	 * add post terms function
	 */
	private function getChildParams_post_addAuthor($arrParams){
		
		$strCode = "{# Use this function this way:  getPostAuthor(username, getMeta=true/false, getAvatar=true/false) #}\n\n";
		$strCode .= "{% set author = getPostAuthor([param_prefix].author_id) %}\n\n";
		$strCode .= "{{author.id}} {{author.name}} {{author.email}}\n\n";
		$strCode .= "{% set author2 = getPostAuthor([param_prefix].author_id,true,true) %}\n\n";
		$strCode .= "<img src=\"{{author2.avatar_url|raw}}\">\n\n";
		$strCode .= "{{printVar(author2)}}\n";
		
	    $arrParams[] = $this->createChildParam("getPostAuthor", null, array("raw_insert_text"=>$strCode));
		
		return($arrParams);
	}
	
	
	/**
	 * create add child products param
	 */
	private function createWooPostParam_getChildProducts(){
				
		$strCode = "";
		$strCode .= "{# Get child products for 'grouped' product type. The function defenition is:  getWooChildProducts(postID, getCustomFields [true/false] , getCategory [true/false])  #}\n\n";
		
		$strCode .= "{% set child_products = getWooChildProducts([param_prefix].id, false, false) %}\n\n";
		
		$strCode .= "{% for product in child_products %}\n\n";
		
		$strCode .= "	Child Product: {{ product.title }}<br>\n\n";
		
		$strCode .= "	{# For other fields please look at output of this function #}<br>\n ";
		$strCode .= "	{{printVar(product)}} <br>\n\n ";
		
		$strCode .= "{% endfor %}\n";
		
	    $arrParam = $this->createChildParam("getWooChildProducts", null, array("raw_insert_text"=>$strCode));
		
	    return($arrParam);
	}

	/**
	 * create add child products param
	 */
	private function createWooPostParam_putProductGallery(){
		
		$strCode = "\n";
		$strCode .= "{%set gallery = ucfunc(\"get_wc_gallery\", [param_prefix].id) %}\n\n";
		$strCode .= "{% for gallery_item in gallery %}\n\n";
		$strCode .= "<!--\n";
		$strCode .= "{{gallery_item.image}}\n";
		$strCode .= "{{gallery_item.thumb_large}}\n";
		$strCode .= "{{gallery_item.title}}\n";
		$strCode .= "-->\n";
		$strCode .= "<img src=\"{{gallery_item.thumb}}\">\n";
		$strCode .= "{% endfor %}\">\n";
		
	    $arrParam = $this->createChildParam("putWooProductGallery", null, array("raw_insert_text"=>$strCode));
		
	    return($arrParam);
	}

	/**
	 * create add child products param
	 */
	private function createWooPostParam_getEndpoints(){
		
		$strCode = "\n";
		$strCode .= "{%set url_cart = ucfunc(\"get_woo_endpoint\",\"cart\") %}\n";
		$strCode .= "{%set url_checkout = ucfunc(\"get_woo_endpoint\",\"checkout\") %}\n";
		$strCode .= "{%set url_myaccount = ucfunc(\"get_woo_endpoint\",\"myaccount\") %}\n";
		$strCode .= "{%set url_shop = ucfunc(\"get_woo_endpoint\",\"shop\") %}\n\n";
		
		$strCode .= "{{url_cart}}\n";
		$strCode .= "<br>\n";
		$strCode .= "{{url_checkout}}\n";
		$strCode .= "<br>\n";
		$strCode .= "{{url_myaccount}}\n";
		$strCode .= "<br>\n";
		$strCode .= "{{url_shop}}\n\n";
		
		
	    $arrParam = $this->createChildParam("getWooEndpoints", null, array("raw_insert_text"=>$strCode));
		
	    return($arrParam);
	}
	
	
	/**
	 * create add child products param
	 */
	private function createWooPostParam_putVariations(){
		
		$strCode = "";
		$strCode .= "{# The variations exists only at variable products  #}\n\n";
		
		$strCode .= "{% set variations = ucfunc(\"get_wc_variations\",[param_prefix].id) %}\n\n";
		
		$strCode .= "{% for variation in variations %}\n\n";
		
		$strCode .= "	Title: <b>{{ variation.title }}</b> <br>\n\n";
		$strCode .= "	Title key for rename: <b>{{ variation.title_key }}</b><br>\n\n";
		$strCode .= "	Title parts for self combine: {{ printVar(variation.title_parts) }}<br>\n\n";
		$strCode .= "	Price Html: {{variation.price_html|raw}}<br>\n\n";
		$strCode .= "	Sku: <b>{{variation.sku}}</b><br>\n\n";
		$strCode .= "	ID: <b>{{variation.variation_id}}</b><br>\n\n";
		$strCode .= "	Link add to cart: {{variation.link_addcart_cart}}<br>\n\n";
		
		$strCode .= "	{# For other fields please look at output of this function #}<br>\n ";
		$strCode .= "	{{ printVar(variation) }} <br>\n\n ";
		
		$strCode .= "{% endfor %}\n";
		
	    $arrParam = $this->createChildParam("putWooVariations", null, array("raw_insert_text"=>$strCode));
		
	    return($arrParam);
	}
	
	/**
	 * create add child products param
	 */
	private function createWooPostParam_putAttributes(){
		
		$strCode = "";
		
		$strCode .= "{%set attributes = ucfunc(\"get_product_attributes\",[param_prefix].id) %}\n\n";
		
		$strCode .= "{% for attribute in attributes %}\n";
		$strCode .= "  <p>{{attribute}}</p>\n";
		$strCode .= "{% endfor %}\n";
		
	    $arrParam = $this->createChildParam("putWooAttributes", null, array("raw_insert_text"=>$strCode));
		
	    return($arrParam);
	}
	
	
	
	/**
	 * check and add woo post params
	 */
	private function checkAddWooPostParams($postID, $arrParams, $isForce = false){
		
		
		if(empty($postID) && $isForce == true)
			$arrKeys = UniteCreatorWooIntegrate::getWooKeysNoPost();
		else
			$arrKeys = UniteCreatorWooIntegrate::getWooKeysByPostID($postID);
		
			
		if(empty($arrKeys))
			return($arrParams);
		
		$arrParams[] = $this->createWooPostParam_getChildProducts();
		$arrParams[] = $this->createWooPostParam_putVariations();
		$arrParams[] = $this->createWooPostParam_putAttributes();
		$arrParams[] = $this->createWooPostParam_putProductGallery();
		$arrParams[] = $this->createWooPostParam_getEndpoints();
		
		foreach($arrKeys as $key){
		
			switch($key){
				case "woo_sale_price":
				case "woo_regular_price":
				case "woo_price":
				case "woo_price_to":
				case "woo_price_from":
				case "woo_sale_price_to":
				case "woo_sale_price_from":
				case "woo_regular_price_from":
				case "woo_regular_price_to":
					$arrParams[] = $this->createChildParam($key,null,array("raw_insert_text"=>"{{[param_name]|wc_price|raw}}"));
				break;
				default:
					$arrParams[] = $this->createChildParam($key);
				break;
			}
		}
		
		return($arrParams);
	}
	
	
	/**
	 * add woo commerce post param without post id
	 */
	private function addWooPostParamsNoPostID($arrParams){
		
		$arrParams = $this->checkAddWooPostParams(null, $arrParams, true);
		
		return($arrParams);
	}
	
	/**
	 * get thumb sizes array
	 */
	private function getArrImageThumbSizes(){
		
		$arrSizesOutput = array();
		$arrSizesOutput["thumb"] = __("Thumb (max width 300)", "unlimited-elements-for-elementor");
		
		$arrSizes = UniteFunctionsWPUC::getArrThumbSizes();
		
		//add large
		$sizeLargeDesc = UniteFunctionsUC::getVal($arrSizes, "large");
		if(empty($sizeLargeDesc))
			$sizeLargeDesc = __("Large (max width 780)");
		
		$arrSizesOutput["thumb_large"] = $sizeLargeDesc;
			
		foreach($arrSizes as $size=>$desc){
			
			if($size == "medium")
				continue;
				
			if($size == "large")
				continue;
				
			$arrSizesOutput["thumb_".$size] = $desc;
			
		}
		
		return($arrSizesOutput);
	}
	
	/**
	 * add image child param
	 */
	private function createChildParam_image($key, $isSingle = false){
		
		if($isSingle == false)
			$param = $this->createChildParam($key);
		else
			$param = $this->createChildParam_underscore($key);
		
		return($param);
	}
	
	
	/**
	 * add post child params
	 */
	private function addPostImageChildParams($arrParams, $isSingle = false){
		
		if($isSingle == false)
			$arrParams[] = $this->createChildParam_image("image", $isSingle);
		else
			$arrParams[] = $this->createChildParam_underscore(null);
		
		$prefix = "image_";
		if($isSingle == true)
			$prefix = "";
		
		$arrParams[] = $this->createChildParam_image("{$prefix}attributes|raw", $isSingle);
		$arrParams[] = $this->createChildParam_image("{$prefix}attributes_nosize|raw", $isSingle);
		
			
		$arrSizes = $this->getArrImageThumbSizes();
		
		foreach($arrSizes as $size=>$desc){
			
			$size = str_replace("-", "_", $size);
			
			if($isSingle == false){
				$key = "image_{$size}";
				$sap = ".";
			}
			else{
				$key = $size;
				$sap = "_";
			}
			
			$thumbCode = "{{".self::PARAM_PREFIX."{$sap}$key}}\n";
			$thumbCode .= "{{".self::PARAM_PREFIX."{$sap}{$key}_width}}\n";;
			$thumbCode .= "{{".self::PARAM_PREFIX."{$sap}{$key}_height}}\n";;
						
			$arrParams[] = $this->createChildParam_code("{{".self::PARAM_PREFIX."_".$key."}}", $thumbCode, false, true);
		}
		
				
		$arrParams[] = $this->createChildParam_image("{$prefix}title", $isSingle);
		$arrParams[] = $this->createChildParam_image("{$prefix}alt", $isSingle);
		$arrParams[] = $this->createChildParam_image("{$prefix}description", $isSingle);
		$arrParams[] = $this->createChildParam_image("{$prefix}caption", $isSingle);
		$arrParams[] = $this->createChildParam_image("{$prefix}imageid", $isSingle);
		$arrParams[] = $this->createChildParam_image("{$prefix}width", $isSingle);
		$arrParams[] = $this->createChildParam_image("{$prefix}height", $isSingle);
		
		return($arrParams);
	}
	
	/**
	 * get post child params
	 */
	public function getChildParams_post($postID = null, $arrAdditions = array()){
				
		$arrParams = array();
		$arrParams[] = $this->createChildParam("id");
		$arrParams[] = $this->createChildParam("title",UniteCreatorDialogParam::PARAM_EDITOR);
		$arrParams[] = $this->createChildParam("alias");
		$arrParams[] = $this->createChildParam("content", UniteCreatorDialogParam::PARAM_EDITOR);
		$arrParams[] = $this->createChildParam("content|wpautop", UniteCreatorDialogParam::PARAM_EDITOR);
		$arrParams[] = $this->createChildParam("put_post_content()",null,array("raw_insert_text"=>"{{ucfunc(\"put_post_content\", [param_prefix].id, [param_prefix].content)}}"));
		
		$arrParams[] = $this->createChildParam("intro");
		$arrParams[] = $this->createChildParam("intro_full",null,array("raw_insert_text"=>"{{[param_name]|truncate(100)}}"));
		
		$arrParams[] = $this->createChildParam("link");
		$arrParams[] = $this->createChildParam("link_attributes",null,array("raw_insert_text"=>"{{[param_name]|raw"));
		
		$arrParams[] = $this->createChildParam("date",null,array("raw_insert_text"=>"{{[param_name]|ucdate(\"d F Y, H:i\")|raw}}"));
		$arrParams[] = $this->createChildParam("date_modified",null,array("raw_insert_text"=>"{{[param_name]|ucdate(\"d F Y, H:i\")|raw}}"));
		$arrParams[] = $this->createChildParam("post_type",null,array("raw_insert_text"=>"{{[param_name]}}\n\n{{ucfunc(\"put_post_type_title\",[param_name])}}\n"));
		
		$arrParams[] = $this->createChildParam("tagslist",null,array("raw_insert_text"=>"{{putPostTags([param_prefix].id)}}"));		
		
		$arrParams = $this->getChildParams_post_addTerms($arrParams);
		$arrParams = $this->getChildParams_post_addAuthor($arrParams);
		$arrParams = $this->getChildParams_post_addPostMeta($arrParams);
		$arrParams = $this->getChildParams_post_getImageFromMeta($arrParams);
		$arrParams = $this->getChildParams_post_putHtmlData($arrParams);
		$arrParams = $this->getChildParams_post_numComments($arrParams);
		
		$isWooAdded = false;
		
		if(!empty($postID)){
			$numParams = count($arrParams);
			$arrParams = $this->checkAddWooPostParams($postID, $arrParams);
			
			$numParamsAfter = count($arrParams);
			
			if($numParamsAfter > $numParams)
				$isWooAdded = true;
		}
			
		$arrParams = $this->addPostImageChildParams($arrParams);
		
		
		//add post additions
		if(empty($arrAdditions))
			return($arrParams);
				
		foreach($arrAdditions as $addition){
			
			switch($addition){
				case GlobalsProviderUC::POST_ADDITION_CATEGORY:
					
					$arrParams = $this->createChildParams_category($arrParams);
					
				break;
				case GlobalsProviderUC::POST_ADDITION_CUSTOMFIELDS:
					
					if(!empty($postID))
						$arrParams = $this->addCustomFieldsParams($arrParams, $postID);
					
				break;
				case GlobalsProviderUC::POST_ADDITION_WOO:
					
					if($isWooAdded == false)
						$arrParams = $this->addWooPostParamsNoPostID($arrParams);
					
				break;
			}
		}
		
		
		return($arrParams);
	}
	
	private function __________POST_FIELDS_END_________(){}
	
	
	/**
	 * get term code
	 */
	private function getTermCode($itemName, $parentName, $isWoo = false){
		
		$strCode = "";
		$strCode .= "{% for $itemName in $parentName %}\n";
		$strCode .= "\n";
		$strCode .= "	Term ID: {{{$itemName}.id}} <br>\n ";
		$strCode .= "	Name: {{{$itemName}.name|raw}} <br>\n ";
		$strCode .= "	Slug: {{{$itemName}.slug}} <br>\n ";
		$strCode .= "	Description: {{{$itemName}.description}} <br>\n ";
		$strCode .= "	Link: {{{$itemName}.link}} <br>\n ";
		
		if($isWoo == false)
			$strCode .= "	Num posts: {{{$itemName}.num_posts}} <br>\n ";
		else
			$strCode .= "	Num products: {{{$itemName}.num_products}} <br>\n ";
		
		$strCode .= "	Is Current: {{{$itemName}.iscurrent}} <br>\n ";
		$strCode .= "	Selected Class: {{{$itemName}.class_selected}} <br>\n ";
		
		if($isWoo == true){
			$strCode .= "	Image: {{{$itemName}.image}} <br>\n ";
			$strCode .= "	Image Thumb: {{{$itemName}.image_thumb}} <br>\n\n ";
		}
		
		$strCode .= "\n	{# For other fields please look at output of this function #}<br>\n\n ";
		$strCode .= "	{{printVar({$itemName})}} <br>\n\n ";

		$strCode .= "	{# Also you can use the getTermMeta() #}\n\n";
		
		$strCode .= "	{% set meta_fields = getTermMeta({$itemName}.id) %}\n ";
		$strCode .= "	{{meta_fields.fieldname}}<br>\n\n ";
		$strCode .= "	{{printVar(meta_fields)}}<br>\n\n ";
		
		$strCode .= "	{# Also you can use the getTermCustomFields() #}\n\n";
		
		$strCode .= "	{% set custom_fields = getTermCustomFields({$itemName}.id) %}\n ";
		$strCode .= "	{{custom_fields.fieldname}}<br>\n\n ";
		$strCode .= "	{{printVar(custom_fields)}}<br>\n\n ";
		
		$strCode .= "	<hr>\n";
		
		$strCode .= "\n";
		
		$strCode .= "{% endfor %}\n";
		
		return($strCode);
	}
	
	
	/**
	 * get term code
	 */
	private function getUsersCode($itemName, $parentName){
		
		$strCode = "";
		$strCode .= "{% for $itemName in $parentName %}\n";
		$strCode .= "\n";
		$strCode .= "	User ID: {{{$itemName}.id}} <br>\n ";
		$strCode .= "	Username: {{{$itemName}.username}} <br>\n ";
		$strCode .= "	Name: {{{$itemName}.name|raw}} <br>\n ";
		$strCode .= "	Email: {{{$itemName}.email}} <br>\n ";
		$strCode .= "	Role: {{{$itemName}.role}} <br>\n ";
		
		$strCode .= "\n";
		$strCode .= "	<hr>\n";
		
		$strCode .= "	# ---- Avatar Fields: ----- \n\n";
		
		$arrAvatarKeys = UniteFunctionsWPUC::getUserAvatarKeys();
		
		foreach($arrAvatarKeys as $key){
			$title = UniteFunctionsUC::convertHandleToTitle($key);
		
			$strCode .= "	$title: {{{$itemName}.{$key}}} <br>\n ";
		}
		
		$strCode .= "\n";
		$strCode .= "	<hr>\n";
		
		$strCode .= "	# ---- User Meta Fields: ----- \n\n";
				
		$strCode .= "	Url Posts: {{{$itemName}.url_posts}} <br>\n ";
		$strCode .= "	Num Posts: {{{$itemName}.num_posts}} <br>\n\n ";
		
		$arrMetaKeys = UniteFunctionsWPUC::getUserMetaKeys();
			
			
		foreach($arrMetaKeys as $key){
			$title = UniteFunctionsUC::convertHandleToTitle($key);
		
			$strCode .= "	$title: {{{$itemName}.{$key}}} <br>\n ";
		}
		
		$strCode .= "\n";
		
		$strCode .= "	{#For additional user meta you can use getUserMeta function. If meta key not given, it will print all meta keys available#}\n\n";
		
		$strCode .= "	{% set somevalue = getUserMeta({$itemName}.id,\"admin_color\") %}\n";
		$strCode .= "	Meta Value: {{printVar(somevalue)}}\n";
		
		$strCode .= "\n";
		
		$strCode .= "{% endfor %}\n";
		
		return($strCode);
	}
	
	
	/**
	 * get post child params
	 */
	public function getAddParams_terms($isWoo = false){
		
		$arrParams = array();
		
		$strCode = $this->getTermCode("term", "[parent_name]", $isWoo);
		
		$arrParams[] = $this->createChildParam_code("[parent_name]_output", $strCode);
		
				
		return($arrParams);
	}

	/**
	 * add listing child param
	 */
	public function getAddParams_listing(){
		
		$arrParams = array();
		
		$strCode = "
{% for item in [parent_name]_items %}
	
	{{putDynamicLoopTemplate(item.object,[parent_name]_templateid)}}

{% endfor %}
		
";
		$arrParams[] = $this->createChildParam_code("[parent_name]_output", $strCode);
		
		return($arrParams);
	}
	
	
	/**
	 * get users child params
	 */
	public function getAddParams_users(){
		
		$arrParams = array();
		
		$strCode = $this->getUsersCode("user", "[parent_name]");
		
		$arrParams[] = $this->createChildParam_code("[parent_name]_output", $strCode);
		
		return($arrParams);
	}
	
	
	/**
	 * get link add params
	 */
	public function getAddParams_link(){

		$arrParams = array();
			
		$arrParams[] = $this->createAddParam();
		$arrParams[] = $this->createAddParam("html_attributes|raw");
		$arrParams[] = $this->createAddParam("withprefix");
		$arrParams[] = $this->createAddParam("noprefix");
		
		return($arrParams);
	}

	/**
	 * get date time add params
	 */
	public function getAddParams_datetime(){

		$arrParams = array();
		
		$arrParams[] = $this->createAddParam();
		$arrParams[] = $this->createAddParam("stamp");
		
		$strUcDate = "\n {{[param_prefix]_stamp|ucdate(\"d m Y\")}}
					  \n {{[param_prefix]_stamp|ucdate(\"H:i\")}}
		";
		
		$arrParams[] = $this->createChildParam_code("[parent_name]_stamp|ucdate", $strUcDate);
		
		
		return($arrParams);
	}
	
	
	/**
	 * get post child params
	 */
	public function getAddParams_slider(){

		$arrParams = array();
		
		$arrParams[] = $this->createAddParam();
		$arrParams[] = $this->createAddParam("nounit");
	
		$addParams = array("condition"=>"responsive");
		
		$arrParams[] = $this->createAddParam("tablet", $addParams);
		$arrParams[] = $this->createAddParam("mobile", $addParams);
		
		return($arrParams);
	}
	
	/**
	 * get post child params
	 */
	public function getAddParams_dropdown(){
		
		$arrParams = array();
		
		$arrParams[] = $this->createAddParam();
		
		$addParams = array("condition"=>"responsive");
		
		$arrParams[] = $this->createAddParam("tablet", $addParams);
		$arrParams[] = $this->createAddParam("mobile", $addParams);
		
		return($arrParams);
	}
	
	
	/**
	 * get post child params
	 */
	public function getAddParams_menu(){

		$arrParams = array();
		
		$arrParams[] = $this->createAddParam("|raw");
		$arrParams[] = $this->createAddParam("id");
		
		return($arrParams);
	}

	
	/**
	 * get post child params
	 */
	public function getAddParams_template(){

		$arrParams = array();
		
		$strCode = "{{putElementorTemplate([param_prefix]_templateid)}}";
		
	    $arrParam = $this->createChildParam(null, null, array("raw_insert_text"=>$strCode));
		
		$arrParams[] = $arrParam;
		
		return($arrParams);
	}
	
	
	/**
	 * icon library add params
	 */
	public function getAddParams_iconLibrary(){
		
		$arrParams = array();
		
		$arrParams[] = $this->createAddParam(null);
		$arrParams[] = $this->createAddParam("html|raw");

		
		return($arrParams);
	}
	
	
	
}

