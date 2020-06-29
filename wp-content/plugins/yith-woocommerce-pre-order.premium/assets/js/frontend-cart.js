/**
 * Created by Carlos Mora on 25/07/2016.
 */
(function ($) {
    $(document).ready(function ($) {

        load_dates();
        $(document.body).bind('updated_checkout', load_dates);

    });

    function load_dates() {
        $('div.pre_order_on_cart').each(function () {
            var unix_time = parseInt($(this).data('time'));
            var date = new Date(0);
            date.setUTCSeconds(unix_time);
            var time = date.toLocaleTimeString();
            time = time.slice(0, -3);
            $(this).find('.availability_date').text(date.toLocaleDateString());
            $(this).find('.availability_time').text(time);
        });
    }
})
(jQuery);