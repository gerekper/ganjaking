<?php
if( !defined('ABSPATH')) {
    exit;
}

if( !class_exists( 'WC_Product_YWF_Deposit' ) ){

    class WC_Product_YWF_Deposit extends WC_Product{

        public function __construct( $product )
        {
            parent::__construct( $product );
            $this->product_type = 'ywf_deposit';
            
        }

        
        public function get_type(){
            return 'ywf_deposit';
        }
        /**
         * product isn't visible
         * @author YITHEMES
         * @since 1.0.0
         * @return bool
         */
        public function is_visible()
        {
            return false;
        }

        /**
         * @author YITHEMES
         * @since 1.0.1
         * @return bool
         */
        public function is_downloadable()
        {
            return true;
        }

        /**
         * deposit is virtual
         * @author YITHEMES
         * @since 1.0.1
         * @return bool
         */
        public function is_virtual()
        {
            return true;

        }

        public function get_tax_class( $context = 'view' )
        {
            return '';
        }

        /**
         * product is always purchasable
         * @author YITHEMES
         * @since 1.0.0
         * @return bool
         */
        public function is_purchasable()
        {
           return apply_filters('yith_fund_product_is_purchasable', true, $this );
        }

        /**
         * product exists
         * @author YITHEMES
         * @since 1.0.0
         * @return bool
         */
        public function exists()
        {
            return true;
        }

       

        /**
         * Returns the main product image.
         * @author YITHEMES
         * @since 1.0.0
         * @param string $size
         * @param array $attr
         * @return string
         */
        public function get_image( $size = 'shop_thumbnail', $attr = array(), $placeholder=true )
        {
           return  parent::get_image( $size = 'shop_thumbnail', $attr = array(), $placeholder=true );
        }

        public function single_add_to_cart_text() {

        	return __( 'Add funds', 'yith-woocommerce-account-funds' );
        }

	    /**
         * @author YITHEMES
         * @since 1.0.0
         * @return string
         */
        public function get_title()
        {
            return __('Deposit funds', 'yith-woocommerce-account-funds');
        }

	    /**
	     * @return bool
	     */
        public function is_taxable() {
	        return false;
        }

	    /**
	     * @return bool
	     */
        public function is_sold_individually() {
	       return true;
        }

	    /**
	     * @param string $context
	     *
	     * @return string
	     */
        public function get_tax_status( $context = 'view' ){

        	return '';
        }

    }
}