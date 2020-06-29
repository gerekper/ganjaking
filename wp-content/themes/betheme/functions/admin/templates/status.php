<?php
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
?>

<div id="mfn-dashboard" class="wrap about-wrap">

	<?php include_once get_theme_file_path('/functions/admin/templates/parts/header.php'); ?>

	<div class="dashboard-tab status">

		<div class="col col-left">

			<h3 class="primary"><?php esc_html_e('Server Environment', 'mfn-opts'); ?></h3>

			<ul class="system-status">

				<li>
					<span class="label"><?php esc_html_e('MySQL Version', 'mfn-opts'); ?></span>
					<span class="desc"><?php echo esc_html($this->data['mysql']); ?></span>
				</li>

				<li>
					<span class="label"><?php esc_html_e('PHP Version', 'mfn-opts'); ?></span>
					<?php if ($this->status['php']): ?>
						<span class="status yes dashicons dashicons-yes"></span>
						<span class="desc"><?php echo esc_html(PHP_VERSION); ?></span>
					<?php else: ?>
						<span class="status no dashicons dashicons-no"></span>
						<span class="desc"><?php echo esc_html(PHP_VERSION); ?></span>
						<p class="status-notice status-error">WordPress requires PHP version 7 or greater. <a target="_blank" href="https://wordpress.org/about/requirements/">Learn more</a></p>
					<?php endif; ?>
				</li>

				<?php if ($this->status[ 'suhosin' ]): ?>

					<li>
						<span class="label"><?php esc_html_e('SUHOSIN Installed', 'mfn-opts'); ?></span>
						<span class="status info dashicons dashicons-info"></span>
						<p class="status-notice">Suhosin may need to be configured to increase its data submission limits.</p>
					</li>

				<?php else: ?>

					<li>
						<span class="label"><?php esc_html_e('PHP Memory Limit', 'mfn-opts'); ?></span>
						<?php if ($this->status['memory_limit']): ?>
							<span class="status yes dashicons dashicons-yes"></span>
							<span class="desc"><?php echo esc_html(size_format($this->data['memory_limit'])); ?></span>
						<?php else: ?>

							<?php if ($this->data['memory_limit'] < 134217728): ?>

								<span class="status no dashicons dashicons-no"></span>
								<span class="desc"><?php echo esc_html(size_format($this->data['memory_limit'])); ?></span>
								<p class="status-notice status-error">Minimum <strong>128 MB</strong> is required, <strong>256 MB</strong> is recommended. </p>

							<?php else: ?>

								<span class="status info dashicons dashicons-info"></span>
								<span class="desc"><?php echo esc_html(size_format($this->data['memory_limit'])); ?></span>
								<p class="status-notice status-error">Current memory limit is OK, however <strong>256 MB</strong> is recommended. </p>

							<?php endif; ?>

						<?php endif; ?>
					</li>

					<li>
						<span class="label"><?php esc_html_e('PHP Time Limit', 'mfn-opts'); ?></span>
						<?php if ($this->status['time_limit']): ?>
							<span class="status yes dashicons dashicons-yes"></span>
							<span class="desc"><?php echo esc_html($this->data['time_limit']); ?></span>
						<?php else: ?>

							<?php if ($this->data['time_limit'] < 60): ?>

								<span class="status no dashicons dashicons-no"></span>
								<span class="desc"><?php echo esc_html($this->data['time_limit']); ?></span>
								<p class="status-notice status-error">Minimum <strong>60</strong> is required, <strong>180</strong> is recommended. </p>

							<?php else: ?>

								<span class="status info dashicons dashicons-info"></span>
								<span class="desc"><?php echo esc_html($this->data['time_limit']); ?></span>
								<p class="status-notice status-error">Current time limit is OK, however <strong>180</strong> is recommended. </p>

							<?php endif; ?>

						<?php endif; ?>
					</li>

					<li>
						<span class="label"><?php esc_html_e('PHP Max Input Vars', 'mfn-opts'); ?></span>
						<?php if ($this->status['max_input_vars']): ?>
							<span class="status yes dashicons dashicons-yes"></span>
							<span class="desc"><?php echo esc_html($this->data['max_input_vars']); ?></span>
						<?php else: ?>
							<span class="status no dashicons dashicons-no"></span>
							<span class="desc"><?php echo esc_html($this->data['max_input_vars']); ?></span>
							<p class="status-notice status-error">Minimum 5000 is required</p>
						<?php endif; ?>
					</li>

				<?php endif; ?>

				<li>
					<span class="label"><?php esc_html_e('WP Max Upload Size', 'mfn-opts'); ?></span>
					<span class="desc"><?php echo esc_html($this->data['max_upload_size']); ?></span>
				</li>

				<li>
					<span class="label"><?php esc_html_e('cURL', 'mfn-opts'); ?></span>
					<?php if ($this->status['curl']): ?>
						<span class="status yes dashicons dashicons-yes"></span>
					<?php else: ?>
						<span class="status no dashicons dashicons-no"></span>
						<p class="status-notice status-error">Your server does not have <strong>cURL</strong> enabled. Please contact your hosting provider.</p>
					<?php endif; ?>
				</li>

				<li>
					<span class="label"><?php esc_html_e('DOMDocument', 'mfn-opts'); ?></span>
					<?php if ($this->status['dom']): ?>
						<span class="status yes dashicons dashicons-yes"></span>
					<?php else: ?>
						<span class="status no dashicons dashicons-no"></span>
						<p class="status-notice status-error">DOMDocument is required for WordPress Importer. Please contact your hosting provider.</p>
					<?php endif; ?>
				</li>

				<li class="info">
					php.ini values are shown above. Real values may vary, please check your limits using <a target="_blank" href="http://php.net/manual/en/function.phpinfo.php">php_info()</a>.<br />
					For more details about file creation, please <a target="_blank" href="https://mediatemple.net/community/products/dv/204643880/how-can-i-create-a-phpinfo.php-page">see this tutorial</a>.
				</li>

			</ul>

		</div>

		<div class="col col-right">

			<h3 class="primary"><?php esc_html_e('WordPress Environment', 'mfn-opts'); ?></h3>

			<ul class="system-status">

				<li>
					<span class="label"><?php esc_html_e('Home URL', 'mfn-opts'); ?></span>
					<span class="desc"><?php echo esc_html($this->data['home']); ?></span>
				</li>

				<li>
					<span class="label"><?php esc_html_e('Site URL', 'mfn-opts'); ?></span>
					<?php if ($this->status['siteurl']): ?>
						<span class="desc"><?php echo esc_html($this->data['siteurl']); ?></span>
					<?php else: ?>
						<span class="status no dashicons dashicons-no"></span>
						<span class="desc"><?php echo esc_html($this->data['siteurl']); ?></span>
						<p class="status-notice status-error">Home URL host must be the same as Site URL host.</p>
					<?php endif; ?>
				</li>

				<li>
					<span class="label"><?php esc_html_e('WP Version', 'mfn-opts'); ?></span>
					<?php if ($this->status['wp_version']): ?>
						<span class="status yes dashicons dashicons-yes"></span>
						<span class="desc"><?php echo esc_html($this->data['wp_version']); ?></span>
					<?php else: ?>
						<span class="status no dashicons dashicons-no"></span>
						<span class="desc"><?php echo esc_html($this->data['wp_version']); ?></span>
						<p class="status-notice status-error">Please update WordPress to the latest version.</p>
					<?php endif; ?>
				</li>

				<li class="secondary">
					<span class="label"><?php esc_html_e('WP Multisite', 'mfn-opts'); ?></span>
					<?php if ($this->status['multisite']): ?>
						<span class="status yes dashicons dashicons-yes"></span>
					<?php else: ?>
						<span class="status no dashicons dashicons-no"></span>
					<?php endif; ?>
				</li>

				<li class="secondary">
					<span class="label"><?php esc_html_e('WP Debug', 'mfn-opts'); ?></span>
					<?php if ($this->status['debug']): ?>
						<span class="status yes dashicons dashicons-yes"></span>
					<?php else: ?>
						<span class="status no dashicons dashicons-no"></span>
					<?php endif; ?>
				</li>

				<li>
					<span class="label"><?php esc_html_e('Language', 'mfn-opts'); ?></span>
					<span class="desc"><?php printf('%s, text direction: %s', $this->data['language'], $this->data['rtl']); ?></span>
				</li>

			</ul>

		</div>

	</div>

</div>
