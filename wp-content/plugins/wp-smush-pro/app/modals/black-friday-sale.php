<?php
/**
 * Show Black Friday sale modal.
 *
 * @package WP_Smush
 *
 * @since 3.7.3
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$main_cta_url = add_query_arg(
	array(
		'coupon'       => 'BF2020SMUSH',
		'checkout'     => 0,
		'utm_source'   => 'smush',
		'utm_medium'   => 'plugin',
		'utm_campaign' => 'smush_bf2020modal_cta',
	),
	$this->upgrade_url
);

$check_plans_url = add_query_arg(
	array(
		'coupon'       => 'BF2020SMUSH',
		'checkout'     => 0,
		'utm_source'   => 'smush',
		'utm_medium'   => 'plugin',
		'utm_campaign' => 'smush_bf2020modal_checkallplansbutton',
	),
	$this->upgrade_url
);
?>

<div class="sui-modal sui-modal-lg">

	<div
		role="dialog"
		id="smush-black-friday-dialog"
		class="sui-modal-content"
		aria-modal="true"
		aria-labelledby="smush-dialog--black-friday-sale-title"
		aria-describedby="smush-dialog--black-friday-sale-desc"
	>

		<div class="sui-box">

			<div class="sui-box-header sui-content-center sui-flatten sui-spacing-top--60">

				<button class="sui-button-icon sui-button-float--right" data-modal-close>
					<i class="sui-icon-close sui-md" aria-hidden="true"></i>
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'wp-smushit' ); ?></span>
				</button>

				<figure class="sui-box-banner" role="banner" aria-hidden="true">
						<img class="sui-image sui-image-center"
							src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/black-friday-modal-header.png' ); ?>"
							srcset="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/black-friday-modal-header@2x.png' ); ?> 2x"
						>
				</figure>

			</div>

			<div class="sui-box-body sui-spacing-top--0 sui-spacing-sides--50">

				<div id="smush-black-modal-content" class="sui-border-frame">

					<h3 id="smush-dialog--black-friday-sale-title" class="smush-black-title"><?php esc_html_e( 'Smush Pro 60% OFF', 'wp-smushit' ); ?></h3>

					<p id="smush-dialog--black-friday-sale-desc" class="sui-description"><?php esc_html_e( 'For all your image optimization needs.', 'wp-smushit' ); ?></p>

					<p class="sui-screen-reader-text" style="margin-bottom: 0;"><?php esc_html_e( 'Before $60 per year, now $24 per year. A total of 8 months free!', 'wp-smushit' ); ?></p>

					<div class="smush-black-price" aria-hidden="true">

						<p>
							<del>$60</del>
							<ins>$24</ins>
							<span>/<?php esc_html_e( 'year', 'wp-smushit' ); ?></span>
						</p>

						<p><?php esc_html_e( 'Total of 8 months free!', 'wp-smushit' ); ?></p>

					</div>

					<p>
						<a
							href="<?php echo esc_url( $main_cta_url ); ?>"
							target="_blank"
							class="sui-button sui-button-purple"
						>
							<?php esc_html_e( 'Get 60% Off Smush Pro', 'wp-smushit' ); ?>
						</a>
					</p>

					<h4 class="smush-black-benefits-title"><?php esc_html_e( 'Smush Pro benefits', 'wp-smushit' ); ?></h4>

					<ul class="smush-black-benefits-list">

						<li>
							<span class="sui-icon-check sui-sm" aria-hidden="true"></span>
							<?php esc_html_e( 'Unlimited Bulk Smush', 'wp-smushit' ); ?>
						</li>

						<li>
							<span class="sui-icon-check sui-sm" aria-hidden="true"></span>
							<?php esc_html_e( 'Smush original images', 'wp-smushit' ); ?>
						</li>

						<li>
							<span class="sui-icon-check sui-sm" aria-hidden="true"></span>
							<?php esc_html_e( '10 GB Smush CDN', 'wp-smushit' ); ?>
						</li>

						<li>
							<span class="sui-icon-check sui-sm" aria-hidden="true"></span>
							<?php esc_html_e( 'Automated resizing', 'wp-smushit' ); ?>
						</li>

						<li>
							<span class="sui-icon-check sui-sm" aria-hidden="true"></span>
							<?php esc_html_e( 'Super Smush lossy compression', 'wp-smushit' ); ?>
						</li>

						<li>
							<span class="sui-icon-check sui-sm" aria-hidden="true"></span>
							<?php esc_html_e( 'Auto convert PNG’s to JPEG’s', 'wp-smushit' ); ?>
						</li>

						<li>
							<span class="sui-icon-check sui-sm" aria-hidden="true"></span>
							<?php esc_html_e( 'NextGen Gallery integration', 'wp-smushit' ); ?>
						</li>

						<li>
							<span class="sui-icon-check sui-sm" aria-hidden="true"></span>
							<?php esc_html_e( 'WebP Conversion', 'wp-smushit' ); ?>
						</li>

					</ul>

				</div>

			</div>

			<div class="sui-box-footer sui-content-center sui-flatten">

				<a
					href="<?php echo esc_url( $check_plans_url ); ?>"
					target="_blank"
					class="sui-button sui-button-ghost"
				>
					<i class="sui-icon-open-new-window sui-sm" aria-hidden="true"></i>
					<?php esc_html_e( 'Check All Plans', 'wp-smushit' ); ?>
				</a>

			</div>

		</div>

	</div>

</div>
