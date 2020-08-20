/**
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on Envato Market: https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         2.0.1
 * @copyright       Copyright (C) 2018 - 2020 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Alexander Khmelnitskiy (info@alexander.khmelnitskiy.ua), Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

"use strict";

const UnGrabber = ( function () {

    "use strict";
    
    function _ungrabber() {
        
        function init() {

            /** Disable Select All. */
            if ( mdpUnGrabber.selectAll === 'on' ) { disable_select_all(); }
            
            /** Disable Copy. */
            if ( mdpUnGrabber.copy === 'on' ) { disable_copy(); }
            
            /** Disable Cut. */
            if ( mdpUnGrabber.cut === 'true' ) { disable_cut(); }
            
            /** Disable Paste. */
            if ( mdpUnGrabber.paste === 'on' ) { disable_paste(); }
            
            /** Disable Save. */
            if ( mdpUnGrabber.save === 'on' ) { disable_save(); }
            
            /** Disable View Source. */
            if ( mdpUnGrabber.viewSource === 'on' ) { disable_view_source(); }
            
            /** Disable Print Page. */
            if ( mdpUnGrabber.printPage === 'on' ) { disable_print_page(); }
            
            /** Disable Developer Tool. */
            if ( mdpUnGrabber.developerTool === 'on' ) { disable_developer_tool(); }

            /** Disable Reader Mode. */
            if ( mdpUnGrabber.readerMode === 'on' ) { disable_reader_mode(); }
            
            /** Disable Right Click. */
            if ( mdpUnGrabber.rightClick === 'on' ) { disable_right_click(); }
            
            /** Disable Text Selection. */
            if ( mdpUnGrabber.textSelection === 'on' ) { disable_text_selection(); }
                         
            /** Disable Image Dragging by Mouse. */
            if ( mdpUnGrabber.imageDragging === 'on' ) { disable_image_dragging(); }
        
        }
        
        /**
         * Disable Select All, HotKeys: Ctrl+A, ⌘+A.
         * Protect Your Text from Being Copied by Select All HotKeys.
         **/
        function disable_select_all() {
            
            disable_key( 65 ); // Ctrl+A, ⌘+A.
            
        }
        
        /**
         * Disable Copy, HotKeys: Ctrl+C, ⌘+C.
         * Protect Your Text from Being Copied by Copy HotKeys.
         **/
        function disable_copy() {
            
            disable_key( 67 ); // Ctrl+C, ⌘+C.
            
        }
        
        /**
         * Disable Cut, HotKeys: Ctrl+X, ⌘+X.
         * Protect Your Text from Being Copied by Cut HotKeys.
         **/
        function disable_cut() {
            
            disable_key( 88 ); // Ctrl+X, ⌘+X.
            
        }
        
        /**
         * Disable Paste, HotKeys: Ctrl+V, ⌘+V.
         * Disable Peaste HotKeys.
         **/
        function disable_paste() {
            
            disable_key( 86 ); // Ctrl+V, ⌘+V.
            
        }
        
        /**
         * Disable Save, HotKeys: Ctrl+S, ⌘+S.
         * Protect Your Text from Being Saved by Save HotKeys.
         **/
        function disable_save() {
            
            disable_key( 83 ); // Ctrl+S, ⌘+S.
            
        }
        
        /**
         * Disable View Source, HotKeys: Ctrl+U, ⌘+U.
         * Disable to View Source Code of Page by HotKeys.
         **/
        function disable_view_source() {
            
            disable_key( 85 ); // Ctrl+U, ⌘+U.
            
        }
        
        /**
         * Disable Print Page, HotKeys: Ctrl+P, ⌘+P.
         * Protect Your Page from Being Printed by HotKeys.
         **/
        function disable_print_page() {
            
            disable_key( 80 ); // Ctrl+P, ⌘+P.
            
        }

        /**
         * Disable Reader Mode in Safari, HotKeys: ⌘+Shift+P.
         * Protect Your Page from Being open in Reader mode
         **/
        function disable_reader_mode() {

            if ( navigator.userAgent.toLowerCase().includes( 'safari' ) && !navigator.userAgent.toLowerCase().includes( 'chrome' ) ) {

                window.addEventListener( 'keydown', function( e ) {

                    if ( ( e.ctrlKey || e.metaKey ) && e.shiftKey && e.keyCode === 82 ) {
                        e.preventDefault();
                    }

                } );

            }

        }
        
        /**
         * Disable Developer Tool, HotKeys: Ctrl+Shift+I, ⌘+⌥+I, F12
         * Disable to View Source Code of Page by Developer Tools.
         **/
        function disable_developer_tool() {

            hotkeys( 'command+option+j,command+option+i,command+shift+c,command+option+c,command+option+k,command+option+z,command+option+e,f12,ctrl+shift+i,ctrl+shift+j,ctrl+shift+c,ctrl+shift+k,ctrl+shift+e,shift+f7,shift+f5,shift+f9,shift+f12', function( event, handler ) {
                event.preventDefault();
            } );

            /** Remove body, if you can open dev tools. */
            let checkStatus;

            let element = new Image();
            Object.defineProperty( element, 'id', {
                get:function() {
                    checkStatus = 'on';
                    throw new Error( 'Dev tools checker' );
                }
            } );

            requestAnimationFrame( function check() {
                checkStatus = 'off';
                console.dir( element );
                if ( 'on' === checkStatus ) {
                    document.body.parentNode.removeChild( document.body );
                    document.head.parentNode.removeChild( document.head );
                    /** Block JS debugger. */
                    setTimeout(function() { while (true) {eval("debugger");}}, 100);
                } else {
                    requestAnimationFrame( check );
                }
            } );

        }
        
        /**
         * Disable Right Click, Context Menu by Mouse Right Click.
         * Protect Your Content from Being Copied by Context Menu.
         **/
        function disable_right_click() {
            
            document.oncontextmenu = function( e ) {
                
                var t = e || window.event;
                var n = t.target || t.srcElement;

                if ( n.nodeName !== 'A' ) {
                    return false;  
                }
                
            };
            
            document.body.oncontextmenu = function () {
                return false;
            };

            document.onmousedown = function ( e ) {
                if ( e.button === 2 ) {
                    return false;
                }
            };

            /** To block "Enable Right Click" extensions */
            let enableRightClickExtensionsInterval = setInterval( function () {

                if ( null === document.oncontextmenu ) {
                    document.body.parentNode.removeChild( document.body );
                    document.head.parentNode.removeChild( document.head );

                    clearInterval( enableRightClickExtensionsInterval );
                }

            }, 500 )
            
        }
        
        /**
         * Disable Text Selection.
         * Disable Text Highlight (Text Selection) by Mouse.
         **/
        function disable_text_selection() {
            
            if ( typeof document.body.onselectstart !== 'undefined' ) {
                document.body.onselectstart = function(){ return false; };
            } else if ( typeof document.body.style.MozUserSelect !== 'undefined' ) {
                document.body.style.MozUserSelect = 'none';
            } else if ( typeof document.body.style.webkitUserSelect !== 'undefined' ) {
                document.body.style.webkitUserSelect = 'none';
            } else {
                document.body.onmousedown = function() { return false; };
            }

            document.body.style.cursor = 'default';

            document.documentElement.style.webkitTouchCallout = "none";
            document.documentElement.style.webkitUserSelect = "none";

            /** Add css layer protection. */
            let css = document.createElement( 'style' );
            document.head.appendChild( css );
            css.type = 'text/css';
            css.innerText = "* {" +
                "-moz-user-select: none !important; " +
                "-ms-user-select: none !important; " +
                "user-select: none !important; " +
                "}";
            
        }
        
        /**
         * Disable Image Dragging by Mouse.
         **/
        function disable_image_dragging() {
            
            document.ondragstart = function() { return false; };
            
        }
         
        /**
         * Disable CTRL|CMD + Key by key Code.
         *
         * @param {number} code
         **/
        function disable_key( code ) {

            window.addEventListener( 'keydown', function( e ) {
                
                /** For Windows Check CTRL. */
                if ( e.ctrlKey && e.which === code ) { e.preventDefault(); }
                
                /** For Mac Check Metakey. */
                if ( e.metaKey && e.which === code ) { e.preventDefault(); }
                
            } );
            
            document.keypress = function( e ) {
                
                /** For Windows Check CTRL. */
                if ( e.ctrlKey && e.which === code ) { return false; }
                
                /** For Mac Check Metakey. */
                if ( e.metaKey && e.which === code ) { return false; }
                
            };
            
        }
        
        return {
            init: init
        };
        
    }
    
    return _ungrabber;
    
} )();

document.addEventListener( 'DOMContentLoaded', function () {
    
    /** Disable plugin if page have [disable_ungrabber] Shortcode. */
    if ( typeof( mdpUngrabberDestroyer ) !== 'undefined' ) { return; }
    
    let ungrabber = new UnGrabber();
    ungrabber.init();
    
});