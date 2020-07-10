	<div class="wc-bookings-store-availability-views">
		<input type="checkbox" id="wc-bookings-store-availability-icon" />
		<label for="wc-bookings-store-availability-icon" class="wc-bookings-store-availability-icon-label"></label>
		<div class="wc-bookings-store-availability-views-menu">
			<ul class="wc-bookings-store-availability-views-content">
				<li><h4><?php esc_html_e( 'View', 'woocommerce-bookings' ); ?></h4></li>
				<li>
					<a class="view-select <?php echo ( 'calendar' === $view ) ? 'current' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'view', 'calendar' ) ); ?>">
						<span><?php esc_html_e( 'Calendar Mode', 'woocommerce-bookings' ); ?></span>
						<span>Visualize and configure store availability rules</span>
					</a>
				</li>
				<li>
					<a class="view-select <?php echo ( 'classic' === $view ) ? 'current' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'view', 'classic' ) ); ?>">
						<span><?php esc_html_e( 'Classic Mode', 'woocommerce-bookings' ); ?></span>
						<span>Setup availability rules with the classic table format</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
