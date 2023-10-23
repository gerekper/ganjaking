<?php
/**
 * Document notes template.
 *
 * Override this template by copying it to [your theme]/woocommerce/yith-pdf-invoice/document-notes.php
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( YITH_PDF_Invoice()->is_visible_document_notes( $document ) ) {
	/**
	 * DO_ACTION: yith_ywpi_before_print_document_notes
	 *
	 * Section before display the document notes.
	 */
	do_action( 'yith_ywpi_before_print_document_notes' );

	?>
	<div class="document-notes-section">
	<?php
	/**
	 * APPLY_FILTERS: yith_ywpi_print_document_notes
	 *
	 * Filter the document notes data.
	 *
	 * @param string the document notes.
	 * @param object $document the document object.
	 *
	 * @return string
	 */
	$notes = apply_filters( 'yith_ywpi_print_document_notes', YITH_PDF_Invoice()->get_document_notes( $document ), $document );
	if ( $notes ) {
		?>
		<div class="ywpi-section-notes">
			<span class="notes-title"><?php esc_html_e( 'Notes:', 'yith-woocommerce-pdf-invoice' ); ?></span>
			<div class="notes">
				<span><?php echo wp_kses_post( nl2br( $notes ) ); ?></span>
				<?php
				/**
				 * DO_ACTION: yith_ywpi_after_document_notes
				 *
				 * Section after display the document notes.
				 *
				 * @param object $document the document object.
				 */
				do_action( 'yith_ywpi_after_document_notes', $document );
				?>
			</div>
		</div>
		<?php
	}

	if ( 'yes' === strval( get_option( 'ywpi_show_delivery_info' ) ) ) {
		?>
		<br>
		<div class="delivery">
			<?php
			/**
			 * DO_ACTION: yith_ywpi_delivery_date_label
			 *
			 * Section to display the delivery date label.
			 *
			 * @param object $document the document object.
			 */
			do_action( 'yith_ywpi_delivery_date_label', $document );
			?>
		</div>
		<?php
	}
	?>
	</div>
	<?php
}
