<?php
/**
Plugin Name: WP Sitemap Page
Plugin URI: http://tonyarchambeau.com/
Description: Add a sitemap on any page/post using the simple shortcode [wp_sitemap_page]
<<<<<<< .mine
Version: 1.6.2
||||||| .r1142557
Version: 1.5.4
=======
Version: 1.6.1
>>>>>>> .r2114646
Author: Tony Archambeau
Author URI: http://tonyarchambeau.com/
Text Domain: wp-sitemap-page
Domain Path: /languages

Copyright 2014 Tony Archambeau
*/


// SECURITY : Exit if accessed directly
if ( !defined('ABSPATH') ) {
	exit;
}


// i18n
// OLD WAY : load_plugin_textdomain( 'wp_sitemap_page', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
add_action( 'plugins_loaded', 'wsp_load_textdomain' );

/**
 * Load plugin textdomain.
 * @since 1.5.2
 */
function wsp_load_textdomain() {
  load_plugin_textdomain( 'wp_sitemap_page', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}



/***************************************************************
 * Define
 ***************************************************************/

if ( !defined('WSP_USER_NAME') ) {
	define('WSP_USER_NAME', basename(dirname(__FILE__)) );
}
if ( !defined('WSP_USER_PLUGIN_DIR') ) {
	define('WSP_USER_PLUGIN_DIR', WP_PLUGIN_DIR .'/'. WSP_USER_NAME );
}
if ( !defined('WSP_USER_PLUGIN_URL') ) {
	define('WSP_USER_PLUGIN_URL', WP_PLUGIN_URL .'/'. WSP_USER_NAME );
}

if ( !defined('WSP_PLUGIN_NAME') ) {
	define('WSP_PLUGIN_NAME', 'wp_sitemap_page');
}
if ( !defined('WSP_VERSION') ) {
	define('WSP_VERSION', '1.3.0');
}
if ( !defined('WSP_DONATE_LINK') ) {
	define('WSP_DONATE_LINK', 'https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=FQKK22PPR3EJE&amp;lc=GB&amp;item_name=WP%20Sitemap%20Page&amp;item_number=wp%2dsitemap%2dpage&amp;currency_code=EUR&amp;bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted');
}
if (!defined('WSP_VERSION_NUM')) {
	define('WSP_VERSION_NUM', '1.3.0');
}


/***************************************************************
 * Install and uninstall
 ***************************************************************/


/**
 * Hooks for install
 */
if (function_exists('register_uninstall_hook')) {
	register_deactivation_hook(__FILE__, 'wsp_uninstall');
}


/**
 * Hooks for uninstall
 */
if ( function_exists('register_activation_hook')) {
	register_activation_hook(__FILE__, 'wsp_install');
}


/**
 * Install this plugin
 */
function wsp_install() {
	// Initialise the RSS footer and save it
	$wsp_posts_by_category = '<a href="{permalink}">{title}</a>';
	add_option( 'wsp_posts_by_category', $wsp_posts_by_category );
	
	// by default deactivate the ARCHIVE and AUTHOR
	add_option( 'wsp_exclude_cpt_archive', '1' );
	add_option( 'wsp_exclude_cpt_author', '1' );
}


/**
 * Uninstall this plugin
 */
function wsp_uninstall() {
	// Unregister an option
	delete_option( 'wsp_posts_by_category' );
	delete_option( 'wsp_exclude_pages' );
	delete_option( 'wsp_exclude_cpt_page' );
	delete_option( 'wsp_exclude_cpt_post' );
	delete_option( 'wsp_exclude_cpt_archive' );
	delete_option( 'wsp_exclude_cpt_author' );
	delete_option( 'wsp_add_nofollow' );
	delete_option( 'wsp_is_display_copyright' );
	delete_option( 'wsp_is_display_post_multiple_time' );
	delete_option( 'wsp_is_exclude_password_protected' );
	unregister_setting('wp-sitemap-page', 'wsp_posts_by_category');
}


/***************************************************************
 * UPGRADE
 ***************************************************************/

// Manage the upgrade to version 1.1.0
if (get_option('wsp_version_key') != WSP_VERSION_NUM) {
    // Add option
	
	// by default deactivate the ARCHIVE and AUTHOR
	add_option( 'wsp_exclude_cpt_archive', '1' );
	add_option( 'wsp_exclude_cpt_author', '1' );
	
    // Update the version value
    update_option('wsp_version_key', WSP_VERSION_NUM);
}


/***************************************************************
 * Menu + settings page
 ***************************************************************/


/**
 * Add menu on the Back-Office for the plugin
 */
function wsp_add_options_page() {
	if (function_exists('add_options_page')) {
		$page_title = __('WP Sitemap Page', 'wp_sitemap_page');
		$menu_title = __('WP Sitemap Page', 'wp_sitemap_page');
		$capability = 'administrator';
		$menu_slug = 'wp_sitemap_page';
		$function = 'wsp_settings_page'; // function that contain the page
		add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function );
	}
}
add_action('admin_menu', 'wsp_add_options_page');


/**
 * Add the settings page
 * 
 * @return boolean
 */
function wsp_settings_page() {
	$path = trailingslashit(dirname(__FILE__));
	
	if (!file_exists( $path . 'settings.php')) {
		return false;
	}
	require_once($path . 'settings.php');
}


/**
 * Additional links on the plugin page
 *
 * @param array $links
 * @param str $file
 * @return array
 */
function wsp_plugin_row_meta($links, $file) {
	if ($file == plugin_basename(__FILE__)) {
		$settings_page = 'wp_sitemap_page';
		$links[] = '<a href="options-general.php?page=' . $settings_page .'">' . __('Settings','wp_sitemap_page') . '</a>';
		$links[] = '<a href="' . WSP_DONATE_LINK . '">'.__('Donate', 'wp_sitemap_page').'</a>';
	}
	return $links;
}
add_filter('plugin_row_meta', 'wsp_plugin_row_meta',10,2);



/**
 * Manage the option when we submit the form
 */
function wsp_save_settings() {
	
	// Register the settings
	register_setting( 'wp-sitemap-page', 'wsp_posts_by_category' );
	register_setting( 'wp-sitemap-page', 'wsp_exclude_pages' );
	register_setting( 'wp-sitemap-page', 'wsp_exclude_cpt_page' );
	register_setting( 'wp-sitemap-page', 'wsp_exclude_cpt_post' );
	register_setting( 'wp-sitemap-page', 'wsp_exclude_cpt_archive' );
	register_setting( 'wp-sitemap-page', 'wsp_exclude_cpt_author' );
	register_setting( 'wp-sitemap-page', 'wsp_add_nofollow' );
	register_setting( 'wp-sitemap-page', 'wsp_is_display_copyright' );
	register_setting( 'wp-sitemap-page', 'wsp_is_display_post_multiple_time' );
	register_setting( 'wp-sitemap-page', 'wsp_is_exclude_password_protected' );
	
	// Get the CPT (Custom Post Type)
	$args = array(
		'public'   => true,
		'_builtin' => false
	);
	$post_types = get_post_types( $args, 'names' ); 
	
	// list all the CPT
	foreach ( $post_types as $post_type ) {
		
		// extract CPT object
		$cpt = get_post_type_object( $post_type );
		
		// register settings
		register_setting( 'wp-sitemap-page', 'wsp_exclude_cpt_'.$cpt->name );
	}
	
	// Get the Taxonomies
	$args = array(
		'public'   => true,
		'_builtin' => false
		);
	$taxonomies_names = get_taxonomies( $args );
	
	// list all the taxonomies
	foreach ( $taxonomies_names as $taxonomy_name ) {
		
		// register settings
		register_setting( 'wp-sitemap-page', 'wsp_exclude_taxonomy_'.$taxonomy_name );
	}
	
} 
add_action( 'admin_init', 'wsp_save_settings' );



/***************************************************************
 * Manage the option
 ***************************************************************/

/**
 * Fonction de callback
 * 
 * @param array $matches
 */
function wsp_manage_option( array $matches = array() ) {
	
	global $the_post_id;
	
	if (isset($matches[1])) {
		$key = strtolower( $matches[1] );
		
		switch ($key) {
			// Get the title of the post
			case 'title':
				return get_the_title($the_post_id);
				break;
			
			// Get the URL of the post
			case 'permalink':
				return get_permalink($the_post_id);
				break;
			
			// Get the year of the post
			case 'year':
				return get_the_time('Y', $the_post_id);
				break;
			
			// Get the month of the post
			case 'monthnum':
				return get_the_time('m', $the_post_id);
				break;
			
			// Get the day of the post
			case 'day':
				return get_the_time('d', $the_post_id);
				break;
			
			// Get the day of the post
			case 'hour':
				return get_the_time('H', $the_post_id);
				break;
			
			// Get the day of the post
			case 'minute':
				return get_the_time('i', $the_post_id);
				break;
			
			// Get the day of the post
			case 'second':
				return get_the_time('s', $the_post_id);
				break;
			
			// Get the day of the post
			case 'post_id':
				return $the_post_id;
				break;
			
			// Get the day of the post
			case 'category':
				$categorie_info = get_the_category($the_post_id);
				if (!empty($categorie_info)) {
					$categorie_info = current($categorie_info);
					//return print_r($categorie_info,1);
					return (isset($categorie_info->name) ? $categorie_info->name : '');
				}
				return '';
				break;
			
			// default value
			default:
				if (isset($matches[0])) {
					return $matches[0];
				}
				return false;
				break;
		}
		
	}
	return false;
}


/***************************************************************
 * Tabs
 ***************************************************************/

/**
 * Get the current tab
 * 
 * @return Ambigous <string, mixed>|string
 */
function wsp_get_current_tab() {
	if (isset($_GET['tab'])) {
		return esc_html($_GET['tab']);
	} else {
		return 'main';
	}
}


/**
 * Display the tabs
 */
function wsp_show_tabs() {
	global $wp_db_version;
	
	// Get the current tab
	$current_tab = wsp_get_current_tab();
	
	// All tabs
	$tabs = array();
	$tabs['main']    = __('Settings', 'wp_sitemap_page');
	$tabs['about']   = __('How to use', 'wp_sitemap_page');
	
	// Generate the tab links
	$tab_links = array();
	foreach ($tabs as $tab_k => $tab_name) {
		$tab_curent = ($tab_k === $current_tab ? ' nav-tab-active' : '' );
		$tab_url = '?page=' . WSP_PLUGIN_NAME .'&amp;tab='.$tab_k;
		$tab_links[] = '<a class="nav-tab'.$tab_curent.'" href="'.$tab_url.'">'.$tab_name.'</a>';
	}
	
	// Since the 25 oct. 2010 WordPress include the tabs (in CSS)
	// The 25 oct. 2010 = WordPress version was "3.1-alpha"
	if ( $wp_db_version >= 15477 ) {
		// Tabs in CSS
		?>
		<h2 class="nav-tab-wrapper">
			<?php echo implode("\n", $tab_links); ?>
		</h2>
		<?php
	} else {
		// Tabs without CSS (instead, separate links with "|")
		?>
		<div>
			<?php echo implode(' | ', $tab_links); ?>
		</div>
		<?php
	}
	
	return;
}


/***************************************************************
 * Generate the sitemap
 ***************************************************************/


/**
 * Shortcode function that generate the sitemap
 * Use like this : [wp_sitemap_page]
 * 
 * @param $atts
 * @param $content
 * @return str $return
 */
function wsp_wp_sitemap_page_main_func( $atts, $content=null ) {
	return '<div class="wsp-container">'.wsp_wp_sitemap_page_func( $atts, $content ).'</div>';
}
add_shortcode( 'wp_sitemap_page', 'wsp_wp_sitemap_page_main_func' );


/**
 * Main function to call all the various features
 * 
 * @param $atts
 * @param $content
 * @return str $return
 */
function wsp_wp_sitemap_page_func( $atts, $content=null ) {
	
	// init
	$return = '';
	
	// display only some CPT
	// the "only" parameter always is higher than "exclude" options
	$only_cpt = (isset($atts['only']) ? sanitize_text_field($atts['only']) : '');
	
	// display or not the title
	$get_display_title = (isset($atts['display_title']) ? sanitize_text_field($atts['display_title']) : 'true');
	$is_title_displayed = ( $get_display_title=='false' ? false : true );
	
	// display or not the category title "category:"
	$get_display_category_title_wording = (isset($atts['display_category_title_wording']) ? sanitize_text_field($atts['display_category_title_wording']) : 'true');
	$is_category_title_wording_displayed = ( $get_display_category_title_wording=='false' ? false : true );
	
	// get only the private page/post ...
	$only_private = (isset($atts['only_private']) ? sanitize_text_field($atts['only_private']) : 'false');
	$is_get_only_private = ( $only_private=='true' ? true : false );
	
	// get the kind of sort
	$sort = (isset($atts['sort']) ? sanitize_text_field($atts['sort']) : null);
	$order = (isset($atts['order']) ? sanitize_text_field($atts['order']) : null);
	
	// Exclude some pages (separated by a coma)
	$wsp_exclude_pages        = trim(get_option('wsp_exclude_pages'));
	$wsp_add_nofollow         = get_option('wsp_add_nofollow');
	$wsp_is_display_copyright = get_option('wsp_is_display_copyright');
	$wsp_is_display_post_multiple_time = get_option('wsp_is_display_post_multiple_time');
	$wsp_is_exclude_password_protected = get_option('wsp_is_exclude_password_protected');
	
	// Determine if the posts should be displayed multiple time if it is in multiple category
	$display_post_only_once = ($wsp_is_display_post_multiple_time==1 ? false : true );
	
	// Determine if the posts should be displayed multiple time if it is in multiple category
	$display_nofollow = ($wsp_add_nofollow==1 ? true : false );
	
	$copyright_link = '';
	// add a copyright link
	if ($wsp_is_display_copyright==1) {
		$copyright_link = '<p><a href="http://wordpress.org/plugins/wp-sitemap-page/">'.__('Powered by "WP Sitemap Page"', 'wp_sitemap_page').'</a></p>';
	}
	
	
	// Exclude pages, posts and CTPs protected by password
	if ($wsp_is_exclude_password_protected==1) {
		
		global $wpdb;
		
		// Obtain the password protected content
		$sql = 'SELECT ID FROM '.$wpdb->posts.' WHERE post_status = \'publish\' AND post_password <> \'\' ';
		$password_pages = $wpdb->get_col($sql);
		
		// add to the other if not empty
		if (!empty($password_pages)) {
			// convert array to string
			$exclude_pages = implode(',', $password_pages);
			
			// Add the excluded page to the other protected page
			if (!empty($wsp_exclude_pages)) {
				$wsp_exclude_pages .= ','.$exclude_pages;
			} else {
				$wsp_exclude_pages = $exclude_pages;
			}
		}
	}
	
	// check if the attribute "only" is used
	switch ($only_cpt) {
		// display only PAGE
		case 'page':
			return wsp_return_content_type_page($is_title_displayed, $is_get_only_private, $display_nofollow, $wsp_exclude_pages, $sort).$copyright_link;
			break;
		// display only POST
		case 'post':
			return wsp_return_content_type_post($is_title_displayed, $display_nofollow, $display_post_only_once, $is_category_title_wording_displayed, 
												$wsp_exclude_pages, $sort, $sort, $order).$copyright_link;
			break;
		// display only ARCHIVE
		case 'archive':
			return wsp_return_content_type_archive($is_title_displayed, $display_nofollow).$copyright_link;
			break;
		// display only AUTHOR
		case 'author':
			return wsp_return_content_type_author($is_title_displayed, $display_nofollow, $sort).$copyright_link;
			break;
		// display only CATEGORY
		case 'category':
			return wsp_return_content_type_categories($is_title_displayed, $display_nofollow, $sort).$copyright_link;
			break;
		// display only TAGS
		case 'tag':
			return wsp_return_content_type_tag($is_title_displayed, $display_nofollow).$copyright_link;
			break;
		// empty
		case '':
			// nothing but do
			break;
		default:
			// check if it's the name of a CPT
			
			// extract CPT object
			$cpt = get_post_type_object( $only_cpt );
			
			if ( !empty($cpt) ) {
				return wsp_return_content_type_cpt_items( $is_title_displayed, $display_nofollow, $cpt, $only_cpt, $wsp_exclude_pages, $sort );
			}
			
			// check if it's a taxonomy
			$taxonomy_obj = get_taxonomy( $only_cpt );
			
			if ( !empty($taxonomy_obj) ) {
				return wsp_return_content_type_taxonomy_items($is_title_displayed, $display_nofollow, $taxonomy_obj, $wsp_exclude_pages);
			}
			// end
	}
	
	
	//===============================================
	// Otherwise, display traditionnal sitemap
	//===============================================
	
	// exclude some custome post type (page, post, archive or author)
	// value : 0=do not exclude ; 1=exclude
	$wsp_exclude_cpt_page    = get_option('wsp_exclude_cpt_page');
	$wsp_exclude_cpt_post    = get_option('wsp_exclude_cpt_post');
	$wsp_exclude_cpt_archive = get_option('wsp_exclude_cpt_archive');
	$wsp_exclude_cpt_author  = get_option('wsp_exclude_cpt_author');
	
	
	// List the PAGES
	if ( empty($wsp_exclude_cpt_page) ) {
		$return .= wsp_return_content_type_page($is_title_displayed, $is_get_only_private, $display_nofollow, $wsp_exclude_pages, $sort);
	}
	
	// List the POSTS by CATEGORY
	if ( empty($wsp_exclude_cpt_post) ) {
		$return .= wsp_return_content_type_post($is_title_displayed, $display_nofollow, $display_post_only_once, $is_category_title_wording_displayed, 
												$wsp_exclude_pages, $sort, $sort, $order);
	}
	
	// List the CPT
	$return .= wsp_return_content_type_cpt_lists($is_title_displayed, $display_nofollow, $wsp_exclude_pages);
	
	// List the Taxonomies
	$return .= wsp_return_content_type_taxonomies_lists($is_title_displayed, $display_nofollow, $wsp_exclude_pages);
	
	// List the ARCHIVES
	if ( empty($wsp_exclude_cpt_archive) ) {
		$return .= wsp_return_content_type_archive($is_title_displayed, $display_nofollow);
	}
	
	// List the AUTHORS
	if ( empty($wsp_exclude_cpt_author) ) {
		$return .= wsp_return_content_type_author($is_title_displayed, $display_nofollow, $sort);
	}
	
	// return the content
	return $return.$copyright_link;
}


/**
 * Return list of pages
 * 
 * @param bool $is_title_displayed
 * @param bool $is_get_only_private
 * @param bool $display_nofollow
 * @param array $wsp_exclude_pages
 * @param str $sort
 * @return str $return
 */
function wsp_return_content_type_page($is_title_displayed = true, $is_get_only_private = false, $display_nofollow = false, $wsp_exclude_pages = array(), $sort = null) {
	
	// init
	$return = '';
	
	if ($display_nofollow==true) {
		add_filter('wp_list_pages', 'wsp_add_no_follow_to_links');
	}
	
	// define the way the pages should be displayed
	$args = array();
	$args['title_li'] = '';
	$args['echo']     = '0';
	
	// change the sort
	if ($sort!==null) {
		$args['sort_column'] = $sort;
	}
	
	// exclude some pages ?
	if (!empty($wsp_exclude_pages)) {
		$args['exclude'] = $wsp_exclude_pages;
	}
	
	// get only the private content
	if ($is_get_only_private==true) {
		$args['post_status'] = 'private';
	}
	
	// get data
	$list_pages = wp_list_pages($args);
	
	// check it's not empty
	if (empty($list_pages)) {
		return '';
	}
	
	// add content
	if ($is_title_displayed==true) {
		$return .= '<h2 class="wsp-pages-title">'.__('Pages', 'wp_sitemap_page').'</h2>'."\n";
	}
	$return .= '<ul class="wsp-pages-list">'."\n";
	$return .= $list_pages;
	$return .= '</ul>'."\n";
	
	// return content
	return apply_filters( 'wsp_pages_return', $return );
}


/**
 * Return list of posts in the categories
 * 
 * @param bool $is_title_displayed
 * @param bool $display_nofollow
 * @param bool $display_post_only_once
 * @param bool $is_category_title_wording_displayed
 * @param array $wsp_exclude_pages
 * @param str $sort_categories
 * @param str $sort
 * @param str $order
 * @return str $return
 */
function wsp_return_content_type_post( $is_title_displayed=true, $display_nofollow=false, $display_post_only_once, $is_category_title_wording_displayed=true, 
										$wsp_exclude_pages=array(), $sort_categories=null, $sort=null, $order=null ) {
	
	// init
	$return = '';
	
	// args
	$args = array();
	
	// change the sort order
	if ($sort_categories!==null) {
		$args['orderby'] = $sort_categories;
	}
	
	// Get the categories
	$cats = get_categories( $args );
	
	// check it's not empty
	if (empty($cats)) {
		return '';
	}
	
	// Get the categories
	$cats = wsp_generateMultiArray($cats);
	
	// add content
	if ($is_title_displayed==true) {
		$return .= '<h2 class="wsp-posts-title">'.__('Posts by category', 'wp_sitemap_page').'</h2>'."\n";
	}
	$return .= wsp_htmlFromMultiArray($cats, true, $display_post_only_once, $is_category_title_wording_displayed, 
									$display_nofollow, $wsp_exclude_pages, $sort, $order);
	
	// return content
	return apply_filters( 'wsp_posts_return', $return );
}


/**
 * Return list of posts in the categories
 * 
 * @param bool $is_title_displayed
 * @return str $return
 */
function wsp_return_content_type_categories( $is_title_displayed=true, $display_nofollow=false, $sort=null ) {
	
	// init
	$return = '';
	
	// args
	$args = array();
	
	// change the sort order
	if ($sort!==null) {
		$args['orderby'] = $sort;
	}
	
	// Get the categories
	$cats = get_categories( $args );
	
	// check it's not empty
	if (empty($cats)) {
		return '';
	}
	
	// display a nofollow attribute ?
	$attr_nofollow = ($display_nofollow==true ? ' rel="nofollow"' : '');
	
	// add content
	if ($is_title_displayed==true) {
		$return .= '<h2 class="wsp-categories-title">'.__('Categories', 'wp_sitemap_page').'</h2>'."\n";
	}
	$return .= '<ul class="wsp-categories-list">'."\n";
	foreach ($cats as $cat) {
		$return .= "\t".'<li><a href="'.get_category_link($cat->cat_ID).'"'.$attr_nofollow.'>'.$cat->name.'</a></li>'."\n";
	}
	$return .= '</ul>'."\n";
	
	// return content
	return apply_filters( 'wsp_categories_return', $return );
}


/**
 * Return list of posts in the categories
 * 
 * @param bool $is_title_displayed
 * @return str $return
 */
function wsp_return_content_type_tag($is_title_displayed=true, $display_nofollow=false) {
	
	// init
	$return = '';
	
	// args
	$args = array();
	
	// Get the categories
	$posttags = get_tags( $args );
	
	// check it's not empty
	if (empty($posttags)) {
		return '';
	}
	
	// display a nofollow attribute ?
	$attr_nofollow = ($display_nofollow==true ? ' rel="nofollow"' : '');
	
	// add content
	if ($is_title_displayed==true) {
		$return .= '<h2 class="wsp-tags-title">'.__('Tags', 'wp_sitemap_page').'</h2>'."\n";
	}
	$return .= '<ul class="wsp-tags-list">'."\n";
	foreach($posttags as $tag) {
		$return .= "\t".'<li><a href="'.get_tag_link($tag->term_id).'"'.$attr_nofollow.'>'.$tag->name.'</a></li>'."\n";
	}
	$return .= '</ul>'."\n";
	
	// return content
	return apply_filters( 'wsp_tags_return', $return );
}


/**
 * Return list of archives
 * 
 * @param bool $is_title_displayed
 * @return str $return
 */
function wsp_return_content_type_archive($is_title_displayed=true, $display_nofollow=false) {
	
	// init
	$return = '';
	
	// define the way the pages should be displayed
	$args = array();
	$args['echo'] = 0;
	
	// get data
	$list_archives = wp_get_archives($args);
	
	// check it's not empty
	if (empty($list_archives)) {
		return '';
	}
	
	// display a nofollow attribute ?
	$attr_nofollow = ($display_nofollow==true ? ' rel="nofollow"' : '');
	
	// add content
	if ($is_title_displayed==true) {
		$return .= '<h2 class="wsp-archives-title">'.__('Archives', 'wp_sitemap_page').'</h2>'."\n";
	}
	$return .= '<ul class="wsp-archives-list">'."\n";
	$return .= $list_archives;
	$return .= '</ul>'."\n";
	
	// return content
	return apply_filters( 'wsp_archives_return', $return );
}


/**
 * Return list of authors
 * 
 * @param bool $is_title_displayed
 * @param bool $display_nofollow
 * @param text $sort
 * @return str $return
 */
function wsp_return_content_type_author( $is_title_displayed=true, $display_nofollow=false, $sort=null ) {
	
	// init
	$return = '';
	
	// define the way the pages should be displayed
	$args = array();
	$args['echo'] = 0;
	
	// change the sort order
	if ($sort!==null) {
		$args['orderby'] = $sort;
	}
	
	// get data
	$list_authors = wp_list_authors($args);
	
	// check it's not empty
	if (empty($list_authors)) {
		return '';
	}
	
	// display a nofollow attribute ?
	$attr_nofollow = ($display_nofollow==true ? ' rel="nofollow"' : '');
	
	// add content
	if ($is_title_displayed==true) {
		$return .= '<h2 class="wsp-authors-title">'.__('Authors', 'wp_sitemap_page').'</h2>'."\n";
	}
	$return .= '<ul class="wsp-authors-list">'."\n";
	$return .= $list_authors;
	$return .= '</ul>'."\n";
	
	// return content
	return apply_filters( 'wsp_authors_return', $return );
}


/**
 * Return list of all other custom post type
 * 
 * @param bool $is_title_displayed
 * @param bool $display_nofollow
 * @param str $wsp_exclude_pages
 * @return str $return
 */
function wsp_return_content_type_cpt_lists( $is_title_displayed=true, $display_nofollow=false, $wsp_exclude_pages ) {
	
	// init
	$return = '';
	
	// define the main arguments
	$args = array(
		'public'   => true,
		'_builtin' => false
	);
	
	// Get the CPT (Custom Post Type)
	$post_types = get_post_types( $args, 'names' ); 
	
	// check it's not empty
	if (empty($post_types)) {
		return '';
	}
	
	// list all the CPT
	foreach ( $post_types as $post_type ) {
		
		// extract CPT object
		$cpt = get_post_type_object( $post_type );
		
		// Is this CPT already excluded ?
		$wsp_exclude_cpt = get_option('wsp_exclude_cpt_'.$cpt->name);
		
		if ( empty($wsp_exclude_cpt) ) {
			$return .= wsp_return_content_type_cpt_items( $is_title_displayed, $display_nofollow, $cpt, $post_type, $wsp_exclude_pages );
		}
	}
	
	// return content
	return $return;
}


/**
 * Return list of all other custom post type
 * 
 * @param bool $is_title_displayed
 * @param bool $display_nofollow
 * @param str $cpt
 * @param str $post_type
 * @param str $wsp_exclude_pages
 * @param str $sort
 * @return str $return
 */
function wsp_return_content_type_cpt_items( $is_title_displayed=true, $display_nofollow=false, $cpt, $post_type, $wsp_exclude_pages, $sort=null ) {
	
	// init
	$return = '';
	
	// List the pages
	$list_pages = '';
	
	// define the way the pages should be displayed
	$args = array();
	$args['post_type'] = $post_type;
	$args['posts_per_page'] = 999999;
	$args['suppress_filters'] = 0;
	
	// exclude some pages ?
	if (!empty($wsp_exclude_pages)) {
		$args['exclude'] = $wsp_exclude_pages;
	}
	
	// change the sort order
	if ($sort!==null) {
		$args['orderby'] = $sort;
	}
	
	// Query to get the current custom post type
	$posts_cpt = get_posts( $args );
	
	// display a nofollow attribute ?
	$attr_nofollow = ($display_nofollow==true ? ' rel="nofollow"' : '');
	
	// List all the results
	if ( !empty($posts_cpt) ) {
		foreach( $posts_cpt as $post_cpt ) {
			$list_pages .= '<li><a href="'.get_permalink( $post_cpt->ID ).'"'.$attr_nofollow.'>'.$post_cpt->post_title.'</a></li>'."\n";
		}
	}
	
	// Return the data (if it exists)
	if (!empty($list_pages)) {
		if ($is_title_displayed==true) {
			$return .= '<h2 class="wsp-'.$post_type.'s-title">' . $cpt->label . '</h2>'."\n";
		}
		$return .= '<ul class="wsp-'.$post_type.'s-list">'."\n";
		$return .= $list_pages;
		$return .= '</ul>'."\n";
	}
	
	// return content
	return apply_filters( 'wsp_cpts_return', $return );
}


/**
 * Return list of all other custom post type
 * 
 * @param bool $is_title_displayed
 * @param bool $display_nofollow
 * @param str $wsp_exclude_pages
 * @return str $return
 */
function wsp_return_content_type_taxonomies_lists($is_title_displayed=true, $display_nofollow=false, $wsp_exclude_pages) {
	
	// init
	$return = '';
	
	$args = array(
		'public'   => true,
		'_builtin' => false
		);
	$taxonomies_names = get_taxonomies( $args );
	
	// check it's not empty
	if (empty($taxonomies_names)) {
		return '';
	}
	
	// list all the taxonomies
	foreach ( $taxonomies_names as $taxonomy_name ) {
		
		// Extract
		$taxonomy_obj = get_taxonomy( $taxonomy_name );
		
		// Is this taxonomy already excluded ?
		$wsp_exclude_taxonomy = get_option('wsp_exclude_taxonomy_'.$taxonomy_name);
		
		if ( empty($wsp_exclude_taxonomy) ) {
			$return .= wsp_return_content_type_taxonomy_items( $is_title_displayed, $display_nofollow, $taxonomy_obj, $wsp_exclude_taxonomy );
		}
	}
	
	// return content
	return $return;
}


/**
 * Return list of all other taxonomies
 * 
 * @param bool $is_title_displayed
 * @param bool $display_nofollow
 * @param object $taxonomy_obj
 * @param str $wsp_exclude_pages
 * @return str $return
 */
function wsp_return_content_type_taxonomy_items( $is_title_displayed=true, $display_nofollow=false, $taxonomy_obj, $wsp_exclude_taxonomy ) {
	
	// init
	$return = '';
	
	// List the pages
	$list_pages = '';
	
	// get some data
	$taxonomy_name = $taxonomy_obj->name;
	$taxonomy_label = $taxonomy_obj->label;
	
	// init variable to get terms of a taxonomy
	$taxonomies = array( $taxonomy_name );
	$args = array();
	
	// get the terms of this taxonomy
	$terms = get_terms($taxonomies, $args);
	
	// display a nofollow attribute ?
	$attr_nofollow = ($display_nofollow==true ? ' rel="nofollow"' : '');
	
	// List all the results
	if ( !empty($terms) ) {
		foreach( $terms as $terms_obj ) {
			$list_pages .= '<li><a href="'.get_term_link( $terms_obj ).'"'.$attr_nofollow.'>'.$terms_obj->name.'</a></li>'."\n";
		}
	}
	
	// Return the data (if it exists)
	if (!empty($list_pages)) {
		if ($is_title_displayed==true) {
			$return .= '<h2 class="wsp-'.$taxonomy_name.'s-title">' . $taxonomy_label . '</h2>'."\n";
		}
		$return .= '<ul class="wsp-'.$taxonomy_name.'s-list">'."\n";
		$return .= $list_pages;
		$return .= '</ul>'."\n";
	}
	
	// return content
	return apply_filters( 'wsp_taxonomies_return', $return );
}


/**
 * Generate a multidimensional array from a simple linear array using a recursive function
 * 
 * @param array $arr
 * @param int $parent
 * @return array $pages
 */
function wsp_generateMultiArray( array $arr = array() , $parent = 0 ) {
	
	// check if not empty
	if (empty($arr)) {
		return array();
	}
	
	$pages = array();
	// go through the array
	foreach($arr as $k => $page) {
		if ($page->parent == $parent) {
			$page->sub = isset($page->sub) ? $page->sub : wsp_generateMultiArray($arr, $page->cat_ID);
			$pages[] = $page;
		}
	}
	
	return $pages;
}


/**
 * Display the multidimensional array using a recursive function
 * 
 * @param array $nav
 * @param bool $useUL
 * @param bool $display_post_only_once
 * @param bool $display_nofollow
 * @param array $wsp_exclude_pages
 * @param text $sort
 * @param text $order
 * @return str $html
 */
function wsp_htmlFromMultiArray( array $nav = array() , $useUL = true, $display_post_only_once = true, $is_category_title_wording_displayed = true, 
								$display_nofollow = false, $wsp_exclude_pages = array(), $sort=null, $order=null ) {
	
	// check if not empty
	if (empty($nav)) {
		return '';
	}
	
	$html = '';
	if ($useUL === true) {
		$html .= '<ul class="wsp-posts-list">'."\n";
	}
	
	// display a nofollow attribute ?
	$attr_nofollow = ($display_nofollow==true ? ' rel="nofollow"' : '');
	
	// List all the categories
	foreach ($nav as $page) {
		// define category link
		$category_link = '<a href="'.get_category_link($page->cat_ID).'"'.$attr_nofollow.'>'.$page->name.'</a>';
		// define the text to display for the title of the category
		if ($is_category_title_wording_displayed) {
			$category_link_display = sprintf( __('Category: %1$s', 'wp_sitemap_page'), $category_link );
		} else {
			$category_link_display = $category_link;
		}
		$html .= "\t".'<li><strong class="wsp-category-title">'.$category_link_display.'</strong>'."\n";
		
		$post_by_cat = wsp_displayPostByCat($page->cat_ID, $display_post_only_once, $display_nofollow, $wsp_exclude_pages, $sort, $order);
		
		// List of posts for this category
		$category_recursive = '';
		if (!empty($page->sub)) {
			// Use recursive function to get the childs categories
			$category_recursive = wsp_htmlFromMultiArray( $page->sub, false, $display_post_only_once, $is_category_title_wording_displayed, 
														$display_nofollow, $wsp_exclude_pages, $sort, $order );
		}
		
		// display if it exist
		if ( !empty($post_by_cat) || !empty($category_recursive) ) {
			$html .= '<ul class="wsp-posts-list">';
		}
		if ( !empty($post_by_cat) ) {
			$html .= $post_by_cat;
		}
		if ( !empty($category_recursive) ) {
			$html .= $category_recursive;
		}
		if ( !empty($post_by_cat) || !empty($category_recursive) ) {
			$html .= '</ul>';
		}
		
		$html .= '</li>'."\n";
	}
	
	if ($useUL === true) {
		$html .= '</ul>'."\n";
	}
	return $html;
}


/**
 * Display the multidimensional array using a recursive function
 * 
 * @param int $cat_id
 * @param bool $display_post_only_once
 * @param bool $display_nofollow
 * @param array $wsp_exclude_pages
 * @param text $sort
 * @param text $order
 * @return str $html
 */
function wsp_displayPostByCat( $cat_id, $display_post_only_once=true, $display_nofollow=false, $wsp_exclude_pages=array(), $sort=null, $order=null ) {
	
	global $the_post_id;
	
	// init
	$html = '';
	
	// define the way the pages should be displayed
	$args = array();
	$args['numberposts'] = 999999;
	$args['cat'] = $cat_id;
	
	// exclude some pages ?
	if (!empty($wsp_exclude_pages)) {
		$args['exclude'] = $wsp_exclude_pages;
	}
	
	// change the sort order
	if ($sort!==null) {
		$args['orderby'] = $sort;
	}
	if ($order!==null) {
		$args['order'] = $order;
	}
	
	// List of posts for this category
	$the_posts = get_posts( $args );
	
	// check if not empty
	if (empty($the_posts)) {
		return '';
	}
	
	// display a nofollow attribute ?
	$attr_nofollow = ($display_nofollow==true ? ' rel="nofollow"' : '');
	
	// determine the code to place in the textarea
	$wsp_posts_by_category = get_option('wsp_posts_by_category');
	if ( $wsp_posts_by_category === false ) {
		// this option does not exists
		$wsp_posts_by_category = sprintf(__('<a href="{permalink}"%1$s>{title}</a> ({monthnum}/{day}/{year})', 'wp_sitemap_page'), $attr_nofollow);
		
		// save this option
		add_option( 'wsp_posts_by_category', $wsp_posts_by_category );
	}
	
	// list the posts
	foreach ( $the_posts as $the_post ) {
		// Display the line of a post
		$get_category = get_the_category($the_post->ID);
		
		// Display the post only if it is on the deepest category
		if ( $display_post_only_once==false || ($display_post_only_once==true && $get_category[0]->cat_ID == $cat_id) ) {
			
			// get post ID
			$the_post_id = $the_post->ID;
			
			// replace the ID by the real value
			$html .= "\t\t".'<li class="wsp-post">'
				.preg_replace_callback( '#\{(.*)\}#Ui', 'wsp_manage_option', $wsp_posts_by_category)
				.'</li>'."\n";
		}
	}
	
	return $html;
}


/**
 * Add nofollow attribute to the links of the wp_list_pages() functions
 * 
 * @param str $output content
 * @return str
 */
function wsp_add_no_follow_to_links($output) {
	//return wp_rel_nofollow($output);
	return str_replace('<a href=', '<a rel="nofollow" href=',  $output);
}

