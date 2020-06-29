<?php

add_action('admin_head', 'rs_function_to_expand_collpase_js');

function rs_function_to_expand_collpase_js() {
    if (get_option('rs_expand_collapse') == '1') {
        ?>
        <script type="text/javascript">
           jQuery(function ($) {
             jQuery('.rs_section_wrapper h2').nextUntil('h2').hide();
             jQuery('.rs_membership_compatible_wrapper h2').nextUntil('h2').hide();
             jQuery('.rs_bsn_compatible_wrapper h2').nextUntil('h2').hide();
             jQuery( '.rs_affs_compatible_wrapper h2' ).nextUntil( 'h2' ).hide() ;
             jQuery('.rs_fpwcrs_compatible_wrapper h2').nextUntil('h2').hide();
             jQuery('.rs_subscription_compatible_wrapper h2').nextUntil('h2').hide();
             jQuery('.rs_coupon_compatible_wrapper h2').nextUntil('h2').hide();
              jQuery('.rs_adminstrator_wrapper h2').nextUntil('h2').hide();
             jQuery('.rs_exp_col input[type="checkbox"]').click(function(){
                 if(jQuery(this).is(":checked")){
                    jQuery('.rs_section_wrapper h2').nextUntil('h2').show();
                    jQuery('.rs_membership_compatible_wrapper h2').nextUntil('h2').show();
                    jQuery('.rs_bsn_compatible_wrapper h2').nextUntil('h2').show();
                    jQuery( '.rs_affs_compatible_wrapper h2' ).nextUntil( 'h2' ).show() ;
                    jQuery('.rs_fpwcrs_compatible_wrapper h2').nextUntil('h2').show();
                    jQuery('.rs_subscription_compatible_wrapper h2').nextUntil('h2').show();
                    jQuery('.rs_coupon_compatible_wrapper h2').nextUntil('h2').show();
                    jQuery('.rs_adminstrator_wrapper h2').nextUntil('h2').show();
                }
                else if(jQuery(this).is(":not(:checked)")){
                    jQuery('.rs_section_wrapper h2').nextUntil('h2').hide();
                    jQuery('.rs_membership_compatible_wrapper h2').nextUntil('h2').hide();
                    jQuery('.rs_bsn_compatible_wrapper h2').nextUntil('h2').hide();
                    jQuery( '.rs_affs_compatible_wrapper h2' ).nextUntil( 'h2' ).hide() ;
                    jQuery('.rs_fpwcrs_compatible_wrapper h2').nextUntil('h2').hide();
                    jQuery('.rs_subscription_compatible_wrapper h2').nextUntil('h2').hide();
                    jQuery('.rs_coupon_compatible_wrapper h2').nextUntil('h2').hide();
                     jQuery('.rs_adminstrator_wrapper h2').nextUntil('h2').hide();
                }
                });
           });  
        </script>
    <?php } else { ?>
        <script type="text/javascript">
           jQuery(function ($) {
             jQuery('.rs_section_wrapper h2').nextUntil('h2').show();
             jQuery('.rs_membership_compatible_wrapper h2').nextUntil('h2').show();
             jQuery('.rs_bsn_compatible_wrapper h2').nextUntil('h2').show();
             jQuery( '.rs_affs_compatible_wrapper h2' ).nextUntil( 'h2' ).show() ;
             jQuery('.rs_fpwcrs_compatible_wrapper h2').nextUntil('h2').show();
             jQuery('.rs_subscription_compatible_wrapper h2').nextUntil('h2').show();
             jQuery('.rs_coupon_compatible_wrapper h2').nextUntil('h2').show();
              jQuery('.rs_adminstrator_wrapper h2').nextUntil('h2').show();
             jQuery('.rs_exp_col input[type="checkbox"]').click(function(){
                 if(jQuery(this).is(":checked")){
                    jQuery('.rs_section_wrapper h2').nextUntil('h2').hide();
                    jQuery('.rs_membership_compatible_wrapper h2').nextUntil('h2').hide();
                    jQuery('.rs_bsn_compatible_wrapper h2').nextUntil('h2').hide();
                    jQuery( '.rs_affs_compatible_wrapper h2' ).nextUntil( 'h2' ).hide() ;
                    jQuery('.rs_fpwcrs_compatible_wrapper h2').nextUntil('h2').hide();
                    jQuery('.rs_subscription_compatible_wrapper h2').nextUntil('h2').hide();
                    jQuery('.rs_coupon_compatible_wrapper h2').nextUntil('h2').hide();
                     jQuery('.rs_adminstrator_wrapper h2').nextUntil('h2').hide();
                }
                else if(jQuery(this).is(":not(:checked)")){
                    jQuery('.rs_section_wrapper h2').nextUntil('h2').show();
                    jQuery('.rs_membership_compatible_wrapper h2').nextUntil('h2').show();
                    jQuery('.rs_bsn_compatible_wrapper h2').nextUntil('h2').show();
                    jQuery( '.rs_affs_compatible_wrapper h2' ).nextUntil( 'h2' ).show() ;
                    jQuery('.rs_fpwcrs_compatible_wrapper h2').nextUntil('h2').show();
                    jQuery('.rs_subscription_compatible_wrapper h2').nextUntil('h2').show();
                    jQuery('.rs_coupon_compatible_wrapper h2').nextUntil('h2').show();
                     jQuery('.rs_adminstrator_wrapper h2').nextUntil('h2').show();
                }
             });
           });
        </script>       
        <?php }

}
    