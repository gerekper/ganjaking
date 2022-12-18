<?php
	global $porto_settings, $porto_layout, $event_countdown_vc;

	$event_start_date = get_post_meta( $post->ID, 'event_start_date', true );
	$event_start_time = get_post_meta( $post->ID, 'event_start_time', true );
	$event_location   = get_post_meta( $post->ID, 'event_location', true );
	$event_count_down = get_post_meta( $post->ID, 'event_time_counter', true );

if ( isset( $event_countdown_vc ) && $event_countdown_vc ) {
	$event_count_down = $event_countdown_vc;
}

if ( empty( $event_count_down ) ) {
	$show_count_down = isset( $porto_settings['event-archive-countdown'] ) ? $porto_settings['event-archive-countdown'] : true;
} elseif ( 'show' == $event_count_down ) {
	$show_count_down = true;
} else {
	$show_count_down = false;
}

if ( isset( $event_start_date ) && $event_start_date ) {
	$has_event_date   = true;
	$event_date_parts = explode( '/', $event_start_date );

	if ( isset( $event_date_parts ) && count( $event_date_parts ) == 3 ) {
		$has_event_date      = true;
		$event_year_numeric  = isset( $event_date_parts[0] ) ? trim( $event_date_parts[0] ) : '';
		$event_month_numeric = isset( $event_date_parts[1] ) ? trim( $event_date_parts[1] ) : '';
		$event_date_numeric  = isset( $event_date_parts[2] ) ? trim( $event_date_parts[2] ) : '';
		$event_month_short   = date( 'M', mktime( 0, 0, 0, $event_month_numeric, 1 ) );
	} else {
		$has_event_date = false;
	}
} else {
		$has_event_date = false;
}

if ( isset( $event_start_time ) && $event_start_time ) {
	$event_time_js_format = date( 'H:i', strtotime( $event_start_time ) );
} else {
	$event_time_js_format = '00:00:00';
}
?>

<!--<h2 class="text-color-dark font-weight-bold">Next Event</h2>-->
<article class="thumb-info custom-thumb-info custom-box-shadow m-b-md<?php echo empty( $post_classes ) ? '' : ' ' . esc_attr( trim( $post_classes ) ); ?>"> 
	<?php
	$thumbnail = get_the_post_thumbnail_url();
	$image_alt = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
	if ( $thumbnail ) :
		?>
		<span class="thumb-info-wrapper"> <a href="<?php the_permalink(); ?>"> 
			<img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" class="img-responsive" /> </a> 
		</span> 
	<?php endif; ?>
	<div class="thumb-info-caption"> 
		<div class="custom-thumb-info-wrapper-box center"> 
			<?php if ( $has_event_date && $show_count_down ) : ?>
					<?php
					echo do_shortcode(
						'[porto_countdown 
datetime="' . $event_start_date . ' ' . $event_time_js_format . '" 
countdown_opts="sday,shr,smin,ssec" 
tick_col="#da7940" 
tick_style="bold" 
tick_sep_col="#2e353e" 
tick_sep_style="bold" 
el_class="m-b-none custom-newcomers-class" 
string_hours="Hr" 
string_hours2="Hrs" 
string_minutes="Min" 
string_minutes2="Mins" 
string_seconds="Sec" 
string_seconds2="Secs"]'
					);
					?>
			<?php endif; ?>
		</div> 
		<div class="custom-event-infos">
			<ul>
				<?php if ( isset( $event_start_time ) && $event_start_time ) : ?>
					<li> <i class="far fa-clock"></i> <?php echo esc_html( $event_start_time ); ?> </li>
				<?php endif; ?>
				<?php if ( isset( $event_location ) && $event_location ) : ?>
					<li class="text-uppercase"> <i class="fas fa-map-marker-alt"></i> <?php echo porto_strip_script_tags( $event_location ); ?></li>
				<?php endif; ?>
			</ul>
		</div>

		<div class="thumb-info-caption-text">
			<span class="event-date d-none"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $event_start_date ) ); ?></span>
			<h4 class="porto-post-title"> <a href="<?php the_permalink(); ?>"> <?php the_title(); ?> </a> </h4>
			<?php
			if ( ! empty( $porto_settings['event-excerpt'] ) ) {
				echo porto_get_excerpt( $porto_settings['event-excerpt-length'], false );
			} else {
				porto_the_content();
			}
			?>
			<?php if ( isset( $porto_settings['event-readmore'] ) && $porto_settings['event-readmore'] ) : ?>
				<?php /* translators: $1: Event Singular Name */ ?>
				<div><a class="read-more" href="<?php the_permalink(); ?>"><?php printf( esc_html__( 'View %s', 'porto' ), $porto_settings['event-singular-name'] ? esc_html( $porto_settings['event-singular-name'] ) : esc_html__( 'Event', 'porto' ) ); ?></a></div>
			<?php endif; ?>
		</div>
	</div>
</article>
