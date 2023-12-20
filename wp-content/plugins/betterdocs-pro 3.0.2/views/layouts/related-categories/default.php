<div class="betterdocs-single-related-category">
    <div class="betterdocs-single-related-category-inner">
        <?php $view_object->get( 'template-parts/category-image' );?>

        <div class="betterdocs-category-header">
            <?php
                $view_object->get( 'template-parts/category-title', [
                    'title' => $term->name,
                    'tag' => $title_tag
                ] );
                $view_object->get( 'template-parts/category-counter', [ 'show_count' => true, 'counts' => $counts ] );
            ?>
        </div>

        <?php
            if ( $term->description ) {
                $view_object->get( 'template-parts/category-description', [
                    'show_description' => true,
                    'description'      => wp_trim_words( $term->description, 10 )
                ] );
            }
        ?>
    </div>
</div>
