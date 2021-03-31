<?php
$completed                  = get_option('fue_sensei_course_completed_email', 0);
$completed_email_subject    = get_option('fue_sensei_course_completed_email_subject', '');
$completed_email_body       = get_option('fue_sensei_course_completed_email_body', '');

$incomplete                 = get_option('fue_sensei_course_incomplete_email', 0);
$incomplete_email_subject   = get_option('fue_sensei_course_incomplete_email_subject', '');
$incomplete_email_body      = get_option('fue_sensei_course_incomplete_email_body', '');
$deadline_number            = get_option('fue_sensei_course_incomplete_email_deadline_number', 1);
$deadline_period            = get_option('fue_sensei_course_incomplete_email_deadline_period', 'weeks');
?>
<h3><?php esc_html_e('Sensei Settings', 'follow_up_emails'); ?></h3>

<?php wp_nonce_field( 'fue-update-settings-verify' ); ?>

<table class="form-table">
	<tr>
		<th colspan="2">
			<label for="sensei_course_completed_email">
				<input type="checkbox" name="sensei_course_completed_email" id="sensei_course_completed_email" value="1" <?php checked( 1, $completed ); ?> />
				<?php esc_html_e('Send email to the course creator when all learners have taken and passed the course', 'follow_up_emails'); ?>
			</label>
		</th>
	</tr>
	<tr class="sensei_course_completed_email_row">
		<th>
			<label for="sensei_course_completed_email_subject"><?php esc_html_e('Email Subject', 'follow_up_emails'); ?></label>
		</th>
		<td>
			<input type="text" name="sensei_course_completed_email_subject" id="sensei_course_completed_email_subject" class="text" size="50" value="<?php echo esc_attr( $completed_email_subject ); ?>" />
		</td>
	</tr>
	<tr class="sensei_course_completed_email_row">
		<th>
			<label for="sensei_course_completed_email_body"><?php esc_html_e('Email Body', 'follow_up_emails'); ?></label>
			<p>
				<?php esc_html_e('Email Variables', 'follow_up_emails'); ?>
			</p>
			<dl>
				<dd>{course}</dd>
				<dd>{learners}</dd>
			</dl>
		</th>
		<td>
			<?php wp_editor( $completed_email_body, 'sensei_course_completed_email_body', array('editor_height' => 200) ); ?>
		</td>
	</tr>
	<tr>
		<th colspan="2">
			<label for="sensei_course_incomplete_email">
				<input type="checkbox" name="sensei_course_incomplete_email" id="sensei_course_incomplete_email" value="1" <?php checked( 1, $incomplete ); ?> />
				<?php esc_html_e('Send an email to the course creator when there are learners who haven\'t completed a course in ', 'follow_up_emails'); ?>
			</label>
			<input name="sensei_course_incomplete_email_deadline_number" type="number" min="1" value="<?php echo esc_attr( $deadline_number ); ?>" style="width:50px;" />
			<select name="sensei_course_incomplete_email_deadline_period">
				<option value="days" <?php selected( 'days', $deadline_period ); ?>>Days</option>
				<option value="weeks" <?php selected( 'weeks', $deadline_period ); ?>>Weeks</option>
				<option value="months" <?php selected( 'months', $deadline_period ); ?>>Months</option>
				<option value="years" <?php selected( 'years', $deadline_period ); ?>>Years</option>
			</select>
		</th>
	</tr>
	<tr class="sensei_course_incomplete_email_row">
		<th>
			<label for="sensei_course_incomplete_email_subject"><?php esc_html_e('Email Subject', 'follow_up_emails'); ?></label>
		</th>
		<td>
			<input type="text" name="sensei_course_incomplete_email_subject" id="sensei_course_incomplete_email_subject" class="text" size="50" value="<?php echo esc_attr( $incomplete_email_subject ); ?>" />
		</td>
	</tr>
	<tr class="sensei_course_incomplete_email_row">
		<th>
			<label for="sensei_course_incomplete_email_body"><?php esc_html_e('Email Body', 'follow_up_emails'); ?></label>
			<p>
				<?php esc_html_e('Email Variables', 'follow_up_emails'); ?>
			</p>
			<dl>
				<dd>{course}</dd>
				<dd>{learner_name}</dd>
				<dd>{learner_email}</dd>
				<dd>{start_date}</dd>
			</dl>
		</th>
		<td>
			<?php wp_editor( $incomplete_email_body, 'sensei_course_incomplete_email_body', array('editor_height' => 200) ); ?>
		</td>
	</tr>
</table>
<script>
(function($) {
	$( '#sensei_course_completed_email' ).on( 'change', function() {
		if ( $(this).is(":checked") ) {
			$(".sensei_course_completed_email_row").show();
		} else {
			$(".sensei_course_completed_email_row").hide();
		}
	} ).trigger( 'change' );

	$( '#sensei_course_incomplete_email').on( 'change', function() {
		if ( $(this).is(":checked") ) {
			$(".sensei_course_incomplete_email_row").show();
		} else {
			$(".sensei_course_incomplete_email_row").hide();
		}
	} ).trigger( 'change' );
}(jQuery));
</script>
