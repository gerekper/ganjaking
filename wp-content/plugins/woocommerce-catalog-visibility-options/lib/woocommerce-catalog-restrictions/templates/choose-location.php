<?php global $woocommerce, $wc_catalog_restrictions; ?>
<?php get_header( 'shop' ); ?>

<?php do_action( 'woocommerce_before_main_content' ); ?>

<?php while ( have_posts() ) : the_post(); ?>

	<h1 class="page-title">
		<?php the_title(); ?>
	</h1>

	<?php do_action( 'woocommerce_catalog_restrictions_before_choose_location_description' ); ?>
	<?php the_content(); ?>
	<?php do_action( 'woocommerce_catalog_restrictions_after_choose_location_description' ); ?>

	<?php do_action( 'woocommerce_catalog_restrictions_before_choose_location_form' ); ?>

	<form name="choose-location" method="post">
		<?php woocommerce_catalog_restrictions_country_input( $wc_catalog_restrictions->get_location_for_current_user() ); ?>
		<br />

		<input type="hidden" value="shortcode" name="choose_location" />
		<input type="submit" name="save-location" value="<?php _e( 'Set Location', 'wc_catalog_restrictions' ); ?>" />
	</form>

	<?php do_action( 'woocommerce_catalog_restrictions_after_choose_location_form' ); ?>




<?php endwhile; // end of the loop.  ?>

<?php do_action( 'woocommerce_after_main_content' ); ?>

<?php do_action( 'woocommerce_sidebar' ); ?>

<?php get_footer( 'shop' ); ?>