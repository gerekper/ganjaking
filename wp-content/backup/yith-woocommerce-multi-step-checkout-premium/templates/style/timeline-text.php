<?php
$default = array(
	'prev'    => '#43A08C',
	'current' => '#000000',
	'future'  => '#9B9B9B',
	'hover'   => '#000000',
);
$step_color = get_option( 'yith_wcms_timeline_text_step_color', $default );

?>
<style>
	#checkout_timeline.text li.active .timeline-wrapper .timeline-label,
	#checkout_timeline.text li.active .timeline-wrapper::after {
		color: <?php echo $step_color['current']; ?>
	}

	#checkout_timeline.text li.done .timeline-wrapper .timeline-label,
	#checkout_timeline.text li.done .timeline-wrapper::after{
		color: <?php echo $step_color['prev']; ?>
	}

	#checkout_timeline.text li .timeline-wrapper .timeline-label,
	#checkout_timeline.text li .timeline-wrapper::after {
		color: <?php echo $step_color['future']; ?>
	}

	#checkout_timeline.text li .timeline-wrapper:hover .timeline-label,
	#checkout_timeline.text li .timeline-wrapper:hover::after{
		color: <?php echo $step_color['hover']; ?>
	}
</style>
