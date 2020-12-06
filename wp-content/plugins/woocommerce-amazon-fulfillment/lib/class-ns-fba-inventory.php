<?php
/**
 * Inventory class for integrating with the Amazon Inventory API
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 2.0.0
 */

if ( ! class_exists( 'NS_FBA_Inventory' ) ) {

	class NS_FBA_Inventory {

		private $ns_fba;

		function __construct( $ns_fba ) {
			// local reference to the main ns_fba object
			$this->ns_fba = $ns_fba;

		}

		/**
		 * Builds an MWS inventory service request
		 *
		 * @return FBAInventoryServiceMWS_Client
		 */
		function create_service_inventory() {
			$config = array(
				'ServiceURL' => $this->ns_fba->options['ns_fba_service_url'] . '/FulfillmentInventory/2010-10-01',
				'ProxyHost' => null,
				'ProxyPort' => -1,
				'MaxErrorRetry' => 3,
			);
			$service = new FBAInventoryServiceMWS_Client(
				NS_AWS_ACCESS_KEY_ID,
				NS_AWS_SECRET_ACCESS_KEY,
				$config,
				NS_APPLICATION_NAME,
				NS_APPLICATION_VERSION
			);
			return $service;
		}

		/**
		 * Get Inventory level for a given SKU
		 *
		 * @param 	string	SKU
		 * @return 	array	number = -1 for error otherwise number in inventory
		 * 					message = '' if success or error message if fail
		 */
		function get_sku_inventory( $service, $sku ) {
			$skus = new FBAInventoryServiceMWS_Model_SellerSkuList();
			$skus->setmember( $sku );
			$request = new FBAInventoryServiceMWS_Model_ListInventorySupplyRequest();
			if ( defined( 'NS_MWS_AUTH_TOKEN' ) ) {
				$request->setMWSAuthToken( NS_MWS_AUTH_TOKEN );
			}
			$request->setSellerId( NS_MERCHANT_ID );
			$request->setMarketplaceId( NS_MARKETPLACE_ID );
			$request->setSellerSkus( $skus );
			$inventory = array();
			$inventory['number'] = -1;
			$inventory['message'] = __( 'Inventory Response from FBA not Initialized or Received', $this->ns_fba->text_domain );
			try {
				$response = $service->listInventorySupply( $request );
				if ( $response->isSetListInventorySupplyResult() ) {
					$listInventorySupplyResult = $response->getListInventorySupplyResult();
					if ( $listInventorySupplyResult->isSetInventorySupplyList() ) {
						$inventorySupplyList = $listInventorySupplyResult->getInventorySupplyList();
						$memberList = $inventorySupplyList->getmember();
						// we're only checking a single SKU at a time, so we know any result will be at [0]
						$member = $memberList[0];
						if ( $member->isSetInStockSupplyQuantity() ) {
							$inventory['number'] = $member->getInStockSupplyQuantity();
							$inventory['message'] = '';
						}
					}
				}
			} catch ( FBAInventoryServiceMWS_Exception $ex ) {
				$inventory['number'] = -1;
				$inventory['message'] = $ex->getMessage();
			}
			return $inventory;
		}

		/**
		 * Set the stock level for a product
		 *
		 * @param 	WC_Product	current_product
		 * @param 	string      sku
		 * @param 	string      log_entry_prefix
		 * @param 	int	        stock
		 * @param 	int	        i
		 *
		 */
		function set_product_stock( $current_product, $sku, $log_entry_prefix, $stock ) {
			// check if the FBA stock level for the product is at or below our threshold
			$threshold = $this->ns_fba->options['ns_fba_low_stock_threshold'];
			if ( $stock > $threshold ) {
				$reason = ' because it is safely more than the set threshold: ' . $threshold;
			} else {
				// set WC stock level to 0 to prevent overselling this product
				$reason = ' because current stock of ' . $stock . ' is equal to or less than the set threshold: ' . $threshold;
				$stock = 0;
			}
			if ( $this->ns_fba->wc->is_woo_version( '3.0' ) ) {
				wc_update_product_stock( $current_product, $stock );
			} else {
				$current_product->set_stock( $stock );
			}
			error_log( $log_entry_prefix . ' Updated ' . $sku . ' stock to ' . $stock . $reason . "<br />\n", 3, $this->ns_fba->inv_log_path );
		}

		/**
		 * Sync ALL Inventory levels for ALL products
		 *
		 * Works recursively when Amazon returns a NextToken. $next is usually empty, unless it gets set by recursion with NextToken
		 */
		function sync_all_inventory( $next = '' ) {
			// setup global to support WPML with multiple translated IDs per product
			// only if WPML is detected and Polylang does not trigger a false positive
			if ( function_exists( 'icl_object_id' ) &&
			     ! defined( 'POLYLANG_BASENAME' ) ) {
				global $sitepress;
			}

			// special log for inventory
			error_log(
				"---------------------------------------------------------------------<br />\n" .
				'Inventory Sync Running on ' . date( 'c', time() ) . "<br />\n" .
				"---------------------------------------------------------------------<br />\n",
				3, $this->ns_fba->inv_log_path
			);
			$service_inventory = $this->create_service_inventory();
			$inventory = $this->get_updated_inventory( $service_inventory, $next );
			$i = 0;
			// test for errors
			if ( $inventory['number'] > 0 && '' == $inventory['message'] ) {
				$items = $inventory['data'];
				foreach ( $items as $sku => $stock ) {
					$args = array(
						'posts_per_page'   	=> 1,
						'post_type' 		=> array( 'product', 'product_variation' ),
						'meta_key'  		=> '_sku',
						'meta_value'		=> $sku,
					);
					$products = get_posts( $args );
					foreach ( $products as $product ) {
						$product_id = $product->ID;
						$parent_id = $product->post_parent;
						$current_product = wc_get_product( $product_id );
						//error_log( $sku . ' id ' . $product_id . ' sync is set ' . $this->ns_fba->utils->isset_how( get_post_meta( $product_id, 'ns_fba_is_fulfill', true ) ) . '<br />\n', 3, $this->ns_fba->inv_log_path );
						//error_log( $sku . ' id ' . $parent_id . ' sync is set ' . $this->ns_fba->utils->isset_how( get_post_meta( $parent_id, 'ns_fba_is_fulfill', true ) ) . '<br />\n', 3, $this->ns_fba->inv_log_path );
						// Only update stock level if the product exists and if it's set to fulfill with FBA
						// or its parent is set to fulfill with FBA in the case of variations
						if ( $current_product
							 && ( $this->ns_fba->utils->isset_on( get_post_meta( $product_id, 'ns_fba_is_fulfill', true ) )
							 ||   $this->ns_fba->utils->isset_on( get_post_meta( $parent_id, 'ns_fba_is_fulfill', true ) ) ) ) {

							$message_prefix = $i++ . ': >> ';
							$this->set_product_stock( $current_product, $sku, $message_prefix, $stock );

							// Test if WPML and NOT Polylang (which can cause false positive for WPML) is active,
							// then use the TRID to find and loop through all translated products as well
							// setup global to support WPML with multiple translated IDs per product
							if ( function_exists( 'icl_object_id' ) &&
								 ! defined( 'POLYLANG_BASENAME' ) ) {
								$trid = $sitepress->get_element_trid( $product_id, 'post_product' );
					            if ( is_numeric( $trid ) ) {
					                $translations = $sitepress->get_element_translations( $trid, 'post_product' );
					                if ( is_array( $translations ) ) {
					         			// Loop through each existing translation
					                    foreach ( $translations as $translation ) {
					                        if ( ! isset( $translation->element_id ) || $translation->element_id == $product_id ) {
					                            continue;
					                        }
					                     	// Set the stock quantity in the translated product
					                     	$trans_product = wc_get_product( $translation->element_id );
						                    $message_prefix = $i . '[Translated Product (' . $translation->language_code . ')]: >> ';
						                    $this->set_product_stock( $trans_product, $sku, $message_prefix, $stock );
					                    }
					                }
					            }
							}
						} else {
							error_log( $i++ . ': >> SKIPPED ' . $sku . " because it does not exist in WooCommerce or it is NOT set to Fulfill with FBA or there was another problem with it.<br />\n", 3, $this->ns_fba->inv_log_path );
						}
					}// End foreach().
				}// End foreach().
				if ( '' != $inventory['next'] ) {
					// Call recursively with the next token until we're done getting all inventory
					error_log( $i++ . ': >> Detected next token: ' . $inventory['next'] . " - calling recursively to complete sync.<br />\n", 3, $this->ns_fba->inv_log_path );
					$this->sync_all_inventory( $inventory['next'] );
				}
			} else {
				error_log( $i++ . ': <span style="color:red;"> >> Error updating stock' .
				' with FBA message: ' . $inventory['message'] . "</span><br />\n", 3, $this->ns_fba->inv_log_path );
			}// End if().
		}

		/**
		 * Get Inventory level for all items with recent inventory changes
		 *
		 * @return 	array	number = -1 for error otherwise number in inventory
		 * 					message = '' if success or error message if fail
		 */
		function get_updated_inventory( $service, $next = '' ) {
			// setup global to support WPML with multiple translated IDs per product
			// only if WPML is detected and Polylang does not trigger a false positive
			if ( function_exists( 'icl_object_id' ) &&
			     ! defined( 'POLYLANG_BASENAME' ) ) {
				global $sitepress;
			}

			if ( '' == $next ) {
				$request = new FBAInventoryServiceMWS_Model_ListInventorySupplyRequest();
				if ( defined( 'NS_MWS_AUTH_TOKEN' ) ) {
					$request->setMWSAuthToken( NS_MWS_AUTH_TOKEN );
				}
				$request->setMarketplaceId( NS_MARKETPLACE_ID );
				// if the manual sync button was used, adjust the timeframe to include all inventory
				// default is updates within the last day
				$timeframe = ' - 1 days';
				if ( isset( $_POST['ns_fba_sync_inventory_manually'] ) || (isset( $_POST['action'] ) && 'ns_fba_sync_inventory_manually' === $_POST['action']) ) {
					$timeframe = ' - 365 days';
				}
				// ISO 8601 date
				$request->setQueryStartDateTime( date( 'c', strtotime( date( 'Y-m-d' ) . $timeframe ) ) );
				;
				if ( $this->ns_fba->is_debug ) {
					error_log( 'Set request for FBAInventoryServiceMWS_Model_ListInventorySupplyRequest ' . "<br />\n", 3, $this->ns_fba->inv_log_path );
				}
			} else {
				$request = new FBAInventoryServiceMWS_Model_ListInventorySupplyByNextTokenRequest();
				if ( defined( 'NS_MWS_AUTH_TOKEN' ) ) {
					$request->setMWSAuthToken( NS_MWS_AUTH_TOKEN );
				}
				// ARG CRAZY AMAZON LIB - ListInventorySupplyByNextTokenRequest() ONLY has setMarketplace() which is
				// inconsistent with other request models that have and require setMarketplaceId
				$request->setMarketplace( NS_MARKETPLACE_ID );
				$request->setNextToken( $next );
				if ( $this->ns_fba->is_debug ) {
					error_log( 'Set request for FBAInventoryServiceMWS_Model_ListInventorySupplyByNextTokenRequest ' . "<br />\n", 3, $this->ns_fba->inv_log_path );
					error_log( 'Next Token is: ' . $next . "<br />\n", 3, $this->ns_fba->inv_log_path );
				}
			}
			$request->setSellerId( NS_MERCHANT_ID );
			$inventory = array();
			$stock_data = array();
			$inventory['number'] = 0;
			$inventory['message'] = __( 'Inventory Response from FBA not Initialized or Received', $this->ns_fba->text_domain );
			$response = '';
			$results_available = false;
			try {
				if ( '' == $next ) {
					$response = $service->listInventorySupply( $request );
					if ( $response->isSetListInventorySupplyResult() ) {
						$results_available = true;
					}
				} else {
					$response = $service->listInventorySupplyByNextToken( $request );
					if ( $response->isSetListInventorySupplyByNextTokenResult() ) {
						$results_available = true;
					}
				}
				if ( $results_available ) {
					if ( '' == $next ) {
						$listInventorySupplyResult = $response->getListInventorySupplyResult();
					} else {
						$listInventorySupplyResult = $response->getListInventorySupplyByNextTokenResult();
					}
					if ( $listInventorySupplyResult->isSetInventorySupplyList() ) {
						$inventorySupplyList = $listInventorySupplyResult->getInventorySupplyList();
						$memberList = $inventorySupplyList->getmember();
						foreach ( $memberList as $member ) {
							if ( $member->isSetSellerSKU() && $member->isSetInStockSupplyQuantity() ) {
								// use the SKU for the key and stock number for the VALUE
								$stock_data[ $member->getSellerSKU() ] = $member->getInStockSupplyQuantity();
								$inventory['number'] = $inventory['number'] + 1;
							}
						}
					}
					if ( $listInventorySupplyResult->isSetNextToken() ) {
						$inventory['next'] = $listInventorySupplyResult->getNextToken();
					} else {
						$inventory['next'] = '';
					}
					$inventory['data'] = &$stock_data;
					$inventory['message'] = '';
					$inventory['response'] = $response;
				}
			} catch ( FBAInventoryServiceMWS_Exception $ex ) {
				$inventory['number'] = -1;
				$inventory['message'] = $ex->getMessage();
				$inventory['response'] = $response;
			}// End try().
			return $inventory;
		}
	}
}// End if().
