<?php

namespace GroovyMenu;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class FieldSelect
 */
class FieldSelect extends \GroovyMenu\FieldField {
	public function renderField() {
		?>
		<div class="gm-gui__module__ui gm-gui__module__select-wrapper">
			<select data-value="<?php echo esc_attr( $this->getValue() ); ?>" data-name="<?php echo esc_attr( $this->name ); ?>"
			        class="gm-select" name="<?php echo esc_attr( $this->getName() ); ?>"
			        data-default="<?php echo esc_attr( $this->getDefault() ); ?>">
				<?php foreach ( $this->field['options'] as $key => $option ) {
					$optionName = $option;
					if ( is_array( $option ) ) {
						$optionName = $option['title'];
					}
					?>

					<option
						<?php echo ( is_array( $option ) and isset( $option['condition'] ) ) ? ' data-condition=\'' . wp_json_encode( $option['condition'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ) . '\'' : ''; ?>
						<?php echo ( is_array( $option ) and isset( $option['condition_type'] ) ) ? ' data-condition_type="' . $option['condition_type'] . '" ' : ''; ?>
						value="<?php echo esc_attr( $key ); ?>"
						<?php echo ( strval( $this->getValue() ) === strval( $key ) ) ? 'selected' : ''; ?>
					><?php echo esc_html( $optionName ); ?></option>
				<?php } ?>
			</select>
		</div>
		<?php
	}
}
