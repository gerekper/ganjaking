<?php

    /**
     * Template archive docs
     *
     * @link       https://wpdeveloper.com
     * @since      1.0.0
     *
     * @package    BetterDocs
     * @subpackage BetterDocs/public
     */

    get_header();

    $view_object            = betterdocs()->views;
    $layout                 = betterdocs()->customizer->defaults->get( 'betterdocs_archive_layout_select', 'layout-1' );
    $title_tag              = betterdocs()->customizer->defaults->get( 'betterdocs_archive_title_tag', 'h2' );
    $title_tag              = betterdocs()->template_helper->is_valid_tag( $title_tag );
    $related_categories     = betterdocs()->customizer->defaults->get( 'betterdocs_archive_other_categories_heading_text', __( 'Related Categories', 'betterdocs-pro' ) );
    $related_categories_btn = betterdocs()->customizer->defaults->get( 'betterdocs_archive_other_categories_load_more_text', __( 'Load More', 'betterdocs-pro' ) );

    $content_area_classes = [
        'betterdocs-content-wrapper betterdocs-display-flex doc-category-layout-6'
    ];

    $current_category = get_queried_object();
?>

<div class="betterdocs-wrapper betterdocs-taxonomy-wrapper layout-6 betterdocs-handbook-layout betterdocs-wraper">
    <?php betterdocs()->template_helper->search();?>

    <div class="<?php esc_attr_e( implode( ' ', $content_area_classes ) );?>">
        <div id="main" class="betterdocs-content-area">
            <div id="main" class="betterdocs-content-inner-area">
                <div id="main" class="betterdocs-category-heading">
                    <div class="betterdocs-category-info">
                        <div class="betterdocs-category-image">
                            <?php
                                betterdocs()->views->get( 'template-parts/category-image', [
                                    'term'            => $current_category,
                                    'image_size'      => 'full',
                                    'show_term_image' => true
                                ] );
                            ?>
                        </div>
                        <div class="betterdocs-entry-title">
                            <div class="betterdocs-category-title-counts">
                                <?php
                                    echo wp_sprintf(
                                        '<%1$s class="betterdocs-entry-heading">%2$s</%1$s>',
                                        $title_tag,
                                        $current_category->name
                                    );
                                ?>
                                <div class="betterdocs-category-items-counts">
                                    <span>
                                        <?php
                                            echo betterdocs()->query->get_docs_count(
                                                $current_category,
                                                betterdocs()->settings->get( 'nested_subcategory' ),
                                                [
                                                    'multiple_knowledge_base' => betterdocs()->settings->get( 'multiple_kb' )
                                                ]
                                            );
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <?php
                                /**
                                 * Breadcrumbs
                                 */
                                $view_object->get( 'templates/parts/breadcrumbs' );
                            ?>
                        </div>
                    </div>
                    <?php
                        betterdocs()->views->get( 'template-parts/category-description', [
                            'show_description' => true,
                            'description'      => $current_category->description
                        ] );
                    ?>
                </div>

                <div class="betterdocs-entry-body betterdocs-taxonomy-doc-category">
                    <div class="layout-6 betterdocs-category-grid-list-inner-wrapper">
                        <div class="betterdocs-single-category-inner">
                            <?php
                                $args = betterdocs()->query->docs_query_args( [
                                    'term_id'        => $current_category->term_id,
                                    'term_slug'      => $current_category->slug,
                                    'posts_per_page' => -1
                                ] );

                                $post_query = new WP_Query( $args );

                                if ( $post_query->have_posts() ):
                            ?>
                            <div class="betterdocs-body">
                                <ul>
                                    <?php
                                        while ( $post_query->have_posts() ): $post_query->the_post();
                                            echo wp_sprintf(
                                                '<li><a href="%s"><p>%s</p> %s</a><p>%s</p></li>',
                                                esc_attr( esc_url( get_the_permalink() ) ),
                                                betterdocs()->template_helper->kses( get_the_title() ),
                                                betterdocs()->template_helper->icon( 'arrow-right', false ),
                                                wp_trim_words( get_the_content(), 20 )
                                            );
                                        endwhile;
                                        wp_reset_query();
                                    ?>
                                </ul>
                                <?php
                                    endif; // $post_query->have_posts()

                                    echo do_shortcode( '[betterdocs_related_categories heading="' . $related_categories . '" load_more_text="' . $related_categories_btn . '"]' );
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
