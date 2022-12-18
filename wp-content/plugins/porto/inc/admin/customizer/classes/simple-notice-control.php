<?php

defined( 'ABSPATH' ) || exit;

class Porto_Simple_Notice_Custom_Control extends WP_Customize_Control {
	/**
	 * The type of control being rendered
	 */
	public $type = 'simple_notice';
	/**
	 * Render the control in the customizer
	 */
	public function render_content() {
		?>
		<div class="simple-notice-custom-control">
			<?php if ( ! empty( $this->label ) ) { ?>
			<div class="customize-control-title"><?php echo esc_html( $this->label ); ?></div>
			<?php } ?>
			<?php if ( ! empty( $this->description ) ) { ?>
			<div class="customize-control-description"><?php echo wp_kses_post( $this->description ); ?></div>
			<?php } ?>
		</div>

		<?php
	}
}
