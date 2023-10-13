<?php
/**
 * Service description - inline
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
$pricing          = $service->get_pricing( $product );
?>

<?php if ( count( $pricing ) !== 1 ) : ?>
	<?php if ( $show_description && $description_html ) : ?>
		<div class='yith-wcbk-booking-service__description'>
			<?php echo $description_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endif; ?>

	<?php if ( $show_price && $pricing ) : ?>
		<div class='yith-wcbk-booking-service__pricing yith-wcbk-booking-service__pricing--block'>
			<?php
			$pricing_display = wp_list_pluck( $pricing, 'display' );

			echo sprintf(
				'<strong>%s</strong> %s',
				esc_html__( 'Prices:', 'yith-booking-for-woocommerce' ),
				wp_kses_post( implode( ', ', $pricing_display ) )
			);
			?>
		</div>
	<?php endif; ?>
<?php else : ?>
	<?php if ( $show_price && $pricing ) : ?>
		<?php
		$current_pricing = current( $pricing );
		$separator       = $current_pricing['price'] > 0 ? '+ ' : '- ';
		?>
		<span class='yith-wcbk-booking-service__pricing yith-wcbk-booking-service__pricing--inline'>
			<?php echo esc_html( $separator ) . wp_kses_post( current( $pricing )['display'] ); ?>
		</span>
	<?php endif; ?>

	<?php if ( $show_description && $description_html ) : ?>
		<div class='yith-wcbk-booking-service__description'>
			<?php echo $description_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
