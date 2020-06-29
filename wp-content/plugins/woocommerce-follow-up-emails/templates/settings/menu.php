<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
	<a href="admin.php?page=followup-emails-settings&amp;tab=system" class="nav-tab <?php if ($tab == 'system') echo 'nav-tab-active'; ?>"><?php esc_html_e(' General Settings', 'follow_up_emails'); ?></a>
	<a href="admin.php?page=followup-emails-settings&amp;tab=auth" class="nav-tab <?php if ($tab == 'auth') echo 'nav-tab-active'; ?>"><?php esc_html_e(' DKIM & SPF', 'follow_up_emails'); ?></a>
	<a href="admin.php?page=followup-emails-settings&amp;tab=subscribers" class="nav-tab <?php if ($tab == 'subscribers') echo 'nav-tab-active'; ?>"><?php esc_html_e(' Subscribers', 'follow_up_emails'); ?></a>
	<a href="admin.php?page=followup-emails-settings&amp;tab=tools" class="nav-tab <?php if ($tab == 'tools') echo 'nav-tab-active'; ?>"><?php esc_html_e(' Tools', 'follow_up_emails'); ?></a>
	<a href="admin.php?page=followup-emails-settings&amp;tab=integration" class="nav-tab <?php if ($tab == 'integration') echo 'nav-tab-active'; ?>"><?php esc_html_e(' Optional Extras', 'follow_up_emails'); ?></a>
	<?php do_action( 'fue_settings_tabs' ); ?>
</h2>
