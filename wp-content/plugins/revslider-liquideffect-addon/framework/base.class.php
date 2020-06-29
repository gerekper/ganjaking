<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnLiquideffectBase {
	
	const MINIMUM_VERSION = '6.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnLiquideffectBase::MINIMUM_VERSION, '>=')) {
		
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
			$update_admin = new RevAddOnLiquideffectUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'));
			add_filter('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
			// templates ajax
			add_action('revslider_do_ajax', array($this, 'do_ajax'), 10, 2);	
			
			require_once(static::$_PluginPath . 'admin/includes/slider.class.php');
			
			// admin init
			new RsLiquideffectSliderAdmin();
			
		}
		
		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		require_once(static::$_PluginPath . 'public/includes/slide.class.php');
		
		new RsLiquideffectSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsLiquideffectSlideFront(static::$_PluginTitle);
		
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
			
			wp_enqueue_script($_handle, $_base . 'js/revslider-' . static::$_PluginTitle . '-addon-admin.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			wp_localize_script($_handle, 'revslider_liquideffect_addon', self::get_var() );

		}
		
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-liquideffect-addon') {
		
		if($slug === 'revslider-liquideffect-addon'){
			
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
			$definitions['editor_settings']['slide_settings']['addons']['liquideffect_addon'] = $help['slide'];
		}
		
		return $definitions;
	
	}
	
	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public static function get_var() {
			
		$_textdomain = 'revslider-liquideffect-addon';
		return array(
			
			'custom_templates' => self::get_templates(),
			'baseurl' => RS_LIQUIDEFFECT_PLUGIN_URL . 'public/assets/images/',
			'bricks' => array(
				'distortion' => __('Distortion', $_textdomain),
				'placeholder' => __('Select', $_textdomain),
				'active' => __('Active', $_textdomain),
				'settings' => __('Distortion Effect', $_textdomain),
				'loadsettings' => __('Load Settings', $_textdomain),
				'map' => __('Distortion Map', $_textdomain),
				'library' => __('Media Library', $_textdomain),
				'animation' => __('Animation Settings', $_textdomain),
				'size' => __('Map Size', $_textdomain),
				'imagemap' => __('Image Map', $_textdomain),
				'rotation' => __('2D Rotation', $_textdomain),
				'transition' => __('Slide Transition', $_textdomain),
				'interaction' => __('User Interaction', $_textdomain),
				'mouse' => __('Mouse Event', $_textdomain),
				'easing' => __('Easing', $_textdomain),
				'duration' => __('Duration', $_textdomain),
				'mobile' => __('Disable Mobile', $_textdomain),
				'note' => __('Slide Main Background Image not set', $_textdomain),
				'bmlibrary' => __('Distortion Effect Library', $_textdomain),
				'presets' => __('Distortion Presets', $_textdomain),
				'customprests' => __('Custom Presets', $_textdomain),
				'transmessage' => __('Slide Transition values will animate to the "Animation Settings" values', $_textdomain),
				'intermessage' => __('User Interaction values will animate from the "Animation Settings" values', $_textdomain),
				
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
					
					'dependency_id' => 'liquideffect_enable',
					'buttonTitle' => __('Enable Distortion Addon', 'revslider-liquideffect-addon'), 
					'title' => __('Enable', 'revslider-liquideffect-addon'),
					'helpPath' => 'addOns.revslider-liquideffect-addon.enable', 
					'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect'), 
					'description' => __("Enable the Distortion Effect Addon for the current Slide", 'revslider-liquideffect-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Distortion',
					'highlight' => array(
					
						'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
						'scrollTo' => '#form_slide_revslider-liquideffect-addon', 
						'focus' => "#distortion_enable"
						
					)
					
				),
				
				'distortion_map' => array(
				
					'library' => array(
						
						'buttonTitle' => __('Distortion Library', 'revslider-liquideffect-addon'), 
						'title' => __('Settings Templates', 'revslider-liquideffect-addon'),
						'helpPath' => 'distortion_templates', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion library'), 
						'description' => __("Load a settings template for the Distortion effect", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable')), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_map_wrap', 
							'focus' => "#distortion_templates .presets_liste_head"
							
						)
						
					),
					
					'image_map' => array(
						
						'buttonTitle' => __('Distortion Image Map', 'revslider-liquideffect-addon'), 
						'title' => __('Image Map', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.map.image', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion map', 'distortion image'), 
						'description' => __("The overlay image which creates the distortion effect", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable')), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_map_wrap', 
							'focus' => "#distortion_map"
							
						)
						
					),
					
					'map_size' => array(
						
						'buttonTitle' => __('Distortion Map Size', 'revslider-liquideffect-addon'), 
						'title' => __('Image Map Size', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.map.size', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion map', 'distortion image', 'distortion map size'), 
						'description' => __("Use a small or large version of the selected map image for varying visuals", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable')), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_map_wrap', 
							'focus' => "#distortion_map_size"
							
						)
						
					)
					
				),
				
				'animation_settings' => array(
				
					'enable' => array(
						
						'dependency_id' => 'distortion_animation_enable',
						'buttonTitle' => __('Distortion Animation', 'revslider-liquideffect-addon'), 
						'title' => __('Autoplay Effect', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.animation.enable', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion animation', 'distortion autoplay'), 
						'description' => __("Run the effect automatically without any user-interaction", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable')), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_animation_wrap', 
							'focus' => "#distortion_animation_enable"
							
						)
						
					),
					
					'speedx' => array(
						
						'buttonTitle' => __('Distortion Speed X', 'revslider-liquideffect-addon'), 
						'title' => __('Speed X', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.animation.speedx', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion animation', 'distortion speed'), 
						'description' => __("Speed for the displacement map's left movement", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.animation.enable', 'value' => true, 'option' => 'distortion_animation_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_animation_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.animation.speedx']"
							
						)
						
					),
					
					'speedy' => array(
						
						'buttonTitle' => __('Distortion Speed Y', 'revslider-liquideffect-addon'), 
						'title' => __('Speed Y', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.animation.speedy', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion animation', 'distortion speed'), 
						'description' => __("Speed for the displacement map's top movement", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.animation.enable', 'value' => true, 'option' => 'distortion_animation_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_animation_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.animation.speedy']"
							
						)
						
					),
					
					'scalex' => array(
						
						'buttonTitle' => __('Distortion Scale X', 'revslider-liquideffect-addon'), 
						'title' => __('Scale X', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.animation.scalex', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion animation', 'distortion scale'), 
						'description' => __("Initial scaleX value for the displacement map", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.animation.enable', 'value' => true, 'option' => 'distortion_animation_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_animation_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.animation.scalex']"
							
						)
						
					),
					
					'scaley' => array(
						
						'buttonTitle' => __('Distortion Scale Y', 'revslider-liquideffect-addon'), 
						'title' => __('Scale Y', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.animation.scaley', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion animation', 'distortion scale'), 
						'description' => __("Initial scaleY value for the displacement map", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.animation.enable', 'value' => true, 'option' => 'distortion_animation_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_animation_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.animation.scaley']"
							
						)
						
					),
					
					'rotationx' => array(
						
						'buttonTitle' => __('Distortion Rotation X', 'revslider-liquideffect-addon'), 
						'title' => __('Rotation X', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.animation.rotationx', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion animation', 'distortion rotation'), 
						'description' => __("rotationX movement for the displacement map", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.animation.enable', 'value' => true, 'option' => 'distortion_animation_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_animation_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.animation.rotationx']"
							
						)
						
					),
					
					'rotationy' => array(
						
						'buttonTitle' => __('Distortion Rotation Y', 'revslider-liquideffect-addon'), 
						'title' => __('Rotation Y', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.animation.rotationy', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion animation', 'distortion rotation'), 
						'description' => __("rotationY movement for the displacement map", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.animation.enable', 'value' => true, 'option' => 'distortion_animation_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_animation_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.animation.rotationy']"
							
						)
						
					),
					
					'rotation' => array(
						
						'buttonTitle' => __('Distortion 2D Rotation', 'revslider-liquideffect-addon'), 
						'title' => __('2D Rotation', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.animation.rotation', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion animation', 'distortion rotation'), 
						'description' => __("2d rotation movement for the displacement map", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.animation.enable', 'value' => true, 'option' => 'distortion_animation_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_animation_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.animation.rotation']"
							
						)
						
					)
					
				),
				
				'slide_transition' => array(
				
					'enable' => array(
						
						'dependency_id' => 'distortion_transition_enable',
						'buttonTitle' => __('Distortion Transition', 'revslider-liquideffect-addon'), 
						'title' => __('Enable', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.transition.enable', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion transition', 'slide animation', 'slide transition'), 
						'description' => __("Run the effect automatically without any user-interaction", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable')), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_transition_wrap', 
							'focus' => "#distortion_transition_enable"
							
						)
						
					),
					
					'easing' => array(
						
						'buttonTitle' => __('Distortion Transition Easing', 'revslider-liquideffect-addon'), 
						'title' => __('Transition Easing', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.transition.easing', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion transition', 'slide animation', 'slide transition'), 
						'description' => __("The easing function applied to the transition", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.transition.enable', 'value' => true, 'option' => 'distortion_transition_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_transition_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.transition.easing']"
							
						)
						
					),
					
					'duration' => array(
						
						'buttonTitle' => __('Distortion Transition Duration', 'revslider-liquideffect-addon'), 
						'title' => __('Transition Duration', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.transition.duration', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion transition', 'slide animation', 'slide transition'), 
						'description' => __("The transition's total time in milliseconds", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.transition.enable', 'value' => true, 'option' => 'distortion_transition_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_transition_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.transition.duration']"
							
						)
						
					),
					
					'trans_cross' => array(
						
						'buttonTitle' => __('Distortion Cross Transition', 'revslider-liquideffect-addon'), 
						'title' => __('Cross Transition', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.transition.cross', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion transition', 'slide animation', 'slide transition', 'cross transition'), 
						'description' => __("Use back-to-back transitions when the Slide changes", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.transition.enable', 'value' => true, 'option' => 'distortion_transition_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_transition_wrap', 
							'focus' => "#distortion_transcross"
							
						)
						
					),
					
					'trans_power' => array(
						
						'title' => __('Enhanced Distortion', 'revslider-liquideffect-addon'), 
						'helpPath' => 'addOns.revslider-liquideffect-addon.transition.power', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion transition', 'slide animation', 'slide transition'), 
						'description' => __("Apply extra power to the transition", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.transition.enable', 'value' => true, 'option' => 'distortion_transition_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_transition_wrap', 
							'focus' => "#distortion_transpower"
							
						)
						
					),
					
					'speedx' => array(
						
						'title' => __('Speed X Offset', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.transition.speedx', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion transition', 'distortion speed'), 
						'description' => __("Animate the speedX value by this offset number", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.transition.enable', 'value' => true, 'option' => 'distortion_transition_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_transition_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.transition.speedx']"
							
						)
						
					),
					
					'speedy' => array(
						
						'title' => __('Speed Y Offset', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.transition.speedy', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion transition', 'distortion speed'), 
						'description' => __("Animate the speedY value by this offset number", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.transition.enable', 'value' => true, 'option' => 'distortion_transition_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_transition_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.transition.speedy']"
							
						)
						
					),
					
					'scalex' => array(
						
						'title' => __('Scale X Offset', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.transition.scalex', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion transition', 'distortion scale'), 
						'description' => __("Animate the scaleX value by this offset number", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.transition.enable', 'value' => true, 'option' => 'distortion_transition_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_transition_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.transition.scalex']"
							
						)
						
					),
					
					'scaley' => array(
						
						'title' => __('Scale Y Offset', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.transition.scaley', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion transition', 'distortion scale'), 
						'description' => __("Animate the scaleY value by this offset number", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.transition.enable', 'value' => true, 'option' => 'distortion_transition_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_transition_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.transition.scaley']"
							
						)
						
					),
					
					'rotationx' => array(
						
						'title' => __('Rotation X Offset', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.transition.rotationx', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion transition', 'distortion rotation'), 
						'description' => __("Animate the rotationX value by this offset number", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.transition.enable', 'value' => true, 'option' => 'distortion_transition_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_transition_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.transition.rotationx']"
							
						)
						
					),
					
					'rotationy' => array(
						 
						'title' => __('Rotation Y Offset', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.transition.rotationy', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion transition', 'distortion rotation'), 
						'description' => __("Animate the rotationY value by this offset number", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.transition.enable', 'value' => true, 'option' => 'distortion_transition_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_transition_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.transition.rotationy']"
							
						)
						
					),
					
					'rotation' => array(
						 
						'title' => __('2D Rotation Offset', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.transition.rotation', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion transition', 'distortion rotation'), 
						'description' => __("Animate the 2D rotation value by this offset number", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.transition.enable', 'value' => true, 'option' => 'distortion_transition_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_transition_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.transition.rotation']"
							
						)
						
					)
				
				),
				
				'interaction' => array(
				
					'enable' => array(
						
						'dependency_id' => 'distortion_interaction_enable',
						'buttonTitle' => __('Distortion Interaction', 'revslider-liquideffect-addon'), 
						'title' => __('Enable', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.interaction.enable', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion interaction'), 
						'description' => __("Enable mouse interation for the distortion effect", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable')), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_interaction_wrap', 
							'focus' => "#distortion_interaction_enable"
							
						)
						
					),
					
					'disable_mobile' => array(
						
						'title' => __('Mobile Interaction', 'revslider-liquideffect-addon'), 
						'helpPath' => 'addOns.revslider-liquideffect-addon.interaction.disablemobile', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion interaction', 'distortion mobile', 'mobile'), 
						'description' => __("Disable user-interaction on mobile devices", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.interaction.enable', 'value' => true, 'option' => 'distortion_interaction_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_interaction_wrap', 
							'focus' => "#distortion_disablemobile"
							
						)
						
					),
					
					'mouse_event' => array(
						
						'buttonTitle' => __('Distortion Mouse Event', 'revslider-liquideffect-addon'), 
						'title' => __('Mouse Event', 'revslider-liquideffect-addon'), 
						'helpPath' => 'addOns.revslider-liquideffect-addon.interaction.event', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion interaction', 'distortion event', 'distortion mouse event'), 
						'description' => __("Choose which mouse event should trigger the distortion movement", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.interaction.enable', 'value' => true, 'option' => 'distortion_interaction_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_interaction_wrap', 
							'focus' => "#distortion_mouse_event"
							
						)
						
					),
					
					'easing' => array(
						
						'buttonTitle' => __('Distortion Interaction Easing', 'revslider-liquideffect-addon'), 
						'title' => __('Interaction Easing', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.interaction.easing', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion interaction', 'slide animation', 'slide interaction'), 
						'description' => __("The easing function applied to the interaction movement", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.interaction.enable', 'value' => true, 'option' => 'distortion_interaction_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_interaction_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.interaction.easing']"
							
						)
						
					),
					
					'duration' => array(
						
						'buttonTitle' => __('Distortion Interaction Duration', 'revslider-liquideffect-addon'), 
						'title' => __('Interaction Duration', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.interaction.duration', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion interaction', 'slide animation', 'slide interaction'), 
						'description' => __("The mouse interaction transition time in milliseconds", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.interaction.enable', 'value' => true, 'option' => 'distortion_interaction_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_interaction_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.interaction.duration']"
							
						)
						
					),
					
					'speedx' => array(
						
						'title' => __('Speed X Offset', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.interaction.speedx', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion interaction', 'distortion speed'), 
						'description' => __("Animate the speedX value by this offset number", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.interaction.enable', 'value' => true, 'option' => 'distortion_interaction_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_interaction_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.interaction.speedx']"
							
						)
						
					),
					
					'speedy' => array(
						
						'title' => __('Speed Y Offset', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.interaction.speedy', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion interaction', 'distortion speed'), 
						'description' => __("Animate the speedY value by this offset number", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.interaction.enable', 'value' => true, 'option' => 'distortion_interaction_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_interaction_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.interaction.speedy']"
							
						)
						
					),
					
					'scalex' => array(
						
						'title' => __('Scale X Offset', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.interaction.scalex', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion interaction', 'distortion scale'), 
						'description' => __("Animate the scaleX value by this offset number", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.interaction.enable', 'value' => true, 'option' => 'distortion_interaction_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_interaction_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.interaction.scalex']"
							
						)
						
					),
					
					'scaley' => array(
						
						'title' => __('Scale Y Offset', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.interaction.scaley', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion interaction', 'distortion scale'), 
						'description' => __("Animate the scaleY value by this offset number", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.interaction.enable', 'value' => true, 'option' => 'distortion_interaction_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_interaction_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.interaction.scaley']"
							
						)
						
					),
					
					'rotation' => array(
						 
						'title' => __('2D Rotation Offset', 'revslider-liquideffect-addon'),
						'helpPath' => 'addOns.revslider-liquideffect-addon.interaction.rotation', 
						'keywords' => array('addon', 'addons', 'distortion', 'distortion addon', 'distortion effect', 'distortion interaction', 'distortion rotation'), 
						'description' => __("Animate the 2D rotation value by this offset number", 'revslider-liquideffect-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/distortion-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Distortion',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.enable', 'value' => true, 'option' => 'liquideffect_enable'),
								array('path' => '#slide#.slide.addOns.revslider-liquideffect-addon.interaction.enable', 'value' => true, 'option' => 'distortion_interaction_enable')
								
							), 
							'menu' => "#module_slide_trigger, #gst_slide_revslider-liquideffect-addon", 
							'scrollTo' => '#distortion_interaction_wrap', 
							'focus' => "*[data-r='addOns.revslider-liquideffect-addon.interaction.rotation']"
							
						)
						
					)
				
				)
		
			)
			
		);
		
	}
	
	/**
	 * Handle Ajax Calls from RevSlider core
	 *
	 * @since    2.0.0
	 */
	public function do_ajax($return = "",$action ="") {
		switch ($action) {
			case 'delete_custom_templates_revslider-liquideffect-addon':
				$return = $this->delete_template($_REQUEST["data"]);
				if($return){
					return  __('Distortion Template deleted', 'revslider-liquideffect-addon');
				}
				else{
					return  __('Distortion Template could not be deleted', 'revslider-liquideffect-addon');
				}
				break;
			case 'save_custom_templates_revslider-liquideffect-addon':
				$return = $this->save_template($_REQUEST["data"]);
				if(empty($return) || !$return){
					return  __('Distortion Template could not be saved', 'revslider-liquideffect-addon');
				} 
				else {
					return  array( 'message' => __('Distortion Template saved', 'revslider-liquideffect-addon'),
								   'data'	=> array("id" => $return)
					);	
				}
				break;
			default:
				return $return;
				break;
		}
	}
	
	/**
	 * Read Custom Templates from WP option, false if not set
	 *
	 * @since    2.0.0
	 */
	private static function get_templates(){
		//load WP option
		$custom = get_option('revslider_addon_liquideffect_templates',false);

		//check for templates saved before 6.0
		if(!isset($custom[1]["title"])){

			//save new array into WP option
			update_option('revslider_addon_liquideffect_templates',$custom);
		}

		return $custom;
	}
	
	/**
	 * Save Custom Template
	 *
	 * @since    2.0.0
	 */
	private function save_template($template){		
		//load already saved templates
		$custom = $this->get_templates();
		
		//empty custom templates?
		if(!$custom && !is_array($custom)){
			$custom = array();
			$new_id = 1;
		}
		else{
			//custom templates exist
			if(isset($template["id"]) && is_numeric($template["id"]) ){
				//id exists , overwrite
				$new_id = $template["id"];
			}
			else{
				//id does not exist , new template
				$new_id = max(array_keys($custom))+1;
			}
		}
		
		//update or insert template
		$custom[$new_id]["title"] = $template["obj"]["title"];
		$custom[$new_id]["preset"] = $template["obj"]["preset"];
		if(update_option( 'revslider_addon_liquideffect_templates', $custom )){
			//return the ID the template was saved with
			return $new_id;	
		}
		else {
			//updating failed, blank result set
			return "";
		}
	
	}

	/**
	 * Delete Custom Template
	 *
	 * @since    2.0.0
	 */
	private function delete_template($template){
		//load templates array
		$custom = $this->get_templates();
		
		//custom template exist
		if(isset($template["id"]) && is_numeric($template["id"]) ){
			//delete given ID
			$delete_id = $template["id"];
			unset($custom[$delete_id]);
			//save the resulting templates array again
			if(update_option( 'revslider_addon_liquideffect_templates', $custom )){
				return true;	
			}
			else {
				return false;
			}
		}
	}

}
	
?>