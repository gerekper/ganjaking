<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Metabox for Subscription Activity
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 *
 * @var array $activities List of activities about the Current subscription.
 * @var bool|string $view_more Flag to show or not the link to view more Activities, if not false it is the link to the activities.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! empty( $activities ) ) : ?>
	<ul class="order_notes">
		<?php
		foreach ( $activities as $activity ) :
			?>
			<li rel="<?php echo esc_attr( $activity->id ); ?>" class="note <?php echo esc_attr( $activity->status ); ?>">
				<div class="note_content">
					<p><?php echo wp_kses_post( stripslashes( $activity->description ) ); ?></p>
				</div>
				<p class="meta">
					<?php // translators: 1: date, 2: time. ?>
					<abbr class="exact-date" title="<?php echo esc_attr( $activity->timestamp_date ); ?>"><?php printf( esc_html_x( 'added on %1$s at %2$s', '1: date, 2: time', 'yith-woocommerce-subscription' ), esc_html( date_i18n( wc_date_format(), strtotime( $activity->timestamp_date ) ) ), esc_html( date_i18n( wc_time_format(), strtotime( $activity->timestamp_date ) ) ) ); ?></abbr>
				</p>
			</li>
		<?php endforeach ?>
	</ul>
	<?php if ( $view_more ) : ?>
		<div class="view-more-activities"><a href="<?php echo esc_url( $view_more ); ?>"><?php esc_html_e( 'View all', 'yith-woocommerce-subscription' ); ?></a></div>
	<?php endif; ?>
<?php endif ?>
