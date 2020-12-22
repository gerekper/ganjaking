<?php
/**
* Class for Range Control.
*
* @since  1.0.23
* @access public
*/
class LoginPress_Range_Control extends WP_Customize_Control {

	/**
  * The type of customize control being rendered.
  *
  * @since  1.0.23
  * @access public
  * @var    string
  */
	public $type = 'loginpress-range';

	/**
  * Default for the Controler
  *
  * @since  1.0.23
  * @access public
  * @var    string
  */
  public $default;

	/**
  * Unit for the Controler
  *
  * @since  1.0.23
  * @access public
  * @var    string
  */
  public $unit = 'px';

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since 1.0.23
   * @access public
   * @return void
	 */
	public function enqueue() {

		wp_enqueue_script( 'loginpress-range-control-js', LOGINPRESS_DIR_URL . 'js/controls/loginpress-range-control.js', array( 'jquery' ), LOGINPRESS_VERSION, true );

		wp_enqueue_style( 'loginpress-range-control-css', LOGINPRESS_DIR_URL . 'css/controls/loginpress-range-control.css', array(), LOGINPRESS_VERSION );
	}

	/**
  * Displays the control content.
  *
  * @since  1.0.23
  * @access public
  * @return void
  */
	public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<div class="loginpress-range-slider"  style="width:100%; display:flex;flex-direction: row;justify-content: flex-start;">
				<span  style="width:100%; flex: 1 0 0; vertical-align: middle;">
					<span class="loginpress-range-slider_reset"><a type="button" value="reset" class="loginpress-range-reset"></a></span>
					<input class="loginpress-range-slider_range" data-default-value="<?php echo esc_html( $this->default ); ?>" type="range" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->input_attrs(); $this->link(); ?>>
					<input type="text" class="loginpress-range-slider_val" value="<?php echo esc_attr( $this->value() ); ?>" />
					<span><?php echo $this->unit; ?></span>
				</span>
			</div>
			<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
		</label>
		<?php
	}
}
