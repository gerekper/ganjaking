import { saveAs } from 'file-saver'

( function ( $ ) {

    "use strict";

    $( document ).ready( function () {

        const $exportButton = document.querySelector( '#mdp-export-btn' );
        const { ajaxURL, nonce } = mdpUngrabber;

        /**
         * Export settings
         */

        try {

            let isFileSaverSupported = !!new Blob;

        } catch ( e ) {

            console.warn( 'Settings export not available.' );

        }
        finally {

            $exportButton.addEventListener('click', (e) => {

                e.preventDefault();

                let xhr = new XMLHttpRequest();
                xhr.open('POST', ajaxURL, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
                xhr.responseType = 'json';
                xhr.onload = () => {

                    if (xhr.status === 200) {

                        // Wrong settings format
                        if (typeof (xhr.response) !== 'object') {
                            console.warn('Wrong settings format! See details above:');
                            console.warn(xhr.response);
                        }

                        // Download file
                        let fileName = 'ungrabber-settings.json'
                        let fileContent = new Blob(
                            [ JSON.stringify( xhr.response, null, 4 ) ],
                            {
                                type: 'application/json',
                                name: fileName
                            }
                        );
                        saveAs( fileContent, fileName );
                        $( '#submit' ).click();

                    } else {

                        // Wrong response
                        console.error(`${xhr.statusText}: ${xhr.status}. Error during settings export! See details above:`);
                        console.error(xhr);

                    }

                };
                xhr.onerror = () => {

                    // Error sending request
                    console.error('Error sending request!');

                };
                xhr.send(`action=export_settings_ungrabber&nonce=${nonce}`);

            });

        }

        /**
         * Import settings
         */

        /** Drag & Drop JSON reader. */
        let $dropZone = $( '.mdp-tab-name-migration .mdp-drop-zone' );
        $dropZone.on( 'dragenter', function() {
            hideMessage();
            $( this ).addClass( 'mdp-hover' );
        } );

        $dropZone.on('dragleave', function() {
            $( this ).removeClass( 'mdp-hover' );
        } );

        /** Setup Drag & Drop. */
        $dropZone.on( 'dragover', handleDragOver );

        /** Text Input to store key file. */
        let $key_input = $( '.mdp-drop-zone-input' );

        /**
         * Read dragged file by JS.
         **/
        $dropZone.on( 'drop', function ( e ) {

            e.stopPropagation();
            e.preventDefault();

            // Show busy spinner.
            $( this ).removeClass( 'mdp-hover' );
            $dropZone.addClass( 'mdp-busy' );

            let file = e.originalEvent.dataTransfer.files[0]; // FileList object.

            /** Check is one valid JSON file. */
            if ( ! checkKeyFile( file ) ) {
                $dropZone.removeClass( 'mdp-busy' );
                return;
            }

            /** Read key file to input. */
            readFile( file )

        } );

        /**
         * Read key file to input.
         **/
        function readFile( file ) {

            let reader = new FileReader();

            /** Closure to capture the file information. */
            reader.onload = ( function( theFile ) {

                return function( e ) {

                    let jsonContent = e.target.result;
                    let json = JSON.parse( jsonContent );

                    /** Check if a string is a valid JSON string. */
                    if ( ! isJSON( jsonContent ) ) {

                        showErrorMessage( 'Error: Uploaded file is empty or not a valid JSON file.' );

                        $dropZone.removeClass( 'mdp-busy' );
                        return;

                    }

                    /** Check if the key has required field. */
                    let key = JSON.parse( jsonContent );
                    if ( typeof( key.plugin_info ) === 'undefined' ){

                        showErrorMessage( 'Error: Your API key file looks like not valid. Please make sure you use the correct key.' );

                        $dropZone.removeClass( 'mdp-busy' );
                        return;

                    }

                    /** Hide error messages. */
                    hideMessage();

                    let xhr = new XMLHttpRequest();
                    xhr.open('POST', ajaxURL, true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
                    xhr.onload = () => {

                        if ( xhr.status === 200 ) {

                            const response = JSON.parse( xhr.response );

                            if ( ! response.status ) {
                                showErrorMessage( `Error: ${ response.message }` );
                                $dropZone.removeClass( 'mdp-busy' );
                            } else {
                                $( '#submit' ).click(); // Imported
                            }

                        } else {

                            // Wrong response
                            console.error( 'Error during settings import! See details above:' );
                            console.error( xhr );

                        }

                    };
                    xhr.onerror = () => {

                        // Error sending request
                        console.error( 'Error sending request!' );

                    };
                    xhr.send(`action=import_settings_ungrabber&nonce=${ nonce }&import=${ encodeURIComponent( JSON.stringify( json ) ) }`);

                };

            } )( file );

            /** Read file as text. */
            reader.readAsText( file );

        }

        /**
         * Show upload form on click.
         **/
        let $file_input = $( '#mdp-dnd-file-input' );
        $dropZone.on( 'click', function () {

            $file_input.click();

        } );

        $file_input.on( 'change', function ( e ) {

            $dropZone.addClass( 'mdp-busy' );

            let file = e.target.files[0];

            /** Check is one valid JSON file. */
            if ( ! checkKeyFile( file ) ) {
                $dropZone.removeClass( 'mdp-busy' );
                return;
            }

            /** Read key file to input. */
            readFile( file );

        } );

        /** Show Error message under drop zone. */
        function showErrorMessage( msg ) {

            let $msgBox = $dropZone.next();

            $msgBox.addClass( 'mdp-error' ).html( msg );

        }

        /** Hide message message under drop zone. */
        function hideMessage() {

            let $msgBox = $dropZone.next();

            $msgBox.removeClass( 'mdp-error' ).html( '' );

        }

        /**
         * Check if a string is a valid JSON string.
         *
         * @param str - JSON string to check.
         **/
        function isJSON( str ) {

            try {

                JSON.parse( str );

            } catch ( e ) {

                return false;

            }

            return true;

        }

        function handleDragOver( e ) {

            e.stopPropagation();
            e.preventDefault();

        }

        /**
         * Check file is a single valid JSON file.
         *
         * @param file - JSON file to check.
         **/
        function checkKeyFile( file ) {

            /** Select only one file. */
            if ( null == file ) {

                showErrorMessage( 'Error: Failed to read file. Please try again.' );

                return false;

            }

            /** Process json file only. */
            if ( ! file.type.match( 'application/json' ) ) {

                showErrorMessage( 'Error: API Key must be a valid JSON file.' );

                return false;

            }

            return true;
        }

        /** Reset Key File. */
        $( '.mdp-reset-key-btn' ).on( 'click', function () {

            $key_input.val( '' );
            $( '#submit' ).trigger( 'click' );

        } );

    } );

} ( jQuery ) );
