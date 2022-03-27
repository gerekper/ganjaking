<?php
/**
 * Smart Coupons Admin Notifications
 *
 * @author      StoreApps
 * @since       4.0.0
 * @version     1.3.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_SC_Admin_Notifications' ) ) {

	/**
	 * Class for handling admin pages of Smart Coupons
	 */
	class WC_SC_Admin_Notifications {

		/**
		 * Variable to hold instance of WC_SC_Admin_Notifications
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		private function __construct() {

			add_filter( 'plugin_action_links_' . plugin_basename( WC_SC_PLUGIN_FILE ), array( $this, 'plugin_action_links' ) );

			add_action( 'wp_ajax_wc_sc_review_notice_action', array( $this, 'wc_sc_review_notice_action' ) );
			add_action( 'wp_ajax_wc_sc_40_notice_action', array( $this, 'wc_sc_40_notice_action' ) );
			add_action( 'admin_notices', array( $this, 'show_plugin_notice' ) );

			// To update footer text on SC screens.
			add_filter( 'admin_footer_text', array( $this, 'wc_sc_footer_text' ) );
			add_filter( 'update_footer', array( $this, 'wc_sc_update_footer_text' ), 99 );

			// To show 'Connect your store' notice of WC Helper on SC pages.
			add_filter( 'woocommerce_screen_ids', array( $this, 'add_wc_connect_store_notice_on_sc_pages' ) );

			// Show Database update notices.
			add_action( 'admin_notices', array( $this, 'admin_db_update_notices' ) );

		}

		/**
		 * Get single instance of WC_SC_Admin_Pages
		 *
		 * @return WC_SC_Admin_Pages Singleton object of WC_SC_Admin_Pages
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Function to add more action on plugins page
		 *
		 * @param array $links Existing links.
		 * @return array $links
		 */
		public function plugin_action_links( $links ) {
			$action_links = array(
				'settings' => '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=wc-smart-coupons' ) ) . '">' . esc_html__( 'Settings', 'woocommerce-smart-coupons' ) . '</a>',
				'faqs'     => '<a href="' . esc_url( admin_url( 'admin.php?page=sc-faqs' ) ) . '">' . esc_html__( 'FAQ\'s', 'woocommerce-smart-coupons' ) . '</a>',
				'docs'     => '<a target="_blank" href="' . esc_url( 'https://woocommerce.com/document/smart-coupons/' ) . '">' . __( 'Docs', 'woocommerce-smart-coupons' ) . '</a>',
				'support'  => '<a target="_blank" href="' . esc_url( 'https://woocommerce.com/my-account/create-a-ticket/' ) . '">' . __( 'Support', 'woocommerce-smart-coupons' ) . '</a>',
				'review'   => '<a target="_blank" href="' . esc_url( 'https://woocommerce.com/products/smart-coupons/?review' ) . '">' . __( 'Review', 'woocommerce-smart-coupons' ) . '</a>',
			);

			return array_merge( $action_links, $links );
		}

		/**
		 * Handle Smart Coupons review notice action
		 */
		public function wc_sc_review_notice_action() {

			check_ajax_referer( 'wc-sc-review-notice-action', 'security' );

			$post_do = ( ! empty( $_POST['do'] ) ) ? wc_clean( wp_unslash( $_POST['do'] ) ) : ''; // phpcs:ignore

			$option = strtotime( '+1 month' );
			if ( 'remove' === $post_do ) {
				$option = 'no';
			}

			update_option( 'wc_sc_is_show_review_notice', $option, 'no' );

			wp_send_json( array( 'success' => 'yes' ) );

		}

		/**
		 * Handle Smart Coupons version 4.0.0 notice action
		 */
		public function wc_sc_40_notice_action() {

			check_ajax_referer( 'wc-sc-40-notice-action', 'security' );

			update_option( 'wc_sc_is_show_40_notice', 'no', 'no' );

			wp_send_json( array( 'success' => 'yes' ) );

		}

		/**
		 * Show plugin review notice
		 */
		public function show_plugin_notice() {

			global $pagenow, $post;

			$valid_post_types      = array( 'shop_coupon', 'shop_order', 'product' );
			$valid_pagenow         = array( 'edit.php', 'post.php', 'plugins.php' );
			$is_show_review_notice = get_option( 'wc_sc_is_show_review_notice' );
			$is_coupon_enabled     = get_option( 'woocommerce_enable_coupons' );
			$get_post_type         = ( ! empty( $post->post_type ) ) ? $post->post_type : '';
			$get_page              = ( ! empty( $_GET['page'] ) ) ? wc_clean( wp_unslash( $_GET['page'] ) ) : '';  // phpcs:ignore
			$get_tab               = ( ! empty( $_GET['tab'] ) ) ? wc_clean( wp_unslash( $_GET['tab'] ) ) : '';    // phpcs:ignore
			$design                = get_option( 'wc_sc_setting_coupon_design', 'basic' );

			$is_page = ( in_array( $pagenow, $valid_pagenow, true ) || in_array( $get_post_type, $valid_post_types, true ) || ( 'admin.php' === $pagenow && ( 'wc-smart-coupons' === $get_page || 'wc-smart-coupons' === $get_tab ) ) );

			if ( $is_page && 'yes' !== $is_coupon_enabled ) {
				?>
				<div id="wc_sc_coupon_disabled" class="updated fade error">
					<p>
						<?php
						echo '<strong>' . esc_html__( 'Important', 'woocommerce-smart-coupons' ) . ':</strong> ' . esc_html__( 'Setting "Enable the use of coupon codes" is disabled.', 'woocommerce-smart-coupons' ) . ' ' . sprintf(
							'<a href="%s">%s</a>',
							esc_url(
								add_query_arg(
									array(
										'page' => 'wc-settings',
										'tab'  => 'general',
									),
									admin_url( 'admin.php' )
								)
							),
							esc_html__( 'Enable', 'woocommerce-smart-coupons' )
						) . ' ' . esc_html__( 'it to use', 'woocommerce-smart-coupons' ) . ' <strong>' . esc_html__( 'WooCommerce Smart Coupons', 'woocommerce-smart-coupons' ) . '</strong> ' . esc_html__( 'features.', 'woocommerce-smart-coupons' );
						?>
					</p>
				</div>
				<?php
			}

			// Review Notice.
			if ( $is_page && ! empty( $is_show_review_notice ) && 'no' !== $is_show_review_notice && time() >= absint( $is_show_review_notice ) ) {
				if ( ! wp_script_is( 'jquery' ) ) {
					wp_enqueue_script( 'jquery' );
				}
				?>
				<style type="text/css" media="screen">
					#wc_sc_review_notice .wc_sc_review_notice_action {
						float: right;
						padding: 0.5em 0;
						text-align: right;
					}
				</style>
				<script type="text/javascript">
					jQuery(function(){
						jQuery('body').on('click', '#wc_sc_review_notice .wc_sc_review_notice_action a.wc_sc_review_notice_remind', function( e ){
							jQuery.ajax({
								url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
								type: 'post',
								dataType: 'json',
								data: {
									action: 'wc_sc_review_notice_action',
									security: '<?php echo esc_html( wp_create_nonce( 'wc-sc-review-notice-action' ) ); ?>',
									do: 'remind'
								},
								success: function( response ){
									if ( response.success != undefined && response.success != '' && response.success == 'yes' ) {
										jQuery('#wc_sc_review_notice').fadeOut(500, function(){ jQuery('#wc_sc_review_notice').remove(); });
									}
								}
							});
							return false;
						});
						jQuery('body').on('click', '#wc_sc_review_notice .wc_sc_review_notice_action a.wc_sc_review_notice_remove', function(){
							jQuery.ajax({
								url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
								type: 'post',
								dataType: 'json',
								data: {
									action: 'wc_sc_review_notice_action',
									security: '<?php echo esc_html( wp_create_nonce( 'wc-sc-review-notice-action' ) ); ?>',
									do: 'remove'
								},
								success: function( response ){
									if ( response.success != undefined && response.success != '' && response.success == 'yes' ) {
										jQuery('#wc_sc_review_notice').fadeOut(500, function(){ jQuery('#wc_sc_review_notice').remove(); });
									}
								}
							});
							return false;
						});
					});
				</script>
				<div id="wc_sc_review_notice" class="updated fade">
					<div class="wc_sc_review_notice_action">
						<a href="javascript:void(0)" class="wc_sc_review_notice_remind"><?php echo esc_html__( 'Remind me after a month', 'woocommerce-smart-coupons' ); ?></a><br>
						<a href="javascript:void(0)" class="wc_sc_review_notice_remove"><?php echo esc_html__( 'Never show again', 'woocommerce-smart-coupons' ); ?></a>
					</div>
					<p>
						<?php echo esc_html__( 'Awesome, you successfully auto-generated a coupon! Are you having a great experience with', 'woocommerce-smart-coupons' ) . ' <strong>' . esc_html__( 'WooCommerce Smart Coupons', 'woocommerce-smart-coupons' ) . '</strong> ' . esc_html__( 'so far?', 'woocommerce-smart-coupons' ) . '<br>' . esc_html__( 'Please consider', 'woocommerce-smart-coupons' ) . ' <a href="' . esc_url( 'https://woocommerce.com/products/smart-coupons/#reviews' ) . '">' . esc_html__( 'leaving a review', 'woocommerce-smart-coupons' ) . '</a> ' . esc_html__( '! If things aren\'t going quite as expected, we\'re happy to help -- please reach out to', 'woocommerce-smart-coupons' ) . ' <a href="' . esc_url( 'https://woocommerce.com/my-account/create-a-ticket/' ) . '">' . esc_html__( 'our support team', 'woocommerce-smart-coupons' ) . '</a>.'; ?>
					</p>
				</div>
				<?php
			}

			if ( $is_page && 'custom-design' === $design ) {
				?>
				<div class="updated fade error" style="background-color: #f0fff0;">
					<p>
						<?php
						echo sprintf(
							/* translators: 1: WooCommerce Smart Coupons 2: Link for the Smart Coupons settings */
							esc_html__( '%1$s: You are using a custom coupon style which is planned to be removed from the plugin in upcoming versions. New, improved styles & colors are added in the version 4.9.0. We would request you to choose a color scheme & a style for coupon from the newly added colors & styles. You can do this from %2$s.', 'woocommerce-smart-coupons' ),
							'<strong>' . esc_html__( 'WooCommerce Smart Coupons', 'woocommerce-smart-coupons' ) . '</strong>',
							'<a href="' . esc_url(
								add_query_arg(
									array(
										'page' => 'wc-settings',
										'tab'  => 'wc-smart-coupons',
									),
									admin_url( 'admin.php' )
								)
							) . '" target="_blank">' . esc_html__(
								'Smart Coupons settings',
								'woocommerce-smart-coupons'
							) . '</a>'
						);
						?>
					</p>
				</div>
				<?php
			}

		}

		/**
		 * Function to 'Connect your store' notice on Smart Coupons pages in admin
		 *
		 * @param  string $sc_rating_text Text in footer (left).
		 * @return string $sc_rating_text
		 */
		public function wc_sc_footer_text( $sc_rating_text ) {

			global $post, $pagenow;

			if ( ! empty( $pagenow ) ) {
				$get_post_type = ( ! empty( $post->post_type ) ) ? $post->post_type : '';
	  			$get_page      = ( ! empty( $_GET['page'] ) ) ? wc_clean( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore
				$get_tab       = ( ! empty( $_GET['tab'] ) ) ? wc_clean( wp_unslash( $_GET['tab'] ) ) : ''; // phpcs:ignore
				$sc_pages      = array( 'wc-smart-coupons', 'sc-about', 'sc-faqs' );

				if ( in_array( $get_page, $sc_pages, true ) || 'shop_coupon' === $get_post_type || 'wc-smart-coupons' === $get_tab ) {
					?>
					<style type="text/css">
						#wpfooter {
							display: block !important;
						}
					</style>
					<?php
					/* translators: %s: link to review WooCommerce Smart Coupons */
					$sc_rating_text = wp_kses_post( sprintf( __( 'Liked WooCommerce Smart Coupons? Leave us a %s. A huge thank you from WooCommerce & StoreApps in advance!', 'woocommerce-smart-coupons' ), '<a target="_blank" href="' . esc_url( 'https://woocommerce.com/products/smart-coupons/?review' ) . '" style="color: #5850EC;">5-star rating here</a>' ) );
				}
			}

			return $sc_rating_text;

		}

		/**
		 * Function to 'Connect your store' notice on Smart Coupons pages in admin
		 *
		 * @param  string $sc_text Text in footer (right).
		 * @return string $sc_text
		 */
		public function wc_sc_update_footer_text( $sc_text ) {

			global $post, $pagenow;

			if ( ! empty( $pagenow ) ) {
				$get_post_type = ( ! empty( $post->post_type ) ) ? $post->post_type : '';
	  			$get_page      = ( ! empty( $_GET['page'] ) ) ? wc_clean( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore
	  			$get_tab       = ( ! empty( $_GET['tab'] ) ) ? wc_clean( wp_unslash( $_GET['tab'] ) ) : ''; // phpcs:ignore
				$sc_pages      = array( 'wc-smart-coupons', 'sc-about', 'sc-faqs' );

				if ( in_array( $get_page, $sc_pages, true ) || 'shop_coupon' === $get_post_type || 'wc-smart-coupons' === $get_tab ) {
					/* translators: %s: link to submit idea for Smart Coupons on WooCommerce idea board */
					$sc_text = sprintf( __( 'Have a feature request? Submit it on our %s.', 'woocommerce-smart-coupons' ), '<a href="' . esc_url( 'http://ideas.woocommerce.com/forums/133476-woocommerce?category_id=163716' ) . '" target="_blank" style="color: #5850EC;">idea board</a>' );
				}
			}

			return $sc_text;

		}

		/**
		 * Function to 'Connect your store' notice on Smart Coupons pages in admin
		 *
		 * @param  array $screen_ids List of existing screen ids.
		 * @return array $screen_ids
		 */
		public function add_wc_connect_store_notice_on_sc_pages( $screen_ids ) {

			array_push( $screen_ids, 'woocommerce_page_wc-smart-coupons' );

			return $screen_ids;
		}

		/**
		 * Function to render admin notice
		 *
		 * @param string $type         Notice type.
		 * @param string $title        Notice title.
		 * @param string $message      Notice message.
		 * @param string $action       Notice actions.
		 * @param bool   $dismissible  Notice dismissible.
		 * @return void.
		 */
		public function show_notice( $type = 'info', $title = '', $message = '', $action = '', $dismissible = false ) {
			$css_classes = array(
				'notice',
				'notice-' . $type,
			);
			if ( true === $dismissible ) {
				$css_classes[] = 'is-dismissible';
			}
			?>
			<div class="<?php echo esc_attr( implode( ' ', $css_classes ) ); ?>">
				<?php
				if ( ! empty( $title ) ) {
					printf( '<p><strong>%s</strong></p>', esc_html( $title ) );
				}
				if ( ! empty( $message ) ) {
					printf( '<p>%s</p>', esc_html( $message ) );
				}
				if ( ! empty( $action ) ) {
					printf( '<p class="submit">%s</p>', wp_kses_post( $action ) );
				}
				?>
			</div>
			<?php
		}

		/**
		 * Function to show database update notice
		 */
		public function admin_db_update_notices() {
			if ( ! class_exists( 'WC_SC_Background_Upgrade' ) ) {
				include_once 'class-wc-sc-background-upgrade.php';
			}
			$wcsc_db       = WC_SC_Background_Upgrade::get_instance();
			$update_status = $wcsc_db->get_status( '4.28.0' );
			if ( 'pending' === $update_status ) {
				// Notice for pending update.
				$this->db_update_pending_notice();
			} elseif ( 'processing' === $update_status ) {
				// Notice for processing update.
				$this->db_update_processing_notice();
			} elseif ( 'completed' === $update_status ) {
				// Notice for completed update.
				$this->db_update_completed_notice();
				$wcsc_db->set_status( '4.28.0', 'done' );
			}
		}

		/**
		 * Function to show pending database update notice
		 */
		public function db_update_pending_notice() {
			global $woocommerce_smart_coupon;

			$plugin_version = $woocommerce_smart_coupon->get_smart_coupons_version();
			/* translators: %s: Plugin name */
			$title         = sprintf( __( '%s database update required', 'woocommerce-smart-coupons' ), 'WooCommerce Smart Coupons' );
			$message       = __( 'The database update process runs in the background and may take a little while, so please be patient.', 'woocommerce-smart-coupons' );
			$update_url    = wp_nonce_url(
				add_query_arg(
					array(
						'page'         => 'wc-settings',
						'tab'          => 'wc-smart-coupons',
						'wc_sc_update' => '4.28.0',
					),
					admin_url( 'admin.php' )
				),
				'wc_sc_db_process',
				'wc_sc_db_update_nonce'
			);
			$action_button = sprintf( '<a href="%1$s" class="button button-primary">%2$s</a>', esc_url( $update_url ), __( 'Update database', 'woocommerce-smart-coupons' ) );

			$this->show_notice( 'warning', $title, $message, $action_button );
		}

		/**
		 * Function to show database update processing notice.
		 */
		public function db_update_processing_notice() {
			if ( 'woocommerce_page_wc-status' === $this->get_current_screen_id() && isset( $_GET['tab'] ) && 'action-scheduler' === wc_clean( wp_unslash( $_GET['tab'] ) ) ) { // phpcs:ignore
				return;
			}

			$actions_url   = add_query_arg(
				array(
					'page'   => 'wc-status',
					'tab'    => 'action-scheduler',
					's'      => 'move_applied_coupon_options_to_transient',
					'status' => 'pending',
				),
				admin_url( 'admin.php' )
			);
			$cron_disabled = defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON;
			/* translators: %s: Plugin name */
			$message = sprintf( __( '%s is updating the database in the background. The database update process may take a little while, so please be patient.', 'woocommerce-smart-coupons' ), 'WooCommerce Smart Coupons' );
			if ( true === $cron_disabled ) {
				$message .= '<br>' . __( 'Note: WP CRON has been disabled on your install which may prevent this update from completing.', 'woocommerce-smart-coupons' );
			}
			$action_button = sprintf( '<a href="%1$s" class="button button-secondary">%2$s</a>', esc_url( $actions_url ), __( 'View status', 'woocommerce-smart-coupons' ) );
			$this->show_notice( 'info', '', $message, $action_button );
		}

		/**
		 * Function to show database update completed notice.
		 */
		public function db_update_completed_notice() {
			/* translators: %s: Plugin name */
			$message = sprintf( __( '%s database update completed. Thank you for updating to the latest version!', 'woocommerce-smart-coupons' ), 'WooCommerce Smart Coupons' );
			$this->show_notice( 'success', '', $message, '', true );
		}

		/**
		 * Function to get current screen id.
		 *
		 * @return string.
		 */
		public function get_current_screen_id() {
			$screen = get_current_screen();
			return $screen ? $screen->id : '';
		}

	}

}

WC_SC_Admin_Notifications::get_instance();
