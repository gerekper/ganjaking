jQuery(document).ready(function($){
   
    var add_receiver = $('#yith_add_receivers'),
        list_receiver = $('#yith_product_receiver_list'),
        change_label_after_remove = function(){

            var fields  = list_receiver.find('.form-field');

            fields.each( function( index ) {

                var label = $(this).find('label'),
                    label_html = label.text();
                    label_html = label_html.replace(/\d+/, ( index+1 ) );
                label.html( label_html);
            });
        },dialog =  $( "#yith_padp_commission_error" ).dialog({
		      resizable: false,
		      autoOpen: false,
		      height: "auto",
		      width: 400,
		      modal: true,
		      buttons: {
		      Ok: function() {
		          $( this ).dialog( "close" );
		        }
		      }
		    }),
        change_label_after_remove = function(){

            var fields  = list_receiver.find('.form-field');

            fields.each( function( index ) {

                var label = $(this).find('label'),
                label_html = label.html();
                    label_html = label_html.replace(/\d+/, ( index+1 ) );
                label.html( label_html);
            });
        },
        sum_commission = function() {
        	var commissions =list_receiver.find('.yith_receiver_commission'),
        	tot = 0;
        	
        	commissions.each(function(){
        		var value = $(this).val();
        		if( !isNaN( value ) ){
        		tot+= parseInt( $(this).val() );
        		}
        	});
        	
        	return tot;
        	
        },
        show_commission_error = function() {
        	
        	var tot_commission = sum_commission();
        	
        	if( tot_commission > 100 ){
        		
        		dialog.dialog("open");
        	}
        };
    
   $(document).on('click','#yith_add_receivers', function(e){

       
        e.preventDefault();
        
      var last_receiver = list_receiver.find('.form-field').size();
       
       var new_row = $('#field_hidden').clone();

       new_row.find('.yith_receiver_user_id').removeClass('enhanced').removeClass('hidden');

       var name_1 = new_row.find('.form-field').data('name_1'),
           name_2 = new_row.find('.form-field').data('name_2'),
           name_3 = new_row.find( '.form-field').data( 'name_3' ),
           name_4 = new_row.find( '.form-field').data( 'name_4' );
       new_row.removeData();
       new_row.find('.yith_receiver_user_id').attr('name', name_1 );
       new_row.find('.yith_receiver_commission').attr('name', name_2 ).attr( 'required' , 'required');
       new_row.find('.yith_receiver_email').attr('name', name_3 ).attr('required', 'required');
       new_row.find('.yith_receiver_split_after').attr('name', name_4 );

       new_row = new_row.html().replace( /%i/g, last_receiver );
       new_row = new_row.replace(/%#/g, last_receiver+1 );

       list_receiver.append( new_row );
       $('body').trigger('wc-enhanced-select-init').trigger('ywpadp-enhanced-init');
    } );

    $(document).on( 'click', '.delete_receiver', function(e){

        e.preventDefault();
        var t = $(this);

        t.parents('.form-field').remove();

        change_label_after_remove();
    });

    $(document).on('change', '.yith_receiver_commission', function(){
    	show_commission_error();
    });
    
});
