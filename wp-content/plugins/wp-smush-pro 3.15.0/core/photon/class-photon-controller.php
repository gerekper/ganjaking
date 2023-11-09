<?php

namespace Smush\Core\Photon;

use Smush\Core\Controller;
use Smush\Core\Media\Media_Item;
use WP_Smush;

/**
 * Photon is an image acceleration and modification service for Jetpack-connected WordPress sites. When Photon is active, only the main file is kept on the server as a physical file, everything else is on a CDN.
 * @see https://developer.wordpress.com/docs/photon/
 */
class Photon_Controller extends Controller {
	public function __construct() {
		$this->register_filter( 'wp_smush_media_item_size', array( $this, 'only_handle_full_size' ), 10, 2 );

		$this->register_action( 'smush_setting_column_right_outside', array( $this, 'render_site_accelerator_notice' ), 20, 2 );
	}

	public function is_photon_active() {
		return has_filter( 'wp_image_editors', 'photon_subsizes_override_image_editors' );
	}

	public function only_handle_full_size( $size, $key ) {
		if ( ! $this->is_photon_active() ) {
			return $size;
		}

		return $key === Media_Item::SIZE_KEY_FULL
			? $size
			: false;
	}

	public function render_site_accelerator_notice( $name ) {
		if ( ! $this->is_photon_active() || 'bulk' !== $name ) {
			return;
		}

		$text = sprintf( /* translators: %1$s - <a>, %2$s - </a> */
			esc_html__( "We noticed that your site is configured to completely offload intermediate thumbnail sizes (they don't exist in your Media Library), so Smush can't optimize those images. You can still optimize your %1\$sOriginal Images%2\$s if you want to.", 'wp-smushit' ),
			'<a href="#original">',
			'</a>'
		);
		?>
		<div class="sui-notice sui-notice-warning" style="margin-top: -20px">
			<div class="sui-notice-content">
				<div class="sui-notice-message">
					<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
					<p><?php echo wp_kses_post( $text ); ?></p>
				</div>
			</div>
		</div>
		<?php
	}
}