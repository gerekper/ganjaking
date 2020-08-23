<?php
/**
 * WooCommerce Product Reviews Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Reviews List Table class
 *
 * @since 1.0.0
 */
class WC_Reviews_List_Table extends \WP_List_Table {


	/** bool whether this list table uses the checkbox column or not **/
	public $checkbox = true;

	/** array additional items to display hidden in the list table **/
	protected $extra_items = array();

	/** bool true if current user can edit comments, false otherwise **/
	protected $user_can = false;

	/** int the number of pending comments **/
	protected $pending_count = 0;


	/**
	 * Constructor
	 *
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		global $post_id;

		$post_id = isset( $_REQUEST['p'] ) ? absint( $_REQUEST['p'] ) : 0;

		if ( get_option( 'show_avatars' ) ) {
			add_filter( 'comment_author', 'floated_admin_avatar' );
		}

		parent::__construct( array(
			'plural'   => __( 'Reviews', 'woocommerce-product-reviews-pro' ),
			'singular' => __( 'Review',  'woocommerce-product-reviews-pro' ),
			'ajax'     => true,
			'screen'   => isset( $args['screen'] ) ? $args['screen'] : null,
		) );

		add_action( 'admin_head', array( $this, 'enqueue_js' ) );

		// Remove groupby clause from count query
		add_filter( 'comments_clauses', array( $this, 'remove_groupby_from_count_query' ), 10, 2 );

		// Add rating filter to reviews screen
		add_action( 'restrict_manage_reviews', array( $this, 'restrict_manage_reviews' ) );

		// Filter reviews
		add_filter( 'pre_get_comments', array( $this, 'filter_reviews_list' ), 10 );

		// Add contribution types as avatar comment types on the admin list table
		add_filter( 'get_avatar_comment_types', array( $this, 'add_contribution_avatar_types' ) );
	}


	/**
	 * Enqueue JS helpers
	 */
	public function enqueue_js() {

		wc_enqueue_js( "
			jQuery( function() {

				$( '.comment-show-more-link' ).on( 'click', function( e ) {
					e.preventDefault();
					$( this ).parent().hide();
					$( this ).parent().next( '.comment-text-full' ).show();
				} );

			} );
		" );
	}


	/**
	 * AJAX user permissions check
	 *
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can( 'edit_posts' );
	}


	/**
	 * Prepare reviews for display
	 */
	public function prepare_items() {
		global $post_id, $comment_status, $search, $comment_type;

		// Prepare for querying reviews
		$meta_query = $meta_key = '';

		$comment_status = isset( $_REQUEST['comment_status'] ) ? $_REQUEST['comment_status'] : 'all';

		if ( ! in_array( $comment_status, array( 'all', 'moderated', 'approved', 'spam', 'trash' ) ) ) {
			$comment_status = 'all';
		}

		$comment_type = ! empty( $_REQUEST['comment_type'] ) ? $_REQUEST['comment_type'] : '';
		$search  = isset( $_REQUEST['s'] )                   ? $_REQUEST['s']            : '';
		$user_id = isset( $_REQUEST['user_id'] )             ? $_REQUEST['user_id']      : '';
		$orderby = isset( $_REQUEST['orderby'] )             ? $_REQUEST['orderby']      : '';
		$order   = isset( $_REQUEST['order'] )               ? $_REQUEST['order']        : '';

		$comments_per_page = $this->get_per_page( $comment_status );

		if ( isset( $_REQUEST['number'] ) ) {
			$number = (int) $_REQUEST['number'];
		} else {
			$number = $comments_per_page + min( 8, $comments_per_page ); // Grab a few extra
		}

		$page = $this->get_pagenum();

		if ( isset( $_REQUEST['start'] ) ) {
			$start = (int) $_REQUEST['start'];
		} else {
			$start = ( $page - 1 ) * $comments_per_page;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['offset'] ) ) {
			$start += (int) $_REQUEST['offset'];
		}

		$status_map = array(
			'moderated' => 'hold',
			'approved'  => 'approve',
			'all'       => '',
		);

		$args = array(
			'status'     => isset( $status_map[ $comment_status ] ) ? $status_map[ $comment_status ] : $comment_status,
			'search'     => $search,
			'user_id'    => $user_id,
			'offset'     => $start,
			'number'     => $number,
			'post_id'    => $post_id,
			'type'       => $comment_type,
			'orderby'    => $orderby,
			'order'      => $order,
			'meta_key'   => $meta_key,
			'meta_query' => $meta_query,
			'post_type'  => 'product',
		);

		$_comments = get_comments( $args );

		update_comment_cache( $_comments );

		$this->items       = array_slice( $_comments, 0, $comments_per_page );
		$this->extra_items = array_slice( $_comments, $comments_per_page );

		$total_comments = get_comments( array_merge( $args, array( 'count' => true, 'offset' => 0, 'number' => 0 ) ) );

		$_comment_post_ids = array();

		foreach ( $_comments as $_c ) {
			$_comment_post_ids[] = $_c->comment_post_ID;
		}

		$_comment_post_ids = array_unique( $_comment_post_ids );

		$this->pending_count = get_pending_comments_num( $_comment_post_ids );

		$this->set_pagination_args( array(
			'total_items' => $total_comments,
			'per_page'    => $comments_per_page,
		) );
	}


	/**
	 * Add rating and media type dropdowns to reviews list screen filter
	 */
	public function restrict_manage_reviews() {

		$rating_options = array(
			''  => __( 'All ratings', 'woocommerce-product-reviews-pro' ),
			'5' => __( 'Perfect',     'woocommerce-product-reviews-pro' ),
			'4' => __( 'Good',        'woocommerce-product-reviews-pro' ),
			'3' => __( 'Average',     'woocommerce-product-reviews-pro' ),
			'2' => __( 'Mediocre',    'woocommerce-product-reviews-pro' ),
			'1' => __( 'Poor',        'woocommerce-product-reviews-pro' ),
		);

		$current_rating = isset( $_REQUEST['rating'] ) ? $_REQUEST['rating'] : '';

		?>
		<select name="rating">
			<?php foreach ( $rating_options as $value => $label ) : ?>
				<option value="<?php echo $value; ?>" <?php selected( $current_rating, $value ); ?>><?php echo $label; ?></option>
			<?php endforeach; ?>
		</select>
		<?php

		$current_product      = isset( $_REQUEST['p'] ) ? $_REQUEST['p'] : '';
		$current_product_name = '';

		if ( $current_product ) {
			$product              = wc_get_product( $current_product );
			$current_product_name = $product->get_formatted_name();
		}

		?>

			<select
				class="wc-product-search"
				name="p"
				style="width: 200px;"
				data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce-product-reviews-pro' ); ?>"
				data-action="woocommerce_json_search_products"
				data-allow_clear="true">
				<?php if ( ! empty( $current_product ) ) : ?>
					<option value="<?php echo esc_attr( $current_product ); ?>"><?php echo esc_html( $current_product_name ); ?></option>
				<?php endif; ?>
			</select>

		<?php
	}


	/**
	 * Filter reviews list by rating
	 *
	 * The implementation modifies the comments query
	 *
	 * @see https://core.trac.wordpress.org/ticket/23469
	 * @see https://gist.github.com/markjaquith/681af58ce22d79c08c09
	 * @param WP_Comment_Query $query Instance
	 * @return WP_Comment_Query The (modified) query instance
	 */
	public function filter_reviews_list( $query ) {

		// Get the existing meta_query or create one now
		$meta_query = $query->query_vars['meta_query'] ? $query->query_vars['meta_query'] : array();
		$modified   = false;

		// Filter by rating
		if ( isset( $_REQUEST['rating'] ) && (int) $_REQUEST['rating'] > 0 ) {

			$meta_query[] = array(
				'key'     => 'rating',
				'value'   => (int) $_REQUEST['rating'],
				'compare' => '=',
				'type'    => 'NUMERIC',
			);

			// Re-assign the meta_query to query_vars
			$query->query_vars['meta_query'] = $meta_query;

			$query->meta_query->parse_query_vars( $query->query_vars );
		}

		return $query;
	}


	/**
	 * Remove groupby clause from comments count query
	 *
	 * @param array $pieces
	 * @param WP_Comment_Query $query
	 * @return array
	 */
	public function remove_groupby_from_count_query( array $pieces, $query ) {

		if ( $query->query_vars['count'] ) {
			$pieces['groupby'] = '';
		}

		return $pieces;
	}


	/**
	 * Get number of reviews per page
	 *
	 * @param string $comment_status The comment status name. Default 'All'.
	 * @return int The number of reviews to list per page.
	 */
	public function get_per_page( $comment_status = 'all' ) {

		$reviews_per_page = $this->get_items_per_page( 'edit_comments_per_page' );

		/**
		 * Filter the number of reviews listed per page in the reviews list table.
		 *
		 * @param int    $comments_per_page The number of reviews to list per page.
		 * @param string $comment_status    The comment status name. Default 'All'.
		 */
		return apply_filters( 'reviews_per_page', $reviews_per_page, $comment_status );
	}


	/**
	 * The HTML to display when there are no reviews to display
	 *
	 * @see WP_List_Table::no_items()
	 */
	public function no_items() {
		global $comment_status;

		if ( 'moderated' === $comment_status ) {
			esc_html_e( 'No reviews awaiting moderation.', 'woocommerce-product-reviews-pro' );
		} else {
			esc_html_e( 'No reviews found.', 'woocommerce-product-reviews-pro' );
		}
	}


	/**
	 * Get status link labels
	 *
	 * @return array
	 */
	public function get_status_link_labels() {
		return array(
			/* translators: Placeholder: %s total contributions, singular not used */
			'all'       => _nx_noop( 'All <span class="count">(<span class="all-count">%s</span>)</span>', 'All <span class="count">(<span class="all-count">%s</span>)</span>', 'comments', 'woocommerce-product-reviews-pro' ),
			/* translators: Placeholder: %s count of pending contributions */
			'moderated' => _nx_noop( 'Pending <span class="count">(<span class="pending-count">%s</span>)</span>', 'Pending <span class="count">(<span class="pending-count">%s</span>)</span>', 'Reviews', 'woocommerce-product-reviews-pro' ),
			/* translators: Placeholder: %s Approved contributions, singular not used */
			'approved'  => _nx_noop( 'Approved <span class="count">(<span class="approved-count">%s</span>)</span>', 'Approved <span class="count">(<span class="all-count">%s</span>)</span>', 'Reviews', 'woocommerce-product-reviews-pro' ),
			/* translators: Placeholder: %s count of spam contributions, singular not used */
			'spam'      => _nx_noop( 'Spam <span class="count">(<span class="spam-count">%s</span>)</span>', 'Spam <span class="count">(<span class="spam-count">%s</span>)</span>', 'Reviews', 'woocommerce-product-reviews-pro' ),
			/* translators: Placeholder: %s count of trashed contributions, singular not used */
			'trash'     => _nx_noop( 'Trash <span class="count">(<span class="trash-count">%s</span>)</span>', 'Trash <span class="count">(<span class="trash-count">%s</span>)</span>', 'Reviews', 'woocommerce-product-reviews-pro' )
		);
	}


	/**
	 * Returns an associative array listing all the views that can be used with this table.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_views() {
		global $post_id, $comment_status, $comment_type;

		/**
		 * Filters the reviews submenu page URL.
		 *
		 * @since 1.10.0
		 *
		 * @param string $link URL
		 */
		$link = apply_filters( 'wc_product_reviews_pro_reviews_screen_page_url', add_query_arg( 'page', 'reviews', 'admin.php' ) );

		// add comment type to the base link
		if ( ! empty( $comment_type ) && 'all' !== $comment_type ) {
			$link = add_query_arg( 'comment_type', $comment_type, $link );
		}

		$status_link_labels = $this->get_status_link_labels();

		if ( ! EMPTY_TRASH_DAYS && isset( $status_link_labels['trash'] ) ) {
			unset( $status_link_labels['trash'] );
		}

		$status_links = array();

		// prepare status links and counts
		foreach ( $status_link_labels as $status => $label ) {

			$link = add_query_arg( 'comment_status', $status, $link );

			// if viewing reviews for a specific product, add that to the link as well
			if ( $post_id ) {
				$link = add_query_arg( 'p', absint( $post_id ), $link );
			}

			$class = $status === $comment_status ? ' class="current"' : '';

			if ( 'approved' === $status ) {
				$status = 1;
			} elseif ( 'moderated' === $status ) {
				$status = 0;
			}

			// get the comment count for current status in loop
			$status_count = wc_product_reviews_pro_get_reviews_count( 'all', $status );

			// translate and format link
			$status_links[ (string) $status ] = '<a href="' . esc_url( $link ) . '" ' . $class . '>' . sprintf( translate_nooped_plural( $label, $status_count ), number_format_i18n( $status_count ) ) . '</a>';
		}

		/**
		 * Filters the review status links.
		 *
		 * @since 1.0.0
		 *
		 * @param array $status_links an associative array of fully-formed status links (default 'all' - accepts 'all', 'pending', 'approved', 'spam', and 'trash')
		 */
		return apply_filters( 'review_status_links', $status_links );
	}


	/**
	 * Return a list of available bulk actions
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		global $comment_status;

		$actions = array();

		if ( current_user_can( 'manage_woocommerce' ) ) {

			if ( in_array( $comment_status, array( 'all', 'approved' ), true ) ) {
				$actions['unapprove'] = __( 'Unapprove', 'woocommerce-product-reviews-pro' );
			}

			if ( in_array( $comment_status, array( 'all', 'moderated' ), true ) ) {
				$actions['approve'] = __( 'Approve', 'woocommerce-product-reviews-pro' );
			}

			if ( in_array( $comment_status, array( 'all', 'moderated', 'approved' ), true ) ) {
				$actions['spam'] = _x( 'Mark as Spam', 'comment', 'woocommerce-product-reviews-pro' );
			}

			if ( 'trash' === $comment_status ) {
				$actions['untrash'] = __( 'Restore', 'woocommerce-product-reviews-pro' );
			} elseif ( 'spam' === $comment_status ) {
				$actions['unspam']  = _x( 'Not Spam', 'comment', 'woocommerce-product-reviews-pro' );
			}

			if ( ! EMPTY_TRASH_DAYS || in_array( $comment_status, array( 'trash', 'spam' ), true ) ) {
				$actions['delete'] = __( 'Delete Permanently', 'woocommerce-product-reviews-pro' );
			} else {
				$actions['trash']  = __( 'Move to Trash', 'woocommerce-product-reviews-pro' );
			}
		}

		return $actions;
	}


	/**
	 * Render additional controls
	 *
	 * These will be rendered between bulk actions and
	 * pagination controls
	 *
	 * @param string $which position
	 */
	public function extra_tablenav( $which ) {
		global $comment_status, $comment_type;

		?>
		<div class="alignleft actions">
			<?php

			 if ( 'top' === $which ) {

				/**
				 * Fires just before the Filter submit button for review types.
				 */
				do_action( 'restrict_manage_reviews' );

				submit_button( _x( 'Filter', 'verb', 'woocommerce-product-reviews-pro' ), 'button', false, false, array( 'id' => 'post-query-submit' ) );
			}

			if ( ( 'spam' === $comment_status || 'trash' === $comment_status ) && current_user_can( 'moderate_comments' ) ) {

				wp_nonce_field( 'bulk-destroy', '_destroy_nonce' );

				$title = 'spam' === $comment_status ? esc_attr__( 'Empty Spam', 'woocommerce-product-reviews-pro' ) : esc_attr__( 'Empty Trash', 'woocommerce-product-reviews-pro' );

				submit_button( $title, 'apply', 'delete_all', false );
			}

			/**
			 * Fires after the Filter submit button for review types.
			 *
			 * @param string $comment_status The review status name. Default 'All'.
			 */
			do_action( 'manage_reviews_nav', $comment_status );

			?>
		</div>
		<?php
	}


	/**
	 * Returns the current action select in bulk actions menu
	 *
	 * @return string
	 */
	public function current_action() {

		if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) ) {
			return 'delete_all';
		}

		return parent::current_action();
	}


	/**
	 * Set column titles
	 *
	 * @return array
	 */
	public function get_columns() {

		$columns = array();

		if ( $this->checkbox && current_user_can( 'manage_woocommerce' ) ) {
			$columns['cb']  = '<input type="checkbox" />';
		}

		$columns['author'] 	= __( 'Author', 'woocommerce-product-reviews-pro' );
		$columns['comment'] = __( 'Review', 'woocommerce-product-reviews-pro' );
		$columns['product'] = __( 'Product', 'woocommerce-product-reviews-pro' );
		$columns['parent']  = _x( 'Parent', 'Review parent item', 'woocommerce-product-reviews-pro' );

		return $columns;
	}


	/**
	 * Get a list of sortable columns
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'author'   => 'comment_author',
		);
	}


	/**
	 * Output the list table
	 */
	public function display() {

		wp_nonce_field( 'fetch-list-' . get_class( $this ), '_ajax_fetch_list_nonce' );

		$this->display_tablenav( 'top' );

		?>
		<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
			<thead>
				<tr><?php $this->print_column_headers(); ?></tr>
			</thead>

			<tfoot>
				<tr><?php $this->print_column_headers( false ); ?></tr>
			</tfoot>

			<tbody id="the-comment-list" data-wp-lists="list:comment">
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>

			<tbody id="the-extra-comment-list" data-wp-lists="list:comment" style="display: none;">
				<?php
					$this->items = $this->extra_items;

					if ( ! empty( $this->items ) ) {
						$this->display_rows();
					}
				?>
			</tbody>

		</table>
		<?php

		$this->display_tablenav( 'bottom' );
	}


	/**
	 * Render a single row HTML
     *
     * @param \WP_Comment $a_comment
	 */
	public function single_row( $a_comment ) {
		global $post, $comment;

		$comment           = $a_comment;
		$the_comment_class = wp_get_comment_status( $comment->comment_ID );
		$the_comment_class = implode( ' ', get_comment_class( $the_comment_class, $comment->comment_ID, $comment->comment_post_ID ) );

		$post = get_post( $comment->comment_post_ID );

		$this->user_can = current_user_can( 'edit_comment', $comment->comment_ID );

		?>
		<tr id="comment-<?php echo $comment->comment_ID; ?>" class="<?php echo esc_attr( $the_comment_class ); ?>">
			<?php $this->single_row_columns( $comment ); ?>
		</tr>
		<?php
	}


	/**
	 * Render the checkbox column HTML
	 *
	 * @param WP_Comment $comment
	 */
	public function column_cb( $comment ) {

		if ( $this->user_can ) {

			?>
			<label class="screen-reader-text" for="cb-select-<?php echo $comment->comment_ID; ?>"><?php esc_html_e( 'Select comment', 'woocommerce-product-reviews-pro' ); ?></label>
			<input
				id="cb-select-<?php echo $comment->comment_ID; ?>"
				type="checkbox"
				name="delete_comments[]"
				value="<?php echo $comment->comment_ID; ?>"
			/>
			<?php
		}
	}


	/**
	 * Renders the author column HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Comment $comment contribution comment object
	 */
	public function column_author( $comment ) {
		global $comment_status;

		$author_url = get_comment_author_url();

		if ( 'http://' === $author_url ) {
			$author_url = '';
		}

		$author_url_display = preg_replace( '|http://(www\.)?|i', '', $author_url );

		if ( strlen( $author_url_display ) > 50 ) {
			$author_url_display = substr( $author_url_display, 0, 49 ) . '&hellip;';
		}

		?>

		<strong><?php comment_author(); ?></strong><br />

		<?php if ( ! empty( $author_url ) ) : ?>

			<a title="<?php echo esc_attr( $author_url ); ?>" href="<?php echo esc_attr( $author_url ); ?>"><?php echo esc_html( $author_url_display ); ?></a>
			<br>

		<?php endif; ?>

		<?php if ( $this->user_can ) : ?>

			<?php if ( ! empty( $comment->comment_author_email ) ) : ?>

				<?php comment_author_email_link(); ?>
				<br>

			<?php endif; ?>

			<?php

			$link = add_query_arg(
				array(
					's'    => get_comment_author_IP( $comment->comment_ID ),
					'page' => 'reviews',
					'mode' => 'detail'
				),
				'admin.php'
			);

			if ( 'spam' === $comment_status ) {
				$link = add_query_arg( array( 'comment_status' => 'spam' ), $link );
			}

			?>
			<a href="<?php echo esc_url( $link ); ?>"><?php comment_author_IP( $comment->comment_ID ); ?></a>

		<?php endif;
	}


	/**
	 * Renders the comment column HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Comment $comment the comment object
	 */
	public function column_comment( $comment ) {
		global $comment_status;

		$post               = get_post();
		$rating             = get_comment_meta( $comment->comment_ID, 'rating', true );
		$comment_url        = get_comment_link( $comment->comment_ID );
		$the_comment_status = wp_get_comment_status( $comment->comment_ID );

		?>

		<div class="comment-author">';
			<?php $this->column_author( $comment ); ?>
		</div>

		<div class="submitted-on">
			<?php /* translators: Placeholders: %1$s opening <a> link tag, %2$s comment date, %3$s comment time, %4$s closing <a> link tag */
			printf( __( 'On %1$s%2$s at %3$s%4$s', 'woocommerce-product-reviews-pro' ),
				'<a href="' . esc_url( $comment_url ) . '">',
				get_comment_date( wc_date_format() ),
				get_comment_date( wc_time_format() ),
				'</a>'
			); ?>
			<hr>
		</div>

		<?php if ( $rating && wc_review_ratings_enabled() ) : ?>

			<?php // if this is a review with a rating, display the star rating: ?>
			<span class="star-rating" title="<?php /* translators: Placeholder %d - rating (e.g. 'Rated 4 out of 5') */
				echo esc_attr( sprintf( __( 'Rated %d out of 5', 'woocommerce-product-reviews-pro' ), $rating ) ); ?>">
				<span style="width:<?php echo esc_attr( ( $rating / 5 ) * 100 ); ?>%"><?php /* translators: Placeholder %d - rating (e.g. '4 out of 5') */
					printf( __( '%d out of 5', 'woocommerce-product-reviews-pro' ), $rating ) ; ?></span>
			</span>

		<?php endif; ?>

		<?php do_action( 'woocommerce_reviews_list_before_comment_text' ); ?>

		<?php // output the comment text
		$full_comment    = get_comment_text( $comment->comment_ID );
		$trimmed_comment = wp_trim_words( $full_comment, 20 );
		?>

		<?php if ( strlen( $full_comment ) !== strlen( $trimmed_comment ) ) : ?>

			<div class="comment-text comment-text-trimmed">
				<?php echo $trimmed_comment; ?> <a href="#" class="comment-show-more-link"><?php esc_html_e( 'show more', 'woocommerce-product-reviews-pro' ); ?></a>
			</div>

		<?php endif; ?>

		<div class="comment-text comment-text-full">
			<?php echo $full_comment; ?>
		</div>

		<?php if ( $this->user_can ) : ?>

			<div id="inline-<?php echo $comment->comment_ID; ?>" class="hidden">
				<textarea class="comment" rows="1" cols="1"><?php
					/** this filter is documented in wp-admin/includes/comment.php */
					echo esc_textarea( apply_filters( 'comment_edit_pre', $comment->comment_content ) );
				?></textarea>
				<div class="author-email"><?php echo esc_attr( $comment->comment_author_email ); ?></div>
				<div class="author"><?php echo esc_attr( $comment->comment_author ); ?></div>
				<div class="author-url"><?php echo esc_attr( $comment->comment_author_url ); ?></div>
				<div class="comment_status"><?php echo $comment->comment_approved; ?></div>
			</div>

			<?php if ( $contribution = wc_product_reviews_pro_get_contribution( $comment ) ) : ?>

				<?php

				// get the contribution type to use in text strings
				$contribution_type = wc_product_reviews_pro_get_contribution_type( $contribution->get_type() );

				// nonces
				$del_nonce     = esc_html( '_wpnonce=' . wp_create_nonce( "delete-comment_{$comment->comment_ID}" ) );
				$approve_nonce = esc_html( '_wpnonce=' . wp_create_nonce( "approve-comment_{$comment->comment_ID}" ) );

				// main URL
				$url = "comment.php?c={$comment->comment_ID}";

				// action URLs
				$approve_url   = esc_url( $url . "&action=approvecomment&{$approve_nonce}" );
				$unapprove_url = esc_url( $url . "&action=unapprovecomment&{$approve_nonce}" );
				$spam_url      = esc_url( $url . "&action=spamcomment&{$del_nonce}" );
				$unspam_url    = esc_url( $url . "&action=unspamcomment&{$del_nonce}" );
				$trash_url     = esc_url( $url . "&action=trashcomment&{$del_nonce}" );
				$untrash_url   = esc_url( $url . "&action=untrashcomment&{$del_nonce}" );
				$delete_url    = esc_url( $url . "&action=deletecomment&{$del_nonce}" );

				// labels and titles
				/* translators: Placeholder: %s contribution type name */
				$approve_title   = esc_attr( sprintf( __( 'Approve this %s', 'woocommerce-product-reviews-pro' ), $contribution_type->get_title() ) );
				$approve_label   = esc_html( __( 'Approve', 'woocommerce-product-reviews-pro' ) );
				/* translators: Placeholder: %s contribution type name */
				$unapprove_title = esc_attr( sprintf( __( 'Unapprove this %s', 'woocommerce-product-reviews-pro' ), $contribution_type->get_title() ) );
				$unapprove_label = esc_html( __( 'Unapprove', 'woocommerce-product-reviews-pro' ) );
				/* translators: Placeholder: %s contribution type name */
				$spam_title      = esc_attr( sprintf( __( 'Mark this %s as spam', 'woocommerce-product-reviews-pro' ), $contribution_type->get_title() ) );
				$spam_label      = esc_html( _x( 'Spam', 'Mark as spam', 'woocommerce-product-reviews-pro' ) );
				/* translators: Placeholder: %s contribution type name */
				$unspam_title    = esc_attr( sprintf( __( '', 'woocommerce-product-reviews-pro' ), $contribution_type->get_title() ) );
				$unspam_label    = esc_html( _x( 'Not spam', 'Mark as not spam', 'woocommerce-product-reviews-pro' ) );
				/* translators: Placeholder: %s contribution type name */
				$trash_title     = esc_attr( sprintf( __( 'Move this %s to the trash', 'woocommerce-product-reviews-pro' ), $contribution_type->get_title() ) );
				$trash_label     = esc_html( _x( 'Trash', 'Verb', 'woocommerce-product-reviews-pro' ) );
				/* translators: Placeholder: %s contribution type name */
				$untrash_title   = esc_attr( sprintf( __( 'Restore %s from trash', 'woocommerce-product-reviews-pro' ), $contribution_type->get_title() ) );
				$untrash_label   = esc_html( __( 'Restore', 'woocommerce-product-reviews-pro' ) );
				/* translators: Placeholder: %s contribution type name */
				$delete_title    = esc_attr( sprintf( __( 'Delete this %s permanently', 'woocommerce-product-reviews-pro' ), $contribution_type->get_title() ) );
				$delete_label    = esc_html( __( 'Delete', 'woocommerce-product-reviews-pro' ) );
				/* translators: Placeholder: %s contribution type name */
				$edit_title      = esc_attr( sprintf( __( 'Edit this %s', 'woocommerce-product-reviews-pro' ), $contribution_type->get_title() ) );
				$edit_label      = esc_html( __( 'Edit', 'woocommerce-product-reviews-pro' ) );
				/* translators: Placeholder: %s contribution type name */
				$reply_title     = esc_attr( sprintf( __( 'Reply to this %s', 'woocommerce-product-reviews-pro' ), $contribution_type->get_title() ) );
				$reply_label     = esc_html( __( 'Reply', 'woocommerce-product-reviews-pro' ) );

				// preorder it: Approve | Reply | Edit | Spam | Trash
				$actions = array(
					'approve' => '', 'unapprove' => '',
					'reply'   => '',
					'edit'    => '',
					'spam'    => '', 'unspam' => '',
					'trash'   => '', 'untrash' => '', 'delete' => ''
				);

				// URLs and text already escaped
				if ( $comment_status && 'all' !== $comment_status ) {

					// not looking at all comments
					if ( 'approved' === $the_comment_status ) {
						$actions['unapprove'] = '<a href="' . $unapprove_url . '" data-wp-lists="delete:the-comment-list:comment-' . $comment->comment_ID . ':e7e7d3:action=dim-comment&amp;new=unapproved" class="vim-u vim-destructive" title="' . $unapprove_title . '">' . $unapprove_label . '</a>';
					} else if ( 'unapproved' === $the_comment_status ) {
						$actions['approve']   = '<a href="' . $approve_url . '" data-wp-lists="delete:the-comment-list:comment-' . $comment->comment_ID . ':e7e7d3:action=dim-comment&amp;new=approved" class="vim-a vim-destructive" title="' . $approve_title . '">' . $approve_label . '</a>';
					}

				} else {

					$actions['approve']   = '<a href="' . $approve_url . '" data-wp-lists="dim:the-comment-list:comment-' . $comment->comment_ID . ':unapproved:e7e7d3:e7e7d3:new=approved" class="vim-a" title="' . $approve_title . '">' . $approve_label . '</a>';
					$actions['unapprove'] = '<a href="' . $unapprove_url . '" data-wp-lists="dim:the-comment-list:comment-' . $comment->comment_ID . ':unapproved:e7e7d3:e7e7d3:new=unapproved" class="vim-u" title="' . $unapprove_title . '">' . $unapprove_label . '</a>';
				}

				if ( 'spam' !== $the_comment_status && 'trash' !== $the_comment_status ) {
					$actions['spam']    = '<a href="' . $spam_url . '" data-wp-lists="delete:the-comment-list:comment-' . $comment->comment_ID . '::spam=1" class="vim-s vim-destructive" title="' . $spam_title . '">' . $spam_label . '</a>';
				} elseif ( 'spam' === $the_comment_status ) {
					$actions['unspam']  = '<a href="' . $unspam_url . '" data-wp-lists="delete:the-comment-list:comment-' . $comment->comment_ID . ':66cc66:unspam=1" class="vim-z vim-destructive" title="' . $unspam_title .'">' . $unspam_label . '</a>';
				} elseif ( 'trash' === $the_comment_status ) {
					$actions['untrash'] = '<a href="' . $untrash_url . '" data-wp-lists="delete:the-comment-list:comment-' . $comment->comment_ID . ':66cc66:untrash=1" class="vim-z vim-destructive" title="' . $untrash_title . '">' . $untrash_label . '</a>';
				}

				if ( ! EMPTY_TRASH_DAYS || 'spam' === $the_comment_status || 'trash' === $the_comment_status ) {
					$actions['delete'] = '<a href="' . $delete_url . '" data-wp-lists="delete:the-comment-list:comment-' . $comment->comment_ID . '::delete=1" class="delete vim-d vim-destructive" title="' . $delete_title . '">' . $delete_label . '</a>';
				} else {
					$actions['trash']  = '<a href="' . $trash_url . '" data-wp-lists="delete:the-comment-list:comment-' . $comment->comment_ID . '::trash=1" class="delete vim-d vim-destructive" title="' . $trash_title . '">' . $trash_label . '</a>';
				}

				if ( 'spam' !== $the_comment_status && 'trash' !== $the_comment_status ) {
					$actions['reply'] = '<a onclick="window.commentReply && commentReply.open( \''.$comment->comment_ID.'\',\''.$post->ID.'\' );return false;" class="vim-r" title="' . $reply_title . '" href="#">' . $reply_label . '</a>';
					$actions['edit']  = '<a href="comment.php?action=editcomment&amp;c=' . $comment->comment_ID . '" title="' . $edit_title . '">' . $edit_label . '</a>';
				}

				/** This filter is documented in wp-admin/includes/dashboard.php */
				$actions = apply_filters( 'review_row_actions', array_filter( $actions ), $comment );

				?>
				<div class="row-actions">
					<?php

					$i = 0;

					foreach ( $actions as $action => $link ) {

						++ $i;

						if ( ( ( 'approve' === $action || 'unapprove' === $action ) && 2 === $i ) || 1 === $i ) {
							$sep = '';
						} else {
							$sep = ' | ';
						}

						// Reply and quickedit need a hide-if-no-js span when not added with ajax
						if ( ( 'reply' === $action || 'quickedit' === $action ) && ! defined( 'DOING_AJAX' ) ) {

							$action .= ' hide-if-no-js';

						} elseif ( ( $action === 'untrash' && $the_comment_status === 'trash' ) || ( $action === 'unspam' && $the_comment_status === 'spam' ) ) {

							if ( 1 === (int) get_comment_meta( $comment->comment_ID, '_wp_trash_meta_status', true ) ) {
								$action .= ' approve';
							} else {
								$action .= ' unapprove';
							}

						}

						echo '<span class="' . $action . '">' . $sep . $link . '</span>';
					}

					?>
				</div>

			<?php endif; ?>

		<?php endif; ?>

		<?php
	}


	/**
	 * Render the product column HTML
	 *
	 * @param WP_Comment $comment
	 * @return string
	 */
	public function column_product( $comment ) {
		return sprintf( '<a href="%1$s">%2$s</a>', get_edit_post_link( $comment->comment_post_ID ), get_the_title( $comment->comment_post_ID ) );
	}


	/**
	 * Render the parent column HTML
	 *
	 * @param WP_Comment $comment
	 * @return string
	 */
	public function column_parent( $comment ) {

		$link = '';

		if ( $comment->comment_parent ) {

			$parent_comment = get_comment( $comment->comment_parent );
			$parent_comment->comment_author;

			/** translators: Placeholders: %1$s opening <a> link tag, %2$s review author, %3$s closing <a> link tag */
			$link = sprintf( __( '%1$sReview by %2$s%3$s', 'woocommerce-product-reviews-pro' ),
				'<a href="' . get_edit_comment_link( $comment->comment_parent ) . '">',
				'</a>',
				$parent_comment->comment_author
			);
		}

		/**
		 * Filter the review parent link
		 *
		 * @param string $link The default link (review post/product)
		 * @param WP_comment $comment The comment object
		 */
		return apply_filters( 'review_column_parent_link', $link, $comment );

	}


	/**
	 * Get column content
	 *
	 * @param WP_Comment $comment Comment object
	 * @param string $column_name
	 * @return array
	 */
	public function column_default( $comment, $column_name ) {

		/**
		 * Fires when the default column output is displayed for a single row.
		 *
		 * @param string $column_name         The custom column's name.
		 * @param int    $comment->comment_ID The custom column's unique ID number.
		 */
		do_action( 'manage_reviews_custom_column', $column_name, $comment->comment_ID );
	}


	/**
	 * Process the selected bulk action
	 */
	public function process_bulk_action() {

		if ( current_user_can( 'manage_woocommerce' ) ) {

			$do_action = $this->current_action();

			if ( $do_action ) {

				check_admin_referer( 'bulk-reviews' );

				$query_string = remove_query_arg( 'page', $_SERVER['QUERY_STRING'] );

				// Replace current nonce with bulk-comments nonce
				$comments_nonce = wp_create_nonce( 'bulk-comments' );
				$query_string   = remove_query_arg( '_wpnonce', $query_string );
				$query_string   = add_query_arg( '_wpnonce', $comments_nonce, $query_string );

				// Redirect to edit-comments.php
				wp_redirect( esc_url_raw( admin_url( 'edit-comments.php?' . $query_string ) ) );
				exit;

			} elseif ( ! empty( $_GET['_wp_http_referer'] ) ) {

				wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
				exit;
			}
		}
	}


	/**
	 * Add contribution types as allowed comment types for avatars
	 *
	 * @since 1.0.0
	 * @param array $allowed_types
	 * @return array
	 */
	public function add_contribution_avatar_types( $allowed_types ) {

		$contribution_types = array_keys( wc_product_reviews_pro_get_contribution_types() );

		return array_unique( array_merge( $allowed_types, $contribution_types ) );
	}


}
