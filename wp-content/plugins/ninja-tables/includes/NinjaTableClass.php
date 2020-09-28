<?php

namespace NinjaTables\Classes;
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wpmanageninja.com
 * @since      1.0.0
 *
 * @package    Wp_table_data_press
 * @subpackage Wp_table_data_press/includes
 */

use NinjaTable\FrontEnd\NinjaTablePublic;
use NinjaTables\Admin\DeactivationMessage;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    ninja-tables
 * @subpackage ninja-tables/includes
 * @author     Shahjahan Jewel <cep.jewel@gmail.com>
 */
class NinjaTableClass
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      NinjaTablesLoader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        $this->plugin_name = 'ninja-tables';
        $this->version = NINJA_TABLES_VERSION;
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     * Include the following files that make up the plugin:
     *
     * - NinjaTablesLoader. Orchestrates the hooks of the plugin.
     * - Wp_table_data_press_i18n. Defines internationalization functionality.
     * - Wp_table_data_press_Admin. Defines all hooks for the admin area.
     * - Wp_table_data_press_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/NinjaTablesLoader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/NinjaTablesI18n.php';


        /**
         * The class responsible for all global functions.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/ninja_tables-global-functions.php';

        /**
         * Include Libs
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/libs/autoload.php';

        /**
         * Extorior Page
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/ProcessDemoPage.php';


        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/NinjaTablesAdmin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/NinjaTableImport.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/BackgroundInstaller.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/DeactivationMessage.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/NinjaTablePublic.php';

        /**
         * The class is responsible for providing data for the table (default data source).
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/dataProviders/DefaultProvider.php';

        /**
         * The class is responsible for providing external data (FluentForm Data Source).
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/dataProviders/FluentFormProvider.php';


        /**
         * Load Tables Migration Class
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/libs/Migrations/NinjaTablesMigration.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/libs/Migrations/NinjaTablesUltimateTableMigration.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/libs/Migrations/NinjaTablesSupsysticTableMigration.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/libs/Migrations/NinjaTablesTablePressMigration.php';


        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/I18nStrings.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/ArrayHelper.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/libs/TableDrivers/NinjaFooTable.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/libs/Lead/LeadFlow.php';

        /*
         * Load Table Importers
         */


        $this->loader = new NinjaTablesLoader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the NinjaTablesI18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new NinjaTablesI18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new \NinjaTablesAdmin($this->get_plugin_name(), $this->get_version());
        $leadActions = new \WPManageNinja\Lead\LeadFlow();
        $this->loader->add_action('init', $plugin_admin, 'register_post_type');
        $this->loader->add_action('init', $leadActions, 'boot');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_menu');

        $this->loader->add_action('save_post', $plugin_admin, 'saveNinjaTableFlagOnShortCode');

        $this->loader->add_action('wp_ajax_ninja_tables_ajax_actions',
            $plugin_admin,
            'ajax_routes'
        );
        $this->loader->add_action('init', $plugin_admin, 'add_tabales_to_editor');

        $this->loader->add_action('ninja_table_check_db_integrity', $plugin_admin, 'checkDBMigrations');

        add_action('admin_enqueue_scripts', function () {
            if (isset($_GET['page']) && $_GET['page'] == 'ninja_tables') {
                if (function_exists('wp_enqueue_editor')) {
                    wp_enqueue_editor();
                }
                if (function_exists('wp_enqueue_media')) {
                    wp_enqueue_media();
                }
            }
        });

        add_filter('pre_set_site_transient_update_plugins', function ($updates) {
            if (!empty($updates->response['ninja-tables-pro'])) {
                $updates->response['ninja-tables-pro/ninja-tables-pro.php'] = $updates->response['ninja-tables-pro'];
                unset($updates->response['ninja-tables-pro']);
            }
            return $updates;
        }, 999, 1);


        global $pagenow;
        $showMessageBox = new DeactivationMessage();
        if ($pagenow == 'plugins.php') {
            $this->loader->add_action('admin_footer', $showMessageBox, 'addPluginDeactivationMessage');
        }
        $this->loader->add_action('wp_ajax_ninja-tables_deactivate_feedback', $showMessageBox, 'broadcastFeedback');

        $this->loadGutenBlock();

        $this->loader->add_filter('plugin_action_links_ninja-tables/ninja-tables.php', $plugin_admin, 'add_plugin_action_links');

        add_filter('admin_footer_text', function ($content) {
            if (isset($_GET['page']) && $_GET['page'] == 'ninja_tables') {
                $content = 'If you like Ninja Tables <a target="_blank" href="https://wordpress.org/support/plugin/ninja-tables/reviews/#new-post">please leave us a ★★★★★ rating</a>. Many thanks from the WPManageNinja team in advance :)';
            }
            return $content;
        });
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new NinjaTablePublic($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('init', $plugin_public, 'register_table_render_functions');

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueueNinjaTableScript', 100);

        $this->loader->add_action(
            'wp_ajax_wp_ajax_ninja_tables_public_action',
            $plugin_public,
            'register_ajax_routes'
        );

        $this->loader->add_action(
            'wp_ajax_nopriv_wp_ajax_ninja_tables_public_action',
            $plugin_public,
            'register_ajax_routes'
        );

        // run foo table
        $this->loader->add_action(
            'ninja_tables-render-table-footable',
            'NinjaTable\TableDrivers\NinjaFooTable',
            'run'
        );

        $this->loader->add_action('ninja_tables_inside_table_render',
            'NinjaTable\TableDrivers\NinjaFooTable',
            'getTableHTML',
            10,
            2
        );

        add_action('wp_head', function () {
            $errorType = get_option('_ninja_suppress_error');
            if (!$errorType) {
                $errorType = 'no';
            }
            if ($errorType != 'no'):
                ?>
                <script type="text/javascript">
                    // Ninja Tables is supressing the global JS to keep all the JS functions work event other plugins throw error.
                    // If You want to disable this please go to Ninja Tables -> Tools -> Global Settings and disable it
                    var oldOnError = window.onerror;
                    window.onerror = function (message, url, lineNumber) {
                        if (oldOnError) oldOnError.apply(this, arguments);  // Call any previously assigned handler
                        <?php if($errorType == 'log_silently'): ?>
                        console.error(message, [url, "Line#: " + lineNumber]);
                        <?php endif; ?>
                        return true;
                    };
                </script>
            <?php
            endif;
        }, 9);

        $demoPage = new ProcessDemoPage();
        $this->loader->add_action('init', $demoPage, 'handleExteriorPages');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    NinjaTablesLoader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }


    public function addPluginDeactivationMessage()
    {

    }

    /**
     * Load block for the gutenberg editor.
     */
    private function loadGutenBlock()
    {
        add_action('enqueue_block_editor_assets', function () {
            wp_enqueue_script(
                'ninja-tables-gutenberg-block',
                NINJA_TABLES_DIR_URL . 'assets/js/ninja-tables-gutenblock.js',
                array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor')
            );

            wp_enqueue_style(
                'ninja-tables-gutenberg-block',
                NINJA_TABLES_DIR_URL . 'assets/css/ninja-tables-gutenblock.css',
                array('wp-edit-blocks')
            );
        });
    }
}
