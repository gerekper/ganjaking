( function( $ ) {

	var RegisterUAELQuickView = function( $scope, $ ) {

		var scope_id 		= $scope.data( 'id' );
		var modal_wrap 		= $scope.find('.uael-quick-view-' + scope_id );

		modal_wrap.appendTo( document.body );

		var uael_quick_view_bg    	= modal_wrap.find( '.uael-quick-view-bg' ),
			uael_qv_modal    		= modal_wrap.find( '#uael-quick-view-modal' ),
			uael_qv_content  		= uael_qv_modal.find( '#uael-quick-view-content' ),
			uael_qv_close_btn 		= uael_qv_modal.find( '#uael-quick-view-close' ),
			uael_qv_wrapper  		= uael_qv_modal.find( '.uael-content-main-wrapper');

		$scope
			.off( 'click', '.uael-quick-view-btn' )
			.on( 'click', '.uael-quick-view-btn', function( e ) {
				e.preventDefault();

				var $this       = $(this);
				var product_id  = $this.data( 'product_id' );

				if( ! uael_qv_modal.hasClass( 'loading' ) ) {
					uael_qv_modal.addClass('loading');
				}

				if ( ! uael_quick_view_bg.hasClass( 'uael-quick-view-bg-ready' ) ) {
					uael_quick_view_bg.addClass( 'uael-quick-view-bg-ready' );
				}

				$(document).trigger( 'uael_quick_view_loading' );

				uael_qv_ajax_call( $this, product_id );
			});

		$scope
			.off( 'click', '.uael-quick-view-data' )
			.on( 'click', '.uael-quick-view-data', function( e ) {
				e.preventDefault();
				var $this       = $(this);
				var product_id  = $this.data( 'product_id' );

				if( ! uael_qv_modal.hasClass( 'loading' ) ) {
					uael_qv_modal.addClass('loading');
				}

				if ( ! uael_quick_view_bg.hasClass( 'uael-quick-view-bg-ready' ) ) {
					uael_quick_view_bg.addClass( 'uael-quick-view-bg-ready' );
				}

				$(document).trigger( 'uael_quick_view_loading' );

				uael_qv_ajax_call( $this, product_id );
			});

		var uael_qv_ajax_call = function( t, product_id ) {

			uael_qv_modal.css( 'opacity', 0 );

			$.ajax({
	            url: uael_wc_script.ajax_url,
				data: {
					action: 'uael_woo_quick_view',
					product_id: product_id,
					nonce: uael_wc_script.quick_view_nonce,
				},
				dataType: 'html',
				type: 'POST',
				success: function (data) {
					uael_qv_content.html(data);
					uael_qv_content_height();
				}
			});
		};

		var uael_qv_content_height = function() {

			// Variation Form
			var form_variation = uael_qv_content.find('.variations_form');

			form_variation.trigger( 'check_variations' );
			form_variation.trigger( 'reset_image' );

			if (!uael_qv_modal.hasClass('open')) {

				uael_qv_modal.removeClass('loading').addClass('open');

				var scrollbar_width = uael_get_scrollbar_width();
				var $html = $('html');

				$html.css( 'margin-right', scrollbar_width );
				$html.addClass('uael-quick-view-is-open');
			}

			var var_form = uael_qv_modal.find('.variations_form');
			if ( var_form.length > 0 && 'function' === typeof var_form.wc_variation_form) {
				var_form.wc_variation_form();
				var_form.find('select').change();
			}

			uael_qv_content.imagesLoaded( function( e ) {

				var image_slider_wrap = uael_qv_modal.find('.uael-qv-image-slider');

				if ( image_slider_wrap.find('li').length > 1 ) {
					image_slider_wrap.flexslider({
						animation: "slide",
						start: function( slider ) {
							setTimeout(function() {
								uael_update_summary_height( true );
							}, 300);
						},
					});
				}else{
					setTimeout(function() {
						uael_update_summary_height( true );
					}, 300);
				}
			});

			// stop loader
			$(document).trigger('uael_quick_view_loader_stop');
		};

		var uael_qv_close_modal = function() {

			// Close box by click overlay
			uael_qv_wrapper.on( 'click', function( e ) {

				if ( this === e.target ) {
					uael_qv_close();
				}
			});

			// Close box with esc key

			$( document ).on( 'keyup', function(e) {

				if( e.keyCode === 27 ) {
					uael_qv_close();
				}
			});

			// Close box by click close button
			uael_qv_close_btn.on( 'click', function( e ) {
				e.preventDefault();
				uael_qv_close();
			});

			var uael_qv_close = function() {
				uael_quick_view_bg.removeClass( 'uael-quick-view-bg-ready' );
				uael_qv_modal.removeClass('open').removeClass('loading');
				$('html').removeClass('uael-quick-view-is-open');
				$('html').css( 'margin-right', '' );

				setTimeout(function () {
					uael_qv_content.html('');
				}, 600);
			}
		};

		var uael_update_summary_height = function( update_css ) {
			var quick_view = uael_qv_content,
				img_height = quick_view.find( '.product .uael-qv-image-slider' ).first().height(),
				summary    = quick_view.find('.product .summary.entry-summary'),
				content    = summary.css('content');

			if ( 'undefined' != typeof content && 544 == content.replace( /[^0-9]/g, '' ) && 0 != img_height && null !== img_height ) {
				summary.css('height', img_height );
			} else {
				summary.css('height', '' );
			}

			if ( true === update_css ) {
				uael_qv_modal.css( 'opacity', 1 );
			}
		};

		var uael_get_scrollbar_width = function () {

			var div = $('<div style="width:50px;height:50px;overflow:hidden;position:absolute;top:-200px;left:-200px;"><div style="height:100px;"></div>');
			// Append our div, do our calculation and then remove it
			$('body').append(div);
			var w1 = $('div', div).innerWidth();
			div.css('overflow-y', 'scroll');
			var w2 = $('div', div).innerWidth();
			$(div).remove();

			return (w1 - w2);
		}


		uael_qv_close_modal();
		//uael_update_summary_height();

		window.addEventListener("resize", function(event) {
			uael_update_summary_height();
		});

		/* Add to cart ajax */
		/**
		 * uael_add_to_cart_ajax class.
		 */
		var uael_add_to_cart_ajax = function() {

			modal_wrap
				.off( 'click', '#uael-quick-view-content .single_add_to_cart_button' )
				.off( 'uael_added_to_cart' )
				.on( 'click', '#uael-quick-view-content .single_add_to_cart_button', this.onAddToCart )
				.on( 'uael_added_to_cart', this.updateButton );
		};

		/**
		 * Handle the add to cart event.
		 */
		uael_add_to_cart_ajax.prototype.onAddToCart = function( e ) {

			e.preventDefault();

			var $form = $(this).closest('form');

			// If the form inputs are invalid
			if( ! $form[0].checkValidity() ) {
				$form[0].reportValidity();
				return false;
			}

			var $thisbutton = $( this ),
				product_id = $(this).val(),
				variation_id = $('input[name="variation_id"]').val() || '';

			// Set Quantity.
			//
			// For grouped product quantity should be array instead of single value
			// For that set the quantity as array for grouped product.
			var quantity = $('input[name="quantity"]').val();
			if( $scope.find('.woocommerce-grouped-product-list-item' ).length )
			{
				var quantities = $('input.qty'),
					quantity   = [];
				$.each(quantities, function(index, val) {

					var name = $( this ).attr( 'name' );

					name = name.replace('quantity[','');
					name = name.replace(']','');
					name = parseInt( name );

					if( $( this ).val() ) {
						quantity[ name ] = $( this ).val();
					}
				});
			}

			var cartFormData = $form.serialize();

			if ( $thisbutton.is( '.single_add_to_cart_button' ) ) {

				$thisbutton.removeClass( 'added' );
				$thisbutton.addClass( 'loading' );

				// Ajax action.
				if ( variation_id != '') {
					jQuery.ajax ({
						url: uael_wc_script.ajax_url,
						type:'POST',
						data:'action=uael_add_cart_single_product&product_id=' + product_id  + '&nonce=' + uael_wc_script.add_cart_nonce + '&'+ cartFormData,
						success:function(results) {
							// Trigger event so themes can refresh other areas.
							$( document.body ).trigger( 'wc_fragment_refresh' );
							modal_wrap.trigger( 'uael_added_to_cart', [ $thisbutton ] );
						}
					});
				} else {
					jQuery.ajax ({
						url: uael_wc_script.ajax_url,
						type:'POST',
						data:'action=uael_add_cart_single_product&product_id=' + product_id  + '&nonce=' + uael_wc_script.add_cart_nonce + '&'+ cartFormData,
						success:function(results) {
							// Trigger event so themes can refresh other areas.
							$( document.body ).trigger( 'wc_fragment_refresh' );
							modal_wrap.trigger( 'uael_added_to_cart', [ $thisbutton ] );
						}
					});
				}
			}
		};

		/**
		 * Update cart page elements after add to cart events.
		 */
		uael_add_to_cart_ajax.prototype.updateButton = function( e, button ) {
			button = typeof button === 'undefined' ? false : button;

			if ( $(button) ) {
				$(button).removeClass( 'loading' );
				$(button).addClass( 'added' );

				// View cart text.
				if ( ! uael_wc_script.is_cart && $(button).parent().find( '.added_to_cart' ).length === 0  && uael_wc_script.is_single_product) {
					$(button).after( ' <a href="' + uael_wc_script.cart_url + '" class="added_to_cart wc-forward" title="' +
						uael_wc_script.view_cart + '">' + uael_wc_script.view_cart + '</a>' );
				}


			}
		};

		/**
		 * Init uael_add_to_cart_ajax.
		 */
		new uael_add_to_cart_ajax();
	}

	var RegisterUAELAddCart = function( $scope, $ ) {

		$layout = $scope.data('widget_type');

		if ( 'uael-woo-products.grid-franko' !== $layout && 'uael-woo-products-slider.slider-franko' !== $layout ) {
			return;
		}

		/* Add to cart for styles */
		var style_add_to_cart = function() {

			//fa-spinner
			$( document.body )
				.off( 'click', '.uael-product-actions .uael-add-to-cart-btn.product_type_simple' )
				.off( 'uael_product_actions_added_to_cart' )
				.on( 'click', '.uael-product-actions .uael-add-to-cart-btn.product_type_simple', this.onAddToCart )
				.on( 'uael_product_actions_added_to_cart', this.updateButton );
		};

		/**
		 * Handle the add to cart event.
		 */
		style_add_to_cart.prototype.onAddToCart = function( e ) {

			e.preventDefault();

			var $thisbutton = $(this),
				product_id 	= $thisbutton.data('product_id'),
				quantity 	= 1;

			$thisbutton.removeClass( 'added' );
			$thisbutton.addClass( 'loading' );

			jQuery.ajax ({
				url: uael_wc_script.ajax_url,
				type:'POST',
				data: {
					action: 'uael_add_cart_single_product',
					product_id : product_id,
					quantity: quantity,
					nonce: uael_wc_script.add_cart_nonce,
				},

				success:function(results) {
					// Trigger event so themes can refresh other areas.
					$( document.body ).trigger( 'wc_fragment_refresh' );
					$( document.body ).trigger( 'uael_product_actions_added_to_cart', [ $thisbutton ] );
				}
			});
		};

		/**
		 * Update cart page elements after add to cart events.
		 */
		style_add_to_cart.prototype.updateButton = function( e, button ) {
			button = typeof button === 'undefined' ? false : button;

			if ( $(button) ) {
				$(button).removeClass( 'loading' );
				$(button).addClass( 'added' );
			}
		};

		/**
		 * Init style_add_to_cart.
		 */
		new style_add_to_cart();
	}

	/**
	 * Function for Product Categories.
	 *
	 */
	var WidgetUAELWooCategories = function( $scope, $ ) {
		if ( 'undefined' == typeof $scope ) {
			return;
		}
		var cat_slider 	= $scope.find('.uael-woo-categories-slider');

		if ( cat_slider.length > 0 ) {
			var slider_selector = cat_slider.find('ul.products'),
				slider_options 	= JSON.parse( cat_slider.attr('data-cat_slider') );

			WidgetUAELSlickCall( slider_selector, slider_options );
		}
	}

	/**
	 * Function for Product Grid.
	 *
	 */
	var WidgetUAELWooProducts = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope ) {
			return;
		}

		/* Slider */
		var slider_wrapper 	= $scope.find('.uael-woo-products-slider');

		if ( slider_wrapper.length > 0 ) {
			var slider_selector = slider_wrapper.find('ul.products'),
				slider_options 	= JSON.parse( slider_wrapper.attr('data-woo_slider') );

			WidgetUAELSlickCall( slider_selector, slider_options );

		}

		if ( ! elementorFrontend.isEditMode()  ) {
			/* Common */
			RegisterUAELQuickView( $scope, $ );
			/* Style specific cart button */
			RegisterUAELAddCart( $scope, $ );
		}

	}

	/**
	 * Function for slick call.
	 *
	 */
	 var WidgetUAELSlickCall = function( slider_selector, slider_options ) {

		// Slick slider has issue with the tablet breakpoint. So, we are managing this with a tweak.
		var tablet_breakpoint = ( slider_options.responsive && 'undefined' !== slider_options.responsive[0].breakpoint ) ? slider_options.responsive[0].breakpoint : '';

		if( tablet_breakpoint == $( window ).width() ) {
			slider_options.mobileFirst = true;
		}

		slider_selector.slick( slider_options );

		window.addEventListener( "resize", function( event ) {
			slider_selector.slick( 'resize' );
		});

	}

	var UAELAjaxAddToCart = function( $scope, $ ) {

		$layout	= $scope.data( 'widget_type' );

		if ( 'uael-woo-add-to-cart.default' !== $layout ) {
			return;
		}
		var uael_atc_call = $scope.find( '.uael-add-to-cart' );

		var uael_ajax_add_to_cart = function() {

			$( document.body )
				.off( 'click', '.uael-add-to-cart .single_add_to_cart_button' )
				.off( 'uael_woo_added_to_cart' )
				.on( 'click', '.uael-add-to-cart .single_add_to_cart_button', this.onAddToCart )
				.on( 'uael_woo_added_to_cart', this.updateButton );
		};

		/**
		 * Handle the add to cart event.
		 */
		uael_ajax_add_to_cart.prototype.onAddToCart = function( e ) {

			e.preventDefault();

			var $form = $( this ).closest( 'form' );

			// If the form inputs are invalid
			if( ! $form[0].checkValidity() ) {
				$form[0].reportValidity();
				return false;
			}

			var $thisbutton = $( this ),
				product_id = $( this ).val();

			// Set Quantity.
			//
			// For grouped product quantity should be array instead of single value
			// For that set the quantity as array for grouped product.
			var quantity = $( 'input[name="quantity"]' ).val();
			if( $scope.find( '.woocommerce-grouped-product-list-item' ).length )
			{
				var quantities = $( 'input.qty' ),
					quantity   = [];
				$.each( quantities, function( index, val ) {

					var name = $( this ).attr( 'name' );

					name = name.replace( 'quantity[', '' );
					name = name.replace( ']', '' );
					name = parseInt( name );

					if( $( this ).val() ) {
						quantity[ name ] = $( this ).val();
					}
				});
			}

			var cartFormData = $form.serialize();
			var variation_id = $('input[name="variation_id"]').val() || '';

			if ( $thisbutton.is( '.single_add_to_cart_button' ) ) {

				if(variation_id != 0){
					$thisbutton.removeClass( 'added' );
					$thisbutton.addClass( 'loading' );
					// Ajax action.
					jQuery.ajax ({
						url: uael_wc_script.ajax_url,
						type:'POST',
						data:'action=uael_add_cart_single_product&product_id=' + product_id  + '&nonce=' + uael_wc_script.add_cart_nonce + '&'+ cartFormData,
						success:function( results ) {
							// Trigger event so themes can refresh other areas.
							$( document.body ).trigger( 'wc_fragment_refresh' );
							uael_atc_call.trigger( 'uael_woo_added_to_cart', [ $thisbutton ] );
						}
					});
				}
			}
		};

		/**
		 * Update cart page elements after add to cart events.
		 */
		uael_ajax_add_to_cart.prototype.updateButton = function( e, button ) {
			button = typeof button === 'undefined' ? false : button;

			if ( $( button ) ) {
				$( button ).removeClass( 'loading' );
				$( button ).addClass( 'added' );
			}
		};


		/**
		 * Init uael_ajax_add_to_cart.
		 */
		new uael_ajax_add_to_cart();
	}


	/**
	 * Function for Product Grid.
	 *
	 */
	var WidgetUAELWooAddToCart = function( $scope, $ ) {

		var enable_single_product_page = $scope.find( '.uael-add-to-cart' ).data( 'enable-feature' );

		$( 'body' ).off( 'added_to_cart.uael_cart' ).on( 'added_to_cart.uael_cart', function( e, fragments, cart_hash, btn ) {

			if ( btn && btn.closest( '.elementor-widget-uael-woo-add-to-cart' ).length > 0 ) {

				if ( btn.hasClass( 'uael-redirect' ) ) {

					setTimeout( function() {
						// View cart text.
						if ( ! uael_wc_script.is_cart && btn.hasClass( 'added' ) ) {
							window.location = uael_wc_script.cart_url;
						}
					}, 200 );
				}
			}
		});

		if ( ! elementorFrontend.isEditMode() && 'yes' === enable_single_product_page ) {
			UAELAjaxAddToCart( $scope, $ );
		}
	}

	$( document )
	.off( 'click', '.uael-woocommerce-pagination a.page-numbers' )
	.on( 'click', '.uael-woocommerce-pagination a.page-numbers', function( e ) {

		$scope = $( this ).closest( '.elementor-widget-uael-woo-products' );

		if ( $scope.find( '.uael-woocommerce' ).hasClass( 'uael-woo-query-main' ) ) {
			return;
		}

		e.preventDefault();

		$scope.find( 'ul.products' ).after( '<div class="uael-woo-loader"><div class="uael-loader"></div><div class="uael-loader-overlay"></div></div>' );

		var node =$scope.data( 'id' );

		var page_id = $scope.find( '.uael-woocommerce' ).data('page');
		var page_number = 1;
		var curr = parseInt( $scope.find( '.uael-woocommerce-pagination .page-numbers.current' ).html() );
		var skin = $scope.find( '.uael-woocommerce' ).data( 'skin' );

		if ( $( this ).hasClass( 'next' ) ) {
			page_number = curr + 1;
		} else if ( $( this ).hasClass( 'prev' ) ) {
			page_number = curr - 1;
		} else {
			page_number = $( this ).html();
		}

		$.ajax({
			url: uael_wc_script.ajax_url,
			data: {
				action: 'uael_get_products',
				page_id : page_id,
				widget_id: $scope.data( 'id' ),
				category: '',
				skin: skin,
				page_number : page_number,
				nonce : uael_wc_script.get_product_nonce,
			},
			dataType: 'json',
			type: 'POST',
			success: function ( data ) {

				$scope.find( '.uael-woo-loader' ).remove();

				$('html, body').animate({
					scrollTop: ( ( $scope.find( '.uael-woocommerce' ).offset().top ) - 30 )
				}, 'slow');

				var sel = $scope.find( '.uael-woo-products-inner ul.products' );

				sel.replaceWith( data.data.html );
				$scope.find( '.uael-woocommerce-pagination' ).replaceWith( data.data.pagination );

				$( window ).trigger( 'uael_woocommerce_after_pagination', [ page_id, node ] );
			}
		});

	} );

	var WidgetUAELMiniCart = function($scope, $) {

		var miniCartButton   = $scope.find( '.uael-mc__btn' );
		var cartBtnBehaviour = $scope.find( '.uael-mc__btn' ).data( 'behaviour' );
		var modal_open = $scope.find( '.uael-mc-modal-wrap' );
		var offcanvas_open = $scope.find( '.uael-mc-offcanvas-wrap' );
		var dropdown_main = $scope.find( '.uael-mc-dropdown' );
		var modal_main = $scope.find( '.uael-mc-modal' );
		var offcanvas_main = $scope.find( '.uael-mc-offcanvas' );
		var is_preview_enabled = $scope.hasClass( 'elementor-element-edit-mode' ) && $scope.hasClass( 'uael-mini-cart--preview-yes' );
		var cart_dropdown_style = $scope.find( '.uael-mc' ).data( 'cart_dropdown' );

		if( is_preview_enabled ) {

			dropdown_main.removeClass( 'uael-mc-dropdown-close' );

			modal_open.removeClass( 'uael-mc-modal-wrap-close' );
			modal_main.removeClass( 'uael-mc-modal-close' );

			offcanvas_open.removeClass( 'uael-mc-offcanvas-wrap-close' );
			offcanvas_main.removeClass( 'uael-mc-offcanvas-close' );
		}

		miniCartButton.on( 'click', function( e ) {

			e.preventDefault();
			if( 'click' === cartBtnBehaviour ) {

				if ( 'dropdown' == cart_dropdown_style ) {

					dropdown_main.toggleClass( 'uael-mc-dropdown-close' );
					e.stopPropagation();
				}
			}

			if( 'modal' == cart_dropdown_style ) {

				modal_open.removeClass( 'uael-mc-modal-wrap-close' );
				modal_main.removeClass( 'uael-mc-modal-close' );

				$( document ).on( 'click', '.uael-mc-modal-wrap, .uael-mc-modal__close-btn', function() {

					var $this = $( this ).closest( '.uael-mc' );

					$this.find( '.uael-mc-modal-wrap' ).addClass( 'uael-mc-modal-wrap-close' );
					$this.find( '.uael-mc-modal' ).addClass( 'uael-mc-modal-close' );
				} );
			}

			if( 'offcanvas' == cart_dropdown_style ) {

				offcanvas_open.removeClass( 'uael-mc-offcanvas-wrap-close' );
				offcanvas_main.removeClass( 'uael-mc-offcanvas-close' );

				$( document ).on( 'click', '.uael-mc-offcanvas-wrap, .uael-mc-offcanvas__close-btn', function() {

					var $this = $( this ).closest( '.uael-mc' );

					$this.find( '.uael-mc-offcanvas-wrap' ).addClass( 'uael-mc-offcanvas-wrap-close' );
					$this.find( '.uael-mc-offcanvas' ).addClass( 'uael-mc-offcanvas-close' );
				} );
			}
		});

		if( 'hover' === cartBtnBehaviour ) {

			if( ! is_preview_enabled ) {

				miniCartButton.hover( function( e ) {

					e.preventDefault();
					if( 'dropdown' == cart_dropdown_style ) {

						dropdown_main.removeClass( 'uael-mc-dropdown-close' );
					}
				}, function( e ) {

					e.preventDefault();
					if( 'dropdown' == cart_dropdown_style ) {

						dropdown_main.addClass( 'uael-mc-dropdown-close' );
					}
				});

				dropdown_main.hover( function( e ) {

					e.preventDefault();
					dropdown_main.removeClass( 'uael-mc-dropdown-close' );
				}, function( e ) {

					e.preventDefault();
					dropdown_main.addClass( 'uael-mc-dropdown-close' );
				});
			}
		}

		$( document ).on( 'keyup', function( e ) {

			if ( 27 == e.keyCode ) {

				$( '.elementor-widget-uael-mini-cart' ).each( function() {

					var $this = $( this );

					$this.find( '.uael-mc-modal-wrap' ).addClass( 'uael-mc-modal-wrap-close' );
					$this.find( '.uael-mc-modal' ).addClass( 'uael-mc-modal-close' );

					$this.find( '.uael-mc-offcanvas-wrap' ).addClass( 'uael-mc-offcanvas-wrap-close' );
					$this.find( '.uael-mc-offcanvas' ).addClass( 'uael-mc-offcanvas-close' );
				} );
			}
		});

		dropdown_main.on( 'click', function( e ) {

			if( 'A' == e.target.nodeName && 'remove remove_from_cart_button' == $( e.target ).attr( 'class' ) ) {

				$( this ).removeClass( 'uael-mc-dropdown-close' );

				return;
			}
			e.stopPropagation();
		});

		$( document ).on( 'click', function( e ) {

			if( 'A' != e.target.nodeName && 'remove remove_from_cart_button' != $( e.target ).attr( 'class' ) ) {

				dropdown_main.addClass( 'uael-mc-dropdown-close' );
				e.stopPropagation();
			}
		});
	};

	var WidgetUAELCheckout = function ($scope, $) {


		var tabs_wrapper = $scope.find('#uael_multistep_container');
		var tabs = $scope.find('#uael-tabs');
		var tab_panels = $scope.find('#uael-tab-panels');
		var first_step = 0;
		var last_step = 0;

		var button_prev = $scope.find('#action-prev');
		var button_next = $scope.find('#action-next');

		var active_step = 1;

		var uael_script_var = true;
		var validation_msg = uael_woo_chekout.validation_msg;
		var woo_checkout = $scope.find('.uael-woo-checkout');

		var widget_data = {};


		function render_order_review(){

			widget_data['widget_id'] = $scope.data('id');
			widget_data['page_id'] = woo_checkout.data('page-id');

			setTimeout( function () {
				$(".uael-checkout-review-order-table").addClass("processing").block( {
			        message: null,
			        overlayCSS: {
			          background: "#fff",
			          opacity: 0.6
				    }
				});

			    jQuery.ajax({
			        type: 'POST',
			        url: uael_wc_script.ajax_url,
			        data: {
			          action: "uae_woo_checkout_update_order_review",
					  content: widget_data,
					  nonce: uael_wc_script.checkout_update_nonce,
			        },
				    success: function success(data) {

					    $(".uael-checkout-review-order-table").replaceWith(data.order_review);
					    setTimeout(function () {
						    $(".uael-checkout-review-order-table").removeClass("uael-order-review-processing blockUI blockOverlay");
						}, 100000);
					}
				});
			}, 2000 );
		}

		$( document ).on( 'click', '.uael-woo-checkout-order-review .woocommerce-remove-coupon', function (e) {
		    render_order_review();
		});

 		$( '.uael-woo-checkout-coupon form.checkout_coupon' ).submit( function ( event ) {
		    render_order_review();
		});

		UaelCheckout = {
			_initialize_uael_checkout: function () {
				if( tabs_wrapper.length ) {
					var first_step_tab = tabs.find( 'li.uael-tab a.first' );
					first_step = first_step_tab.data( 'step' );
					last_step = tabs.find( 'li.uael-tab a.last' ).data( 'step' );

					UaelCheckout._jump_to_step( first_step, first_step_tab );

					tabs.find( 'li.uael-tab a' ).on( 'click', function() {
						var $this = $( this );
						var step_number = $this.data( 'step' );
						if( step_number < active_step ) {
							UaelCheckout._jump_to_step( step_number, $this );
						}
					});

					button_prev.on( 'click', function() {
						var step_number = active_step - 1;
						if( step_number >= first_step ) {
							UaelCheckout._jump_to_step( step_number, false );
							UaelCheckout._scroll_to_top();
						}
					});

					button_next.on( 'click', function() {
						var step_number = active_step + 1;
						if( step_number <= last_step ) {
							if( uael_script_var ) {
								UaelCheckout._validate_checkout_step( active_step, step_number );
							}else{
								UaelCheckout._jump_to_step( step_number, false );
								UaelCheckout._scroll_to_top();
							}

						}
					});
				}
			},
			_validate_checkout_step: function ( active_step, next_step ) {
				var valid = UaelCheckout._validate_step_fields( active_step );

				if( valid ) {
					tabs.find( '#step-' + active_step ).addClass( 'uael-finished-step' );

					UaelCheckout._jump_to_step( next_step, false );
					UaelCheckout._scroll_to_top();
				}else{
					UaelCheckout._display_error_message();
					UaelCheckout._scroll_to_error();
				}
			},
			_scroll_to_error: function () {
				var error_class = $scope.find( '#uael_multistep_container .woocommerce-error' );
				$scope.find( 'html, body' ).animate({ scrollTop:( error_class.offset().top - 100 ) }, 1000 );
			},
			_display_error_message: function () {
				var error_msg = validation_msg;
				var error = '<ul class="woocommerce-error" role="alert"><li>'+ error_msg +'</li></ul>';
				tab_panels.prepend( '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">' + error + '</div>' );
			},
			_validate_step_fields: function ( active_step ) {

				UaelCheckout._clear_validation_error();
				var active_section = $scope.find( '#uael-tab-panel-' + active_step );

				if( active_section ) {
					var all_inputs = active_section.find( ":input" ).not( '.woocommerce-validated,:hidden' );
					var ship_to_different_address = $scope.find( 'input[name="ship_to_different_address"]' );
					var is_account_field = $scope.find( '#createaccount' );

					var valid = true;
					$.each( all_inputs, function( field ) {
						var $this = $( this );
						var type = $this.getType();
						var name = $this.attr( 'name' );

						if( type == 'checkbox' || type == 'select' ) {
							var formated_name = name.replace( '[]','' );
							var parent = $scope.find( '#' + formated_name + '_field' );
						} else {
							var parent = $scope.find( '#' + name + '_field' );
						}

						var is_shipping_field = parent.parents( '.shipping_address' );
						if( is_shipping_field.length > 0 && ship_to_different_address.prop( 'checked' ) != true ) {
							return valid;
						}

						var is_disabled_section = parent.parents();
						var is_required = '';
						if( !( is_disabled_section.length > 0 ) ) {
							is_required = parent.data( 'validations' );
						}

						var account_required = true;
						if( is_account_field.length > 0 ) {
							if( ( is_account_field.prop( 'checked' ) == false ) && ( name == 'account_username' || name == 'account_password' ) ) {
								account_required = false;
							}
						}

						if( ( parent.hasClass( 'validate-required' ) || is_required == 'validate-required' ) && account_required ) {
							var value = UaelCheckout._get_field_value( type, $( this ), name );

							if( UaelCheckout._isEmpty( value ) ) {
								valid = false;
							}else if( parent.hasClass( 'validate-email' ) ) {
								var valid_email = UaelCheckout._validate_email( value );
								if( ! valid_email ) {
									valid = false;
								}
							}

						}

					} );
				}

				return valid;
			},
			_isEmpty: function ( str ) {
				return ( ! str || 0 === str.length );
			},
			_validate_email: function ( email ) {
				var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,})$/;

				if ( reg.test( email ) == false ) {
					return false;
				}
				return true;
			},
			_get_field_value: function ( type, elm, name ) {
				var value = '';
				switch( type ) {
					case 'radio':
						value = $scope.find( "input[type=radio][name='" + name + "']:checked" ).val();
						value = value ? value : '';
						break;
					case 'checkbox':
						if( elm.data( 'multiple' ) == 1 ) {
							var valueArr = [];
							$scope.find( "input[type=checkbox][name='" + name+ "']:checked" ).each( function() {
								valueArr.push( $( this ).val() );
							} );
							value = valueArr;
							if ( $.isEmptyObject(value ) ) {
								value = "";
							}
						}else{
							value = $scope.find( "input[type=checkbox][name='"+ name + "']:checked").val();
							value = value ? value : '';
						}
						break;
					case 'select':
						value = elm.val();
						break;
					case 'multiselect':
						value = elm.val();
						break;
					default:
						value = elm.val();
						break;
				}
				return value;
			},
			_clear_validation_error: function () {
				$scope.find( '.uael_multistep_container .woocommerce-NoticeGroup-checkout, .uael_multistep_container .woocommerce-error, .uael_multistep_container .woocommerce-message, .woocommerce .woocommerce-error' ).remove();
			},
			_jump_to_step: function ( step_number, step ) {
				if( ! step ) {
					step = tabs.find( '#step-' + step_number );
				}

				tabs.find( 'li a' ).removeClass( 'active' );
				tabs.find( 'li.uael-tab' ).removeClass( 'uael-tab-after' );
				var active_tab_panel = tab_panels.find( '#uael-tab-panel-' + step_number );

				if( ! step.hasClass( "active" ) ) {
					step.addClass( "active" );
					step.closest( '.uael-tab' ).addClass( "uael-tab-after" );
				}

				tab_panels.find( 'div.uael-tab-panel' ).not( '#uael-tab-panel-'+step_number ).hide();
				active_tab_panel.show();
				active_step = step_number;

				button_prev.prop( 'disabled', false );
				button_next.prop( 'disabled', false );

				button_prev.removeClass( 'uael-first-prev' );
				button_next.removeClass( 'uael-last-next' );

				if( active_step == first_step ) {
					button_prev.prop( 'disabled', true );
					button_prev.addClass( 'uael-first-prev' );
				}
				if( active_step == last_step ) {
					button_next.prop( 'disabled', true );
					button_next.addClass( 'uael-last-next' );
				}
			},
			_scroll_to_top: function () {
				if ( tabs_wrapper.length ) {
					window.scrollTo({
					  top: 0,
					  behavior: 'smooth'
					});
				}
			},

			_order_review_ajax: function() {
		 		woo_checkout.on('change', 'select.shipping_method, input[name^="shipping_method"], #ship-to-different-address input, .update_totals_on_change select, .update_totals_on_change input[type="radio"], .update_totals_on_change input[type="checkbox"], input[name^="billing_postcode"], input[name^="shipping_postcode"]', function () {

			    	$(document.body).trigger('update_checkout');
					render_order_review();

				});
			},
		}

		$.fn.getType = function() {
			try {
				return this[0].tagName == "INPUT" ? this[0].type.toLowerCase() : this[0].tagName.toLowerCase();
			} catch( err ) {
				return 'E001';
			}
		}

		UaelCheckout._initialize_uael_checkout();
		UaelCheckout._order_review_ajax();

	};

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/uael-woo-products.grid-default', WidgetUAELWooProducts);
		elementorFrontend.hooks.addAction('frontend/element_ready/uael-woo-products.grid-franko', WidgetUAELWooProducts);
		elementorFrontend.hooks.addAction('frontend/element_ready/uael-woo-add-to-cart.default', WidgetUAELWooAddToCart);
		elementorFrontend.hooks.addAction('frontend/element_ready/uael-woo-categories.default', WidgetUAELWooCategories);
		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-mini-cart.default', WidgetUAELMiniCart );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-woo-checkout.default', WidgetUAELCheckout );
	});


} )( jQuery );
