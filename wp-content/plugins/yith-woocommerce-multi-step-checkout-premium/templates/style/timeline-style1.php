<?php
$step_background_color_default = array(
	'prev'    => '#40bfa4',
	'current' => '#777777',
	'future'  => '#c1c1c1',
	'hover'   => '#777777',
);

$step_background_color = get_option( 'yith_wcms_timeline_style1_step_background_color', $step_background_color_default );

$step_text_color_default = array(
	'prev'    => '#ffffff',
	'current' => '#ffffff',
	'future'  => '#ffffff',
	'hover'   => '#ffffff',
);

$step_text_color = get_option( 'yith_wcms_timeline_style1_step_text_color', $step_text_color_default );

$square_background_color_default = array(
	'prev'    => '#43a08c',
	'current' => '#5c5c5c',
	'future'  => '#aaaaaa',
	'hover'   => '#5c5c5c',
);

$square_background_color = get_option( 'yith_wcms_timeline_style1_square_background_color', $square_background_color_default );

$square_text_color_default = array(
	'prev'    => '#ffffff',
	'current' => '#ffffff',
	'future'  => '#ffffff',
	'hover'   => '#ffffff',
);

$square_text_color = get_option( 'yith_wcms_timeline_style1_square_text_color', $square_text_color_default );
$text_alignment    = get_option( 'yith_wcms_timeline_style1_step_text_alignment', 'left' );
?>
<style>
	/* Step Background Color */
	#checkout_timeline.style1 li .timeline-wrapper {
        background-color: <?php echo $step_background_color['future']; ?>;
    }

	#checkout_timeline.style1 li.active .timeline-wrapper {
		background-color: <?php echo $step_background_color['current']; ?>;
	}

	#checkout_timeline.style1 li.done .timeline-wrapper {
		background-color: <?php echo $step_background_color['prev']; ?>;
	}

	#checkout_timeline.style1 li:not( .active ) .timeline-wrapper:hover {
		background-color: <?php echo $step_background_color['hover']; ?>;
	}

	/* Step Text Color */
	#checkout_timeline.style1 li .timeline-wrapper .timeline-label {
		color: <?php echo $step_text_color['future']; ?>;
		text-align: <?php echo $text_alignment ?>;
	}

	#checkout_timeline.style1 li.active .timeline-wrapper .timeline-label {
		color: <?php echo $step_text_color['current']; ?>;
	}

	#checkout_timeline.style1 li:not( .active ) .timeline-wrapper:hover .timeline-label,
	#checkout_timeline.style1 li.done .timeline-wrapper:hover .timeline-label {
		color: <?php echo $step_text_color['hover']; ?>;
	}

	#checkout_timeline.style1 li.done .timeline-wrapper .timeline-label{
		color: <?php echo $step_text_color['prev']; ?>;
	}

	/* Square Background and Text Color */
	#checkout_timeline.style1 li .timeline-wrapper .timeline-step{
		background-color: <?php echo $square_background_color['future']; ?>;
		color: <?php echo $square_text_color['future']; ?>;
	}

	#checkout_timeline.style1 li.active .timeline-wrapper .timeline-step{
		background-color: <?php echo $square_background_color['current']; ?>;
		color: <?php echo $square_text_color['current']; ?>;
	}

	#checkout_timeline.style1 li:not( .active ) .timeline-wrapper:hover .timeline-step{
		background-color: <?php echo $square_background_color['hover']; ?>;
		color: <?php echo $square_text_color['hover']; ?>;
	}

	#checkout_timeline.style1 li.done .timeline-wrapper .timeline-step{
		background-color: <?php echo $square_background_color['prev']; ?>;
		color: <?php echo $square_text_color['prev']; ?>;
	}

	/* Icons */
	#checkout_timeline.style1 .yith-wcms-icon{
		width: <?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_style1']['width'];?>px;
		height: <?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_style1']['height'];?>px;
	}

	#checkout_timeline.style1 .timeline-wrapper .timeline-step .yith-wcms-icon{
		fill: <?php echo $square_text_color['future']; ?>;
	}

	#checkout_timeline.style1 li.active .timeline-wrapper .timeline-step .yith-wcms-icon{
		fill: <?php echo $square_text_color['current']; ?>;
	}

	#checkout_timeline.style1 li.done .timeline-wrapper .timeline-step .yith-wcms-icon{
		fill: <?php echo $square_text_color['prev']; ?>;
	}

	#checkout_timeline.style1 li:not( .active ) .timeline-wrapper:hover .timeline-step .yith-wcms-icon{
		fill: <?php echo $square_text_color['hover']; ?>;
	}
</style>
