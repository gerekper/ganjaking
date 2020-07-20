<?php

$default_step_text_color = array(
	'prev'    => '#3ABFA3',
	'current' => '#4b4b4b',
	'future'  => '#C1C1C1',
	'hover'   => '#4b4b4b'
);

$step_text_color = get_option( 'yith_wcms_timeline_style4_step_text_color', $default_step_text_color );
$step_border_color = get_option( 'yith_wcms_timeline_style4_step_border_color', '#707070' );
?>
<style>
	#checkout_timeline.style4 li .timeline-wrapper .timeline-label {
		color: <?php echo $step_text_color['future']; ?>
	}

	#checkout_timeline.style4 li.done .timeline-wrapper .timeline-label{
		color: <?php echo $step_text_color['prev']; ?>
	}

	#checkout_timeline.style4 li.active .timeline-wrapper .timeline-label{
		color: <?php echo $step_text_color['current']; ?>
	}

	#checkout_timeline.style4 li .timeline-wrapper:hover .timeline-label{
		color: <?php echo $step_text_color['hover']; ?>
	}

	/* Icons */
	#checkout_timeline.style4.horizontal .yith-wcms-icon{
		width: <?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_style4_horizontal']['width'];?>px;
		height: <?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_style4_horizontal']['height'];?>px;
	}

	#checkout_timeline.style4.vertical .yith-wcms-icon,
	#checkout_timeline.style4.yith-is-mobile .yith-wcms-icon{
		width: <?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_style4_vertical']['width'];?>px;
		height: <?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_style4_vertical']['height'];?>px;
	}

	#checkout_timeline.style4 li .timeline-wrapper .yith-wcms-icon{
		fill: <?php echo $step_text_color['future']; ?>
	}

	#checkout_timeline.style4 li.done .timeline-wrapper .yith-wcms-icon{
		fill: <?php echo $step_text_color['prev']; ?>
	}

	#checkout_timeline.style4 li.active .timeline-wrapper .yith-wcms-icon{
		fill: <?php echo $step_text_color['current']; ?>
	}

	#checkout_timeline.style4 li .timeline-wrapper:hover .timeline-step .yith-wcms-icon{
		fill: <?php echo $step_text_color['hover']; ?>
	}

	#checkout_timeline.style4 li .timeline-wrapper .timeline-step.with-icon:after {
		color: <?php echo $step_text_color['prev']; ?>
	}

	#checkout_timeline.style4 li .timeline-wrapper:hover .timeline-step.with-icon:after {
		color: <?php echo $step_text_color['hover']; ?>
	}

	/* Step Separator */
	#checkout_timeline.style4.horizontal li:not(:last-child) .timeline-wrapper::after{
		background-color: <?php echo $step_border_color; ?>
	}
</style>
