<?php
/**
 * My Downloads - Deprecated
 *
 * Shows downloads on the account page.
 *
 * @version     2.0.0
 * @depreacated 2.6.0
 */

defined( 'ABSPATH' ) || exit;

$downloads = WC()->customer->get_downloadable_products();

if ( $downloads ) : ?>

	<div class="featured-box featured-box-primary align-left m-t-lg">
		<div class="box-content">

			<?php do_action( 'woocommerce_before_available_downloads' ); ?>

			<h2><?php echo apply_filters( 'woocommerce_my_account_my_downloads_title', esc_html__( 'Available downloads', 'woocommerce' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>

			<ul class="digital-downloads">
				<?php foreach ( $downloads as $download ) : ?>
					<li>
						<?php
							do_action( 'woocommerce_available_download_start', $download );

						if ( is_numeric( $download['downloads_remaining'] ) ) {
							$downloads_remain = $download['downloads_remaining'];
							/* translators: %s product name */
							echo apply_filters( 'woocommerce_available_download_count', '<span class="count">' . sprintf( _n( '%s download remaining', '%s downloads remaining', $downloads_remain, 'woocommerce' ), $downloads_remain ) . '</span> ', $download ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}

							echo apply_filters( 'woocommerce_available_download_link', '<a href="' . esc_url( $download['download_url'] ) . '">' . $download['download_name'] . '</a>', $download ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

							do_action( 'woocommerce_available_download_end', $download );
						?>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php do_action( 'woocommerce_after_available_downloads' ); ?>
		</div>
	</div>

<?php endif; ?>
