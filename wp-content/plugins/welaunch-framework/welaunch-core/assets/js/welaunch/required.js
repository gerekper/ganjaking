/* global welaunch */

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.makeBoolStr = function( val ) {
		if ( 'false' === val || false === val || '0' === val || 0 === val || null === val || '' === val ) {
			return 'false';
		} else if ( 'true' === val || true === val || '1' === val || 1 === val ) {
			return 'true';
		} else {
			return val;
		}
	};

	$.welaunch.checkRequired = function( el ) {
		$.welaunch.required();

		$( 'body' ).on(
			'change',
			'.welaunch-main select, .welaunch-main radio, .welaunch-main input[type=checkbox], .welaunch-main input[type=hidden]',
			function() {
				$.welaunch.check_dependencies( this );
			}
		);

		$( 'body' ).on(
			'check_dependencies',
			function( e, variable ) {
				e = null;
				$.welaunch.check_dependencies( variable );
			}
		);

		if ( welaunch.customizer ) {
			el.find( '.customize-control.welaunch-field.hide' ).hide();
		}

		el.find( '.welaunch-container td > fieldset:empty,td > div:empty' ).parent().parent().hide();
	};

	$.welaunch.required = function() {

		// Hide the fold elements on load.
		// It's better to do this by PHP but there is no filter in tr tag , so is not possible
		// we going to move each attributes we may need for folding to tr tag.
		$.each(
			welaunch.opt_names,
			function( x ) {
				$.each(
					window['welaunch_' + welaunch.opt_names[x].replace( /\-/g, '_' )].folds,
					function( i, v ) {
						var div;
						var rawTable;

						var fieldset = $( '#' + welaunch.opt_names[x] + '-' + i );

						fieldset.parents( 'tr:first, li:first' ).addClass( 'fold' );

						if ( 'hide' === v ) {
							fieldset.parents( 'tr:first, li:first' ).addClass( 'hide' );

							if ( fieldset.hasClass( 'welaunch-container-section' ) ) {
								div = $( '#section-' + i );

								if ( div.hasClass( 'welaunch-section-indent-start' ) ) {
									$( '#section-table-' + i ).hide().addClass( 'hide' );
									div.hide().addClass( 'hide' );
								}
							}

							if ( fieldset.hasClass( 'welaunch-container-info' ) ) {
								$( '#info-' + i ).hide().addClass( 'hide' );
							}

							if ( fieldset.hasClass( 'welaunch-container-divide' ) ) {
								$( '#divide-' + i ).hide().addClass( 'hide' );
							}

							if ( fieldset.hasClass( 'welaunch-container-raw' ) ) {
								rawTable = fieldset.parents().find( 'table#' + welaunch.opt_names[x] + '-' + i );
								rawTable.hide().addClass( 'hide' );
							}
						}
					}
				);
			}
		);
	};

	$.welaunch.getContainerValue = function( id ) {
		var value = $( '#' + welaunch.optName.args.opt_name + '-' + id ).serializeForm();

		if ( null !== value && 'object' === typeof value && value.hasOwnProperty( welaunch.optName.args.opt_name ) ) {
			value = value[welaunch.optName.args.opt_name][id];
		}

		if ( $( '#' + welaunch.optName.args.opt_name + '-' + id ).hasClass( 'welaunch-container-media' ) ) {
			value = value.url;
		}

		return value;
	};

	$.welaunch.check_dependencies = function( variable ) {
		var current;
		var id;
		var container;
		var isHidden;

		if ( null === welaunch.optName.required ) {
			return;
		}

		current = $( variable );
		id      = current.parents( '.welaunch-field:first' ).data( 'id' );

		if ( ! welaunch.optName.required.hasOwnProperty( id ) ) {
			return;
		}

		container = current.parents( '.welaunch-field-container:first' );
		isHidden  = container.parents( 'tr:first' ).hasClass( 'hide' );

		if ( ! container.parents( 'tr:first' ).length ) {
			isHidden = container.parents( '.customize-control:first' ).hasClass( 'hide' );
		}

		$.each(
			welaunch.optName.required[id],
			function( child ) {
				var div;
				var rawTable;
				var tr;

				var current       = $( this );
				var show          = false;
				var childFieldset = $( '#' + welaunch.optName.args.opt_name + '-' + child );

				tr = childFieldset.parents( 'tr:first' );

				if ( 0 === tr.length ) {
					tr = childFieldset.parents( 'li:first' );
				}

				if ( ! isHidden ) {
					show = $.welaunch.check_parents_dependencies( child );
				}

				if ( true === show ) {

					// Shim for sections.
					if ( childFieldset.hasClass( 'welaunch-container-section' ) ) {
						div = $( '#section-' + child );

						if ( div.hasClass( 'welaunch-section-indent-start' ) && div.hasClass( 'hide' ) ) {
							$( '#section-table-' + child ).fadeIn( 300 ).removeClass( 'hide' );
							div.fadeIn( 300 ).removeClass( 'hide' );
						}
					}

					if ( childFieldset.hasClass( 'welaunch-container-info' ) ) {
						$( '#info-' + child ).fadeIn( 300 ).removeClass( 'hide' );
					}

					if ( childFieldset.hasClass( 'welaunch-container-divide' ) ) {
						$( '#divide-' + child ).fadeIn( 300 ).removeClass( 'hide' );
					}

					if ( childFieldset.hasClass( 'welaunch-container-raw' ) ) {
						rawTable = childFieldset.parents().find( 'table#' + welaunch.optName.args.opt_name + '-' + child );
						rawTable.fadeIn( 300 ).removeClass( 'hide' );
					}

					tr.fadeIn(
						300,
						function() {
							$( this ).removeClass( 'hide' );
							if ( welaunch.optName.required.hasOwnProperty( child ) ) {
								$.welaunch.check_dependencies( $( '#' + welaunch.optName.args.opt_name + '-' + child ).children().first() );
							}

							$.welaunch.initFields();
						}
					);

					if ( childFieldset.hasClass( 'welaunch-container-section' ) || childFieldset.hasClass( 'welaunch-container-info' ) ) {
						tr.css( { display: 'none' } );
					}
				} else if ( false === show ) {
					tr.fadeOut(
						100,
						function() {
							$( this ).addClass( 'hide' );
							if ( welaunch.optName.required.hasOwnProperty( child ) ) {
								$.welaunch.required_recursive_hide( child );
							}
						}
					);
				}

				current.find( 'select, radio, input[type=checkbox]' ).trigger( 'change' );
			}
		);
	};

	$.welaunch.required_recursive_hide = function( id ) {
		var div;
		var rawTable;
		var toFade;

		toFade = $( '#' + welaunch.optName.args.opt_name + '-' + id ).parents( 'tr:first' );
		if ( 0 === toFade ) {
			toFade = $( '#' + welaunch.optName.args.opt_name + '-' + id ).parents( 'li:first' );
		}

		toFade.fadeOut(
			50,
			function() {
				$( this ).addClass( 'hide' );

				if ( $( '#' + welaunch.optName.args.opt_name + '-' + id ).hasClass( 'welaunch-container-section' ) ) {
					div = $( '#section-' + id );

					if ( div.hasClass( 'welaunch-section-indent-start' ) ) {
						$( '#section-table-' + id ).fadeOut( 50 ).addClass( 'hide' );
						div.fadeOut( 50 ).addClass( 'hide' );
					}
				}

				if ( $( '#' + welaunch.optName.args.opt_name + '-' + id ).hasClass( 'welaunch-container-info' ) ) {
					$( '#info-' + id ).fadeOut( 50 ).addClass( 'hide' );
				}

				if ( $( '#' + welaunch.optName.args.opt_name + '-' + id ).hasClass( 'welaunch-container-divide' ) ) {
					$( '#divide-' + id ).fadeOut( 50 ).addClass( 'hide' );
				}

				if ( $( '#' + welaunch.optName.args.opt_name + '-' + id ).hasClass( 'welaunch-container-raw' ) ) {
					rawTable = $( '#' + welaunch.optName.args.opt_name + '-' + id ).parents().find( 'table#' + welaunch.optName.args.opt_name + '-' + id );
					rawTable.fadeOut( 50 ).addClass( 'hide' );
				}

				if ( welaunch.optName.required.hasOwnProperty( id ) ) {
					$.each(
						welaunch.optName.required[id],
						function( child ) {
							$.welaunch.required_recursive_hide( child );
						}
					);
				}
			}
		);
	};

	$.welaunch.check_parents_dependencies = function( id ) {
		var show = '';

		if ( welaunch.optName.required_child.hasOwnProperty( id ) ) {
			$.each(
				welaunch.optName.required_child[id],
				function( i, parentData ) {
					var parentValue;

					i = null;

					if ( $( '#' + welaunch.optName.args.opt_name + '-' + parentData.parent ).parents( 'tr:first' ).hasClass( 'hide' ) ) {
						show = false;
					} else if ( $( '#' + welaunch.optName.args.opt_name + '-' + parentData.parent ).parents( 'li:first' ).hasClass( 'hide' ) ) {
						show = false;
					} else {
						if ( false !== show ) {
							parentValue = $.welaunch.getContainerValue( parentData.parent );

							show = $.welaunch.check_dependencies_visibility( parentValue, parentData );
						}
					}
				}
			);
		} else {
			show = true;
		}

		return show;
	};

	$.welaunch.check_dependencies_visibility = function( parentValue, data ) {
		var show       = false;
		var checkValue = data.checkValue;
		var operation  = data.operation;
		var arr;

		if ( $.isPlainObject( parentValue ) ) {
			parentValue = Object.keys( parentValue ).map(
				function( key ) {
					return [key, parentValue[key]];
				}
			);
		}

		switch ( operation ) {
			case '=':
			case 'equals':
				if ( $.isArray( parentValue ) ) {
					$( parentValue[0] ).each(
						function( idx, val ) {
							idx = null;

							if ( $.isArray( checkValue ) ) {
								$( checkValue ).each(
									function( i, v ) {
										i = null;
										if ( $.welaunch.makeBoolStr( val ) === $.welaunch.makeBoolStr( v ) ) {
											show = true;

											return true;
										}
									}
								);
							} else {
								if ( $.welaunch.makeBoolStr( val ) === $.welaunch.makeBoolStr( checkValue ) ) {
									show = true;

									return true;
								}
							}
						}
					);
				} else {
					if ( $.isArray( checkValue ) ) {
						$( checkValue ).each(
							function( i, v ) {
								i = null;

								if ( $.welaunch.makeBoolStr( parentValue ) === $.welaunch.makeBoolStr( v ) ) {
									show = true;
								}
							}
						);
					} else {
						if ( $.welaunch.makeBoolStr( parentValue ) === $.welaunch.makeBoolStr( checkValue ) ) {
							show = true;
						}
					}
				}
				break;

			case '!=':
			case 'not':
				if ( $.isArray( parentValue ) ) {
					$( parentValue[0] ).each(
						function( idx, val ) {
							idx = null;

							if ( $.isArray( checkValue ) ) {
								$( checkValue ).each(
									function( i, v ) {
										i = null;

										if ( $.welaunch.makeBoolStr( val ) !== $.welaunch.makeBoolStr( v ) ) {
											show = true;

											return true;
										}
									}
								);
							} else {
								if ( $.welaunch.makeBoolStr( val ) !== $.welaunch.makeBoolStr( checkValue ) ) {
									show = true;

									return true;
								}
							}
						}
					);
				} else {
					if ( $.isArray( checkValue ) ) {
						$( checkValue ).each(
							function( i, v ) {
								i = null;

								if ( $.welaunch.makeBoolStr( parentValue ) !== $.welaunch.makeBoolStr( v ) ) {
									show = true;
								}
							}
						);
					} else {
						if ( $.welaunch.makeBoolStr( parentValue ) !== $.welaunch.makeBoolStr( checkValue ) ) {
							show = true;
						}
					}
				}
				break;

			case '>':
			case 'greater':
			case 'is_larger':
				if ( parseFloat( parentValue ) > parseFloat( checkValue ) ) {
					show = true;
				}
				break;

			case '>=':
			case 'greater_equal':
			case 'is_larger_equal':
				if ( parseFloat( parentValue ) >= parseFloat( checkValue ) ) {
					show = true;
				}
				break;

			case '<':
			case 'less':
			case 'is_smaller':
				if ( parseFloat( parentValue ) < parseFloat( checkValue ) ) {
					show = true;
				}
				break;

			case '<=':
			case 'less_equal':
			case 'is_smaller_equal':
				if ( parseFloat( parentValue ) <= parseFloat( checkValue ) ) {
					show = true;
				}
				break;

			case 'contains':
				if ( $.isPlainObject( parentValue ) ) {
					parentValue = Object.keys( parentValue ).map(
						function( key ) {
							return [key, parentValue[key]];
						}
					);
				}

				if ( $.isPlainObject( checkValue ) ) {
					checkValue = Object.keys( checkValue ).map(
						function( key ) {
							return [key, checkValue[key]];
						}
					);
				}

				if ( $.isArray( checkValue ) ) {
					$( checkValue ).each(
						function( idx, val ) {
							var breakMe = false;
							var toFind  = val[0];
							var findVal = val[1];

							idx = null;

							$( parentValue ).each(
								function( i, v ) {
									var toMatch  = v[0];
									var matchVal = v[1];

									i = null;

									if ( toFind === toMatch ) {
										if ( findVal === matchVal ) {
											show    = true;
											breakMe = true;

											return false;
										}
									}
								}
							);

							if ( true === breakMe ) {
								return false;
							}
						}
					);
				} else {
					if ( parentValue.toString().indexOf( checkValue ) !== - 1 ) {
						show = true;
					}
				}
				break;

			case 'doesnt_contain':
			case 'not_contain':
				if ( $.isPlainObject( parentValue ) ) {
					arr = Object.keys( parentValue ).map(
						function( key ) {
							return parentValue[key];
						}
					);

					parentValue = arr;
				}

				if ( $.isPlainObject( checkValue ) ) {
					arr = Object.keys( checkValue ).map(
						function( key ) {
							return checkValue[key];
						}
					);

					checkValue = arr;
				}

				if ( $.isArray( checkValue ) ) {
					$( checkValue ).each(
						function( idx, val ) {
							idx = null;

							if ( parentValue.toString().indexOf( val ) === - 1 ) {
								show = true;
							}
						}
					);
				} else {
					if ( parentValue.toString().indexOf( checkValue ) === - 1 ) {
						show = true;
					}
				}
				break;

			case 'is_empty_or':
				if ( '' === parentValue || checkValue === parentValue ) {
					show = true;
				}
				break;

			case 'not_empty_and':
				if ( '' !== parentValue && checkValue !== parentValue ) {
					show = true;
				}
				break;

			case 'is_empty':
			case 'empty':
			case '!isset':
				if ( ! parentValue || '' === parentValue || null === parentValue ) {
					show = true;
				}
				break;

			case 'not_empty':
			case '!empty':
			case 'isset':
				if ( parentValue && '' !== parentValue && null !== parentValue ) {
					show = true;
				}
				break;
		}

		return show;
	};
})( jQuery );
