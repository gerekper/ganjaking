<?php
/**
 * @var string      $human_bytes
 * @var int         $resize_count       Number of resizes images.
 * @var int         $total_optimized    Total nubmer of images optimized.
 * @var string|int  $stats_percent
 */

?>
<div class="sui-summary-segment">
	<div class="sui-summary-details">
		<span class="sui-summary-large wp-smush-stats-human">
			<?php echo esc_html( $human_bytes ); ?>
		</span>
		<span class="sui-summary-detail wp-smush-savings">
			/<span class="wp-smush-stats-percent"><?php echo esc_html( $stats_percent ); ?></span>%		</span>
		<span class="sui-summary-sub">
			<?php esc_html_e( 'Total Savings', 'wp-smushit' ); ?>
		</span>
		<span class="smushed-items-count">
			<span class="wp-smush-count-total">
				<span class="sui-summary-detail wp-smush-total-optimised">
					<?php echo esc_html( $total_optimized ); ?>
				</span>
				<span class="sui-summary-sub">
					<?php esc_html_e( 'Images Smushed', 'wp-smushit' ); ?>
				</span>
			</span>
			<span class="wp-smush-count-resize-total <?php echo $resize_count > 0 ? '' : 'sui-hidden'; ?>">
				<span class="sui-summary-detail wp-smush-total-optimised">
					<?php echo esc_html( $resize_count ); ?>
				</span>
				<span class="sui-summary-sub">
					<?php esc_html_e( 'Images Resized', 'wp-smushit' ); ?>
				</span>
			</span>
		</span>
	</div>
</div>