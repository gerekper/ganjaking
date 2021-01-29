<?php
/**
 * Plugin Name: Folders
 * Description: Arrange media, pages, custom post types and posts into folders
 * Version: 2.6.7
 * Author: Premio
 * Author URI: https://premio.io/downloads/folders/
 * Text Domain: folders
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if(!defined("WCP_FOLDERS_PLUGIN_FILE")) {
    define('WCP_FOLDERS_PLUGIN_FILE', __FILE__);
}
if(!defined("WCP_FOLDERS_PLUGIN_BASE")) {
    define('WCP_FOLDERS_PLUGIN_BASE', plugin_basename(WCP_FOLDERS_PLUGIN_FILE));
} 
if(!defined("WCP_FOLDER")) {
    define('WCP_FOLDER', 'folders');
}
if(!defined("WCP_FOLDER_VAR")) {
    define('WCP_FOLDER_VAR', 'folders_settings');
}
if(!defined("WCP_DS")) {
    define("WCP_DS", DIRECTORY_SEPARATOR);
}
if(!defined("WCP_FOLDER_URL")) {
    define('WCP_FOLDER_URL', plugin_dir_url(__FILE__));
}
if(!defined("WCP_FOLDER_VERSION")) {
    define('WCP_FOLDER_VERSION', "2.6.7");
}


if(!function_exists("folders_clear_all_caches")) {
    function folders_clear_all_caches()
    {
        /* Clear cookies from browser */
        try {
            global $wp_fastest_cache;
            // if W3 Total Cache is being used, clear the cache
            if (function_exists('w3tc_flush_all')) {
                w3tc_flush_all();
                /* if WP Super Cache is being used, clear the cache */
            } else if (function_exists('wp_cache_clean_cache')) {
                global $file_prefix, $supercachedir;
                if (empty($supercachedir) && function_exists('get_supercache_dir')) {
                    $supercachedir = get_supercache_dir();
                }
                wp_cache_clean_cache($file_prefix);
            } else if (class_exists('WpeCommon')) {
                //be extra careful, just in case 3rd party changes things on us
                if (method_exists('WpeCommon', 'purge_memcached')) {
                    //WpeCommon::purge_memcached();
                }
                if (method_exists('WpeCommon', 'clear_maxcdn_cache')) {
                    //WpeCommon::clear_maxcdn_cache();
                }
                if (method_exists('WpeCommon', 'purge_varnish_cache')) {
                    //WpeCommon::purge_varnish_cache();
                }
            } else if (method_exists('WpFastestCache', 'deleteCache') && !empty($wp_fastest_cache)) {
                $wp_fastest_cache->deleteCache();
            } else if (function_exists('rocket_clean_domain')) {
                rocket_clean_domain();
                // Preload cache.
                if (function_exists('run_rocket_sitemap_preload')) {
                    run_rocket_sitemap_preload();
                }
            } else if (class_exists("autoptimizeCache") && method_exists("autoptimizeCache", "clearall")) {
                autoptimizeCache::clearall();
            } else if (class_exists("LiteSpeed_Cache_API") && method_exists("autoptimizeCache", "purge_all")) {
                LiteSpeed_Cache_API::purge_all();
            }

            if (class_exists("Breeze_PurgeCache") && method_exists("Breeze_PurgeCache", "breeze_cache_flush")) {
                Breeze_PurgeCache::breeze_cache_flush();
            }


            if (class_exists( '\Hummingbird\Core\Utils' ) ) {
                $modules   = \Hummingbird\Core\Utils::get_active_cache_modules();
                foreach ( $modules as $module => $name ) {
                    $mod = \Hummingbird\Core\Utils::get_module( $module );
                    if ( $mod->is_active() ) {
                        if ( 'minify' === $module ) {
                            $mod->clear_files();
                        } else {
                            $mod->clear_cache();
                        }
                    }
                }
            }

            if ( function_exists( 'wp_cache_clean_cache' ) ) {
                global $file_prefix;
                wp_cache_clean_cache( $file_prefix, true );
            }
        } catch (Exception $e) {
            return 1;
        }
    }
}

include_once plugin_dir_path(__FILE__)."includes/plugins.class.php";
include_once plugin_dir_path(__FILE__)."includes/media.replace.php";
include_once plugin_dir_path(__FILE__)."includes/folders.class.php";
register_activation_hook( __FILE__, array( 'WCP_Folders', 'activate' ) );
WCP_Folders::get_instance();

/* Affiliate Class*/
if(is_admin()) {
    include_once plugin_dir_path(__FILE__)."includes/class-affiliate.php";
    include_once plugin_dir_path(__FILE__) . "includes/class-review-box.php";
}