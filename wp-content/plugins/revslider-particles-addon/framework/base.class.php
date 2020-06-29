<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnParticlesBase {
	
	const MINIMUM_VERSION = '6.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnParticlesBase::MINIMUM_VERSION, '>=')) {
		
			return 'add_notice_version';
		
		}
		else if(get_option('revslider-valid', 'false') == 'false') {
		
			 return 'add_notice_activation';
		
		}
		
		return false;
		
	}
	
	protected function loadClasses() {
		
		$isAdmin = is_admin();
		//require_once(static::$_PluginPath . 'shared/svg.class.php');
		
		if($isAdmin) {
			
			//handle update process, this uses the typical ThemePunch server process
			require_once(static::$_PluginPath . 'admin/includes/update.class.php');
			$update_admin = new RevAddOnParticlesUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin ,'set_update_transient'));
			add_filter('plugins_api', array($update_admin,'set_updates_api_results'), 10, 3);
			
			// admin CSS/JS, ajax
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			add_action( 'revslider_do_ajax', array($this, 'do_ajax'),10,2);	
			
		}
		
		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		require_once(static::$_PluginPath . 'public/includes/slide.class.php');
		
		new RsParticlesSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsParticlesSlideFront(static::$_PluginTitle);
		
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
			wp_localize_script( $_handle, 'revslider_particles_addon', self::get_var() );
			
		}
		
	}

	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public static function get_var() {
		
		$_textDomain = 'revslider-particles-addon';
		return array(
		
			'custom_templates' => self::get_templates(),
			'bricks' => array(
				'active'  =>  __('Active',$_textDomain),
				'entertext' => __('enter text...',$_textDomain),
				'particles' => __('Particle Eff.',$_textDomain),
				'firstslide' => __('First',$_textDomain),
				'lastslide' => __('Last',$_textDomain),
				'general' => __('General Settings',$_textDomain),
				'settings' => __('Particle Settings',$_textDomain),
				'singparticles' => __('Particles',$_textDomain),
				'amount' => __('Number of Particles',$_textDomain),
				'size' => __('Particle Size',$_textDomain),
				'randomsize' => __('Random Sizes',$_textDomain),
				'minsize' => __('Minimum Size',$_textDomain),
				'style' => __('Style',$_textDomain),
				'partopa' => __('Opacity',$_textDomain),
				'randopa' => __('Random Opacity',$_textDomain),
				'minopa' => __('Minimum Opacity',$_textDomain),
				'borders' => __('Borders  & Strokes',$_textDomain),
				'borsize' => __('Border Size',$_textDomain),
				'boropa' => __('Border Opacity',$_textDomain),
				'conlin' => __('Conneted Lines',$_textDomain),
				'linwidth' => __('Connected Line Width',$_textDomain),
				'linopa' => __('Connected Line Opacity',$_textDomain),
				'lindist' => __('Distance between Particles',$_textDomain),
				'zindex' => __('z-Index',$_textDomain),
				'movement' => __('Particle Movement',$_textDomain),
				'smovement' => __('Movement',$_textDomain),
				'interactivity' => __('Interactivity',$_textDomain),
				'pulse' => __('Pulse',$_textDomain),
				'speed' => __('Speed',$_textDomain),
				'vspeed' => __('Varying Speed',$_textDomain),
				'minspeed' => __('Min. Speed',$_textDomain),
				'direction' => __('Direction',$_textDomain),
				'top' => __('Top',$_textDomain),
				'bottom' => __('Bottom',$_textDomain),
				'left' => __('Left',$_textDomain),
				'right' => __('Right',$_textDomain),
				'topleft' => __('Top Left',$_textDomain),
				'topright' => __('Top Right',$_textDomain),
				'bottomleft' => __('Bottom Left',$_textDomain),
				'bottomright' => __('Bottom Right',$_textDomain),
				'static' => __('Static',$_textDomain),
				'random' => __('Random',$_textDomain),
				'vmovement' => __('Varying Movement',$_textDomain),
				'bounce' => __('Bounce',$_textDomain),
				'hovers'  => __( 'Mouse Hovers',$_textDomain),
				'clicks'  => __( 'Click Actions',$_textDomain),
				'grab' => __( 'Grab',$_textDomain),
				'bubble' => __('Bubble',$_textDomain),
				'repulse' => __( 'Repulse',$_textDomain),
				'bdist' => __('Bubble Distance',$_textDomain),
				'bsize' => __('Bubble Size',$_textDomain),
				'bop' => __('Bubble Opacity',$_textDomain),
				'gdist' => __('Grab Distance',$_textDomain),
				'gop' => __('Grap Opacity',$_textDomain),
				'rdist' => __('Repulse Distance',$_textDomain),
				'rease' => __('Repulse Easing',$_textDomain),
				'hmode' => __('Hover Mode',$_textDomain),
				'cmode' => __('Click Mode',$_textDomain),
				'nohover' => __('No Mouse Hover',$_textDomain),
				'noclick' => __('No Click Action',$_textDomain),
				'sync' => __('Synchronise',$_textDomain),
				'apsize' => __('Animate Particle Size',$_textDomain),
				'apopa' => __('Animate Particle Opacity',$_textDomain),
				'colorvariant' => __('Color Variant',$_textDomain),
				'particlecolor' => __('Particle Colors',$_textDomain),
				'bordercolor' => __('Border Colors',$_textDomain),
				'linescolor' => __('Connected Line Colors',$_textDomain),
				'pelib' => __('Particle Effects Library',$_textDomain),
				'parpres' => __('Default Presets',$_textDomain),
				'custompres' => __('Custom Presets',$_textDomain),
				'hideonmobile' => __('Disable on Mobile',$_textDomain),
				'objectlibrary' => __('SVG Library', $_textDomain),
				'bubblemessage' => __('Bubble clicking is not compatible with Hovers', $_textDomain),
			)
		);
	
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-particles-addon') {
		
		if($slug === 'revslider-particles-addon'){
			
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
			$definitions['editor_settings']['slide_settings']['addons']['particles_addon'] = $help['slide'];
		}
		
		return $definitions;
	
	}

	/**
	 * Handle Ajax Calls from RevSlider core
	 *
	 * @since    2.0.0
	 */
	public function do_ajax($return = "",$action ="") {
		switch ($action) {
			case 'delete_custom_templates_revslider-particles-addon':
				$return = $this->delete_template($_REQUEST["data"]);
				if($return){
					return  __('Particle Template deleted', 'revslider-particles-addon');
				}
				else{
					return  __('Particle Template could not be deleted', 'revslider-particles-addon');
				}
				break;
			case 'save_custom_templates_revslider-particles-addon':
				$return = $this->save_template($_REQUEST["data"]);
				if(empty($return) || !$return){
					return  __('Particle Template could not be saved', 'revslider-particles-addon');
				} 
				else {
					return  array( 'message' => __('Particle Template saved', 'revslider-particles-addon'),
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
		$custom = get_option('revslider_addon_particles_templates',false);

		//check for templates saved before 6.0
		if(!isset($custom[1]["title"])){
			$custom = self::fallback_templates($custom);
			//save new array into WP option
			update_option('revslider_addon_particles_templates',$custom);
		}

		return $custom;
	}

	/**
	 * Prepare old templates (before 6.0) to be readable/translateable for 6.x
	 *
	 * @since    2.0.0
	 */
	private static function fallback_templates($templates){
		$template_count = 1;
		$custom = array();
		/*
		//run through templates and build compatible array
		foreach($templates as $template_title => $template_presets){
			$custom[$template_count]["title"] = $template_title;
			$custom[$template_count]["preset"] = $template_presets;
		}
		*/
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
		if(update_option( 'revslider_addon_particles_templates', $custom )){
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
			if(update_option( 'revslider_addon_particles_templates', $custom )){
				return true;	
			}
			else {
				return false;
			}
		}
	}
	
	/**
	 * Returns the addon help definitions
	 *
	 * @since    2.0.0
	 */
	private static function get_definitions() {
		
		$_textdomain = 'revslider-particles-addon';
		return array(
		
			'slide' => array(
				
				'enable' => array(
						
					'dependency_id' => 'particles_enable',
					'buttonTitle' => __('Enable Particles', $_textdomain), 
					'title' => __('Enable', $_textdomain),
					'helpPath' => 'addOns.revslider-particles-addon.enable', 
					'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'enable particles', 'activate particles'), 
					'description' => __('Enable the Particles AddOn for this Slide', $_textdomain), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
					'video' => false,
					'section' => 'Slide Settings -> Particles',
					'highlight' => array(
					
						'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon", 
						'scrollTo' => '#form_slidegeneral_revslider-particles-addon', 
						'focus' => "#particles_enable"
						
					)
					
				),
				
				'particles' => array(
					
					'svg' => array(
					
						'buttonTitle' => __('Particles Icon', $_textdomain), 
						'title' => __('Icon', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.particles.shape', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles icon', 'particles svg', 'icon', 'svg'),
						'description' => __("Select one or more icons to display in the effect.", $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-1 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => ".particles-icon.selected"
							
						)
						
					),
					
					'num_particles' => array(
						
						'buttonTitle' => __('Number of Particles', $_textdomain), 
						'title' => __('Total Particles', $_textdomain),
						'helpPath' => 'addOns.revslider-particles-addon.particles.number', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'total particles', 'number of particles'), 
						'description' => __('The maximum number of particles to display at any given time.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-1 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "#particles_amount"
							
						)
						
					),
					
					'particles_size' => array(
						
						'buttonTitle' => __('Particle Size', $_textdomain), 
						'title' => __('Size', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.particles.size', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles size', 'particle size'), 
						'description' => __('The default size of each particle in pixels.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-1 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "#particles_size"
							
						)
						
					),
					
					'particles_random_size' => array(
						
						'dependency_id' => 'particles_random_size',
						'title' => __('Randomize Size', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.particles.random', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles size', 'particle size', 'random size'), 
						'description' => __('Randomize the particle sizes.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-1 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "*[data-r='addOns.revslider-particles-addon.particles.random']"
							
						)
						
					),
					
					'particles_min_size' => array(
						
						'title' => __('Minimum Size', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.particles.sizeMin', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles size', 'particle size', 'random size', 'minimum size'), 
						'description' => __('The minimum size for randomized sizes.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.particles.random', 'value' => true, 'option' => 'particles_random_size')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-1 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "#particles_minsize"
							
						)
						
					),
					
					'particles_disable_mobile' => array(
						
						'title' => __('Disable on Mobile', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.hideOnMobile', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'mobile', 'disable mobile'), 
						'description' => __('Disable the Particles effect on mobile devices.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-1 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "*[data-r='addOns.revslider-particles-addon.hideOnMobile']"
							
						)
						
					)
					
				),
				
				'style' => array(
				
					'particles_zindex' => array(
						
						'buttonTitle' => __('Particles zIndex', $_textdomain), 
						'title' => __('zIndex', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.styles.particle.zIndex', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'zindex', 'particles zindex'), 
						'description' => __('Set a custom <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/z-index">CSS z-index</a> for the particles HTML Canvas element.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-2 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "*[data-r='addOns.revslider-particles-addon.styles.particle.zIndex']"
							
						)
						
					),
					
					'particles_colors' => array(
						
						'buttonTitle' => __('Particle Color', $_textdomain), 
						'title' => __('Color', $_textdomain), 
						'helpPath' => 'particles-particle-color', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles color', 'color'), 
						'description' => __('Select one or multiple colors for the particles.  Multiple colors will be alternated between particles.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-2 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "#particle_particle_colors_wrap .rev-colorbox"
							
						)
						
					),
					
					'particles_opacity' => array(
						
						'buttonTitle' => __('Particle Opacity', $_textdomain),
						'title' => __('Opacity', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.styles.particle.opacity', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles opacity', 'opacity'), 
						'description' => __('The transparency level of the particles (0-100).', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-2 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "#particles_opacity"
							
						)
						
					),
					
					'particles_random_opacity' => array(
						
						'dependency_id' => 'particles_random_opacity',
						'title' => __('Random Opacity', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.styles.particle.opacityRandom', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles opacity', 'opacity', 'random opacity'), 
						'description' => __('Randomize the transparency for each particle.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-2 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "*[data-r='addOns.revslider-particles-addon.styles.particle.opacityRandom']"
							
						)
						
					),
					
					'particles_min_opacity' => array(
						
						'title' => __('Minimum Opacity', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.styles.particle.opacityMin', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles opacity', 'opacity', 'random opacity', 'minimum opacity'), 
						'description' => __('The minimum opacity to apply when the opacity is randomized.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.styles.particle.opacityRandom', 'value' => true, 'option' => 'particles_random_opacity')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-2 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "#particles_min_opacity"
							
						)
						
					),
					
					'borders_and_strokes' => array(
					
						'enable_border' => array(
							
							'dependency_id' => 'particles_border',
							'buttonTitle' => __('Enable Border/Stroke', $_textdomain), 
							'title' => __('Enable', $_textdomain), 
							'helpPath' => 'addOns.revslider-particles-addon.styles.border.enable', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'border', 'stroke', 'particles border'), 
							'description' => __('Add borders and strokes to the particle SVGs.', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable')),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-2 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "*[data-r='addOns.revslider-particles-addon.styles.border.enable']"
								
							)
							
						),
						
						'border_color' => array(
							
							'buttonTitle' => __('Border/Stroke Color', $_textdomain), 
							'title' => __('Color', $_textdomain), 
							'helpPath' => 'particles-border-color', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles border', 'particles stroke', 'border', 'stroke', 'color'), 
							'description' => __('Choose one or more colors for the border/stroke.  Multiple colors will be alternated between particles.', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(
							
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.styles.border.enable', 'value' => true, 'option' => 'particles_border')
									
								),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-2 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "#particle_border_colors_wrap .rev-colorbox"
								
							)
							
						),
						
						'border_size' => array(
							
							'buttonTitle' => __('Border/Stroke Size', $_textdomain), 
							'title' => __('Size', $_textdomain), 
							'helpPath' => 'addOns.revslider-particles-addon.styles.border.size', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles border', 'particles stroke', 'border', 'stroke', 'size'), 
							'description' => __('The "stroke-width" for the particle SVGs.', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(
							
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.styles.border.enable', 'value' => true, 'option' => 'particles_border')
									
								),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-2 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "#particles_border_size"
								
							)
							
						),
						
						'border_opacity' => array(
							
							'buttonTitle' => __('Border/Stroke Opacity', $_textdomain), 
							'title' => __('Opacity', $_textdomain), 
							'helpPath' => 'addOns.revslider-particles-addon.styles.border.opacity', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles border', 'particles stroke', 'border', 'stroke', 'opacity'), 
							'description' => __('A transparency level for the SVG border (0-100).', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(
							
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.styles.border.enable', 'value' => true, 'option' => 'particles_border')
									
								),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-2 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "#particles_border_opacity"
								
							)
							
						)
						
					),
					
					'connected_lines' => array(
						
						'enable_lines' => array(
							
							'dependency_id' => 'particles_connected_lines',
							'buttonTitle' => __('Enable Connected Lines', $_textdomain), 
							'title' => __('Enable', $_textdomain), 
							'helpPath' => 'addOns.revslider-particles-addon.styles.lines.enable', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'lines', 'connected lines'), 
							'description' => __('Connect each particle with lines, creating a spider-web type visual.', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable')),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-2 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "*[data-r='addOns.revslider-particles-addon.styles.lines.enable']"
								
							)
							
						),
						
						'lines_color' => array(
							
							'buttonTitle' => __('Connected Lines Color', $_textdomain),
							'title' => __('Color', $_textdomain), 
							'helpPath' => 'particles-lines-color', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'lines', 'connected lines', 'color'), 
							'description' => __('Choose one or more colors for the connected lines.  Multiple colors will be alternated between particles.', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(
							
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.styles.lines.enable', 'value' => true, 'option' => 'particles_connected_lines')
									
								),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-2 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "#particle_lines_colors_wrap .rev-colorbox"
								
							)
							
						),
						
						'lines_size' => array(
							
							'buttonTitle' => __('Connected Lines Size', $_textdomain), 
							'title' => __('Size', $_textdomain), 
							'helpPath' => 'addOns.revslider-particles-addon.styles.lines.width', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'lines', 'connected lines', 'size'), 
							'description' => __('The width of the connected lines in pixels.', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(
							
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.styles.lines.enable', 'value' => true, 'option' => 'particles_connected_lines')
									
								),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-2 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "#particles_lines_size"
								
							)
							
						),
						
						'lines_opacity' => array(
							
							'buttonTitle' => __('Connected Lines Opacity', $_textdomain), 
							'title' => __('Opacity', $_textdomain), 
							'helpPath' => 'addOns.revslider-particles-addon.styles.lines.opacity', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'lines', 'connected lines', 'opacity'), 
							'description' => __('The transparency level of the connected lines (0-100).', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(
							
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.styles.lines.enable', 'value' => true, 'option' => 'particles_connected_lines')
									
								),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-2 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "#particles_lines_opacity"
								
							)
							
						),
						
						'lines_distance' => array(
							
							'buttonTitle' => __('Connected Lines Distance', $_textdomain), 
							'title' => __('Distance', $_textdomain), 
							'helpPath' => 'addOns.revslider-particles-addon.styles.lines.distance', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'lines', 'connected lines', 'distance'), 
							'description' => __('The amount of space that needs to exist before a two particles are connected with lines.', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(
							
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.styles.lines.enable', 'value' => true, 'option' => 'particles_connected_lines')
									
								),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-2 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "#particles_lines_distance"
								
							)
							
						)
						
					)
					
				),
					
				'movement' => array(
				
					'enable_movement' => array(
						
						'dependency_id' => 'particles_movement',
						'buttonTitle' => __('Particles Movement', $_textdomain), 
						'title' => __('Enable', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.movement.enable', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles movement', 'movement'), 
						'description' => __('Most of the time you will want your particles to move for the effect, but they can also appear as a static image if this option is disabled.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-3 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "*[data-r='addOns.revslider-particles-addon.movement.enable']"
							
						)
						
					),
					
					'movement_speed' => array(
						
						'buttonTitle' => __('Particles Speed', $_textdomain), 
						'title' => __('Speed', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.movement.speed', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles speed', 'speed'), 
						'description' => __('The speed at which the particles move.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(
						
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.movement.enable', 'value' => true, 'option' => 'particles_movement')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-3 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "#particles_mov_speed"
							
						)
						
					),
					
					'varying_speed' => array(
						
						'dependency_id' => 'particles_varying_speed',
						'title' => __('Varying Speed', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.movement.randomSpeed', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles speed', 'speed', 'random speed', 'varying speed'), 
						'description' => __('Randomize the speed at which the particles move.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(
						
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.movement.enable', 'value' => true, 'option' => 'particles_movement')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-3 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "*[data-r='addOns.revslider-particles-addon.movement.randomSpeed']"
							
						)
						
					),
					
					'min_speed' => array(
						
						'title' => __('Minimum Speed', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.movement.speedMin', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles speed', 'speed', 'random speed', 'varying speed', 'min speed', 'minimum speed'), 
						'description' => __('The minimum speed the particles will move if the movement is randomized.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(
						
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.movement.enable', 'value' => true, 'option' => 'particles_movement'),
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.movement.randomSpeed', 'value' => true, 'option' => 'particles_varying_speed')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-3 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "#particles_mov_min_speed"
							
						)
						
					),
					
					'movement_bounce' => array(
						
						'title' => __('Bouncing', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.movement.bounce', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles bounce', 'bounce', 'bouncing'), 
						'description' => __('Choose if the particles should disappear when they reach the module bounding box, or if they should "bounce" off the walls and continue to move in another direction.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(
						
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.movement.enable', 'value' => true, 'option' => 'particles_movement')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-3 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "*[data-r='addOns.revslider-particles-addon.movement.bounce']"
							
						)
						
					),
					
					'movement_direction' => array(
						
						'title' => __('Direction', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.movement.direction', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles direction', 'direction'), 
						'description' => __('Particles can move in a linear direction (up, down, top-right, etc.) or move in a random direction.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(
						
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.movement.enable', 'value' => true, 'option' => 'particles_movement')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-3 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "#particles_mov_direction"
							
						)
						
					)
				
				),
				
				'interactivity' => array(
				
					'hover_mode' => array(
						
						'dependency_id' => 'particles_hover',
						'title' => __('Hover Mode', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.interactivity.hoverMode', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles hover', 'hover', 'hover mode'), 
						'description' => __('Display a special effect when the user hovers their mouse over the Slide', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-particles-addon.movement.enable', 'value' => true, 'option' => 'particles_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-4 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "*[data-r='addOns.revslider-particles-addon.interactivity.hoverMode']"
							
						)
						
					),
					
					'click_mode' => array(
						
						'title' => __('Click Mode', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.interactivity.clickMode', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles click', 'click', 'click mode'), 
						'description' => __('Display a special effect when the user clicks anywhere in the Slide', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable')),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-4 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "*[data-r='addOns.revslider-particles-addon.interactivity.clickMode']"
							
						)
						
					),
					
					'repulse_distance' => array(
						
						'title' => __('Repulse Distance', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.interactivity.repulse.distance', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles repulse', 'repulse', 'repulse distance'), 
						'description' => __('The distance at which the particles will jump to in random directions away from the mouse.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.interactivity.hoverMode', 'value' => 'repulse', 'option' => 'particles_hover')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-4 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "*[data-r='addOns.revslider-particles-addon.interactivity.repulse.distance']"
							
						)
						
					),
					
					'repulse_easing' => array(
						
						'title' => __('Repulse Easing', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.interactivity.repulse.easing', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles repulse', 'repulse', 'repulse easing', 'easing'), 
						'description' => __('The strength at which the particles will move.  For example, if "100" is entered, particles will start to move at a speed of "100" and then the speed will gradually be reduced to zero as the repulse effect takes place.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.interactivity.hoverMode', 'value' => 'repulse', 'option' => 'particles_hover')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-4 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "*[data-r='addOns.revslider-particles-addon.interactivity.repulse.easing']"
							
						)
						
					),
					
					'grab_distance' => array(
						
						'title' => __('Grab Distance', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.interactivity.grab.distance', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles grab', 'grab', 'grab distance', 'distance'), 
						'description' => __('The maximum distance the particles need to be from the mouse before the connected lines are drawn.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.interactivity.hoverMode', 'value' => 'grab', 'option' => 'particles_hover')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-4 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "*[data-r='addOns.revslider-particles-addon.interactivity.grab.distance']"
							
						)
						
					),
					
					'grab_opacity' => array(
						
						'title' => __('Grab Opacity', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.interactivity.grab.opacity', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles grab', 'grab', 'grab opacity', 'opacity'), 
						'description' => __('The opacity level for the connected lines when the grab effect takes place.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.interactivity.hoverMode', 'value' => 'grab', 'option' => 'particles_hover')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-4 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "*[data-r='addOns.revslider-particles-addon.interactivity.grab.opacity']"
							
						)
						
					),
					
					'bubble_distance' => array(
						
						'title' => __('Bubble Distance', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.interactivity.bubble.distance', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles bubble', 'bubble', 'bubble distance', 'distance'), 
						'description' => __('The maximum distance the particles need to be from the mouse before the particles are scaled.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.interactivity.hoverMode', 'value' => 'bubble', 'option' => 'particles_hover')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-4 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "*[data-r='addOns.revslider-particles-addon.interactivity.bubble.distance']"
							
						)
						
					),
					
					'bubble_size' => array(
						
						'title' => __('Bubble Size', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.interactivity.bubble.size', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles bubble', 'bubble', 'bubble size', 'size'), 
						'description' => __('The maximum size in pixels the particles will scale to.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.interactivity.hoverMode', 'value' => 'bubble', 'option' => 'particles_hover')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-4 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "*[data-r='addOns.revslider-particles-addon.interactivity.bubble.size']"
							
						)
						
					),
					
					'bubble_opacity' => array(
						
						'title' => __('Bubble Opacity', $_textdomain), 
						'helpPath' => 'addOns.revslider-particles-addon.interactivity.bubble.opacity', 
						'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles bubble', 'bubble', 'bubble opacity', 'opacity'), 
						'description' => __('The transparency level of the scaled particles.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
						'video' => false,
						'section' => 'Slide Settings -> Particles',
						'highlight' => array(
							
							'dependencies' => array(
							
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
								array('path' => '#slide#.slide.addOns.revslider-particles-addon.interactivity.hoverMode', 'value' => 'bubble', 'option' => 'particles_hover')
								
							),
							'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-4 > div", 
							'scrollTo' => '#particle_settings_wrap', 
							'focus' => "*[data-r='addOns.revslider-particles-addon.interactivity.bubble.opacity']"
							
						)
						
					)
				
				),
				
				'pulse' => array(
				
					'animate_size' => array(
					
						'enable_animation' => array(
						
							'dependency_id' => 'particles_animate_size',
							'buttonTitle' => __('Animate Size', $_textdomain), 
							'title' => __('Enable', $_textdomain), 
							'helpPath' => 'addOns.revslider-particles-addon.pulse.size.enable', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles animate', 'animate', 'animate size'), 
							'description' => __('Choose to continuously animate the particles size.', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable')),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-5 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "*[data-r='addOns.revslider-particles-addon.pulse.size.enable']"
								
							)
							
						),
						
						'speed' => array(
						
							'title' => __('Animation Speed', $_textdomain), 
							'helpPath' => 'addOns.revslider-particles-addon.pulse.size.speed', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles animate', 'animate', 'animate size', 'speed'), 
							'description' => __('The speed in milliseconds the particle’s size will animate.', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(
								
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.pulse.size.enable', 'value' => true, 'option' => 'particles_animate_size')
									
								),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-5 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "*[data-r='addOns.revslider-particles-addon.pulse.size.speed']"
								
							)
							
						),
						
						'min_size' => array(
						
							'title' => __('Minimum Size', $_textdomain), 
							'helpPath' => 'addOns.revslider-particles-addon.pulse.size.min', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles animate', 'animate', 'animate size', 'min size', 'minimum size'), 
							'description' => __('The smallest size in pixels the particles will animate to.', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(
								
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.pulse.size.enable', 'value' => true, 'option' => 'particles_animate_size')
									
								),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-5 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "*[data-r='addOns.revslider-particles-addon.pulse.size.min']"
								
							)
							
						),
						
						'synchronize' => array(
						
							'title' => __('Synchronize Animation', $_textdomain), 
							'helpPath' => 'addOns.revslider-particles-addon.pulse.size.sync', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles animate', 'animate', 'animate size', 'size', 'sync', 'synchronize'), 
							'description' => __('Enable this option to animate all particles size at the same time (otherwise they will animate randomly).', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(
								
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.pulse.size.enable', 'value' => true, 'option' => 'particles_animate_size')
									
								),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-5 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "*[data-r='addOns.revslider-particles-addon.pulse.size.sync']"
								
							)
							
						)
						
					),
					
					'animate_opacity' => array(
					
						'enable_animation' => array(
						
							'dependency_id' => 'particles_animate_opacity',
							'buttonTitle' => __('Animate Opacity', $_textdomain), 
							'title' => __('Enable', $_textdomain), 
							'helpPath' => 'addOns.revslider-particles-addon.pulse.opacity.enable', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles animate', 'animate', 'animate opacity'), 
							'description' => __('Choose to continuously animate the particles transparency levels.', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable')),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-5 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "*[data-r='addOns.revslider-particles-addon.pulse.opacity.enable']"
								
							)
							
						),
						
						'speed' => array(
						
							'title' => __('Animation Speed', $_textdomain), 
							'helpPath' => 'addOns.revslider-particles-addon.pulse.opacity.speed', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles animate', 'animate', 'animate size', 'speed'), 
							'description' => __('The speed in milliseconds the particles opacity will animate.', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(
								
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.pulse.opacity.enable', 'value' => true, 'option' => 'particles_animate_opacity')
									
								),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-5 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "*[data-r='addOns.revslider-particles-addon.pulse.opacity.speed']"
								
							)
							
						),
						
						'min_opacity' => array(
						
							'title' => __('Minimum Opacity', $_textdomain), 
							'helpPath' => 'addOns.revslider-particles-addon.pulse.opacity.min', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles animate', 'animate', 'animate size', 'min opacity', 'minimum opacity'), 
							'description' => __('The lowest opacity level the particles will animate to.', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(
								
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.pulse.opacity.enable', 'value' => true, 'option' => 'particles_animate_opacity')
									
								),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-5 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "*[data-r='addOns.revslider-particles-addon.pulse.opacity.min']"
								
							)
							
						),
						
						'synchronize' => array(
						
							'title' => __('Synchronize Animation', $_textdomain), 
							'helpPath' => 'addOns.revslider-particles-addon.pulse.opacity.sync', 
							'keywords' => array('addon', 'addons', 'particles', 'particles addon', 'particles animate', 'animate', 'animate size', 'size', 'sync', 'synchronize'), 
							'description' => __('Enable this option to animate the opacity level of all particles at the same time (otherwise they will animate randomly).', $_textdomain), 
							'helpStyle' => 'normal', 
							'article' => 'http://docs.themepunch.com/slider-revolution/particles-addon/', 
							'video' => false,
							'section' => 'Slide Settings -> Particles',
							'highlight' => array(
								
								'dependencies' => array(
								
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.enable', 'value' => true, 'option' => 'particles_enable'),
									array('path' => '#slide#.slide.addOns.revslider-particles-addon.pulse.opacity.enable', 'value' => true, 'option' => 'particles_animate_opacity')
									
								),
								'menu' => "#module_slide_trigger, #gst_slide_revslider-particles-addon, #particles-tab-5 > div", 
								'scrollTo' => '#particle_settings_wrap', 
								'focus' => "*[data-r='addOns.revslider-particles-addon.pulse.opacity.sync']"
								
							)
							
						)
						
					)
				
				)
			
			)
			
		);
		
	}

}
	
?>