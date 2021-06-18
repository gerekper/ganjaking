<div class="updb-widget-style updb-dashboardb-widget">
	<div class="updb-view-activity">
		<div class="updb-basic-info">
			<?php
			global $post, $wp, $userpro_admin, $userpro;
			if ($userpro->is_user_logged_user($user_id)) { ?>
				<?php _e('Your Posts','userpro-dashboard'); ?>
				<?php } else { ?>
				<?php _e(userpro_profile_data('display_name', $user_id)."'s Posts",'userpro-dashboard'); ?>
				<?php } ?>
		</div>
		<?php //echo do_shortcode('[userpro template=postsbyuser]'); 
                
                    $postsbyuser_mode = 'grid';
                    
                
                if (isset($args['user'])){
                        if ($args['user'] == 'author') {
                                if (is_author()){
                                        $user_id = get_query_var('author');
                                } else {
                                        $user_id = get_the_author_meta('ID');
                                }
                        } 


                        else {
                                $user_id = userpro_get_view_user( $args['user'], 'shortcode_user' );
                        }
                } 

                else if(isset($args['user_id']))
                {

                        $user_id=$args['user_id'];
                }
                else {
                        $user_id = userpro_get_view_user( get_query_var('up_username') );
                }
                $template = 'postsbyuser';
                $totalposts = count_user_posts( $user_id );
                $paginate = paginate_links( array(
                                'base'         => add_query_arg('postp' , '%#%'),
                                'total'        => ceil($totalposts/$args['postsbyuser_num']),
                                'current'      => isset($_GET['postp']) ? $_GET['postp'] : 1,
                                'show_all'     => false,
                                'end_size'     => 1,
                                'mid_size'     => 2,
                                'prev_next'    => true,
                                'prev_text'    => __('« Previous','userpro'),
                                'next_text'    => __('Next »','userpro'),
                                'type'         => 'plain',
                                'add_args' => false ,
                ));
                $is_paginate = $args['post_paginate'];
                if($is_paginate == 0)
                        $args['postsbyuser_num'] = $totalposts;
                $post_query = $userpro->posts_by_user($user_id, $args);
                if (locate_template('userpro/' . $template . '.php') != '') {
                        include get_stylesheet_directory() . '/userpro/'. $template . '.php';
                } else {
                        include userpro_path . "templates/$template.php";
                }
                wp_reset_query();
                
                ?>
	</div>
</div>
