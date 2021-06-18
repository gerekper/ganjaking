<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnExplodinglayersBase {
	
	const MINIMUM_VERSION = '6.5.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnExplodinglayersBase::MINIMUM_VERSION, '>=') && RevSliderGlobals::SLIDER_REVISION!="6.3.CANVAS") {
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
			$update_admin = new RevAddOnExplodinglayersUpdate(static::$_Version);

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
		
		new RsExplodinglayersSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsExplodinglayersSlideFront(static::$_PluginTitle);
		
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
			wp_localize_script( $_handle, 'revslider_explodinglayers_addon', self::get_var() );
		}
		
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-explodinglayers-addon') {
		
		if($slug === 'revslider-explodinglayers-addon'){
			
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
		
		if(isset($definitions['editor_settings']['layer_settings']) && isset($definitions['editor_settings']['layer_settings']['addons'])) {
			$definitions['editor_settings']['layer_settings']['addons']['explodinglayers_addon'] = $help['layer'];
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
				'explodinglayers' => __('Exploding Layers','revslider-explodinglayers-addon'),
				'particlestyle' => __('Particle Style','revslider-explodinglayers-addon'),
				'particlesize' => __('Particle Size','revslider-explodinglayers-addon'),
				'particledensity' => __('Particle Density','revslider-explodinglayers-addon'),
				'particleswarm' => __('Particle Swarm','revslider-explodinglayers-addon'),
				'padding' => __('Padding','revslider-explodinglayers-addon'),
				'randomsize' => __('Random Size','revslider-explodinglayers-addon'),
				'particlecolor' => __('Particle Color','revslider-explodinglayers-addon'),
				'direction' => __('Direction','revslider-explodinglayers-addon'),
				'direction' => __('Direction','revslider-explodinglayers-addon'),
				'antigravity' => __('Anti Gravity','revslider-explodinglayers-addon'),
				'randomgravity' => __('Random Gravity','revslider-explodinglayers-addon'),
				'synchelper' => __('Sync Helper','revslider-explodinglayers-addon'),
				'top' => __('Top','revslider-explodinglayers-addon'),
				'bottom' => __('Bottom','revslider-explodinglayers-addon'),
				'left' => __('Left','revslider-explodinglayers-addon'),
				'right' => __('Right','revslider-explodinglayers-addon'),
				'fill' => __('Fill','revslider-explodinglayers-addon'),
				'stroke' => __('Stroke','revslider-explodinglayers-addon'),
				'objlibrary' => __('Object Library','revslider-explodinglayers-addon'),
				'shape' => __('Shape','revslider-explodinglayers-addon')

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
				
				'enable' => array(
					
					'dependency_id' => 'explodinglayers_enable',
					'buttonTitle' => __('Exploding Layer Animation', 'revslider-explodinglayers-addon'), 
					'title' => __('Enable', 'revslider-explodinglayers-addon'),
					'helpPath' => '#frame#.explode.use', 
					'keywords' => array('addon', 'addons', 'exploding', 'exploding layers', 'exploding layers addon', 'transition', 'animation'), 
					'description' => __('Enable the Exploding Layers AddOn for the Layer Animation(s), available for "In -> Anim From" and "Out -> Anim To"', 'revslider-explodinglayers-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/exploding-layers-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Animation -> Advanced',
					'highlight' => array(
						
						'dependencies' => array('layerselected'),
						'menu' => "#module_layers_trigger, #gst_layer_4, #keyframe_list_el_frame_0 .frame_list_title, #explode_ts_wrapbrtn .transtarget_selector", 
						'scrollTo' => '#form_animation_sframes_advanced', 
						'focus' => "*[data-r='#frame#.explode.use']"
						
					)
					
				),
				
				'type' => array(
					
					'buttonTitle' => __('Exploding Layer Shape', 'revslider-explodinglayers-addon'), 
					'title' => __('Shape Selection', 'revslider-explodinglayers-addon'),
					'helpPath' => '#frame#.explode.type', 
					'keywords' => array('addon', 'addons', 'exploding', 'exploding layers', 'exploding layers addon', 'shape', 'exploding layers shape', 'svg', 'particle'), 
					'description' => __("Choose an SVG to use for the Exploding Layers effect", 'revslider-explodinglayers-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/exploding-layers-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Animation -> Advanced',
					'highlight' => array(
						
						'dependencies' => array('layerselected', '#module_layers_trigger', '#gst_layer_4', '#keyframe_list_el_frame_0 .frame_list_title', array('path' => '#slide#.layers.#layer#.timeline.frames.#frame#.explode.use', 'value' => true, 'option' => 'explodinglayers_enable')),
						'scrollTo' => '#form_animation_sframes_advanced', 
						'focus' => ".explodinglayers-icon.selected"
						
					)
					
				),
				
				'size_style' => array(
				
					'color' => array(
						
						'buttonTitle' => __('Exploding Layer Color', 'revslider-explodinglayers-addon'), 
						'title' => __('Particle Color', 'revslider-explodinglayers-addon'),
						'helpPath' => '#frame#.explode.color', 
						'keywords' => array('addon', 'addons', 'exploding', 'exploding layers', 'exploding layers addon', 'color', 'exploding layers color', 'particle color'), 
						'description' => __("Choose an SVG to use for the Exploding Layers effect", 'revslider-explodinglayers-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/exploding-layers-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Animation -> Advanced',
						'highlight' => array(
							
							'dependencies' => array('layerselected', '#module_layers_trigger', '#gst_layer_4', '#keyframe_list_el_frame_0 .frame_list_title', array('path' => '#slide#.layers.#layer#.timeline.frames.#frame#.explode.use', 'value' => true, 'option' => 'explodinglayers_enable')),
							'scrollTo' => '#form_animation_sframes_advanced', 
							'focus' => "#expllay_fr_color"
							
						)
						
					),
					
					'style' => array(
						
						'title' => __('Particle Fill Style', 'revslider-explodinglayers-addon'),
						'helpPath' => '#frame#.explode.style', 
						'keywords' => array('addon', 'addons', 'exploding', 'exploding layers', 'exploding layers addon', 'fill', 'particle', 'particle fill'), 
						'description' => __("Choose if the particle shape should have a solid color (fill) or an outline/border (stroke)", 'revslider-explodinglayers-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/exploding-layers-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Animation -> Advanced',
						'highlight' => array(
							
							'dependencies' => array('layerselected', '#module_layers_trigger', '#gst_layer_4', '#keyframe_list_el_frame_0 .frame_list_title', array('path' => '#slide#.layers.#layer#.timeline.frames.#frame#.explode.use', 'value' => true, 'option' => 'explodinglayers_enable')),
							'scrollTo' => '#form_animation_sframes_advanced', 
							'focus' => "#el_part_style"
							
						)
						
					),
					
					'size' => array(
						
						'title' => __('Particle Size', 'revslider-explodinglayers-addon'),
						'helpPath' => '#frame#.explode.size', 
						'keywords' => array('addon', 'addons', 'exploding', 'exploding layers', 'exploding layers addon', 'size', 'particle', 'particle size'), 
						'description' => __("The size of the Exploding Layer particles in pixels", 'revslider-explodinglayers-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/exploding-layers-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Animation -> Advanced',
						'highlight' => array(
							
							'dependencies' => array('layerselected', '#module_layers_trigger', '#gst_layer_4', '#keyframe_list_el_frame_0 .frame_list_title', array('path' => '#slide#.layers.#layer#.timeline.frames.#frame#.explode.use', 'value' => true, 'option' => 'explodinglayers_enable')),
							'scrollTo' => '#form_animation_sframes_advanced', 
							'focus' => "*[data-r='#frame#.explode.size']"
							
						)
						
					),
					
					'random' => array(
					
						'title' => __('Random Size', 'revslider-explodinglayers-addon'),
						'helpPath' => '#frame#.explode.randomsize', 
						'keywords' => array('addon', 'addons', 'exploding', 'exploding layers', 'exploding layers addon', 'size', 'particle', 'particle size', 'random'), 
						'description' => __("Randomize the size of the particles as the Layer explodes/implodes", 'revslider-explodinglayers-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/exploding-layers-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Animation -> Advanced',
						'highlight' => array(
							
							'dependencies' => array('layerselected', '#module_layers_trigger', '#gst_layer_4', '#keyframe_list_el_frame_0 .frame_list_title', array('path' => '#slide#.layers.#layer#.timeline.frames.#frame#.explode.use', 'value' => true, 'option' => 'explodinglayers_enable')),
							'scrollTo' => '#form_animation_sframes_advanced', 
							'focus' => "*[data-r='#frame#.explode.randomsize']"
							
						)
						
					),
					
				),
				
				'additional_settings' => array(
				
					'direction' => array(
						
						'title' => __('Exploding Direction', 'revslider-explodinglayers-addon'),
						'helpPath' => '#frame#.explode.direction', 
						'keywords' => array('addon', 'addons', 'exploding', 'exploding layers', 'exploding layers addon', 'direction', 'exploding direction'), 
						'description' => __("Choose to explode/implode the Layer from the left or right", 'revslider-explodinglayers-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/exploding-layers-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Animation -> Advanced',
						'highlight' => array(
							
							'dependencies' => array('layerselected', '#module_layers_trigger', '#gst_layer_4', '#keyframe_list_el_frame_0 .frame_list_title', array('path' => '#slide#.layers.#layer#.timeline.frames.#frame#.explode.use', 'value' => true, 'option' => 'explodinglayers_enable')),
							'scrollTo' => '#form_animation_sframes_advanced', 
							'focus' => "#el_part_expldirection"
							
						)
						
					),
					
					'anti_gravity' => array(
						
						'title' => __('Anti Gravity', 'revslider-explodinglayers-addon'),
						'helpPath' => '#frame#.explode.speed', 
						'keywords' => array('addon', 'addons', 'exploding', 'exploding layers', 'exploding layers addon', 'speed', 'exploding layer speed', 'exploding layers speed'), 
						'description' => __("Adds anti-gravity to the Particles as they explode/implode, creating the yin/yang motion", 'revslider-explodinglayers-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/exploding-layers-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Animation -> Advanced',
						'highlight' => array(
							
							'dependencies' => array('layerselected', '#module_layers_trigger', '#gst_layer_4', '#keyframe_list_el_frame_0 .frame_list_title', array('path' => '#slide#.layers.#layer#.timeline.frames.#frame#.explode.use', 'value' => true, 'option' => 'explodinglayers_enable')),
							'scrollTo' => '#form_animation_sframes_advanced', 
							'focus' => "*[data-r='#frame#.explode.speed']"
							
						)
						
					),
					
					'random_speed' => array(
						
						'title' => __('Randomize Gravity', 'revslider-explodinglayers-addon'),
						'helpPath' => '#frame#.explode.randomspeed', 
						'keywords' => array('addon', 'addons', 'exploding', 'exploding layers', 'exploding layers addon', 'speed', 'exploding layer speed', 'exploding layers speed', 'random speed'), 
						'description' => __("Randomize the anti-gravity for the Exploding Layer", 'revslider-explodinglayers-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/exploding-layers-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Animation -> Advanced',
						'highlight' => array(
							
							'dependencies' => array('layerselected', '#module_layers_trigger', '#gst_layer_4', '#keyframe_list_el_frame_0 .frame_list_title', array('path' => '#slide#.layers.#layer#.timeline.frames.#frame#.explode.use', 'value' => true, 'option' => 'explodinglayers_enable')),
							'scrollTo' => '#form_animation_sframes_advanced', 
							'focus' => "*[data-r='#frame#.explode.randomspeed']"
							
						)
						
					),
					
					'density' => array(
						
						'title' => __('Particle Density', 'revslider-explodinglayers-addon'),
						'helpPath' => '#frame#.explode.density', 
						'keywords' => array('addon', 'addons', 'exploding', 'exploding layers', 'exploding layers addon', 'exploding layers density', 'density', 'particle', 'particle density'), 
						'description' => __("Controls how manu particles appear in relation to the the Layer's size", 'revslider-explodinglayers-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/exploding-layers-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Animation -> Advanced',
						'highlight' => array(
							
							'dependencies' => array('layerselected', '#module_layers_trigger', '#gst_layer_4', '#keyframe_list_el_frame_0 .frame_list_title', array('path' => '#slide#.layers.#layer#.timeline.frames.#frame#.explode.use', 'value' => true, 'option' => 'explodinglayers_enable')),
							'scrollTo' => '#form_animation_sframes_advanced', 
							'focus' => "*[data-r='#frame#.explode.density']"
							
						)
						
					),
					
					'power' => array(
						
						'title' => __('Particle Speed', 'revslider-explodinglayers-addon'),
						'helpPath' => '#frame#.explode.power', 
						'keywords' => array('addon', 'addons', 'exploding', 'exploding layers', 'exploding layers addon', 'power', 'speed'), 
						'description' => __("The speed at which the particles implode/explode", 'revslider-explodinglayers-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/exploding-layers-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Animation -> Advanced',
						'highlight' => array(
							
							'dependencies' => array('layerselected', '#module_layers_trigger', '#gst_layer_4', '#keyframe_list_el_frame_0 .frame_list_title', array('path' => '#slide#.layers.#layer#.timeline.frames.#frame#.explode.use', 'value' => true, 'option' => 'explodinglayers_enable')),
							'scrollTo' => '#form_animation_sframes_advanced', 
							'focus' => "*[data-r='#frame#.explode.power']"
							
						)
						
					),
					
					'padding' => array(
						
						'title' => __('Canvas Padding', 'revslider-explodinglayers-addon'),
						'helpPath' => '#frame#.explode.padding', 
						'keywords' => array('addon', 'addons', 'exploding', 'exploding layers', 'exploding layers addon', 'padding', 'size', 'particle padding', 'exploding layer padding', 'exploding layers padding'), 
						'description' => __("Adds space between the Layer and the bounding box for the Particles Canvas", 'revslider-explodinglayers-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/exploding-layers-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Animation -> Advanced',
						'highlight' => array(
							
							'dependencies' => array('layerselected', '#module_layers_trigger', '#gst_layer_4', '#keyframe_list_el_frame_0 .frame_list_title', array('path' => '#slide#.layers.#layer#.timeline.frames.#frame#.explode.use', 'value' => true, 'option' => 'explodinglayers_enable')),
							'scrollTo' => '#form_animation_sframes_advanced', 
							'focus' => "*[data-r='#frame#.explode.padding']"
							
						)
						
					),
					
					'sync' => array(
						
						'title' => __('Sync Helper', 'revslider-explodinglayers-addon'),
						'helpPath' => '#frame#.explode.sync', 
						'keywords' => array('addon', 'addons', 'exploding', 'exploding layers', 'exploding layers addon', 'sync', 'sync helper', 'exploding layer sync', 'exploding layers sync'), 
						'description' => __("Adds space between the Layer and the bounding box for the Particles Canvas", 'revslider-explodinglayers-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/exploding-layers-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> Animation -> Advanced',
						'highlight' => array(
							
							'dependencies' => array('layerselected', '#module_layers_trigger', '#gst_layer_4', '#keyframe_list_el_frame_0 .frame_list_title', array('path' => '#slide#.layers.#layer#.timeline.frames.#frame#.explode.use', 'value' => true, 'option' => 'explodinglayers_enable')),
							'scrollTo' => '#form_animation_sframes_advanced', 
							'focus' => "*[data-r='#frame#.explode.sync']"
							
						)
						
					)
					
				)
				
			)
			
		);
		
	}

}
	
?>