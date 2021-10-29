<?php  if ( ! defined( 'ABSPATH' ) ) exit;
/*
Plugin Name: Mute Screamer
Plugin URI: http://ampt.github.com/mute-screamer
Description: <a href="http://phpids.org/">PHPIDS</a> for Wordpress.
Author: ampt
Version: 1.0.7
Author URI: http://notfornoone.com/
*/

/*
 * Mute Screamer
 *
 * PHPIDS for Wordpress
 *
 * Copyright (c) 2011 Luke Gallagher
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHesc_htmlANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

if ( ! class_exists( 'HMWP_MS_IDS' ) AND version_compare( PHP_VERSION, '5.2', '>=' ) ) :

define( 'HMWP_MS_PATH', dirname( __FILE__ ) );
set_include_path( get_include_path() . PATH_SEPARATOR . HMWP_MS_PATH . '/libraries' );

require_once 'hmwp_ms/Utils.php';
require_once 'hmwp_ms/Log_Database.php';
require_once 'hmwp_ms/functions.php';
require_once 'IDS/Init.php';
require_once 'IDS/Log/Composite.php';

/**
 * Mute Screamer
 */
class HMWP_MS_IDS {

	const INTRUSIONS_TABLE = 'hmwp_ms_intrusions';
	const VERSION          = '1.0.7';
	const DB_VERSION       = 2;
	const POST_TYPE        = 'hmwp_ms_ban';

    //hassan
    const slug = 'hide_my_wp';

	/**
	 * An instance of this class
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Email address to send alerts to
	 *
	 * @var string
	 */
	private $email = '';

	/**
	 * Email notifications flag
	 *
	 * @var boolean
	 */
	//private $email_notifications = false;

	/**
	 * Email notifications threshold
	 *
	 * @var int
	 */
	//private $email_threshold = 0;

	/**
	 * Input fields to be exlcuded from PHPIDS
	 *
	 * @var array
	 */
	//private $exception_fields = array();

	/**
	 * Input fields to be treated as HTML
	 *
	 * @var array
	 */
	//private $html_fields = array();

	/**
	 * Input fields to be treated as JSON data
	 *
	 * @var array
	 */
	//private $json_fields = array();

	/**
	 * New intrusion count
	 *
	 * @var int
	 */
	//private $new_intrusions_count = 0;

	/**
	 * Enable PHPIDS in the WordPress admin
	 *
	 * @var int
	 */
	//private $enable_admin = 1;

	/**
	 * Impact for a warning page to be shown
	 *
	 * @var int
	 */
	//private $warning_threshold = 40;

	/**
	 * Log user out of WordPress admin as a warning
	 *
	 * @var int
	 */
	//private $warning_wp_admin = 0;

	/**
	 * Ban clients
	 *
	 * @var int
	 */
//	private $ban_enabled = 0;

	/**
	 * Impact for a ban to be applied
	 *
	 * @var int
	 */
	//private $ban_threshold = 70;

	/**
	 * Attack repeat limit
	 *
	 * @var int
	 */
	//private $attack_repeat_limit = 5;

	/**
	 * Time in seconds a user is banned for.
	 *
	 * @var int
	 */
	//private $ban_time = 300;

	/**
	 * Enable logging of intrusion attempts
	 *
	 * @var int
	 */
	//private $enable_intrusion_logs = 1;

	/**
	 * PHPIDS result
	 *
	 * @var object
	 */
	private $result = null;

	/**
	 * Is the current request a banned request?
	 *
	 * @var boolean
	 */
	//public $is_ban = false;

	/**
	 * Constructor
	 *
	 * Initialise Mute Screamer and run PHPIDS
	 *
	 * @return object
	 */
	public function __construct() {
		// Require 3.0.
		if ( ! function_exists( '__return_false' ) )
			return;

		/*if ( is_multisite() ) {
			add_action( 'network_admin_notices', 'HMWP_MS_Utils::ms_notice' );
			return;
		}*/

        if (is_multisite())
            $installed = get_blog_option(BLOG_ID_CURRENT_SITE, 'hmwp_ids_installed');
        else
            $installed = get_option('hmwp_ids_installed');

        if (!$installed)
            $this->activate();
            
                add_action('admin_init', array($this,'activate'));    

		// PHPIDS requires a writable folder
		if ( ! is_writable( HMWP_MS_Utils::upload_path() ) ) {
			add_action( 'admin_notices', 'HMWP_MS_Utils::writable_notice' );
			return;
		}

		// Display updates in admin bar, run after wp_admin_bar_updates_menu
		//add_action( 'admin_bar_menu', array( $this, 'action_admin_bar_menu' ), 100 );


        self::$instance = $this;
		$this->init();
		$this->run();

		// Process wp-login.php requests
		if ( HMWP_MS_Utils::is_wp_login() ) {
			do_action( 'hmwp_ms_wp_login' );
		}
	}

	/**
	 * Initialise Mute Screamer
	 *
	 * @return void
	 */
	private function init() {
		self::db_table();
		$this->init_options();

		// Update db table reference when switching blogs
		//add_action( 'switch_blog', 'HMWP_MS_IDS::db_table' );

		// Load textdomain
		//HMWP_MS load_plugin_textdomain( 'mute-screamer', false, dirname( plugin_basename( __FILE__ ) ).'/languages' );

		// Add ban post type, to track banned users
		//$args = array(
		//	'public' => false,
		//);
		//register_post_type( self::POST_TYPE, $args );

		// Remove expired user bans
		//$this->delete_expired_bans();

		// Is this a banned user?
		//$this->banned_user();

		// Are we in the WP Admin?
		if ( is_admin() ) {
            //hassan
			//if ( $this->db_version < self::DB_VERSION )
			//	$this->upgrade();

		//	require_once 'hmwp_ms/Update.php';
			require_once 'hmwp_ms_admin.php';
			new HMWP_MS_Admin();
		}
	}

	/**
	 * Initialise PHPIDS
	 *
	 * @return object
	 */
	private function init_ids() {
		$config['General']['filter_type']   = 'xml';
		$config['General']['base_path']     = HMWP_MS_PATH . '/libraries/IDS/';
		$config['General']['use_base_path'] = false;
		$config['General']['filter_path']   = HMWP_MS_PATH . '/libraries/IDS/default_filter.xml';
		$config['General']['tmp_path']      = HMWP_MS_Utils::upload_path();
		$config['General']['scan_keys']     = false;

		$config['General']['HTML_Purifier_Path']  = 'vendors/htmlpurifier/HTMLPurifier.standalone.php';
		$config['General']['HTML_Purifier_Cache'] = HMWP_MS_Utils::upload_path();

		$config['Caching']['caching'] = 'none';

        $exceptions = $this->opt( 'exception_fields' );
        $exceptions = str_replace( array( "\r\n", "\n", "\r" ), "\n", $exceptions);
        $exceptions = explode( "\n", $exceptions );

        // Exception fields array must not contain an empty string
        // otherwise all fields will be excepted
        foreach ( $exceptions as $k => $v ) {
            if ( strlen( $exceptions[$k] ) == 0 ) {
                unset( $exceptions[$k] );
            }else{
                $exceptions[$k] = trim($exceptions[$k],' ');
            }
        }

		// Mark fields that shouldn't be monitored
		$config['General']['exceptions'] = $exceptions ? $exceptions : false;


        $html_fields = str_replace( array( "\r\n", "\n", "\r" ), "\n", $this->opt('ids_html_fields') );
        $html_fields = explode( "\n", $html_fields );

        // Exception fields array must not contain an empty string
        // otherwise all fields will be excepted
        foreach ( $html_fields as $k => $v ) {
            if ( strlen( $html_fields[$k] ) == 0 ) {
                unset( $html_fields[$k] );
            }
        }


		// Mark fields that contain HTML
		$config['General']['html'] = $html_fields;

		// Mark fields that have JSON data
		//$config['General']['json'] = $this->json_fields ? $this->json_fields : false;
        $config['General']['json']= false;

		// Email logging
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$subject = sprintf( __( '[%s] HMWP IDS Alert', 'mute-screamer' ), $blogname );
		// $config['Logging']['recipients']   = get_option( 'admin_email' );
		$config['Logging']['recipients']   = $this->opt('ids_email') == '' ? get_option( 'admin_email' ) : $this->opt('ids_email');
		$config['Logging']['subject']      = $subject;
		$config['Logging']['header']       = '';
		$config['Logging']['envelope']     = '';
		$config['Logging']['safemode']     = true;
		$config['Logging']['urlencode']    = true;
		$config['Logging']['allowed_rate'] = 15;

		$ids = IDS_Init::init();
		$ids->setConfig( $config, true );

		return $ids;
	}

	/**
	 * Run PHPIDS
	 *
	 * @return void
	 */
	public function run() {
		// Are we running in the WordPress admin?
		//if ( is_admin() AND $this->enable_admin == false ) {
		///	return;
		//}

            //hassan HMWP_MS
        $can_deactive= false;
        if (isset($_COOKIE['hmwp_can_deactivate']) && preg_replace("/[^a-zA-Z]/", "", substr(NONCE_SALT, 0, 8)) == preg_replace("/[^a-zA-Z]/", "",$_COOKIE['hmwp_can_deactivate']))
            $can_deactive= true;

        if (!$this->opt('ids_admin_include') &&  $can_deactive)
            return;

        if (is_admin() && !$this->opt('ids_level') ) // is 0
            return;

        if ($this->opt('login_query'))
            $login_query = preg_replace("/[^a-zA-Z]/", "", substr(NONCE_SALT, 0, 6)).'_'.$this->opt('login_query');
        else
            $login_query = preg_replace("/[^a-zA-Z]/", "", substr(NONCE_SALT, 0, 6)).'_'.'hide_my_wp';


	    $request = array(
	        'REQUEST' => $_REQUEST,
	        'GET' => $_GET,
	        'POST' => $_POST,
	        'COOKIE'=>'',
            'SERVER'=>''
	    );

        //Do not allow to cookies block login area!
      //  if ($this->opt('ids_cookie') && strpos($_SERVER['PHP_SELF'], 'wp-login.php')===false)
       //     $request['COOKIE'] = $_COOKIE;


        $request['SERVER'] = array (
            'HTTP_REFERER'=> '', //will be added below
            'REQUEST_URI' =>  (isset($_SERVER['REQUEST_URI'])) ? strtok($_SERVER["REQUEST_URI"],'?') :'',
            'PHP_SELF' => (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : ''
            //HTTP_USER_AGENT
            //http://snippets.khromov.se/safe-php-_server-variables/
        );

        if ((isset($_GET['style_wrapper']) || isset($_GET['style_internal_wrapper'])) && $this->opt('admin_key') && isset($_GET[$login_query]) && $_GET[$login_query]==$this->opt('admin_key'))
            $request['SERVER'] = '';//nothing! to load main style correctly
        else
            $request['SERVER'] = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';

        if ($this->has_short_values($_GET, 5) && $this->has_short_values($_POST, 5) && $this->has_short_values($_REQUEST, 5) && $this->has_short_values($request['SERVER'], 12))
            return;

        $init = $this->init_ids();
		$ids = new IDS_Monitor( $request, $init );
		$this->result = $ids->run();

        // Nothing more to do
        if ( $this->result->isEmpty() ) {
            return;
        }

        $max=0;
        foreach ($this->result as $event) {
            $max = max($max, $event->getImpact());
		}

        $compositeLog = new IDS_Log_Composite();
		if (!$this->opt('enable_ids')){
			return;
		}
        if ($this->opt('log_ids_min') && $this->opt('log_ids_min') <= $this->result->getImpact()){
			$compositeLog->addLogger( new HMWP_MS_Log_Database() );
			// Update new intrusion count, log the event
			$this->update_intrusion_count();
		}
		// Send alert email
        if ($this->opt('email_ids_min') && $this->opt('email_ids_min') <= $this->result->getImpact()){
			require_once 'hmwp_ms/Log_Email.php';
			$compositeLog->addLogger( HMWP_MS_Log_Email::getInstance( $init, 'HMWP_MS_Log_Email' ) );
		}
		$compositeLog->execute( $this->result );

        if ($this->opt('ids_mode') && $this->opt('ids_mode') == '1'){
			/**
			 * Alert Only
			 */
		} else {
			if ($this->opt('block_ids_min') && $this->opt('block_ids_min') <= $this->result->getImpact()){
				$this->block_access();
				// Load custom error page
				//add_action( 'template_redirect', array( $this, 'load_template' ) );
				// Catch wp-login.php requests
				//add_action( 'hmwp_ms_wp_login', array( $this, 'load_template' ) );
			}
		}

		//$this->ban_user();

		// Warning page runs last to allow for ban processing
	    //	$this->warning_page();
	}

    public function has_short_values($array, $max){
        if (is_array($array)){
            foreach (array_values($array) as $v){
                if (is_array($v)){
                    foreach (array_values($v) as $vv){
                        if (is_array($vv)){
                            foreach (array_values($vv) as $vvv)
                                if (!is_array($vvv) && strlen($vvv) > $max)
                                    return false;

                        }else{
                            if (strlen($vv) > $max)
                                return false;
                        }
                    }
                }else{
                    if (strlen($v) > $max)
                        return false;
                }
            }
            return true;
        }
        return false;

    }

	/**
	 * We are sending alert emails if email notifications
	 * are turned on and the result impact is greater than the
	 * email threshold.
	 *
	 * @return boolean
	 */
	/*private function send_alert_email() {
		if ( ! $this->email_notifications ) {
			return false;
		}

		if ( $this->result->getImpact() < $this->email_threshold ) {
			return false;
		}

		return true;
	}*/

	/**
	 * Display a warning page if the impact is over the warning threshold
	 * If the request was in WP Admin logout the current user.
	 *
	 * @return void
	 */
	/*private function warning_page() {
		if ( $this->result->getImpact() < $this->warning_threshold ) {
			return;
		}

		// End user's session if they are in the wp admin
		if ( is_admin() AND $this->warning_wp_admin == true ) {
			wp_logout();
			wp_safe_redirect( '/wp-login.php?loggedout=true' );
			exit;
		}

		// Load custom error page
		add_action( 'template_redirect', array( $this, 'load_template' ) );

		// Catch wp-login.php requests
		add_action( 'hmwp_ms_wp_login', array( $this, 'load_template' ) );
	}*/



        function block_access(){
            include_once(ABSPATH . '/wp-includes/pluggable.php');
            status_header( 404 );
            nocache_headers();

            $headers = array('X-Pingback' => get_bloginfo('pingback_url'));
            $headers['Content-Type'] = get_option('html_type') . '; charset=' . get_option('blog_charset');
            foreach( (array) $headers as $name => $field_value )
                @header("{$name}: {$field_value}");

            //if ( isset( $headers['Last-Modified'] ) && empty( $headers['Last-Modified'] ) && function_exists( 'header_remove' ) )
            //	@header_remove( 'Last-Modified' );


            //wp-login.php wp-admin and direct .php access can not be implemented using 'wp' hook block_access can't work correctly with init hook so we use wp_remote_get to fix the problem
        //    if ( is_admin()) {

                if ($this->opt('custom_404') && $this->opt('custom_404_page') )   {
					$response = @wp_remote_get( home_url( '?'.$this->opt('page_query').'=' . $this->opt('custom_404_page') ) );

					if ( ! is_wp_error($response) )
						echo $response['body'];
					else
                    //wp_redirect( get_permalink($this->opt('custom_404_page'))) ;
                    	wp_redirect( home_url( '?'.$this->opt('page_query').'=' . $this->opt('custom_404_page') )) ;
                }else{
                    $response = @wp_remote_get( home_url('/nothing_404_404') );

                    if ( ! is_wp_error($response) )
                        echo $response['body'];
                    else
                        wp_redirect( home_url('/404_Not_Found')) ;
                }

      //      }else{
     //           if  (get_404_template())
      //              require_once( get_404_template() );
      //          else
      //              require_once(get_single_template());
       //     }

            die();
        }

        /**
	 * Show a 500 error page if the template exists.
	 * Otherwise show a 404 error or redirect to homepage.
	 *
	 * @return void
	 */
	public function load_template( $template = '' ) {
		global $wp_query;

		if ( did_action( 'hmwp_ms_wp_login' ) ) {
			$this->admin_message();
		}

		$templates[] = '500.php';
		$templates[] = '404.php';
		$templates[] = 'index.php';

		$template = locate_template( $templates );

		// Did we find a template? If not fail silently...
		if ( '' == $template )
			exit;

		if ( '404.php' == basename( $template ) ) {
			$wp_query->set_404();
			status_header( 404 );
			nocache_headers();
		} else if ( '500.php' == basename( $template ) ) {
			status_header( 500 );
			nocache_headers();
		} else if ( ! is_front_page() ) {
			wp_redirect( get_bloginfo( 'url' ) );
			exit;
		}

		load_template( $template );
		exit;
	}

	/**
	 * Ban user if the impact is over the ban threshold,
	 * if it is under the ban threshold record the attack
	 * for the repeat attack limit.
	 *
	 * @return void
	 */
	/*private function ban_user() {
		$data = array();

		// If the attack is under the ban threshold mark this
		// post as a repeat attack
		if ( $this->result->getImpact() < $this->ban_threshold ) {
			$data['post_excerpt'] = 'repeat_attack';
		}

		$data['post_type']    = self::POST_TYPE;
		$data['post_status']  = 'publish';
		$data['post_content'] = HMWP_MS_Utils::ip_address();
		$data['post_title']   = HMWP_MS_Utils::server( 'HTTP_USER_AGENT' );
		wp_insert_post( $data );
	}*/

	/**
	 * Remove user bans that have expired.
	 *
	 * @return void
	 */
	/*private function delete_expired_bans() {
		global $wpdb;

		$date = date( 'Y-m-d H:i:s', time() - $this->ban_time );
		$sql  = $wpdb->prepare( "DELETE FROM {$wpdb->posts} WHERE post_type = '".self::POST_TYPE."' AND post_date_gmt < '%s'", $date );
		$wpdb->query( $sql );
	}*/

	/**
	 * Number of attacks the user has made
	 *
	 * @return integer
	 */
	private function attack_count() {
		global $wpdb;

		$sql    = $wpdb->prepare( "SELECT COUNT(*) AS count FROM {$wpdb->posts} WHERE post_content = '%s' AND post_excerpt = 'repeat_attack'", HMWP_MS_Utils::ip_address() );
		$result = $wpdb->get_row( $sql );
		return (int) $result->count;
	}

	/**
	 * Display an error page for banned users
	 *
	 * @return void
	 */
	/*private function banned_user() {
		global $wpdb;

		// Is banning enabled?
		if ( ! $this->ban_enabled ) {
			return;
		}

		$sql    = $wpdb->prepare( "SELECT post_type, post_content, post_title, post_excerpt FROM {$wpdb->posts} WHERE post_type = '".self::POST_TYPE."' AND post_content = '%s' AND post_excerpt <> 'repeat_attack'", HMWP_MS_Utils::ip_address() );
		$result = $wpdb->get_row( $sql );

		// If there is no result and the user is under the repeat limit, we're good
		if ( ! $result AND $this->attack_count() < $this->attack_repeat_limit ) {
			return;
		}

		// This is a ban request
		$this->is_ban = true;

		// Admin notice
		if ( is_admin() ) {
			$this->admin_message();
		}

		// Load warning template
		add_action( 'template_redirect', array( $this, 'load_template' ) );

		// Catch wp-login.php requests
		add_action( 'hmwp_ms_wp_login', array( $this, 'load_template' ) );
	}*/

	/**
	 * Display admin warning message for a ban in the wp-admin
	 * and for warning on the wp-login page.
	 *
	 * @return void
	 */
	private function admin_message() {
		$filter  = 'hmwp_ms_admin_warn_message';
		$message = __( 'There was an error with the page you requested.', 'mute-screamer' );

		/*if ( $this->is_ban ) {
			$filter  = 'hmwp_ms_admin_ban_message';
			$message = __( 'There was a problem processing your request.', 'mute-screamer' );
		}*/

		$message = apply_filters( $filter, $message );
		wp_die( $message );
	}

	/**
	 * Get the Mute Screamer instance
	 *
	 * @return object
	 */
	public static function instance() {
		return self::$instance;
	}

    public function get_option($key =''){
        return $this->opt($key);
    }
	/**
	 * Retrieve options
	 *
	 * @param string
	 * @return mixed
	 */
	public function opt( $key = '' ) {
		//return isset( $this->$key ) ? $this->$key : false;

        //HMWP_MS
        if (is_multisite())
            $options = get_blog_option(BLOG_ID_CURRENT_SITE, 'hide_my_wp');
        else
            $options = get_option('hide_my_wp');

        if (isset($options[$key]))
            return $options[$key];
        return false;

    }

	/**
	 * Update options
	 *
	 * @param string
	 * @param mixed
	 * @return void
	 */
	public function set_option( $key = '', $val = '' ) {
        return $this->set_opt($key = '', $val = '' );
		// Bail if the key to be set does not exist in defaults
		/*if ( ! array_key_exists( $key, self::default_options() ) )
			return;

		$options = get_option( 'hmwp_ms_options' );
		$options[$key] = $val;
		update_option( 'hmwp_ms_options', $options );
		$this->$key = $val;*/
	}

	/**
	 * Initialse options
	 *
	 * @return void
	 */
	private function init_options() {
        //hassan
        if (is_multisite())
            $options = get_blog_option(BLOG_ID_CURRENT_SITE, self::slug);
        else
            $options = get_option(self::slug);

		//$options = get_option( 'hmwp' );
		//$options['db_version'] = isset( $options['db_version'] ) ? $options['db_version'] : 0;
		$default_options = self::default_options();

		// Fallback to default options if the options don't exist in
		// the database (kind of like a soft upgrade).
		// Automatic plugin updates don't call register_activation_hook.
		/*foreach ( $default_options as $key => $val ) {
            if (isset($options[$key]))
			    $this->$key =  $options[$key] ;
            else
                $this->key = '';
		}*/


	}

	/**
	 * Update intrusion count for menu
	 *
	 * @return void
	 */
	private function update_intrusion_count() {
		$new_count = $this->opt('new_intrusions_count') + count( $this->result->getIterator() );
        //hassan
		$this->set_opt( 'new_intrusions_count', $new_count );
	}

    //hassan
    function set_opt($key='', $value=''){
        if (is_multisite()) {
            $opts = get_blog_option(BLOG_ID_CURRENT_SITE, 'hide_my_wp');
            $opts[$key]= $value;
            update_blog_option(BLOG_ID_CURRENT_SITE, 'hide_my_wp', $opts);
        }else{
            $opts = get_option('hide_my_wp');
            $opts[$key]= $value;
            update_option('hide_my_wp', $opts);
        }
    }

	/**
	 * Modify admin bar update count when there are Mute Screamer updates available
	 *
	 * @return void
	 */
	/*public function action_admin_bar_menu()	{
		global $wp_admin_bar;

		$updates = get_site_transient( 'hmwp_ms_update' );
		if ( $updates === false OR empty( $updates['updates'] ) ) {
			return;
		}

		$hmwp_ms_count = count( $updates['updates'] );
		$hmwp_ms_title = sprintf( _n( '%d Mute Screamer Update', '%d Mute Screamer Updates', $hmwp_ms_count, 'mute-screamer' ), $hmwp_ms_count );

		// WordPress 3.3
		if ( function_exists( 'wp_allowed_protocols' ) ) {
			$this->wp_admin_bar_updates_menu( $wp_admin_bar, $hmwp_ms_count, $hmwp_ms_title );
			return;
		}

		// WordPress 3.1, 3.2
		// Other WP updates, modify existing menu
		if ( isset( $wp_admin_bar->menu->updates ) ) {
			// <span title='1 Plugin Update'>Updates <span id='ab-updates' class='update-count'>1</span></span>
			$title = $wp_admin_bar->menu->updates['title'];

			// Get the existing title attribute
			preg_match( "/title='(.+?)'/", $title, $matches );
			$link_title  = isset( $matches[1] ) ? $matches[1] : '';
			$link_title .= ', '.esc_attr( $hmwp_ms_title );

			// Get the existing update count
			preg_match( '/<span\b[^>]*>(\d+)<\/span>/', $title, $matches );
			$update_count = isset( $matches[1] ) ? $matches[1] : 0;

			$update_count += $hmwp_ms_count;

			$update_title  = "<span title='$link_title'>";
			$update_title .= sprintf( __( 'Updates %s', 'mute-screamer' ), "<span id='ab-updates' class='update-count'>" . number_format_i18n( $update_count ) . '</span>' );
			$update_title .= '</span>';

			$wp_admin_bar->menu->updates['title'] = $update_title;
			return;
		}

		// Add update menu
		$update_title  = "<span title='".esc_attr( $hmwp_ms_title )."'>";
		$update_title .= sprintf( __( 'Updates %s', 'mute-screamer' ), "<span id='ab-updates' class='update-count'>" . number_format_i18n( $hmwp_ms_count ) . '</span>' );
		$update_title .= '</span>';
		$wp_admin_bar->add_menu( array( 'id' => 'updates', 'title' => $update_title, 'href' => network_admin_url( 'update-core.php' ) ) );
	}*/

	/**
	 * Display admin bar updates for WordPress 3.3
	 *
	 * @param WP_Admin_Bar instance
	 * @param integer $count
	 * @param string $title
	 * @return void
	 */
	private function wp_admin_bar_updates_menu( $wp_admin_bar, $count = 0, $title = '' ) {
		if ( ! $count OR ! $title )
			return;

		$update_data = wp_get_update_data();

		$update_data['counts']['total'] += $count;

		if ( ! $update_data['title'] ) {
			$update_data['title'] = $title;
		} else {
			$update_data['title'] .= ", {$title}";
		}

		$update_title = '<span class="ab-icon"></span><span class="ab-label">' . number_format_i18n( $update_data['counts']['total'] ) . '</span>';

		$update_node = $wp_admin_bar->get_node( 'updates' );

		// Does the update menu already exist?
		if ( ! $update_node ) {
			$wp_admin_bar->add_menu( array(
				'id'    => 'updates',
				'title' => $update_title,
				'href'  => network_admin_url( 'update-core.php' ),
				'meta'  => array(
					'title' => $update_data['title'],
				),
			) );

			return;
		}

		// Update existing menu
		$update_node->title = $update_title;
		$update_node->meta['title'] = $update_data['title'];

		$wp_admin_bar->add_menu( $update_node );
	}

	/**
	 * Default options
	 *
	 * @return array
	 */
	public static function default_options() {
		$default_exceptions = array(
			'REQUEST.comment',
			'POST.comment',
			'REQUEST.permalink_structure',
			'POST.permalink_structure',
			'REQUEST.selection',
			'POST.selection',
			'REQUEST.content',
			'POST.content',
			'REQUEST.__utmz',
			'COOKIE.__utmz',
			'REQUEST.s_pers',
			'COOKIE.s_pers',
			'REQUEST.user_pass',
			'POST.user_pass',
			'REQUEST.pass1',
			'POST.pass1',
			'REQUEST.pass2',
			'POST.pass2',
			'REQUEST.password',
			'POST.password',
		);

		return array(
			//'db_version' => self::DB_VERSION,
			//'email_threshold' => 20,
			//'email_notifications' => false,
			//'email' => get_option( 'admin_email' ),
			//'exception_fields' => $default_exceptions,
			//'html_fields' => array(),
			//'json_fields' => array(),
			//'new_intrusions_count' => 0,
			//'enable_admin' => 1,
			//'warning_threshold' => 40,
			//'warning_wp_admin' => 0,
			//'ban_enabled' => 0,
			//'ban_threshold' => 70,
			//'attack_repeat_limit' => 5,
			//'ban_time' => 300,
			//'enable_intrusion_logs' => 1,
			//'enable_automatic_updates' => 1,
		);
	}

	/**
	 * Upgrade database
	 *
	 * @return void
	 */
    /*private function upgrade() {
        global $wpdb;

        if ( $this->db_version < 2 ) {
            // Prefix intrusions table
            $wpdb->query( 'DROP TABLE IF EXISTS `' . $wpdb->hmwp_ms_intrusions . '`' );
            $wpdb->query( 'ALTER TABLE ' . self::INTRUSIONS_TABLE . " RENAME TO {$wpdb->hmwp_ms_intrusions}" );

            // Take a punt and change the intrusion dates to what we *think* GMT time is
            $time_difference = get_option( 'gmt_offset' );

            $server_time = time() + date( 'Z' );
            $blog_time   = $server_time + $time_difference * 3600;
            $gmt_time    = time();

            $diff_gmt_server  = ($gmt_time - $server_time) / 3600;
            $diff_blog_server = ($blog_time - $server_time) / 3600;
            $diff_gmt_blog    = $diff_gmt_server - $diff_blog_server;
            $gmt_offset       = -$diff_gmt_blog;

            // Add or substract time to all dates, to get GMT dates
            $add_hours   = intval( $diff_gmt_blog );
            $add_minutes = intval( 60 * ($diff_gmt_blog - $add_hours) );
            $wpdb->query( "UPDATE $wpdb->hmwp_ms_intrusions SET created = DATE_ADD(created, INTERVAL '$add_hours:$add_minutes' HOUR_MINUTE)" );
        }

        // Update db version
        $this->set_option( 'db_version', self::DB_VERSION );
	}*/

	/**
	 * Setup options, database table on activation
	 *
	 * @return void
	 */
	public static function activate() {

		global $wpdb;
		self::db_table();

		// Attack attempts database table
        //hassan user_id
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `" . $wpdb->hmwp_ms_intrusions . "` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `value` text NOT NULL,
			  `page` varchar(255) NOT NULL,
			  `tags` varchar(50) NOT NULL,
			  `ip` varchar(16) NOT NULL DEFAULT '0',
			  `user_id` int(11) unsigned NOT NULL,
			  `total_impact` int(11) unsigned NOT NULL,
			  `impact` int(11) unsigned NOT NULL,
		      `origin` varchar(16) NOT null,
			  `created` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
		);
                
                //Create ips attack table
                $wpdb->query(
			"CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "hmwp_blocked_ips` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `ip` varchar(25) NOT NULL,
			  `source` varchar(255) DEFAULT NULL,
			  `allow` tinyint(1) DEFAULT '0',
			  `created` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
		);

        if (is_multisite())
             update_blog_option(BLOG_ID_CURRENT_SITE, 'hmwp_ids_installed', 1);
        else
             update_option('hmwp_ids_installed', 1);


	}

	/**
	 * Clean up on deactivation
	 *
	 * @return void
	 */
	public static function deactivate() {
		global $wpdb;
		$wpdb->query( "DELETE FROM `{$wpdb->usermeta}` WHERE meta_key = 'hmwp_ms_intrusions_per_page'" );
		$wpdb->query( "DELETE FROM `{$wpdb->posts}` WHERE post_type = '".self::POST_TYPE."'" );
	}

	/**
	 * Clean up database on uninstall
	 *
	 * @return void
	 */
	public static function uninstall() {
		global $wpdb;
		self::db_table();

        // Remove intrusions table
        $wpdb->query( 'DROP TABLE IF EXISTS `' . $wpdb->hmwp_ms_intrusions . '`' );

        if (is_multisite())
            delete_blog_option(BLOG_ID_CURRENT_SITE, 'hmwp_ids_installed');
        else
            delete_option('hmwp_ids_installed');
	}

	/**
	 * Add database table references to wpdb
	 *
	 * @param string
	 * @return void
	 */
	public static function db_table() {
		global $wpdb;

		$table_name = self::INTRUSIONS_TABLE;
		$table = $wpdb->get_blog_prefix().$table_name;
		$wpdb->$table_name = $table;
	}

	/**
	 * Get URL path to the plugin directory
	 *
	 * @return string
	 */
	public static function plugin_url() {
		return plugin_dir_url( __FILE__ );
	}
}

// Register activation, deactivation and uninstall hooks,
// run Mute Screamer on init
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	register_activation_hook( __FILE__, 'HMWP_MS_IDS::activate' );
	register_deactivation_hook( __FILE__, 'HMWP_MS_IDS::deactivate' );
	register_uninstall_hook( __FILE__, 'HMWP_MS_IDS::uninstall' );
//after_setup_theme even sooner than plugins_loaded
	//add_action( 'plugins_loaded', create_function( '','new HMWP_MS_IDS();' ), '-100002' );
    new HMWP_MS_IDS();
	//add_action( 'init', create_function( '','new HMWP_MS_IDS();' ), '-10000' );

}

endif;
