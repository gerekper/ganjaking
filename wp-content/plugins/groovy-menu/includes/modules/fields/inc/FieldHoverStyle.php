<?php

namespace GroovyMenu;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class FieldHoverStyle
 */
class FieldHoverStyle extends \GroovyMenu\FieldField {
	public function renderField() {
		$styles = $this->field['options'];
		?>
		<div class="gm-gui__module__ui gm-gui__module__hover-style-wrapper">
			<?php foreach ( $styles as $id => $style ) {
				?>
				<span class="gm-gui__module__hover-style__item" rel="<?php echo esc_attr( $id ); ?>"
					<?php echo ( isset( $style['condition'] ) && is_array( $style['condition'] ) ) ? 'data-condition=\'' . esc_attr( wp_json_encode( $style['condition'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ) ) . '\'' : ''; ?>
					<?php echo ( isset( $style['condition_type'] ) && is_array( $style['condition'] ) ) ? ' data-condition_type="' . $style['condition_type'] . '" ' : '';  ?>
				>
					<img src="<?php echo esc_url( GROOVY_MENU_URL ); ?>assets/images/hover<?php echo esc_attr( $id ); ?>.png" alt="">
				</span>
			<?php } ?>
			<input type="hidden" data-name="<?php echo esc_attr( $this->name ); ?>" value="<?php echo esc_attr( $this->getValue() ); ?>"
			       class="gm-hover-style-input" name="<?php echo esc_attr( $this->getName() ); ?>"
			       data-default="<?php echo esc_attr( $this->getDefault() ); ?>"/>
		</div>
		<?php
	}
}
