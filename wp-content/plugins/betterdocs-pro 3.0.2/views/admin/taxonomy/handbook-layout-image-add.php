<div class="form-field term-group">
	<label>
		<?php _e( 'Category Cover Image for Handbook Layout', 'betterdocs-pro' );?>
	</label>
	<input type="hidden" class="doc-category-image-id" name="term_meta[thumb-id]" value="">
	<div class="doc-category-image-wrapper betterdocs-category-thumb">
		<img width="100" src="<?php echo betterdocs_pro()->assets->icon( 'full-default.png', true ); ?>" alt="">
	</div>
	<p>
		<input
			type="button" class="button button-secondary betterdocs_tax_media_button"
			id="betterdocs_cat_thumb_button" name="betterdocs_cat_thumb_button"
			value="<?php _e( 'Add Image', 'betterdocs-pro' );?>"
		/>
		<input
			type="button" class="button button-secondary doc_tax_media_remove" id="doc_cat_thumb_remove"
			name="doc_cat_thumb_remove"
			value="<?php _e( 'Remove Image', 'betterdocs-pro' );?>"
		/>
	</p>
</div>
