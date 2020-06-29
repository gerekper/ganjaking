/***************************************************
 * REVOLUTION 6.0.0 BACKUP ADDON
 * @version: 2.0 (11.07.2018)
 * @author ThemePunch
***************************************************/

(function($) {		
		var addon = {},
			slug = "revslider-backup-addon",
			bricks = revslider_backup_addon.bricks;
			
		//CHECK GLOBAL ADDONS VARIABLE		
		RVS.LIB.ADDONS = RVS.LIB.ADDONS === undefined ? {} : RVS.LIB.ADDONS;
		RVS.LIB.ADDONS[slug] = RVS.LIB.ADDONS[slug]===undefined ? {} : RVS.LIB.ADDONS[slug];			
		RVS.LIB.ADDONS[slug].enable = _truefalse(revslider_backup_addon.enabled);
		
		var events = {
			
			getBackups: function() {
				
				RVS.F.ajaxRequest('fetch_slide_backups', {'slideID': RVS.S.slideId}, function(response) {
					
					if(response.data !== undefined && response.data.length > 0) {
							
						var _h = '';
						for(var key in response.data){
							
							if(!response.data.hasOwnProperty(key)) continue;
							_h += '<div class="rs-backup-data-holder" data-backup="'+response.data[key].id + '" data-slide_id="' + RVS.S.slideId + '">' + 
									'<div class="rs-backup-data-inner">' + 
										'<span class="rs-backup-time">' + 
											'<i class="material-icons">date_range</i><span class="rs-backup-date">' + response.data[key].created + '</span>' + 
										'</span>' + 
										'<div class="rs-load-backup basic_action_button rs-addon-backup-btn">' + bricks.load_backup + '</div>' + 
										// '<div class="rs-preview-backup basic_action_button rs-addon-backup-btn">' + bricks.preview_backup + '</div>' + 
									'</div>' + 
								'</div>';
							
						}
						
						addon.forms.container.html(_h);
						
					}
					else{
						addon.forms.container.html('<div class="nobackups">' + bricks.no_backups + '</div>');
					}
					
					// open the modal
					RVS.F.RSDialog.create({modalid:'rbm_addon_backups', bgopacity: 0.5});
					// RVS.F.RSDialog.centerInEditor(); // RSDialog handles this automatically now
					
					if(!addon.scrollInit) {
						
						addon.scrollInit = true;
						addon.forms.inner.RSScroll({
							
							wheelPropagation:true,
							suppressScrollX:true,				
							minScrollbarLength:100
							
						});
						
					}
					else {
						
						addon.forms.inner.RSScroll('update');
						
					}
					
				});
			
			},
			
			saveSlide: function(e, data) {
				
				data.session_id = $('#rs-session-id').val();
				
			},
			
			closeModal: function() {
						
				RVS.F.RSDialog.close();
			
			},
			
			loadBackup: function() {
				
				var dh = $(this).closest('.rs-backup-data-holder');
				if(confirm(bricks.restore + ': ' + dh.find('.rs-backup-date').text() + '?')) {
					
					events.closeModal();
					RVS.F.ajaxRequest('restore_slide_backup', {
						
						id: dh.data('backup'), 
						slide_id: dh.data('slide_id'), 
						session_id: $('#rs-session-id').val()
						
					}, function(response){
						
						if(response.success) window.location.reload(true);
						
					});
					
				}
				
			},
			
			showPreview: function() {
			
				// is a preview possible now?
			
			}
			
		};
		
		function init() {
		
			// CREATE CONTAINERS				
			RVS.F.addOnContainer.create({slug: slug, icon: 'backup', title: bricks.backup, alias: bricks.backup, slide: true});				
			
			// PICK THE CONTAINERS WE NEED			
			addon.forms = {slidegeneral : $('#form_slidegeneral_'+slug)};
			createSlideSettingsFields();	
			
			// init events
			addEvents();	
			addon.initialised = true;
		
		}
		
		if(!addon.initialised && revslider_backup_addon.enabled) init();
		
		// INITIALISE THE ADDON
		RVS.DOC.on(slug+'_init',function() {
			
			if(!addon.initialised) init();
			revslider_backup_addon.enabled = _truefalse(RVS.LIB.ADDONS[slug].enable);
			
			if(revslider_backup_addon.enabled) {				
				
				//Show Hide Areas
				punchgs.TweenLite.set('#gst_slide_'+slug,{display:"inline-block"});
				
			} 
			else {
				
				if(addon.initialised) {
				
					// DISABLE THINGS	
					var submenu = $('#gst_slide_'+slug);
					if(submenu.hasClass('selected')) {
						$('#gst_slide_'+slug).removeClass("selected");	
						$('#gst_slide_1').click();
					}
					
					punchgs.TweenLite.set('#gst_slide_'+slug,{display:"none"});	
					$('#form_slide_revslider-backup-addon').hide();
					
				}
				
			}	
			
		});
					
		// CREATE INPUT FIELDS
		function createSlideSettingsFields() {
			
			var _h = '';										
			_h += '<div class="form_inner_header"><i class="material-icons">backup</i>'+bricks.backup_addon+'</div>';
			_h += '<br>';
			_h += '<div class="collapsable" style="display:block !important">';								
			_h += '<div class="fullbutton callEventButton basic_action_button" data-evt="addonbackupsfetch">' + bricks.show_backups + '<i class="material-icons" style="margin-left: 10px">open_in_new</i></div>';
			_h += '<input type="hidden" id="rs-session-id" value="' + revslider_backup_addon.md5 + '" />';
			_h += '</div>';
			
			addon.forms.slidegeneral.append(_h);
			
			_h =  '<div class="rb-modal-wrapper" data-modal="rbm_addon_backups">';
			_h += '    <div class="rb-modal-inner">';
			_h += '        <div class="rb-modal-content">';
			_h += '            <div id="rbm_addon_backups" class="rb_modal form_inner">';
			_h += '                <div class="rbm_header"><i class="rbm_symbol material-icons">backup</i><span class="rbm_title">Slide Backups</span><i class="rbm_close material-icons">close</i></div>';
			_h += '    			   <div class="rbm_content">';
			_h += '		   		       <div id="rs-backup-wrap">';
			_h += '            		       <div id="rs-backup-inner"><div id="rs-backup-container"></div></div>';
			_h += '        		       </div>';
			_h += '    		       </div>';
			_h += '		       </div>';
			_h += '        </div>';
			_h += '    </div>';
			_h += '</div>';
			
			$('body').append(_h);
			addon.forms.inner = $('#rs-backup-inner');
			addon.forms.container = $('#rs-backup-container');
			
		}
		
		function addEvents() {
			
			RVS.DOC.on('addonbackupsfetch', events.getBackups)
							.on('rs_save_slide_params', events.saveSlide)
							.on('backupsaddonload', events.loadBackup)
							.on('backupsaddonpreview', events.showPreview);
							
			$('body').on('click', '#rbm_addon_backups .rbm_close', events.closeModal)
						  .on('click', '.rs-load-backup', events.loadBackup);
			
		}
		
		function _truefalse(v) {
			if (v==="false" || v===false || v==="off" || v===undefined || v===0 || v===-1 || v==="0")
				v=false;
			else
			if (v==="true" || v===true || v==="on" || v===1 || v==="1")
				v=true;
			return v;
		}
		
		function onInit() {
		
			if(typeof RVS.S.slideId === 'undefined') setTimeout(onInit, 500);
			else RVS.DOC.trigger(slug+'_init');
		
		}
		
		setTimeout(onInit, 500);


})(jQuery);