<?php

namespace GroovyMenu;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class FieldHeader
 */
class FieldHeader extends \GroovyMenu\FieldField {
	const VALUE_NOT_FOUND = 'value not found';

	public function renderField() {
		$val = $this->getValue();
		if ( is_array( $val ) ) {
			$val = stripslashes( wp_json_encode( $val ) );
		}
		$lver = false;
		if ( defined( 'GROOVY_MENU_LVER' ) && '2' === GROOVY_MENU_LVER ) {
			$lver = true;
		}
		?>
		<div id="gm-gui__header-types" class="gm-gui__module__ui">
			<div class="gm-gui__header-types__options">
				<input type="hidden" data-align="<?php echo esc_attr( $this->getAlign() ); ?>"
				       data-toolbar="<?php echo esc_attr( $this->getToolbar() ); ?>"
				       data-style="<?php echo esc_attr( $this->getStyle() ); ?>" data-name="<?php echo esc_attr( $this->name ); ?>"
				       id="gm-gui__header-types__options" class="gm-header" name="<?php echo esc_attr( $this->getName() ); ?>"
				       value="<?php echo esc_attr( $val ); ?>" data-default='<?php echo esc_attr( stripslashes( $this->getDefault() ) ); ?>'/>

				<div class="gm-gui__header-types__options__list gm-gui__module__select-wrapper">
					<div class="gm-gui__header-types__title"><?php esc_html_e( 'Type', 'groovy-menu' ); ?></div>
					<select class="gm-select">
						<option value="1" selected><?php esc_html_e( 'Classic', 'groovy-menu' ); ?></option>
						<option value="2"><?php esc_html_e( 'Minimalistic', 'groovy-menu' ); ?></option>
						<?php if ( ! $lver ) : ?>
						<option value="3"><?php esc_html_e( 'Sidebar', 'groovy-menu' ); ?></option>
						<option value="4"><?php esc_html_e( 'Icon sidebar', 'groovy-menu' ); ?></option>
						<?php endif; ?>
					</select>
				</div>

				<div class="gm-gui__header-types__options__align" data-condition="<?php echo esc_attr( '[["logo_type","in",["img","text"]]]' ); ?>">
					<div class="gm-gui__header-types__title"><?php esc_html_e( 'Logo Align', 'groovy-menu' ); ?></div>
					<span rel="left" class="gm-gui__header-types__options__align--left"><i class="fa fa-align-left"></i></span>
					<span rel="center" class="gm-gui__header-types__options__align--center"><i class="fa fa-align-center"></i></span>
					<span rel="right" class="gm-gui__header-types__options__align--right"><i class="fa fa-align-right"></i></span>
				</div>

				<div class="gm-gui__header-types__options__toolbar-toggle">
					<div class="gm-gui__header-types__title"><?php esc_html_e( 'Toolbar', 'groovy-menu' ); ?></div>
					<div class="gm-gui__module__switch-wrapper">
						<input type="checkbox" class="switch gm-gui__header-types__options__toolbar-toggle__input" id="switch-toolbar-toggle">
					</div>
				</div>
			</div>

			<div class="gm-gui__header-types__preview-wrapper">
				<div class="gm-gui__header-types__preview"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * @return array|mixed|null|object|string
	 */
	public function getValue() {
		return json_decode( stripslashes( parent::getValue() ), true );
	}

	/**
	 * @return string
	 */
	public function getAlign() {
		$align = $this->getValueFromJson( 'align' );

		return ( $align != self::VALUE_NOT_FOUND ) ? $align : 'left';
	}


	/**
	 * @return string
	 */
	public function getStyle() {
		$style = $this->getValueFromJson( 'style' );

		return ( $style != self::VALUE_NOT_FOUND ) ? $style : '1';
	}

	/**
	 * @return string
	 */
	public function getToolbar() {
		$toolbar = $this->getValueFromJson( 'toolbar' );

		return ( $toolbar != self::VALUE_NOT_FOUND ) ? $toolbar : 'false';
	}

	/**
	 * @param $key
	 *
	 * @return string
	 */
	public function getValueFromJson( $key ) {
		$settings = $this->getValue();
		if ( isset( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}

		return self::VALUE_NOT_FOUND;
	}
}
