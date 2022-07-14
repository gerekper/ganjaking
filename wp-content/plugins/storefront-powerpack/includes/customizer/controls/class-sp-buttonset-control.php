<?php
/**
 * Create a Buttonset control
 *
 * This class incorporates code from the Kirki Customizer Framework and from a tutorial
 * written by Otto Wood.
 *
 * The Kirki Customizer Framework, Copyright Aristeides Stathopoulos (@aristath),
 * is licensed under the terms of the GNU GPL, Version 2 (or later).
 *
 * @link https://github.com/reduxframework/kirki/
 * @link http://ottopress.com/2012/making-a-custom-control-for-the-theme-customizer/
 */
class SP_Buttonset_Control extends WP_Customize_Control {

	/**
	 * Declare the control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'radio-image';

	/**
	 * Enqueue scripts and styles for the custom control.
	 *
	 * Scripts are hooked at {@see 'customize_controls_enqueue_scripts'}.
	 *
	 * Note, you can also enqueue stylesheets here as well. Stylesheets are hooked
	 * at 'customize_controls_print_styles'.
	 *
	 * @access public
	 */
	public function enqueue() {
		wp_enqueue_script( 'jquery-ui-button' );

		$control_css = '
			.sp-buttonset {
				background-color: #ddd;
				overflow: hidden;
				zoom: 1;
				display: inline-block;
				border-radius: 3px;
				box-shadow: inset 0 1px 2px rgba(0,0,0,0.15), 0 1px 1px rgba(255,255,255,.5);
			}

			.sp-buttonset label {
				display: inline-block;
				padding: 5px 10px;
				border-right: 1px solid #ccc;
				float: left;
			}

			.sp-buttonset label:first-child {
				border-radius: 3px 0 0 3px;
			}

			.sp-buttonset label:last-child {
				border-right: 0;
				border-radius: 0 3px 3px 0;
			}

			.sp-buttonset label.ui-state-active {
				background-color: #008ec2;
				color: #fff;
				text-shadow: 0 -1px 1px #006799, 1px 0 1px #006799, 0 1px 1px #006799, -1px 0 1px #006799;
				box-shadow: inset 0 0 0 1px rgba(0,0,0,0.2);
			}
		';

		wp_add_inline_style( 'customize-controls', $control_css );
	}

	/**
	 * Render the control to be displayed in the Customizer.
	 */
	public function render_content() {
		if ( empty( $this->choices ) ) {
			return;
		}

		$name = '_customize-radio-' . $this->id; ?>

		<span class="customize-control-title">
			<?php echo esc_attr( $this->label ); ?>
		</span>

		<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif; ?>

		<div id="input_<?php echo esc_attr( $this->id ); ?>" class="sp-buttonset">
			<?php foreach ( $this->choices as $value => $label ) : ?>
				<input class="image-select" type="radio" value="<?php echo esc_attr( $value ); ?>" id="<?php echo esc_attr( $this->id . $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?>>
					<label for="<?php echo esc_attr( $this->id ) . esc_attr( $value ); ?>">
						<?php echo esc_attr( $label ); ?>
					</label>
				</input>
			<?php endforeach; ?>
		</div>

		<script>jQuery(document).ready(function($) { $( '[id="input_<?php echo esc_attr( $this->id ); ?>"]' ).buttonset(); });</script>
		<?php
	}
}
