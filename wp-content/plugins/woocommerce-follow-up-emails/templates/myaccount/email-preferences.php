<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! is_user_logged_in() ) {
	$email_preferences = get_option( 'fue_email_preferences_endpoint', 'email-preferences' );
	wp_safe_redirect( fue_get_login_url( site_url( "/$email_preferences" ) ) );
	exit;
}

$me               = wp_get_current_user();
$newsletter       = new FUE_Newsletter();
$public_lists     = $newsletter->get_public_lists();
$subscriber       = $newsletter->get_subscriber( $me->user_email );
$subscriber_lists = array();

if ( ! empty( $subscriber['lists'] ) ) {
	foreach ( $subscriber['lists'] as $list ) {
		$subscriber_lists[] = $list['id'];
	}
}

get_header(); ?>
<div class="wrap">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<article class="page type-page status-publish hentry">
				<header class="entry-header">
					<h1 class="entry-title"><?php echo wp_kses_post( get_option( 'fue_email_subscriptions_page_title', 'Email Subscriptions' ) ); ?></h1>
				</header>

				<div class="entry-content">
					<div class="follow-up-subscriptions">

						<div class="fue-subscriptions-message hidden fue-success">
							<p><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Saved', 'follow_up_emails'); ?></p>
						</div>

						<?php
						if ( empty( $public_lists ) ) {
							echo '<p>' . esc_html__( 'There are currently no mailing list to manage.', 'follow_up_emails' ) . '</p>';
						} else {
						?>
						<form id="fue-subscriptions-form" action="" method="post">
							<ul class="follow-up-lists">
								<?php foreach ( $public_lists as $list ): ?>
									<li class="list-<?php echo esc_attr( $list['id'] ); ?>">
										<label>
											<input type="checkbox" class="chk-fue-list" name="fue_lists[]" value="<?php echo esc_attr( $list['id'] ); ?>" <?php checked( true, in_array( $list['id'], $subscriber_lists ) ); ?> />
											<?php echo esc_html( $list['list_name'] ); ?>
										</label>
									</li>
								<?php endforeach; ?>
							</ul>
							<?php wp_nonce_field( 'update_email_subscriptions', 'update-email-subscriptions-nonce' ); ?>
							<input type="submit" class="button button-primary fue-button" value="<?php echo esc_attr( get_option('fue_email_subscriptions_button_text', 'Update Subscriptions') ); ?>" />
						</form>
						<?php
						}
						?>
					</div>
				</div>
			</article>
		</div><!-- #content -->
	</div><!-- #primary -->
	<?php
	get_sidebar( 'content' );
	get_sidebar();
	?>
</div><!-- .wrap -->
<?php

get_footer();
