<?php
/**
 * Render Pages
 */


class SEED_CSPV5{

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;

	private $comingsoon_rendered = false;
	private $path = null;

	function __construct(){
        
            if(!seed_cspv5_cu('none')){

            $ts = seed_cspv5_get_settings();
            if(!empty($ts) && is_array($ts)){
			 extract($ts);
            }else{
                return false;
            }

            // Landing Page Code


            try{
    			global $wpdb;
                $wpdb->suppress_errors = true;
                $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
                $path = rtrim(ltrim($_SERVER['REQUEST_URI'], '/'),'/');
                $path = preg_replace('/\?.*/', '', $path);



                $url = home_url();

                $r = array_intersect(explode('/',$path),explode('/',$url));

                $path = str_replace($r,'',$path);

                $path = str_replace('/','',$path);

                if(!empty($path)){
                    $sql = "SELECT * FROM $tablename WHERE path = %s";
                    $safe_sql = $wpdb->prepare($sql,$path);
                    $this->path =$wpdb->get_row($safe_sql);

        			if(!empty($this->path)){
                        add_action('init',array( &$this, 'remove_ngg_print_scripts' ));
        			    if(function_exists('bp_is_active')){
                            add_action( 'template_redirect', array(&$this,'render_landing_page'),9);
                        }else{
                            $priority = 10;
                            if(function_exists ('tve_frontend_enqueue_scripts')){
                                $priority = 8;
                            }
                            add_action( 'template_redirect', array(&$this,'render_landing_page'),$priority);
                        }
        			}
                }
            } catch (Exception $e) {
                //echo 'Caught exception: ',  $e->getMessage(), "\n";
            }


            // Actions & Filters if the landing page is active or being previewed
            if(((!empty($status) && $status === '1') || (!empty($status) && $status === '2') || (!empty($status) && $status === '3')) || (isset($_GET['seed_cspv5_preview']))){
            	if(function_exists('bp_is_active')){
                    add_action( 'template_redirect', array(&$this,'render_comingsoon_page'),9);
                }else{
                    $priority = 10;
                    if(function_exists ('tve_frontend_enqueue_scripts')){
                        $priority = 8;
                    }
                    // FreshFramework
                    if(class_exists('ffFrameworkVersionManager')){
                       $priority = 1; 
                    }
                    // Seoframwork
                    if(function_exists('the_seo_framework_pre_load')){
                       $priority = 1; 
                    }
                    // jetpack subscribe
                    if ( isset( $_REQUEST['jetpack_subscriptions_widget'] ) ){
                        $priority = 11; 
                    }
                   
                    add_action( 'template_redirect', array(&$this,'render_comingsoon_page'),$priority );
                }
                add_action('init',array( &$this, 'remove_ngg_print_scripts' ));
                add_action( 'admin_bar_menu',array( &$this, 'admin_bar_menu' ), 1000 );
            }
            }

        // Deactivate License
        add_action( 'init', array(&$this,'deactivate_license'));

        // enable /disable coming soon/maintenanace mode
        add_action( 'init', array(&$this,'csp_mm_api'));


    }

    /**
     * Return an instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    function remove_ngg_print_scripts(){
        if (class_exists('C_Photocrati_Resource_Manager')) {
            remove_all_actions( 'wp_print_footer_scripts', 1 );
        }
    }



    /**
     * Display admin bar when active
     */
    function admin_bar_menu($str){
        global $seed_cspv5_settings,$wp_admin_bar;
        extract($seed_cspv5_settings);

        if(!isset($status)){
            return false;
        }

        // Disable if page line editor open
        if(isset($_GET['pl_edit'])){
            return false;
        }

        $msg = '';
        if($status == '1'){
        	$msg = __('Coming Soon Mode Active','seedprod-coming-soon-pro');
        }elseif($status == '2'){
        	$msg = __('Maintenance Mode Active','seedprod-coming-soon-pro');
        }
        elseif($status == '3' ){
        	$msg = __('Redirect Mode Active','seedprod-coming-soon-pro');
        }

    	//Add the main siteadmin menu item
        $wp_admin_bar->add_menu( array(
            'id'     => 'seed-cspcom-notice',
            'href' => admin_url().'options-general.php?page=seed_cspv5',
            'parent' => 'top-secondary',
            'title'  => $msg,
            'meta'   => array( 'class' => 'seed-cspv5-mode-active' ),
        ) );
    }

    /**
     *  Deactivate License
     */
    function deactivate_license(){
        $token = get_option('seed_cspv5_token');
        $seed_cspv5_api_key = '';
          if(defined('SEED_CSP_API_KEY')){
              $seed_cspv5_api_key = SEED_CSP_API_KEY;
          }
          if(empty($seed_cspv5_api_key)){
              $seed_cspv5_api_key = get_option('seed_cspv5_license_key');
          }

        if(((isset($_REQUEST['seed_cspv5_token']) && $_REQUEST['seed_cspv5_token'] == $token) || isset($_REQUEST['seed_cspv5_token']) && $_REQUEST['seed_cspv5_token'] == $seed_cspv5_api_key) && (isset($_REQUEST['seed_cspv5_action']) && $_REQUEST['seed_cspv5_action'] == 'deactivate')) {
                    $seed_cspv5_per = '';
                    if(!empty($_REQUEST['seed_cspv5_per'])){
                        $seed_cspv5_per = $_REQUEST['seed_cspv5_per'];
                    }

                    $seed_cspv5_api_nag='Site Deactivated';
                    if(!empty($_REQUEST['seed_cspv5_api_nag'])){
                        $seed_cspv5_api_nag = $_REQUEST['seed_cspv5_api_nag'];
                    }

                    update_option('seed_cspv5_api_nag',$seed_cspv5_api_nag);
                    update_option('seed_cspv5_api_message',$seed_cspv5_api_nag);
                    update_option('seed_cspv5_a',false);
                    update_option('seed_cspv5_per',$seed_cspv5_per);
                    update_option('seed_cspv5_license_key','');

                    echo 'true';

            exit();
        }
    }

    /**
     *  coming soon mode/maintence mode api 
     *   mode 0 /disable 1/ coming soon mode 2/maintenance mode
     *  curl http://wordpress.dev/?seed_cspv5_token=4b51fd72-69b7-4796-8d24-f3499c2ec44b&seed_cspv5_mode=1
     */
    function csp_mm_api(){
        $seed_cspv5_api_key = '';
        if(defined('SEED_CSP_API_KEY')){
          $seed_cspv5_api_key = SEED_CSP_API_KEY;
        }
        if(empty($seed_cspv5_api_key)){
          $seed_cspv5_api_key = get_option('seed_cspv5_license_key');
        }
        if(!empty($seed_cspv5_api_key)){
            if(isset($_REQUEST['seed_cspv5_token']) && $_REQUEST['seed_cspv5_token'] == $seed_cspv5_api_key){
             
                if(isset($_REQUEST['seed_cspv5_mode'])){
                    $mode = $_REQUEST['seed_cspv5_mode'];
                    $settings = get_option('seed_cspv5_settings_content');
                    $settings = maybe_unserialize($settings);

                    if(!empty($settings)){
                        if($mode == 0){
                            $settings['status'] = '0';
                            echo '0';
                            update_option('seed_cspv5_settings_content',$settings);
                            exit();

                        }elseif($mode == 1){
                            $settings['status'] = '1';
                            echo '1';
                            update_option('seed_cspv5_settings_content',$settings);
                            exit();

                        }elseif($mode == 2){
                            $settings['status'] = '2';
                            echo '2';
                            update_option('seed_cspv5_settings_content',$settings);
                            exit();

                        }elseif($mode == 3){
                            $settings['status'] = '3';
                            echo '3';
                            update_option('seed_cspv5_settings_content',$settings);
                            exit();

                        }
                    }

                }

            }
        }


    }




    /**
     * Display the default template
     */
    function get_default_template(){
        $file = file_get_contents(SEED_CSPV5_PLUGIN_PATH.'/themes/default/index.php');
        return $file;
    }


    /**
     * Remove theme's style sheets so they do not conflict with the coming soon page
     */
    function deregister_frontend_theme_styles(){
        // remove scripts registered ny the theme so they don't screw up our page's style

               global $wp_styles;
                // list of styles to keep else remove
                $styles = "admin-bar";
                $d = explode("|",$styles);
                $theme = wp_get_theme();
      
      
                //loop styles to see which one's the theme registers
                $remove_these_styles = array('admin-bar');
                foreach($wp_styles->registered as $k=> $v){
                  if(in_array($k,$wp_styles->queue)){
                    // if the src contains the template or stylesheet remove
                    if(strpos($v->src,$theme->stylesheet) !== false){
                      $remove_these_styles[] = $k;
                    }
                    if(strpos($v->src,$theme->template) !== false){
                       if(!in_array($k,$remove_these_styles)){
                        $remove_these_styles[] = $k;
                       }
                       
                    }
                  }
                }
      
                foreach( $wp_styles->queue as $handle ) {
                  //echo '<br> '.$handle;
                    if(!empty($remove_these_styles)){
                        if(in_array($handle,$remove_these_styles)){
                           
                                wp_dequeue_style( $handle );
                                wp_deregister_style( $handle );
                                //echo '<br>removed '.$handle;
                          
                        }
                    }
                }


                add_filter('show_admin_bar', '__return_false');  
               
       
      }
      




    /**
     * Display the landing page
     */
    function render_landing_page() {
            // Prevetn Plugins from caching
            // Disable caching plugins. This should take care of:
            //   - W3 Total Cache
            //   - WP Super Cache
            //   - ZenCache (Previously QuickCache)
            if(!defined('DONOTCACHEPAGE')) {
              define('DONOTCACHEPAGE', true);
            }

            if(!defined('DONOTCDN')) {
              define('DONOTCDN', true);
            }

            if(!defined('DONOTCACHEDB')) {
              define('DONOTCACHEDB', true);
            }

            if(!defined('DONOTMINIFY')) {
              define('DONOTMINIFY', true);
            }

            if(!defined('DONOTCACHEOBJECT')) {
              define('DONOTCACHEOBJECT', true);
            }

            $page_id = $this->path->id;

            // Get Page
            global $wpdb;
            $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
            $sql = "SELECT * FROM $tablename WHERE id= %d and deactivate = 0";
            $safe_sql = $wpdb->prepare($sql,$page_id);
            $page = $wpdb->get_row($safe_sql);
            if(empty($page)){
                return false;
            }

            // Check for base64 encoding of settings
            if ( base64_encode(base64_decode($page->settings, true)) === $page->settings){
                $settings = unserialize(base64_decode($page->settings));
            } else {
                $settings = unserialize($page->settings);
            }



            // Check  for languages
            if(seed_cspv5_cu('ml')){
                $lang_settings_name = 'seed_cspv5_'.$page_id.'_language';
                $lang_settings = get_option($lang_settings_name);
                if(!empty($lang_settings)){
                    $lang_settings = maybe_unserialize($lang_settings);
                    $lang_settings_all = $lang_settings;
                    $langs = array('0'=>$lang_settings['default_lang']['label']);
                    foreach($lang_settings as $k => $v){
                        if(substr( $k, 0, 5 ) === "lang_"){
                            $langs[$k] = $v['label'];
                        }
                    }
                }

                $lang_id = '';
                if(!empty($_GET['lang'])){
                    $lang_id = $_GET['lang'];
                }

                // if(isset($_GET['lang'])){
                //     var_dump($_GET);
                //     die();
                // }

                // Get lang settings
                $lang_settings_name = 'seed_cspv5_'.$page_id.'_language_'.$lang_id;
                $lang_settings = get_option($lang_settings_name);
                if(!empty($lang_settings)){
                    $lang_settings = maybe_unserialize($lang_settings);
                }



                if(!empty($lang_id) && !empty($lang_settings)){
                    $settings = array_merge($settings, $lang_settings);
                }
            }


            //If Referrer record it
            if(isset($_GET['ref'])){
                $id = intval($_GET['ref'],36)-1000;

                global $wpdb;
                $tablename = $wpdb->prefix . SEED_CSPV5_SUBSCRIBERS_TABLENAME;
                $sql = "UPDATE $tablename SET clicks = clicks + 1 WHERE id = %d";
                $safe_sql = $wpdb->prepare($sql,$id);
                $update_result =$wpdb->get_var($safe_sql);
            }

            // check if 3rd party plugins is enabled
            if(!empty($settings['enable_wp_head_footer'])){
                add_action( 'wp_enqueue_scripts', array(&$this,'deregister_frontend_theme_styles'), PHP_INT_MAX );
            }

            header("HTTP/1.1 200 OK");
            header('Cache-Control: max-age=0, private');

            // render
            $upload_dir = wp_upload_dir();
            if(is_multisite()){
                $path = $upload_dir['baseurl'].'/seedprod/'.get_current_blog_id().'/template-'.$page_id.'/index.php';
            }else{
                $path = $upload_dir['basedir'].'/seedprod/template-'.$page_id.'/index.php';
            }


            if(!empty($page->html)){
                echo $page->html;
            }else{

                if(file_exists($path)){
                    require_once($path);
                }else{
                    require_once(SEED_CSPV5_PLUGIN_PATH.'template/index.php');
                }
            }

            exit();
    }


    /**
     * Display the coming soon page
     */
    function render_comingsoon_page() {
      
        //var_dump('coming soon');

        // Setting
        $plugin_settings = seed_cspv5_get_settings();
        extract($plugin_settings);

        // Page Info
        $page_id = 0;
        if(isset($_GET['seed_cspv5_preview'])){
            $page_id = $_GET['seed_cspv5_preview'];
        }else{
            //Get Coming Soon Page Id
            $page_id = get_option('seed_cspv5_coming_soon_page_id');
        }

        // Get Page
        global $wpdb;
        $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
        $sql = "SELECT * FROM $tablename WHERE id= %d";
        $safe_sql = $wpdb->prepare($sql,$page_id);
        $page = $wpdb->get_row($safe_sql);

        // Check for base64 encoding of settings
        if ( base64_encode(base64_decode($page->settings, true)) === $page->settings){
            $settings = unserialize(base64_decode($page->settings));
        } else {
            $settings = unserialize($page->settings);
        }


        // Check  for languages
        if(seed_cspv5_cu('ml')){
            $lang_settings_name = 'seed_cspv5_'.$page_id.'_language';
            $lang_settings = get_option($lang_settings_name);
            if(!empty($lang_settings)){
                $lang_settings = maybe_unserialize($lang_settings);
                $lang_settings_all = $lang_settings;
                $langs = array('0'=>$lang_settings['default_lang']['label']);
                foreach($lang_settings as $k => $v){
                    if(substr( $k, 0, 5 ) === "lang_"){
                        $langs[$k] = $v['label'];
                    }
                }
            }

            $lang_id = '';
            if(!empty($_GET['lang'])){
                $lang_id = $_GET['lang'];
            }

            // if(isset($_GET['lang'])){
            //     var_dump($_GET);
            //     die();
            // }

            // Get lang settings
            $lang_settings_name = 'seed_cspv5_'.$page_id.'_language_'.$lang_id;
            $lang_settings = get_option($lang_settings_name);
            if(!empty($lang_settings)){
                $lang_settings = maybe_unserialize($lang_settings);
            }

            if(!empty($lang_id) && !empty($lang_settings)){
                $settings = array_merge($settings, $lang_settings);
            }
        }


        // if(!isset($status)){
        //     $err =  new WP_Error('error', __("Please enter your settings.", 'seedprod-coming-soon-pro'));
        //     echo $err->get_error_message();
        //     exit();
        // }


        // Check if Preview
        $is_preview = false;
        if ((isset($_GET['seed_cspv5_preview']))) {
            //show_admin_bar( false );
            $is_preview = true;
        }

        // Die if preview and redirect mode
        if($status == '3' && $is_preview == true){
            $status = '1';
        }

        // Countdown Launch
        if($is_preview == false){

            if(!empty($settings['countdown_date']) && !empty($settings['enable_countdown']) && !empty($settings['countdown_launch'])){

                $date = new DateTime($settings['countdown_date'], new DateTimeZone($settings['countdown_timezone']));
                $timestamp = $date->format('U');
                 // var_dump($timestamp);
                 // var_dump(time());

                // Launch this biatch
                if($timestamp <= time()){
                    // Email the admin the site has been launched
                    $message = __(sprintf('%s has been launched.',home_url()), 'seedprod');
                    $result = wp_mail( get_option('admin_email'), __(sprintf('%s has been launched.',home_url()), 'seedprod'), $message);

                    $o = get_option('seed_cspv5_settings_content');
                    //var_dump($o);
                    $o['status'] = 0;
                    update_option('seed_cspv5_settings_content', $o);
                    return false;

                }
            }
        }

        //If Referrer record it
        if(isset($_GET['ref'])){
            $id = intval($_GET['ref'],36)-1000;

            global $wpdb;
            $tablename = $wpdb->prefix . SEED_CSPV5_SUBSCRIBERS_TABLENAME;
            $sql = "UPDATE $tablename SET clicks = clicks + 1 WHERE id = %d";
            $safe_sql = $wpdb->prepare($sql,$id);
            $update_result =$wpdb->get_var($safe_sql);
        }

        // Exit if feed and feedburner is enabled.
        if(is_feed() && !empty($emaillist) && $emaillist == 'feedburner'  ){
            return false;
        }

        //Bypass code
        if(empty($_GET['bypass'])){
            $_GET['bypass'] = false;
        }

        if(empty($alt_bypass)){
           $alt_bypass = false;
        }


        if ( is_multisite() ||  $alt_bypass){

            // Multisite Clientview
            if(empty($_GET['bypass'])){
                $_GET['bypass'] = false;
            }

            if($is_preview == false){
            //Check for Client View
            if ( isset($_COOKIE['wp-client-view']) && ((strtolower(basename($_SERVER['REQUEST_URI'])) == trim(strtolower($client_view_url))) || (strtolower($_GET['bypass']) == trim(strtolower($client_view_url))) ) && !empty($client_view_url)) {

  			if(!empty($_REQUEST['return'])){
                        nocache_headers();
                        header('Cache-Control: max-age=0, private');
	                    header( 'Location: '.urldecode($_REQUEST['return']) ) ;
	                    exit;
	                }else{
                        nocache_headers();
                        header('Cache-Control: max-age=0, private');
	                    header( 'Location: '.home_url().'?'.rand() ) ;
	                    exit;
	                }
            }

            // Don't show Coming Soon Page if client View is active
            $client_view_hash = md5($client_view_url . get_current_blog_id());
            if (isset($_COOKIE['wp-client-view']) && $_COOKIE['wp-client-view'] == $client_view_hash  && !empty($client_view_url)) {
                nocache_headers();
                header('Cache-Control: max-age=0, private');
                return false;
            }else{
                nocache_headers();
                header('Cache-Control: max-age=0, private');
                setcookie("wp-client-view", "", time()-3600);

            }



            // If Client view is not empty and we are on the client view url set cookie.
            if(!empty($client_view_url)){
                if(empty($_GET['bypass'])){
                    $_GET['bypass'] = '';
                }

                if((strtolower(basename($_SERVER['REQUEST_URI'])) == trim(strtolower($client_view_url))) || (strtolower($_GET['bypass']) == trim(strtolower($client_view_url)))) {
                    if(!empty($bypass_expires)){
                        $exipres_in = time()+ (3600 * $bypass_expires);
                    }else{
                        $exipres_in = time()+172800;
                    }


                    setcookie("wp-client-view", $client_view_hash , $exipres_in , COOKIEPATH, COOKIE_DOMAIN, false);


	                if(!empty($_REQUEST['return'])){
                        nocache_headers();
                        header('Cache-Control: max-age=0, private');
	                    header( 'Location: '.urldecode($_REQUEST['return']) ) ;
	                    exit;
	                }else{
                        nocache_headers();
                        header('Cache-Control: max-age=0, private');
	                    header( 'Location: '.home_url().'?'.rand() ) ;
	                    exit;
	                }
                }
            }
        }
        

        }else{


        // ClientView
        if(!empty($client_view_url)){


            if(empty($_GET['bypass'])){
                $_GET['bypass'] = '';
            }

            // If client view url is passed in log user in
            if((strtolower(basename($_SERVER['REQUEST_URI'])) == trim(strtolower($client_view_url))) || (strtolower($_GET['bypass']) == trim(strtolower($client_view_url)))) {


                if(!username_exists('seed_cspv5_clientview_'.$client_view_url)){
                    $user_id = wp_create_user('seed_cspv5_clientview_'.$client_view_url,wp_generate_password());
                    $user = new WP_User($user_id);
                    $user->set_role('none');
                }


                if(!empty($bypass_expires)){
                    global $seed_cspv5_bypass_expires;
                    $seed_cspv5_bypass_expires = (3600 * $bypass_expires);
                }

                $client_view_hash = md5($client_view_url . get_current_blog_id());
                setcookie("wp-client-view", $client_view_hash , 0 , COOKIEPATH, COOKIE_DOMAIN, false);


                add_filter( 'auth_cookie_expiration', 'seed_cspv5_change_wp_cookie_logout');

                // Log user in auto
                $username = 'seed_cspv5_clientview_'.$client_view_url;
                if ( !is_user_logged_in() ) {

                    $user = get_user_by( 'login', $username );
                    $user_id = $user->ID;
                    wp_set_current_user( $user_id, $username );
                    wp_set_auth_cookie( $user_id );
                    do_action( 'wp_login', $username, $user );
                    update_user_meta($user_id, 'show_admin_bar_front', false);

                }

                



                if(!empty($_REQUEST['return'])){
                    nocache_headers();
                    header('Cache-Control: max-age=0, private');
                    header( 'Location: '.urldecode($_REQUEST['return']) ) ;
                    exit;
                }else{
                    nocache_headers();
                    header('Cache-Control: max-age=0, private');
                    header( 'Location: '.home_url().'?'.rand() ) ;
                    exit;
                }


            }
        }
        }

        // Check for excluded IP's
        if($is_preview == false){
            if(!empty($ip_access)){
                $ip = seed_cspv5_get_ip();
                $exclude_ips = explode("\r\n",$ip_access);
                if(is_array($exclude_ips) && in_array($ip,$exclude_ips)){
                    return false;
                }
            }
        }


        if($is_preview == false){
        if(!empty($include_exclude_options) && $include_exclude_options == '2'){
        if(substr($include_url_pattern, 0, 3) != '>>>'){

         // Check for included pages
            if(!empty($include_url_pattern)){
                //$url = preg_replace('/\?ref=\d*/','',$_SERVER['REQUEST_URI']);
                // TODO lok for when wordpress is in sub folder
                $request_uri = explode('?',$_SERVER['REQUEST_URI']);
                $url = rtrim(ltrim($request_uri[0], '/'),'/');

                $r = array_intersect(explode('/',$url),explode('/',home_url()));

                $url = str_replace($r,'',$url);

                $url = str_replace('/','',$url);
                //var_dump($url);

                $include_urls = explode("\r\n",$include_url_pattern);
                $include_urls = array_filter($include_urls);
                $include_urls = str_replace(home_url(),"",$include_urls);
                $include_urls = str_replace('/','',$include_urls);
                //$include_urls = array_filter($include_urls);
                //var_dump($include_urls);
                //var_dump($url);
                $post_id = '';
                global $post;
                //var_dump($post->ID);
                if(!empty($post->ID)){
                    $post_id = $post->ID;
                }

                $show_coming_soon_page = false;

                if(is_array($include_urls) && (in_array($url,$include_urls) || in_array($post_id,$include_urls))){
                    $show_coming_soon_page = true;
                }

                // check wildcard urls
                $urls_to_test = $include_urls;
                $urls_to_test = str_replace(home_url(),"",$urls_to_test);
                $url_uri = $_SERVER['REQUEST_URI'];
                foreach($urls_to_test as $url_to_test){
                    if(strpos($url_to_test, '*') !== false){
                        // Wildcard url
                        $url_to_test = str_replace("*","",$url_to_test);
                        if(strpos($url_uri, untrailingslashit($url_to_test)) !== false){
                            $show_coming_soon_page = true;
                        }
                    }
                }

                if($show_coming_soon_page === false){
                    return false;
                }


            }
        }else{
            // Check for included pages regex
            $include_url_pattern = substr($include_url_pattern, 3);
            if(!empty($include_url_pattern) && @preg_match("/{$include_url_pattern}/",$_SERVER['REQUEST_URI']) == 0){
                return false;
            }
        }
        }

        // Check for excludes pages
        if(!empty($include_exclude_options) && $include_exclude_options == '3'){
        if(substr($exclude_url_pattern, 0, 3) != '>>>'){
            if(!empty($exclude_url_pattern)){
                //$url = preg_replace('/\?ref=\d*/','',$_SERVER['REQUEST_URI']);
                $request_uri = explode('?',$_SERVER['REQUEST_URI']);
                $url = rtrim(ltrim($request_uri[0], '/'),'/');

                $r = array_intersect(explode('/',$url),explode('/',home_url()));

                $url = str_replace($r,'',$url);

                $url = str_replace('/','',$url);
                //var_dump($url);

                $exclude_urls = explode("\r\n",$exclude_url_pattern);
                $exclude_urls = array_filter($exclude_urls);
                $exclude_urls = str_replace(home_url(),"",$exclude_urls);
                $exclude_urls = str_replace('/','',$exclude_urls);
                //$exclude_urls = array_filter($exclude_urls);
                $post_id = '';
                global $post;
                //var_dump($post->ID);
                if(!empty($post->ID)){
                   $post_id = $post->ID; 
                }
                
                // check exact urls
                if(is_array($exclude_urls) && (in_array($url,$exclude_urls) || in_array($post_id,$exclude_urls))){
                    return false;
                }

                // check wildcard urls
                $urls_to_test = $exclude_urls;
                $urls_to_test = str_replace(home_url(),"",$urls_to_test);
                $url_uri = $_SERVER['REQUEST_URI'];
                foreach($urls_to_test as $url_to_test){
                    if(strpos($url_to_test, '*') !== false){
                        // Wildcard url
                        $url_to_test = str_replace("*","",$url_to_test);
                        if(strpos($url_uri, untrailingslashit($url_to_test)) !== false){
                            return false;
                        }
                    }
                }

                // Check for affiliateWP
                if(class_exists( 'Affiliate_WP' ) && (strpos($url, 'ref') !== false)){
                    return false;
                }
            }


        }else{

            // Check for excluded pages
            $exclude_url_pattern = substr($exclude_url_pattern, 3);
            if(!empty($exclude_url_pattern) && @preg_match("/{$exclude_url_pattern}/",$_SERVER['REQUEST_URI']) > 0){
               return false;
            }
        }
        }
        }


        // Only show the Coming Soon Page on the home page
        if(!empty($include_exclude_options) && $include_exclude_options == '1' && $is_preview == false){
                if($_SERVER['REQUEST_URI'] == "/" || substr($_SERVER['REQUEST_URI'], 0, 2) == '/?'){

                }else{
                   return false; 
                }     
        }



        // Check if redirect url and exclude
        if($status == '3' && !empty($redirect_url)){
            $r_url = parse_url($redirect_url);
            if($r_url['host'] == $_SERVER['HTTP_HOST'] && $r_url['path'] == $_SERVER['REQUEST_URI'] && $is_preview == false){
                return false;
            }
        }

        // Exit if a custom login page
        if(empty($disable_default_excluded_urls)){
            if(preg_match("/login|admin|dashboard|account/i",$_SERVER['REQUEST_URI']) > 0 && $is_preview == false){
                return false;
            }
        }


        //Exit if wysija double opt-in
        if(isset($emaillist) &&  $emaillist == 'wysija' && preg_match("/wysija/i",$_SERVER['REQUEST_URI']) > 0 && $is_preview == false){
            return false;
        }

        if(isset($emaillist) &&  $emaillist == 'mailpoet' && preg_match("/mailpoet/i",$_SERVER['REQUEST_URI']) > 0 && $is_preview == false){
            return false;
        }

        if(isset($emaillist) &&  $emaillist == 'mymail' && preg_match("/confirm/i",$_SERVER['REQUEST_URI']) > 0 && $is_preview == false){
            return false;
        }



        //Limit access by role


            if($is_preview === false){
                if(!empty($include_roles) && !isset($_COOKIE['wp-client-view'])){
                    foreach($include_roles as $v){
                        if($v == 'anyone' && is_user_logged_in()){
                            return false;
                        }
                        if(current_user_can($v)){
                            return false;
                        }
                    }
                }elseif(is_user_logged_in()){
                    return false;
                }
            }

        // check if 3rd party plugins is enabled
        if(!empty($settings['enable_wp_head_footer'])){
            add_action( 'wp_enqueue_scripts', array(&$this,'deregister_frontend_theme_styles'), PHP_INT_MAX );
        }



        // Finally check if we should show the coming soon page.
        $this->comingsoon_rendered = true;

        // set headers
        if($status == '2' && $is_preview == false){
            if(empty($settings)){
                echo __( "Please create your Maintenance Page in the plugin settings.", "seedprod-coming-soon-pro" );
                exit();
            }
            header('HTTP/1.1 503 Service Temporarily Unavailable');
            header('Status: 503 Service Temporarily Unavailable');
            header('Retry-After: 86400'); // retry in a day
            $seed_cspv5_maintenance_file = WP_CONTENT_DIR."/maintenance.php";
            if(!empty($enable_maintenance_php) and file_exists($seed_cspv5_maintenance_file)){
                include_once( $seed_cspv5_maintenance_file );
                exit();
            }
        }elseif($status == '3'){
            if(!empty($redirect_url)){
                wp_redirect( $redirect_url );
                exit;
            }else{
                echo __( "Please create enter your redirect url in the plugin settings.", "seedprod-coming-soon-pro" );
                exit();
            }
        }else{
            if(empty($settings)){
                echo __( "Please create your Coming Soon Page in the plugin settings.", "seedprod-coming-soon-pro" );
                exit();
            }
            header("HTTP/1.1 200 OK");
            header('Cache-Control: max-age=0, private');
        }

        if(is_feed()){
            header('Content-Type: text/html; charset=UTF-8');
        }





       
        // Prevetn Plugins from caching
        // Disable caching plugins. This should take care of:
        //   - W3 Total Cache
        //   - WP Super Cache
        //   - ZenCache (Previously QuickCache)
        if(!defined('DONOTCACHEPAGE')) {
          define('DONOTCACHEPAGE', true);
        }

        if(!defined('DONOTCDN')) {
          define('DONOTCDN', true);
        }

        if(!defined('DONOTCACHEDB')) {
          define('DONOTCACHEDB', true);
        }

        if(!defined('DONOTMINIFY')) {
          define('DONOTMINIFY', true);
        }

        if(!defined('DONOTCACHEOBJECT')) {
          define('DONOTCACHEOBJECT', true);
        }

       

        // render
        $upload_dir = wp_upload_dir();
       	if(is_multisite()){
			$path = $upload_dir['baseurl'].'/seedprod/'.get_current_blog_id().'/template-'.$page_id.'/index.php';
		}else{
			$path = $upload_dir['basedir'].'/seedprod/template-'.$page_id.'/index.php';
		}

        if(!empty($page->html)){
            echo $page->html;
        }else{

    		if(file_exists($path)){
    		    require_once($path);
    		}else{
    		    require_once(SEED_CSPV5_PLUGIN_PATH.'template/index.php');
    		}
        }

        exit();

    }

}
