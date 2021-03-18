/** @var ct_ultimate_gdpr_cookie_list object - localized */

jQuery(document).on('ready', function () {

    render_list(jQuery('#ct-ultimate-gdpr-party-filter').val());

    jQuery('#ct-ultimate-gdpr-party-filter').change(function () {
        render_list(jQuery(this).val());
    });

    function render_list(party_filter) {

        var cookies = ct_ultimate_gdpr_cookie_list.list;
        jQuery("#ct-ultimate-gdpr-cookies-table tbody").empty();

        if (party_filter) {
            cookies = cookies.filter(function (party) {
                return party.first_or_third_party === party_filter;
            });
        }

        cookies.forEach(cookie => {
            jQuery("#ct-ultimate-gdpr-cookies-table tbody").append(render_row(cookie));
        });
    }

    function render_row(cookie) {
        var row = jQuery("<tr></tr>");

        for (const key in cookie) {
            if (key === 'can_be_blocked') {
                // check cookie block if true or false
                if (cookie[key] == 1)
                    row.append('<td><i class="fa fa-check" aria-hidden="true" style="color: green"></i></td>');
                else
                    row.append('<td><i class="fa fa-times" aria-hidden="true" style="color: red"></i></td>');
            } else
                row.append("<td>" + cookie[key] + "</td>");
        }

        return row;
    }

});
