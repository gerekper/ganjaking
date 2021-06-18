<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnPaintbrushBase {
	
	const MINIMUM_VERSION = '6.5.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnPaintbrushBase::MINIMUM_VERSION, '>=')) {
		
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
			$update_admin = new RevAddOnPaintbrushUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'));
			add_filter('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
			require_once(static::$_PluginPath . 'admin/includes/slider.class.php');
			
			// admin init
			new RsPaintbrushSliderAdmin();
			
		}
		
		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		require_once(static::$_PluginPath . 'public/includes/slide.class.php');
		
		new RsPaintbrushSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsPaintbrushSlideFront(static::$_PluginTitle);
		
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
			
			wp_enqueue_script($_handle, $_base . 'js/revslider-' . static::$_PluginTitle . '-addon-admin' . $_jsPathMin . '.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			wp_localize_script($_handle, 'revslider_paintbrush_addon', self::get_var() );

		}
		
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-paintbrush-addon') {
		
		if($slug === 'revslider-paintbrush-addon'){
			
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
			$definitions['editor_settings']['slide_settings']['addons']['paintbrush_addon'] = $help['slide'];
		}
		
		return $definitions;
	
	}
	
	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public static function get_var() {
			
		$_textdomain = 'revslider-paintbrush-addon';
		return array(
		
			'bricks' => array(
				'paintbrush' => __('Paintbrush', $_textdomain),
				'placeholder' => __('Select', $_textdomain),
				'active' => __('Active', $_textdomain),
				'imagesettings' => __('Image Settings', $_textdomain),
				'source' => __('Image Source', $_textdomain),
				'custom' => __('Custom Image', $_textdomain),
				'slidebg' => __('Slide Background', $_textdomain),
				'medialibrary' => __('Media Library', $_textdomain),
				'objectlibrary' => __('Object Library', $_textdomain),
				'blurimage' => __('Blur Image', $_textdomain),
				'bluramount' => __('Blur Amount', $_textdomain),
				'responsive' => __('Responsive', $_textdomain),
				'fixedges' => __('Fix Soft Edges', $_textdomain),
				'stretchamount' => __('Stretch By', $_textdomain),
				'brushsettings' => __('Brush Settings', $_textdomain),
				'brushstyle' => __('Brush Style', $_textdomain),
				'brushsize' => __('Brush Size', $_textdomain),
				'brushstrength' => __('Brush Strengh', $_textdomain),
				'disappear' => __('Disappear', $_textdomain),
				'fadetime' => __('Fade Time', $_textdomain),
				'disable' => __('Disable Mobile', $_textdomain),
				'fallback' => __('Use Fallback', $_textdomain),
				'mobile' => __('Mobile Settings', $_textdomain),
				'note' => __('Slide Main Background Image not set', $_textdomain)
				
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
					
					'dependency_id' => 'paintbrush_enable',
					'buttonTitle' => __('Enable Paintbrush Addon', 'revslider-paintbrush-addon'), 
					'title' => __('Enable AddOn', 'revslider-paintbrush-addon'),
					'helpPath' => 'addOns.revslider-paintbrush-addon.enable', 
					'keywords' => array('addon', 'addons', 'paintbrush', 'paintbrush addon'), 
					'description' => __("Enable the paintbrush AddOn for the current Slide", 'revslider-paintbrush-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/paintbrush-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Paintbrush',
					'highlight' => array(
					
						'menu' => "#module_slide_trigger, #gst_slide_revslider-paintbrush-addon", 
						'scrollTo' => '#form_slidegeneral_revslider-paintbrush-addon', 
						'focus' => "#paintbrush_enable"
						
					)
					
				),
				
				'image' => array(
					
					'title' => __('Paintbrush Image', 'revslider-paintbrush-addon'), 
					'helpPath' => 'addOns.revslider-paintbrush-addon.image.source', 
					'keywords' => array('addon', 'addons', 'paintbrush', 'paintbrush addon', 'image', 'paintbrush image'), 
					'description' => __('Set an image to "Paint" for the effect', 'revslider-paintbrush-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/paintbrush-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Paintbrush',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.enable', 'value' => true, 'option' => 'paintbrush_enable')),
						'menu' => "#module_slide_trigger, #gst_slide_revslider-paintbrush-addon", 
						'scrollTo' => '#form_slidegeneral_revslider-paintbrush-addon', 
						'focus' => "#paintbrush_image_source"
						
					)
					
				),
				
				'blur_image' => array(
				
					'enable' => array(
						
						'dependency_id' => 'paintbrush_blur',
						'buttonTitle' => __('Paintbrush Blur', 'revslider-paintbrush-addon'), 
						'title' => __('Enable Blur', 'revslider-paintbrush-addon'), 
						'helpPath' => 'addOns.revslider-paintbrush-addon.image.blur.enable', 
						'keywords' => array('addon', 'addons', 'paintbrush', 'paintbrush addon', 'paintbrush image', 'blur', 'paintbrush blur'), 
						'description' => __('Apply a blur filter to the Paintbrush image', 'revslider-paintbrush-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/paintbrush-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Paintbrush',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.enable', 'value' => true, 'option' => 'paintbrush_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-paintbrush-addon", 
							'scrollTo' => '#form_slidegeneral_revslider-paintbrush-addon', 
							'focus' => "#paintbrush_blur_enable"
							
						)
						
					),
					
					'blur_amount' => array(
						
						'buttonTitle' => __('Paintbrush Blur Amount', 'revslider-paintbrush-addon'), 
						'title' => __('Blur Amount', 'revslider-paintbrush-addon'), 
						'helpPath' => 'addOns.revslider-paintbrush-addon.image.blur.amount', 
						'keywords' => array('addon', 'addons', 'paintbrush', 'paintbrush addon', 'paintbrush image', 'blur', 'paintbrush blur'), 
						'description' => __('The blur amount in pixels to apply to the Paintbrush image', 'revslider-paintbrush-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/paintbrush-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Paintbrush',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.enable', 'value' => true, 'option' => 'paintbrush_enable'),
								array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.image.blur.enable', 'value' => true, 'option' => 'paintbrush_blur')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-paintbrush-addon", 
							'scrollTo' => '#form_slidegeneral_revslider-paintbrush-addon', 
							'focus' => "*[data-r='addOns.revslider-paintbrush-addon.image.blur.amount']"
							
						)
						
					),
					
					'responsive' => array(
						
						'title' => __('Responsive Blur', 'revslider-paintbrush-addon'), 
						'helpPath' => 'addOns.revslider-paintbrush-addon.image.blur.responsive', 
						'keywords' => array('addon', 'addons', 'paintbrush', 'paintbrush addon', 'paintbrush image', 'blur', 'paintbrush blur'), 
						'description' => __("Choose if the blur amount should adjust responsively based on the Slider's resizing behavior", 'revslider-paintbrush-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/paintbrush-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Paintbrush',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.enable', 'value' => true, 'option' => 'paintbrush_enable'),
								array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.image.blur.enable', 'value' => true, 'option' => 'paintbrush_blur')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-paintbrush-addon", 
							'scrollTo' => '#form_slidegeneral_revslider-paintbrush-addon', 
							'focus' => "#paintbrush_responsive_blur"
							
						)
						
					),
					
					'soft_edges' => array(
						
						'dependency_id' => 'paintbrush_soft_edges',
						'title' => __('Fix Soft Edges', 'revslider-paintbrush-addon'), 
						'helpPath' => 'addOns.revslider-paintbrush-addon.image.blur.fixedges.enable', 
						'keywords' => array('addon', 'addons', 'paintbrush', 'paintbrush addon', 'paintbrush image', 'blur', 'paintbrush blur'), 
						'description' => __("Scale the image up slightly to create a gaussian blur", 'revslider-paintbrush-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/paintbrush-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Paintbrush',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.enable', 'value' => true, 'option' => 'paintbrush_enable'),
								array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.image.blur.enable', 'value' => true, 'option' => 'paintbrush_blur')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-paintbrush-addon", 
							'scrollTo' => '#form_slidegeneral_revslider-paintbrush-addon', 
							'focus' => "#paintbrush_fixedges"
							
						)
						
					),
					
					'stretch_by' => array(
						
						'title' => __('Stretch Image By', 'revslider-paintbrush-addon'), 
						'helpPath' => 'addOns.revslider-paintbrush-addon.image.blur.fixedges.amount', 
						'keywords' => array('addon', 'addons', 'paintbrush', 'paintbrush addon', 'paintbrush image', 'blur', 'paintbrush blur'), 
						'description' => __("Stretch the image by this percentage to create the gaussian blur", 'revslider-paintbrush-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/paintbrush-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Paintbrush',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.enable', 'value' => true, 'option' => 'paintbrush_enable'),
								array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.image.blur.enable', 'value' => true, 'option' => 'paintbrush_blur'),
								array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.image.blur.fixedges.enable', 'value' => true, 'option' => 'paintbrush_soft_edges')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-paintbrush-addon", 
							'scrollTo' => '#form_slidegeneral_revslider-paintbrush-addon', 
							'focus' => "*[data-r='addOns.revslider-paintbrush-addon.image.blur.fixedges.amount']"
							
						)
						
					)
					
				),
				
				'brush_settings' => array(
				
					'style' => array(
						
						'title' => __('Paintbrush Style', 'revslider-paintbrush-addon'), 
						'helpPath' => 'addOns.revslider-paintbrush-addon.brush.style', 
						'keywords' => array('addon', 'addons', 'paintbrush', 'paintbrush addon', 'paintbrush style', 'brush style'), 
						'description' => __('The "edge" style for the brush effect', 'revslider-paintbrush-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/paintbrush-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Paintbrush',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.enable', 'value' => true, 'option' => 'paintbrush_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-paintbrush-addon", 
							'scrollTo' => '#paintbrush_brush_settings', 
							'focus' => "#paintbrush_brush_style"
							
						)
						
					),
					
					'size' => array(
						
						'title' => __('Paintbrush Size', 'revslider-paintbrush-addon'), 
						'helpPath' => 'addOns.revslider-paintbrush-addon.brush.size', 
						'keywords' => array('addon', 'addons', 'paintbrush', 'paintbrush addon', 'paintbrush size', 'brush size'), 
						'description' => __('The size of the brush effect in pixels', 'revslider-paintbrush-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/paintbrush-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Paintbrush',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.enable', 'value' => true, 'option' => 'paintbrush_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-paintbrush-addon", 
							'scrollTo' => '#paintbrush_brush_settings', 
							'focus' => "*[data-r='addOns.revslider-paintbrush-addon.brush.size']"
							
						)
						
					),

					'strength' => array(
						
						'title' => __('Paintbrush Strength', 'revslider-paintbrush-addon'), 
						'helpPath' => 'addOns.revslider-paintbrush-addon.brush.strength', 
						'keywords' => array('addon', 'addons', 'paintbrush', 'paintbrush addon', 'paintbrush strength', 'brush strength'), 
						'description' => __('The Strength of the brush effect in pixels', 'revslider-paintbrush-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/paintbrush-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Paintbrush',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.enable', 'value' => true, 'option' => 'paintbrush_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-paintbrush-addon", 
							'scrollTo' => '#paintbrush_brush_settings', 
							'focus' => "*[data-r='addOns.revslider-paintbrush-addon.brush.strength']"
							
						)
						
					),
					
					'responsive' => array(
						
						'title' => __('Responsive Size', 'revslider-paintbrush-addon'), 
						'helpPath' => 'addOns.revslider-paintbrush-addon.brush.responsive', 
						'keywords' => array('addon', 'addons', 'paintbrush', 'paintbrush addon', 'paintbrush responsive', 'responsive'), 
						'description' => __("Choose if the brush size should adjust responsively based on the Slider's resizing behavior", 'revslider-paintbrush-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/paintbrush-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Paintbrush',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.enable', 'value' => true, 'option' => 'paintbrush_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-paintbrush-addon", 
							'scrollTo' => '#paintbrush_brush_settings', 
							'focus' => "#paintbrush_responsivesize"
							
						)
						
					),
					
					'disappear' => array(
						
						'dependency_id' => 'paintbrush_disappear',
						'title' => __('Brush Disappear', 'revslider-paintbrush-addon'), 
						'helpPath' => 'addOns.revslider-paintbrush-addon.brush.disappear.enable', 
						'keywords' => array('addon', 'addons', 'paintbrush', 'paintbrush addon', 'paintbrush disappear', 'disappear'), 
						'description' => __("Choose if the brush stroke should disappear after it's painted", 'revslider-paintbrush-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/paintbrush-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Paintbrush',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.enable', 'value' => true, 'option' => 'paintbrush_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-paintbrush-addon", 
							'scrollTo' => '#paintbrush_brush_settings', 
							'focus' => "#paintbrush_disappear"
							
						)
						
					),
					
					'fade_time' => array(
						
						'title' => __('Disappear Time', 'revslider-paintbrush-addon'), 
						'helpPath' => 'addOns.revslider-paintbrush-addon.brush.disappear.time', 
						'keywords' => array('addon', 'addons', 'paintbrush', 'paintbrush addon', 'paintbrush disappear', 'disappear', 'time'), 
						'description' => __("The amount of time before the brush stroke disappears in milliseconds", 'revslider-paintbrush-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/paintbrush-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Paintbrush',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.enable', 'value' => true, 'option' => 'paintbrush_enable'),
								array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.brush.disappear.enable', 'value' => true, 'option' => 'paintbrush_disappear')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-paintbrush-addon", 
							'scrollTo' => '#paintbrush_brush_settings', 
							'focus' => "*[data-r='addOns.revslider-paintbrush-addon.brush.disappear.time']"
							
						)
						
					)
				
				),
				
				'mobile_settings' => array(
				
					'disable' => array(
						
						'buttonTitle' => __('Paintbrush Mobile', 'revslider-paintbrush-addon'), 
						'title' => __('Disable on Mobile', 'revslider-paintbrush-addon'), 
						'helpPath' => 'addOns.revslider-paintbrush-addon.mobile.disable', 
						'keywords' => array('addon', 'addons', 'paintbrush', 'paintbrush addon', 'paintbrush mobile', 'mobile', 'disable'), 
						'description' => __("Choose to disable the paintbrush effect on mobile devices", 'revslider-paintbrush-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/paintbrush-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Paintbrush',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.enable', 'value' => true, 'option' => 'paintbrush_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-paintbrush-addon", 
							'scrollTo' => '#paintbrush_mobile_settings', 
							'focus' => "#paintbrush_mobiledisable"
							
						)
						
					),
					
					'fallback' => array(
						
						'title' => __('Mobile Fallback', 'revslider-paintbrush-addon'), 
						'helpPath' => 'addOns.revslider-paintbrush-addon.mobile.fallback', 
						'keywords' => array('addon', 'addons', 'paintbrush', 'paintbrush addon', 'paintbrush mobile', 'mobile', 'fallback', 'mobile fallback'), 
						'description' => __("Use the designated paintbrush image as the slide's main background on mobile", 'revslider-paintbrush-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/paintbrush-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Paintbrush',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-paintbrush-addon.enable', 'value' => true, 'option' => 'paintbrush_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-paintbrush-addon", 
							'scrollTo' => '#paintbrush_mobile_settings', 
							'focus' => "#paintbrush_mobilefallback"
							
						)
						
					)
				
				)
				
			)
			
		);
		
	}
	
}
	
?>