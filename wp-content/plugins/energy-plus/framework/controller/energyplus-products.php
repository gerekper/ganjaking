<?php

/**
* EnergyPlus Products
*
* Product management
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class EnergyPlus_Products extends EnergyPlus {

	/**
	* Starts everything
	*
	* @return void
	*/

	public static function run() {

		EnergyPlus::wc_engine();

		wp_enqueue_script("energyplus-products",  EnergyPlus_Public . "js/energyplus-products.js", array(), EnergyPlus_Version);
		wp_enqueue_script("energyplus-nested-sortable",  EnergyPlus_Public . "3rd/nested-sortable.js", array(), EnergyPlus_Version);

		self::route();
	}

	/**
	* Router for sub pages
	*
	* @return void
	*/

	private static function route() {

		switch (EnergyPlus_Helpers::get('action')) {

			case 'categories':
			self::categories();
			break;

			case 'attributes':
			self::attributes();
			break;

			case 'bulk_price':
			self::bulk_price();
			break;

			default:
			self::index();
			break;
		}
	}

	/**
	* Prepare filter array for query
	*
	* @param  mixed $filter   array of filter or false
	* @return array           new filter array
	*/

	public static function filter($filter = false) {

		if (!$filter) {
			$filter['post_status'] = array('publish', 'private');
			$filter['offset']      = 0;
			$filter['page']        = 1;
			$filter['q']           = '';
			$filter['orderby']     = "date";
			$filter['order']       = "DESC";

		}

		if (!isset($filter['limit'])) {
			$filter['limit'] = absint(EnergyPlus::option('reactors-tweaks-pg-products', 10));
		}

		if (EnergyPlus_Helpers::get('go', null)) {
			$filter['mode'] = 95;
		}

		if ('' !== EnergyPlus_Helpers::get('s', '')) {
			$filter['q'] = EnergyPlus_Helpers::get('s', '');
		}

		if ('' !== EnergyPlus_Helpers::get('category', '')) {
			$filter['category'] = EnergyPlus_Helpers::get( 'category', 0 );
		}


		if ('trash' === EnergyPlus_Helpers::get('status', '')) {
			$filter['post_status'] = array('trash');
		}

		if ('private' === EnergyPlus_Helpers::get('status', '')) {
			$filter['post_status'] = array('private');
		}

		if ('draft' === EnergyPlus_Helpers::get('status', '')) {
			$filter['post_status'] = array('draft');
		}

		if (EnergyPlus_Helpers::get('pg', null)) {
			$filter['offset'] = (intval( EnergyPlus_Helpers::get( 'pg', 1 )) - 1) *  $filter['limit'];
		}

		if (EnergyPlus_Helpers::get('orderby')) {
			if (false !== strpos(EnergyPlus_Helpers::get('orderby', ''), 'meta_')){
				$filter['orderby']          = "meta_value_num";
				$filter['orderby_meta_key'] = sanitize_sql_orderby(str_replace ( 'meta_', '', EnergyPlus_Helpers::get('orderby','')));
			} else {
				$filter['orderby'] = sanitize_sql_orderby(EnergyPlus_Helpers::get('orderby',''));
			}


			$filter['order'] 	= 'ASC' === EnergyPlus_Helpers::get('order','ASC') ? 'ASC' : 'DESC';
		}

		return $filter;
	}

	/**
	* Main function
	*
	* @param  mixed $filter   array of filter
	*
	* @return EnergyPlus_View
	*/

	public static function index($filter = false) {

		global $wpdb;

		$filter = self::filter($filter);

		$pagination = array();
		$pagination['query'] = false;

		$products = array();

		$critical_stock = intval($wpdb->get_var(
			$wpdb->prepare(
				"
				SELECT COUNT(p.ID)
				FROM {$wpdb->prefix}posts as p
				INNER JOIN {$wpdb->prefix}postmeta as pm ON p.ID = pm.post_id
				WHERE p.post_type = 'product'
				AND p.post_status IN ('publish', 'private')
				AND pm.meta_key = '_manage_stock'
				AND pm.meta_value = 'yes'
				AND pm.post_id IN (SELECT post_id FROM `{$wpdb->prefix}postmeta` WHERE meta_key ='_stock' AND meta_value < %d)
				AND pm.post_id IN (SELECT post_id FROM `{$wpdb->prefix}postmeta` WHERE meta_key ='_stock_status' AND meta_value = 'instock')
				",
				get_option( 'woocommerce_notify_low_stock_amount', 2 )
				)
				)
			);


			$outof_stock = intval($wpdb->get_var(
				$wpdb->prepare(
					"
					SELECT COUNT(p.ID)
					FROM {$wpdb->prefix}posts as p
					INNER JOIN {$wpdb->prefix}postmeta as pm ON p.ID = pm.post_id
					WHERE p.post_type = 'product'
					AND p.post_status IN ('publish', 'private')
					AND pm.meta_key = '_stock_status'
					AND pm.meta_value = %s
					",
					'outofstock')
					)
				);

				$on_sales = intval($wpdb->get_var(
					$wpdb->prepare(
						"
						SELECT COUNT(p.ID)
						FROM {$wpdb->prefix}posts as p
						INNER JOIN {$wpdb->prefix}postmeta as pm ON p.ID = pm.post_id
						WHERE p.post_type = 'product'
						AND p.post_status IN ('publish', 'private')
						AND pm.meta_key = '_sale_price'
						AND pm.meta_value > %d
						",
						0)
						)
					);

					// Getting 'Critical Stock'
					if ('-1' === EnergyPlus_Helpers::get('category')) {

						$_products['products'] = array();

						$critical_stocks_ids = $wpdb->get_results(
							$wpdb->prepare(
								"
								SELECT p.ID
								FROM {$wpdb->prefix}posts as p
								INNER JOIN {$wpdb->prefix}postmeta as pm ON p.ID = pm.post_id
								WHERE p.post_type = 'product'
								AND p.post_status IN ('publish', 'private')
								AND pm.meta_key = '_manage_stock'
								AND pm.meta_value = 'yes'
								AND pm.post_id IN (SELECT post_id FROM `{$wpdb->prefix}postmeta` WHERE meta_key ='_stock' AND meta_value < %d)
								AND pm.post_id IN (SELECT post_id FROM `{$wpdb->prefix}postmeta` WHERE meta_key ='_stock_status' AND meta_value = 'instock')
								LIMIT %d
								OFFSET %d
								",
								get_option( 'woocommerce_notify_low_stock_amount', 2 ),
								absint(EnergyPlus::option('reactors-tweaks-pg-products', 10)),
								absint(EnergyPlus::option('reactors-tweaks-pg-products', 10)) * ( intval ( EnergyPlus_Helpers::get('pg' , 1) ) - 1 )
								)
							);


							if ($critical_stocks_ids) {

								$critical_stocks_ids_filter['post_status'] =  array('publish', 'private');
								$critical_stocks_ids_filter['in'] = implode(",", wp_list_pluck($critical_stocks_ids, "ID"));

								$_products  = WC()->api->WC_API_Products->get_products(null, null, $critical_stocks_ids_filter);
								$pagination = EnergyPlus_Admin::$api;
								$pagination['query']->found_posts = $critical_stock;
							}

							// Getting 'Out of Stock'
						}	elseif ('-2' === EnergyPlus_Helpers::get('category')) {

							$_products['products'] = array();

							$outofstocks_ids = $wpdb->get_results(
								$wpdb->prepare(
									"
									SELECT p.ID
									FROM {$wpdb->prefix}posts as p
									INNER JOIN {$wpdb->prefix}postmeta as pm ON p.ID = pm.post_id
									WHERE p.post_type = 'product'
									AND p.post_status IN ('publish', 'private')
									AND pm.meta_key = '_stock_status'
									AND pm.meta_value = %s
									LIMIT %d
									OFFSET %d
									",
									'outofstock',
									absint(EnergyPlus::option('reactors-tweaks-pg-products', 10)),
									absint(EnergyPlus::option('reactors-tweaks-pg-products', 10)) * ( intval ( EnergyPlus_Helpers::get('pg' , 1) ) - 1 )

									)
								);

								if ($outofstocks_ids) {

									$outofstocks_ids_filter['post_status'] = array('publish', 'private');
									$outofstocks_ids_filter['in']          = implode(",", wp_list_pluck($outofstocks_ids, "ID"));

									$_products  = WC()->api->WC_API_Products->get_products(null, null, $outofstocks_ids_filter);
									$pagination = EnergyPlus_Admin::$api;
									$pagination['query']->found_posts = $outof_stock;

								} else {

									$pagination['query'] = null;
								}
							} elseif ('-4' === EnergyPlus_Helpers::get('category')) {

								$_products['products'] = array();

								$onsales_ids = $wpdb->get_results(
									$wpdb->prepare(
										"
										SELECT p.ID
										FROM {$wpdb->prefix}posts as p
										INNER JOIN {$wpdb->prefix}postmeta as pm ON p.ID = pm.post_id
										WHERE p.post_type = 'product'
										AND p.post_status IN ('publish', 'private')
										AND pm.meta_key = '_sale_price'
										AND pm.meta_value > %d
										LIMIT %d
										OFFSET %d
										",
										0,
										absint(EnergyPlus::option('reactors-tweaks-pg-products', 10)),
										absint(EnergyPlus::option('reactors-tweaks-pg-products', 10)) * ( intval ( EnergyPlus_Helpers::get('pg' , 1) ) - 1 )

										)
									);

									if ($onsales_ids) {

										$onsales_ids_filter['post_status'] = array('publish', 'private');
										$onsales_ids_filter['in']          = implode(",", wp_list_pluck($onsales_ids, "ID"));

										$_products  = WC()->api->WC_API_Products->get_products(null, null, $onsales_ids_filter);
										$pagination = EnergyPlus_Admin::$api;
										$pagination['query']->found_posts = $outof_stock;

									} else {

										$pagination['query'] = null;
									}
								}  else {
									$_products =  WC()->api->WC_API_Products->get_products(null, null, $filter);
									$pagination = EnergyPlus_Admin::$api;
								}

								$search_categories = array();

								foreach ($_products['products'] AS $product) {
									$products[ $product['id'] ]               = $product;
									$products[ $product['id'] ]['categories'] = wc_get_object_terms( $product['id'], 'product_cat' );

									if ('variable' === $product['type']){

										foreach ($product['variations'] AS $variant)	{
											$products[$variant['id']] = $variant;
											$products[$variant['id']]['parent'] = $product['id'];
											$products[$variant['id']]['type'] = 'variant';
										}
									}
								}



								switch ( $mode = ( !empty($filter['mode']) ? absint($filter['mode']) : EnergyPlus::option('mode-energyplus-products', 1) ) ) {

									// Woocommerce Native
									case 99:
									if (!EnergyPlus_Admin::is_full()) {
										EnergyPlus_Helpers::frame( admin_url( 'edit.php?post_type=product' ) );
									} else {
										wp_redirect( admin_url( 'edit.php?post_type=product' ) );
									}
									break;

									// Other menus
									case 95:
									echo EnergyPlus_View::run('products/list-95', array('iframe_url' => EnergyPlus_Helpers::get_submenu_url(EnergyPlus_Helpers::get('go')) ));
									break;

									// Standart
									default:
									case 98:
									case 2:
									case 1:
									$categories =  EnergyPlus_Helpers::group_by('parent', WC()->api->WC_API_Products->get_product_categories()['product_categories']);
									echo EnergyPlus_View::run('products/list-' . $mode,  array( 'filter'=>$filter, 'products'=> $products, 'categories' => $categories, 'critical_stock' => $critical_stock, 'outof_stock' => $outof_stock, 'on_sales' => $on_sales,  'pagination'=>$pagination['query'], 'ajax' =>   EnergyPlus_Helpers::is_ajax()));
									break;
								}
							}

							/**
							* Get categories
							*
							* @return void
							*/

							public static function categories ()	{
								$_categories =  WC()->api->WC_API_Products->get_product_categories();
								$categories = EnergyPlus_Helpers::group_by('parent', $_categories['product_categories']);
								echo EnergyPlus_View::run('products/categories',  array( 'categories' => $categories));
							}

							/**
							* Get attributes
							*
							* @return void
							*/

							public static function attributes ( ) {

								$attributes =  WC()->api->WC_API_Products->get_product_attributes();
								echo EnergyPlus_View::run('products/attributes',  array( 'attributes' => $attributes));

							}

							/**
							* Bulk operations for setting prices
							*
							* @return null
							*/

							public static function bulk_price ( ) {

								if (!EnergyPlus_Helpers::get('ids')) {
									wp_die(-2);
								}

								$ids = explode('-', EnergyPlus_Helpers::get('ids', ''));

								if (!array($ids) || 0 === count($ids)) {
									wp_die(-3);
								}

								$ids = array_map('absint', $ids);

								$products = array();

								if ($_POST):

									$type = absint(EnergyPlus_Helpers::post('type', 0));

									$percent = 0;
									$fixed   = 0;

									switch ($type) {

										case 1:

										$percent = floatval(EnergyPlus_Helpers::post('percent_1', 0));
										$fixed   = floatval(EnergyPlus_Helpers::post('fixed_1', 0));

										break;

										case 2:

										$percent = floatval(EnergyPlus_Helpers::post('percent_2', 0)) * -1;
										$fixed   = floatval(EnergyPlus_Helpers::post('fixed_2', 0)) * -1;

										break;
									}


									foreach ($ids AS $id) {

										$_product = wc_get_product( absint($id) );

										$new = array();

										if ($_product) {

											if ($_product->get_regular_price() > 0) {
												$new['regular_price'] = floatval($_product->get_regular_price())*(1+$percent/100)+$fixed;
											}

											if ($_product->get_sale_price() > 0) {
												$new['sale_price'] = floatval($_product->get_sale_price())*(1+$percent/100)+$fixed;
											}

											$o =	WC()->api->WC_API_Products->edit_product( absint($id), array(
												'product' => $new
											) );

										}
									}

								endif;

								foreach ($ids AS $id) {
									$_product = wc_get_product( absint($id) );
									if ($_product) {
										$products[$id] = $_product;
									}
								}

								echo EnergyPlus_View::run('products/bulk-price',  array( 'products' => $products ));

							}

							/**
							* Ajax router
							*
							* @since  1.0.0
							* @return EnergyPlus_Ajax
							*/

							public static function ajax() {

								$do	=	 EnergyPlus_Helpers::post('do') ;
								EnergyPlus::wc_engine();

								switch ($do) {

									// Searching
									case 'search':

									$filter['q']        = EnergyPlus_Helpers::post('q', '');
									$filter['category'] = EnergyPlus_Helpers::post('status', '');
									$filter['mode']     = (EnergyPlus_Helpers::post('mode') ? absint(EnergyPlus_Helpers::post('mode')) : null) ;
									$filter['post_status'] = array('publish', 'private');
									echo self::index($filter);
									wp_die();

									break;

									// Delete an attributes
									case 'delete-attribute':

									$id = absint(EnergyPlus_Helpers::post('id', 0));

									if (0 === $id) {
										wp_die(-1);
									}

									if ( ! wp_verify_nonce( EnergyPlus_Helpers::post('_wpnonce'), 'energyplus-products--attr-delete-' . $id ) ) {
										wp_die( 'Security check' );
									}

									EnergyPlus::wc_engine();

									$r = WC()->api->WC_API_Products->delete_product_attribute($id);

									if ( is_wp_error($r) ){
										EnergyPlus_Ajax::error( $r->get_error_message() );
									} else {
										EnergyPlus_Ajax::success('OK');
									}

									break;

									// Bulk operations
									case 'bulk':

									EnergyPlus::wc_engine();

									$product_ids	=	 EnergyPlus_Helpers::post('id') ;

									if ('' === $product_ids) {
										exit;
									}

									$ids = explode ( ',', $product_ids );

									if ( !is_array( $ids ) OR ( 0 === count( $ids ) ) ) {
										exit;
									}

									$ids = array_map('absint', $ids);

									$success = array();

									foreach ($ids AS $id)	{

										$product =  WC()->api->WC_API_Products->get_product(intval($id));

										if (! $product ) {
											continue;
										}

										if ( 'outofstock' === EnergyPlus_Helpers::post( 'state' ) OR 'instock' === EnergyPlus_Helpers::post( 'state' )) {

											if ('instock' === EnergyPlus_Helpers::post( 'state' )) {
												if (0 < $product['product']['stock_quantity']) {
													$new_stock_quantity = intval($product['product']['stock_quantity']);
												} else {
													$new_stock_quantity = 9998;
												}
												$new_instock        = true;
											} else {
												$new_stock_quantity = 0;
												$new_instock        = false;
											}

											if (true === $product['product']['managing_stock'])	{
												$do_it = array('stock_quantity' => $new_stock_quantity);
											} else {
												$do_it = array('in_stock' => $new_instock);
											}

											$return = WC()->api->WC_API_Products->edit_product( $product['product']['id'], array(
												'product' => $do_it
											));

											if (true === $return['product']['in_stock']) {
												if (true === $return['product']['managing_stock']){
													$r[] = array('id'=>$id, 'status'=>intval($return['product']['stock_quantity']));
												} else {
													$r[] = array('id'=>$id, 'status'=>'<span class="text-mute">∞</span>');
												}
											} else {
												$r[] = array('id'=>$id, 'status'=>'<span class="badge badge-danger">'.esc_html__('Out Of Stock', 'energyplus').'</span>');
											}
										} else if ( 'trash' === EnergyPlus_Helpers::post( 'state' ) ) {

											$change = wp_trash_post( $id );

											if ($change) {
												$r[] = array('id'=>$id, 'status'=>esc_html__('Deleted', 'energyplus'));
											} else {
												return EnergyPlus_Ajax::error(sprintf(esc_html__('Product #%d can not be deleted', 'energyplus'), $id));
											}

										} else if ( 'deleteforever' === EnergyPlus_Helpers::post( 'state' ) ) {

											$change = wp_delete_post( $id, true );

											if ($change) {
												$r[] = array('id'=>$id, 'status'=>esc_html__('Deleted', 'energyplus'));
											} else {
												return EnergyPlus_Ajax::error(sprintf(esc_html__('Product #%d can not be deleted', 'energyplus'), $id));
											}

										} else if ( 'restore' === EnergyPlus_Helpers::post( 'state' ) ) {

											$change = wp_untrash_post( $id );

											if ($change) {
												$r[] = array('id'=>$id, 'status'=>'<span class="badge badge-success">'.esc_html__('Restored', 'energyplus').'</span>');
											} else {
												return EnergyPlus_Ajax::error(sprintf(esc_html__('Product #%d can not be restored', 'energyplus'), $id));
											}

										}
									}

									return EnergyPlus_Ajax::success('OK', array('id'=> $r, ''));

									break;

									// Set quantity and prices for product
									case 'quantity':

									$id    = intval( EnergyPlus_Helpers::post('id', 0 ));
									$name  = EnergyPlus_Helpers::post('name', '' );
									$val   = EnergyPlus_Helpers::post('val', '' );
									$state = ('true' === EnergyPlus_Helpers::post('state',  'false')) ? false : true;

									$product =  WC()->api->WC_API_Products->get_product( intval($id) );

									if (is_wp_error( $product )) {
										wp_die(-1);
									}

									switch ($name) {

										// Set price of product
										case 'sale_price':
										case 'regular_price':
										case 'set_price':

										$id        = intval( EnergyPlus_Helpers::post('id', 0 ));
										$name      = ('sale_price' === EnergyPlus_Helpers::post('name',  '')) ? 'sale_price' : 'regular_price';

										$product =  WC()->api->WC_API_Products->get_product( intval(EnergyPlus_Helpers::clean($id)));

										if (is_wp_error( $product )) {
											wp_die(-1);
										}

										$product = current( $product );

										$k =	WC()->api->WC_API_Products->edit_product( $product['id'], array(
											'product' => array(
												'regular_price' => EnergyPlus_Helpers::post('val'),
												'sale_price'    => EnergyPlus_Helpers::post('val1')
											)
										)
									);

									$product =  WC()->api->WC_API_Products->get_product( intval(EnergyPlus_Helpers::clean($id)));
									$product = current( $product );

									EnergyPlus_Ajax::success($product['price_html'], array(), TRUE);

									break;

									// Set quantity of product
									case "qnty":

									$return = WC()->api->WC_API_Products->edit_product( $product['product']['id'], array(
										'product' => array(
											'stock_quantity' => intval($val),
											'managing_stock' => true,
											'in_stock'=> true
										)
									));

									self::stock_status($return);

									break;

									// Set stock to unlimited
									case "unlimited":

									$return = WC()->api->WC_API_Products->edit_product( $product['product']['id'], array(
										'product' => array(
											'managing_stock' => $state
										)
									));

									self::stock_status($return);

									break;

									// Set stock to Out Of Stock
									case "outofstock":
									if (true === $product['product']['managing_stock'])	{
										if (false === $state) {
											$do_it = array('stock_quantity' => 0);
										} else {
											$do_it = array('stock_quantity' => 9999);
										}
									} else {
										$do_it = array('in_stock' => $state);
									}

									$return = WC()->api->WC_API_Products->edit_product( $product['product']['id'], array(
										'product' => $do_it
									));


									self::stock_status($return);

									break;
								}

								break;


								// Set product's visibilty on catalog
								case 'visible':

								$id           = intval( EnergyPlus_Helpers::post('id', 0 ));
								$state        = ('true' === EnergyPlus_Helpers::post('state',  'false')) ? 'visible' : 'hidden';
								$state_status = ('true' === EnergyPlus_Helpers::post('state',  'false')) ? 'publish' : 'private';

								$product =  WC()->api->WC_API_Products->get_product( intval(EnergyPlus_Helpers::clean($id)));

								if (is_wp_error( $product )) {
									wp_die(-1);
								}

								$product = current( $product );

								$k =	WC()->api->WC_API_Products->edit_product( $product['id'], array(
									'product' => array(
										'catalog_visibility' => $state,
										'status'             => $state_status
									)
								) );

								EnergyPlus_Ajax::success();

								break;

								// Set stock of procut
								case 'in_stock':
								$id = intval( EnergyPlus_Helpers::post('id', 0 ));
								$state = ('true' === EnergyPlus_Helpers::post('state',  'false')) ? true : false;

								$product =  WC()->api->WC_API_Products->get_product( intval(EnergyPlus_Helpers::clean($id)));

								if (is_wp_error( $product )) {
									wp_die(-1);
								}

								$product = current( $product );
								if ('variable' === $product['type']) {
									foreach ($product['variations'] AS $variants)
									{
										$k =	WC()->api->WC_API_Products->edit_product( $variants['id'], array(
											'product' => array(
												'in_stock' => $state
											)
										) );
									}
								} else {
									$k =	WC()->api->WC_API_Products->edit_product( $product['id'], array(
										'product' => array(
											'in_stock' => $state
										)
									) );
								}
								EnergyPlus_Ajax::success();

								break;

								// Reorder categories
								case 'categories_reorder':

								$ids	=	 $_POST['ids'] ;

								if (!is_array( $ids ))	{
									wp_die ( -1 );
								}

								foreach ($ids AS $i => $id)	{

									if (!isset($id['id'])) {
										continue;
									}

									$i = absint($i);

									$category =  WC()->api->WC_API_Products->get_product_category(absint($id['id']));

									if (!is_wp_error($category)) {

										wc_set_term_order( $category['product_category']['id'], $i, 'product_cat' );
										$k = WC()->api->WC_API_Products->edit_product_category($category['product_category']['id'], array(
											'product_category' => array(
												'product_category' => absint($category['product_category']['id']),
												'parent'           => absint($id['parent_id'])
											)
										)
									);
									// do_action( 'woocommerce_after_set_term_order', $term, $index, $taxonomy );
								}
							}

							EnergyPlus_Ajax::success('Done');
						}
					}

					/**
					* Get stock status label
					*
					* @since  1.0.0
					* @param  array   $return
					* @return null
					*/

					public static function stock_status($return) {

						if (true === $return['product']['in_stock'])	{
							if (true === $return['product']['managing_stock'])	{
								EnergyPlus_Ajax::success(intval($return['product']['stock_quantity']));
							} else {
								EnergyPlus_Ajax::success('<span class="text-mute">∞</span>');
							}
						} else {
							EnergyPlus_Ajax::success('<span class="badge badge-danger">Out Of Stock</span>');
						}
					}
				}

				?>
