<?php
	function pafe_youtube_shortcode($args, $content) {
		ob_start();
		echo '<iframe src="' . $content . '" width="100%" height="400" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe>';
		return ob_get_clean();
	}
	add_shortcode( 'youtube', 'pafe_youtube_shortcode' );