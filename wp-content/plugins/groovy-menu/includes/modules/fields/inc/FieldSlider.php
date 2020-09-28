<?php

namespace GroovyMenu;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuFieldSlider
 */
class FieldSlider extends \GroovyMenu\FieldField {
	/**
	 * @return mixed
	 */
	public function getMax() {
		return $this->field['range'][1];
	}

	/**
	 * @return mixed
	 */
	public function getMin() {
		return $this->field['range'][0];
	}

	/**
	 * @return int
	 */
	public function getStep() {
		if ( isset( $this->field['step'] ) ) {
			return $this->field['step'];
		}

		return 1;
	}

	/**
	 * @return string
	 */
	public function getUnit() {
		return ( isset( $this->field['unit'] ) ? $this->field['unit'] : '' );
	}

	public function renderField() {
		?>
		<div class="gm-gui__module__ui gm-gui__module__range-wrapper">
			<div class="gm-gui__module__range__range"></div>
			<input data-name="<?php echo esc_attr( $this->name ); ?>" class="gm-gui__module__range__input"
			       name="<?php echo esc_attr( $this->getName() ); ?>" type="text" value="<?php echo esc_attr( $this->getValue() ); ?>"
			       data-default="<?php echo esc_attr( $this->getDefault() ); ?>" data-max="<?php echo esc_attr( $this->getMax() ); ?>"
			       data-min="<?php echo esc_attr( $this->getMin() ); ?>" data-step="<?php echo esc_attr( $this->getStep() ); ?>">
			<span class="gm-gui-unit"><?php echo esc_html( $this->getUnit() ); ?></span>
		</div>
		<?php
	}
}
