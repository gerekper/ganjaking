<div class="follow-up-subscriptions">
	<h2><?php echo wp_kses_post( get_option( 'fue_email_subscriptions_page_title', 'Email Subscriptions' ) ); ?></h2>

	<a href="<?php echo esc_url( add_query_arg( get_option( 'fue_email_preferences_endpoint', 'email-preferences' ), '', get_permalink( get_option('woocommerce_myaccount_page_id') ) ) ); ?>">
		<?php esc_html_e('Manage email subscriptions', 'follow_up_emails'); ?>
	</a>
</div>
