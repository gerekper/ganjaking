<?php

/**
* default page template
*/

    get_header();

    // get metabox page layout option
    $agro_mb_page_layout = rwmb_meta('agro_page_layout');
    $agro_page_layout = $agro_mb_page_layout != '' ? $agro_mb_page_layout : 'full';

  // if empty metabox option set metabox page layout
    $agro_page_layout_ctrl = ($agro_page_layout == 'left-sidebar' or $agro_page_layout == 'right-sidebar') ? 'col-md-8' : 'col-md-12' ; // sidebar setting

    do_action("agro_before_page");

?>

	<div id="nt-page-container" class="nt-page-layout">

		<!-- Hero section - this function using on all inner pages -->
		<?php agro_hero_section(); ?>

		<div id="nt-page" class="nt-theme-inner-container section">
			<div class="container">
				<div class="row">

					<!-- Left sidebar -->
					<?php if ($agro_page_layout =='left-sidebar') {
                        get_sidebar();
                    } ?>

					<!-- Sidebar control column -->
					<div class="<?php echo esc_attr($agro_page_layout_ctrl) ?>">

					<?php while (have_posts()) : the_post(); ?>

						<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

							<div class="nt-theme-content nt-clearfix">
								<?php

                                    /* translators: %s: Name of current post */
                                    the_content(sprintf(
                                        esc_html__('Continue reading %s', 'agro'),
                                        the_title('<span class="screen-reader-text">', '</span>', false)
                                    ));

                                    /* theme page link pagination */
                                    agro_wp_link_pages();

                                ?>
							</div><!-- End .nt-theme-content -->

						</div><!--End article -->

						<?php

                            // If comments are open or we have at least one comment, load up the comment template.
                            if (comments_open() || get_comments_number()) {
                                comments_template();
                            }

                            // End the loop.
                            endwhile;

                        ?>

					</div>

					<!-- Right sidebar -->
					<?php if ($agro_page_layout =='right-sidebar') {
                        get_sidebar();
                    } ?>

				</div><!--End row -->
			</div><!--End container -->
		</div><!--End #blog -->
	</div><!--End page general div -->

<?php

    // you can use this action for add any content after container element
    do_action("agro_after_page");

    get_footer();

?>
