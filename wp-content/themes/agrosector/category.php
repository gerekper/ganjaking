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
$row_class = ' sidebar_'.$layout;
?>
    <div class="container">
        <div class="row<?php echo esc_attr($row_class); ?>">
            <div class="content-container span<?php echo (int)esc_attr($column); ?>">
                <section id='main_content'>
                    <?php
                    while (have_posts()) : the_post();
                        get_template_part("bloglisting");
                    endwhile;
                    wp_reset_postdata();
                    echo gt3_get_theme_pagination();
                    ?>
                </section>
            </div>
            <?php
            if ($layout == 'left' || $layout == 'right') {
                echo '<div class="sidebar-container span'.(12 - (int)esc_attr($column)).'">';
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