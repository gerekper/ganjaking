<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

// Attempt to workaround memory limit & execution time issues
@ini_set( 'max_execution_time', 0 );
@ini_set( 'memory_limit', '256M' );

$deleteLink = '';
if( !empty( $_GET['user'] ) ) {
	$deleteLink = wp_nonce_url('users.php?action=delete&amp;user='.(int)$_GET['user'], 'bulk-users' );
}

$authorized = LS_Config::isActivatedSite();
$isAdmin 	= current_user_can('manage_options');

$notifications = array(

	'dbUpdateSuccess' => __('LayerSlider has attempted to update your database. Server restrictions may apply, please verify whether it was successful.', 'LayerSlider')
);

?><div class="wrap">
	<h2>
		<?php _e('System Status', 'LayerSlider') ?>
		<a href="<?php echo admin_url('admin.php?page=layerslider-options') ?>" class="add-new-h2"><?php _e('&larr; Options', 'LayerSlider') ?></a>
	</h2>

	<div class="notice notice-info">
		<p>
			<?php _e('This page is intended to help you identifying possible issues and to display relevant debug information about your site.', 'LayerSlider') ?>
			<?php _e('Whenever a potential issues is detected, it will be marked with red or orange text describing the nature of that issue.', 'LayerSlider') ?>
			<strong><?php _e('Please keep in mind that in most cases only your web hosting company can change server settings, thus you should contact them with the messages provided (if any).', 'LayerSlider') ?></strong>
		</p>
	</div>

	<!-- Error messages -->
	<?php if(isset($_GET['message'])) : ?>
	<div class="ls-notification <?php echo isset($_GET['error']) ? 'error' : 'updated' ?>">
		<div><?php echo $notifications[ $_GET['message'] ] ?></div>
	</div>
	<?php endif; ?>
	<!-- End of error messages -->

	<!-- System Status -->
	<?php
		$latest 	= get_option('ls-latest-version', 0);
		$plugins 	= get_plugins();
		$cachePlugs = array();
		$timeout 	= (int) ini_get('max_execution_time');
		$memory 	= ini_get('memory_limit');
		$memoryB 	= str_replace(array('G', 'M', 'K'), array('000000000', '000000', '000'), $memory);
		$postMaxB 	= str_replace(array('G', 'M', 'K'), array('000000000', '000000', '000'), ini_get('post_max_size'));
		$uploadB 	= str_replace(array('G', 'M', 'K'), array('000000000', '000000', '000'), ini_get('upload_max_filesize'));
	?>
	<div class="ls-system-status">
		<div class="ls-box km-tabs-inner">
			<table>
				<thead>
					<tr>
						<th colspan="4"><?php _e('Available Updates', 'LayerSlider') ?></th>
					</tr>
				</thead>
				<tbody>
					<tr class="<?php echo ! empty($authorized) ? '' : 'ls-warning' ?>">
						<td><?php _e('Auto-Updates:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo ! empty($authorized) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo ! empty($authorized) ? __('Activated', 'LayerSlider') : __('Not set', 'LayerSlider') ?></td>
						<td>
							<?php if( ! $authorized ) : ?>
							<span><?php echo sprintf(__('Activate your copy of LayerSlider for auto-updates, or ask new versions from the theme author, so you can always use the latest release with all the new features and bug fixes. %sClick here to learn more%s.', 'LayerSlider'), '<a href="https://layerslider.kreaturamedia.com/documentation/#updating" target="_blank">', '</a>') ?></span>
							<?php endif ?>
						</td>
					</tr>
					<tr>
						<?php $test = version_compare(LS_PLUGIN_VERSION, $latest, '<'); ?>
						<td><?php _e('LayerSlider version:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo empty($test) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo LS_PLUGIN_VERSION ?></td>
						<td>
							<?php if( $test ) : ?>
							<span><?php echo sprintf( __('Update to latest version (%1$s), as we are constantly working on new features, improvements and bug fixes.', 'LayerSlider'), $latest) ?></span>
							<?php endif ?>
						</td>
					</tr>
					<tr>
						<?php $test = layerslider_verify_db_tables(); ?>
						<td><?php _e('LayerSlider database:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo ! empty($test) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo ! empty($test) ? __('OK', 'LayerSlider') : __('Error', 'LayerSlider') ?></td>
						<td class="has-button">
							<div>
								<?php if( ! $test ) : ?>
								<span><?php echo __('Your database needs an update in order for LayerSlider to work properly. Please press the ’Update Database’ button on the right. If this does not help, you need to contact your web server hosting company to fix any issue preventing plugins creating and updating database tables.', 'LayerSlider') ?></span>
								<?php endif ?>
								<a href="<?php echo wp_nonce_url( admin_url('admin.php?page=layerslider-options&section=system-status&action=database_update'), 'database_update') ?>" class="button button-small"><?php _e('Update Database', 'LayerSlider') ?></a>
							</div>
						</td>
					</tr>
					<tr>
						<?php $test = true; ?>
						<td><?php _e('WordPress version:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo ! empty($test) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo get_bloginfo('version') ?></td>
						<td></td>
					</tr>
				<tbody>
				<thead>
					<th colspan="4"><?php _e('Site Setup & Plugin Settings', 'LayerSlider') ?></th>
				</thead>
				<tbody>


					<?php

						if( $authorized ) :
						$test = strpos(LS_ROOT_FILE, '/wp-content/plugins/LayerSlider/');
						if( ! $test ) { $test = strpos(LS_ROOT_FILE, '\\wp-content\\plugins\\LayerSlider\\'); }

					?>
					<tr>
						<td><?php _e('Install Location', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo ! empty($test) ? 'dashicons-yes' : 'dashicons-info' ?>"></span></td>
						<td><?php echo ! empty( $test ) ? _e('OK', 'LayerSlider') : _e('Non-standard', 'LayerSlider') ?></td>
						<td>
							<?php if( ! $test ) : ?>
							<span>
								<?php echo __('Using LayerSlider from a non-standard install location or having a different directory name could lead issues in receiving and installing updates. Commonly, you see this issue when you’re using a theme-included version of LayerSlider. To fix this, please first search for an option to disable/unload the bundled version in your theme, then re-install a fresh copy. Your sliders and settings are stored in the database, re-installing the plugin will not harm them.', 'LayerSlider') ?>
							</span>
							<?php endif ?>
						</td>
					</tr>
					<?php endif ?>


					<?php $test = defined('WP_DEBUG') &&  WP_DEBUG; ?>
					<tr class="<?php echo ! empty($test) ? '' : 'ls-info' ?>">
						<td><?php _e('WP Debug Mode:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo ! empty($test) ? 'dashicons-yes' : 'dashicons-info' ?>"></span></td>
						<td><?php echo ! empty( $test ) ? _e('Enabled', 'LayerSlider') : _e('Disabled', 'LayerSlider') ?></td>
						<td>
							<?php if( ! $test ) : ?>
							<span>
								<?php echo __('If you experience any issue, we recommend enabling the WP Debug mode while debugging.', 'LayerSlider') ?>
								<?php echo '<a href="https://wordpress.org/support/article/debugging-in-wordpress/" target="_blank">'. __('Click here to learn more', 'LayerSlider') .'</a>' ?>
							</span>
							<?php endif ?>
						</td>
					</tr>
					<?php
						$uploads = wp_upload_dir();
						$uploadsDir = $uploads['basedir'];
						$test = file_exists($uploadsDir) && is_writable($uploadsDir);
					?>
					<tr>
						<td><?php _e('Uploads directory:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo ! empty($test) ? 'dashicons-yes' : 'dashicons-info' ?>"></span></td>
						<td><?php echo ! empty( $test ) ? _e('OK', 'LayerSlider') : _e('Unavailable', 'LayerSlider') ?></td>
						<td>
							<?php if( ! $test ) : ?>
							<span>
								<?php echo __('LayerSlider uses the uploads directory for image uploads, exporting/importing sliders, etc. Make sure that your /wp-content/uploads/ directory exists and has write permission.', 'LayerSlider') ?>
								<?php echo '<a href="http://www.wpbeginner.com/wp-tutorials/how-to-fix-image-upload-issue-in-wordpress/" target="_blank">'. __('Click here to learn more', 'LayerSlider') .'</a>' ?>
							</span>
							<?php endif ?>
						</td>
					</tr>

					<?php

						foreach($plugins as $key => $plugin) {
							if(
								stripos( $plugin['Name'], 'cache' ) !== false ||
								stripos( $plugin['Name'], 'Cachify' ) !== false ||
								stripos( $plugin['Name'], 'Optimi' ) !== false ||
								stripos( $plugin['Name'], 'WP Rocket' ) !== false
							) {
								$cachePlugs[] = $plugin['Name'];
							}
						}

						$test = empty( $cachePlugs );
					?>
					<tr class="<?php echo $test ? '' : 'ls-warning' ?>">
						<td><?php _e('Cache plugins', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo ! empty($test) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo ! $test ? implode(', ', $cachePlugs) : __('Not found', 'LayerSlider') ?></td>
						<td>
							<?php if( ! $test ) : ?>
							<span><?php _e('The listed plugin(s) may prevent edits and other changes to show up on your site in real-time. Empty your caches if you experience any issue.', 'LayerSlider') ?></span>
							<?php endif ?>
						</td>
					</tr>
					<tr>
						<?php $test = get_option('ls_use_custom_jquery', false); ?>
						<td><?php _e('jQuery Google CDN:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo empty($test) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo ! empty($test) ? __('Enabled', 'LayerSlider') : __('Disabled', 'LayerSlider') ?></td>
						<td>
							<?php if( ! empty( $test ) ) : ?>
							<span><?php _e('Should be used in special cases only, as it can break otherwise functioning sites. This option is located on the main LayerSlider admin screen under the Advanced tab.', 'LayerSlider') ?></span>
							<?php endif ?>
						</td>
					</tr>
				</tbody>
				<thead>
					<tr>
						<th colspan="4"><?php _e('Server Settings', 'LayerSlider') ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<?php $test = version_compare(phpversion(), '5.3', '<'); ?>
						<td><?php _e('PHP Version:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo empty($test) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo phpversion() ?></td>
						<td>
							<?php if( ! empty( $test ) ) : ?>
							<span><?php _e('LayerSlider requires PHP 5.3.0 or newer. Please contact your host and ask them to upgrade PHP on your web server. Alternatively, they often offer a customer dashboard for their services, which might also provide an option to choose your preferred PHP version.', 'LayerSlider') ?></span>
							<?php endif ?>
						</td>
					</tr>
					<tr>
						<?php $test = $timeout > 0 && $timeout < 60; ?>
						<td><?php _e('PHP Time Limit:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo empty($test) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo ! empty( $timeout ) ? $timeout.'s' : 'No limit' ?></td>
						<td>
							<?php if( $test ) : ?>
							<span><?php _e('PHP max. execution time should be set to at least 60 seconds or higher when importing large sliders. Please contact your host and ask them to change this PHP setting on your web server accordingly.', 'LayerSlider') ?></span>
							<?php endif ?>
						</td>
					</tr>
					<tr>
						<?php $test = (int)$memory > 0 && $memoryB < 64 * 1000 * 1000; ?>
						<td><?php _e('PHP Memory Limit:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo empty($test) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo $memory ?></td>
						<td>
							<?php if( $test ) : ?>
							<span><?php _e('PHP memory limit should be set to at least 64MB or higher when dealing with large sliders. Please contact your host and ask them to change this PHP setting on your web server accordingly.', 'LayerSlider') ?></span>
							<?php endif ?>
						</td>

					</tr>
					<tr>
						<?php $test = $postMaxB < 16 * 1000 * 1000; ?>
						<td><?php _e('PHP Post Max Size:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo empty($test) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo ini_get('post_max_size') ?></td>
						<td>
							<?php if( $test ) : ?>
							<span><?php _e('Importing larger sliders could be problematic in some cases. This option is needed to upload large files. We recommend to set it to at least 16MB or higher. Please contact your host and ask them to change this PHP setting on your web server accordingly.', 'LayerSlider') ?></span>
							<?php endif ?>
						</td>
					</tr>
					<tr>
						<?php $test = $uploadB < 16 * 1000 * 1000; ?>
						<td><?php _e('PHP Max Upload Size:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo empty($test) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo ini_get('upload_max_filesize') ?></td>
						<td>
							<?php if( $test ) : ?>
							<span><?php _e('Importing larger sliders could be problematic in some cases. This option is needed to upload large files. We recommend to set it to at least 16MB or higher. Please contact your host and ask them to change this PHP setting on your web server accordingly.', 'LayerSlider') ?></span>
							<?php endif ?>
						</td>
					</tr>

					<?php $test = extension_loaded('suhosin'); ?>
					<tr class="<?php echo empty($test) ? '' : 'ls-warning' ?>">
						<td><?php _e('Suhosin:', '') ?></td>
						<td><span class="dashicons <?php echo  empty($test) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo $test ? __('Active', 'LayerSlider') : __('Not found', 'LayerSlider'); ?></td>
						<td>
							<?php if( $test ) : ?>
							<span><?php _e('Suhosin may override PHP server settings that are otherwise marked OK here. If you experience issues, please contact your web hosting company and ask them to verify the listed server settings above.', 'LayerSlider') ?></span>
							<?php endif ?>
						</td>
					</tr>
					<tr>
						<?php $test = class_exists('ZipArchive'); ?>
						<td><?php _e('PHP ZipArchive Extension:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo ! empty($test) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo $test ? __('Enabled', 'LayerSlider') : __('Disabled', 'LayerSlider'); ?></td>
						<td>
							<?php if( ! $test ) : ?>
							<span><?php _e('The PHP ZipArchive extension is needed to use the Template Store and import/export sliders with images.', 'LayerSlider') ?></span>
							<?php endif ?>
						</td>
					</tr>
					<tr>
						<?php $test = class_exists('DOMDocument'); ?>
						<td><?php _e('PHP DOMDocument Extension:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo ! empty($test) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo $test ? __('Enabled', 'LayerSlider') : __('Disabled', 'LayerSlider') ?></td>
						<td>
							<?php if( ! $test ) : ?>
							<span><?php _e('Front-end sliders and the slider builder interface require the PHP DOMDocument extension.', 'LayerSlider') ?></span>
							<?php endif ?>
						</td>
					</tr>
					<tr>
						<?php $test = extension_loaded('mbstring'); ?>
						<td><?php _e('PHP Multibyte String Extension:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo ! empty($test) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo $test ? __('Enabled', 'LayerSlider') : __('Disabled', 'LayerSlider') ?></td>
						<td>
							<?php if( ! $test ) : ?>
							<span><?php _e('The lack of PHP “mbstring” extension can lead to unexpected issues. Contact your server hosting provider and ask them to install/enable this extension.', 'LayerSlider') ?></span>
							<?php endif ?>
						</td>
					</tr>
					<tr>
						<?php $test = function_exists('mb_ereg_match'); ?>
						<td><?php _e('PHP Multibyte Regex Functions:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo ! empty($test) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo $test ? __('Enabled', 'LayerSlider') : __('Disabled', 'LayerSlider') ?></td>
						<td>
							<?php if( ! $test ) : ?>
							<span><?php _e('The lack of PHP “mbregex” module can lead to unexpected issues. Contact your server hosting provider and ask them to install/enable this module.', 'LayerSlider') ?></span>
							<?php endif ?>
						</td>
					</tr>
					<tr>
						<?php
							$response = wp_remote_post('https://repository.kreaturamedia.com/v4/ping/' );
							$test = ( ! is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 );
						?>
						<td><?php _e('WP Remote functions:', 'LayerSlider') ?></td>
						<td><span class="dashicons <?php echo ! empty($test) ? 'dashicons-yes' : 'dashicons-warning' ?>"></span></td>
						<td><?php echo $test ? __('OK', 'LayerSlider') : __('Blocked', 'LayerSlider') ?></td>
						<td>
							<?php if( ! $test ) : ?>
							<span><?php _e('Failed to connect to our update server. This could cause issues with product activation, serving updates or downloading templates from the Template Store. It’s most likely a web server configuration issue. Please contact your server host and ask them to allow external connections to <mark>repository.kreaturamedia.com</mark> domain and have cURL and the necessary components installed.', 'LayerSlider') ?></span>
							<?php endif ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<?php if( $isAdmin && ! empty( $_GET['updateinfo']) ) : ?>
	<div class="ls-box">
		<div class="header">
			<h2><?php _e('Update info', 'LayerSlider') ?></h2>
		</div>
		<div class="inner">
			<pre><?php var_dump( get_option('layerslider_update_info') ) ?></pre>
		</div>
	</div>

	<div class="ls-box">
		<div class="header">
			<h2><?php _e('Update info after cancellation', 'LayerSlider') ?></h2>
		</div>
		<div class="inner">
			<pre><?php var_dump( get_option('layerslider_cancellation_update_info') ) ?></pre>
		</div>
	</div>
	<?php endif ?>

	<script type="text/html" id="ls-phpinfo">
		<?php phpinfo(); ?>
	</script>

	<script type="text/html" id="ls-phpinfo-modal">
		<div id="ls-phpinfo-modal-window">
			<h1 class="kmw-modal-title"><?php _e('Advanced Debug Details', 'LayerSlider') ?></h1>
			<iframe></iframe>
		</div>
	</script>


	<script type="text/html" id="ls-erase-modal">
		<div id="ls-erase-modal-window">

			<h1 class="kmw-modal-title"><?php _e('Erase All Plugin Data', 'LayerSlider') ?></h1>
			<form method="post" class="inner" onsubmit="return confirm('<?php _e('This action cannot be undone. All LayerSlider data will be permanently deleted and you will not be able to restore them afterwards. Please consider every possibility before deciding.\r\n\r\n Are you sure you want to continue?', 'LayerSlider') ?>');">
				<?php wp_nonce_field('erase_data'); ?>
				<p><?php _e('When you remove LayerSlider, it does not automatically delete your settings and sliders by default to prevent accidental data loss. You can use this utility if you really want to erase all data used by LayerSlider.', 'LayerSlider') ?></p>
				<p class="ls-dark"><?php _e('The following actions will be performed when you confirm your intention to erase all plugin data:', 'LayerSlider'); ?></p>

				<ul>
					<li><?php _e('Remove the <i>wp_layerslider</i> database table, which stores your sliders.', 'LayerSlider') ?></li>
					<li><?php _e('Remove the relevant entries from the <i>wp_options</i> database table, which stores plugin settings.', 'LayerSlider') ?></li>
					<li><?php _e('Remove the relevant entries from the <i>wp_usermeta</i> database table, which stores user associated plugin settings.', 'LayerSlider') ?></li>
					<li><?php _e('Remove files and folders created by LayerSlider from the <i>/wp-content/uploads</i> directory. This will not affect your own uploads in the Media Library.', 'LayerSlider') ?></li>
					<li><?php _e('Deactivate LayerSlider as a last step.', 'LayerSlider') ?></li>
				</ul>
				<p><i><?php _e('The actions above will be performed on this blog only. If you have a multisite network and you are a network administrator, then an “Apply to all sites” checkbox will appear, which you can use to erase data from every site in your network if you choose so.', 'LayerSlider') ?></i></p>

				<p><?php _e('Please note: You CANNOT UNDO this action. Please CONSIDER EVERY POSSIBILITY before choosing to erase all plugin data, as you will not be able to restore data afterwards.', 'LayerSlider') ?></p>

				<?php if( is_multisite() && current_user_can('manage_network') ) : ?>
					<p class="ls-center">
						<label><input type="checkbox" name="networkwide" onclick="return confirm('<?php _e('Are you sure you want to erase plugin data from every site in network?', 'LayerSlider') ?>');"> <?php _e('Apply to all sites in multisite network', 'LayerSlider') ?></label>
					</p>
				<?php endif ?>


				<div class="ls-center">
					<button type="submit" name="ls-erase-plugin-data" class="button ls-button-red button-hero <?php echo $isAdmin ? '' : 'disabled' ?>" <?php echo $isAdmin ? '' : 'disabled' ?>><?php _e('Erase Plugin Data', 'LayerSlider') ?></button>
					<?php if( ! $isAdmin ) : ?>
					<div class="ls-notice">
						<?php _e('You must be an administrator to use this feature.', 'LayerSlider') ?>
					</div>
					<?php endif ?>
				</div>
			</form>
		</div>
	</script>


	<div class="ls-system-status-actions">
		<button class="button button-hero button-primary ls-phpinfo-button"><?php _e('Show Advanced Details', 'LayerSlider') ?></button>

		<button class="button button-hero ls-button-red ls-erase-button"><?php _e('Erase All Plugin Data', 'LayerSlider') ?></button>
	</div>


	<script>

		jQuery(document).ready(function() {

			jQuery('.ls-phpinfo-button').click(function() {

				var $modal = kmw.modal.open({
					content: '#ls-phpinfo-modal',
					minWidth: 400,
					maxWidth: 1200,
					maxHeight: '100%'
				});

				var $contents = jQuery('#ls-phpinfo').text();

				$modal.find('iframe').contents().find('html').html( $contents );
			});


			jQuery('.ls-erase-button').click(function() {

				kmw.modal.open({
					content: '#ls-erase-modal',
					minWidth: 400,
					maxWidth: 1000
				});
			});

		});
	</script>
</div>