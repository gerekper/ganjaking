<?php
$default_color_schema_1 = array(
	'prev'    => '#ffffff',
	'current' => '#ffffff',
	'future'  => '#ffffff',
	'hover'   => '#ffffff',
);

$step_background_color = get_option( 'yith_wcms_timeline_style2_step_background_color', $default_color_schema_1 );
$circle_text_color       = get_option( 'yith_wcms_timeline_style2_circle_text_color', $default_color_schema_1 );

$default_color_schema_2 = array(
	'prev'    => '#3ABFA3',
	'current' => '#535353',
	'future'  => '#c1c1c1',
	'hover'   => '#535353',
);

$step_text_color         = get_option( 'yith_wcms_timeline_style2_step_text_color', $default_color_schema_2 );
$step_border_color       = get_option( 'yith_wcms_timeline_style2_step_border_color', $default_color_schema_2 );
$circle_background_color = get_option( 'yith_wcms_timeline_style2_circle_background_color', $default_color_schema_2 );
$circle_border_color     = get_option( 'yith_wcms_timeline_style2_circle_border_color', $default_color_schema_2 );
$text_alignment          = get_option( 'yith_wcms_timeline_style2_step_text_alignment', 'left' );

?>
<style>
    #checkout_timeline.style2 li .timeline-wrapper {
        background-color: <?php echo $step_background_color['future'] ?>;
		border-color: <?php echo $step_border_color['future'] ?>;
    }

	#checkout_timeline.style2 li .timeline-wrapper .timeline-label{
		color: <?php echo $step_text_color['future'] ?>;
		text-align: <?php echo $text_alignment ?>;
	}

	#checkout_timeline.style2 li .timeline-wrapper .timeline-step{
		background-color: <?php echo $circle_background_color['future'] ?>;
		color: <?php echo $circle_text_color['future'] ?>;
		border-color: <?php echo $circle_border_color['future'] ?>;
	}

	#checkout_timeline.style2 li.done .timeline-wrapper {
		background-color: <?php echo $step_background_color['prev'] ?>;
		border-color: <?php echo $step_border_color['prev'] ?>;
	}

	#checkout_timeline.style2.horizontal li.done .timeline-wrapper {
		box-shadow: 0 6px 10px <?php echo $step_border_color['prev'] ?>3d;
	}

	#checkout_timeline.style2 li.done .timeline-wrapper .timeline-label{
		color: <?php echo $step_text_color['prev'] ?>;
	}

	#checkout_timeline.style2 li.done .timeline-wrapper .timeline-step{
		background-color: <?php echo $circle_background_color['prev'] ?>;
		color: <?php echo $circle_text_color['prev'] ?>;
		border-color: <?php echo $circle_border_color['prev'] ?>;
	}

	#checkout_timeline.style2 li.active .timeline-wrapper {
		background-color: <?php echo $step_background_color['current'] ?>;
		border-color: <?php echo $step_border_color['current'] ?>;
	}

	#checkout_timeline.style2 li.active .timeline-wrapper .timeline-label{
		color: <?php echo $step_text_color['current'] ?>;
	}

	#checkout_timeline.style2 li.active .timeline-wrapper .timeline-step{
		background-color: <?php echo $circle_background_color['current'] ?>;
		color: <?php echo $circle_text_color['current'] ?>;
		border-color: <?php echo $circle_border_color['current'] ?>;
	}

	#checkout_timeline.style2 li:hover .timeline-wrapper {
		background-color: <?php echo $step_background_color['hover'] ?>;
		border-color: <?php echo $step_border_color['hover'] ?>;
	}

	#checkout_timeline.style2 li:hover .timeline-wrapper .timeline-label{
		color: <?php echo $step_text_color['hover'] ?>;
	}

	#checkout_timeline.style2 li:hover .timeline-wrapper .timeline-step{
		background-color: <?php echo $circle_background_color['hover'] ?>;
		color: <?php echo $circle_text_color['hover'] ?>;
		border-color: <?php echo $circle_border_color['hover'] ?>;
	}

	#checkout_timeline.vertical.style2 li{
		border-color: <?php echo $step_border_color['future'] ?>;
	}

	#checkout_timeline.vertical.style2 li.done{
		border-color: <?php echo $step_border_color['prev'] ?>;
		box-shadow: 0 6px 10px <?php echo $step_border_color['prev'] ?>3d;
	}

	#checkout_timeline.vertical.style2 li.active{
		border-color: <?php echo $step_border_color['current'] ?>;
	}

	#checkout_timeline.vertical.style2 li:hover{
		border-color: <?php echo $step_border_color['hover'] ?>;
	}

	/* Icons */
	#checkout_timeline.style2 .yith-wcms-icon{
		width: <?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_style2']['width'];?>px;
		height: <?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_style2']['height'];?>px;
	}

	#checkout_timeline.style2 li .yith-wcms-icon{
		fill: <?php echo $circle_text_color['future']; ?>
	}

	#checkout_timeline.style2 li.active .yith-wcms-icon{
		fill: <?php echo $circle_text_color['current']; ?>
	}

	#checkout_timeline.style2 li.done .yith-wcms-icon{
		fill: <?php echo $circle_text_color['prev']; ?>
	}

	#checkout_timeline.style2 li:hover .yith-wcms-icon{
		fill: <?php echo $circle_text_color['hover']; ?>
	}
</style>
