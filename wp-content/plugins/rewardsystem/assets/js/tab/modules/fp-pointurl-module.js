/*
 * PointURL - Module
 */
jQuery( function ( $ ) {
    var PointURLModule = {
        init : function () {
            this.trigger_on_load() ;
            $( document ).on( 'change' , '#changepagesizers_for_url' , this.pagination_for_pointurl ) ;
            $( document ).on( 'click' , '#rs_button_for_point_url' , this.generate_point_url ) ;
            $( document ).on( 'click' , '.rs_remove_point_url' , this.remove_point_url ) ;
        } ,
        trigger_on_load : function () {
            jQuery( '#rs_expiry_time_for_pointurl' ).datepicker( { dateFormat : 'yy-mm-dd' , minDate : 0 } ) ;
            jQuery( '#rs_table_for_point_url' ).footable().bind( 'footable_filtering' , function ( e ) {
                var selected = jQuery( '.filter-status' ).find( ':selected' ).text() ;
                if ( selected && selected.length > 0 ) {
                    e.filter += ( e.filter && e.filter.length > 0 ) ? ' ' + selected : selected ;
                    e.clear = ! e.filter ;
                }
            } ) ;
        } ,
        pagination_for_pointurl : function ( e ) {
            e.preventDefault() ;
            var pageSize = jQuery( this ).val() ;
            jQuery( '.footable' ).data( 'page-size' , pageSize ) ;
            jQuery( '.footable' ).trigger( 'footable_initialized' ) ;
        } ,
        generate_point_url : function () {
            PointURLModule.block( '.form-table' ) ;
            if ( $( '#rs_point_for_url' ).val() != '' && $( '#rs_label_for_site_url' ).val() != '' ) {
                var data = ( {
                    action : 'generatepointurl' ,
                    name : $( '#rs_label_for_site_url' ).val() ,
                    url : $( '#rs_site_url' ).val() ,
                    points : $( '#rs_point_for_url' ).val() ,
                    time_limit : $( '#rs_time_limit_for_pointurl' ).val() ,
                    expiry_time : $( '#rs_expiry_time_for_pointurl' ).val() ,
                    count_limit : $( '#rs_count_limit_for_pointurl' ).val() ,
                    current_usage_count : 0 ,
                    date : fp_pointurl_module_params.date ,
                    count : $( '#rs_count_for_pointurl' ).val() ,
                    used_by : '' ,
                    sumo_security : fp_pointurl_module_params.fp_generate_url
                } ) ;
                $.post( fp_pointurl_module_params.ajaxurl , data , function ( response ) {
                    if ( true === response.success ) {
                        PointURLModule.unblock( '.form-table' ) ;
                        $( ".rs_table_for_point_url" ).load( window.location + " .rs_table_for_point_url" ) ;
                    } else {
                        window.alert( response.data.error ) ;
                        PointURLModule.unblock( '.form-table' ) ;
                    }
                } ) ;
            }
            return false ;
        } ,
        remove_point_url : function () {
            PointURLModule.block( '.form-table' ) ;
            var uniqueid = jQuery( this ).attr( 'data-uniqid' ) ;
            var data = ( {
                action : 'removepointurl' ,
                uniqueid : uniqueid ,
                sumo_security : fp_pointurl_module_params.fp_remove_url
            } ) ;
            $.post( fp_pointurl_module_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    PointURLModule.unblock( '.form-table' ) ;
                    $( ".rs_table_for_point_url" ).load( window.location + " .rs_table_for_point_url" ) ;
                } else {
                    window.alert( response.data.error ) ;
                    PointURLModule.unblock( '.form-table' ) ;
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
    PointURLModule.init() ;
} ) ;