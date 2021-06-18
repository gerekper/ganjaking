<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnDuotoneBase {
	
	const MINIMUM_VERSION = '6.5.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnDuotoneBase::MINIMUM_VERSION, '>=')) {
		
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
			$update_admin = new RevAddOnDuotoneUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'));
			add_filter('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
		}
		
		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		require_once(static::$_PluginPath . 'public/includes/slide.class.php');
		
		new RsDuotoneSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsDuotoneFiltersSlideFront(static::$_PluginTitle);
		
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
			$_jsPathMin = file_exists(static::$_PluginPath . 'admin/assets/js/revslider-' . static::$_PluginTitle . '-addon-admin.dev.js') ? '.dev' : '';
			
			$_cssbase = static::$_PluginUrl . 'public/assets/';
			$_csshandle = 'rs-' . static::$_PluginTitle . '-front';
			
			wp_enqueue_style($_csshandle, $_cssbase . 'css/revolution.addon.' . static::$_PluginTitle . '.css', array(), static::$_Version);
			wp_enqueue_script($_handle, $_base . 'js/revslider-' . static::$_PluginTitle . '-addon-admin' . $_jsPathMin . '.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			wp_localize_script($_handle, 'revslider_duotonefilters_addon', self::get_var() );

		}
		
	}
	
	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public static function get_var() {
			
		return array(
		
			'bricks' => array(
				'duotone' => __('Duotone', 'revslider-duotone-addon'),
				'placeholder' => __('Select', 'revslider-duotone-addon'),
				'bgfilter' => __('BG Filter', 'revslider-duotone-addon'),
				'simplify' => __('Simplify', 'revslider-duotone-addon'),
				'easing' => __('Easing', 'revslider-duotone-addon'),
				'duration' => __('Duration', 'revslider-duotone-addon')
				
			)
		);
	
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-duotonefilters-addon') {
		
		if($slug === 'revslider-duotonefilters-addon'){
			
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
		$help = self::get_definitions();
		
		if(isset($definitions['editor_settings']['slider_settings']) && isset($definitions['editor_settings']['slider_settings']['addons'])) {
			$definitions['editor_settings']['slider_settings']['addons']['duotonefilters_addon'] = $help['slider'];
		}
		
		if(isset($definitions['editor_settings']['slide_settings']) && isset($definitions['editor_settings']['slide_settings']['addons'])) {
			$definitions['editor_settings']['slide_settings']['addons']['duotonefilters_addon'] = $help['slide'];
		}
		
		return $definitions;
	
	}
	
	/**
	 * Returns the addon help definitions
	 *
	 * @since    2.0.0
	 */
	private static function get_definitions() {
		
		return array(
			
			'slider' => array(
			
				'simplify' => array(
					
					'dependency_id' => 'duotone_simplify',
					'buttonTitle' => __('Duotone Transitions', 'revslider-duotone-addon'), 
					'title' => __('Simplify Transitions', 'revslider-duotone-addon'),
					'helpPath' => 'addOns.revslider-duotonefilters-addon.simplify.enable', 
					'keywords' => array('addon', 'addons', 'duotone', 'duotone addon', 'duotone filter', 'filter', 'filters', 'background', 'background filter', 'duotone transition'), 
					'description' => __("Simplify Slide transitions when the duotone filter is used.  Useful for improving visuals and performance", 'revslider-duotone-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/duotone-filters-addon/', 
					'video' => false,
					'section' => 'Slider Settings -> Duotone',
					'highlight' => array(
					
						'menu' => "#module_settings_trigger, #gst_sl_revslider-duotonefilters-addon", 
						'scrollTo' => '#form_slidergeneral_revslider-duotonefilters-addon', 
						'focus' => "*[data-r='addOns.revslider-duotonefilters-addon.simplify.enable']"
						
					)
					
				),
				
				'easing' => array(
					
					'buttonTitle' => __('Duotone Easing', 'revslider-duotone-addon'), 
					'title' => __('Transition Easing', 'revslider-duotone-addon'),
					'helpPath' => 'addOns.revslider-duotonefilters-addon.simplify.easing', 
					'keywords' => array('addon', 'addons', 'duotone', 'duotone addon', 'duotone filter', 'filter', 'filters', 'background', 'background filter', 'duotone transition', 'duotone easing'), 
					'description' => __("The CSS easing equation to use when simplified transitions are enabled", 'revslider-duotone-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/duotone-filters-addon/', 
					'video' => false,
					'section' => 'Slider Settings -> Duotone',
					'highlight' => array(
						
						'dependencies' => array(array('path' => 'settings.addOns.revslider-duotonefilters-addon.simplify.enable', 'value' => true, 'option' => 'duotone_simplify')), 
						'menu' => "#module_settings_trigger, #gst_sl_revslider-duotonefilters-addon", 
						'scrollTo' => '#form_slidergeneral_revslider-duotonefilters-addon', 
						'focus' => "#duotone_easing"
						
					)
					
				),
				
				'duration' => array(
					
					'buttonTitle' => __('Duotone Transition Duration', 'revslider-duotone-addon'), 
					'title' => __('Transition Duration', 'revslider-duotone-addon'),
					'helpPath' => 'addOns.revslider-duotonefilters-addon.simplify.duration', 
					'keywords' => array('addon', 'addons', 'duotone', 'duotone addon', 'duotone filter', 'filter', 'filters', 'background', 'background filter', 'duotone transition', 'duotone easing'), 
					'description' => __("The duration in milliseconds for the simplified transitions", 'revslider-duotone-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/duotone-filters-addon/', 
					'video' => false,
					'section' => 'Slider Settings -> Duotone',
					'highlight' => array(
						
						'dependencies' => array(array('path' => 'settings.addOns.revslider-duotonefilters-addon.simplify.enable', 'value' => true, 'option' => 'duotone_simplify')), 
						'menu' => "#module_settings_trigger, #gst_sl_revslider-duotonefilters-addon", 
						'scrollTo' => '#form_slidergeneral_revslider-duotonefilters-addon', 
						'focus' => "*[data-r='addOns.revslider-duotonefilters-addon.simplify.duration']"
						
					)
					
				)
			
			),
			
			'slide' => array(
				
				'bg_filter' => array(
						
					'buttonTitle' => __('Duotone Filter', 'revslider-duotone-addon'), 
					'title' => __('BG Filter', 'revslider-duotone-addon'),
					'helpPath' => 'addOns.revslider-duotonefilters-addon.filter', 
					'keywords' => array('addon', 'addons', 'duotone', 'duotone addon', 'duotone filter', 'filter', 'filters', 'background', 'background filter'), 
					'description' => __("Add a duotone style filter to the Slide's main background", 'revslider-duotone-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/duotone-filters-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Duotone',
					'highlight' => array(
					
						'menu' => "#module_slide_trigger, #gst_slide_revslider-duotonefilters-addon", 
						'scrollTo' => '#form_slidegeneral_revslider-duotonefilters-addon', 
						'focus' => "#duotone_bg_filter"
						
					)
					
				)
				
			)
			
		);
		
	}

}
	
?>