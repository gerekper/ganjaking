<?php

namespace GroovyMenu;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class FieldMedia
 */
class FieldMedia extends \GroovyMenu\FieldField {

	/**
	 * Render media field
	 */
	public function renderField() {
		wp_enqueue_media();
		?>
		<div class="gm-gui__module__ui gm-gui__module__media">
			<?php
			$image    = '';
			$image_id = $this->getValue();

			if ( ! empty( $image_id ) ) {
				$image = wp_get_attachment_url( $image_id );
			}

			$thumbnail_src = $image;

			if ( $image_id ) {
				$thumbnail     = wp_get_attachment_image_src( $image_id, 'full' );
				$thumbnail_src = empty( $thumbnail[0] ) ? $image : $thumbnail[0];
			}

			?>
			<input data-name="<?php echo esc_attr( $this->name ); ?>" type="hidden" class="gm-upload-input"
				data-reset="<?php echo ( isset( $this->field['reset'] ) && ! $this->field['reset'] ) ? 'false' : ''; ?>"
				name="<?php echo esc_attr( $this->getName() ); ?>" value="<?php echo esc_attr( $image_id ); ?>"
				data-url="<?php echo esc_url( $image ); ?>" data-thumbnail="<?php echo esc_url( $thumbnail_src ); ?>"/>

			<div class="gm-media-preview"></div>
			<div>
				<input type="button" name="upload-btn" class="gm-upload-btn button-primary" value="<?php esc_html_e( 'Upload Image', 'groovy-menu' ); ?>">
				<input type="button" name="remove-btn" class="gm-remove-btn button-secondary" value="<?php esc_html_e( 'Remove Image', 'groovy-menu' ); ?>">
			</div>
		</div>
		<?php
	}

	/**
	 * Get value
	 *
	 * @return false|null|string
	 */
	public function getValue() {
		$id = parent::getValue();

		return $id;
	}

}
