<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnBeforeAfterBase {
	
	const MINIMUM_VERSION = '6.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnBeforeAfterBase::MINIMUM_VERSION, '>=')) {
		
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
			$update_admin = new RevAddOnBeforeAfterUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'));
			add_filter('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
			require_once(static::$_PluginPath . 'admin/includes/slider.class.php');
			
			// admin init
			new RsBeforeAfterSliderAdmin();
			
		}
		
		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		require_once(static::$_PluginPath . 'public/includes/slide.class.php');
		
		new RsBeforeAfterSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsBeforeAfterSlideFront(static::$_PluginTitle);
		
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
			wp_localize_script( $_handle, 'revslider_beforeafter_addon', self::get_var() );
		}
		
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-beforeafter-addon') {
		
		if($slug === 'revslider-beforeafter-addon'){
			
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
			$definitions['editor_settings']['slider_settings']['addons']['beforeafter_addon'] = $help['slider'];
		}
		
		if(isset($definitions['editor_settings']['slide_settings']) && isset($definitions['editor_settings']['slide_settings']['addons'])) {
			$help = self::get_definitions();
			$definitions['editor_settings']['slide_settings']['addons']['beforeafter_addon'] = $help['slide'];
		}
		
		if(isset($definitions['editor_settings']['layer_settings']) && isset($definitions['editor_settings']['layer_settings']['addons'])) {
			$help = self::get_definitions();
			$definitions['editor_settings']['layer_settings']['addons']['beforeafter_addon'] = $help['layer'];
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
			'beforeafter' => __('Before/After','revslider-beforeafter-addon'),
			'active' => __('Active','revslider-beforeafter-addon'),
			'beforeafter' => __('Before After','revslider-beforeafter-addon'),
			'basettings' => __('Before After Settings','revslider-beforeafter-addon'),
			'arrows' => __('Arrows','revslider-beforeafter-addon'),
			'dragcontainer' => __('Drag Container','revslider-beforeafter-addon'),
			'dividerline' => __('Divider Line','revslider-beforeafter-addon'),
			'defaults' => __('Defaults','revslider-beforeafter-addon'),
			'misc' => __('Misc','revslider-beforeafter-addon'),
			'iconsize' => __('Icon Size','revslider-beforeafter-addon'),
			'iconspacing' => __('Icon Spacing','revslider-beforeafter-addon'),
			'horizontal' => __('Horizontal','revslider-beforeafter-addon'),
			'vertical' => __('Vertical','revslider-beforeafter-addon'),
			'iconcolor' => __('Icon Color','revslider-beforeafter-addon'),
			'iconshadow' => __('Icon Shadow','revslider-beforeafter-addon'),
			'shadowblur' => __('Shadow Blur','revslider-beforeafter-addon'),
			'shadowcolor' => __('Shadow Color','revslider-beforeafter-addon'),
			'padding' => __('Padding','revslider-beforeafter-addon'),
			'borderradius' => __('Border Radius','revslider-beforeafter-addon'),
			'bgcolor' => __('BG Color','revslider-beforeafter-addon'),
			'border' => __('Border','revslider-beforeafter-addon'),
			'borderwidth' => __('Border Width','revslider-beforeafter-addon'),
			'bordercolor' => __('Border Color','revslider-beforeafter-addon'),
			'boxshadow' => __('Box Shadow','revslider-beforeafter-addon'),
			'shadowstrength' => __('Shadow Strength','revslider-beforeafter-addon'),
			'linesize' => __('Line Width','revslider-beforeafter-addon'),
			'linecolor' => __('Line Color','revslider-beforeafter-addon'),
			'lineshadow' => __('Line Shadow','revslider-beforeafter-addon'),
			'animonstg' => __('Animate on Stage Click','revslider-beforeafter-addon'),
			'duration' => __('Duration','revslider-beforeafter-addon'),
			'easing' => __('Easing','revslider-beforeafter-addon'),
			'mousecursor' => __('Mouse Cursor','revslider-beforeafter-addon'),
			'icons' => __('Icons','revslider-beforeafter-addon'),
			'animate' => __('Animate','revslider-beforeafter-addon'),
			'direction' => __('Direction','revslider-beforeafter-addon'),
			'delay' => __('Delay','revslider-beforeafter-addon'),
			'animateout' => __('Type','revslider-beforeafter-addon'),
			'teaser' => __('Teaser Anim','revslider-beforeafter-addon'),
			'bouncetype' => __('Type','revslider-beforeafter-addon'),
			'distance' => __('Distance','revslider-beforeafter-addon'),
			'speed' => __('Speed','revslider-beforeafter-addon'),
			'arrowanim' => __('Transition','revslider-beforeafter-addon'),
			'initdistance' => __('Init. Distance','revslider-beforeafter-addon'),
			'fade' => __('Fade','revslider-beforeafter-addon'),
			'collapse' => __('Collapse','revslider-beforeafter-addon'),
			'none' => __('None','revslider-beforeafter-addon'),
			'teainitial' => __('On Initial Reveal','revslider-beforeafter-addon'),
			'teaonce' => __('Until First Grab','revslider-beforeafter-addon'),
			'tealoop' => __('Infinite Loop','revslider-beforeafter-addon'),
			'general' => __('General Settings','revslider-beforeafter-addon'),
			'initsettings' => __('Start/End Animation','revslider-beforeafter-addon'),
			'teasersettings' => __('Teaser Settings','revslider-beforeafter-addon'),
			'arrowsettings' => __('Arrow Settings','revslider-beforeafter-addon'),
			'initsplit' => __('Init Split','revslider-beforeafter-addon'),
			'repel' => __('Repel','revslider-beforeafter-addon'),
			'attract' => __('Attract','revslider-beforeafter-addon'),
			'linear' => __('Linear','revslider-beforeafter-addon'),
			'environment' => __('Environment','revslider-beforeafter-addon'),
			'before' => __('Before','revslider-beforeafter-addon'),
			'after' => __('After','revslider-beforeafter-addon'),
			'settings' => __('Settings','revslider-beforeafter-addon'),
			'sourcesize' => __('Source Size','revslider-beforeafter-addon'),
			'bgfit' => __('BG Fit','revslider-beforeafter-addon'),
			'repeat' => __('BG Fit','revslider-beforeafter-addon'),					
			'position' => __('Position','revslider-beforeafter-addon'),
			'xperyper' => __('X% Y%','revslider-beforeafter-addon'),
			'leftcenter' => __('Left Center','revslider-beforeafter-addon'),
			'leftbottom' => __('Left Bottom','revslider-beforeafter-addon'),
			'lefttop' => __('Left Top','revslider-beforeafter-addon'),
			'centertop' => __('Center Top','revslider-beforeafter-addon'),
			'centercenter' => __('Center Center','revslider-beforeafter-addon'),
			'centerbottom' => __('Center Bottom','revslider-beforeafter-addon'),
			'righttop' => __('Right Top','revslider-beforeafter-addon'),
			'rightcenter' => __('Right Center','revslider-beforeafter-addon'),
			'rightbottom' => __('Right Bottom','revslider-beforeafter-addon'),
			'widthattr' => __('Width Attr.','revslider-beforeafter-addon'),
			'heightattr' => __('Height Attr.','revslider-beforeafter-addon'),
			'aspectratio' => __('Aspect Ratio','revslider-beforeafter-addon'),
			'overlay' => __('Overlay','revslider-beforeafter-addon'),
			'loopmode' => __('Loop Mode','revslider-beforeafter-addon'),
			'disable' => __('Disable','revslider-beforeafter-addon'),
			'slidertimepause' => __('Slider Timer Paused','revslider-beforeafter-addon'),
			'slidertimerkeep' => __('Slider Timer keep Going','revslider-beforeafter-addon'),
			'forcecovermode' => __('Force Cover Mode','revslider-beforeafter-addon'),
			'nextslideatend' => __('Next Slide At End','revslider-beforeafter-addon'),
			'rewindstart' => __('Rewind at Start','revslider-beforeafter-addon'),
			'muteatstart' => __('Mute at Start','revslider-beforeafter-addon'),
			'arguments' => __('Arguments','revslider-beforeafter-addon'),
			'image' => __('Image','revslider-beforeafter-addon'),
			'externalimage' => __('External Image','revslider-beforeafter-addon'),
			'youtube' => __('YouTube','revslider-beforeafter-addon'),
			'htmlvideo' => __('HTML5 Video','revslider-beforeafter-addon'),
			'vimeo' => __('Vimeo','revslider-beforeafter-addon'),
			'colored' => __('Colored','revslider-beforeafter-addon'),
			'transparent' => __('Transparent','revslider-beforeafter-addon'),
			'type' => __('Type','revslider-beforeafter-addon'),
			'source' => __('Source','revslider-beforeafter-addon'),
			'refreshsource' => __('Refresh Source','revslider-beforeafter-addon'),
			'enterimageurl' => __('Enter Image URL...','revslider-beforeafter-addon'),
			'backgroundcolor' => __('Background Color','revslider-beforeafter-addon'),
			'medialibrary' => __('Media Library','revslider-beforeafter-addon'),
			'objectlibrary' => __('Object Library','revslider-beforeafter-addon'),
			'enterytid' => __('Enter YouTube Id','revslider-beforeafter-addon'),
			'entervimeoid' => __('Enter Vimeo Id','revslider-beforeafter-addon'),
			'youtubeid' => __('YouTube ID','revslider-beforeafter-addon'),
			'posterimage' => __('Poster Image','revslider-beforeafter-addon'),
			'ytposter' => __('YouTube Poster','revslider-beforeafter-addon'),
			'vimeoid' => __('Vimeo ID','revslider-beforeafter-addon'),
			'mpeg' => __('Mpeg','revslider-beforeafter-addon'),
			'entermpegsrc' => __('Enter MPEG Source','revslider-beforeafter-addon'),
			'settings' => __('Settings','revslider-beforeafter-addon'),
			'selectbefore' => __('Show Before Mode','revslider-beforeafter-addon'),
			'selectafter' => __('Show After Mode','revslider-beforeafter-addon'),
			'selectbeforeafter' => __('Show All Layers','revslider-beforeafter-addon'),
			'offset' => __('Shift Offset','revslider-beforeafter-addon'),
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
			
				'icons' => array(
				
					'icon' => array(
					
						'title' => __('Drag/Reveal Icons', 'revslider-beforeafter-addon'), 
						'helpPath' => 'beforeafter-icon', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter icon', 'icon'), 
						'description' => __("Select the icon to be used for the drag/reveal navigation", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_arrows']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "{first}*[data-helpkey='beforeafter-icon']"
							
						)
						
					),
					
					'size' => array(
						
						'title' => __('Icons Size', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.icon.size', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter icon', 'icon', 'icon size'), 
						'description' => __("The size of the drag/reveal icons in pixels", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_arrows']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.icon.size']"
							
						)
						
					),
					
					'spacing' => array(
						
						'title' => __('Icon Spacing', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.icon.space', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter icon', 'icon', 'icon spacing'), 
						'description' => __("Space/padding between the icons in pixels", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_arrows']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.icon.space']"
							
						)
						
					),
					
					'color' => array(
					
						'title' => __('Icon Color', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.icon.color', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter icon', 'icon', 'icon color'), 
						'description' => __("The color to use for the drag/reveal navigation icons", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_arrows']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.icon.color']"
							
						)
						
					),
					
					'shadow' => array(
						
						'dependency_id' => 'beforeafter_iconshadow',
						'title' => __('Icon Shadow', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.icon.shadow.set', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter icon', 'icon', 'icon shadow'), 
						'description' => __("Add a CSS text-shadow to the drag/reveal navigation icons", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_arrows']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.icon.shadow.set']"
							
						)
						
					),
					
					'shadow_blur' => array(
					
						'title' => __('Shadow Blur', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.icon.shadow.blur', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter icon', 'icon', 'icon shadow'), 
						'description' => __("The blur amount in pixels to use for the CSS text-shadow", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-beforeafter-addon.icon.shadow.set', 'value' => true, 'option' => 'beforeafter_iconshadow')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_arrows']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.icon.shadow.blur']"
							
						)
						
					),
					
					'shadow_color' => array(
					
						'title' => __('Shadow Color', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.icon.shadow.color', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter icon', 'icon', 'icon shadow'), 
						'description' => __("The color for the icon's text-shadow", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-beforeafter-addon.icon.shadow.set', 'value' => true, 'option' => 'beforeafter_iconshadow')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_arrows']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "#beforeafter_iconshadowcolor"
							
						)
						
					)
				
				),
				
				'divider_line' => array(
				
					'line_width' => array(
						
						'buttonTitle' => __('Divider Line Width', 'revslider-beforeafter-addon'), 
						'title' => __('Line Width', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.divider.size', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter line', 'divider', 'divider line', 'divider line width'), 
						'description' => __("The width of the divider line in pixels that seperates the before/after view", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_dividerline']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.divider.size']"
							
						)
						
					),
					
					'line_color' => array(
						
						'buttonTitle' => __('Divider Line Color', 'revslider-beforeafter-addon'), 
						'title' => __('Line Color', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.divider.color', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter line', 'divider', 'divider line', 'divider line color'), 
						'description' => __("The color of the divider line that seperates the before/after view", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_dividerline']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.divider.color']"
							
						)
						
					),
					
					'line_shadow' => array(
						
						'dependency_id' => 'beforeafter_lineshadow',
						'buttonTitle' => __('Divider Line Shadow', 'revslider-beforeafter-addon'), 
						'title' => __('Line Shadow', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.divider.shadow.set', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter line', 'divider', 'divider line', 'divider line shadow'), 
						'description' => __("Add a CSS box-shadow to the before/after divider line", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_dividerline']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.divider.shadow.set']"
							
						)
						
					),
					
					'line_shadow_blur' => array(
					
						'title' => __('Line Shadow Blur', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.divider.shadow.blur', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter line', 'divider', 'divider line', 'divider line shadow', 'line shadow blur'), 
						'description' => __("The blur amount in pixels to use for the CSS box-shadow", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-beforeafter-addon.divider.shadow.blur', 'value' => true, 'option' => 'beforeafter_lineshadow')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_arrows']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.divider.shadow.blur']"
							
						)
						
					),
					
					'shadow_color' => array(
					
						'title' => __('Shadow Color', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.divider.shadow.color', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter line', 'divider', 'divider line', 'divider line shadow', 'line shadow color'), 
						'description' => __("The color for the divider line's CSS box-shadow", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-beforeafter-addon.divider.shadow.blur', 'value' => true, 'option' => 'beforeafter_lineshadow')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_arrows']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "#beforeafter_dividershadowcolor"
							
						)
						
					)
				
				),
				
				'animate' => array(
				
					'enable' => array(
						
						'dependency_id' => 'beforeafter_animate',
						'title' => __('Animate on-click', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.onclick.set', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'animate on stage click'), 
						'description' => __("Animate the Before/After view to where the user clicks on the screen", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_misc']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.onclick.set']"
							
						)
						
					),
					
					'duration' => array(
						
						'title' => __('Animation Duration', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.onclick.time', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'animate on stage click', 'animation duration'), 
						'description' => __("The on-click animation duration in milliseconds", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-beforeafter-addon.onclick.set', 'value' => true, 'option' => 'beforeafter_animate')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_misc']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.onclick.time']"
							
						)
						
					),
					
					'easing' => array(
						
						'title' => __('Animation Easing', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.onclick.easing', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'animate on stage click', 'animation easing'), 
						'description' => __("The easing equation to apply for the on-click animation", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-beforeafter-addon.onclick.set', 'value' => true, 'option' => 'beforeafter_animate')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_misc']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.onclick.easing']"
							
						)
						
					),
					
					'cursor' => array(
						
						'title' => __('Mouse Cursor', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.onclick.cursor', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'animate on stage click', 'mouse cursor', 'cursor'), 
						'description' => __("The default cursor to use when the on-click animation is enabled", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-beforeafter-addon.onclick.set', 'value' => true, 'option' => 'beforeafter_animate')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_misc']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.onclick.cursor']"
							
						)
						
					)
				
				),
				
				'drag_container' => array(
				
					'padding' => array(
					
						'buttonTitle' => __('Drag Container Padding', 'revslider-beforeafter-addon'), 
						'title' => __('Padding', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.drag.padding', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter drag container', 'drag container', 'drag container padding'), 
						'description' => __("Padding/spacing applied to the wrapping container for the drag/reveal navigation arrows", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_dragcontainer']", 
							'scrollTo' => '#form_layerinner_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.drag.padding']"
							
						)
						
					),
					
					'radius' => array(
					
						'title' => __('Border Radius', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.drag.radius', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter drag container', 'drag container', 'border radius'), 
						'description' => __("The border radius for the navigation arrows' drag/reveal container", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_dragcontainer']", 
							'scrollTo' => '#form_layerinner_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.drag.radius']"
							
						)
						
					),
					
					'color' => array(
					
						'title' => __('Background Color', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.drag.bgcolor', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter drag container', 'drag container', 'drag container color'), 
						'description' => __("The background color for the arrows' drag/reveal container", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_dragcontainer']", 
							'scrollTo' => '#form_layerinner_revslider-beforeafter-addon', 
							'focus' => "#beforeafter_dragbgcolor"
							
						)
						
					),
					
					'border' => array(
						
						'dependency_id' => 'beforeafter_dragborder',
						'title' => __('Border', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.drag.border.set', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter drag container', 'drag container', 'drag container border'), 
						'description' => __("Optional border the arrows' drag/reveal container", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_dragcontainer']", 
							'scrollTo' => '#form_layerinner_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.drag.border.set']"
							
						)
						
					),
					
					'border_width' => array(
						
						'title' => __('Border Width', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.drag.border.width', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter drag container', 'drag container', 'drag container border'), 
						'description' => __("Border width/size for the drag/reveal container's border", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-beforeafter-addon.drag.border.set', 'value' => true, 'option' => 'beforeafter_dragborder')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_dragcontainer']", 
							'scrollTo' => '#form_layerinner_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.drag.border.width']"
							
						)
						
					),
					
					'border_color' => array(
						
						'title' => __('Border Color', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.drag.border.color', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter drag container', 'drag container', 'drag container border'), 
						'description' => __("Border color for the drag/reveal container's border", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-beforeafter-addon.drag.border.set', 'value' => true, 'option' => 'beforeafter_dragborder')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_dragcontainer']", 
							'scrollTo' => '#form_layerinner_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.drag.border.color']"
							
						)
						
					),
					
					'box_shadow' => array(
						
						'dependency_id' => 'beforeafter_dragboxshadow',
						'title' => __('Box Shadow', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.drag.boxshadow.set', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter drag container', 'drag container', 'drag container box shadow', 'box shadow'), 
						'description' => __("Optional box-shadow for the drag/reveal container's border", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_dragcontainer']", 
							'scrollTo' => '#form_layerinner_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.drag.boxshadow.set']"
							
						)
						
					),
					
					'box_shadow_blur' => array(
					
						'title' => __('Box Shadow Blur', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.drag.boxshadow.blur', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'box shadow'), 
						'description' => __("The blur amount in pixels to use for the CSS box-shadow", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-beforeafter-addon.drag.boxshadow.set', 'value' => true, 'option' => 'beforeafter_dragboxshadow')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_dragcontainer']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.drag.boxshadow.blur']"
							
						)
						
					),
					
					'box_shadow_strength' => array(
					
						'title' => __('Box Shadow Strength', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.drag.boxshadow.strength', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter line', 'divider', 'divider line', 'divider line shadow', 'line shadow blur'), 
						'description' => __("The spread/strength amount in pixels to use for the CSS box-shadow", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-beforeafter-addon.drag.boxshadow.set', 'value' => true, 'option' => 'beforeafter_dragboxshadow')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_dragcontainer']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.drag.boxshadow.strength']"
							
						)
						
					),
					
					'box_shadow_color' => array(
					
						'title' => __('Box Shadow Color', 'revslider-beforeafter-addon'), 
						'helpPath' => 'addOns.revslider-beforeafter-addon.icon.boxshadow.color', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter line', 'divider', 'divider line', 'divider line shadow', 'line shadow color'), 
						'description' => __("The color to be used for the CSS box-shadow", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => 'settings.addOns.revslider-beforeafter-addon.drag.boxshadow.set', 'value' => true, 'option' => 'beforeafter_dragboxshadow')),
							'menu' => "#module_settings_trigger, #gst_sl_revslider-beforeafter-addon, .ssmbtn[data-showssm='#beforeafter_dragcontainer']", 
							'scrollTo' => '#form_module_revslider-beforeafter-addon', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.icon.boxshadow.color']"
							
						)
						
					)
				
				)
			
			),
			
			'slide' => array(
			
				'enable' => array(
					
					'dependency_id' => 'beforeafter_enable',
					'buttonTitle' => __('Activate Before/After', 'revslider-beforeafter-addon'), 
					'title' => __('Enable', 'revslider-beforeafter-addon'),
					'helpPath' => 'addOns.revslider-beforeafter-addon.enable', 
					'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon'), 
					'description' => __("Enable the Before/After AddOn for the current Slide", 'revslider-beforeafter-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Before/After',
					'highlight' => array(
						
						'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
						'scrollTo' => '#form_slidegeneral_revslider-beforeafter-addon', 
						'focus' => "*[data-r='addOns.revslider-beforeafter-addon.enable']"
						
					)
					
				),
				
				'split' => array(
					
					'buttonTitle' => __('Starting Split Point', 'revslider-beforeafter-addon'), 
					'title' => __('Split Point', 'revslider-beforeafter-addon'),
					'helpPath' => 'addOns.revslider-beforeafter-addon.moveTo.#size#.v', 
					'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'before after split', 'split', 'init split'), 
					'description' => __("The starting split point for the Before and After view.  Can be set differently for each of the Slider's viewports", 'revslider-beforeafter-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Before/After',
					'highlight' => array(
						
						'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable')),
						'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
						'scrollTo' => '#form_slidegeneral_revslider-beforeafter-addon', 
						'focus' => "*[data-r='addOns.revslider-beforeafter-addon.moveTo.#size#.v']"
						
					)
					
				),
				
				'starting_animation' => array(
				
					'direction' => array(
						
						'title' => __('View Direction', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.direction', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'before after split', 'init animation', 'animation direction', 'view', 'view direction'), 
						'description' => __("Choose if the Before/After views should be side by side (horizontal) or on top of one another (vertical)", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
							'scrollTo' => '#beforeafter_slide_settings', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.direction']"
							
						)
						
					),
					
					'delay' => array(
						
						'title' => __('Delay', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.delay', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'init animation', 'animation delay'), 
						'description' => __("A delay in milliseconds before the views are revealed", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
							'scrollTo' => '#beforeafter_slide_settings', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.delay']"
							
						)
						
					),
					
					'time' => array(
						
						'title' => __('Duration', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.time', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'init animation', 'animation duration'), 
						'description' => __("The total time in milliseconds the initial reveal takes place", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
							'scrollTo' => '#beforeafter_slide_settings', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.time']"
							
						)
						
					),
					
					'easing' => array(
						
						'title' => __('Easing', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.easing', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'init animation', 'animation easing'), 
						'description' => __("The easing equation to be used for the initial reveal animation", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
							'scrollTo' => '#beforeafter_slide_settings', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.easing']"
							
						)
						
					),
					
					'animate_out' => array(
						
						'title' => __('Animation Out', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.animateOut', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'slide change'), 
						'description' => __("Choose if the current Slide should fade out or close/collapse itself before the next Slide is shown", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
							'scrollTo' => '#beforeafter_slide_settings', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.animateOut']"
							
						)
						
					)
				
				),
				
				'teaser_settings' => array(
				
					'teaser_animation' => array(
						
						'title' => __('Teaser Animation', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.teaser.set', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'teaser animation', 'teaser'), 
						'description' => __("Choose if the move/drag icons should 'bounce' slightly to get the user's attention", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
							'scrollTo' => '#beforeafter_teaser_settings', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.teaser.set']"
							
						)
						
					),
					
					'teaser_type' => array(
						
						'title' => __('Teaser Type', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.teaser.type', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'teaser animation', 'teaser', 'animation out'), 
						'description' => __("The gravity direction in which the icons will bounce toward and away from each other", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
							'scrollTo' => '#beforeafter_teaser_settings', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.teaser.type']"
							
						)
						
					),
					
					'teaser_distance' => array(
						
						'title' => __('Distance', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.teaser.distance', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'teaser animation', 'teaser', 'distance', 'teaser distance'), 
						'description' => __("The amount of pixels the icons should pull and repel toward each other for the teaser animation", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
							'scrollTo' => '#beforeafter_teaser_settings', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.teaser.distance']"
							
						)
						
					),
					
					'teaser_speed' => array(
						
						'title' => __('Speed', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.teaser.speed', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'teaser animation', 'teaser', 'speed', 'teaser speed'), 
						'description' => __("The teaser animation speed in milliseonds for each bounce", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
							'scrollTo' => '#beforeafter_teaser_settings', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.teaser.speed']"
							
						)
						
					),
					
					'teaser_easing' => array(
						
						'title' => __('Easing', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.teaser.easing', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'teaser animation', 'teaser', 'easing', 'teaser easing'), 
						'description' => __("The teaser animation speed in milliseonds for each bounce", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
							'scrollTo' => '#beforeafter_teaser_settings', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.teaser.easing']"
							
						)
						
					),
					
					'teaser_delay' => array(
						
						'title' => __('Delay', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.teaser.delay', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'teaser animation', 'teaser', 'delay', 'teaser delay'), 
						'description' => __("A delay in milliseonds before the teaser animation begins", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
							'scrollTo' => '#beforeafter_teaser_settings', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.teaser.delay']"
							
						)
						
					)
				
				),
				
				'arrow_settings' => array(
				
					'transition' => array(
						
						'dependency_id' => 'beforeafter_transition',
						'title' => __('Arrow Transition', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.shift.set', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'arrows', 'beforeafter arrows', 'arrow transition'), 
						'description' => __("Choose if the move/drag icons should 'bounce' slightly to get the user's attention", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
							'scrollTo' => '#beforeafter_arrow_settings', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.shift.set']"
							
						)
						
					),
					
					'offset' => array(
						
						'title' => __('Arrows Spacing', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.shift.offset', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'arrows', 'beforeafter arrows', 'arrow spacing', 'spacing'), 
						'description' => __("The space in pixels between the move/drag arrows before they are moved into place", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable'),
								array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.shift.set', 'value' => true, 'option' => 'beforeafter_transition')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
							'scrollTo' => '#beforeafter_arrow_settings', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.shift.offset']"
							
						)
						
					),
					
					'speed' => array(
						
						'title' => __('Speed', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.shift.speed', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'arrows', 'beforeafter arrows', 'arrows animation'), 
						'description' => __("The duration/speed in milliseconds for the initial Arrows transition", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable'),
								array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.shift.set', 'value' => true, 'option' => 'beforeafter_transition')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
							'scrollTo' => '#beforeafter_arrow_settings', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.shift.speed']"
							
						)
						
					),
					
					'easing' => array(
						
						'title' => __('Easing', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.shift.easing', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'arrows', 'beforeafter arrows', 'arrows animation'), 
						'description' => __("The duration/speed in milliseconds for the initial Arrows transition", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable'),
								array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.shift.set', 'value' => true, 'option' => 'beforeafter_transition')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
							'scrollTo' => '#beforeafter_arrow_settings', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.shift.easing']"
							
						)
						
					),
					
					'delay' => array(
						
						'title' => __('Delay', 'revslider-beforeafter-addon'),
						'helpPath' => 'addOns.revslider-beforeafter-addon.shift.delay', 
						'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'arrows', 'beforeafter arrows', 'arrows animation'), 
						'description' => __("A delay in milliseconds before the starting arrows animation begins", 'revslider-beforeafter-addon'), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Before/After',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.enable', 'value' => true, 'option' => 'beforeafter_enable'),
								array('path' => '#slide#.slide.addOns.revslider-beforeafter-addon.shift.set', 'value' => true, 'option' => 'beforeafter_transition')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-beforeafter-addon", 
							'scrollTo' => '#beforeafter_arrow_settings', 
							'focus' => "*[data-r='addOns.revslider-beforeafter-addon.shift.delay']"
							
						)
						
					)
				
				)
			
			),
			
			'layer' => array(
				
				'environment' => array(
					
					'buttonTitle' => __('Before/After Placement', 'revslider-beforeafter-addon'), 
					'title' => __('Environment', 'revslider-beforeafter-addon'),
					'helpPath' => 'addOns.revslider-beforeafter-addon.position', 
					'keywords' => array('addon', 'addons', 'before after', 'before after addon', 'beforeafter', 'beforeafter addon', 'beforeafter environment', 'environment'), 
					'description' => __("Choose if the Layer should appear in the 'Before' or 'After' view", 'revslider-beforeafter-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/before-after-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Before/After',
					'highlight' => array(
						
						'dependencies' => array('layerselected'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-beforeafter-addon", 
						'scrollTo' => '#form_layerinner_revslider-beforeafter-addon', 
						'focus' => "*[data-r='addOns.revslider-beforeafter-addon.position']"
						
					)
					
				)
				
			)
			
		);
		
	}

}
	
?>