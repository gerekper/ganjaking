<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class UniteTranslateUC{
	
	private $entries;
	private $domain;
	
	
	/**
	 * load language
	 */
	public function __construct($domain){
		$this->domain = $domain;
		$this->get_translations_for_domain($domain);
		
	}
	
	
	/*
	 * read from language file and put it in entries for translate
	* @Params String $domain - basse name of language file
	*/
	private function get_translations_for_domain( $domain = "default" ){
		
		if($this->entries != null){
			return true;
		}
	
		$mo = new MO();
		$current_language = JFactory::getLanguage();
		
		$mo_file = JPATH_COMPONENT . DIRECTORY_SEPARATOR."language".DIRECTORY_SEPARATOR. $domain ."-". $current_language->getTag() .".mo" ;
		
		
		if(!file_exists($mo_file)){
			$mo_file = JPATH_COMPONENT . DIRECTORY_SEPARATOR."language".DIRECTORY_SEPARATOR. $domain ."-". str_replace("-", "_", $current_language->getTag()) .".mo" ;
			if(!file_exists($mo_file)){
				return false;
			}
		}
				
		if ( !$mo->import_from_file( $mo_file ) ) return false;
		if ( !isset( $lang[$domain] ) ){
			$lang[$domain] = $mo;
		}
				
		$this->merge_with( $lang[$domain] );
	}
	
	
	/**
	 * translate text
	 */
	private function translate_singular($singular, $context=null) {
	
		$entry = new Translation_Entry(array('singular' => $singular, 'context' => $context));
		$translated = $this->translate_entry($entry);
		return ($translated && !empty($translated->translations))? $translated->translations[0] : $singular;
	}
	
	
	
	/*
	 * put data read from language file to entries for translate
	*/	
	private function merge_with(&$other) {
		
		foreach( $other->entries as $entry ) {
			$this->entries[$entry->key()] = $entry;
		}
		
	}
	
	
	/**
	 * translate entry
	 */
	private function translate_entry(&$entry) {
		
		$key = $entry->key();
		return isset($this->entries[$key])? $this->entries[$key] : false;
	}
	
	
	/**
	 * translate the text
	 */
	public function translate($text) {
		
		$translations = $this->translate_singular($text);
	
		return $translations;
	}
	
	
	
}

?>
