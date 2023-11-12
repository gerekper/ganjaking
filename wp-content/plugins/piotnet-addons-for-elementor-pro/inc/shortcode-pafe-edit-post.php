<?php
	function pafe_edit_post_shortcode($args, $content) {
		ob_start();
			if( is_user_logged_in()) {
				if (current_user_can( 'edit_others_posts' ) || get_current_user_id() == get_post(get_the_ID())->post_author) {
					$edit_text = __('Edit Post', 'pafe');
					if (!empty($args['edit_text'])) {
						$edit_text = $args['edit_text'];
					}
					$url = $content;
					if (stripos($content, '?') == false) {
						$url .= '?';
					}
					if (!empty($args['sm'])) {
						if (stripos($content, '?') !== false) {
							$url .= '&';
						}
						$url .= 'sm=' . $args['sm'];
					}
					if (!empty($args['smpid'])) {
						$url .= '&smpid=' . $args['smpid'];
					}
					$url .= '&edit=' . get_the_ID();
					echo '<a href="' . $url . '" class="pafe-form-builder-edit-post">' . $edit_text . '</a>';
				}
			}
		return ob_get_clean();
	}
	add_shortcode( 'edit_post', 'pafe_edit_post_shortcode' );