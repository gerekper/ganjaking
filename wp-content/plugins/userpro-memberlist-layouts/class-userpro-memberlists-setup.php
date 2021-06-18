<?php
/*
Plugin Name: MemberList Layouts for UserPro
Plugin URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
Description: Unique layouts of the UserPro Memberlist
Version: 1.0
Author: Deluxe Themes
Author URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
*/

if(!defined('ABSPATH')) {exit;}

define('userpro_memberlists_url',plugin_dir_url(__FILE__ ));
define('userpro_memberlists_path',plugin_dir_path(__FILE__ ));

if(!class_exists('userpro_memberlists_setup') ) :

    class userpro_memberlists_setup {
    private static $_instance;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        global $userpro;
        $this->define_constant();
        $this->includes_file();
        add_action('init',array($this,'userpro_memberlists_init'));
        add_action('wp_enqueue_scripts',array($this,'userpro_memberlists_scripts'));

    }

    function userpro_memberlists_init() {
        load_plugin_textdomain('userpro-memberlists', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    function userpro_memberlists_scripts(){
        wp_enqueue_script( 'memberlists-scripts', userpro_memberlists_url. 'scripts/memberlists-scripts.js', array('userpro_memberlists_scripts'),'', true );
            //$userpro_limit_tags = userpro_tags_get_option('limit_tags' );
            //wp_localize_script('tags-scripts', 'userpro_tags_script_data', array( 'userpro_limit_tags' => $userpro_limit_tags ) );
    }
    
    function includes_file() {
        if(is_multisite()){
            require_once USERPRO_PLUGIN_DIR . "/functions/api.php";
        }
        require_once(UPML_PLUGIN_DIR.'functions/defaults.php');		
        require_once(UPML_PLUGIN_DIR.'/admin/admin.php');
        $activated_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
        if( in_array('userpro/index.php', $activated_plugins) ){
            require_once(USERPRO_PLUGIN_DIR.'/functions/memberlist-functions.php');
            require_once(USERPRO_PLUGIN_DIR.'/functions/user-functions.php');
            require_once UPML_PLUGIN_DIR.'/functions/shortcode-main.php';
            require_once(UPML_PLUGIN_DIR.'/functions/hooks-actions.php');
        }
        else{
            add_action( 'admin_notices', array($this, 'UPML_userpro_activation_notices') );
            return 0;
        }
        //include_once(UPR_PLUGIN_DIR.'functions/upml-ajax.php');		
    }

    public function define_constant(){
        if ( !defined( 'USERPRO_PLUGIN_URL' ) ){
            define('USERPRO_PLUGIN_URL',WP_PLUGIN_URL.'/userpro/');
        }
        if ( !defined( 'USERPRO_PLUGIN_DIR' ) ){
            define('USERPRO_PLUGIN_DIR',WP_PLUGIN_DIR.'/userpro/');
        }	
        define('UPML_PLUGIN_URL',WP_PLUGIN_URL.'/userpro-memberlist-layouts/');
        define('UPML_PLUGIN_DIR',WP_PLUGIN_DIR.'/userpro-memberlist-layouts/');

    }	

    function UPML_userpro_activation_notices(){
            echo '<div class="error" role="alert"><p>Attention: User-Pro Memberlists requires User-Pro to be installed and activated.</p></div>';
            deactivate_plugins( plugin_basename( __FILE__ ) );
            return 0;
        }
    }
endif;

$UPML = userpro_memberlists_setup::instance();
?>
