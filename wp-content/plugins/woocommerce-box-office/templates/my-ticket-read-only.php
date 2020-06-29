<?php
/**
 * The Template for displaying read-only ticket via barcode scanning.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/my-ticket-read-only.php.
 *
 * HOWEVER, on occasion WooCommerce Box Office will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @author  WooCommerce
 * @version 1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo $ticket_description;
?>

<p class="buttons">
	<a href="<?php echo esc_url( $edit_ticket_url ); ?>" target="_blank" class="button">
		<?php _e( 'Edit', 'woocommerce-box-office' ); ?>
	</a>

	<?php if ( $print_ticket_enabled ) : ?>
		<a href="<?php echo esc_url( $print_ticket_url ); ?>" target="_blank" class="button">
			<?php _e( 'Print ticket', 'woocommerce-box-office' ); ?>
		</a>
	<?php endif; ?>
</p>
