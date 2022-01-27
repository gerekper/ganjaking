<?php
	/**
	 * Template used to display the pixproof gallery
	 * Available vars:
	 * string       $client_name
	 * string       $event_date
	 * int          $number_of_images
	 * string       $file
	 */
?>
	<div id="pixproof_data" class="pixproof-data">
		<div class="grid">
			<?php if(!empty($client_name)) {
				?>
				<div class="grid__item  one-half  lap-and-up-one-quarter  push-half--bottom">
					<div class="entry__meta-box">
						<span class="meta-box__title"><?php esc_attr_e('Client', 'agrosector'); ?></span>
						<span><?php echo esc_html($client_name); ?></span>
					</div>
				</div>
				<?php
			}
				if(!empty($event_date)) {
					?>
					<div class="grid__item  one-half  lap-and-up-one-quarter  push-half--bottom">
						<div class="entry__meta-box">
							<span class="meta-box__title"><?php esc_html_e('Event date', 'agrosector'); ?></span>
							<span><?php echo esc_html($event_date); ?></span>
						</div>
					</div>
					<?php
				}
				if(!empty($number_of_images)) {
					?>
					<div class="grid__item  one-half  lap-and-up-one-quarter  push-half--bottom">
						<div class="entry__meta-box">
							<span class="meta-box__title"><?php esc_html_e('Images', 'agrosector'); ?></span>
							<span><?php echo esc_html($number_of_images); ?></span>
						</div>
					</div>
					<?php
				}
				if(!empty($file)) {
					?>
					<div class="grid__item  one-half  lap-and-up-one-quarter  push-half--bottom">
						<div class="entry__meta-box">
							<button class="button-download  js-download" onclick="window.open('<?php echo esc_url($file); ?>')"><?php esc_html_e('Download', 'agrosector'); ?>
							</button>
						</div>
					</div>
					<?php
				}
			?>
		</div>
	</div>
<?php

