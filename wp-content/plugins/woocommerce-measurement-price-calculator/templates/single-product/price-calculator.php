<?php
/**
 * WooCommerce Measurement Price Calculator
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Measurement Price Calculator to newer
 * versions in the future. If you wish to customize WooCommerce Measurement Price Calculator for your
 * needs please refer to http://docs.woocommerce.com/document/measurement-price-calculator/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Product page measurement pricing calculator.
 *
 * @global \WC_Product $product the product
 * @type \WC_Price_Calculator_Measurement[] $measurements array of measurements
 * @type \WC_Price_Calculator_Measurement $product_measurement the measurement
 * @type \WC_Price_Calculator_Settings $settings calculator settings
 * @type float $default_step default step value based on calculator precision
 * @type string $calculator_mode the current calculator mode for the product
 *
 * @version 3.13.5
 * @since 1.0.0
 */
global $product;

$total_amount_text = apply_filters(
	'wc_measurement_price_calculator_total_amount_text',
	$product_measurement->get_unit_label() ?
		/* translators: Placeholders: %1$s - measurement label, %2$s - measurement unit label */
		sprintf( __( 'Total %1$s (%2$s)', 'woocommerce-measurement-price-calculator' ), $product_measurement->get_label(), __( $product_measurement->get_unit_label(), 'woocommerce-measurement-price-calculator' ) ) :
		/* translators: Placeholders: %s - measurement label */
		sprintf( __( 'Total %s', 'woocommerce-measurement-price-calculator' ), $product_measurement->get_label() ),
	$product
);

// pricing overage
$pricing_overage     = $settings->get_pricing_overage();
$has_pricing_overage = $pricing_overage > 0;

?>
<table id="price_calculator" class="wc-measurement-price-calculator-price-table <?php echo sanitize_html_class( $product->get_type() . '_price_calculator' ) . ' ' . sanitize_html_class( $calculator_mode ); ?>">

	<?php foreach ( $measurements as $measurement ) : ?>

		<?php

		$measurement_name    = $measurement->get_name() . '_needed';
		$measurement_value   = isset( $_POST[ $measurement_name ] ) ? wc_clean( $_POST[ $measurement_name ] ) : '';
		$measurement_options = $measurement->get_options();
		$input_accepted      = $settings->get_accepted_input( $measurement->get_name() );
		$input_attributes    = $settings->get_input_attributes( $measurement->get_name() );
		$attributes          = array();
		$help_tooltip        = '';

		if ( empty( $input_attributes ) ) {

			// default text input field
			$input_type = 'text';

		} else {

			// numeric input field
			$input_type = 'number';

			if ( ! isset( $input_attributes['step'] ) ) {
				$input_attributes['step'] = $default_step;
			}

			// convert to HTML attributes
			foreach ( $input_attributes as $key => $value ) {
				$attributes[] = $key . '="' . $value . '"';
			}
		}

		if ( 'text' === $input_type || ( 'limited' === $input_accepted && empty( $measurement_options ) ) ) {

			$decimal_separator  = trim( wc_get_price_decimal_separator() );
			$thousand_separator = trim( wc_get_price_thousand_separator() );
			$format_example    = "1{$thousand_separator}234{$decimal_separator}56";

			/* translators: Placeholder: %s - format example */
			$help_text = sprintf( __( 'Please enter the desired amount with this format: %s', 'woocommerce-measurement-price-calculator' ), $format_example );

		} else {

			$help_text           = '';
		}

		?>
		<tr class="price-table-row <?php echo sanitize_html_class( $measurement->get_name() ); ?>-input">

			<td>
				<label for="<?php echo esc_attr( $measurement_name ); ?>">
					<?php

					echo ( $measurement->get_unit_label() ?
						/* translators: Placeholders: %1$s - measurement label, %2$s - measurement unit label */
						sprintf( __( '%1$s (%2$s)', 'woocommerce-measurement-price-calculator' ), $measurement->get_label(), __( $measurement->get_unit_label(), 'woocommerce-measurement-price-calculator' ) ) :
						__( $measurement->get_label(), 'woocommerce-measurement-price-calculator' )
					);

					?>
				</label>
			</td>

			<td style="text-align:right;">

				<?php if ( '' !== $help_text ) : ?>

					<?php $help_tooltip = '<span class="dashicons dashicons-editor-help wc-measurement-price-calculator-input-help tip" title="' . esc_html( $help_text ) . '"></span>'; ?>

				<?php endif; ?>

				<?php if ( 'limited' === $input_accepted ) : ?>

					<?php if ( empty( $measurement_options ) ) : // in case this option was set, but no options are entered, show it like a free-form input ?>

						<?php echo $help_tooltip; ?>

						<input
							type="<?php echo $input_type; ?>"
							name="<?php echo esc_attr( $measurement_name ); ?>"
							id="<?php echo esc_attr( $measurement_name ); ?>"
							class="amount_needed"
							value="<?php echo esc_attr( $measurement_value ); ?>"
							data-unit="<?php echo esc_attr( $measurement->get_unit() ); ?>"
							data-common-unit="<?php echo esc_attr( $measurement->get_unit_common() ); ?>"
							autocomplete="off"
							<?php echo implode( ' ', $attributes ); ?>
						/>

					<?php elseif ( 1 === count( $measurement_options ) ) : ?>

						<?php

						$measurement_options_keys = array_keys( $measurement_options );

						echo array_pop( $measurement_options );

						?>
						<input
							type="hidden"
							name="<?php echo esc_attr( $measurement_name ); ?>"
							id="<?php echo esc_attr( $measurement_name ); ?>"
							class="amount_needed fixed-value"
							value="<?php echo esc_attr( array_pop( $measurement_options_keys ) ); ?>"
							data-unit="<?php echo esc_attr( $measurement->get_unit() ); ?>"
							data-common-unit="<?php echo esc_attr( $measurement->get_unit_common() ); ?>"
						/>

					<?php else : ?>

						<select data-unit="<?php echo esc_attr( $measurement->get_unit() ); ?>" data-common-unit="<?php echo esc_attr( $measurement->get_unit_common() ); ?>"  name="<?php echo esc_attr( $measurement_name ); ?>" id="<?php echo esc_attr( $measurement_name ); ?>" class="amount_needed">
							<?php foreach ( $measurement->get_options() as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $measurement_value ); ?>><?php echo $label; ?></option>
							<?php endforeach; ?>
						</select>

					<?php endif; ?>

				<?php elseif ( 'free' === $input_accepted ) : ?>

					<?php echo $help_tooltip; ?>

					<input
						type="<?php echo $input_type; ?>"
						name="<?php echo esc_attr( $measurement_name ); ?>"
						id="<?php echo esc_attr( $measurement_name ); ?>"
						class="amount_needed"
						value="<?php echo esc_attr( $measurement_value ); ?>"
						data-unit="<?php echo esc_attr( $measurement->get_unit() ); ?>"
						data-common-unit="<?php echo esc_attr( $measurement->get_unit_common() ); ?>"
						autocomplete="off"
						<?php echo implode( ' ', $attributes ); ?>
					/>

				<?php endif; ?>

			</td>
		</tr>

	<?php endforeach; ?>

	<?php if ( $settings->is_calculator_type_derived() ) : ?>

		<tr class="price-table-row total-amount">
			<td>
				<?php echo $total_amount_text; ?>
			</td>
			<td>
				<span
					class="wc-measurement-price-calculator-total-amount"
					data-unit="<?php echo esc_attr( $product_measurement->get_unit() ); ?>"></span>
			</td>
		</tr>

	<?php endif; ?>

	<?php if ( $has_pricing_overage ) : ?>

		<tr class="price-table-row calculated-price-overage">
			<td><?php echo esc_html( sprintf( __( 'Overage estimate (%s%%)', 'woocommerce-measurement-price-calculator' ), $pricing_overage * 100 ) ); ?></td>
			<td>
				<span class="product_price_overage"></span>
			</td>
		</tr>

	<?php endif; ?>

	<tr class="price-table-row calculated-price">

		<td><?php echo esc_html( $has_pricing_overage ? __( 'Total Price', 'woocommerce-measurement-price-calculator' ) : __( 'Product Price', 'woocommerce-measurement-price-calculator' ) ); ?></td>

		<td>

			<span class="product_price"></span>
			<input
				type="hidden"
				id="_measurement_needed"
				name="_measurement_needed"
				value=""
			/>
			<input
				type="hidden"
				id="_measurement_needed_unit"
				name="_measurement_needed_unit"
				value=""
			/>

			<?php if ( $product->is_sold_individually() ) : ?>

				<input
					type="hidden"
					name="quantity"
					value="1"
				/>

			<?php endif; ?>

		</td>

	</tr>

</table>
<?php
