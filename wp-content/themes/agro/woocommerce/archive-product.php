<?php

    /*
    ** WooCommerce shop/product listing page
    */

	get_header();

	do_action("agro_before_woo_shop_page");

	/**
	 * Change number or products per row to 3
	 */
	 if( '' != agro_settings( 'shop_loop_columns' ) ) {

 		add_filter('loop_shop_columns', 'agro_loop_columns', 999);
 		if (!function_exists('agro_loop_columns')) {
 			function agro_loop_columns() {
 				$agro_shop_column = agro_settings( 'shop_loop_columns', 3 );
 				return intval($agro_shop_column); // 2 products per row
 			}
 		}
 	}

    $agro_shop_layout = agro_settings( 'shop_layout' );
    $agro_shop_layout = isset($agro_shop_layout) && $agro_shop_layout == 'full-width' ? 'col-lg-12' : 'col-lg-9';


?>

<!-- Woo shop page general div -->
<div id="nt-shop-page" class="nt-shop-page">

	<!-- Hero section - this function using on all inner pages -->
	<?php agro_woo_hero_section(); ?>

	<div class="nt-theme-inner-container">
		<div class="container">
			<div class="row">


				<!-- Left sidebar -->
                <?php if( 'left-sidebar' == agro_settings( 'shop_layout' )  AND is_active_sidebar( 'shop-sidebar-1' )  ) {

					echo '<div id="nt-sidebar" class="nt-sidebar col-lg-3 col-md-4 col-sm-12"><div class="nt-sidebar-inner">';
						dynamic_sidebar( 'shop-sidebar-1' );
					echo '</div></div>';

				} ?>

				<!-- Sidebar none -->
				<!-- Sidebar none -->
					<div class="<?php echo esc_attr( $agro_shop_layout ); ?>">

						<?php echo agro_settings( 'shop_before_loop' ); ?>

						<?php woocommerce_content(); ?>

						<?php echo agro_settings( 'shop_after_loop' ); ?>

					</div>
                <!-- End sidebar + content -->

				<!-- Right sidebar -->
                <?php if( 'right-sidebar' == agro_settings( 'shop_layout' )  AND is_active_sidebar( 'shop-sidebar-1' )  ) {

					echo '<div id="nt-sidebar" class="nt-sidebar col-lg-3 col-md-4 col-sm-12"><div class="nt-sidebar-inner">';
						dynamic_sidebar( 'shop-sidebar-1' );
					echo '</div></div>';

				} ?>

			</div><!-- End row -->
		</div><!-- End container -->
	</div><!-- End #blog -->
</div><!-- End woo shop page general div -->

<?php

	do_action("agro_after_woo_shop_page");

	get_footer();

?>
