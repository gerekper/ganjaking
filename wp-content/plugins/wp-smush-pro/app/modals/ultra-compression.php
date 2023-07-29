<?php
/**
 * Ultra Compression Modal.
 *
 * @package WP_Smush
 *
 * @var string $location Where to tracking ultra compression.
 */
use Smush\Core\Helper;

if ( ! defined( 'WPINC' ) ) {
	die;
}
$utm_campain_locations = array(
	'settings'          => array(
		'upgrade_link' => 'smush_ultra_bulksmush_radio',
	),
	'summary_box'       => array(
		'upgrade_link' => 'smush_ultra_bulksmush_summary',
	),
	'dashboard_summary' => array(
		'upgrade_link' => 'smush_ultra_dashboard_summary',
	),
);

$location     = isset( $location ) && isset( $utm_campain_locations[ $location ] ) ? $location : 'settings';
$utm_campains = $utm_campain_locations[ $location ];

$upgrade_utm_link = Helper::get_url( $utm_campains['upgrade_link'] );
$connect_site_url = $this->get_connect_site_link();


$modal_id = "wp-smush-ultra-compression-modal__{$location}";
?>

<div class="sui-modal sui-modal-md">
	<div
		role="dialog"
		id="<?php echo esc_attr( $modal_id ); ?>"
		class="sui-modal-content wp-smush-modal-dark-background wp-smush-ultra-compression-modal"
		aria-modal="true"
		aria-labelledby="smush-title-updated-dialog"
	>
		<div class="sui-box">
			<div class="sui-box-header sui-flatten sui-content-center sui-spacing-sides--20">
				<figure class="sui-box-banner" aria-hidden="true">
					<img src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/bulk-smush/smush-ultra-compression.jpg' ); ?>"
						srcset="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/bulk-smush/smush-ultra-compression.jpg' ); ?> 1x, <?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/bulk-smush/smush-ultra-compression' ); ?>@2x.jpg 2x"
						alt="<?php esc_attr_e( 'Smush Ultra Compression', 'wp-smushit' ); ?>" class="sui-image sui-image-center">
				</figure>

				<button type="button" class="sui-button-icon sui-button-float--right sui-button-white" style="box-shadow:none!important" data-esc-close="true" data-modal-close="">
					<i class="sui-icon-close sui-md" aria-hidden="true"></i>
				</button>
			</div>

			<div class="sui-box-body sui-content-center sui-spacing-sides--30 sui-spacing-top--50 sui-spacing-bottom--20">
				<h3 class="sui-box-title sui-lg" id="smush-title-updated-dialog" style="white-space: normal">
					<?php esc_html_e( 'Serve images faster with Ultra Smush', 'wp-smushit' ); ?>
				</h3>

				<p class="sui-description">
					<?php esc_html_e( 'Experience up to 5x better compression than Super Smush. Optimize your images even further and make your pages load faster than ever. Level up your site performance with Ultra Smush now.', 'wp-smushit' ); ?>
				</p>
				<a target="_blank" data-action="upgrade" href="<?php echo esc_url( $upgrade_utm_link ); ?>" class="sui-button sui-button-blue wp-smush-modal-link-close" >
					<?php esc_html_e( 'Find out more', 'wp-smushit' ); ?>
				</a>
				<?php
				if ( $connect_site_url ) :
					$to_dash_page = strpos( $connect_site_url, 'page=wpmudev' );
					?>
					<p class="smush-footer-link"><?php esc_html_e( 'Already a member?', 'wp-smushit' ); ?> <a target="<?php echo $to_dash_page ? '_self' : '_blank'; ?>" data-action="<?php echo $to_dash_page ? 'connect_dash' : 'connect_site'; ?>" class="wp-smush-modal-link-close" href="<?php echo esc_url( $connect_site_url ); ?>"><?php esc_html_e( 'Connect site', 'wp-smushit' ); ?></a></p>
				<?php endif; ?>
			</div>

			<div class="sui-box-footer sui-flatten sui-spacing-bottom--30">
				<h4 class="sui-box-title"><?php esc_html_e( 'Get the following Smush Pro features', 'wp-smushit' ); ?></h4>
				<div class="wp-smush-pro-features">
					<ul>
						<li>
							<div class="wp-smush-pro-features_item">
								<div class="sui-icon-box">
									<span class="sui-icon-unlock" aria-hidden="true"></span>
								</div>
								<div class="sui-content-box">
									<h5><?php esc_html_e( 'No Limits, no restrictions', 'wp-smushit' ); ?></h5>
										<p><?php esc_html_e( "One-click bulk optimization, it's quick and easy to compress unlimited images.", 'wp-smushit' ); ?></p>
								</div>
							</div>
							<div class="wp-smush-pro-features_item">
								<div class="sui-icon-box">
									<span class="sui-icon-loader" aria-hidden="true"></span>
								</div>
								<div class="sui-content-box">
									<h5><?php esc_html_e( 'Compress images in the background', 'wp-smushit' ); ?></h5>
									<p><?php esc_html_e( 'With background optimization, Smush will keep working even if you leave the page.', 'wp-smushit' ); ?></p>
								</div>
							</div>
						</li>
						<li>
							<div class="wp-smush-pro-features_item">
								<div class="sui-icon-box">
									<span class="sui-icon-web-globe-world" aria-hidden="true"></span>
								</div>
								<div class="sui-content-box">
									<h5><?php esc_html_e( 'Streamline your images with Smush CDN', 'wp-smushit' ); ?></h5>
									<p><?php esc_html_e( 'Serve images faster and closer than ever from 114 data centers all over the globe.', 'wp-smushit' ); ?></p>
								</div>
							</div>
							<div class="wp-smush-pro-features_item">
								<div class="sui-icon-box">
									<span class="sui-icon-wand-magic" aria-hidden="true"></span>
								</div>
								<div class="sui-content-box">
									<h5><?php esc_html_e( 'Auto-convert PNGs to JPEGs (lossy)', 'wp-smushit' ); ?></h5>
									<p><?php esc_html_e( 'Enjoy more savings and speed by auto-converting to the ideal file format.', 'wp-smushit' ); ?></p>
								</div>
							</div>
						</li>
						<li>
							<div class="wp-smush-pro-features_item">
								<div class="sui-icon-box">
									<span class="sui-icon-photo-picture" aria-hidden="true"></span>
								</div>
								<div class="sui-content-box">
									<h5><?php esc_html_e( 'Serve next-gen WebP images', 'wp-smushit' ); ?></h5>
									<p><?php esc_html_e( 'Serve lighter and more streamlined WebP images without losing quality.', 'wp-smushit' ); ?></p>
								</div>
							</div>
							<div class="wp-smush-pro-features_item">
								<div class="sui-icon-box">
									<span class="sui-icon-hummingbird" aria-hidden="true"></span>
								</div>
								<div class="sui-content-box">
									<h5><?php esc_html_e( 'Faster sites with Hummingbird Pro', 'wp-smushit' ); ?></h5>
									<p><?php esc_html_e( 'Optimize the performance of your sites further with a full caching suite and automatic asset optimization.', 'wp-smushit' ); ?></p>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>