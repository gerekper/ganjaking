<?php defined( 'LS_ROOT_FILE' ) || exit; ?>
<div class="slider-item <?php echo $class ?>" data-id="<?php echo $item['id'] ?>">
	<div class="slider-item-wrapper">
		<input type="checkbox" name="sliders[]" class="checkbox ls-hover" value="<?php echo $item['id'] ?>">
		<?php if(!$item['flag_deleted']) : ?>
		<span class="ls-hover slider-actions-button dashicons dashicons-arrow-down-alt2"></span>
		<?php else : ?>
		<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=layerslider&action=restore&id='.$item['id']), 'restore_'.$item['id'] ) ?>">
			<span class="ls-hover dashicons dashicons-backup" data-help="<?php _e('Restore removed slider', 'LayerSlider') ?>"></span>
		</a>
		<?php endif; ?>
		<a class="preview" style="background-image: url(<?php echo  ! empty( $preview ) ? $preview : LS_ROOT_URL . '/static/admin/img/blank.gif' ?>);" href="<?php echo admin_url('admin.php?page=layerslider&action=edit&id='.$item['id']) ?>">
			<?php if( empty( $preview ) ) : ?>
			<div class="no-preview">
				<h5><?php _e('No Preview', 'LayerSlider') ?></h5>
				<small><?php _e('Previews are automatically generated from slide images in sliders.', 'LayerSlider') ?></small>
			</div>
			<?php endif ?>
		</a>

		<div class="slider-actions-sheet">
			<div class="slider-actions">
				<ul>
					<li>
						<a href="#" class="embed" data-id="<?php echo $item['id'] ?>" data-slug="<?php echo htmlentities($item['slug']) ?>">
							<i class="dashicons dashicons-plus"></i>
							<span><?php _e('Embed Slider', 'LayerSlider') ?></span>
						</a>
					</li>
					<li class="half">
						<a href="<?php echo wp_nonce_url( admin_url('admin.php?page=layerslider&action=export&id='.$item['id'] ), 'export-sliders') ?>">
							<i class="dashicons dashicons-share-alt2"></i>
							<span><?php _e('Export', 'LayerSlider') ?></span>
						</a>
						<a href="#" class="ls-export-options-button">
							<i class="dashicons dashicons-arrow-right-alt2"></i>
						</a>
					</li>
					<li>
						<a href="<?php echo wp_nonce_url( admin_url('admin.php?page=layerslider&action=duplicate&id='.$item['id'] ), 'duplicate_'.$item['id']) ?>">
							<i class="dashicons dashicons-admin-page"></i>
							<span><?php _e('Duplicate', 'LayerSlider') ?></span>
						</a>
					</li>
					<li>
						<a href="<?php echo admin_url('admin.php?page=layerslider-addons&section=revisions&id='.$item['id'] ) ?>">
							<i class="dashicons dashicons-backup"></i>
							<span><?php _e('Revisions', 'LayerSlider') ?></span>
						</a>
					</li>
					<li>
						<a href="<?php echo wp_nonce_url( admin_url('admin.php?page=layerslider&action=remove&id='.$item['id'] ), 'remove_'.$item['id']) ?>" class="remove">
							<i class="dashicons dashicons-trash"></i>
							<span><?php _e('Remove', 'LayerSlider') ?></span>
						</a>
					</li>
				</ul>
			</div>

			<ul class="ls-export-options">
				<li>
					<a href="<?php echo wp_nonce_url( admin_url('admin.php?page=layerslider&action=export&id='.$item['id'] ), 'export-sliders') ?>">
						<i class="dashicons dashicons-wordpress"></i>
						<?php _e('Export for WordPress sites', 'LayerSlider') ?>
						<small><?php _e('Usual method. Used for backups or to move sliders across WP sites.', 'LayerSlider') ?></small>
					</a>
				</li>
				<li>
					<a class="ls-html-export" href="<?php echo wp_nonce_url( admin_url('admin.php?page=layerslider&action=export-html&id='.$item['id'] ), 'export-sliders') ?>">
						<i class="dashicons dashicons-editor-code"></i>
						<?php _e('Export as HTML', 'LayerSlider') ?>
						<small><?php _e('Not suitable for WP sites. Used for the jQuery version of LayerSlider.', 'LayerSlider') ?></small>
					</a>
				</li>
			</ul>

		</div>


	</div>
	<div class="info">
		<div class="name">
			<?php echo apply_filters('ls_slider_title', stripslashes($item['name']), 40) ?>
		</div>
	</div>
</div>