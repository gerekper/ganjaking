<?php
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
?>

<?php

	$data 	= array(
		'wp_uploads' 			=> wp_get_upload_dir(),
		'memory_limit' 		=> wp_convert_hr_to_bytes(@ini_get('memory_limit')),
		'time_limit' 			=> ini_get('max_execution_time'),
		'max_input_vars' 	=> ini_get('max_input_vars'),
	);

	$status = array(
		'version' 				=> $this->version > 0,
		'uploads'					=> wp_is_writable($data['wp_uploads']['basedir']),
		'fs'							=> (Mfn_Helper::filesystem() || WP_Filesystem()) ? true : false,
		'zip'							=> class_exists('ZipArchive'),
		'suhosin'					=> extension_loaded('suhosin'),
		'memory_limit'		=> $data['memory_limit'] >= 268435456,
		'time_limit'			=> (($data['time_limit'] >= 180) || ($data['time_limit'] == 0)) ? true : false,
		'max_input_vars'	=> $data['max_input_vars'] >= 5000,
	);

?>

<div class="mfn-mini-status">

	<ul class="system-status mini">

		<li>
			<span class="label"><?php esc_html_e('API server connection', 'mfn-opts') ?></span>
			<?php if ($status['version']): ?>
				<span class="status yes dashicons dashicons-yes"></span>
				<span class="desc"><a class="button button-secondary" href="admin.php?page=betheme&forcecheck"><?php esc_html_e('check again', 'mfn-opts') ?></a></span>
			<?php else: ?>
				<span class="status no dashicons dashicons-no"></span>
				<span class="desc"><a class="button button-secondary" href="admin.php?page=betheme&forcecheck"><?php esc_html_e('check again', 'mfn-opts') ?></a></span>
				<p class="status-notice status-error">Your server is blocking connection to API server <b>api.muffingroup.com</b><br />Please contact your hosting provider.<br /><a target="_blank" href="admin.php?page=betheme&forcecheck&be-debug">Debug informations</a></p>
			<?php endif; ?>
		</li>

		<li>
			<span class="label"><?php esc_html_e('Uploads folder writable', 'mfn-opts') ?></span>
			<?php if ($status['uploads']): ?>
				<span class="status yes dashicons dashicons-yes"></span>
			<?php else: ?>
				<span class="status no dashicons dashicons-no"></span>
				<p class="status-notice status-error">Uploads folder must be writable. Please set write permission to your wp-content/uploads folders</p>
			<?php endif; ?>
		</li>

		<li>
			<span class="label"><?php esc_html_e('WP File System', 'mfn-opts') ?></span>
			<?php if ($status['fs']): ?>
				<span class="status yes dashicons dashicons-yes"></span>
			<?php else: ?>
				<span class="status no dashicons dashicons-no"></span>
				<p class="status-notice status-error">File System access is required for pre-built websites and plugins installation. Please contact your hosting provider.</p>
			<?php endif; ?>
		</li>

		<li>
			<span class="label"><?php esc_html_e('ZipArchive', 'mfn-opts') ?></span>
			<?php if ($status['zip']): ?>
				<span class="status yes dashicons dashicons-yes"></span>
			<?php else: ?>
				<span class="status no dashicons dashicons-no"></span>
				<p class="status-notice status-error">ZipArchive is required for pre-built websites and plugins installation. Please contact your hosting provider.</p>
			<?php endif; ?>
		</li>

		<?php if ($status[ 'suhosin' ]): ?>

			<li>
				<span class="label"><?php esc_html_e('SUHOSIN Installed', 'mfn-opts') ?></span>
				<span class="status info dashicons dashicons-info"></span>
				<p class="status-notice">Suhosin may need to be configured to increase its data submission limits.</p>
			</li>

		<?php else: ?>

			<li>
				<span class="label"><?php esc_html_e('PHP Memory Limit', 'mfn-opts') ?></span>
				<?php if ($status['memory_limit']): ?>
					<span class="status yes dashicons dashicons-yes"></span>
					<span class="desc"><?php echo esc_html(size_format($data['memory_limit'])); ?></span>
				<?php else: ?>

					<?php if ($data['memory_limit'] < 134217728): ?>

						<span class="status no dashicons dashicons-no"></span>
						<span class="desc"><?php echo esc_html(size_format($data['memory_limit'])); ?></span>
						<p class="status-notice status-error">Minimum <strong>128 MB</strong> is required, <strong>256 MB</strong> is recommended. </p>

					<?php else: ?>

						<span class="status info dashicons dashicons-info"></span>
						<span class="desc"><?php echo esc_html(size_format($data['memory_limit'])); ?></span>
						<p class="status-notice status-error">Current memory limit is OK, however <strong>256 MB</strong> is recommended. </p>

					<?php endif; ?>

				<?php endif; ?>
			</li>

			<li>
				<span class="label"><?php esc_html_e('PHP Time Limit', 'mfn-opts') ?></span>
				<?php if ($status['time_limit']): ?>
					<span class="status yes dashicons dashicons-yes"></span>
					<span class="desc"><?php echo esc_html($data['time_limit']); ?></span>
				<?php else: ?>

					<?php if ($data['time_limit'] < 60): ?>

						<span class="status no dashicons dashicons-no"></span>
						<span class="desc"><?php echo esc_html($data['time_limit']); ?></span>
						<p class="status-notice status-error">Minimum <strong>60</strong> is required, <strong>180</strong> is recommended. </p>

					<?php else: ?>

						<span class="status info dashicons dashicons-info"></span>
						<span class="desc"><?php echo esc_html($data['time_limit']); ?></span>
						<p class="status-notice status-error">Current time limit is OK, however <strong>180</strong> is recommended. </p>

					<?php endif; ?>

				<?php endif; ?>
			</li>

			<li>
				<span class="label"><?php esc_html_e('PHP Max Input Vars', 'mfn-opts') ?></span>
				<?php if ($status['max_input_vars']): ?>
					<span class="status yes dashicons dashicons-yes"></span>
					<span class="desc"><?php echo esc_html($data['max_input_vars']); ?></span>
				<?php else: ?>
					<span class="status no dashicons dashicons-no"></span>
					<span class="desc"><?php echo esc_html($data['max_input_vars']); ?></span>
					<p class="status-notice status-error">Minimum 5000 is required</p>
				<?php endif; ?>
			</li>

			<li class="info">php.ini values are shown above. Real values may vary, please check your limits using <a target="_blank" href="http://php.net/manual/en/function.phpinfo.php">php_info()</a></li>

		<?php endif; ?>

		<li>
			<a href="admin.php?page=be-status"><?php esc_html_e('More details', 'mfn-opts') ?></a>
		</li>

	</ul>

</div>
