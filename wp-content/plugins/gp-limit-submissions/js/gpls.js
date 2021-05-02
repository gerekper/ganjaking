( function( $ ) {

	init();

	function init() {

		// check if this is the limit rule setting page
		var isLimitRuleSettingPage = $( '.ruleset' );
		if ( isLimitRuleSettingPage.length == 0 ) {
			return;
		}

		// Remove loading class to fully render UI.
		$( '.gpls-loading' ).removeClass( 'gpls-loading' );

		setRuleGroupTemplate();
		setupRepeater();
		setDefaultData();
		ruleTimePeriodChange();
		ruleEmbedChange();
		ruleIpChange();
		setupSubmitHandler();
		setupTimePeriod();
		setupDebug();

		jQuery.validator.addMethod( 'numberOrMergeTag', function( value, element ) {
			return this.optional( element ) || ! isNaN( parseInt( value ) ) || /^{.+}$/.test( value.trim() );
		}, 'Please enter a number (or a valid merge tag).' );

	}

	function setupSelect2( ruleGroup ) {
		var select2Options = {};
		if ( ! isLegacyUI() ) {
			select2Options.width = '100%';
		}
		$( ruleGroup ).find( 'select:not( .select2-hidden-accessible )' ).select2( select2Options );
	}

	function setupTimePeriod() {
		var timePeriod = $( '#rule_time_period_type' ).val();
		timePeriodFieldToggle( timePeriod );
	}

	function setupSubmitHandler() {
		$( '#gform-settings' ).validate();

		$( "#rule_group_name" ).rules( "add", {
			required: true,
			minlength: 1,
			messages: {
				required: "Required input",
				minlength: jQuery.validator.format( "Please enter a Rule Group Name" )
			}
		});

		$( "#rule_submission_limit" ).rules( "add", {
			required: true,
			numberOrMergeTag: true,
			messages: {
				required: "Required",
				minlength: jQuery.validator.format( "Please enter a number" )
			}
		});

		$( "#rule_time_period_value" ).rules( "add", {
			number: true,
			min: 0,
			step: 1,
			messages: {
				minlength: jQuery.validator.format( "Please enter a number" )
			}
		});

		$( ".rule_embed_url_value_full" ).rules( "add", {
			required: true,
			url: true,
			messages: {
				required: "Required",
			}
		});

		$( ".rule_embed_url_value_post" ).rules( "add", {
			required: true,
			number: true,
			messages: {
				required: "Required",
			}
		});

	}

	function embedUrlFieldToggle( embedUrlType, $row ) {

		if ( embedUrlType === 'full' ) {
			getSiblingSettings( $row, '.rule_embed_url_value_post + span' ).hide();
			getSiblingSettings( $row, '.rule_embed_url_value_full' ).show();
		}

		if ( embedUrlType === 'post_id' ) {
			getSiblingSettings( $row, '.rule_embed_url_value_full' ).hide();
			getSiblingSettings( $row, '.rule_embed_url_value_post + span' ).show();
		}

		if ( embedUrlType === 'all' ) {
			getSiblingSettings( $row, '.rule_embed_url_value_full' ).hide();
		}

		// reset validation errors
		// @todo
		$row.siblings( 'label.error' ).hide();

	}

	/* user selects the type of embed_url */
	function ruleTimePeriodChange() {
		$( '#rule_time_period_type' ).change( function() {
			var type = $( this ).val();
			timePeriodFieldToggle( type );
		} );
	}

	function timePeriodFieldToggle( type ) {

		// time period
		if ( type == 'time_period' ) {
			if ( isLegacyUI() ) {
				$( '#rule_calendar_period' ).hide();
				$( '#rule_time_period_value' ).show();
				$( '#rule_time_period_unit' ).show();
			} else {
				$( '#rule_calendar_period' ).parent().hide();
				$( '#rule_time_period_value' ).parent().show();
				$( '#rule_time_period_unit' ).parent().show();
			}
		}

		// calendar period
		if ( type == 'calendar_period' ) {
			if ( isLegacyUI() ) {
				$( '#rule_time_period_value' ).hide();
				$( '#rule_time_period_unit' ).hide();
				$( '#rule_calendar_period' ).show();
			} else {
				$( '#rule_time_period_value' ).parent().hide();
				$( '#rule_time_period_unit' ).parent().hide();
				$( '#rule_calendar_period' ).parent().show();
			}
		}

		// default
		if ( type != 'calendar_period' && type != 'time_period' ) {
			if ( isLegacyUI() ) {
				$( '#rule_time_period_value' ).hide();
				$( '#rule_time_period_unit' ).hide();
				$( '#rule_calendar_period' ).hide();
			} else {
				$( '#rule_time_period_value' ).parent().hide();
				$( '#rule_time_period_unit' ).parent().hide();
				$( '#rule_calendar_period' ).parent().hide();
			}
		}

	}

	/* user selects the type of embed rule */
	function ruleEmbedChange() {
		$( '.rule_embed_url' ).change( function() {
			var embedUrlType = $( this ).val();
			embedUrlFieldToggle( embedUrlType, $( this ) );
		});
	}

	/* user selects the type of ip rule */
	function ruleIpChange() {

		$( '.rule_ip' ).change( function() {
			var ipType = $( this ).val();
			console.log( 'ipType2', ipType );
			if ( ipType == 'specific' ) {
				getSiblingSettings( $( this ), '.rule_ip_specific' ).show();
			} else {
				getSiblingSettings( $( this ), '.rule_ip_specific' ).hide();
			}
		});
	}

	function getSiblingSettings( $elem, selector, getContainer ) {

		var $row = $elem;
		if ( ! $elem.hasClass( '.row' ) ) {
			$row = $elem.parents( '.row' );
		}

		if ( isLegacyUI() ) {
			return $elem.siblings( selector );
		}

		if ( typeof getContainer === 'undefined' ) {
			getContainer = true;
		}

		var $siblings = $();
		$row.find( selector ).each( function( index ) {
			var $sibling = getContainer ? $( this ).parent() : $( this );
			$siblings    = $siblings.add( $sibling );
		} );

		return $siblings;
	}

	function setupDebug() {
		$( '#limit_rules_data' ).change( function() {
			var data = $( this ).val();
		});
	}

	function getLimitRuleData() {
		var data = $( '#limit_rules_data' ).val();
		if ( ! data ) {
			return [];
		}
		return JSON.parse( data );
	}

	function setLimitRuleData( data ) {
		var dataString = JSON.stringify( data );
		$( '#limit_rules_data' ).val( dataString );
		$( '.limit_rules_data' ).trigger( 'change' );
	}

	function setDefaultData() {
		var existingRules = getLimitRuleData();

		if ( isEmpty( existingRules ) ) {
			var data = { '#rule_group_0': [gpls_repeater_default_rule] };
			setLimitRuleData( data );
		}

	}

	function setupRepeater() {
		$( '.rule_group' ).each( function( index ) {
			var elementId = '#' + $( this ).attr( 'id' );

			// get repeater items, if empty use defaults
			var repeaterItems = gpls_repeater_items[ $( this ).attr( 'id' ) ];
			if ( repeaterItems.length == 0 ) {
				repeaterItems = gpls_repeater_default_rule;
			}

			// init repeater
			initRepeater( elementId, repeaterItems );

		});

	}

	function showSelectors() {

		// init change handler
		attachTypeChangeHandler();
		attachAddRuleGroupButtonHandler();

		$( '.rule_type_selector' ).each( function() {

			var type = $( this ).val();

			var selector = '.rule_' + type;

			getSiblingSettings( $( this ), '.rule_value_selector, .rule_value_selector + span' ).hide();
			getSiblingSettings( $( this ), '{0}, {0} + span'.format( selector ) ).show();

			// handle embed url
			if ( type === 'embed_url' ) {
				var embedUrlType = getSiblingSettings( $( this ), '.rule_embed_url', false ).val();
				embedUrlFieldToggle( embedUrlType, $( this ) );
			}

			// handle ip
			if ( type === 'ip' ) {
				var ipType = getSiblingSettings( $( this ), '.rule_ip', false ).val();
				if ( ipType === 'specific' ) {
					getSiblingSettings( $( this ), '.rule_ip_specific' ).show();
				} else {
					getSiblingSettings( $( this ), '.rule_ip_specific' ).hide();
				}
			}

		});

	}

	function saveLimitRules( obj, data ) {

		var rules = {};

		// merge existing rules if they exist
		var existingRules = getLimitRuleData();

		if ( ! isEmpty( existingRules ) ) {
			rules = existingRules;
		}

		if ( data.length >= 1 ) {
			rules[ getObjSelector( obj ) ] = data;
		}

		setLimitRuleData( rules );
	}

	function getObjSelector( obj ) {
		if (typeof obj.selector !== 'undefined') {
			obj.selector;
		}

		return '#' + $( obj ).attr( 'id' );
	}

	function attachTypeChangeHandler() {
		$( '.rule_type_selector' ).on( "change", function() {
			var type     = $( this ).val();
			var selector = '.rule_' + type;

			getSiblingSettings( $( this ), '.rule_value_selector, .rule_value_selector + span' ).hide();
			getSiblingSettings( $( this ), '{0}, {0} + span'.format( selector ) ).show();

			if ( type === 'embed_url' ) {
				setSelectValue( getSiblingSettings( $( this ), selector, false ) );
			}

		});
	}

	function setSelectValue( choiceSelector ) {
		choiceSelector.val( "all" );
	}

	function attachAddRuleGroupButtonHandler() {

		$( '#add_rule_group' ).off( 'click' ); // remove existing
		$( '#add_rule_group' ).on('click', function( event ) {

			event.preventDefault();

			var ruleGroupId = makeRuleGroupId();
			var template    = getRuleGroupTemplate();

			if ( countRuleGroups() >= 1 ) {
				$( '.ruleset' ).append( '<h4 class="gpls-or-header" id="' + ruleGroupId + '_header">&mdash; OR &mdash;</h4>' );
			}

			$( '.ruleset' ).append( '<div id="' + ruleGroupId + '" class="rule_group"> ' + template + ' </div>' );
			initRepeater( '#' + ruleGroupId, [gpls_repeater_default_rule] );

			// add rule group data
			var ruleData                = getLimitRuleData();
			ruleData['#' + ruleGroupId] = [gpls_repeater_default_rule];

			setLimitRuleData( ruleData );

		});

	}

	function countRuleGroups() {
		var rule_groups = $( '.rule_group' );
		return rule_groups.length;
	}

	function makeRuleGroupId() {
		var rule_groups = $( '.rule_group' );
		var newId       = 0;
		rule_groups.each( function() {
			var ruleGroupId = $( this ).attr( 'id' );
			var idNumber    = ruleGroupId.replace( 'rule_group_', '' );

			if ( idNumber >= newId ) {
				newId = parseInt( idNumber ) + 1;
			}
		});
		return 'rule_group_' + newId;
	}

	function setRuleGroupTemplate() {
		window.ruleGroupTemplate = $( '.rule_group' ).first().html();
	}

	function getRuleGroupTemplate() {
		return window.ruleGroupTemplate;
	}

	function removeRuleGroup( obj ) {
		var selector = getObjSelector( obj );
		var $obj     = $( selector );

		$obj.siblings( selector + '_header' ).remove();
		$obj.remove();

		// remove rule group data
		var ruleData = getLimitRuleData();
		delete ruleData[selector];

		setLimitRuleData( ruleData );

		// if removing first group, remove header from the new first group
		removeHeaderFromFirstRuleGroup();

	}

	function initRepeater( ruleGroup, source ) {

		$( ruleGroup ).repeater({
			items: source,
			limit: 0,
			callbacks: {
				save: function( obj, data ) {

					saveLimitRules( obj, data );
					attachTypeChangeHandler();
					attachAddRuleGroupButtonHandler();

				},

				// can also be used as init because it runs at setup
				add: function( $group, itemElem, item, index ) {

					// attach handlers
					ruleTimePeriodChange();
					ruleEmbedChange();
					ruleIpChange();

					for ( var property in item ) {
						if ( ! item.hasOwnProperty( property ) ) {
							continue;
						}

						var input = $group.find( '.' + property + '_' + index );
						if ( input.is( 'select' ) ) {
							continue;
						}

						input.val( item[ property ] );
					}

					setupSelect2( $( itemElem ).parents( '.rule_group' ) );
					showSelectors();

				},
				remove: function( obj, index ) {
					if ( obj.items.length == 1 ) {
						removeRuleGroup( obj );
					}
				},
				repeaterButtonsLegacyGF24: function( self, index ) {
					var atLimit = self.items.length >= self.options.limit && self.options.limit !== 0;
					var buttons = '';

					if (!atLimit) {
						buttons += '<a class="add-item" data-index="' + index + '">' + "<i class=\"gficon-add\"></i>" + '</a>';
					}

					// we allow removal of last rule so the remove item option is always shown
					buttons += '<a class="remove-item" data-index="' + index + '">' + "<i class=\"gficon-subtract\"></i>" + '</a>';

					return '<div class="repeater-buttons">' + buttons + '</div>';
				},
				repeaterButtons: function( self, index ) {
					if ( isLegacyUI() ) {
						return self.callbacks.repeaterButtonsLegacyGF24( self, index );
					}

					var atLimit = self.items.length >= self.options.limit && self.options.limit !== 0;
					var buttons = '';

					if (!atLimit) {
						buttons += '<a class="add-item" data-index="' + index + '">\
								<button \
								type="button" \
								class="gform-st-icon gform-st-icon--circle-plus" \
								title="add another rule"></button>\
							</a>';
					}

					// we allow removal of last rule so the remove item option is always shown
					buttons += '<a class="remove-item" data-index="' + index + '">\
								<button \
								type="button" \
								class="gform-st-icon gform-st-icon--circle-minus" \
								title="remove this rule"></button>\
							</a>';

					return '<div class="repeater-buttons">' + buttons + '</div>';
				}
			}
		});

	}

	function removeHeaderFromFirstRuleGroup() {
		var firstGroup = $( '.rule_group' ).first();
		if ( firstGroup.length == 0 ) {
			return;
		}
		$( '#' + firstGroup.attr( 'id' ) + '_header' ).remove();
	}

	function isEmpty(obj) {
		for (var key in obj) {
			if (obj.hasOwnProperty( key )) {
				return false;
			}
		}
		return true;
	}

	function isLegacyUI() {
		return $( 'body' ).hasClass( 'gf-legacy-ui' );
	}

} )( jQuery );
