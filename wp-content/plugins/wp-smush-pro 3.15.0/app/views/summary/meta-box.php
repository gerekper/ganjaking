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
		<?php
		/**
		 * Allows to output Directory Smush stats
		 */
		do_action( 'stats_ui_after_resize_savings' );
		?>
	</ul>
</div>