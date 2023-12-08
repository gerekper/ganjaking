/**
 * Start source code widget script
 */

( function( $, elementor ) {

    'use strict';

    var sourceCodeWidget = function( $scope, $ ) {
        var $sourceCode = $scope.find('.bdt-source-code'),
            $preCode = $sourceCode.find('pre > code');

        if ( ! $sourceCode.length ) {
            return;
        }

        // create clipboard for every copy element
        var clipboard = new ClipboardJS('.bdt-copy-button', {
            target: function target(trigger) {
                return trigger.nextElementSibling;
            }
        });

        // do stuff when copy is clicked
        clipboard.on('success', function (event) {
            event.trigger.textContent = 'copied!';
            setTimeout(function () {
                event.clearSelection();
                event.trigger.textContent = 'copy';
            }, 2000);
        });

        //if ($lng_type !== undefined && $code !== undefined) {
            Prism.highlightElement($preCode.get(0));
       // }

    };

    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-source-code.default', sourceCodeWidget );
    });

}( jQuery, window.elementorFrontend ) );

/**
 * End source code widget script
 */

