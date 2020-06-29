<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Text Plugin Admin View
 *
 * @package    YITH
 * @author     Emanuela Castorina <emanuela.castorina@yithemes.it>
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

extract( $field );

$class            = isset( $option['class'] ) ? $option['class'] : '';
$currencies       = array();
$default_currency = get_woocommerce_currency();
array_push( $currencies, $default_currency );
$value = maybe_unserialize( $value );
// filter to multi currencies integration.
$currencies = apply_filters( 'ywpar_get_active_currency_list', $currencies );
// DO_ACTION : ywpar_before_currency_loop : action triggered before the currency loop inside the option useful to multi-currency plugins.
do_action( 'ywpar_before_currency_loop' );

foreach ( $currencies as $current_currency ) :

	$curr_name = $name . '[' . $current_currency . ']';
	$curr_id   = $id . '[' . $current_currency . ']';
	$points    = ( isset( $value[ $current_currency ]['points'] ) ) ? $value[ $current_currency ]['points'] : ( isset( $value['points'] ) ? $value['points'] : '' );
	$discount  = ( isset( $value[ $current_currency ]['discount'] ) ) ? $value[ $current_currency ]['discount'] : ( isset( $value['discount'] ) ? $value['discount'] : '' );
	?>
	<div id="<?php echo esc_attr( $id ); ?>-container"
		 class="yit_options rm_option rm_input rm_text conversion-options <?php echo esc_attr( $class ); ?>">
		<div class="option">
			<input type="number" name="<?php echo esc_attr( $curr_name ); ?>[points]" step="1" min="0"
				   id="<?php echo esc_attr( $curr_id ); ?>-points"
				   value="<?php echo esc_attr( $points ); ?>"/>
			<span><?php esc_html_e( 'Points', 'yith-woocommerce-points-and-rewards' ); ?></span>
			<input type="number"
				   name="<?php echo esc_attr( $curr_name ); ?>[discount]" step="1" min="0" id="<?php echo esc_attr( $curr_id ); ?>-discount"
				   value="<?php echo esc_attr( $discount ); ?>"/>
			<span>% (<?php echo wp_kses_post( $current_currency . ' - ' . get_woocommerce_currency_symbol( $current_currency ) ); ?>)</span>
		</div>
		<div class="clear"></div>
	</div>

<?php endforeach; ?>
