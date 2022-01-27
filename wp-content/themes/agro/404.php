<?php

    /**
    * The template for displaying 404 pages (not found)
    *
    * @link https://codex.wordpress.org/Creating_an_Error_404_Page
    *
    * @package WordPress
    * @subpackage Agro
    * @since 1.0.0
    */

    get_header();

    // you can use this action for add any content before container element
    do_action("agro_before_404");

    $content_image = get_template_directory().'/images/404.jpg';
    $content_image = file_exists($content_image) ? get_template_directory_uri().'/images/404.jpg' : '';

    $content_image2 = agro_settings( 'error_content_image' );

    if ( 'custom-page' == agro_settings( 'error_page_type' ) ) :
        agro_vc_inject_shortcode_css( agro_settings( 'error_select_page_type' ) );

        $content = get_post_field('post_content', agro_settings( 'error_select_page_type' ));

        echo do_shortcode( $content );

    else:

?>

    <!-- container -->
    <div id="nt-404" class="nt-404 error">

    	<!-- Hero section - this function using on all inner pages -->
    	<?php agro_hero_section(); ?>

        <div class="nt-theme-inner-container section">
            <div class="container">
                <div class="row">

                    <!-- left sidebar -->
                    <?php if ('left-sidebar' == agro_settings( 'error_layout', 'full-width' )) {
                        get_sidebar();
                    } ?>

                    <!-- content area -->
                    <div class="text-center <?php echo agro_sidebar_control('error_layout'); ?>">

                        <?php if ( 'custom' == agro_settings( 'error_page_content_type', 'default') ) : ?>

                            <?php echo wp_kses(agro_settings( 'error_page_custom_content' ), agro_allowed_html()); ?>

                        <?php else: ?>

                            <?php if ( '0' != agro_settings( 'error_content_image_visibility', '1' ) ) : ?>

                                <div class="mb-9">
                                    <?php if ( is_array($content_image2) && !empty( $content_image2 ) ) : ?>
                                        <img class="img-fluid" src="<?php echo esc_url($content_image2['url']); ?>" alt="<?php esc_attr_e('404', 'agro'); ?>" />
                                    <?php else: ?>
                                        <?php if ( $content_image ) : ?>
                                            <img class="img-fluid" src="<?php echo esc_url($content_image); ?>" alt="<?php esc_attr_e('404', 'agro'); ?>" />
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="__text">

                                <?php if ( '0' != agro_settings( 'error_content_title_visibility', '1' ) ) : ?>
                                    <?php if ( '' != agro_settings( 'error_content_title' ) ) : ?>
                                        <div class="error-content-title"><?php echo wp_kses(agro_settings( 'error_content_title' ), agro_allowed_html()); ?></div>
                                    <?php else: ?>
                                        <h3 class="error-content-title"><?php esc_html_e('Oops!', 'agro'); ?> <span><?php esc_html_e('That page can’t be found.', 'agro'); ?></span></h3>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ( '0' != agro_settings( 'error_content_desc_visibility', '1' ) ) : ?>
                                    <?php if ( '' != agro_settings( 'error_content_desc' ) ) : ?>
                                        <div class="error-content-desc"><?php echo wp_kses(agro_settings( 'error_content_desc' ), agro_allowed_html()); ?></div>
                                    <?php else: ?>
                                        <p class="error-content-desc"><strong><?php esc_html_e('It looks like nothing was found at this location. Maybe try a search?', 'agro'); ?></strong></p>
                                    <?php endif; ?>
                                <?php endif; ?>

                            </div>
                            <?php
                                if ( '0' != agro_settings( 'error_content_search_visibility', '1' ) ) {
                                    get_search_form();
                                }
                            ?>
                        <?php endif; ?>

                    </div>
                    <!-- End column control -->

                    <!-- Right sidebar -->
                    <?php
                    if ( 'right-sidebar' == agro_settings( 'error_layout', 'full-width' ) )  {
                        get_sidebar();
                    }
                    ?>

                </div>
                <!-- End row -->
            </div>
            <!-- End container -->
        </div>
        <!-- End div #blog-post -->
    </div>
    <!-- End 404 page general div -->
<?php endif; ?>
<?php

    // use this action to add any content after 404 page container element
    do_action("agro_after_404");

    get_footer();

?>
