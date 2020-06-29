<?php

namespace GroovyMenu;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class FieldText
 */
class FieldText extends \GroovyMenu\FieldField {

	public function renderField() {
		?>
		<div class="gm-gui__module__ui gm-gui__module__text-wrapper">
			<input data-name="<?php echo esc_attr( $this->name ); ?>" type="text" value="<?php echo esc_attr( $this->getValue() ); ?>"
			       name="<?php echo esc_attr( $this->getName() ); ?>" data-default="<?php echo esc_attr( $this->getDefault() ); ?>">
		</div>
		<?php
	}

}
