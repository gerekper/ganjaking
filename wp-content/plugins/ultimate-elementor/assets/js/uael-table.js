( function( $ ) {

	/**
	 * Table handler Function.
	 *
	 */
	var WidgetUAELTableHandler = function( $scope, $ ) {
		if ( 'undefined' == typeof $scope ) {
			return;
		}
		// Define variables.
		var node_id              = $scope.data( 'id' );
		var uael_table           = $scope.find( '.uael-table' );
		var uael_table_id        = $scope.find( '#uael-table-id-' + node_id );
		var searchable 			 = false;
		var showentries 		 = false;
		var sortable 			 = false;

		if ( 0 == uael_table_id.length )
			return;

		var uael_table_responsive = $( '.elementor-element-' + node_id + ' #' + uael_table_id[0].id ).data( 'responsive' );

		if( 'yes' === uael_table_responsive ) {
			var column_head = $scope.find( '.uael-table-head-cell-text' );
			var rowtr = $scope.find( '.uael-table-row' );
			
			rowtr.each( function( i, tr ){
				var th = $( tr ).find( '.uael-table-body-cell-text' );
				th.each( function( index, th ){
					var classList = $scope.find( 'thead th.uael-table-col' ).eq( index ).attr( 'class' );
					var sort1 = $scope.find( 'thead th.uael-table-col' ).eq( index ).data( 'sort' );
				
					$( th ).prepend( '<div class="uael-table-head ' + classList + '"data-sort=' + sort1 + '>' + column_head.eq( index ).html() + '</div>' );
					$( '.uael-table-head span.uael-table__text:nth-child(1)' ).addClass( 'uael-tbody-head-text' );
					$( 'div.uael-table-head' ).addClass( 'responsive-header-text' );
				});
			});
		}

		var table_node =  $( '.elementor-element-' + node_id + ' #' + uael_table_id[0].id );

		//Search entries
		var search_entry = table_node.data( 'searchable' );
		
		if ( 'yes' == search_entry ) {
			searchable = true;
		}

		//Show entries select
		var show_entry = table_node.data( 'show-entry' );

		if ( 'yes' == show_entry ) {
			showentries = true;
		}

		//Sort entries
		var sort_table = table_node.data( 'sort-table' );

		if ( 'yes' == sort_table ) {
			$( '.elementor-element-' + node_id + ' #' + uael_table_id[0].id + ' th' ).css({'cursor': 'pointer'});

			sortable = true;
		}

		var search_string = table_node.data( 'search_text' ) || '';
		var length_string = uael_table_script.table_length_string;
		var no_record_found_string = uael_table_script.table_not_found_str;


		if( searchable || showentries || sortable ) {
			$( '#' + uael_table_id[0].id ).DataTable( {
				"paging": showentries, 
				"searching": searchable, 
				"ordering": sortable,
				"info": false,
				"oLanguage": {
					"sSearch": search_string,
					"sLengthMenu": length_string,
					"sZeroRecords" :no_record_found_string, 
				},
			});

			var	div_entries = $scope.find('.dataTables_length');
			div_entries.addClass('uael-tbl-entry-wrapper uael-table-info');

			var	div_search = $scope.find('.dataTables_filter');
			div_search.addClass('uael-tbl-search-wrapper uael-table-info');

			$scope.find( '.uael-table-info').wrapAll( '<div class="uael-advance-heading"></div>' );

		}
	
		function coloumn_rules() {
			var uael_table_widget = $( uael_table );
			if( $( window ).width() > 767 ) {
				uael_table_widget.addClass( 'uael-column-rules' );
				uael_table_widget.removeClass( 'uael-no-column-rules' );
			}else{
				uael_table_widget.removeClass( 'uael-column-rules' );
				uael_table_widget.addClass( 'uael-no-column-rules' );
			}
		}

		// Listen for events.
		window.addEventListener("load", coloumn_rules);
		window.addEventListener("resize", coloumn_rules);
	};

	$( window ).on( 'elementor/frontend/init', function () {
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-table.default', WidgetUAELTableHandler );
	});
} )( jQuery );
