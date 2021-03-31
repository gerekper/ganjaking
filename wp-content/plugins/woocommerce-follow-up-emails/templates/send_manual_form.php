<?php
$defaults = apply_filters( 'fue_manual_email_defaults', array(
	'type'              => $email->type,
	'always_send'       => $email->always_send,
	'name'              => $email->name,
	'interval'          => $email->interval_num,
	'interval_duration' => $email->interval_duration,
	'interval_type'     => $email->interval_type,
	'send_date'         => $email->send_date,
	'send_date_hour'    => $email->send_date_hour,
	'send_date_minute'  => $email->send_date_minute,
	'product_id'        => $email->product_id,
	'category_id'       => $email->category_id,
	'subject'           => $email->subject,
	'message'           => $email->message,
	'tracking_on'       => (!empty($email->tracking_code)) ? 1 : 0,
	'tracking'          => $email->tracking_code
), $email);

// if type is date, switch columns
if ( $defaults['interval_type'] == 'date' ) {
	$defaults['interval_type'] = $defaults['interval_duration'];
	$defaults['interval_duration'] = 'date';
}

if ( isset($_POST) && !empty($_POST) ) { // phpcs:ignore WordPress.Security.NonceVerification
	$defaults = array_merge( $defaults, $_POST ); // phpcs:ignore WordPress.Security.NonceVerification
}
?>
<div class="wrap email-form">
	<div class="icon32"><img src="<?php echo esc_url( FUE_TEMPLATES_URL ) .'/images/send_mail.png'; ?>" /></div>
	<h2><?php esc_html_e('Follow-Up Emails &raquo; Send Single Email', 'follow_up_emails'); ?></h2>

	<form action="admin-post.php" method="post" id="frm">
		<h3><?php echo esc_html( sprintf(__('Send Email: %s', 'follow_up_emails'), $email->name) ); ?></h3>

		<table class="form-table">
			<tbody>
				<tr valign="top" class="send_type_tr">
					<th scope="row" class="send_type_th">
						<label for="send_type"><?php esc_html_e('Send Email To', 'follow_up_emails'); ?></label>
					</th>
					<td class="send_type_td">
						<select name="send_type" id="send_type" style="min-width: 25em;">
							<option value="email"><?php esc_html_e('This email address', 'follow_up_emails'); ?></option>
							<option value="subscribers"><?php esc_html_e('Subscribers and Lists', 'follow_up_emails'); ?></option>
							<option value="roles"><?php esc_html_e('User Role', 'follow_up_emails'); ?></option>
							<?php do_action( 'fue_manual_types', $email ); ?>
						</select>

						<div class="send-type-email send-type-div">
							<input type="text" name="recipient_email" id="recipients" class="email-recipients" placeholder="someone@example.com" style="width: 600px;" />
						</div>

						<div class="send-type-subscribers send-type-div">
							<select name="email_list" id="email_list" style="min-width: 25em;">
								<option value=""><?php esc_html_e('All Subscribers', 'follow_up_emails'); ?></option>
								<?php foreach ( fue_get_subscription_lists() as $list ): ?>
								<option value="<?php echo esc_attr( $list['list_name'] ); ?>"><?php echo wp_kses_post( $list['list_name'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="send-type-roles send-type-div">
							<select name="roles[]" multiple id="roles" class="select2" data-placeholder="<?php esc_attr_e('Select the Roles to send to', 'follow_up_emails'); ?>" style="width: 400px;">
								<?php wp_dropdown_roles(); ?>
							</select>
						</div>

						<?php do_action( 'fue_manual_type_actions', $email ); ?>
					</td>
				</tr>

				<tr valign="top" class="sending_schedule">
					<th scope="row">
						<label for="schedule_email"><?php esc_html_e('Send at a specific time', 'follow_up_emails'); ?></label>
					</th>
					<td>
						<input type="checkbox" name="schedule_email" id="schedule_email" value="1" />
					</td>
				</tr>

				<tr valign="top" class="sending_schedule_picker">
					<th>&nbsp;</th>
					<td>
						<input type="text" class="date" size="10" name="sending_schedule_date" value="" />

						<input type="number" min="1" max="12" step="1" name="sending_schedule_hour" value="" placeholder="<?php esc_attr_e('Hour', 'follow_up_emails'); ?>" style="width:80px;" />
						<input type="number" min="0" max="59" step="1" name="sending_schedule_minute" value="" placeholder="<?php esc_attr_e('Minute', 'follow_up_emails'); ?>" style="width:80px;" />
						<select name="sending_schedule_ampm">
							<option value="am">AM</option>
							<option value="pm">PM</option>
						</select>

					</td>
				</tr>

				<tr valign="top" class="send_again_tr">
					<th scope="row">
						<label for="send_again"><?php esc_html_e('Send again', 'follow_up_emails'); ?></label>
					</th>
					<td>
						<input type="checkbox" name="send_again" id="send_again" value="1" />
					</td>
				</tr>

				<tr valign="top" class="class_send_again send_again_interval_tr">
					<th scope="row" class="interval_th">
						<label for="interval_type"><?php esc_html_e('Send again in:', 'follow_up_emails'); ?></label>
					</th>
					<td class="interval_td">
						<span class="hide-if-date interval_span hideable">
							<input type="text" name="interval" id="interval" value="<?php echo esc_attr($defaults['interval']); ?>" size="2" placeholder="0" />
						</span>
						<select name="interval_duration" id="interval_duration" class="interval_duration hideable">
							<?php
							/* @var FUE_Email $email */
							$durations = Follow_Up_Emails::get_durations();

							foreach ( $durations as $key => $value ):
								if ( $key == 'date') continue;
							?>
							<option class="interval_duration_<?php echo esc_attr( $key ); ?> hideable" value="<?php echo esc_attr($key); ?>"><?php echo  esc_html( Follow_Up_Emails::get_duration( $key, $value ) ); ?></option>
							<?php endforeach; ?>
						</select>

					</td>
				</tr>

				<?php do_action( 'fue_manual_email_form_before_message', $defaults ); ?>

				<tr valign="top">
					<th scope="row">
						<label for="subject"><?php esc_html_e('Email Subject', 'follow_up_emails'); ?></label>
					</th>
					<td>
						<input type="text" name="email_subject" id="email_subject" value="<?php echo esc_attr($defaults['subject']); ?>" class="regular-text" />
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<label for="message"><?php esc_html_e('Email Body', 'follow_up_emails'); ?></label>
						<br />
						<span class="description">
							<?php esc_html_e('You may use the following variables in the Email Subject and Body', 'follow_up_emails'); ?>
							<ul>
								<?php do_action('fue_email_manual_variables_list', $email); ?>
								<li class="var hideable var_web_version_url"><strong>{webversion_url}</strong> <img class="help_tip" title="<?php esc_attr_e('The URL to the web version of the email.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
								<li class="var hideable var_web_version_link"><strong>{webversion_link}</strong> <img class="help_tip" title="<?php esc_attr_e('Renders a <em>View in browser</em> link that points to the web version of the email.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
								<li class="var hideable var_customer_username"><strong>{customer_username}</strong> <img class="help_tip" title="<?php esc_attr_e('The first name of the customer who purchased from your store.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
								<li class="var hideable var_customer_first_name"><strong>{customer_first_name}</strong> <img class="help_tip" title="<?php esc_attr_e('The first name of the customer who purchased from your store.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
								<li class="var hideable var_customer_name"><strong>{customer_name}</strong> <img class="help_tip" title="<?php esc_attr_e('The full name of the customer who purchased from your store.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
								<li class="var hideable var_customer_email"><strong>{customer_email}</strong> <img class="help_tip" title="<?php esc_attr_e('The email address of the customer who purchased from your store.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
								<li class="var hideable var_store_url"><strong>{store_url}</strong> <img class="help_tip" title="<?php esc_attr_e('The URL/Address of your store.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
								<li class="var hideable var_store_url_secure"><strong>{store_url_secure}</strong> <img class="help_tip" title="<?php esc_attr_e('the secure url/address of your store (https).', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
								<li class="var hideable var_store_url_path"><strong>{store_url=path}</strong> <img class="help_tip" title="<?php esc_attr_e('The URL/Address of your store with path added at the end. Ex. {store_url=/categories}', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
								<li class="var hideable var_store_name"><strong>{store_name}</strong> <img class="help_tip" title="<?php esc_attr_e('The name of your store.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
								<li class="var hideable var_unsubscribe_url"><strong>{unsubscribe_url}</strong> <img class="help_tip" title="<?php esc_attr_e('URL where users will be able to opt-out of the email list.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
								<li class="var hideable var_post_id"><strong>{post_id=xx}</strong> <img class="help_tip" title="<?php esc_attr_e('Include the excerpt of the specified Post ID.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
							</ul>
						</span>
					</th>
					<td>
						<?php
						$settings = array(
							'textarea_rows' => 20,
							'teeny'         => false
						);
						wp_editor($defaults['message'], 'email_message', $settings); ?>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="tracking_on"><?php esc_html_e('Add Google Analytics tracking to links', 'follow_up_emails'); ?></label>
					</th>
					<td>
						<input type="checkbox" name="tracking_on" id="tracking_on" value="1" <?php if ($defaults['tracking_on'] == 1) echo 'checked'; ?> />
					</td>
				</tr>
				<tr class="tracking_on">
					<th scope="row">
						<label for="tracking"><?php esc_html_e('Link Tracking', 'follow_up_emails'); ?></label>
					</th>
					<td>
						<input type="text" name="tracking" id="tracking" class="test-email-field" value="<?php echo esc_attr($defaults['tracking']); ?>" placeholder="e.g. utm_campaign=Follow-up-Emails-by-WooCommerce" size="40" />
						<p class="description">
							<?php esc_html_e('The value inserted here will be appended to all URLs in the Email Body', 'follow_up_emails'); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="test_email"><strong>Send a test email</strong></label>
					</th>
					<td>
						<input type="hidden" id="email_type" value="manual" class="test-email-field" />
						<input type="text" id="email" placeholder="Email Address" value="" class="test-email-field" />
						<input type="button" id="test_send" value="<?php esc_attr_e('Send Test', 'follow_up_emails'); ?>" class="button" />
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<?php wp_nonce_field( 'fue-send-manual' ); ?>
			<input type="hidden" name="action" value="fue_followup_send_manual" />
			<input type="hidden" name="id" id="id" value="<?php echo isset( $_GET['id'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['id'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification ?>" />
			<input type="submit" name="save" id="save" value="<?php esc_attr_e('Send Email Now', 'follow_up_emails'); ?>" class="button-primary" />
		</p>
	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$( '#frm' ).on( 'submit', function() {
		$( '#save' ).prop( 'disabled', true );
	} );

	jQuery(".send-type-div").hide();

	jQuery( '#send_type' ).on( 'change', function() {
		jQuery(".send-type-div").hide();
		switch (jQuery(this).val()) {

			case "email":
				jQuery(".send-type-email").show();
				break;

			case "subscribers":
				$(".send-type-subscribers").show();
				break;

			case "roles":
				$(".send-type-roles").show();
				break;

			default:
				break;

		}
	} ).trigger( 'change' );

	jQuery( '#tracking_on').on( 'change', function() {
		if (jQuery(this).prop("checked")) {
			jQuery(".tracking_on").show();
		} else {
			jQuery(".tracking_on").hide();
		}
	} ).trigger( 'change' );

	jQuery( '#interval_type' ).on( 'change', function() {
		if (jQuery(this).val() != "cart") {
			jQuery(".not-cart").show();
		} else {
			jQuery(".not-cart").hide();
		}
	} ).trigger( 'change' );

	jQuery( '#interval_duration' ).on( 'change', function() {
		if (jQuery(this).val() == "date") {
			jQuery(".hide-if-date").hide();
			jQuery(".show-if-date").show();
		} else {
			jQuery(".hide-if-date").show();
			jQuery(".show-if-date").hide();
		}

		jQuery( '#email_type').trigger( 'change' );
	} ).trigger( 'change' );

	jQuery( '.date, #timeframe_to' ).datepicker( {
		dateFormat: 'yy-mm-dd',
	} );

	jQuery( '#timeframe_from' ).datepicker( {
		dateFormat: 'yy-mm-dd',
		onClose: function( selectedDate ) {
			$( '#timeframe_to' ).datepicker( 'option', 'minDate', selectedDate );
		}
	} );

	<?php do_action('fue_manual_email_form_script'); ?>

	jQuery( '#send_again').on( 'change', function() {
		if (jQuery(this).prop("checked")) {
			jQuery(".class_send_again").show();
		} else {
			jQuery(".class_send_again").hide();
		}
	} ).trigger( 'change' );

	jQuery( '#schedule_email' ).on( 'change', function() {
		if (jQuery(this).prop("checked")) {
			jQuery(".sending_schedule_picker").show();
		} else {
			jQuery(".sending_schedule_picker").hide();
		}
	} ).trigger( 'change' );

	// Test Email
	jQuery( 'div.email-form' ).on( 'click', '#test_send', function() {
		var $btn    = jQuery(this);
		var old_val = $btn.val();

		if (jQuery("#wp-email_message-wrap").hasClass("tmce-active")){
			var message = tinyMCE.activeEditor.getContent();
		}else{
			var message = jQuery('#email_message').val();
		}

		$btn
			.val("Please wait...")
			.prop( 'disabled', true );

		var data = {
			'action' : 'fue_send_test_email',
			'id'     : jQuery("#id").val(),
			'subject': jQuery("#email_subject").val(),
			'message': message,
			'nonce'  : jQuery( '#_wpnonce' ).val()
		};

		jQuery(".test-email-field").each(function() {
			var field = jQuery(this).data("key") || jQuery(this).attr("id");
			data[field] = jQuery(this).val();
		});

		jQuery.post(ajaxurl, data, function(resp) {
			if (resp == "OK")
				alert("Email sent!");
			else
				alert(resp);

			$btn
				.val(old_val)
				.prop( 'disabled', false );
		});
	} );

	jQuery(".tips, .help_tip").tipTip({
		'attribute' : 'title',
		'fadeIn' : 50,
		'fadeOut' : 50,
		'delay' : 200
	});

	<?php do_action( 'fue_manual_js' ); ?>

});
</script>
