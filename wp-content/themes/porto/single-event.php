<?php get_header(); ?>

<?php

global $porto_settings, $porto_layout;
?>

<?php
if ( have_posts() ) :
	the_post();
	$event_start_date = get_post_meta( $post->ID, 'event_start_date', true );
	$event_start_time = get_post_meta( $post->ID, 'event_start_time', true );
	$event_location   = get_post_meta( $post->ID, 'event_location', true );
	$event_count_down = get_post_meta( $post->ID, 'event_time_counter', true );
	$event_link       = get_post_meta( $post->ID, 'event_link', true );

	if ( '' == $event_count_down ) {
		$show_count_down = $porto_settings['event-single-countdown'];
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
<section class="section section-no-border background-color-light m-none">
	<div class="container">
		<div class="row custom-negative-margin-2 m-b-xlg">
			<div class="col-lg-12">
				<div class="custom-event-top-image"> 
				<?php if ( $has_event_date && $show_count_down ) : ?>
					<span class="custom-newcomers-class custom-newcomers-pos-2">
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
string_seconds2="Secs" 
tick_size="desktop:17px;" 
tick_sep_size="desktop:17px;"]'
						);
					?>
					</span> 
				<?php endif; ?>
				<?php
					$thumbnail = get_the_post_thumbnail_url();
					$alt_text  = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
				if ( $thumbnail ) {
					echo '<img src="' . esc_url( $thumbnail ) . '" alt="' . esc_attr( $alt_text ) . '" class="img-responsive custom-border-1 custom-box-shadow" />';
				}
				?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<article class="custom-post-event background-color-light">
				<?php if ( $has_event_date ) : ?>
					<div class="post-event-date background-color-primary center"> 
						<span class="month text-uppercase custom-secondary-font text-color-light"><?php echo esc_html( $event_month_short ); ?></span> 
						<span class="day font-weight-bold text-color-light"><?php echo esc_html( $event_date_numeric ); ?></span> 
						<span class="year text-color-light"><?php echo esc_html( $event_year_numeric ); ?></span> 
					</div>
				<?php endif; ?>
					<div class="post-event-content custom-margin-1 m-b-xlg">
						<?php if ( $has_event_date ) : ?>
							<span class="event-date d-none"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $event_start_date ) ); ?><time><?php echo esc_html( $event_start_time ); ?></time></span>
						<?php endif; ?>
						<h2 class="font-weight-bold text-color-dark m-b-none"><?php the_title(); ?></h2>
						<span class="custom-event-infos">
							<ul class="m-b-md">
							<?php if ( isset( $event_start_time ) && $event_start_time ) : ?>
								<li> <i class="far fa-clock"></i> <?php echo esc_html( $event_start_time ); ?> </li>
							<?php endif; ?>
							<?php if ( isset( $event_location ) && $event_location ) : ?>
								<li class="text-uppercase"> <i class="fas fa-map-marker-alt"></i> <?php echo wp_kses_post( $event_location ); ?></li>
							<?php endif; ?>
							</ul>
						</span>
						<?php the_content(); ?>
						<?php if ( $event_link ) : ?>
							<a href="<?php echo esc_url( $event_link ); ?>" class="btn btn-primary btn-preview" target="_blank"><?php esc_html_e( 'Live Preview', 'porto' ); ?></a>
						<?php endif; ?>
					</div>
				</article>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>
<?php get_footer(); ?>
