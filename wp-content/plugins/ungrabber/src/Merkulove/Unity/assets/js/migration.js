/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/file-saver/dist/FileSaver.min.js":
/*!*******************************************************!*\
  !*** ./node_modules/file-saver/dist/FileSaver.min.js ***!
  \*******************************************************/
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function(a,b){if(true)!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_FACTORY__ = (b),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));else {}})(this,function(){"use strict";function b(a,b){return"undefined"==typeof b?b={autoBom:!1}:"object"!=typeof b&&(console.warn("Deprecated: Expected third argument to be a object"),b={autoBom:!b}),b.autoBom&&/^\s*(?:text\/\S*|application\/xml|\S*\/\S*\+xml)\s*;.*charset\s*=\s*utf-8/i.test(a.type)?new Blob(["\uFEFF",a],{type:a.type}):a}function c(a,b,c){var d=new XMLHttpRequest;d.open("GET",a),d.responseType="blob",d.onload=function(){g(d.response,b,c)},d.onerror=function(){console.error("could not download file")},d.send()}function d(a){var b=new XMLHttpRequest;b.open("HEAD",a,!1);try{b.send()}catch(a){}return 200<=b.status&&299>=b.status}function e(a){try{a.dispatchEvent(new MouseEvent("click"))}catch(c){var b=document.createEvent("MouseEvents");b.initMouseEvent("click",!0,!0,window,0,0,0,80,20,!1,!1,!1,!1,0,null),a.dispatchEvent(b)}}var f="object"==typeof window&&window.window===window?window:"object"==typeof self&&self.self===self?self:"object"==typeof __webpack_require__.g&&__webpack_require__.g.global===__webpack_require__.g?__webpack_require__.g:void 0,a=f.navigator&&/Macintosh/.test(navigator.userAgent)&&/AppleWebKit/.test(navigator.userAgent)&&!/Safari/.test(navigator.userAgent),g=f.saveAs||("object"!=typeof window||window!==f?function(){}:"download"in HTMLAnchorElement.prototype&&!a?function(b,g,h){var i=f.URL||f.webkitURL,j=document.createElement("a");g=g||b.name||"download",j.download=g,j.rel="noopener","string"==typeof b?(j.href=b,j.origin===location.origin?e(j):d(j.href)?c(b,g,h):e(j,j.target="_blank")):(j.href=i.createObjectURL(b),setTimeout(function(){i.revokeObjectURL(j.href)},4E4),setTimeout(function(){e(j)},0))}:"msSaveOrOpenBlob"in navigator?function(f,g,h){if(g=g||f.name||"download","string"!=typeof f)navigator.msSaveOrOpenBlob(b(f,h),g);else if(d(f))c(f,g,h);else{var i=document.createElement("a");i.href=f,i.target="_blank",setTimeout(function(){e(i)})}}:function(b,d,e,g){if(g=g||open("","_blank"),g&&(g.document.title=g.document.body.innerText="downloading..."),"string"==typeof b)return c(b,d,e);var h="application/octet-stream"===b.type,i=/constructor/i.test(f.HTMLElement)||f.safari,j=/CriOS\/[\d]+/.test(navigator.userAgent);if((j||h&&i||a)&&"undefined"!=typeof FileReader){var k=new FileReader;k.onloadend=function(){var a=k.result;a=j?a:a.replace(/^data:[^;]*;/,"data:attachment/file;"),g?g.location.href=a:location=a,g=null},k.readAsDataURL(b)}else{var l=f.URL||f.webkitURL,m=l.createObjectURL(b);g?g.location=m:location.href=m,g=null,setTimeout(function(){l.revokeObjectURL(m)},4E4)}});f.saveAs=g.saveAs=g, true&&(module.exports=g)});

//# sourceMappingURL=FileSaver.min.js.map

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/
/******/ 	/* webpack/runtime/global */
/******/ 	(() => {
/******/ 		__webpack_require__.g = (function() {
/******/ 			if (typeof globalThis === 'object') return globalThis;
/******/ 			try {
/******/ 				return this || new Function('return this')();
/******/ 			} catch (e) {
/******/ 				if (typeof window === 'object') return window;
/******/ 			}
/******/ 		})();
/******/ 	})();
/******/
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!*******************************************************!*\
  !*** ./wp-content/plugins/ungrabber/js/source/exp.js ***!
  \*******************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var file_saver__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! file-saver */ "./node_modules/file-saver/dist/FileSaver.min.js");
/* harmony import */ var file_saver__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(file_saver__WEBPACK_IMPORTED_MODULE_0__);


( function ( $ ) {

    "use strict";

    $( document ).ready( function () {

        const $exportButton = document.querySelector( '#mdp-export-btn' );
        const { ajaxURL, nonce } = mdpUngrabberUnity;

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
                        (0,file_saver__WEBPACK_IMPORTED_MODULE_0__.saveAs)( fileContent, fileName );
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

})();

/******/ })()
;
