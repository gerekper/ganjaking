<?php
/**
 * Admin class for Premium Version
 *
 * @author YITH
 * @package YITH WooCommerce Social Login
 * @version 1.0.0
 */

if ( ! defined( 'YITH_YWSL_INIT' ) ) {
    exit;
} // Exit if accessed directly

if( ! class_exists( 'YITH_WC_Social_Login_Admin_Premium' ) ){
    /**
     * YITH WooCommerce Social Login Admin Premium class
     *
     * @since 1.0.0
     */
    class YITH_WC_Social_Login_Admin_Premium extends YITH_WC_Social_Login_Admin{

        /**
         * @var $_panel Panel Object
         */
        protected $_panel;

        public $stats = array();

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WC_Social_Login_Admin_Premium
         * @since 1.0.0
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        /**
         * Constructor.
         *
         * @return \YITH_WC_Social_Login_Admin_Premium
         * @since 1.0.0
         */
        public function __construct() {

			parent::__construct();

			//reports
			add_filter('woocommerce_admin_reports', array($this, 'add_report_customer'));

            //user list table
            add_action( 'manage_users_columns', array( $this, 'add_connection_user_column' ) );
            add_action( 'manage_users_custom_column', array( $this, 'add_connection_user_column_content' ), 10, 3 );

            //user profile
            add_action( 'show_user_profile', array( $this, 'show_connection_in_user_profile' ));
            add_action( 'edit_user_profile', array( $this, 'show_connection_in_user_profile' ));

            //apply_filters
            add_filter('ywsl_admin_tabs', array($this, 'admin_tab_premium'));
			add_action('ywsl_register_panel', array($this, 'register_panel_advanced'));



        }

	    /**
	     * @param $new_row_meta_args
	     * @param $plugin_meta
	     * @param $plugin_file
	     * @param $plugin_data
	     * @param $status
	     * @param string $init_file
	     *
	     * @return array
	     */
	    public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWSL_INIT' ) {
		    $new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

		    if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ){
			    $new_row_meta_args['is_premium'] = true;
		    }

		    return $new_row_meta_args;
	    }

	    /**
	     * @param $links
	     *
	     * @return mixed
	     */
	    public function action_links( $links ) {
		    $links = yith_add_action_links( $links, $this->_panel_page, true );
		    return $links;
	    }


	    /**
		 * Add A link to Customer Tab in WooCommerce Report
		 *
		 * @return void
		 * @since    1.0.0
		 * @author   Emanuela Castorina
		 */
		public function add_report_customer($report){

			if ( isset( $report['customers'] ) ) {

				$report['customers']['reports']['social_login'] = array(
					'title'       => __( 'Social Connections', 'yith-woocommerce-social-login' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( $this, 'get_social_login_report' ),
				);
			}

			return $report;

		}

		/**
		 * Print Social Connection report in Customer Tab of WooCommerce Report
		 *
		 * @return void
		 * @since    1.0.0
		 * @author   Emanuela Castorina
		 */
		public function get_social_login_report(){

			$args = array(
				'enabled_social' => YITH_WC_Social_Login()->enabled_social,
				'stats' => $this->get_providers_stats(),
				'colors' => $this->get_providers_colors()
			);

			yit_plugin_get_template( YITH_YWSL_DIR, 'admin/reports/social-connect-report.php', $args );
		}

        /**
         * Return the stats of connection for network order by connection desc
         *
         * @return array
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        function get_providers_stats(){
			global $wpdb;
			$enabled_social = YITH_WC_Social_Login()->enabled_social;

			foreach ( $enabled_social as $key => $value ){
				$query = $wpdb->prepare('SELECT count(1) from '. $wpdb->usermeta .' WHERE meta_key LIKE "%s"', $key.'_login_id');
				$this->stats[] =array(
                    'id'    => $key,
					'label' => $value['label'],
					'data'  => $wpdb->get_var($query)
				);
            }
            usort( $this->stats, 'ywsl_providers_stats_sort');
            return $this->stats;
		}

        /**
         * Return the colors of connection for pie
         *
         * @return array
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        function get_providers_colors(){
            $enabled_social = YITH_WC_Social_Login()->enabled_social;
            $colors = array();
			foreach ( $this->stats as $stat ){

				$colors[] = $enabled_social[$stat['id']]['color'];
			}

			return $colors;

		}

        /**
         * Add a "Connections" column in user table of wordpress
         *
         * @return array
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        public function add_connection_user_column( $columns ){
            $new_columns = array();
            foreach( $columns as $key => $column){
                $new_columns[$key] =$column;
                if( $key == 'email'){
                    $new_columns['connections'] = __('Connections', 'yith-woocommerce-social-login');
                }

            }
            return $new_columns;
        }

        /**
         * Return the connections for each user in the user table of wordpress
         *
         * @return array
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        function add_connection_user_column_content($value, $column_name, $user_id) {
            global $wpdb;
            $connections =  YITH_WC_Social_Login_Premium()->get_social_login_connection( $user_id, 20 , 'buttons' );
            if ( 'connections' == $column_name ){
                return $connections;
            }
            return $value;
        }

        /**
         * Add social connetion info in user profile
         *
         * @return   array
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        function show_connection_in_user_profile( $user ){
            $args = array(
                'connections' => YITH_WC_Social_Login_Premium()->get_social_login_connection( $user->ID, 30, 'buttons' ),
            );

            yit_plugin_get_template( YITH_YWSL_DIR, 'admin/users/social-connect-profile.php', $args );
        }

        /**
         * Add admin tabs to premium version
         *
         * @return   array
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        function admin_tab_premium( $tabs = array() ){

            $premium_tabs = array(
                'facebook'   => __( 'Facebook', 'yith-woocommerce-social-login' ),
                'twitter'    => __( 'Twitter', 'yith-woocommerce-social-login' ),
                'google'     => __( 'Google', 'yith-woocommerce-social-login' ),
                'linkedin'   => __( 'LinkedIn', 'yith-woocommerce-social-login' ),
                'yahoo'      => __( 'Yahoo', 'yith-woocommerce-social-login' ),
                'foursquare' => __( 'Foursquare', 'yith-woocommerce-social-login' ),
                'live'       => __( 'Live', 'yith-woocommerce-social-login' ),
                'instagram'  => __( 'Instagram', 'yith-woocommerce-social-login' ),
                'paypal'     => __( 'PayPal', 'yith-woocommerce-social-login' ),
                'tumblr'     => __( 'Tumblr', 'yith-woocommerce-social-login' ),
                'vkontakte'  => __( 'Vkontakte', 'yith-woocommerce-social-login' ),
                'github'     => __( 'GitHub', 'yith-woocommerce-social-login' ),
              //  'aol'        => __( 'AOL', 'yith-woocommerce-social-login' ),
            );

            return  array_merge($tabs, $premium_tabs);
        }

        /**
         * Order tab in panel options
         *
         * @return   array
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        function admin_tab_premium_ordered() {
            $tabs = $this->admin_tab_premium();
            if ( get_option( 'ywsl_social_networks' ) ) {
                $tabs = array_merge( array_flip( get_option( 'ywsl_social_networks' ) ), $tabs );
            }
            return $tabs;
        }

        /**
         * Add more featured in options  panel
         *
         * @return   void
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        function register_panel_advanced() {
            add_action( 'woocommerce_admin_field_ywsl_social_networks', array( $this, 'social_network_table' ), 10, 2 );
        }

        /**
         * Show the table with social
         *
         * @return   void
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        function social_network_table( $args = array() ){
			$new_args= array(
                'tabs' => $this->admin_tab_premium_ordered(  ),
                'panel_page' => $this->_panel_page,
			);
            $args = array_merge($new_args, $args);
			yit_plugin_get_template( YITH_YWSL_DIR, 'admin/social_network_table.php', $args );
		}

    }

    /**
     * Unique access to instance of YITH_WC_Social_Login_Admin_Premium class
     *
     * @return \YITH_WC_Social_Login_Admin_Premium
     */
    function YITH_WC_Social_Login_Admin_Premium() {
        return YITH_WC_Social_Login_Admin_Premium::get_instance();
    }
}

