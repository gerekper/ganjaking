<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * @version 1.3.4
 */

if ( ! defined( 'YITH_WOCC' ) ) {
	exit;
}
?>

<form method="post">

	<?php
	$title = ( $action == 'add' ) ? __( 'Add address', 'yith-woocommerce-one-click-checkout' ) : esc_html__( 'Edit address', 'yith-woocommerce-one-click-checkout' );
	echo '<h3>' . esc_html( $title ) . '</h3>';

	if ( $address && ! empty( $address ) ):

		foreach ( $address as $key => $field ) :
			woocommerce_form_field( $key, $field, ! empty( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : $field['value'] );
		endforeach;

	endif;
	?>

    <p>
		<?php if ( isset( $_GET['edit'] ) ) : ?>
            <input type="hidden" name="address_edit" value="<?php echo esc_attr( $_GET['edit'] ); ?>">
		<?php endif; ?>
        <input type="submit" class="button" name="save_address" value="<?php echo esc_attr( esc_html__( 'Save Address', 'yith-woocommerce-one-click-checkout' ) ); ?>" />
		<?php wp_nonce_field( $action_form ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <input type="hidden" name="_action_form" value="<?php echo esc_html( $action_form ); ?>" />
    </p>

</form>