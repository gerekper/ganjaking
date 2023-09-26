<?php
$can_deactive = false;
if (isset($_COOKIE['hmwp_can_deactivate']) && preg_replace("/[^a-zA-Z]/", "", substr(NONCE_SALT, 0, 8)) == preg_replace("/[^a-zA-Z]/", "", $_COOKIE['hmwp_can_deactivate'])) {
	$can_deactive = true;
}

//may also need to change mute-sceamer
$this->short_prefix = preg_replace("/[^a-zA-Z]/", "", substr(NONCE_SALT, 0, 6)) . '_';

//Fix a WP problem caused by filters order for deactivation
$settings = get_option(self::slug);
if (isset($_GET['action']) && $_GET['action'] == 'deactivate' && isset($_GET['plugin']) && $_GET['plugin'] == self::main_file && is_admin() && $can_deactive) {
    update_option(self::slug . '_undo', get_option(self::slug));
    if(isset($settings['uninstall_hmwp_data']) && $settings['uninstall_hmwp_data']){
        delete_option(self::slug);
        delete_option('hmwp_setup_run');
    }
}

if(isset($settings['uninstall_hmwp_data']) && $settings['uninstall_hmwp_data']){
    if ((isset($_POST['action']) && $_POST['action'] == 'deactivate-selected') || (isset($_POST['action2']) && $_POST['action2'] == 'deactivate-selected') && is_admin() && $can_deactive) {        
        $plugins = isset($_POST['checked']) ? (array)$_POST['checked'] : array();
        foreach ($plugins as $plugin){
            if ($plugin == self::main_file){
                delete_option(self::slug);
                delete_option('hmwp_setup_run');
            }
        }
    }    
}

include_once('lib/class.helper.php');
$this->h = new PP_Helper(self::slug, self::ver);
$this->h->check_versions('5.0', '3.4');
if (is_admin() || $can_deactive) {
	$this->h->register_messages();
}

$sub_installation = trim(str_replace(home_url(), '', site_url()), ' /');

if ($sub_installation && substr($sub_installation, 0, 4) != 'http')
    $this->sub_folder = $sub_installation . '/';

$this->is_subdir_mu = false;
if (is_multisite())
    $this->is_subdir_mu = true;
if ((defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL) || (defined('VHOST') && VHOST == 'yes'))
    $this->is_subdir_mu = false;

if (is_multisite() && !$this->sub_folder && $this->is_subdir_mu)
    $this->sub_folder = ltrim(parse_url(trim(get_blog_option(BLOG_ID_CURRENT_SITE, 'home'), '/') . '/', PHP_URL_PATH), '/');

if (is_multisite() && !$this->blog_path && $this->is_subdir_mu) {
    global $current_blog;
    $this->blog_path = str_replace($this->sub_folder, '', $current_blog->path); //has /
}

if (is_admin()) {
    include_once('lib/class.wplisttable.php');
    include_once('lib/class.settings-api.php');
    add_action('init', array(&$this, 'register_settings'), 5);
}

if (is_multisite())
    $this->options = get_blog_option(BLOG_ID_CURRENT_SITE, self::slug);
else
    $this->options = get_option(self::slug);

if (is_admin() && $can_deactive) {
	$this->load_this_plugin_first();
}

/* Block IP Check ----------------------------------------------------------- */
global $wpdb;
$blocked_ips_table = $wpdb->prefix . 'hmwp_blocked_ips';
$user_ip = $this->hmwp_get_user_ip();
/**
 * Check IP from DB Table
 */
//$dbips_info = $wpdb->get_var("SELECT `ip` FROM `{$blocked_ips_table}` WHERE `allow`='1' AND `ip`='{$user_ip}'");
if(filter_var($user_ip, FILTER_VALIDATE_IP)===false){
	$dbips_info = null;
} else {
	$dbips_info = $wpdb->get_var($wpdb->prepare("SELECT `ip` FROM `{$blocked_ips_table}` WHERE `allow`='1' AND `ip`=%s",$user_ip));
}

if (empty($dbips_info)) {
	/**
	 * Check Blocked & Allowed Countries
	 */
	$ccode = $this->h->countryCode($user_ip);
	$blockIPInfo = array('user_id' => $user_ip, 'country' => $ccode, 'is_block' => false, 'type' => '');
	if (!empty($ccode)) {
		if ($this->opt('blocked_countries')) {
			foreach (explode(',', $this->opt('blocked_countries')) as $country) {
				if (strtoupper($ccode) == strtoupper(trim($country, ' '))) {
					$blockIPInfo['is_block'] = true;
					$blockIPInfo['source'] = 'blocked_countries';
				}
			}
		}
		if ($this->opt('allowed_countries')) {
			$allowed_countries = explode(',', $this->opt('allowed_countries'));
			if (!empty($allowed_countries)) {
				$blockIPInfo['is_block'] = true;
				$blockIPInfo['source'] = 'allowed_countries';
				foreach ($allowed_countries as $country) {
					if (strtoupper($ccode) == strtoupper(trim($country, ' '))) {
						$blockIPInfo['is_block'] = false;
						$blockIPInfo['source'] = '';
					}
				}
			}
		}
	}
	/**
	 * Check malware ips from server (trust_network)
	 */
	if ($this->opt('trust_network')) {
		$malware_ips = array();
		$get_malware_db = get_transient('hmwp_server_malware_ips');
		$get_whitelist_db = get_transient('hmwp_server_whitelist_ips');
		$whitelist_ips=array();
		if ($get_whitelist_db !== false) {
			$whitelist_ips=$get_whitelist_db;
		} else {			
			$whitelist_get_post = wp_remote_get('https://api.wpwave.com/v2/whiltelist-ip.json');
			if (is_array($whitelist_get_post) && isset($whitelist_get_post['response']) && isset($whitelist_get_post['response']['code']) && $whitelist_get_post['response']['code'] == 200) {
				$tn_body = json_decode($whitelist_get_post['body']);
				if (is_array($tn_body)) {
					foreach ($tn_body as $single_whitelist_ips) {
						$whitelist_ips[] = $single_whitelist_ips->ip;
					}
				}
			}
			set_transient('hmwp_server_whitelist_ips', $whitelist_ips, 24 * 60 * 60);
		}
		if ($get_malware_db !== false) {
			$malware_ips = $get_malware_db;
			$malware_ips=array_diff($malware_ips,$whitelist_ips);		
		} else {
			$malware_get_post = wp_remote_get('https://api.wpwave.com/v2/wp-json/wpw_api/dangerous-ip/');
			if (is_array($malware_get_post) && isset($malware_get_post['response']) && isset($malware_get_post['response']['code']) && $malware_get_post['response']['code'] == 200) {
				$tn_body = json_decode($malware_get_post['body']);
				if (is_array($tn_body)) {
					foreach ($tn_body as $single_ban_ips) {
						$malware_ips[] = $single_ban_ips->ip;
					}
				}
			}
			$malware_ips=array_diff($malware_ips,$whitelist_ips);
			set_transient('hmwp_server_malware_ips', $malware_ips, 24 * 60 * 60);
		}

		/* Remove Local IPs ----------------------------------------------------- */
		if ($key_127001 = array_search('127.0.0.1', $malware_ips)) {
			unset($malware_ips[$key_127001]);
		}
		if ($key_1 = array_search('::1', $malware_ips)) {
			unset($malware_ips[$key_1]);
		}
		/* ---------------------------------------------------------------------- */
		$malware_ips = apply_filters('hmwp_filter_trust_network_ips', $malware_ips, $blockIPInfo);
		if ($malware_ips) {
			foreach ($malware_ips as $ip) {
				if ($this->netMatch($ip, $user_ip)) {
					$blockIPInfo['is_block'] = true;
					$blockIPInfo['source'] = 'trust_network';
				}
			}
		}
	}
	/**
	 * Check blocked_ips from plugin settings
	 */
	if ($this->opt('blocked_ips')) {
		$banned_ips = explode(',', $this->opt('blocked_ips'));
		$banned_ips = apply_filters('hmwp_filter_blocked_ips', $banned_ips, $blockIPInfo);
		if (!empty($banned_ips)) {
			foreach ($banned_ips as $ip) {
				if ($this->netMatch($ip, $user_ip)) {
					$blockIPInfo['is_block'] = true;
					$blockIPInfo['source'] = 'blocked_ips';
				}
			}
		}
	}
	$blockIPInfo = apply_filters('hmwp_filter_before_user_blocked', $blockIPInfo);
	if (!$can_deactive && (isset($blockIPInfo['is_block']) && $blockIPInfo['is_block'] == true)) {
		do_action('hmwp_action_before_user_blocked', $blockIPInfo);
		status_header(404);
		nocache_headers();
		if ($this->opt('enable_ids') || $this->opt('trust_network')) {
			$is_ips = $wpdb->get_var("SELECT `ip` FROM `{$blocked_ips_table}` WHERE `ip`='{$user_ip}' LIMIT 1");
			if (empty($is_ips)) {
				$wpdb->insert(
					$blocked_ips_table, array(
						'ip' => $user_ip,
						'source' => (isset($blockIPInfo['source']) ? $blockIPInfo['source'] : ''),
						'created' => date('Y-m-d H:i:s', time())
					)
				);
			}
		}
		echo $this->opt('blocked_ip_message');
		die;
	}
}
/* End Block IP Check ------------------------------------------------------- */

if (defined('W3TC') && trim($this->opt('new_content_path'), ' /') && trim($this->opt('new_content_path'), '/ ') != 'wp-content') {
	if ($this->h->str_contains($_SERVER['REQUEST_URI'], trim($this->opt('new_content_path'), ' /') . '/cache/minify/')) {
		$_SERVER['REQUEST_URI'] = str_replace('inc', 'wp-content', $_SERVER['REQUEST_URI']);
	}
}
/**
 * IDS
 */
if ($this->opt('enable_ids')) {
    include_once('lib/mute-screamer/mute-screamer.php');

    if (!$this->h->str_contains($this->opt('exception_fields'), 'REQUEST.remember_%')) {
        $opts = get_option(self::slug);
        $opts['exception_fields'] = $opts['exception_fields'] . "\n" . "REQUEST.remember_%";
        update_option(self::slug, $opts);
    }
}

add_filter('pp_settings_api_filter', array(&$this, 'pp_settings_api_filter'), 100, 1);
add_action('pp_settings_api_reset', array(&$this, 'pp_settings_api_reset'), 100, 1);
add_action('init', array(&$this, 'init'), 1);
add_action('wp', array(&$this, 'wp'));
add_action('generate_rewrite_rules', array(&$this, 'add_rewrite_rules'));
add_filter('404_template', array(&$this, 'custom_404_page'), 10, 1);
add_filter('the_content', array(&$this, 'post_filter'));

global $wp_rewrite, $wp, $wp_query, $wp_version;
if (version_compare($wp_version, '4.7', '>=')) {
	if ('1' == $this->opt('api_disable') && !is_admin()) {
		add_filter('rest_authentication_errors', array(&$this, 'hmwp_disable_api'), 99);
	}
	add_filter('rest_url_prefix', array(&$this, 'hmwp_rest_url_prefix'), 99);
	add_filter('rest_url', array(&$this, 'hmwp_rest_url'), 1000, 4);
}

$current_page = basename($_SERVER['REQUEST_URI']);    
if(is_admin() && $current_page == 'admin.php?page=hide_my_wp'){
    //do nothing
}else{
    add_action('admin_notices', array(&$this, 'admin_notices'));
}

add_filter('posts_request', array(&$this, 'disable_main_wp_query'), 110, 2 );
add_action('wp', array(&$this, 'global_assets_filter'));
/**
 * Remove all dns-prefetch links
 */
remove_action('wp_head', 'wp_resource_hints', 2);
/*if(!function_exists('wp_get_current_user')) {
	include_once(ABSPATH . '/wp-includes/pluggable.php');
}*/
if (isset($_GET['die_message']) && is_admin()) {
    add_action('admin_init', array(&$this, 'die_message'), 1000);
}

if ((is_admin() || $can_deactive)) {
	add_action('admin_init', array(&$this, 'hmwp_plugin_update_checker'));
}

//compatibility with social login
if ($this->opt('disable_directory_listing')) {
    defined('WORDPRESS_SOCIAL_LOGIN_PLUGIN_URL')
    || define('WORDPRESS_SOCIAL_LOGIN_PLUGIN_URL', plugins_url() . '/wordpress-social-login/');
    defined('WORDPRESS_SOCIAL_LOGIN_HYBRIDAUTH_ENDPOINT_URL')
    || define('WORDPRESS_SOCIAL_LOGIN_HYBRIDAUTH_ENDPOINT_URL', WORDPRESS_SOCIAL_LOGIN_PLUGIN_URL . '/hybridauth/index.php');
}

if (is_multisite())
    add_action('network_admin_notices', array(&$this, 'admin_notices'));

if ($this->opt('antispam')) {
    add_action('init', array(&$this, 'spam_blocker'), 1);
    add_action('comment_form_default_fields', array(&$this, 'spam_blocker_fake_field'), 1000);
}

if ($this->opt('login_query'))
    $login_query = $this->opt('login_query');
else
    $login_query = 'hide_my_wp';

if (!$can_deactive && $this->opt('hide_wp_admin') && $this->opt('hide_other_wp_files') && $this->h->ends_with($_SERVER['PHP_SELF'], 'customize.php') && (!isset($_GET[$login_query]) || $_GET[$login_query] != $this->opt('admin_key')))
    $this->block_access();

if ($this->opt('replace_mode') == 'quick' && !is_admin() && !isset($_GET['die_message'])) {
//root
    add_filter('plugins_url', array(&$this, 'partial_filter'), 1000, 1);
    add_filter('bloginfo', array(&$this, 'partial_filter'), 1000, 1);
    add_filter('stylesheet_directory_uri', array(&$this, 'partial_filter'), 1000, 1);
    add_filter('template_directory_uri', array(&$this, 'partial_filter'), 1000, 1);
    add_filter('script_loader_src', array(&$this, 'partial_filter'), 1000, 1);
    add_filter('style_loader_src', array(&$this, 'partial_filter'), 1000, 1);

    add_filter('stylesheet_uri', array(&$this, 'partial_filter'), 1000, 1);
    add_filter('includes_url', array(&$this, 'partial_filter'), 1000, 1);
    add_filter('bloginfo_url', array(&$this, 'partial_filter'), 1000, 1);

    if (!$this->is_permalink()) {
        add_filter('author_link', array(&$this, 'partial_filter'), 1000, 1);
        add_filter('post_link', array(&$this, 'partial_filter'), 1000, 1);
        add_filter('page_link', array(&$this, 'partial_filter'), 1000, 1);
        add_filter('attachment_link', array(&$this, 'partial_filter'), 1000, 1);
        add_filter('post_type_link', array(&$this, 'partial_filter'), 1000, 1);
        add_filter('get_pagenum_link', array(&$this, 'partial_filter'), 1000, 1);

        add_filter('category_link', array(&$this, 'partial_filter'), 1000, 1);
        add_filter('tag_link', array(&$this, 'partial_filter'), 1000, 1);

        add_filter('feed_link', array(&$this, 'partial_filter'), 1000, 1);
        add_filter('category_feed_link', array(&$this, 'partial_filter'), 1000, 1);
        add_filter('tag_feed_link', array(&$this, 'partial_filter'), 1000, 1);
        add_filter('taxonomy_feed_link', array(&$this, 'partial_filter'), 1000, 1);
        add_filter('author_feed_link', array(&$this, 'partial_filter'), 1000, 1);
        add_filter('the_feed_link', array(&$this, 'partial_filter'), 1000, 1);

    }
}

if ($this->opt('email_from_name'))
    add_filter('wp_mail_from_name', array(&$this, 'email_from_name'));

if ($this->opt('email_from_address'))
    add_filter('wp_mail_from', array(&$this, 'email_from_address'));

if ($this->opt('hide_wp_login')) {
    add_action('site_url', array(&$this, 'add_login_key_to_action_from'), 101, 4);
    remove_action('template_redirect', 'wp_redirect_admin_locations', 1000);
    add_filter('login_url', array(&$this, 'add_key_login_to_url'), 101, 2);
    add_filter('logout_url', array(&$this, 'add_key_login_to_url'), 101, 2);
    add_filter('lostpassword_url', array(&$this, 'add_key_login_to_url'), 101, 2);
    add_filter('register', array(&$this, 'add_key_login_to_url'), 101, 2);

//since 4.5
    add_filter('comment_moderation_text', array(&$this, 'add_key_login_to_messages'), 101, 2);
    add_filter('comment_notification_text', array(&$this, 'add_key_login_to_messages'), 101, 2);

    add_filter('wp_logout', array(&$this, 'correct_logout_redirect'), 101);

    add_filter('wp_redirect', array(&$this, 'add_key_login_to_url'), 101, 2);
}

add_action('after_setup_theme', array(&$this, 'ob_starter'), -100001);
//add_action('shutdown',  array(&$this, 'do_shutdown'), 110);

// Fix hyper_cache problem!
if (WP_CACHE && function_exists('hyper_cache_sanitize_uri'))
    add_filter('cache_buffer', array(&$this, 'global_html_filter'), -100);

add_action('admin_enqueue_scripts', array($this, 'admin_css_js'));
add_action( 'wp_head', array( $this, 'front_css_js' ) );

if (function_exists('bp_is_current_component'))
    add_action('bp_uri', array($this, 'bp_uri'));

if ($this->opt('replace_wpnonce')) {
    if (isset($_GET['_nonce']))
        $_GET['_wpnonce'] = $_GET['_nonce'];

    if (isset($_POST['_nonce']))
        $_POST['_wpnonce'] = $_POST['_nonce'];

    $this->preg_replace_old[] = '/_wpnonce/';
    $this->preg_replace_new[] = '_nonce';
}

/**
 * @version 6.0
 * Disable XML RPC
 */
if ($this->opt('disable_xml_rpc')) {
    add_filter( 'xmlrpc_enabled', '__return_false' );
    // Disable X-Pingback to header
    add_filter('pings_open', '__return_false', PHP_INT_MAX);
}

/**
 * Create cron job schedule
 */
add_filter( 'cron_schedules', array($this, 'hmwp_cron_add_weekly') );

if (!wp_next_scheduled('hmwp_update_ips_to_server')) {
    wp_schedule_event(time(), 'weekly', 'hmwp_update_ips_to_server');
}
add_action('hmwp_update_ips_to_server', array($this, 'hmwp_update_ips_to_server_func'));
