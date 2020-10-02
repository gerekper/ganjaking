<?php

namespace GroovyMenu;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class FieldTextarea
 */
class FieldTextarea extends \GroovyMenu\FieldField {

	/**
	 * Field output code
	 */
	public function renderField() {

		$additioonal_attr_escaped = '';
		$lang_type                = '';
		$codemirror_editor        = false;
		$additional_class         = '';

		if ( isset( $this->field['codemirror_editor'] ) && $this->field['codemirror_editor'] ) {
			$codemirror_editor = $this->field['codemirror_editor'];
			if ( isset( $this->field['lang_type'] ) && $this->field['lang_type'] ) {
				$lang_type = $this->field['lang_type'];
			} else {
				$lang_type = 'css';
			}
		}

		if ( $codemirror_editor ) {
			$additioonal_attr_escaped = 'class="gmCodemirrorInit"';
		}

		$lang_type_escaped = '';
		if ( isset( $lang_type ) && $lang_type ) {
			$lang_type_escaped = 'data-lang_type="' . esc_attr( $lang_type ) . '"';
			if ( $codemirror_editor && 'html' === $lang_type ) {
				$additioonal_attr_escaped = 'class="gmCodemirrorInit gmHtmlTextarea"';
			}
		}

		if ( isset( $this->field['small_height'] ) && $this->field['small_height'] ) {
			$additional_class .= ' gm-textarea-small-height';
		}


		?>
		<div class="gm-gui__module__ui gm-gui__module__text-wrapper<?php echo esc_attr( $additional_class ); ?>">
			<textarea data-name="<?php echo esc_attr( $this->name ); ?>" name="<?php echo esc_attr( $this->getName() ); ?>" <?php echo $additioonal_attr_escaped; ?> data-default="<?php echo esc_attr( $this->getDefault() ); ?>" <?php echo $lang_type_escaped; ?>><?php echo stripslashes( $this->getValue() ); ?></textarea>
		</div>
		<?php
	}
}
