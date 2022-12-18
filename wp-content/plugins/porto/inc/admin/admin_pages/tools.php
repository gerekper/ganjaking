<div class="wrap">
	<h1 class="screen-reader-text"><?php esc_html_e( 'Tools', 'porto' ); ?></h1>
</div>
<div class="wrap porto-wrap porto-setup-wizard">
	<?php
		porto_get_template_part(
			'inc/admin/admin_pages/header',
			null,
			array(
				'active_item' => 'tools',
				'title'       => __( 'Tools', 'porto' ),
				'subtitle'    => __( 'Please clear caches and transients here.', 'porto' ),
			)
		);

		$nonce = wp_create_nonce( 'porto-tools' );
		?>
	<main style="display: block">
		<?php
		if ( ! empty( $result_message ) ) {
			if ( $result_success ) {
				echo '<div class="updated inline"><p>' . esc_html( $result_message ) . '</p></div>';
			} else {
				echo '<div class="error inline"><p>' . esc_html( $result_message ) . '</p></div>';
			}
		}
		?>
		<table class="porto-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Name', 'porto' ); ?></th>
					<th><?php esc_html_e( 'Action', 'porto' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<h4><?php esc_html_e( 'Plugin transients', 'porto' ); ?></h4>
						<p><?php esc_html_e( 'This tool will clear the plugin(Porto Functionality, Revolution Slider and WPBakery Page Builder) update transients cache.', 'porto' ); ?></p>
					</td>
					<td>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=porto-tools&action=clear_plugin_transient&_wpnonce=' . $nonce ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'Clear transients', 'porto' ); ?></a>
					</td>
				</tr>
				<tr>
					<td>
						<h4><?php esc_html_e( 'Studio Block transients', 'porto' ); ?></h4>
						<p><?php esc_html_e( 'This tool will clear the Porto Studio block transients cache.', 'porto' ); ?></p>
					</td>
					<td>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=porto-tools&action=clear_studio_transient&_wpnonce=' . $nonce ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'Clear transients', 'porto' ); ?></a>
					</td>
				</tr>
				<tr>
					<td>
						<h4><?php esc_html_e( 'Compile all css & Clear merged css and js', 'porto' ); ?></h4>
						<p><?php esc_html_e( 'This tool will compile shortcodes css, bootstrap css and dynamic styles and clear merged stylesheet and javascript.', 'porto' ); ?></p>
					</td>
					<td>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=porto-tools&action=compile_css&_wpnonce=' . $nonce ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'Compile CSS', 'porto' ); ?></a>
					</td>
				</tr>
				<tr>
					<td>
						<h4><?php esc_html_e( 'Refresh templates information', 'porto' ); ?></h4>
						<p><?php esc_html_e( 'This tool will reset the information about Porto templates used in pages, posts, sidebars, menus, etc.', 'porto' ); ?></p>
					</td>
					<td>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=porto-tools&action=refresh_blocks&_wpnonce=' . $nonce ) ); ?>" class="btn btn-dark"><?php esc_html_e( 'Refresh Templates', 'porto' ); ?></a>
					</td>
				</tr>
				<tr>
					<td>
						<h4><?php esc_html_e( 'Refresh Templates\' display conditions', 'porto' ); ?></h4>
						<p><?php esc_html_e( 'This tool will reset the display conditions for all templates.', 'porto' ); ?></p>
					</td>
					<td>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=porto-tools&action=refresh_conditions&_wpnonce=' . $nonce ) ); ?>" class="btn btn-dark"><?php esc_html_e( 'Refresh Conditions', 'porto' ); ?></a>
					</td>
				</tr>
			</tbody>
		</table>
	</main>
</div>
