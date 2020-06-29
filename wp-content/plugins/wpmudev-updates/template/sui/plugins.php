<?php
if( 'full' === $membership_type ):

	// Render the page header section.
	$page_title = __( 'Plugins', 'wpmudev' );
	$page_slug  = 'plugins';

	// plugins & update stats
	$support_thread_url = $urls->support_url;
	$update_plugins_num = $update_plugins;
	$total_active_plugins = isset( $active_projects['all'] ) ? absint( $active_projects['all'] ) : 0 ;
	$update_plugins_html = $update_plugins > 0 ? sprintf( '<span class="sui-tag sui-tag-warning sui-tag-sm"><a href="%s" style="color:#333">%s</a></span>', esc_url( $urls->plugins_url ), $update_plugins ) : __( 'All up to date', 'wpmudev' );

	/** @var $this WPMUDEV_Dashboard_Sui */
	$this->render_sui_header( $page_title, $page_slug );
	?>
	<div class="sui-box sui-summary sui-summary-sm">

		<div class="sui-summary-image-space" aria-hidden="true"></div>

			<div class="sui-summary-segment">

			<div class="sui-summary-details">
				<span class="sui-summary-large"><?php echo absint( $total_active_plugins ); ?></span>
				<span class="sui-summary-sub"><?php echo esc_html( _n( 'Active Pro plugin', 'Active Pro plugins', $total_active_plugins, 'wpmudev' ) ); ?></span>
			</div>

		</div>

		<div class="sui-summary-segment">

			<ul class="sui-list">

				<li>
					<span class="sui-list-label"><?php esc_html_e( 'Plugin Updates Available', 'wpmudev' ); ?> </span>
					<span class="sui-list-detail"><?php echo $update_plugins_html; //phpcs:ignore ?></span>
				</li>

				<li>
					<span class="sui-list-label"><?php esc_html_e( 'Total Active Plugins', 'wpmudev' ); ?></span>
					<span class="sui-list-detail">
						<?php echo count( get_option('active_plugins') ); //phpcs:ignore  ?>
					</span>
				</li>

			</ul>

		</div>

	</div><!-- End Overview -->

	<div class="sui-row-with-sidenav dashui-plugin-box ">
		<div class="sui-sidenav dashui-plugins-filter-tabs dashui-mobile-hidden">
			<div class="sui-sidenav-sticky sui-sidenav-hide-md">
				<div class="sui-tabs-menu sui-sidenav-sticky">
					<ul class="sui-vertical-tabs">
						<li class="sui-vertical-tab current">
							<a role="button"
							class="sui-tab-item wdev-all-tab active"
							data-filter="all"
							tabindex="1">
								<?php esc_html_e( 'All', 'wpmudev' ); ?>
							</a>
						</li>

						<!-- <li class="sui-vertical-tab">
							<a role="button"
								class="sui-tab-item"
								data-filter="activated"
								tabindex="2">
								<?php esc_html_e( 'Activated', 'wpmudev' ); ?>
							</a>
						</li>

						<li class="sui-vertical-tab">
							<a role="button"
								class="sui-tab-item"
								data-filter="deactivated"
								tabindex="3">
								<?php esc_html_e( 'Deactivated', 'wpmudev' ); ?>
							</a>
						</li> -->

						<?php if ( ! empty( $update_plugins ) && $update_plugins ) : ?>
							<li class="sui-vertical-tab">
								<a role="button"
									class="sui-tab-item wdev-update-tab"
									data-filter="hasupdate"
									data-count="<?php echo esc_attr( $update_plugins ); ?>"
									tabindex="4"
									style="display:inline-block; position:relative; width:40%"
									>
									<?php esc_html_e( 'Updates', 'wpmudev' ); ?> <span class="sui-tag sui-tag-yellow sui-tag-sm" style="right: -25px;"><?php echo esc_html( $update_plugins ); ?></span>
								</a>
							</li>
						<?php endif; ?>
					</ul>

				</div>

			</div>
			<div class="sui-sidenav-hide-lg" style="margin-bottom: 20px;">
				<select name="dashui-mobile-filter" class="sui-select-lg" id="dashui-mobile-filter">
					<option value="all"><?php esc_html_e( 'All', 'wpmudev' ); ?></option>
					<?php if ( ! empty( $update_plugins ) && $update_plugins ) : ?>
						<option value="hasupdate"><?php esc_html_e( 'Updates', 'wpmudev' ); ?></option>
					<?php endif; ?>
				</select>
			</div>
		</div>

		<div class="sui-box" id="dashui-all-plugins">

			<div class="sui-box-header">

				<h2 class="sui-box-title"><?php esc_html_e( 'All Plugins', 'wpmudev' ); ?></h2>

				<div class="sui-actions-right">

					<div class="sui-form-field dashui-plugins-filter-search">

						<label for="dashboard-plugins-search-field" id="dashboard-plugins-search-field-label" class="sui-screen-reader-text"></label>

						<div class="sui-control-with-icon">

							<input
								type="text"
								name="search"
								placeholder="<?php esc_html_e( 'Search plugins', 'wpmudev' ); ?>"
								id="dashboard-plugins-search-field"
								class="sui-form-control"
								aria-labelledby="dashboard-plugins-search-field-label"
							/>

							<i class="sui-icon-magnifying-glass-search" aria-hidden="true"></i>

						</div>

					</div>

				</div>

			</div><!-- end box header -->

			<div class="sui-box-body">

				<div class="sui-notice sui-notice-info js-no-result-search sui-hidden">
					<p class="js-no-result-search-message"></p>
				</div>

				<p style="margin-top: 0;"><?php esc_html_e( 'Install, update and configure our Pro plugins.', 'wpmudev' ); ?></p>

			</div>

			<div role="alert" class="sui-box-body dashui-plugin-loader">

				<p><?php printf( esc_html__( '%s Checking for updates, please wait…', 'wpmudev' ), '<i class="sui-icon-loader sui-md sui-loading" aria-hidden="true"></i>' ); ?></p>

				<img
					src="<?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/devman-loading.png' ); ?>"
					srcset="<?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/devman-loading.png' ); ?> 1x, <?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/devman-loading@2x.png' ); ?> 2x"
					alt="<?php esc_html_e( 'Dev-man is drinking a cup of coffee while waiting for content to load.' ); ?>"
					class="sui-image sui-image-center"
				/>

			</div>

			<table class="sui-table sui-table-flushed dashui-table-plugins" style="display: none;">

				<tbody>

					<tr class="dashui-bulk-action bulk-action-row js-plugins-bulk-action">

						<td colspan="3">

							<label for="bulk-actions-all"
								class="sui-checkbox">
								<input type="checkbox"
									name="all-actions"
									id="bulk-actions-all"
									class="js-plugin-check-all"/>
								<span aria-hidden="true"></span>
								<span class="sui-screen-reader-text"><?php esc_html_e( 'Select all plugins', 'wpmudev' ); ?></span>
							</label>

							<select name="current-bulk-action"
									class="sui-select-sm sui-select-inline">
								<option value=""><?php esc_html_e( 'Bulk Actions', 'wpmudev' ); ?></option>
								<option value="update"><?php esc_html_e( 'Update', 'wpmudev' ); ?></option>
								<option value="activate"><?php esc_html_e( 'Activate', 'wpmudev' ); ?></option>
								<option value="install"><?php esc_html_e( 'Install', 'wpmudev' ); ?></option>
								<option value="deactivate"><?php esc_html_e( 'Deactivate', 'wpmudev' ); ?></option>
								<option value="delete"><?php esc_html_e( 'Delete', 'wpmudev' ); ?></option>
							</select>

							<button class="sui-button sui-button-ghost js-plugins-bulk-action-button"
									disabled="disabled">
								<?php esc_html_e( 'Apply', 'wpmudev' ); ?>
							</button>

						</td>

					</tr>

				</tbody>

			</table>

			<div class="sui-box-body">

				<?php $this->load_sui_template( 'element-last-refresh', array(), true ); ?>

			</div>

		</div>

	</div><!-- End Plugin Box -->

	<div class="sui-hidden">
		<?php
		foreach ( $data['projects'] as $project ) {
			if ( empty( $project['id'] ) ) {
				continue;
			}
			if ( 'plugin' !== $project['type'] ) {
				continue;
			}

			$this->render_project( $project['id'] );
		}
		?>

		<div class="js-notifications">
			<div class="sui-notice-top sui-notice-success js-activated-single">
				<div class="sui-notice-content">
					<p><strong><?php esc_html_e( 'Success', 'wpmudev' ); ?>:</strong> <?php esc_html_e( 'Plugin activated successfully.', 'wpmudev' ); ?></p>
					<p><?php esc_html_e( 'Please wait while we refresh the page...', 'wpmudev' ); ?></p>
				</div>
			</div>
			<div class="sui-notice-top sui-notice-error sui-can-dismiss js-failed-activated-single">
				<div class="sui-notice-content">
					<p><strong><?php esc_html_e( 'Failed', 'wpmudev' ); ?>:</strong> <?php esc_html_e( 'Plugin failed to be activated.', 'wpmudev' ); ?></p>
					<p class="js-custom-message"></p>
				</div>
				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>
			</div>
			<div class="sui-notice-top sui-notice-success js-activated-multi">
				<div class="sui-notice-content">
					<p><strong><?php esc_html_e( 'Success', 'wpmudev' ); ?>:</strong> <?php esc_html_e( 'Plugins activated.', 'wpmudev' ); ?></p>
					<p><?php esc_html_e( 'Please wait while we refresh the page...', 'wpmudev' ); ?></p>
				</div>
			</div>

			<div class="sui-notice-top sui-notice-success js-deactivated-single">
				<div class="sui-notice-content">
					<p><strong><?php esc_html_e( 'Success', 'wpmudev' ); ?>:</strong> <?php esc_html_e( 'Plugin deactivated.', 'wpmudev' ); ?></p>
					<p><?php esc_html_e( 'Please wait while we refresh the page...', 'wpmudev' ); ?></p>
				</div>
			</div>
			<div class="sui-notice-top sui-notice-success js-deactivated-multi">
				<div class="sui-notice-content">
					<p><strong><?php esc_html_e( 'Success', 'wpmudev' ); ?>:</strong> <?php esc_html_e( 'Plugins deactivated.', 'wpmudev' ); ?></p>
					<p><?php esc_html_e( 'Please wait while we refresh the page...', 'wpmudev' ); ?></p>
				</div>
			</div>

			<div class="sui-notice-top sui-notice-success sui-can-dismiss js-installed-single">
				<div class="sui-notice-content">
					<p><strong><?php esc_html_e( 'Success', 'wpmudev' ); ?>:</strong> <?php esc_html_e( 'Plugin successfully installed.', 'wpmudev' ); ?></p>
				</div>
				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>
			</div>
			<div class="sui-notice-top sui-notice-error sui-can-dismiss js-failed-installed-single">
				<div class="sui-notice-content">
					<p><strong><?php esc_html_e( 'Failed', 'wpmudev' ); ?>:</strong> <?php esc_html_e( 'Plugin failed to be installed.', 'wpmudev' ); ?></p>
					<p class="js-custom-message"></p>
				</div>
				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>
			</div>

			<div class="sui-notice-top sui-notice-success sui-can-dismiss js-deleted-single">
				<div class="sui-notice-content">
					<p><strong><?php esc_html_e( 'Success', 'wpmudev' ); ?>:</strong> <?php esc_html_e( 'Plugin successfully deleted.', 'wpmudev' ); ?></p>
				</div>
				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>
			</div>
			<div class="sui-notice-top sui-notice-error sui-can-dismiss js-failed-deleted-single">
				<div class="sui-notice-content">
					<p><strong><?php esc_html_e( 'Failed', 'wpmudev' ); ?>:</strong> <?php esc_html_e( 'Plugin failed to be deleted.', 'wpmudev' ); ?></p>
					<p class="js-custom-message"></p>
				</div>
				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>
			</div>

			<div class="sui-notice-top sui-notice-success sui-can-dismiss js-updated-single">
				<div class="sui-notice-content">
					<p><strong><?php esc_html_e( 'Success', 'wpmudev' ); ?>:</strong> <?php esc_html_e( 'Plugin successfully updated.', 'wpmudev' ); ?></p>
				</div>
				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>
			</div>
			<div class="sui-notice-top sui-notice-error sui-can-dismiss js-failed-updated-single">
				<div class="sui-notice-content">
					<p><strong><?php esc_html_e( 'Failed', 'wpmudev' ); ?>:</strong> <?php esc_html_e( 'Plugin failed to be updated.', 'wpmudev' ); ?></p>
					<p class="js-custom-message"></p>
				</div>
				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>
			</div>

			<div class="sui-notice-top sui-notice-success sui-can-dismiss js-updated-bulk">
				<div class="sui-notice-content">
					<p><strong><?php esc_html_e( 'Success', 'wpmudev' ); ?>:</strong> <?php esc_html_e( 'Plugins successfully updated.', 'wpmudev' ); ?></p>
				</div>
				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>
			</div>

			<div class="sui-notice-top sui-notice-success sui-can-dismiss js-installed-bulk">
				<div class="sui-notice-content">
					<p><strong><?php esc_html_e( 'Success', 'wpmudev' ); ?>:</strong> <?php esc_html_e( 'Plugins successfully installed.', 'wpmudev' ); ?></p>
				</div>
				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>
			</div>

			<div class="sui-notice-top sui-notice-success sui-can-dismiss js-deleted-bulk">
				<div class="sui-notice-content">
					<p><strong><?php esc_html_e( 'Success', 'wpmudev' ); ?>:</strong> <?php esc_html_e( 'Plugins successfully deleted.', 'wpmudev' ); ?></p>
				</div>
				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>
			</div>


			<div class="sui-notice-top sui-notice-error sui-can-dismiss js-general-fail">
				<div class="sui-notice-content">
					<p><strong><?php esc_html_e( 'Failed', 'wpmudev' ); ?>:</strong> <?php esc_html_e( 'Whoops, we had an unexpected response from WordPress, please try again.', 'wpmudev' ); ?></p>
				</div>
				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>
			</div>
		</div>

	</div>

	<?php // bulk action ?>
	<div class="sui-dialog" aria-hidden="true" tabindex="-1" id="bulk-action-modal">

		<div class="sui-dialog-overlay" data-a11y-dialog-hide></div>

		<div class="sui-dialog-content" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="alertdialog">

			<div class="sui-box" role="document">

				<div class="sui-box-header">
					<h3 class="sui-box-title" id="dialogTitle"><?php esc_html_e( 'Bulk Actions', 'wpmudev' ); ?></h3>
					<div class="sui-actions-right">
						<a data-a11y-dialog-hide class="sui-dialog-close" aria-label="<?php esc_html_e( 'Close this dialog window', 'wpmudev' ); ?>"></a>
					</div>
				</div>

				<div class="sui-box-body">

					<div class="sui-notice sui-notice-warning js-bulk-errors" style="text-align:left">
					</div>

					<div class="sui-notice js-bulk-message-need-reload" style="text-align:left">
						<p><?php esc_html_e( 'This page need to be reloaded before changes you just made become visible.', 'wpmudev' ); ?></p>
						<div class="sui-notice-buttons">
							<a href="" class="sui-button"><?php esc_html_e( 'Reload now', 'wpmudev' ); ?></a>
						</div>
					</div>

					<div class="sui-progress-block">

						<div class="sui-progress">

							<span class="sui-progress-icon js-bulk-actions-loader-icon" aria-hidden="true">
								<i class="sui-icon-loader sui-loading"></i>
							</span>

							<span class="sui-progress-text">
								<span>0%</span>
							</span>

							<div class="sui-progress-bar" aria-hidden="true">
								<span style="width: 0%" class="js-bulk-actions-progress"></span>
							</div>
						</div>
					</div>

					<div class="sui-progress-state">
						<span class="js-bulk-actions-state"></span>
					</div>

				</div>


				<div class="sui-hidden js-bulk-hash"
				     data-activate="<?php echo esc_attr( wp_create_nonce( 'project-activate' ) ); ?>"
				     data-deactivate="<?php echo esc_attr( wp_create_nonce( 'project-deactivate' ) ); ?>"
				     data-install="<?php echo esc_attr( wp_create_nonce( 'project-install' ) ); ?>"
				     data-delete="<?php echo esc_attr( wp_create_nonce( 'project-delete' ) ); ?>"
				     data-update="<?php echo esc_attr( wp_create_nonce( 'project-update' ) ); ?>"
				>

				</div>

				<div style="display:none">
					<?php
					/**
					 * ROW FOR NOT INSTALLED PLUGIN LIST TABLE
					 */
					?>
					<div class="js-available-plugin-header">
						<table>
							<tr class="dashui-tr-header">
								<td><p><?php esc_html_e( 'Available', 'wpmudev' ); ?></p></td>
								<td></td>
								<td></td>
							</tr>
						</table>
					</div>

				</div>

			</div>

		</div>
	</div>

	<?php $this->load_sui_template( 'footer', array(), true ); ?>
<?php endif; ?>

<?php
if ( 'free' === $membership_type || 'single' === $membership_type ) {
	$this->render_upgrade_box( $membership_type );
}
if ( ! WPMUDEV_Dashboard::$upgrader->can_auto_install( 'plugin' ) ) {
	$this->load_sui_template( 'popup-ftp-details', array(), true );
}
?>