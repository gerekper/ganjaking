<tr class="form-field term-group-wrap">
    <th scope="row">
        <label><?php _e( 'Knowledge Base', 'betterdocs-pro' );?></label>
    </th>
    <td>
        <select id="doc-category-kb" class="doc-category-kb" name="doc_category_kb[]" multiple="multiple">
            <option value="" selected><?php _e( 'No Knowledge Base', 'betterdocs-pro' );?></option>';
            <?php
                foreach ( $terms as $_term ) {
                    $_selected_slug = ( is_array( $knowledge_base ) && in_array( $_term->slug, $knowledge_base ) ) ? $_term->slug : '';
                    echo betterdocs()->template_helper->option_kses( $_term->slug, $_term->name, $_selected_slug );
                }
            ?>
        </select>
    </td>
</tr>
