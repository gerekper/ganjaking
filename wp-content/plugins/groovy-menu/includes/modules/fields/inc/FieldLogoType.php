<?php

namespace GroovyMenu;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class FieldLogoType
 */
class FieldLogoType extends \GroovyMenu\FieldField {

	public function renderField() {
		?>
		<div class="gm-gui__module__ui gm-gui__module__logotype-wrapper">
			<?php foreach ( $this->field['options'] as $key => $option ) { ?>
				<label
					class="gm-gui__logotype<?php echo ( $this->getValue() === $key ) ? ' gm-gui__logotype--selected' : ''; ?>">
					<img src="<?php echo GROOVY_MENU_URL; ?>assets/images/logo-type-<?php echo esc_attr( $key ); ?>.png" alt="">
					<input data-name="<?php echo esc_attr( $this->name ); ?>" type="radio"
					       name="<?php echo esc_attr( $this->getName() ); ?>"
					       value="<?php echo esc_attr( $key ); ?>" <?php echo ( $this->getValue() === $key ) ? 'checked' : ''; ?>>
				</label>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Get value
	 *
	 * @return null|string
	 */
	public function getValue() {
		if ( isset( $this->field['value'] ) ) {
			return $this->field['value'];
		}

		return $this->getDefault();
	}
}
