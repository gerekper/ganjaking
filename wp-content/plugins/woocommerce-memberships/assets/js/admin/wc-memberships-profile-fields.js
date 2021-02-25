/**
 * WooCommerce Memberships Profile Fields script.
 *
 * @since 1.19.0
 */
jQuery( function( $ ) {

	var wc_memberships_admin = window.wc_memberships_admin !== null ? window.wc_memberships_admin : {},
	    pagenow              = window.pagenow !== null ? window.pagenow : '';


	if ( 'admin_page_wc_memberships_profile_fields' === pagenow ) {

		// makes the profile fields table sortable
		$( '#the-list' ).sortable( {
			'axis':   'y',
			'handle': $( '.wc-memberships-profile-field-sort-handle' ).not( '.disabled' ),
			'update': function() {

				var $tableViewList = $( '.table-view-list ' ),
				    profileFields = [];

				// prevents further dragging until the current sorting is complete
				$tableViewList.block( {
					message:    null,
					overlayCSS: {
						opacity: 0.6
					}
				} )

				// gets the current rows as resulting from drag event
				$tableViewList.find( 'input[name="bulk_action[]"]' ).each( function() {
					profileFields.push( $( this ).val() );
				} );

				// send new sort order via AJAX to data store
				$.post( wc_memberships_admin.ajax_url, {
					action :        'wc_memberships_sort_profile_fields',
					security :       wc_memberships_admin.sort_profile_fields_nonce,
					profile_fields : profileFields
				}, function() {} ).always( function() { $tableViewList.unblock(); } );
			},
		} );

		// toggles a profile field "editable by" status
		$( '.wc-memberships-profile-field-switch > input' ).on( 'click change', function( e ) {
			e.preventDefault();

			var $toggleSwitch = $( this ),
			    prevState     = $toggleSwitch.is( ':checked' );

			if ( 'editable_by' === $( this ).data( 'prop' ) && $( this ).data( 'profile-field' ).length ) {

				var toggleData = {
					action :        'wc_memberships_toggle_profile_field_editable_by',
					security :      wc_memberships_admin.toggle_profile_field_editable_by_nonce,
					profile_field : $( this ).data( 'profile-field' )
				}

				$.post( wc_memberships_admin.ajax_url, toggleData, function( response ) {

					if ( response && response.success ) {

						var $visibilityCol = $toggleSwitch.closest( 'tr' ).find( 'td.visibility' );

						$toggleSwitch.prop( 'checked', 'customer' === response.data.editable_by )

						$visibilityCol.html( '' )

						if ( 0 === response.data.visibility.length ) {
							$visibilityCol.append( '<span>&mdash;</span>' );
						} else {
							$( response.data.visibility ).each( function( i, option ) {
								$visibilityCol.append( '<span>' + wc_memberships_admin.profile_fields_visibility_options[ option ] + '</span>' );
							} );
						}

					} else {

						$toggleSwitch.prop( 'checked', prevState );
						console.log( response );
					}
				} )
			}

		} );

		// adjust "Show on" options based on chosen plans
		$( '#membership_plan_ids' ).on( 'change', function( e ) {

			var $visibility          = $( '#visibility' ),
			    $membershipPlans     = $( '#membership_plan_ids' ),
			    $selectedPlans       = $membershipPlans.find( 'option:selected' ),
			    allVisibilityOptions = window.wc_memberships_admin.profile_fields_visibility_options,
			    availableOptions     = [],
				$option;

			// if no plan is selected (== any plans) make all visibility options available
			if ( ! $selectedPlans || 0 === $selectedPlans.length ) {

				for ( var optionValue in allVisibilityOptions ) {

					$option = $visibility.find( 'option[value="' + optionValue + '"]' );

					if ( allVisibilityOptions.hasOwnProperty( optionValue ) && ( ! $option || 0 === $option.length ) ) {
						$visibility.append( '<option value="' + optionValue + '">' + allVisibilityOptions[ optionValue ] + '</option>' );
					}
				}

				$visibility.trigger( 'change' );

				return;
			}

			// grab the visibility options matching the chosen plans
			$selectedPlans.each( function() {

				var options = $( this ).data( 'visibility-options' ).split( ',' );

				$( options ).each( function( i, option ) {
					availableOptions.push( option );
				} );
			} )

			// add visibility options made available by the chosen plans
			for ( var visibilityOptionValue in allVisibilityOptions ) {

				$option = $visibility.find( 'option[value="' + visibilityOptionValue + '"]' );

				if ( allVisibilityOptions.hasOwnProperty( visibilityOptionValue ) && availableOptions.includes( visibilityOptionValue ) ) {
					if ( ! $option || 0 === $option.length ) {
						$visibility.append( '<option value="' + visibilityOptionValue + '">' + allVisibilityOptions[ visibilityOptionValue ] + '</option>' );
					}
				} else if ( $option && $option.length > 0 ) {
					$option.remove();
				}
			}

			$visibility.trigger( 'change' );

		} ).trigger( 'change' );

		// toggles meta boxes collapsing and expanding
		$( '.handlediv' ).on( 'click', function( e ) {
			e.preventDefault()

			var $postBox = $( this ).closest( '.postbox' );

			$postBox.toggleClass( 'closed' );

			$( this ).attr( 'aria-expanded', ! $postBox.hasClass( 'closed' ) ? 'true' : 'false' );
		} );

		// handles the data meta box tabs and panels
		$( 'ul.wc-tabs > li a' ).on( 'click', function( e ) {
			e.preventDefault()

			if ( $( e.target ).closest( 'li' ).hasClass( 'disabled' ) ) {
				return false;
			}

			var $tabClicked  = $( e.target ).closest( 'li' ),
			    $targetPanel = $( $( e.target ).attr( 'href' ) );

			$( '.panel' ).hide()
			$targetPanel.show()

			$( '.wc-tab' ).removeClass( 'active' );
			$tabClicked.addClass( 'active' );

		} ).first().trigger( 'click' );

		// handle field type changes
		$( '#type' ).on( 'change', function( e) {

			var fieldType = $( this ).val();

			// show default value for checkbox field types only
			if ( 'checkbox' !== fieldType ) {
				$( 'input[name="default_value"]' ).closest( 'p' ).hide();
			} else {
				$( 'input[name="default_value"]' ).closest( 'p' ).show();
			}

			// toggle options tab for field types that use options
			if ( -1 !== $.inArray( fieldType, [ 'select', 'radio', 'multiselect', 'multicheckbox' ] ) ) {
				$( '.field_options_tab' ).removeClass( 'disabled' );
			} else {
				$( '.field_options_tab' ).addClass( 'disabled' );
			}

			// multi-choice field types turn default option field into a radio vs checkbox for single-choice field types
			if ( -1 !== $.inArray( fieldType, [ 'multiselect', 'multicheckbox' ] ) ) {
				$( '.profile-field-option-default > input[type="radio"]' ).hide();
				$( '.profile-field-option-default > input[type="checkbox"]' ).show();
			} else {
				$( '.profile-field-option-default > input[type="radio"]' ).show();
				$( '.profile-field-option-default > input[type="checkbox"]' ).hide();
			}

		} ).trigger( 'change' );

		// handle field type changes
		$( '#visibility' ).on( 'change', function( e ) {

			toggleRequired();

		} ).trigger( 'change' );

		// when only admins can edit fields, disable input label and description
		$( 'input[name="editable_by"]' ).on( 'change', function( e ) {

			if ( 'admin' === $( this ).attr( 'value' ) && $( this ).is( ':checked' ) ) {
				$( '#label, #description, #visibility' ).closest( 'p' ).hide();
			} else if ( 'customer' === $( this ).attr( 'value' ) && $( this ).is( ':checked' ) ) {
				$( '#label, #description, #visibility' ).closest( 'p' ).show();
			}

			toggleRequired();

		} ).trigger( 'change' );

		// handle adding a profile field option
		$( 'button.add-profile-field-option' ).on( 'click', function( e ) {

			var $template  = $( '#profile-field-option--template' ),
			    nextIndex  = $( '.profile-field-option-row:not(#profile-field-option--template)' ).length,
			    $newOption = $template.clone().attr( 'id', '' );

			$newOption.find( '.profile-field-option-field' ).each( function( index, field ) {

				$( field ).attr( 'name', $( field ).attr( 'name' ).replace( 'template', nextIndex ) );

				if ( 'default_option' === $( field ).attr( 'name' ) ) {
					$( field ).attr( 'value', nextIndex );
				}
			} );

			$newOption.appendTo( $template.closest( 'tbody' ) );

			$( '.profile-field-no-options' ).hide();
			$( '.remove-profile-field-options' ).show();

			destroySortableOptions();
			initSortableOptions();
		} );

		// handle delete of profile field options
		$( 'button.remove-profile-field-options' ).on( 'click', function( e ) {

			$( '.profile-field-option-row th.check-column input:checked' ).closest( '.profile-field-option-row' ).remove()

			if ( 0 === $( '.profile-field-option-row' ).not( '#profile-field-option--template' ).length ) {
				$( '.profile-field-no-options' ).show();
				$( '.remove-profile-field-options' ).hide();
			} else {
				$( '.profile-field-no-options' ).hide();
				$( '.remove-profile-field-options' ).show();
			}
		} );

		// make the profile field options sortable
		function initSortableOptions() {

			$( '#profile-field-options' ).sortable( {
				'axis':   'y',
				'handle': $( '.wc-memberships-profile-field-sort-handle' ),
				'update': function() {

					// resets the indexes of the fields in the options table row
					$( '.profile-field-option-row' ).not( '#profile-field-option--template' ).each( function( index, row ) {

						var rowIndex = index;

						$( row ).find( 'input' ).each( function( i, field ) {

							if ( $( field ).hasClass( 'multi-default-field' ) ) {
								$( field ).attr( 'name', 'default_options[' + rowIndex + ']' );
								$( field ).attr( 'value', rowIndex );
							} else if ( $( field ).hasClass( 'default-field' ) ) {
								$( field ).attr( 'value', rowIndex );
							} else if ( $( field ).hasClass( 'name-field' ) ) {
								$( field ).attr( 'name', 'options[' + rowIndex + ']' );
							}
						} );
					} );
				}
			} );
		}

		// resets the sortable widget
		function destroySortableOptions() {

			var $profileFieldOptions = $( '#profile-field-options' );

			$profileFieldOptions.sortable( 'destroy');
			$profileFieldOptions.find( 'li' ).removeClass( 'ui-state-default' );
			$profileFieldOptions.find( 'li span' ).remove();
		}

		// determines whether the required field must be visible or not
		function toggleRequired() {

			var visibility                = $( '#visibility' ).val();
			var isFieldEditedByCustomer   = 'customer' === $( 'input[name="editable_by"]:checked' ).val();
			var isMyAccountVisibilityOnly = visibility && 1 === visibility.length && 'profile-fields-area' === visibility[0];

			if ( isFieldEditedByCustomer && ! isMyAccountVisibilityOnly ) {
				$( '#required' ).closest( 'p' ).show();
			} else {
				$( '#required' ).closest( 'p' ).hide();
			}
		}

		initSortableOptions();

		var doSubmit = false;

		// performs some simple pre-validation upon form submission
		$( '#mainform' ).on( 'submit', function( e ) {

			// the form must not be validated if it's a filtering action
			if ( $( '#filter_action' ).length ) {
				return true;
			}

			if ( ! doSubmit ) {
				e.preventDefault();

				var $fieldName       = $( '#title' ),
				    $fieldVisibility = $( '#visibility' ),
					$generalTab      = $( '.wc-tab.general_tab > a' ),
					$optionsTab      = $( '.wc-tab.field_options_tab > a' );

				// the field name cannot be blank
				if ( '' === $fieldName.val().trim() ) {

					$( '.show-if-no-name' ).show();

					$generalTab.trigger( 'click' );

					$fieldName.fadeOut(150).fadeIn(150).fadeOut(150).fadeIn(150).fadeOut(150).fadeIn(150);

				// if the field is editable by members, it should have visibility options
				} else if ( 'customer' === $( 'input[name="editable_by"]:checked' ).val() && ( ! $fieldVisibility.val() || 0 === $fieldVisibility.val().length ) ) {

					$generalTab.trigger( 'click' );

					if ( 0 === $( '.show-if-no-visibility' ).length ) {
						$( 'p.visibility_field' ).append( '<span class="description show-if-no-visibility profile-field-validation-error" style="display:block;clear:both;">' + wc_memberships_admin.i18n.profile_field_no_visibility + ' </span>' );
					}

					$fieldVisibility.closest( 'p' ).fadeOut(150).fadeIn(150).fadeOut(150).fadeIn(150).fadeOut(150).fadeIn(150);

				// if the field has options, ensure these exist and are not blank
				} else if ( -1 !== $.inArray( $( '#type' ).val(), [ 'select', 'radio', 'multiselect', 'multicheckbox' ] ) ) {

					var $noOptions = $( '.profile-field-no-options' );

					if ( 'table-row' === $noOptions.css( 'display' ) ) {

						$optionsTab.trigger( 'click' );

						$( '.show-if-options-required' ).show();
						$( '.show-if-options-empty' ).hide();
						$( '.field_options_tab > a' ).trigger( 'click' );

						$noOptions.fadeOut( 150 ).fadeIn( 150 ).fadeOut( 150 ).fadeIn( 150 ).fadeOut( 150 ).fadeIn( 150 );

					} else {

						doSubmit = true;

						$( '.profile-field-option-row' ).not( '#profile-field-option--template' ).find( '.name-field' ).each( function( i, field ) {

							if ( '' === $( field ).val().trim() ) {

								$optionsTab.trigger( 'click' );

								$( '.show-if-options-required' ).hide();
								$( '.show-if-options-empty' ).show();
								$( '.field_options_tab > a' ).trigger( 'click' );

								$( field ).fadeOut( 150 ).fadeIn( 150 ).fadeOut( 150 ).fadeIn( 150 ).fadeOut( 150 ).fadeIn( 150 );

								doSubmit = false;

								return false;
							}
						} );
					}

				} else {

					doSubmit = true;
				}

				if ( doSubmit ) {
					$( '#mainform' ).trigger( 'submit' );
				}
			}
		} );


		// modal to confirm deletion of profile fields in use
		window.WC_Memberships_Modal_View_Profile_Fields_Confirm_Deletion = WC_Memberships_Modal_View.extend( {

			template_id: 'wc-memberships-modal-confirm-profile-field-deletion',

			initialize: function( options ) {

				WC_Memberships_Modal_View_Profile_Fields_Confirm_Deletion.__super__.initialize.apply( this, arguments );

				$( '#wc-memberships-modal-confirm-profile-field-deletion' )
					.on( 'click', '#btn-hide', ( event ) => this.hideButton( event ) )
					.on( 'click', '#btn-delete', ( event ) => this.deleteButton( event ) )
					.on( 'click', '#btn-cancel', () => this.close() );
			},

			hideButton: function( event ) {

				event.preventDefault();

				$( 'input[name="editable_by"][value="admin"]' ).prop( 'checked', true ).trigger( 'change' );
				$( 'select[name="visibility[]"]' ).val( null ).trigger('change');
				$( '#mainform' ).trigger( 'submit' );
			},

			deleteButton: function( event ) {

				event.preventDefault();

				document.location.href = $( '#delete-action .submitdelete' ).attr( 'href' );
			},

		} );

		// trigger modal only if the profile field in edit screen context is in use
		if ( wc_memberships_admin.profile_field_is_in_use ) {

			$( '#delete-action .submitdelete' ).on( 'click.memberships', function( event ) {

				event.preventDefault();

				new window.WC_Memberships_Modal_View_Profile_Fields_Confirm_Deletion();
			} );
		}
	}

} );
