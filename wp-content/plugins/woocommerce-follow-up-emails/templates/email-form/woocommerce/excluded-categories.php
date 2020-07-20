<div class="options_group storewide non-signup hideable <?php do_action( 'fue_form_excluded_category_tr_class', $email ); ?> excluded_category_tr">

	<p class="form-field">
		<label for="excluded_category_ids"><?php esc_html_e( 'Exclude these categories', 'follow_up_emails' ); ?></label>

		<input type="hidden" name="meta[excluded_categories]" value="" />
		<select id="excluded_category_ids" name="meta[excluded_categories][]" class="select2" data-placeholder="<?php esc_attr_e( 'Select categories&hellip;', 'follow_up_emails' ); ?>"  multiple style="width:100%;">
		<?php
			$excluded = isset( $email->meta['excluded_categories'] ) ? $email->meta['excluded_categories'] : array();

			if ( ! is_array( $excluded ) ) {
				$excluded = array();
			}

			foreach ( $categories as $category ) :
		?>
			<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( in_array( $category->term_id, (array) $excluded ), true ); ?>><?php echo esc_html( $category->name ); ?></option>
		<?php endforeach; ?>
		</select>
	</p>

</div>
