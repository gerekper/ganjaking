( function( $ ) {

	init();

	function init() {

		// check if this is the limit rule setting page
		var isLimitRuleSettingPage = $('.ruleset');
		if( isLimitRuleSettingPage.length == 0 ) {
			return;
		}

		setRuleGroupTemplate();
		setupRepeater();
		setDefaultData();
		ruleTimePeriodChange();
		ruleEmbedChange();
		ruleIpChange();
		setupSubmitHandler();
		setupTimePeriod();
		setupDebug()

	}

	function setupSelect2( ruleGroup ) {
		$( ruleGroup ).find( 'select:not( .select2-hidden-accessible )' ).select2();
	}

	function setupTimePeriod() {
		var timePeriod = $('#rule_time_period_type').val();
		timePeriodFieldToggle( timePeriod );
	}

	function setupSubmitHandler() {
		$('#gform-settings').validate();

		$( "#rule_group_name" ).rules( "add", {
			required: true,
			minlength: 1,
			messages: {
				required: "Required input",
				minlength: jQuery.validator.format("Please enter a Rule Group Name")
			}
		});

		$( "#rule_submission_limit" ).rules( "add", {
			required: true,
			number: true,
			min: 0,
			step: 1,
			messages: {
				required: "Required",
				minlength: jQuery.validator.format("Please enter a number")
			}
		});

		$( "#rule_time_period_value" ).rules( "add", {
			number: true,
			min: 0,
			step: 1,
			messages: {
				minlength: jQuery.validator.format("Please enter a number")
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

	function embedUrlFieldToggle( embedUrlType, row ) {

		if( embedUrlType == 'full' ) {
			$(row).siblings('.rule_embed_url_value_post + span').hide();
			$(row).siblings('.rule_embed_url_value_full').show();
		}

		if( embedUrlType == 'post_id' ) {
			$(row).siblings('.rule_embed_url_value_full').hide();
			$(row).siblings('.rule_embed_url_value_post + span').show();
		}

		if( embedUrlType == 'all' ) {
			$(row).siblings('.rule_embed_url_value_post + span').hide();
			$(row).siblings('.rule_embed_url_value_full').hide();
		}

		// reset validation errors
		$(row).siblings('label.error').hide();

	}

	/* user selects the type of embed_url */
	function ruleTimePeriodChange() {
		$('#rule_time_period_type').change( function() {
			var type = $(this).val();
			timePeriodFieldToggle( type );
		});
	}

	function timePeriodFieldToggle( type ) {

		// time period
		if( type == 'time_period' ) {
			$('#rule_calendar_period').hide();
			$('#rule_time_period_value').show();
			$('#rule_time_period_unit').show();
		}

		// calendar period
		if( type == 'calendar_period' ) {
			$('#rule_time_period_value').hide();
			$('#rule_time_period_unit').hide();
			$('#rule_calendar_period').show();
		}

		// default
		if( type != 'calendar_period' && type != 'time_period' ) {
			$('#rule_time_period_value').hide();
			$('#rule_time_period_unit').hide();
			$('#rule_calendar_period').hide();
		}

	}

	/* user selects the type of embed rule */
	function ruleEmbedChange() {
		$('.rule_embed_url').change( function() {
			var embedUrlType = $(this).val();
			embedUrlFieldToggle( embedUrlType, this );
		});
	}

	/* user selects the type of ip rule */
	function ruleIpChange() {

		$('.rule_ip').change( function() {
			var ipType = $(this).val();
			if( ipType == 'specific' ) {
				$(this).siblings('.rule_ip_specific').show();
			} else {
				$(this).siblings('.rule_ip_specific').hide();
			}
		});
	}

	function setupDebug() {
		$('#limit_rules_data').change( function() {
			var data = $(this).val();
		});
	}

	function getLimitRuleData() {
		var data = $('#limit_rules_data').val();
		if( !data ) {
			return [];
		}
		return JSON.parse( data );
	}

	function setLimitRuleData( data ) {
		var dataString = JSON.stringify( data );
		$('#limit_rules_data').val( dataString );
		$('.limit_rules_data').trigger('change');
	}

	function setDefaultData() {
		var existingRules = getLimitRuleData();

		if( isEmpty( existingRules ) ) {
			var data = { '#rule_group_0': [gpls_repeater_default_rule] };
			setLimitRuleData( data );
		}

	}

	function setupRepeater() {
		$('.rule_group').each( function( index ) {
			var elementId = '#' + $(this).attr('id');

			// get repeater items, if empty use defaults
			var repeaterItems = gpls_repeater_items[ index ];
			if( repeaterItems.length == 0 ) {
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

			var type = $(this).val();

			var selector = '.rule_' + type;

			$(this).siblings( '.rule_value_selector, .rule_value_selector + span' ).hide(); // hide all value choices
			$(this).siblings( '{0}, {0} + span'.format( selector ) ).show(); // show applicable choice type

			// handle embed url
			if( type == 'embed_url') {
				var embedUrlType = $(this).siblings('.rule_embed_url').val();
				embedUrlFieldToggle( embedUrlType, this );
			}

			// handle ip
			if( type == 'ip') {
				var ipType = $(this).siblings('.rule_ip').val();

				if( ipType == 'specific' ) {
					$(this).siblings('.rule_ip_specific').show();
				} else {
					$(this).siblings('.rule_ip_specific').hide();
				}
			}

		});

	}

	function saveLimitRules( obj, data ) {

		var rules = {};

		// merge existing rules if they exist
		var existingRules = getLimitRuleData();

		if( !isEmpty(existingRules) ) {
			rules = existingRules;
		}

		if( data.length >= 1 ) {
			rules[ obj.selector ] = data;
		}

		setLimitRuleData( rules );
	}

	function attachTypeChangeHandler() {
		$('.rule_type_selector').on( "change", function() {
			var type = $(this).val();
			var selector = '.rule_' + type;
			$(this).siblings( '.rule_value_selector, .rule_value_selector + span' ).hide(); // hide all value choices
			var choiceSelector = $(this).siblings( '{0}, {0} + span'.format( selector ) ).show(); // show applicable choice type

			if( type == 'embed_url' ) {
				setSelectValue( choiceSelector );
			}

		});
	}

	function setSelectValue( choiceSelector ) {
		choiceSelector.val("all");
	}

	function attachAddRuleGroupButtonHandler() {

		$('#add_rule_group').off('click'); // remove existing
		$('#add_rule_group').on('click', function( event ) {

			event.preventDefault();

			var ruleGroupId = makeRuleGroupId();
			var template = getRuleGroupTemplate();

			if( countRuleGroups() >= 1 ) {
				$('.ruleset').append('<h4 class="gpls-or-header" id="' + ruleGroupId + '_header">&mdash; OR &mdash;</h4>');
			}

			$('.ruleset').append('<div id="' + ruleGroupId + '" class="rule_group"> ' + template + ' </div>');
			initRepeater( '#' + ruleGroupId, [gpls_repeater_default_rule] );

			// add rule group data
			var ruleData = getLimitRuleData();
			ruleData['#' + ruleGroupId] = [gpls_repeater_default_rule];

			setLimitRuleData( ruleData );

		});

	}

	function countRuleGroups() {
		var rule_groups = $('.rule_group');
		return rule_groups.length;
	}

	function makeRuleGroupId() {
		var rule_groups = $('.rule_group');
		var newId = 0;
		rule_groups.each( function() {
			var ruleGroupId = $(this).attr('id');
			var idNumber = ruleGroupId.replace('rule_group_', '');
			if( idNumber >= newId ) {
				newId = parseInt( idNumber ) + 1;
			}
		});
		return 'rule_group_' + newId;
	}

	function setRuleGroupTemplate() {
		window.ruleGroupTemplate = $('#rule_group_0').html();
	}

	function getRuleGroupTemplate() {
		return window.ruleGroupTemplate;
	}

	function removeRuleGroup( obj ) {

		$(obj.selector).remove();
		$(obj.selector + '_header').remove();

		// remove rule group data
		var ruleData = getLimitRuleData();
		delete ruleData[obj.selector];

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
				add: function( selector, itemElem, item, index ) {

					// attach handlers
					ruleTimePeriodChange();
					ruleEmbedChange();
					ruleIpChange();

					for ( var property in item ) {
						if ( ! item.hasOwnProperty( property ) ) {
							continue;
						}

						var input = $( '.' + property + '_' + index );
						if ( input.is( 'select' ) ) {
							continue;
						}

						input.val( item[ property ] );
					}

					setupSelect2( $( itemElem ).parents( '.rule_group' ) );
					showSelectors();

				},
				remove: function( obj, index ) {
					if( obj.items.length == 1 ) {
						removeRuleGroup( obj );
					}
				},
				repeaterButtons: function( self, index ) {
					var cssClass = self.items.length >= self.options.limit && self.options.limit !== 0 ? 'inactive' : '',
						buttons = '<a class="add-item ' + cssClass + '" data-index="' + index + '">' + "<i class=\"gficon-add\"></i>" + '</a>';

					// if( self.items.length > self.options.minItemCount )
					// we allow removal of last
					buttons += '<a class="remove-item" data-index="' + index + '">' + "<i class=\"gficon-subtract\"></i>" + '</a>';

					return '<div class="repeater-buttons">' +  buttons + '</div>';
				}
			}
		});

	}

	function removeHeaderFromFirstRuleGroup() {
		var firstGroup = $('.rule_group').first();
		if( firstGroup.length == 0 ) {
			return;
		}
		$('#' + firstGroup.attr('id')  + '_header').remove();
	}

	function isEmpty(obj) {
		for(var key in obj) {
			if(obj.hasOwnProperty(key))
				return false;
		}
		return true;
	}

} )( jQuery );
