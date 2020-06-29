<?php

if ( ! defined( 'YITH_WCARS_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Advanced_Refund_System
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Advanced_Refund_System' ) ) {
	/**
	 * Class YITH_Advanced_Refund_System
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Advanced_Refund_System {
        /**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0
		 */
		protected $version = YITH_WCARS_VERSION;

        /**
		 * Main Instance
		 *
		 * @var YITH_Advanced_Refund_System
		 * @since 1.0
		 * @access protected
		 */
		protected static $_instance = null;

		/**
		 * Main Admin Instance
		 *
		 * @var YITH_Advanced_Refund_System_Admin
		 * @since 1.0
		 */
		public $admin = null;

        /**
         * Main Frontend Instance
         *
         * @var YITH_Advanced_Refund_System_Frontend
         * @since 1.0
         */
        public $frontend = null;

		/**
		 * Main Request Manager Instance
		 *
		 * @var YITH_Advanced_Refund_System_Request_Manager
		 * @since 1.0
		 */
		public $request_manager = null;

		/**
		 * Main My Account Instance
		 *
		 * @var YITH_Advanced_Refund_System_Request_Manager
		 * @since 1.0
		 */
		public $my_account = null;
		

        /**
         * Construct
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0
         */
        protected function __construct() {

			/* === Require Main Files === */

			require_once( YITH_WCARS_PATH . 'includes/class.yith-advanced-refund-system-admin.php');
			require_once( YITH_WCARS_PATH . 'includes/class.yith-advanced-refund-system-frontend.php');
	        require_once( YITH_WCARS_PATH . 'includes/class.yith-advanced-refund-system-request-manager.php');
            require_once( YITH_WCARS_PATH . 'includes/class.yith-advanced-refund-system-my-account.php');
            require_once( YITH_WCARS_PATH . 'includes/class.yith-refund-request.php');
            require_once( YITH_WCARS_PATH . 'includes/class.yith-request-message.php');
	        require_once( YITH_WCARS_PATH . 'includes/class.yith-upload-exception.php');

            add_action( 'init', array( $this, 'init' ) );
            add_filter( 'woocommerce_email_classes', array( $this, 'register_email_classes' ) );
            add_filter( 'woocommerce_locate_core_template', array( $this, 'locate_core_template' ), 10, 3 );
	        add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
	        add_action( 'init', array( $this, 'rewrite_rules' ), 22 );
        }

        /**
		 * Main plugin Instance
		 *
		 * @return YITH_Advanced_Refund_System Main instance
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}



        /**
		 * Class Initialization
		 *
		 * Instance the admin or frontend classes
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since  1.0
		 * @return void
		 * @access protected
		 */

		public function init() {
			$this->ywcars_init_post_type();
            $this->ywcars_init_post_statuses();

			register_activation_hook( YITH_WCARS_FILE, array( 'YITH_Advanced_Refund_System_My_Account', 'install' ) );

			$this->my_account      = new YITH_Advanced_Refund_System_My_Account();
			$this->request_manager = new YITH_Advanced_Refund_System_Request_Manager();

            if ( is_admin() ) {
				$this->admin = new YITH_Advanced_Refund_System_Admin();
			}

			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				$this->frontend = new YITH_Advanced_Refund_System_Frontend();
			}

			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				require_once( YITH_WCARS_PATH . 'includes/elementor/class.yith-ars-elementor.php' );
			}
		}
		
		public function ywcars_init_post_type() {
			$labels = array(
				'name'               => esc_html_x( 'YITH Refund Requests', 'Page title', 'yith-advanced-refund-system-for-woocommerce' ),
				'singular_name'      => esc_html_x( 'Refund Request', 'Post type singular name', 'yith-advanced-refund-system-for-woocommerce' ),
				'menu_name'          => esc_html_x( 'YITH Refund Requests', 'admin menu', 'yith-advanced-refund-system-for-woocommerce' ),
				'name_admin_bar'     => esc_html_x( 'YITH Refund Request', 'add new on admin bar', 'yith-advanced-refund-system-for-woocommerce' ),
				'add_new'            => esc_html_x( 'Add New', 'Add New refund request', 'yith-advanced-refund-system-for-woocommerce' ),
				'add_new_item'       => esc_html__( 'Add new refund request', 'yith-advanced-refund-system-for-woocommerce' ),
				'new_item'           => esc_html__( 'New refund request', 'yith-advanced-refund-system-for-woocommerce' ),
				'edit_item'          => esc_html__( 'Manage refund request', 'yith-advanced-refund-system-for-woocommerce' ),
				'view_item'          => esc_html__( 'View refund request', 'yith-advanced-refund-system-for-woocommerce' ),
				'all_items'          => esc_html__( 'All refund requests', 'yith-advanced-refund-system-for-woocommerce' ),
				'search_items'       => esc_html__( 'Search refund requests', 'yith-advanced-refund-system-for-woocommerce' ),
				'parent_item_colon'  => esc_html__( 'Parent refund requests:', 'yith-advanced-refund-system-for-woocommerce' ),
				'not_found'          => esc_html__( 'No refund request found.', 'yith-advanced-refund-system-for-woocommerce' ),
				'not_found_in_trash' => esc_html__( 'No refund requests found in Trash.', 'yith-advanced-refund-system-for-woocommerce' )
			);

			$args = array(
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => false,
				'menu_position'       => 57,
				'can_export'          => false,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'menu_icon'           => 'dashicons-money',
				'query_var'           => false,
				'labels'              => $labels,
				'description'         => esc_html__( 'Description.', 'yith-advanced-refund-system-for-woocommerce' ),
				'publicly_queryable'  => true,
				'capability_type'     => 'post',
				'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'        => true,
				'supports'            => array( 'title' )
			);

			register_post_type( YITH_WCARS_CUSTOM_POST_TYPE, $args );
		}

        public function ywcars_init_post_statuses() {
            register_post_status( 'ywcars-new', array(
                    'label'                     => esc_html__( 'New refund request', 'yith-advanced-refund-system-for-woocommerce' ),
                    'public'                    => true,
                    'exclude_from_search'       => false,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => _n_noop( esc_html__( 'New', 'yith-advanced-refund-system-for-woocommerce' ) . '<span class="count"> (%s)</span>', esc_html__( 'New', 'yith-advanced-refund-system-for-woocommerce' ) . ' <span class="count"> (%s)</span>' ),
                )
            );

            register_post_status( 'ywcars-processing', array(
                    'label'                     => esc_html__( 'Processing refund request', 'yith-advanced-refund-system-for-woocommerce' ),
                    'public'                    => true,
                    'exclude_from_search'       => false,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => _n_noop( esc_html__( 'Processing', 'yith-advanced-refund-system-for-woocommerce' ) . '<span class="count"> (%s)</span>', esc_html__( 'Processing', 'yith-advanced-refund-system-for-woocommerce' ) . ' <span class="count"> (%s)</span>' ),
                )
            );

            register_post_status( 'ywcars-on-hold', array(
                    'label'                     => esc_html__( 'Refund request on hold', 'yith-advanced-refund-system-for-woocommerce' ),
                    'public'                    => true,
                    'exclude_from_search'       => false,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => _n_noop( esc_html__( 'On Hold', 'yith-advanced-refund-system-for-woocommerce' ) . '<span class="count"> (%s)</span>', esc_html__( 'On Hold', 'yith-advanced-refund-system-for-woocommerce' ) . ' <span class="count"> (%s)</span>' ),
                )
            );

            register_post_status( 'ywcars-approved', array(
                    'label'                     => esc_html__( 'Refund request approved', 'yith-advanced-refund-system-for-woocommerce' ),
                    'public'                    => true,
                    'exclude_from_search'       => false,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => _n_noop( esc_html__( 'Approved', 'yith-advanced-refund-system-for-woocommerce' ) . '<span class="count"> (%s)</span>', esc_html__( 'Approved', 'yith-advanced-refund-system-for-woocommerce' ) . ' <span class="count"> (%s)</span>' ),
                )
            );

            register_post_status( 'ywcars-rejected', array(
                    'label'                     => esc_html__( 'Refund request rejected', 'yith-advanced-refund-system-for-woocommerce' ),
                    'public'                    => true,
                    'exclude_from_search'       => false,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => _n_noop( esc_html__( 'Rejected', 'yith-advanced-refund-system-for-woocommerce' ) . '<span class="count"> (%s)</span>', esc_html__( 'Rejected', 'yith-advanced-refund-system-for-woocommerce' ) . ' <span class="count"> (%s)</span>' ),
                )
            );

        }

        function register_email_classes( $email_classes ) {
            $email_classes['YITH_ARS_New_Request_Admin_Email'] = include( YITH_WCARS_PATH . 'includes/emails/class.yith-ars-new-request-admin-email.php' );
	        $email_classes['YITH_ARS_New_Message_Admin_Email'] = include( YITH_WCARS_PATH . 'includes/emails/class.yith-ars-new-message-admin-email.php' );
            $email_classes['YITH_ARS_New_Request_User_Email'] = include( YITH_WCARS_PATH . 'includes/emails/class.yith-ars-new-request-user-email.php' );
	        $email_classes['YITH_ARS_New_Message_User_Email'] = include( YITH_WCARS_PATH . 'includes/emails/class.yith-ars-new-message-user-email.php' );
            $email_classes['YITH_ARS_Processing_User_Email'] = include( YITH_WCARS_PATH . 'includes/emails/class.yith-ars-processing-user-email.php' );
            $email_classes['YITH_ARS_On_Hold_User_Email'] = include( YITH_WCARS_PATH . 'includes/emails/class.yith-ars-on-hold-user-email.php' );
            $email_classes['YITH_ARS_Approved_User_Email'] = include( YITH_WCARS_PATH . 'includes/emails/class.yith-ars-approved-user-email.php' );
            $email_classes['YITH_ARS_Rejected_User_Email'] = include( YITH_WCARS_PATH . 'includes/emails/class.yith-ars-rejected-user-email.php' );

            return $email_classes;

        }

        public function locate_core_template( $core_file, $template, $template_base ) {
            $custom_template = array(
                'emails/ywcars-new-request-admin.php',
	            'emails/ywcars-new-message.php',
                'emails/ywcars-email-for-user.php'
            );

            if ( in_array( $template, $custom_template ) ) {
                $core_file = YITH_WCARS_TEMPLATE_PATH . $template;
            }
            return $core_file;
        }

		/**
		 * Load plugin framework
		 *
		 * @author Carlos Mora <cjmora.yithemes@gmail.com>
		 * @since  1.0
		 * @return void
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		public function rewrite_rules(){
			$do_flush = get_option( 'yith-ywcars-flush-rewrite-rules', 1 );

			if( $do_flush ) {
				// change option
				update_option( 'yith-ywcars-flush-rewrite-rules', 0 );
				// the flush rewrite rules
				flush_rewrite_rules();
			}
		}
    }
}