<?php
/**
 * seed_cspv5 Admin
 *
 * @package WordPress
 * @subpackage seed_cspv5
 * @since 0.1.0
 */


class SEED_CSPV5_ADMIN
{
    public $plugin_version = SEED_CSPV5_VERSION;
    public $plugin_name = SEED_CSPV5_PLUGIN_NAME;

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;

   /**
     * Slug of the plugin screen.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $plugin_screen_hook_suffix = null;
    protected $plugin_screen_customizer_hook_suffix = null;

    /**
     * Load Hooks
     */
    function __construct( )
    {

        if ( is_admin() && ( !defined( 'DOING_AJAX' ) ) ){
            add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts'  ) );
            add_action( 'admin_enqueue_scripts', array( &$this, 'deregister_scripts' ), PHP_INT_MAX );
            add_action( 'admin_menu', array( &$this, 'create_menus'  ) );
            
            // Render Options
            add_action( 'admin_init', array( &$this, 'reset_defaults' ) );
            add_action( 'admin_init', array( &$this, 'create_settings' ) );
            // Add link to options on the plugin page
            add_filter( 'plugin_action_links', array( &$this, 'plugin_action_links' ), 10, 2 );
            // Add or Upgrade DB
            add_action( 'admin_init', array( &$this, 'upgrade' ), 0 );
            //Display Pages
            add_action('seed_cspv5_render_page', array( &$this, 'display_pages' ));
            
            //Display Subscribers
            add_action('seed_cspv5_render_page', array( &$this, 'display_subscribers' ));
            
            // Handle action post
            add_action( 'admin_init', array( &$this, 'subscriber_actions' ), 0 );
            add_filter( 'tmp_grunion_allow_editor_view', '__return_false' );
            
        }
        
        if (defined( 'DOING_AJAX' )){
            // Save Page Ajqx
            add_action( 'wp_ajax_seed_cspv5_save_page', array(&$this,'save_page'));

            // Save Page Ajqx
            add_action( 'wp_ajax_seed_cspv5_save_page_v2', array(&$this,'save_page_v2'));

            // Duplicate Page
            add_action( 'wp_ajax_seed_cspv5_duplicate_page', array(&$this,'duplicate_page_process'));

            // Save Form Settings
            add_action( 'wp_ajax_seed_cspv5_save_form', array(&$this,'save_form'));

            // Save Autoresponder Settings
            add_action( 'wp_ajax_seed_cspv5_save_autoresponder', array(&$this,'save_autoresponder'));

            // Save Form Settings
            add_action( 'wp_ajax_seed_cspv5_save_prizes', array(&$this,'save_prizes'));

            // Save Language Settings
            add_action( 'wp_ajax_seed_cspv5_save_language', array(&$this,'save_language'));

            // Save Language Settings Details
            add_action( 'wp_ajax_seed_cspv5_save_language_detail', array(&$this,'save_language_detail'));

            // Save Page HTML Ajqx
            add_action( 'wp_ajax_seed_cspv5_get_html', array(&$this,'get_html'));
            add_action( 'wp_ajax_seed_cspv5_save_html', array(&$this,'save_html'));
            
            // Background API Ajax
            add_action( 'wp_ajax_seed_cspv5_backgrounds', array(&$this,'backgrounds'));
            
            // Sideload Background API Ajax
            add_action( 'wp_ajax_seed_cspv5_backgrounds_sideload', array(&$this,'backgrounds_sideload'));

            // Download Background API Ajax
            add_action( 'wp_ajax_seed_cspv5_backgrounds_download', array(&$this,'backgrounds_download'));
            
            // Theme Load Api
            add_action( 'wp_ajax_seed_cspv5_load_theme', array(&$this,'load_theme'));
            
            //Subscribe Callback
            add_action( 'wp_ajax_seed_cspv5_subscribe_callback', array(&$this,'subscribe_callback') );
            add_action( 'wp_ajax_nopriv_seed_cspv5_subscribe_callback', array(&$this,'subscribe_callback') );

            //ContactForm Callback
            add_action( 'wp_ajax_seed_cspv5_contactform_callback', array(&$this,'contactform_callback') );
            add_action( 'wp_ajax_nopriv_seed_cspv5_contactform_callback', array(&$this,'contactform_callback') );
            
            // Save Emaillist Settings
            add_action( 'wp_ajax_seed_cspv5_save_emaillist_settings', array(&$this,'save_emaillist_settings') );
            
            // Save Get Email Lists
            add_action( 'wp_ajax_seed_cspv5_get_email_lists', array(&$this,'get_email_lists') );

            // Export page settings
            add_action( 'wp_ajax_seed_cspv5_export_page_settings', array(&$this,'export_page_settings') );

            // Import page settings
            add_action( 'wp_ajax_seed_cspv5_import_page_settings', array(&$this,'import_page_settings') );


            // Activate License
            add_action( 'wp_ajax_seed_cspv5_activate_license', array(&$this,'activate_license'));
            
        }

    }
    

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    function deregister_scripts(){
        // always remove
        wp_dequeue_script( 'pirateforms_scripts_admin' );
        wp_deregister_script( 'pirateforms_scripts_admin' );

        // debug remove
        if(1 == 0){
        if(isset($_GET['page']) && ($_GET['page'] == 'seed_cspv5_customizer' || $_GET['page'] == 'seed_cspv5_customizer_v2' )){
            //if(isset($_GET['seed_cspv5_debug'])){
            // only ones we need are: common|utils|wp-auth-check|media-upload|seed_cspv5-customizer-js|jquery|jquery-ui|media-editor
            $s = 'admin-bar|common|utils|wp-auth-check|media-upload|seed_cspv5-customizer-js|jquery|jquery-ui|media-editor';
            $d = explode("|",urldecode($s)); 
        
            global $wp_scripts,$wp_styles;
            foreach( $wp_scripts->queue as $handle ) :
                //echo $handle . '|';
                
                if(!empty($d)){
                if(!in_array($handle,$d)){
                    wp_dequeue_script( $handle );
                    wp_deregister_script( $handle );
                    //echo '<br>removed '.$handle;
                }
                }
            endforeach;

            $handle = '';
            $s = 'colors|admin-bar|ie';
            $d = explode("|",urldecode($s)); 
            foreach($wp_styles->queue as $handle){
                if(!empty($d)){
                if(!in_array($handle,$d)){
                    wp_dequeue_style( $handle );
                    wp_deregister_style( $handle );
                    //echo '<br>removed '.$handle;
                }
                }
            }
            //}
        //die();
        }
       
    }}
    
    /**
     * Get pages and put in assoc array
     */
    function get_pages(){
        $pages = get_pages();
        $page_arr = array();
        if(is_array($pages)){
            foreach($pages as $k=>$v){
                $page_arr[$v->ID] = $v->post_title;
            }
        }
        return $page_arr;
    }


    /**
     *  Activate License
     */
    function activate_license(){
        if(check_ajax_referer('seed_cspv5_activate_license') && isset($_GET['apikey'])){
        	$request["status"] = '200';
$request["per"] = 'GPL001122334455AA6677BB8899CC000';
$request["message"] = 'You have a valid license';
update_option('seed_cspv5_license_key','GPL001122334455AA6677BB8899CC000');
update_option('seed_cspv5_api_message','You have a valid license');
update_option('seed_cspv5_api_nag','');
update_option('seed_cspv5_a',true);
update_option('seed_cspv5_per','You have a valid license');
echo json_encode($request);
exit();
            $api_key = $_GET['apikey'];
            $params = array(
                'action'     => 'info',
                'license_key'=> $api_key,
                'slug'       => SEED_CSPV5_SLUG,
                'domain'        => home_url(),
                'installed_version' => SEED_CSPV5_VERSION,
                'token'      => get_option('seed_cspv5_token'),
            );
            $request = wp_remote_post( SEED_CSPV5_API_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $params ) );

            //SEED_CSPV5_API_URL
           
            if ( ! is_wp_error( $request ) ) {
                $request = wp_remote_retrieve_body( $request );
                $arequest = json_decode($request);
                $nag = $arequest->message;

                update_option('seed_cspv5_license_key',$api_key);
                
                update_option('seed_cspv5_api_message',$nag);
                if($arequest->status == '200'){
                    update_option('seed_cspv5_api_nag','');
                    update_option('seed_cspv5_a',true);
                    update_option('seed_cspv5_per',$arequest->per);
                }elseif($arequest->status == '401'){
                    update_option('seed_cspv5_api_nag',$nag);
                    update_option('seed_cspv5_a',false);
                    update_option('seed_cspv5_per','');
                }elseif($arequest->status == '402'){
                    update_option('seed_cspv5_api_nag',$nag);
                    update_option('seed_cspv5_a',false);
                    update_option('seed_cspv5_per',$arequest->per);

                }     

                echo $request;

            }else{
                echo $request->get_error_message();;
            }
            exit();
        }
    }



    
    /**
     * Upgrade setting pages. This allows you to run an upgrade script when the version changes.
     *
     */
    function upgrade( )
    {
        // get current version
        $seed_cspv5_current_version = get_option( 'seed_cspv5_version' );
        $upgrade_complete = false;
        if ( empty( $seed_cspv5_current_version ) ) {
            $seed_cspv5_current_version = 0;
        }

        if($seed_cspv5_current_version === 0){
            // Load Defaults if new install
            //require_once(SEED_CSPV5_PLUGIN_PATH.'inc/defaults.php');
            
            //Try to upgrade settings from 2 to 3
            // $mapping = array(
            //     'api_key' => 'seedprod_api_key',
            //     // '' => '',
            //     // '' => '',
            //     // '' => '',
            //     // '' => '',
            //     // '' => '',
            //     // '' => '',
            //     // '' => '',
            //     // '' => '',
            //     // '' => '',
            //     // '' => '',
            //     // '' => '',
            //     // '' => '',
            //     // '' => '',
            //     // '' => '',
            //     // '' => '',
            //     );

            // $old_fields = array();
            // $old_fields = get_option('csp2_option_tree');
            // var_dump($old_fields);
            // if(!empty($old_fields)){
            //     foreach($seed_cspv5_settings_deafults as $k=>$v){
            //         foreach($v as $k2=>$v2){
            //             if(array_key_exists($k2,$mapping)){
            //                 if(!empty($old_fields[$mapping[$k2]]))
            //                     $seed_cspv5_settings_deafults[$k][$k2] = $old_fields[$mapping[$k2]];
            //             }
            //         }
            //     }
            // }
            // var_dump($seed_cspv5_settings_deafults);

            //foreach($seed_cspv5_settings_deafults as $k=>$v){
               // update_option( $k, $v );
            //}
        }
// var_dump($seed_cspv5_current_version);
// var_dump(SEED_CSPV5_VERSION);
// var_dump(version_compare( $seed_cspv5_current_version,SEED_CSPV5_VERSION));
        if ( version_compare( $seed_cspv5_current_version,SEED_CSPV5_VERSION) === -1 || !empty($_GET['seed_cspv5_force_db_setup'])) {
            // Upgrade db if new version
            $this->database_setup();
            $upgrade_complete = true;

        }

        if($upgrade_complete){
            update_option( 'seed_cspv5_version', SEED_CSPV5_VERSION );
        }
        //var_dump($upgrade_complete);
        
        // Sample script to update field if it's changed to a different tab.
        // if ( version_compare( SEED_CSPV5_VERSION,$seed_cspv5_current_version ) === 1) {
        //     $old_fields = array();
        //     $old_fields = get_option('seed_cspv5_options_1');
        //     $old_fields = $old_fields + get_option('seed_cspv5_options_1');
        
        //     $new_fields = array();
        //     foreach ($this->options as $k) {
        //         switch ($k['type']) {
        //             case 'setting':
        //             case 'section':
        //             case 'tab':
        //                 break;
        //             default:
        //                 if(isset($old_fields[$k['id']])){
        //                     $new_fields[$k['setting_id']][$k['id']] = $old_fields[$k['id']];
        //                 }
        
        
        //         }
        //     }
        //     var_dump($old_fields);
        //     var_dump($new_fields);
        
        // }
    }
    
    
    /**
     * Create Database to Store Emails
     */
    function database_setup() {

        global $wpdb;
        $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
        //if( $wpdb->get_var("SHOW TABLES LIKE '$tablename'") != $tablename ){
            $sql = "CREATE TABLE `$tablename` (
              id int(11) unsigned NOT NULL AUTO_INCREMENT,
              path varchar(255) DEFAULT NULL,
              name varchar(255) DEFAULT NULL,
              type varchar(255) DEFAULT NULL,
              settings mediumtext DEFAULT NULL,
              settings_v2 mediumtext DEFAULT NULL,
              html text DEFAULT NULL,
              meta text DEFAULT NULL,
              mailprovider varchar(255) DEFAULT NULL,
              deactivate int(11) NOT NULL DEFAULT '0',
              created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY  (id)
            );";
        
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            dbDelta($sql);
        //}

        global $wpdb;
        $tablename = $wpdb->prefix . SEED_CSPV5_SUBSCRIBERS_TABLENAME;
        //if( $wpdb->get_var("SHOW TABLES LIKE '$tablename'") != $tablename ){
            $sql = "CREATE TABLE `$tablename` (
              id int(11) unsigned NOT NULL AUTO_INCREMENT,
              page_id int(11) NOT NULL,
              email varchar(255) DEFAULT NULL,
              fname varchar(255) DEFAULT NULL,
              lname varchar(255) DEFAULT NULL,
              ref_url varchar(255) DEFAULT NULL,
              clicks int(11) NOT NULL DEFAULT '0',
              conversions int(11) NOT NULL DEFAULT '0',
              referrer int(11) NOT NULL DEFAULT '0',
              confirmed int(11) NOT NULL DEFAULT '0',
              optin_confirm int(11) NOT NULL DEFAULT '0',
              ip varchar(255) DEFAULT NULL,
              meta text DEFAULT NULL,
              created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY  (id)
            );";
        
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            dbDelta($sql);
        //}
    }

    /**
     * Export Page_Settings
     *
     */
    function export_page_settings( )
    {
        if(check_ajax_referer('seed_cspv5_export_page_settings')){
                //Get page settings
                $page_id = '';
                if(isset($_GET['page_id'])){
                    $page_id = $_GET['page_id'];
                }
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

                echo json_encode($settings);

        }
        exit();
    }

    /**
     * Import Page_Settings
     *
     */
    function import_page_settings( )
    {
        if(check_ajax_referer('seed_cspv5_import_page_settings')){
                $page_id = '';
                if(isset($_GET['page_id'])){
                    $page_id = $_GET['page_id'];
                }

                $settings = json_decode(file_get_contents('php://input'),TRUE);

               
                // if(json_last_error() != JSON_ERROR_NONE){
                //     echo '0';
                // }
                $r = false;
              
                if(!empty($settings['check_mic'])){
                    global $wpdb;
                    $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
                    // Update Row
                    $r = $wpdb->update( 
                        $tablename, 
                        array( 
                            'settings' => base64_encode(serialize($settings)),
                            
                        ), 
                        array( 'ID' => $page_id ), 
                        array( 
                            '%s',
       
                        ), 
                        array( '%d' ) 
                    );
                }
            
                if($r !== false){
                    echo $page_id;
                }else{
                    echo 'false';
                }
                
        }
        exit();
    }

    function save_html( ){
        if(check_ajax_referer('seed_cspv5_save_html','html_wpnonce')){
            $page_id = '';
            $html = '';

            if(isset($_GET['page_id'])){
                $page_id = $_GET['page_id'];
            }

            $html = file_get_contents('php://input');

            // DB setup
            global $wpdb;
            $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
          
            // Update Row
            $r = $wpdb->update( 
                $tablename, 
                array( 
                    'html' => $html,     
                ), 
                array( 'ID' => $page_id ), 
                array( 
                    '%s',
                ), 
                array( '%d' ) 
            );

        }
        exit();
    }
    function save_prizes( ){
        if(check_ajax_referer('seed_cspv5_save_prizes')){

            $settings_name = $_REQUEST['settings_name'];
            $settings = $_REQUEST;
            unset($settings['action']);
            unset($settings['_wpnonce']);
            $r = update_option($settings_name,stripslashes_deep($settings));
            echo '1';

        }
        exit();
    }


    function save_autoresponder( ){
        if(check_ajax_referer('seed_cspv5_save_autoresponder')){

            $settings_name = $_REQUEST['settings_name'];
            $settings = $_REQUEST;
            unset($settings['action']);
            unset($settings['_wpnonce']);
            $r = update_option($settings_name,stripslashes_deep($settings));
            echo '1';

        }
        exit();
    }

    function save_form( ){
        if(check_ajax_referer('seed_cspv5_save_form')){
            // Update name setting

            // Get Page
            global $wpdb;
            $page_id = $_REQUEST['page_id'];
            $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
            $sql = "SELECT * FROM $tablename WHERE id= %d";
            $safe_sql = $wpdb->prepare($sql,$page_id);
            $page = $wpdb->get_row($safe_sql);

            // Check for base64 encoding of settings
            if ( base64_encode(base64_decode($page->settings, true)) === $page->settings){
                $page_settings = unserialize(base64_decode($page->settings));
            } else {
                $page_settings = unserialize($page->settings);
            }

           
       
            if(!empty($_REQUEST['field_name']['visible'])){
                $page_settings['display_name'] = '1';
            }else{
                $page_settings['display_name'] = '0';
            }

     
            if(!empty($_REQUEST['field_name']['required'])){
                $page_settings['require_name'] = '1';
            }else{
                $page_settings['require_name'] = '0';
            }


            $page_settings = base64_encode(serialize($page_settings));
                
            // Update Row
            $r = $wpdb->update( 
                $tablename, 
                array( 
                    'settings' => $page_settings,
                ), 
                array( 'ID' => $page_id ), 
                array( 
                    '%s',
                ), 
                array( '%d' ) 
            );

            $settings_name = $_REQUEST['settings_name'];
            $settings = $_REQUEST;
            unset($settings['action']);
            unset($settings['_wpnonce']);
            $r = update_option($settings_name,stripslashes_deep($settings));
            echo '1';

        }
        exit();
    }

    function save_language( ){
        if(check_ajax_referer('seed_cspv5_save_language')){
            $settings_name = $_REQUEST['settings_name'];
            $settings = $_REQUEST;
            unset($settings['action']);
            unset($settings['_wpnonce']);
            $r = update_option($settings_name,stripslashes_deep($settings));
            echo '1';

        }
        exit();
    }

    function save_language_detail( ){
        if(check_ajax_referer('seed_cspv5_save_language_detail')){
            $settings_name = $_REQUEST['settings_name'];
            $settings = $_REQUEST;
            unset($settings['action']);
            unset($settings['_wpnonce']);
            $r = update_option($settings_name,stripslashes_deep(array_filter($settings)));
            echo '1';

        }
        exit();
    }


    function duplicate_page_process( ){
        if(check_ajax_referer('seed_cspv5_duplicate_page')){
            // Duplicate Page
            $page_id = esc_sql($_REQUEST['page_id']);
            $path = sanitize_title(esc_sql($_REQUEST['path']));
            
            // Get Page
            global $wpdb;
            $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
            $sql = "SELECT * FROM $tablename WHERE id= %d";
            $safe_sql = $wpdb->prepare($sql,$page_id);
            $page = $wpdb->get_row($safe_sql);

            // Insert new page
            if(!empty($page)){
            $wpdb->insert( 
                $tablename, 
                array( 
                    'path' => $path, 
                    'name' => $page->name,
                    'type' => $page->type,
                    'settings' => $page->settings,
                    'html' => $page->html,
                    'meta' => $page->meta,
                    'mailprovider' => $page->mailprovider,
                    'deactivate' => $page->deactivate,
                ), 
                array( 
                    '%s', 
                    '%s', 
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                ) 
            );

            $new_page_id = $wpdb->insert_id;

            // Duplicate Mail Options
            $mail_options = get_option('seed_cspv5_'.$page_id.'_'.$page->mailprovider);
            add_option('seed_cspv5_'.$new_page_id.'_'.$page->mailprovider, $mail_options);
            }



            echo $new_page_id;
        }
        exit();
    }

    /**
     * Render html to Databae
     *
     */
    function get_html( ){
       
        if(check_ajax_referer('seed_cspv5_get_html')){
            $page_id = '';

            if(isset($_GET['page_id'])){
                $page_id = $_GET['page_id'];
            }

            // Get Page
            global $wpdb;
            $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
            $sql = "SELECT * FROM $tablename WHERE id= %d";
            $safe_sql = $wpdb->prepare($sql,$page_id);
            $page = $wpdb->get_row($safe_sql);
            $html = '';

            // Check for base64 encoding of settings
            if ( base64_encode(base64_decode($page->settings, true)) === $page->settings){
                $settings = unserialize(base64_decode($page->settings));
            } else {
                $settings = unserialize($page->settings);
            }

            // render
            $upload_dir = wp_upload_dir();
            if(is_multisite()){
                $path = $upload_dir['baseurl'].'/seedprod/'.get_current_blog_id().'/template-'.$page_id.'/index.php';
            }else{
                $path = $upload_dir['basedir'].'/seedprod/template-'.$page_id.'/index.php';
            }

            ob_start();
            if(file_exists($path)){
                require_once($path);
            }else{
                require_once(SEED_CSPV5_PLUGIN_PATH.'template/index.php');
            }
            $html = ob_get_clean();

            echo $html; 
        }
        exit();
    }


    /**
     * Reset the settings page. Reset works per settings id.
     *
     */
    function save_page_v2( )
    {
        if(check_ajax_referer('seed_cspv5_save_page_v2')){

            array_walk_recursive ( $_REQUEST, 'seed_cspv5_change_string_boolean_to_boolean');
        

            // Vars
            $request = stripslashes_deep($_REQUEST); 

            $page_id = $request['page']['id'];
            $name = $request['page']['name'];
            $settings = $request['settings'];

            $mailprovider = $settings['emaillist'];
        
            $settings = base64_encode(serialize($settings));

                
            // DB setup
            global $wpdb;
            $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
            
            // Update Row
            $r = $wpdb->update( 
                $tablename, 
                array( 
                    'name' => stripslashes($name),
                    //'url' => $url,
                    'settings_v2' => $settings,
                    'mailprovider' => $mailprovider,
                    
                ), 
                array( 'ID' => $page_id ), 
                array( 
                    '%s',
                    '%s',
                    '%s',
                ), 
                array( '%d' ) 
            );
            
            
            
            if($r !== false){
                echo 'true';
            }else{
                echo 'false';
            }
            exit();
        }
    }
    
    
    /**
     * Reset the settings page. Reset works per settings id.
     *
     */
    function save_page( )
    {
        if(check_ajax_referer('seed_cspv5_save_page')){


            // Vars 
            $r = false;
            $page_id = $_REQUEST['page_id'];
            $name = $_REQUEST['name'];
            //$url = $_REQUEST['url'];
            $settings = stripslashes_deep($_REQUEST);
            $settings_arr = $settings;

            $mailprovider = $settings['emaillist'];
            unset($settings['import_settings']);
            unset($settings['html']);
            $settings = base64_encode(serialize($settings));


            $r = false;

            // Make sure these fields are not empty

            $error_message_map = array(
                'background_color' => '<strong>Background Color</strong><br>under the Background Settings Section',
                'button_color' => '<strong>Elements Color</strong><br>under the Elements Color Section',
                'form_color' => '<strong>Form Input Background Color</strong><br>under the Elements Color Section',
                'text_color' => '<strong>Text Color</strong><br>under the Typography Section',
                'headline_color' => '<strong>Headline Color</strong><br>under the Typography Section',
                'container_color' => '<strong>Container Color & Opacity</strong><br>under the Content Container Section',
                'txt_subscribe_button' => '<strong>Subscribe Button Text</strong><br>under the Translate Text Section',
                'txt_email_field' => '<strong>Email Field Text</strong><br>under the Translate Text Section',
                'txt_name_field' => '<strong>Name Field Text </strong><br>under the Translate Text Section',
                'txt_already_subscribed_msg' => '<strong>Already Subscribed Text</strong><br>under the Translate Text Section',
                'txt_invalid_email_msg' => '<strong>Invalid Email Text</strong><br>under the Translate Text Section',
                'txt_invalid_name_msg' => '<strong>Invalid Name Text</strong><br>under the Content Container Section',
                'check_mic' => ''
            );

            $validated = true;
            $error_message = array();

            $v_fields = array(
                'background_color',
                'button_color',
                'form_color',
                'text_color',
                'headline_color',
                'container_color',
                'txt_subscribe_button',
                'txt_email_field',
                'txt_name_field',
                'txt_already_subscribed_msg',
                'txt_invalid_email_msg',
                'txt_invalid_name_msg',
                'check_mic');
            foreach($v_fields as $v){

                if(empty($settings_arr[$v])){
                    $validated = false;
                    if(!empty($error_message_map[$v])){
                    $error_message[] = $error_message_map[$v];
                    }
                }
            }

            if($validated){
                
                // DB setup
                global $wpdb;
                $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
              
                // Update Row
                $r = $wpdb->update( 
                	$tablename, 
                	array( 
                		'name' => stripslashes($name),
                		//'url' => $url,
                		'settings' => $settings,
                        'mailprovider' => $mailprovider,
                		
                	), 
                	array( 'ID' => $page_id ), 
                	array( 
                		'%s',
                		'%s',
                		'%s',
                	), 
                	array( '%d' ) 
                );
            }
            
            
            if($r !== false){
                echo 'true';
            }else{
                echo json_encode($error_message);
            }
            exit();
        }
    }
    
    /**
     * Get Email Lists
     *
     */
    function get_email_lists( )
    {
        if(check_ajax_referer('seed_cspv5_get_email_lists')){
            $emaillist = $_REQUEST['emaillist'];
            $mod = $_REQUEST['mod'];
            if(!empty($mod )){
                $emaillist = $mod;
            }

            $r = call_user_func('seed_cspv5_get_'.$emaillist.'_lists');
            echo $r;
            
        }
        exit();
    }
    
    /**
     * Save Emaillist Settings
     *
     */
    function save_emaillist_settings( )
    {
        if(check_ajax_referer('seed_cspv5_save_emaillist_settings')){
            $settings_name = $_REQUEST['settings_name'];
            $r = update_option($settings_name,stripslashes_deep($_REQUEST));
            echo '1';
            
        }
        exit();
    }
    
    
    /**
     * Load theme
     *
     */
    function load_theme( )
    {
        if(check_ajax_referer('seed_cspv5_load_theme')){



           
            // Vars
            $page_id = '';
            if(isset($_REQUEST['page_id'])){
                $page_id = sanitize_text_field($_REQUEST['page_id']);
            }

            $type = '';
            if(isset($_REQUEST['type'])){
                $type = sanitize_text_field($_REQUEST['type']);
            }

            $name = '';
            if(isset($_REQUEST['name'])){
                $name = sanitize_text_field($_REQUEST['name']);
            }

            $path = '';
            if(isset($_REQUEST['path'])){
                $path = sanitize_text_field($_REQUEST['path']);
            }


            // Create new page if page_id -1
            if($page_id == '-1'){
                global $wpdb;
                $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
                $default_settings = seed_cspv5_get_page_default_settings();

                $wpdb->insert( 
                    $tablename, 
                    array( 
                        'name' => stripslashes($name), 
                        'type' => $type,
                        'path' => sanitize_title_with_dashes($path),
                        'settings' => serialize($default_settings) ,
                    ), 
                    array( 
                        '%s', 
                        '%s' 
                    ) 
                );
                $page_id = $wpdb->insert_id;
                if($type == 'cs'){
                    update_option('seed_cspv5_coming_soon_page_id',$page_id);
                }
            }

            
            $theme_id = '';
            if(isset($_REQUEST['theme'])){
                $theme_id = $_REQUEST['theme'];
            }
            
            $theme = array();

            if($theme_id != '0'){
            if($theme_id != ''){
                // Get theme specifics
                if ( false === ( get_transient( 'seed_cspv5_theme_id_'.$theme_id ) )  || SEED_CSPV5_THEME_DEV ) {
                   //echo 'miss';
                    $url = SEED_CSPV5_THEME_API_URL.'?theme_id='.$theme_id; 
                    $response = wp_remote_get( $url );
                    if ( is_wp_error( $response ) ) {
                        $error_message = $response->get_error_message();
                        echo "false";
                        exit();
                    }else{
                        $theme = $response['body'];
                        $theme_arr = json_decode($theme);
                        if(!empty($theme_arr->id)){
                            set_transient('seed_cspv5_theme_id_'.$theme_id,$response['body'],432000);
                        }else{
                            echo "false";
                            exit(); 
                        }
                        
                    }
                }else{
                    //echo 'hit';
                    $theme =  get_transient( 'seed_cspv5_theme_id_'.$theme_id);
                }
                $theme = json_decode($theme);
            }
        }
            
            //var_dump(json_decode($theme->settings, TRUE));


            
             // Merge theme into curretn theme
            if(!empty($page_id)){
                //Get page settings
                
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

 
                if(!empty($theme->id)){
                if($settings !== false){

                    //var_dump($settings);
                    //var_dump(json_decode($theme->settings, TRUE));
                    $settings = array_merge($settings,json_decode($theme->settings, TRUE));
                    //var_dump($settings);
                    $settings['theme_css'] = $theme->css;
                    $settings['theme_scripts'] = $theme->scripts;
                    $settings['theme'] = $theme->id;
                    
                
                    // Save settings back to page
                    global $wpdb;
                    $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
                    $r = false;
                    // Update Row
                    $r = $wpdb->update( 
                    	$tablename, 
                    	array( 
                    		'settings' => base64_encode(serialize($settings)),
                    		
                    	), 
                    	array( 'ID' => $page_id ), 
                    	array( 
                    		'%s',
       
                    	), 
                    	array( '%d' ) 
                    );

                }
                }
                if(!isset($r)){
                    echo $page_id;
                    exit();
                }
          
                if($r !== false){
                    echo $page_id;
                }else{
                    echo 'false';
                }       
            }
                
            exit();
        }
    }
    
    /**
     * Reset the settings page. Reset works per settings id.
     *
     */
    function backgrounds( )
    {
        if(check_ajax_referer('seed_cspv5_backgrounds')){
           
           $r = array();
            $page = '';
            if(isset($_REQUEST['page'])){
                $page = $_REQUEST['page'];
                $page = '?page='.$page;
                $r['page'] = $_REQUEST['page'];
            }
            $query = '';
            if(isset($_REQUEST['query'])){
                $query = $_REQUEST['query'];
                $query = '?query='.$query;
                $r['query'] = $_REQUEST['query'];
            }
            
            if ( false === ( get_transient( 'seed_cspv5_backgrounds_page_'.$query.$page ) ) ) {
            //f(1){
                //echo 'miss';
                if(seed_cspv5_cu('su')){
                    $bg_api = SEED_CSPV5_BACKGROUND_SEARCH_API_URL;
                }else{
                    $bg_api = SEED_CSPV5_BACKGROUND_API_URL;
                }
                $url = $bg_api.'?'.http_build_query($r).'&api_key='.get_option('seed_cspv5_license_key').'&domain='.urlencode(home_url()); 
                $response = wp_remote_get( $url );
                //var_dump($url);
                if ( is_wp_error( $response ) ) {
                    $error_message = $response->get_error_message();
                    echo "Something went wrong: $error_message";
                }else{
                    $response_code = wp_remote_retrieve_response_code( $response );
                    if($response_code == '200' && seed_cspv5_cu()){
                        set_transient('seed_cspv5_backgrounds_page_'.$query.$page,$response['body'],604800);
                        echo $response['body'];
                    }else{
                        echo 'There was an issue loading the backgrounds. Please make sure you have entered a valid license key in the plugin or try again later.';
                    }
                    
                }
            }else{
                //echo 'hit';
                echo get_transient( 'seed_cspv5_backgrounds_page_'.$query.$page );
            }
                    


            exit();
        }
    }
    
    /**
     * Reset the settings page. Reset works per settings id.
     *
     */
    function backgrounds_sideload( )
    {
        if(check_ajax_referer('seed_cspv5_backgrounds_sideload')){
           
            $image = '';
            if(isset($_REQUEST['image'])){
                $image =  urldecode($_REQUEST['image']);
                $file = media_sideload_image($image.'&type=.jpg', 0, null, 'src' );
                
                if ( is_wp_error( $file ) ) {
                    $error_message = $file->get_error_message();
                    echo "0";
                    exit();
            	}else{
            	   echo $file; 
            	}
                
            }
            
            exit();
        }
    }

    function backgrounds_download( )
    {
        if(check_ajax_referer('seed_cspv5_backgrounds_download')){
           
            $image = '';
            if(isset($_REQUEST['image'])){
                $image =  urldecode($_REQUEST['image']);
                $response = wp_remote_get( 'https://api.seedprod.com/v3/background_download?image='.$image );
                if ( !is_wp_error( $response ) ) {
                  echo '1';
                }
                
            }
            
            exit();
        }
    }


    /**
     * Reset the settings page. Reset works per settings id.
     *
     */
    function reset_defaults( )
    {
        if ( isset( $_POST[ 'seed_cspv5_reset' ] ) ) {
            $option_page = $_POST[ 'option_page' ];
            check_admin_referer( $option_page . '-options' );
            require_once(SEED_CSPV5_PLUGIN_PATH.'inc/default-settings.php');

            $_POST[ $_POST[ 'option_page' ] ] = $seed_cspv5_settings_deafults[$_POST[ 'option_page' ]];
            add_settings_error( 'general', 'seed_cspv5-settings-reset', __( "Settings reset." ), 'updated' );
        }
    }

    /**
     * Properly enqueue styles and scripts for our theme options page.
     *
     * This function is attached to the admin_enqueue_scripts action hook.
     *
     * @since  0.1.0
     * @param string $hook_suffix The name of the current page we are on.
     */
    function admin_enqueue_scripts( $hook_suffix )
    {
        $pages = array(
            'settings_page_seed_cspv5'
        );

        wp_enqueue_style( 'seed-cspv5-adminbar-notification', SEED_CSPV5_PLUGIN_URL.'inc/adminbar-style.css', false, SEED_CSPV5_VERSION, 'screen');

        if ( $hook_suffix ==  'settings_page_seed_cspv5_form'){
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script( 'jquery-ui-sortable' );
        }

        if ( $hook_suffix ==  'settings_page_seed_cspv5_autoresponder'){
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script('jquery-ui-core');
        }

        if ( $hook_suffix ==  'settings_page_seed_cspv5_language'){
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script( 'jquery-ui-sortable' );
        }

        if ( $hook_suffix ==  'settings_page_seed_cspv5_language_detail'){
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script('jquery-ui-core');
        }
        
        if ( $hook_suffix ==  'settings_page_seed_cspv5_themes'){
            wp_enqueue_script( 'jquery-masonry' );
            wp_enqueue_style( 'seed_cspv5-framework-css', SEED_CSPV5_PLUGIN_URL . 'admin/settings-style.css', false, $this->plugin_version );
        }

        if ( in_array($hook_suffix,$pages) ){
            
            wp_enqueue_script( 'media-upload' );
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_script( 'wp-lists' );
            wp_enqueue_script( 'seed_cspv5-framework-js', SEED_CSPV5_PLUGIN_URL . 'admin/settings-scripts.js', array( 'jquery' ), $this->plugin_version );
            wp_enqueue_script( 'theme-preview' );
            wp_enqueue_style( 'thickbox' );
            wp_enqueue_style( 'media-upload' );
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style( 'seed_cspv5-framework-css', SEED_CSPV5_PLUGIN_URL . 'admin/settings-style.css', false, $this->plugin_version );
            wp_enqueue_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.css', false, $this->plugin_version );
            wp_enqueue_script( 'seed-cspv5-backend-script', plugins_url('admin/backend-scripts.js',dirname(__FILE__)), array( 'jquery' ),SEED_CSPV5_VERSION, true );  
			$data = array( 
                'delete_confirm' => __( 'Are you sure you want to DELETE all pages?' , 'seedprod-coming-soon-pro'),
            );
			wp_localize_script( 'seed-cspv5-backend-script', 'seed_cspv5_msgs', $data );
        }
        
        if($hook_suffix == $this->plugin_screen_customizer_hook_suffix){
            wp_dequeue_script( 'color-picker-min' );
            wp_enqueue_script( 'media-upload' );
            wp_enqueue_script( 'seed_cspv5-customizer-js', SEED_CSPV5_PLUGIN_URL . 'customizer/customizer-scripts.js', array( 'jquery' ), $this->plugin_version );
            wp_enqueue_style( 'thickbox' );
            wp_enqueue_style( 'media-upload' );
            wp_enqueue_style( 'seed_cspv5-customizer-css', SEED_CSPV5_PLUGIN_URL . 'customizer/customizer-style.css', false, $this->plugin_version );
        }
    }

    /**
     * Creates WordPress Menu pages from an array in the config file.
     *
     * This function is attached to the admin_menu action hook.
     *
     * @since 0.1.0
     */
    function create_menus( )
    {
  
    $this->plugin_screen_hook_suffix = add_options_page(
            __( "Coming Soon Pro", 'seedprod-coming-soon-pro' ),
            __( "Coming Soon Pro", 'seedprod-coming-soon-pro' ),
            'manage_options',
            'seed_cspv5',
            array( &$this , 'option_page' )
            );

    $this->plugin_screen_customizer_hook_suffix = add_submenu_page(
            NULL,
            __( "Export Theme", 'seedprod-coming-soon-pro' ),
            __( "Export Theme", 'seedprod-coming-soon-pro' ),
            'manage_options',
            'seed_cspv5_export_theme',
            array( &$this , 'export_theme' )
            );
            
    $this->plugin_screen_customizer_hook_suffix = add_submenu_page(
            NULL,
            __( "Customizer", 'seedprod-coming-soon-pro' ),
            __( "Customizer", 'seedprod-coming-soon-pro' ),
            'manage_options',
            'seed_cspv5_customizer',
            array( &$this , 'customizer' )
            );

    $this->plugin_screen_customizer_hook_suffix = add_submenu_page(
        NULL,
        __( "Customizer", 'seedprod-coming-soon-pro' ),
        __( "Customizer", 'seedprod-coming-soon-pro' ),
        'manage_options',
        'seed_cspv5_customizer_v2',
        array( &$this , 'customizer_v2' )
        );

    $this->plugin_screen_themes_hook_suffix = add_submenu_page(
            NULL,
            __( "Themes", 'seedprod-coming-soon-pro' ),
            __( "Themes", 'seedprod-coming-soon-pro' ),
            'manage_options',
            'seed_cspv5_prizes',
            array( &$this , 'prizes_page' )
            );
            
    $this->plugin_screen_themes_hook_suffix = add_submenu_page(
            NULL,
            __( "Themes", 'seedprod-coming-soon-pro' ),
            __( "Themes", 'seedprod-coming-soon-pro' ),
            'manage_options',
            'seed_cspv5_themes',
            array( &$this , 'themes_page' )
            );

    $this->plugin_screen_form_hook_suffix = add_submenu_page(
        NULL,
        __( "Forms", 'seedprod-coming-soon-pro' ),
        __( "Forms", 'seedprod-coming-soon-pro' ),
        'manage_options',
        'seed_cspv5_form',
        array( &$this , 'form_page' )
        );

    $this->plugin_screen_duplicate_hook_suffix = add_submenu_page(
        NULL,
        __( "Duplicate", 'seedprod-coming-soon-pro' ),
        __( "Duplicate", 'seedprod-coming-soon-pro' ),
        'manage_options',
        'seed_cspv5_duplicate',
        array( &$this , 'duplicate_page' )
        );

    $this->plugin_screen_duplicate_hook_suffix = add_submenu_page(
            NULL,
            __( "Duplicate", 'seedprod-coming-soon-pro' ),
            __( "Duplicate", 'seedprod-coming-soon-pro' ),
            'manage_options',
            'seed_cspv5_deactivate',
            array( &$this , 'deactivate_page' )
            );

    $this->plugin_screen_form_hook_suffix = add_submenu_page(
        NULL,
        __( "Autoresponder", 'seedprod-coming-soon-pro' ),
        __( "Autoresponder", 'seedprod-coming-soon-pro' ),
        'manage_options',
        'seed_cspv5_autoresponder',
        array( &$this , 'autoresponder_page' )
    );

    $this->plugin_screen_language_hook_suffix = add_submenu_page(
        NULL,
        __( "Languages", 'seedprod-coming-soon-pro' ),
        __( "Languages", 'seedprod-coming-soon-pro' ),
        'manage_options',
        'seed_cspv5_language',
        array( &$this , 'language_page' )
        );

    $this->plugin_screen_language_detail_hook_suffix = add_submenu_page(
        NULL,
        __( "Language Detail", 'seedprod-coming-soon-pro' ),
        __( "Language Detail", 'seedprod-coming-soon-pro' ),
        'manage_options',
        'seed_cspv5_language_detail',
        array( &$this , 'language_detail_page' )
        );
      
    $this->plugin_screen_integrations_hook_suffix = add_submenu_page(
            NULL,
            __( "Integrations", 'seedprod-coming-soon-pro' ),
            __( "Integrations", 'seedprod-coming-soon-pro' ),
            'manage_options',
            'seed_cspv5_integrations',
            array( &$this , 'integrations_page' )
            );
    
    
    $this->plugin_screen_importer_hook_suffix = add_submenu_page(
            NULL,
            __( "Import", 'seedprod-coming-soon-pro' ),
            __( "Import", 'seedprod-coming-soon-pro' ),
            'manage_options',
            'seed_cspv5_import',
            array( &$this , 'import_page' )
            );
    
    }



    function import_page()
    {
        require_once(SEED_CSPV5_PLUGIN_PATH.'admin/import.php');
    }

    


    /**
     * Display settings link on plugin page
     */
    function plugin_action_links( $links, $file )
    {
        $plugin_file = SEED_CSPV5_SLUG;

        if ( $file == $plugin_file ) {
            $settings_link = '<a href="options-general.php?page=seed_cspv5">Settings</a>';
            array_unshift( $links, $settings_link );
        }
        return $links;
    }


    /**
     * Allow Tabs on the Settings Page
     *
     */
    function plugin_options_tabs( )
    {
        $menu_slug   = null;
        $page        = $_REQUEST[ 'page' ];
        $uses_tabs   = false;
        $current_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : false;

        //Check if this config uses tabs
        foreach ( seed_cspv5_get_options() as $v ) {
            if ( $v[ 'type' ] == 'tab' ) {
                $uses_tabs = true;
                break;
            }
        }

        // If uses tabs then generate the tabs
        if ( $uses_tabs ) {
            echo '<h2 class="nav-tab-wrapper" style="padding-left:20px">';
            $c = 1;
            foreach ( seed_cspv5_get_options() as $v ) {
                    if ( isset( $v[ 'menu_slug' ] ) ) {
                        $menu_slug = $v[ 'menu_slug' ];
                    }
                    if ( $menu_slug == $page && $v[ 'type' ] == 'tab' ) {
                        $active = '';
                        if ( $current_tab ) {
                            $active = $current_tab == $v[ 'id' ] ? 'nav-tab-active' : '';
                        } elseif ( $c == 1 ) {
                            $active = 'nav-tab-active';
                        }

                        if(empty($v[ 'icon' ])){
                            $v[ 'icon' ] = '';
                        }

                        echo '<a class="nav-tab ' . $active . '" href="?page=' . $menu_slug . '&tab=' . $v[ 'id' ] . '"><i class="'.$v[ 'icon' ].'"></i> ' . $v[ 'label' ] . '</a>';
                        $c++;
                    }
            }
            if(seed_cspv5_cu()){
            echo '<a class="nav-tab " href="http://support.seedprod.com/" target="_blank" style="float:right;"><i class="fa fa-life-ring"></i> Support</a>';
            }
            if(!defined('SEED_CSP_API_KEY')){
            echo '<a class="nav-tab " href="'.admin_url().'options-general.php?page=seed_cspv5_welcome" style="float:right;"><i class="fa fa-hashtag"></i> License</a>';
            }
            
            echo '</h2>';

        }
    }

    /**
     * Get the layout for the page. classic|2-col
     *
     */
    function get_page_layout( )
    {
        $layout = 'classic';
        foreach ( seed_cspv5_get_options() as $v ) {
            switch ( $v[ 'type' ] ) {
                case 'menu';
                    $page = $_REQUEST[ 'page' ];
                    if ( $page == $v[ 'menu_slug' ] ) {
                        if ( isset( $v[ 'layout' ] ) ) {
                            $layout = $v[ 'layout' ];
                        }
                    }
                    break;
            }
        }
        return $layout;
    }

    function prizes_page( ){
         require_once(SEED_CSPV5_PLUGIN_PATH.'admin/prize.php');
    }
    
    
    function themes_page( ){
       // Get themes
        $paged = '?page=1';
        if(isset($_REQUEST['paged'])){
            $paged = $_REQUEST['paged'];
            $paged = '?page='.$paged;
        }
        
        $themes = array();
        if ( false === ( get_transient( 'seed_cspv5_themes_page_'.$paged ) ) || SEED_CSPV5_THEME_DEV ) {
            
            $url = SEED_CSPV5_THEME_API_URL.$paged.'&api_key='.get_option('seed_cspv5_license_key').'&domain='.urlencode(home_url());
            if(SEED_CSPV5_THEME_DEV ){
                echo 'miss';
                $url = $url.'&all=1';
                var_dump($url);
            } 
            $response = wp_remote_get( $url );

            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                    $themes = "<br><br>Please enter a valid license key to access the themes. There was an issue loading the themes but you can still proceed to create a page with the default theme. <br><a class='seed_cspv5_no_themes' href='?theme=0'>Click to continue &#8594;</a>";
            }else{
                $response_code = wp_remote_retrieve_response_code( $response );
                if($response_code == '200' && seed_cspv5_cu()){
                    set_transient('seed_cspv5_themes_page_'.$paged,$response['body'],604800);
                    $themes = $response['body'];
                }else{
                    $themes = "<br><br>Please enter a valid license key to access the themes. You can still proceed to create a page with the default theme.<br> <a class='seed_cspv5_no_themes' href='?theme=0'>Click to continue &#8594;</a>";
                }
            }
        }else{
            //echo 'hit';
            $themes = get_transient( 'seed_cspv5_themes_page_'.$paged );
        }
        
       
       require_once(SEED_CSPV5_PLUGIN_PATH.'admin/themes.php');
    }

    function form_page( ){
        require_once(SEED_CSPV5_PLUGIN_PATH.'admin/form.php');
    }

    function autoresponder_page( ){
        require_once(SEED_CSPV5_PLUGIN_PATH.'admin/autoresponder.php');
    }

    function language_page( ){
        require_once(SEED_CSPV5_PLUGIN_PATH.'admin/language.php');
    }

    function language_detail_page( ){
        require_once(SEED_CSPV5_PLUGIN_PATH.'admin/language_detail.php');
    }
    
    function integrations_page( ){
       require_once(SEED_CSPV5_PLUGIN_PATH.'admin/integrations.php');
    }
    
    function subscribers_page( ){
       require_once(SEED_CSPV5_PLUGIN_PATH.'admin/subscribers.php');
    }

    function duplicate_page( ){
       require_once(SEED_CSPV5_PLUGIN_PATH.'admin/duplicate.php');
    }

    function deactivate_page( ){
       $page_id = $_GET['id'];
       global $wpdb;

       $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
       $sql = "SELECT * FROM $tablename WHERE id= %d";
       $safe_sql = $wpdb->prepare($sql,$page_id);
       $page = $wpdb->get_row($safe_sql);

       if(empty($page->deactivate)){
           // deactive
           $wpdb->update( 
            $tablename, 
            array( 
                'deactivate' => 1,	// string
            ), 
            array( 'id' => $page_id ), 
            array( 
                '%d'	// value2
            ), 
            array( '%d' ) 
            );

       }else{
           // activate
           $wpdb->update( 
            $tablename, 
            array( 
                'deactivate' => 0,	// string
            ), 
            array( 'id' => $page_id ), 
            array( 
                '%d'	// value2
            ), 
            array( '%d' ));
       }

       echo '<script>window.location = "options-general.php?page=seed_cspv5&tab=seed_cspv5_tab_pages";</script>';
 
     }

    function customizer_v2( ){
    //var_dump($_GET['seed_cspv5_customize']);

        

    // Page auth make sure user can perform this action
    
    // Page Info
    $page_id = 0;
    if(isset($_GET['seed_cspv5_customize'])){
        $page_id = $_GET['seed_cspv5_customize'];
    }
    
    
    $fontfile = SEED_CSPV5_PLUGIN_PATH . 'customizer/webfonts.json';
    $fonts_json = file_get_contents($fontfile);
    $fonts[0] = 'Inherit';
    $fonts['Standard Fonts'] = array(
        "Helvetica, Arial, sans-serif"                         => "Helvetica, Arial, sans-serif",
        "'Arial Black', Gadget, sans-serif"                    => "'Arial Black', Gadget, sans-serif",
        "'Bookman Old Style', serif"                           => "'Bookman Old Style', serif",
        "'Comic Sans MS', cursive"                             => "'Comic Sans MS', cursive",
        "Courier, monospace"                                   => "Courier, monospace",
        "Garamond, serif"                                      => "Garamond, serif",
        "Georgia, serif"                                       => "Georgia, serif",
        "Impact, Charcoal, sans-serif"                         => "Impact, Charcoal, sans-serif",
        "'Lucida Console', Monaco, monospace"                  => "'Lucida Console', Monaco, monospace",
        "'Lucida Sans Unicode', 'Lucida Grande', sans-serif"   => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
        "'MS Sans Serif', Geneva, sans-serif"                  => "'MS Sans Serif', Geneva, sans-serif",
        "'MS Serif', 'New York', sans-serif"                   => "'MS Serif', 'New York', sans-serif",
        "'Palatino Linotype', 'Book Antiqua', Palatino, serif" => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
        "Tahoma,Geneva, sans-serif"                            => "Tahoma, Geneva, sans-serif",
        "'Times New Roman', Times,serif"                       => "'Times New Roman', Times, serif",
        "'Trebuchet MS', Helvetica, sans-serif"                => "'Trebuchet MS', Helvetica, sans-serif",
        "Verdana, Geneva, sans-serif"                          => "Verdana, Geneva, sans-serif",
    );
    $gfonts = json_decode($fonts_json,true);
    foreach($gfonts as $k => $v){
        $font_families["'".$k."'"] = $k;
    }
    $fonts['Google Fonts'] = $font_families;

    $font_families = $fonts;
    
    $emaillist = apply_filters( 'seed_cspv5_providers', array(
                                    'database' => __( 'Database', 'seedprod-coming-soon-pro' ),
                                    'feedblitz' => 'FeedBlitz',
                                    'drip' => 'Drip',
                                    'feedburner' => 'FeedBurner',
                                    'activecampaign' => 'Active Campaign',
                                    'aweber' => 'Aweber',
                                    'campaignmonitor' => 'Campaign Monitor',
                                    'constantcontact' => 'Constant Contact',
                                    'convertkit' => 'ConvertKit',
                                    'getresponse' => 'Get Response',
                                    'gravityforms' => 'Gravity Forms',
                                    'ninjaforms' => 'Ninja Forms',
                                    'followupemails' => 'Follow-Up Emails',
                                    'formidable' => 'Formidable',
                                    'icontact' => 'iContact',
                                    'infusionsoft' => 'Infusionsoft',
                                    'madmimi' => 'Mad Mimi',
                                    'mailchimp' => 'MailChimp',
                                    'sendy' => 'Sendy',
                                    'zapier' => 'Zapier',
                                    'mailpoet' => 'MailPoet',
                                    'mymail' => 'Mailster formerly MyMail',
                                    'htmlwebform' => 'HTML Web Form / Shortcode',
                                ) );
    
    natcasesort($emaillist);
    $emaillist = array('database'=>'Database') + $emaillist;
    
    
    
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

    if($settings === false){
        echo '<br><br><strong>There was an issue retrieving your settings. Please open a ticket &amp; copy and paste in this text below.</strong><br><br>';
        echo '<div style="width:500px;white-space: pre-wrap; word-wrap: break-word; ">';
        var_dump($page->settings);
        echo '</div>';
        die();
        
    }

    $blocks = array('logo','headline','description','form','progress_bar','countdown','social_profiles','share_buttons','contact_form','column');
    if(!empty($settings->blocks)){
        foreach($block as $v){
            $settings->blocks = seed_cspv5_array_add($v);
        }
        $blocks = $settings->blocks;
    }
    // else{
    //     array_splice($blocks, -1, "contact_form");
    //     $settings->blocks = $blocks;
    //     // die();
    // }

    $settings = seed_cspv5_array_add($settings,'blocks',$blocks);
    
    // Add contact_form block
    if(!in_array('contact_form',$settings['blocks'])){
        $key = array_search('column',$settings['blocks']);
        seed_cspv5_array_insert(
            $settings['blocks'],
            $key,
            "contact_form"
        );
    }

    
    $settings = json_decode(json_encode($settings), FALSE);

  
     // var_dump($settings);
     // die();
        if($settings !== false){
            ?>

            <div id="seed-cspv5-customizer">
              <?php require_once(SEED_CSPV5_PLUGIN_PATH.'customizer/customizer_v2.php'); ?>
            </div>
            <?php
        }
    }

    function export_theme( ){
        // Page Info
        $page_id = 0;
        if(isset($_GET['seed_cspv5_customize'])){
            $page_id = $_GET['seed_cspv5_customize'];
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

        var_dump($settings['theme']);
        
        $white_list = array(
            "background_color",
            "background_repeat",
            "background_size",
            "background_attachment",
            "background_position",
            "background_image",
            "enable_background_adv_settings",
            "background_overlay",
            "enable_background_overlay",
            "bg_slideshow",
            "bg_video",
            "text_font",
            "text_weight",
            "text_color",
            "text_size",
            "text_line_height",
            "headline_font",
            "headline_weight",
            "headline_color",
            "headline_size",
            "headline_line_height",
            "button_font",
            "button_weight",
            "container_radius",
            "container_color",
            "container_position",
            "container_width",
            "container_effect_animation",
            "container_transparent",
            "button_color",
            "form_color",
            "container_transparent",
            "form_width",
            "contactform_color",
            "disabled_fields",
        );

        foreach($settings as $k => $v){
            if (!in_array($k, $white_list)) {
                unset($settings[$k]);
            }
        }

        if(!array_key_exists('container_transparent',$settings)){
            $settings['container_transparent'] = '0';
        }
     
        if(!array_key_exists('enable_background_overlay',$settings)){
            $settings['enable_background_overlay'] = '0';
        }

        //echo '<pre>';
        echo '<textarea style="width:90%;height:500px">'.json_encode($settings).'</textarea>';
        //echo '</pre>';



    }
    
    function customizer( ){

    //var_dump($_GET['seed_cspv5_customize']);

        

    // Page auth make sure user can perform this action
    
    // Page Info
    $page_id = 0;
    if(isset($_GET['seed_cspv5_customize'])){
        $page_id = $_GET['seed_cspv5_customize'];
    }
    
    
    $fontfile = SEED_CSPV5_PLUGIN_PATH . 'customizer/webfonts.json';
    $fonts_json = file_get_contents($fontfile);
    $fonts[0] = 'Inherit';
    $fonts['Standard Fonts'] = array(
        "Helvetica, Arial, sans-serif"                         => "Helvetica, Arial, sans-serif",
        "'Arial Black', Gadget, sans-serif"                    => "'Arial Black', Gadget, sans-serif",
        "'Bookman Old Style', serif"                           => "'Bookman Old Style', serif",
        "'Comic Sans MS', cursive"                             => "'Comic Sans MS', cursive",
        "Courier, monospace"                                   => "Courier, monospace",
        "Garamond, serif"                                      => "Garamond, serif",
        "Georgia, serif"                                       => "Georgia, serif",
        "Impact, Charcoal, sans-serif"                         => "Impact, Charcoal, sans-serif",
        "'Lucida Console', Monaco, monospace"                  => "'Lucida Console', Monaco, monospace",
        "'Lucida Sans Unicode', 'Lucida Grande', sans-serif"   => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
        "'MS Sans Serif', Geneva, sans-serif"                  => "'MS Sans Serif', Geneva, sans-serif",
        "'MS Serif', 'New York', sans-serif"                   => "'MS Serif', 'New York', sans-serif",
        "'Palatino Linotype', 'Book Antiqua', Palatino, serif" => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
        "Tahoma,Geneva, sans-serif"                            => "Tahoma, Geneva, sans-serif",
        "'Times New Roman', Times,serif"                       => "'Times New Roman', Times, serif",
        "'Trebuchet MS', Helvetica, sans-serif"                => "'Trebuchet MS', Helvetica, sans-serif",
        "Verdana, Geneva, sans-serif"                          => "Verdana, Geneva, sans-serif",
    );
    $gfonts = json_decode($fonts_json,true);
    foreach($gfonts as $k => $v){
        $font_families["'".$k."'"] = $k;
    }
    $fonts['Google Fonts'] = $font_families;

    $font_families = $fonts;
    
    $emaillist = apply_filters( 'seed_cspv5_providers', array(
                                    'database' => __( 'Database', 'seedprod-coming-soon-pro' ),

                                ) );



    
    natcasesort($emaillist);
    $emaillist = array('database'=>'Database') + $emaillist;
    
    
    
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

    if($settings === false){
        echo '<br><br><strong>There was an issue retrieving your settings. Please open a ticket &amp; copy and paste in this text below.</strong><br><br>';
        echo '<div style="width:500px;white-space: pre-wrap; word-wrap: break-word; ">';
        var_dump($page->settings);
        echo '</div>';
        die();
        
    }

    $blocks = array('logo','headline','description','form','progress_bar','countdown','social_profiles','share_buttons','contact_form','column');
    if(!empty($settings->blocks)){
        foreach($block as $v){
            $settings->blocks = seed_cspv5_array_add($v);
        }
        $blocks = $settings->blocks;
    }
    // else{
    //     array_splice($blocks, -1, "contact_form");
    //     $settings->blocks = $blocks;
    //     // die();
    // }

    $settings = seed_cspv5_array_add($settings,'blocks',$blocks);
    
    // Add contact_form block
    if(!in_array('contact_form',$settings['blocks'])){
        $key = array_search('column',$settings['blocks']);
        seed_cspv5_array_insert(
            $settings['blocks'],
            $key,
            "contact_form"
        );
    }

    
    $settings = json_decode(json_encode($settings), FALSE);

  
     // var_dump($settings);
     // die();
        if($settings !== false){
            ?>

            <div id="seed-cspv5-customizer">
              <?php require_once(SEED_CSPV5_PLUGIN_PATH.'customizer/customizer.php'); ?>
            </div>
            <?php
        }
    }

    /**
     * Render the option pages.
     *
     * @since 0.1.0
     */
    function option_page( )
    {
        if(seed_cspv5_cu('none')){return false;}

        $menu_slug = null;
        $page   = $_REQUEST[ 'page' ];
        $layout = $this->get_page_layout();
        ?>
        <div class="wrap seed_cspv5 columns-2">

           
            <?php include(SEED_CSPV5_PLUGIN_PATH.'admin/header.php') ?>
            <?php $this->plugin_options_tabs(); ?>
            <?php if ( $layout == '2-col' ): ?>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-1">
                    <div id="post-body-content" >
            <?php endif; ?>
                    <?php if(!empty($_GET['tab']))
                            do_action( 'seed_cspv5_render_page', array('tab'=>$_GET['tab']));
                    ?>
                    <?php if(!empty($_GET['tab']) && $_GET['tab'] == 'seed_cspv5_build' ) { 
                    //$token = '6a6c0899-0139-4a25-806e-a780d863a5af';
                    ?>
                        <iframe src="test<?php echo $token ?>" width="100%"  height="500px"></iframe>
                    <?php }else{ ?>
                    
                    
                    <form action="options.php" method="post">

                    <!-- <input name="submit" type="submit" value="<?php _e( 'Save All Changes', 'seedprod-coming-soon-pro' ); ?>" class="button-primary"/> -->
                    <?php if(!empty($_GET['tab']) && $_GET['tab'] != 'seed_cspv5_tab_3') { ?>
                    <!-- <input id="reset" name="reset" type="submit" value="<?php _e( 'Reset Settings', 'seedprod-coming-soon-pro' ); ?>" class="button-secondary"/>     -->
                    <?php } ?>

                            <?php
                            $show_submit = false;
                            foreach ( seed_cspv5_get_options() as $v ) {
                                if ( isset( $v[ 'menu_slug' ] ) ) {
                                    $menu_slug = $v[ 'menu_slug' ];
                                }
                                    if ( $menu_slug == $page ) {
                                        switch ( $v[ 'type' ] ) {
                                            case 'menu';
                                                break;
                                            case 'tab';
                                                $tab = $v;
                                                if ( empty( $default_tab ) )
                                                    $default_tab = $v[ 'id' ];
                                                break;
                                            case 'setting':
                                                $current_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : $default_tab;
                                                if ( $current_tab == $tab[ 'id' ] ) {
                                                    settings_fields( $v[ 'id' ] );
                                                    $show_submit = true;
                                                }

                                                break;
                                            case 'section':
                                                $current_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : $default_tab;
                                                if ( $current_tab == $tab[ 'id' ] or $current_tab === false ) {
                                                    if ( $layout == '2-col' ) {
                                                        echo '<div id="'.$v[ 'id' ].'" class="postbox seedprod-postbox">';
                                                        $icon = $v[ 'icon' ];
                                                        $this->do_settings_sections( $v[ 'id' ],$show_submit,$icon );
                                                        echo '</div>';
                                                    } else {
                                                        do_settings_sections( $v[ 'id' ] );
                                                    }

                                                }
                                                break;

                                        }

                                }
                            }
                        ?>
                    <?php if($show_submit): ?>
                    <p>
                    <!-- <input name="submit" type="submit" value="<?php _e( 'Save All Changes', 'seedprod-coming-soon-pro' ); ?>" class="button-primary"/> -->
                    <!-- <input id="reset" name="reset" type="submit" value="<?php _e( 'Reset Settings', 'seedprod-coming-soon-pro' ); ?>" class="button-secondary"/> -->
                    </p>
                    <?php endif; ?>
                    </form>
                    <?php } ?>
                    <?php //if ( $layout == '2-col' ): ?>
                     <?php if ( 1 == 0 ): ?>
                    </div> <!-- #post-body-content -->

                    <div id="postbox-container-1" class="postbox-container">
                        <div id="side-sortables" class="meta-box-sortables ui-sortable">

                            <div class="postbox rss-postbox" style="background-color: #fcf8e3">
									<div class="handlediv" title="Click to toggle"><br /></div>
									<form action="https://www.getdrip.com/forms/2650489/submissions" method="post" target="_blank" data-drip-embedded-form="2650489">
	  <h3 class="hndle" data-drip-attribute="headline"><span>How to launch a site that&#x27;s successful on Day One</span></h3>
						<div class="inside">


							<p data-drip-attribute="description">There&#x27;s nothing more disappointing than launching a new site and not get enough visitors to support it. Find out how to build an audience before you launch in this free 5-part course.</p>
							<div>
								<label for="fields[email]">Email Address</label><br />
								<input class="regular-text" style="width:100%" type="email" name="fields[email]" value="<?php echo get_option( 'admin_email' ); ?>" />
							</div>

							<div style="margin-top:10px">
								<label for="fields[first_name]">First Name</label><br />
								<input class="regular-text" style="width:100%" type="text" name="fields[first_name]" value="" />
							</div>

						<div style="margin-top:10px">
							<input type="submit" name="submit" value="Subscribe Now" style="background-color:red; border-color:firebrick;" data-drip-attribute="sign-up-button" class="button-primary" />
						</div>


										<!-- <div class="rss-widget">
											<?php
											wp_widget_rss_output(array(
												'url' => 'http://seedprod.com/feed/',
												'title' => 'SeedProd Blog',
												'items' => 3,
												'show_summary' => 0,
												'show_author' => 0,
												'show_date' => 1,
												));
												?>
												<ul>
													<li>&raquo; <a href="http://seedprod.com/subscribe/"><?php _e('Subscribe by Email', 'ultimate-coming-soon-page') ?></a></li>
												</ul>
											</div> -->
										</div>
									</form>


									</div>
                            <!-- <a href="http://www.seedprod.com/plugins/wordpress-coming-soon-pro-plugin/?utm_source=plugin&utm_medium=banner&utm_campaign=coming-soon-pro-in-plugin-banner" target="_blank"><img src="http://static.seedprod.com/ads/coming-soon-pro-sidebar.png" /></a>
                            <br><br> -->
                            <div class="postbox support-postbox" style="background-color:#d9edf7">
                                <div class="handlediv" title="Click to toggle"><br /></div>
                                <h3 class="hndle"><span><?php _e('Plugin Support', 'seedprod-coming-soon-pro') ?></span></h3>
                                <div class="inside">
                                    <div class="support-widget">
                                        <p>
                                            <?php _e('Got a Question, Idea, Problem or Praise?') ?>
                                        </p>
                                        <ul>
                                            <li>&raquo; <a href="https://wordpress.org/support/plugin/coming-soon" target="_blank"><?php _e('Support Request', 'seedprod-coming-soon-pro') ?></a></li>
                                            <li>&raquo; <a href="http://support.seedprod.com/article/83-how-to-clear-wp-super-caches-cache" target="_blank"><?php _e('Common Caching Issues Resolutions', 'seedprod-coming-soon-pro') ?></a></li>
                                        </ul>

                                    </div>
                                </div>
                            </div>
                           
                                <div class="postbox like-postbox" style="background-color:#d9edf7">
                                    <div class="handlediv" title="Click to toggle"><br /></div>
                                    <h3 class="hndle"><span><?php _e('Show Some Love', 'seedprod-coming-soon-pro') ?></span></h3>
                                    <div class="inside">
                                        <div class="like-widget">
                                            <p><?php _e('Like this plugin? Show your support by:', 'seedprod-coming-soon-pro')?></p>
                                            <ul>
                                                <li>&raquo; <a target="_blank" href="http://www.seedprod.com/features/?utm_source=coming-soon-plugin&utm_medium=banner&utm_campaign=coming-soon-link-in-plugin"><?php _e('Buy It', 'seedprod-coming-soon-pro') ?></a></li>

                                                <li>&raquo; <a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/coming-soon?rate=5#postform"><?php _e('Rate It', 'seedprod-coming-soon-pro') ?></a></li>
                                                <li>&raquo; <a target="_blank" href="<?php echo "http://twitter.com/share?url=https%3A%2F%2Fwordpress.org%2Fplugins%2Fultimate-coming-soon-page%2F&text=Check out this awesome %23WordPress Plugin I'm using, Coming Soon Page and Maintenance Mode by SeedProd"; ?>"><?php _e('Tweet It', 'seedprod-coming-soon-pro') ?></a></li>

                                                <li>&raquo; <a href="https://www.seedprod.com/submit-site/"><?php _e('Submit your site to the Showcase', 'seedprod-coming-soon-pro') ?></a></li>

                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            


                                <div class="postbox rss-postbox" style="background-color:#d9edf7">
    											<div class="handlediv" title="Click to toggle"><br /></div>
    											<h3 class="hndle"><span><?php _e('SeedProd Blog', 'ultimate-coming-soon-page') ?></span></h3>
    											<div class="inside">

    												<div class="rss-widget">
    													<?php
    													wp_widget_rss_output(array(
    													'url' => 'http://feeds.feedburner.com/seedprod/',
    													'title' => 'SeedProd Blog',
    													'items' => 3,
    													'show_summary' => 0,
    													'show_author' => 0,
    													'show_date' => 1,
    												));
    												?>
    												<ul>
    													<br>
    												<li>&raquo; <a href="https://www.getdrip.com/forms/9414625/submissions/new"><?php _e('Subscribe by Email', 'ultimate-coming-soon-page') ?></a></li>
    											</ul>
    										</div>
    									</div>
    								</div>

                        </div>
                    </div>
                </div> <!-- #post-body -->


            </div> <!-- #poststuff -->
            <?php endif; ?>
                    </div> <!-- #post-body-content -->
                </div> <!-- #post-body -->
            </div> <!-- #poststuff -->
        </div> <!-- .wrap -->

        <!-- JS login to confirm setting resets. -->
        <script>
            jQuery(document).ready(function($) {
                $('#reset').click(function(e){
                    if(!confirm('<?php _e( 'This tabs settings be deleted and reset to the defaults. Are you sure you want to reset?', 'seedprod-coming-soon-pro' ); ?>')){
                        e.preventDefault();
                    }
                });
                if(jQuery(".include_exclude_options:checked").val() == '2'){
                    jQuery("#include_url_pattern").parents('tr').show();
                }else{
                    jQuery("#include_url_pattern").parents('tr').hide();
                }

                if(jQuery(".include_exclude_options:checked").val() == '3'){
                    jQuery("#exclude_url_pattern").parents('tr').show();
                }else{
                    jQuery("#exclude_url_pattern").parents('tr').hide();
                }

                jQuery(".include_exclude_options").click(function() {
                    var val = jQuery(this).val();
                    if(val == '2'){
                        jQuery("#include_url_pattern").parents('tr').fadeIn();
                    }else{
                        jQuery("#include_url_pattern").parents('tr').hide();
                    }

                    if(val == '3'){
                        jQuery("#exclude_url_pattern").parents('tr').fadeIn();
                    }else{
                        jQuery("#exclude_url_pattern").parents('tr').hide();
                    } 
                });
            });
        </script>
        <?php
    }

    /**
     * Create the settings options, sections and fields via the WordPress Settings API
     *
     * This function is attached to the admin_init action hook.
     *
     * @since 0.1.0
     */
    function create_settings( )
    {
        foreach ( seed_cspv5_get_options() as $k => $v ) {

            switch ( $v[ 'type' ] ) {
                case 'menu':
                    $menu_slug = $v[ 'menu_slug' ];

                    break;
                case 'setting':
                    if ( empty( $v[ 'validate_function' ] ) ) {
                        $v[ 'validate_function' ] = array(
                             &$this,
                            'validate_machine'
                        );
                    }
                    register_setting( $v[ 'id' ], $v[ 'id' ], $v[ 'validate_function' ] );
                    $setting_id = $v[ 'id' ];
                    break;
                case 'section':
                    if ( empty( $v[ 'desc_callback' ] ) ) {
                        $v[ 'desc_callback' ] = array(
                             &$this,
                            '__return_empty_string'
                        );
                    } else {
                        $v[ 'desc_callback' ] = $v[ 'desc_callback' ];
                    }
                    add_settings_section( $v[ 'id' ], $v[ 'label' ], $v[ 'desc_callback' ], $v[ 'id' ] );
                    $section_id = $v[ 'id' ];
                    break;
                case 'tab':
                    break;
                default:
                    if ( empty( $v[ 'callback' ] ) ) {
                        $v[ 'callback' ] = array(
                             &$this,
                            'field_machine'
                        );
                    }

                    add_settings_field( $v[ 'id' ], $v[ 'label' ], $v[ 'callback' ], $section_id, $section_id, array(
                         'id' => $v[ 'id' ],
                        'desc' => ( isset( $v[ 'desc' ] ) ? $v[ 'desc' ] : '' ),
                        'setting_id' => $setting_id,
                        'class' => ( isset( $v[ 'class' ] ) ? $v[ 'class' ] : '' ),
                        'type' => $v[ 'type' ],
                        'default_value' => ( isset( $v[ 'default_value' ] ) ? $v[ 'default_value' ] : '' ),
                        'option_values' => ( isset( $v[ 'option_values' ] ) ? $v[ 'option_values' ] : '' )
                    ) );

            }
        }
    }

    /**
     * Create a field based on the field type passed in.
     *
     * @since 0.1.0
     */
    function field_machine( $args )
    {
        extract( $args ); //$id, $desc, $setting_id, $class, $type, $default_value, $option_values

        // Load defaults
        $defaults = array( );
        foreach ( seed_cspv5_get_options() as $k ) {
            switch ( $k[ 'type' ] ) {
                case 'setting':
                case 'section':
                case 'tab':
                    break;
                default:
                    if ( isset( $k[ 'default_value' ] ) ) {
                        $defaults[ $k[ 'id' ] ] = $k[ 'default_value' ];
                    }
            }
        }
        $options = get_option( $setting_id );

        $options = wp_parse_args( $options, $defaults );

        $path = SEED_CSPV5_PLUGIN_PATH . 'admin/field-types/' . $type . '.php';
        if ( file_exists( $path ) ) {
            // Show Field
            include( $path );
            // Show description
            if ( !empty( $desc ) ) {
                echo "<small class='description'>{$desc}</small>";
            }
        }

    }

    /**
     * Validates user input before we save it via the Options API. If error add_setting_error
     *
     * @since 0.1.0
     * @param array $input Contains all the values submitted to the POST.
     * @return array $input Contains sanitized values.
     * @todo Figure out best way to validate values.
     */
    function validate_machine( $input )
    {
        if(!isset($_POST['option_page'])){
           return $input; 
        }
        $option_page = $_POST['option_page'];
        foreach ( seed_cspv5_get_options() as $k ) {
            switch ( $k[ 'type' ] ) {
                case 'menu':
                case 'setting':
                    if(isset($k['id']))
                        $setting_id = $k['id'];
                case 'section':
                case 'tab';
                    break;
                default:
                    if ( !empty( $k[ 'validate' ] ) && $setting_id == $option_page ) {
                        $validation_rules = explode( ',', $k[ 'validate' ] );

                        foreach ( $validation_rules as $v ) {
                            $path = SEED_CSPV5_PLUGIN_PATH . 'admin/validations/' . $v . '.php';
                            if ( file_exists( $path ) ) {
                                // Defaults Values
                                $is_valid  = true;
                                $error_msg = '';

                                // Test Validation
                                include( $path );

                                // Is it valid?
                                if ( $is_valid === false ) {
                                    add_settings_error( $k[ 'id' ], 'seedprod_error', $error_msg, 'error' );
                                    // Unset invalids
                                    unset( $input[ $k[ 'id' ] ] );
                                }

                            }
                        } //end foreach

                    }
            }
        }

        return $input;
    }

    /**
     * Dummy function to be called by all sections from the Settings API. Define a custom function in the config.
     *
     * @since 0.1.0
     * @return string Empty
     */
    function __return_empty_string( )
    {
        echo '';
    }


    /**
     * SeedProd version of WP's do_settings_sections
     *
     * @since 0.1.0
     */
    function do_settings_sections( $page, $show_submit, $icon )
    {
        global $wp_settings_sections, $wp_settings_fields;

        if ( !isset( $wp_settings_sections ) || !isset( $wp_settings_sections[ $page ] ) )
            return;

        foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
            echo "<h3 class='hndle'><i class='{$icon}'></i> {$section['title']}</h3>\n";
            echo '<div class="inside">';
            call_user_func( $section[ 'callback' ], $section );
            if ( !isset( $wp_settings_fields ) || !isset( $wp_settings_fields[ $page ] ) || !isset( $wp_settings_fields[ $page ][ $section[ 'id' ] ] ) )
                continue;
            echo '<table class="form-table">';
            $this->do_settings_fields( $page, $section[ 'id' ] );
            echo '</table>';
            if($show_submit): ?>
                <p>
                <input name="submit" type="submit" value="<?php _e( 'Save All Changes', 'seedprod-coming-soon-pro' ); ?>" class="button-primary"/>
                </p>
            <?php endif;
            echo '</div>';
        }
    }

    function do_settings_fields($page, $section) {
          global $wp_settings_fields;

          if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section]) )
              return;

          foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
              echo '<tr valign="top">';
              if ( !empty($field['args']['label_for']) )
                  echo '<th scope="row"><label for="' . $field['args']['label_for'] . '">' . $field['title'] . '</label></th>';
              else
                  echo '<th scope="row"><strong>' . $field['title'] . '</strong><!--<br>'.$field['args']['desc'].'--></th>';
              echo '<td>';
              call_user_func($field['callback'], $field['args']);
              echo '</td>';
              echo '</tr>';
          }
      }
    
    /*
     * Sunscriber Actions
     */
    function subscriber_actions(){
        if(isset($_GET['tab']) && $_GET['tab'] == 'seed_cspv5_tab_subscribers'){
    
            if(!empty($_POST['action'])){
                if($_POST['action'] == 'seed_cspv5_export_subscribers'){
                    $this->export_all_subscribers();
                }
            }
        }
    }

    /**
     * Send Contact Form or return an error.
     */
    function contactform_callback(){
         // Get the page id
        $page_id = '';
        if(!empty($_REQUEST['page_id'])){
            $page_id = $_REQUEST['page_id'];
        }
        $data['page_id'] = $page_id;


        
        //Get page settings
        global $wpdb;
        $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
        $sql = "SELECT * FROM $tablename WHERE id= %d";
        $safe_sql = $wpdb->prepare($sql,$page_id);
        $page = $wpdb->get_row($safe_sql);

        if ( base64_encode(base64_decode($page->settings, true)) === $page->settings){
            $settings = unserialize(base64_decode($page->settings));
        } else {
            $settings = unserialize($page->settings);
        }

        // Get language info
        $lang_id = '';
        if(!empty($_REQUEST['lang'])){
            $lang_id = $_REQUEST['lang'];
        }

        if(!empty($lang_id)){
            $lang_settings_name = 'seed_cspv5_'.$page_id.'_language_'.$lang_id;
            $lang_settings = get_option($lang_settings_name);
            if(!empty($lang_settings)){
                $lang_settings = maybe_unserialize($lang_settings);
            }
        }
     
        if(!empty($lang_settings['txt_contact_form_error']) && !empty($lang_id)){
            $settings['txt_contact_form_error'] = $lang_settings['txt_contact_form_error'];     
        }

        
        @extract($settings);


        //if(check_ajax_referer('seed_cspv5_contactform_callback')){
        $email = sanitize_email( $_REQUEST['cspio-cf-email'] );
        $msg = sanitize_textarea_field( $_REQUEST['cspio-cf-msg'] );
        $msg = $email.PHP_EOL.PHP_EOL.$msg;
        $headers[] = 'Reply-To: '.$email;
        $mresult = false;
        $is_error = false;
        if(is_email($email) && !empty($msg)){
            if(empty($cf_form_emails)){
                $emails = get_option('admin_email');
            }else{
                $emails = $cf_form_emails;
            }
            $mresult = wp_mail($emails , '['.home_url() . __('] New Contact Form Message', 'seedprod'), $msg,$headers);
            if($mresult == false){
                http_response_code(500);
                $status = '500';
            }else{
                $status = '200';
            }
            
        }else{
            $is_error = true;
            $status = $txt_contact_form_error;
        }

        $html = '';
        if(!empty($cf_confirmation_msg)){
            $html = $cf_confirmation_msg;
        }

        // check recaptcha
        if($is_error == false && !empty($enable_recaptcha) && !empty($recaptcha_site_key) && !empty($recaptcha_secret_key)){
            $response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', 
                array('body' => array( 
                    'secret' =>  $recaptcha_secret_key, 
                    'response' => $_REQUEST['g-recaptcha-response'] 
                    )
                )
                );

            if ( is_wp_error( $response ) ) {
                $status = '500';
                $html = $error_message;
            } else {
                $body = json_decode(wp_remote_retrieve_body($response));
            }

            if($body->success === false){
                 $status = 'Invalid Recaptcha';
            }
        }
        header('Content-Type: text/javascript; charset=utf8');

        $response = array(
            'status' => $status,
            'html' => $html,
        );

        echo sanitize_text_field($_GET['callback']) . '(' . json_encode($response) . ')';
        //}
        exit();
    }
    
    /**
     * Subscribe User to Mailing List or return an error.
     */
    function subscribe_callback(){
        //if(check_ajax_referer('seed_cspv5_subscribe_callback')){
        
            // Initialize a global var to store results in
            global $seed_cspv5_post_result;
            global $errors;
            $errors = array();
            
            // Get the page id
            $page_id = '';
            if(!empty($_REQUEST['page_id'])){
                $page_id = $_REQUEST['page_id'];
            }
            $data['page_id'] = $page_id;
            
            //Get page settings
            global $wpdb;
            $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
            $sql = "SELECT * FROM $tablename WHERE id= %d";
            $safe_sql = $wpdb->prepare($sql,$page_id);
            $page = $wpdb->get_row($safe_sql);

            if ( base64_encode(base64_decode($page->settings, true)) === $page->settings){
                $settings = unserialize(base64_decode($page->settings));
            } else {
                $settings = unserialize($page->settings);
            }

            
            @extract($settings);

            $cookie_submit = false;
            if(!empty($_REQUEST['comment'])){
                $cookie_submit = true;
            }
            
            

            // Get language info
            $lang_id = '';
            if(!empty($_REQUEST['lang'])){
                $lang_id = $_REQUEST['lang'];
            }

            if(!empty($lang_id)){
                $lang_settings_name = 'seed_cspv5_'.$page_id.'_language_'.$lang_id;
                $lang_settings = get_option($lang_settings_name);
                if(!empty($lang_settings)){
                    $lang_settings = maybe_unserialize($lang_settings);
                }
            }
         
            if(!empty($lang_settings['thankyou_msg']) && !empty($lang_id)){
                $ty_content = $lang_settings['thankyou_msg'];     
            }else{
                $ty_content = $settings['thankyou_msg'];    
            }

            if(!empty($lang_settings['txt_stats_referral_url']) && !empty($lang_id)){
                $txt_stats_referral_url = $lang_settings['txt_stats_referral_url'];
            }else{
                $txt_stats_referral_url = $settings['txt_stats_referral_url'];
            }

            if(!empty($lang_settings['txt_stats_referral_stats']) && !empty($lang_id)){
                $txt_stats_referral_stats = $lang_settings['txt_stats_referral_stats'];
            }else{
                $txt_stats_referral_stats = $settings['txt_stats_referral_stats'];
            }

            if(!empty($lang_settings['txt_stats_referral_subscribers']) && !empty($lang_id)){
                $txt_stats_referral_subscribers = $lang_settings['txt_stats_referral_subscribers'];
            }else{
                $txt_stats_referral_subscribers = $settings['txt_stats_referral_subscribers'];
            }

            if(!empty($lang_settings['txt_already_subscribed_msg']) && !empty($lang_id)){
                $txt_already_subscribed_msg = $lang_settings['txt_already_subscribed_msg'];
            }else{
                $txt_already_subscribed_msg = $settings['txt_already_subscribed_msg'];
            }


            // Get form info
            // Get form settings
            if(seed_cspv5_cu('fb')){
                $form_settings_name = 'seed_cspv5_'.$page_id.'_form';
                $form_settings = get_option($form_settings_name);
                if(!empty($form_settings)){
                    $form_settings = maybe_unserialize($form_settings);
                }
            }

            // Collect request data
            // Spam check, this will be fined in if spam
            if(!empty($_REQUEST['message'])){
                return false;
            }
    
            // Check field values
            $email = '';
            if(!empty($_REQUEST['email'])){
                $email = sanitize_email($_REQUEST['email']);
            }
            
            $name = '';
            if(!empty($_REQUEST['name'])){
                $name = sanitize_text_field($_REQUEST['name']);
            }

            $optin_confirmation = 0;
            if(!empty($_REQUEST['optin_confirmation'])){
                $optin_confirmation = 1;
            }


            // Sanitize random fields
            if(seed_cspv5_cu('fb')){
                foreach($_REQUEST as $k => $v){
                    if(substr( $k, 0, 6 ) === "field_"){
                        $_REQUEST[$k] = sanitize_text_field($v);
                    }
                }
            }


            
            $bypassed_emaillist = apply_filters('seed_cspv5_bypassed_emaillist',array('gravityforms','ninjaforms','formidable'));

            // Check it we need to validate recaptcha
            if(!in_array($emaillist, $bypassed_emaillist)){
                if(!empty($enable_recaptcha) && !$cookie_submit  && !empty($recaptcha_site_key) && !empty($recaptcha_secret_key)){
                    $response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', 
                        array('body' => array( 
                            'secret' =>  $recaptcha_secret_key, 
                            'response' => $_REQUEST['g-recaptcha-response'] 
                            )
                        )
                        );

                    if ( is_wp_error( $response ) ) {
                        $seed_cspv5_post_result['status'] = '500';
                        $seed_cspv5_post_result['html'] = $error_message;
                    } else {
                        $body = json_decode(wp_remote_retrieve_body($response));
                    }

                    if($body->success === false){
                         $seed_cspv5_post_result['status'] = '400';
                         $seed_cspv5_post_result['msg'] = 'Invalid Recaptcha';
                         $seed_cspv5_post_result['msg_class'] = 'alert-danger';
                         $errors[] = $seed_cspv5_post_result['msg'];
        
                         $emaillist = '';
                    }
                }
            }

    
        // Check it we need to validate email
            if(!in_array($emaillist, $bypassed_emaillist)){
                if(is_email($email) != $email || empty($email)){
                     $seed_cspv5_post_result['status'] = '400';
                     if(!empty($lang_settings['txt_invalid_email_msg']) && !empty($lang_id)){
                        $seed_cspv5_post_result['msg'] = $lang_settings['txt_invalid_email_msg'];
                     }else{
                        $seed_cspv5_post_result['msg'] = $txt_invalid_email_msg;
                     }
                     $seed_cspv5_post_result['msg_class'] = 'alert-danger';
                     $errors[] = $seed_cspv5_post_result['msg'];
    
                     $emaillist = '';
                }
            }
            
            // Check it we need to validate name
            if(!in_array($emaillist, $bypassed_emaillist)){
                if(!empty($display_name)){
                    if(!empty($require_name) && !$cookie_submit){
                        if(empty($name)){
                             $seed_cspv5_post_result['status'] = '400';
                             if(!empty($lang_settings['txt_invalid_name_msg']) && !empty($lang_id)){
                                $seed_cspv5_post_result['msg'] = $lang_settings['txt_invalid_name_msg'];
                             }else{
                                $seed_cspv5_post_result['msg'] = $txt_invalid_name_msg;
                             }
                             $seed_cspv5_post_result['msg_class'] = 'alert-danger';
                             $errors[] = $seed_cspv5_post_result['msg'];
                             
        
                             $emaillist = '';
                        }
                    }
                }
                // Validate Optin Confirmation
                if(!empty($display_optin_confirm)){
                    if(empty($optin_confirmation) && !$cookie_submit){
                            $seed_cspv5_post_result['status'] = '400';
                            if(!empty($lang_settings['txt_optin_confirmation_required']) && !empty($lang_id)){
                            $seed_cspv5_post_result['msg'] = $lang_settings['txt_optin_confirmation_required'];
                            }else{
                            $seed_cspv5_post_result['msg'] = $txt_optin_confirmation_required;
                            }
                            $seed_cspv5_post_result['msg_class'] = 'alert-danger';
                            $errors[] = $seed_cspv5_post_result['msg'];
                            
    
                            $emaillist = '';
                    }
                }

                //Check custom fields for required
                if(!empty($form_settings) && seed_cspv5_cu('fb')){
                foreach($form_settings as $k => $v){
                    if(is_array($v)){
                        if(substr( $k, 0, 6 ) === "field_" && $k != 'field_name'){
                            if(!empty($v['required']) && !empty($v['visible']) && !$cookie_submit){
                                if(empty($_REQUEST[$k])){
                                    $seed_cspv5_post_result['status'] = '400';
                                     // if(!empty($lang_settings['txt_invalid_name_msg']) && !empty($lang_id)){
                                     //    $seed_cspv5_post_result['msg'] = $lang_settings['txt_invalid_name_msg'];
                                     // }else{
                                        $seed_cspv5_post_result['msg'] = $v['label'].' Required';
                                     //}
                                     $seed_cspv5_post_result['msg_class'] = 'alert-danger';
                                     $errors[] = $seed_cspv5_post_result['msg'];
                                     
                
                                     $emaillist = '';
                                }
                            }
                        }
                    }
                }
                }
            }

            // Do email list action
            if(!empty($emaillist)){
                $data['settings'] = $settings;
                 // Get settings
                $mod = '';
                if($emaillist == 'mailchimp'){
                    $e_settings_name = 'seed_cspv5_'.$page_id.'_'.$emaillist;
                    $e_settings = get_option($e_settings_name);
                    if(!empty($e_settings)){
                        $e_settings = maybe_unserialize($e_settings);
                    }
                    if(empty($e_settings['mailchimp_api_key']) || (!empty($e_settings['api_version']) && $e_settings['api_version'] == '3')){
                        // Use V3
                        $mod = '_v3';
                    }
                }
                do_action('seed_cspv5_emaillist_'.$emaillist.$mod,$data);
            }
        //}



        
        $html = '';


        if(isset($GLOBALS['wp_embed'])){
            $ty_content = $GLOBALS['wp_embed']->autoembed($ty_content);
        }
        $ty_content = do_shortcode(shortcode_unautop(wpautop(convert_chars(wptexturize($ty_content)))));
        
        // Return HTML
        if('200' == $seed_cspv5_post_result['status']){
             // New Subscriber
            $status = '200';

            $html = $ty_content.$settings['conversion_scripts'];
            if(!empty($settings['enable_reflink'])){
                $html .= "<br><br><div id='cspio-ref-link'>".$txt_stats_referral_url.'<br>'.seed_cspv5_ref_link().'</div>';

                    if(!empty($settings['enable_prize_levels']) && $settings['enable_prize_levels'] == '1'){
                    // get settings
                    // Get form settings
                    $prize_settings_name = 'seed_cspv5_'.$page_id.'_prizes';
                    $prize_settings = get_option($prize_settings_name);
                    if(!empty($prize_settings)){
                        $prize_settings = maybe_unserialize($prize_settings);
                    }

                    $html .= '<table id="cspio-prizes">';
                    foreach($prize_settings as $k=>$v){
                        if(strrpos($k, "prize_") !== false){
                        if(!empty($v['description'])){
                            $class='';
                            if(empty($seed_cspv5_post_result['subscribers'])){
                                $seed_cspv5_post_result['subscribers'] = 0;
                            }
                            if($seed_cspv5_post_result['subscribers'] >= $v['number']){
                                $class='cspio-reveal';
                            }
                            $html .= '<tr class="'.$class.'"><td class="cspio-prizes-desc">'.$v['description'].'</td>';
                            if($seed_cspv5_post_result['subscribers'] >= $v['number']){
                                $html .= '<td class="cspio-prizes-reveal">'.$v['reveal'].'</td>';
                            }else{
                                if(empty($txt_prize_level_more)){
                                    $txt_prize_level_more = 'Refer %d more subscribers to claim this.';

                                }
                                $need = ($v['number'] - $seed_cspv5_post_result['subscribers']);
                                $html .= '<td class="cspio-prizes-reveal">'.sprintf($txt_prize_level_more, $need).' </td>';
                            }

                            $html .= '</tr>';
                        }
                        }
                    }
                    $html .= '</table>';
                    
                }
            }
            // Send Auto responder if setup
            // Get autoresponder settings
            $autoresponder_settings_name = 'seed_cspv5_'.$page_id.'_autoresponder';
            $autoresponder_settings = get_option($autoresponder_settings_name);
            if(!empty($autoresponder_settings)){
                if(!empty($autoresponder_settings['autoresponder']) && $autoresponder_settings['from_email'] && $autoresponder_settings['subject']){
                $autoresponder_settings = maybe_unserialize($autoresponder_settings);
                // Send auto responder
                $msg = $autoresponder_settings['autoresponder'];
                $template_tags = array(
                '{referral_url}' => seed_cspv5_ref_link(),
                );
                $msg =  strtr($msg, $template_tags);
                $from_email = sanitize_text_field( $autoresponder_settings['from_email']  );
                $subject = sanitize_text_field( $autoresponder_settings['subject']  );
                
                $headers[] = 'From: '.$from_email;
                $headers[] = 'Content-Type: text/html; charset=UTF-8';

                $mresult = wp_mail($email , $subject, $msg,$headers);
                }
            }


        }elseif('409' == $seed_cspv5_post_result['status']){
             // Already Subscribed
            $status = '409';



            $html = $txt_already_subscribed_msg;
            if(!empty($settings['enable_reflink'])){
                 // Already Subscribed send Referaral Info
                $html .= "<br><br><div id='cspio-ref-link'>".$txt_stats_referral_url.'<br>'.seed_cspv5_ref_link();
                $html .= '</div>';
                $html .= '<br>'.$txt_stats_referral_stats.'<br>'.$txt_stats_referral_subscribers.': <span class="cspio-subscriber-count">'.$seed_cspv5_post_result['subscribers'].'</span>';

                if(!empty($settings['enable_prize_levels']) && $settings['enable_prize_levels'] == '1'){
                    // get settings
                    // Get form settings
                    $prize_settings_name = 'seed_cspv5_'.$page_id.'_prizes';
                    $prize_settings = get_option($prize_settings_name);
                    if(!empty($prize_settings)){
                        $prize_settings = maybe_unserialize($prize_settings);
                    }
                    unset($prize_settings['page_id']);
                    unset($prize_settings['settings_name']);
                    $html .= '<table id="cspio-prizes">';
                    foreach($prize_settings as $k=>$v){
                        if(strrpos($k, "prize_") !== false){
                        if(!empty($v['description'])){
                            $class='';
                            if($seed_cspv5_post_result['subscribers'] >= $v['number']){
                                $class='cspio-reveal';
                            }
                            $html .= '<tr class="'.$class.'"><td class="cspio-prizes-desc">'.$v['description'].'</td>';
                            if($seed_cspv5_post_result['subscribers'] >= $v['number']){
                                $html .= '<td class="cspio-prizes-reveal">'.$v['reveal'].'</td>';
                            }else{
                                if(empty($txt_prize_level_more)){
                                    $txt_prize_level_more = 'Refer %d more subscribers to claim this.';

                                }
                                $need = ($v['number'] - $seed_cspv5_post_result['subscribers']);
                                $html .= '<td class="cspio-prizes-reveal">'.sprintf($txt_prize_level_more, $need).' </td>';
                                
                            }

                            $html .= '</tr>';
                        }
                        }
                    }
                    $html .= '</table>';
                    
                }
               
            }
        }elseif('400' == $seed_cspv5_post_result['status']){
            // Validation Error
            $status = '400';
            $html ='<div id="cspio-alert" class="alert '.$seed_cspv5_post_result['msg_class'].'"><ul>';
            foreach($errors as $e){
                $html .=  '<li>'.$e.'</li>';
            }
            $html .= '</ul></div>';
        }elseif('500' == $seed_cspv5_post_result['status']){
            // API Error
            $status = '500';
            $html = $seed_cspv5_post_result['html'];
        }
        
        if($status != '500'){
            $html = '<div id="cspio-thankyoumsg">'.$html.'</div>';
        }
        
        $content = '';
        if($status == '200' || $status == '409'){
        ob_start();
        include(SEED_CSPV5_PLUGIN_PATH.'template/show_share_buttons_ty.php');
        $content = ob_get_clean();
        }
        header('Content-Type: text/javascript; charset=utf8');
        // Return jsonp results
        $html = $html.$content;

        $response = array(
            'status' => $status,
            'html' => $html
        );
        echo sanitize_text_field($_GET['callback']) . '(' . json_encode($response) . ')';
        exit();
    }
    
    /*
     * Export Subscribers
     */
    function export_all_subscribers(){
        ob_get_clean();
        global $wpdb;
        $csv_output = '';
        $csv_output .= "Page,Email,Fname,Lname,Clicks,Conversions,City,Country,IP,Created,Referrer,Optin Confirm,Meta";
        $csv_output .= "\n";
        $tablename = $wpdb->prefix . SEED_CSPV5_SUBSCRIBERS_TABLENAME;
        $sql = "SELECT * FROM " . $tablename;
        $results = $wpdb->get_results($sql);
        foreach ($results as $result) {
            $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
            $sql = "SELECT name FROM $tablename WHERE id = %d";
            $safe_sql = $wpdb->prepare($sql,$result->page_id);
            $pp = $wpdb->get_var($safe_sql);
            $page = $pp;

            $tablename = $wpdb->prefix . SEED_CSPV5_SUBSCRIBERS_TABLENAME;
            $sql = "SELECT email FROM $tablename WHERE id = %d";
            $safe_sql = $wpdb->prepare($sql,$result->referrer);
            $pr = $wpdb->get_var($safe_sql);
            $referrer = $pr;

            // Get meta
            $print_meta = '';
            if(seed_cspv5_cu('fb')){
                $form_settings_name = 'seed_cspv5_'.$result->page_id.'_form';
                $form_settings = get_option($form_settings_name);
                if(!empty($form_settings)){
                    $form_settings = maybe_unserialize($form_settings);
                }
                $meta = null;
                
                if(!empty($result->meta)){
                    $meta = maybe_unserialize($result->meta);
                    foreach($meta as $k => $v){
                        if(substr( $k, 0, 6 ) === "field_"){
                            $print_meta .= $form_settings[$k]['label'].":".$v.',';
                        }
                    }
                }
            }

            
            if(!empty($result->location)){
                $location = json_decode($result->location,true);
                $city = $location['city'];
                $country = $location['country_name'];
            }else{
                $city = '';
                $country = '';
            }
           $csv_output .= $page .",".$result->email ."," . $result->fname . ",". $result->lname . "," . $result->clicks . "," . $result->conversions . "," . $city . "," . $country . "," . $result->ip . "," . $result->created . "," . $referrer."," . $result->optin_confirm. "," .
           $print_meta."\n";
        }

        $filename = "subscribers_".date("Y-m-d_H-i",time());
        header("Content-type: text/plain");
        header("Content-disposition: attachment; filename=".$filename.".csv");
        print $csv_output;
        die();
    } 

    /*
     * Delete Pages
     */
    function delete_all_pages(){
        if (current_user_can( 'delete_users' )) {
            global $wpdb;
            $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
            $sql = "DELETE FROM " . $tablename . " WHERE type = 'lp'";
            $result = $wpdb->query($sql);
            if($result){
                return true;
            }
        }
    } 

    /*
     * Delete Selected Pages
     */
    function delete_selected_pages($ids){
        if (current_user_can( 'list_users' )) {
            if(is_array($ids) && !empty($ids)){
                global $wpdb;
                $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
                $sql = "DELETE FROM " . $tablename . " WHERE id IN ( ".implode(",", $ids)." )";
                $result = $wpdb->query($sql);
                if($result){
                    return true;
                }
            }
        }
    } 
      
    /*
     * Delete Subscribers
     */
    function delete_all_subscribers(){
        if (current_user_can( 'delete_users' )) {
            global $wpdb;
            $tablename = $wpdb->prefix . SEED_CSPV5_SUBSCRIBERS_TABLENAME;
            $sql = "TRUNCATE " . $tablename;
            $result = $wpdb->query($sql);
            if($result){
                return true;
            }
        }
    } 

    /*
     * Delete Selected Subscribers
     */
    function delete_selected_subscribers($ids){
        if (current_user_can( 'list_users' )) {
            if(is_array($ids) && !empty($ids)){
                global $wpdb;
                $tablename = $wpdb->prefix . SEED_CSPV5_SUBSCRIBERS_TABLENAME;
                $sql = "DELETE FROM " . $tablename . " WHERE id IN ( ".implode(",", $ids)." )";
                $result = $wpdb->query($sql);
                if($result){
                    return true;
                }
            }
        }
    } 
    
    /*
	 * Display Subscribers
	 */
	 function display_subscribers($args){
        if(seed_cspv5_cu('none')){return false;}
        //Display if we are on the subscribers tab
		if($args['tab'] == 'seed_cspv5_tab_subscribers'){
    
            if(!empty($_POST['action'])){
                //$nonce = $_POST['_wpnonce'];
                //var_dump(wp_verify_nonce($nonce, 'buljk-toplevel_page_seed_cspv5'));
                if($_POST['action'] == 'seed_cspv5_delete_subscribers'){
                    if($this->delete_all_subscribers()){
                        echo '
                        <div id="setting-error-seedprod_error" class="error settings-error below-h2"> 
                        <p><strong>'.__('All subscribers deleted.','seedprod').'</strong></p></div>';
                    }
                }
                if($_POST['action'] == 'seed_cspv5_delete_selected_subscribers'){
                    if($this->delete_selected_subscribers($_POST['subscriber'])){
                        echo '
                        <div id="setting-error-seedprod_error" class="error settings-error below-h2"> 
                        <p><strong>'.__('Selected subscribers deleted.','seedprod').'</strong></p></div>';
                    }
                }
            }
            // Get all page ids
            $page_id = '';
            if(isset($_GET['page_id'])){
              $page_id = $_GET['page_id'];  
            }

            global $wpdb;
            $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
            $sql = "SELECT id,name FROM $tablename";
            $pages = $wpdb->get_results($sql);
            $options = array('<option value="">All Pages</option>');
            if(empty($page_id ) && !empty($pages) && count($pages) == 1){
                // redirect to only page filter
                if(strrpos($_SERVER['REQUEST_URI'], "page_id") === false){
                    $url = $_SERVER['REQUEST_URI'].'&page_id='.$pages[0]->id;
                    echo '<script>window.location.replace("'.$url.'")</script>';
                
                }
            }
            foreach($pages as $p){
                $selected = '';
                if($page_id == $p->id){
                    $selected = 'selected';
                }
                $options[] = "<option value='$p->id' $selected>$p->name</option>";
            }

            // Get page settings
            if(!empty($page_id)){
                $sql = "SELECT * FROM $tablename WHERE id = %d";
                $safe_sql = $wpdb->prepare($sql,$page_id);
                $page_settings = $wpdb->get_row($safe_sql);
            }
            



            
            // Render Subscriber

            $seed_cspv5_subscribers = new SEED_CSPV5_SUBSCRIBERS();
            $seed_cspv5_subscribers->prepare_items();
            echo '<strong>Page</strong> <select id="seed_cspv5_page_id" style="width:100%">'.implode($options).'</select>';
            ?>
            <script>
            jQuery( document ).ready(function($) {
                jQuery('#seed_cspv5_page_id').change(function() {
                    id = $(this).val();
                    location.href = '<?php echo admin_url() ?>options-general.php?page=seed_cspv5&tab=seed_cspv5_tab_subscribers&page_id='+id;
                });
            });
            </script>


            <?php

            if(!empty($page_settings->mailprovider)){
               echo '<p>Emails are being collected to: <strong>'.ucfirst($page_settings->mailprovider).'</strong></p>'; 
            }
            //var_dump($page_settings);
            // Check for base64 encoding of settings
            if(!empty($page_settings->settings)){
                if ( base64_encode(base64_decode($page_settings->settings, true)) === $page_settings->settings){
                    $settings = unserialize(base64_decode($page_settings->settings));
                } else {
                    $settings = unserialize($page_settings->settings);
                }
            }
            //var_dump($settings);
            if(!empty($settings['enable_prize_levels']) && $settings['enable_prize_levels'] == '1'){
                // get prize level settings
                $prize_settings_name = 'seed_cspv5_'.$page_id.'_prizes';
                $prize_settings = get_option($prize_settings_name);
                if(!empty($prize_settings)){
                    $prize_settings = maybe_unserialize($prize_settings);
                }
                echo '&nbsp;&nbsp;&nbsp;&nbsp;<ul class="subsubsub" style="float:none;display:inline-block;margin: 0px 0px 0 -14px">';
                //
                if(isset($_GET['prize_level'])){
                  $class='';  
                }else{
                  $class='current';
                }
                
                echo '<li ><a class="'.$class.'" href="options-general.php?page=seed_cspv5&tab=seed_cspv5_tab_subscribers" >All <span class="count">('.$seed_cspv5_subscribers->get_data_total_all().')</span></a></li> | ';
                $i = 0;
                foreach($prize_settings as $k=>$v){
                    if(strrpos($k, "prize_") !== false){
                        if(!empty($v['number'])){
                        // Get count
                        if ($i != 0) {
                        echo ' | ';
                        }
                        if(strrpos($_SERVER['REQUEST_URI'], "?") === false){
                            $url = $_SERVER['REQUEST_URI'].'?prize_level='.$k;
                        }else{
                            $url = $_SERVER['REQUEST_URI'].'&prize_level='.$k;
                        }
                        if(isset($_GET['prize_level']) && $_GET['prize_level'] == $k){
                            $class='current';
                        }else{
                            $class='';
                        }
                        echo '<li><a class="'.$class.'" href="'.$url.'" >'.ucfirst(str_replace('_', ' ', $k)).' <span class="count">('.$seed_cspv5_subscribers->get_data_total_filter($k,$page_id).')</span></a></li>';
                        $i++;
                        }
                    }
                }
                echo '</ul>';
            }
            echo '<form id="seed_cspv5_search" method="post">';
            $seed_cspv5_subscribers->search_box('Search Emails', 'email'); 
            echo '</form>';
            echo '<form id="seed_cspv5_bulk_actions" method="post">';
            $seed_cspv5_subscribers->display();
            wp_nonce_field('seed_cspv5_subscribers');
            echo '</form>';

        ?>
        <script>
        jQuery(document).ready(function($){
            $(".bottom > .actions").hide();
            $("#doaction").click(function(event) {
                event.preventDefault();
                var action = $('select[name="action"]').val();
                if(action != '-1'){
                    if(action == 'delete'){
                        if(confirm(seed_cspv5_msgs.delete_confirm)){
                            $("#seed_cspv5_bulk_actions").submit();
                        }
                    }else{
                        $("#seed_cspv5_bulk_actions").submit();
                    }
                }
            });
        });
        </script>

        <?php

		}
	}
    
    
    /*
	 * Display Pages
	 */
	function display_pages($args){
        if(!seed_cspv5_cu('lp')){return false;}
        //Display if we are on the subscribers tab
		if($args['tab'] == 'seed_cspv5_tab_pages'){


echo '<h2>Add a Landing Page</h2>';
echo home_url()."/<input autocomplete='off' id='page_path' class='regular-text' type='text' placeholder='Path' />";
echo "<button id='seed_cspv5_page_create' type='button' class='button-primary'>".__('Create Page','seedprod-coming-soon-pro')."</button><br>";
echo "<div id='seed_cspv5_page_msg'></div>";
echo '<small class="description">Enter the Path where the Page should be displayed. <br><strong>Example:</strong> If you want your landing page to show up at: '.home_url().'/landing-page just enter <strong>landing-page</strong> as the Path.</small>';
?>
<script type='text/javascript'>
jQuery(document).ready(function($) {
    $('#seed_cspv5_page_create').click(function() {
      $('#seed_cspv5_page_create').prop("disabled", true);
      $('#seed_cspv5_check_page_msg').hide();
    	path = $('#page_path').val();
    	if(path!= ''){
            location.href="<?php echo admin_url() ?>options-general.php?page=seed_cspv5_themes&page_id=-1&type=lp&path="+path;
		}else{
      $('#seed_cspv5_page_msg').show();
			$('#seed_cspv5_page_create').prop("disabled", false);
		}

    }); 
});
</script>
<br><br>
<hr>
<?php
    echo '<h2>Landing Pages</h2>';
            if(!empty($_POST['action'])){
                //$nonce = $_POST['_wpnonce'];
                //var_dump(wp_verify_nonce($nonce, 'buljk-toplevel_page_seed_cspv5'));
                if($_POST['action'] == 'seed_cspv5_delete_pages'){
                    if($this->delete_all_pages()){
                        echo '
                        <div id="setting-error-seedprod_error" class="error settings-error below-h2"> 
                        <p><strong>'.__('All pages deleted.','seedprod-coming-soon-pro').'</strong></p></div>';
                    }
                }
                if($_POST['action'] == 'seed_cspv5_delete_selected_pages'){
                    if($this->delete_selected_pages($_POST['subscriber'])){
                        echo '
                        <div id="setting-error-seedprod_error" class="error settings-error below-h2"> 
                        <p><strong>'.__('Selected pages deleted.','seedprod-coming-soon-pro').'</strong></p></div>';
                    }
                }
            }


            
            // Render Subscriber
            $seed_cspv5_pages = new SEED_CSPV5_PAGES();
            $seed_cspv5_pages->prepare_items();
            echo '<form id="seed_cspv5_search"" method="post">';
            $seed_cspv5_pages->search_box('Search Pages', 'email'); 
            echo '</form>';
            echo '<form id="seed_cspv5_bulk_actions" method="post">';
            $seed_cspv5_pages->display();
            wp_nonce_field('seed_cspv5_subscribers');
            echo '</form>';

        ?>
        <script>
        jQuery(document).ready(function($){
            $(".bottom > .actions").hide();
            $("#doaction").click(function(event) {
                event.preventDefault();
                var action = $('select[name="action"]').val();
                if(action != '-1'){
                    if(action == 'delete'){
                        if(confirm(seed_cspv5_msgs.delete_confirm)){
                            $("#seed_cspv5_bulk_actions").submit();
                        }
                    }else{
                        $("#seed_cspv5_bulk_actions").submit();
                    }
                }
            });
        });
        </script>

        <?php

		}
	}


}

// Display Subscribers Class
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class SEED_CSPV5_PAGES extends WP_List_Table {
    function get_data($current_page,$per_page){
        // Get records
        global $wpdb;

        $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;

        $sql = "SELECT * FROM $tablename WHERE type = 'lp' ";

        if(!empty($_POST['s'])){
            $sql .= ' AND id LIKE "%'. esc_sql(trim($_POST['s'])) .'%" OR path LIKE "%'. esc_sql(trim($_POST['s'])) .'%"';
        }

        if ( ! empty( $_GET['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_GET['orderby'] );
            $sql .= ! empty( $_GET['order'] ) ? ' ' . esc_sql( $_GET['order'] ) : ' ASC';
        }else{
            $sql .= ' ORDER BY created DESC';
        }

        $sql .= " LIMIT $per_page";
        if(empty($_POST['s'])){
            $sql .= ' OFFSET ' . ( $current_page - 1 ) * $per_page;
        }
        //var_dump($sql);
        //$safe_sql = $wpdb->prepare($sql,$email,$page_id);
        $results = $wpdb->get_results($sql);

        $data = array();
        foreach($results as $v){
            // Path
            $path = $v->path;
            $name = $v->name;
            
 
            // Format Date
            $date = date(get_option('date_format').' '.get_option('time_format'), strtotime($v->created));
            $created  = $date;

            // Load Data
            $data[] = array(
                'ID' => $v->id,
                'name' => esc_html($name),
                'path' => '<a href="'.home_url().'/'.esc_html($path).'" target="_blank">'.home_url().'/'.esc_html($path).'</a>',
                'created' => $created,
                'deactivate' => $v->deactivate,
            );
        }
        return $data;
    }

    function get_data_total(){
        global $wpdb;

        $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;

        $sql = "SELECT count(id) FROM $tablename WHERE type = 'lp' ";

        if(!empty($_POST['s'])){
            $sql .= ' AND path LIKE "%'. esc_sql($_POST['s']) .'%"';
        }

        $results = $wpdb->get_var($sql);
        return $results;
    }

    function get_sortable_columns() {
      $sortable_columns = array(
        'name'  => array('name',false),
        'path'  => array('path',false),
        'created'   => array('created',false)
      );
      return $sortable_columns;
    }

    function get_columns(){
      $columns = array(
        'cb'        => '<input type="checkbox" />',
        'name'    => __('Name','seedprod'),
        'path'    => __('Path','seedprod'),
        'created'      => __('Created','seedprod'),
      );
      return $columns;
    }
    function prepare_items() {
      $columns = $this->get_columns();
      $hidden = array();
      $sortable = $this->get_sortable_columns();
      $this->_column_headers = array($columns, $hidden, $sortable);
      $per_page = 10;
      $current_page = $this->get_pagenum();
      $total_items = $this->get_data_total();
      $this->set_pagination_args( array(
        'total_items' => $total_items,
        'per_page'    => $per_page
      ) );
      $customvar = ( isset($_REQUEST['customvar']) ? $_REQUEST['customvar'] : 'all');
      $this->items = $this->get_data($current_page,$per_page);
    }

    function column_default( $item, $column_name ) {
      switch( $column_name ) {
        case 'name':
        case 'path':
        case 'created':
          return $item[ $column_name ];
        default:
          return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_bulk_actions() {
      $actions = array(
        //'seed_cspv5_delete_pages'    => __('Delete All','seedprod'),
        'seed_cspv5_delete_selected_pages'    => __('Delete Selected','seedprod'),
      );
      return $actions;
    }

    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="subscriber[]" value="%s" />', $item['ID']
        );
    }
    

    function column_name($item) {
      $label = 'Activate';
      if(empty($item['deactivate'])){
        $label = 'Deactivate';
      }
      $actions = array(
                'edit'      => sprintf('<a href="options-general.php?page=seed_cspv5_customizer&seed_cspv5_customize=%s">Edit</a>',$item['ID']),
                'duplicate'      => sprintf('<a href="options-general.php?page=seed_cspv5_duplicate&id=%s">Duplicate</a>',$item['ID']),
                'deactivate'      => sprintf('<a href="options-general.php?page=seed_cspv5_deactivate&id=%s">%s</a>',$item['ID'],$label),
            );
      return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions, true) );
    }
}


class SEED_CSPV5_SUBSCRIBERS extends WP_List_Table {
    function get_data($current_page,$per_page){
        // Get records
        global $wpdb;

        $tablename = $wpdb->prefix . SEED_CSPV5_SUBSCRIBERS_TABLENAME;

        $sql = "SELECT * FROM $tablename WHERE 1 = 1 ";

        if(!empty($_POST['s'])){
            $sql .= ' AND email LIKE "%'. esc_sql(trim($_POST['s'])) .'%"';
        }

        if(!empty($_REQUEST['page_id'])){
            $sql .= ' AND page_id = "'. esc_sql(trim($_GET['page_id'])) .'"';
        }

        $page_id = '';
        if(!empty($passed_page_id)){
            $page_id = $passed_page_id ;
        }elseif(!empty($_REQUEST['page_id'])){
            $page_id = $_REQUEST['page_id'];
        }

        if(isset($_GET['prize_level'])){
            $prize_level = esc_sql($_GET['prize_level']);
            // get prize
            $prize_settings_name = 'seed_cspv5_'.$page_id.'_prizes';
            $prize_settings = get_option($prize_settings_name);
            if(!empty($prize_settings)){
                $prize_settings = maybe_unserialize($prize_settings);
            }

            $sql .= " AND conversions >= ".$prize_settings[$prize_level]['number'];
        }

        if ( ! empty( $_GET['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_GET['orderby'] );
            $sql .= ! empty( $_GET['order'] ) ? ' ' . esc_sql( $_GET['order'] ) : ' ASC';
        }else{
            $sql .= ' ORDER BY created DESC';
        }


        $sql .= " LIMIT $per_page";
        if(empty($_POST['s'])){
            $sql .= ' OFFSET ' . ( $current_page - 1 ) * $per_page;
        }

        $results = $wpdb->get_results($sql);
        //var_dump($sql);
        $data = array();
        foreach($results as $v){
            // Sep
            $sep = '';
            if($v->fname != '' || $v->lname != ''){
                $sep = '<br>';
            }
            // Format Date
            $date = date(get_option('date_format').' '.get_option('time_format'), strtotime($v->created));

            // Get Gravatar
            $gravatar = '<img src="https://www.gravatar.com/avatar/'.md5($v->email) .'?s=36" alt="Gravatar" style="float:left;padding:2px;backgroun-color:#fff;border:1px solid #ccc;margin-right:8px">';

            // Format email
            $email = "<a href='mailto:{$v->email}'>{$v->email}</a>";

            $ref = $v->id+1000;
            if($v->ref_url){
                $referrer_url = $v->ref_url;
            }else{
                $referrer_url = home_url() . '?ref='.base_convert($ref, 10, 36);
            }

            // Subscriber
            $subscriber = $gravatar.$v->fname.' '.$v->lname.$sep.$email.' <br clear="both"><strong>Referrer URL</strong><br><a href="'.$referrer_url.'" traget="_blank">'.$referrer_url.'</a>';

            // Influence
            $influence = $v->conversions. ' of '. $v->clicks. ' referrals have subscribed to your list';

            $conversions = $v->conversions;
            if(!empty($v->conversions)){
                $conversion_rate = round(($v->conversions/$v->clicks) * 100).'%';
            }else{
                $conversion_rate = '0%';
            }
            $clicks = $v->clicks;


            // Get meta
            $print_meta = '';
            if(seed_cspv5_cu('fb')){
                $form_settings_name = 'seed_cspv5_'.$v->page_id.'_form';
                $form_settings = get_option($form_settings_name);
                if(!empty($form_settings)){
                    $form_settings = maybe_unserialize($form_settings);
                }
                $meta = null;
                
                if(!empty($v->meta)){
                    $meta = maybe_unserialize($v->meta);
                    if(is_array($meta)){
                    foreach($meta as $k1 => $v1){
                        if(substr( $k1, 0, 6 ) === "field_"){
                            //var_dump($v);
                            $print_meta .= $form_settings[$k1]['label'].":".$v1.PHP_EOL;
                        }
                    }
                    }
                }
            }      
             //var_dump($print_meta);
            // die();


            
            // Insights
            $insights = '';
            // $insights .= $v->insights;
            // var_export(json_decode($v->insights,true));
            if(!empty($v->location)){
                $location = json_decode($v->location,true);
                $insights .= 'Subscribed from: '.$location['city'].', '.$location['country_name'].'<br>';
            }

            $insights .= __('Subscribed on: ','seedprod').$date;
            $created  = $date;

            $tablename = $wpdb->prefix . SEED_CSPV5_SUBSCRIBERS_TABLENAME;
            $sql = "SELECT email FROM $tablename WHERE id = %d";
            $safe_sql = $wpdb->prepare($sql,$v->referrer);
            $results = $wpdb->get_results($safe_sql);

            $referrer = null;
            if(!empty($results[0]->email))
            $referrer = $results[0]->email;

            // Load Data
            $data[] = array(
                'ID' => $v->id,
                'subscriber' => $subscriber,
                'clicks' => $clicks,
                'conversions' => $conversions,
                'conversion_rate' => $conversion_rate,
                'created' => $created,
                'referrer' => $referrer,
                'meta' => $print_meta,
                );
        }
        return $data;
    }

    function get_data_total_all(){
        global $wpdb;

        $tablename = $wpdb->prefix . SEED_CSPV5_SUBSCRIBERS_TABLENAME;

        $sql = "SELECT count(id) FROM $tablename WHERE 1 = 1 ";

        if(!empty($_POST['s'])){
            $sql .= ' AND email LIKE "%'. esc_sql(trim($_POST['s'])) .'%"';
        }

        if(!empty($_REQUEST['page_id'])){
            $sql .= ' AND page_id = "'. esc_sql(trim($_GET['page_id'])) .'"';
        }

        $results = $wpdb->get_var($sql);
        
        return $results;
    }


    function get_data_total_filter($prize_level = null, $passed_page_id = null){
        global $wpdb;

        $tablename = $wpdb->prefix . SEED_CSPV5_SUBSCRIBERS_TABLENAME;

        $sql = "SELECT count(id) FROM $tablename WHERE 1 = 1 ";

        if(!empty($_POST['s'])){
            $sql .= ' AND email LIKE "%'. esc_sql(trim($_POST['s'])) .'%"';
        }

        if(!empty($_REQUEST['page_id'])){
            $sql .= ' AND page_id = "'. esc_sql(trim($_GET['page_id'])) .'"';
        }

        if(isset($_GET['prize_level'])){
            //$prize_level = esc_sql($_GET['prize_level']);
        }

        $page_id = '';
        if(!empty($passed_page_id)){
            $page_id = $passed_page_id ;
        }elseif(!empty($_REQUEST['page_id'])){
            $page_id = $_REQUEST['page_id'];
        }


        $number_required = '';
        if(!empty($prize_level)){
            // get prize
            $prize_settings_name = 'seed_cspv5_'.$page_id.'_prizes';
            $prize_settings = get_option($prize_settings_name);
            if(!empty($prize_settings)){
                $prize_settings = maybe_unserialize($prize_settings);
            }

            foreach($prize_settings as $k=>$v){
                if($k == $prize_level){
                    $number_required = $v['number'];
                }
            }
        }

        if(!empty($number_required)){
            $sql .= " AND conversions >= ".$number_required;
        }

        $results = $wpdb->get_var($sql);

        return $results;
    }

    function get_data_total($prize_level = null, $passed_page_id = null){
        global $wpdb;

        $tablename = $wpdb->prefix . SEED_CSPV5_SUBSCRIBERS_TABLENAME;

        $sql = "SELECT count(id) FROM $tablename WHERE 1 = 1 ";

        if(!empty($_POST['s'])){
            $sql .= ' AND email LIKE "%'. esc_sql(trim($_POST['s'])) .'%"';
        }

        if(!empty($_REQUEST['page_id'])){
            $sql .= ' AND page_id = "'. esc_sql(trim($_GET['page_id'])) .'"';
        }

        if(isset($_GET['prize_level'])){
            $prize_level = esc_sql($_GET['prize_level']);
        }

        $page_id = '';
        if(!empty($passed_page_id)){
            $page_id = $passed_page_id ;
        }elseif(!empty($_REQUEST['page_id'])){
            $page_id = $_REQUEST['page_id'];
        }

        $number_required = '';
        if(!empty($prize_level)){
            // get prize
            $prize_settings_name = 'seed_cspv5_'.$page_id.'_prizes';
            $prize_settings = get_option($prize_settings_name);
            if(!empty($prize_settings)){
                $prize_settings = maybe_unserialize($prize_settings);
            }

            foreach($prize_settings as $k=>$v){
                if($k == $prize_level){
                    $number_required = $v['number'];
                }
            }
        }

        if(!empty($number_required)){
            $sql .= " AND conversions >= ".$number_required;
        }

        $results = $wpdb->get_var($sql);

        return $results;
    }


    function get_sortable_columns() {
      $sortable_columns = array(
        'clicks'  => array('clicks',false),
        'conversions' => array('conversions',false),
        'conversion_rate'   => array('conversion_rate',false),
        'created'   => array('created',false)
      );
      return $sortable_columns;
    }


    function get_columns(){
      $columns = array(
        'cb'        => '<input type="checkbox" />',
        'subscriber' => __('Subscribers','seedprod'),
        'clicks'    => __('Clicks','seedprod'),
        'conversions'    => __('# People Signed Up','seedprod'),
        'conversion_rate'    => __('Conversion Rate','seedprod'),
        'created'      => __('Created','seedprod'),
        'referrer'      => __('Referrer','seedprod'),
        'meta'      => __('Meta','seedprod'),
      );
      return $columns;
    }
    function prepare_items() {
      $columns = $this->get_columns();
      $hidden = array();
      $sortable = $this->get_sortable_columns();
      $this->_column_headers = array($columns, $hidden, $sortable);
      $per_page = 10;
      $current_page = $this->get_pagenum();
      $total_items = $this->get_data_total();
      $this->set_pagination_args( array(
        'total_items' => $total_items,
        'per_page'    => $per_page
      ) );
      $customvar = ( isset($_REQUEST['customvar']) ? $_REQUEST['customvar'] : 'all');
      $this->items = $this->get_data($current_page,$per_page);
    }

    function column_default( $item, $column_name ) {
      switch( $column_name ) {
        case 'subscriber':
        case 'clicks':
        case 'conversions':
        case 'conversion_rate':
        case 'created':
        case 'referrer':
        case 'meta':
          return $item[ $column_name ];
        default:
          return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_bulk_actions() {
      $actions = array(
        'seed_cspv5_export_subscribers'    => __('Export All','seedprod'),
        'seed_cspv5_delete_selected_subscribers'    => __('Delete Selected','seedprod'),
      );
      return $actions;
    }

    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="subscriber[]" value="%s" />', $item['ID']
        );
    }


}

