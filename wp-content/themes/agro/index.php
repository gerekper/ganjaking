<?php
/**
* The main template file
*
* This is the most generic template file in a WordPress theme
* and one of the two required files for a theme (the other being style.css).
* It is used to display a page when nothing more specific matches a query.
* E.g., it puts together the home page when no home.php file exists.
*
* @link https://developer.wordpress.org/themes/basics/template-hierarchy/
*
* @package WordPress
* @subpackage Agro
* @since 1.0.0
*/

get_header();

do_action("agro_before_index");
$index_type = agro_settings('blog_index_type', 'grid' );
$post_skin_type = agro_settings('post_skin_type', '1' );
$post_column = agro_settings('post_column', '' );
$container_type = agro_settings('blog_container_type', '' );
$index_layout = agro_settings('index_layout', 'right-sidebar' );
$index_column = is_active_sidebar( 'sidebar-1' ) && 'full-width' != $index_layout && ( '-fluid' == $container_type || '-off' == $container_type ) ? 'col-lg-10 col-md-9 col-sm-12' : agro_sidebar_control('index_layout');

?>
<!-- container -->
<div id="nt-index" class="nt-index">

    <!-- Hero section - this function using on all inner pages -->
    <?php agro_hero_section(); ?>

    <div class="nt-theme-inner-container section">
        <div class="container<?php echo esc_attr($container_type); ?>">
            <div class="row">

                <!-- left sidebar -->
                <?php
                if ( agro_settings('index_layout', 'right-sidebar' ) == 'left-sidebar' ) {
                    get_sidebar();
                }
                ?>

                <!-- Sidebar column control -->
                <div class="<?php echo esc_attr($index_column); ?>">
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
                <!-- End sidebar column control -->

                <!-- right sidebar -->
                <?php
                if ( agro_settings('index_layout', 'right-sidebar') == 'right-sidebar' ) {
                    get_sidebar();
                }
                ?>

            </div>
            <!--End row -->
        </div>
        <!--End container -->
    </div>
    <!--End #blog -->
</div>
<!--End index general div -->

<?php
if ( '1' == agro_settings( 'blog_after_content_display' ) && class_exists( 'Agro_Saved_Templates' ) ) {

    Agro_Saved_Templates::vc_print_saved_template( agro_settings('blog_after_content_saved_templates' ) );

}

// you can use this action to add any content after index page
do_action("agro_after_index");

get_footer();

?>
