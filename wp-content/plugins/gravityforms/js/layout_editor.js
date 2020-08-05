( function ( $ ) {

	/**
	 * Get the group ID of the targeted element.
	 *
	 * @param {string} groupId The ID of the group to be set on the targeted element.
	 *
	 * @returns {jQuery}
	 */
	$.fn.setGroupId = function ( groupId ) {

		this.attr( 'data-groupId', groupId );

		this.each( function () {
			var field = getFieldByElement( $( this ) );
			if ( field ) {
				field.layoutGroupId = groupId;
			}
		} );

		return this;
	};

	/**
	 * Set the grid column span CSS property of the targeted element.
	 *
	 * @param {number} span The number of columns the targeted element should span.
	 *
	 * @returns {jQuery}
	 */
	$.fn.setGridColumnSpan = function ( span ) {

		if ( span === null ) {
			this.css( 'grid-column', 'auto / auto' );
			return this;
		}

		var field;

		this.css( 'grid-column', 'span {0}'.format( span ) );

		this.each( function () {
			// Spacer fields are pseudo-fields; they are generated when the last field in the group is resized and are
			// rendered based on that field's layoutSpacerGridColumnSpan property.
			if ( $( this ).hasClass( 'spacer' ) ) {
				var $prev = $( this ).prev( '.gfield' );
				field = getFieldByElement( $prev );
				field.layoutSpacerGridColumnSpan = span;
			} else {
				field = getFieldByElement( $( this ) );
				if ( field ) {
					field.layoutGridColumnSpan = span;
				}
			}
		} );

		return this;
	};

	/**
	 * Get the grid column span CSS property of the targeted element.
	 *
	 * @returns {number}
	 */
	$.fn.getGridColumnSpan = function () {
		// Use 'gridColumnStart' instead of 'grid-column' as Firefox returns null for the latter.
		var span = parseInt( this.css( 'gridColumnStart' ).split( ' ' )[ 1 ] );
		if ( isNaN( span ) && typeof columnCount !== 'undefined' ) {
			span = columnCount;
		}
		return span;
	};

	/**
	 * Replace placeholders in the targeted string with passed values.
	 *
	 * @returns {string}
	 */
	String.prototype.format = function () {
		var args = arguments;
		return this.replace( /{(\d+)}/g, function ( match, number ) {
			return typeof args[ number ] != 'undefined' ? args[ number ] : match;
		} );
	};

	var $editorContainer = $( '#form_editor_fields_container' ),
		$editor = $( '.gform_editor' ),
		$container = $( '#gform_fields' ),
		$noFields = $( '#no-fields' ),
		$sidebar = $( '.editor-sidebar' ),
		$button = $( '.gfield-field-action' ),
		$elem = null,
		fieldButtonsSelector = '.add-buttons button';

	/**
	 * The max column count determined by the fields container's grid CSS.
	 * @type {number}
	 */
	var columnCount = getComputedStyle( $container[ 0 ] )[ 'grid-template-columns' ].split( ' ' ).length,
		/**
		 * The minimum number of columns a field can span.
		 * @type {number}
		 */
		min = columnCount / 4,
		/**
		 * The maximum number of columns a field can span.
		 * @type {number}
		 */
		max = null,
		/**
		 * A flag to determine if the field was dropped in droparea that appears when the form has no fields.
		 * @type {boolean}
		 */
		isNoFieldsDrop = false,
		/**
		 * The group ID of the last deleted field. This is used to resize the remaining fields in that group once the field has been removed from the DOM.
		 * @type {boolean}
		 */
		deletedFieldGroupId;

	// Initialize fields for layout editor.
	initElement( $elements() );

	// Initialize field buttons.
	initFieldButtons( $( fieldButtonsSelector ) );

	// Initialize the No Fields droparea.
	$noFields.droppable( {
		accept: fieldButtonsSelector,
		activate: function ( event, ui ) {
			$( this ).addClass( 'ready' )
		},
		over: function () {
			$( this ).addClass( 'hovering' );
		},
		out: function () {
			$( this ).removeClass( 'hovering' );
		},
		drop: function () {
			isNoFieldsDrop = true;
			$( this ).removeClass( 'hovering' );
		},
		deactivate: function () {
			$( this ).removeClass( 'ready' );
		}
	} );

	// Clear field selection when clicking off of any field.
	$editorContainer.on( 'click', function () {
		clearFieldSelection();
	} );

	// Handle adding a new field.
	$( document ).on( 'gform_field_added', function ( event, form, field ) {

		var $field = $( '#field_' + field.id );

		// This field was added by clicking.
		if ( $elem === null ) {

			$field.setGroupId( getGroupId() );

		}
		// This field was added by dragging into the editor.
		else {

			moveByTarget( $field, $indicator().data( 'target' ), $indicator().data( 'where' ) );

			$elem.remove();
			$elem = null;

		}

		$indicator().remove();

		initElement( $field );

	} );

	// Save the group ID of the deleted field.
	$( document ).on( 'gform_field_deleted', function ( event, form, fieldId ) {
		deletedFieldGroupId = getGroupId( $( '#field_' + fieldId ) );
	} );

	// Handle resizing the group after the deleted field has been fully removed from the DOM.
	gform.addAction( 'gform_after_field_removed', function ( form, fieldId ) {
		resizeGroup( deletedFieldGroupId );
	} );

	// Handle duplicating a field.
	gform.addAction( 'gform_field_duplicated', function ( form, field, $field, sourceFieldId ) {

		var $source = $( '#field_' + sourceFieldId );
		var $sourceGroup = getGroup( getGroupId( $source ) );

		// Add duplicated fields *after* the last field in its group so that it will always appear on a new row.
		$sourceGroup.last().after( $field );

		$field
			.setGridColumnSpan( columnCount )
			.setGroupId( getGroupId() );

		initElement( $field );

	} );

	// Re-initialize the field after it's markup is refreshed (e.g. after the description is updated).
	gform.addAction( 'gform_after_refresh_field_preview', function ( fieldId ) {
		initElement( $( '#field_' + fieldId ) );
	} );

	/**
	 * Initialize a form field so that it can be dragged and resized.
	 *
	 * @param {jQuery} $element The element(s) to be initialized.
	 */
	function initElement( $element ) {

		if ( $element.hasClass( 'ui-draggable' ) ) {
			$element
				.draggable( 'destroy' )
				.resizable( 'destroy' );
		}

		$element
			.draggable( {
				helper: 'clone',
				zIndex: 999,
				handle: '.gfield-drag',
				create: function ( event, ui ) {

					if ( isSpacer( $( this ) ) ) {
						return;
					}

					var groupId,
						fieldId = $( this ).attr( 'id' ).replace( 'field_', '' ),
						field = fieldId ? GetFieldById( fieldId ) : false;

					if ( field && field.layoutGroupId && ! $editor.hasClass( 'gform_legacy_markup' ) ) {
						groupId = field.layoutGroupId;
					}
					// This applies when initializing a newly added field.
					else if ( ! getGroupId( $( this ), false ) ) {
						groupId = getGroupId();
					}

					$( this ).setGroupId( groupId );

				},
				start: function ( event, ui ) {

					$container.addClass( 'dragging' );
					$elem = $( ui.helper.context );
					$elem.addClass( 'placeholder' );

				},
				drag: function ( event, ui ) {

					// Match the helper to the current elements size.
					ui.helper
						.width( $elem.width() )
						.height( $elem.height() )
						// Firefox has trouble positioning the dragged element when it still has it's grid-column property set.
						.setGridColumnSpan( null );

					handleDrag( event, ui, ui.position.top, ui.position.left );

				},
				stop: function ( event, ui ) {

					$container.removeClass( 'dragging' );
					$elem.removeClass( 'placeholder' );
					$elements().removeClass( 'hovering' );

					if ( $indicator().data( 'target' ) ) {
						moveByTarget( $elem, $indicator().data( 'target' ), $indicator().data( 'where' ) );
					}

					$indicator().remove();

					ui.helper.remove();

				}
			} )
			.resizable( {
				handles: 'e',
				start: function () {
					max = null;
					$container.addClass( 'resizing' );
				},
				resize: function ( event, ui ) {

					var columnWidth = $container.outerWidth() / columnCount,
						$item = ui.element,
						width = $item.outerWidth(),
						span = Math.max( min, Math.round( width / columnWidth ) ),
						prevSpan = $item.getGridColumnSpan(),
						$group = getGroup( getGroupId( $item ) ),
						lastInGroup = isLastInGroup( $item, $group ),
						$spacer = $group.filter( '.spacer' ),
						$sibling = lastInGroup && ! $spacer.length ? null : $item.next(),
						siblingSpan;

					/**
					 * Calculate the max on the first move of a resize and then rely on the set max until a new resize is initialized.
					 * Attempting to recalculate the max on each move results in some odd calculations...
					 */
					if ( max === null ) {
						if ( $group.length > 1 ) {
							siblingSpan = $sibling ? getGroupGridColumnSpan( $sibling ) : 0;
							max = prevSpan + siblingSpan;
						} else {
							max = columnCount;
						}
					}

					/**
					 * We've calculated the desired span based on the physical size of the field. Now let's adjust it to
					 * make sure it's not too big or too small.
					 *
					 * If the field is in a group, we will deduct the minimum span from the max to always save room for
					 * the field to it's right. If it the last field, we do not have to save this room.
					 */
					span = getAdjustedGridColumnSpan( span, min, max - ( $group.length > 1 && ! lastInGroup ? min : 0 ) );

					$().add( ui.helper ).add( ui.element )
						// Resizable will set a width with each increment, we have to deliberately override this.
						.css( 'width', 'auto' )
						.setGridColumnSpan( span );

					if ( $sibling ) {
						siblingSpan = max - span;
						$sibling
							.css( 'width', 'auto' )
							.setGridColumnSpan( siblingSpan );
					}

					// If resizing a field to it's max allowable span, remove the spacer.
					if ( span == columnCount || span == max ) {
						removeSpacer( $spacer );
					}
					// Insert spacer when resizing a field with no field to its right.
					else if ( lastInGroup && ! $spacer.length && getGroupGridColumnSpan( $group ) < columnCount ) {
						addSpacer( $item, getGroupId( $item ), 1 );
					}

				},
				stop: function () {
					$container.removeClass( 'resizing' );
				}
			} );

	}

	/**
	 * Initialize the field buttons so they can be dragged over the layout editor.
	 *
	 * @param {jQuery} $buttons All field buttons.
	 */
	function initFieldButtons( $buttons ) {
		$buttons
			.draggable( {
				helper: 'clone',
				revert: function () {
					// @todo Return true when field will not be added. This is low priority polish.
					return false;
				},
				cancel: false,
				appendTo: $container,
				containment: 'document',
				start: function ( event, ui ) {

					clearFieldSelection();

					$editorContainer.css( 'z-index', 2 );

					if ( gf_vars[ 'currentlyAddingField' ] == true ) {
						return false;
					}

					// Match the helper to the current elements size.
					ui.helper
						.width( $( ui.helper.context ).width() )
						.height( $( ui.helper.context ).height() );

					$container.addClass( 'dragging' );

					$elem = $( ui.helper.context ).clone();
					$elem.addClass( 'placeholder' );

					$( this ).css( 'opacity', 0.5 );

				},
				drag: function ( event, ui ) {

					// When form has no fields, there is only one place the field can be dragged...
					if ( ! form.fields.length ) {
						return;
					}

					/**
					 * New field buttons are dragged relative to #wpbody so their position needs to be adjusted to work the
					 * the same way as dragging an existing field (which is relative to #gform_fields).
					 */
					var helperTop = ui.position.top - 0 + ( ui.helper.outerHeight() / 2 ),
						helperLeft = ui.position.left - 0 + ( ui.helper.outerWidth() / 2 );

					handleDrag( event, ui, helperTop, helperLeft );

				},
				stop: function ( event, ui ) {

					$( this ).css( 'opacity', 1 );
					$editorContainer.css( 'z-index', 1 );
					$container.removeClass( 'dragging' );

					var isAddingField = false;

					// Make sure the *entire* button has been dragged into the fields area before we add a field.
					if ( ! form.fields.length && isNoFieldsDrop ) {
						isNoFieldsDrop = false;
						isAddingField = addField( ui.helper.data( 'type' ) );
					} else if ( form.fields.length && $indicator( false ).data( 'target' ) ) {
						isAddingField = addField( ui.helper.data( 'type' ) );
					}

					// If we're not adding a new field, remove our placeholder element.
					if ( ! isAddingField ) {
						$indicator( false ).remove();
						$elem.remove();
						$elem = null;
					}

				}
			} )
			.on( 'click keypress', function () {
				$elem = null;
			} );
	}

	/**
	 * Handle placing the indicator when a field is dragged over the layout editor.
	 *
	 * @param {Event}  event
	 * @param {object} ui         jQuery UI helper object which manages the current state.
	 * @param {number} helperTop  The top position of the element being dragged.
	 * @param {number} helperLeft The left position of the element being dragged.
	 */
	function handleDrag( event, ui, helperTop, helperLeft ) {

		$elements().removeClass( 'hovering' );

		if ( ! isInEditorArea( helperLeft, helperTop ) ) {
			$indicator( false ).remove();
			return;
		}

		// Check if field is dragged *above* all other fields.
		if( helperTop < 0 ) {
			$indicator()
				.css( {
					top: -10,
					left: 0,
					height: '4px',
					width: $container.outerWidth()
				} )
				.data( {
					where: 'top',
					target: $elements().first()
				} );
			return;
		}
		// Check if field is dragged *below* all other fields.
		else if ( helperTop > $container.outerHeight() ) {
			$indicator()
				.css( {
					top: $container.outerHeight() + 6,
					left: 0,
					height: '4px',
					width: $container.outerWidth()
				} )
				.data( {
					where: 'bottom',
					target: $elements().last()
				} );
			return;
		}

		$elements()
			.not( ui.helper )
			.not( ui.helper.context )
			.each( function () {

				var $target = $( this ),
					sibPos = $target.position(),
					sibArea = {
						top: sibPos.top,
						right: sibPos.left + $target.outerWidth(),
						bottom: sibPos.top + $target.outerHeight(),
						left: sibPos.left
					};

				if ( ! isInArea( helperLeft, helperTop, sibArea ) ) {
					return;
				}

				$target.addClass( 'hovering' );

				var where = whichArea( helperLeft, helperTop, sibArea, $target.outerWidth(), $target.outerHeight() ),
					targetGroupId = getGroupId( $target ),
					$targetGroup = getGroup( targetGroupId ),
					isGroupMaxed = $targetGroup.length >= ( columnCount / min );

				if ( where === 'left' || where === 'right' ) {
					// Columns are not supported in Legacy markup or with Page or Section fields.
					if ( ! areColumnsEnabled( $target, $elem ) ) {
						return;
					} else if ( isGroupMaxed ) {
						return;
					}
				}

				if ( isSpacer( $target ) ) {
					$target = $target.prev();
					sibPos = $target.position();
					where = 'right';
				}

				$indicator().data( {
					where: where,
					target: $target
				} );

				// Where on the child field has the helper been dragged?
				switch ( where ) {
					case 'left':

						$indicator()
							.css( {
								top: sibPos.top,
								left: sibPos.left - 10,
								height: $target.outerHeight(),
								width: '4px'
							} );

						return false;
					case 'right':

						$indicator().css( {
							top: sibPos.top,
							left: sibPos.left + $target.outerWidth() + 6,
							right: 'auto',
							height: $target.outerHeight(),
							width: '4px'
						} );

						return false;
					case 'bottom':

						$indicator().css( {
							top: sibPos.top + $target.outerHeight() + 6,
							left: 0,
							height: '4px',
							width: '100%'
						} );

						return false;
					case 'top':

						$indicator().css( {
							top: sibPos.top - 10,
							left: 0,
							height: '4px',
							width: '100%'
						} );

						return false;
				}

			} );

	}

	/**
	 * Determine whether columns are enabled based on the current element and the target over which it is being dragged.
	 *
	 * @param {jQuery} $target The element over which the dragged element is currently positioned.
	 * @param {jQuery} $elem   The element that is being dragged.
	 *
	 * @returns {boolean}
	 */
	function areColumnsEnabled( $target, $elem ) {

		if ( $editor.hasClass( 'gform_legacy_markup' ) ) {
			return false;
		}

		if ( $target.hasClass( 'gpage' ) || $target.hasClass( 'gsection' ) || $target.hasClass( 'gform_hidden' ) ) {
			return false;
		}

		if ( $elem.hasClass( 'gpage' ) || $elem.hasClass( 'gsection' ) || $elem.hasClass( 'gform_hidden' ) ) {
			return false;
		}

		if ( $elem.is( 'button' ) && ( $.inArray( $elem.val().toLowerCase(), [ 'page', 'section' ] ) !== -1 ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Determine whether the given coordinates are in the specified area.
	 *
	 * @param {number} x    The left position of the coordinate.
	 * @param {number} y    The top position of the coordinate.
	 * @param {object} area An object of top, right, bottom and left positions.
	 *
	 * @returns {boolean}
	 */
	function isInArea( x, y, area ) {
		return y < area.bottom && y > area.top && x < area.right && x > area.left;
	}

	/**
	 * Determine which portion of a specified area the given coordinates are in.
	 *
	 * @param {number} x      The left position of the coordinate.
	 * @param {number} y      The top position of the coordinate.
	 * @param {object} area   An object of top, right, bottom and left positions.
	 * @param {number} width  The width of the given area.
	 * @param {number} height The height of the given area.
	 *
	 * @returns {string}
	 */
	function whichArea( x, y, area, width, height ) {

		var thresholdLeft = area.left + ( width / 2 ),
			thresholdRight = area.right - ( width / 2 ),
			thresholdTop = area.top + ( height / 5 ),
			thresholdBottom = area.bottom - ( height / 5 );

		if ( y > area.top && y < thresholdTop ) {
			return 'top';
		} else if ( y < area.bottom && y > thresholdBottom ) {
			return 'bottom';
		} else if ( x > area.left && x < thresholdLeft ) {
			return 'left';
		} else if ( x < area.right && x > thresholdRight ) {
			return 'right';
		}

		return 'center';
	}

	/**
	 * Determine whether the given coordinates are in the area of the layout editor.
	 *
	 * @param {number} x The left position of the coordinate.
	 * @param {number} y The top position of the coordinate.
	 *
	 * @returns {boolean}
	 */
	function isInEditorArea( x, y ) {
		var editorOffset = $editorContainer.offset(),
			containerOffset = $container.offset(),
			offsetTop = containerOffset.top - editorOffset.top,
			offsetLeft = containerOffset.left - editorOffset.left,
			buttonWidth = $button.outerWidth(),
			editorArea = {
				top: -offsetTop + buttonWidth,
				right: -offsetLeft + $editorContainer.outerWidth() - $sidebar.outerWidth() - buttonWidth,
				bottom: -offsetTop + $editorContainer.outerHeight(),
				left: -offsetLeft
			};

		return y > editorArea.top && y < editorArea.bottom && x > editorArea.left && x < editorArea.right;
	}

	/**
	 * Move the given element based on the specified target and location.
	 *
	 * @param {jQuery} $elem   The element to be moved.
	 * @param {jQuery} $target The element over which the dragged element was last positioned.
	 * @param {string} where   The area of the target element over which the element was last positioned.
	 */
	function moveByTarget( $elem, $target, where ) {

		if ( ! $target ) {
			return;
		}

		var targetSpan, splitSpan, $targetGroup, $resizeGroup, groupId, sourceGroupId,
			movingIntoTargetGroup, $spacer;

		sourceGroupId = getGroupId( $elem );
		groupId = getGroupId( $target );
		$targetGroup = getGroup( groupId );

		if ( isSpacer( $target ) ) {
			$spacer = $target;
			$target = $target.prev();
		} else if ( isSpacer( $target.next() ) && $targetGroup.index( $target.next() ) !== false ) {
			$spacer = $target.next();
		}

		if ( $spacer ) {
			targetSpan = $spacer.getGridColumnSpan();
			removeSpacer( $spacer );
			$targetGroup = getGroup( groupId );
		}

		movingIntoTargetGroup = where === 'left' || where === 'right';

		if ( where == 'top' ) {
			$target = $targetGroup.first();
		} else if ( where == 'bottom' ) {
			$target = $targetGroup.last();
		}

		if ( where == 'top' || where == 'left' ) {
			$elem.insertBefore( $target );
		} else {
			$elem.insertAfter( $target );
		}

		if ( ! movingIntoTargetGroup ) {

			groupId = getGroupId();
			$elem.setGridColumnSpan( columnCount );

		} else {

			if ( targetSpan ) {
				$resizeGroup = $elem;
				splitSpan = targetSpan;
			} else if ( isEvenSplit( $targetGroup ) ) {
				splitSpan = columnCount / ( $targetGroup.length + 1 ); // +1 for the element about to be added to this group.
				$resizeGroup = $targetGroup.add( $elem );
			} else {
				targetSpan = $target.getGridColumnSpan();
				splitSpan = targetSpan / 2;
				$resizeGroup = $target.add( $elem );
			}

			if ( parseInt( splitSpan ) == splitSpan ) {
				$resizeGroup.setGridColumnSpan( splitSpan );
			}
			// Handle non-even spans by making one smaller than the other. Should only happen in non-even splits.
			else {
				var floor = Math.floor( splitSpan ),
					ceil = Math.ceil( splitSpan );
				$elem.setGridColumnSpan( floor );
				$target.setGridColumnSpan( ceil );
			}

		}

		$elem.setGroupId( groupId );

		// Reset sizes on the group the element has been removed from.
		resizeGroup( sourceGroupId );

	}

	/**
	 * Get the group ID of the given element or generate a new group ID if none exists.
	 *
	 * @param {jQuery}  $elem        The element for which we are getting the group ID.
	 * @param {boolean} autoGenerate Whether or not a group ID should be auto-generated if no group ID exists.
	 *
	 * @returns {string}
	 */
	function getGroupId( $elem, autoGenerate ) {
		var groupId;
		if ( typeof $elem !== 'undefined' ) {
			groupId = $elem.attr( 'data-groupId' );
		}
		if ( ! groupId && ( autoGenerate || typeof autoGenerate === 'undefined' ) ) {
			groupId = 'xxxxxxxx'.replace( /[xy]/g, function ( c ) {
				var r = Math.random() * 16 | 0, v = c == 'x' ? r : r & 0x3 | 0x8;
				return v.toString( 16 );
			} );
		}
		return groupId;
	}

	/**
	 * Get a group of field elements by the given group ID.
	 *
	 * @param {string} groupId The ID of the group to be set on the targeted element.
	 *
	 * @returns {jQuery}
	 */
	function getGroup( groupId ) {
		return $elements()
			.filter( '[data-groupId="{0}"]'.format( groupId ) )
			.not( '.ui-draggable-dragging' );
	}

	/**
	 * Get the grid column span value adjusted by a specified min and max value.
	 *
	 * @param {number} span The desired number columns to be spanned.
	 * @param {number} min  The minimum number of columns that must be spanned.
	 * @param {number} max  The maximum number of columns that can be spanned.
	 *
	 * @returns {number}
	 */
	function getAdjustedGridColumnSpan( span, min, max ) {
		return Math.max( min, Math.min( max, span ) );
	}

	/**
	 * Get the combined grid column span value of the given group.
	 *
	 * @param {jQuery} $group A group of field elements making up a row.
	 *
	 * @returns {number}
	 */
	function getGroupGridColumnSpan( $group ) {
		var span = 0;
		$group.each( function () {
			span += $( this ).getGridColumnSpan();
		} );
		return span;
	}

	/**
	 * Determine whether the grid column span for the given group is the same for all elements in the group.
	 *
	 * @param {jQuery} $group A group of field elements making up a row.
	 *
	 * @returns {boolean}
	 */
	function isEvenSplit( $group ) {

		var baseSpan = $group.first().getGridColumnSpan(),
			isEvenSplit = true;

		$group.each( function () {
			var span = $( this ).getGridColumnSpan();
			if ( span !== baseSpan ) {
				isEvenSplit = false;
				return false;
			}
		} );

		return isEvenSplit;
	}

	/**
	 * Resize the elements in a group based on the provided group ID.
	 *
	 * @param {string} groupId The ID of the group to be set on the targeted element.
	 */
	function resizeGroup( groupId ) {

		var $group = getGroup( groupId ),
			splitSpan = columnCount / ( $group.length );

		$group.setGridColumnSpan( splitSpan );

	}

	/**
	 * Determine whether the given element is the last element in the specified group.
	 *
	 * @param {jQuery} $elem  The element to check if it is the last in the specified group.
	 * @param {jQuery} $group The group of field elements to which the given element belongs.
	 *
	 * @returns {boolean}
	 */
	function isLastInGroup( $elem, $group ) {
		$group = $group.not( '.spacer' );
		return $group.length === 1 || $group.last()[ 0 ] === $elem[ 0 ];
	}

	/**
	 * Insert a Spacer field after the given field element.
	 *
	 * @param {jQuery} $field  The field element after which the Spacer should be inserted.
	 * @param {string} groupId The ID of the group to be set on the targeted element.
	 * @param {number} span    The number of columns the Spacer should span.
	 *
	 * @returns {jQuery}
	 */
	function addSpacer( $field, groupId, span ) {

		var $spacer = $( '<div class="spacer gfield"></div>' )
			.setGroupId( groupId )
			.setGridColumnSpan( span );

		$field.after( $spacer );

		return $spacer;
	}

	/**
	 * Remove the given Spacer field from the DOM.
	 *
	 * @param {jQuery} $spacer A field element representing a Spacer field.
	 */
	function removeSpacer( $spacer ) {
		$spacer
			.setGridColumnSpan( 0 )
			.remove();
	}

	/**
	 * Determine whether the given element is a Spacer field.
	 *
	 * @param {jQuery} $elem The element for which to determine if it is a Spacer field.
	 *
	 * @returns {boolean}
	 */
	function isSpacer( $elem ) {
		return $elem.filter( '.spacer' ).length > 0;
	}

	/**
	 * Get the Gravity Forms field object based on the given element.
	 *
	 * @param {jQuery} $elem The element to be used to fetch the field object.
	 *
	 * @returns {object|boolean}
	 */
	function getFieldByElement( $elem ) {
		var id = $elem.attr( 'id' );
		var fieldId = id && id.indexOf( 'field_' ) !== -1 ? String( id ).replace( 'field_', '' ) : false;
		return fieldId ? GetFieldById( fieldId ) : false;
	}

	/**
	 * Add a new field of the specified type to the form.
	 *
	 * @param {string} type The field type to add to the form.
	 *
	 * @returns {boolean}
	 */
	function addField( type ) {
		return StartAddField( type, Math.max( 0, $container.children().index( $elem ) ) );
	}

	/**
	 * Deselect the currently selected field.
	 */
	function clearFieldSelection() {
		$elements().removeClass( 'field_selected' );
		$( '.sidebar' ).tabs( 'option', 'active', 0 );
		HideSettings();
	}

	/**
	 * Get all field elements in current form.
	 *
	 * @returns {jQuery|[]}
	 */
	function $elements() {
		return $container.find( '.gfield' );
	}

	/**
	 * Create or return the current Indicator. The Indicator indicates where the currently dragged field will be placed when dropped.
	 *
	 * @param {boolean} create Whether or not an indicator should be created if it does not exist.
	 *
	 * @returns {jQuery}
	 */
	function $indicator( create ) {

		create = typeof create === 'undefined';

		var $indicator = $( '#indicator' );

		if ( ! $indicator.length && create ) {
			$indicator = $( '<div id="indicator"></div>' );
			$container.append( $indicator );
		}

		return $indicator;
	}

} )( jQuery );