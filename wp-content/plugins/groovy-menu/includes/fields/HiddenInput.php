<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuFieldHiddenInput
 */
class GroovyMenuFieldHiddenInput extends GroovyMenuFieldField {

	public function renderField() {
		?>
		<input data-name="<?php echo esc_attr( $this->name ); ?>" type="hidden" value="<?php echo esc_attr( $this->getValue() ); ?>" name="<?php echo esc_attr( $this->getName() ); ?>" data-default="<?php echo esc_attr( $this->getDefault() ); ?>">
		<?php
	}

}
