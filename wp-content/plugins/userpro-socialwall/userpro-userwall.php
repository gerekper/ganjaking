<?php
/*
Plugin Name: Social Wall Add-on for UserPro
Plugin URI: http://codecanyon.net/item/social-wall-addon-for-userpro/9553858
Description: Allow users to post, comment and interact with each other.
Version: 4.4
Author: Deluxe Themes
Author URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
*/

?>
<?php
if(!defined('ABSPATH')) {exit;}

if(!class_exists('UP_userPro_userwall')) :

class UP_userPro_userwall {

	private static $_instance;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct(){

						$this->define_constant();
						if(is_multisite()){
							require_once SUSERPRO_PLUGIN_DIR . "/functions/api.php";
						}

							if (file_exists(UPS_PLUGIN_DIR.'/functions/shortcode-main.php'))
							require_once UPS_PLUGIN_DIR.'/functions/shortcode-main.php';
							if (file_exists(UPS_PLUGIN_DIR.'/functions/user_function.php'))
							require_once UPS_PLUGIN_DIR.'/functions/user_function.php';
							if (file_exists(UPS_PLUGIN_DIR.'/functions/defaults.php'))
							require_once UPS_PLUGIN_DIR.'/functions/defaults.php';
							if (file_exists(UPS_PLUGIN_DIR.'/functions/hook-actions.php'))
							require_once UPS_PLUGIN_DIR.'/functions/hook-actions.php';

							if (is_admin()){
						foreach (glob(UPS_PLUGIN_DIR . 'admin/*.php') as $filename) { include $filename; }
						//require_once(UPS_PLUGIN_DIR . 'admin/wp-updates-class-socialwall.php');
						//new WPUpdatesPluginUpdater_1110( 'http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__));
						}
                                                add_filter( 'ajax_query_attachments_args', array( $this, 'filter_media' ) );
						add_action('wp_enqueue_scripts', array($this , 'load_styles') , 999);
						add_action('wp_enqueue_scripts', array($this,'load_js') , 999);
						add_action('wp_head',array($this,'pluginname_ajaxurl'));
						add_action('admin_head', array($this,'load_js') , 999);
                                                //add_filter('admin_init',array($this,'up_upload_media_permission'));

	}

	public function load_js(){

		wp_register_script('script_js', UPS_PLUGIN_URL.'scripts/userwall_script.js');
		wp_enqueue_script('script_js','','','',true);
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_register_script('userpro_m_share', UPS_PLUGIN_URL . 'scripts/sharebutton.js');
		wp_enqueue_script('userpro_m_share','','','',true);
                wp_enqueue_media();

	}
	public function load_styles(){

		wp_register_style('userwall', UPS_PLUGIN_URL.'css/userpro_userwall.css');
		wp_enqueue_style('userwall');
		wp_register_style('fontowsome', UPS_PLUGIN_URL.'assets/font-awesome-4.2.0/css/font-awesome.min.css');
		wp_enqueue_style('fontowsome');
	}


	function pluginname_ajaxurl() {
		?>
	<script type="text/javascript">
	var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	 var total=0;
	var userwall_upload_path='<?php echo UPS_PLUGIN_URL."lib/fileupload/fileupload.php";?>';

	</script>
	<?php
	}



	public function define_constant(){

		define('SUSERPRO_PLUGIN_URL',WP_PLUGIN_URL.'/userpro/');
		define('SUSERPRO_PLUGIN_DIR',WP_PLUGIN_DIR.'/userpro/');

		define('UPS_PLUGIN_URL',WP_PLUGIN_URL.'/userpro-socialwall/');
		define('UPS_PLUGIN_DIR',WP_PLUGIN_DIR.'/userpro-socialwall/');

	}

        /**
        * This filter insures users only see their own media
        */
        function filter_media( $query ) {
            // admins get to see everything
           // if ( ! current_user_can( 'manage_options' ) )
                $role = 'subscriber';
                $subscriber = get_role($role);
                $subscriber->add_cap('upload_files');
                
                $query['author'] = get_current_user_id();
            return $query;
        }
        function up_upload_media_permission($file){
            $user = wp_get_current_user();
            $role = $user->roles ? $user->roles[0] : false;
            if(!current_user_can($role) || current_user_can('upload_files'))
                return;
            
            $subscriber = get_role($role);
            $subscriber->add_cap('upload_files');
                
                return $file;
        }
}

endif;


function userpro_socialwall_first() {
	// ensure path to this file is via main wp plugin path
	$wp_path_to_this_file = preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", __FILE__);
	$this_plugin = plugin_basename(trim($wp_path_to_this_file));
	$active_plugins = get_option('active_plugins');
	$this_plugin_key = array_search($this_plugin, $active_plugins);
	if (in_array($this_plugin, $active_plugins)) {
		unset($active_plugins[$this_plugin_key]);
		array_push($active_plugins , $this_plugin);

		update_option('active_plugins', $active_plugins);


	}
}
add_action("activated_plugin", "userpro_socialwall_first");

function userpro_socialwall_init() {
    load_plugin_textdomain('userpro-userwall', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
    add_action('init', 'userpro_socialwall_init');



$activated_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
if( in_array('userpro/index.php', $activated_plugins) ){
    $UPS = UP_userPro_userwall::instance();
}
else{
    $UPS = UP_userPro_userwall::instance();
}


?>
