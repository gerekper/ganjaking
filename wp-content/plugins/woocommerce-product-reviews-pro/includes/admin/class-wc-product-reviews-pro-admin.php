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
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Admin class
 *
 * @since 1.0.0
 */
class WC_Product_Reviews_Pro_Admin {


	/** @var \WC_Reviews instance */
	private $reviews;


	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->reviews = wc_product_reviews_pro()->load_class( '/includes/admin/class-wc-reviews.php', 'WC_Reviews' );

		// Add 'flagged' status link
		add_filter( 'review_status_links', array( $this, 'contribution_status_links' ) );

		// Add custom contribution columns
		add_filter( 'manage_woocommerce_page_reviews_columns',          array( $this, 'add_custom_contributions_columns' ) );
		add_filter( 'manage_woocommerce_page_reviews_sortable_columns', array( $this, 'make_custom_contributions_sortable_columns' ) );

		// Render custom column contents
		add_action( 'manage_reviews_custom_column', array( $this, 'custom_contribution_column' ), 10, 2 );
		add_filter( 'review_column_parent_link',    array( $this, 'contribution_column_parent_link' ), 10, 2 );

		// Add type/media type filters to contributions screen
		add_action( 'restrict_manage_reviews', array( $this, 'restrict_manage_contribution_types' ), 1 );
		add_action( 'restrict_manage_reviews', array( $this, 'restrict_manage_contributions' ) );

		// filter/order contributions by custom fields
		add_filter( 'parse_comment_query', array( $this, 'modify_contributions_query' ) );

		// load frontend styles and scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		// redirect to reviews edit screen admin links pointing to the comments screen requesting a product
		add_action( 'admin_init', array( $this, 'redirect_edit_comments_for_product_reviews_screen' ) );

		// Process custom contribution actions
		add_action( 'admin_init', array( $this, 'process_contribution_action' ), 99 );

		// Display messages
		add_action( 'load-woocommerce_page_reviews', array( $this, 'enqueue_contribution_messages' ) );

		// Add contribution-related settings
		add_filter( 'woocommerce_products_general_settings', array( $this, 'add_contribution_settings' ) );

		// add product reviews admin report
		add_filter( 'woocommerce_admin_reports', array( $this, 'add_admin_reports' ) );

		// Add meta boxes
		// Add meta boxes
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );

		// Save contribution title comment meta field
		add_action( 'comment_edit_redirect', array( $this, 'save_contribution_title_meta_box' ), 1, 2 );

		// Show contribution title in review list
		add_action( 'woocommerce_reviews_list_before_comment_text', array( $this, 'add_title_in_review_list' ) );

		// Filter Edit Comment screen title & heading
		add_filter( 'gettext', array( $this, 'filter_edit_comments_screen_translations' ), 9, 2 );
	}


	/**
	 * Returns the Reviews admin handler instance.
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Reviews
	 */
	public function get_reviews_instance() {

		return $this->reviews;
	}


	/**
	 * Formats a URL pointing to a contribution status admin page.
	 *
	 * @since 1.10.0
	 *
	 * @param string $url base URL
	 * @param string $comment_type the comment type (default 'all')
	 * @return string URL
	 */
	private function get_flagged_contribution_status_url( $url, $comment_type = 'all' ) {
		global $post_id;

		// add comment type to the base link
		if ( ! empty( $comment_type ) && 'all' !== $comment_type ) {
			$url = add_query_arg( 'comment_type', $comment_type, $url );
		}

		$url = remove_query_arg( 'comment_status', $url );
		$url = add_query_arg( 'is_flagged', 1, $url );

		// if viewing contributions for a specific product, add that to the link as well
		if ( $post_id ) {
			$url = add_query_arg( 'p', absint( $post_id ), $url );
		}

		return $url;
	}


	/**
	 * Returns the flagged contributions status link used in the reviews edit screen.
	 *
	 * @since 1.10.0
	 *
	 * @param int $flagged_count the flagged contributions count to use in the HTML
	 * @param null|string $url a valid URL or will use the default one if null
	 * @return string HTML link
	 */
	public function get_flagged_contribution_status_link( $flagged_count, $url = null ) {
		global $comment_type;

		// are we viewing flagged contributions?
		$is_flagged = isset( $_REQUEST['is_flagged'] ) && $_REQUEST['is_flagged'];
		$class      = $is_flagged ? ' class="current"' : '';

		/* translators: Placeholders: %s contribution flagged n times */
		$label = _n_noop( 'Flagged <span class="count">(<span class="flagged-count">%s</span>)</span>', 'Flagged <span class="count">(<span class="flagged-count">%s</span>)</span>', 'woocommerce-product-reviews-pro' );
		$url   = $this->get_flagged_contribution_status_url( null === $url ? add_query_arg( 'page', 'reviews', 'admin.php' ) : $url, $comment_type );

		// translate and format link
		return '<a href="' . esc_url( $url ) . '" ' . $class . '>' . sprintf( translate_nooped_plural( $label, $flagged_count ), number_format_i18n( $flagged_count ) ) . '</a>';
	}


	/**
	 * Handles the contributions status links shown in the reviews edit screen.
	 *
	 * Adds a 'flagged' custom status link to standard contributions (comment) statuses.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $status_links associative array
	 * @return array
	 */
	public function contribution_status_links( array $status_links ) {
		global $post_id, $wpdb;

		// remove current class from "all" when viewing flagged contributions
		if ( ! empty( $_REQUEST['is_flagged'] ) ) {
			$status_links['all'] = str_replace( 'current', '', $status_links['all'] );
		}

		// fetch number of flagged contributions, optionally filtered by current post_id (product)
		$where_comment = $post_id ? $wpdb->prepare( " AND c.comment_post_ID = %d", $post_id ) : '';
		$flagged_count = max( 0, (int) $wpdb->get_var( "
			SELECT COUNT(c.comment_ID)
			FROM {$wpdb->comments} c
			LEFT JOIN {$wpdb->commentmeta} m ON c.comment_ID = m.comment_id
			WHERE m.meta_key = 'flag_count'
			AND m.meta_value > 0
			{$where_comment}
		" ) );

		$flagged_link = $this->get_flagged_contribution_status_link( $flagged_count );

		return Framework\SV_WC_Helper::array_insert_after( $status_links, 'spam', array( 'is_flagged' => $flagged_link ) );
	}


	/**
	 * Add custom contribution columns
	 *
	 * @since 1.0.0
	 * @param  array $columns
	 * @return array
	 */
	public function add_custom_contributions_columns( array $columns ) {

		$columns = Framework\SV_WC_Helper::array_insert_after( $columns, 'cb',      array( 'type'  => __( 'Type',  'woocommerce-product-reviews-pro' ) ) );
		$columns = Framework\SV_WC_Helper::array_insert_after( $columns, 'comment', array( 'votes' => __( 'Votes', 'woocommerce-product-reviews-pro' ) ) );
		$columns = Framework\SV_WC_Helper::array_insert_after( $columns, 'votes',   array( 'flags' => _x( 'Flags', 'number of times contribution has been flagged', 'woocommerce-product-reviews-pro' ) ) );

		return $columns;
	}


	/**
	 * Make custom columns sortable
	 *
	 * @since 1.0.0
	 * @param array $sortable Columns
	 * @return array $sortable Filtered columns to make sortable
	 */
	public function make_custom_contributions_sortable_columns( $sortable ) {

		$sortable['type']  = 'comment_type';
		$sortable['votes'] = 'comment_karma';
		$sortable['flags'] = 'flag_count';

		return $sortable;
	}


	/**
	 * Output custom contribution column content
	 *
	 * @since 1.0.0
	 * @param string $column_name
	 * @param int $comment_id
	 */
	public function custom_contribution_column( $column_name, $comment_id ) {
		global $comment;

		$contribution = wc_product_reviews_pro_get_contribution( $comment );

		switch ( $column_name ) {

			case 'type':

				$contribution_type_instance = wc_product_reviews_pro_get_contribution_type( $comment->comment_type );
				$contribution_type          = $contribution_type_instance->type;
				$contribution_title         = $contribution_type_instance->get_title();

				// Handle existing WooCommerce reviews (comments) before plugin activation
				if ( empty( $comment->comment_type ) ) {
					$contribution_type  = 0 !== (int) $comment->comment_parent ? 'contribution_comment' : 'review';
					$contribution_title = 'review' === $contribution_type      ? __( 'Review', 'woocommerce-product-reviews-pro' ) : __( 'Comment', 'woocommerce-product-reviews-pro' );
				}

				/* translators: Placeholders: %1$s - Contribution type (CSS class), %2$s - Contribution type name */
				printf( '<span class="contribution-type contribution-type-%1$s">%2$s</span>', esc_attr( $contribution_type ), esc_html( $contribution_title ) );

				// show when a review or question has an attachment
				if ( $contribution->has_attachment() && ( 'review' === $contribution_type || 'question' === $contribution_type ) ) {
					printf( '<br /><span title="%1$s %2$s" class="has-attachment">%1$s</span>', esc_html( ucwords( $contribution->get_attachment_type() ) ), esc_html( __( 'attached', 'woocommerce-product-reviews-pro' ) ) );
				}

			break;

			case 'votes':

				echo (int) $contribution->get_positive_votes(); ?><span class="vote vote-up"   data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>" title="<?php esc_attr_e( 'Positive votes', 'woocommerce-product-reviews-pro' ); ?>"></span><br><?php
				echo (int) $contribution->get_negative_votes(); ?><span class="vote vote-down" data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>" title="<?php esc_attr_e( 'Negative votes', 'woocommerce-product-reviews-pro' ); ?>"></span><?php

			break;

			case 'flags':

				echo (int) $contribution->get_flag_count();

			break;
		}
	}


	/**
	 * Loads admin styles and scripts.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix the current screen or URL filename, ie edit.php, post.php, etc.
	 */
	public function load_styles_scripts( $hook_suffix ) {

		// reviews edit screens
		if ( 'comment.php' === $hook_suffix || 'product_page_reviews' === $hook_suffix || Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id( 'reviews' ) === $hook_suffix ) {

			wp_enqueue_style( 'wc-product-reviews-pro-admin-reviews', wc_product_reviews_pro()->get_plugin_url() . '/assets/css/admin/wc-product-reviews-pro-admin-reviews.min.css', null, \WC_Product_Reviews_Pro::VERSION );

			if ( isset( $_GET['action'] ) && 'editcomment' === $_GET['action'] ) {

				wp_enqueue_script( 'wc-product-reviews-pro-admin-reviews', wc_product_reviews_pro()->get_plugin_url() . '/assets/js/admin/wc-product-reviews-pro-admin-reviews.min.js', array( 'jquery' ), \WC_Product_Reviews_Pro::VERSION );

				wp_localize_script( 'wc-product-reviews-pro-admin-reviews', 'wc_product_reviews_pro_admin_reviews', array(

					'ajax_url'                         => admin_url( 'admin-ajax.php' ),
					'nonce'                            => wp_create_nonce( 'wc-product-reviews-pro-admin' ),
					'handle_contribution_flag_nonce'   => wp_create_nonce( 'handle-contribution-flag' ),
					'update_contribution_status_nonce' => wp_create_nonce( 'update-contribution-status' ),

					'i18n'     => array(
						'confirm_delete_flag'       => __( 'Are you sure you want to remove this flag? This action cannot be undone.', 'woocommerce-product-reviews-pro' ),
						'confirm_delete_flags'      => __( 'Are you sure you want to remove these flags? This action cannot be undone.', 'woocommerce-product-reviews-pro' ),
						'confirm_remove_attachment' => __( 'Are you sure you want to remove the attachment from this contribution?', 'woocommerce-product-reviews-pro' ),
						'error_removing_attachment' => __( 'There was an error removing the attachment. Please try again later.', 'woocommerce-product-reviews-pro' ),
					),
				) );
			}

		// review qualifiers
		} elseif ( 'edit.php' === $hook_suffix && isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] ) {

			wc_enqueue_js( "
				$( '#wpbody' ).on( 'click', '#doaction, #doaction2', function() {

					var tax = 'product_review_qualifier';

					$( 'tr.inline-editor textarea[name=\"tax_input['+tax+']\"]' ).suggest( ajaxurl + '?action=ajax-tag-search&tax=' + tax, { delay: 500, minchars: 2, multiple: true, multipleSep: inlineEditL10n.comma } );
				} );
			" );

		// settings
		} elseif ( isset( $_REQUEST['tab'] ) && 'products' === $_REQUEST['tab'] && Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id() === $hook_suffix ) {

			wp_enqueue_script( 'wc-product-reviews-pro-admin-settings', wc_product_reviews_pro()->get_plugin_url() . '/assets/js/admin/wc-product-reviews-pro-admin-settings.min.js', array( 'jquery' ), WC_Product_Reviews_Pro::VERSION );
		}
	}


	/**
	 * Outputs the contribution type selector.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function restrict_manage_contribution_types() {
		global $comment_type;

		?>
		<select name="comment_type">
			<option value=""><?php esc_html_e( 'All contribution types', 'woocommerce-product-reviews-pro' ); ?></option>
			<?php

			$contribution_types = array();

			foreach ( wc_product_reviews_pro_get_contribution_types() as $type ) {
				$contribution_type           = wc_product_reviews_pro_get_contribution_type( $type );
				$contribution_types[ $type ] = $contribution_type->get_title();
			}

			/**
			 * Filters the comment types dropdown menu.
			 *
			 * @since 1.0.0
			 *
			 * @param array $contribution_types an array of contribution types
			 */
			$contribution_types = apply_filters( 'admin_contribution_types_dropdown', $contribution_types );

			?>
			<?php foreach ( $contribution_types as $type => $label ) : ?>
				<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $comment_type, $type, true ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php

		// small CSS adjustment
		wc_enqueue_js( 'jQuery( ".bulkactions select" ).css( "margin-left", 0 );' );
	}


	/**
	 * Add media type dropdown to contributions list screen filter
	 *
	 * Also adds the is_flagged hidden input to the filter form
	 *
	 * @since 1.0.0
	 */
	public function restrict_manage_contributions() {

		$is_flagged = isset( $_REQUEST['is_flagged'] ) ? $_REQUEST['is_flagged'] : '';

		$media_options = array(
			''      => __( 'All media', 'woocommerce-product-reviews-pro' ),
			'photo' => __( 'Photo', 'woocommerce-product-reviews-pro' ),
			'video' => __( 'Video', 'woocommerce-product-reviews-pro' ),
		);

		$current = isset( $_REQUEST['media'] ) ? $_REQUEST['media'] : '';

		?>
		<select name="media">
			<?php foreach ( $media_options as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>

		<input type="hidden" name="is_flagged" value="<?php echo esc_attr( $is_flagged ); ?>" />
		<?php
	}

	/**
	 * Modifies the contributions query.
	 *
	 * @see https://core.trac.wordpress.org/ticket/23469
	 * @see https://gist.github.com/markjaquith/681af58ce22d79c08c09
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Comment_Query $comment_query the WP_Comment_Query instance
	 * @return \WP_Comment_Query
	 */
	public function modify_contributions_query($comment_query ) {

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( $screen instanceof \WP_Screen && ( 'product_page_reviews' === $screen->id || Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id( 'reviews' ) === $screen->id ) ) {

			// ORDERBY

			if ( 'flag_count' === $comment_query->query_vars['orderby'] ) {

				if ( ! empty( $_REQUEST['is_flagged'] ) ) {
					// if we are only viewing flagged comments, this is easy...
					$comment_query->query_vars['orderby']  = 'meta_value_num';
					$comment_query->query_vars['meta_key'] = 'flag_count';
				} else {
					// ...otherwise, we need to pull up some magic
					add_filter( 'comments_clauses', array( $this, 'orderby_flag_count' ), 1, 2 );
				}
			}

			// FILTERS

			// get the existing meta_query or create one now
			$meta_query = isset( $comment_query->query_vars['meta_query'] ) && is_array( $comment_query->query_vars['meta_query'] ) ? $comment_query->query_vars['meta_query'] : array();

			// filter by attached media type
			if ( ! empty( $_REQUEST['media'] ) ) {
				$meta_query[] = array(
					'key'   => 'attachment_type',
					'value' => $_REQUEST['media'],
				);
			}

			// support querying flagged contributions
			if ( ! empty( $_REQUEST['is_flagged'] ) ) {
				$meta_query[] = array(
					'key'     => 'flag_count',
					'value'   => 1,
					'compare' => '>=',
					'type'    => 'NUMERIC'
				);
			}

			if ( ! empty( $meta_query ) ) {

				if ( count( $meta_query ) > 1 ) {
					$meta_query['relation'] = 'AND';
				}

				$comment_query->query_vars['meta_query'] = $meta_query;
			}
		}

		return $comment_query;
	}


	/**
	 * Redirects admins to the reviews screen when requesting filtered views of comments for a product.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function redirect_edit_comments_for_product_reviews_screen() {
		global $pagenow;

		if (   'edit-comments.php' === $pagenow
			 && ! empty( $_GET['p'] )
			 && is_numeric( $_GET['p'] )
			 && 'product' === get_post_type( $_GET['p'] )
			 && current_user_can( 'manage_woocommerce' ) ) {

			wp_safe_redirect( add_query_arg( array( 'page' => 'reviews', 'p' => (int) $_GET['p'] ), admin_url( 'admin.php' ) ) );
		}
	}


	/**
	 * Process the selected action for a single contribution
	 *
	 * @since 1.0.0
	 */
	public function process_contribution_action() {

		if ( ! isset( $_REQUEST['action'] ) || ! isset( $_REQUEST['c'] ) ) {
			return;
		}

		switch ( $_REQUEST['action'] ) {
			case 'flagcomment' :

				$comment_id = absint( $_REQUEST['c'] );

				check_admin_referer( 'delete-comment_' . $comment_id );

				$noredir = isset( $_REQUEST['noredir'] );

				if ( ! $comment = get_comment( $comment_id ) ) {
					comment_footer_die( __( 'Oops, no comment with this ID.', 'woocommerce-product-reviews-pro' ) . sprintf( ' <a href="%s">' . __( 'Go back', 'woocommerce-product-reviews-pro' ) . '</a>.', 'admin.php?page=contributions' ) );
				}

				if ( ! $noredir && '' !== wp_get_referer() && false === strpos( wp_get_referer(), 'page=contributions' ) ) {
					$redir = wp_get_referer();

				} elseif ( ! $noredir && '' !== wp_get_original_referer() ) {
					$redir = wp_get_original_referer();

				} else {
					$redir = admin_url( 'admin.php?page=contributions' );
				}

				$redir = remove_query_arg( array( 'ids', 'flagged' ), $redir );

				$contribution = wc_product_reviews_pro_get_contribution( $comment_id );

				if ( $contribution && $contribution->flag() ) {
					$redir = add_query_arg( array( 'flagged' => '1' ), $redir );
				}

				wp_redirect( esc_url_raw( $redir ) );
				exit;

			break;
		}

	}


	/**
	 * Filter comment SQL clauses when sorting by flag_count
	 *
	 * Since WP_Meta_Query doesn't really support this kind of query,
	 * we need to construct it 'manually' by modifying the comment
	 * query SQL clauses. This ensures that when sorting by flag_count,
	 * ALL the comments are returned, regardless if they have the flag_count
	 * meta or not.
	 *
	 * @since 1.0.0
	 * @param array $pieces
	 * @return array modified pieces
	 */
	public function orderby_flag_count( $pieces ) {
		global $wpdb;

		$pieces['join']   .= " LEFT JOIN $wpdb->commentmeta cm ON ( wp_comments.comment_ID = cm.comment_id AND cm.meta_key = 'flag_count' )";
		$pieces['where']  .= " AND ( cm.meta_key = 'flag_count' OR cm.comment_id IS NULL )";
		$pieces['orderby'] = "cm.meta_value+0";

		return $pieces;
	}


	/**
	 * Enqueue (hook) admin notices to be shown on contributions screen
	 *
	 * @since 1.0.0
	 */
	public function enqueue_contribution_messages() {

		add_action( 'admin_notices', array( $this, 'contribution_admin_notices' ) );
	}


	/**
	 * Add contribution admin notices
	 *
	 * @since 1.0.0
	 */
	public function contribution_admin_notices() {

		$messages = array();

		if ( isset( $_REQUEST['flagged'] ) ) {

			$flagged = isset( $_REQUEST['flagged'] ) ? (int) $_REQUEST['flagged'] : 0;

			if ( $flagged > 0 ) {
				/* translators: Placeholders: %s count of contributions flagged */
				$messages[] = sprintf( _n( '%s contribution flagged', '%s contributions flagged', $flagged, 'woocommerce-product-reviews-pro' ), $flagged );

				echo '<div id="moderated" class="updated"><p>' . implode( "<br/>\n", $messages ) . '</p></div>';
			}
		}
	}


	/**
	 * Show parent comment edit link in contribution parent column
	 *
	 * @since 1.0.0
	 * @param string $link
	 * @param \WP_Comment $comment
	 * @return string
	 */
	public function contribution_column_parent_link( $link, $comment ) {

		$link = '&mdash;';

		// This contribution is a comment/response to another contribution
		if ( $comment->comment_parent ) {

			$parent_comment = get_comment( $comment->comment_parent );

			$type = $parent_comment->comment_type ? $parent_comment->comment_type : 'review';
			$contribution_type = wc_product_reviews_pro_get_contribution_type( $type );

			ob_start();
			edit_comment_link( sprintf( _x( '%1$s by %2$s', '[contribution type] by [author name]', 'woocommerce-product-reviews-pro' ), $contribution_type->get_title(), $parent_comment->comment_author ) );
			$link = ob_get_clean();
		}

		return $link;
	}


	/**
	 * Adds contribution settings to product settings page.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings
	 * @return array associative array of settings
	 */
	public function add_contribution_settings( $settings ) {

		$contribution_type_options = array();

		foreach ( wc_product_reviews_pro_get_contribution_types() as $type ) {
			$contribution_type = wc_product_reviews_pro_get_contribution_type( $type );
			$contribution_type_options[ $type ] = $contribution_type->get_title();
		}

		$contribution_settings = array(
			array(
				'title'             => __( 'Contributions types', 'woocommerce-product-reviews-pro' ),
				'desc'              => __( 'Select which contribution types to enable', 'woocommerce-product-reviews-pro' ),
				'desc_tip'          => true,
				'id'                => 'wc_product_reviews_pro_enabled_contribution_types',
				'type'              => 'select',
				'class'             => 'wc-enhanced-select',
				'css'               => 'min-width: 350px;',
				'default'           => 'all',
				'options'           => array(
					'all'      => __( 'Enable all contribution types', 'woocommerce-product-reviews-pro' ),
					'specific' => __( 'Enable specific contribution types only', 'woocommerce-product-reviews-pro' )
				),
			),
			array(
				'title'             => __( 'Specific contribution types', 'woocommerce-product-reviews-pro' ),
				'desc'              => '',
				'id'                => 'wc_product_reviews_pro_specific_enabled_contribution_types',
				'class'             => 'wc-enhanced-select',
				'css'               => 'min-width: 350px;',
				'type'              => 'multiselect',
				'default'           => '',
				'options'           => $contribution_type_options,
			),
			array(
				'title'             => __( 'Admins can always reply', 'woocommerce-product-reviews-pro' ),
				'desc'              => __( 'Allow administrators and shop managers to leave replies to contributions', 'woocommerce-product-reviews-pro' ),
				'id'                => 'wc_product_reviews_pro_admins_can_always_reply',
				'type'              => 'checkbox',
			),
			array(
				'title'             => __( 'Allow Attachments', 'woocommerce-product-reviews-pro' ),
				'desc'              => __( 'Enable to allow customers to add photo and video attachments as part of a review or question', 'woocommerce-product-reviews-pro' ),
				'type'              => 'checkbox',
				'default'           => 'yes',
				'id'                => 'wc_product_reviews_pro_contribution_allow_attachments'
			),
			array(
				'title'             => __( 'Admin badges', 'woocommerce-product-reviews-pro' ),
				'type'              => 'text',
				'desc'              => __( 'Leave blank to disable badges.', 'woocommerce-product-reviews-pro' ),
				'desc_tip'          => __( 'Enter the text to use on badges displayed on admin and shop manager contributions.', 'woocommerce-product-reviews-pro' ),
				'id'                => 'wc_product_reviews_pro_contribution_badge',
				'default'           => __( 'Admin', 'woocommerce-product-reviews-pro' ),
			),
			array(
				'title'             => __( 'Sorting order', 'woocommerce-product-reviews-pro' ),
				'desc'              => __( 'Choose how contributions are sorted on product pages', 'woocommerce-product-reviews-pro' ),
				'desc_tip'          => true,
				'id'                => 'wc_product_reviews_pro_contributions_orderby',
				'type'              => 'select',
				'class'             => 'wc-enhanced-select',
				'css'               => 'min-width: 350px;',
				'default'           => 'most_helpful',
				'options'           => array(
					'most_helpful' => __( 'Most helpful first', 'woocommerce-product-reviews-pro' ),
					'newest'       => __( 'Newest first', 'woocommerce-product-reviews-pro' ),
				),
			),
			array(
				'title'             => __( 'Minimum word count', 'woocommerce-product-reviews-pro' ),
				'desc_tip'          => __( 'Users need to enter at least this amount of words when posting a contribution', 'woocommerce-product-reviews-pro' ),
				'type'              => 'text',
				'id'                => 'wc_product_reviews_pro_min_word_count',
			),
			array(
				'title'             => __( 'Maximum word count', 'woocommerce-product-reviews-pro' ),
				'desc_tip'          => __( 'Maximum number of words users can enter when posting a contribution', 'woocommerce-product-reviews-pro' ),
				'type'              => 'text',
				'id'                => 'wc_product_reviews_pro_max_word_count',
			),
			array(
				'title'             => __( 'Threshold for publication', 'woocommerce-product-reviews-pro' ),
				'desc_tip'          => __( 'Show contributions to public only after a minimum amount of submission is met', 'woocommerce-product-reviews-pro' ),
				'type'              => 'number',
				'id'                => 'wc_product_reviews_pro_contribution_threshold',
				'default'           => 1,
				'custom_attributes' => array(
					'min'  => 1,
					'step' => 1,
				),
			),
			array(
				'title'             => __( 'Flagged contributions', 'woocommerce-product-reviews-pro' ),
				'desc_tip'          => __( 'Change publication status should a contribution be flagged as inappropriate', 'woocommerce-product-reviews-pro' ),
				'type'              => 'select',
				'class'             => 'wc-enhanced-select',
				'css'               => 'min-width: 350px;',
				'id'                => 'wc_product_reviews_pro_flagged_contribution_handling',
				'default'           => 'keep_published',
				'options'           => array(
					'keep_published'            => __( 'Keep published', 'woocommerce-product-reviews-pro' ),
					'pending_approval_customer' => __( 'Set to pending approval when flagged by a registered customer', 'woocommerce-product-reviews-pro' ),
					'pending_approval_guest'    => __( 'Set to pending approval when flagged by anyone', 'woocommerce-product-reviews-pro' ),
				),
			),
			array(
				'title'             => __( 'Moderation', 'woocommerce-product-reviews-pro' ),
				'desc'              => __( 'Contributions must be manually approved', 'woocommerce-product-reviews-pro' ),
				'type'              => 'checkbox',
				'id'                => 'wc_product_reviews_pro_contribution_moderation'
			),
		);

		/**
		 * Filters the product contribution settings.
		 *
		 * @since 1.10.0
		 *
		 * @param array $contribution_settings associative array of settings
		 */
		$contribution_settings = (array) apply_filters( 'wc_product_reviews_pro_product_contribution_settings', $contribution_settings );

		$new_settings = array();

		foreach ( $settings as $setting ) {

			$new_settings[] = $setting;

			if ( 'product_rating_options' === $setting['id'] && 'title' === $setting['type'] ) {

				foreach ( $contribution_settings as $contribution_setting ) {

					$new_settings[] = $contribution_setting;
				}
			}
		}

		$settings = $new_settings;

		return $settings;
	}


	/**
	 * Add product reviews reports
	 *
	 * @since 1.0.0
	 * @param array $reports
	 * @return array
	 */
	public function add_admin_reports( $reports ) {

		$reports['reviews'] = array(
			'title'   => __( 'Reviews', 'woocommerce-product-reviews-pro' ),
			'reports' => array(
				'most_reviews' => array(
					'title'       => __( 'Most Reviews', 'woocommerce-product-reviews-pro' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( $this, 'get_most_reviews_admin_report' ),
				),
				'highest_rating' => array(
					'title'       => __( 'Highest Rating', 'woocommerce-product-reviews-pro' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( $this, 'get_highest_rating_admin_report' ),
				),
				'lowest_rating' => array(
					'title'       => __( 'Lowest Rating', 'woocommerce-product-reviews-pro' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( $this, 'get_lowest_rating_admin_report' ),
				),
			),
		);

		return $reports;
	}


	/**
	 * Output the most reviewed products report
	 *
	 * @since 1.0.0
	 */
	public static function get_most_reviews_admin_report() {

		require_once( wc_product_reviews_pro()->get_plugin_path() . '/includes/admin/class-wc-product-reviews-pro-admin-report-most-reviews.php' );
		$report = new \WC_Product_Reviews_Pro_Admin_Report_Most_Reviews();
		$report->output_report();
	}


	/**
	 * Output the highest rated products report
	 *
	 * @since 1.0.0
	 */
	public static function get_highest_rating_admin_report() {

		require_once( wc_product_reviews_pro()->get_plugin_path() . '/includes/admin/class-wc-product-reviews-pro-admin-report-highest-rating.php' );
		$report = new \WC_Product_Reviews_Pro_Admin_Report_Highest_Rating();
		$report->output_report();
	}


	/**
	 * Output the lowest rated products report
	 *
	 * @since 1.0.0
	 */
	public static function get_lowest_rating_admin_report() {

		require_once( wc_product_reviews_pro()->get_plugin_path() . '/includes/admin/class-wc-product-reviews-pro-admin-report-lowest-rating.php' );
		$report = new \WC_Product_Reviews_Pro_Admin_Report_Lowest_Rating();
		$report->output_report();
	}


	/**
	 * Adds contribution meta boxes.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( $screen instanceof \WP_Screen && isset( $_GET['c'] ) && 'comment' === $screen->id ) {

			$comment_id = (int) $_GET['c'];
			$comment    = get_comment( $comment_id );

			if ( $comment && in_array( $comment->comment_type, wc_product_reviews_pro_get_contribution_types(), false ) ) {

				$contribution = wc_product_reviews_pro_get_contribution( $comment );

				if ( in_array( $contribution->get_type(), array( 'review', 'photo', 'video' ), false ) ) {
					add_meta_box( 'wc-product-reviews-pro-title', __( 'Title', 'woocommerce-product-reviews-pro' ), array( $this, 'contribution_title_meta_box' ), 'comment', 'normal', 'high' );
				}

				add_meta_box( 'wc-product-reviews-pro-stats', __( 'Stats', 'woocommerce-product-reviews-pro' ), array( $this, 'contribution_stats_meta_box' ), 'comment', 'normal', 'high' );
				add_meta_box( 'wc-product-reviews-pro-flags', __( 'Flags', 'woocommerce-product-reviews-pro' ), array( $this, 'contribution_flags_meta_box' ), 'comment', 'normal', 'high' );

				if ( $contribution->has_attachment() || in_array( $contribution->get_type(), array( 'video', 'photo' ), false ) ) {
					add_meta_box( 'wc-product-reviews-pro-attachment', __( 'Attached media', 'woocommerce-product-reviews-pro' ), array( $this, 'contribution_attachment_meta_box' ), 'comment', 'normal', 'high' );
				}
			}
		}
	}


	/**
	 * Saves the title comment meta
	 *
	 * @since 1.0.4
	 * @param string $location The URI the user will be redirected to.
	 * @param int $comment_id The ID of the comment being edited.
	 * @return string The URI the user should be redirected to.
	 */
	public function save_contribution_title_meta_box( $location, $comment_id ) {

		// $comment_id is required
		if ( empty( $comment_id ) ) {
			return $location;
		}

		// Check if the title is set
		if ( ! isset( $_POST['title'] ) ) {
			return $location;
		}

		// Check the nonce
		if ( empty( $_POST['wc_product_reviews_comment_meta_nonce'] ) || ! wp_verify_nonce( $_POST['wc_product_reviews_comment_meta_nonce'], 'wc_product_reviews_pro_save_comment_meta' ) ) {
			return $location;
		}

		// Check if user has permission to edit comments
		if ( ! current_user_can( 'edit_comment', $comment_id ) ) {
			return $location;
		}

		$comment      = get_comment( $comment_id );
		$contribution = wc_product_reviews_pro_get_contribution( $comment );

		// save the comment meta if the contribution type supports the title field
		if ( in_array( $contribution->get_type(), array( 'review', 'photo', 'video' ), false ) ) {

			update_comment_meta( $comment_id, 'title', $_POST['title'] );
		}

		return $location;
	}


	/**
	 * Outputs the title meta box HTML.
	 *
	 * @since 1.0.4
	 *
	 * @param null|\WP_Comment comment object (optional, not used in callback)
	 */
	public function contribution_title_meta_box( $the_comment = null ) {
		global $comment;

		$the_comment = null === $the_comment ? $comment : $the_comment;

		?>
		<input
			type="text"
			name="title"
			value="<?php echo esc_attr( $the_comment instanceof \WP_Comment ? get_comment_meta( $the_comment->comment_ID, 'title', true ) : '' ); ?>"
			id="title"
			style="width:100%;"
		/>
		<?php
	}


	/**
	 * Outputs the stats meta box HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param null|\WP_Comment comment object (optional, not used in callback)
	 */
	public function contribution_stats_meta_box( $the_comment = null ) {
		global $comment;

		$the_comment = null === $the_comment ? $comment : $the_comment;

		// the stats meta box is always printed, unlike maybe others, so we put our nonce here
		wp_nonce_field( 'wc_product_reviews_pro_save_comment_meta', 'wc_product_reviews_comment_meta_nonce' );

		if ( $contribution = wc_product_reviews_pro_get_contribution( $the_comment ) ) :

			$contribution_type = wc_product_reviews_pro_get_contribution_type( $contribution->get_type() );

			/* @see \WC_Meta_Box_Product_Reviews::save() this is a workaround to prevent a possible PHP notice */ ?>
			<?php if ( 'review' !== $contribution->get_type() ) : ?>

				<input
					type="hidden"
					name="woocommerce_meta_nonce"
					value=""
				/>

			<?php endif; ?>

			<table class="stats">
				<tbody>
					<tr class="stat type">
						<th class="odd"><strong><?php esc_html_e( 'Type', 'woocommerce-product-reviews-pro' ); ?></strong></th>
						<td class="odd"><?php echo esc_html( $contribution_type->get_title() ); ?></td>
					</tr>
					<tr class="stat product">
						<th class="even"><strong><?php esc_html_e( 'Product', 'woocommerce-product-reviews-pro' ); ?></strong></th>
						<td class="even"><a href="<?php esc_url( get_edit_post_link( $contribution->get_product_id() ) ); ?>"><?php echo esc_html( get_the_title( $contribution->get_product_id() ) ); ?></a></td>
					</tr>
					<tr class="stat upvotes">
						<th class="odd"><strong><?php esc_html_e( 'Upvotes', 'woocommerce-product-reviews-pro' ); ?></strong></th>
						<td class="odd"><?php echo esc_html( $contribution->get_positive_votes() ); ?></td>
					</tr>
					<tr class="stat downvotes">
						<th class="even"><strong><?php esc_html_e( 'Downvotes', 'woocommerce-product-reviews-pro' ); ?></strong></th>
						<td class="even"><?php echo esc_html( $contribution->get_negative_votes() ); ?></td>
					</tr>
					<tr class="stat active-flags">
						<th class="odd"><strong><?php esc_html_e( 'Active Flags', 'woocommerce-product-reviews-pro' ); ?></strong></th>
						<td class="odd"><span><?php echo $contribution->get_flag_count(); ?></span></td>
					</tr>

					<?php $review_qualifiers = wp_get_post_terms( $contribution->get_product_id(), 'product_review_qualifier' ); ?>

					<?php if ( ! empty( $review_qualifiers ) ) : ?>

						<?php

						$applied_qualifiers = array();

						foreach ( $review_qualifiers as $review_qualifier ) {
							if ( $value = get_comment_meta( $contribution->id, 'wc_product_reviews_pro_review_qualifier_' . $review_qualifier->term_id, true ) ) {
								$applied_qualifiers[ $review_qualifier->name ] = $value;
							}
						}

						?>

						<?php if ( ! empty( $applied_qualifiers ) ) : ?>

							<tr class="stat qualifiers">
								<th class="even"><?php esc_html_e( 'Qualifiers', 'woocommerce-product-reviews-pro' ); ?></th>
								<td class="even">
									<ul>
										<?php foreach ( $applied_qualifiers as $qualifier => $value ) : ?>
											<li><?php echo esc_html( $qualifier . ' - ' . $value ); ?></li>
										<?php endforeach; ?>
									</ul>
								</td>
							</tr>

						<?php endif; ?>

					<?php endif; ?>
				</tbody>
			</table>
			<?php

		endif;
	}


	/**
	 * Outputs the flags meta box HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param null|\WP_Comment $the_comment the comment object (optional, not used in callback)
	 */
	public function contribution_flags_meta_box( $the_comment = null ) {
		global $comment;

		$the_comment = null === $the_comment ? $comment : $the_comment;

		if ( $contribution = wc_product_reviews_pro_get_contribution( $the_comment ) ) :

			$contribution_id = $contribution->get_id();
			$flags           = $contribution->get_flags();

			?>
			<div class="tablenav top">
				<div class="alignleft actions bulkactions">
					<?php // note: do not name this just 'action' or it may disrupt the default "editcomment" action ?>
					<select name="bulk-action">
						<option value=""><?php esc_html_e( 'Bulk Actions', 'woocommerce-product-reviews-pro' ); ?></option>
						<option value="resolve"><?php esc_html_e( 'Resolve', 'woocommerce-product-reviews-pro' ); ?></option>
						<option value="delete"><?php esc_html_e( 'Delete', 'woocommerce-product-reviews-pro' ); ?></option>
					</select>
					<button class="button action" data-contribution-id="<?php echo esc_attr( $contribution_id ); ?>"><?php esc_html_e( 'Apply', 'woocommerce-product-reviews-pro' ); ?></button>
				</div>
			</div>
			<table class="flags">
				<thead>
					<tr>
						<th class="check-column"><input id="cb-select-all-1" type="checkbox" /></th>
						<th class="flagged-by"><?php esc_html_e( 'Flagged by', 'woocommerce-product-reviews-pro' ); ?></th>
						<th class="reason"><?php esc_html_e( 'Reason', 'woocommerce-product-reviews-pro' ); ?></th>
						<th class="actions"><?php esc_html_e( 'Actions', 'woocommerce-product-reviews-pro' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php $has_flags = ! empty( $flags ); ?>
					<?php if ( $has_flags ) : $i = 0; ?>

						<?php foreach ( $flags as $flag ) : $i++; ?>

							<?php $flag_id = $flag->get_id(); ?>

							<tr
								id="flag-<?php echo esc_attr( $flag_id ); ?>"
								class="flag <?php
									echo ( $flag->is_resolved() ? 'resolved' : 'unresolved' ) . ' ';
									echo ( $i % 2 === 0 ? 'even' : 'odd' );?>">

								<td class="check-column">
									<input
										id="cb-select-<?php echo $i - 1; ?>"
										class="select-flag-id"
										type="checkbox"
										value="<?php echo esc_attr( $flag_id ); ?>"
									/>
								</td>
								<td class="flagged-by">
									<?php if ( $flag->is_anonymous() ) : ?>
										<em><?php esc_html_e( 'Guest', 'woocommerce-product-reviews-pro' ); ?></em>
									<?php elseif ( $user = $flag->get_user() ) : ?>
										<a href="<?php echo esc_url( get_edit_user_link( $user->ID ) ); ?>"><?php echo esc_html( $user->display_name ); ?></a>
									<?php endif; ?>
									<div class="meta">
										<small><?php echo $flag->get_date( wc_date_format() . ' @ ' . wc_time_format() ); ?></small>
										<br />
										<small><?php echo $flag->has_ip() ? esc_html( $flag->get_ip() ) : '&ndash;' ?></small>
									</div>
								</td>
								<td class="reason">
									<?php if ( ! $flag->has_reason() ) : ?>
										<em><?php esc_html_e( 'No flag reasons given', 'woocommerce-product-reviews-pro' ); ?></em>
									<?php else : ?>
										<?php echo esc_html( $flag->get_reason() ); ?>
									<?php endif; ?>
								</td>
								<td class="actions">
									<?php if ( ! $flag->is_resolved() ) : ?>
										<button
											class="button resolve"
											data-action="resolve"
											data-contribution-id="<?php echo esc_attr( $contribution_id ); ?>"
											data-flag-id="<?php echo esc_attr( $flag_id ); ?>"
										><?php esc_html_e( 'Resolve', 'woocommerce-product-reviews-pro' ); ?></button>
									<?php endif ?>
									<button
										class="button delete"
										data-action="delete"
										data-contribution-id="<?php echo esc_attr( $contribution_id ); ?>"
										data-flag-id="<?php echo esc_attr( $flag_id ); ?>"
									><?php esc_html_e( 'Delete', 'woocommerce-product-reviews-pro' ); ?></button>
								</td>

							</tr>

						<?php endforeach; ?>

					<?php endif; ?>

					<tr class="no-flags" <?php if ( $has_flags ) { echo 'style="display:none;"'; } ?>>
						<td colspan="4" class="odd">
							<em><?php esc_html_e( 'No one has flagged this contribution for removal.', 'woocommerce-product-reviews-pro' ); ?></em>
						</td>
					</tr>

				</tbody>
				<tfoot>
					<tr>
						<th class="check-column"><input id="cb-select-all-2" type="checkbox" /></th>
						<th class="flagged-by"><?php esc_html_e( 'Flagged by', 'woocommerce-product-reviews-pro' ); ?></th>
						<th class="reason"><?php esc_html_e( 'Reason', 'woocommerce-product-reviews-pro' ); ?></th>
						<th class="actions"><?php esc_html_e( 'Actions', 'woocommerce-product-reviews-pro' ); ?></th>
					</tr>
				</tfoot>
			</table>
			<div class="tablenav bottom">
				<div class="alignleft actions bulkactions">
					<?php // note: do not name this just 'action' or it may disrupt the default "editcomment" action ?>
					<select name="bulk-action2">
						<option value=""><?php esc_html_e( 'Bulk Actions', 'woocommerce-product-reviews-pro' ); ?></option>
						<option value="resolve"><?php esc_html_e( 'Resolve', 'woocommerce-product-reviews-pro' ); ?></option>
						<option value="delete"><?php esc_html_e( 'Delete', 'woocommerce-product-reviews-pro' ); ?></option>
					</select>
					<button class="button action" data-contribution-id="<?php echo esc_attr( $contribution_id ); ?>"><?php esc_html_e( 'Apply', 'woocommerce-product-reviews-pro' ); ?></button>
				</div>
			</div>
			<?php

		endif;
	}


	/**
	 * Outputs the attachment meta box HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param null|\WP_Comment $the_comment comment object (optional, not used in callback)
	 */
	public function contribution_attachment_meta_box( $the_comment = null ) {
		global $comment;

		$the_comment = null === $the_comment ? $comment : $the_comment; ?>

		<?php if ( $contribution = wc_product_reviews_pro_get_contribution( $the_comment ) ) : ?>

			<?php

			// don't use the getter, we need to return an exact URL the getter will fetch the media lib URL if not external
			$attachment_url    = $contribution->attachment_url;
			$attachment_id     = $contribution->get_attachment_id();
			$image             = wp_get_attachment_image( $attachment_id, 'large' );
			$attachment_exists = ( $attachment_id && $image ) || $attachment_url;

			// attachment controls ?>
			<?php if ( $attachment_exists ) : ?>

				<p>
					<?php if ( $attachment_url ) : ?>
						<a href="<?php echo esc_url( $attachment_url ); ?>"><?php esc_html_e( 'View Source', 'woocommerce-product-reviews-pro' ); ?></a>
					<?php endif; ?>
					<?php if ( current_user_can( 'manage_woocommerce' ) ): ?>
						<?php if ( $attachment_id ) : ?>
							<a href="<?php echo get_edit_post_link( $attachment_id ); ?>"><?php esc_html_e( 'Edit attachment', 'woocommerce-product-reviews-pro' ); ?></a>
						<?php endif ; ?>
						| <a href="#" class="remove-attachment" data-comment-id="<?php echo esc_attr( $contribution->get_id() ); ?>"><?php esc_html_e( 'Remove attachment', 'woocommerce-product-reviews-pro' ); ?></a>
					<?php endif; ?>
				</p>

				<?php // display photo ?>
				<?php if ( 'photo' === $contribution->get_attachment_type() ) : ?>
					<?php if ( $attachment_url ) : ?>
						<img alt="" src="<?php echo esc_url( $attachment_url ); ?>" />
					<?php elseif ( $image ) : ?>
						<?php echo $image; ?>
					<?php endif; ?>
				<?php endif; ?>

				<?php // embed video, or simply display a link ?>
				<?php if ( $attachment_url && 'video' === $contribution->get_attachment_type() ) : ?>
					<?php $embed_code = wp_oembed_get( $attachment_url ); ?>
					<?php if ( $embed_code ) : ?>
						<p><?php printf( '<a href="%1$s">%2$s</a>', esc_url( $attachment_url ), esc_url( $attachment_url ) ); ?></p>
					<?php endif; ?>
				<?php endif; ?>

			<?php else : ?>

				<p><?php esc_html_e( 'Attachment has been removed', 'woocommerce-product-reviews-pro' ); ?></p>

			<?php endif; ?>

		<?php endif; ?>

		<?php
	}


	/**
	 * Display comment title just before the comment text
	 *
	 * @since 1.0.0
	 */
	public function add_title_in_review_list() {
		global $comment;

		if ( $title = get_comment_meta( $comment->comment_ID, 'title', true ) ) {
			echo '<h3 class="contribution-title">' . esc_html( $title ) . '</h3>';
		}
	}


	/**
	 * Replace Edit/Moderate Comment title/headline with Edit {$type}, when editing/moderating a contribution
	 *
	 * @param  string $translation Translated text.
	 * @param  string $text        Text to translate.
	 * @return string              Translated text.
	 */
	public function filter_edit_comments_screen_translations( $translation, $text ) {
		global $comment;

		$replace_texts = array( 'Edit Comment', 'Moderate Comment' );

		// Bail out if not a text we should replace
		if ( ! in_array( $text, $replace_texts, false ) ) {
			return $translation;
		}

		// Try to get comment from query params
		if ( ! $comment && isset( $_GET['action'], $_GET['c'] ) && 'editcomment' === $_GET['action'] ) {
			$comment_id = (int) $_GET['c'];
			$comment    = get_comment( $comment_id );
		}

		// Bail out if no comment type is set
		if ( ! $comment || ! $comment->comment_type ) {
			return $translation;
		}

		// only replace the translated text if we are editing a comment left on a product, which effectively means it's a review
		if ( in_array( $comment->comment_type, wc_product_reviews_pro_get_contribution_types(), false ) ) {

			$contribution_type = wc_product_reviews_pro_get_contribution_type( $comment->comment_type );

			switch ( $text ) {
				case 'Edit Comment':
					$translation = $contribution_type->get_edit_text();
					break;
				case 'Moderate Comment':
					$translation = $contribution_type->get_moderate_text();
					break;
			}
		}

		return $translation;
	}


}
