'use strict';

/* global jQuery, ajaxurl, yith_wcan_admin, YITH_WCAN_Filters */

jQuery( function ( $ ) {
	$.add_new_range = function ( t ) {
		const range_filter = t
				.parents( '.widget-content' )
				.find( '.range-filter' ),
			input_field = range_filter.find( 'input:last-child' ),
			field_name = range_filter.data( 'field_name' ),
			position = parseInt( input_field.data( 'position' ) ) + 1,
			html =
				'<input type="text" placeholder="min" name="' +
				field_name +
				'[' +
				position +
				'][min]" value="" class="yith-wcan-price-filter-input widefat" data-position="' +
				position +
				'"/>' +
				'<input type="text" placeholder="max" name="' +
				field_name +
				'[' +
				position +
				'][max]" value="" class="yith-wcan-price-filter-input widefat" data-position="' +
				position +
				'"/>';

		range_filter.append( html );
	};

	$.select_dropdown = function ( elem ) {
		const t = elem,
			select = t.parents( 'p' ).next( 'p' );

		t.is( ':checked' ) ? select.fadeIn( 'slow' ) : select.fadeOut( 'slow' );
	};

	$( document ).on(
		'change',
		'.yith_wcan_type, .yith_wcan_attributes',
		function ( e ) {
			const t = this,
				container = $( this )
					.parents( '.widget-content' )
					.find( '.yith_wcan_placeholder' )
					.html( '' ),
				spinner = container.next( '.spinner' ).show(),
				display = $( this )
					.parents( '.widget-content' )
					.find( '#yit-wcan-display' ),
				style = $( this )
					.parents( '.widget-content' )
					.find( '#yit-wcan-style' ),
				show_count = $( this )
					.parents( '.widget-content' )
					.find( '#yit-wcan-show-count' ),
				attributes = $( this )
					.parents( '.widget-content' )
					.find( '.yith-wcan-attribute-list' ),
				tag_list = $( this )
					.parents( '.widget-content' )
					.find( '.yit-wcan-widget-tag-list' ),
				see_all_text = $( this )
					.parents( '.widget-content' )
					.find( '.yit-wcan-see-all-taxonomies-text' );

			const data = {
				action: 'yith_wcan_select_type',
				id: $(
					'input[name=widget_id]',
					$( t ).parents( '.widget-content' )
				).val(),
				name: $(
					'input[name=widget_name]',
					$( t ).parents( '.widget-content' )
				).val(),
				attribute: $(
					'.yith_wcan_attributes',
					$( t ).parents( '.widget-content' )
				).val(),
				value: $(
					'.yith_wcan_type',
					$( t ).parents( '.widget-content' )
				).val(),
			};

			/* Hierarchical hide/show */
			if (
				data.value === 'list' ||
				data.value === 'select' ||
				data.value === 'brands' ||
				data.value === 'tags'
			) {
				display.show();
				style.hide();
			} else if (
				data.value === 'label' ||
				data.value === 'color' ||
				data.value === 'multicolor'
			) {
				display.hide();
			}

			if ( data.value === 'color' || data.value === 'multicolor' ) {
				style.show();
			} else {
				style.hide();
			}

			if (
				data.value === 'list' ||
				data.value === 'tags' ||
				data.value === 'brands' ||
				data.value === 'categories' ||
				data.value === 'select'
			) {
				show_count.show();
			} else {
				show_count.hide();
			}

			if (
				data.value === 'tags' ||
				data.value === 'brands' ||
				data.value === 'categories'
			) {
				attributes.hide();
			} else {
				attributes.show();
			}

			if ( data.value === 'tags' ) {
				tag_list.show();
			} else {
				tag_list.hide();
			}

			if ( data.value === 'tags' || data.value === 'categories' ) {
				see_all_text.show();
			} else {
				see_all_text.hide();
			}

			$.post(
				ajaxurl,
				data,
				function ( response ) {
					spinner.hide();
					container.html( response.content );
					$( document ).trigger( 'yith_colorpicker' );
				},
				'json'
			);
		}
	);

	// Color-picker
	$( document )
		.on( 'yith_colorpicker', function () {
			$( '.yith-colorpicker' ).each( function () {
				$( this ).wpColorPicker();
			} );
		} )
		.trigger( 'yith_colorpicker' );

	// Custom style handling
	$( document ).on( 'change', '.yith-wcan-enable-custom-style', function () {
		const t = $( this ),
			enable_custom_style = t
				.parents( '.widget-content' )
				.find( '.yith-wcan-reset-custom-style' ),
			checked = t
				.find( '.yith-wcan-enable-custom-style-check' )
				.is( ':checked' );

		checked
			? enable_custom_style.fadeIn( 'slow' )
			: enable_custom_style.fadeOut( 'slow' );
	} );

	// Dropdown dependencies
	$( document ).on( 'change', '.yith-wcan-dropdown-check', function () {
		$.select_dropdown( $( this ) );
	} );

	// Preset status handling
	$( document ).on( 'change', '#preset-status', function () {
		const t = $( this ),
			preset = t.data( 'preset' ),
			status = t.is( ':checked' );

		window.onbeforeunload = '';

		$.post( ajaxurl, {
			preset,
			status: status ? 1 : 0,
			action: 'yith_wcan_change_preset_status',
			_wpnonce: yith_wcan_admin.nonce.change_preset_status,
		} );
	} );

	// Copy preset shortcode
	$( document ).on( 'click', '.copy-on-click', function () {
		const t = $( this ),
			obj_to_copy = t.find( 'input' );

		obj_to_copy.select();
		document.execCommand( 'copy' );

		window.alert( yith_wcan_admin.messages.confirm_copy );
	} );

	// Init filters handling
	$( document )
		.on( 'yith_wcan_filters_init', function () {
			new YITH_WCAN_Filters( jQuery );
		} )
		.trigger( 'yith_wcan_filters_init' );

	// Init upgrade note modal
	$( document )
		.on( 'yith_wcan_upgrade_note_init', function () {
			$( '#yith_wcan_update_to_presets' )
				.off( 'click' )
				.on( 'click', function ( ev ) {
					const t = $( this );

					ev.preventDefault();

					t.WCBackboneModal( {
						template: 'yith-wcan-upgrade-note',
					} );
				} );
		} )
		.trigger( 'yith_wcan_upgrade_note_init' );

	// Custom dependencies
	$( document )
		.on( 'yith_wcan_admin_fields', function () {
			$( '#yith_wcan_show_active_labels' )
				.on( 'change', function () {
					const t = $( this ),
						target = $(
							'#yith_wcan_reset_button_position-after_active_labels'
						),
						target_parent = target.parent();

					if ( t.is( ':checked' ) ) {
						target_parent.show();
					} else {
						target_parent.hide();

						if ( target.is( ':checked' ) ) {
							target_parent
								.prev()
								.find( 'input' )
								.prop( 'checked', true );
						}
					}
				} )
				.change();
		} )
		.trigger( 'yith_wcan_admin_fields' );

	// Filter By Tag tab
	const select_all = $( '.yith-wcan-select-option .select-all' ),
		unselect_all = $( '.yith-wcan-select-option .unselect-all' ),
		checklist = $( '.yith_wcan_select_tag' ),
		widget_select = $( '#yith-wcan-tag-widget-select' );

	// Select all handling
	select_all.on( 'click', function ( e ) {
		e.preventDefault();
		$( this )
			.parents( '.yith-wcan-select-option' )
			.next( '.yith_wcan_select_tag_wrapper' )
			.find( '.yith_wcan_tag_list_checkbox' )
			.attr( 'checked', true );
	} );

	// Unselect all handling
	unselect_all.on( 'click', function ( e ) {
		e.preventDefault();
		$( this )
			.parents( '.yith-wcan-select-option' )
			.next( '.yith_wcan_select_tag_wrapper' )
			.find( '.yith_wcan_tag_list_checkbox' )
			.attr( 'checked', false );
	} );

	$( document )
		.find( '.yith-add-button' )
		.appendTo( '.yith-plugin-fw__panel__content__page__title' );

	$( document ).on(
		'click',
		'.yith-require-confirmation-modal.action__trash',
		function ( e ) {
			e.preventDefault();
			e.stopPropagation();
			const title = $( this ).data( 'title' ),
				message = $( this ).data( 'message' ),
				url = $( this )
					.find( '.yith-plugin-fw__action-button__link' )
					.attr( 'href' );
			// eslint-disable-next-line no-undef
			yith.ui.confirm( {
				closeAfterConfirm: false,
				title,
				message,
				onConfirm() {
					window.location.href = url;
				},
			} );
		}
	);
} );
