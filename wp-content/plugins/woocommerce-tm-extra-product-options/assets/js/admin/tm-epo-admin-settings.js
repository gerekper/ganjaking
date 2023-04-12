( function( window, document, $ ) {
	'use strict';

	var TMEPOADMINSETTINGSJS = window.TMEPOADMINSETTINGSJS;
	var localStorage = $.epoAPI.util.getStorage( 'localStorage' );
	var confirm = window.confirm;
	var toastr = window.toastr;
	var ajaxCheck = 0;

	function tm_license_check( action ) {
		var tm_epo_consent_for_transmit = $( '#tm_epo_consent_for_transmit' );
		var data;

		if ( tm_epo_consent_for_transmit.length && tm_epo_consent_for_transmit.is( ':checked' ) && ajaxCheck === 0 ) {
			ajaxCheck = 1;
			$( '.tm-license-result' ).html( '<div class="activated"><p>' + TMEPOADMINSETTINGSJS.i18n_sending_data + '</p></div>' );
			$( '.tm-license-button' ).block( {
				message: null
			} );
			data = {
				action: 'tm_' + action + '_license',
				username: $( '#tm_epo_envato_username' ).val(),
				key: $( '#tm_epo_envato_purchasecode' ).val(),
				security: TMEPOADMINSETTINGSJS.settings_nonce
			};
			$.post(
				TMEPOADMINSETTINGSJS.ajax_url,
				data,
				function( response ) {
					var html;

					if ( ! response || response === -1 ) {
						html = TMEPOADMINSETTINGSJS.i18n_invalid_request;
					} else if ( response && response.message && response.result && ( response.result === '-3' || response.result === '-2' || response.result === 'wp_error' || response.result === 'server_error' ) ) {
						html = response.message;
					} else if ( response && response.message && response.result && response.result === '4' ) {
						html = response.message;
					} else {
						html = '';
					}

					$( '.tm-license-result' ).html( html );
					$( '.tm-license-button' ).unblock();
				},
				'json'
			).always( function( response ) {
				$( '.tm-license-button' ).unblock();
				ajaxCheck = 0;
				if ( response && response.result && response.result === '4' ) {
					if ( action === 'activate' ) {
						$( '.tm-deactivate-license' ).removeClass( 'tm-hidden' );
						$( '.tm-activate-license' ).removeClass( 'tm-hidden' ).addClass( 'tm-hidden' );
					}
					if ( action === 'deactivate' ) {
						$( '.tm-deactivate-license' ).removeClass( 'tm-hidden' ).addClass( 'tm-hidden' );
						$( '.tm-activate-license' ).removeClass( 'tm-hidden' );
					}
				}
			} );
		} else {
			$( '.tm-license-result' ).html( '<div class="error"><p>' + TMEPOADMINSETTINGSJS.i18n_must_concent + '</p></div>' );
		}
	}

	function tm_options_placement_settings( select ) {
		var val = select.val();
		var row1 = $( '#tm_epo_options_placement_custom_hook' ).closest( 'tr' );

		if ( val === 'custom' ) {
			row1.show();
		} else {
			row1.hide();
		}
	}

	function tm_totals_box_placement_settings( select ) {
		var val = select.val();
		var row1 = $( '#tm_epo_totals_box_placement_custom_hook' ).closest( 'tr' );

		if ( val === 'custom' ) {
			row1.show();
		} else {
			row1.hide();
		}
	}

	function tm_display_settings( select ) {
		var val = select.val();
		var row1 = $( '#tm_epo_options_placement' ).closest( 'tr' );
		var row2 = $( '#tm_epo_totals_box_placement' ).closest( 'tr' );
		var row3 = $( '#tm_epo_options_placement_custom_hook' ).closest( 'tr' );
		var row4 = $( '#tm_epo_totals_box_placement_custom_hook' ).closest( 'tr' );

		if ( val === 'action' ) {
			row1.hide();
			row2.hide();
			row3.hide();
			row4.hide();
		} else {
			row1.show();
			row2.show();
			tm_options_placement_settings( $( '#tm_epo_options_placement' ) );
			tm_totals_box_placement_settings( $( '#tm_epo_totals_box_placement' ) );
		}
	}

	function tm_epo_floating_totals_box_settings( select ) {
		var val = select.val();
		var row1 = $( '#tm_epo_floating_totals_box_visibility' ).closest( 'tr' );
		var row2 = $( '#tm_epo_floating_totals_box_add_button' ).closest( 'tr' );
		var row3 = $( '#tm_epo_floating_totals_box_pixels' ).closest( 'tr' );

		if ( val === 'disable' ) {
			row1.hide();
			row2.hide();
			row3.hide();
		} else {
			row1.show();
			row2.show();
			tm_epo_floating_totals_box_visibility_settings( $( '#tm_epo_floating_totals_box_visibility' ) );
		}
	}

	function tm_epo_floating_totals_box_visibility_settings( select ) {
		var val = select.val();
		var val2 = $( '#tm_epo_floating_totals_box' ).val();
		var row1 = $( '#tm_epo_floating_totals_box_pixels' ).closest( 'tr' );

		if ( val === 'always' || val2 === 'disable' ) {
			row1.hide();
		} else {
			row1.show();
		}
	}

	function tm_epo_show_price_inside_option_settings( select ) {
		var val = select.is( ':checked' );
		var row1 = $( '#tm_epo_show_price_inside_option_hidden_even' ).closest( 'tr' );
		var row2 = $( '#tm_epo_multiply_price_inside_option' ).closest( 'tr' );

		if ( val ) {
			row1.show();
			row2.show();
		} else {
			row1.hide();
			row2.hide();
		}
	}

	function tm_epo_show_hide_uploaded_file_url_cart_settings( select ) {
		var val = select.is( ':checked' );
		var row1 = $( '#tm_epo_show_upload_image_replacement' ).closest( 'tr' );

		if ( val ) {
			row1.hide();
		} else {
			row1.show();
		}
	}

	function tm_css_styles_style( select ) {
		$( select )
			.closest( 'td' )
			.css( 'position', 'relative' )
			.append(
				'<label class="tm-epo-field-label"><span class="tc-epo-style-wrapper"><input type="checkbox" checked><span class="tm-epo-style"></span></span></label><label class="tm-epo-field-label"><span class="tc-epo-style-wrapper"><input type="radio" checked><span class="tm-epo-style"></span></span></label>'
			);
	}

	function tm_css_styles_style_settings( select ) {
		var val = select.val();
		var label = $( '.tm-epo-field-label' );

		label.find( '.tc-epo-style-wrapper, .tm-epo-style' ).removeClass( 'square square2 round round2' ).addClass( val );
	}

	function tm_css_styles_settings( select ) {
		var row1 = $( '#tm_epo_css_styles_style' ).closest( 'tr' );

		if ( select.is( ':checked' ) ) {
			row1.show();
		} else {
			row1.hide();
		}
	}

	function tm_epo_css_selected_border( select ) {
		$( select ).closest( 'td' ).css( 'position', 'relative' ).append( '<div class="tm-border-type"></div>' );
	}

	function tm_epo_css_selected_border_settings( select ) {
		var val = select.val();
		var border = $( '.tm-border-type' );

		border.removeClass( 'square round shadow thinline' ).addClass( val );
	}

	function tc_find_row( obj ) {
		return obj.closest( 'tr' );
	}

	function show_sub_section( mitem, item, inputs, table ) {
		tc_find_row( inputs ).addClass( 'tm-hidden' );
		tc_find_row( inputs.filter( '.' + mitem ) ).removeClass( 'tm-hidden' );
		table.find( '.tm-section-desc .tm-section-menu-item' ).removeClass( 'active' );
		item.addClass( 'active' );
	}

	function doConfirm( title, func, funcThis, funcArgs ) {
		var $_html = $.epoAPI.template.html( window.wp.template( 'tc-floatbox' ), {
			id: 'tc-floatbox-content',
			title: title,
			html: '',
			uniqid: '',
			update: TMEPOADMINSETTINGSJS.i18n_yes,
			cancel: TMEPOADMINSETTINGSJS.i18n_no
		} );
		var clicked = false;

		$.tcFloatBox( {
			closefadeouttime: 0,
			overlayopacity: 0.6,
			animateOut: '',
			fps: 1,
			ismodal: true,
			refresh: 'fixed',
			width: '50%',
			height: 'auto',
			classname: 'flasho tc-wrapper tc-question',
			data: $_html,
			cancelEvent: function( inst ) {
				if ( clicked ) {
					return;
				}
				clicked = true;

				inst.destroy();
			},
			cancelClass: '.floatbox-cancel',
			updateEvent: function( inst ) {
				if ( clicked ) {
					return;
				}
				clicked = true;

				func.apply( funcThis, funcArgs );

				inst.destroy();
			},
			updateClass: '.floatbox-update',
			isconfirm: true
		} );
	}

	function doPost( form, data, refresh ) {
		form.block( {
			message: null
		} );

		$.post(
			TMEPOADMINSETTINGSJS.ajax_url,
			data,
			function( response ) {
				if ( response ) {
					if ( response.error === 1 ) {
						toastr.error( response.message, TMEPOADMINSETTINGSJS.i18n_epo );
					} else {
						toastr.success( response.message, TMEPOADMINSETTINGSJS.i18n_epo );
						if ( refresh ) {
							window.location.reload();
						}
					}
				}
			},
			'json'
		).always( function() {
			form.unblock();
		} );
	}

	function setMathData() {
		var tmEpoMath = $( '#tm_epo_math' );
		var dataRow = $( '.constantrow' );
		var mathArray = {};

		dataRow
			.toArray()
			.forEach( function( el, i ) {
				var $el = $( el );
				var constantName = $el.find( '.constant-name' );
				var name = constantName.val();
				var constantValue = $el.find( '.constant-value' );
				var value = constantValue.val();
				var labelName = constantName.closest( '.constant-label-wrap' );
				var labelValue = constantValue.closest( '.constant-label-wrap' );

				if ( name === '' ) {
					labelName.addClass( 'tm-error' );
				} else {
					labelName.removeClass( 'tm-error' );
				}
				if ( value === '' ) {
					labelValue.addClass( 'tm-error' );
				} else {
					labelValue.removeClass( 'tm-error' );
				}

				if ( name !== '' || value !== '' ) {
					mathArray[ 'm' + i ] = {
						name: String( name ),
						value: String( value )
					};
				}
			} );

		tmEpoMath.val( JSON.stringify( mathArray ) );
	}

	function tm_epo_math() {
		var tmEpoMath = $( '#tm_epo_math' );
		var mathArray;
		var template = '';

		tmEpoMath.closest( 'tr' ).hide();

		mathArray = $.epoAPI.util.parseJSON( tmEpoMath.val() );
		if ( ! mathArray ) {
			mathArray = {};
		}

		Object.keys( mathArray ).forEach( function( key, i ) {
			var name = mathArray[ key ].name;
			var value = mathArray[ key ].value;

			template = template + $.epoAPI.template.html( window.wp.template( 'tc-constant-template' ), {
				id: i,
				labelname: TMEPOADMINSETTINGSJS.i18n_constant_name,
				labelvalue: TMEPOADMINSETTINGSJS.i18n_constant_value,
				constantname: name,
				constantvalue: value,
				labelnameclass: ( name === '' ) ? ' tm-error' : '',
				labelvalueclass: ( value === '' ) ? ' tm-error' : ''
			} );
		} );

		$( '.tc-add-constant' ).closest( 'tr' ).before( template );

		$( document ).on( 'click', '.tc-add-constant', function() {
			var $this = $( this );
			var constantDiv = $this.closest( 'tr' );
			var len = $( '.constantrow' ).length;

			constantDiv.before( $.epoAPI.template.html( window.wp.template( 'tc-constant-template' ), {
				id: len,
				labelname: TMEPOADMINSETTINGSJS.i18n_constant_name,
				labelvalue: TMEPOADMINSETTINGSJS.i18n_constant_value
			} ) );
		} );

		$( document ).on( 'click', '.tc-constant-delete .delete', function() {
			$( this ).closest( 'tr' ).remove();
		} );

		$( document ).on( 'change blur keyup', '.constant-name, .constant-value', function() {
			var $this = $( this );
			var label = $this.closest( '.constant-label-wrap' );

			if ( $this.val() === '' ) {
				label.addClass( 'tm-error' );
			} else {
				label.removeClass( 'tm-error' );
			}
		} );
	}

	// document ready
	$( function() {
		var tm_settings_wrap = $( '.tm-settings-wrap' );
		var tm_settings_wrap_checkbox;

		if ( tm_settings_wrap.length > 0 ) {
			tm_settings_wrap_checkbox = tm_settings_wrap.find( ':checkbox' );
			tm_settings_wrap_checkbox.closest( 'label' ).addClass( 'tm-epo-switch-wrapper-label' );
			tm_settings_wrap_checkbox.wrap( '<span class="tm-epo-switch-wrapper tc"></span>' );
			tm_settings_wrap_checkbox.after( '<span class="tc-label tm-epo-switch tc"></span>' );

			$( '.forminp .description' )
				.toArray()
				.forEach( function( el ) {
					var $el = $( el );
					var tr = $el.closest( 'tr' );
					var titledesc = tr.find( '.titledesc' );

					titledesc.append( $el );
				} );

			$( '#tm_epo_consent_for_transmit' ).closest( 'tr' ).after( $( '<tr valign="top"><th scope="row" class="titledesc tm-license-div"></th><td class="forminp forminp-license"></td></tr>' ) );

			$( '.tm-license-div' ).append( $( '.tm-license-button' ) );
			$( '.forminp-license' ).append( $( '.tm-license-result' ) );

			$( window ).on( 'tc-opentab.tmtabs tc-isopentab.tmtabs', function( e, o ) {
				var items = o.table.find( '.tm-section-desc .tm-section-menu-item' );
				var mitem;
				var item;
				var inputs;

				if ( items.length > 0 ) {
					if ( localStorage ) {
						mitem = localStorage.getItem( 'tmadminextratab' );
					}
					item = items.filter( '[data-menu="' + mitem + '"]' );
					inputs = o.table.find( 'select,input,text,textarea' );

					if ( ! mitem || item.length === 0 ) {
						item = $( items ).eq( 0 );
						mitem = item.attr( 'data-menu' );
					}
					show_sub_section( mitem, item, inputs, o.table );
				}
			} );

			$( document ).on( 'keydown.tmtabs', '.tm-section-menu-item', function( e ) {
				var $this = $( this );
				var prevnext;
				if ( e.keyCode === 13 ) {
					$this.trigger( 'click' );
				}
				if ( e.keyCode === 39 ) {
					prevnext = $this.next( '.tm-section-menu-item' );
				}
				if ( e.keyCode === 37 ) {
					prevnext = $this.prev( '.tm-section-menu-item' );
				}
				if ( prevnext && prevnext.length ) {
					$this.trigger( 'blur' );
					prevnext.trigger( ' focus' ).trigger( 'click' );
					e.preventDefault();
				}
			} );

			$( document ).on( 'click', '.tm-section-menu-item', function() {
				var item = $( this );
				var mitem = item.attr( 'data-menu' );
				var table = item.closest( '.tm-tab' );
				var inputs = table.find( 'select,input,text,textarea' );

				show_sub_section( mitem, item, inputs, table );

				if ( localStorage ) {
					localStorage.setItem( 'tmadminextratab', mitem );
				}
			} );

			$( window ).on( 'tc-tmtabs-clicked', function( e, o ) {
				var items = o.table.find( '.tm-section-desc .tm-section-menu-item' );
				var mitem;
				var c;
				var con;
				var item;
				var inputs;

				if ( items.length > 0 ) {
					if ( localStorage ) {
						mitem = localStorage.getItem( 'tmadminextratab' );
					}
					if ( localStorage ) {
						c = localStorage.getItem( 'tmadminextratab-context' );
					}
					con = o.header.attr( o.options.dataattribute );
					inputs = o.table.find( 'select,input,text,textarea' );

					if ( mitem && c === con ) {
						item = items.filter( '[data-menu="' + mitem + '"]' );
					} else {
						item = $( items ).eq( 0 );
						mitem = item.attr( 'data-menu' );
					}
					if ( localStorage ) {
						localStorage.setItem( 'tmadminextratab-context', con );
					}
					if ( item.length > 0 && ! item.is( '.active' ) ) {
						show_sub_section( mitem, item, inputs, o.table );
					}
				}
			} );

			$( document ).on( 'click', '.tc-save-button', function( e ) {
				var form = $( this ).closest( 'form' );
				var data;

				setMathData();
				data = form.tcSerializeObject();
				data = $.extend( true, data, {
					action: 'tm_save_settings',
					save: 'save',
					security: TMEPOADMINSETTINGSJS.settings_nonce
				} );

				e.preventDefault();

				doPost( form, data );
			} );

			$( document ).on( 'click', '.tc-reset-button', function( e ) {
				var form = $( this ).closest( 'form' );
				var data = $.extend( true, {}, {
					action: 'tm_reset_settings',
					save: 'save',
					security: TMEPOADMINSETTINGSJS.settings_nonce
				} );

				e.preventDefault();

				doConfirm( TMEPOADMINSETTINGSJS.i18n_reset_settings, doPost, this, [ form, data, true ] );
			} );

			$( '.tm-settings-wrap .tm-tabs' ).tcTabs( { scroll: false } );

			$( '.tm-activate-license' ).on( 'click', function( e ) {
				e.preventDefault();
				tm_license_check( 'activate' );
			} );
			$( '.tm-deactivate-license' ).on( 'click', function( e ) {
				e.preventDefault();
				tm_license_check( 'deactivate' );
			} );

			$( '#tm_epo_display' ).on( 'change', function() {
				tm_display_settings( $( this ) );
			} );
			$( '#tm_epo_options_placement' ).on( 'change', function() {
				tm_options_placement_settings( $( this ) );
			} );
			$( '#tm_epo_totals_box_placement' ).on( 'change', function() {
				tm_totals_box_placement_settings( $( this ) );
			} );
			$( '#tm_epo_css_styles' ).on( 'change', function() {
				tm_css_styles_settings( $( this ) );
			} );
			$( '#tm_epo_css_styles_style' ).on( 'change', function() {
				tm_css_styles_style_settings( $( this ) );
			} );
			$( '#tm_epo_css_selected_border' ).on( 'change', function() {
				tm_epo_css_selected_border_settings( $( this ) );
			} );
			$( '#tm_epo_floating_totals_box' ).on( 'change', function() {
				tm_epo_floating_totals_box_settings( $( this ) );
			} );
			$( '#tm_epo_floating_totals_box_visibility' ).on( 'change', function() {
				tm_epo_floating_totals_box_visibility_settings( $( this ) );
			} );
			$( '#tm_epo_show_price_inside_option' ).on( 'change', function() {
				tm_epo_show_price_inside_option_settings( $( this ) );
			} );
			$( '#tm_epo_show_hide_uploaded_file_url_cart' ).on( 'change', function() {
				tm_epo_show_hide_uploaded_file_url_cart_settings( $( this ) );
			} );

			tm_display_settings( $( '#tm_epo_display' ) );
			tm_options_placement_settings( $( '#tm_epo_options_placement' ) );
			tm_totals_box_placement_settings( $( '#tm_epo_totals_box_placement' ) );
			tm_css_styles_style( $( '#tm_epo_css_styles_style' ) );
			tm_css_styles_style_settings( $( '#tm_epo_css_styles_style' ) );
			tm_css_styles_settings( $( '#tm_epo_css_styles' ) );
			tm_epo_css_selected_border( $( '#tm_epo_css_selected_border' ) );
			tm_epo_css_selected_border_settings( $( '#tm_epo_css_selected_border' ) );
			tm_epo_floating_totals_box_settings( $( '#tm_epo_floating_totals_box' ) );
			tm_epo_floating_totals_box_visibility_settings( $( '#tm_epo_floating_totals_box_visibility' ) );
			tm_epo_show_price_inside_option_settings( $( '#tm_epo_show_price_inside_option' ) );
			tm_epo_show_hide_uploaded_file_url_cart_settings( $( '#tm_epo_show_hide_uploaded_file_url_cart' ) );

			tm_epo_math();

			$( document ).on( 'click.cpf', '.tm-mn-movetodir,.tm-mn-deldir,.tm-mn-delfile', function( e ) {
				var $this = $( this );
				var forminp_tm_html = $( '.forminp-tm_html' );
				var action;
				var data;

				e.preventDefault();

				if ( forminp_tm_html.length > 0 ) {
					if ( forminp_tm_html.data( 'doing_ajax' ) ) {
						return;
					}
					if ( $this.is( '.tm-mn-deldir' ) && ! confirm( TMEPOADMINSETTINGSJS.i18n_mn_delete_folder ) ) {
						return;
					} else if ( $this.is( '.tm-mn-delfile' ) && ! confirm( TMEPOADMINSETTINGSJS.i18n_mn_delete_file ) ) {
						return;
					}
					$this.prepend( '<i class="tm-icon tcfa tcfa-spin tcfa-spinner"></i>' );

					forminp_tm_html.data( 'doing_ajax', 1 ).block( {
						message: null
					} );
					action = 'tm_mn_movetodir';
					data = {
						action: action,
						dir: $this.attr( 'data-tm-dir' ),
						security: TMEPOADMINSETTINGSJS.settings_nonce
					};

					if ( $this.is( '.tm-mn-deldir' ) ) {
						data.action = 'tm_mn_deldir';
						data.tmdir = $this.attr( 'data-tm-deldir' );
					} else if ( $this.is( '.tm-mn-delfile' ) ) {
						data.action = 'tm_mn_delfile';
						data.tmfile = $this.attr( 'data-tm-delfile' );
						data.tmdir = $this.attr( 'data-tm-deldir' );
					}
					$.post(
						TMEPOADMINSETTINGSJS.ajax_url,
						data,
						function( response ) {
							var $_html;

							if ( response && response.result && response.result !== '' ) {
								forminp_tm_html.html( response.result );
							} else if ( response && response.error && response.message ) {
								$_html = $.epoAPI.template.html( window.wp.template( 'tc-floatbox-import' ), {
									id: 'tc-floatbox-content',
									title: TMEPOADMINSETTINGSJS.i18n_error_title,
									html: '<div class="tm-inner">' + response.message + '</div>',
									cancel: TMEPOADMINSETTINGSJS.i18n_cancel
								} );
								$.tcFloatBox( {
									closefadeouttime: 0,
									//"animationBaseClass": "",
									//"animateIn": "",
									animateOut: '',
									fps: 1,
									ismodal: true,
									refresh: 'fixed',
									width: '50%',
									height: '300px',
									classname: 'flasho tc-wrapper tm-error',
									data: $_html
								} );
							}
						},
						'json'
					).always( function() {
						forminp_tm_html.data( 'doing_ajax', 0 ).unblock();
						$this.find( '.tm-icon' ).remove();
					} );
				}
			} );
		}

		$.tcToolTip();
	} );
}( window, document, window.jQuery ) );
