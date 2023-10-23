<?php //phpcs:ignore

/**
 * The class manage the post table for our accordion
 *
 * @package YITH\CategoryAccordion
 */

/**
 * The class that manage the list table.
 */
class YITH_Category_Accordion_Table extends YITH_Post_Type_Admin {

	/**
	 * Instance of the class
	 *
	 * @var YITH_Category_Accordion_Table
	 */
	private static $instance;
	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'yith_cacc';

	/**
	 * Add the right columns for the post type
	 *
	 * @param array $columns columns.
	 * @return array
	 */

	protected function  __construct() {
		parent::__construct();

		add_action( 'admin_action_yith_ywcca_duplicate_accordion_style', array( $this, 'duplicate_accordion_style' ) );
		add_filter( 'views_edit-' . YITH_Category_Accordion_Post_Types::$post_type,  '__return_empty_array' );

	}

	public function define_columns( $columns ) {
		if ( isset( $columns['date'] ) ) {
			unset( $columns['date'] );
		}

		if ( isset( $columns['title'] ) ) {
			$columns['title'] = __( 'Name', 'yith-woocommerce-category-accordion' );
		}

		$custom_columns = array(

			'shortcode' => __( 'Shortcode', 'yith-woocommerce-category-accordion' ),
			'actions'   => __( 'Actions', 'yith-woocommerce-category-accordion' ),
		);

		return array_merge( $columns, $custom_columns );
	}

	/**
	 * Print the content columns
	 *
	 * @param mixed $column column.
	 * @param mixed $post_id post id.
	 */
	public function render_columns( $column, $post_id ) {

		$duplicate_link  = add_query_arg(
			array(
				'action'            => 'yith_ywcca_duplicate_accordion_style',
				'accordion_style_id' => $post_id,
				'duplicate_nonce'   => wp_create_nonce( 'yith_ywcca_duplicate_accordion_style' ),
				),
			admin_url()
		);

		switch ( $column ) {
			case 'shortcode':
				$shortcode = '[yith_wcca_category_accordion acc_style=' . esc_html( $post_id ) . ' how_show=wc]';
				echo yith_plugin_fw_get_field(
					array(
						'type' => 'copy-to-clipboard',
						'value' => $shortcode,
						'id' => 'ywcacc_shortcode_copy',
						)
				); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case 'actions':
				$actions = yith_plugin_fw_get_default_post_actions( $post_id );
				if ( isset( $actions['trash'] ) ) {
					unset( $actions['trash'] );
				}
				$actions['delete']                 = array(
					'type'   => 'action-button',
					'title'  => _x( 'Delete Permanently', 'Post action', 'yith-plugin-fw' ),
					'action' => 'delete',
					'icon'   => 'trash',
					'url'    => get_delete_post_link( $post_id, '', true ),
					);
				$actions['delete']['confirm_data'] = array(
					'title'               => __( 'Confirm delete', 'yith-plugin-fw' ),
					/* translators: %s is the post id */
					'message'             => sprintf( __( 'Are you sure you want to delete "%s"?', 'yith-plugin-fw' ), '<strong>' . _draft_or_post_title( $post_id ) . '</strong>' ) . '<br /><br />' . __( 'This action cannot be undone and you will not be able to recover this data.', 'yith-plugin-fw' ),
					'cancel-button'       => __( 'No', 'yith-plugin-fw' ),
					'confirm-button'      => _x( 'Yes, delete', 'Delete confirmation action', 'yith-plugin-fw' ),
					'confirm-button-type' => 'delete',
					);
				$actions['clone'] = array(
					'type'   => 'action-button',
					'action' => 'duplicate',
					'title'  => esc_html__( 'Duplicate', 'yith-woocommerce-category-accordion' ),
					'icon'   => 'clone',
					'url'    => $duplicate_link,
				);
				yith_plugin_fw_get_action_buttons( $actions, true );
				break;
		}
	}

	/**
	 * Use the post id
	 *
	 * @return bool
	 * @since 2.0.0
	 * @author YITH
	 */
	public function use_object() {
		return false;
	}

	/**
	 * Define_bulk_actions
	 *
	 * @param array $actions actions.
	 * @return array
	 */
	public function define_bulk_actions( $actions ) {
		$new_actions = array(
			'delete' => __( 'Delete', 'yith-woocommerce-category-accordion' ),
		);
		return $new_actions;
	}

	/**
	 * Get_back_to_wp_list_text
	 *
	 * @return string
	 */
	public function get_back_to_wp_list_text() {
		return __( 'Back to accordion styles list', 'yith-woocommerce-category-accordion' );
	}

	/**
	 * Render blank state.
	 *
	 * @author YITH
	 * @since 2.0.0
	 */
	protected function render_blank_state() {
		parent::render_blank_state();
		echo '<style>.page-title-action{display: none!important;}</style>';
	}

	/**
	 * Retrieve an array of parameters for blank state.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	protected function get_blank_state_params() {
		$submessage = '<br/>' . esc_html__( 'Create the first one now', 'yith-woocommerce-category-accordion' );

		$new_post_url = admin_url( 'post-new.php' );
		$args         = array(
			'post_type' => $this->post_type,
		);
		$new_post_url = esc_url( add_query_arg( $args, $new_post_url ) );

		return array(
			'icon_url' => esc_url( YWCCA_ASSETS_URL ) . 'images/new-icon-accordion.png',
			'message'  => __( 'You have no accordions created yet', 'yith-woocommerce-category-accordion' ) . $submessage,
			'cta'      => array(
				'title' => __( 'Add accordion', 'yith-woocommerce-category-accordion' ),
				'class' => 'ywcwat_add_new_accordion',
				'icon'  => 'plus',
				'url'   => $new_post_url,
			),
			'class'    => 'yith_cacc_div_new_accordion',
		);
	}

	/**
	 * Duplicate Accordion Style
	 */
	public function duplicate_accordion_style() {

		if ( isset( $_REQUEST['action'], $_GET['duplicate_nonce'], $_GET['accordion_style_id'] ) && 'yith_ywcca_duplicate_accordion_style' === $_REQUEST['action'] && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['duplicate_nonce'] ) ), 'yith_ywcca_duplicate_accordion_style' ) ) {

			$post_id = absint( wp_unslash( $_GET['accordion_style_id'] ) );
			$post    = get_post( $post_id );

			if ( ! $post || YITH_Category_Accordion_Post_Types::$post_type !== $post->post_type ) {
				return;
			}
			$new_title = $post->post_title . esc_html_x( ' - Copy', 'Name of duplicated rule', 'yith-woocommerce-category-accordion' );
			$new_post  = array(
				'post_status' => 'publish',
				'post_type'   => YITH_Category_Accordion_Post_Types::$post_type,
				'post_title'  => $new_title,
			);

			$new_post_id = wp_insert_post( $new_post );
			$metas       = get_post_meta( $post_id );

			if ( ! empty( $metas ) ) {
				foreach ( $metas as $meta_key => $meta_value ) {
					if ( '_edit_lock' === $meta_key || '_edit_last' === $meta_key ) {
						continue;
					}

					update_post_meta( $new_post_id, $meta_key, maybe_unserialize( $meta_value[0] ) );
				}
			}


			$redirect_url = apply_filters(
				'yith_ywcca_duplicate_accordion_style_redirect_url',
				add_query_arg(
					array(
						'post_type'   => YITH_Category_Accordion_Post_Types::$post_type,
					),
					admin_url( 'edit.php' )
				)
			);

			wp_safe_redirect( $redirect_url );
			exit;
		}
	}


}

return YITH_Category_Accordion_Table::instance();
