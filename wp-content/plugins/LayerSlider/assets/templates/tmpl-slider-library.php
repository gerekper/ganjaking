<?php defined( 'LS_ROOT_FILE' ) || exit; ?>

<div class="ls-sliders-grid">

<?php

if( ! empty($sliders) ) {
	foreach($sliders as $key => $item) {
		$preview = apply_filters('ls_preview_for_slider', $item );

		if( ! empty( $item['flag_group'] ) ) {
			$groupItems = $item['items'];

			if( empty( $groupItems ) ) { continue; }
			?>
			<div class="slider-item group-item"
				data-id="<?php echo $item['id'] ?>"
				data-name="<?php echo apply_filters('ls_slider_title', stripslashes($item['name']), 40) ?>"
			>
				<div class="slider-item-wrapper">
					<div class="items">
						<?php
							if( ! empty( $item['items'] ) ) {
							foreach( $groupItems as $groupKey => $groupItem )  {
							$groupPreview = apply_filters('ls_preview_for_slider', $groupItem ); ?>
								<div class="item <?php echo ($groupItem['flag_deleted'] == '1') ? 'dimmed' : '' ?>">
									<div class="preview" style="background-image: url(<?php echo  ! empty( $groupPreview ) ? $groupPreview : LS_ROOT_URL . '/static/admin/img/blank.gif' ?>);">
										<?php if( empty( $groupPreview ) ) : ?>
										<div class="no-preview">
											<?php _e('No Preview', 'LayerSlider') ?>
										</div>
										<?php endif ?>
									</div>
								</div>
							<?php } } ?>
					</div>
				</div>
				<div class="info">
					<div class="name">
						<?php echo apply_filters('ls_slider_title', stripslashes($item['name']), 40) ?>
					</div>
				</div>
			</div>
			<div class="ls-hidden">
				<div class="clearfix">
					<?php
						if( ! empty( $item['items'] ) ) {
							foreach( $groupItems as $groupKey => $item ) {
								$preview = apply_filters('ls_preview_for_slider', $item );
								?>
								<div class="slider-item"
									data-id="<?php echo $item['id'] ?>"
									data-slug="<?php echo $item['slug'] ?>"
									data-name="<?php echo apply_filters('ls_slider_title', stripslashes($item['name']), 40) ?>"
									data-previewurl="<?php echo  ! empty( $preview ) ? $preview : LS_ROOT_URL . '/static/admin/img/blank.gif' ?>"
									data-slidecount="<?php echo ! empty( $item['data']['layers'] ) ? count( $item['data']['layers'] ) : 0 ?>"
									data-author="<?php echo $item['author'] ?>"
									data-date_c="<?php echo $item['date_c'] ?>"
									data-date_m="<?php echo $item['date_m'] ?>"
								>
									<div class="slider-item-wrapper">
										<div class="preview" style="background-image: url(<?php echo  ! empty( $preview ) ? $preview : LS_ROOT_URL . '/static/admin/img/blank.gif' ?>);">
											<?php if( empty( $preview ) ) : ?>
											<div class="no-preview">
												<h5><?php _e('No Preview', 'LayerSlider') ?></h5>
												<small><?php _e('Previews are automatically generated from slide images in sliders.', 'LayerSlider') ?></small>
											</div>
											<?php endif ?>
										</div>
									</div>
									<div class="info">
										<div class="name">
											<?php echo apply_filters('ls_slider_title', stripslashes($item['name']), 40) ?>
										</div>
									</div>
								</div><?php
							}
						}
					?>
				</div>
			</div>
			<?php

		} else { ?>
			<div class="slider-item"
				data-id="<?php echo $item['id'] ?>"
				data-slug="<?php echo $item['slug'] ?>"
				data-name="<?php echo apply_filters('ls_slider_title', stripslashes($item['name']), 40) ?>"
				data-previewurl="<?php echo  ! empty( $preview ) ? $preview : LS_ROOT_URL . '/static/admin/img/blank.gif' ?>"
				data-slidecount="<?php echo ! empty( $item['data']['layers'] ) ? count( $item['data']['layers'] ) : 0 ?>"
				data-author="<?php echo $item['author'] ?>"
				data-date_c="<?php echo $item['date_c'] ?>"
				data-date_m="<?php echo $item['date_m'] ?>"
			>
				<div class="slider-item-wrapper">
					<div class="preview" style="background-image: url(<?php echo  ! empty( $preview ) ? $preview : LS_ROOT_URL . '/static/admin/img/blank.gif' ?>);">
						<?php if( empty( $preview ) ) : ?>
						<div class="no-preview">
							<h5><?php _e('No Preview', 'LayerSlider') ?></h5>
							<small><?php _e('Previews are automatically generated from slide images in sliders.', 'LayerSlider') ?></small>
						</div>
						<?php endif ?>
					</div>
				</div>
				<div class="info">
					<div class="name">
						<?php echo apply_filters('ls_slider_title', stripslashes($item['name']), 40) ?>
					</div>
				</div>
			</div><?php
		}
	}
}
?>

</div>