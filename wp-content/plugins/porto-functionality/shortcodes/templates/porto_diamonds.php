<?php
$output = $lightbox = $image1_url = $image1_id = $title1 = $zoom_image1_url = $zoom_image1_id = $link1 = $image2_url = $image2_id = $title2 = $zoom_image2_url = $zoom_image2_id = $link2 = $image3_url = $image3_id = $title3 = $zoom_image3_url = $zoom_image3_id = $link3 = $image4_url = $image4_id = $title4 = $zoom_image4_url = $zoom_image4_id = $link4 = $image5_url = $image5_id = $title5 = $zoom_image5_url = $zoom_image5_id = $link5 = $image6_url = $image6_id = $title6 = $zoom_image6_url = $zoom_image6_id = $link6 = $image7_url = $image7_id = $title7 = $zoom_image7_url = $zoom_image7_id = $link7 = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'lightbox'           => true,
			'image1_url'         => '',
			'image1_id'          => '',
			'title1'             => '',
			'zoom_image1_url'    => '',
			'zoom_image1_id'     => '',
			'link1'              => '',
			'image2_url'         => '',
			'image2_id'          => '',
			'title2'             => '',
			'zoom_image2_url'    => '',
			'zoom_image2_id'     => '',
			'link2'              => '',
			'image3_url'         => '',
			'image3_id'          => '',
			'title3'             => '',
			'zoom_image3_url'    => '',
			'zoom_image3_id'     => '',
			'link3'              => '',
			'image4_url'         => '',
			'image4_id'          => '',
			'title4'             => '',
			'zoom_image4_url'    => '',
			'zoom_image4_id'     => '',
			'link4'              => '',
			'image5_url'         => '',
			'image5_id'          => '',
			'title5'             => '',
			'zoom_image5_url'    => '',
			'zoom_image5_id'     => '',
			'link5'              => '',
			'image6_url'         => '',
			'image6_id'          => '',
			'title6'             => '',
			'zoom_image6_url'    => '',
			'zoom_image6_id'     => '',
			'link6'              => '',
			'image7_url'         => '',
			'image7_id'          => '',
			'title7'             => '',
			'zoom_image7_url'    => '',
			'zoom_image7_id'     => '',
			'link7'              => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

if ( ! $image1_url && $image1_id ) {
	$image1_url = wp_get_attachment_url( $image1_id );
}
if ( ! $image2_url && $image2_id ) {
	$image2_url = wp_get_attachment_url( $image2_id );
}
if ( ! $image3_url && $image3_id ) {
	$image3_url = wp_get_attachment_url( $image3_id );
}
if ( ! $image4_url && $image4_id ) {
	$image4_url = wp_get_attachment_url( $image4_id );
}
if ( ! $image5_url && $image5_id ) {
	$image5_url = wp_get_attachment_url( $image5_id );
}
if ( ! $image6_url && $image5_id ) {
	$image6_url = wp_get_attachment_url( $image6_id );
}
if ( ! $image7_url && $image5_id ) {
	$image7_url = wp_get_attachment_url( $image7_id );
}

$lightbox_options = '';
if ( $lightbox ) {
	$lightbox_options                       = array();
	$lightbox_options['delegate']           = '.diamond';
	$lightbox_options['type']               = 'image';
	$lightbox_options['gallery']            = array();
	$lightbox_options['gallery']['enabled'] = true;
	$lightbox_options                       = json_encode( $lightbox_options );
	if ( ! $zoom_image1_url && $zoom_image1_id ) {
		$zoom_image1_url = wp_get_attachment_url( $zoom_image1_id );
	}
	if ( ! $zoom_image1_url ) {
		$zoom_image1_url = $image1_url;
	}
	if ( ! $zoom_image2_url && $zoom_image2_id ) {
		$zoom_image2_url = wp_get_attachment_url( $zoom_image2_id );
	}
	if ( ! $zoom_image2_url ) {
		$zoom_image2_url = $image2_url;
	}
	if ( ! $zoom_image3_url && $zoom_image3_id ) {
		$zoom_image3_url = wp_get_attachment_url( $zoom_image3_id );
	}
	if ( ! $zoom_image3_url ) {
		$zoom_image3_url = $image3_url;
	}
	if ( ! $zoom_image4_url && $zoom_image4_id ) {
		$zoom_image4_url = wp_get_attachment_url( $zoom_image4_id );
	}
	if ( ! $zoom_image4_url ) {
		$zoom_image4_url = $image4_url;
	}
	if ( ! $zoom_image5_url && $zoom_image5_id ) {
		$zoom_image5_url = wp_get_attachment_url( $zoom_image5_id );
	}
	if ( ! $zoom_image5_url ) {
		$zoom_image5_url = $image5_url;
	}
	if ( ! $zoom_image6_url && $zoom_image6_id ) {
		$zoom_image6_url = wp_get_attachment_url( $zoom_image6_id );
	}
	if ( ! $zoom_image6_url ) {
		$zoom_image6_url = $image6_url;
	}
	if ( ! $zoom_image7_url && $zoom_image7_id ) {
		$zoom_image7_url = wp_get_attachment_url( $zoom_image7_id );
	}
	if ( ! $zoom_image7_url ) {
		$zoom_image7_url = $image7_url;
	}
}

if ( $lightbox ) {
	$output .= '<div class="lightbox" data-plugin-options="' . esc_attr( $lightbox_options ) . '">';
}

$output .= '<ul class="porto-diamonds' . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '"';
if ( $animation_type ) {
	$output .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}
$output .= '>';

ob_start();
?>
	<li>
		<a href="<?php echo esc_url( $lightbox ? $zoom_image1_url : $link1 ); ?>" class="diamond" title="<?php echo esc_attr( $title1 ); ?>">
			<div class="content">
				<img src="<?php echo esc_url( $image1_url ); ?>" alt="<?php echo esc_attr( $title1 ); ?>" class="img-responsive">
			</div>
		</a>
	</li>
	<li>
		<a href="<?php echo esc_url( $lightbox ? $zoom_image2_url : $link2 ); ?>" class="diamond" title="<?php echo esc_attr( $title2 ); ?>">
			<div class="content">
				<img src="<?php echo esc_url( $image2_url ); ?>" alt="<?php echo esc_attr( $title2 ); ?>" class="img-responsive">
			</div>
		</a>
	</li>
	<li>
		<a href="<?php echo esc_url( $lightbox ? $zoom_image3_url : $link3 ); ?>" class="diamond" title="<?php echo esc_attr( $title3 ); ?>">
			<div class="content">
				<img src="<?php echo esc_url( $image3_url ); ?>" alt="<?php echo esc_attr( $title3 ); ?>" class="img-responsive">
			</div>
		</a>
	</li>
	<li>
		<a href="<?php echo esc_url( $lightbox ? $zoom_image4_url : $link4 ); ?>" class="diamond diamond-sm" title="<?php echo esc_attr( $title4 ); ?>">
			<div class="content">
				<img src="<?php echo esc_url( $image4_url ); ?>" alt="<?php echo esc_attr( $title4 ); ?>" class="img-responsive">
			</div>
		</a>
	</li>
	<li>
		<a href="<?php echo esc_url( $lightbox ? $zoom_image5_url : $link5 ); ?>" class="diamond" title="<?php echo esc_attr( $title5 ); ?>">
			<div class="content">
				<img src="<?php echo esc_url( $image5_url ); ?>" alt="<?php echo esc_attr( $title6 ); ?>" class="img-responsive">
			</div>
		</a>
	</li>
	<li>
		<a href="<?php echo esc_url( $lightbox ? $zoom_image6_url : $link6 ); ?>" class="diamond diamond-sm" title="<?php echo esc_attr( $title6 ); ?>">
			<div class="content">
				<img src="<?php echo esc_url( $image6_url ); ?>" alt="<?php echo esc_attr( $title6 ); ?>" class="img-responsive">
			</div>
		</a>
	</li>
	<li>
		<a href="<?php echo esc_url( $lightbox ? $zoom_image7_url : $link7 ); ?>" class="diamond diamond-sm" title="<?php echo esc_attr( $title7 ); ?>">
			<div class="content">
				<img src="<?php echo esc_url( $image7_url ); ?>" alt="<?php echo esc_attr( $title7 ); ?>" class="img-responsive">
			</div>
		</a>
	</li>
<?php

$output .= ob_get_clean();

$output .= '</ul>';

if ( $lightbox ) {
	$output .= '</div>';
}

echo porto_filter_output( $output );
