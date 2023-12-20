<?php do_action( 'betterdocs_knowledge_base_update_form_before', $term ); ?>

<tr class="form-field term-group-wrap batterdocs-cat-media-upload">
	<th scope="row">
		<label for="knowledge-base-image-id">
			<?php __( 'KB Icon', 'betterdocs-pro' );?>
		</label>
	</th>
	<td>
		<input class="doc-category-image-id" type="hidden" id="knowledge-base-image-id" name="term_meta[image-id]" value="<?php esc_attr_e( $icon_id )?>" />
		<div id="knowledge-base-image-wrapper" class="doc-category-image-wrapper">
			<?php
                if ( $icon_id ) {
                    echo '<img style="display: none;" src="' . betterdocs()->assets->icon( 'betterdocs-cat-icon.svg', true ) . '" alt="">';
                    echo wp_get_attachment_image( $icon_id, 'thumbnail', false, 'class=custom_media_image' );
                } else {
                    echo '<img src="'. betterdocs()->assets->icon('betterdocs-cat-icon.svg', true) .'" alt="">';
                }
            ?>
		</div>
		<p>
			<input
				type="button"
				class="button button-secondary betterdocs_tax_media_button" id="betterdocs_tax_media_button"
				name="betterdocs_tax_media_button"
				value="<?php _e('Add Image', 'betterdocs-pro'); ?>" />
			<input
				type="button"
				class="button button-secondary doc_tax_media_remove" id="doc_tax_media_remove"
				name="doc_tax_media_remove"
				value="<?php _e('Remove Image', 'betterdocs-pro'); ?>"
			/>
		</p>
	</td>
</tr>
