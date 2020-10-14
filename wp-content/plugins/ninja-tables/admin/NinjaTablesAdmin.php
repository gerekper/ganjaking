<?php
/*
 * Do Not USE namespace because The Pro Add-On Used this Class
 */

use NinjaTable\TableDrivers\NinjaFooTable;
use NinjaTables\Classes\ArrayHelper;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpmanageninja.com
 * @since      1.0.0
 *
 * @package    ninja-tables
 * @subpackage ninja-tables/admin
 */
class NinjaTablesAdmin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * Custom Post Type Name
     *
     * @since    1.0.0
     * @access   private
     * @var      string $cpt_name .
     */
    private $cpt_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name = 'ninja-tables', $version = NINJA_TABLES_VERSION)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->cpt_name = 'ninja-table';

        add_action('admin_enqueue_scripts', array($this, 'enqueue_data_tables_scripts'));

    }

    /**
     * Register form post types
     *
     * @return void
     */
    public function register_post_type()
    {
        $args = array(
            'label'               => __('Ninja Tables', 'ninja-tables'),
            'public'              => false,
            'publicly_queryable'  => false,
            'exclude_from_search' => true,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'query_var'           => false,
            'supports'            => array('title'),
            'labels'              => array(
                'name'               => __('Ninja Tables', 'ninja-tables'),
                'singular_name'      => __('Table', 'ninja-tables'),
                'menu_name'          => __('Ninja Tables', 'ninja-tables'),
                'add_new'            => __('Add Table', 'ninja-tables'),
                'add_new_item'       => __('Add New Table', 'ninja-tables'),
                'edit'               => __('Edit', 'ninja-tables'),
                'edit_item'          => __('Edit Table', 'ninja-tables'),
                'new_item'           => __('New Table', 'ninja-tables'),
                'view'               => __('View Table', 'ninja-tables'),
                'view_item'          => __('View Table', 'ninja-tables'),
                'search_items'       => __('Search Table', 'ninja-tables'),
                'not_found'          => __('No Table Found', 'ninja-tables'),
                'not_found_in_trash' => __('No Table Found in Trash', 'ninja-tables'),
                'parent'             => __('Parent Table', 'ninja-tables'),
            ),
        );
        $args = apply_filters('ninja_table_post_type_args', $args);
        register_post_type($this->cpt_name, $args);
    }


    /**
     * Adds a settings page link to a menu
     *
     * @link  https://codex.wordpress.org/Administration_Menus
     * @since 1.0.0
     * @return void
     */
    public function add_menu()
    {
        global $submenu;
        $capability = ninja_table_admin_role();

        // Continue only if the current user has
        // the capability to manage ninja tables
        if (!$capability) {
            return;
        }

        // Top-level page
        $menuName = __('NinjaTables', 'ninja-tables');
        if (defined('NINJATABLESPRO')) {
            $menuName .= ' Pro';
        }

        add_menu_page(
            $menuName,
            $menuName,
            $capability,
            'ninja_tables',
            array($this, 'main_page'),
            ninja_table_get_icon_url(),
            25
        );

        $submenu['ninja_tables']['all_tables'] = array(
            __('Tables', 'ninja-tables'),
            $capability,
            'admin.php?page=ninja_tables#/home',
            '',
            'ninja_tables_all_tables'
        );

        $submenu['ninja_tables']['import'] = array(
            __('Import', 'ninja-tables'),
            $capability,
            'admin.php?page=ninja_tables#/tools',
            '',
            'ninja_table_import_menu'
        );

        $submenu['ninja_tables']['tools'] = array(
            __('Tools', 'ninja-tables'),
            $capability,
            'admin.php?page=ninja_tables#/tools',
            '',
            'ninja_table_tools_menu'
        );

	    if (!defined('NINJA_CHARTS_VERSION')) {
		    $submenu['ninja_tables']['ninja_charts'] = array(
			    __('Charts', 'ninja-tables'),
			    $capability,
			    'admin.php?page=ninja_tables#/charts'
		    );
	    } else {
		    $submenu['ninja_tables']['ninja_charts'] = array(
			    __('Charts', 'ninja-tables'),
			    $capability,
			    'admin.php?page=ninja-charts#/chart-list'
		    );

		    $submenu['ninja_tables']['add_chart'] = array(
			    __('Add Chart', 'ninja-tables'),
			    $capability,
			    'admin.php?page=ninja-charts#/add-chart',
		    );
	    }

        if (!defined('NINJATABLESPRO')) {
            $submenu['ninja_tables']['upgrade_pro'] = array(
                __('<span style="color:#f39c12;">Get Pro</span>', 'ninja-tables'),
                $capability,
                'https://wpmanageninja.com/downloads/ninja-tables-pro-add-on/?utm_source=ninja-tables&utm_medium=wp&utm_campaign=wp_plugin&utm_term=upgrade_menu',
                '',
                'ninja_table_upgrade_menu'
            );
        } else if (defined('NINJATABLESPRO_SORTABLE')) {
            $license = get_option('_ninjatables_pro_license_status');
            if ($license != 'valid' && is_multisite()) {
                $license = get_network_option(get_main_network_id(), '_ninjatables_pro_license_status');
            }

            if ($license != 'valid') {
                $text = 'Activate License';
                if ($license == 'expired') {
                    $text = 'Renew License';
                }

                $submenu['ninja_tables']['activate_license'] = array(
                    '<span style="color:#f39c12;">' . $text . '</span>',
                    $capability,
                    'admin.php?page=ninja_tables#/tools/licensing',
                    '',
                    'ninja_table_license_menu'
                );


            }
        }

	    ninjaTablesAdminPrintStyles();

        $submenu['ninja_tables']['help'] = array(
            __('Help', 'ninja-tables'),
            $capability,
            'admin.php?page=ninja_tables#/help',
            '',
            'ninja_tables_help'
        );
    }

    public function main_page()
    {
        include(plugin_dir_path(__FILE__) . 'partials/wp_data_tables_display.php');
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        $vendorSrc = plugin_dir_url(__DIR__) . "assets/css/ninja-tables-vendor.css";

        if (is_rtl()) {
            $vendorSrc = plugin_dir_url(__DIR__) . "assets/css/ninja-tables-vendor-rtl.css";
        }

        wp_enqueue_style(
            $this->plugin_name . '-vendor',
            $vendorSrc,
            [],
            $this->version,
            'all'
        );

        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__DIR__) . "assets/css/ninja-tables-admin.css",
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        if (function_exists('wp_enqueue_editor')) {
            add_filter('user_can_richedit', function ($status) {
                return true;
            });
            wp_enqueue_editor();
            wp_enqueue_script('thickbox');
        }
        if (function_exists('wp_enqueue_media')) {
            wp_enqueue_media();
        }

        wp_enqueue_script(
            'ninja_table_boot',
            plugin_dir_url(__DIR__) . "assets/js/ninja-tables-boot.js",
            array('jquery'),
            $this->version,
            false
        );

        do_action('ninja_tables_loaded_boot_script');

        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__DIR__) . "assets/js/ninja-tables-admin." . NINJA_TABLES_ASSET_VERSION . ".js",
            array('jquery'),
            $this->version,
            true
        );

        $fluentUrl = admin_url('plugin-install.php?s=FluentForm&tab=search&type=term');

        $isInstalled = defined('FLUENTFORM') || defined('NINJATABLESPRO');
        $dismissed = false;
        $dismissedTime = get_option('_ninja_tables_plugin_suggest_dismiss');

        if ($dismissedTime) {
            if ((time() - intval($dismissedTime)) < 518400) {
                $dismissed = true;
            }
        } else {
            $dismissed = true;
            update_option('_ninja_tables_plugin_suggest_dismiss', time() - 345600);
        }

        $currentUser = wp_get_current_user();

        $leadStatus = false;
        $reviewOptinStatus = false;
        $tableCount = wp_count_posts($this->cpt_name);
        $totalPublishedTable = 0;
        if ($tableCount && $tableCount->publish > 1) {
            $leadStatus = apply_filters('ninja_tables_show_lead', $leadStatus);
        }
        if ($tableCount->publish > 2 && !$leadStatus) {
            $reviewOptinStatus = apply_filters('ninja_tables_show_review_optin', $reviewOptinStatus);
        }

        if ($tableCount && $tableCount->publish > 0) {
            $totalPublishedTable = $tableCount->publish;
        }

        $hasFluentFrom = defined('FLUENTFORM_VERSION');
        $isFluentFromUpdated = false;

        // check for right version
        if ($hasFluentFrom) {
            if ($fluentVersionCompare = version_compare(FLUENTFORM_VERSION, '1.7.4') >= 1) {
                $isFluentFromUpdated = true;
            }
        }

        // Let's deregister existing vuejs by other devs
        // Other devs should not regis
        add_action('admin_print_scripts', function () {
            wp_dequeue_script('vuejs');
            wp_dequeue_script('vue');
        });

        if (current_user_can('manage_options')) {
            $isAdmin = 'yes';
        } else {
            $isAdmin = 'no';
        }


        wp_localize_script($this->plugin_name, 'ninja_table_admin', array(
            'img_url'                  => plugin_dir_url(__DIR__) . "assets/img/",
            'fluentform_url'           => $fluentUrl,
            'fluent_wp_url'            => 'https://wordpress.org/plugins/fluentform/',
            'fluent_form_icon'         => getNinjaFluentFormMenuIcon(),
            'dismissed'                => $dismissed,
            'show_lead_pop_up'         => $leadStatus,
            'show_review_dialog'       => $reviewOptinStatus,
            'current_user_name'        => $currentUser->display_name,
            'isInstalled'              => $isInstalled,
            'hasPro'                   => defined('NINJATABLESPRO'),
            'hasFluentForm'            => $hasFluentFrom,
            'isFluentFormUpdated'      => $isFluentFromUpdated,
            'hasAdvancedFilters'       => class_exists('NinjaTablesPro\CustomFilters'),
            'hasSortable'              => defined('NINJATABLESPRO_SORTABLE'),
            'ace_path_url'             => plugin_dir_url(__DIR__) . "assets/libs/ace",
            'upgradeGuide'             => 'https://wpmanageninja.com/r/docs/ninja-tables/how-to-install-and-upgrade/#upgrade',
            'hasValidLicense'          => get_option('_ninjatables_pro_license_status'),
            'i18n'                     => \NinjaTables\Classes\I18nStrings::getStrings(),
            'published_tables'         => $totalPublishedTable,
            'preview_required_scripts' => array(
                plugin_dir_url(__DIR__) . "assets/css/ninjatables-public.css",
                plugin_dir_url(__DIR__) . "public/libs/footable/js/footable.min.js",
                plugin_dir_url(__DIR__) . "public/libs/moment/moment.min.js",
                plugin_dir_url(__DIR__) . "assets/js/ninja-tables-footable." . NINJA_TABLES_ASSET_VERSION . ".js",
            ),
            'activated_features'       => apply_filters('ninja_table_activated_features', array(
                'default_tables'    => true,
                'fluentform_tables' => true
            )),
            'nt_integrity'             => $this->getIntegrity(),
            'admin_notices'            => apply_filters('ninja_dashboard_notices', []),
            'has_sql_permission'       => apply_filters('ninja_table_sql_permission', $isAdmin),
            'prefered_thumb'           => apply_filters('ninja_table_prefered_thumb', 'medium'),
            'has_woocommerce'          => defined('WC_PLUGIN_FILE'),
            'license_status'           => get_option('_ninjatables_pro_license_status'),
            'ninja_charts_url'         => defined('NINJA_CHARTS_VERSION') ? self_admin_url('admin.php?page=ninja-charts#/chart-list') : null
        ));

        // Elementor plugin have a bug where they throw error to parse #url, and I really don't know why they want to parse
        // other plugin's page's uri. They should fix it.
        // For now I am de-registering their script in ninja-table admin pages.
        wp_deregister_script('elementor-admin-app');

        // These last two line is for dumb devs who enqueue their scripts unversally
        // People should think what they are writing in their code 
        wp_dequeue_script('vue');
        wp_dequeue_script('vuejs');

        // We are gonna dequeue every other scripts on our pages.
        add_action('wp_print_scripts', function () {
            if (is_admin()) {
                $skip = apply_filters('ninja_table_skip_no_confict', false);

                if ($skip) return;

                global $wp_scripts;
                $pluginUrl = plugins_url();
                foreach ($wp_scripts->queue as $script) {
                    $src = $wp_scripts->registered[$script]->src;

                    if (strpos($src, $pluginUrl) !== false && !strpos($src, 'ninja-tables') !== false) {
                        wp_dequeue_script($wp_scripts->registered[$script]->handle);
                    }
                }
            }
        }, 1);
    }


    public function enqueue_data_tables_scripts()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'ninja_tables') {
            $this->enqueue_scripts();
            $this->enqueue_styles();
        }
    }

    public function ajax_routes()
    {
        if (!ninja_table_admin_role()) {
            return;
        }

        $valid_routes = array(
            'get-all-tables'           => 'getAllTables',
            'store-a-table'            => 'storeTable',
            'delete-a-table'           => 'deleteTable',
            'update-table-settings'    => 'updateTableSettings',
            'get-table-settings'       => 'getTableSettings',
            'get-table-data'           => 'getTableData',
            'store-table-data'         => 'storeData',
            'edit-data'                => 'editData',
            'delete-data'              => 'deleteData',
            'duplicate-table'          => 'duplicateTable',
            'export-data'              => 'exportData',
            'dismiss_fluent_suggest'   => 'dismissPluginSuggest',
            'save_custom_css_js'       => 'saveCustomCSSJS',
            'get_custom_css_js'        => 'getCustomCSSJS',
            'get_access_roles'         => 'getAccessRoles',
            'get_table_preview_html'   => 'getTablePreviewHtml',
            'set-external-data-source' => 'createTableWithExternalDataSource',
            'get_wp_post_types'        => 'getAllPostTypes',
            'get_wp_post_authors'      => 'getWPPostTypesAuthor',
            'save_wp_post_data_source' => 'createTableWithWPPostDataSource',
            'install_fluent_form'      => 'installFluentForm',
            'get_default_settings'     => 'getDefaultSettings',
            'save_default_settings'    => 'saveDefaultSettings',
            'get_button_settings'      => 'getButtonSettings',
            'update_button_settings'   => 'updateButtonSettings',
            'get_global_settings'      => 'getGlobalSettings',
            'update_global_settings'   => 'updateGlobalSettings',
            'clear_tables_cache'       => 'clearTablesCache',
            'update-single-cell'       => 'updateSingleCell',
            'install-extra-plugins'    => 'installExtraPlugins'
        );

        $importRoutes = array(
            'import-table'             => 'importTable',
            'upload-data'              => 'uploadData',
            'import-table-from-plugin' => 'importTableFromPlugin',
            'get-tables-from-plugin'   => 'getTablesFromPlugin',
        );


        $requested_route = $_REQUEST['target_action'];
        if (isset($valid_routes[$requested_route])) {
            $this->{$valid_routes[$requested_route]}();
        } else if (isset($importRoutes[$requested_route])) {
            $tableImport = new \NinjaTables\Classes\NinjaTableImport();
            $tableImport->{$importRoutes[$requested_route]}();
        }

        wp_die();
    }

    public function getAllTables()
    {
        $perPage = intval($_REQUEST['per_page']) ?: 10;

        $currentPage = intval($_GET['page']);

        $skip = $perPage * ($currentPage - 1);

        $args = array(
            'posts_per_page' => $perPage,
            'offset'         => $skip,
            'orderby'        => $_REQUEST['orderBy'],
            'order'          => $_REQUEST['order'],
            'post_type'      => $this->cpt_name,
            'post_status'    => 'any',
        );

        if (isset($_REQUEST['search']) && $_REQUEST['search']) {
            $args['s'] = sanitize_text_field($_REQUEST['search']);
        }

        $tables = get_posts($args);

        foreach ($tables as $table) {
            $table->preview_url = site_url('?ninjatable_preview=' . $table->ID);
            $dataSourceType = ninja_table_get_data_provider($table->ID);
            $table->dataSourceType = $dataSourceType;
            if ($dataSourceType == 'fluent-form') {
                $fluentFormFormId = get_post_meta($table->ID, '_ninja_tables_data_provider_ff_form_id', true);
                if ($fluentFormFormId) {
                    $table->fluentfrom_url = admin_url('admin.php?page=fluent_forms&route=entries&form_id=' . $fluentFormFormId);
                }
            } else if ($dataSourceType == 'csv' || $dataSourceType == 'google-csv') {
                $table->remoteURL = get_post_meta($table->ID, '_ninja_tables_data_provider_url', true);
            }
        }

        $tables = apply_filters('ninja_tables_get_all_tables', $tables);

        $total = wp_count_posts('ninja-table');
        $total = intval($total->publish);
        $lastPage = ceil($total / $perPage);

        wp_send_json(array(
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $currentPage,
            'last_page'    => ($lastPage) ? $lastPage : 1,
            'data'         => $tables,
        ), 200);
    }

    public function storeTable()
    {
        if (!$_REQUEST['post_title']) {
            wp_send_json_error(array(
                'message' => __('The name field is required.', 'ninja-tables')
            ), 423);
        }

        $postId = intval($_REQUEST['tableId']);

        if (isset($_REQUEST['table_caption'])) {
            update_post_meta($postId, '_ninja_table_caption', sanitize_text_field($_REQUEST['table_caption']));
        }

        wp_send_json(array(
            'table_id' => $this->saveTable($postId),
            'message'  => __('Table ' . ($postId ? 'updated' : 'created') . ' successfully.', 'ninja-tables')
        ), 200);
    }

    protected function saveTable($postId = null)
    {
        $attributes = array(
            'post_title'   => sanitize_text_field($_REQUEST['post_title']),
            'post_content' => wp_kses_post($_REQUEST['post_content']),
            'post_type'    => $this->cpt_name,
            'post_status'  => 'publish'
        );

        if (!$postId) {
            $postId = wp_insert_post($attributes);
        } else {
            $attributes['ID'] = $postId;
            wp_update_post($attributes);
        }
        update_post_meta($postId, '_last_edited_by', get_current_user_id());
        update_post_meta($postId, '_last_edited_time', date('Y-m-d H:i:s'));

        return $postId;
    }

    public function saveCustomCSSJS()
    {
        $tableId = intval($_REQUEST['table_id']);
        $css = $_REQUEST['custom_css'];
        $js = $_REQUEST['custom_js'];
        $css = wp_strip_all_tags($css);
        //   $js = wp_unslash($js);
        update_post_meta($tableId, '_ninja_tables_custom_css', $css);
        update_post_meta($tableId, '_ninja_tables_custom_js', $js);

        wp_send_json_success(array(
            'message' => 'Custom CSS and JS successfully saved'
        ), 200);
    }

    public function getCustomCSSJS()
    {
        $tableId = intval($_REQUEST['table_id']);
        wp_send_json_success(array(
            'custom_css' => get_post_meta($tableId, '_ninja_tables_custom_css', true),
            'custom_js'  => get_post_meta($tableId, '_ninja_tables_custom_js', true),
        ), 200);
    }

    public function getTableSettings()
    {
        $table = get_post($tableID = intval($_REQUEST['table_id']));
        if (!$table || $table->post_type != 'ninja-table') {
            wp_send_json_error(array(
                'message' => __('No Table Found'),
                'route'   => 'home'
            ), 423);
        }
        $provider = ninja_table_get_data_provider($table->ID);

        $table = apply_filters('ninja_tables_get_table_' . $provider, $table);

        $table->table_caption = get_post_meta($tableID, '_ninja_table_caption', true);

        $table->custom_css = get_post_meta($tableID, '_ninja_tables_custom_css', true);

        $this->checkDBMigrations();

        wp_send_json(array(
            'preview_url' => site_url('?ninjatable_preview=' . $tableID),
            'columns'     => ninja_table_get_table_columns($tableID, 'admin'),
            'settings'    => ninja_table_get_table_settings($tableID, 'admin'),
            'table'       => $table,
        ), 200);
    }

    public function updateTableSettings()
    {
        $tableId = intval($_REQUEST['table_id']);

        $tableColumns = array();

        if (isset($_REQUEST['columns'])) {
            $rawColumns = $_REQUEST['columns'];
            if ($rawColumns && is_array($rawColumns)) {
                foreach ($rawColumns as $column) {
                    foreach ($column as $column_index => $column_value) {
                        if ($column_index == 'header_html_content' || $column_index == 'selections' || $column_index == 'wp_post_custom_data_value') {
                            $column[$column_index] = wp_kses_post($column_value);
                        } else {
                            if (is_array($column_value)) {
                                $column[$column_index] = ninja_tables_sanitize_array($column_value);
                            } else if (is_int($column_value)) {
                                $column[$column_index] = intval($column_value);
                            } else if (is_bool($column_value)) {
                                $column[$column_index] = $column_value;
                            }
                        }
                    }
                    $tableColumns[] = $column;
                }
                $tableColumns = apply_filters('ninja_table_update_columns_' . ninja_table_get_data_provider($tableId), $tableColumns, $rawColumns, $tableId);
                do_action('ninja_table_before_update_columns_' . ninja_table_get_data_provider($tableId), $tableColumns, $rawColumns, $tableId);
                update_post_meta($tableId, '_ninja_table_columns', $tableColumns);
            }
        }

        $formattedTablePreference = array();

        if (isset($_REQUEST['table_settings'])) {
            $tablePreference = $_REQUEST['table_settings'];
            if ($tablePreference && is_array($tablePreference)) {
                $formattedTablePreference = ninjaTableNormalize($tablePreference);

                update_post_meta($tableId, '_ninja_table_settings', $formattedTablePreference);
            }
        }

        ninjaTablesClearTableDataCache($tableId);

        update_post_meta($tableId, '_last_edited_by', get_current_user_id());
        update_post_meta($tableId, '_last_edited_time', date('Y-m-d H:i:s'));

        wp_send_json(array(
            'message'  => __('Successfully updated configuration.', 'ninja-tables'),
            'columns'  => $tableColumns,
            'settings' => $formattedTablePreference
        ), 200);
    }

    public function getTable()
    {
        $tableId = intval($_REQUEST['id']);
        $table = get_post($tableId);

        if ($table) {
            $table->table_caption = get_post_meta($tableId, '_ninja_table_caption', true);
        }

        wp_send_json(array(
            'data' => $table
        ), 200);
    }

    public function deleteTable()
    {
        $tableId = intval($_REQUEST['table_id']);

        if (get_post_type($tableId) != $this->cpt_name) {
            wp_send_json(array(
                'message' => __('Invalid Table to Delete', 'ninja-tables')
            ), 300);
        }


        wp_delete_post($tableId, true);
        // Delete the post metas
        delete_post_meta($tableId, '_ninja_table_columns');
        delete_post_meta($tableId, '_ninja_table_settings');
        delete_post_meta($tableId, '_ninja_table_cache_object');
        // now delete the data
        try {
            ninja_tables_DbTable()->where('table_id', $tableId)->delete();
        } catch (Exception $e) {
            //
        }

        wp_send_json(array(
            'message' => __('Successfully deleted the table.', 'ninja-tables')
        ), 200);
    }

    public function getTableData()
    {
        $perPage = intval($_REQUEST['per_page']) ? intval($_REQUEST['per_page']) : 10;
        $currentPage = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $skip = $perPage * ($currentPage - 1);
        $tableId = intval($_REQUEST['table_id']);
        $search = esc_attr($_REQUEST['search']);

        $dataSourceType = ninja_table_get_data_provider($tableId);
        if ($dataSourceType == 'default') {
            list($orderByField, $orderByType) = $this->getTableSortingParams($tableId);

            $query = ninja_tables_DbTable()->where('table_id', $tableId);
            if ($search) {
                $query->search($search, array('value'));
            }
            $data = $query->take($perPage)
                ->skip($skip)
                ->limit($perPage)
                ->orderBy($orderByField, $orderByType)
                ->get();

            $total = ninja_tables_DbTable()->where('table_id', $tableId)->count();

            $response = array();

            $hasSettings = true;
            foreach ($data as $item) {
                $hasSettings = property_exists($item, 'settings');
                $settings = (object)array();
                if ($hasSettings) {
                    $settings = maybe_unserialize($item->settings);
                    if (!is_array($settings)) {
                        $settings = (object)array();
                    }
                }
                $createdBy = '';
                if (property_exists($item, 'owner_id')) {
                    $userInfo = get_userdata($item->owner_id);
                    if ($userInfo && property_exists($userInfo->data, 'display_name')) {
                        $createdBy = $userInfo->data->display_name;
                    }
                }
                $response[] = array(
                    'id'         => $item->id,
                    'created_at' => $item->created_at,
                    'settings'   => $settings,
                    'created_by' => $createdBy,
                    'position'   => property_exists($item, 'position') ? $item->position : null,
                    'values'     => json_decode($item->value, true)
                );
            }

            if (!$hasSettings) {
                // We have to migrate the data now
            }

        } else {
            list($response, $total) = apply_filters(
                'ninja_tables_get_table_data_' . $dataSourceType,
                array(array(), 0),
                $tableId,
                $perPage,
                $skip
            );
        }

        // Needed for other data source providers
        list($response, $total) = apply_filters(
            'ninja_tables_get_table_data',
            array($response, $total),
            $tableId,
            $perPage,
            $skip
        );

        wp_send_json(array(
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $currentPage,
            'last_page'    => ceil($total / $perPage),
            'data'         => $response,
            'data_source'  => $dataSourceType
        ), 200);
    }

    /**
     * Get the order by field and order by type values.
     *
     * @param        $tableId
     * @param  null $tableSettings
     *
     * @return array
     */
    protected function getTableSortingParams($tableId, $tableSettings = null)
    {
        $tableSettings = $tableSettings ?: ninja_table_get_table_settings($tableId, 'admin');

        $orderByField = 'created_at';
        $orderByType = 'DESC';

        if (isset($tableSettings['sorting_type'])) {
            if ($tableSettings['sorting_type'] === 'manual_sort') {
                $orderByField = 'position';
                $orderByType = 'ASC';
            } elseif ($tableSettings['sorting_type'] === 'by_created_at') {
                $orderByField = 'created_at';
                if ($tableSettings['default_sorting'] === 'new_first') {
                    $orderByType = 'DESC';
                } else {
                    $orderByType = 'ASC';
                }
            }
        }
        return [$orderByField, $orderByType];
    }

    public function storeData()
    {
        $tableId = intval($_REQUEST['table_id']);
        $row = $_REQUEST['row'];
        $formattedRow = array();

        foreach ($row as $key => $item) {
            $formattedRow[$key] = wp_unslash($item);
        }

        $attributes = array(
            'table_id'   => $tableId,
            'attribute'  => 'value',
            'value'      => json_encode($formattedRow, JSON_UNESCAPED_UNICODE),
            'owner_id'   => get_current_user_id(),
            'updated_at' => date('Y-m-d H:i:s')
        );

        if (isset($_REQUEST['settings'])) {
            $attributes['settings'] = maybe_serialize(wp_unslash($_REQUEST['settings']));
        }

        if (isset($_REQUEST['created_at']) && $_REQUEST['created_at']) {
            $attributes['created_at'] = sanitize_text_field($_REQUEST['created_at']);
        }

        if ($id = intval($_REQUEST['id'])) {
            do_action('ninja_table_before_update_item', $id, $tableId, $attributes);
            ninja_tables_DbTable()->where('id', $id)->update($attributes);
            do_action('ninja_table_after_update_item', $id, $tableId, $attributes);
        } else {
            if (isset($_REQUEST['insert_after_id'])) {
                list($orderByField, $orderByType) = $this->getTableSortingParams($tableId);
                if ($orderByField == 'created_at') {
                    // Calculate the insert position Date
                    $prevItemId = absint($_REQUEST['insert_after_id']);
                    $previousItem = ninja_tables_DbTable()
                        ->where('id', $prevItemId)
                        ->first();
                    if ($previousItem) {
                        if ($orderByType == 'ASC') {
                            // ASC means, We have to minus time to created_at
                            $newDateStamp = strtotime($previousItem->created_at) + 1;
                        } else {
                            $newDateStamp = strtotime($previousItem->created_at) - 1;
                        }
                        $attributes['created_at'] = date('Y-m-d H:i:s', $newDateStamp);
                        $this->fixCreatedAtDate($tableId, $previousItem->created_at, $orderByType);
                    }
                }
            }

            if (!isset($attributes['created_at'])) {
                $attributes['created_at'] = date('Y-m-d H:i:s');
            }

            $attributes = apply_filters('ninja_tables_item_attributes', $attributes);

            do_action('ninja_table_before_add_item', $tableId, $attributes);
            $id = $insertId = ninja_tables_DbTable()->insert($attributes);
            do_action('ninja_table_after_add_item', $insertId, $tableId, $attributes);
        }

        $item = ninja_tables_DbTable()->find($id);

        ninjaTablesClearTableDataCache($tableId);

        update_post_meta($tableId, '_last_edited_by', get_current_user_id());
        update_post_meta($tableId, '_last_edited_time', date('Y-m-d H:i:s'));

        $itemSettings = maybe_unserialize($item->settings);
        if (!is_array($itemSettings)) {
            $itemSettings = (object)array();
        }

        wp_send_json(array(
            'message' => __('Successfully saved the data.', 'ninja-tables'),
            'item'    => array(
                'id'         => $item->id,
                'values'     => $formattedRow,
                'row'        => json_decode($item->value),
                'created_at' => $item->created_at,
                'settings'   => $itemSettings,
                'position'   => property_exists($item, 'position') ? $item->position : null
            )
        ), 200);
    }

    public function updateSingleCell()
    {
        $rowId = intval($_REQUEST['row_id']);
        $columnKey = sanitize_text_field($_REQUEST['column_key']);
        $columnValue = wp_unslash($_REQUEST['column_value']);

        // get The row first
        $row = ninja_tables_DbTable()
            ->where('id', $rowId)
            ->first();

        $values = json_decode($row->value, true);
        $values[$columnKey] = $columnValue;
        ninja_tables_DbTable()
            ->where('id', $rowId)
            ->update([
                'value'      => json_encode($values, JSON_UNESCAPED_UNICODE),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        ninjaTablesClearTableDataCache($row->table_id);
        update_post_meta($row->table_id, '_last_edited_by', get_current_user_id());
        update_post_meta($row->table_id, '_last_edited_time', date('Y-m-d H:i:s'));


        wp_send_json_success([
            'message' => 'Cell successfully updated'
        ]);
    }

    private function fixCreatedAtDate($tableId, $refDate, $orderType)
    {
        global $wpdb;
        $tableName = $wpdb->prefix . ninja_tables_db_table_name();
        if ($orderType == 'ASC') {
            $query = "UPDATE {$tableName}
                  SET created_at = ADDTIME(created_at, 2)
                  WHERE table_id = %d
                  AND created_at > %s";
        } else {
            $orderType = 'DESC';
            $query = "UPDATE {$tableName}
                  SET created_at = SUBTIME(created_at, 2)
                  WHERE table_id = %d
                  AND created_at < %s";
        }

        $bindings = [
            $tableId,
            $refDate
        ];
        $query .= " ORDER BY created_at " . $orderType;
        $wpdb->query($wpdb->prepare($query, $bindings));
    }

    public function deleteData()
    {
        $tableId = intval($_REQUEST['table_id']);

        $id = $_REQUEST['id'];

        $ids = is_array($id) ? $id : array($id);

        $ids = array_map(function ($item) {
            return intval($item);
        }, $ids);

        add_action('ninja_table_before_items_deleted', $ids, $tableId);
        ninja_tables_DbTable()->where('table_id', $tableId)->whereIn('id', $ids)->delete();
        add_action('ninja_table_after_items_deleted', $ids, $tableId);

        ninjaTablesClearTableDataCache($tableId);

        wp_send_json(array(
            'message' => __('Successfully deleted data.', 'ninja-tables')
        ), 200);
    }

    public function exportData()
    {
        $format = esc_attr($_REQUEST['format']);

        $tableId = intval($_REQUEST['table_id']);

        $tableTitle = get_the_title($tableId);

        $fileName = sanitize_title($tableTitle, 'Export-Table-' . date('Y-m-d-H-i-s'), 'preview');

        $tableColumns = ninja_table_get_table_columns($tableId, 'admin');

        $tableSettings = ninja_table_get_table_settings($tableId, 'admin');

        if ($format == 'csv') {

            $sortingType = ArrayHelper::get($tableSettings, 'sorting_type', 'by_created_at');

	        $tableColumns = ninja_table_get_table_columns($tableId, 'admin');
            $data = ninjaTablesGetTablesDataByID($tableId, $tableColumns, $sortingType, true);

            $header = array();

            foreach ($tableColumns as $item) {
                $header[$item['key']] = $item['name'];
            }

            $exportData = array();

            foreach ($data as $item) {
                $temp = array();
                foreach ($header as $accessor => $name) {
                    $value = \NinjaTables\Classes\ArrayHelper::get($item, $accessor);
                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }
                    $temp[] = $value;
                }
                array_push($exportData, $temp);
            }
            $this->exportAsCSV(array_values($header), $exportData, $fileName . '.csv');
        } elseif ($format == 'json') {
            $table = get_post($tableId);

            $dataProvider = ninja_table_get_data_provider($tableId);
            $rows = array();
            if ($dataProvider == 'default') {
                $rawRows = ninja_tables_DbTable()
                    ->select(array('position', 'owner_id', 'attribute', 'value', 'settings', 'created_at', 'updated_at'))
                    ->where('table_id', $tableId)
                    ->get();
                foreach ($rawRows as $row) {
                    $row->value = json_decode($row->value, true);
                    $rows[] = $row;
                }
            }

            $matas = get_post_meta($tableId);
            $allMeta = array();

            $excludedMetaKeys = array(
                '_ninja_table_cache_object',
                '_ninja_table_cache_html',
                '_external_cached_data',
                '_last_external_cached_time',
                '_last_edited_by',
                '_last_edited_time',
                '__ninja_cached_table_html'
            );

            foreach ($matas as $metaKey => $metaValue) {
                if (!in_array($metaKey, $excludedMetaKeys)) {
                    if (isset($metaValue[0])) {
                        $metaValue = maybe_unserialize($metaValue[0]);
                        $allMeta[$metaKey] = $metaValue;
                    }
                }
            }

            $exportData = array(
                'post'          => $table,
                'columns'       => $tableColumns,
                'settings'      => $tableSettings,
                'data_provider' => $dataProvider,
                'metas'         => $allMeta,
                'rows'          => array(),
                'original_rows' => $rows
            );
            $this->exportAsJSON($exportData, $fileName . '.json');
        }
    }

    private function exportAsCSV($header, $data, $fileName = null)
    {
        $fileName = ($fileName) ? $fileName : 'export-data-' . date('d-m-Y') . '.csv';

        $writer = \League\Csv\Writer::createFromFileObject(new SplTempFileObject());
        $writer->setDelimiter(",");
        $writer->setNewline("\r\n");
        $writer->insertOne($header);
        $writer->insertAll($data);
        $writer->output($fileName);
        die();
    }

    private function exportAsJSON($data, $fileName = null)
    {
        $fileName = ($fileName) ? $fileName : 'export-data-' . date('d-m-Y') . '.json';

        header('Content-disposition: attachment; filename=' . $fileName);

        header('Content-type: application/json');

        echo json_encode($data);

        die();
    }

    public function add_tabales_to_editor()
    {
        $pages_with_editor_button = array('post.php', 'post-new.php');
        foreach ($pages_with_editor_button as $editor_page) {
            add_action("load-{$editor_page}", array($this, 'init_ninja_mce_buttons'));
        }
    }

    public function init_ninja_mce_buttons()
    {
        if (!user_can_richedit()) {
            return;
        }
        add_filter("mce_external_plugins", array($this, 'ninja_table_add_button'));
        add_filter('mce_buttons', array($this, 'ninja_table_register_button'));
        add_action('admin_footer', array($this, 'pushNinjaTablesToEditorFooter'));
    }

    public function pushNinjaTablesToEditorFooter()
    {
        $tables = $this->getAllTablesForMce();
        ?>
        <script type="text/javascript">
            window.ninja_tables_tiny_mce = {
                label: '<?php _e('Select a Table to insert', 'ninja-tables') ?>',
                title: '<?php _e('Insert Ninja Tables Shortcode', 'ninja-tables') ?>',
                select_error: '<?php _e('Please select a table'); ?>',
                insert_text: '<?php _e('Insert Shortcode', 'ninja-tables'); ?>',
                tables: <?php echo json_encode($tables);?>,
                logo: <?php echo json_encode(NINJA_TABLES_DIR_URL . 'assets/img/ninja-table-editor-button-2x.png');?>
            }
        </script>
        <?php
    }

    private function getAllTablesForMce()
    {
        $args = array(
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post_type'      => $this->cpt_name,
            'post_status'    => 'any'
        );

        $tables = get_posts($args);
        $formatted = array();

        $title = __('Select a Table', 'ninja-tables');
        if (!$tables) {
            $title = __('No Tables found. Please add a table first');
        }
        $formatted[] = array(
            'text'  => $title,
            'value' => ''
        );

        foreach ($tables as $table) {
            $formatted[] = array(
                'text'  => $table->post_title,
                'value' => $table->ID
            );
        }

        return $formatted;
    }

    /**
     * add a button to Tiny MCE editor
     *
     * @param $plugin_array
     *
     * @return mixed
     */
    public function ninja_table_add_button($plugin_array)
    {
        $plugin_array['ninja_table'] = NINJA_TABLES_DIR_URL . 'assets/js/ninja-table-tinymce-button.js';

        return $plugin_array;
    }

    /**
     * register a button to Tiny MCE editor
     *
     * @param $buttons
     *
     * @return mixed
     */
    public function ninja_table_register_button($buttons)
    {
        array_push($buttons, 'ninja_table');

        return $buttons;
    }

    public function dismissPluginSuggest()
    {
        update_option('_ninja_tables_plugin_suggest_dismiss', time());
    }

    /**
     * Save a flag if the a post/page/cpt have [ninja_tables] shortcode
     *
     * @param int $post_id
     *
     * @return void
     */
    public function saveNinjaTableFlagOnShortCode($post_id)
    {
        if (isset($_POST['post_content'])) {
            $post_content = $_POST['post_content'];
        } else {
            $post = get_post($post_id);
            $post_content = $post->post_content;
        }

        $ids = ninjaTablesGetShortCodeIds($post_content);

        if ($ids) {
            update_post_meta($post_id, '_has_ninja_tables', $ids);
        } elseif (get_post_meta($post_id, '_has_ninja_tables', true)) {
            update_post_meta($post_id, '_has_ninja_tables', 0);
        }
    }

    public function duplicateTable()
    {
        $oldPostId = intval($_REQUEST['tableId']);

        $this->checkDBMigrations();
        $post = get_post($oldPostId);

        // Duplicate table itself.
        $attributes = array(
            'post_title'   => $post->post_title . '( Duplicate )',
            'post_content' => $post->post_content,
            'post_type'    => $post->post_type,
            'post_status'  => 'publish'
        );

        $newPostId = wp_insert_post($attributes);

        global $wpdb;

        // Duplicate table settings.
        $postMetas = get_post_meta($oldPostId);

        foreach ($postMetas as $metaKey => $metaValue) {
            update_post_meta($newPostId, $metaKey, maybe_unserialize($metaValue[0]));
        }

        // Duplicate table rows.
        $itemsTable = $wpdb->prefix . ninja_tables_db_table_name();

        $sql = "INSERT INTO $itemsTable (`position`, `table_id`, `owner_id`, `settings`, `attribute`, `value`, `created_at`, `updated_at`)";
        $sql .= " SELECT `position`, $newPostId, `owner_id`, `settings`, `attribute`, `value`, `created_at`, `updated_at` FROM $itemsTable";
        $sql .= " WHERE `table_id` = $oldPostId";

        $wpdb->query($sql);

        wp_send_json_success(array(
            'message'  => __('Successfully duplicated table.', 'ninja-tables'),
            'table_id' => $newPostId
        ), 200);
    }

    public function getAccessRoles()
    {
        $roles = get_editable_roles();
        $formatted = array();
        $excludedRoles = array('subscriber', 'administrator');
        foreach ($roles as $key => $role) {
            if (!in_array($key, $excludedRoles)) {
                $formatted[] = array(
                    'name' => $role['name'],
                    'key'  => $key
                );
            }
        }

        $capability = get_option('_ninja_tables_permission');

        if (is_string($capability)) {
            $capability = [];
        }

        wp_send_json(array(
            'capability'     => $capability,
            'roles'          => $formatted,
            'sql_permission' => get_option('_ninja_tables_sql_permission')
        ), 200);
    }

    public function getTablePreviewHtml()
    {
        $tableId = intval($_REQUEST['table_id']);
        $tableColumns = ninja_table_get_table_columns($tableId, 'public');
        $tableSettings = ninja_table_get_table_settings($tableId, 'public');

        $formattedColumns = [];
        foreach ($tableColumns as $index => $column) {
            $formattedColumn = NinjaFooTable::getFormattedColumn($column, $index, $tableSettings, true,
                'by_created_at');
            $formattedColumn['original'] = $column;
            $formattedColumns[] = $formattedColumn;
        }

        $formatted_data = ninjaTablesGetTablesDataByID($tableId, $tableColumns, $tableSettings['default_sorting'], true, 25);

        if (count($formatted_data) > 25) {
            $formatted_data = array_slice($formatted_data, 0, 25);
        }

        echo self::loadView('public/views/table_inner_html', array(
            'table_columns' => $formattedColumns,
            'table_rows'    => $formatted_data
        ));
    }

    private static function loadView($file, $data)
    {
        $file = NINJA_TABLES_DIR_PATH . $file . '.php';
        ob_start();
        extract($data);
        include $file;

        return ob_get_clean();
    }

    public function checkDBMigrations()
    {
        $firstRow = ninja_tables_DbTable()->first();
        if (!$firstRow) {
            if (get_option('_ninja_table_db_settings_owner_id')) {
                return true;
            }
            $this->migrateSettingColumnIfNeeded();
            $this->migrateOwnerColumnIfNeeded();
            update_option('_ninja_table_db_settings_owner_id', true);
            return true;
        }
        if (!property_exists($firstRow, 'owner_id')) {
            $this->migrateOwnerColumnIfNeeded();
        }
        if (!property_exists($firstRow, 'settings')) {
            $this->migrateSettingColumnIfNeeded();
        }
        if (!property_exists($firstRow, 'position')) {
            $this->migratePositionDatabase();
        }
        return true;
    }

    public function migratePositionDatabase()
    {
        // If the database is already migrated for manual
        // sorting the option table would have a flag.
        $option = '_ninja_tables_sorting_migration';
        global $wpdb;
        $tableName = $wpdb->prefix . ninja_tables_db_table_name();

        // Update the databse to hold the sorting position number.
        $sql = "ALTER TABLE $tableName ADD COLUMN `position` INT(11) AFTER `id`;";

        $wpdb->query($sql);
        // Keep a flag on the options table that the
        // db is migrated to use for manual sorting.
        update_option($option, true);
    }

    private function migrateSettingColumnIfNeeded()
    {
        global $wpdb;
        $tableName = $wpdb->prefix . ninja_tables_db_table_name();
        // Update the databse to hold the sorting position number.
        $sql = "ALTER TABLE $tableName ADD COLUMN `settings` LONGTEXT AFTER `value`;";
        ob_start();
        $wpdb->query($sql);
        $maybeError = ob_get_clean();
        update_option('_ninja_tables_settings_migration', true);
    }

    private function migrateOwnerColumnIfNeeded()
    {
        global $wpdb;
        $tableName = $wpdb->prefix . ninja_tables_db_table_name();
        // Update the databse to hold the sorting position number.
        $sql = "ALTER TABLE $tableName ADD COLUMN `owner_id` int(11) AFTER `table_id`;";
        ob_start();
        $wpdb->query($sql);
        $maybeError = ob_get_clean();
    }

    public function getAllPostTypes()
    {
        global $wpdb;

        $postStatuses = ninjaTablesGetPostStatuses();
        $post_fields = $wpdb->get_col("DESC {$wpdb->prefix}posts");

        $publicPostTypes = get_post_types(array(
            'public' => true
        ));

        $excludedTypes = apply_filters('ninja_table_excluded_post_types', array(
            'ninja-table',
            'revision',
            'nav_menu_item',
            'oembed_cache',
            'user_request',
            'acf-field-group',
            'acf-field'
        ));

        $all_post_types = array_diff(get_post_types(), $excludedTypes);

        $post_types = array(
            'public'  => array(),
            'private' => array()
        );

        foreach ($all_post_types as $type) {
            $taxonomies = get_object_taxonomies($type);
            $taxonomies = array_combine($taxonomies, $taxonomies);

            foreach ($taxonomies as $taxonomy) {
                $taxonomies[$taxonomy] = get_terms([
                    'taxonomy'   => $taxonomy,
                    'hide_empty' => false,
                ]);
            }

            $status = isset($publicPostTypes[$type]) ? 'public' : 'private';

            $post_types[$status][$type] = array(
                'status'     => $status,
                'taxonomies' => $taxonomies,
                'fields'     => array_map(function ($taxonomy) use ($type) {
                    return "{$type}.{$taxonomy}";
                }, array_keys($taxonomies))
            );
        }

        if ($post_types['private']) {
            $post_types = array_merge($post_types['public'], $post_types['private']);
        } else {
            $post_types = $post_types['public'];
        }

        wp_send_json_success(
            compact('post_fields', 'post_types', 'postStatuses'), 200
        );
    }

    public function getWPPostTypesAuthor()
    {
        $authors = array();
        if (isset($_REQUEST['post_types'])) {
            $postTypes = ninja_tables_sanitize_array($_REQUEST['post_types']);
            if ($postTypes) {
                global $wpdb;
                $postTypes = implode("','", $postTypes);
                $authors = $wpdb->get_results("SELECT {$wpdb->prefix}users.ID, {$wpdb->prefix}users.display_name FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}users ON {$wpdb->prefix}users.ID = {$wpdb->prefix}posts.post_author WHERE {$wpdb->prefix}posts.post_type IN ('" . $postTypes . "') GROUP BY {$wpdb->prefix}posts.post_author");
            }
        }
        wp_send_json_success(array(
            'authors' => $authors
        ));
    }

    public function installFluentForm()
    {
        if (!current_user_can('install_plugins')) {
            wp_send_json_error(array(
                'message' => __('You do not have permission to install a plugin, Please ask your administrator to install WP Fluent Form')
            ), 423);
            return;
        }

        if (is_multisite()) {
            wp_send_json_error(array(
                'message' => __('You are using wp multisite environment so please install WP FluentForm manually')
            ), 423);
            return;
        }

        $result = $this->install_plugin('fluentform', 'fluentform.php');
        $status = !is_wp_error($result);

        if ($status) {
            wp_send_json_success(array(
                'message'      => __('WP Fluent Form successfully installed and activated, You are redirecting to WP Fluent Form Now'),
                'redirect_url' => admin_url('admin.php?page=fluent_forms')
            ), 200);
            return;
        } else {
            wp_send_json_error(array(
                'message' => __('There was an error to install the plugin. Please install the plugin manually.')
            ), 423);
            return;
        }
    }

    public function install_plugin($slug, $file)
    {
        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        $plugin_basename = $slug . '/' . $file;

        // if exists and not activated
        if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_basename)) {
            return activate_plugin($plugin_basename);
        }

        // seems like the plugin doesn't exists. Download and activate it
        $upgrader = new Plugin_Upgrader(new WP_Ajax_Upgrader_Skin());

        $api = plugins_api('plugin_information', array('slug' => $slug, 'fields' => array('sections' => false)));
        $result = $upgrader->install($api->download_link);

        if (is_wp_error($result)) {
            return $result;
        }

        return activate_plugin($plugin_basename);
    }

    public function getDefaultSettings()
    {
        $settings = getDefaultNinjaTableSettings();
        wp_send_json_success(array(
            'default_settings' => $settings
        ), 200);
    }

    public function saveDefaultSettings()
    {
        $settings = ninjaTableNormalize(wp_unslash($_REQUEST['default_settings']));
        update_option('_ninja_table_default_appearance_settings', $settings);
        wp_send_json_success(array(
            'message' => __('Settings successfully updated', 'ninja-tables')
        ), 200);
    }

    public function getButtonSettings()
    {
        $tableId = absint($_REQUEST['table_id']);
        $tableButtonDefaults = array(
            'csv'              => array(
                'status'     => 'no',
                'label'      => 'CSV',
                'all_rows'   => 'no',
                'bg_color'   => 'rgb(0,0,0)',
                'text_color' => 'rgb(255,255,255)'
            ),
            'print'            => array(
                'status'     => 'no',
                'label'      => 'Print',
                'all_rows'   => 'no',
                'bg_color'   => 'rgb(0,0,0)',
                'text_color' => 'rgb(255,255,255)'
            ),
            'button_position'  => 'after_search_box',
            'button_alignment' => 'ninja_buttons_right'
        );

        $tableButtons = get_post_meta($tableId, '_ninja_custom_table_buttons', true);
        if (!$tableButtons) {
            $tableButtons = array();
        }

        $tableButtons = wp_parse_args($tableButtons, $tableButtonDefaults);

        wp_send_json_success(array(
            'button_settings' => $tableButtons
        ));
    }

    public function updateButtonSettings()
    {
        $tableId = absint($_REQUEST['table_id']);
        $buttonSettings = wp_unslash($_REQUEST['button_settings']);
        update_post_meta($tableId, '_ninja_custom_table_buttons', $buttonSettings);
        wp_send_json_success(array(
            'message' => __('Settings successfully updated', 'ninja-tables')
        ), 200);
    }

    public function add_plugin_action_links($links)
    {

        if (!defined('NINJATABLESPRO')) {
            $links[] = '<a style="color: green; font-weight: bold;" target="_blank" href="https://wpmanageninja.com/downloads/ninja-tables-pro-add-on/">Go Pro</a>';
        }

        $links[] = '<a href="' . admin_url('admin.php?page=ninja_tables#/') . '">' . __('All Tables', 'ninja-tables') . '</a>';

        return $links;
    }

    public function getGlobalSettings()
    {
        $suppressError = get_option('_ninja_suppress_error');
        if (!$suppressError) {
            $suppressError = 'no';
        }

        wp_send_json_success(array(
            'ninja_suppress_error' => $suppressError
        ), 200);
    }

    public function updateGlobalSettings()
    {
        $errorHandling = sanitize_text_field($_REQUEST['suppress_error']);
        update_option('_ninja_suppress_error', $errorHandling, true);
        wp_send_json_success(array(
            'message' => __('Settings successfully updated', 'ninja-tables')
        ), 200);
    }


    private function getIntegrity()
    {
        if (defined('NINJATABLESPRO')) {
            if (is_multisite()) {
                return 'valid';
            }
            $status = get_option('_ninjatables_pro_license_status');
            if (is_multisite() && $status != 'valid') {
                $status = get_network_option(get_main_network_id(), '_ninjatables_pro_license_status');
            }
            if ($status == 'valid') {
                $key = get_option('_ninjatables_pro_license_key');
                if (is_multisite()) {
                    $key = get_network_option(get_main_network_id(), '_ninjatables_pro_license_key');
                }
                $length = strlen($key);
                if ($length < 20) {
                    return apply_filters('ninja_table_integrity', 'nope');
                }
            }
        }
        return apply_filters('ninja_table_integrity', 'valid');
    }


    public function clearTablesCache()
    {
        ninja_table_clear_all_cache();
        wp_send_json_success(array(
            'message' => __('All Cache successfully cleared', 'ninja_tables')
        ), 200);
    }

	/**
	 * Installs extra plugins when necessary.
	 */
	private function installExtraPlugins()
	{
		$plugin = [
			'name'      => 'Ninja Charts',
			'repo-slug' => 'ninja-charts',
			'file'      => 'ninja-charts.php',
            'redirect'  => self_admin_url('admin.php?page=ninja-charts#/chart-list')
		];

        (new \NinjaTables\Classes\BackgroundInstaller())->install($plugin);

        wp_send_json_success(array(
	        'message'  => 'Successfully enabled Ninja Charts.',
            'redirect' => $plugin['redirect']
        ));
    }
}
