<?php
try {
    if (!is_admin() && seedprod_pro_cu('dm')) {
            $seedprod_page_mapped_id = null;
            $seedprod_page_mapped_url = null;

            // get requested url
            $seedprod_page_mapped_url = (isset($_SERVER['HTTPS']) &&
                                 $_SERVER['HTTPS'] === 'on' ?
                                         "https" :
                                         "http")
                                 . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $seedprod_url_parsed = parse_url($seedprod_page_mapped_url);

            // Database Query
            global $wpdb;
            $seedprod_tablename = $wpdb->prefix . 'sp_domain_mapping';

            $seedprod_sql  = "SELECT * FROM $seedprod_tablename";
            $seedprod_sql .= ' WHERE domain = "%s"';

            if ($seedprod_url_parsed['path'] == '/') {
                $seedprod_sql .= ' AND (path = "" OR path IS NULL)';
                $seedprod_safe_sql = $wpdb->prepare($seedprod_sql, $seedprod_url_parsed['host']);
            } else {
                $seedprod_sql .= ' AND path = "%s"';
                $seedprod_safe_sql = $wpdb->prepare(
                $seedprod_sql,
                $seedprod_url_parsed['host'],
                trim($seedprod_url_parsed['path'], '/')
            );
            }

            $seedprod_results = $wpdb->get_results($seedprod_safe_sql);

            // Die if no matches from sp_domain_mapping table &&
            // the site host !== the requested host
            $site_host = parse_url(site_url(), PHP_URL_HOST);

            if (empty($seedprod_results) && $site_host !== $seedprod_url_parsed['host']) {
                // check if domain has any mappings before applying this rule.
                $seedprod_tablename = $wpdb->prefix . 'sp_domain_mapping';
                $seedprod_sql  = "SELECT * FROM $seedprod_tablename";
                $seedprod_sql .= ' WHERE domain = "%s" LIMIT 1';
                $seedprod_safe_sql = $wpdb->prepare($seedprod_sql, $seedprod_url_parsed['host']);
                $seedprod_results = $wpdb->get_results($seedprod_safe_sql);
                if (!empty($seedprod_results)) {
                    wp_die('Page Not Found', 'Page Not Found', array('response'=> 404));
                }
            }
    }

    if (!empty($seedprod_results)) {

    // Prevent WordPress from automatically redirecting to main site when mapped domain does not have a path
        if ($seedprod_url_parsed['path'] == '/') {
            remove_filter('template_redirect', 'redirect_canonical');
        }

        // Redirect if force_https is true and URL scheme is not https
        if ($seedprod_results[0]->force_https &&
         $seedprod_url_parsed['scheme'] !== 'https') {
            header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
            exit;
        }

        // if we match show the mapped page
        $seedprod_page_mapped_id = $seedprod_results[0]->mapped_page_id;

        if (function_exists('bp_is_active')) {
            add_action('template_redirect', 'seedprod_pro_mapped_domain_render', 9);
        } else {
            add_action('template_redirect', 'seedprod_pro_mapped_domain_render', 10);
        }
    }
} catch (Exception $e) {
}


function seedprod_pro_mapped_domain_render(){
    global $seedprod_page_mapped_id;
    if(!empty($seedprod_page_mapped_id)){
        $has_settings = get_post_meta( $seedprod_page_mapped_id, '_seedprod_page', true );
        if (!empty($has_settings)) {
            // Get Page
            global $wpdb;
            $tablename = $wpdb->prefix . 'posts';
            $sql = "SELECT * FROM $tablename WHERE id= %d";
            $safe_sql = $wpdb->prepare($sql, absint($seedprod_page_mapped_id));
            $page = $wpdb->get_row($safe_sql);

            $settings = json_decode($page->post_content_filtered);

            $template = SEEDPROD_PRO_PLUGIN_PATH.'resources/views/seedprod-preview.php';
            add_action('wp_enqueue_scripts', 'seedprod_pro_deregister_styles', PHP_INT_MAX);
            add_filter( 'option_siteurl', 'seedprod_pro_modify_url' );
            add_filter( 'option_home', 'seedprod_pro_modify_url' );
            add_filter( 'script_loader_src', 'seedprod_pro_modify_asset_url', 10, 2 );
            add_filter( 'style_loader_src', 'seedprod_pro_modify_asset_url', 10, 2 );
            add_filter( 'stylesheet_directory_uri', 'seedprod_pro_modify_url' );
            add_filter( 'template_directory_uri', 'seedprod_pro_modify_url' );
            add_filter( 'pre_get_document_title', 'seedprod_pro_replace_title', 10, 2 );
            //remove_action( 'wp_head', '_wp_render_title_tag', 1 );
            header("HTTP/1.1 200 OK");
            $is_mapped =true;
            require_once($template);

            exit();
        }
    }
}

function seedprod_pro_modify_url( $url ) {
    return seedprod_pro_replace_url( $url );
}

function seedprod_pro_modify_asset_url( $url, $handle ) {
    return seedprod_pro_replace_url( $url );
}

function seedprod_pro_replace_url( $url ) {
    global $seedprod_page_mapped_url;
    $seedprod_url_parsed = parse_url($seedprod_page_mapped_url);
    $new_domain = $seedprod_url_parsed['scheme'].'://'.$seedprod_url_parsed['host'];
    if(strpos($url,'/wp-content/') != false){
        $domain = explode('/wp-content/',$url);
        $url = str_replace($domain[0],$new_domain,$url);
    }elseif(strpos($url,'/wp-includes/') != false){
        $domain = explode('/wp-includes/',$url);
        $url = str_replace($domain[0],$new_domain,$url);
    }else{
        $url = $new_domain;
    }
    return $url;
}

function seedprod_pro_replace_title($title){
    global $seedprod_page_mapped_url;
    $seedprod_url_parsed = parse_url($seedprod_page_mapped_url);
    $new_domain = $seedprod_url_parsed['host'];
    global $wp_query;
    //if (is_404()) {
        $title = $new_domain;
    //}

    return $title;
}
