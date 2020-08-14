<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined ( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

if ( ! class_exists ( 'YITH_Vendor' ) ) {

    //@TODO: Add shipping property

    /**
     * The main class for the Vendor
     *
     * @class      YITH_Vendor
     * @package    Yithemes
     * @since      1.0.0
     * @author     Your Inspiration Themes
     *
     * @property    string     $telephone
     * @property    string     $location
     * @property    string     $store_email
     * @property    string     $paypal_email
     * @property    string     $enable_selling
     * @property    string     $header_image
     * @property    string     $avatar
     * @property    string     $payment_type
     * @property    string     $threshold
     * @property    string     $registration_date
     * @property    string     $registration_date_gmt
     * @property    array      $admins
     * @property    string     $vacation_selling
     * @property    string     $vacation_message
     * @property    string     $vacation_start_date
     * @property    string     $name
     * @property    string     $vacation_end_date
     * @property    string     $show_gravatar
     * @property    int|string $commission
     * @property    string     $vat
     * @property    string     $featured_products
     * @property    string     $bank_account
     *
     */
    class YITH_Vendor {


        /** @public int The vendor ID. */
        public $id = 0;

        /** @public object Stores term data of vendor */
        public $term = null;

        /** @public string The taxonomy of the vendor. */
        public static $taxonomy;

        /** @protected string Stores term data of vendor */
        protected static $_usermetaKey = '';

        /** @protected string Stores term data of vendor */
        protected static $_usermetaOwner = '';

	    /** @protected string Default store header image */
	    public static $default_store_header = '';

        /** @private array Indicate the change properties status. */
        private $_changed = false;

        /**
         * Main Instance
         *
         * @var string
         * @since  1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Construct
         *
         * @param mixed  $vendor The vendor object
         * @param string $obj    What object is if is numeric (vendor|user|product)
         *
         * @return bool|YITH_Vendor
         */
        public static function retrieve ( $vendor = false, $obj = 'vendor' ) {
	        self::$_usermetaKey          = YITH_Vendors()->get_user_meta_key();
	        self::$_usermetaOwner        = YITH_Vendors()->get_user_meta_owner();
	        self::$taxonomy              = YITH_Vendors()->get_taxonomy_name();

            // change value 'current' to false for $vendor, to make it more rock!
            if ( 'current' == $vendor ) {
                $vendor = false;
            }

            // Get by user
            if ( 'user' == $obj ) {

                // get vendor of actual user if nothind passed
                if ( false === $vendor ) {
                    $vendor = get_user_meta ( get_current_user_id (), self::$_usermetaKey, true );
                } // Get Vendor ID by user ID passed by $vendor and set the getter to 'vendor'
                else {
                    $vendor = get_user_meta ( $vendor, self::$_usermetaKey, true );
                }

                $obj = 'vendor';
            } // Get by product
            elseif ( 'product' == $obj ) {

                // get vendor of actual product if nothind passed
                if ( false === $vendor ) {
                    global $post;
                    $vendor = isset($post) ? $post->ID : null;
                } elseif ( $vendor instanceof WP_Post ) {
                    $vendor = $vendor->ID;
                } elseif ( $vendor instanceof WC_Product_Variation ) {
	                $vendor = $vendor->get_parent_id();
                } elseif ( $vendor instanceof WC_Product ) {
                    $vendor = $vendor->get_id();
                }

                $terms = wp_get_post_terms ( $vendor, self::$taxonomy );

                if ( empty( $terms ) || is_wp_error( $terms ) ) {
                    return self::_instance ();
                }

                /* WPML SUPPORT */
                $vendor_term = array_shift ( $terms );
                $default_language = function_exists( 'wpml_get_default_language' ) ? wpml_get_default_language() : null;
                $vendor_id   = yit_wpml_object_id( $vendor_term->term_id, YITH_Vendors()->get_taxonomy_name(), true, $default_language );

                return self::_instance ( $vendor_id, $vendor_term );
            }

            // exit if any object is retrieved
            if ( empty( $vendor ) ) {
                return self::_instance ();
            }

            // RETRIEVE OBJECT
            // Get vendor by Vendor ID
            if ( is_numeric ( $vendor ) && 'vendor' == $obj ) {
                $vendor_id   = absint ( $vendor );
                $vendor_term = get_term_by ( 'term_id', $vendor_id, self::$taxonomy );
            } // get vendor by Vendor slug or name
            elseif ( is_string ( $vendor ) ) {
                $vendor_term = get_term_by ( 'slug', $vendor, self::$taxonomy );
                if ( empty( $vendor_term ) || is_wp_error ( $vendor_term ) ) {
                    $vendor_term = get_term_by ( 'name', $vendor, self::$taxonomy );
                }
                if ( empty( $vendor_term ) || is_wp_error ( $vendor_term ) ) {
                    return self::_instance ();
                }
                $vendor_id = $vendor_term->term_id;
            } // get vendor by object vendor
            elseif ( $vendor instanceof YITH_Vendor ) {
                $vendor_id   = absint ( $vendor->id );
                $vendor_term = $vendor->term;

                return self::_instance ( $vendor_id, $vendor_term );
            } // get vendor by term object
            elseif ( isset( $vendor->slug ) && term_exists ( $vendor->slug, self::$taxonomy ) ) {
                $vendor_id   = absint ( $vendor->term_id );
                $vendor_term = $vendor;
            } // no vendor found
            else {
                return self::_instance ();
            }

            // return false is there is a term associated
            if ( empty( $vendor_term ) ) {
                return self::_instance ();
            }

            return self::_instance ( $vendor_id, $vendor_term );
        }

        /**
         * Get cached vendor instance by ID
         *
         * @param int  $vendor_id
         * @param null $vendor_term
         *
         * @return mixed
         */
        protected static function _instance ( $vendor_id = 0, $vendor_term = null ) {
            if ( is_null ( self::$_instance ) || ! isset( self::$_instance[ $vendor_id ] ) ) {
                self::$_instance[ $vendor_id ] = new self( $vendor_id, $vendor_term );
            }

            return self::$_instance[ $vendor_id ];
        }

        /**
         * Populate the instance with term data
         *
         * @param int  $vendor_id
         * @param null $term
         *
         * @return YITH_Vendor The current object
         *
         */
        public function __construct ( $vendor_id = 0, $term = null ) {
            if ( empty( $vendor_id ) || empty( $term ) ) {
                return;
            }

	        /**
	         * WPML Support for Shipping Policy and Refund Policy
	         */
	        $has_wpml = apply_filters( 'wpml_setting', false, 'setup_complete' );

	        if ( $has_wpml ) {
		        add_filter( 'yith_wcmv_get_vendor_value', array( $this, 'get_translation' ), 10, 3 );
	        }

	        self::$default_store_header = apply_filters( 'yith_wcmv_default_store_header_image_url', YITH_WPV_ASSETS_URL . 'images/vendor-header-placeholder.jpg', $vendor_id );

            $this->id   = $vendor_id;
            $this->term = $term;

            $this->_populate ();

            return $this;
        }

        /**
         * Populate information of vendor
         *
         * @since 1.0
         */
        protected function _populate () {
            $this->name        = $this->term->name;
            $this->slug        = $this->term->slug;
            $this->description = $this->term->description;

            $this->_changed = array ();
            add_action ( 'shutdown', array ( $this, 'save_data' ), 10 );
        }

        /**
         * __get function.
         *
         * @param string $key
         *
         * @return mixed
         */
        public function __get ( $key ) {
            if ( isset( $this->_changed[ $key ] ) ) {
                return $this->_changed[ $key ];
            }

            $vendor_id = $this->id;

            $value = YITH_Vendors()->get_term_meta( $vendor_id, $key );

            // defaults
            $defaults = array (
                'payment_type' => 'instant',
                'threshold'    => 50,
            );

            foreach ( $defaults as $std_key => $std_value ) {
                $key == $std_key && ! isset( $this->$key ) && $value = $std_value;
            }

            // Get values or default if not set
            switch( $key ){
                case 'admins':
                    $value = $this->get_admins();
                    break;

                case 'owner':
                    $value = $this->get_owner();
                    break;

                case 'taxonomy':
                    $value = self::$taxonomy;
                    break;

                case 'socials':
                    if( empty( $value ) ){
                        $value = array();
                    }
                    break;

                case 'registration_date':
                    if( empty( $value ) ){
                        $owner_id = $this->get_owner();
                        if ( ! empty( $owner_id ) ) {
                            $owner = get_user_by ( 'id', $owner_id );
                            $value = $owner->user_registered;
                        }
                    }
                    break;

                case 'enable_selling':
                    $value = $this->get_enable_selling();
                    break;

                case 'pending':
                    $value = $this->get_pending();
                    break;

	            case 'website':
	            	if( ! empty( $this->socials['website'] ) ){
	            		$value = $this->socials['website'];
	                }
	                break;

	            case 'header_image':
		            $value = $this->get_header_image();
		            break;

                default:
                    if ( isset( $this->term->$key ) ) {
                        $value = $this->term->$key;
                    }
                    break;
            }

            return apply_filters( 'yith_wcmv_get_vendor_value', $value, $this, $key );
        }

        /**
         * __set function.
         *
         * @param mixed $property
         * @param mixed $value
         */
        public function __set ( $property, $value ) {
            if ( $this->_changed === false ) {
                return;
            }

            if ( $value === true ) {
                $value = 'yes';
            } elseif ( $value === false ) {
                $value = 'no';
            }

            $this->_changed[ $property ] = $value;
        }

        /**
         * Save data function.
         */
        public function save_data () {
            if ( ! $this->is_valid () || empty( $this->_changed ) ) {
                return;
            }

            // save the property to change in the term
            $term_properties = array ();
	        $skip_wc_clean_for = apply_filters( 'yith_wcmv_skip_wc_clean_for_fields_array', array( 'description', 'shipping_policy', 'shipping_refund_policy' ) );

            foreach ( $this->_changed as $property => $value ) {

                if(  ! in_array( $property, $skip_wc_clean_for ) ){
                    $value = ! is_array ( $value ) ? wc_clean ( $value ) : $value;
                }

                if ( in_array ( $property, array ( 'name', 'slug', 'description' ) ) ) {
                    $term_properties[ $property ] = $value;
                } else {
                    YITH_Vendors()->update_term_meta ( $this->id, $property, $value );
                }
            }

            // save the term data
            if ( ! empty( $term_properties ) ) {
                wp_update_term ( $this->id, self::$taxonomy, $term_properties );
            }
        }

        /**
         * __isset function.
         *
         * @param mixed $key
         *
         * @return bool
         */
        public function __isset ( $key ) {
            /* Support to WooCommerce 2.6 */
	        $socials   = YITH_Vendors()->get_social_fields();
	        $meta_type = YITH_Vendors()->is_wc_lower_2_6 ? 'woocommerce_term' : 'term';
	        $isset     = false;

            if( isset( $this->term->$key ) ){
	            $isset = true;
            }
            //Check if the fields is a socials
            elseif( ! empty( $socials ) && ! empty( $socials['social_fields'] ) && isset( $socials['social_fields'][ $key ] ) ){
	            $isset = true;
            }
            //check for valid metadata
            else{
	            $isset = metadata_exists ( $meta_type, $this->id, $key );
            }

            return $isset;
        }

        /**
         * Get the vendor commission
         *
         * @Author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return string The vendor commission
         * @fire   yith_vendor_commission filter
         */
        public function get_commission ( $product_id = false ) {
            $base_commission = YITH_Vendors ()->get_base_commission();

            return apply_filters ( 'yith_vendor_commission', $base_commission, $this->id, $this, $product_id );
        }

        /**
         * Get the vendor's settings
         *
         * @param             $key
         * @param bool|string $default
         *
         * @return mixed
         */
        public function get_setting ( $key, $default = false ) {
            $settings = get_option ( 'yit_vendor_' . $this->id . '_options' );

            return isset( $settings[ $key ] ) ? wc_clean ( $settings[ $key ] ) : $default;
        }

	    /**
	     * Get store header image
	     *
	     * @return   string Store header image url
	     * @since    1.0
	     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
	     */
	    public function get_header_image () {
		    $vendor_id = yith_wcmv_get_wpml_vendor_id( $this->id );
		    $value     = get_term_meta( $vendor_id, 'header_image', true );

		    if ( empty( $value ) && 'yes' == get_option( 'yith_wpv_vendor_store_default_header_image', 'no' ) ) {
			    $value = self::$default_store_header;
		    }

		    return apply_filters( 'yith_wcmv_get_vendor_header_image_value', $value, $this, 'header_image' );
	    }

        /**
         * Get vendor owner
         *
         * @return   int The owner user id
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_owner () {
            $args  = array (
                'meta_key'     => self::$_usermetaOwner,
                'meta_value'   => yith_wcmv_get_wpml_vendor_id( $this->id, 'current_language' ),
                'meta_compare' => '=',
                'fields'       => 'ids',
                'number'       => 1,
            );
            $owner = get_users ( $args );

            return ! empty( $owner ) ? array_shift ( $owner ) : 0;
        }

        /**
         * Get admins for vendor
         *
         * @return   Array of user IDs
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_admins () {
            $args   = array (
                'meta_key'     => self::$_usermetaKey,
                'meta_value'   => yith_wcmv_get_wpml_vendor_id ($this->id ),
                'meta_compare' => '=',
                'fields'       => 'ids',
            );
            $admins = get_users ( $args );

            return $admins;
        }

        /**
         * Get enabled selling cap for vendor
         *
         * @return   bool
         * @since    1.11.2
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_enable_selling () {
            $vendor_id = yith_wcmv_get_wpml_vendor_id ($this->id );
            $return = get_term_meta( $vendor_id, 'enable_selling', true );

            return $return;
        }

        /**
         * Get enabled selling cap for vendor
         *
         * @return   bool
         * @since    1.11.2
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_pending () {
            $vendor_id = yith_wcmv_get_wpml_vendor_id ($this->id );
            $return = get_term_meta( $vendor_id, 'pending', true );

            return $return;
        }

        /**
         * Check if the user passed in parameter is admin
         *
         * @param bool $user_id The user to check
         *
         * @return bool
         * @since 1.0
         */
        public function is_super_user ( $user_id = false ) {
            if ( ! $user_id ) {
                $user_id = get_current_user_id ();
            }

            // if the user is shop manager or administrator, return true
            return user_can ( $user_id, 'manage_woocommerce' );
        }

        /**
         * Check if the user passed in parameter is admin
         *
         * @param bool $user_id The user to check
         *
         * @return bool
         * @since 1.0
         */
        public function is_user_admin ( $user_id = false ) {
            if ( ! $user_id ) {
                $user_id = get_current_user_id ();
            }

            // if the user is shop manager or administrator, return true
            if ( $this->is_super_user ( $user_id ) ) {
                return true;
            }

            foreach ( $this->get_admins () as $admin_id ) {
                if ( $admin_id == $user_id ) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Check if the user has limited access to admin dashboard, valid only for Vendor Admin
         *
         * @param bool|int $user_id
         *
         * @return bool
         * @since 1.0
         */
        public function has_limited_access ( $user_id = false ) {
            return (bool)( ! $this->is_super_user ( $user_id ) && $this->is_user_admin ( $user_id ) );
        }

        /**
         * Return the arguments to make a query for the posts of this vendor
         *
         * @param array $extra More arguments to append
         *
         * @return array
         */
        public function get_query_products_args ( $extra = array () ) {
            return wp_parse_args ( $extra, array (
                'post_type' => 'product',
                'tax_query' => array (
                    array (
                        'taxonomy' => self::$taxonomy,
                        'field'    => 'id',
                        'terms'    => $this->id,
                    ),
                ),
            ) );
        }

        /**
         * Get query results of this vendor
         *
         * @param array $extra More arguments to append
         *
         * @return array
         */
        public function get_products ( $extra = array () ) {            
            $args = wp_parse_args ( $extra, array (
                    'posts_per_page' => - 1,
                    'fields'         => 'ids',
                )
            );

            $args = $this->get_query_products_args ( $args );
            $args = apply_filters( 'yith_wcmv_vendor_get_products_query_args', $args, $extra, $this );
            
            return get_posts ( $args );
        }

        /**
         * Check if the current object is a valid vendor
         *
         * @since  1.0
         *
         * @author Andrea Grillo    <andrea.grillo@yithemes.com>
         * @author Antonino Scarfi  <antonino.scarfi@yithemes.com>
         * @return bool
         */
        public function is_valid () {
            return ! empty( $this->id ) && ! empty( $this->term );
        }

        /**
         * Check if the current user is the vendor owner
         *
         * @since  1.0
         *
         * @author Andrea Grillo    <andrea.grillo@yithemes.com>
         * @author Antonino Scarfi  <antonino.scarfi@yithemes.com>
         * @return bool
         */
        public function is_owner ( $user_id = false ) {
            if ( ! $user_id ) {
                $user_id = get_current_user_id ();
            }

            return get_user_meta ( $user_id, self::$_usermetaOwner, true ) == $this->id;
        }

        /**
         * Get the frontend URL
         *
         * @param string $context
         *
         * @return string
         */
        public function get_url ( $context = 'frontend' ) {
            $url = '';

            if ( 'frontend' == $context ) {
                $url = get_term_link ( $this->term, self::$taxonomy );

                if ( $url && is_wp_error ( $url ) ) {
                    $url = false;
                }
            } else {
                if ( 'admin' == $context ) {
                    $url = admin_url( 'term.php' ) . '?taxonomy='. self::$taxonomy .'&tag_ID=' . $this->id;
                }
            }

            return apply_filters ( 'yith_vendor_url', $url, $this, $context );
        }

        /**
         * Get all unpaid commissions, if the sum amount is out threshold
         *
         * @return array|null
         */
        public function get_unpaid_commissions ( $extra_args = array () ) {
            $args = array (
                'vendor_id' => $this->id,
                'order_id'  => '', // useful when is set the order as completed from orders list, because it set "order_id" in the query string
                'status'    => 'unpaid',
            );

            $args = wp_parse_args ( $extra_args, $args );

            return YITH_Commissions ()->get_commissions ( $args );
        }

        /**
         * Get all unpaid commissions, if the sum amount is out threshold
         *
         * @return array|null
         */
        public function get_unpaid_commissions_if_out_threshold () {
            if ( $this->get_unpaid_commissions_amount () < $this->threshold ) {
                return array ();
            }

            $args = array (
                'vendor_id' => $this->id,
                'order_id'  => '', // useful when is set the order as completed from orders list, because it set "order_id" in the query string
                'status'    => 'unpaid',
            );

            return YITH_Commissions ()->get_commissions ( $args );
        }

        /**
         * If payment minimum threshold is reached, get all commissions that haven't been paid yet.
         *
         * @return float
         */
        public function get_unpaid_commissions_amount () {
            global $wpdb;
            $amount = $wpdb->get_var ( $wpdb->prepare ( "SELECT SUM(amount) FROM $wpdb->commissions WHERE status = %s AND vendor_id = %d", 'unpaid', $this->id ) );

            return floatval ( $amount );
        }

        /**
         * Pay commitions unpaid, in base of payment type choosen
         *
         * @param string $type all or only after threeshold
         *
         * @return array
         */
        public function commissions_to_pay ( $type = '' ) {
            $commissions = array ();

            if ( 'threshold' == $type ) {
                $commissions = $this->get_unpaid_commissions_if_out_threshold (); // could be empty
            } else {
	            $commissions = $this->get_unpaid_commissions ();
            }

            return $commissions;
        }

        /**
         * Get the registration date
         *
         * @param string $context
         * @param string $format
         * @param bool   $gmt
         *
         * @return string The registration date
         */
        public function get_registration_date ( $context = '', $format = '', $gmt = false ) {
            $registration_date = $gmt ? $this->registration_date_gmt : $this->registration_date;

            if ( 'timestamp' == $context ) {
                return mysql2date ( 'U', $registration_date );
            } else {
                if ( 'display' == $context ) {
                    if ( empty( $format ) ) {
                        $format = get_option ( 'date_format' );
                    }

                    return mysql2date ( $format, $registration_date );
                } else {
                    return $registration_date;
                }
            }
        }

        /**
         * Get query order ids of this vendor
         *
         * @return array The order ids
         */
        public function get_orders ( $type = 'all', $status = false ) {
            global $wpdb;
            $query = $wpdb->prepare ( "SELECT DISTINCT order_id FROM {$wpdb->commissions} WHERE vendor_id = %d", $this->id );

            if ( 'suborder' == $type ) {
                $query = $wpdb->prepare ( "SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_parent!=%d AND post_type=%s AND post_author=%d", 0, 'shop_order', $this->get_owner() );

                if ( $status ) {
                    if( is_array( $status) ){
                        $post_status_in = '';
                        $count          = count($status);
                        $i              = 1;
                        foreach( $status as $stati ){
                            $post_status_in .= "'{$stati}'";
                            if( $i < $count ){
                                $post_status_in .= ',';
                            }
                            $i++;
                        }
                        $query .= " AND post_status IN (" . $post_status_in . ")";
                    }
                    
                    else {
                        $query .= $wpdb->prepare ( " AND post_status=%s", $status );
                    }
                }
            }

            if ( 'quote' == $type ) {
                $query = $wpdb->prepare ( "SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_parent!=%d AND post_type=%s AND post_author=%d", 0, 'shop_order', $this->get_owner() );
                if ( $status && is_string( $status )) {
                    $query .= $wpdb->prepare ( " AND post_status=%s", $status );
                }

                elseif( is_array( $status) ){
                    $post_status_in = '';
                    $count          = count($status);
                    $i              = 1;
                    foreach( $status as $stati ){
                        $post_status_in .= "'{$stati}'";
                        if( $i < $count ){
                            $post_status_in .= ',';
                        }
                        $i++;
                    }
                    $query .= " AND post_status IN (" . $post_status_in . ")";
                }
            }

            $order_ids = $wpdb->get_col ( $query );

            return $order_ids;
        }

        /**
         * get the reviews average
         *
         * @return array The review average and the product with reviews count
         */
        public function get_reviews_average_and_product () {
            if( 'no' == get_option( 'yith_wpv_vendor_show_average_ratings' ) ){
                return array();
            }

            $response = apply_filters ( 'yith_wcmv_reviews_average_and_product', array (), $this );

            if ( ! empty( $response ) ) {
                return $response;
            }

            global $wpdb;
	        $average_rating = $reviews_product_count = $average = $count_reviewed_products = 0;
	        $product_ids    = $this->get_products();

	        if( ! empty( $product_ids ) ){
				$product_ids = implode(',', $product_ids );
		        $product_review_count_query = $wpdb->prepare( "SELECT SUM(pm.meta_value) FROM {$wpdb->postmeta} as pm WHERE meta_key=%s AND post_id IN ( ##vendor_product_ids## )", '_wc_review_count' );
		        $product_review_count_query = str_replace( '##vendor_product_ids##', $product_ids, $product_review_count_query );
		        $reviews_product_count      = $wpdb->get_var( $product_review_count_query );

		        $average_query = $product_review_count_query = $wpdb->prepare( "SELECT SUM(pm.meta_value) FROM {$wpdb->postmeta} as pm WHERE meta_key=%s AND post_id IN ( ##vendor_product_ids## )", '_wc_average_rating' );
		        $average_query = str_replace( '##vendor_product_ids##', $product_ids, $average_query );
		        $average       = $wpdb->get_var( $average_query );

		        $count_reviewed_products_query = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->postmeta} as pm WHERE meta_key=%s AND post_id IN ( ##vendor_product_ids## ) AND meta_value !=0", '_wc_review_count' );
		        $count_reviewed_products_query = str_replace( '##vendor_product_ids##', $product_ids, $count_reviewed_products_query );
		        $count_reviewed_products = $wpdb->get_var( $count_reviewed_products_query );
	        }

	        if ( !empty( $count_reviewed_products ) ) {
		        $average_rating = number_format( $average / $count_reviewed_products, 2 );
	        }

	        $response = array(
		        'average_rating'        => $average_rating,
		        'reviews_product_count' => $reviews_product_count,
	        );

	        return $response;
        }

        /**
         * get the email vendor order table
         *
         * @return array The review average and the product with reviews count
         */
        public function email_order_items_table ( $order, $show_download_links = false, $show_sku = false, $show_purchase_note = false, $show_image = false, $image_size = array ( 32, 32 ), $plain_text = false ) {

            ob_start ();

            $template = $plain_text ? 'emails/plain/vendor-email-order-items.php' : 'emails/vendor-email-order-items.php';

            yith_wcpv_get_template ( $template, array (
	            'order'                  => $order,
	            'vendor'                 => $this,
	            'items'                  => $order->get_items(),
	            'show_download_links'    => $show_download_links,
	            'show_sku'               => $show_sku,
	            'show_purchase_note'     => $show_purchase_note,
	            'show_image'             => $show_image,
	            'image_size'             => $image_size,
	            'tax_credited_to_vendor' => 'vendor' == get_option( 'yith_wpv_commissions_tax_management', 'website' ),
            ), '' );

            return ob_get_clean ();
        }

        public function is_on_vacation () {
            return function_exists ( 'YITH_Vendor_Vacation' ) ? YITH_Vendor_Vacation ()->vendor_is_on_vacation ( $this ) : false;
        }

        public function featured_products_management(){
            return get_option( 'yith_wpv_vendors_option_featured_management', 'no' );
        }

	    /**
	     * Get the translation for Shipping Policy and Refund Policy for vendor
	     *
	     * @param string $value Current term meta value
	     * @param YITH_Vendor Current vendor object $vendor
	     * @param string $key Field to get
	     *
	     * @return string The translated string
	     * @author Andrea Grillo <andrea.grillo@yithemes.com>
	     */
	    public function get_translation( $value, $vendor, $key ) {
		    $keys = apply_filters( 'yith_wcmv_get_translation_keys', array(
				    'shipping_policy',
				    'shipping_refund_policy'
			    )
		    );

		    if (  in_array( $key, $keys ) ) {
			    $type             = apply_filters( 'wpml_element_type', YITH_Vendors()->get_taxonomy_name() );
			    $trid             = apply_filters( 'wpml_element_trid', null, $vendor->id, $type );
			    $vendors          = apply_filters( 'wpml_get_element_translations', array(), $trid, $type );
			    $current_language = apply_filters( 'wpml_current_language', '' );

			    if ( ! empty( $vendors[ $current_language ] ) ) {
				    $wpml_vendor_args = $vendors[ $current_language ];
				    $wpml_vendor      = get_term( $wpml_vendor_args->element_id, YITH_Vendors()->get_taxonomy_name() );

				    if ( $wpml_vendor instanceof WP_Term ) {
					    $value = YITH_Vendors()->get_term_meta( $wpml_vendor->term_id, $key );
				    }
			    }
		    }

		    return $value;
	    }
    }
}

if ( ! function_exists ( 'yith_get_vendor' ) ) {
    /**
     * Main instance of plugin
     *
     * @param mixed  $vendor
     * @param string $obj
     *
     * @return YITH_Vendor
     * @since  1.0
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     */
    function yith_get_vendor ( $vendor = false, $obj = 'vendor' ) {
        return YITH_Vendor::retrieve ( $vendor, $obj );
    }
}
