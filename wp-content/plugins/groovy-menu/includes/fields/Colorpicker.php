<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuFieldColorpicker
 */
class GroovyMenuFieldColorpicker extends GroovyMenuFieldField {
	/**
	 * Render
	 */
	public function renderField() {
		$alpha_escaped = ( isset( $this->field['alpha'] ) && $this->field['alpha'] ) ? ' data-alpha="true" data-reset-alpha="true"' : '';
		?>
		<div class="gm-gui__module__ui gm-gui__module__colorpicker">
			<input data-name="<?php echo esc_attr( $this->name ); ?>" type="text" name="<?php echo esc_attr( $this->getName() ); ?>" class="gm-colorpicker" value="<?php echo esc_attr( $this->getValue() ); ?>" data-default="<?php echo esc_attr( $this->getDefault() ); ?>" <?php echo $alpha_escaped; ?>/>
		</div>
		<?php
	}
}
