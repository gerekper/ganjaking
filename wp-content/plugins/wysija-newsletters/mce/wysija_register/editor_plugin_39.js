
(function() {
	function getUrlVars() {
            var vars = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                vars[key] = value;
            });
            return vars;
        }
        tinymce.create('tinymce.plugins.WYSIJA_register', {

                init : function(ed, url) {
                    var t = this;
                    t.url = url;
                    t._createButtons();
                    t.editValue="";
                    t.post_id=getUrlVars()["post"];
                    // Register commands

                    ed.addCommand('wysijaRegister', function() {
                       ed.windowManager.open({
                           /*file : url + '/wysija_register.php',*/
                            file : ajaxurl+"?action=wysija_ajax&wysilog=1&controller=tmce&task=registerAdd",
                            width : 440,
                            height : 185 ,
                            inline : 1
                        }, {
                            plugin_url : url
                        });
                    });

//                    ed.addCommand('wysijaRegisterEdit', function() {
//                            ed.plugins.wysija_register._hideButtons();
//                            ed.windowManager.open({
//                               /*file : url + '/wysija_register.php?'+t.editValue,*/
//                               file : ajaxurl+"?action=wysija_ajax&wysilog=1&controller=tmce&task=registerEdit&widget-data64="+t.editValue+'&post_id='+t.post_id,
//                                width : 240 ,
//                                height : 185,
//                                inline : 1
//                            }, {
//                                plugin_url : url
//                            });
//                    });

                    ed.addButton('wysija_register', {
				title : 'Add MailPoet newsletter subscription form',
				image : url+'/wysija_register.png',
                                cmd: 'wysijaRegister'
			});
                    // Register Events


                    ed.onMouseUp.add(function(ed, e) {
                                if ( tinymce.isWebKit || tinymce.isOpera )
					return;

				if ( ed.dom.getParent(e.target, 'div.mceTemp') || ed.dom.is(e.target, 'div.mceTemp') ) {
					window.setTimeout(function(){
						var ed = tinyMCE.activeEditor, n = ed.selection.getNode(), DL = ed.dom.getParent(n, 'dl.wp-caption');

						if ( DL && n.width != ( parseInt(ed.dom.getStyle(DL, 'width'), 10) - 10 ) ) {
							ed.dom.setStyle(DL, 'width', parseInt(n.width, 10) + 10);
							ed.execCommand('mceRepaint');
						}
					}, 100);
				}
			});

			ed.onMouseDown.add(function(ed, e) {
				var p;

				if ( e.target.nodeName == 'DIV'   && e.target.className=="wysija-register" ) {
                                        t.editValue=e.target.innerHTML;
					ed.plugins.wysija_register._showButtons(e.target, 'wp_wysijaregister');
					if ( tinymce.isGecko && (p = ed.dom.getParent(e.target, 'dl.wp-caption')) && ed.dom.hasClass(p.parentNode, 'mceTemp') )
						ed.selection.select(p.parentNode);
				}
			});
                        ed.onSaveContent.add(function(ed, o) {
				ed.plugins.wysija_register._hideButtons();
			});

			ed.onMouseDown.add(function(ed, e) {
				if ( e.target.nodeName != 'DIV' )
					ed.plugins.wysija_register._hideButtons();
			});

                },
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "Wysija registration Shortcode",
				version : "1.0"
			};
		},
                _showButtons : function(n, id) {
			var ed = tinyMCE.activeEditor, p1, p2, vp, DOM = tinymce.DOM, X, Y;

			vp = ed.dom.getViewPort(ed.getWin());
			p1 = DOM.getPos(ed.getContentAreaContainer());
			p2 = ed.dom.getPos(n);

			X = Math.max(p2.x - vp.x, 0) + p1.x;
			Y = Math.max(p2.y - vp.y, 0) + p1.y;

			DOM.setStyles(id, {
				'top' : Y+5+'px',
				'left' : X+5+'px',
				'display' : 'block'
			});

			if ( this.mceTout )
				clearTimeout(this.mceTout);

			this.mceTout = setTimeout( function(){ed.plugins.wysija_register._hideButtons();}, 5000 );
		},

		_hideButtons : function() {
			if ( !this.mceTout )
				return;

			if ( document.getElementById('wp_wysijaregister') )
				tinymce.DOM.hide('wp_wysijaregister');

			clearTimeout(this.mceTout);
			this.mceTout = 0;
		},
                _createButtons : function() {
			var t = this, ed = tinyMCE.activeEditor, DOM = tinymce.DOM, editButton, dellButton;

			DOM.remove('wp_wysijaregister');

			DOM.add(document.body, 'div', {
				id : 'wp_wysijaregister',
				style : 'display:none;'
			});

			editButton = DOM.add('wp_wysijaregister', 'img', {
				src : t.url+'/wysija_register.png',
				id : 'wp_edit_wysinl_btn',
				width : '24',
				height : '24'
			});
//
//			tinymce.dom.Event.add(editButton, 'mousedown', function(e) {
//				var ed = tinyMCE.activeEditor;
//				ed.execCommand("wysijaRegisterEdit");
//			});
//
//			dellButton = DOM.add('wp_wysijaregister', 'img', {
//				src : t.url+'/delete.png',
//				id : 'wp_del_wysinl_btn',
//				width : '24',
//				height : '24'
//			});
//
//			tinymce.dom.Event.add(dellButton, 'mousedown', function(e) {
//				var ed = tinyMCE.activeEditor, el = ed.selection.getNode(), p;
//				if ( el.nodeName == 'DIV'  && el.className=="wysija-register" ) {
//					ed.dom.remove(el);
//                                        ed.plugins.wysija_register._hideButtons();
//					ed.execCommand('mceRepaint');
//					return false;
//				}
//			});
		}

	});
	tinymce.PluginManager.add('wysija_register', tinymce.plugins.WYSIJA_register);
})();


