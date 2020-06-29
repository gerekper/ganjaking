<?php
add_action( 'widgets_init', 'porto_block_load_widgets' );

function porto_block_load_widgets() {
	register_widget( 'Porto_Block_Widget' );
}

class Porto_Block_Widget extends WP_Widget {

	public function __construct() {

		$widget_ops = array(
			'classname'   => 'widget-block',
			'description' => __( 'Show block.', 'porto-functionality' ),
		);

		$control_ops = array( 'id_base' => 'block-widget' );

		parent::__construct( 'block-widget', __( 'Porto: Block', 'porto-functionality' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = '';
		if ( isset( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		}

		$output = '';
		if ( $instance['name'] ) {
			$output = do_shortcode( '[porto_block name="' . esc_attr( $instance['name'] ) . '"]' );
		}

		if ( ! $output ) {
			return;
		}

		echo porto_filter_output( $before_widget );

		if ( $title ) {
			echo $before_title . sanitize_text_field( $title ) . $after_title;
		}

		?>
			<div class="block">
				<?php echo porto_filter_output( $output ); ?>
			</div>
		<?php

		echo porto_filter_output( $after_widget );
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = $new_instance['title'];
		$instance['name']  = $new_instance['name'];

		return $instance;
	}

	function form( $instance ) {
		$defaults = array();
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<strong><?php esc_html_e( 'Title', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo isset( $instance['title'] ) ? sanitize_text_field( $instance['title'] ) : ''; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'name' ); ?>">
				<strong><?php esc_html_e( 'Block Slug Name', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" value="<?php echo isset( $instance['name'] ) ? esc_attr( $instance['name'] ) : ''; ?>" />
			</label>
		</p>
		<?php
	}
}
