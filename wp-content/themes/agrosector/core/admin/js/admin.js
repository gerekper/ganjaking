(function($) { 
"use strict"; 
// Popup
function gt3_show_admin_pop(gt3_message_text, gt3_message_type) {
    // Success - gt3_message_type = 'info_message'
    // Error - gt3_message_type = 'error_message'
    jQuery(".gt3_result_message").remove();
    jQuery("body").removeClass('active_message_popup').addClass('active_message_popup');
    jQuery("body").append("<div class='gt3_result_message " + gt3_message_type + "'>" + gt3_message_text + "</div>");
    var messagetimer = setTimeout(function () {
        jQuery(".gt3_result_message").fadeOut();
        jQuery("body").removeClass('active_message_popup');
        clearTimeout(messagetimer);
    }, 3000);
}

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function waiting_state_start() {
    jQuery(".waiting-bg").show();
}

function waiting_state_end() {
    jQuery(".waiting-bg").hide();
}

jQuery(document).ready(function() {
    var navigationForm = jQuery('#update-nav-menu');
    navigationForm.on('change', '[data-item-option]', function() {
        if (jQuery(this).attr('type') == 'checkbox') {
            jQuery(this).parent().find('input[type=hidden]').val(jQuery(this).parent().find('input[type=checkbox]').is(":checked"));
            if (jQuery(this).hasClass('mega-menu-checkbox')) {
                if (jQuery(this).parent().find('input[type=checkbox]').is(":checked")) {
                    jQuery(this).parents('.menu-item ').addClass('menu-item-megamenu-active');
                    $item = jQuery(this).parents('.menu-item ');
                    do{
                        $item = $item.next();
                        if (!$item.hasClass('menu-item-depth-0')) {
                            $item.addClass('menu-item-megamenu_sub-active');
                        }
                    } while(!$item.hasClass('menu-item-depth-0') && $item.next().length != 0)
                }else{
                    jQuery(this).parents('.menu-item ').removeClass('menu-item-megamenu-active');
                    $item = jQuery(this).parents('.menu-item ');
                    do{
                        $item = $item.next();
                        if (!$item.hasClass('menu-item-depth-0')) {
                            $item.removeClass('menu-item-megamenu_sub-active');
                        }
                    } while(!$item.hasClass('menu-item-depth-0') && $item.next().length != 0)
                }
            }
        }
        if (jQuery(this)[0].tagName == 'SELECT') {
            jQuery(this).parent().find('input[type=hidden]').val(jQuery(this)[0].value);
        }
    });

    //Mailchimp Form
    if (jQuery('.mc_form_inside').length) {
        jQuery('.mc_form_inside').each(function () {
            if (jQuery(this).find('.mc_merge_var').length == '1') {
                jQuery(this).addClass('has_only_email');
            }
        });
    }
});
})(jQuery);