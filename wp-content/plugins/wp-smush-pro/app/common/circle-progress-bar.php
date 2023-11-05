<?php
/**
 * @var string    $percent_grade            Circle grade class.
 * @var int|float $percent_metric           Metric to calculate circle score.
 * @var int       $percent_optimized        Percent optimized.
 * @var string    $progressbar_description  Progressbar description.
 */
if ( ! isset( $progressbar_description ) ) {
	$progressbar_description = __( 'Images optimized in the media library', 'wp-smushit' );
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
	<small><?php echo esc_html( $progressbar_description ); ?></small>
</div>