<?php
/**
 * Sales report email
 *
 * @author        WooThemes
 * @version       1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

	<p><?php printf( __( "Hi there. Please find your %s sales report below.", 'woocommerce-sales-report-email' ), $interval ); ?></p>

	<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
		<tbody>
		<?php
		foreach ( $rows as $row ) {
			?>
			<tr>
				<td style="font-weight: bold;width: 30%"><?php echo $row->get_label(); ?></td>
				<td><?php echo $row->get_value(); ?></td>
			</tr>
		<?php
		}
		?>

		</tbody>
	</table>

<?php do_action( 'woocommerce_email_footer' ); ?>