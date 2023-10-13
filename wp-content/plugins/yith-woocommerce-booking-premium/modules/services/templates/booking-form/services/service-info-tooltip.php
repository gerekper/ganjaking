<?php
/**
 * Service description - tooltip
 *
 * @var WC_Product_Booking $product          The booking product.
 * @var YITH_WCBK_Service  $service          The service.
 * @var bool               $show_description Show description flag.
 * @var bool               $show_price       Show price flag.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Services\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$description_html = $service->get_description_html();
$pricing_html     = $service->get_pricing_html( $product );
$allowed_tags     = array(
	'br' => array(),
);
?>

<?php if ( $show_description && $description_html ) : ?>
	<div class='yith-wcbk-booking-service__description'>
		<?php echo wp_kses( $description_html, $allowed_tags ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
<?php endif; ?>

<?php if ( $show_price && $pricing_html ) : ?>
	<div class='yith-wcbk-booking-service__pricing'>
		<?php echo wp_kses( $pricing_html, $allowed_tags ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
<?php endif; ?>
