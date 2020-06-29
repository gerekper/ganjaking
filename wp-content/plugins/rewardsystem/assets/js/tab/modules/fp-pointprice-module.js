/*
 * Points Price - Module
 */
jQuery( function ( $ ) {
    var PointsPriceModule = {
        init : function () {
            this.trigger_on_page_load() ;
            this.show_or_hide_for_global_level() ;
            this.show_or_hide_for_enable_point_price() ;
            this.show_or_hide_for_product_category_selection() ;
            $( document ).on( 'change' , '.rs_enable_product_category_level_for_points_price' , this.global_level ) ;
            $( document ).on( 'change' , '#rs_point_pricing_global_level_applicable_for' , this.global_level_applicable_for ) ;
            $( document ).on( 'change' , '#rs_enable_disable_point_priceing' , this.enable_point_price ) ;
            $( document ).on( 'change' , '#rs_which_point_precing_product_selection' , this.product_category_selection ) ;

            $( document ).on( 'click' , '.rs_sumo_point_price_button' , this.bulk_update_points_for_point_price_product ) ;
        } ,
        trigger_on_page_load : function () {
            if ( fp_pointprice_module_param.fp_wc_version <= parseFloat( '2.2.0' ) ) {
                $( '#rs_include_particular_categories_for_point_pricing' ).chosen() ;
                $( '#rs_exclude_particular_categories_for_point_pricing' ).chosen() ;
                $( '#rs_select_particular_categories_for_point_price' ).chosen() ;
            } else {
                $( '#rs_include_particular_categories_for_point_pricing' ).select2() ;
                $( '#rs_exclude_particular_categories_for_point_pricing' ).select2() ;
                $( '#rs_select_particular_categories_for_point_price' ).select2() ;
            }
        } ,
        global_level : function () {
            PointsPriceModule.show_or_hide_for_global_level() ;
        } ,
        show_or_hide_for_global_level : function () {
            if ( jQuery( 'input[name=rs_enable_product_category_level_for_points_price]:checked' ).val() == 'no' ) {
                jQuery( '#rs_point_pricing_global_level_applicable_for' ).closest( 'tr' ).show() ;
                this.show_or_hide_for_global_level_applicable_for() ;
                jQuery( '.rs_hide_bulk_update_for_point_price_start' ).hide() ;
            } else {
                jQuery( '#rs_point_pricing_global_level_applicable_for' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_include_products_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_products_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_include_particular_categories_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_particular_categories_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '.rs_hide_bulk_update_for_point_price_start' ).show() ;
            }
        } ,
        global_level_applicable_for : function () {
            PointsPriceModule.show_or_hide_for_global_level_applicable_for() ;
        } ,
        show_or_hide_for_global_level_applicable_for : function () {
            if ( jQuery( '#rs_point_pricing_global_level_applicable_for' ).val() == '1' ) {
                jQuery( '#rs_include_products_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_products_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_include_particular_categories_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_particular_categories_for_point_pricing' ).closest( 'tr' ).hide() ;
            } else if ( jQuery( '#rs_point_pricing_global_level_applicable_for' ).val() == '2' ) {
                jQuery( '#rs_include_products_for_point_pricing' ).closest( 'tr' ).show() ;
                jQuery( '#rs_exclude_products_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_include_particular_categories_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_particular_categories_for_point_pricing' ).closest( 'tr' ).hide() ;
            } else if ( jQuery( '#rs_point_pricing_global_level_applicable_for' ).val() == '3' ) {
                jQuery( '#rs_include_products_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_products_for_point_pricing' ).closest( 'tr' ).show() ;
                jQuery( '#rs_include_particular_categories_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_particular_categories_for_point_pricing' ).closest( 'tr' ).hide() ;
            } else if ( jQuery( '#rs_point_pricing_global_level_applicable_for' ).val() == '4' ) {
                jQuery( '#rs_include_products_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_products_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_include_particular_categories_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_particular_categories_for_point_pricing' ).closest( 'tr' ).hide() ;
            } else if ( jQuery( '#rs_point_pricing_global_level_applicable_for' ).val() == '5' ) {
                jQuery( '#rs_include_products_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_products_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_include_particular_categories_for_point_pricing' ).closest( 'tr' ).show() ;
                jQuery( '#rs_exclude_particular_categories_for_point_pricing' ).closest( 'tr' ).hide() ;
            } else {
                jQuery( '#rs_include_products_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_products_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_include_particular_categories_for_point_pricing' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_particular_categories_for_point_pricing' ).closest( 'tr' ).show() ;
            }
        } ,
        enable_point_price : function () {
            PointsPriceModule.show_or_hide_for_enable_point_price() ;
        } ,
        show_or_hide_for_enable_point_price : function () {
            if ( jQuery( '#rs_enable_disable_point_priceing' ).val() == '2' ) {
                jQuery( '#rs_pricing_type_global_level' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_sufix_prefix_point_price_label' ).parent().parent().hide() ;
                jQuery( '#rs_local_price_points_for_product' ).parent().parent().hide() ;
                jQuery( '#rs_label_for_point_value' ).parent().parent().hide() ;
                jQuery( '#rs_local_enable_disable_point_price_for_product' ).parent().parent().hide() ;
                jQuery( '#rs_global_point_price_type' ).parent().parent().hide() ;
                jQuery( '#rs_pixel_val' ).closest( 'tr' ).hide() ;
            } else {
                jQuery( '#rs_label_for_point_value' ).parent().parent().show() ;
                jQuery( '#rs_sufix_prefix_point_price_label' ).parent().parent().show() ;
                jQuery( '#rs_pixel_val' ).closest( 'tr' ).show() ;
                jQuery( '#rs_local_enable_disable_point_price_for_product' ).parent().parent().show() ;
                if ( jQuery( '#rs_local_enable_disable_point_price_for_product' ).val() == '2' ) {
                    jQuery( '#rs_pricing_type_global_level' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_global_point_price_type' ).parent().parent().hide() ;
                    jQuery( '#rs_local_price_points_for_product' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_pricing_type_global_level' ).closest( 'tr' ).show() ;
                    if ( jQuery( '#rs_pricing_type_global_level' ).val() == '1' ) {
                        jQuery( '#rs_global_point_price_type' ).parent().parent().show() ;
                        if ( jQuery( '#rs_global_point_price_type' ).val() == '2' ) {
                            jQuery( '#rs_local_price_points_for_product' ).parent().parent().hide() ;
                        } else {
                            jQuery( '#rs_local_price_points_for_product' ).parent().parent().show() ;
                        }
                        jQuery( '#rs_global_point_price_type' ).change( function () {
                            if ( jQuery( '#rs_global_point_price_type' ).val() == '2' ) {
                                jQuery( '#rs_local_price_points_for_product' ).parent().parent().hide() ;
                            } else {
                                jQuery( '#rs_local_price_points_for_product' ).parent().parent().show() ;
                            }
                        } ) ;
                    } else {
                        jQuery( '#rs_global_point_price_type' ).parent().parent().hide() ;
                        jQuery( '#rs_local_price_points_for_product' ).parent().parent().show() ;
                    }

                    jQuery( '#rs_pricing_type_global_level' ).change( function () {
                        if ( jQuery( '#rs_pricing_type_global_level' ).val() == '1' ) {
                            jQuery( '#rs_global_point_price_type' ).parent().parent().show() ;
                            if ( jQuery( '#rs_global_point_price_type' ).val() == '2' ) {
                                jQuery( '#rs_local_price_points_for_product' ).parent().parent().hide() ;
                            } else {
                                jQuery( '#rs_local_price_points_for_product' ).parent().parent().show() ;
                            }
                            jQuery( '#rs_global_point_price_type' ).change( function () {
                                if ( jQuery( '#rs_global_point_price_type' ).val() == '2' ) {
                                    jQuery( '#rs_local_price_points_for_product' ).parent().parent().hide() ;
                                } else {
                                    jQuery( '#rs_local_price_points_for_product' ).parent().parent().show() ;
                                }
                            } ) ;
                        } else {
                            jQuery( '#rs_global_point_price_type' ).parent().parent().hide() ;
                            jQuery( '#rs_local_price_points_for_product' ).parent().parent().show() ;
                        }
                    } ) ;
                }
                jQuery( '#rs_local_enable_disable_point_price_for_product' ).change( function () {
                    if ( jQuery( '#rs_local_enable_disable_point_price_for_product' ).val() == '2' ) {
                        jQuery( '#rs_pricing_type_global_level' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_global_point_price_type' ).parent().parent().hide() ;
                        jQuery( '#rs_local_price_points_for_product' ).parent().parent().hide() ;
                    } else {
                        jQuery( '#rs_global_point_price_type' ).parent().parent().show() ;
                        jQuery( '#rs_pricing_type_global_level' ).closest( 'tr' ).show() ;
                        if ( jQuery( '#rs_pricing_type_global_level' ).val() == '1' ) {
                            jQuery( '#rs_global_point_price_type' ).parent().parent().show() ;
                            if ( jQuery( '#rs_global_point_price_type' ).val() == '2' ) {
                                jQuery( '#rs_local_price_points_for_product' ).parent().parent().hide() ;
                            } else {
                                jQuery( '#rs_local_price_points_for_product' ).parent().parent().show() ;
                            }
                            jQuery( '#rs_global_point_price_type' ).change( function () {
                                if ( jQuery( '#rs_global_point_price_type' ).val() == '2' ) {
                                    jQuery( '#rs_local_price_points_for_product' ).parent().parent().hide() ;
                                } else {
                                    jQuery( '#rs_local_price_points_for_product' ).parent().parent().show() ;
                                }
                            } ) ;
                        } else {
                            jQuery( '#rs_global_point_price_type' ).parent().parent().hide() ;
                            jQuery( '#rs_local_price_points_for_product' ).parent().parent().show() ;
                        }

                        jQuery( '#rs_pricing_type_global_level' ).change( function () {
                            if ( jQuery( '#rs_pricing_type_global_level' ).val() == '1' ) {
                                jQuery( '#rs_global_point_price_type' ).parent().parent().show() ;
                                if ( jQuery( '#rs_global_point_price_type' ).val() == '2' ) {
                                    jQuery( '#rs_local_price_points_for_product' ).parent().parent().hide() ;
                                } else {
                                    jQuery( '#rs_local_price_points_for_product' ).parent().parent().show() ;
                                }
                                jQuery( '#rs_global_point_price_type' ).change( function () {
                                    if ( jQuery( '#rs_global_point_price_type' ).val() == '2' ) {
                                        jQuery( '#rs_local_price_points_for_product' ).parent().parent().hide() ;
                                    } else {
                                        jQuery( '#rs_local_price_points_for_product' ).parent().parent().show() ;
                                    }
                                } ) ;
                            } else {
                                jQuery( '#rs_global_point_price_type' ).parent().parent().hide() ;
                                jQuery( '#rs_local_price_points_for_product' ).parent().parent().show() ;
                            }
                        } ) ;
                    }
                } ) ;
            }
        } ,
        product_category_selection : function () {
            PointsPriceModule.show_or_hide_for_product_category_selection() ;
        } ,
        show_or_hide_for_product_category_selection : function () {
            if ( ( jQuery( '.rs_which_point_precing_product_selection' ).val() === '1' ) ) {
                jQuery( '#rs_select_particular_products_for_point_price' ).parent().parent().hide() ;
                jQuery( '#rs_select_particular_categories_for_point_price' ).parent().parent().hide() ;
            } else if ( jQuery( '.rs_which_point_precing_product_selection' ).val() === '2' ) {
                jQuery( '#rs_select_particular_products_for_point_price' ).parent().parent().show() ;
                jQuery( '#rs_select_particular_categories_for_point_price' ).parent().parent().hide() ;
            } else if ( jQuery( '.rs_which_point_precing_product_selection' ).val() === '3' ) {
                jQuery( '#rs_select_particular_products_for_point_price' ).parent().parent().hide() ;
                jQuery( '#rs_select_particular_categories_for_point_price' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_select_particular_categories_for_point_price' ).parent().parent().show() ;
                jQuery( '#rs_select_particular_products_for_point_price' ).parent().parent().hide() ;
            }
        } ,
        bulk_update_points_for_point_price_product : function ( ) {
            var rsconfirm = confirm( "It is strongly recommended that you do not reload or refresh page. Are you sure you wish to update now?" ) ;
            if ( rsconfirm === true ) {
                PointsPriceModule.block( '.rs_hide_bulk_update_for_point_price_start' ) ;
                var data = {
                    action : 'update_point_price_for_product' ,
                    sumo_security : fp_pointprice_module_param.point_price_bulk_update ,
                    productselection : $( '#rs_which_point_precing_product_selection' ).val() ,
                    enablepointprice : $( '#rs_local_enable_disable_point_price' ).val() ,
                    pointpricetype : $( '#rs_local_point_price_type' ).val() ,
                    selectedproducts : $( '#rs_select_particular_products_for_point_price' ).val() ,
                    pricepoints : $( '#rs_local_price_points' ).val() ,
                    selectedcategories : $( '#rs_select_particular_categories_for_point_price' ).val() ,
                    pointpricingtype : $( '#rs_local_point_pricing_type' ).val() ,
                } ;
                $.post( fp_pointprice_module_param.ajaxurl , data , function ( response ) {
                    if ( true === response.success ) {
                        window.location.href = fp_pointprice_module_param.redirecturl ;
                    } else {
                        window.alert( response.data.error ) ;
                    }
                    PointsPriceModule.unblock( '.rs_hide_bulk_update_for_point_price_start' ) ;
                } ) ;
            }
            return false ;
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
    PointsPriceModule.init() ;
} ) ;