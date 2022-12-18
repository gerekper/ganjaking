<?php
global $global_tax, $porto_settings, $porto_layout, $post, $porto_portfolio_columns, $porto_portfolio_view, $porto_portfolio_thumb, $porto_portfolio_thumb_bg, $porto_portfolio_thumb_image, $porto_portfolio_ajax_load, $porto_portfolio_ajax_modal;
?>

<?php
if ( $global_tax['is_first'] ) {
	?>
	<div class="container-fluid portfolio-stripes">
		<div class="row">
			<div class="owl-carousel owl-theme nav-center custom-carousel-arrows-style m-none" data-plugin-options='{"items": 4, "on_page": <?php echo ! empty( $global_tax['number'] ) ? $global_tax['number'] : 4; ?>, "loop": false, "dots": false, "nav": true}'>
<?php } ?>
				<div>
					<div class="portfolio-item">
						<a href="<?php echo esc_url( get_site_url() . '/portfolio_cat/' . $global_tax['slug'] ); ?>" class="text-decoration-none">
							<span class="thumb-info">
								<span class="thumb-info-wrapper m-none">
									<div class="background-image" style="background-image: url('<?php echo esc_url( $global_tax['image'] ); ?>')"></div>
									<span class="thumb-info-title text-capitalize alternative-font font-weight-light text-transform-none">
										<?php echo esc_html( $global_tax['name'] ); ?>
									</span>
									<?php if ( true == $global_tax['image_counter'] ) { ?>
										<span class="thumb-info-icons position-style-1 text-color-light">
											<span class="thumb-info-icon pictures background-color-primary">
												<?php echo esc_html( $global_tax['count'] ); ?>
												<i class="far fa-image"></i>
											</span>
										</span>
									<?php } ?>
									<span class="thumb-info-plus"></span>
								</span>
							</span>
						</a>
					</div>
				</div>
<?php
if ( $global_tax['is_last'] ) {
	?>
			</div>
		</div>
	</div>

<?php } ?>
