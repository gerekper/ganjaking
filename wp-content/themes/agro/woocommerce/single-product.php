<?php

    /*
    ** WooCommerce product page
    */

	get_header();

	do_action("agro_before_woo_single");

?>

<!-- WooCommerce product page container -->
<div id="nt-woo-single" class="nt-woo-single">

	<!-- Hero section - this function using on all inner pages -->
	<?php agro_woo_single_hero_section(); ?>

	<div class="nt-theme-inner-container">
        <?php echo agro_settings( 'shop_single_before_content' ); ?>
		<div class="container">
			<div class="row">

				<!-- Left sidebar -->
                <?php if( 'left-sidebar' == agro_settings( 'shop_single_layout' ) AND is_active_sidebar( 'product-sidebar-1' )  ) {

					echo '<div id="nt-sidebar" class="nt-sidebar col-lg-4 col-md-4 col-sm-12"><div class="nt-sidebar-inner">';
						dynamic_sidebar( 'product-sidebar-1' );
					echo '</div></div>';

				} ?>

				<!-- Sidebar none -->
                <div class="<?php echo agro_sidebar_control( 'shop_single_layout' ); ?>">

                    <?php woocommerce_content(); ?>

				</div>
                <!-- End sidebar + content -->

				<!-- Right sidebar -->
                <?php if( 'right-sidebar' == agro_settings( 'shop_single_layout' ) AND is_active_sidebar( 'product-sidebar-1' )  ) {

					echo '<div id="nt-sidebar" class="nt-sidebar col-lg-4 col-md-4 col-sm-12"><div class="nt-sidebar-inner">';
						dynamic_sidebar( 'product-sidebar-1' );
					echo '</div></div>';

				} ?>

			</div><!-- End row -->
		</div><!-- End #container -->
        <?php echo agro_settings( 'shop_single_after_content' ); ?>
	</div><!-- End #blog -->
</div><!-- End woo shop page general div -->

<?php

	do_action("agro_after_woo_single");

	get_footer();

?>
