<?php get_header();

global $porto_settings;
?>

<div id="content" class="no-content">
	<div class="container">
		<section class="page-not-found">
			<div class="row">
				<div class="col-lg-6 offset-lg-1">
					<div class="page-not-found-main">
						<h2 class="entry-title"><?php esc_html_e( '404', 'porto' ); ?> <i class="fas fa-file"></i></h2>
						<p><?php _e( "We're sorry, but the page you were looking for doesn't exist.", 'porto' ); ?></p>
					</div>
				</div>
				<?php if ( $porto_settings['error-block'] ) : ?>
					<div class="col-lg-4">
						<?php echo do_shortcode( '[porto_block name="' . $porto_settings['error-block'] . '"]' ); ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
	</div>
</div>

<?php get_footer(); ?>
