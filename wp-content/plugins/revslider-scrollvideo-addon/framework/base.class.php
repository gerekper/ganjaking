<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnScrollvideoBase {
	
	const MINIMUM_VERSION = '6.5.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnScrollvideoBase::MINIMUM_VERSION, '>=')) {
		
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
			$update_admin = new RevAddOnScrollvideoUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'));
			add_filter('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

			// require_once(static::$_PluginPath . 'admin/includes/slider.class.php');			
			
			// admin init
			// new RsScrollvideoSliderAdmin(static::$_PluginTitle, static::$_Version);			
			
		}

		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		require_once(static::$_PluginPath . 'public/includes/slide.class.php');
		
		new RsScrollvideoSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsScrollvideoSlideFront(static::$_PluginTitle);
		
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
			$_jsPathMin = file_exists(static::$_PluginPath . 'admin/assets/js/revslider-' . static::$_PluginTitle . '-addon-admin.dev.js') ? '.dev' : '';
									
			wp_enqueue_style($_handle, $_base . 'css/revslider-' . static::$_PluginTitle . '-addon-admin.css', array(), static::$_Version);
			wp_enqueue_script($_handle, $_base . 'js/revslider-' . static::$_PluginTitle . '-addon-admin' . $_jsPathMin . '.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			wp_localize_script( $_handle, 'revslider_scrollvideo_addon', self::get_var() );
		}
		
	}

	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public static function get_var() {
		$_textdomain = 'revslider-scrollvideo-addon';
		return array(
			'bricks' => array(
				'general' => __('Scroll Video Settings',$_textdomain),
				'active' => __('Use Scroll Video Playback',$_textdomain),
				'animateto' => __('Play Scroll Video until',$_textdomain),
				'mpeg' => __('Video Source',$_textdomain),
				'mpegsrc' => __('Enter MPEG Source',$_textdomain),
				'medialib' => __('Media Library',$_textdomain),
				'objlib' => __('Object Library',$_textdomain),
				'aratio' => __('Aspect Ratio',$_textdomain),
				'fmode' => __('Force Cover Mode',$_textdomain),
				'globalstart' => __('Scroll Video from',$_textdomain),
				'globalend' => __('Scroll Video until',$_textdomain),
				'slideuntil' => __('Video on Slide until',$_textdomain),
				'scrollvideo' => __('Scroll Video',$_textdomain),
				'scrollvideoart' => __('Scrollvideo Type',$_textdomain),
				'autoplay' => __('Auto Play',$_textdomain),
				'approx' => __('Approx',$_textdomain),
				'tobegenerate' => __('to be generated',$_textdomain),
				'fps' => __('Frames/Sec',$_textdomain),
				'quality' => __('Quality',$_textdomain),
				'bestq' => __('Best Quality (Large Files)',$_textdomain),
				'optq' => __('Suggested',$_textdomain),
				'minq' => __('Bad Quality (Small Files)',$_textdomain),
				'slidebased' => __('Slide Based',$_textdomain),
				'udpattoscrollbase' => __('Update Module',$_textdomain),
				'anchorbased' => __('Pause at Anchors',$_textdomain),
				'activeglobalplyback' => __('Scroll Video is Active',$_textdomain),
				'generateframes' => __('Generate Frames',$_textdomain),
				'frameseq' => __('Generated Frame Sequence',$_textdomain),
				'noavailableframes' => __('No frames generated yet',$_textdomain),
				'clearallframes' => __('Delete Frames, get new Video',$_textdomain),
				'clearallframestitle' => __('Delete Generated Frames',$_textdomain),
				'clearallframesmain' => __('Are you sure to remove the existing Frames ?',$_textdomain),
				'clearallframessub' => __('This will delete all frames from the library and clear the folder. The process can not be undone.',$_textdomain),
				'staytuned' => __('Video will be Generated. Please stay tuned...',$_textdomain),
				'hasbeengenerated' => __('Scroll Based Video Frames has been generated',$_textdomain),
				'hasbeennotgenerated' => __('Scroll Based Video Frames has been NOT generated',$_textdomain),
				'preparingvideo' => __('Loading Full Video...',$_textdomain),				
				'yesclearit' => __('Delete Frames',$_textdomain),
				'startframe' => __('Start Time',$_textdomain),
				'endframe' => __('End Time',$_textdomain),
				'savingframes' => __('Saving Frames',$_textdomain),
				'renderingtime' => __('Generation duration ≈',$_textdomain),
				'nompegavailable' => __('No Video Selected. Please select a Video first',$_textdomain),
				'buildingframes' => __('Extracting Frames',$_textdomain),
				'resolution' => __('Target Size',$_textdomain),
				'keepspinner' => __('Keep Spinner during Load',$_textdomain),
				'blockscroll' => __('Block Scroll during Load',$_textdomain),
				'winoffset' => __('Start Offset',$_textdomain),
				'winoffsetend' => __('End Offset',$_textdomain),
				'nospinnerset' => __('Please enable Spinner in Global Settings',$_textdomain),
				'scrollbased' => __('Scroll Based',$_textdomain)								
			)
		);
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-scrollvideo-addon') {
		
		if($slug === 'revslider-scrollvideo-addon'){
			
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
			$definitions['editor_settings']['slider_settings']['addons']['scrollvideo_addon'] = $help['slider'];
		}
		
		if(isset($definitions['editor_settings']['slide_settings']) && isset($definitions['editor_settings']['slide_settings']['addons'])) {
			$help = self::get_definitions();
			$definitions['editor_settings']['slide_settings']['addons']['scrollvideo_addon'] = $help['slide'];
		}
						
		return $definitions;
	
	}
	
	/**
	 * Returns the addon help definitions
	 *
	 * @since    2.0.0
	 */
	private static function get_definitions() {		
		$_textdomain = 'revslider-scrollvideo-addon';
		return array(		
			'slider' => array(										

					'start_at' => array(							
						'buttonTitle' => __('Video Scroll extraction starts at', $_textdomain), 
						'title' => __('Scroll Video Extraction Start', $_textdomain),
						'helpPath' => 'addOns.revslider-scrollvideo-addon.startAtReal', 
						'keywords' => array('addon', 'addons', 'scrollvideo', 'scrollvideo addon',  'scroll video extraction', 'scroll video start'), 
						'description' => __('Defines the start time position of the Video before Extracting frames', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'https://www.sliderrevolution.com/documentation/scrollvideo-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Scroll Video',
						'highlight' => array(							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-scrollvideo-addon", 
							'scrollTo' => '#form_module_revslider-scrollvideo-addon', 
							'focus' => "#sr_bg_mpeg_scroll_startAt"							
						)						
					),

					'end_at' => array(							
						'buttonTitle' => __('Video Scroll extraction ends at', $_textdomain), 
						'title' => __('Scroll Video Extraction End', $_textdomain),
						'helpPath' => 'addOns.revslider-scrollvideo-addon.endAtReal', 
						'keywords' => array('addon', 'addons', 'scrollvideo', 'scrollvideo addon',  'scroll video extraction', 'scroll video end'), 
						'description' => __('Defines the end time position of the Video before Extracting frames', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'https://www.sliderrevolution.com/documentation/scrollvideo-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Scroll Video',
						'highlight' => array(							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-scrollvideo-addon", 
							'scrollTo' => '#form_module_revslider-scrollvideo-addon', 
							'focus' => "#sr_bg_mpeg_scroll_startAt"							
						)						
					),

					'fps' => array(							
						'buttonTitle' => __('Frame per Sec', $_textdomain), 
						'title' => __('Extracted Frames per Sec', $_textdomain),
						'helpPath' => 'addOns.revslider-scrollvideo-addon.sequence.fps', 				
						'keywords' => array('addon', 'addons', 'scrollvideo', 'scrollvideo addon',  'scroll video extraction', 'frame per sec', 'fps'), 
						'description' => __('Defines the amount of frames per sec to extract as images from the selected video', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'https://www.sliderrevolution.com/documentation/scrollvideo-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Scroll Video',
						'highlight' => array(							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-scrollvideo-addon", 
							'scrollTo' => '#form_module_revslider-scrollvideo-addon', 
							'focus' => "#pb_fps_gen"							
						)						
					),

					'quality' => array(							
						'buttonTitle' => __('Extraction Quality', $_textdomain), 
						'title' => __('Extracted Frames Quality', $_textdomain),
						'helpPath' => 'addOns.revslider-scrollvideo-addon.sequence.quality', 				
						'keywords' => array('addon', 'addons', 'scrollvideo', 'scrollvideo addon',  'scroll video extraction', 'frame quality', 'extraction quality', 'quality'), 
						'description' => __('Defines the quality of extracted single frames. Suggested quality is 0.5 which creates a well detailed great compressed jpg', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'https://www.sliderrevolution.com/documentation/scrollvideo-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Scroll Video',
						'highlight' => array(							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-scrollvideo-addon", 
							'scrollTo' => '#form_module_revslider-scrollvideo-addon', 
							'focus' => "#pb_quality_gen"							
						)						
					),

					'resolution' => array(							
						'buttonTitle' => __('Extraction Resolution', $_textdomain), 
						'title' => __('Extracted Frame Resolution', $_textdomain),
						'helpPath' => 'addOns.revslider-scrollvideo-addon.res', 				
						'keywords' => array('addon', 'addons', 'scrollvideo', 'scrollvideo addon',  'scroll video extraction', 'frame resolution', 'extraction resolution', 'resolution'), 
						'description' => __('Defines the resolution of extracted single frames. Suggested resolution is always equal or smaller than the original video resolution.  i.e. 720px', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'https://www.sliderrevolution.com/documentation/scrollvideo-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Scroll Video',
						'highlight' => array(							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-scrollvideo-addon", 
							'scrollTo' => '#form_module_revslider-scrollvideo-addon', 
							'focus' => "#pb_quality_resol"							
						)						
					),

					'keepspinner' => array(							
						'buttonTitle' => __('Keep Spinner until all Frames loaded', $_textdomain), 
						'title' => __('Keep Spinner during Load', $_textdomain),
						'helpPath' => 'addOns.revslider-scrollvideo-addon.keepspinner', 				
						'keywords' => array('addon', 'addons', 'scrollvideo', 'scrollvideo addon',  'scroll video spinner', 'frontend loading', 'load spinner', 'spinner'), 
						'description' => __('Use the General Spinner (it must be ser before) to show a Loading spinner until all frames loaded, which gives a better user experiences when to many frames need to be preloaded', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'https://www.sliderrevolution.com/documentation/scrollvideo-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Scroll Video',
						'highlight' => array(							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-scrollvideo-addon", 
							'scrollTo' => '#form_module_revslider-scrollvideo-addon', 
							'focus' => "#pb_keep_spinner"							
						)						
					),

					'blockscroll' => array(							
						'buttonTitle' => __('Block Page Scrolling', $_textdomain), 
						'title' => __('Block Page Scrolling', $_textdomain),
						'helpPath' => 'addOns.revslider-scrollvideo-addon.blockscroll', 				
						'keywords' => array('addon', 'addons', 'scrollvideo', 'scrollvideo addon',  'scroll video block', 'frontend loading', 'load block', 'page scroll'), 
						'description' => __('Block Page Scrolling until all frames been loaded to keep visitors at the Module without missing and effects during scrolling through the page', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'https://www.sliderrevolution.com/documentation/scrollvideo-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Scroll Video',
						'highlight' => array(							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-scrollvideo-addon", 
							'scrollTo' => '#form_module_revslider-scrollvideo-addon', 
							'focus' => "#pb_keep_scroll"							
						)						
					),

					'generateframes' => array(							
						'buttonTitle' => __('Generate Video Scroll Frames', $_textdomain), 
						'title' => __('Generate Video Scroll Frames', $_textdomain),
						'helpPath' => 'videoscroll_generate_frame', 				
						'keywords' => array('addon', 'addons', 'scrollvideo', 'scrollvideo addon',  'generate frames', 'frontend preparation', 'extracting frames', 'extraction'), 
						'description' => __('Start generating the frames based on the selected video, frame per sec, quality and resolution.  This process can be cancelled if it takes too long. To get the best and quickest results, try to reduce fps, set video length shorter, reduce quality and resolution.', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'https://www.sliderrevolution.com/documentation/scrollvideo-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Scroll Video',
						'highlight' => array(							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-scrollvideo-addon", 
							'scrollTo' => '#form_module_revslider-scrollvideo-addon', 
							'focus' => "#pb_gen_frames"							
						)						
					),

					'clearframes' => array(							
						'buttonTitle' => __('Clear Video Scroll Frames', $_textdomain), 
						'title' => __('Clear Video Scroll Frames', $_textdomain),
						'helpPath' => 'videoscroll_pb_clear_frames', 				
						'keywords' => array('addon', 'addons', 'scrollvideo', 'scrollvideo addon',  'generate frames', 'frontend preparation', 'extracting frames', 'extraction'), 
						'description' => __('Clear all generated Frames. It can not be undone', $_textdomain), 
						'helpStyle' => 'normal', 
						'article' => 'https://www.sliderrevolution.com/documentation/scrollvideo-addon/', 
						'video' => false,
						'section' => 'Slider Settings -> Scroll Video',
						'highlight' => array(							
							'menu' => "#module_settings_trigger, #gst_sl_revslider-scrollvideo-addon", 
							'scrollTo' => '#form_module_revslider-scrollvideo-addon', 
							'focus' => "#pb_clear_frames"							
						)						
					)

						

										
												
			)							
		);
		
	}
	
}
	
?>