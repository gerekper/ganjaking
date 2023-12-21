<?php
/**
 * Button renderer trait
 */
namespace Happy_Addons\Elementor\Traits;

defined( 'ABSPATH' ) || exit;

trait Button_Renderer {

	/**
	 * Render button with icon
	 *
	 * @param array $args { old_icon, icon_pos, new_icon, text, link, class, text_class }
	 * @return void
	 */
	public function render_icon_button( $args = [] ) {
		$args = wp_parse_args( $args, [
			'old_icon'   => 'button_icon',
			'icon_pos'   => 'button_icon_position',
			'new_icon'   => 'button_selected_icon',
			'text'       => 'button_text',
			'link'       => 'button_link',
			'class'      => 'ha-btn ha-btn--link',
			'text_class' => 'ha-btn-text',
		] );

		$settings = $this->get_settings_for_display();
		$button_text = isset( $settings[ $args['text'] ] ) ? $settings[ $args['text'] ] : '';
		$has_new_icon = ( ! empty( $settings[ $args['new_icon'] ] ) && ! empty( $settings[ $args['new_icon'] ]['value'] ) ) ? true : false;
		$has_old_icon = ! empty( $settings[ $args['old_icon'] ] ) ? true : false;

		// Return as early as possible
		// Do not process anything if there is no icon and text
		if ( empty( $button_text ) && ! $has_new_icon && ! $has_old_icon ) {
			return;
		}

		$this->add_inline_editing_attributes( $args['text'], 'none' );
        $this->add_render_attribute( $args['text'], 'class', $args['text_class'] );

        $this->add_render_attribute( 'button', 'class', $args['class'] );
		
		if( ! empty($settings[ $args['link'] ]) ){
			$this->add_link_attributes( 'button', $settings[ $args['link'] ] );
		}

		if ( $button_text && ( empty( $has_new_icon ) && empty( $has_old_icon ) ) ) :
			printf( '<a %1$s>%2$s</a>',
				$this->get_render_attribute_string( 'button' ),
				sprintf( '<span %1$s>%2$s</span>', $this->get_render_attribute_string( $args['text'] ), esc_html( $button_text ) )
			);
		elseif ( empty( $button_text ) && ( ! empty( $has_old_icon ) || ! empty( $has_new_icon ) ) ) : ?>
			<a <?php $this->print_render_attribute_string( 'button' ); ?>><?php ha_render_button_icon( $settings, $args['old_icon'], $args['new_icon'] ); ?></a>
		<?php elseif ( $button_text && ( ! empty( $has_old_icon ) || ! empty( $has_new_icon ) ) ) :
			if ( $settings[ $args['icon_pos'] ] === 'before' ) :
				$this->add_render_attribute( 'button', 'class', 'ha-btn--icon-before' );
				$button_text = sprintf( '<span %1$s>%2$s</span>', $this->get_render_attribute_string( $args['text'] ), esc_html( $button_text ) );
				?>
				<a <?php $this->print_render_attribute_string( 'button' ); ?>><?php ha_render_button_icon( $settings, $args['old_icon'], $args['new_icon'], ['class' => 'ha-btn-icon'] ); ?> <?php echo $button_text; ?></a>
				<?php
			else :
				$this->add_render_attribute( 'button', 'class', 'ha-btn--icon-after' );
				$button_text = sprintf( '<span %1$s>%2$s</span>', $this->get_render_attribute_string( $args['text'] ), esc_html( $button_text ) );
				?>
				<a <?php $this->print_render_attribute_string( 'button' ); ?>><?php echo $button_text; ?> <?php ha_render_button_icon( $settings, $args['old_icon'], $args['new_icon'], ['class' => 'ha-btn-icon'] ); ?></a>
				<?php
			endif;
		endif;
	}
}
