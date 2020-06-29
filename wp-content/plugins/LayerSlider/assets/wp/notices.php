<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

add_action('admin_init', function() {

	add_action('admin_notices', 'layerslider_important_notice');

	if( ! empty( $_GET['page'] ) && $_GET['page'] === 'layerslider' ) {
		add_action('admin_notices', 'layerslider_update_notice');
		add_action('admin_notices', 'layerslider_dependency_notice');

		if( get_option('ls-show-canceled_activation_notice', 0) ) {
			add_action('admin_notices', 'layerslider_canceled_activation');
			update_user_meta( get_current_user_id(), 'ls-show-support-notice-timestamp', time() - WEEK_IN_SECONDS * 3 );

		} else {

			if( LS_Config::get('notices') && ! LS_Config::isActivatedSite() ) {

				// Make sure to set an initial timestamp for the notice.
				if( ! $lastCheck = get_user_meta( get_current_user_id(), 'ls-show-support-notice-timestamp', true ) ) {
					$lastCheck = time() - WEEK_IN_SECONDS * 3;
					update_user_meta( get_current_user_id(), 'ls-show-support-notice-timestamp', $lastCheck );
				}

				if( time() - ( DAY_IN_SECONDS * 31 ) > $lastCheck ) {
					add_action('admin_notices', 'layerslider_premium_notice');
				}
			}
		}
	}

	// Storage notice
	if(get_option('layerslider-slides') !== false) {

		global $pagenow;
		if($pagenow == 'plugins.php' || $pagenow == 'index.php' || strpos($_SERVER['REQUEST_URI'], 'layerslider')) {
			add_action('admin_notices', 'layerslider_compatibility_notice');
		}
	}

	// License notification under the plugin row on the Plugins screen
	if( ! LS_Config::isActivatedSite() ) {
		add_action('after_plugin_row_'.LS_PLUGIN_BASE, 'layerslider_plugins_screen_notice', 10, 3 );
	}
});


function layerslider_important_notice() {

	// Get data
	$storeData 	= get_option('ls-store-data', false);
	$lastNotice = get_option('ls-last-important-notice', 0);

	// Check if there's an important notice
	if( $storeData && ! empty( $storeData['important_notice'] ) ) {

		// Get notice data
		$notice = $storeData['important_notice'];

		// Check notice validity
		if( ! empty($notice['date']) && ! empty($notice['title']) && ! empty($notice['message']) ) {

			// Check date
			if( $notice['date'] <= $lastNotice ) {
				return;
			}


			// Check min version (if any)
			if( ! empty( $notice['min_version'] ) ) {
				if( version_compare( LS_PLUGIN_VERSION, $notice['min_version'], '<' ) ) {
					return;
				}
			}


			// Check max version (if any)
			if( ! empty( $notice['max_version'] ) ) {
				if( version_compare( LS_PLUGIN_VERSION, $notice['max_version'], '>' ) ) {
					return;
				}
			}

			// Check audience
			if( ! empty( $notice['unactivated'] ) ) {
				if( LS_Config::isActivatedSite() ) {
					return;
				}
			}

			// Check target pages
			if( ! empty( $notice['url_filter'] ) ) {

				$matches = 0;
				$fragments = explode( ' ', $notice['url_filter'] );

				foreach( $fragments as $fragment ) {
					if( strpos( $_SERVER['REQUEST_URI'], $fragment ) !== false) {
						$matches++;
						break;
					}
				}

				if( ! $matches ) {
					return;
				}
			}

			// Show the notice

			if( ! empty( $notice['banner'] ) ) {
				$bannerStyle = ! empty( $notice['banner']['style'] ) ? $notice['banner']['style'] : '';
				$bannerClass = ! empty( $notice['banner']['class'] ) ? $notice['banner']['class'] : '';

			 ?>

			<div class="layerslider_notice_img <?php echo $bannerClass ?>" style="<?php echo $bannerStyle ?>">

				<?php if( ! empty( $notice['banner']['url'] ) ) : ?>
				<a href="<?php echo $notice['banner']['url'] ?>" target="<?php echo $notice['banner']['target'] ?>" class="banner_link"></a>
				<?php endif ?>


				<?php if( ! empty( $notice['banner']['content'] ) ) : ?>
				<?php echo $notice['banner']['content'] ?>
				<?php endif ?>

				<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=layerslider&action=hide-important-notice' ), 'hide-important-notice') ?>" class="dashicons dashicons-dismiss" data-help="<?php _e('Hide this banner', 'LayerSlider') ?>"></a>

				<?php if( ! empty( $notice['button'] ) && ! empty( $notice['button']['text']) ) : ?>
				<a href="<?php echo $notice['button']['url'] ?>" target="<?php echo $notice['button']['target'] ?>" class="button button-read-more" style="<?php echo ! empty( $notice['button']['style'] ) ? $notice['button']['style'] : ''; ?>">
					<?php echo $notice['button']['text'] ?>
				</a>
				<?php endif ?>
			</div>

			<?php } else { ?>

			<div class="layerslider_notice">
				<img src="<?php echo ! empty($notice['image']) ? $notice['image'] : LS_ROOT_URL.'/static/admin/img/ls-logo.png' ?>" class="ls-product-icon" alt="LayerSlider icon">
				<h1><?php echo $notice['title'] ?></h1>
				<p>
					<?php echo $notice['message'] ?>
					<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=layerslider&action=hide-important-notice' ), 'hide-important-notice') ?>" class="button button-primary button-hero"><?php _e('OK, I understand', 'LayerSlider') ?></a>
				</p>
				<div class="clear"></div>
			</div>

			<?php }
		}
	}
}

function layerslider_update_notice() {

	$activated 	= LS_Config::isActivatedSite();
	$updates 	= get_plugin_updates();
	$latest 	= get_option( 'ls-latest-version', false );

	$last_notification = get_option( 'ls-last-update-notification', LS_PLUGIN_VERSION );


	// Check PHP and WP requirements
	if( ! empty( $updates[ LS_PLUGIN_BASE ]->update ) ) {

		$update 		= $updates[ LS_PLUGIN_BASE ];
		$currentVersion = $update->Version;
		$newVersion 	= $update->update->new_version;

		if( version_compare( $newVersion, $currentVersion, '>' ) ) {

			$php_version 	= phpversion();
			$requires_php 	= $update->update->requires_php;

			$wp_version  	= get_bloginfo('version');
			$requires_wp 	= $update->update->requires;

			$php_issue 		= version_compare( $php_version, $requires_php, '<' );
			$wp_issue 		= version_compare( $wp_version, $requires_wp, '<' );

			if( $php_issue || $wp_issue ) {
				?><div class="layerslider_notice">
					<img src="<?php echo LS_ROOT_URL.'/static/admin/img/ls-logo.png' ?>" class="ls-product-icon" alt="LayerSlider icon">
					<h1><?php _e('An update is available for LayerSlider WP!', 'LayerSlider') ?></h1>
					<p class="no-padding">
						<?php echo sprintf(__('You have version %1$s. Update to version %2$s.', 'LayerSlider'), $currentVersion, $newVersion); ?><br>

						<?php if( $php_issue ) : ?>
						<span class="color-red"><?php echo sprintf( __('This update requires PHP %s or greater. You have PHP %s. %sLearn more about updating PHP%s', 'LayerSlider'), $requires_php, $php_version, '<a href="https://wordpress.org/support/update-php/" target="_blank">', '</a>' ) ?></span><br>
						<?php endif ?>

						<?php if( $wp_issue ) : ?>
						<span class="color-red"><?php echo sprintf( __('This update requires WordPress %s or greater. You have WordPress %s. %sLearn more about updating WordPress%s', 'LayerSlider'), $requires_wp, $wp_version, '<a href="https://wordpress.org/about/requirements/" target="_blank">', '</a>' ) ?></span><br>
						<?php endif ?>

						<i><?php echo $update->update->upgrade_notice ?></i>
					</p>
				</div><?php

				// IMPORTANT: prevent further execution of this function
				// when there's a PHP or WordPress issue.
				return;
			}
		}
	}


	if( ! empty( $updates[ LS_PLUGIN_BASE ]->update->_banner ) ) {

		if( version_compare( $last_notification, $latest, '<' ) ) {

			$update = $updates[ LS_PLUGIN_BASE ]->update;
			$banner = $update->_banner;

			$bannerStyle = ! empty( $banner['style'] ) ? $banner['style'] : '';
			$bannerClass = ! empty( $banner['class'] ) ? $banner['class'] : '';

		 ?>
			<div class="layerslider_notice_img <?php echo $bannerClass ?>" style="<?php echo $bannerStyle ?>">

				<?php if( ! empty( $banner['url'] ) ) : ?>
				<a href="<?php echo $banner['url'] ?>" target="<?php echo $banner['target'] ?>" class="banner_link"></a>
				<?php endif ?>


				<?php if( ! empty( $banner['content'] ) ) : ?>
				<?php echo $banner['content'] ?>
				<?php endif ?>


				<?php if( ! $activated ) : ?>
				<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=layerslider&action=hide-update-notice' ), 'hide-update-notice') ?>" class="dashicons dashicons-dismiss" data-help="<?php _e('Hide this banner', 'LayerSlider') ?>"></a>
				<?php endif ?>

				<a href="<?php echo wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin='.LS_PLUGIN_BASE), 'upgrade-plugin_'.LS_PLUGIN_BASE ) ?>" class="button button-install" title="<?php _e('Install now', 'LayerSlider') ?>">
						<?php _e('Install now', 'LayerSlider') ?>
				</a>
			</div>
		<?php
		}

	} elseif( $activated ) {

		// Check for update
		if( isset( $updates[ LS_PLUGIN_BASE ] ) && isset( $updates[ LS_PLUGIN_BASE ]->update ) ) {
			$update 		= $updates[ LS_PLUGIN_BASE ];
			$currentVersion = $update->Version;
			$newVersion 	= $update->update->new_version;

			if( version_compare( $newVersion, $currentVersion, '>' ) ) {
			?>
			<div class="layerslider_notice">
				<img src="<?php echo LS_ROOT_URL.'/static/admin/img/ls-logo.png' ?>" class="ls-product-icon" alt="LayerSlider icon">
				<h1><?php _e('An update is available for LayerSlider WP!', 'LayerSlider') ?></h1>
				<p>
					<?php echo sprintf(__('You have version %1$s. Update to version %2$s.', 'LayerSlider'), $currentVersion, $newVersion); ?><br>
					<i><?php echo $update->update->upgrade_notice ?></i>
					<a href="<?php echo wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin='.LS_PLUGIN_BASE), 'upgrade-plugin_'.LS_PLUGIN_BASE) ?>" class="button button-primary button-hero" title="<?php _e('Install now', 'LayerSlider') ?>">
						<?php _e('Install now', 'LayerSlider') ?>
					</a>
				</p>
				<div class="clear"></div>
			</div>
			<?php
			}
		}
	} elseif( ! $activated ) {

		$latest = get_option( 'ls-latest-version', false );
		if( $latest && version_compare( LS_PLUGIN_VERSION, $latest, '<' ) ) {
			$last_notification = get_option( 'ls-last-update-notification', LS_PLUGIN_VERSION );
			if( version_compare( $last_notification, $latest, '<' ) ) {
			?>
			<div class="layerslider_notice">
				<img src="<?php echo LS_ROOT_URL.'/static/admin/img/ls-logo.png' ?>" class="ls-product-icon" alt="LayerSlider icon">
				<h1><?php _e('An update is available for LayerSlider WP!', 'LayerSlider') ?></h1>
				<p>
					<?php echo sprintf(__('You have version %1$s. The latest version is %2$s.', 'LayerSlider'), LS_PLUGIN_VERSION, $latest); ?><br>
					<i><?php _e('New releases contain new features, bug fixes and various improvements across the entire plugin.', 'LayerSlider') ?></i>
					<i><?php echo sprintf(__('Set up auto-updates to upgrade to this new version, or request it from the author of your theme if you’ve received LayerSlider from them. %sClick here%s to learn more.', 'LayerSlider'), '<a href="https://layerslider.kreaturamedia.com/documentation/#updating" target="_blank">', '</a>') ?></i>
					<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=layerslider&action=hide-update-notice' ), 'hide-update-notice') ?>" class="button button-extra"><?php _e('Hide this message', 'LayerSlider') ?></a>
				</p>
				<div class="clear"></div>
			</div><?php
			}
		}
	}

}


function layerslider_compatibility_notice() { ?>
	<div class="layerslider_notice">
		<img src="<?php echo LS_ROOT_URL.'/static/admin/img/ls-logo.png' ?>" class="ls-product-icon" alt="LayerSlider icon">
		<h1><?php _e('The new version of LayerSlider WP is almost ready!', 'LayerSlider') ?></h1>
		<p>
			<?php _e('For a faster and more reliable solution, LayerSlider WP needs to convert your data associated with the plugin. Your sliders and settings will remain still, and it only takes a click on this button.', 'LayerSlider') ?>

			<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=layerslider&action=convert' ), 'convertoldsliders') ?>" class="button button-primary button-hero">
				<?php _e('Convert Data', 'LayerSlider') ?>
			</a>
		</p>
		<div class="clear"></div>
	</div>
<?php }

function layerslider_dependency_notice() {

	if( ! class_exists('DOMDocument') ) {
	?>
	<div class="layerslider_notice">
		<img src="<?php echo LS_ROOT_URL.'/static/admin/img/ls-logo.png' ?>" class="ls-product-icon" alt="LayerSlider icon">
		<h1><?php _e('Server configuration issues detected!', 'LayerSlider') ?></h1>
		<p>
			<?php echo sprintf(__('LayerSlider and its external dependencies require PHP’s DOM extension. Please contact with your server hosting provider to resolve this issue, as it will likely prevent LayerSlider from functioning properly. %sThis issue could result a blank page in slider builder.%s Check %sSystem Status%s for more information and comprehensive test about your server environment.', 'LayerSlider'), '<strong>', '</strong>', '<a href="'.admin_url('admin.php?page=layerslider-options&section=system-status').'">', '</a>' ) ?>

			<a href="<?php echo admin_url('admin.php?page=layerslider-options&section=system-status') ?>" class="button button-primary"><?php _e('Check System Status', 'LayerSlider') ?></a>
		</p>
		<div class="clear"></div>
	</div>
<?php } }

function layerslider_premium_notice() { ?>
<div class="layerslider_notice">
	<img src="<?php echo LS_ROOT_URL.'/static/admin/img/ls-logo.png' ?>" class="ls-product-icon" alt="LayerSlider icon">
		<h1><?php _e('Unlock the full potential of LayerSlider', 'LayerSlider') ?></h1>
		<p>
			<?php echo sprintf(
				__('Activate LayerSlider to unlock premium features, slider templates and other exclusive content & services. Receive live plugin updates with 1-Click installation (including optional early access releases) and premium support. Please read our %sdocumentation%s for more information. %sGot LayerSlider with a theme?%s', 'LayerSlider'),
				'<a href="https://layerslider.kreaturamedia.com/documentation/#activation" target="_blank">',
				'</a>',
				'<a href="https://layerslider.kreaturamedia.com/documentation/#activation-bundles" target="_blank">',
				'</a>')
			?>
			<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=layerslider&action=hide-support-notice' ), 'hide-support-notice') ?>" class="button"><?php _e('Hide this message', 'LayerSlider') ?></a>
		</p>
	<div class="clear"></div>
</div>

<?php }

function layerslider_plugins_screen_notice( $plugin_file, $plugin_data, $status ) {
	$table = _get_list_table('WP_Plugins_List_Table');
	if( empty( $plugin_data['update'] ) ) {
	?>
	<tr class="plugin-update-tr active ls-plugin-update-row" data-slug="<?php echo LS_PLUGIN_SLUG ?>" data-plugin="<?php echo LS_PLUGIN_BASE ?>">
		<td colspan="<?php echo $table->get_column_count(); ?>" class="plugin-update colspanchange">
			<div class="update-message notice inline notice-warning notice-alt">
				<p>
					<?php
						printf(__('License activation is required in order to receive updates and premium support for LayerSlider. %sPurchase a license%s or %sread the documentation%s to learn more. %sGot LayerSlider in a theme?%s', 'installer'),
							'<a href="'.LS_Config::get('purchase_url').'" target="_blank">', '</a>', '<a href="https://layerslider.kreaturamedia.com/documentation/#activation" target="_blank">', '</a>', '<a href="https://layerslider.kreaturamedia.com/documentation/#activation-bundles" target="_blank">', '</a>');
					?>
				</p>
			</div>
		</td>
	</tr>
<?php } }

function layerslider_canceled_activation() { ?>
	<div class="layerslider_notice">
	<img src="<?php echo LS_ROOT_URL.'/static/admin/img/ls-logo.png' ?>" class="ls-product-icon" alt="LayerSlider icon">
	<h1><?php _e('LayerSlider product activation was canceled on this site', 'LayerSlider') ?></h1>
	<p>
		<?php _e('LayerSlider was previously activated on this site to receive live plugin updates, exclusive features, and premium templates in the Template Store. However, it seems that your LayerSlider product activation is no longer valid and these benefits are unavailable until you re-activate your license.', 'LayerSlider') ?>

		<br><br>
		 <?php echo sprintf(__('There are a number of potential reasons why this could happen, the common ones include: you’ve remotely deactivated your site using our online tools or asked us to do the same on your behalf; you’re using a non-genuine copy of LayerSlider; your purchase has been refunded or the transaction disputed. To review all the possible reasons and find out what to do next, please refer to the %sWhy was my activation canceled?%s section in our documentation.', 'LayerSlider'), '<a target="_blank" href="https://layerslider.kreaturamedia.com/documentation/#canceled-activation">', '</a>') ?>


		<a href="<?php echo wp_nonce_url( admin_url('admin.php?page=layerslider&action=hide-canceled-activation-notice' ), 'hide-canceled-activation-notice') ?>" class="button button-primary button-hero"><?php _e('OK, I understand', 'LayerSlider') ?></a>
	</p>
	<div class="clear"></div>
</div>
<?php }

