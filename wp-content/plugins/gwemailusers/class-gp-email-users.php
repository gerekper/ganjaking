<?php

class GP_Email_Users extends GWPerk {

	public $version = GP_EMAIL_USERS_VERSION;

	public static $version_info;
	public static $email_errors;
	public static $current_emails = array(); // Used to prevent duplicates from being sent if 'gpeu_send_to_duplicates' filter is returned false.

	private $_checking_user_cap;

	const CAP = 'gravityperks_gwemailusers';

	function init() {

		load_plugin_textdomain( 'gp-email-users', false, basename( dirname( __file__ ) ) . '/languages/' );

		add_filter( 'user_has_cap',             array( $this, 'user_has_cap' ), 10, 4 );
		add_filter( 'members_get_capabilities', array( $this, 'members_get_capabilities' ) );

		add_filter( 'gform_addon_navigation', array( $this, 'create_menu' ) );

		// AJAX
		add_action('wp_ajax_gweu_get_form_data', array( $this, 'ajax_get_form_data' ) );
		add_action('wp_ajax_gweu_send_email',    array( $this, 'ajax_send_email' ) );

		$this->enqueue_script( array(
			'handle' => 'gwp-common',
			'pages'  => 'gwp_email_users'
		) );

	}

	public function create_menu( $menus ) {

		$menus[] = array(
			'name'       => 'gwp_email_users',
			'label'      => __( 'Email Users', 'gp-email-users' ),
			'callback'   => array( $this, 'email_users_page' ),
			'permission' => GP_Email_Users::CAP
		);

		return $menus;
	}

	public function email_users_page() {

		require_once(GFCommon::get_base_path() . '/notification.php');
		add_action('media_buttons', array('GFNotification', 'media_buttons'), 40);

		$form_id = 0;
		$email_options = array();
		self::$email_errors = array();

		if(rgpost('is_submit')) {

			$form_id = $email_options['form_id'] = rgpost('form_id');
			$form = RGFormsModel::get_form_meta($form_id);

			$email_options['to'] = rgpost('email_to');
			$email_options['from'] = rgpost('email_from');
			$email_options['from_name'] = rgpost('email_from_name');
			$email_options['reply_to'] = rgpost('email_reply_to');
			$email_options['bcc'] = rgpost('email_bcc');
			$email_options['subject'] = rgpost('email_subject');
			$email_options['message'] = rgpost('email_message');

			// check for empty required fields
			foreach(array('to', 'subject', 'message') as $key) {
				if(!rgar($email_options, $key))
					self::$email_errors[$key] = __('This field is required.', 'gp-email-users' );
			}

			// check for valid emails
			foreach(array('reply_to', 'from') as $key) {
				$email = rgar($email_options, $key);
				if($email && !GFCommon::is_valid_email($email))
					self::$email_errors[$key] = __('Please enter valid emails.', 'gp-email-users');
			}

		}

		$form = $form_id ? RGFormsModel::get_form_meta($form_id) : false;

		?>

		<style type="text/css">
			.wrap h2 { margin: 0 0 18px; }
			#settings { margin: 18px 0; }
			#settings input.button { margin-left: 130px; }

			.page-content label { display: block; font-weight: bold; width: 120px; float: left; line-height: 26px; }
			.page-content textarea { display: block; width: 75%; height: 200px; }
			.page-content option { padding-right: 10px; }

			.setting { margin: 0 0 10px; border-bottom: 1px dotted #eee; padding: 0 10px 10px; }
			.setting.form-setting { border: 1px solid #eee; border-width: 1px 0; background-color: #f7f7f7; padding: 10px; }
			.setting input { width: 240px; }
			.setting.email_message { border-bottom: 0; }
			.setting select { min-width: 240px !important; }
			.setting .validation_message { color: #900; font-style: italic; margin-left: 120px; }

			#email_message_variable_select { display: block; margin: 0 0 2px; }
			#email_message { margin-left: 120px; }

			.sending { }
		</style>

		<div class="wrap">

			<h2><?php _e('Email Users', 'gp-email-users'); ?></h2>

			<?php if(rgget('debug')): ?>
				<div class="error"><p>
						<?php _e('You\'re in debug mode. This allows you to test sending emails without actually sending them.', 'gp-email-users'); ?>
					</p></div>
			<?php endif; ?>

			<?php if(rgpost('is_submit') && empty(self::$email_errors)) {
				$emails = self::prepare_emails($email_options);
				?>

				<div class="updated sending"><p>
						<span class="count"><span class="current">0</span> / <?php echo count($emails); ?> sent.</span>
					</p></div>

				<script type="text/javascript">

					function sendEmails() {

						var emails = <?php echo json_encode($emails); ?>;
						var count = 0;

						var spinner = new gperk.ajaxSpinner('.sending .count', '<?php echo $this->get_base_url(); ?>/images/ajax-loader.gif');

						for(i in emails) {
							jQuery.post(ajaxurl, {
								email: emails[i],
								debug: '<?php echo rgget('debug'); ?>',
								action: 'gweu_send_email'
							}, function(response){
								if(response) {
									count++;
									jQuery('.sending .current').text(count);
									if(emails.length == count) {
										spinner.destroy();
										jQuery('.sending p').html('<strong>All done!</strong>');
									}
								}
							});
						}

					}

					sendEmails();

				</script>
				<?php
				return;
			} ?>

			<form method="post" id="email_form" name="form_email">

				<input type="hidden" name="is_submit" value="1" />

				<div class="page-content">

					<div class="setting form-setting">

						<label for="form_id">Form</label>

						<select name="form_id" id="form_id" onchange="gweuSelectForm(this);">
							<option value="">Select a Form</option>
							<?php
							/**
							 * Filter the forms available on the Email Users form.
							 *
							 * @since 1.3.6
							 *
							 * @param $forms array An array of form meta.
							 */
							$forms = apply_filters( 'gpeu_forms', RGFormsModel::get_forms() );
							foreach($forms as $form_obj):
								$selected = $form_obj->id == $form_id ? 'selected="selected"' : '';
								?>

								<option value="<?php echo $form_obj->id; ?>" <?php echo $selected; ?>><?php echo $form_obj->title; ?></option>

							<?php endforeach; ?>
						</select>

					</div>

					<div id="notice" class="" style="display:none;"></div>

					<div id="settings" <?php self::echo_if('style="display:none;"', $form_id, false); ?>>

						<div class="email_to setting">
							<?php if($form_id) {
								echo self::get_email_select_ui($form, 'email_to', 'to', __('Email To', 'gp-email-users'), rgar($email_options, 'to'), true);
							} ?>
						</div> <!-- / to -->

						<div class="email_from setting">

							<label for="email_from">
								<?php _e('From Email', 'gp-email-users'); ?>
							</label>

							<input type="text" value="<?php echo rgar($email_options, 'from'); ?>" id="email_from" name="email_from" />

							<?php if(isset(self::$email_errors['from'])) { ?>
								<div class="validation_message"><?php _e('Please enter a valid email.', 'gp-email-users') ?></div>
							<?php } ?>

						</div>

						<div class="email_from_name setting">

							<label for="email_from_name">
								<?php _e('From Name', 'gp-email-users'); ?>
							</label>

							<input type="text" value="<?php echo rgar($email_options, 'from_name'); ?>" id="email_from_name" name="email_from_name" />

						</div>

						<div class="email_reply_to setting">

							<label for="email_reply_to">
								<?php _e('Reply To', 'gp-email-users'); ?>
							</label>

							<input type="text" value="<?php echo rgar($email_options, 'reply_to'); ?>" id="email_reply_to" name="email_reply_to" />

							<?php if(isset(self::$email_errors['reply_to'])) { ?>
								<div class="validation_message"><?php _e('This email is invalid.', 'gp-email-users') ?></div>
							<?php } ?>

						</div>

						<div class="email_subject setting">

							<label for="email_subject">
								<?php _e('Subject', 'gp-email-users'); ?> <span class="gfield_required">*</span>
							</label>

							<input type="text" value="<?php echo rgar($email_options, 'subject'); ?>" id="email_subject" name="email_subject" />

							<?php if(isset(self::$email_errors['subject'])) { ?>
								<div class="validation_message"><?php _e('Please enter a subject for the email.', 'gp-email-users') ?></div>
							<?php } ?>

						</div>

						<div class="email_bcc setting">

							<label for="email_bcc">
								<?php _e( 'BCC', 'gp-email-users' ); ?>
							</label>

							<input type="email" value="<?php echo rgar( $email_options, 'bcc' ); ?>" id="email_bcc" name="email_bcc" multiple>

						</div>

						<div class="email_message setting">
							<?php if($form_id) {
								echo self::get_email_message_ui($form, rgar($email_options, 'message'));
							} ?>
						</div>

						<input type="submit" value="Send Email" class="button button-primary" />

					</div>

				</div>

			</form>

		</div>

		<script type="text/javascript">

			function gweuSelectForm(elem) {

				var formId = jQuery(elem).val();

				if(!formId)
					return;

				jQuery.post(ajaxurl, {
					form_id: formId,
					action: 'gweu_get_form_data'
				}, function(response){

					var response = jQuery.parseJSON(response);

					if(typeof response['error'] != 'undefined') {
						toggleNotice('<p>' + response['error'] + '</p>');
						toggleSettings(false);
					} else {
						toggleNotice(false);
						toggleSettings(true, response);
					}

				});

			}

			function toggleNotice(message) {

				var notice = jQuery('#notice');

				if(message === false) {
					notice.slideUp();
				} else {
					notice.html(message).removeClass('updated error').addClass('error').slideDown();
				}

			}

			function toggleSettings(open, options) {

				var settings = jQuery('#settings');

				if(typeof open == 'undefined') {
					settings.slideToggle();
				} else if(open) {
					jQuery('.email_message').html(options['message_ui']);
					jQuery('.email_to').html(options['to_ui']);
					//jQuery('.email_bcc').html(options['bcc_ui']);
					settings.slideDown();
				} else {
					settings.slideUp();
				}

			}

			function InsertVariable(element_id, callback, variable){
				if(!variable)
					variable = jQuery('#' + element_id + '_variable_select').val();

				var messageElement = jQuery("#" + element_id);

				if(document.selection) {
					// Go the IE way
					messageElement[0].focus();
					document.selection.createRange().text=variable;
				}
				else if(messageElement[0].selectionStart) {
					// Go the Gecko way
					obj = messageElement[0]
					obj.value = obj.value.substr(0, obj.selectionStart) + variable + obj.value.substr(obj.selectionEnd, obj.value.length);
				}
				else {
					messageElement.val(variable + messageElement.val());
				}

				jQuery('#' + element_id + '_variable_select')[0].selectedIndex = 0;


				if(callback && window[callback]){
					window[callback].call(null, element_id, variable);
				}
			}

		</script>

		<?php
	}

	public function ajax_get_form_data() {

		$form_id = rgpost('form_id');
		$form = RGFormsModel::get_form_meta($form_id);

		if( count( (array) GFCommon::get_fields_by_type( $form, array( 'email' ) ) ) < 1 ) {
			echo json_encode( array( 'error' => sprintf( __( 'Oops! This form does not have any %sEmail%s fields.', 'gp-email-users' ), '<strong>', '</strong>' ) ) );
			die();
		}

		$to_ui = self::get_email_select_ui($form, 'email_to', 'to', __('Email To', 'gp-email-users'), false, true);
		$message_ui = self::get_email_message_ui($form);
		$bcc_ui = self::get_email_select_ui($form, 'email_bcc', 'bcc', __('BCC', 'gp-email-users'));

		echo json_encode(array('form' => $form, 'message_ui' => $message_ui, 'to_ui' => $to_ui, 'bcc_ui' => $bcc_ui));
		die();
	}

	public function get_email_select_ui($form, $id = '', $key = '', $label = '', $value = '', $required = false) {
		ob_start();
		$email_fields = GFCommon::get_fields_by_type($form, array('email'));
		?>

		<label for="<?php echo $id; ?>">
			<?php echo $label; ?> <?php if($required): ?><span class="gfield_required">*</span><?php endif; ?>
		</label>

		<select name="<?php echo $id; ?>" id="<?php echo $id; ?>">
			<option value=""><?php _e('Select an Email Field', 'gp-email-users'); ?></option>
			<?php foreach($email_fields as $email_field):
				$selected = $email_field['id'] == $value ? 'selected="selected"' : '';
				?>
				<option value="<?php echo $email_field['id']; ?>" <?php echo $selected; ?>><?php echo $email_field['label']; ?></option>
			<?php endforeach; ?>
		</select>

		<?php if(isset(self::$email_errors[$key])) { ?>
			<div class="validation_message"><?php _e('This is a required field.', 'gp-email-users') ?></div>
		<?php } ?>

		<?php
		return ob_get_clean();
	}

	public function get_email_message_ui($form, $value = false) {
		ob_start();
		?>

		<label for="email_message">
			<?php _e('Message', 'gp-email-users'); ?> <span class="gfield_required">*</span>
		</label>

		<?php GFCommon::insert_variables($form['fields'], 'email_message', false); ?>

		<textarea name="email_message" id="email_message"><?php echo $value; ?></textarea>

		<?php if(isset(self::$email_errors['message'])) { ?>
			<div class="validation_message"><?php _e('Please enter a message for the email.', 'gp-email-users') ?></div>
		<?php } ?>

		<?php
		return ob_get_clean();
	}

	public function prepare_emails( $email_options ) {

		$form = RGFormsModel::get_form_meta( rgar( $email_options, 'form_id' ) );

		if(!$form)
			return array();

		$leads = GFAPI::get_entries( $form['id'], array( 'status' => 'active' ), array(), array( 'offset' => 0, 'page_size' => 999 ) );
		$emails = array();

		$send_duplicates = apply_filters( 'gpeu_send_to_duplicates', true, $form, $leads, $email_options );

		foreach($leads as $lead) {

			$options = array();
			foreach($email_options as $key => $option) {
				if( in_array( $key, array( 'to' ) ) ) {
					$options[$key] = rgar($lead, rgar($email_options, $key));
				} else {
					$options[$key] = GFCommon::replace_variables($option, $form, $lead);
				}
			}

			/**
			 * @var $to
			 * @var $from
			 * @var $bcc
			 * @var $reply_to
			 */
			extract($options);

			if( ! $send_duplicates ) {
				if( in_array( $to, self::$current_emails ) ) {
					continue;
				} else {
					self::$current_emails[] = $to;
				}
			}

			$content_type = "text/html";
			$headers = '';

			if( $from ) {
				$name = empty($from_name) ? $from : $from_name;
				$headers = "From: \"$name\" <$from> \r\n";
			}

			$headers .= GFCommon::is_valid_email($reply_to) ? "Reply-To: $reply_to\r\n" :"";
			$headers .= GFCommon::is_valid_email($bcc) ? "Bcc: $bcc\r\n" :"";
			$headers .= "Content-type: {$content_type}; charset=" . get_option('blog_charset') . "\r\n";

			$emails[] = compact('to', 'subject', 'message', 'headers');

		}

		return $emails;
	}

	public function ajax_send_email() {

		$email = rgpost('email');
		/**
		 * @var $to
		 * @var $subject
		 * @var $message
		 * @var $headers
		 */
		extract($email);

		// if not in debug mode, actually send email
		if(rgpost('debug')) {
			$result = true;
		} else {
			$result = wp_mail($to, $subject, $message, $headers);
		}

		echo $result;
		die();
	}

	public function members_get_capabilities( $caps ) {
		return array_merge( $caps, array( GP_Email_Users::CAP ) );
	}

	public function user_has_cap( $all_caps, $cap, $args, $user ) {

		// Avoid infinite loop.
		if( $this->_checking_user_cap ) {
			return $all_caps;
		}

		$this->_checking_user_cap = true;

		$is_admin = current_user_can( 'administrator' ) || is_super_admin();

		// If Members is installed, give full access to administrators - unless - the cap is specifically denied.
		if( class_exists( 'Members_Role' ) && $is_admin )  {

			$is_denied = false;

			foreach( $user->roles as $role ) {
				$role = new Members_Role( $role );
				if( in_array( GP_Email_Users::CAP, $role->denied_caps ) ) {
					$is_denied = true;
					break;
				}
			}

			if( ! $is_denied ) {
				$all_caps[ GP_Email_Users::CAP ] = true;
			}

		}
		// Otherwise, give full access to administrators if the members plugin is not installed.
		else if( $is_admin ) {

			$all_caps[ GP_Email_Users::CAP ] = true;

		}

		$this->_checking_user_cap = false;

		return $all_caps;
	}

	public function documentation() {
		return array(
			'type'  => 'url',
			'value' => 'https://gravitywiz.com/documentation/gravity-forms-email-users/'
		);
	}

}

class GWEmailUsers extends GP_Email_Users { }