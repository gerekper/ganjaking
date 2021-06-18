<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if(!defined('ABSPATH')) exit();

class RsAddOnBase {
	
	const MINIMUM_VERSION = '6.5.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnBase::MINIMUM_VERSION, '>=')) {
		
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
			$update_admin = new RevAddOnTypewriterUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin ,'set_update_transient'));
			add_filter('plugins_api', array($update_admin,'set_updates_api_results'), 10, 3);
						
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
		}
		
		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		require_once(static::$_PluginPath . 'public/includes/slide.class.php');
		
		new RsTypewriterSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsTypewriterSlideFront(static::$_PluginTitle);
		
	}
	
	/**
	 * Load the textdomain
	 **/
	protected function _loadPluginTextDomain(){
		
		load_plugin_textdomain('rs_' . static::$_PluginTitle, false, static::$_PluginPath . 'languages/');
		
	}
	
	public function enqueue_admin_scripts($hook) {
		
		$_handle = 'rs-' . static::$_PluginTitle . '-admin';
		$_base   = static::$_PluginUrl . 'admin/assets/';
		$_jsPathMin = file_exists(static::$_PluginPath . 'admin/assets/js/revslider-' . static::$_PluginTitle . '-addon-admin.dev.js') ? '.dev' : '';
		
		if($hook === 'toplevel_page_revslider') {
			
			if(!isset($_GET['page']) || !isset($_GET['view'])) return;
			
			$page = $_GET['page'];
			if($page !== 'revslider') return;
			

			switch($page) {
				
				case 'revslider':
				
					if(isset($_GET['view'])) {						
						
						switch($_GET['view']) {
							
							case 'slide':
							
								wp_enqueue_style($_handle, $_base . 'css/revslider-typewriter-addon-admin.css', array(), static::$_Version);
								wp_enqueue_script($_handle, $_base . 'js/revslider-typewriter-addon-admin' . $_jsPathMin . '.js',array( 'jquery','revbuilder-admin' ), static::$_Version, true);
								wp_localize_script( $_handle, 'revslider_typewriter_addon', self::get_var() );
								
							break;
							
						}
						
					}
				
				break;
				
				
				
			}
			
		}
		
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-typewriter-addon') {
		
		if($slug === 'revslider-typewriter-addon'){
			
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
			$definitions['editor_settings']['layer_settings']['addons']['typewriter_addon'] = $help['layer'];
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
			'twdefaults' => __('TypeWriter Defaults','revslider-typewriter-addon'),
			'blinkingcursor' => __('Blinking Cursor','revslider-typewriter-addon'),
			'active' => __('Active','revslider-typewriter-addon'),
			'blinking' => __('Blinking','revslider-typewriter-addon'),
			'multiplelines' => __('Multiple/Sequenced Lines','revslider-typewriter-addon'),
			'twgeneral' => __('Typewriter General','revslider-typewriter-addon'),
			'worddelays' => __('Delay between Words','revslider-typewriter-addon'),
			'wordpattern' => __('Delay between Words Pattern','revslider-typewriter-addon'),
			'typespeed' => __('Typing Speed','revslider-typewriter-addon'),
			'offsetdelay' => __('Start Delay','revslider-typewriter-addon'),
			'lbdelay' => __('LineBreak Delay','revslider-typewriter-addon'),
			'twtyping' => __('Typing Behavior','revslider-typewriter-addon'),
			'blinkspeed' => __('Blinking Speed','revslider-typewriter-addon'),
			'blinkinghide' => __('Hide Cursor at End','revslider-typewriter-addon'),
			'cursor' => __('Cursor','revslider-typewriter-addon'),
			'color' => __('Cursor Color', 'revslider-typewriter-addon'),
			'blinkeffect' => __('Blinking Effect','revslider-typewriter-addon'),
			'deletion' => __('Deletion Behavior','revslider-typewriter-addon'),
			'deletionspeed' => __('Cursor Deletion Speed','revslider-typewriter-addon'),
			'deletiondelay' => __('Deletion Delay','revslider-typewriter-addon'),
			'newlinedelay' => __('New Line Delay','revslider-typewriter-addon'),
			'multilinebe' => __('Multiple/Sequenced Behavior','revslider-typewriter-addon'),
			'worddelaypattern' => __('Word Delay Pattern','revslider-typewriter-addon'),
			'addmultiple' => __('Add Typewriter Line','revslider-typewriter-addon'),
			'entertext' => __('enter text...','revslider-typewriter-addon'),
			'typewriter' => __('TypeWriter','revslider-typewriter-addon'),
			'loop' => __('Loop Lines','revslider-typewriter-addon')
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
					
					'dependency_id' => 'typewriter_enable',
					'buttonTitle' => __('Enable TypeWriter', 'revslider-typewriter-addon'),
					'title' => __('Enable', 'revslider-typewriter-addon'),
					'helpPath' => 'addOns.revslider-typewriter-addon.enable', 
					'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon'), 
					'description' => __("Enable the TypeWriter effect for the selected Layer", 'revslider-typewriter-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TypeWriter',
					'highlight' => array(
						
						'dependencies' => array('layerselected::text||button'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
						'scrollTo' => '#form_layerinner_revslider-typewriter-addon', 
						'focus' => "*[data-r='addOns.revslider-typewriter-addon.enable']"
						
					)
					
				),
				
				'blinking_effect' => array(
				
					'enable' => array(
					
						'dependency_id' => 'typewriter_blinking',
						'buttonTitle' => __('Blinking Cursor', 'revslider-typewriter-addon'),
						'title' => __('Enable', 'revslider-typewriter-addon'),
						'helpPath' => 'addOns.revslider-typewriter-addon.blinking', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'cursor', 'blink', 'blinking', 'blinking cursor'), 
						'description' => __("Enable a blinking cursor for the TypeWriter effect", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '#form_layerinner_revslider-typewriter-addon', 
							'focus' => "*[data-r='addOns.revslider-typewriter-addon.blinking']"
							
						)
						
					),
					
					'hide_cursor' => array(
					
						'title' => __('Hide Cursor at End', 'revslider-typewriter-addon'),
						'helpPath' => 'addOns.revslider-typewriter-addon.hide_cursor', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'cursor', 'blink', 'blinking', 'blinking cursor'), 
						'description' => __("Hide the blinking cursor when the typing is complete", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable'),
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.blinking', 'value' => true, 'option' => 'typewriter_blinking')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '.typewriter_layer_blinking_form', 
							'focus' => "*[data-r='addOns.revslider-typewriter-addon.hide_cursor']"
							
						)
						
					),
					
					'speed' => array(
						
						'buttonTitle' => __('Blinking Cursor Speed', 'revslider-typewriter-addon'),
						'title' => __('Blinking Speed', 'revslider-typewriter-addon'),
						'helpPath' => 'addOns.revslider-typewriter-addon.blinking_speed', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'cursor', 'blink', 'blinking', 'blinking cursor', 'blinking speed', 'speed'), 
						'description' => __("How fast/often the cursor should blink in milliseconds", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable'),
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.blinking', 'value' => true, 'option' => 'typewriter_blinking')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '.typewriter_layer_blinking_form', 
							'focus' => "*[data-r='addOns.revslider-typewriter-addon.blinking_speed']"
							
						)
						
					),
					
					'type' => array(
						
						'title' => __('Cursor Type', 'revslider-typewriter-addon'),
						'helpPath' => 'addOns.revslider-typewriter-addon.cursor_type', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'cursor', 'blink', 'blinking', 'blinking cursor', 'blinking speed', 'speed'), 
						'description' => __("Choose if the cursor should blink as an underscore or vertical bar character", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable'),
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.blinking', 'value' => true, 'option' => 'typewriter_blinking')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '.typewriter_layer_blinking_form', 
							'focus' => "*[data-r='addOns.revslider-typewriter-addon.cursor_type']"
							
						)
						
					),
										
					'color' => array(
						
						'title' => __('Cursor Color', 'revslider-typewriter-addon'),
						'helpPath' => 'addOns.revslider-typewriter-addon.color', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'cursor', 'blink', 'blinking', 'blinking cursor', 'blinking speed', 'color'), 
						'description' => __("Choose to change the cursor color, transparent will use default font color", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable'),
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.blinking', 'value' => true, 'option' => 'typewriter_blinking')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '.typewriter_layer_blinking_form', 
							'focus' => "*[data-r='addOns.revslider-typewriter-addon.cursor_type']"
							
						)
						
					)
				
				),
				
				'typing_behavior' => array(
				
					'speed' => array(
						
						'title' => __('Typing Speed', 'revslider-typewriter-addon'),
						'helpPath' => 'addOns.revslider-typewriter-addon.speed', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'speed', 'typing', 'typing speed'), 
						'description' => __("The speed at which the characters should be typed in milliseconds", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '#typewriter_behavior', 
							'focus' => "*[data-r='addOns.revslider-typewriter-addon.speed']"
							
						)
						
					),
					
					'start_delay' => array(
						
						'title' => __('Start Delay', 'revslider-typewriter-addon'),
						'helpPath' => 'addOns.revslider-typewriter-addon.start_delay', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'start delay', 'delay'), 
						'description' => __("A delay in milliseconds before the typing starts", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '#typewriter_behavior', 
							'focus' => "*[data-r='addOns.revslider-typewriter-addon.start_delay']"
							
						)
						
					),
					
					'line_break_delay' => array(
						
						'title' => __('Line Break Delay', 'revslider-typewriter-addon'),
						'helpPath' => 'addOns.revslider-typewriter-addon.linebreak_delay', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'line break delay', 'delay', 'line break'), 
						'description' => __("A delay in milliseconds before a new line is typed", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '#typewriter_behavior', 
							'focus' => "*[data-r='addOns.revslider-typewriter-addon.linebreak_delay']"
							
						)
						
					)
				
				),
				
				'multiple_lines' => array(
				
					'enable' => array(
						
						'dependency_id' => 'typewriter_sequenced',
						'buttonTitle' => __('Multiple Lines', 'revslider-typewriter-addon'),
						'title' => __('Enable', 'revslider-typewriter-addon'),
						'helpPath' => 'addOns.revslider-typewriter-addon.sequenced', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'multiple lines', 'sequenced lines'), 
						'description' => __("Enable sequenced lines for the typewriter effect.  Additional lines can be added in the Layer's content section", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '#form_layer_revslider-typewriter-addon', 
							'focus' => "*[data-r='addOns.revslider-typewriter-addon.sequenced']"
							
						)
						
					),
					
					'delete_speed' => array(
						
						'title' => __('Line Delete Speed', 'revslider-typewriter-addon'),
						'helpPath' => 'addOns.revslider-typewriter-addon.deletion_speed', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'multiple lines', 'sequenced lines', 'cursor deletion', 'cursor deletion speed'), 
						'description' => __("The speed at which each sequenced lines will be deleted in milliseconds", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable'),
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.sequenced', 'value' => true, 'option' => 'typewriter_sequenced')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '.typewriter_layer_sequenced_form', 
							'focus' => "*[data-r='addOns.revslider-typewriter-addon.deletion_speed']"
							
						)
						
					),
					
					'delete_delay' => array(
						
						'title' => __('Line Deletion Delay', 'revslider-typewriter-addon'),
						'helpPath' => 'addOns.revslider-typewriter-addon.deletion_delay', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'multiple lines', 'sequenced lines', 'deletion', 'deletion delay'), 
						'description' => __("A delay in milliseconds before the line begins to delete itself", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable'),
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.sequenced', 'value' => true, 'option' => 'typewriter_sequenced')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '.typewriter_layer_sequenced_form', 
							'focus' => "*[data-r='addOns.revslider-typewriter-addon.deletion_delay']"
							
						)
						
					),
					
					'new_line_delay' => array(
						
						'title' => __('New Line Delay', 'revslider-typewriter-addon'),
						'helpPath' => 'addOns.revslider-typewriter-addon.newline_delay', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'multiple lines', 'sequenced lines', 'new line', 'new line delay'), 
						'description' => __("A delay in milliseconds before the new line begins to type", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable'),
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.sequenced', 'value' => true, 'option' => 'typewriter_sequenced')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '.typewriter_layer_sequenced_form', 
							'focus' => "*[data-r='addOns.revslider-typewriter-addon.newline_delay']"
							
						)
						
					),
					
					'loop_lines' => array(
						
						'title' => __('Loop Lines', 'revslider-typewriter-addon'),
						'helpPath' => 'addOns.revslider-typewriter-addon.looped', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'multiple lines', 'sequenced lines', 'loop lines', 'loop'), 
						'description' => __("Replay the typing animation sequence when it finished.  Will apply to multiple lines or a single line.", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable'),
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.sequenced', 'value' => true, 'option' => 'typewriter_sequenced')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '.typewriter_layer_sequenced_form', 
							'focus' => "*[data-r='addOns.revslider-typewriter-addon.looped']"
							
						)
						
					)
				
				),
				
				'word_delays' => array(
				
					'enable' => array(
						
						'dependency_id' => 'typewriter_word_delay',
						'buttonTitle' => __('Word Delays', 'revslider-typewriter-addon'),
						'title' => __('Enable', 'revslider-typewriter-addon'),
						'helpPath' => 'addOns.revslider-typewriter-addon.word_delay', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'delay between words', 'word delay'), 
						'description' => __("Enable delays between each word as they are typed.  This helps to create a more natural typing visual.", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '#form_layer_revslider-typewriter-addon', 
							'focus' => "*[data-r='addOns.revslider-typewriter-addon.word_delay']"
							
						)
						
					),
					
					'pattern' => array(
						
						'dependency_id' => 'typewriter_word_delay_patterns',
						'title' => __('Word Delay Patterns', 'revslider-typewriter-addon'),
						'helpPath' => 'typewriter-word-delay', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'delay between words', 'word delay', 'word delay pattern'), 
						'description' => __("Add one or more word delay patterns to create a more natural typing visual", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable'),
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.word_delay', 'value' => true, 'option' => 'typewriter_word_delay')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '.typewriter_layer_worddelay_form', 
							'focus' => "*[data-helpkey='typewriter-word-delay']"
							
						)
						
					),
					
					'pattern_a' => array(
						
						'title' => __('Total Words', 'revslider-typewriter-addon'),
						'helpPath' => 'typewriter-word-delay-a', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'delay between words', 'word delay', 'word delay pattern'), 
						'description' => __("The delay will be applied after this many words are typed", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable'),
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.word_delay', 'value' => true, 'option' => 'typewriter_word_delay')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '.typewriter_layer_worddelay_form', 
							'focus' => "*[data-helpkey='typewriter-word-delay-a']"
							
						)
						
					),
					
					'pattern_b' => array(
						
						'title' => __('Delay Time', 'revslider-typewriter-addon'),
						'helpPath' => 'typewriter-word-delay-b', 
						'keywords' => array('addon', 'addons', 'typewriter', 'typewriter addon', 'delay between words', 'word delay', 'word delay pattern'), 
						'description' => __("The time in milliseconds for the word pattern delay", 'revslider-typewriter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/typewriter-addon/', 
						'video' => false,
						'section' => 'Layer Settings -> TypeWriter',
						'highlight' => array(
							
							'dependencies' => array(
							
								'layerselected::text||button',
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.enable', 'value' => true, 'option' => 'typewriter_enable'),
								array('path' => '#slide#.layers.#layer#.addOns.revslider-typewriter-addon.word_delay', 'value' => true, 'option' => 'typewriter_word_delay')
								
							), 
							'menu' => "#module_layers_trigger, #gst_layer_revslider-typewriter-addon", 
							'scrollTo' => '.typewriter_layer_worddelay_form', 
							'focus' => "*[data-helpkey='typewriter-word-delay-b']"
							
						)
						
					),
				
				)
				
			)
			
		);
		
	}

}
	
?>