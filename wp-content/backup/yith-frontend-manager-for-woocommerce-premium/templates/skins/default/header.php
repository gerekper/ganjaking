<?php
/*
YITH FRONTEND DASHBOARD SKIN DEFAULT HEADER
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="yith_wcfm-header" class="left-logo">
	<div class="yith_wcfm-container">
		<div class="yith_wcfm-header-content">
			<?php
			$blog_title = get_bloginfo( 'name' );
			$blog_link  = get_bloginfo( 'url' );
			?>
			<div class="yith_wcfm-site-name">
				<a href="<?php echo $blog_link; ?>"><?php echo $blog_title; ?></a>
			</div>
			<div class="yith_wcfm-widget-area">
				<?php dynamic_sidebar( 'yith_wcfm_header_sidebar' ); ?>
			</div>

		</div>

	</div>
</div>