<div class="field-item" id="field_<?php echo esc_attr( $item_id ) ?>_<?php echo esc_attr( $field_id ) ?>">
	<div class="field-row">
		<div class="field-column">
			<label for="yith_wcmc_advanced_integration_<?php echo esc_attr( $item_id ) ?>_fields_<?php echo esc_attr( $field_id ) ?>_checkout"><?php _e( 'Checkout field', 'yith-woocommerce-mailchimp' )?></label>
			<select class="chosen_select" name="yith_wcmc_advanced_integration[<?php echo esc_attr( $item_id ) ?>][fields][<?php echo esc_attr( $field_id ) ?>][checkout]" id="yith_wcmc_advanced_integration_<?php echo esc_attr( $item_id ) ?>_fields_<?php echo esc_attr( $field_id ) ?>_checkout" style="width: 300px;">
				<?php
				if ( ! empty( $checkout_fields ) ):
					foreach ( $checkout_fields as $category ):
						?>
						<optgroup label="<?php echo $category['name'] ?>">
							<?php foreach( $category['fields'] as $id => $field_name ): ?>
								<option value="<?php echo esc_attr( $id ) ?>" <?php selected( $selected_checkout, $id )?> ><?php echo $field_name ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php
					endforeach;
				endif;
				?>
			</select>
		</div>
		<div class="field-column">
			<label for="yith_wcmc_advanced_integration_<?php echo esc_attr( $item_id ) ?>_fields_<?php echo esc_attr( $field_id ) ?>_checkout"><?php _e( 'MailChimp field', 'yith-woocommerce-mailchimp' )?></label>
			<select class="chosen_select" name="yith_wcmc_advanced_integration[<?php echo esc_attr( $item_id ) ?>][fields][<?php echo esc_attr( $field_id ) ?>][merge_var]" id="yith_wcmc_advanced_integration_<?php echo esc_attr( $item_id ) ?>_fields_<?php echo esc_attr( $field_id ) ?>_merge_var" style="width: 300px;">
				<?php
				if ( ! empty( $fields ) ):
					foreach ( $fields as $id => $field_name ):
						?>
						<option value="<?php echo esc_attr( $id ) ?>" <?php selected( $selected_merge_var, $id )?> ><?php echo $field_name ?></option>
					<?php
					endforeach;
				endif;
				?>
			</select>
			<a href="#" class="update-fields button button-secondary"><?php _e( 'Update fields', 'yith-woocommerce-mailchimp' )?></a>
		</div>
		<a href="#" class="remove-button"><?php _e( 'Remove', 'yith-woocommerce-mailchimp' )?></a>
	</div>
</div>