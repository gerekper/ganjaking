<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>">
    <?php echo((gt3_option('responsive') == "1") ? '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">' : ''); ?>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <link rel="pingback" href="<?php esc_url(bloginfo('pingback_url')); ?>">
    <?php
        wp_head();
    ?>
</head>

<?php $gt3_ID = gt3_get_queried_object_id(); ?>

<body <?php body_class(); ?> <?php echo 'data-theme-color="'.esc_attr(gt3_option("theme-custom-color")).'"'; ?> >
    <?php
        gt3_preloader();
        gt3_get_header_builder( $gt3_ID );
        if (get_post_type() != 'gallery') {
	        gt3_get_page_title($gt3_ID);
        }
    ?>
    <div class="site_wrapper fadeOnLoad">
        <?php
            $page_shortcode = '';
            if (class_exists( 'RWMB_Loader' )) {
                $page_shortcode = rwmb_meta('mb_page_shortcode');
                if (strlen($page_shortcode) > 0) {
                    echo do_shortcode($page_shortcode);
                }
            }
        ?>
        <div class="main_wrapper">