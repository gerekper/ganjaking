<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnFilmstripBase {
	
	const MINIMUM_VERSION = '6.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnFilmstripBase::MINIMUM_VERSION, '>=')) {
		
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
			$update_admin = new RevAddOnFilmstripUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'));
			add_filter('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
			require_once(static::$_PluginPath . 'admin/includes/slider.class.php');
			
			// admin init
			new RsFilmstripSliderAdmin();			
			
		}
		
		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		require_once(static::$_PluginPath . 'public/includes/slide.class.php');
		
		new RsFilmstripSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsFilmstripSlideFront(static::$_PluginTitle);
		
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
									
			wp_enqueue_style($_handle, $_base . 'css/revslider-' . static::$_PluginTitle . '-addon-admin.css', array(), static::$_Version);
			wp_enqueue_script($_handle, $_base . 'js/revslider-' . static::$_PluginTitle . '-addon-admin.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			wp_localize_script( $_handle, 'revslider_filmstrip_addon', self::get_var() );
		}
		
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-filmstrip-addon') {
		
		if($slug === 'revslider-filmstrip-addon'){
			
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
		
		if(isset($definitions['editor_settings']['slide_settings']) && isset($definitions['editor_settings']['slide_settings']['addons'])) {
			$help = self::get_definitions();
			$definitions['editor_settings']['slide_settings']['addons']['filmstrip_addon'] = $help['slide'];
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
			'filmstrip' => __('Filmstrip','revslider-filmstrip-addon'),
			'placeholder' => __('Select','revslider-filmstrip-addon'),
			'active' => __('Active','revslider-filmstrip-addon'),
			'rtl' => __('Right to Left','revslider-filmstrip-addon'),
			'ltr' => __('Left to Right','revslider-filmstrip-addon'),
			'ttb' => __('Top to Bottom','revslider-filmstrip-addon'),
			'btt' => __('Bottom to Top','revslider-filmstrip-addon'),
			'movefrom' => __('Move from','revslider-filmstrip-addon'),
			'ssize' => __('Source Size','revslider-filmstrip-addon'),
			'alttext' => __('Alt Text','revslider-filmstrip-addon'),
			'from' => __('From','revslider-filmstrip-addon'),
			'medialibrary' => __('Media Library','revslider-filmstrip-addon'),
			'filename' => __('File Name','revslider-filmstrip-addon'),
			'objectlibrary' => __('Object Library','revslider-filmstrip-addon'),
			'url' => __('URL','revslider-filmstrip-addon'),
			'original' => __('Original','revslider-filmstrip-addon'),
			'large' => __('Large','revslider-filmstrip-addon'),
			'medium' => __('Medium','revslider-filmstrip-addon'),
			'small' => __('Small','revslider-filmstrip-addon'),
			'thumb' => __('Thumbnail','revslider-filmstrip-addon'),
			'deleteslide' => __('Delete Selected Slide','revslider-filmstrip-addon'),
			'addnewslide' => __('Add Slide','revslider-filmstrip-addon'),
			'sortslide' => __('Sort Filmstrip Slide','revslider-filmstrip-addon'),
			'custom' => __('Custom','revslider-filmstrip-addon'),
			'cantusekenburn' => __('Disable Film Strip on Slide to use Pan Zoom Effect again.','revslider-filmstrip-addon')
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
			
			'slide' => array(
				
				'enable' => array(
					
					'dependency_id' => 'filmstrip_enable',
					'buttonTitle' => __('Enable FilmStrip', 'revslider-filmstrip-addon'), 
					'title' => __('Enable', 'revslider-filmstrip-addon'),
					'helpPath' => 'addOns.revslider-filmstrip-addon.enable', 
					'keywords' => array('addon', 'addons', 'filmstrip', 'filmstrip addon', 'enable', 'enable filmstrip'), 
					'description' => __("Enable the FilmStrip AddOn for the current Slide", 'revslider-filmstrip-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/filmstrip-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Filmstrip',
					'highlight' => array(
						
						'menu' => "#module_slide_trigger, #gst_slide_revslider-filmstrip-addon", 
						'scrollTo' => '#form_slidegeneral_revslider-filmstrip-addon', 
						'focus' => "*[data-r='addOns.revslider-filmstrip-addon.enable']"
						
					)
					
				),
				
				'direction' => array(
					
					'title' => __('FilmStrip Direction', 'revslider-filmstrip-addon'),
					'helpPath' => 'addOns.revslider-filmstrip-addon.direction', 
					'keywords' => array('addon', 'addons', 'filmstrip', 'filmstrip addon', 'move from', 'filmstrip move from'), 
					'description' => __("Choose if the images should move right to left or left to right", 'revslider-filmstrip-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/filmstrip-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Filmstrip',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-filmstrip-addon.enable', 'value' => true, 'option' => 'filmstrip_enable')),
						'menu' => "#module_slide_trigger, #gst_slide_revslider-filmstrip-addon", 
						'scrollTo' => '#form_slidegeneral_revslider-filmstrip-addon', 
						'focus' => "*[data-r='addOns.revslider-filmstrip-addon.direction']"
						
					)
					
				),
				
				'speed' => array(
					
					'title' => __('FilmStrip Speed', 'revslider-filmstrip-addon'),
					'helpPath' => 'addOns.revslider-filmstrip-addon.times.#size#.v', 
					'keywords' => array('addon', 'addons', 'filmstrip', 'filmstrip addon', 'speed', 'filmstrip speed'), 
					'description' => __("The speed for the FilmStrip movement.  Can be customized for each of the Slider's viewports", 'revslider-filmstrip-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/filmstrip-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Filmstrip',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-filmstrip-addon.enable', 'value' => true, 'option' => 'filmstrip_enable')),
						'menu' => "#module_slide_trigger, #gst_slide_revslider-filmstrip-addon", 
						'scrollTo' => '#form_slidegeneral_revslider-filmstrip-addon', 
						'focus' => "*[data-r='addOns.revslider-filmstrip-addon.times.#size#.v']"
						
					)
					
				),
				
				'mobile' => array(
					
					'title' => __('Disable on Mobile', 'revslider-filmstrip-addon'),
					'helpPath' => 'addOns.revslider-filmstrip-addon.mobile', 
					'keywords' => array('addon', 'addons', 'filmstrip', 'filmstrip addon', 'mobile', 'filmstrip mobile'), 
					'description' => __("Choose to disable the FilmStrip effect for mobile devices", 'revslider-filmstrip-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/filmstrip-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Filmstrip',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-filmstrip-addon.enable', 'value' => true, 'option' => 'filmstrip_enable')),
						'menu' => "#module_slide_trigger, #gst_slide_revslider-filmstrip-addon", 
						'scrollTo' => '#form_slidegeneral_revslider-filmstrip-addon', 
						'focus' => "*[data-r='addOns.revslider-filmstrip-addon.mobile']"
						
					)
					
				),
				
				'image_slide' => array(
					
					'title' => __('Image Slide', 'revslider-filmstrip-addon'),
					'helpPath' => 'film-strip-preview-image', 
					'keywords' => array('addon', 'addons', 'filmstrip', 'filmstrip addon', 'filmstrip image'), 
					'description' => __("Drag and drop the images next to one another to define a custom order, and set the image from the Media/Object Library buttons below.", 'revslider-filmstrip-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/filmstrip-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Filmstrip',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-filmstrip-addon.enable', 'value' => true, 'option' => 'filmstrip_enable')),
						'menu' => "#module_slide_trigger, #gst_slide_revslider-filmstrip-addon", 
						'scrollTo' => '#form_slidegeneral_revslider-filmstrip-addon', 
						'focus' => "{first}*[data-helpkey='film-strip-preview-image']"
						
					)
					
				),
				
				'image_url' => array(
					
					'title' => __('Image URL', 'revslider-filmstrip-addon'),
					'helpPath' => 'addOns.revslider-filmstrip-addon.settings.0.url, addOns.revslider-filmstrip-addon.settings.1.url, addOns.revslider-filmstrip-addon.settings.2.url, addOns.revslider-filmstrip-addon.settings.3.url, addOns.revslider-filmstrip-addon.settings.4.url, addOns.revslider-filmstrip-addon.settings.5.url', 
					'keywords' => array('addon', 'addons', 'filmstrip', 'filmstrip addon', 'image url', 'filmstrip url'), 
					'description' => __("The URL for the selected FilmStrip image", 'revslider-filmstrip-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/filmstrip-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Filmstrip',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-filmstrip-addon.enable', 'value' => true, 'option' => 'filmstrip_enable')),
						'menu' => "#module_slide_trigger, #gst_slide_revslider-filmstrip-addon", 
						'scrollTo' => '#form_slidegeneral_revslider-filmstrip-addon', 
						'focus' => '#filmstrip_imageurl_currentEditing'
						
					)
					
				),
				
				'source_size' => array(
					
					'title' => __('Image Source Size', 'revslider-filmstrip-addon'),
					'helpPath' => 'addOns.revslider-filmstrip-addon.settings.0.size, addOns.revslider-filmstrip-addon.settings.1.size, addOns.revslider-filmstrip-addon.settings.2.size, addOns.revslider-filmstrip-addon.settings.3.size, addOns.revslider-filmstrip-addon.settings.4.size, addOns.revslider-filmstrip-addon.settings.5.size', 
					'keywords' => array('addon', 'addons', 'filmstrip', 'filmstrip addon', 'source size', 'filmstrip source size'), 
					'description' => __("The WP image size to load for the selected FilmStrip image", 'revslider-filmstrip-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/filmstrip-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Filmstrip',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-filmstrip-addon.enable', 'value' => true, 'option' => 'filmstrip_enable')),
						'menu' => "#module_slide_trigger, #gst_slide_revslider-filmstrip-addon", 
						'scrollTo' => '#form_slidegeneral_revslider-filmstrip-addon', 
						'focus' => '#filmstrip_source_size'
						
					)
					
				),
				
				'alt_text' => array(
					
					'title' => __('Image Alt Text', 'revslider-filmstrip-addon'),
					'helpPath' => 'addOns.revslider-filmstrip-addon.settings.0.alt, addOns.revslider-filmstrip-addon.settings.1.alt, addOns.revslider-filmstrip-addon.settings.2.alt, addOns.revslider-filmstrip-addon.settings.3.alt, addOns.revslider-filmstrip-addon.settings.4.alt, addOns.revslider-filmstrip-addon.settings.5.alt, addOns.revslider-filmstrip-addon.settings.0.custom, addOns.revslider-filmstrip-addon.settings.1.custom, addOns.revslider-filmstrip-addon.settings.2.custom, addOns.revslider-filmstrip-addon.settings.3.custom, addOns.revslider-filmstrip-addon.settings.4.custom, addOns.revslider-filmstrip-addon.settings.5.custom', 
					'keywords' => array('addon', 'addons', 'filmstrip', 'filmstrip addon', 'alt text', 'filmstrip alt text'), 
					'description' => __("The alt attribute for the selected FilmStrip image", 'revslider-filmstrip-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/filmstrip-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Filmstrip',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-filmstrip-addon.enable', 'value' => true, 'option' => 'filmstrip_enable')),
						'menu' => "#module_slide_trigger, #gst_slide_revslider-filmstrip-addon", 
						'scrollTo' => '#form_slidegeneral_revslider-filmstrip-addon', 
						'focus' => '#filmstrip_alt_text, #filmstrip_imagealt_currentEditing'
						
					)
					
				)
				
			)
			
		);
		
	}

}
	
?>