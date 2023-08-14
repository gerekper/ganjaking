<div
    <?php echo $wrapper_attr; ?>>
    <?php
        $tag = betterdocs()->template_helper->is_valid_tag( $title_tag );
        echo wp_kses_post( '<' . $tag . ' class="betterdocs-popular-articles-heading">' . $title . '</' . $tag . '>' );

        $view_object->get( 'template-parts/category-list' );
    ?>
</div>
