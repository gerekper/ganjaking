/* global woocommerce_free_gift_coupon_meta_i18n */

/**
 * Script for handling the coupon metabox UI.
 * 
 * @type {Object} JavaScript namespace for our application.
 */
let WC_Free_Gift_Coupons = {};

(function($, WC_Free_Gift_Coupons) {

	// Model.
	WC_Free_Gift_Coupons.Product = Backbone.Model.extend({
		defaults: {
			'quantity': 1,
			'title': '',
			'product_id' : '',
			'variation_id' : '',
			'gift_id' : ''
		},

		initialize: function() {
			if ( ! this.get( 'gift_id' ).length ) {
				this.set( 'gift_id', this.get( 'variation_id' ) > 0 ? this.get( 'variation_id' ) : this.get( 'product_id' ) );
			}
		},
	});

	// Collection.
	WC_Free_Gift_Coupons.ProductsCollection = Backbone.Collection.extend({
		model: WC_Free_Gift_Coupons.Product,
		el: '#wc-free-gift-container'
	});

	// Singular Row View.
	WC_Free_Gift_Coupons.productView = Backbone.View.extend({

		model: WC_Free_Gift_Coupons.Product,
		tagName: 'tr',

		events: {
			'click .delete-product': 'removeProduct',
			'change .product-quantity :input': 'setQuantity'
		},

		// Get the template from the DOM.
		template: wp.template( 'wc-free-gift-product' ),

		// Render the single model.
		render: function() {
			this.$el.html( this.template( this.model.toJSON() ) );
			return this;
		},

		// Reorder after sort.
		reorder: function( event, index ) {
			this.$el.trigger( 'update-sort', [this.model, index] );
		},

		// Remove/destroy a model.
		removeProduct: function(e) { 
			e.preventDefault();
			this.model.destroy();
			WC_Free_Gift_Coupons.productList.render(); // Manually calling instead of listening since listening interferes with sorting.
		},

		// Persist the quantity in the model.
		setQuantity: function() {
			let value = this.$el.find('.product-quantity :input').val();
			this.model.set('quantity', value);
		}

	});

	// List View.
	WC_Free_Gift_Coupons.productListTable = Backbone.View.extend({

		el: '#wc-free-gift-table',
		template: wp.template( 'wc-free-gift-products-table-header'),

		addSingle: function(model) {
			let view = new WC_Free_Gift_Coupons.productView({
				model: model
			});
			this.$el.find('tbody').prepend(view.render().el);
		},

		// Callback for SelectWoo section.
		addProduct: function( event, attributes) {
			let Product = new WC_Free_Gift_Coupons.Product(attributes);
			this.collection.add(Product, {at: 0} );
			this.render();
		},

		events: {
			'click .delete-product': 'removeProduct',
			'addFreeGiftProduct': 'addProduct',
		},
	 
		render: function() {

			this.$el.children().remove();

			if ( this.collection.length ) {

				this.$el.html(this.template());
			
				this.collection.each( function(data) {
					this.$el.find('tbody').append(new WC_Free_Gift_Coupons.productView({model : data}).render().el);
				}, this);

			}
	 
			return this;
		}

	});

	// Init.
	WC_Free_Gift_Coupons.initApplication = function() {

		// Create Collection From Existing Meta.
		WC_Free_Gift_Coupons.productCollection = new WC_Free_Gift_Coupons.ProductsCollection(woocommerce_free_gift_coupon_meta_i18n.free_gifts);

		// Create the List View.
		WC_Free_Gift_Coupons.productList = new WC_Free_Gift_Coupons.productListTable({
			collection: WC_Free_Gift_Coupons.productCollection
		});

		// Render the List View.
		WC_Free_Gift_Coupons.productList.render();

	};

	/*-----------------------------------------------------------------------------------*/
	/* Execute the above methods in the WC_Free_Gift_Coupons object.
	/*-----------------------------------------------------------------------------------*/

	jQuery(function($) {

		WC_Free_Gift_Coupons.initApplication();

		$( '#wc-free-gift-table' ).sortable({
			items: 'tbody > tr',
			handle: '.product-title',
			axis: 'y',
			opacity: 0.5,
			grid: [20, 10],
			tolerance: 'pointer'
		});

		$( document.body ).trigger( 'wc-enhanced-select-init' );

		if ( $( '#free_gift_ids' ).hasClass( 'select2-hidden-accessible' ) ) {

			$( '#free_gift_ids' ).on( 'select2:select', function ( e ) { 
				let data = e.params.data;
				
				if ( data.id.length ) {

					let new_gift = {
						gift_id: data.id,
						title: data.text,
					};

					$( '#wc-free-gift-table' ).trigger( 'addFreeGiftProduct', new_gift );

					// Keep values out of enhanced select container.
					$( this ).val([]).trigger('change');

					$('#wc-free-gift-table').sortable('refresh');

				}
			});
		}
	
		// Toggle coupon type options.
		$( 'select#discount_type' ).change(function(){

			// Get value.
			let select_val = $(this).val();

			let $toggle_fields = $( '.coupon_amount_field' );

			let coupon_types = jQuery.parseJSON( woocommerce_free_gift_coupon_meta_i18n.coupon_types );

			// Check if coupon type is in supported type list.
			if ( $.inArray( select_val, coupon_types ) !== -1 ) {
				$( '.show_if_free_gift' ).show();

				// Only hide the price field for Free Gift type
				if ( 'free_gift' === select_val ) {
					$toggle_fields.hide();
				} else {
					$toggle_fields.show();
				}
			} else {
				$( '.show_if_free_gift' ).hide();
				$toggle_fields.show();
			}

		}).trigger('change');
	});

})(jQuery, WC_Free_Gift_Coupons);
