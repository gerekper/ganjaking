<?php
/**
 * Field: Product Catalogs.
 *
 * @package WC_Instagram/Admin/Fields
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Instagram_Admin_Field_Product_Catalogs', false ) ) {
	return;
}

if ( ! class_exists( 'WC_Instagram_Admin_Field_Table', false ) ) {
	include_once 'abstract-class-wc-instagram-admin-field-table.php';
}

/**
 * WC_Instagram_Admin_Field_Product_Catalogs class.
 */
class WC_Instagram_Admin_Field_Product_Catalogs extends WC_Instagram_Admin_Field_Table {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param array $field The field arguments.
	 */
	public function __construct( $field ) {
		$data    = ( isset( $field['value'] ) && is_array( $field['value'] ) ? $field['value'] : array() );
		$columns = array(
			'title'        => array(
				'label' => _x( 'Title', 'product catalogs: table column', 'woocommerce-instagram' ),
			),
			'products'     => array(
				'label' => _x( 'Products', 'product catalogs: table column', 'woocommerce-instagram' ),
			),
			'variations'   => array(
				'label' => _x( 'Variations', 'product catalogs: table column', 'woocommerce-instagram' ),
			),
			'tax_location' => array(
				'label' => _x( 'Tax location', 'product catalogs: table column', 'woocommerce-instagram' ),
			),
			'stock'        => array(
				'label' => _x( 'Stock', 'product catalogs: table column', 'woocommerce-instagram' ),
			),
			'feeds'        => array(
				'label' => _x( 'Data feeds', 'product catalogs: table column', 'woocommerce-instagram' ),
				'width' => '1%',
			),
		);

		parent::__construct( $field, $columns, $data );
	}

	/**
	 * Gets the row URL.
	 *
	 * @since 3.0.0
	 *
	 * @param int $row The row index.
	 * @return string
	 */
	public function get_row_url( $row ) {
		$params = array(
			'catalog_id' => $row,
		);

		return wc_instagram_get_settings_url( $params );
	}

	/**
	 * Outputs the 'add catalog' button.
	 *
	 * @since 3.0.0
	 */
	public function add_button() {
		printf(
			'<a class="button" href="%1$s">%2$s</a>',
			esc_url( $this->get_row_url( 'new' ) ),
			esc_html__( 'Add catalog', 'woocommerce-instagram' )
		);
	}

	/**
	 * Outputs the field content.
	 *
	 * @since 4.0.0
	 */
	public function output() {
		parent::output();

		include 'views/html-admin-field-product-catalogs.php';
	}

	/**
	 * Outputs the blank row.
	 *
	 * @since 3.0.0
	 */
	public function output_blank_row() {
		?>
		<tr class="wc-instagram-product-catalogs-blank-row">
			<td colspan="<?php echo esc_attr( count( $this->columns ) ); ?>">
				<p><?php esc_html_e( 'Some considerations before creating a catalog:', 'woocommerce-instagram' ); ?></p>
				<p>
					<ul class="wc-instagram-product-catalogs-tips">
						<li><?php esc_html_e( 'Each catalog will add a data feed URL in your store to import the products to a Facebook Catalog.', 'woocommerce-instagram' ); ?></li>
						<li><?php esc_html_e( 'The feeds will be updated periodically with the latest product data.', 'woocommerce-instagram' ); ?></li>
						<li><?php echo wp_kses_post( __( 'To keep your catalog synchronized, set an <strong>Automatic File Upload Schedule</strong> in the Facebook Catalog.', 'woocommerce-instagram' ) ); ?></li>
					</ul>
				</p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Outputs the action links for a row.
	 *
	 * @since 3.0.0
	 *
	 * @param int                          $row             The row index.
	 * @param WC_Instagram_Product_Catalog $product_catalog Product Catalog.
	 */
	public function row_actions( $row, $product_catalog ) {
		$catalog_url = wc_instagram_get_product_catalog_url( $product_catalog );

		$actions = array(
			'settings' => array(
				'label' => __( 'Edit', 'woocommerce-instagram' ),
				'url'   => $this->get_row_url( $product_catalog->get_id() ),
			),
			'delete'   => array(
				'label' => __( 'Delete', 'woocommerce-instagram' ),
				'url'   => '#',
			),
			'view'     => array(
				'label'  => __( 'View', 'woocommerce-instagram' ),
				'url'    => $catalog_url,
				'target' => '_blank',
			),
			'copy'     => array(
				'label'    => __( 'Copy URL', 'woocommerce-instagram' ),
				'url'      => $catalog_url,
				'target'   => '_blank',
				'data-tip' => __( 'Copied!', 'woocommerce-instagram' ),
			),
		);

		$action_strings = array();

		foreach ( $actions as $key => $action ) {
			$custom_attributes = wc_instagram_get_attrs_html( array_diff_key( $action, array_flip( array( 'label', 'url' ) ) ) );

			$action_strings[] = sprintf(
				'<a class="wc-instagram-product-catalog-%1$s" href="%2$s"%3$s>%4$s</a>',
				esc_attr( $key ),
				esc_url( $action['url'] ),
				( ! empty( $custom_attributes ) ? " {$custom_attributes}" : '' ),
				esc_html( $action['label'] )
			);
		}

		echo '<div class="row-actions">';
		echo join( ' | ', $action_strings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}

	/**
	 * Outputs the column 'title'.
	 *
	 * @since 3.0.0
	 *
	 * @param int                          $row             The row index.
	 * @param WC_Instagram_Product_Catalog $product_catalog Product Catalog.
	 */
	public function output_column_title( $row, $product_catalog ) {
		printf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $this->get_row_url( $product_catalog->get_id() ) ),
			esc_html( $product_catalog->get_title() )
		);

		$this->row_actions( $row, $product_catalog );
	}

	/**
	 * Outputs the column 'products'.
	 *
	 * @since 3.0.0
	 *
	 * @param int                          $row             The row index.
	 * @param WC_Instagram_Product_Catalog $product_catalog Product Catalog.
	 */
	public function output_column_products( $row, $product_catalog ) {
		printf(
			'<span class="products-number">%s</span>',
			esc_html( count( $product_catalog->get_product_ids() ) )
		);
	}

	/**
	 * Outputs the column 'variations'.
	 *
	 * @since 3.0.0
	 *
	 * @param int                          $row             The row index.
	 * @param WC_Instagram_Product_Catalog $product_catalog Product Catalog.
	 */
	public function output_column_variations( $row, $product_catalog ) {
		$this->output_column_boolean( $row, $product_catalog->get_include_variations() );
	}

	/**
	 * Outputs the column 'tax_location'.
	 *
	 * @since 3.0.0
	 *
	 * @param int                          $row             The row index.
	 * @param WC_Instagram_Product_Catalog $product_catalog Product Catalog.
	 */
	public function output_column_tax_location( $row, $product_catalog ) {
		echo esc_html( wc_instagram_get_formatted_product_catalog_tax_location( $product_catalog, '-' ) );
	}

	/**
	 * Outputs the column 'stock'.
	 *
	 * @since 4.1.0
	 *
	 * @param int                          $row             The row index.
	 * @param WC_Instagram_Product_Catalog $product_catalog Product Catalog.
	 */
	public function output_column_stock( $row, $product_catalog ) {
		$this->output_column_boolean( $row, $product_catalog->get_include_stock() );
	}

	/**
	 * Outputs the column 'feeds'.
	 *
	 * @since 4.2.0
	 *
	 * @param int                          $row             The row index.
	 * @param WC_Instagram_Product_Catalog $product_catalog Product Catalog.
	 */
	public function output_column_feeds( $row, $product_catalog ) {
		$formats = array( 'xml', 'csv' );

		if ( has_filter( 'wc_instagram_product_catalog_download_formats' ) ) {
			wc_deprecated_hook( 'wc_instagram_product_catalog_download_formats', '4.2.0', 'wc_instagram_product_catalog_feed_formats' );

			/**
			 * Filters the available formats to download a product catalog.
			 *
			 * @since 3.0.0
			 * @deprecated 4.2.0
			 *
			 * @param array $formats The available formats.
			 */
			$formats = apply_filters( 'wc_instagram_product_catalog_download_formats', $formats );
		}

		/**
		 * Filters the available data feed formats of a product catalog.
		 *
		 * @since 4.2.0
		 *
		 * @param array $formats The available formats.
		 */
		$formats = apply_filters( 'wc_instagram_product_catalog_feed_formats', $formats );

		foreach ( $formats as $format ) :
			printf(
				'<a class="button wc-instagram-product-catalog-feed-%1$s help_tip" href="#" aria-label="%2$s" data-tip="%2$s">%3$s</a>',
				esc_attr( $format ),
				/* translators: %s: file format */
				esc_attr( sprintf( _x( 'The catalog data feed in %s format', 'product catalogs: data feed aria-label', 'woocommerce-instagram' ), strtoupper( $format ) ) ),
				esc_attr( strtoupper( $format ) )
			);
		endforeach;
	}

	/**
	 * Outputs the table footer.
	 *
	 * @since 3.0.0
	 */
	public function output_footer() {
		?>
		<tfoot>
			<tr>
				<td colspan="<?php echo esc_attr( count( $this->columns ) ); ?>">
					<?php $this->add_button(); ?>
				</td>
			</tr>
		</tfoot>
		<?php
	}

	/**
	 * Outputs the column 'download'.
	 *
	 * @since 3.0.0
	 * @deprecated 4.2.0
	 *
	 * @param int                          $row             The row index.
	 * @param WC_Instagram_Product_Catalog $product_catalog Product Catalog.
	 */
	public function output_column_download( $row, $product_catalog ) {
		wc_deprecated_function( __FUNCTION__, '4.2.0', 'WC_Instagram_Admin_Field_Product_Catalogs->output_column_feeds()' );

		$this->output_column_feeds( $row, $product_catalog );
	}
}
