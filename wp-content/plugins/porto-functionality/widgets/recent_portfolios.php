<?php
add_action( 'widgets_init', 'porto_recent_portfolios_load_widgets' );

function porto_recent_portfolios_load_widgets() {
	register_widget( 'Porto_Recent_Portfolios_Widget' );
}

class Porto_Recent_Portfolios_Widget extends WP_Widget {

	public function __construct() {

		$widget_ops = array(
			'classname'   => 'widget-recent-portfolios',
			'description' => __( 'Show recent portfolios.', 'porto-functionality' ),
		);

		$control_ops = array( 'id_base' => 'recent_portfolios-widget' );

		parent::__construct( 'recent_portfolios-widget', __( 'Porto: Recent Portfolios', 'porto-functionality' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {

		extract( $args );
		$title  = apply_filters( 'widget_title', $instance['title'] );
		$number = $instance['number'];
		$items  = $instance['items'];
		$view   = $instance['view'];
		$cat    = $instance['cat'];

		if ( empty( $items ) ) {
			$items = 6;
		} else {
			$items = (int) $items;
		}

		$options                = array();
		$options['themeConfig'] = true;
		$options['lg']          = 1;
		$options['md']          = 3;
		$options['sm']          = 2;
		$options['single']      = 'small' == $view ? true : false;
		$options['animateIn']   = '';
		$options['animateOut']  = '';
		$options                = json_encode( $options );

		$args = array(
			'post_type'      => 'portfolio',
			'posts_per_page' => $number,
		);

		if ( $cat ) {
			$categories = explode( ',', $cat );
			$gc         = array();
			foreach ( $categories as $grid_cat ) {
				array_push( $gc, $grid_cat );
			}
			$gc = implode( ',', $gc );
			//$args['category_name'] = $gc;

			$taxonomies        = get_taxonomies( '', 'object' );
			$args['tax_query'] = array( 'relation' => 'OR' );
			foreach ( $taxonomies as $t ) {
				if ( 'portfolio' == $t->object_type[0] ) {
					$args['tax_query'][] = array(
						'taxonomy' => $t->name, //$t->name,//'portfolio_cat',
						'terms'    => $categories,
					);
				}
			}
		}

		$portfolios = new WP_Query( $args );

		if ( $portfolios->have_posts() ) :

			echo porto_filter_output( $before_widget );

			if ( $title ) {
				echo porto_filter_output( $before_title ) . sanitize_text_field( $title ) . $after_title;
			}

			?>
			<div class="<?php echo (int) $number > (int) $items ? 'row' : '', 'small' == $view ? ' gallery-row' : ''; ?>">
				<div
				<?php if ( $number > $items ) : ?>
					class="portfolio-carousel porto-carousel owl-carousel show-nav-title" data-plugin-options="<?php echo esc_attr( $options ); ?>"<?php endif; ?>>
					<?php
					$count = 0;
					while ( $portfolios->have_posts() ) {
						$portfolios->the_post();

						if ( 0 == $count % $items ) {
							echo '<div class="portfolio-slide">';
						}
						if ( 'simple' == $view ) {
							echo '<div class="portfolio-item">';
								echo '<div class="portfolio-cats">' . porto_filter_output( get_the_term_list( get_the_ID(), 'portfolio_cat', '', ', ', '' ) ) . '</div>';
								echo '<h5 class="portfolio-item-title">';
								echo '<a href="' . esc_url( get_the_permalink() ) . '">';
								the_title();
								echo '</a>';
								echo '</h5>';
								echo '<a class="btn-view-more" href="' . esc_url( get_the_permalink() ) . '">' . esc_html__( 'View More', 'porto-functionality' ) . '</a>';
							echo '</div>';
						} else {
							get_template_part( 'content', 'portfolio-item' . ( 'small' == $view ? '-small' : '' ) );
						}

						if ( $count % $items == $items - 1 ) {
							echo '</div>';
						}

						$count++;
					}
					?>
				</div>
			</div>
			<a class="btn-flat pt-right btn-xs view-more" href="<?php echo get_post_type_archive_link( 'portfolio' ); ?>"><?php esc_html_e( 'View More', 'porto-functionality' ); ?> <i class="fas fa-arrow-<?php echo is_rtl() ? 'left' : 'right'; ?>"></i></a>
			<?php

			echo porto_filter_output( $after_widget );

		endif;
		wp_reset_postdata();
	}

	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['number'] = $new_instance['number'];
		$instance['items']  = $new_instance['items'];
		$instance['view']   = $new_instance['view'];
		$instance['cat']    = $new_instance['cat'];

		return $instance;
	}

	function form( $instance ) {

		$defaults = array(
			'title'  => __( 'Recent Portfolios', 'porto-functionality' ),
			'number' => 6,
			'items'  => 6,
			'view'   => 'small',
			'cat'    => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<strong><?php esc_html_e( 'Title', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo isset( $instance['title'] ) ? sanitize_text_field( $instance['title'] ) : ''; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">
				<strong><?php esc_html_e( 'Number of portfolios to show', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" value="<?php echo isset( $instance['number'] ) ? esc_attr( $instance['number'] ) : ''; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'view' ) ); ?>">
				<strong><?php esc_html_e( 'View Type', 'porto-functionality' ); ?>:</strong>
				<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'view' ) ); ?>">
					<option value="small"<?php echo ( isset( $instance['view'] ) && 'small' == $instance['view'] ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'Small', 'porto-functionality' ); ?></option>
					<option value="large"<?php echo ( isset( $instance['view'] ) && 'large' == $instance['view'] ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'Large', 'porto-functionality' ); ?></option>
					<option value="simple"<?php echo ( isset( $instance['view'] ) && 'simple' == $instance['view'] ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'Simple', 'porto-functionality' ); ?></option>
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
		<?php
	}
}
