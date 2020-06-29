<?php
/**
 * View template to listing labels from AJAX response.
 *
 * @package WC_Stamps_Integration/View
 */

?>

<table class="widefat labels">
	<?php
	foreach ( $labels as $label ) {
		if ( $label->is_valid() ) {
			?>
			<tr>
				<td width="64"><?php include( 'html-label.php' ); ?></td>
				<td>
					<?php
					$tracking = $label->get_tracking_number();
					if ( $tracking ) {
						echo '<a href="https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1=' . esc_attr( $tracking ) . '" title="' . esc_attr( $tracking ) . '">' . esc_html( $label->get_tracking_number_excerpt() ) . '</a>' . '<br/>';
					}

					$date = $label->get_value( 'ShipDate' );
					if ( $date ) {
						echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) . '<br/>';
					}
					?>
					<div class="label-actions">
						<a href="#"
							class="stamps-action cancel-label"
							data-stamps_action="cancel_label"
							data-id="<?php echo esc_attr( $label->get_id() ); ?>"
							data-confirm="<?php echo esc_attr( __( 'Are you sure you want to cancel this label? This action cannot be undone.', 'woocommerce-shipping-stamps' ) ) ?>">
							<?php _e( 'Refund', 'woocommerce-shipping-stamps' ) ?>
						</a> |
						<a href="#"
							class="stamps-action delete-label"
							data-stamps_action="delete_label"
							data-id="<?php echo esc_attr( $label->get_id() ); ?>"
							data-confirm="<?php echo esc_attr( __( 'Are you sure you want to delete this label? This action cannot be undone.', 'woocommerce-shipping-stamps' ) ) ?>">
							<?php _e( 'Delete', 'woocommerce-shipping-stamps' ) ?>
						</a> |
						<a
							href="#"
							class="copy-tracking-number"
							title="<?php esc_attr_e( 'Copy tracking number to clipboard.', 'woocommerce-shipping-stamps' ); ?>"
							data-clipboard-success="<?php esc_attr_e( 'Copied!', 'woocommerce-shipping-stamps' ); ?>"
							data-clipboard-text="<?php echo esc_attr( $tracking ); ?>">
							<?php _e( 'Copy', 'woocommerce-shipping-stamps' ); ?>
						</a>
					</div>
				</td>
			</tr>
			<?php
		}
	}
	?>
</table>
<p><button type="submit" class="button stamps-action" data-stamps_action="define_package"><?php _e( 'Request another label', 'woocommerce-shipping-stamps' ); ?></button></p>
