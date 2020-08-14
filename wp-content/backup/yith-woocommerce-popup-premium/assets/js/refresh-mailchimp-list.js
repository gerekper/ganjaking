/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

jQuery(document).ready(function($){
    $('.mailchimp-list-refresh').click(function(event){
        event.preventDefault();

        var t = $(this);
        var div_container = t.closest('div');
        $.ajax({
            beforeSend: function(){
                div_container.find('.spinner').css('display', 'inline-block');
                div_container.find('.button-secondary').attr('disabled', 'disabled');
                div_container.find('.select_wrapper span').html(mailchimp_localization.refresh_label);
            },
            cache: false,
            complete: function(jqXHR, status){
                div_container.find('.spinner').hide();
                div_container.find('.button-secondary').removeAttr('disabled');
            },
            data: {
                action: 'ypop_refresh_mailchimp_list',
                yit_mailchimp_refresh_list_nonce: mailchimp_localization.nonce_field,
                apikey:  $('#_mailchimp-apikey').val(),
                post_id: $('#post_ID').val()
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
            url: mailchimp_localization.url
        });
    })
});
