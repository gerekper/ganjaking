<?php
/**
 * Follow Up Emails admin table.
 *
 * @version X.X.X TODO set proper version.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table', false ) ) {
	include_once FUE_TEMPLATES_DIR . '/email-list/class-wp-list-table.php';
}

/**
 * Class for displaying and managing Follow Up Emails
 */
class WC_FUE_List_Table extends WP_List_Table {
	private $page;
	private $search;
	private $view;
	private $type;
	private $campaign;
	private $emails;            // emails in current view
	private $emails_all;        // active and inactive
	private $emails_archived;   // archive

	public function __construct() {
		$this->page     = $this->get_pagenum();
		$this->search   = $this->get_search();
		$this->view     = $this->get_view();
		$this->type     = $this->get_type();
		$this->campaign = $this->get_campaign();

		// This needs to go before get_emails,
		// because emails status and number may change here
		$this->execute_bulk_actions();

		$this->update_priorieties();

		$this->get_emails();

		$plural = 'fue';
		if ( 'any' === $this->type || '' === $this->type ) {
			$plural = $plural . ' fue_no_filtered';
		}

		parent::__construct(
			array(
				'plural' => $plural,
			)
		);
	}

	/**
	 * Get views.
	 * @return array
	 */
	protected function get_views() {
		$views = array();

		$count          = wp_count_posts( 'follow_up_email' );
		$active_count   = $count->{'fue-active'};
		$inactive_count = $count->{'fue-inactive'};

		$current_url = add_query_arg( array( 'page' => 'followup-emails' ), 'admin.php' );

		/* translators: view that shows all emails in a list */
		$text = _n( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', 666 );
		array_push(
			$views,
			sprintf(
				"<a href='%s'%s>%s</a>",
				esc_url( $current_url ),
				( 'all' === $this->view || '' === $this->view ) ? ' class="current" aria-current="page"' : '',
				sprintf( $text, number_format_i18n( $active_count + $inactive_count ) )
			)
		);

		/* translators: view that shows only the active emails */
		$text = _n( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 666 );
		array_push(
			$views,
			sprintf(
				"<a href='%s'%s>%s</a>",
				esc_url( add_query_arg( array( 'view' => 'active' ), $current_url ) ),
				( 'active' === $this->view ) ? ' class="current" aria-current="page"' : '',
				sprintf( $text, number_format_i18n( $active_count ) )
			)
		);

		/* translators: view that shows only the inactive emails */
		$text = _n( 'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>', 345 );
		array_push(
			$views,
			sprintf(
				"<a href='%s'%s>%s</a>",
				esc_url( add_query_arg( array( 'view' => 'inactive' ), $current_url ) ),
				( 'inactive' === $this->view ) ? ' class="current" aria-current="page"' : '',
				sprintf( $text, number_format_i18n( $inactive_count ) )
			)
		);

		return $views;
	}

	/**
	 * Display a monthly dropdown for filtering items
	 *
	 * @since 3.1.0
	 *
	 * @param string $post_type
	 */
	protected function types_dropdown( $post_type ) {
		$types = Follow_Up_Emails::get_email_types();
		?>
		<label for="filter-by-type" class="screen-reader-text"><?php esc_html_e( 'Filter by email Type' ); ?></label>
		<select name="type" id="filter-by-date">
			<option<?php selected( $this->type, 'all' ); ?> value=""><?php esc_html_e( 'All Types' ); ?></option>
		<?php
		foreach ( $types as $type ) {

			printf(
				"<option %s value='%s'>%s</option>\n",
				selected( $this->type, $type->id, false ),
				esc_attr( $type->id ),
				esc_html( $type->label )
			);
		}
		?>
		</select>
		<?php
	}

	protected function campaigns_dropdown( $post_type ) {
		$campaigns = get_terms( 'follow_up_email_campaign', array( 'hide_empty' => false ) );
		?>
		<label for="filter-by-campaign" class="screen-reader-text"><?php esc_html_e( 'Filter by Campaign' ); ?></label>
		<select name="campaign" id="filter-by-campaign">
			<option<?php selected( $this->campaign, 'all' ); ?> value=""><?php esc_html_e( 'All Campaigns' ); ?></option>
		<?php
		foreach ( $campaigns as $campaign ) {

			printf(
				"<option %s value='%s'>%s</option>\n",
				selected( $this->campaign, $campaign->slug, false ),
				esc_attr( $campaign->slug ),
				esc_html( $campaign->name )
			);
		}
		?>
		</select>
		<?php
	}

	public function get_columns() {
		$collumns = array(
			'cb'   => '<input type="checkbox" />',
			'name' => __( 'Name', 'follow_up_emails' ),
		);

		// priority only if we have tye selected, otherwise we confuse user with multiple priorieties of the same type
		if ( 'any' !== $this->type && '' !== $this->type ) {
			$collumns = array_merge( $collumns, array( 'priority' => __( 'Priority', 'follow_up_emails' ) ) );
		}

		$collumns = array_merge(
			$collumns,
			array(
				'interval' => __( 'Delay', 'folllow_up_emails' ),
				'product'  => __( 'Product', 'follow_up_emails' ),
				'category' => __( 'Category', 'follow_up_emails' ),
				'type'     => __( 'Type', 'follow_up_emails' ),
				'stats'    => __( 'Stats', 'follow_up_emails' ),
				'status'   => __( 'Status', 'follow_up_emails' ),
			)
		);
		return $collumns;
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			default:
                //phpcs:ignore
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	public function column_cb( $email ) {
		$cb  = '<input type="checkbox" value="' . $email->id . '" name="chk_emails[]" id="cb-select-' . $email->id . '">';
		$cb .= '<div class="locked-indicator"></div>';
		return $cb;
	}

	public function get_bulk_actions() {
		$actions = array(
			'activate'   => __( 'Activate', 'follow_up_emails' ),
			'deactivate' => __( 'Deactivate', 'follow_up_emails' ),
			'archive'    => __( 'Archive', 'follow_up_emails' ),
			'unarchive'  => __( 'Unarchive', 'follow_up_emails' ),
			'delete'     => __( 'Delete', 'follow_up_emails' ),
		);
		return $actions;
	}

	/**
	 * Display the bulk actions dropdown.
	 *
	 * @since 4.8.1
	 *
	 * @param string $which The location of the bulk actions: 'top' or 'bottom'.
	 *                      This is designated as optional for backward compatibility.
	 */
	protected function bulk_actions( $which = '', $view = 'all', $type = 'any' ) {
		if ( is_null( $this->_actions ) ) {
			$this->_actions = $this->get_bulk_actions();
			/**
			 * Filters the list table Bulk Actions drop-down.
			 *
			 * The dynamic portion of the hook name, `$this->screen->id`, refers
			 * to the ID of the current screen, usually a string.
			 *
			 * This filter can currently only be used to remove bulk actions.
			 *
			 * @since 3.5.0
			 *
			 * @param array $actions An array of the available bulk actions.
			 */
			$this->_actions = apply_filters( "bulk_actions-{$this->screen->id}", $this->_actions );
			$two            = '';
		} else {
			$two = '2';
		}

		if ( empty( $this->_actions ) )
			return;

		echo '<label for="bulk-action-selector-' . esc_attr( $which ) . '" class="screen-reader-text">' . esc_html__( 'Select bulk action' ) . '</label>';
		echo '<select name="action' . esc_attr( $two ) . '" id="bulk-action-selector-' . esc_attr( $which ) . "\">\n";
		echo '<option value="">' . esc_html__( 'Bulk Actions' ) . "</option>\n";

		foreach ( $this->_actions as $name => $title ) {
			$class = 'edit' === $name ? ' class="hide-if-no-js"' : '';

			echo "\t" . '<option value="' . esc_attr( $name ) . '"' . esc_attr( $class ) . '>' . esc_attr( $title ) . "</option>\n";
		}

		// Add custom bulk actions for different email types
		// Add custom bulk actions only if specific email type is selected
		if ( 'archived' === $view ) {
			do_action( 'fue_archived_bulk_actions', $type );
		} elseif ( 'inactive' !== $view ) {
			do_action( 'fue_active_bulk_actions', $type );
		}

		echo "</select>\n";

		submit_button( __( 'Apply' ), 'action', '', false, array( 'id' => "doaction$two" ) );
		echo "\n";
	}

	public function update_priorieties() {
		// This is early return exit, we don't need to check anything
		// phpcs:ignore
		if ( empty( $_REQUEST['update_priorities'] ) ) {
			return;
		}
		$types = Follow_Up_Emails::get_email_types();

		if ( ! isset( $_GET['nonce_fue_emails_list'] )
			|| ! wp_verify_nonce( sanitize_key( $_GET['nonce_fue_emails_list'] ), 'fue_emails_actions' )
		) {
			exit;
		}

		foreach ( $types as $key => $type ) {
			if ( isset( $_REQUEST[ $key . '_order' ] ) && ! empty( $_REQUEST[ $key . '_order' ] ) && is_array( $_REQUEST[ $key . '_order' ] ) ) {
				// Sanitized later
                // phpcs:ignore
				foreach ( $_REQUEST[ $key . '_order' ] as $idx => $email_id ) {
					$priority = intval( $idx ) + 1;

					fue_save_email(
						array(
							'id'       => sanitize_text_field( wp_unslash( $email_id ) ),
							'priority' => $priority,
						)
					);
				}
			}
		}
		do_action( 'fue_update_priorities', $_REQUEST );

		$message = __( 'Follow-up emails updated', 'follow_up_emails' );
		wp_safe_redirect( 'admin.php?page=followup-emails&tab=list&updated=1&message=' . rawurlencode( $message ) );
		exit;
	}


	public function execute_bulk_actions() {
		$action = $this->get_action();
		if ( ! $action ) {
			return;
		}

		if ( ! isset( $_GET['nonce_fue_emails_list'] )
			|| ! wp_verify_nonce( sanitize_key( $_GET['nonce_fue_emails_list'] ), 'fue_emails_actions' )
		) {
			exit;
		}
        // phpcs:ignore
		$count   = empty( $_REQUEST['chk_emails'] ) ? 0 : count( $_REQUEST['chk_emails'] );
		$message = '';

		if ( empty( $count ) ) {
			$message = __( 'No emails selected', 'follow_up_emails' );
			wp_safe_redirect( 'admin.php?page=followup-emails&tab=list&updated=1&message=' . rawurlencode( $message ) );
			exit;
		}
        // phpcs:ignore
		$emails = $_REQUEST['chk_emails'];

		if ( ! is_array( $emails ) || empty( $emails ) ) {
			return;
		}

		foreach ( $emails as $email_id ) {
			$email = new FUE_Email( absint( $email_id ) );
			switch ( $action ) {
				case 'activate':
					$email->update_status( FUE_Email::STATUS_ACTIVE );
					break;
				case 'deactivate':
					$email->update_status( FUE_Email::STATUS_INACTIVE );
					break;
				case 'archive':
					$email->update_status( FUE_Email::STATUS_ARCHIVED );
					break;
				case 'unarchive':
					$email->update_status( FUE_Email::STATUS_ACTIVE );
					break;
				case 'delete':
					wp_delete_post( $email_id, true );
					break;
			}
		}

		do_action( 'fue_execute_bulk_action', $action, $emails );

		switch ( $action ) {
			case 'activate':
			case 'unarchive':
				$message = sprintf(
                // translators: how many emails activated by activation action
					_n(
						'%d email activated',
						'%d emails activated',
						$count,
						'follow_up_emails'
					),
					$count
				);
				break;

			case 'deactivate':
				$message = sprintf(
                // translators: how many emails deactivated by deactivation action
					_n(
						'%d email deactivated',
						'%d emails deactivated',
						$count,
						'follow_up_emails'
					),
					$count
				);
				break;

			case 'archive':
				$message = sprintf(
                // translators: how many emails archived by archivation action
					_n(
						'%d email archived',
						'%d emails archived',
						$count,
						'follow_up_emails'
					),
					$count
				);
				break;

			case 'delete':
				$message = sprintf(
                // translators: how many emails deleted by deletion action
					_n(
						'%d email deleted',
						'%d emails deleted',
						$count,
						'follow_up_emails'
					),
					$count
				);
				break;
		}

		wp_safe_redirect( 'admin.php?page=followup-emails&tab=list&updated=1&message=' . rawurlencode( $message ) );
		exit;
	}

    // Accessing unsafe values here is OK, Placves that react to those values have proper guards.
    // phpcs:disable
	public function get_view() {
		$view = '';
		if ( isset( $_REQUEST['view'] ) && in_array( $_REQUEST['view'], array( 'all', 'active', 'inactive', 'archived' ) ) ) {
			$view = $_REQUEST['view'];
		}
		return $view;
	}

	function get_action() {
		$action = '';
		if ( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], array( 'activate', 'deactivate', 'archive', 'unarchive', 'delete' ) ) ) {
			$action = $_REQUEST['action'];
		}
		return $action;
	}

	function get_type() {
		$type = 'any';
		if ( isset( $_REQUEST['type'] ) && in_array( $_REQUEST['type'], array_keys( Follow_Up_Emails::get_email_types() ) ) ) {
			$type = $_REQUEST['type'];
		}
		return $type;
	}

	function get_campaign() {
		$campaign = '';
		if ( isset( $_REQUEST['campaign'] ) ) {
			$campaign = $_REQUEST['campaign'];
		}
		return $campaign;
	}

	function get_search() {
		return isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
	}
    // phpcs:enable 

	public function column_name( $email ) {
		?>
		<strong><a class="row-title" href="post.php?post=<?php echo esc_html( $email->id ); ?>&action=edit"><?php echo esc_html( stripslashes( $email->name ) ); ?></a></strong>
        <div class="row-actions"> 
        <?php
        if ( 'manual' === $email->type ) {
        ?>
            <span class="send"><a href="admin.php?page=followup-emails&tab=send&id=<?php echo esc_html( $email->id ); ?>"><?php esc_attr_e( 'Send', 'follow_up_emails' ); ?></a></span>
			|
        <?php
        }
        ?>
			<span class="edit">
				<a href="<?php echo esc_html( $email->get_preview_url() ); ?>" target="_blank"><?php esc_attr_e( 'Preview', 'follow_up_emails' ); ?></a>
			</span>
			|
			<span class="edit">
				<a href="post.php?post=<?php echo esc_html( $email->id ); ?>&action=edit"><?php esc_attr_e( 'Edit', 'follow_up_emails' ); ?></a>
			</span>
            |
			<span class="edit">
				<a href="#" class="clone-email" data-id="<?php echo esc_attr( $email->id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'duplicate_email' ) ); ?>"><?php esc_html_e( 'Duplicate', 'follow_up_emails' ); ?></a>
			</span>
			|
			<span class="trash">
				<a onclick="return confirm( '<?php esc_html_e( 'Really delete this email?', 'follow_up_emails' ); ?>' );" href="<?php echo esc_url( wp_nonce_url( 'admin-post.php?action=fue_followup_delete&id=' . $email->id, 'delete-email' ) ); ?>"><?php esc_html_e( 'Delete', 'follow_up_emails' ); ?></a>
			</span>
		<?php
	}

	public function column_interval( $email ) {
		return $email->get_trigger_string();
	}

	public function column_priority( $email ) {
		?>
		<div class="column-priority" style="text-align: center;"><span class="priority"><?php echo esc_html( $email->priority ); ?></span></div>
		<input type="hidden" name= <?php echo esc_html( $email->type ); ?>_order[]" value="<?php echo esc_attr( $email->id ); ?>" />
		<?php
	}

	public function column_product( $email ) {
		return ( $email->product_id > 0 ) ? '<a href="post.php?post=' . $email->product_id . '&action=edit">' . get_the_title( $email->product_id ) . '</a>' : '-';
	}

	public function column_category( $email ) {
		if ( 0 == $email->category_id ) {
			return '-';
		} else {
			$term = get_term( $email->category_id, 'product_cat' );
			if ( ! $term ) {
				return '-';
			} else {
				return '<a href="edit-tags.php?action=edit&taxonomy=product_cat&tag_ID=' . $email->category_id . '&post_type=product">' . $term->name . '</a>';
			}
		}
	}

	public function column_type( $email ) {
		$types = Follow_Up_Emails::get_email_types();
		return isset( $types[ $email->type ] ) ? $types[ $email->type ]->singular_label : '';
	}

	public function column_stats( $email ) {
		$sent      = $email->usage_count;
		$opens     = FUE_Reports::count_event_occurences( $email->id, 'open' );
		$clicks    = FUE_Reports::count_unique_clicks( $email->id );
		$opens_pct = 0;

		if ( $sent > 0 ) {
			$opens_pct = ( $opens / $sent ) * 100;
		}

		return sprintf( '<small>Sent: %d<br/>Opens: %d (%.2f%%)<br/>Clicks: %d</small>', $sent, $opens, $opens_pct, $clicks );
	}

	public function column_status( $email ) {
		?>
		<span class="status-toggle">
			<?php if ( FUE_Email::STATUS_ACTIVE === $email->status ) : ?>
				<?php esc_attr_e( 'Active', 'follow_up_emails' ); ?>
				<br/><small><a href="#" class="toggle-activation" data-id="<?php echo esc_html( $email->id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'toggle_activate_email_' . $email->id ) ); ?>"><?php esc_attr_e( 'Deactivate', 'follow_up_emails' ); ?></a></small>
			<?php elseif ( FUE_Email::STATUS_INACTIVE === $email->status ) : ?>
				<?php esc_attr_e( 'Inactive', 'follow_up_emails' ); ?>
				<br/><small><a href="#" class="toggle-activation" data-id="<?php echo esc_html( $email->id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'toggle_activate_email_' . $email->id ) ); ?>"><?php esc_attr_e( 'Activate', 'follow_up_emails' ); ?></a></small>
			<?php else : ?>
				<?php esc_attr_e( 'Archived', 'follow_up_emails' ); ?>
				<br/><small><a href="#" class="unarchive" data-id="<?php echo esc_html( $email->id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'unarchive_email_' . $email->id ) ); ?>"><?php esc_attr_e( 'Unarchive', 'follow_up_emails' ); ?></a></small>
			<?php endif; ?>
		</span>
		<?php if ( FUE_Email::STATUS_ARCHIVED != $email->status ) { ?>
			|
			<small><a href="#" class="archive-email" data-id="<?php echo esc_html( $email->id ); ?>" data-key="<?php echo esc_html( $email->type ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'archive_email_' . $email->id ) ); ?>"><?php esc_attr_e( 'Archive', 'follow_up_emails' ); ?></a></small>
		<?php } ?>
		<?php
		do_action( 'fue_table_status_actions', $email );

		return '';
		// TODO check if ^ $email->type is correct? it was $type->id
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @since 3.1.0
	 * @param string $which
	 */
	protected function display_tablenav( $which ) {
		?>
	<div class="tablenav <?php echo esc_attr( $which ); ?>">

		<?php if ( $this->has_items() ) : ?>
		<div class="alignleft actions bulkactions">
			<?php $this->bulk_actions( $which, $this->view, $this->type ); ?>
		</div>
			<?php
		endif;
		$this->extra_tablenav( $which );
if ( 'bottom' === $which ) {
	$this->pagination( $which );
}
?>
		<br class="clear" />
	</div>
		<?php
		if ( 'top' === $which ) {
			?>
			<div class="tablenav">
			<?php
			$this->extra_nav_items( $which );
			$this->pagination( $which );
			?>
			</div>
			<?php
		}
		?>
		<?php
		if ( 'bottom' === $which ) {
			$this->display_extra_bottom_items();
		}
	}


	/**
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		?>
		<div class="alignleft actions">
            <input type="hidden" name="view" value="<?php echo esc_attr( $this->view ); ?>">

            <?php
            if ( 'top' === $which ) {
                ob_start();

                $this->types_dropdown( $this->screen->post_type );
                $this->campaigns_dropdown( $this->screen->post_type );
                $output = ob_get_clean();

            }
            if ( ! empty( $output ) ) {
                // phpcs:ignore
                echo $output;
			submit_button( __( 'Filter' ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
		}
		?>
		</div>
		<?php
	}

	private function get_emails() {
		$type = 'any';
		if ( $this->type ) {
			$type = $this->type;
		}

		// STATUS
		$status = array( 'fue-active', 'fue-inactive' );
		if ( 'archived' === $this->view ) {
			$status = 'fue-archived';
		} elseif ( 'active' === $this->view ) {
			$status = 'fue-active';
		} elseif ( 'inactive' === $this->view ) {
			$status = 'fue-inactive';
		}

		// FILTERS
		$filters = array();
		if ( $this->search ) {
			$filters['fue_post_title'] = $this->search;
		}

		if ( $this->campaign ) {
			$campaign                = array();
			$campaign['tax_query'][] = array(
				'taxonomy' => 'follow_up_email_campaign',
				'terms'    => $this->campaign,
				'field'    => 'slug',
			);
			$filters                 = array_merge( $filters, $campaign );
		}
		$this->emails = fue_get_emails_with_like_title( $type, $status, $filters );
	}

	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$per_page    = 10;
		$total_items = count( $this->emails );

		// TODO We should implememnt pagination at this slice here.
		// This should be a function that would grab necesarry data
		$emails_paged = array_slice( $this->emails, ( ( $this->page - 1 ) * $per_page ), $per_page );
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);
		$this->items = $emails_paged;
	}

	public function extra_nav_items( $which ) {

		?>
		<div>
			<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
				<a href='<?php echo esc_url( add_query_arg( array( 'view' => 'false' ) ) ); ?>' class="status-tab nav-tab <?php echo esc_html( 'archived' != $this->view ? 'nav-tab-active' : '' ); ?>"><?php esc_attr_e( 'Follow Ups', 'follow_up_emails' ); ?></a>
				<a href='<?php echo esc_url( add_query_arg( array( 'view' => 'archived' ) ) ); ?>' class="status-tab nav-tab <?php echo esc_url( 'archived' === $this->view ? 'nav-tab-active' : '' ); ?>"><?php esc_attr_e( 'Archived', 'follow_up_emails' ); ?></a>
			</h2>
		</div>
		<?php
	}

	public function display_extra_bottom_items() {
		if ( 'any' === $this->type || '' === $this->type ) {
			return;
		}
		// Show update priorieties button only when email type is selected
		?>
		<p class="submit">
			<input type="submit" name="update_priorities" value="<?php esc_attr_e( 'Update Priorities', 'follow_up_emails' ); ?>" class="button-primary" />
		</p>
		<?php
	}

	public function no_items() {
		esc_attr_e( 'No emails available.', 'follow_up_emails' );
	}
}

