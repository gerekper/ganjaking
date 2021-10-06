<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_version;
?>
<div class="tablenav">
	<div class="nav-content">
		<div class="filters">
			<span class="bookings-filter-label"><?php esc_html_e( 'Filter By', 'woocommerce-bookings' ); ?></span>
			<span class="calendar-bookings-filter-container">
				<select class="wc-enhanced-select" name="filter_bookings_product" style="width: 200px;">
					<option value=""><?php esc_html_e( 'All Products', 'woocommerce-bookings' ); ?></option>
					<?php
						// Get product list with resources excluded.
						$product_filters = $this->product_filters( false );
						if ( $product_filters ) {
							foreach ( $product_filters as $filter_id => $filter_name ) {
								?>
									<option value="<?php echo esc_attr( $filter_id ); ?>" <?php selected( $product_filter, $filter_id ); ?>>
										<?php echo esc_html( $filter_name ); ?>
									</option>
								<?php
							}
						}
					?>
				</select>
			</span>
			<span class="calendar-bookings-filter-container">
				<select class="wc-enhanced-select" name="filter_bookings_resource" style="width: 200px;">
					<option value=""><?php esc_html_e( 'All Resources', 'woocommerce-bookings' ); ?></option>
					<?php
					$resource_filters = $this->resources_filters();
					if ( $resource_filters ) {
						foreach ( $resource_filters as $filter_id => $filter_name ) {
							?>
								<option value="<?php echo esc_attr( $filter_id ); ?>" <?php selected( $resource_filter, $filter_id ); ?>>
									<?php echo esc_html( $filter_name ); ?>
								</option>
							<?php
						}
					}
					?>
				</select>
		</div>
	</div>
	<?php if ( in_array( $view, array( 'month', 'schedule' ), true ) ) : ?>
		<?php if ( ! WC_BOOKINGS_GUTENBERG_EXISTS ) { ?>
			<div class="date_selector">
				<div>
					<a class="prev" href="<?php
						echo esc_url( add_query_arg( array(
							'calendar_year'  => $month == 1 ? $year - 1 : $year,
							'calendar_month' => $month == 1 ? 12 : $month - 1,
						) ) );
					?>">&larr;</a>
				</div>
				<div>
					<input type="text" name="calendar_day" class="calendar_day" placeholder="yyyy-mm-dd" value="<?php echo esc_attr( date_i18n( 'F', mktime( 0, 0, 0, $month, 10 ) ) . ' ' . $year ); ?>" />
				</div>
				<div>
					<a class="next" href="<?php
						echo esc_url( add_query_arg( array(
							'calendar_year'  => $month == 12 ? $year + 1 : $year,
							'calendar_month' => $month == 12 ? 1 : $month + 1,
						) ) );
					?>">&rarr;</a>
				</div>
			</div>
		<?php } else { ?>
			<div class="date-selector-popover">
				<div class="current_month">
					<div id="wc-bookings-datepicker-container-month">
					</div>
				</div>
				<div>
					<a class="change-date prev" href="<?php
						echo esc_url( add_query_arg( array(
							'calendar_year'  => $month == 1 ? $year - 1 : $year,
							'calendar_month' => $month == 1 ? 12 : $month - 1,
						) ) );
					?>">&larr;</a>
				</div>
				<div>
					<a class="change-date today" href="<?php
					if ( 'schedule' === $view ) {
						echo esc_url( add_query_arg( array(
							'calendar_day'   => '',
							'calendar_year'  => date( 'Y' ),
							'calendar_month' => date( 'm' ),
							'view'           => 'schedule',
						) ) );
					} else {
						echo esc_url( add_query_arg( array(
							'calendar_day' => current_time( 'Y-m-d' ),
							'view'         => 'day',
						) ) );
					}
					?>"><?php esc_html_e( 'Today', 'woocommerce-bookings' ); ?></a>
				</div>
				<div>
					<a class="change-date next" href="<?php
						echo esc_url( add_query_arg( array(
							'calendar_year'  => $month == 12 ? $year + 1 : $year,
							'calendar_month' => $month == 12 ? 1 : $month + 1,
						) ) );
					?>">&rarr;</a>
				</div>
			</div>
		<?php } ?>
	<?php endif;?>
	<?php if ( 'day' === $view ): ?>
		<?php if ( ! WC_BOOKINGS_GUTENBERG_EXISTS ) { ?>
			<div class="date_selector">
				<a class="prev" href="<?php echo esc_url( add_query_arg( 'calendar_day', date_i18n( 'Y-m-d', strtotime( '-1 day', strtotime( $day ) ) ) ) ); ?>">&larr;</a>
				<div>
					<input type="text" name="calendar_day" class="calendar_day" placeholder="yyyy-mm-dd" value="<?php echo esc_attr( $day ); ?>" />
				</div>

				<a class="next" href="<?php echo esc_url( add_query_arg( 'calendar_day', date_i18n( 'Y-m-d', strtotime( '+1 day', strtotime( $day ) ) ) ) ); ?>">&rarr;</a>
			</div>
		<?php } else { ?>
			<div class="date-selector-popover">
				<div class="current_month">
					<div id="wc-bookings-datepicker-container-day">
					</div>
				</div>
				<div>
					<a class="change-date prev" href="<?php
						echo esc_url( add_query_arg( 'calendar_day', date_i18n( 'Y-m-d', strtotime( '-1 day', strtotime( $day ) ) ) ) );
					?>">&larr;</a>
				</div>
				<div>
					<a class="change-date today" href="<?php
						echo esc_url( add_query_arg( array(
							'calendar_day' => current_time( 'Y-m-d' ),
							'view'         => 'day',
						) ) );
					?>"><?php esc_html_e( 'Today', 'woocommerce-bookings' ); ?></a>
				</div>
				<div>
					<a class="change-date next" href="<?php
						echo esc_url( add_query_arg( 'calendar_day', date_i18n( 'Y-m-d', strtotime( '+1 day', strtotime( $day ) ) ) ) );
					?>">&rarr;</a>
				</div>
			</div>
		<?php } ?>
	<?php endif;?>
	<div class="views">
		<a class="view-select <?php echo ( 'schedule' === $view ) ? 'current' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'view', 'schedule' ) ); ?>">
			<?php esc_html_e( 'Schedule', 'woocommerce-bookings' ); ?>
		</a>
		<a class="view-select <?php echo ( 'month' === $view ) ? 'current' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'view', 'month' ) ); ?>">
			<?php esc_html_e( 'Month', 'woocommerce-bookings' ); ?>
		</a>
		<a class="view-select <?php echo ( 'day' === $view ) ? 'current' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'view', 'day' ) ); ?>">
			<?php esc_html_e( 'Day', 'woocommerce-bookings' ); ?>
		</a>
	</div>
</div>
<script type="text/javascript">
	jQuery( ".tablenav select, .tablenav input" ).change( function() {
		jQuery( "#mainform" ).submit();
	} );
</script>
