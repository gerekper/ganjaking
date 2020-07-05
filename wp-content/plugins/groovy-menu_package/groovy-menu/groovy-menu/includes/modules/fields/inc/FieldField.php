<?php

namespace GroovyMenu;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class FieldField
 */
class FieldField {
	protected $name;
	protected $field;
	protected $categoryName;

	/**
	 * FieldField constructor.
	 *
	 * @param $categoryName
	 * @param $name
	 * @param $field
	 */
	public function __construct( $categoryName, $name, $field ) {
		$this->categoryName = $categoryName;
		$this->field        = $field;
		$this->name         = $name;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'menu[' . $this->categoryName . '][' . $this->name . ']';
	}


	/**
	 * Return field array
	 *
	 * @return array
	 */
	public function getArrayData() {
		return $this->field;
	}


	/**
	 * Return field type
	 *
	 * @return string
	 */
	public function getFieldType() {
		return $this->field['type'];
	}

	public function renderField() {

	}

	/**
	 * Render front-end
	 */
	public function render() {
		?>
		<div
			class="gm-gui__module<?php echo ( isset( $this->field['type'] ) && $this->field['type'] === 'hiddenInput' ) ? ' gm-gui__module--hidden' : ''; ?>"
			<?php echo ( isset( $this->field['condition'] ) ) ? ' data-condition=\'' . json_encode( $this->field['condition'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ) . '\'' : ''; ?>
			<?php echo ( isset( $this->field['condition_type'] ) ) ? ' data-condition_type="' . $this->field['condition_type'] . '" ' : ''; ?>
			>

			<span class="gm-gui__module__title"><?php echo __( $this->field['title'] ); ?></span>
			<?php

			if ( isset( $this->field['description'] ) && ! empty( $this->field['description'] ) ) {
				?>
				<p class="gm-gui__module__info"><?php echo __( $this->field['description'] ); ?></p>
				<?php
			}
			$this->renderField();
			?>
		</div>
		<?php
	}

	/**
	 * Default get value method
	 *
	 * @return null|string
	 */
	public function getValue() {

		$val = isset( $this->field['value'] ) ? $this->field['value'] : null;

		if ( is_bool( $val ) ) {
			return $val;
		}

		if ( ! is_null( $val ) ) {

			if ( is_array( $val ) ) {
				$val = stripslashes( wp_json_encode( $val ) );
			}

			return $val;
		}

		return $this->getDefault();
	}

	/**
	 * Default get value method for some fields
	 *
	 * @return null|string
	 */
	public function getValueId() {

		return $this->getValue();
	}

	/**
	 * Return default sub-field from option
	 *
	 * @return null|string
	 */
	public function getDefault() {
		if ( isset( $this->field['default'] ) ) {
			return stripslashes( $this->field['default'] );
		}

		return null;
	}
}
