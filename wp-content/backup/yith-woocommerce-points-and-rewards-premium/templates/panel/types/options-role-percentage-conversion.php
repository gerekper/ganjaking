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


global $wp_roles;
$roles = $wp_roles->roles;
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
$index = 0;

if ( isset( $value['role_conversion'] ) ) :
	foreach ( $value['role_conversion'] as $conversion ) :

		$index++;
		$single_role  = $conversion['role'];
		$current_name = $name . '[role_conversion][' . $index . ']';
		$current_id   = $id . '[role_conversion][' . $index . ']';
		$hide_remove  = 1 == $index ? 'hide-remove' : 1;
		?>
		<div id="<?php echo esc_attr( $id ); ?>-container" data-index="<?php echo esc_attr( $index ); ?>"
			class="yit_options rm_option rm_input rm_text role-conversion-options <?php echo esc_attr( $class ); ?>">
			<div class="option">
				<div class="conversion-role">
					<select name="<?php echo esc_attr( $current_name . '[role]' ); ?>"
						id="<?php echo esc_attr( $current_id . '[role]' ); ?>">
						<?php foreach ( $roles as $key => $role ) : ?>
							<option
								value="<?php echo esc_attr( $key ); ?>" <?php selected( $single_role, $key, 1 ); ?>><?php echo esc_html( translate_user_role( $role['name'] ) ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="conversion-currencies">
					<?php
					foreach ( $currencies as $current_currency ) :
						$current_name = $name . '[role_conversion][' . $index . '][' . $current_currency . ']';
						$current_id   = $id . '[role_conversion][' . $index . '][' . $current_currency . ']';
						$points       = ( isset( $conversion[ $current_currency ]['points'] ) ) ? $conversion[ $current_currency ]['points'] : '';
						$discount     = ( isset( $conversion[ $current_currency ]['discount'] ) ) ? $conversion[ $current_currency ]['discount'] : '';
						?>
						<p>
							<input type="number" name="<?php echo esc_attr( $current_name ); ?>[points]" step="1"
								min="0" id="<?php echo esc_attr( $current_id ); ?>-points"
								value="<?php echo esc_attr( $points ); ?>"/>
							<span><?php esc_html_e( 'Points', 'yith-woocommerce-points-and-rewards' ); ?></span>

							<input type="number" name="<?php echo esc_attr( $current_name ); ?>[discount]" step="1"
								min="0" id="<?php echo esc_attr( $current_id ); ?>-discount"
								value="<?php echo esc_attr( $discount ); ?>"/>
							<span>% (<?php echo wp_kses_post( $current_currency . ' - ' . get_woocommerce_currency_symbol( $current_currency ) ); ?>)</span>
						</p>
						<?php
					endforeach;
					?>
				</div>
				<div class="ywpar-actions">
					<span class="ywpar-add-row"></span>
					<span class="ywpar-remove-row <?php echo esc_attr( $hide_remove ); ?>"></span>
				</div>


			</div>
			<div class="clear"></div>
		</div>
		<?php
	endforeach;
endif;
?>
