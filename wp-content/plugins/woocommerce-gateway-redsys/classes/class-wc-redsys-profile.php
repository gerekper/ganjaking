<?php
/**
 * Add extra profile fields for users in admin
 *
 * @package  WooCommerce/Admin
 * @version  2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

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
		}
		public function add_tokens_fields( $user ) {
			if ( ! apply_filters( 'woocommerce_current_user_can_edit_customer_meta_fields', current_user_can( 'manage_woocommerce' ), $user->ID ) ) {
				return;
			} ?>
			<h2><?php _e( 'Type', 'woocommerce-redsys' ); ?></h2>
			<table class="form-table" id="fieldset-redsys-tokens">
				<tbody>
					<tr>
						<th>
							<label for="toekens"><?php _e( '1click Tokens', 'woocommerce-redsys' ); ?></label>
						</th>
						<td>
							<textarea name="toekens" id="toekens" rows="10" cols="60" readonly><?php WCRed()->get_all_tokens( $user->ID, 'C' ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th>
							<label for="toekenr"><?php _e( 'Subscriptions Tokens', 'woocommerce-redsys' ); ?></label>
						</th>
						<td>
							<textarea name="toekenr" id="toekenr" rows="10" cols="60" readonly><?php WCRed()->get_all_tokens( $user->ID, 'R' ); ?></textarea>
							<p class="description"></p>
						</td>
					</tr>
				</tbody>
			</table>
			<?php
		}
	}

endif;

return new WC_Redsys_Profile();
