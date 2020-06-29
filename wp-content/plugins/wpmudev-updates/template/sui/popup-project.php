<?php

// Skip if project-ID is invalid.
$pid = intval( $pid );
if ( ! $pid ) {
	return;
}

$res = WPMUDEV_Dashboard::$site->get_project_infos( $pid, true );

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

$main_action         = array();
$actions             = array();
$is_single_action    = false;
$actions_icon        = 'sui-icon-plus';
$main_action_class   = 'sui-button-blue';
$show_num_install    = false;
$num_install         = 0;
$rounded_num_install = 0;

if ( ! $res->is_installed ) {
	$is_single_action    = true;
	$show_num_install    = true;
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
		$actions = array(
			'upgrade-membership' => array(
				'name' => __( 'Upgrade Membership', 'wpmudev' ),
				'url'  => '#upgrade-membership',
				'type' => 'modal',
				'icon' => 'sui-wpmudev-logo',
				'data' => array(
					'action'  => 'project-upgrade',
					'hash'    => $hashes['project-upgrade'],
					'project' => $pid,
				),
			),
		);
	} elseif ( $res->is_compatible && $res->url->install ) {
		$actions = array(
			'install' => array(
				'name' => __( 'Install', 'wpmudev' ),
				'url'  => $res->url->install,
				'type' => 'modal-ajax',
				'icon' => 'sui-icon-plus',
				'data' => array(
					'action'  => 'project-install',
					'hash'    => $hashes['project-install'],
					'project' => $pid,
				),
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
		$actions = array(
			'disabled' => array(
				'name' => $res->incompatible_reason,
				'url'  => '#',
				'type' => 'none',
				'icon' => 'sui-icon-close',
				'data' => array(
					'action'  => 'project-disabled',
					'hash'    => '',
					'project' => $pid,
				),
			),
		);
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
			'icon' => '',
			'data' => array(
				'action'  => 'project-update',
				'hash'    => $hashes['project-update'],
				'project' => $pid,
			),
		);

		$actions['update'] = array(
			'name' => __( 'Update', 'wpmudev' ),
			'url'  => '',
			'type' => 'modal-ajax',
			'icon' => 'sui-icon-update',
			'data' => array(
				'action'  => 'project-update',
				'hash'    => $hashes['project-update'],
				'project' => $pid,
			),
		);

		//activate/deactivate, configure, delete
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

		$actions['delete'] = array(
			'name' => __( 'Delete', 'wpmudev' ),
			'url'  => '#',
			'type' => 'ajax',
			'icon' => 'sui-icon-trash',
			'data' => array(
				'action'  => 'project-delete',
				'hash'    => $hashes['project-delete'],
				'project' => $pid,
			),
		);

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
			$main_action_class = 'sui-button-ghost';

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

		$actions['delete'] = array(
			'name' => __( 'Delete', 'wpmudev' ),
			'url'  => '#',
			'type' => 'href',
			'icon' => 'sui-icon-trash',
			'data' => array(
				'action'  => 'project-delete',
				'hash'    => $hashes['project-delete'],
				'project' => $pid,
			),
		);

	} else {
		// activate
		$main_action = array(
			'name' => ( $res->is_network_admin ? __( 'Network Activate', 'wpmudev' ) : __( 'Activate', 'wpmudev' ) ),
			'url'  => '#activate=' . $pid,
			'type' => 'ajax',
			'icon' => '',
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
			'name' => __( 'Delete', 'wpmudev' ),
			'url'  => '#',
			'type' => 'href',
			'icon' => 'sui-icon-trash',
			'data' => array(
				'action'  => 'project-delete',
				'hash'    => $hashes['project-delete'],
				'project' => $pid,
			),
		);
	}
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
		//		'full'  => $res->url->video,
		'full'  => $res->url->thumbnail,
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
	$slider_class = ' no-nav';
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
	'project'       => $pid,
	'licensed'      => intval( $res->is_licensed ),
	'installed'     => intval( $res->is_installed ),
	'has-update'    => intval( $res->has_update ),
	'is-compatible' => intval( $res->is_compatible ),
	'active'        => intval( $res->is_active ),
	'order'         => intval( $res->default_order ),
	'popularity'    => $res->popularity,
	'downloads'     => $res->downloads,
	'released'      => $res->release_stamp,
	'updated'       => $res->update_stamp,
	'type'          => $res->type,
	'name'          => esc_html( $res->name ),
	'info'          => esc_html( $res->info ),
);

foreach ( $res->tags as $tid => $plugin_tag ) {
	$attr[ 'plugin-tag-' . $tid ] = 1;
}
?>

<p id="dialogDescription"><?php echo esc_html( $res->info ); ?></p>

<div class="sui-tabs sui-tabs-flushed"
	style="margin-top: 0 !important; border-top: 1px solid #E6E6E6;">

	<div data-tabs="">
		<div class="active" data-index="overview"><?php esc_html_e( 'Overview', 'wpmudev' ); ?></div>
		<div class="" data-index="features"><?php esc_html_e( 'Features', 'wpmudev' ); ?></div>
		<div class="" data-index="changelog"><?php esc_html_e( 'Changelog', 'wpmudev' ); ?></div>
	</div>

	<div data-panes="">

		<?php
		// TAB: Overview ?>
		<div class="sui-tab-content active" data-index="overview">

			<div class="dashui-slider<?php echo esc_attr( $slider_class ); ?>"
				aria-hidden="true">

				<ul class="dashui-slider-main">

					<?php foreach ( $gallery_items as $key => $item ) : ?>

						<li class="item-<?php echo esc_attr( $key ); ?> <?php echo esc_attr( $item['type'] ); ?>"
							data-full="<?php echo esc_url( $item['full'] ); ?>">

							<?php if ( ! empty( $item['desc'] ) ) { ?>
								<img src="<?php echo esc_url( $item['full'] ); ?>"
									alt="<?php echo esc_html( $item['desc'] ); ?>" />
							<?php } else { ?>
								<img src="<?php echo esc_url( $item['full'] ); ?>" />
							<?php } ?>

						</li>

					<?php endforeach; ?>

				</ul>

				<div class="dashui-slider-nav slider-nav-wrapper">

					<button class="dashui-slider-nav-left">
						<i class="sui-icon-chevron-left" aria-hidden="true"></i>
					</button>

					<div class="dashui-slider-nav-items">

						<ul class="slider-nav">

							<?php foreach ( $gallery_items as $key => $item ) : ?>

								<li class="<?php echo esc_attr( $item['type'] ); ?> <?php echo esc_attr( ( ! $key ? 'current' : '' ) ); ?>"
									data-key="item-<?php echo esc_attr( $key ); ?>"
									data-full="<?php echo esc_url( $item['full'] ); ?>">
									<span style="background-image: url(<?php echo esc_url( $item['thumb'] ); ?>);"></span>
								</li>

							<?php endforeach; ?>

						</ul>

					</div>

					<button class="dashui-slider-nav-right">
						<i class="sui-icon-chevron-right" aria-hidden="true"></i>
					</button>

				</div>

			</div>

			<p class="dashui-slider-notice">
				<a href="<?php echo esc_url( $res->url->website ); ?>"
					target="_blank">
					<?php esc_html_e( 'View more information on WPMU DEV', 'wpmudev' ); ?>
					<i aria-hidden="true" class="sui-icon-arrow-right"></i>
				</a>
			</p>

		</div>

		<?php
		// TAB: Features ?>
		<div data-index="features">

			<?php if ( $has_features ) : ?>

				<div class="sui-row">

					<?php foreach ( $features as $group => $feature ) : ?>

						<div class="sui-col-md-6">

							<ul class="dashui-features-list"><?php foreach ( $feature as $item ) : ?>

								<li>
									<i class="sui-icon-check" aria-hidden="true"></i>
									<?php echo esc_html( $item ); ?>
								</li>

							<?php endforeach; ?></ul>

						</div>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

			<p class="dashui-slider-notice">
				<a href="<?php echo esc_url( $res->url->website ); ?>"
					target="_blank">
					<?php esc_html_e( 'View more information on WPMU DEV', 'wpmudev' ); ?>
					<i aria-hidden="true" class="sui-icon-arrow-right"></i>
				</a>
			</p>

		</div>

		<?php
		// TAB: Changelog ?>
		<div data-index="changelog">

			<?php
			$i             = 1;
			$max_changelog = 3;

			foreach ( $res->changelog as $log ) : ?>

				<?php if ( $i > $max_changelog ) {
					break;
				} ?>

				<?php if ( is_array( $log ) && ! empty( $log ) ) : ?>

					<?php
					$version = $log['version'];
					$badges  = array(
						array(
							'class' => 'sui-tag sui-tag-purple',
							'text'  => $version,
						),
					);

					if ( $res->is_installed ) {
						// -1 .. local is higher (dev) | 0 .. equal | 1 .. new version available
						$version_check = version_compare( $version, $res->version_installed );

						if ( 0 === $version_check ) {

							$badges [] = array(
								'class' => 'sui-tag sui-tag-default',
								'text'  => __( 'Current', 'wpmudev' ),
							);

						} elseif ( 1 === $version_check ) {

							$badges [] = array(
								'class' => 'sui-tag sui-tag-green',
								'text'  => __( 'New', 'wpmudev' ),
							);

						}
					}

					$rel_date = '';

					if ( ! empty( $log['time'] ) ) {
						$rel_date = date_i18n( get_option( 'date_format' ), $log['time'] );
					} ?>

					<div class="sui-box-settings-row sui-flushed">

						<div class="sui-box-settings-col-2">

							<div class="dashui-changelog-version">

								<?php foreach ( $badges as $badge ) {

									if ( 'sui-tag sui-tag-purple' === $badge['class'] ) { ?>
										<span class="<?php echo esc_attr( $badge['class'] ); ?>"><?php esc_html_e( 'Version', 'wpmudev' ); ?> <?php echo esc_html( $badge['text'] ); ?></span>
									<?php } else { ?>
										<span class="<?php echo esc_attr( $badge['class'] ); ?>"><?php echo esc_html( $badge['text'] ); ?></span>
									<?php }

								} ?>

								<div class="sui-actions-right">
									<span class="sui-changelog-date"><?php echo esc_html( $rel_date ); ?></span>
								</div>
							</div>

							<?php
							// Changelogs
							$notes = explode( "\n", $log['log'] ); ?>

							<ul class="dashui-changelog-list"><?php foreach ( $notes as $note ) : ?>
								<?php
								$note = stripslashes( $note );
								$note = preg_replace( '/(<br ?\/?>|<p>|<\/p>)/', '', $note );
								$note = trim( preg_replace( '/^\s*(\*|\-)\s*/', '', $note ) );
								$note = str_replace( array( '<', '>' ), array( '&lt;', '&gt;' ), $note );
								$note = preg_replace( '/`(.*?)`/', '<code>\1</code>', $note );

								if ( empty( $note ) ) {
									continue;
								} ?>
								<li><?php echo wp_kses_post( $note ); ?></li>
							<?php endforeach; ?></ul>

						</div>

					</div>

					<?php $i ++; ?>

				<?php endif; ?>

			<?php endforeach; ?>

			<p class="dashui-slider-notice">
				<a href="<?php echo esc_url( $res->url->website ); ?>"
					target="_blank">
					<?php esc_html_e( 'View more information on WPMU DEV', 'wpmudev' ); ?>
					<i aria-hidden="true" class="sui-icon-arrow-right"></i>
				</a>
			</p>

		</div>

	</div>

</div>


