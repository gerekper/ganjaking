<?php
/**
 * The template for printed ticket.
 *
 * This template can be overridden by copying it to yourtheme/my-ticket-printed.php.
 *
 * HOWEVER, on occasion WooCommerce Box Office will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @author  WooThemes
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * woocommerce_box_office_before_print_ticket hook.
 */
do_action( 'woocommerce_box_office_before_print_ticket' );
?>

<div id="ticket-print-content-container">
	<div id="ticket-print-content">
		<?php echo $printed_content; ?>

		<?php if ( $print_barcode ) : ?>
		<div id="ticket-print-content-barcode">
			<?php WCBO()->components->ticket_barcode->display_ticket_barcode( $ticket_id, array( 'auto_generate' => false ) ); ?>
		</div>
		<?php endif; ?>
	</div>
</div>

<?php
/**
 * woocommerce_box_office_after_edit_ticket hook.
 */
do_action( 'woocommerce_box_office_after_print_ticket' );
