<?php

namespace GroovyMenu;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class FieldCheckbox
 */
class FieldCheckbox extends \GroovyMenu\FieldField {

	/**
	 * Render
	 */
	public function renderField() {
		?>
		<div class="gm-gui__module__ui gm-gui__module__switch-wrapper">
			<span class="gm-gui__module__switch__info"><?php esc_html_e( 'off', 'groovy-menu' ); ?></span>
			<input type="hidden" class="switch" value="" name="<?php echo esc_attr( $this->getName() ); ?>">
			<input data-name="<?php echo esc_attr( $this->name ); ?>" type="checkbox" class="switch" value="1" name="<?php echo esc_attr( $this->getName() ); ?>" data-default="<?php echo esc_attr( $this->getDefault() ); ?>" <?php echo ( $this->getValue() === true ) ? 'checked' : ''; ?>>
			<span class="gm-gui__module__switch__info"><?php esc_html_e( 'on', 'groovy-menu' ); ?></span>
		</div>
		<?php
	}

	/**
	 * Get value
	 *
	 * @return bool
	 */
	public function getValue() {
		$value = parent::getValue();

		if ( 'false' === $value || '0' === $value ) {
			$value = '';
		}
		$value = empty( $value ) ? false : true;

		return $value;
	}
}
