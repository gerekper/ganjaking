<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YWCTM_Alternative_Text_Table' ) ) {

	/**
	 * Displays the custom url table in YWCTM plugin admin tab
	 *
	 * @class   YWCTM_Alternative_Text_Table
	 * @package Yithemes
	 * @since   1.3.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWCTM_Alternative_Text_Table {

		/**
		 * Single instance of the class
		 *
		 * @var \YWCTM_Alternative_Text_Table
		 * @since 1.3.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWCTM_Alternative_Text_Table
		 * @since 1.3.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self();

			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since   1.3.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			add_filter( 'set-screen-option', array( $this, 'set_options' ), 10, 3 );
			add_action( 'current_screen', array( $this, 'add_options' ) );
			add_action( 'wp_ajax_ywctm_json_search_product_categories', array( $this, 'json_search_product_categories' ), 10 );
			add_action( 'wp_ajax_ywctm_json_search_product_tags', array( $this, 'json_search_product_tags' ), 10 );

		}

		/**
		 * Outputs the custom url table template with insert form in plugin options panel
		 *
		 * @since   1.3.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function output() {

			global $wpdb;

			$current_section = isset( $_GET['section'] ) ? $_GET['section'] : 'products';
			$column_name     = __( 'Custom text', 'yith-woocommerce-catalog-mode' );

			$sections   = array(
				'products'   => array(
					'section' => __( 'Products', 'yith-woocommerce-catalog-mode' ),
					'args'    => array(
						'singular' => __( 'product', 'yith-woocommerce-catalog-mode' ),
						'plural'   => __( 'products', 'yith-woocommerce-catalog-mode' ),
						'id'       => 'product'
					),
					'options' => array(
						'select_table'     => $wpdb->prefix . 'posts a INNER JOIN ' . $wpdb->prefix . 'postmeta b ON a.ID = b.post_id',
						'select_columns'   => array(
							'a.ID',
							'a.post_title',
							'MAX( CASE WHEN b.meta_key = "_ywctm_alternative_text' . $this->get_vendor_id() . '" THEN b.meta_value ELSE NULL END ) AS alternative_text',
						),
						'select_where'     => 'a.post_type = "product" AND ( b.meta_key = "_ywctm_alternative_text' . $this->get_vendor_id() . '")',
						'select_group'     => 'a.ID',
						'select_order'     => 'a.post_title',
						'select_order_dir' => 'ASC',
						'search_where'     => array(
							'a.post_title'
						),
						'per_page_option'  => 'products_per_page',
						'count_table'      => '( SELECT a.ID, a.post_title FROM ' . $wpdb->prefix . 'posts a INNER JOIN ' . $wpdb->prefix . 'postmeta b ON a.ID = b.post_id  WHERE a.post_type = "product" AND ( b.meta_key = "_ywctm_alternative_text' . $this->get_vendor_id() . '" ) GROUP BY a.ID ) AS a',
						'count_where'      => '',
						'key_column'       => 'ID',
						'view_columns'     => array(
							'cb'               => '<input type="checkbox" />',
							'product'          => __( 'Product', 'yith-woocommerce-catalog-mode' ),
							'alternative_text' => $column_name,
						),
						'hidden_columns'   => array(),
						'sortable_columns' => array(
							'product' => array( 'post_title', true )
						),
						'custom_columns'   => array(
							'column_product'          => function ( $item, $row ) {
								return $this->row_action_links( $item, $row );
							},
							'column_alternative_text' => function ( $item ) {
								$product = wc_get_product( $item['ID'] );

								return $product->get_meta( '_ywctm_alternative_text' . $this->get_vendor_id() );
							},
						),
						'bulk_actions'     => array(
							'actions'   => array(
								'delete' => __( 'Remove from list', 'yith-woocommerce-catalog-mode' )
							),
							'functions' => array(
								'function_delete' => function () {
									$this->bulk_delete( 'product' );
								}
							)
						),
					),
					'action'  => 'woocommerce_json_search_products'
				),
				'categories' => array(
					'section' => __( 'Categories', 'yith-woocommerce-catalog-mode' ),
					'args'    => array(
						'singular' => __( 'category', 'yith-woocommerce-catalog-mode' ),
						'plural'   => __( 'categories', 'yith-woocommerce-catalog-mode' ),
						'id'       => 'category'
					),
					'options' => array(
						'select_table'     => $wpdb->prefix . 'terms a INNER JOIN ' . $wpdb->prefix . 'term_taxonomy b ON a.term_id = b.term_id INNER JOIN ' . $wpdb->prefix . 'termmeta c ON c.' . 'term_id = a.term_id',
						'select_columns'   => array(
							'a.term_id AS ID',
							'a.name',
							'MAX( CASE WHEN c.meta_key = "_ywctm_alternative_text' . $this->get_vendor_id() . '" THEN c.meta_value ELSE NULL END ) AS alternative_text',
						),
						'select_where'     => 'b.taxonomy = "product_cat" AND ( c.meta_key = "_ywctm_alternative_text' . $this->get_vendor_id() . '" )',
						'select_group'     => 'a.term_id',
						'select_order'     => 'a.name',
						'select_order_dir' => 'ASC',
						'per_page_option'  => 'categories_per_page',
						'search_where'     => array(
							'a.name'
						),
						'count_table'      => '( SELECT a.* FROM ' . $wpdb->prefix . 'terms a INNER JOIN ' . $wpdb->prefix . 'term_taxonomy b ON a.term_id = b.term_id INNER JOIN ' . $wpdb->prefix . 'termmeta c ON c.' . 'term_id = a.term_id WHERE b.taxonomy = "product_cat" AND ( c.meta_key = "_ywctm_alternative_text' . $this->get_vendor_id() . '" ) GROUP BY a.term_id ) AS a',
						'count_where'      => '',
						'key_column'       => 'ID',
						'view_columns'     => array(
							'cb'               => '<input type="checkbox" />',
							'category'         => __( 'Category', 'yith-woocommerce-catalog-mode' ),
							'alternative_text' => $column_name,
						),
						'hidden_columns'   => array(),
						'sortable_columns' => array(
							'category' => array( 'name', true )
						),
						'custom_columns'   => array(
							'column_category'         => function ( $item, $row ) {
								return $this->row_action_links( $item, $row, 'cat' );
							},
							'column_alternative_text' => function ( $item ) {
								return get_term_meta( $item['ID'], '_ywctm_alternative_text' . $this->get_vendor_id(), true );
							},
						),
						'bulk_actions'     => array(
							'actions'   => array(
								'delete' => __( 'Remove from list', 'yith-woocommerce-catalog-mode' )
							),
							'functions' => array(
								'function_delete' => function () {
									$this->bulk_delete();
								}
							)
						),
					),
					'action'  => 'ywctm_json_search_product_categories'
				),
				'tags'       => array(
					'section' => __( 'Tags', 'yith-woocommerce-catalog-mode' ),
					'args'    => array(
						'singular' => __( 'tag', 'yith-woocommerce-catalog-mode' ),
						'plural'   => __( 'tags', 'yith-woocommerce-catalog-mode' ),
						'id'       => 'tag'
					),
					'options' => array(
						'select_table'     => $wpdb->prefix . 'terms a INNER JOIN ' . $wpdb->prefix . 'term_taxonomy b ON a.term_id = b.term_id INNER JOIN ' . $wpdb->prefix . 'termmeta c ON c.' . 'term_id = a.term_id',
						'select_columns'   => array(
							'a.term_id AS ID',
							'a.name',
							'MAX( CASE WHEN c.meta_key = "_ywctm_alternative_text' . $this->get_vendor_id() . '" THEN c.meta_value ELSE NULL END ) AS alternative_text',
						),
						'select_where'     => 'b.taxonomy = "product_tag" AND ( c.meta_key = "_ywctm_alternative_text' . $this->get_vendor_id() . '" ) ',
						'select_group'     => 'a.term_id',
						'select_order'     => 'a.name',
						'select_order_dir' => 'ASC',
						'per_page_option'  => 'tags_per_page',
						'search_where'     => array(
							'a.name'
						),
						'count_table'      => '( SELECT a.* FROM ' . $wpdb->prefix . 'terms a INNER JOIN ' . $wpdb->prefix . 'term_taxonomy b ON a.term_id = b.term_id INNER JOIN ' . $wpdb->prefix . 'termmeta c ON c.' . 'term_id = a.term_id WHERE b.taxonomy = "product_tag" AND ( c.meta_key = "_ywctm_alternative_text' . $this->get_vendor_id() . '" OR c.meta_key = "_ywctm_exclude_button' . $this->get_vendor_id() . '" ) AND c.meta_value = "yes" GROUP BY a.term_id ) AS a',
						'count_where'      => '',
						'key_column'       => 'ID',
						'view_columns'     => array(
							'cb'               => '<input type="checkbox" />',
							'tag'              => __( 'Tag', 'yith-woocommerce-catalog-mode' ),
							'alternative_text' => $column_name,
						),
						'hidden_columns'   => array(),
						'sortable_columns' => array(
							'tag' => array( 'name', true )
						),
						'custom_columns'   => array(
							'column_tag'              => function ( $item, $row ) {
								return $this->row_action_links( $item, $row, 'tag' );
							},
							'column_alternative_text' => function ( $item ) {
								return get_term_meta( $item['ID'], '_ywctm_alternative_text' . $this->get_vendor_id(), true );
							},
						),
						'bulk_actions'     => array(
							'actions'   => array(
								'delete' => __( 'Remove from list', 'yith-woocommerce-catalog-mode' )
							),
							'functions' => array(
								'function_delete' => function () {
									$this->bulk_delete();
								}
							)
						),
					),
					'action'  => 'ywctm_json_search_product_tags'
				),
			);
			$array_keys = array_keys( $sections );

			$table = new YITH_Custom_Table( $sections[ $current_section ]['args'] );

			$table->options = $sections[ $current_section ]['options'];

			$message = '';
			$notice  = '';

			$list_query_args = array(
				'page'    => $_GET['page'],
				'tab'     => $_GET['tab'],
				'section' => $current_section
			);

			$list_url = esc_url( add_query_arg( $list_query_args, admin_url( 'admin.php' ) ) );

			if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], basename( __FILE__ ) ) ) {

				$item_valid = $this->validate_fields( $_POST, $current_section );

				if ( $item_valid !== true ) {

					$notice = $item_valid;

				} else {

					$alternative_text = isset( $_POST['_ywctm_alternative_text'] ) ? $_POST['_ywctm_alternative_text'] : '';

					switch ( $current_section ) {

						case 'categories':

							$category_ids = ( ! is_array( $_POST['category_ids'] ) ) ? explode( ',', $_POST['category_ids'] ) : $_POST['category_ids'];
							$count        = count( $category_ids );

							if ( ! empty( $category_ids ) ) {

								foreach ( $category_ids as $category_id ) {

									if ( $alternative_text ) {
										update_term_meta( $category_id, '_ywctm_alternative_text' . $this->get_vendor_id(), $alternative_text );
									} else {
										delete_term_meta( $category_id, '_ywctm_alternative_text' . $this->get_vendor_id() );
									}

								}

							}

							break;

						case 'tags':

							$tag_ids = ( ! is_array( $_POST['tag_ids'] ) ) ? explode( ',', $_POST['tag_ids'] ) : $_POST['tag_ids'];
							$count   = count( $tag_ids );

							if ( ! empty( $tag_ids ) ) {

								foreach ( $tag_ids as $tag_id ) {

									if ( $alternative_text ) {
										update_term_meta( $tag_id, '_ywctm_alternative_text' . $this->get_vendor_id(), $alternative_text );
									} else {
										delete_term_meta( $tag_id, '_ywctm_alternative_text' . $this->get_vendor_id() );
									}

								}

							}

							break;

						default:

							$product_ids = ( ! is_array( $_POST['product_ids'] ) ) ? explode( ',', $_POST['product_ids'] ) : $_POST['product_ids'];
							$count       = count( $product_ids );

							if ( ! empty( $product_ids ) ) {

								foreach ( $product_ids as $product_id ) {

									$product = wc_get_product( $product_id );

									if ( $alternative_text ) {
										$product->update_meta_data( '_ywctm_alternative_text' . $this->get_vendor_id(), $alternative_text );
									} else {
										$product->delete_meta_data( '_ywctm_alternative_text' . $this->get_vendor_id() );

									}

									$product->save();
								}

							}

					}

					if ( ! empty( $_POST['insert'] ) ) {

						$singular = sprintf( __( '1 %s added successfully', 'yith-woocommerce-catalog-mode' ), ucfirst( $sections[ $current_section ]['args']['singular'] ) );
						$plural   = sprintf( __( '%s %s added successfully', 'yith-woocommerce-catalog-mode' ), $count, ucfirst( $sections[ $current_section ]['args']['plural'] ) );
						$message  = $count > 1 ? $plural : $singular;

					} elseif ( ! empty( $_POST['edit'] ) ) {

						$message = sprintf( __( '%s updated successfully', 'yith-woocommerce-catalog-mode' ), ucfirst( $sections[ $current_section ]['args']['singular'] ) );

					}

				}

			}

			$table->prepare_items();

			$data_selected = '';
			$value         = '';
			$item          = array(
				'ID'               => 0,
				'alternative_text' => '',
			);

			if ( isset( $_GET['id'] ) && ! empty( $_GET['action'] ) && ( 'edit' == $_GET['action'] ) ) {

				switch ( $current_section ) {

					case'categories':

						$item = array(
							'ID'               => $_GET['id'],
							'alternative_text' => get_term_meta( $_GET['id'], '_ywctm_alternative_text' . $this->get_vendor_id(), true ),

						);

						$category      = get_term( $_GET['id'], 'product_cat' );
						$data_selected = wp_kses_post( $category->name );
						break;

					case 'tags':

						$item = array(
							'ID'               => $_GET['id'],
							'alternative_text' => get_term_meta( $_GET['id'], '_ywctm_alternative_text' . $this->get_vendor_id(), true ),

						);

						$tag           = get_term( $_GET['id'], 'product_tag' );
						$data_selected = wp_kses_post( $tag->name );
						break;

					default:
						$product       = wc_get_product( $_GET['id'] );
						$item          = array(
							'ID'               => $_GET['id'],
							'alternative_text' => $product->get_meta( '_ywctm_alternative_text' . $this->get_vendor_id() ),
						);
						$data_selected = wp_kses_post( $product->get_formatted_name() );
				}

				$value         = $_GET['id'];
				$data_selected = array( $value => $data_selected );

			}

			if ( 'delete' === $table->current_action() ) {
				$deleted = is_array( $_GET['id'] ) ? $_GET['id'] : explode( ',', $_GET['id'] );

				$singular = sprintf( __( '1 %s removed successfully', 'yith-woocommerce-catalog-mode' ), ucfirst( $sections[ $current_section ]['args']['singular'] ) );
				$plural   = sprintf( __( '%s %s removed successfully', 'yith-woocommerce-catalog-mode' ), count( $deleted ), ucfirst( $sections[ $current_section ]['args']['plural'] ) );
				$message  = count( $deleted ) > 1 ? $plural : $singular;

			}

			?>
            <ul class="subsubsub">
				<?php foreach ( $sections as $id => $section ) : ?>
                    <li>
						<?php
						$query_args  = array( 'page' => $_GET['page'], 'tab' => $_GET['tab'], 'section' => $id );
						$section_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
						?>

                        <a href="<?php echo $section_url; ?>" class="<?php echo( $current_section == $id ? 'current' : '' ); ?>">
							<?php echo $section['section']; ?>
                        </a>
						<?php echo( end( $array_keys ) == $id ? '' : '|' ); ?>
                    </li>
				<?php endforeach; ?>
            </ul>
            <br class="clear" />
            <div class="wrap">
                <div class="icon32 icon32-posts-post" id="icon-edit"><br /></div>
                <h1><?php _e( 'Texts replacing price (Hidden price mode)', 'yith-woocommerce-catalog-mode' ); ?>

					<?php if ( empty( $_GET['action'] ) || ( 'insert' !== $_GET['action'] && 'edit' !== $_GET['action'] ) ) : ?>
						<?php
						$query_args   = array( 'page' => $_GET['page'], 'tab' => $_GET['tab'], 'section' => $current_section, 'action' => 'insert' );
						$add_form_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
						?>
                        <a class="page-title-action" href="<?php echo $add_form_url; ?>"><?php echo sprintf( __( 'Add %s', 'yith-woocommerce-catalog-mode' ), $sections[ $current_section ]['section'] ) ?></a>
					<?php endif; ?>
                </h1>

				<?php if ( ! empty( $notice ) ) : ?>
                    <div id="notice" class="error below-h2"><p><?php echo $notice; ?></p></div>
				<?php endif; ?>

				<?php if ( ! empty( $message ) ) : ?>
                    <div id="message" class="updated below-h2"><p><?php echo $message; ?></p></div>
				<?php endif; ?>

				<?php if ( ! empty( $_GET['action'] ) && ( 'insert' == $_GET['action'] || 'edit' == $_GET['action'] ) ) : ?>

                    <form id="form" method="POST" action="<?php echo $list_url; ?>">
                        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />
                        <table class="form-table">
                            <tbody>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="<?php echo $sections[ $current_section ]['args']['id']; ?>_ids">
										<?php echo ( 'edit' == $_GET['action'] ) ? sprintf( __( '%s to edit', 'yith-woocommerce-catalog-mode' ), ucfirst( $sections[ $current_section ]['args']['singular'] ) ) : sprintf( __( 'Select %s', 'yith-woocommerce-catalog-mode' ), ucfirst( $sections[ $current_section ]['args']['plural'] ) ); ?>
                                    </label>
                                </th>
                                <td class="forminp">

									<?php if ( 'edit' == $_GET['action'] ) : ?>
                                        <input id="<?php echo $sections[ $current_section ]['args']['id']; ?>_id" name="<?php echo $sections[ $current_section ]['args']['id']; ?>_ids" type="hidden" value="<?php echo esc_attr( $item['ID'] ); ?>" />
									<?php endif; ?>

									<?php

									$select_args = array(
										'class'            => 'wc-product-search',
										'id'               => $sections[ $current_section ]['args']['id'] . '_ids',
										'name'             => $sections[ $current_section ]['args']['id'] . '_ids',
										'data-placeholder' => sprintf( __( 'Search for a %s&hellip;', 'yith-woocommerce-catalog-mode' ), $sections[ $current_section ]['args']['singular'] ),
										'data-allow_clear' => false,
										'data-selected'    => $data_selected,
										'data-multiple'    => ( 'edit' == $_GET['action'] ) ? false : true,
										'data-action'      => $sections[ $current_section ]['action'],
										'value'            => $value,
										'style'            => 'width: 50%'
									);

									if ( 'edit' == $_GET['action'] ) {
										$select_args['custom-attributes'] = array( 'disabled' => 'disabled' );
									}

									yit_add_select2_fields( $select_args )

									?>

                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="_ywctm_alternative_text"><?php _e( 'Custom text', 'yith-woocommerce-catalog-mode' ); ?></label>
                                </th>
                                <td class="forminp forminp-text">

                                    <input id="_ywctm_alternative_text" name="_ywctm_alternative_text" type="text" value="<?php echo $item['alternative_text']; ?>" />

                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <input id="<?php echo $_GET['action'] ?>" name="<?php echo $_GET['action'] ?>" type="submit" class="button-primary"
                               value="<?php echo( ( 'insert' == $_GET['action'] ) ? __( 'Add text', 'yith-woocommerce-catalog-mode' ) : __( 'Update text', 'yith-woocommerce-catalog-mode' ) ); ?>"
                        />
                        <a class="button-secondary" href="<?php echo $list_url; ?>"><?php _e( 'Return to list', 'yith-woocommerce-catalog-mode' ); ?></a>
                    </form>

				<?php else : ?>
                    <p>
                        <i>
							<?php _e( 'If you activate the "Hide price" option, the items belonging to the following list will use a custom text instead of the price.', 'yith-woocommerce-catalog-mode' ); ?>
                        </i>
                    </p>
                    <form id="custom-table" method="GET" action="<?php echo $list_url; ?>">
						<?php $table->search_box( sprintf( __( 'Search %s' ), $sections[ $current_section ]['args']['singular'] ), $sections[ $current_section ]['args']['singular'] ); ?>
                        <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
                        <input type="hidden" name="tab" value="<?php echo $_GET['tab']; ?>" />
                        <input type="hidden" name="section" value="<?php echo $current_section; ?>" />
						<?php $table->display(); ?>
                    </form>

				<?php endif; ?>

            </div>
			<?php

		}

		/**
		 * Get current vendor ID
		 *
		 * @since   1.3.0
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_vendor_id() {

			$vendor_id = '';

			if ( YITH_WCTM()->is_multivendor_active() ) {

				$vendor    = yith_get_vendor( 'current', 'user' );
				$vendor_id = ( $vendor->id > 0 ) ? '_' . $vendor->id : '';

			}

			return $vendor_id;

		}

		/**
		 * Validate input fields
		 *
		 * @since   1.3.0
		 *
		 * @param   $item array POST data array
		 * @param   $current_section
		 *
		 * @return  bool|string
		 * @author  Alberto Ruggiero
		 */
		public function validate_fields( $item, $current_section ) {

			$messages = array();

			if ( ! empty( $item['insert'] ) ) {

				switch ( $current_section ) {

					case 'categories':

						if ( empty( $item['category_ids'] ) ) {
							$messages[] = __( 'Select at least one category', 'yith-woocommerce-catalog-mode' );
						}

						break;

					case 'tags':

						if ( empty( $item['tag_ids'] ) ) {
							$messages[] = __( 'Select at least one tag', 'yith-woocommerce-catalog-mode' );
						}

						break;

					default:

						if ( empty( $item['product_ids'] ) ) {
							$messages[] = __( 'Select at least one product', 'yith-woocommerce-catalog-mode' );
						}

				}

			}

			if ( empty( $messages ) ) {
				return true;
			}

			return implode( '<br />', $messages );
		}

		/**
		 * Add screen options for exclusions list table template
		 *
		 * @since   1.3.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_options() {

			$sections = array(
				'products'   => __( 'Products', 'yith-woocommerce-catalog-mode' ),
				'categories' => __( 'Categories', 'yith-woocommerce-catalog-mode' ),
				'tags'       => __( 'Tags', 'yith-woocommerce-catalog-mode' ),
			);

			$current_section = isset( $_GET['section'] ) ? $_GET['section'] : 'products';

			if ( ( 'yith-plugins_page_yith_wc_catalog_mode_panel' == get_current_screen()->id || 'toplevel_page_yith_vendor_ctm_settings' == get_current_screen()->id ) && ( isset( $_GET['tab'] ) && $_GET['tab'] == 'custom-url' ) && ( ! isset( $_GET['action'] ) || ( $_GET['action'] != 'edit' && $_GET['action'] != 'insert' ) ) ) {

				$option = 'per_page';

				$args = array(
					'label'   => $sections[ $current_section ],
					'default' => 10,
					'option'  => $current_section . '_per_page'
				);

				add_screen_option( $option, $args );

			}

		}

		/**
		 * Handles bulk deletion
		 *
		 * @since   1.6.1
		 *
		 * @param   $type
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function bulk_delete( $type = 'term' ) {

			$ids = isset( $_GET['id'] ) ? $_GET['id'] : array();
			$ids = is_array( $ids ) ? $ids : array( $ids );

			if ( ! empty( $ids ) ) {

				foreach ( $ids as $id ) {

					if ( $type == 'product' ) {
						$product = wc_get_product( $id );
						$product->delete_meta_data( '_ywctm_alternative_text' . $this->get_vendor_id() );
						$product->save();
					} else {
						delete_term_meta( $id, '_ywctm_alternative_text' . $this->get_vendor_id() );
					}

				}

			}

		}

		/**
		 * Sets row actions
		 *
		 * @since   1.6.1
		 *
		 * @param   $item
		 * @param   $row
		 * @param   $type
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function row_action_links( $item, $row, $type = 'product' ) {

			$edit_query_args = array(
				'page'    => $_GET['page'],
				'tab'     => $_GET['tab'],
				'section' => ( isset( $_GET['section'] ) ? $_GET['section'] : 'products' ),
				'action'  => 'edit',
				'id'      => $item['ID']
			);
			$edit_url        = esc_url( add_query_arg( $edit_query_args, admin_url( 'admin.php' ) ) );

			$delete_query_args = array(
				'page'    => $_GET['page'],
				'tab'     => $_GET['tab'],
				'section' => ( isset( $_GET['section'] ) ? $_GET['section'] : 'products' ),
				'action'  => 'delete',
				'id'      => $item['ID']
			);
			$delete_url        = esc_url( add_query_arg( $delete_query_args, admin_url( 'admin.php' ) ) );

			if ( $type != 'product' ) {
				$link_query_args = array(
					'taxonomy'  => 'product_' . $type,
					'post_type' => 'product',
					'tag_ID'    => $item['ID'],
					'action'    => 'edit'
				);
				$link_url        = esc_url( add_query_arg( $link_query_args, admin_url( 'edit-tags.php' ) ) );
				$label           = $type == 'cat' ? __( 'Edit category', 'yith-woocommerce-catalog-mode' ) : __( 'Edit tag', 'yith-woocommerce-catalog-mode' );
				$title           = $item['name'];
			} else {
				$link_query_args = array(
					'post'   => $item['ID'],
					'action' => 'edit'
				);
				$link_url        = esc_url( add_query_arg( $link_query_args, admin_url( 'post.php' ) ) );
				$label           = __( 'Edit product', 'yith-woocommerce-catalog-mode' );
				$title           = $item['post_title'];
			}

			$actions = array(
				'edit'        => '<a href="' . $edit_url . '">' . __( 'Edit text', 'yith-woocommerce-catalog-mode' ) . '</a>',
				'direct_edit' => '<a href="' . $link_url . '" target="_blank">' . $label . '</a>',
				'delete'      => '<a href="' . $delete_url . '">' . __( 'Remove from list', 'yith-woocommerce-catalog-mode' ) . '</a>',
			);

			return sprintf( '<strong><a class="tips" href="%s" data-tip="%s">#%d %s </a></strong> %s', $edit_url, __( 'Edit text', 'yith-woocommerce-catalog-mode' ), $item['ID'], $title, $row->row_actions( $actions ) );

		}

		/**
		 * Set screen options for exclusions list table template
		 *
		 * @since   1.3.0
		 *
		 * @param   $status
		 * @param   $option
		 * @param   $value
		 *
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function set_options( $status, $option, $value ) {

			$current_section = isset( $_GET['section'] ) ? $_GET['section'] : 'products';

			return ( $current_section . '_per_page' == $option ) ? $value : $status;

		}

		/**
		 * Get category name
		 *
		 * @since   1.3.0
		 *
		 * @param   $x
		 * @param   $taxonomy_types
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function json_search_product_categories( $x = '', $taxonomy_types = array( 'product_cat' ) ) {

			global $wpdb;

			$term = (string) urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );
			$term = '%' . $term . '%';

			$query_cat = $wpdb->prepare( "SELECT {$wpdb->terms}.term_id,{$wpdb->terms}.name, {$wpdb->terms}.slug
                                   FROM {$wpdb->terms} INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
                                   WHERE {$wpdb->term_taxonomy}.taxonomy IN (%s) AND {$wpdb->terms}.slug LIKE %s", implode( ',', $taxonomy_types ), $term );

			$product_categories = $wpdb->get_results( $query_cat );

			$to_json = array();

			foreach ( $product_categories as $product_category ) {

				$to_json[ $product_category->term_id ] = sprintf( '#%s &ndash; %s', $product_category->term_id, $product_category->name );

			}

			wp_send_json( $to_json );

		}

		/**
		 * Get tag name
		 *
		 * @since   1.3.0
		 *
		 * @param   $x
		 * @param   $taxonomy_types
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function json_search_product_tags( $x = '', $taxonomy_types = array( 'product_tag' ) ) {

			global $wpdb;

			$term = (string) urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );
			$term = '%' . $term . '%';

			$query_cat = $wpdb->prepare( "SELECT {$wpdb->terms}.term_id,{$wpdb->terms}.name, {$wpdb->terms}.slug
                                   FROM {$wpdb->terms} INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
                                   WHERE {$wpdb->term_taxonomy}.taxonomy IN (%s) AND {$wpdb->terms}.slug LIKE %s", implode( ',', $taxonomy_types ), $term );

			$product_tags = $wpdb->get_results( $query_cat );

			$to_json = array();

			foreach ( $product_tags as $product_tag ) {

				$to_json[ $product_tag->term_id ] = sprintf( '#%s &ndash; %s', $product_tag->term_id, $product_tag->name );

			}

			wp_send_json( $to_json );

		}

	}

	/**
	 * Unique access to instance of YWCTM_Alternative_Text_Table class
	 *
	 * @return \YWCTM_Alternative_Text_Table
	 */
	function YWCTM_Alternative_Text_Table() {

		return YWCTM_Alternative_Text_Table::get_instance();

	}

	new YWCTM_Alternative_Text_Table();
}