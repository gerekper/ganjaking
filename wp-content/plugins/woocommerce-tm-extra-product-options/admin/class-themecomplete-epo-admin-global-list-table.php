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
		global $locked_post_status, $avail_post_stati;

		$link_post_type = 'product';

		$post_type = $this->screen->post_type;

		if ( ! empty( $locked_post_status ) ) {
			return [];
		}

		$status_links = [];
		$num_posts    = wp_count_posts( $post_type, 'readable' );
		$total_posts  = array_sum( (array) $num_posts );
		$class        = '';

		$current_user_id = get_current_user_id();
		$all_args        = [
			'post_type' => $link_post_type,
			'page'      => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK,
		];
		$mine            = '';

		// Subtract post types that are not included in the admin all list.
		foreach ( get_post_stati( [ 'show_in_admin_all_list' => false ] ) as $state ) {
			$total_posts -= $num_posts->$state;
		}

		if ( $this->return_user_posts_count() && $this->return_user_posts_count() !== $total_posts ) {
			if ( isset( $_GET['author'] ) && ( $current_user_id === (int) $_GET['author'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$class = 'current';
			}

			$mine_args = [
				'post_type' => $link_post_type,
				'page'      => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK,
				'author'    => $current_user_id,
			];

			$mine_inner_html = sprintf(
				/* translators: %s: Number of posts. */
				_nx(
					'Mine <span class="count">(%s)</span>',
					'Mine <span class="count">(%s)</span>',
					$this->user_posts_count,
					'posts'
				),
				number_format_i18n( $this->user_posts_count )
			);

			$mine = [
				'url'     => esc_url( add_query_arg( $mine_args, 'edit.php' ) ),
				'label'   => $mine_inner_html,
				'current' => isset( $_GET['author'] ) && ( $current_user_id === (int) $_GET['author'] ), // phpcs:ignore WordPress.Security.NonceVerification
			];

			$all_args['all_posts'] = 1;
			$class                 = '';
		}

		$all_inner_html = sprintf(
			/* translators: %s: Number of posts. */
			_nx(
				'All <span class="count">(%s)</span>',
				'All <span class="count">(%s)</span>',
				$total_posts,
				'posts'
			),
			number_format_i18n( $total_posts )
		);

		$status_links['all'] = [
			'url'     => esc_url( add_query_arg( $all_args, 'edit.php' ) ),
			'label'   => $all_inner_html,
			'current' => $this->is_base_request() || isset( $_REQUEST['all_posts'] ), // phpcs:ignore WordPress.Security.NonceVerification
		];

		if ( $mine ) {
			$status_links['mine'] = $mine;
		}

		foreach ( get_post_stati( [ 'show_in_admin_status_list' => true ], 'objects' ) as $status ) {
			$class = '';

			$status_name = $status->name;

			if ( ! in_array( $status_name, $avail_post_stati, true ) || empty( $num_posts->$status_name ) ) {
				continue;
			}

			if ( isset( $_REQUEST['post_status'] ) && $status_name === $_REQUEST['post_status'] ) { // phpcs:ignore WordPress.Security.NonceVerification
				$class = 'current';
			}

			$status_args = [
				'post_status' => $status_name,
				'post_type'   => $link_post_type,
				'page'        => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK,
			];

			$status_label = sprintf(
				translate_nooped_plural( $status->label_count, $num_posts->$status_name ),
				number_format_i18n( $num_posts->$status_name )
			);

			$status_links[ $status_name ] = [
				'url'     => esc_url( add_query_arg( $status_args, 'edit.php' ) ),
				'label'   => $status_label,
				'current' => isset( $_REQUEST['post_status'] ) && $status_name === $_REQUEST['post_status'], // phpcs:ignore WordPress.Security.NonceVerification
			];
		}

		if ( ! empty( $this->sticky_posts_count ) ) {
			$class = ! empty( $_REQUEST['show_sticky'] ) ? 'current' : ''; // phpcs:ignore WordPress.Security.NonceVerification

			$sticky_args = [
				'post_type'   => $link_post_type,
				'page'        => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK,
				'show_sticky' => 1,
			];

			$sticky_inner_html = sprintf(
				/* translators: %s: Number of posts. */
				_nx(
					'Sticky <span class="count">(%s)</span>',
					'Sticky <span class="count">(%s)</span>',
					$this->sticky_posts_count,
					'posts'
				),
				number_format_i18n( $this->sticky_posts_count )
			);

			$sticky_link = [
				'sticky' => [
					'url'     => esc_url( add_query_arg( $sticky_args, 'edit.php' ) ),
					'label'   => $sticky_inner_html,
					'current' => ! empty( $_REQUEST['show_sticky'] ), // phpcs:ignore WordPress.Security.NonceVerification
				],
			];

			// Sticky comes after Publish, or if not listed, after All.
			$split        = 1 + array_search( ( isset( $status_links['publish'] ) ? 'publish' : 'all' ), array_keys( $status_links ), true );
			$status_links = array_merge( array_slice( $status_links, 0, $split ), $sticky_link, array_slice( $status_links, $split ) );
		}

		$views = parent::get_views_links( $status_links );

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
	 * Wrapper to access private property.
	 */
	public function return_user_posts_count() {
		return $this->user_posts_count;
	}

	/**
	 * Wrapper to access private property.
	 */
	public function return_sticky_posts_count() {
		return $this->sticky_posts_count;
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
				$total_items = $this->return_sticky_posts_count();
			} elseif ( isset( $_GET['author'] ) && get_current_user_id() === (int) $_GET['author'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$total_items = $this->return_user_posts_count();
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

		$this->set_pagination_args(
			[
				'total_items' => $total_items,
				'total_pages' => $total_pages,
				'per_page'    => $per_page,
			]
		);
	}

}
