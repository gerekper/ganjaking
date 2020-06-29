<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class rs_whiteboard_base { // extends RevSliderFunctions
	
	public function __construct(){
		
		add_filter('revslider_get_svg_sets', array('rs_whiteboard_base', 'enqueue_svg'));			
		self::load_plugin_textdomain();
		
		if(is_admin()){
			require_once(WHITEBOARD_PLUGIN_PATH.'admin/includes/slider.class.php');								
			rs_whiteboard_slider::init();
			
			//Updates
			require_once(WHITEBOARD_PLUGIN_PATH.'admin/includes/update.class.php');
			$update_admin = new rs_whiteboard_update(WHITEBOARD_VERSION);
			add_filter('pre_set_site_transient_update_plugins', array($update_admin ,'set_update_transient'));
			add_filter('plugins_api', array($update_admin ,'set_updates_api_results'), 10, 3);

		}
			
		require_once(WHITEBOARD_PLUGIN_PATH.'public/includes/slider.class.php');
		require_once(WHITEBOARD_PLUGIN_PATH.'public/includes/slide.class.php');
		
		new rs_whiteboard_fe_slide();
		new rs_whiteboard_fe_slider();

	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-whiteboard-addon') {
		
		if($slug === 'revslider-whiteboard-addon'){
			
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
			$definitions['editor_settings']['slider_settings']['addons']['whiteboard_addon'] = $help['slider'];
		}
		
		if(isset($definitions['editor_settings']['layer_settings']) && isset($definitions['editor_settings']['layer_settings']['addons'])) {
			$help = self::get_definitions();
			$definitions['editor_settings']['layer_settings']['addons']['whiteboard_addon'] = $help['layer'];
		}
		
		return $definitions;
	
	}
	
	// new get_var for on-demand activation (slider.class.php was not included previously)
	public static function get_var($var='',$slug=''){
	
		if(!class_exists('rs_whiteboard_slider')) {
			include_once(WHITEBOARD_PLUGIN_PATH.'admin/includes/slider.class.php');
		}
		
		return rs_whiteboard_slider::whiteboard_get_var();
	
	}
	
	public static function enqueue_svg($svg_sets){
		
		$svg_sets['Whiteboard'] = array('path' => WHITEBOARD_PLUGIN_PATH . 'public/assets/svg/busy-icons-svg/', 'url' => WHITEBOARD_PLUGIN_URL . 'public/assets/svg/busy-icons-svg/');
		
		return $svg_sets;
	}
	
	public static function load_plugin_textdomain(){
		load_plugin_textdomain('rs_whiteboard', false, 'revslider-whiteboard-addon/languages/');
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public static function display_plugin_admin_page() {
		include_once( WHITEBOARD_PLUGIN_PATH . 'admin/views/admin-display.php' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public static function enqueue_dash_scripts() {
		if(isset($_GET['page']) && $_GET['page'] == 'rev_addon'){
			wp_enqueue_script('rs_whiteboard_dash', WHITEBOARD_PLUGIN_URL . 'admin/assets/js/rev_addon_dash-admin.js', array('jquery', 'revbuilder-admin'), WHITEBOARD_VERSION, false);
			/*wp_localize_script( $this->plugin_name, 'rs_whiteboard', array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
			));*/
		}
	}

	/**
	 * Register the CSS for the admin area.
	 *
	 * @since    1.0.0
	 */
	public static function enqueue_dash_style(){
		if(isset($_GET['page']) && $_GET['page'] == 'rev_addon'){
			wp_enqueue_style('rs_whiteboard_dash', WHITEBOARD_PLUGIN_URL . 'admin/assets/css/whiteboard-dash-admin.css', array(), WHITEBOARD_VERSION);
		}
	}
	
	/**
	 * Returns the addon help definitions
	 *
	 * @since    2.0.0
	 */
	private static function get_definitions() {
		
		return array(
			
			'slider' => array(
				
				'write_hand' => array(
				
					'image' => array(
						
						'buttonTitle' => __('Write Hand Image', 'revslider-whiteboard-addon'), 
						'title' => __('Image Graphic', 'revslider-whiteboard-addon'),
						'helpPath' => 'settings.addOns.revslider-whiteboard-addon.writehand.source', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'write hand'), 
						'description' => __("The image graphic to use for the writing hand", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Whiteboard',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-whiteboard-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-whiteboard-addon', 
							'focus' => "*[data-r='settings.addOns.revslider-whiteboard-addon.writehand.source']"
							
						)
						
					),
					
					'width' => array(
						
						'title' => __('Image Width', 'revslider-whiteboard-addon'), 
						'helpPath' => 'addOns.revslider-whiteboard-addon.writehand.width', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'write hand', 'write hand width'), 
						'description' => __("The default width for the writing hand image (the image will be responsive by default)", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Whiteboard',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-whiteboard-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-whiteboard-addon', 
							'focus' => "*[data-r='addOns.revslider-whiteboard-addon.writehand.width']"
							
						)
						
					),
					
					'height' => array(
						
						'title' => __('Image Height', 'revslider-whiteboard-addon'), 
						'helpPath' => 'addOns.revslider-whiteboard-addon.writehand.height', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'write hand', 'write hand height'), 
						'description' => __("The default height for the writing hand image (the image will be responsive by default)", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Whiteboard',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-whiteboard-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-whiteboard-addon', 
							'focus' => "*[data-r='addOns.revslider-whiteboard-addon.writehand.height']"
							
						)
						
					),
					
					'origin_x' => array(
						
						'title' => __('Origin X', 'revslider-whiteboard-addon'), 
						'helpPath' => 'addOns.revslider-whiteboard-addon.writehand.originX', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'write hand', 'origin x', 'position x'), 
						'description' => __("The graphic's offsetX position in relation to the Layer as it's drawn", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Whiteboard',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-whiteboard-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-whiteboard-addon', 
							'focus' => "*[data-r='addOns.revslider-whiteboard-addon.writehand.originX']"
							
						)
						
					),
					
					'origin_y' => array(
						
						'title' => __('Origin Y', 'revslider-whiteboard-addon'), 
						'helpPath' => 'addOns.revslider-whiteboard-addon.writehand.originY', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'write hand', 'origin y', 'position y'), 
						'description' => __("The graphic's offsetY position in relation to the Layer as it's drawn", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Whiteboard',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-whiteboard-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-whiteboard-addon', 
							'focus' => "*[data-r='addOns.revslider-whiteboard-addon.writehand.originY']"
							
						)
						
					)
					
				),
				
				'move_hand' => array(
				
					'image' => array(
						
						'buttonTitle' => __('Move Hand Image', 'revslider-whiteboard-addon'), 
						'title' => __('Image Graphic', 'revslider-whiteboard-addon'),
						'helpPath' => 'settings.addOns.revslider-whiteboard-addon.movehand.source', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'move hand'), 
						'description' => __("The image graphic to use for the writing hand", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Whiteboard',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-whiteboard-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-whiteboard-addon', 
							'focus' => "*[data-r='settings.addOns.revslider-whiteboard-addon.movehand.source']"
							
						)
						
					),
					
					'width' => array(
						
						'title' => __('Image Width', 'revslider-whiteboard-addon'), 
						'helpPath' => 'addOns.revslider-whiteboard-addon.movehand.width', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'move hand', 'move hand width'), 
						'description' => __("The default width for the writing hand image (the image will be responsive by default)", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Whiteboard',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-whiteboard-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-whiteboard-addon', 
							'focus' => "*[data-r='addOns.revslider-whiteboard-addon.movehand.width']"
							
						)
						
					),
					
					'height' => array(
						
						'title' => __('Image Height', 'revslider-whiteboard-addon'), 
						'helpPath' => 'addOns.revslider-whiteboard-addon.movehand.height', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'move hand', 'move hand height'), 
						'description' => __("The default height for the writing hand image (the image will be responsive by default)", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Whiteboard',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-whiteboard-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-whiteboard-addon', 
							'focus' => "*[data-r='addOns.revslider-whiteboard-addon.movehand.height']"
							
						)
						
					),
					
					'origin_x' => array(
						
						'title' => __('Origin X', 'revslider-whiteboard-addon'), 
						'helpPath' => 'addOns.revslider-whiteboard-addon.movehand.originX', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'move hand', 'origin x', 'position x'), 
						'description' => __("The graphic's offsetX position in relation to the Layer as it's drawn", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Whiteboard',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-whiteboard-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-whiteboard-addon', 
							'focus' => "*[data-r='addOns.revslider-whiteboard-addon.movehand.originX']"
							
						)
						
					),
					
					'origin_y' => array(
						
						'title' => __('Origin Y', 'revslider-whiteboard-addon'), 
						'helpPath' => 'addOns.revslider-whiteboard-addon.movehand.originY', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'move hand', 'origin y', 'position y'), 
						'description' => __("The graphic's offsetY position in relation to the Layer as it's drawn", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Whiteboard',
						'highlight' => array(
						
							'menu' => "#module_settings_trigger, #gst_sl_revslider-whiteboard-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-whiteboard-addon', 
							'focus' => "*[data-r='addOns.revslider-whiteboard-addon.movehand.originY']"
							
						)
						
					)
					
				)
				
			),
			
			'layer' => array(
			
				'enable' => array(
					
					'dependency_id' => 'whiteboard_enable',
					'buttonTitle' => __('Enable Whiteboard', 'revslider-whiteboard-addon'), 
					'title' => __('Enable', 'revslider-whiteboard-addon'), 
					'helpPath' => 'addOns.revslider-whiteboard-addon.enable', 
					'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon'), 
					'description' => __("Draw/Move the currently selected Layer with the Whiteboard AddOn", 'revslider-whiteboard-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Whiteboard',
					'highlight' => array(
						
						'dependencies' => array('layerselected'),
						'menu' => "#module_layers_trigger, #gst_layer_revslider-whiteboard-addon", 
						'scrollTo' => '#form_layerinner_revslider-whiteboard-addon', 
						'focus' => "*[data-r='addOns.revslider-whiteboard-addon.enable']"
						
					)
					
				),
				
				'presets' => array(
					
					'title' => __('Settings Presets', 'revslider-whiteboard-addon'), 
					'helpPath' => 'whiteboard-presets', 
					'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon'), 
					'description' => __("Choose a predefined group of settings for quick use", 'revslider-whiteboard-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Whiteboard',
					'highlight' => array(
						
						'dependencies' => array('layerselected', array('path' => '#slide#.layers.#layer#.addOns.revslider-whiteboard-addon.enable', 'value' => true, 'option' => 'whiteboard_enable')),
						'menu' => "#module_layers_trigger, #gst_layer_revslider-whiteboard-addon", 
						'scrollTo' => '#form_layerinner_revslider-whiteboard-addon', 
						'focus' => "*[data-helpkey='whiteboard-presets']"
						
					)
					
				),
				
				'functionality' => array(
				
					'mode' => array(
						
						'buttonTitle' => __('Whiteboard Mode', 'revslider-whiteboard-addon'), 
						'title' => __('Mode', 'revslider-whiteboard-addon'), 
						'helpPath' => 'addOns.revslider-whiteboard-addon.hand.mode', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'whiteboard mode'), 
						'description' => __("Choose if the AddOn should 'write', 'draw' or 'move' the Layer into view", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Whiteboard',
						'highlight' => array(
							
							'dependencies' => array('layerselected', array('path' => '#slide#.layers.#layer#.addOns.revslider-whiteboard-addon.enable', 'value' => true, 'option' => 'whiteboard_enable')),
							'menu' => "#module_layers_trigger, #gst_layer_revslider-whiteboard-addon", 
							'scrollTo' => '#form_layerinner_revslider-whiteboard-addon', 
							'focus' => "*[data-r='addOns.revslider-whiteboard-addon.hand.mode']"
							
						)
						
					),
					
					'hand' => array(
						
						'buttonTitle' => __('Whiteboard Hand', 'revslider-whiteboard-addon'), 
						'title' => __('Hand Type', 'revslider-whiteboard-addon'), 
						'helpPath' => 'addOns.revslider-whiteboard-addon.hand.type', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'whiteboard hand', 'hand'), 
						'description' => __("Choose to draw/write/move the Layer with a left or right hand", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Whiteboard',
						'highlight' => array(
							
							'dependencies' => array('layerselected', array('path' => '#slide#.layers.#layer#.addOns.revslider-whiteboard-addon.enable', 'value' => true, 'option' => 'whiteboard_enable')),
							'menu' => "#module_layers_trigger, #gst_layer_revslider-whiteboard-addon", 
							'scrollTo' => '#form_layerinner_revslider-whiteboard-addon', 
							'focus' => "*[data-r='addOns.revslider-whiteboard-addon.hand.type']"
							
						)
						
					),
					
					'at_end' => array(
						
						'title' => __('End Behavior', 'revslider-whiteboard-addon'), 
						'helpPath' => 'addOns.revslider-whiteboard-addon.hand.gotoLayer', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'whiteboard hand', 'hand', 'hide hand'), 
						'description' => __("Choose to hide the hand after the Layer is written/moved into view", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Whiteboard',
						'highlight' => array(
							
							'dependencies' => array('layerselected', array('path' => '#slide#.layers.#layer#.addOns.revslider-whiteboard-addon.enable', 'value' => true, 'option' => 'whiteboard_enable')),
							'menu' => "#module_layers_trigger, #gst_layer_revslider-whiteboard-addon", 
							'scrollTo' => '#form_layerinner_revslider-whiteboard-addon', 
							'focus' => "*[data-r='addOns.revslider-whiteboard-addon.hand.gotoLayer']"
							
						)
						
					)
					
				),
				
				'hand_options' => array(
				
					'angle' => array(
							
						'title' => __('Writing Angle', 'revslider-whiteboard-addon'), 
						'helpPath' => 'addOns.revslider-whiteboard-addon.hand.angle', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'whiteboard angle'), 
						'description' => __("Determines the tilt of the hand as the Layer is drawn/moved", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Whiteboard',
						'highlight' => array(
							
							'dependencies' => array('layerselected', array('path' => '#slide#.layers.#layer#.addOns.revslider-whiteboard-addon.enable', 'value' => true, 'option' => 'whiteboard_enable')),
							'menu' => "#module_layers_trigger, #gst_layer_revslider-whiteboard-addon", 
							'scrollTo' => '#form_layerinner_revslider-whiteboard-addon', 
							'focus' => "*[data-r='addOns.revslider-whiteboard-addon.hand.angle']"
							
						)
						
					),
					
					'variations' => array(
							
						'title' => __('Angle Variations', 'revslider-whiteboard-addon'), 
						'helpPath' => 'addOns.revslider-whiteboard-addon.hand.angleRepeat', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'whiteboard angle', 'variations', 'writing angle'), 
						'description' => __("Helps to create more natural movement as the hand draws/moves the Layer into place", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Whiteboard',
						'highlight' => array(
							
							'dependencies' => array('layerselected', array('path' => '#slide#.layers.#layer#.addOns.revslider-whiteboard-addon.enable', 'value' => true, 'option' => 'whiteboard_enable')),
							'menu' => "#module_layers_trigger, #gst_layer_revslider-whiteboard-addon", 
							'scrollTo' => '#form_layerinner_revslider-whiteboard-addon', 
							'focus' => "*[data-r='addOns.revslider-whiteboard-addon.hand.angleRepeat']"
							
						)
						
					),
					
					'jittering_area' => array(
							
						'title' => __('Jittering Area Height', 'revslider-whiteboard-addon'), 
						'helpPath' => 'addOns.revslider-whiteboard-addon.jitter.distance', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'jittering', 'whiteboard height'), 
						'description' => __("The amount of space the hand will move in relation to the size of the Layer", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Whiteboard',
						'highlight' => array(
							
							'dependencies' => array('layerselected', array('path' => '#slide#.layers.#layer#.addOns.revslider-whiteboard-addon.enable', 'value' => true, 'option' => 'whiteboard_enable')),
							'menu' => "#module_layers_trigger, #gst_layer_revslider-whiteboard-addon", 
							'scrollTo' => '#form_layerinner_revslider-whiteboard-addon', 
							'focus' => "*[data-r='addOns.revslider-whiteboard-addon.jitter.distance']"
							
						)
						
					),
					
					'jittering_offset' => array(
							
						'title' => __('Jittering Area Offset', 'revslider-whiteboard-addon'), 
						'helpPath' => 'addOns.revslider-whiteboard-addon.jitter.offset', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'jittering', 'area offset'), 
						'description' => __("A translateY offset for the hand to place it slightly higher or lower in relation to the drawn Layer", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Whiteboard',
						'highlight' => array(
							
							'dependencies' => array('layerselected', array('path' => '#slide#.layers.#layer#.addOns.revslider-whiteboard-addon.enable', 'value' => true, 'option' => 'whiteboard_enable')),
							'menu' => "#module_layers_trigger, #gst_layer_revslider-whiteboard-addon", 
							'scrollTo' => '#form_layerinner_revslider-whiteboard-addon', 
							'focus' => "*[data-r='addOns.revslider-whiteboard-addon.jitter.offset']"
							
						)
						
					),
					
					'jittering_changes' => array(
							
						'title' => __('Jittering Area Offset', 'revslider-whiteboard-addon'), 
						'helpPath' => 'addOns.revslider-whiteboard-addon.jitter.repeat', 
						'keywords' => array('addon', 'addons', 'whiteboard', 'whiteboard addon', 'jittering', 'jittering changes'), 
						'description' => __("The amount of times the hand should jitter/shake as the Layer is drawn", 'revslider-whiteboard-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/whiteboard-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Whiteboard',
						'highlight' => array(
							
							'dependencies' => array('layerselected', array('path' => '#slide#.layers.#layer#.addOns.revslider-whiteboard-addon.enable', 'value' => true, 'option' => 'whiteboard_enable')),
							'menu' => "#module_layers_trigger, #gst_layer_revslider-whiteboard-addon", 
							'scrollTo' => '#form_layerinner_revslider-whiteboard-addon', 
							'focus' => "*[data-r='addOns.revslider-whiteboard-addon.jitter.repeat']"
							
						)
						
					)
					
				)
				
			)
			
		);
		
	}

}
?>