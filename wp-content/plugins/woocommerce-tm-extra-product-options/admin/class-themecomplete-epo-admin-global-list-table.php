<?php
/**
 * Extra Product Options Field List Table class
 *
 * @package Extra Product Options/Admin
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

require_once ABSPATH . 'wp-admin/includes/class-wp-posts-list-table.php';

/**
 * Extra Product Options Field List Table class
 *
 * Original WordPress class : class-wp-posts-list-table.php
 *
 * @package Extra Product Options/Admin
 * @version 6.0
 */
class THEMECOMPLETE_EPO_ADMIN_Global_List_Table extends WP_Posts_List_Table {

	/**
	 * Holds the admin edit link
	 *
	 * @var string
	 */
	protected $editlink;

	/**
	 * Constructor.
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = [] ) {

		$this->editlink = 'edit.php?post_type=product&page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;

		unset( $_GET['post_type'] ); // phpcs:ignore WordPress.Security.NonceVerification

		add_filter( 'wp_count_posts', [ $this, 'wp_count_posts' ], 10, 3 );

		parent::__construct(
			[
				'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
			]
		);

	}

	/**
	 * Find the amount of post's type
	 *
	 * @param object $counts An object containing the current post_type's post counts by status.
	 * @param string $type Post type to retrieve count. Default 'post'.
	 * @param string $perm 'readable' or empty. Default empty.
	 * @since 1.0
	 */
	public function wp_count_posts( $counts, $type, $perm ) {

		if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
			$counts = THEMECOMPLETE_EPO_HELPER()->wp_count_posts( $type, $perm );
		}

		return $counts;

	}

	/**
	 * Get an associative array ( id => link ) with the list
	 * of views available on this table.
	 *
	 * @return array
	 */
	protected function get_views() {

		$views = parent::get_views();

		if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
			unset( $views['mine'] );
		}

		return $views;
	}

	/**
	 * Helper to create links to edit.php with params.
	 *
	 * @param string[] $args  Associative array of URL parameters for the link.
	 * @param string   $label Link text.
	 * @param string   $class Optional. Class attribute. Default empty string.
	 *
	 * @return string The formatted link string.
	 */
	protected function get_edit_link( $args, $label, $class = '' ) {

		unset( $args['post_type'] );

		$url          = add_query_arg( $args, $this->editlink );
		$class_html   = '';
		$aria_current = '';
		if ( ! empty( $class ) ) {
			$class_html = sprintf(
				' class="%s"',
				esc_attr( $class )
			);

			if ( 'current' === $class ) {
				$aria_current = ' aria-current="page"';
			}
		}

		return sprintf(
			'<a href="%s"%s%s>%s</a>',
			esc_url( $url ),
			$class_html,
			$aria_current,
			$label
		);
	}

	/**
	 * Prepares the list of items for displaying.
	 */
	public function prepare_items() {

		parent::prepare_items();

		global $avail_post_stati, $wp_query, $per_page, $mode;

		$post_type = $this->screen->post_type;

		THEMECOMPLETE_EPO_WPML()->apply_query_filter();
		$avail_post_stati = wp_edit_posts_query( array_merge( $_GET, [ 'post_type' => $post_type ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.WP.GlobalVariablesOverride
		THEMECOMPLETE_EPO_WPML()->remove_query_filter();

		$this->hierarchical_display = ( is_post_type_hierarchical( $this->screen->post_type ) && 'menu_order title' === $wp_query->query['orderby'] );

		$per_page = $this->get_items_per_page( 'edit_' . $post_type . '_per_page' ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride

		// This filter is documented in wp-admin/includes/post.php.
		$per_page = apply_filters( 'edit_posts_per_page', $per_page, $post_type ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride

		if ( $this->hierarchical_display ) {
			$total_items = $wp_query->post_count;
		} elseif ( $wp_query->found_posts || $this->get_pagenum() === 1 ) {
			$total_items = $wp_query->found_posts;
		} else {
			$post_counts = (array) wp_count_posts( $post_type, 'readable' );

			if ( isset( $_REQUEST['post_status'] ) && in_array( $_REQUEST['post_status'], $avail_post_stati, true ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$total_items = $post_counts[ sanitize_text_field( wp_unslash( $_REQUEST['post_status'] ) ) ]; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			} elseif ( isset( $_REQUEST['show_sticky'] ) && sanitize_text_field( wp_unslash( $_REQUEST['show_sticky'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$total_items = $this->sticky_posts_count;
			} elseif ( isset( $_GET['author'] ) && get_current_user_id() === (int) $_GET['author'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$total_items = $this->user_posts_count;
			} else {
				$total_items = array_sum( $post_counts );

				// Subtract post types that are not included in the admin all list.
				foreach ( get_post_stati( [ 'show_in_admin_all_list' => false ] ) as $state ) {
					$total_items -= $post_counts[ $state ];
				}
			}
		}

		if ( $this->hierarchical_display ) {
			$total_pages = ceil( $total_items / $per_page );
		} else {
			$total_pages = $wp_query->max_num_pages;
		}

		$this->is_trash = isset( $_REQUEST['post_status'] ) && 'trash' === $_REQUEST['post_status']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$this->set_pagination_args(
			[
				'total_items' => $total_items,
				'total_pages' => $total_pages,
				'per_page'    => $per_page,
			]
		);
	}

}
