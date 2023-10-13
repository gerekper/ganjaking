<?php
/**
 * Bookings
 * Shows booking on the account page.
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/bookings.php.
 *
 * @var YITH_WCBK_Booking[] $bookings
 * @var bool                $has_bookings
 * @var int                 $max_num_pages
 * @var int                 $current_page
 * @var int                 $total
 *
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

$bookings_endpoint = apply_filters( 'yith_wcbk_endpoint_bookings', 'bookings' );
$endpoint          = yith_wcbk()->endpoints->get_endpoint( $bookings_endpoint );
?>

<?php
/**
 * DO_ACTION: yith_wcbk_before_account_bookings
 * Hook to output something before the bookings' list in My Account > Bookings.
 *
 * @param bool $has_bookings True if the current user has at least one booking.
 */
do_action( 'yith_wcbk_before_account_bookings', $has_bookings );
?>

<?php if ( $has_bookings ) : ?>

	<?php
	/**
	 * DO_ACTION: yith_wcbk_show_bookings_table
	 * Hook to output the bookings' list table in My Account > Bookings.
	 *
	 * @hooked yith_wcbk_show_bookings_table - 10
	 *
	 * @param YITH_WCBK_Booking[] $bookings The bookings.
	 */
	do_action( 'yith_wcbk_show_bookings_table', $bookings );
	?>

	<?php
	/**
	 * DO_ACTION: yith_wcbk_before_account_bookings_pagination
	 * Hook to output something before the pagination in the bookings' list in My Account > Bookings.
	 */
	do_action( 'yith_wcbk_before_account_bookings_pagination' );
	?>

	<?php if ( 1 < $max_num_pages ) : ?>
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
			<?php
			if ( 1 !== $current_page ) :
				$prev_url = wc_get_endpoint_url( $endpoint, $current_page - 1 );
				?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( $prev_url ); ?>"><?php esc_html_e( 'Previous', 'yith-booking-for-woocommerce' ); ?></a>
			<?php endif; ?>

			<?php
			if ( intval( $max_num_pages ) !== $current_page ) :
				$next_url = wc_get_endpoint_url( $endpoint, $current_page + 1 );
				?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( $next_url ); ?>"><?php esc_html_e( 'Next', 'yith-booking-for-woocommerce' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>
	<div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
		<a class="woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php esc_html_e( 'Go to the Shop', 'yith-booking-for-woocommerce' ); ?>
		</a>
		<?php esc_html_e( 'No booking has been made yet.', 'yith-booking-for-woocommerce' ); ?>
	</div>
<?php endif; ?>

<?php
/**
 * DO_ACTION: yith_wcbk_after_account_bookings
 * Hook to output something after the bookings' list in My Account > Bookings.
 *
 * @param bool $has_bookings True if the current user has at least one booking.
 */
do_action( 'yith_wcbk_after_account_bookings', $has_bookings );
?>
