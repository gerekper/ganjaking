<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class UniteCreatorCategories extends UniteCreatorCategoriesWork{
	
	
	/**
	 * modify category title before create
	 * function for override
	 */
	protected function modifyCatTitleBeforeCreate($title){
		
		if(UniteCreatorWebAPI::IS_CATALOG_UNLIMITED == true)
			return($title);
		
		$title = str_replace("Article", "Post", $title);
		
		$title = str_replace("article", "post", $title);
		
		return($title);
	}
	
	
	
}