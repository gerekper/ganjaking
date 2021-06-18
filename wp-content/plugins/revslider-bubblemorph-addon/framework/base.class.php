<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnBubblemorphBase {
	
	const MINIMUM_VERSION = '6.5.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnBubblemorphBase::MINIMUM_VERSION, '>=')) {
		
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
			$update_admin = new RevAddOnBubblemorphUpdate(static::$_Version);

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
		
		new RsBubblemorphSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsBubblemorphSlideFront(static::$_PluginTitle);
		
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
			wp_localize_script($_handle, 'revslider_bubblemorph_addon', self::get_var() );

		}
		
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-bubblemorph-addon') {
		
		if($slug === 'revslider-bubblemorph-addon'){
			
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
		
		if(isset($definitions['editor_settings']['layer_settings']) && isset($definitions['editor_settings']['layer_settings']['addons'])) {
			$help = self::get_definitions();
			$definitions['editor_settings']['layer_settings']['addons']['bubblemorph_addon'] = $help['layer'];
		}
		
		return $definitions;
	
	}
	
	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public static function get_var() {
			
		$_textdomain = 'revslider-bubblemorph-addon';
		return array(
		
			'bricks' => array(
				'bubblemorph' => __('BubbleMorph', $_textdomain),
				'settings' => __('BubbleMorph Settings', $_textdomain),
				'maxmorphs' => __('Max Morphs', $_textdomain),
				'shadow' => __('Shadow Style', $_textdomain),
				'border' => __('Border Style', $_textdomain),
				'strength' => __('Strength', $_textdomain),
				'color' => __('Color', $_textdomain),
				'size' => __('Size', $_textdomain),
				'placeholder' => __('Select', $_textdomain)
				
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
			
			'layer' => array(
				
				'max' => array(
					
					'buttonTitle' => __('BubbleMorph Max Morphs', 'revslider-bubblemorph-addon'), 
					'title' => __('Max Morphs', 'revslider-bubblemorph-addon'),
					'helpPath' => 'addOns.revslider-bubblemorph-addon.settings.maxmorphs.#size#.v', 
					'keywords' => array('addon', 'addons', 'bubblemorph', 'bubblemorph addon', 'bubblemorph max', 'max morphs'), 
					'description' => __("The maximum number of bends the bubble can have.  A low number will produce subtle distortion and a higher number will often break off into additional bubbles", 'revslider-bubblemorph-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/bubblemorph-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> BubbleMorph',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{bubblemorph}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-bubblemorph-addon", 
						'scrollTo' => '#form_layerinner_revslider-bubblemorph-addon', 
						'focus' => "*[data-r='addOns.revslider-bubblemorph-addon.settings.maxmorphs.#size#.v']"
						
					)
					
				),
				
				'speedx' => array(
					
					'buttonTitle' => __('BubbleMorph Speed X', 'revslider-bubblemorph-addon'), 
					'title' => __('Speed X', 'revslider-bubblemorph-addon'),
					'helpPath' => 'addOns.revslider-bubblemorph-addon.settings.speedx.#size#.v', 
					'keywords' => array('addon', 'addons', 'bubblemorph', 'bubblemorph addon', 'bubblemorph speed'), 
					'description' => __("The horizontal movement speed for the effect", 'revslider-bubblemorph-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/bubblemorph-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> BubbleMorph',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{bubblemorph}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-bubblemorph-addon", 
						'scrollTo' => '#form_layerinner_revslider-bubblemorph-addon', 
						'focus' => "*[data-r='addOns.revslider-bubblemorph-addon.settings.speedx.#size#.v']"
						
					)
					
				),
				
				'speedy' => array(
					
					'buttonTitle' => __('BubbleMorph Speed Y', 'revslider-bubblemorph-addon'), 
					'title' => __('Speed Y', 'revslider-bubblemorph-addon'),
					'helpPath' => 'addOns.revslider-bubblemorph-addon.settings.speedy.#size#.v', 
					'keywords' => array('addon', 'addons', 'bubblemorph', 'bubblemorph addon', 'bubblemorph speed'), 
					'description' => __("The vertical movement speed for the effect", 'revslider-bubblemorph-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/bubblemorph-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> BubbleMorph',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{bubblemorph}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-bubblemorph-addon", 
						'scrollTo' => '#form_layerinner_revslider-bubblemorph-addon', 
						'focus' => "*[data-r='addOns.revslider-bubblemorph-addon.settings.speedy.#size#.v']"
						
					)
					
				),
				
				'bufferx' => array(
					
					'buttonTitle' => __('BubbleMorph Buffer X', 'revslider-bubblemorph-addon'), 
					'title' => __('Buffer X', 'revslider-bubblemorph-addon'),
					'helpPath' => 'addOns.revslider-bubblemorph-addon.settings.bufferx.#size#.v', 
					'keywords' => array('addon', 'addons', 'bubblemorph', 'bubblemorph addon', 'bubblemorph buffer'), 
					'description' => __("Horizontal buffer in pixels to help keep the bubble from bleeding outside the Slider", 'revslider-bubblemorph-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/bubblemorph-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> BubbleMorph',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{bubblemorph}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-bubblemorph-addon", 
						'scrollTo' => '#form_layerinner_revslider-bubblemorph-addon', 
						'focus' => "*[data-r='addOns.revslider-bubblemorph-addon.settings.bufferx.#size#.v']"
						
					)
					
				),
				
				'buffery' => array(
					
					'buttonTitle' => __('BubbleMorph Buffer Y', 'revslider-bubblemorph-addon'), 
					'title' => __('Buffer Y', 'revslider-bubblemorph-addon'),
					'helpPath' => 'addOns.revslider-bubblemorph-addon.settings.buffery.#size#.v', 
					'keywords' => array('addon', 'addons', 'bubblemorph', 'bubblemorph addon', 'bubblemorph buffer'), 
					'description' => __("Vertical buffer in pixels to help keep the bubble from bleeding outside the Slider", 'revslider-bubblemorph-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/bubblemorph-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> BubbleMorph',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{bubblemorph}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-bubblemorph-addon", 
						'scrollTo' => '#form_layerinner_revslider-bubblemorph-addon', 
						'focus' => "*[data-r='addOns.revslider-bubblemorph-addon.settings.buffery.#size#.v']"
						
					)
					
				),
				
				'shadow' => array(
				
					'strength' => array(
					
						'buttonTitle' => __('BubbleMorph Shadow', 'revslider-bubblemorph-addon'), 
						'title' => __('Shadow Strength', 'revslider-bubblemorph-addon'),
						'helpPath' => 'addOns.revslider-bubblemorph-addon.shadow.strength.#size#.v', 
						'keywords' => array('addon', 'addons', 'bubblemorph', 'bubblemorph addon', 'bubblemorph shadow', 'shadow'), 
						'description' => __('Add a shadow to the bubbles.  Enter "0" for no shadow', 'revslider-bubblemorph-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/bubblemorph-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> BubbleMorph',
						'highlight' => array(
							
							'dependencies' => array('layerselected::shape{{bubblemorph}}'), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-bubblemorph-addon", 
							'scrollTo' => '#bubblemorph_shadow', 
							'focus' => "*[data-r='addOns.revslider-bubblemorph-addon.shadow.strength.#size#.v']"
							
						)
						
					),
					
					'color' => array(
					
						'buttonTitle' => __('BubbleMorph Shadow Color', 'revslider-bubblemorph-addon'), 
						'title' => __('Shadow Color', 'revslider-bubblemorph-addon'),
						'helpPath' => 'addOns.revslider-bubblemorph-addon.shadow.color.#size#.v', 
						'keywords' => array('addon', 'addons', 'bubblemorph', 'bubblemorph addon', 'bubblemorph shadow', 'shadow'), 
						'description' => __("The color for the bubble's shadow", 'revslider-bubblemorph-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/bubblemorph-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> BubbleMorph',
						'highlight' => array(
							
							'dependencies' => array('layerselected::shape{{bubblemorph}}'), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-bubblemorph-addon", 
							'scrollTo' => '#bubblemorph_shadow', 
							'focus' => "#bubblemorph_shadow_color"
							
						)
						
					),
					
					'offsetx' => array(
					
						'title' => __('Shadow Offset X', 'revslider-bubblemorph-addon'),
						'helpPath' => 'addOns.revslider-bubblemorph-addon.shadow.offsetx.#size#.v', 
						'keywords' => array('addon', 'addons', 'bubblemorph', 'bubblemorph addon', 'bubblemorph shadow', 'shadow'), 
						'description' => __('The offset-x value for the <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/box-shadow" target="_blank">CSS box-shadow</a>', 'revslider-bubblemorph-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/bubblemorph-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> BubbleMorph',
						'highlight' => array(
							
							'dependencies' => array('layerselected::shape{{bubblemorph}}'), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-bubblemorph-addon", 
							'scrollTo' => '#bubblemorph_shadow', 
							'focus' => "*[data-r='addOns.revslider-bubblemorph-addon.shadow.offsetx.#size#.v']"
							
						)
						
					),
					
					'offsety' => array(
					
						'title' => __('Shadow Offset Y', 'revslider-bubblemorph-addon'),
						'helpPath' => 'addOns.revslider-bubblemorph-addon.shadow.offsety.#size#.v', 
						'keywords' => array('addon', 'addons', 'bubblemorph', 'bubblemorph addon', 'bubblemorph shadow', 'shadow'), 
						'description' => __('The offset-y value for the <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/box-shadow" target="_blank">CSS box-shadow</a>', 'revslider-bubblemorph-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/bubblemorph-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> BubbleMorph',
						'highlight' => array(
							
							'dependencies' => array('layerselected::shape{{bubblemorph}}'), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-bubblemorph-addon", 
							'scrollTo' => '#bubblemorph_shadow', 
							'focus' => "*[data-r='addOns.revslider-bubblemorph-addon.shadow.offsety.#size#.v']"
							
						)
						
					)
				
				),
				
				'border' => array(
				
					'size' => array(
						
						'buttonTitle' => __('BubbleMorph Border Size', 'revslider-bubblemorph-addon'), 
						'title' => __('Border Size', 'revslider-bubblemorph-addon'),
						'helpPath' => 'addOns.revslider-bubblemorph-addon.border.size.#size#.v', 
						'keywords' => array('addon', 'addons', 'bubblemorph', 'bubblemorph addon', 'bubblemorph border', 'border'), 
						'description' => __('Adds a border to the bubble.  Enter "0" for no border', 'revslider-bubblemorph-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/bubblemorph-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> BubbleMorph',
						'highlight' => array(
							
							'dependencies' => array('layerselected::shape{{bubblemorph}}'), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-bubblemorph-addon", 
							'scrollTo' => '#bubblemorph_border', 
							'focus' => "*[data-r='addOns.revslider-bubblemorph-addon.border.size.#size#.v']"
							
						)
						
					),
					
					'color' => array(
						
						'buttonTitle' => __('BubbleMorph Border Color', 'revslider-bubblemorph-addon'), 
						'title' => __('Border Color', 'revslider-bubblemorph-addon'),
						'helpPath' => 'addOns.revslider-bubblemorph-addon.border.color.#size#.v', 
						'keywords' => array('addon', 'addons', 'bubblemorph', 'bubblemorph addon', 'bubblemorph border', 'border'), 
						'description' => __('Adds a border to the bubble.  Enter "0" for no border', 'revslider-bubblemorph-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/bubblemorph-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> BubbleMorph',
						'highlight' => array(
							
							'dependencies' => array('layerselected::shape{{bubblemorph}}'), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-bubblemorph-addon", 
							'scrollTo' => '#bubblemorph_border', 
							'focus' => "#bubblemorph_border_color"
							
						)
						
					)
				
				)
			
			)
			
		);
		
	}

}
	
?>