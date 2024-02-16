<?php
/**
 * Pay Gold Page
 *
 * @package WooCommerce Redsys Gateway
 * @since 16.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Pay Gold Page
 */
function paygold_page() {
	?>
	<div class="wrap">
		<h1>
			<?php
			esc_html_e( 'Pay Gold Tools', 'woocommerce-redsys' );
			?>
		</h1>
		<h2 class="nav-tab-wrapper wp-clearfix">
			<a href="admin.php?page=paygold-page&tab=request-token" class="nav-tab nav-tab-active"><?php esc_html_e( 'Request a token', 'woocommerce-redsys' ); ?></a>
		</h2>
		<?php
		if ( ! empty( $_POST ) && check_admin_referer( 'request_token_action', 'request_token_nonce_field' ) ) {

			/**
			 * Capturamos todo de $data.
			 ******** data **********
			 * $data['user_id'];
			 * $data['token_type'];
			 * $data['send_type'];
			 * $POST['send_to'];
			 * $data['description'];.
			 */

			if ( isset( $_POST['description'] ) ) {
				$description = sanitize_text_field( wp_unslash( $_POST['description'] ) );
			} else {
				$description = '';
			}

			if ( ! isset( $_POST['woo_search_users_paygold_label_field'] ) || ! isset( $_POST['token_type'] ) || ! isset( $_POST['send_type'] ) || ! isset( $_POST['send_to'] ) ) {
				echo '<p>' . esc_html__( 'You must fill all the fields', 'woocommerce-redsys' ) . '</p>';
				return;
			}

			$log        = new WC_Logger();
			$data       = array();
			$user_id    = sanitize_text_field( wp_unslash( $_POST['woo_search_users_paygold_label_field'] ) ); // User ID.
			$token_type = sanitize_text_field( wp_unslash( $_POST['token_type'] ) ); // R or C.
			$send_type  = sanitize_text_field( wp_unslash( $_POST['send_type'] ) ); // email or sms.
			$send_to    = sanitize_text_field( wp_unslash( $_POST['send_to'] ) ); // email or SMS number.
			$data       = array(
				'user_id'     => $user_id,
				'token_type'  => $token_type,
				'send_type'   => $send_type,
				'send_to'     => $send_to,
				'description' => $description,
			);
			if ( 'yes' === WCRed()->get_redsys_option( 'debug', 'paygold' ) ) {
				$log->add( 'paygold', '$data: ' . print_r( $data, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}

			$result = WCRed()->send_paygold_link( false, $data );
			echo '<p>' . esc_html__( 'The link is: ', 'woocommerce-redsys' ) . esc_url( $result ) . '</p>';
			echo '<p>' . esc_html__( 'The link is not saved, so if you want it, copy it', 'woocommerce-redsys' ) . '</p>';

		} else {
			?>
			<form method="post" name="paygold_form">
				<?php wp_nonce_field( 'request_token_action', 'request_token_nonce_field' ); ?>
				<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="woo_search_users_paygold"><?php esc_html_e( 'User', 'woocommerce-redsys' ); ?></label>
						</th>
						<td>
							<select id="woo_search_users_paygold" name="woo_search_users_paygold_label_field[]" class="js-woo-search-users-pay-gold" style="width:50%;">
							<?php
							if ( ! empty( $user_selected['0'] ) ) {
								foreach ( $user_selected['0'] as $user_id ) {
									$user_data  = get_userdata( $user_id );
									$user_email = $user_data->user_email;
									?>
									<option value="<?php echo esc_attr( $user_id ); ?>" selected="selected"><?php echo esc_html( $user_email ); ?></option>
									<?php
								}
							}
							?>
							</select>
					</tr>
					<tr>
						<th scope="row">
							<label for="send_type"><?php esc_html_e( 'Email or SMS', 'woocommerce-redsys' ); ?></label>
						</th>
						<td>
							<select name="send_type" id="send_type">
								<option value="email" lang="af"><?php esc_html_e( 'Send Email', 'woocommerce-redsys' ); ?></option>
								<option value="sms" lang="ar"><?php esc_html_e( 'Send SMS', 'woocommerce-redsys' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="send_to"><?php esc_html_e( 'Send To (type mail or SMS number)', 'woocommerce-redsys' ); ?></label>
						</th>
						<td>
							<input name="send_to" type="text" id="send_to" value="" class="regular-text"></td>
					</tr>
					<tr>
						<th scope="row">
							<label for="token_type"><?php esc_html_e( 'Token Type', 'woocommerce-redsys' ); ?></label>
						</th>
						<td>
							<select name="token_type" id="token_type">
								<option value="R" lang="af"><?php esc_html_e( 'Subscription', 'woocommerce-redsys' ); ?></option>
								<option value="C" lang="ar"><?php esc_html_e( 'Pay with 1Click', 'woocommerce-redsys' ); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Send Link', 'woocommerce-redsys' ); ?>"></p>
			</form>
	<?php } ?>
	</div>
	<?php
}
