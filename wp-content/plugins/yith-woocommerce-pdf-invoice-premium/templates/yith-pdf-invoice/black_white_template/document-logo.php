<?php
/**
 * Company logo template.
 *
 * @author  YITH
 * @package YITH\PDFInvoice\Templates
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

?>
<?php if ( $company_logo_path ) : ?>
	<div class="company-logo-section">
		<img class="ywpi-company-logo" src="<?php echo wp_kses_post( apply_filters( 'yith_ywpi_company_image_path', $company_logo_path ) ); ?>">
	</div>
<?php endif; ?>
