<?php
/**
 * An HTML file for the Bookings Templates Page.
 *
 * @package WooCommerce Bookings
 */

?>

<div class="wrap woocommerce bookings-templates">
	<div id="product-templates">
		<h2><?php esc_html_e( 'Get started quickly with a template', 'woocommerce-bookings' ); ?></h2>
		<p><?php esc_html_e( 'Choose a template option below and get started quickly with your online bookings. 👇', 'woocommerce-bookings' ); ?></p>
		<div id="template-loop">
			<div id="template-loop-ul">
				<?php
				if ( isset( $product_data['products'] ) ) :
					foreach ( $product_data['products'] as $index => $product ) :
						?>
						<div class="template-loop-item" data-template-index="<?php echo esc_attr( $index ); ?>" data-template-slug="<?php echo esc_attr( $product['slug'] ); ?>">
							<?php if ( isset( $product['product_thumbnail'] ) ) : ?>
								<div class="template-item-thumbnail">
									<img
										src="<?php echo esc_url( WC_BOOKINGS_PLUGIN_URL . '/dist/images/product-templates/' . $product['product_thumbnail'] ); ?>"/>
								</div>
							<?php endif; ?>
							<div class="template-item-info">
								<?php if ( isset( $product['name'] ) ) : ?>
									<h2><?php echo esc_html( $product['name'] ); ?></h2>
								<?php endif; ?>

								<?php if ( isset( $product['short_description'] ) ) : ?>
									<p><?php echo esc_html( $product['short_description'] ); ?></p>
								<?php endif; ?>
							</div>
							<div class="template-item-popup" id="item-popup-<?php echo esc_attr( $product['slug'] ); ?>">
								<span class="item-popup-arrow left dashicons dashicons-arrow-left-alt2"></span>
								<span class="close-template-popup dashicons dashicons-no"></span>
								<div class="template-item-popup-inner">
									<div class="template-item-popup-content">
										<?php if ( isset( $product['name'] ) ) : ?>
											<h2><?php echo esc_html( $product['name'] ); ?></h2>
										<?php endif; ?>

										<?php if ( isset( $product['scenario'] ) ) : ?>
											<h4><?php esc_html_e( 'Scenario', 'woocommerce-bookings' ); ?></h4>
											<p><?php echo wp_kses_post( $product['scenario'] ); ?></p>
										<?php endif; ?>

										<?php if ( isset( $product['features_utilized'] ) ) : ?>
											<h4><?php esc_html_e( 'Booking features utilized', 'woocommerce-bookings' ); ?></h4>
											<ul>
												<?php foreach ( $product['features_utilized'] as $features ) : ?>
													<li><?php echo esc_html( $features ); ?></li>
												<?php endforeach; ?>
											</ul>
										<?php endif; ?>
										<hr/>

										<a class="wc-use-templates button button-primary" href="javascript:void(0)">
											<?php esc_html_e( 'Use template', 'woocommerce-bookings' ); ?>
											<span class="dashicons"></span>
										</a>
									</div>
									<div class="template-item-popup-images">
										<div class="item-popup-fe-images">
											<?php if ( isset( $product['front_end_display'] ) ) : ?>
												<img
													src="<?php echo esc_url( WC_BOOKINGS_PLUGIN_URL . '/dist/images/product-templates/' . $product['front_end_display'] ); ?>"/>
											<?php endif; ?>
										</div>
									</div>
								</div>
								<span class="item-popup-arrow right dashicons dashicons-arrow-right-alt2"></span>
							</div>
						</div>
						<?php
						endforeach;
				endif;
				?>
			</div>
		</div>
	</div>
	<div class="wrap white-box">
		<h2><?php esc_html_e( 'Get started from scratch', 'woocommerce-bookings' ); ?></h2>
		<p>
			<?php
			printf(
				// translators: 1: opening link 2: closing link.
				esc_html__(
					'Or, if you know your business like we think you do and you are confident in filling in an array of settings, be our guest! We have %1$sdocumentation%2$s to assist you.',
					'woocommerce-bookings'
				),
				'<a href="https://woocommerce.com/document/woocommerce-bookings-store-manager-guide/#section-1" target="_blank">',
				'</a>'
			);
			?>
		</p>
		<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=product&bookable_product=1' ) ); ?>" class="start-from-scratch-link">
			<span id="blue-circle"></span>
			<span><?php esc_html_e( 'Start from scratch', 'woocommerce-bookings' ); ?></span>
			<span><?php esc_html_e( 'Create from a blank template', 'woocommerce-bookings' ); ?></span>
		</a>
	</div>
</div>
