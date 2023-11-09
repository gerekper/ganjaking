
<?php
if ($manage_docs_terms) {
    ?>
    <div class="form-field term-group">
        <label><?php _e('Knowledge Base', 'betterdocs-pro') ?></label>
        <select id="doc-category-kb" class="doc-category-kb" name="doc_category_kb[]" multiple="multiple">
            <option value="" selected><?php _e('No Knowledge Base', 'betterdocs-pro') ?></option>
            <?php foreach ($manage_docs_terms as $term) {
                echo '<option value="' . esc_attr($term->slug) . '">' . $term->name . '</option>';
            } ?>
        </select>
    </div>
<?php
}