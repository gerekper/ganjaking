<?php
/**
 * The sidebar containing the main widget area
 *
 * @package WordPress
 * @subpackage Agro
 * @since Agro 1.0
 */

$sidebar_attr = 'col-lg-4 col-md-4 col-sm-12';

if ( is_home() or is_front_page() ) {
    $container_type = agro_settings('blog_container_type', '' );
    $sidebar_attr = is_active_sidebar( 'sidebar-1' ) &&  ( '-fluid' == $container_type || '-off' == $container_type ) ? 'col-lg-2 col-md-3 col-sm-12' : 'col-lg-4 col-md-4 col-sm-12';
}

if (  is_active_sidebar( 'sidebar-1' )  ) : ?>

	<div id="nt-sidebar" class="nt-sidebar <?php echo esc_attr( $sidebar_attr ); ?>">
		<div class="nt-sidebar-inner">
			<?php dynamic_sidebar( 'sidebar-1' ); ?>
		</div><!-- End nt-sidebar-inner -->
	</div><!-- End nt-sidebar -->

<?php endif; ?>
