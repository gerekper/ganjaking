<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2022 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnTheClusterBase {
	
	const MINIMUM_VERSION = '6.6.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnTheClusterBase::MINIMUM_VERSION, '>=')) {
		
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
			$update_admin = new RevAddOnTheClusterUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'));
			add_filter('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			add_action('revslider_do_ajax', array($this, 'do_ajax'), 10, 2);	
		}
		
		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		require_once(static::$_PluginPath . 'public/includes/slide.class.php');
		
		new RsTheClusterSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsTheClusterSlideFront(static::$_PluginTitle);
		
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
			$_jsPathMin = file_exists(RS_THECLUSTER_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . static::$_PluginTitle . '.js') ? '' : '.min';	
			wp_enqueue_script($_handle.'-js', static::$_PluginUrl . 'public/assets/js/revolution.addon.' . static::$_PluginTitle . $_jsPathMin . '.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			
			$_jsPathMin = file_exists(static::$_PluginPath . 'admin/assets/js/revslider-' . static::$_PluginTitle . '-addon-admin.dev.js') ? '.dev' : '';
			wp_enqueue_style($_handle.'-css', $_base . 'css/revslider-' . static::$_PluginTitle . '-addon-admin.css', array(), static::$_Version);
			wp_enqueue_script($_handle.'-addon-admin-js', $_base . 'js/revslider-' . static::$_PluginTitle . '-addon-admin' . $_jsPathMin . '.js', array('jquery', 'revbuilder-admin', 'revbuilder-threejs'), static::$_Version, true);
			wp_localize_script($_handle.'-addon-admin-js', 'revslider_thecluster_addon', self::get_var() );
			
			wp_enqueue_script('revbuilder-threejs', RS_PLUGIN_URL . 'public/assets/js/libs/three.min.js', array('jquery', 'revbuilder-admin',$_handle.'-js'), RS_REVISION);
		}		
	}

	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-thecluster-addon') {
		
		if($slug === 'revslider-thecluster-addon'){
			
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
			$definitions['editor_settings']['layer_settings']['addons']['thecluster_addon'] = $help['layer'];
		}
		
		return $definitions;
	
	}

	public function do_ajax($return = "",$action ="") {
		switch ($action) {
			case 'delete_custom_templates_revslider-thecluster-addon':
				$return = $this->delete_template($_REQUEST["data"]);
				if($return){
					return  __('The Cluster Template deleted', 'revslider-thecluster-addon');
				}
				else{
					return  __('The Cluster Template could not be deleted', 'revslider-thecluster-addon');
				}
				break;
			case 'save_custom_templates_revslider-thecluster-addon':
				$return = $this->save_template($_REQUEST["data"]);
				if(empty($return) || !$return){
					return  __('The Cluster Template could not be saved', 'revslider-thecluster-addon');
				} 
				else {
					return  array( 'message' => __('The Cluster Template saved', 'revslider-thecluster-addon'), 'data' => array("id" => $return));	
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
		if(update_option( 'revslider_addon_thecluster_templates', $custom )){
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
			if(update_option( 'revslider_addon_thecluster_templates', $custom )){
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
		$custom = get_option('revslider_addon_thecluster_templates',false);

		return $custom;
	}

	
	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public static function get_var() {
			
		$_textdomain = 'revslider-thecluster-addon';
		return array(
			'custom_templates' => self::get_templates(),
			'bricks' => array(		
				// GENERAL
				'thecluster' => __('The Cluster', $_textdomain),
				'clusterdatt' => __('The Cluster Setup', $_textdomain),
				'restartAni' => __('Restart Animation', $_textdomain),
				'pelib' => __('Cluster FX Library',$_textdomain),
				'parpres' => __('Default Presets',$_textdomain),
				'custompres' => __('Custom Presets',$_textdomain),
				'gravpoint' => __('Gravity Point',$_textdomain),
				'lifeGradientHelper' => __('Colors from the Main Color/Gradient are used across the Lifetime.',$_textdomain),
				'colorImageMixValueHelper' => __('Mix determines how much the Color and Image Color get mixed.',$_textdomain),
				'periodicSpawnHelper' => __('Cluster particles gets spawned in groups with a given intensity',$_textdomain),
				'gravPointsselectionHelper' => __('Click here to Setup and Customize Gravity Points.',$_textdomain),
				'movementHelper' => __('Camera/Scene Animation Options arent available with Mouse based Individual Gravity selected',$_textdomain),
				'gravityModalhelper' => __('Turn on and off Specific Gravity Points using the switches and position via XYZ-Coordinates. Mass value determines how much a Point attracts or defects particles around it (positive -> attraction; negative -> deflection).',$_textdomain),

				//Menus
				'main' => __('Main', $_textdomain),
				'scene' => __('Scene', $_textdomain),
				'grav' => __('Gravity', $_textdomain),
				'spawn' => __('Spawn', $_textdomain),
				'particle' => __('Particles', $_textdomain),
				'movement' => __('Movement', $_textdomain),
				'interaction' => __('Interactions', $_textdomain),
				'vfx' => __('VFX', $_textdomain),

				//Main
				'speed' => __('Speed', $_textdomain),
				'mass' => __('Force', $_textdomain),
				'size' => __('Size', $_textdomain),
				'spawnDiameter' => __('Spawn Width', $_textdomain),
				'amount' => __('Amount', $_textdomain),
				'spawnForm' => __('Spawn Form', $_textdomain),
				'toCentre' => __('Towards Centre', $_textdomain),
				'direction' => __('Vector', $_textdomain),
				'noiseOn' => __('Noise', $_textdomain),
				'noiseSetup' => __('Noise Setup', $_textdomain),
				'noiseAmount' => __('Influence', $_textdomain),

				//Scene
				'adjScene' => __('Adjust Scene', $_textdomain),
				'showHelper' => __('Show Helper', $_textdomain),
				'angle' => __('Angle', $_textdomain),
				'tilt' => __('Tilt', $_textdomain),
				'adjCamera' => __('Adjust Camera', $_textdomain),
				'keepCentered' => __('Keep in Centre', $_textdomain),
				'offsetx' => __('Horizontal', $_textdomain),
				'offsety' => __('Vertical', $_textdomain),
				'offsetz' => __('Zoom', $_textdomain),
				'reset' => __('Reset', $_textdomain),
				'perfsettings' => __('Performance Settings', $_textdomain),
				'maxdpr' => __('Max DPR', $_textdomain),

				//Gravity
				'grav1Box' => __('Gravity Point 1', $_textdomain),
				'grav2Box' => __('Gravity Point 2', $_textdomain),
				'grav3Box' => __('Gravity Point 3', $_textdomain),
				'one' => __('1', $_textdomain),
				'two' => __('2', $_textdomain),
				'three' => __('3', $_textdomain),
				'x' => __('X', $_textdomain),
				'y' => __('Y', $_textdomain),
				'z' => __('Z', $_textdomain),
				'limitMovement' => __('Limit Cluster', $_textdomain),
				'limitMovementValue' => __('Limit Amount', $_textdomain),
				'gravPointsVisible' => __('Edit Points', $_textdomain),

				//Spawn
				'spawnInit' => __('Spawn Position', $_textdomain),
				'spawnDir' => __('Initial Direction', $_textdomain),
				'spawnAccVec' => __('Direction', $_textdomain),
				'onGlobe' => __('On Globe', $_textdomain),
				'inGlobe' => __('In Globe', $_textdomain),
				'random' => __('Random', $_textdomain),
				'random3D' => __('Random 3D', $_textdomain),
				'onBorder' => __('On Border', $_textdomain),
				'inLine' => __('In Line', $_textdomain),
				'inCircle' => __('In Circle', $_textdomain),
				'normalCW' => __('Spin CW', $_textdomain),
				'normalCCW' => __('Spin CCW', $_textdomain),
				'resetSpawn' => __('Set to Zero', $_textdomain),
				'mirroredOn' => __('Mirror', $_textdomain),

				//Particles
				'tc_mainColor' => __('Main Color', $_textdomain),
				'tcParticleColor' => __('Design', $_textdomain),
				'randomizeSize' => __('Random Size', $_textdomain),
				'randomizeOpacity' => __('Random Opacity', $_textdomain),
				'randSizeMin' => __('Min', $_textdomain),
				'randSizeMax' => __('Max', $_textdomain),
				'colorImageMixValue' => __('Color Mix', $_textdomain),

				//Movement
				'tcMovementAni' => __('Camera/Scene Animation', $_textdomain),
				'type' => __('Type', $_textdomain),
				'loop' => __('Loop', $_textdomain),
				'tcMovementAniSetup' => __('Setup', $_textdomain),
				'off' => __('Off', $_textdomain),
				'tcMovementPattern' => __('Particle Behaviour', $_textdomain),
				'continuous' => __('Continuous', $_textdomain),
				'sinus' => __('Sinus', $_textdomain),
				'lifetime' => __('Lifetime', $_textdomain),
				'lifeLength' => __('Life Length', $_textdomain),
				'tcMovementGrav' => __('Gravity Point Animation', $_textdomain),
				'gravP1MoveSel' => __('Grav Point 1', $_textdomain),
				'gravP2MoveSel' => __('Grav Point 2', $_textdomain),
				'gravP3MoveSel' => __('Grav Point 3', $_textdomain),
				'xy' => __('XY Plane', $_textdomain),
				'yz' => __('YZ Plane', $_textdomain),
				'xz' => __('XZ Plane', $_textdomain),
				'lifetimeAlphaChange' => __('Transition', $_textdomain),
				'fadeIn' => __('Fade In', $_textdomain),
				'fadeOut' => __('Fade Out', $_textdomain),
				'fadeInOut' => __('Fade In & Out', $_textdomain),
				'lifeTimeGradient' => __('Life Gradient', $_textdomain),
				'periodicSpawn' => __('Periodic Spawn', $_textdomain),
				'periodicSpawnValue' => __('Respawn Intensity', $_textdomain),
				
				//Interaction
				'indivGrav' => __('Individual Gravity', $_textdomain),
				'indivRota' => __('Individual Rotation', $_textdomain),
				'indivRotaRet' => __('Indiv Rotation & Return', $_textdomain),
				'gravFollowMouseValue' => __('Intensity', $_textdomain),
				'gravRotateMouseValue' => __('Angle', $_textdomain),
				'gravRotateReturnValue' => __('Return Speed', $_textdomain),

				//VFX
				'bokeh' => __('Bokeh', $_textdomain),
				'sfxBreathing' => __('Breathing', $_textdomain),
				'minBlur' => __('Min', $_textdomain),
				'maxBlur' => __('Max', $_textdomain),

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
	
				// ____MAIN_____

				'speed' => array(
					
					'buttonTitle' => __('Speed', 'revslider-thecluster-addon'), 
					'title' => __('Speed', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.speed', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Speed'), 
					'description' => __("Sets the main animation & movement speed for the Cluster", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.speed']"
						
					)
					
				),

				'limitMovement' => array(
					
					'buttonTitle' => __('Limit Cluster', 'revslider-thecluster-addon'), 
					'title' => __('Limit Cluster', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.limitMovement', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Limit Cluster'), 
					'description' => __("Limits the movement area of the Cluster to a globe around the midpoint of the gravity Points", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.limitMovement']"
						
					)
					
				),

				'limitMovementValue' => array(
					
					'buttonTitle' => __('Limit Amount', 'revslider-thecluster-addon'), 
					'title' => __('Limit Amount', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.limitMovementValue', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Limit Amount'), 
					'description' => __("Sets the maximal distance the Particles can move away from the gravity points midpoint", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.limitMovementValue']"
						
					)
					
				),

				'noiseOn' => array(
					
					'buttonTitle' => __('Noise', 'revslider-thecluster-addon'), 
					'title' => __('Noise', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.noiseOn', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Noise'), 
					'description' => __("Turns on noise generation, enabling a different movement pattern", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.noiseOn']"
						
					)
					
				),

				'noiseAmount' => array(
					
					'buttonTitle' => __('Influence', 'revslider-thecluster-addon'), 
					'title' => __('Influence', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.noiseAmount', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Influence'), 
					'description' => __("Sets the influence of the Noise movement on the particles", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.noiseAmount']"
						
					)
					
				),

				// ____SCENE_____

				'showHelper' => array(
					
					'buttonTitle' => __('Show Helper', 'revslider-thecluster-addon'), 
					'title' => __('Show Helper', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.showHelper', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Show Helper'), 
					'description' => __("When enabled, creates a visual representation for the axis directions (X - Red, Y - Green, Z - Blue", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.showHelper']"
						
					)
					
				),

				'angle' => array(
					
					'buttonTitle' => __('Angle', 'revslider-thecluster-addon'), 
					'title' => __('Angle', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.angle', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Angle'), 
					'description' => __("Sets the Scene rotation arount the Z axis (Clockwise/Anticlockwise)", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.angle']"
						
					)
					
				),

				'tilt' => array(
					
					'buttonTitle' => __('Tilt', 'revslider-thecluster-addon'), 
					'title' => __('Tilt', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.tilt', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Tilt'), 
					'description' => __("Sets the Scene rotation arount the X axis (Forwards/Backwards)", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.tilt']"
						
					)
					
				),

				'keepCentered' => array(
					
					'buttonTitle' => __('Keep in Centre', 'revslider-thecluster-addon'), 
					'title' => __('Keep in Centre', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.keepCentered', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Keep in Centre'), 
					'description' => __("When turned on, keeps the midpoint (0,0,0) in the center of the layer visualy", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.keepCentered']"
						
					)
					
				),

				'offsetx' => array(
					
					'buttonTitle' => __('Horizontal', 'revslider-thecluster-addon'), 
					'title' => __('Horizontal', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.offsetx', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Horizontal'), 
					'description' => __("Offsets the Camera horizontaly from the center Point (0,0,0)", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.offsetx']"
						
					)
					
				),

				'offsety' => array(
					
					'buttonTitle' => __('Vertical', 'revslider-thecluster-addon'), 
					'title' => __('Vertical', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.offsety', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Vertical'), 
					'description' => __("Offsets the Camera verticaly from the center Point (0,0,0)", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.offsety']"
						
					)
					
				),

				'offsetz' => array(
					
					'buttonTitle' => __('Zoom', 'revslider-thecluster-addon'), 
					'title' => __('Zoom', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.offsetz', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Zoom'), 
					'description' => __("Offsets camera along the Z Axis creating a Zoom effect", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.offsetz']"
						
					)
					
				),

				'maxdpr' => array(
					
					'buttonTitle' => __('Max DPR', 'revslider-thecluster-addon'), 
					'title' => __('Max DPR', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.maxdpr', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Max DPR'), 
					'description' => __("Optimizes animation for playback on less powerfull devices", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.maxdpr']"
						
					)
					
				),

				// ____GRAV_____

				'editGravPoints' => array(
					
					'buttonTitle' => __('Edit Gravity Points', 'revslider-thecluster-addon'), 
					'title' => __('Edit Gravity Points', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.editGravPoints', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Edit Gravity Points'), 
					'description' => __("Opens the gravity point editing window", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.editGravPoints']"
						
					)
					
				),

				//TODO: Add Gravity Point XYZ and Mass?

				// ____SPAWN_____

				'spawnDiameter' => array(
					
					'buttonTitle' => __('Spawn Width', 'revslider-thecluster-addon'), 
					'title' => __('Spawn Width', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.spawnDiameter', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Spawn Width'), 
					'description' => __("Sets the Diameter of the Initial spawn form", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.spawnDiameter']"
						
					)
					
				),

				'amount' => array(
					
					'buttonTitle' => __('Amount', 'revslider-thecluster-addon'), 
					'title' => __('Amount', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.amount', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Amount'), 
					'description' => __("Amount of all Particles in Cluster", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.amount']"
						
					)
					
				),

				'spawnForm' => array(
					
					'buttonTitle' => __('Spawn Form', 'revslider-thecluster-addon'), 
					'title' => __('Spawn Form', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.spawnForm', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Spawn Form'), 
					'description' => __("3D Form in which Particles get spawned initialy", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.spawnForm']"
						
					)
					
				),

				'mirroredOn' => array(
					
					'buttonTitle' => __('Mirror', 'revslider-thecluster-addon'), 
					'title' => __('Mirror', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.mirroredOn', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Mirror'), 
					'description' => __("Select how many the cluster should apear mirrored, mirror plane is allways the largest central plane", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.mirroredOn']"
						
					)
					
				),

				'spawnInit' => array(
					
					'buttonTitle' => __('Spawn Position', 'revslider-thecluster-addon'), 
					'title' => __('Spawn Position', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.spawnInit', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Spawn Position'), 
					'description' => __("Sets the initial spawn midpoint for the selected spawn form", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.spawnInit']"
						
					)
					
				),

				'spawnAccVec' => array(
					
					'buttonTitle' => __('Direction', 'revslider-thecluster-addon'), 
					'title' => __('Direction', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.spawnAccVec', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Direction'), 
					'description' => __("Sets type of initial movement of particles", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.spawnAccVec']"
						
					)
					
				),

				'spawnDir' => array(
					
					'buttonTitle' => __('Initial Direction', 'revslider-thecluster-addon'), 
					'title' => __('Initial Direction', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.spawnDir', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Initial Direction'), 
					'description' => __("Sets custom movement vector direction", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.spawnDir']"
						
					)
					
				),

				// ____PARTICLE_____

				'size' => array(
					
					'buttonTitle' => __('Size', 'revslider-thecluster-addon'), 
					'title' => __('Size', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.size', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Size'), 
					'description' => __("Sets particle Size/Diameter", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.size']"
						
					)
					
				),

				'randomizeSize' => array(
					
					'buttonTitle' => __('Random Size', 'revslider-thecluster-addon'), 
					'title' => __('Random Size', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.randomizeSize', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Random Size'), 
					'description' => __("If turned on, varies the particle size", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.randomizeSize']"
						
					)
					
				),

				'randSizeMin' => array(
					
					'buttonTitle' => __('Min', 'revslider-thecluster-addon'), 
					'title' => __('Min', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.randSizeMin', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Min'), 
					'description' => __("Minimum scale of the original size that is allowed when random size is turned on", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.randSizeMin']"
						
					)
					
				),

				'randSizeMax' => array(
					
					'buttonTitle' => __('Max', 'revslider-thecluster-addon'), 
					'title' => __('Max', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.randSizeMax', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Max'), 
					'description' => __("Maximum scale of the original size that is allowed when random size is turned on", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.randSizeMax']"
						
					)
					
				),

				'tc_mainColor' => array(
					
					'buttonTitle' => __('Main Color', 'revslider-thecluster-addon'), 
					'title' => __('Main Color', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.tc_mainColor', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Main Color'), 
					'description' => __("Cluster color, select either single color or gradient", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.tc_mainColor']"
						
					)
					
				),

				'colorImageMixValue' => array(
					
					'buttonTitle' => __('Color Mix', 'revslider-thecluster-addon'), 
					'title' => __('Color Mix', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.colorImageMixValue', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Color Mix'), 
					'description' => __("Valus determines with which ratio the original color is mixed with the selected color (1% -> the selected color is shown; 100% -> the original particle color is shown)", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.colorImageMixValue']"
						
					)
					
				),

				'randomizeOpacity' => array(
					
					'buttonTitle' => __('Random Opacity', 'revslider-thecluster-addon'), 
					'title' => __('Random Opacity', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.randomizeOpacity', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Random Opacity'), 
					'description' => __("Sets a random opacity value for each particle", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.randomizeOpacity']"
						
					)
					
				),

				// ____MOVEMENT_____

				'animationSel' => array(
					
					'buttonTitle' => __('Type', 'revslider-thecluster-addon'), 
					'title' => __('Type', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.animationSel', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Type'), 
					'description' => __("Select camera animation", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.animationSel']"
						
					)
					
				),

				'aniX' => array(
					
					'buttonTitle' => __('X', 'revslider-thecluster-addon'), 
					'title' => __('X', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.aniX', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'X'), 
					'description' => __("Sets speed at which camera turnes around the X Axis", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.aniX']"
						
					)
					
				),

				'aniY' => array(
					
					'buttonTitle' => __('Y', 'revslider-thecluster-addon'), 
					'title' => __('Y', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.aniY', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Y'), 
					'description' => __("Sets speed at which camera turnes around the Y Axis", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.aniY']"
						
					)
					
				),

				'aniZ' => array(
					
					'buttonTitle' => __('Z', 'revslider-thecluster-addon'), 
					'title' => __('Z', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.aniZ', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Z'), 
					'description' => __("Sets speed at which camera turnes around the Z Axis", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.aniZ']"
						
					)
					
				),

				'patternSel' => array(
					
					'buttonTitle' => __('Type', 'revslider-thecluster-addon'), 
					'title' => __('Type', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.patternSel', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Type'), 
					'description' => __("Customizes lifetime movement behaviour. Continous spawnes particles once and keeps them moving. Sinus also spawnes particles once but the time value changes according to a sinus wave. Lifetime respawnes particles after a given time period", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.patternSel']"
						
					)
					
				),

				'lifetimeAlphaChange' => array(
					
					'buttonTitle' => __('Transition', 'revslider-thecluster-addon'), 
					'title' => __('Transition', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.lifetimeAlphaChange', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Transition'), 
					'description' => __("Sets how the particles opacity changes over its lifetime", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.lifetimeAlphaChange']"
						
					)
					
				),

				'lifeLength' => array(
					
					'buttonTitle' => __('Life Length', 'revslider-thecluster-addon'), 
					'title' => __('Life Length', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.lifeLength', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Life Length'), 
					'description' => __("Sets life length of a particle", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.lifeLength']"
						
					)
					
				),

				'lifeTimeGradient' => array(
					
					'buttonTitle' => __('Life Gradient', 'revslider-thecluster-addon'), 
					'title' => __('Life Gradient', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.lifeTimeGradient', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Life Gradient'), 
					'description' => __("If selected, over the particle lifetime the color will cycle through the selected Gradient", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.lifeTimeGradient']"
						
					)
					
				),

				'periodicSpawn' => array(
					
					'buttonTitle' => __('Periodic Spawn', 'revslider-thecluster-addon'), 
					'title' => __('Periodic Spawn', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.periodicSpawn', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Periodic Spawn'), 
					'description' => __("If turned on, particles will spawn in bunched at the given intervalls, else the particles will respawn randomly", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.periodicSpawn']"
						
					)
					
				),

				'periodicSpawnValue' => array(
					
					'buttonTitle' => __('Respawn Intensity', 'revslider-thecluster-addon'), 
					'title' => __('Respawn Intensity', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.periodicSpawnValue', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Respawn Intensity'), 
					'description' => __("Sets the respawn intervall at which groups of particles will respawn at once", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.periodicSpawnValue']"
						
					)
					
				),

				'gravP1MoveSel' => array(
					
					'buttonTitle' => __('Grav Point 1', 'revslider-thecluster-addon'), 
					'title' => __('Grav Point 1', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.gravP1MoveSel', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Grav Point 1'), 
					'description' => __("Sets a movement pattern for Gravity Point 1 (allways a circular path on the selected plane)", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.gravP1MoveSel']"
						
					)
					
				),

				'gravP2MoveSel' => array(
					
					'buttonTitle' => __('Grav Point 2', 'revslider-thecluster-addon'), 
					'title' => __('Grav Point 2', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.gravP2MoveSel', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Grav Point 2'), 
					'description' => __("Sets a movement pattern for Gravity Point 2 (allways a circular path on the selected plane)", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.gravP2MoveSel']"
						
					)
					
				),

				'gravP3MoveSel' => array(
					
					'buttonTitle' => __('Grav Point 3', 'revslider-thecluster-addon'), 
					'title' => __('Grav Point 3', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.gravP3MoveSel', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Grav Point 3'), 
					'description' => __("Sets a movement pattern for Gravity Point 3 (allways a circular path on the selected plane)", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.gravP3MoveSel']"
						
					)
					
				),

				// ____INTERACTION_____

				'gravFollowMouse' => array(
					
					'buttonTitle' => __('Individual Gravity', 'revslider-thecluster-addon'), 
					'title' => __('Individual Gravity', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.gravFollowMouse', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Individual Gravity'), 
					'description' => __("Individual Gravity: A new gravity Point gets created which moves with the mouse cursor. Individual Rotation: Scene Rotates along with Mouse movement. Indiv Rotation & Return: Scene Rotates along with Mouse movement and returns to origin at set speed", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.gravFollowMouse']"
						
					)
					
				),

				'gravFollowMouseValue' => array(
					
					'buttonTitle' => __('Intensity', 'revslider-thecluster-addon'), 
					'title' => __('Intensity', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.gravFollowMouseValue', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Intensity'), 
					'description' => __("Sets mouses gravity point intensity", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.gravFollowMouseValue']"
						
					)
					
				),

				'gravRotateMouseValue' => array(
					
					'buttonTitle' => __('Angle', 'revslider-thecluster-addon'), 
					'title' => __('Angle', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.gravRotateMouseValue', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Angle'), 
					'description' => __("Sets the maximal rotation angle for the scene with mouse movement", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.gravRotateMouseValue']"
						
					)
					
				),

				'gravRotateReturnValue' => array(
					
					'buttonTitle' => __('Return Speed', 'revslider-thecluster-addon'), 
					'title' => __('Return Speed', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.gravRotateReturnValue', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Return Speed'), 
					'description' => __("Sets return rotation speed for the scene", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.gravRotateReturnValue']"
						
					)
					
				),

				// ____VFX_____

				'vfxSelector' => array(
					
					'buttonTitle' => __('Type', 'revslider-thecluster-addon'), 
					'title' => __('Type', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.vfxSelector', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Type'), 
					'description' => __("If turned on a Bokeh effect gets applied to the Cluster", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.vfxSelector']"
						
					)
					
				),

				'sfxBreathing' => array(
					
					'buttonTitle' => __('Breathing', 'revslider-thecluster-addon'), 
					'title' => __('Breathing', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.sfxBreathing', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Breathing'), 
					'description' => __("If turned on, shifts focus point back and forth", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.sfxBreathing']"
						
					)
					
				),

				'minBlur' => array(
					
					'buttonTitle' => __('Min', 'revslider-thecluster-addon'), 
					'title' => __('Min', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.minBlur', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Min'), 
					'description' => __("Sets the minimal blur amount applied to the Particle", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.minBlur']"
						
					)
					
				),

				'maxBlur' => array(
					
					'buttonTitle' => __('Max', 'revslider-thecluster-addon'), 
					'title' => __('Max', 'revslider-thecluster-addon'),
					'helpPath' => 'addOns.revslider-thecluster-addon.maxBlur', 
					'keywords' => array('addon', 'addons', 'thecluster', 'thecluster addon', 'Max'), 
					'description' => __("Sets the maximal blur amount applied to the Particle", 'revslider-thecluster-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/thecluster-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> TheCluster',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{thecluster}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-thecluster-addon", 
						'scrollTo' => '#form_layerinner_revslider-thecluster-addon', 
						'focus' => "*[data-r='addOns.revslider-thecluster-addon.maxBlur']"
						
					)
					
				),

			)
			
		);
		
	}

}
	
?>