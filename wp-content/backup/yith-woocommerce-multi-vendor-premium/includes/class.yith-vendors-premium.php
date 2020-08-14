<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( !defined( 'YITH_WPV_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Vendors
 * @package    Yithemes
 * @since      Version 2.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( !class_exists( 'YITH_Vendors_Premium' ) ) {
    /**
     * Class YITH_Vendors
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     */
    class YITH_Vendors_Premium extends YITH_Vendors {

        /**
         * Vendors sidebar id
         */
        public $vendors_sidebar_id;

        /**
         * @var \YITH_Orders
         */
        public $orders;

	    /**
	     * @var \YITH_Vendors_Gateways
	     */
	    public $gateways;

	    /**
	     * @var \YITH_Vendors_Payments
	     */
	    public $payments;

        /**
         * @var \YITH_WCMV_Addons
         */
        public $addons;

	    /**
	     * @var \YITH_Vendor_Request_Quote
	     */
	    public $quote = null;

        /**
         * Construct
         */
        public function __construct() {
            add_filter( 'yith_wcpv_require_class', array( $this, 'require_class' ) );
            add_filter( 'yith_vendor_commission', array( $this, 'get_commission' ), 10, 4 );
            add_filter( 'yith_wpv_register_widgets', array( $this, 'register_premium_widgets' ) );

            /* init emails */
            add_filter( 'woocommerce_email_classes', array( $this, 'register_emails' ) );
            add_filter( 'woocommerce_locate_core_template', array( $this, 'locate_core_template' ), 10, 3 );

            /* Vendor approve email */
            add_action( 'woocommerce_init', array( $this, 'load_wc_mailer' ) );

            /* Load modules */
            add_action( 'admin_menu', array( $this, 'load_admin_modules' ), 5 );
            add_action( 'wp_loaded',  array( $this, 'load_common_modules' ) );

            parent::__construct();

            if ( is_admin() ) {
                $this->addons = YITH_WCMV_Addons::get_instance();
            }
        }

        /**
         * Class Initializzation
         *
         * Instance the admin or frontend classes
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since  1.0
         * @return void
         * @access protected
         */
        public function init() {
	        if ( is_admin() ) {
                $this->admin = new YITH_Vendors_Admin_Premium();
            }

            if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
                $this->frontend = new YITH_Vendors_Frontend_Premium();
            }

            $this->orders = new YITH_Orders_Premium();

            if( function_exists( 'YITH_Vendor_Shipping' ) ) {
                $this->shipping = YITH_Vendor_Shipping();
            }

            if( class_exists( 'YITH_Vendors_Payments' ) ){
            	$this->payments = new YITH_Vendors_Payments();
            }

            if( function_exists( 'YITH_Vendors_Gateways' ) ){
            	$this->gateways = YITH_Vendors_Gateways();
            }

	        /* === Font Awesome Sylesheet === */
	        $style_deps = is_admin() ? array( 'yith-wcmv-font-awesome' ) : array();
	        wp_register_style( 'yith-wcmv-font-awesome', YITH_WPV_ASSETS_URL . 'third-party/font-awesome/css/fontawesome-all.min.css', array(), '5.0.9' );
	        wp_register_style( 'yith-wc-product-vendors', YITH_WPV_ASSETS_URL . 'css/product-vendors.css', $style_deps, YITH_WPV_VERSION );
        }

        /**
         * Add the premium class to require array
         *
         * @param $require The required file array
         *
         * @return array The required file
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @use    yith_wcpv_require_class filter
         */
        public function require_class( $require ) {
            /* === Load Premium Classes === */

            $require['admin'][]  = 'includes/class.yith-vendors-admin-premium.php';
            $require['admin'][]  = 'includes/class.yith-reports.php';
            $require['admin'][]  = 'includes/modules/class.yith-wcmv-addons.php';
            $require['admin'][]  = 'includes/modules/class.yith-wcmv-addons-compatibility.php';
            $require['admin'][]  = 'includes/class.yith-vendors-privacy-premium.php';
            $require['common'][] = 'includes/class.yith-vendors-frontend-premium.php';
            $require['common'][] = 'includes/class.yith-orders-premium.php';
            $require['common'][] = 'includes/class.yith-vendors-payments.php';
	        $require['common'][] = 'includes/class.yith-vendors-gateways.php';
	        $require['common'][] = 'includes/class.yith-vendors-gateway.php';

	        /* === Use this file for special action/filter that I can't trigger from gateway class === */
	        $require['common'][] = 'includes/functions.yith-vendors-gateways.php';

	        /* === Load Widgets === */

            $require['common'][] = 'includes/widgets/class.yith-vendor-store-location.php';
            $require['common'][] = 'includes/widgets/class.yith-vendor-quick-info.php';

            /* === Load Shortcodes === */

            $require['frontend'][] = 'includes/shortcodes/class.yith-multi-vendor-shortcodes.php';

            return $require;
        }

        /**
         * Load plugin modules
         *
         * @return void
         * @since  1.9
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function load_admin_modules(){
            $require = array();

            //Coupon Module
            if ( 'yes' == get_option( 'yith_wpv_vendors_option_coupon_management', 'no' ) ) {
                $require['admin'][] = 'includes/modules/coupons/abstract.module.yith-vendor-coupons.php';
                $coupon_class = $this->is_wc_2_7_or_greather ? 'module.yith-vendor-coupons.php' : 'module.yith-vendor-coupons-wc-2-6-or-lower.php';
                $require['admin'][] = "includes/modules/coupons/{$coupon_class}";
            }

            // GeoDirectory Module
            if( function_exists( 'geodir_allow_wpadmin' ) ){
                $require['admin'][] = 'includes/modules/module.yith-geodirectory-support.php';
            }

            // WP User Avatar Module
            if( class_exists( 'WP_User_Avatar_Subscriber' ) ){
                $require['admin'][] = 'includes/modules/module.yith-wp-user-avatar-support.php';
            }

            // WordPress User Frontend
            if( function_exists( 'wpuf' ) ){
                $vendor = yith_get_vendor( 'current', 'user' );
                if( $vendor->is_valid() && $vendor->has_limited_access() ){
                    remove_action( 'admin_init', array( wpuf(), 'block_admin_access' ) );
                }
            }

            // WooCommerce Customer/Order CSV Export
            if( function_exists( 'wc_customer_order_csv_export' ) ){
                $require['admin'][] = 'includes/modules/module.yith-wc-customer-order-export-support.php';
            }

            ! empty( $require ) && $this->_require( $require );
        }

        /**
         * Load common plugin modules
         *
         * @return void
         * @since  1.9
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function load_common_modules(){
            $require = array();

            //Seller Vacation Module
            if ( 'yes' == get_option( 'yith_wpv_vendors_option_seller_vacation_management', 'no' ) ) {
                $require['common'][] = 'includes/modules/module.yith-vendor-vacation.php';
            }

            // WooCommerce Points and Rewards
            if( class_exists( 'WC_Points_Rewards' ) ){
                $require['common'][] = 'includes/modules/module.yith-wc-points-and-rewards.php';
            }

            //Shipping Module
            if ( 'yes' == get_option( 'yith_wpv_vendors_option_shipping_management', 'no' ) ) {
                $require['common'][]    = 'includes/shipping/class.yith-wcmv-shipping-admin.php';
                $require['common'][]    = 'includes/shipping/class.yith-wcmv-shipping-frontend.php';
                $require['common'][]    = 'includes/modules/module.yith-vendor-shipping.php';
            }

            // WooCommerce Cost of Goods
            if( class_exists( 'WC_COG' ) ){
                $require['common'][] = 'includes/modules/module.yith-wc-cog.php';
            }

	        //Request a quote Module
	        if( 'yes' == get_option( 'yith_wpv_vendors_enable_request_quote', 'no' ) && function_exists( 'YITH_Request_Quote' ) ){
		        $require['common'][] = 'includes/modules/module.yith-vendor-quote.php';
	        }

	        //YOAST SEO
	        if( defined( 'WPSEO_VERSION' ) ) {
		        $require['common'][] = 'includes/modules/module.yith-yoast-seo.php';
	        }

	        // YITH Cost of Goods for WooCommerce
            if( class_exists( 'YITH_COG' ) ){
                $require['common'][] = 'includes/modules/module.yith-cost-of-goods.php';
            }

            if( function_exists( 'autoptimize' ) ){
	            $require['frontend'][] = 'includes/modules/module.yith-wp-autoptimize.php';
            }

            ! empty( $require ) && $this->_require( $require );
        }

	    /**
	     * Load PayPal Deprecated Module
	     *
	     * @return void
	     * @since  2.5.0
	     * @author Andrea Grillo <andrea.grillo@yithemes.com>
	     */
	    public function load_paypal_deprecated_service_module(){
		    if( apply_filters( 'yith_deprecated_paypal_service_support', false ) ){
			    require_once  YITH_WPV_PATH . 'includes/modules/compatibility/paypal/module.yith-vendor-deprecated-paypal-service.php';
		    }
	    }

        /**
         * Main plugin Instance
         *
         * @return YITH_Vendors Main instance
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * Register Emails for Vendors
         *
         * @since  1.0.0
         * @return string The taxonomy name
         */
        public function register_emails( $emails ) {
	        $emails['YITH_WC_Email_Commissions_Unpaid']             = include( 'emails/class-yith-wc-email-commissions-unpaid.php' );
	        $emails['YITH_WC_Email_Commissions_Paid']               = include( 'emails/class-yith-wc-email-commissions-paid.php' );
	        $emails['YITH_WC_Email_Vendor_Commissions_Paid']        = include( 'emails/class-yith-wc-email-vendor-commissions-paid.php' );
	        $emails['YITH_WC_Email_New_Vendor_Registration']        = include( 'emails/class-yith-wc-email-new-vendor-registration.php' );
	        $emails['YITH_WC_Email_Vendor_New_Account']             = include( 'emails/class-yith-wc-email-vendor-new-account.php' );
	        $emails['YITH_WC_Email_New_Order']                      = include( 'emails/class-yith-wc-email-new-order.php' );
	        $emails['YITH_WC_Email_Cancelled_Order']                = include( 'emails/class-yith-wc-email-cancelled-order.php' );
	        $emails['YITH_WC_Email_Vendor_Commissions_Bulk_Action'] = include( 'emails/class-yith-wc-email-vendor-commissions-bulk-action.php' );
            $emails['YITH_WC_Email_Product_Set_In_Pending_Review'] = include( 'emails/class-yith-wc-email-product-set-in-pending-review.php' );

            return $emails;
        }

        /**
         * Save extra taxonomy fields for product vendors taxonomy
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         *
         * @param $commission   string The commission
         * @param $vendor_id    string The vendor id
         * @param $vendor       YITH_Vendor The vendor object
         *
         * @return string The vendor commissions
         * @since  1.0
         * @use    yith_vendor_commission filter
         */
        public function get_commission( $commission, $vendor_id, $vendor, $product_id ) {
            /* Add Tag Ajax Hack */
            if ( isset( $_POST[ 'screen' ] ) && 'edit-yith_shop_vendor' == $_POST[ 'screen' ] && isset( $_POST[ 'action' ] ) && 'add-tag' == $_POST[ 'action' ] && isset( $_POST[ 'yith_vendor_data' ][ 'commission' ] ) ) {
                return $_POST[ 'yith_vendor_data' ][ 'commission' ] / 100;
            }

            $commission = isset( $vendor->commission ) ? $vendor->commission / 100 : $commission;;

	        if( ! empty( $product_id ) ){
		        $product = wc_get_product( $product_id );
		        $product_base_commission = $product->get_meta( '_product_commission' );
		        if( ! empty( $product_base_commission ) ){
			        $commission = $product_base_commission / 100;
		        }
	        }

            return $commission;
        }

        /**
         * Register premium widgets
         *
         * @param $widgets The widgets to register
         *
         * @return array The widgets array
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use      yith_wpv_register_widgets filter
         */
        public function register_premium_widgets( $widgets ) {
            $widgets[] = 'YITH_Vendor_Store_Location_Widget';
            $widgets[] = 'YITH_Vendor_Quick_Info_Widget';

            return $widgets;
        }

        /**
         * Set up array of vendor admin capabilities
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         *
         * @return array Vendor capabilities
         * @since  1.0
         */
        public function vendor_enabled_capabilities() {
            $caps = parent::vendor_enabled_capabilities();

            $live_chat_caps = $membership_caps = $size_charts_caps = $subscription_caps = $surveys_caps = array();

            /* === View Report Capability === */
            $caps[ 'view_woocommerce_reports' ] = true;

            /* === Coupon Capabilities === */
            if ( 'yes' == get_option( 'yith_wpv_vendors_option_coupon_management', 'no' ) ) {
                $caps[ 'edit_shop_coupons' ]             = true;
                $caps[ 'read_shop_coupons' ]             = true;
                $caps[ 'delete_shop_coupons' ]           = true;
                $caps[ 'publish_shop_coupons' ]          = true;
                $caps[ 'edit_published_shop_coupons' ]   = true;
                $caps[ 'delete_published_shop_coupons' ] = true;
                $caps[ 'edit_others_shop_coupons' ]      = true;
                $caps[ 'delete_others_shop_coupons' ]    = true;
            }

            /* === Product reviews === */
            if ( 'yes' == get_option( 'yith_wpv_vendors_option_review_management', 'no' ) ) {
                $caps[ 'moderate_comments' ] = true;
                $caps[ 'edit_posts' ]        = true;
            }

            if( YITH_Vendors()->addons ){
                /* === YITH Live Chat === */
                if ( YITH_Vendors()->addons->has_plugin( 'live-chat' ) && 'yes' == get_option( 'yith_wpv_vendors_option_live_chat_management', 'no' ) ) {
                    $live_chat_caps = apply_filters( 'yith_wcmv_live_chat_caps', array() );
                }

                /* === Surveys === */
                if ( YITH_Vendors()->addons->has_plugin( 'surveys' ) && 'yes' == get_option( 'yith_wpv_vendors_option_surveys_management', 'no' ) ) {
                    $surveys_caps = apply_filters( 'yith_wcmv_surveys_caps', array() );
                }
            }

            /* === Add-Ons capabilities === */
            $addons_caps = array();
            if ( ! empty( YITH_Vendors()->addons ) && ! empty( YITH_Vendors()->addons->compatibility ) ) {
                foreach ( YITH_Vendors()->addons->compatibility->plugin_with_capabilities as $plugin_name => $plugin_options ) {
                    $slug = YITH_Vendors()->addons->compatibility->get_slug( $plugin_name );
                    if ( YITH_Vendors()->addons->has_plugin( $plugin_name ) && 'yes' == get_option( 'yith_wpv_vendors_option_' . $slug . '_management', 'no' ) ) {
                        $addons_caps = array_merge( $addons_caps, (array) $plugin_options['capabilities'] );
                    }
                }
            }

            return apply_filters( 'yith_wcmv_vendor_capabilities', array_merge( $caps, $live_chat_caps, $membership_caps, $size_charts_caps, $subscription_caps, $surveys_caps, $addons_caps ) );
        }

        /**
         * Locate core template file
         *
         * @param $core_file
         * @param $template
         * @param $template_base
         *
         * @return array Vendor capabilities
         * @since  1.0
         */
        public function locate_core_template( $core_file, $template, $template_base ) {
            $custom_template = array(
                //HTML Email
                'emails/commissions-paid.php',
                'emails/commissions-unpaid.php',
                'emails/vendor-commissions-paid.php',
                'emails/new-vendor-registration.php',
                'emails/vendor-new-account.php',
                'emails/vendor-new-order.php',
                'emails/vendor-cancelled-order.php',
                'emails/commissions-bulk.php',

                // Plain Email
                'emails/plain/commissions-paid.php',
                'emails/plain/commissions-unpaid.php',
                'emails/plain/vendor-commissions-paid.php',
                'emails/plain/new-vendor-registration.php',
                'emails/plain/vendor-new-account.php',
                'emails/plain/vendor-new-order.php',
                'emails/plain/vendor-cancelled-order.php',
	            'emails/plain/commissions-bulk.php',
            );

            if ( in_array( $template, $custom_template ) ) {
                $core_file = YITH_WPV_TEMPLATE_PATH . $template;
            }

            return $core_file;
        }

        /**
         * Loads WC Mailer when needed
         *
         * @return void
         * @since  1.0
         * @author andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function load_wc_mailer() {
            add_action( 'yith_vendors_account_approved', array( 'WC_Emails', 'send_transactional_email' ), 10 );
        }

        /**
         * Get the social fields array
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since  1.8.4
         * @return array
         */
        public function get_social_fields() {
            $socials = array(
                'social_fields' => array(
                    'facebook'  => array(
                        'label' => __( 'Facebook', 'yith-woocommerce-product-vendors' ),
                        'icon'  => 'fab fa-facebook-square'
                    ),
                    'twitter'   => array(
                        'label' => __( 'Twitter', 'yith-woocommerce-product-vendors' ),
                        'icon'  => 'fab fa-twitter-square'
                    ),
                    'google'    => array(
                        'label' => __( 'Google+', 'yith-woocommerce-product-vendors' ),
                        'icon'  => 'fab fa-google-plus-square'
                    ),
                    'linkedin'  => array(
                        'label' => __( 'Linkedin', 'yith-woocommerce-product-vendors' ),
                        'icon'  => 'fab fa-linkedin'
                    ),
                    'youtube'   => array(
                        'label' => __( 'Youtube', 'yith-woocommerce-product-vendors' ),
                        'icon'  => 'fab fa-youtube'
                    ),
                    'vimeo'   => array(
                        'label' => __( 'Vimeo', 'yith-woocommerce-product-vendors' ),
                        'icon'  => 'fab fa-vimeo-square'
                    ),
                    'instagram' => array(
                        'label' => __( 'Instagram', 'yith-woocommerce-product-vendors' ),
                        'icon'  => 'fab fa-instagram'
                    ),
                    'pinterest' => array(
                        'label' => __( 'Pinterest', 'yith-woocommerce-product-vendors' ),
                        'icon'  => 'fab fa-pinterest-square'
                    ),
                    'flickr'    => array(
                        'label' => __( 'Flickr', 'yith-woocommerce-product-vendors' ),
                        'icon'  => 'fab fa-flickr'
                    ),
                    'behance'   => array(
                        'label' => __( 'Behance', 'yith-woocommerce-product-vendors' ),
                        'icon'  => 'fab fa-behance-square'
                    ),
                    'tripadvisor'   => array(
                        'label' => __( 'Tripadvisor  ', 'yith-woocommerce-product-vendors' ),
                        'icon'  => 'fab fa-tripadvisor  '
                    ),
                )
            );

            if( 'yes' == get_option( 'yith_wpv_vendors_option_live_chat_management' ) ){
                $socials['social_fields']['live-chat'] = array(
                    'label' => sprintf(
                        '%s<br/><small>%s: <em>%s</em></small>',
                        __( 'YITH Live Chat', 'yith-woocommerce-product-vendors' ),
                        _x( 'Use this value to show live chat button', 'option description', 'yith-woocommerce-product-vendors'),
                        '#yith-live-chat'
                    ),
                    'icon'  => 'fas fa-comments',
                );
            }

            if( 'no' != get_option( 'yith_wpv_vendor_show_vendor_website', 'no' ) ){
	            $socials['social_fields']['website'] = array(
		            'label' => __( 'Website url  ', 'yith-woocommerce-product-vendors' ),
		            'icon'  => 'fas fa-link'
	            );
            }

            $socials = apply_filters( 'yith_vendors_admin_social_fields', $socials );

            return $socials;
        }

        /**
         * Add or Remove publish_products capabilities to vendor admins when global option change
         *
         * @return   void|string
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @since    1.0
         */
        public function force_skip_review_option( $vendors = array() ) {
            //on my signal unleash hell
            if( empty( $vendors ) ) {
                $vendors = YITH_Vendors()->get_vendors();
            }
            $skip_option = get_option( 'yith_wpv_vendors_option_skip_review', 'no' );
            $method      = 'yes' == $skip_option ? 'add_cap' : 'remove_cap';

            foreach ( $vendors as $vendor ) {
                $admin_ids = $vendor->get_admins();
                foreach ( $admin_ids as $user_id ) {
                    $user = get_user_by( 'id', $user_id );
                    $user->$method( 'publish_products' );
                }
                $vendor->skip_review = $skip_option;
            }

            if( defined( 'DOING_AJAX' ) && DOING_AJAX ){
                wp_send_json( 'complete' );
            }
        }

	    /**
	     * Gets the message of the privacy to display.
	     * To be overloaded by the implementor.
	     *
	     * @return string
	     */
	    public function get_privacy_message() {
		    $content = '
			<div contenteditable="false">' .
		               '<p class="wp-policy-help">' .
		               __( 'This sample language includes the basics around what personal data your store may be collecting, storing and sharing, as well as who may have access to that data. Depending on what settings are enabled and which additional plugins are used, the specific information shared by your store will vary. We recommend consulting with a lawyer when deciding what information to disclose on your privacy policy.', 'yith-woocommerce-product-vendors' ) .
		               '</p>' .
		               '</div>' .
		               '<p>' . __( 'We collect information about you during the checkout process on our store.', 'yith-woocommerce-product-vendors' ) . '</p>' .
		               '<h2>' . __( 'What we collect and store', 'yith-woocommerce-product-vendors' ) . '</h2>' .
		               '<p>' . __( 'While you visit our site, we’ll track:', 'yith-woocommerce-product-vendors' ) . '</p>' .
		               '<ul>' .
		               '<li>' . __( 'Vendors data: we will use this information to create vendor profiles and allow them to sell their products on the site in exchange for a commission on sales. ', 'yith-woocommerce-product-vendors' ) . '</li>' .
		               '<li>' . __( 'Data required to start a store: store name and description, header image, store logo, address, email address, phone number, VAT/SSN, legal notes, social network links (Facebook, Twitter, Google+, Linkedin, Youtube, Vimeo, Instagram, Pinterest, Flickr, Behance, Tripadvisor), payment information (IBAN and PayPal email address), information related to commissions and payments made. ', 'yith-woocommerce-product-vendors' ) . '</li>' .
		               '</ul>' .
		               '<div contenteditable="false">' .
		               '<h2>' . __( 'Who on our team has access', 'yith-woocommerce-product-vendors' ) . '</h2>' .
		               '<p>' . __( 'Members of our team have access to the information you provide us. For example, both Administrators and Shop Managers can access:', 'yith-woocommerce-product-vendors' ) . '</p>' .
		               '<p>' . __( 'Our team members have access to this information to help fulfill orders, process refunds and support you.', 'yith-woocommerce-product-vendors' ) . '</p>' .
		               '</div>';

		    return $content;
	    }
    }
}
