<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$is_admin = is_admin_bar_showing() ? ' ywpc-admin' : '';
$position = get_option( 'ywpc_topbar_position', 'top' );
$date     = ywpc_get_countdown( $args['end_date'] );
$variable = ( $args['type'] == 'variable' ) ? $args['url'] : '';

?>
<div class="ywpc-topbar<?php echo $is_admin; ?> ywpc-<?php echo $position; ?>">
	<a href="<?php echo get_permalink( $args['id'] ) . $variable; ?>" class="ywpc-countdown-topbar">
		<div class="ywpc-header">
			<?php echo get_option( 'ywpc_topbar_timer_title', esc_html__( 'Click here for a special offer!', 'yith-woocommerce-product-countdown' ) ); ?>
		</div>
		<div class="ywpc-timer">
			<div class="ywpc-days">
				<div class="ywpc-amount">
					<?php $days = ( ( is_rtl() ) ? strrev( $date['dd'] ) : $date['dd'] ); ?>
					<span class="ywpc-char-0"><?php echo substr( $days, 0, 1 ); ?></span>
					<span class="ywpc-char-1"><?php echo substr( $days, 1, 1 ); ?></span>
					<span class="ywpc-char-2"><?php echo substr( $days, 2, 1 ); ?></span>
				</div>
				<div class="ywpc-label">
					<?php esc_html_e( 'Days', 'yith-woocommerce-product-countdown' ) ?>
				</div>
			</div>
			<div class="ywpc-hours">
				<div class="ywpc-amount">
					<?php $hours = ( ( is_rtl() ) ? strrev( $date['hh'] ) : $date['hh'] ); ?>
					<span class="ywpc-char-1"><?php echo substr( $hours, 0, 1 ); ?></span>
					<span class="ywpc-char-2"><?php echo substr( $hours, 1, 1 ); ?></span>
				</div>
				<div class="ywpc-label">
					<?php esc_html_e( 'Hours', 'yith-woocommerce-product-countdown' ) ?>
				</div>
			</div>
			<div class="ywpc-minutes">
				<div class="ywpc-amount">
					<?php $minutes = ( ( is_rtl() ) ? strrev( $date['mm'] ) : $date['mm'] ); ?>
					<span class="ywpc-char-1"><?php echo substr( $minutes, 0, 1 ); ?></span>
					<span class="ywpc-char-2"><?php echo substr( $minutes, 1, 1 ); ?></span>
				</div>
				<div class="ywpc-label">
					<?php esc_html_e( 'Minutes', 'yith-woocommerce-product-countdown' ) ?>
				</div>
			</div>
			<div class="ywpc-seconds">
				<div class="ywpc-amount">
					<?php $seconds = ( ( is_rtl() ) ? strrev( $date['ss'] ) : $date['ss'] ); ?>
					<span class="ywpc-char-1"><?php echo substr( $seconds, 0, 1 ); ?></span>
					<span class="ywpc-char-2"><?php echo substr( $seconds, 1, 1 ); ?></span>
				</div>
				<div class="ywpc-label">
					<?php esc_html_e( 'Seconds', 'yith-woocommerce-product-countdown' ) ?>
				</div>
			</div>
		</div>
		<input type="hidden" value="<?php echo( date( 'Y', $date['to'] ) ) ?>.<?php echo( date( 'm', $date['to'] ) - 1 ) ?>.<?php echo( date( 'd', $date['to'] ) ) ?>.<?php echo( date( 'H', $date['to'] ) ) ?>.<?php echo( date( 'i', $date['to'] ) ) ?>">
	</a>
</div>
