<div class="smush-final-log sui-hidden">
	<div class="smush-bulk-errors"></div>
	<div class="smush-bulk-errors-actions sui-hidden">
		<a href="<?php echo esc_url( apply_filters( 'smush_unsmushed_media_filter_url', admin_url( 'upload.php?mode=list&attachment-filter=post_mime_type:image&smush-filter=failed_processing' ) ) ); ?>" class="sui-button">
			<i class="sui-icon-eye" aria-hidden="true"></i>
			<?php esc_html_e( 'View all in library', 'wp-smushit' ); ?>
		</a>
		<span class="sui-button sui-button-ghost wp_smush_ignore_all_failed_items">
			<?php esc_html_e( 'Ignore all', 'wp-smushit' ); ?>
		</span>
	</div>
</div>