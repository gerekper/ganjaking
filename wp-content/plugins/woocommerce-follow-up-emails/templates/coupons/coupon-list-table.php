<?php
$coupons = FUE_Coupons::get_coupons();
?>
<div class="wrap woocommerce">
	<div class="icon32"><img src="<?php echo esc_url( FUE_TEMPLATES_URL ) .'/images/send_mail.png'; ?>" /></div>
	<h2>
		<?php esc_html_e('Follow-Up Emails &raquo; Email Coupons', 'follow_up_emails'); ?>
		<a href="admin.php?page=followup-emails-coupons&action=new-coupon" class="add-new-h2"><?php esc_html_e('Add Coupon', 'wc_followup_emalis'); ?></a>
	</h2>

	<p><?php esc_html_e('Add customized and automated coupons to your follow-ups. You must create the coupon in this menu first, then select to include it in your Follow-up. The coupon code generation feature used here allows coupons to be created that are are unique for each user.', 'follow_up_emails'); ?></p>

	<?php include 'notifications.php'; ?>

	<div class="subsubsub_section">
		<ul class="subsubsub">
			<li>
				<a href="#coupons" class="current"><?php esc_html_e('Coupons', 'follow_up_emails'); ?></a> |
				<a href="#usage" class=""><?php esc_html_e('Reports', 'follow_up_emails'); ?></a>
			</li>
		</ul>
		<br class="clear">

		<div class="section" id="coupons">
			<form action="admin-post.php" method="post">
				<table class="widefat fixed striped posts">
					<thead>
					<tr>
						<th scope="col" id="name" class="manage-column column-name" style=""><?php esc_html_e('Name', 'follow_up_emails'); ?></th>
						<th scope="col" id="type" class="manage-column column-type" style=""><?php esc_html_e('Type', 'follow_up_emails'); ?></th>
						<th scope="col" id="amount" class="manage-column column-amount" style=""><?php esc_html_e('Amount', 'follow_up_emails'); ?></th>
						<th scope="col" id="usage_count" class="manage-column column-usage_count" style=""><?php esc_html_e('Sent', 'follow_up_emails'); ?></th>
					</tr>
					</thead>
					<tbody id="the_list">
					<?php
					if (empty($coupons)):
						?>
						<tr scope="row">
							<th colspan="4"><?php esc_html_e('No coupons available', 'follow_up_emails'); ?></th>
						</tr>
					<?php
					else:
						foreach ($coupons as $coupon):
							?>
							<tr scope="row">
								<td class="post-title column-title">
									<strong><a class="row-title" href="admin.php?page=followup-emails-coupons&action=edit-coupon&id=<?php echo esc_attr( $coupon->id ); ?>"><?php echo esc_html( wp_unslash($coupon->coupon_name) ); ?></a></strong>
									<div class="row-actions">
										<span class="edit"><a href="admin.php?page=followup-emails-coupons&action=edit-coupon&id=<?php echo esc_attr( $coupon->id ); ?>"><?php esc_html_e('Edit', 'follow_up_emails'); ?></a></span>
										|
										<span class="trash"><a onclick="return confirm('<?php esc_html_e('Really delete this entry?', 'follow_up_emails'); ?>');" href="<?php echo wp_nonce_url('admin-post.php?action=fue_delete_coupon&id=' . $coupon->id, 'fue-delete-coupon'); ?>"><?php esc_html_e('Delete', 'follow_up_emails'); ?></a></span>
									</div>
								</td>
								<td><?php echo esc_html( FUE_Coupons::get_discount_type($coupon->coupon_type) ); ?></td>
								<td><?php echo esc_html( floatval($coupon->amount) ); ?></td>
								<td><?php echo esc_html( $coupon->usage_count ); ?></td>
							</tr>
						<?php
						endforeach;
					endif;
					?>
					</tbody>
				</table>
			</form>
		</div>

		<div class="section" id="usage">
			<?php
			// coupons sorting
			$sort['sortby'] = 'date_sent';
			$sort['sort']   = 'desc';

			if ( isset($_GET['sortby']) && !empty($_GET['sortby']) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$valid = array('date_sent', 'email_address', 'coupon_used');
			$_sortby = isset( $_GET['sortby'] ) ? sanitize_text_field( wp_unslash( $_GET['sortby'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			if ( in_array($_sortby, $valid) ) {
				$_sort = isset( $_GET['sort'] ) ? sanitize_text_field( wp_unslash( $_GET['sort'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
				$sort['sortby'] = $_sortby;
				$sort['sort']   = ($_sort == 'asc') ? 'asc' : 'desc';
			}
			}

			$coupon_reports = FUE_Reports::get_reports(array('type' => 'coupons', 'sort' => $sort));

			$email_address_class    = ($sort['sortby'] != 'email_address') ? 'sortable' : 'sorted';
			$email_address_sort     = ($email_address_class == 'sorted') ? $sort['sort'] : 'asc';
			$email_address_dir      = ($email_address_sort == 'asc') ? 'desc' : 'asc';

			$used_class     = ($sort['sortby'] != 'coupon_used') ? 'sortable' : 'sorted';
			$used_sort      = ($used_class == 'sorted') ? $sort['sort'] : 'asc';
			$used_dir       = ($used_sort == 'asc') ? 'desc' : 'asc';

			$sent_class     = ($sort['sortby'] != 'date_sent') ? 'sortable' : 'sorted';
			$sent_sort      = ($sent_class == 'sorted') ? $sort['sort'] : 'asc';
			$sent_dir       = ($sent_sort == 'asc') ? 'desc' : 'asc';

			?>
			<form action="admin-post.php" method="post">
				<table class="widefat fixed striped posts">
					<thead>
					<tr>
						<td scope="col" id="cb" class="manage-column column-cb check-column">
							<label class="screen-reader-text" for="cb-select-all-coupons"><?php esc_html_e( 'Select All', 'follow_up_emails' ); ?></label>
							<input id="cb-select-all-coupons" type="checkbox">
						</td>
						<th scope="col" id="coupon_name" class="manage-column column-type" style=""><?php esc_html_e('Coupon Name', 'follow_up_emails'); ?></th>
						<th scope="col" id="email_address" class="manage-column column-usage_count <?php echo esc_attr( $email_address_class .' '. $email_address_sort ); ?>" style="">
							<a href="admin.php?page=followup-emails-coupons&tab=reports&sortby=email_address&sort=<?php echo esc_attr( $email_address_dir ); ?>&v=coupons#usage">
								<span><?php esc_html_e('Email Address', 'follow_up_emails'); ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
						<th scope="col" id="coupon_code" class="manage-column column-usage_count" style=""><?php esc_html_e('Coupon Code', 'follow_up_emails'); ?> <img class="help_tip" width="16" height="16" title="<?php esc_attr_e('This is the unique coupon code generated by the follow-up email for this specific email address', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" /></th>
						<th scope="col" id="email_name" class="manage-column column-usage_count" style=""><?php esc_html_e('Email Name', 'follow_up_emails'); ?> <img class="help_tip" width="16" height="16" title="<?php esc_attr_e('This is the name of the follow-up email that generated the coupon that was sent to this specific email address', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" /></th>
						<th scope="col" id="used" class="manage-column column-used <?php echo esc_attr( $used_class .' '. $used_sort ); ?>" style="">
							<a href="admin.php?page=followup-emails-coupons&tab=reports&sortby=coupon_used&sort=<?php echo esc_attr( $used_dir ); ?>&v=coupons#usage">
								<span><?php esc_html_e('Used', 'follow_up_emails'); ?>  <img class="help_tip" width="16" height="16" title="<?php esc_attr_e('This tells you if this specific coupon code generated and sent via follow-up emails has been used, and if it has, it includes the date and time', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" /></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
						<th scope="col" id="date_sent" class="manage-column column-date_sent <?php echo esc_attr( $sent_class .' '. $sent_sort ); ?>" style="">
							<a href="admin.php?page=followup-emails-coupons&tab=reports&sortby=date_sent&sort=<?php echo esc_attr( $sent_dir ); ?>&v=coupons#usage">
								<span><?php esc_html_e('Date Sent', 'follow_up_emails'); ?> <img class="help_tip" width="16" height="16" title="<?php esc_attr_e('This is the date and time that this specific coupon code was sent to this email address', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" /></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
					</tr>
					</thead>
					<tbody id="the_list">
					<?php
					if (empty($coupon_reports)) {
						echo '
						<tr scope="row">
							<th colspan="7">'. esc_html__('No reports available', 'follow_up_emails') .'</th>
						</tr>';
					} else {
						foreach ($coupon_reports as $report) {
							$used = __('No', 'follow_up_emails');

							if ( $report->coupon_used == 1 ) {
								$date = date( get_option('date_format') .' '. get_option('time_format') , strtotime($report->date_used));
								$used = sprintf(__('Yes (%s)', 'follow_up_emails'), $date);
							}

							echo '
							<tr scope="row">
								<th scope="row" class="check-column">
									<input id="cb-select-'. esc_attr( $report->id ) .'" type="checkbox" name="coupon_id[]" value="'. esc_attr( $report->id ) .'">
									<div class="locked-indicator"></div>
								</th>
								<td class="post-title column-title">
									<strong>'. esc_html( wp_unslash($report->coupon_name)).'</strong>
								</td>
								<td>'. esc_html($report->email_address) .'</td>
								<td>'. esc_html($report->coupon_code) .'</td>
								<td>'. esc_html($report->email_name) .'</td>
								<td>'. esc_html( $used ) .'</td>
								<td>'. esc_html( date( get_option('date_format') .' '. get_option('time_format') , strtotime($report->date_sent))) .'</td>
							</tr>
							';
						}
					}
					?>
					</tbody>
				</table>
				<div class="tablenav bottom">
					<div class="alignleft actions bulkactions">
						<input type="hidden" name="action" value="fue_reset_reports" />
						<input type="hidden" name="type" value="coupons" />
						<?php wp_nonce_field( 'fue-reset-reports') ?>
						<select name="coupons_action">
							<option value="-1" selected="selected"><?php esc_html_e('Bulk Actions', 'wordpress'); ?></option>
							<option value="trash"><?php esc_html_e('Delete Selected', 'follow_up_emails'); ?></option>
						</select>
						<input type="submit" name="" id="doaction-coupon" class="button action" value="Apply">
					</div>
				</div>
			</form>

		</div>
	</div>

</div>
<script>
	jQuery(document).ready(function($) {
		$("div.section").slice(1).hide();

		// Subsubsub tabs
		jQuery('div.subsubsub_section ul.subsubsub li a').first().addClass('current');
		jQuery('div.subsubsub_section .section').slice(1).hide();

		jQuery( 'div.subsubsub_section ul.subsubsub li a' ).on( 'click', function() {
			var $clicked = jQuery(this);
			var $section = $clicked.closest('.subsubsub_section');
			var $target  = $clicked.attr('href');

			$section.find('a').removeClass('current');

			if ($section.find('.section:visible').length) {
				$section.find('.section:visible').fadeOut( 100, function() {
					$section.find( $target ).fadeIn('fast');
				});
			} else {
				$section.find( $target ).fadeIn('fast');
			}

			$clicked.addClass('current');
			jQuery('#last_tab').val( $target );

			return false;
		} );

		if ( '#usage' === window.location.hash ) {
			jQuery( 'a[href="#usage"]' ).trigger( 'click' );
		}
	});
</script>
