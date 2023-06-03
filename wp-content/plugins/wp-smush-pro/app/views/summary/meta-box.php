<?php
/**
 * Summary meta box on dashboard page.
 *
 * @package WP_Smush
 *
 * @var string     $human_bytes
 * @var int        $remaining
 * @var int        $resize_count
 * @var bool       $resize_enabled
 * @var int        $resize_savings
 * @var string|int $stats_percent
 * @var int        $total_optimized
 * @var string     $percent_grade
 * @var int|float  $percent_metric
 * @var int        $percent_optimized
 *
 * @var Smush\App\Abstract_Page $this  Page.
 */

use Smush\Core\Settings;

if ( ! defined( 'WPINC' ) ) {
	die;
}

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


<div class="sui-summary-segment">
	<ul class="sui-list smush-stats-list">
		<li class="smush-resize-savings">
			<span class="sui-list-label">
				<?php esc_html_e( 'Image Resize Savings', 'wp-smushit' ); ?>
				<?php if ( ! $resize_enabled && $resize_savings <= 0 ) : ?>
					<p class="wp-smush-stats-label-message sui-hidden-sm sui-hidden-md sui-hidden-lg">
						<?php
						$settings_link = '#';
						$link_class    = 'wp-smush-resize-enable';

						if ( Settings::can_access( 'bulk' ) && 'smush-bulk' !== $this->get_slug() ) {
							$settings_link = $this->get_url( 'smush-bulk' ) . '#enable-resize';
							$link_class    = '';
						}

						printf(
							/* translators: %1$1s - opening <a> tag, %2$2s - closing <a> tag */
							esc_html__( 'Save a ton of space by not storing over-sized images on your server. %1$1sEnable image resizing%2$2s', 'wp-smushit' ),
							'<a role="button" class="' . esc_attr( $link_class ) . '" href="' . esc_url( $settings_link ) . '">',
							'</a>'
						);
						?>
					</p>
				<?php endif; ?>
			</span>
			<span class="sui-list-detail wp-smush-stats">
				<?php if ( $resize_enabled || $resize_savings > 0 ) : ?>
					<?php echo $resize_savings > 0 ? esc_html( $resize_savings ) : esc_html__( 'No resize savings', 'wp-smushit' ); ?>
				<?php else : ?>
					<a role="button" class="sui-hidden-xs <?php echo esc_attr( $link_class ); ?>" href="<?php echo esc_url( $settings_link ); ?>">
						<?php esc_html_e( 'Resize images', 'wp-smushit' ); ?>
					</a>
				<?php endif; ?>
			</span>
		</li>
		<?php
		/**
		 * Allows to output Directory Smush stats
		 */
		do_action( 'stats_ui_after_resize_savings' );
		?>
	</ul>
</div>