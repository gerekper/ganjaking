<div class="fields-item draggable <?php echo ( ! $removable ) ? 'unremovable' : '' ?>" id="field_<?php echo esc_attr( $item_id ) ?>">
	<div class="field-row">
		<div class="field-column">
			<label for="<?php echo esc_attr( $id )?>_<?php echo esc_attr( $item_id ) ?>_name"><?php _e( 'Field name', 'yith-woocommerce-mailchimp' ) ?></label>
			<input type="text" name="<?php echo esc_attr( $id )?>[<?php echo esc_attr( $item_id ) ?>][name]" value="<?php echo esc_attr( $selected_name )?>" style="min-width: 300px;"/>
		</div>
		<div class="field-column">
			<label for="<?php echo esc_attr( $id )?>_<?php echo esc_attr( $item_id ) ?>_merge_var"><?php _e( 'Merge var', 'yith-woocommerce-mailchimp' ) ?></label>

			<?php if( ! $removable ): ?>
				<input type="hidden" value="<?php echo $selected_merge_var ?>" name="<?php echo esc_attr( $id )?>[<?php echo esc_attr( $item_id ) ?>][merge_var]" id="<?php echo esc_attr( $id )?>_<?php echo esc_attr( $item_id ) ?>_merge_var" />
			<?php endif; ?>

			<select <?php echo ( ! $removable ) ? 'disabled="disabled"' : '' ?> class="chosen_select" name="<?php echo esc_attr( $id )?>[<?php echo esc_attr( $item_id ) ?>][merge_var]" id="<?php echo esc_attr( $id )?>_<?php echo esc_attr( $item_id ) ?>_merge_var" style="width: 300px;">
				<option value="EMAIL" <?php selected( $selected_merge_var, 'EMAIL' )?> ><?php _e( 'Email', 'yith-woocommerce-mailchimp' ) ?></option>
				<?php
				if ( ! empty( $fields ) ):
					foreach ( $fields as $field_id => $field_name ):
						?>
						<option value="<?php echo esc_attr( $field_id ) ?>" <?php selected( $selected_merge_var, $field_id )?> ><?php echo $field_name ?></option>
					<?php
					endforeach;
				endif;
				?>
			</select>

			<a href="#" class="update-fields button button-secondary"><?php _e( 'Update fields', 'yith-woocommerce-mailchimp' )?></a>

			<input type="hidden" name="<?php echo esc_attr( $id )?>[<?php echo esc_attr( $item_id ) ?>][removable]" value="<?php echo ( $removable ) ? esc_attr( 'yes' ) : esc_attr( 'no' ) ?>" />
		</div>
		<?php if( $removable ): ?>
			<a href="#" class="remove-button"><?php _e( 'Remove', 'yith-woocommerce-mailchimp' ) ?></a>
		<?php endif; ?>
	</div>
</div>