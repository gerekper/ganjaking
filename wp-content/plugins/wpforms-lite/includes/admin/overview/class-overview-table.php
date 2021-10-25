<?php

/**
 * Generate the table on the plugin overview page.
 *
 * @since 1.0.0
 */
class WPForms_Overview_Table extends WP_List_Table {

	/**
	 * Number of forms to show per page.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $per_page;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Utilize the parent constructor to build the main class properties.
		parent::__construct(
			array(
				'singular' => 'form',
				'plural'   => 'forms',
				'ajax'     => false,
			)
		);

		// Default number of forms to show per page.
		$this->per_page = (int) apply_filters( 'wpforms_overview_per_page', 20 );
	}

	/**
	 * Retrieve the table columns.
	 *
	 * @since 1.0.0
	 *
	 * @return array $columns Array of all the list table columns.
	 */
	public function get_columns() {

		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'form_name' => esc_html__( 'Name', 'wpforms-lite' ),
			'shortcode' => esc_html__( 'Shortcode', 'wpforms-lite' ),
			'created'   => esc_html__( 'Created', 'wpforms-lite' ),
		);

		return apply_filters( 'wpforms_overview_table_columns', $columns );
	}

	/**
	 * Render the checkbox column.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $form
	 *
	 * @return string
	 */
	public function column_cb( $form ) {

		return '<input type="checkbox" name="form_id[]" value="' . absint( $form->ID ) . '" />';
	}

	/**
	 * Render the columns.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $form
	 * @param string  $column_name
	 *
	 * @return string
	 */
	public function column_default( $form, $column_name ) {

		switch ( $column_name ) {
			case 'id':
				$value = $form->ID;
				break;

			case 'shortcode':
				$value = '[wpforms id="' . $form->ID . '"]';
				break;

			case 'created':
				$value = get_the_date( get_option( 'date_format' ), $form );
				break;

			case 'modified':
				$value = get_post_modified_time( get_option( 'date_format' ), false, $form );
				break;

			case 'author':
				$author = get_userdata( $form->post_author );
				$value  = $author->display_name;
				break;

			case 'php':
				$value = '<code style="display:block;font-size:11px;">if( function_exists( \'wpforms_get\' ) ){ wpforms_get( ' . $form->ID . ' ); }</code>';
				break;

			default:
				$value = '';
		}

		return apply_filters( 'wpforms_overview_table_column_value', $value, $form, $column_name );
	}

	/**
	 * Render the form name column with action links.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $form
	 *
	 * @return string
	 */
	public function column_form_name( $form ) {

		// Build the row action links and return the value.
		return $this->get_column_form_name_title( $form ) . $this->get_column_form_name_row_actions( $form );
	}

	/**
	 * Get the form name HTML for the form name column.
	 *
	 * @since 1.5.8
	 *
	 * @param WP_Post $form Form object.
	 *
	 * @return string
	 */
	protected function get_column_form_name_title( $form ) {

		$title = ! empty( $form->post_title ) ? $form->post_title : $form->post_name;
		$name  = sprintf(
			'<span><strong>%s</strong></span>',
			esc_html( $title )
		);

		if ( wpforms_current_user_can( 'view_form_single', $form->ID ) ) {
			$name = sprintf(
				'<a href="%s" title="%s" class="row-title" target="_blank" rel="noopener noreferrer"><strong>%s</strong></a>',
				esc_url( wpforms_get_form_preview_url( $form->ID ) ),
				esc_attr__( 'View preview', 'wpforms-lite' ),
				esc_html( $title )
			);
		}

		if ( wpforms_current_user_can( 'view_entries_form_single', $form->ID ) ) {
			$name = sprintf(
				'<a href="%s" title="%s"><strong>%s</strong></a>',
				esc_url(
					add_query_arg(
						array(
							'view'    => 'list',
							'form_id' => $form->ID,
						),
						admin_url( 'admin.php?page=wpforms-entries' )
					)
				),
				esc_attr__( 'View entries', 'wpforms-lite' ),
				esc_html( $title )
			);
		}

		if ( wpforms_current_user_can( 'edit_form_single', $form->ID ) ) {
			$name = sprintf(
				'<a href="%s" title="%s"><strong>%s</strong></a>',
				esc_url(
					add_query_arg(
						array(
							'view'    => 'fields',
							'form_id' => $form->ID,
						),
						admin_url( 'admin.php?page=wpforms-builder' )
					)
				),
				esc_attr__( 'Edit This Form', 'wpforms-lite' ),
				esc_html( $title )
			);
		}

		return $name;
	}

	/**
	 * Get the row actions HTML for the form name column.
	 *
	 * @since 1.5.8
	 *
	 * @param WP_Post $form Form object.
	 *
	 * @return string
	 */
	protected function get_column_form_name_row_actions( $form ) {

		// Build all of the row action links.
		$row_actions = array();

		// Edit.
		if ( wpforms_current_user_can( 'edit_form_single', $form->ID ) ) {
			$row_actions['edit'] = sprintf(
				'<a href="%s" title="%s">%s</a>',
				esc_url(
					add_query_arg(
						array(
							'view'    => 'fields',
							'form_id' => $form->ID,
						),
						admin_url( 'admin.php?page=wpforms-builder' )
					)
				),
				esc_attr__( 'Edit This Form', 'wpforms-lite' ),
				esc_html__( 'Edit', 'wpforms-lite' )
			);
		}

		// Entries.
		if ( wpforms_current_user_can( 'view_entries_form_single', $form->ID ) ) {
			$row_actions['entries'] = sprintf(
				'<a href="%s" title="%s">%s</a>',
				esc_url(
					add_query_arg(
						array(
							'view'    => 'list',
							'form_id' => $form->ID,
						),
						admin_url( 'admin.php?page=wpforms-entries' )
					)
				),
				esc_attr__( 'View entries', 'wpforms-lite' ),
				esc_html__( 'Entries', 'wpforms-lite' )
			);
		}

		// Preview.
		if ( wpforms_current_user_can( 'view_form_single', $form->ID ) ) {
			$row_actions['preview_'] = sprintf(
				'<a href="%s" title="%s" target="_blank" rel="noopener noreferrer">%s</a>',
				esc_url( wpforms_get_form_preview_url( $form->ID ) ),
				esc_attr__( 'View preview', 'wpforms-lite' ),
				esc_html__( 'Preview', 'wpforms-lite' )
			);
		}

		// Duplicate.
		if ( wpforms_current_user_can( 'create_forms' ) && wpforms_current_user_can( 'view_form_single', $form->ID ) ) {
			$row_actions['duplicate'] = sprintf(
				'<a href="%s" title="%s">%s</a>',
				esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'action'  => 'duplicate',
								'form_id' => $form->ID,
							),
							admin_url( 'admin.php?page=wpforms-overview' )
						),
						'wpforms_duplicate_form_nonce'
					)
				),
				esc_attr__( 'Duplicate this form', 'wpforms-lite' ),
				esc_html__( 'Duplicate', 'wpforms-lite' )
			);
		}

		// Delete.
		if ( wpforms_current_user_can( 'delete_form_single', $form->ID ) ) {
			$row_actions['delete'] = sprintf(
				'<a href="%s" title="%s">%s</a>',
				esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'action'  => 'delete',
								'form_id' => $form->ID,
							),
							admin_url( 'admin.php?page=wpforms-overview' )
						),
						'wpforms_delete_form_nonce'
					)
				),
				esc_attr__( 'Delete this form', 'wpforms-lite' ),
				esc_html__( 'Delete', 'wpforms-lite' )
			);
		}

		return $this->row_actions( apply_filters( 'wpforms_overview_row_actions', $row_actions, $form ) );
	}

	/**
	 * Define bulk actions available for our table listing.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_bulk_actions() {

		$actions = array();

		if ( wpforms_current_user_can( 'delete_entries' ) ) {
			$actions = array(
				'delete' => esc_html__( 'Delete', 'wpforms-lite' ),
			);
		}

		return $actions;
	}

	/**
	 * Message to be displayed when there are no forms.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {

		printf(
			wp_kses( /* translators: %s - WPForms Builder page. */
				__( 'Whoops, you haven\'t created a form yet. Want to <a href="%s">give it a go</a>?', 'wpforms-lite' ),
				array(
					'a' => array(
						'href' => array(),
					),
				)
			),
			esc_url( admin_url( 'admin.php?page=wpforms-builder' ) )
		);
	}

	/**
	 * Fetch and setup the final data for the table.
	 *
	 * @since 1.0.0
	 */
	public function prepare_items() {

		// Setup the columns.
		$columns = $this->get_columns();

		// Hidden columns (none).
		$hidden = array();

		// Define which columns can be sorted - form name, date.
		$sortable = array(
			'form_name' => array( 'title', false ),
			'created'   => array( 'date', false ),
		);

		// Set column headers.
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// Get forms.
		if ( wpforms_current_user_can( 'wpforms_view_others_forms' ) ) {
			$total = wp_count_posts( 'wpforms' )->publish;
		} else {
			$total = count_user_posts( get_current_user_id(), 'wpforms', true );
		}

		$page     = $this->get_pagenum();
		$order    = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
		$orderby  = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'ID';
		$per_page = $this->get_items_per_page( 'wpforms_forms_per_page', $this->per_page );

		$args = array(
			'orderby'        => $orderby,
			'order'          => $order,
			'nopaging'       => false,
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'no_found_rows'  => false,
			'post_status'    => 'publish',
		);

		$data = wpforms()->form->get( '', $args );

		// Giddy up.
		$this->items = $data;

		// Finalize pagination.
		$this->set_pagination_args(
			array(
				'total_items' => $total,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total / $per_page ),
			)
		);
	}

	/**
	 * Extending the `display_rows()` method in order to add hooks.
	 *
	 * @since 1.5.6
	 */
	public function display_rows() {

		do_action( 'wpforms_admin_overview_before_rows', $this );

		parent::display_rows();

		do_action( 'wpforms_admin_overview_after_rows', $this );
	}
}
