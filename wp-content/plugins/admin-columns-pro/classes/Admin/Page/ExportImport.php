<?php

namespace ACP\Admin\Page;

use AC\Admin\Page;
use AC\ListScreen;
use AC\ListScreenFactory;
use AC\ListScreenGroups;
use AC\Message;
use AC\Registrable;
use ACP;

/**
 * @since 1.4.6.5
 */
class ExportImport extends Page
	implements Registrable {

	const NAME = 'import-export';

	/**
	 * @var string
	 */
	private $php_export_string;

	/**
	 * @since 1.4.6.5
	 */
	public function __construct() {
		parent::__construct( self::NAME, __( 'Export/Import', 'codepress-admin-columns' ) );
	}

	/**
	 * Register Hooks
	 */
	public function register() {
		$this->handle_export();
		$this->handle_import();

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * @param string $action
	 *
	 * @return bool
	 */
	private function verify_nonce( $action ) {
		return wp_verify_nonce( filter_input( INPUT_POST, '_ac_nonce' ), $action );
	}

	/**
	 * @since 1.4.6.5
	 */
	public function handle_export() {
		if ( ! $this->verify_nonce( 'export' ) ) {
			return;
		}

		$export_types = $this->get_exported_types();

		if ( empty( $export_types ) ) {
			$notice = new Message\Notice( __( 'Export field is empty. Please select your types from the left column.', 'codepress-admin-columns' ) );
			$notice
				->set_type( Message::ERROR )
				->register();

			return;
		}

		// PHP
		if ( filter_input( INPUT_POST, 'ac-export-php' ) ) {
			$this->php_export_string = $this->get_php_export_string_by_types( $export_types );
		}

		// JSON
		if ( filter_input( INPUT_POST, 'ac-export-json' ) ) {
			$json = $this->get_json_export_string( $export_types );
			$filename = 'admin-columns-export_' . date( 'Y-m-d' );

			if ( 1 === count( $export_types ) ) {
				$filename .= '_' . $export_types[0];
			}

			$this->create_json_file( $filename, $json );
		}
	}

	/**
	 * @return array
	 */
	private function get_exported_types() {
		return (array) filter_input( INPUT_POST, 'export_types', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	}

	/**
	 * @param string $filename
	 * @param string $json JSON format
	 */
	private function create_json_file( $filename, $json ) {
		header( 'Content-disposition: attachment; filename=' . $filename . '.json' );
		header( 'Content-type: application/json' );

		echo $json;
		exit;
	}

	/**
	 * @param string $message
	 */
	private function notice_error( $message ) {
		$notice = new Message\Notice( $message );
		$notice
			->set_type( Message::ERROR )
			->register();
	}

	/**
	 * @uses  wp_import_handle_upload()
	 * @since 2.0.0
	 */
	public function handle_import() {
		if ( empty( $_FILES['import'] ) || ! $this->verify_nonce( 'file-import' ) ) {
			return;
		}

		$file = wp_import_handle_upload();

		$error = false;

		if ( isset( $file['error'] ) ) {
			$error = __( 'Sorry, there has been an error.', 'codepress-admin-columns' ) . '<br>' . esc_html( $file['error'] );
		} elseif ( ! file_exists( $file['file'] ) ) {
			$error = __( 'Sorry, there has been an error.', 'codepress-admin-columns' ) . '<br>' . sprintf( __( 'The export file could not be found at %s. It is likely that this was caused by a permissions problem.', 'codepress-admin-columns' ), '<code>' . esc_html( $file['file'] ) . '</code>' );
		}

		if ( false !== $error ) {
			$this->notice_error( $error );

			return;
		}

		$content = file_get_contents( $file['file'] );

		// cleanup
		wp_delete_attachment( $file['id'] );

		$columndata = $this->get_decoded_settings( $content );

		if ( empty( $columndata ) ) {
			$this->notice_error( __( 'Import failed. File does not contain Admin Column settings.', 'codepress-admin-columns' ) );

			return;
		}

		foreach ( $columndata as $type => $_data ) {
			$list_screen = ListScreenFactory::create( $type );

			if ( ! $list_screen ) {
				$this->notice_error( sprintf( __( 'Screen %s does not exist.', 'codepress-admin-columns' ), "<strong>{$type}</strong>" ) );

				continue;
			}

			$created_layouts = array();

			$layouts = ACP()->layouts( $list_screen );

			// Create Original layout. The old column settings will not be overwritten but stored in an "Original" layout
			if ( ! $layouts->get_layouts() && $list_screen->get_settings() ) {
				$original_layout = array(
					'name' => __( 'Original', 'codepress-admin-columns' ),
				);
				if ( $layout = $layouts->create( $original_layout, true ) ) {
					$created_layouts[ $layout->get_id() ] = $original_layout['name'];
				}
			}

			$default_layout_data = array(
				'name' => __( 'Imported', 'codepress-admin-columns' ),
			);

			// Determine the import format. New import has layouts, the old import doesn't
			$is_layout_format = isset( $_data[0] );

			// New json format with layouts
			// $_data contains [layouts] and [columns]
			if ( $is_layout_format ) {
				foreach ( $_data as $data ) {
					$layout_id = isset( $data['layout'] ) ? $data['layout'] : $default_layout_data;

					$layout = $layouts->create( $layout_id );

					if ( $layout ) {
						$list_screen->set_layout_id( $layout->get_id() )->store( $data['columns'] );

						$created_layouts[ $layout->get_id() ] = $layout->get_name();
					}
				}
			} // Old json format without layouts
			else if ( $layout = $layouts->create( $default_layout_data ) ) {
				$list_screen->set_layout_id( $layout->get_id() )->store( $_data );

				$created_layouts[ $layout->get_id() ] = $layout->get_name();
			}

			if ( ! $created_layouts ) {
				$this->notice_error( __( 'Import failed.', 'codepress-admin-columns' ) );

				return;
			}

			$links = array();

			foreach ( $created_layouts as $id => $name ) {
				$links[] = ac_helper()->html->link( add_query_arg( 'layout_id', $id, $list_screen->get_edit_link() ), '<strong>' . esc_html( $name ) . '</strong>' );
			}

			$message = sprintf(
				__( 'Succesfully created %s for %s.', 'codepress-admin-columns' ),
				ac_helper()->string->enumeration_list( $links, 'and' ) . ' ' . _n( 'set', 'sets', count( $links ), 'codepress-admin-columns' ),
				"<strong>" . $list_screen->get_label() . "</strong>"
			);

			$notice = new Message\Notice( $message );
			$notice->register();
		}
	}

	/**
	 * @since 3.8
	 */
	private function export_single_layouts() {

		/**
		 * @since 4.0
		 *
		 * @param bool True will display single layout sets. False will export all layouts.
		 */
		return apply_filters( 'acp/export_single_sets', true );
	}

	/**
	 * @param ListScreen $list_screen
	 *
	 * @return array
	 */
	public function get_columndata_by_list_screen( $list_screen ) {
		$columndata = array();

		if ( $columns = $list_screen->get_settings() ) {
			$columndata = array(
				'columns' => $columns,
			);

			if ( $layout = ACP()->layouts( $list_screen )->get_current_layout() ) {
				$columndata['layout'] = $layout->to_array();
			}
		}

		return $columndata;
	}

	/**
	 * @since 3.8
	 *
	 * @param $types
	 *
	 * @return array
	 */
	private function get_export_data( $types ) {
		$data = array();

		foreach ( AC()->get_list_screens() as $list_screen ) {

			$layouts = $layouts = ACP()->layouts( $list_screen )->get_layouts();

			// Individual layouts
			if ( $this->export_single_layouts() ) {

				foreach ( $layouts as $layout ) {
					if ( in_array( $list_screen->get_key() . $layout->get_id(), $types ) ) {
						$list_screen->set_layout_id( $layout->get_id() );

						$data[ $list_screen->get_key() ][] = $this->get_columndata_by_list_screen( $list_screen );
					}
				}
			} // All layouts
			else {
				if ( in_array( $list_screen->get_key(), $types ) ) {
					foreach ( $layouts as $layout ) {
						$list_screen->set_layout_id( $layout->get_id() );

						$data[ $list_screen->get_key() ][] = $this->get_columndata_by_list_screen( $list_screen );
					}
				}
			}

			// No layout
			if ( empty( $data[ $list_screen->get_key() ] ) && in_array( $list_screen->get_key(), $types ) && ( $columns = $list_screen->get_settings() ) ) {
				$data[ $list_screen->get_key() ][] = $this->get_columndata_by_list_screen( $list_screen );
			}
		}

		return array_filter( $data );
	}

	/**
	 * Gets multi select options to use in a HTML select element
	 * @since 2.0.0
	 * @return array Multi select options
	 */
	private function get_export_multiselect_options() {
		$options = array();

		foreach ( AC()->get_list_screens() as $list_screen ) {
			$layouts = ACP()->layouts( $list_screen )->get_layouts();

			// Individual layouts
			if ( $this->export_single_layouts() ) {
				$group = $list_screen->get_singular_label();

				if ( $list_screen instanceof ACP\ListScreen\Taxonomy ) {
					$group = ListScreenGroups::get_groups()->get_group_label( $list_screen->get_group() ) . ' - ' . $group;
				}

				if ( $layouts ) {
					foreach ( $layouts as $layout ) {
						$list_screen->set_layout_id( $layout->get_id() );

						if ( $list_screen->get_settings() ) {
							$label = $list_screen->get_label() . ' - ' . $layout->get_name();

							if ( $layout->is_read_only() ) {
								$label .= ' (' . __( 'read only', 'codepress-admin-columns' ) . ')';
							}

							$options[ $group ][ $list_screen->get_storage_key() ] = $label;
						}
					}
				} else if ( $list_screen->get_settings() ) {
					$options[ $group ][ $list_screen->get_key() ] = $list_screen->get_label();
				}
			} // All layouts
			else {
				$has_stored_columns = false;

				$group = ListScreenGroups::get_groups()->get_group_label( $list_screen->get_group() );

				// Layouts
				if ( $layouts ) {
					foreach ( $layouts as $layout ) {
						if ( $list_screen->set_layout_id( $layout->get_id() )->get_settings() ) {
							$has_stored_columns = true;
							break;
						}
					}
				} // Single
				else if ( $list_screen->get_settings() ) {
					$has_stored_columns = true;
				}

				// Add menu type
				if ( $has_stored_columns ) {
					$options[ $group ][ $list_screen->get_key() ] = $list_screen->get_label();
				}
			}
		}

		return $options;
	}

	/**
	 * @since 2.0.0
	 *
	 * @param array $types
	 *
	 * @return string
	 */
	private function get_json_export_string( $types = array() ) {
		if ( empty( $types ) ) {
			return false;
		}

		$data = $this->get_export_data( $types );

		if ( empty( $data ) ) {
			return false;
		}

		// PHP 5.4 <
		if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
			return json_encode( $data, JSON_PRETTY_PRINT );
		}

		// Older versions of PHP
		return $this->get_pretty_json( $data );
	}

	/**
	 * @param array $columns
	 *
	 * @return string
	 */
	private function get_columns_part( $columns ) {
		$columns_parts = array();

		foreach ( $columns as $column_name => $column ) {
			$properties_parts = array();

			foreach ( $column as $property => $value ) {
				$properties_parts[] = "\t'{$property}' => '{$value}'";
			}

			$columns_string = '';
			$columns_string .= "'{$column_name}' => array(\n";
			$columns_string .= implode( ",\n", $properties_parts ) . "\n";
			$columns_string .= ")";

			$columns_parts[] = $columns_string;
		}

		return implode( ",\n", $columns_parts );
	}

	/**
	 * @param array $layout
	 *
	 * @return string
	 */
	private function get_layout_part( $layout ) {
		$columns_parts = array();

		foreach ( $layout as $k => $value ) {
			if ( $value ) {
				if ( is_array( $value ) ) {
					$value = "'" . implode( "',\n'", $value ) . "'";

					$columns_parts[] = "\t'{$k}' => array( " . $value . " )";
				} else {
					$columns_parts[] = "\t'{$k}' => '{$value}'";
				}
			} else {
				$columns_parts[] = "\t'{$k}' => false";
			}
		}

		$layout_string = "'layout' => array(";
		$layout_string .= "\n" . implode( ",\n", $columns_parts );
		$layout_string .= "\n)\n";

		return $layout_string;
	}

	private function do_indent( $string, $indents = 0 ) {
		$array = explode( "\n", $string );

		foreach ( $array as $k => $item ) {
			$array[ $k ] = str_repeat( "\t", $indents ) . $item;
		}

		return implode( "\n", $array );
	}

	/**
	 * @since 2.0.0
	 *
	 * @param array $types
	 *
	 * @return bool|string
	 */
	private function get_php_export_string_by_types( $types = array() ) {

		if ( empty( $types ) ) {
			return false;
		}

		$exported = $this->get_export_data( $types );

		if ( empty( $exported ) ) {
			return false;
		}

		// callback has to be unique
		$function_id = substr( md5( serialize( $exported ) ), -8 );

		$string = "function ac_custom_column_settings_{$function_id}() {\n";

		foreach ( $exported as $list_screen => $columndata ) {
			$string .= "\n\tac_register_columns( '{$list_screen}', array(\n";

			// Layouts
			if ( isset( $columndata[0] ) ) {

				$layout_parts = array();
				foreach ( $columndata as $data ) {

					if ( ! $data ) {
						continue;
					}

					$layout = '';
					$layout .= "'columns' => array(";
					$layout .= "\n" . $this->do_indent( $this->get_columns_part( $data['columns'] ), 1 );
					$layout .= "\n),\n";

					if ( isset( $data['layout'] ) ) {
						$layout .= $this->get_layout_part( $data['layout'] );
					}

					$layout_parts[] = $this->do_indent( $layout, 3 );
				}

				$string .= "\t\tarray(\n" . implode( "\n),\narray(\n", $layout_parts ) . "\n\t\t)";
			} // Single
			else {
				$string .= $this->get_columns_part( $columndata );
			}

			$string .= "\n\t) );";
		}

		$string .= "\n}";
		$string .= "\nadd_action( 'ac/ready', 'ac_custom_column_settings_{$function_id}' );";

		return $string;
	}

	/**
	 * @since 1.0
	 */
	public function admin_scripts() {
		wp_enqueue_style( 'acp-export-import', ACP()->get_url() . 'assets/core/css/export-import.css', array(), ACP()->get_version() );
		wp_enqueue_script( 'acp-export-import', ACP()->get_url() . 'assets/core/js/export-import.js', array( 'jquery' ), ACP()->get_version() );
		wp_enqueue_script( 'acp-export-import-multi-select', ACP()->get_url() . 'assets/core/js/jquery.multi-select.js', array( 'jquery' ), ACP()->get_version() );
	}

	/**
	 * Indents JSON
	 * Only needed for PHP less that 5.4
	 * Props to http://snipplr.com/view.php?codeview&id=60559
	 * @since 3.2.2
	 *
	 * @param $json
	 *
	 * @return string
	 */
	private function get_pretty_json( $json ) {

		$json = json_encode( $json );

		$result = '';
		$pos = 0;
		$strLen = strlen( $json );
		$indentStr = '  ';
		$newLine = "\n";
		$prevChar = '';
		$outOfQuotes = true;

		for ( $i = 0; $i <= $strLen; $i++ ) {

			// Grab the next character in the string.
			$char = substr( $json, $i, 1 );

			// Are we inside a quoted string?
			if ( $char == '"' && $prevChar != '\\' ) {
				$outOfQuotes = ! $outOfQuotes;

				// If this character is the end of an element,
				// output a new line and indent the next line.
			} else if ( ( $char == '}' || $char == ']' ) && $outOfQuotes ) {
				$result .= $newLine;
				$pos--;
				for ( $j = 0; $j < $pos; $j++ ) {
					$result .= $indentStr;
				}
			}

			// Add the character to the result string.
			$result .= $char;

			// If the last character was the beginning of an element,
			// output a new line and indent the next line.
			if ( ( $char == ',' || $char == '{' || $char == '[' ) && $outOfQuotes ) {
				$result .= $newLine;
				if ( $char == '{' || $char == '[' ) {
					$pos++;
				}

				for ( $j = 0; $j < $pos; $j++ ) {
					$result .= $indentStr;
				}
			}

			$prevChar = $char;
		}

		return $result;
	}

	/**
	 * @param string $contents
	 *
	 * @return bool|mixed
	 */
	private function get_decoded_txt( $contents ) {
		$decoded = false;

		if ( is_string( $contents ) && strpos( $contents, '<!-- START: Admin Columns export -->' ) !== false ) {
			$contents = str_replace( "<!-- START: Admin Columns export -->\n", "", $contents );
			$contents = str_replace( "\n<!-- END: Admin Columns export -->", "", $contents );

			$contents = maybe_unserialize( base64_decode( trim( $contents ) ) );

			if ( $contents && is_array( $contents ) ) {
				$decoded = $contents;
			}
		}

		return $decoded;
	}

	/**
	 * @param string $contents
	 *
	 * @return array|false
	 */
	private function get_decoded_json( $contents ) {
		$decoded = false;

		if ( is_string( $contents ) ) {
			$result = json_decode( $contents, true );

			if ( $result && is_array( $result ) ) {
				$decoded = $result;
			}
		}

		return $decoded;
	}

	/**
	 * @since      2.0
	 *
	 * @param string $encoded_string
	 *
	 * @return array|false Column data
	 */
	private function get_decoded_settings( $encoded_string ) {

		// TXT File. Deprecated.
		$decoded = $this->get_decoded_txt( $encoded_string );

		if ( $decoded ) {
			return $decoded;
		}

		// JSON File
		return $this->get_decoded_json( $encoded_string );
	}

	/**
	 * @since 1.4.6.5
	 */
	public function render() {
		?>
		<table class="form-table ac-form-table">
			<tbody>
			<?php if ( $this->php_export_string ) : ?>
				<tr>
					<th scope="row">
						<h2><?php _e( 'Results', 'codepress-admin-columns' ); ?></h2>
						<p>
							<a href="#" class="ac-pointer" rel="ac-php-export-instructions-html" data-pos="right"><?php _e( 'Instructions', 'codepress-admin-columns' ); ?></a>
						</p>
						<div id="ac-php-export-instructions-html" style="display:none;">
							<h3><?php _e( 'Using the PHP export', 'codepress-admin-columns' ); ?></h3>
							<ol>
								<li><?php _e( 'Copy the generated PHP code in the right column', 'codepress-admin-columns' ); ?></li>
								<li><?php _e( 'Insert the code in your themes functions.php or in your plugin (on the init action)', 'codepress-admin-columns' ); ?></li>
								<li><?php _e( 'Your columns settings are now loaded from your PHP code instead of from your stored settings!', 'codepress-admin-columns' ); ?></li>
							</ol>
						</div>
					</th>
					<td>
						<form action="" method="post" id="php-export-results">
							<textarea title="Exported code" class="widefat" rows="20"><?php echo $this->php_export_string; ?></textarea>
						</form>
					</td>
				</tr>
			<?php endif; ?>
			<tr>
				<th scope="row">
					<h2><?php _e( 'Columns', 'codepress-admin-columns' ); ?></h2>
					<p><?php _e( 'Select the columns to be exported.', 'codepress-admin-columns' ); ?></p>
				</th>
				<td>
					<div class="ac-export">

						<?php if ( $groups = $this->get_export_multiselect_options() ) : ?>
							<form method="post" class="<?php echo $this->export_single_layouts() ? 'large' : ''; ?>">

								<?php wp_nonce_field( 'export', '_ac_nonce', false ); ?>

								<select title="Exported types" name="export_types[]" multiple="multiple" class="select ac-export-multiselect" id="export_types">
									<?php foreach ( $groups as $group_key => $group ) : ?>
										<optgroup label="<?php echo esc_attr( $group_key ); ?>">
											<?php foreach ( $group as $key => $label ) : ?>
												<option value="<?php echo esc_attr( $key ); ?>"<?php selected( false !== array_search( $key, $this->get_exported_types() ) ); ?>>
													<?php echo esc_html( $label ); ?>
												</option>
											<?php endforeach; ?>
										</optgroup>
									<?php endforeach; ?>
								</select>
								<div class="actions">
									<div class="actions-left">
										<a class="export-select-all" href="#"><?php _e( 'select all', 'codepress-admin-columns' ); ?></a>
									</div>
									<div class="actions-right">
										<a class="export-deselect-all" href="#"><?php _e( 'deselect all', 'codepress-admin-columns' ); ?></a>
									</div>
								</div>
								<div class="submit">
									<input type="submit" class="button button-primary" name="ac-export-php" value="<?php _e( 'Export PHP', 'codepress-admin-columns' ); ?>">
									<input type="submit" class="button button-primary" name="ac-export-json" value="<?php _e( 'Download export file', 'codepress-admin-columns' ); ?>">
								</div>
							</form>
						<?php else : ?>
							<p><?php _e( 'No stored column settings are found.', 'codepress-admin-columns' ); ?></p>
						<?php endif; ?>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
		<table class="form-table ac-form-table">
			<tbody>
			<tr>
				<td>
					<h2><?php _e( 'Download export file', 'codepress-admin-columns' ); ?></h2>
					<p><?php _e( 'Admin Columns will export to a format compatible with the Admin Columns import functionality.', 'codepress-admin-columns' ); ?></p>
					<ol>
						<li><?php _e( 'Select the columns you like to export from the list in the left column', 'codepress-admin-columns' ); ?></li>
						<li><?php _e( 'Click the &quot;Download export file&quot; button', 'codepress-admin-columns' ); ?></li>
						<li><?php _e( 'Save the .json-file when prompted', 'codepress-admin-columns' ); ?></li>
						<li><?php _e( 'Go to the Admin Columns import/export page in your other installation', 'codepress-admin-columns' ); ?></li>
						<li><?php _e( 'Select the export .json-file', 'codepress-admin-columns' ); ?></li>
						<li><?php _e( 'Click the &quot;Start import&quot; button', 'codepress-admin-columns' ); ?></li>
						<li><?php _e( "That's it!", 'codepress-admin-columns' ); ?></li>
					</ol>
				</td>
				<td>
					<h2><?php _e( 'Export to PHP', 'codepress-admin-columns' ); ?></h2>
					<p><?php _e( 'Admin Columns will export PHP code you can directly insert in your plugin or theme.', 'codepress-admin-columns' ); ?></p>
					<ol>
						<li><?php _e( 'Select the columns you like to export from the list in the left column', 'codepress-admin-columns' ); ?></li>
						<li><?php _e( 'Click the &quot;Export to PHP&quot; button', 'codepress-admin-columns' ); ?></li>
						<li><?php _e( 'Copy the generated PHP code in the right column', 'codepress-admin-columns' ); ?></li>
						<li><?php _e( 'Insert the code in your themes functions.php or in your plugin (on the init action)', 'codepress-admin-columns' ); ?></li>
						<li><?php _e( 'Your columns settings are now loaded from your PHP code instead of from your stored settings!', 'codepress-admin-columns' ); ?></li>
					</ol>
				</td>
			</tr>
			</tbody>
		</table>
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row">
					<h2><?php _e( 'Import', 'codepress-admin-columns' ); ?></h2>
					<p><?php _e( 'Import your Admin Column settings here.', 'codepress-admin-columns' ); ?></p>
					<p>
						<a href="#" class="ac-pointer" rel="ac-import-instructions-html" data-pos="right"><?php _e( 'Instructions', 'codepress-admin-columns' ); ?></a>
					</p>
					<div id="ac-import-instructions-html" style="display:none;">
						<h3><?php _e( 'Import Columns Types', 'codepress-admin-columns' ); ?></h3>
						<ol>
							<li><?php _e( 'Choose a Admin Columns Export file to upload.', 'codepress-admin-columns' ); ?></li>
							<li><?php _e( 'Click upload file and import.', 'codepress-admin-columns' ); ?></li>
							<li><?php _e( "That's it! You imported settings are now active.", 'codepress-admin-columns' ); ?></li>
						</ol>
					</div>
				</th>
				<td>
					<div id="ac-import-input">
						<form method="post" action="" enctype="multipart/form-data">
							<input type="file" size="25" name="import" id="upload">

							<?php wp_nonce_field( 'file-import', '_ac_nonce', false ); ?>

							<input type="submit" value="<?php _e( 'Upload file and import', 'codepress-admin-columns' ); ?>" class="button" id="import-submit" name="file-submit">
						</form>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}

}