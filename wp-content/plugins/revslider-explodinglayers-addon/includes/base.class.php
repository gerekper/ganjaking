<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

if(!defined('ABSPATH')) exit();

require_once(RS_EXPLODINGLAYERS_PLUGIN_PATH . 'framework/base.class.php');

class RsExplodinglayersBase extends RsAddOnExplodinglayersBase {
	
	protected static $_PluginPath    = RS_EXPLODINGLAYERS_PLUGIN_PATH,
					 $_PluginUrl     = RS_EXPLODINGLAYERS_PLUGIN_URL,
					 $_PluginTitle   = 'explodinglayers',
				     $_FilePath      = __FILE__,
				     $_Version       = '3.0.2';
	
	public function __construct() {
		
		//load the textdomain, if the plugin should be translateable
		parent::_loadPluginTextDomain();
		
		// check to make sure all requirements are met
		$notice = $this->systemsCheck();
		if($notice) {
			
			require_once(RS_EXPLODINGLAYERS_PLUGIN_PATH . 'framework/notices.class.php');
			
			new RsAddOnExplodinglayersNotice($notice, static::$_PluginTitle, static::$_Version);
			return;
			
		}
		
		parent::loadClasses();

	}

}
?>