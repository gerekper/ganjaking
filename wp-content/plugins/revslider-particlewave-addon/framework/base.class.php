<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2022 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnParticleWaveBase {
	
	const MINIMUM_VERSION = '6.5.6';

	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnParticleWaveBase::MINIMUM_VERSION, '>=')) {
		
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
			$update_admin = new RevAddOnParticleWaveUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'));
			add_filter('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
			add_filter('revslider_exportSlider_usedMedia', array($this, 'export_adddon_images'), 10, 3);	
			add_filter('revslider_importSliderFromPost_modify_data', array($this, 'import_update_addon_image_urls'), 10, 3);
		}
		
		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		require_once(static::$_PluginPath . 'public/includes/slide.class.php');
		
		new RsParticleWaveSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsParticleWaveSlideFront(static::$_PluginTitle);
		
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
			
			$_handle = 'rs-' . static::$_PluginTitle;
			$_base   = static::$_PluginUrl . 'admin/assets/';
			
			// load fronted Script for some global function
			$_jsPathMin = file_exists(RS_PARTICLEWAVE_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . static::$_PluginTitle . '.js') ? '' : '.min';	
			wp_enqueue_script($_handle.'-js', static::$_PluginUrl . 'public/assets/js/revolution.addon.' . static::$_PluginTitle . $_jsPathMin . '.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			
			$_jsPathMin = file_exists(static::$_PluginPath . 'admin/assets/js/revslider-' . static::$_PluginTitle . '-addon-admin.dev.js') ? '.dev' : '';
			wp_enqueue_style($_handle.'-css', $_base . 'css/revslider-' . static::$_PluginTitle . '-addon-admin.css', array(), static::$_Version);
			wp_enqueue_script($_handle.'-addon-admin-js', $_base . 'js/revslider-' . static::$_PluginTitle . '-addon-admin' . $_jsPathMin . '.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			wp_localize_script($_handle.'-addon-admin-js', 'revslider_particlewave_addon', self::get_var() );
			
			wp_enqueue_script('revbuilder-threejs', RS_PLUGIN_URL . 'public/assets/js/libs/three.min.js', array('jquery', 'revbuilder-admin',$_handle.'-js'), RS_REVISION);						
			add_action('revslider_do_ajax', array($this, 'do_ajax'), 10, 2);
		}		
	}

	/**
	 * add images to the export
	 **/
	public function export_adddon_images($data, $slides, $sliderParams){
		$func = new RevSliderFunctions();
		foreach($slides as $slide){
			$layers = $func->get_val($slide, 'layers', array());
			if(!empty($layers)){
				foreach($layers as $layer){
					$image = $func->get_val($layer, array('addOns', 'revslider-particlewave-addon', 'parbackground'), '');
					$particle = $func->get_val($layer, array('addOns', 'revslider-particlewave-addon', 'particle'), '');

					if(!empty($image)) $data['used_images'][$image] = true;
					if(!empty($particle)) {
						if(pathinfo($particle, PATHINFO_EXTENSION) === 'svg'){
							$data['used_svg'][$particle] = true;
						} else {
							$data['used_images'][$particle] = true;
						}
					}
				}
			}
		}
		
		return $data;
	}

	/**
	 * import images if existing
	 **/
	public function import_update_addon_image_urls($data, $slidetype, $image_path) {
		global $wp_filesystem;
		
		$func = new RevSliderFunctions();

		$alias = $func->get_val($data, array('sliderParams', 'alias'), '');
		if(!empty($alias)) {
			$upload_dir = wp_upload_dir();
			$path = '/';

			$layers = $func->get_val($data, 'layers', array());
			if(!empty($layers)){
				foreach($layers as $k => $layer){
					$_images = array(
						'particle' => $func->get_val($layer, array('addOns', 'revslider-particlewave-addon', 'particle'), ''),
						'parbackground' => $func->get_val($layer, array('addOns', 'revslider-particlewave-addon', 'parbackground'), '')
					);

					foreach($_images as $key => $_image){
						if(empty($_image)) continue;
						
						$imported = $func->get_val($data, 'imported', array());
						
						$strip	= false;
						$zimage	= $wp_filesystem->exists($image_path.'images/'.$_image);
						if(!$zimage){
							$zimage	= $wp_filesystem->exists(str_replace('//', '/', $image_path.'images/'.$_image));
							$strip	= true;
						}

						$ext = pathinfo($_image, PATHINFO_EXTENSION);
						if($ext == 'svg'){
							//check if we need to import it, if its available in the zip file
							if(!$zimage) $_image = content_url().$_image;
						}
						
						if($zimage){
							if(!isset($imported['images/'.$_image])){
								//check if we are object folder, if yes, do not import into media library but add it to the object folder
								$uimg = ($strip == true) ? str_replace('//', '/', 'images/'.$_image) : $_image; //pclzip
								
								$file = $upload_dir['basedir'] . $path . $_image;
								$_file = $upload_dir['baseurl'] . $path . $_image;
								
								@mkdir(dirname($file), 0777, true);
								@copy($image_path.'images/'.$_image, $file);
								
								$imported['images/'.$_image] = $_file;
								$_image = $_file;
							}else{
								$_image = $imported['images/'.$_image];
							}
						}

						if(!empty($_image)){
							$data['layers'][$k]['addOns']['revslider-particlewave-addon'][$key] = $_image;
						}
					}
				}
			}
		}

		return $data;
	}

	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-particlewave-addon') {
		
		if($slug === 'revslider-particlewave-addon'){
			
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
			$definitions['editor_settings']['layer_settings']['addons']['particlewave_addon'] = $help['layer'];
		}
		
		return $definitions;
	
	}
	

	public function do_ajax($return = "",$action ="") {
		switch ($action) {
			case 'delete_custom_templates_revslider-particlewave-addon':
				$return = $this->delete_template($_REQUEST["data"]);
				if($return){
					return  __('Particle Wave Template deleted', 'revslider-particlewave-addon');
				}
				else{
					return  __('Particle Wave Template could not be deleted', 'revslider-particlewave-addon');
				}
				break;
			case 'save_custom_templates_revslider-particlewave-addon':
				$return = $this->save_template($_REQUEST["data"]);
				if(empty($return) || !$return){
					return  __('Particle Wave Template could not be saved', 'revslider-particlewave-addon');
				} 
				else {
					return  array( 'message' => __('Particle Wave Template saved', 'revslider-particlewave-addon'), 'data' => array("id" => $return));	
				}
				break;
			default:
				return $return;
				break;
		}
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
		if(update_option( 'revslider_addon_particlewave_templates', $custom )){
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
			if(update_option( 'revslider_addon_particlewave_templates', $custom )){
				return true;	
			}
			else {
				return false;
			}
		}
	}

	/**
	 * Read Custom Templates from WP option, false if not set
	 *
	 * @since    2.0.0
	 */
	private static function get_templates(){
		//load WP option
		$custom = get_option('revslider_addon_particlewave_templates',false);

		return $custom;
	}

	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public static function get_var() {
			
		$_textdomain = 'revslider-particlewave-addon';
		return array(
		
			'bricks' => array(		
				// GENERAL
				'particlewave' => __('Particle Wave', $_textdomain),
				'allsettings' => __('Wave Particle Settings', $_textdomain),
				'wavedatt' => __('Wave Default Attributes', $_textdomain),
				'settings' => __('Settings', $_textdomain),
				'reset' => __('Reset', $_textdomain),
				
				// MENU
				'wpWave' => __('Wave', $_textdomain),
				'wpScene' => __('Scene', $_textdomain),
				'wpParticles' => __('Particles', $_textdomain),
				'wpAnimations' => __('Motion', $_textdomain),
				'wpBalance' => __('Fade', $_textdomain),
				'wpDesign' => __('FX', $_textdomain),
				'partColors' => __('Color Settings', $_textdomain),
				'conMatSettings' => __('Light/Material Settings', $_textdomain),
				'theworld' => __('Adjust Scene', $_textdomain),
				'cameraadjust' => __('Adjust Camera', $_textdomain),
				'conSettings' => __('Connection Options', $_textdomain),
				'partSetup' => __('Particle Options', $_textdomain),
				
				// WAVE
				//DD MENU WAVE ANIMATION
				'waveeffect' => __('Wave Effect', $_textdomain),
				'default' => __('Default', $_textdomain),
				'plainx' => __('Plain X', $_textdomain),
				'plainz' => __('Plain Z', $_textdomain),
				'powerful' => __('Powerful', $_textdomain),
				'funky' => __('Funky', $_textdomain),
				'speed' => __('Speed', $_textdomain),
				'curve' => __('Curve', $_textdomain),	
				'amplitude' => __('Amplitude', $_textdomain),
				'randomizeValue' => __('RND Distances', $_textdomain),	

				// SCENE
				'keepCentered' => __('Keep in Centre', $_textdomain),
				'angle' => __('Angle', $_textdomain),	
				'tilt' => __('Tilt', $_textdomain),		
				'offsetx' => __('Horizontal', $_textdomain),
				'offsety' => __('Vertical', $_textdomain),
				'offsetz' => __('Zoom', $_textdomain),
				'maxdpr' => __('Max DPR', $_textdomain),
				'perfsettings' => __('Performance Settings', $_textdomain),

				// PARTICLES
				'particle' => __('Particle', $_textdomain),
				'svg' => __('SVG', $_textdomain),
				'image' => __('Media', $_textdomain),	
				'particleSize' => __('Size', $_textdomain),
				'particleAmount' => __('Amount', $_textdomain),
				'groShr' => __('Grow/Shrink by', $_textdomain),
				'gap' => __('Gap', $_textdomain),
				'wp_color' => __('Main', $_textdomain),
				'filled' => __('Fill', $_textdomain),
				'bgfit' => __('BG Fit', $_textdomain),
				'cover' => __('Cover', $_textdomain),
				'stretch' => __('Stretch', $_textdomain),
				'contain' => __('Contain', $_textdomain),
				'position' => __('Position', $_textdomain),
				'border' => __('Border', $_textdomain),
				'advancedSet' => __('Advanced', $_textdomain),
				'linesOpacity' => __('Line Opacity', $_textdomain),
				'hexaShift' => __('Hexa Shifter', $_textdomain),

				// BALANCE
				'off' => __('Off', $_textdomain),
				//DD MENU OPACITY Z
				'depthopacity' => __('Options', $_textdomain),
				'sides' => __('Sides', $_textdomain),
				'back' => __('Back', $_textdomain),
				'front' => __('Front', $_textdomain),
				'backfront' => __('Back & Front', $_textdomain),
				'all' => __('All', $_textdomain),
				'intensity' => __('Intensity', $_textdomain),

				// COLOR
				//DD MENU SINGLE/DUAL COLOR
				'coloroptions' => __('Options', $_textdomain),
				'colorgradient' => __('Color/Gradient', $_textdomain),
				'image' => __('Image', $_textdomain),
				'keepcolor' => __('Keep Particle Color', $_textdomain),

				'parbackground' => __('Image', $_textdomain),

				// LINES
				'particlesOn' => __('Show Particles', $_textdomain),
				//DD MENU LINE PATTERN
				'lines' => __('Lines', $_textdomain),
				'polygon' => __('Polygon', $_textdomain),
				'hexa' => __('Hexa', $_textdomain),
				'triangles' => __('Triangles', $_textdomain),
				'boxes' => __('Boxes', $_textdomain),

				'customColorOn' => __('Secondary', $_textdomain),
				'fillColor' => __('Border Color', $_textdomain),
				'fillColor' => __('Lines/Fill', $_textdomain),
				//DD MENU HEXA MATERIAL
				'finish' => __('Finish', $_textdomain),
				'matte' => __('Matte', $_textdomain),
				'shiny' => __('Shiny', $_textdomain),

				// BLENDING MODE
				'blendingmode' => __('Blending Mode', $_textdomain),
				'additive' => __('Additive', $_textdomain),
				'normal' => __('Normal', $_textdomain),
				'custom' => __('Custom', $_textdomain),

				// ANIMATION
				'interactive' => __('Interactive', $_textdomain),
				'automatic' => __('Automatic', $_textdomain),
				'movement' => __('Animation', $_textdomain),
				'path' => __('Path', $_textdomain),
				'pendulum' => __('Pendulum', $_textdomain),
				'loop' => __('Loop', $_textdomain),
				'aniSettings' => __('Animation Settings', $_textdomain),
				//PATH
				'ocean' => __('Ocean', $_textdomain),
				'lost' => __('Lost', $_textdomain),
				'round' => __('Circular', $_textdomain),
				'oceanEdge' => __('Ocean Edge', $_textdomain),
				'oceanEdgeSpin' => __('Ocean Edge Spin', $_textdomain),
				'upAnDown' => __('Up & Down', $_textdomain),
				'quarterPipe' => __('Quarterpipe', $_textdomain),
				'handheld' => __('Handheld', $_textdomain),
				'oldOcean' => __('Waves', $_textdomain),
				'oldLost' => __('The Unknown', $_textdomain),
				'oldRound' => __('Round', $_textdomain),
				//PEND
				'route' => __('Route', $_textdomain),
				//DD MENU PEND	
				'direct' => __('Direct', $_textdomain),
				'rounded' => __('Rounded', $_textdomain),
				'angleEnd' => __('Angle End', $_textdomain),	
				'tiltEnd' => __('Tilt End', $_textdomain),		
				'offsetxEnd' => __('Hor. End', $_textdomain),
				'xposend' => __('X Pos End', $_textdomain),
				'offsetyEnd' => __('Ver. End', $_textdomain),
				'offsetzEnd' => __('Zoom End', $_textdomain),
				//LOOP
				'angleSpeed' => __('Rotation Speed', $_textdomain),	
				'tiltSpeed' => __('Tilt Speed', $_textdomain),		
				'offsetzSpeed' => __('Angle Speed', $_textdomain),

				// INTERACTION
				'intSettings' => __('Interaction Settings', $_textdomain),
				'interaction' => __('Interaction', $_textdomain),
				'mousefollow' => __('Mouse Follow', $_textdomain),
				'scrollbased' => __('Scroll Based', $_textdomain),
				'timelinebased' => __('Timeline Based', $_textdomain),
				'tilt' => __('Tilt', $_textdomain),	
				'rotate' => __('Rotate', $_textdomain),
				'speed' => __('Speed', $_textdomain),
				'intensity' => __('Intensity', $_textdomain),
				'shifty' => __('Shift Y', $_textdomain),

				// DESIGN
				'type' => __('Type', $_textdomain),
				'blur' => __('Blur', $_textdomain),
				'film' => __('Film', $_textdomain),
				'glitch' => __('Glitch', $_textdomain),
				'focus' => __('Focus', $_textdomain),
				'aperture' => __('Aperture', $_textdomain),
				'maxblur' => __('Max Blur', $_textdomain),
				'breathing' => __('Breathing', $_textdomain),
				'minblur' => __('Min Blur', $_textdomain),
				'grayscale' => __('Grayscale', $_textdomain),

				//PRESETS
				'pelib' => __('Particle Effects Library',$_textdomain),
				'parpres' => __('Default Presets',$_textdomain),
				'custompres' => __('Custom Presets',$_textdomain)

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

				/**________________________________________
				 * WAVE
				 */

				'type' => array(
					
					'buttonTitle' => __('Wave Effect', 'revslider-particlewave-addon'), 
					'title' => __('Wave Effect', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.type', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particlewave addon', 'wave effect'), 
					'description' => __("Select the type of wave pattern", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.type']"
						
					)
					
				),
				
				'speed' => array(
					
					'buttonTitle' => __('Waves Speed', 'revslider-particlewave-addon'), 
					'title' => __('Waves Speed', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.speed', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particlewave addon', 'waves speed'), 
					'description' => __("Set wave animation speed", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.speed']"
						
					)
					
				),

				'curve' => array(
					
					'buttonTitle' => __('Wave Curve', 'revslider-particlewave-addon'), 
					'title' => __('Wave Curve', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.curve', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particlewave addon', 'wave curve'), 
					'description' => __("Curve option determines number of curves in particles, higher number creates more curvy waves", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.curve']"
						
					)
					
				),

				'amplitude' => array(
					
					'buttonTitle' => __('Wave Amplitude', 'revslider-particlewave-addon'), 
					'title' => __('Wave Amplitude', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.amplitude', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particlewave addon', 'wave amplitude'), 
					'description' => __("Amplitude option determines how far particles travel from original position", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.amplitude']"
						
					)
					
				),
				
								
				
				'gap' => array(
					
					'buttonTitle' => __('Particle Wave Gap', 'revslider-particlewave-addon'), 
					'title' => __('Particle Wave Gap', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.gap', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particlewave addon', 'waves gap'), 
					'description' => __("Gap option determines gap between particles and spreads them further away from center with higher number", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.gap']"			
					)
					
				),

				'randomizeValue' => array(
					
					'buttonTitle' => __('Random Distances', 'revslider-particlewave-addon'), 
					'title' => __('Random Distances', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.randomizeValue', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particlewave addon', 'particlewave random','RND Distances'), 
					'description' => __("Sets permanent randomized position for particles, higher value will spread particles further away from original position", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.randomizeValue']"			
					)
					
				),

				/**________________________________________
				 * Particles
				 */

				'particlesOn' => array(
					
					'buttonTitle' => __('Show Particles', 'revslider-particlewave-addon'), 
					'title' => __('Show Particles', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.particlesOn', 
					'keywords' => array('addon', 'addons', 'particlewave', 'hide particles', 'show particles'), 
					'description' => __("Show or hide Particles when either 'Triangles' or 'Boxes' are selected", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.particlesOn']"			
					)					
				),

				'connectionType' => array(
					
					'buttonTitle' => __('Lines Connection Design', 'revslider-particlewave-addon'), 
					'title' => __('Lines Connection Design', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.connectionType', 
					'keywords' => array('addon', 'addons', 'particlewave', 'waves lines', 'lines', 'boxes', 'hexa'), 
					'description' => __("Select type of connection between particles to be applied", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.connectionType']"			
					)					
				),

				'wp_select_particle' => array(
					'buttonTitle' => __('Select Particle', 'revslider-particlewave-addon'), 
					'title' => __('Select Particle', 'revslider-particlewave-addon'),
					'helpPath' => 'wp_select_particle', 
					'keywords' => array('addon', 'addons', 'particlewave', 'waves particle', 'particle image'), 
					'description' => __("Select a svg or image as custom particle for waves effect", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'hover' => "wp_select_particle"
					)					
				),

				'particleSize' => array(
					
					'buttonTitle' => __('Particle Size', 'revslider-particlewave-addon'), 
					'title' => __('Particle Size', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.particleSize', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particle size', 'size'), 
					'description' => __("Defines the size of the particle in 3D space", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.particleSize']"			
					)					
				),

				'particleAmount' => array(
					
					'buttonTitle' => __('Particle Amount', 'revslider-particlewave-addon'), 
					'title' => __('Particle Amount', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.particleAmount', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particle amount', 'amount'), 
					'description' => __("Select the amount of particles that are drawn", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.particleAmount']"			
					)					
				),

				'groShr' => array(
					
					'buttonTitle' => __('Grow Shrink by Percent', 'revslider-particlewave-addon'), 
					'title' => __('Grow Shrink by Percent', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.groShr', 
					'keywords' => array('addon', 'addons', 'particlewave', 'grow/shrink by', 'grow', 'shrink'), 
					'description' => __("For 50% the particles will grow to 150% and shrink to 50% of their original size while going up and down in the wave", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.groShr']"			
					)					
				),

				'borderFilled' => array(
					
					'buttonTitle' => __('Fill Faces', 'revslider-particlewave-addon'), 
					'title' => __('Fill Faces', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.borderFilled', 
					'keywords' => array('addon', 'addons', 'particlewave', 'fill', 'fill particle'), 
					'description' => __("If turned on the Faces between 'Triangles' and 'Boxes' will be filled in", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.borderFilled']"			
					)					
				),

				'linesOpacity' => array(
					
					'buttonTitle' => __('Line Opacity', 'revslider-particlewave-addon'), 
					'title' => __('Line Opacity', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.linesOpacity', 
					'keywords' => array('addon', 'addons', 'particlewave', 'line opacity'), 
					'description' => __("Changes the opacity of the line, effecting the perceived thickness of thus", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.linesOpacity']"			
					)					
				),

				'hexaShift' => array(
					
					'buttonTitle' => __('Hexa Shifter', 'revslider-particlewave-addon'), 
					'title' => __('Hexa Shifter', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.hexaShift', 
					'keywords' => array('addon', 'addons', 'particlewave', 'hexa shifter'), 
					'description' => __("Shifts/transforms the hexa pattern", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.hexaShift']"			
					)					
				),

				'particleColor' => array(
					
					'buttonTitle' => __('Color Options', 'revslider-particlewave-addon'), 
					'title' => __('Color Options', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.particleColor', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particle color', 'color'), 
					'description' => __("Select the way the wave gets colored", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.particleColor']"			
					)					
				),

				'wp_color' => array(
					
					'buttonTitle' => __('Main Color', 'revslider-particlewave-addon'), 
					'title' => __('Main Color', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.wp_color', 
					'keywords' => array('addon', 'addons', 'particlewave', 'primary color', 'main color'), 
					'description' => __("Select the main color with which particles get colored. This color will also be used for lines and fill unless secondary color is used for it.", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.wp_color']"			
					)					
				),

				'particlewavebg_svg' => array(
					
					'buttonTitle' => __('Reference Image', 'revslider-particlewave-addon'), 
					'title' => __('Reference Image', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.particlewavebg_svg', 
					'keywords' => array('addon', 'addons', 'particlewave', 'waves image', 'waves color'), 
					'description' => __("Select the image to project on all particles", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal',
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.particlewavebg_svg']"			
					)					
				),

				'secondaryColor' => array(
					
					'buttonTitle' => __('Custom Color On', 'revslider-particlewave-addon'), 
					'title' => __('Custom Color On', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.customColorOn', 
					'keywords' => array('addon', 'addons', 'particlewave', 'secondary color', 'lines color', 'fill color'), 
					'description' => __("If the switch is on it adds a second color to the wave, coloring the lines and faces", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.customColorOn']"			
					)					
				),

				'fillColor' => array(
					
					'buttonTitle' => __('Lines/Fill Color', 'revslider-particlewave-addon'), 
					'title' => __('Lines/Fill Color', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.fillColor', 
					'keywords' => array('addon', 'addons', 'particlewave', 'secondary color', 'lines color', 'fill color'), 
					'description' => __("Select separate color for lines or filled faces", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.fillColor']"			
					)					
				),

				// 'finish' => array(
					
				// 	'buttonTitle' => __('Finish Effect', 'revslider-particlewave-addon'), 
				// 	'title' => __('Finish Effect', 'revslider-particlewave-addon'),
				// 	'helpPath' => 'addOns.revslider-particlewave-addon.finish', 
				// 	'keywords' => array('addon', 'addons', 'particlewave', 'material finish', 'finish'), 
				// 	'description' => __("Select the effect that applies to the faces", 'revslider-particlewave-addon'), 
				// 	'helpStyle' => 'normal', 
				// 	'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
				// 	'video' => false,
				// 	'section' => 'Layer Settings -> ParticleWave',
				// 	'highlight' => array(
				// 		'dependencies' => array('layerselected::shape{{particlewave}}'), 
				// 		'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
				// 		'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
				// 		'focus' => "*[data-r='addOns.revslider-particlewave-addon.finish']"			
				// 	)					
				// ),

				'blending' => array(
					
					'buttonTitle' => __('Blending Mode', 'revslider-particlewave-addon'), 
					'title' => __('Blending Mode', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.blending', 
					'keywords' => array('addon', 'addons', 'particlewave', 'blend mode', 'particles blend'), 
					'description' => __("Sets the way particles/lines blend together in scene.<br/><br/> <ul> <li><strong>Additive: </strong>Increases the intensity of overlapping areas</li> <li><strong>Normal and Custom: </strong> These options do not change the particle color, useful if you want to keep particle color. Both options give similar output except in rare cases using one of normal or custom blending mode may fix edge artifacts around particles.</li> </ul>", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.blending']"			
					)					
				),
				/**________________________________________
				 * SCENE
				 */

				'angle' => array(
					
					'buttonTitle' => __('Angle', 'revslider-particlewave-addon'), 
					'title' => __('Angle', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.angle', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particlewave addon', 'waves angle','scene angle', 'angle'), 
					'description' => __("Rotates the wave clockwise for positive values and anticlockwise for negative values", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.angle']"			
					)					
				),

				'tilt' => array(
					
					'buttonTitle' => __('Tilt', 'revslider-particlewave-addon'), 
					'title' => __('Tilt', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.tilt', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particlewave addon', 'waves tilt', 'scene tilt'), 
					'description' => __("Tilts the wave in and out of the screen", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.tilt']"			
					)
				),
				

				'keepCentered' => array(
					
					'buttonTitle' => __('Keep in Centre', 'revslider-particlewave-addon'), 
					'title' => __('Keep in Centre', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.keepCentered', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particlewave addon', 'keep centered', 'centered scene'), 
					'description' => __("If turned on, the camera will always face the middle of the scene", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.keepCentered']"
					)
				),

				'sx' => array(
					
					'buttonTitle' => __('Horizontal Shift', 'revslider-particlewave-addon'), 
					'title' => __('Horizontal Shift', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.sx', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particlewave addon', 'waves horizontal', 'camera horizontal', 'horizontal'), 
					'description' => __("Shifts the scene and all particles horizontally", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.sx']"			
					)					
				),

				'sy' => array(
					
					'buttonTitle' => __('Vertical Shift', 'revslider-particlewave-addon'), 
					'title' => __('Vertical Shift', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.sy', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particlewave addon', 'waves vertical', 'camera vertical', 'vertical', 'datapoint'), 
					'description' => __("Shifts the scene and all particles vertically", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.sy']"			
					)					
				),

				'sz' => array(
					
					'buttonTitle' => __('Zoom', 'revslider-particlewave-addon'), 
					'title' => __('Zoom', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.sz', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particlewave addon', 'waves zoom', 'camera zoom', 'zoom'), 
					'description' => __("Zooms in for smaller numbers and zooms out for larger numbers", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.sz']"			
					)					
				),

				'dpr' => array(
					
					'buttonTitle' => __('Zoom', 'revslider-particlewave-addon'), 
					'title' => __('Zoom', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.dpr', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particlewave addon', 'performance', 'max dpr', 'dpr'), 
					'description' => __("Set the max device pixel ratio to determine quality of waves, higher value will create big canvas on high DPR devices but can affect performance.", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.dpr']"			
					)					
				),

				/**________________________________________
				 * Fade
				 */

				'fade' => array(
					
					'buttonTitle' => __('Fade Options', 'revslider-particlewave-addon'), 
					'title' => __('Fade Options', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.fade', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particlewave addon', 'waves fade', 'scene fade', 'fade'),
					'description' => __("Select the direction for the particles to fade", 'revslider-particlewave-addon'),
					'helpStyle' => 'normal',
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/',
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.fade']"			
					)					
				),

				'opacityIntensity' => array(
					
					'buttonTitle' => __('Fade Intensity', 'revslider-particlewave-addon'), 
					'title' => __('Fade Intensity', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.opacityIntensity', 
					'keywords' => array('addon', 'addons', 'particlewave', 'particlewave addon', 'waves fade', 'scene fade', 'intensity', 'fade intensity'), 
					'description' => __("Set the fade intensity, higher number shows more particles", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.opacityIntensity']"			
					)					
				),

				/**________________________________________
				 * Motion
				 */

				'movement' => array(
					
					'buttonTitle' => __('Animations Movement', 'revslider-particlewave-addon'), 
					'title' => __('Animations Movement', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.movement', 
					'keywords' => array('addon', 'addons', 'particlewave', 'movement', 'animation'), 
					'description' => __("Select the way the scene should animate", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.movement']"			
					)					
				),

				'animPath' => array(
					
					'buttonTitle' => __('Path Selection', 'revslider-particlewave-addon'), 
					'title' => __('Path Selection', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.animPath', 
					'keywords' => array('addon', 'addons', 'particlewave', 'animation path', 'path'), 
					'description' => __("Select a path to animate scene along it", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.animPath']"			
					)					
				),

				'aniMainSpeed' => array(
					
					'buttonTitle' => __('Animation Speed', 'revslider-particlewave-addon'), 
					'title' => __('Animation Speed', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.aniMainSpeed', 
					'keywords' => array('addon', 'addons', 'particlewave', 'animation speed', 'speed'), 
					'description' => __("Speed at which the scene moves during the animation", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.aniMainSpeed']"			
					)					
				),

				'angleEnd' => array(
					
					'buttonTitle' => __('Angle End Position', 'revslider-particlewave-addon'), 
					'title' => __('Angle End Position', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.angleEnd', 
					'keywords' => array('addon', 'addons', 'particlewave', 'angle end', 'animation angle'), 
					'description' => __("The angle set here will be the end position the scene reaches before swinging back to the starting angle set in the 'Scene' tab", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.angleEnd']"			
					)					
				),

				'tiltEnd' => array(
					
					'buttonTitle' => __('Tilt End Position', 'revslider-particlewave-addon'), 
					'title' => __('Tilt End Position', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.tiltEnd', 
					'keywords' => array('addon', 'addons', 'particlewave', 'tilt end', 'animation tilt'), 
					'description' => __("The tilt angle set here will be the end position the scene reaches before swinging back to the starting tilt angle set in the 'Scene' tab", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.tiltEnd']"			
					)					
				),

				'offsetzEnd' => array(
					
					'buttonTitle' => __('Zoom End Value', 'revslider-particlewave-addon'), 
					'title' => __('Zoom End Value', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.offsetzEnd', 
					'keywords' => array('addon', 'addons', 'particlewave', 'zoom end', 'animation zoom end'), 
					'description' => __("The zoom value set here will be the end zoom the camera reaches before zooming back to the starting zoom value set in the 'Scene' tab", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.offsetzEnd']"			
					)					
				),

				'offsetend' => array(
					
					'buttonTitle' => __('Horizonal Shift End Value', 'revslider-particlewave-addon'), 
					'title' => __('Horizonal Shift End Value', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.offsetend', 
					'keywords' => array('addon', 'addons', 'particlewave', 'horizontal shift'), 
					'description' => __("The shift value set here will be the end position the scene reaches before swinging back to the starting shift value set in the 'Scene' tab", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.offsetend']"			
					)					
				),

				'offsetyEnd' => array(
					
					'buttonTitle' => __('Vertical Shift End Value', 'revslider-particlewave-addon'), 
					'title' => __('Vertical Shift End Value', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.offsetyEnd', 
					'keywords' => array('addon', 'addons', 'particlewave', 'vertical shift'), 
					'description' => __("The shift value set here will be the end position the scene reaches before swinging back to the starting shift value set in the 'Scene' tab", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.offsetyEnd']"			
					)					
				),

				'animRoute' => array(
					
					'buttonTitle' => __('Pendulum Route', 'revslider-particlewave-addon'), 
					'title' => __('Pendulum Route', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.animRoute', 
					'keywords' => array('addon', 'addons', 'particlewave', 'pendulum route'), 
					'description' => __("If 'Rounded' is selected the camera will swing between the given points on a round Path, always looking towards the middle of the scene. If 'Direct' is selected, the camera will travel in a straight path always looking in the same direction", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.animRoute']"			
					)					
				),

				'angleSpeed' => array(
					
					'buttonTitle' => __('Angle Speed', 'revslider-particlewave-addon'), 
					'title' => __('Angle Speed', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.angleSpeed', 
					'keywords' => array('addon', 'addons', 'particlewave', 'angle speed'), 
					'description' => __("Speed at which the scene is continuously rotated around its Z-axis (clockwise/anticlockwise)", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.angleSpeed']"			
					)					
				),

				'tiltSpeed' => array(
					
					'buttonTitle' => __('Tilt Speed', 'revslider-particlewave-addon'), 
					'title' => __('Tilt Speed', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.tiltSpeed', 
					'keywords' => array('addon', 'addons', 'particlewave', 'tilt speed'), 
					'description' => __("Speed at which the scene is continuously rotated around its X-axis (in/out of the screen)", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.tiltSpeed']"			
					)					
				),

				'offsetzSpeed' => array(
					
					'buttonTitle' => __('Rotation Speed', 'revslider-particlewave-addon'), 
					'title' => __('Rotation Speed', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.offsetzSpeed', 
					'keywords' => array('addon', 'addons', 'particlewave', 'rotation speed'), 
					'description' => __("Speed at which the scene is continuously rotated around its Y-axis", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.offsetzSpeed']"			
					)					
				),

				'interaction' => array(
					
					'buttonTitle' => __('Interaction Options', 'revslider-particlewave-addon'), 
					'title' => __('Interaction Options', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.interaction', 
					'keywords' => array('addon', 'addons', 'particlewave', 'interaction options', 'interaction'), 
					'description' => __("Select interaction type <br/><br/> <ul> <li><strong>Mouse Follow: </strong>sets the scene position with respect to mouse position as you  move your mouse on layer, same options are used for parallax effect on mobile devices</li> <li><strong>Scroll Based: </strong>sets the scene position as you scroll</li> <li><strong>Timeline Based: </strong>ties the waves animation to layer timeline, with this option enabled you can set waves to animate either with slide progress or animate back and forth on scroll</li> </ul>", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.interaction']"
					)					
				),

				'pTilt' => array(
					
					'buttonTitle' => __('Waves Interaction Tilt', 'revslider-particlewave-addon'), 
					'title' => __('Waves Interaction Tilt', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.pTilt', 
					'keywords' => array('addon', 'addons', 'particlewave', 'interaction tilt', 'tilt'), 
					'description' => __("Set the tilt amount to tilt scene with interaction", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.pTilt']"
					)					
				),

				'pRotate' => array(
					
					'buttonTitle' => __('Waves Interaction Rotate', 'revslider-particlewave-addon'), 
					'title' => __('Waves Interaction Rotate', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.pRotate', 
					'keywords' => array('addon', 'addons', 'particlewave', 'interaction rotate', 'rotate'), 
					'description' => __("Set the rotation amount to rotate scene with interaction", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.pRotate']"
					)					
				),

				'pSpeed' => array(
					
					'buttonTitle' => __('Waves Interaction Speed', 'revslider-particlewave-addon'), 
					'title' => __('Waves Interaction Speed', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.pSpeed', 
					'keywords' => array('addon', 'addons', 'particlewave', 'interaction speed', 'speed'), 
					'description' => __("Mouse interaction uses basic easing for smooth animation on mouse move, set speed to determine scene movement speed", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.pSpeed']"
					)					
				),

				'pIntensity' => array(
					
					'buttonTitle' => __('Parallax Intensity', 'revslider-particlewave-addon'), 
					'title' => __('Parallax Intensity', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.pIntensity', 
					'keywords' => array('addon', 'addons', 'particlewave', 'interaction intensity', 'parallax intensity'), 
					'description' => __("Determines the mobile parallax intensity", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.pIntensity']"
					)					
				),

				'sbshifty' => array(
					
					'buttonTitle' => __('Waves Shift Y', 'revslider-particlewave-addon'), 
					'title' => __('Waves Shift Y', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.sbshifty', 
					'keywords' => array('addon', 'addons', 'particlewave', 'waves shift y', 'shift y'), 
					'description' => __("Moves the scene with respect scroll position in 3D space", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.sbshifty']"
					)					
				),

				'ppfx' => array(
					
					'buttonTitle' => __('FX Design', 'revslider-particlewave-addon'), 
					'title' => __('FX Design', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.ppfx', 
					'keywords' => array('addon', 'addons', 'particlewave', 'FX Design', 'FX'), 
					'description' => __("Select the visual design to apply to the particles", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.ppfx']"			
					)					
				),

				// 'focus' => array(
					
				// 	'buttonTitle' => __('Bokeh Focus', 'revslider-particlewave-addon'), 
				// 	'title' => __('Bokeh Focus', 'revslider-particlewave-addon'),
				// 	'helpPath' => 'addOns.revslider-particlewave-addon.focus', 
				// 	'keywords' => array('addon', 'addons', 'particlewave', 'Bokeh Focus', 'foxus'), 
				// 	'description' => __("Sets the point of focus along the Z axis (in and out of the screen)", 'revslider-particlewave-addon'), 
				// 	'helpStyle' => 'normal', 
				// 	'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
				// 	'video' => false,
				// 	'section' => 'Layer Settings -> ParticleWave',
				// 	'highlight' => array(						
				// 		'dependencies' => array('layerselected::shape{{particlewave}}'), 
				// 		'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
				// 		'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
				// 		'focus' => "*[data-r='addOns.revslider-particlewave-addon.focus']"			
				// 	)					
				// ),

				// 'aperture' => array(
					
				// 	'buttonTitle' => __('Bokeh Aperture', 'revslider-particlewave-addon'), 
				// 	'title' => __('Bokeh Aperture', 'revslider-particlewave-addon'),
				// 	'helpPath' => 'addOns.revslider-particlewave-addon.aperture', 
				// 	'keywords' => array('addon', 'addons', 'particlewave', 'bokeh aperture', 'aperture'), 
				// 	'description' => __("Sets the virtual aperture of the camera lens. Lower numbers result in more in-focus particles", 'revslider-particlewave-addon'), 
				// 	'helpStyle' => 'normal', 
				// 	'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
				// 	'video' => false,
				// 	'section' => 'Layer Settings -> ParticleWave',
				// 	'highlight' => array(						
				// 		'dependencies' => array('layerselected::shape{{particlewave}}'), 
				// 		'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
				// 		'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
				// 		'focus' => "*[data-r='addOns.revslider-particlewave-addon.aperture']"			
				// 	)					
				// ),

				'maxblur' => array(
					
					'buttonTitle' => __('Max Boheh Blur', 'revslider-particlewave-addon'), 
					'title' => __('Max Boheh Blur', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.maxblur', 
					'keywords' => array('addon', 'addons', 'particlewave', 'bokeh maxblur', 'maxblur'), 
					'description' => __("Sets the maximal blur size for out-of-focus particles", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.maxblur']"			
					)					
				),

				'ppbb' => array(
					
					'buttonTitle' => __('Blur Breating Effect', 'revslider-particlewave-addon'), 
					'title' => __('Blur Breating Effect', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.ppbb', 
					'keywords' => array('addon', 'addons', 'particlewave', 'blur breathing', 'breathing'), 
					'description' => __("if turned on the focus point automatically shifts around to create a breathing effect", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.ppbb']"			
					)					
				),

				'minblur' => array(
					
					'buttonTitle' => __('Minimum Blur Breating', 'revslider-particlewave-addon'), 
					'title' => __('Minimum Blur Breating', 'revslider-particlewave-addon'),
					'helpPath' => 'addOns.revslider-particlewave-addon.minblur', 
					'keywords' => array('addon', 'addons', 'particlewave', 'blur breathing', 'minimum blur'), 
					'description' => __("Set minimum blur while breathing blur is on", 'revslider-particlewave-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/particlewave-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ParticleWave',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{particlewave}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-particlewave-addon", 
						'scrollTo' => '#form_layerinner_revslider-particlewave-addon', 
						'focus' => "*[data-r='addOns.revslider-particlewave-addon.minblur']"			
					)					
				),
	
			)
			
		);
		
	}

}
	
?>