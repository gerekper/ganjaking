jQuery( function ( $ ) {
	"use strict";

	function classNames( classes ) {
		return classes.filter( Boolean ).join( ' ' );
	}

	/**
	 * Select Alt
	 */
	var selectAltParams = {
		containerSelector: '.yith-wcbk-select-alt__container',
		openedClass      : 'yith-wcbk-select-alt__container--opened',
		unselectedClass  : 'yith-wcbk-select-alt__container--unselected',
		open             : function () {
			$( this ).closest( selectAltParams.containerSelector ).addClass( selectAltParams.openedClass );
		},
		close            : function () {
			$( this ).closest( selectAltParams.containerSelector ).removeClass( selectAltParams.openedClass );
		},
		blur             : function () {
			$( this ).trigger( 'blur' );
		}
	};

	$( document )
		.on( 'focusin', selectAltParams.containerSelector + ' select', selectAltParams.open )
		.on( 'focusout change', selectAltParams.containerSelector + ' select', selectAltParams.close )
		.on( 'change', selectAltParams.containerSelector + ' select', selectAltParams.blur );

	/**
	 * Tip tip
	 */
	$( document ).on( 'yith-wcbk-init-fields:help-tip', function () {
		$( '.yith-wcbk-help-tip:not(.yith-wcbk-help-tip--initialized)' ).each( function () {
			$( this ).tipTip( {
								  'attribute': 'data-tip',
								  'fadeIn'   : 50,
								  'fadeOut'  : 50,
								  'delay'    : 200
							  } );
			$( this ).addClass( 'yith-wcbk-help-tip--initialized' );
		} );
	} ).trigger( 'yith-wcbk-init-fields:help-tip' );

	/**
	 * Selector
	 */
	$( document ).on( 'yith-wcbk-init-fields:selector', function () {
		$( '.yith-wcbk-selector:not(.yith-wcbk-selector--initialized)' ).each(
			function () {

				var selector                         = $( this ),
					placeholder                      = selector.data( 'placeholder' ),
					dataOptions                      = selector.data( 'options' ),
					selected                         = selector.data( 'selected' ),
					isMultiple                       = !!selector.data( 'multiple' ),
					allowClear                       = !!selector.data( 'allow-clear' ),
					items                            = {},
					options                          = {},
					dom                              = {
						field     : selector.find( '.yith-wcbk-selector__field' ),
						head      : selector.find( '.yith-wcbk-selector__head' ),
						label     : selector.find( '.yith-wcbk-selector__label' ),
						labelImage: selector.find( '.yith-wcbk-selector__label__image' ),
						dropdown  : selector.find( '.yith-wcbk-selector__dropdown, .yith-wcbk-selector__list' ),
						close     : selector.find( '.yith-wcbk-selector__close' ),
						clear     : selector.find( '.yith-wcbk-selector__clear' ),
						items     : selector.find( '.yith-wcbk-selector__item' )
					},
					init                             = function () {
						$.each(
							dataOptions,
							function ( idx, theOption ) {
								options[ theOption.key ] = theOption;
							}
						);

						dom.items.each(
							function () {
								var theItem    = $( this ),
									theItemKey = theItem.data( 'key' );

								items[ theItemKey ] = theItem;
							}
						);
					},
					getItem                          = function ( key ) {
						return key in items ? items[ key ] : false;
					},
					isOpened                         = function () {
						return selector.hasClass( 'yith-wcbk-selector--opened' );
					},
					getOptionLabel                   = function ( key ) {
						if ( key in options && 'label' in options[ key ] ) {
							return options[ key ].label;
						}
						return '';
					},
					getOptionImage                   = function ( key ) {
						if ( key in options && 'image' in options[ key ] ) {
							return options[ key ].image;
						}
						return '';
					},
					isSelected                       = function () {
						return isMultiple ? !!selected.length : !!selected;
					},
					updateLabel                      = function () {
						if ( isSelected() ) {
							if ( isMultiple ) {
								var selectedLabels = selected.map( getOptionLabel );
								selectedLabels     = selectedLabels.filter( function ( theValue ) {
									return !!theValue;
								} );

								dom.label.html( selectedLabels.join( ', ' ) );
							} else {
								var labelImage = getOptionImage( selected );
								dom.label.html( getOptionLabel( selected ) );
								dom.labelImage.html( labelImage );

								labelImage ? dom.head.removeClass( 'yith-wcbk-selector__head--no-image' ) : dom.head.addClass( 'yith-wcbk-selector__head--no-image' );
							}

							dom.label.addClass( 'yith-wcbk-selector__label--selected' );
							dom.label.removeClass( 'yith-wcbk-selector__label--placeholder' );
						} else {
							dom.label.html( placeholder );
							dom.label.removeClass( 'yith-wcbk-selector__label--selected' );
							dom.label.addClass( 'yith-wcbk-selector__label--placeholder' );
							dom.head.addClass( 'yith-wcbk-selector__head--no-image' );
						}
					},
					updateItems                      = function () {
						dom.items.removeClass( 'yith-wcbk-selector__item--selected' );
						if ( isSelected() ) {
							if ( isMultiple ) {
								$.each( selected, function ( idx, currentSelected ) {
									var theItem = getItem( currentSelected );
									if ( theItem ) {
										theItem.addClass( 'yith-wcbk-selector__item--selected' );
									}
								} );
							} else {
								var theItem = getItem( selected );
								if ( theItem ) {
									theItem.addClass( 'yith-wcbk-selector__item--selected' );
								}
							}
						}
					},
					updateMain                       = function () {
						if ( isSelected() ) {
							selector.addClass( 'yith-wcbk-selector--selected' );
						} else {
							selector.removeClass( 'yith-wcbk-selector--selected' );
						}
					},
					setValue                         = function ( newValue ) {
						selected = newValue;
						selector.data( 'selected', selected );
						update();
					},
					update                           = function () {
						updateItems();
						updateLabel();
						updateMain();

						dom.field.val( selected ).trigger( 'change' );
					},
					getNewValueByCurrentSelection    = function ( selectedKey ) {
						if ( isMultiple ) {
							var newSelected = selected.slice(),
								idx         = newSelected.indexOf( selectedKey );
							if ( idx > -1 ) {
								newSelected.splice( idx, 1 );
							} else {
								newSelected.push( selectedKey );
							}
							return newSelected;
						} else {
							return selectedKey;
						}
					},
					updateSelectedByCurrentSelection = function ( selectedKey ) {
						setValue( getNewValueByCurrentSelection( selectedKey ) );
					},
					clear                            = function () {
						if ( allowClear ) {
							setValue( isMultiple ? [] : '' );
							handleClose();
						}
					},
					maybeCloseOnChange               = function () {
						if ( !isMultiple ) {
							handleClose();
						}
					},
					handleToggle                     = function () {
						if ( isOpened() ) {
							handleClose();
						} else {
							handleOpen();
						}
					},
					handleOpen                       = function () {
						selector.addClass( 'yith-wcbk-selector--opened' );
						var dropdownRect = dom.dropdown.get( 0 ).getBoundingClientRect(),
							headRect     = dom.head.get( 0 ).getBoundingClientRect(),
							isGoodAbove  = headRect.y > dropdownRect.height,
							isGoodBelow  = !isGoodAbove || window.innerHeight >= dropdownRect.y + dropdownRect.height;

						if ( isGoodBelow ) {
							selector.addClass( 'yith-wcbk-selector--opened--below' );
							selector.removeClass( 'yith-wcbk-selector--opened--above' );
						} else {
							selector.addClass( 'yith-wcbk-selector--opened--above' );
							selector.removeClass( 'yith-wcbk-selector--opened--below' );
						}
					},
					handleClose                      = function () {
						selector.removeClass( 'yith-wcbk-selector--opened' );
						selector.removeClass( 'yith-wcbk-selector--opened--below' );
						selector.removeClass( 'yith-wcbk-selector--opened--above' );
					},
					handleChange                     = function ( e ) {
						var theItem    = $( e.target ).closest( '.yith-wcbk-selector__item' ),
							theItemKey = theItem.data( 'key' );

						updateSelectedByCurrentSelection( theItemKey );
						maybeCloseOnChange();
					},
					handleClick                      = function ( e ) {
						if ( dom.close.length && dom.close.get( 0 ).contains( e.target ) ) {
							e.preventDefault();
							handleClose();
						} else if ( dom.clear.length && dom.clear.get( 0 ).contains( e.target ) ) {
							e.preventDefault();
							clear();
						} else if ( dom.dropdown.length && dom.dropdown.get( 0 ).contains( e.target ) ) {
							// do nothing.
						} else if ( dom.head.length &&  dom.head.get( 0 ).contains( e.target ) ) {
							e.preventDefault();
							handleToggle();
						} else {
							handleClose();
						}
					};

				init();

				// Use Event Listener directly, to avoid bubbling (and issues with e.stopPropagation).
				document.addEventListener( 'click', handleClick, true );

				dom.items.on( 'click', handleChange );

				selector.addClass( 'yith-wcbk-selector--initialized' );
			}
		);
	} ).trigger( 'yith-wcbk-init-fields:selector' );

	/**
	 * Select list
	 */
	$( document ).on( 'yith-wcbk-init-fields:select-list', function () {
		$( '.yith-wcbk-select-list:not(.yith-wcbk-select-list--initialized)' ).each(
			function () {

				var selectList        = $( this ),
					emptyMessage      = selectList.data( 'empty-message' ),
					classes           = {
						option        : 'yith-wcbk-select-list__option',
						optionSkeleton: 'yith-wcbk-select-list__option-skeleton',
						selected      : 'yith-wcbk-select-list__option--selected',
						emptyMessage  : 'yith-wcbk-select-list__empty-message'
					},
					dom               = {
						field    : selectList.find( 'select.yith-wcbk-select-list__field' ),
						container: selectList.find( '.yith-wcbk-select-list__options' )
					},
					setIsLoading      = function ( newValue ) {
						selectList.data( 'is-loading', newValue );
					},
					isLoading         = function () {
						return selectList.data( 'is-loading' );
					},
					init              = function () {
						updateFromField();

						dom.field.on( 'yith-wcbk-select-list:update', updateFromField );
						dom.field.on( 'change', handleFieldChange );
						dom.container.on( 'click', '.' + classes.option, onOptionClick );
					},
					updateFromField   = function () {
						dom.container.html( '' );
						var fieldOptions = dom.field.find( 'option[value!=""]' );
						if ( fieldOptions.length ) {
							fieldOptions.each( function () {
								var optionClass = classNames( [classes.option, $( this ).is( ':selected' ) ? classes.selected : ''] ),
									theOption   = $( '<div />' );
								theOption.addClass( optionClass );
								theOption.attr( 'data-value', $( this ).val() );
								theOption.html( $( this ).html() );
								dom.container.append( theOption );
							} );
						} else if ( isLoading() ) {
							var skeletonOption = $( '<div />' ).addClass( classes.optionSkeleton );
							dom.container.append( skeletonOption.clone() );
							dom.container.append( skeletonOption.clone() );
							dom.container.append( skeletonOption.clone() );
						} else if ( emptyMessage ) {
							dom.container.append( $( '<div />' ).addClass( classes.emptyMessage ).html( emptyMessage ) );
						}
						setIsLoading( false );
					},
					handleFieldChange = function () {
						var optionValue = dom.field.val();
						dom.container.find( '.' + classes.selected ).removeClass( classes.selected );
						dom.container.find( '.' + classes.option + '[data-value="' + optionValue + '"]' ).addClass( classes.selected );
					},
					onOptionClick     = function ( e ) {
						var theOption   = $( e.target ),
							optionValue = theOption.data( 'value' );
						dom.field.val( optionValue );
						dom.field.trigger( 'change' );
					};

				init();

				selectList.addClass( 'yith-wcbk-select-list--initialized' );
			}
		);
	} ).trigger( 'yith-wcbk-init-fields:select-list' );
} );

