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

    if ( $order_term ) {
        $terms_orderby = 'name';
    }
?>

<div class="betterdocs-wrapper betterdocs-mkb-wrapper betterdocs-mkb-layout-4 betterdocs-tabbed-layout betterdocs-wraper">
    <?php betterdocs()->template_helper->search();?>

    <div class="betterdocs-content-wrapper betterdocs-archive-wrap betterdocs-archive-main">
        <?php
            $_shortcode_attributes = [
                'terms_order'   => $terms_order,
                'terms_orderby' => $terms_orderby,
                'show_icon'     => betterdocs()->customizer->defaults->get( 'betterdocs_mkb_page_show_category_icon' )
            ];

            $attributes = betterdocs()->template_helper->shortcode_atts( $_shortcode_attributes, 'betterdocs_multiple_kb_tab_grid', 'layout-4' );
            echo do_shortcode( '[betterdocs_multiple_kb_tab_grid ' . $attributes . ']' );
        ?>
    </div>
    <?php betterdocs()->views->get( 'templates/faq-mkb' );?>
</div>

<?php
    /**
     * Footer
     */
get_footer();
