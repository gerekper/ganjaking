<?php
/**
 * NextGen summary meta box.
 *
 * @package WP_Smuh
 *
 * @var int        $image_count
 * @var bool       $lossy_enabled
 * @var int        $smushed_image_count
 * @var int        $super_smushed_count
 * @var string     $stats_human
 * @var string|int $stats_percent
 * @var int        $total_count
 * @var string     $percent_grade
 * @var int|float  $percent_metric
 * @var int        $percent_optimized
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<div class="sui-summary-image-space" aria-hidden="true">
	<div class="sui-circle-score <?php echo esc_attr( $percent_grade ); ?> loaded" data-score="<?php echo absint( $percent_optimized ); ?>" id="smush-image-score">
		<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
			<circle stroke-width="16" cx="50" cy="50" r="42"></circle>
			<circle stroke-width="16" cx="50" cy="50" r="42" style="--metric-array: <?php echo 2.63893782902 * absint( $percent_metric ); ?> <?php echo 263.893782902 - absint( $percent_metric ); ?>"></circle>
		</svg>
		<span class="sui-circle-score-label"><?php echo absint( $percent_optimized ); ?></span>
	</div>
	<small><?php esc_html_e( 'Images optimized in the NextGEN Gallery', 'wp-smushit' ); ?></small>
</div>
<div class="sui-summary-segment">
	<div class="sui-summary-details">
		<span class="sui-summary-large wp-smush-total-optimised">
			<?php echo absint( $image_count ); ?>
		</span>
		<span class="sui-summary-sub">
			<?php esc_html_e( 'Images smushed', 'wp-smushit' ); ?>
		</span>
	</div>
</div>
<div class="sui-summary-segment">
	<ul class="sui-list smush-stats-list-nextgen">
		<li class="smush-resize-savings">
			<span class="sui-list-label">
				<?php esc_html_e( 'Total savings', 'wp-smushit' ); ?>
			</span>
			<span class="sui-list-detail wp-smush-stats wp-smush-savings">
				<?php wp_nonce_field( 'save_wp_smush_options', 'wp_smush_options_nonce', '' ); ?>
				<span class="wp-smush-stats-percent">
					<?php echo esc_html( $stats_percent ); ?>
				</span>%
				<span class="wp-smush-stats-sep">/</span>
				<span class="wp-smush-stats-human">
					<?php echo esc_html( $stats_human ); ?>
				</span>
			</span>
		</li>
		<?php if ( apply_filters( 'wp_smush_show_nextgen_lossy_stats', true ) ) : ?>
			<li class="super-smush-attachments">
				<span class="sui-list-label">
					<?php esc_html_e( 'Super-Smushed images', 'wp-smushit' ); ?>
				</span>
				<span class="sui-list-detail wp-smush-stats">
					<?php if ( $lossy_enabled ) : ?>
						<span class="smushed-count"><?php echo count( $super_smushed_count ); ?></span>/<?php echo absint( $total_count ); ?>
					<?php else : ?>
						<span class="sui-tag sui-tag-disabled wp-smush-lossy-disabled">
							<?php esc_html_e( 'Disabled', 'wp-smushit' ); ?>
						</span>
					<?php endif; ?>
				</span>
			</li>
		<?php endif; ?>
	</ul>
</div>
