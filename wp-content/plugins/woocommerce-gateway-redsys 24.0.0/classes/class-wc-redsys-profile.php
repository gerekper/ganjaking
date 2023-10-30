<?php
/**
 * Add extra profile fields for users in admin
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com > https://woocommerce.com/products/redsys-gateway/
 * @since 13.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2023 José Conti.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Admin_Profile', false ) ) :

	/**
	 * WC_Admin_Profile Class.
	 */
	class WC_Redsys_Profile {

		/**
		 * Hook in tabs.
		 */
		public function __construct() {
			add_action( 'show_user_profile', array( $this, 'add_tokens_fields' ) );
			add_action( 'edit_user_profile', array( $this, 'add_tokens_fields' ) );
			add_action( 'admin_head', array( $this, 'check_send_link' ) );
		}
		/**
		 * Add tokens fields to user profile
		 *
		 * @param WP_User $profileuser User object.
		 */
		public function add_tokens_fields( $profileuser ) {
			if ( ! apply_filters( 'woocommerce_current_user_can_edit_customer_meta_fields', current_user_can( 'manage_woocommerce' ), $profileuser->ID ) ) {
				return;
			}
			if ( is_network_admin() ) {
				return;
			}
			$nonce     = wp_create_nonce( 'redsys-nonce-token' );
			$url_admin = get_admin_url();
			$user_id   = $profileuser->ID;
			$full_url  = $url_admin . 'user-edit.php?user_id=' . $user_id . '&nonce=' . $nonce;
			?>
			<h2><?php esc_html_e( 'Type', 'woocommerce-redsys' ); ?></h2>
			<table class="form-table" id="fieldset-redsys-tokens">
				<tbody>
					<tr>
						<th>
							<label for="toekens"><?php esc_html_e( '1click Tokens', 'woocommerce-redsys' ); ?></label>
						</th>
						<td>
							<textarea name="toekens" id="toekens" rows="10" cols="60" readonly><?php WCRed()->get_all_tokens( $user_id, 'C' ); ?></textarea>
							<p class="submit">
								<a href="<?php echo esc_url( $full_url ) . '&token=s'; ?>" class="button-primary" target="_self"><?php esc_html_e( 'Request 1click Token', 'woocommerce-redsys' ); ?></a>
							</p>
						</td>
					</tr>
					<tr>
						<th>
							<label for="toekenr"><?php esc_html_e( 'Subscriptions Tokens', 'woocommerce-redsys' ); ?></label>
						</th>
						<td>
							<textarea name="toekenr" id="toekenr" rows="10" cols="60" readonly><?php WCRed()->get_all_tokens( $user_id, 'R' ); ?></textarea>
							<p class="submit">
								<a href="<?php echo esc_url( $full_url ) . '&token=r'; ?>" class="button-primary" target="_self"><?php esc_html_e( 'Request Subscription Token', 'woocommerce-redsys' ); ?></a>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
			<?php
		}
		/**
		 * Create Hash
		 */
		public function create_hash() {
			$length            = 30;
			$characters        = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$characters_length = strlen( $characters );
			$random_string     = '';
			for ( $i = 0; $i < $length; $i++ ) {
				$random_string .= $characters[ wp_rand( 0, $characters_length - 1 ) ];
			}
			return $random_string;
		}
		/**
		 * Create link to send to user.
		 *
		 * @param int    $user user id.
		 * @param string $toke_type token type.
		 */
		public function create_link_user( $user, $toke_type ) {

			$hash = $this->create_hash();
			set_transient( 'hash_' . $user, $hash, DAY_IN_SECONDS * 2 );
			$pay_url = home_url() . '/redsys-add-card';
			return add_query_arg(
				array(
					'redsys-user-id' => $user,
					'token-type'     => $toke_type,
					'hash'           => $hash,
				),
				$pay_url
			);
		}
		/**
		 * Send email to user.
		 */
		public function notice_send_email() {

			$url = get_transient( 'redsys_url_token' );
			delete_transient( 'redsys_url_token' );
			?>
			<div class="updated notice">
				<p><?php echo esc_html__( 'The email requesting the token has been sent! Link: ', 'woocommerce-redsys' ) . esc_url( $url ); ?></p>
			</div>
			<?php
		}
		/**
		 * Check if send link.
		 */
		public function check_send_link() {
			$screen = get_current_screen();
			if ( 'profile' === $screen->id || 'user-edit' === $screen->id ) {
				if ( ! empty( $_GET['nonce'] ) && ! empty( $_GET['token'] ) && ! empty( $_GET['user_id'] ) ) {
					$nonce = sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
					if ( wp_verify_nonce( $nonce, 'redsys-nonce-token' ) ) {
						$toke_type  = sanitize_text_field( wp_unslash( $_REQUEST['token'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
						$user       = sanitize_text_field( wp_unslash( $_REQUEST['user_id'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
						$user_data  = get_userdata( (int) $user );
						$customer   = new WC_Customer( $user );
						$email      = $user_data->user_email;
						$name       = $customer->get_billing_first_name();
						$last_name  = $customer->get_billing_last_name();
						$site_title = get_bloginfo( 'name' );
						$user_link  = $this->create_link_user( $user, $toke_type );
						$subject    = __( 'Add your credit cart to ', 'woocommerce-redsys' ) . $site_title;
						$body       = '<p>' . __( 'Hi ', 'woocommerce-redsys' ) . $name . ' ' . $last_name . ',</p>
						<p>' . __( 'This is an email from ', 'woocommerce-redsys' ) . $site_title . '</p>
						<p>' . __( 'We need your credit card, please follow the link below: ', 'woocommerce-redsys' ) . '</p>
						<p><a href="' . $user_link . '">' . $user_link . '</a></p>';
						$headers    = array( 'Content-Type: text/html; charset=UTF-8' );
						$data       = array(
							'token_type' => $toke_type,
							'user_id'    => $user,
							'email'      => $email,
							'name'       => $name,
							'last_name'  => $last_name,
							'site_title' => $site_title,
							'user_link'  => $user_link,
							'subject'    => $subject,
							'body'       => $body,
							'header'     => $headers,
						);
						$data       = apply_filters( 'redsys_mail_add_token', $data );
						$email      = $data['email'];
						$subject    = $data['subject'];
						$body       = $data['body'];
						$headers    = $data['header'];
						set_transient( 'redsys_url_token', $user_link, 300 );
						wp_mail( $email, $subject, $body, $headers );
						add_action( 'admin_notices', array( $this, 'notice_send_email' ) );
					}
				}
			}
		}
		/**
		 * Handle new method.
		 *
		 * @param array $wp data from wp.
		 */
		public static function redsys_handle_requests_add_method( $wp ) {
			global $woocommerce;

			// Nonce is not necesary because we are using a transient to check the hash.
			if ( isset( $_GET['redsys-user-id'] ) && isset( $_GET['token-type'] ) && isset( $_GET['hash'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				ob_start();
				$user       = sanitize_key( wp_unslash( $_GET['redsys-user-id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$token_type = sanitize_key( wp_unslash( $_GET['token-type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$hash       = sanitize_text_field( wp_unslash( $_GET['hash'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$transient  = get_transient( 'hash_' . $user );
				if ( $transient !== $hash ) {
					wp_die( esc_html__( 'Ask to administrator for new link', 'woocommerce-redsys' ) );
				}
				if ( 'r' === $token_type ) {
					$token_type = 'tokenr';
				} else {
					$token_type = 'tokens';
				}
				$number = WCRed()->create_add_payment_method_number();
				set_transient( $number, $user, DAY_IN_SECONDS * 2 );
				set_transient( $number . '_get_method', 'yes', DAY_IN_SECONDS * 2 );
				set_transient( $number . '_token_type', $token_type, DAY_IN_SECONDS * 2 );
				set_transient( $number, $user, DAY_IN_SECONDS * 2 );
				$redsys_adr  = WC_Gateway_Redsys::get_redsys_url_gateway_p( $user );
				$redsys_args = WC_Gateway_Redsys::get_redsys_args_add_method( $number );
				$form_inputs = array();
				foreach ( $redsys_args as $key => $value ) {
					$form_inputs[] .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
				}
				echo '<form action="' . esc_url( $redsys_adr ) . '" method="post" id="redsys_payment_form" target="_top">
				' . implode( '', $form_inputs ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				. '
				<input type="submit" class="button-alt" id="submit_redsys_payment_form" value="' . esc_html__( 'Pay with Credit Card via Servired/RedSys', 'woocommerce-redsys' ) . '" />
				<a class="button cancel" href="' . esc_url( wc_get_endpoint_url( 'add-payment-method' ) ) . '">' . esc_html__( 'Cancel order &amp; restore cart', 'woocommerce-redsys' ) . '</a>
				</form>';
				echo '<script>document.getElementById("redsys_payment_form").submit();</script>';
				exit();
			}
		}
	}

endif;

return new WC_Redsys_Profile();
