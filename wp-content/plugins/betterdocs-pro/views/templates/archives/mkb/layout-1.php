<?php
    /**
     * Template archive docs
     *
     * @link       https://wpdeveloper.com
     * @since      1.0.0
     *
     * @package    WPDeveloper/BetterDocs
     * @subpackage BetterDocs/public
     */

    get_header();

    $title_tag     = betterdocs()->customizer->defaults->get( 'betterdocs_mkb_title_tag' );
    $title_tag     = betterdocs()->template_helper->is_valid_tag( $title_tag );
?>

<div class="betterdocs-wrapper betterdocs-mkb-wrapper betterdocs-mkb-layout-1 betterdocs-box-layout betterdocs-wraper">
    <?php betterdocs()->template_helper->search();?>

    <div class="betterdocs-content-wrapper betterdocs-archive-wrap betterdocs-archive-main">
        <?php
            $attributes = betterdocs()->template_helper->shortcode_atts( [
                'title_tag'     => $title_tag,
                'show_icon'     =>  betterdocs()->customizer->defaults->get('betterdocs_mkb_page_show_category_icon')
            ], 'betterdocs_multiple_kb', 'layout-1' );

            echo do_shortcode( '[betterdocs_multiple_kb ' . $attributes . ']' );
        ?>
    </div>
    <?php betterdocs()->views->get( 'templates/faq-mkb' );?>
</div>

<?php
    /**
     * Footer
     */
get_footer();
