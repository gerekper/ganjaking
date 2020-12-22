<?php
/**
* Class for Background Gallery Control.
*
* @since  1.1.0
* @access public
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Exit if WP_Customize_Control does not exsist.
if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return null;
}

/**
 * This class is for the gallery selector in the Customizer.
 *
 * @access  public
 */
class LoginPress_Background_Gallery_Control extends WP_Customize_Control {

	/**
	 * The control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'loginpress-gallery';

	/**
	 * Enqueue neccessary custom control scripts.
	 */
	public function enqueue() {

		// Custom control scripts.
		// wp_enqueue_script( 'loginpress-gallery-control', LOGINPRESS_DIR_URL . 'js/controls/loginpress-gallery-control.js', array( 'jquery' ), LOGINPRESS_VERSION, true );
	}

	/**
  * Displays the control content.
  *
  * @since  1.1.0
  * @access public
  * @return void
  */
	public function render_content() {

		if ( empty( $this->choices ) )
			return;

		$name = 'loginpress_gallery-' . $this->id; ?>

		<span class="customize-control-title">
      <?php echo esc_attr( $this->label ); ?>
      <?php if ( ! empty( $this->description ) ) : ?>
        <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
      <?php endif; ?>
    </span>

		<div id="loginpress-gallery" class="gallery">
			<?php foreach ( $this->choices as $value ) : ?>
				<div class="loginpress_gallery_thumbnails">
			   	<input id="<?php echo $this->id . esc_attr( $value['id'] ); ?>" class="image-select" type="radio" value="<?php echo esc_attr( $value['id'] ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $value['id'] ); ?> />
					<label for="<?php echo $this->id . esc_attr( $value['id'] ); ?>">
						<div class="gallery_thumbnail_img">
							<img src="<?php echo $value['thumbnail']; ?>" alt="<?php echo esc_attr( $value['id'] ); ?>" title="<?php echo esc_attr( $value['id'] ); ?>">
						</div>
					</label>
				</div>
			<?php endforeach; ?>
		</div>

  <input name='presets_hidden' type="hidden" <?php $this->link(); ?> value="<?php echo  $this->value(); ?>" />

		<?php
	}
}

function loginpress_gallery_control_css() {
  ?>
  <style>
	.loginpress_gallery_thumbnails {
	    width: 33%;
	    float: left;
	    box-sizing: border-box;
	    padding: 4px;
	}
	.loginpress_gallery_thumbnails .gallery_thumbnail_img{
		border-radius: 2px;
		transition: all .4s;
		border: 1px solid transparent;
	}
	.loginpress_gallery_thumbnails .gallery_thumbnail_img img{
		border:2px solid #fff;
		display: block;
		border-radius: 2px;
		width: calc(100% - 4px)
	}
	.customize-control .loginpress_gallery_thumbnails input[type=radio] {
	    display: none;
	}
	.customize-control .loginpress_gallery_thumbnails input[type=radio]:checked + label .gallery_thumbnail_img{
		border-radius: 2px;
		border: 1px solid #36bcf2;
		box-shadow: 0 0 1px #36bcf2;
	}
	</style>
	<?php
}
add_action( 'customize_controls_print_styles', 'loginpress_gallery_control_css' );
?>
