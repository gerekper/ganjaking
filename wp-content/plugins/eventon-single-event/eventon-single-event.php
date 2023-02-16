<?php
/*
 * Plugin Name: EventON - Single Event 
 * Plugin URI: http://www.myeventon.com/single-event
 * Description: Add more power to a single event in EventON
 * Version: 1.1.6
 * Author: AshanJay
 * Author URI: http://www.ashanjay.com
 * Requires at least: 4.0
 * Tested up to: 4.6
 */

define('EVO_SIN_EV',true);	
 
class EventON_sin_event{
	
	public $version='1.1.6';
	public $eventon_version = '2.4.6';
	public $name = 'SingleEvent';
	
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	private $urls;
	
	public $is_single_event = false;

	public $evo_opt='';
	
	/* Construct */
		public function __construct(){	
			$this->super_init();
			add_action('plugins_loaded', array($this, 'plugin_init'));
		}
		
		function plugin_init(){
			include_once( 'includes/admin/class-admin_check.php' );
			$this->check = new addon_check($this->addon_data);
			$check = $this->check->initial_check();
			
			if($check){
				$this->addon = new evo_addon($this->addon_data);

				add_action( 'init', array( $this, 'init' ), 0 );

				$this->evo_opt = get_option('evcal_options_evcal_1');
				
				// Installation
				register_activation_hook( __FILE__, array( $this, 'activate' ) );
				
			}else{
				add_action('admin_notices', array($this, '_eventon_warning'));
			}
		}
		function _eventon_warning(){
			?><div class="message error"><p><?php _e('EventON is required for Single Event to work properly.', 'eventon'); ?></p></div><?php
		}
	
	// SUPER init
		function super_init(){
			// PLUGIN SLUGS			
			$this->addon_data['plugin_url'] = path_join(WP_PLUGIN_URL, basename(dirname(__FILE__)));
			$this->addon_data['plugin_slug'] = plugin_basename(__FILE__);
			list ($t1, $t2) = explode('/', $this->addon_data['plugin_slug'] );
	        $this->addon_data['slug'] = $t1;
	        $this->addon_data['plugin_path'] = dirname( __FILE__ );
	        $this->addon_data['evo_version'] = $this->eventon_version;
	        $this->addon_data['version'] = $this->version;
	        $this->addon_data['name'] = $this->name;

	        $this->plugin_url = $this->addon_data['plugin_url'];
	        $this->plugin_slug = $this->addon_data['plugin_slug'];
	        $this->slug = $this->addon_data['slug'];
	        $this->plugin_path = $this->addon_data['plugin_path'];
	        $this->assets_path = str_replace(array('http:','https:'), '',$this->addon_data['plugin_url']).'/assets/';
	        
		}

	// INITIATE please
		function init(){
			
			include_once( 'includes/eventonSE_shortcode.php' );
			include_once( 'includes/class-frontend.php' );

			$this->frontend = new evose_frontend();

			// if single events running status
			$this->is_single_event = $this->frontend->is_single_event;

			if ( is_admin() ){
				include_once( 'includes/admin/admin-init.php' );
				$this->addon->updater();	
			}			

			if ( defined('DOING_AJAX') ){
				//include_once( 'includes/eventonSE_ajax.php' );
			}
					
			// Re-activate the permalinks on events
			remove_filter('get_sample_permalink_html','eventon_perm',10,4);
			
			$this->register_se_sidebar();			
			
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));
			
			$this->shortcodes = new evo_se_shortcode();			
		}

	//	SINGLE EVENT page template functions
		// HEADER
		public function eventon_header(){
			$this->frontend->page_frontend_scripts();	
			//add_action('wp_head', array( $this, 'remove_script') );			
			
			global $post;
			
			get_header();
		}	
		// GET : month and year for an event
		function get_single_event_header($event_id, $repeat_interval='', $lang='L1'){
			
			$event_datetime = new evo_datetime();
			$pmv = get_post_custom($event_id);

			$adjusted_start_time = $event_datetime->get_int_correct_event_time($pmv,$repeat_interval);					
			$formatted_time = eventon_get_formatted_time($adjusted_start_time);				
			return get_eventon_cal_title_month($formatted_time['n'], $formatted_time['Y'], $lang);
		}
	
	
	// SECONDARY FUNCTIONS
		// create a single event sidebar
			function register_se_sidebar(){
				$opt = $this->evo_opt;

				if(!empty($opt['evosm_1']) && $opt['evosm_1'] =='yes'){
					register_sidebar(array(
					  'name' => __( 'Single Event Sidebar' ),
					  'id' => 'evose_sidebar',
					  'description' => __( 'Widgets in this area will be shown on the right-hand side of single events page.' ),
					  'before_title' => '<h3 class="widget-title">',
					  'after_title' => '</h3>'
					));
				}
			}

			public function has_evo_se_sidebar(){
				$opt = $this->evo_opt;
				return (!empty($opt['evosm_1']) && $opt['evosm_1'] =='yes')? true: false;
			}
	
		// ACTIVATION			
			function activate(){
				global $wp_rewrite;
				//Call flush_rules() as a method of the $wp_rewrite object
				$wp_rewrite->flush_rules();

				// add actionUser addon to eventon addons list
				$this->addon->activate();
			}
			
			// Deactivate addon
			function deactivate(){
				$this->addon->remove_addon();
			}
		
}
// Initiate this addon within the plugin
$GLOBALS['eventon_sin_event'] = new EventON_sin_event();



//	Universally available functions
	// main content body for the single event page template
	function eventon_se_page_content(){
		global $eventon_sin_event, $eventon, $post;

		$cal_args = $eventon->evo_generator->shortcode_args;
		$lang = !empty($cal_args['lang'])? $cal_args['lang']:'L1';

		//_onlyloggedin
		$epmv = get_post_meta($post->ID);

		// only loggedin users can see single events
		$onlylogged_cansee = (!empty($eventon_sin_event->evo_opt['evosm_loggedin']) && $eventon_sin_event->evo_opt['evosm_loggedin']=='yes') ? true:false;
		$thisevent_onlylogged_cansee = (!empty($epmv['_onlyloggedin']) && $epmv['_onlyloggedin'][0]=='yes')? true:false;

		if( (!$onlylogged_cansee || ($onlylogged_cansee && is_user_logged_in() ) ) && 
			( !$thisevent_onlylogged_cansee || $thisevent_onlylogged_cansee && is_user_logged_in())  
		){
			
			$eventon_sin_event->frontend->page_frontend_scripts();
			eventon_get_template_part( 'content', 'single-event' , $eventon_sin_event->plugin_path.'/templates');	
		}else{
			echo "<p>".evo_lang('You must login to see this event', $lang)."<br/><a class='button' href=". wp_login_url() ." title='".evo_lang('Login', $lang)."'>".evo_lang('Login', $lang)."</a></p>";
		}		
	}

	// sidebar 
	function eventon_se_sidebar(){
		// sidebar
		$opt = get_option('evcal_options_evcal_1');
		if(!empty($opt['evosm_1']) && $opt['evosm_1'] =='yes'){
			
			if ( is_active_sidebar( 'evose_sidebar' ) ){

				?>
				<?php //get_sidebar('evose_sidebar'); ?>
				<div class='evo_page_sidebar'>
					<ul id="sidebar">
						<?php dynamic_sidebar( 'evose_sidebar' ); ?>
					</ul>
				</div>
				<?php
			}
		}
	}