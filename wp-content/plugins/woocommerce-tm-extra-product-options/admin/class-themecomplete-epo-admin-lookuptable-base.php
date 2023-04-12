<?php
/**
 * Extra Product Options admin setup
 *
 * @package Extra Product Options/Admin
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options admin lookup table setup
 *
 * @package Extra Product Options/Admin
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_Admin_LookupTable_Base {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_Admin_LookupTable_Base|null
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 6.1
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 6.1
	 */
	public function __construct() {
		// Load scripts.
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 11 );

		add_action( 'admin_footer', [ $this, 'script_templates' ] );

		add_action( 'wp_ajax_tc_lookup_table_import', [ $this, 'import' ] );
		add_action( 'wp_ajax_tc_lookup_table_export', [ $this, 'export' ] );

		// save meta data.
		add_action( 'save_post', [ $this, 'tm_save_postdata' ], 1, 2 );

	}

	/**
	 * Save our meta data
	 *
	 * @param integer $post_id The post id.
	 * @param object  $post_object The post object.
	 * @since 6.1
	 */
	public function tm_save_postdata( $post_id, $post_object ) {
		if ( empty( $_POST ) || ! isset( $_POST['post_type'] ) || ( THEMECOMPLETE_EPO_LOOKUPTABLE_POST_TYPE !== $_POST['post_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		$this->tm_save_postdata_do( $post_id, $post_object );
	}

	/**
	 * Save meta data
	 *
	 * @param integer $post_id The post id.
	 * @param object  $post_object The post object.
	 * @since 6.1
	 */
	public function tm_save_postdata_do( $post_id, $post_object ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		if ( 'revision' === $post_object->post_type ) {
			return;
		}
		check_admin_referer( 'update-post_' . $post_id );

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( isset( $_POST['lookuptable_meta_changed'] ) ) {
			$tm_metas = wp_unslash( $_POST['lookuptable_meta_changed'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$tm_metas = rawurldecode( $tm_metas );
			$tm_metas = nl2br( $tm_metas );
			$tm_metas = json_decode( $tm_metas, true );

			if ( ! empty( $tm_metas ) && is_array( $tm_metas ) ) {
				$old_data = themecomplete_get_post_meta( $post_id, 'lookuptable_meta', true );
				$save     = themecomplete_save_post_meta( $post_id, $tm_metas, $old_data, 'lookuptable_meta' );
			}
		}

	}

	/**
	 * Import a lookup table.
	 *
	 * @since 6.1
	 */
	public function import() {

		$csv = new THEMECOMPLETE_EPO_ADMIN_CSV();
		$csv->lookuptable_import();

	}

	/**
	 * Export a lookup table.
	 *
	 * @since 6.3
	 */
	public function export() {

		$csv = new THEMECOMPLETE_EPO_ADMIN_CSV();
		$csv->export_lookuptable( 'metaserialized' );

	}

	/**
	 * Print script templates
	 *
	 * @since 6.1
	 */
	public function script_templates() {
		// The check is required in case other plugin do things that don't load the wc_get_template function.
		if ( function_exists( 'wc_get_template' ) ) {
			wc_get_template( 'tc-js-admin-templates.php', [], null, THEMECOMPLETE_EPO_PLUGIN_PATH . '/assets/js/admin/' );
		}

	}

	/**
	 * Load scripts
	 *
	 * @param string $hook_suffix The current admin page.
	 * @since 6.1
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		if ( in_array( $hook_suffix, [ 'post.php', 'post-new.php' ], true ) ) {
			$screen = get_current_screen();
			if ( is_object( $screen ) && THEMECOMPLETE_EPO_LOOKUPTABLE_POST_TYPE === $screen->post_type ) {
				$this->register_admin_scripts();
			}
		}
	}

	/**
	 * Enqueue plugin scripts and dequeue unwanted woocommerce scripts
	 *
	 * @since 6.1
	 */
	public function register_admin_scripts() {
		global $wp_query, $post;
		$ext = '.min';
		if ( 'dev' === THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode ) {
			$ext = '';
		}
		THEMECOMPLETE_EPO_ADMIN_GLOBAL()->register_admin_styles( 1 );
		wp_enqueue_style( 'themecomplete-lookuptable-admin', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/admin/tm-lookup-table-admin' . $ext . '.css', false, THEMECOMPLETE_EPO_VERSION );

		wp_register_script( 'themecomplete-api', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-api' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, true );
		wp_register_script( 'jquery-tcfloatbox', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.tcfloatbox' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, true );
		wp_register_script( 'jquery-tctooltip', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.tctooltip' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, true );
		wp_register_script( 'themecomplete-tabs', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.tctabs' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, true );
		wp_register_script( 'toastr', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/admin/toastr' . $ext . '.js', '', '2.1.4', true );
		wp_register_script(
			'themecomplete-epo-admin-lookuptable',
			THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/admin/tm-epo-admin-lookuptable' . $ext . '.js',
			[
				'jquery',
				'json2',
				'wp-util',
				'themecomplete-api',
				'toastr',
				'themecomplete-tabs',
				'jquery-tcfloatbox',
				'jquery-tctooltip',
				'plupload-all',
			],
			THEMECOMPLETE_EPO_VERSION,
			true
		);

		$post_id = isset( $post->ID ) ? floatval( $post->ID ) : '';
		$meta    = themecomplete_get_post_meta( $post_id, 'lookuptable_meta', true );
		$params  = [
			'post_id'                                   => sprintf( '%d', $post_id ),
			'import_nonce'                              => wp_create_nonce( 'import-nonce' ),
			'export_nonce'                              => wp_create_nonce( 'export-nonce' ),
			'tm_epo_global_displayed_decimal_separator' => get_option( 'tm_epo_global_displayed_decimal_separator' ),
			'currency_format_decimal_sep'               => esc_attr( stripslashes_deep( get_option( 'woocommerce_price_decimal_sep' ) ) ),

			// WPML 3.3.x fix.
			'ajax_url'                                  => strtok( admin_url( 'admin-ajax' . '.php' ), '?' ), // phpcs:ignore Generic.Strings.UnnecessaryStringConcat
			'i18n_invalid_request'                      => esc_html__( 'Invalid request!', 'woocommerce-tm-extra-product-options' ),
			'i18n_import_title'                         => esc_html__( 'Importing data', 'woocommerce-tm-extra-product-options' ),
			'i18n_epo'                                  => esc_html__( 'Extra Product Options', 'woocommerce-tm-extra-product-options' ),
			'i18n_importing'                            => esc_html__( 'Importing csv...', 'woocommerce-tm-extra-product-options' ),
			'i18n_saving'                               => esc_html__( 'Saving... Please wait.', 'woocommerce-tm-extra-product-options' ),
			'i18n_update'                               => esc_html__( 'Update', 'woocommerce-tm-extra-product-options' ),
			'i18n_cancel'                               => esc_html__( 'Cancel', 'woocommerce-tm-extra-product-options' ),
			'i18n_invalid_csv'                          => esc_html__( 'Invalid CSV table!', 'woocommerce-tm-extra-product-options' ),
			'i18n_error_title'                          => esc_html__( 'Error', 'woocommerce-tm-extra-product-options' ),
			'i18n_error_message'                        => esc_html__( 'An error has occurred!', 'woocommerce-tm-extra-product-options' ),
			'i18n_overwrite_existing_tables'            => esc_html__( 'Overwrite existing tables', 'woocommerce-tm-extra-product-options' ),
			'lookuptable'                               => $meta,
		];
		wp_localize_script( 'themecomplete-epo-admin-lookuptable', 'TMEPOADMINLOOKUPJS', $params );
		wp_enqueue_script( 'themecomplete-epo-admin-lookuptable' );
	}

	/**
	 * Submenu "Lookup Table"
	 *
	 * @since 6.1
	 */
	public function preload_lookuptable_settings() {
		// Look up table meta box.
		add_meta_box( 'tmformfieldsbuilder', esc_html__( 'Look up table', 'woocommerce-tm-extra-product-options' ), [ $this, 'tm_lookup_tables_meta_box' ], THEMECOMPLETE_EPO_LOOKUPTABLE_POST_TYPE, 'normal', 'core' );
	}

	/**
	 * Lookup tables meta box
	 *
	 * @param object $post The post object.
	 * @since 6.1
	 */
	public function tm_lookup_tables_meta_box( $post ) {

		?>
		<div id="tmformfieldsbuilderwrap" class="tc-wrapper">
			<?php
			echo '<input id="builder_import_file" name="builder_import_file" type="file" class="builder-import-file">';
			echo '<div class="builder-layout tm-hidden builder-lookuptable">';
			$this->print_saved_lookuptable( $post->ID );
			echo '</div>';

			echo '<div id="tc-welcome" class="tc-welcome">';
			echo '<div class="tc-info-text">'
				. esc_html__( 'No CSV table found!', 'woocommerce-tm-extra-product-options' )
				. '<br><small>'
				. esc_html__( 'Import one by clicking the button below.', 'woocommerce-tm-extra-product-options' )
				. '</small></div>';
			echo '</div>';
			echo '<div class="tc-buttons">';
			echo '<button type="button" class="tm-animated tc-add-import-csv tc tc-button large" title="' . esc_html__( 'Import CSV', 'woocommerce-tm-extra-product-options' ) . '"><span class="tc-button-label">' . esc_html__( 'Import CSV', 'woocommerce-tm-extra-product-options' ) . '</span></button>';
			echo '<button type="button" class="tm-animated tc-add-export-csv tc tc-button large" title="' . esc_html__( 'Export CSV', 'woocommerce-tm-extra-product-options' ) . '"><span class="tc-button-label">' . esc_html__( 'Export CSV', 'woocommerce-tm-extra-product-options' ) . '</span></button>';
			echo '</div>';
			?>
		</div>
		<?php
	}

	/**
	 * Generates the saved lookup table.
	 *
	 * @param integer $post_id The current post id.
	 * @since  6.1
	 * @access public
	 */
	public function print_saved_lookuptable( $post_id = 0 ) {

		$builder = themecomplete_get_post_meta( $post_id, 'lookuptable_meta', true );

		if ( ! is_array( $builder ) ) {
			$builder = [];
		}

		if ( ! empty( $post_id ) && is_array( $builder ) && count( $builder ) > 0 ) {
			$lookup_tables = [];
			$lookuptables  = THEMECOMPLETE_EPO()->fetch_all_lookuptables();
			if ( $lookuptables ) {
				foreach ( $lookuptables as $table ) {
					$meta = themecomplete_get_post_meta( $table->ID, 'lookuptable_meta', true );
					if ( ! is_array( $meta ) ) {
						$meta = [];
					}
					foreach ( $meta as $table_name => $table_data ) {
						$index = 0;
						if ( isset( $lookup_tables[ $table_name ] ) ) {
							$index = count( $lookup_tables[ $table_name ] );
						}
						$lookup_tables[ $table_name ][ $table->ID ] = $index;
					}
				}
			}
			echo '<div class="lookuptable-wrapper">';
			echo '<div class="container-lookup-table">';
			echo '<div class="wrap-lookup-table">';
			$decimal_separator = wc_get_price_decimal_separator();
			foreach ( $builder as $table_name => $data ) {
				$table_index = isset( $lookup_tables[ $table_name ][ $post_id ] ) ? $lookup_tables[ $table_name ][ $post_id ] : 0;
				$rows        = [];
				echo '<div class="lookuptable-name">';
				echo '<div class="table-name">';
				echo '<span class="table-name-label">' . esc_html__( 'Table name', 'woocommerce-tm-extra-product-options' ) . '</span>';
				echo '<span contenteditable="true" class="table-name-value">' . esc_html( $table_name ) . '</span>';
				echo '</div>';
				if ( isset( $lookup_tables[ $table_name ] ) && count( $lookup_tables[ $table_name ] ) > 1 ) {
					echo '<div class="table-index">';
					echo '<span class="table-index-label">' . esc_html__( 'Table index', 'woocommerce-tm-extra-product-options' ) . '</span>';
					echo '<span class="table-index-value">' . esc_html( $table_index ) . '</span>';
					echo '</div>';
				}
				echo '</div>';
				echo '<div class="lookup-table-wrap epo">';
				echo '<table class="lookup-table" data-vertable="epo">';
				echo '<thead>';
				echo '<tr class="row head">';
				echo '<th class="ltcell column1" data-row="1" data-column="1"></th>';
				$counter = 1;
				foreach ( $data as $x => $y_data ) {
					$counter++;
					foreach ( $y_data as $y => $cell ) {
						$rows[ $y ][] = $cell;
					}
					echo '<th contenteditable="true" class="ltcell column' . esc_attr( $counter ) . '" data-row="1" data-column="' . esc_attr( $counter ) . '">' . esc_html( str_replace( '.', $decimal_separator, $x ) ) . '</th>';
				}
				echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				$row = 1;
				foreach ( $rows as $row1 => $rown ) {
					$counter = 1;
					$row++;
					echo '<tr class="row row' . esc_attr( $row ) . '">';
					echo '<td contenteditable="true" class="ltcell column' . esc_attr( $counter ) . '" data-row="' . esc_attr( $row ) . '" data-column="' . esc_attr( $counter ) . '">' . esc_html( str_replace( '.', $decimal_separator, $row1 ) ) . '</td>';
					foreach ( $rown as $cell_data ) {
						$counter++;
						echo '<td contenteditable="true" class="ltcell column' . esc_attr( $counter ) . '" data-row="' . esc_attr( $row ) . '" data-column="' . esc_attr( $counter ) . '">' . esc_html( str_replace( '.', $decimal_separator, $cell_data ) ) . '</td>';
					}
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
				echo '</div>';
			}
			echo '</div>';
			echo '</div>';
			echo '</div>';
		}
	}

	/**
	 * Save imported CSV
	 *
	 * @param integer $post_id The post id.
	 * @since 6.1
	 */
	public function save_imported_csv( $post_id ) {

		if ( empty( $post_id ) ) {
			return false;
		}

		$import = get_transient( 'tc_lookuptable_import_csv_' . $post_id );

		if ( ! empty( $import ) ) {

			delete_transient( 'tc_lookuptable_import_csv_' . $post_id );

			$meta = 'lookuptable_meta';
			$post = get_post( $post_id );
			if ( $post && property_exists( $post, 'ID' ) && property_exists( $post, 'post_type' ) ) {
				$old_data = themecomplete_get_post_meta( $post_id, $meta, true );
				themecomplete_save_post_meta( $post_id, $import, $old_data, $meta );
				$this->print_saved_lookuptable( $post_id );
			}
		}

	}
}
