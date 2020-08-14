<?php
$step_background_color_default = array(
	'prev'    => '#3ABFA3',
	'current' => '#4b4b4b',
	'future'  => '#ffffff',
	'hover'   => '#4b4b4b',
);

$step_background_color = get_option( 'yith_wcms_timeline_style3_step_background_color', $step_background_color_default );

$step_text_color_default = array(
	'prev'    => '#ffffff',
	'current' => '#ffffff',
	'future'  => '#c1c1c1',
	'hover'   => '#ffffff',
);

$step_text_color = get_option( 'yith_wcms_timeline_style3_step_text_color', $step_text_color_default );

$step_border_color_default = array(
	'prev'    => '#3ABFA3',
	'current' => '#4b4b4b',
	'future'  => '#C1C1C1',
	'hover'   => '#4b4b4b',
);

$step_border_color = get_option( 'yith_wcms_timeline_style3_step_border_color', $step_border_color_default );
$text_alignment    = get_option( 'yith_wcms_timeline_style3_step_text_alignment', 'left' );
?>

<style>
	/* Step Background Color */
	#checkout_timeline.style3 li .timeline-wrapper {
		background-color: <?php echo $step_background_color['future']; ?>;
	}

	#checkout_timeline.style3 li.active .timeline-wrapper {
		background-color: <?php echo $step_background_color['current']; ?>;
	}

	#checkout_timeline.style3 li.done .timeline-wrapper {
		background-color: <?php echo $step_background_color['prev']; ?>;
	}

	#checkout_timeline.style3 li .timeline-wrapper:hover {
		background-color: <?php echo $step_background_color['hover']; ?>;
	}

	/* Step Text Background Color */
	#checkout_timeline.style3 li .timeline-wrapper .timeline-step,
	#checkout_timeline.style3 li .timeline-wrapper .timeline-label {
		color: <?php echo $step_text_color['future']; ?>;
	}

	#checkout_timeline.style3 li .timeline-wrapper .timeline-label {
		text-align: <?php echo $text_alignment ?>;
	}

	#checkout_timeline.style3 li.active .timeline-wrapper .timeline-step,
	#checkout_timeline.style3 li.active .timeline-wrapper .timeline-label {
		color: <?php echo $step_text_color['current']; ?>;
	}

	#checkout_timeline.style3 li.done .timeline-wrapper .timeline-step,
	#checkout_timeline.style3 li.done .timeline-wrapper .timeline-label {
		color: <?php echo $step_text_color['prev']; ?>;
	}

	#checkout_timeline.style3 li .timeline-wrapper:hover .timeline-step,
	#checkout_timeline.style3 li .timeline-wrapper:hover .timeline-label {
		color: <?php echo $step_text_color['hover']; ?>;
	}

	/* Step Border Color */
	#checkout_timeline.style3 li .timeline-wrapper {
		border-color: <?php echo $step_border_color['future']; ?>;
	}

	#checkout_timeline.style3 li.active .timeline-wrapper {
		border-color: <?php echo $step_border_color['current']; ?>;
	}

	#checkout_timeline.style3 li.done .timeline-wrapper {
		border-color: <?php echo $step_border_color['prev']; ?>;
	}

	#checkout_timeline.style3 li .timeline-wrapper:hover {
		border-color: <?php echo $step_border_color['hover']; ?>;
	}

	#checkout_timeline.style3 li.done .timeline-wrapper {
		box-shadow: 0 6px 10px <?php echo $step_border_color['prev'] ?>3d;
	}

	/* Icons */
	#checkout_timeline.style3 .yith-wcms-icon{
		margin-right: 5px;
		width: <?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_style3']['width'];?>px;
		height: <?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_style3']['height'];?>px;
	}

	#checkout_timeline.style3 li .yith-wcms-icon{
		fill: <?php echo $step_text_color['future']; ?>
	}

	#checkout_timeline.style3 li.active .yith-wcms-icon{
		fill: <?php echo $step_text_color['current']; ?>
	}

	#checkout_timeline.style3 li.done .yith-wcms-icon{
		fill: <?php echo $step_text_color['prev']; ?>
	}

	#checkout_timeline.style3 li:hover .yith-wcms-icon{
		fill: <?php echo $step_text_color['hover']; ?>
	}
</style>
