<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2020 ThemePunch
*/

if(!defined('ABSPATH')) exit();

require_once(RS_LOTTIE_PLUGIN_PATH . 'framework/base.class.php');

class RsLottieBase extends RsAddOnLottieBase {
	
	protected static $_PluginPath    = RS_LOTTIE_PLUGIN_PATH,
					 $_PluginUrl     = RS_LOTTIE_PLUGIN_URL,
					 $_PluginTitle   = 'lottie',
				     $_FilePath      = __FILE__,
				     $_Version       = '2.0.5';
	
	public function __construct() {
		
		//load the textdomain, if the plugin should be translateable
		parent::_loadPluginTextDomain();
		
		// check to make sure all requirements are met
		$notice = $this->systemsCheck();
		if($notice) {
			
			require_once(RS_LOTTIE_PLUGIN_PATH . 'framework/notices.class.php');
			
			new RsAddOnLottieNotice($notice, static::$_PluginTitle, static::$_Version);
			return;
			
		}
		
		if(is_admin()){
			require_once(RS_LOTTIE_PLUGIN_PATH.'admin/includes/loader.class.php');
			$loader = new RsLottieLoader();
			$loader->add_filters();
		}
		
		parent::loadClasses();

	}

}
?>