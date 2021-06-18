<?php
/**
 * Class to handle feature Coupon Message
 *
 * @author      Ratnakar
 * @category    Admin
 * @package     wocommerce-smart-coupons/includes
 * @version     1.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Coupon_Message' ) ) {

	/**
	 * Class WC_SC_Coupon_Message
	 */
	class WC_SC_Coupon_Message {

		/**
		 * Variable to hold instance of this class
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 *
		 * @since 1.0
		 */
		private function __construct() {

			add_action( 'wc_smart_coupons_actions', array( $this, 'wc_coupon_message_options' ), 10, 2 );
			add_action( 'save_post', array( $this, 'wc_process_coupon_message_meta' ), 1, 2 );

			add_action( 'wp_ajax_get_wc_coupon_message', array( $this, 'get_wc_coupon_message' ) );
			add_action( 'wp_ajax_nopriv_get_wc_coupon_message', array( $this, 'get_wc_coupon_message' ) );
			add_action( 'woocommerce_before_cart_table', array( $this, 'wc_coupon_message_display' ) );
			add_action( 'woocommerce_checkout_before_customer_details', array( $this, 'wc_coupon_message_display' ) );
			add_action( 'woocommerce_email_after_order_table', array( $this, 'wc_add_coupons_message_in_email' ), null, 3 );

			add_filter( 'wc_smart_coupons_export_headers', array( $this, 'export_headers' ) );
			add_filter( 'smart_coupons_parser_postmeta_defaults', array( $this, 'postmeta_defaults' ) );
			add_filter( 'sc_generate_coupon_meta', array( $this, 'generate_coupon_meta' ), 10, 2 );
			add_filter( 'is_protected_meta', array( $this, 'make_action_meta_protected' ), 10, 3 );

			add_action( 'wc_sc_new_coupon_generated', array( $this, 'copy_coupon_action_meta' ) );

		}

		/**
		 * Get single instance of this class
		 *
		 * @return this class Singleton object of this class
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
		 * Function to add additional fields for coupon
		 *
		 * @param integer   $coupon_id The coupon id.
		 * @param WC_Coupon $coupon The coupon object.
		 * @since 1.0
		 */
		public function wc_coupon_message_options( $coupon_id = 0, $coupon = null ) {
			global $post;
			?>
			<style type="text/css">
				#wc_coupon_message_options #wc_coupon_message_ifr {
					height: 100% !important;
				}
				#wp-wc_coupon_message-wrap {
					display: inline-block;
					width: 70%;
					margin: -3em 0 0 12.5em;
				}
				.wp_editor_coupon_message {
					width: 100%;
				}
			</style>
			<?php
			$editor_args = array(
				'textarea_name' => 'wc_coupon_message',
				'textarea_rows' => 10,
				'editor_class'  => 'wp_editor_coupon_message',
				'media_buttons' => true,
				'tinymce'       => true,
			);
			echo '<div id="wc_coupon_message_options" class="options_group smart-coupons-field">';
			?>
			<p class="form-field wc_coupon_message_row">
				<label for="wc_coupon_message"><?php echo esc_html__( 'Display message', 'woocommerce-smart-coupons' ); ?></label>
				<?php $wc_coupon_message = get_post_meta( $post->ID, 'wc_coupon_message', true ); ?>
				<?php wp_editor( $wc_coupon_message, 'wc_coupon_message', $editor_args ); ?>
			</p>
			<?php
			woocommerce_wp_checkbox(
				array(
					'id'          => 'wc_email_message',
					'label'       => __( 'Email message?', 'woocommerce-smart-coupons' ),
					'description' => __(
						'Check this box to include above message in order confirmation email',
						'woocommerce-smart-coupons'
					),
				)
			);
			echo '</div>';
		}

		/**
		 * Function to save coupon plus data in coupon's meta
		 *
		 * @since 1.0
		 *
		 * @param  integer $post_id Coupon's id.
		 * @param  object  $post Current coupon's post object.
		 */
		public function wc_process_coupon_message_meta( $post_id, $post ) {

			if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( is_int( wp_is_post_revision( $post ) ) ) {
				return;
			}
			if ( is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}
			if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( wc_clean( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ), 'woocommerce_save_data' ) ) { // phpcs:ignore
				return;
			}
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			if ( 'shop_coupon' !== $post->post_type ) {
				return;
			}

			if ( isset( $_POST['wc_coupon_message'] ) ) {
				update_post_meta( $post_id, 'wc_coupon_message', wp_filter_post_kses( $_POST['wc_coupon_message'] ) ); // phpcs:ignore
			}

			if ( isset( $_POST['wc_email_message'] ) ) {
				update_post_meta( $post_id, 'wc_email_message', wc_clean( wp_unslash( $_POST['wc_email_message'] ) ) ); // phpcs:ignore
			} else {
				update_post_meta( $post_id, 'wc_email_message', 'no' );
			}

		}

		/**
		 * Function to print coupon message
		 *
		 * @param array $applied_coupons Applied coupons.
		 */
		public function print_coupon_message( $applied_coupons = array() ) {

			if ( empty( $applied_coupons ) ) {
				echo '<div class="no_wc_coupon_message"></div>';
				return;
			}

			foreach ( $applied_coupons as $coupon_code ) {

				$coupon = new WC_Coupon( $coupon_code );
				if ( ! $coupon->is_valid() ) {
					continue;
				}
				if ( $this->is_wc_gte_30() ) {
					$coupon_id = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
				} else {
					$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				}
				$wc_coupon_message = get_post_meta( $coupon_id, 'wc_coupon_message', true );
				if ( empty( $wc_coupon_message ) ) {
					continue;
				}
				?>
					<div id="wc_coupon_message_<?php echo esc_attr( $coupon_id ); ?>" class="wc_coupon_message_container">
						<div class="wc_coupon_message_body">
							<?php
								$is_filter_content = apply_filters(
									'wc_sc_is_filter_content_coupon_message',
									true,
									array(
										'source'        => $this,
										'called_by'     => current_filter(),
										'coupon_object' => $coupon,
									)
								);
							if ( true === $is_filter_content ) {
								$wc_coupon_message = apply_filters( 'the_content', $wc_coupon_message );
							}
							?>
							<?php echo wp_kses_post( $wc_coupon_message ); // phpcs:ignore ?>
						</div>
					</div>
				<?php

			}
		}

		/**
		 * Function to validate applied coupon's additional field which comes from this plugin
		 *
		 * @since 1.0
		 */
		public function wc_coupon_message_display() {

			if ( ! is_object( WC() ) || ! is_object( WC()->cart ) || WC()->cart->is_empty() ) {
				return;
			}

			$applied_coupons = WC()->cart->get_applied_coupons();

			?>
			<div class="wc_coupon_message_wrap" style="padding: 10px 0 10px;">
			<?php $this->print_coupon_message( $applied_coupons ); ?>
			</div>
			<?php

			if ( is_cart() || is_checkout() ) {
				$js = "
						if (typeof sc_coupon_message_ajax === 'undefined') {
							var sc_coupon_message_ajax = null;
						}
						jQuery('body').on('applied_coupon removed_coupon updated_checkout', function(){
							clearTimeout( sc_coupon_message_ajax );
							sc_coupon_message_ajax = setTimeout(function(){
								jQuery.ajax({
									url: '" . admin_url( 'admin-ajax.php' ) . "',
									type: 'POST',
									dataType: 'html',
									data: {
										action: 'get_wc_coupon_message',
										security: '" . wp_create_nonce( 'wc_coupon_message' ) . "'
									},
									success: function( response ) {
										jQuery('.wc_coupon_message_wrap').html('');
										if ( response != undefined && response != '' ) {
											jQuery('.wc_coupon_message_wrap').html( response );										
										}
									}
								});
							}, 200);
						});

						";
				wc_enqueue_js( $js );
			}

		}

		/**
		 * Function to get coupon messages via ajax
		 */
		public function get_wc_coupon_message() {

			check_ajax_referer( 'wc_coupon_message', 'security' );

			$applied_coupons = WC()->cart->get_applied_coupons();

			$this->print_coupon_message( $applied_coupons );

			die();
		}

		/**
		 * Function to add coupon's message in email
		 *
		 * @since 1.0
		 *
		 * @param  WC_Order $order Order's object.
		 * @param  boolean  $bool Not used in this function.
		 * @param  boolean  $plain_text Not used in this function.
		 */
		public function wc_add_coupons_message_in_email( $order = null, $bool = false, $plain_text = false ) {
			$used_coupons = $this->get_coupon_codes( $order );
			if ( count( $used_coupons ) <= 0 ) {
				return;
			}
			$show_coupon_message_title = false;
			$coupon_messages           = '';
			foreach ( $used_coupons as $coupon_code ) {
				$coupon = new WC_Coupon( $coupon_code );
				if ( $this->is_wc_gte_30() ) {
					$coupon_id = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
				} else {
					$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				}
				$coupon_message   = get_post_meta( $coupon_id, 'wc_coupon_message', true );
				$include_in_email = get_post_meta( $coupon_id, 'wc_email_message', true );
				if ( ! empty( $coupon_message ) && 'yes' === $include_in_email ) {
					$is_filter_content = apply_filters(
						'wc_sc_is_filter_content_coupon_message',
						true,
						array(
							'source'        => $this,
							'called_by'     => current_filter(),
							'coupon_object' => $coupon,
							'order_object'  => $order,
						)
					);
					if ( true === $is_filter_content ) {
						$coupon_messages .= apply_filters( 'the_content', $coupon_message );
					} else {
						$coupon_messages .= $coupon_message;
					}
					$show_coupon_message_title = true;
				}
			}
			if ( $show_coupon_message_title ) {
				?>
				<h2><?php echo esc_html__( 'Coupon Message', 'woocommerce-smart-coupons' ); ?></h2>
				<?php
				echo '<div class="wc_coupon_message_wrap" style="padding: 10px 0 10px;">';
				echo wp_kses_post( $coupon_messages ); // phpcs:ignore
				echo '</div>';
			}
		}

		/**
		 * Add meta in export headers
		 *
		 * @param  array $headers Existing headers.
		 * @return array
		 */
		public function export_headers( $headers = array() ) {

			$cm_headers = array(
				'wc_coupon_message' => __( 'Coupon Message', 'woocommerce-smart-coupons' ),
				'wc_email_message'  => __( 'Is Email Coupon Message', 'woocommerce-smart-coupons' ),
			);

			return array_merge( $headers, $cm_headers );

		}

		/**
		 * Post meta defaults for CM's meta
		 *
		 * @param  array $defaults Existing postmeta defaults.
		 * @return array
		 */
		public function postmeta_defaults( $defaults = array() ) {

			$cm_defaults = array(
				'wc_coupon_message' => '',
				'wc_email_message'  => '',
			);

			return array_merge( $defaults, $cm_defaults );
		}

		/**
		 * Add CM's meta with value in coupon meta
		 *
		 * @param  array $data The row data.
		 * @param  array $post The POST values.
		 * @return array Modified data
		 */
		public function generate_coupon_meta( $data = array(), $post = array() ) {

			$data['wc_coupon_message'] = ( ! empty( $post['wc_coupon_message'] ) ) ? wp_kses_post( $post['wc_coupon_message'] ) : '';
			$data['wc_email_message']  = ( ! empty( $post['wc_email_message'] ) ) ? wc_clean( wp_unslash( $post['wc_email_message'] ) ) : 'no';

			return $data;
		}

		/**
		 * Make meta data of SC CM, protected
		 *
		 * @param bool   $protected Is protected.
		 * @param string $meta_key The meta key.
		 * @param string $meta_type The meta type.
		 * @return bool $protected
		 */
		public function make_action_meta_protected( $protected, $meta_key, $meta_type ) {
			$sc_meta = array(
				'wc_coupon_message' => '',
				'wc_email_message'  => '',
			);
			if ( in_array( $meta_key, $sc_meta, true ) ) {
				return true;
			}
			return $protected;
		}

		/**
		 * Function to copy CM meta in newly generated coupon
		 *
		 * @param  array $args The arguments.
		 */
		public function copy_coupon_action_meta( $args = array() ) {

			$new_coupon_id = ( ! empty( $args['new_coupon_id'] ) ) ? absint( $args['new_coupon_id'] ) : 0;
			$coupon        = ( ! empty( $args['ref_coupon'] ) ) ? $args['ref_coupon'] : false;

			if ( empty( $new_coupon_id ) || empty( $coupon ) ) {
				return;
			}

			if ( $this->is_wc_gte_30() ) {
				$coupon_message = $coupon->get_meta( 'wc_coupon_message' );
				$email_message  = $coupon->get_meta( 'wc_email_message' );
			} else {
				$old_coupon_id  = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				$coupon_message = get_post_meta( $old_coupon_id, 'wc_coupon_message', true );
				$email_message  = get_post_meta( $old_coupon_id, 'wc_email_message', true );
			}
			update_post_meta( $new_coupon_id, 'wc_coupon_message', wp_filter_post_kses( $coupon_message ) );
			update_post_meta( $new_coupon_id, 'wc_email_message', $email_message );

		}

	}

}

WC_SC_Coupon_Message::get_instance();
