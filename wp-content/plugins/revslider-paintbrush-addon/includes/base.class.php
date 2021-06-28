<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

if(!defined('ABSPATH')) exit();

require_once(RS_PAINTBRUSH_PLUGIN_PATH . 'framework/base.class.php');

class RsPaintbrushBase extends RsAddOnPaintbrushBase {
	
	protected static $_PluginPath    = RS_PAINTBRUSH_PLUGIN_PATH,
					 $_PluginUrl     = RS_PAINTBRUSH_PLUGIN_URL,
					 $_PluginTitle   = 'paintbrush',
				     $_FilePath      = __FILE__,
				     $_Version       = '3.0.3';
	
	public function __construct() {
		
		//load the textdomain, if the plugin should be translateable
		parent::_loadPluginTextDomain();
		
		// check to make sure all requirements are met
		$notice = $this->systemsCheck();
		if($notice) {
			
			require_once(RS_PAINTBRUSH_PLUGIN_PATH . 'framework/notices.class.php');
			
			new RsAddOnPaintbrushNotice($notice, static::$_PluginTitle, static::$_Version);
			return;
			
		}
		
		parent::loadClasses();

	}

}
?>