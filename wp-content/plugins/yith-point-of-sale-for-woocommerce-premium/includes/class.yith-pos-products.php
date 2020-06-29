<?php
!defined( 'YITH_POS' ) && exit; // Exit if accessed directly


if ( !class_exists( 'YITH_POS_Products' ) ) {
    /**
     * Class YITH_POS_Products
     * Products management
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
     */
    class YITH_POS_Products {

        /** @var YITH_POS_Products */
        private static $_instance;


        /**
         * Singleton implementation
         *
         * @return YITH_POS_Products
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_POS_Products constructor.
         */
        private function __construct() {
        	// handle POS Visibility for products
	        add_action( 'init', array( $this, 'add_new_term' ) );
	        add_filter( 'woocommerce_product_visibility_options', array( $this, 'add_pos_visibility' ), 10, 1 );
	        add_filter( 'woocommerce_product_get_catalog_visibility', array( $this, 'get_catalog_visibility' ), 10, 2 );
	        add_filter( 'woocommerce_product_set_visibility', array( $this, 'set_product_visibility' ), 10, 2 );
	        add_action( 'woocommerce_before_product_object_save', array( $this, 'save_product_visibility' ), 10, 1 );
        }

	    /**
	     * Create a new term.
	     */
	    public function add_new_term() {
		    wp_insert_term( 'yith_pos', 'product_visibility' );
	    }

	    /**
	     * Add new type of visibility to the shop
	     *
	     * @param $options
	     *
	     * @return mixed
	     */
	    public function add_pos_visibility( $options ) {
		    $options[ 'yith_pos' ] = __( 'POS results only', 'yith-point-of-sale-for-woocommerce' );

		    return $options;
	    }


	    /**
	     * Filter the visibility of a product.
	     *
	     * @param $value
	     * @param $product WC_Product
	     *
	     * @return string
	     */
	    public function get_catalog_visibility( $value, $product ) {
		    return has_term( 'yith_pos', 'product_visibility', $product->get_id() ) ? 'yith_pos' : $value;
	    }

	    /**
	     * Force the visibility to yith_pos.
	     *
	     * @param $product_id
	     * @param $catalog_vis
	     */
	    public function set_product_visibility( $product_id, $catalog_vis ) {
		    if ( isset( $_REQUEST[ 'yith_pos_add_product' ] ) || ( isset( $_REQUEST[ '_visibility' ] ) && $_REQUEST[ '_visibility' ] == 'yith_pos' ) ) {
			    $terms[] = 'yith_pos';
			    wp_set_post_terms( $product_id, $terms, 'product_visibility', false );
		    }
	    }

	    /**
	     * Add or remove the term 'yith_pos' from a product_visibility taxonomy.
	     *
	     * @param $product WC_Product
	     */
	    public function save_product_visibility( $product ) {

		    if ( ! isset( $_POST[ '_visibility' ] ) ) {
			    return;
		    }

		    $product_id = $product->get_id();
		    if ( $_POST[ '_visibility' ] !== 'yith_pos' ) {
			    $new_terms     = array();
			    $current_terms = wp_get_object_terms( $product_id, 'product_visibility' );
			    foreach ( $current_terms as $current_term ) {
				    if ( $current_term->name !== 'yith_pos' ) {
					    $new_terms[] = $current_term->name;
				    }
			    }

			    wp_set_post_terms( $product_id, $new_terms, 'product_visibility', false );

		    } else {
			    $terms[] = 'yith_pos';
			    wp_set_post_terms( $product_id, $terms, 'product_visibility', false );
		    }
	    }

    }
}