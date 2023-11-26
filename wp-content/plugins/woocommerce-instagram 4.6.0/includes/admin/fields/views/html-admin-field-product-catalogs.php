<?php
/**
 * Admin field: Product catalogs.
 *
 * @package WC_Instagram/Admin/Fields/Views
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<script type="text/html" id="tmpl-wc-instagram-product-catalog-feed">
	<div class="wc-backbone-modal wc-instagram-product-catalog-feed">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1>
					<?php
					/* translators: 1: product catalog name 2: file format */
					echo esc_html( sprintf( _x( 'Data feed: %1$s.%2$s', 'product catalog data feed modal title', 'woocommerce-instagram' ), '{{ data.catalog.name }}', '{{ data.format }}' ) );
					?>
					</h1>
					<button class="modal-close modal-close-link dashicons dashicons-no-alt">
						<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'woocommerce' ); // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch ?></span>
					</button>
				</header>
				<article>
					<# if ( _.contains( ['creating', 'viewing'], data.action ) ) { #>
						<div class="wc-instagram-notice info">
							<# if ( data.format === 'xml' ) { #>
								<p><?php esc_html_e( 'This data feed is updated periodically on your site and has a public URL that Facebook visits for synchronizing the product data.', 'woocommerce-instagram' ); ?></p>
							<# } else if ( data.format === 'csv' ) { #>
								<p><?php esc_html_e( 'This data feed is useful for bulk editing the product data and updating your Facebook Catalog manually.', 'woocommerce-instagram' ); ?></p>
								<p><?php echo wp_kses_post( '<strong>Important: </strong> This data feed is not updated automatically.', 'woocommerce-instagram' ); ?></p>
							<# } #>
						</div>
					<# } #>

					<# if ( data.action === 'creating' ) { #>
						<p>
							<?php
							/* translators: %s: file format */
							echo wp_kses_post( sprintf( __( 'Create the catalog data feed in %s format.', 'woocommerce-instagram' ), '<span class="data-feed-format">{{ data.format }}</span>' ) );
							?>
						</p>
					<# } else if ( data.action === 'viewing' ) { #>
						<p>
							<?php
							echo wp_kses(
								sprintf(
								/* translators: %s: last modified date */
									__( 'The catalog data feed was updated on %s.', 'woocommerce-instagram' ),
									'<time datetime="{{ data.file.lastModified.datetime }}">{{ data.file.lastModified.i18n }}</time>'
								),
								array( 'time' => array( 'datetime' => array() ) )
							);
							?>
						</p>
						<p><?php esc_html_e( 'You can download the current version or request an update.', 'woocommerce-instagram' ); ?></p>
					<# } else if ( data.action === 'updating' ) { #>
						<p>
							<?php
							echo wp_kses_post(
								__( 'The catalog data feed is being updated at this moment.<br/>Depending on the number of products in the catalog, this may take a while.<br/>Please, wait.', 'woocommerce-instagram' )
							);
							?>
						</p>
					<# } else if ( data.action === 'canceling' ) { #>
						<p>
							<?php
							echo wp_kses_post(
								__( 'Canceling the process for updating the catalog data feed.<br/>Please, wait.', 'woocommerce-instagram' )
							);
							?>
						</p>
					<# } #>

					<# if ( _.contains( ['loading', 'updating', 'canceling'], data.action ) ) { #>
						<?php wc_instagram_loading(); ?>
					<# } #>
				</article>

				<# if ( data.action !== 'loading' ) { #>
				<footer>
					<div class="inner">
						<# if ( data.action === 'viewing' ) { #>
							<div class="wc-action-button-group">
								<span class="wc-action-button-group__items">
								<?php
								printf(
									'<a class="button button-secondary button-large" href="%1$s&format={{ data.format }}&catalog_id={{ data.catalog.id }}">%2$s</a>',
									esc_url( wc_instagram_get_settings_url( array( 'action' => 'download' ) ) ),
									esc_html__( 'Download file', 'woocommerce-instagram' )
								);
								?>
								</span>

								<# if ( data.format === 'xml' ) { #>
									<span class="wc-action-button-group__items">
										<?php
										printf(
											'<a class="button button-secondary button-large copy-url" href="{{ data.catalog.url }}" data-tip="%1$s">%2$s</a>',
											esc_attr__( 'Copied!', 'woocommerce-instagram' ),
											esc_html__( 'Copy URL', 'woocommerce-instagram' )
										);
										?>
									</span>
								<# } #>
							</div>

							<a class="button button-primary button-large request-update" href="#"><?php esc_html_e( 'Update feed', 'woocommerce-instagram' ); ?></a>
						<# } else if ( data.action === 'creating' ) { #>
							<a class="button button-primary button-large request-update" href="#"><?php esc_html_e( 'Create feed', 'woocommerce-instagram' ); ?></a>
						<# } else if ( data.action === 'updating' ) { #>
							<a class="button button-primary button-large cancel-update" href="#"><?php esc_html_e( 'Cancel process', 'woocommerce-instagram' ); ?></a>
						<# } else if ( data.action === 'canceling' ) { #>
							<a class="button button-primary button-large cancel-update" href="#" disabled="disabled"><?php esc_html_e( 'Canceling&hellip;', 'woocommerce-instagram' ); ?></a>
						<# } #>
					</div>
				</footer>
				<# } #>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
