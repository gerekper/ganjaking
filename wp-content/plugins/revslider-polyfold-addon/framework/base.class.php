<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnPolyfoldBase {
	
	const MINIMUM_VERSION = '6.5.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnPolyfoldBase::MINIMUM_VERSION, '>=')) {
		
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
			$update_admin = new RevAddOnPolyfoldUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'));
			add_filter('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
		}
		
		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		
		new RsPolyfoldSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		
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
			
			wp_enqueue_style($_handle, $_base . 'css/revslider-' . static::$_PluginTitle . '-addon-admin.css', array(), static::$_Version);
			wp_enqueue_script($_handle, $_base . 'js/revslider-' . static::$_PluginTitle . '-addon-admin' . $_jsPathMin . '.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			wp_localize_script( $_handle, 'revslider_polyfold_addon', self::get_var() );
		}
		
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-polyfold-addon') {
		
		if($slug === 'revslider-polyfold-addon'){
			
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
			$definitions['editor_settings']['slider_settings']['addons']['polyfold_addon'] = $help['slider'];
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
					'polyfold' => __('Polyfold','revslider-polyfold-addon'),
					'topedge' => __('Top Edge','revslider-polyfold-addon'),
					'bottomedge' => __('Bottom Edge','revslider-polyfold-addon'),
					'enabletopedge' => __('Enable Top Edge','revslider-polyfold-addon'),
					'enablebottomedge' => __('Enable Bottom Edge','revslider-polyfold-addon'),
					'bgcolor' => __('BG Color','revslider-polyfold-addon'),
					'drawonscroll' => __('Draw on Scroll','revslider-polyfold-addon'),
					'drange' => __('Range','revslider-polyfold-addon'),
					'sliderheight' => __('Slider Height','revslider-polyfold-addon'),
					'windowheight' => __('Window Height','revslider-polyfold-addon'),
					'usetrans' => __('Transition','revslider-polyfold-addon'),
					'easing' => __('Easing','revslider-polyfold-addon'),
					'linear' => __('Linear','revslider-polyfold-addon'),
					'invert' => __('Invert Scroll','revslider-polyfold-addon'),
					'lewidth' => __('Left','revslider-polyfold-addon'),
					'riwidth' => __('Right','revslider-polyfold-addon'),
					'defheight' => __('Default Height','revslider-polyfold-addon'),
					'invertang' => __('Inv. Angles','revslider-polyfold-addon'),
					'responsive' => __('Responsive','revslider-polyfold-addon'),
					'scenter' => __('Slider Center','revslider-polyfold-addon'),
					'sside' => __('Slider Sides','revslider-polyfold-addon'),
					'drawtheedge' => __('Draw Edges','revslider-polyfold-addon'),
					'dte_1' => __('Above Entire Slider','revslider-polyfold-addon'),
					'dte_2' => __('Behind Navigation','revslider-polyfold-addon'),
					'dte_3' => __('Behind Static Layers','revslider-polyfold-addon'),
					'hideonmobile' => __('Hide on Mobile','revslider-polyfold-addon'),
					'topedgebgcolor' => __('Top Edge Color','revslider-polyfold-addon'),
					'bottomedgebgcolor' => __('Bottom Edge Color','revslider-polyfold-addon'),
					'active' => __('Active','revslider-polyfold-addon'),
					'drawfrom' => __('Draw from','revslider-polyfold-addon'),
					'time' => __('Time','revslider-polyfold-addon'),
					'leftright' => __('Left/Right','revslider-polyfold-addon'),
			),
			'placeholder_select' => __('Select From List','revslider-polyfold-addon')
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
				
				'enable' => array(
					
					'dependency_id' => 'polyfold_enable',
					'title' => __('Enable Polyfold Edge', 'revslider-polyfold-addon'),
					'helpPath' => 'addOns.revslider-polyfold-addon.top.enabled, addOns.revslider-polyfold-addon.bottom.enabled', 
					'keywords' => array('addon', 'addons', 'polyfold', 'polyfold addon', 'polyfold edge', 'polyfold top edge', 'polyfold bottom edge'), 
					'description' => __("Enable a top or bottom edge for the Polyfold AddOn", 'revslider-polyfold-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/polyfold-scroll-addon/', 
					'video' => false,
					'section' => 'Slider Settings -> Polyfold',
					'highlight' => array(
						
						'menu' => "#module_settings_trigger, #gst_sl_revslider-polyfold-addon", 
						'scrollTo' => '#form_slidergeneral_revslider-polyfold-addon', 
						'focus' => "*[data-r='addOns.revslider-polyfold-addon.top.enabled']"
						
					)
					
				),
				
				'mobile' => array(
					
					'buttonTitle' => __('Polyfold Mobile', 'revslider-polyfold-addon'),
					'title' => __('Disable on Mobile', 'revslider-polyfold-addon'),
					'helpPath' => 'addOns.revslider-polyfold-addon.top.hideOnMobile, addOns.revslider-polyfold-addon.bottom.hideOnMobile', 
					'keywords' => array('addon', 'addons', 'polyfold', 'polyfold addon', 'polyfold edge', 'polyfold top edge', 'polyfold bottom edge', 'polyfold mobile', 'mobile'), 
					'description' => __("Disable the polyfold edge on mobile devices", 'revslider-polyfold-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/polyfold-scroll-addon/', 
					'video' => false,
					'section' => 'Slider Settings -> Polyfold',
					'highlight' => array(
						
						'dependencies' => array(array('path' => 'settings.addOns.revslider-polyfold-addon.top.enabled', 'value' => true, 'option' => 'polyfold_enable')),
						'menu' => "#module_settings_trigger, #gst_sl_revslider-polyfold-addon", 
						'scrollTo' => '#form_slidergeneral_revslider-polyfold-addon', 
						'focus' => "*[data-r='addOns.revslider-polyfold-addon.top.hideOnMobile']"
						
					)
					
				),
				
				'color' => array(
					
					'title' => __('Polyfold Color', 'revslider-polyfold-addon'),
					'helpPath' => 'addOns.revslider-polyfold-addon.top.color, addOns.revslider-polyfold-addon.bottom.color', 
					'keywords' => array('addon', 'addons', 'polyfold', 'polyfold addon', 'polyfold color'), 
					'description' => __("The color for the polyfold effect.  In general this should match the page content's background color", 'revslider-polyfold-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/polyfold-scroll-addon/', 
					'video' => false,
					'section' => 'Slider Settings -> Polyfold',
					'highlight' => array(
						
						'dependencies' => array(array('path' => 'settings.addOns.revslider-polyfold-addon.top.enabled', 'value' => true, 'option' => 'polyfold_enable')),
						'menu' => "#module_settings_trigger, #gst_sl_revslider-polyfold-addon", 
						'scrollTo' => '#form_slidergeneral_revslider-polyfold-addon', 
						'focus' => "#polyfoldtopcolor"
						
					)
					
				),
				
				'draw_from' => array(
					
					'title' => __('Draw From', 'revslider-polyfold-addon'),
					'helpPath' => 'addOns.revslider-polyfold-addon.top.point, addOns.revslider-polyfold-addon.bottom.point', 
					'keywords' => array('addon', 'addons', 'polyfold', 'polyfold addon', 'polyfold draw'), 
					'description' => __("Choose if the drawing should start from the Slider's sides or center", 'revslider-polyfold-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/polyfold-scroll-addon/', 
					'video' => false,
					'section' => 'Slider Settings -> Polyfold',
					'highlight' => array(
						
						'dependencies' => array(array('path' => 'settings.addOns.revslider-polyfold-addon.top.enabled', 'value' => true, 'option' => 'polyfold_enable')),
						'menu' => "#module_settings_trigger, #gst_sl_revslider-polyfold-addon", 
						'scrollTo' => '#form_slidergeneral_revslider-polyfold-addon', 
						'focus' => "*[data-r='addOns.revslider-polyfold-addon.top.point']"
						
					)
					
				),
				
				'draw_edges' => array(
					
					'title' => __('Draw Edges', 'revslider-polyfold-addon'),
					'helpPath' => 'addOns.revslider-polyfold-addon.top.placement, addOns.revslider-polyfold-addon.bottom.placement', 
					'keywords' => array('addon', 'addons', 'polyfold', 'polyfold addon', 'polyfold draw'), 
					'description' => __("Choose if the shape should be drawn on top of or behind navigation and static layers", 'revslider-polyfold-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/polyfold-scroll-addon/', 
					'video' => false,
					'section' => 'Slider Settings -> Polyfold',
					'highlight' => array(
						
						'dependencies' => array(array('path' => 'settings.addOns.revslider-polyfold-addon.top.enabled', 'value' => true, 'option' => 'polyfold_enable')),
						'menu' => "#module_settings_trigger, #gst_sl_revslider-polyfold-addon", 
						'scrollTo' => '#form_slidergeneral_revslider-polyfold-addon', 
						'focus' => "*[data-r='addOns.revslider-polyfold-addon.top.placement']"
						
					)
					
				),
				
				'responsive' => array(
					
					'title' => __('Polyfold Responsive', 'revslider-polyfold-addon'),
					'helpPath' => 'addOns.revslider-polyfold-addon.top.responsive, addOns.revslider-polyfold-addon.bottom.responsive', 
					'keywords' => array('addon', 'addons', 'polyfold', 'polyfold addon', 'polyfold draw'), 
					'description' => __("Choose if the shape's height should adjust based on the Slider's responsive behavior", 'revslider-polyfold-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/polyfold-scroll-addon/', 
					'video' => false,
					'section' => 'Slider Settings -> Polyfold',
					'highlight' => array(
						
						'dependencies' => array(array('path' => 'settings.addOns.revslider-polyfold-addon.top.enabled', 'value' => true, 'option' => 'polyfold_enable')),
						'menu' => "#module_settings_trigger, #gst_sl_revslider-polyfold-addon", 
						'scrollTo' => '#form_slidergeneral_revslider-polyfold-addon', 
						'focus' => "*[data-r='addOns.revslider-polyfold-addon.top.responsive']"
						
					)
					
				),
				
				'inverse' => array(
					
					'title' => __('Invert Edges', 'revslider-polyfold-addon'),
					'helpPath' => 'addOns.revslider-polyfold-addon.top.negative, addOns.revslider-polyfold-addon.bottom.negative', 
					'keywords' => array('addon', 'addons', 'polyfold', 'polyfold addon', 'polyfold inverse'), 
					'description' => __("Invert the drawing of the Polyfold shape", 'revslider-polyfold-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/polyfold-scroll-addon/', 
					'video' => false,
					'section' => 'Slider Settings -> Polyfold',
					'highlight' => array(
						
						'dependencies' => array(array('path' => 'settings.addOns.revslider-polyfold-addon.top.enabled', 'value' => true, 'option' => 'polyfold_enable')),
						'menu' => "#module_settings_trigger, #gst_sl_revslider-polyfold-addon", 
						'scrollTo' => '#form_slidergeneral_revslider-polyfold-addon', 
						'focus' => "*[data-r='addOns.revslider-polyfold-addon.top.negative']"
						
					)
					
				),
				
				'size' => array(
				
					'left_width' => array(
						
						'buttonTitle' => __('Polyfold Left Width', 'revslider-polyfold-addon'),
						'title' => __('Left Width', 'revslider-polyfold-addon'),
						'helpPath' => 'addOns.revslider-polyfold-addon.top.leftWidth, addOns.revslider-polyfold-addon.bottom.leftWidth', 
						'keywords' => array('addon', 'addons', 'polyfold', 'polyfold addon', 'polyfold width'), 
						'description' => __("The width for the left half of the drawn shape", 'revslider-polyfold-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/polyfold-scroll-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Polyfold',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-polyfold-addon.top.enabled', 'value' => true, 'option' => 'polyfold_enable')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-polyfold-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-polyfold-addon', 
							'focus' => "*[data-r='addOns.revslider-polyfold-addon.top.leftWidth']"
							
						)
						
					),
					
					'right_width' => array(
						
						'buttonTitle' => __('Polyfold Right Width', 'revslider-polyfold-addon'),
						'title' => __('Right Width', 'revslider-polyfold-addon'),
						'helpPath' => 'addOns.revslider-polyfold-addon.top.rightWidth, addOns.revslider-polyfold-addon.bottom.rightWidth', 
						'keywords' => array('addon', 'addons', 'polyfold', 'polyfold addon', 'polyfold width'), 
						'description' => __("The width for the right half of the drawn shape", 'revslider-polyfold-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/polyfold-scroll-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Polyfold',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-polyfold-addon.top.enabled', 'value' => true, 'option' => 'polyfold_enable')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-polyfold-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-polyfold-addon', 
							'focus' => "*[data-r='addOns.revslider-polyfold-addon.top.rightWidth']"
							
						)
						
					),
					
					'height' => array(
						
						'title' => __('Polyfold Height', 'revslider-polyfold-addon'),
						'helpPath' => 'addOns.revslider-polyfold-addon.top.height, addOns.revslider-polyfold-addon.bottom.height', 
						'keywords' => array('addon', 'addons', 'polyfold', 'polyfold addon', 'polyfold height'), 
						'description' => __("The maximum height for the shape once its fully drawn", 'revslider-polyfold-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/polyfold-scroll-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Polyfold',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-polyfold-addon.top.enabled', 'value' => true, 'option' => 'polyfold_enable')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-polyfold-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-polyfold-addon', 
							'focus' => "*[data-r='addOns.revslider-polyfold-addon.top.height']"
							
						)
						
					)
					
				),
				
				'scroll' => array(
				
					'draw' => array(
						
						'dependency_id' => 'polyfold_scroll',
						'title' => __('Draw on Scroll', 'revslider-polyfold-addon'),
						'helpPath' => 'addOns.revslider-polyfold-addon.top.scroll, addOns.revslider-polyfold-addon.bottom.scroll', 
						'keywords' => array('addon', 'addons', 'polyfold', 'polyfold addon', 'polyfold scroll', 'scroll'), 
						'description' => __("Draw the polyfold shape when the page is scrolled", 'revslider-polyfold-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/polyfold-scroll-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Polyfold',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-polyfold-addon.top.enabled', 'value' => true, 'option' => 'polyfold_enable')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-polyfold-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-polyfold-addon', 
							'focus' => "*[data-r='addOns.revslider-polyfold-addon.top.scroll']"
							
						)
						
					),
					
					'range' => array(
						
						'title' => __('Drawing Range', 'revslider-polyfold-addon'),
						'helpPath' => 'addOns.revslider-polyfold-addon.top.range, addOns.revslider-polyfold-addon.bottom.range', 
						'keywords' => array('addon', 'addons', 'polyfold', 'polyfold addon', 'polyfold scroll', 'scroll'), 
						'description' => __("Draw the shape based on the Slider's height or the browser window's height", 'revslider-polyfold-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/polyfold-scroll-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Polyfold',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => 'settings.addOns.revslider-polyfold-addon.top.enabled', 'value' => true, 'option' => 'polyfold_enable'),
								array('path' => 'settings.addOns.revslider-polyfold-addon.top.scroll', 'value' => true, 'option' => 'polyfold_scroll')
								
							),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-polyfold-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-polyfold-addon', 
							'focus' => "*[data-r='addOns.revslider-polyfold-addon.top.range']"
							
						)
						
					),
					
					'transition' => array(
						
						'dependency_id' => 'polyfold_transition',
						'title' => __('Drawing Transition', 'revslider-polyfold-addon'),
						'helpPath' => 'addOns.revslider-polyfold-addon.top.animated, addOns.revslider-polyfold-addon.bottom.animated', 
						'keywords' => array('addon', 'addons', 'polyfold', 'polyfold addon', 'polyfold transition', 'transition'), 
						'description' => __("Animate the shape with a CSS transition as the page is scrolled", 'revslider-polyfold-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/polyfold-scroll-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Polyfold',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => 'settings.addOns.revslider-polyfold-addon.top.enabled', 'value' => true, 'option' => 'polyfold_enable'),
								array('path' => 'settings.addOns.revslider-polyfold-addon.top.scroll', 'value' => true, 'option' => 'polyfold_scroll')
								
							),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-polyfold-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-polyfold-addon', 
							'focus' => "*[data-r='addOns.revslider-polyfold-addon.top.animated']"
							
						)
						
					),
					
					'easing' => array(
						
						'title' => __('Transition Easing', 'revslider-polyfold-addon'),
						'helpPath' => 'addOns.revslider-polyfold-addon.top.ease, addOns.revslider-polyfold-addon.bottom.ease', 
						'keywords' => array('addon', 'addons', 'polyfold', 'polyfold addon', 'polyfold transition', 'transition'), 
						'description' => __("The easing equation to use for the CSS transition as the page is scrolled", 'revslider-polyfold-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/polyfold-scroll-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Polyfold',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => 'settings.addOns.revslider-polyfold-addon.top.enabled', 'value' => true, 'option' => 'polyfold_enable'),
								array('path' => 'settings.addOns.revslider-polyfold-addon.top.animated', 'value' => true, 'option' => 'polyfold_transition')
								
							),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-polyfold-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-polyfold-addon', 
							'focus' => "*[data-r='addOns.revslider-polyfold-addon.top.ease']"
							
						)
						
					),
					
					'duration' => array(
						
						'title' => __('Transition Duration', 'revslider-polyfold-addon'),
						'helpPath' => 'addOns.revslider-polyfold-addon.top.time, addOns.revslider-polyfold-addon.bottom.time', 
						'keywords' => array('addon', 'addons', 'polyfold', 'polyfold addon', 'polyfold transition', 'transition'), 
						'description' => __("The total time the transition should occur", 'revslider-polyfold-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/polyfold-scroll-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Polyfold',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => 'settings.addOns.revslider-polyfold-addon.top.enabled', 'value' => true, 'option' => 'polyfold_enable'),
								array('path' => 'settings.addOns.revslider-polyfold-addon.top.scroll', 'value' => true, 'option' => 'polyfold_scroll'),
								array('path' => 'settings.addOns.revslider-polyfold-addon.top.animated', 'value' => true, 'option' => 'polyfold_transition')
								
							),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-polyfold-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-polyfold-addon', 
							'focus' => "*[data-r='addOns.revslider-polyfold-addon.top.time']"
							
						)
						
					),
					
					'invert_scroll' => array(
						
						'title' => __('Invert Scroll', 'revslider-polyfold-addon'),
						'helpPath' => 'addOns.revslider-polyfold-addon.top.invert, addOns.revslider-polyfold-addon.bottom.invert', 
						'keywords' => array('addon', 'addons', 'polyfold', 'polyfold addon', 'polyfold invert', 'invert scroll'), 
						'description' => __("Choose to invert drawing as the page is scrolled", 'revslider-polyfold-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/polyfold-scroll-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Polyfold',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => 'settings.addOns.revslider-polyfold-addon.top.enabled', 'value' => true, 'option' => 'polyfold_enable'),
								array('path' => 'settings.addOns.revslider-polyfold-addon.top.scroll', 'value' => true, 'option' => 'polyfold_scroll'),
								array('path' => 'settings.addOns.revslider-polyfold-addon.top.animated', 'value' => true, 'option' => 'polyfold_transition')
								
							),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-polyfold-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-polyfold-addon', 
							'focus' => "*[data-r='addOns.revslider-polyfold-addon.top.invert']"
							
						)
						
					)
				
				)
				
			)
			
		);
		
	}

}
	
?>