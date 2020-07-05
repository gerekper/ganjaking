<?php

namespace GroovyMenu;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class FieldHiddenInput
 */
class FieldHiddenInput extends \GroovyMenu\FieldField {

	public function renderField() {
		?>
		<input data-name="<?php echo esc_attr( $this->name ); ?>" type="hidden" value="<?php echo esc_attr( $this->getValue() ); ?>" name="<?php echo esc_attr( $this->getName() ); ?>" data-default="<?php echo esc_attr( $this->getDefault() ); ?>">
		<?php
	}

}
