<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if(!defined('ABSPATH')) exit();

require_once(RS_SCROLLVIDEO_PLUGIN_PATH . 'framework/base.class.php');

class RsScrollvideoBase extends RsAddOnScrollvideoBase {
	
	protected static $_PluginPath    = RS_SCROLLVIDEO_PLUGIN_PATH,
					 $_PluginUrl     = RS_SCROLLVIDEO_PLUGIN_URL,
					 $_PluginTitle   = 'scrollvideo',
				     $_FilePath      = __FILE__,
				     $_Version       = '3.0.0';
	
	public function __construct() {
		
		//load the textdomain, if the plugin should be translateable
		parent::_loadPluginTextDomain();
		
		// check to make sure all requirements are met
		$notice = $this->systemsCheck();
		if($notice) {
			
			require_once(RS_SCROLLVIDEO_PLUGIN_PATH . 'framework/notices.class.php');
			
			new RsAddOnScrollvideoNotice($notice, static::$_PluginTitle, static::$_Version);
			return;
			
		}
		
		
		if(is_admin()){
			require_once(RS_SCROLLVIDEO_PLUGIN_PATH.'admin/includes/loader.class.php');
			$loader = new RsScrollvideoLoader();
			$loader->add_filters();
		}
		
		parent::loadClasses();

	}

}
?>