<?php

namespace GroovyMenu;

use \GroovyMenuUtils as GroovyMenuUtils;
use \GroovyMenuStyle as GroovyMenuStyle;


defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class FieldIcons
 */
class FieldIcons extends \GroovyMenu\FieldField {
	public function renderField() {

		$uploaded_fonts = $this->getFonts();
		$default_packs  = GroovyMenuUtils::get_default_icon_packs_list();

		if ( is_array( $uploaded_fonts ) && ! empty( $uploaded_fonts ) ) {
			foreach ( $uploaded_fonts as $index => $_font ) {
				if ( isset( $_font['name'] ) && isset( $default_packs[ $_font['name'] ] ) ) {
					unset( $default_packs[ $_font['name'] ] );
				}
			}
		}

		?>

		<p class="gm-gui__module__info">
			<?php echo sprintf(
				esc_html__( 'Please upload .ZIP archive created with %1$s service only.', 'groovy-menu' ),
					'<a href="https://icomoon.io/app/#/select" title="icomoon.io" target="_blank">icomoon.io</a>' ) . ' ' . sprintf( esc_html__( 'How to create font icon package read in %1$s this article %2$s', 'groovy-menu' ),
					'<a href="https://grooni.com/docs/groovy-menu/global-settings/managing-icon-packs/" title="Grooni docs" target="_blank">',
					'</a>'
				);
			?>
		</p>

		<div class="gm-gui__module__ui gm-gui__module__import">
			<button class="gm-upload-icon-pack"
				type="button"><?php esc_html_e( 'Upload icon pack', 'groovy-menu' ); ?></button>
			<input type="hidden" id="gm-replace-field-name" name="gm-replace-field-name" value="">
			<input type="hidden" class="groovy-upload-icon" name="icons">
			<?php if ( ! empty( $default_packs ) ): ?>
				<button class="gm-install-default-icon-pack"
					type="button"><?php esc_html_e( 'Install default icon packs', 'groovy-menu' ); ?></button>
			<?php endif; ?>

			<?php

			$i = 1;
			foreach ( self::getFonts() as $fontName => $font ) {
				?>
				<div class="groovy-iconset" data-name="<?php echo esc_attr( $fontName ); ?>">
					<span class="groovy-iconset-name"><?php echo esc_html( $font['name'] ); ?> (<?php echo esc_attr( $fontName ); ?>)</span>
					<button class="groovy-replace-icon-pack"><?php esc_html_e( 'Replace', 'groovy-menu' ); ?></button>
					<button class="groovy-delete-font"><?php esc_html_e( 'delete', 'groovy-menu' ); ?></button>
					<div class="groovy-icons">
						<?php
						foreach ( $font['icons'] as $icon ) {
							echo '<span class="' . esc_attr( $fontName ) . '-' . esc_attr( $icon['name'] ) . '"></span>';
						}
						?>
					</div>
				</div>
				<?php
				$i ++;
			}
			?>
		</div>
		<?php
	}

	/**
	 * @return array
	 */
	public static function getIcons() {
		$icons = array();
		foreach ( self::getFonts() as $name => $font ) {
			foreach ( $font['icons'] as $icon ) {
				$icon['class'] = $name . '-' . $icon['name'];
				$icons[]       = $icon;
			}
		}

		return $icons;
	}

	/**
	 * @return string
	 */
	public static function getStyles() {
		$styles = '';
		foreach ( self::getFonts() as $name => $font ) {
			$styles .= '<link rel="stylesheet" href="' . esc_url( GroovyMenuUtils::getUploadUri() . 'fonts/' . $name . '.css' ) . '?fontname=1" />';
		}

		return $styles;
	}

	/**
	 * @return mixed
	 */
	public static function getFonts() {
		return get_option( GroovyMenuStyle::OPTION_NAME . '_fonts', array() );
	}

	/**
	 * @param $fonts
	 */
	public static function setFonts( $fonts ) {
		update_option( GroovyMenuStyle::OPTION_NAME . '_fonts', $fonts );
	}

	/**
	 * @param $fontName
	 *
	 * @return null
	 */
	public static function getFontByName( $fontName ) {
		$fonts = self::getFonts();
		if ( isset( $fonts[ $fontName ] ) ) {
			return $fonts[ $fontName ][0];
		}

		return null;
	}

}
