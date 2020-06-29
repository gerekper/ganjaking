<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if( ! class_exists( 'YITH_Vendors_Gateway_Manual_Payments' ) ) {
    /**
     * YITH YITH_Vendors_Gateway_Manual
     *
     * Define methods and properties for class that manages payments via paypal
     *
     * @package   YITH_Marketplace
     * @author    Your Inspiration <info@yourinspiration.it>
     * @license   GPL-2.0+
     * @link      http://yourinspirationstore.it
     * @copyright 2014 Your Inspiration
     */
    class YITH_Vendors_Gateway_Manual_Payments extends YITH_Vendors_Gateway {

    	/**
	     * @var string gateway slug
	     */
	    protected $_id = 'yith-wcmv-manual-payments';

	    /**
	     * @var string gateway name
	     */
	    protected $_method_title = 'YITH Manual Payments Gateway';

	    /**
	     * YITH_Vendors_Gateway_Manual constructor.
	     *
	     * @param $gateway
	     */
	    public function __construct( $gateway ) {
	    	//Silence is golden...
	    }
    }
}