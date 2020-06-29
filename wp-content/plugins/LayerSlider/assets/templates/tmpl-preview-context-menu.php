<?php defined( 'LS_ROOT_FILE' ) || exit; ?>
<script type="text/html" id="tmpl-ls-preview-context-menu">
	<div class="ls-preview-context-menu">
		<ul>
			<li class="group">
				<i class="dashicons dashicons-plus"></i>
				<span><?php _e('Add Layer', 'LayerSlider') ?></span>
				<div>
					<ul class="ls-context-add-layer">
						<li data-type="img">
							<i class="dashicons dashicons-format-image"></i>
							<span><?php _e('Image', 'LayerSlider') ?></span>
						</li>
						<li data-type="icon">
							<i class="dashicons dashicons-flag"></i>
							<span><?php _e('Icon', 'LayerSlider') ?></span>
						</li>
						<li data-type="text">
							<i class="dashicons dashicons-text"></i>
							<span><?php _e('Text', 'LayerSlider') ?></span>
						</li>
						<li data-type="button">
							<i class="dashicons dashicons-marker"></i>
							<span><?php _e('Button', 'LayerSlider') ?></span>
						</li>
						<li data-type="media">
							<i class="dashicons dashicons-video-alt3"></i>
							<span><?php _e('Video / Audio', 'LayerSlider') ?></span>
						</li>
						<li data-type="html">
							<i class="dashicons dashicons-editor-code"></i>
							<span><?php _e('HTML', 'LayerSlider') ?></span>
						</li>
						<li data-type="post">
							<i class="dashicons dashicons-admin-post"></i>
							<span><?php _e('Dynamic Layer', 'LayerSlider') ?></span>
						</li>
						<li data-type="import">
							<i class="dashicons dashicons-upload"></i>
							<span><?php _e('Import Layer', 'LayerSlider') ?></span>
						</li>
					</ul>
				</div>
			</li>
			<li class="group ls-context-overlapping-layers">
				<i class="dashicons dashicons-menu"></i>
				<span><?php _e('Overlapping Layers', 'LayerSlider') ?></span>
				<div>
					<ul></ul>
				</div>
			</li>
			<li class="group ls-context-menu-align">
				<i class="dashicons dashicons-align-right"></i>
				<span><?php _e('Align Layer', 'LayerSlider') ?></span>
				<div>
					<ul>
						<li data-move="left" class="ls-align-left">
							<i class="dashicons dashicons-align-left"></i>
							<span><?php _e('Left Edge', 'LayerSlider') ?></span>
						</li>
						<li data-move="center" class="ls-align-center">
							<i class="dashicons dashicons-align-center"></i>
							<span><?php _e('Horizontal Center', 'LayerSlider') ?></span>
						</li>
						<li data-move="right" class="ls-align-right">
							<i class="dashicons dashicons-align-right"></i>
							<span><?php _e('Right Edge', 'LayerSlider') ?></span>
						</li>
						<li class="divider"></li>
						<li data-move="top" class="ls-align-top">
							<i class="dashicons dashicons-align-left"></i>
							<span><?php _e('Top Edge', 'LayerSlider') ?></span>
						</li>
						<li data-move="middle" class="ls-align-middle">
							<i class="dashicons dashicons-align-center"></i>
							<span><?php _e('Vertical Center', 'LayerSlider') ?></span>
						</li>
						<li data-move="bottom" class="ls-align-bottom">
							<i class="dashicons dashicons-align-right"></i>
							<span><?php _e('Bottom Edge', 'LayerSlider') ?></span>
						</li>
						<li class="divider"></li>
						<li data-move="middle center" class="ls-align-center-center">
							<i class="dashicons dashicons-align-center"></i>
							<span><?php _e('Center Center', 'LayerSlider') ?></span>
						</li>
					</ul>
				</div>
			</li>
			<li class="ls-context-menu-duplicate">
				<i class="dashicons dashicons-admin-page"></i>
				<span><?php _e('Duplicate Layer', 'LayerSlider') ?></span>
			</li>
			<li class="ls-context-menu-remove">
				<i class="dashicons dashicons-trash"></i>
				<span><?php _e('Remove Layer', 'LayerSlider') ?></span>
			</li>
			<li class="divider"></li>
			<li class="ls-context-menu-copy-layer">
				<i class="dashicons dashicons-clipboard"></i>
				<span><?php _e('Copy Layer', 'LayerSlider') ?></span>
			</li>
			<li class="ls-context-menu-paste-layer">
				<i class="dashicons dashicons-admin-page"></i>
				<span><?php _e('Paste Layer', 'LayerSlider') ?></span>
			</li>
			<li class="divider"></li>
			<li class="ls-context-menu-hide">
				<i class="dashicons dashicons-visibility"></i>
				<span><?php _e('Toggle Layer Visibility', 'LayerSlider') ?></span>
			</li>
			<li class="ls-context-menu-lock">
				<i class="dashicons dashicons-lock"></i>
				<span><?php _e('Toggle Layer Locking', 'LayerSlider') ?></span>
			</li>
			<li class="divider"></li>
			<li class="ls-context-menu-copy-styles">
				<i class="dashicons dashicons-clipboard"></i>
				<span><?php _e('Copy Layer Styles', 'LayerSlider') ?></span>
			</li>
			<li class="ls-context-menu-paste-styles">
				<i class="dashicons dashicons-admin-page"></i>
				<span><?php _e('Paste Layer Styles', 'LayerSlider') ?></span>
			</li>
		</ul>
	</div>
</script>
