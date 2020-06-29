<div class="condition-item" id="condition_<?php echo esc_attr( $item_id ) ?>_<?php echo esc_attr( $condition_id ) ?>">
	<div class="condition-row">
		<div class="condition-column">
			<label for="yith_wcmc_advanced_integration_<?php echo esc_attr( $item_id ) ?>_conditions_<?php echo esc_attr( $condition_id ) ?>_condition"><?php _e( 'Condition', 'yith-woocommerce-mailchimp' )?></label>
			<select class="condition_type" name="yith_wcmc_advanced_integration[<?php echo esc_attr( $item_id ) ?>][conditions][<?php echo esc_attr( $condition_id ) ?>][condition]" id="yith_wcmc_advanced_integration_<?php echo esc_attr( $item_id ) ?>_conditions_<?php echo esc_attr( $condition_id ) ?>_condition" style="width: 300px;">
				<option value="product_in_cart" <?php selected( $condition, 'product_in_cart' ) ?> ><?php _e( 'Product in cart', 'yith-woocommerce-mailchimp' )?></option>
				<option value="product_cat_in_cart" <?php selected( $condition, 'product_cat_in_cart' ) ?> ><?php _e( 'Product category in cart', 'yith-woocommerce-mailchimp' )?></option>
				<option value="order_total" <?php selected( $condition, 'order_total' ) ?> ><?php _e( 'Order total', 'yith-woocommerce-mailchimp' )?></option>
				<option value="custom" <?php selected( $condition, 'custom' ) ?> ><?php _e( 'Custom', 'yith-woocommerce-mailchimp' )?></option>
			</select>
		</div>
		<div class="condition-column">
			<label><?php _e( 'Details', 'yith-woocommerce-mailchimp' )?></label>

			<!-- Operator set -->
			<select class="condition_op_set" name="yith_wcmc_advanced_integration[<?php echo esc_attr( $item_id ) ?>][conditions][<?php echo esc_attr( $condition_id ) ?>][op_set]" id="yith_wcmc_advanced_integration_<?php echo esc_attr( $item_id ) ?>_conditions_<?php echo esc_attr( $condition_id ) ?>_op_set" style="min-width: 300px;">
				<option value="contains_one" <?php selected( $op_set, 'contains_one' ) ?> ><?php _e( 'Contains at least one of', 'yith-woocommerce-mailchimp' )?></option>
				<option value="contains_all" <?php selected( $op_set, 'contains_all' ) ?> ><?php _e( 'Contains all of', 'yith-woocommerce-mailchimp' )?></option>
				<option value="not_contain" <?php selected( $op_set, 'not_contain' ) ?> ><?php _e( 'Does not contain', 'yith-woocommerce-mailchimp' )?></option>
			</select>

			<!-- Operator number -->
			<select class="condition_op_number" name="yith_wcmc_advanced_integration[<?php echo esc_attr( $item_id ) ?>][conditions][<?php echo esc_attr( $condition_id ) ?>][op_number]" id="yith_wcmc_advanced_integration_<?php echo esc_attr( $item_id ) ?>_conditions_<?php echo esc_attr( $condition_id ) ?>_op_number" style="min-width: 300px;">
				<option value="less_than" <?php selected( $op_number, 'less_than' ) ?> ><?php _e( 'Less than', 'yith-woocommerce-mailchimp' )?></option>
				<option value="less_or_equal" <?php selected( $op_number, 'less_or_equal' ) ?> ><?php _e( 'Less than or equal to', 'yith-woocommerce-mailchimp' )?></option>
				<option value="equal" <?php selected( $op_number, 'equal' ) ?> ><?php _e( 'Equals to', 'yith-woocommerce-mailchimp' )?></option>
				<option value="greater_or_equal" <?php selected( $op_number, 'greater_or_equal' ) ?> ><?php _e( 'Greater than or equal to', 'yith-woocommerce-mailchimp' )?></option>
				<option value="greater_than" <?php selected( $op_number, 'greater_than' ) ?> ><?php _e( 'Greater than', 'yith-woocommerce-mailchimp' )?></option>
			</select>

			<!-- Products -->
			<?php yit_add_select2_fields( array(
				'class' => 'condition_products wc-product-search',
				'name' => "yith_wcmc_advanced_integration[{$item_id}][conditions][{$condition_id}][products]",
				'id' => "yith_wcmc_advanced_integration_{$item_id}_conditions_{$condition_id}_products",
				'data-multiple' => true,
				'data-selected' => $json_ids,
				'data-placeholder' => __( 'Search for a product&hellip;', 'yith-woocommerce-mailchimp' ),
				'data-action' => 'woocommerce_json_search_products_and_variations',
				'value' => implode( ',', array_keys( $json_ids ) )
			) ) ?>

			<!-- Products cat -->
			<select class="wc-enhanced-select condition_cats" multiple="multiple" name="yith_wcmc_advanced_integration[<?php echo esc_attr( $item_id ) ?>][conditions][<?php echo esc_attr( $condition_id ) ?>][prod_cats][]" id="yith_wcmc_advanced_integration_<?php echo esc_attr( $item_id ) ?>_conditions_<?php echo esc_attr( $condition_id ) ?>_prod_cats" style="min-width: 300px;" data-placeholder="<?php _e( 'Select a category', 'yith-woocommerce-mailchimp' ); ?>">
				<?php
				$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );

				if ( $categories ) :
					foreach ( $categories as $cat ) :
					?>
					<option value="<?php echo esc_attr( $cat->term_id )?>" <?php selected( in_array( $cat->term_id, $prod_cats ) ) ?> ><?php echo esc_html( $cat->name )?></option>
				<?php
					endforeach;
				endif;
				?>
			</select>

			<!-- Order total -->
			<input class="condition_total" type="number" step="any" name="yith_wcmc_advanced_integration[<?php echo esc_attr( $item_id ) ?>][conditions][<?php echo esc_attr( $condition_id ) ?>][order_total]" id="yith_wcmc_advanced_integration_<?php echo esc_attr( $item_id ) ?>_conditions_<?php echo esc_attr( $condition_id ) ?>_order_total" placeholder="<?php _e( 'Threshold', 'yith-woocommerce-mailchimp' )?>" style="min-width: 300px;" value="<?php echo esc_attr( $order_total ) ?>" />

			<!-- Custom key -->
			<input class="condition_key" type="text" name="yith_wcmc_advanced_integration[<?php echo esc_attr( $item_id ) ?>][conditions][<?php echo esc_attr( $condition_id ) ?>][custom_key]" id="yith_wcmc_advanced_integration_<?php echo esc_attr( $item_id ) ?>_conditions_<?php echo esc_attr( $condition_id ) ?>_custom_key" placeholder="<?php _e( 'Field name in checkout page', 'yith-woocommerce-mailchimp' )?>" style="min-width: 300px;" value="<?php echo esc_attr( $custom_key ) ?>" />

			<!-- Operator mixed -->
			<select class="condition_op_mixed" name="yith_wcmc_advanced_integration[<?php echo esc_attr( $item_id ) ?>][conditions][<?php echo esc_attr( $condition_id ) ?>][op_mixed]" id="yith_wcmc_advanced_integration_<?php echo esc_attr( $item_id ) ?>_conditions_<?php echo esc_attr( $condition_id ) ?>_op_mixed" style="min-width: 300px;">
				<optgroup label="<?php _e( 'String operator', 'yith-woocommerce-mailchimp' )?>">
					<option value="is" <?php selected( $op_mixed, 'is' ) ?> ><?php _e( 'Is', 'yith-woocommerce-mailchimp' )?></option>
					<option value="not_is" <?php selected( $op_mixed, 'not_is' ) ?> ><?php _e( 'Is not', 'yith-woocommerce-mailchimp' )?></option>
					<option value="contains" <?php selected( $op_mixed, 'contains' ) ?> ><?php _e( 'Contains', 'yith-woocommerce-mailchimp' )?></option>
					<option value="not_contains" <?php selected( $op_mixed, 'not_contains' ) ?> ><?php _e( 'Does not contain', 'yith-woocommerce-mailchimp' )?></option>
				</optgroup>
				<optgroup label="<?php _e( 'Number', 'yith-woocommerce-mailchimp' )?>">
					<option value="less_than" <?php selected( $op_mixed, 'less_than' ) ?> ><?php _e( 'Less than', 'yith-woocommerce-mailchimp' )?></option>
					<option value="less_or_equal" <?php selected( $op_mixed, 'less_or_equal' ) ?> ><?php _e( 'Less than or equal to', 'yith-woocommerce-mailchimp' )?></option>
					<option value="equal" <?php selected( $op_mixed, 'equal' ) ?> ><?php _e( 'Equal to', 'yith-woocommerce-mailchimp' )?></option>
					<option value="greater_or_equal" <?php selected( $op_mixed, 'greater_or_equal' ) ?> ><?php _e( 'Greater than or equal to', 'yith-woocommerce-mailchimp' )?></option>
					<option value="greater_than" <?php selected( $op_mixed, 'greater_than' ) ?> ><?php _e( 'Greater than', 'yith-woocommerce-mailchimp' )?></option>
				</optgroup>
			</select>

			<!-- Custom value -->
			<input class="condition_value" type="text" name="yith_wcmc_advanced_integration[<?php echo esc_attr( $item_id ) ?>][conditions][<?php echo esc_attr( $condition_id ) ?>][custom_value]" id="yith_wcmc_advanced_integration_<?php echo esc_attr( $item_id ) ?>_conditions_<?php echo esc_attr( $condition_id ) ?>_custom_value" placeholder="<?php _e( 'Field value in checkout page', 'yith-woocommerce-mailchimp' )?>" style="min-width: 300px;" value="<?php echo esc_attr( $custom_value ) ?>" />
		</div>
		<a href="#" class="remove-button"><?php _e( 'Remove', 'yith-woocommerce-mailchimp' )?></a>
	</div>
</div>