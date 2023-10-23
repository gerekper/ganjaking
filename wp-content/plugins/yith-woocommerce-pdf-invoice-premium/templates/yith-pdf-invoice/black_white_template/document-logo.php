<?php
/**
 * Company logo template.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Templates
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * APPLY_FILTERS: yith_ywpi_company_image_path
 *
 * Filter the path of the company image in the documents.
 *
 * @param string $company_logo_path the company logo path.
 *
 * @return string
 */
if ( $company_logo_path ) : ?>
	<div class="company-logo-section">
		<img class="ywpi-company-logo" src="<?php echo esc_url( apply_filters( 'yith_ywpi_company_image_path', $company_logo_path ) ); ?>">
	</div>
<?php endif; ?>
