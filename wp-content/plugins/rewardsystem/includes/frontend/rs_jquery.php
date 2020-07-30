<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}

/*
 * Common Function For Choosen.
 */

function rs_common_chosen_function( $id ) {
    wp_enqueue_script( 'chosen' ) ;
    ob_start() ;
    ?>
    <script type="text/javascript">
        jQuery( function () {
            jQuery( 'select' + '<?php echo $id ; ?>' ).chosen() ;
        } ) ;
    </script>
    <?php
    $getcontent = ob_get_clean() ;
    return $getcontent ;
}

/*
 * Common Function For select.
 */

function rs_common_select_function( $id ) {
    wp_enqueue_script( 'select2' ) ;
    ob_start() ;
    ?>
    <script type="text/javascript">
        jQuery( function () {
            jQuery( 'select' + '<?php echo $id ; ?>' ).select2() ;
        } ) ;
    </script>
    <?php
    $getcontent = ob_get_clean() ;
    return $getcontent ;
}

/*
 * Common ajax function to select user.
 */

function rs_common_ajax_function_to_select_user( $ajaxid ) {
    global $woocommerce ;
    ob_start() ;
    ?>
    <script type="text/javascript">
    <?php if ( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) { ?>
            jQuery( function () {
                jQuery( 'select.<?php echo $ajaxid ; ?>' ).ajaxChosen( {
                    method : 'GET' ,
                    url : '<?php echo SRP_ADMIN_AJAX_URL ; ?>' ,
                    dataType : 'json' ,
                    afterTypeDelay : 100 ,
                    data : {
                        action : 'woocommerce_json_search_customers' ,
                        security : '<?php echo wp_create_nonce( "search-customers" ) ; ?>'
                    }
                } , function ( data ) {
                    var terms = { } ;

                    jQuery.each( data , function ( i , val ) {
                        terms[i] = val ;
                    } ) ;
                    return terms ;
                } ) ;
            } ) ;
    <?php } ?>
    </script>
    <?php
    $getcontent = ob_get_clean() ;
    return $getcontent ;
}

/*
 * Common Ajax Function to select products
 */

function rs_common_ajax_function_to_select_products( $ajaxid ) {
    global $woocommerce ;
    ob_start() ;
    ?>
    <script type="text/javascript">
    <?php if ( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) { ?>
            jQuery( function () {
                jQuery( "select.<?php echo $ajaxid ; ?>" ).ajaxChosen( {
                    method : 'GET' ,
                    url : '<?php echo SRP_ADMIN_AJAX_URL ; ?>' ,
                    dataType : 'json' ,
                    afterTypeDelay : 100 ,
                    data : {
                        action : 'woocommerce_json_search_products_and_variations' ,
                        security : '<?php echo wp_create_nonce( "search-products" ) ; ?>'
                    }
                } , function ( data ) {
                    var terms = { } ;

                    jQuery.each( data , function ( i , val ) {
                        terms[i] = val ;
                    } ) ;
                    return terms ;
                } ) ;
            } ) ;
    <?php } ?>
    </script>
    <?php
    $getcontent = ob_get_clean() ;
    return $getcontent ;
}

/*
 * Ajax Function for Upload Your Own Gift Voucher.
 */

function rs_ajax_for_upload_your_gift_voucher( $button_id , $field_id ) {
    ?>
    <script type="text/javascript">
        jQuery( document ).ready( function ( $ ) {
            var rs_custom_uploader ;
            jQuery( '#<?php echo esc_html( $button_id ) ; ?>' ).click( function ( e ) {
                e.preventDefault() ;
                if ( rs_custom_uploader ) {
                    rs_custom_uploader.open() ;
                    return ;
                }
                rs_custom_uploader = wp.media.frames.file_frame = wp.media( {
                    title : 'Choose Image' ,
                    button : { text : 'Choose Image'
                    } ,
                    multiple : false
                } ) ;
                //When a file is selected, grab the URL and set it as the text field's value
                rs_custom_uploader.on( 'select' , function () {
                    attachment = rs_custom_uploader.state().get( 'selection' ).first().toJSON() ;
                    jQuery( '#<?php echo esc_html( $field_id ) ; ?>' ).val( attachment.url ) ;
                } ) ;
                //Open the uploader dialog
                rs_custom_uploader.open() ;
            } ) ;
        } ) ;
    </script>
    <?php
}

function user_selection_field( $field_id , $field_label , $getuser ) {
    global $woocommerce ;
    echo rs_common_ajax_function_to_select_user( $field_id ) ;
    ?>
    <?php if ( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) { ?>
        <tr valign="top">
            <th class="titledesc" scope="row">
                <label for="<?php echo $field_id ; ?>"><?php _e( $field_label , SRP_LOCALE ) ; ?></label>
            </th>
            <td>
                <select name="<?php echo $field_id ; ?>[]" style="width:343px;" multiple="multiple" id="<?php echo $field_id ; ?>" class="short <?php echo $field_id ; ?>" data-exclude='<?php echo json_encode( rs_exclude_particular_users($field_id) ) ; ?>'>
                    <?php
                    $json_ids = array() ;
                    if ( $getuser != "" ) {
                        $listofuser = $getuser ;
                        if ( ! is_array( $listofuser ) ) {
                            $userids = array_filter( array_map( 'absint' , ( array ) explode( ',' , $listofuser ) ) ) ;
                        } else {
                            $userids = $listofuser ;
                        }
                        foreach ( $userids as $userid ) {
                            $user = get_user_by( 'id' , $userid ) ;
                            ?>
                            <option value="<?php echo $userid ; ?>" selected="selected"><?php echo esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <?php
    } else {
        if ( ( float ) $woocommerce->version >= ( float ) ('3.0.0') ) {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="<?php echo $field_id ; ?>"><?php _e( $field_label , SRP_LOCALE ) ; ?></label>
                </th>
                <td>
                    <select multiple="multiple"  class="wc-customer-search"  name="<?php echo $field_id ; ?>[]" id="<?php echo $field_id ; ?>" data-placeholder="<?php _e( 'Search Users' , SRP_LOCALE ) ; ?>" data-exclude='<?php echo json_encode( rs_exclude_particular_users($field_id) ) ; ?>'>
                        <?php
                        $json_ids = array() ;
                        if ( $getuser != "" ) {
                            $listofuser = $getuser ;
                            if ( ! is_array( $listofuser ) ) {
                                $userids = array_filter( array_map( 'absint' , ( array ) explode( ',' , $listofuser ) ) ) ;
                            } else {
                                $userids = $listofuser ;
                            }
                            foreach ( $userids as $userid ) {
                                $user     = get_user_by( 'id' , $userid ) ;
                                $json_ids = esc_html( $user->display_name ) . '(#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ;
                                ?>
                                <option value="<?php echo $userid ; ?>" selected="selected"><?php echo esc_html( $json_ids ) ; ?></option>
                                <?php
                            }
                        }
                        ?>                                
                    </select>
                </td>
            </tr>
            </select>
            <?php
        } else {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="<?php echo $field_id ; ?>"><?php _e( $field_label , SRP_LOCALE ) ; ?></label>
                </th>
                <td>
                    <input type="hidden" class="wc-customer-search" name="<?php echo $field_id ; ?>" id="<?php echo $field_id ; ?>" data-multiple="true" data-exclude='<?php echo json_encode( rs_exclude_particular_users($field_id) ) ; ?>' data-placeholder="<?php _e( 'Search Users' , SRP_LOCALE ) ; ?>" data-selected="<?php
                    $json_ids = array() ;
                    if ( $getuser != "" ) {
                        $listofuser = $getuser ;
                        if ( ! is_array( $listofuser ) ) {
                            $userids = array_filter( array_map( 'absint' , ( array ) explode( ',' , $listofuser ) ) ) ;
                        } else {
                            $userids = $listofuser ;
                        }
                        foreach ( $userids as $userid ) {
                            $user                  = get_user_by( 'id' , $userid ) ;
                            $json_ids[ $user->ID ] = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ;
                        }echo esc_attr( json_encode( $json_ids ) ) ;
                    }
                    ?>" value="<?php echo implode( ',' , array_keys( $json_ids ) ) ; ?>" data-allow_clear="true" />
                </td>
            </tr>

            <?php
        }
    }
}

function rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) {
    global $woocommerce ;
    if ( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) {
        ?>
        <tr valign="top">
            <th class="titledesc" scope="row">
                <label for="<?php echo $field_id ; ?>"><?php _e( $field_label , SRP_LOCALE ) ; ?></label>
            </th>
            <td class="forminp forminp-select">
                <select multiple name="<?php echo $field_id ; ?>[]" style='width:350px;' id='<?php echo $field_id ; ?>' class="<?php echo $field_id ; ?> rs_ajax_chosen_select_products_redeem">
                    <?php
                    $selected_products_exclude = array_filter( ( array ) $getproducts ) ;
                    if ( $selected_products_exclude != "" ) {
                        if ( ! empty( $selected_products_exclude ) ) {
                            $list_of_produts = ( array ) $getproducts ;
                            foreach ( $list_of_produts as $rs_free_id ) {
                                echo '<option value="' . $rs_free_id . '" ' ;
                                selected( 1 , 1 ) ;
                                echo '>' . ' #' . $rs_free_id . ' &ndash; ' . get_the_title( $rs_free_id ) ;
                            }
                        }
                    } else {
                        ?>
                        <option value=""></option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <?php
    } else {
        if ( $woocommerce->version >= ( float ) ('3.0.0') ) {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="<?php echo $field_id ; ?>"><?php _e( $field_label , SRP_LOCALE ) ; ?></label>
                </th>
                <td class="forminp forminp-select">
                    <select class="wc-product-search" style="width: 50%;" multiple="multiple" id="<?php echo $field_id ; ?>"  name="<?php echo $field_id ; ?>[]" data-placeholder="<?php _e( 'Search for a product' , SRP_LOCALE ) ; ?>"  >
                        <?php
                        $json_ids = array() ;
                        if ( $getproducts != "" ) {
                            $product_ids = $getproducts ;
                            foreach ( $product_ids as $product_id ) {
                                $product = wc_get_product( $product_id ) ;
                                if ( is_object( $product ) ) {
                                    $json_ids = wp_kses_post( $product->get_formatted_name() ) ;
                                    ?> <option value="<?php echo $product_id ; ?>" selected="selected"><?php echo esc_html( $json_ids ) ; ?></option><?php
                                }
                            }
                        }
                        ?>        
                    </select>
                </td>
            </tr>
            <?php
        } else {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="<?php echo $field_id ; ?>"><?php _e( $field_label , SRP_LOCALE ) ; ?></label>
                </th>
                <td class="forminp forminp-select">
                    <input type="hidden" class="wc-product-search" style="width: 350%;" id="<?php echo $field_id ; ?>"  name="<?php echo $field_id ; ?>" data-placeholder="<?php _e( 'Search for a product' , SRP_LOCALE ) ; ?>" data-action="woocommerce_json_search_products_and_variations" data-multiple="true" data-selected="<?php
                           $json_ids = array() ;
                           if ( $getproducts != "" ) {
                               $list_of_produts = $getproducts ;
                               $product_ids     = array_filter( array_map( 'absint' , ( array ) explode( ',' , $getproducts ) ) ) ;

                               foreach ( $product_ids as $product_id ) {
                                   $product = wc_get_product( $product_id ) ;
                                   if ( is_object( $product ) ) {
                                       $json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() ) ;
                                   }
                               } echo esc_attr( json_encode( $json_ids ) ) ;
                           }
                           ?>" value="<?php echo implode( ',' , array_keys( $json_ids ) ) ; ?>" />
                </td>
            </tr>

            <?php
        }
    }
}
