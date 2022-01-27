<?php
get_header();

$layout = gt3_option('page_sidebar_layout');
$sidebar = gt3_option('page_sidebar_def');
$column = 12;

if ( ($layout == 'left' || $layout == 'right') && is_active_sidebar( $sidebar )  ) {
    $column = 9;
}else{
    $sidebar = '';
}
$row_class = ' sidebar_'.esc_attr($layout);

global $wp_query;

?>
    <div class="container">
        <div class="row<?php echo esc_attr($row_class); ?>">
            <div class="content-container span<?php echo (int)$column; ?>">
                <section id='main_content'>
                    <?php

                    global $paged, $offset, $posts_per_page;

                    $offset = 0;
                    $posts_per_page = 10;
                    $foundSomething = false;

                    $defaults = array('numberposts' => 10, 'offset' => 0, 'post_type' => 'any', 'post_status' => 'publish', 'post_password' => '', 'suppress_filters' => false, 's' => get_search_query(), 'paged' => $paged);
                    $query = http_build_query($defaults);
                    $posts = get_posts($query);
                    foreach ($posts as $post) {
                        setup_postdata($post);
                        ?>
                        <div class="blog_post_preview">
                            <div class="blog_content">
                                <h3 class="blogpost_title"><a href="<?php echo esc_url(get_permalink()); ?>"><?php esc_html(the_title()); ?></a></h3>
                                <div class="listing_meta">
                                    <span class="post_date"><?php echo esc_html(get_the_time(get_option('date_format'))); ?></span>
                                    <span class="post_author"><?php echo esc_html__('by', 'agrosector'); ?> <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php echo esc_html(get_the_author_meta('display_name')); ?></a></span>
                                </div>
                            </div>
                        </div>
                        <?php

                        $foundSomething = true;
                    }
                    wp_reset_postdata();

                    if ($foundSomething == false) {
                        ?>
                        <div class="wrapper_404 height_100percent pp_block">
                            <div class="container_vertical_wrapper">
                                <div class="container a-center pp_container">
                                    <h2><?php echo esc_html__('Oops!', 'agrosector'); ?> <?php echo esc_html__('Not Found!', 'agrosector'); ?></h2>
                                    <p class="no_found_content"><?php echo esc_html__('Apologies, but we were unable to find what you were looking for.', 'agrosector'); ?></p>
                                    <div class="search_result_form text-center">
                                        <?php get_search_form(true); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }

                    echo gt3_get_theme_pagination();

                    ?>
                </section>
            </div>
            <?php
            if ($layout == 'left' || $layout == 'right') {
                echo '<div class="sidebar-container span'.(12 - (int)$column).'">';
                if (is_active_sidebar( $sidebar )) {
                    echo "<aside class='sidebar'>";
                    dynamic_sidebar( $sidebar );
                    echo "</aside>";
                }
                echo "</div>";
            }
            ?>
        </div>

    </div>

<?php get_footer(); ?>
