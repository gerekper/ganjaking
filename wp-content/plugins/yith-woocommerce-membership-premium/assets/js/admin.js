/* global yith_wcmbs_admin, ajaxurl */
jQuery( function ( $ ) {
	$( '.tips' ).tipTip( {
							 'attribute': 'data-tip',
							 'fadeIn'   : 50,
							 'fadeOut'  : 50,
							 'delay'    : 0
						 } );

	$( '.tips-top' ).tipTip( {
								 'attribute'      : 'data-tip',
								 'fadeIn'         : 50,
								 'fadeOut'        : 50,
								 'delay'          : 0,
								 'defaultPosition': 'top'
							 } );

	$( '.yith-wcmbs-tabs' ).tabs();

	$( '.yith-wcmbs-select2' ).select2();
	$( '.yith-wcmbs-color-picker' ).wpColorPicker();

	$( '.yith-wcmbs-date' ).datepicker( {
											dateFormat: 'yy-mm-dd'
										} );

	/** ----------------------------------------------
	 *  Plans Delay
	 */

	var plansDelay = {
		dom                   : {
			container: $( '.yith-wcmbs-admin-plans-delay' ).first(),
			plansList: $( '#_yith_wcmbs_restrict_access_plan' )
		},
		template              : wp.template( 'yith-wcmbs-admin-single-plan-delay' ),
		init                  : function () {
			if ( !this.dom.container.length ) {
				return;
			}

			this.dom.plansList.on( 'change', this.plansListChangeHandler ).trigger( 'change' );

			this.dom.container.on( 'change', '.yith-plugin-fw-radio', this.radioChangeHandler );
		},
		plansListChangeHandler: function () {
			var select        = $( this ),
				rows          = plansDelay._getRows(),
				selectedPlans = select.val(),
				currentRowIDs = [],
				i;

			rows.each( function () {
				var _planID = $( this ).data( 'plan-id' ) + '';
				currentRowIDs.push( _planID );
				if ( !selectedPlans || selectedPlans.indexOf( _planID ) < 0 ) {
					$( this ).remove();
				}
			} );

			for ( i in selectedPlans ) {
				var _planID = selectedPlans[ i ];
				if ( !currentRowIDs.length || currentRowIDs.indexOf( _planID ) < 0 ) {
					var _planName = select.find( 'option[value="' + _planID + '"]' ).html(),
						_newRow   = $( plansDelay.template( { planID: _planID, planName: _planName } ) );

					_newRow.find( 'input[type=radio]' ).each( function () {
						var _myID = '_yith_wcmbs_single_plan_delay_' + _planID + '-' + $( this ).val();
						// Fix the framework sanitize in IDs of radio fields.
						$( this ).attr( 'id', _myID );
						$( this ).parent().find( 'label' ).attr( 'for', _myID );
					} );

					plansDelay.dom.container.append( _newRow );
				}
			}


		},
		radioChangeHandler    : function () {
			var delay = $( this ).parent().find( '.yith-wcmbs-single-plan-delay-field' );
			if ( $( this ).val() === 'delay' ) {
				delay.removeAttr( 'disabled' );
			} else {
				delay.attr( 'disabled', 'disabled' );
			}
		},
		_getRows              : function () {
			return plansDelay.dom.container.find( '.yith-wcmbs-single-plan-delay-row' );
		}
	};

	plansDelay.init();


	/* - - - - - - - - - -  Plan Item Order - - - - - - - - - - - */

	var plan_item_order_container = $( '#yith-wcmbs-plan-item-order-container' ),
		items                     = plan_item_order_container.find( 'li' ).get(),
		plan_item_text            = $( '#yith-wcmbs-plan-item-text' ),
		block_params              = {
			message        : null,
			overlayCSS     : {
				background: '#000',
				opacity   : 0.6
			},
			ignoreIfBlocked: true
		};

	items.sort( function ( a, b ) {
		var compA = parseInt( $( a ).attr( 'rel' ) );
		var compB = parseInt( $( b ).attr( 'rel' ) );
		return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
	} );

	$( items ).each( function ( idx, itm ) {
		plan_item_order_container.append( itm );
	} );
	//ordering
	plan_item_order_container.sortable( {
											items               : 'li',
											cursor              : 'move',
											scrollSensitivity   : 40,
											forcePlaceholderSize: true,
											helper              : 'clone',
											opacity             : 0.80,
											revert              : true,
											stop                : function ( event, ui ) {
												if ( ui.item.hasClass( 'yith-wcmbs-plan-item-text' ) ) {
													ui.item.find( 'input' ).focus();
												}
											}
										} );

	plan_item_text.draggable( {
								  connectToSortable: '#yith-wcmbs-plan-item-order-container',
								  helper           : "clone",
								  revert           : "invalid",
								  stop             : function ( event, ui ) {
									  ui.helper.html( '<input type="text" name="_yith_wcmbs_plan_items[]" /><span class="dashicons dashicons-no-alt close"></span>' );
									  ui.helper.css( { height: 'auto' } );
								  }
							  } );

	plan_item_order_container.on( 'click', '.close', function ( event ) {
		$( event.target ).closest( 'li' ).remove();
	} );

	plan_item_order_container.find( 'li' ).disableSelection();
	plan_item_text.disableSelection();


	// Delete from plan Actions
	plan_item_order_container.on( 'click', '.yith-wcmbs-delete-from-plan', function ( e ) {
		var target  = $( e.target ),
			li      = target.closest( 'li' ),
			post_id = target.data( 'post-id' ),
			plan_id = target.data( 'plan-id' );

		li.block( block_params );

		// send data for ajax request
		$.ajax( {
					url    : ajaxurl,
					type   : 'POST',
					data   : {
						action : 'yith_wcmbs_remove_plan_for_post',
						post_id: post_id,
						plan_id: plan_id
					},
					success: function ( data ) {
						console.log( data );
						li.unblock();
						li.remove();
					}
				} );
	} );

	plan_item_order_container.on( 'click', '.yith-wcmbs-hide-show-item', function ( e ) {
		var target         = $( e.target ),
			li             = target.closest( 'li' ),
			hidden_item_id = li.children( 'input.yith_wcmbs_hidden_item_ids' ),
			post_id        = target.data( 'post-id' ),
			item_action    = 'show';

		if ( target.is( '.dashicons-visibility' ) ) {
			item_action = 'hide';
		}

		if ( item_action == 'show' ) {
			hidden_item_id.prop( 'disabled', true );
			target.removeClass( 'dashicons-hidden' ).addClass( 'dashicons-visibility' );
		} else {
			hidden_item_id.prop( 'disabled', false );
			target.removeClass( 'dashicons-visibility' ).addClass( 'dashicons-hidden' );
		}
	} );


	/* - - - - - - - - - -  Plan Item Order - - - - - - - - - - - */
	/* - - - - - - - - - - - - - END - - -  - - - - - - - - - - - */


	/* - - - - - - - - - -  BULK EDIT - - - - - - - - - - - */
	$( '#bulk_edit' ).on( 'click', function () {
		// define the bulk edit row
		var $bulk_row = $( '#bulk-edit' );

		// get the selected post ids that are being edited
		var $post_ids = [];
		$bulk_row.find( '#bulk-titles' ).children().each( function () {
			$post_ids.push( $( this ).attr( 'id' ).replace( /^(ttle)/i, '' ) );
		} );

		// get the data
		var $plans_checkboxes = $( '.plans-checklist', $bulk_row ).find( 'input:checked' );
		var plans_ids         = [];
		$plans_checkboxes.each( function () {
			plans_ids.push( $( this ).val() );
		} );

		// save the data
		$.ajax( {
					url  : ajaxurl,
					type : 'POST',
					async: false,
					cache: false,
					data : {
						action                         : 'yith_wcmbs_save_bulk_edit',
						post_ids                       : $post_ids,
						yith_wcmbs_restrict_access_plan: plans_ids
					}
				} );
	} );

	$( document ).on( 'yith_wcmbs_select2_init', function () {
		var ajax_select2 = $( '.yith_wcmbs_ajax_select2_select_customer' );
		ajax_select2.each( function () {
			var select2_args = {
				allowClear        : $( this ).data( 'allow_clear' ) ? true : false,
				placeholder       : $( this ).data( 'placeholder' ),
				minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
				escapeMarkup      : function ( m ) {
					return m;
				},
				ajax              : {
					url           : ajaxurl,
					dataType      : 'json',
					quietMillis   : 250,
					data          : function ( params ) {
						return {
							term    : params.term,
							action  : 'woocommerce_json_search_customers',
							security: yith_wcmbs_admin.customer_nonce
						};
					},
					processResults: function ( data ) {
						var terms = [];
						if ( data ) {
							$.each( data, function ( id, text ) {
								terms.push( { id: id, text: text } );
							} );
						}
						return {
							results: terms
						};
					},
					cache         : true
				}
			};

			$( this ).select2( select2_args );
		} );

		ajax_select2.on( 'yith_wcmbs_select2_reset', function () {
			$( this ).val( '' ).trigger( 'change' );

		} ).trigger( 'yith_wcmbs_select2_reset' );
	} ).trigger( 'yith_wcmbs_select2_init' );

	/**
	 * CHOSEN SELECT and DESELECT ALL BUTTON
	 */

	$( '.yith-wcmbs-select2-select-all' ).on( 'click', function ( e ) {
		var target         = $( e.target ),
			container_id   = target.data( 'container-id' ),
			container      = $( '#' + container_id ),
			current_select = container.find( '.yith-wcmbs-select2' ).first();

		current_select.find( 'option' ).prop( 'selected', true );
		current_select.trigger( 'change' );
	} );

	$( '.yith-wcmbs-select2-deselect-all' ).on( 'click', function ( e ) {
		var target         = $( e.target ),
			container_id   = target.data( 'container-id' ),
			container      = $( '#' + container_id ),
			current_select = container.find( '.yith-wcmbs-select2' ).first();

		current_select.find( 'option:selected' ).removeAttr( 'selected' );
		current_select.trigger( 'change' );
	} );


	// Copy on clipboard
	$( document ).on( 'click', '.yith-wcmbs-copy-to-clipboard', function ( event ) {
		var target           = $( this ),
			selector_to_copy = target.data( 'selector-to-copy' ),
			obj_to_copy      = $( selector_to_copy );

		if ( obj_to_copy.length > 0 ) {
			var temp = $( "<input>" );
			$( 'body' ).append( temp );

			temp.val( obj_to_copy.html() ).select();
			document.execCommand( "copy" );

			temp.remove();
		}
	} );


	/**
	 * Conditionals
	 */
	var showConditional = {
		target   : '.yith-wcmbs-show-conditional:not(.yith-wcmbs-show-conditional--initialized)',
		initEvent: 'yith-wcmbs-show-conditional-init',
		init     : function () {
			var self = showConditional;
			$( self.target ).hide().each( function () {
				var target        = $( this ),
					fieldSelector = target.data( 'dep-selector' ),
					field         = $( fieldSelector ),
					value         = target.data( 'dep-value' ),
					_to_compare, _is_checkbox, _values, currentValue;

				if ( field.length ) {
					_values = value.split( ',' );

					field.on( 'change keyup', function () {
						var _show = true;

						field.each( function ( index ) {
							var currentField = $( this );
							currentValue     = _values[ index ];

							_is_checkbox = currentField.is( 'input[type=checkbox]' );
							_is_checkbox && ( currentValue = currentValue !== 'no' );

							_to_compare = !_is_checkbox ? currentField.val() : currentField.is( ':checked' );
							if ( _to_compare !== currentValue ) {
								_show = false;
							}
						} );

						if ( _show ) {
							target.show();
						} else {
							target.hide();
						}

					} ).trigger( 'change' );

					target.addClass( 'yith-wcmbs-show-conditional--initialized' );
				}
			} );
		}
	};

	$( document ).on( showConditional.initEvent, showConditional.init );
	showConditional.init();


	var panelFieldsVisibility = {
		dom                               : {
			hideContents                 : $( '#yith-wcmbs-hide-contents' ),
			alternativeContentMode       : $( '#yith-wcmbs-default-alternative-content-mode' ),
			alternativeContentContainer  : $( '#yith-wcmbs-default-alternative-content' ).closest( '.yith-plugin-fw-panel-wc-row' ),
			alternativeContentIDContainer: $( '#yith-wcmbs-default-alternative-content-id' ).closest( '.yith-plugin-fw-panel-wc-row' )
		},
		init                              : function () {
			var self = panelFieldsVisibility;

			self.dom.hideContents.on( 'change', self.handleAlternativeContentVisibility );
			self.dom.alternativeContentMode.on( 'change', self.handleAlternativeContentVisibility );

			self.handleAlternativeContentVisibility();
		},
		handleAlternativeContentVisibility: function () {
			var self        = panelFieldsVisibility,
				showContent = false,
				showID      = false;
			if ( 'alternative_content' === self.dom.hideContents.val() ) {
				if ( 'set' === self.dom.alternativeContentMode.val() ) {
					showContent = true;
				} else {
					showID = true;
				}
			}

			if ( showContent ) {
				self.dom.alternativeContentContainer.show();
			} else {
				self.dom.alternativeContentContainer.hide();
			}

			if ( showID ) {
				self.dom.alternativeContentIDContainer.show();
			} else {
				self.dom.alternativeContentIDContainer.hide();
			}
		}
	};

	panelFieldsVisibility.init();

} );