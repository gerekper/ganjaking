<?php
/**
 * Edit address form
 *
 * @version     3.6.0
 */

defined( 'ABSPATH' ) || exit;

$page_title = ( 'billing' === $load_address ) ? esc_html__( 'Billing address', 'woocommerce' ) : esc_html__( 'Shipping address', 'woocommerce' );

if ( version_compare( porto_get_woo_version_number(), '2.5.1', '<' ) ) {
	global $current_user;
	wp_get_current_user();
}

$porto_woo_version = porto_get_woo_version_number();
if ( version_compare( $porto_woo_version, '2.6', '<' ) ) {
	wc_print_notices();
}

do_action( 'woocommerce_before_edit_account_address_form' ); ?>

<?php if ( ! $load_address ) : ?>

	<?php wc_get_template( 'myaccount/my-address.php' ); ?>

<?php else : ?>

	<?php if ( version_compare( $porto_woo_version, '2.6', '<' ) ) : ?>
	<div class="featured-box align-left">
		<div class="box-content">
	<?php endif; ?>

	<form method="post">

		<h3><?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title ); ?></h3><?php // @codingStandardsIgnoreLine ?>
		<div class="woocommerce-address-fields">
			<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>			<div class="woocommerce-address-fields__field-wrapper">
				<?php
				foreach ( $address as $key => $field ) {
					if ( version_compare( $porto_woo_version, '3.6', '<' ) && isset( $field['country_field'], $address[ $field['country_field'] ] ) ) {
						$field['country'] = wc_get_post_data_by_key( $field['country_field'], $address[ $field['country_field'] ]['value'] );
					}
					woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
				}
				?>
			</div>
			<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>
			<p class="clearfix">
				<button type="submit" class="button btn-lg pt-right" name="save_address" value="<?php esc_attr_e( 'Save Address', 'porto' ); ?>"><?php esc_html_e( 'Save Address', 'porto' ); ?></button>
				<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
				<input type="hidden" name="action" value="edit_address" />
			</p>

		</div>
	</form>

	<?php if ( version_compare( $porto_woo_version, '2.6', '<' ) ) : ?>
		</div>
	</div>

	<?php endif; ?>
<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
