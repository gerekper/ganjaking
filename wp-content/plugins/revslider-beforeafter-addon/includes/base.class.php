<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if(!defined('ABSPATH')) exit();

require_once(RS_BEFOREAFTER_PLUGIN_PATH . 'framework/base.class.php');

class RsBeforeAfterBase extends RsAddOnBeforeAfterBase {
	
	protected static $_PluginPath    = RS_BEFOREAFTER_PLUGIN_PATH,
					 $_PluginUrl     = RS_BEFOREAFTER_PLUGIN_URL,
					 $_PluginTitle   = 'beforeafter',
				     $_FilePath      = __FILE__,
				     $_Version       = '2.0.4';
	
	public function __construct() {
		
		//load the textdomain, if the plugin should be translateable
		parent::_loadPluginTextDomain();
		
		// check to make sure all requirements are met
		$notice = $this->systemsCheck();
		if($notice) {
			
			require_once(RS_BEFOREAFTER_PLUGIN_PATH . 'framework/notices.class.php');
			
			new RsAddOnBeforeAfterNotice($notice, static::$_PluginTitle, static::$_Version);
			return;
			
		}
		
		parent::loadClasses();

	}

}
?>