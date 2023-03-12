<?php
/**
 * Smush PRO upgrade page.
 *
 * @since 3.2.3
 * @package WP_Smush
 */

$upgrade_url = add_query_arg(
	array(
		'utm_source' => 'smush',
		'utm_medium' => 'plugin',
	),
	'https://wpmudev.com/project/wp-smush-pro/'
);

$bg_optimization    = WP_Smush::get_instance()->core()->mod->bg_optimization;
$can_use_background = $bg_optimization->can_use_background();

?>

<div class="sui-upgrade-page">
	<div class="sui-upgrade-page-header">
		<div class="sui-upgrade-page__container">
			<div class="sui-upgrade-page-header__content">
				<h1><?php esc_html_e( 'Upgrade to Smush Pro', 'wp-smushit' ); ?></h1>
				<p><?php esc_html_e( 'Get Smush Pro and bulk optimize every image you’ve ever added to your site with one click. Smush images without having to keep the plugin open and serve stunning, high-quality images from 114 locations around the globe with our blazing-fast CDN.', 'wp-smushit' ); ?></p>
				<p><?php esc_html_e( 'Automatically compress and resize huge photos without any size limitations. Double your savings and fix your Google PageSpeed with the best image optimizer WordPress has ever known.', 'wp-smushit' ); ?></p>
				<a href="<?php echo esc_url( add_query_arg( 'utm_campaign', 'smush_propage_topbutton', $upgrade_url ) ); ?>" class="sui-button sui-button-lg sui-button-purple" target="_blank">
					<?php esc_html_e( 'TRY SMUSH PRO NOW', 'wp-smushit' ); ?>
				</a>
				<div class="sui-reviews">
					<span class="sui-reviews__stars"></span>
					<div class="sui-reviews__rating"><span class="sui-reviews-rating">-</span> / <?php esc_html_e( '5.0 rating from', 'wp-smushit' ); ?> <span class="sui-reviews-customer-count">-</span> <?php esc_html_e( 'customers', 'wp-smushit' ); ?></div>
					<a class="sui-reviews__link" href="https://www.reviews.io/company-reviews/store/wpmudev-org" target="_blank">
						Reviews.io<i class="sui-icon-arrow-right" aria-hidden="true"></i>
					</a>
				</div>
			</div>

			<div class="sui-upgrade-page-header__image"></div>
		</div>
	</div>
	<div class="smush-stats">
		<div class="smush-stats-item">
			<div><span>65.83</span> <?php esc_html_e( 'Billion', 'wp-smushit' ); ?></div>
			<div class="smush-stats-description"><?php esc_html_e( 'Images Optimized', 'wp-smushit' ); ?></div>
		</div>
		<div class="smush-stats-item">
			<div><span>726,410</span></div>
			<div class="smush-stats-description"><?php esc_html_e( 'Sites Optimized', 'wp-smushit' ); ?></div>
		</div>
		<div class="smush-stats-item">
			<div><span>287,038</span> GB</div>
			<div class="smush-stats-description"><?php esc_html_e( 'Total Savings', 'wp-smushit' ); ?></div>
		</div>
	</div>

	<div class="sui-upgrade-page-features">
		<div class="sui-upgrade-page-features__header" style="margin-top: 70px">
			<h2><?php esc_html_e( 'Optimize unlimited images with Smush Pro', 'wp-smushit' ); ?></h2>
			<p><?php esc_html_e( 'Learn why Smush Pro is the best image optimization plugin.', 'wp-smushit' ); ?></p>
			<div class="thumbnail-container">
				<img src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/smush-thumbnail@2x.png' ); ?>" alt="<?php esc_attr_e( 'Play', 'wp-smushit' ); ?>" id="wistia-play-button" role="button">
			</div>
			<span id="wistia_oegnwrdag1"></span>
			<script>
				document.addEventListener("DOMContentLoaded", function() {
					var trigger = document.getElementById("wistia-play-button");

					window.wistiaSmushEmbed = null;
					window.wistiaInit = function(Wistia) {
						window.wistiaSmushEmbed = Wistia.embed("oegnwrdag1", {
							version: "v2",
							videoWidth: 1280,
							videoHeight: 720,
							playerColor: "14485f",
							videoQuality: "hd-only",
							popover: true,
							popoverPreventScroll: true,
							popoverContent: 'html'
						});
					};

					if (trigger) {
						trigger.addEventListener("click", function(e) {
							e.preventDefault();
							if (window.wistiaSmushEmbed) {
								window.wistiaSmushEmbed.play();
							}
						});
					}
				});
			</script>
		</div>
	</div>

	<div class="sui-upgrade-page-features">
		<div class="sui-upgrade-page-features__header">
			<h2><?php esc_html_e( 'Pro Features', 'wp-smushit' ); ?></h2>
			<p><?php esc_html_e( 'Upgrading to Pro will get you the following benefits.', 'wp-smushit' ); ?></p>
		</div>
	</div>

	<div class="sui-upgrade-page__container">
		<div class="sui-upgrade-page-features__items">
			<?php if( ! $can_use_background ) : ?>
			<div class="sui-upgrade-page-features__item">
				<i class="sui-icon-unlock" aria-hidden="true"></i>
				<h3><?php esc_html_e( 'No limits, no restrictions', 'wp-smushit' ); ?></h3>
				<p><?php esc_html_e( 'Need a one-click bulk optimization solution to quickly and easily compress your entire image library? Remove the ‘per batch’ bulk smushing restriction and increase the image size limit from 5MB to completely unlimited.', 'wp-smushit' ); ?></p>
			</div>
			<div class="sui-upgrade-page-features__item">
			<span class="sui-icon-loader" aria-hidden="true"></span>
				<h3><?php esc_html_e( 'Compress images in the background', 'wp-smushit' ); ?></h3>
				<p><?php esc_html_e( 'Thanks to Background Optimization, you can leave the plugin interface while images are still being compressed. Smush will continue to work its magic in the background, leaving you free to do other things!', 'wp-smushit' ); ?></p>
			</div>
			<?php endif;?>
			<div class="sui-upgrade-page-features__item">
				<i class="sui-icon-web-globe-world" aria-hidden="true"></i>
				<h3><?php esc_html_e( 'Streamline your images with Smush CDN', 'wp-smushit' ); ?></h3>
				<p><?php esc_html_e( 'Use the blazing-fast Smush image CDN to automatically resize your files to the perfect size and serve WebP files (25% smaller than PNG and JPG) from 114 locations around the globe.', 'wp-smushit' ); ?></p>
			</div>
			<div class="sui-upgrade-page-features__item">
				<i class="sui-icon-wand-magic" aria-hidden="true"></i>
				<h3><?php esc_html_e( 'Auto-convert PNGs to JPEGs (lossy)', 'wp-smushit' ); ?></h3>
				<p><?php esc_html_e( "Smush looks for additional savings and automatically converts PNG files to JPEG if it will further reduce the size without a visible drop in quality. Now that's smart image compression.", 'wp-smushit' ); ?></p>
			</div>
			<div class="sui-upgrade-page-features__item">
				<i class="sui-icon-photo-picture" aria-hidden="true"></i>
				<h3><?php esc_html_e( 'Serve next-gen WebP images (without Smush CDN)', 'wp-smushit' ); ?></h3>
				<p><?php esc_html_e( "Prefer not to use Smush CDN? Our standalone WebP feature allows you to serve next-gen images without sacrificing quality. You can also gracefully fall back to the older image formats for browsers that aren't compatible.", 'wp-smushit' ); ?></p>
			</div>
			<div class="sui-upgrade-page-features__item">
				<i class="sui-icon-gdpr" aria-hidden="true"></i>
				<h3><?php esc_html_e( 'Premium WordPress plugins', 'wp-smushit' ); ?></h3>
				<p><?php esc_html_e( 'Along with Smush, you get WPMU DEV’s (the developers of Smush) full suite of premium WP plugins. Covering everything from security and backups, to marketing and SEO. Use these bonus tools on unlimited sites and keep them free, forever!', 'wp-smushit' ); ?></p>
			</div>
			<div class="sui-upgrade-page-features__item">
				<i class="sui-icon-graph-bar" aria-hidden="true"></i>
				<h3><?php esc_html_e( 'Automated white label reports', 'wp-smushit' ); ?></h3>
				<p><?php esc_html_e( 'Customize, style, schedule and send white label client and developer reports in just a few clicks. Each report includes embedded performance, security, SEO, and analytics data.', 'wp-smushit' ); ?></p>
			</div>
			<div class="sui-upgrade-page-features__item">
				<i class="sui-icon-hub" aria-hidden="true"></i>
				<h3><?php esc_html_e( 'Manage unlimited WP sites with The Hub', 'wp-smushit' ); ?></h3>
				<p><?php esc_html_e( 'Automate site updates, backups, security, and performance – all from one central site management dashboard. Call on our expert 24/7 live support directly from your interface at anytime.', 'wp-smushit' ); ?></p>
			</div>
			<div class="sui-upgrade-page-features__item">
				<i class="sui-icon-help-support" aria-hidden="true"></i>
				<h3><?php esc_html_e( '24/7 live WordPress support', 'wp-smushit' ); ?></h3>
				<p><?php esc_html_e( "We can’t stress this enough: Our outstanding WordPress support is available with live chat 24/7, and we’ll help you with absolutely any WordPress issue, not just our products. It’s an expert WordPress team on call whenever you need them.", 'wp-smushit' ); ?></p>
			</div>
			<div class="sui-upgrade-page-features__item">
				<i class="sui-icon-wpmudev-logo" aria-hidden="true"></i>
				<h3><?php esc_html_e( 'Zero risk, 30-day money-back guarantee', 'wp-smushit' ); ?></h3>
				<p><?php esc_html_e( "We offer a full 30-day money-back guarantee. So if Smush isn’t the best image optimizer you’ve ever used, let us know and we’ll refund all of your money immediately.", 'wp-smushit' ); ?></p>
			</div>
		</div>
	</div>
	<div class="sui-upgrade-page-cta">
		<div class="sui-upgrade-page-cta__inner">
			<h2><?php esc_html_e( 'Join 936,586 Happy Members', 'wp-smushit' ); ?></h2>
			<p><?php esc_html_e( "97% of customers are happy with WPMU DEV's service. See what all the fuss is about.", 'wp-smushit' ); ?></p>
			<div>
				<a href="<?php echo esc_url( add_query_arg( 'utm_campaign', 'smush_propage_bottombutton', $upgrade_url ) ); ?>" class="sui-button sui-button-lg sui-button-purple" target="_blank">
					<?php esc_html_e( 'Get Smush Pro for a faster WP SITE', 'wp-smushit' ); ?>
				</a>
			</div>
			<small>
				<?php esc_html_e( 'Includes a 30-day money-back guarantee', 'wp-smushit' ); ?>
			</small>
		</div>
	</div>
</div>

<div class="sui-footer">
	<?php esc_html_e( 'Made with', 'wp-smushit' ); ?> <i class="sui-icon-heart" aria-hidden="true"></i> <?php esc_html_e( 'by WPMU DEV', 'wp-smushit' ); ?>
</div>

<ul class="sui-footer-nav">
	<li><a href="https://profiles.wordpress.org/wpmudev#content-plugins" target="_blank">
			<?php esc_html_e( 'Free Plugins', 'wp-smushit' ); ?>
		</a></li>
	<li><a href="https://wpmudev.com/roadmap/" target="_blank">
			<?php esc_html_e( 'Roadmap', 'wp-smushit' ); ?>
		</a></li>
	<li><a href="https://wordpress.org/support/plugin/wp-smushit" target="_blank">
			<?php esc_html_e( 'Support', 'wp-smushit' ); ?>
		</a></li>
	<li><a href="https://wpmudev.com/docs/" target="_blank">
			<?php esc_html_e( 'Docs', 'wp-smushit' ); ?>
		</a></li>
	<li><a href="https://wpmudev.com/hub-welcome/" target="_blank">
			<?php esc_html_e( 'The Hub', 'wp-smushit' ); ?>
		</a></li>
	<li><a href="https://wpmudev.com/terms-of-service/" target="_blank">
			<?php esc_html_e( 'Terms of Service', 'wp-smushit' ); ?>
		</a></li>
	<li><a href="https://incsub.com/privacy-policy/" target="_blank">
			<?php esc_html_e( 'Privacy Policy', 'wp-smushit' ); ?>
		</a></li>
</ul>