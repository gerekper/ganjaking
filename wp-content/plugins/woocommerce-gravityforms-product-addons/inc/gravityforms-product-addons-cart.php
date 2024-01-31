<?php

class WC_GFPA_Cart {

	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WC_GFPA_Cart;
		}
	}

	public static function instance() {
		self::register();

		return self::$instance;
	}

	private $removed_captcha = false;


	/**
	 * @var null|array
	 */
	private $lead_from_validation = null;

	private function __construct() {
		// Filters for cart actions

		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 3 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 2 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 10, 1 );

		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'order_item_meta' ), 10, 3 );
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 99, 3 );

		//Order Again
		add_filter( 'woocommerce_order_again_cart_item_data', array(
			$this,
			'on_get_order_again_cart_item_data'
		), 10, 3 );
	}

	/**
	 * Performs the validation of the gravity form.
	 * Processes the form and saves the lead for later use during the cart_item_data filter.
	 * This is being done so that multi file fields can be processed.
	 * Also resolves issues with the honeypot plugin and Signature addons.
	 *
	 * @param $valid
	 * @param $product_id
	 * @param $quantity
	 *
	 * @return mixed
	 */
	public function add_to_cart_validation( $valid, $product_id, $quantity ) {

		$changing_pages = false;
		if ( ! $valid ) {
			return false;
		}

		// Check if we need a gravity form
		$context           = ( isset( $_POST['add-variations-to-cart'] ) && $_POST['add-variations-to-cart'] ) ? 'bulk' : 'single';
		$gravity_form_data = wc_gfpa()->get_gravity_form_data( $product_id, $context );

		if ( is_array( $gravity_form_data ) && $gravity_form_data['id'] && empty( $_POST['gform_form_id'] ) ) {
			return false;
		}

		if ( $gravity_form_data && is_array( $gravity_form_data ) && isset( $gravity_form_data['id'] ) && intval( $gravity_form_data['id'] ) > 0 && isset( $_POST['gform_form_id'] ) && is_numeric( $_POST['gform_form_id'] ) ) {

			//Gravity forms generates errors and warnings.  To prevent these from conflicting with other things, we are going to disable warnings and errors.
			$err_level = error_reporting();
			error_reporting( 0 );

			if ( ! class_exists( 'GFFormDisplay' ) ) {
				require_once( GFCommon::get_base_path() . "/form_display.php" );
			}

			if ( ! class_exists( 'RGFormsModel' ) ) {
				require_once( GFCommon::get_base_path() . "/forms_model.php" );
			}

			$form_id = $_POST['gform_form_id'];

			//Disable all hooks so that the form does not get processed or sent to feeds.
			$this->disable_hooks( $form_id );

			//Remove all post_submission hooks so data does not get sent to feeds such as Zapier
			$this->disable_gform_after_submission_hooks( $form_id );

			GFFormDisplay::$submission = array();
			require_once( GFCommon::get_base_path() . "/form_display.php" );
			$_POST['gform_submit'] = $_POST['gform_old_submit'];

			GFCommon::log_debug( __METHOD__ . "(): [woocommerce-gravityforms-product-addons] Processing Add to Cart Validation #{$form_id}." );

			$delete_cart_entries = ! ( isset( $gravity_form_data['keep_cart_entries'] ) && $gravity_form_data['keep_cart_entries'] == 'yes' );
			if ( apply_filters( 'woocommerce_gravityforms_delete_entries', $delete_cart_entries ) ) {
				//We are going to delete this entry, so let's remove all after submission hooks.
				//Remove all post_submission hooks so data does not get sent to feeds such as Zapier
				$this->disable_gform_after_submission_hooks( $form_id );
			} else {
				//Entry will not be deleted, so add the hooks back in so they will be fired when the form is processed by GForms
				$this->enable_gform_after_submission_hooks( $form_id );
			}

			add_filter( 'gform_pre_process_' . $form_id, array( $this, 'on_gform_pre_process' ) );
			add_filter( 'gform_abort_submission_with_confirmation', '__return_false', 999, 1 );
			add_filter( 'gform_entry_is_spam', '__return_false', 999, 1 );

			GFFormDisplay::process_form( $form_id );

			remove_filter( 'gform_pre_process_' . $form_id, array( $this, 'on_gform_pre_process' ) );

			$_POST['gform_old_submit'] = $_POST['gform_submit'];
			unset( $_POST['gform_submit'] );

			$submission = GFFormDisplay::$submission[ $form_id ];

			if ( empty( $submission ) ) {
				$valid = false;
			}

			if ( ! $submission['is_valid'] ) {
				$valid = false;
			}


			// This changing pages logic is we can determine if we should add a wc_notice for invalid submissions.  You can't add a wc_notice if the form is valid, but you are changing pages.
			$page_to_display    = intval( $submission['page_number'] );
			$source_page_number = intval( $submission['source_page_number'] );

			// If the page to display is not the same as the source page number, then we are changing pages.
			// If the page to display is 0, then we are on the last page.  This is either the only page or the last page of a multi-page form.
			// Page to display is 0 when the form is valid and the user is on the last page of the form.
			if ( $valid && $page_to_display != 0 && $page_to_display != $source_page_number ) {
				$changing_pages = true;
			}

			// If the submission is valid, but the page to display is not the first or last page, then we need to mark valid as false so that item is not added to the cart.
			if ( $page_to_display != 0 ) {
				$valid = false;
			}

			if ( $valid ) {
				$lead                       = $submission['lead'];
				$this->lead_from_validation = $lead;
			}
			//GFCommon::log_debug( __METHOD__ . "(): [woocommerce-gravityforms-product-addons] Add to Cart Validation - Deleting Entry #{$lead['id']}." );

			error_reporting( $err_level );
		}

		if ( ! $valid && ! $changing_pages ) {
			$validation_message = $gravity_form_data['validation_message'];
			$show_wc_notices    = $gravity_form_data['show_wc_notices'] === 'yes';
			if ( $show_wc_notices ) {
				wc_add_notice( $validation_message, 'error' );
			}
		}

		return $valid;
	}


	//Helper function, used when an item is added to the cart as well as when an item is restored from session.
	public function add_cart_item( $cart_item, $restoring_session = false ) {

		// Adjust price if required based on the gravity form data
		if ( isset( $cart_item['_gravity_form_lead'] ) && isset( $cart_item['_gravity_form_data'] ) ) {

			if ( function_exists( 'wc_pb_is_bundled_cart_item' ) && wc_pb_is_bundled_cart_item( $cart_item ) ) {
				return $cart_item;
			}

			if ( ! class_exists( 'RGFormsModel' ) ) {
				require_once( GFCommon::get_base_path() . "/forms_model.php" );
			}

			//Gravity forms generates errors and warnings.  To prevent these from conflicting with other things, we are going to disable warnings and errors.
			$err_level = error_reporting();
			error_reporting( 0 );

			$gravity_form_data = $cart_item['_gravity_form_data'];
			$form_meta         = RGFormsModel::get_form_meta( $gravity_form_data['id'] );

			if ( empty( $form_meta ) ) {
				return $cart_item;
			}

			$lead = $cart_item['_gravity_form_lead'];

			$products = array();
			$total    = 0;

			$lead['id'] = uniqid() . time() . rand();

			$products = WC_GFPA_Field_Helpers::get_product_fields( $form_meta, $lead );
			if ( ! empty( $products["products"] ) ) {
				foreach ( $products["products"] as $product ) {
					$price = GFCommon::to_number( $product["price"] );
					if ( is_array( rgar( $product, "options" ) ) ) {
						$count = sizeof( $product["options"] );
						$index = 1;
						foreach ( $product["options"] as $option ) {
							$price += GFCommon::to_number( $option["price"] );
							$class = $index == $count ? " class='lastitem'" : "";
							$index ++;
						}
					}
					$subtotal = floatval( $product["quantity"] ) * $price;
					$total    += $subtotal;
				}

				$total += floatval( $products["shipping"]["price"] );
			}

			$total = apply_filters( 'woocommerce_gforms_get_cart_item_total', $total, $cart_item );

			if ( apply_filters( 'woocommerce_gforms_product_price_context', 'edit', $cart_item, $gravity_form_data, $lead ) == 'view' ) {
				$price = $cart_item['data']->get_price();
			} else {
				//Don't filter the price by default.
				$price = $cart_item['data']->get_price( 'edit' );
			}

			$price = floatval( $price ) + floatval( $total );
			$cart_item['data']->set_price( $price );
			$cart_item['_gform_total'] = floatval( $total );
			error_reporting( $err_level );

			if ( $restoring_session === false ) {
				if ( isset( $gravity_form_data['enable_cart_quantity_management'] ) && $gravity_form_data['enable_cart_quantity_management'] == 'yes' ) {

					$field = isset( $gravity_form_data['cart_quantity_field'] ) ? $gravity_form_data['cart_quantity_field'] : false;

					if ( $field ) {
						if ( isset( $products['products'][ $field ] ) ) {
							$quantity = isset( $products['products'][ $field ] ) ? $products['products'][ $field ]['quantity'] : $cart_item['quantity'];
						} else {
							$quantity = isset( $lead[ $field ] ) ? $lead[ $field ] : $cart_item['quantity'];
						}

						$cart_item['quantity'] = $quantity;
					}

				}
			}

		}

		return $cart_item;
	}

	//When the item is being added to the cart.
	public function add_cart_item_data( $cart_item_meta, $product_id, $variation_id = null ) {
		if ( ! isset( $_POST['gform_old_submit'] ) ) {
			return $cart_item_meta;
		}

		if ( isset( $cart_item_meta['_gravity_form_data'] ) && isset( $cart_item_meta['_gravity_form_lead'] ) ) {
			return $cart_item_meta;
		}

		$context                              = ( isset( $_POST['add-variations-to-cart'] ) && $_POST['add-variations-to-cart'] ) ? 'bulk' : 'single';
		$gravity_form_data                    = wc_gfpa()->get_gravity_form_data( $product_id, $context );
		$cart_item_meta['_gravity_form_data'] = $gravity_form_data;

		if ( $gravity_form_data && is_array( $gravity_form_data ) && isset( $gravity_form_data['id'] ) && intval( $gravity_form_data['id'] ) > 0 ) {

			// Gravity forms generates errors and warnings.  To prevent these from conflicting with other things, we are going to disable warnings and errors.
			$err_level = error_reporting();
			error_reporting( 0 );

			if ( ! class_exists( 'GFFormDisplay' ) ) {
				require_once( GFCommon::get_base_path() . "/form_display.php" );
			}

			if ( ! class_exists( 'RGFormsModel' ) ) {
				require_once( GFCommon::get_base_path() . "/forms_model.php" );
			}

			$form_id = $gravity_form_data['id'];

			// Store a hash of the form's fields.  Used to determine if the form has changed for future validation.
			$cart_item_meta['_gravity_form_hash'] = wc_gfpa()->get_form_field_hash( $form_id );

			$form_meta = RGFormsModel::get_form_meta( $form_id );
			$form_meta = gf_apply_filters( array( 'gform_pre_render', $form_id ), $form_meta );

			GFCommon::log_debug( __METHOD__ . "(): [woocommerce-gravityforms-product-addons] Processing Add to Cart #{$form_id}." );

			if ( $this->lead_from_validation ) {
				GFCommon::log_debug( __METHOD__ . "(): [woocommerce-gravityforms-product-addons] Using Lead from Validation #{$form_id}." );
				unset( GFFormDisplay::$submission[ $form_id ]['confirmation_message'] );
				$lead                                 = $this->lead_from_validation;
				$cart_item_meta['_gravity_form_lead'] = array(
					'form_id'    => $form_id,
					'source_url' => $lead['source_url'],
					'ip'         => $lead['ip'],
					'original_lead_id' => $lead['id'],
				);

				GFCommon::log_debug( __METHOD__ . "(): [] Lead From Validation #{$form_id}." );
				GFCommon::log_debug( print_r( $this->lead_from_validation, true ) );
				GFCommon::log_debug( __METHOD__ . "(): [] Lead from Submission #{$form_id}." );
				GFCommon::log_debug( print_r( $lead, true ) );

				foreach ( $form_meta['fields'] as $field ) {

					if ( isset( $field['displayOnly'] ) && $field['displayOnly'] ) {
						continue;
					}

					$value = WC_GFPA_Field_Helpers::get_lead_field_value( $lead, $field );

					$inputs = $field instanceof GF_Field ? $field->get_entry_inputs() : rgar( $field, 'inputs' );
					if ( is_array( $inputs ) ) {
						//making sure values submitted are sent in the value even if
						//there isn't an input associated with it
						$lead_field_keys = array_keys( $lead );
						natsort( $lead_field_keys );
						foreach ( $lead_field_keys as $input_id ) {
							if ( is_numeric( $input_id ) && absint( $input_id ) == absint( $field->id ) ) {
								$cart_item_meta['_gravity_form_lead'][ strval( $input_id ) ] = $value[ strval( $input_id ) ];
							}
						}

						foreach ( $inputs as $input ) {
							$input_id = $input['id'];
							if ( is_numeric( $input_id ) && absint( $input_id ) == absint( $field->id ) ) {
								$cart_item_meta['_gravity_form_lead'][ strval( $input['id'] ) ] = apply_filters( 'wcgf_gform_input_value', $cart_item_meta['_gravity_form_lead'][ strval( $input_id ) ], $product_id, $variation_id, $field, $input );
							}
						}

					} else {
						$cart_item_meta['_gravity_form_lead'][ strval( $field['id'] ) ] = apply_filters( 'wcgf_gform_field_value', $value, $product_id, $variation_id, $field );
					}
				}

				$delete_cart_entries = ! ( isset( $gravity_form_data['keep_cart_entries'] ) && $gravity_form_data['keep_cart_entries'] == 'yes' );
				if ( apply_filters( 'woocommerce_gravityforms_delete_entries', $delete_cart_entries ) ) {
					GFCommon::log_debug( __METHOD__ . "(): [woocommerce-gravityforms-product-addons] Add to Cart - Deleting Entry #{$lead['id']}." );
					$this->delete_entry( $lead );
				}

				error_reporting( $err_level );
			} else {
				GFCommon::log_debug( __METHOD__ . "(): [woocommerce-gravityforms-product-addons] ERROR: No Lead From Validation #{$form_id}." );
			}
			GFCommon::log_debug( __METHOD__ . "(): [woocommerce-gravityforms-product-addons] Generated Cart Item Meta #{$form_id}." );
			GFCommon::log_debug( print_r( $cart_item_meta, true ) );
		}

		$this->lead_from_validation = null;

		return $cart_item_meta;
	}

	public function get_cart_item_from_session( $cart_item, $values ) {

		if ( function_exists( 'wc_pb_is_bundled_cart_item' ) && wc_pb_is_bundled_cart_item( $cart_item ) ) {
			return $cart_item;
		}

		if ( isset( $values['_gravity_form_data'] ) ) {
			$cart_item['_gravity_form_data'] = $values['_gravity_form_data'];
		}

		if ( isset( $values['_gravity_form_lead'] ) ) {
			$cart_item['_gravity_form_lead'] = $values['_gravity_form_lead'];
		}

		if ( isset( $values['_gravity_form_hash'] ) ) {
			$cart_item['_gravity_form_hash'] = $values['_gravity_form_hash'];
		}

		if ( isset( $cart_item['_gravity_form_lead'] ) && isset( $cart_item['_gravity_form_data'] ) ) {
			$this->add_cart_item( $cart_item, true );
		}

		return $cart_item;
	}

	public function get_item_data( $other_data, $cart_item ) {

		//Short circuit because subscriptions stores the metadata and automatically adds it back in.  If we allow this to run, we end up with duplicate cart item metadata.
		if ( isset( $cart_item['subscription_initial_payment'] ) ) {
			return $other_data;
		}

		if ( isset( $cart_item['_gravity_form_lead'] ) && isset( $cart_item['_gravity_form_data'] ) ) {

			$gravity_form_data = $cart_item['_gravity_form_data'];

			//Gravity forms generates errors and warnings.  To prevent these from conflicting with other things, we are going to disable warnings and errors.
			$err_level = error_reporting();
			error_reporting( 0 );

			//Ensure GFFormDisplay exists in case developers use hooks that expect it to.
			if ( ! class_exists( 'GFFormDisplay' ) ) {
				require_once( GFCommon::get_base_path() . "/form_display.php" );
			}

			if ( ! class_exists( 'RGFormsModel' ) ) {
				require_once( GFCommon::get_base_path() . "/forms_model.php" );
			}

			$form_meta = RGFormsModel::get_form_meta( $gravity_form_data['id'] );
			$form_meta = gf_apply_filters( array( 'gform_pre_render', $gravity_form_data['id'] ), $form_meta );
			if ( ! empty( $form_meta ) ) {
				$lead           = $cart_item['_gravity_form_lead'];
				$lead['id']     = 0; //Set the lead ID to 0 so that the entry is not updated, and gravity forms plugins such as populate anything don't cause errors.
				$products       = $this->get_product_fields( $form_meta, $lead );
				$valid_products = array();
				foreach ( $products['products'] as $id => $product ) {
					if ( $product['quantity'] ) {
						$valid_products[] = $id;
					}
				}

				foreach ( $form_meta['fields'] as $field ) {

					if ( ( isset( $field['inputType'] ) && $field['inputType'] == 'hiddenproduct' ) || ( isset( $field['displayOnly'] ) && $field['displayOnly'] ) || ( isset( $field->cssClass ) && strpos( $field->cssClass, 'wc-gforms-hide-from-email-and-admin' ) !== false ) ) {
						continue;
					}

					if ( $field['type'] == 'product' ) {
						if ( ! in_array( $field['id'], $valid_products ) ) {
							continue;
						}
					}

					$value   = WC_GFPA_Field_Helpers::get_lead_field_value( $lead, $field );
					$arr_var = ( is_array( $value ) ) ? implode( '', $value ) : '-';

					if ( $value === '0' || ( ! empty( $value ) && ! empty( $arr_var ) && $value != '[]' ) ) {
						$display_value     = GFCommon::get_lead_field_display( $field, $value, isset( $lead["currency"] ) ? $lead["currency"] : false, false );
						$price_adjustement = false;
						$display_value     = apply_filters( "gform_entry_field_value", $display_value, $field, $lead, $form_meta );

						$display_text = GFCommon::get_lead_field_display( $field, $value, isset( $lead["currency"] ) ? $lead["currency"] : false, apply_filters( 'woocommerce_gforms_use_label_as_value', true, $value, $field, $lead, $form_meta ) );
						$display_text = apply_filters( "woocommerce_gforms_field_display_text", $display_text, $display_value, $field, $lead, $form_meta );

						if ( $field['type'] == 'product' ) {
							$prefix        = '';
							$display_title = GFCommon::get_label( $field );
							$display_text  = str_replace( $display_title . ',', '', $display_text );
							if ( strpos( $field->cssClass, 'wc-gforms-hide-from-email' ) !== false ) {
								$hidden = true;
							} else {
								$hidden = false;
							}
						} else {

							$display_title = GFCommon::get_label( $field );

							$prefix         = '';
							$hidden         = $field['type'] == 'hidden' || ( isset( $field['visibility'] ) && $field['visibility'] == 'hidden' );
							$display_hidden = apply_filters( "woocommerce_gforms_field_is_hidden", $hidden, $display_value, $display_title, $field, $lead, $form_meta );
							if ( $display_hidden ) {
								$prefix = $hidden ? '_' : '';
							}

							if ( ! $display_hidden && ( isset( $field->cssClass ) && strpos( $field->cssClass, 'wc-gforms-hide-from-email' ) !== false ) ) {
								$prefix        = '_gf_email_hidden_';
								$display_title = str_replace( '_gf_email_hidden_', '', $display_title );
								$hidden        = true;
							}
						}

						$cart_item_data = apply_filters( "woocommerce_gforms_get_item_data", array(
							'name'    => $prefix . $display_title,
							'display' => $display_text,
							'value'   => $display_value,
							'hidden'  => $hidden
						), $field, $lead, $form_meta );


						$other_data[] = $cart_item_data;
					}
				}
			}
			error_reporting( $err_level );
		}

		return $other_data;
	}

	/**
	 * @param $item WC_Order_Item
	 * @param $cart_item_key
	 * @param $cart_item
	 */
	public function order_item_meta( WC_Order_Item $item, $cart_item_key, $cart_item ): WC_Order_Item {
		GFCommon::log_debug( "Gravity Forms Begin Adding Order Item Meta: (#{$cart_item_key}) - Order (#{$item->get_order_id()}) - Item(#{$item->get_id()}" );

		if ( function_exists( 'woocommerce_add_order_item_meta' ) ) {
			GFCommon::log_debug( "Gravity Forms woocommerce_add_order_item_meta Exists Proceeding.. - Order (#{$item->get_order_id()}) - Item(#{$item->get_id()}" );

			if ( isset( $cart_item['_gravity_form_lead'] ) && isset( $cart_item['_gravity_form_data'] ) ) {
				if ( function_exists( 'wc_pb_is_bundled_cart_item' ) && wc_pb_is_bundled_cart_item( $cart_item ) ) {
					return $item;
				}

				$item_id = $item->get_id();

				$history = $item->get_meta( '_gravity_forms_history' );

				if ( $history ) {
					GFCommon::log_debug( "Gravity Forms Meta Data Already Added: Order Item ID(#{$item_id})" );
					GFCommon::log_debug( "Gravity Forms Skipping: Order Item ID(#{$item_id})" );

					return $item;
				}

				GFCommon::log_debug( "Gravity Forms Has cart_item['_gravity_form_lead'] and cart_item['_gravity_form_data']: Order Item ID(#{$item_id})" );

				//slash it so that unicode doesn't get stripped out by WP add_metadata wp_unslash
				$cart_item_lead = wp_slash( $cart_item['_gravity_form_lead'] );

				$item->add_meta_data( '_gravity_forms_history', array(
						'_gravity_form_hash'          => $cart_item['_gravity_form_hash'],
						'_gravity_form_lead'          => $cart_item_lead,
						'_gravity_form_data'          => $cart_item['_gravity_form_data'],
						'_gravity_form_cart_item_key' => $cart_item_key
					)
				);

				GFCommon::log_debug( "Gravity Forms Added Order Item Gravity Forms History: Order Item ID(#{$item_id})" );

				//Gravity forms generates errors and warnings.  To prevent these from conflicting with other things, we are going to disable warnings and errors.
				$err_level = error_reporting();
				error_reporting( 0 );

				$gravity_form_data = $cart_item['_gravity_form_data'];

				//Ensure GFFormDisplay exists in case developers use hooks that expect it to.
				if ( ! class_exists( 'GFFormDisplay' ) ) {
					require_once( GFCommon::get_base_path() . "/form_display.php" );
				}

				if ( ! class_exists( 'RGFormsModel' ) ) {
					require_once( GFCommon::get_base_path() . "/forms_model.php" );
				}

				$form_meta = RGFormsModel::get_form_meta( $gravity_form_data['id'] );
				$form_meta = gf_apply_filters( array(
					'gform_pre_render',
					$gravity_form_data['id']
				), $form_meta );
				if ( ! empty( $form_meta ) ) {
					$lead = $cart_item['_gravity_form_lead'];
					//We reset the lead id to disable caching of the gravity form value by gravity forms.
					//This cache causes issues with multipule cart line items each with their own form.
					$lead['id'] = uniqid() . time() . rand();

					$products       = $this->get_product_fields( $form_meta, $lead );
					$valid_products = array();
					foreach ( $products['products'] as $id => $product ) {
						if ( ! isset( $product['quantity'] ) ) {

						} elseif ( $product['quantity'] ) {
							$valid_products[] = $id;
						}
					}

					foreach ( $form_meta['fields'] as $field ) {

						if ( ( isset( $field['inputType'] ) && $field['inputType'] == 'hiddenproduct' ) || ( isset( $field['displayOnly'] ) && $field['displayOnly'] ) || ( isset( $field->cssClass ) && strpos( $field->cssClass, 'wc-gforms-hide-from-email-and-admin' ) !== false ) ) {
							$field_debug_string = print_r( $field, true );
							GFCommon::log_debug( "Gravity Forms Function - Add Order Item Meta: Skipping (#{$field_debug_string})" );
							continue;
						}

						if ( $field['type'] == 'product' ) {
							if ( ! in_array( $field['id'], $valid_products ) ) {
								GFCommon::log_debug( "Gravity Forms Add Order Item Meta: Skipping Non-Valid Product(#{$field['id']})" );
								continue;
							}
						}

						$value   = WC_GFPA_Field_Helpers::get_lead_field_value( $lead, $field );
						$arr_var = ( is_array( $value ) ) ? implode( '', $value ) : '-';

						if ( $value === '0' || ( ! empty( $value ) && ! empty( $arr_var ) && $value != '[]' ) ) {
							try {
								$strip_html = true;
								if ( $field['type'] == 'fileupload' && isset( $lead[ $field['id'] ] ) ) {
									$strip_html = false;
									$dv         = $lead[ $field['id'] ];
									$files      = json_decode( $dv );

									if ( empty( $files ) ) {
										$files = array( $dv );
									}

									$display_value = '';

									$sep = '';
									foreach ( $files as $file ) {
										$name = basename( $file );
										if ( empty( $name ) ) {
											$name = $file;
										}
										$display_value .= $sep . '<a href="' . $file . '">' . $name . '</a>';
										$sep           = ', ';
									}
								} else {

									if ( $field['type'] == 'address' ) {
										$display_value = implode( ', ', array_filter( $value ) );
									} else {
										$display_value = GFCommon::get_lead_field_display( $field, $value, isset( $lead["currency"] ) ? $lead["currency"] : false, apply_filters( 'woocommerce_gforms_use_label_as_value', true, $value, $field, $lead, $form_meta ) );
									}

									$display_value = apply_filters( "gform_entry_field_value", $display_value, $field, $lead, $form_meta );

									if ( strpos( $display_value, '<img' ) !== false ) {
										$strip_html = false;
									}
								}

								$display_title = GFCommon::get_label( $field );
								$display_title = apply_filters( "woocommerce_gforms_order_meta_title", $display_title, $field, $lead, $form_meta, $item_id, $cart_item );
								$display_value = apply_filters( "woocommerce_gforms_order_meta_value", $display_value, $field, $lead, $form_meta, $item_id, $cart_item );


								if ( apply_filters( 'woocommerce_gforms_strip_meta_html', $strip_html, $display_value, $field, $lead, $form_meta, $item_id, $cart_item ) ) {
									if ( strstr( $display_value, '<li>' ) ) {
										$display_value = str_replace( '<li>', '', $display_value );
										$display_value = explode( '</li>', $display_value );
										$display_value = trim( strip_tags( implode( ', ', $display_value ) ) );
										$display_value = trim( $display_value, ',' );
									}

									$display_value = strip_tags( wp_kses( $display_value, '' ) );
								}

								$display_text  = GFCommon::get_lead_field_display( $field, $value, isset( $lead["currency"] ) ? $lead["currency"] : false, false );
								$display_value = apply_filters( "woocommerce_gforms_field_display_text", $display_value, $display_text, $field, $lead, $form_meta );

								$prefix         = '';
								$hidden         = $field['type'] == 'hidden' || ( isset( $field['visibility'] ) && $field['visibility'] == 'hidden' );
								$display_hidden = apply_filters( "woocommerce_gforms_field_is_hidden", $hidden, $display_value, $display_title, $field, $lead, $form_meta );
								if ( $display_hidden ) {
									$prefix = $hidden ? '_' : '';
								}

								if ( ! $display_hidden && ( isset( $field->cssClass ) && strpos( $field->cssClass, 'wc-gforms-hide-from-email' ) !== false ) ) {
									$prefix        = '_gf_email_hidden_';
									$display_title = str_replace( '_gf_email_hidden_', '', $display_title );
								}

								if ( $field['type'] == 'product' ) {
									//Set the prefix to hidden if the hidden class is present.
									$prefix        = strpos( $field->cssClass, 'wc-gforms-hide-from-email' ) !== false ? '_' : '';
									$display_title = GFCommon::get_label( $field );
									$display_value = str_replace( $display_title . ',', '', $display_value );;
								}

								if ( empty( $prefix ) && empty( $display_title ) ) {
									$display_title = $field['id'] . ' -';
								}
								$value_debug_string = $prefix . $display_title . ' - Value:' . $display_value;
								GFCommon::log_debug( "Gravity Forms Adding Order Item Meta:(#{$value_debug_string})" );

								$order_item_meta = array(
									'name'  => $prefix . $display_title,
									'value' => $display_value
								);

								$order_item_meta = apply_filters( "woocommerce_gforms_order_item_meta", $order_item_meta, $field, $lead, $form_meta, $item_id, $cart_item );

								if ( $order_item_meta ) {
									$item->add_meta_data( $order_item_meta['name'], $order_item_meta['value'] );
									GFCommon::log_debug( "Gravity Forms Added Order Item Meta:({$order_item_meta['name']} - {$order_item_meta['value']})" );
								} else {
									GFCommon::log_debug( "(ERROR) Gravity Forms Did Not Add Order Item Meta, It was empty after the filter" );
								}
							} catch ( Exception $e ) {
								$e_debug_string = $e->getMessage();
								GFCommon::log_debug( "(ERROR) Gravity Forms Add Order Item Meta Exception:(#{$e_debug_string})" );
							}
						}
					}
				} else {
					GFCommon::log_debug( "(ERROR) Gravity Forms Form Meta Did Not Exist - Form ID(#{$gravity_form_data['id']} - Order (#{$item->get_order_id()}) - Item(#{$item->get_id()}" );
				}
				error_reporting( $err_level );
			} else {
				GFCommon::log_debug( "(NOTE) Gravity Forms Product Addons, Skipping: (#{$cart_item_key}) - Order Item ID (#{$item->get_id()}) - No cart_item data form gravity forms." );
			}
		} else {
			GFCommon::log_debug( "(ERROR) Gravity Forms woocommerce_add_order_item_meta DOES NOT EXIST - Order (#{$item->get_order_id()}) - Item(#{$item->get_id()}" );
		}

		return $item;
	}

	public function on_get_order_again_cart_item_data( $data, $item, $order ) {
		//disable validation
		remove_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 99, 3 );

		return $data;
	}

	//Helper Functions
	protected function get_product_fields( $form, $lead, $use_choice_text = false, $use_admin_label = false ) {
		return WC_GFPA_Field_Helpers::get_product_fields( $form, $lead, $use_choice_text, $use_admin_label );
	}


	//Use a custom delete function so we don't delete files that are uploaded.
	private function delete_entry( $entry ) {
		return WC_GFPA_Helpers_Entry::safe_delete_entry( $entry );
	}

	private function delete_entry_legacy( $entry ) {
		global $wpdb;

		$lead_id = $entry['id'];

		GFCommon::log_debug( __METHOD__ . "(): [woocommerce-gravityforms-product-addons] Deleting legacy entry #{$lead_id}." );

		/**
		 * Fires before a lead is deleted
		 *
		 * @param $lead_id
		 *
		 * @deprecated
		 * @see gform_delete_entry
		 */
		do_action( 'gform_delete_lead', $lead_id );

		$lead_table        = GFFormsModel::get_lead_table_name();
		$lead_notes_table  = GFFormsModel::get_lead_notes_table_name();
		$lead_detail_table = GFFormsModel::get_lead_details_table_name();


		//Delete from lead details
		$sql = $wpdb->prepare( "DELETE FROM $lead_detail_table WHERE lead_id=%d", $lead_id );
		$wpdb->query( $sql );

		//Delete from lead notes
		$sql = $wpdb->prepare( "DELETE FROM $lead_notes_table WHERE lead_id=%d", $lead_id );
		$wpdb->query( $sql );

		//Delete from lead meta
		gform_delete_meta( $lead_id );

		//Delete from lead
		$sql = $wpdb->prepare( "DELETE FROM $lead_table WHERE id=%d", $lead_id );
		$wpdb->query( $sql );
	}


	private function disable_hooks( $form_id ) {
		//MUST disable notifications manually.
		add_filter( 'gform_disable_notification', array( $this, 'disable_notifications' ), 999, 3 );

		add_filter( 'gform_disable_user_notification', array( $this, 'disable_notifications' ), 999, 3 );
		add_filter( 'gform_disable_user_notification_' . $form_id, array(
			$this,
			'disable_notifications'
		), 999, 3 );

		add_filter( 'gform_disable_admin_notification' . $form_id, array(
			$this,
			'disable_notifications'
		), 10, 3 );


		add_filter( 'gform_disable_admin_notification_' . $form_id, array(
			$this,
			'disable_notifications'
		), 10, 3 );


		add_filter( 'gform_disable_notification_' . $form_id, array( $this, 'disable_notifications' ), 999, 3 );

		add_filter( "gform_confirmation_" . $form_id, array( $this, "disable_confirmation" ), 998, 4 );

		$delete_cart_entries = isset( $gravity_form_data['keep_cart_entries'] ) && $gravity_form_data['keep_cart_entries'] == 'yes' ? false : true;

	}

	private function disable_gform_after_submission_hooks( $form_id ) {
		global $wp_filter, $wp_actions;
		$tag = 'gform_after_submission';
		if ( ! isset( $this->_wp_filters[ $tag ] ) ) {
			if ( isset( $wp_filter[ $tag ] ) ) {
				$this->_wp_filters[ $tag ] = $wp_filter[ $tag ];
				unset( $wp_filter[ $tag ] );
			}
		}
		$tag = "gform_after_submission_{$form_id}";
		if ( ! isset( $this->_wp_filters[ $tag ] ) ) {
			if ( isset( $wp_filter[ $tag ] ) ) {
				$this->_wp_filters[ $tag ] = $wp_filter[ $tag ];
				unset( $wp_filter[ $tag ] );
			}
		}
		$tag = 'gform_entry_post_save';
		if ( ! isset( $this->_wp_filters[ $tag ] ) ) {
			if ( isset( $wp_filter[ $tag ] ) ) {
				$this->_wp_filters[ $tag ] = $wp_filter[ $tag ];
				unset( $wp_filter[ $tag ] );
			}
		}
		$tag = "gform_entry_post_save_{$form_id}";
		if ( ! isset( $this->_wp_filters[ $tag ] ) ) {
			if ( isset( $wp_filter[ $tag ] ) ) {
				$this->_wp_filters[ $tag ] = $wp_filter[ $tag ];
				unset( $wp_filter[ $tag ] );
			}
		}

	}

	private function enable_gform_after_submission_hooks( $form_id ) {
		global $wp_filter;
		$tag = 'gform_after_submission';
		if ( isset( $this->_wp_filters[ $tag ] ) ) {
			$wp_filter[ $tag ] = $this->_wp_filters[ $tag ];
		}
		$tag = "gform_after_submission_{$form_id}";
		if ( isset( $this->_wp_filters[ $tag ] ) ) {
			$wp_filter[ $tag ] = $this->_wp_filters[ $tag ];
		}
	}


	/**
	 * Disable gravity forms notifications for the form.
	 *
	 * @param type $disabled
	 * @param type $form
	 * @param type $lead
	 *
	 * @return boolean
	 */
	public function disable_notifications( $disabled, $form, $lead ) {
		return true;
	}


	/**
	 * Disable any type of confirmations for the form.
	 *
	 */
	public function disable_confirmation( $confirmation, $form, $lead, $ajax ) {
		if ( is_array( $confirmation ) && isset( $confirmation['redirect'] ) ) {
			return $confirmation;
		} else {
			return false;
		}
	}


	public function on_gform_pre_process( $form ) {

		$captcha_id = null;
		if ( isset( $form['fields'] ) ) {
			foreach ( $form['fields'] as $index => $field ) {
				if ( isset( $field['type'] ) && $field['type'] == 'captcha' ) {
					$captcha_id = $index;

					$this->removed_captcha = array(
						'index' => $index,
						'field' => $field
					);

				}
			}
		}

		if ( $captcha_id !== null ) {
			unset( $form['fields'][ $captcha_id ] );
		}

		return $form;
	}

}
