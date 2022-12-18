<?php

global $porto_settings, $porto_layout;
?>

<?php
	$event_start_date = get_post_meta( $post->ID, 'event_start_date', true );
	$event_start_time = get_post_meta( $post->ID, 'event_start_time', true );
	$event_location   = get_post_meta( $post->ID, 'event_location', true );
	$event_count_down = get_post_meta( $post->ID, 'event_time_counter', true );

if ( empty( $event_count_down ) ) {
	$show_count_down = isset( $porto_settings['event-single-countdown'] ) ? $porto_settings['event-single-countdown'] : true;
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

<article class="custom-post-event post-event-list custom-sm-margin-bottom-2">
	<?php if ( $has_event_date ) : ?>
		<div class="post-event-date background-color-primary center"> 
			<span class="month text-uppercase custom-secondary-font text-color-light"><?php echo esc_html( $event_month_short ); ?></span> 
			<span class="day font-weight-bold text-color-light"><?php echo esc_html( $event_date_numeric ); ?></span> 
			<span class="year text-color-light"><?php echo esc_html( $event_year_numeric ); ?></span> 
		</div>
	<?php endif; ?>
	<div class="post-event-content custom-margin-1">
	<?php if ( $has_event_date ) : ?>
		<span class="event-date d-none"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $event_start_date ) ); ?><time datetime="<?php echo date( 'Y-m-d H:i', strtotime( $event_start_date . ' ' . $event_start_time ) ); ?>"><?php echo esc_html( $event_start_date . ' ' . $event_start_time ); ?></time></span>
	<?php endif; ?>
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
		<h4 class="font-weight-bold text-color-dark"> <a href="<?php the_permalink(); ?>" class="text-decoration-none custom-secondary-font text-color-dark"> <?php the_title(); ?> </a> </h4>
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
	<hr class="solid">
</article>
