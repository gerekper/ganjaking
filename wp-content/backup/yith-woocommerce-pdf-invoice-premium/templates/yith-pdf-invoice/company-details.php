<?php
/**
 * The Template for invoice
 *
 * Override this template by copying it to [your theme]/woocommerce/invoice/ywpi-invoice-template.php
 *
 * @author        Yithemes
 * @package       yith-woocommerce-pdf-invoice-premium/Templates
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>

<div class="company-header">
	<div class="ywpi-company-details">
		<div class="ywpi-company-content">
			<?php if ( isset( $company_name ) ): ?>
				<div class="company-name">
					<?php echo $company_name; ?>
				</div>
			<?php endif; ?>

			<?php if ( isset ( $company_details ) ): ?>
				<div>
					<span class="company-details"><?php echo $company_details; ?></span>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $company_logo_path ) : ?>
		<div class="company-logo-section">
			<img class="ywpi-company-logo" src="<?php echo apply_filters('yith_ywpi_company_image_path', $company_logo_path); ?>">
		</div>

	<?php endif; ?>

</div>

<div style="clear: both"></div>