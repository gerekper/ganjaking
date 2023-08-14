<?php
    /**
     * The template for single doc page
     *
     * @author WPDeveloper
     * @package Documentation/SinglePage
     */

    get_header();

    $view_object             = betterdocs()->views;
    $enable_toc              = betterdocs()->settings->get( 'enable_toc' );
    $collapsible_toc_mobile  = betterdocs()->settings->get( 'collapsible_toc_mobile' );
    $enable_sidebar_cat_list = betterdocs()->settings->get( 'enable_sidebar_cat_list' );

    $wrapper_class = ['betterdocs-content-wrapper betterdocs-display-flex'];

    if ( $enable_sidebar_cat_list == 1 && $enable_toc == 1 ) {
        $wrapper_class[] = 'grid-col-3 sidebar-toc-enable';
    } elseif ( $enable_sidebar_cat_list == 1 ) {
        $wrapper_class[] = 'grid-col-2 sidebar-enable';
    } elseif ( $enable_toc == 1 ) {
        $wrapper_class[] = 'grid-col-2 toc-enable';
    } elseif ( ! $enable_sidebar_cat_list && ! $enable_toc ) {
        $wrapper_class[] = 'grid-col-1 content-enable';
    }

?>
<div class="betterdocs-wrapper betterdocs-fluid-wrapper betterdocs-single-wrapper betterdocs-single-layout-2 betterdocs-single-minimalist-layout betterdocs-single-wraper">
    <?php betterdocs()->template_helper->search();?>
    <div class="<?php echo implode( ' ', $wrapper_class ); ?>">
        <?php $view_object->get( 'templates/sidebars/sidebar-2' );?>
        <div id="betterdocs-single-main" class="betterdocs-content-area docs-content-full-main">
            <div class="betterdocs-content-inner-area">
                <?php
                    while ( have_posts() ): the_post();
                        /**
                         * Headers
                         */
                        $view_object->get( 'templates/headers/layout-4' );
                        $view_object->get( 'templates/contents/layout-2' );
                        $view_object->get( 'templates/footer' );
                    endwhile;
                    $view_object->get( 'templates/parts/navigation' );
                    $view_object->get( 'templates/parts/credit' );
                    $view_object->get( 'templates/parts/comment' );
                ?>
            </div>
        </div> <!-- #main -->
        <?php
            if ( $enable_toc ) {
                $view_object->get( 'templates/sidebars/sidebar-right' );
            }
        ?>
    </div> <!-- #primary -->
</div> <!-- .betterdocs-single-wraper -->
<?php
get_footer();
