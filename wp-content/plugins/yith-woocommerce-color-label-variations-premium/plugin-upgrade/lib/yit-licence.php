<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_Licence' ) ) {
    /**
     * YIT Licence Panel
     *
     * Setting Page to Manage Products
     *
     * @class      YITH_Licence
     * @package    YITH
     * @since      1.0
     * @author     Andrea Grillo      <andrea.grillo@yithemes.com>
     */
    abstract class YITH_Licence {

        /**
         * @var mixed array The registered products info
         * @since 1.0
         */
        protected $_products = array();

        /**
         * @var array The settings require to add the submenu page "Activation"
         * @since 1.0
         */
        protected $_settings = array();

        /**
         * @var string Option name
         * @since 1.0
         */
        protected $_licence_option = 'yit_products_licence_activation';

        /**
         * @var string The yithemes api uri
         * @since 1.0
         */
        protected $_api_uri = 'https://yithemes.com/wc-api/software-api/';

        /**
         * @var string The yithemes api uri query args
         * @since 1.0
         */
        protected $_api_uri_query_args = '?request=%request%';

	    /**
	     * @var string The yithemes api uri query args
	     * @since 1.0
	     */
	    public $version = '1.0.0';

        /**
         * Constructor
         *
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function __construct() {
            $plugin_version = get_file_data( plugin_dir_path( __DIR__ ) . '/init.php', array( 'version' ) );
            $this->version = ! empty( $plugin_version ) ? $plugin_version[0] : '1.0.0';
            $is_debug_enabled = defined( 'YIT_LICENCE_DEBUG' ) && YIT_LICENCE_DEBUG;
            if ( $is_debug_enabled ) {
                $this->_api_uri = defined( 'YIT_LICENCE_DEBUG_LOCALHOST' ) ? YIT_LICENCE_DEBUG_LOCALHOST : 'http://dev.yithemes.com';
                add_filter( 'block_local_requests', '__return_false' );
            }

	        add_action( 'admin_notices', array( $this, 'activation_license_notice' ), 15 );

            /* Style adn Script */
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

	        // load from theme folder...
	        load_textdomain( 'yith-plugin-upgrade-fw', get_template_directory() . '/core/plugin-upgrade/languages/yith-plugin-upgrade-fw-' . apply_filters( 'plugin_locale', get_locale(), 'yith-plugin-upgrade-fw' ) . '.mo' )

	        // ...or from plugin folder
	        || load_textdomain( 'yith-plugin-upgrade-fw', dirname(__DIR__) . '/languages/yith-plugin-upgrade-fw-' . apply_filters( 'plugin_locale', get_locale(), 'yith-plugin-upgrade-fw' ) . '.mo' );

	        if ( function_exists( 'yith_plugin_fw_add_requirements' ) ) {
		        yith_plugin_fw_add_requirements( __( 'YITH License Activation', 'yith-plugin-fw' ), array( 'min_tls_version' => '1.2' ) );
	        }

	        add_action( 'wp_ajax_yith_license_banner_dismiss', array( $this, 'dismiss_license_banner' ) );
        }

        /**
         * Premium products registration
         *
         * @param $init         string | The products identifier
         * @param $secret_key   string | The secret key
         * @param $product_id   string | The product id
         *
         * @return void
         *
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        abstract public function register( $init, $secret_key, $product_id );

        /**
         * Get protected array products
         *
         * @return mixed array
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_products() {
            return $this->_products;
        }

        /**
         * Get The home url without protocol
         *
         * @return string the home url
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_home_url() {
            $home_url = home_url();
            $schemes  = array( 'https://', 'http://', 'www.' );

            foreach ( $schemes as $scheme ) {
                $home_url = str_replace( $scheme, '', $home_url );
            }

            if ( strpos( $home_url, '?' ) !== false ) {
                list( $base, $query ) = explode( '?', $home_url, 2 );
                $home_url = $base;
            }

            $home_url = untrailingslashit( $home_url );

            return $home_url;
        }

        /**
         * Check if the request is ajax
         *
         * @return bool true if the request is ajax, false otherwise
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function is_ajax() {
            return defined( 'DOING_AJAX' ) && DOING_AJAX ? true : false;
        }

        /**
         * Admin Enqueue Scripts
         *
         * @return void
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function admin_enqueue_scripts() {
            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            /**
             * Support to YIT Framework < 2.0
             */
            $script_path = $style_path = plugin_dir_url( __DIR__ );
	        $current_plugin_upgrade_path = plugin_dir_path( __DIR__ );

	        if ( false !== strpos( $current_plugin_upgrade_path, 'wp-content/themes' ) ) {
		        //If no files in current plugin url try to search it in theme
		        $script_path = $style_path = get_template_directory_uri() . '/theme/plugins/yit-framework/plugin-upgrade';
	        }
	        wp_register_script( 'yit-license-utils', $script_path . '/assets/js/yit-license-utils' . $suffix . '.js', array( 'jquery' ), $this->version, true );
	        wp_register_script( 'yit-licence', $script_path . '/assets/js/yit-licence' . $suffix . '.js', array( 'jquery', 'wc-enhanced-select' ), $this->version, true );
            wp_register_style( 'yit-theme-licence', $style_path . '/assets/css/yit-licence.css', array(), $this->version );

            /* Register select2 stylesheet */
	        if( ! wp_style_is( 'select2', 'registered' ) ){
		        $is_wc_enabled = function_exists( 'WC' );
		        $base_path = $is_wc_enabled ? WC()->plugin_url() : $style_path;
		        $select2_version = $is_wc_enabled ? WC()->version : $this->version;
		        wp_register_style( 'select2', $base_path . '/assets/css/select2.css', array(), $select2_version );
	        }
	        /* Register select2 stylesheet */
            if( ! wp_script_is( 'wc-enhanced-select', 'registered' ) ){

                if( ! wp_script_is( 'selectWoo', 'registered' ) ){
	                wp_register_script( 'selectWoo', $script_path . 'assets/js/selectWoo/selectWoo.full' . $suffix . '.js', array( 'jquery' ), $this->version, true );
	            }

                $deps = array( 'jquery', 'selectWoo' );
                wp_register_script( 'wc-enhanced-select', $script_path . 'assets/js/wc-enhanced-select/wc-enhanced-select' . $suffix . '.js', $deps, $this->version, true );
            }

            /* Localize Scripts */
            wp_localize_script( 'yit-licence', 'licence_message', array(
		            'error'                      => sprintf( esc_html_x( 'Please, insert a valid %s', '%s = field name', 'yith-plugin-upgrade-fw' ), '%field%' ),
		            // sprintf must be used to avoid errors with '%field%' string in translation in .po file
		            'errors'                     => sprintf( esc_html__( 'Please, insert a valid %s and a valid %s', 'yith-plugin-upgrade-fw' ), '%field_1%', '%field_2%' ),
		            'server'                     => esc_html__( 'Unable to contact remote server: this occurs when there are issues with connecting to our own servers. Trying again after a few minutes should solve the issue. If the problem persists please submit a support ticket and we will be happy to help you.', 'yith-plugin-upgrade-fw' ),
		            'email'                      => esc_html__( 'email address', 'yith-plugin-upgrade-fw' ),
		            'license_key'                => esc_html__( 'license key', 'yith-plugin-upgrade-fw' ),
		            'are_you_sure'               => esc_html__( 'Are you sure you want to deactivate the license for current site?', 'yith-plugin-upgrade-fw' ),
		            'choose_the_plugin_singular' => esc_html( _nx( 'Plugin', 'Choose the plugin', 1,'Form Label', 'yith-plugin-upgrade-fw' ) ),
		            'choose_the_plugin_plural'   => esc_html( _nx( 'Plugin', 'Choose the plugin', 2,'Form Label', 'yith-plugin-upgrade-fw' ) ),
	            )
            );

            wp_localize_script( 'yit-licence', 'script_info', array(
                                                 'is_debug' => defined( 'YIT_LICENCE_DEBUG' ) && YIT_LICENCE_DEBUG
                                             )
            );

	        wp_localize_script( 'yit-licence', 'yith_ajax', array(
			        'url' => admin_url( 'admin-ajax.php', 'relative' )
		        )
	        );

	        wp_localize_script( 'yit-license-utils', 'yith_ajax', array(
			        'url' => admin_url( 'admin-ajax.php', 'relative' )
		        )
	        );

            /* Enqueue Scripts only in Licence Activation page of plugins and themes */
            if ( strpos( get_current_screen()->id, 'yith_plugins_activation' ) !== false || strpos( get_current_screen()->id, 'yit_panel_license' ) !== false ) {
                wp_enqueue_script( 'yit-licence' );
                wp_enqueue_style( 'yit-theme-licence' );
	            wp_enqueue_style( 'yith-plugin-fw-fields' );
	            wp_enqueue_style( 'select2' );
            }

            if( $this->_show_license_banner() ){
	            /* Check for banner view */
	            wp_enqueue_script( 'yit-license-utils' );
            }
        }

        /**
         * Activate Plugins
         *
         * Send a request to API server to activate plugins
         *
         * @return void
         * @use    wp_send_json
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function activate() {
            $product_init = $_REQUEST[ 'product_init' ];
            $product      = $this->get_product( $product_init );

            $args = array(
                'email'       => urlencode( sanitize_email( $_REQUEST[ 'email' ] ) ),
                'licence_key' => sanitize_text_field( $_REQUEST[ 'licence_key' ] ),
                'product_id'  => sanitize_text_field( $product[ 'product_id' ] ),
                'secret_key'  => sanitize_text_field( $product[ 'secret_key' ] ),
                'instance'    => $this->get_home_url()
            );

	        $api_uri         = esc_url_raw( add_query_arg( $args, $this->get_api_uri( 'activation' ) ) );
	        $timeout         = apply_filters( 'yith_plugin_fw_licence_timeout', 30, __FUNCTION__ );
	        $do_request_args = apply_filters( 'yith_plugin_fw_do_request_args', array( 'timeout' => $timeout ) );
	        $response        = $this->do_request( $api_uri, $do_request_args );

            if ( is_wp_error( $response ) ) {
                $body = false;
            } else {
                $body = json_decode( $response[ 'body' ] );
                $body = is_object( $body ) ? get_object_vars( $body ) : false;
            }

            if ( $body && is_array( $body ) && isset( $body[ 'activated' ] ) && $body[ 'activated' ] ) {
                $license = array(
	                'email'                => urldecode( $args[ 'email' ] ),
	                'licence_key'          => $args[ 'licence_key' ],
	                'licence_expires'      => $body[ 'licence_expires' ],
	                'message'              => $body[ 'message' ],
	                'activated'            => true,
	                'activation_limit'     => $body[ 'activation_limit' ],
	                'activation_remaining' => $body[ 'activation_remaining' ],
	                'is_membership'        => isset( $body[ 'is_membership' ] ) ? $body[ 'is_membership' ] : false,
	                'marketplace'          => isset( $body[ 'marketplace' ] ) ? $body[ 'marketplace' ] : 'yith',
                );

                $option[ $product[ 'product_id' ] ] = $license;

                /* === Check for other plugins activation === */
                $options                             = $this->get_licence();
                $options[ $product[ 'product_id' ] ] = $option[ $product[ 'product_id' ] ];

                update_option( $this->_licence_option, $options );

                /* === Update Plugin Licence Information === */
                yith_plugin_fw_force_regenerate_plugin_update_transient();

	            $licenses     = $this->get_licence();
	            $info         = $licenses[ $product[ 'product_id' ] ];
	            $info['init'] = $product_init;
	            $info['licence'] = $license;
	            $info = array_merge( $info, $product );

                /* === Licence Activation Template === */
                $body['template'] = $this->show_activation_row( $info, true );
                $body['code'] = 200;
                $body['activation_message'] =  $this->get_response_code_message( 200 );
            }

            if ( !empty( $_REQUEST[ 'debug' ] ) ) {
                $body            = is_array( $body ) ? $body : array();
                $body[ 'debug' ] = array( 'response' => $response );
                if ( 'print_r' === $_REQUEST[ 'debug' ] ) {
                    $body[ 'debug' ] = print_r( $body[ 'debug' ], true );
                }
            }

            wp_send_json( $body );
        }

        /**
         * Deactivate Plugins
         *
         * Send a request to API server to activate plugins
         *
         * @return void
         * @use    wp_send_json
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function deactivate() {
            $force_delete = ! empty( $_REQUEST[ 'force_delete' ] ) ? $_REQUEST[ 'force_delete' ] : false;
	        $product_init = $_REQUEST['product_init'];
	        $product      = $this->get_product( $product_init );
	        $license      = $this->get_licence();
	        $product_id   = sanitize_text_field( $_POST['product_id'] );

            $args = array(
                'email'       => urlencode( sanitize_email( $license[ $product_id ][ 'email' ] ) ),
                'licence_key' => sanitize_text_field( $license[ $product_id ][ 'licence_key' ] ),
                'marketplace' => sanitize_text_field( $license[ $product_id ][ 'marketplace' ] ),
                'product_id'  => sanitize_text_field( $product[ 'product_id' ] ),
                'secret_key'  => sanitize_text_field( $product[ 'secret_key' ] ),
                'instance'    => $this->get_home_url()
            );

	        $api_uri         = esc_url_raw( add_query_arg( $args, $this->get_api_uri( 'deactivation' ) ) );
	        $timeout         = apply_filters( 'yith_plugin_fw_licence_timeout', 30, __FUNCTION__ );
	        $do_request_args = apply_filters( 'yith_plugin_fw_do_request_args', array( 'timeout' => $timeout ) );
	        $response        = $this->do_request( $api_uri, $do_request_args );

            if ( is_wp_error( $response ) ) {
                $body = false;
            } else {
                $body = json_decode( $response[ 'body' ] );
                $body = is_object( $body ) ? get_object_vars( $body ) : false;
            }

            if ( $body && is_array( $body )  ) {
            	/* === Get License === */
	            $options = $this->get_licence();
            	if(  isset( $body[ 'activated' ] ) && ! $body[ 'activated' ] && ! isset( $body[ 'error' ] ) ){
		            $option[ $product[ 'product_id' ] ] = array(
			            'activated'            => false,
			            'email'                => urldecode( $args[ 'email' ] ),
			            'licence_key'          => $args[ 'licence_key' ],
			            'licence_expires'      => $body[ 'licence_expires' ],
			            'message'              => $body[ 'message' ],
			            'activation_limit'     => $body[ 'activation_limit' ],
			            'activation_remaining' => $body[ 'activation_remaining' ],
			            'is_membership'        => isset( $body[ 'is_membership' ] ) ? $body[ 'is_membership' ] : false,
			            'marketplace'          => isset( $body[ 'marketplace' ] ) ? $body[ 'marketplace' ] : 'yith',
		            );

		            /* === Check for other plugins activation === */
		            $options[ $product[ 'product_id' ] ] = $option[ $product[ 'product_id' ] ];

		            /* === Update Plugin Licence Information === */
		            yith_plugin_fw_force_regenerate_plugin_update_transient();

		            update_option( $this->_licence_option, $options );

		            /* === Licence Activation Template === */
		            $body[ 'template' ] = $this->show_activation_panel( $this->get_response_code_message( 'deactivated', array( 'instance' => $body[ 'instance' ] ) ) );
	            }

	            else {
	                $body[ 'error' ] = $this->get_response_code_message( $body[ 'code' ] );

		            if( $force_delete && in_array( $body[ 'code' ], array( 106, 107 ) ) ){
			            $body[ 'code' ] = '999';
		            }

		            switch ( $body[ 'code' ] ) {

			            /**
			             * Error Code List:
			             *
			             * 100 -> Invalid Request
			             * 101 -> Invalid licence key
			             * 102 -> Software has been deactivate
			             * 103 -> Exceeded maximum number of activations
			             * 104 -> Invalid instance ID
			             * 105 -> Invalid security key
			             * 106 -> Licence key has expired
			             * 107 -> Licence key has be banned
                         * 999 -> Remove entry from DB
			             *
			             * Only code 101, 106 and 107 have effect on DB during activation
			             * All error code have effect on DB during deactivation
			             *
			             */

			            case '101':
			            case '102':
			            case '104':
                        case '999':
				            unset( $options[ $product[ 'product_id' ]  ] );
				            break;

			            case '106':
				            $options[ $product[ 'product_id' ]  ][ 'activated' ]       = false;
				            $options[ $product[ 'product_id' ]  ][ 'message' ]         = $body[ 'error' ];
				            $options[ $product[ 'product_id' ]  ][ 'status_code' ]     = $body[ 'code' ];
				            $options[ $product[ 'product_id' ]  ][ 'licence_expires' ] = $body[ 'licence_expires' ];
				            break;

			            case '107':
				            $options[ $product[ 'product_id' ]  ][ 'activated' ]   = false;
				            $options[ $product[ 'product_id' ]  ][ 'message' ]     = $body[ 'error' ];
				            $options[ $product[ 'product_id' ]  ][ 'status_code' ] = $body[ 'code' ];
				            break;
		            }

		            update_option( $this->_licence_option, $options );

		            /* === Licence Activation Template === */
		            $deactivate_message = $this->get_response_code_message( 'deactivated' );
		            $error_code_message = $this->get_response_code_message( $body['code'] );
		            $message            = sprintf( "<strong>%s</strong>: %s", $deactivate_message, $error_code_message );
		            $body['template']   = $this->show_activation_panel( $message );
		            $body['activated']  = false;
	            }
            }

            wp_send_json( $body );
        }

        /**
         * Check Plugins Licence
         *
         * Send a request to API server to check if plugins is activated
         *
         * @param string|The plugin init slug $plugin_init
         * @param boolean $regenerate_transient
         * @param boolean $force_check
         *
         * @return bool | true if activated, false otherwise
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function check( $product_init, $regenerate_transient = true, $force_check = false ) {

            $status     = false;
            $body       = false;
            $product    = $this->get_product( $product_init );
            $licence    = $this->get_licence();
            $product_id = $product[ 'product_id' ];

            if ( ! isset( $licence[ $product_id ] ) ) {
                return false;
            }

            if( ! $force_check && ! $this->is_check_needed( $licence[ $product_id ] ) ) {
                return true;
            }

            $args = array(
                'email'       => urlencode( $licence[ $product_id ][ 'email' ] ),
                'licence_key' => $licence[ $product_id ][ 'licence_key' ],
                'product_id'  => $product_id,
                'secret_key'  => $product[ 'secret_key' ],
                'instance'    => $this->get_home_url()
            );

            $api_uri  = esc_url_raw( add_query_arg( $args, $this->get_api_uri( 'check' ) ) );
            $timeout  = apply_filters( 'yith_plugin_fw_licence_timeout', 30, __FUNCTION__ );
	        $do_request_args = apply_filters( 'yith_plugin_fw_do_request_args', array( 'timeout' => $timeout ) );
            $response = $this->do_request( $api_uri, $do_request_args );

            if ( ! is_wp_error( $response ) ) {
                $body = json_decode( $response[ 'body' ] );
                $body = is_object( $body ) ? get_object_vars( $body ) : false;
            }

            if ( $body && is_array( $body ) ) {
                if ( isset( $body[ 'success' ] ) && $body[ 'success' ] == true ) {

                    /**
                     * Code 200 -> Licence key is valid
                     */
                    $licence[ $product_id ][ 'status_code' ]          = '200';
                    $licence[ $product_id ][ 'activated' ]            = $body[ 'activated' ];
                    $licence[ $product_id ][ 'licence_expires' ]      = $body[ 'licence_expires' ];
                    $licence[ $product_id ][ 'licence_next_check' ]   = time() + ( 12 * HOUR_IN_SECONDS );
                    $licence[ $product_id ][ 'activation_remaining' ] = $body[ 'activation_remaining' ];
                    $licence[ $product_id ][ 'activation_limit' ]     = $body[ 'activation_limit' ];
                    $licence[ $product_id ][ 'is_membership' ]        = isset( $body[ 'is_membership' ] ) ? $body[ 'is_membership' ] : false;
                    $status                                           = (bool) $body[ 'activated' ];
                } elseif ( isset( $body[ 'code' ] ) ) {

                    switch ( $body[ 'code' ] ) {

                        /**
                         * Error Code List:
                         *
                         * 100 -> Invalid Request
                         * 101 -> Invalid licence key
                         * 102 -> Software has been deactivate
                         * 103 -> Exceeded maximum number of activations
                         * 104 -> Invalid instance ID
                         * 105 -> Invalid security key
                         * 106 -> Licence key has expired
                         * 107 -> Licence key has be banned
                         *
                         * Only code 101, 106 and 107 have effect on DB during activation
                         * All error code have effect on DB during deactivation
                         *
                         */

                        case '101':
                        case '102':
	                    case '104':
                            unset( $licence[ $product_id ] );
                            break;

                        case '106':
                            $licence[ $product_id ][ 'activated' ]       = false;
                            $licence[ $product_id ][ 'message' ]         = $body[ 'error' ];
                            $licence[ $product_id ][ 'status_code' ]     = $body[ 'code' ];
                            $licence[ $product_id ][ 'licence_expires' ] = $body[ 'licence_expires' ];
                            break;

                        case '107':
                            $licence[ $product_id ][ 'activated' ]   = false;
                            $licence[ $product_id ][ 'message' ]     = $body[ 'error' ];
                            $licence[ $product_id ][ 'status_code' ] = $body[ 'code' ];
                            break;
                    }
                }

                /* === Update Plugin Licence Information === */
                update_option( $this->_licence_option, $licence );

                /* === Update Plugin Licence Information === */
                if ( $regenerate_transient ) {
                    yith_plugin_fw_force_regenerate_plugin_update_transient();
                }
            }

            return $status;
        }

        /**
         * Check if given licence needs to be checked
         *
         * @since 3.1.18
         * @author Francesco Licandro
         * @param array $licence The licence to check
         * @return boolean
         */
        public function is_check_needed( $licence ){
            if( empty( $licence['licence_expires'] ) || $licence['licence_expires'] < time()
                || empty( $licence['licence_next_check'] ) || $licence['licence_next_check'] < time() ){
                return true;
            }

            return false;
        }

        /**
         * Check for licence update
         *
         * @return void
         * @since  2.5
         *
         * @use    YIT_Theme_Licence->check()
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @param boolean $regenerate_transient
         * @param boolean $force_check
         */
        public function check_all( $regenerate_transient = true, $force_check = false ) {
            foreach ( $this->_products as $init => $info ) {
                $this->check( $init, $regenerate_transient, $force_check );
            }
        }

        /**
         * Update Plugins Information
         *
         * Send a request to API server to check activate plugins and update the informations
         *
         * @return void
         * @use    YIT_Theme_Licence->check()
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function update_licence_information() {
            /* Check licence information for alla products */
            $this->check_all( false, true );

            /* === Regenerate Update Plugins Transient === */
            yith_plugin_fw_force_regenerate_plugin_update_transient();

            do_action( 'yit_licence_after_check' );

            if ( $this->is_ajax() ) {
                $response[ 'template' ] = $this->show_activation_panel();
                wp_send_json( $response );
            }
        }

        /**
         * Include activation page template
         *
         * @return mixed void | string the contents of the output buffer and end output buffering.
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function show_activation_panel( $notice = '' ) {

            $path = dirname(__DIR__ ) ;

            if ( $this->is_ajax() ) {
                ob_start();
                require_once( $path . '/templates/panel/activation/activation-panel.php' );
                return ob_get_clean();
            } else {
                require_once( $path . '/templates/panel/activation/activation-panel.php' );
            }
        }

	    /**
	     * Include activation page template
	     *
	     * @return mixed void | string the contents of the output buffer and end output buffering.
	     *
	     * @since  1.0
	     * @author Andrea Grillo <andrea.grillo@yithemes.com>
	     */
	    public function show_activation_row( $info = array(), $use_buffering = false ) {

		    $path = dirname(__DIR__ ) ;
		    if( true === $use_buffering ){
		        ob_start();
			    require( $path . '/templates/panel/activation/activation-row.php' );
			    return ob_get_clean();
            }

		    else {
			    require( $path . '/templates/panel/activation/activation-row.php' );
            }
	    }

        /**
         * Get activated products
         *
         * @return array
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_activated_products() {
            $activated_products = array();
            $licence            = $this->get_licence();

            if ( is_array( $licence ) ) {
                foreach ( $this->_products as $init => $info ) {
                    if ( in_array( $info[ 'product_id' ], array_keys( $licence ) ) && isset( $licence[ $info[ 'product_id' ] ][ 'activated' ] ) && $licence[ $info[ 'product_id' ] ][ 'activated' ] ) {
                        $product[ $init ]              = $this->_products[ $init ];
                        $product[ $init ][ 'licence' ] = $licence[ $info[ 'product_id' ] ];
                        $activated_products[ $init ]   = $product[ $init ];
                    }
                }
            }
            return $activated_products;
        }

        /**
         * Get to active products
         *
         * @return array
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_to_active_products() {
            return array_diff_key( $this->get_products(), $this->get_activated_products() );
        }

        /**
         * Get no active products
         *
         * @return array
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_no_active_licence_key() {
            $unactive_products = $this->get_to_active_products();
            $licence           = $this->get_licence();
            $licence_key       = array();

            /**
             * Remove banned licence key
             */
            foreach ( $unactive_products as $init => $info ) {
                $product_id = $unactive_products[ $init ][ 'product_id' ];
                if ( isset( $licence[ $product_id ][ 'activated' ] ) && !$licence[ $product_id ][ 'activated' ] && isset( $licence[ $product_id ][ 'status_code' ] ) ) {
                    $status_code = $licence[ $product_id ][ 'status_code' ];

                    switch ( $status_code ) {
                        case '106':
                            $licence_key[ $status_code ][ $init ]              = $unactive_products[ $init ];
                            $licence_key[ $status_code ][ $init ][ 'licence' ] = $licence[ $product_id ];
                            break;

                        case '107':
                            $licence_key[ $status_code ][ $init ]              = $unactive_products[ $init ];
                            $licence_key[ $status_code ][ $init ][ 'licence' ] = $licence[ $product_id ];
                            break;
                    }
                }
            }

            return $licence_key;
        }

        /**
         * Get a specific product information
         *
         * @param $product_init | product init file
         *
         * @return mixed array
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_product( $init ) {
            return isset( $this->_products[ $init ] ) ? $this->_products[ $init ] : false;
        }

        /**
         * Get product product id information
         *
         * @param $product_init | product init file
         *
         * @return mixed array
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_product_id( $init ) {
            return isset( $this->_products[ $init ][ 'product_id' ] ) ? $this->_products[ $init ][ 'product_id' ] : false;
        }

        /**
         * Get Renewing uri
         *
         * @param $licence_key The licence key to renew
         *
         * @return mixed The renewing uri if licence_key exists, false otherwise
         *
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_renewing_uri( $licence_key ) {
            return !empty( $licence_key ) ? str_replace( 'www.', '', $this->_api_uri ) . '?renewing_key=' . $licence_key : false;
        }

        /**
         * Get protected yithemes api uri
         *
         * @param   $request
         *
         * @return mixed array
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_api_uri( $request ) {
            return str_replace( '%request%', $request, $this->_api_uri . $this->_api_uri_query_args );
        }

        /**
         * Get the activation page url
         *
         * @return String | Activation page url
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_licence_activation_page_url() {
            return esc_url( add_query_arg( array( 'page' => $this->_settings[ 'page' ] ), admin_url( 'admin.php' ) ) );
        }


        /**
         * Get the licence information
         *
         * @return array | licence array
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_licence() {
            return get_option( $this->_licence_option );
        }

        /**
         * Get the licence information
         *
         * @param $code string The error code
         *
         * @return string | Error code message
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_response_code_message( $code, $args = array() ) {
            extract( $args );

            $messages = array(
                '100'         => __( 'Invalid Request', 'yith-plugin-upgrade-fw' ),
                '101'         => __( 'Invalid license key', 'yith-plugin-upgrade-fw' ),
                '102'         => __( 'Software has been deactivated', 'yith-plugin-upgrade-fw' ),
                '103'         => __( 'Maximum number of license activations reached', 'yith-plugin-upgrade-fw' ),
                '104'         => __( 'Invalid instance ID', 'yith-plugin-upgrade-fw' ),
                '105'         => __( 'Invalid security key', 'yith-plugin-upgrade-fw' ),
                '106'         => __( 'License key has expired', 'yith-plugin-upgrade-fw' ),
                '107'         => __( 'License key has been revoked', 'yith-plugin-upgrade-fw' ),
                '108'         => __( 'This product is not included in your YITH Club Subscription Plan', 'yith-plugin-upgrade-fw' ),
                '200'         => sprintf( '<strong>%s</strong>! %s', __( 'Great', 'yith-plugin-upgrade-fw' ), __( 'License successfully activated', 'yith-plugin-upgrade-fw' ) ),
                'deactivated' => sprintf( '%s <strong>%s</strong>', __( 'License key deactivated for website', 'yith-plugin-upgrade-fw' ), isset( $instance ) ? $instance : '' ),
                '999'         => 999 //Local use only
            );

            return isset( $messages[ $code ] ) ? $messages[ $code ] : false;
        }

        /**
         * Get the product name to display
         *
         * @param $product_name
         *
         * @return string the product name
         *
         * @since    2.2
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function display_product_name( $product_name ) {
            return str_replace( array( 'for WooCommerce', 'YITH', 'WooCommerce', 'Premium', 'Theme', 'WordPress' ), '', $product_name );
        }

        public function get_number_of_membership_products() {
            $activated_products            = $this->get_activated_products();
            $num_members_products_activate = 0;
            foreach ( $activated_products as $activated_product ) {
                if ( isset( $activated_product[ 'licence' ][ 'is_membership' ] ) && $activated_product[ 'licence' ][ 'is_membership' ] ) {
                    $num_members_products_activate++;
                }
            }

            return $num_members_products_activate;
        }

	    /**
	     * Do requests to yithemes network
	     *
	     * @param $url string Url to call
	     * @param $args array Array of additional parameter for the remote call
	     * @return WP_Error|array wp_remote_get response
	     */
	    protected function do_request( $url, $args = array() ) {
		    $response = wp_remote_get( $url, $args );

		    if( is_wp_error( $response ) || ( isset( $response['response'] ) && isset( $response['response']['code'] ) && floor( $response['response']['code'] / 100 ) >= 4 ) ){
			    $alternative_url = str_replace( 'https://yithemes.com', 'https://casper.yithemes.com', $url );

			    $response = wp_remote_get( $alternative_url, $args );
		    }

		    return $response;
	    }

	    /**
	     * print notice with products to activate
	     *
	     * @since 3.0.0
	     */
	    public function activation_license_notice() {
		    if ( $this->_show_license_banner() ) {
			    $products_to_activate = $this->get_to_active_products();
			    if ( !!$products_to_activate ) {
				    $product_names = array();
				    foreach ( $products_to_activate as $init => $product ) {
					    if ( !empty( $product[ 'Name' ] ) )
						    $product_names[] = $product[ 'Name' ];
				    }

				    if ( !!$product_names ) {
					    $start          = '<span style="display:inline-block; padding:3px 10px; margin: 0 10px 10px 0; background: #f1f1f1; border-radius: 4px;">';
					    $end            = '</span>';
					    $product_list   = '<div>' . $start . implode( $end . $start, $product_names ) . $end . '</div>';
					    $activation_url = $this->get_license_url();
					    $nonce          = wp_create_nonce( 'dismiss-yith-license-banner' );
					    ?>
                        <div id="yith-license-notice" class="notice notice-error is-dismissible" data-nonce="<?php echo $nonce; ?>">
                            <div style="margin: 8px 0">
	                            <?php _e( "<strong>Warning!</strong> You didn't set license key for the following YITH products: {$product_list} which means you're missing out on updates and support. <a href='{$activation_url}'>Enter your license key</a>, please", 'yith-plugin-upgrade-fw' )?>
                            </div>
                        </div>
					    <?php
				    }
			    }
		    }
	    }

	    /**
         * Check if the current logged in user click on dismiss button for banner license
         *
         * @since 4.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
	     * @return string
	     */
	    protected function _show_license_banner() {
		    $show = get_user_meta( get_current_user_id(), 'yith-license-banner', true );
		    return 'hide' == $show ? false : true;
        }

	    public static function get_license_activation_url(){return false;}
	    public function get_license_url(){return false;}

        /**
         * Save an user meta to hide the license banner
         *
         * @since 4.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @retunr void
         */
        public function dismiss_license_banner(){
	        $nonce    = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : false;
	        $verified = wp_verify_nonce( $nonce, 'dismiss-yith-license-banner' );

            if( $verified ){
                update_user_meta( get_current_user_id(), 'yith-license-banner', 'hide' );
            }
        }
    }
}
