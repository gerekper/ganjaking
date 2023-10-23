/* global: yithWcbmBadgeRules */
jQuery( function ($) {

	var addRuleButtons           = $( '.post-php.post-type-ywcbm-badge-rule .page-title-action,.edit-php.post-type-ywcbm-badge-rule .page-title-action, .edit-php.post-type-ywcbm-badge-rule .yith-plugin-fw__list-table-blank-state__cta' ),
		block                    = function (element) {
			element.block( {
				message   : '',
				overlayCSS: {backgroundColor: '#FFFFFF', opacity: 0.8, cursor: 'wait'},
			} );
		},
		unblock                  = function (element) {
			element.unblock();
		},
		openModalToAddRule       = function (event) {
			event.preventDefault();

			yith.ui.modal( {
				title                     : yithWcbmBadgeRules.addBadgeRuleModal.title,
				content                   : yithWcbmBadgeRules.addBadgeRuleModal.content,
				closeWhenClickingOnOverlay: true,
				width                     : 700,
			} );
		},
		initPostTitle            = function () {
			var postTitle = $( '#title' ),
				ruleTitle = $( '#yith-wcbm-title' );
			if ( postTitle && ruleTitle ) {
				ruleTitle.val( postTitle.val() );
			}
		},
		initSelectWoo            = function () {
			$( '.wc-enhanced-select' ).selectWoo();
		},
		updateScheduleDatepicker = function () {
			$( '#_schedule_dates_to' ).datepicker( 'option', 'minDate', $( this ).val() );
		},
		addAssociationRule       = function () {
			var addButton      = $( this ),
				rulesContainer = addButton.parent(),
				rulesList      = rulesContainer.find( '.yith-wcbm-associations-badge-rules' ),
				rowTemplate    = wp.template( addButton.parent().attr( 'id' ) );
			rulesList.append( rowTemplate( {ruleID: Date.now()} ) );
			$( document ).trigger( 'yith_fields_init' );
		},
		removeAssociationRule    = function () {
			$( this ).parent().remove();
		},
		toggleEnableBadgeRules   = function () {
			var $toggleButton          = $( this ),
				$toggleButtonContainer = $toggleButton.closest( '.yith-plugin-fw-onoff-container' ),
				badgeRuleID            = $toggleButtonContainer.data( 'badge-rule-id' ),
				post_data              = {
					action      : yithWcbmBadgeRules.actions.toggleRuleEnable,
					security    : yithWcbmBadgeRules.security,
					rule_id     : badgeRuleID,
					rule_enabled: $toggleButton.prop( 'checked' ) ? 'yes' : 'no',
				};

			block( $toggleButtonContainer );

			$.ajax( {
				type    : 'POST',
				dataType: 'json',
				data    : post_data,
				url     : yithWcbmBadgeRules.ajaxurl,
				success : function (response) {
					if ( ! response[ 'success' ] ) {
						$toggleButton.prop( 'checked', ! $toggleButton.prop( 'checked' ) );
					}
				},
				complete: function () {
					unblock( $toggleButtonContainer );
				},
			} );
		},
		checkInputValidity       = function () {
			var $input       = $( this ),
				$description = $input.closest( '.yith-plugin-fw-metabox-field-row' ).find( '.description' ),
				color        = $input.is( ':valid' ) ? '' : '#ea0034';
			$input.css( 'border-color', color );
			$description.css( 'color', color );
		};

	addRuleButtons.on( 'click', openModalToAddRule );
	$( document ).on( 'click', '.yith-wcbm-add-association-rule', addAssociationRule );
	$( document ).on( 'click', '.yith-wcbm-remove-association-badge-rule', removeAssociationRule );
	$( document ).on( 'change', '#_schedule_dates_from', updateScheduleDatepicker );
	$( document ).on( 'click', '.yith_wcbm_actions .yith-plugin-fw__action-button--delete-action a', function (e) {
		e.preventDefault();
		e.stopPropagation();

		var url      = $( this ).attr( 'href' ),
			ruleName = $( this ).closest( 'tr' ).find( 'td.title a' ).html();
		yith.ui.confirm( {
			title            : yithWcbmBadgeRules.i18n.deleteBadgeRuleModal.title,
			message          : yithWcbmBadgeRules.i18n.deleteBadgeRuleModal.message.replace( '%s', ruleName ),
			confirmButtonType: 'delete',
			confirmButton    : yithWcbmBadgeRules.i18n.deleteBadgeRuleModal.confirmButton,
			closeAfterConfirm: false,
			onConfirm        : function () {
				window.location.href = url;
			},
		} );
	} );

	$( document ).on( 'change', '.column-yith_wcbm_actions .yith-wcbm-enable-badge-rule input', toggleEnableBadgeRules );
	$( document ).on( 'change keyup', '#yith-wcbm-title', checkInputValidity );
	$( '#yith-wcbm-title' ).on( 'invalid', checkInputValidity );

	initPostTitle();
	initSelectWoo();
} );
