<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Custom ThankYou Page for Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Implements the YITH_YCTPW_Saved_Ctpw_Table class.
 *
 * @class   YITH_YCTPW_Saved_Ctpw_Table
 * @package YITH Custom ThankYou Page for Woocommerce
 * @since   1.2.5
 * @author  YITH
 * @extends WP_List_Table
 */
class YITH_YCTPW_Saved_Ctpw_Table extends WP_List_Table {

	/**
	 * @var array $options array of options for table showing
	 */
	public $options;

	/**
	 * Class constructor method.
	 *
	 * @since 1.2.5
	 */
	public function __construct() {
		// Set parent defaults.
		parent::__construct(
			array(
				'singular' => 'ctpw_rule',     // singular name of the listed records .
				'plural'   => 'ctpw_rules',    // plural name of the listed records .
				'ajax'     => false,          // does this table support ajax? .
			)
		);

		$this->options['bulk_actions'] = array(
			'actions'   => array(
				'delete_selected' => esc_html__( 'Delete Selected', 'yith-custom-thankyou-page-for-woocommerce' ),
				'delete_all'      => esc_html__( 'Delete All', 'yith-custom-thankyou-page-for-woocommerce' ),
			),
			'functions' => array(
				'function_delete_selected' => function () {
					$post = $_POST;//phpcs:ignore WordPress.Security.NonceVerification.Missing
					if ( ( ( isset( $post['action'] ) && 'delete_selected' === $post['action'] || ( isset( $post['action2'] ) && 'delete_selected' === $post['action2'] ) ) ) && isset( $post['rules'] ) && '' !== $post['rules'] ) {
						if ( count( $post['rules'] ) > 0 ) {
							foreach ( $post['rules'] as $rule ) {
								$rule_array = explode( '|', $rule );
								YITH_CTPW_RULES()->remove_rule( $rule_array[1], $rule_array[0] );
							}
						}
					}

				},
				'function_delete_all'      => function() {
					$post = $_POST;//phpcs:ignore WordPress.Security.NonceVerification.Missing
					if ( ( isset( $post['action'] ) && 'delete_all' === $post['action'] ) || ( isset( $post['action2'] ) && 'delete_all' === $post['action2'] ) ) {
						$rules = YITH_CTPW_RULES()->get_all_rules();
						if ( count( $rules ) > 0 ) {
							foreach ( $rules as $rule ) {
								YITH_CTPW_RULES()->remove_rule( $rule['object'], $rule['ID'] );
							}
						}
					}
				},
			),

		);
	}

	/**
	 * Print default column content
	 *
	 * @param $item mixed  $item Item of the row.
	 * @param $column_name $column_name string Column name.
	 *
	 * @return string Column content
	 * @since 1.2.5
	 */
	public function column_default( $item, $column_name ) {
		if ( isset( $item[ $column_name ] ) ) {
			return esc_html( $item[ $column_name ] );
		} else {
			return print_r( $item, true ); // Show the whole array for troubleshooting purposes .
		}
	}

	/**
	 * Print product column content
	 *
	 * @param $item mixed $item Item of the row.
	 *
	 * @return string Column content
	 * @since 1.2.5
	 */
	public function column_item( $item ) {
		if ( ! isset( $item['name'] ) || empty( $item['name'] ) ) {
			return '';
		}

		if ( 'product_simple' === $item['object'] || 'product_variable' === $item['object'] ) {
			$column = sprintf( '<strong><a target="_blank" href="%s">%s</a></strong>', get_edit_post_link( $item['ID'] ) , $item['name'] );
		} elseif ( 'product_variation' === $item['object'] ) {
			$p = wc_get_product( $item['ID'] );

			$column = sprintf( '<strong><a target="_blank" href="%s">%s</a></strong>', get_edit_post_link( $p->get_parent_id() ) , $item['name'] );
		} elseif ( 'product_category' === $item['object'] ) {
			$column = sprintf( '<strong><a target="_blank" href="%s">%s</a></strong>', get_edit_term_link( $item['ID'], 'product_cat' ) , $item['name'] );
		} else {
			$column = $item['name'];
		}

		return $column;
	}
	/**
	 * Print product column content
	 *
	 * @param $item mixed $item Item of the row.
	 *
	 * @return string Column content
	 * @since 1.2.5
	 */
	public function column_rid( $item ) {
		if ( ! isset( $item['ID'] ) || empty( $item['ID'] ) ) {
			return '';
		}

		$full_item = $item['ID'] . '|' . $item['object'];
		$column    = '<input type="checkbox" name="rules[]" value="' . $full_item . '" />';

		return $column;
	}

	/**
	 * Print product column content
	 *
	 * @param $item mixed $item Item of the row.
	 *
	 * @return string Column content
	 * @since 1.2.5
	 */
	public function column_object( $item ) {
		if ( ! isset( $item['object'] ) || empty( $item['object'] ) ) {
			return '';
		}

		return $item['object'];
	}

	/**
	 * Print the custom thank you option value column content
	 *
	 * @param $item mixed $item Item of the row.
	 *
	 * @return string Column content
	 * @since 1.2.5
	 */
	public function column_ctpw( $item ) {
		if ( ! isset( $item['ctpw'] ) || empty( $item['ctpw'] ) ) {
			return '';
		}

		if ( intval( $item['ctpw'] ) > 0 ) {
			$value = '<a target="_blank" href="' . get_edit_post_link( $item['ctpw'] ) . '">' . get_the_title( $item['ctpw'] ) . '</a>';
		} else {
			$value = $item['ctpw'];
		}

		return $value;
	}

	/**
	 * Print actions column content
	 *
	 * @param $item mixed $item Item of the row.
	 *
	 * @return string Column content
	 * @since 1.2.5
	 */
	public function column_actions( $item ) {
		$args = array(
			'id'                => $item['ID'],
			'rule_type'         => $item['object'],
			'remove_edit_nonce' => wp_create_nonce( 'yctpw_remove_edit_rule_nonce' ),
			'ctpw_type'         => $item['ctpw_type'],
			'ctpw'              => $item['ctpw'],
			'item_name'         => $item['name'],
			'action'            => 'edit',
		);

		$column = sprintf( '<a style="margin-right: 5px;" href="%s" class="button button-secondary">%s</a>', esc_url( add_query_arg( $args ) ), __( 'Edit', 'yith-custom-thankyou-page-for-woocommerce' ) );

		unset( $args['ctpw_type'] );
		unset( $args['ctpw_value'] );

		$args['action'] = 'delete';

		$column .= sprintf( '<a href="%s" class="button button-secondary delete">%s</a>', esc_url( add_query_arg( $args ) ), __( 'Delete', 'yith-custom-thankyou-page-for-woocommerce' ) );

		return $column;
	}

	/**
	 * Returns columns available in table
	 *
	 * @return array Array of columns of the table
	 * @since 1.2.5
	 */
	public function get_columns() {
		$columns = array(
			'rid'     => '',
			'item'    => __( 'Item', 'yith-custom-thankyou-page-for-woocommerce' ),
			'object'  => __( 'Type', 'yith-custom-thankyou-page-for-woocommerce' ),
			'ctpw'    => __( 'Custom Thank You Page/URL', 'yith-custom-thankyou-page-for-woocommerce' ),
			'actions' => __( 'Actions', 'yith-custom-thankyou-page-for-woocommerce' ),
		);

		return $columns;
	}

	/**
	 * Return array of bulk options
	 *
	 * @return  array
	 * @since   1.2.5
	 * @author  Armando Liccardo <armando.liccardo@yithemes.com>
	 */
	protected function get_bulk_actions() {

		return $this->options['bulk_actions']['actions'];
	}

	/**
	 * Return array of bulk options
	 *
	 * @return  void
	 * @since   1.2.5
	 * @author  Armando Liccardo <armando.liccardo@yithemes.com>
	 */
	public function process_bulk_action() {

		$action = 'function_' . $this->current_action();

		if ( array_key_exists( $action, $this->options['bulk_actions']['functions'] ) ) {

			call_user_func( $this->options['bulk_actions']['functions'][ $action ] );

		}

	}

	/**
	 * Returns column to be sortable in table
	 *
	 * @return array Array of sortable columns
	 * @since 1.2.5
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'item' => array( 'items_name', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Prepare items for table
	 *
	 * @return void
	 * @since 1.2.5
	 */
	public function prepare_items() {

		/* process eventual bulk actions */
		$this->process_bulk_action();

		// sets pagination arguments.
		$per_page     = apply_filters( 'yctpw_rules_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = 0;

		// sets columns headers.
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$yctpw_items = YITH_CTPW_RULES()->get_all_rules();

		$items_name = get_array_column( $yctpw_items, 'name' );

		$request = $_REQUEST;//phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$column_order = isset( $request['orderby'] ) && in_array( $request['orderby'], array( 'items_name', ) ) ? $request['orderby'] : 'items_name';
		$order        = isset( $request['order'] ) ? 'SORT_' . strtoupper( $request['order'] ) : 'SORT_ASC';

		array_multisort( ${$column_order}, constant( $order ), $yctpw_items );

		// retrieve data for table.
		$this->items = $yctpw_items;
		$total_items = count( $yctpw_items );

		// sets pagination args.
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

	/**
	 * Print html container for table
	 *
	 * @since 1.2.5
	 * @author Armando Liccardo <armando.liccardo@yithemes.com>
	 * @return void
	 */
	public function before_table_part() {
		?>
		<div class="yith-plugin-fw-wp-page-wrapper yctpw-rules">
			<div class="wrap">
		<?php
	}
	/**
	 * Print closing html container for table
	 *
	 * @since 1.2.5
	 * @author Armando Liccardo <armando.liccardo@yithemes.com>
	 * @return void
	 */
	public function after_table_part() {
		?>
			</div><!-- closing wrap -->
		</div>
		<?php
	}

	/**
	 * Get Form Fieds
	 *
	 * @since 1.2.5
	 * @author Armando Liccardo <armando.liccardo@yithemes.com>
	 * @return array
	 */
	public function get_fields() {

		/* get payment methods */
		$installed_payment_methods = WC()->payment_gateways->payment_gateways();

		$payments = array();
		foreach ( $installed_payment_methods as $payment_method ) {
			$payments = array_merge( $payments, array( $payment_method->id => $payment_method->title ) );
		}

		$pages = array( '0' => 'none' );
		$pages = $pages + yith_ctpw_list_all_pages();

		/* Fields */
		$fields = array(
			0 => array(
				'id'      => 'item_type',
				'name'    => 'item_type',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => array(
					'product'          => esc_html__( 'Products', 'yith-custom-thankyou-page-for-woocommerce' ),
					'product_category' => esc_html__( 'Categories', 'yith-custom-thankyou-page-for-woocommerce' ),
					'payment_method'   => esc_html__( 'Payment Methods', 'yith-custom-thankyou-page-for-woocommerce' ),
				),
				'title'   => esc_html__( 'Item type', 'yith-custom-thankyou-page-for-woocommerce' ),
				'value'   => '',
				'desc'    => esc_html__( 'Choose whether to set a Thank You page or Custom Url for products, categories or payment methods.', 'yith-custom-thankyou-page-for-woocommerce' ),
			),
			1 => array(
				'id'       => 'product_id',
				'name'     => 'product_id',
				'type'     => 'ajax-products',
				'multiple' => false,
				'data'     => array(
					'action'      => 'woocommerce_json_search_products_and_variations',
					'security'    => wp_create_nonce( 'search-products' ),
					'placeholder' => esc_html__( 'Search products', 'yith-custom-thankyou-page-for-woocommerce' ),
				),
				'title'    => esc_html__( 'Select products', 'yith-custom-thankyou-page-for-woocommerce' ),
				'desc'     => esc_html__( 'Select a product to set a Thank You Page or Custom Url.', 'yith-custom-thankyou-page-for-woocommerce' ),
			),
			2 => array(
				'id'       => 'category_id',
				'name'     => 'category_id',
				'type'     => 'ajax-terms',
				'multiple' => false,
				'data'     => array(
					'placeholder' => esc_html__( 'Search category', 'yith-custom-thankyou-page-for-woocommerce' ),
					'taxonomy'    => 'product_cat',
				),
				'title'    => esc_html__( 'Select categories', 'yith-custom-thankyou-page-for-woocommerce' ),
				'desc'     => esc_html__( 'Select which product categories to add in the exclusion list.', 'yith-custom-thankyou-page-for-woocommerce' ),
			),
			3 => array(
				'id'      => 'payment_method',
				'name'    => 'payment_method',
				'title'   => esc_html__( 'Select payment method', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => $payments,
				'default' => 'none',
			),
			4 => array(
				'id'      => 'yith_ctpw_general_page_or_url',
				'name'    => 'yith_ctpw_general_page_or_url',
				'title'   => esc_html_x( 'Thank You Page redirect', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'    => 'select',
				'options' => array(
					'ctpw_page' => esc_html__( 'Custom Wordpress Page', 'yith-custom-thankyou-page-for-woocommerce' ),
					'ctpw_url'  => esc_html__( 'External URL', 'yith-custom-thankyou-page-for-woocommerce' ),
				),
				'default' => 'ctpw_page',
				'desc'    => esc_html__( 'Select which kind of redirect to use.', 'yith-custom-thankyou-page-for-woocommerce' ),
			),
			5 => array(
				'id'      => 'yith_thankyou_page',
				'name'    => 'yith_thankyou_page',
				'title'   => esc_html_x( 'Select a Page', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'    => 'select',
				'options' => $pages,
				'default' => 'none',
			),

			6 => array(
				'id'    => 'yith_thankyou_url',
				'name'  => 'yith_thankyou_url',
				'title' => esc_html__( 'Write the Url', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'  => 'text',
				'desc'  => esc_html__( 'Set the URL to redirect. Write full url for ex: https://yithemes.com/', 'yith-custom-thankyou-page-for-woocommerce' ),
			),

		);

		return $fields;
	}

	/**
	 * Print New/Edit Rule Form
	 *
	 * @param string $action default is new, it can be edit as well.
	 * @since 1.2.5
	 * @author Armando Liccardo <armando.liccardo@yithemes.com>
	 * @return void
	 */
	public function rule_form( $action = 'new' ) {

		$action = ( 'new' !== trim( $action ) & 'edit' !== trim( $action ) ) ? 'new' : $action;

		$fields = $this->get_fields();

		$query_args = array(
			'page' => 'yith_ctpw_panel',
			'tab'  => 'rules',
		);

		$form_title = ( 'new' === $action ) ? esc_html__( 'Add Rule', 'yith-custom-thankyou-page-for-woocommerce' ) : esc_html__( 'Edit Rule', 'yith-custom-thankyou-page-for-woocommerce' );

		/* if edit action manage the fields  accordingly */
		if ( 'edit' === $action ) {
			$get = $_GET;//phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$rule_type  = $get['rule_type'];
			$id         = $get['id'];
			$ctpw_type  = $get['ctpw_type'];
			$ctpw_value = $get['ctpw'];
			$name       = $get['item_name'];

			$fields[0]['type']  = 'text';
			$fields[0]['class'] = 'yctpw_false_field';
			$fields[0]['desc']  = $rule_type;
			$fields[0]['value'] = $rule_type;



			if ( strpos( $rule_type, 'product' ) !== false && 'product_category' !== $rule_type ) {
				$fields[1]['title'] = esc_html__( 'Product', 'yith-custom-thankyou-page-for-woocommerce' );
				$fields[1]['type']  = 'text';
				$fields[1]['class'] = 'yctpw_false_field';
				$fields[1]['desc']  = $name;
				$fields[1]['value'] = $id;

				unset( $fields[2] );
				unset( $fields[3] );
			}

			if ( 'product_category' === $rule_type ) {

				$fields[2]['title'] = esc_html__( 'Product Category', 'yith-custom-thankyou-page-for-woocommerce' );
				$fields[2]['type']  = 'text';
				$fields[2]['class'] = 'yctpw_false_field';
				$fields[2]['desc']  = $name;
				$fields[2]['value'] = $id;

				unset( $fields[1] );
				unset( $fields[3] );
			}

			if ( 'payment_method' === $rule_type ) {
				$fields[3]['title'] = esc_html__( 'Payment Method', 'yith-custom-thankyou-page-for-woocommerce' );
				$fields[3]['type']  = 'text';
				$fields[3]['class'] = 'yctpw_false_field';
				$fields[3]['desc']  = $name;
				$fields[3]['value'] = $id;

				unset( $fields[1] );
				unset( $fields[2] );
			}

			/* set the type of rule */
			$fields[4]['value'] = $ctpw_type;

			/* set the value of rule */
			if ( 'ctpw_page' === $ctpw_type ) {
				$fields[5]['value'] = $ctpw_value;
			} else {
				$fields[6]['value'] = $ctpw_value;
			}
		}
		?>
		<p>
			<a style="text-decoration: none;" href="<?php echo esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( '< Back to rules list', 'yith-custom-thankyou-page-for-woocommerce' ); ?> </a>
		</p>

		<h1 class="wp-heading-inline"><?php echo esc_html( $form_title ); ?></h1>
		<form id="form" method="POST" action="<?php echo esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) ); ?>">
			<?php wp_nonce_field( 'yctpw_rule_nonce', 'yctpw_rule_nonce' ); ?>
			<table class="form-table">
				<tbody>
				<?php foreach ( $fields as $field ) : ?>
					<tr valign="top" class="yith-plugin-fw-panel-wc-row <?php echo esc_html( $field['type'] ); ?> <?php echo esc_html( $field['name'] ); ?>">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_html( $field['name'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_html( $field['type'] ); ?>">
							<?php
							yith_plugin_fw_get_field( $field, true );
							if ( isset( $field['desc'] ) && '' !== $field['desc'] ) {
								?>
									<span class="description"><?php echo esc_html( $field['desc'] ); ?></span>
								<?php
							}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php if ( 'new' === $action ) { ?>
				<input type="hidden" id="action" name='action' value="yctpw_add_rule" />
			<?php } else { ?>
				<input type="hidden" id="action" name='action' value="yctpw_update_rule" />
			<?php } ?>
			<input id="yctpw_save_button" class="button" name="yctpw_save_button" type="submit" value="<?php echo esc_html( $form_title ); ?>" />
		</form>

		<?php
	}

	/**
	 * Print the Table
	 *
	 * @since 1.2.5
	 * @author Armando Liccardo <armando.liccardo@yithemes.com>
	 * @return void
	 */
	public function display() {

		$get = $_GET;//phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$this->before_table_part();
		if ( isset( $get['tab'] ) && 'rules' === $get['tab'] && isset( $get['action'] ) && 'insert' === $get['action'] ) {
			$this->rule_form();
		} elseif ( isset( $get['tab'] ) && 'rules' === $get['tab'] && isset( $get['action'] ) && 'edit' === $get['action'] ) {
			$this->rule_form( 'edit' );
		} else {
			echo '<h1 class="wp-heading-inline">' . esc_html__( 'Rules', 'yith-custom-thankyou-page-for-woocommerce' ) . '</h1>';
			$query_args = array(
				'page'   => 'yith_ctpw_panel',
				'tab'    => 'rules',
				'action' => 'insert',
			);

			$add_form_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
			?>
			<a class="page-title-action yith-add-button" href="<?php echo $add_form_url; ?>"><?php esc_html_e( 'Add Rule', 'yith-custom-thankyou-page-for-woocommerce' ); ?></a>
			<?php
			$general_option_type = get_option( 'yith_ctpw_general_page_or_url' );
			if ( 'ctpw_page' === $general_option_type || 'ctpw_url' === $general_option_type ) {
				echo '<p class="yctpw_general_page_info_rules">';
				if ( 'ctpw_page' === $general_option_type && ( get_option( 'yith_ctpw_general_page' ) > 0 || '' !== get_option( 'yith_ctpw_general_page' ) ) ) {
						$general_ctpw_value = get_the_title( get_option( 'yith_ctpw_general_page' ) );
				} elseif ( 'ctpw_url' === $general_option_type && '' !== trim( get_option( 'yith_ctpw_general_page_url' ) ) ) {
						$general_ctpw_value = trim( get_option( 'yith_ctpw_general_page_url' ) );
				}

				$general_edit_link = esc_url( add_query_arg( array( 'page' => 'yith_ctpw_panel', 'tab' => 'settings' ), admin_url( 'admin.php' ) ) );
				// translators: first placeholder is the page set as Thank you page redirect, second one is the url to the Settings Tab.
				echo wp_kses_post( sprintf( __( 'A General Thank You Page has been set to <b>%1$s</b>. You can change it <a href="%2$s">here</a>', 'yith-custom-thankyou-page-for-woocommerce' ), $general_ctpw_value, $general_edit_link ) );

				echo '</p>';
			}
			?>

			<form id="yctpw_rule_form" method="post" action="<?php echo add_query_arg(  array( 'page' => 'yith_ctpw_panel', 'tab'  => 'rules' ), admin_url( 'admin.php' ) ); ?>">
			<?php parent::display(); ?>
			</form>
			<?php
		}

		$this->after_table_part();
	}

}