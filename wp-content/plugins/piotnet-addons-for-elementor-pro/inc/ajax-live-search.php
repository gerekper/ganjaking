<?php
	add_action( 'wp_ajax_pafe_ajax_live_search', 'pafe_ajax_live_search' );
	add_action( 'wp_ajax_nopriv_pafe_ajax_live_search', 'pafe_ajax_live_search' );
	function pafe_ajax_live_search() {
		global $wpdb;
			if ( !empty($_POST['search']) ) {
				$search = esc_sql( $_POST['search'] );
				$post_type = esc_sql( $_POST['post_type'] );

				$args = array(
					's' => $search,
				);

				if (!empty($post_type)) {
					$args['post_type'] = $post_type;
				}

				$query = new WP_Query($args);

				if ($query->have_posts()) : while($query->have_posts()) : $query->the_post();
					echo '<div class="pafe-ajax-live-search-results-item" data-pafe-ajax-live-search-href="' . get_the_permalink() . '">' . get_the_title() . '</div>';
				endwhile; endif;

				wp_reset_postdata();
			}
		wp_die();
	}
?>