<?php
/**
 * Output the content for Directory smush list dialog content.
 *
 * @package WP_Smush
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<div class="sui-modal sui-modal-lg">
	<div
			role="dialog"
			id="wp-smush-list-dialog"
			class="sui-modal-content wp-smush-list-dialog"
			aria-modal="true"
			aria-labelledby="list-dialog-title"
			aria-describedby="list-dialog-description"
	>
		<div class="sui-box">
			<div class="sui-box-header">
				<h3 class="sui-box-title" id="list-dialog-title">
					<?php esc_html_e( 'Choose Directory', 'wp-smushit' ); ?>
				</h3>
				<button class="sui-button-icon sui-button-float--right" data-modal-close="" id="dialog-close-div">
					<i class="sui-icon-close sui-md" aria-hidden="true"></i>
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close', 'wp-smushit' ); ?></span>
				</button>
			</div>

			<div class="sui-box-body">
				<p id="list-dialog-description">
					<?php esc_html_e( 'Choose which directory you wish to smush. Smush will automatically include any images in subdirectories of your selected directory.', 'wp-smushit' ); ?>
				</p>
				<div class="sui-toggle-content" style="margin-left: 0px;margin-bottom: 30px">
					<div class="sui-notice sui-notice-info">
						<div class="sui-notice-content">
							<div class="sui-notice-message">
								<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
								<p><?php esc_html_e( 'Note: the wp-admin and wp-includes directories contain core WordPress files and are not selectable. Similarly, the auto-generated media directories in wp-content/uploads are not selectable here as they are processed by Bulk Smush.', 'wp-smushit' ); ?></p>
							</div>
						</div>
					</div>
				</div>
				<div class="content"></div>

				<?php wp_nonce_field( 'smush_get_dir_list', 'list_nonce' ); ?>
				<?php wp_nonce_field( 'smush_get_image_list', 'image_list_nonce' ); ?>
			</div>

			<div class="sui-box-footer sui-content-right">
				<button class="sui-modal-close sui-button sui-button-blue" disabled id="wp-smush-select-dir">
					<span class="sui-loading-text"><?php esc_html_e( 'Choose directory', 'wp-smushit' ); ?></span>
					<span class="sui-icon-loader sui-loading" aria-hidden="true"></span>
				</button>
			</div>
		</div>
	</div>
</div>
