<?php
/**
 * Show Updated Features modal.
 *
 * @package WP_Smush
 *
 * @since 3.12.0
 *
 * @var string $cta_url URL for the modal's CTA button.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$try_free_url = add_query_arg(
	array(
		'utm_source'   => 'smush',
		'utm_medium'   => 'plugin',
		'utm_campaign' => 'smush_unlocked_50_images_free_plan',
	),
	esc_url( 'https://wpmudev.com/register/?free_hub' )
);

?>

<div class="sui-modal sui-modal-lg">
	<div
		role="dialog"
		id="smush-wpmudev-free-dialog"
		class="sui-modal-content smush-wpmudev-free-dialog"
		aria-modal="true"
		aria-labelledby="smush-title-updated-dialog"
	>
		<div class="sui-box">
			<div class="sui-box-header sui-content-center sui-flatten">
				<figure class="sui-box-banner">
					<img
						src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/wpmudev-free-logo.png' ); ?>"
						srcset="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/wpmudev-free-logo.png' ); ?> 1x, <?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/wpmudev-free-logo@2x.png' ); ?> 2x"
						alt="<?php esc_html_e( 'Try WPMUDEV Free', 'wp-smushit' ); ?>"
					/>
				</figure>
				<button class="sui-button-icon sui-button-float--right" data-modal-close="">
					<span class="sui-icon-close sui-md" aria-hidden="true"></span>
					<span class="sui-screen-reader-text">Close this modal</span>
				</button>
			</div>
			<div class="sui-box-body">
				<div class="smush-box-description">
					<h3 class="sui-box-title"><?php esc_html_e( 'The Ultimate Web Developer Toolkit', 'wp-smushit' ); ?></h3>
					<p>
						<?php esc_html_e( 'Over 50K web developers use WPMU DEV for fast and convenient site management. Here’s what you get completely free:', 'wp-smushit' ); ?>
					</p>
				</div>
				<ul class="smush-list-detail">
					<li>
						<?php esc_html_e( 'The Hub - effortlessly manage unlimited sites from one dashboard', 'wp-smushit' ); ?>
					</li>
					<li>
						<?php esc_html_e( 'One-click SSO - access all your sites from one place with one click', 'wp-smushit' ); ?>
					</li>
					<li>
						<?php esc_html_e( 'Uptime monitor - instant downtime alerts and helpful site analytics', 'wp-smushit' ); ?>
					</li>
					<li>
						<?php esc_html_e( 'White label reports - custom website health reports for clients', 'wp-smushit' ); ?>
					</li>
					<li>
						<?php esc_html_e( 'Client billing - a full payment solution for your business', 'wp-smushit' ); ?>
					</li>
					<li>
						<?php esc_html_e( 'Auto updates - schedule safe updates for all your plugins and themes', 'wp-smushit' ); ?>
					</li>
					<li>
						<?php esc_html_e( 'Secure site backups - including 1GB free WPMU DEV storage ', 'wp-smushit' ); ?>
					</li>
				</ul>
			</div>

			<div class="sui-box-footer sui-content-center sui-flatten">
				<div class="sui-box-content">
					<p><?php esc_html_e( 'Plus many more membership perks and benefits!', 'wp-smushit' ); ?></p>
					<a href="<?php echo esc_url( $try_free_url );?>" target="_blank" class="sui-button sui-button-blue sui-a smush-try-wpmudev-free">
						<span class="sui-loading-text"><?php esc_html_e( 'TRY THE FREE WPMU DEV PLAN', 'wp-smushit' ); ?></span>
					</a>
					<p class="sui-span">
						<?php esc_html_e( 'No credit card required.', 'wp-smushit' ); ?>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
