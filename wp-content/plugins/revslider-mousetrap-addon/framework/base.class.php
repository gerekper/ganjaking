<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnMousetrapBase {
	
	const MINIMUM_VERSION = '6.5.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnMousetrapBase::MINIMUM_VERSION, '>=')) {
		
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
			$update_admin = new RevAddOnMousetrapUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'));
			add_filter('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

			// require_once(static::$_PluginPath . 'admin/includes/slider.class.php');			
			
			// admin init
			// new RsMousetrapSliderAdmin(static::$_PluginTitle, static::$_Version);			
			
		}

		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		require_once(static::$_PluginPath . 'public/includes/slide.class.php');
		
		new RsMousetrapSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsMousetrapSlideFront(static::$_PluginTitle);
		
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
			$_jsPathMin = file_exists(static::$_PluginPath . 'admin/assets/js/revslider-' . static::$_PluginTitle . '-addon-admin.dev.js') ? '.dev' : '';
									
			wp_enqueue_style($_handle, $_base . 'css/revslider-' . static::$_PluginTitle . '-addon-admin.css', array(), static::$_Version);
			wp_enqueue_script($_handle, $_base . 'js/revslider-' . static::$_PluginTitle . '-addon-admin' . $_jsPathMin . '.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			wp_localize_script( $_handle, 'revslider_mousetrap_addon', self::get_var() );
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
				'self' => __('Pointer over this Layer','revslider-mousetrap-addon'),
				'limitations' => __('Distance Limitations','revslider-mousetrap-addon'),
				'offset' => __('Reactive Area','revslider-mousetrap-addon'),
				'direction' => __('Pointer Direction','revslider-mousetrap-addon'),
				'distance' => __('Distance','revslider-mousetrap-addon'),
				'none' => __('None','revslider-mousetrap-addon'),
				'horizontal' => __('Horizontal','revslider-mousetrap-addon'),
				'vertical' => __('Vertical','revslider-mousetrap-addon'),
				'center' => __('Center Oriented','revslider-mousetrap-addon'),
				'both' => __('Both','revslider-mousetrap-addon'),
				'min' => __('Minimum','revslider-mousetrap-addon'),
				'max' => __('Maximum','revslider-mousetrap-addon'),
				'attribute' => __('Attribute','revslider-mousetrap-addon'),
				'calcvaule' => __('Calculated By','revslider-mousetrap-addon'),
				'dependonaxis' => __('Mouse Move Axis','revslider-mousetrap-addon'),
				'addcustomrule' => __('Add Custom Rule','revslider-mousetrap-addon'),
				'customsettings' => __('Custom Animations','revslider-mousetrap-addon'),
				'block' => __('Block Axis','revslider-mousetrap-addon'),
				'blockx' => __('Block Axis X','revslider-mousetrap-addon'),
				'blocky' => __('Block Axis Y','revslider-mousetrap-addon'),
				'mousetrap_end' => __('Stop Follow Mouse','revslider-mousetrap-addon'),
				'mousetrap_start' => __('Start Follow Mouse','revslider-mousetrap-addon'),
				'origins' => __('Pointer Origin','revslider-mousetrap-addon'),
				'listener' => __('Use','revslider-mousetrap-addon'),
				'animation' => __('Animation','revslider-mousetrap-addon'),
				'mousetrap' => __('Mouse Trap','revslider-mousetrap-addon'),
				'mousesettings' => __('Mouse Trap Globals','revslider-mousetrap-addon'),
				'useonlayer' => __('Follow Mouse','revslider-mousetrap-addon'),				
				'disabled' => __('Disabled','revslider-mousetrap-addon'),				
				'onslider' => __('Pointer is over Slider','revslider-mousetrap-addon'),				
				'events' => __('On Events','revslider-mousetrap-addon'),				
				'ranges' => __('Pointer is over Ranges','revslider-mousetrap-addon'),				
				'hidemouse' => __('Hide Pointer','revslider-mousetrap-addon'),				
				'delay' => __('Speed','revslider-mousetrap-addon'),				
				'speed' => __('Speed','revslider-mousetrap-addon'),				
				'revert' => __('Revert','revslider-mousetrap-addon'),				
				'revertit' => __('Revert after Leaving Area','revslider-mousetrap-addon'),				
				'easing' => __('Easing','revslider-mousetrap-addon'),				
				'layerorigin' => __('Layer / Pointer Origin','revslider-mousetrap-addon'),				
				'rotate' => __('Rotation','revslider-mousetrap-addon'),				
				'rotation' => __('Layer Rotatation','revslider-mousetrap-addon'),				
				'rotatebase' => __('Dependency','revslider-mousetrap-addon'),				
				'anchorrotation' => __('Original Position','revslider-mousetrap-addon'),				
				'mouserotation' => __('Mouse Direction','revslider-mousetrap-addon'),				
				'moveradius' => __('Move Radius','revslider-mousetrap-addon'),				
				'follow' => __('Follow if','revslider-mousetrap-addon'),				
				'sensorlayer' => __('Layer Sensor','revslider-mousetrap-addon'),				
				'notused' => __('Not Used','revslider-mousetrap-addon'),				
				'onotherlayer' => __('Pointer over other Layer','revslider-mousetrap-addon')				
			)
		);
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-mousetrap-addon') {
		
		if($slug === 'revslider-mousetrap-addon'){
			
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
			$definitions['editor_settings']['slide_settings']['addons']['mousetrap_addon'] = $help['slide'];
		}
		
		if(isset($definitions['editor_settings']['layer_settings']) && isset($definitions['editor_settings']['layer_settings']['addons'])) {
			$definitions['editor_settings']['layer_settings']['addons']['mousetrap_addon'] = $help['layer'];
		}
		
		return $definitions;
	
	}
	
	/**
	 * Returns the addon help definitions
	 *
	 * @since    2.0.0
	 */
	private static function get_definitions() {
		
		$_textdomain = 'revslider-mousetrap-addon';
		return array(
					
		
			'layer' => array(
					
				'mouse_follow' => array(
						
					'buttonTitle' => __('Mouse Trap', $_textdomain), 
					'title' => __('Mouse Trap - Mouse Follower', $_textdomain),
					'helpPath' => 'addOns.revslider-mousetrap-addon.follow.mode', 
					'keywords' => array('addon', 'addons', 'mousetrap', 'mousetrap addon', 'mousetrap layer', 'mouse follow', 'mouse', 'mouse trap'), 
					'description' => __('The Mouse Trap Addon will allow to move, scale, rotate, follow Layers based on the position of Mouse pointer. Generaly a layer can be animated if the Mouse pointer is <ul><li><b>over the Slider</b> (POINTER IS OVER SLIDER)</li><li>over the <b>Layer itself</b> (POINTER OVER THIS LAYER)</li><li>over an <b>other selected layer</b> (POINTER OVER OTHER LAYER)</li><li>An <b>action</b> has been triggered (ON EVENTS)</li></ul>', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/mousetrap-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Mousetrap',
					'highlight' => array(
						
						'dependencies' => array('layerselected'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-mousetrap-addon", 
						'scrollTo' => '#form_layerinner_revslider-mousetrap-addon', 
						'focus' => "*[data-r='addOns.revslider-mousetrap-addon.follow.mode']"
						
					)
					
				),
				
				'mouse_follow_layer' => array(
						
					'buttonTitle' => __('Mouse Trap Follow Layer', $_textdomain), 
					'title' => __('Mouse Follow Layer', $_textdomain),
					'helpPath' => 'addOns.revslider-mousetrap-addon.follow.olayer', 
					'keywords' => array('addon', 'addons', 'mousetrap', 'mousetrap addon', 'mousetrap follow', 'follow layer', 'mouse follow', 'mouse trap follow', 'layer follow'), 
					'description' => __('Select the Layer which should be followed. If Mouse pointer is over this selected layer (sensor), the Mouse Trap Layer become active and will act as set in the further options. ', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/mousetrap-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Mousetrap',
					'highlight' => array(
						
						'dependencies' => array('layerselected'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-mousetrap-addon", 
						'scrollTo' => '#form_layerinner_revslider-mousetrap-addon', 
						'focus' => "*[data-r='addOns.revslider-mousetrap-addon.follow.olayer']"
						
					)
					
				),

				'mouse_follow_pointer_origin_x' => array(
						
					'buttonTitle' => __('Mouse Trap Pointer Origin', $_textdomain), 
					'title' => __('Mouse Trap Pointer Origin', $_textdomain),
					'helpPath' => 'addOns.revslider-mousetrap-addon.offset.x.#size#.v', 
					'keywords' => array('addon', 'addons', 'mousetrap', 'mousetrap addon', 'mousetrap follow', 'follow layer', 'mouse follow', 'mouse trap follow', 'layer follow', 'pointer', 'pointer origin','x offset'), 
					'description' => __('The horizontal position offset of the Mouse Trap Layer. This helps to harmonise and sync the layer position to the mouse pointer horizontaly.', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/mousetrap-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Mousetrap',
					'highlight' => array(
						
						'dependencies' => array('layerselected'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-mousetrap-addon", 
						'scrollTo' => '#form_layerinner_revslider-mousetrap-addon', 
						'focus' => "#mousetrack_offset_x]"
						
					)
					
				),

				'mouse_follow_pointer_origin_y' => array(
						
					'buttonTitle' => __('Mouse Trap Pointer Origin', $_textdomain), 
					'title' => __('Mouse Trap Pointer Origin', $_textdomain),
					'helpPath' => 'addOns.revslider-mousetrap-addon.offset.y.#size#.v', 
					'keywords' => array('addon', 'addons', 'mousetrap', 'mousetrap addon', 'mousetrap follow', 'follow layer', 'mouse follow', 'mouse trap follow', 'layer follow', 'pointer', 'pointer origin','y offset'), 
					'description' => __('The vertical position offset of the Mouse Trap Layer. This helps to harmonise and sync the layer position to the mouse pointer verticaly.', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/mousetrap-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Mousetrap',
					'highlight' => array(
						
						'dependencies' => array('layerselected'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-mousetrap-addon", 
						'scrollTo' => '#form_layerinner_revslider-mousetrap-addon', 
						'focus' => "#mousetrack_offset_y]"
						
					)
					
				),

				'hide_pointer' => array(
						
					'buttonTitle' => __('Mouse Trap Hide Pointer', $_textdomain), 
					'title' => __('Mouse Trap Hide Pointer', $_textdomain),
					'helpPath' => 'addOns.revslider-mousetrap-addon.follow.pointer', 
					'keywords' => array('addon', 'addons', 'mousetrap', 'mousetrap addon', 'mousetrap follow', 'follow layer', 'mouse follow', 'mouse trap follow', 'layer follow', 'pointer', 'pointer hide'), 
					'description' => __('Hide the Mouse Pointer if the Mouse Trap Layer follow the mouse pointer. In some browsers and on some platforms this feature will be ignored.', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/mousetrap-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Mousetrap',
					'highlight' => array(
						
						'dependencies' => array('layerselected'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-mousetrap-addon", 
						'scrollTo' => '#form_layerinner_revslider-mousetrap-addon', 
						'focus' => "*[data-r='addOns.revslider-mousetrap-addon.follow.pointer']"
						
					)
					
				),

				'block_axis_x' => array(
						
					'buttonTitle' => __('Mouse Trap block horizontal movements', $_textdomain), 
					'title' => __('Block horizontal movements', $_textdomain),
					'helpPath' => 'addOns.revslider-mousetrap-addon.follow.blockx', 
					'keywords' => array('addon', 'addons', 'mousetrap', 'mousetrap addon', 'mousetrap follow', 'follow layer', 'mouse follow', 'mouse trap follow', 'layer follow', 'block', 'ignore horizontal', 'horizontal'), 
					'description' => __('The Mouse Trap Layer will not follow horizontaly the mouse pointer. Helpts elements to move only vertically.', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/mousetrap-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Mousetrap',
					'highlight' => array(
						
						'dependencies' => array('layerselected'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-mousetrap-addon", 
						'scrollTo' => '#form_layerinner_revslider-mousetrap-addon', 
						'focus' => "*[data-r='addOns.revslider-mousetrap-addon.follow.blockx']"
						
					)
					
				),

				'block_axis_y' => array(
						
					'buttonTitle' => __('Mouse Trap block vertical movements', $_textdomain), 
					'title' => __('Block vertical movements', $_textdomain),
					'helpPath' => 'addOns.revslider-mousetrap-addon.follow.blocky', 
					'keywords' => array('addon', 'addons', 'mousetrap', 'mousetrap addon', 'mousetrap follow', 'follow layer', 'mouse follow', 'mouse trap follow', 'layer follow', 'block', 'ignore vertical', 'vertical'), 
					'description' => __('The Mouse Trap Layer will not follow verticaly the mouse pointer. Helpts elements to move only horizontaly.', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/mousetrap-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Mousetrap',
					'highlight' => array(
						
						'dependencies' => array('layerselected'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-mousetrap-addon", 
						'scrollTo' => '#form_layerinner_revslider-mousetrap-addon', 
						'focus' => "*[data-r='addOns.revslider-mousetrap-addon.follow.blocky']"
						
					)
					
				),

				'move_radius' => array(
						
					'buttonTitle' => __('Mouse Trap movement radius', $_textdomain), 
					'title' => __('Movement Radisu of Layer', $_textdomain),
					'helpPath' => 'addOns.revslider-mousetrap-addon.follow.radius.#size#.v', 
					'keywords' => array('addon', 'addons', 'mousetrap', 'mousetrap addon', 'mousetrap follow', 'follow layer', 'mouse follow', 'mouse trap follow', 'layer follow', 'radius', 'movement radius'), 
					'description' => __('Defines the max Radius calculated from the Pointer Origin of Mouse Trap Layer. 0px means will ignore this option. Great usage i.e. by following eyes.', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/mousetrap-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Mousetrap',
					'highlight' => array(
						
						'dependencies' => array('layerselected'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-mousetrap-addon", 
						'scrollTo' => '#form_layerinner_revslider-mousetrap-addon', 
						'focus' => "*[data-r='addOns.revslider-mousetrap-addon.follow.radius.#size#.v']"
						
					)
					
				),

				'mousetrap_speed' => array(
						
					'buttonTitle' => __('Mouse Trap movement speed', $_textdomain), 
					'title' => __('Mouse Trap movement speed', $_textdomain),
					'helpPath' => 'addOns.revslider-mousetrap-addon.follow.delay', 
					'keywords' => array('addon', 'addons', 'mousetrap', 'mousetrap addon', 'mousetrap follow', 'follow layer', 'mouse follow', 'mouse trap follow', 'layer follow', 'layer speed', 'follow speed', 'mouse speed'), 
					'description' => __('Defines the time within the Layer should follow the mouse pointer. 0ms means layer will instant follow the mouse pointer. i.e. 1000ms will follow the mouse within 1sec.', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/mousetrap-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Mousetrap',
					'highlight' => array(
						
						'dependencies' => array('layerselected'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-mousetrap-addon", 
						'scrollTo' => '#form_layerinner_revslider-mousetrap-addon', 
						'focus' => "*[data-r='addOns.revslider-mousetrap-addon.follow.delay']"
						
					)
					
				),

				'mousetrap_ease' => array(
						
					'buttonTitle' => __('Mouse Trap movement ease', $_textdomain), 
					'title' => __('Mouse Trap movement easing', $_textdomain),
					'helpPath' => 'addOns.revslider-mousetrap-addon.follow.ease', 
					'keywords' => array('addon', 'addons', 'mousetrap', 'mousetrap addon', 'mousetrap follow', 'follow layer', 'mouse follow', 'mouse trap follow', 'layer follow', 'layer ease', 'follow ease', 'mouse ease'), 
					'description' => __('Defines the easing of the layer animation during it follows the mouse pointer.', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/mousetrap-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Mousetrap',
					'highlight' => array(
						
						'dependencies' => array('layerselected'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-mousetrap-addon", 
						'scrollTo' => '#form_layerinner_revslider-mousetrap-addon', 
						'focus' => "*[data-r='addOns.revslider-mousetrap-addon.follow.ease']"
						
					)
					
				),

				'mousetrap_revert' => array(
						
					'buttonTitle' => __('Mouse Trap revert movement', $_textdomain), 
					'title' => __('Mouse Trap rever movement', $_textdomain),
					'helpPath' => 'addOns.revslider-mousetrap-addon.revert.use', 
					'keywords' => array('addon', 'addons', 'mousetrap', 'mousetrap addon', 'mousetrap follow', 'follow layer', 'mouse follow', 'mouse trap follow', 'layer follow', 'revert', 'follow revert', 'mouse revert'), 
					'description' => __('If revert set to on, the mouse trap layer will go back to its original position when action, pointer is not any more tiggering the layer.', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/mousetrap-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Mousetrap',
					'highlight' => array(
						
						'dependencies' => array('layerselected'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-mousetrap-addon", 
						'scrollTo' => '#form_layerinner_revslider-mousetrap-addon', 
						'focus' => "*[data-r='addOns.revslider-mousetrap-addon.revert.use']"
						
					)
					
				),

				'mousetrap_revert_speed' => array(
						
					'buttonTitle' => __('Mouse Trap revert movement speed', $_textdomain), 
					'title' => __('Mouse Trap revert movement speed', $_textdomain),
					'helpPath' => 'addOns.revslider-mousetrap-addon.revert.speed', 
					'keywords' => array('addon', 'addons', 'mousetrap', 'mousetrap addon', 'mousetrap follow', 'follow layer', 'mouse follow', 'mouse trap follow', 'layer revert', 'layer speed', 'revert speed', 'mouse revert speed'), 
					'description' => __('Defines the time within the Layer should go back to original position after it is not any more triggered. 0ms means layer will instant jump back to original position. i.e. 1000ms will move back the layer within 1sec.', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/mousetrap-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Mousetrap',
					'highlight' => array(
						
						'dependencies' => array('layerselected'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-mousetrap-addon", 
						'scrollTo' => '#form_layerinner_revslider-mousetrap-addon', 
						'focus' => "*[data-r='addOns.revslider-mousetrap-addon.revert.speed']"
						
					)
					
				),

				'mousetrap_revert_ease' => array(
						
					'buttonTitle' => __('Mouse Trap revert movement ease', $_textdomain), 
					'title' => __('Mouse Trap revert movement easing', $_textdomain),
					'helpPath' => 'addOns.revslider-mousetrap-addon.revert.ease', 
					'keywords' => array('addon', 'addons', 'mousetrap', 'mousetrap addon', 'mousetrap follow', 'follow layer', 'mouse follow', 'mouse trap follow', 'layer revert', 'layer revert ease', 'revert ease', 'mouse revert ease'), 
					'description' => __('Defines the easing of the layer animation during it reverts to the original position.', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/mousetrap-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Mousetrap',
					'highlight' => array(
						
						'dependencies' => array('layerselected'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-mousetrap-addon", 
						'scrollTo' => '#form_layerinner_revslider-mousetrap-addon', 
						'focus' => "*[data-r='addOns.revslider-mousetrap-addon.revert.ease']"
						
					)
					
				)				
			)
			
		);
		
	}
	
}
	
?>