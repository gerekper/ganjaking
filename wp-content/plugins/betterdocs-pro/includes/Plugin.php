<?php

namespace WPDeveloper\BetterDocsPro;

use WPDeveloper\BetterDocs\Core\BaseAPI;
use WPDeveloper\BetterDocsPro\Core\Admin;
use WPDeveloper\BetterDocsPro\Core\Query;
use WPDeveloper\BetterDocsPro\Core\Roles;
use WPDeveloper\BetterDocsPro\Core\Install;
use WPDeveloper\BetterDocsPro\Utils\Helper;
use WPDeveloper\BetterDocsPro\Utils\Enqueue;
use WPDeveloper\BetterDocsPro\Core\Installer;
use WPDeveloper\BetterDocsPro\Core\MultipleKB;
use WPDeveloper\BetterDocsPro\FrontEnd\FrontEnd;
use WPDeveloper\BetterDocsPro\Shortcodes\ListView;
use WPDeveloper\BetterDocs\Utils\Views as FreeViews;
use WPDeveloper\BetterDocsPro\Shortcodes\SidebarList;
use WPDeveloper\BetterDocsPro\Shortcodes\MultipleKBTwo;
use WPDeveloper\BetterDocsPro\Shortcodes\CategoryBoxTwo;
use WPDeveloper\BetterDocsPro\Shortcodes\MultipleKBList;
use WPDeveloper\BetterDocsPro\Shortcodes\CategoryGridTwo;
use WPDeveloper\BetterDocsPro\Shortcodes\PopularArticles;
use WPDeveloper\BetterDocsPro\Admin\Customizer\Customizer;
use WPDeveloper\BetterDocsPro\Shortcodes\CategoryGridList;
use WPDeveloper\BetterDocsPro\Shortcodes\MultipleKBTabGrid;
use WPDeveloper\BetterDocsPro\Shortcodes\RelatedCategories;
use WPDeveloper\BetterDocsPro\Shortcodes\MultipleKB as MultipleKBShortcode;
use WPDeveloper\BetterDocsPro\Dependencies\WPDeveloper\Licensing\LicenseManager;

final class Plugin {
    /**
     * Plugin Version
     * @var string
     */
    public $version = '2.5.4';

    /**
     * Plugin DB Version
     * @var string
     */
    public $db_version = '1.0.1';

    private static $_instance = null;

    /**
     * Create a plugin instance.
     *
     * @since 2.5.0
     * @param mixed ...$args
     *
     * @return static
     *
     * @suppress PHP0441
     */
    public static function get_instance() {
        if ( static::$_instance == null ) {
            static::$_instance = new self();

            do_action( 'betterdocs_pro_loaded' );
        }

        return static::$_instance;
    }

    /**
     * Container
     * @var \WPDeveloper\BetterDocs\Dependencies\DI\ContainerBuilder
     */
    public $container;

    /**
     * Assets manager
     *
     * @var Enqueue
     */
    public $assets;

    /**
     * Views Manager
     *
     * @var FreeViews
     */
    public $views;

    /**
     * Query
     *
     * @var Query
     */
    public $query;

    /**
     * Customizer
     *
     * @var Customizer
     */
    public $customizer;

    /**
     * Multiple KB
     *
     * @var MultipleKB
     */
    public $multiple_kb;

    public function __construct() {
        $this->define_constants();
        // Check if BetterDocs Free is installed/activated or not.
        if ( ! Helper::is_plugin_active( 'betterdocs/betterdocs.php' ) ) {
            new Installer;
        }

        /**
         * Register activation and deactivation hooks
         * and version updates check
         */
        new Install;

        // Admin Notices
        add_action( 'admin_notices', [$this, 'required_plugin'] );
        add_action( 'admin_notices', [$this, 'compatibility_notices'] );

        add_action( 'betterdocs_init_before', [$this, 'before_init'] );

        add_action( 'betterdocs_loaded', [$this, 'initialize'] );

        /**
         * After Setup Theme
         */
        add_action( 'after_setup_theme', [$this, 'setup_theme'] );

        /**
         * After Plugins Loaded
         */
        add_action( 'admin_init', [$this, 'admin_init'] );

        /**
         * Plugin Licensing
         * @since 2.5.0
         */
        $this->license();
    }

    /**
     * Summary of define_constants
     * @return void
     */
    private function define_constants() {
        $this->define( 'BETTERDOCS_PRO_VERSION', $this->version );
        $this->define( 'BETTERDOCS_PRO_DB_VERSION', $this->db_version );
        $this->define( 'BETTERDOCS_PRO_ABSPATH', dirname( BETTERDOCS_PRO_FILE ) . '/' );
        $this->define( 'BETTERDOCS_PRO_ABSURL', plugin_dir_url( BETTERDOCS_PRO_FILE ) );
        $this->define( 'BETTERDOCS_PRO_PLUGIN_BASENAME', plugin_basename( BETTERDOCS_PRO_FILE ) );
        // $this->define( 'BETTERDOCS_PRO_BLOCKS_DIRECTORY', BETTERDOCS_PRO_ABSPATH . 'react-src/gutenberg/blocks/' );

        $this->define( 'BETTERDOCS_PRO_STORE_URL', 'https://api.wpdeveloper.com/' );
        $this->define( 'BETTERDOCS_PRO_SL_ITEM_ID', 342422 );
        $this->define( 'BETTERDOCS_PRO_SL_ITEM_SLUG', 'betterdocs-pro' );
        $this->define( 'BETTERDOCS_PRO_SL_ITEM_NAME', 'BetterDocs Pro' );
        $this->define( 'BETTERDOCS_PRO_SL_DB_PREFIX', 'betterdocs_pro_software_' );
        // $this->define( 'BETTERDOCS_FREE_PLUGIN', BETTERDOCS_PRO_ADMIN_DIR_PATH . 'library/betterdocs.zip' );
    }

    public function required_plugin() {
        $plugin                = 'betterdocs/betterdocs.php';
        $_betterdocs_activated = Helper::is_plugin_active( $plugin );
        if ( $_betterdocs_activated ) {
            return;
        }

        $_betterdocs_installed = Helper::get_plugins( $plugin );
        $button_text           = $_betterdocs_installed ? __( 'Activate Now', 'betterdocs-pro' ) : __( 'Install Now', 'betterdocs-pro' );

        $button_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
        if ( $_betterdocs_installed ) {
            $button_url = wp_nonce_url(
                'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s',
                'activate-plugin_' . $plugin
            );
        }

        include BETTERDOCS_PRO_ABSPATH . 'views/admin/notices/activate.php';
    }

    public function compatibility_notices() {
        $plugin      = 'betterdocs/betterdocs.php';
        $plugins     = Helper::get_plugins();
        $plugin_data = $plugins[$plugin];

        if( isset( $plugin_data['Version'] ) && version_compare( $plugin_data['Version'], '2.5.0', '>=' ) ) {
            return;
        }

        include BETTERDOCS_PRO_ABSPATH . 'views/admin/notices/compatibility.php';
    }

    public function initialize() {
        add_action( 'init', [$this, 'init'], 1 );

        $this->container = betterdocs()->container;

        /**
         * Register activation and deactivation hooks
         * and version updates check
         */
        // $this->container->get( Install::class );

        $this->assets      = $this->container->get( Enqueue::class );
        $this->views       = $this->container->get( FreeViews::class );
        $this->customizer  = $this->container->get( Customizer::class );
        $this->multiple_kb = $this->container->get( MultipleKB::class );
        $this->query       = $this->container->get( Query::class );

        add_filter( 'betterdocs_shortcodes', [$this, 'pro_shortcodes'] );

        /**
         * Initialize API
         */
        add_action( 'rest_api_init', [$this, 'api_initialization'] );
    }

    public function pro_shortcodes( $shortcodes ) {
        $shortcodes = array_merge( $shortcodes, [
            CategoryBoxTwo::class,
            ListView::class,
            MultipleKBTabGrid::class,
            PopularArticles::class,
            MultipleKBShortcode::class,
            MultipleKBTwo::class,
            MultipleKBList::class,
            CategoryGridTwo::class,
            CategoryGridList::class,
            SidebarList::class,
            RelatedCategories::class
        ] );

        return $shortcodes;
    }

    /**
     * This methods will invoked after theme is setup.
     * @return void
     */
    public function setup_theme() {
        add_image_size( 'betterdocs-category-thumb', 360, 512 );
    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    public function init() {
        betterdocs()->load_plugin_textdomain( 'betterdocs-pro', BETTERDOCS_PRO_FILE );

        $this->container->get( FrontEnd::class );
        $this->container->get( Admin::class );
        $this->container->get( Roles::class );
    }

    public function admin_init() {
        if ( defined( 'DOING_AJAXX' ) && DOING_AJAX || ! is_admin() ) {
            return;
        }
    }

    public function before_init() {
        add_filter( 'betterdocs_container_config', [$this, 'container_config'] );
    }

    public function scripts( $hook ) {}

    public function container_config( $configs ) {
        $config_array = require_once BETTERDOCS_PRO_ABSPATH . 'includes/config.php';

        if ( is_array( $config_array ) ) {
            $configs = array_merge( $configs, $config_array );
        }

        return $configs;
    }

    /**
     * Get all the API initialized.
     * @return void
     */
    public function api_initialization() {
        $_api_classes = scandir( __DIR__ . DIRECTORY_SEPARATOR . 'REST' );

        if ( ! empty( $_api_classes ) && is_array( $_api_classes ) ) {
            foreach ( $_api_classes as $class ) {
                if ( $class == '.' || $class == '..' ) {
                    continue;
                }

                $classname  = basename( $class, '.php' );
                $classname  = '\\' . __NAMESPACE__ . "\\REST\\$classname";
                $_api_class = $this->container->get( $classname );

                if ( $_api_class instanceof BaseAPI ) {
                    $_api_class->register();
                }
            }
        }
    }

    public function license() {
        add_action( 'plugins_loaded', function () {
            if ( ! did_action( 'betterdocs_loaded' ) ) {
                return;
            }

            LicenseManager::get_instance( [
                'plugin_file'    => BETTERDOCS_PRO_FILE,
                'version'        => $this->version,
                'item_id'        => BETTERDOCS_PRO_SL_ITEM_ID,
                'item_name'      => BETTERDOCS_PRO_SL_ITEM_NAME,
                'item_slug'      => BETTERDOCS_PRO_SL_ITEM_SLUG,
                'storeURL'       => BETTERDOCS_PRO_STORE_URL,
                'textdomain'     => 'betterdocs-pro',
                'db_prefix'      => BETTERDOCS_PRO_SL_DB_PREFIX,
                'page_slug'      => 'betterdocs-settings',

                'scripts_handle' => 'betterdocs-pro-settings',
                'screen_id'      => "betterdocs_page_betterdocs-settings",

                'api'            => 'rest',
                'rest'           => [
                    'namespace'  => 'betterdocs-pro',
                    'permission' => 'delete_users'
                ]
            ] );
        } );
    }
}
