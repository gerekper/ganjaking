<?php
/**
 * Smush PRO upgrade page.
 *
 * @since 3.2.3
 * @package WP_Smush
 */
use Smush\App\Admin;

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
				<p>
				<?php
				printf(
					/* translators: %d: Number of CDN PoP locations */
					esc_html__( "Get Smush Pro and bulk optimize every image you've ever added to your site with one click. Smush images in the background and serve them in stunning high quality from %d locations around the globe with our blazing-fast CDN.", 'wp-smushit' ),
					Admin::CDN_POP_LOCATIONS
				);
				?>
				</p>
				<p>
				<?php
				printf(
					/* translators: 1: Opening <strong>, 2: Closing </strong> */
					esc_html__( 'Automatically compress and resize huge images without any size limitations. %1$sGet up to 5x better savings with Ultra compression%2$s and fix your Google PageSpeed score with the best image optimizer WordPress has ever known.', 'wp-smushit' ),
					'<strong>',
					'</strong>'
				);
				?>
				</p>
				<a href="<?php echo esc_url( add_query_arg( 'utm_campaign', 'smush_propage_topbutton', $upgrade_url ) ); ?>" class="sui-button sui-button-lg sui-button-purple" target="_blank">
					<?php esc_html_e( 'UPGRADE TO PRO', 'wp-smushit' ); ?>
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
			<div class="sui-upgrade-page-features__item">
				<i class="sui-icon-performance" aria-hidden="true"></i>
				<h3><?php esc_html_e( 'Serve images faster with Ultra Compression', 'wp-smushit' ); ?></h3>
				<p><?php esc_html_e( 'Experience up to 5x better compression than Super Smush. Optimize your images even further and make your pages load faster than ever.', 'wp-smushit' ); ?></p>
			</div>
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
				<p>
				<?php
				printf(
					/* translators: %d: Number of CDN PoP locations */
					esc_html__( 'Use the blazing-fast Smush image CDN to automatically resize your files to the perfect size and serve WebP files (25%% smaller than PNG and JPG) from %d locations around the globe.', 'wp-smushit' ),
					Admin::CDN_POP_LOCATIONS
				);
				?>
					</p>
			</div>
			<div class="sui-upgrade-page-features__item">
				<i class="sui-icon-photo-picture" aria-hidden="true"></i>
				<h3><?php esc_html_e( 'Serve next-gen WebP images (without Smush CDN)', 'wp-smushit' ); ?></h3>
				<p><?php esc_html_e( "Prefer not to use Smush CDN? Our standalone WebP feature allows you to serve next-gen images without sacrificing quality. You can also gracefully fall back to the older image formats for browsers that aren't compatible.", 'wp-smushit' ); ?></p>
			</div>
			<div class="sui-upgrade-page-features__item">
				<i class="sui-icon-wand-magic" aria-hidden="true"></i>
				<h3><?php esc_html_e( 'Auto-convert PNGs to JPEGs (lossy)', 'wp-smushit' ); ?></h3>
				<p><?php esc_html_e( "Smush looks for additional savings and automatically converts PNG files to JPEG if it will further reduce the size without a visible drop in quality. Now that's smart image compression.", 'wp-smushit' ); ?></p>
			</div>
			<div class="sui-upgrade-page-features__item">
				<i class="sui-icon-hummingbird" aria-hidden="true"></i>
				<h3><?php esc_html_e( 'Get faster sites with Hummingbird Pro', 'wp-smushit' ); ?></h3>
				<p>
				<?php
				printf(
					/* translators: %d: Number of CDN PoP locations */
					esc_html__( 'Optimize the performance of your site and ace that Google PageSpeed score with a full caching suite, automatic asset optimization, and our blazing-fast %d-point CDN.', 'wp-smushit' ),
					Admin::CDN_POP_LOCATIONS
				);
				?>
					</p>
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
				<i class="sui-icon-gdpr" aria-hidden="true"></i>
				<h3><?php esc_html_e( 'Premium WordPress plugins', 'wp-smushit' ); ?></h3>
				<p><?php esc_html_e( 'Along with Smush, you get WPMU DEV’s (the developers of Smush) full suite of premium WP plugins. Covering everything from security and backups, to marketing and SEO. Use these bonus tools on unlimited sites and keep them free, forever!', 'wp-smushit' ); ?></p>
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
			<h2><?php esc_html_e( 'Join 1 Million+ Happy Users', 'wp-smushit' ); ?></h2>
			<p><?php esc_html_e( "Discover why we're trusted by 97% of our customers and unlock the ultimate image optimization capabilities to deliver blazing-fast websites with stunning visuals.", 'wp-smushit' ); ?></p>
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

<?php
$this->view( 'footer-links', array(), 'common' );