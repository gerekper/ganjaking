<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnPanoramaBase {
	
	const MINIMUM_VERSION = '6.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnPanoramaBase::MINIMUM_VERSION, '>=')) {
		
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
			$update_admin = new RevAddOnPanoramaUpdate(static::$_Version);

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
		
		new RsPanoramaSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsPanoramaSlideFront(static::$_PluginTitle);
		
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
			wp_localize_script($_handle, 'revslider_panorama_addon', self::get_var() );

		}
		
	}
	
	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public static function get_var() {
	
		$_textdomain = 'revslider-panorama-addon';
		return array(
		
			'bricks' => array(
				'panorama' => __('Panorama', $_textdomain),
				'lockvertical' => __('Lock Vertical', $_textdomain),
				'placeholder' => __('Select', $_textdomain),
				'active' => __('Active', $_textdomain),
				'autoplay' => __('Autoplay', $_textdomain),
				'settings' => __('Panorama Settings', $_textdomain),
				'direction' => __('Direction', $_textdomain),
				'forward' => __('Forward', $_textdomain),
				'backward' => __('Backward', $_textdomain),
				'interaction' => __('Interaction', $_textdomain),
				'throww' => __('Throw', $_textdomain),
				'drag' => __('Drag', $_textdomain),
				'mouse' => __('Mouse', $_textdomain),
				'click' => __('Click', $_textdomain),
				'none' => __('None', $_textdomain),
				'interaction' => __('Interaction', $_textdomain),
				'speed' => __('Speed', $_textdomain),
				'zoom' => __('Mousehweel Zoom', $_textdomain),
				'smooth' => __('Easing', $_textdomain),
				'camera_sphere' => __('Camera / Sphere', $_textdomain),
				'radius' => __('Radius', $_textdomain),
				'width' => __('Width', $_textdomain),
				'height' => __('Height', $_textdomain),
				'distance' => __('Distance', $_textdomain),
				'actions_left' => __('Pan Left', $_textdomain),
				'actions_leftstart' => __('Pan Left Start', $_textdomain),
				'actions_leftend' => __('Pan Left End', $_textdomain),
				'actions_right' => __('Pan Right', $_textdomain),
				'actions_rightstart' => __('Pan Right Start', $_textdomain),
				'actions_rightend' => __('Pan Right End', $_textdomain),
				'actions_up' => __('Pan Up', $_textdomain),
				'actions_upstart' => __('Pan Up Start', $_textdomain),
				'actions_upend' => __('Pan Up End', $_textdomain),
				'actions_down' => __('Pan Down', $_textdomain),
				'actions_downstart' => __('Pan Down Start', $_textdomain),
				'actions_downend' => __('Pan Down End', $_textdomain),
				'actions_zoomin' => __('Zoom In', $_textdomain),
				'actions_zoominstart' => __('Zoom In Start', $_textdomain),
				'actions_zoominend' => __('Zoom In End', $_textdomain),
				'actions_zoomout' => __('Zoom Out', $_textdomain),
				'actions_zoomoutstart' => __('Zoom Out Start', $_textdomain),
				'actions_zoomoutend' => __('Zoom Out End', $_textdomain),
				'mobile_settings' => __('Mobile Movement Settings', $_textdomain),
				'mobile_lock' => __('Left/Right Only', $_textdomain)
				
			)
		);
	
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-panorama-addon') {
		
		if($slug === 'revslider-panorama-addon'){
			
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
		
		if(isset($definitions['editor_settings']['slide_settings']) && isset($definitions['editor_settings']['slide_settings']['addons'])) {
			$definitions['editor_settings']['slide_settings']['addons']['panorama_addon'] = $help['slide'];
		}
		
		if(isset($definitions['editor_settings']['layer_settings']) && isset($definitions['editor_settings']['layer_settings']['addons'])) {
			$definitions['editor_settings']['layer_settings']['addons']['panorama_addon'] = $help['layer'];
		}
		
		return $definitions;
	
	}
	
	/**
	 * Returns the addon help definitions
	 *
	 * @since    2.0.0
	 */
	private static function get_definitions() {
		
		$_textdomain = 'revslider-panorama-addon';
		return array(
		
			'slide' => array(
			
				'settings' => array(
					
					'enable' => array(
						
						'dependency_id' => 'panorama_enable',
						'buttonTitle' => __('Enable Panorama', $_textdomain), 
						'title' => __('Enable', $_textdomain),
						'helpPath' => 'addOns.revslider-panorama-addon.enable', 
						'keywords' => array('addon', 'addons', 'panorama', 'panorama addon', 'enable panorama', 'activate panorama'), 
						'description' => __('Enable the Panorama AddOn for this Slide', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/panorama-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Panorama',
						'highlight' => array(
						
							'menu' => "#module_slide_trigger, #gst_slide_revslider-panorama-addon", 
							'scrollTo' => '#form_slidegeneral_revslider-panorama-addon', 
							'focus' => "#panorama_enable"
							
						)
						
					),
					'autoplay' => array(
						
						'dependency_id' => 'panorama_autoplay',
						'buttonTitle' => __('Panorama Autoplay', $_textdomain), 
						'title' => __('Autoplay', $_textdomain),
						'helpPath' => 'addOns.revslider-panorama-addon.autoplay.enable', 
						'keywords' => array('addon', 'addons', 'panorama', 'panorama addon', 'panorama autoplay', 'autoplay panorama'), 
						'description' => __('Auto-move the Panorama image for this Slide', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/panorama-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Panorama',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-panorama-addon.enable', 'value' => true, 'option' => 'panorama_enable')), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-panorama-addon", 
							'scrollTo' => '#form_slidegeneral_revslider-panorama-addon', 
							'focus' => "*[data-r='addOns.revslider-panorama-addon.autoplay.enable']"
							
						)
						
					),
					'direction' => array(
						
						'buttonTitle' => __('Panorama Direction', $_textdomain), 
						'title' => __('Autoplay Direction', $_textdomain),
						'helpPath' => 'addOns.revslider-panorama-addon.autoplay.direction', 
						'keywords' => array('addon', 'addons', 'panorama', 'panorama addon', 'panorama direction', 'direction'), 
						'description' => __('Choose if the image should auto-move forward or backward', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/panorama-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Panorama',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-panorama-addon.enable', 'value' => true, 'option' => 'panorama_enable'),
								array('path' => '#slide#.slide.addOns.revslider-panorama-addon.autoplay.enable', 'value' => true, 'option' => 'panorama_autoplay'),
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-panorama-addon", 
							'scrollTo' => '#form_slidegeneral_revslider-panorama-addon', 
							'focus' => "#panorama_direction"
							
						)
						
					),
					'speed' => array(
						
						'buttonTitle' => __('Panorama Speed', $_textdomain), 
						'title' => __('Autoplay Speed', $_textdomain),
						'helpPath' => 'addOns.revslider-panorama-addon.autoplay.speed', 
						'keywords' => array('addon', 'addons', 'panorama', 'panorama addon', 'panorama speed'), 
						'description' => __('The autoplay speed for the panorama image (milliseconds)', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/panorama-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Panorama',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-panorama-addon.enable', 'value' => true, 'option' => 'panorama_enable'),
								array('path' => '#slide#.slide.addOns.revslider-panorama-addon.autoplay.enable', 'value' => true, 'option' => 'panorama_autoplay'),
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-panorama-addon", 
							'scrollTo' => '#form_slidegeneral_revslider-panorama-addon', 
							'focus' => "*[data-r='addOns.revslider-panorama-addon.autoplay.speed']"
							
						)
						
					)
					
				),
				
				'interaction' => array(
				
					'controls' => array(
						
						'dependency_id' => 'panorama_controls',
						'buttonTitle' => __('Panorama Controls', $_textdomain),
						'title' => __('Controls', $_textdomain),
						'helpPath' => 'addOns.revslider-panorama-addon.interaction.controls', 
						'keywords' => array('addon', 'addons', 'panorama', 'panorama addon', 'panorama interaction', 'panorama controls'), 
						'description' => __('Choose how the panorama should move on user-interaction', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/panorama-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Panorama -> Interaction',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-panorama-addon.enable', 'value' => true, 'option' => 'panorama_enable')), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-panorama-addon", 
							'scrollTo' => '#panorama-interaction-wrap', 
							'focus' => "#panorama_interaction"
							
						)
					
					),
					'throw_speed' => array(
						
						'title' => __('Throw Speed', $_textdomain), 
						'helpPath' => 'addOns.revslider-panorama-addon.interaction.speed', 
						'keywords' => array('addon', 'addons', 'panorama', 'panorama addon', 'panorama interaction', 'panorama speed'), 
						'description' => __('Determines how much movement will occur for the Panorama "Throw" control', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/panorama-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Panorama -> Interaction',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-panorama-addon.enable', 'value' => true, 'option' => 'panorama_enable'),
								array('path' => '#slide#.slide.addOns.revslider-panorama-addon.interaction.controls', 'value' => 'throw', 'option' => 'panorama_controls')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-panorama-addon", 
							'scrollTo' => '#panorama-interaction-wrap', 
							'focus' => "*[data-r='addOns.revslider-panorama-addon.interaction.speed']"
							
						)
					
					)
				
				),
				'mousewheel_zoom' => array(
					
					'enable' => array(
					
						'dependency_id' => 'panorama_mouse',
						'buttonTitle' => __('Mouse Zoom', $_textdomain), 
						'title' => __('Enable', $_textdomain),
						'helpPath' => 'addOns.revslider-panorama-addon.zoom.enable', 
						'keywords' => array('addon', 'addons', 'panorama', 'panorama addon', 'panorama mousewheel', 'panorama zoom'), 
						'description' => __('Zoom the panorama image in and out with the mouse-wheel', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/panorama-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Panorama -> Mousewheel Zoom',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-panorama-addon.enable', 'value' => true, 'option' => 'panorama_enable')), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-panorama-addon", 
							'scrollTo' => '#panorama-mousewheel-wrap', 
							'focus' => "*[data-r='addOns.revslider-panorama-addon.zoom.enable']"
							
						)
					
					),
					'mousewheel_easing' => array(
					
						'buttonTitle' => __('Zoom Easing', $_textdomain), 
						'title' => __('Easing', $_textdomain),
						'helpPath' => 'addOns.revslider-panorama-addon.zoom.smooth', 
						'keywords' => array('addon', 'addons', 'panorama', 'panorama addon', 'panorama mousewheel', 'panorama zoom', 'panorama easing'), 
						'description' => __('Apply transition smoothing to the mousehweel zoom movement', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/panorama-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Panorama -> Mousewheel Zoom',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-panorama-addon.enable', 'value' => true, 'option' => 'panorama_enable'),
								array('path' => '#slide#.slide.addOns.revslider-panorama-addon.zoom.enable', 'value' => true, 'option' => 'panorama_mouse'),
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-panorama-addon", 
							'scrollTo' => '#panorama-mousewheel-wrap', 
							'focus' => "*[data-r='addOns.revslider-panorama-addon.zoom.smooth']"
							
						)
					
					),
					'mousewheel_zoom_min' => array(
					
						'title' => __('Min Zoom', $_textdomain),
						'helpPath' => 'addOns.revslider-panorama-addon.zoom.min', 
						'keywords' => array('addon', 'addons', 'panorama', 'panorama addon', 'panorama mousewheel', 'panorama zoom', 'min zoom'), 
						'description' => __('The minimum percentage the image can zoom to', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/panorama-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Panorama -> Mousewheel Zoom',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-panorama-addon.enable', 'value' => true, 'option' => 'panorama_enable'),
								array('path' => '#slide#.slide.addOns.revslider-panorama-addon.zoom.enable', 'value' => true, 'option' => 'panorama_mouse'),
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-panorama-addon", 
							'scrollTo' => '#panorama-mousewheel-wrap', 
							'focus' => "*[data-r='addOns.revslider-panorama-addon.zoom.min']"
							
						)
					
					),
					'mousewheel_zoom_max' => array(
					
						'title' => __('Max Zoom', $_textdomain),
						'helpPath' => 'addOns.revslider-panorama-addon.zoom.max', 
						'keywords' => array('addon', 'addons', 'panorama', 'panorama addon', 'panorama mousewheel', 'panorama zoom', 'max zoom'), 
						'description' => __('The maximum percentage the image can zoom to', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/panorama-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Panorama -> Mousewheel Zoom',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-panorama-addon.enable', 'value' => true, 'option' => 'panorama_enable'),
								array('path' => '#slide#.slide.addOns.revslider-panorama-addon.zoom.enable', 'value' => true, 'option' => 'panorama_mouse'),
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-panorama-addon", 
							'scrollTo' => '#panorama-mousewheel-wrap', 
							'focus' => "*[data-r='addOns.revslider-panorama-addon.zoom.max']"
							
						)
					
					)
					
				),
				'camera_sphere' => array(
				
					'radius' => array(
					
						'title' => __('Sphere Radius', $_textdomain),
						'helpPath' => 'addOns.revslider-panorama-addon.sphere.radius', 
						'keywords' => array('panorama radius', 'panorama sphere'), 
						'description' => __("The number of radians applied to the camera's sphere", $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/panorama-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Panorama -> Camera/Sphere',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-panorama-addon.enable', 'value' => true, 'option' => 'panorama_enable')), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-panorama-addon", 
							'scrollTo' => '#panorama-camera-wrap', 
							'focus' => "*[data-r='addOns.revslider-panorama-addon.sphere.radius']"
							
						)
					
					),
					'wsegments' => array(
					
						'title' => __('Width Segments', $_textdomain),
						'helpPath' => 'addOns.revslider-panorama-addon.sphere.wsegments', 
						'keywords' => array('panorama segments'), 
						'description' => __("The number of horizontal segments for the 3D camera", $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/panorama-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Panorama -> Camera/Sphere',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-panorama-addon.enable', 'value' => true, 'option' => 'panorama_enable')), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-panorama-addon", 
							'scrollTo' => '#panorama-camera-wrap', 
							'focus' => "*[data-r='addOns.revslider-panorama-addon.sphere.wsegments']"
							
						)
					
					),
					'hsegments' => array(
					
						'title' => __('Height Segments', $_textdomain),
						'helpPath' => 'addOns.revslider-panorama-addon.sphere.hsegments', 
						'keywords' => array('panorama segments'), 
						'description' => __("The number of vertical segments for the 3D camera", $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/panorama-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Panorama -> Camera/Sphere',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-panorama-addon.enable', 'value' => true, 'option' => 'panorama_enable')), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-panorama-addon", 
							'scrollTo' => '#panorama-camera-wrap', 
							'focus' => "*[data-r='addOns.revslider-panorama-addon.sphere.hsegments']"
							
						)
					
					),
					'camerafov' => array(
					
						'title' => __('Camera Fov', $_textdomain),
						'helpPath' => 'addOns.revslider-panorama-addon.camera.fov', 
						'keywords' => array('panorama camera'), 
						'description' => __("Camera frustrum vertical field of view", $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/panorama-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Panorama -> Camera/Sphere',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-panorama-addon.enable', 'value' => true, 'option' => 'panorama_enable')), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-panorama-addon", 
							'scrollTo' => '#panorama-camera-wrap', 
							'focus' => "*[data-r='addOns.revslider-panorama-addon.camera.fov']"
							
						)
					
					),
					'camerafar' => array(
					
						'title' => __('Camera Far', $_textdomain),
						'helpPath' => 'addOns.revslider-panorama-addon.camera.far', 
						'keywords' => array('panorama camera'), 
						'description' => __("Camera frustrum far plane", $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/panorama-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Panorama -> Camera/Sphere',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-panorama-addon.enable', 'value' => true, 'option' => 'panorama_enable')), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-panorama-addon", 
							'scrollTo' => '#panorama-camera-wrap', 
							'focus' => "*[data-r='addOns.revslider-panorama-addon.camera.far']"
							
						)
					
					)
					
				),
				
				'mobile' => array(
				
					'mobile_lock' => array(
					
						'title' => __('Left/Right Only', $_textdomain),
						'helpPath' => 'addOns.revslider-panorama-addon.mobilelock', 
						'keywords' => array('panorama mobile', 'mobile', 'mobile scrolling'), 
						'description' => __("Restrict user-interaction movement on mobile devices to horizontal only.  Improves the effect when combined with web pages that scroll vertically.", $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/panorama-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Panorama -> Mobile',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-panorama-addon.enable', 'value' => true, 'option' => 'panorama_enable')), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-panorama-addon", 
							'scrollTo' => '#panorama-mobile-settings', 
							'focus' => "*[data-r='addOns.revslider-panorama-addon.mobilelock']"
							
						)
					
					)
				)
				
			),
			
			'layer' => array(
				
				'actions' => array(
					
					'pan_left' => array(
								
						'title' => __("Pan Left", $_textdomain),
						'helpPath' => "actions.action.#actionindex#.pan_left",
						'keywords' => array("action", "actions", "panorama", "pan left"),
						'description' => __("Move the image left on user-interaction.  Use 'Start' and 'End' for mouseenter/mouseleave events", $_textdomain),
						'helpStyle' => "normal",
						'article' => "https://www.themepunch.com/support-center/",
						'video' => false,
						'section' => "Layer Settings -> Actions -> Panorama",
						'highlight' => array(
								
							'dependencies' => array(
							
								'.single_layer_action:first-child', 
								array('path' => '#slide#.layers.#layer#.actions.action.#action#.action', 'value' => 'panorama_left::panorama_leftstart::panorama_leftend', 'option' => 'layer_action_type')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_5", 
							'scrollTo' => '{actions}#layeraction_group_panorama', 
							'focus' => "#layeraction_picker_panorama_left, #layeraction_picker_panorama_leftstart, #layeraction_picker_panorama_leftend, #layer_action_type",
							'modal' => 'actions'
							
						)

					),
					'pan_right' => array(
								
						'title' => __("Pan Right", $_textdomain),
						'helpPath' => "actions.action.#actionindex#.pan_right",
						'keywords' => array("action", "actions", "panorama", "pan right"),
						'description' => __("Move the image right on user-interaction.  Use 'Start' and 'End' for mouseenter/mouseleave events", $_textdomain),
						'helpStyle' => "normal",
						'article' => "https://www.themepunch.com/support-center/",
						'video' => false,
						'section' => "Layer Settings -> Actions -> Panorama",
						'highlight' => array(
								
							'dependencies' => array(
							
								'.single_layer_action:first-child', 
								array('path' => '#slide#.layers.#layer#.actions.action.#action#.action', 'value' => 'panorama_right::panorama_rightstart::panorama_rightend', 'option' => 'layer_action_type')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_5", 
							'scrollTo' => '{actions}#layeraction_group_panorama', 
							'focus' => "#layeraction_picker_panorama_right, #layeraction_picker_panorama_rightstart, #layeraction_picker_panorama_rightend, #layer_action_type",
							'modal' => 'actions'
							
						)

					),
					'pan_up' => array(
								
						'title' => __("Pan Up", $_textdomain),
						'helpPath' => "actions.action.#actionindex#.pan_up",
						'keywords' => array("action", "actions", "panorama", "pan up"),
						'description' => __("Move the image up on user-interaction.  Use 'Start' and 'End' for mouseenter/mouseleave events", $_textdomain),
						'helpStyle' => "normal",
						'article' => "https://www.themepunch.com/support-center/",
						'video' => false,
						'section' => "Layer Settings -> Actions -> Panorama",
						'highlight' => array(
								
							'dependencies' => array(
							
								'.single_layer_action:first-child', 
								array('path' => '#slide#.layers.#layer#.actions.action.#action#.action', 'value' => 'panorama_up::panorama_upstart::panorama_upend', 'option' => 'layer_action_type')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_5", 
							'scrollTo' => '{actions}#layeraction_group_panorama', 
							'focus' => "#layeraction_picker_panorama_up, #layeraction_picker_panorama_upstart, #layeraction_picker_panorama_upend, #layer_action_type",
							'modal' => 'actions'
							
						)

					),
					'pan_down' => array(
								
						'title' => __("Pan Down", $_textdomain),
						'helpPath' => "actions.action.#actionindex#.pan_down",
						'keywords' => array("action", "actions", "panorama", "pan down"),
						'description' => __("Move the image down on user-interaction.  Use 'Start' and 'End' for mouseenter/mouseleave events", $_textdomain),
						'helpStyle' => "normal",
						'article' => "https://www.themepunch.com/support-center/",
						'video' => false,
						'section' => "Layer Settings -> Actions -> Panorama",
						'highlight' => array(
								
							'dependencies' => array(
							
								'.single_layer_action:first-child', 
								array('path' => '#slide#.layers.#layer#.actions.action.#action#.action', 'value' => 'panorama_down::panorama_downstart::panorama_downend', 'option' => 'layer_action_type')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_5", 
							'scrollTo' => '{actions}#layeraction_group_panorama', 
							'focus' => "#layeraction_picker_panorama_down, #layeraction_picker_panorama_downstart, #layeraction_picker_panorama_downend, #layer_action_type",
							'modal' => 'actions'
							
						)

					),
					'zoom_in' => array(
								
						'title' => __("Zoom In", $_textdomain),
						'helpPath' => "actions.action.#actionindex#.zoom_in",
						'keywords' => array("action", "actions", "panorama", "panorama zoom"),
						'description' => __("Zoom the image in on user-interaction.  Use 'Start' and 'End' for mouseenter/mouseleave events", $_textdomain),
						'helpStyle' => "normal",
						'article' => "https://www.themepunch.com/support-center/",
						'video' => false,
						'section' => "Layer Settings -> Actions -> Panorama",
						'highlight' => array(
								
							'dependencies' => array(
							
								'.single_layer_action:first-child', 
								array('path' => '#slide#.layers.#layer#.actions.action.#action#.action', 'value' => 'panorama_zoomin::panorama_zoominstart::panorama_zoominend', 'option' => 'layer_action_type')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_5", 
							'scrollTo' => '{actions}#layeraction_group_panorama', 
							'focus' => "#layeraction_picker_panorama_zoomin, #layeraction_picker_panorama_zoominstart, #layeraction_picker_panorama_zoominend, #layer_action_type",
							'modal' => 'actions'
							
						)

					),
					'zoom_out' => array(
								
						'title' => __("Zoom Out", $_textdomain),
						'helpPath' => "actions.action.#actionindex#.zoom_out",
						'keywords' => array("action", "actions", "panorama", "panorama zoom"),
						'description' => __("Zoom the image out on user-interaction.  Use 'Start' and 'End' for mouseenter/mouseleave events", $_textdomain),
						'helpStyle' => "normal",
						'article' => "https://www.themepunch.com/support-center/",
						'video' => false,
						'section' => "Layer Settings -> Actions -> Panorama",
						'highlight' => array(
								
							'dependencies' => array(
							
								'.single_layer_action:first-child', 
								array('path' => '#slide#.layers.#layer#.actions.action.#action#.action', 'value' => 'panorama_zoomout::panorama_zoomoutstart::panorama_zoomoutend', 'option' => 'layer_action_type')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_5", 
							'scrollTo' => '{actions}#layeraction_group_panorama', 
							'focus' => "#layeraction_picker_panorama_zoomout, #layeraction_picker_panorama_zoomoutstart, #layeraction_picker_panorama_zoomoutend, #layer_action_type",
							'modal' => 'actions'
							
						)

					),
					'pan_zoom_distance' => array(
					
						'title' => __("Zoom/Pan Distance", $_textdomain),
						'helpPath' => "actions.action.#actionindex#.panorama_amount",
						'keywords' => array("action", "actions", "panorama", "panorama zoom"),
						'description' => __("Zoom/Pan the image by this percentage on user-interation", $_textdomain),
						'helpStyle' => "normal",
						'article' => "https://www.themepunch.com/support-center/",
						'video' => false,
						'section' => "Layer Settings -> Actions -> Panorama",
						'highlight' => array(
								
							'dependencies' => array(
							
								'.single_layer_action:first-child', 
								array('path' => '#slide#.layers.#layer#.actions.action.#action#.action', 'value' => 'panorama_left::panorama_leftstart::panorama_leftend::panorama_right::panorama_rightstart::panorama_rightend::panorama_up::panorama_upstart::panorama_upend::panorama_down::panorama_downstart::panorama_downend::panorama_zoomin::panorama_zoominstart::panorama_zoominend::panorama_zoomout::panorama_zoomoutstart::panorama_zoomoutend', 'option' => 'layer_action_type')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_5", 
							'scrollTo' => '{actions}#layeraction_group_panorama', 
							'focus' => "#panorama_amount, #layeraction_picker_panorama_left",
							'modal' => 'actions'
							
						)
					
					)
					
				)
				
			)
			
		);
	
	}

}
	
?>