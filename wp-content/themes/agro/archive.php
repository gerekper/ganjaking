<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Agro
 * @since 1.0.0
 */

    get_header();

$index_type = agro_settings('blog_index_type', 'grid' );
$post_skin_type = agro_settings('post_skin_type', '1' );

    do_action("agro_before_archive");

?>

	<!-- container -->
	<div id="nt-archive" class="nt-archive" >

		<!-- Hero section - this function using on all inner pages -->
		<?php agro_hero_section(); ?>

		<div class="nt-theme-inner-container section">
			<div class="container">
				<div class="row">

					<!-- left sidebar -->
					<?php if ('left-sidebar' == agro_settings('archive_layout', 'right-sidebar')) {
                        get_sidebar();
                    } ?>

					<!-- Sidebar column control -->
					<div class="<?php echo agro_sidebar_control('archive_layout'); ?>">

                    <?php

                    if (have_posts()) :

                        // masonry type
                        if( 'masonry' == $index_type ) {
                            echo '<div class="row">';
                            echo '<div id="masonry-container">';
                        }

                        // grid type
                        if( 'grid' == $index_type ) {
                            echo '<div class="row">';
                        }

                        while (have_posts()) : the_post();
                        // if there are posts, run agro_post function
                        // contain supported post formats from theme

                        if ( '3' == $post_skin_type ) {

                            agro_post_three();

                        } elseif ( '2' == $post_skin_type ) {

                            agro_post_two();

                        } else {

                            agro_post();
                        }

                    endwhile;

                    // masonry type container end
                    if( 'masonry' == $index_type ) {
                        echo '</div>';
                        echo '</div>';
                    }

                    // grid type
                    if( 'grid' == $index_type ) {
                        echo '</div>';
                    }

                    echo '<div class="u-space"></div>';

                    // this function working with wp reading settins + posts
                    agro_index_loop_pagination();

                    else :
                        // if there are no posts, read content none function
                        agro_content_none();

                    endif;

                    ?>
					</div>
                    <!-- End column control -->

					<!-- Right sidebar -->
					<?php if ('right-sidebar' == agro_settings('archive_layout', 'right-sidebar')) {
                        get_sidebar();
                    } ?>

				</div>
                <!-- End row -->
			</div>
            <!-- End container -->
		</div>
        <!-- End div #blog-post -->
	</div>
    <!-- End archive page general div-->

<?php

    // use this action to add any content after archive page container element
    do_action("agro_after_archive");

    get_footer();

?>
