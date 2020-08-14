<?php
/*
YITH FRONTEND DASHBOARD SKIN DEFAULT HEADER
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<body <?php body_class(); ?>>
<div id="yith_wcfm-header" class="left-logo">
    <div class="yith_wcfm-container">
        <div class="yith_wcfm-header-content">
            <div id="yith_wcfm-nav-toggle">
                <a href="#" aria-expanded="false">
                    <span class="screen-reader-text">Frontend Dashboard Menu</span>
                </a>
            </div>
			<?php
			$blog_title = get_bloginfo( 'name' );
			$blog_link  = get_bloginfo( 'url' );
			?>
            <div class="yith_wcfm-site-name">
                <a href="<?php echo $blog_link; ?>">
                    <?php echo apply_filters( 'yith_wcfm_skin_1_header_blog_title', $blog_title ); ?>
                </a>
            </div>
            <?php do_action('yith_wcfm_after_site_name'); ?>

            <div class="yith_wcfm-widget-area">
		        <?php dynamic_sidebar( 'yith_wcfm_header_sidebar' ); ?>
            </div>

        </div>

    </div>
</div>