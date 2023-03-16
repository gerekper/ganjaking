<?php
/**
* @package Unlimited Elements
* @author unlimited-elements.com
* @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

class UniteCreatorChildParamsPro extends UniteCreatorAddonViewChildParams{
		
	
	
	/**
	 * add code examples params
	 * override the free functionality
	 */
	public function getCodeExamplesParams_php($arrParams){
	
		
			$key = "Run PHP Action";
			$text = "
{# run any wp action, and any custom PHP function. Use add_action to create the actions. \n The function support up to 3 custom params #}
\n
{{ do_action('some_action') }}
{{ do_action('some_action','param1','param2','param3') }}
";
	//-------- data from php -------------
			
			$arrParams[] = $this->createChildParam_code($key, $text);
		
			$key = "Get Data From PHP Filter";
			$text = "
{# apply any WordPress filters, and any custom PHP function. Use apply_filters to create the actions. \n The function support up to 2 custom params #}
\n
{% set myValue = apply_filters('my_filter') }}
{% set myValue = apply_filters('my_filter',value, param2, param3) }}

";
			$arrParams[] = $this->createChildParam_code($key, $text);


	//-------- run php get functoin -------------


			$key = "getByPHPFunction()";
			$text = "
{# Run any custom php function that starts with \"get_\". #}
{# Can take any number of arguments. Look the examples #}

{% set postData = getByPHPFunction('get_post') }}
{% set postMeta = getByPHPFunction('get_post_meta',15) }}

";
			$arrParams[] = $this->createChildParam_code($key, $text);
			
			
		return($arrParams);
	}
	
	
}