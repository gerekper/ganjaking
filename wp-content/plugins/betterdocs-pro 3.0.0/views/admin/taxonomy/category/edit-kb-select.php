<?php
if ($manage_docs_terms) { ?>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label><?php _e('Knowledge Base', 'betterdocs-pro') ?></label>
        </th>
        <td>
            <select id="doc-category-kb" class="doc-category-kb" name="doc_category_kb[]" multiple="multiple">
                <option value=""><?php _e('No Knowledge Base', 'betterdocs-pro') ?></option>
                <?php
                foreach ($manage_docs_terms as $term) {
                    $selected = (is_array($knowledge_base) && in_array($term->slug, $knowledge_base)) ? ' selected' : '';
                    echo '<option value="' . esc_attr($term->slug) . '"' . $selected . '>' . $term->name . '</option>';
                }
                ?>
            </select>
        </td>
    </tr>
<?php
}