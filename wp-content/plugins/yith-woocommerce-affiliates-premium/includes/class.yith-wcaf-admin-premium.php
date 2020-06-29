<?php
/**
 * Admin class premium
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Admin_Premium' ) ) {
	/**
	 * WooCommerce Affiliates Admin Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Admin_Premium extends YITH_WCAF_Admin {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCAF_Admin_Premium
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAF_Admin_Premium
		 * @since 1.0.0
		 */
		public function __construct() {
			add_filter( 'yith_wcaf_available_admin_tabs', array( $this, 'filter_admin_tabs' ) );
			add_filter( 'yith_wcaf_general_settings', array( $this, 'filter_general_settings' ) );

			// custom fields.
			add_action( 'woocommerce_admin_field_yith_wcaf_template', array( $this, 'print_template_field' ) );

			// register plugin to licence/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			parent::__construct();
		}

		/**
		 * Filters tabs for admin section
		 *
		 * @param mixed $tabs Array of available tabs.
		 *
		 * @return mixed Filtered array of tabs
		 * @since 1.0.0
		 */
		public function filter_admin_tabs( $tabs ) {
			$array_chunk_1 = array_slice( $tabs, 0, 2 );
			$array_chunk_2 = array_splice( $tabs, 1, count( $tabs ) - 1 );

			$tabs = array_merge(
				$array_chunk_1,
				array(
					'rates' => __( 'Rates', 'yith-woocommerce-affiliates' ),
				),
				YITH_WCAF_Click_Handler()->are_hits_registered() ? array(
					'clicks' => __( 'Clicks', 'yith-woocommerce-affiliates' ),
				) : array(),
				$array_chunk_2
			);

			unset( $tabs['premium'] );
			return $tabs;
		}

		/**
		 * Filters settings for premium users
		 *
		 * @param mixed $settings Array of available settings.
		 * @return mixed Filtered array of settings
		 * @since 1.0.6
		 */
		public function filter_general_settings( $settings ) {
			$settings_options      = $settings['settings'];
			$before_index          = 'general-options';
			$before_index_position = array_search( $before_index, array_keys( $settings_options ), true );

			$settings_options_chunk_1 = array_slice( $settings_options, 0, $before_index_position + 1 );
			$settings_options_chunk_2 = array_slice( $settings_options, $before_index_position + 1, count( $settings_options ) );

			$premium_settings = array(
				'general-referral-cod' => array(
					'title'    => __( 'How to get referrer id', 'yith-woocommerce-affiliates' ),
					'type'     => 'select',
					'desc'     => __( 'Choose how to receive referrer id during purchase', 'yith-woocommerce-affiliates' ),
					'options'  => array(
						'query_string' => __( 'Via query string', 'yith-woocommerce-affiliates' ),
						'checkout'     => __( 'Let users enter it in checkout page', 'yith-woocommerce-affiliates' ),
					),
					'id'       => 'yith_wcaf_general_referral_cod',
					'default'  => 'query_string',
					'desc_tip' => true,
					'css'      => 'min-width: 300px;',
				),
			);

			$settings['settings'] = array_merge(
				$settings_options_chunk_1,
				$premium_settings,
				$settings_options_chunk_2
			);

			return $settings;
		}

		/* === PLUGIN LINK METHODS === */

		/**
		 * Adds plugin row meta
		 *
		 * @param array $plugin_meta Array of unfiltered plugin meta.
		 * @param string $plugin_file Plugin base file path.
		 *
		 * @return array Filtered array of plugin meta
		 * @since 1.0.0
		 */
		public function add_plugin_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCAF_PREMIUM_INIT' ) {
			$new_row_meta_args = parent::add_plugin_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

		/**
		 * Add plugin action links
		 *
		 * @param mixed $links Plugins links array.
		 *
		 * @return array Filtered link array
		 * @since 1.0.0
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, 'yith_wcaf_panel', true );

			return $links;
		}

		/* === CUSTOM FIELDS === */

		/**
		 * Print template type field
		 *
		 * @param array $value Array of options for the field to print.
		 *
		 * @return void
		 * @since 1.3.0
		 */
		public function print_template_field( $value ) {
			if ( ! isset( $value['template'] ) ) {
				return;
			}

			$template = $value['template'];

			if (
				( ! empty( $_GET['move_template'] ) || ! empty( $_GET['delete_template'] ) )
				&& 'GET' === $_SERVER['REQUEST_METHOD'] // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			) {
				if ( empty( $_GET['_yith_wcaf_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_yith_wcaf_nonce'] ) ), 'yith_wcaf_template_nonce' ) ) {
					wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'yith-woocommerce-affiliates' ) );
				}

				if ( ! current_user_can( 'edit_themes' ) ) {
					wp_die( esc_html__( 'You don&#8217;t have permission to do this.', 'yith-woocommerce-affiliates' ) );
				}

				if ( ! empty( $_GET['move_template'] ) ) {
					$this->move_template_action( sanitize_text_field( wp_unslash( $_GET['move_template'] ) ) );
				}

				if ( ! empty( $_GET['delete_template'] ) ) {
					$this->delete_template_action( sanitize_text_field( wp_unslash( $_GET['delete_template'] ) ) );
				}
			}

			if ( ! empty( $_POST['save_template'] ) && is_array( $_POST['save_template'] ) && 'POST' === sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) && current_user_can( 'edit_themes' ) ) {
				foreach ( $_POST['save_template'] as $template ) {
					$template       = wc_clean( wp_unslash( $template ) );
					$code_post_data = str_replace( '.', '_', $template ) . '_code';

					if ( isset( $_POST[ $code_post_data ] ) ) {
						$this->save_template( $_POST[ $code_post_data ], $template );
					}
				}
			}

			include( YITH_WCAF_DIR . 'templates/admin/types/invoice-template-field.php' );
		}

		/* === UTILITY METHODS === */

		/**
		 * Return path for a specific template inside theme folder
		 *
		 * @param string $template Template to locate.
		 *
		 * @return string Path to template
		 * @since 1.3.0
		 */
		public function get_theme_template_file( $template ) {
			return get_stylesheet_directory() . '/' . apply_filters( 'woocommerce_template_directory', 'woocommerce', $template ) . '/yith-wcaf/' . $template;
		}

		/**
		 * Save the templates
		 *
		 * @param string $template_code Template code.
		 * @param string $template_path Template path.
		 *
		 * @since 1.3.0
		 */
		protected function save_template( $template_code, $template_path ) {

			if ( current_user_can( 'edit_themes' ) && ! empty( $template_code ) && ! empty( $template_path ) ) {
				$saved = false;
				$file  = get_stylesheet_directory() . '/' . WC()->template_path() . $template_path;
				$file  = $this->get_theme_template_file( $template_path );
				$code  = wp_unslash( $template_code );

				if ( is_writeable( $file ) ) {
					/**
					 * @var $wp_filesystem \WP_Filesystem_Base
					 */
					global $wp_filesystem;

					$saved = $wp_filesystem->put_contents( $file, $code );
				}

				if ( ! $saved ) {
					?>
					<div class="error">
						<p><?php echo esc_html__( 'Could not write to template file.', 'woocommerce' ); ?></p>
					</div>
					<?php
				}
			}
		}

		/**
		 * Move template action.
		 *
		 * @param string $template Template to move.
		 *
		 * @return void
		 * @since 1.3.0
		 */
		protected function move_template_action( $template ) {
			$theme_file = $this->get_theme_template_file( $template );

			if ( wp_mkdir_p( dirname( $theme_file ) ) && ! file_exists( $theme_file ) ) {

				// Locate template file.
				$template_file = yith_wcaf_locate_template( $template );

				// Copy template file.
				copy( $template_file, $theme_file );

				/**
				 * Action hook fired after copying email template file.
				 *
				 * @param string $template_type The copied template type
				 * @param string $email         The email object
				 */
				do_action( 'yith_wcaf_copy_template', $template, $this );

				?>
				<div class="updated">
					<p><?php echo esc_html__( 'Template file copied to theme.', 'woocommerce' ); ?></p>
				</div>
				<?php
			}
		}

		/**
		 * Delete template action.
		 *
		 * @param string $template Template to delete.
		 *
		 * @return void
		 * @since 1.3.0
		 */
		protected function delete_template_action( $template ) {
			if ( ! empty( $template ) ) {
				$theme_file = $this->get_theme_template_file( $template );

				if ( file_exists( $theme_file ) ) {
					unlink( $theme_file ); // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_unlink

					/**
					 * Action hook fired after deleting template file.
					 *
					 * @param string $template The deleted template type
					 * @param string $email    The email object
					 */
					do_action( 'yith_wcaf_delete_template', $template, $this );
					?>
					<div class="updated">
						<p><?php echo esc_html__( 'Template file deleted from theme.', 'woocommerce' ); ?></p>
					</div>
					<?php
				}
			}
		}

		/* === STATS METHODS === */

		/**
		 * Print plugin stat panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_stat_panel() {
			$page_title   = __( 'Global stats', 'yith-woocommerce-affiliates' );
			$title_suffix = '';

			// init stat filters.
			$from         = isset( $_REQUEST['_from'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_from'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$to           = isset( $_REQUEST['_to'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_to'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$affiliate_id = isset( $_REQUEST['_affiliate_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_affiliate_id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$need_reset   = false;

			$filters = array();

			if ( ! empty( $from ) ) {
				$filters['interval']['start_date'] = gmdate( 'Y-m-d 00:00:00', strtotime( $from ) );
			}

			if ( ! empty( $to ) ) {
				$filters['interval']['end_date'] = gmdate( 'Y-m-d 23:59:59', strtotime( $to ) );
			}

			if ( ! empty( $from ) || ! empty( $to ) ) {
				$title_suffix = sprintf( ' (%s - %s)', ! empty( $from ) ? date_i18n( wc_date_format(), strtotime( $from ) ) : __( 'Ever', 'yith-woocommerce-affiliates' ), ! empty( $to ) ? date_i18n( wc_date_format(), strtotime( $to ) ) : __( 'Today', 'yith-woocommerce-affiliates' ) );
			}

			if ( ! empty( $affiliate_id ) ) {
				$filters['affiliate_id'] = intval( $affiliate_id );
				$affiliate               = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $affiliate_id );

				if ( $affiliate ) {
					$user = get_userdata( $affiliate['user_id'] );
					if ( ! is_wp_error( $user ) ) {
						$username = '';

						if ( $user->first_name || $user->last_name ) {
							$username .= esc_html( ucfirst( $user->first_name ) . ' ' . ucfirst( $user->last_name ) );
						} else {
							$username .= esc_html( ucfirst( $user->display_name ) );
						}

						$selected = $username . ' (#' . $user->ID . ' &ndash; ' . sanitize_email( $user->user_email ) . ')';

						// translators: 1. Affiliate username (First name/Last name when available; display_name otherwise).
						$page_title = sprintf( __( 'Stats for affiliate "%s"', 'yith-woocommerce-affiliates' ), $username );
					}
				}
			}

			if ( ! empty( $from ) || ! empty( $to ) || ! empty( $affiliate_id ) ) {
				$need_reset = true;
				$reset_link = esc_url(
					add_query_arg(
						array(
							'page' => 'yith_wcaf_panel',
							'tab'  => 'stats',
						),
						admin_url( 'admin.php' )
					)
				);
			}

			// define variables to be used in the template.
			$total_amount   = YITH_WCAF_Commission_Handler()->get_commission_stats( 'total_amount', array_merge( $filters, array( 'status' => array( 'pending', 'pending-payment', 'paid' ) ) ) );
			$total_paid     = YITH_WCAF_Commission_Handler()->get_commission_stats( 'total_amount', array_merge( $filters, array( 'status' => array( 'pending-payment', 'paid' ) ) ) );
			$total_earned   = YITH_WCAF_Commission_Handler()->get_commission_stats( 'total_earned', array_merge( $filters, array( 'status' => array( 'pending', 'pending-payment', 'paid' ) ) ) );
			$total_refunded = YITH_WCAF_Commission_Handler()->get_commission_stats( 'total_refunds', array_merge( $filters ) );

			$total_clicks      = YITH_WCAF_Click_Handler()->get_hit_stats( 'total_clicks', $filters );
			$total_conversions = YITH_WCAF_Click_Handler()->get_hit_stats( 'total_conversions', $filters );
			$avg_conv_rate     = ! empty( $total_clicks ) ? $total_conversions / $total_clicks * 100 : 0;
			$avg_conv_rate     = ! empty( $avg_conv_rate ) ? number_format( $avg_conv_rate, 2 ) . '%' : __( 'N/A', 'yith-woocommerce-affiliates' );

			$avg_conv_time          = YITH_WCAF_Click_Handler()->get_hit_stats( 'avg_conv_time', $filters );
			$readable_avg_conv_time = ! empty( $avg_conv_time ) ? human_time_diff( time(), time() + $avg_conv_time ) : __( 'N/A', 'yith-woocommerce-affiliates' );

			$page_title .= $title_suffix;

			$product_table = new YITH_WCAF_Product_Stat_Table();
			$product_table->prepare_items();

			// includes panel template.
			include YITH_WCAF_DIR . 'templates/admin/stat-panel-premium.php';
		}

		/* === LICENCE HANDLING METHODS === */

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCAF_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_WCAF_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}

			YIT_Plugin_Licence()->register( YITH_WCAF_INIT, YITH_WCAF_SECRET_KEY, YITH_WCAF_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCAF_DIR . 'plugin-fw/lib/yit-upgrade.php';
			}

			YIT_Upgrade()->register( YITH_WCAF_SLUG, YITH_WCAF_INIT );
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF_Admin_Premium
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF_Admin_Premium class
 *
 * @return \YITH_WCAF_Admin_Premium
 * @since 1.0.0
 */
function YITH_WCAF_Admin_premium() {
	return YITH_WCAF_Admin_premium::get_instance();
}