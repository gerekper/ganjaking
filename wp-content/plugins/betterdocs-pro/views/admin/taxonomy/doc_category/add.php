<div class="form-field term-group">
    <label><?php _e( 'Knowledge Base', 'betterdocs-pro' );?></label>
    <select id="doc-category-kb" class="doc-category-kb" name="doc_category_kb[]" multiple="multiple">
        <option value="" selected><?php _e( 'No Knowledge Base', 'betterdocs-pro' );?></option>';
        <?php
            foreach ( $terms as $_term ) {
                echo betterdocs()->template_helper->option_kses( $_term->slug, $_term->name, '' );
            }
        ?>
    </select>
</div>
