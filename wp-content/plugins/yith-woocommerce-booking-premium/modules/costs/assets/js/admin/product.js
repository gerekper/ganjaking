/* global jQuery */
( function ( $ ) {
	var extraCosts = {
		table               : $( '#yith-wcbk-extra-costs__table' ),
		list                : $( '#yith-wcbk-extra-costs__list' ),
		add                 : $( '#yith-wcbk-extra-costs__new-extra-cost' ),
		customExtraCosts    : false,
		lastIndex           : 1,
		init                : function () {
			this._initParams();

			this.add.on( 'click', this.addExtraCost );
			$( document ).on( 'click', '.yith-wcbk-extra-cost__delete', this.deleteExtraCost );

			this.checkEmptyVisibility();
		},
		_initParams         : function () {
			this.customExtraCosts = this.list.find( '.yith-wcbk-extra-cost--custom' );
			this.lastIndex        = this.customExtraCosts.length || 0;
		},
		nextIndex           : function () {
			return ++this.lastIndex;
		},
		addExtraCost        : function ( event ) {
			event.preventDefault();

			var button   = $( event.target ),
				template = button.data( 'template' ),
				index    = extraCosts.nextIndex(),
				new_extra_cost;

			template       = template.replace( new RegExp( '{{INDEX}}', 'g' ), index );
			new_extra_cost = $( template );
			extraCosts.list.append( new_extra_cost );
			new_extra_cost.find( '.yith-wcbk-extra-cost__name' ).first().focus();

			extraCosts.checkEmptyVisibility();
		},
		deleteExtraCost     : function ( event ) {
			$( event.target ).closest( '.yith-wcbk-extra-cost' ).remove();

			extraCosts.checkEmptyVisibility();
		},
		checkEmptyVisibility: function () {
			var items = extraCosts.list.find( '.yith-wcbk-extra-cost' );

			items.length ? extraCosts.table.show() : extraCosts.table.hide();
		}
	};

	extraCosts.init();

} )( jQuery );