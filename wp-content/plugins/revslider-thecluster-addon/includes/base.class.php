<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2022 ThemePunch
*/

if(!defined('ABSPATH')) exit();

require_once(RS_THECLUSTER_PLUGIN_PATH . 'framework/base.class.php');

class RsTheClusterBase extends RsAddOnTheClusterBase {
	
	protected static $_PluginPath    = RS_THECLUSTER_PLUGIN_PATH,
					 $_PluginUrl     = RS_THECLUSTER_PLUGIN_URL,
					 $_PluginTitle   = 'thecluster',
				     $_FilePath      = __FILE__,
				     $_Version       = '1.0.5';
	
	public function __construct() {
		
		//load the textdomain, if the plugin should be translateable
		parent::_loadPluginTextDomain();
		
		// check to make sure all requirements are met
		$notice = $this->systemsCheck();
		if($notice) {
			
			require_once(RS_THECLUSTER_PLUGIN_PATH . 'framework/notices.class.php');
			
			new RsAddOnTheClusterNotice($notice, static::$_PluginTitle, static::$_Version);
			return;
			
		}
		
		parent::loadClasses();

	}

}
?>