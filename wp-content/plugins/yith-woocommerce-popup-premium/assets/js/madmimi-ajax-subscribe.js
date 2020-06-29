/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

jQuery( document).ready( function($){

    $(document).on('submit','#ypop-madmimi_ypop', function(){
        var t = $(this);

        $.ajax({
            cache: false,
            complete: function(jqXHR, status){

            },
            data: t.serialize(),
            error: function(jqXHR, status, error){
                t.siblings('.message-box').html( madmimi_localization.error_message );
            },
            success: function(data, status, jqXHR){
                t.siblings('.message-box').html(data);
            },
            url: madmimi_localization.url
        });

        return false;
    });
} );