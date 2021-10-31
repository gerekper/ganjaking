<?php
$output = $title1 = $link1 = $image1_url = $image1_id = $title1 = $link2 = $image2_url = $image2_id = $title3 = $link3 = $image3_url = $image3_id = $title4 = $slide_link1 = $slide_image1_url = $slide_image1_id = $slide_link2 = $slide_image2_url = $slide_image2_id = $slide_link3 = $slide_image3_url = $slide_image3_id = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'title1'             => '',
			'link1'              => '',
			'image1_url'         => '',
			'image1_id'          => '',
			'title2'             => '',
			'link2'              => '',
			'image2_url'         => '',
			'image2_id'          => '',
			'title3'             => '',
			'link3'              => '',
			'image3_url'         => '',
			'image3_id'          => '',
			'title4'             => '',
			'slide_link1'        => '',
			'slide_image1_url'   => '',
			'slide_image1_id'    => '',
			'slide_link2'        => '',
			'slide_image2_url'   => '',
			'slide_image2_id'    => '',
			'slide_link3'        => '',
			'slide_image3_url'   => '',
			'slide_image3_id'    => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

wp_enqueue_script( 'modernizr' );
wp_enqueue_script( 'jquery-flipshow' );
wp_enqueue_script( 'porto_shortcodes_flipshow_loader_js' );

$el_class = porto_shortcode_extract_class( $el_class );

$alt_text_image1 = $alt_text_image2 = $alt_text_image3 = $alt_text_slide_image1 = $alt_text_slide_image2 = $alt_text_slide_image3 = '';

if ( ! $image1_url && $image1_id ) {
	$image1_url      = wp_get_attachment_url( $image1_id );
	$alt_text_image1 = get_post_meta( $image1_id, '_wp_attachment_image_alt', true );
}

if ( ! $image2_url && $image2_id ) {
	$image2_url      = wp_get_attachment_url( $image2_id );
	$alt_text_image2 = get_post_meta( $image2_id, '_wp_attachment_image_alt', true );
}

if ( ! $image3_url && $image3_id ) {
	$image3_url      = wp_get_attachment_url( $image3_id );
	$alt_text_image3 = get_post_meta( $image3_id, '_wp_attachment_image_alt', true );
}

if ( ! $slide_image1_url && $slide_image1_id ) {
	$slide_image1_url      = wp_get_attachment_url( $slide_image1_id );
	$alt_text_slide_image1 = get_post_meta( $slide_image1_id, '_wp_attachment_image_alt', true );
}

if ( ! $slide_image2_url && $slide_image2_id ) {
	$slide_image2_url      = wp_get_attachment_url( $slide_image2_id );
	$alt_text_slide_image2 = get_post_meta( $slide_image2_id, '_wp_attachment_image_alt', true );
}

if ( ! $slide_image3_url && $slide_image3_id ) {
	$slide_image3_url      = wp_get_attachment_url( $slide_image3_id );
	$alt_text_slide_image3 = get_post_meta( $slide_image3_id, '_wp_attachment_image_alt', true );
}

$output .= '<div class="porto-concept wpb_content_element ' . esc_attr( $el_class ) . '"';
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
<div class="container">
	<div class="row center">
		<span class="sun"></span>
		<span class="cloud"></span>
		<div class="col-lg-2 offset-lg-1">
			<div class="process-image" data-appear-animation="bounceIn">
				<?php if ( $link1 ) : ?>
					<a href="<?php echo esc_url( $link1 ); ?>">
				<?php endif; ?>
					<?php if ( $image1_url ) : ?>
						<img src="<?php echo esc_url( str_replace( array( 'http:', 'https:' ), '', $image1_url ) ); ?>" alt="<?php echo esc_attr( $alt_text_image1 ); ?>" />
					<?php endif; ?>
				<?php if ( $link1 ) : ?>
					</a>
				<?php endif; ?>
				<?php if ( $title1 ) : ?>
					<strong><?php echo porto_strip_script_tags( $title1 ); ?></strong>
				<?php endif; ?>
			</div>
		</div>
		<div class="col-lg-2">
			<div class="process-image" data-appear-animation="bounceIn" data-appear-animation-delay="200">
				<?php if ( $link2 ) : ?>
					<a href="<?php echo esc_url( $link2 ); ?>">
				<?php endif; ?>
					<?php if ( $image2_url ) : ?>
						<img src="<?php echo esc_url( str_replace( array( 'http:', 'https:' ), '', $image2_url ) ); ?>" alt="<?php echo esc_attr( $alt_text_image2 ); ?>" />
					<?php endif; ?>
				<?php if ( $link2 ) : ?>
					</a>
				<?php endif; ?>
				<?php if ( $title2 ) : ?>
					<strong><?php echo porto_strip_script_tags( $title2 ); ?></strong>
				<?php endif; ?>
			</div>
		</div>
		<div class="col-lg-2">
			<div class="process-image" data-appear-animation="bounceIn" data-appear-animation-delay="400">
				<?php if ( $link3 ) : ?>
					<a href="<?php echo esc_url( $link3 ); ?>">
				<?php endif; ?>
					<?php if ( $image3_url ) : ?>
						<img src="<?php echo esc_url( str_replace( array( 'http:', 'https:' ), '', $image3_url ) ); ?>" alt="<?php echo esc_attr( $alt_text_image3 ); ?>" />
					<?php endif; ?>
				<?php if ( $link3 ) : ?>
					</a>
				<?php endif; ?>
				<?php if ( $title3 ) : ?>
					<strong><?php echo porto_strip_script_tags( $title3 ); ?></strong>
				<?php endif; ?>
			</div>
		</div>
		<div class="col-lg-4 offset-lg-1">
			<div class="project-image">
				<div class="concept-slideshow fc-slideshow">
					<ul class="fc-slides">
						<?php if ( $slide_image1_url ) : ?>
							<li>
								<?php
								if ( $slide_link1 ) :
									?>
									<a href="<?php echo esc_url( $slide_link1 ); ?>"><?php endif; ?>
									<img class="img-responsive" src="<?php echo esc_url( str_replace( array( 'http:', 'https:' ), '', $slide_image1_url ) ); ?>" alt="<?php echo esc_attr( $alt_text_slide_image1 ); ?>" />
								<?php
								if ( $slide_link1 ) :
									?>
									</a><?php endif; ?>
							</li>
						<?php endif; ?>
						<?php if ( $slide_image2_url ) : ?>
							<li>
								<?php
								if ( $slide_link2 ) :
									?>
									<a href="<?php echo esc_url( $slide_link2 ); ?>"><?php endif; ?>
									<img class="img-responsive" src="<?php echo esc_url( str_replace( array( 'http:', 'https:' ), '', $slide_image2_url ) ); ?>" alt="<?php echo esc_attr( $alt_text_slide_image2 ); ?>" />
								<?php
								if ( $slide_link2 ) :
									?>
									</a><?php endif; ?>
							</li>
						<?php endif; ?>
						<?php if ( $slide_image3_url ) : ?>
							<li>
								<?php
								if ( $slide_link3 ) :
									?>
									<a href="<?php echo esc_url( $slide_link3 ); ?>"><?php endif; ?>
									<img class="img-responsive" src="<?php echo esc_url( str_replace( array( 'http:', 'https:' ), '', $slide_image3_url ) ); ?>" alt="<?php echo esc_attr( $alt_text_slide_image3 ); ?>" />
								<?php
								if ( $slide_link3 ) :
									?>
									</a><?php endif; ?>
							</li>
						<?php endif; ?>
					</ul>
				</div>
				<?php
				if ( $title4 ) :
					?>
					<strong class="our-work"><?php echo porto_strip_script_tags( $title4 ); ?></strong>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php

$output .= ob_get_clean();

$output .= '</div>';

echo porto_filter_output( $output );
