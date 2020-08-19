<?php

class GP_Conditional_Pricing extends GWPerk {

	public $version = GP_CONDITIONAL_PRICING_VERSION;
	protected $min_gravity_forms_version = '1.8.8';
	protected $min_wp_version = '3.5.1';
	protected $min_perks_version = '1.0.1';

	private $_import_pricing_logic = array();

	// set in self::is_match() method so that functions using the 'gform_is_value_match' filter can retrieve the current form object
	public static $current_form;

	public function init() {

		load_plugin_textdomain( 'gp-conditional-pricing', false, basename( dirname( __file__ ) ) . '/languages/' );

		// add a custom menu item to the Form Settings page menu
		add_filter( 'gform_form_settings_menu', array( $this, 'conditional_pricing_menu_item' ), 10, 2 );
		add_action( 'gform_form_settings_page_conditional_pricing_page', array( $this, 'conditional_pricing_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'gform_form_update_meta', array( $this, 'preserve_conditional_pricing_rules' ), 10, 3 );
		add_action( 'admin_init', array( $this, 'pricing_rules_import_listener' ) );

		add_action( 'gform_enqueue_scripts', array( $this, 'enqueue_form_scripts' ) );
		add_action( 'gform_register_init_scripts', array( $this, 'register_init_script' ), 10, 2 );
		add_action( 'gform_validation', array( $this, 'validate_submission' ), 9 );
		add_filter( 'gform_has_conditional_logic', array( $this, 'has_conditional_logic' ), 10, 2 );

		// export + import hooks
		add_filter( 'gform_export_form', array( $this, 'modify_export_form' ) );
		add_filter( 'gform_export_options', array( $this, 'modify_export_options' ) );
		add_filter( 'gform_form_update_meta', array( $this, 'modify_imported_form' ), 10, 3 );
		add_filter( 'gform_import_form_xml_options', array( $this, 'modify_import_form_xml_options' ) );

		wp_register_script( "{$this->slug}-frontend", $this->get_base_url() . '/scripts/gwconditionalpricing.js', array( 'jquery', 'gwp-common', 'gform_gravityforms' ) );

	}

	function conditional_pricing_menu_item( $menu_items, $form_id ) {

		$form = RGFormsModel::get_form_meta( $form_id );

		if( ! $form || ! self::has_product_field( $form ) )
			return $menu_items;

		$menu_items[] = array(
			'name' => 'conditional_pricing_page',
			'label' => __('Conditional Pricing', 'gp-conditional-pricing')
		);

		return $menu_items;
	}

	function enqueue_admin_scripts() {

		if ( rgget( 'page' ) != 'gf_edit_forms' || rgget( 'view' ) != 'settings' || rgget( 'subview' ) != 'conditional_pricing_page' ) {
			return;
		}

		if( $this->is_importer() ) {
			wp_enqueue_script( 'gpcp-importer', $this->get_base_url() . '/scripts/gpcp-importer.js', array( 'jquery' ), $this->version, false );
		} else {
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-sortable' );
		}

	}

	function is_importer() {
		return rgget( 'subview' ) == 'conditional_pricing_page' && rgget( 'tab' ) == 'import';
	}

	function conditional_pricing_page() {

		if( $this->is_importer() ) {
			$this->import_pricing_levels_page();
		} else {
			$this->pricing_rules_page();
		}

	}

	function pricing_rules_page() {

		// this is where saving happens
		$result = $this->handle_conditional_pricing_save_submission();

		GFFormSettings::page_header( __( 'Conditional Pricing', 'gp-conditional-pricing' ) );

		wp_print_scripts( array( 'underscore', 'gform_forms', 'gform_gravityforms', 'gform_form_admin', 'gform_placeholder' ) );

		$form = GFFormsModel::get_form_meta( rgget( 'id' ) );
		$form = apply_filters( "gform_admin_pre_render_{$form['id']}", apply_filters( 'gform_admin_pre_render', $form ) );

		$products = self::get_products( $form );
		$is_submit = isset( $_POST['gw_pricing_logic'] );

		// if page was submitted and there was an error, reload the submitted data to help mitigate data loss
		if( $is_submit && !$result ) {
			$pricing_logic = json_decode( stripslashes( gwpost( 'gw_pricing_logic' ) ), ARRAY_A );
		}
		// in all other cases, get the pricing logic from the form meta
		else {
			$pricing_logic = self::get_pricing_logic( $form );
		} 
		
		if( ! empty( $this->_import_pricing_logic ) ) {

			$import_counts = array( 'products' => 0, 'pricing_levels' => 0 );
			
			foreach( $this->_import_pricing_logic as $product_id => $pricing_levels ) {

				if( ! isset( $pricing_logic[ $product_id ] ) ) {
					$pricing_logic[ $product_id ] = array();
				}

				$pricing_logic[ $product_id ] = array_merge( $pricing_logic[ $product_id ], $pricing_levels );

				$import_counts['products']++;
				$import_counts['pricing_levels'] += count( $pricing_levels );

			}
		}

		/*$pricing_logic = array(
			'2' => array(
				array(
					'price' => '5.00',
					'conditionalLogic' => array(
						'actionType' => 'show',
						'logicType' => 'any',
						'rules' => array(
							array(
								'fieldId' => 3,
								'operator' => 'is',
								'value' => 'Second Choice'
								),
							array(
								'fieldId' => 3,
								'operator' => 'is',
								'value' => 'Second Choice'
								)
							)
						)
					),
				array(
					'price' => '10.00',
					'conditionalLogic' => array(
						'actionType' => 'show',
						'logicType' => 'any',
						'rules' => array(
							array(
								'fieldId' => 3,
								'operator' => 'is',
								'value' => 'First Choice'
								)
							)
						)
					)
				),
			'3.3' => array(
				array(
					'price' => '5.00',
					'conditionalLogic' => array(
						'actionType' => 'show',
						'logicType' => 'any',
						'rules' => array(
							array(
								'fieldId' => 3,
								'operator' => 'is',
								'value' => 'Second Choice'
								),
							array(
								'fieldId' => 3,
								'operator' => 'is',
								'value' => 'Second Choice'
								)
							)
						)
					)
				),
			);*/

		?>

		<style type="text/css">

			.gw-conditional-pricing-product-select { max-width: 350px; }
			.gw-conditional-pricing-buttons { border-bottom: 1px solid #eee; padding-bottom: 20px; }
			.gwcp-pricing-level { background-color: #fff; border-bottom: 2px solid #eee;
				padding: 10px; border-radius: 4px; margin: 0 0 10px; }

			.gwcp-pricing-level p { margin: 0 0 5px; padding: 0 0 5px; border-bottom: 1px dotted #e7e7e7; }
			.gwcp-pricing-level li:last-child { margin: 0; }

			.gwcp-edit-pricing-level { float: right; cursor: pointer; }

			#pricing_level_action_type { display: none; }

			.gwcp-no-pricing-levels { text-align: center; margin-top: 7em; }
			.gwcp-no-pricing-levels h4 { font-size: 16px; margin: 1.33em 0 0; }
			.gwcp-no-pricing-levels p { color: #999; font-size: 14px; }

			#gwcp-pricing-level-editor { background-color: #fff; border-bottom: 2px solid #eee;
				padding: 10px; border-radius: 4px; margin: 0 0 10px; }

			#gwcp-pricing-level-price { text-align: right; width: 80px; }
			#pricing_level_conditional_logic_container { margin: 0 0 10px; padding: 0 0 10px; border-bottom: 1px dotted #e7e7e7; }
			#gwcp-pricing-level-editor-close { margin-right: 10px; }

			.gwcp-product { border-bottom: 1px solid #f7f7f7; overflow: hidden; }
			.gwcp-product h4 { float: left; cursor: pointer; }
			.gwcp-product .dropdown-arrow { float: left; margin: 17px 5px; cursor: pointer; }
			.gwcp-product .gwcp-pricing-levels { display: none; }
			.gwcp-product .gwcp-add-new-product-level { display: none; }

			.gwcp-product.open {}
			.gwcp-product.open .gwcp-pricing-level { cursor:move; cursor:-moz-move; cursor:-webkit-move; }
			.gwcp-product.open .dropdown-arrow { -webkit-transform:rotate(-180deg); -moz-transform:rotate(-180deg); -ms-transform:rotate(-180deg); -o-transform:rotate(180deg); transform:rotate(-180deg); }
			.gwcp-product.open .gwcp-pricing-levels { display: block; }
			.gwcp-product.open .gwcp-add-new-product-level { display: inline-block; margin: 0 0 1.33em; }

			.gwcp-pricing-levels { clear: left; }

			#gw-save-conditional-pricing { float: right; margin-top: 0; }
			#gpcp-import { float: right; margin-top: 0; margin-right: 10px; }

		</style>

		<script>

			<?php GFCommon::gf_global(); ?>
			<?php GFCommon::gf_vars(); ?>
			form = <?php echo json_encode( $form ); ?>;

			var gwcp, gwConditionalLogic;

			(function($){

				gwcp = function(){

					this.currentProductId = false;
					this.currentPricingLevelIndex = false;
					this.pricingLogic = <?php echo !empty( $pricing_logic ) ? json_encode( $pricing_logic ) : '{}'; ?>;
					this.origPricingLogic = $.extend( true, {}, this.pricingLogic );
					this.supportIndividualProducts = <?php echo apply_filters( "gwcp_support_individual_products_{$form['id']}", apply_filters( 'gwcp_support_individual_products', true ) ) ? 'true' : 'false'; ?>;
					this.isImport = <?php echo empty( $this->_import_pricing_logic ) ? 'false' : 'true'; ?>;

					this.strings = {
						'conditionalDescription': '<?php _e( 'This product costs <strong>{0}</strong> if <strong>{1}</strong> of the following match:', 'gp-conditional-pricing' ); ?>',
						'editorConditionalDescription': '<?php printf( __( 'This product costs %s if ', 'gp-conditional-pricing' ), '<input type="text" class="gf_money" id="gwcp-pricing-level-price" value="" />' ); ?>',
						'addNewPricingLevel': '<?php _e( 'Add New Pricing Level', 'gp-conditional-pricing' ); ?>',
						'productSelectDefaultOption': '<?php _e( 'Select a Product', 'gp-conditional-pricing' ); ?>',
						'quantity': '<?php _e( 'Quantity', 'gp-conditional-pricing' ); ?>',
						'enterQuantity': '<?php _e( 'Enter Quantity', 'gp-conditional-pricing' ); ?>',
						'or': '<?php _e( 'or', 'gp-conditional-pricing' ); ?>',
						'and': '<?php _e( 'and', 'gp-conditional-pricing' ); ?>'
					}

					this.init = function() {

						// not ready for this yet... but it does work!
						//this.cleanPricingLogic();

						this.bindEvents();
						//this.hooks();

					}

					this.bindEvents = function(){

						var gwcp = this;

						$(document).ready(function(){

							gwcp.pageElem = $('#tab_conditional_pricing_page');
							gwcp.editorElem = $('#gwcp-pricing-level-editor');

							var enableProductSelect = $('.gw-conditional-pricing-product-select');
							enableProductSelect.change(function(){
								var fieldId = $(this).val();
								gwcp.enableForProduct( fieldId );
								$(this).val('');
							}).each(function(){
								gwcp.updateProductSelect();
							});

							var saveConditionalPricing = $('.gw-save-conditional-pricing');
							saveConditionalPricing.click(function(event){
								//event.preventDefault();

								var pricingLogicInput = $('#gw_pricing_logic');
								pricingLogicInput.val( JSON.stringify( gwcp.pricingLogic ) );

								// saving should bypass the unsaved changes alert
								gwcp.origPricingLogic = gwcp.pricingLogic;

							});

							gwcp.pageElem.on('click', 'h4, img', function() {
								gwcp.maybeCloseEditor( $(this).parents('.gwcp-product') );
								$(this).parent().toggleClass('open');
							});

							var editorClose = $('#gwcp-pricing-level-editor-close');
							editorClose.click(function(){
								gwcp.closeConditionalLogicEditor();
							});

							var editorDelete = $('#gwcp-pricing-level-editor-delete');
							editorDelete.click(function(){
								var deleteButton = $(this);
								gwcp.deleteCurrentPricingLevel();
							});

							gwcp.pageElem.on('click', '.gwcp-edit-pricing-level', function(){

								var editButton = $(this);
								var parent = editButton.parents('.gwcp-pricing-levels');
								var pricingLevelElem = editButton.parents('.gwcp-pricing-level');

								gwcp.loadConditionalLogicEditor( pricingLevelElem, parent.data('productid'), pricingLevelElem.data('index') );

							});

							gwcp.pageElem.on('keyup', '#gwcp-pricing-level-price', function(){

								var pricingLevel = gwcpObj.getCurrentPricingLevelObject();
								var price = $(this).val();

								pricingLevel.price = gformToNumber( price );

							});

							gwcp.pageElem.on('click', '.gwcp-add-new-product-level', function(){
								var addNewButton = $(this);
								gwcp.addNewPricingLevel( addNewButton.data('productid') );
							});

							gwcp.setupSortables();
							gwcp.doesConditionPricingExist();

							gwcp.checkForChanges();

						});

						window.onbeforeunload = function() {
							if ( gwcp.hasChanges() )
								return 'Unsaved changes...'; // this string is not actually shown to the user so no need to localize
						}

					}

					this.hooks = function() {

						gform.addFilter( 'gform_conditional_object', gwcpObj.getPricingConditionalObject );
						gform.addFilter( 'gform_conditional_logic_description', gwcpObj.getPricingConditionalDescription );
						gform.addFilter( 'gform_conditional_logic_fields', gwcpObj.addCustomQtyFields );
						gform.addFilter( 'gform_conditional_logic_operators', gwcpObj.modifyConditionalLogicOperators );
						gform.addFilter( 'gform_conditional_logic_values_input', gwcpObj.getQtyValuesInput );

					}

					this.setupSortables = function() {

						var gwcp = this;

						$('.gwcp-pricing-levels').each(function(){

							if( $(this).data( 'sortable' ) ) {

								$(this).sortable('refresh');

							} else {

								$(this).sortable({
									items: '.gwcp-pricing-level',
									update: function(event, ui) {

										var parent = $(this);
										var productId = parent.data('productid');
										var pricingLevels = gwcp.pricingLogic[productId];
										var sortedPricingLevels = [];

										// 'index' is the only mapping between the HTML and the JS pricingLevels object
										// loop through the HTML levels and resort the JS based on that
										parent.find('.gwcp-pricing-level').each(function(i){

											var newIndex = i;
											var prevIndex = $(this).data('index');

											sortedPricingLevels.push( pricingLevels[prevIndex] );
											$(this).data('index', newIndex );

										});

										gwcp.pricingLogic[productId] = sortedPricingLevels;
										gwcp.checkForChanges();

									}

								});

							}

						});

					}

					this.enableForProduct = function( productId ){

						var field = GetFieldById( productId );
						var inputOnly = productId != parseInt( productId );
						var productLabel = this.getFieldLabel( field, productId, inputOnly );

						// add new product section for pricing levels
						$('.gw-conditional-pricing-buttons').after(
							'<div class="gwcp-product open" data-productid="' + productId + '">' +
							'<input type="hidden" name="sort[' + productId + ']">' +
							'<h4>' + productLabel + '</h4>' +
							'<img class="dropdown-arrow" src="data:image/gif;base64,R0lGODlhCwAPAJEAAAAAAP///////wAAACH5BAUUAAIALAAAAAALAA8AAAIRlI+py+0CopRnUmTX1a/77xQAOw==" />' +
							'<div class="gwcp-pricing-levels" data-productid="' + productId + '"></div>' +
							'<a class="gwcp-add-new-product-level button" data-productid="' + productId + '">' + this.strings.addNewPricingLevel + '</a>' +
							'</div>'
						);

						this.addNewPricingLevel( productId );

					}

					this.addNewPricingLevel = function( productId ) {

						// if new product (no other pricing levels), set pricingLogic for this productId as array
						if( typeof this.pricingLogic[productId] == 'undefined' )
							this.pricingLogic[productId] = [];

						// push default pricing level settings for productId
						this.pricingLogic[productId].push( {
							price: '0.00',
							position: '',
							conditionalLogic: new ConditionalLogic()
						} );

						var pricingLevelIndex = this.pricingLogic[productId].length - 1;
						var pricingLevelsContainer = $('.gwcp-pricing-levels[data-productid="' + productId + '"]');
						var newPricingLevel = $('<div class="gwcp-pricing-level" data-index="' + pricingLevelIndex + '"></div>');

						pricingLevelsContainer.append( newPricingLevel );

						this.setupSortables();
						this.loadConditionalLogicEditor( newPricingLevel, productId, pricingLevelIndex );
						this.checkForChanges();

					}

					this.loadConditionalLogicEditor = function( pricingLevelElem, productId, index ) {

						if( this.currentProductId )
							this.closeConditionalLogicEditor();

						this.currentPricingLevelElem = pricingLevelElem;
						this.currentProductId = productId;
						this.currentPricingLevelIndex = index;

						ToggleConditionalLogic( true, 'pricing_level' );

						pricingLevelElem.hide();
						pricingLevelElem.parents('.ui-sortable').sortable('disable');

						this.editorElem.insertAfter(pricingLevelElem).show();

					}

					this.closeConditionalLogicEditor = function( isDelete ) {

						if( !this.editorElem.is(':visible') )
							return;

						if( typeof isDelete == 'undefined' )
							isDelete = false;

						var pricingLevel = gwcpObj.getCurrentPricingLevelObject();

						// make sure we didn't just delete the pricing level
						if( !isDelete && pricingLevel != false ) {
							this.refreshPricingLevelUI( this.currentPricingLevelElem, pricingLevel );
							this.currentPricingLevelElem.show();
						}

						// if deleting a pricing rule, update the sortable
						if( isDelete )
							this.setupSortables();

						this.currentPricingLevelElem.parents('.ui-sortable').sortable('enable');

						this.editorElem.hide();
						this.checkForChanges();

					}

					this.maybeCloseEditor = function( productElem ) {

						if( !productElem.hasClass('open') )
							return;

						var isEditorInProduct = productElem.find( '#gwcp-pricing-level-editor' ).length > 0;
						if( !isEditorInProduct )
							return;

						this.closeConditionalLogicEditor()

					}

					/**
					 * Used when deleting a pricing level to recreate the remaining pricing levels with the correct indexes.
					 */
					this.refreshProductPricingLevelsUI = function( parent ) {

						var productId = parent.data('productid');
						var pricingLevels = this.pricingLogic[productId];
						var pricingLevelsHtml = '';

						for( i in pricingLevels ) {

							pricingLevelsHtml += '<div class="gwcp-pricing-level" data-index="' + i + '"> \
                            ' + this.getPricingLevelHtml( pricingLevels[i], productId ) + ' \
                            </div>';

						}

						parent.html( pricingLevelsHtml );

					}

					this.refreshPricingLevelUI = function( pricingLevelElem, pricingLevel ) {
						var html = this.getPricingLevelHtml( pricingLevel, this.currentProductId );
						pricingLevelElem.html( html );
					}

					this.getPricingLevelHtml = function( pricingLevel, productId ) {

						var ruleStrings = this.getReadablePricingRules( pricingLevel.conditionalLogic );
						var connector = pricingLevel.conditionalLogic.logicType == 'all' ? this.strings.and : this.strings.or;

						var price = gformFormatMoney( pricingLevel.price, true );
						var html = '<p>' + this.strings.conditionalDescription.format( price, pricingLevel.conditionalLogic.logicType ) +
							'<a class="gwcp-edit-pricing-level">Edit</a>' +
							'</p>';
						html += '<ul><li>' + ruleStrings.join( ' <i style="color: #999;">' + connector + '</i></li><li>' ) + '</li></ul>';

						return html;
					}

					this.getCurrentPricingLevelObject = function() {

						var pricingLevels = this.pricingLogic[this.currentProductId];

						for( i in pricingLevels ) {
							if( i == this.currentPricingLevelIndex )
								return pricingLevels[i];
						}

						return false;
					}

					this.getReadablePricingRules = function( conditionalLogic ) {

						var strings = [],
							operatorSlugs = {
								'is':          'is',
								'isnot':       'isNot',
								'>':           'greaterThan',
								'<':           'lessThan',
								'contains':    'contains',
								'starts_with': 'startsWith',
								'ends_with':   'endsWith'
							};

						for( i in conditionalLogic.rules ) {

							var rule = conditionalLogic.rules[i];
							var isQuantityRule = this.isCustomQtyField( rule.fieldId );
							var fieldId = rule.fieldId;

							if( isQuantityRule )
								fieldId = this.getCustomQtyFieldId( rule.fieldId );

							var field = GetFieldById( fieldId ),
								fieldLabel = '';

							if( rule.fieldId.indexOf( '_' ) === 0 ) {
								// this is going to get me into trouble...
								switch( rule.fieldId ) {
									case '_gpcld_current_time':
										fieldLabel = 'Δ Current Time';
										break;
									default:
										fieldLabel = rule.fieldId.replace( '_', ' ' ).replace( /\b\w/g, function( l ) { return l.toUpperCase() } );
								}
							} else {
								fieldLabel = this.truncateText( GetLabel( field, rule.fieldId ) );
							}

							if( isQuantityRule )
								fieldLabel += ' (' + this.strings.quantity + ')';

							strings.push( fieldLabel + ' <em>' + gf_vars[operatorSlugs[rule.operator]] + '</em> "' + rule.value.replace( /(<([^>]+)>)/ig, '' ) + '"' );

						}

						return strings;
					}

					this.getFieldLabel = function( field, inputId, inputOnly ) {

						var label = GetLabel( field, inputId, inputOnly );

						// for multi-product fields, get specially formatted label: "Choice Label (Field Label)"
						if(  this.isMultiProductField( field ) && field.id != inputId )
							label = this.getChoiceLabel(field, inputId) + ' (' + label + ')';

						return label;
					}

					this.getChoiceLabel = function( field, choiceId ) {

						for( var i = 0; i < field.choices.length; i++ ) {
							var choice = field.choices[i];
							var currentChoiceId = field.id + '.' + (i + 1);
							if( choiceId == currentChoiceId )
								return choice.text;
						}

						return '';
					}

					this.isMultiProductField = function( field ) {
						return $.inArray( GetInputType( field ), [ 'select', 'radio' ] ) != -1;
					}

					this.checkForChanges = function() {

						var saveButton          = $( '.gw-save-conditional-pricing' ),
							saveButtonText      = saveButton.val(),
							hasChangesIndicator = saveButtonText.charAt( saveButtonText.length - 1 ) == '*',
							hasChanges          = this.hasChanges() || this.isImport;

						if( ! hasChanges ) {
							if( hasChangesIndicator ) {
								saveButton.val( saveButtonText.substring( 0, saveButtonText.length - 2 ) );
							}
						} else {
							if( ! hasChangesIndicator ) {
								saveButton.val( saveButtonText + ' *' );
							}
							this.doesConditionPricingExist();
						}

					}

					this.hasChanges = function() {
						return JSON.stringify( this.origPricingLogic ) != JSON.stringify( this.pricingLogic );
					}

					this.deleteCurrentPricingLevel = function() {

						this.pricingLogic[this.currentProductId].splice( this.currentPricingLevelIndex, 1 );

						this.closeConditionalLogicEditor( true );
						this.editorElem.appendTo( this.editorElem.parents('form') );

						var productContainer = $('.gwcp-product[data-productid="' + this.currentProductId + '"]');

						// remove entire product container if there are no other pricing levels
						// make sure this happens after we move the editor out of the container to be removed
						if( this.pricingLogic[this.currentProductId].length <= 0 ) {
							delete this.pricingLogic[this.currentProductId];
							productContainer.remove();
							this.doesConditionPricingExist();
						} else {
							this.refreshProductPricingLevelsUI( productContainer.find('.gwcp-pricing-levels') )
						}



					}

					this.updateProductSelect = function() {

						var products = this.getProducts();
						var currentProductIds = _.keys( this.pricingLogic );

						var productSelect = $('.gw-conditional-pricing-product-select');
						var options = '<option value="">' + this.strings.productSelectDefaultOption + '</option>';
						var hasOptions = false;

						for( var i = 0; i < products.length; i++ ) {

							var product = products[i];

							if( $.inArray( product.id.toString(), currentProductIds ) != -1 )
								continue;

							hasOptions = true;

							options += '<option value="' + product.id + '">' + product.label + '</option>';

						}

						if (! hasOptions)
							options += '<option disabled="disabled">&mdash; All products have been assigned a pricing level</option>';


						productSelect.html( options );

					}

					this.getProducts = function() {

						var products = [];

						// 'form' variable is JS global
						for( var i = 0; i < form.fields.length; i++ ) {

							var field = form.fields[i];

							if( field.type != 'product' || $.inArray( field.inputType, [ 'calculation', 'price' ] ) != - 1 )
								continue;

							if( this.isMultiProductField( field ) ) {

								// add "parent" product
								products.push({
									id: field.id,
									label: GetLabel( field, field.id )
								});

								// add "child" products
								if( this.supportIndividualProducts ) {
									for( var j = 0; j < field.choices.length; j++ ) {

										var choice = field.choices[j];
										var id = j + 1;
										var inputId = field.id + '.' + id;

										products.push({
											id: inputId,
											label: choice.text + ' (' + GetLabel( field, field.id ) + ')'
										});

									}
								}

							} else {

								products.push({
									id: field.id,
									label: GetLabel( field, field.id )
								});

							}

						}

						return products;
					}

					// Conditional Logic Integration

					this.getPricingConditionalObject = function( object, objectType ) {
						return gwcpObj.getCurrentPricingLevelObject();
					}

					this.getPricingConditionalDescription = function( description, descPieces, objectType, object ) {

						var pricingLevel = gwcpObj.getCurrentPricingLevelObject();
						var priceNumber  = gformToNumber( object.price ); //gformFormatNumber( object.price, 2 ); //gformToNumber( object.price );

						descPieces.objectDescription = gwcpObj.strings.editorConditionalDescription.replace( /value=""/, 'value="' + priceNumber + '"' );

						return makeArray( descPieces ).join(' ');
					}

					this.addCustomQtyFields = function( options, form ) {

						for( i in form.fields ) {

							var field = form.fields[i];
							if( field.type != 'product'
								|| field.inputType != 'singleproduct'
								|| gwcpObj.productHasQuantityField( field.id, form )
								|| field.disableQuantity )
								continue;

							options.push({
								label: ( field.adminLabel ? field.adminLabel : field.label ) + ' (' + gwcpObj.strings.quantity + ')',
								value: 'quantity_' + field.id
							});

						}

						return options;
					}

					this.productHasQuantityField = function( fieldId, form ) {

						for( i in form.fields ) {
							if( form.fields[i].type == 'quantity' && form.fields[i].productId == fieldId )
								return true;
						}

						return false;
					}

					this.modifyConditionalLogicOperators = function( operators, objectType, fieldId ) {

						if( !gwcpObj.isQtyField( fieldId ) && !gwcpObj.isCustomQtyField( fieldId ) )
							return operators;

						operators = {
							'is': 'is',
							'isnot': 'isNot',
							'>': 'greaterThan',
							'<': 'lessThan'
						};

						return operators;
					}

					this.getQtyValuesInput = function( inputHtml, objectType, ruleIndex, selectedFieldId, selectedValue ) {

						if( !gwcpObj.isQtyField( selectedFieldId ) && !gwcpObj.isCustomQtyField( selectedFieldId ) )
							return inputHtml;

						if( selectedValue == undefined ) {
							selectedValue = '';
						}

						var setRuleProp = "SetRuleProperty(\"" + objectType + "\", " + ruleIndex + ", \"value\", jQuery(this).val());";

						return "<input \
                                type='number' \
                                placeholder='" + gwcpObj.strings.enterQuantity + "' \
                                class='gfield_rule_select' \
                                id='" + objectType + "_rule_value_" + ruleIndex + "' \
                                value='" + selectedValue.replace(/'/g, "&#039;") + "' \
                                onchange='" + setRuleProp + "' \
                                onkeyup='" + setRuleProp + "'>";
					}

					this.isCustomQtyField = function( fieldId ) {

						// check for actual field IDs cheaply
						if( !isNaN( parseInt( fieldId ) ) )
							return false;

						// check of our quantity_X tag
						var regex = /(quantity)_([0-9]+)/
						var match = regex.exec( fieldId );

						if( !match || match[1] != 'quantity' )
							return false;

						return true;
					}

					this.getCustomQtyFieldId = function( fieldId ) {

						// check for our quantity_X tag
						var regex = /(quantity)_([0-9]+)/
						var match = regex.exec( fieldId );

						return match[2];
					}

					this.isQtyField = function( fieldId ) {
						var field = GetFieldById( fieldId )
						return field && field.type == 'quantity';
					}

					this.doesConditionPricingExist = function() {
						var noPricingLevels = $('.gwcp-no-pricing-levels');
						if ($('.gwcp-product').length >= 1) {
							noPricingLevels.hide();
						} else {
							noPricingLevels.show();
						}
					}

					this.truncateText = function( text ) {

						if( ! text || text.length <= 50 ) {
							return text;
						}

						return text.substr( 0, 25 ) + "..." + text.substr( text.length -24, 25 );

					}

					this.cleanPricingLogic = function() {

						var cleanPricingLogic = {};

						for( productId in this.pricingLogic ) {

							if( ! this.pricingLogic.hasOwnProperty( productId ) ) {
								continue;
							}

							var fieldId = parseInt( productId );

							for( var i = 0; i < form.fields.length; i++ ) {

								if( form.fields[i].id != fieldId ) {
									continue;
								}

								// choice-specific rules; make sure product choice still exists
								if( fieldId != productId ) {
									var inputId = String( productId ).split( '.' )[1];
									if( typeof form.fields[i].choices[ inputId ] != "undefined" ) {
										cleanPricingLogic[ productId ] = this.pricingLogic[ productId ];
									}
								}
								// field-specific rules
								else {
									cleanPricingLogic[ productId ] = this.pricingLogic[ productId ];
								}

							}

						}

						this.pricingLogic = $.extend( {}, cleanPricingLogic );

					};

					this.init();

				}

			})(jQuery);

			var gwcpObj = new gwcp();
			gwcpObj.hooks();

		</script>

		<?php if( isset( $import_counts ) ): ?>
			<div class="updated notice is-dismissible">
				<p><?php printf( $this->get_import_count_notice( $import_counts ), $import_counts['pricing_levels'], $import_counts['products'] ); ?> You <strong>must Save Conditional Pricing</strong> to preserve these changes.</p>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text">Dismiss this notice.</span>
				</button>
			</div>
		<?php endif; ?>

		<form name="gw-conditional-pricing" action="" method="post">

			<?php wp_nonce_field( 'gw_conditional_pricing_save', 'gw_conditional_pricing_save', true ); ?>
			<input type="hidden" name="gw_pricing_logic" id="gw_pricing_logic" value="" />

			<div class='gw-conditional-pricing-buttons'>

				<select class="gw-conditional-pricing-product-select">
					<!-- Dynamically populated via gwcp.updateProductSelect method -->
				</select>

				<input type="submit" id="gw-save-conditional-pricing" class="gw-save-conditional-pricing button button-primary" value="<?php _e( 'Save Conditional Pricing', 'gp-conditional-pricing' ); ?>">
				<?php if( apply_filters( 'gpcp_enable_importer', false ) ): ?>
					<a href="<?php echo add_query_arg( 'tab', 'import' ); ?>" id="gpcp-import" class="button button-secondary"><?php esc_html_e( 'Import Pricing Levels', 'gp-conditional-pricing' ); ?></a>
				<?php endif; ?>

			</div>

			<?php foreach( $pricing_logic as $product_id => $pricing_levels ):

				$product = rgar( $products, $product_id );
				if( !$product )
					continue;

				?>

				<div class="gwcp-product" data-productid="<?php echo $product['id']; ?>">
					<input type="hidden" name="sort[<?php echo $product['id']; ?>]">
					<h4><?php echo $product['label']; ?></h4>
					<img class="dropdown-arrow" src="data:image/gif;base64,R0lGODlhCwAPAJEAAAAAAP///////wAAACH5BAUUAAIALAAAAAALAA8AAAIRlI+py+0CopRnUmTX1a/77xQAOw==" />

					<div class="gwcp-pricing-levels" data-productid="<?php echo $product['id']; ?>">

						<?php
						foreach( $pricing_levels as $index => $pricing_level ):
							$connector = $pricing_level['conditionalLogic']['logicType'] == 'all' ? __('and', 'gp-conditional-pricing') : __('or', 'gp-conditional-pricing');
							?>

							<div class="gwcp-pricing-level" data-index="<?php echo $index; ?>">
								<p>
									This product costs <strong><?php echo GFCommon::to_money( $pricing_level['price'] ); ?></strong> if <strong><?php echo $pricing_level['conditionalLogic']['logicType']; ?></strong> of the following match:
									<a class="gwcp-edit-pricing-level">Edit</a>
								</p>
								<ul>
									<li><?php echo implode( ' <i style="color: #999;">' . $connector . '</i></li><li>', self::readable_pricing_rules( $pricing_level['conditionalLogic'], $form ) ); ?></li>
								</ul>
							</div>

						<?php endforeach; ?>
					</div>

					<a class="gwcp-add-new-product-level button" data-productid="<?php echo $product['id']; ?>"><?php _e( 'Add New Pricing Level', 'gp-conditional-pricing' ); ?></a>
				</div>

			<?php endforeach; ?>

			<div style="<?php echo ($pricing_logic) ? "display: none;" : null; ?>" class="gwcp-no-pricing-levels">
				<h4><?php _e('You have not added any pricing levels.', 'gp-conditional-pricing' ); ?></h4>
				<p><?php _e('Select a product from the drop down above to add a pricing level for that product.', 'gp-conditional-pricing' ); ?></p>
			</div>

			<div id="gwcp-pricing-level-editor" style="display:none;">
				<input type="checkbox" id="pricing_level_conditional_logic" value="1" checked="checked" style="visibility:hidden;position:absolute;left:-999em;" />
				<div id="pricing_level_conditional_logic_container">
					<!-- dynamically populated -->
				</div>
				<div class="gwcp-pricing-level-editor-actions submitbox">
					<a id="gwcp-pricing-level-editor-close" class="button"><?php _e( 'Done', 'gp-conditional-pricing' ); ?></a>
					<a id="gwcp-pricing-level-editor-delete" class="submitdelete deletion"><?php _e( 'Delete', 'gp-conditional-pricing' ); ?></a>
				</div>
			</div>

		</form>

		<?php

		require_once( GFCommon::get_base_path() . '/form_detail.php' );
		if( is_callable( array( 'GFFormDetail', 'inline_scripts' ) ) ) {
			GFFormDetail::inline_scripts( $form );
		}

		GFFormSettings::page_footer();

	}

	function import_pricing_levels_page() {

		GFFormSettings::page_header( __( 'Pricing Rules Importer', 'gp-conditional-pricing' ) );

		$tmp_import_file = rgars( $_FILES, 'gpcp-import-file/tmp_name' );

		if( $tmp_import_file ) {
			$this->import_pricing_levels_disambiguation_page( $tmp_import_file );
		} else {
			$this->import_pricing_levels_start_page();
		}

		GFFormSettings::page_footer();

	}

	function import_pricing_levels_start_page() {
		?>

		<style type="text/css">
			#gpcp-importer { }
			#gpcp-importer label { display: block; }
			.gpcp-importer-mode-disambiguate { }
			#gpcp-import-file-input { padding: 20px; border: 1px solid #eee; border-radius: 5px; margin: 25px 0 20px; }
		</style>

		<form id="gpcp-importer" name="gpcp-importer" action="" method="post" enctype="multipart/form-data">

			<h3><span><i class="fa fa-cogs"></i> <?php esc_html_e( 'Import File', 'gp-conditional-pricing' ); ?></span></h3>

			<p class="instructions"><?php printf( __( 'Select a CSV file to import. Use this %ssample.csv%s file as an starting point for configuring your own import files.', 'gp-conditional-pricing' ), '<a href="' . $this->get_base_url() . '/assets/sample.csv">', '</a>' ); ?></p>

			<div id="gpcp-import-file-input">
				<label for="gpcp-import-file" style="display:none;"><?php esc_html_e( 'Import File', 'gp-conditional-pricing' ); ?></label>
				<input type="file" id="gpcp-import-file" name="gpcp-import-file" />
			</div>

			<div>
				<input type="submit" value="<?php esc_html_e( 'Prepare Import File' ); ?>" class="button button-primary" />
			</div>

		</form>

		<?php
	}

	function import_pricing_levels_disambiguation_page( $import_file ) {

		$disambiguation = $this->get_import_disambiguation( $import_file );
		$temp_import_path = $this->get_temp_import_path( $import_file );
		$form = GFAPI::get_form( rgget( 'id' ) );

		?>

		<style type="text/css">
			#gpcp-importer-disambiguation { }
			#gpcp-importer-disambiguation ul { }
			#gpcp-importer-disambiguation li span,
			#gpcp-importer-disambiguation li label { width: 35%; display: inline-block; }
			#gpcp-importer-disambiguation li span { font-weight: bold; }
			.gpcp-importer-products { border-top: 1px solid #eee; }
			.gpcp-importer-fields { border-bottom: 1px solid #eee; padding: 20px; margin-top: -1px; }
			.gpcp-importer-actions { margin-top: 20px; }
		</style>

		<form id="gpcp-importer-disambiguation" name="gpcp-importer-disambiguation" action="<?php echo remove_query_arg( 'tab' ); ?>" method="post" enctype="multipart/form-data">

			<h3><span><i class="fa fa-cogs"></i> <?php esc_html_e( 'Import File: Field Mappings', 'gp-conditional-pricing' ); ?></span></h3>

			<p class="instructions"><?php _e( 'Specify which form fields map to your import products, columns, and rows.', 'gp-conditional-pricing' ); ?></p>

			<?php wp_nonce_field( 'gw_conditional_pricing_save', 'gw_conditional_pricing_save', true ); ?>

			<div>
				<input type="hidden" id="gpcp-import-file" name="gpcp-import-file" value="<?php echo $temp_import_path; ?>" />
				<input type="hidden" id="gpcp-import-nonce" name="gpcp-import-nonce" value="<?php echo wp_create_nonce( 'gpcp-import-nonce' ); ?>" />
			</div>

			<ul class="gpcp-importer-products gpcp-importer-fields">
				<li>
					<span>Import Product</span>
					<span>Form Product</span>
				</li>
				<?php foreach( $disambiguation['products'] as $product ):
					$slug = sanitize_title_with_dashes( $product );
					?>
					<li>
						<label for="product_<?php echo $slug; ?>"><?php echo $product; ?></label>
						<select id="product_<?php echo $slug; ?>" name="product_<?php echo $slug; ?>">
							<?php foreach( self::get_products( $form ) as $_product ): ?>
								<option value="<?php echo $_product['id']; ?>" <?php selected( $product, $_product['label'] ); ?>><?php echo $_product['label']; ?></option>
							<?php endforeach; ?>
						</select>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php if( ! empty( $disambiguation['columns'] ) ): ?>
				<ul class="gpcp-importer-columns gpcp-importer-fields">
					<li>
						<span>Import Column</span>
						<span>Form Field</span>
					</li>
					<?php foreach( $disambiguation['columns'] as $index => $column ): ?>
						<li>
							<label for="column_<?php echo $index; ?>"><?php echo $column; ?></label>
							<select id="column_<?php echo $index; ?>" name="column_<?php echo $index; ?>">
								<?php foreach( $form['fields'] as $field ): ?>
									<option value="<?php echo $field->id; ?>" <?php selected( $column, $field['label'] ); ?>><?php echo $field->label; ?></option>
								<?php endforeach; ?>
							</select>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if( ! empty( $disambiguation['rows'] ) ): ?>
				<ul class="gpcp-importer-rows gpcp-importer-fields">
					<li>
						<span>Import Row</span>
						<span>Form Field</span>
					</li>
					<?php foreach( $disambiguation['rows'] as $index => $row ): ?>
						<li>
							<label for="row_<?php echo $index; ?>"><?php echo implode( ', ', array_slice( $row, 0, 3 ) ); ?></label>
							<select id="row_<?php echo $index; ?>" name="row_<?php echo $index; ?>">
								<?php foreach( $form['fields'] as $field ): ?>
									<option value="<?php echo $field->id; ?>"><?php echo $field->label; ?></option>
									<?php if( $field->get_input_type() == 'singleproduct' ): ?>
										<option value="quantity_<?php echo $field->id; ?>"><?php echo $field->label; ?> (<?php esc_html_e( 'Quantity' ); ?>)</option>
									<?php endif; ?>
								<?php endforeach; ?>
							</select>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<div class="gpcp-importer-actions">
				<input type="submit" value="<?php esc_html_e( 'Import Pricing Levels' ); ?>" class="button button-primary" />
			</div>

		</form>

		<?php
	}

	function pricing_rules_import_listener() {
		$import_file = rgpost( 'gpcp-import-file' );
		if( $import_file && wp_verify_nonce( rgpost( 'gpcp-import-nonce' ), 'gpcp-import-nonce' ) ) {
			$pricing_logic = $this->import_pricing_levels( $import_file );
			if( ! empty( $pricing_logic ) ) {
				$this->_import_pricing_logic = $pricing_logic;
			}
		}
	}

	function import_pricing_levels( $import_file ) {

		$pricing_logic = array();

		if ( ( $handle = fopen( $import_file, 'r' ) ) !== false ) {

			$row = 0;
			$fresh = true;
			$condition_columns = array();
			$column_conditions = array(); // Populated from condition rows.

			while ( ( $data = fgetcsv( $handle, 1000, ',' ) ) !== false ) {

				$row++;

				// Empty rows indicate a "fresh" group of pricing rules.
				if( $this->is_empty_array( $data ) ) {
					$fresh = true;
					continue;
				}
				// Rows with an empty first column indicate a condition row which generate column conditions.
				else if( empty( $data[0] ) ) {
					array_shift( $data ); // First row is always product.
					foreach( $data as $index => $value ) {
						// Make sure it is not a condition column.
						if( ! array_key_exists( $index, $condition_columns ) ) {

							if( ! isset( $column_conditions[ $index ] ) ) {
								$column_conditions[ $index ] = array();
							}

							$field_id = rgpost( "row_{$row}" );

							// Split on comma that is not preceeded (i.e. escaped) by a backslash.
							$values = preg_split( '/(?<!\\\),/', $value );

							foreach( $values as $_value ) {
								$column_conditions[ $index ][] = array(
									'fieldId'  => $field_id,
									'operator' => $this->get_import_operator( $_value ),
									// Backslash is used to escape reserved operators.
									'value'    => $this->get_import_value( $_value )
								);
							}

						}
					}
					continue;
				}
				// The next row after an empty row will always be a header row.
				else if( $fresh ) {
					$fresh = false;
					array_shift( $data ); // First row is always product.
					foreach( $data as $index => $column ) {
						if( ! empty( $column ) ) {
							$condition_columns[ $index ] = rgpost( "column_{$index}" );
						}
					}
					continue;
				}

				$product        = array_shift( $data );
				$product_slug   = sanitize_title_with_dashes( $product );
				$product_id     = rgpost( "product_{$product_slug}" );
				$row_conditions = array();

				if( ! isset( $pricing_logic[ $product_id ] ) ) {
					$pricing_logic[ $product_id ] = array();
				}

				foreach( $data as $index => $column ) {

					if( array_key_exists( $index, $condition_columns ) ) {
						$field_id = $condition_columns[ $index ];
						$row_conditions[] = array(
							'fieldId'  => $field_id,
							'operator' =>   'is', // @todo
							'value'    => $column
						);
						continue;
					}

					$pricing_logic[ $product_id ][] = array(
						'price'    => $column,
						'position' => '',
						'conditionalLogic' => array(
							'actionType' => 'show',
							'logicType'  => 'all',
							'rules'      => array_merge( $column_conditions[ $index ], $row_conditions ),
						)
					);

				}

			}

			fclose( $handle );

		}

		return $pricing_logic;
	}

	function get_import_disambiguation( $import_file ) {

		$disambiguation = array(
			'products' => array(),
			'rows'     => array(),
			'columns'  => array(),
		);

		if ( ( $handle = fopen( $import_file, 'r' ) ) !== false ) {

			$row = 1;
			$fresh = true;

			while ( ( $data = fgetcsv( $handle, 1000, ',' ) ) !== false ) {

				if( ! $fresh ) {

					// Empty rows should be skipped; during import this will signifiy a new set of import instructions.
					if( $this->is_empty_array( $data ) ) {
						$fresh = true;
						continue;
					}
					// Products will always be in the first column. Create an array of product names to be disambiguated.
					else if( ! empty( $data[0] ) && ! in_array( $data[0], $disambiguation['products'] ) ) {
						$disambiguation['products'][] = $data[0];
					}
					// First column will always be empty for condition rows.
					else if( empty( $data[0] ) ) {
						$disambiguation['rows'][ $row ] = array_filter( $data );
					}

				} else {

					$fresh = false;

					// Remove first column, it will always refer to Products.
					array_shift( $data );

					foreach( $data as $column ) {
						if( ! empty( $column ) ) {
							$disambiguation['columns'][] = $column;
						}
					}

				}

				$row++;

			}

			fclose( $handle );

		}

		return $disambiguation;
	}

	function get_temp_import_path( $import_file ) {

		$target_path = GFFormsModel::get_upload_path( rgget( 'id' ) ) . '/tmp/';
		if( $target_path == trailingslashit( dirname( $import_file ) ) ) {
			return $import_file;
		}

		wp_mkdir_p( $target_path );

		$path = $target_path . basename( $import_file );
		move_uploaded_file( $import_file, $path );

		return $path;
	}

	function verify_import_file( $file, $name ) {

		$verified_file_type = wp_check_filetype_and_ext( $file, $name, array( 'csv' => 'text/csv' ) );
		if( $verified_file_type['ext'] != 'csv' ) {
			return new WP_Error( 'Bad llama!', 'This is not a valid import file.' );
		}

		return true;
	}

	function get_import_count_notice( $counts ) {

		$notices = array(
			'ss' => __( '%d pricing level has been imported for %d product.', 'gp-limit-choices' ),
			'sp' => __( '%d pricing level has been imported for %d products.', 'gp-limit-choices' ),
			'ps' => __( '%d pricing levels have been imported for %d product.', 'gp-limit-choices' ),
			'pp' => __( '%d pricing levels have been imported for %d products.', 'gp-limit-choices' ),
		);

		$key  = $counts['pricing_levels'] > 1 ? 'p' : 's';
		$key .= $counts['products'] > 1       ? 'p' : 's';

		return rgar( $notices, $key );
	}

	function get_import_operator( $value ) {
		$operator = substr( $value, 0, 1 );
		$operator = rgar( $this->get_supported_import_operators(), $operator, 'is' );
		return $operator;
	}

	function get_import_value( $value ) {
		$first_char = substr( $value, 0, 1 );
		if( $first_char === '\\' || in_array( $first_char, array_keys( $this->get_supported_import_operators() ) ) ) {
			$value = substr( $value, 1, strlen( $value ) - 1 );
		}
		return $value;
	}

	function get_supported_import_operators() {
		return array(
			'!' => 'isnot',
			'<' => '<',
			'>' => '>',
			'*' => 'contains',
			'^' => 'starts_with',
			'$' => 'ends_with'
		);
	}

	function handle_conditional_pricing_save_submission() {

		if( ! isset( $_POST['gw_pricing_logic'] ) || ! check_admin_referer( 'gw_conditional_pricing_save', 'gw_conditional_pricing_save' ) ) {
			return false;
		}

		$pricing_logic = json_decode( rgpost( 'gw_pricing_logic' ), ARRAY_A );
		if( is_array( $pricing_logic ) ) {
			$pricing_logic = array_filter( $pricing_logic );
		}

		$form_id = gwget( 'id' );
		$form = GFFormsModel::get_form_meta( $form_id );

		// Check if there are actually changes in the submitted pricing logic
		$has_changes = !isset( $form['gw_pricing_logic'] ) || json_encode( $form['gw_pricing_logic'] ) != json_encode( $pricing_logic );

		// If there is an error parsing our logic JSON, show error.
		if( $pricing_logic === null ) {
			$result = false;
		}
		// If there are changes, save them
		else if( $has_changes ) {
			$form['gw_pricing_logic'] = $pricing_logic;
			$result = GFFormsModel::update_form_meta( $form_id, $form );
		}
		// If no changes, set result to true so success message is displayed
		else {
			$result = true;
		}

		if( $result ) {
			GFCommon::add_message( __( 'Conditional pricing saved successfully!', 'gp-conditional-pricing' ) );
		} else {
			GFCommon::add_error_message( __( 'There was an error saving your conditional pricing.', 'gp-conditional-pricing' ) );
		}

		// flush cached form meta so updated meta will be retrieved
		GFFormsModel::flush_current_forms();

		return $result;
	}

	function add_quantity_fields_to_conditional_field_select( $form ) {
		?>

		<script type="text/javascript">

			// ability to add support for
			//  - adding existing but not supported field types
			//  - adding new non-field-based fields (ie quantity)
			//  - disabling supported field types for specific conditional objects

			var allOptionsRule = {
				'fieldType': 'date', // string or function, function( field ) { }
				'operators': [ 'is', 'isnot', 'greater_than', 'less_than' ],
				'valueInput': '<input class="" />' // string or function
			};

			var dateFieldRule = {
				'fieldType': 'date', // string or function, function( field ) { }
				'operators': [ 'is', 'isnot', 'greater_than', 'less_than' ],
				'valueInput': function( field ) {
					return '<input class=""></input>';
				}
			};

			var quantityFieldRule = {
				'operators': [ 'is', 'isnot', 'greater_than', 'less_than' ],
				'valueInput': function( field, value ) {
					return '<input value=' + value + ' />';
				}
			}

		</script>

		<?php
		return $form;
	}

	function enqueue_form_scripts( $form ) {

		if( $this->has_pricing_logic( $form ) ) {
			wp_enqueue_script( "{$this->slug}-frontend" );
			wp_enqueue_script( 'gform_conditional_logic' );
		}

	}

	function register_init_script( $form, $field_values ) {

		if( ! self::has_pricing_logic( $form ) )
			return $form;

		$pricing_logic_json = json_encode( self::get_pricing_logic( $form ) );
		$base_prices_json = json_encode( self::get_base_prices( $form, $field_values ) );
		$script = "new GWConditionalPricing( {$form['id']}, {$pricing_logic_json}, {$base_prices_json} );";

		GFFormDisplay::add_init_script( $form['id'], $this->slug, GFFormDisplay::ON_PAGE_RENDER, $script );

	}

	function validate_submission( $validation_result ) {

		$form = $validation_result['form'];

		if( ! self::has_pricing_logic( $form ) ) {
			return $validation_result;
		}

		$entry = $this->get_current_entry();
		$product_info = GFCommon::get_product_fields( $form, $entry );

		$pricing_logic = self::get_pricing_logic( $form );

		// get the field ID of all pricing logic fields (could be individual inputs)
		$pricing_input_ids = array_keys( $pricing_logic );
		$pricing_field_ids = array_map( 'intval', $pricing_input_ids );

		// get_product_fields() sets meta value, remove it so future checks pull from current info not static cache
		gform_delete_meta( $entry['id'], 'gform_product_info' );

		// foreach field, if field is product, has pricing logic, and matching pricing rule
		foreach( $form['fields'] as &$field ) {

			$current_page = GFFormDisplay::get_source_page( $form['id'] );

			if( $field['type'] != 'product' || ! in_array( $field['id'], $pricing_field_ids ) || ( intval( $current_page ) !== 0 && $field->pageNumber > $current_page ) ) {
				continue;
			}

			$quantity = self::get_product_quantity( $field );

			// Re-process GF's quantity validation; if it's an invalid quantity, do not override the validation result
//			if( ! empty( $quantity ) && ( ! is_numeric( $quantity ) || intval( $quantity ) != floatval( $quantity ) || intval( $quantity ) < 0 ) ) {
//				continue;
//			}

			// If GF has failed validation for this field based on the Quantity, do not re-validated for GPCP.
			// Note: this will override the invalid value validation that happens after this is originally validated by GF.
			$field->validate( GFFormsModel::get_field_value( $field ), $form );
			if( in_array( $field->validation_message, array( esc_html__( 'This field is required.', 'gravityforms' ), esc_html__( 'Please enter a valid quantity', 'gravityforms' ) ) ) ) {
				continue;
			}

			// pass validation for any product without a quantity as it won't be included in the order anyways
			if( $quantity <= 0 || GFFormsModel::is_field_hidden( $form, $field, array() ) ) {
				$field['failed_validation'] = false;
				continue;
			}

			$input_id = $field['id'];
			if( in_array( $field->get_input_type(), array( 'select', 'multiselect', 'radio', 'checkbox' ) ) && ! rgempty( 'choices', $field )  ) {
				$value_bits = explode( '|', (string) GFFormsModel::get_field_value( $field ) );
				$value = $value_bits[0];
				foreach( $field['choices'] as $index => $choice ) {
					if( $choice['value'] == $value ) {
						$input_id = $field['id'] . '.' . ( $index + 1 );
						break;
					}
				}
			}

			// get product info for current field
			$product = $this->get_product_info_by_id( $field['id'], $product_info );
			if( ! $product )
				continue;

			$matched_pricing_level = false;

			// check for "product-specific" rules that only apply to the selected product of this field
			if( rgar( $pricing_logic, $input_id ) ) {
				$matched_pricing_level = $this->get_matching_pricing_level( $pricing_logic[$input_id], $form, /*$entry*/ null );
			}

			// if no matching specific rule found, check for "global" rules that apply to all products of this field
			if( ! $matched_pricing_level && rgar( $pricing_logic, $field['id'] ) ) {
				$matched_pricing_level = $this->get_matching_pricing_level( $pricing_logic[$field['id']], $form, /*$entry*/ null );
			}

			// if no matching pricing level is found, move on to next field
			if( ! $matched_pricing_level )
				continue;

			$prices_match = GFCommon::to_number( $matched_pricing_level['price'] ) == GFCommon::to_number( $product['price'] );

			if( ! $prices_match ) {

				$field['failed_validation'] = true;
				$field['validation_message'] = __( 'There was an error calculating the price for this field.', 'gp-conditional-pricing' );

			} else {

				$field['failed_validation'] = false;

			}

		}

		$validation_result['is_valid'] = GWPerk::is_form_valid( $form );
		$validation_result['form'] = $form;

		return $validation_result;
	}

	function get_current_entry() {

		require_once( GFCommon::get_base_path() . '/forms_model.php' );

		$gravitate_encryption_enabled = has_filter( 'gform_save_field_value', 'gds_encryption_gform_save_field_value' );
		if( $gravitate_encryption_enabled ) {
			remove_filter( 'gform_save_field_value', 'gds_encryption_gform_save_field_value' );
		}

		$entry = GFFormsModel::get_current_lead();

		if( $gravitate_encryption_enabled ) {
			add_filter( 'gform_save_field_value', 'gds_encryption_gform_save_field_value', 10, 4 );
		}

		return $entry;
	}

	function get_product_info_by_id( $field_id, $product_info ) {

		foreach( $product_info['products'] as $_field_id => $product ) {
			if( $field_id == $_field_id )
				return $product;
		}

		return false;
	}

	function get_matching_pricing_level( $pricing_logic, $form, $lead ) {
		foreach( $pricing_logic as $pricing_level ) {
			if( self::is_match( $form, $pricing_level, $lead ) )
				return $pricing_level;
		}
		return false;
	}

	function add_custom_qty_field_support( $pricing_logic ) {

		foreach( $pricing_logic as &$pricing_levels ) {
			foreach( $pricing_levels as &$pricing_level ) {
				foreach( $pricing_level['conditionalLogic']['rules'] as &$pricing_rule ) {

					preg_match( '/(quantity)_([0-9]+)/', $pricing_rule['fieldId'], $matches );
					if( !is_array( $matches ) )
						continue;

					list( $full_value, $tag, $field_id ) = array_pad( $matches, 3, '' );

					if( $field_id )
						$pricing_rule['fieldId'] = $field_id;

				}
			}
		}

		return $pricing_logic;
	}

	function get_product_quantity( $field ) {

		$form = GFFormsModel::get_form_meta( $field['formId'] );
		$lead = GFFormsModel::get_current_lead();
		$product_value = GFFormsModel::get_lead_field_value( $lead, $field );

		$qty_field = GFCommon::get_product_fields_by_type( $form, array( 'quantity' ), $field['id'] );
		$has_qty_field = !empty( $qty_field );

		if( $has_qty_field ) {
			$qty_field = $qty_field[0];
		}

		$is_qty_field_valid = $has_qty_field && !GFFormsModel::is_field_hidden( $form, $qty_field, array(), $lead );

		if( $is_qty_field_valid ) {
			$quantity = RGFormsModel::get_lead_field_value( $lead, $qty_field );
		} else
			// if 'singleproduct' (will have an array for the $product_value) and is using the built-in qty field
			if( is_array( $product_value ) && !rgar( $field, 'disableQuantity' ) ) {
				$quantity = rgar( $product_value, "{$field['id']}.3" );
			} else {
				// this should only happen if the field does not have a quantity field (of any kind)
				$quantity = 1;
			}

		return ! $quantity ? 0 : $quantity;
	}

	function preserve_conditional_pricing_rules( $current_form, $form_id, $meta_name ) {

		// only preserve if saving form from Form Editor or Form Settings; consider expanding this for any time the form is updated
		$is_form_editor_or_settings = GFCommon::is_form_editor() || GFForms::get_page() == 'form_settings';
		$is_display_meta            = $meta_name == 'display_meta';

		if( ! $is_form_editor_or_settings || ! $is_display_meta ) {
			return $current_form;
		}

		$saved_form = GFAPI::get_form( $form_id );
		if( ! isset( $saved_form['gw_pricing_logic'] ) ) {
			return $current_form;
		}

		if( json_encode( $current_form['gw_pricing_logic'] ) != json_encode( $saved_form['gw_pricing_logic'] ) ) {
			$current_form['gw_pricing_logic'] = $saved_form['gw_pricing_logic'];
		}

		return $current_form;
	}



	// EXPORT + IMPORT METHODS

	function modify_export_form( $form ) {

		if( ! isset( $form['gw_pricing_logic'] ) )
			return $form;

		$gw_pricing_logic = array();

		foreach( $form['gw_pricing_logic'] as $product_id => $pricing_logic ) {

			$product = array(
				'id' => $product_id,
				'pricing_levels' => $pricing_logic
			);

			$gw_pricing_logic[] = $product;

		}

		$form['gwcpProducts'] = $gw_pricing_logic;
		unset( $form['gw_pricing_logic'] );

		return $form;
	}

	function modify_export_options( $options ) {
		return array_merge( $options, array(
			'forms/form/gwcpProducts/gwcpProduct/id'                                                                => array( 'is_attribute' => true ),
			'forms/form/gwcpProducts/gwcpProduct/pricing_levels/pricing_level/price'                                => array( 'is_attribute' => true ),
			'forms/form/gwcpProducts/gwcpProduct/pricing_levels/pricing_level/conditionalLogic/actionType'          => array( 'is_attribute' => true ),
			'forms/form/gwcpProducts/gwcpProduct/pricing_levels/pricing_level/conditionalLogic/logicType'           => array( 'is_attribute' => true ),
			'forms/form/gwcpProducts/gwcpProduct/pricing_levels/pricing_level/conditionalLogic/rules/rule/fieldId'  => array( 'is_attribute' => true ),
			'forms/form/gwcpProducts/gwcpProduct/pricing_levels/pricing_level/conditionalLogic/rules/rule/operator' => array( 'is_attribute' => true ),
			'forms/form/gwcpProducts/gwcpProduct/pricing_levels/pricing_level/conditionalLogic/rules/rule/value'    => array( 'allow_empty'  => true )
		) );
	}

	function modify_imported_form( $meta, $form_id, $meta_name ) {

		$is_import_page           = GFForms::get_page() == 'import_form';
		$is_updating_display_meta = $meta_name == 'display_meta';
		$has_pricing_logic        = isset( $meta['gwcpProducts'] );

		if( ! $is_import_page || ! $is_updating_display_meta || ! $has_pricing_logic )
			return $meta;

		$form = $meta;
		$gw_pricing_logic = array();

		foreach( $form['gwcpProducts'] as $product ) {
			$gw_pricing_logic[$product['id']] = array_values( $product['pricing_levels'] );
		}

		$form['gw_pricing_logic'] = $gw_pricing_logic;
		unset( $form['gwcpProducts'] );

		return $form;
	}

	function modify_import_form_xml_options( $options ) {

		$options['gwcpProduct'] = array( 'unserialize_as_array' => true );
		$options['pricing_level'] = array( 'unserialize_as_array' => true );

		return $options;
	}



	// HELPER METHODS

	public static function has_product_field( $form ) {

		foreach( $form['fields'] as $field ) {
			if( GFCommon::is_product_field( $field['type'] ) && ! $field->adminOnly ) {
				return true;
			}
		}

		return false;
	}

	public static function get_products( $form, $field_values = array() ) {

		$products = array();

		foreach( $form['fields'] as $field ) {

			if( $field['type'] != 'product' )
				continue;

			$input_type = GFFormsModel::get_input_type( $field );

			if( in_array( $input_type, array( 'calculation', 'price' ) ) )
				continue;

			$base_price = rgar( GFFormsModel::get_field_value( $field, $field_values ), "{$field->id}.2", '' );
			if( $base_price === '' ) {
				$base_price = rgar( $field, 'basePrice', 0 );
			}

			// always add top-level product (single product or product group for multi-product fields)
			$products[$field['id']] = array(
				'id' => $field['id'],
				'label' => GFCommon::get_label( $field ),
				'basePrice' => $base_price,
			);

			// for multi-product fields, also add the individual products
			if( in_array( $input_type, array( 'radio', 'select' ) ) ) {

				foreach( $field['choices'] as $id => $choice ) {

					$id += 1;
					$input_id = $field['id'] . '.' . $id;
					$products[$input_id] = array(
						'id' => $input_id,
						'label' => $choice['text'] . ' (' . GFCommon::get_label( $field ) . ')',
						'basePrice' => rgar( $choice, 'price' )
					);

				}

			}

		}

		return $products;
	}

	public static function get_pricing_logic( $form ) {
		return rgar( $form, 'gw_pricing_logic' ) ? rgar( $form, 'gw_pricing_logic' ) : array();
	}

	public static function has_conditional_logic( $has_conditional_logic, $form ) {
		return $has_conditional_logic || self::has_pricing_logic( $form );
	}

	public static function has_pricing_logic( $form ) {
		$pricing_logic = self::get_pricing_logic( $form );
		return apply_filters( 'gpcp_has_pricing_logic', !empty( $pricing_logic ), $form );
	}

	public static function readable_pricing_rules( $conditional_logic, $form ) {

		$strings = array();

		$operator_labels = array(
			'is'           => __( 'is', 'gravityforms' ),
			'isnot'        => __( 'is not', 'gravityforms' ),
			'>'            => __( 'greater than', 'gravityforms' ),
			'<'            => __( 'less than', 'gravityforms' ),
			'contains'     => __( 'contains', 'gravityforms' ),
			'starts_with'  => __( 'starts with', 'gravityforms' ),
			'ends_with'    => __( 'ends with', 'gravityforms' )
		);

		foreach( $conditional_logic['rules'] as $rule ) {

			$rule_field_id = $rule['fieldId'];

			// check for custom quantity fields (for singleproduct fields)
			preg_match( '/(quantity)_([0-9]+)/', $rule_field_id, $matches );
			$is_custom_qty_field = !empty( $matches ) && $matches[1] == 'quantity';

			if( $is_custom_qty_field ) {
				$rule_field_id = $matches[2];
			}

			$field = GFFormsModel::get_field( $form, $rule_field_id );
			$field_label = strip_tags( GFCommon::truncate_middle( GFFormsModel::get_label( $field, $rule_field_id ), 50 ) );

			if( $is_custom_qty_field )
				$field_label .= ' (' . __( 'Quantity', 'gp-conditional-pricing' ) . ')';

			$strings[] = sprintf( '%s <em>%s</em> "%s"', $field_label, $operator_labels[ $rule['operator'] ], strip_tags( $rule['value'] ) );

		}

		return $strings;
	}

	public static function get_base_prices( $form, $field_values ) {

		$products = self::get_products( $form, $field_values );
		$base_prices = array();

		// @TODO: pretty sure this will only work for single products, multi-product fields will need to get basePrice differently, QA
		foreach( $products as $id => $product ) {
			$base_prices[$id] = $product['basePrice'];
		}

		return $base_prices;
	}

	public static function is_match( $form, $pricing_level, $lead ) {

		self::$current_form = $form;

		add_filter( 'gform_is_value_match', array( __class__, 'is_custom_quantity_value_match' ), 10, 6 );
		$is_match = self::get_field_display( $form, $pricing_level, array(), $lead ) == 'show';
		remove_filter( 'gform_is_value_match', array( __class__, 'is_custom_quantity_value_match' ) );

		self::$current_form = false;

		return $is_match;
	}

	public static function is_custom_quantity_value_match( $is_match, $field_value, $target_value, $operation, $source_field, $rule ) {

		// if a source field is passed, assume that this is not a custom quantity field
		if( ! empty( $source_field ) && $source_field->id != '' ) {
			return $is_match;
		}

		// check of our quantity_X tag
		preg_match( '/(quantity)_([0-9]+)/', $rule['fieldId'], $matches );
		if( ! is_array( $matches ) )
			return $is_match;

		list( $full_value, $tag, $field_id ) = array_pad( $matches, 3, '' );
		if( $tag != 'quantity' || ! $field_id )
			return $is_match;

		$form = self::$current_form;
		$source_field = GFFormsModel::get_field( $form, $field_id );
		$field_values = GFFormsModel::get_field_value( $source_field, array() );

		return GFFormsModel::matches_operation( $field_values["{$field_id}.3"], $target_value, $operation );
	}

	/**
	 * Port of private method from GFFormsModel
	 */
	public static function get_field_display( $form, $field, $field_values, $lead = null ) {

		if( version_compare( GFCommon::$version, '1.9.dev1', '>=' ) ) {
			return self::get_field_display_1_9( $form, (object) $field, $field_values, $lead );
		}

		$logic = rgar($field, "conditionalLogic");

		//if this field does not have any conditional logic associated with it, it won't be hidden
		if( empty( $logic ) )
			return "show";

		$match_count = 0;
		foreach($logic["rules"] as $rule){
			$source_field = GFFormsModel::get_field($form, $rule["fieldId"]);
			$field_value = empty($lead) ? GFFormsModel::get_field_value($source_field, $field_values) : GFFormsModel::get_lead_field_value($lead, $source_field);

			$is_value_match = GFFormsModel::is_value_match( $field_value, $rule["value"], $rule["operator"], $source_field, $rule );

			if($is_value_match)
				$match_count++;
		}

		$do_action = ($logic["logicType"] == "all" && $match_count == sizeof($logic["rules"]) ) || ($logic["logicType"] == "any"  && $match_count > 0);
		$is_hidden = ($do_action && $logic["actionType"] == "hide") || (!$do_action && $logic["actionType"] == "show");

		return $is_hidden ? "hide" : "show";
	}

	/**
	 * Port of private method from GFFormsModel (1.9 version)
	 */
	public static function get_field_display_1_9( $form, $field, $field_values, $lead = null ) {

		if ( empty( $field ) ) {
			return 'show';
		}

		$logic = $field->conditionalLogic;

		//if this field does not have any conditional logic associated with it, it won't be hidden
		if ( empty( $logic ) ) {
			return 'show';
		}

		$match_count = 0;
		foreach ( $logic['rules'] as $rule ) {
			$source_field = RGFormsModel::get_field( $form, $rule['fieldId'] );
			$field_value  = empty( $lead ) ? GFFormsModel::get_field_value( $source_field, $field_values ) : GFFormsModel::get_lead_field_value( $lead, $source_field );

			// if rule fieldId is input specific, get the specified input's value
			if( is_array( $field_value ) && isset( $field_value[ $rule['fieldId'] ] ) ) {
				$field_value = rgar( $field_value, $rule['fieldId'] );
			}

			$is_value_match = GFFormsModel::is_value_match( $field_value, $rule['value'], $rule['operator'], $source_field, $rule, $form );

			if ( $is_value_match ) {
				$match_count ++;
			}
		}

		$do_action = ( $logic['logicType'] == 'all' && $match_count == sizeof( $logic['rules'] ) ) || ( $logic['logicType'] == 'any' && $match_count > 0 );
		$is_hidden = ( $do_action && $logic['actionType'] == 'hide' ) || ( ! $do_action && $logic['actionType'] == 'show' );

		return $is_hidden ? 'hide' : 'show';
	}

	public function is_empty_array( $data ) {
		$data = array_filter( $data );
		return empty( $data );
	}



	function documentation() {
		return array(
			'type'  => 'url',
			'value' => 'http://gravitywiz.com/documentation/gp-conditional-pricing/'
		);
	}

}

class GWConditionalPricing extends GP_Conditional_Pricing { }