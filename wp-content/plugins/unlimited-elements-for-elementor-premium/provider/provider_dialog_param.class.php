<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorDialogParam extends UniteCreatorDialogParamWork{
	
	
	/**
	 * modify param text, function for override
	 */
	protected function modifyParamText($paramType, $paramText){
		
		
		return($paramText);
	}
	
	
	
	/**
	 * filter main params
	 * function for override
	 */
	protected function filterMainParams($arrParams){
		
		return($arrParams);
	}
	
	
	/**
	 * init main params, add platform related param
	 */
	public function initMainParams(){
		
		parent::initMainParams();
		
		$this->arrParams[] = self::PARAM_POSTS_LIST;
		$this->arrParams[] = self::PARAM_POST_TERMS;
		$this->arrParams[] = self::PARAM_WOO_CATS;
		$this->arrParams[] = self::PARAM_USERS;		
		$this->arrParams[] = self::PARAM_TEMPLATE;
		$this->arrParams[] = self::PARAM_LISTING;
		$this->arrParams[] = self::PARAM_TYPOGRAPHY;
		$this->arrParams[] = self::PARAM_MARGINS;
		$this->arrParams[] = self::PARAM_PADDING;
		$this->arrParams[] = self::PARAM_BACKGROUND;
		$this->arrParams[] = self::PARAM_MENU;
		$this->arrParams[] = self::PARAM_BORDER;
		$this->arrParams[] = self::PARAM_BOXSHADOW;
		$this->arrParams[] = self::PARAM_TEXTSHADOW;
		$this->arrParams[] = self::PARAM_DATETIME;
		$this->arrParams[] = self::PARAM_BORDER_DIMENTIONS;
		$this->arrParams[] = self::PARAM_CSS_FILTERS;
		$this->arrParams[] = self::PARAM_HOVER_ANIMATIONS;
		$this->arrParams[] = self::PARAM_POST_SELECT;
		$this->arrParams[] = self::PARAM_TERM_SELECT;
		$this->arrParams[] = self::PARAM_SPECIAL;
		
		$this->arrParams = $this->filterMainParams($this->arrParams);
	}
	
	
}