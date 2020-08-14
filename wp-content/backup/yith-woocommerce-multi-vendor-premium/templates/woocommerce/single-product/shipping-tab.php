<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php if( ! empty( $shipping_processing_time ) || ! empty( $shipping_location_from ) ) : ?>

    <div id="ready-to-ship">
        <?php
        $title = $ready_to_ship = $title_text = $shipping_location_from_part = '';

        if( ! empty( $shipping_processing_time ) ){

            $title_text = $processing_time_title;

            if( ! empty( $shipping_location_from ) ){
                $shipping_location_from_part =  sprintf( '%s <strong>%s</strong>', $shipping_location_from_prefix, $shipping_location_from );
            }

            $ready_to_ship = sprintf( '<p>%s <strong>%s</strong> %s</p>',
                $shipping_processing_time_prefix,
                $shipping_processing_time,
                $shipping_location_from_part,
                $shipping_location_from_part
            );
        }

        else {
            $title_text     = $shipping_location_from_title;
            $ready_to_ship  = sprintf( '<p>%s</p>', $shipping_location_from );
        }

        $title = sprintf( '<h4 class="yith_wcmv_shipping_tab_title">%s</h4>', $title_text );

        echo $title . $ready_to_ship;
        ?>
    </div>

<?php endif; ?>

<?php if( ! empty( $shipping_policy ) ) : ?>

    <div id="shipping-policy">
        <?php printf('<h4 class="yith_wcmv_shipping_tab_title">%s</h4><p>%s</p>', $shipping_policy_title, $shipping_policy ); ?>
    </div>

<?php endif; ?>

<?php if( ! empty( $shipping_policy ) ) : ?>

    <div id="refund-policy">
        <?php printf('<h4 class="yith_wcmv_shipping_tab_title">%s</h4><p>%s</p>', $refund_policy_title, $shipping_refund_policy ); ?>
    </div>

<?php endif; ?>