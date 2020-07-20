<?php
/**
 * @var array $reports
 * @var Wpdb $wpdb
 */

if ( empty($reports) ) {
	$heading = sprintf(__('Report for %s', 'follow_up_emails'), $email);
} else {
	$report = $reports[0];
	$heading = sprintf(__('Report for %s (%s)', 'follow_up_emails'), $report->customer_name, $report->email_address);
}

if ( $user_id ):
?>
<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
	<a href="<?php echo esc_url( get_edit_user_link( $user_id ) ); ?>" class="nav-tab"><?php esc_html_e('Personal Options', 'follow_up_emails'); ?></a>
	<a href="#" class="nav-tab nav-tab-active"><?php esc_html_e('Customer Data', 'follow_up_emails'); ?></a>
</h2>
<?php endif; ?>

<div id="fue_user_report">
	<div class="col-left">
		<h3><?php echo esc_html( $heading ); ?></h3>
		<table class="widefat fixed striped posts">
			<thead>
			<tr>
				<th scope="col" id="email_name" class="manage-column column-email_name" style=""><?php esc_html_e('Email', 'follow_up_emails'); ?></th>
				<th scope="col" id="trigger" class="manage-column column-trigger" style=""><?php esc_html_e('Trigger', 'follow_up_emails'); ?></th>
				<th scope="col" id="opened" class="manage-column column-opened"><?php esc_html_e('Opened', 'follow_up_emails'); ?></th>
				<th scope="col" id="clicked" class="manage-column column-clicked"><?php esc_html_e('Clicked', 'follow_up_emails'); ?></th>
				<?php do_action('fue_report_customer_emails_header'); ?>
				<th scope="col" id="date_sent" class="manage-column column-date_sent" style=""><?php esc_html_e('Date Sent', 'follow_up_emails'); ?></th>
				<th scope="col" id="order" class="manage-column column-order" style="">&nbsp;</th>
				<th scope="col" width="30" class="manage-column column-toggle" style=""><a href="#" class="table-toggle"><span class="dashicons dashicons-arrow-up">&nbsp;</span></a></th>
			</tr>
			</thead>
			<tbody>
			<?php
			if ( empty($reports) ):
				?>
				<tr scope="row">
					<th colspan="6"><?php esc_html_e('No reports available', 'follow_up_emails'); ?></th>
				</tr>
			<?php
			else:
				foreach ($reports as $report):
					$opens = FUE_Reports::count_opened_emails( array('email_order_id' => $report->email_order_id) );
					$clicks= FUE_Reports::count_total_email_clicks( array('email_order_id' => $report->email_order_id) );
					?>
					<tr scope="row">
						<td class="post-title column-title"><?php echo esc_html( $report->email_name ); ?></td>
						<td><?php echo esc_html( $report->email_trigger ); ?></td>
						<td><?php echo $opens > 0 ? wp_kses_post( __('<span class="dashicons dashicons-visibility" style="color:#7AD03A;"></span> Yes', 'follow_up_emails') ) : wp_kses_post( __('<span class="dashicons dashicons-hidden" style="color:#EEE;"></span> No', 'follow_up_emails')); ?></td>
						<td><?php echo $clicks > 0 ? wp_kses_post( __('<span class="dashicons dashicons-carrot" style="color:#7AD03A;"></span> Yes', 'follow_up_emails') ) : wp_kses_post( __('<span class="dashicons dashicons-carrot" style="color:#EEE;"></span> No', 'follow_up_emails')); ?></td>
						<?php do_action('fue_report_customer_emails_row', $report); ?>
						<td><?php echo esc_html( date( get_option('date_format') .' '. get_option('time_format') , strtotime($report->date_sent)) ); ?></td>
						<td>
							<?php
							$btn_empty = true;
							if ($report->order_id != 0) {
								$btn_empty = false;
								echo '<a class="button" href="post.php?post='. esc_attr( $report->order_id ) .'&action=edit">View Order</a><br/><br/>';
							}

							$queue_item = new FUE_Sending_Queue_Item( $report->email_order_id );

							if ( $queue_item->exists() ) {
								echo '<a class="button" target="_blank" href="'. esc_url( $queue_item->get_web_version_url() ) .'">View Email</a><br/>';
							}

							?>
						</td>
						<td>&nbsp;</td>
					</tr>
				<?php
				endforeach;
			endif;
			?>
			</tbody>
		</table>

		<hr>


		<h3>
			<?php
			esc_html_e('Scheduled Emails', 'follow_up_emails');
			$url = add_query_arg( array(
				'action'    => 'fue_wc_clear_cart',
				'_wpnonce'  => wp_create_nonce('wc_clear_cart'),
				'email'     => ($email) ? $email : '',
				'user_id'   => ($user_id) ? $user_id : ''
			), 'admin-post.php' );
			?>
			<a class="button button-secondary" href="<?php echo esc_url( $url ); ?>" style="float: right;">
				<?php esc_html_e('Clear Cart Emails', 'follow_up_emails'); ?>
			</a>
		</h3>

		<table class="widefat fixed striped posts">
			<thead>
			<tr>
				<th scope="col" id="order" class="manage-column column-product" style="" width="60"><?php esc_html_e('Order', 'follow_up_emails'); ?></th>
				<th scope="col" id="email_name" class="manage-column column-email_name" style=""><?php esc_html_e('Email', 'follow_up_emails'); ?></th>
				<th scope="col" id="status" class="manage-column column-status" style=""><?php esc_html_e('Status', 'follow_up_emails'); ?></th>
				<th scope="col" id="date_sent" class="manage-column column-date_sent" style="" width="180"><?php esc_html_e('Scheduled', 'follow_up_emails'); ?></th>
				<?php do_action('fue_reports_customer_scheduled_header'); ?>
				<th scope="col" width="30" class="manage-column column-toggle" style=""><a href="#" class="table-toggle"><span class="dashicons dashicons-arrow-up">&nbsp;</span></a></th>
			</tr>
			</thead>
			<tbody>
			<?php if ( empty($queue) ): ?>
				<tr>
					<td colspan="5"><?php esc_html_e('No emails scheduled', 'follow_up_emails'); ?></td>
				</tr>
			<?php
			else:
				$email_rows     = array();
				$date_format    = get_option('date_format') .' '. get_option('time_format');
				foreach ( $queue as $row ):
					$item = new FUE_Sending_Queue_Item( $row->id );

					if (! isset($email_rows[$row->email_id]) ) {
						$email_row = new FUE_Email( $row->email_id );
						$email_rows[$row->email_id] = $email_row;
					}

					$email_name = $email_rows[$row->email_id]->name;
					$email = $email_rows[$row->email_id];

					if (! $email->exists() ) {
						continue;
					}

					?>
					<tr>
						<td>
							<?php
							if ( $row->order_id > 0 && ($order = WC_FUE_Compatibility::wc_get_order($row->order_id)) ) {
								echo '<a href="post.php?post='. esc_attr( $row->order_id ) .'&action=edit">'. esc_html( $order->get_order_number() ) .'</a>';
							} else {
								echo '-';
							}

							if ( $row->product_id > 0 ) {
								echo ' for <a href="post.php?post='. esc_attr( $row->product_id ) .'&action=edit">'. esc_html( get_the_title($row->product_id) ) .'</a>';
							}
							?>
						</td>
						<td>
							<?php
							echo wp_kses_post( sprintf(
								__('<a href="%s">#%d %s</a><br/><small>(%s)</small>', 'follow_up_emails'),
								esc_url( admin_url('post.php?post='. $item->email_id .'&action=edit') ),
								$item->email_id,
								$email->name,
								$email->get_trigger_string()
							) );
							?>
						</td>
						<td class="status">
							<?php
							if ( $row->status == 1 ) {
								echo esc_html__('Queued', 'follow_up_emails');
								echo '<br/><small><a href="#" class="queue-toggle" data-status="queued" data-id="'. esc_attr( $row->id ) .'">'. esc_html__('Do not send', 'follow_up_emails') .'</a></small>';
							} else {
								echo esc_html__('Suspended', 'follow_up_emails');
								echo '<br/><small><a href="#" class="queue-toggle" data-status="paused" data-id="'. esc_attr( $row->id ) .'">'. esc_html__('Re-enable', 'follow_up_emails') .'</a></small>';
							}
							?>
						</td>
						<td>
							<?php echo esc_html( date( $date_format, $row->send_on ) ); ?>
						</td>
						<?php do_action('fue_reports_customer_scheduled_row', $row); ?>
						<td>&nbsp;</td>
					</tr>
				<?php
				endforeach;
			endif;
			?>
			</tbody>
		</table>

		<hr>

		<h3><?php esc_html_e('Conversions', 'follow_up_emails'); ?></h3>

		<table class="wp-list-table widefat fixed striped posts">
			<thead>
			<tr>
				<th><?php esc_html_e('Email Received', 'follow_up_emails'); ?></th>
				<th><?php esc_html_e('Order #', 'follow_up_emails'); ?></th>
				<th><?php esc_html_e('Conversion Value', 'follow_up_emails'); ?></th>
				<?php do_action('fue_reports_customer_conversions_header'); ?>
				<th>&nbsp;</th>
				<th scope="col" width="30" class="manage-column column-toggle" style=""><a href="#" class="table-toggle"><span class="dashicons dashicons-arrow-up">&nbsp;</span></a></th>
			</tr>
			</thead>
			<?php if ( empty($conversions) ): ?>
				<tr>
					<td colspan="5"><?php esc_html_e('No conversions found', 'follow_up_emails'); ?></td>
				</tr>
			<?php
			else:
				/* @var $order WC_Order */
				$total_conversions = 0;
				foreach ( $conversions as $conversion ):
					$order      = $conversion['order'];
					$user       = new WP_User( WC_FUE_Compatibility::get_order_prop( $order, 'customer_user' ) );
					$name       = $user->billing_first_name .' '. $user->billing_last_name;
					$total_conversions += WC_FUE_Compatibility::get_order_prop( $order, 'order_total' );
				?>
					<tr>
						<td><?php echo '<a href="'. esc_url( get_edit_post_link( $conversion['email']->id ) ) .'">'. esc_html( $conversion['email']->name ) .'</a>'; ?></td>
						<td><?php echo '<a href="'. esc_url( get_edit_post_link( WC_FUE_Compatibility::get_order_prop( $order, 'id' ) ) ) .'">Order #'. esc_html( WC_FUE_Compatibility::get_order_prop( $order, 'id' ) ) .'</a>'; ?></td>
						<td><?php echo wp_kses_post( wc_price( WC_FUE_Compatibility::get_order_prop( $order, 'order_total' ) ) ); ?></td>
						<?php do_action('fue_reports_customer_conversion_row', $conversion); ?>
						<td><?php echo '<a class="button button-secondary" href="'. esc_url( get_edit_post_link( WC_FUE_Compatibility::get_order_prop( $order, 'id' ) ) ) .'">View Order</a>'; ?></td>
						<td>&nbsp;</td>
					</tr>
				<?php
				endforeach;
			endif;
			?>
			</tbody>
		</table>

		<hr>

		<h3><?php esc_html_e('Abandoned Items', 'follow_up_emails'); ?></h3>

		<?php if ( $cart_updated ): ?>
			<p><?php echo esc_html( sprintf( __('Last updated: %s', 'follow_up_emails'), date( wc_date_format() .' '. wc_time_format(), strtotime( $cart_updated ) ) ) ); ?></p>
			<p><?php echo esc_html( sprintf( __('Status: %s', 'follow_up_emails'), FUE_Addon_Woocommerce_Cart::get_cart_status( $user_id ) ) ); ?></p>
		 <?php endif; ?>
		<table class="wp-list-table widefat fixed striped posts">
			<thead>
			<tr>
				<th>&nbsp;</th>
				<th><?php esc_html_e('Product', 'follow_up_emails'); ?></th>
				<th><?php esc_html_e('Quantity', 'follow_up_emails'); ?></th>
				<th><?php esc_html_e('Price', 'follow_up_emails'); ?></th>
				<?php do_action('fue_reports_customer_abandoned_header'); ?>
				<th scope="col" width="30" class="manage-column column-toggle" style=""><a href="#" class="table-toggle"><span class="dashicons dashicons-arrow-up">&nbsp;</span></a></th>
			</tr>
			</thead>
			<?php if ( empty($cart) ): ?>
				<tr>
					<td colspan="5"><?php esc_html_e('No saved cart items', 'follow_up_emails'); ?></td>
				</tr>
			<?php
			else:
				/* @var $order WC_Order */

				foreach ( $cart['cart_items'] as $cart_item_key => $cart_item ):
					$product_id   = ($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id'];
					$_product     = WC_FUE_Compatibility::wc_get_product( $product_id );
					?>
					<tr>
						<td>
							<?php
							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

							if ( ! $_product->is_visible() ) {
								echo wp_kses_post( sprintf( '<a href="%s">%s</a>', '#', $thumbnail ) );
							} else {
								echo wp_kses_post( sprintf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), $thumbnail ) );
							}
							?>
						</td>
						<td>
							<?php
							if ( ! $_product->is_visible() ) {
								echo esc_html( apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) ) . '&nbsp;';
							} else {
								echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s </a>', esc_url( $_product->get_permalink( $cart_item ) ), $_product->get_title() ), $cart_item, $cart_item_key ) );
							}

							// Meta data
							if ( version_compare( WC_VERSION, '3.3', '>=' ) ) {
								echo esc_html( wc_get_formatted_cart_item_data( $cart_item ) );
							} else {
								echo esc_html( WC()->cart->get_item_data( $cart_item ) );
							}
							?>
						</td>
						<td><?php echo esc_html( $cart_item['quantity'] ); ?></td>
						<td>
							<?php
							echo esc_html( apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ) );
							?>
						</td>
						<?php do_action('fue_reports_customer_abandoned_row', $cart_item, $cart_item_key); ?>
						<td>&nbsp;</td>
					</tr>
				<?php
				endforeach;
			endif;
			?>
			</tbody>
		</table>

		<hr>

		<h3><?php esc_html_e('Opt-Outs', 'follow_up_emails'); ?></h3>

		<table class="widefat fixed striped posts">
			<thead>
			<tr>
				<th scope="col" id="exclude_user_email" class="manage-column column-user_email" style=""><?php esc_html_e('Email', 'follow_up_emails'); ?></th>
				<th scope="col" id="exclude_order" class="manage-column column-order" style=""><?php esc_html_e('Order', 'follow_up_emails'); ?></th>
				<th scope="col" id="exclude_date_added" class="manage-column column-date_added" style=""><?php esc_html_e('Date Added', 'follow_up_emails'); ?></th>
				<?php do_action('fue_reports_customer_optout_header'); ?>
				<th scope="col" width="30" class="manage-column column-toggle" style=""><a href="#" class="table-toggle"><span class="dashicons dashicons-arrow-up">&nbsp;</span></a></th>
			</tr>
			</thead>
			<tbody>
			<?php if ( empty($excludes) ): ?>
				<tr>
					<td colspan="4"><?php esc_html_e('No opt-outs found', 'follow_up_emails'); ?></td>
				</tr>
			<?php
			else:
				$date_format    = get_option('date_format') .' '. get_option('time_format');
				foreach ( $excludes as $row ):
					$order        = wc_get_order( $row->order_id );
					$order_number = $order instanceof WC_Order ? $order->get_order_number() : $row->order_id;
					$order_str    = ( empty( $row->order_id ) ) ? __( 'All emails', 'follow_up_emails' ) : $order_number;
					?>
					<tr>
						<td><?php echo esc_html( $row->email ); ?></td>
						<td><?php echo esc_html( $order_str ); ?></td>
						<td>
							<?php echo esc_html( date( $date_format, strtotime($row->date_added) ) ); ?>
						</td>
						<?php do_action('fue_reports_customer_optout_row', $row); ?>
						<td>&nbsp;</td>
					</tr>
				<?php
				endforeach;
			endif;
			?>
			</tbody>
		</table>

		<hr>

		<h3><?php esc_html_e('Order History', 'follow_up_emails'); ?></h3>

		<table class="widefat fixed striped posts">
			<thead>
			<tr>
				<th scope="col" class="manage-column" style=""><?php esc_html_e( 'Order (status)', 'follow_up_emails' ); ?></th>
				<th scope="col" class="manage-column" style=""><?php esc_html_e( 'Date', 'follow_up_emails' ); ?></th>
				<th scope="col" class="manage-column" style=""><?php esc_html_e( 'Total', 'follow_up_emails' ); ?></th>
				<th scope="col" class="manage-column" style=""></th>
				<?php do_action('fue_reports_customer_orders_header'); ?>
				<th scope="col" width="30" class="manage-column column-toggle" style=""><a href="#" class="table-toggle"><span class="dashicons dashicons-arrow-up">&nbsp;</span></a></th>
			</tr>
			</thead>
			<tbody>
			<?php
			// Setup important variables
			$lifetime_total = 0;
			$count          = 1;

			if ( $user_id ) {
				$args = array(
					'numberposts' => -1,
					'meta_key'    => '_customer_user',
					'meta_value'  => absint( $user_id ),
					'post_type'   => 'shop_order',
					'post_status' => function_exists( 'wc_get_order_statuses' ) ? array_keys( wc_get_order_statuses() ) : array( 'publish' ),
					'order'       => 'ASC',
				);
			} else {
				$args = array(
					'numberposts' => -1,
					'meta_key'    => '_billing_email',
					'meta_value'  => $email,
					'post_type'   => 'shop_order',
					'post_status' => function_exists( 'wc_get_order_statuses' ) ? array_keys( wc_get_order_statuses() ) : array( 'publish' ),
					'order'       => 'ASC',
				);
			}


			$orders = get_posts( $args );
			if ( ! empty( $orders ) ) {
				foreach ( $orders as $key => $purchase ) {
					$purchase_order = new WC_Order( $purchase );

					// If order isn't cancelled, refunded, failed or pending, include its total
					if ( in_array( $purchase->post_status, array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) ) {
						$lifetime_total += WC_FUE_Compatibility::get_order_prop( $purchase_order, 'order_total' );
						$lifetime_total -= WC_FUE_Compatibility::get_order_prop( $purchase_order, 'total_refunded' );
					}
					?>
					<tr>
						<td>
							<?php
							$url = esc_url( admin_url('post.php?post='. WC_FUE_Compatibility::get_order_prop( $purchase_order, 'id' ) .'&action=edit') );
							echo wp_kses_post( sprintf( __('<a href="%s">%s</a> <br /> (%s)', 'follow_up_emails' ), $url, $purchase_order->get_order_number(), WC_FUE_Compatibility::get_order_prop( $purchase_order, 'status' ) ) );
							?>
						</td>
						<td><?php echo esc_html( date( get_option('date_format') .' '. get_option('time_format') , strtotime( WC_FUE_Compatibility::get_order_prop( $purchase_order, 'order_date' ) ) ) ); ?></td>
						<td><?php echo wp_kses_post( $purchase_order->get_formatted_order_total() ); ?></td>
						<td>
	                        <?php
							if ( isset( $report ) && $report->order_id != 0) {
								echo wp_kses_post( sprintf( __('<a class="button" href="%s">View Order</a>', 'follow_up_emails' ), $url ) );
							} else {
								echo '-';
							}
							?>
						</td>
						<?php do_action('fue_reports_customer_orders_row', $purchase_order); ?>
						<td>&nbsp;</td>
					</tr>
					<?php
				}
			}
			?>
			</tbody>
		</table>

		<hr>

		<p><?php echo wp_kses_post( sprintf( __( '<strong>Lifetime Value:</strong> %s', 'follow_up_emails' ), '<span style="color:#7EB03B; font-size:1.2em; font-weight:bold;">' . wc_price( $lifetime_total ) . '</span>' ) ); ?></p>

	</div>
	<div class="col-right">
		<div class="postbox" id="fue_customer_followups">
			<h3 class="handle"><?php esc_html_e('Schedule Emails', 'follow_up_emails'); ?></h3>
			<div class="inside">
				<p id="schedule_email_error"></p>
				<p id="schedule_email_success"></p>
				<p>
					<label for="email"><?php esc_html_e('Select Email', 'follow_up_emails'); ?></label>
					<br/>
					<select id="email" class="full">
						<option value=""></option>
						<?php
						$emails = fue_get_emails( 'manual', FUE_Email::STATUS_ACTIVE );

						foreach ( $emails as $email ):
							?>
							<option value="<?php echo esc_attr( $email->id ); ?>"><?php echo esc_html( $email->name ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<p>
					<label for="send_schedule"><?php esc_html_e('Send', 'follow_up_emails'); ?></label>
					<select id="send_schedule">
						<option value="now"><?php esc_html_e('now', 'follow_up_emails'); ?></option>
						<option value="later"><?php esc_html_e('later', 'follow_up_emails'); ?></option>
					</select>
				</p>
				<p class="send-later">
					<input type="text" id="send_date" class="datepicker" placeholder="yyyy-mm-dd" />

					<?php esc_html_e('at', 'follow_up_emails'); ?>
					<select id="send_time_hour">
						<?php
						for ( $x = 1; $x <= 12; $x++ ):
							$y = ($x >= 10) ? $x : '0'. $x;
						?>
						<option value="<?php echo esc_attr( $x ); ?>"><?php echo esc_html( $y ); ?></option>
						<?php endfor; ?>
					</select>
					:
					<select id="send_time_minute">
						<?php for ( $x = 0; $x < 60; $x+=5 ):
							$y = ($x >= 10) ? $x : '0'. $x;
						?>
							<option value="<?php echo esc_attr( $x ); ?>"><?php echo esc_html( $y ); ?></option>
						<?php endfor; ?>
					</select>
					<select id="send_time_ampm">
						<option value="am"><?php esc_html_e('AM', 'follow_up_emails'); ?></option>
						<option value="pm"><?php esc_html_e('PM', 'follow_up_emails'); ?></option>
					</select>
				</p>
				<p class="send-again-p">
					<label>
						<input type="checkbox" id="send_again" />
						<?php esc_html_e('Send again', 'follow_up_emails'); ?>
					</label>

					<span class="send-again">
						<?php esc_html_e('in', 'follow_up_emails'); ?>
						<input type="number" min="1" id="send_again_value" />
						<select id="send_again_interval">
							<option value="minutes"><?php esc_html_e('minutes', 'follow_up_emails'); ?></option>
							<option value="hours"><?php esc_html_e('hours', 'follow_up_emails'); ?></option>
							<option value="days"><?php esc_html_e('days', 'follow_up_emails'); ?></option>
							<option value="weeks"><?php esc_html_e('weeks', 'follow_up_emails'); ?></option>
							<option value="months"><?php esc_html_e('months', 'follow_up_emails'); ?></option>
							<option value="years"><?php esc_html_e('years', 'follow_up_emails'); ?></option>
						</select>
					</span>
				</p>
				<p class="separated">
					<a class="schedule-email button-primary" href="#" data-nonce="<?php echo esc_attr( wp_create_nonce( 'schedule_email' ) ); ?>"><?php esc_html_e('Schedule Email', 'follow_up_emails'); ?></a>
				</p>
			</div>
		</div>
		<?php if ( $customer ): ?>
		<div class="postbox" id="fue_customer_reminders">
			<h3 class="handle"><?php esc_html_e('Reminders', 'follow_up_emails'); ?></h3>
			<div class="inside">
				<ul class="customer-reminders">
					<?php if ( empty( $reminders ) ): ?>
						<li><?php esc_html_e('There are no reminders yet', 'follow_up_emails'); ?></li>
					<?php
					else:
						$date_format    = get_option( 'date_format' );
						$time_format    = get_option( 'time_format' );
						foreach( $reminders as $reminder ):
							$author     = new WP_User( $reminder->meta['author'] );
							$assignee   = new WP_User( $reminder->meta['assignee'] );
							$date   = date( $date_format, $reminder->send_on );
							$time   = date( $time_format, $reminder->send_on );

							if ( $assignee->ID == $author->ID ) {
								$meta = sprintf( __('added by %s', 'follow_up_emails'), $author->display_name );
							} else {
								$meta = sprintf( __('assigned to %s by %s', 'follow_up_emails'), $assignee->display_name, $author->display_name );
							}
							?>
							<li class="reminder" data-id="<?php echo esc_attr( $reminder->id ); ?>">
								<div class="reminder-content">
									<p>
										<?php echo esc_html( sprintf( __('Reminder set for %s at %s', 'follow_up_emails'), $date, $time ) ); ?>
									</p>
									<?php if ( !empty( $reminder->meta['note'] ) ): ?>
										<pre><?php echo wp_kses_post( $reminder->meta['note'] ); ?></pre>
									<?php endif; ?>
								</div>
								<p class="meta">
									<?php echo esc_html( $meta ); ?>
									<a class="delete_reminder" href="#" data-nonce="<?php echo esc_attr( wp_create_nonce( 'delete_reminder_' . $reminder->id ) ); ?>"><?php esc_html_e('Delete', 'follow_up_emails'); ?></a>
								</p>
							</li>
						<?php
						endforeach;
					endif;
					?>
				</ul>
				<div class="add-reminder">
					<h4><?php esc_html_e('Add Reminder', 'follow_up_emails'); ?></h4>

					<p>
						<textarea id="reminder_note" placeholder="<?php esc_attr_e('Reminder notes', 'follow_up_emails'); ?>"></textarea>
					</p>

					<p>
						<label>
							<input type="checkbox" id="assign_reminder" />
							<?php esc_html_e( 'Assign reminder', 'follow_up_emails' ); ?>
						</label>
					</p>

					<p id="assignee_block">
						<select
							id="assignee"
							name="assignee"
							class="user-search-select"
							data-placeholder="<?php esc_attr_e( 'Search for a user&hellip;', 'follow_up_emails' ); ?>"
							data-allow_clear="true"
							tabindex="-1"
							title=""
						></select>
					</p>

					<p class="separated">
						<?php esc_html_e('Send in', 'follow_up_emails'); ?>
						<input type="number" min="1" step="1" id="reminder_interval_days" value="1" size="3" />
						<?php esc_html_e('day(s)', 'follow_up_emails'); ?>

						<a class="set_interval_reminder button" href="#" data-nonce="<?php echo esc_attr( wp_create_nonce( 'set_reminder' ) ); ?>"><?php esc_html_e('Set Reminder', 'follow_up_emails'); ?></a>
					</p>

					<p class="separated">
						<?php esc_html_e('Send on', 'follow_up_emails'); ?>
						<br/>
						<input type="text" id="reminder_date" value="" class="datepicker" placeholder="yyyy-mm-dd" />
						@
						<select id="reminder_hour">
							<option value="01">01</option>
							<option value="02">02</option>
							<option value="03">03</option>
							<option value="04">04</option>
							<option value="05">05</option>
							<option value="06">06</option>
							<option value="07">07</option>
							<option value="08">08</option>
							<option value="09">09</option>
							<option value="10">10</option>
							<option value="11">11</option>
							<option value="12">12</option>
						</select>
						<select id="reminder_minute">
							<option value="00">00</option>
							<option value="05">05</option>
							<option value="10">10</option>
							<option value="15">15</option>
							<option value="20">20</option>
							<option value="25">25</option>
							<option value="30">30</option>
							<option value="35">35</option>
							<option value="40">40</option>
							<option value="45">45</option>
							<option value="50">50</option>
							<option value="55">55</option>
						</select>
						<select id="reminder_ampm">
							<option value="am"><?php esc_html_e('AM', 'follow_up_emails'); ?></option>
							<option value="pm"><?php esc_html_e('PM', 'follow_up_emails'); ?></option>
						</select>

						<a class="set_date_reminder button" href="#" data-nonce="<?php echo esc_attr( wp_create_nonce( 'set_reminder' ) ); ?>"><?php esc_html_e('Set Reminder', 'follow_up_emails'); ?></a>
					</p>
					<br class="clear"/>
				</div>

			</div>
		</div>

		<div class="postbox" id="fue_customer_notes">
			<h3 class="handle"><?php esc_html_e('Customer Notes', 'follow_up_emails'); ?></h3>
			<div class="inside">
				<ul class="customer-notes">
					<?php if ( empty( $notes ) ): ?>
					<li><?php esc_html_e('There are no notes yet', 'follow_up_emails'); ?></li>
					<?php
					else:
						$datetime_format = get_option( 'date_format' ) .' '. get_option( 'time_format' );
						foreach( $notes as $note ):
							$author = new WP_User( $note->author_id );
							$pretty_date = date_i18n( $datetime_format, strtotime( $note->date_added ) );
					?>
					<li class="note" data-id="<?php echo esc_attr( $note->id ); ?>">
						<div class="note-content">
							<p><?php echo wp_kses_post( $note->note ); ?></p>
						</div>
						<p class="meta">
							<?php echo wp_kses_post( sprintf( 'added by %s on <abbr title="%s" class="exact-date">%s</abbr>', $author->display_name, $note->date_added, $pretty_date ) ); ?>
							<a class="delete_note" href="#" data-nonce="<?php echo esc_attr( wp_create_nonce( 'delete_note_' . $note->id ) ); ?>"><?php esc_html_e('Delete note', 'follow_up_emails'); ?></a>
						</p>
					</li>
					<?php
						endforeach;
					endif;
					?>
				</ul>
				<div class="add-note">
					<h4><?php esc_html_e('Add Note', 'follow_up_emails'); ?></h4>

					<p>
						<textarea rows="5" cols="20" class="input-text" id="add_customer_note" name="customer_note" type="text"></textarea>
					</p>

					<p>
						<input type="hidden" id="customer_id" value="<?php echo esc_attr( $customer->id ); ?>" />
						<input type="hidden" id="user_id" value="<?php echo esc_attr( $user_id ); ?>" />
						<a class="add_note button" href="#" data-nonce="<?php echo esc_attr( wp_create_nonce( 'add_notes' ) ); ?>"><?php esc_html_e('Add', 'follow_up_emails'); ?></a>
					</p>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>
