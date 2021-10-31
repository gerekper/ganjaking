<?php
add_action( 'widgets_init', 'porto_recent_posts_load_widgets' );

function porto_recent_posts_load_widgets() {
	register_widget( 'Porto_Recent_Posts_Widget' );
}

class Porto_Recent_Posts_Widget extends WP_Widget {

	public function __construct() {

		$widget_ops = array(
			'classname'   => 'widget-recent-posts',
			'description' => __( 'Show recent posts.', 'porto-functionality' ),
		);

		$control_ops = array( 'id_base' => 'recent_posts-widget' );

		parent::__construct( 'recent_posts-widget', __( 'Porto: Recent Posts', 'porto-functionality' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {

		global $porto_settings;

		extract( $args );
		$title      = apply_filters( 'widget_title', $instance['title'] );
		$number     = $instance['number'];
		$items      = $instance['items'];
		$view       = $instance['view'];
		$cat        = $instance['cat'];
		$show_image = $instance['show_image'];

		if ( empty( $items ) || 0 === (int) $items ) {
			$items = 3;
		} else {
			$items = (int) $items;
		}

		$options                = array();
		$options['themeConfig'] = true;
		$options['lg']          = 1;
		$options['md']          = ( $porto_settings && isset( $porto_settings['show-mobile-sidebar'] ) && $porto_settings['show-mobile-sidebar'] ) ? 1 : 3;
		$options['sm']          = ( $porto_settings && isset( $porto_settings['show-mobile-sidebar'] ) && $porto_settings['show-mobile-sidebar'] ) ? 1 : 2;
		$options['single']      = 'small' == $view ? true : false;
		$options['animateIn']   = '';
		$options['animateOut']  = '';
		$options                = json_encode( $options );

		$args = array(
			'post_type'      => 'post',
			'posts_per_page' => $number,
		);

		if ( $cat ) {
			$args['cat'] = $cat;
		}

		$posts = new WP_Query( $args );

		if ( $posts->have_posts() ) :

			echo porto_filter_output( $before_widget );

			if ( $title ) {
				echo porto_filter_output( $before_title ) . porto_strip_script_tags( $title ) . $after_title;
			}

			?>
			<div<?php echo (int) $number > $items ? ' class="row"' : ''; ?>>
				<div<?php echo (int) $number > $items ? ' class="post-carousel porto-carousel owl-carousel show-nav-title" data-plugin-options="' . esc_attr( $options ) . '"' : ''; ?>>
					<?php
					$count = 0;
					while ( $posts->have_posts() ) {
						$posts->the_post();
						global $previousday;
						unset( $previousday );

						if ( 0 == $count % $items ) {
							echo '<div class="post-slide">';
						}

						if ( $show_image ) {
							get_template_part( 'content', 'post-item' . ( 'small' == $view ? '-small' : '' ) );
						} else {
							get_template_part( 'content', 'post-item-no-image' . ( 'small' == $view ? '-small' : '' ) );
						}

						if ( $count % $items == $items - 1 || $count == $number - 1 ) {
							echo '</div>';
						}

						$count++;
					}
					if ( 0 != $count % $items && $count != $number ) {
						echo '</div>';
					}
					?>
				</div>
			</div>
			<?php

			echo porto_filter_output( $after_widget );

		endif;
		wp_reset_postdata();
	}

	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']      = strip_tags( $new_instance['title'] );
		$instance['number']     = $new_instance['number'];
		$instance['items']      = $new_instance['items'];
		$instance['view']       = $new_instance['view'];
		$instance['cat']        = $new_instance['cat'];
		$instance['show_image'] = $new_instance['show_image'];

		return $instance;
	}

	function form( $instance ) {

		$defaults = array(
			'title'      => __( 'Recent Posts', 'porto-functionality' ),
			'number'     => 6,
			'items'      => 3,
			'view'       => 'small',
			'cat'        => '',
			'show_image' => 'on',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<strong><?php esc_html_e( 'Title', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo isset( $instance['title'] ) ? porto_strip_script_tags( $instance['title'] ) : ''; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">
				<strong><?php esc_html_e( 'Number of posts to show', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" value="<?php echo isset( $instance['number'] ) ? esc_attr( $instance['number'] ) : ''; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'view' ) ); ?>">
				<strong><?php esc_html_e( 'View Type', 'porto-functionality' ); ?>:</strong>
				<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'view' ) ); ?>">
					<option value="small"<?php echo ( isset( $instance['view'] ) && 'small' == $instance['view'] ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'Small', 'porto-functionality' ); ?></option>
					<option value="large"<?php echo ( isset( $instance['view'] ) && 'large' == $instance['view'] ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'Large', 'porto-functionality' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>">
				<strong><?php esc_html_e( 'Number of items per slide', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>" value="<?php echo isset( $instance['items'] ) ? esc_attr( $instance['items'] ) : ''; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'cat' ) ); ?>">
				<strong><?php esc_html_e( 'Category IDs', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'cat' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cat' ) ); ?>" value="<?php echo isset( $instance['cat'] ) ? esc_attr( $instance['cat'] ) : ''; ?>" />
			</label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_image'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_image' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_image' ) ); ?>"><?php esc_html_e( 'Show Post Image', 'porto-functionality' ); ?></label>
		</p>
		<?php
	}
}
