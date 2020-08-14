<?php

namespace YITH\POS\RestApi;

defined( 'ABSPATH' ) || exit;

/**
 * Loader
 */
class Loader {

    private static $_instance;

    /** @var Server */
    private $_server;

    /**
     * Singleton implementation
     *
     * @return Loader
     */
    public static function get_instance() {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
    }

    protected function __construct() {
        if ( yith_pos_is_wc_admin_enabled() ) {
            $this->load();
            $this->include_files();
            $this->init();
        }
    }

    protected function load() {
        require_once "Server.php";
        $this->_server = \YITH\POS\RestApi\Server::get_instance();
    }

    protected function include_files() {
        // Functions
        require_once 'Utils/functions.php';

        // Controllers
        $controller_files = array(
            'Version1' => array_keys( $this->_server->get_v1_controllers() )
        );

        foreach ( $controller_files as $version => $controllers ) {
            foreach ( $controllers as $controller ) {
                $filename = "class.yith-pos-wc-rest-{$controller}-controller.php";
                $path     = "Controllers/{$version}/$filename";
                require_once $path;
            }
        }

        // Reports - Orders
        require_once 'Reports/Orders/Stats/Controller.php';
        require_once 'Reports/Orders/Stats/DataStore.php';
        require_once 'Reports/Orders/Stats/Query.php';

        // Reports - Cashiers
        require_once 'Reports/Cashiers/Controller.php';
        require_once 'Reports/Cashiers/DataStore.php';
        require_once 'Reports/Cashiers/Query.php';


        // Reports - Payment Methods
        require_once 'Reports/PaymentMethods/Controller.php';
        require_once 'Reports/PaymentMethods/DataStore.php';
        require_once 'Reports/PaymentMethods/Query.php';
    }

    protected function init() {
        $this->_server->init();
    }

}
