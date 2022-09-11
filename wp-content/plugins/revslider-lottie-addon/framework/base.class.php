<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnLottieBase {
	
	const MINIMUM_VERSION = '6.5.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnLottieBase::MINIMUM_VERSION, '>=')) {
		
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
			require_once(static::$_PluginPath . 'admin/includes/layers.class.php');
			$update_admin = new RevAddOnLottieUpdate(static::$_Version);
			$layers = new RsAddOnLottieLayers();

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
		
		new RsLottieSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsLottieSlideFront(static::$_PluginTitle);
		
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
			wp_enqueue_script($_handle . "rslottie", static::$_PluginUrl . 'public/assets/js/lottie.min.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			wp_enqueue_script($_handle, $_base . 'js/revslider-' . static::$_PluginTitle . '-addon-admin' . $_jsPathMin . '.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			wp_localize_script($_handle, 'revslider_lottie_addon', self::get_var() );

		}
		
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-lottie-addon') {
		
		if($slug === 'revslider-lottie-addon'){
			
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
			$definitions['editor_settings']['layer_settings']['addons']['lottie_addon'] = $help['layer'];
		}
		
		return $definitions;
	
	}
	
	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public static function get_var() {
			
		$_textdomain = 'revslider-lottie-addon';
		return array(
		
			'bricks' => array(
				'lottie' => __('Lottie', $_textdomain),				
				'config' => __('Lottie Settings', $_textdomain),
				'jsonUrl' => __('Lottie url', $_textdomain),
				'duration' => __('Duration', $_textdomain),
				'autoplay' => __('Autoplay', $_textdomain),
				'respectTlStart' => __('Respect Timeline Start', $_textdomain),
				'endlessLoop' => __('Endless Loop', $_textdomain),
				'reverse' => __('Reverse', $_textdomain),
				'repeat' => __('Repeat', $_textdomain),

				'objectLibrary' => __('Select From Library', $_textdomain),
				'customlottie' => __('Custom Lottie File', $_textdomain),
				'savelottiefile' => __('Import Lottie File', $_textdomain),
				'lottieimport' => __('Lottie Files Import', $_textdomain),
				'importfiles' => __('Import Lottie Files', $_textdomain),
				'uploadfirstitem' => __('Import your First Item', $_textdomain),				
				'missinglottiefiles' => __('Missing an icon? Buy the full set at <a target="_blank" href="lottifiles.com">lottiefiles.com</a>', $_textdomain),
				'missinglordicon' => __('Missing an icon? Buy the full set at <a target="_blank" href="https://lordicon.com">lordicon.com</a>', $_textdomain),
				'lordiconsBIG' => __('LORDICONS', $_textdomain),									
				'lottiefilesBIG' => __('LOTTIEFILES', $_textdomain),
				'comingsoon' => __('Integration Coming Soon', $_textdomain),
				'playLottie' => __('Play Lottie', $_textdomain),
				'pauseLottie' => __('Pause Lottie', $_textdomain),
				'restartLottie' => __('Restart Lottie', $_textdomain),

				'interactivity' => __('Interactivity', $_textdomain),
				'interaction' => __('Interaction', $_textdomain),				
				'disabled' => __('Disabled', $_textdomain),
				'click' => __('Click', $_textdomain),
				'hover' => __('Hover', $_textdomain),
				'morph' => __('Morph', $_textdomain),
				
				'mousemove' => __('Mouse Move', $_textdomain),
				'lerp' => __('Ease Speed', $_textdomain),
				'continue' => __('Continue Playing', $_textdomain),

				'scroll' => __('Scroll', $_textdomain),
				'addAction' => __('Add Action', $_textdomain),
				'scrollActions' => __('Scroll Actions', $_textdomain),
				'seek' => __('Seek', $_textdomain),
				'loop' => __('Loop', $_textdomain),
				'stop' => __('Stop', $_textdomain),
				'frames' => __('Frames', $_textdomain),
				'progress' => __('Progress', $_textdomain),

				'renderer' => __('Renderer Settings', $_textdomain),
				'type' => __('Renderer', $_textdomain),
				'maxdpr' => __('Max DPR', $_textdomain),
				'size' => __('Lottie Size', $_textdomain),
				'progressiveLoad' => __('Progressive Load', $_textdomain),
				'hideTransparent' => __('Hide on transparent', $_textdomain),	
				
				'editor' => __('Lottie Editor', $_textdomain),
				'restoreStyle' => __('Restore Original Style', $_textdomain),
				'bigLottieWarning' => __('is too large file size, editing it may slowdown page loading time.', $_textdomain),
				'lottieFieldsMissing' => __('No editable fields found in this file. This file could be using images to build animation. Avoid using such files as they are not resizable, and are too heavy.', $_textdomain),
				'groupedColors' => __('Grouped Colors'),
				'groupedStrokes' => __('Grouped Strokes'),
				'editLayers' => __('Edit Layers'),
				'editGradient' => __('Edit Gradient'),
				'editorActive' => __('Style Editing Active'),
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
				
				'json_url' => array(
					
					'buttonTitle' => __('Lottie url', 'revslider-lottie-addon'), 
					'title' => __('Lottie Url', 'revslider-lottie-addon'),
					'helpPath' => 'addOns.revslider-lottie-addon.config.jsonUrl', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie files', 'url'), 
					'description' => __("Add JSON file url of lottie animation from trusted sources to include lottie animation in sliders", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-r='addOns.revslider-lottie-addon.config.jsonUrl']"
						
					)
					
				),

				'lottie_object_library' => array(
					
					'buttonTitle' => __('Lottie Object Library', 'revslider-lottie-addon'), 
					'title' => __('Object Library', 'revslider-lottie-addon'),
					'helpPath' => 'lottie_object_library', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie files', 'Object Library'), 
					'description' => __("Add JSON file url from Object Library", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '*[data-helpkey=lottie_object_library]', 
						'hover' => "*[data-helpkey=lottie_object_library]"
						
					)
					
				),
				
				'duration' => array(
					
					'buttonTitle' => __('Lottie Duration', 'revslider-lottie-addon'), 
					'title' => __('Duration', 'revslider-lottie-addon'),
					'helpPath' => 'addOns.revslider-lottie-addon.config.duration', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'time', 'duration'), 
					'description' => __("Change duration of lottie animation", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-r='addOns.revslider-lottie-addon.config.duration']"
						
					)
					
				),

				'autoplay' => array(
					
					'buttonTitle' => __('Lottie Autoplay', 'revslider-lottie-addon'), 
					'title' => __('Autoplay', 'revslider-lottie-addon'),
					'helpPath' => 'addOns.revslider-lottie-addon.config.autoplay', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'autoplay'), 
					'description' => __("Enabling this option will autoplay the lottie animation. Disable autoplay to trigger animation from layer actions", 'revslider-lottie-addon'),
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-r='addOns.revslider-lottie-addon.config.autoplay']"
						
					)
					
				),

				'respectTlStart' => array(
					
					'buttonTitle' => __('Lottie Respect Timeline Start', 'revslider-lottie-addon'), 
					'title' => __('Respect Timeline Start', 'revslider-lottie-addon'),
					'helpPath' => 'addOns.revslider-lottie-addon.config.respectTlStart', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'respect timeline start', 'timeline start'), 
					'description' => __("Enabling this option will prevent lottie animation from starting early and animation will only start playing when 'in animation' completes. For example, autoplay starts playing lottie animation as soon as layer enters the stage. In case opacity of layer is being animated lottie animation will start before user can see lottie layer.", 'revslider-lottie-addon'),
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-r='addOns.revslider-lottie-addon.config.respectTlStart']"
						
					)
					
				),
				
				'endless_loop' => array(
					
					'buttonTitle' => __('Lottie Endless Loop', 'revslider-lottie-addon'), 
					'title' => __('Endless Loop', 'revslider-lottie-addon'),
					'helpPath' => 'addOns.revslider-lottie-addon.config.endlessLoop', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'repeat', 'loop'), 
					'description' => __("Turn on this option to have endless loop on lottie layer or to control repetitions", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-r='addOns.revslider-lottie-addon.config.endlessLoop']"
						
					)
					
				),
				
				'reverse' => array(
					
					'buttonTitle' => __('Lottie Reverse', 'revslider-lottie-addon'), 
					'title' => __('Reverse', 'revslider-lottie-addon'),
					'helpPath' => 'addOns.revslider-lottie-addon.config.reverse', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie play', 'lottie reverse'), 
					'description' => __("Setting this option to true will change playback to yoyo, animation will play forward and then backward", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-r='addOns.revslider-lottie-addon.config.reverse']"
						
					)
					
				),
				
				'repeat' => array(
					
					'buttonTitle' => __('Lottie Repeat', 'revslider-lottie-addon'), 
					'title' => __('Repeat', 'revslider-lottie-addon'),
					'helpPath' => 'addOns.revslider-lottie-addon.config.repeat', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie repeat'), 
					'description' => __("Vertical buffer in pixels to help keep the bubble from bleeding outside the Slider", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-r='addOns.revslider-lottie-addon.config.repeat']"
						
					)
					
				),
				
				'display_frame' => array(
					
					'buttonTitle' => __('Lottie Display Frame', 'revslider-lottie-addon'), 
					'title' => __('Display Frame', 'revslider-lottie-addon'),
					'helpPath' => 'lottie_display_frame', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie display frame'), 
					'description' => __("Backend only option: set which frame should be displayed in backend to help visually while editing", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon #display_frame_wrap', 
						'hover' => "#display_frame_wrap"
						
					)
					
				),
				
				'type' => array(
					
					'buttonTitle' => __('Lottie Interaction Type', 'revslider-lottie-addon'), 
					'title' => __('Interaction Type', 'revslider-lottie-addon'),
					'helpPath' => 'addOns.revslider-lottie-addon.interaction.type', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie interaction', 'interaction type'), 
					'description' => __("Set the type of interaction on this layer to control animation playback. <br /> <ul><li><b>Click: </b>Click interaction uses settings 'endless loop', 'repeat' and 'reverse' settings and plays animation accordingly.</li><li><b>Hover: </b>Like click animation will start playing on mouse enter. If endless animation is disabled, after animation completes and on hovering over layer animation will play again.</li><li><b>Morph: </b>Morph option plays animation only once on mouse enter and reverses on mouse leave</li><li><b>Mouse Move: </b>In Mouse Move, animation will respond to mouse position.</li><li><b>Scroll: </b>With Scroll, cool interactive scrolling animations can be created using seek, loop and stop actions</li></ul>", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-r='addOns.revslider-lottie-addon.interaction.type']"
						
					)
					
				),

				'mousemove_ease_speed' => array(
					
					'buttonTitle' => __('Lottie Mousemove Ease Speed', 'revslider-lottie-addon'), 
					'title' => __('Mousemove Ease Speed', 'revslider-lottie-addon'),
					'helpPath' => 'addOns.revslider-lottie-addon.interaction.lerp', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie interaction mousemove', 'lottie mousemove ease'), 
					'description' => __("Set ease speed to smoothly transition between frames as mouse moves on layer", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-r='addOns.revslider-lottie-addon.interaction.lerp']"
						
					)
					
				),

				'mousemove_continue_playing' => array(
					
					'buttonTitle' => __('Lottie Mousemove Continue Playing', 'revslider-lottie-addon'), 
					'title' => __('Mousemove Continue Playing', 'revslider-lottie-addon'),
					'helpPath' => 'addOns.revslider-lottie-addon.interaction.continue', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie mousemove continue playing', 'lottie mousemove continue'), 
					'description' => __("Continue playing animation when mouse moves out from layer", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-r='addOns.revslider-lottie-addon.interaction.continue']"
						
					)
					
				),

				'seek_button' => array(
					
					'buttonTitle' => __('Lottie Scroll Seek', 'revslider-lottie-addon'), 
					'title' => __('Scroll Seek', 'revslider-lottie-addon'),
					'helpPath' => 'lottie_seekButton',
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie scroll', 'lottie scroll seek'), 
					'description' => __("Add 'scroll seek' action to playback animation between frames as page is scrolled back and forward", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#layer#.layer.addOns.revslider-particles-addon.interaction.type', 'value' => 'scroll')),
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon .lottie_scroll_button_seek', 
						'hover' => ".lottie_scroll_button_seek"
						
					)
					
				),

				'loop_button' => array(
					
					'buttonTitle' => __('Lottie Scroll Loop', 'revslider-lottie-addon'), 
					'title' => __('Scroll Loop', 'revslider-lottie-addon'),
					'helpPath' => 'lottie_loopButton',
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie scroll', 'lottie scroll loop'), 
					'description' => __("Add 'scroll loop' action to loop animation between frames in certain scroll area", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#layer#.layer.addOns.revslider-particles-addon.interaction.type', 'value' => 'scroll')),
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon .lottie_scroll_button_loop', 
						'hover' => ".lottie_scroll_button_loop"
						
					)
					
				),

				'stop_button' => array(
					
					'buttonTitle' => __('Lottie Scroll Stop', 'revslider-lottie-addon'), 
					'title' => __('Scroll Stop', 'revslider-lottie-addon'),
					'helpPath' => 'lottie_stopButton',
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie scroll', 'lottie scroll stop'), 
					'description' => __("Add 'scroll stop' action to stop animation on frame in certain scroll area", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#layer#.layer.addOns.revslider-particles-addon.interaction.type', 'value' => 'scroll')),
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon .lottie_scroll_button_stop', 
						'hover' => ".lottie_scroll_button_stop"
						
					)
					
				),

				'scrollLerp' => array(
					
					'buttonTitle' => __('Lottie Scroll Ease Speed', 'revslider-lottie-addon'), 
					'title' => __('Scroll Ease Speed', 'revslider-lottie-addon'),
					'helpPath' => 'addOns.revslider-lottie-addon.interaction.scrollLerp', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie scroll ease', 'lottie ease speed'), 
					'description' => __("Set ease speed to smoothly transition between frames as page is scrolled", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#layer#.layer.addOns.revslider-particles-addon.interaction.type', 'value' => 'scroll')),
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon',
						'focus' => "*[data-r='addOns.revslider-lottie-addon.interaction.scrollLerp']"
						
					)
					
				),

				'lottie_scroll_frame_start' => array(
					
					'buttonTitle' => __('Lottie Action Start Frame', 'revslider-lottie-addon'), 
					'title' => __('Action Frame Start', 'revslider-lottie-addon'),
					'helpPath' => 'lottie_scroll_frame_start', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie scroll frame', 'lottie frame start'), 
					'description' => __("Set start frame from where animation should begin for scroll action, in case of stop action set at which frame animation should stop", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#layer#.layer.addOns.revslider-particles-addon.interaction.type', 'value' => 'scroll')),
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon',
						'focus' => ".lottie_frame.frameStart"
						
					)
					
				),

				'lottie_scroll_frame_end' => array(
					
					'buttonTitle' => __('Lottie Action End Frame', 'revslider-lottie-addon'), 
					'title' => __('Action End Frame', 'revslider-lottie-addon'),
					'helpPath' => 'lottie_scroll_frame_end', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie scroll frame', 'lottie frame end'), 
					'description' => __("Set start frame from where animation should end for scroll action", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#layer#.layer.addOns.revslider-particles-addon.interaction.type', 'value' => 'scroll')),
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon',
						'focus' => ".lottie_frame.frameEnd"
						
					)
					
				),

				'lottie_scroll_progress_start' => array(
					
					'buttonTitle' => __('Lottie Scroll Progress Start', 'revslider-lottie-addon'), 
					'title' => __('Scroll Progress Start', 'revslider-lottie-addon'),
					'helpPath' => 'lottie_scroll_progress_start', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie scroll progress', 'lottie progress start'), 
					'description' => __("Set start value for progress between 0-1, from where scroll action progress should begin.", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#layer#.layer.addOns.revslider-particles-addon.interaction.type', 'value' => 'scroll')),
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon',
						'focus' => ".lottie_progress.progressStart"
						
					)
					
				),

				'lottie_scroll_progress_end' => array(
					
					'buttonTitle' => __('Lottie Scroll Progress End', 'revslider-lottie-addon'), 
					'title' => __('Scroll Progress End', 'revslider-lottie-addon'),
					'helpPath' => 'lottie_scroll_progress_end', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie scroll progress', 'lottie progress end'), 
					'description' => __("Set end value for progress between 0-1, from where scroll action progress should end", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#layer#.layer.addOns.revslider-particles-addon.interaction.type', 'value' => 'scroll')),
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon',
						'focus' => ".lottie_progress.progressEnd"
						
					)
					
				),

				'renderer_type' => array(
					
					'buttonTitle' => __('Lottie Renderer Type', 'revslider-lottie-addon'), 
					'title' => __('Renderer Type', 'revslider-lottie-addon'),
					'helpPath' => 'addOns.revslider-lottie-addon.renderer.type', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie renderer', 'lottie renderer type'), 
					'description' => __("Set animation renderer. Default is SVG, which successfully renders most animations. Canvas sometimes fails to render layer correctly but in most cases gives a lot better performance", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-r='addOns.revslider-lottie-addon.renderer.type']"
						
					)
					
				),

				'renderer_maxdpr' => array(
					
					'buttonTitle' => __('Lottie Max DPR', 'revslider-lottie-addon'), 
					'title' => __('Max DPR', 'revslider-lottie-addon'),
					'helpPath' => 'addOns.revslider-lottie-addon.renderer.maxdpr', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie dpr', 'lottie max dpr'), 
					'description' => __("Sets maximum device pixel ratio setting for canvas renderer. Set value to low number to improve performance on high DPI devices by reducing quality.", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-r='addOns.revslider-lottie-addon.renderer.maxdpr']"
						
					)
					
				),

				'renderer_size' => array(
					
					'buttonTitle' => __('Lottie Layer Contain', 'revslider-lottie-addon'), 
					'title' => __('Layer Contain', 'revslider-lottie-addon'),
					'helpPath' => 'addOns.revslider-lottie-addon.renderer.size', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie renderer', 'lottie renderer contain'), 
					'description' => __("Setting this option to contain will fit animation inside layer. Setting it to cover will cover entire layer but parts of animation may get clipped out of layer", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-r='addOns.revslider-lottie-addon.renderer.size']"
						
					)
					
				),

				'renderer_progressive_load' => array(
					
					'buttonTitle' => __('Lottie Progressive Load', 'revslider-lottie-addon'), 
					'title' => __('Progressive Load', 'revslider-lottie-addon'),
					'helpPath' => 'addOns.revslider-lottie-addon.renderer.progressiveLoad', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie renderer load', 'lottie renderer progressive load'), 
					'description' => __("Enabling this option can be helpful in cases where large lottie files are used with SVG renderer. Renderer will try to add only elements that are required at beginning to improve loading.", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-r='addOns.revslider-lottie-addon.renderer.progressiveLoad']"
						
					)
					
				),

				
				'editor_enable' => array(
					
					'buttonTitle' => __('Lottie Enable Editor', 'revslider-lottie-addon'), 
					'title' => __('Enable Editor', 'revslider-lottie-addon'),
					'helpPath' => 'addOns.revslider-lottie-addon.editor.enabled', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie editor', 'lottie editor enable'), 
					'description' => __("Enable editor to change color, stroke size of lottie shapes. Gradient Position can also be edited.", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-r='addOns.revslider-lottie-addon.editor.enabled']"
						
					)
					
				),
				
				'lottie_restore_style' => array(
					
					'buttonTitle' => __('Lottie Restore Original Style', 'revslider-lottie-addon'), 
					'title' => __('Restore Original Style', 'revslider-lottie-addon'),
					'helpPath' => 'lottie_restore_style', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'restore', 'restore style'), 
					'description' => __("Restore original style of lottie file", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'hover' => "*[data-helpkey='lottie_restore_style']"
						
					)
					
				),

				'lottie_grouped_colors' => array(
					
					'buttonTitle' => __('Lottie Edit Grouped Colors', 'revslider-lottie-addon'), 
					'title' => __('Edit Grouped Colors', 'revslider-lottie-addon'),
					'helpPath' => 'lottie_grouped_colors', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie editor edit', 'lottie edit all colors'), 
					'description' => __("Editing one color will update that color in all layers automatically", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'hover' => "*[data-helpkey='lottie_grouped_colors']"
						
					)
					
				),

				'lottie_grouped_strokes' => array(
					
					'buttonTitle' => __('Lottie Edit Grouped Strokes', 'revslider-lottie-addon'), 
					'title' => __('Edit Grouped Strokes', 'revslider-lottie-addon'),
					'helpPath' => 'lottie_grouped_strokes', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie editor edit', 'lottie edit all strokes'), 
					'description' => __("Editing one stroke will update all strokes with similar values", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'hover' => "*[data-helpkey='lottie_grouped_strokes']"
						
					)
					
				),

				'lottie_edit_layers' => array(
					
					'buttonTitle' => __('Lottie Edit Layers', 'revslider-lottie-addon'), 
					'title' => __('Edit Layers', 'revslider-lottie-addon'),
					'helpPath' => 'lottie_edit_layers', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie editor edit', 'lottie edit all colors'), 
					'description' => __("Edit individual color for each layer manually, a really handy feature to edit small lottie files.", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'hover' => "*[data-helpkey='lottie_edit_layers']"
						
					)
					
				),

				'lottie_edit_stroke' => array(
					
					'buttonTitle' => __('Lottie Edit Stroke', 'revslider-lottie-addon'), 
					'title' => __('Edit Stroke', 'revslider-lottie-addon'),
					'helpPath' => 'lottie_edit_stroke', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie editor stroke', 'lottie edit stroke'), 
					'description' => __("Set stroke size, stroke size will depend on lottie file's internal dimensions", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-helpkey='lottie_edit_stroke']"
						
					)
					
				),

				'lottie_edit_color' => array(
					
					'buttonTitle' => __('Lottie Edit Color', 'revslider-lottie-addon'), 
					'title' => __('Edit Color', 'revslider-lottie-addon'),
					'helpPath' => 'lottie_edit_color', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie editor color', 'lottie edit color'), 
					'description' => __("Change color of field, this also supports switching between solid and gradient colors", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-helpkey='lottie_edit_color']"
						
					)
					
				),

				'lottie_edit_gradient' => array(
					
					'buttonTitle' => __('Lottie Edit Gradient', 'revslider-lottie-addon'), 
					'title' => __('Edit Gradient', 'revslider-lottie-addon'),
					'helpPath' => 'lottie_edit_gradient', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie editor color', 'lottie edit gradient'), 
					'description' => __("Change gradient start and end position and see changes on leave mouse. Note: Not all fields may show up correctly in gradient editor due to complex lottie file structure.", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-helpkey='lottie_edit_gradient']"
						
					)
					
				),

				'edit_grouped_colors' => array(
					
					'buttonTitle' => __('Lottie Edit Grouped Colors', 'revslider-lottie-addon'), 
					'title' => __('Edit Grouped Colors', 'revslider-lottie-addon'),
					'helpPath' => 'edit_grouped_colors', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie editor color', 'lottie edit colors'), 
					'description' => __("Update single color on all layers automatically in one place", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-helpkey='edit_grouped_colors']"
						
					)
					
				),

				'edit_grouped_strokes' => array(
					
					'buttonTitle' => __('Lottie Edit Grouped Strokes', 'revslider-lottie-addon'), 
					'title' => __('Edit Grouped Strokes', 'revslider-lottie-addon'),
					'helpPath' => 'edit_grouped_strokes', 
					'keywords' => array('addon', 'addons', 'lottie', 'lottie addon', 'lottie editor stroke', 'lottie edit strokes'), 
					'description' => __("Update single stroke on all layers automatically in one place", 'revslider-lottie-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/lottie-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Lottie',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{lottie}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-lottie-addon", 
						'scrollTo' => '#form_layerinner_revslider-lottie-addon', 
						'focus' => "*[data-helpkey='edit_grouped_strokes']"
						
					)
					
				),
				
			)
			
		);
		
	}

}
	
?>