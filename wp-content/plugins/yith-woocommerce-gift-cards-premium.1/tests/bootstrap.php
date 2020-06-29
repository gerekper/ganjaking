<?php

/**
 * PHPUnit bootstrap file
 *
 * @package Yith_Woocommerce_Booking_Premium
 */


class BK_Unit_Tests_Bootstrap {
    /** @var BK_Unit_Tests_Bootstrap instance */
    protected static $instance = null;

    /** @var string directory where wordpress-tests-lib is installed */
    public $wp_tests_dir;

    /** @var string testing directory */
    public $tests_dir;

    /** @var string plugin directory */
    public $plugin_dir;

    /** @var string woocommerce directory */
    public $woocommerce_dir;

    public static function instance() {
        return !is_null( self::$instance ) ? self::$instance : self::$instance = new self();
    }

    /**
     * Setup the unit testing environment.
     *
     */
    protected function __construct() {

        ini_set( 'display_errors', 'on' );
        error_reporting( E_ALL );

        // Ensure server variable is set for WP email functions.
        if ( !isset( $_SERVER[ 'SERVER_NAME' ] ) ) {
            $_SERVER[ 'SERVER_NAME' ] = 'localhost';
        }

        $this->tests_dir       = dirname( __FILE__ );
        $this->plugin_dir      = dirname( $this->tests_dir );
        $this->woocommerce_dir = dirname( $this->plugin_dir ) . '/woocommerce';
        $this->wp_tests_dir    = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : ( rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib' );

        if ( !file_exists( $this->wp_tests_dir . '/includes/functions.php' ) ) {
            echo "Could not find {$this->wp_tests_dir}/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL;
            exit( 1 );
        }

        require_once( $this->wp_tests_dir . '/includes/functions.php' );

        // manually load gift cards
        tests_add_filter( 'muplugins_loaded', array( $this, 'load_yith_woocommerce_gift_card' ) );
        tests_add_filter( 'setup_theme', array( $this, 'install_wc' ) );


        // load the WP testing environment
        require_once( $this->wp_tests_dir . '/includes/bootstrap.php' );

        // load testing framework
        $this->includes();
    }

    public function load_yith_woocommerce_gift_card() {
        define( 'WC_TAX_ROUNDING_MODE', 'auto' );

        // include WooCommerce plugin
        require_once( $this->woocommerce_dir . '/woocommerce.php' );

        // include the wp_mail function for testing Gift Card
        require_once $this->tests_dir . '/framework/functions/wp-mail.php';

        // include our plugin Gift Cards
        require_once( $this->plugin_dir . '/init.php' );
    }

    public function install_wc() {

        // Clean existing install first.
        define( 'WP_UNINSTALL_PLUGIN', true );
        define( 'WC_REMOVE_ALL_DATA', true );
        include( $this->woocommerce_dir . '/uninstall.php' );

        WC_Install::install();

        // Reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374
        if ( version_compare( $GLOBALS[ 'wp_version' ], '4.7', '<' ) ) {
            $GLOBALS[ 'wp_roles' ]->reinit();
        } else {
            $GLOBALS[ 'wp_roles' ] = null;
            wp_roles();
        }

        echo 'Installing WooCommerce...' . PHP_EOL;
    }

    public function includes() {

        // framework
        /*require_once $this->tests_dir . '/framework/class-wc-unit-test-factory.php';
        require_once $this->tests_dir . '/framework/class-wc-mock-session-handler.php';
        require_once $this->tests_dir . '/framework/class-wc-mock-wc-data.php';
        require_once $this->tests_dir . '/framework/class-wc-mock-wc-object-query.php';
        require_once $this->tests_dir . '/framework/class-wc-mock-payment-gateway.php';
        require_once $this->tests_dir . '/framework/class-wc-payment-token-stub.php';
        require_once $this->tests_dir . '/framework/vendor/class-wp-test-spy-rest-server.php';*/

        // test cases
        /*require_once $this->tests_dir . '/framework/class-wc-unit-test-case.php';
        require_once $this->tests_dir . '/framework/class-wc-api-unit-test-case.php';
        require_once $this->tests_dir . '/framework/class-wc-rest-unit-test-case.php';*/

        // Functions

        // Helpers
        require_once $this->woocommerce_dir . '/includes/admin/wc-admin-functions.php';
        require_once $this->tests_dir . '/framework/helpers/class-wc-helpers-yith-cart-checkout-ajax-calls.php';
        require_once $this->tests_dir . '/framework/helpers/class-yith-wc-gift-card-helper.php';
        require_once $this->tests_dir . '/framework/helpers/class-wc-helper-product.php';
        require_once $this->tests_dir . '/framework/helpers/class-wc-helper-coupon.php';
        require_once $this->tests_dir . '/framework/helpers/class-wc-helper-fee.php';
        require_once $this->tests_dir . '/framework/helpers/class-wc-helper-shipping.php';
        require_once $this->tests_dir . '/framework/helpers/class-wc-helper-customer.php';
        require_once $this->tests_dir . '/framework/helpers/class-wc-helper-order.php';
        require_once $this->tests_dir . '/framework/helpers/class-wc-helper-shipping-zones.php';
        require_once $this->tests_dir . '/framework/helpers/class-wc-helper-payment-token.php';
        require_once $this->tests_dir . '/framework/helpers/class-wc-helper-settings.php';
    }


}

BK_Unit_Tests_Bootstrap::instance();
