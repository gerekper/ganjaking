<?php 
/**
 * EventON Setup
 *
 * @since 4.1.3
 */

defined( 'ABSPATH' ) || exit;

// Main EventON Class
final class EventON {
	public $version = '4.1.3';
	/**
	 * @var evo_generator
	 */
	public $evo_generator;	
	
	public $template_url;
	public $print_scripts=false;

	public $lang = 'L1';


	// setup one instance of eventon
	protected static $_instance = null;
	public static function instance(){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function template_path(){
		return $this->template_url;
	}

	/** Constructor. */
	public function __construct() {

		$this->define_constants();	
		add_action( 'init', array( $this, 'init' ), 0 );
		
		// Installation
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		
		// Include required files
		$this->includes();
		$this->init_hooks();
	}

	private function init_hooks(){		
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
		add_action( 'init', array( $this, 'template_controls' ) );

		// Deactivation
		register_deactivation_hook( AJDE_EVCAL_FILE, array($this,'deactivate'));
	}

	/**
	 * Define EVO Constants
	 */
	public function define_constants() {
		$upload_dir = wp_upload_dir( null, false );

		$this->define( 'EVO_VERSION', $this->version );
		$this->define( 'AJDE_EVCAL_DIR', WP_PLUGIN_DIR ); //E:\xampp\htdocs\WP/wp-content/plugins
		$this->define( 'AJDE_EVCAL_PATH', dirname( EVO_PLUGIN_FILE ) ); //E:\xampp\htdocs\WP/wp-content/plugins/eventON
		$this->define( 'EVO_ABSPATH', dirname( EVO_PLUGIN_FILE ) .'/'); 
		$this->define( 'AJDE_EVCAL_FILE', ( EVO_PLUGIN_FILE ) ); //E:\xampp\htdocs\WP\wp-content\plugins\eventON\eventon.php
		$this->define( 'AJDE_EVCAL_URL', path_join(plugins_url(), basename(dirname(EVO_PLUGIN_FILE))) );
		$this->define( 'AJDE_EVCAL_BASENAME', plugin_basename(EVO_PLUGIN_FILE) );//eventON/eventon.php
		$this->define( 'EVENTON_BASE', basename(dirname(EVO_PLUGIN_FILE)) );//eventON
		$this->define( 'BACKEND_URL', get_bloginfo('url').'/wp-admin/' );

		$this->assets_path = str_replace(array('http:','https:'), '',AJDE_EVCAL_URL).'/assets/';
		
	}
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	function plugin_path(){
		return untrailingslashit( plugin_dir_path( EVO_PLUGIN_FILE ) );
	}
		
	/**
	 * Include required files
	 * 
	 * @access private
	 * @return void
	 * @since  0.1
	 */
	private function includes(){		

		// post types
		include_once( EVO_ABSPATH.'includes/products/class-evo-addons.php' );
		include_once( EVO_ABSPATH.'includes/products/class-evo-products.php' );					
		include_once( EVO_ABSPATH.'includes/products/class-evo-product.php' );
		include_once( EVO_ABSPATH.'includes/class-evo-post-types.php' );
		include_once( EVO_ABSPATH.'includes/class-multi-data-types.php' );
		include_once( EVO_ABSPATH.'includes/class-search.php' );
		include_once( EVO_ABSPATH.'includes/class-evo-datetime.php' );
		include_once( EVO_ABSPATH.'includes/class-evo-helper.php' );
		include_once( EVO_ABSPATH.'includes/class-evo-install.php' );
		include_once( EVO_ABSPATH.'includes/class-cronjobs.php' );		
		include_once( EVO_ABSPATH.'includes/class-templates.php' );		
		include_once( EVO_ABSPATH.'includes/class-rest-api.php' );	
		include_once( EVO_ABSPATH.'includes/calendar/class-data-store.php' );	
		
		include_once( EVO_ABSPATH.'includes/elements/class-elements-main.php' );	
		include_once( EVO_ABSPATH.'includes/elements/class-shortcode_generator.php' );	
		include_once( EVO_ABSPATH.'includes/elements/class-lightboxes.php' );	

		include_once( EVO_ABSPATH.'ajde/ajde.php' );
			
		include_once( EVO_ABSPATH. 'includes/class-environment.php' );
		include_once( EVO_ABSPATH. 'includes/eventon-core-functions.php' );
		include_once( EVO_ABSPATH. 'includes/class-event.php' );
		include_once( EVO_ABSPATH. 'includes/class-frontend.php' );		
		include_once( EVO_ABSPATH. 'includes/class-map-styles.php' );
		
		include_once( EVO_ABSPATH. 'includes/calendar/class-calendar-now.php' );
		include_once( EVO_ABSPATH. 'includes/calendar/class-calendar-schedule.php' );
		include_once( EVO_ABSPATH. 'includes/calendar/class-calendar-helper.php' );
		include_once( EVO_ABSPATH. 'includes/calendar/class-calendar_generator.php' );
		include_once( EVO_ABSPATH. 'includes/calendar/class-calendar_gen.php' );
		include_once( EVO_ABSPATH. 'includes/calendar/views/eventcard_virtual.php' );
		include_once( EVO_ABSPATH. 'includes/calendar/class-calendar.php' );// deprecating

		include_once( EVO_ABSPATH.'includes/class-deprecations.php' );	
		
		include_once( EVO_ABSPATH.'includes/integration/class-intergration-general.php' );
		include_once( EVO_ABSPATH.'includes/integration/class-intergration-gutenberg.php' );
		include_once( EVO_ABSPATH.'includes/integration/class-intergration-visualcomposer.php' );	
		include_once( EVO_ABSPATH.'includes/integration/class-intergration-webhooks.php' );	// added in v4.1
		include_once( EVO_ABSPATH.'includes/integration/elementor/class-elementor-init.php' );
		include_once( EVO_ABSPATH.'includes/integration/zoom/class-zoom.php' );

		include_once( EVO_ABSPATH.'includes/class-evo-shortcodes.php' );

		include_once( EVO_ABSPATH.'includes/class-evo-ajax.php' );
			
		if ( $this->is_request('admin') ){	
			include_once(EVO_ABSPATH.'includes/admin/class-forms.php' );	
			include_once(EVO_ABSPATH.'includes/admin/settings/class-addon-details.php' );				
			include_once(EVO_ABSPATH.'includes/admin/class-views.php' );
			include_once(EVO_ABSPATH.'includes/admin/eventon-admin-functions.php' );
			include_once(EVO_ABSPATH.'includes/admin/eventon-admin-html.php' );
			include_once(EVO_ABSPATH.'includes/admin/eventon-admin-taxonomies.php' );
			include_once(EVO_ABSPATH.'includes/admin/post_types/ajde_events.php' );
			include_once(EVO_ABSPATH.'includes/admin/welcome.php' );		
			include_once(EVO_ABSPATH.'includes/admin/class-evo-admin.php' );	
			include_once(EVO_ABSPATH.'includes/products/class-licenses.php' );						
			include_once(EVO_ABSPATH.'includes/admin/class-evo-errors.php' );					
		}
		if ( ! $this->is_request('admin') || $this->is_request('ajax') ){
			// Functions
			include_once( EVO_ABSPATH.'includes/eventon-functions.php' );
		}

		if( $this->is_request('frontend')){
			$this->frontend_includes();
		}
		
		
	}	

	// include classes for frontend files
	public function frontend_includes(){
		
	}

	/**
	 * Function used to Init Eventon Template Functions - This makes them pluggable by plugins and themes.
	 */
	public function include_template_functions() {
		include_once( EVO_ABSPATH.'templates/_evo-template-functions.php' );
		include_once( EVO_ABSPATH.'templates/_evo-template-blocks.php' );
		include_once( EVO_ABSPATH.'includes/class-evo-template-loader.php' );
		
	}

	public function template_controls(){
		include_once EVO_ABSPATH . 'templates/_evo-template-control.php';
	}
	
	/** Init eventON when WordPress Initialises.	 */
	public function init() {

		if( class_exists('ajde')) $this->ajde = $GLOBALS['ajde'] = new ajde();
		
		// Set up localisation
		$this->load_plugin_textdomain();
		
		$this->template_url = apply_filters('eventon_template_url','eventon');

		
		$this->cal 				= new EVO_Cal_Gen();
		$this->evo_generator	= $this->calendar = new EVO_generator();	
		$this->frontend			= new evo_frontend();
		$this->mdt				= new evo_mdt();
		$this->temp 			= new EVO_Temp();
		$this->shortcodes		= new EVO_Shortcodes();	
		$this->ajax 			= new evo_ajax();
		
		$this->rest				= new EVO_Rest_API();
		$this->cron 			= new evo_cron();

		$this->elements			= new EVO_General_Elements();
		$this->shortcode_gen	= new EVO_Shortcode_Generator();
		$this->lightbox 		= new EVO_Lightboxes();	

		$this->gen_int 			= new EVO_Int_General(); 
		$this->evosv 			= new Evo_Cal_Schedule(); 

		$this->evohooks 		= new EVO_WebHooks();


		$GLOBALS['evo_shortcode_box'] = $this->shortcode_gen;
		//$this->helper			= new evo_helper();

		// Classes/actions loaded for the frontend and for ajax requests
		if ( ! is_admin() || defined('DOING_AJAX') ) {
			
		}
		if(is_admin()){
			if( class_exists('evo_admin')) $this->evo_admin 	= new evo_admin();
			if( class_exists('eventon_taxonomies') ) $this->taxonomies	= new eventon_taxonomies();	
		}

		
		// roles and capabilities
		eventon_init_caps();
		
		global $pagenow;
		
		// Initiate eventon 
		$this->init_evo_product();
		
		// Init action
		do_action( 'eventon_init' );
	}

	/*
	*	Return true is the request if non-legacy REST API request
	*/
	public function is_rest_api_request() {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix         = trailingslashit( rest_get_url_prefix() );
		$is_rest_api_request = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		/**
		 * Filter to specify if the current request is a REST API request.
		 */
		return apply_filters( 'evo_is_rest_api_request', $is_rest_api_request ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingSinceComment
	}

	// what type of request is this?
	// @param string $type admin, ajax, cron, frontend
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! $this->is_rest_api_request();
		}
	}
	// Initiate evo product
		function init_evo_product(){
			$ADDON = new evo_addons(array(
				'ID'=> 'EVO',
				'version'=> $this->version,
				'slug'=>strtolower(EVENTON_BASE),
				'plugin_slug'=>AJDE_EVCAL_BASENAME,
				'tested'=> 5.3,
				'name'=>EVENTON_BASE
			));
		}

	/** register_widgets function. */
		function register_widgets() {
			include_once( EVO_ABSPATH. 'includes/class-evo-wp-widgets.php' );
		}

	
	
	// MOVED functions
		/*** output the inpage popup window for eventon	 */
			public function output_eventon_pop_window($arg){
				$this->lightbox->admin_lightbox_content($arg);			
			}
		/*	Legend popup box across wp-admin	*/
			public function throw_guide($content, $position='', $echo=true){
				$content = $this->elements->tooltips($content, $position);
				if($echo){ echo $content;  }else{ return $content; }			
			}
		/* EMAIL functions */
			public function get_email_part($part){
				return $this->frontend->get_email_part($part);
			}
		/**
		 * body part of the email template loading
		 * @update 2.2.24
		 */
			public function get_email_body($part, $def_location, $args='', $paths=''){
				return $this->frontend->get_email_body($part, $def_location, $args='', $paths='');
			}
		
	/** Activate function to store version.	 */
		public function activate(){
			set_transient( '_evo_activation_redirect', 1, 60 * 60 );		
			do_action('eventon_activate');
		}	
		// update function
			public function update(){
				//set_transient( '_evo_activation_redirect', 1, 60 * 60 );		
			}

	// deactivate eventon
		public function deactivate(){	
			do_action('eventon_deactivate');
		}
	
	/** Ensure theme and server variable compatibility and setup image sizes.. */
		public function setup_environment() {
			// Post thumbnail support
			if ( ! current_theme_supports( 'post-thumbnails', 'ajde_events' ) ) {
				add_theme_support( 'post-thumbnails' );
				remove_post_type_support( 'post', 'thumbnail' );
				remove_post_type_support( 'page', 'thumbnail' );
			} else {
				add_post_type_support( 'ajde_events', 'thumbnail' );
			}
		}
		
	/** LOAD Backender UI and functionalities for settings. */
	// Legacy
		public function load_ajde_backender(){			
			include_once(  'includes/admin/settings/class-settings.php' );
			$this->settings = new EVO_Settings();
		}	
		public function register_backender_scripts(){
			$this->settings->register_ss();		
		}
		public function enqueue_backender_styles(){
			$this->settings->load_styles_scripts();	
		}
		
		
	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 * Admin Locale. Looks in:
	 * - WP_LANG_DIR/eventon/eventon-admin-LOCALE.mo
	 * - WP_LANG_DIR/plugins/eventon-admin-LOCALE.mo
	 *
	 * @access public
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'eventon' );
		
		load_textdomain( 'eventon', WP_LANG_DIR . "/eventon/eventon-admin-".$locale.".mo" );
		load_textdomain( 'eventon', WP_LANG_DIR . "/plugins/eventon-admin-".$locale.".mo" );		
		
		if ( is_admin() ) {
			load_plugin_textdomain( 'eventon', false, plugin_basename( dirname( __FILE__ ) ) . "/lang" );
		}

		// frontend - translations are controlled by myeventon settings> language	
	}
	
	public function get_current_version(){
		return $this->version;
	}	
	
	// return eventon option settings values **/
		public function evo_get_options($field, $array_field=''){
			if(!empty($array_field)){
				$options = get_option($field);
				$options = $options[$array_field];
			}else{
				$options = get_option($field);
			}		
			return !empty($options)?$options:null;
		}

	// deprecated function after 2.2.12
		public function addon_has_new_version($values){}

	// template locator function to use for addons
		function template_locator($file, $default_locations, $append='', $args=''){
			$childThemePath = get_stylesheet_directory();

			// Paths to check
			$paths = apply_filters('evo_file_template_paths', array(
				1=>TEMPLATEPATH.'/'.$this->template_url. $append, // TEMPLATEPATH/eventon/--append--
				2=>$childThemePath.'/',
				3=>$childThemePath.'/'.$this->template_url. $append,
			));

			$location = $default_locations .$file;
			// FILE Exist
			if ( $file ) {			
				// each path
				foreach($paths as $path){				
					if(file_exists($path.$file) ){	
						$location = $path.$file;	
						break;
					}
				}
			}

			return $location;
		}

	// Events archive page content
		function archive_page(){
			$archive_page_id = evo_get_event_page_id();

			// check whether archieve post id passed
			if($archive_page_id){

				$archive_page  = get_page($archive_page_id);	
				
				echo "<div class='wrapper evo_archive_page'>";

				do_action('evo_event_archive_page_before_content');

				echo apply_filters('the_content', $archive_page->post_content);

				do_action('evo_event_archive_page_after_content');

				echo "</div>";

			}else{
				echo "<p>ERROR: Please select a event archive page in eventON Settings > Events Paging > Select Events Page</p>";
			}
		}	
}