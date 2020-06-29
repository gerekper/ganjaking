<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnSliceyBase {
	
	const MINIMUM_VERSION = '6.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnSliceyBase::MINIMUM_VERSION, '>=')) {
		
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
			$update_admin = new RevAddOnSliceyUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'));
			add_filter('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

			// require_once(static::$_PluginPath . 'admin/includes/slider.class.php');			
			
			// admin init
			// new RsSliceySliderAdmin(static::$_PluginTitle, static::$_Version);			
			
		}

		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		require_once(static::$_PluginPath . 'public/includes/slide.class.php');
		
		new RsSliceySliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsSliceySlideFront(static::$_PluginTitle);
		
	}
	
	/**
	 * Load the textdomain
	 **/
	protected function _loadPluginTextDomain(){
		
		load_plugin_textdomain('rs_' . static::$_PluginTitle, false, static::$_PluginPath . 'languages/');
		
	}
	
	// AddOn's page slideout panel
	public function addons_page_content() {
		
		include_once(static::$_PluginPath . 'admin/views/admin-display.php');
		
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
			wp_localize_script( $_handle, 'revslider_slicey_addon', self::get_var() );
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
				'slicey' => __('Slicey','revslider-slicey-addon'),
				'sliceylayersettings' => __('Slicey Layer Settings', 'revslider-slicey-addon'),
				'scaleoffset' => __('Scale Offset', 'revslider-slicey-addon'),
				'sliceylayer' => __('Slicey Layer', 'revslider-slicey-addon'),
				'sliceyupdatepz' => __('Slicey Update on Pan Zoom', 'revslider-slicey-addon'),
				'shadowsettings' => __('Shadow Settings', 'revslider-slicey-addon'),
				'shadowcolor' => __('Shadow Color', 'revslider-slicey-addon'),
				'sliceyshadow' => __('Slicey Shadow', 'revslider-slicey-addon')									
			)
		);
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-slicey-addon') {
		
		if($slug === 'revslider-slicey-addon'){
			
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
			$definitions['editor_settings']['slide_settings']['addons']['slicey_addon'] = $help['slide'];
		}
		
		if(isset($definitions['editor_settings']['layer_settings']) && isset($definitions['editor_settings']['layer_settings']['addons'])) {
			$definitions['editor_settings']['layer_settings']['addons']['slicey_addon'] = $help['layer'];
		}
		
		return $definitions;
	
	}
	
	/**
	 * Returns the addon help definitions
	 *
	 * @since    2.0.0
	 */
	private static function get_definitions() {
		
		$_textdomain = 'revslider-slicey-addon';
		return array(
		
			'slide' => array(
				
				'pan_zoom' => array(
				
					'scale_end' => array(
							
						'buttonTitle' => __('Slicey Scale To', $_textdomain), 
						'title' => __('Scale To', $_textdomain),
						'helpPath' => 'slicey.panzoom.fitEnd', 
						'keywords' => array('addon', 'addons', 'slicey', 'slicey addon', 'slicey zoom', 'slicey scale', 'zoom', 'scale'), 
						'description' => __('The ending scale percentage for the Slides <a href="http://docs.themepunch.com/slider-revolution/slide-background/#image" target="_blank">main background image</a>.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/slicey-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Slicey',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.bg.type', 'value' => 'image::external', 'option' => 'slide_bg_type')),
							'menu' => "#module_slide_trigger, #gst_slide_3", 
							'scrollTo' => '#form_slidebg_kenburn', 
							'focus' => "#sl_pz_fe"
							
						)
						
					),
					
					'blur_start' => array(
							
						'buttonTitle' => __('Slicey Blur From', $_textdomain), 
						'title' => __('Blur From', $_textdomain),
						'helpPath' => 'slicey.panzoom.blurStart', 
						'keywords' => array('addon', 'addons', 'slicey', 'slicey addon', 'slicey zoom', 'slicey blur', 'blur'), 
						'description' => __('The starting blur value for the Slides <a href="http://docs.themepunch.com/slider-revolution/slide-background/#image" target="_blank">main background image</a>.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/slicey-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Slicey',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.bg.type', 'value' => 'image::external', 'option' => 'slide_bg_type')),
							'menu' => "#module_slide_trigger, #gst_slide_3", 
							'scrollTo' => '#form_slidebg_kenburn', 
							'focus' => "#sl_pz_blurs"
							
						)
						
					),
					
					'blur_end' => array(
							
						'buttonTitle' => __('Slicey Blur To', $_textdomain), 
						'title' => __('Blur To', $_textdomain),
						'helpPath' => 'slicey.panzoom.blurEnd', 
						'keywords' => array('addon', 'addons', 'slicey', 'slicey addon', 'slicey zoom', 'slicey blur', 'blur'), 
						'description' => __('The ending blur value for the Slides <a href="http://docs.themepunch.com/slider-revolution/slide-background/#image" target="_blank">main background image</a>.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/slicey-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Slicey',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.bg.type', 'value' => 'image::external', 'option' => 'slide_bg_type')),
							'menu' => "#module_slide_trigger, #gst_slide_3", 
							'scrollTo' => '#form_slidebg_kenburn', 
							'focus' => "#sl_pz_blure"
							
						)
						
					),
					
					'easing' => array(
							
						'buttonTitle' => __('Slicey Easing', $_textdomain), 
						'title' => __('Animation Easing', $_textdomain),
						'helpPath' => 'slicey.panzoom.ease', 
						'keywords' => array('addon', 'addons', 'slicey', 'slicey addon', 'slicey zoom', 'slicey easing', 'easing', 'animation', 'animation easing'), 
						'description' => __('The <a href="https://greensock.com/ease-visualizer" target="_blank">easing equation</a> to be used for the animation.  "Linear.easeNone" is recommended.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/slicey-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Slicey',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.bg.type', 'value' => 'image::external', 'option' => 'slide_bg_type')),
							'menu' => "#module_slide_trigger, #gst_slide_3", 
							'scrollTo' => '#form_slidebg_kenburn', 
							'focus' => "#sl_pz_ease"
							
						)
						
					),
					
					'duration' => array(
							
						'buttonTitle' => __('Slicey Duration', $_textdomain), 
						'title' => __('Animation Duration', $_textdomain),
						'helpPath' => 'slicey.panzoom.duration', 
						'keywords' => array('addon', 'addons', 'slicey', 'slicey addon', 'slicey zoom', 'slicey duration', 'duration', 'time', 'animation duration', 'animation time'), 
						'description' => __('The total time the animation will take place in milliseconds.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/slicey-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Slicey',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.bg.type', 'value' => 'image::external', 'option' => 'slide_bg_type')),
							'menu' => "#module_slide_trigger, #gst_slide_3", 
							'scrollTo' => '#form_slidebg_kenburn', 
							'focus' => "#sl_pz_dur"
							
						)
						
					),
					
				),
				
				'shadow_settings' => array(
				
					'color' => array(
							
						'buttonTitle' => __('Slicey Shadow Color', $_textdomain), 
						'title' => __('Shadow Color', $_textdomain),
						'helpPath' => 'slicey.addOns.revslider-slicey-addon.shadow.color', 
						'keywords' => array('addon', 'addons', 'slicey', 'slicey addon', 'slicey shadow', 'shadow', 'box-shadow', 'box shadow'), 
						'description' => __('Add shadows to the <a href="http://docs.themepunch.com/slider-revolution/slicey-addon/#add-slicey-layers" target="_blank">Slicey Layers</a> to enhance their 3D visual.  The shadow color can be set here.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/slicey-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Slicey',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.bg.type', 'value' => 'image::external', 'option' => 'slide_bg_type')),
							'menu' => "#module_slide_trigger, #gst_slide_3", 
							'scrollTo' => '#slicey_panzoom_extension', 
							'focus' => "#sliceycolor"
							
						)
						
					),
					
					'blur' => array(
							
						'buttonTitle' => __('Slicey Shadow Blur', $_textdomain), 
						'title' => __('Shadow Blur', $_textdomain),
						'helpPath' => 'slicey.addOns.revslider-slicey-addon.shadow.blur', 
						'keywords' => array('addon', 'addons', 'slicey', 'slicey addon', 'slicey shadow', 'shadow', 'box-shadow', 'box shadow', 'blur', 'shadow blur'), 
						'description' => __('The blur value for the <a href="https://www.w3schools.com/csSref/css3_pr_box-shadow.asp" target="_blank">CSS box-shadow</a> applied to the <a href="http://docs.themepunch.com/slider-revolution/slicey-addon/#add-slicey-layers" target="_blank">Slicey Layers</a>.  Enter "0" for no shadow.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/slicey-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Slicey',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.bg.type', 'value' => 'image::external', 'option' => 'slide_bg_type')),
							'menu' => "#module_slide_trigger, #gst_slide_3", 
							'scrollTo' => '#slicey_panzoom_extension', 
							'focus' => "*[data-r='addOns.revslider-slicey-addon.shadow.blur']"
							
						)
						
					),
					
					'strength' => array(
							
						'buttonTitle' => __('Slicey Shadow Strength', $_textdomain), 
						'title' => __('Shadow Strength', $_textdomain),
						'helpPath' => 'slicey.addOns.revslider-slicey-addon.shadow.strength', 
						'keywords' => array('addon', 'addons', 'slicey', 'slicey addon', 'slicey shadow', 'shadow', 'box-shadow', 'box shadow', 'strength', 'shadow strength'), 
						'description' => __('The strength value for the <a href="https://www.w3schools.com/csSref/css3_pr_box-shadow.asp" target="_blank">CSS box-shadow</a> applied to the <a href="http://docs.themepunch.com/slider-revolution/slicey-addon/#add-slicey-layers" target="_blank">Slicey Layers</a>.  Enter "0" for no shadow.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/slicey-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Slicey',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.bg.type', 'value' => 'image::external', 'option' => 'slide_bg_type')),
							'menu' => "#module_slide_trigger, #gst_slide_3", 
							'scrollTo' => '#slicey_panzoom_extension', 
							'focus' => "*[data-r='addOns.revslider-slicey-addon.shadow.strength']"
							
						)
						
					)
					
				)
				
			),
		
			'layer' => array(
					
				'scale_offset' => array(
						
					'buttonTitle' => __('Slicey Scale Offset', $_textdomain), 
					'title' => __('Scale Offset', $_textdomain),
					'helpPath' => 'addOns.revslider-slicey-addon.scaleOffset', 
					'keywords' => array('addon', 'addons', 'slicey', 'slicey addon', 'slicey layer', 'scale', 'scale offset'), 
					'description' => __('The <a href="http://docs.themepunch.com/slider-revolution/slicey-addon/#add-slicey-layers" target="_blank">Slicey Layer</a> will be scaled up this percentage from the Slicey Pan/Zoom movement.  This offset is what creates the effect visually.', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/slicey-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Slicey',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{slicey}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-slicey-addon", 
						'scrollTo' => '#form_layerinner_revslider-slicey-addon', 
						'focus' => "*[data-r='addOns.revslider-slicey-addon.scaleOffset']"
						
					)
					
				),
				
				'blur_start' => array(
						
					'buttonTitle' => __('Slicey Blur From', $_textdomain), 
					'title' => __('Blur From', $_textdomain),
					'helpPath' => 'addOns.revslider-slicey-addon.blurStart', 
					'keywords' => array('addon', 'addons', 'slicey', 'slicey addon', 'slicey shadow', 'shadow', 'box-shadow', 'box shadow', 'blur', 'shadow blur'), 
					'description' => __('Optional custom starting blur value.  "inherit" will simply use the values set in the Slicey Pan/Zoom settings instead.', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/slicey-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Slicey',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{slicey}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-slicey-addon", 
						'scrollTo' => '#form_layerinner_revslider-slicey-addon', 
						'focus' => "#slicey_layer_blur_start"
						
					)
					
				),
				
				'blur_end' => array(
						
					'buttonTitle' => __('Slicey Blur To', $_textdomain), 
					'title' => __('Blur To', $_textdomain),
					'helpPath' => 'addOns.revslider-slicey-addon.blurEnd', 
					'keywords' => array('addon', 'addons', 'slicey', 'slicey addon', 'slicey shadow', 'shadow', 'box-shadow', 'box shadow', 'blur', 'shadow blur'), 
					'description' => __('Optional custom ending blur value.  "inherit" will simply use the values set in the Slicey Pan/Zoom settings instead.', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/slicey-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Slicey',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{slicey}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-slicey-addon", 
						'scrollTo' => '#form_layerinner_revslider-slicey-addon', 
						'focus' => "#slicey_layer_blur_end"
						
					)
					
				)
				
			)
			
		);
		
	}
	
}
	
?>