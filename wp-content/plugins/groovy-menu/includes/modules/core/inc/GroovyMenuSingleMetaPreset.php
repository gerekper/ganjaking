<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuSingleMetaPreset
 */
class GroovyMenuSingleMetaPreset {

	public $post_types = array();

	public $show_meta_box = true;

	protected $lver = false;

	const meta_name             = 'gm_custom_preset_id';
	const meta_menu_name        = 'gm_custom_menu_id';
	const simple_meta_name      = 'groovy_preset';
	const simple_meta_menu_name = 'groovy_menu_id';

	public function __construct() {

		global $gm_supported_module;
		if ( isset( $gm_supported_module['GroovyMenuSingleMetaPreset'] ) && ! $gm_supported_module['GroovyMenuSingleMetaPreset'] ) {
			$this->show_meta_box = false;
		}

		if ( defined( 'GROOVY_MENU_LVER' ) && '2' === GROOVY_MENU_LVER ) {
			$this->lver = true;
		}

		if ( $this->show_meta_box ) {
			$styles      = new GroovyMenuStyle( null );
			$permissions = $styles->getGlobal( 'permissions', 'post_types' );
			if ( ! empty( $permissions ) ) {
				$this->post_types = explode( ',', $permissions );
			} else {
				$this->post_types = array( 'groovy_menu_empty' );
			}

			add_action( 'add_meta_boxes', array( $this, 'add_meta_box', ) );

		} else {
			$this->post_types = apply_filters( 'groovy_menu_single_post_add_meta_box_post_types', $this->get_all_post_types() );
		}

		add_action( 'save_post', array( $this, 'save_post_meta' ), 10, 2 );

	}


	/**
	 * Add save action.
	 */
	public function add_save_meta_action() {
		add_action( 'init', array( 'GroovyMenuUtils', 'add_groovy_menu_preset_post_type' ), 3 );
	}


	public function add_meta_box() {

		$show_meta_box = apply_filters( 'groovy_menu_single_post_show_meta_box', true );
		if ( ! $show_meta_box ) {
			return;
		}

		if ( ! is_array( $this->post_types ) ) {
			$this->post_types = array();
		}

		add_meta_box(
			'groovy_menu_metabox',
			__( 'Groovy menu', 'groovy-menu' ),
			array( $this, 'meta_box_html' ),
			$this->lver ? $this->get_all_post_types() : $this->post_types,
			'side',
			'default'
		);
	}

	/**
	 * Meta box content.
	 */
	public function meta_box_html() {
		$post    = get_post();
		$post_id = isset( $post->ID ) ? $post->ID : 0;

		$saved_preset = get_post_meta( $post_id, self::meta_name, true );
		$saved_menu   = get_post_meta( $post_id, self::meta_menu_name, true );

		$presets = GroovyMenuPreset::getAll();
		$menus   = get_terms( 'nav_menu', array( 'hide_empty' => false ) );

		?>
		<div class="groovy-meta-box-wrapper">
			<?php if ( ! $this->lver ) { ?>
				<div class="groovy-meta-box-item">
					<div class="groovy-meta-box-item--label">
						<label for="groovy-preset"><?php esc_html_e( 'Menu preset', 'groovy-menu' ); ?></label>
					</div>
					<div class="groovy-meta-box-item--select">
						<select id="groovy-preset" name="<?php echo esc_attr( self::meta_name ); ?>"
							class="groovy-select-maxwidth">
							<option value=""><?php esc_html_e( 'Default', 'groovy-menu' ); ?></option>
							<option
								value="none" <?php echo ( ! empty( $saved_preset ) && $saved_preset === 'none' ) ? 'selected' : '' ?>><?php _e( 'Hide Groovy menu', 'groovy-menu' ); ?></option>
							<?php foreach ( $presets as $preset ) { ?>
								<option <?php echo ( ! empty( $saved_preset ) && $saved_preset === $preset->id ) ? 'selected' : '' ?>
									value="<?php echo esc_attr( $preset->id ); ?>"><?php echo esc_html( $preset->name ); ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			<?php } ?>
			<div class="groovy-meta-box-item">
				<div class="groovy-meta-box-item--label">
					<label
						for="groovy-menu-name"><?php esc_html_e( 'Navigation menu (from Appearance > Menus)', 'groovy-menu' ); ?></label>
				</div>
				<div class="groovy-meta-box-item--select">
					<select id="groovy-menu-name" name="<?php echo self::meta_menu_name; ?>"
						class="groovy-select-maxwidth">
						<option value=""><?php esc_html_e( 'Default', 'groovy-menu' ); ?></option>
						<?php foreach ( $menus as $menu ) { ?>
							<option <?php echo ( ! empty( $saved_menu ) && $saved_menu === $menu->slug ) ? 'selected' : '' ?>
								value="<?php echo esc_attr( $menu->slug ); ?>"><?php echo esc_html( $menu->name ); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save data from metabox.
	 *
	 * @param      $post_id
	 * @param null $post
	 *
	 * @return mixed
	 */
	public function save_post_meta( $post_id, $post = null ) {

		// $post_id and $post are required.
		if ( empty( $post_id ) ) {
			return $post_id;
		}

		// don't save for Migration process.
		if ( ! empty( $_GET['crane-theme-migrate-job'] ) || ! empty( $_GET['crane-theme-migrate'] ) || ( defined( 'CRANE_DOING_MIGRATE_JOB' ) && CRANE_DOING_MIGRATE_JOB ) ) { // @codingStandardsIgnoreLine
			return $post_id;
		}

		if ( defined( 'WP_LOAD_IMPORTERS' ) && WP_LOAD_IMPORTERS ) {
			return $post_id;
		}

		$post_revision = wp_is_post_revision( $post_id );
		$post_autosave = wp_is_post_autosave( $post );

		// Dont' save meta for autosaves.
		if ( ! $post_revision && ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE && is_int( $post_autosave ) ) ) {
			return $post_id;
		}

		// don't save for "Quick Edit".
		if ( ! empty( $_POST['post_ID'] ) && isset( $_POST['action'] ) && 'inline-save' === $_POST['action'] ) {
			return $post_id;
		}

		// don't save for "Bulk Edit".
		if ( isset( $_REQUEST['action'] ) && 'edit' === $_REQUEST['action'] && isset( $_REQUEST['post_status'] ) && 'all' === $_REQUEST['post_status'] && isset( $_REQUEST['bulk_edit'] ) ) { // @codingStandardsIgnoreLine
			return $post_id;
		}

		$current_post_type = isset( $_POST['post_type'] ) ? wp_unslash( trim( $_POST['post_type'] ) ) : null;

		// Check for elementor_ajax.
		$elementor_ajax_post_id = false;
		if ( $post_revision && ! empty( $_POST['editor_post_id'] ) && isset( $_POST['action'] ) && 'elementor_ajax' === $_POST['action'] ) {
			$elementor_ajax_post_id = intval( $_POST['editor_post_id'] ) ? : false;
		}

		// check permissions.
		if ( 'page' === $current_post_type ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( ! in_array( $current_post_type, $this->post_types, true ) && ! $elementor_ajax_post_id ) {
			return $post_id;
		}


		$meta_preset_name = ( $this->show_meta_box ) ? self::meta_name : self::simple_meta_name;
		$new_preset_value = isset( $_POST[ $meta_preset_name ] ) ? $_POST[ $meta_preset_name ] : '';

		$meta_menu_name = ( $this->show_meta_box ) ? self::meta_menu_name : self::simple_meta_menu_name;
		$new_menu_value = isset( $_POST[ $meta_menu_name ] ) ? $_POST[ $meta_menu_name ] : '';

		// get GM meta for elementor_ajax.
		if ( $post_revision && $elementor_ajax_post_id && $elementor_ajax_post_id !== $post_id ) {
			$new_preset_value = get_post_meta( $elementor_ajax_post_id, self::meta_name, true );
			$new_menu_value   = get_post_meta( $elementor_ajax_post_id, self::meta_menu_name, true );
		}


		$revision = false;
		if ( $post_id && $post_revision && isset( $post->ID ) ) {

			$revision = $post->ID;

		} else {

			if (
				! empty( $_POST['wp-preview'] ) && 'dopreview' === $_POST['wp-preview'] &&
				! empty( $_POST['post_status'] ) && 'draft' === $_POST['post_status']
			) {
				$post_revisions = wp_get_post_revisions( $post_id, array( 'numberposts' => 1 ) );

				if ( ! empty( $post_revisions ) ) {
					foreach ( $post_revisions as $revision_object ) {
						$last_revision = $revision_object;
						break;
					}
					$post_id  = isset( $last_revision->ID ) ? $last_revision->ID : $post_id;
					$revision = $post_id;
				}
			}
		}

		/**
		 * Fires before post meta save action.
		 *
		 * @since 1.2.20
		 */
		do_action( 'gm_before_post_meta_save' );


		$used_in_storage = get_option( 'groovy_menu_preset_used_in_storage' );
		if ( empty( $used_in_storage ) ) {
			$used_in_storage = array();
		}
		$post_type = isset( $_POST['post_type'] ) ? esc_attr( wp_unslash( $_POST['post_type'] ) ) : '';

		if ( empty( $post_type ) && ! $elementor_ajax_post_id ) {
			return $post_id;
		}


		if ( $revision ) {
			/*
			 * Use the underlying update_metadata() function vs add_post_meta()
			 * to ensure metadata is added to the revision post and not its parent.
			 */
			update_metadata( 'post', $revision, self::meta_name, $new_preset_value );
			update_metadata( 'post', $revision, self::meta_menu_name, $new_menu_value );

		} else {
			// Remove post meta if it's empty
			if ( empty( $new_preset_value ) || is_array( $new_preset_value ) ) {
				delete_post_meta( $post_id, self::meta_name );
				if ( isset( $used_in_storage['post'][ $post_type ][ intval( $post_id ) ] ) ) {
					unset( $used_in_storage['post'][ $post_type ][ intval( $post_id ) ] );
				}
			} else {
				update_post_meta( $post_id, self::meta_name, $new_preset_value );
				$used_in_storage['post'][ $post_type ][ intval( $post_id ) ] = intval( $new_preset_value );
			}

			update_option( 'groovy_menu_preset_used_in_storage', $used_in_storage, false );

			// Remove post meta if it's empty
			if ( empty( $new_menu_value ) || is_array( $new_menu_value ) ) {
				delete_post_meta( $post_id, self::meta_menu_name );
			} else {
				update_post_meta( $post_id, self::meta_menu_name, $new_menu_value );
			}
		}


		return $post_id;
	}

	/**
	 * @param bool $add_custom_types
	 *
	 * @return array
	 */
	public function get_all_post_types() {
		global $gm_supported_module;
		$post_types = array();

		$work_with = array_merge( groovy_menu_get_post_types(), $gm_supported_module['post_types'] );

		foreach ( $work_with as $post_type => $post_name ) {
			$post_types[] = $post_type;
		}

		return $post_types;
	}

	/**
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	public static function get_preset_id_from_meta( $post_id = 0 ) {

		if ( ! $post_id && ( is_single() || is_singular() || is_page() ) ) {

			$post_id = get_the_ID();

			if ( is_preview() ) {
				$revision       = null;
				$post_revisions = wp_get_post_revisions( $post_id, array( 'numberposts' => 1 ) );

				if ( ! empty( $post_revisions ) ) {
					foreach ( $post_revisions as $revision_object ) {
						$last_revision = $revision_object;
						break;
					}
					$revision = isset( $last_revision->ID ) ? $last_revision->ID : null;
				}
				if ( $revision ) {
					$post_id = $revision;
				}
			}
		}

		global $wp_query;

		if ( ! $post_id && ! empty( $wp_query ) && 'product' === get_query_var( 'post_type' ) ) {
			if ( function_exists( 'wc_get_page_id' ) ) {
				$post_id = wc_get_page_id( 'shop' );
			}
		}

		$post = get_post( $post_id );

		if ( ! $post ) {
			return false;
		}

		return get_post_meta( $post_id, self::meta_name, true );

	}

	/**
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	public static function get_menu_id_from_meta( $post_id = 0 ) {

		if ( ! $post_id && ( is_single() || is_singular() || is_page() ) ) {

			$post_id = get_the_ID();

			if ( is_preview() ) {
				$revision       = null;
				$post_revisions = wp_get_post_revisions( $post_id, array( 'numberposts' => 1 ) );

				if ( ! empty( $post_revisions ) ) {
					foreach ( $post_revisions as $revision_object ) {
						$last_revision = $revision_object;
						break;
					}
					$revision = isset( $last_revision->ID ) ? $last_revision->ID : null;
				}
				if ( $revision ) {
					$post_id = $revision;
				}
			}
		}

		if ( ! $post_id && 'product' === get_query_var( 'post_type' ) ) {
			if ( function_exists( 'wc_get_page_id' ) ) {
				$post_id = wc_get_page_id( 'shop' );
			}
		}

		$post = get_post( $post_id );

		if ( ! $post ) {
			return false;
		}

		return get_post_meta( $post_id, self::meta_menu_name, true );

	}

}
