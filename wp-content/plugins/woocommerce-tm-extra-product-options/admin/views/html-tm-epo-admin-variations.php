<?php
/**
 * View for displaying the variation select box
 *
 * Variables used:
 * @required   $variations
 * @required   $loop
 *
 * @package Extra Product Options/Admin/Views
 * @version 4.8.5
 */

defined( 'ABSPATH' ) || exit;

if ( isset( $variations ) && isset( $loop ) ) {
	$tmcp_attribute_selected_value = isset( $tmcp_data['tmcp_attribute'][0] ) ? $tmcp_data['tmcp_attribute'][0] : '';
	$tmcp_type_selected_value      = isset( $tmcp_data['tmcp_type'][0] ) ? $tmcp_data['tmcp_type'][0] : '';

	?>
    <select class="tmcp-variation" name="tmcp_variation[<?php echo esc_attr( $loop ); ?>]">
        <option value="0"><?php esc_html_e( 'Any', 'woocommerce-tm-extra-product-options' ); ?> &hellip;</option>
		<?php
		$_variations = (array) $variations;
		foreach ( $_variations as $_variation ) {
			$_variation = (array) $_variation;
			?>
            <option value="<?php echo esc_attr( sanitize_title( $_variation['ID'] ) ); ?>"><?php echo esc_html( $_variation['ID'] ); ?></option>
			<?php
		}
		?>
    </select>
	<?php
	unset( $_variations );
}