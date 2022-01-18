<div class="options_group storewide non-signup hideable <?php do_action('fue_form_downloadables_class', $email); ?> downloadables_div">

	<p class="form-field">
		<label for="downloadable_file"><?php esc_html_e('Downloadable Files', 'follow_up_emails'); ?></label>

		<?php
		$files = array();
		$selected = !empty( $email->meta['downloadable_file'] ) ? $email->meta['downloadable_file'] : '';

		if ( $email->product_id ) {
			$product = WC_FUE_Compatibility::wc_get_product( $email->product_id );
			$downloadables = ( $product ) ? $product->get_downloads() : array();

			if ( !empty( $downloadables ) ) {
				foreach ( $downloadables as $key => $file ) {
					$files[ $key ] = $file['name'] .' ('. basename($file['file']) .')';
				}
			}
		}
		?>
		<select id="downloadable_file" name="meta[downloadable_file]" class="select2" data-placeholder="<?php esc_attr_e( 'Select file&hellip;', 'follow_up_emails' ); ?>" style="width:100%;">
			<option></option>
			<?php foreach ( $files as $key => $file ) : ?>
			<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $selected, $key ); ?>><?php echo esc_html( $file ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>

</div>
