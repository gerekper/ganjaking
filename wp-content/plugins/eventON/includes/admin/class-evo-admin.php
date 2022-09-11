<?php
/**
 * all wp-admin functions for admin side of eventon
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon/Admin
 * @version     2.4.9
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** evo_admin Class */
class evo_admin {

	private $class_name;
	/** Constructor */
	public function __construct() {

		$this->opt = evo_get_options('1');

		add_action('admin_menu', array($this,'eventon_admin_menu'), 5);
		add_action('admin_init', array($this,'eventon_admin_init'));
		
		add_action('admin_action_duplicate_event', array($this,'eventon_duplicate_event_action'));
		add_filter("plugin_action_links_".AJDE_EVCAL_BASENAME, array($this,'eventon_plugin_links') );

		add_action('media_buttons',  array($this,'eventon_shortcode_button'));
		add_filter( 'tiny_mce_version', array($this,'eventon_refresh_mce') ); 

		add_filter('display_post_states', array($this,'post_state'),10,2);

		$tt = strtotime( 'first day of 2 months ago');
		//echo date('y-m-d', $tt);

		//add_action( 'admin_enqueue_scripts', array($this,'eventon_admin_scripts') );
		//add_action( 'admin_enqueue_scripts', array($this,'eventon_all_backend_files') );

		// eventon elements
		EVO()->elements->register_styles_scripts();
	}

	function post_state( $states, $post){
		//print_r($post);
		if (  'page' == get_post_type( $post->ID ) &&  $post->post_name == 'event-directory'){
	        $states[] = __('Events Page'); 
	    } 

	    return $states;
	}

// admin init
	function eventon_admin_init() {				
		// Includes
			require_once(AJDE_EVCAL_PATH.'/includes/products/class-evo_plugins_api_data.php');

		global $pagenow, $typenow, $wpdb, $post;	

		$postType = !empty($_GET['post_type'])? sanitize_text_field($_GET['post_type']): false;
	   
	    if(!$postType && !empty($_GET['post']))  $postType = get_post_type( sanitize_text_field($_GET['post']) );
			
		// EVENT POSTS
		if ( $postType && $postType == "ajde_events" ) {	

			EVO()->elements->register_colorpicker();
			EVO()->elements->load_colorpicker();

			// Event Post Only
			$print_css_on = array( 'post-new.php', 'post.php' );

			foreach ( $print_css_on as $page ){
				add_action( 'admin_print_styles-'. $page, array($this,'eventon_admin_post_css') );
				add_action( 'admin_print_scripts-'. $page, array($this,'eventon_admin_post_script') );			
			}
						
			// taxonomy only page
			if($pagenow =='edit-tags.php' || $pagenow == 'term.php'){
				EVO()->elements->load_colorpicker();
				wp_enqueue_script('taxonomy',AJDE_EVCAL_URL.'/assets/js/admin/taxonomy.js' ,array('jquery'),'1.0', true);
			}
		}else{
			$this->eventon_shortcode_button_init();
		}
	

		// event edit page content
			include_once(  AJDE_EVCAL_PATH.'/includes/admin/post_types/class-meta_boxes.php' );
			$this->metaboxes = new evo_event_metaboxes();

		// Includes for admin
			if(defined('DOING_AJAX')){	include_once( 'class-admin-ajax.php' );		}			

		// evneton settings only 
			include_once(  AJDE_EVCAL_PATH.'/includes/admin/settings/class-settings.php' );
			$this->settings = new EVO_Settings();

			if($pagenow =='admin.php' && isset($_GET['page']) && ($_GET['page']=='eventon' || $_GET['page']=='action_user')
			){
				
				$this->settings->register_ss();
				$this->settings->load_styles_scripts();
			}

		// all eventon wp-admin pages
			$this->wp_admin_scripts_styles();
					
		// create necessary pages	
			$_eventon_create_pages = get_option('_eventon_create_pages'); // get saved status for creating pages
			if(empty($_eventon_create_pages) || $_eventon_create_pages!= 1){
				evo_install::create_pages();
			}

		// force update checking on wp-admin
			if($pagenow =='update-core.php' && isset($_REQUEST['force-check']) && $_REQUEST['force-check']==1){
				EVO_Prods()->get_remote_prods_data(true);
			}

		// RTL styles for wp-admin
			if( is_rtl() ){
				wp_enqueue_style( 'rtl_styles',AJDE_EVCAL_URL.'/assets/css/admin/wp_admin_rtl.css',array(), EVO()->version);
			}
			
		// when an addon is updated or installed - since 2.5
			add_action('evo_addon_version_change', array($this, 'update_addon_styles'), 10);

		// Deactivate single events addon
			deactivate_plugins('eventon-single-event/eventon-single-event.php');
			deactivate_plugins('eventon-search/eventon-search.php');
	}
	
// admin menus
	function eventon_admin_menu() {
	    global $menu, $pagenow;

	    if ( current_user_can( 'manage_eventon' ) ){
	    	$menu[] = array( '', 'read', 'separator-eventon', '', 'wp-menu-separator eventon' );
	    }
			
		$menu_icon = 'data:image/svg+xml;base64,'. base64_encode('<svg width="20" height="20" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path fill="black" d="'. $this->get_svg_evo().'"/></svg>');
		
		// Create admin menu page 
		$main_page = add_menu_page(
			__('EventON - Event Calendar','eventon'), 
			__('EventON','eventon'),
			'manage_eventon',
			'eventon',
			array($this,'eventon_settings_page'), 
			$menu_icon
		);

	    add_action( 'load-' . $main_page, array($this,'eventon_admin_help_tab') );	
		
		
		// add submenus to the eventon menu
		add_submenu_page('eventon', __('EventON - Event Calendar','eventon'), __('Settings','eventon'), 'manage_eventon', 'eventon' );
		add_submenu_page( 'eventon', 'Language', __('Language','eventon') , 'manage_eventon', 'admin.php?page=eventon&tab=evcal_2', '' );
		add_submenu_page( 'eventon', 'Styles', __('Styles','eventon') , 'manage_eventon', 'admin.php?page=eventon&tab=evcal_3', '' );
		add_submenu_page( 'eventon', 'Addons & Licenses', __('Addons & Licenses','eventon') , 'manage_eventon', 'admin.php?page=eventon&tab=evcal_4', '' );
		add_submenu_page( 'eventon', 'Support', __('Support','eventon') , 'manage_eventon', 'admin.php?page=eventon&tab=evcal_5', '' );
	}	


	/** Include and display the settings page. */
		function eventon_settings_page() {
			include_once(  AJDE_EVCAL_PATH.'/includes/admin/settings/class-settings.php' );
			
			include_once(  AJDE_EVCAL_PATH.'/includes/admin/settings/eventon-admin-settings.php' );
			include_once(  AJDE_EVCAL_PATH.'/includes/admin/settings/class-settings-appearance.php' );
			include_once(  AJDE_EVCAL_PATH.'/includes/admin/settings/class-settings-scripts.php' );

			$settings = new EVO_Settings();
			$settings->print_page();
		}

// evo icon SVG
	function get_svg_el(){
		ob_start();?>
		<svg id="evo_icon" viewBox="0 0 32 32">
		<path d="<?php echo $this->get_svg_evo();?>"></path>
		</svg>
		<?php
		return ob_get_clean();
	}
		function get_svg_evo(){
			return EVO()->elements->svg->get_icon_path('evo_icon');
		}

// admin styles and scripts
	// EVENTON Posts pages
		function eventon_admin_post_css() {
			global $wp_scripts;
			$protocol = is_ssl() ? 'https' : 'http';

			// JQ UI styles
			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.10.4';		
			
			wp_enqueue_style("jquery-ui-css", $protocol."://ajax.googleapis.com/ajax/libs/jqueryui/{$jquery_version}/themes/smoothness/jquery-ui.min.css");
			
			wp_enqueue_style( 'backend_evcal_post',AJDE_EVCAL_URL.'/assets/css/admin/backend_evcal_post.css', array(), EVO()->version );
			wp_enqueue_style( 'select2',AJDE_EVCAL_URL.'/assets/lib/select2/select2.css',array(), EVO()->version);

			
		}
		function eventon_admin_post_script() {
			global $pagenow, $typenow, $post, $ajde;	
			
			if ( $typenow == 'post' && ! empty( $_GET['post'] ) ) {
				$typenow = $post->post_type;
			} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
		        $post = get_post( sanitize_text_field($_GET['post']) );
		        $typenow = $post->post_type;
		    }
			
			if ( $typenow == '' || $typenow == "ajde_events" ) {

				// load color picker files
				EVO()->elements->load_colorpicker();

				$eventon_JQ_UI_tp = AJDE_EVCAL_URL.'/assets/lib/jqtimepicker/jquery.timepicker.css';
				wp_enqueue_style( 'eventon_JQ_UI_tp',$eventon_JQ_UI_tp);
			
				// other scripts 
				wp_register_script( 'evo_handlebars',EVO()->assets_path.'js/lib/handlebars.js',array('jquery'), EVO()->version, true);
				wp_enqueue_script('select2',AJDE_EVCAL_URL.'/assets/lib/select2/select2.min.js');
				wp_enqueue_script('evcal_backend_post_timepicker',AJDE_EVCAL_URL.'/assets/lib/jqtimepicker/jquery.timepicker.js');
				wp_enqueue_script('evcal_backend_post',AJDE_EVCAL_URL.'/assets/js/admin/event-post.js', array('jquery','jquery-form','jquery-ui-core','jquery-ui-datepicker'), EVO()->version, true );
				wp_enqueue_script("jquery-ui-core");
				
				wp_localize_script( 'evcal_backend_post', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));	
				
				// hook for plugins
				do_action('eventon_admin_post_script');
			}
		}

	// scripts and styles for wp-admin
		function wp_admin_scripts_styles(){
			global $pagenow, $wp_version;

			if($pagenow == 'term.php')	wp_enqueue_media();

			
			wp_enqueue_script('evo_wp_admin',AJDE_EVCAL_URL.'/assets/js/admin/wp_admin.js',array('jquery','jquery-form'), EVO()->version,true);
			wp_localize_script( 
				'evo_wp_admin', 
				'evo_admin_ajax_handle', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ), 
					'postnonce' => wp_create_nonce( 'eventon_admin_nonce' ),
					'select_from_list'=> esc_html__('Select from list', 'eventon'),
					'add_new_item'=> esc_html__('Add new item', 'eventon'),
					'edit_item'=> esc_html__('Edit item', 'eventon'),
				)
			);

			// EventON Settings Page
			if( (!empty($pagenow) && $pagenow=='admin.php')
			 && (isset($_GET['page']) && ($_GET['page']=='eventon'|| $_GET['page']=='action_user'|| $_GET['page']=='evo-sync') ) 
			){

				// only addons page
			 	if(!empty($_GET['tab']) && $_GET['tab']=='evcal_4'){
			 		wp_enqueue_script('evcal_addons',AJDE_EVCAL_URL.'/assets/js/admin/settings_addons_licenses.js',array('jquery'), EVO()->version,true);
			 	}
			 	
			 	// wp-admin script			 		
			 		wp_localize_script( 'evo_wp_admin', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));			 		

			 	// LOAD thickbox
					if(isset($_GET['tab']) && ( $_GET['tab']=='evcal_5' || $_GET['tab']=='evcal_4') ){
						wp_enqueue_script('thickbox');
						wp_enqueue_style('thickbox');
					}

				// LOAD custom google fonts for skins		
					$gfont="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400,300";
					wp_register_style( 'evcal_google_fonts', $gfont, '', '', 'screen' );
			}
			
			// ALL wp-admin
			wp_register_style('evo_font_icons',AJDE_EVCAL_URL.'/assets/fonts/all.css',array(), EVO()->version);
			wp_enqueue_style( 'evo_font_icons' );

			// wp-admin styles
			 	wp_enqueue_style( 'evo_wp_admin',AJDE_EVCAL_URL.'/assets/css/admin/wp_admin.css',array(), EVO()->version);
			 	wp_enqueue_style( 'evo_wp_admin_widgets',AJDE_EVCAL_URL.'/assets/css/admin/widgets.css',array(), EVO()->version);


			// styles for WP>=3.8
			if($wp_version>=3.8)
				wp_enqueue_style( 'newwp',AJDE_EVCAL_URL.'/assets/css/admin/wp3.8.css',array(), EVO()->version);
			// styles for WP<3.8
			if($wp_version<3.8)
				wp_enqueue_style( 'newwp',AJDE_EVCAL_URL.'/assets/css/admin/wp_old.css',array(), EVO()->version);

			// fonts
			wp_enqueue_style( 'evcal_google_fonts' );
			
			EVO()->elements->enqueue();

			do_action('evo_admin_all_wp_admin_scripts');

		}

// Dynamic Style Related
	/*	Dynamic styles generation */
		function generate_dynamic_styles_file($newdata='') {
		 
			/** Define some vars **/
			$data = $newdata; 
			$uploads = wp_upload_dir();
			
			//$css_dir = get_template_directory() . '/css/'; // Shorten code, save 1 call
			$css_dir = AJDE_EVCAL_DIR . '/'. EVENTON_BASE.  '/assets/css/'; 
			//$css_dir = plugin_dir_path( __FILE__ ).  '/assets/css/'; 
			
			//echo $css_dir;

			/** Save on different directory if on multisite **/
			if(is_multisite()) {
				$aq_uploads_dir = trailingslashit($uploads['basedir']);
			} else {
				$aq_uploads_dir = $css_dir;
			}
			
			/** Capture CSS output **/
			ob_start();
			require($css_dir . 'dynamic_styles.php');
			$css = ob_get_clean();

			//print_r($css);
			
			/** Write to options.css file **/
			WP_Filesystem();
			global $wp_filesystem;
			if ( ! $wp_filesystem->put_contents( $aq_uploads_dir . 'eventon_dynamic_styles.css', $css, 0777) ) {
			    return true;
			}	

			// also update concatenated addon styles
				$this->update_addon_styles();	
		}

	/**
	 * Update and save addon styles passed via pluggable function
	 * @since   2.5
	 */
		function update_addon_styles(){
			// check if enabled via eventon settings
			if( evo_settings_val('evcal_concat_styles',$this->opt, true)) return false;
			
			/** Define some vars **/
			//$data = $newdata; 
			$uploads = wp_upload_dir();
			
			//$css_dir = get_template_directory() . '/css/'; // Shorten code, save 1 call
			$css_dir = AJDE_EVCAL_DIR . '/'. EVENTON_BASE.  '/assets/css/'; 
			//$css_dir = plugin_dir_path( __FILE__ ).  '/assets/css/'; 
			
			//echo $css_dir;

			/** Save on different directory if on multisite **/
			if(is_multisite()) {
				$aq_uploads_dir = trailingslashit($uploads['basedir']);
			} else {
				$aq_uploads_dir = $css_dir;
			}
			
			/** Capture CSS output **/
			ob_start();
			require($css_dir . 'styles_evo_addons.php');
			$css = ob_get_clean();
				
			// if there is nothing on css
			if(empty($css)) return false;

			// save a version number for this
				$ver = get_option('eventon_addon_styles_version');
				(empty($ver))? 
					add_option('eventon_addon_styles_version', 1.00001):
					update_option('eventon_addon_styles_version', ($ver+0.00001));

			
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			/** Write to options.css file **/
			WP_Filesystem();
			global $wp_filesystem;
			if ( ! $wp_filesystem->put_contents( $aq_uploads_dir . 'eventon_addon_styles.css', $css, 0777) ) {
			    return true;
			}		
		}

	// update the dynamic styles file with updates styles val
	// @ 2.5
		function update_dynamic_styles(){
			ob_start();
			include(AJDE_EVCAL_PATH.'/assets/css/dynamic_styles.php');
			$evo_dyn_css = ob_get_clean();						
			update_option('evo_dyn_css', $evo_dyn_css);
		}	

// Shortcode on Editor
	function eventon_shortcode_button_init() {

	   	//Abort early if the user will never see TinyMCE
	    if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages'))
	    	return;

	    if( get_user_option('rich_editing') == 'true'){
		    //Add a callback to regiser our tinymce plugin   
		    add_filter("mce_external_plugins", array($this,"eventon_register_tinymce_plugin")); 

		    // Add a callback to add our button to the TinyMCE toolbar
		    add_filter('mce_buttons', array($this,'eventon_add_tinymce_button'));
		}
	}
	//This callback registers our plug-in
	function eventon_register_tinymce_plugin($plugin_array) {
	    $plugin_array['eventon_shortcode_button'] = AJDE_EVCAL_URL.'/assets/js/admin/shortcode.js';
	    return $plugin_array;
	}

	//This callback adds our button to the toolbar
	function eventon_add_tinymce_button($buttons) {
	    //Add the button ID to the $button array
	    array_push($buttons, "|", "eventon_shortcode_button");
	    
	    $this->eventon_shortcode_pop_content();
	    return $buttons;
	}

// shortcode generator
	function eventon_shortcode_button($context) {	
		global $pagenow, $typenow, $post;	
		
		if ( $typenow == 'post' && ! empty( $_GET['post'] ) ) {
			$typenow = $post->post_type;
		} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
	        $post = get_post( sanitize_text_field($_GET['post']) );
	        $typenow = $post->post_type;
	    }
		
		if ( $typenow == '' || $typenow == "ajde_events" ) return;

		if(evo_settings_check_yn($this->opt, 'evo_hide_shortcode_btn') ) return;

		//our popup's title
	  	$text = '[ ] ADD EVENTON';
	  	$title = __('eventON Shortcode generator','eventon');

	  	//append the icon
	  	$context .= "<a id='evo_shortcode_btn' class='ajde_popup_trig evo_admin_btn btn_prime' data-popc='eventon_shortcode' title='{$title}' href='#'>{$text}</a>";
		
		$this->eventon_shortcode_pop_content();
		
	  	return $context;
	}
	function eventon_shortcode_pop_content(){		
		$content = EVO()->shortcode_gen->get_content();		
		// eventon popup box
		EVO()->lightbox->admin_lightbox_content(array(
			'content'=>$content, 
			'class'=>'eventon_shortcode', 
			'attr'=>'clear="false"', 
			'title'=> __('Shortcode Generator','eventon'),			
			//'subtitle'=>'Select option to customize shortcode variable values'
		));

	}

// Supporting functions
	function get_image($size='', $placeholder=true){
		global $postid;

		$size = (!empty($size))? $size: 'thumbnail';

		$thumb = get_post_thumbnail_id($postid);

		if(!empty($thumb)){
			$img = wp_get_attachment_image_src($thumb, $size);
			return ( $img && isset($img[0]) )? $img[0]: false;
		}else if($placeholder){
			return AJDE_EVCAL_URL.'/assets/images/placeholder.png';
		}else{
			return false;
		}
	}

	function get_color($pmv=''){
		if(!empty($pmv['evcal_event_color'])){
			if( strpos($pmv['evcal_event_color'][0], '#') !== false ){
				return $pmv['evcal_event_color'][0];
			}else{
				return '#'.$pmv['evcal_event_color'][0];
			}
		}else{
			$opt = get_option('evcal_options_evcal_1');
			$cl = (!empty($opt['evcal_hexcode']))? $opt['evcal_hexcode']: '4bb5d8';
			return '#'.$cl;
		}
	}

	public function addon_exists($slug){
		$addon = new EVO_Product($slug);
		return $addon->is_installed();
	}

	

	// help dropdown
		function eventon_admin_help_tab() {
			include_once( AJDE_EVCAL_PATH.'/includes/admin/eventon-admin-content.php' );
			eventon_admin_help_tab_content();
		}
	// duplicate events action
		function eventon_duplicate_event_action() {			
			include_once( AJDE_EVCAL_PATH.'/includes/admin/post_types/duplicate_event.php');
			eventon_duplicate_event();
		}

	// plugin settings page additional links
		function eventon_plugin_links($links) { 
		  	$settings_link = '<a href="admin.php?page=eventon">'.__('Settings','eventon').'</a>'; 	  
		  	
		  	array_unshift($links, $settings_link); 
		  	return $links; 
		}

	// form mc refresh
	function eventon_refresh_mce( $ver ) {
		$ver += 3;
		return $ver;
	}

// LEGACY
	function eventon_load_colorpicker(){
		EVO()->elements->load_colorpicker();
	}
		
}