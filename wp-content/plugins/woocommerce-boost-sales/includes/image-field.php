<?php
if ( ! class_exists( 'VillaTheme_Image_Field' ) ) {
	class VillaTheme_Image_Field {
		var $field_name;
		var $default = '';
		var $current_val = '';

		public function __construct( $field_name, $default, $current_val ) {
			$this->field_name  = $field_name;
			$this->default     = $default;
			$this->current_val = $current_val;
		}

		public function get_field() {
			$field_name  = $this->field_name;
			$current_val = intval( $this->current_val ) ? intval( $this->current_val ) : intval( $this->default );

			ob_start(); ?>
            <div class="vi-media-field">
                <div class="option">
                    <div class="controls">
                        <input value="<?php echo ent2ncr( $current_val ) ?>" name="<?php echo ent2ncr( $field_name ) ?>"
                               class=" upload of-input" type="hidden">

                        <div class="screenshot">
							<?php if ( $current_val ) {
								$src = wp_get_attachment_link( $current_val, 'thumbnail', false );
								?>
								<?php echo $src ?>
							<?php } ?>
                        </div>
                        <div class="upload_button_div">
                            <span class="vi-ui button green media_upload_button"><?php esc_html_e( 'Select Image', 'woocommerce-boost-sales' ) ?></span>
                            <span title="<?php echo esc_attr( $field_name ) ?>"
                                  id="reset_<?php echo esc_attr( $field_name ) ?>"
                                  class="vi-ui button red remove-image "><?php esc_html_e( 'Remove', 'woocommerce-boost-sales' ) ?></span>
                        </div>

                        <div class="clear"></div>
                    </div>

                    <div class="clear"></div>
                </div>
            </div>
			<?php
			$html = ob_get_clean();

			return $html;
		}
	}
}