<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnRevealerBase {
	
	const MINIMUM_VERSION = '6.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnRevealerBase::MINIMUM_VERSION, '>=')) {
		
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
			$update_admin = new RevAddOnRevealerUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'));
			add_filter('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
		}
		
		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		
		new RsRevealerSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		
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
			$_textdomain = 'rs_' . static::$_PluginTitle;
			
			wp_enqueue_style($_handle, static::$_PluginUrl . 'public/assets/css/revolution.addon.revealer.preloaders.css', array(), static::$_Version);
			wp_enqueue_script($_handle, $_base . 'js/revslider-' . static::$_PluginTitle . '-addon-admin.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			wp_localize_script($_handle, 'revslider_revealer_addon', self::get_var() );
		}
		
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-revealer-addon') {
		
		if($slug === 'revslider-revealer-addon'){
			
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
			$definitions['editor_settings']['slider_settings']['addons']['revealer_addon'] = $help['slider'];
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
				'revealer' => __('Reveal', 'revslider-revealer-addon'),
				'reveal_settings' => __('Reveal Settings', 'revslider-revealer-addon'),
				'reveal_color' => __('Reveal Color', 'revslider-revealer-addon'),
				'active' => __('Active', 'revslider-revealer-addon'),
				'opening_reveal' => __('Reveal Type', 'revslider-revealer-addon'),
				'none' => __('None', 'revslider-revealer-addon'),
				'open_horizontal' => __('Open Horizontal', 'revslider-revealer-addon'),
				'open_vertical' => __('Open Vertical', 'revslider-revealer-addon'),
				'split_left_corner' => __('Open Left Corner', 'revslider-revealer-addon'),
				'split_right_corner' => __('Open Right Corner', 'revslider-revealer-addon'),
				'shrink_circle' => __('Shrink Circle', 'revslider-revealer-addon'),
				'expand_circle' => __('Expand Circle', 'revslider-revealer-addon'),
				'left_to_right' => __('Left to Right', 'revslider-revealer-addon'),
				'right_to_left' => __('Right to Left', 'revslider-revealer-addon'),
				'top_to_bottom' => __('Top to Bottom', 'revslider-revealer-addon'),
				'bottom_to_top' => __('Bottom to Top', 'revslider-revealer-addon'),
				'tlbr_skew' => __('Top Left to Bottom Right', 'revslider-revealer-addon'),
				'trbl_skew' => __('Top Right to Bottom Left', 'revslider-revealer-addon'),
				'bltr_skew' => __('Bottom Left to Top Right', 'revslider-revealer-addon'),
				'brtl_skew' => __('Bottom Right to Top Left', 'revslider-revealer-addon'),
				'reveal_easing' => __('Reveal Ease', 'revslider-revealer-addon'),
				'reveal_duration' => __('Reveal Duration', 'revslider-revealer-addon'),
				'enable_overlay' => __('Use Overlay', 'revslider-revealer-addon'),
				'overlay_color' => __('Overlay Color', 'revslider-revealer-addon'),
				'overlay_easing' => __('Overlay Ease', 'revslider-revealer-addon'),
				'spinner_settings' => __('Spinner Settings', 'revslider-revealer-addon'),
				'defaults' => __('Default', 'revslider-revealer-addon'),					
				'spinner' => __('Spinner', 'revslider-revealer-addon'),
				'spinner_color' => __('Spinner Color', 'revslider-revealer-addon'),
				'placeholder_select' => __('Select From List', 'revslider-revealer-addon')
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
			
			'slider' => array(
			
				'type' => array(
					
					'dependency_id' => 'reveal_type',
					'buttonTitle' => __('Revealer Addon Type', 'revslider-revealer-addon'), 
					'title' => __('Reveal Type', 'revslider-revealer-addon'),
					'helpPath' => 'addOns.revslider-revealer-addon.direction', 
					'keywords' => array('addon', 'addons', 'reveal', 'reveal addon', 'revealer', 'revealer addon', 'reveal type'), 
					'description' => __("Choose how the Slider should be revealed", 'revslider-revealer-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/reveal-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Reveal',
					'highlight' => array(
					
						'menu' => "#module_settings_trigger, #gst_sl_revslider-revealer-addon", 
						'scrollTo' => '#form_slidergeneral_revslider-revealer-addon', 
						'focus' => "#revealer_direction"
						
					)
					
				),
				
				'color' => array(
					
					'buttonTitle' => __('Revealer Addon Color', 'revslider-revealer-addon'), 
					'title' => __('Reveal Color', 'revslider-revealer-addon'),
					'helpPath' => 'addOns.revslider-revealer-addon.color', 
					'keywords' => array('addon', 'addons', 'reveal', 'reveal addon', 'revealer', 'revealer addon', 'reveal color', 'color'), 
					'description' => __("The main color for the chosen reveal effect", 'revslider-revealer-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/reveal-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Reveal',
					'highlight' => array(
						
						'dependencies' => array(array('path' => 'settings.addOns.revslider-revealer-addon.direction', 'value' => 'open_horizontal::open_vertical::split_left_corner::split_right_corner::shrink_circle::left_to_right::right_to_left::top_to_bottom::bottom_to_top::tlbr_skew::trbl_skew::bltr_skew::brtl_skew', 'option' => 'reveal_type')),
						'menu' => "#module_settings_trigger, #gst_sl_revslider-revealer-addon", 
						'scrollTo' => '#form_slidergeneral_revslider-revealer-addon', 
						'focus' => "#revealer_color"
						
					)
					
				),
				
				'easing' => array(
					
					'buttonTitle' => __('Revealer Addon Easing', 'revslider-revealer-addon'), 
					'title' => __('Reveal Easing', 'revslider-revealer-addon'),
					'helpPath' => 'addOns.revslider-revealer-addon.easing', 
					'keywords' => array('addon', 'addons', 'reveal', 'reveal addon', 'revealer', 'revealer addon', 'reveal easing', 'easing'), 
					'description' => __("The easing equation to use for the reveal effect", 'revslider-revealer-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/reveal-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Reveal',
					'highlight' => array(
						
						'dependencies' => array(array('path' => 'settings.addOns.revslider-revealer-addon.direction', 'value' => 'open_horizontal::open_vertical::split_left_corner::split_right_corner::shrink_circle::left_to_right::right_to_left::top_to_bottom::bottom_to_top::tlbr_skew::trbl_skew::bltr_skew::brtl_skew::expand_circle', 'option' => 'reveal_type')),
						'menu' => "#module_settings_trigger, #gst_sl_revslider-revealer-addon", 
						'scrollTo' => '#form_slidergeneral_revslider-revealer-addon', 
						'focus' => "*[data-r='addOns.revslider-revealer-addon.easing']"
						
					)
					
				),
				
				'duration' => array(
					
					'buttonTitle' => __('Revealer Addon Duration', 'revslider-revealer-addon'), 
					'title' => __('Reveal Duration', 'revslider-revealer-addon'),
					'helpPath' => 'addOns.revslider-revealer-addon.duration', 
					'keywords' => array('addon', 'addons', 'reveal', 'reveal addon', 'revealer', 'revealer addon', 'reveal duration', 'duration'), 
					'description' => __("The total time the reveal effect should occur in milliseconds", 'revslider-revealer-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/reveal-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Reveal',
					'highlight' => array(
						
						'dependencies' => array(array('path' => 'settings.addOns.revslider-revealer-addon.direction', 'value' => 'open_horizontal::open_vertical::split_left_corner::split_right_corner::shrink_circle::left_to_right::right_to_left::top_to_bottom::bottom_to_top::tlbr_skew::trbl_skew::bltr_skew::brtl_skew::expand_circle', 'option' => 'reveal_type')),
						'menu' => "#module_settings_trigger, #gst_sl_revslider-revealer-addon", 
						'scrollTo' => '#form_slidergeneral_revslider-revealer-addon', 
						'focus' => "#revealer_duration"
						
					)
					
				),
				
				'delay' => array(
					
					'buttonTitle' => __('Revealer Addon Delay', 'revslider-revealer-addon'), 
					'title' => __('Reveal Delay', 'revslider-revealer-addon'),
					'helpPath' => 'addOns.revslider-revealer-addon.delay', 
					'keywords' => array('addon', 'addons', 'reveal', 'reveal addon', 'revealer', 'revealer addon', 'reveal delay', 'delay'), 
					'description' => __("A delay in milliseconds before the reveal effect should start", 'revslider-revealer-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/reveal-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Reveal',
					'highlight' => array(
						
						'dependencies' => array(array('path' => 'settings.addOns.revslider-revealer-addon.direction', 'value' => 'open_horizontal::open_vertical::split_left_corner::split_right_corner::shrink_circle::left_to_right::right_to_left::top_to_bottom::bottom_to_top::tlbr_skew::trbl_skew::bltr_skew::brtl_skew::expand_circle', 'option' => 'reveal_type')),
						'menu' => "#module_settings_trigger, #gst_sl_revslider-revealer-addon", 
						'scrollTo' => '#form_slidergeneral_revslider-revealer-addon', 
						'focus' => "#revealer_delay"
						
					)
					
				),
				
				'overlay' => array(
				
					'enable' => array(
						
						'dependency_id' => 'reveal_overlay',
						'buttonTitle' => __('Revealer Addon Overlay', 'revslider-revealer-addon'), 
						'title' => __('Enable Overlay', 'revslider-revealer-addon'),
						'helpPath' => 'addOns.revslider-revealer-addon.overlay.enable', 
						'keywords' => array('addon', 'addons', 'reveal', 'reveal addon', 'revealer', 'revealer addon', 'reveal overlay', 'overlay'), 
						'description' => __("Add an optional overlay to the Slider as the reveal effect takes place", 'revslider-revealer-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/reveal-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Reveal',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-revealer-addon.direction', 'value' => 'open_horizontal::open_vertical::split_left_corner::split_right_corner::shrink_circle::left_to_right::right_to_left::top_to_bottom::bottom_to_top::tlbr_skew::trbl_skew::bltr_skew::brtl_skew::expand_circle', 'option' => 'reveal_type')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-revealer-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-revealer-addon', 
							'focus' => "*[data-r='addOns.revslider-revealer-addon.overlay.enable']"
							
						)
						
					),
					
					'color' => array(
					
						'title' => __('Overlay Color', 'revslider-revealer-addon'),
						'helpPath' => 'addOns.revslider-revealer-addon.overlay.color', 
						'keywords' => array('addon', 'addons', 'reveal', 'reveal addon', 'revealer', 'revealer addon', 'reveal overlay', 'overlay color'), 
						'description' => __("The main color for the revealer's overlay", 'revslider-revealer-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/reveal-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Reveal',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => 'settings.addOns.revslider-revealer-addon.direction', 'value' => 'open_horizontal::open_vertical::split_left_corner::split_right_corner::shrink_circle::left_to_right::right_to_left::top_to_bottom::bottom_to_top::tlbr_skew::trbl_skew::bltr_skew::brtl_skew', 'option' => 'reveal_type'),
								array('path' => 'settings.addOns.revslider-revealer-addon.overlay.enable', 'value' => true, 'option' => 'reveal_overlay')
								
							),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-revealer-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-revealer-addon', 
							'focus' => "*[data-r='addOns.revslider-revealer-addon.overlay.color']"
							
						)
						
					),
					
					'easing' => array(
						
						'title' => __('Overlay Easing', 'revslider-revealer-addon'),
						'helpPath' => 'addOns.revslider-revealer-addon.overlay.easing', 
						'keywords' => array('addon', 'addons', 'reveal', 'reveal addon', 'revealer', 'revealer addon', 'overlay easing', 'easing'), 
						'description' => __("The easing equation to use for the revealer's overlay", 'revslider-revealer-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/reveal-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Reveal',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => 'settings.addOns.revslider-revealer-addon.direction', 'value' => 'open_horizontal::open_vertical::split_left_corner::split_right_corner::shrink_circle::left_to_right::right_to_left::top_to_bottom::bottom_to_top::tlbr_skew::trbl_skew::bltr_skew::brtl_skew', 'option' => 'reveal_type'),
								array('path' => 'settings.addOns.revslider-revealer-addon.overlay.enable', 'value' => true, 'option' => 'reveal_overlay')
								
							),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-revealer-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-revealer-addon', 
							'focus' => "*[data-r='addOns.revslider-revealer-addon.overlay.easing']"
							
						)
						
					),
					
					'duration' => array(
						 
						'title' => __('Overlay Duration', 'revslider-revealer-addon'),
						'helpPath' => 'addOns.revslider-revealer-addon.overlay.duration', 
						'keywords' => array('addon', 'addons', 'reveal', 'reveal addon', 'revealer', 'revealer addon', 'overlay duration', 'duration'), 
						'description' => __("The total time the overlay should fade out in milliseconds", 'revslider-revealer-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/reveal-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Reveal',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => 'settings.addOns.revslider-revealer-addon.direction', 'value' => 'open_horizontal::open_vertical::split_left_corner::split_right_corner::shrink_circle::left_to_right::right_to_left::top_to_bottom::bottom_to_top::tlbr_skew::trbl_skew::bltr_skew::brtl_skew', 'option' => 'reveal_type'),
								array('path' => 'settings.addOns.revslider-revealer-addon.overlay.enable', 'value' => true, 'option' => 'reveal_overlay')
								
							),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-revealer-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-revealer-addon', 
							'focus' => "*[data-r='addOns.revslider-revealer-addon.overlay.duration']"
							
						)
						
					),
					
					'delay' => array(
						
						'title' => __('Overlay Delay', 'revslider-revealer-addon'),
						'helpPath' => 'addOns.revslider-revealer-addon.overlay.delay', 
						'keywords' => array('addon', 'addons', 'reveal', 'reveal addon', 'revealer', 'revealer addon', 'overlay delay', 'delay'), 
						'description' => __("A delay in milliseconds before the overlay should fade out", 'revslider-revealer-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/reveal-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Reveal',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => 'settings.addOns.revslider-revealer-addon.direction', 'value' => 'open_horizontal::open_vertical::split_left_corner::split_right_corner::shrink_circle::left_to_right::right_to_left::top_to_bottom::bottom_to_top::tlbr_skew::trbl_skew::bltr_skew::brtl_skew', 'option' => 'reveal_type'),
								array('path' => 'settings.addOns.revslider-revealer-addon.overlay.enable', 'value' => true, 'option' => 'reveal_overlay')
								
							),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-revealer-addon", 
							'scrollTo' => '#form_slidergeneral_revslider-revealer-addon', 
							'focus' => "*[data-r='addOns.revslider-revealer-addon.overlay.delay']"
							
						)
						
					),
				
				),
				
				'spinner' => array(
					
					'title' => __('Revealer Spinner', 'revslider-revealer-addon'),
					'helpPath' => 'addOns.revslider-revealer-addon.spinner.type', 
					'keywords' => array('addon', 'addons', 'reveal', 'reveal addon', 'revealer', 'revealer addon', 'reveal spinner', 'revealer spinner', 'spinner', 'preloader', 'reveal preloader', 'revealer preloader'), 
					'description' => __("The preloader spinner to show as the reveal effect occurs", 'revslider-revealer-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/reveal-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Reveal',
					'highlight' => array(
						
						'menu' => "#module_settings_trigger, #gst_sl_revslider-revealer-addon", 
						'scrollTo' => '#form_slidergeneral_revslider-revealer-addon', 
						'focus' => "#revealer_spinners"
						
					)
					
				)
				
			)
			
		);
		
	}

}
	
?>