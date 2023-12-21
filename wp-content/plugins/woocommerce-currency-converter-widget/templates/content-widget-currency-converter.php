<?php
/**
 * Template: Currency Converter widget.
 *
 * @package WC_Currency_Converter/Templates
 * @version 2.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var array $instance   Arguments.
 * @var array $currencies Currencies from a widget instance.
 */

$default_currency = get_woocommerce_currency();
?>
<form id="currency_converter" method="post" action="">
	<div>
		<?php
		if ( ! empty( $instance['message'] ) ) :
			echo wp_kses_post( wpautop( $instance['message'] ) );
		endif;

		if ( ! empty( $instance['currency_display'] ) && 'select' === $instance['currency_display'] ) :
			?>
			<label for="currency_switcher" class="currency_switcher_label">
				<?php esc_html_e( 'Choose a Currency', 'woocommerce-currency-converter-widget' ); ?>
			</label>
			<select id="currency_switcher" class="currency_switcher select" data-default="<?php echo esc_attr( $default_currency ); ?>">
				<?php
				foreach ( $currencies as $currency ) :
					$label = empty( $instance['show_symbols'] ) ? $currency : $currency . ' (' . get_woocommerce_currency_symbol( $currency ) . ')';
					?>
					<option value="<?php echo esc_attr( $currency ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php
				endforeach;
				?>
			</select>
			<?php
			if ( ! empty( $instance['show_reset'] ) ) :
				?>
				<a href="#" class="wc-currency-converter-reset reset">
					<?php esc_html_e( 'Reset', 'woocommerce-currency-converter-widget' ); ?>
				</a>
				<?php
			endif;
		else :
			?>
			<ul class="currency_switcher">
				<?php foreach ( $currencies as $currency ) : ?>
					<?php
					$class = $default_currency === $currency ? 'default currency-' . $currency : 'currency-' . $currency;
					$label = empty( $instance['show_symbols'] ) ? $currency : $currency . ' (' . get_woocommerce_currency_symbol( $currency ) . ')';
					?>
					<li>
						<a href="#" class="<?php echo esc_attr( $class ); ?>" data-currencycode="<?php echo esc_attr( $currency ); ?>">
							<?php echo esc_html( $label ); ?>
						</a>
					</li>
					<?php
				endforeach;
				if ( ! empty( $instance['show_reset'] ) ) :
					?>
					<li>
						<a href="#" class="wc-currency-converter-reset reset">
							<?php esc_html_e( 'Reset', 'woocommerce-currency-converter-widget' ); ?>
						</a>
					</li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>
	</div>
</form>
