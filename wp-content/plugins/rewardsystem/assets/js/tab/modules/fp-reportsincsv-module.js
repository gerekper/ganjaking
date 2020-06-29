/*
 * Reports in CSV - Module
 */
jQuery( function ( $ ) {
    var ReportsInCSVModuleScripts = {
        init : function ( ) {
            this.trigger_on_page_load( ) ;
            this.update_user_type() ;
            this.update_selected_user() ;
            this.update_report_date_type() ;
            this.update_type_of_points() ;
            $( document ).on( 'change' , '#rs_point_export_report_start_date' , this.update_start_date ) ;
            $( document ).on( 'change' , '#rs_point_export_report_start_date' , this.update_end_date ) ;
            $( document ).on( 'change' , '#rs_export_user_report_option' , this.update_user_type ) ;
            $( document ).on( 'change' , '#rs_export_users_report_list' , this.update_selected_user ) ;
            $( document ).on( 'change' , '.rs_export_report_date_option' , this.update_report_date_type ) ;
            $( document ).on( 'change' , '#rs_export_report_pointtype_option_earning' , this.update_type_of_points ) ;
            $( document ).on( 'change' , '#rs_export_report_pointtype_option_redeeming' , this.update_type_of_points ) ;
            $( document ).on( 'change' , '#rs_export_report_pointtype_option_total' , this.update_type_of_points ) ;

            $( document ).on( 'click' , '#rs_export_user_points_report_csv' , this.export_report_for_user ) ;
        } ,
        trigger_on_page_load : function ( ) {
            if ( fp_reports_in_csv_module_params.fp_wc_version <= parseFloat( '2.2.0' ) ) {
                $( '#rs_export_users_report_list' ).chosen( ) ;
            } else {
                $( '#rs_export_users_report_list' ).select2( ) ;
            }
            $( '#rs_point_export_report_start_date' ).datepicker( { dateFormat : 'yy-mm-dd' } ) ;
            $( '#rs_point_export_report_end_date' ).datepicker( { dateFormat : 'yy-mm-dd' } ) ;
        } ,
        update_start_date : function ( ) {
            var data = {
                action : "update_report_start_date" ,
                sumo_security : fp_reports_in_csv_module_params.fp_start_date ,
                export_report_startdate : $( '#rs_point_export_report_start_date' ).val( ) ,
            } ;
            $.post( fp_reports_in_csv_module_params.ajaxurl , data , function ( ) {

            } ) ;
        } ,
        update_end_date : function ( ) {
            var data = {
                action : "update_report_end_date" ,
                sumo_security : fp_reports_in_csv_module_params.fp_end_date ,
                export_report_enddate : $( '#rs_point_export_report_end_date' ).val( ) ,
            } ;
            $.post( fp_reports_in_csv_module_params.ajaxurl , data , function ( ) {

            } ) ;
        } ,
        update_user_type : function ( ) {
            var data = {
                action : "update_user_type" ,
                sumo_security : fp_reports_in_csv_module_params.fp_user_type ,
                user_type : $( 'input[name="rs_export_user_report_option"]:checked' ).val()
            } ;
            $.post( fp_reports_in_csv_module_params.ajaxurl , data , function ( ) {

            } ) ;
        } ,
        update_selected_user : function ( ) {
            var data = {
                action : "update_selected_user" ,
                sumo_security : fp_reports_in_csv_module_params.fp_selected_user ,
                selectedusers : $( '#rs_export_users_report_list' ).val()
            } ;
            $.post( fp_reports_in_csv_module_params.ajaxurl , data , function ( ) {

            } ) ;
        } ,
        update_report_date_type : function ( ) {
            var data = {
                action : "update_report_date_type" ,
                sumo_security : fp_reports_in_csv_module_params.fp_date_type ,
                datetype : $( 'input:radio[name="rs_export_report_date_option"]:checked' ).val()
            } ;
            $.post( fp_reports_in_csv_module_params.ajaxurl , data , function ( ) {

            } ) ;
        } ,
        update_type_of_points : function ( ) {
            var data = {
                action : "update_type_of_points" ,
                sumo_security : fp_reports_in_csv_module_params.fp_points_type ,
                earnpoints : $( '#rs_export_report_pointtype_option_earning' ).is( ':checked' ) ? 1 : 0 ,
                redeempoints : $( '#rs_export_report_pointtype_option_redeeming' ).is( ':checked' ) ? 1 : 0 ,
                totalpoints : $( '#rs_export_report_pointtype_option_total' ).is( ':checked' ) ? 1 : 0 ,
            } ;
            $.post( fp_reports_in_csv_module_params.ajaxurl , data , function ( ) {

            } ) ;
        } ,
        export_report_for_user : function ( ) {
            var block = $( this ).closest( '.rs_section_wrapper' ) ;
            ReportsInCSVModuleScripts.block( block ) ;
            var dataparam = ( {
                action : 'exportreport' ,
                sumo_security : fp_reports_in_csv_module_params.fp_export_report ,
                usertype : $( "input:radio[name=rs_export_user_report_option]:checked" ).val( ) ,
                selecteduser : $( "#rs_export_users_report_list" ).val( )
            } ) ;
            $.post( fp_reports_in_csv_module_params.ajaxurl , dataparam , function ( response ) {
                if ( true === response.success ) {
                    $( '#rs_export_user_points_report_csv1' ).trigger( 'click' ) ;
                    window.location.href = fp_reports_in_csv_module_params.redirecturl ;
                } else {
                    window.alert( response.data.error ) ;
                }
                ReportsInCSVModuleScripts.unblock( block ) ;
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
    ReportsInCSVModuleScripts.init( ) ;
} ) ;