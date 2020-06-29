<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if(!defined('ABSPATH')) exit();

class RsAddOnSnowBase {
	
	const MINIMUM_VERSION = '6.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnSnowBase::MINIMUM_VERSION, '>=')) {
		
			return 'add_notice_version';
		
		}
		else if(get_option('revslider-valid', 'false') == 'false') {
		
			 return 'add_notice_activation';
		
		}
		
		return false;
		
	}
	
	protected function loadClasses() {
		
		$isAdmin = is_admin();
		
		if($isAdmin) {
			
			//handle update process, this uses the typical ThemePunch server process
			require_once(static::$_PluginPath . 'admin/includes/update.class.php');
			$update_admin = new RevAddOnSnowUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin ,'set_update_transient'));
			add_filter('plugins_api', array($update_admin,'set_updates_api_results'), 10, 3);
									
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
		}
		
		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		
		new RsSnowSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		
	}
	
	/**
	 * Load the textdomain
	 **/
	protected function _loadPluginTextDomain(){
		
		load_plugin_textdomain('rs_' . static::$_PluginTitle, false, static::$_PluginPath . 'languages/');
		
	}
	
	
	// load admin scripts
	public function enqueue_admin_scripts($hook) {
		
		if($hook === 'toplevel_page_revslider') {
		
			if(!isset($_GET['page']) || !isset($_GET['view'])) return;
			
			$page = $_GET['page'];
			if($page !== 'revslider') return;
			
			$_handle = 'rs-' . static::$_PluginTitle . '-admin';
			$_base   = static::$_PluginUrl . 'admin/assets/';
						
			wp_enqueue_script($_handle, $_base . 'js/revslider-snow-addon-admin.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			wp_localize_script( $_handle, 'revslider_snow_addon', self::get_var() );
		}
		
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-snow-addon') {
		
		if($slug === 'revslider-snow-addon'){
			
			$obj = self::get_var();
			$obj['help'] = self::get_definitions();
			return $obj;
			
		}
		
		return $var;
	
	}
	
	/**
	 * Called via php filter.  Merges AddOn definitions with core revslider definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_help($definitions) {
		
		if(empty($definitions) || !isset($definitions['editor_settings'])) return $definitions;
		
		if(isset($definitions['editor_settings']['slider_settings']) && isset($definitions['editor_settings']['slider_settings']['addons'])) {
			$help = self::get_definitions();
			$definitions['editor_settings']['slider_settings']['addons']['snow_addon'] = $help['slider'];
		}
		
		return $definitions;
	
	}

	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public static function get_var() {
	
		return array(
			'bricks' => array(
			'active' => __('Active','revslider-snow-addon'),
			'entertext' => __('enter text...','revslider-snow-addon'),
			'snow' => __('Holiday Snow','revslider-snow-addon'),
			'firstslide' => __('First Slide','revslider-snow-addon'),
			'lastslide' => __('Last Slide','revslider-snow-addon'),
			'general' => __('General Settings','revslider-snow-addon'),
			'snowflake' => __('Snowflake Settings','revslider-snow-addon'),
			'maxsnow' => __('Amount','revslider-snow-addon'),
			'minsize' => __('Min Size','revslider-snow-addon'),
			'maxsize' => __('Max Size','revslider-snow-addon'),
			'minop' => __('Min. Opacity','revslider-snow-addon'),
			'maxop' => __('Max. Opacity','revslider-snow-addon'),
			'minspeed' => __('Min Speed','revslider-snow-addon'),
			'maxspeed' => __('Max Speed','revslider-snow-addon'),
			'minamp' => __('Min Amplitude','revslider-snow-addon'),
			'maxamp' => __('Max Amplitude','revslider-snow-addon')
			)
		);
	
	}
	
	/**
	 * Returns the addon help definitions
	 *
	 * @since    2.0.0
	 */
	private static function get_definitions() {
		
		return array(
			
			'slider' => array(
				
				'general_settings' => array(
				
					'start' => array(
						
						'title' => __('Starting Slide', 'revslider-snow-addon'),
						'helpPath' => 'addOns.revslider-snow-addon.startSlide', 
						'keywords' => array('addon', 'addons', 'snow', 'snow addon', 'holiday', 'holiday snow'), 
						'description' => __("Choose the Slide when the snow should first appear", 'revslider-snow-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/holiday-snow-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Holiday Snow',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-snow-addon", 
							'scrollTo' => '#form_slidegeneral_revslider-snow-addon', 
							'focus' => "#snow_start_slide"
							
						)
						
					),
					
					'end' => array(
						
						'title' => __('Ending Slide', 'revslider-snow-addon'),
						'helpPath' => 'addOns.revslider-snow-addon.endSlide', 
						'keywords' => array('addon', 'addons', 'snow', 'snow addon', 'holiday', 'holiday snow'), 
						'description' => __("Choose the Slide when the snow should stop falling", 'revslider-snow-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/holiday-snow-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Holiday Snow',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-snow-addon", 
							'scrollTo' => '#form_slidegeneral_revslider-snow-addon', 
							'focus' => "#snow_end_slide"
							
						)
						
					)
					
				),
				
				'snowflake_settings' => array(
				
					'amount' => array(
						
						'title' => __('Num. Snow Flakes', 'revslider-snow-addon'),
						'helpPath' => 'addOns.revslider-snow-addon.max.number', 
						'keywords' => array('addon', 'addons', 'snow', 'snow addon', 'holiday', 'holiday snow', 'amount'), 
						'description' => __("The amount of snow flakes that should fall at any given time", 'revslider-snow-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/holiday-snow-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Holiday Snow',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-snow-addon", 
							'scrollTo' => '#snowflake_settings', 
							'focus' => "#snowflake_amount"
							
						)
						
					),
					
					'min_size' => array(
						
						'title' => __('Min. Size', 'revslider-snow-addon'),
						'helpPath' => 'addOns.revslider-snow-addon.min.size', 
						'keywords' => array('addon', 'addons', 'snow', 'snow addon', 'holiday', 'holiday snow', 'min size', 'size'), 
						'description' => __("The minimum size for any given snowflake", 'revslider-snow-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/holiday-snow-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Holiday Snow',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-snow-addon", 
							'scrollTo' => '#snowflake_settings', 
							'focus' => "#snow_min_size"
							
						)
						
					),
					
					'max_size' => array(
						
						'title' => __('Max. Size', 'revslider-snow-addon'),
						'helpPath' => 'addOns.revslider-snow-addon.max.size', 
						'keywords' => array('addon', 'addons', 'snow', 'snow addon', 'holiday', 'holiday snow', 'max size', 'size'), 
						'description' => __("The maximum size for any given snowflake", 'revslider-snow-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/holiday-snow-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Holiday Snow',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-snow-addon", 
							'scrollTo' => '#snowflake_settings', 
							'focus' => "#snow_max_size"
							
						)
						
					),
					
					'min_opacity' => array(
						
						'title' => __('Min. Opacity', 'revslider-snow-addon'),
						'helpPath' => 'addOns.revslider-snow-addon.min.opacity', 
						'keywords' => array('addon', 'addons', 'snow', 'snow addon', 'holiday', 'holiday snow', 'min opacity', 'opacity'), 
						'description' => __("The minimum opacity for any given snowflake", 'revslider-snow-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/holiday-snow-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Holiday Snow',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-snow-addon", 
							'scrollTo' => '#snowflake_settings', 
							'focus' => "#snow_min_op"
							
						)
						
					),
					
					'max_opacity' => array(
						
						'title' => __('Max. Opacity', 'revslider-snow-addon'),
						'helpPath' => 'addOns.revslider-snow-addon.max.opacity', 
						'keywords' => array('addon', 'addons', 'snow', 'snow addon', 'holiday', 'holiday snow', 'max opacity', 'opacity'), 
						'description' => __("The maximum opacity for any given snowflake", 'revslider-snow-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/holiday-snow-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Holiday Snow',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-snow-addon", 
							'scrollTo' => '#snowflake_settings', 
							'focus' => "#snow_max_op"
							
						)
						
					),
					
					'min_speed' => array(
						
						'title' => __('Min. Speed', 'revslider-snow-addon'),
						'helpPath' => 'addOns.revslider-snow-addon.min.speed', 
						'keywords' => array('addon', 'addons', 'snow', 'snow addon', 'holiday', 'holiday snow', 'min speed', 'speed'), 
						'description' => __("The minimum speed for any given snowflake", 'revslider-snow-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/holiday-snow-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Holiday Snow',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-snow-addon", 
							'scrollTo' => '#snowflake_settings', 
							'focus' => "#snow_min_speed"
							
						)
						
					),
					
					'max_speed' => array(
						
						'title' => __('Max. Speed', 'revslider-snow-addon'),
						'helpPath' => 'addOns.revslider-snow-addon.max.speed', 
						'keywords' => array('addon', 'addons', 'snow', 'snow addon', 'holiday', 'holiday snow', 'max speed', 'speed'), 
						'description' => __("The maximum speed for any given snowflake", 'revslider-snow-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/holiday-snow-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Holiday Snow',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-snow-addon", 
							'scrollTo' => '#snowflake_settings', 
							'focus' => "#snow_max_speed"
							
						)
						
					),
					
					'min_sinus' => array(
						
						'title' => __('Min. Amplitude', 'revslider-snow-addon'),
						'helpPath' => 'addOns.revslider-snow-addon.min.sinus', 
						'keywords' => array('addon', 'addons', 'snow', 'snow addon', 'holiday', 'holiday snow', 'min sinus', 'sinus'), 
						'description' => __("The minimum amplitude for any given snowflake.  This acts as the snowflake's gravity", 'revslider-snow-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/holiday-snow-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Holiday Snow',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-snow-addon", 
							'scrollTo' => '#snowflake_settings', 
							'focus' => "#snow_min_sinus"
							
						)
						
					),
					
					'max_sinus' => array(
						
						'title' => __('Max. Amplitude', 'revslider-snow-addon'),
						'helpPath' => 'addOns.revslider-snow-addon.max.sinus', 
						'keywords' => array('addon', 'addons', 'snow', 'snow addon', 'holiday', 'holiday snow', 'max sinus', 'sinus'), 
						'description' => __("The maximum amplitude for any given snowflake.  This acts as the snowflake's gravity", 'revslider-snow-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/holiday-snow-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Holiday Snow',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-snow-addon", 
							'scrollTo' => '#snowflake_settings', 
							'focus' => "#snow_max_sinus"
							
						)
						
					)
				
				)
				
			)
			
		);
		
	}

}
	
?>