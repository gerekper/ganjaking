<?php
/**
 * Summary meta box on dashboard page.
 *
 * @since 3.8.3
 * @package WP_Smush
 *
 * @var string    $cdn_status         CDN status.
 * @var string    $human_bytes
 * @var bool      $is_cdn             CDN module status.
 * @var bool      $is_lazy_load       Lazy load status.
 * @var bool      $is_local_webp      Local WebP status.
 * @var int       $resize_count       Number of resizes images.
 * @var string    $upsell_url_cdn     CDN upsell URL.
 * @var string    $upsell_url_webp    Local WebP upsell URL.
 * @var bool      $webp_configured    WebP set up configured.
 * @var string    $percent_grade      Circle grade class.
 * @var int|float $percent_metric     Metric to calculate circle score.
 * @var int       $percent_optimized  Percent optimized.
 * @var int       $total_optimized    Total nubmer of images optimized.
 * @var string|int $stats_percent
 */
use Smush\App\Admin;

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>
<?php
$this->view(
	'scan-progress-bar',
	array(),
	'common'
);

$this->view(
	'circle-progress-bar',
	array(
		'percent_grade'     => $percent_grade,
		'percent_optimized' => $percent_optimized,
		'percent_metric'    => $percent_metric,
	),
	'common'
);

$this->view(
	'summary-segment',
	array(
		'human_bytes'     => $human_bytes,
		'total_optimized' => $total_optimized,
		'stats_percent'   => $stats_percent,
		'resize_count'    => $resize_count,
	),
	'common'
);
?>
<div class="sui-summary-segment" style="overflow: visible">
	<ul class="sui-list">
		<li>
			<span class="sui-list-label">
				<?php esc_html_e( 'CDN', 'wp-smushit' ); ?>
			</span>
			<span class="sui-list-detail">
				<?php
				if ( ! WP_Smush::is_pro() ) :
					$cdn_upsell_tooltip = sprintf(
						/* translators: %d: Number of CDN PoP locations */
						esc_attr__( 'Multiply the speed and savings! Serve your images from our CDN from %d blazing fast servers around the world.', 'wp-smushit' ),
						Admin::CDN_POP_LOCATIONS
					);
					?>
					<a href="<?php echo esc_url( $upsell_url_cdn ); ?>" target="_blank" class="smush-upgrade-text">
					<?php esc_html_e( 'Upgrade', 'wp-smushit' ); ?>
					</a>
					<span class="sui-tooltip sui-tooltip-constrained sui-tooltip-top-right" style="--tooltip-width: 360px;" data-tooltip="<?php echo esc_attr( $cdn_upsell_tooltip ); ?>">
						<span class="sui-tag sui-tag-sm sui-tag-purple"><?php esc_html_e( 'Pro', 'wp-smushit' ); ?></span>
					</span>
				<?php elseif ( $is_cdn ) : ?>
					<?php if ( 'overcap' === $cdn_status ) : ?>
						<span class="sui-tooltip sui-tooltip-constrained" data-tooltip="<?php esc_attr_e( "You're almost through your CDN bandwidth limit. Please contact your administrator to upgrade your Smush CDN plan to ensure you don't lose this service.", 'wp-smushit' ); ?>">
							<span class="sui-icon-warning-alert sui-md sui-warning" aria-hidden="true"></span>
						</span>
						<span><?php esc_html_e( 'Overcap', 'wp-smushit' ); ?></span>
					<?php elseif ( 'upgrade' === $cdn_status ) : ?>
						<span class="sui-tooltip sui-tooltip-constrained" data-tooltip="<?php esc_attr_e( "You've gone through your CDN bandwidth limit, so we’ve stopped serving your images via the CDN. Contact your administrator to upgrade your Smush CDN plan to reactivate this service.", 'wp-smushit' ); ?>">
							<span class="sui-icon-warning-alert sui-md sui-error" aria-hidden="true"></span>
						</span>
						<span><?php esc_html_e( 'Overcap', 'wp-smushit' ); ?></span>
					<?php else : ?>
						<a href="<?php echo esc_url( $this->get_url( 'smush-cdn' ) ); ?>">
							<span class="sui-tag sui-tag-green"><?php esc_html_e( 'Active', 'wp-smushit' ); ?></span>
						</a>
					<?php endif; ?>
				<?php else : ?>
					<a href="<?php echo esc_url( $this->get_url( 'smush-cdn' ) ); ?>">
						<span class="sui-tag"><?php esc_html_e( 'Inactive', 'wp-smushit' ); ?></span>
					</a>
				<?php endif; ?>
			</span>
		</li>
		<?php if ( ! is_multisite() ) : ?>
			<li>
				<span class="sui-list-label">
					<?php esc_html_e( 'Local WebP', 'wp-smushit' ); ?>
				</span>
				<span class="sui-list-detail">
					<?php if ( ! WP_Smush::is_pro() ) : ?>
						<a href="<?php echo esc_url( $upsell_url_webp ); ?>" target="_blank" class="smush-upgrade-text">
							<?php esc_html_e( 'Upgrade', 'wp-smushit' ); ?>
						</a>
						<span class="sui-tooltip sui-tooltip-constrained sui-tooltip-top-right" style="--tooltip-width: 360px;" data-tooltip="<?php esc_attr_e( 'Fix the “Serve images in next-gen format” Google PageSpeed recommendation by setting up this feature. Locally serve WebP versions of your images to supported browsers, and gracefully fall back to JPEGs and PNGs for browsers that don’t support WebP.', 'wp-smushit' ); ?>">
							<span class="sui-tag sui-tag-sm sui-tag-purple"><?php esc_html_e( 'Pro', 'wp-smushit' ); ?></span>
						</span>
					<?php elseif ( $is_local_webp && $webp_configured ) : ?>
						<a href="<?php echo esc_url( $this->get_url( 'smush-webp' ) ); ?>">
							<span class="sui-tag sui-tag-green"><?php esc_html_e( 'Active', 'wp-smushit' ); ?></span>
						</a>
					<?php elseif ( $is_local_webp && ! $webp_configured ) : ?>
						<span class="sui-tooltip sui-tooltip-constrained" data-tooltip="<?php esc_attr_e( 'The setup for Local WebP feature is inactive. Complete the setup, to activate the feature.', 'wp-smushit' ); ?>">
							<span class="sui-icon-warning-alert sui-md sui-warning" aria-hidden="true"></span>
						</span>
						<span><?php esc_html_e( 'Incomplete setup', 'wp-smushit' ); ?></span>
					<?php else : ?>
						<a href="<?php echo esc_url( $this->get_url( 'smush-webp' ) ); ?>">
							<span class="sui-tag"><?php esc_html_e( 'Inactive', 'wp-smushit' ); ?></span>
						</a>
					<?php endif; ?>
				</span>
			</li>
		<?php endif; ?>
		<li>
			<span class="sui-list-label">
				<?php esc_html_e( 'Lazy Load', 'wp-smushit' ); ?>
			</span>
			<span class="sui-list-detail">
				<?php if ( $is_lazy_load ) : ?>
					<a href="<?php echo esc_url( $this->get_url( 'smush-lazy-load' ) ); ?>">
						<span class="sui-tag sui-tag-green"><?php esc_html_e( 'Active', 'wp-smushit' ); ?></span>
					</a>
				<?php else : ?>
					<a href="<?php echo esc_url( $this->get_url( 'smush-lazy-load' ) ); ?>">
						<span class="sui-tag"><?php esc_html_e( 'Inactive', 'wp-smushit' ); ?></span>
					</a>
				<?php endif; ?>
			</span>
		</li>
		<?php $this->view( 'summary/lossy-level' ); ?>
	</ul>
</div>