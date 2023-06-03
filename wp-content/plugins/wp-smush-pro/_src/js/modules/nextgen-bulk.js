import Smush from '../smush/smush';
import SmushProcess from '../common/progressbar';

(function($) {
    $(function() {
        /** Handle NextGen Gallery smush button click **/
        $('body').on('click', '.wp-smush-nextgen-send', function (e) {
            // prevent the default action
            e.preventDefault();
            new Smush($(this), false, 'nextgen');
        });

        /** Handle NextGen Gallery Bulk smush button click **/
        $('body').on('click', '.wp-smush-nextgen-bulk', function (e) {
            // prevent the default action
            e.preventDefault();

            // Remove existing Re-Smush notices.
            // TODO: REMOVE re-smush-notice since no longer used.
            $('.wp-smush-resmush-notice').remove();

            //Check for ids, if there is none (Unsmushed or lossless), don't call smush function
            if (
                'undefined' === typeof wp_smushit_data ||
                (wp_smushit_data.unsmushed.length === 0 &&
                    wp_smushit_data.resmush.length === 0)
            ) {
                return false;
            }

            const bulkSmush = new Smush( $(this), true, 'nextgen' );
			SmushProcess.setOnCancelCallback( () => {
				bulkSmush.cancelAjax();
			}).update( 0, bulkSmush.ids.length ).show();

            jQuery('.wp-smush-all, .wp-smush-scan').prop('disabled', true);
            $('.wp-smush-notice.wp-smush-remaining').hide();

			// Run bulk Smush.
			bulkSmush.run();
        })
        .on('click', '.wp-smush-trigger-nextgen-bulk', function(e){
            e.preventDefault();
            const bulkSmushButton = $('.wp-smush-nextgen-bulk');
            if ( bulkSmushButton.length ) {
                bulkSmushButton.trigger('click');
                SUI.closeNotice( 'wp-smush-ajax-notice' );
            }
        });

    });
}(window.jQuery));