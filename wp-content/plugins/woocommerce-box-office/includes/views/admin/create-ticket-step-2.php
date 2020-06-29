<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$data    = $this->_clean_data;
$product = $this->_current_product;
?>
<div class="wrap woocommerce">
	<h2><?php _e( 'Create Ticket', 'woocommerce-box-office' ); ?></h2>

	<?php $this->maybe_print_errors(); ?>

	<form method="POST">
		<table class="form-table">
			<tbody>
				<?php if ( $product->is_type( 'variable' ) ) : ?>
					<?php foreach ( $product->get_variation_attributes() as $attribute_name => $options ) : ?>
					<tr valign="top">
						<th scope="row">
							<label for="<?php echo sanitize_title( $attribute_name ); ?>"><?php echo wc_attribute_label( $attribute_name ); ?></label>
						</th>
						<td>
							<?php
								$selected = isset( $data[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( $data[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $product->get_variation_default_attribute( $attribute_name );
								wc_dropdown_variation_attribute_options(
									array(
										'options'   => $options,
										'attribute' => $attribute_name,
										'product'   => $product,
										'selected'  => $selected,
									)
								);
							?>
						</td>
					</tr>
					<?php endforeach; ?>
				<?php endif; ?>

				<tr valign="top">
					<th scope="row">
						<label><?php _e( 'Tickets', 'woocommerce-box-office' ); ?></label>
					</th>
					<td>
						<?php if ( $ticket_form ) : ?>
						<div class="wc-box-office-ticket-form">
							<div class="wc-box-office-ticket-fields" data-index="0" style="display: none">
								<h3 class="wc-box-office-ticket-fields-title">
									<a href="#"><?php _e( 'Ticket #1', 'woocommerce-box-office' ); ?></a>
								</h3>
								<div class="wc-box-office-ticket-fields-body">
									<?php

									$ticket_form->render( array(
										'field_name_prefix' => 'ticket_fields[0]',
										'multiple_tickets'  => true,
									) );
									?>
								</div>
							</div>
						</div>
						<?php endif; ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">&nbsp;</th>
					<td>
						<p style="margin-bottom: 10px">
							<label>
								<input type="checkbox" name="send_confirmation_email" value="1">
								<?php _e( 'Send confirmation email to each ticket?', 'woocommerce-box-office' ); ?>
								<?php echo wc_help_tip( __( 'The content in the email is based on the content of Ticket Emails panel found in selected ticket product.', 'woocommerce-box-office' ) ); ?>
							</label>
						</p>

						<input type="submit" name="submit_create_ticket" class="button-primary" value="<?php _e( 'Create Ticket', 'woocommerce-box-office' ); ?>" />
						<input type="hidden" name="customer_id" value="<?php echo esc_attr( $data['customer_id'] ); ?>" />
						<input type="hidden" name="product_id" value="<?php echo esc_attr( $data['product_id'] ); ?>" />
						<input type="hidden" name="quantity" value="<?php echo esc_attr( $data['quantity'] ); ?>" />
						<input type="hidden" name="create_order_method" value="<?php echo esc_attr( $data['create_order_method'] ); ?>" />
						<input type="hidden" name="ticket_order_id" value="<?php echo esc_attr( $data['ticket_order_id'] ); ?>" />

						<input type="hidden" name="create_ticket_step" value="2" />
						<?php wp_nonce_field( 'create_event_ticket' ); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
