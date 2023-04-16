<?php
/**
 * Inventory class for integrating with the Amazon Inventory API through SP API.
 *
 * TODO: 4.1.0 This class should implement all inventory calls to SP API.
 * TODO: It should also contain all helper functions that are needed in dealing with inventory.
 * TODO: Code will need to be moved in here FROM class-ns-fba-inventory.php.
 * TODO: When all the inventory functionality is here and tested well, then class-ns-fba-inventory.php can be deleted.
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 4.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'NS_MCF_Inventory' ) ) {

	/**
	 * Inventory class. For handling all API and local operations and functions working with product inventory.
	 */
	class NS_MCF_Inventory extends NS_MCF_Integration {

		/**
		 * Initialize class.
		 */
		public function init() {
			add_action( 'init', array( $this, 'maybe_init_sp_api' ) );
		}

		/**
		 * Initialize our local SP_API object.
		 */
		public function maybe_init_sp_api() {
			if ( ! $this->ns_fba->sp_api->is_initialized() && is_object( $this->ns_fba->wc_integration ) ) {
				$this->ns_fba->sp_api->init_api( $this->wc_integration->get_SP_API_options() );
			}
		}


		/**
		 * Get SKUs.
		 *
		 * @param string $marketplace_id The marketplace id.
		 * @param string $next_token The next token.
		 *
		 * @return array
		 */
		public function get_SKUs( $marketplace_id, $next_token = '' ): array { // phpcs:ignore WordPress.NamingConventions
			$path = '/fba/inventory/v1/summaries?details=true&granularityType=Marketplace&granularityId=' . $marketplace_id . '&marketplaceIds=' . $marketplace_id;

			if ( ! empty( $next_token ) ) {
				$path .= '&nextToken=' . $next_token;
			}
			$response = $this->make_request( $path, 'GetInventorySkus' );
			if ( SP_API::is_error_in( $response ) ) {
				$this->ns_fba->logger->add_entry( $response, 'wc', '_inventory' );
				return array(
					'success' => false,
					'message' => 'An error has occurred getting SKUs from API',
					'data'    => array(),
				);
			}
			$body              = json_decode( $response['body'] );
			$added_inventory   = array();
			$pending_inventory = array();
			// phpcs:disable WordPress.NamingConventions.ValidVariableName
			if ( isset( $body->payload ) && isset( $body->payload->inventorySummaries ) ) {

				/**
				 * Iterates the inventory summary to differentiate the product according to
				 * whether it already exists in woocommerce or not,
				 * and stores it in two different arrays for further processing.
				 */
				foreach ( $body->payload->inventorySummaries as $inventorySummary ) {

					$wc_product_id = wc_get_product_id_by_sku( $inventorySummary->sellerSku );

					$inventorySummary->totalQuantity = $inventorySummary->inventoryDetails->fulfillableQuantity;

					$sp_product_details = $this->ns_fba->fulfill->get_sp_product_details( $inventorySummary->sellerSku );

					if ( ! empty( $sp_product_details ) ) {
						if ( ! empty( $sp_product_details->SmallImage ) ) {
							$inventorySummary->image_url = $sp_product_details->SmallImage->URL;
						}

						if ( ! empty( $sp_product_details->ListPrice ) ) {
							$inventorySummary->regular_price = $sp_product_details->ListPrice->Amount;
						}
					}

					if ( 0 === $wc_product_id ) {
						$inventorySummary->md5_sku      = md5( $inventorySummary->sellerSku );
						$inventorySummary->string_value = base64_encode( wp_json_encode( $inventorySummary ) );
						$pending_inventory[]            = $inventorySummary;
					} else {
						$wc_product = (object) wc_get_product( $wc_product_id )->get_data();

						$wc_product = $this->get_formatted_wc_product( $wc_product );

						$added_inventory[] = $wc_product;
					}
				}
			}
			// phpcs:enable WordPress.NamingConventions.ValidVariableName
			$data = array(
				'added_inventory'   => $added_inventory,
				'pending_inventory' => $pending_inventory,
				'next_token'        => $body->pagination->nextToken ?? '',
			);
			return array(
				'success' => true,
				'message' => 'Success',
				'data'    => $data,
			);
		}


		/**
		 * Import Seller Partner SKUs
		 *
		 * @param array $data An encode object collection.
		 *
		 * @return array
		 */
		public function import_SKUs( $data ): array { // phpcs:ignore WordPress.NamingConventions
			$response = array(
				'added'   => array(),
				'failure' => array(),
				'ignored' => array(),
			);
			if ( empty( $data ) ) {
				return $response;
			}

			foreach ( $data as $encoded_object ) {
				$sp_product = json_decode( base64_decode( $encoded_object ) );

				$wc_product_id = wc_get_product_id_by_sku( $sp_product->sellerSku ); // phpcs:ignore WordPress.NamingConventions

				if ( ! $wc_product_id ) {
					$this->create_simple_product( $sp_product, $response['added'], $response['failure'] );
				} else {
					$response['ignored'][] = md5( $sp_product->sellerSku ); // phpcs:ignore WordPress.NamingConventions
				}
			}
			return $response;
		}

		/**
		 * Set the stock level for a product.
		 *
		 * @param WC_Product $current_product  Current product.
		 * @param string     $sku              Product SKU.
		 * @param string     $log_entry_prefix The log entry prefix.
		 * @param int        $stock            The stock amount.
		 */
		public function set_product_stock( $current_product, $sku, $log_entry_prefix, $stock ) {
			if ( ! $current_product ) {
				// Try get by SKU.
				$current_product = $this->ns_fba->utils->get_product_by_sku( $sku );
			}

			// If products do not match, exit early.
			if ( ! $current_product ) {
				$this->ns_fba->logger->add_entry(
					"Invalid product<br />\n",
					'debug',
					$this->ns_fba->inv_log_path
				);
				return;
			}
			// check if the FBA stock level for the product is at or below our threshold.
			$threshold = $this->ns_fba->options['ns_fba_low_stock_threshold'];
			if ( $stock > $threshold ) {
				$reason = ' because it is safely more than the set threshold: ' . $threshold;
			} else {
				// set WC stock level to 0 to prevent overselling this product.
				$reason = ' because current stock of ' . $stock . ' is equal to or less than the set threshold: ' . $threshold;
				$stock  = 0;
			}

			$sync_inventory_selected_only = isset( $this->ns_fba->options['ns_fba_update_inventory_selected_only'] )
				&& $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_update_inventory_selected_only'] );
			$update_product_stock         = true;
			if ( 'sync-inventory' === $log_entry_prefix && $sync_inventory_selected_only ) {
				$update_product_stock = $this->ns_fba->utils->is_product_amazon_fulfill( $current_product );
			}

			if ( $update_product_stock ) {
				if ( $this->ns_fba->wc->is_woo_version( '3.0' ) ) {
					wc_update_product_stock( $current_product, $stock );
				} else {
					$current_product->set_stock( $stock );
				}

				$this->ns_fba->logger->add_entry( $log_entry_prefix . ' Updated ' . $sku . ' stock to ' . $stock . $reason, 'wc', '_inventory' );
				$this->ns_fba->logger->add_entry(
					$log_entry_prefix . ' Updated ' . $sku . ' stock to ' . $stock . $reason . "<br />\n",
					'debug',
					$this->ns_fba->inv_log_path
				);
			} else {
				$this->ns_fba->logger->add_entry( $log_entry_prefix . ' Skipped ' . $sku . '. Only FBA products are allowed to sync', 'wc', '_inventory' );
			}
		}


		/**
		 * Prepares the WC product object to adapt it for showing in More SKUs modal table
		 *
		 * @param object $wc_product The product.
		 *
		 * @return object
		 */
		private function get_formatted_wc_product( $wc_product ) {
			// Formatting some fields format.
			$wc_product->stock_quantity    = $wc_product->stock_quantity ?? '';
			$wc_product->date_modified     = null !== $wc_product->date_modified ? $wc_product->date_modified->format( 'Y-m-d' ) : $wc_product->date_created->format( 'Y-m-d' );
			$product_meta                  = get_post_meta( $wc_product->id, 'ns_fba_is_fulfill', true );
			$wc_product->ns_fba_is_fulfill = ! empty( $product_meta ) && 'yes' === $product_meta;
			$wc_product->md5_sku           = md5( $wc_product->id );

			return $wc_product;
		}


		/**
		 * Receives an object with product data and create a simple product in woocommerce
		 *
		 * @param object $sp_product The product from Amazon.
		 * @param array  $added Added array of products.
		 * @param array  $failure Failed product skus.
		 */
		private function create_simple_product( $sp_product, &$added, &$failure ) {
			// phpcs:disable WordPress.NamingConventions.ValidVariableName
			try {
				$product = new WC_Product_Simple();
				$product->set_name( $sp_product->productName );
				$product->set_status( 'draft' );
				$product->set_manage_stock( true );
				$product->set_stock_quantity( $sp_product->totalQuantity );
				$product->set_sku( $sp_product->sellerSku );
				$product->set_regular_price( $sp_product->regular_price );
				$product->update_meta_data( 'ns_fba_is_fulfill', 'yes' );
				$product->save();

				if ( ! empty( $sp_product->image_url ) ) {
					$upload = wc_rest_upload_image_from_url( esc_url_raw( $sp_product->image_url ) );

					if ( ! is_wp_error( $upload ) ) {
						$attachment_id = wc_rest_set_uploaded_image_as_attachment( $upload, $product->get_id() );

						if ( wp_attachment_is_image( $attachment_id ) ) {
							$product->set_image_id( $attachment_id );
							$product->save();
						}
					}
				}

				$added[] = $this->get_formatted_wc_product( (object) $product->get_data() );
			} catch ( Exception $exception ) {
				$failure[] = md5( $sp_product->sellerSku );
			}
			// phpcs:enable WordPress.NamingConventions.ValidVariableName
		}


		/**
		 * Make the remote request.
		 *
		 * @param   string $path               URL to be called.
		 * @param   string $type               Amazon request type / end-point / function.
		 * @param   int    $qty                Total fulfillment Qty (if applicable).
		 * @param   string $method             Request method.
		 * @param   string $json_encoded_body  The json body.
		 *
		 * @return WP_Error|array can be WP_Error or array
		 */
		private function make_request( string $path = '',
			string $type = 'Unspecified',
			int $qty = 0,
			string $method = 'GET',
			string $json_encoded_body = '' ) {

			// Check if the ap_api is null and initiate it.
			$this->maybe_init_sp_api();
			return $this->ns_fba->sp_api->make_request( $path, $type, $qty, $method, $json_encoded_body );
		}

	} // class.
}
