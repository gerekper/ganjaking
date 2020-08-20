<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if ( ! class_exists('Mega_Menu_Widget_Elementor_Template') ) :

/**
 * Outputs an Elementor template
 */
class Mega_Menu_Widget_Elementor_Template extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'maxmegamenu_elementor_template', // Base ID
			'Elementor Template', // Name
			array( 'description' => __( 'Outputs a saved Elementor template.', 'megamenu' ) ) // Args
		);
	}


	/**
	 * Front-end display of widget.
	 *
	 * @since 2.7.4
	 * @see WP_Widget::widget()
	 * @param array   $args     Widget arguments.
	 * @param array   $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		if ( empty( $instance['template_id'] ) || ! get_post_type( $instance['template_id'] ) ) {
			return;
		}

		extract( $args );

		echo $before_widget;

        $contentElementor = "";

        if (class_exists("\\Elementor\\Plugin")) {
            $pluginElementor = \Elementor\Plugin::instance();
            $contentElementor = $pluginElementor->frontend->get_builder_content( $instance['template_id'] );
        }

        echo $contentElementor;

		echo $after_widget;
	}


	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @since 2.7.4
	 * @see WP_Widget::update()
	 * @param array   $new_instance Values just sent to be saved.
	 * @param array   $old_instance Previously saved values from database.
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['template_id'] = ! empty( $new_instance['template_id'] ) ? $new_instance['template_id'] : 0;
		
		return $instance;
	}


	/**
	 * Back-end widget form.
	 *
	 * @since 2.7.4
	 * @see WP_Widget::form()
	 * @param array   $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$template_id = ! empty( $instance['template_id'] ) ? absint( $instance['template_id'] ) : 0;
		
		$widget_title = $template_id ? get_post_field( 'post_title', $template_id ) : '';

		$posts = get_posts( array ( 'post_type' => 'elementor_library', 'post_status' => 'publish', 'numberposts' => -1 ) );

		// No blocks found.
		if ( empty( $posts ) ) {
			printf( '<p>%s</p>', __( 'No Elementor Templates found.', 'megamenu' ) );

			return;
		}

		// Input field with id is required for WordPress to display the title in the widget header.
		?>
		<input type="hidden" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" value="<?php echo esc_attr( $widget_title ); ?>">
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'template_id' ) ); ?>"><?php esc_attr_e( 'Template', 'megamenu' ); ?>:</label> 
			<select id="<?php echo esc_attr( $this->get_field_id( 'template_id' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'template_id' ) ); ?>">
				<option value=""><?php esc_html_e( '- Select -', 'megamenu' ); ?></option>
				<?php foreach ( $posts as $post ) : ?>


				<?php
					$elementor_data = get_post_meta( $instance['template_id'],'_elementor_data' );

					$type = $elementor_data['elType'];

				?>
				<option value="<?php echo esc_attr( $post->ID ); ?>"<?php selected( $post->ID, $template_id ); ?>>
					<?php echo esc_html( $post->post_title . " (" . $post->ID . " - " . get_post_meta( $post->ID,'_elementor_template_type', true ) . ")" ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<?php 
	}

}

endif;