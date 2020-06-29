<?php

/*make the difference between frontend and backend routing*/
/*require what is needed for common purpose in backend such as backend menus*/
defined( 'ABSPATH' ) or die( 'Not allowed' );
if(defined('WP_ADMIN')) {
    define('WYSIJA_SIDE','back');
}else define('WYSIJA_SIDE','front');

$plugin_name='wysija-newsletters';
$plugin_folder_name=dirname(dirname(plugin_basename(__FILE__)));
$current_folder=dirname(dirname(__FILE__));

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
define('WYSIJA', $plugin_name);
if(!defined('WYSIJA_PLG_DIR')) define('WYSIJA_PLG_DIR', dirname($current_folder).DS);
define('WYSIJA_DIR', $current_folder.DS);
define('WYSIJA_DATA_DIR', WYSIJA_DIR.'data'.DS);
define('WYSIJA_FILE',WYSIJA_DIR.'index.php');
define('WYSIJA_URL',plugins_url().'/'.strtolower('wysija-newsletters').'/');

$upload_dir = wp_upload_dir();

define('WYSIJA_UPLOADS_DIR',str_replace('/',DS,$upload_dir['basedir']).DS.'wysija'.DS);
define('WYSIJA_UPLOADS_URL',$upload_dir['baseurl'].'/wysija/');
if(is_multisite()){
    define('WYSIJA_UPLOADS_MS_DIR',str_replace(get_option( 'upload_path' ), get_blog_option(1, 'upload_path'), $upload_dir['basedir']).DS.'wysija'.DS);
    define('WYSIJA_UPLOADS_MS_URL',get_blog_option(1, 'siteurl').'/'.get_blog_option(1, 'upload_path').'/wysija/');
}

define('WYSIJA_INC',WYSIJA_DIR.'inc'.DS);
define('WYSIJA_CORE',WYSIJA_DIR.'core'.DS);
define('WYSIJA_VIEWS',WYSIJA_DIR.'views'.DS);
define('WYSIJA_MODELS',WYSIJA_DIR.'models'.DS);
define('WYSIJA_HELPERS',WYSIJA_DIR.'helpers'.DS);
define('WYSIJA_CLASSES',WYSIJA_DIR.'classes'.DS);
define('WYSIJA_CTRL',WYSIJA_DIR.'controllers'.DS);
define('WYSIJA_WIDGETS',WYSIJA_DIR.'widgets'.DS);

define('WYSIJA_DIR_IMG',WYSIJA_DIR.'img'.DS);
define('WYSIJA_EDITOR_IMG',WYSIJA_URL.'img/');
define('WYSIJA_EDITOR_TOOLS',WYSIJA_DIR.'tools'.DS);
global $blog_id;
define('WYSIJA_CRON',md5(__FILE__.$blog_id));