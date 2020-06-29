/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

jQuery( document).ready( function($){

    $('.mailpoet-subscription-ajax-submit').on( 'click', function(e){
        e.preventDefault();
        var t = $(this);

        $.ajax({
            cache: false,
            complete: function(jqXHR, status){

            },
            data: t.parents('form').serialize(),
            error: function(jqXHR, status, error){
                t.parents('form').siblings('.message-box').html( mailpoet_localization.error_message );
            },
            success: function(data, status, jqXHR){
                t.parents('form').siblings('.message-box').html(data);
            },
            url: mailpoet_localization.url
        });
    });

} );
