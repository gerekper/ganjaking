<?php
/**
 * Admin class
 *
 * @author  YITH
 * @package YITH WooCommerce Waiting List
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWTL_Admin_Premium' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWTL_Admin_Premium extends YITH_WCWTL_Admin {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var \YITH_WCWTL_Admin_Premium
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return \YITH_WCWTL_Admin_Premium
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {

			parent::__construct();

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_premium' ) );

			add_filter( 'yith-wcwtl-admin-tabs', array( $this, 'add_premium_options_tabs' ), 10, 1 );

			// custom tab
			add_action( 'yith_wcwtl_exclusions_table', array( $this, 'exclusions_table' ) );
			add_action( 'yith_wcwtl_waitlist_data', array( $this, 'waitlist_data' ) );
			add_action( 'yith_wcwtl_email_settings', array( $this, 'email_settings' ) );
			add_action( 'yith_wcwtl_waitlist_importer', array( $this, 'waitlist_importer' ) );

			// Register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// add notice
			add_action( 'all_admin_notices', array( $this, 'add_mailout_notice' ) );
			// Custom tinymce button
			add_action( 'admin_head', array( $this, 'tc_button' ) );
			// handle table action
			add_action( 'admin_init', array( $this, 'table_actions' ) );
		}

		/**
		 * Enqueue script premium
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function admin_scripts_premium() {

			$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			if ( isset( $_GET['page'] ) && $_GET['page'] == $this->_panel_page ) {
				// style
				wp_enqueue_style( 'yith-waitlist-admin-stile', YITH_WCWTL_ASSETS_URL . '/css/admin.css', array(), YITH_WCWTL_VERSION, 'all' );
				// script
				wp_enqueue_script( 'yith-waitlist-admin', YITH_WCWTL_ASSETS_URL . '/js/admin' . $min . '.js', array( 'jquery' ), YITH_WCWTL_VERSION, true );

				wp_localize_script( 'yith-waitlist-admin', 'yith_wcwtl_admin', array(
					'ajaxurl'   => admin_url( 'admin-ajax.php' ),
					'security'  => wp_create_nonce( "search-products" ),
					'conf_msg'  => __( 'Do you really want to send the mail?', 'yith-woocommerce-waiting-list' ),
					'email_tab' => admin_url( "admin.php?page={$this->_panel_page}&tab=email" ),
				) );
			}
		}

		/**
		 * Add premium tabs options to standard
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param mixed $tabs Standard admin tabs
		 *
		 * @return mixed
		 */
		public function add_premium_options_tabs( $tabs ) {

			$tabs = array_merge( $tabs, array(
				'style'            => __( 'Style', 'yith-woocommerce-waiting-list' ),
				'email'            => __( 'Email Settings', 'yith-woocommerce-waiting-list' ),
				'exclusions'       => __( 'Exclusions List', 'yith-woocommerce-waiting-list' ),
				'waitlistdata'     => __( 'Waiting list Checklist', 'yith-woocommerce-waiting-list' ),
				'waitlistimporter' => __( 'Waiting list Importer', 'yith-woocommerce-waiting-list' ),
			) );

			return $tabs;
		}

		/**
		 * Print exclusions table
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function exclusions_table() {
			if ( file_exists( YITH_WCWTL_DIR . '/templates/admin/exclusions-tab.php' ) ) {
				// first load required classes
				include_once 'admin-table/class.yith-wcwtl-exclusions-table.php';

				$table = new YITH_WCWTL_Exclusions_Table();
				$table->prepare_items();

				// then template
				include_once YITH_WCWTL_DIR . '/templates/admin/exclusions-tab.php';
			}
		}

		/**
		 * Print waitlist data table
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function waitlist_data() {

			if ( ! empty( $_GET['view'] ) && 'users' == $_GET['view'] ) {
				include_once 'admin-table/class.yith-wcwtl-waitlistusers-table.php';
				$table = new YITH_WCWTL_WaitlistUsers_Table();
				$table->prepare_items();

				// then template
				include_once YITH_WCWTL_DIR . '/templates/admin/waitlistdata-users-tab.php';
			} else {
				include_once 'admin-table/class.yith-wcwtl-waitlistdata-table.php';
				$table = new YITH_WCWTL_WaitlistData_Table();
				$table->prepare_items();

				// then template
				include_once YITH_WCWTL_DIR . '/templates/admin/waitlistdata-tab.php';
			}
		}

		/**
		 * Handle waitlist importer view
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function waitlist_importer() {
			include 'class.yith-wcwtl-importer-controller.php';
			$importer = new YITH_WCWTL_Importer_Controller();
			$importer->output();
		}

		/**
		 * Handle email settings tab
		 * This method based on query string load single email options or the general table
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro
		 */
		public function email_settings() {

			$emails = YITH_WCWTL()->get_emails();
			// is a single email view?
			$active = '';
			if ( isset( $_GET['section'] ) ) {
				foreach ( $emails as $email ) {
					if ( strtolower( $email ) == $_GET['section'] ) {
						$active = $email;
						break;
					}
				}
			}

			// load mailer
			$mailer = WC()->mailer();

			if ( ! $active ) {
				// build email table element
				$emails_table = array();
				foreach ( $emails as $email ) {
					$email_class            = $mailer->emails[ $email ];
					$emails_table[ $email ] = array(
						'title'       => $email_class->get_title(),
						'description' => $email_class->get_description(),
						'recipient'   => $email_class->is_customer_email() ? __( 'Customer', 'woocommerce' ) : $email_class->get_recipient(),
						'enable'      => $email_class->is_enabled(),
						'content'     => $email_class->get_content_type(),
					);
				}

				include_once YITH_WCWTL_DIR . '/templates/admin/email-settings-tab.php';
			} else {
				global $current_section;
				$current_section = $_GET['section'];
				$class           = $mailer->emails[ $active ];

				WC_Admin_Settings::get_settings_pages();

				if ( ! empty( $_POST ) ) {
					$class->process_admin_options();
				}

				include_once YITH_WCWTL_DIR . '/templates/admin/email-settings-single.php';
			}
		}

		/**
		 * Build single email settings page
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro
		 * @param string $email_key
		 * @return string
		 */
		public function build_single_email_settings_url( $email_key ) {
			return admin_url( "admin.php?page={$this->_panel_page}&tab=email&section=" . strtolower( $email_key ) );
		}

		/**
		 * Send mail to users in waitlist for product when pass from 'out of stock' status to 'in stock'
		 *
		 * @access     public
		 * @since      1.0.0
		 * @author     Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param integer $object_id
		 * @param string  $meta_key
		 * @param mixed   $meta_value
		 * @param integer $meta_id
		 * @deprecated This method is deprecated
		 */
		public function mailout_on_status_change( $meta_id, $object_id, $meta_key, $meta_value ) {
			YITH_WCWTL()->mailout_on_status_change_old( $meta_id, $object_id, $meta_key, $meta_value );
		}

		/**
		 * Add query string to standard location redirect after a post update
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param $post_id
		 *
		 * @param $location
		 * @return string
		 */
		public function add_query_to_redirect_location( $location, $post_id ) {

			$response = apply_filters( 'yith_waitlist_mail_instock_send_response', null );

			if ( true === $response ) {
				$location = add_query_arg( 'yith_wcwtl_message', 1, $location );
			}
			elseif( false === $response ) {
				$location = add_query_arg( 'yith_wcwtl_message', 2, $location );
			}

			return esc_url_raw( $location );
		}

		/**
		 * Admin Message after mailout on status change
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function add_mailout_notice() {

			if ( ! ( isset( $_GET['post'] ) && isset( $_GET['yith_wcwtl_message'] ) && get_post_type( $_GET['post'] ) == 'product' ) ) {
				return;
			}

			if ( $_GET['yith_wcwtl_message'] == 1 ) {
				$msg = apply_filters( 'yith_wcwtl_success_message_edit_post', __( 'You have successfully sent the email to the users of the waiting list!', 'yith-woocommerce-waiting-list' ) );
				echo '<div id="yith-success-message" class="updated"><p>' . esc_html( $msg ) . '</p></div>';
			} elseif ( $_GET['yith_wcwtl_message'] == 2 ) {
				$msg = apply_filters( 'yith_wcwtl_error_message_edit_post', __( 'An error occurred sending the email to the users. Please try again.', 'yith-woocommerce-waiting-list' ) );
				echo '<div class="error"><p>' . esc_html( $msg ) . '</p></div>';
			}

		}

		/**
		 * Register plugins for activation tab
		 *
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCWTL_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_WCWTL_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_WCWTL_INIT, YITH_WCWTL_SECRET_KEY, YITH_WCWTL_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once YITH_WCWTL_DIR . 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YITH_WCWTL_SLUG, YITH_WCWTL_INIT );
		}

		/**
		 * Add a new button to tinymce
		 *
		 * @since    1.0
		 * @author   Emanuela Castorina
		 * @return   void
		 */
		public function tc_button() {
			global $typenow;

			if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
				return;
			}

			if ( ! isset( $_GET['page'] ) || $_GET['page'] != $this->_panel_page ) {
				return;
			}

			if ( get_user_option( 'rich_editing' ) == 'true' ) {
				add_filter( "mce_external_plugins", array( $this, 'add_tinymce_plugin' ) );
				add_filter( "mce_buttons", array( $this, 'register_tc_button' ) );
				add_filter( 'mce_external_languages', array( $this, 'add_tc_button_lang' ) );
			}
		}

		/**
		 * Add plugin button to tinymce from filter mce_external_plugins
		 *
		 * @since    1.0
		 * @author   Emanuela Castorina
		 * @return   void
		 */
		function add_tinymce_plugin( $plugin_array ) {
			$min                       = ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.min' : '';
			$plugin_array['tc_button'] = YITH_WCWTL_ASSETS_URL . '/js/tinymce/text-editor' . $min . '.js';
			return $plugin_array;
		}

		/**
		 * Register the custom button to tinymce from filter mce_buttons
		 *
		 * @since    1.0
		 * @author   Emanuela Castorina
		 * @return   void
		 */
		function register_tc_button( $buttons ) {
			array_push( $buttons, "tc_button" );
			return $buttons;
		}

		/**
		 * Add multilingual to mce button from filter mce_external_languages
		 *
		 * @since    1.0
		 * @author   Emanuela Castorina
		 * @return   void
		 */
		function add_tc_button_lang( $locales ) {
			$locales ['tc_button'] = YITH_WCWTL_DIR . 'includes/tinymce/tinymce-plugin-langs.php';
			return $locales;
		}

		/**
		 * Get panel page name
		 *
		 * @access public
		 * @since  1.0.6
		 * @author Francesco Licandro
		 */
		public function get_panel_page_name() {
			return $this->_panel_page;
		}

		/**
		 * Handle table action
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function table_actions() {

			$page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';
			$tab  = isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : '';

			// get action
			$action = '';
			if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] != '-1' ) {
				$action = $_REQUEST['action'];
			} elseif ( isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] != '-1' ) {
				$action = $_REQUEST['action2'];
			} elseif ( isset( $_REQUEST['export_action'] ) ) {
				$action = 'export_action';
			}

			if ( $page != 'yith_wcwtl_panel' || ! in_array( $tab, array( 'exclusions', 'waitlistdata' ) ) || ! $action ) {
				return;
			}

			$mess = '';

			// Delete product/products
			if ( 'delete' === $action && isset( $_GET['id'] ) ) {
				$ids = $_GET['id'];
				if ( ! is_array( $ids ) ) {
					$ids = explode( ',', $ids );
				}


				// delete data for ids
				foreach ( $ids as $id ) {
					if ( $tab == 'exclusions' ) {
						delete_post_meta( $id, '_yith_wcwtl_exclude_list' );
					} else {
						yith_waitlist_empty( $id );
					}
				}
				// add message
				if ( empty( $ids ) ) {
					$mess = 1;
				} else {
					$mess = 2;
				}
			} // Delete users from list
			elseif ( 'remove_user' == $action && isset( $_GET['user_email'] ) && isset( $_GET['id'] ) ) {

				$user_emails = is_array( $_GET['user_email'] ) ? $_GET['user_email'] : array( $_GET['user_email'] );
				foreach ( $user_emails as $user_email ) {
					yith_waitlist_unregister_user( $user_email, $_GET['id'] );
				}

				$mess = 8;
			} // Send Mail action
			elseif ( 'send_mail' == $action && isset( $_GET['id'] ) ) {

				if ( get_option( 'yith-wcwtl-enable' ) !== 'yes' ) {
					$mess = 4;
				} else {
					$post_id = intval( $_GET['id'] );
					$users   = ( isset( $_GET['user'] ) ) ? $_GET['user'] : yith_waitlist_get_registered_users( $post_id );

					global $sitepress;
					if ( function_exists( 'wpml_get_language_information' ) && ! is_null( $sitepress ) ) {
						$post_lang = wpml_get_language_information( null, $post_id );
						( isset( $post_lang['language_code'] ) && $post_lang['language_code'] != $sitepress->get_current_language() ) && $sitepress->switch_lang( $post_lang['language_code'], false );
					}

					do_action( 'send_yith_waitlist_mail_instock', $users, $post_id );

					$res = apply_filters( 'yith_waitlist_mail_instock_send_response', null );

					if ( true === $res ) {
						$mess = 5;
						if ( get_option( 'yith-wcwtl-keep-after-email' ) != 'yes' ) {
							if ( isset( $_GET['user'] ) ) {
								yith_waitlist_unregister_user( $_GET['user'], $post_id );
							} else {
								yith_waitlist_empty( $post_id );
							}
						}
					}
					elseif( false === $res ) {
						$mess = 6;
					}
					// reset to default language
					! is_null( $sitepress ) && $sitepress->switch_lang( $sitepress->get_default_language(), false );
				}
			} // Add users to waiting list list
			elseif ( 'insert_users' === $action ) {
				if ( empty( $_POST['users_id'] ) ) {
					$mess = 3;
				} else {

					$users = is_array( $_POST['users_id'] ) ? $_POST['users_id'] : explode( ',', $_POST['users_id'] );

					foreach ( $users as $user ) {
						$user_data = get_userdata( $user );

						yith_waitlist_register_user( $user_data->user_email, $_GET['id'] );
					}
					$mess = 7;
				}
			} elseif ( 'exclude_products' === $action ) {
				$products_id = ! empty( $_POST['products'] ) ? $_POST['products'] : array();
				! is_array( $products_id ) && $products_id = explode( ',', $products_id );
				// update post meta for each product
				foreach ( $products_id as $product_id ) {
					$product = wc_get_product( $product_id );
					if ( $product ) {
						$product->update_meta_data( '_yith_wcwtl_exclude_list', true );
						$product->save();
					}
				}

				// add message
				if ( empty( $products_id ) ) {
					$mess = 4;
				} else {
					$mess = 3;
				}
			} elseif ( 'export_action' === $action && isset( $_REQUEST['id'] ) ) {
				include( 'class.yith-wcwtl-exporter.php' );
				$exporter = new YITH_WCWTL_Exporter( $_REQUEST['id'] );
				if ( ! $exporter->run() ) {
					return;
				}
			}

			$list_query_args = array(
				'page' => $page,
				'tab'  => $tab,
			);

			// Set users table if any
			if ( ( isset( $_GET['view'] ) || isset( $_POST['view'] ) ) && isset( $_GET['id'] ) ) {
				$view                    = isset( $_GET['view'] ) ? $_GET['view'] : ( isset( $_POST['view'] ) ? $_POST['view'] : '' );
				$list_query_args['view'] = $view;
				$list_query_args['id']   = $_GET['id'];
			}
			// Add message
			if ( isset( $mess ) && $mess != '' ) {
				$list_query_args['wcwtl_mess'] = $mess;

				if ( $mess == 8 ) {
					$list_query_args['wcwtl_count'] = is_array( $_GET['id'] ) ? count( $_GET['id'] ) : 1;
				}
			}

			$list_url = add_query_arg( $list_query_args, admin_url( 'admin.php' ) );

			wp_redirect( $list_url );
			exit;
		}
	}
}
/**
 * Unique access to instance of YITH_WCWTL_Admin_Premium class
 *
 * @since 1.0.0
 * @return \YITH_WCWTL_Admin_Premium
 */
function YITH_WCWTL_Admin_Premium() {
	return YITH_WCWTL_Admin_Premium::get_instance();
}