<?php

if(!defined('ABSPATH')) {
	exit;
}

add_action('admin_notices', 'fix__elemetor_pages');

add_action('wp_ajax_fix_elementor_page', 'ajax_fix__elemetor_pages');

function fix__elemetor_pages(){
	$posts   = new \WP_Query(
		array(
			'post_type'      => array_keys(get_post_types()),
			'posts_per_page' => '-1',
			'meta_query'     => array_merge(
				array(
					'relation' => 'AND',
				),
				array(
					array(
						'key'     => '_elementor_edit_mode',
						'compare' => 'EXISTS',
					),
				)
			),
			'fields'         => 'ids',
		)
	);
	$message = '<p>Fix Pages?</p>';
	$message = '<p><span id="current_fix_page">0</span> / <span id="all_fix_page">'.$posts->post_count.'</span></p>';
	$message .= '<p>'.sprintf('<a href="%s" class="button-primary" id="fix_elementor_button">%s</a>', '#', esc_html__('Fix', 'gt3_themes_core')).'</p>';

	echo '<div class="error"><p>'.$message.'</p></div>';
	?>
	<script>
		(function () {
			var ids = <?php echo json_encode($posts->posts); ?>;
			var button = document.getElementById('fix_elementor_button');
			var current = document.getElementById('current_fix_page');
			var currentIndex = 0;

			button && button.addEventListener('click', function () {
				this.remove();
				send_request();
			});

			function send_request() {
				jQuery.ajax({
					url: ajaxurl,
					method: "POST",
					data: {
						action: "fix_elementor_page",
						id: ids[currentIndex],
					}
				}).done(function () {
					current.innerText = (++currentIndex).toString();
					if (currentIndex >= ids.length) return;
					send_request();
				});
			}
		})();
	</script>
	<?php
}

function ajax_fix__elemetor_pages(){
	$post_id         = $_POST['id'];
	$elementor       = \Elementor\Plugin::instance();
	$elementor_post  = $elementor->documents->get($post_id);
	$is_meta_updated = null;
	if($elementor_post !== false) {
		$meta = $elementor_post->get_json_meta('_elementor_data');
		foreach($meta as &$level0) {
			gt3_clear_elementor_tabs_clearMeta($level0);
		}

		$json_value      = wp_slash(wp_json_encode($meta));
		$is_meta_updated = update_metadata('post', $post_id, '_elementor_data', $json_value);
	}

	echo json_encode(
		array(
			'code' => $is_meta_updated
		)
	);
}

function gt3_clear_elementor_tabs_clearMeta(&$key){

	if(key_exists('elType', $key) && in_array($key['elType'], array('section','column')) && key_exists('settings', $key) && key_exists('items', $key['settings'])) {
		unset($key['settings']['items']);
	}
	if(key_exists('elements', $key) && is_array($key['elements'])) {
		foreach($key['elements'] as &$element) {
			gt3_clear_elementor_tabs_clearMeta($element);
		}
	}
}

;
