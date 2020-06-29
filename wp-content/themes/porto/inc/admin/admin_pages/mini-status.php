<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php

	global $wp_filesystem;
	// Initialize the WordPress filesystem, no more using file_put_contents function
if ( empty( $wp_filesystem ) ) {
	require_once ABSPATH . '/wp-admin/includes/file.php';
	WP_Filesystem();
}

	$data = array(
		'wp_uploads'     => wp_get_upload_dir(),
		'memory_limit'   => wp_convert_hr_to_bytes( @ini_get( 'memory_limit' ) ),
		'time_limit'     => ini_get( 'max_execution_time' ),
		'max_input_vars' => ini_get( 'max_input_vars' ),
	);

	$status = array(
		'uploads'        => wp_is_writable( $data['wp_uploads']['basedir'] ),
		'fs'             => ( $wp_filesystem || WP_Filesystem() ) ? true : false,
		'zip'            => class_exists( 'ZipArchive' ),
		'suhosin'        => extension_loaded( 'suhosin' ),
		'memory_limit'   => $data['memory_limit'] >= 268435456,
		'time_limit'     => ( ( $data['time_limit'] >= 600 ) || ( 0 == $data['time_limit'] ) ) ? true : false,
		'max_input_vars' => $data['max_input_vars'] >= 2000,
	);

	?>

<div class="porto_mini_status">

	<ul class="system-status">
		<li>
			<?php if ( $status['uploads'] ) : ?>
				<i class="status yes fas fa-check"></i>
			<?php else : ?>
				<i class="status no fas fa-ban"></i>
			<?php endif; ?>
			<span class="label"><?php esc_html_e( 'Uploads folder writable', 'porto' ); ?></span>
			<?php if ( ! $status['uploads'] ) : ?>
				<p class="status-notice status-error">Uploads folder must be writable. Please set write permission to your wp-content/uploads folder.</p>
			<?php endif; ?>
		</li>

		<li>
			<?php if ( $status['fs'] ) : ?>
				<i class="status yes fas fa-check"></i>
			<?php else : ?>
				<i class="status no fas fa-ban"></i>
			<?php endif; ?>
			<span class="label"><?php esc_html_e( 'WP File System', 'porto' ); ?></span>
			<?php if ( ! $status['fs'] ) : ?>
				<p class="status-notice status-error">File System access is required for pre-built websites and plugins installation. Please contact your hosting provider.</p>
			<?php endif; ?>
		</li>

		<li>
			<?php if ( $status['zip'] ) : ?>
				<i class="status yes fas fa-check"></i>
			<?php else : ?>
				<i class="status no fas fa-ban"></i>
			<?php endif; ?>
			<span class="label"><?php esc_html_e( 'ZipArchive', 'porto' ); ?></span>
			<?php if ( ! $status['zip'] ) : ?>
				<p class="status-notice status-error">ZipArchive is required for pre-built websites and plugins installation. Please contact your hosting provider.</p>
			<?php endif; ?>
		</li>

		<?php if ( $status['suhosin'] ) : ?>

			<li>
				<i class="status info fas fa-info"></i>
				<span class="label"><?php esc_html_e( 'SUHOSIN Installed', 'porto' ); ?></span>
				<p class="status-notice">Suhosin may need to be configured to increase its data submission limits.</p>
			</li>

		<?php else : ?>

			<li>
				
				<?php if ( $status['memory_limit'] ) : ?>
					<i class="status yes fas fa-check"></i>
				<?php else : ?>
					<?php if ( $data['memory_limit'] < 134217728 ) : ?>
						<i class="status no fas fa-ban"></i>
					<?php else : ?>
						<i class="status info fas fa-info"></i>
					<?php endif; ?>
				<?php endif; ?>
				<span class="label"><?php esc_html_e( 'PHP Memory Limit:', 'porto' ); ?> <em>(<?php echo size_format( $data['memory_limit'] ); ?>)</em></span>
				<?php if ( ! $status['memory_limit'] ) : ?>

					<?php if ( $data['memory_limit'] < 134217728 ) : ?>
						<p class="status-notice status-error">Minimum <strong>128 MB</strong> is required, <strong>256 MB</strong> is recommended. </p>

					<?php else : ?>
						<p class="status-notice status-error">Current memory limit is OK, however <strong>256 MB</strong> is recommended. </p>

					<?php endif; ?>

				<?php endif; ?>
			</li>

			<li>
				<?php if ( $status['time_limit'] ) : ?>
					<i class="status yes fas fa-check"></i>
				<?php else : ?>
					<?php if ( $data['time_limit'] < 300 ) : ?>
						<i class="status no fas fa-ban"></i>
					<?php else : ?>
						<i class="status info fas fa-info"></i>
					<?php endif; ?>
				<?php endif; ?>
				<span class="label"><?php esc_html_e( 'PHP max_execution_time', 'porto' ); ?> <em>(<?php echo esc_html( $data['time_limit'] ); ?>)</em></span>
				<?php if ( ! $status['time_limit'] ) : ?>
					<?php if ( $data['time_limit'] < 300 ) : ?>
						<p class="status-notice status-error">Minimum <strong>300</strong> is required, <strong>600</strong> is recommended. </p>

					<?php else : ?>
						<p class="status-notice status-error">Current time limit is OK, however <strong>600</strong> is recommended. </p>

					<?php endif; ?>

				<?php endif; ?>
			</li>

			<li>
				<?php if ( $status['max_input_vars'] ) : ?>
					<i class="status yes fas fa-check"></i>
				<?php else : ?>
					<i class="status no fas fa-ban"></i>
				<?php endif; ?>
				<span class="label"><?php esc_html_e( 'PHP max_input_vars', 'porto' ); ?> <em>(<?php echo esc_html( $data['max_input_vars'] ); ?>)</em></span>
				<?php if ( ! $status['max_input_vars'] ) : ?>
					<p class="status-notice status-error">Minimum 2000 is required</p>
				<?php endif; ?>
			</li>
			<li>
				<p class="mb-0"><em><i class="fas fa-info-circle"></i> Do not worry if you are unable to update your server configuration due to hosting limit, you can use "Alternative Import" method in Demo Content import page.</em></p>
			</li>

			<li class="info">php.ini values are shown above. Real values may vary, please check your limits using <a target="_blank" href="http://php.net/manual/en/function.phpinfo.php">php_info()</a></li>
		<?php endif; ?>

	</ul>

</div>
