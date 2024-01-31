<?php
/*
Plugin Name: Ali2Woo
Plugin URI: https://ali2woo.com/pricing/
Description: Ali2Woo is a WordPress plugin created for AliExpress Drop-Shipping and Affiliate marketing
Text Domain: ali2woo
Domain Path: /languages
Version: 3.2.2
Author: Ali2Woo Team
Author URI: https://ali2woo.com/dropshipping-plugin/
License: GPLv3
Requires at least: 5.9
Tested up to: 6.4
WC tested up to: 8.5
WC requires at least: 5.0
Requires PHP: 8.0
 */

use Ali2Woo\Loader;
use Ali2Woo\ImportProcess;
use Ali2Woo\Json_Api_Configurator;
use DI\ContainerBuilder;
use DI\Container;

if (!defined('A2W_PLUGIN_FILE')) {
    define('A2W_PLUGIN_FILE', __FILE__);
}

if (!class_exists('A2W_Main')) {

    class A2W_Main
    {
        protected static ?A2W_Main $_instance = null;
        public string $version;
        public string $plugin_name;
        public string $plugin_slug;
        public string $chrome_url = 'https://chrome.google.com/webstore/detail/faieahckjkcpljkaedbjidlhhcigddal';
        private ?Container $DI = null;

        public static function instance(): A2W_Main
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        private function __construct()
        {
            $this->declareCompatibilityWithHPOS();
	        register_activation_hook(A2W_PLUGIN_FILE, array($this, 'install'));
	        register_deactivation_hook(A2W_PLUGIN_FILE, array($this, 'uninstall'));

            require_once ABSPATH . 'wp-admin/includes/plugin.php';
            $plugin_data = get_plugin_data(A2W_PLUGIN_FILE);

            $this->version = $plugin_data['Version'];
            $this->plugin_name = plugin_basename(A2W_PLUGIN_FILE);

            list ($t1, $t2) = explode('/', $this->plugin_name);
            $this->plugin_slug = $t1;

            require_once $this->plugin_path() . "/includes/libs/ae-php-sdk/IopSdk.php";

            require_once $this->plugin_path() . '/includes/libs/wp-background-processing/wp-background-processing.php';

            include_once $this->plugin_path() . '/includes/settings.php';
            include_once $this->plugin_path() . '/includes/functions.php';

            $this->initDIContainer();

            include_once $this->plugin_path() . '/includes/loader.php';
            Loader::classes(
                $this->plugin_path() . '/includes/classes',
                'a2w_init',
                $this->getDI()
            );
            Loader::addons($this->plugin_path() . '/addons');

            include_once $this->plugin_path() . "/includes/libs/json_api/json_api.php";
            Json_Api_Configurator::init('a2w_dashboard');

            if (!class_exists('Ali2Woo\Lib\Requests')) {
                include_once $this->plugin_path() . '/includes/libs/Requests/Requests.php';
                Ali2Woo\Lib\Requests::register_autoloader();
            }

            // Need to activate cron healthcheck
            ImportProcess::init();

            add_action('admin_menu', array($this, 'admin_menu'));

            add_action('admin_enqueue_scripts', array($this, 'admin_assets'));

            add_action('wp_enqueue_scripts', array($this, 'assets'));
        }

        /**
         * Path to Ali2Woo plugin root url
         * @return string
         */
        public function plugin_url(): string
        {
            return untrailingslashit(plugins_url('/', A2W_PLUGIN_FILE));
        }

        /**
         * Path to Ali2Woo plugin root dir
         * @return string
         */
        public function plugin_path(): string
        {
            return untrailingslashit(plugin_dir_path(A2W_PLUGIN_FILE));
        }

        public function install(): void
        {
			$activationError = '';
	        if (!class_exists('Woocommerce')) {
		        $activationError = _x('Please install Woocommerce', 'Activation error', 'ali2woo');
	        }

			if ($activationError) {
				die(sprintf("Plugin NOT activated: %s", $activationError));
			}

            a2w_gen_pk();
            do_action('a2w_install');
        }

        public function uninstall(): void
        {
            do_action('a2w_uninstall');
        }

        public function assets($page): void
        {
            do_action('a2w_assets', $page);
        }

        public function admin_assets($page): void
        {
            do_action('a2w_admin_assets', $page);
        }

        public function admin_menu(): void
        {
            do_action('a2w_before_admin_menu');

            add_menu_page(__('Ali2Woo', 'ali2woo'), __('Ali2Woo', 'ali2woo'), 'import', 'a2w_dashboard', '', plugins_url('assets/img/icon.png', A2W_PLUGIN_FILE));

            do_action('a2w_init_admin_menu', 'a2w_dashboard');
        }

        public function getDI(): ?Container {
            return $this->DI;
        }

        /**
         * @throws Exception
         */
        private function initDIContainer(): void {
            require_once $this->plugin_path() . '/vendor/autoload_packages.php';
            $containerBuilder = new ContainerBuilder;
            $containerBuilder->addDefinitions($this->plugin_path() . '/di-config.php');
            $this->DI = $containerBuilder->build();
        }

        private function declareCompatibilityWithHPOS(): void
        {
            add_action( 'before_woocommerce_init', function() {
                if (class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
                    \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
                        'custom_order_tables',
                        __FILE__,
                        true
                    );
                }
            });
        }
    }
}

/**
 * Returns the main instance of A2W_Main to prevent the need to use globals.
 *
 * @return A2W_Main
 */
if (!function_exists('A2W')) {

    function A2W(): A2W_Main
    {
        return A2W_Main::instance();
    }
}

$ali2woo = A2W();

/**
 * Ali2Woo global init action
 */
do_action('a2w_init');

if (is_admin()) {
    do_action('a2w_admin_init');
} else {
    do_action('a2w_frontend_init');
}
