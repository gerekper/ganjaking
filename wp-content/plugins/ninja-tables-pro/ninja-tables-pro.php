<?php
/*
Plugin Name: Ninja Tables Pro
Description: The Pro Add-On of Ninja Tables, best Responsive Table Plugin for WordPress.
Version: 4.1.5
Author: WPManageNinja
Author URI: https://wpmanageninja.com/
Plugin URI: https://wpmanageninja.com/downloads/ninja-tables-pro-add-on/
License: GPLv2 or later
Text Domain: ninja-tables-pro
Domain Path: /resources/languages
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    die;
}

update_option('_ninjatables_pro_license_status', 'valid');
// To check if pro is available in scripts in this plugin
defined('NINJATABLESPRO') or define('NINJATABLESPRO', true);
define('NINJAPROPLUGIN_PATH', plugin_dir_path(__FILE__));
define('NINJAPRO_PLUGIN_FILE', __FILE__);
defined('NINJAPROPLUGIN_VERSION') or define('NINJAPROPLUGIN_VERSION', '4.1.5');
define('NINJAPROPLUGIN_URL', plugin_dir_url(__FILE__));

define('NINJATABLESPRO_SORTABLE', true);

$ninja_table_after_print = array();

if (!class_exists('NinjaTablesPro')) {
    class NinjaTablesPro
    {
        public function boot()
        {
            $this->loadDependencies();
            if (is_admin()) {
                $this->adminHooks();
            }
            $this->public_hooks();
            $this->loadTextDomain();
        }

        /**
         * Register admin/backend hooks
         */
        public function adminHooks()
        {
            if (current_user_can('install_plugins')) {
                $this->checkForPluginInstall();
            }

            add_filter('ninja_tables_item_attributes', '\NinjaTablesPro\Position::make');
            add_filter('ninja_tables_import_table_data', '\NinjaTablesPro\Position::maker', 10, 2);

            add_action('wp_ajax_ninja_tables_set_permission', '\NinjaTablesPro\Permission::set');
            add_action('wp_ajax_ninja_tables_get_permission', '\NinjaTablesPro\Permission::get');

            // Init sortable for the table.
            add_action('wp_ajax_ninja_tables_init_sortable', '\NinjaTablesPro\Sortable::init');
            add_action('wp_ajax_ninja_tables_sort_table', '\NinjaTablesPro\Sortable::sort');

            add_action('ninja_tables_loaded_boot_script', array($this, 'loadFormulaScript'));
        }

        public function public_hooks()
        {
            $tableEditor = new \NinjaTablesPro\TableEditor();
            $tableEditor->register();

            add_filter('ninja_table_admin_role', '\NinjaTablesPro\Permission::getPermission');



            add_filter('ninja_table_js_config', function ($config, $filter) {
                if (!empty($config['shortCodeData']['get_filter'])) {
                    $filter_var = $config['shortCodeData']['get_filter'];
                    if (isset($_GET[$filter_var])) {
                        $filter = sanitize_text_field($_GET[$filter_var]);
                    }
                    if (isset($_GET['column'])) {
                        $config['settings']['filter_column'] = explode(',', $_GET['column']);
                    }
                }
                $filter = apply_filters('ninja_parse_placeholder', $filter);
                $config['default_filter'] = $filter;


                if (isset($config['shortCodeData']['stackable']) && $config['shortCodeData']['stackable']) {
                    $config['settings']['stackable'] = $config['shortCodeData']['stackable'];
                    $devices = $config['shortCodeData']['stack_devices'];
                    $devices = explode(',', $devices);
                    $config['settings']['stacks_devices'] = $devices;
                }

                return $config;
            }, 10, 2);

            add_filter('ninja_table_column_attributes', function ($formatted_column, $originalColumn) {
                if (isset($formatted_column['title'])) {
                    $formatted_column['title'] = do_shortcode($formatted_column['title']);
                }

                if (isset($originalColumn['conditions'])) {
                    $conditions = $originalColumn['conditions'];

                    foreach ($conditions as $conditionIndex => $condition) {
                        $conditions[$conditionIndex]['conditionalValue'] = apply_filters('ninja_parse_placeholder', $conditions[$conditionIndex]['conditionalValue']);
                        $conditions[$conditionIndex]['conditionalValue2'] = apply_filters('ninja_parse_placeholder', $conditions[$conditionIndex]['conditionalValue2']);
                    }
                    $formatted_column['conditions'] = $conditions;
                }
                
                if(isset($originalColumn['transformed_value']) && $originalColumn['transformed_value']) {
                    $originalColumn['transformed_value'] = apply_filters('ninja_parse_placeholder', $originalColumn['transformed_value']);
                }

                if (isset($originalColumn['transformed_value'])) {
                    $formatted_column['transformed_value'] = $originalColumn['transformed_value'];
                }

                return $formatted_column;
            }, 10, 2);

            add_filter('ninja_tables_shortcode_defaults', function ($defaults) {
                wp_register_script('ninja-tables-pro', NINJAPROPLUGIN_URL . 'assets/ninja-tables-pro.js', array('footable'), NINJAPROPLUGIN_VERSION, true);
                $defaults['per_page'] = null;
                $defaults['search'] = null;
                $defaults['sorting'] = null;
                $defaults['hide_header'] = null;
                $defaults['logged_in_only'] = null;
                $defaults['columns'] = 'all';
                $defaults['get_filter'] = '';
                $defaults['filter_column'] = '';
                $defaults['skip'] = 0;
                $defaults['limit'] = 0;
                $defaults['disable_edit'] = 'no';
                $defaults['hide_default_filter'] = '';
                $defaults['filter_selects'] = '';
                $defaults['stackable'] = '';
                $defaults['stack_devices'] = 'xs,sm';
                $defaults['post_tax'] = '';
                $defaults['post_tax_field'] = 'slug';
                $defaults['sf_filter'] = '';
                $defaults['sf_column'] = '';
                $defaults['sf_match'] = 'equal';
                return $defaults;
            });

            add_filter('ninja_tables_rendering_table_settings', function ($settings, $shortCodeData) {
                if (isset($shortCodeData['per_page']) && $shortCodeData['per_page'] !== null) {
                    $settings['perPage'] = intval($shortCodeData['per_page']);
                }

                if (isset($shortCodeData['search']) && $shortCodeData['search'] !== null) {
                    $settings['enable_search'] = (bool)$shortCodeData['search'];
                }
                if (isset($shortCodeData['sorting']) && $shortCodeData['sorting'] !== null) {
                    $settings['column_sorting'] = (bool)$shortCodeData['sorting'];
                }
                if (isset($shortCodeData['hide_header']) && $shortCodeData['hide_header'] !== null) {
                    $settings['hide_header_row'] = (bool)$shortCodeData['hide_header'];
                }

                if (isset($shortCodeData['logged_in_only']) && $shortCodeData['logged_in_only'] !== null && $shortCodeData['logged_in_only']) {
                    if (!is_user_logged_in()) {
                        return array();
                    }
                }

                if (isset($shortCodeData['skip']) && $shortCodeData['skip']) {
                    $settings['skip_rows'] = $shortCodeData['skip'];
                }
                if (isset($shortCodeData['limit']) && $shortCodeData['limit']) {
                    $settings['limit_rows'] = $shortCodeData['limit'];
                }

                if (isset($shortCodeData['filter_column']) && $shortCodeData['filter_column']) {
                    $filterColumns = explode(',', $shortCodeData['filter_column']);
                    $settings['filter_column'] = $filterColumns;
                }

                if (isset($shortCodeData['columns']) && !empty($shortCodeData['columns']) && $shortCodeData['columns'] != 'all') {
                    $columns = explode(',', $shortCodeData['columns']);
                    if ($columns) {
                        $columns = array_flip($columns);
                        $settings['columns_only'] = $columns;
                    }
                }
                return $settings;
            }, 10, 2);

            add_filter('ninja_table_rendering_table_vars', function ($table_vars, $table_id, $tableArray) {
                if (isset($tableArray['shortCodeData']['disable_edit']) && $tableArray['shortCodeData']['disable_edit'] == "no") {
                    $dataProvider = ninja_table_get_data_provider($table_id);
                    if ($dataProvider == 'default') {
                        $editor = new \NinjaTablesPro\TableEditor();
                        $table_vars['editing'] = $editor->getEditingVars($table_id);
                    }
                }

                $table_vars['settings']['filter_selects'] = array();
                $defaultSelects = $tableArray['shortCodeData']['filter_selects'];
                if (isset($_GET['filter_selects']) && $_GET['filter_selects']) {
                    $defaultSelects = sanitize_text_field($_GET['filter_selects']);
                }
                if ($defaultSelects) {
                    $defaultSelects = explode('|', $defaultSelects);
                    $formattedSelects = array();
                    foreach ($defaultSelects as $defaultSelect) {
                        $selectPair = explode('=', $defaultSelect);
                        if (count($selectPair) == 2) {
                            $formattedSelects[] = array(
                                'target' => $selectPair[0],
                                'value'  => $selectPair[1]
                            );
                        }
                    }
                    $table_vars['settings']['filter_selects'] = $formattedSelects;
                }

                if (isset($tableArray['shortCodeData']['hide_default_filter']) && $tableArray['shortCodeData']['hide_default_filter'] == "yes") {
                    $table_vars['settings']['hide_default_filter'] = 'yes';
                }

                if (\NinjaTables\Classes\ArrayHelper::get($tableArray, 'settings.sticky_header') == 'yes') {
                    wp_enqueue_script('jquery.stickytableheaders', NINJAPROPLUGIN_URL . 'assets/libs/stickyheaders/jquery.stickytableheaders.min.js', array('jquery'), '0.1.24', true);
                    $table_vars['settings']['sticky_header'] = true;
                    $table_vars['settings']['sticky_header_offset'] = \NinjaTables\Classes\ArrayHelper::get($tableArray, 'settings.sticky_header_offset');
                }

                return $table_vars;
            }, 10, 3);

            add_filter('ninja_parse_placeholder', function ($string) {
                return \NinjaTablesPro\PlaceholderParser::parse($string);
            });

            add_action('ninja_tables_require_formulajs', array($this, 'loadFormulaScript'));

            add_action('ninja_tables_load_lightbox', function ($settings) {
                wp_enqueue_script('lity', NINJAPROPLUGIN_URL . 'assets/libs/lity/lity.min.js', array('jquery'), '2.3.1', true);
                wp_enqueue_style('lity', NINJAPROPLUGIN_URL . 'assets/libs/lity/lity.min.css', array(), '2.3.1');
            });

            add_filter('ninja_tables_get_public_data', array($this, 'serverSideFilter'), 11, 1);
        }

        private function checkForPluginInstall()
        {
            if (defined('NINJA_TABLES_DIR_URL')) {
                return;
            }
            // parent plugin is not installed;
            add_action('admin_notices', function () {
                $pluginInfo = $this->getNinjaTableInstallDetails();

                $class = 'notice notice-error';

                $install_url_text = 'Click Here to Install the plugin';

                if ($pluginInfo->action == 'activate') {
                    $install_url_text = 'Click Here to Activate the plugin';
                }

                $message = 'NinjaTables Pro Add-On Requires Ninja Tables Base Plugin, <b><a href="' . $pluginInfo->url . '">' . $install_url_text . '</a></b>';

                printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
            });
        }

        private function getNinjaTableInstallDetails()
        {
            $activation = (object)array(
                'action' => 'install',
                'url'    => ''
            );
            $allPlugins = get_plugins();
            if (isset($allPlugins['ninja-tables/ninja-tables.php'])) {
                $url = wp_nonce_url(
                    self_admin_url('plugins.php?action=activate&plugin=ninja-tables/ninja-tables.php'),
                    'activate-plugin_ninja-tables/ninja-tables.php'
                );
                $activation->action = 'activate';
            } else {
                $api = (object)array(
                    'slug' => 'ninja-tables'
                );
                $url = wp_nonce_url(
                    self_admin_url('update.php?action=install-plugin&plugin=' . $api->slug),
                    'install-plugin_' . $api->slug
                );
            }
            $activation->url = $url;
            return $activation;
        }

        private function loadDependencies()
        {
            include 'helper_functions.php';
            include 'src/Permission.php';
            include 'src/Sortable.php';
            include 'src/Position.php';
            include 'src/CustomFilters.php';
            include 'src/TableEditor.php';
            include 'src/PlaceholderParser.php';
            include 'src/ExtraShortcodes.php';

            if (defined('NINJA_TABLES_DIR_URL')) {
                include 'src/DataProviders/WPPostDataSourceTrait.php';
                include 'src/DataProviders/WooDataSourceTrait.php';
                include 'src/DataProviders/WPPostsProvider.php';
                include 'src/DataProviders/WoocommercePostsProvider.php';
                include 'src/DataProviders/CsvProvider.php';
                include 'src/DataProviders/GoogleSheetProvider.php';
                include 'src/DataProviders/RawSqlProvider.php';
                $wpPostProvider = new \NinjaTablesPro\DataProviders\WPPostsProvider();
                $wooProvider = new \NinjaTablesPro\DataProviders\WoocommercePostsProvider();
                $csvProvider = new \NinjaTablesPro\DataProviders\CsvProvider();
                $sqlProvider = new \NinjaTablesPro\DataProviders\RawSqlProvider();
                $wpPostProvider->boot();
                $csvProvider->boot();
                $sqlProvider->boot();
                $wooProvider->boot();

                $extraShortcodes = new \NinjaTablesPro\ExtraShortcodes();
                $extraShortcodes->register();
            }
        }

        public function loadTextDomain()
        {
            load_plugin_textdomain('ninja-tables-pro', false, basename(dirname(__FILE__)) . '/resources/languages/');
        }

        public function loadFormulaScript()
        {
            wp_enqueue_script('formula-parser',
                NINJAPROPLUGIN_URL . "assets/libs/formula/formula-parser.min.js",
                array('jquery'), '3.0.1', true
            );
        }

        public function serverSideFilter($data) {
            global $ninja_table_current_rendering_table;
            if (!$ninja_table_current_rendering_table || !isset($ninja_table_current_rendering_table['shortCodeData']['sf_column']) || !$ninja_table_current_rendering_table['shortCodeData']['sf_column']) {
                return $data;
            }
            $sFilter = $ninja_table_current_rendering_table['shortCodeData']['sf_filter'];
            $sColumn = $ninja_table_current_rendering_table['shortCodeData']['sf_column'];
            $sfMatch = $ninja_table_current_rendering_table['shortCodeData']['sf_match'];
            if (!$sFilter || !$sColumn) {
                return $data;
            }
            $sFilter  = apply_filters('ninja_parse_placeholder', $sFilter);
            $newData = array_filter($data, function ($array) use ($sFilter, $sColumn, $sfMatch) {

                if(!isset($array[$sColumn])) {
                    return false;
                }

                switch ($sfMatch) {
                    case 'equal':
                        return ($array[$sColumn] == $sFilter);
                    case 'contains':
                        return  (strpos($array[$sColumn], $sFilter) !== false);
                    case 'lt':
                        return ($array[$sColumn] < $sFilter);
                    case 'gt':
                        return ($array[$sColumn] > $sFilter);
                    case 'startswith':
                        return  (strpos($array[$sColumn], $sFilter) === 0);
                }
                return false;
            });
            return $newData;
        }
    }


    /**
     * Plugin init hook
     */
    add_action('init', function () {
        $ninjaTableBoot = new \NinjaTablesPro();
        $ninjaTableBoot->boot();
    });

    include 'src/libs/updater/ninja_table_pro_updater.php';
}

add_action('ninja_tables_item_attributes', function ($attributes) {
    $values = json_decode($attributes['value'], true);

    if(isset($values['created_at']) && !$values['created_at']) {
        $values['created_at'] = date('Y-m-d h:i A', current_time('timestamp'));
        $attributes['value'] = json_encode($values, JSON_UNESCAPED_UNICODE);
    }

    if(isset($values['updated_at'])) {
        $values['updated_at'] = date('Y-m-d h:i A', current_time('timestamp'));
        $attributes['value'] = json_encode($values, JSON_UNESCAPED_UNICODE);
    }

    return $attributes;
});
