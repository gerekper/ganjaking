<?php
extract(
	shortcode_atts(
		array(
			'el_class' => '',
		),
		$atts
	)
);

porto_woocommerce_output_horizontal_filter();
