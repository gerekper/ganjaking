<?php
    /**
     * Template Archive Docs
     * Layout 4
     *
     * @link       https://wpdeveloper.com
     * @since      1.0.0
     *
     * @package    WPDeveloper/BetterDocs
     * @subpackage BetterDocs/public
     */

    get_header();

    $terms_orderby = betterdocs()->settings->get( 'terms_orderby' );
    $terms_order   = betterdocs()->settings->get( 'terms_order' );
    $order_term    = betterdocs()->settings->get( 'alphabetically_order_term', false );
    $title_tag     = betterdocs()->customizer->defaults->get( 'betterdocs_category_title_tag' );
    $title_tag     = betterdocs()->template_helper->is_valid_tag( $title_tag );
    $popular_docs  = betterdocs()->customizer->defaults->get( 'betterdocs_docs_page_popular_docs_switch' );

    if ( $order_term ) {
        $terms_orderby = 'name';
    }
?>

<div class="betterdocs-wrapper betterdocs-docs-archive-wrapper betterdocs-category-layout-5 betterdocs-classic-layout betterdocs-wraper">
    <?php betterdocs()->template_helper->search();?>

    <div class="betterdocs-content-wrapper betterdocs-archive-wrap betterdocs-archive-main">
        <div class="betterdocs-display-flex">
            <div class="betterdocs-article-list-wrapper">
                <?php
                    $_shortcode_attributes = [
                        'title_tag'     => $title_tag,
                        'terms_order'   => $terms_order,
                        'terms_orderby' => esc_html( $terms_orderby ),
                        'show_icon'     => betterdocs()->customizer->defaults->get( 'betterdocs_doc_page_show_category_icon' )
                    ];

                    if ( is_tax( 'knowledge_base' ) ) {
                        $_shortcode_attributes['multiple_knowledge_base'] = true;
                    }

                    $attributes = betterdocs()->template_helper->shortcode_atts( $_shortcode_attributes, 'betterdocs_list_view', 'layout-5' );
                    echo do_shortcode( '[betterdocs_list_view ' . $attributes . ']' );
                ?>
            </div>
            <?php
                if ( $popular_docs ) {
                    $popular_doc_text       = betterdocs()->settings->get( 'betterdocs_popular_docs_text', __( 'Popular Docs', 'betterdocs-pro' ) );
                    $popular_posts_per_page = betterdocs()->settings->get( 'betterdocs_popular_docs_number', 10 );

                    echo '<div class="betterdocs-popular-article-list-wrapper">';
                    echo do_shortcode( '[betterdocs_popular_articles title="' . $popular_doc_text . '" post_per_page="' . $popular_posts_per_page . '"]' );
                    echo '</div>';
                }
            ?>
        </div>
    </div>
    <?php betterdocs()->views->get( 'templates/faq' );?>
</div>

<?php
    /**
     * Footer
     */
get_footer();
