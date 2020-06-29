<?php

// Skip if project-ID is invalid.
$pid = intval( $pid );
if ( ! $pid ) {
	return;
}

$res = WPMUDEV_Dashboard::$site->get_project_infos( $pid );

// Skip invalid projects.
if ( empty( $res->pid ) || empty( $res->name ) ) {
	return;
}

// Skip hidden projects.
if ( $res->is_hidden ) {
	return;
}

$hashes = array(
	'project-activate'   => wp_create_nonce( 'project-activate' ),
	'project-deactivate' => wp_create_nonce( 'project-deactivate' ),
	'project-install'    => wp_create_nonce( 'project-install' ),
	'project-delete'     => wp_create_nonce( 'project-delete' ),
	'project-update'     => wp_create_nonce( 'project-update' ),
	'project-upgrade'    => wp_create_nonce( 'project-upgrade' ),
	'project-download'   => wp_create_nonce( 'project-download' ),
);

$main_action          = array();
$actions              = array();
$is_single_action     = false;
$actions_icon         = 'sui-icon-plus';
$main_action_class    = 'sui-button-blue';
$main_action_class_modal = 'sui-button-blue';
$show_num_install     = false;
$allow_description    = false;
$num_install          = 0;
$rounded_num_install  = 0;
$modal_install_button = array();
$incompatible_reason  = '';

if ( ! $res->is_installed ) {
	$is_single_action    = true;
	$show_num_install    = false;
	$allow_description   = true;
	$num_install         = (int) $res->downloads;
	$rounded_num_install = $num_install;
	if ( $num_install > 999 ) {
		$rounded_num_install = ceil( ( $num_install / 1000 ) ) . 'k';
	}
	if ( $num_install > 999999 ) {
		$rounded_num_install = ceil( ( $num_install / 1000000 ) ) . 'm';
	}

	/*
	 * Plugin is not installed yet.
	 * Possible Actions: Install, Download, Incompatible, Upgrade Membership.
	 */
	$actions_icon = 'sui-icon-plus';

	if ( ! $res->is_licensed ) {
		$main_action = array(
			'name' => __( 'Upgrade Membership', 'wpmudev' ),
			'url'  => $urls->trial_url,
			'icon' => 'sui-wpmudev-logo',
			'type' => 'none',
		);
	} elseif ( $res->is_compatible && $res->url->install ) {
		$actions              = array(
			'install' => array(
				'name' => __( 'Install', 'wpmudev' ),
				'url'  => $res->url->install,
				'type' => 'modal-ajax',
				'icon' => 'sui-icon-download',
				'data' => array(
					'action'  => 'project-install',
					'hash'    => $hashes['project-install'],
					'project' => $pid,
				),
			),
		);
		$modal_install_button = array(
			'name'  => __( 'Install', 'wpmudev' ),
			'class' => 'sui-button-blue',
			'url'   => $res->url->install,
			'type'  => 'modal-ajax',
			'icon'  => 'sui-icon-plus',
			'data'  => array(
				'action'  => 'project-install',
				'hash'    => $hashes['project-install'],
				'project' => $pid,
			),
		);
	} elseif ( $res->is_compatible ) {
		$actions = array(
			'download' => array(
				'name' => '',
				'url'  => $res->url->download,
				'type' => 'ajax',
				'icon' => 'sui-icon-download',
				'data' => array(
					'action'  => 'project-download',
					'hash'    => $hashes['project-download'],
					'project' => $pid,
				),
			),

		);
	} else {
		$incompatible_reason = $res->incompatible_reason;
		if ( empty( $incompatible_reason ) ) {
			$incompatible_reason = __( 'Incompatible', 'wpmudev' );
		}
	}

} else {
	/*
	 * Plugin is installed.
	 * Possible Actions: Update, Activate, Deactivate, Install Upfront, Configure, Delete.
	 */
	$is_single_action = false;
	$actions_icon     = 'sui-icon-widget-settings-config';

	//update always prioritized
	if ( $res->has_update ) {
		$main_action = array(
			'name' => __( 'Update', 'wpmudev' ),
			'url'  => '',
			'type' => 'modal-ajax',
			'icon' => 'sui-icon-download',
			'data' => array(
				'action'  => 'project-update',
				'hash'    => $hashes['project-update'],
				'project' => $pid,
			),
		);

		$actions['update'] = array(
			'name' => __( 'Update', 'wpmudev' ),
			'url'  => '#update=' . $pid,
			'type' => 'modal-ajax',
			'icon' => 'sui-icon-download',
			'data' => array(
				'action'  => 'project-update',
				'hash'    => $hashes['project-update'],
				'project' => $pid,
			),
		);

		$actions['changelog'] = array(
			'name' => __( 'View Changelog', 'wpmudev' ),
			'url'  => '#update=' . $pid,
			'type' => 'modal-ajax',
			'icon' => 'sui-icon-list-bullet',
			'data' => array(
				'action'  => 'project-update',
				'hash'    => $hashes['project-update'],
				'project' => $pid,
			),
		);

		//activate, configure, delete
		if ( ! $res->is_active ) {
			$actions['activate'] = array(
				'name' => ( $res->is_network_admin ? __( 'Network Activate', 'wpmudev' ) : __( 'Activate', 'wpmudev' ) ),
				'url'  => '#activate=' . $pid,
				'type' => 'ajax',
				'icon' => 'sui-icon-power-on-off',
				'data' => array(
					'action'  => 'project-activate',
					'hash'    => $hashes['project-activate'],
					'project' => $pid,
				),
			);
		}

		if ( isset( $res->url->config ) && ! empty( $res->url->config ) ) {
			$actions['configure'] = array(
				'name' => __( 'Configure', 'wpmudev' ),
				'url'  => $res->url->config,
				'type' => 'href',
				'icon' => 'sui-icon-wrench-tool',
				'data' => array(
					'action'  => 'project-configure',
					'hash'    => '',
					'project' => $pid,
				),
			);
		}

		if ( $res->is_active ) {
			$actions['deactivate'] = array(
				'name' => ( $res->is_network_admin ? __( 'Network Deactivate', 'wpmudev' ) : __( 'Deactivate', 'wpmudev' ) ),
				'url'  => '#deactivate=' . $pid,
				'type' => 'ajax',
				'icon' => 'sui-icon-power-on-off',
				'data' => array(
					'action'  => 'project-deactivate',
					'hash'    => $hashes['project-deactivate'],
					'project' => $pid,
				),
			);
		} else {
			$actions['delete'] = array(
				'name'  => __( 'Delete', 'wpmudev' ),
				'url'   => '#',
				'type'  => 'ajax',
				'icon'  => 'sui-icon-trash',
				'class' => 'dashui-red-link',
				'data'  => array(
					'action'  => 'project-delete',
					'hash'    => $hashes['project-delete'],
					'project' => $pid,
				),
			);
		}


	} elseif ( $res->special ) {
		switch ( $res->special ) {
			case 'dropin':
				$main_action = array(
					'name' => __( 'Dropin', 'wpmudev' ),
					'url'  => '#',
					'type' => 'none',
					'icon' => '',
					'data' => array(
						'action'  => 'project-dropin',
						'hash'    => '',
						'project' => $pid,
					),
				);
				break;
			case 'muplugin':
				$main_action = array(
					'name' => __( 'MU Plugin', 'wpmudev' ),
					'url'  => '#',
					'type' => 'none',
					'icon' => '',
					'data' => array(
						'action'  => 'project-muplugin',
						'hash'    => '',
						'project' => $pid,
					),
				);
				break;
			default:
				break;
		}
	} elseif ( $res->is_active ) {
		if ( isset( $res->url->config ) && ! empty( $res->url->config ) ) {
			$main_action       = array(
				'name' => __( 'Configure', 'wpmudev' ),
				'url'  => $res->url->config,
				'type' => 'href',
				'icon' => 'sui-icon-wrench-tool',
				'data' => array(
					'action'  => 'project-configure',
					'hash'    => '',
					'project' => $pid,
				),
			);

			$actions['configure'] = array(
				'name' => __( 'Configure', 'wpmudev' ),
				'url'  => $res->url->config,
				'type' => 'href',
				'icon' => 'sui-icon-wrench-tool',
				'data' => array(
					'action'  => 'project-configure',
					'hash'    => '',
					'project' => $pid,
				),
			);
		}

		$actions['deactivate'] = array(
			'name' => ( $res->is_network_admin ? __( 'Network Deactivate', 'wpmudev' ) : __( 'Deactivate', 'wpmudev' ) ),
			'url'  => '#deactivate=' . $pid,
			'type' => 'ajax',
			'icon' => 'sui-icon-power-on-off',
			'data' => array(
				'action'  => 'project-deactivate',
				'hash'    => $hashes['project-deactivate'],
				'project' => $pid,
			),
		);

	} else {
		// activate
		$main_action = array(
			'name' => ( $res->is_network_admin ? __( 'Network Activate', 'wpmudev' ) : __( 'Activate', 'wpmudev' ) ),
			'url'  => '#activate=' . $pid,
			'type' => 'ajax',
			'icon' => 'sui-icon-power-on-off',
			'data' => array(
				'action'  => 'project-activate',
				'hash'    => $hashes['project-activate'],
				'project' => $pid,
			),
		);

		$actions['activate'] = array(
			'name' => ( $res->is_network_admin ? __( 'Network Activate', 'wpmudev' ) : __( 'Activate', 'wpmudev' ) ),
			'url'  => '#activate=' . $pid,
			'type' => 'ajax',
			'icon' => 'sui-icon-power-on-off',
			'data' => array(
				'action'  => 'project-activate',
				'hash'    => $hashes['project-activate'],
				'project' => $pid,
			),
		);

		$actions['delete'] = array(
			'name'  => __( 'Delete', 'wpmudev' ),
			'url'   => '#',
			'type'  => 'href',
			'icon'  => 'sui-icon-trash',
			'class' => 'sui-button-delete',
			'data'  => array(
				'action'  => 'project-delete',
				'hash'    => $hashes['project-delete'],
				'project' => $pid,
			),
		);
	}

	$main_action_class = 'sui-button-icon';
}

// Show special error and message if Upfront not installed
if ( $res->is_installed && $res->need_upfront ) {
	if ( ! WPMUDEV_Dashboard::$site->is_upfront_installed() ) {
		// This upfront theme needs Upfront parent to work!
		echo "Upfront needed";
	}
}

// PIC GALERY
$gallery_items = array();
if ( ! empty( $res->url->video ) ) {
	$gallery_items[] = array(
		'thumb' => $res->url->thumbnail,
		'full'  => $res->url->video,
		'desc'  => '',
		'type'  => 'video',
	);
}
if ( is_array( $res->screenshots ) ) {
	foreach ( $res->screenshots as $item ) {
		$gallery_items[] = array(
			'thumb' => $item['url'],
			'full'  => $item['url'],
			'desc'  => $item['desc'],
			'type'  => 'image',
		);
	}
}

if ( empty( $gallery_items ) ) {
	$gallery_items[] = array(
		'thumb' => $res->url->thumbnail,
		'full'  => $res->url->thumbnail,
		'desc'  => '',
		'type'  => 'image',
	);
}

$slider_class = '';
if ( 1 === count( $gallery_items ) ) {
	$slider_class = 'no-nav';
}

$has_features = false;
$features     = array(
	0 => array(),
	1 => array(),
);
// chunk feature into 2
if ( is_array( $res->features ) && ! empty( $res->features ) ) {
	$has_features = true;
	$chunk_size   = ceil( count( $res->features ) / 2 );
	$features     = array_chunk( $res->features, $chunk_size );
}


$attr = array(
	'project'             => $pid,
	'licensed'            => intval( $res->is_licensed ),
	'installed'           => intval( $res->is_installed ),
	'has-update'          => intval( $res->has_update ),
	'is-compatible'       => intval( $res->is_compatible ),
	'incompatible-reason' => $incompatible_reason,
	'active'              => intval( $res->is_active ),
	'order'               => intval( $res->default_order ),
	'popularity'          => $res->popularity,
	'downloads'           => $res->downloads,
	'released'            => $res->release_stamp,
	'updated'             => $res->update_stamp,
	'type'                => $res->type,
	'name'                => esc_html( $res->name ),
	'info'                => esc_html( $res->info ),
);

foreach ( $res->tags as $tid => $plugin_tag ) {
	$attr[ 'plugin-tag-' . $tid ] = 1;
}
?>
<div class="js-plugin-box"
	<?php foreach ( $attr as $key => $item ) : ?>
		data-<?php echo esc_attr( $key ); ?>="<?php echo esc_attr( $item ); ?>"
	<?php endforeach; ?>
>

	<div class="js-mode-box">
		<?php
		/**
		 * BOXES MODE (TOP PLUGINS and NEW RELEASES)
		 */
		?>
		<div class="dashui-plugin-card" data-project="<?php echo esc_attr( $pid ); ?>">

			<div class="dashui-plugin-card-header">

				<h3 class="dashui-plugin-card-title"><?php echo esc_html( $res->name ); ?></h3>

				<div class="sui-actions-right">

					<?php if ( ! empty( $main_action ) ) : ?>
						<a href="<?php echo esc_url( $main_action['url'] ); ?>"
						   class="sui-button <?php echo esc_attr( $main_action_class ); ?>"
						   data-type="<?php echo esc_attr( $main_action['type'] ); ?>"
							<?php if ( isset( $main_action['data'] ) && is_array( $main_action['data'] ) ) : ?>
								<?php foreach ( $main_action['data'] as $key_attr => $data_attr ) : ?>
									data-<?php echo esc_attr( $key_attr ); ?>="<?php echo esc_attr( $data_attr ); ?>"
								<?php endforeach; ?>
							<?php endif; ?>
						>

							<span class="sui-loading-text">
								<i class="<?php echo esc_attr( $main_action['icon'] ); ?>"></i>
								<?php echo esc_html( $main_action['name'] ); ?>
							</span>

							<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

						</a>
					<?php endif; ?>

					<?php if ( ! empty( $incompatible_reason ) ) : ?>
						<span class="sui-tag sui-tag-sm sui-tag-red sui-tag-ghost sui-tag-sm"><?php echo esc_html( $incompatible_reason ); ?></span>
					<?php endif; ?>

					<?php if ( ! empty( $actions ) ) : ?>

						<?php if ( 1 === count( $actions ) ) : ?>

							<?php $plugin_action = reset( $actions ); ?>

							<?php if ( $plugin_action['icon'] ) { ?>
								<a href="<?php echo esc_url( $plugin_action['url'] ); ?>"
								   class="sui-button-icon sui-button-blue sui-tooltip"
								   data-tooltip="<?php echo esc_attr( $plugin_action['name'] ); ?>"
								   data-type="<?php echo esc_attr( $plugin_action['type'] ); ?>"
									<?php if ( isset( $plugin_action['data'] ) && is_array( $plugin_action['data'] ) ) : ?>
										<?php foreach ( $plugin_action['data'] as $key_attr => $data_attr ) : ?>
											data-<?php echo esc_attr( $key_attr ); ?>="<?php echo esc_attr( $data_attr ); ?>"
										<?php endforeach; ?>
									<?php endif; ?>
								>
									<span class="sui-loading-text">
										<i class="<?php echo esc_attr( $plugin_action['icon'] ); ?>"></i>
									</span>
									<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
								</a>
							<?php } ?>

						<?php else: ?>

							<?php //dropdown ?>
							<div class="sui-dropdown">
								<a href="#" class="sui-button-icon sui-dropdown-anchor js-dropdown-actions" data-project="<?php echo esc_attr( $pid ); ?>" aria-label="">
									<span class="sui-loading-text">
										<i class="<?php echo esc_attr( $actions_icon ); ?>"></i>
									</span>
									<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
								</a>
								<ul>
									<?php foreach ( $actions as $plugin_action ): ?>
										<li>
											<a href="<?php echo esc_url( $plugin_action['url'] ); ?>"
												<?php if ( isset( $plugin_action['class'] ) ) : ?>
													class="<?php echo esc_attr( $plugin_action['class'] ); ?>"
												<?php endif; ?>
                                               data-tooltip="<?php echo esc_attr( $plugin_action['name'] ); ?>"
                                               data-type="<?php echo esc_attr( $plugin_action['type'] ); ?>"
												<?php if ( isset( $plugin_action['data'] ) && is_array( $plugin_action['data'] ) ) : ?>
													<?php foreach ( $plugin_action['data'] as $key_attr => $data_attr ) : ?>
														data-<?php echo esc_attr( $key_attr ); ?>="<?php echo esc_attr( $data_attr ); ?>"
													<?php endforeach; ?>
												<?php endif; ?>
											>
												<?php if ( $plugin_action['icon'] ): ?>
													<i class="<?php echo esc_attr( $plugin_action['icon'] ); ?>"></i>
												<?php endif; ?>
												<?php echo esc_html( $plugin_action['name'] ); ?>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
			<div class="dashui-plugin-card-body">
				<div class="dashui-plugin-image">
					<img src="<?php echo esc_url( $res->url->thumbnail ); ?>"
					     alt="<?php esc_html_e( 'Plugin image', 'wpmudev' ); ?>"
					     aria-hidden="true"/>
					<button class="sui-button sui-button-white sui-button-ghost js-show-plugin-modal"
					        data-action="info"
					        data-project="<?php echo esc_attr( $pid ); ?>">
						<i class="sui-icon-info" aria-hidden="true"></i>
						<?php esc_html_e( 'View Info', 'wpmudev' ); ?>
					</button>
				</div>
				<p><?php echo esc_html( $res->info ); ?></p>
			</div>
		</div>
	</div>

	<?php
	/**
	 * ROW FOR PLUGIN LIST TABLE
	 */
	?>
	<div class="js-mode-row">

		<table class="sui-table">

			<tr
				data-project="<?php echo esc_attr( $pid ); ?>"
				class="<?php echo ! $res->is_installed ? esc_attr( 'dashui-is-notinstalled' ): ''; ?> <?php echo $res->has_update ? esc_attr( 'dashui-plugin-hasupdate' ): ''; ?> <?php echo ! $res->is_active ? esc_attr( 'dashui-plugin-notactive' ): ''; ?>"
			>

				<td class="dashui-column-title">

					<div class="dashui-plugin-title">

						<label for="bulk-action-<?php echo esc_attr( $pid ); ?>" class="sui-checkbox">
							<input type="checkbox"
							       name="pids[]"
							       value="<?php echo esc_attr( $pid ); ?>"
							       id="bulk-action-<?php echo esc_attr( $pid ); ?>"
							       class="js-plugin-check"/>
							<span aria-hidden="true"></span>
							<span class="sui-screen-reader-text"><?php esc_html_e( 'Select this plugin', 'wpmudev' ); ?></span>
						</label>

						<div class="dashui-plugin-image plugin-image"
						     style="position:relative;"
							 aria-hidden="true">
							 <?php if ( $res->has_update || ! $res->is_installed ): ?>
							 	<?php echo $res->has_update ? '<span class="dashui-update-dot"></span>' : ''; ?>
								<img
								src="<?php echo esc_url( $res->url->thumbnail_square ); ?>"
								class="sui-image plugin-image js-show-plugin-modal"
								style="width:30px;height:30px; border-radius: 5px;"
								data-action="<?php echo $res->has_update ? 'changelog' : 'info'; ?>"
					        	data-project="<?php echo esc_attr( $pid ); ?>"
								>
							<?php else: ?>
								<a href="<?php echo esc_url( $res->url->config ); ?>">
									<img
										src="<?php echo esc_url( $res->url->thumbnail_square ); ?>"
										class="sui-image plugin-image"
										style="width:30px;height:30px; border-radius: 5px;"
										data-project="<?php echo esc_attr( $pid ); ?>"
									>
								</a>
							<?php endif; ?>

						</div>
						<?php if ( $res->has_update || ! $res->is_installed ): ?>
							<button class="dashui-plugin-name js-show-plugin-modal"
									data-action="<?php echo $res->has_update ? 'changelog' : 'info'; ?>"
									data-project="<?php echo esc_attr( $pid ); ?>">
								<?php
								if( $res->is_installed ):
									printf( '%s <span class="sui-tag sui-tag-sm" style="margin-left:10px;">v%s</span>', esc_html( $res->name ), esc_html( $res->version_installed ) );
								else:
									echo esc_html( $res->name );
								endif; ?>
								<div class="dashui-desktop-hidden" style="display:inline-block; margin-left:5px;">
									<?php if( $res->has_update ){ ?>
										<a
											href="#"
											class="js-show-plugin-modal"
											data-action="<?php echo $res->has_update ? 'changelog' : 'info'; ?>"
											data-project="<?php echo esc_attr( $pid ); ?>"
											>
											<?php printf( '<span class="sui-tag sui-tag-sm sui-tag-yellow" style="cursor:pointer;">v%s %s</span>', esc_html( $res->version_latest ), esc_html__('update available' ) ); ?>
										</a>
									<?php } elseif( $res->is_active ) { ?>
											<div class="dashui-loader-wrap">
												<div class="dashui-loader-text">
													<span class="sui-tag sui-tag-sm sui-tag-blue sui-loading-text"> <?php esc_html_e( 'Active', 'wpmudev' ); ?></span>
												</div>
												<div class="dashui-loader" style="display: none;">
													<p class="sui-p-small"><i class="sui-icon-loader sui-loading" aria-hidden="true"></i><?php esc_html_e( 'Deactivating...', 'wpmudev' ); ?></p>
												</div>
											</div>
									<?php } elseif( $res->is_installed ) { ?>
											<div class="dashui-loader-wrap">
												<div class="dashui-loader-text">
													<span class="sui-tag sui-tag-sm sui-loading-text"> <?php esc_html_e( 'Inactive', 'wpmudev' ); ?> </span>
												</div>
												<div class="dashui-loader" style="display: none;">
													<div class="dashui-loader-activate">
														<p class="sui-p-small"><i class="sui-icon-loader sui-loading" aria-hidden="true"></i><?php esc_html_e( 'Activating...', 'wpmudev' ); ?></p>
													</div>
													<div class="dashui-loader-delete">
														<p class="sui-p-small"><i class="sui-icon-loader sui-loading" aria-hidden="true"></i><?php esc_html_e( 'Deleting...', 'wpmudev' ); ?></p>
													</div>
												</div>

											</div>
									<?php } ?>
								</div>
							</button>
						<?php else: ?>
							<div class="dashui-plugin-name">
								<a href="<?php echo esc_url( $res->url->config ); ?>">
								<?php echo esc_html( $res->name );  ?>
								</a>
								<a
									href="#"
									class="js-show-plugin-modal"
									data-action="changelog"
									data-project="<?php echo esc_attr( $pid ); ?>">
									<span class="sui-tag sui-tag-sm" style="margin-left:10px; cursor:pointer;">v<?php echo $res->version_installed; ?></span>
								</a>
								<div class="dashui-desktop-hidden" style="display:inline-block; margin-left:5px;">
									<?php if( $res->has_update ){ ?>
										<a
											href="#"
											class="js-show-plugin-modal"
											data-action="<?php echo $res->has_update ? 'changelog' : 'info'; ?>"
											data-project="<?php echo esc_attr( $pid ); ?>"
											>
											<?php printf( '<span class="sui-tag sui-tag-sm sui-tag-yellow" style="cursor:pointer;">v%s %s</span>', esc_html( $res->version_latest ), esc_html__('update available' ) ); ?>
										</a>
									<?php } elseif( $res->is_active ) { ?>
											<div class="dashui-loader-wrap">
												<div class="dashui-loader-text">
													<span class="sui-tag sui-tag-sm sui-tag-blue sui-loading-text"> <?php esc_html_e( 'Active', 'wpmudev' ); ?></span>
												</div>
												<div class="dashui-loader" style="display: none;">
													<p class="sui-p-small"><i class="sui-icon-loader sui-loading" aria-hidden="true"></i><?php esc_html_e( 'Deactivating...', 'wpmudev' ); ?></p>
												</div>
											</div>
									<?php } elseif( $res->is_installed ) { ?>
											<div class="dashui-loader-wrap">
												<div class="dashui-loader-text">
													<span class="sui-tag sui-tag-sm sui-loading-text"> <?php esc_html_e( 'Inactive', 'wpmudev' ); ?> </span>
												</div>
												<div class="dashui-loader" style="display: none;">
													<div class="dashui-loader-activate">
														<p class="sui-p-small"><i class="sui-icon-loader sui-loading" aria-hidden="true"></i><?php esc_html_e( 'Activating...', 'wpmudev' ); ?></p>
													</div>
													<div class="dashui-loader-delete">
														<p class="sui-p-small"><i class="sui-icon-loader sui-loading" aria-hidden="true"></i><?php esc_html_e( 'Deleting...', 'wpmudev' ); ?></p>
													</div>
												</div>

											</div>
									<?php } ?>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $incompatible_reason ) || ! empty( $actions ) ) { ?>

							<div class="dashui-plugin-actions dashui-desktop-hidden" style="display:inline-flex">
								<div class="dashui-mobile-main-action" style="width:60px">
									<?php
									// Primary action button
									if ( ! empty( $main_action ) ) : ?>

										<a
											href="<?php echo esc_url( $main_action['url'] ); ?>"
											class="sui-button <?php echo esc_attr( $main_action_class ); ?>"
											data-type="<?php echo esc_attr( $main_action['type'] ); ?>"
											<?php if ( isset( $main_action['data'] ) && is_array( $main_action['data'] ) ) : ?>
												<?php foreach ( $main_action['data'] as $key_attr => $data_attr ) : ?>
													data-<?php echo esc_attr( $key_attr ); ?>="<?php echo esc_attr( $data_attr ); ?>"
												<?php endforeach; ?>
											<?php endif; ?>
										>

											<?php if( 'sui-button-icon' !== $main_action_class ): ?>
												<span class="sui-loading-text">
													<?php if ( $main_action['icon'] ): ?>
														<i class="<?php echo esc_attr( $main_action['icon'] ); ?>"></i>
													<?php endif; ?>

													<?php echo esc_html( $main_action['name'] ); ?>
												</span>
												<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

											<?php else: ?>

												<?php if ( $main_action['icon'] ): ?>
													<i class="<?php echo esc_attr( $main_action['icon'] ); ?>"></i>
												<?php endif; ?>

											<?php endif; ?>

										</a>

									<?php endif; ?>
								</div>

								<?php
								// Secondary action button
								if ( ! empty( $actions ) ) : ?>

									<?php
									// Single action button
									if ( 1 === count( $actions ) ) { ?>

										<?php $plugin_action = reset( $actions ); ?>

										<?php if ( $plugin_action['icon'] ) : ?>

											<a
												href="<?php echo esc_url( $plugin_action['url'] ); ?>"
												class="sui-button-icon sui-button-blue sui-tooltip"
												data-tooltip="<?php echo esc_attr( $plugin_action['name'] ); ?>"
												data-type="<?php echo esc_attr( $plugin_action['type'] ); ?>"
												<?php if ( isset( $plugin_action['data'] ) && is_array( $plugin_action['data'] ) ) : ?>
													<?php foreach ( $plugin_action['data'] as $key_attr => $data_attr ) : ?>
														data-<?php echo esc_attr( $key_attr ); ?>="<?php echo esc_attr( $data_attr ); ?>"
													<?php endforeach; ?>
												<?php endif; ?>
											>

												<span class="sui-loading-text">
													<i class="<?php echo esc_attr( $plugin_action['icon'] ); ?>"></i>
												</span>

												<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
											</a>

										<?php endif; ?>

									<?php
									// Multiple actions dropdown
									} else { ?>

										<div class="sui-dropdown">

											<button
												class="sui-button-icon sui-dropdown-anchor js-dropdown-actions"
												data-project="<?php echo esc_attr( $pid ); ?>"
											>

												<span class="sui-loading-text">
													<i class="<?php echo esc_attr( $actions_icon ); ?>"></i>
												</span>

												<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

											</button>

											<ul><?php foreach( $actions as $plugin_action ) : ?>

												<li><a
													href="<?php echo esc_url( $plugin_action['url'] ); ?>"
													<?php if ( isset( $plugin_action['class'] ) ) : ?>
														class="<?php echo esc_attr( $plugin_action['class'] ); ?>"
													<?php endif; ?>
													data-tooltip="<?php echo esc_attr( $plugin_action['name'] ); ?>"
													data-type="<?php echo esc_attr( $plugin_action['type'] ); ?>"
													<?php if ( isset( $plugin_action['data'] ) && is_array( $plugin_action['data'] ) ) : ?>
														<?php foreach ( $plugin_action['data'] as $key_attr => $data_attr ) : ?>
															data-<?php echo esc_attr( $key_attr ); ?>="<?php echo esc_attr( $data_attr ); ?>"
														<?php endforeach; ?>
													<?php endif; ?>
												>
													<?php if ( $plugin_action['icon'] ) : ?>
														<i class="<?php echo esc_attr( $plugin_action['icon'] ); ?>"></i>
													<?php endif; ?>
													<?php echo esc_html( $plugin_action['name'] ); ?>
												</a></li>

											<?php endforeach; ?></ul>

										</div>

									<?php } ?>

								<?php endif; ?>

							</div>

						<?php } ?>

					</div>

				</td>

				<?php if( $res->is_installed ): ?>
					<td class="dashui-column-actions plugin-row-actions dashui-mobile-hidden">
						<?php if( $res->has_update ){ ?>
							<a
								href="#"
								class="js-show-plugin-modal"
								data-action="<?php echo $res->has_update ? 'changelog' : 'info'; ?>"
								data-project="<?php echo esc_attr( $pid ); ?>"
								>
						  		<?php printf( '<span class="sui-tag sui-tag-sm sui-tag-yellow" style="cursor:pointer;">v%s %s</span>', esc_html( $res->version_latest ), esc_html__('update available' ) ); ?>
							</a>
						   <?php } elseif( $res->is_active ) { ?>
								<div class="dashui-loader-wrap">
									<div class="dashui-loader-text">
										<span class="sui-tag sui-tag-sm sui-tag-blue sui-loading-text"> <?php esc_html_e( 'Active', 'wpmudev' ); ?></span>
									</div>
									<div class="dashui-loader" style="display: none;">
										<p class="sui-p-small"><i class="sui-icon-loader sui-loading" aria-hidden="true"></i><?php esc_html_e( 'Deactivating...', 'wpmudev' ); ?></p>
						   			</div>
						   		</div>
						<?php } else{ ?>
								<div class="dashui-loader-wrap">
									<div class="dashui-loader-text">
										<span class="sui-tag sui-tag-sm sui-loading-text"> <?php esc_html_e( 'Inactive', 'wpmudev' ); ?> </span>
									</div>
									<div class="dashui-loader" style="display: none;">
										<div class="dashui-loader-activate">
											<p class="sui-p-small"><i class="sui-icon-loader sui-loading" aria-hidden="true"></i><?php esc_html_e( 'Activating...', 'wpmudev' ); ?></p>
										</div>
										<div class="dashui-loader-delete">
											<p class="sui-p-small"><i class="sui-icon-loader sui-loading" aria-hidden="true"></i><?php esc_html_e( 'Deleting...', 'wpmudev' ); ?></p>
										</div>
									</div>

								</div>
						<?php } ?>
					</td>
				<?php endif; ?>

				<?php if( true === $allow_description ): ?>
					<td class="dashui-column-description plugin-row-info"><?php echo esc_html( $res->info ); ?></td>
				<?php endif; ?>

				<td class="dashui-column-actions plugin-row-actions">

					<div class="dashui-plugin-actions dashui-mobile-hidden">

						<?php
						// Show total number of installs.
						if ( $show_num_install ) { ?>
							<strong><?php echo esc_html( sprintf( _n( '%s install', '%s installs', $num_install, 'wpmudev' ), $rounded_num_install ) ); ?></strong>
						<?php } ?>

						<?php
						// Plugin actions ?>
						<div class="sui-actions-right">

							<?php
							// Primary action button
							if ( ! empty( $main_action ) ) : ?>

								<a
									href="<?php echo esc_url( $main_action['url'] ); ?>"
									class="sui-button <?php echo esc_attr( $main_action_class ); ?>"
									data-type="<?php echo esc_attr( $main_action['type'] ); ?>"
									<?php if ( isset( $main_action['data'] ) && is_array( $main_action['data'] ) ) : ?>
										<?php foreach ( $main_action['data'] as $key_attr => $data_attr ) : ?>
											data-<?php echo esc_attr( $key_attr ); ?>="<?php echo esc_attr( $data_attr ); ?>"
										<?php endforeach; ?>
									<?php endif; ?>
								>

									<?php if( 'sui-button-icon' !== $main_action_class ): ?>
										<span class="sui-loading-text">
											<?php if ( $main_action['icon'] ): ?>
												<i class="<?php echo esc_attr( $main_action['icon'] ); ?>"></i>
											<?php endif; ?>

											<?php echo esc_html( $main_action['name'] ); ?>
										</span>
										<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

									<?php else: ?>

										<?php if ( $main_action['icon'] ): ?>
											<i class="<?php echo esc_attr( $main_action['icon'] ); ?>"></i>
										<?php endif; ?>

									<?php endif; ?>

								</a>

							<?php endif; ?>

							<?php
							// Incompatible notice
							if ( ! empty( $incompatible_reason ) ) : ?>
								<span class="sui-tag sui-tag-sm sui-tag-red sui-tag-ghost"><?php echo esc_html( $incompatible_reason ); ?></span>
							<?php endif; ?>

							<?php
							// Secondary action button
							if ( ! empty( $actions ) ) : ?>

								<?php
								// Single action button
								if ( 1 === count( $actions ) ) { ?>

									<?php $plugin_action = reset( $actions ); ?>

									<?php if ( $plugin_action['icon'] ) : ?>

										<a
											href="<?php echo esc_url( $plugin_action['url'] ); ?>"
											class="<?php echo $res->is_active ? 'sui-button-icon' : 'sui-button sui-button-blue'; ?>"
											data-type="<?php echo esc_attr( $plugin_action['type'] ); ?>"
											<?php if ( isset( $plugin_action['data'] ) && is_array( $plugin_action['data'] ) ) : ?>
												<?php foreach ( $plugin_action['data'] as $key_attr => $data_attr ) : ?>
													data-<?php echo esc_attr( $key_attr ); ?>="<?php echo esc_attr( $data_attr ); ?>"
												<?php endforeach; ?>
											<?php endif; ?>
										>

											<span class="sui-loading-text">
												<i class="<?php echo esc_attr( $plugin_action['icon'] ); ?>"></i>
												<?php if( ! $res->is_active ){
													echo esc_html( $plugin_action['name'] );
												} ?>
											</span>

											<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

										</a>

									<?php endif; ?>

								<?php
								// Multiple actions dropdown
								} else { ?>

									<div class="sui-dropdown">

										<button
											class="sui-button-icon sui-dropdown-anchor js-dropdown-actions"
											data-project="<?php echo esc_attr( $pid ); ?>"
										>

											<span class="sui-loading-text">
												<i class="<?php echo esc_attr( $actions_icon ); ?>"></i>
											</span>

											<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

										</button>

										<ul><?php foreach( $actions as $plugin_action ) : ?>

											<li><a
												href="<?php echo esc_url( $plugin_action['url'] ); ?>"
												<?php if ( isset( $plugin_action['class'] ) ) : ?>
													class="<?php echo esc_attr( $plugin_action['class'] ); ?>"
												<?php endif; ?>
												data-tooltip="<?php echo esc_attr( $plugin_action['name'] ); ?>"
												data-type="<?php echo esc_attr( $plugin_action['type'] ); ?>"
												<?php if ( isset( $plugin_action['data'] ) && is_array( $plugin_action['data'] ) ) : ?>
													<?php foreach ( $plugin_action['data'] as $key_attr => $data_attr ) : ?>
														data-<?php echo esc_attr( $key_attr ); ?>="<?php echo esc_attr( $data_attr ); ?>"
													<?php endforeach; ?>
												<?php endif; ?>
											>
												<?php if ( $plugin_action['icon'] ) : ?>
													<i class="<?php echo esc_attr( $plugin_action['icon'] ); ?>"></i>
												<?php endif; ?>
												<?php echo esc_html( $plugin_action['name'] ); ?>
											</a></li>

										<?php endforeach; ?></ul>

									</div>

								<?php } ?>

							<?php endif; ?>

						</div>

					</div>

				</td>

			</tr>

		</table>

	</div>

	<div class="js-mode-modal">
		<?php
		/**
		 * MODAL MODE
		 */
		?>

		<div class="sui-dialog js-plugin-modal"
		     aria-hidden="true"
		     tabindex="-1"
		     id="plugin-modal-<?php echo esc_attr( $pid ); ?>"
		     data-project="<?php echo esc_attr( $pid ); ?>"
		     data-hash="<?php echo esc_attr( wp_create_nonce( 'show-popup' ) ); ?>">

			<div class="sui-dialog-overlay" data-a11y-dialog-hide></div>
			<div class="sui-dialog-content" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="dialog">
				<div class="sui-box" role="document">

					<div class="sui-box-header">
						<h3 class="sui-box-title" id="dialogTitle"><?php echo esc_html( $res->name ); ?></h3>
						<div class="sui-actions-right">

							<?php if ( ! empty( $incompatible_reason ) ) : ?>
								<span class="sui-tag sui-tag-sm sui-tag-red sui-tag-ghost"><?php echo esc_html( $incompatible_reason ); ?></span>
							<?php endif; ?>


							<?php if ( ! empty( $modal_install_button ) ) : ?>
								<a class="sui-button <?php echo esc_attr( $modal_install_button['class'] ); ?>"
								   href="<?php echo esc_url( $modal_install_button['url'] ); ?>"
								   data-type="<?php echo esc_attr( $modal_install_button['type'] ); ?>"
									<?php if ( isset( $modal_install_button['data'] ) && is_array( $modal_install_button['data'] ) ) : ?>
										<?php foreach ( $modal_install_button['data'] as $key_attr => $data_attr ) : ?>
											data-<?php echo esc_attr( $key_attr ); ?>="<?php echo esc_attr( $data_attr ); ?>"
										<?php endforeach; ?>
									<?php endif; ?>
								>
							<span class="sui-loading-text">
							<?php if ( $modal_install_button['icon'] ): ?>
								<i class="<?php echo esc_attr( $modal_install_button['icon'] ); ?>"></i>
							<?php endif; ?>
								<?php echo esc_html( $modal_install_button['name'] ); ?>
							</span>
									<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
								</a>
							<?php endif; ?>


							<?php if ( ! empty( $main_action ) ) : ?>
								<a class="sui-button <?php echo esc_attr( $main_action_class_modal ); ?>"
								   href="<?php echo esc_url( $main_action['url'] ); ?>"
								   data-type="<?php echo esc_attr( $main_action['type'] ); ?>"
									<?php if ( isset( $main_action['data'] ) && is_array( $main_action['data'] ) ) : ?>
										<?php foreach ( $main_action['data'] as $key_attr => $data_attr ) : ?>
											data-<?php echo esc_attr( $key_attr ); ?>="<?php echo esc_attr( $data_attr ); ?>"
										<?php endforeach; ?>
									<?php endif; ?>
								>
							<span class="sui-loading-text">
								<?php echo esc_html( $main_action['name'] ); ?>
							</span>
									<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
								</a>
							<?php endif; ?>

							<a data-a11y-dialog-hide class="sui-dialog-close" aria-label="<?php esc_html_e( 'Close this dialog window', 'wpmudev' ); ?>"></a>
						</div>
					</div>

					<?php // load async later ?>
					<div class="sui-box-body js-dialog-body js-is-loading">
						<div class="sui-block-content-center js-dialog-loader">
							<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
						</div>
					</div>

					<div class="sui-box-footer">
						<a class="sui-button sui-button-ghost" data-a11y-dialog-hide="plugin-modal-<?php echo esc_attr( $pid ); ?>"><?php esc_html_e( 'Close', 'wpmudev' ); ?></a>
						<div class="sui-actions-right">

							<?php if ( ! empty( $modal_install_button ) ) : ?>
								<a class="sui-button <?php echo esc_attr( $modal_install_button['class'] ); ?>"
								   href="<?php echo esc_url( $modal_install_button['url'] ); ?>"
								   data-type="<?php echo esc_attr( $modal_install_button['type'] ); ?>"
									<?php if ( isset( $modal_install_button['data'] ) && is_array( $modal_install_button['data'] ) ) : ?>
										<?php foreach ( $modal_install_button['data'] as $key_attr => $data_attr ) : ?>
											data-<?php echo esc_attr( $key_attr ); ?>="<?php echo esc_attr( $data_attr ); ?>"
										<?php endforeach; ?>
									<?php endif; ?>
								>
							<span class="sui-loading-text">
							<?php if ( $modal_install_button['icon'] ): ?>
								<i class="<?php echo esc_attr( $modal_install_button['icon'] ); ?>"></i>
							<?php endif; ?>
								<?php echo esc_html( $modal_install_button['name'] ); ?>
							</span>
									<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
								</a>
							<?php endif; ?>


							<?php if ( ! empty( $main_action ) ) : ?>
								<a class="sui-button <?php echo esc_attr( $main_action_class_modal ); ?>"
								   href="<?php echo esc_url( $main_action['url'] ); ?>"
								   data-type="<?php echo esc_attr( $main_action['type'] ); ?>"
									<?php if ( isset( $main_action['data'] ) && is_array( $main_action['data'] ) ) : ?>
										<?php foreach ( $main_action['data'] as $key_attr => $data_attr ) : ?>
											data-<?php echo esc_attr( $key_attr ); ?>="<?php echo esc_attr( $data_attr ); ?>"
										<?php endforeach; ?>
									<?php endif; ?>
								>
							<span class="sui-loading-text">

								<?php echo esc_html( $main_action['name'] ); ?>
							</span>
									<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
								</a>
							<?php endif; ?>
						</div>
					</div>
				</div>

			</div>

		</div>

	</div>

	<div class="js-mode-modal-after-install">
		<?php
		/**
		 * MODAL After install MODE
		 */
		?>

		<div class="sui-dialog sui-dialog-sm js-plugin-modal-after-install"
		     aria-hidden="true"
		     tabindex="-1"
		     id="plugin-modal-after-install-<?php echo esc_attr( $pid ); ?>"
		     data-project="<?php echo esc_attr( $pid ); ?>">

			<div class="sui-dialog-overlay" data-a11y-dialog-hide></div>

			<div class="sui-dialog-content" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="dialog">

				<div class="sui-box" role="document">


					<div class="sui-box-header">
						<h3 class="sui-box-title" id="dialogTitle"><?php echo esc_html( sprintf( __( '%s installed!', 'wpmudev' ), $res->name ) ); ?></h3>
						<div class="sui-actions-right">
							<a data-a11y-dialog-hide class="sui-dialog-close" aria-label="<?php esc_html_e( 'Close this dialog window', 'wpmudev' ); ?>"></a>
						</div>
					</div>

					<div class="sui-box-body">
						<p id="dialogDescription">
							<?php esc_html_e( 'Would you like to activate it now?', 'wpmudev' ); ?>
						</p>
					</div>

					<div class="sui-box-footer">
						<a class="sui-button sui-button-ghost" data-a11y-dialog-hide="admin-add"><?php esc_html_e( 'CONTINUE', 'wpmudev' ); ?></a>
						<a class="sui-button sui-button-blue"
						   data-action="project-activate"
						   data-hash="<?php echo esc_attr( $hashes['project-activate'] ); ?>"
						   data-project="<?php echo esc_attr( $pid ); ?>"
						>
							<span class="sui-loading-text">
								<?php esc_html_e( 'ACTIVATE', 'wpmudev' ); ?>
							</span>
							<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
						</a>
					</div>

					<div class="sui-block-content-center">
						<img
							src="<?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/devman-loading.png' ); ?>"
							srcset="<?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/devman-loading.png' ); ?> 1x, <?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/devman-loading@2x.png' ); ?> 2x"
							alt="Upgrade"
							aria-hidden="true"
							style = "vertical-align: middle;"
						/>
					</div>

				</div>

			</div>

		</div>

	</div>
</div>


