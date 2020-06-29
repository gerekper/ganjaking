var sfn_select = null;
jQuery(document).ready(function($) {

    // Select2 Enhancement if it exists
    if ( $().select2 ) {
        sfn_select = function() {
            $(":input.chzn-select").filter(":not(.enhanced)").each( function() {
                $(this).select2();
            }).addClass('enhanced');
        }
    } else {
        // fallback to Chosen
        sfn_select = function() {
            $(":input.chzn-select").filter(":not(.enhanced)").each( function() {
                $(this).chosen();
            }).addClass('enhanced');
        }
    }

    sfn_select();
} );