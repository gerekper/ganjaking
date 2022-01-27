<?php
/**
 * Admin field: Product catalogs.
 *
 * @package WC_Instagram/Admin/Fields/Views
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
<script type="text/html" id="tmpl-wc-instagram-product-catalog-download">
	<div class="wc-backbone-modal wc-instagram-product-catalog-download">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1>
					<?php
					/* translators: 1: product catalog name 2: file format */
					echo esc_html( sprintf( _x( 'Download %1$s %2$s', 'product catalog download modal title', 'woocommerce-instagram' ), '{{ data.catalog.name }}', '{{ data.format }}' ) );
					?>
					</h1>
					<button class="modal-close modal-close-link dashicons dashicons-no-alt">
						<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'woocommerce' ); // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch ?></span>
					</button>
				</header>
				<article>
					<# if ( _.isEmpty( data.file ) ) { #>
						<?php wc_instagram_loading(); ?>
					<# } else if ( data.file.status ) { #>
						<p>
							<?php
							echo wp_kses_post(
								__( 'The catalog file is being generated at this moment.<br/>Depending on the number of products in the catalog, this may take a while.<br/>Please, wait.', 'woocommerce-instagram' )
							);
							?>
						</p>

						<?php wc_instagram_loading(); ?>
					<# } else if ( _.isEmpty( data.file.lastModified ) ) { #>
						<p>
							<?php
							/* translators: %s: file format */
							echo esc_html( sprintf( __( 'Generate your first %s catalog file.', 'woocommerce-instagram' ), '{{ data.format }}' ) );
							?>
						</p>

						<div class="action-buttons">
							<a class="button button-primary button-large request-update" href="#"><?php esc_html_e( 'Generate file', 'woocommerce-instagram' ); ?></a>
						</div>
					<# } else { #>
						<p>
							<?php
							echo wp_kses(
								sprintf(
									/* translators: %s: last modified date */
									__( 'The catalog file was updated on %s.', 'woocommerce-instagram' ),
									'<time datetime="{{ data.file.lastModified.datetime }}">{{ data.file.lastModified.i18n }}</time>'
								),
								array( 'time' => array( 'datetime' => array() ) )
							);
							?>
						</p>
						<p><?php esc_html_e( 'You can download the current version or request an update.', 'woocommerce-instagram' ); ?></p>

						<div class="action-buttons">
							<a class="button button-secondary button-large request-update" href="#"><?php esc_html_e( 'Request update', 'woocommerce-instagram' ); ?></a>
							<?php
							printf(
								'<a class="button button-primary button-large" href="%1$s&format={{ data.format }}&catalog_id={{ data.catalog.id }}">%2$s</a>',
								esc_url( wc_instagram_get_settings_url( array( 'action' => 'download' ) ) ),
								esc_html( __( 'Download file', 'woocommerce-instagram' ) )
							);
							?>
						</div>
					<# } #>
				</article>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
