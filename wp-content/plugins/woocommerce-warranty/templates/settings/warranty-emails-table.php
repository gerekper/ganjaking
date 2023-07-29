<?php
/**
 * The template for displaying warranty options.
 *
 * @package WooCommerce_Warranty\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<style type="text/css">
	span.status-label {line-height: 30px;}
</style>
<p class="description">
	<?php esc_html_e( 'Available variables:', 'wc_warranty' ); ?>
	<code>{order_id}</code>, <code>{rma_code}</code>,
	<code>{product_id}</code>, <code>{product_name}</code>, <code>{warranty_status}</code>,
	<?php
	foreach ( $custom_vars as $custom_var ) {
		$custom_var = str_replace( '-', '_', sanitize_title( strtolower( $custom_var ) ) );
		echo '<code>{' . esc_html( $custom_var ) . '}</code>, ';
	}
	?>
	<code>{coupon_code}</code>, <code>{refund_amount}</code>,
	<code>{customer_name}</code>, <code>{customer_email}</code>, <code>{customer_shipping_code}</code>,
	<code>{store_shipping_code}</code>, <code>{warranty_request_url}</code>, <code>{store_url}</code>
</p>

<table class="wp-list-table widefat fixed posts generic-table striped">
	<thead>
	<tr>
		<th scope="col" id="trigger" class="manage-column column-trigger" width="17%"><?php esc_html_e( 'Trigger', 'wc_warranty' ); ?></th>
		<th scope="col" id="settings" class="manage-column column-settings" style=""><?php esc_html_e( 'Settings', 'wc_warranty' ); ?></th>
		<th scope="col" id="message" class="manage-column column-message" width="35%"><?php esc_html_e( 'Message', 'wc_warranty' ); ?></th>
		<th scope="col" id="delete" class="manage-column column-delete" width="30"></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<td colspan="4">
			<a class="button add-email" href="#"><?php esc_html_e( '+ Add Email', 'wc_warranty' ); ?></a>
		</td>
	</tr>
	</tfoot>
	<tbody id="emails_tbody">
	<?php
	$admin_email = get_option( 'admin_email' );
	if ( ! empty( $emails ) ) :
		$idx = 0;

		foreach ( $emails as $email_status => $status_email ) :
			foreach ( $status_email as $email ) :
				if ( ! isset( $email['from_status'] ) ) {
					$email['from_status'] = 'any';
				}

				if ( ! isset( $email['trigger'] ) ) {
					$email['trigger'] = 'status';
				}

				if ( 'Request Tracking' === $email_status ) {
					$email['trigger'] = 'request_tracking';
				}
				?>
				<tr id="email_<?php echo esc_attr( $idx ); ?>">
					<td>
						<p>
							<label for="trigger_<?php echo esc_attr( $idx ); ?>"><?php esc_html_e( 'Trigger', 'wc_warranty' ); ?></label>
							<br/>
							<select name="trigger[<?php echo esc_attr( $idx ); ?>]" class="trigger" id="trigger_<?php echo esc_attr( $idx ); ?>">
								<option value="status" <?php selected( 'status', $email['trigger'] ); ?>><?php esc_html_e( 'Status change', 'wc_warranty' ); ?></option>
								<option value="request_tracking" <?php selected( 'request_tracking', $email['trigger'] ); ?>><?php esc_html_e( 'Request Tracking', 'wc_warranty' ); ?></option>
								<option value="item_refunded" <?php selected( 'item_refunded', $email['trigger'] ); ?>><?php esc_html_e( 'Item Refunded', 'wc_warranty' ); ?></option>
								<option value="coupon_sent" <?php selected( 'coupon_sent', $email['trigger'] ); ?>><?php esc_html_e( 'Coupon Sent', 'wc_warranty' ); ?></option>
							</select>
						</p>
						<div class="trigger_status">
							<p>
								<label for="from_status_<?php echo esc_attr( $idx ); ?>"><?php esc_html_e( 'From', 'wc_warranty' ); ?></label>
								<br/>
								<select name="from_status[<?php echo esc_attr( $idx ); ?>]" id="from_status_<?php echo esc_attr( $idx ); ?>" >
									<option value="any"><?php esc_html_e( 'Any status', 'wc_warranty' ); ?></option>
									<?php foreach ( $all_statuses as $warranty_status ) : ?>
										<option value="<?php echo esc_attr( $warranty_status->slug ); ?>" <?php selected( $email['from_status'], $warranty_status->slug ); ?>><?php echo esc_html( $warranty_status->name ); ?></option>
									<?php endforeach; ?>
								</select>
							</p>

							<p>
								<label for="to_status_<?php echo esc_attr( $idx ); ?>"><?php esc_html_e( 'To', 'wc_warranty' ); ?></label>
								<br/>
								<select name="status[<?php echo esc_attr( $idx ); ?>]" id="to_status_<?php echo esc_attr( $idx ); ?>">
									<?php foreach ( $all_statuses as $warranty_status ) : ?>
										<option value="<?php echo esc_attr( $warranty_status->slug ); ?>" <?php selected( $email_status, $warranty_status->slug ); ?>><?php echo esc_html( $warranty_status->name ); ?></option>
									<?php endforeach; ?>
								</select>
							</p>
						</div>
					</td>
					<td>
						<div>
							<label for="recipient_<?php echo esc_attr( $idx ); ?>"><?php esc_html_e( 'Recipient', 'wc_warranty' ); ?></label>
							<br/>
							<select name="send_to[<?php echo esc_attr( $idx ); ?>]" id="recipient_<?php echo esc_attr( $idx ); ?>" class="recipient-select">
								<option value="customer" <?php selected( 'customer', $email['recipient'] ); ?>><?php esc_html_e( 'Customer', 'wc_warranty' ); ?></option>
								<option value="admin" <?php selected( 'admin', $email['recipient'] ); ?>><?php esc_html_e( 'Admin', 'wc_warranty' ); ?></option>
								<option value="both" <?php selected( 'both', $email['recipient'] ); ?>><?php esc_html_e( 'Customer &amp; Admin', 'wc_warranty' ); ?></option>
							</select>
							<br />
							<div class="search-container">
								<?php
								$recipient_emails = array_filter( array_map( 'trim', explode( ',', @$email['admin_recipients'] ) ) );
								$json             = array();
								foreach ( $recipient_emails as $recipient_email ) {
									$json[ $recipient_email ] = $recipient_email;
								}
								$email_data = wp_json_encode( $json );
								$email_data = function_exists( 'wc_esc_json' ) ? wc_esc_json( $email_data ) : _wp_specialchars( $email_data, ENT_QUOTES, 'UTF-8', true );
								?>
								<select
									class="admin-recipients email-search-select"
									name="admin_recipients[<?php echo esc_attr( $idx ); ?>][]"
									multiple="multiple"
									placeholder="<?php echo esc_attr( $admin_email ); ?>"
									style="width: 400px"
								>
								<?php foreach ( $json as $recipient_id => $recipient_name ) : ?>
									<option value="<?php echo esc_attr( $recipient_id ); ?>" selected="selected"><?php echo esc_html( $recipient_name ); ?></option>
								<?php endforeach; ?>
								</select>
							</div>
						</div>

						<p>
							<label for="subject_<?php echo esc_attr( $idx ); ?>"><?php esc_html_e( 'Subject', 'wc_warranty' ); ?></label>
							<br/>
							<input type="text" name="subject[<?php echo esc_attr( $idx ); ?>]" id="subject_<?php echo esc_attr( $idx ); ?>" value="<?php echo esc_attr( $email['subject'] ); ?>" class="" style="width:100%;" />
						</p>
					</td>
					<td>
						<textarea name="message[<?php echo esc_attr( $idx ); ?>]" rows="5" style="width: 99%;"><?php echo esc_attr( $email['message'] ); ?></textarea>
					</td>
					<td><a class="button delete-row" href="#">&times;</a></td>
				</tr>
				<?php
					$idx++;
			endforeach;
		endforeach;
	else :
		?>
		<tr id="email_0">
			<td>
				<p>
					<label for="trigger_0"><?php esc_html_e( 'Trigger', 'wc_warranty' ); ?></label>
					<br/>
					<select name="trigger[0]" class="trigger">
						<option value="status"><?php esc_html_e( 'Status change', 'wc_warranty' ); ?></option>
						<option value="request_tracking"><?php esc_html_e( 'Request Tracking', 'wc_warranty' ); ?></option>
						<option value="item_refunded"><?php esc_html_e( 'Item Refunded', 'wc_warranty' ); ?></option>
						<option value="coupon_sent"><?php esc_html_e( 'Coupon Sent', 'wc_warranty' ); ?></option>
					</select>
				</p>
				<div class="trigger_status">
					<p>
						<label for="from_status_0"><?php esc_html_e( 'From', 'wc_warranty' ); ?></label>
						<br/>
						<select name="from_status[0]" id="from_status__id">
							<option value="any"><?php esc_html_e( 'Any status', 'wc_warranty' ); ?></option>
							<?php foreach ( $all_statuses as $warranty_status ) : ?>
								<option value="<?php echo esc_attr( $warranty_status->slug ); ?>"><?php echo esc_html( $warranty_status->name ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>

					<p>
						<label for="to_status_0"><?php esc_html_e( 'To', 'wc_warranty' ); ?></label>
						<br/>
						<select name="status[0]" id="to_status_0">
							<?php foreach ( $all_statuses as $warranty_status ) : ?>
								<option value="<?php echo esc_attr( $warranty_status->slug ); ?>"><?php echo esc_html( $warranty_status->name ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
				</div>
			</td>
			<td>
				<p>
					<label for="recipient_0"><?php esc_html_e( 'Recipient', 'wc_warranty' ); ?></label>
					<br/>
					<select name="send_to[0]" id="recipient_0">
						<option value="customer"><?php esc_html_e( 'Customer', 'wc_warranty' ); ?></option>
						<option value="admin"><?php esc_html_e( 'Admin', 'wc_warranty' ); ?></option>
						<option value="both"><?php esc_html_e( 'Customer and Admin', 'wc_warranty' ); ?></option>
					</select>
					<div class="search-container">
						<select
							class="admin-recipients email-search-select"
							name="admin_recipients[0][]"
							multiple="multiple"
							placeholder="<?php echo esc_attr( $admin_email ); ?>"
							style="width: 400px">
						</select>
					</div>
				</p>

				<p>
					<label for="subject_0"><?php esc_html_e( 'Subject', 'wc_warranty' ); ?></label>
					<br/>
					<input type="text" name="subject[0]" id="subject_0" value="" class="" style="width:100%;" />
				</p>
			</td>
			<td>
				<textarea name="message[0]" rows="5" style="width: 99%;"></textarea>
			</td>
			<td></td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
<div style="display:none;">
	<table id="email-row-template"><tbody>
		<tr id="email__id_">
			<td>
				<p>
					<label for="trigger__id_"><?php esc_html_e( 'Trigger', 'wc_warranty' ); ?></label>
					<br/>
					<select name="trigger[_id_]" class="trigger">
						<option value="status"><?php esc_html_e( 'Status change', 'wc_warranty' ); ?></option>
						<option value="request_tracking"><?php esc_html_e( 'Request Tracking', 'wc_warranty' ); ?></option>
						<option value="item_refunded"><?php esc_html_e( 'Item Refunded', 'wc_warranty' ); ?></option>
						<option value="coupon_sent"><?php esc_html_e( 'Coupon Sent', 'wc_warranty' ); ?></option>
					</select>
				</p>
				<div class="trigger_status">
					<p>
						<label for="from_status__id_"><?php esc_html_e( 'From', 'wc_warranty' ); ?></label>
						<br/>
						<select name="from_status[_id_]" id="from_status__id">
							<option value="any"><?php esc_html_e( 'Any status', 'wc_warranty' ); ?></option>
							<?php foreach ( $all_statuses as $warranty_status ) : ?>
								<option value="<?php echo esc_attr( $warranty_status->slug ); ?>"><?php echo esc_html( $warranty_status->name ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>

					<p>
						<label for="to_status__id_"><?php esc_html_e( 'To', 'wc_warranty' ); ?></label>
						<br/>
						<select name="status[_id_]" id="to_status__id_">
							<?php foreach ( $all_statuses as $warranty_status ) : ?>
								<option value="<?php echo esc_attr( $warranty_status->slug ); ?>"><?php echo esc_html( $warranty_status->name ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
				</div>
			</td>
			<td>
				<p>
					<label for="recipient__id_"><?php esc_html_e( 'Recipient', 'wc_warranty' ); ?></label>
					<br/>
					<select name="send_to[_id_]" id="recipient__id_">
						<option value="customer"><?php esc_html_e( 'Customer', 'wc_warranty' ); ?></option>
						<option value="admin"><?php esc_html_e( 'Admin', 'wc_warranty' ); ?></option>
						<option value="both"><?php esc_html_e( 'Customer and Admin', 'wc_warranty' ); ?></option>
					</select>
					<div class="search-container">
						<select
							class="admin-recipients email-search-select_noenhance_"
							name="admin_recipients[_id_][]"
							multiple="multiple"
							placeholder="<?php echo esc_attr( $admin_email ); ?>"
							style="width: 400px">
						</select>
					</div>
				</p>

				<p>
					<label for="subject__id_"><?php esc_html_e( 'Subject', 'wc_warranty' ); ?></label>
					<br/>
					<input type="text" name="subject[_id_]" id="subject__id_" value="" class="" style="width:100%;" />
				</p>
			</td>
			<td>
				<textarea name="message[_id_]" rows="5" style="width: 99%;"></textarea>
			</td>
			<td><a class="button delete-row" href="#">&times;</a></td>
		</tr>
		</tbody></table>
</div>
<script type="text/javascript">
	<?php
	$js_statuses = array();
	foreach ( $all_statuses as $warranty_status ) {
		if ( ! isset( $warranty_status->slug ) || empty( $warranty_status->slug ) ) {
			$warranty_status->slug = $warranty_status->name;
		}
		$js_statuses[] = array(
			'slug' => $warranty_status->slug,
			'name' => $warranty_status->name,
		);
	}
	?>
	var statuses = JSON.parse( decodeURIComponent( '<?php echo rawurlencode( wp_json_encode( $js_statuses ) ); ?>' ) );
	jQuery( document ).ready( function( $ ) {
		$( '.add-email' ).click( function( e ) {
			e.preventDefault();

			var idx = 1;

			while ( $( '#email_' + idx ).length > 0 ) {
				idx ++;
			}

			var src = $( '#email-row-template tbody' ).html();
			src = src.replace( /_id_/g, idx );
			// Need to replace noenhance with empty string, otherwise Select2 will be initialized for the template.
			src = src.replace( /_noenhance_/g, '' );

			$( '#emails_tbody' ).append( src );
			$( 'body' ).trigger( 'wc-enhanced-select-init' );
		} );

		$( '.delete-row' ).on( 'click', function( e ) {
			e.preventDefault();

			$( this ).parents( 'tr' ).remove();
		} );

		$( '#emails_tbody' ).on( 'change', '.trigger', function() {
			var tr = $( this ).closest( 'tr' );

			if ( 'status' === $( this ).val() ) {
				$( tr ).find( '.trigger_status' ).show();
			} else {
				$( tr ).find( '.trigger_status' ).hide();
			}
		} );
		$( '.trigger' ).change();
	} );
</script>
