<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package WordPress
 * @subpackage Agro
 * @since 1.0.0
 */

    get_header();
$index_type = agro_settings('blog_index_type', 'grid' );
$post_skin_type = agro_settings('post_skin_type', '1' );
    // you can use this action for add any content before container element
    do_action("agro_before_search");

?>
<!-- Search page general div -->
<div id="nt-search" class="nt-search">

	<!-- Hero section - this function using on all inner pages -->
	<?php agro_hero_section(); ?>

	<div class="nt-theme-inner-container section">
		<div class="container">
			<div class="row">

				<!-- Left sidebar -->
				<?php if (agro_settings('search_layout', 'right-sidebar') == 'left-sidebar') {
                    if (have_posts()) {
                        get_sidebar();
                    }
                } ?>

				<!-- Sidebar none -->
				<div class="<?php echo agro_sidebar_control('search_layout'); ?>">

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

				</div><!-- End sidebar + content -->

				<!-- Right sidebar -->
				<?php if (agro_settings('search_layout', 'right-sidebar') == 'right-sidebar') {
                    if (have_posts()) {
                        get_sidebar();
                    }
                } ?>

			</div><!-- End row -->
		</div><!-- End container -->
	</div><!-- End #blog-post -->
</div><!--End search page general div -->

<?php

    // you can use this action to add any content after search page
    do_action("agro_after_search");

    get_footer();

?>
