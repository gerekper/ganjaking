<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2021 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnTransitionpackBase {
	
	const MINIMUM_VERSION = '6.5.6';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnTransitionpackBase::MINIMUM_VERSION, '>=')) {
		
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
			$update_admin = new RevAddOnTransitionpackUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'));
			add_filter('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
		}
		
		add_filter('revslider_data_get_base_transitions', array('RsAddOnTransitionpackBase', 'add_transitions'), 10, 1);
		
		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		require_once(static::$_PluginPath . 'public/includes/slide.class.php');
		
		new RsTransitionpackSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsTransitionpackSlideFront(static::$_PluginTitle);
		
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
			$_jsPathMin = file_exists(RS_TRANSITIONPACK_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . static::$_PluginTitle . '.js') ? '' : '.min';	
			wp_enqueue_script($_handle.'-js', static::$_PluginUrl . 'public/assets/js/revolution.addon.' . static::$_PluginTitle . $_jsPathMin . '.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			
			$_jsPathMin = file_exists(static::$_PluginPath . 'admin/assets/js/revslider-' . static::$_PluginTitle . '-addon-admin.dev.js') ? '.dev' : '';
			wp_enqueue_style($_handle.'-css', $_base . 'css/revslider-' . static::$_PluginTitle . '-addon-admin.css', array(), static::$_Version);
			wp_enqueue_script($_handle.'-addon-admin-js', $_base . 'js/revslider-' . static::$_PluginTitle . '-addon-admin' . $_jsPathMin . '.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			wp_localize_script($_handle.'-addon-admin-js', 'revslider_transitionpack_addon', self::get_var() );
			
			wp_enqueue_script('revbuilder-threejs', RS_PLUGIN_URL . 'public/assets/js/libs/three.min.js', array('jquery', 'revbuilder-admin',$_handle.'-js'), RS_REVISION);
			
			$shaders = self::get_shaders();
			echo '<script type="text/javascript">'."\n";
			echo 'window.TP_SHDRS = window.TP_SHDRS || {};'."\n";
			foreach($shaders as $name => $values){
				echo 'window.TP_SHDRS.'.$name.' = window.TP_SHDRS.'.$name.' || '.$values.";\n";
			}
			echo '</script>'."\n";
		}
		
	}
	
	public static function add_transitions($transitions){
		//as the data is raw, it needs to be changed to be an array
		$transitions = json_decode($transitions, true);

		$newtransition = array('tpack' => array('icon' => 'motion_photos_auto', 'eclass' =>'tpacktrans'));
		$transitions = (!empty($transitions)) ? array_merge($newtransition,$transitions) : $newtransition;

		/*$transitions['tpack']['cube'] = array(		
			'tp_webgl_simple_cube' => array('eng' => 'transitionPack',
				'e' => 'cube',
				'title' => 'Single Cube Rotate','speed' => 1200,'d' => 20,'addOns' => array(
				'tpack' => array('col' => 1,'row' => 1,'rx' => 15,'rz' => 0,'ry' => 0,'gz' => 100,'gy' => 10,'sr' => -1,'o' => 1,'ie' => 'sine.inOut','ige' => 'sine.inOut'))),							
			
			'tp_webgl_crazy_slots' => array('eng' => 'transitionPack',
				'e' => 'cube',
				'title' => 'Crazy Cube Columns','speed' => 3000,'d' => 20,'addOns' => array(
				'tpack' => array('col' => 10,'row' => 1,'rx' => 180,'rz' => 170,'ry' => 180,'gz' => 200,'sr' => 1,'o' => 1,'ie' => 'elastic.out','ige' => 'power2.inOut'))),			
			
			'tp_webgl_simple_slots' => array('eng' => 'transitionPack',
				'e' => 'cube',
				'title' => 'Cube Columns Lite','speed' => 1000,'d' => 20,'addOns' => array(
				'tpack' => array('col' => 10,'row' => 1,'rx' => 5,'rz' => 0,'ry' => -5,'sx' => 0.2,'gz' => 80,'gy' => 20,'sr' => 1,'o' => 1,'ie' => 'power2.inOut','ige' => 'power2.inOut'))),
			
			'tp_webgl_simple_slots_strong' => array('eng' => 'transitionPack',
					'e' => 'cube',
					'title' => 'Cube Columns Blur','speed' => 1000,'d' => 20,
					'addOns' => array('tpack' => array('col' => 7,'row' => 1,'rx' => 35,'rz' => 0,'ry' => -5,'sx' => 0.7,'gz' => 80,'gy' => 20,'sr' => -3,'o' => 1,'ie' => 'power2.inOut','ige' => 'power2.inOut','pp' => 'blur', 'ppbt' => 'd3', 'ppbf' => 50,'ppbm' => 50,'ppba' => 10))),
			
			'tp_webgl_simple_slots_row' => array('eng' => 'transitionPack',
				'e' => 'cube',
				'title' => 'Cube Rows Lite','speed' => 1000,'d' => 20,'addOns' => array(
				'tpack' => array('row' => 5,'col' => 1,'rx' => 5,'rz' => 0,'ry' => -5,'sx' => 1, 'sy' => 0.2, 'gz' => 80,'gy' => 20,'sr' => 1,'o' => 1,'ie' => 'power2.inOut','ige' => 'power2.inOut'))),
			
			'tp_webgl_crazy_slots_rows' => array('eng' => 'transitionPack',
					'e' => 'cube',
					'title' => 'Cube Rows Blur','speed' => 1000,'d' => 20,'addOns' => array(
					'tpack' => array('col' => 1,'row' => 7,'rx' => 20,'rz' => 0,'ry' => 180,'sy' => 0.5, 'gz' => 180,'gy' => 20,'sr' => 2,'o' => 1,'ie' => 'power0.inOut','ige' => 'power2.inOut',	'pp' => 'blur', 'ppbt' => 'd3', 'ppbf' => 50, 'ppbm' => 50, 'ppba' => 10))),

			'tp_webgl_twist_simple' => array(
				'eng' => 'transitionPack',
				'e' => 'twist',
				'title' => 'Simple Twist', 'speed' => 1300, 'd' => 20, 'addOns' => array(
				'tpack' => array( 'ef' => 'none', 'ie' => 'sine.inOut', 'twe' => 'simple', 'twa' => 0, 'twv' => 230, 'twz' => 30, 'twd' => 'left', 'twdi' => 30,'rx' => 0,'ry' => 0,'rz' => 0, 'twf' => 'rgba(0., 0., 0., 0.7)', 'tws' => 'rgba(0., 0., 0., 0.7)' ))),
			'tp_webgl_twist_flip' => array(
				'eng' => 'transitionPack',
				'e' => 'twist',
				'title' => 'Twist Flip', 'speed' => 2500, 'd' => 20, 'addOns' => array(
				'tpack' => array( 'ef' => 'none', 'ie' => 'sine.inOut', 'twe' => 'twistwave', 'twa' => 0, 'twv' => 410, 'twz' => 30, 'twd' => 'left', 'twdi' => 60,'rx' => 90,'ry' => 190,'rz' => 10, 'twf' => 'rgba(0., 0., 0., 0.7)', 'tws' => 'rgba(0., 0., 0., 0.7)' ))),
			'tp_webgl_twist_wave_left' => array(
				'eng' => 'transitionPack',
				'e' => 'twist',
				'title' => 'Twist Wave to Left', 'speed' => 2500, 'd' => 20, 'addOns' => array(
				'tpack' => array( 'ef' => 'none', 'ie' => 'sine.inOut', 'twe' => 'twistwave', 'twa' => 0, 'twv' => 230, 'twz' => 30, 'twd' => 'left', 'twdi' => 70, 'twc' => false, 'rx' => 0,'ry' => 0,'rz' => 0, 'twf' => 'rgba(0., 0., 0., 0.7)', 'tws' => 'rgba(0., 0., 0., 0.7)'))),
			'tp_webgl_twist_wave_right' => array(
				'eng' => 'transitionPack',
				'e' => 'twist',
				'title' => 'Twist Wave to Right', 'speed' => 2500, 'd' => 20, 'addOns' => array(
					'tpack' => array( 'ef' => 'none', 'ie' => 'sine.inOut', 'twe' => 'twistwave', 'twa' => 0, 'twv' => 230, 'twz' => 30, 'twd' => 'right', 'twdi' => 70, 'twc' => false,'rx' => 0,'ry' => 0,'rz' => 0, 'twf' => 'rgba(0., 0., 0., 0.7)', 'tws' => 'rgba(0., 0., 0., 0.7)'))),
			'tp_webgl_twist_wave_curtain' => array(
				'eng' => 'transitionPack',
				'e' => 'twist',
				'title' => 'Twist Curtain', 'speed' => 1700, 'd' => 20, 'addOns' => array(
					'tpack' => array( 'ef' => 'none', 'ie' => 'sine.inOut', 'twe' => 'twistwave', 'twa' => 0, 'twv' => 230, 'twz' => 30, 'twd' => 'right', 'twdi' => 50, 'twc' => true, 'rx' => 0,'ry' => 0,'rz' => 0, 'twf' => 'rgba(0., 0., 0., 0.7)', 'tws' => 'rgba(0., 0., 0., 0.7)'))),

			'tp_webgl_twist_mirrorcube' => array( 'eng' => 'transitionPack', 'e' => 'tpbasic',
				'title' => 'Mirrored Cube', 'speed' => 2500, 'd' => 20, 'addOns' => array(
				'tpack' => array( 'ef' => 'mirrorcube', 'flo' => 30, 'ref' => '0.4', 'gz' => 30 )))	
		);	*/		
		
		// BURN EFFECTS
		$transitions['tpack']['tpburn'] = array(
			'tp_webgl_mixfade_realburn' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Real Burn','speed' => 1500,'d' => 20,'addOns' => array(
					'tpack' => array('ef' => 'burn', 'iny' => '50'))),

			'tp_webgl_mixfade_realburn2' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Real Burn Over','speed' => 1500,'d' => 20,'addOns' => array(
					'tpack' => array('ef' => 'burnover','dplm' => '10'))),	

			'tp_webgl_mixfade_burn' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Map Fade Burn','speed' => 1500,'d' => 20,'addOns' => array(
					'tpack' => array('ef' => 'fade'))),	

			'tp_webgl_mixfade_ice' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Map Fade Ice','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'fade','dplm' => '4'))),

			'tp_webgl_mixfade_boxes' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Map Fade Boxes','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'fade','dplm' => '2'))),		

			'tp_webgl_mixfade_leo' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Map Fade Leopard','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'fade','dplm' => '3')
					)
				)			
		);	

		// CUT EFFECTS
		$transitions['tpack']['tpcuts'] = array(
			'tp_webgl_cut1' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Fire Horizontal','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'cut','dplm' => '8','w' => 5,'ssx' => 66,'ssy' => 66))),
			
			'tp_webgl_cut2' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Solid Wave Horizontal','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'cut','dplm' => '8','w' => 3,'ssx' => 20,'ssy' =>20, 'dir' => 1))),
			'tp_webgl_cut3' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Fire Vertical','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'cut','dplm' => '8','w' => 5,'ssx' => 66,'ssy' => 66, 'dir' => 3))),
			
			'tp_webgl_cut4' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Solid Wave Vertical','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'cut','dplm' => '8','w' => 5,'ssx' => 20,'ssy' => 20, 'dir' => 2))),
			'tp_webgl_straight_cut' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Horizontal Cut','speed' => 1500,'d' => 20,'addOns' => array(
					'tpack' => array('ef' => 'fadeb','dplm' => '5', 'mfl'=>'2'))),	
			'tp_webgl_straight_vcut' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Vertical Cut','speed' => 1500,'d' => 20,'addOns' => array(
					'tpack' => array('ef' => 'fadeb','dplm' => '17', 'mfl'=>'2'))),	
			'tp_webgl_clock' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Clock Cut','speed' => 1500,'d' => 20,'addOns' => array(
					'tpack' => array('ef' => 'fadeb','dplm' => '6',))),
			'tp_webgl_mosaic' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Mosaic','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'mosaic', 'y' => '(2)', 'y' => '(-1)'))),
			'tp_webgl_twist_mirrorcube' => array( 'eng' => 'transitionPack', 'e' => 'tpbasic',
				'title' => 'Mirrored Cube', 'speed' => 2500, 'd' => 20, 'addOns' => array(
				'tpack' => array( 'ef' => 'mirrorcube', 'flo' => 30, 'ref' => '0.4', 'gz' => 30 )))	

		);

		// FLUID EFFECTS
		$transitions['tpack']['tpfluid'] = array(
			'tp_webgl_water' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Circle Wave','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'wave','dplm' => '8','rad' => 90,'w' => 35))),
						

			'tp_webgl_water2' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Circle Wave 2','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'wave','dplm' => '3','rad' => 80,'w' => 20, 'ie' => 'sine.in'))),
			
			'tp_webgl_water2_out' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Circle Wave 3','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'wave','dplm' => '2','rad' => 30,'w' => 5, 'ie' => 'circ.in'))),

			'tp_webgl_water_out' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Circle Wave Out','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'wave','dplm' => '8','rad' => 15,'w' => 40, 'ie' => 'power1.in'))),

			'tp_webgl_water3' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Color Flow','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'colorflow','dplm' => '1','iny' => 33, 'x'=> '(1)'))),

			'tp_webgl_water4' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Color Flow 2','speed' => 2000,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'colorflow','dplm' => '3','iny' => 33, 'x'=> '(1)'))),

			'tp_webgl_water5' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Color Flow Ice','speed' => 2000,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'colorflow','dplm' => '11','iny' => 33, 'x'=> '(1)'))),
			
			'tp_webgl_water6B' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Color Flow Ice 2 Effect','speed' => 2000,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'colorflow','dplm' => '16','iny' => 40, 'x'=> '(1)'))),

			'tp_webgl_water6' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Color Flow Water','speed' => 2000,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'colorflow','dplm' => '13','iny' => 33, 'x'=> '(1)'))),

			'tp_webgl_water6' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Color Flow ZigZag','speed' => 2000,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'colorflow','dplm' => '15','iny' => 40, 'x'=> '(1)'))),
			
			'tp_webgl_water7' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Water Surface Lite','speed' => 2000,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'water','iny' => 10))),

			'tp_webgl_water8' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Water Surface Medium','speed' => 2000,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'water','iny' => 25))),

			'tp_webgl_water9' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Water Surface Strong','speed' => 2000,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'water','iny' => 70))),

			'tp_webgl_waterdrop' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Water Drop','speed' => 2000,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'waterdrop','rad' => 30, 'iny'=> 30))),
			
			'tp_webgl_waterdrop2' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Water Drop Waves','speed' => 2000,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'waterdrop','rad' => 80, 'iny'=> 10, 'ie' => 'none')))
			
		);

		// ROLL EFFECTS
		$transitions['tpack']['tprolls'] = array(
			'tp_webgl_oscale' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Vertical Roll','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'overroll','dplm' => '8', 'dir' => 3, 'iny' => 10))),

			
			'tp_webgl_oroll' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Horizontal Roll','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'overroll','dplm' => '8', 'dir' => 0, 'iny' => 30, 'ie' => 'circ.out'))),

			'tp_webgl_oroll2' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Roll Over Water','speed' => 1750,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'overroll','dplm' => '16', 'ie' => 'power1.in','dir' => 1, 'iny' => 20))),

			'tp_webgl_oroll3' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Roll Over ZigZag','speed' => 2500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'overroll','dplm' => '15', 'dir' => 1, 'iny' => 40, 'ie'=>'none'))),

			'tp_webgl_oroll4' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Roll Over Wire','speed' => 2000,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'overroll','dplm' => '14', 'ie' => 'bounce.out', 'dir' => 2, 'iny' => 10))),

			'tp_webgl_oroll5' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Roll over Leopard','speed' => 1800,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'overroll','dplm' => '3', 'dir' => 3, 'iny' => 30, 'ie'=>'none')))

		);

		// MELT EFFECTS
		$transitions['tpack']['tpstmelt'] = array(			
						
			'tp_webgl_zoom' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Zoom Over','speed' => 1000,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'zoomover', 'iny' => 40, 'ie' => 'none'))),

			'tp_webgl_morph' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Morph Vertical','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'morph', 'iny' => 10, 'x' => 0, 'y' => '(1)', 'ie' => 'expo.out'))),

			'tp_webgl_morphv' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Morph Horiztontal','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'morph', 'iny' => 30, 'x' => '(1)', 'y' => 0, 'ie' => 'circ.out'))),

			'tp_webgl_morphd' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Morph Diagonal','speed' => 1000,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'morph', 'iny' => 70, 'x' => '(2)', 'y' =>'(1)', 'ie' => 'sine.inOut'))),
			
			'tp_webgl_blur' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Blur Over','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'blur', 'iny' => 50, 'x' => 0, 'y' => 0, 'ox' => 50, 'oy' => 50, 'ao' => 'none', 'roz' => 0, 'zre' => 70, 'prange' => 60, 'zo' => 50, 'zi' => -40))),

			'tp_webgl_dreamy' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Dreamy','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'dreamy')))

		);

		// STRETCH AND SKEW EFFECTS
		$transitions['tpack']['tpstsk'] = array(
						
			'tp_wbgl_stretch_1' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Stretch Horizontal','speed' => 1750,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'stretch', 'iny' => 20, 'x' => '(-2)', 'y' => 0, 'stri' => 0 ,'pp' => 'blur' , 'ppbt' => 'motion'))),

			'tp_wbgl_stretch_ela' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Stretch H. Elastic','speed' => 1750,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'stretch', 'iny' => 20, 'x' => '(-2)', 'y' => 0, 'stri' => 20 ,'pp' => 'blur' , 'ppbt' => 'motion', 'ie' => 'back.out'))),


			'tp_wbgl_stretch_2' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Stretch Vertical','speed' => 2500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'stretch', 'iny' => 60, 'x' => 0, 'y' => '(3)', 'stri' => 0, 'pp' => 'blur' , 'ppbt' => 'motion'))),
			
			
			
			'tp_wbgl_stretch_twisth' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Stretch Horizontal Twist','speed' => 3000,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'stretch', 'iny' => 30, 'x' => '(-2)','ie' => 'sine.inOut', 'y' => 0, 'stri' => 20, 'strs'=> 10, 'strf' => 1))),
			'tp_wbgl_stretch_twistv' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Stretch Vertical Twist','speed' => 3000,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'stretch', 'iny' => 30, 'x' => 0, 'y' => '(2)', 'ie' => 'sine.inOut', 'stri' => 20, 'strs' => 10, 'strf' => 1))),

			'tp_wbgl_skew_1' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Skew Horizontal','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'skew', 'iny' => 30, 'x' => '(-2)', 'y' => 0, 'sko' => 0, 'pp' => 'blur' , 'ppbt' => 'motion'))),
			'tp_wbgl_skew_2' => array('eng' => 'transitionPack','e' => 'tpbasic',
						'title' => 'Skew Vertical','speed' => 1500,'d' => 20,'addOns' => array(
							'tpack' => array('ef' => 'skew', 'iny' => 30, 'x' => 0, 'y' => '(2)', 'sko' => 0, 'pp' => 'blur' , 'ppbt' => 'motion'))),
			'tp_wbgl_skew_3' => array('eng' => 'transitionPack','e' => 'tpbasic',
						'title' => 'Skew Diagonal','speed' => 1500,'d' => 20,'addOns' => array(
							'tpack' => array('ef' => 'skew', 'iny' => 30, 'x' => '(-2)', 'y' => '(4)', 'sko' => 0, 'pp' => 'blur' , 'ppbt' => 'motion'))),
			
			'tp_wbgl_skew_4' => array('eng' => 'transitionPack','e' => 'tpbasic',
						'title' => 'Skew Shake','speed' => 2000,'d' => 20,'addOns' => array(
							'tpack' => array('ef' => 'skew', 'iny' => 20, 'x' => '(-2)', 'y' => 0, 'sko' => 0, 'sh' => true, 'shx' => -40, 'shy' => -40, 'shr' => -10, 'shz' => 70, 'shv' => 70, 'ie' => 'none'))),
			

			'tp_wbgl_chaos_1' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Chaos','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'chaos', 'iny' => 30, 'x' => '(-2)', 'y' => '(2)', 'ch1' => 'v2', 'ch2' => 'v1', 'ch3' => 'random', 'ch4' => 'random', 'pp' => 'blur' , 'ppbt' => 'motion')))

			

		);
		// FLAT EFFECTS
		$transitions['tpack']['tpflats'] = array(
			'tp_wbgl_flat_1' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Motion Blurred Vertical','speed' => 2500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'flat',  'x' => 0, 'z' => 100, 'y' => '(-3)', 'pp' => 'blur' , 'ppbt' => 'motion'))),

			'tp_wbgl_flat_glitch_1' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Motion Glitched Vertical','speed' => 2500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'flat',  'x' => 0, 'z' => 100, 'y' =>-1, 'pp' => 'glitch2' ))),

			'tp_wbgl_flat_2' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Motion Blurred Horizontal','speed' => 1200,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'flat',  'x' => '(3)', 'z' => 80, 'tlt' => 2, 'y' =>0, 'pp' => 'blur' , 'ppbt' => 'motion'))),

			'tp_wbgl_flat_3' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Motion Blurred Jump','speed' => 800,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'flat', 'z' => 80, 'ie' => 'power0.inOut', 'x' => '(-1)', 'y' => '(-1)', 'prange'=>'20', 'tlt' => '-2', 'pp' => 'blur' , 'ppbt' => 'motion'))),
			

			'tp_wbgl_flat_4' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Flim Slide','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'flat', 'iny' => 20, 'x' => 0, 'y' =>'(2)', 'pp' => 'film', 'ppfn'=>80 , 'ppfs' => 82,  'ppfh' =>256, 'ppfbw' =>false))),


			'tp_wbgl_pano_1' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Panorama Vertical','speed' => 2500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'pano', 'iny' => 60, 'x' => 0, 'y' => '(16)', 'z' => 250, 'pp' => 'blur' , 'ppbt' => 'motion' ,'tlt' =>10))),
			'tp_wbgl_pano_2' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Panorama Horizontal','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'pano', 'iny' => 50, 'x' => '(6)', 'y' => 0, 'z' => 150, 'pp' => 'blur' , 'ppbt' => 'motion'))),
			'tp_wbgl_pano_3' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Panorama Diagonal','speed' => 2200,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'pano', 'iny' => 30, 'x' => '(-4)', 'y' => '(6)', 'z' => 200, 'tlt'=>5, 'pp' => 'blur' , 'ppbt' => 'motion'))),

			'tp_wbgl_perspective_1' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Disc Rotation I.','speed' => 2200,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'perspective', 'roz' => 1, 'ox' => 50, 'oy' => 50, 'pr' => 30, 'prange' => 30, 'ie' => 'power0.inOut'))),

			'tp_wbgl_perspective_2' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Disc Rotation II.','speed' => 2200,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'perspective', 'roz' => -2, 'ox' => 40, 'oy' => 100, 'pr' => 15, 'prange' => 30, 'ie' => 'circ.out'))),


			'tp_wbgl_spin_1' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Spin Center','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'spin', 'roz' => 4, 'ox' => 50, 'oy' => 50, 'iny' => 50, 'x' => 0, 'y' => 0, 'z' => 0, 'ao' => 'none', 'ie' => 'power3.inOut'))),
			
			'tp_wbgl_spin_2' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Spin Around','speed' => 2500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'spin', 'roz' => -4, 'ox' => 50, 'oy' => 50, 'iny' => 20, 'x' => 1, 'y' => 0, 'z' => 30,'ao' => 'spinaround', 'ie' => 'sine.inOut'))),
			'tp_wbgl_spin_3' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Spin Zoom','speed' => 1800,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'spin', 'roz' => -1, 'ox' => 50, 'oy' => 50, 'iny' => 15, 'x' => 1, 'y' => 0, 'z' => 30,'ao' => 'none', 'ie' => 'sine.inOut', 'prange' => 40))),

			'tp_wbgl_rings_1' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Rings Center','speed' => 1500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'rings', 'roz' => 4, 'ox' => 50, 'oy' => 50, 'iny' => 50, 'x' => 0, 'y' => 0, 'z' => 0, 'ao' => 'none', 'ie' => 'power3.inOut'))),
			
			'tp_wbgl_rings_2' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Rings Spin Around','speed' => 2500,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'rings', 'roz' => -4, 'ox' => 50, 'oy' => 50, 'iny' => 20, 'x' => 1, 'y' => 0, 'z' => 30,'ao' => 'spinaround', 'ie' => 'sine.inOut'))),

			'tp_wbgl_zoom_1' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Zoom Basic','speed' => 1000,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'zoom', 'roz' => 0, 'ox' => 50, 'oy' => 50, 'ao' => 'none', 'prange' => 20, 'zo' => 50, 'zi' => -100, 
							'zb' => 50, 'zwo' => 0, 'zwi' => 0, 'zre' => 70, 'ie' => 'sine.inOut'))),
			'tp_wbgl_zoom_2' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Warp Zoom Out','speed' => 1400,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'zoom', 'roz' => 0, 'ox' => 50, 'oy' => 50, 'ao' => 'none', 'prange' => 20, 'zo' => -50, 'zi' => 50, 
							'zb' => 10, 'zwo' => 50, 'zwi' => 50, 'zre' => 70, 'ie' => 'power2.inOut'))),
			'tp_wbgl_zoom_3' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Warp Zoom In','speed' => 1400,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'zoom', 'roz' => 0, 'ox' => 50, 'oy' => 50, 'ao' => 'none', 'prange' => 20, 'zo' => 50, 'zi' => 10, 
							'zb' => 30, 'zwo' => 30, 'zwi' => 60, 'zre' => 70, 'ie' => 'power2.inOut'))),
			'tp_wbgl_zoom_4' => array('eng' => 'transitionPack','e' => 'tpbasic',
					'title' => 'Warp Origin','speed' => 1400,'d' => 20,'addOns' => array(
						'tpack' => array('ef' => 'zoom', 'roz' => 0, 'ox' => 10, 'oy' => 50, 'ao' => 'inverse', 'prange' => 100, 'zo' => 10, 'zi' => 10, 
							'zb' => 40, 'zwo' => 30, 'zwi' => 30, 'zre' => 70, 'ie' => 'sine.inOut'))),
		);

		
				
			
		return json_encode($transitions); //set them back to raw
	}
	
	public static function get_shaders(){
		$shaders = array(
/*TH*/		'fade'		=> "`uniform float progress; uniform bool flipx; uniform bool flipy; uniform sampler2D src1; uniform sampler2D src2; uniform sampler2D displacement; uniform int useTexture; uniform float threshold; varying vec2 vUv; void main() { vec4 texel1 = TEXTURE2D(src1, vUv); vec4 texel2 = TEXTURE2D(src2, vUv); vec4 transitionTexel = TEXTURE2D(displacement, vec2(flipx ? 1. - vUv.x : vUv.x, flipy ? 1. - vUv.y : vUv.y)); float r = progress * (1.0 + threshold * 2.0) - threshold; float mixf = clamp((transitionTexel.r - r) * (1.0 / threshold), .0, 1.0); gl_FragColor = mix(texel1, texel2, mixf); }`",
/*CR*/		'wave'		=> "`uniform float time;uniform float progress;uniform float width;uniform float scaleX;uniform float scaleY;uniform float transition;uniform float radius;uniform float swipe;uniform sampler2D src1;uniform sampler2D src2;uniform sampler2D displacement;uniform vec4 resolution;varying vec2 vUv;varying vec4 vPosition;float parabola( float x, float k ) {return pow( 4. * x * ( 1. - x ), k );}void main()	{vec2 newUV = (vUv - vec2(0.5))*resolution.zw + vec2(0.5);vec2 p = newUV;vec2 start = vec2(0.5,0.5);vec2 aspect = resolution.wz;vec2 uv = newUV;float dt = parabola(progress, 1.);vec4 noise = TEXTURE2D(displacement, fract(vUv+progress*0.04));float prog = progress*0.66 + noise.g * 0.04;float circ = 1. - smoothstep(-width, 0.0, radius * distance(start*aspect, uv*aspect) - prog*(1.+width));float intpl = pow(abs(circ), 1.);vec4 t1 = TEXTURE2D( src1, (uv - 0.5) * (1.0 - intpl) + 0.5 ) ;vec4 t2 = TEXTURE2D( src2, (uv - 0.5) * intpl + 0.5 );gl_FragColor = mix( t1, t2, intpl );}`",
/*CR*/		'cut'	=> "`uniform int dir;uniform float time;uniform float progress;uniform float width;uniform float scaleX;uniform float scaleY;uniform sampler2D src1;uniform sampler2D src2;uniform sampler2D displacement;uniform vec4 resolution;varying vec2 vUv;varying vec4 vPosition;vec4 permute(vec4 x){return mod(((x*34.0)+1.0)*x, 289.0);}vec4 taylorInvSqrt(vec4 r){return 1.79284291400159 - 0.85373472095314 * r;}vec4 fade(vec4 t) {return t*t*t*(t*(t*6.0-15.0)+10.0);}float cnoise(vec4 P){  ;  vec4 Pi0 = floor(P);   vec4 Pi1 = Pi0 + 1.0;   Pi0 = mod(Pi0, 289.0);  Pi1 = mod(Pi1, 289.0);  vec4 Pf0 = fract(P);   vec4 Pf1 = Pf0 - 1.0;   vec4 ix = vec4(Pi0.x, Pi1.x, Pi0.x, Pi1.x);  vec4 iy = vec4(Pi0.yy, Pi1.yy);  vec4 iz0 = vec4(Pi0.zzzz);  vec4 iz1 = vec4(Pi1.zzzz);  vec4 iw0 = vec4(Pi0.wwww);  vec4 iw1 = vec4(Pi1.wwww);vec4 ixy = permute(permute(ix) + iy);  vec4 ixy0 = permute(ixy + iz0);  vec4 ixy1 = permute(ixy + iz1);  vec4 ixy00 = permute(ixy0 + iw0);  vec4 ixy01 = permute(ixy0 + iw1);  vec4 ixy10 = permute(ixy1 + iw0);  vec4 ixy11 = permute(ixy1 + iw1);vec4 gx00 = ixy00 / 7.0;  vec4 gy00 = floor(gx00) / 7.0;  vec4 gz00 = floor(gy00) / 6.0;  gx00 = fract(gx00) - 0.5;  gy00 = fract(gy00) - 0.5;  gz00 = fract(gz00) - 0.5;  vec4 gw00 = vec4(0.75) - abs(gx00) - abs(gy00) - abs(gz00);  vec4 sw00 = step(gw00, vec4(0.0));  gx00 -= sw00 * (step(0.0, gx00) - 0.5);gy00 -= sw00 * (step(0.0, gy00) - 0.5);vec4 gx01 = ixy01 / 7.0;vec4 gy01 = floor(gx01) / 7.0;vec4 gz01 = floor(gy01) / 6.0;gx01 = fract(gx01) - 0.5;gy01 = fract(gy01) - 0.5;gz01 = fract(gz01) - 0.5;vec4 gw01 = vec4(0.75) - abs(gx01) - abs(gy01) - abs(gz01);vec4 sw01 = step(gw01, vec4(0.0));gx01 -= sw01 * (step(0.0, gx01) - 0.5);gy01 -= sw01 * (step(0.0, gy01) - 0.5);vec4 gx10 = ixy10 / 7.0;vec4 gy10 = floor(gx10) / 7.0;vec4 gz10 = floor(gy10) / 6.0;gx10 = fract(gx10) - 0.5;gy10 = fract(gy10) - 0.5;gz10 = fract(gz10) - 0.5;vec4 gw10 = vec4(0.75) - abs(gx10) - abs(gy10) - abs(gz10);vec4 sw10 = step(gw10, vec4(0.0));gx10 -= sw10 * (step(0.0, gx10) - 0.5);gy10 -= sw10 * (step(0.0, gy10) - 0.5);vec4 gx11 = ixy11 / 7.0;vec4 gy11 = floor(gx11) / 7.0;vec4 gz11 = floor(gy11) / 6.0;gx11 = fract(gx11) - 0.5;gy11 = fract(gy11) - 0.5;gz11 = fract(gz11) - 0.5;vec4 gw11 = vec4(0.75) - abs(gx11) - abs(gy11) - abs(gz11);vec4 sw11 = step(gw11, vec4(0.0));gx11 -= sw11 * (step(0.0, gx11) - 0.5);gy11 -= sw11 * (step(0.0, gy11) - 0.5);vec4 g0000 = vec4(gx00.x,gy00.x,gz00.x,gw00.x);vec4 g1000 = vec4(gx00.y,gy00.y,gz00.y,gw00.y);vec4 g0100 = vec4(gx00.z,gy00.z,gz00.z,gw00.z);vec4 g1100 = vec4(gx00.w,gy00.w,gz00.w,gw00.w);vec4 g0010 = vec4(gx10.x,gy10.x,gz10.x,gw10.x);vec4 g1010 = vec4(gx10.y,gy10.y,gz10.y,gw10.y);vec4 g0110 = vec4(gx10.z,gy10.z,gz10.z,gw10.z);vec4 g1110 = vec4(gx10.w,gy10.w,gz10.w,gw10.w);vec4 g0001 = vec4(gx01.x,gy01.x,gz01.x,gw01.x);vec4 g1001 = vec4(gx01.y,gy01.y,gz01.y,gw01.y);vec4 g0101 = vec4(gx01.z,gy01.z,gz01.z,gw01.z);vec4 g1101 = vec4(gx01.w,gy01.w,gz01.w,gw01.w);vec4 g0011 = vec4(gx11.x,gy11.x,gz11.x,gw11.x);vec4 g1011 = vec4(gx11.y,gy11.y,gz11.y,gw11.y);vec4 g0111 = vec4(gx11.z,gy11.z,gz11.z,gw11.z);vec4 g1111 = vec4(gx11.w,gy11.w,gz11.w,gw11.w);vec4 norm00 = taylorInvSqrt(vec4(dot(g0000, g0000), dot(g0100, g0100), dot(g1000, g1000), dot(g1100, g1100)));g0000 *= norm00.x;g0100 *= norm00.y;g1000 *= norm00.z;g1100 *= norm00.w;vec4 norm01 = taylorInvSqrt(vec4(dot(g0001, g0001), dot(g0101, g0101), dot(g1001, g1001), dot(g1101, g1101)));g0001 *= norm01.x;g0101 *= norm01.y;g1001 *= norm01.z;g1101 *= norm01.w;vec4 norm10 = taylorInvSqrt(vec4(dot(g0010, g0010), dot(g0110, g0110), dot(g1010, g1010), dot(g1110, g1110)));g0010 *= norm10.x;g0110 *= norm10.y;g1010 *= norm10.z;g1110 *= norm10.w;vec4 norm11 = taylorInvSqrt(vec4(dot(g0011, g0011), dot(g0111, g0111), dot(g1011, g1011), dot(g1111, g1111)));g0011 *= norm11.x;g0111 *= norm11.y;g1011 *= norm11.z;g1111 *= norm11.w;float n0000 = dot(g0000, Pf0);float n1000 = dot(g1000, vec4(Pf1.x, Pf0.yzw));float n0100 = dot(g0100, vec4(Pf0.x, Pf1.y, Pf0.zw));float n1100 = dot(g1100, vec4(Pf1.xy, Pf0.zw));float n0010 = dot(g0010, vec4(Pf0.xy, Pf1.z, Pf0.w));float n1010 = dot(g1010, vec4(Pf1.x, Pf0.y, Pf1.z, Pf0.w));float n0110 = dot(g0110, vec4(Pf0.x, Pf1.yz, Pf0.w));float n1110 = dot(g1110, vec4(Pf1.xyz, Pf0.w));float n0001 = dot(g0001, vec4(Pf0.xyz, Pf1.w));float n1001 = dot(g1001, vec4(Pf1.x, Pf0.yz, Pf1.w));float n0101 = dot(g0101, vec4(Pf0.x, Pf1.y, Pf0.z, Pf1.w));float n1101 = dot(g1101, vec4(Pf1.xy, Pf0.z, Pf1.w));float n0011 = dot(g0011, vec4(Pf0.xy, Pf1.zw));float n1011 = dot(g1011, vec4(Pf1.x, Pf0.y, Pf1.zw));float n0111 = dot(g0111, vec4(Pf0.x, Pf1.yzw));float n1111 = dot(g1111, Pf1);vec4 fade_xyzw = fade(Pf0);vec4 n_0w = mix(vec4(n0000, n1000, n0100, n1100), vec4(n0001, n1001, n0101, n1101), fade_xyzw.w);vec4 n_1w = mix(vec4(n0010, n1010, n0110, n1110), vec4(n0011, n1011, n0111, n1111), fade_xyzw.w);vec4 n_zw = mix(n_0w, n_1w, fade_xyzw.z);vec2 n_yzw = mix(n_zw.xy, n_zw.zw, fade_xyzw.y);float n_xyzw = mix(n_yzw.x, n_yzw.y, fade_xyzw.x);return 2.2 * n_xyzw;}float map(float value, float min1, float max1, float min2, float max2) {return min2 + (value - min1) * (max2 - min2) / (max1 - min1);}float parabola( float x, float k ) {return pow( 4. * x * ( 1. - x ), k );}void main()	{float dt = parabola(progress,1.);float border = 1.;vec2 newUV = (vUv - vec2(0.5))*resolution.zw + vec2(0.5);vec4 color1 = TEXTURE2D(src1,newUV);vec4 color2 = TEXTURE2D(src2,newUV);vec4 d = TEXTURE2D(displacement,vec2(newUV.x*scaleX,newUV.y*scaleY));float realnoise = 0.5*(cnoise(vec4(newUV.x*scaleX  + 0.*progress/3., newUV.y*scaleY,0.*progress/3.,0.)) +1.);float w = width*dt;float maskvalue = smoothstep(w,0.,1. - (vUv.x + mix(-w/2., 1. - w/2., (1.-progress))));float maskvalue0 = smoothstep(1.,1.,vUv.x + progress);if (dir==1) {maskvalue = smoothstep(1.-w,1.,vUv.x + mix(-w/2., 1. - w/2., progress));maskvalue0 = smoothstep(1.,1.,vUv.x + progress);}if (dir==2) {maskvalue = smoothstep(1. ,1.-w,vUv.y + mix(-w/2., 1. - w/2., progress));maskvalue0 = smoothstep(1.,1.,vUv.y + progress);}if (dir==3) {maskvalue = smoothstep(0. ,w,1. - (vUv.y + mix(-w/2., 1. - w/2., (1.-progress))));maskvalue0 = smoothstep(1.,1.,vUv.y + progress);}float mask = maskvalue + maskvalue*realnoise;float final = smoothstep(border,border+0.01,mask);gl_FragColor = dir==0 || dir==2 ? mix(color2,color1,final) : mix(color1,color2,final);}`",
/*CR*/		'overroll'	=> "`uniform int dir; uniform float time; uniform float progress; uniform float width; uniform float scaleX; uniform float scaleY; uniform float transition; uniform float radius; uniform float swipe; uniform float intensity; uniform sampler2D src1; uniform sampler2D src2; uniform sampler2D displacement; uniform vec4 resolution; varying vec2 vUv; varying vec4 vPosition; vec2 mirrored(vec2 v) { vec2 m = mod(v, 2.); return mix(m, 2.0 - m, step(1.0, m)); } void main() { vec2 newUV = (vUv - vec2(0.5)) * resolution.zw + vec2(0.5); vec4 noise = TEXTURE2D(displacement, mirrored(newUV + time * 0.04)); float prog = progress * (0.8 + intensity) - (intensity) + noise.g * (intensity); float intpl = dir == 0 ? pow(abs(smoothstep(0., 1., (prog * 2. - vUv.x + 0.5))), 10.) : dir == 1 ? pow(abs(smoothstep(0., 1., (prog * 2. + vUv.x - 0.5))), 10.) : dir == 2 ? pow(abs(smoothstep(0., 1., (prog * 2. + vUv.y - 0.5))), 10.) : pow(abs(smoothstep(0., 1., (prog * 2. - vUv.y + 0.5))), 10.); vec4 t1 = TEXTURE2D(src1, (newUV - 0.5) * (1.0 - intpl) + 0.5); vec4 t2 = TEXTURE2D(src2, (newUV - 0.5) * intpl + 0.5); gl_FragColor = mix(t1, t2, intpl); }`",
/*CR*/		'colorflow' => "`uniform float progress; uniform float intensity; uniform sampler2D src1; uniform sampler2D src2; uniform sampler2D displacement; uniform vec4 resolution; varying vec2 vUv; uniform float left; uniform float top; uniform float angle; vec2 mirror(vec2 v) { vec2 m = mod(v, 2.0); return mix(m, 2.0 - m, step(1.0, m)); } mat2 getRotM(float angle) { float s = sin(angle); float c = cos(angle); return mat2(c, -s, s, c); } const float PI = 3.1415; void main() { vec2 newUV = (vUv - vec2(0.5)) * resolution.zw + vec2(0.5); vec4 disp = TEXTURE2D(displacement, newUV); vec2 dispVec = vec2(disp.r, disp.g); vec2 distort1 = getRotM(angle) * dispVec * intensity * progress; vec2 distort2 = getRotM(angle - PI) * dispVec * intensity * (1.0 - progress); if(left != 0.) { distort1.x *= left; distort2.x *= left; } if(top != 0.) { distort1.y *= top; distort2.y *= top; } vec2 distortedPosition1 = newUV + distort1; vec2 distortedPosition2 = newUV + distort2; vec4 t1 = TEXTURE2D(src1, mirror(distortedPosition1)); vec4 t2 = TEXTURE2D(src2, mirror(distortedPosition2)); gl_FragColor = mix(t1, t2, progress); }`",
/*CR*/		'rotation' => "`uniform float progress;uniform float intensityx;uniform float intensityy;uniform sampler2D src1;uniform sampler2D src2;uniform vec4 resolution;varying vec2 vUv;mat2 rotate(float a) {float s = sin(a);float c = cos(a);return mat2(c, -s, s, c);}const float PI = 3.1415;const float angle1 = PI *0.25;const float angle2 = -PI *0.75;void main()	{vec2 newUV = (vUv - vec2(0.5))*resolution.zw + vec2(0.5);vec2 uvDivided = fract(newUV*vec2(intensityx,intensityy));vec2 uvDisplaced1 = newUV + rotate(3.1415926/4.)*uvDivided*progress*0.1;vec2 uvDisplaced2 = newUV + rotate(3.1415926/4.)*uvDivided*(1. - progress)*0.1;vec4 t1 = TEXTURE2D(src1,uvDisplaced1);vec4 t2 = TEXTURE2D(src2,uvDisplaced2);gl_FragColor = mix(t1, t2, progress);}`",
/*CR*/		'stretch' => "`uniform float progress;uniform sampler2D src1;uniform sampler2D src2;uniform vec4 resolution;varying vec2 vUv;mat2 rotate(float a) {float s = sin(a);float c = cos(a);return mat2(c, -s, s, c);}const float PI = 3.1415;const float angle1 = PI *0.25;const float angle2 = -PI *0.75;const float noiseSeed = 2.;float random() { return fract(sin(noiseSeed + dot(gl_FragCoord.xy / resolution.xy / 10.0, vec2(12.9898, 4.1414))) * 43758.5453);}float hash(float n) { return fract(sin(n) * 1e4); }float hash(vec2 p) { return fract(1e4 * sin(17.0 * p.x + p.y * 0.1) * (0.1 + abs(sin(p.y * 13.0 + p.x)))); }float hnoise(vec2 x) {vec2 i = floor(x);vec2 f = fract(x);float a = hash(i);float b = hash(i + vec2(1.0, 0.0));float c = hash(i + vec2(0.0, 1.0));float d = hash(i + vec2(1.0, 1.0));vec2 u = f * f * (3.0 - 2.0 * f);return mix(a, b, u.x) + (c - a) * u.y * (1.0 - u.x) + (d - b) * u.x * u.y;}void main()	{vec2 newUV = (vUv - vec2(0.5))*resolution.zw + vec2(0.5);float hn = hnoise(newUV.xy * resolution.xy / 100.0);vec2 d = vec2(0.,normalize(vec2(0.5,0.5) - newUV.xy).y);vec2 uv1 = newUV + d * progress / 5.0 * (1.0 + hn / 2.0);vec2 uv2 = newUV - d * (1.0 - progress) / 5.0 * (1.0 + hn / 2.0);vec4 t1 = TEXTURE2D(src1,uv1);vec4 t2 = TEXTURE2D(src2,uv2);gl_FragColor = mix(t1, t2, progress);}`",
/*ST*/		'water' => "`uniform float intensity;uniform float progress;uniform sampler2D src1;uniform sampler2D src2;uniform vec4 resolution;varying vec2 vUv;vec3 mod289(vec3 x) {return x - floor(x * (1.0 / 289.0)) * 289.0;}vec4 mod289(vec4 x) {return x - floor(x * (1.0 / 289.0)) * 289.0;}vec4 permute(vec4 x) {return mod289(((x*34.0)+1.0)*x);}vec4 taylorInvSqrt(vec4 r){return 1.79284291400159 - 0.85373472095314 * r;}float snoise(vec3 v) {const vec2  C = vec2(1.0/6.0, 1.0/3.0) ;const vec4  D = vec4(0.0, 0.5, 1.0, 2.0);vec3 i  = floor(v + dot(v, C.yyy) );vec3 x0 =   v - i + dot(i, C.xxx) ;vec3 g = step(x0.yzx, x0.xyz);vec3 l = 1.0 - g;vec3 i1 = min( g.xyz, l.zxy );vec3 i2 = max( g.xyz, l.zxy );vec3 x1 = x0 - i1 + C.xxx;vec3 x2 = x0 - i2 + C.yyy; vec3 x3 = x0 - D.yyy;i = mod289(i);vec4 p = permute( permute( permute(i.z + vec4(0.0, i1.z, i2.z, 1.0 ))+ i.y + vec4(0.0, i1.y, i2.y, 1.0 ))+ i.x + vec4(0.0, i1.x, i2.x, 1.0 ));float n_ = 0.142857142857;vec3  ns = n_ * D.wyz - D.xzx;vec4 j = p - 49.0 * floor(p * ns.z * ns.z);vec4 x_ = floor(j * ns.z);vec4 y_ = floor(j - 7.0 * x_ );    vec4 x = x_ *ns.x + ns.yyyy;vec4 y = y_ *ns.x + ns.yyyy;vec4 h = 1.0 - abs(x) - abs(y);vec4 b0 = vec4( x.xy, y.xy );vec4 b1 = vec4( x.zw, y.zw );vec4 s0 = floor(b0)*2.0 + 1.0;vec4 s1 = floor(b1)*2.0 + 1.0;vec4 sh = -step(h, vec4(0.0));vec4 a0 = b0.xzyw + s0.xzyw*sh.xxyy ;vec4 a1 = b1.xzyw + s1.xzyw*sh.zzww ;vec3 p0 = vec3(a0.xy,h.x);vec3 p1 = vec3(a0.zw,h.y);vec3 p2 = vec3(a1.xy,h.z);vec3 p3 = vec3(a1.zw,h.w);vec4 norm = taylorInvSqrt(vec4(dot(p0,p0), dot(p1,p1), dot(p2, p2), dot(p3,p3)));p0 *= norm.x;p1 *= norm.y;p2 *= norm.z;p3 *= norm.w;  vec4 m = max(0.6 - vec4(dot(x0,x0), dot(x1,x1), dot(x2,x2), dot(x3,x3)), 0.0);m = m * m;return 42.0 * dot( m*m, vec4( dot(p0,x0), dot(p1,x1), dot(p2,x2), dot(p3,x3) ) );}vec3 curlNoise( vec3 p ){const float e = 0.1;float  n1 = snoise(vec3(p.x, p.y + e, p.z));float  n2 = snoise(vec3(p.x, p.y - e, p.z));float  n3 = snoise(vec3(p.x, p.y, p.z + e));float  n4 = snoise(vec3(p.x, p.y, p.z - e));float  n5 = snoise(vec3(p.x + e, p.y, p.z));float  n6 = snoise(vec3(p.x - e, p.y, p.z));float x = n2 - n1 - n4 + n3;float y = n4 - n3 - n6 + n5;float z = n6 - n5 - n2 + n1;const float divisor = 1.0 / ( 2.0 * e );return normalize( vec3( x , y , z ) * divisor );}void main(){vec2 nUV = (vUv - vec2(0.5))*resolution.zw + vec2(0.5);float f = progress;vec3 curl = curlNoise(vec3(nUV,1.) *intensity + progress*.5) / 1.;vec4 t0 = TEXTURE2D(src1, vec2(nUV.x,nUV.y + f * (curl.x) ) );vec4 t1 = TEXTURE2D(src2, vec2(nUV.x,nUV.y + (1.-f) * (curl.x) ));nUV.x += curl.x;gl_FragColor = mix(t0,t1,f);}`",
/*GL*/		'zoomover' => "`uniform float intensity; uniform float progress; uniform sampler2D src1; uniform sampler2D src2; varying vec2 vUv; uniform vec4 resolution; const float PI = 3.141592653589793; float Linear_ease( in float begin, in float change, in float duration, in float time) { return change * time / duration + begin; } float Exponential_easeInOut( in float begin, in float change, in float duration, in float time) { if (time == 0.0) return begin; else if (time == duration) return begin + change; time = time / (duration / 2.0); if (time < 1.0) return change / 2.0 * pow(2.0, 10.0 * (time - 1.0)) + begin; return change / 2.0 * (-pow(2.0, -10.0 * (time - 1.0)) + 2.0) + begin; } float Sinusoidal_easeInOut( in float begin, in float change, in float duration, in float time) { return -change / 2.0 * (cos(PI * time / duration) - 1.0) + begin; } float random( in vec3 scale, in float seed) { return fract(sin(dot(gl_FragCoord.xyz + seed, scale)) * 43758.5453 + seed); } vec4 crossFade( in vec2 uv, in float dissolve) { return mix(TEXTURE2D(src1, uv), TEXTURE2D(src2, uv), dissolve); } void main() { vec2 texCoord = vUv / resolution.zw; vec2 center = vec2(Linear_ease(0.5, 0.0, 1.0, progress), 0.5); float dissolve = Exponential_easeInOut(0.0, 1.0, 1.0, progress); float intensity = Sinusoidal_easeInOut(0.0, intensity, 0.5, progress); vec4 color = vec4(0.0); float total = 0.0; vec2 toCenter = center - texCoord; float offset = random(vec3(12.9898, 78.233, 151.7182), 0.0) * 0.5; for (float t = 0.0; t <= 20.0; t++) { float percent = (t + offset) / 20.0; float weight = 1.0 * (percent - percent * percent); color += crossFade(texCoord + toCenter * percent * intensity, dissolve) * weight; total += weight; } gl_FragColor = color / total; }`",
/*ST*/		'burn' => "`uniform int dir; uniform float intensity; uniform float progress; uniform sampler2D src1; uniform sampler2D src2; varying vec2 vUv; uniform vec4 resolution; float Hash(vec2 p) { vec3 p2 = vec3(p.xy, 1.0); return fract(sin(dot(p2, vec3(37.1, 61.7, 12.4))) * 10.); } float noise( in vec2 p) { vec2 i = floor(p); vec2 f = fract(p); f *= f * (3.0 - 2.0 * f); return mix(mix(Hash(i + vec2(0., 0.)), Hash(i + vec2(1., 0.)), f.x), mix(Hash(i + vec2(0., 1.)), Hash(i + vec2(1., 1.)), f.x), f.y); } float fbm(vec2 p) { float v = 0.0; v += noise(p * 1.) * .4; v += noise(p * 2.) * .2; v += noise(p * 4.) * .135; return v; } void main() { float nIntensity = intensity / 3.; vec2 uv = (vUv - vec2(0.5)) * resolution.zw + vec2(0.5); vec4 src = TEXTURE2D(src1, uv); vec4 tgt = TEXTURE2D(src2, uv); vec4 col = src; float pr = progress; float d; float efEnd = dir == 1 || dir == 2 ? 0.5 : 0.8 * nIntensity; float efStart = dir == 1 || dir == 2 ? 0.5 : 1. * nIntensity - .3; if (dir == 0) { uv.x += efStart; d = -uv.x + 0.5 * fbm(uv * 30.1) * intensity + pr * 1.3 + efEnd * pr; } if (dir == 1) { uv.x -= efStart; d = uv.x - 0.5 * fbm(uv * 30.1) * intensity + pr * 1.3 + efEnd * pr; } if (dir == 2) { uv.y -= efStart; d = uv.y - 0.5 * fbm(uv * 30.1) * intensity + pr * 1.3 + efEnd * pr; } if (dir == 3) { uv.y += efStart; d = -uv.y + 0.5 * fbm(uv * 30.1) * intensity + pr * 1.3 + efEnd * pr; } if (d > 0.35 + (0.1 * (1. - nIntensity))) col.rgb = clamp(col.rgb - (d - 0.35 - (0.1 * (1. - nIntensity))) * 10., 0.0, 1.0); if (d > 0.47) { if (d < 0.5) col.rgb += (d - 0.4) * 35.0 * 0.4 * (0.1 + noise(100. * uv + vec2(-pr, 0.))) * vec3(1.5, 0.5, 0.0); else col += tgt; } gl_FragColor = col; }`",
/*GL*/		'morph' => "`uniform vec4 resolution; uniform float intensity; uniform float progress; uniform sampler2D src1; uniform sampler2D src2; varying vec2 vUv; uniform float left; uniform float top; vec2 mirror(vec2 v) { vec2 m = mod(v, 2.0); return mix(m, 2.0 - m, step(1.0, m)); } vec4 getFromColor(vec2 uv) { return TEXTURE2D(src1, mirror(uv)); } vec4 getToColor(vec2 uv) { return TEXTURE2D(src2, mirror(uv)); } void main() { vec2 p = (vUv - vec2(0.5)) * resolution.zw + vec2(0.5); vec4 ca = getFromColor(p); vec4 cb = getToColor(p); vec2 oa = (((ca.rg + ca.b) * 0.5) * 2.0 - 1.0); vec2 ob = (((cb.rg + cb.b) * 0.5) * 2.0 - 1.0); vec2 oc = mix(oa, ob, 0.5) * intensity; float w0 = progress; float w1 = 1.0 - w0; vec2 uvOut = p; vec2 uvIn = p; if(left != 0.) { uvOut.x = p.x + left * oc.x * w0; uvIn.x = p.x + left * oc.x * w1; } if(top != 0.){ uvOut.y = p.y + top * oc.y * w0; uvIn.y = p.y + top * oc.y * w1; } gl_FragColor = mix(getFromColor(uvOut), getToColor(uvIn), progress); }`",
/*GL*/		'blur' => "` uniform float left; uniform float top; uniform float ox; uniform float oy; uniform float zIn; uniform float zOut; uniform float roz; uniform float rEnd; uniform bool isShort; uniform float prange; uniform vec4 resolution; uniform float intensity; uniform float progress; uniform sampler2D src1; uniform sampler2D src2; varying vec2 vUv; const int passes = 6; float pi = 3.141592653; vec4 getFromColor(vec2 uv) { return TEXTURE2D(src1, uv); } vec4 getToColor(vec2 uv) { return TEXTURE2D(src2, uv); } float map(float a, float b, float c, float d, float v, float cmin, float cmax) { return clamp((v - a) * (d - c) / (b - a) + c, cmin, cmax); } vec2 mirror(vec2 v) { vec2 m = mod(v, 2.0); return mix(m, 2.0 - m, step(1.0, m)); } vec2 rotate(vec2 uv, vec2 mid, float rotation) { return vec2( cos(rotation) * (uv.x - mid.x) + sin(rotation) * (uv.y - mid.y) + mid.x, cos(rotation) * (uv.y - mid.y) - sin(rotation) * (uv.x - mid.x) + mid.y ); } vec2 zoomFunction(vec2 uv, vec2 o, float z, float m) { uv -= o; uv *= 1. + z * m; uv += o; return uv; } void main() { vec2 o = vec2(ox, oy); vec2 uv = (vUv - vec2(0.5)) * resolution.zw + vec2(0.5); float ratio = resolution.x / resolution.y; vec4 c1 = vec4(0.0); vec4 c2 = vec4(0.0); float disp = intensity * (0.5 - distance(0.5, progress)); float m = progress; float nprog = map((0.5 - prange), (0.5 + prange), 0., 1., m, 0., 1.); float rm = map(0., rEnd, 0., 1., m, 0., 1.); o.x *= ratio; uv.x *= ratio; uv = rotate(uv, o, roz * pi * progress); uv.x /= ratio; o.x /= ratio; vec2 uvIn = uv; vec2 uvOut = uv; if (isShort) { uvIn.x -= o.x * 2.; uvIn.y -= o.y * 2.; } float zm = sin(pi * progress); if (zOut != 0.) uvOut = zoomFunction(uvOut, o, max(zOut, -0.9), progress); if (zIn != 0.) uvIn = zoomFunction(uvIn, o, max(zIn, -0.9), 1. - progress); for (int xi = 0; xi < passes; xi++) { float x = float(xi) / float(passes) - 0.5; for (int yi = 0; yi < passes; yi++) { float y = float(yi) / float(passes) - 0.5; vec2 v = vec2(x, y); float d = disp; vec2 nUvOut = vec2(uvOut.x +  progress * left, uvOut.y + progress * top) + d * v; nUvOut = mirror(nUvOut); vec2 nUvIn = vec2(uvIn.x +  progress * left, uvIn.y + progress * top) + d * v; nUvIn = mirror(nUvIn); if(mod(left, 2.0) != 0.) nUvIn.x *= -1.; if(mod(top, 2.0) != 0.) nUvIn.y *= -1.; c1 += getFromColor(nUvOut); c2 += getToColor(nUvIn); } } c1 /= float(passes * passes); c2 /= float(passes * passes); gl_FragColor = mix(c1, c2, nprog); }`",
/*GL*/		'waterdrop' => "`uniform float amplitude; uniform float speed; uniform vec4 resolution; uniform float intensity; uniform float progress; uniform sampler2D src1; uniform sampler2D src2; varying vec2 vUv; vec4 getFromColor(vec2 uv) { return TEXTURE2D(src1, uv); } vec4 getToColor(vec2 uv) { return TEXTURE2D(src2, uv); } void main() { vec2 p = (vUv - vec2(0.5)) * resolution.zw + vec2(0.5); vec2 dir = p - vec2(.5); float dist = length(dir); float ratio = resolution.x / resolution.y; float maxdist = resolution.x > resolution.y ? ratio : 1./ratio; ratio = maxdist; maxdist *= progress; maxdist = smoothstep(dist, dist * (1.5 * ratio + 1.5 * (1. - progress)), maxdist); if (dist > progress) { gl_FragColor = mix(getFromColor(p), getToColor(p), progress); } else { vec2 offset = dir * sin(dist * amplitude - progress * speed); gl_FragColor = mix(getFromColor(p + offset), getToColor(p), progress); } gl_FragColor = mix(getFromColor(p), gl_FragColor, maxdist); }`",
/*GL*/		'mosaic' => "`uniform int endx;uniform int endy;uniform vec4 resolution;uniform float intensity;uniform float progress;uniform sampler2D src1;uniform sampler2D src2;varying vec2 vUv;vec4 getFromColor(vec2 uv){return TEXTURE2D(src1, uv);}vec4 getToColor(vec2 uv){return TEXTURE2D(src2, uv);}float Rand(vec2 v) {return fract(sin(dot(v.xy ,vec2(12.9898,78.233))) * 43758.5453);}vec2 Rotate(vec2 v, float a) {mat2 rm = mat2(cos(a), -sin(a),sin(a), cos(a));return rm*v;}float CosInterpolation(float x) {return -cos(x*3.14159265358979323)/2.+.5;}float POW2(float X) { return X*X;}float POW3(float X) { return X*X*X;}void main() {vec2 newUV = (vUv - vec2(0.5))*resolution.zw + vec2(0.5);vec2 p = newUV.xy / vec2(1.0).xy - .5;vec2 rp = p;float rpr = (progress*2.-1.);float z = -(rpr*rpr*2.) + 3.;float az = abs(z);rp *= az;rp += mix(vec2(.5, .5), vec2(float(endx) + .5, float(endy) + .5), POW2(CosInterpolation(progress)));vec2 mrp = mod(rp, 1.);vec2 crp = rp;bool onEnd = int(floor(crp.x))==endx&&int(floor(crp.y))==endy;if(!onEnd) {float ang = float(int(Rand(floor(crp))*4.))*.5*3.14159265358979323;mrp = vec2(.5) + Rotate(mrp-vec2(.5), ang);} if(onEnd || Rand(floor(crp))>.5) {gl_FragColor = getToColor(mrp);} else {gl_FragColor = getFromColor(mrp);}}`",
/*ST*/		'burnover' => "`uniform vec4 resolution; uniform float progress; uniform sampler2D src1; uniform sampler2D src2; uniform sampler2D displacement; uniform int useTexture; varying vec2 vUv; void main() { float p = progress; vec2 uv = (vUv - vec2(0.5)) * resolution.zw + vec2(0.5); vec4 col = vec4(0.); vec4 heightmap = TEXTURE2D(displacement, uv).rrra; vec4 background = TEXTURE2D(src1, uv); vec4 foreground = TEXTURE2D(src2, uv); float t = p * 1.2; vec4 erosion = smoothstep(t - .2, t, heightmap); vec4 border = smoothstep(0., .1, erosion) - smoothstep(.1, 1., erosion); col = (1. - erosion) * foreground + erosion * background; vec4 leadcol = vec4(1., .5, .1, 1.); vec4 trailcol = vec4(0.2, .4, 1., 1.); vec4 fire = mix(leadcol, trailcol, smoothstep(0.8, 1., border)) * 2.; col += border * fire; gl_FragColor = col; }`",
/*GL*/		'dreamy' => "`uniform vec4 resolution;uniform float intensity;uniform float progress;uniform sampler2D src1;uniform sampler2D src2;varying vec2 vUv;vec4 getFromColor(vec2 uv){return TEXTURE2D(src1, uv);}vec4 getToColor(vec2 uv){return TEXTURE2D(src2, uv);}vec2 offset(float progress, float x, float theta) {float phase = progress*progress + progress + theta;float shifty = 0.03*progress*cos(10.0*(progress+x));return vec2(0, shifty);}void main() {vec2 p = (vUv - vec2(0.5))*resolution.zw + vec2(0.5);gl_FragColor = mix(getFromColor(p + offset(progress, p.x, 0.0)), getToColor(p + offset(1.0-progress, p.x, 3.14)), progress);}`",
/*GL*/		'mirrorcube' => "`uniform float persp;uniform float unzoom;uniform float reflection;uniform float floating;uniform vec4 resolution;uniform float progress;uniform sampler2D src1;uniform sampler2D src2;varying vec2 vUv;vec4 getFromColor(vec2 uv){return TEXTURE2D(src1, uv);}vec4 getToColor(vec2 uv){return TEXTURE2D(src2, uv);}vec2 project (vec2 p) {return p * vec2(1.0, -1.2) + vec2(0.0, -floating/100.);}bool inBounds (vec2 p) {return all(lessThan(vec2(0.0), p)) && all(lessThan(p, vec2(1.0)));}vec4 bgColor (vec2 p, vec2 pfr, vec2 pto) {vec4 c = vec4(0.0, 0.0, 0.0, 1.0);pfr = project(pfr);if (inBounds(pfr)) {c += mix(vec4(0.0), getFromColor(pfr), reflection * mix(1.0, 0.0, pfr.y));}pto = project(pto);if (inBounds(pto)) {c += mix(vec4(0.0), getToColor(pto), reflection * mix(1.0, 0.0, pto.y));}return c;}vec2 xchaos (vec2 p, float persp, float center) {float x = mix(p.x, 1.0-p.x, center);return ((vec2( x, (p.y - 0.5*(1.0-persp) * x) / (1.0+(persp-1.0)*x) )- vec2(0.5-distance(center, 0.5), 0.0))* vec2(0.5 / distance(center, 0.5) * (center<0.5 ? 1.0 : -1.0), 1.0)+ vec2(center<0.5 ? 0.0 : 1.0, 0.0));}void main() {vec2 op=(vUv - vec2(0.5))*resolution.zw + vec2(0.5);float uz = unzoom * 2.0*(0.5-distance(0.5, progress));vec2 p = -uz*0.5+(1.0+uz) * op;vec2 fromP = xchaos((p - vec2(progress, 0.0)) / vec2(1.0-progress, 1.0),1.0-mix(progress, 0.0, persp),0.0);vec2 toP = xchaos(p / vec2(progress, 1.0),mix(pow(progress, 2.0), 1.0, persp),1.0);if (inBounds(fromP)) {gl_FragColor = getFromColor(fromP);}else if (inBounds(toP)) {gl_FragColor = getToColor(toP);} else {gl_FragColor = bgColor(op, fromP, toP);}}`",
/*OWN*/		'flat' => "`uniform float zoom; uniform float prange; uniform float tilt; uniform float left; uniform float top; uniform vec4 resolution; uniform float progress; uniform sampler2D src1; uniform sampler2D src2; uniform sampler2D displacement; varying vec2 vUv; vec2 mirror(vec2 v) { vec2 m = mod(v, 2.0); return mix(m, 2.0 - m, step(1.0, m)); } float map(float a, float b, float c, float d, float v, float cmin, float cmax) { return clamp((v - a) * (d - c) / (b - a) + c, cmin, cmax); } vec2 rotateUV(vec2 uv, float rotation, vec2 mid) { return vec2(cos(rotation) * (uv.x - mid.x) + sin(rotation) * (uv.y - mid.y) + mid.x, cos(rotation) * (uv.y - mid.y) - sin(rotation) * (uv.x - mid.x) + mid.y); } void main() { vec2 uv = (vUv - vec2(0.5)) * resolution.zw + vec2(0.5); float m = progress; m = smoothstep(0., 1., m); vec2 nUv = vec2(.0, .0); if (tilt != 0.) { float tm = map(0.5, 1., 0., 10., m, 0., 10.); float mc = tm > 9.999 ? 0. : (tm < 8.236 ? max(0., sin(tm / 6.)) : sin(tm - 0.58)); mc = map(0., 10.005, 0., 3., mc, 0., 10.005); nUv = rotateUV(uv, mc * -radians(360. * tilt), vec2(.5, .5)); nUv.x += m * left; nUv.y += m * top; } else { nUv = vec2(uv.x + m * left, uv.y + m * top); } nUv.x -= 0.5; nUv.y -= 0.5; nUv *= 1. + (sin(m * 3.141592653)) * (1. / zoom - 1.); nUv.x += 0.5; nUv.y += 0.5; nUv = mirror(nUv); vec2 nUvIn = nUv; if(mod(left, 2.0) != 0.) nUvIn.x *= -1.; if(mod(top, 2.0) != 0.) nUvIn.y *= -1.; float nprog = map((0.5 - prange), (0.5 + prange), 0., 1., m, 0., 1.); vec4 col = mix(TEXTURE2D(src1, nUv), TEXTURE2D(src2, nUvIn), nprog); gl_FragColor = col; }`",
/*OWN*/	 	'pano' => "`uniform float prange; uniform float tilt; uniform float left; uniform float top; uniform float pano; uniform float zoomOut; uniform float progress; uniform vec4 resolution; uniform sampler2D src1; uniform sampler2D src2; varying vec2 vUv; vec2 mirror(vec2 v) { vec2 m = mod(v, 2.0); return mix(m, 2.0 - m, step(1.0, m)); } float map(float a, float b, float c, float d, float v, float cmin, float cmax) { return clamp((v - a) * (d - c) / (b - a) + c, cmin, cmax); } vec2 rotateUV(vec2 uv, float rotation, vec2 mid) { return vec2(cos(rotation) * (uv.x - mid.x) + sin(rotation) * (uv.y - mid.y) + mid.x, cos(rotation) * (uv.y - mid.y) - sin(rotation) * (uv.x - mid.x) + mid.y); } void main() { vec2 uv = (vUv - vec2(0.5)) * resolution.zw + vec2(0.5); vec3 vw = vec3((uv - 0.5), pano); vec3 vwo = vw; float m = progress; float w = m; w = map(0.1, 0.9, 0.0, 1., w, 0., 1.); float mult = (w - 0.5) * 2.; mult = (-(mult * mult) + 1.); m = smoothstep(0., 1., m); vw = normalize(vw) * zoomOut; vw = mix(vwo, vw, mult); uv = 0.5 + (vw.xy); vec2 nUv = vec2(.0, .0); if (tilt != 0.) { float tm = map(0.5, 1., 0., 10., m, 0., 10.); float mc = tm > 9.999 ? 0. : (tm < 8.236 ? max(0., sin(tm / 6.)) : sin(tm - 0.58)); mc = map(0., 10.005, 0., 3., mc, 0., 10.005); nUv = rotateUV(uv, mc * -radians(360. * tilt), vec2(.5, .5)); nUv.x += m * left; nUv.y += m * top; } else { nUv = vec2(uv.x + m * left, uv.y + m * top); } nUv = mirror(nUv); vec2 nUvIn = nUv; if(mod(left, 2.) != 0.) nUvIn.x *= -1.; if(mod(top, 2.) != 0.) nUvIn.y *= -1.; float nprog = map((0.5 - prange), (0.5 + prange), 0., 1., m, 0., 1.); vec4 col = mix(TEXTURE2D(src1, nUv), TEXTURE2D(src2, nUvIn), nprog); gl_FragColor = col; }`",
/*OWN*/  	'chaos' => "`uniform float prange; uniform float left; uniform float top; uniform int dir; uniform float progress; uniform vec4 resolution; uniform sampler2D src1; uniform sampler2D src2; uniform float intensity; varying vec2 vUv; vec2 mirror(vec2 v) { vec2 m = mod(v, 2.0); return mix(m, 2.0 - m, step(1.0, m)); } float map(float a, float b, float c, float d, float v, float cmin, float cmax) { return clamp((v - a) * (d - c) / (b - a) + c, cmin, cmax); } void main() { vec2 uv = vUv; vec2 vw = uv - 0.5; vec2 vwo = vw; float m = progress; float w = m; float mult = (w - 0.5) * 2.; mult = (-(mult * mult) + 1.); #replaceChaos vw = mix(vwo, vw, mult * intensity/10.); uv = .5 + (vw.xy); vec2 nUv = vec2(uv.x + m * left, uv.y + m * top); nUv = mirror(nUv); vec2 nUvIn = nUv; if(mod(left, 2.) != 0.) nUvIn.x *= -1.; if(mod(top, 2.) != 0.) nUvIn.y *= -1.; float nprog = map((0.5 - prange), (0.5 + prange), 0., 1., progress, 0., 1.); vec4 col = mix(TEXTURE2D(src1, nUv), TEXTURE2D(src2, nUvIn), nprog); gl_FragColor = col; }`",
/*OWN*/	 	'stretch' => "`uniform float left; uniform float top; uniform int dir; uniform float intensity; uniform float twistIntensity; uniform float twistSize; uniform float flipTwist; uniform float progress; uniform vec4 resolution; uniform sampler2D src1; uniform sampler2D src2; varying vec2 vUv; float pi = 3.141592653; vec2 mirror(vec2 v) { vec2 m = mod(v, 2.0); return mix(m, 2.0 - m, step(1.0, m)); } float map(float a, float b, float c, float d, float v) { return clamp((v - a) * (d - c) / (b - a) + c, 0., 1.); } void main() { float ratio = resolution.x / resolution.y; vec2 uv = vUv; vec2 uvc = uv; vec2 vw = uv; vec2 vwo = vw; float m = progress; float steps = 1.0 / (abs(left + top) + 2.); float ms = map(steps, 1.0 - steps, 0., 1., m); float flip = (m - 0.5) * 2.; float signFlip = -sign(left + top); float mult = sin(m * pi); if (dir == 1) { vw.x = uv.x * 1. / intensity; vw = mix(vwo, vw, mult); uv.x = vw.x + (-flip * signFlip > 0. ? .0 * mult : 1. * mult); if (twistIntensity != 0.) { uv.y += mult * flip * signFlip * twistIntensity / 20. * flipTwist; uv.y += twistIntensity * flip * pow((flip * -signFlip > 0. ? uvc.x : 1. - uvc.x), twistSize) * mult * flipTwist * ratio / 5.; } } else { vw.y = uv.y * 1. / intensity; vw = mix(vwo, vw, mult); uv.y = vw.y + (flip * -signFlip > 0. ? .0 * mult : 1. * mult); if (twistIntensity != 0.) { uv.x += mult * flip * twistIntensity / 20. * flipTwist; uv.x += twistIntensity * flip * pow((flip * -signFlip > 0. ? uvc.y : 1. - uvc.y), twistSize) * mult * flipTwist / ratio / 5.; } } vec2 nUv = vec2(uv.x + ms * left, uv.y + ms * top); nUv = mirror(nUv); vec2 nUvO = nUv; if(mod(left, 2.0) != 0.) nUv.x *= -1.; if(mod(top, 2.0) != 0.) nUv.y *= -1.; gl_FragColor = mix(TEXTURE2D(src1, nUvO), TEXTURE2D(src2, nUv), ms);}`", 
/*OWN*/	 	'skew' => "` uniform float left; uniform float top; uniform int dir; uniform float intensity; uniform float origin; uniform bool sh; uniform float shx; uniform float shy; uniform float shr; uniform float shz; uniform float shv; uniform float prange; uniform float progress; uniform vec4 resolution; uniform sampler2D src1; uniform sampler2D src2; varying vec2 vUv; float pi = 3.141592653; float map(float a, float b, float c, float d, float v){ return clamp((v-a)*(d-c)/(b-a) + c, 0., 1.); } vec2 mirror(vec2 v) { vec2 m = mod(v, 2.0); return mix(m, 2.0 - m, step(1.0, m)); } vec2 rotateUV(vec2 uv, float rotation, vec2 mid){ return vec2( cos(rotation) * (uv.x - mid.x) + sin(rotation) * (uv.y - mid.y) + mid.x, cos(rotation) * (uv.y - mid.y) - sin(rotation) * (uv.x - mid.x) + mid.y ); } vec2 getAngle(vec2 p1){ return vec2(.5, .5) * normalize(p1); } float qinticOut(float t, float power) { return 1.0 - pow(1. - t, power); } float elasticOut(float t) { return sin(13.0 * t * pi / shv) * pow(5.5, 3. * (t - 1.0)); } vec2 shake(vec2 uv, float p, float shx, float shy, float tilt, float z) { float m = elasticOut(p); float n = (1. - p) * p; if (tilt != 0.) { float tmc = (1. - m) * m * n; uv = rotateUV(uv, tmc * tilt,vec2(.5,.5)); } if (z != 0.) { p = p * p; uv.x -= 0.5; uv.y -= 0.5; uv *= (z * p * n + 1.); uv.x += 0.5; uv.y += 0.5; } if (shx != 0. && shy != 0.) { uv.x += m * shx * 2. * n; uv.y -= m * (1. - m) * shy * n; } return uv; } void main() { vec2 uv = vUv; vec2 vw = uv; vec2 vwo = vw; float m = progress; float steps = 1.0/(max(abs(left),abs(top)) + 2.); float ms = m; if(sh) ms = qinticOut(m, 10.); float flip = (m -0.5) * 2.; float mult = sin(ms * pi); mult = min(mult, 0.5); if(dir == 1 || dir == 2) mult /= 10.; if(dir == 1) { float shift = origin <= 0.5 ? uv.y - origin : origin - uv.y; float shift2 = origin == 0. || origin == 1. ? 1. : origin; vw.x += sign(left) * mix( shift, shift - 1., ms) * intensity; } else if (dir == 2) { float shift = origin <= 0.5 ? uv.x - origin : origin - uv.x; float shift2 = origin == 0. || origin == 1. ? 1. : origin; vw.y += sign(top) * mix(shift, shift - shift2, ms) * intensity; } else { vec2 d1 = getAngle(vec2(0.5 * top, 0.5 * left)); vec2 d2 = getAngle(vec2(0.5 * top, 0.5 * left)); float l1 = length(vec2(left > 0. ? 1. - uv.x : uv.x, top > 0. ? 1. - uv.y : uv.y)); float l2 = length(vec2(left > 0. ? uv.x : 1. - uv.x, top > 0. ? uv.y : 1. - uv.y)); vec2 a = vw + l1 * d1 * flip * (step(top * left, 0.) - 0.5) * intensity; vec2 b = vw + l2 * d2 * flip * (step(top * left, 0.) - 0.5) * intensity; vw = mix(a, b, ms); } uv = mix(vwo, vw, mult); if(sh) uv = shake(uv, 1. - m, shx, shy, shr, shz); vec2 nUv = vec2(uv.x + ms  * left, uv.y + ms * top); nUv = mirror(nUv); vec2 nUvIn = nUv; if(mod(left, 2.0) != 0.) nUvIn.x *= -1.; if(mod(top, 2.0) != 0.) nUvIn.y *= -1.; float nprog = map((0.5 - prange),(0.5+prange),0.,1.,ms); vec4 col = mix(TEXTURE2D(src1, nUv),  TEXTURE2D(src2, nUvIn), nprog); gl_FragColor = col; }`",
/*OWN*/	 	'perspective' => "`uniform float ox; uniform float oy; uniform float rotation; uniform bool isShort; uniform float intensity; uniform float angle; uniform float prange; uniform float progress; uniform vec4 resolution; uniform sampler2D src1; uniform sampler2D src2; varying vec2 vUv; float pi = 3.141592653; vec2 rotate(vec2 uv, vec2 mid, float rotation) { return vec2(cos(rotation) * (uv.x - mid.x) + sin(rotation) * (uv.y - mid.y) + mid.x, cos(rotation) * (uv.y - mid.y) - sin(rotation) * (uv.x - mid.x) + mid.y); } float map(float a, float b, float c, float d, float v, float cmin, float cmax){ return clamp((v-a)*(d-c)/(b-a) + c, cmin, cmax); } vec2 plane3d(vec2 uv, vec2 center, float xr, float yr) { vec2 rd = vec2(uv.x, 0.); vec2 a1 = vec2(0., -1.); vec2 b1 = rd - a1; vec2 c1 = rotate(vec2(-1., 0.), vec2(center.x, 0.), yr); vec2 d1 = rotate(vec2(1., 0.), vec2(center.x, 0.), yr) - c1; float u = ((c1.y + 1.) * d1.x - c1.x * d1.y) / (d1.x * b1.y - d1.y * b1.x); float sx = u * b1.x; float sy = u * uv.y; rd = vec2(sy, 0.); vec2 b2 = rd - a1; vec2 c2 = rotate(vec2(-1., 0.), vec2(center.y, 0.), xr); vec2 d2 = rotate(vec2(1., 0.), vec2(center.y, 0.), xr) - c2; float v = ((c2.y + 1.) * d2.x - c2.x * d2.y) / (d2.x * b2.y - d2.y * b2.x); return vec2(v * sx, v * b2.x); } vec2 rotatePlane(vec2 uv, vec2 o, float rx, float ry) { uv = uv * 2. - 1.; uv = plane3d(uv, o, rx, ry); uv = (1. + uv) / 2.; return uv; } float map(float a, float b, float c, float d, float v) { return clamp((v - a) * (d - c) / (b - a) + c, 0., 1.); } vec2 mirror(vec2 v) { vec2 m = mod(v, 2.0); return mix(m, 2.0 - m, step(1.0, m)); } vec2 zoom(vec2 uv, float p, float z) { float m = sin(pi * pow(p, 1.)); if (z != 0.) { uv.x -= 0.5; uv.y -= 0.5; uv *= (z * m + 1.); uv.x += 0.5; uv.y += 0.5; } return uv; } float quarticInOut(float t) { return t < 0.5 ? +8.0 * pow(t, 4.0) : -8.0 * pow(t - 1.0, 4.0) + 1.0; } void main() { vec2 uv = vUv; float aspect = resolution.x / resolution.y; vec2 origin = vec2(ox, oy); float m = progress; float nprog = map((0.5 - prange),(0.5+prange),0.,1.,m,0.,1.); float tr = sin(pi * pow(map(0.0, .7, 0., 1., m), 2.)); float rm = map(0., 1., 0., 1., m); rm = quarticInOut(rm); float rx = tr * radians(intensity) * cos(angle); float ry = tr * radians(intensity) * sin(angle); uv = zoom(uv, 1. - rm, .5); vec2 tUv = uv; tUv = rotatePlane(tUv, origin, rx, ry); origin.x *= aspect; tUv.x *= aspect; tUv = rotate(tUv, origin, rm * rotation); tUv.x /= aspect; vec2 tUvIn = tUv; if(isShort){ tUvIn.x -= origin.x/aspect * 2.; tUvIn.y -= origin.y * 2.; } tUvIn = mirror(tUvIn); vec2 nUv = tUv; nUv = mirror(nUv); gl_FragColor = mix(TEXTURE2D(src1, nUv), TEXTURE2D(src2, tUvIn), nprog); } `",
/*OWN*/	 	'spin' => "` const int Samples = 32; uniform float ox; uniform float oy; uniform float roz; uniform float xdist; uniform float ydist; uniform float zoom; uniform float intensity; uniform bool isShort; uniform float prange; uniform float progress; uniform vec4 resolution; uniform sampler2D src1; uniform sampler2D src2; varying vec2 vUv; float pi = 3.141592653; vec2 mirror(vec2 v) { vec2 m = mod(v, 2.0); return mix(m, 2.0 - m, step(1.0, m)); } vec2 rotate(vec2 uv, vec2 mid, float rotation) { return vec2(cos(rotation) * (uv.x - mid.x) + sin(rotation) * (uv.y - mid.y) + mid.x, cos(rotation) * (uv.y - mid.y) - sin(rotation) * (uv.x - mid.x) + mid.y); } float map(float a, float b, float c, float d, float v) { return clamp((v - a) * (d - c) / (b - a) + c, 0., 1.); } vec2 getSample(vec2 temp, vec2 o, float ratio, int i, float intensity) { o.x *= ratio; temp.x *= ratio; temp = rotate(temp, o, float(i) * intensity); temp.x /= ratio; temp = mirror(temp); return temp; } vec2 zoomFunction(vec2 uv, vec2 o, float z, float m) { uv -= o; uv *= 1. + z * m; uv += o; return uv; } void main() { vec2 o = vec2(ox, oy); float Intensity = intensity; vec2 uv = vUv; float ratio = resolution.x / resolution.y; vec2 dir = uv - o; vec4 color = vec4(0.0, 0.0, 0.0, 0.0); float m = progress; m = map(0., 0.999, 0., 1., m); float mult = sin(m * pi); float zm = mult; Intensity *= pow(mult, 2.) * 0.1; o.x *= ratio; uv.x *= ratio; uv = rotate(uv, vec2(o.x, o.y), roz * 1. * pi * m); o.x /= ratio; uv.x /= ratio; if (zoom != 0.) uv = zoomFunction(uv, o, max(zoom, -0.9), zm); vec2 uvIn = uv; vec2 nO = o; if (isShort) { uvIn.x -= o.x * 2.; uvIn.y -= o.y * 2.; nO.x -= o.x * 2.; nO.y -= o.y * 2.; } float nprog = map((0.5 - prange), (0.5 + prange), 0., 1., m); vec2 bUv = uv; vec2 temp = bUv; vec2 tempIn = uv; for (int i = 0; i < Samples; i += 2) { temp = bUv; temp = getSample(temp, o, ratio, i, -Intensity); tempIn = uvIn; tempIn = getSample(tempIn, nO, ratio, i, -Intensity); color += mix(TEXTURE2D(src1, temp), TEXTURE2D(src2, tempIn), nprog); temp = bUv; temp = getSample(temp, o, ratio, i + 1, -Intensity); tempIn = uvIn; tempIn = getSample(tempIn, nO, ratio, i, -Intensity); color += mix(TEXTURE2D(src1, temp), TEXTURE2D(src2, tempIn), nprog); } gl_FragColor = color / float(Samples); }`",
/*OWN*/	 	'rings' => "` uniform float roz; uniform float ox; uniform float oy; uniform bool isShort; uniform float iny; uniform float Splits; uniform float s; uniform vec4 iColor; uniform bool cnprog; uniform bool useo; uniform float grado; uniform bool cover; uniform bool altDir; uniform float prange; uniform float progress; uniform vec4 resolution; uniform sampler2D src1; uniform sampler2D src2; varying vec2 vUv; float pi = 3.141592653; float map(float a, float b, float c, float d, float v, float cmin, float cmax){ return clamp((v-a)*(d-c)/(b-a) + c, cmin, cmax); } vec2 rotate2D(vec2 p, float theta) { return p * mat2(cos(theta), -sin(theta), sin(theta), cos(theta)); } vec2 mirror(vec2 v) { vec2 m = mod(v, 2.0); return mix(m, 2.0 - m, step(1.0, m)); } vec2 rotateUV(vec2 uv, float rotation, vec2 mid) { return vec2( cos(rotation) * (uv.x - mid.x) + sin(rotation) * (uv.y - mid.y) + mid.x, cos(rotation) * (uv.y - mid.y) - sin(rotation) * (uv.x - mid.x) + mid.y ); } vec4 cutPart( float m, float amount, vec2 uv, float minv, float maxv, float ratio, float i, float ox, float oy, float nprog) { if(cover){ minv = minv * ratio; maxv = maxv * ratio; } float offsmax = (Splits - 1.)/Splits; float offset = (Splits - i - 1.)/Splits; m = map((offsmax - offset)/iny, 1. - offset/iny, 0., 1., m, 0. ,1.); float zm = sin(pi * pow(m, 2.)); float cm = min(sin(pi * m), 0.3)/0.3; float mult = sin(m * pi); vec4 c = vec4(0.,0.,0.,0.); if(cnprog) nprog = map((offsmax - offset)/max(iny/2.5, 1.), 1. - offset/max(iny/2.5, 1.), 0., 1., m, 0. ,1.); float dir = altDir ? sign(mod(i, 2.0) - 0.5) : 1.; ox *= ratio; uv.x *= ratio; uv = rotateUV(uv, (m * amount + -sign(amount) * mult * 1. * (Splits - i)/Splits) * dir,vec2(ox,oy)); float d = length(uv - vec2(ox, oy)); uv.x /= ratio; uv *= (1. - zm* 1. * i/Splits); vec2 cUv = uv; if(isShort){ cUv.x -= ox/ratio * 2.; cUv.y -= oy * 2.; } cUv = mirror(cUv); uv = mirror(uv); float a = smoothstep(minv, minv + 0.005, d); float b = 1. - smoothstep(maxv, maxv + 0.005, d); if(minv == 0.) a = step(minv, d); if(maxv >= 1. * (cover ? ratio : 1.)) b = step(minv, d); vec4 tex = mix(TEXTURE2D(src1,uv),TEXTURE2D(src2,cUv), nprog); vec4 color = vec4(0.); if(useo || s != 0.){ color = iColor; float sh = 1. - smoothstep(minv, minv + (maxv - minv) * s, d); float ol = grado == 1. || grado == 2. ? 1. : mod(i, 2.); float gol = grado == 1. ? (Splits-i - 1.)/Splits : grado == 2. ? i/Splits : 1.; if(minv <= 0.) sh = 0.; if(useo && ol != 0.) sh = 1.; if(ol * ( a * b) != 0. || s != 0.) tex.rgb = mix(tex, color, color.a * sh * gol * cm).rgb; } vec4 f = vec4(0.); return mix(f, tex, a * b); } void main() { float amount = roz * pi; vec2  uv = vUv; float ratio = resolution.x / resolution.y; float m = progress; m = map(0., 0.999, 0., 1., m, 0., 1.); float nprog = map((0.5 - prange),(0.5+prange),0.,1.,m,0.,1.); vec4 color = vec4(0.); for (int i = 0; i < splits; i++) { float min = float(i)/Splits; float max = float(i)/Splits + 1./Splits; color += cutPart(m,amount,uv,min,max,ratio,float(i), ox, oy, nprog); } gl_FragColor = color; } `",
/*OWN*/	 	'zoom' => "` const int Samples = 32; float warp = 2.; float mapPow = .5; const float power = 9.0; uniform float ox; uniform float oy; uniform float zIn; uniform float zOut; uniform float warpIn; uniform float warpOut; uniform float blur; uniform float roz; uniform float rEnd; uniform bool isShort; uniform float prange; uniform float progress; uniform vec4 resolution; uniform sampler2D src1; uniform sampler2D src2; varying vec2 vUv; float pi = 3.141592653; vec2 mirror(vec2 v) { vec2 m = mod(v, 2.0); return mix(m, 2.0 - m, step(1.0, m)); } float map(float a, float b, float c, float d, float v, float cmin, float cmax) { return clamp((v - a) * (d - c) / (b - a) + c, cmin, cmax); } float map(float a, float b, float c, float d, float v) { return (v - a) * (d - c) / (b - a) + c; } float powerOut(float t) { return 1.0 - pow(1.0 - t, power); } float mapToEase(float a, float b, float v, float z) { return powerOut(map(a, b, 0., 1., v)) / (warp); } vec4 rayBlur(vec2 uv, vec2 uvo, vec2 o, float m, float nprog) { vec2 dirOut = mix(uvo, uv, step(zOut, 0.)) - o; vec2 dirIn = mix(uv, uvo, step(zIn, 0.)) - o; float bm = sin(pi * m); float Blur = blur != 0. ? blur * bm * max(zOut, -.9) : 0.; float iBlur = blur != 0. ? blur * bm * max(zIn, -.9) : 0.; if (isShort) { uvo.x -= o.x * 2.; uvo.y -= o.y * 2.; } vec4 color = vec4(0.); for (int i = 0; i < Samples; i += 2) { color += mix(TEXTURE2D(src1, mirror(uv + float(i) / float(Samples) * dirOut * Blur)), TEXTURE2D(src2, mirror(uvo + float(i) / float(Samples) * dirIn * iBlur)), nprog); color += mix(TEXTURE2D(src1, mirror(uv + float(i + 1) / float(Samples) * dirOut * Blur)), TEXTURE2D(src2, mirror(uvo + float(i + 1) / float(Samples) * dirIn * iBlur)), nprog); } return color / float(Samples); } vec2 hitEffect(vec2 uv, vec2 o, float m, sampler2D t, float z, bool animOut) { z = max(z, -0.9); m = 1. - sin(pi * m); float dist = distance(o, uv); float angle = atan(uv.y - o.y, uv.x - o.x); vec2 uvo = uv; dist = mix(mapToEase(0., pow(animOut ? warpOut : warpIn, mapPow), dist, z), dist, m); uv.x = o.x + cos(angle) * dist; uv.y = o.y + sin(angle) * dist; uv = mix(uv, uvo, m); return uv; } vec2 zoom(vec2 uv, vec2 o, float z, float m) { uv -= o; uv *= 1. + z * m; uv += o; return uv; } vec2 rotate(vec2 uv, vec2 mid, float rotation) { return vec2( cos(rotation) * (uv.x - mid.x) + sin(rotation) * (uv.y - mid.y) + mid.x, cos(rotation) * (uv.y - mid.y) - sin(rotation) * (uv.x - mid.x) + mid.y ); } void main() { vec2 o = vec2(ox, oy); vec2 uv = vUv; float ratio = resolution.x / resolution.y; float m = progress; float nprog = map((0.5 - prange), (0.5 + prange), 0., 1., m, 0., 1.); float rm = map(0., rEnd, 0., 1., m, 0., 1.); o.x *= ratio; uv.x *= ratio; uv = rotate(uv, o, roz * pi * rm); uv.x /= ratio; o.x /= ratio; vec2 uvo = uv; vec2 uvz = uv; if (warpOut != 0.) uv = hitEffect(uv, o, 1. - m, src1, zOut, true); if (warpIn != 0.) uvo = hitEffect(uvo, o, m, src2, zIn, false); if (zOut != 0.) uv = zoom(uv, o, max(zOut, -0.9), m); if (zIn != 0.) uvo = zoom(uvo, o, max(zIn, -0.9), 1. - m); gl_FragColor = rayBlur(uv, uvo, o, m, nprog); } `"
		);
		return $shaders;
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-transitionpack-addon') {
		
		if($slug === 'revslider-transitionpack-addon'){
			
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
			$definitions['editor_settings']['slide_settings']['addons']['transitionpack'] = $help['slide'];
		}
		
		return $definitions;
	
	}
	
	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public static function get_var() {
			
		$_textdomain = 'revslider-transitionpack-addon';
		return array(
		
			'bricks' => array(
				'film' => __('Film', $_textdomain),							
				'flat' => __('Flat', $_textdomain),							
				'reflection' => __('Reflection', $_textdomain),							
				'floating' => __('Floating', $_textdomain),							
				'mirrorcube' => __('Mirrored Cube', $_textdomain),							
				'dreamy' => __('Dreamy', $_textdomain),							
				'mosaic' => __('Mosaic', $_textdomain),							
				'waterdrop' => __('Water Drop', $_textdomain),							
				'morph' => __('Morph', $_textdomain),							
				'blur' => __('Linear Blur', $_textdomain),							
				'left' => __('Left', $_textdomain),							
				'right' => __('Right', $_textdomain),							
				'direction' => __('Direction', $_textdomain),							
				'anchor' => __('Anchor', $_textdomain),							
				'distance' => __('Distance', $_textdomain),							
				'twistsimple' => __('Twist Simple', $_textdomain),							
				'twistwave' => __('Twist Wave', $_textdomain),							
				'twist' => __('Twist', $_textdomain),							
				'twisteffect' => __('Twist Effect', $_textdomain),							
				'horizontal' => __('Horizontal', $_textdomain),							
				'vertical' => __('Vertical', $_textdomain),							
				'twists' => __('Twist', $_textdomain),
				'fog' => __('Fog', $_textdomain),
				'curtain' => __('Curtain', $_textdomain),
				'burn' => __('Burn Effect', $_textdomain),							
				'zoomover' => __('Zoom Over', $_textdomain),							
				'intensity' => __('Intensity', $_textdomain),							
				'tpack' => __('Advanced', $_textdomain),							
				'cube' => __('Cube Animations', $_textdomain),							
				'burnover' => __('Burn Over', $_textdomain),							
				'tpburn' => __('Burn Effects', $_textdomain),							
				'tpfluid' => __('Fluid Effects', $_textdomain),							
				'tpcuts' => __('Cut & Slide Effects', $_textdomain),							
				'tpflats' => __('Motion Effects', $_textdomain),							
				'tpstmelt' => __('Melt Effects', $_textdomain),							
				'tpstsk' => __('Stretch & Skew Effects', $_textdomain),							
				'tprolls' => __('Roll Effects', $_textdomain),							
				'transpack' => __('Transition Pack', $_textdomain),							
				'cubesettings' => __('Cube', $_textdomain),							
				'crossfadesettings' => __('CrossFade', $_textdomain),											
				'effect' => __('Effect', $_textdomain),
				'speed' => __('Speed', $_textdomain),
				'basic' => __('Settings', $_textdomain),
				'map' => __('Map Fade Effect', $_textdomain),							
				'wave' => __('Wave Effect', $_textdomain),
				'overscale' => __('Overscale Effect', $_textdomain),
				'horwater' => __('Horizontal Water Effect', $_textdomain),
				'cut' => __('Cut Overfade Effect', $_textdomain),
				'overroll' => __('Overroll Effect', $_textdomain),
				'verticalmelt' => __('Vertical Melt (Between Slides)', $_textdomain),
				'rotateeffect' => __('Pixel Rotation', $_textdomain),
				'stretch' => __('Stretch', $_textdomain),
				'colorflow' => __('Color Flow', $_textdomain),
				'radius' => __('Radius', $_textdomain),
				'rippleiny' => __('Ripple Int.', $_textdomain),
				'width' => __('Width', $_textdomain),
				'filters' => __('Filters', $_textdomain),							
				'none' => __('None', $_textdomain),							
				'glitches' => __('Glitches', $_textdomain),							
				'glitches2' => __('Glitch & Noise', $_textdomain),							
				'ftop' => __('From Top', $_textdomain),							
				'fbottom' => __('From Bottom', $_textdomain),							
				'fleft' => __('From Left', $_textdomain),							
				'fright' => __('From Right', $_textdomain),							
				'blur' => __('Blur', $_textdomain),							
				'blur2d' => __('Motion Blur (2D)', $_textdomain),							
				'blur3d' => __('Blur 3D (i.e. Cube)', $_textdomain),							
				'blurrotation' => __('Radial Blur', $_textdomain),							
				'blurtype' => __('Blur Type', $_textdomain),							
				'map1' => __('Burn', $_textdomain),							
				'map2' => __('Boxes', $_textdomain),							
				'map3' => __('Leopard Skin', $_textdomain),							
				'map4' => __('Melting Ice', $_textdomain),							
				'map5' => __('Horizontal Fade', $_textdomain),							
				'map17' => __('Vertical Fade', $_textdomain),							
				'map6' => __('Clock Fade', $_textdomain),							
				'map7' => __('Cloud', $_textdomain),							
				'map8' => __('Cell', $_textdomain),											
				'map9' => __('Cell 2', $_textdomain),						
				'map10' => __('Bubbles', $_textdomain),	
				'map11' => __('Cracks', $_textdomain),	
				'map12' => __('Tunnel', $_textdomain),	
				'map13' => __('Waterfall', $_textdomain),	
				'map14' => __('Wire', $_textdomain),	
				'map15' => __('ZigZag', $_textdomain),	
				'map16' => __('Paper', $_textdomain),	
				'noflip' => __('No Flip', $_textdomain),							
				'flip' => __('Flip', $_textdomain),							
				'dirflip' => __('Direction Based Flip', $_textdomain),							
				'water' => __('Water Effect', $_textdomain),							
				'grayscale' => __('Grayscaled Film', $_textdomain),							
				'colored' => __('Colored Film', $_textdomain),							
				'fixlabel' => __('Fixed Label', $_textdomain),
				'sphere' => __('Sphere', $_textdomain),
				'cylinder_h' => __('Cylinder Horizontal', $_textdomain),
				'cylinder_v' => __('Cylinder Vertical', $_textdomain),
				'tilt' => __('Tilt', $_textdomain),
				'prange' => __('Mix Range', $_textdomain),
				'chaos' => __('Chaos', $_textdomain),
				'mixValue' => __('Mix Value', $_textdomain),
				'type' => __('Type', $_textdomain),
				'random' => __('Random', $_textdomain),
				'stretch' => __('Stretch', $_textdomain),
				'twistintensity' => __('Twist Int.', $_textdomain),
				'flipTwist' => __('Flip Twist', $_textdomain),
				'twistsize' => __('Size', $_textdomain),				
				'efforigin' => __('Effect Origin', $_textdomain),				
				'perspective' => __('Perspective', $_textdomain),
				'shake' => __('Shake', $_textdomain),
				'shakestart' => __('Shake Start', $_textdomain),
				'shakeX' => __('Shake X', $_textdomain),
				'shakeY' => __('Y', $_textdomain),
				'shakeZ' => __('Shake Z', $_textdomain),
				'length' => __('Length', $_textdomain),
				'frequencyx' => __('Frequency X', $_textdomain),				
				'frequencyy' => __('Frequency y', $_textdomain),
				'inverse' => __('Inverse', $_textdomain),
				'tocenter' => __('To Center', $_textdomain),
				'animorigin' => __('Animate Origin', $_textdomain),
				'color' => __('Color', $_textdomain),
				'shadow' => __('Shadow', $_textdomain),
				'rings' => __('Rings', $_textdomain),
				'mixStaggered' => __('Mix Staggered', $_textdomain),
				'overlay' => __('Overlay', $_textdomain),
				'alternate' => __('Alternate', $_textdomain),
				'spinAlt' => __('Spin Alternate', $_textdomain),
				'gradual' => __('Gradual', $_textdomain),
				'cover' => __('Cover', $_textdomain),
				'zoomOut' => __('Zoom: Out', $_textdomain),
				'in' => __('In', $_textdomain),
				'warpOut' => __('Warp: Out', $_textdomain),
				'blurIntensity' => __('Blur Intensity', $_textdomain),
				'warpIntensity' => __('Warp Intensity', $_textdomain),
				'dbased' => __('Direct. Based', $_textdomain),
				'spinEnd' => __('Spin End Time', $_textdomain)
			)
		);
	
	}
	
	/**
	 * Returns the addon help definitions
	 *
	 * @since    2.0.0
	 */
	private static function get_definitions() {
		$u = 'https://www.themepunch.com/slider-revolution/';
		$fu = 'https://www.themepunch.com/faq/';
		$t = 'title';
		$h = 'helpPath';
		$k = 'keywords';
		$d = 'description';
		$a = 'article';
		$s = 'section';
		$hl = 'highlight';
		$m = 'menu';
		$st = 'scrollTo';
		$f = 'focus';
		$d = 'description';
		$di = 'dependency_id';
		$dp = 'dependencies';
		$p = 'path';
		$v = 'value';
		$o = 'option';

		return array(
						
			'slide' => array(
				'mfl' => array(
					$t => __("Flip Map", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.mfl",
					$k => array('tpack', 'addons', 'transition pack', 'map', 'flip', 'map direction'), 
					$d => __("Flips the effect map vertically/horizontally depending on map. Direction based flip will flip map based on slider direction", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.mfl']"
					)
				),		
				'dir' => array(
					$t => __("Transition Direction", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.dir",
					$k => array('tpack', 'addons', 'transition pack', 'transition direction'), 
					$d => __("Sets direction of animation", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.dir']"
					)
				),
				'dbas' => array(
					$t => __("Direction Based", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.dbas",
					$k => array('tpack', 'addons', 'transition pack', 'transition direction', 'direction based'), 
					$d => __("Changes direction of animations based on slider direction", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.dbas']"
					)
				),
				'rad' => array(
					$t => __("Ripple Intensity", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.rad",
					$k => array('tpack', 'addons', 'transition pack', 'transition ripple', 'ripple intensity'), 
					$d => __("Sets intensity of ripple/wave effect <br /> <ul><li><b>Water Drop: </b>Controls the number of ripples in effect</li><li><b>Wave Effect: </b>Controls size of wave effect, lower values create smaller transition wave creating a bit of zoom effect. Higher values create longer transition wave.</li></ul>", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.rad']"
					)
				),
				'w' => array(
					$t => __("Transition Effect Length", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.w",
					$k => array('tpack', 'addons', 'transition pack', 'transition length', 'effect size'), 
					$d => __("Sets length of effect", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.w']"
					)
				),
				'ssx' => array(
					$t => __("Frequncy X", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.ssx",
					$k => array('tpack', 'addons', 'transition pack', 'transition frequency', 'frequency', 'horizontal frequency'), 
					$d => __("Sets amount horizontal frequency for effect, using higher numbers create a bit of sprinkling effects", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.ssx']"
					)
				),
				'ssy' => array(
					$t => __("Frequncy Y", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.ssy",
					$k => array('tpack', 'addons', 'transition pack', 'transition frequency', 'frequency', 'vertical frequency'),
					$d => __("Sets amount vertical frequency for effect, using higher numbers create a bit of sprinkling effects", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.ssy']"
					)
				),
				'x' => array(
					$t => __("Position X", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.x",
					$k => array('tpack', 'addons', 'transition pack', 'transition steps', 'transition position'), 
					$d => __("Sets amount of horizontal steps transition will take", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.x']"
					)
				),
				'y' => array(
					$t => __("Position Y", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.y",
					$k => array('tpack', 'addons', 'transition pack', 'transition steps', 'transition position'),
					$d => __("Sets amount of vertical steps transition will take", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.y']"
					)
				),
				'z' => array(
					$t => __("Zoom", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.z",
					$k => array('tpack', 'addons', 'transition pack', 'transition zoom', 'zoom'),
					$d => __("Sets zoom for texture/image, negative value will zoom out texture and positive value will zoom in.", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.z']"
					)
				),
				'ox' => array(
					$t => __("Origin X", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.ox",
					$k => array('tpack', 'addons', 'transition pack', 'transition origin', 'origin x'),
					$d => __("Sets origin x for effect", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.ox']"
					)
				),
				'oy' => array(
					$t => __("Origin X", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.oy",
					$k => array('tpack', 'addons', 'transition pack', 'transition origin', 'origin y'),
					$d => __("Sets origin y for effect", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.oy']"
					)
				),
				'ao' => array(
					$t => __("Animate Origin", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.ao",
					$k => array('tpack', 'addons', 'transition pack', 'transition steps', 'transition origin', 'animate origin'),
					$d => __("Animate origin of the effect with transition <br /> <ul> <li><b>None: </b>by default origin will not animate</li> <li><b>Center: </b>Origin will transition from any set position to the center</li> <li><b>Inverse: </b>Inverse will cause origin to animate opposite side on x and y coordinates. For example, if origin is 'x: 10% and y: 10%' then origin will animate to 'x:90% y: 90%'</li> <li><b>Path: </b>Selecting different path for animating will animate origin over SVG path</li> </ul>", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.ao']"
					)
				),
				'iny' => array(
					$t => __("Transition Effect Intensity", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.iny",
					$k => array('tpack', 'addons', 'transition pack', 'transition steps', 'transition intensity', 'effect intensity'),
					$d => __("Sets effect intensity for different transition effects", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.iny']"
					)
				),
				'stri' => array(
					$t => __("Stretch: Twist Intensity", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.stri",
					$k => array('tpack', 'addons', 'transition pack', 'stretch twist', 'twist intensity'),
					$d => __("Sets twist intensity for stretch effect", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.stri']"
					)
				),
				'strs' => array(
					$t => __("Stretch: Twist Size", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.strs",
					$k => array('tpack', 'addons', 'transition pack', 'stretch twist', 'twist size'),
					$d => __("Sets twist size for stretch effect, higher number will create bigger twist", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.strs']"
					)
				),
				'strf' => array(
					$t => __("Stretch: Flip Twist", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.strf",
					$k => array('tpack', 'addons', 'transition pack', 'stretch twist', 'flip twist'),
					$d => __("Flips the direction of twist. For example, vertical stretch twists to left side, this option will flip twist to right side", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.strf']"
					)
				),
				'roz' => array(
					$t => __("Transition Rotations", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.roz",
					$k => array('tpack', 'addons', 'transition pack', 'rotations', 'spins'),
					$d => __("Sets the number of rotations transition will perform, negative value will spin image to opposite direction", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.roz']"
					)
				),
				'zre' => array(
					$t => __("Rotation End Time", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.zre",
					$k => array('tpack', 'addons', 'transition pack', 'rotation end', 'rotation time'),
					$d => __("Sets time in percentage when rotation will complete in transition, this option is useful to end rotation early and highlight the effect", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.zre']"
					)
				),
				'cispl' => array(
					$t => __("Transition Rings", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.cispl",
					$k => array('tpack', 'addons', 'transition pack', 'rings', 'rings count'),
					$d => __("Sets number of rings that will be created", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.cispl']"
					)
				),
				'cicl' => array(
					$t => __("Rings Overlay Color", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.cicl",
					$k => array('tpack', 'addons', 'transition pack', 'rings', 'rings overlay', 'overlay color'),
					$d => __("Sets overlay and shadow color for rings effect", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.cicl']"
					)
				),
				'cish' => array(
					$t => __("Rings Shadow", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.cish",
					$k => array('tpack', 'addons', 'transition pack', 'rings', 'rings shadow'),
					$d => __("Sets shadow distance in percentage for rings", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.cish']"
					)
				),
				'cio' => array(
					$t => __("Rings Overlay", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.cio",
					$k => array('tpack', 'addons', 'transition pack', 'rings', 'rings overlay'),
					$d => __("Sets overlay effect for rings <br /> <ul> <li><b>None: </b>No overlay on rings</li> <li><b>Alternate: </b>Alternate rings will have overlay color</li> <li><b>Gradual: </b>Rings overlay opacity will increase gradually from edge</li> <li><b>Inverse: </b>Rings overlay opacity will increase gradually from center</li> </ul>", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.cio']"
					)
				),
				'cico' => array(
					$t => __("Rings Cover Size", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.cico",
					$k => array('tpack', 'addons', 'transition pack', 'rings', 'rings cover', 'rings size'),
					$d => __("Changes rings effect size to cover. This is helpful if origin is set away from center, this option will create bigger rings that cover entire image", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.cico']"
					)
				),
				'ciad' => array(
					$t => __("Rings Spin Direction", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.ciad",
					$k => array('tpack', 'addons', 'transition pack', 'rings', 'rings direction'),
					$d => __("Spins rings in alternate directions", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.ciad']"
					)
				),
				'cimw' => array(
					$t => __("Rings Mix Staggered", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.cimw",
					$k => array('tpack', 'addons', 'transition pack', 'rings', 'rings mix', 'rings staggered mix'),
					$d => __("Cross fades images along with individual ring animation", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.cimw']"
					)
				),
				'pr' => array(
					$t => __("Perspective", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.pr",
					$k => array('tpack', 'addons', 'transition pack', 'transition perspective'),
					$d => __("Sets perspective angle from center", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.pr']"
					)
				),
				'prange' => array(
					$t => __("Mix Range", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.prange",
					$k => array('tpack', 'addons', 'transition pack', 'transition mix'),
					$d => __("Controls when images cross fade during transition. For example, with value set to 100%, images will fade along with transition. If value is set to 10% then fade will start at 40% of transition and complete at 60%.", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.prange']"
					)
				),
				'tlt' => array(
					$t => __("Tilt", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.tlt",
					$k => array('tpack', 'addons', 'transition pack', 'transition tilt'),
					$d => __("Sets tilt amount during transition", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.tlt']"
					)
				),
				'sko' => array(
					$t => __("Skew Origin", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.sko",
					$k => array('tpack', 'addons', 'transition pack', 'skew origin'),
					$d => __("Sets origin for skew in percent, image starts skewing around this point", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.sko']"
					)
				),
				'shv' => array(
					$t => __("Shake Amount", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.shv",
					$k => array('tpack', 'addons', 'transition pack', 'shake amount'),
					$d => __("Controls the shake effect intensity", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.shv']"
					)
				),
				'shx' => array(
					$t => __("Shake X", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.shx",
					$k => array('tpack', 'addons', 'transition pack', 'shake x'),
					$d => __("Sets x amount for shake effect", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.shx']"
					)
				),
				'shy' => array(
					$t => __("Shake Y", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.shy",
					$k => array('tpack', 'addons', 'transition pack', 'shake y'),
					$d => __("Sets y amount for shake effect", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.shy']"
					)
				),
				'shz' => array(
					$t => __("Sheke Zoom", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.shz",
					$k => array('tpack', 'addons', 'transition pack', 'shake rotation'),
					$d => __("Sets zoom amount for shake effect", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.shz']"
					)
				),
				'shr' => array(
					$t => __("Shake Rotation", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.shr",
					$k => array('tpack', 'addons', 'transition pack', 'shake rotation'),
					$d => __("Sets rotation amount for shake effect", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.shr']"
					)
				),
				'chm1' => array(
					$t => __("Chaos Mix Value", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.chm1",
					$k => array('tpack', 'addons', 'transition pack', 'transition chaos', 'chaos mix'),
					$d => __("Chaos effect takes two types of values internally for multiplication, mixing them creates different effect.", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.chm1']"
					)
				),
				'chm2' => array(
					$t => __("Chaos Mix Value", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.chm2",
					$k => array('tpack', 'addons', 'transition pack', 'transition chaos', 'chaos mix'),
					$d => __("Chaos effect takes two types of values internally for multiplication, mixing them creates different effect.", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.chm2']"
					)
				),
				'chm3' => array(
					$t => __("Chaos Mix Value", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.chm3",
					$k => array('tpack', 'addons', 'transition pack', 'transition chaos', 'chaos mix'),
					$d => __("Chaos effect takes two types of values internally for multiplication, mixing them creates different effect.", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.chm3']"
					)
				),
				'chm4' => array(
					$t => __("Chaos Mix Value", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.chm4",
					$k => array('tpack', 'addons', 'transition pack', 'transition chaos', 'chaos mix'),
					$d => __("Chaos effect takes two types of values internally for multiplication, mixing them creates different effect.", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.chm4']"
					)
				),
				'zi' => array(
					$t => __("Zoom: In", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.zi",
					$k => array('tpack', 'addons', 'transition pack', 'transition zoom', 'zoom in'),
					$d => __("Sets zoom value for incoming image, negative value will zoom out and positive value zooms in", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.zi']"
					)
				),
				'zo' => array(
					$t => __("Zoom: Out", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.zi",
					$k => array('tpack', 'addons', 'transition pack', 'transition zoom', 'zoom out'),
					$d => __("Sets zoom value for outgoing image, negative value will zoom out and positive value zooms in", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.zo']"
					)
				),
				'zb' => array(
					$t => __("Blur Intensity", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.zb",
					$k => array('tpack', 'addons', 'transition pack', 'transition zoom', 'blur intensity'),
					$d => __("Sets blur intensity for effect", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.zb']"
					)
				),
				'zwo' => array(
					$t => __("Warp: Out", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.zwo",
					$k => array('tpack', 'addons', 'transition pack', 'transition zoom', 'zoom warp'),
					$d => __("Sets warp intensity for outgoing image", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.zwo']"
					)
				),
				'zwi' => array(
					$t => __("Warp: In", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.zwi",
					$k => array('tpack', 'addons', 'transition pack', 'transition zoom', 'zoom warp'),
					$d => __("Sets warp intensity for incoming image", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.zwi']"
					)
				),
				'ref' => array(
					$t => __("Cube Reflection", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.ref",
					$k => array('tpack', 'addons', 'transition pack', 'cube reflection'),
					$d => __("Sets cube reflection opacity", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.ref']"
					)
				),
				'flo' => array(
					$t => __("Cube Floating", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.flo",
					$k => array('tpack', 'addons', 'transition pack', 'cube floating'),
					$d => __("Sets distance for floating effect between cube and reflection", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.flo']"
					)
				),
				'gz' => array(
					$t => __("Cube Zoom", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.gz",
					$k => array('tpack', 'addons', 'transition pack', 'cube zoom'),
					$d => __("Sets zoom value for cube", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.gz']"
					)
				),
				'ie' => array(
					$t => __("Transition Ease", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.ie",
					$k => array('tpack', 'addons', 'transition pack', 'transition ease'),
					$d => __("Sets transition ease effect", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.ie']"
					)
				),
				'pp' => array(
					$t => __("Post Processing Effect", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.pp",
					$k => array('tpack', 'addons', 'transition pack', 'transition post processing'),
					$d => __("Sets transition ease effect", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.pp']"
					)
				),
				'ppbt' => array(
					$t => __("Post Processing Effect", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.ppbt",
					$k => array('tpack', 'addons', 'transition pack', 'transition blur type'),
					$d => __("Sets type of blur effect, blur intensity is tied to effect intensity", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.ppbt']"
					)
				),
				
				'ppga' => array(
					$t => __("Post Processing Glitch Effect", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.ppga",
					$k => array('tpack', 'addons', 'transition pack','transition glitch effect', 'noise'),
					$d => __("Defines the replication, size and strength of Noise", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.ppga']"
					)
				),
				'ppgr' => array(
					$t => __("Post Processing Glitch Effect", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.ppgr",
					$k => array('tpack', 'addons', 'transition pack', 'transition glitch effect', 'repeat'),
					$d => __("The minimun random time delay between two Noise wave processing", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.ppgr']"
					)
				),
				'ppgs' => array(
					$t => __("Post Processing Glitch Effect", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.ppgs",
					$k => array('tpack', 'addons', 'transition pack','transition glitch effect', 'masking'),
					$d => __("Disortion size and dynamic of offseted masks during glitch", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.ppgs']"
					)
				),
				'ppgl' => array(
					$t => __("Post Processing Glitch Effect", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.ppgl",
					$k => array('tpack', 'addons', 'transition pack','transition glitch effect', 'glitch length'),
					$d => __("The Length of one Glitch wave before it stops and waits for next Random start", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.ppgl']"
					)
				),
				// TODO Krisztian add info for glitch END
				'ppfn' => array(
					$t => __("Film Effect Opacity", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.ppfn",
					$k => array('tpack', 'addons', 'transition pack', 'film opacity'),
					$d => __("Sets opacity for film effect", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.ppfn']"
					)
				),
				'ppfs' => array(
					$t => __("Film Effect Intensity", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.ppfs",
					$k => array('tpack', 'addons', 'transition pack', 'film intensity'),
					$d => __("Controls intensity of film effect lines", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.ppfs']"
					)
				),
				'ppfh' => array(
					$t => __("Film Effect Intensity", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.ppfh",
					$k => array('tpack', 'addons', 'transition pack', 'film grain'),
					$d => __("Controls grain amount for film effect", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.ppfh']"
					)
				),
				'ppfbw' => array(
					$t => __("Film Effect Intensity", 'revsliderhelp'),
					$h => "slideChange.addOns.tpack.ppfbw",
					$k => array('tpack', 'addons', 'transition pack', 'film color'),
					$d => __("Controls color of film effect", 'revslider-transitionpack-addon'),
					$a => $u . 'advanced-transition-addon/', 
					$hl => array(
						$m => "#module_slide_trigger, #gst_slide_2",
						$st => '#form_slidebg_transition',
						$f =>  "*[data-r='slideChange.addOns.tpack.ppfbw']"
					)
				),
			)

		);
		
	}

}
	
?>