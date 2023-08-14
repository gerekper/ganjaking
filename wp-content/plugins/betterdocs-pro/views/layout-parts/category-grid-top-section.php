<div class="betterdocs-grid-top-row-wrapper">
    <?php
        $_shortcode_attributes = [
            'terms'                   => $term_ids,
            'column'                  => $column,
            'title_tag'               => $title_tag,
            'terms_order'             => $terms_order,
            'terms_orderby'           => esc_html( $terms_orderby ),
            'multiple_knowledge_base' => $multiple_knowledge_base
        ];

        $attributes = betterdocs()->template_helper->get_html_attributes( $_shortcode_attributes );
        echo do_shortcode( '[betterdocs_category_box_2 ' . $attributes . ']' );
    ?>
</div>


