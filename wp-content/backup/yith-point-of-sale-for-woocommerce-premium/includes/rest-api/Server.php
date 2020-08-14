<?php

namespace YITH\POS\RestApi;

defined( 'ABSPATH' ) || exit;

/**
 * Class responsible for loading the REST API and all REST API namespaces.
 */
class Server {
    private static $_instance;

    /**
     * REST API namespaces and endpoints.
     *
     * @var array
     */
    protected $controllers = [];

    /**
     * Singleton implementation
     *
     * @return Server
     */
    public static function get_instance() {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
    }

    /**
     * Hook into WordPress ready to init the REST API as needed.
     */
    public function init() {
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
        add_filter( 'woocommerce_admin_rest_controllers', array( $this, 'wc_admin_rest_controllers' ), 10, 1 );
        add_filter( 'woocommerce_data_stores', array( __CLASS__, 'add_data_stores' ) );
    }

    public static function add_data_stores( $data_stores ) {
        return array_merge(
            $data_stores,
            array(
                'yith-pos-report-orders-stats'    => 'YITH\POS\RestApi\Reports\Orders\Stats\DataStore',
                'yith-pos-report-cashiers'        => 'YITH\POS\RestApi\Reports\Cashiers\DataStore',
                'yith-pos-report-payment-methods' => 'YITH\POS\RestApi\Reports\PaymentMethods\DataStore',
            )
        );
    }

    public function wc_admin_rest_controllers( $controllers ) {
        $controllers[] = 'YITH\POS\RestApi\Reports\Orders\Stats\Controller';
        $controllers[] = 'YITH\POS\RestApi\Reports\Cashiers\Controller';
        $controllers[] = 'YITH\POS\RestApi\Reports\PaymentMethods\Controller';
        return $controllers;
    }

    /**
     * Register REST API routes.
     */
    public function register_rest_routes() {
        foreach ( $this->get_rest_namespaces() as $namespace => $controllers ) {
            foreach ( $controllers as $controller_name => $controller_class ) {
                $this->controllers[ $namespace ][ $controller_name ] = new $controller_class();
                $this->controllers[ $namespace ][ $controller_name ]->register_routes();
            }
        }
    }

    /**
     * Get API namespaces - new namespaces should be registered here.
     *
     * @return array List of Namespaces and Main controller classes.
     */
    protected function get_rest_namespaces() {
        return apply_filters(
            'yith_pos_rest_api_get_rest_namespaces',
            [
                'yith-pos/v1' => $this->get_v1_controllers(),
            ]
        );
    }

    /**
     * List of controllers in the wc/v1 namespace.
     *
     * @return array
     */
    public function get_v1_controllers() {
        return [
            'registers' => 'YITH_POS_REST_Registers_Controller',
        ];
    }
}
