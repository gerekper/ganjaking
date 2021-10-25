<?php

use GT3\ThemesCore\Assets;

if(!defined('ABSPATH')) {
	exit;
}

//Variable
$gt3_theme_check = wp_get_theme();
$gt3_theme_check_template = $gt3_theme_check->get('Template');
$options_name = !empty($gt3_theme_check_template) ? $gt3_theme_check_template : $gt3_theme_check->get('TextDomain');

define('GT3_THEME_OPTIONS_NAME', $options_name );
define('GT3_CORE_WIDGETS_IMG', plugin_dir_url(__FILE__).'core/elementor/assets/image/');
define('GT3_CORE_URL', plugin_dir_url(__FILE__));

require_once __DIR__.'/core/autoload.php';
Assets::instance();

// Aq_Resizer
require_once __DIR__.'/core/aq_resizer.php';

//Post type
require_once __DIR__.'/core/cpt/init.php';

//Load redux
require_once __DIR__.'/core/framework/class.redux-plugin.php';
require_once __DIR__.'/core/framework/init.php';
require_once __DIR__.'/core/redux-extension-loader.php';
require_once __DIR__.'/core/redux-importer-config.php';

//Load meta-box
require_once __DIR__.'/core/meta-box/meta-box.php';
require_once __DIR__.'/core/metabox-addon.php';
require_once __DIR__.'/core/theme-adding-functions.php';
require_once __DIR__.'/core/theme_icons_svg.php';

//Load assets
require_once __DIR__.'/assets/init.php';

/*column-tabs*/
//require_once __DIR__.'/core/fix_elementor/index.php';

$menu_enable = apply_filters('gt3/core/mega-menu-enable', false);
if ($menu_enable) {
	require_once __DIR__.'/core/gt3_mega_menu/class-gt3_mega_menu.php';
}
