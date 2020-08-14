<?php
/**
 * Add to cart deposit shipping calculator
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.1
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCDP' ) ) {
	exit;
} // Exit if accessed directly

?>

<?php
if ( 'no' === get_option( 'woocommerce_enable_shipping_calc' ) ) {
	return;
}
?>

<?php do_action( 'woocommerce_before_shipping_calculator' ); ?>

<p><a href="#" class="shipping-calculator-button"><?php esc_html_e( 'Calculate Shipping', 'yith-woocommerce-deposits-and-down-payments' ); ?></a></p>

<section class="shipping-calculator-form" style="display:none;">

	<p class="form-row form-row-wide" id="calc_shipping_country_field">
		<select name="calc_shipping_country" id="calc_shipping_country" class="country_to_state country_select" rel="calc_shipping_state">
			<option value=""><?php esc_html_e( 'Select a country&hellip;', 'yith-woocommerce-deposits-and-down-payments' ); ?></option>
			<?php
			foreach ( WC()->countries->get_shipping_countries() as $key => $value ) {
				echo '<option value="' . esc_attr( $key ) . '"' . selected( WC()->customer->get_shipping_country(), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
			}
			?>
		</select>
	</p>

	<p class="form-row form-row-wide" id="calc_shipping_state_field">
		<?php
		$current_cc = WC()->customer->get_shipping_country();
		$current_r  = WC()->customer->get_shipping_state();
		$states     = WC()->countries->get_states( $current_cc );

		// Hidden Input.
		if ( is_array( $states ) && empty( $states ) ) :
			?>
			<input type="hidden" name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php esc_attr_e( 'State / county', 'yith-woocommerce-deposits-and-down-payments' ); ?>" />
			<?php
		elseif ( is_array( $states ) ) :
			?>
			<span>
			<select name="calc_shipping_state" class="state_select" id="calc_shipping_state" placeholder="<?php esc_attr_e( 'State / county', 'yith-woocommerce-deposits-and-down-payments' ); ?>">
				<option value=""><?php esc_html_e( 'Select a state&hellip;', 'yith-woocommerce-deposits-and-down-payments' ); ?></option>
				<?php
				foreach ( $states as $ckey => $cvalue ) :
					?>
					<option value="<?php echo esc_attr( $ckey ); ?>" <?php selected( $current_r, $ckey ); ?> ><?php echo esc_html( $cvalue ); ?></option>
					<?php
				endforeach;
				?>
			</select>
			</span>
			<?php
		else :
			?>
			<input type="text" class="input-text" value="<?php echo esc_attr( $current_r ); ?>" placeholder="<?php esc_attr_e( 'State / County', 'yith-woocommerce-deposits-and-down-payments' ); ?>" name="calc_shipping_state" id="calc_shipping_state" />
			<?php
		endif;
		?>
	</p>

	<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_city', false ) ) : ?>

		<p class="form-row form-row-wide" id="calc_shipping_city_field">
			<input type="text" class="input-text" value="<?php echo esc_attr( WC()->customer->get_shipping_city() ); ?>" placeholder="<?php esc_attr_e( 'City', 'yith-woocommerce-deposits-and-down-payments' ); ?>" name="calc_shipping_city" id="calc_shipping_city" />
		</p>

	<?php endif; ?>

	<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_postcode', true ) ) : ?>

		<p class="form-row form-row-wide" id="calc_shipping_postcode_field">
			<input type="text" class="input-text" value="<?php echo esc_attr( WC()->customer->get_shipping_postcode() ); ?>" placeholder="<?php esc_attr_e( 'Postcode / ZIP', 'yith-woocommerce-deposits-and-down-payments' ); ?>" name="calc_shipping_postcode" id="calc_shipping_postcode" />
		</p>

	<?php endif; ?>

	<p><button type="submit" name="calc_shipping" value="1" class="button"><?php esc_html_e( 'Update Shipping Methods', 'yith-woocommerce-deposits-and-down-payments' ); ?></button></p>
</section>

<?php do_action( 'woocommerce_after_shipping_calculator' ); ?>
