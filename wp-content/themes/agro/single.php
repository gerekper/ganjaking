<?php

/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Agro
 * @since 1.0.0
 */

get_header();

// you can use this action to add any content before single page
do_action("agro_before_post_single");
$container_type = agro_settings('single_container_type', '' );
?>

<!-- Single page general div -->
<div id="nt-single" class="nt-single">

	<!-- Hero section - this function using on all inner pages -->
	<?php agro_hero_section(); ?>

	<!-- Section Post -->
	<div id="nt-single" class="nt-theme-inner-container section">
		<div class="container<?php echo esc_attr($container_type); ?>">
			<div class="row">

				<!-- Left sidebar -->
				<?php if (agro_settings('single_layout', 'right-sidebar') == 'left-sidebar') {
				    get_sidebar();
				} ?>

				<!-- Sidebar column control -->
				<div class="<?php echo agro_sidebar_control('single_layout'); ?>">


					<div class="posts nt-theme-content nt-clearfix nt-single-content">

					<?php

                        // Post featured thumbnail image
                        if (has_post_thumbnail()) {
                            the_post_thumbnail('large', array('class' => 'the-post-thumbnail'));
                        }

                        // Post formats
                        // you can add here metabox variable to add any element before content area

                        // Post content
                        while (have_posts()) : the_post();

                            /* get post content */
                            the_content();

                            /* Theme page link pagination */
                            agro_wp_link_pages();

                        endwhile;

                        echo '</div>'; // nt-theme-content

                        // Single post tags
                        agro_single_post_tags();

                        // Author box
                        agro_single_post_author_box();

                        // Post comments
                        if (comments_open() || '0' != get_comments_number()) {
                            comments_template();
                        }

                        // Post navigation
                        agro_single_navigation();

                        // Related post
                        agro_single_post_related();

                    ?>

				</div><!-- End column sidebar control -->

				<!-- Right sidebar -->
				<?php if (agro_settings('single_layout', 'right-sidebar') == 'right-sidebar') {
                    get_sidebar();
                } ?>

			</div><!-- End row -->
		</div><!-- End container -->
	</div><!-- End Section Post -->
</div><!--End single page general div -->

<?php

    // you can use this action to add any content after single page
    do_action("agro_after_post_single");

    get_footer();

?>
