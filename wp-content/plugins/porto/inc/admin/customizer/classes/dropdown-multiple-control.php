<?php

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WP_Customize_Control' ) ) {
	class Porto_Dropdown_Custom_control extends WP_Customize_Control {

		public $type = 'multiple-select';

		public function render_content() {

			if ( empty( $this->choices ) ) {
				return;
			}
			?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<select <?php $this->link(); ?> multiple="multiple" size="5">
			<?php
			foreach ( $this->choices as $value => $label ) {
				$selected = ( in_array( $value, is_array( $this->value() ) ? $this->value() : array() ) ) ? selected( 1, 1, false ) : '';
				echo '<option value="' . esc_attr( $value ) . '"' . $selected . '>' . esc_html( $label ) . '</option>';
			}
			?>
			</select>
		</label>
			<?php
		}
	}
}
