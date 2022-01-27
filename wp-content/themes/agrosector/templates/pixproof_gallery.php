<?php
	/**
	 * Template used to display the pixproof gallery
	 * Available vars:
	 * array        $gallery_ids        An array with all attachments ids
	 * object       $attachments        An object with all the attachments
	 * string       $number_of_images   Count attachments
	 * string       $columns            Number of columns
	 * string       $thumbnails_size    The size of the thumbnail
	 */
?>
<div id="pixproof_gallery" class="gallery  gallery-columns-<?php echo esc_attr($columns); ?>  cf  js-pixproof-gallery">
	<?php
		$idx = 1;
		foreach($attachments as $attachment) {
			if('selected' == self::get_attachment_class($attachment)) {
				$select_label = esc_html__('Deselect', 'agrosector');
			} else {
				$select_label = esc_html__('Select', 'agrosector');
			}

			$thumb_img  = wp_get_attachment_image_src($attachment->ID, $thumbnails_size);
			$image_full = wp_get_attachment_image_src($attachment->ID, 'full-size');
			$thumb = wp_prepare_attachment_for_js($attachment->ID);

			//lets determine what should we display under each image according to settings
			// also what id should we assign to that image so the auto comments linking works
			$image_name   = '';
			$image_id_tag = '';
			if(isset($photo_display_name)) {
				switch($photo_display_name) {
					case 'unique_ids':
						$image_name   = '#'.$attachment->ID;
						$image_id_tag = 'item-'.$attachment->ID;
						break;
					case 'consecutive_ids':
						$image_name   = '#'.$idx;
						$image_id_tag = 'item-'.$idx;
						break;
					case 'file_name':
						$image_name   = '#'.$attachment->post_name;
						$image_id_tag = 'item-'.$attachment->post_name;
						break;
					case 'unique_ids_photo_title':
						$image_name   = '#'.$attachment->ID.' '.$attachment->post_title;
						$image_id_tag = 'item-'.$attachment->ID;
						break;
					case 'consecutive_ids_photo_title':
						$image_name   = '#'.$idx.' '.$attachment->post_title;
						$image_id_tag = 'item-'.$idx;
						break;
				}
			} else {
				//default to unique ids aka attachment id
				$image_name   = '#'.$attachment->ID;
				$image_id_tag = 'item-'.$attachment->ID;
			} ?>
			<div class="proof-photo  js-proof-photo  gallery-item <?php self::attachment_class($attachment); ?>" <?php self::attachment_data($attachment); ?>
			     id="<?php echo esc_attr($image_id_tag); ?>">
				<div class="proof-photo__bg">
					<div class="proof-photo__container">
						<div class="img_wrap">
							<img src="<?php echo esc_url($thumb_img[0]); ?>" alt="<?php echo esc_attr($attachment->post_title); ?>" data-ratio="<?php echo esc_attr($thumb['orientation'])?>" />
						</div>

						<div class="proof-photo__meta">
							<div class="flexbox">
								<div class="flexbox__item">
									<ul class="actions-nav  nav  nav--stacked">
										<li>
											<a class="meta__action  zoom-action" href="<?php echo esc_url($image_full[0]); ?>"
											   data-photoid="<?php echo esc_attr($image_id_tag); ?>">
												<span class="button-text"><?php esc_html_e('Zoom', 'agrosector'); ?></span>
											</a>
										</li>
										<li>
											<hr class="separator" />
										</li>
										<li>
											<a class="meta__action  select-action" href="javascript:void(0)" data-photoid="<?php echo esc_attr($image_id_tag); ?>">
												<span class="button-text"><?php echo esc_html($select_label); ?></span>
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="proof-photo__status">
							<span class="ticker">&check;</span>
							<span class="spinner"></span>
						</div>
					</div>
					<span class="proof-photo__id"><?php echo esc_html($image_name); ?></span>
				</div>
			</div>
			<?php

			$idx++;
		} ?>
</div>
