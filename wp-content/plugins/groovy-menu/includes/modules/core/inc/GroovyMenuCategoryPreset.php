<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuCategoryPreset
 */
class GroovyMenuCategoryPreset {

	const meta_name      = 'gm_custom_preset_id';
	const meta_menu_name = 'gm_custom_menu_id';

	protected $taxonomies = array();

	/**
	 * GroovyMenuCategoryPreset constructor.
	 *
	 * @param $taxonomies
	 */
	public function __construct( $taxonomies = array() ) {
		global $gm_supported_module;

		if ( ! is_array( $taxonomies ) ) {
			$taxonomies = array();
		}

		if ( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
			foreach ( $taxonomies as $tax ) {
				if ( empty( $this->taxonomies[ $tax ] ) ) {
					$this->taxonomies[ $tax ] = $tax;
				}
			}
		}

		if ( ! empty( $gm_supported_module['categories'] ) && is_array( $gm_supported_module['categories'] ) ) {
			foreach ( $gm_supported_module['categories'] as $category ) {
				if ( empty( $this->taxonomies[ $category ] ) ) {
					$this->taxonomies[ $category ] = $category;
				}
			}
		}

		$lver = false;
		if ( defined( 'GROOVY_MENU_LVER' ) && '2' === GROOVY_MENU_LVER ) {
			$lver = true;
		}

		if ( ! $lver ) {
			add_action( 'init', array( $this, 'init_taxonomies' ), 1010 ); // late init.
		}
	}

	/**
	 * GroovyMenuCategoryPreset init.
	 */
	public function init_taxonomies() {
		$taxonomies = array();

		if ( ! empty( $this->taxonomies ) && is_array( $this->taxonomies ) ) {
			$taxonomies = $this->taxonomies;
		}

		$post_tax = GroovyMenuUtils::getTaxonomiesExtended();

		if ( ! empty( $post_tax ) && is_array( $post_tax ) ) {
			foreach ( $post_tax as $tax_name => $tax_label ) {
				if ( ! in_array( $tax_name, $taxonomies, true ) ) {
					$taxonomies[ $tax_name ] = $tax_name;
				}
			}
		}

		foreach ( $taxonomies as $tax ) {
			add_action( $tax . '_edit_form_fields', array( $this, 'fields' ), 20 );
			add_action( 'edited_' . $tax, array( $this, 'save' ) );
		}
	}

	/**
	 * @param $tag
	 */
	public function fields( $tag ) {
		$savedPreset = get_term_meta( $tag->term_id, self::meta_name, true );
		$savedMenu   = get_term_meta( $tag->term_id, self::meta_menu_name, true );

		$presets = GroovyMenuPreset::getAll();
		$menus   = get_terms( 'nav_menu', array( 'hide_empty' => false ) );

		?>
		<tr class="form-field groovy-menu-fields term_meta__custom_options__field">
			<th scope="row" valign="top">
				<label><?php esc_html_e( 'Groovy Menu', 'groovy-menu' ); ?>: <?php esc_html_e( 'Preset', 'groovy-menu' ); ?></label>
			</th>
			<td>
				<select id="groovy-preset" name="<?php echo esc_attr( self::meta_name ); ?>">
					<option value=""><?php esc_html_e( 'default', 'groovy-menu' ); ?></option>
					<?php foreach ( $presets as $preset ) { ?>
						<option <?php echo ( ! empty( $savedPreset ) && $savedPreset === $preset->id ) ? 'selected' : '' ?>
							value="<?php echo esc_attr( $preset->id ); ?>"><?php echo esc_html( $preset->name ); ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>

		<tr class="form-field groovy-menu-fields term_meta__custom_options__field">
			<th scope="row" valign="top">
				<label><?php esc_html_e( 'Groovy Menu', 'groovy-menu' ); ?>: <?php esc_html_e( 'Navigation menu (from Appearance > Menus)', 'groovy-menu' ); ?></label>
			</th>
			<td>
				<select id="groovy-preset" name="<?php echo esc_attr( self::meta_menu_name ); ?>">
					<option value=""><?php esc_html_e( 'default', 'groovy-menu' ); ?></option>
					<?php foreach ( $menus as $menu ) { ?>
						<option <?php echo ( ! empty( $savedMenu ) && $savedMenu === $menu->slug ) ? 'selected' : '' ?>
							value="<?php echo esc_attr( $menu->slug ); ?>"><?php echo esc_html( $menu->name ); ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>

		<?php

		wp_nonce_field( 'groovymenu_update_taxonomy_meta', 'gm_nonce' );

	}

	/**
	 * @param $term_id
	 */
	public function save( $term_id ) {

		if (
			! isset( $_REQUEST['gm_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['gm_nonce'] ) ), 'groovymenu_update_taxonomy_meta' )
		) {
			return;
		}

		if ( isset( $_POST[ self::meta_name ] ) ) {
			$preset = trim( $_POST[ self::meta_name ] );

			update_term_meta( $term_id, self::meta_name, $preset );

			$used_in_storage = get_option( 'groovy_menu_preset_used_in_storage' );
			if ( empty( $used_in_storage ) ) {
				$used_in_storage = array();
			}
			if ( 'default' === $preset && isset( $used_in_storage['taxonomy'][ intval( $term_id ) ] ) ) {
				unset( $used_in_storage['taxonomy'][ intval( $term_id ) ] );
			} elseif ( intval( $preset ) ) {
				$used_in_storage['taxonomy'][ intval( $term_id ) ] = intval( $preset );
			}
			update_option( 'groovy_menu_preset_used_in_storage', $used_in_storage, false );
		}

		if ( isset( $_POST[ self::meta_menu_name ] ) ) {
			$navMenu = trim( $_POST[ self::meta_menu_name ] );

			update_term_meta( $term_id, self::meta_menu_name, $navMenu );
		}
	}

	/**
	 * @param null $term_id
	 *
	 * @return mixed|null
	 */
	public static function getCurrentPreset( $term_id = null ) {
		if ( empty( $term_id ) ) {

			$current_cat     = get_queried_object();
			$current_term_id = isset( $current_cat->term_id ) ? $current_cat->term_id : null;

			if ( $current_term_id ) {
				$term_id = $current_term_id;
			} else {
				return null;
			}
		}

		return get_term_meta( $term_id, self::meta_name, true );

	}


	/**
	 * @param null $term_id
	 *
	 * @return mixed|null
	 */
	public static function getCurrentNavMenu( $term_id = null ) {
		if ( empty( $term_id ) ) {

			$current_cat     = get_queried_object();
			$current_term_id = isset( $current_cat->term_id ) ? $current_cat->term_id : null;

			if ( $current_term_id ) {
				$term_id = $current_term_id;
			} else {
				return null;
			}
		}

		return get_term_meta( $term_id, self::meta_menu_name, true );

	}
}
