<?php
global $wp_version;
?>
<div class="sui-header">

	<h1 class="sui-header-title"><?php esc_html_e( 'Support', 'wpmudev' ); ?></h1>

	<div class="sui-actions-right">

		<a href="<?php echo esc_url( $urls->dashboard_url ); ?>"
		   class="sui-button">
			<i class="sui-icon-arrow-left" aria-hidden="true"></i>
			<?php esc_html_e( 'Back To Login', 'wpmudev' ); ?>
		</a>

	</div>

</div>

<div class="sui-row-with-sidenav">

	<div class="sui-sidenav">

		<ul class="sui-vertical-tabs sui-sidenav-hide-md">

			<li class="sui-vertical-tab current">
				<a href="" style="pointer-events: none;"><?php esc_html_e( 'System Information', 'wpmudev' ); ?></a>
			</li>

		</ul>

	</div>

	<div class="sui-box">

		<div class="sui-box-header">

			<h2 class="sui-box-title"><?php esc_html_e( 'System Information', 'wpmudev' ); ?></h2>

		</div>

		<div class="sui-box-body">

			<p><?php esc_html_e( 'Use this detailed overview of your system stack to debug issues with your WordPress installation.', 'wpmudev' ); ?></p>

			<ul class="dashui-list-sysinfo">

				<li>
					<strong><?php esc_html_e( 'WordPress', 'wpmudev' ); ?></strong>
					<span class="sui-tag"><?php echo esc_html( $wp_version ); ?></span>
				</li>

				<li>
					<strong><?php esc_html_e( 'WPMU DEV Dashboard', 'wpmudev' ); ?></strong>
					<span class="sui-tag"><?php echo esc_html( WPMUDEV_Dashboard::$version ); ?></span>
				</li>

			</ul>

		</div>

		<div class="sui-box-body">

			<?php $this->load_sui_template( 'part-system-info', array(), true ); ?>

		</div>

	</div>

</div>
