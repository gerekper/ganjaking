<?php
$key = ( !empty( $_GET['key'] ) ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

if ( empty( $key ) ) {
	wp_die('Invalid request. Please try again.');
}

$data = FUE_Transients::get_transient( 'fue_manual_email_'. $key );
$recipients = FUE_Transients::get_transient( 'fue_manual_email_recipients_'. $key );

if ( !$data ) {
	wp_die('Invalid request. Please try again.');
}

?>
<script>var key = '<?php echo esc_js( $key ); ?>';</script>
<style>
	.ui-progressbar {
		position: relative;
	}
	.ui-progressbar-value {
		border: 1px solid #fff;
		background: #ededed;
	}
	.progress-label {
		position: absolute;
		left: 10px;
		top: 4px;
		font-weight: bold;
		text-shadow: 1px 1px 0 #fff;
		color: #a9a9a9;
	}
	#log {
		max-height: 300px;
		overflow: auto;
	}
	#log p.success {
		color: green;
	}
	#log p.failure {
		color: #ff0000;
	}
</style>
<div class="wrap">
	<h2><?php esc_html_e('Send Emails', 'follow_up_emails'); ?></h2>

	<p id="total-recipients-label">
		<?php
		$recipients_count = count( $recipients );

		if ( $data['schedule_email'] ) {
			$title = _n( 'Scheduling %d email', 'Scheduling %d emails', $recipients_count, 'follow_up_emails' );
		} else {
			$title = _n( 'Sending %d email', 'Sending %d emails', $recipients_count, 'follow_up_emails' );
		}

		echo esc_html( sprintf( $title, $recipients_count ) );
		?>
	</p>
	<div id="progressbar"><div class="progress-label">Loading...</div></div>

	<div id="log">

	</div>
</div>