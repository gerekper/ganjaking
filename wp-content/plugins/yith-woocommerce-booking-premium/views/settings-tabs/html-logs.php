<?php
/**
 * Logs tab content.
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit();
$logger = yith_wcbk_logger();

$order_by_options = array(
	'type'  => __( 'Type', 'yith-booking-for-woocommerce' ),
	'group' => __( 'Group', 'yith-booking-for-woocommerce' ),
	'date'  => __( 'Date', 'yith-booking-for-woocommerce' ),
);

$order_options = array(
	'ASC'  => __( 'Asc', 'yith-booking-for-woocommerce' ),
	'DESC' => __( 'Desc', 'yith-booking-for-woocommerce' ),
);

$default_args = array(
	'order_by' => 'date',
	'order'    => 'DESC',
	'limit'    => 20,
	'paged'    => '1',
);

$request = wc_clean( wp_unslash( $_REQUEST ?? array() ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$request = wp_parse_args( $request, $default_args );

$has_logs   = $logger->has_logs();
$logs       = $logger->get_logs( $request );
$total_logs = $logger->count_logs( $request );
$groups     = $logger->get_groups();
$types      = $logger->get_types();

$pagination               = new StdClass();
$pagination->totals       = $total_logs;
$pagination->per_page     = $request['limit'];
$pagination->current_page = max( 1, absint( $request['paged'] ) );
$pagination->pages        = ceil( $total_logs / $request['limit'] );

$delete_log_url = add_query_arg(
	array(
		'yith-wcbk-logs-action' => 'delete-logs',
		'yith-wcbk-logs-nonce'  => wp_create_nonce( 'yith_wcbk_delete_logs' ),
	)
);
if ( ! $logger->is_enabled() ) {
	yith_wcbk_print_notice( __( 'Warning: Booking Logger is not enabled', 'yith-booking-for-woocommerce' ), 'warning' );
}

$selected_group = $request['group'] ?? '';
$selected_type  = $request['type'] ?? '';

?>

<?php if ( $has_logs ) : ?>
	<div id="yith-wcbk-logs-page-actions">
		<a class="yith-plugin-fw__button--trash" href="<?php echo esc_url( $delete_log_url ); ?>"><?php esc_html_e( 'Delete Logs', 'yith-booking-for-woocommerce' ); ?></a>
	</div>
	<div id="yith-wcbk-logs-tab-wrapper" class="yith-plugin-fw">
		<div id="yith-wcbk-logs-tab-wrapper">
			<div id="yith-wcbk-logs-tab-actions" class="clearfix">

				<div class="alignleft actions">
					<form method="post">
						<input type="hidden" name="paged" value="1"/>

						<label><?php echo esc_html( _x( 'Limit', 'Label in Logs tab', 'yith-booking-for-woocommerce' ) ); ?></label>
						<input type="number" min="1" name="limit" value="<?php echo absint( $request['limit'] ); ?>"/>

						<label><?php echo esc_html( _x( 'Group', 'Label in Logs tab', 'yith-booking-for-woocommerce' ) ); ?></label>
						<select name="group">
							<option value=""><?php esc_html_e( 'Any', 'yith-booking-for-woocommerce' ); ?></option>
							<?php foreach ( $groups as $key ) : ?>
								<option value='<?php echo esc_attr( $key ); ?>' <?php echo selected( $key, $selected_group, true ); ?>><?php echo esc_html( YITH_WCBK_Logger_Groups::get_label( $key ) ); ?></option>";
							<?php endforeach; ?>
						</select>

						<label><?php echo esc_html( _x( 'Type', 'Label in Logs tab', 'yith-booking-for-woocommerce' ) ); ?></label>
						<select name="type">
							<option value=""><?php esc_html_e( 'Any', 'yith-booking-for-woocommerce' ); ?></option>
							<?php foreach ( $types as $key ) : ?>
								<option value='<?php echo esc_attr( $key ); ?>' <?php echo selected( $key, $selected_type, true ); ?>><?php echo esc_html( $key ); ?></option>";
							<?php endforeach; ?>
						</select>

						<label><?php echo esc_html( _x( 'Order by', 'Label in Logs tab', 'yith-booking-for-woocommerce' ) ); ?></label>
						<select name="order_by">
							<?php foreach ( $order_by_options as $key => $name ) : ?>
								<option value='<?php echo esc_attr( $key ); ?>' <?php echo selected( $key, $request['order_by'], true ); ?>><?php echo esc_html( $name ); ?></option>";
							<?php endforeach; ?>
						</select>
						<select name="order">
							<?php foreach ( $order_options as $key => $name ) : ?>
								<option value='<?php echo esc_attr( $key ); ?>' <?php echo selected( $key, $request['order'], true ); ?>><?php echo esc_html( $name ); ?></option>";
							<?php endforeach; ?>
						</select>

						<input type="submit" class="yith-plugin-fw__button--secondary" value="<?php esc_html_e( 'Filter', 'yith-booking-for-woocommerce' ); ?>">
					</form>
				</div>

				<div class="alignright actions">
					<form method="post">
					<span class="displaying-num">
						<?php
						// translators: %s is the number of items.
						echo esc_html( sprintf( _n( '%s item', '%s items', 'yith-booking-for-woocommerce' ), $pagination->totals ) );
						?>
					</span>
						<span class="pagination">
						<?php
						if ( $pagination->pages > 1 ) {
							$first = "<span class='navspan first' aria-hidden='true'>«</span>";
							$prev  = "<span class='navspan prev' aria-hidden='true'>‹</span>";
							$next  = "<span class='navspan next' aria-hidden='true'>›</span>";
							$last  = "<span class='navspan last' aria-hidden='true'>»</span>";
							if ( $pagination->current_page > 1 ) {
								$prev_url = add_query_arg( array_merge( $request, array( 'paged' => $pagination->current_page - 1 ) ) );
								$prev     = sprintf( '<a href="%s">%s</a>', esc_url( $prev_url ), $prev );

								$first_url = add_query_arg( array_merge( $request, array( 'paged' => 1 ) ) );
								$first     = sprintf( '<a href="%s">%s</a>', esc_url( $first_url ), $first );
							}

							if ( $pagination->current_page < $pagination->pages ) {
								$next_url = add_query_arg( array_merge( $request, array( 'paged' => $pagination->current_page + 1 ) ) );
								$next     = sprintf( '<a href="%s">%s</a>', esc_url( $next_url ), $next );

								$last_url = add_query_arg( array_merge( $request, array( 'paged' => $pagination->pages ) ) );
								$last     = sprintf( '<a href="%s">%s</a>', esc_url( $last_url ), $last );
							}

							$current = sprintf(
							/* translators: 1: Current page, 2: Total pages. */
								_x( '%1$s of %2$s', 'paging', 'yith-booking-for-woocommerce' ),
								'<span class="current-page"><input type="text" name="paged" value="' . esc_attr( $pagination->current_page ) . '" size="3" /></span>',
								esc_html( $pagination->pages )
							);

							echo $first . $prev . $current . $next . $last; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</span>
					</form>
				</div>

				<div class="clear"></div>
			</div>
			<?php if ( ! ! $logs ) : ?>
				<div id="yith-wcbk-logs-tab">
					<table id="yith-wcbk-logs-tab-table" class="yith-plugin-fw__classic-table">
						<thead>
						<tr>
							<th class="type-column"><?php esc_html_e( 'Type', 'yith-booking-for-woocommerce' ); ?></th>
							<th class="group-column"><?php esc_html_e( 'Group', 'yith-booking-for-woocommerce' ); ?></th>
							<th class="description-column"><?php esc_html_e( 'Description', 'yith-booking-for-woocommerce' ); ?></th>
							<th class="date-column"><?php esc_html_e( 'Date', 'yith-booking-for-woocommerce' ); ?></th>
						</tr>
						</thead>
						<tbody>


						<?php
						foreach ( $logs as $log ) :
							$group_label = YITH_WCBK_Logger_Groups::get_label( $log->group );
							?>
							<tr>
								<td class="type-column">
									<span class="yith-wcbk-logs-type <?php echo esc_attr( $log->type ); ?>"><?php echo esc_html( $log->type ); ?></span>
								</td>
								<td class="group-column">
									<span class="yith-wcbk-logs-group <?php echo esc_attr( $log->group ); ?>"><?php echo esc_html( $group_label ); ?></span>
								</td>
								<td class="description-column">
									<?php
									$expand_class = ! ! strstr( $log->description, PHP_EOL ) || strlen( $log->description ) > 100 ? '' : 'disabled';
									?>
									<span class="expand <?php echo esc_attr( $expand_class ); ?>"></span>
									<div class="log-description"><?php echo esc_html( $log->description ); ?></div>
								</td>
								<td class="date-column"><?php echo esc_html( $log->date ); ?></td>
							</tr>
						<?php endforeach ?>

						</tbody>
					</table>
				</div>
			<?php else : ?>
				<?php
				yith_plugin_fw_get_component(
					array(
						'type'     => 'list-table-blank-state',
						'icon_url' => YITH_WCBK_ASSETS_URL . '/images/empty-log.svg',
						'message'  => __( 'No logs for the selected filters.', 'yith-booking-for-woocommerce' ),
					),
					true
				)
				?>
			<?php endif; ?>
		</div>
	</div>
<?php else : ?>
	<?php
	yith_plugin_fw_get_component(
		array(
			'type'     => 'list-table-blank-state',
			'icon_url' => YITH_WCBK_ASSETS_URL . '/images/empty-log.svg',
			'message'  => __( 'There are no logs yet!', 'yith-booking-for-woocommerce' ),
		),
		true
	)
	?>
<?php endif; ?>
