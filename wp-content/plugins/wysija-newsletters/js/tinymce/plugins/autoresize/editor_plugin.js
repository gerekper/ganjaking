/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
	/**
	 * Auto Resize
	 *
	 * This plugin automatically resizes the content area to fit its content height.
	 * It will retain a minimum height, which is the height of the content area when
	 * it's initialized.
	 */
	tinymce.create('tinymce.plugins.AutoResizePlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
                        // Create namespace for variables on the editor instance
                        tinymce.createNS('springload', ed);

                        // Define minimum height in the namespace
                        ed.springload.autoresize_min_height = ed.getElement().offsetHeight;

                        // Things to do before the editor is ready
                        ed.onPreInit.add(function(ed) {
                            // Get content element, store it in the namespace
                            ed.springload.content_container = ed.getContentAreaContainer().firstChild;

                            // Add onload listener to IFRAME
                            if(ed.springload.content_container.tagName == 'IFRAME'){

                                tinymce.dom.Event.add(ed.springload.content_container, 'load', function(e) {
                                    tinymce.EditorManager.get(ed.id).execCommand('mceAutoResize');
                                });
                            }
                        });

                        // Backup TinyMCE default focus function, to prevent jumping in IE
                        ed.springload.defaultFocus = ed.focus;
                        ed.focus = function(){};

			var t = this, oldSize = 0;

			if (ed.getParam('fullscreen_is_enabled'))
				return;

			/**
			 * This method gets executed each time the editor needs to resize.
			 */
			function resize() {
				var deltaSize, d = ed.getDoc(), body = d.body, de = d.documentElement, DOM = tinymce.DOM, resizeHeight = ed.springload.autoresize_min_height, myHeight;

                                // Fix: when removing long text, table container would not resize
                                DOM.setStyle(DOM.get(ed.id + '_tbl'), 'height', 'auto');

				// Get height differently depending on the browser used
				myHeight = tinymce.isIE ? body.scrollHeight : (tinymce.isWebKit && body.clientHeight == 0 ? 0 : body.offsetHeight);

				// Don't make it smaller than the minimum height
				if (myHeight > t.autoresize_min_height)
					resizeHeight = myHeight;

				// If a maximum height has been defined don't exceed this height
				if (t.autoresize_max_height && myHeight > t.autoresize_max_height) {
					resizeHeight = t.autoresize_max_height;
					body.style.overflowY = "auto";
					de.style.overflowY = "auto"; // Old IE
				} else {
					body.style.overflowY = "hidden";
					de.style.overflowY = "hidden"; // Old IE
					body.scrollTop = 0;
				}

				// Resize content element
				if (resizeHeight !== oldSize) {
					deltaSize = resizeHeight - oldSize;
					DOM.setStyle(DOM.get(ed.id + '_ifr'), 'height', resizeHeight + 'px');
					oldSize = resizeHeight;

					// WebKit doesn't decrease the size of the body element until the iframe gets resized
					// So we need to continue to resize the iframe down until the size gets fixed
					if (tinymce.isWebKit && deltaSize < 0)
						resize();
				}
                                /*if(ed.springload.throbbing){
                                    //ed.setProgressState(false);
                                    //ed.setProgressState(true);
                                }*/
			};

			t.editor = ed;

			// Define minimum height
			t.autoresize_min_height = parseInt(ed.getParam('autoresize_min_height', ed.getElement().offsetHeight));

			// Define maximum height
			t.autoresize_max_height = parseInt(ed.getParam('autoresize_max_height', 0));

			// Add padding at the bottom for better UX
			ed.onInit.add(function(ed){
                            // show loading
                            ed.setProgressState(true);
                            ed.springload.throbbing = true;

                            // set padding bottom
                            ed.dom.setStyle(ed.getBody(), 'paddingBottom', ed.getParam('autoresize_bottom_margin', 50) + 'px');
			});

			// Add appropriate listeners for resizing content area
			ed.onChange.add(resize);
			ed.onSetContent.add(resize);
			ed.onPaste.add(resize);
			ed.onKeyUp.add(resize);
			ed.onPostRender.add(resize);

			if (ed.getParam('autoresize_on_init', true)) {
				ed.onLoad.add(resize);
				ed.onLoadContent.add(resize);
                                setTimeout("tinymce.EditorManager.get('" + ed.id + "').execCommand('mceAutoResizeTimeout');", 500);
			}

			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mceAutoResize', resize);

                        // Interval resize trigger function
                        ed.addCommand('mceAutoResizeTimeout', function() {
                            // Resize
                            this.execCommand('mceAutoResize')
                            // Disable throbber
                            this.setProgressState(false);
                            this.springload.throbbing = false;
                            // Restore TinyMCE default focus function
                            if(typeof(this.springload.defaultFocus) == "function"){
                                this.focus = this.springload.defaultFocus;
                                this.springload.defaultFocus = false;
                            }
                        });
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'Auto Resize',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/autoresize',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('autoresize', tinymce.plugins.AutoResizePlugin);
})();
