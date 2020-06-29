<div class="sui-header">

	<h1 class="sui-header-title"><?php echo esc_html( $page_title ); ?></h1>

	<div class="sui-actions-right">

		<div class="dashui-login-bar">

			<?php if ( WPMUDEV_CUSTOM_API_SERVER ) { ?>

				<div class="sui-tooltip sui-tooltip-bottom sui-tooltip-bottom-right-mobile"
					data-tooltip="<?php echo esc_attr( sprintf( "Custom API Server:\n%s", WPMUDEV_CUSTOM_API_SERVER ) ); ?>">
					<i class="sui-icon-plug-connected"></i>
				</div>

			<?php } ?>

			<?php if ( $is_logged_in ) : ?>

				<a href="<?php echo esc_url( $url_dash ); ?>"
					class="sui-button-icon sui-tooltip sui-tooltip-bottom sui-tooltip-bottom-left-mobile"
					data-tooltip="<?php esc_html_e( 'Hub', 'wpmudev' ); ?>">
					<i class="sui-icon-hub sui-md"></i>
				</a>

				<a href="<?php echo esc_url( $documentation_url ); ?>"
					target="_blank"
					class="sui-button-icon sui-tooltip sui-tooltip-bottom sui-tooltip-bottom-left-mobile"
					data-tooltip="<?php esc_html_e( 'Documentation', 'wpmudev' ); ?>">
					<i class="sui-icon-academy sui-md"></i>
				</a>

				<a href="<?php echo esc_url( $url_support ); ?>"
					class="sui-button-icon sui-tooltip sui-tooltip-bottom sui-tooltip-bottom-left-mobile"
					data-tooltip="<?php esc_html_e( 'Support', 'wpmudev' ); ?>">
					<i class="sui-icon-help-support sui-md"></i>
				</a>

				<div class="sui-dropdown">

					<?php if ( ! empty( $profile['avatar'] ) ) { ?>

						<button class="dashui-logout-button sui-dropdown-anchor">

							<img src="<?php echo esc_url( $profile['avatar'] ); ?>"
								aria-hidden="true" />

							<i class="sui-icon-chevron-down"></i>

							<span class="sui-screen-reader-text"><?php esc_html_e( 'Open settings', 'wpmudev' ); ?></span>

						</button>

					<?php } else { ?>

						<button class="sui-button-icon sui-dropdown-anchor">

							<i class="sui-icon-widget-settings-config" aria-hidden="true"></i>

							<span class="sui-screen-reader-text"><?php esc_html_e( 'Open settings', 'wpmudev' ); ?></span>

						</button>

					<?php } ?>

					<ul>
						<li><a href="<?php echo esc_url( $url_logout ); ?>">
							<i class="sui-icon-plug-disconnected" aria-hidden="true"></i> <?php esc_html_e( 'Logout', 'wpmudev' ); ?>
						</a></li>
					</ul>

				</div>

			<?php endif; ?>

		</div>

	</div>

</div>
