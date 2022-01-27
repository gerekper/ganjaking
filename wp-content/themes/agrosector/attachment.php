<?php get_header();
the_post();

$layout = gt3_option('page_sidebar_layout');
$sidebar = gt3_option('page_sidebar_def');
$column = 12;

if ( ($layout == 'left' || $layout == 'right') && is_active_sidebar( $sidebar )  ) {
    $column = 9;
}else{
    $sidebar = '';
}
$row_class = ' sidebar_'.$layout;

$attachment_image_src = wp_get_attachment_url(get_the_ID(), "full");

?>

    <div class="container">
        <div class="row<?php echo esc_attr($row_class); ?>">
            <div class="content-container span<?php echo (int)esc_attr($column); ?>">
                <section id='main_content'>
                    <?php if (isset($attachment_image_src[1]) && $attachment_image_src[1] > 0) { ?>
                        <img src="<?php echo esc_url($attachment_image_src[0]); ?>" alt="<?php echo esc_attr__('attachment image', 'agrosector'); ?>"/>
                    <?php } ?>
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