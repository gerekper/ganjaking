<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

//no direct accees
defined ('UNLIMITED_ELEMENTS_INC') or die ('restricted aceess');
	
   class UniteCreatorAdminWPPluginBase{
		
   		protected static $pluginName = "";
   		
	   	private static $arrMenuPages = array();
	   	private static $arrSubMenuPages = array();
	   	private static $capability = "manage_options";
	   	   
	   	private static $t;
	   	
	   	const ACTION_ADMIN_MENU = "admin_menu";
	   	const ACTION_ADMIN_INIT = "admin_init";
	   	const ACTION_ADD_SCRIPTS = "admin_enqueue_scripts";
	   	const ACTION_WP_LOADED = "wp_loaded";
   		const ACTION_ADMIN_FOOTER = "admin_footer";
   		
   		
   		
		/**
		 *
		 * the constructor
		 */
		public function __construct(){
			self::$t = $this;
			
			$this->init();
		}		

		
		/**
		 *
		 * add menu page
		 */
		protected function addMenuPage($title, $icon=null, $link=null){
			
			self::$arrMenuPages[] = array("title"=>$title, 
				"plugin_name"=>self::$pluginName, 
				"pageFunction"=>"adminPages",
				"icon"=>$icon,
				"link"=>$link);
		}
		
		
		/**
		 *
		 * add sub menu page
		 */
		protected function addSubMenuPage($slug, $title, $realLink = false,$parentSlug = null){
			
			self::$arrSubMenuPages[] = array(
				"slug"=>$slug,
				"title"=>$title,
				"plugin_name"=>self::$pluginName, 
				"pageFunction"=>"adminPages",
				"realLink"=>$realLink,
				"parentSlug"=>$parentSlug);
		
		}
		
		
		/**
		 *
		 * add some wordpress action
		 */
		protected function addAction($action,$eventFunction, $numArgs=1){
			
			add_action( $action, array($this, $eventFunction) ,10, $numArgs);
		}
		
		/**
		 *
		 * add some wordpress action
		 */
		protected function addFilter($action,$eventFunction,$numArgs){
			
			add_action( $action, array($this, $eventFunction), 10, $numArgs);
		}
   	
		
		/**
		 * get menu arrays
		 */
		public static function getArrMenuPages(){
			
			return(self::$arrMenuPages);
		}
		
		
		/**
		 * return sub menu pages
		 */
		public static function getArrSubmenuPages(){
			
			return(self::$arrSubMenuPages);
		}
		
	
		/**
		 * init function
		 */
		protected function init(){
						
		}
		
   }