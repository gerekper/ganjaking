<h2><?php esc_html_e('Twitter', 'follow_up_emails'); ?></h2>

<p>
	<strong><?php esc_html_e('Twitter Handle:', 'follow_up_emails'); ?></strong>
	<?php
	$handle = get_user_meta( get_current_user_id(), 'twitter_handle', true );

	if ( !$handle ) {
		esc_html_e('<em>not set</em>', 'follow_up_emails');
	} else {
		echo '@'. esc_html( $handle );
	}
	?>
	<a href="edit-account" style="margin-left: 50px;"><?php esc_html_e('Change', 'follow_up_emails'); ?></a>
</p>
