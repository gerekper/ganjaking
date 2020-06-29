<?php
global $global_tax, $porto_settings, $porto_layout, $post, $porto_portfolio_columns, $porto_portfolio_view, $porto_portfolio_thumb, $porto_portfolio_thumb_bg, $porto_portfolio_thumb_image, $porto_portfolio_ajax_load, $porto_portfolio_ajax_modal;

wp_enqueue_script( 'skrollr' );
?>
<a href="<?php echo esc_url( get_site_url() . '/portfolio_cat/' . $global_tax['slug'] ); ?>">
	<section class="portfolio-parallax parallax section section-text-light section-parallax m-none" data-plugin-parallax data-plugin-options='{"speed": 1.5}' data-image-src="<?php echo esc_url( $global_tax['image'] ); ?>">
		<div class="container-fluid">
			<h2><?php echo esc_html( $global_tax['name'] ); ?></h2>
				<?php if ( true == $global_tax['image_counter'] ) { ?>
					<span class="thumb-info-icons position-style-3 text-color-light">
						<span class="thumb-info-icon pictures background-color-primary">
							<?php echo esc_html( $global_tax['count'] ); ?>
							<i class="far fa-image"></i>
						</span>
					</span>
				<?php } ?>
			<span class="thumb-info-plus"></span>
		</div>
	</section>
</a>
