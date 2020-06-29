<?php
if( 'full' === $type ):
	// Render the page header section.
	/** @var WPMUDEV_Dashboard_Sui $this */
	$page_title = __( 'Dashboard', 'wpmudev' );
	$page_slug  = 'dashboard';
	$this->render_sui_header( $page_title, $page_slug );

	/** @var WPMUDEV_Dashboard_Sui_Page_Urls $urls */
	// Support & update stats
	$support_thread_url = $urls->support_url;

	$support_threads = count( $member['forum']['support_threads'] );
	$support_threads = $support_threads > 0 ? sprintf( '<span class="sui-tag sui-tag-sm sui-tag-branded"><a href="%s" style="color:#fff">%s</a></span>', esc_url( $support_thread_url ), absint( $support_threads ) ) :  absint( $support_threads );

	$update_plugins_html = $update_plugins > 0 ? sprintf( '<span class="sui-tag sui-tag-sm sui-tag-warning"><a href="%s" style="color:#333">%s</a></span>', esc_url( $urls->plugins_url ), $update_plugins ) : $update_plugins;
	$total_active_plugins = isset( $active_projects['all'] ) ? absint( $active_projects['all'] ) : 0 ;

	// Find the 5 most popular plugins, that are not installed yet.
	$selected_plugins = array();
	asort( $data['projects'] );
	$projects = wp_list_pluck( $data['projects'], 'id', 'name' );

	//sort by name
	ksort( $projects );
	if( $update_plugins > 0 ):
		foreach ( $projects as $key => $item ) {
			//if update is complete break
			if( $update_plugins <= count( $selected_plugins ) ){
				break;
			}

			// Skip themes.
			if ( 'plugin' != $data['projects'][$item]['type'] ) {
				continue;
			}

			$plugin = WPMUDEV_Dashboard::$site->get_project_infos( $item );
			//get the updates first
			if( ! $plugin->has_update ){
				continue;
			}

			$selected_plugins[] = $plugin->pid;
		}
	endif;

	foreach ( $projects as $key => $item ) {
		// Skip themes.
		if ( 'plugin' != $data['projects'][$item]['type'] ) {
			continue;
		}

		$plugin = WPMUDEV_Dashboard::$site->get_project_infos( $item );

		//if update is complete break
		if( 5 <= count( $selected_plugins ) ){
			break;
		}

		//ignore plugin with updates
		if( $plugin->has_update ){
			continue;
		}

		// Skip plugin if it's already installed.
		if ( ! $plugin->is_active ) {
			continue;
		}

		// Skip plugins that are not compatible with current site.
		if ( ! $plugin->is_compatible ) {
			continue;
		}

		// Skip hidden/deprecated projects.
		if ( $plugin->is_hidden ) {
			continue;
		}

		$selected_plugins[] = $plugin->pid;

	}
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
					<span class="sui-list-label"><?php esc_html_e( 'Active Support Tickets', 'wpmudev' ); ?></span>
					<span class="sui-list-detail">
						<?php echo $support_threads; //phpcs:ignore  ?>
					</span>
				</li>

			</ul>

		</div>

	</div><!-- End Overview -->

	<div class="sui-row dashui-table-widgets">
		<div class="sui-col-md-6">
			<?php // BOX: Installed Plugins ?>
			<?php $this->load_sui_template( '/dashboard-templates/installed-plugins', compact( 'data', 'urls', 'selected_plugins' ), true ); ?>

			<?php // BOX: Services ?>
			<?php $this->load_sui_template( '/dashboard-templates/services', compact( 'urls', 'membership_data' ), true ); ?>

			<?php // BOX: Support ?>
			<?php $this->load_sui_template( '/dashboard-templates/support', compact( 'urls', 'member', 'staff_login' ), true ); ?>
		</div>

		<div class="sui-col-md-6">

			<?php // BOX: Tools ?>
			<?php $this->load_sui_template( '/dashboard-templates/tools', compact( 'urls', 'whitelabel_settings', 'analytics_enabled', 'total_visits' ), true ); ?>

			<?php // BOX: Resources ?>
			<?php $this->load_sui_template( '/dashboard-templates/resources', compact( 'urls' ), true ); ?>

		</div>
	</div>

	<div class="sui-hidden">
		<?php
		foreach ( $selected_plugins as $project ) {
			$this->render_project( $project );
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
	<?php
	$this->load_sui_template( 'element-last-refresh', array(), true );

	$this->load_sui_template( 'footer', array(), true );
endif;

if ( 'free' === $type || 'single' === $type ) :
	$this->render_upgrade_box( $type );
endif;

