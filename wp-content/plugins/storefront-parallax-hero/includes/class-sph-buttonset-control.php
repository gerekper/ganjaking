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
class SPH_Buttonset_Control extends WP_Customize_Control {

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

		<div id="input_<?php echo esc_attr( $this->id ); ?>" class="sph-buttonset">
			<?php foreach ( $this->choices as $value => $label ) : ?>
				<input class="image-select" type="radio" value="<?php echo esc_attr( $value ); ?>" id="<?php echo esc_attr( $this->id . $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?>>
					<label for="<?php echo esc_attr( $this->id ) . esc_attr( $value ); ?>">
						<?php echo esc_attr( $label ); ?>
					</label>
				</input>
			<?php endforeach; ?>
		</div>

		<style>
			.sph-buttonset {
				background-color: #ddd;
				overflow: hidden;
				zoom: 1;
				display: inline-block;
				border-radius: 3px;
				box-shadow: inset 0 1px 2px rgba(0,0,0,0.15), 0 1px 1px rgba(255,255,255,.5);
			}

			.sph-buttonset label {
				display: inline-block;
				padding: 5px 10px;
				border-right: 1px solid #ccc;
				float: left;
			}

			.sph-buttonset label:first-child {
				border-radius: 3px 0 0 3px;
			}

			.sph-buttonset label:last-child {
				border-right: 0;
				border-radius: 0 3px 3px 0;
			}

			.sph-buttonset label.ui-state-active {
				background-color: #008ec2;
				color: #fff;
				text-shadow: 0 -1px 1px #006799, 1px 0 1px #006799, 0 1px 1px #006799, -1px 0 1px #006799;
				box-shadow: inset 0 0 0 1px rgba(0,0,0,0.2);
			}
		</style>

		<script>
			jQuery(document).ready(function($) {
				var $mediaButtonset            = $( '[id="input_<?php echo esc_attr( $this->id ); ?>"]' );
					$bgImage                   = $( '[id="customize-control-sph_hero_background_image"]' ),
					$bgImageSize               = $( '[id="customize-control-sph_background_size"]' ),
					$bgImageVideo              = $( '[id="customize-control-sph_hero_background_video"]' );
					$bgImageVideoImageFallback = $( '[id="customize-control-sph_hero_background_video_image_fallback"]' );

				var SPHShowHide = function() {
					var value = $mediaButtonset.find( 'input:checked' ).val();

					switch ( value ) {
						case 'none':
							$bgImage.hide();
							$bgImageSize.hide();
							$bgImageVideo.hide();
							$bgImageVideoImageFallback.hide();
							break;
						case 'image':
							$bgImageVideo.hide();
							$bgImage.show();
							$bgImageSize.show();
							$bgImageVideoImageFallback.hide();
							break;
						case 'video':
							$bgImage.hide();
							$bgImageSize.hide();
							$bgImageVideo.show();
							$bgImageVideoImageFallback.show();
							break;
						default:
							$bgImage.hide();
							$bgImageSize.hide();
							$bgImageVideo.hide();
							$bgImageVideoImageFallback.hide();
					}
				};

				$mediaButtonset.buttonset();
				SPHShowHide();

				$( '[id="input_<?php echo esc_attr( $this->id ); ?>"] input' ).on( 'click', function() {
					SPHShowHide();
				});
			});</script>
		<?php
	}
}