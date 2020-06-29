<?php
/**
 * General Stat Admin Panel
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly
?>

<h3><?php echo $page_title ?></h3>

<div class="tablenav top">
	<div class="alignleft">
		<input type="text" name="_from" placeholder="<?php _e( 'From:', 'yith-woocommerce-affiliates' ) ?>" value="<?php echo esc_attr( $from ) ?>" class="date-picker" />
		<input type="text" name="_to" placeholder="<?php _e( 'To:', 'yith-woocommerce-affiliates' ) ?>" value="<?php echo esc_attr( $to ) ?>" class="date-picker" />
		<input type="submit" name="filter_action" id="post-query-submit" class="button" value="<?php _e( 'Filter', 'yith-woocommerce-affiliates' ) ?>" />
		<?php if( $need_reset ): ?>
			<a href="<?php echo $reset_link ?>" class="button"><?php _e( 'Reset', 'yith-woocommerce-affiliates' ) ?></a>
		<?php endif; ?>
	</div>
</div>
<table class="wc_status_table widefat">
	<thead>
	<tr>
		<th colspan="3">
			<?php _e( 'General stats', 'yith-woocommerce-affiliates' ) ?>
		</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td><?php _e( 'Total commissions', 'yith-woocommerce-affiliates' ) ?></td>
		<td class="help">
			<a href="#" class="help_tip" data-tip="<?php _e( 'Sum of all confirmed commissions so far', 'yith-woocommerce-affiliates' ) ?>">[?]</a>
		</td>
		<td><?php echo wc_price( $total_amount ) ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Total Paid', 'yith-woocommerce-affiliates' ) ?></td>
		<td class="help">
			<a href="#" class="help_tip" data-tip="<?php _e( 'Sum of all paid commissions so far', 'yith-woocommerce-affiliates' ) ?>">[?]</a>
		</td>
		<td><?php echo wc_price( $total_paid ) ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Number of hits', 'yith-woocommerce-affiliates' ) ?></td>
		<td class="help">
			<a href="#" class="help_tip" data-tip="<?php _e( 'Number of clicks', 'yith-woocommerce-affiliates' ) ?>">[?]</a>
		</td>
		<td><?php echo $total_clicks ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Number of conversions', 'yith-woocommerce-affiliates' ) ?></td>
		<td class="help">
			<a href="#" class="help_tip" data-tip="<?php _e( 'Number of conversions', 'yith-woocommerce-affiliates' ) ?>">[?]</a>
		</td>
		<td><?php echo $total_conversions ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Average conversion rate', 'yith-woocommerce-affiliates' ) ?></td>
		<td class="help">
			<a href="#" class="help_tip" data-tip="<?php _e( 'Average percent conversion rate', 'yith-woocommerce-affiliates' ) ?>">[?]</a>
		</td>
		<td><?php echo $avg_conv_rate ?></td>
	</tr>
	</tbody>
</table>