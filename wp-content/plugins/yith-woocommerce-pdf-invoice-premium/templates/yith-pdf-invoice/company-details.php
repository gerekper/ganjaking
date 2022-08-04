<?php
/**
 * Company details template.
 *
 * @author  YITH
 * @package YITH\PDFInvoice\Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! $document instanceof YITH_Credit_Note ) {
	if ( 'yith_wcmv_vendor_suborder' === $document->order->get_created_via() ) {
		$order     = wc_get_order( $document->order->get_id() ); // phpcs:ignore
		$vendor_id = $order->get_meta( 'vendor_id' );

		$company_name_option      = 'ywpi_company_name_' . $vendor_id;
		$company_details_option   = 'ywpi_company_details_' . $vendor_id;
		$company_logo_path_option = 'ywpi_company_logo_' . $vendor_id;

		$company_name      = 'yes' === ywpi_get_option( 'ywpi_show_company_name', $document, 'yes' ) ? ywpi_get_option( $company_name_option, $document ) : null;
		$company_details   = 'yes' === ywpi_get_option( 'ywpi_show_company_details', $document, 'yes' ) ? nl2br( ywpi_get_option( $company_details_option, $document ) ) : null;
		$company_logo_path = 'yes' === ywpi_get_option( 'ywpi_show_company_logo', $document, 'yes' ) ? ywpi_get_option( $company_logo_path_option, $document ) : null;
	}
}

?>

<div class="company-header">
	<div class="ywpi-company-details">
		<div class="ywpi-company-content">
			<?php if ( isset( $company_name ) ) : ?>
				<div class="company-name">
					<?php echo esc_html( $company_name ); ?>
				</div>
			<?php endif; ?>

			<?php if ( isset( $company_details ) ) : ?>
				<div>
					<span class="company-details"><?php echo wp_kses_post( $company_details ); ?></span>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $company_logo_path ) : ?>
		<div class="company-logo-section">
			<img class="ywpi-company-logo" src="<?php echo wp_kses_post( apply_filters( 'yith_ywpi_company_image_path', $company_logo_path ) ); ?>">
		</div>

	<?php endif; ?>

</div>

<div style="clear: both"></div>
