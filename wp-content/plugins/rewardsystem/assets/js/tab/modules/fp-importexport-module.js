/*
 * Import/Export Reward Points - Module
 */
jQuery( function ( $ ) {
    var RSImpExp = {
        init : function () {
            this.update_start_date() ;
            this.update_end_date() ;
            this.update_date_type() ;
            this.export_user_based_on() ;
            $( '#rs_point_export_start_date' ).datepicker( { dateFormat : 'yy-mm-dd' } ) ;
            $( '#rs_point_export_end_date' ).datepicker( { dateFormat : 'yy-mm-dd' } ) ;
            $( document ).on( 'change' , '#rs_point_export_start_date' , this.update_start_date ) ;
            $( document ).on( 'change' , '#rs_point_export_end_date' , this.update_end_date ) ;
            $( document ).on( 'change' , '.rs_export_import_date_option' , this.update_date_type ) ;
            $( document ).on( 'change' , '.rs_csv_format' , this.export_user_based_on ) ;
            $( document ).on( 'click' , '#rs_export_user_points_csv' , this.export_points_as_csv ) ;
        } ,
        export_points_as_csv : function () {
            var block = $( this ).closest( '.rs_section_wrapper' ) ;
            RSImpExp.block( block ) ;
            var usertype = $( "input:radio[name=rs_export_import_user_option]:checked" ).val() ;
            var selecteduser = $( "#rs_import_export_users_list" ).val() ;
            var data = ( {
                action : 'exportpoints' ,
                usertype : usertype ,
                selecteduser : selecteduser ,
                sumo_security : fp_impexp_module_params.fp_export_points ,
            } ) ;
            $.post( fp_impexp_module_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    window.location.href = fp_impexp_module_params.redirect ;
                } else {
                    window.alert( response.data.error ) ;
                }
                RSImpExp.unblock( block ) ;
            } ) ;
        } ,
        update_start_date : function () {
            var data = ( {
                action : "update_start_date" ,
                start_date : $( '#rs_point_export_start_date' ).val() ,
                sumo_security : fp_impexp_module_params.fp_start_date ,
            } ) ;
            $.post( fp_impexp_module_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {

                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
        } ,
        update_end_date : function () {
            var data = ( {
                action : "update_end_date" ,
                end_date : $( '#rs_point_export_end_date' ).val() ,
                sumo_security : fp_impexp_module_params.fp_end_date ,
            } ) ;
            $.post( fp_impexp_module_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {

                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
        } ,
        update_date_type : function () {
            var data = ( {
                action : "update_date_type" ,
                datetype : $( 'input[name=rs_export_import_date_option]:checked' ).val() ,
                sumo_security : fp_impexp_module_params.fp_date_type ,
            } ) ;
            $.post( fp_impexp_module_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {

                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
        } ,
        export_user_based_on : function () {
            var data = ( {
                action : "update_user_selection_format" ,
                selected_format : $( 'input[name=rs_csv_format]:checked' ).val() ,
                sumo_security : fp_impexp_module_params.fp_user_selection ,
            } ) ;
            $.post( fp_impexp_module_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {

                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
        } ,
        block : function ( id ) {
            $( id ).block( {
                message : null ,
                overlayCSS : {
                    background : '#fff' ,
                    opacity : 0.6
                }
            } ) ;
        } ,
        unblock : function ( id ) {
            $( id ).unblock() ;
        } ,
    } ;
    RSImpExp.init() ;
} ) ;