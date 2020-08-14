<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCCH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCCH' ) ) {
	/**
	 * YITH WooCommerce Ajax Navigation
	 *
	 * @since 1.0.0
	 */
	class YITH_WCCH {
		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version;

		/**
		 * Frontend object
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $frontend = null;


		/**
		 * Admin object
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $admin = null;


		/**
		 * Main instance
		 *
		 * @var string
		 * @since 1.4.0
		 */
		protected static $_instance = null;

		/**
		 * Constructor
		 *
		 * @return mixed|YITH_WCCH_Admin|YITH_WCCH_Frontend
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->version = YITH_WCCH_VERSION;

			/*
			 *  Load Plugin Framework
			 */

			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'load_privacy' ), 20 );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			$this->create_tables();
			$this->required();
			$this->init();

			/*
			 *  Check update
			 */

			if ( get_option( 'yith_wcch_db_version' ) != YITH_WCCH_DB_VERSION ) {
				require_once( YITH_WCCH_DIR . 'includes/functions/yith-wcch-updated-version.php' );
				yith_wcch_updated_version();
			}

			/*
			 *  Register plugin to licence/update system
			 */

			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );


			if ( isset( $_POST['csv-export'] ) && $_POST['csv-export'] == 1 ) {
				add_action( 'wp_loaded', array( $this, 'csv_export' ) );
			}

			if ( isset( $_GET['page'] ) && $_GET['page'] == 'yith_wcch_panel' && isset( $_GET['export'] ) && $_GET['export'] == 1 ) {
				add_action( 'wp_loaded', array( $this, 'export' ) );
			}

			if ( isset( $_GET['page'] ) && $_GET['page'] == 'yith_wcch_panel' && isset( $_GET['import'] ) && $_GET['import'] == 1 ) {
				add_action( 'wp_loaded', array( $this, 'import' ) );
			}

			if ( isset( $_GET['page'] ) && $_GET['page'] == 'yith_wcch_panel' && isset( $_GET['delete_sessions'] ) && $_GET['delete_sessions'] == 1 ) {
				add_action( 'wp_loaded', array( $this, 'delete_sessions' ) );
			}

			if ( isset( $_GET['page'] ) && $_GET['page'] == 'yith_wcch_panel' && isset( $_GET['delete_emails'] ) && $_GET['delete_emails'] == 1 ) {
				add_action( 'wp_loaded', array( $this, 'delete_emails' ) );
			}

		}

		/**
		 * Load plugin framework
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @return void
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( !empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 * Plugin row meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @since   1.0.0
		 *
		 * @param   $plugin_meta
		 * @param   $plugin_file
		 * @param   $plugin_data
		 * @param   $status
		 * @param   $init_file
		 *
		 * @return  array
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCCH_INIT' ) {
		    if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
		        $new_row_meta_args['slug'] = YITH_WCCH_SLUG;
		        $new_row_meta_args['is_premium'] = true;
		    }
		    return $new_row_meta_args;
		}
		
		/**
		 * Load Privacy
		 */  
		function load_privacy() {
			require_once( YITH_WCCH_DIR . 'includes/classes/yith-wcch-privacy.php' );
		}

		/**
		 * Main plugin Instance
		 *
		 * @return YITH_WCCH Main instance
		 * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
		 */
		public static function instance() {

			if( is_null( YITH_WCCH::$_instance ) ){ YITH_WCCH::$_instance = new YITH_WCCH(); }
			return YITH_WCCH::$_instance;

		}

		public static function create_tables() {

			/*
			 *  If exists yith_wcch_db_version option return null
			 */

			if ( apply_filters( 'yith_wcch_db_version', get_option( 'yith_wcch_db_version' ) ) != YITH_WCCH_DB_VERSION ) {
				YITH_WCCH_Session::create_tables();
				YITH_WCCH_Email::create_tables();
				update_option( 'yith_wcch_db_version', YITH_WCCH_DB_VERSION );
			}

		}

		/**
		 * Load required files
		 *
		 * @since 1.4
		 * @return void
		 * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
		 */
		public function required() {

			$required = apply_filters( 'yith_wcch_required_files',
				array(
					'includes/classes/yith-wcch-admin.php',
					'includes/classes/yith-wcch-frontend.php',
				)
			);
			foreach( $required as $file ){ file_exists( YITH_WCCH_DIR . $file ) && require_once( YITH_WCCH_DIR . $file ); }

		}

		public function init() {

			if ( is_admin() ) { $this->admin = new YITH_WCCH_Admin( $this->version ); }
			else {

				if ( ! current_user_can( 'manage_woocommerce' ) || get_option('yith-wcch-default_save_admin_session') == 'yes' ) {

					if ( isset( $_GET['s'] ) ) {
						$url = 'ACTION::search::' . $_GET['s'];
						YITH_WCCH_Session::insert( is_user_logged_in() ? get_current_user_id() : 0, $url );
					}

					// Insert session URL
					function yith_wcch_session_insert() {
						if ( ! is_404() ) {
							global $wp;
							$url = add_query_arg( array(), $wp->request );
							if ( yith_wcch_bot_detected() ) {
								$url = 'BOT::' . $_SERVER['HTTP_USER_AGENT'] . '::' . $url;
								YITH_WCCH_Session::insert( 999999999999999, $url );
							} else { YITH_WCCH_Session::insert( is_user_logged_in() ? get_current_user_id() : 0, $url ); }
						}
					}
					add_action( 'wp_footer', 'yith_wcch_session_insert' );

					// Insert add_to_cart action
					function action_woocommerce_add_to_cart( $array, $product_id, $quantity ) {
						$url = 'ACTION::add_to_cart::' . $product_id . '::' . $quantity;
						YITH_WCCH_Session::insert( is_user_logged_in() ? get_current_user_id() : 0, $url );
					};
					add_action( 'woocommerce_add_to_cart', 'action_woocommerce_add_to_cart', 10, 3 );

					// Insert new_order action
					function action_woocommerce_new_order( $order_id ) {
						$url = 'ACTION::new_order::' . $order_id;
						YITH_WCCH_Session::insert( is_user_logged_in() ? get_current_user_id() : 0, $url );
					}
					add_action( 'woocommerce_new_order', 'action_woocommerce_new_order', 10, 1 );

				}

				$this->frontend = new YITH_WCCH_Frontend( $this->version );

			}

		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCCH_DIR . '/plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_WCCH_DIR . '/plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_WCCH_INIT, YITH_WCCH_SECRET_KEY, YITH_WCCH_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) { require_once( YITH_WCCH_DIR . '/plugin-fw/lib/yit-upgrade.php' ); }
			YIT_Upgrade()->register( YITH_WCCH_SLUG, YITH_WCCH_INIT );
		}

		public function csv_export() {

			// ...

		}

		public function export() {

			global $wpdb;

			$query = "SELECT * FROM {$wpdb->prefix}yith_wcch_sessions ORDER BY id ASC";
			$sessions = $wpdb->get_results( $query );

			$query = "SELECT * FROM {$wpdb->prefix}yith_wcch_emails ORDER BY id ASC";
			$emails = $wpdb->get_results( $query );

			if ( ! $sessions && ! $emails ) {
				return false;
			}

			$filename = 'customer-history-export-' . date( 'Y-m-d' ) . '.xml';

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );

			echo "<!-- This is an export of YITH WooCommerce Customer History Premium -->\n";

			/*
			 *  Sessions
			 */

			echo '<export>
	<sessions>
';
			foreach ( $sessions as $session ) : ?>
		<session>
			<id><?php echo absint( $session->id ); ?></id>
			<user_id><?php echo absint($session->user_id); ?></user_id>
			<ip><?php echo $session->ip; ?></ip>
			<url><?php echo $session->url; ?></url>
			<referer><?php echo $session->referer; ?></referer>
			<reg_date><?php echo $session->reg_date; ?></reg_date>
			<del><?php echo $session->del; ?></del>
		</session>
<?php endforeach;
			echo '    </sessions>
';

			/*
			 *  Emails
			 */

			echo '    <emails>
';
			foreach ( $emails as $email ) : ?>
		<email>
			<id><?php echo absint( $email->id ); ?></id>
			<sender_name><?php echo $email->sender_name; ?></sender_name>
			<sender_email><?php echo $email->sender_email; ?></sender_email>
			<user_id><?php echo absint($session->user_id); ?></user_id>
			<subject><?php echo $email->subject; ?></subject>
			<content><?php echo $email->content; ?></content>
			<reg_date><?php echo $email->reg_date; ?></reg_date>
			<del><?php echo $email->del; ?></del>
		</email>
<?php endforeach;
			echo '    </emails>
</export>';

			/*
			 *  End
			 */

			exit();

		}

		public function import( ) {

			if ( isset( $_FILES['wcch_import'] ) && $_FILES['wcch_import']['error'] == UPLOAD_ERR_OK ) {

				global $wpdb;

				$xml = simplexml_load_file( $_FILES['wcch_import']['tmp_name'] );

				$sessions = $xml->sessions->session;

				foreach ( $sessions as $session ) {

					$user_id    = $session->user_id;
					$ipaddress  = $session->ip;
					$url        = $session->url;
					$referer    = $session->referer;
					$reg_date   = $session->reg_date;
					$del        = $session->del;

					$sql = "INSERT INTO {$wpdb->prefix}yith_wcch_sessions (user_id,ip,url,referer,reg_date,del) VALUES ('$user_id','$ipaddress','$url','$referer','$reg_date','$del')";
					$wpdb->query( $sql );

				}

				$emails = $xml->emails->email;

				foreach ( $emails as $email ) {

					$sender_name    = $email->sender_name;
					$sender_email   = $email->sender_email;
					$user_id        = $email->user_id;
					$subject        = $email->subject;
					$content        = $email->content;
					$reg_date       = $email->reg_date;
					$del            = $email->del;

					$sql = "INSERT INTO {$wpdb->prefix}yith_wcch_emails (id,sender_name,sender_email,user_id,subject,content,reg_date,del)
							VALUES ('','$sender_name','$sender_email','$user_id','$subject','$content','$reg_date','$del')";
					$wpdb->query( $sql );

				}

				add_action( 'admin_notices', function(){ echo '<div class="notice notice-success"><p>' . __( 'File imported.', 'yith-woocommerce-customer-history' ) . '</p></div>'; } );

			} else { add_action( 'admin_notices', function(){ echo '<div class="notice notice-error"><p>' . __( 'Error!', 'yith-woocommerce-customer-history' ) . '</p></div>'; } ); }

		}

		public function delete_sessions( ) {

			global $wpdb;

			if ( current_user_can( 'manage_woocommerce' ) ) {

				$sql = "TRUNCATE {$wpdb->prefix}yith_wcch_sessions";
				$result = $wpdb->query( $sql );

				if ( $result ) {

					add_action( 'admin_notices', function(){ echo '<div class="notice notice-success"><p>' . __( 'Sessions deleted.', 'yith-woocommerce-customer-history' ) . '</p></div>'; } );

				} else {

					add_action( 'admin_notices', function(){ echo '<div class="notice notice-error"><p>' . __( 'Error!', 'yith-woocommerce-customer-history' ) . '</p></div>'; } );

				}

			} else { add_action( 'admin_notices', function(){ echo '<div class="notice notice-error"><p>' . __( 'Sorry, you are not allowed to access this page.', 'yith-woocommerce-customer-history' ) . '</p></div>'; } ); }

		}

		public function delete_emails( ) {

			global $wpdb;

			if ( current_user_can( 'manage_woocommerce' ) ) {

				$sql = "TRUNCATE {$wpdb->prefix}yith_wcch_emails";
				$result = $wpdb->query( $sql );

				if ( $result ) {

					add_action( 'admin_notices', function(){ echo '<div class="notice notice-success"><p>' . __( 'Emails deleted.', 'yith-woocommerce-customer-history' ) . '</p></div>'; } );

				} else {

					add_action( 'admin_notices', function(){ echo '<div class="notice notice-error"><p>' . __( 'Error!', 'yith-woocommerce-customer-history' ) . '</p></div>'; } );

				}

			} else { add_action( 'admin_notices', function(){ echo '<div class="notice notice-error"><p>' . __( 'Sorry, you are not allowed to access this page.', 'yith-woocommerce-customer-history' ) . '</p></div>'; } ); }

		}

	}

}
