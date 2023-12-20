<?php if ( ! empty( $settings->enable_progressbar ) ) { ?>
				<?php
				$class = '';

				if ( ! empty( $settings->progressbar_effect ) ) {
					if ( $settings->progressbar_effect == 'striped' ) {
						$class = 'progress-striped';
					} elseif ( $settings->progressbar_effect == 'animated' ) {
						$class = 'progress-striped active';
					}
				}


				$progressbar_percentage = '0';

				if ( $settings->progress_bar_method == 'date' ) {

					if ( empty( $settings->progress_bar_start_date ) || empty( $settings->progress_bar_end_date ) ) {
					} else {
						$start_date = strtotime( $settings->progress_bar_start_date );
						$end_date   = strtotime( $settings->progress_bar_end_date );
						$today      = time();
						$diff       = abs( $end_date - $start_date ); // 8
						$complete   = abs( $start_date - $today ); //4
						if ( $diff !== 0 ) {
							$progressbar_percentage = ( $complete / $diff ) * 100;
						}
						if ( $progressbar_percentage > 100 ) {
							$progressbar_percentage = '100';
						} elseif ( $progressbar_percentage < 0 ) {
							$progressbar_percentage = '0';
						}

						$progressbar_percentage = round( $progressbar_percentage );
					}
				} else {
					if ( ! empty( $settings->progressbar_percentage ) ) {
						$progressbar_percentage = round( $settings->progressbar_percentage );
					}
				}
				?>
				<div id="cspio-progressbar">
					<div class="progress <?php echo $class; ?>">
					<div class="progress-bar" style="width: <?php echo $progressbar_percentage; ?>%;"><span><?php echo $progressbar_percentage; ?>%</span></div>
					</div>
				</div>
<?php } ?>
