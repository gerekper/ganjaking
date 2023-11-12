<?php
	function pafe_delete_post_shortcode($args, $content) {
		ob_start();
			if( is_user_logged_in()) {
				if (current_user_can( 'edit_others_posts' ) || get_current_user_id() == get_post(get_the_ID())->post_author) {
					$delete_text = __('Delete Post', 'pafe');
					$redirect = get_home_url();
					$force_delete = 0;

					if (!empty($args['delete_text'])) {
						$delete_text = $args['delete_text'];
					}

					if (!empty($args['redirect'])) {
						$redirect = $args['redirect'];
					}

					if (!empty($args['force_delete'])) {
						$force_delete = $args['force_delete'];
					}

					wp_enqueue_script( 'pafe-form-builder', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder.min.js', array('jquery'), PAFE_PRO_VERSION );
					wp_enqueue_style( 'pafe-form-builder-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/form-builder.min.css', [], PAFE_PRO_VERSION );

					echo '<a data-pafe-form-builder-delete-post="' . get_the_ID() . '" data-pafe-form-builder-delete-post-redirect="' . $redirect . '" data-pafe-form-builder-delete-post-force="' . $force_delete . '" class="pafe-form-builder-delete-post">' . $delete_text . '</a>';

					wp_enqueue_script( 'pafe-form-builder-advanced2-script' );
				}
			}
		return ob_get_clean();
	}
	add_shortcode( 'delete_post', 'pafe_delete_post_shortcode' );