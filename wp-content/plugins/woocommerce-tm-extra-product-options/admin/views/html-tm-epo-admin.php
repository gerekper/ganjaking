<?php
/**
 * View for displaying single TM EPO record
 *
 * Variables used:
 * @required   $variations
 * @required   $parent_data['attributes']
 * @required   $tmcp_data
 * @required   $tmcp_id
 * @required   $loop
 * @required   $tmcp_post_status
 * @required   $tmcp_required
 * @required   $tmcp_hide_price
 * @required   $tmcp_limit
 *
 * @optional   $_regular_price
 *
 * @package Extra Product Options/Admin/Views
 * @version 4.8
 */

defined( 'ABSPATH' ) || exit;

if ( isset( $variations )
     && isset( $parent_data )
     && isset( $tmcp_data )
     && isset( $tmcp_id )
     && isset( $loop )
     && isset( $tmcp_post_status )
     && isset( $tmcp_required )
     && isset( $tmcp_hide_price )
     && isset( $tmcp_limit )
) {

	$tmcp_attribute_selected_value = isset( $tmcp_data['tmcp_attribute'][0] ) ? $tmcp_data['tmcp_attribute'][0] : '';
	$tmcp_type_selected_value      = isset( $tmcp_data['tmcp_type'][0] ) ? $tmcp_data['tmcp_type'][0] : '';

	$_field_attribute = FALSE;
	foreach ( $parent_data['attributes'] as $attribute ) {
		// Get only attributes that are not variations
		if ( $attribute['is_variation'] || sanitize_title( $attribute['name'] ) != $tmcp_attribute_selected_value ) {
			continue;
		}

		$_field_attribute = TRUE;
		break;
	}
	if ( ! empty( $_field_attribute ) ) {
		?>
        <div data-epo-attr="<?php echo esc_attr( sanitize_title( $tmcp_attribute_selected_value ) ); ?>" class="woocommerce_tm_epo wc-metabox closed">
            <h3>
                <div class="tmicon tcfa tcfa-times delete remove_tm_epo" rel="<?php echo esc_attr( $tmcp_id ); ?>"></div>
                <div class="tmicon tcfa tcfa-caret-up fold tip" title="<?php esc_html_e( 'Click to toggle', 'woocommerce-tm-extra-product-options' ); ?>"></div>
                <div class="tmicon tcfa tcfa-grip-vertical move"></div>
                <span class="tm-att-id">#<?php echo esc_html( $tmcp_id ); ?></span>
                <span class="tm-att-label"><?php esc_html_e( 'Attribute:', 'woocommerce-tm-extra-product-options' ); ?></span>
                <span class="tm-att-value"><?php echo esc_html( wc_attribute_label( urldecode( $tmcp_attribute_selected_value ) ) ); ?></span>
                <input type="hidden" value="<?php echo esc_attr( sanitize_title( $tmcp_attribute_selected_value ) ); ?>" class="tmcp_attribute" name="tmcp_attribute[<?php echo esc_attr( $loop ); ?>]">
                <input type="hidden" class="tmcp_loop" name="tmcp_loop[<?php echo esc_attr( $loop ); ?>]" value="<?php echo esc_attr( $loop ); ?>"/>
                <input type="hidden" name="tmcp_post_id[<?php echo esc_attr( $loop ); ?>]" value="<?php echo esc_attr( $tmcp_id ); ?>"/>
                <input type="hidden" class="tm_epo_menu_order" name="tmcp_menu_order[<?php echo esc_attr( $loop ); ?>]" value="<?php echo esc_attr( $loop ); ?>"/>
            </h3>
            <div class="woocommerce_tmcp_attributes wc-metabox-content">
                <div class="data tc-clearfix">
                    <p class="form-row form-row-first tmcp-main-choices">
                        <label><?php esc_html_e( 'Type:', 'woocommerce-tm-extra-product-options' ); ?></label>
                        <select class="tm-type" name="tmcp_type[<?php echo esc_attr( $loop ); ?>]">
                            <option <?php selected( $tmcp_type_selected_value, 'radio' ) ?> value="radio"><?php esc_html_e( 'Radio buttons', 'woocommerce-tm-extra-product-options' ); ?></option>
                            <option <?php selected( $tmcp_type_selected_value, 'checkbox' ) ?> value="checkbox"><?php esc_html_e( 'Checkbox', 'woocommerce-tm-extra-product-options' ); ?></option>
                            <option <?php selected( $tmcp_type_selected_value, 'select' ) ?> value="select"><?php esc_html_e( 'Select', 'woocommerce-tm-extra-product-options' ); ?></option>
                        </select>
                    </p>
                    <p class="form-row form-row-last">
                    <span class="tm-options">
                        <label><?php esc_html_e( 'Settings:', 'woocommerce-tm-extra-product-options' ); ?></label>
                        <span class="tm-hide-price">
                            <label><input type="checkbox" class="checkbox" name="tmcp_hide_price[<?php echo esc_attr( $loop ); ?>]" <?php checked( $tmcp_hide_price, 1 ); ?> value="1"/> <?php esc_html_e( 'Hide price', 'woocommerce-tm-extra-product-options' ); ?></label>
                        </span>
                        <span class="tm-required">
                            <label><input type="checkbox" class="checkbox" name="tmcp_required[<?php echo esc_attr( $loop ); ?>]" <?php checked( $tmcp_required, 1 ); ?> value="1"/> <?php esc_html_e( 'Required', 'woocommerce-tm-extra-product-options' ); ?></label>
                        </span>
                        <span class="tm-enabled">
                            <label><input type="checkbox" class="checkbox" name="tmcp_enabled[<?php echo esc_attr( $loop ); ?>]" <?php checked( $tmcp_post_status, 'publish' ); ?> /> <?php esc_html_e( 'Enabled', 'woocommerce-tm-extra-product-options' ); ?></label>
                        </span>
                    </span>
                        <span class="tmcp_choices<?php if ( $tmcp_type_selected_value != "checkbox" ) {
							echo ' tm-hidden';
						} ?>">
                        <?php
                        echo '<span class="tm-hide-price"><label>' . esc_html__( 'Limit selection', 'woocommerce-tm-extra-product-options' ) . ': <input step="1" min="0" max="" name="tmcp_limit[' . esc_attr( $loop ) . ']" value="' . esc_attr( $tmcp_limit ) . '" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric" type="number" /></label></span>';
                        ?>
                    </span>
                    </p>
                    <p class="form-row form-row-full tmcp_variation show_if_variable">
                        <label><?php esc_html_e( 'Variation:', 'woocommerce-tm-extra-product-options' ); ?></label>
						<?php include( 'html-tm-epo-admin-variations.php' ); ?>
                    </p>
                    <p class="form-row form-row-first tmcp_attribute">
                        <label><?php esc_html_e( 'Attribute:', 'woocommerce-tm-extra-product-options' ); ?></label>
						<?php include( 'html-tm-epo-admin-attributes.php' ); ?>
                    </p>
                    <p class="form-row form-row-last tmcp_pricing">
                        <label><?php echo esc_html__( 'Price:', 'woocommerce-tm-extra-product-options' ) . ' (' . get_woocommerce_currency_symbol() . ')'; ?></label>
						<?php
						if ( ! empty( $_regular_price ) && is_array( $_regular_price ) ) {
							/*
							* $key_attribute = attirbute
							* $key_variation = variation
							* $price = price
							*/
							if ( ! is_array( $_regular_price_type ) ) {
								$_regular_price_type = array();
							}
							foreach ( $_regular_price as $key_attribute => $value ) {
								foreach ( $value as $key_variation => $price ) {
									if ( ! isset( $_regular_price_type[ $key_attribute ] ) ) {
										$_regular_price_type[ $key_attribute ] = array();
									}
									if ( ! isset( $_regular_price_type[ $key_attribute ][ $key_variation ] ) ) {
										$_regular_price_type[ $key_attribute ][ $key_variation ] = "";
									}
									?>
                                    <input type="text" size="5" name="tmcp_regular_price[<?php echo esc_attr( $loop ); ?>][<?php echo esc_attr( $key_attribute ); ?>][<?php echo esc_attr( $key_variation ); ?>]" value="<?php echo esc_attr( $price ); ?>" class="wc_input_price tmcp-price-input tmcp-price-input-variation-<?php echo esc_attr( $key_variation ); ?>" data-price-input-attribute="<?php echo esc_attr( $key_attribute ); ?>" placeholder="<?php esc_html_e( 'Custom price (required)', 'woocommerce-tm-extra-product-options' ); ?>"/>
                                    <select class="tmcp-price-input-type tmcp-price-input-variation-<?php echo esc_attr( $key_variation ); ?>" data-price-input-attribute="<?php echo esc_attr( $key_attribute ); ?>" name="tmcp_regular_price_type[<?php echo esc_attr( $loop ); ?>][<?php echo esc_attr( $key_attribute ); ?>][<?php echo esc_attr( $key_variation ); ?>]">
                                        <option <?php selected( $_regular_price_type[ $key_attribute ][ $key_variation ], '' ) ?> value=""><?php esc_html_e( 'Fixed amount', 'woocommerce-tm-extra-product-options' ); ?></option>
                                        <option <?php selected( $_regular_price_type[ $key_attribute ][ $key_variation ], 'percent' ) ?> value="percent"><?php esc_html_e( 'Percent of the original price', 'woocommerce-tm-extra-product-options' ); ?></option>
                                    </select>
									<?php
								}
							}
						} else {
							?>
                            <input type="text" size="5" name="tmcp_regular_price[<?php echo esc_attr( $loop ); ?>][0][0]" value="" class="wc_input_price tmcp-price-input tmcp-price-input-variation-0 tmcp-price-input-attribute-0" data-price-input-attribute="0" placeholder="<?php esc_html_e( 'Custom price', 'woocommerce-tm-extra-product-options' ); ?>"/>
                            <select class="tmcp-price-input-type tmcp-price-input-variation-0 tmcp-price-input-attribute-0" data-price-input-attribute="0" name="tmcp_regular_price_type[<?php echo esc_attr( $loop ); ?>][0][0]">
                                <option value=""><?php esc_html_e( 'Fixed amount', 'woocommerce-tm-extra-product-options' ); ?></option>
                                <option value="percent"><?php esc_html_e( 'Percent of the original price', 'woocommerce-tm-extra-product-options' ); ?></option>
                            </select>
							<?php
						}
						?>
                    </p>
                </div>
            </div>

        </div>
		<?php
	} else {
		?>
        <div data-epo-attr="<?php echo esc_attr( sanitize_title( $tmcp_attribute_selected_value ) ); ?>" class="missing woocommerce_tm_epo wc-metabox closed">
            <h3>
                <div class="tmicon tcfa tcfa-times delete remove_tm_epo" rel="<?php echo esc_attr( $tmcp_id ); ?>"></div>
                <span class="tm-att-id">#<?php echo esc_html( $tmcp_id ); ?> &mdash; </span>
                <span class="tm-att-label"><?php esc_html_e( 'Attribute:', 'woocommerce-tm-extra-product-options' ); ?></span>
                <span class="tm-att-value"><?php echo esc_html( wc_attribute_label( $tmcp_attribute_selected_value ) ); ?></span>
                <input type="hidden" value="<?php echo esc_attr( sanitize_title( $tmcp_attribute_selected_value ) ); ?>" class="tmcp_attribute" name="tmcp_attribute[<?php echo esc_attr( $loop ); ?>]">
				<?php esc_html_e( 'Attributes missing. Please DELETE this extra option:', 'woocommerce-tm-extra-product-options' );
				?>

                <input type="hidden" class="checkbox" name="tmcp_type[<?php echo esc_attr( $loop ); ?>]" value="<?php echo esc_attr( $tmcp_type_selected_value ); ?>"/>
                <input type="hidden" class="checkbox" name="tmcp_hide_price[<?php echo esc_attr( $loop ); ?>]" <?php checked( $tmcp_hide_price, 1 ); ?> value="1"/>


                <input type="hidden" class="tmcp_loop" name="tmcp_loop[<?php echo esc_attr( $loop ); ?>]" value="<?php echo esc_attr( $loop ); ?>"/>
                <input type="hidden" name="tmcp_post_id[<?php echo esc_attr( $loop ); ?>]" value="<?php echo esc_attr( $tmcp_id ); ?>"/>
                <input type="hidden" class="tm_epo_menu_order" name="tmcp_menu_order[<?php echo esc_attr( $loop ); ?>]" value="<?php echo esc_attr( $loop ); ?>"/>
            </h3>
            <div class="woocommerce_tmcp_attributes wc-metabox-content">
                <div class="data tc-clearfix">
					<?php
					echo '<label><input type="hidden" name="tmcp_limit[' . esc_attr( $loop ) . ']" value="' . esc_attr( $tmcp_limit ) . '" /></label>';
					include( 'html-tm-epo-admin-variations.php' );
					if ( isset( $_regular_price ) && is_array( $_regular_price ) ) {
						/*
						* $key_attribute = attirbute
						* $key_variation = variation
						* $price = price
						*/
						if ( ! is_array( $_regular_price_type ) ) {
							$_regular_price_type = array();
						}
						foreach ( $_regular_price as $key_attribute => $value ) {
							foreach ( $value as $key_variation => $price ) {
								if ( ! isset( $_regular_price_type[ $key_attribute ] ) ) {
									$_regular_price_type[ $key_attribute ] = array();
								}
								if ( ! isset( $_regular_price_type[ $key_attribute ][ $key_variation ] ) ) {
									$_regular_price_type[ $key_attribute ][ $key_variation ] = "";
								}
								?>
                                <input type="text" size="5" name="tmcp_regular_price[<?php echo esc_attr( $loop ); ?>][<?php echo esc_attr( $key_attribute ); ?>][<?php echo esc_attr( $key_variation ); ?>]" value="<?php echo esc_attr( $price ); ?>" class="wc_input_price tmcp-price-input tmcp-price-input-variation-<?php echo esc_attr( $key_variation ); ?>" data-price-input-attribute="<?php echo esc_attr( $key_attribute ); ?>" placeholder="<?php esc_html_e( 'Custom price (required)', 'woocommerce-tm-extra-product-options' ); ?>"/>
                                <select class="tmcp-price-input-type tmcp-price-input-variation-<?php echo esc_attr( $key_variation ); ?>" data-price-input-attribute="<?php echo esc_attr( $key_attribute ); ?>" name="tmcp_regular_price_type[<?php echo esc_attr( $loop ); ?>][<?php echo esc_attr( $key_attribute ); ?>][<?php echo esc_attr( $key_variation ); ?>]">
                                    <option <?php selected( $_regular_price_type[ $key_attribute ][ $key_variation ], '' ) ?> value=""><?php esc_html_e( 'Fixed amount', 'woocommerce-tm-extra-product-options' ); ?></option>
                                    <option <?php selected( $_regular_price_type[ $key_attribute ][ $key_variation ], 'percent' ) ?> value="percent"><?php esc_html_e( 'Percent of the orignal price', 'woocommerce-tm-extra-product-options' ); ?></option>
                                </select>
								<?php
							}
						}
					} else {
						?>
                        <input type="text" size="5" name="tmcp_regular_price[<?php echo esc_attr( $loop ); ?>][0][0]" value="" class="wc_input_price tmcp-price-input tmcp-price-input-variation-0 tmcp-price-input-attribute-0" data-price-input-attribute="0" placeholder="<?php esc_html_e( 'Custom price', 'woocommerce-tm-extra-product-options' ); ?>"/>
                        <select class="tmcp-price-input-type tmcp-price-input-variation-0 tmcp-price-input-attribute-0" data-price-input-attribute="0" name="tmcp_regular_price_type[<?php echo esc_attr( $loop ); ?>][0][0]">
                            <option value=""><?php esc_html_e( 'Fixed amount', 'woocommerce-tm-extra-product-options' ); ?></option>
                            <option value="percent"><?php esc_html_e( 'Percent of the orignal price', 'woocommerce-tm-extra-product-options' ); ?></option>
                        </select>
						<?php
					}
					?>
                </div>
            </div>
        </div>
		<?php
	}
}
