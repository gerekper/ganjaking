<?php

/**
 * Products Viewed together or Purchased History
 *
 * Gets and displays recommended products in an unordered list
 *
 * @author      Lucas Stark
 * @category    Widgets
 * @package     WooCommerce_Recommender/Widgets
 * @version     1.0
 * @extends     WP_Widget
 */
class WooCommerce_Widget_Recommended_Products extends WP_Widget {

	/** Variable to store the recommended prodcuts for sorting. */
	private $similar_products;

	/** Variables to setup the widget. */
	var $woo_widget_cssclass;
	var $woo_widget_description;
	var $woo_widget_idbase;
	var $woo_widget_name;

	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {

		/* Widget variable settings. */
		$this->woo_widget_cssclass    = 'widget_recommended_products';
		$this->woo_widget_description = __( 'Display a list of recommended products on your site.', 'wc_recommender' );
		$this->woo_widget_idbase      = 'woocommerce_recommended_products';
		$this->woo_widget_name        = __( 'WooCommerce Recommended Products', 'wc_recommender' );

		/* Widget settings. */
		$widget_ops = array(
			'classname'   => $this->woo_widget_cssclass,
			'description' => $this->woo_widget_description
		);

		/* Create the widget. */
		parent::__construct( 'recommended-products', $this->woo_widget_name, $widget_ops );
	}

	/**
	 * widget function.
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 * @see    WP_Widget
	 * @access public
	 */
	function widget( $args, $instance ) {
		global $post, $woocommerce;

		if ( ! is_product() ) {
			return;
		}

		ob_start();
		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Recommended Products', 'woocommerce' ) : $instance['title'], $instance, $this->id_base );
		if ( ! $number = (int) $instance['number'] ) {
			$number = 10;
		} else if ( $number < 1 ) {
			$number = 1;
		} else if ( $number > 15 ) {
			$number = 15;
		}

		$activity_type = isset( $instance['activity'] ) ? $instance['activity'] : 'viewed'
		?>

		<?php
		$query_args = array(
			'posts_per_page' => - 1,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product'
		);

		$query_args['post__in'] = array();

		$this->similar_products = woocommerce_recommender_get_simularity( get_the_ID(), $activity_type );
		if ( $this->similar_products ) {
			foreach ( $this->similar_products as $product_id => $score ) {
				if ( $score > 0 ) {
					$query_args['post__in'][] = $product_id;
				}
			}
		}

		if ( ! count( $this->similar_products ) ) {
		    ob_end_clean();
			return;
		}

		$query_args['meta_query']   = array();
		$query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
		$query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();

		$posts = get_posts( $query_args );


		if ( $posts && count( $posts ) ) :

			woocommerce_recommender_sort_posts( $posts, $this->similar_products );
			$products = array_chunk( $posts, $number );
			?>

			<?php echo $before_widget; ?>
			<?php if ( $title ) {
			echo $before_title . $title . $after_title;
		} ?>
            <ul class="product_list_widget">
				<?php
				foreach ( $products[0] as $post ) :
					setup_postdata( $post );
					global $post;
					global $product;
					?>
                    <li><a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>"
                           title="<?php echo esc_attr( $post->post_title ? $post->post_title : $post->ID ); ?>">
							<?php echo $product->get_image(); ?>
							<?php if ( $post->post_title ) {
								echo get_the_title( $post->ID );
							} else {
								echo $post->ID;
							} ?>

                        </a> <?php echo $product->get_price_html(); ?></li>

				<?php endforeach; ?>
            </ul>
			<?php echo $after_widget; ?>

		<?php
		endif;

		$content = ob_get_clean();

		if ( isset( $args['widget_id'] ) ) {
			$cache[ $args['widget_id'] ] = $content;
		}

		echo $content;

		wp_reset_postdata();
	}

	private function sort_by_index( $a, $b ) {
		if ( $this->similar_products[ $a->ID ] < $this->similar_products[ $b->ID ] ) {
			return 1;
		} elseif ( $this->similar_products[ $a->ID ] > $this->similar_products[ $b->ID ] ) {
			return - 1;
		} else {
			return 0;
		}
	}

	/**
	 * update function.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 * @see    WP_Widget->update
	 * @access public
	 */
	function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$instance['title']    = strip_tags( $new_instance['title'] );
		$instance['number']   = (int) $new_instance['number'];
		$instance['activity'] = isset( $new_instance['activity'] ) ? $new_instance['activity'] : 'viewed';

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @param array $instance
	 *
	 * @return void
	 * @see    WP_Widget->form
	 * @access public
	 */
	function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		if ( ! isset( $instance['number'] ) || ! $number = (int) $instance['number'] ) {
			$number = 5;
		}

		if (!isset($instance['activity'])) {
		    $instance['activity'] = 'completed';
        }

		if ( is_array( $instance['activity'] ) ) {
			$instance['activity'] = array_shift( $instance['activity'] );
		}

		?>
        <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'woocommerce' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
                   value="<?php echo esc_attr( $title ); ?>"/></p>

        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of products to show:', 'woocommerce' ); ?></label>
            <input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text"
                   value="<?php echo esc_attr( $number ); ?>" size="3"/></p>

        <p>
        <p>
            <label for="<?php echo $this->get_field_id( 'activity' ); ?>"><?php _e( 'Show recommendations based on:', 'woocommerce' ); ?></label>
			<?php
			$activities = array( 'viewed' => 'View History', 'completed' => 'Purchase History' );
			echo '<select id="' . $this->get_field_id( 'activity' ) . '" name="' . $this->get_field_name( 'activity' ) . '">';
			?>
			<?php foreach ( $activities as $activity => $label ): ?>
                <option <?php selected( $activity, $instance['activity'] ); ?>
                        value="<?php echo $activity; ?>"><?php echo $label; ?></option>
			<?php endforeach; ?>
            <?php echo '</select>'; ?>
        </p>

		<?php
	}

}
