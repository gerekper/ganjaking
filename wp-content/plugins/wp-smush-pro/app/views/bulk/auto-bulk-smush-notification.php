<?php
/**
 * Notification for auto Bulk Smush on scan completed while re-checking images.
 *
 * @var bool    $background_processing_enabled  Background optimization is enabled.
 *
 */
?>
<div class="sui-notice sui-notice-grey wp-smush-auto-bulk-smush-notification sui-hidden">
	<div class="sui-notice-content">
		<div class="sui-notice-message">
			<span class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></span>
			<p>
                <?php
                    if( $background_processing_enabled ) {
                        esc_html_e( 'Upon completion of the image recheck process, Smush will automatically proceed to initiate bulk image compression.', 'wp-smushit' );
                    } else {
                        esc_html_e( 'Once Smush completes the recheck process it will begin the Smush, it is recommended to keep this page open to initiate bulk image compression.', 'wp-smushit' );
                    }
                ?>
            </p>
		</div>
	</div>
</div>