/**
 * Created by axelk on 07.06.2017.
 */
jQuery(function($){
    // Initialize with options
    var $dp = $('.daterange-predefined');

    $dp.daterangepicker( {
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            maxDate: moment(),
            dateLimit: { days: 365 },
            opens: 'right',
            applyClass: 'btn-small bg-slate',
            cancelClass: 'btn-small btn-default'
        },
        function(start, end) {
            $dp.find('span').html(start.format('MMM D, YYYY') + ' &nbsp; - &nbsp; ' + end.format('MMM D, YYYY'));
            $( $dp.data('ads_from') ).val( start.format('YYYY-MM-DD') );
            $( $dp.data('ads_to') ).val( end.format('YYYY-MM-DD') );

            $.event.trigger({
                type: "datepicker:update",
                from: $( $dp.data('ads_from') ).val(),
                to: $( $dp.data('ads_to') ).val()
            });
        }
    );

    function dpViewChange( $obj ) {
        $obj.find('span').html(moment().subtract(29, 'days').format('MMM D, YYYY') + ' &nbsp; - &nbsp; ' + moment().format('MMM D, YYYY'));
    }

    dpViewChange( $dp );

    $(document).on('datepicker:change', function () {
        dpViewChange( $dp );
    });
});