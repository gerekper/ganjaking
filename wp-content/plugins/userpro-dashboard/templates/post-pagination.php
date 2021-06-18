<?php

$total_query = array('posts_per_page' => '-1','author' => $current_user->ID,'post_status' => array( 'pending', 'draft','publish'));
                    $total_posts =  get_posts($total_query);
                    $updb_count = ceil(count($total_posts)/$updb_post_count);

echo  '<div class="updb-pagi">'.paginate_links( array(
					'total'        => $updb_count,
					'current'      => $page_no,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __('Previous','userpro'),
					'next_text'    => __('Next','userpro'),
					'type'         => 'plain',
					'add_args' => false ,
				)).'</div>';