<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header(); ?>
<div class="wrap">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<article class="page type-page status-publish hentry">
				<header class="entry-header">
					<h1 class="entry-title"><?php esc_html_e('Email Subscriptions', 'follow_up_emails'); ?></h1>
				</header>

				<div class="entry-content">
					<?php

					$me = wp_get_current_user();

					if ( $me->ID > 0 ):
						global $wpdb;
						$user   = wp_get_current_user();
						$emails = $wpdb->get_results( $wpdb->prepare("SELECT COUNT(*) AS num, user_email, order_id FROM {$wpdb->prefix}followup_email_orders WHERE (user_id = %d OR user_email = %s) AND is_sent = 0 AND order_id > 0 GROUP BY order_id", $user->ID, $user->user_email) );

						$args = array(
							'user'          => $user,
							'emails'        => $emails,
							'unsubscribed'  => ( isset($_GET['fue_order_unsubscribed']) ) ? true : false // phpcs:ignore WordPress.Security.NonceVerification
						);

						wc_get_template( 'my-account-emails.php', $args, 'follow-up-emails', trailingslashit( FUE_TEMPLATES_DIR ) );
					else:
						// not logged in
					?>
						<div class="woocommerce-info">
							<?php esc_html_e('You are not subscribed to any emails.', 'follow_up_emails'); ?>
						</div>
					<?php
					endif;
					?>
				</div>
			</article>
		</div><!-- #content -->
	</div><!-- #primary -->
	<?php
	get_sidebar( 'content' );
	get_sidebar();
	?>
</div><!-- /.wrap -->
<?php

get_footer();
