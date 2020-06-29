<?php
/**
 * Template for product enabled-ticket that renders ticket fields in add to cart.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

?>

<noscript><?php _e( 'Your browser must support JavaScript in order to purchase ticket(s).', 'woocommerce-box-office' ); ?></noscript>

<div class="wc-box-office-ticket-form">

	<?php do_action( 'woocommerce_before_ticket_fields' ); ?>

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
