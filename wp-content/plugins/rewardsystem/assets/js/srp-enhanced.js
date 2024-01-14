// global srp_enhanced_params.
jQuery( function( $ ) {
    'use strict' ;

    function getEnhancedSelectFormatString() {
        return {
            'language' : {
                errorLoading : function() {
                    // Workaround for https://github.com/select2/select2/issues/4355 instead of i18n_ajax_error.
                    return srp_enhanced_params.i18n_searching ;
                } ,
                inputTooLong : function( args ) {
                    var overChars = args.input.length - args.maximum ;

                    if( 1 === overChars ) {
                        return srp_enhanced_params.i18n_input_too_long_1 ;
                    }

                    return srp_enhanced_params.i18n_input_too_long_n.replace( '%qty%' , overChars ) ;
                } ,
                inputTooShort : function( args ) {
                    var remainingChars = args.minimum - args.input.length ;

                    if( 1 === remainingChars ) {
                        return srp_enhanced_params.i18n_input_too_short_1 ;
                    }

                    return srp_enhanced_params.i18n_input_too_short_n.replace( '%qty%' , remainingChars ) ;
                } ,
                loadingMore : function() {
                    return srp_enhanced_params.i18n_load_more ;
                } ,
                maximumSelected : function( args ) {
                    if( args.maximum === 1 ) {
                        return srp_enhanced_params.i18n_selection_too_long_1 ;
                    }

                    return srp_enhanced_params.i18n_selection_too_long_n.replace( '%qty%' , args.maximum ) ;
                } ,
                noResults : function() {
                    return srp_enhanced_params.i18n_no_matches ;
                } ,
                searching : function() {
                    return srp_enhanced_params.i18n_searching ;
                }
            }
        } ;
    }

    try {
        $( document.body ).on( 'srp-enhanced-init' , function( ) {
            if( $( 'select.srp_select2' ).length ) {
                //Select2 with customization
                $( 'select.srp_select2' ).each( function( ) {
                    var select2_args = {
                        allowClear : $( this ).data( 'allow_clear' ) ? true : false ,
                        placeholder : $( this ).data( 'placeholder' ) ,
                        minimumResultsForSearch : 10 ,
                    } ;
                    
                    if( parseFloat( srp_enhanced_params.srp_wc_version ) > parseFloat( '2.2.0' ) ) {
                        $( this ).select2( select2_args ) ;
                    } else {
                        $( this ).chosen( select2_args ) ;
                    }
                } ) ;
            }
            if( $( 'select.rs-pages-and-posts-search' ).length ) {
                // Ajax pages and posts search boxes.
                $( 'select.rs-pages-and-posts-search' ).each( function() {
                    var select2_args = {
                        allowClear : $( this ).data( 'allow_clear' ) ? true : false ,
                        placeholder : $( this ).data( 'placeholder' ) ,
                        minimumInputLength : $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : 3 ,
                        escapeMarkup : function( m ) {
                            return m ;
                        } ,
                        ajax : {
                            url : srp_enhanced_params.ajax_url ,
                            dataType : 'json' ,
                            delay : 250 ,
                            data : function( params ) {
                                return {
                                    term : params.term ,
                                    action : 'json_search_pages_and_posts' ,
                                    sumo_security : srp_enhanced_params.fp_pages_and_posts_search_nonce ,
                                    exclude : $( this ).data( 'exclude' )
                                } ;
                            } ,
                            processResults : function( data ) {
                                var terms = [ ] ;
                                if( data ) {
                                    $.each( data , function( id , text ) {
                                        terms.push( {
                                            id : id ,
                                            text : text
                                        } ) ;
                                    } ) ;
                                }
                                return {
                                    results : terms
                                } ;
                            } ,
                            cache : true
                        } } ;

                    select2_args = $.extend( select2_args , getEnhancedSelectFormatString() ) ;

                    $( this ).selectWoo( select2_args ).addClass( 'enhanced' ) ;
                } ) ;
            }
            
            if ( $( 'select.srp_select2_search' ).length ) {
                //Multiple select with ajax search
                $( 'select.srp_select2_search' ).each( function () {

                    var select2_args = {
                        allowClear : $( this ).data( 'allow_clear' ) ? true : false ,
                        placeholder : $( this ).data( 'placeholder' ) ,
                        minimumInputLength : $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : 3 ,
                        escapeMarkup : function ( m ) {
                            return m ;
                        } ,
                        ajax : {
                            url : srp_enhanced_params.ajax_url ,
                            dataType : 'json' ,
                            delay : 250 ,
                            data : function ( params ) {
                                return {
                                    term : params.term ,
                                    action : $( this ).data( 'action' ) ? $( this ).data( 'action' ) : '' ,
                                    srp_security : $( this ).data( 'nonce' ) ? $( this ).data( 'nonce' ) : srp_enhanced_params.search_nonce ,
                                } ;
                            } ,
                            processResults : function ( data ) {
                                var terms = [ ] ;
                                if ( data ) {
                                    $.each( data , function ( id , term ) {
                                        terms.push( {
                                            id : id ,
                                            text : term
                                        } ) ;
                                    } ) ;
                                }
                                return {
                                    results : terms
                                } ;
                            } ,
                            cache : true
                        }
                    } ;

                    $( this ).select2( select2_args ) ;
                } ) ;
            }
            
            if ( $( '.srp_datepicker' ).length ) {
                $( '.srp_datepicker' ).on( 'change' , function ( ) {
                    if ( $( this ).val() === '' ) {
                        $( this ).next( ".srp_alter_datepicker_value" ).val( '' ) ;
                    }
                } ) ;

                $( '.srp_datepicker' ).each( function ( ) {
                    $( this ).datepicker( {
                        altField : $( this ).next( ".srp_alter_datepicker_value" ) ,
                        altFormat : 'yy-mm-dd' ,
                        changeMonth : true ,
                        changeYear : true
                    } ) ;
                } ) ;
            }
        } ) ;
        $( document.body ).trigger( 'srp-enhanced-init' ) ;

        if ( parseFloat( srp_enhanced_params.srp_wc_version) <= ( '2.2.0' ) ){
            $( 'select.rs-customer-search' ).ajaxChosen( {
		method : 'GET' ,
		url : srp_enhanced_params.ajax_url ,
		dataType : 'json' ,
		afterTypeDelay : 100 ,
		data : {
		    action : 'woocommerce_json_search_customers' ,
		    security : srp_enhanced_params.search_customers
		    }
	    } , function ( data ) {
		    var terms = { } ;

		    $.each( data , function ( i , val ) {
			terms[i] = val ;
		    } ) ;
		    return terms ;
            } ) ;
            
        $( "select.rs-product-search" ).ajaxChosen( {
		method : 'GET' ,
		url : srp_enhanced_params.ajax_url ,
		dataType : 'json' ,
		afterTypeDelay : 100 ,
		data : {
		    action : 'woocommerce_json_search_products_and_variations' ,
		    security : srp_enhanced_params.search_products
		}
	    } , function ( data ) {
		    var terms = { } ;

		    $.each( data , function ( i , val ) {
			terms[i] = val ;
		    } ) ;
		return terms ;
	    } ) ;
        }
    } catch( err ) {
        window.console.log( err ) ;
    }

} ) ;