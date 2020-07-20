<?php


global $wp_rewrite, $wp, $wp_roles, $wp_query, $current_user, $wp_version;
load_plugin_textdomain(self::slug, FALSE, self::dir . '/lang/');
//*deprected file.

//todo:RewriteRule ^([_0-9a-zA-Z-]+/)?panel/(.*) $1wp-admin/$2 [QSA,L] you also need to change wp-includes/ms-default-contsant line 69



/*
if (!is_admin()) {
echo 'ffff blog_path: '.  $this->blog_path .' sub_folder:'. $this->sub_folder ;
echo "\n".'testcontent:'. get_option('test');
} */

if ($wp_roles && is_admin()) {
    $wp_roles->add_cap('administrator', self::slug . '_trusted');
    if ($this->opt('trusted_user_roles')) {
        foreach ($this->opt('trusted_user_roles') as $trusted_role)
            $wp_roles->add_cap($trusted_role, self::slug . '_trusted');
    }
}

if ($this->opt('login_query'))
    $login_query = $this->opt('login_query');
else
    $login_query = 'hide_my_wp';

if ($this->opt('admin_key'))
    $this->trust_key = '?' . $this->short_prefix . $login_query . '=' . $this->opt('admin_key');
else
    $this->trust_key = '';

$is_trusted = false;
$is_scanmywp = false;

if (current_user_can(self::slug . '_trusted') || (isset($_GET[$login_query]) && $_GET[$login_query] == $this->opt('admin_key')))
    $is_trusted = true;

//integration with scan my wp server
if($this->opt('enable_smwp_server') && class_exists('Scanmywp') && isset($_SERVER['REMOTE_ADDR']) &&  gethostbyaddr($_SERVER['REMOTE_ADDR']) == 'api.scanmywp.com') {
    $is_trusted = true; 
    $is_scanmywp = true;
}

$new_admin_path = (trim($this->opt('new_admin_path'), ' /')) ? trim($this->opt('new_admin_path'), ' /') : 'wp-admin';

if (trim($this->opt('new_admin_path'), ' /') && trim($this->opt('new_admin_path'), ' /') != 'wp-admin') {
    $_SERVER['REQUEST_URI'] = $this->replace_admin_url($_SERVER['REQUEST_URI']);
    add_filter('admin_url', array(&$this, 'replace_admin_url'), 100, 3);
    add_filter('network_admin_url', array(&$this, 'replace_admin_url'), 100, 2);
}

if (current_user_can('activate_plugins'))
    setcookie("hmwp_can_deactivate", preg_replace("/[^a-zA-Z]/", "", substr(NONCE_SALT, 0, 8)), time() + 3600, null, null, null, true);
elseif (isset($_COOKIE['hmwp_can_deactivate']))
    setcookie("hmwp_can_deactivate","", time() - 3600, null, null, null, true);

if ($this->opt('remove_ver_scripts')) {
    add_filter('style_loader_src', array(&$this, 'remove_ver_scripts'), 9999);
    add_filter('script_loader_src', array(&$this, 'remove_ver_scripts'), 9999);
}

if (defined( 'W3TC' )) {
    if (class_exists('W3TC\Dispatcher')) {
        $config = W3TC\Dispatcher::config();
        if ($config->get_boolean('minify.enabled')){
            if ($config->get_boolean('minify.auto')) {

                add_filter('w3tc_minify_before', array(&$this, 'w3tc_minify_before'), 1000, 1);
                add_filter('w3tc_minify_processed', array(&$this, 'w3tc_minify_after'), 1000, 1);
            }

            if (!$config->get_boolean( 'minify.rewrite' )) {
                if (isset($_REQUEST['w_minify'])) {
                    $_REQUEST['w3tc_minify'] = $_REQUEST['w_minify'];
                    unset($_REQUEST['w_minify']);
                }
                $this->replace_old[] = '?w3tc_minify=';
                $this->replace_new[] = '?w_minify=';
            }
        }
    }
}



if ($this->opt('remove_other_meta')) {//and emojis and rename embed!
    //From https://geek.hellyer.kiwi/plugins/disable-emojis/  by Ryan Hellyer
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

    add_filter( 'tiny_mce_plugins', array(&$this, 'tinymce_emoji'));

    if ($this->is_permalink() && $this->opt('new_include_path') && $this->opt('new_include_path')!= 'wp-includes') {
    	$prefix ='';
		if (is_multisite() && $this->is_subdir_mu)
    		$prefix =  $this->blog_path;
        $new_include_path = trim($this->opt('new_include_path'), '/ ');
        $this->auto_replace_urls[]=$prefix.'wp-includes/js/wp-embed.min.js=='. $new_include_path .'/js/embed.min.js';
    }
}

add_action( 'wp_enqueue_scripts', array(&$this, 'styles_scripts') , 0);

if ($this->opt('remove_default_description'))
    add_filter('get_bloginfo_rss', array(&$this, 'remove_default_description'));


if ($this->opt('nice_search_redirect') && $this->is_permalink())
    add_action('template_redirect', array(&$this, 'nice_search_redirect'));


//prioty 1 let other plugin add something to it. or delte it entirely.
if ($this->opt('remove_menu_class')) {
    add_filter('nav_menu_css_class', array(&$this, 'remove_menu_class'), 9);
    add_filter('nav_menu_item_id', array(&$this, 'remove_menu_class'), 9);
    add_filter('page_css_class', array(&$this, 'remove_menu_class'), 9);
}

if ($this->opt('remove_body_class'))
    add_filter('body_class', array(&$this, 'body_class_filter'), 9);

if ($this->opt('clean_post_class'))
    add_filter('post_class', array(&$this, 'post_class_filter'), 9);

if ($this->opt('hide_admin_bar') && !$is_trusted)
    add_filter('show_admin_bar', '__return_false');


if (version_compare( $wp_version, '4.7', '>=') && $this->opt('api_disable')){
    add_filter('rest_api_init', array(&$this, 'block_access'));
    $this->top_replace_old[]="<link rel='https://api.w.org/' href='http://127.0.0.1/wp39/wp-json/' />";
    $this->top_replace_new[]=" ";
}

if (version_compare( $wp_version, '4.7', '>=') && !$this->opt('api_disable') && trim($this->opt('api_base'),' /') && $this->opt('api_base') !='wp-json') {
    if(!(version_compare( $wp_version, '5.0', '>=') && $is_trusted)) {
        $this->top_replace_old[]=" rel='https://api.w.org/'";
        $this->top_replace_new[]=" ";
        add_filter('rest_url_prefix', create_function('', 'return "' . $this->opt('api_base') . '";'));
    }
    
}


$new_api_query = trim($this->opt('api_query'), ' /');

if (version_compare( $wp_version, '4.7', '>=') && !$this->opt('api_disable') && $new_api_query != 'rest_route' && !is_admin() && !$this->is_permalink()) {
    if (isset($_GET['rest_route']))
        unset($_GET['rest_route']);

    $wp->add_query_var($new_api_query);

    if (isset($_GET[$new_api_query]))
        $_GET['rest_route'] = $_GET[$new_api_query];

    $this->top_replace_old[]=" rel='https://api.w.org/'";
    $this->top_replace_new[]=" ";

    add_filter('rest_url', create_function('$url', 'return str_replace("rest_route=", "'.$new_api_query.'=", $url);'), 1000, 1);
}

if ($this->opt('auto_config_plugins') && $this->is_permalink()){
    include_once('lib/auto_config.php');
}


$feed_base = trim($this->opt('feed_base'), '/ ');
if ($this->opt('disable_canonical_redirect') || ($feed_base && $this->h->str_contains($_SERVER['REQUEST_URI'], $feed_base, false)))
    add_filter('redirect_canonical', create_function('', 'return false;'), 101, 2);


//Remove W3 Total Cache Comments for untrusteds
if (defined('W3TC'))
    if ($this->opt('remove_html_comments') || !$is_trusted)
        add_filter('w3tc_can_print_comment', create_function('', 'return false;'));


$feed_enable = $this->opt('feed_enable');

if (!$feed_enable && !is_admin()) {
    unset($_GET['feed']);
    unset($_GET[$this->opt('feed_query')]);
    add_action('do_feed', array(&$this, 'block_access'), 1);
    add_action('do_feed_rdf', array(&$this, 'block_access'), 1);
    add_action('do_feed_rss', array(&$this, 'block_access'), 1);
    add_action('do_feed_rss2', array(&$this, 'block_access'), 1);
    add_action('do_feed_atom', array(&$this, 'block_access'), 1);

//...and our own feed type!
    $new_feed_base = trim($this->opt('feed_base'), '/ ');
    if ($new_feed_base) {
        add_action('do_feed_' . $new_feed_base, array(&$this, 'block_access'), 1);
    }
}
if (!$feed_enable || $this->opt('remove_feed_meta')) {
    remove_action('wp_head', 'feed_links', 2);
//Remove automatic the links to the extra feeds such as category feeds.
    remove_action('wp_head', 'feed_links_extra', 3);
}





$new_feed_query = $this->opt('feed_query');
if ($new_feed_query && $new_feed_query != 'feed' && !is_admin()) {
    if (isset($_GET['feed']))
        unset($_GET['feed']);

    $wp->add_query_var($new_feed_query);
    if (isset($_GET[$new_feed_query]))
        $_GET['feed'] = $_GET[$new_feed_query];

    if (!$this->is_permalink()) {
        $this->partial_preg_replace_old[] = '#(' . home_url() . '(/\?)[0-9a-z=_/.&\-;]*)(feed=)#';  //;&amp;
        $this->partial_preg_replace_new[] = '$1' . $new_feed_query . '=';
    }
}

$new_feed_base = trim($this->opt('feed_base'), '/ ');

if ($new_feed_base && 'feed' != $new_feed_base && $this->is_permalink()) {
    $wp_rewrite->feed_base = $new_feed_base;
    add_feed($new_feed_base, array(&$this, 'do_feed_base'));


    $this->partial_preg_replace_old[] = '#(' . home_url() . '/[0-9a-z_\-/.]*)(/feed)#';
    $this->partial_preg_replace_new[] = '$1/' . $new_feed_base;

//Remove default 'feed' type
    $feeds = $wp_rewrite->feeds;
    unset($feeds[0]);
    $wp_rewrite->feeds = $feeds;
}

$author_enable = $this->opt('author_enable');


if (!$author_enable && !is_admin()) {
    unset($_GET['author']);
    unset($_GET['author_name']);
    unset($_GET[$this->opt('author_query')]);
}

$new_author_query = $this->opt('author_query');
if ($new_author_query && $new_author_query != 'author' && !is_admin()) {
    if (isset($_GET['author']))
        unset($_GET['author']);

    if (isset($_GET['author_name']))
        unset($_GET['author_name']);

    $wp->add_query_var($new_author_query);

    if (isset($_GET[$new_author_query]) && is_numeric($_GET[$new_author_query]))
        $_GET['author'] = $_GET[$new_author_query];

    if (isset($_GET[$new_author_query]) && !is_numeric($_GET[$new_author_query]))
        $_GET['author_name'] = $_GET[$new_author_query];

    if (!$this->is_permalink()) {
        $this->partial_preg_replace_old[] = '#(' . home_url() . '(/\?)[0-9a-z=_/.&\-;]*)((author|author_name)=)#';
        $this->partial_preg_replace_new[] = '$1' . $new_author_query . '=';
    }
}

if ($this->opt('antispam')) {
    if (isset($_GET['authar']) && $_GET['authar']) {
        $_GET['author'] = $_GET['authar'];
    }
}


$new_author_base = trim($this->opt('author_base'), '/ ');

if ($this->opt('author_enable') && $new_author_base && 'author' != $new_author_base && $this->is_permalink()) {
    $wp_rewrite->author_base = $new_author_base;

//Not require in most cases!
//$this->preg_replace_old[]= '#('.home_url().'/)(author/)([0-9a-z_\-/.]+)#';
//$this->preg_replace_new[]= '$1'.$new_author_base.'/'.'$3' ;
}


if ($this->opt('author_enable') && $this->opt('author_without_base') && $this->is_permalink()) {
    $wp_rewrite->author_structure = $wp_rewrite->root . '/%author%';

}

$search_enable = $this->opt('search_enable');

if (!$search_enable && !is_admin()) {
    unset($_GET['s']);
    unset($_GET[$this->opt('search_query')]);
}

$new_search_query = $this->opt('search_query');

if ($new_search_query && $new_search_query != 's' && !is_admin()) {
    if (isset($_GET['s']))
        unset($_GET['s']);

    $wp->add_query_var($new_search_query);

    if (isset($_GET[$new_search_query]))
        $_GET['s'] = $_GET[$new_search_query];


//Not require in most cases!
//$this->preg_replace_old[]='#('.home_url().'(/\?)[0-9a-z=_/.&\-;]*)(s=)#';
//$this->preg_replace_new[]='$1'.$new_search_query.'=' ;
//echo $new_search_query;

    $this->preg_replace_old[] = "/name=('|\")s('|\")/";
    $this->preg_replace_new[] = "name='" . $new_search_query . "'";


}

$new_search_base = trim($this->opt('search_base'), '/ ');

if ($new_search_base && 'search' != $new_search_base && $this->is_permalink()) {
    $wp_rewrite->search_base = $new_search_base;
}


$paginate_enable = $this->opt('paginate_enable');

if (!$paginate_enable && !is_admin()) {
    unset($_GET['paged']);
    unset($_GET[$this->opt('paginate_query')]);
}

$new_paginate_query = $this->opt('paginate_query');

if ($new_paginate_query && $new_paginate_query != 'paged' && !is_admin()) {
    if (isset($_GET['paged']))
        unset($_GET['paged']);

    $wp->add_query_var($new_paginate_query);

    if (isset($_GET[$new_paginate_query]))
        $_GET['paged'] = $_GET[$new_paginate_query];

    if (!$this->is_permalink()) {
//Fixed the bug. Here we delete new query that assume as current URL by WP
        $this->partial_preg_replace_old[] = '#(' . home_url() . '(/\?)[0-9a-z=_/.&\-;]*)(' . $new_paginate_query . '=[0-9&]+)#';
        $this->partial_preg_replace_new[] = '$1';

        $this->partial_preg_replace_old[] = '#(' . home_url() . '(/\?)[0-9a-z=_/.&\-;]*)(paged=)#';
        $this->partial_preg_replace_new[] = '$1' . $new_paginate_query . '=';
    }
}

$new_paginate_base = trim($this->opt('paginate_base'), '/ ');

if ($new_paginate_base && 'page' != $new_paginate_base && $this->is_permalink()) {
    $wp_rewrite->pagination_base = $new_paginate_base;
}


$page_enable = $this->opt('page_enable');

if (!$page_enable && !is_admin()) {
    unset($_GET['pagename']);
    unset($_GET['page_id']);
    unset($_GET[$this->opt('page_query')]);
}

$new_page_query = $this->opt('page_query');

if ($new_page_query && $new_page_query != 'page_id' && !is_admin()) {
    if (isset($_GET['page_id']))
        unset($_GET['page_id']);

    if (isset($_GET['pagename']))
        unset($_GET['pagename']);

    $wp->add_query_var($new_page_query);

    if (isset($_GET[$new_page_query]) && is_numeric($_GET[$new_page_query]))
        $_GET['page_id'] = $_GET[$new_page_query];

    if (isset($_GET[$new_page_query]) && !is_numeric($_GET[$new_page_query]))
        $_GET['pagename'] = $_GET[$new_page_query];

    if (!$this->is_permalink()) {
        $this->partial_preg_replace_old[] = '#(' . home_url() . '(/\?)[0-9a-z=_/.&\-;]*)((page_id|pagename)=)#';
        $this->partial_preg_replace_new[] = '$1' . $new_page_query . '=';
    }
}

$new_page_base = trim($this->opt('page_base'), '/ ');

if ($new_page_base && $this->is_permalink()) {

    $wp_rewrite->page_base = $new_page_base;
    $wp_rewrite->page_structure = $wp_rewrite->root . '/' . $new_page_base . '/' . '%pagename%';

}

$post_enable = $this->opt('post_enable');

if (!$post_enable && !is_admin()) {
    unset($_GET['p']);

    unset($_GET[$this->opt('post_query')]);
}

$new_post_query = $this->opt('post_query');

if ($new_post_query && $new_post_query != 'p' && !is_admin() && !isset($_GET['preview'])) {
    $wp->add_query_var($new_post_query);

    if (isset($_GET['p']))
        unset($_GET['p']);

    if (isset($_GET[$new_post_query]) && is_numeric($_GET[$new_post_query]))
        $_GET['p'] = $_GET[$new_post_query];

    if (!$this->is_permalink()) {
        $this->partial_preg_replace_old[] = '#(' . home_url() . '(/\?)[0-9a-z=_/.&\-;]*)(p=)#';
        $this->partial_preg_replace_new[] = '$1' . $new_post_query . '=';
    }
}

//Not work in multisite at all!
if (basename($_SERVER['PHP_SELF']) == 'options-permalink.php' && isset($_POST['permalink_structure'])) {
    $this->options['post_base'] = $_POST['permalink_structure'];
    update_option(self::slug, $this->options);

}


$category_enable = $this->opt('category_enable');

if (!$category_enable && !is_admin()) {
    unset($_GET['cat']);
    unset($_GET[$this->opt('category_name')]);
}

$new_category_query = $this->opt('category_query');

if ($new_category_query && $new_category_query != 'cat' && !is_admin()) {
    $wp->add_query_var($new_category_query);

    unset($_GET['cat']);
    unset($_GET['category_name']);
    if (isset($_GET[$new_category_query]) && is_numeric($_GET[$new_category_query]))
        $_GET['cat'] = $_GET[$new_category_query];

    if (isset($_GET[$new_category_query]) && !is_numeric($_GET[$new_category_query]))
        $_GET['category_name'] = $_GET[$new_category_query];

    if (!$this->is_permalink()) {
        $this->partial_preg_replace_old[] = '#(' . home_url() . '(/\?)[0-9a-z=_/.&\-;]*)((cat|category_name)=)#';
        $this->partial_preg_replace_new[] = '$1' . $new_category_query . '=';
    }
}

if (basename($_SERVER['PHP_SELF']) == 'options-permalink.php' && isset($_POST['category_base'])) {
    $this->options['category_base'] = $_POST['category_base'];
    update_option(self::slug, $this->options);
}

$tag_enable = $this->opt('tag_enable');

if (!$tag_enable && !is_admin()) {
    unset($_GET['tag']);
}

$new_tag_query = $this->opt('tag_query');

if ($new_tag_query && $new_tag_query != 'tag' && !is_admin()) {
    $wp->add_query_var($new_tag_query);

    unset($_GET['tag']);
    if (isset($_GET[$new_tag_query]))
        $_GET['tag'] = $_GET[$new_tag_query];

    if (!$this->is_permalink()) {
        $this->partial_preg_replace_old[] = '#(' . home_url() . '(/\?)[0-9a-z=_/.&\-;]*)(tag=)#';
        $this->partial_preg_replace_new[] = '$1' . $new_tag_query . '=';
    }
}


if (basename($_SERVER['PHP_SELF']) == 'options-permalink.php' && isset($_POST['tag_base'])) {
    $this->options['tag_base'] = $_POST['tag_base'];
    update_option(self::slug, $this->options);
}


if ($this->opt('disable_archive') && !is_admin()) {
    unset($_GET['year']);
    unset($_GET['m']);
    unset($_GET['w']);
    unset($_GET['day']);
    unset($_GET['hour']);
    unset($_GET['minute']);
    unset($_GET['second']);

    unset($_GET['calendar']);
    unset($_GET['monthnum']);
}


if ($this->opt('disable_other_wp') && !is_admin()) {
    unset($_GET['post_type']);
    unset($_GET['cpage']);
    unset($_GET['term']);
    unset($_GET['taxonomy']);
    unset($_GET['robots']);

    unset($_GET['attachment_id']);
    unset($_GET['attachment']);

    unset($_GET['withcomments']);
    unset($_GET['withoutcomments']);

    unset($_GET['orderby']);
    unset($_GET['order']);

//There's still a little more but we ignore them
}


if ($this->opt('remove_other_meta')) {
//Remove generator name and version from your Website pages and from the RSS feed.
    add_filter('the_generator', create_function('', 'return "";'));
//Display the XHTML generator that is generated on the wp_head hook, WP version
    remove_action('wp_head', 'wp_generator');
//Remove the link to the Windows Live Writer manifest file.
    remove_action('wp_head', 'wlwmanifest_link');
//Remove EditURI
    remove_action('wp_head', 'rsd_link');
//Remove index link.
    remove_action('wp_head', 'index_rel_link');
//Remove previous link.
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
//Remove start link.
    remove_action('wp_head', 'start_post_rel_link', 10, 0);
//Remove relational links (previous and next) for the posts adjacent to the current post.
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
//Remove shortlink if it is defined.
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

//remove_action('do_robotstxt', 'wp_shortlink_wp_head', 10, 0);
    remove_action('do_robots', 'do_robots', 10, 0);

    if (isset($GLOBALS['woocommerce'])) {
        remove_action('wp_head', array($GLOBALS['woocommerce'], 'generator'));
        remove_action('wp_head', 'wc_generator_tag');
    }

    $this->replace_old[] = '<link rel="profile" href="http://gmpg.org/xfn/11" />';
    $this->replace_new[] = '';

    $this->replace_old[] = '<link rel="pingback" href="' . get_bloginfo('pingback_url') . '" />';
    $this->replace_new[] = '';

//Added from roots
    if (!class_exists('WPSEO_Frontend'))
        remove_action('wp_head', 'rel_canonical');
}

if (!isset($_POST['wp_customize'])){
    $wp->add_query_var('style_internal_wrapper');
    $wp->add_query_var('script_internal_wrapper');
    if ($this->opt('auto_config_plugins'))
        $wp->add_query_var('get_wrapper');
}





if ($this->opt('new_style_name') && $this->opt('new_style_name') != 'style.css' && $this->is_permalink() && !isset($_POST['wp_customize'])) {

    $rel_style_path = $this->sub_folder . trim(str_replace(site_url(), '', get_stylesheet_directory_uri() . '/style.css'), '/');

//style should be in theme directory.
    $new_style_path = trim($this->opt('new_theme_path'), ' /') . '/' . trim($this->opt('new_style_name'), '/ ');
    $new_style_path = str_replace('.', '\.', $new_style_path);


    if (is_multisite()) {

        $new_style_path = '/' . trim($this->opt('new_theme_path'), '/ ') . '/' . get_stylesheet() . '/' . trim($this->opt('new_style_name'), '/ ');

        $rel_theme_path_with_theme = trim(str_replace(site_url(), '', get_stylesheet_directory_uri()), '/');
        $rel_style_path = $this->blog_path . $rel_theme_path_with_theme . '/style.css'; //without theme

        $wp->add_query_var('template_wrapper');

//Fix a little issue with Multisite partial order
        $this->partial_replace_old[] = '/' . get_stylesheet() . '/style.css';
        $this->partial_replace_new[] = '/' . get_stylesheet() . '/' . str_replace('\.', '.', trim($this->opt('new_style_name'), '/ '));
    } else {
        $this->partial_replace_old[] = '/' . trim($this->opt('new_theme_path'), ' /') . '/style.css';
        $this->partial_replace_new[] = '/' . str_replace('\.', '.', $new_style_path);
    }

    $wp->add_query_var('style_wrapper');

    if (is_child_theme())
        $wp->add_query_var('parent_wrapper');

//This line doesn't work in multisite
    $wp_rewrite->add_rule($new_style_path, 'index.php?style_wrapper=true' . str_replace('?', '&', $this->trust_key), 'top');

   // $wp_rewrite->add_rule(trim($this->opt('new_theme_path'), ' /') . '/' . trim('inline\.css', '/ '), 'index.php?style_internal_wrapper=true' . str_replace('?', '&', $this->trust_key), 'top');

    $this->partial_replace_old[] = $rel_style_path;
    $this->partial_replace_new[] = str_replace('\.', '.', $new_style_path);


    if ($this->opt('clean_new_style')) {
        $old = array('wp-caption', 'alignright', 'alignleft', 'alignnone', 'aligncenter');
        $new = array('x-caption', 'x-right', 'x-left', 'x-none', 'x-center');

        $this->post_replace_old = array_merge($this->post_replace_old, $old);
        $this->post_replace_new = array_merge($this->post_replace_new, $new);

        $this->post_preg_replace_old[] = '#wp\-(image|att)\-[0-9]*#';
        $this->post_preg_replace_new[] = '';

    }
}




if (is_multisite()){
    $recent_message_last= get_blog_option(SITE_ID_CURRENT_SITE,'pp_important_messages_last');
}else{
    $recent_message_last= get_option('pp_important_messages_last');
}




//echo '<pre>';
//print_r($wp_rewrite);
//echo '</pre>';

//These 3 should be after page base so get_permalink in block access should work correctly


if ($this->opt('hide_wp_admin') && !$is_trusted) {

    if ($this->h->str_contains(($_SERVER['PHP_SELF']), '/wp-admin/') || is_admin() && trim($this->opt('new_admin_path'), ' /') != 'wp-admin' && !$this->h->str_contains($_SERVER['REQUEST_URI'], $this->opt('new_admin_path'))) {
        if (!$this->h->ends_with($_SERVER['PHP_SELF'], '/admin-ajax.php') && !$this->h->ends_with($_SERVER['PHP_SELF'], '/tevolution-ajax.php') ) {
            $this->block_access();
        }
    }
}

//$is_trusted: When user request xmlrpc.php current user will be set to 0 by WP so only admin key works
if ($this->opt('avoid_direct_access') && !$is_trusted) {
    if ($this->h->ends_with($_SERVER['PHP_SELF'], '.php') && !$this->h->str_contains($_SERVER['PHP_SELF'], '/wp-admin/')) {
        $white_list = explode(",", $this->opt('direct_access_except'));
        $white_list[] = 'wp-login.php';
        $white_list[] = 'index.php';
        $block = true;

        if ($this->opt('new_login_path'))
            $white_list[]=$this->opt('new_login_path');

        foreach ($white_list as $white_file) {
            if ($this->h->ends_with($_SERVER['PHP_SELF'], trim($white_file, ', \r\n')))
                $block = false;
        }

        if ($block)
            $this->block_access();
    }
}

if ($this->opt('hide_wp_login') && !$is_trusted) {
    if ($this->h->ends_with($_SERVER['PHP_SELF'], '/wp-login.php') || $this->h->ends_with($_SERVER['PHP_SELF'], '/wp-login.php/') || $this->h->ends_with($_SERVER['PHP_SELF'], '/wp-signup.php')) {

        if (!trim($this->opt('new_login_path'), '/ ') || !$this->h->str_contains($_SERVER['REQUEST_URI'], '/'.$this->opt('new_login_path')))
            $this->block_access();
    }
}


//Fix a WooCommerce problem
if (function_exists('wc_get_page_id') && trim($this->opt('page_base'), ' /')) {
    $this->replace_old [] = get_permalink(wc_get_page_id('shop'));
    $this->replace_new [] = str_replace(trim($this->opt('page_base'), ' /') . '/', '', get_permalink(wc_get_page_id('shop')));
}

if ($this->opt('remove_other_meta') && !$this->opt('remove_html_comments') && !$is_trusted && (function_exists('hyper_cache_sanitize_uri') || class_exists('WpFastestCache') || defined('QUICK_CACHE_ENABLE') || defined('CACHIFY_FILE') || defined('WP_CACHE')  || defined('WP_ROCKET_VERSION')|| function_exists('wc_get_page_id'))){
    $this->preg_replace_old[]='/<!--(?:(?!-->).)*(WooCommerce|W3 Total Cache|WP Rocket|WP\-Super\-Cache)(?:(?!-->).)*-->/s';
    $this->preg_replace_new[]= ' ';
}

if (defined('WP_CACHE') && !$is_trusted){
    global $wp_super_cache_comments;
    $wp_super_cache_comments = 0;
}

//We only need replaces in this line. htaccess related works don't work here. They need flush and generate_rewrite_rules filter
//do not hide anything for scan my wp server. 

if(!$is_scanmywp) {
    $this->add_rewrite_rules($wp_rewrite);
}




?>