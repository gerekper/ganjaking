<?php
/**
 * Document title template.
 *
 * Override this template by copying it to [your theme]/woocommerce/invoice/ywpi-invoice-details.php
 *
 * @author  YITH
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( $document_title ) : ?>
	<div class="ywpi-document-title">
		<?php echo esc_html( $document_title ); ?>
	</div>
	<?php
endif;
