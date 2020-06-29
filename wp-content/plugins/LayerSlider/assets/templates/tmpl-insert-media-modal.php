<?php defined( 'LS_ROOT_FILE' ) || exit; ?>
<script type="text/html" id="tmpl-insert-media-modal">
	<div id="tmpl-insert-media-modal-window">
		<h1 class="kmw-modal-title"><?php _e('Insert Media', 'LayerSlider') ?></h1>

		<div class="clearfix">
			<div class="ls-left">

				<div class="ls-url divider">
					<span><?php _ex('or', 'Media modal divider', 'LayerSlider') ?></span>
					<h3><?php _e('Insert from URL (YouTube, Vimeo)', 'LayerSlider') ?></h3>
					<input type="text">
					<button class="button button-primary ls-insert"><?php _e('Add Video', 'LayerSlider') ?></button>
				</div>

				<div class="ls-embed-code divider">
					<span><?php _ex('or', 'Media modal divider', 'LayerSlider') ?></span>
					<h3><?php _e('Paste embed or HTML code', 'LayerSlider') ?></h3>
					<textarea></textarea>
					<button class="button button-primary ls-insert"><?php _e('Add Media', 'LayerSlider') ?></button>
				</div>

				<div class="ls-html5">
					<h3><?php _e('Add self-hosted HTML 5 video', 'LayerSlider') ?></h3>
					<p><?php _e('You can select multiple media formats to maximize browser compatibility across devices by holding down the Ctrl / Command key and selecting multiple uploads. We recommend using MP3 or AAC in MP4 for audio, and VP8+Vorbis in WebM or H.264+MP3/AAC in MP4 for video.', 'LayerSlider') ?></p>
					<button class="button button-primary button-hero ls-html5-button"><?php _e('Choose Media', 'LayerSlider') ?></button>
				</div>
			</div>

			<div class="ls-right">
				<h3><?php _e('Preview', 'LayerSlider') ?></h3>
				<div class="ls-media-preview"></div>
			</div>
		</div>

	</div>
</script>