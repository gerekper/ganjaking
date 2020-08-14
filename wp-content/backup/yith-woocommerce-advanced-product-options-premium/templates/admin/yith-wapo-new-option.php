<tr>
	<td class="option-sort"><i class="dashicons dashicons-move"></i></td>
	<td>
		<div id="option-image-new" class="option-image">
			<input class="opt-image" type="hidden" name="options[image][]" size="60" value="">
			<p class="save-first"><?php _e( 'Save to set image!', 'yith-woocommerce-product-add-ons' ) ?></p>
		</div>
	</td>
	<td>
		<div class="option-label">
			<small><?php echo __( 'Option Label', 'yith-woocommerce-product-add-ons' ); ?> (<?php echo __( 'Required', 'yith-woocommerce-product-add-ons' ); ?>)</small>
			<input type="text" name="options[label][]" value="" />
		</div>
		<div class="option-description">
			<small><?php echo __( 'Description', 'yith-woocommerce-product-add-ons' ); ?></small>
			<input type="text" name="options[description][]" value="" />
		</div>
		<div class="option-placeholder">
			<small><?php echo __( 'Placeholder', 'yith-woocommerce-product-add-ons' ); ?></small>
			<input type="text" name="options[placeholder][]" value="" />
		</div>
		<div class="option-tooltip">
			<small><?php echo __( 'Tooltip', 'yith-woocommerce-product-add-ons' ); ?></small>
			<input type="text" name="options[tooltip][]" value="" />
		</div>
		<div class="option-price">
			<small><?php echo __( 'Price', 'yith-woocommerce-product-add-ons' ); ?></small>
			<input type="text" name="options[price][]" value="" placeholder="0" />
		</div>
		<div class="option-type">
			<small><?php echo __( 'Amount', 'yith-woocommerce-product-add-ons' ); ?></small>
			<select name="options[type][]">
				<option value="fixed"><?php _e( 'Fixed amount', 'yith-woocommerce-product-add-ons' ); ?></option>
				<option value="percentage"><?php _e( '% markup', 'yith-woocommerce-product-add-ons' ); ?></option>
				<option value="calculated_multiplication"><?php _e( 'Price multiplied by value', 'yith-woocommerce-product-add-ons' ); ?></option>
				<option value="calculated_character_count"><?php _e( 'Price multiplied by string length', 'yith-woocommerce-product-add-ons' ); ?></option>
			</select>
		</div>
		<div class="option-min">
			<small><?php echo __( 'Min', 'yith-woocommerce-product-add-ons' ); ?></small>
			<input type="text" name="options[min][]" value="" placeholder="0" />
		</div>
		<div class="option-max">
			<small><?php echo __( 'Max', 'yith-woocommerce-product-add-ons' ); ?></small>
			<input type="text" name="options[max][]" value="" placeholder="0" />
		</div>
		<div class="option-default">
			<small><?php echo __( 'Checked', 'yith-woocommerce-product-add-ons' ); ?><br /></small>
			<input type="checkbox" name="options[default][]" value="<?php echo isset( $i ) ? $i : ''; ?>" class="new_default" />
		</div>
		<div class="option-required">
			<small><?php echo __( 'Required', 'yith-woocommerce-product-add-ons' );?><br /></small>
			<input type="checkbox" name="options[required][]" value="<?php echo isset( $i ) ? $i : ''; ?>" class="new_required" />
		</div>
	</td>
	<td>
		<div class="option-actions">
			<br />
			<a class="button remove-row" title="<?php echo __( 'Delete', 'yith-woocommerce-product-add-ons' ); ?>"><span class="dashicons dashicons-dismiss" style="line-height: 27px;"></span></a>
		</div>
	</td>
</tr>