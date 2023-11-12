<?php
	function pafe_get_posts_shortcode($args, $content) {
		ob_start();
			$value = !empty($args['value']) ? $args['value'] : 'title';
			$post_type = !empty($args['post_type']) ? explode(',', $args['post_type']) : array('post');
			$post_type_count = count($post_type);
			$posts_options = '';

			foreach ($post_type as $type) {
				if ($post_type_count > 1) {
					$posts_options .= '[optgroup label="' . get_post_type_object( $type )->labels->name . '"]' . PHP_EOL;
				}

				$query_args = array(
					'post_type' => $type,
					'posts_per_page' => -1,
				);

				if (!empty($args['taxonomy']) && !empty($args['terms'])) {
		            $query_args['tax_query'] = array(
		            	array(
				            'taxonomy' => $args['taxonomy'],
			                'field'    => 'slug',
			                'terms'    => explode(',', $args['terms']),
				        ),                
		            ); 
		        }

				$posts = get_posts($query_args);
				
				foreach ($posts as $post) {
					if ($value == 'id') {
						$posts_options .= $post->post_title . '|' . $post->ID . PHP_EOL;
					} else {
						$posts_options .= $post->post_title . '|' . $post->post_title . PHP_EOL;
					}
				}

				if ($post_type_count > 1) {
					$posts_options .= '[/optgroup]' . PHP_EOL;
				}
			}
			
			echo $posts_options;

		return ob_get_clean();
	}
	add_shortcode( 'pafe_get_posts', 'pafe_get_posts_shortcode' );