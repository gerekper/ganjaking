/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

jQuery(document).ready(function($){
    $('.campaignmonitor-list-refresh').click(function(event){
        event.preventDefault();

        var t = $(this);
        var div_container = t.closest('div');
        $.ajax({
            beforeSend: function(){
                div_container.find('.spinner').css('display', 'inline-block');
                div_container.find('.button-secondary').attr('disabled', 'disabled');
                div_container.find('.select_wrapper span').html(campaignmonitor_localization.refresh_label);
            },
            cache: false,
            complete: function(jqXHR, status){
                div_container.find('.spinner').hide();
                div_container.find('.button-secondary').removeAttr('disabled');
            },
            data: {
                action: 'ypop_refresh_campaignmonitor_list',
                yit_campaignmonitor_refresh_list_nonce: campaignmonitor_localization.nonce_field,
                post_id: $('#post_ID').val(),
                apikey: $('#_cm-apikey').val(),
                clientid: $('#_cm-client-id').val()
            },
            error: function(jqXHR, status, error){
            },
            success: function(data, status, jqXHR){

                data = JSON.parse(data);

                div_container.find('select option:gt(0)').remove();

                $.each(data, function(i,v){
                    div_container.find('select')
                        .append(
                            '<option value="' + i + '">' + v + '</option>'
                        )
                })

                div_container.find('select').change();
            },
            url: campaignmonitor_localization.url
        });
    })
});
