<?php
/*
Plugin Name: Hide My WP
Plugin URI: http://hide-my-wp.wpwave.com/
Description: An excellent security plugin to hide your WordPress installation packed with some of the coolest and most unique features in the community.
Author: wpWave
Author URI: http://wpwave.com
Version: 6.2.6
Text Domain: hide_my_wp
Domain Path: /lang
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Network: True
*/


/**
 *   ++ Credits ++
 *   Copyright 2019 ExpressTech Team
 *   Copyright 2017 Hassan Jahangiri
 *   Some code from dxplugin base by mpeshev, plugin base v2 by Brad Vincent, weDevs Settings API by Tareq Hasan, rootstheme by Ben Word, Minify by Stephen Clay and Mute Scemer by ampt
 */
 $hide_my_wp_settings = get_option( 'hide_my_wp' );
 $hide_my_wp_settings['li'] = 'purchase-code';
 update_option( 'hide_my_wp', $hide_my_wp_settings );
delete_option( 'pp_important_messages');
define('HMW_TITLE', 'Hide My WP');
define('HMW_VERSION', '6.2.6');
define('HMW_SLUG', 'hide_my_wp'); //use _
define('HMW_PATH', dirname(__FILE__));
define('HMW_DIR', basename(HMW_PATH));
define('HMW_URL', plugins_url() . '/' . HMW_DIR);
define('HMW_FILE', plugin_basename(__FILE__));

if (is_ssl()) {
    define('HMW_WP_CONTENT_URL', str_replace('http:', 'https:', WP_CONTENT_URL));
    define('HMW_WP_PLUGIN_URL', str_replace('http:', 'https:', WP_PLUGIN_URL));
} else {
    define('HMW_WP_CONTENT_URL', WP_CONTENT_URL);
    define('HMW_WP_PLUGIN_URL', WP_PLUGIN_URL);
}

class HideMyWP
{
    const title = HMW_TITLE;
    const ver = HMW_VERSION;
    const slug = HMW_SLUG;
    const path = HMW_PATH;
    const dir = HMW_DIR;
    const url = HMW_URL;
    const main_file = HMW_FILE;

    private $s;
    private $sub_folder;
    private $is_subdir_mu;
    private $blog_path;

    private $trust_key;
    private $short_prefix;


    private $post_replace_old = array();
    private $post_replace_new = array();

    private $post_preg_replace_new = array();
    private $post_preg_replace_old = array();

    private $partial_replace_old = array();
    private $partial_replace_new = array();

    private $top_replace_old = array();
    private $top_replace_new = array();

    private $partial_preg_replace_new = array();
    private $partial_preg_replace_old = array();

    private $replace_old = array();
    private $replace_new = array();

    private $preg_replace_old = array();
    private $preg_replace_new = array();

    private $admin_replace_old = array();
    private $admin_replace_new = array();

    private $auto_replace_urls = array(); //strings with ==
    private $auto_config_internal_css;
    private $auto_config_internal_js;

    private $none_replaced_buffer = '';

    /**
     * HideMyWP::__construct()
     *
     * @return
     */
    function __construct()
    {
		register_activation_hook(__FILE__, array(&$this, 'on_activate_callback'));
		register_deactivation_hook(__FILE__, array(&$this, 'on_deactivate_callback'));

		self::load_ip_countries_db_table();
		require_once('load.php');
        add_action( 'activated_plugin', array(&$this, 'hmwp_activation_redirect') );
    }

	function hmwp_plugin_update_checker() {
		require_once('lib/plugin-update/plugin-update-checker.php');
		$HMWP_UpdateChecker = PucFactory::buildUpdateChecker(
			'http://api.wpwave.com/hide_my_wp.json',//li will be added automatically
			__DIR__.'/hide-my-wp.php',
			'hide-my-wp',
			100 //4days + manual and auto checks in several places (7 days when there's an update)!
		);
		$HMWP_UpdateChecker->throttleRedundantChecks = true;
		$HMWP_UpdateChecker->addQueryArgFilter(array(&$this, 'update_attr'));
	}

    /**
     * HideMyWP::bp_uri()
     * Fix buddypress pages URL when page_base is enabled
     *
     * @return
     */
    function bp_uri($uri)
    {
        if (trim($this->opt('page_base'), ' /'))
            return str_replace(trim($this->opt('page_base'), ' /') . '/', '', $uri);
        else
            return $uri;
    }

    function access_cookie()
    {
        return preg_replace("/[^a-zA-Z]/", "", substr(SECURE_AUTH_SALT, 2, 8));
    }
    function get_short_prefix()
    {
        return $this->short_prefix;
    }
    function get_trust_key()
    {
        return $this->trust_key;
    }

    /**
     * HideMyWP::replace_admin_url()
     * Filter to replace old and new admin URL
     *
     * @return
     */
    function replace_admin_url($url, $path = '', $scheme = 'admin')
    {
        if (trim($this->opt('new_admin_path'), '/ ') && trim($this->opt('new_admin_path'), '/ ') != 'wp-admin')
            $url = str_replace('wp-admin/', trim($this->opt('new_admin_path'), '/ ') . '/', $url);
        return $url;
    }

    function netMatch($network, $ip)
    {
        $network = trim($network);
        $orig_network = $network;
        $ip = trim($ip);
        if ($ip == $network) {
            //echo "used network ($network) for ($ip)\n";
            return TRUE;
        }
        $network = str_replace(' ', '', $network);
        if (strpos($network, '*') !== FALSE) {
            if (strpos($network, '/') !== FALSE) {
                $asParts = explode('/', $network);
                $network = @ $asParts[0];
            }
            $nCount = substr_count($network, '*');
            $network = str_replace('*', '0', $network);
            if ($nCount == 1) {
                $network .= '/24';
            } else if ($nCount == 2) {
                $network .= '/16';
            } else if ($nCount == 3) {
                $network .= '/8';
            } else if ($nCount > 3) {
                return TRUE; // if *.*.*.*, then all, so matched
            }
        }

        // echo "from original network($orig_network), used network ($network) for ($ip)\n";

        $d = strpos($network, '-');
        if ($d === FALSE) {
            $ip_arr = explode('/', $network);
            if (!preg_match("@\d*\.\d*\.\d*\.\d*@", $ip_arr[0], $matches)) {
                $ip_arr[0] .= ".0";    // Alternate form 194.1.4/24
            }
            $network_long = ip2long($ip_arr[0]);
            if (isset($ip_arr[1])) {
                $x = ip2long($ip_arr[1]);
                $mask = long2ip($x) == $ip_arr[1] ? $x : (0xffffffff << (32 - $ip_arr[1]));
                $ip_long = ip2long($ip);
                return ($ip_long & $mask) == ($network_long & $mask);
            }
        } else {
            $from = trim(ip2long(substr($network, 0, $d)));
            $to = trim(ip2long(substr($network, $d + 1)));
            $ip = ip2long($ip);
            return ($ip >= $from and $ip <= $to);
        }
    }

    /**
     * HideMyWP::admin_notices()
     * Displays necessary information in admin panel
     *
     * @return
     */
    function admin_notices()
    {
        global $current_user;
        if (is_super_admin()) {
			$this->h->update_pp_important_messages();
		}

        $options_file = (is_multisite()) ? 'network/settings.php' : 'admin.php';
        $page_url = admin_url(add_query_arg('page', self::slug, $options_file));
        $show_access_message = true;

        //Update hmw_all_plugins list whenever a theme or plugin activate
        if ((isset($_GET['page']) && ($_GET['page'] == self::slug)) || isset($_GET['deactivate']) || isset($_GET['activate']) || isset($_GET['activated']) || isset($_GET['activate-multi'])) {
            update_option('hmw_all_plugins', array_keys(get_plugins()));

            $blog_id = get_current_blog_id();
            if (!is_multisite())
                delete_option('hmwp_internal_assets');
            else
                delete_blog_option($blog_id, 'hmwp_internal_assets');
        }

        if (isset($_GET['page']) && $_GET['page'] == self::slug && function_exists('bulletproof_security_load_plugin_textdomain')) {
            echo __('<div class="error"><p>You use BulletProof security plugin. To make it work correctly you need to configure Hide My WP manually. <a target="_blank" href="' . add_query_arg(array('die_message' => 'single')) . '" class="button">' . __('Manual Configuration', self::slug) . '</a>. (If you already did that ignore this message).', self::slug) . '</p></div>';
            $show_access_message = false;
        }

        if (isset($_GET['page']) && $_GET['page'] == self::slug && isset($_GET['new_admin_action']) && $_GET['new_admin_action'] == 'configured') {

            if (is_multisite()) {
                $opts = (array)get_blog_option(BLOG_ID_CURRENT_SITE, self::slug);
                $opts['new_admin_path'] = get_option('hmwp_temp_admin_path');
                update_blog_option(BLOG_ID_CURRENT_SITE, self::slug, $opts);
            } else {
                $opts = (array)get_option(self::slug);
                $opts['new_admin_path'] = get_option('hmwp_temp_admin_path');
                update_option(self::slug, $opts);
            }
            delete_option('hmwp_temp_admin_path');
            wp_redirect(add_query_arg('new_admin_action', 'redirect_to_new', $page_url));
        }

        if (isset($_GET['page']) && $_GET['page'] == self::slug && isset($_GET['new_admin_action']) && $_GET['new_admin_action'] == 'redirect_to_new') {
            //wp_logout();
            wp_redirect(wp_login_url('', true)); //true means force auth
        }

        if (isset($_GET['page']) && $_GET['page'] == self::slug && isset($_GET['new_admin_action']) && $_GET['new_admin_action'] == "abort") {
            ///update_option('hmwp_temp_admin_path', $this->opt('new_admin_path'));
            delete_option('hmwp_temp_admin_path');
            wp_redirect(add_query_arg('new_admin_action', 'aborted_msg', $page_url));
        }

        if (isset($_GET['page']) && $_GET['page'] == self::slug && isset($_GET['new_admin_action']) && $_GET['new_admin_action'] == "aborted_msg") {
            echo '<div class="error"><p>Change of admin path is cancelled!</p></div>';
        }
        $permalink_structure = get_option( 'permalink_structure' );
        $permalink_structurel_link = admin_url('/options-permalink.php');
        if(empty($permalink_structure))
        {
            echo '<div class="error"><p>For HMWP to work correctly, the site\'s Permalink Structure has to be custom one and NOT the Plain. Please change it from <a href='.$permalink_structurel_link.'>here</a>.!</p></div>';
        }

        if (trim(get_option('hmwp_temp_admin_path'), ' /'))
            $new_admin_path = trim(get_option('hmwp_temp_admin_path'), ' /');
        elseif (trim($this->opt('new_admin_path'), '/ '))
            $new_admin_path = trim($this->opt('new_admin_path'), '/ ');
        else
            $new_admin_path = 'wp-admin';


        if ($this->admin_current_cookie() != $new_admin_path && is_super_admin()) {
            if (!isset($_GET['new_admin_action']) && !isset($_GET['die_message'])) {
                $page_url = str_replace($this->admin_current_cookie(), 'wp-admin', $page_url);

                if ($new_admin_path == 'wp-admin')
                    wp_redirect(add_query_arg(array('die_message' => 'revert_admin'), $page_url));
                else {
                    $rand_token = uniqid('token', true);
                    update_option('hmwp_reset_token', $rand_token);
                    wp_redirect(add_query_arg(array('die_message' => 'new_admin'), $page_url));
                }

            }

        }
        //Good place to flush! We really need this.
        if (is_super_admin() && !function_exists('bulletproof_security_load_plugin_textdomain') && !$this->opt('customized_htaccess'))
            flush_rewrite_rules(true);

        if (is_multisite() && is_network_admin()) {
            global $wpdb;
            $sites = $wpdb->get_results("SELECT blog_id, domain FROM {$wpdb->blogs} WHERE archived = '0' AND spam = '0' AND deleted = '0' ORDER BY blog_id");

            //Loop through them
            foreach ($sites as $site) {
                global $wp_rewrite;
                //switch_to_blog($site->blog_id);
                delete_blog_option($site->blog_id, 'rewrite_rules');
                //$wp_rewrite->init();
                //$wp_rewrite->flush_rules();
            }

        }

        $home_path = get_home_path();
        if ((!file_exists($home_path . '.htaccess') && is_writable($home_path)) || is_writable($home_path . '.htaccess')) {
			$writable = true;
		} else {
			$writable = false;
		}

		if (!$this->is_permalink()) {
			if (isset($_GET['page']) && $_GET['page'] == 'hmwp_setup_wizard') {
				echo '<div class="error"><p>' . __('Your <a href="options-permalink.php">permalink structure</a> is off. Once you finish setup, We will change permalink in order to work all features.', self::slug) . '</p></div>';
			}
			if (isset($_GET['page']) && $_GET['page'] == self::slug) {
				if (is_multisite()) {
					echo '<div class="error"><p>' . __('Please enable WP permalink structure (Settings -> Permalink ) in your sites.', self::slug) . '</p></div>';
				} else {
					echo '<div class="error"><p>' . __('Your <a href="options-permalink.php">permalink structure</a> is off. In order to get all features of this plugin please enable it.', self::slug) . '</p></div>';
				}
			}
			$show_access_message = false;
		}


		if (isset($_GET['page']) && $_GET['page'] == self::slug && (isset($_GET['settings-updated']) || isset($_GET['settings-imported'])) && is_multisite()) {
            echo '<div class="error"><p>' . __('You have enabled Multisite. It\'s require to (re)configure Hide My WP after changing settings or activating new plugin or theme. <br><br><a target="_blank" href="' . add_query_arg(array('die_message' => 'multisite')) . '" class="button">' . __('Multisite Configuration', self::slug) . '</a>', self::slug) . '</p></div>';
            $show_access_message = false;
        }

        $nginx = false;
        if (isset($_GET['page']) && $_GET['page'] == self::slug && (stristr($_SERVER['SERVER_SOFTWARE'], 'nginx') || stristr($_SERVER['SERVER_SOFTWARE'], 'wpengine'))) {
            echo '<div class="error"><p>' . __('You use Nginx web server. It\'s require to (re)configure Hide My WP  after changing settings or activating new plugin or theme. <br><br><a target="_blank" href="' . add_query_arg(array('die_message' => 'nginx')) . '" class="button">' . __('Nginx Configuration', self::slug) . '</a>', self::slug) . '</p></div>';
            $show_access_message = false;
            $nginx = true;
        }

        $win = false;
        if (isset($_GET['page']) && $_GET['page'] == self::slug && stristr($_SERVER['SERVER_SOFTWARE'], 'iis') || stristr($_SERVER['SERVER_SOFTWARE'], 'Windows')) {
            echo '<div class="error"><p>' . __('You use Windows (IIS) web server. It\'s require to (re)configure Hide My WP after changing settings or activating new plugin or theme. <br><br><a target="_blank" href="' . add_query_arg(array('die_message' => 'iis')) . '" class="button">' . __('IIS Configuration', self::slug) . '</a>', self::slug) . '</p></div>';
            $show_access_message = false;
            $win = true;
        }


        if (isset($_GET['page']) && $_GET['page'] == self::slug && isset($_GET['undo_config']) && $_GET['undo_config'])
            echo '<div class="updated fade"><p>' . __('Previous settings have been restored!', self::slug) . '</p></div>';

        if (isset($_GET['page']) && $_GET['page'] == self::slug && !$writable && !$nginx && !$win && !function_exists('bulletproof_security_load_plugin_textdomain')) {
            echo '<div class="error"><p>' . __('It seems there is no writable htaccess file in your WP directory. If you use Apache (and not Nginx or IIS) please change permission of .htaccess file.', self::slug) . '</p></div>';
            $show_access_message = false;
        }

        if (basename($_SERVER['PHP_SELF']) == 'options-permalink.php' && $this->is_permalink() && isset($_POST['permalink_structure'])) {
			echo '<div class="updated"><p>' . sprintf(__('We are refreshing this page in order to implement changes. %s', self::slug), '<a href="options-permalink.php">Manual Refresh</a>') . '<script type="text/JavaScript"><!--  setTimeout("window.location = \'options-permalink.php\';", 5000);   --></script></p> </div>';
		}

		/*if (isset($_GET['page']) && $_GET['page'] == self::slug && (isset($_GET['settings-updated']) || isset($_GET['settings-imported'])) && $show_access_message && !$this->access_test()) {
			echo '<div class="error"><p>' . __('HMWP guesses it broke your site. If it didn\'t ignore this messsage otherwise read <a href="http://codecanyon.net/item/hide-my-wp-no-one-can-know-you-use-wordpress/4177158/faqs/18136" target="_blank"><strong>this FAQ</strong></a> or revert settings to default.', self::slug) . '</p></div>';
		}*/

		if (!defined('AUTH_KEY') || !defined('SECURE_AUTH_KEY') || !defined('LOGGED_IN_KEY') || !defined('NONCE_KEY') || !defined('AUTH_SALT') || !defined('SECURE_AUTH_SALT') || !defined('LOGGED_IN_SALT') || !defined('LOGGED_IN_SALT') || !defined('NONCE_SALT') || NONCE_SALT == 'put your unique phrase here' || !NONCE_SALT || AUTH_KEY == 'put your unique phrase here' || !AUTH_KEY || NONCE_KEY == 'put your unique phrase here') {
			echo '<div class="error"><p>' . __('Hide My WP Security Check: Your site is at risk. WP installed wrongly: one or more of security keys are invalid. <a href="https://codex.wordpress.org/Editing_wp-config.php#Security_Keys" target="_blank"><strong>Read here</strong></a> for details.', self::slug) . '</p></div>';
		}

		if (isset($_GET['page']) && $_GET['page'] == self::slug && (isset($_GET['settings-updated']) || isset($_GET['settings-imported'])) && (WP_CACHE || function_exists('hyper_cache_sanitize_uri') || class_exists('WpFastestCache') || defined('QUICK_CACHE_ENABLE') || defined('CACHIFY_FILE') || defined('WP_ROCKET_VERSION'))) {
			echo '<div class="updated"><p>' . __('It seems you use a caching plugin alongside Hide My WP. Good, just please make sure to flush it to see changes! (consider browser cache, too!)', self::slug) . '</p></div>';
		}
	}

    function access_test()
    {
        $response = wp_remote_get($this->partial_filter(get_stylesheet_uri()));

        if (200 !== wp_remote_retrieve_response_code($response)
            AND 'OK' !== wp_remote_retrieve_response_message($response)
            AND is_wp_error($response)
        )
            return false;

        return true;
    }

    /**
     * HideMyWP::email_from_name()
     *
     * Change mail name
     * @return
     */
    function email_from_name()
    {
        return $this->opt('email_from_name');
    }

    /**
     * HideMyWP::email_from_address()
     *
     * Change mail address
     * @return
     */
    function email_from_address()
    {
        return $this->opt('email_from_address');
    }


    function styles_scripts()
    {
        $site = '';
        if (is_multisite() && !$this->is_subdir_mu)
            $site = '&sn=' . $this->blog_path;

        if ($this->add_auto_internal('css') && $this->is_permalink()) {
            $page = $this->hash($_SERVER['REQUEST_URI']);
            //todo:
            if ($this->opt('auto_internal') == 1 || $this->opt('auto_internal') == 3)
                $page = $this->hash($_SERVER['REQUEST_URI']);

            wp_enqueue_style('auto_css', network_home_url('/_auto.css') . '?_req=' . $page . $site, array(), false);
        }

        if ($this->add_auto_internal('js') && $this->is_permalink()) {
            //can not use $site here because woocommerce need req
            $page = urlencode(base64_encode($_SERVER['REQUEST_URI']));

            //require for woocommerce endpoint issue
            if ($this->opt('auto_config_plugins') || $this->opt('auto_internal') >= 2)
                $page = urlencode(base64_encode($_SERVER['REQUEST_URI']));

            wp_enqueue_script('auto_js', network_home_url('/_auto.js') . '?_req=' . $page . $site, array(), false);
        }
    }


    function wc_endpoint($req)
    {
        if ($this->h->str_contains($req, '_auto.') && isset($_REQUEST['_req']))
            return add_query_arg('wc-ajax', '%%endpoint%%', remove_query_arg(array('remove_item', 'add-to-cart', 'added-to-cart'), base64_decode(urldecode($_REQUEST['_req']))));
        return $req;
    }

    function hash($key)
    {
        return hash('crc32', preg_replace("/[^a-zA-Z]/", "", substr(NONCE_KEY, 2, 6)) . $key);
    }

    function encrypt($str, $key)
    {
        //$key = "abc123 as long as you want bla bla bla";
        $result = '';
        for ($i = 0; $i < strlen($str); $i++) {
            $char = substr($str, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result .= $char;
        }
        return urlencode(base64_encode($result));
    }


    function decrypt($str, $key)
    {
        $str = base64_decode(urldecode($str));
        $result = '';
        //$key = "must be same key as in encrypt";
        for ($i = 0; $i < strlen($str); $i++) {
            $char = substr($str, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result .= $char;
        }
        return $result;
    }

    /**
     * HideMyWP::wp()
     *
     * Disable WP components when permalink is enabled
     * @return
     */
    function wp()
    {
        global $wp_query;

        //echo '<pre>'; print_r($wp_query); echo '</pre>';

        if ((is_feed() || is_comment_feed()) && !isset($_GET['feed']) && !$this->opt('feed_enable'))
            $this->block_access();
        //04-11-2019 Commented by dev. I dont know the exact reason add this code
        /*
        if (is_author() && !isset($_GET['author']) && !isset($_GET['author']) && !$this->opt('author_enable'))
            $this->block_access();
        if (is_search() && !isset($_GET['s']) && !$this->opt('search_enable'))
            $this->block_access();
        if (is_paged() && !isset($_GET['paged']) && !$this->opt('paginate_enable'))
            $this->block_access();
        if (is_page() && !isset($_GET['page_id']) && !isset($_GET['pagename']) && !$this->opt('page_enable'))
            $this->block_access();
        if (is_single() && !isset($_GET['p']) && !$this->opt('post_enable'))
            $this->block_access();
        if (is_category() && !isset($_GET['cat']) && !$this->opt('category_enable'))
            $this->block_access();
        if (is_tag() && !isset($_GET['tag']) && !$this->opt('tag_enable'))
            $this->block_access();
         *
         */
        if ((is_date() || is_time()) && !isset($_GET['monthnum']) && !isset($_GET['m']) && !isset($_GET['w']) && !isset($_GET['second']) && !isset($_GET['year']) && !isset($_GET['day']) && !isset($_GET['hour']) && !isset($_GET['second']) && !isset($_GET['minute']) && !isset($_GET['calendar']) && $this->opt('disable_archive'))
            $this->block_access();

        if ((is_tax() || is_post_type_archive() || is_trackback() || is_attachment()) && !isset($_GET['post_type']) && !isset($_GET['taxonamy']) && !isset($_GET['attachment']) && !isset($_GET['attachment_id']) && !isset($_GET['preview']) && $this->opt('disable_other_wp'))
            $this->block_access();

        if (isset($_SERVER['HTTP_USER_AGENT']) && !is_404() && !is_home() && (stristr($_SERVER['HTTP_USER_AGENT'], 'BuiltWith') || stristr($_SERVER['HTTP_USER_AGENT'], '2ip.ru')))
            wp_redirect(home_url());

        if ($this->opt('remove_other_meta')) {
            if (function_exists('header_remove')) {
                header_remove('X-Powered-By'); // PHP 5.3+
                header_remove('WP-Super-Cache');
                header_remove('wp-super-cache');
                // exit;
            } else {
                // exit;
                header('X-Powered-By: ');
                header('WP-Super-Cache: ');
                header('wp-super-cache: ');
            }
        }
    }

    function die_message()
    {
        if(current_user_can( 'manage_options')){
            //already checked to be super admin
            if (!isset($_GET['die_message']))
                return;

            $options_file = (is_multisite()) ? 'network/settings.php' : 'admin.php';
            $page_url = admin_url(add_query_arg('page', self::slug, $options_file));

            if (trim(get_option('hmwp_temp_admin_path'), ' /'))
                $new_admin_path = trim(get_option('hmwp_temp_admin_path'), ' /');
            elseif (trim($this->opt('new_admin_path'), '/ '))
                $new_admin_path = trim($this->opt('new_admin_path'), '/ ');
            else
                $new_admin_path = 'wp-admin';

            $page_url = str_replace($this->admin_current_cookie(), 'wp-admin', $page_url);
            $title = '';
            switch ($_GET['die_message']) {
                case 'nginx':
                    $title = "Nginx Configuration";
                    $_GET['nginx_config'] = 1;
                    $content = $this->nginx_config();
                    break;
                case 'single':
                    $title = "Manual Configuration";
                    $_GET['single_config'] = 1;
                    $content = $this->single_config();
                    break;
                case 'multisite':
                    $title = "Multisite Configuration";
                    $_GET['multisite_config'] = 1;
                    $content = $this->multisite_config();
                    break;
                case 'iis':
                    $title = "IIS Configuration";
                    $_GET['iis_config'] = 1;
                    $content = $this->iis_config();
                    break;
                case 'new_admin':
                    $title = "Custom Admin Path";
                    $token = get_option('hmwp_reset_token');
                    $reset_url = plugins_url() . '/'. dirname(HMW_FILE) . '/d.php'.'?token='.$token;
                    $content = sprintf(__('<div class="error"><p>Do not click back or close this tab.<br> Follow these steps <strong>IMMEDIATELY</strong> to enable new admin path or <a href="' . add_query_arg(array('new_admin_action' => 'abort'), $page_url) . '">Cancel</a> and try later. (<a target="_blank" href="http://support.wpwave.com/videos/change-wp-admin-to-myadmin" >' . __('Video Tutorial', self::slug) . '</a>) <br><strong>1) Re-configure server: (if require)</strong> <br> If you don\'t have a writable htaccess or enabled multi-site choose appropriate setup otherwise, HMWP updates your htaccess automatically and you can go to next step<br/><a target="_blank" href="' . add_query_arg(array('die_message' => 'single'), $page_url) . '" class="button">' . __('Manual Configuration', self::slug) . '</a> <a target="_blank" href="' . add_query_arg(array('die_message' => 'multisite'), $page_url) . '" class="button">' . __('Multisite Configuration (Apache)', self::slug) . '</a> <a target="_blank" href="' . add_query_arg(array('die_message' => 'nginx'), $page_url) . '" class="button">' . __('Nginx Configuration', self::slug) . '</a> <a target="_blank" href="' . add_query_arg(array('die_message' => 'iis'), $page_url) . '" class="button">' . __('IIS Configuration', self::slug) . '</a>
                    <br><br><strong> 2) <span style="color: #ee0000">Edit /wp-config.php  </span></strong><br>  Open wp-config.php using FTP and add following line somewhere before require_once(...) (if it already exist replace it with new code): <br><i><code>define("ADMIN_COOKIE_PATH",  "%1$s");</code></i><br><br>%4$s<a class="button btn-red" href="%3$s">Cancel and Use Current Admin Path</a>  <a class="button btn-blue" target="_blank" href="%2$s">I Did it! (Login to New Dashboard)</a> </p>
                    <p style="color: #ee0000"><strong>If you get locked out of your WordPress site, use this link to instantly uninstall HMWP plugin - </strong><input type="text" value="'.$reset_url.'" onclick="this.select();" readonly></p></div>', self::slug), preg_replace('|https?://[^/]+|i', '', get_option('siteurl') . '/') . $new_admin_path, add_query_arg(array('new_admin_action' => 'configured'), $page_url), add_query_arg(array('new_admin_action' => 'abort'), $page_url), '');
                    $content .= "<style>input[type='text']{display: block;width: 100%;max-width: 100%;box-sizing: border-box;margin: 5px 0;padding: 10px;border-radius: 4px;border: 1px solid #ccc;font-size: 14px;cursor: pointer;}.button{background: #f7f7f7;border: 1px solid #ccc;color: #555;text-decoration: none;font-size: 13px;line-height: 2;height: auto;display: inline-block;box-sizing: border-box;margin: 5px;padding: 4px 10px;cursor: pointer;-webkit-border-radius: 3px;-webkit-appearance: none;border-radius: 4px;white-space: nowrap;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;-webkit-box-shadow: 0 1px 0 #ccc;box-shadow: 0 1px 0 #ccc;vertical-align: top;}.btn-red{background: #d33434;color: #FFF;}.btn-blue{background: #249124;color: #FFF;}</style>";
                    $body_email = 'Hello admin,<br/><br/><p style="color:green"><strong>If you get locked out of your WordPress site, use this link to instantly uninstall HMWP plugin - '.$reset_url.'<strong></p>';
                    $subject_email = sprintf(__('[%s] Your New WP Login!', self::slug), self::title);
                    wp_mail(get_option('admin_email'), $subject_email, $body_email, array('Content-Type: text/html; charset=UTF-8'));
                    break;
                case 'revert_admin':
                    $title = "Reset Default Admin Path";
                    $content = sprintf(__('<div class="error">Do not click back or close this tab. <br>Follow these steps <strong>IMMEDIATELY</strong> to enable new admin path or <a href="' . add_query_arg(array('new_admin_action' => 'abort'), $page_url) . '">Cancel</a> and try later.<p><strong><span style="color: #ee0000">Edit /wp-config.php: </span></strong><br>  Open wp-config.php using FTP and <span style="color: #ee0000"><strong>DELETE or comment (//)</strong></span> line which starts with following code: <br><code><i>define("ADMIN_COOKIE_PATH",  "...</i></code><br><br> <a class="button" href="%3$s">Cancel and Use Current Admin Path</a> <a class="button" href="%2$s" target="_blank">I Did it! (Login to Default Admin)</a></p></div>', self::slug), '', add_query_arg(array('new_admin_action' => 'configured'), $page_url), add_query_arg(array('new_admin_action' => 'abort'), $page_url));
                    break;
            }
            wp_die('<h3>' . $title . '</h3>' . $content);
        }else{
            return;
        }
    }

    /**
     * HideMyWP::admin_css_js()
     *
     * Adds admin.js to options page
     * @return
     */
    function admin_css_js()
    {

        if (isset($_GET['page']) && $_GET['page'] == self::slug) {
            wp_enqueue_script('jquery');
            wp_register_script(self::slug . '_admin_js', self::url . '/js/admin.js', array('jquery'), self::ver, false);
            wp_enqueue_script(self::slug . '_admin_js');
        }

        //wp_register_style( self::slug.'_admin_css', self::url. '/css/admin.css', array(), self::ver, 'all' );
        //wp_enqueue_style( self::slug.'_admin_css' );
    }

    /**
     * HideMyWP::front_css_js()
     *
     * Adds buddypress ajax solution
     * @return
     *
     */
    function front_css_js(){
        if($this->opt('replace_wpnonce') == 'on'){ ?>
            <script type="text/javascript">
                jQuery(document).ajaxSuccess(function (event, xhr, settings) {
                    var content = xhr.responseText;
                    if(content.indexOf('data') != -1){
                        var new_js = jQuery.parseJSON(content);
                        if( jQuery('#activity-stream').length > 0 ){
                            var get_content = new_js.data['contents'];
                            var get_content_n = HMWPReplaceMethod(get_content,"wpnonce=","nonce=");
                            jQuery('#activity-stream').replaceWith(get_content_n);
                        }
                    }
                });
                function HMWPReplaceMethod(str,find,replace_with){
                    while (str.indexOf(find) !== -1 ){
                        from = str.indexOf(find);
                        to = from + find.length;
                        str = str.substr(0,from)+replace_with+str.substr(to, str.length-to);
                    }
                    return str;
                }
            </script>
        <?php
        }
    }

    /**
     * HideMyWP::pp_settings_api_reset()
     * Filter after reseting Options
     * @return
     */
    function pp_settings_api_reset()
    {
        delete_option('hmw_all_plugins');
        delete_option('pp_important_messages');
        delete_option('pp_important_messages_last');
        delete_option('trust_network_rules');
        delete_option('hmwp_internal_assets');

        update_option('hmwp_temp_admin_path', 'wp-admin');
        flush_rewrite_rules();

    }

    /**
     * HideMyWP::pp_settings_api_filter()
     * Filter after updateing Options
     * @param mixed $post
     * @return
     */
    function pp_settings_api_filter($post)
    {
        global $wp_rewrite;

		$filename = ABSPATH . '.htaccess';
		if(!is_writable($filename)){
			$options_file = (is_multisite()) ? 'network/settings.php' : 'admin.php';
            $page_url = admin_url(add_query_arg('page', 'hide_my_wp', $options_file));
			$goback = add_query_arg(array('htaccess-write' => 'true'), $page_url);
			wp_redirect($goback);
			exit;
		}

        update_option(self::slug . '_undo', get_option(self::slug));

        if ((isset($post[self::slug]['admin_key']) && $this->opt('admin_key') != $post[self::slug]['admin_key']) || (isset($post[self::slug]['login_query']) && $this->opt('login_query') != $post[self::slug]['login_query'])) {
            $body = "Hi-\nThis is %s plugin. Here is your new WordPress login address:\nURL: %s\n\nBest Regards,\n%s";

            if (isset($post[self::slug]['login_query']) && $post[self::slug]['login_query'])
                $login_query = $post[self::slug]['login_query'];
            else
                $login_query = 'hide_my_wp';

            $new_url = site_url('wp-login.php');
            if ($this->h->str_contains($new_url, 'wp-login.php'))
                $new_url = add_query_arg($login_query, $post[self::slug]['admin_key'], $new_url);

            $body = sprintf(__($body, self::slug), self::title, $new_url, self::title);
            $subject = sprintf(__('[%s] Your New WP Login!', self::slug), self::title);
            wp_mail(get_option('admin_email'), $subject, $body);
        }

        if (!trim($this->opt('new_admin_path'), ' /') || trim($this->opt('new_admin_path'), ' /') == 'wp-admin')
            $current_admin_path = 'wp-admin';
        else
            $current_admin_path = trim($this->opt('new_admin_path'), ' /');

        if (isset($post['import_field']) && $post['import_field']) {
            $import_field = stripslashes($post['import_field']);
            $import_field = json_decode($import_field, true);
            $new_admin_path_input = (isset($import_field['new_admin_path']) && trim($import_field['new_admin_path'], '/ ')) ? $import_field['new_admin_path'] : 'wp-admin';
        } else {
            $new_admin_path_input = (isset($post[self::slug]['new_admin_path'])) ? $post[self::slug]['new_admin_path'] : '';
        }

        if (!trim($new_admin_path_input, ' /') || trim($new_admin_path_input, ' /') == 'wp-admin')
            $new_admin_path = 'wp-admin';
        else
            $new_admin_path = trim($new_admin_path_input, ' /');

        if ($new_admin_path != $current_admin_path) {
            //save temp value and return everything back whether it was enter by user or import fields
            if (isset($post['import_field']) && $post['import_field'])
                $post['import_field'] = str_replace('\"new_admin_path\":\"' . $new_admin_path . '\"', '\"new_admin_path\":\"' . $current_admin_path . '\"');
            else
                $post[self::slug]['new_admin_path'] = $current_admin_path;

            update_option('hmwp_temp_admin_path', $new_admin_path);
        }


        if (!is_multisite()) {
            $wp_rewrite->set_permalink_structure(trim($post[self::slug]['post_base'], ' '));
            $wp_rewrite->set_category_base(trim($post[self::slug]['category_base'], '/ '));
            $wp_rewrite->set_tag_base(trim($post[self::slug]['tag_base'], '/ '));
        }


        if (isset ($post[self::slug]['li']) && (strlen($post[self::slug]['li']) > 34 || strlen($post[self::slug]['li']) < 42))
            delete_option('pp_important_messages');

        flush_rewrite_rules();


        if (isset($post['replace_in_html1']) && $post['replace_in_html1']) {
            $i = 0;
            foreach ($post['replace_in_html1'] as $old) {

                //bslash done by javascript or user hisself and will be saved automatically
                $old = str_replace(array('=', "\r\n", "\n", "\r"), array('[equal]', '[new_line]', '[new_line]', '[new_line]'), $old);
                $new = str_replace(array('=', "\r\n", "\n", "\r"), array('[equal]', '[new_line]', '[new_line]', '[new_line]'), $post['replace_in_html2'][$i]);

                //$new = htmlentities(stripslashes($new));

                $post[self::slug]['replace_in_html'] .= $old . '=' . $new . "\n";
                $i++;

            }
        }

        if (isset($post['replace_urls1']) && $post['replace_urls1']) {
            $i = 0;
            foreach ($post['replace_urls1'] as $old) {

                $old = str_replace(array('\\'), array('[bslash]'), $old);
                $new = str_replace(array('\\'), array('[bslash]'), $post['replace_urls2'][$i]);
                $post[self::slug]['replace_urls'] .= $old . '==' . $new . "\n";
                $i++;

                //print_r($post);exit;

            }
        }

        return $post;
    }

    /**
     * HideMyWP::add_login_key_to_action_from()
     * Add admin key to links in wp-login.php
     * @param string $url
     * @param string $path
     * @param string $scheme
     * @param int $blog_id
     * @return
     */
    function add_login_key_to_action_from($url, $path, $scheme, $blog_id)
    {
        if ($this->opt('login_query'))
            $login_query = $this->opt('login_query');
        else
            $login_query = 'hide_my_wp';

        if (trim($this->opt('new_login_path'), ' /') && trim($this->opt('new_login_path'), ' /') != 'wp-login.php')
            return str_replace('wp-login.php', trim($this->opt('new_login_path'), ' /'), $url);

        if ($url && $this->h->str_contains($url, 'wp-login.php'))
            if ($scheme == 'login' || $scheme == 'login_post')
                return add_query_arg($login_query, $this->opt('admin_key'), $url);


        return $url;
    }

    /**
     * HideMyWP::add_key_login_to_url()
     * Add admin key to wp-login url
     * @param mixed $url
     * @param string $redirect
     * @return
     */
    function add_key_login_to_url($url, $redirect = '0')
    {
        if ($this->opt('login_query'))
            $login_query = $this->opt('login_query');
        else
            $login_query = 'hide_my_wp';

        if ($this->opt('admin_key'))
            $admin_key = $this->opt('admin_key');
        else
            $admin_key = '1234';

        if (trim($this->opt('new_login_path'), ' /') && trim($this->opt('new_login_path'), ' /') != 'wp-login.php')
            return str_replace('wp-login.php', trim($this->opt('new_login_path'), ' /'), $url);

        if ($url && $this->h->str_contains($url, 'wp-login.php') && !$this->h->str_contains($url, $login_query) && !$this->h->str_contains($url, $admin_key) && !$this->h->str_contains($url, 'ref_url'))
            return add_query_arg($login_query, $this->opt('admin_key'), $url);

        return $url;
    }


    function add_key_login_to_messages($msg)
    {
        if ($this->opt('login_query'))
            $login_query = $this->opt('login_query');
        else
            $login_query = 'hide_my_wp';

        if ($this->opt('admin_key'))
            $admin_key = $this->opt('admin_key');
        else
            $admin_key = '1234';

        if ($msg && $this->h->str_contains($msg, '/comment.php?') && !$this->h->str_contains($msg, $login_query . '=' . $admin_key))
            return str_replace('/comment.php?', '/comment.php?' . $login_query . '=' . $admin_key, $msg);

        return $msg;
    }


    function correct_logout_redirect()
    {
        $url = $_SERVER['PHP_SELF'];

        if ($this->opt('login_query'))
            $login_query = $this->opt('login_query');
        else
            $login_query = 'hide_my_wp';


        if ($this->h->ends_with($url, 'wp-login.php') && isset($_REQUEST['action']) && $_REQUEST['action'] == 'logout') {
            if (!$this->h->str_contains($_SERVER['REQUEST_URI'], '/' . $this->opt('new_login_path'))) {
                $redirect_to = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : 'wp-login.php?loggedout=true&' . $login_query . '=' . $this->opt('admin_key');
                wp_redirect($redirect_to);
                exit();
            }
        }
    }

    /**
     * HideMyWP::ob_starter()
     *
     * @return
     */
    function ob_starter()
    {
        ob_start(array(&$this, "global_html_filter"));
        //echo ob_get_level();
        if (class_exists('WooCommerce'))
            ob_start(); //Fix some WooCommerce themes bug
    }

    /*function do_shutdown(){
        $final = ob_get_level().'sdsds';

        // We'll need to get the number of ob levels we're in, so that we can iterate over each, collecting that buffer's output into the final output.
        $levels = ob_get_level();

        for ($i = 0; $i < $levels; $i++) {
            $final .= ob_get_clean();
        }

        echo $this->global_html_filter($final);
    }*/

    /**
     * HideMyWP::custom_404_page()
     *
     * @param mixed $templates
     * @return
     */
    function custom_404_page($templates)
    {
        global $current_user;
        $visitor = esc_attr((is_user_logged_in()) ? $current_user->user_login : $_SERVER["REMOTE_ADDR"]);

        if (is_multisite())
            $permalink = get_blog_permalink(BLOG_ID_CURRENT_SITE, $this->opt('custom_404_page'));
        else
            $permalink = get_permalink($this->opt('custom_404_page'));
        //$permalink = home_url('?'.$this->opt('page_query').'='.$this->opt('custom_404_page'));
        if ($this->opt('custom_404') && $this->opt('custom_404_page'))
            wp_redirect(add_query_arg(array('by_user' => $visitor, 'ref_url' => urldecode($_SERVER["REQUEST_URI"])), $permalink));
        else
            return $templates;

        die();

    }

    /**
     * HideMyWP::do_feed_base()
     *
     * @param boolean $for_comments
     * @return
     */
    function do_feed_base($for_comments)
    {
        if ($for_comments)
            load_template(ABSPATH . WPINC . '/feed-rss2-comments.php');
        else
            load_template(ABSPATH . WPINC . '/feed-rss2.php');
    }

    /**
     * HideMyWP::is_permalink()
     * Is permalink enabled?
     * @return
     */
    function is_permalink()
    {
        global $wp_rewrite;
        if (!isset($wp_rewrite) || !is_object($wp_rewrite) || !$wp_rewrite->using_permalinks())
            return false;
        return true;
    }

    /**
     * HideMyWP::block_access()
     *
     * @return
     */
    function block_access()
    {
        global $wp_query, $current_user;
        include_once(ABSPATH . '/wp-includes/pluggable.php');


        if (function_exists('is_user_logged_in') && is_user_logged_in())
            $visitor = $current_user->user_login;
        else
            $visitor = $_SERVER["REMOTE_ADDR"];

        $url = esc_url('http' . (empty($_SERVER['HTTPS']) ? '' : 's') . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
        // $wp_query->set('page_id', 2);
        // $wp_query->query(w$p_query->query_vars);

        if ($this->opt('spy_notifier')) {
            $body = "Hi-\nThis is %s plugin. We guess someone is researching about your WordPress site.\n\nHere is some more details:\nVisitor: %s\nURL: %s\nUser Agent: %s\n\nBest Regards,\n%s";
            $body = sprintf(__($body, self::slug), self::title, $visitor, $url, $_SERVER['HTTP_USER_AGENT'], self::title);
            $subject = sprintf(__('[%s] Someone is mousing!', self::slug), self::title);
            wp_mail(get_option('admin_email'), $subject, $body);
        }

        status_header(404);
        nocache_headers();

        $headers = array('X-Pingback' => get_bloginfo('pingback_url'));
        $headers['Content-Type'] = get_option('html_type') . '; charset=' . get_option('blog_charset');
        foreach ((array)$headers as $name => $field_value)
            @header("{$name}: {$field_value}");

        //if ( isset( $headers['Last-Modified'] ) && empty( $headers['Last-Modified'] ) && function_exists( 'header_remove' ) )
        //	@header_remove( 'Last-Modified' );


        //wp-login.php wp-admin and direct .php access can not be implemented using 'wp' hook block_access can't work correctly with init hook so we use wp_remote_get to fix the problem
        if ($this->h->str_contains($_SERVER['PHP_SELF'], '/wp-admin/') || $this->h->ends_with($_SERVER['PHP_SELF'], '.php')) {

            if ($this->opt('custom_404') && $this->opt('custom_404_page')) {
                wp_redirect(add_query_arg(array('by_user' => $visitor, 'ref_url' => urldecode($_SERVER["REQUEST_URI"])), home_url('?' . $this->opt('page_query') . '=' . $this->opt('custom_404_page'))));
            } else {
                $response = @wp_remote_get(home_url('/nothing_404_404' . $this->trust_key));

                if (!is_wp_error($response))
                    echo $response['body'];
                else
                    wp_redirect(home_url('/404_Not_Found'));
            }

        } else {
            if (get_404_template())
                require_once(get_404_template());
            else
                require_once(get_single_template());
        }

        die();
    }

    /**
     * HideMyWP::nice_search_redirect()
     *
     * @return
     */
    function nice_search_redirect()
    {
        global $wp_rewrite;
        if (!isset($wp_rewrite) || !is_object($wp_rewrite) || !$wp_rewrite->using_permalinks())
            return;

        if ($this->opt('nice_search_redirect') && $this->is_permalink()) {
            $search_base = $wp_rewrite->search_base;

            if (is_search() && strpos($_SERVER['REQUEST_URI'], "/{$search_base}/") === false) {
                if (isset($_GET['s']))
                    $keyword = get_query_var('s');

                if (isset($_GET[$this->opt('search_query')]))
                    $keyword = get_query_var($this->opt('search_query'));

                wp_redirect(home_url("/{$search_base}/" . urlencode($keyword)));
                exit();
            }
        }
    }


    /**
     * HideMyWP::remove_menu_class()
     *
     * @param array $classes
     * @return
     */
    function remove_menu_class($classes)
    {
        $new_classes = array();
        if (is_array($classes)) {
            foreach ($classes as $class) {
                if ($this->h->starts_with($class, 'current_'))
                    $new_classes[] = $class;

            }
        } else {
            $new_classes = '';
        }

        return $new_classes;

    }


    /**
     * HideMyWP::partial_filter()
     * Filter partial HTML
     * @param mixed $content
     * @return
     */
    function partial_filter($content)
    {

        if ($this->top_replace_old)
            $content = str_replace($this->top_replace_old, $this->top_replace_new, $content);

        if ($this->partial_replace_old)
            $content = str_replace($this->partial_replace_old, $this->partial_replace_new, $content);

        if ($this->partial_preg_replace_old)
            $content = preg_replace($this->partial_preg_replace_old, $this->partial_preg_replace_new, $content);

        return $content;
    }

    /**
     * HideMyWP::post_filter()
     * Filter post HTML
     * @param mixed $content
     * @return
     */
    function post_filter($content)
    {
        if ($this->post_replace_old)
            $content = str_replace($this->post_replace_old, $this->post_replace_new, $content);

        if ($this->post_preg_replace_old)
            $content = preg_replace($this->post_preg_replace_old, $this->post_preg_replace_new, $content);

        return $content;
    }

    function replace_field($type = 'replace_in_html')
    {
        $output = '<div class="field_wrapper ' . $type . '">';

        $replace_type = $this->h->replace_newline(trim($this->opt($type), ' '), '|');
        $replace_lines = explode('|', $replace_type);

        if ($replace_lines) {
            foreach ($replace_lines as $line) {
                if ($type == 'replace_in_html')
                    $replace_word = explode('=', $line);
                else
                    $replace_word = explode('==', $line);


                if (isset($replace_word[0]) && isset($replace_word[1])) {
                    $replace_word[0] = str_replace(array('[equal]', '[bslash]', '[new_line]'), array('=', "\\", "\n"), $replace_word[0]);
                    $replace_word[1] = str_replace(array('[equal]', '[bslash]', '[new_line]',), array('=', "\\", "\n"), $replace_word[1]);

                    $remove_checked = '';
                    $replace_checked = ' checked="checked" ';
                    $remove_hidden = '';
                    if (!$replace_word[1] && $type == 'replace_in_html') {
                        $remove_checked = ' checked="checked" ';
                        $replace_checked = '';
                        $remove_hidden = 'hidden';
                    } elseif ($replace_word[1] == 'nothing_404_404' && $type == 'replace_urls') {
                        $remove_checked = ' checked="checked" ';
                        $replace_checked = '';
                        $remove_hidden = 'hidden';
                    }

                    $rand = rand(1, 10000);

                    $output .= '<div class="hmwp_field_row">';
                    $output .= '<textarea name="' . $type . '1[]" class="first_field"/>' . $replace_word[0] . '</textarea>';

                    $output .= '<div class="action_field">';
                    if ($type == 'replace_in_html') {
                        $output .= '<label><input type="radio" ' . $replace_checked . ' class="html_actiontype radio" value="replace" name="html_actiontype_' . $rand . '" >Replace</label>
<br>';
                        $output .= '<label><input type="radio" ' . $remove_checked . ' class="html_actiontype radio" value="remove" name="html_actiontype_' . $rand . '" >Remove</label>
</div>';
                    } else {
                        $output .= '<label><input type="radio" ' . $replace_checked . ' class="url_actiontype radio" value="replace" name="urls_actiontype_' . $rand . '" >Replace</label>
<br>';
                        $output .= '<label><input type="radio" ' . $remove_checked . ' class="url_actiontype radio" value="remove" name="urls_actiontype_' . $rand . '" >Hide (404)</label>
</div>';
                    }

                    $output .= '<textarea style="visibility:' . $remove_hidden . '" name="' . $type . '2[]" class="second_field"/>' . $replace_word[1] . '</textarea>';

                    $output .= '<a href="javascript:void(0);" class="button hmwp_action hmwp_remove_button" title="Remove Rule"><img src="' . self::url . '/img/delete.png" width="12"/>
                  </a>
';

                    $output .= '</div><div class="clear"></div>';


                }
            }
        }
        $output .= '<style>.first_field,.second_field, .action_field{float:left;}
.action_field{padding:10px;}
.hmwp_field_row{ margin:10px 3px;}
.hmwp_action{margin: 4px !important;}
</style>';

        $output .= '<a href="javascript:void(0);" class="button hmwp_action htmwp_add_button " title="Add Rule">
                               <img src="' . self::url . '/img/add.png" width="12" />
                               Add
                          </a>';
        $output .= '</div>';

        if ($type == 'replace_in_html') {
            $output .= "<br/><span class='description'>Do not use this to change URLs<br>Use<code>[bslash]</code> for '\'<br>Base on OSes multiple lines queries may work or not so please check.'</span>";
        } else {
            $output .= "<br/><span class='description'>Use this only to change URLs. <br>Relative path base on WP directory. e.g. wp-content/plugins/woocommerce/assets/css/woocommerce.css Replace ec.css<br>You can also replace some kind of custom paths<br>Add '/' at the end of the first path to change all files at the folder.  </span>";
        }

        return $output;
    }

    function disable_main_wp_query($sql, WP_Query $wpQuery)
    {
        if ($wpQuery->is_main_query() && (isset($_GET['style_internal_wrapper']) || isset($_GET['script_internal_wrapper']) || isset($_GET['style_wrapper']) || isset($_GET['get_wrapper']) || isset($_GET['parent_wrapper']) || isset($_GET['template_wrapper']))) {
            /* prevent SELECT FOUND_ROWS() query*/
            $wpQuery->query_vars['no_found_rows'] = true;

            /* prevent post term and meta cache update queries */
            $wpQuery->query_vars['cache_results'] = false;

            return false;
        }
        return $sql;
    }


    /**
     * HideMyWP::global_html_filter()
     * Filter output HTML
     * @param mixed $buffer
     * @return
     */
    function global_html_filter($buffer)
    {

        $this->none_replaced_buffer = $buffer;

        //not replace for crons
        if (!$this->is_html($buffer) && isset($_GET['die_message']) || isset($_GET['style_internal_wrapper']) || isset($_GET['script_internal_wrapper']) || isset($_GET['style_wrapper']) || isset($_GET['get_wrapper']) || isset($_GET['parent_wrapper']) || isset($_GET['template_wrapper']) || isset($_GET['doing_wp_cron']))
            return $buffer;


        if (is_admin() && $this->admin_replace_old) {
            $buffer = str_replace($this->admin_replace_old, $this->admin_replace_new, $buffer);
            return $buffer;
        }

        if ($this->opt('replace_in_ajax')) {
            if (is_admin() && !defined('DOING_AJAX'))
                return $buffer;
        } else {
            if (is_admin())
                return $buffer;
        }

        //first minify rocket then change other URLS
		if (defined('WP_ROCKET_VERSION')) {
			if (version_compare(WP_ROCKET_VERSION, '3.1') >= 0) {
				@define('WP_ROCKET_WHITE_LABEL_FOOTPRINT', 1);
			} else {
				@define('WP_ROCKET_WHITE_LABEL_FOOTPRINT', 1);
				if (function_exists('rocket_minify_process')) {
					$buffer = rocket_minify_process($buffer);
				}
			}
		}

		if ($this->opt('remove_html_comments') && !defined('DOING_AJAX')) {
            if ($this->opt('remove_html_comments') == 'simple') {
                $this->preg_replace_old[] = '/<!--(.*?)-->/';
                $this->preg_replace_new[] = ' ';
                $this->preg_replace_old[] = "%(\n){2,}%";
                $this->preg_replace_new[] = "\n";

            } elseif ($this->opt('remove_html_comments') == 'quick') {
                //comments and more than 2 space or line break will be remove. Simple & quick but not perfect!
                $this->preg_replace_old[] = '!/\*.*?\*/!s';
                $this->preg_replace_new[] = ' ';
                $this->preg_replace_old[] = '/\n\s*\n/';
                $this->preg_replace_new[] = ' ';
                $this->preg_replace_old[] = '/<!--(.*?)-->/';
                $this->preg_replace_new[] = ' ';
                $this->preg_replace_old[] = "%(\s){3,}%";
                $this->preg_replace_new[] = ' ';

            } elseif ($this->opt('remove_html_comments') == 'safe') {
                require_once('lib/class.HTML-minify.php');
                $min = new Minify_HTML($buffer, array('xhtml' => true));
                $buffer = $min->process();
            }
        }

        if ($this->top_replace_old)
            $buffer = str_replace($this->top_replace_old, $this->top_replace_new, $buffer);

        if ($this->opt('replace_in_html')) {
            $replace_in_html = $this->h->replace_newline(trim($this->opt('replace_in_html'), ' '), '|%|');
            $replace_lines = explode('|%|', $replace_in_html);

            if ($replace_lines) {
                foreach ($replace_lines as $line) {
                    $replace_word = explode('=', $line);

                    if (isset($replace_word[0]) && isset($replace_word[1])) {
                        $replace_word[0] = str_replace(array('[equal]', '[bslash]', '[new_line]'), array('=', "\\", "\n"), $replace_word[0]);

                        $replace_word[1] = str_replace(array('[equal]', '[bslash]', '[new_line]'), array('=', "\\", "\n"), $replace_word[1]);

                        $this->replace_old[] = trim($replace_word[0], ' ');
                        $this->replace_new[] = trim($replace_word[1], ' ');
                    }
                }
            }
        }

//good but problem to find exclude ie and src= styles
        if ($this->opt('auto_internal')) {

            //!isset($_GET['doing_wp_cron']) && !style_internal_wrapper
            $blog_id = get_current_blog_id();
            if (!is_multisite())
                $old = get_option('hmwp_internal_assets');
            else
                $old = get_blog_option($blog_id, 'hmwp_internal_assets');
            if ($old)
                $new = $old;
            else
                $new = array('css' => '', 'js' => '');
        }

        if ($this->opt('auto_internal') == 1 || $this->opt('auto_internal') == 3) {

            preg_match_all("@<style(.*?)>(.*?)</style>@is",//conflict if it have inline ie tags
                $buffer,
                $matches,
                PREG_PATTERN_ORDER);

            $new_css = '';
            if (is_array($matches)) {
                for ($i = 0; $i < count($matches[1]); $i++) {
                    if (!$matches[1][$i] || (!stristr("print'", $matches[1][$i]) && !stristr('print"', $matches[1][$i])))
                        $new_css .= $matches[2][$i] . "\n";
                }
            }

            //or is_home is_archive is_single is_author is_feed
            if ($new_css)
                $new['css'] = $new_css;

            $this->preg_replace_old[] = "@<style(.*?)>(.*?)</style>@is"; //prints will be removed but not remain|conflict if had inline ie tags
            $this->preg_replace_new[] = " ";
        }

        if ($this->opt('auto_internal') >= 2) {

            preg_match_all("@<script(.*?)>(.*?)</script>([\s]*\<\!\[)?@is",  //still not <!--<![
//conflict with ie conditional tag will be added
                $buffer,
                $matches,
                PREG_PATTERN_ORDER);

            $new_js = '';
            if (is_array($matches)) {
                for ($i = 0; $i < count($matches[1]); $i++) { //should be tested
                    if (!$matches[1][$i] || (!stristr("src=", $matches[1][$i]) && !stristr('<![', $matches[3][$i])))
                        $new_js .= $matches[2][$i] . "\n";
                }
            }

            //or is_home is_archive is_single is_author is_feed
            if ($new_js)
                $new['js'] = $new_js;

            $this->preg_replace_old[] = "@(<s2cript[^>]*>)(.*?)(</script>)@is";
            $this->preg_replace_new[] = "$1$3"; //src will remain the same
        }

        if (isset($new) && $new && $new != $old) {

            if (!is_multisite())
                update_option('hmwp_internal_assets', $new);
            else
                update_blog_option($blog_id, 'hmwp_internal_assets', $new);
        }


        if ($this->opt('cdn_path')) {

            if (trim($this->opt('new_theme_path'), ' /')) {
                $this->replace_old[] = site_url(trim($this->opt('new_theme_path'), ' /'));
                $this->replace_new[] = trim($this->opt('cdn_path'), '/ ') . '/' . trim($this->opt('new_theme_path'), ' /');

            } else {
                $this->replace_old[] = site_url(str_replace(ABSPATH, '', WP_CONTENT_DIR . '/themes'));
                $this->replace_new[] = trim($this->opt('cdn_path'), '/ ') . '/' . str_replace(ABSPATH, '', WP_CONTENT_DIR . '/themes');
            }

            if (trim($this->opt('new_plugin_path'), ' /')) {
                $this->replace_old[] = site_url(trim($this->opt('new_plugin_path'), ' /'));
                $this->replace_new[] = trim($this->opt('cdn_path'), '/ ') . '/' . trim($this->opt('new_plugin_path'), ' /');

            } else {
                $this->replace_old[] = site_url(str_replace(ABSPATH, '', WP_PLUGIN_DIR));
                $this->replace_new[] = trim($this->opt('cdn_path'), '/ ') . '/' . site_url(str_replace(ABSPATH, '', WP_PLUGIN_DIR));
            }

            if (trim($this->opt('new_include_path'), ' /')) {
                $this->replace_old[] = site_url(trim($this->opt('new_include_path'), ' /'));
                $this->replace_new[] = trim($this->opt('cdn_path'), '/ ') . '/' . trim($this->opt('new_include_path'), ' /');

            } else {
                $this->replace_old[] = site_url(WPINC);
                $this->replace_new[] = trim($this->opt('cdn_path'), '/ ') . '/' . WPINC;
            }


            if (trim($this->opt('new_upload_path'), ' /')) {
                $this->replace_old[] = site_url(trim($this->opt('new_upload_path'), ' /'));
                $this->replace_new[] = trim($this->opt('cdn_path'), '/ ') . '/' . trim($this->opt('new_upload_path'), ' /');

            } else {
                $this->replace_old[] = site_url('wp-content/uploads');
                $this->replace_new[] = trim($this->opt('cdn_path'), '/ ') . '/' . 'wp-content/uploads';
            }
        }


        if ($this->opt('replace_mode') == 'safe' && $this->partial_replace_old)
            $buffer = str_replace($this->partial_replace_old, $this->partial_replace_new, $buffer);

        if ($this->opt('replace_mode') == 'safe' && $this->partial_preg_replace_old)
            $buffer = preg_replace($this->partial_preg_replace_old, $this->partial_preg_replace_new, $buffer);


        if ($this->replace_old)
            $buffer = str_replace($this->replace_old, $this->replace_new, $buffer);

        if ($this->preg_replace_old)
            $buffer = preg_replace($this->preg_replace_old, $this->preg_replace_new, $buffer);

        return $buffer;

    }

    /**
     * HideMyWP::remove_ver_scripts()
     *
     * @param string $src
     * @return
     */
    function remove_ver_scripts($src)
    {
        if (strpos($src, 'ver='))
            $src = remove_query_arg('ver', $src);
        return $src;
    }

    function spam_blocker_fake_field($fields)
    {
        $fake = '<input type="text" name="author" data-hwm="" value="" class="f_author_hm"> <style type="text/css">.f_author_hm{display:none;}</style>';
        $fields ['author'] = str_replace('</label>', '</label>' . $fake, $fields ['author']);
        return $fields;
    }

    /**
     * HideMyWP::spam_blocker()
     * Check queries before saving comment
     * @param string $src
     * @return
     */
    function spam_blocker($post_id)
    {

        $check = false;
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post' && ($this->h->ends_with($_SERVER['PHP_SELF'], $this->opt('replace_comments_post')) || $this->h->ends_with($_SERVER['PHP_SELF'], 'wp-comments-post.php')))
            $check = true;

        if (!$check)
            return $post_id;

        (array)$counter = get_option('hmwp_spam_counter');

        if (!isset($counter['1']))
            $counter['1'] = 0;

        if (!isset($counter['2']))
            $counter['2'] = 0;

        if ($this->opt('login_query'))
            $login_query = $this->opt('login_query');
        else
            $login_query = 'hide_my_wp';

        $spam = false;
        if ($this->is_permalink() && $this->opt('replace_comments_post') && (!isset($_GET[$this->short_prefix . $login_query]) || $_GET[$this->short_prefix . $login_query] != $this->opt('admin_key'))) {
            $counter['1']++;
            $spam = true;
        }


        if (isset($_POST['email']) && !isset($_POST['authar'])) {
            $counter['2']++;
            $spam = true;
        }

        if (isset($_POST['author']) && strlen($_POST['author']) > 0) {
            $counter['2']++;
            $spam = true;
        }

        if ($spam) {
            update_option('hmwp_spam_counter', $counter);
            die('You\'re spam! aren\'t you?');
        }

        if (isset($_POST['authar']) && $_POST['authar'])
            $_POST['author'] = $_POST['authar'];

        return $post_id;
    }


    /**
     * HideMyWP::global_assets_filter()
     * Generate new style from main file
     * @return
     */
    function global_assets_filter()
    {
        global $wp_query;

        if ($this->opt('login_query'))
            $login_query = $this->opt('login_query');
        else
            $login_query = 'hide_my_wp';

        $new_style_path = trim($this->opt('new_style_name'), ' /');

		do_action('before_global_assets_filter', $this, $login_query);

		//$this->h->ends_with($_SERVER["REQUEST_URI"], 'main.css') ||   <- For multisite
        if (isset($wp_query->query_vars['style_wrapper']) && $wp_query->query_vars['style_wrapper'] && $this->is_permalink()) {
            if ($this->opt('full_hide') && $this->opt('admin_key')) {
                if (!isset($_GET[$this->short_prefix . $login_query]) || $_GET[$this->short_prefix . $login_query] != $this->opt('admin_key'))
                    return false;

            }

            if (is_multisite() && isset($wp_query->query_vars['template_wrapper']))
                $css_file = str_replace(get_stylesheet(), $wp_query->query_vars['template_wrapper'], get_stylesheet_directory()) . '/style.css';
            else
                $css_file = get_stylesheet_directory() . '/style.css';

            status_header(200);
            //$expires = 60*60*24; // 1 day
            $days_to_expire = $this->opt('style_expiry_days');
            if(!is_numeric($days_to_expire) || $days_to_expire <= 0) {
                $days_to_expire = 3; //default
            }
            $expires = 60 * 60 * 24 * $days_to_expire; //3 day
            header("Pragma: public");
            header("Cache-Control: maxage=" . $expires);
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
            header('Content-type: text/css; charset=UTF-8');

            $css = file_get_contents($css_file);

            if ($this->opt('minify_new_style')) {
                if ($this->opt('minify_new_style') == 'quick') {
                    $to_remove = array('%\n\r%', '!/\*.*?\*/!s', '/\n\s*\n/', "%(\s){1,}%");
                    $css = preg_replace($to_remove, ' ', $css);
                } elseif ($this->opt('minify_new_style') == 'safe') {
                    require_once('lib/class.CSS-minify.php');
                    $css = Minify_CSS_Compressor::process($css, array());
                }

            }

            if ($this->opt('clean_new_style')) {
                if (strpos($css, 'alignright') === false) {  //Disable it if it uses import or so on
                    if (is_multisite()) {
                        $opts = get_blog_option(BLOG_ID_CURRENT_SITE, self::slug);
                        $opts['clean_new_style'] = '';
                        update_blog_option(BLOG_ID_CURRENT_SITE, self::slug, $opts);
                    } else {
                        $opts = get_option(self::slug);
                        $opts['clean_new_style'] = '';
                        update_option(self::slug, $opts);
                    }
                } else {
                    $old = array('wp-caption', 'alignright', 'alignleft', 'alignnone', 'aligncenter');
                    $new = array('x-caption', 'x-right', 'x-left', 'x-none', 'x-center');
                    $css = str_replace($old, $new, $css);
                }
                //We replace HTML, too
            }

            // if (is_child_theme())
            //     $css = str_replace('/thematic/', '/parent/', $css);

            echo $css;

            //  if(extension_loaded('zlib'))
            //     ob_end_flush();

            exit;
        }

        if ((isset($wp_query->query_vars['parent_wrapper']) && $wp_query->query_vars['parent_wrapper'] && $this->is_permalink())) {

            if ($this->opt('full_hide') && $this->opt('admin_key')) {
                if (!isset($_GET[$this->short_prefix . $login_query]) || $_GET[$this->short_prefix . $login_query] != $this->opt('admin_key'))
                    return false;
            }

            if (is_multisite() && isset($wp_query->query_vars['template_wrapper']))
                $css_file = str_replace(get_template(), $wp_query->query_vars['template_wrapper'], get_template_directory()) . '/style.css';
            else
                $css_file = get_template_directory() . '/style.css';


            status_header(200);
            //$expires = 60*60*24; // 1 day
            $expires = 60 * 60 * 24 * 3; //3 day
            header("Pragma: public");
            header("Cache-Control: maxage=" . $expires);
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
            header('Content-type: text/css; charset=UTF-8');

            $css = file_get_contents($css_file);

            if ($this->opt('minify_new_style')) {
                if ($this->opt('minify_new_style') == 'quick') {
                    $to_remove = array('%\n\r%', '!/\*.*?\*/!s', '/\n\s*\n/', "%(\s){1,}%");
                    $css = preg_replace($to_remove, ' ', $css);
                } elseif ($this->opt('minify_new_style') == 'safe') {
                    require_once('lib/class.CSS-minify.php');
                    $css = Minify_CSS_Compressor::process($css, array());
                }


            }

            if ($this->opt('clean_new_style')) {
                if (strpos($css, 'alignright') === false) {  //Disable it if it uses import or so on
                    if (is_multisite()) {
                        $opts = get_blog_option(BLOG_ID_CURRENT_SITE, self::slug);
                        $opts['clean_new_style'] = '';
                        update_blog_option(BLOG_ID_CURRENT_SITE, self::slug, $opts);
                    } else {
                        $opts = get_option(self::slug);
                        $opts['clean_new_style'] = '';
                        update_option(self::slug, $opts);
                    }
                } else {
                    $old = array('wp-caption', 'alignright', 'alignleft', 'alignnone', 'aligncenter');
                    $new = array('x-caption', 'x-right', 'x-left', 'x-none', 'x-center');
                    $css = str_replace($old, $new, $css);
                }
                //We replace HTML, too
            }

            // if (is_child_theme())
            //     $css = str_replace('/thematic/', '/parent/', $css);

            echo $css;

            //  if(extension_loaded('zlib'))
            //     ob_end_flush();

            exit;
        }


        if ((isset($wp_query->query_vars['style_internal_wrapper']) && $wp_query->query_vars['style_internal_wrapper'] && $this->is_permalink())) {

            if ($this->opt('full_hide') && $this->opt('admin_key')) {
                if (!isset($_GET[$this->short_prefix . $login_query]) || $_GET[$this->short_prefix . $login_query] != $this->opt('admin_key'))
                    return false;
            }

            status_header(200);
            //$expires = 60*60*24; // 1 day
            $expires = 60 * 60 * 24 * 10; //10 days
            header("Pragma: public");
            header("Cache-Control: maxage=" . $expires);
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
            header('Content-type: text/css; charset=UTF-8');

            $blog_id = get_current_blog_id();
            if (!is_multisite())
                $old = get_option('hmwp_internal_assets');
            else
                $old = get_blog_option($blog_id, 'hmwp_internal_assets');

            if (is_array($old))
                echo $old['css'] . "\n\n\n";

            echo $this->auto_config_internal_css;
            echo $this->opt('internal_css');

            //  if(extension_loaded('zlib'))
            //     ob_end_flush();
            exit;
        }


        if ((isset($wp_query->query_vars['script_internal_wrapper']) && $wp_query->query_vars['script_internal_wrapper'] && $this->is_permalink())) {

            if ($this->opt('full_hide') && $this->opt('admin_key')) {
                if (!isset($_GET[$this->short_prefix . $login_query]) || $_GET[$this->short_prefix . $login_query] != $this->opt('admin_key'))
                    return false;
            }

            status_header(200);
            //$expires = 60*60*24; // 1 day
            $expires = 60 * 60 * 24 * 10; //10 day
            header("Pragma: public");
            header("Cache-Control: maxage=" . $expires);
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
            header('Content-type: application/javascript; charset=UTF-8');

            $blog_id = get_current_blog_id();
            if (!is_multisite())
                $old = get_option('hmwp_internal_assets');
            else
                $old = get_blog_option($blog_id, 'hmwp_internal_assets');

            if (is_array($old))
                echo $old['js'] . "\n\n\n";

            //todo: avoid duplicate with auto_internal
            echo $this->auto_config_internal_js;
            echo $this->opt('internal_js');

            //  if(extension_loaded('zlib'))
            //     ob_end_flush();
            exit;
        }


        if ((isset($wp_query->query_vars['get_wrapper']) && $wp_query->query_vars['get_wrapper'] && $this->is_permalink() && isset($_GET['_case']) && isset($_GET['_addr']))) {
//RewriteRule ^_get/([A-Za-z0-9-_\.]+)/(.*) /wp39/index.php?get_wrapper=1&_case=$1&_addr=$2&AK_hide_my_wp=1234 [QSA,L]

            if ($this->opt('full_hide') && $this->opt('admin_key')) {
                if (!isset($_GET[$this->short_prefix . $login_query]) || $_GET[$this->short_prefix . $login_query] != $this->opt('admin_key'))
                    return false;
            }

            $host = $url = '';
            $data = array();
            $cache = true;

            switch ($_GET['_case']) {

                case 'ws0':
                    $host = 'https://s0.wp.com/'; //add /
                    $cache = false;
                    break;
                case 'ws0i':
                    $host = 'https://s0.wp.com/i/'; //add /
                    $cache = true;
                    break;
                case 'stats':
                    $host = 'http://stats.wp.com/';
                    $cache = false;
                    break;
                case 'ws0js':
                    $host = 'http://s0.wp.com/wp-content/js/';
                    if ($_GET['_addr'] == 'devicepx-jetpack.js')
                        $_GET['_addr'] = 'devicepx.js';
                    $cache = true;
                    break;
                default:
                    $host = 'Invalid';
                    $cache = false;
                    break;
            }


            $url = $host . $_GET['_addr'];

            //echo $url;
            $cache_name = hash('crc32', $_GET['_case'] . $_GET['_addr']);

            if ($cache)
                $data = get_transient($cache_name);

            if (!$data) {
                $data = @wp_remote_get($url, 'reject_unsafe_urls=true&sslverify=true&user-agent=Mozilla/5.0 (Windows NT 6.1; WOW64; rv:18.0) Gecko/20100101 Firefox/18.0&limit_response_size=' . 3 * 1024 * 1024); //3mb
                if ($cache && is_array($data))
                    set_transient($cache_name, $data, 10 * DAY_IN_SECONDS);
            }

            //print_r($data);
            if (!is_wp_error($data) && $data['body']) {

                // print_r($data['headers']->getAll());

                status_header($data['response']['code']);

                //$expires = 60 * 60 * 24 * 10; //10 days
                if ($data['headers']->offsetGet('content-type'))
                    header('Content-Type: ' . $data['headers']->offsetGet('content-type'));

                if (is_array($data['headers']->offsetGet('cache-control'))) {
                    foreach ($data['headers']->offsetGet('cache-control') as $control)
                        header("Cache-Control: " . $control);
                } else {
                    header("Cache-Control: " . $data['headers']->offsetGet('cache-control'));
                }

                if ($data['headers']->offsetGet('pragma'))
                    header('Pragma: ' . $data['headers']->offsetGet('pragma'));

                if ($data['headers']->offsetGet('expires'))
                    header('Expires: ' . $data['headers']->offsetGet('expires'));

                echo $data['body'];
            } else {
                status_header('400');
                echo 'Wrapping Error';
            }
            exit;
        }

		do_action('after_global_assets_filter', $this, $login_query);
    }


    function redirect_canonical($req)
    {
        print_r($req);
        // return $output;
    }

    /**
     * HideMyWP::init()
     *
     * @return
     */
    function init()
    {
        require_once('init.php');
    }

    /**
     * HideMyWP::remove_default_description()
     *
     * @param mixed $bloginfo
     * @return
     */
    function remove_default_description($bloginfo)
    {
        return ($bloginfo == 'Just another WordPress site') ? '' : $bloginfo;
    }


    /**
     * HideMyWP::body_class_filter()
     * Only store page class
     * @param mixed $bloginfo
     * @return
     */
    function body_class_filter($classes)
    {
        $new_classes = array();
        if (is_array($classes)) {
            foreach ($classes as $class) {
                if ($class == 'home' || $class == 'blog' || $class == 'category' || $class == 'tag' || $class == 'rtl' || $class == 'author' || $class == 'archive' || $class == 'single' || $class == 'attachment' || $class == 'search' || $class == 'custom-background')
                    $new_classes[] = $class;

            }
        } else {
            $new_classes = '';
        }

        return $new_classes;
    }

    /**
     * HideMyWP::post_class_filter()
     * Only store post format, post_types and sticky
     * @param mixed $bloginfo
     * @return
     */
    function post_class_filter($classes)
    {
        $post_types = get_post_types();
        $new_classes = array();
        if (is_array($classes)) {
            foreach ($classes as $class) {
                if (($class != 'format-standard' && $this->h->starts_with($class, 'format-')) || $class == 'sticky')
                    $new_classes[] = $class;
                foreach ($post_types as $post_type)
                    if ($class == $post_type)
                        $new_classes[] = $class;

            }
        } else {
            $new_classes = '';
        }

        return $new_classes;
    }

    function admin_current_cookie()
    {
        if (SITECOOKIEPATH)
            $current_cookie = str_replace(SITECOOKIEPATH, '', ADMIN_COOKIE_PATH);

        //For non-sudomain and with pathes mu:
        if (!$current_cookie)
            $current_cookie = 'wp-admin';

        return $current_cookie;
    }

    /**
     * HideMyWP::add_rewrite_rules()
     *
     * @param mixed $wp_rewrite
     * @return
     */
    function add_rewrite_rules($wp_rewrite)
    {
        global $wp_rewrite, $wp;

        if (is_multisite()) {
            global $current_blog;
            $sitewide_plugins = array_keys((array)get_site_option('active_sitewide_plugins', array()));
            $active_plugins = array_merge((array)get_blog_option(BLOG_ID_CURRENT_SITE, 'active_plugins'), $sitewide_plugins);
        } else {
            $active_plugins = get_option('active_plugins');
        }


        if ($this->opt('rename_plugins') == 'all')
            $active_plugins = get_option('hmw_all_plugins');


        if ($this->opt('replace_urls') || $this->auto_replace_urls) {
            $replace_urls = $this->h->replace_newline(trim($this->opt('replace_urls'), ' '), '|%|');
            $replace_lines = explode('|%|', $replace_urls);
            $replace_lines = array_merge($replace_lines, $this->auto_replace_urls);

            if ($replace_lines) {
                foreach ($replace_lines as $line) {

                    $replace_word = explode('==', $line);
                    if (isset($replace_word[0]) && isset($replace_word[1])) {

                        //Check whether last character is / or not to recgnize folders
                        $is_folder = false;
                        if (substr($replace_word[0], strlen($replace_word[0]) - 1, strlen($replace_word[0])) == '/')
                            $is_folder = true;

                        $replace_word[0] = trim($replace_word[0], '/ ');
                        $replace_word[1] = trim($replace_word[1], '/ ');

                        $is_block = false;
                        if ($replace_word[1] == 'nothing_404_404')
                            $is_block = true;


                        if (!$is_block) {
                            $this->top_replace_old[] = $replace_word[0];
                            $this->top_replace_new[] = $replace_word[1];
                        }

                        if ($is_block) {
                            //Swap words to make theme unavailable
                            $temp = $replace_word[0];
                            $replace_word[0] = $replace_word[1];
                            $replace_word[1] = $temp;
                        }

                        $replace_word[0] = str_replace(array('amp;', '%2F', '//', '.'), array('', '/', '/', '.'), $replace_word[0]);
                        $replace_word[1] = str_replace(array('.', 'amp;'), array('\.', ''), $replace_word[1]);

                        if ($is_folder) {
                            $new_non_wp_rules[$rule_1 . '/(.*)'] = $this->sub_folder . $rule_0 . '/$1' . $this->trust_key;
                        } else {
							$rule_0 = trim($replace_word[0], '/ ');
							$rule_1 = trim($replace_word[1], '/ ');
							$file_url = $this->sub_folder . $rule_0;
							if (strpos($file_url, '?') !== FALSE) {
								$new_non_wp_rules[$rule_1] = $file_url . str_replace('?', '&', $this->trust_key);
								;
							} else {
								$new_non_wp_rules[$rule_1] = $file_url . $this->trust_key;
							}
						}
                    }
                }
            }
        }


        //Order is important
        if ($this->opt('rename_plugins') && $this->opt('new_plugin_path') && $this->is_permalink()) {
            foreach ((array)$active_plugins as $active_plugin) {

                //Ignore itself or a plugin without folder
                if (!$this->h->str_contains($active_plugin, '/') || $active_plugin == self::main_file || strpos($active_plugin, 'elementor') !== FALSE)
                    continue;

                $new_plugin_path = trim($this->opt('new_plugin_path'), '/ ');

                $codename_this_plugin = $this->hash($active_plugin);

                $rel_this_plugin_path = trim(str_replace(site_url(), '', plugin_dir_url($active_plugin)), '/');
                //Allows space in plugin folder name
                $rel_this_plugin_path = $this->sub_folder . str_replace(' ', '\ ', $rel_this_plugin_path);

                $new_this_plugin_path = $new_plugin_path . '/' . $codename_this_plugin;
                $new_non_wp_rules[$new_this_plugin_path . '/(.*)'] = $rel_this_plugin_path . '/$1' . $this->trust_key;

                if (is_multisite()) {
                    if ($this->is_subdir_mu)
                        $new_this_plugin_path = '/' . $new_this_plugin_path;
                    $rel_this_plugin_path = $this->blog_path . str_replace($this->sub_folder, '', $rel_this_plugin_path);
                }

                $this->partial_replace_old[] = $rel_this_plugin_path . '/';
                $this->partial_replace_new[] = $new_this_plugin_path . '/';

                if ($this->opt('replace_javascript_path') > 1) {
                    $this->replace_old[] = str_replace('/', '\/', $rel_this_plugin_path . '/');
                    $this->replace_new[] = str_replace('/', '\/', $new_this_plugin_path . '/');
                }


            }
        }

        if ($this->opt('new_include_path') && $this->is_permalink()) {
            $rel_include_path = $this->sub_folder . trim(WPINC);
            $new_include_path = trim($this->opt('new_include_path'), '/ ');

            $new_non_wp_rules[$new_include_path . '/(.*)'] = $rel_include_path . '/$1' . $this->trust_key;

            if (is_multisite()) {
                $rel_include_path = $this->blog_path . str_replace($this->sub_folder, '', $rel_include_path);
                if ($this->is_subdir_mu)
                    $new_include_path = '/' . $new_include_path;
            }

            $this->partial_replace_old[] = $rel_include_path;
            $this->partial_replace_new[] = $new_include_path;
        }

        $rel_admin_path = $this->sub_folder . 'wp-admin';
        $new_admin_path = trim($this->opt('new_admin_path'), '/ ');


        if ($new_admin_path && $new_admin_path != 'wp-admin' && $this->is_permalink()) {

            /*  if (trim(get_option('hmwp_temp_admin_path'), ' /'))
                  $new_admin_path = trim(get_option('hmwp_temp_admin_path'), ' /');
              else
                  $new_admin_path = trim($this->opt('new_admin_path'), '/ ');*/

            $new_non_wp_rules[$new_admin_path . '/(.*)'] = $rel_admin_path . '/$1' . $this->trust_key;

            if (is_multisite()) {
                if ($this->is_subdir_mu)
                    $new_admin_path = '/' . $new_admin_path;
                $rel_admin_path = $this->blog_path . str_replace($this->sub_folder, '', $rel_admin_path);
            }
            //Add / to fix stylesheet and other 'wp-admin'
            //will break all Replace URLs to wp-admin plus all urls of it
            $this->admin_replace_old[] = $rel_admin_path . '/';
            $this->admin_replace_new[] = $new_admin_path . '/';


            //Fix config code for HMWP nginx / multisite, etc
            if (isset($_GET['page']) && $_GET['page'] == self::slug) {
                $this->admin_replace_old[] = $new_admin_path . '/$';
                $this->admin_replace_new[] = $rel_admin_path . '/$';

                $this->admin_replace_old[] = $new_admin_path . '/admin-ajax.php [QSA';
                $this->admin_replace_new[] = 'wp-admin/admin-ajax.php' . $this->trust_key . ' [QSA';

                $this->admin_replace_old[] = $new_admin_path . '/(!network';
                $this->admin_replace_new[] = 'wp-admin/(!network';

                $this->admin_replace_old[] = $new_admin_path . '/admin-ajax.php last;';
                $this->admin_replace_new[] = 'wp-admin/admin-ajax.php' . $this->trust_key . ' last;';
            }

        }


        if ($this->opt('new_upload_path') && $this->is_permalink()) {
            $upload_path = wp_upload_dir();

            if (is_ssl())
                $upload_path['baseurl'] = str_replace('http:', 'https:', $upload_path['baseurl']);

            if (is_multisite() && $current_blog->blog_id != BLOG_ID_CURRENT_SITE) {

                $upload_path_array = explode('/', $upload_path['baseurl']);
                array_pop($upload_path_array);
                array_pop($upload_path_array);
                $upload_path['baseurl'] = implode('/', $upload_path_array);

            }

            $rel_upload_path = $this->sub_folder . trim(str_replace(site_url(), '', $upload_path['baseurl']), '/');;
            $new_upload_path = trim($this->opt('new_upload_path'), '/ ');
            $new_non_wp_rules[$new_upload_path . '/(.*)'] = $rel_upload_path . '/$1' . $this->trust_key;

            if (is_multisite()) {
                $rel_upload_path = str_replace($this->sub_folder, '', $rel_upload_path);
                if ($this->is_subdir_mu)
                    $new_upload_path = str_replace($this->blog_path, '/', home_url($new_upload_path));
            }


            $this->replace_old[] = home_url($rel_upload_path);  //Fix external images problem

            if (is_multisite())
                $this->replace_new[] = $new_upload_path; //already added home_url!
            else
                $this->replace_new[] = home_url($new_upload_path);

            if ($this->opt('replace_javascript_path') > 2) {
                $this->replace_old[] = str_replace('/', '\/', $rel_upload_path);
                $this->replace_new[] = str_replace('/', '\/', $new_upload_path);
            }
        }


        if ($this->opt('new_login_path') && $this->opt('new_login_path') != 'wp-login.php' && $this->is_permalink()) {

            $rel_login_path = $this->sub_folder . '/wp-login.php';

            $new_login_path = trim($this->opt('new_login_path'), '/ ');
            $new_non_wp_rules[$new_login_path] = $rel_login_path . $this->trust_key;

            if (is_multisite()) {
                if ($this->is_subdir_mu)
                    $new_login_path = '/' . $new_login_path;
                $rel_login_path = $this->blog_path . str_replace($this->sub_folder, '', $new_login_path);
            }

//            $this->partial_replace_old[]=$rel_plugin_path;
//            $this->partial_replace_new[]=$new_plugin_path;


        }


        if ($this->opt('new_plugin_path') && $this->is_permalink()) {
            $rel_plugin_path = $this->sub_folder . trim(str_replace(site_url(), '', HMW_WP_PLUGIN_URL), '/');

            $new_plugin_path = trim($this->opt('new_plugin_path'), '/ ');
            $new_non_wp_rules[$new_plugin_path . '/(.*)'] = $rel_plugin_path . '/$1' . $this->trust_key;

            if (is_multisite()) {
                if ($this->is_subdir_mu)
                    $new_plugin_path = '/' . $new_plugin_path;
                $rel_plugin_path = $this->blog_path . str_replace($this->sub_folder, '', $rel_plugin_path);
            }

            $this->partial_replace_old[] = $rel_plugin_path;
            $this->partial_replace_new[] = $new_plugin_path;

            if ($this->opt('replace_javascript_path') > 1) {
                $this->replace_old[] = str_replace('/', '\/', $rel_plugin_path);
                $this->replace_new[] = str_replace('/', '\/', $new_plugin_path);
            }
        }


        if ($this->add_auto_internal('css') && !isset($_POST['wp_customize']) && $this->is_permalink()) {
            $auto_path = '_auto\.css';

            //not multisite
            if ($this->sub_folder)
                $new_non_wp_rules[$auto_path] = add_query_arg('style_internal_wrapper', '1', $this->sub_folder) . str_replace('?', '&', $this->trust_key);
            else
                $new_non_wp_rules[$auto_path] = '/index.php?style_internal_wrapper=1' . str_replace('?', '&', $this->trust_key);

        }

        if ($this->add_auto_internal('js') && !isset($_POST['wp_customize']) && $this->is_permalink()) {
            $auto_path = '_auto\.js';

            //not multisite
            if ($this->sub_folder)
                $new_non_wp_rules[$auto_path] = add_query_arg('script_internal_wrapper', '1', $this->sub_folder) . str_replace('?', '&', $this->trust_key);
            else
                $new_non_wp_rules[$auto_path] = '/index.php?script_internal_wrapper=1' . str_replace('?', '&', $this->trust_key);

        }

        if ($this->opt('new_style_name') && $this->opt('new_theme_path')) {
            $new_style_path = trim($this->opt('new_theme_path'), ' /') . '/' . trim($this->opt('new_style_name'), '/ ');
            $new_style_path = str_replace('.', '\.', $new_style_path);
            $rel_theme_path = $this->sub_folder . trim(str_replace(site_url(), '', get_stylesheet_directory_uri()), '/');
            if ($this->sub_folder)
                $new_non_wp_rules[$new_style_path] = add_query_arg('style_wrapper', '1', $this->sub_folder) . str_replace('?', '&', $this->trust_key);
            else
                $new_non_wp_rules[$new_style_path] = '/index.php?style_wrapper=1' . str_replace('?', '&', $this->trust_key);

            if (trim($this->opt('new_style_name'), ' /') != 'style.css') {
                $old_style = trim($this->opt('new_theme_path'), ' /') . '/' . 'style\.css';
                $new_non_wp_rules[$old_style] = 'nothing_404_404' . $this->trust_key;
            }

            //We do not use _main in child themes on multisites instead we change all style.css to main.css
            //In single we use a different approach and style.css remains and minified for parent themes.
            if (is_multisite()) {
                $this->partial_preg_replace_old[] = '@/' . trim($this->opt('new_theme_path'), ' /') . '/([_0-9a-zA-Z-]+)/style.css@';
                $this->partial_preg_replace_new[] = '/' . trim($this->opt('new_theme_path'), ' /') . '/$1/' . trim($this->opt('new_style_name'), ' /');
            }
        }

        if ($this->opt('auto_config_plugins') && !isset($_POST['wp_customize']) && $this->is_permalink()) {
            $get_path = '_get/([A-Za-z0-9-_\.]+)/(.*)';
//RewriteRule ^_get/([A-Za-z0-9-_\.]+)/(.*) /wp39/index.php?get_wrapper=1&_case=$1&_addr=$2&AK_hide_my_wp=1234 [QSA,L]

            //not multisite
            if ($this->sub_folder)
                $new_non_wp_rules[$get_path] = add_query_arg('get_wrapper', '1', $this->sub_folder) . '&_case=$1&_addr=$2' . str_replace('?', '&', $this->trust_key);
            else
                $new_non_wp_rules[$get_path] = '/index.php?get_wrapper=1&_case=$1&_addr=$2' . str_replace('?', '&', $this->trust_key);

        }


        if ($this->opt('new_theme_path') && $this->is_permalink() && !isset($_POST['wp_customize'])) {
            $rel_theme_path = $this->sub_folder . trim(str_replace(site_url(), '', get_stylesheet_directory_uri()), '/');

            $new_theme_path = trim($this->opt('new_theme_path'), '/ ');
            $new_non_wp_rules[$new_theme_path . '/(.*)'] = $rel_theme_path . '/$1' . $this->trust_key;

            if (is_multisite()) {
                if ($this->is_subdir_mu)
                    $new_theme_path = '/' . $new_theme_path;
                $rel_theme_path_with_theme = trim(str_replace(site_url(), '', get_stylesheet_directory_uri()), '/');
                $rel_theme_path = $this->blog_path . str_replace('/' . get_stylesheet(), '', $rel_theme_path_with_theme); //without theme
            }

            $this->partial_replace_old[] = $rel_theme_path;
            $this->partial_replace_new[] = $new_theme_path;

            if ($this->opt('replace_javascript_path') > 0) {
                $this->replace_old[] = str_replace('/', '\/', $rel_theme_path);
                $this->replace_new[] = str_replace('/', '\/', $new_theme_path);
            }

            if (is_child_theme()) {
                //remove the end folder so we can replace it with parent theme
                $path_array = explode('/', $new_theme_path);
                array_pop($path_array);
                $path_string = implode('/', $path_array);

                if ($path_string)
                    $path_string = $path_string . '/';

                $parent_theme_new_path = $path_string . get_template();
                $rel_parent_theme_path = $this->sub_folder . trim(str_replace(site_url(), '', get_template_directory_uri()), '/');
                $parent_theme_new_path_with_main = $new_theme_path . '_main';

                if ($this->sub_folder)
                    $new_non_wp_rules[$parent_theme_new_path_with_main . '/style\.css'] = add_query_arg('parent_wrapper', '1', $this->sub_folder) . str_replace('?', '&', $this->trust_key);
                else
                    $new_non_wp_rules[$parent_theme_new_path_with_main . '/style\.css'] = '/index.php?parent_wrapper=1' . str_replace('?', '&', $this->trust_key);


                $new_non_wp_rules[$parent_theme_new_path . '/(.*)'] = $rel_parent_theme_path . '/$1' . $this->trust_key;
                $new_non_wp_rules[$parent_theme_new_path_with_main . '/(.*)'] = $rel_parent_theme_path . '/$1' . $this->trust_key;

                if (!is_multisite()) {
                    $this->partial_replace_old[] = $rel_parent_theme_path;
                    $this->partial_replace_new[] = $parent_theme_new_path_with_main;
                }

                if ($this->opt('replace_javascript_path') > 0) {
                    $this->replace_old[] = str_replace('/', '\/', $rel_parent_theme_path);
                    $this->replace_new[] = str_replace('/', '\/', $parent_theme_new_path_with_main);
                }
            }
        }


        if ($this->opt('replace_admin_ajax') && trim($this->opt('replace_admin_ajax'), '/ ') != 'admin-ajax.php' && trim($this->opt('replace_admin_ajax')) != 'wp-admin/admin-ajax.php' && $this->is_permalink()) {
            $rel_admin_ajax = $this->sub_folder . 'wp-admin/admin-ajax.php';
            $new_admin_ajax = trim($this->opt('replace_admin_ajax'), '/ ');

            $admin_ajax = str_replace('.', '\\.', $new_admin_ajax);

            $new_non_wp_rules[$admin_ajax] = $rel_admin_ajax . $this->trust_key;

            if (is_multisite()) {
                $rel_admin_ajax = str_replace($this->sub_folder, '', $rel_admin_ajax);
                $new_admin_ajax = $new_admin_ajax;
            }

            $this->replace_old[] = $rel_admin_ajax;
            $this->replace_new[] = $new_admin_ajax;

            $this->replace_old[] = str_replace('/', '\/', $rel_admin_ajax);
            $this->replace_new[] = str_replace('/', '\/', $new_admin_ajax);
        }

        if (trim($this->opt('new_content_path'), ' /') && trim($this->opt('new_content_path'), '/ ') != 'wp-content' && $this->is_permalink()) {
            $new_content_path = trim($this->opt('new_content_path'), ' /');
            $rel_content_path = $this->sub_folder . trim(str_replace(site_url(), '', HMW_WP_CONTENT_URL), '/');

            $new_non_wp_rules[$new_content_path . '/(.*)'] = $rel_content_path . '/$1' . $this->trust_key;

            $this->replace_old[] = str_replace('/', '\/', $rel_content_path);
            $this->replace_new[] = str_replace('/', '\/', $new_content_path);
        }

        if ($this->opt('replace_comments_post') && trim($this->opt('replace_comments_post'), '/ ') != 'wp-comments-post.php' && $this->is_permalink()) {

            $rel_comments_post = $this->sub_folder . 'wp-comments-post.php';
            $new_comments_post = trim($this->options['replace_comments_post'], '/ ');
            $comments_post = str_replace('.', '\\.', $new_comments_post);

            $new_non_wp_rules[$comments_post] = $rel_comments_post . $this->trust_key;

            if (is_multisite()) {
                $new_comments_post = $new_comments_post;
                $rel_comments_post = str_replace($this->sub_folder, '', $rel_comments_post);
            }

            $this->replace_old[] = $rel_comments_post;
            $this->replace_new[] = $new_comments_post;
        }


        if ($this->opt('antispam')) {
            $this->preg_replace_old[] = "%name(\s*)=(\s*)('|\")author('|\")(?!\sdata-hwm)%";
            $this->preg_replace_new[] = "name='authar'";
        }

        if ($this->opt('hide_other_wp_files') && $this->is_permalink()) {
            $rel_content_path = $this->sub_folder . trim(str_replace(site_url(), '', HMW_WP_CONTENT_URL), '/');
            $rel_plugin_path = $this->sub_folder . trim(str_replace(site_url(), '', HMW_WP_PLUGIN_URL), '/');
            $rel_include_path = $this->sub_folder . trim(WPINC);

            //Fix an annoying strange bug in some webhosts (bright).
            $screenshot = '';
            if (!is_multisite()) {
                $rel_theme_path_with_theme = $this->sub_folder . trim(str_replace(site_url(), '', get_stylesheet_directory_uri()), '/');
                $rel_theme_path = str_replace('/' . get_stylesheet(), '', $rel_theme_path_with_theme);
                $screenshot = $rel_theme_path_with_theme . '/screenshot\.png|';
            }

            $style_path_reg = '';
            //  if (!is_multisite() && $this->opt('new_style_name') && $this->opt('new_style_name') != 'style.css' && !isset($_POST['wp_customize']))
            //      $style_path_reg = '|'.$rel_theme_path_with_theme.'/style\.css';

            //|'.$rel_plugin_path.'/index\.php|'.$rel_theme_path.'/index\.php'
            $new_non_wp_rules[$screenshot . $this->sub_folder . 'readme\.html|' . $this->sub_folder . 'license\.txt|' . $rel_content_path . '/debug\.log' . $style_path_reg . '|' . $rel_include_path . '/$'] = 'nothing_404_404' . $this->trust_key;
        }

        if ($this->opt('disable_directory_listing') && $this->is_permalink()) {
            $rel_content_path = $this->sub_folder . trim(str_replace(site_url(), '', HMW_WP_CONTENT_URL), '/');
            $rel_include_path = $this->sub_folder . trim(WPINC);

            $new_non_wp_rules['(((' . $rel_content_path . '|' . $rel_include_path . ')/([A-Za-z0-9\-\_\/]*))|(wp-admin/(!network\/?)([A-Za-z0-9\-\_\/]+)))(\.txt|/)$'] = 'nothing_404_404' . $this->trust_key;
        }

        if ($this->opt('avoid_direct_access')) {
            $rel_plugin_path = $this->sub_folder . trim(str_replace(site_url(), '', HMW_WP_PLUGIN_URL), '/');
            $rel_theme_path_with_theme = $this->sub_folder . trim(str_replace(site_url(), '', get_stylesheet_directory_uri()), '/');

            $white_list = explode(",", $this->opt('direct_access_except'));
            $white_list[] = 'wp-login.php';
            $white_list[] = 'index.php';
            $white_list[] = 'wp-admin/';

            if (get_option('hmwp_reset_token')) {
                $reset_url = $rel_plugin_path. '/' .dirname(HMW_FILE) . '/d.php';
                $white_list[] = $reset_url;
            }

            if ($this->opt('new_login_path'))
                $white_list[] = $this->opt('new_login_path');

            if ($this->opt('exclude_theme_access'))
                $white_list[] = $rel_theme_path_with_theme . '/';
            if ($this->opt('exclude_plugins_access'))
                $white_list[] = $rel_plugin_path . '/';

            $block = true;
            $white_regex = '';
            foreach ($white_list as $white_file) {
                $white_regex .= $this->sub_folder . str_replace(array('.', ' '), array('\.', ''), $white_file) . '|';                     //make \. remove spaces
            }
            $white_regex = substr($white_regex, 0, strlen($white_regex) - 1); //remove last |
            $white_regex = str_replace(array("\n", "\r\n", "\r"), '', $white_regex);
            //ToDo: Maybe this is a better rule. but harder to implement with WP (Because of RewriteCond):
            //RewriteCond %{REQUEST_URI} !(index\.php|wp-content/repair\.php|wp-includes/js/tinymce/wp-tinymce\.php|wp-comments-post\.php|wp-login\.php|index\.php|wp-admin/)(.*)

            $new_non_wp_rules['(' . $white_regex . ')(.*)'] = '$1$2' . $this->trust_key;
            $new_non_wp_rules[$this->sub_folder . '(.*)\.php(.*)'] = 'nothing_404_404' . $this->trust_key;

        }
        add_filter('mod_rewrite_rules', array(&$this, 'mod_rewrite_rules'), 10, 1);


        if (isset($new_non_wp_rules) && $this->is_permalink())
            $wp_rewrite->non_wp_rules = array_merge($wp_rewrite->non_wp_rules, $new_non_wp_rules);

        return $wp_rewrite;

    }

    /**
     * HideMyWP::mod_rewrite_rules()
     * Fix WP generated rules
     * @param mixed $key
     * @return
     */
    function mod_rewrite_rules($rules)
    {
        $home_root = parse_url(home_url());

        if (isset($home_root['path']))
            $home_root = trailingslashit($home_root['path']);
        else
            $home_root = '/';

        if ($this->opt('avoid_direct_access'))
            $rules = str_replace('(.*) ' . $home_root . '$1$2', '(.*) $1$2', $rules);

        if ($this->opt('full_hide') && $this->opt('admin_key')) {
            $slashed_home = trailingslashit(get_option('home'));
            $base = parse_url($slashed_home, PHP_URL_PATH);

            if (!$this->sub_folder && $base && $base != '/')
                $sub_install = trim($base, ' /') . '/';
            else
                $sub_install = '';

            $trust_key = str_replace('?', '', $this->trust_key); //remove ?

            // $this->sub_folder;
            $new_rules = "RewriteRule ^index\\.php$ - [L]" . PHP_EOL
				. "RewriteCond %{HTTP_COOKIE} !" . $this->access_cookie() . "=1" . PHP_EOL
				. "RewriteCond %{QUERY_STRING} !" . str_replace('?', '', $this->trust_key) . PHP_EOL
				. "RewriteRule ^((wp-content|wp-includes|wp-admin)/(.*)) /" . $sub_install . "nothing_404_404" . $this->trust_key . " [QSA,L]";
			$rules = str_replace('RewriteRule ^index\\.php$ - [L]', $new_rules, $rules);
        }
        //Add online detection rule
        if ($this->opt('hide_online_detectors')) {
            //$rules .= 'RewriteCond %{HTTP_USER_AGENT} WhatCMS [NC]' . "\n" . 'RewriteRule .* - [F,L]' . "\n";
			$detection_sites = array(
				'RewriteCond %{HTTP_REFERER} whatcms\. [NC]',
				'RewriteCond %{HTTP_REFERER} wpthemedetector\. [NC]',
				'RewriteCond %{HTTP_REFERER} wpdetector\. [NC]',
				'RewriteCond %{HTTP_REFERER} builtwith\. [NC]',
				'RewriteCond %{HTTP_REFERER} wappalyzer\. [NC]',
				'RewriteCond %{HTTP_REFERER} isitwp\. [NC]',
				'RewriteCond %{HTTP_USER_AGENT} whatcms [NC]',
				'RewriteRule .* - [F,L]',
			);
			$detection_rules = "<IfModule mod_rewrite.c>" . PHP_EOL;
			$detection_rules .= implode("\n", $detection_sites) . PHP_EOL;
			$detection_rules .= "</IfModule>";
			$rules .= $detection_rules;
		}
        $rules = $this->cleanup_config($rules);

        return $rules;
    }

    function tinymce_emoji($plugins)
    {
        if (is_array($plugins)) {
            return array_diff($plugins, array('wpemoji'));
        } else {
            return array();
        }
    }

    /**
     * HideMyWP::on_activate_callback()
     *
     * @return
     */
    function on_activate_callback()
    {
		self::load_ip_countries_db_table();

		flush_rewrite_rules();
	}


    function add_auto_internal($type = 'css')
    {
        if ($type == 'css')
            return ($this->opt('new_theme_path') && ($this->opt('auto_internal') == 1 || $this->opt('auto_internal') == 3 || $this->opt('internal_css') || $this->auto_config_internal_css));
        elseif ($type == 'js')
            return ($this->opt('new_theme_path') && ($this->opt('auto_internal') >= 2 || $this->opt('internal_js') || $this->auto_config_internal_js || $this->opt('auto_config_plugins')));

        //auto_config_plugins require for woocommerce endpoint in ajax
    }


    function add_get_wrapper()
    {
        return ($this->opt('auto_config_plugins'));
    }

    /**
     * Register deactivation hook
     * HideMyWP::on_deactivate_callback()
     *
     * @return
     */
    function on_deactivate_callback()
    {
        if($this->opt('uninstall_hmwp_data')){
            delete_optioon_deactivate_callbackn(self::slug);
            delete_option('hmwp_temp_admin_path');
            delete_option('trust_network_rules');
            delete_option('hmwp_internal_assets');
        }
        flush_rewrite_rules();
    }

	public static function exception_fields() {
		$except = array(
			"REQUEST.comment", "POST.comment",
			"REQUEST.permalink_structure", "POST.permalink_structure",
			"REQUEST.selection", "POST.selection",
			"REQUEST.content", "POST.content",
			"REQUEST.__utmz", "COOKIE.__utmz",
			"REQUEST.s_pers", "COOKIE.s_pers",
			"REQUEST.user_pass", "POST.user_pass",
			"REQUEST.pass1", "POST.pass1",
			"REQUEST.pass2", "POST.pass2",
			"REQUEST.password", "POST.password",
			"POST.hide_my_wp.%", "REQUEST.hide_my_wp.%",
			"POST.%import%", "REQUEST.%import%",
			"REQUEST.newcontent", "POST.newcontent", "REQUEST.remember_%"
		);
		$except = implode('[new_line]', $except);
		return $except;
	}

	public static function pre_made_settings() {
		if (is_multisite()) {
			$options = get_blog_option(BLOG_ID_CURRENT_SITE, self::slug);
		} else {
			$options = get_option(self::slug);
		}
		$li = (isset($options['li']) ? $options['li'] : '');
		$admin_email = get_option('admin_email');
		$ids_except = self::exception_fields();
		$permalink = get_option('permalink_structure');
		$category_base = get_option('category_base');
		$tag_base = get_option('tag_base');
		$settings = array(
			'low_privacy' => '{"replace_javascript_path":"0","disable_directory_listing":"on","exclude_plugins_access":"","exclude_theme_access":"","replace_wpnonce":"","replace_mode":"safe","custom_404":"0","custom_404_page":"","login_query":"' . self::slug . '","admin_key":"1234","remove_feed_meta":"on","hide_admin_bar":"","remove_other_meta":"on","clean_post_class":"","remove_menu_class":"","remove_default_description":"on","remove_ver_scripts":"","direct_access_except":"index.php, wp-content/repair.php, wp-comments-post.php, wp-includes/js/tinymce/wp-tinymce.php, xmlrpc.php, wp-cron.php","disable_canonical_redirect":"","email_from_name":"' . get_bloginfo('blogname') . '","email_from_address":"' . $admin_email . '","replace_in_html":"","new_theme_path":"/static","new_style_name":"","style_expiry_days":"","new_include_path":"/static/lib","new_plugin_path":"/static/ext","new_upload_path":"/file","replace_comments_post":"","replace_admin_ajax":"ajax","new_content_path":"","author_enable":"1","author_base":"","author_query":"","feed_enable":"1","feed_base":"","feed_query":"","post_enable":"1","post_base":"' . $permalink . '","post_query":"","page_enable":"1","page_base":"","page_query":"","paginate_enable":"1","paginate_base":"","paginate_query":"","category_enable":"1","category_base":"' . $category_base . '","category_query":"","tag_enable":"1","tag_base":"' . $tag_base . '","tag_query":"","search_enable":"1","search_base":"","search_query":"","nice_search_redirect":"","import_options":"","export_options":"","debug_report":"","minify_new_style":"","clean_new_style":"","rename_plugins":"","separator2":"","author_without_base":"","disable_archive":"","disable_other_wp":"","trusted_user_roles":"","hide_wp_login":"on","hide_wp_admin":"on","spy_notifier":"","separator":"","remove_body_class":"","remove_html_comments":"","antispam":"on","avoid_direct_access":"","hide_other_wp_files":"on","enable_ids":"","ids_mode":"0","ids_level":"0","ids_admin_include":"","ids_cookie":"","log_ids_min":"10","block_ids_min":"30","full_hide":"","cdn_path":"","li":"' . $li . '","customized_htaccess":"","email_ids_min":"30","ids_html_fields":"","replace_in_ajax":"","blocked_ip_message":"You are blocked. Please contact site administrator if you think there is a problem.","blocked_countries":"","blocked_ips":"","new_login_path":"","help_trust_network":"","trust_network":"on","exception_fields":"' . $ids_except . '"}',
			'medium_privacy' => '{"replace_javascript_path":"3","disable_directory_listing":"on","exclude_plugins_access":"","exclude_theme_access":"","replace_wpnonce":"","replace_mode":"safe","custom_404":"0","custom_404_page":"","login_query":"' . self::slug . '","admin_key":"1234","remove_feed_meta":"on","hide_admin_bar":"","remove_other_meta":"on","clean_post_class":"","remove_menu_class":"","remove_default_description":"on","remove_ver_scripts":"on","direct_access_except":"index.php, wp-comments-post.php, wp-includes/js/tinymce/wp-tinymce.php, xmlrpc.php, wp-cron.php","disable_canonical_redirect":"","email_from_name":"' . get_bloginfo('blogname') . '","email_from_address":"' . $admin_email . '","replace_in_html":"","new_theme_path":"/skin","new_style_name":"main.css","style_expiry_days":"3","new_include_path":"/other","new_plugin_path":"/ext","new_upload_path":"/file","replace_comments_post":"","replace_admin_ajax":"ajax","new_content_path":"inc","author_enable":"1","author_base":"","author_query":"","feed_enable":"1","feed_base":"","feed_query":"","post_enable":"1","post_base":"' . $permalink . '","post_query":"","page_enable":"1","page_base":"","page_query":"","paginate_enable":"1","paginate_base":"","paginate_query":"","category_enable":"1","category_base":"' . $category_base . '","category_query":"","tag_enable":"1","tag_base":"' . $tag_base . '","tag_query":"","search_enable":"1","search_base":"","search_query":"","nice_search_redirect":"","import_options":"","export_options":"","debug_report":"","minify_new_style":"safe","clean_new_style":"","rename_plugins":"","separator2":"","author_without_base":"","disable_archive":"","disable_other_wp":"","trusted_user_roles":"","hide_wp_login":"on","hide_wp_admin":"on","spy_notifier":"","separator":"","remove_body_class":"","remove_html_comments":"","antispam":"on","avoid_direct_access":"","hide_other_wp_files":"on","enable_ids":"on","ids_mode":"0","ids_level":"0","ids_admin_include":"","ids_cookie":"","log_ids_min":"5","block_ids_min":"20","full_hide":"","cdn_path":"","li":"' . $li . '","customized_htaccess":"","email_ids_min":"30","ids_html_fields":"","replace_in_ajax":"","blocked_ip_message":"You are blocked. Please contact site administrator if you think there is a problem.","blocked_countries":"","blocked_ips":"","help_trust_network":"on","trust_network":"on","api_base":"","api_query":"","new_login_path":"","exception_fields":"' . $ids_except . '"}',
			'high_privacy' => '{"replace_javascript_path":"3","disable_directory_listing":"on","exclude_plugins_access":"","exclude_theme_access":"","replace_wpnonce":"","replace_mode":"safe","custom_404":"0","custom_404_page":"","hide_wp_login":"on","login_query":"' . self::slug . '","admin_key":"1234","hide_wp_admin":"on","remove_feed_meta":"on","hide_admin_bar":"on","remove_other_meta":"on","remove_body_class":"on","clean_post_class":"on","remove_menu_class":"on","remove_default_description":"on","remove_html_comments":"simple","remove_ver_scripts":"on","avoid_direct_access":"","direct_access_except":"index.php, wp-comments-post.php, wp-includes/js/tinymce/wp-tinymce.php, xmlrpc.php, wp-cron.php","hide_other_wp_files":"on","disable_canonical_redirect":"on","email_from_name":"' . get_bloginfo('blogname') . '","email_from_address":"' . $admin_email . '","replace_in_html":"","new_theme_path":"/template","new_style_name":"main.css","style_expiry_days":"3","minify_new_style":"quick","clean_new_style":"on","new_include_path":"/template/lib","new_plugin_path":"/template/ext","rename_plugins":"all","new_upload_path":"/storage","replace_comments_post":"submit_comment.php","replace_admin_ajax":"ajax","new_content_path":"inc","author_enable":"1","author_base":"profile","author_query":"profile","author_without_base":"","feed_enable":"1","feed_base":"rss.xml","feed_query":"rss","post_enable":"1","post_base":"' . $permalink . '","post_query":"entry","page_enable":"1","page_base":"/page","page_query":"page_num","paginate_enable":"1","paginate_base":"list","paginate_query":"list","category_enable":"1","category_base":"' . $category_base . '","category_query":"category","tag_enable":"1","tag_base":"' . $tag_base . '","tag_query":"keyword","search_enable":"1","search_base":"find","search_query":"find","nice_search_redirect":"on","disable_archive":"","disable_other_wp":"","import_options":"","export_options":"","debug_report":"","separator2":"","trusted_user_roles":"","antispam":"on","spy_notifier":"","separator":"","enable_ids":"on","ids_mode":"0","ids_level":"1","ids_admin_include":"","ids_cookie":"","log_ids_min":"1","block_ids_min":"20","full_hide":"on","cdn_path":"","li":"' . $li . '","customized_htaccess":"","email_ids_min":"20","ids_html_fields":"","replace_in_ajax":"","blocked_ip_message":"You are blocked. Please contact site administrator if you think there is a problem.","blocked_countries":"","blocked_ips":"","help_trust_network":"on","trust_network":"on","api_base":"api","api_query":"api","new_login_path":"", "hide_whatcms_detection" : "on", "api_disable": "on", "auto_config_plugins":"on","exception_fields":"' . $ids_except . '"}',
		);
		$settings = apply_filters('hmwp_pre_made_settings', $settings);
		return $settings;
	}

    /**
     * HideMyWP::opt()
     * Get options value
     * @param mixed $key
     * @return
     */
    function opt($key)
    {
        if (isset($this->options[$key]))
            return $this->options[$key];
        return false;
    }

    function set_opt($key, $value)
    {
        if (is_multisite()) {
            $opts = get_blog_option(BLOG_ID_CURRENT_SITE, self::slug);
            $opts[$key] = $value;
            update_blog_option(BLOG_ID_CURRENT_SITE, self::slug, $opts);
        } else {
            $opts = get_option(self::slug);
            $opts[$key] = $value;
            update_option(self::slug, $opts);
        }
    }

    function update_attr($query)
    {
        $query['li'] = $this->opt('li');
        return $query;
    }

    function undo_config() {
		$html = '<a href="' . add_query_arg(array('undo_config' => true)) . '" class="button">' . __('Undo Previous Settings', self::slug) . '</a>';
		$html .= sprintf('<br><span class="description"> %s</span>', "Click above to restore previous saved settings!");
		if (isset($_GET['undo_config']) && $_GET['undo_config'] && !isset($_GET['undo'])) {
			$previous = get_option(self::slug . '_undo');
			if (!empty($previous)) {
				if (!$previous['new_admin_path']) {
					$previous['new_admin_path'] = 'wp-admin';
				}
				update_option('hmwp_temp_admin_path', $previous['new_admin_path']);
				$previous['new_admin_path'] = trim($this->opt('new_admin_path'), ' /');
				update_option(self::slug, $previous);
			}
			wp_redirect(add_query_arg(array('undo_config' => true, 'undo' => 'done')));
		}
		return $html;
	}

    function cleanup_config($config)
    {
        $config = str_replace('//', '/', $config);
        if (defined('WP_SITEURL'))
            $config = str_replace(WP_SITEURL, '', $config);

        if (defined('WP_HOME'))
            $config = str_replace(WP_HOME, '', $config);

        return str_replace(array(site_url(), home_url()), '', $config);
    }

    function nginx_config()
    {
        $new_theme_path = trim($this->opt('new_theme_path'), '/ ');
        $new_plugin_path = trim($this->opt('new_plugin_path'), '/ ');
        $new_upload_path = trim($this->opt('new_upload_path'), '/ ');
        $new_include_path = trim($this->opt('new_include_path'), '/ ');
        $new_style_name = trim($this->opt('new_style_name'), '/ ');
        $new_content_path = trim($this->opt('new_content_path'), '/ ');

        if (trim(get_option('hmwp_temp_admin_path'), ' /'))
            $new_admin_path = trim(get_option('hmwp_temp_admin_path'), ' /');
        else
            $new_admin_path = trim($this->opt('new_admin_path'), '/ ');

        $rel_login_path = $this->sub_folder . '/wp-login.php';
        $new_login_path = str_replace('.', '\.', trim($this->opt('new_login_path'), '/ '));

        if (is_multisite()) {
            if ($this->is_subdir_mu)
                $new_login_path = '/' . $new_login_path;
            $rel_login_path = $this->blog_path . str_replace($this->sub_folder, '', $new_login_path);
        }

        $replace_admin_ajax = trim($this->opt('replace_admin_ajax'), '/ ');
        $replace_admin_ajax_rule = str_replace('.', '\\.', $replace_admin_ajax);
        $replace_comments_post = trim($this->opt('replace_comments_post'), '/ ');
        $replace_comments_post_rule = str_replace('.', '\\.', $replace_comments_post);

        $upload_path = wp_upload_dir();

        //not required for nginx
        $sub_install = '';

        if (is_ssl())
            $upload_path['baseurl'] = str_replace('http:', 'https:', $upload_path['baseurl']);

        $rel_upload_path = $this->sub_folder . trim(str_replace(site_url(), '', $upload_path['baseurl']), '/');
        $rel_include_path = $this->sub_folder . trim(WPINC);
        $rel_plugin_path = $this->sub_folder . trim(str_replace(site_url(), '', HMW_WP_PLUGIN_URL), '/');
        $rel_theme_path = $this->sub_folder . trim(str_replace(site_url(), '', get_stylesheet_directory_uri()), '/');
        $rel_comments_post = $this->sub_folder . 'wp-comments-post.php';
        $rel_admin_ajax = $this->sub_folder . 'wp-admin/admin-ajax.php';


        $rel_content_path = $this->sub_folder . trim(str_replace(site_url(), '', HMW_WP_CONTENT_URL), '/');
        $rel_theme_path_no_template = str_replace('/' . get_stylesheet(), '', $rel_theme_path);

        $style_path_reg = '';
        //if ($this->opt('new_style_name') && $this->opt('new_style_name') != 'style.css' && !isset($_POST['wp_customize']))
        //    $style_path_reg = '|'.$rel_theme_path.'/style\.css';

        //|'.$rel_plugin_path.'/index\.php|'.$rel_theme_path_no_template.'/index\.php'


        $hide_other_file_rule = $this->sub_folder . 'readme\.html|' . $this->sub_folder . 'license\.txt|' . $rel_content_path . '/debug\.log' . $style_path_reg . '|' . $rel_include_path . '/$';

        $disable_directoy_listing = '(((' . $rel_content_path . '|' . $rel_include_path . ')/([A-Za-z0-9\-\_\/]*))|(wp-admin/(!network\/?)([A-Za-z0-9\-\_\/]+)))(\.txt|/)$';

        if ($this->opt('login_query') && $this->opt('login_query'))
            $login_query = $this->opt('login_query');
        else
            $login_query = 'hide_my_wp';

        if ($this->opt('antispam') && $this->opt('admin_key'))
            $antispam = '?' . $login_query . '=' . $this->opt('admin_key');
        else
            $antispam = '';

        if ($this->opt('avoid_direct_access')) {

            $rel_theme_path_with_theme = $this->sub_folder . trim(str_replace(site_url(), '', get_stylesheet_directory_uri()), '/');

            $white_list = explode(",", $this->opt('direct_access_except'));
            $white_list[] = 'wp-login.php';
            $white_list[] = 'index.php';
            $white_list[] = 'wp-admin/';

            if (get_option('hmwp_reset_token')) {
                $reset_url = $rel_plugin_path. '/' .dirname(HMW_FILE) . '/d.php';
                $white_list[] = $reset_url;
            }

            if ($this->opt('new_login_path'))
                $white_list[] = $this->opt('new_login_path');

            if ($this->opt('exclude_theme_access'))
                $white_list[] = $rel_theme_path_with_theme . '/';
            if ($this->opt('exclude_plugins_access'))
                $white_list[] = $rel_plugin_path . '/';

            $block = true;
            $white_regex = '';
            foreach ($white_list as $white_file) {
                $white_regex .= $this->sub_folder . str_replace(array('.', ' '), array('\.', ''), $white_file) . '|';  //make \. remove spaces
            }
            $white_regex = substr($white_regex, 0, strlen($white_regex) - 1); //remove last |
            $white_regex = str_replace(array("\r", "\r\n", "\n"), '', $white_regex);
        }


        $output = '';

        if ($this->opt('full_hide')) {
            //ignored: condition 0
            //todo: wp-content|includes
            $full_hide = '
if ($args !~ "' . str_replace('?', '', $this->trust_key) . '"){
	set $rule_0 1;
}
if ($http_cookie !~* "' . $this->access_cookie() . '=1"){
	set $rule_0 "${rule_0}2";
}
if ($rule_0 = "12"){
	rewrite ^/((wp-content|wp-includes|wp-admin)/(.*)) /nothing_404_404' . $this->trust_key . ' last;
}
';

            $output = $full_hide . $output;
        }

        if ($this->opt('replace_urls') || $this->auto_replace_urls) {
            $replace_urls = $this->h->replace_newline(trim($this->opt('replace_urls'), ' '), '|%|');
            $replace_lines = explode('|%|', $replace_urls);
            $replace_lines = array_merge($replace_lines, $this->auto_replace_urls);
            if ($replace_lines) {
                foreach ($replace_lines as $line) {

                    $replace_word = explode('==', $line);
                    if (isset($replace_word[0]) && isset($replace_word[1])) {

                        //Check whether last character is / or not to recgnize folders
                        $is_folder = false;
                        if (substr($replace_word[0], strlen($replace_word[0]) - 1, strlen($replace_word[0])) == '/')
                            $is_folder = true;

                        $replace_word[0] = trim($replace_word[0], '/ ');
                        $replace_word[1] = trim($replace_word[1], '/ ');

                        $is_block = false;
                        if ($replace_word[1] == 'nothing_404_404')
                            $is_block = true;


                        if ($is_block) {
                            //Swap words to make theme unavailable
                            $temp = $replace_word[0];
                            $replace_word[0] = $replace_word[1];
                            $replace_word[1] = $temp;
                        }

                        $replace_word[0] = str_replace(array('amp;', '%2F', '//', '.'), array('', '/', '/', '.'), $replace_word[0]);
                        $replace_word[1] = str_replace(array('.', 'amp;'), array('\.', ''), $replace_word[1]);

                        if ($is_folder) {
                            $output .= 'rewrite ^/' . $replace_word[1] . '/(.*) /' . $sub_install . $replace_word[0] . '/$1' . $this->trust_key . ' last;' . "\n";
                        } else {
                            $output .= 'rewrite ^/' . $replace_word[1] . ' /' . $sub_install . $replace_word[0] . $this->trust_key . ' last;' . "\n";
                        }
                    }
                }
            }
        }


        if (is_multisite()) {
            $sitewide_plugins = array_keys((array)get_site_option('active_sitewide_plugins', array()));
            $active_plugins = array_merge((array)get_blog_option(BLOG_ID_CURRENT_SITE, 'active_plugins'), $sitewide_plugins);
        } else {
            $active_plugins = get_option('active_plugins');
        }

        if ($this->opt('rename_plugins') == 'all')
            $active_plugins = get_option('hmw_all_plugins');

        $pre_plugin_path = '';
        if ($this->opt('rename_plugins') && $new_plugin_path) {
            foreach ((array)$active_plugins as $active_plugin) {

                //Ignore itself or a plugin without folder
                if (!$this->h->str_contains($active_plugin, '/') || $active_plugin == self::main_file || strpos($active_plugin, 'elementor') !== FALSE)
                    continue;

                $new_plugin_path = trim($new_plugin_path, '/ ');

                $codename_this_plugin = $this->hash($active_plugin);

                $rel_this_plugin_path = trim(str_replace(site_url(), '', plugin_dir_url($active_plugin)), '/');
                //Allows space in plugin folder name
                $rel_this_plugin_path = $this->sub_folder . str_replace(' ', '\ ', $rel_this_plugin_path);

                $new_this_plugin_path = $new_plugin_path . '/' . $codename_this_plugin;
                $pre_plugin_path .= 'rewrite ^/' . $new_this_plugin_path . '/(.*) /' . $rel_this_plugin_path . '/$1' . $this->trust_key . ' last;' . "\n";
            }
        }


        if (is_child_theme()) {
            //remove the end folder of so we can replace it with parent theme
            $path_array = explode('/', $new_theme_path);
            array_pop($path_array);
            $path_string = implode('/', $path_array);

            if ($path_string)
                $path_string = $path_string . '/';

            $parent_theme_new_path = $path_string . get_template();
            $rel_parent_theme_path = $this->sub_folder . trim(str_replace(site_url(), '', get_template_directory_uri()), '/');
            $output .= 'rewrite ^/' . $parent_theme_new_path . '/(.*) /' . $rel_parent_theme_path . '/$1' . $this->trust_key . ' last;' . "\n";
            $parent_theme_new_path_with_main = $new_theme_path . '_main';

            $output .= 'rewrite ^/' . $parent_theme_new_path_with_main . '/style\.css' . ' /?parent_wrapper=1' . str_replace('?', '&', $this->trust_key) . ' last;' . "\n";

            $output .= 'rewrite ^/' . $parent_theme_new_path_with_main . '/(.*) /' . $rel_parent_theme_path . '/$1' . $this->trust_key . ' last;' . "\n";
        }


        if ($new_admin_path && $new_admin_path != 'wp-admin')
            $output .= 'rewrite ^/' . $new_admin_path . '/(.*) /' . $this->sub_folder . 'wp-admin/$1' . $this->trust_key . ' last;' . "\n";

        if ($new_login_path && $new_login_path != 'wp-login.php')
            $output .= 'rewrite ^/' . $new_login_path . ' /' . $this->sub_folder . $rel_login_path . $this->trust_key . ' last;' . "\n";

        if ($new_include_path)
            $output .= 'rewrite ^/' . $new_include_path . '/(.*) /' . $rel_include_path . '/$1' . $this->trust_key . ' last;' . "\n";

        if ($new_upload_path)
            $output .= 'rewrite ^/' . $new_upload_path . '/(.*) /' . $rel_upload_path . '/$1' . $this->trust_key . ' last;' . "\n";

        if ($new_plugin_path && $pre_plugin_path)
            $output .= $pre_plugin_path;

        if ($new_plugin_path)
            $output .= 'rewrite ^/' . $new_plugin_path . '/(.*) /' . $rel_plugin_path . '/$1' . $this->trust_key . ' last;' . "\n";

        if ($new_style_name)
            $output .= 'rewrite ^/' . $new_theme_path . '/' . str_replace('.', '\.', $new_style_name) . ' /?style_wrapper=1' . str_replace('?', '&', $this->trust_key) . ' last;' . "\n";

        if ($this->add_auto_internal('css'))
            $output .= 'rewrite ^/_auto\.css' . ' /?style_internal_wrapper=1' . str_replace('?', '&', $this->trust_key) . ' last;' . "\n";

        if ($this->add_get_wrapper())
            $output .= 'rewrite ^/_get/([A-Za-z0-9-_\.]+)/(.*)' . ' /?get_wrapper=1&_case=$1&_addr=$2' . str_replace('?', '&', $this->trust_key) . ' last;' . "\n";
        //RewriteRule ^_get/([A-Za-z0-9-_\.]+)/(.*) /wp39/index.php?get_wrapper=1&_case=$1&_addr=$2&AK_hide_my_wp=1234 [QSA,L]


        if (trim($this->opt('new_style_name'), ' /') && trim($this->opt('new_style_name'), ' /') != 'style.css') {
            $old_style = $new_theme_path . '/' . 'style\.css';
            $output .= 'rewrite ^/' . $old_style . ' /nothing_404_404' . $this->trust_key . ' last;' . "\n";
        }

        if ($new_theme_path)
            $output .= 'rewrite ^/' . $new_theme_path . '/(.*) /' . $rel_theme_path . '/$1' . $this->trust_key . ' last;' . "\n";

        if ($replace_comments_post && $replace_comments_post != 'wp-comments-post.php')
            $output .= 'rewrite ^/' . $replace_comments_post_rule . ' /' . $rel_comments_post . $this->trust_key . ' last;' . "\n";

        if ($replace_admin_ajax_rule && $replace_admin_ajax_rule != 'wp-admin/admin-ajax.php')
            $output .= 'rewrite ^/' . $replace_admin_ajax_rule . ' /' . $rel_admin_ajax . $this->trust_key . ' last;' . "\n";

        if ($new_content_path)
            $output .= 'rewrite ^/' . $new_content_path . '/(.*) /' . $rel_content_path . '/$1' . $this->trust_key . ' last;' . "\n";


        if ($this->opt('hide_other_wp_files'))
            $output .= 'rewrite ^/(' . $hide_other_file_rule . ') /nothing_404_404' . $this->trust_key . ' last;' . "\n";

        if ($this->opt('disable_directory_listing'))
            $output .= 'rewrite ^/' . $disable_directoy_listing . ' /nothing_404_404' . $this->trust_key . ' last;' . "\n";

        if ($this->opt('avoid_direct_access')) {

            $output .= "\n" . '#If you have a block with "location ~ \.php$"  add following two lines to the top of that block otherwise leave it unchanged' . "\n";
            $output .= 'rewrite ^/(' . $white_regex . ')(.*)' . ' /$1$2' . $this->trust_key . ' break;' . "\n";
            $output .= 'rewrite ^/(.*)\.php(.*)' . ' /nothing_404_404' . $this->trust_key . ' last;' . "\n\n";
        }


        if ($output)
            //$output='if (!-e $request_filename) {'. "\n" .  $output . "     break;\n}";
            $output = "# BEGIN Hide My WP\n\n" . $output . "\n# END Hide My WP";
        else
            $output = __('Nothing to add for current settings.', self::slug);

        $output = $this->cleanup_config($output);


        $html = '';
        $desc = __('Add to Nginx config file to get all features of the plugin. <br>', self::slug);

        if (isset($_GET['nginx_config']) && $_GET['nginx_config']) {

            $html = sprintf('%s ', $desc);
            $html .= sprintf('<span class="description">
        <ol style="color:#ff9900">
        <li>Nginx vhosts config file usually located in /etc/nginx/sites-available/YOURSITE or /etc/nginx/sites-available/default or /etc/nginx/nginx.conf  </li>
        <li>Add HMWP rules within server{} block. You can add the rules just before the closing braces }.  </li>
        <li>Restart Nginx to see changes</li>
        <li>You may need to re-configure the server whenever you change settings or activate a new theme or plugin.</li>
        <li>If you use sub-directory for WP block you have to add that directory before all of below pathes (e.g. rewrite ^/wordpress/lib/(.*) /wordpress/wp-includes/$1 or rewrite ^/wordpress/(.*)\.php(.*) /wordpress/nothing_404_404)</li></ol></span><textarea readonly="readonly" onclick="" rows="5" cols="55" class="regular-text %1$s" id="%2$s" name="%2$s" style="%4$s">%3$s</textarea>', 'nginx_config_class', 'nginx_config', esc_textarea($output), 'width:95% !important;height:400px !important');


        } else {
            $html = '<a target="_blank" href="' . add_query_arg(array('die_message' => 'nginx')) . '" class="button">' . __('Nginx Configuration', self::slug) . '</a>';
            $html .= sprintf('<br><span class="description"> %s</span>', $desc);
        }


        return $html;
        //rewrite ^/assets/css/(.*)$ /wp-content/themes/roots/assets/css/$1 last;


    }

    function iis_config()
    {
        $new_theme_path = trim($this->opt('new_theme_path'), '/ ');
        $new_plugin_path = trim($this->opt('new_plugin_path'), '/ ');
        $new_upload_path = trim($this->opt('new_upload_path'), '/ ');
        $new_include_path = trim($this->opt('new_include_path'), '/ ');
        $new_style_name = trim($this->opt('new_style_name'), '/ ');
        $new_content_path = trim($this->opt('new_content_path'), '/ ');

        if (trim(get_option('hmwp_temp_admin_path'), ' /'))
            $new_admin_path = trim(get_option('hmwp_temp_admin_path'), ' /');
        else
            $new_admin_path = trim($this->opt('new_admin_path'), '/ ');

        $rel_login_path = $this->sub_folder . '/wp-login.php';
        $new_login_path = str_replace('.', '\.', trim($this->opt('new_login_path'), '/ '));

        if (is_multisite()) {
            if ($this->is_subdir_mu)
                $new_login_path = '/' . $new_login_path;
            $rel_login_path = $this->blog_path . str_replace($this->sub_folder, '', $new_login_path);
        }

        $replace_admin_ajax = trim($this->opt('replace_admin_ajax'), '/ ');
        $replace_admin_ajax_rule = str_replace('.', '\\.', $replace_admin_ajax);
        $replace_comments_post = trim($this->opt('replace_comments_post'), '/ ');
        $replace_comments_post_rule = str_replace('.', '\\.', $replace_comments_post);

        $upload_path = wp_upload_dir();

        //not required for nginx
        $sub_install = '';

        $page_query = ($this->opt('page_query')) ? $this->opt('page_query') : 'page_id';

        $iis_not_found = 'index.php?' . $page_query . '=999999999';

        if (is_ssl())
            $upload_path['baseurl'] = str_replace('http:', 'https:', $upload_path['baseurl']);

        $rel_upload_path = $this->sub_folder . trim(str_replace(site_url(), '', $upload_path['baseurl']), '/');
        $rel_include_path = $this->sub_folder . trim(WPINC);
        $rel_plugin_path = $this->sub_folder . trim(str_replace(site_url(), '', HMW_WP_PLUGIN_URL), '/');
        $rel_theme_path = $this->sub_folder . trim(str_replace(site_url(), '', get_stylesheet_directory_uri()), '/');
        $rel_comments_post = $this->sub_folder . 'wp-comments-post.php';
        $rel_admin_ajax = $this->sub_folder . 'wp-admin/admin-ajax.php';


        $rel_content_path = $this->sub_folder . trim(str_replace(site_url(), '', HMW_WP_CONTENT_URL), '/');
        $rel_theme_path_no_template = str_replace('/' . get_stylesheet(), '', $rel_theme_path);

        $style_path_reg = '';
        //if ($this->opt('new_style_name') && $this->opt('new_style_name') != 'style.css' && !isset($_POST['wp_customize']))
        //    $style_path_reg = '|'.$rel_theme_path.'/style\.css';

        //|'.$rel_plugin_path.'/index\.php|'.$rel_theme_path_no_template.'/index\.php'


        $hide_other_file_rule = $this->sub_folder . 'readme\.html|' . $this->sub_folder . 'license\.txt|' . $rel_content_path . '/debug\.log' . $style_path_reg . '|' . $rel_include_path . '/$';

        //Customized for iis! removed 2\ and replaced ? and removed /
        $disable_directoy_listing = '(((' . $rel_content_path . '|' . $rel_include_path . ')([A-Za-z0-9-_\/]*))|(wp-admin/(?!network\/)([A-Za-z0-9-_\/]+)))(\.txt|/)$';

        if ($this->opt('login_query') && $this->opt('login_query'))
            $login_query = $this->opt('login_query');
        else
            $login_query = 'hide_my_wp';

        if ($this->opt('antispam') && $this->opt('admin_key'))
            $antispam = '?' . $login_query . '=' . $this->opt('admin_key');
        else
            $antispam = '';

        if ($this->opt('avoid_direct_access')) {

            $rel_theme_path_with_theme = $this->sub_folder . trim(str_replace(site_url(), '', get_stylesheet_directory_uri()), '/');

            $white_list = explode(",", $this->opt('direct_access_except'));
            $white_list[] = 'wp-login.php';
            $white_list[] = 'index.php';
            $white_list[] = 'wp-admin/';

            if (get_option('hmwp_reset_token')) {
                $reset_url = $rel_plugin_path. '/' .dirname(HMW_FILE) . '/d.php';
                $white_list[] = $reset_url;
            }

            if ($this->opt('new_login_path'))
                $white_list[] = $this->opt('new_login_path');

            if ($this->opt('exclude_theme_access'))
                $white_list[] = $rel_theme_path_with_theme . '/';
            if ($this->opt('exclude_plugins_access'))
                $white_list[] = $rel_plugin_path . '/';

            $block = true;
            $white_regex = '';
            foreach ($white_list as $white_file) {
                $white_regex .= $this->sub_folder . str_replace(array('.', ' '), array('\.', ''), $white_file) . '|';  //make \. remove spaces
            }
            $white_regex = substr($white_regex, 0, strlen($white_regex) - 1); //remove last |
            $white_regex = str_replace(array("\r", "\r\n", "\n"), '', $white_regex);
        }


        $output = '';

        if ($this->opt('replace_urls') || $this->auto_replace_urls) {
            $replace_urls = $this->h->replace_newline(trim($this->opt('replace_urls'), ' '), '|%|');
            $replace_lines = explode('|%|', $replace_urls);
            $replace_lines = array_merge($replace_lines, $this->auto_replace_urls);

            if ($replace_lines) {
                foreach ($replace_lines as $line) {

                    $replace_word = explode('==', $line);
                    if (isset($replace_word[0]) && isset($replace_word[1])) {

                        //Check whether last character is / or not to recgnize folders
                        $is_folder = false;
                        if (substr($replace_word[0], strlen($replace_word[0]) - 1, strlen($replace_word[0])) == '/')
                            $is_folder = true;

                        $replace_word[0] = trim($replace_word[0], '/ ');
                        $replace_word[1] = trim($replace_word[1], '/ ');

                        $is_block = false;
                        if ($replace_word[1] == 'nothing_404_404')
                            $is_block = true;


                        if ($is_block) {
                            //Swap words to make theme unavailable
                            $temp = $replace_word[0];
                            $replace_word[0] = $replace_word[1];
                            $replace_word[1] = $temp;
                        }

                        $replace_word[0] = str_replace(array('amp;', '%2F', '//', '.'), array('', '/', '/', '.'), $replace_word[0]);
                        $replace_word[1] = str_replace(array('.', 'amp;'), array('\.', ''), $replace_word[1]);

                        if ($is_folder) {
                            $output .= '<rule name="HMWP Replace' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $replace_word[1] . '/(.*)"  />' . "\n\t" . '<action type="Rewrite" url="' . $sub_install . $replace_word[0] . $this->trust_key . '/{R:1}"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";

                        } else {
                            $output .= '<rule name="rule HMWP_Replace' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $replace_word[1] . '"  />' . "\n\t" . '<action type="Rewrite" url="' . $sub_install . $replace_word[0] . $this->trust_key . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";
                        }
                    }
                }
            }
        }


        if (is_multisite()) {
            $sitewide_plugins = array_keys((array)get_site_option('active_sitewide_plugins', array()));
            $active_plugins = array_merge((array)get_blog_option(BLOG_ID_CURRENT_SITE, 'active_plugins'), $sitewide_plugins);
        } else {
            $active_plugins = get_option('active_plugins');
        }

        if ($this->opt('rename_plugins') == 'all')
            $active_plugins = get_option('hmw_all_plugins');

        $pre_plugin_path = '';
        if ($this->opt('rename_plugins') && $new_plugin_path) {
            foreach ((array)$active_plugins as $active_plugin) {

                //Ignore itself or a plugin without folder
                if (!$this->h->str_contains($active_plugin, '/') || $active_plugin == self::main_file || strpos($active_plugin, 'elementor') !== FALSE)
                    continue;

                $new_plugin_path = trim($new_plugin_path, '/ ');

                //$codename_this_plugin=  hash('crc32', $this->encrypt($active_plugin, substr(NONCE_SALT, 4, 12))  );
                $codename_this_plugin = $this->hash($active_plugin);

                $rel_this_plugin_path = trim(str_replace(site_url(), '', plugin_dir_url($active_plugin)), '/');
                //Allows space in plugin folder name
                $rel_this_plugin_path = $this->sub_folder . str_replace(' ', '\ ', $rel_this_plugin_path);

                $new_this_plugin_path = $new_plugin_path . '/' . $codename_this_plugin;
                $pre_plugin_path .= '<rule name="HMWP Plugin' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $new_this_plugin_path . '/(.*)"  />' . "\n\t" . '<action type="Rewrite" url="' . $rel_this_plugin_path . '/{R:1}' . $this->trust_key . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";
            }
        }


        if (is_child_theme()) {
            //remove the end folder of so we can replace it with parent theme
            $path_array = explode('/', $new_theme_path);
            array_pop($path_array);
            $path_string = implode('/', $path_array);

            if ($path_string)
                $path_string = $path_string . '/';

            $parent_theme_new_path = $path_string . get_template();
            $rel_parent_theme_path = $this->sub_folder . trim(str_replace(site_url(), '', get_template_directory_uri()), '/');

            $output .= '<rule name="HMWP Theme' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $parent_theme_new_path . '/(.*)"  />' . "\n\t" . '<action type="Rewrite" url="' . $rel_parent_theme_path . '/{R:1}' . $this->trust_key . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";


            $parent_theme_new_path_with_main = $new_theme_path . '_main';

            $output .= '<rule name="HMWP Theme' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $parent_theme_new_path_with_main . '/style\.css"  />' . "\n\t" . '<action type="Rewrite" url="' . '/index.php?parent_wrapper=1' . str_replace('?', '&amp;', $this->trust_key) . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";

            $output .= '<rule name="HMWP Theme' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $parent_theme_new_path_with_main . '/(.*)"  />' . "\n\t" . '<action type="Rewrite" url="' . $rel_parent_theme_path . '/{R:1}' . $this->trust_key . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";


        }


        if ($new_admin_path && $new_admin_path != 'wp-admin')
            $output .= '<rule name="HMWP Admin' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $new_admin_path . '/(.*)"  />' . "\n\t" . '<action type="Rewrite" url="' . $this->sub_folder . 'wp-admin/{R:1}' . $this->trust_key . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";


        if ($new_login_path && $new_login_path != 'wp-login.php')
            $output .= '<rule name="HMWP Login' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $new_login_path . '"  />' . "\n\t" . '<action type="Rewrite" url="' . $this->sub_folder . trim($rel_login_path, '/') . $this->trust_key . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";


        if ($new_include_path)
            $output .= '<rule name="HMWP Include' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $new_include_path . '/(.*)"  />' . "\n\t" . '<action type="Rewrite" url="' . $rel_include_path . '/{R:1}' . $this->trust_key . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";

        if ($new_upload_path)
            $output .= '<rule name="HMWP Upload' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $new_upload_path . '/(.*)"  />' . "\n\t" . '<action type="Rewrite" url="' . $rel_upload_path . '/{R:1}' . $this->trust_key . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";


        if ($new_plugin_path && $pre_plugin_path)
            $output .= $pre_plugin_path;

        if ($new_plugin_path)
            $output .= '<rule name="HMWP Plugin_Dir' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $new_plugin_path . '/(.*)"  />' . "\n\t" . '<action type="Rewrite" url="' . $rel_plugin_path . '/{R:1}' . $this->trust_key . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";


        if ($new_style_name)
            $output .= '<rule name="HMWP Style' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $new_theme_path . '/' . str_replace('.', '\.', $new_style_name) . '"  />' . "\n\t" . '<action type="Rewrite" url="' . '/index.php?style_wrapper=1' . str_replace('?', '&amp;', $this->trust_key) . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";

        if ($this->add_auto_internal('css'))
            $output .= '<rule name="HMWP Int Style' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^_auto\.css' . '"  />' . "\n\t" . '<action type="Rewrite" url="' . '/index.php?style_internal_wrapper=1' . str_replace('?', '&amp;', $this->trust_key) . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";

        if ($this->add_auto_internal('js'))
            $output .= '<rule name="HMWP Int Script' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^_auto\.js' . '"  />' . "\n\t" . '<action type="Rewrite" url="' . '/index.php?script_internal_wrapper=1' . str_replace('?', '&amp;', $this->trust_key) . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";


        if ($this->add_get_wrapper())
            $output .= '<rule name="HMWP Get' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^_get/([A-Za-z0-9-_\.]+)/(.*)' . '"  />' . "\n\t" . '<action type="Rewrite" url="' . '/index.php?get_wrapper=1&_case={R:1}&_addr={R:1}' . str_replace('?', '&amp;', $this->trust_key) . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";

        if (trim($this->opt('new_style_name'), ' /') && trim($this->opt('new_style_name'), ' /') != 'style.css') {
            $old_style = $new_theme_path . '/' . 'style\.css';
            //$output.='rewrite ^/'.$old_style. '/nothing_404_404'.$this->trust_key.' last;'."\n";
            $output .= '<rule name="HMWP Other_WP' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $old_style . '"  />' . "\n\t" . '<action type="Rewrite" url="' . $iis_not_found . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";
        }

        if ($new_theme_path)
            $output .= '<rule name="HMWP Theme' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $new_theme_path . '/(.*)"  />' . "\n\t" . '<action type="Rewrite" url="' . $rel_theme_path . '/{R:1}' . $this->trust_key . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";


        if ($replace_comments_post && $replace_comments_post != 'wp-comments-post.php')
            $output .= '<rule name="HMWP Comment' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $replace_comments_post_rule . '"  />' . "\n\t" . '<action type="Rewrite" url="' . '/' . $rel_comments_post . $this->trust_key . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";

        if ($replace_admin_ajax_rule && $replace_admin_ajax_rule != 'wp-admin/admin-ajax.php')
            $output .= '<rule name="HMWP AJAX' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $replace_admin_ajax_rule . '"  />' . "\n\t" . '<action type="Rewrite" url="' . '/' . $rel_admin_ajax . $this->trust_key . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";

        if ($new_content_path)
            $output .= '<rule name="HMWP Content' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $new_content_path . '/(.*)"  />' . "\n\t" . '<action type="Rewrite" url="' . $rel_content_path . '/{R:1}' . $this->trust_key . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";

        if ($this->opt('hide_other_wp_files'))
            $output .= '<rule name="HMWP Other_WP' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^(' . $hide_other_file_rule . ')"  />' . "\n\t" . '<action type="Rewrite" url="' . $iis_not_found . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";

        if ($this->opt('disable_directory_listing'))
            $output .= '<rule name="HMWP Dir_List' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^' . $disable_directoy_listing . '"  />' . "\n\t" . '<action type="Rewrite" url="' . $iis_not_found . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";

        if ($this->opt('avoid_direct_access')) {
            $output .= '<rule name="HMWP Excerpt_PHP' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^(' . $white_regex . ')(.*)"  />' . "\n\t" . '<action type="Rewrite" url="/{R:1}{R:2}"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";
            $output .= '<rule name="HMWP Avoid_PHP' . rand(0, 9999) . '" stopProcessing="true">' . "\n\t" . '<match url="^(.*)\.php(.*)"  />' . "\n\t" . '<action type="Rewrite" url="' . $iis_not_found . '"  appendQueryString="true" />' . "\n" . '</rule>' . "\n";
        }

        if ($output)
            //$output='if (!-e $request_filename) {'. "\n" .  $output . "     break;\n}";
            $output = "# BEGIN Hide My WP\n\n" . $output . "\n# END Hide My WP";
        else
            $output = __('Nothing to add for current settings.', self::slug);

        $output = $this->cleanup_config($output);

        $html = '';
        $desc = __('Add to web.config to get all features of the plugin<br>', self::slug);

        if (isset($_GET['iis_config']) && $_GET['iis_config']) {

            $html = sprintf('%s ', $desc);
            $html .= sprintf('<span class="description">
        <ol style="color:#ff9900">
        <li>Web.config file is located in WP root directory</li>
        <li>Add it to right before <strong>&lt;rule name="wordpress" patternSyntax="Wildcard"&gt; </strong></li>
        <li>You may need to re-configure the server whenever you change settings or activate a new theme or plugin.</li>
        </ol></span><textarea readonly="readonly" onclick="" rows="5" cols="55" class="regular-text %1$s" id="%2$s" name="%2$s" style="%4$s">%3$s</textarea>', 'iis_config_class', 'iis_config', esc_textarea($output), 'width:95% !important;height:400px !important');


        } else {
            $html = '<a target="_blank" href="' . add_query_arg(array('die_message' => 'iis')) . '" class="button">' . __('Windows Configuration (IIS)', self::slug) . '</a>';
            $html .= sprintf('<br><span class="description"> %s</span>', $desc);
        }
        return $html;

    }

    function single_config()
    {
        $slashed_home = trailingslashit(get_option('home'));
        $base = parse_url($slashed_home, PHP_URL_PATH);

        if (!$this->sub_folder && $base && $base != '/')
            $sub_install = trim($base, ' /') . '/';
        else
            $sub_install = '';

        $new_theme_path = trim($this->opt('new_theme_path'), '/ ');
        $new_plugin_path = trim($this->opt('new_plugin_path'), '/ ');
        $new_upload_path = trim($this->opt('new_upload_path'), '/ ');
        $new_include_path = trim($this->opt('new_include_path'), '/ ');
        $new_style_name = trim($this->opt('new_style_name'), '/ ');
        $new_content_path = trim($this->opt('new_content_path'), '/ ');

        if (trim(get_option('hmwp_temp_admin_path'), ' /'))
            $new_admin_path = trim(get_option('hmwp_temp_admin_path'), ' /');
        else
            $new_admin_path = trim($this->opt('new_admin_path'), '/ ');

        $rel_login_path = $this->sub_folder . '/wp-login.php';
        $new_login_path = str_replace('.', '\.', trim($this->opt('new_login_path'), '/ '));

        if (is_multisite()) {
            if ($this->is_subdir_mu)
                $new_login_path = '/' . $new_login_path;
            $rel_login_path = $this->blog_path . str_replace($this->sub_folder, '', $new_login_path);
        }

        $replace_admin_ajax = trim($this->opt('replace_admin_ajax'), '/ ');
        $replace_admin_ajax_rule = str_replace('.', '\\.', $replace_admin_ajax);
        $replace_comments_post = trim($this->opt('replace_comments_post'), '/ ');
        $replace_comments_post_rule = str_replace('.', '\\.', $replace_comments_post);

        $upload_path = wp_upload_dir();

        if (is_ssl())
            $upload_path['baseurl'] = str_replace('http:', 'https:', $upload_path['baseurl']);

        $rel_upload_path = $sub_install . trim(str_replace(site_url(), '', $upload_path['baseurl']), '/');

        $rel_plugin_path = $sub_install . trim(str_replace(site_url(), '', HMW_WP_PLUGIN_URL), '/');
        $rel_theme_path = $sub_install . trim(str_replace(site_url(), '', get_stylesheet_directory_uri()), '/');
        $rel_comments_post = $sub_install . 'wp-comments-post.php';
        $rel_admin_ajax = $sub_install . 'wp-admin/admin-ajax.php';
        $rel_include_path2 = $sub_install . trim(WPINC); //To use in second part


        //Only use it if you want subfoler in first part
        $rel_include_path = $this->sub_folder . trim(WPINC);
        $rel_theme_path_with_subfolder = $this->sub_folder . trim(str_replace(site_url(), '', get_stylesheet_directory_uri()), '/');
        $rel_content_path = $this->sub_folder . trim(str_replace(site_url(), '', HMW_WP_CONTENT_URL), '/');
        $rel_theme_path_no_template = str_replace('/' . get_stylesheet(), '', $rel_theme_path);


        $style_path_reg = '';
        //if ($new_style_name && $new_style_name != 'style.css' && !isset($_POST['wp_customize']))
        //   $style_path_reg = '|'.$rel_theme_path.'/style\.css';

        //|'.$rel_plugin_path.'/index\.php|'.$rel_theme_path_no_template.'/index\.php'
        $hide_other_file_rule = $this->sub_folder . 'readme\.html|' . $this->sub_folder . 'license\.txt|' . $rel_content_path . '/debug\.log' . $style_path_reg . '|' . $rel_include_path . '/$';

        $disable_directoy_listing = '(((' . $rel_content_path . '|' . $rel_include_path . ')/([A-Za-z0-9\-\_\/]*))|(wp-admin/(!network\/?)([A-Za-z0-9\-\_\/]+)))(\.txt|/)$';

        if ($this->opt('login_query') && $this->opt('login_query'))
            $login_query = $this->opt('login_query');
        else
            $login_query = 'hide_my_wp';


        if ($this->opt('avoid_direct_access')) {
            $rel_theme_path_with_theme = $sub_install . trim(str_replace(site_url(), '', get_stylesheet_directory_uri()), '/');

            $white_list = explode(",", $this->opt('direct_access_except'));
            $white_list[] = 'wp-login.php';
            $white_list[] = 'index.php';
            $white_list[] = 'wp-admin/';

            if (get_option('hmwp_reset_token')) {
                $reset_url = $rel_plugin_path. '/' .dirname(HMW_FILE) . '/d.php';
                $white_list[] = $reset_url;
            }

            if ($this->opt('new_login_path'))
                $white_list[] = $this->opt('new_login_path');

            if ($this->opt('exclude_theme_access'))
                $white_list[] = $rel_theme_path_with_theme . '/';
            if ($this->opt('exclude_plugins_access'))
                $white_list[] = $rel_plugin_path . '/';

            $block = true;
            $white_regex = '';
            foreach ($white_list as $white_file) {
                $white_regex .= $sub_install . str_replace(array('.', ' '), array('\.', ''), $white_file) . '|';  //make \. remove spaces
            }
            $white_regex = substr($white_regex, 0, strlen($white_regex) - 1); //remove last |
            $white_regex = str_replace(array("\n", "\r\n", "\r"), '', $white_regex);
        }

        $output = '';

        if ($this->opt('full_hide')) {

            $full_hide = "
RewriteCond %{HTTP_COOKIE} !" . $this->access_cookie() . "=1
RewriteCond %{QUERY_STRING} !" . str_replace('?', '', $this->trust_key) . "
RewriteRule ^((wp-content|wp-includes|wp-admin)/(.*)) /" . $sub_install . "nothing_404_404" . $this->trust_key . " [QSA,L]
";

            $output = $full_hide . $output;
        }

        if ($this->opt('replace_urls') || $this->auto_replace_urls) {
            $replace_urls = $this->h->replace_newline(trim($this->opt('replace_urls'), ' '), '|%|');
            $replace_lines = explode('|%|', $replace_urls);
            $replace_lines = array_merge($replace_lines, $this->auto_replace_urls);

            if ($replace_lines) {
                foreach ($replace_lines as $line) {

                    $replace_word = explode('==', $line);
                    if (isset($replace_word[0]) && isset($replace_word[1])) {

                        //Check whether last character is / or not to recgnize folders
                        $is_folder = false;
                        if (substr($replace_word[0], strlen($replace_word[0]) - 1, strlen($replace_word[0])) == '/')
                            $is_folder = true;

                        $replace_word[0] = trim($replace_word[0], '/ ');
                        $replace_word[1] = trim($replace_word[1], '/ ');

                        $is_block = false;
                        if ($replace_word[1] == 'nothing_404_404')
                            $is_block = true;


                        if ($is_block) {
                            //Swap words to make theme unavailable
                            $temp = $replace_word[0];
                            $replace_word[0] = $replace_word[1];
                            $replace_word[1] = $temp;
                        }

                        $replace_word[0] = str_replace(array('amp;', '%2F', '//', '.'), array('', '/', '/', '.'), $replace_word[0]);
                        $replace_word[1] = str_replace(array('.', 'amp;'), array('\.', ''), $replace_word[1]);

                        if ($is_folder) {
                            $output .= 'RewriteRule ^' . $replace_word[1] . '/(.*) /' . $sub_install . $replace_word[0] . '/$1' . $this->trust_key . ' [QSA,L]' . "\n";
                        } else {
                            $output .= 'RewriteRule ^' . $replace_word[1] . ' /' . $sub_install . $replace_word[0] . $this->trust_key . ' [QSA,L]' . "\n";
                        }
                    }
                }
            }
        }


        $active_plugins = get_option('active_plugins');

        if ($this->opt('rename_plugins') == 'all')
            $active_plugins = get_option('hmw_all_plugins');

        $pre_plugin_path = '';
        if ($this->opt('rename_plugins') && $new_plugin_path) {
            foreach ((array)$active_plugins as $active_plugin) {

                //Ignore itself or a plugin without folder
                if (!$this->h->str_contains($active_plugin, '/') || $active_plugin == self::main_file || strpos($active_plugin, 'elementor') !== FALSE)
                    continue;

                $new_plugin_path = trim($new_plugin_path, '/ ');

                $codename_this_plugin = $this->hash($active_plugin);

                $rel_this_plugin_path = trim(str_replace(site_url(), '', plugin_dir_url($active_plugin)), '/');
                //Allows space in plugin folder name
                $rel_this_plugin_path = $sub_install . str_replace(' ', '\ ', $rel_this_plugin_path);

                $new_this_plugin_path = $new_plugin_path . '/' . $codename_this_plugin;
                $pre_plugin_path .= 'RewriteRule ^' . $new_this_plugin_path . '/(.*) /' . $rel_this_plugin_path . '/$1' . $this->trust_key . ' [QSA,L]' . "\n";
            }
        }


        if (is_child_theme()) {
            //remove the end folder of so we can replace it with parent theme
            $path_array = explode('/', $new_theme_path);
            array_pop($path_array);
            $path_string = implode('/', $path_array);

            if ($path_string)
                $path_string = $path_string . '/';

            $parent_theme_new_path = $path_string . get_template();
            $rel_parent_theme_path = $sub_install . trim(str_replace(site_url(), '', get_template_directory_uri()), '/');
            $output .= 'RewriteRule ^' . $parent_theme_new_path . '/(.*) /' . $rel_parent_theme_path . '/$1' . $this->trust_key . ' [QSA,L]' . "\n";
            $parent_theme_new_path_with_main = $new_theme_path . '_main';

            if ($sub_install)
                $output .= 'RewriteRule ^' . $parent_theme_new_path_with_main . '/style\.css' . ' /' . add_query_arg('parent_wrapper', '1', $sub_install) . str_replace('?', '&', $this->trust_key) . ' [QSA,L]' . "\n";
            else
                $output .= 'RewriteRule ^' . $parent_theme_new_path_with_main . '/style\.css' . ' /index.php?parent_wrapper=1' . str_replace('?', '&', $this->trust_key) . ' [QSA,L]' . "\n";


            $output .= 'RewriteRule ^' . $parent_theme_new_path_with_main . '/(.*) /' . $rel_parent_theme_path . '/$1' . $this->trust_key . ' [QSA,L]' . "\n";
        }

        if ($new_admin_path && $new_admin_path != 'wp-admin')
            $output .= 'RewriteRule ^' . $new_admin_path . '/(.*) /' . $sub_install . 'wp-admin/$1' . $this->trust_key . ' [QSA,L]' . "\n";

        if ($new_login_path && $new_login_path != 'wp-login.php')
            $output .= 'RewriteRule ^' . $new_login_path . ' /' . $sub_install . $rel_login_path . $this->trust_key . ' [QSA,L]' . "\n";


        if ($new_include_path)
            $output .= 'RewriteRule ^' . $new_include_path . '/(.*) /' . $rel_include_path2 . '/$1' . $this->trust_key . ' [QSA,L]' . "\n";

        if ($new_upload_path)
            $output .= 'RewriteRule ^' . $new_upload_path . '/(.*) /' . $rel_upload_path . '/$1' . $this->trust_key . ' [QSA,L]' . "\n";

        if ($new_plugin_path && $pre_plugin_path)
            $output .= $pre_plugin_path;

        if ($new_plugin_path)
            $output .= 'RewriteRule ^' . $new_plugin_path . '/(.*) /' . $rel_plugin_path . '/$1' . $this->trust_key . ' [QSA,L]' . "\n";

        if ($new_style_name)
            if ($sub_install)
                $output .= 'RewriteRule ^' . $new_theme_path . '/' . str_replace('.', '\.', $new_style_name) . ' /' . add_query_arg('style_wrapper', '1', $sub_install) . str_replace('?', '&', $this->trust_key) . ' [QSA,L]' . "\n";
            else
                $output .= 'RewriteRule ^' . $new_theme_path . '/' . str_replace('.', '\.', $new_style_name) . ' /index.php?style_wrapper=1' . str_replace('?', '&', $this->trust_key) . ' [QSA,L]' . "\n";


        if ($this->add_auto_internal('css'))
            if ($sub_install)
                $output .= 'RewriteRule ^_auto\.css' . ' /' . add_query_arg('style_internal_wrapper', '1', $sub_install) . str_replace('?', '&', $this->trust_key) . ' [QSA,L]' . "\n";
            else
                $output .= 'RewriteRule ^_auto\.css' . ' /index.php?style_internal_wrapper=1' . str_replace('?', '&', $this->trust_key) . ' [QSA,L]' . "\n";

        if ($this->add_auto_internal('js'))
            if ($sub_install)
                $output .= 'RewriteRule ^_auto\.js' . ' /' . add_query_arg('script_internal_wrapper', '1', $sub_install) . str_replace('?', '&', $this->trust_key) . ' [QSA,L]' . "\n";
            else
                $output .= 'RewriteRule ^_auto\.js' . ' /index.php?script_internal_wrapper=1' . str_replace('?', '&', $this->trust_key) . ' [QSA,L]' . "\n";

        //RewriteRule ^_get/([A-Za-z0-9-_\.]+)/(.*) /wp39/index.php?get_wrapper=1&_case=$1&_addr=$2&AK_hide_my_wp=1234 [QSA,L]
        if ($this->add_get_wrapper())
            if ($sub_install)
                $output .= 'RewriteRule ^_get/([A-Za-z0-9-_\.]+)/(.*)' . ' /' . add_query_arg('get_wrapper', '1', $sub_install) . '&_case=$1&_addr=$2' . str_replace('?', '&', $this->trust_key) . ' [QSA,L]' . "\n";
            else
                $output .= 'RewriteRule ^_get/([A-Za-z0-9-_\.]+)/(.*)' . ' /index.php?get_wrapper=1&_case=$1&_addr=$2' . str_replace('?', '&', $this->trust_key) . ' [QSA,L]' . "\n";

        if (trim($this->opt('new_style_name'), ' /') && trim($this->opt('new_style_name'), ' /') != 'style.css') {
            $old_style = $new_theme_path . '/' . 'style\.css';
            $output .= 'RewriteRule ^' . $old_style . ' /' . $sub_install . 'nothing_404_404' . $this->trust_key . ' [QSA,L]' . "\n";
        }


        if ($new_theme_path)
            $output .= 'RewriteRule ^' . $new_theme_path . '/(.*) /' . $rel_theme_path . '/$1' . $this->trust_key . ' [QSA,L]' . "\n";

        if ($replace_comments_post && $replace_comments_post != 'wp-comments-post.php')
            $output .= 'RewriteRule ^' . $replace_comments_post_rule . ' /' . $rel_comments_post . $this->trust_key . ' [QSA,L]' . "\n";

        if ($replace_admin_ajax_rule && $replace_admin_ajax_rule != 'wp-admin/admin-ajax.php')
            $output .= 'RewriteRule ^' . $replace_admin_ajax_rule . ' /' . $rel_admin_ajax . $this->trust_key . ' [QSA,L]' . "\n";

        if ($new_content_path)
            $output .= 'RewriteRule ^' . $new_content_path . '/(.*) /' . $rel_content_path . '/$1' . $this->trust_key . ' [QSA,L]' . "\n";

        if ($this->opt('hide_other_wp_files'))
            $output .= 'RewriteRule ^(' . $hide_other_file_rule . ') /' . $sub_install . 'nothing_404_404' . $this->trust_key . ' [QSA,L]' . "\n";

        if ($this->opt('disable_directory_listing'))
            $output .= 'RewriteRule ^' . $disable_directoy_listing . ' /' . $sub_install . 'nothing_404_404' . $this->trust_key . ' [QSA,L]' . "\n";

        if ($this->opt('avoid_direct_access')) {

            //RewriteCond %{REQUEST_URI} !(index\.php|wp-content/repair\.php|wp-includes/js/tinymce/wp-tinymce\.php|wp-comments-post\.php|wp-login\.php|index\.php|wp-admin/)(.*)

            $output .= 'RewriteCond %{REQUEST_URI} !(' . $white_regex . ')(.*)' . "\n";
            $output .= 'RewriteRule ^(.*)\.php(.*)' . ' /nothing_404_404' . $this->trust_key . ' [QSA,L]' . "\n";
        }

        if (!$output)
            $output = __('Nothing to add for current settings!', self::slug);
        else
            $output = "# BEGIN Hide My WP\n\n" . $output . "\n# END Hide My WP";

        $output = $this->cleanup_config($output);

        $html = '';
        $desc = __('In rare cases you need to configure it manually.<br>', self::slug);

        if (isset($_GET['single_config']) && $_GET['single_config']) {
            $html = sprintf(' %s ', $desc);
            $html .= sprintf('<span class="description">
        <ol style="color:#ff9900">
             <li> If you use <strong>BulletProof Security</strong> plugin first secure htaccess file using it  and then add below lines to your htaccess file using FTP. </li>
            <li> You may need to re-configure server whenever you change settings or activate a new theme or plugin. </li>
            <li>Add these lines right before: <strong>RewriteCond %{REQUEST_FILENAME} !-f</strong>. Next you may want to change htaccess permission to read-only (e.g. 666)</li>
        </ol></span><textarea readonly="readonly" onclick="" rows="5" cols="55" class="regular-text %1$s" id="%2$s" name="%2$s" style="%4$s">%3$s</textarea>', 'single_config_class', 'single_config', esc_textarea($output), 'width:95% !important;height:400px !important');


        } else {
            $html = '<a target="_blank" href="' . add_query_arg(array('die_message' => 'single')) . '" class="button">' . __('Manual Configuration', self::slug) . '</a>';
            $html .= sprintf('<br><span class="description"> %s</span>', $desc);
        }
        return $html;
        //rewrite ^/assets/css/(.*)$ /wp-content/themes/roots/assets/css/$1 last;


    }


    function multisite_config()
    {
        $slashed_home = trailingslashit(get_option('home'));
        $base = parse_url($slashed_home, PHP_URL_PATH);

        $new_theme_path = trim($this->opt('new_theme_path'), '/ ');
        $new_plugin_path = trim($this->opt('new_plugin_path'), '/ ');
        $new_upload_path = trim($this->opt('new_upload_path'), '/ ');
        $new_include_path = trim($this->opt('new_include_path'), '/ ');
        $new_style_name = trim($this->opt('new_style_name'), '/ ');
        $new_content_path = trim($this->opt('new_content_path'), '/ ');

        if (trim(get_option('hmwp_temp_admin_path'), ' /'))
            $new_admin_path = trim(get_option('hmwp_temp_admin_path'), ' /');
        else
            $new_admin_path = trim($this->opt('new_admin_path'), '/ ');

        $rel_login_path = $this->sub_folder . '/wp-login.php';
        $new_login_path = str_replace('.', '\.', trim($this->opt('new_login_path'), '/ '));

        if (is_multisite()) {
            if ($this->is_subdir_mu)
            $rel_login_path = $this->blog_path . str_replace($this->sub_folder, '', $new_login_path);
        }

        $replace_admin_ajax = trim($this->opt('replace_admin_ajax'), '/ ');
        $replace_admin_ajax_rule = str_replace('.', '\\.', $replace_admin_ajax);
        $replace_comments_post = trim($this->opt('replace_comments_post'), '/ ');
        $replace_comments_post_rule = str_replace('.', '\\.', $replace_comments_post);

        $upload_path = wp_upload_dir();

        if (is_ssl())
            $upload_path['baseurl'] = str_replace('http:', 'https:', $upload_path['baseurl']);

        $rel_upload_path = $this->sub_folder . trim(str_replace(site_url(), '', $upload_path['baseurl']), '/');
        $rel_include_path = $this->sub_folder . trim(WPINC);
        $rel_plugin_path = $this->sub_folder . trim(str_replace(site_url(), '', HMW_WP_PLUGIN_URL), '/');
        $rel_theme_path = $this->sub_folder . trim(str_replace(site_url(), '', get_stylesheet_directory_uri()), '/');
        $rel_comments_post = $this->sub_folder . 'wp-comments-post.php';
        $rel_admin_ajax = $this->sub_folder . 'wp-admin/admin-ajax.php';


        $rel_content_path = $this->sub_folder . trim(str_replace(site_url(), '', HMW_WP_CONTENT_URL), '/');
        $rel_theme_path_no_template = str_replace('/' . get_stylesheet(), '', $rel_theme_path);


        $style_path_reg = '';
        //if ($new_style_name && $new_style_name != 'style.css' && !isset($_POST['wp_customize']))
        //   $style_path_reg = '|'.$rel_theme_path.'/style\.css';

        //|'.$rel_plugin_path.'/index\.php|'.$rel_theme_path_no_template.'/index\.php'

        if (!$this->sub_folder && $base && $base != '/')
            $sub_install = trim($base, ' /') . '/';
        else
            $sub_install = '';


        if ($this->is_subdir_mu)
            $hide_other_file_rule = 'readme\.html|' . 'license\.txt|' . str_replace($this->sub_folder, '', $rel_content_path) . '/debug\.log' . str_replace($this->sub_folder, '', $style_path_reg) . '|' . str_replace($this->sub_folder, '', $rel_include_path) . '/$';
        else
            $hide_other_file_rule = $this->sub_folder . 'readme\.html|' . $this->sub_folder . 'license\.txt|' . $rel_content_path . '/debug\.log' . $style_path_reg . '|' . $rel_include_path . '/$';

        $disable_directoy_listing = '(((' . $rel_content_path . '|' . $rel_include_path . ')/([A-Za-z0-9\-\_\/]*))|(wp-admin/(!network\/?)([A-Za-z0-9\-\_\/]+)))(\.txt|/)$';

        if ($this->opt('login_query') && $this->opt('login_query'))
            $login_query = $this->opt('login_query');
        else
            $login_query = 'hide_my_wp';

        $output = '';

        if ($this->opt('avoid_direct_access')) {
            $rel_theme_path_with_theme = $this->sub_folder . trim(str_replace(site_url(), '', get_stylesheet_directory_uri()), '/');

            $white_list = explode(",", $this->opt('direct_access_except'));
            $white_list[] = 'wp-login.php';
            $white_list[] = 'index.php';
            $white_list[] = 'wp-admin/';

            if (get_option('hmwp_reset_token')) {
                $reset_url = $rel_plugin_path. '/' .dirname(HMW_FILE) . '/d.php';
                $white_list[] = $reset_url;
            }

            if ($this->opt('new_login_path'))
                $white_list[] = $this->opt('new_login_path');

            if ($this->opt('exclude_theme_access'))
                $white_list[] = $rel_theme_path_with_theme . '/';
            if ($this->opt('exclude_plugins_access'))
                $white_list[] = $rel_plugin_path . '/';

            $block = true;
            $white_regex = '';
            foreach ($white_list as $white_file) {
                $white_regex .= $this->sub_folder . str_replace(array('.', ' '), array('\.', ''), $white_file) . '|';  //make \. remove spaces
            }
            $white_regex = substr($white_regex, 0, strlen($white_regex) - 1); //remove last |
            $white_regex = str_replace(array("\n", "\r\n", "\r"), '', $white_regex);
        }

        if ($this->opt('full_hide')) {

            $full_hide = "
RewriteCond %{HTTP_COOKIE} !" . $this->access_cookie() . "=1
RewriteCond %{QUERY_STRING} !" . str_replace('?', '', $this->trust_key) . "
RewriteRule ^((wp-content|wp-includes|wp-admin)/(.*)) " . $this->sub_folder . '/nothing_404_404' . $this->trust_key . " [QSA,L]
";

            $output = $full_hide . $output;
        }

        if ($this->opt('replace_urls') || $this->auto_replace_urls) {
            $replace_urls = $this->h->replace_newline(trim($this->opt('replace_urls'), ' '), '|%|');
            $replace_lines = explode('|%|', $replace_urls);
            $replace_lines = array_merge($replace_lines, $this->auto_replace_urls);

            if ($replace_lines) {
                foreach ($replace_lines as $line) {

                    $replace_word = explode('==', $line);
                    if (isset($replace_word[0]) && isset($replace_word[1])) {

                        //Check whether last character is / or not to recgnize folders
                        $is_folder = false;
                        if (substr($replace_word[0], strlen($replace_word[0]) - 1, strlen($replace_word[0])) == '/')
                            $is_folder = true;

                        $replace_word[0] = trim($replace_word[0], '/ ');
                        $replace_word[1] = trim($replace_word[1], '/ ');

                        $is_block = false;
                        if ($replace_word[1] == 'nothing_404_404')
                            $is_block = true;


                        if ($is_block) {
                            //Swap words to make theme unavailable
                            $temp = $replace_word[0];
                            $replace_word[0] = $replace_word[1];
                            $replace_word[1] = $temp;
                        }

                        $replace_word[0] = str_replace(array('amp;', '%2F', '//', '.'), array('', '/', '/', '.'), $replace_word[0]);
                        $replace_word[1] = str_replace(array('.', 'amp;'), array('\.', ''), $replace_word[1]);

                        if ($this->is_subdir_mu)
                            $sub_install2 = $sub_install . $this->sub_folder;
                        else
                            $sub_install2 = $sub_install;

                        if ($is_folder) {

                            $output .= 'RewriteRule ^' . $replace_word[1] . '/(.*) /' . $sub_install2 . $replace_word[0] . '/$1' . $this->trust_key . ' [QSA,L]' . "\n";
                        } else {
                            $output .= 'RewriteRule ^' . $replace_word[1] . ' /' . $sub_install2 . $replace_word[0] . $this->trust_key . ' [QSA,L]' . "\n";
                        }
                    }
                }
            }
        }

        if (is_multisite()) {
            $sitewide_plugins = array_keys((array)get_site_option('active_sitewide_plugins', array()));
            $active_plugins = array_merge((array)get_blog_option(BLOG_ID_CURRENT_SITE, 'active_plugins'), $sitewide_plugins);
        } else {
            $active_plugins = get_option('active_plugins');
        }

        if ($this->opt('rename_plugins') == 'all')
            $active_plugins = get_option('hmw_all_plugins');

        $pre_plugin_path = '';
        if ($this->opt('rename_plugins') && $new_plugin_path) {
            foreach ((array)$active_plugins as $active_plugin) {

                //Ignore itself or a plugin without folder
                if (!$this->h->str_contains($active_plugin, '/') || $active_plugin == self::main_file || strpos($active_plugin, 'elementor') !== FALSE)
                    continue;

                $new_plugin_path = trim($new_plugin_path, '/ ');

                $codename_this_plugin = $this->hash($active_plugin);

                $rel_this_plugin_path = trim(str_replace(site_url(), '', plugin_dir_url($active_plugin)), '/');
                //Allows space in plugin folder name
                $rel_this_plugin_path = $this->sub_folder . str_replace(' ', '\ ', $rel_this_plugin_path);

                $new_this_plugin_path = $new_plugin_path . '/' . $codename_this_plugin;
                $pre_plugin_path .= 'RewriteRule ^' . $new_this_plugin_path . '/(.*) /' . $rel_this_plugin_path . '/$1' . $this->trust_key . ' [QSA,L]' . "\n";
            }
        }

        if ($new_admin_path && $new_admin_path != 'wp-admin')
            $output .= 'RewriteRule ^' . $new_admin_path . '/(.*) /' . $this->sub_folder . 'wp-admin/$1' . $this->trust_key . ' [QSA,L]' . "\n";

        if ($new_login_path && $new_login_path != 'wp-login.php') {
            if(is_multisite()) {
				$blogs = get_sites();
				foreach( $blogs as $blog ){
					$output .= 'RewriteRule ^'.str_replace("/".$this->sub_folder,'',$blog->path) . $new_login_path . ' /' . $blog->path . 'wp-login.php' . $this->trust_key . ' [QSA,L]' . "\n";
				}
			} else {
				$output .= 'RewriteRule ^' . $new_login_path . ' /' . $this->sub_folder . 'wp-login.php' . $this->trust_key . ' [QSA,L]' . "\n";
			}
		}

        if ($new_include_path)
            $output .= 'RewriteRule ^' . $new_include_path . '/(.*) /' . $rel_include_path . '/$1' . $this->trust_key . ' [QSA,L]' . "\n";

        if ($new_upload_path)
            $output .= 'RewriteRule ^' . $new_upload_path . '/(.*) /' . $rel_upload_path . '/$1' . $this->trust_key . ' [QSA,L]' . "\n";

        if ($new_plugin_path && $pre_plugin_path)
            $output .= $pre_plugin_path;

        if ($new_plugin_path)
            $output .= 'RewriteRule ^' . $new_plugin_path . '/(.*) /' . $rel_plugin_path . '/$1' . $this->trust_key . ' [QSA,L]' . "\n";

        if ($new_style_name)
            $output .= 'RewriteRule ^' . str_replace('.', '\.', $new_theme_path) . '/([_0-9a-zA-Z-]+)/' . str_replace('.', '\.', $new_style_name) . ' /' . $this->sub_folder . 'index.php?style_wrapper=true&template_wrapper=$1' . str_replace('?', '&', $this->trust_key) . ' [QSA,L]' . "\n";


        if ($this->add_auto_internal('css'))
            $output .= 'RewriteRule ^_auto\.css' . ' /' . $this->sub_folder . 'index.php?style_internal_wrapper=true' . str_replace('?', '&', $this->trust_key) . ' [QSA,L]' . "\n";

        if ($this->add_auto_internal('js'))
            $output .= 'RewriteRule ^_auto\.js' . ' /' . $this->sub_folder . 'index.php?script_internal_wrapper=true' . str_replace('?', '&', $this->trust_key) . ' [QSA,L]' . "\n";

        if ($this->add_get_wrapper())
            $output .= 'RewriteRule ^_get/([A-Za-z0-9-_\.]+)/(.*)' . ' /' . $this->sub_folder . 'index.php?get_wrapper=true&_case=$1&_addr=$2' . str_replace('?', '&', $this->trust_key) . ' [QSA,L]' . "\n";

        if (trim($this->opt('new_style_name'), ' /') && trim($this->opt('new_style_name'), ' /') != 'style.css') {

            if ($this->is_subdir_mu)
                $output .= 'RewriteRule ^' . $new_theme_path . '/([_0-9a-zA-Z-]+)/style\.css /' . $this->sub_folder . 'nothing_404_404' . $this->trust_key . ' [QSA,L]' . "\n";
            else
                $output .= 'RewriteRule ^' . $new_theme_path . '/([_0-9a-zA-Z-]+)/style\.css /nothing_404_404' . $this->trust_key . ' [QSA,L]' . "\n";

        }

        if ($new_theme_path)
            $output .= 'RewriteRule ^' . $new_theme_path . '/(.*) /' . str_replace('/' . get_stylesheet(), '', $rel_theme_path) . '/$1' . $this->trust_key . ' [QSA,L]' . "\n";

        if ($replace_comments_post && $replace_comments_post != 'wp-comments-post.php')
            if ($this->is_subdir_mu)
                $output .= 'RewriteRule ^([_0-9a-zA-Z-]+/)?' . $replace_comments_post_rule . ' /' . $rel_comments_post . $this->trust_key . ' [QSA,L]' . "\n";
            else
                $output .= 'RewriteRule ^' . $replace_comments_post_rule . ' /' . $rel_comments_post . $this->trust_key . ' [QSA,L]' . "\n";


        if ($replace_admin_ajax_rule && $replace_admin_ajax_rule != 'wp-admin/admin-ajax.php') {
            if ($this->is_subdir_mu)
                $output .= 'RewriteRule ^([_0-9a-zA-Z-]+/)?' . $replace_admin_ajax_rule . ' /' . $rel_admin_ajax . $this->trust_key . ' [QSA,L]' . "\n";
            else
                $output .= 'RewriteRule ^' . $replace_admin_ajax_rule . ' /' . $rel_admin_ajax . $this->trust_key . ' [QSA,L]' . "\n";
        }

        if ($new_content_path)
            $output .= 'RewriteRule ^' . $new_content_path . '/(.*) /' . $rel_content_path . '/$1' . $this->trust_key . ' [QSA,L]' . "\n";

        if ($this->opt('hide_other_wp_files'))
            if ($this->is_subdir_mu)
                $output .= 'RewriteRule ^(' . $hide_other_file_rule . ') /' . $this->sub_folder . 'nothing_404_404' . $this->trust_key . ' [QSA,L]' . "\n";
            else
                $output .= 'RewriteRule ^(' . $hide_other_file_rule . ') /nothing_404_404' . $this->trust_key . ' [QSA,L]' . "\n";

        if ($this->opt('disable_directory_listing'))
            if ($this->is_subdir_mu)
                $output .= 'RewriteRule ^' . $disable_directoy_listing . ' /' . $this->sub_folder . 'nothing_404_404' . $this->trust_key . ' [QSA,L]' . "\n";
            else
                $output .= 'RewriteRule ^' . $disable_directoy_listing . ' /nothing_404_404' . $this->trust_key . ' [QSA,L]' . "\n";

        if ($this->opt('avoid_direct_access')) {

            //RewriteCond %{REQUEST_URI} !(index\.php|wp-content/repair\.php|wp-includes/js/tinymce/wp-tinymce\.php|wp-comments-post\.php|wp-login\.php|index\.php|wp-admin/)(.*)

            if ($this->is_subdir_mu) {
                $output .= 'RewriteCond %{REQUEST_URI} !(' . str_replace($this->sub_folder, '', $white_regex) . ')(.*)' . "\n";
                $output .= 'RewriteRule ^(.*)\.php(.*)' . ' /' . $this->sub_folder . 'nothing_404_404' . $this->trust_key . ' [QSA,L]' . "\n";
            } else {
                $output .= 'RewriteCond %{REQUEST_URI} !(' . $white_regex . ')(.*)' . "\n";
                $output .= 'RewriteRule ^(.*)\.php(.*)' . ' /nothing_404_404' . $this->trust_key . ' [QSA,L]' . "\n";
            }
        }

        if (!$output)
            $output = __('Nothing to add for current settings!', self::slug);
        else
            $output = "# BEGIN Hide My WP\n\n" . $output . "\n# END Hide My WP";

        $output = $this->cleanup_config($output);

        $html = '';
        $desc = __('Add following lines to your .htaccess file to get all features of the plugin.<br>', self::slug);
        if (isset($_GET['multisite_config']) && $_GET['multisite_config']) {

            $html = sprintf('%s ', $desc);
            $html .= sprintf('<span class="description">
            <ol style="color:#ff9900">
            <li>Add below lines right before <strong>RewriteCond %%{REQUEST_FILENAME} !-f [OR]</strong> </li>
            <li>You may need to re-configure the server whenever you change settings or activate a new plugin.</li> </ol></span>.
        <textarea readonly="readonly" onclick="" rows="5" cols="55" class="regular-text %1$s" id="%2$s" name="%2$s" style="%4$s">%3$s</textarea>', 'multisite_config_class', 'multisite_config', esc_textarea($output), 'width:95% !important;height:400px !important');


        } else {
            $html = '<a target="_blank" href="' . add_query_arg(array('die_message' => 'multisite')) . '" class="button">' . __('Multi-site Configuration', self::slug) . '</a>';
            $html .= sprintf('<br><span class="description"> %s</span>', $desc);
        }
        return $html;
        //rewrite ^/assets/css/(.*)$ /wp-content/themes/roots/assets/css/$1 last;
    }


    /**
     * Register settings page
     *
     */
    /**
     * HideMyWP::register_settings()
     *
     * @return
     */
    function register_settings()
    {
        require_once('admin-settings.php');
    }

    function load_this_plugin_first()
    {
        // ensure path to this file is via main wp plugin path
        $wp_path_to_this_file = preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR . "/$2", __FILE__);
        $this_plugin = plugin_basename(trim($wp_path_to_this_file));
        if (is_multisite()) {
            global $current_blog;
            $active_plugins = array_keys(get_site_option('active_sitewide_plugins', array()));
            $codes = array_values(get_site_option('active_sitewide_plugins', array()));
        } else {
            $active_plugins = get_option('active_plugins', array());
        }

        $this_plugin_key = array_search($this_plugin, $active_plugins);

        if (in_array($this_plugin, $active_plugins) && $active_plugins[0] != $this_plugin) {
            array_splice($active_plugins, $this_plugin_key, 1);
            array_unshift($active_plugins, $this_plugin);
            if (is_multisite()) {
                $this_plugin_code = $codes[$this_plugin_key];
                array_splice($codes, $this_plugin_key, 1);
                array_unshift($codes, $this_plugin_code);

                update_site_option('active_sitewide_plugins', array_combine($active_plugins, $codes));
            } else {
                update_option('active_plugins', $active_plugins);
            }

        }

    }

    /* Got from W3TC */
    function is_html($content)
    { // is_html or json or xml

        if (strlen($content) > 1000) {
            $content = substr($content, 0, 1000);
        }

        $content = ltrim($content, "\x00\x09\x0A\x0D\x20\xBB\xBF\xEF");

        return stripos($content, '{[') !== false || stripos($content, '{"') !== false || stripos($content, '<?xml') !== false || stripos($content, '<html') !== false || stripos($content, '<!DOCTYPE') !== false;
    }

    function w3tc_minify_before($buffer)
    {
        return $this->none_replaced_buffer;
    }

    //only works for auto mode not manual
    function w3tc_minify_after($buffer)
    {
        return $this->global_html_filter($buffer);
    }

    /**
     * Redirect wp-register.page to 404 page
     */
    function hmwp_register_page_patch(){
        global $pagenow;
        if ( ( strtolower($pagenow) == 'wp-login.php') && isset( $_GET['action'] ) && ( strtolower( $_GET['action']) == 'register' ) ) {
            wp_redirect( home_url('/404/'));
            exit;
        }else if(!is_admin() && isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != '' && basename($_SERVER['REQUEST_URI']) == 'register' ){
            $slug = basename($_SERVER['REQUEST_URI']);
            $page = get_page_by_path( $slug );
            if( empty($page) && $slug == 'register' ){
                wp_redirect(home_url('/404/'));
                exit;
            }
        }
    }

    /**
     * @version 6.0.0
     * @param type $schedules
     * @return Create weekly cron job
     */
    function hmwp_cron_add_weekly( $schedules ) {
        // Adds once weekly to the existing schedules.
        $schedules['weekly'] = array(
            'interval' => 604800,
            'display' => __( 'Once Weekly' )
        );
        return $schedules;
    }

    /**
     * Function to run the cron job
     */
    public function hmwp_update_ips_to_server_func(){
        //return if help trust network option is disabled
        if(!$this->opt('help_trust_network')){
            return;
        }
        global $wpdb;
        if(get_option('wpw_api_weekly_cron_job') == 'yes'){
            $sql = 'SELECT COUNT(ip) as total_ip, ip FROM ' . $wpdb->prefix . 'hmwp_ms_intrusions WHERE created >= DATE_ADD(CURDATE(),INTERVAL -7 DAY) GROUP BY ip';
        }else{
            $sql = 'SELECT COUNT(ip) as total_ip, ip FROM ' . $wpdb->prefix . 'hmwp_ms_intrusions GROUP BY ip';
            update_option('wpw_api_weekly_cron_job', 'yes');
        }
        /**
        * No need to get trust network rules as we have made it dynamic : 02-12-2019
        $rules = get_option('trust_network_rules');
        */
        $banned_ips = $this->opt('blocked_ips') != '' ? explode(',', $this->opt('blocked_ips')) : array();
        $results = $wpdb->get_results($sql);
        $ip = array();
        if($results){
            foreach ($results as $key => $single_arr) {
                $ip['ip'][] = array(
                    'ip' => $single_arr->ip,
                    'count' => $single_arr->total_ip,
                    'site_id' => home_url(),
                    'ban' => array_search($single_arr->ip,$banned_ips) ? 1 : 0,
                );
            }
            $data = wp_remote_post('https://api.wpwave.com/v2/wp-json/wpw_api/insert-ip/', array(
                'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
                'body'        => json_encode($ip),
                'method'      => 'POST',
                'data_format' => 'body',
            ));
            if ( is_wp_error( $data ) ) {
                $error_message = $data->get_error_message();
                echo "Something went wrong: $error_message";
            } else {
                echo $data['body'];
            }
        }
    }

    public function hmwp_activation_redirect($plugin){
        if( $plugin == plugin_basename( __FILE__ ) ) {
           if(get_option('hmwp_setup_run') !== 'yes' && !is_multisite()){
                exit( wp_redirect( admin_url( 'admin.php?page=hmwp_setup_wizard' ) ) );
            }
        }
    }

	public function hmwp_get_user_ip() {
		$ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
		foreach ($ip_keys as $key) {
			if (getenv($key)) {
				return getenv($key);
			}
			if (isset($_SERVER[$key]) && !empty($_SERVER[$key])) {
				return $_SERVER[$key];
			}
		}
		return '';
	}

	public static function load_ip_countries_db_table() {
		global $wpdb;
		include_once('lib/mute-screamer/mute-screamer.php');

		/* Add Table Columns if not exits ----------------------------------- */
		$hmwp_blocked_ips_table = $wpdb->prefix . 'hmwp_blocked_ips';
		$table_clm = $wpdb->get_var("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE `table_name`= '{$hmwp_blocked_ips_table}' AND table_schema = '".DB_NAME."'");
		if ($table_clm) {
			$allow_clm = $wpdb->get_var("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE `table_name`= '{$hmwp_blocked_ips_table}' AND table_schema = '".DB_NAME."' AND `column_name`= 'allow'");
			if (!$allow_clm) {
				$wpdb->query("ALTER TABLE `{$hmwp_blocked_ips_table}` ADD `allow` TINYINT(1) NULL DEFAULT '0' AFTER `ip`;");
			}
			$source_clm = $wpdb->get_var("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE `table_name`= '{$hmwp_blocked_ips_table}' AND table_schema = '".DB_NAME."' AND `column_name`= 'source'");
			if (!$source_clm) {
				$wpdb->query("ALTER TABLE `{$hmwp_blocked_ips_table}` ADD `source` VARCHAR(255) NULL AFTER `ip`;");
			}
		}
		/* ------------------------------------------------------------------ */
		$_version = get_option('hmwp_version');
		if (empty($_version) || $_version != HMW_VERSION) {
			update_option('hmwp_version', HMW_VERSION);
			$ip_countries_table = $wpdb->prefix . 'hmwp_ip_countries';
			$query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($ip_countries_table));
			if ($wpdb->get_var($query) == $ip_countries_table) {
				return true;
			}
			/**
			 * Create Table
			 */
			$sql_create = "CREATE TABLE IF NOT EXISTS `{$ip_countries_table}` (
					`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
					`ip` varchar(25) NOT NULL,
					`countryCode` varchar(25) NOT NULL,
					`created` datetime NOT NULL,
					PRIMARY KEY (`id`),
					KEY `ip` (`ip`),
					KEY `created` (`created`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$wpdb->query($sql_create);
			if ($wpdb->get_var($query) == $ip_countries_table) {
				return true;
			}
		} else {
			update_option('hmwp_version', HMW_VERSION);
		}
		return false;
	}

	public function hmwp_disable_api($access) {
		$error_message = esc_html__('The REST API is disabled.', self::slug);
		if (is_wp_error($access)) {
			$access->add('rest_cannot_access', $error_message, array('status' => rest_authorization_required_code()));
			return $access;
		}
		return new WP_Error('rest_cannot_access', $error_message, array('status' => rest_authorization_required_code()));
	}

	public function hmwp_rest_url_prefix($prefix = 'wp-json') {
		$this->top_replace_old[] = " rel='https://api.w.org/'";
		$this->top_replace_new[] = " ";
		$new_prefix = $this->opt('api_base');
		if (!$this->opt('api_disable') && !empty($new_prefix) && 'wp-json' != trim($new_prefix, ' /')) {
			$prefix = $new_prefix;
		}
		return $prefix;
	}

	public function hmwp_rest_url($url, $path, $blog_id, $scheme) {
		$new_api_query = trim($this->opt('api_query'), ' /');
		if (!$this->opt('api_disable') && !empty($new_api_query) && 'rest_route' != $new_api_query) {
			$url = str_replace("rest_route=", $new_api_query . '=', $url);
		}
		return $url;
	}

}

$HideMyWP = new HideMyWP();
