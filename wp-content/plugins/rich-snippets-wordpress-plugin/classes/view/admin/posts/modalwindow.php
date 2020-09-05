<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>
<div class="wpb-rs-form-modal media-modal" style="display: none;">
	<button type="button" class="media-modal-close">
		<span class="media-modal-icon">
			<span class="screen-reader-text"><?php _e( 'Close window', 'rich-snippets-schema' ); ?></span>
		</span>
	</button>
	<div class="media-modal-content">
		<div class="media-frame mode-select hide-router">
			<div class="media-frame-menu">
				<div class="media-menu">
					<div class="separator"></div>
					<a target="_blank" href="<?php echo esc_url( admin_url( 'edit.php?post_type=wpb-rs-global' ) ); ?>"
					   class="media-menu-item wpb-rs-modal-new"><?php _e( 'Create New Global Snippet', 'rich-snippets-schema' ); ?></a>
				</div>
			</div>
			<div class="media-frame-title">
				<h1><?php _e( 'Edit global snippets', 'rich-snippets-schema' ); ?></h1>
				<div class="wpb-rs-modalwindow-loader updating-message wpb-rs-modalwindow-loader-hidden">
					<p class="updating-message"></p>
				</div>
			</div>
			<div class="media-frame-content">
				<form action="">
					<div class="wpb-rs-form-outer">
						<div class="wpb-rs-form-info">
							<span class="dashicons dashicons-arrow-left-alt"></span>
							<?php
							printf(
								__(
									'Please select a global snippet from the menu. No global snippets? <a target="_blank" href="%s">Create one here.</a>',
									'rich-snippets-schema'
								),
								esc_url( admin_url( 'edit.php?post_type=wpb-rs-global' ) )
							); ?>
						</div>
					</div>
				</form>
			</div>
			<div class="media-frame-toolbar">
				<div class="media-toolbar">
					<div class="media-toolbar-primary">
						<button type="button"
								class="button media-button button-primary button-large wpb-rs-modalwindow-save">
							<?php _e( 'Save', 'rich-snippets-schema' ); ?>
						</button>
						<button type="button" class="button button-large media-button wpb-rs-modalwindow-reload">
							<?php _ex( 'Reload menu', 'Button label for reloading menu', 'rich-snippets-schema' ); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
