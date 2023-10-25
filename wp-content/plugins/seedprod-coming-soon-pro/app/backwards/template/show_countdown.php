<?php
if ( ! empty( $settings->enable_countdown ) ) :
	?>

			<?php
			// Calulate offset
			date_default_timezone_set( 'UTC' );
			$dt = getdate( strtotime( $settings->countdown_date . ' UTC' ) );

			$tz = $settings->countdown_timezone;//get_option('timezone_string');
			if ( ! empty( $tz ) ) {
				date_default_timezone_set( $tz );
				$now     = new DateTime();
				$seconds = $now->getOffset();
				$offset  = floor( $seconds / 3600 );
			}


			// var_dump($tz);
			// var_dump($offset);
			// $tz = '';//get_option('gmt_offset');
			// if(!empty($tz)){
			// 	$offset = $tz;
			// }
			// if(empty($offset)){
			// 	$offset = 0;
			// }
			// if(!empty($o['countdown_launch'])){
			// 	$o['countdown_launch'] = home_url();
			// }
			// Language Strings
			if ( empty( $settings->txt_countdown_days ) ) {
				$settings->txt_countdown_days = 'Days';
			}
			if ( empty( $settings->txt_countdown_hours ) ) {
				$settings->txt_countdown_hours = 'Hours';
			}
			if ( empty( $settings->txt_countdown_minutes ) ) {
				$settings->txt_countdown_minutes = 'Minutes';
			}
			if ( empty( $settings->txt_countdown_seconds ) ) {
				$settings->txt_countdown_seconds = 'Seconds';
			}
			if ( empty( $settings->txt_countdown_day ) ) {
				$settings->txt_countdown_day = 'Day';
			}
			if ( empty( $settings->txt_countdown_hour ) ) {
				$settings->txt_countdown_hour = 'Hour';
			}
			if ( empty( $settings->txt_countdown_minute ) ) {
				$settings->txt_countdown_minute = 'Minute';
			}
			if ( empty( $settings->txt_countdown_second ) ) {
				$settings->txt_countdown_second = 'Second';
			}
			if ( empty( $settings->countdown_format ) ) {
				$settings->countdown_format = 'dHMS';
			}
			$expiryUrl = '';
			if ( ! empty( $settings->countdown_launch ) ) {
				$expiryUrl = "expiryUrl: '" . home_url() . '?' . rand() . "',";
			}
			?>
				<script>
				jQuery(document).ready(function($){
					var endDate = new Date();
					endDate= new Date('<?php echo $dt['year']; ?>', '<?php echo ( $dt['mon'] - 1 ); ?>', '<?php echo $dt['mday']; ?>', '<?php echo $dt['hours']; ?>', '<?php echo $dt['minutes']; ?>', '00');
					//console.log(endDate);
					$('#cspio-countdown').countdown({
						labels: ['Years', 'Months', 'Weeks', '<?php echo $settings->txt_countdown_days; ?>', '<?php echo $settings->txt_countdown_hours; ?>', '<?php echo $settings->txt_countdown_minutes; ?>', '<?php echo $settings->txt_countdown_seconds; ?>'],
						labels1: ['Years', 'Months', 'Weeks', '<?php echo $settings->txt_countdown_day; ?>', '<?php echo $settings->txt_countdown_hour; ?>', '<?php echo $settings->txt_countdown_minute; ?>', '<?php echo $settings->txt_countdown_second; ?>'],
						until: endDate,
						timezone:<?php echo $offset; ?>,
					<?php echo $expiryUrl; ?>
					<?php
					$cal_code = '';
					echo apply_filters( 'cspio_cal_code_' . $settings->page_id, $cal_code );
					?>
						format: '<?php echo $settings->countdown_format; ?>'
					});

					// $('#cspio-countdown').countdown('2015/11/18').on('update.countdown', function(event) {
					//    var $this = $(this).html(event.strftime('%S %!S:sekunde,sekunden;'
					//      + '<span class="countdown_section"><span class="countdown-amount">%-D</span> day%!d:singular,plural</span>'
					//      + '<span class="countdown_section"><span class="countdown-amount">%H</span> hr</span>'
					//      + '<span class="countdown_section"><span class="countdown-amount">%M</span> min</span>'
					//      + '<span class="countdown_section"><span class="countdown-amount">%S</span> </span> %S %!S:sekunde,sekunden;'));
					//  });
				});
				</script>
				<div id="cspio-countdown"></div>
			<?php endif; ?>
