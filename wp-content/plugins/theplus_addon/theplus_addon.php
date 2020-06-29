<?php
/*
Plugin Name: The Plus Ultimate Visual Composer Addons
Plugin URI: http://theplus.sagar-patel.com/
Description: Collection of most beautiful and modern Visual composer addons made by POSIMYTH Themes.
Version: 3.0.0
Author: Posimyth Themes
Author URI: http://posimyththemes.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('THEPLUS_PLUGIN_URL',plugins_url().'/theplus_addon/');
define('THEPLUS_PLUGIN_PATH',plugin_dir_path(__FILE__));
 defined( 'VERSION_THEPLUS' ) or define( 'VERSION_THEPLUS', '3.0.0' );
 
 
class ThePlus_addon {
	/**
	 * Core singleton class
	 * @var self - pattern realization
	 */
	private static $_instance;
	
	/**
	 * Get the instane of ThePlus_addon
	 *
	 * @return self
	 */
	public static function getInstance() {
		if ( ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
	public function __construct() {
		
		if ( class_exists( 'Vc_Manager', false ) ) {
			add_filter('plugin_action_links',array($this,  'pluginActionLinks'), 10, 2);
			add_action('plugins_loaded', array($this, 'pluginsLoaded'), 10);
			add_action( 'admin_enqueue_scripts', array( $this,'pt_theplus_admin_css') );
			add_action( 'wp_enqueue_scripts', array( $this,'pt_theplus_js_css') );
			add_filter('upload_mimes', array( $this,'pt_theplus_mime_types'));
			add_action('after_setup_theme', array($this, 'addVcElementsAddon'));
			if( empty( get_option( 'theplus-notice-dismissed' ) ) ) {
				add_action( 'admin_notices',array($this, 'pluskey_verify_notify'));
			}
			
		}else{
			add_action('admin_notices', array($this, '_admin_notice__error'));
		}
			
		$general_options=get_option( 'general_options' );
		if(isset($general_options['templates_on_off']) && !empty($general_options['templates_on_off'])){
			$templates=$general_options['templates_on_off'];
		}else{
			$templates='enable';
		}
		if(!empty($templates) && $templates=='enable'){
			if (class_exists('WPBakeryVisualComposerAbstract')) {
				$this->vc_pt_plus_templates_content();
			}
			}
		if(is_admin()){
			add_action( 'admin_print_scripts-post.php', array( &$this, 'pt_admin_vc_enqueue_scripts' ),100 );
			add_action( 'admin_print_scripts-post-new.php', array( &$this, 'pt_admin_vc_enqueue_scripts' ),100 );
		}
	}
	
	/**
	 * Cloning disabled
	 */
	public function __clone() {
	}

	/**
		* Serialization disabled
	 */
	public function __sleep() {
	}

	/**
	 * De-serialization disabled
	 */
	public function __wakeup() {
	}
	
	function pluginsLoaded() {
		load_plugin_textdomain( 'pt_theplus', false, basename( dirname( __FILE__ ) ) . '/lang' ); 
	}
	
	public function addVcElementsAddon() {
		if ( class_exists( 'Vc_Manager', false ) ) {
			require_once(THEPLUS_PLUGIN_PATH.'vc_elements/vc_addon.php');			
		}
		require_once THEPLUS_PLUGIN_PATH.'vc_elements/import/theplus-import.php';
		require_once THEPLUS_PLUGIN_PATH.'post-type/tinymce/theme-shortcode.php';
		if ( ! class_exists( 'cmb_Meta_Box' ) ){
			require_once(THEPLUS_PLUGIN_PATH.'vc_elements/theplus_options.php');
		}
		if ( file_exists(THEPLUS_PLUGIN_PATH.'post-type/metabox/init.php' ) ) {
			require_once THEPLUS_PLUGIN_PATH . 'post-type/includes.php';
			require_once THEPLUS_PLUGIN_PATH . 'post-type/plugin-functions.php';
		}
	}
	public function vc_pt_plus_templates_content() {
	if (class_exists('WPBakeryVisualComposerAbstract')) {
			require_once (THEPLUS_PLUGIN_PATH.'vc_elements/vc_param/theme-vc-templates-panel-editor.php');
			require_once (THEPLUS_PLUGIN_PATH.'vc_elements/vc_param/theme-vc-template.php');			
		}
		$verify_api=pt_plus_check_api();
		if(!empty($verify_api) && $verify_api=='1'){
			$pt_plus_templates = new Pt_plus_Vc_Templates_Editor();
			return $pt_plus_templates->init();
		}
	}
	
	public function pt_admin_vc_enqueue_scripts(){
	
	$general_options=get_option( 'general_options' );
		wp_enqueue_script( 'pt-theplus-vc-admin', THEPLUS_PLUGIN_URL. 'vc_elements/js/admin/pt-theplus-vc-admin.js' );
		if(isset($general_options['vc_clipboard_on_off']) && !empty($general_options['vc_clipboard_on_off'])){
			$vc_clipboard=$general_options['vc_clipboard_on_off'];
		}else{
			$vc_clipboard='enable';
		}
		if(!empty($vc_clipboard) && $vc_clipboard=='enable'){
			wp_enqueue_script( 'pt_plus_vc_clipboard', THEPLUS_PLUGIN_URL. 'vc_elements/js/admin/pt-theplus-vc-clipboard.js' );
		}
		if(isset($general_options['vc_view_shortcode_on_off']) && !empty($general_options['vc_view_shortcode_on_off'])){
			$vc_view_shortcode=$general_options['vc_view_shortcode_on_off'];
		}else{
			$vc_view_shortcode='enable';
		}
		if(!empty($vc_view_shortcode) && $vc_view_shortcode=='enable'){
			wp_enqueue_style('pt_theplus-view-shortcode',THEPLUS_PLUGIN_URL.'vc_elements/css/admin/pt-view-shortcode.css',null,VERSION_THEPLUS);
			wp_enqueue_script('pt_theplus-view-shortcode',THEPLUS_PLUGIN_URL.'vc_elements/js/admin/pt-view-shortcode.js',array('jquery'),VERSION_THEPLUS,true);
		}
	}
	/**
	 * Add Settings link in plugin's page
	 * @since 3.0.0
	 *
	 * @param $links
	 * @param $file
	 *
	 * @return array
	 */
	 public function pluginActionLinks( $links, $file ) {
	if ( plugin_basename( THEPLUS_PLUGIN_PATH.'theplus_addon.php' )== $file ) {
		$html = esc_html__( 'Settings', 'pt_theplus' );
			$title = __( 'Settings ThePlus Options', 'pt_theplus' );
			$link = '<a href="admin.php?page=general_options" title="' . esc_attr( $title ) . '">'.esc_html('Settings','pt_theplus').'</a>';
       
			array_unshift( $links, $link ); 
	}
		return $links;
	}
	
	public function pt_theplus_js_css() {
	$theplus_options=get_option( 'general_options' );
	$get_name = wp_get_theme();
	if(!empty($theplus_options['theplus_google_map_api'])){
		$theplus_google_map_api=$theplus_options['theplus_google_map_api'];
	}else{
		$theplus_google_map_api='AIzaSyAVRU9TRlsqthh0Z3zpaDvzIXeQuctSat8';
	}
	if(!empty($theplus_options['compress_minify_css'])){
		$minify_css=$theplus_options['compress_minify_css'];
	}else{
		$minify_css='disable';
	}
	if(!empty($theplus_options['compress_minify_js'])){
		$minify_js=$theplus_options['compress_minify_js'];
	}else{
		$minify_js='disable';
	}
		if($minify_css=='enable'){
			wp_enqueue_style( 'pt_theplus-style-min',THEPLUS_PLUGIN_URL .'vc_elements/css/main/theplus_style_min.css');
		}else{
			wp_enqueue_style( 'pt_theplus-style',THEPLUS_PLUGIN_URL .'vc_elements/css/main/theplus_style.css');
		}
		if($get_name=='Salient'){
			wp_enqueue_style( 'slient_compatibility-style',THEPLUS_PLUGIN_URL .'vc_elements/css/compatibity/slient_compatibility.css');
		}
		if($get_name=='Avada'){
			wp_enqueue_style( 'avada_compatibility-style',THEPLUS_PLUGIN_URL .'vc_elements/css/compatibity/avada_compatibility.css');
		}
		if($get_name=='Bridge' || $get_name=='Enfold' || $get_name=='Jupiter' || $get_name=='The7'){
			wp_enqueue_style( 'theme_compatibility-style',THEPLUS_PLUGIN_URL .'/vc_elements/css/compatibity/theme_compatibility.css');
		}
		wp_enqueue_style( 'fontawasome-fonts',THEPLUS_PLUGIN_URL .'vc_elements/css/extra/font-awesome.min.css');
		wp_register_style( 'tooltipster', THEPLUS_PLUGIN_URL .'vc_elements/css/extra/tooltipster.bundle.min.css'); //tooltipster  css	
		wp_register_style( 'tooltipster_theme', THEPLUS_PLUGIN_URL .'vc_elements/css/extra/tooltipster-theme.css'); //tooltipster  css	
		wp_enqueue_style( 'lity_css', THEPLUS_PLUGIN_URL .'vc_elements/css/extra/lity.css'); //Lity css Pop-up
		
		wp_enqueue_style( 'slick_min_css', THEPLUS_PLUGIN_URL .'vc_elements/css/extra/slick.min.css', false, '3.0.0' );//slider css
		
		wp_enqueue_script("jquery-effects-core");
		
		$check_elements=pt_plus_get_option('general','check_elements');
		 $switch_api=pt_plus_get_option('general','gmap_api_switch');
		
		if(isset($check_elements) && !empty($check_elements) && in_array('tp_adv_gmap',$check_elements) && (empty($switch_api) || $switch_api=='enable')){
			wp_enqueue_script( 'gmaps-js','//maps.googleapis.com/maps/api/js?key='.$theplus_google_map_api.'&sensor=false', array('jquery'), null, false, true);
		}
		//wp_enqueue_script( 'waypoints-js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/jquery.waypoints.js');// waypoint js
		//wp_enqueue_script( 'velocity_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/velocity.min.js',array(),'', true);//all transistion animated effects
		//wp_enqueue_script( 'velocity_ui_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/velocity.ui.js',array(),'', true);//all transistion animated effects
		
		
		//wp_enqueue_script( 'modernizr_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/modernizr.min.js');
		//wp_enqueue_script( 'imagesloaded_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/imagesloaded.pkgd.min.js'); // image segmentation
		//wp_enqueue_script( 'hover3d_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/jquery.hover3d.min.js');
		
		
				
		wp_register_script( 'easepack_js', THEPLUS_PLUGIN_URL .'/vc_elements/js/extra/easepack.min.js'); //EasePack.min.js canvas style 3	
		wp_register_script( 'raf_js',THEPLUS_PLUGIN_URL .'vc_elements/js/extra/rAF.js',array(),'', true);//all canvas 3
		wp_register_script( 'particles_min_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/particles.min.js',array(),'', true);//all canvas 3
		wp_register_script( 'projector_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/stats.min.js',array(),'', true);//all canvas 3
		//wp_enqueue_script( 'chaffle_min_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/jquery.chaffle.min.js',array(),'', false);
		
		if($minify_js=='enable'){
			wp_enqueue_script( 'pt-theplus-row-script-min-js', THEPLUS_PLUGIN_URL .'vc_elements/js/main/pt-theplus-row-script.min.js',array('jquery'),false,false ); // background row js element
		}else{
			wp_enqueue_script( 'pt-theplus-row-script-js', THEPLUS_PLUGIN_URL .'vc_elements/js/main/pt-theplus-row-script.js',array('jquery'),false,false ); // background row js element
		}
		wp_register_script( 'tooltipster_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/tooltipster.bundle.js'); //tooltipster  js 
		//wp_enqueue_script( 'lity_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/lity.min.js'); //Lity  js Pop-up
		//wp_enqueue_script( 'slick_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/slick.min.js'); //slick js caroseal
		//wp_enqueue_script( 'hoverdir_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/jquery.hoverdir.js'); //hoverdir js 
		//wp_enqueue_script( 'vegas_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/vegas.js',array(),'', false);
		//wp_enqueue_script( 'isotope_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/isotope.pkgd.js',array( 'isotope' ), VERSION_THEPLUS, true );
		//wp_enqueue_script( 'packery_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/packery-mode.pkgd.min.js',array( 'isotope' ), VERSION_THEPLUS, true );
		
		
		//wp_enqueue_script( 'vivus_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/vivus.min.js');//svg draw js 
		
		//wp_enqueue_script( 'timelinemax_min', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/tweenmax/timelinemax.min.js');//timelinemax js  sidebar hubuger menu
		//wp_enqueue_script( 'tweenmax_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/tweenmax/tweenmax.min.js');//tweenmax js 
		//wp_enqueue_script( 'jquery_parallax_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/tweenmax/jquery-parallax.js');//parallax js 
		
		//wp_enqueue_script( 'ScrollMagic-js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/scrollmagic.min.js',array(),'', false ); //scroll page 
		//wp_enqueue_script( 'animation-gsap-js', THEPLUS_PLUGIN_URL . 'vc_elements/js/extra/animation.gsap.min.js',array(),'', false ); //scrollmagic part page
		wp_enqueue_script( 'pt-plus-app', THEPLUS_PLUGIN_URL . 'vc_elements/js/extra/app.min.js',array(),'', false );
		echo '<script> var theplus_ajax_url = "'.admin_url('admin-ajax.php').'";</script>';
		wp_localize_script('theplus_custom_js', 'ajax_var', array(
			'url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax-nonce')
		));
		if($minify_js=='enable'){
			wp_enqueue_script( 'pt-theplus-custom-min', THEPLUS_PLUGIN_URL .'vc_elements/js/main/pt-theplus-custom.min.js',array('jquery'),VERSION_THEPLUS, false);
		}else{
			wp_enqueue_script( 'pt-theplus-custom', THEPLUS_PLUGIN_URL .'vc_elements/js/main/pt-theplus-custom.js',array('jquery'),VERSION_THEPLUS, false);
		}
		
	}


	function pt_theplus_admin_css() {   
		wp_enqueue_style( 'pt-theplus-admin', THEPLUS_PLUGIN_URL .'vc_elements/css/admin/pt-theplus-admin.css', array() );
		wp_enqueue_script( 'pt-theplus-admin-notice', THEPLUS_PLUGIN_URL . 'vc_elements/js/admin/pt-theplus-admin-notice.js', array( 'jquery' ), '1.0', true  );
		wp_enqueue_script( 'pt-theplus-vc-script',   THEPLUS_PLUGIN_URL . 'vc_elements/vc_param/vc-scripts.js' , array('jquery'), '3.0.0', true );
	}
	function pt_theplus_mime_types($mimes) {
		$mimes['svg'] = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
	  return $mimes;
	}
	
	/*
	 * Admin notice text
	 */
	public function pluskey_verify_notify(){
		echo '<div class="plus-key-notify notice notice-info is-dismissible">';
			echo '<h3>ThePlus Verify Key</h3>';
			echo '<p>'. esc_html__( 'Thanks for purchasing ThePlus Addons for WPBakery Page Builder (formerly Visual Composer). Please Verify from settings to get access of ', 'pt_theplus' ) .' ';
			echo '<b><i>Plus Blocks</i></b> and <b><i>Plus Templates</i></b>.';
			echo '</p>';
		echo '</div>';
	}
	
	public function _admin_notice__error() {
		echo '<div class="notice notice-error is-dismissible">';
			echo '<p>'. esc_html__( ' The Plus Ultimate addon is enabled but not effective. It requires Visual Composer Plugins.', 'pt_theplus' ) .'</p>';
		echo '</div>';
	}
}
$ThePlus_addon = new ThePlus_addon();