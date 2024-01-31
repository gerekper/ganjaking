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

    $title_tag          = betterdocs()->customizer->defaults->get( 'betterdocs_category_title_tag' );
    $title_tag          = betterdocs()->template_helper->is_valid_tag( $title_tag );
    $enable_description = betterdocs()->customizer->defaults->get( 'betterdocs_doc_list_desc_switch_layout6' );
    $enable_image       = betterdocs()->customizer->defaults->get( 'betterdocs_doc_list_img_switch_layout6' );

    // global $wp_query;
    // dump($wp_query->query_vars);
?>

<div class="betterdocs-wrapper betterdocs-docs-archive-wrapper betterdocs-category-layout-6 betterdocs-handbook-layout betterdocs-wraper">
    <?php betterdocs()->template_helper->search();?>

    <div class="betterdocs-content-wrapper betterdocs-archive-wrap betterdocs-archive-main">
        <?php
            $_shortcode_attributes = [
                'show_term_image'  => $enable_image,
                'show_description' => $enable_description,
                'title_tag'        => $title_tag
            ];

            $attributes = betterdocs()->template_helper->shortcode_atts( $_shortcode_attributes, 'betterdocs_category_grid_list', 'layout-6' );
            echo do_shortcode( '[betterdocs_category_grid_list ' . $attributes . ']' );
        ?>
    </div>
    <?php betterdocs()->views->get( 'templates/faq' );?>
</div>

<?php
    /**
     * Footer
     */
get_footer();
