/**
* Javascript functions to administrator pane
*
* @package YITH Woocommerce Social Login
* @since   1.0.0
* @version 1.0.0
* @author  YITH
*/
jQuery(document).ready(function($) {
    "use strict";

    var select          = $( document).find( '.yith-ywraq-chosen' );

    select.each( function() {
        $(this).chosen({
            width: '350px',
            disable_search: true,
            multiple: true
        })
    });


// Sorting
	jQuery('table.ywsl_social_networks tbody').sortable({
		items:'tr',
		cursor:'move',
		axis:'y',
		handle: 'td',
		scrollSensitivity:40,
		helper:function(e,ui){
			ui.children().each(function(){
				jQuery(this).width(jQuery(this).width());
			});
			return ui;
		},
		start:function(event,ui){
			ui.item.css('background-color','#f6f6f6');
		},
		stop:function(event,ui){
			ui.item.removeAttr('style');
		}
	});

 if( $('#yith-social-connection').length > 0){

	 var $t = $('#yith-social-connection'),
	 pie_data = $t.data('pie'),
	 pie_colors = $t.data('colors');


	 $.plot('#yith-social-connection', pie_data, {
		 series: {
			 pie: {
				 innerRadius: 0.3,
				 show: true,
				 label: {
					 show: true,
					 radius: 1/2,
					 formatter: labelFormatter,
					 background: {
						 opacity: 0.5
					 }
				 }
			 }
		 },
		 colors: pie_colors
	 });



 }

function labelFormatter(label, series) {
	return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
}

});