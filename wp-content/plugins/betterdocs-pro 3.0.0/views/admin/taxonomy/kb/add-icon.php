<div class="form-field term-group">
	<label for="knowledge-base-image-id">
		<?php _e( 'KB Icon', 'betterdocs-pro' );?>
	</label>
	<input type="hidden" id="knowledge-base-image-id" name="term_meta[image-id]" class="custom_media_url doc-category-image-id" value="">
	<div id="knowledge-base-image-wrapper" class="doc-category-image-wrapper">
		<img src="<?php echo betterdocs()->assets->icon( 'betterdocs-cat-icon.svg', true );?>" alt="">
	</div>
	<p>
		<input
			type="button"
			class="button button-secondary betterdocs_tax_media_button" id="betterdocs_tax_media_button" name="betterdocs_tax_media_button"
			value="<?php _e( 'Add Image', 'betterdocs-pro' );?>"
		/>
		<input
			type="button"
			class="button button-secondary doc_tax_media_remove" id="doc_tax_media_remove" name="doc_tax_media_remove"
			value="<?php _e( 'Remove Image', 'betterdocs-pro' );?>"
		/>
	</p>
</div>
