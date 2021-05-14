<?php
/**
 * Coupons via URL
 *
 * @author      StoreApps
 * @since       4.7.0
 * @version     1.3.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Print_Coupon' ) ) {

	/**
	 * Class for handling coupons applied via URL
	 */
	class WC_SC_Print_Coupon {

		/**
		 * Variable to hold instance of WC_SC_Print_Coupon
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * HTML template path.
		 *
		 * @var string
		 */
		private $template_html;

		/**
		 * Template path.
		 *
		 * @var string
		 */
		private $template_base;

		/**
		 * Constructor
		 */
		private function __construct() {

			// Use our plugin templates directory as the template base.
			$this->template_base = dirname( WC_SC_PLUGIN_FILE ) . '/templates/';

			// Email template location.
			$print_style = get_option( 'wc_sc_coupon_print_style', 'default' );

			$this->template_html = 'print-coupons-' . $print_style . '.php';

			add_action( 'init', array( $this, 'may_be_save_terms_page' ) );
			add_action( 'admin_notices', array( $this, 'may_be_show_terms_notice' ) );
			add_action( 'wp_ajax_wc_sc_terms_notice_action', array( $this, 'terms_notice_action' ) );
			add_action( 'wp_loaded', array( $this, 'print_coupon_from_url' ), 20 );
			add_filter( 'display_post_states', array( $this, 'display_post_states' ), 10, 2 );
			add_filter( 'pre_delete_post', array( $this, 'prevent_deletion_of_terms_page' ), 10, 3 );
			add_filter( 'pre_trash_post', array( $this, 'prevent_deletion_of_terms_page' ), 10, 2 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ), 99 );
		}

		/**
		 * Get single instance of WC_SC_Print_Coupon
		 *
		 * @return WC_SC_Print_Coupon Singleton object of WC_SC_Print_Coupon
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
		 * Function to create and save terms page if already not created
		 */
		public function may_be_save_terms_page() {
			$terms_page_option = $this->get_terms_page_option();
			$terms_page_id     = get_option( $terms_page_option, false );

			// If terms page option is not created yet or may be deleted.
			if ( empty( $terms_page_id ) ) {
				$terms_page_id = $this->add_terms_page();
				$this->update_terms_page_option( $terms_page_id );
			}
		}

		/**
		 * Function to add create terms page
		 */
		public function add_terms_page() {

			$name          = _x( 'wc-sc-coupons-terms', 'Page slug', 'woocommerce-smart-coupons' );
			$title         = _x( 'Smart Coupons Terms', 'Page title', 'woocommerce-smart-coupons' );
			$terms_page_id = $this->create_page( $name, $title, '', 'private' );

			return $terms_page_id;
		}

		/**
		 * Utility function for creating Smart Coupons' pages
		 *
		 * @param  string $slug         Page slug.
		 * @param  string $page_title   Page title.
		 * @param  string $page_content Page content.
		 * @param  string $post_status  Page status.
		 * @param  int    $post_parent  Page parent.
		 * @return int     $page_id      Create page id
		 */
		public function create_page( $slug = '', $page_title = '', $page_content = '', $post_status = 'publish', $post_parent = 0 ) {

			$page_id = 0;
			if ( empty( $slug ) ) {
				return $page_id;
			}

			$args = array(
				'role'    => 'administrator',
				'orderby' => 'ID',
				'order'   => 'ASC',
				'fields'  => 'ID',
			);

			$admin_user_ids = get_users( $args );

			$page_data = array(
				'post_author'    => current( $admin_user_ids ),
				'post_status'    => $post_status,
				'post_type'      => 'page',
				'post_name'      => $slug,
				'post_title'     => $page_title,
				'post_content'   => $page_content,
				'post_parent'    => $post_parent,
				'comment_status' => 'closed',
			);
			$page_id   = wp_insert_post( $page_data );

			return $page_id;
		}

		/**
		 * Function to get terms page id
		 */
		public function get_terms_page_id() {

			$terms_page_option = $this->get_terms_page_option();
			$terms_page_id     = get_option( $terms_page_option, false );

			return $terms_page_id;
		}

		/**
		 * Function to get the option key name for terms page
		 */
		public function get_terms_page_option() {

			// Option key name for Smart Coupons' term page id.
			$terms_page_option = 'wc_sc_terms_page_id';
			return $terms_page_option;
		}

		/**
		 * Function to update terms' page id in the options table
		 *
		 * @param  int $terms_page_id  Page parent.
		 */
		public function update_terms_page_option( $terms_page_id = 0 ) {

			if ( ! empty( $terms_page_id ) && is_numeric( $terms_page_id ) ) {
				$terms_page_option = $this->get_terms_page_option();
				update_option( $terms_page_option, $terms_page_id, 'no' );
			}
		}

		/**
		 * Function to show notice for entering terms' page content
		 */
		public function may_be_show_terms_notice() {

			global $pagenow, $post;

			$is_coupon_enabled = wc_coupons_enabled();
			// Don't show message if coupons are not enabled.
			if ( false === $is_coupon_enabled ) {
				return;
			}

			$terms_page_id = $this->get_terms_page_id();
			// Return if terms page hasn't been set.
			if ( empty( $terms_page_id ) ) {
				return;
			}

			$valid_post_types     = array( 'shop_coupon', 'shop_order', 'product' );
			$valid_pagenow        = array( 'edit.php', 'post.php', 'plugins.php' );
			$is_show_terms_notice = get_option( 'wc_sc_is_show_terms_notice', false );
			$get_post_type        = ( ! empty( $post->post_type ) ) ? $post->post_type : '';
			$get_page             = ( ! empty( $_GET['page'] ) ) ? wc_clean( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore
			$get_tab              = ( ! empty( $_GET['tab'] ) ) ? wc_clean( wp_unslash( $_GET['tab'] ) ) : ''; // phpcs:ignore

			$is_page = ( in_array( $pagenow, $valid_pagenow, true ) || in_array( $get_post_type, $valid_post_types, true ) || ( 'admin.php' === $pagenow && ( 'wc-smart-coupons' === $get_page || 'wc-smart-coupons' === $get_tab ) ) );

			// Terms page notice.
			if ( $is_page && 'no' !== $is_show_terms_notice ) {
				if ( ! wp_script_is( 'jquery' ) ) {
					wp_enqueue_script( 'jquery' );
				}
				?>
				<style type="text/css" media="screen">
					#wc_sc_terms_notice .wc_sc_terms_notice_action {
						float: right;
						padding: 0.5em 0;
						text-align: right;
					}
				</style>
				<script type="text/javascript">
					jQuery(function(){
						jQuery('body').on('click', '#wc_sc_terms_notice a.wc_sc_terms_notice_remove, #wc_sc_terms_notice a.wc_sc_terms_redirect', function(){
							let notice_action = jQuery( this ).data('action');
							jQuery.ajax({
								url: decodeURIComponent( '<?php echo rawurlencode( admin_url( 'admin-ajax.php' ) ); ?>' ),
								type: 'post',
								dataType: 'json',
								data: {
									action: 'wc_sc_terms_notice_action',
									security: '<?php echo esc_html( wp_create_nonce( 'wc-sc-terms-notice-action' ) ); ?>',
									do: notice_action
								},
								success: function( response ){
									if ( response.success != undefined && response.success != '' && response.success == 'yes' ) {
										if( response.redirect_url != undefined && response.redirect_url != '' ) {
											window.location.href = response.redirect_url;
										} else {
											jQuery('#wc_sc_terms_notice').fadeOut(500, function(){ jQuery('#wc_sc_terms_notice').remove(); });
										}
									}
								}
							});
							return false;
						});
					});
				</script>
				<div id="wc_sc_terms_notice" class="updated fade">
					<div class="wc_sc_terms_notice_action">
						<a href="javascript:void(0)" class="wc_sc_terms_notice_remove" data-action="remove"><?php echo esc_html__( 'Never show again', 'woocommerce-smart-coupons' ); ?></a>
					</div>
					<p>
						<?php echo esc_html__( 'Smart Coupons has created a coupon\'s terms page (used during coupon printing) for you. Please edit it as required from', 'woocommerce-smart-coupons' ) . ' <a href="javascript:void(0)" class="wc_sc_terms_redirect" data-action="redirect">' . esc_html__( 'here', 'woocommerce-smart-coupons' ) . '</a>'; ?>
					</p>
				</div>
				<?php
			}
		}

		/**
		 * Function to handle Smart Coupons terms notice action
		 */
		public function terms_notice_action() {

			check_ajax_referer( 'wc-sc-terms-notice-action', 'security' );

			$post_do = ( ! empty( $_POST['do'] ) ) ? wc_clean( wp_unslash( $_POST['do'] ) ) : ''; // phpcs:ignore

			if ( empty( $post_do ) ) {
				return;
			}
			$response = array(
				'success' => 'no',
			);

			$option_updated = update_option( 'wc_sc_is_show_terms_notice', 'no', 'no' );
			if ( true === $option_updated ) {
				$response['success'] = 'yes';
				if ( 'redirect' === $post_do ) {
					$terms_page_id  = $this->get_terms_page_id();
					$terms_edit_url = get_edit_post_link( $terms_page_id, 'edit' );
					if ( ! empty( $terms_edit_url ) ) {
						$response['redirect_url'] = $terms_edit_url;
					}
				}
			}

			wp_send_json( $response );
		}

		/**
		 * Print coupon codes along with terms' page content
		 */
		public function print_coupon_from_url() {
			global $woocommerce_smart_coupon;

			if ( empty( $_SERVER['QUERY_STRING'] ) ) {
				return;
			}

			parse_str( wp_unslash( $_SERVER['QUERY_STRING'] ), $coupon_args ); // phpcs:ignore
			$coupon_args = wc_clean( $coupon_args );

			if ( ! empty( $coupon_args['print-coupons'] ) && 'yes' === $coupon_args['print-coupons'] && ! empty( $coupon_args['source'] ) && 'wc-smart-coupons' === $coupon_args['source'] && ! empty( $coupon_args['coupon-codes'] ) ) {

				$coupon_args['coupon-codes'] = urldecode( $coupon_args['coupon-codes'] );

				$coupon_codes = explode( ',', $coupon_args['coupon-codes'] );

				$coupon_codes = array_filter( $coupon_codes ); // Remove empty coupon codes if any.

				$coupons_data = array();

				foreach ( $coupon_codes as $coupon_code ) {

					if ( empty( $coupon_code ) ) {
						continue;
					}

					$coupons_data[] = array(
						'code' => $coupon_code,
					);
				}

				if ( ! empty( $coupons_data ) ) {
					$terms_page_id      = $this->get_terms_page_id();
					$terms_page_title   = '';
					$terms_page_content = '';
					if ( ! empty( $terms_page_id ) ) {
						$terms_page = get_post( $terms_page_id );
						if ( is_a( $terms_page, 'WP_Post' ) ) {

							$terms_page_title = ( ! empty( $terms_page->post_title ) ) ? $terms_page->post_title : '';

							$terms_page_content = ( ! empty( $terms_page->post_content ) ) ? $terms_page->post_content : '';
							if ( ! empty( $terms_page_content ) ) {
								$terms_page_content = apply_filters( 'the_content', $terms_page_content );
							}
						}
					}
					$design           = get_option( 'wc_sc_setting_coupon_design', 'basic' );
					$background_color = get_option( 'wc_sc_setting_coupon_background_color', '#39cccc' );
					$foreground_color = get_option( 'wc_sc_setting_coupon_foreground_color', '#30050b' );
					$third_color      = get_option( 'wc_sc_setting_coupon_third_color', '#39cccc' );

					$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );

					$valid_designs = $this->get_valid_coupon_designs();

					if ( ! in_array( $design, $valid_designs, true ) ) {
						$design = 'basic';
					}

					$coupon_styles = $woocommerce_smart_coupon->get_coupon_styles( $design );

					$default_path  = $this->template_base;
					$template_path = $woocommerce_smart_coupon->get_template_base_dir( $this->template_html );

					wc_get_template(
						$this->template_html,
						array(
							'coupon_codes'            => $coupons_data,
							'terms_page_title'        => $terms_page_title,
							'terms_page_content'      => $terms_page_content,
							'background_color'        => $background_color,
							'foreground_color'        => $foreground_color,
							'third_color'             => $third_color,
							'coupon_styles'           => $coupon_styles,
							'design'                  => $design,
							'show_coupon_description' => $show_coupon_description,
						),
						$template_path,
						$default_path
					);
				}
				exit;
			}

		}

		/**
		 * Add display state for printable coupon's terms page
		 *
		 * @param array   $post_states An array of post display states.
		 * @param WP_Post $post        The current post object.
		 */
		public function display_post_states( $post_states, $post ) {

			$terms_page_id = $this->get_terms_page_id();

			if ( ! empty( $post->ID ) && ! empty( $terms_page_id ) && absint( $terms_page_id ) === absint( $post->ID ) ) {
				if ( isset( $post_states['private'] ) ) {
					unset( $post_states['private'] );
				}
				$post_states['wc_sc_coupons_terms'] = __( 'Used during coupon printing', 'woocommerce-smart-coupons' );
			}

			return $post_states;
		}

		/**
		 * Prevent Deletion Of Terms Page
		 *
		 * @param  mixed   $is_delete    Whether to allow deletion or not.
		 * @param  WP_Post $post         The post to delete.
		 * @param  boolean $force_delete Whether to permanently delete or not.
		 * @return mixed
		 */
		public function prevent_deletion_of_terms_page( $is_delete = null, $post = null, $force_delete = false ) {
			$terms_page_id = $this->get_terms_page_id();
			if ( ! empty( $post->ID ) && ! empty( $terms_page_id ) && absint( $terms_page_id ) === absint( $post->ID ) ) {
				return false;
			}
			return $is_delete;
		}

		/**
		 * Scripts & styles
		 */
		public function enqueue_scripts_and_styles() {
			global $woocommerce_smart_coupon;

			if ( empty( $_SERVER['QUERY_STRING'] ) ) {
				return;
			}

			parse_str( wp_unslash( $_SERVER['QUERY_STRING'] ), $coupon_args ); // phpcs:ignore
			$coupon_args = wc_clean( $coupon_args );

			$design = get_option( 'wc_sc_setting_coupon_design', 'basic' );

			if ( ! empty( $coupon_args['print-coupons'] ) && 'yes' === $coupon_args['print-coupons'] && ! empty( $coupon_args['source'] ) && 'wc-smart-coupons' === $coupon_args['source'] && ! empty( $coupon_args['coupon-codes'] ) ) {
				if ( 'custom-design' !== $design ) {
					if ( ! wp_style_is( 'smart-coupon-designs' ) ) {
						wp_enqueue_style( 'smart-coupon-designs' );
					}
				}
			}
		}

	}

}

WC_SC_Print_Coupon::get_instance();
