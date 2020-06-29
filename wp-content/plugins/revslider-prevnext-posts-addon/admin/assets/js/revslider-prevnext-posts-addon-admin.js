/***************************************************
 * REVOLUTION 6.0.0 prevnext-posts ADDON
 * @version: 2.0 (15.07.2018)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';
		
		
		
		var bricks = revslider_prevnext_posts_addon.bricks;
		var addon = {};
		
		// ADDON CORE
		var slug = "revslider-prevnext-posts-addon";

		//CHECK GLOBAL ADDONS VARIABLE		
		RVS.LIB.ADDONS = RVS.LIB.ADDONS === undefined ? {} : RVS.LIB.ADDONS;
		RVS.LIB.ADDONS[slug] = RVS.LIB.ADDONS[slug]===undefined ? {} : RVS.LIB.ADDONS[slug];			
		RVS.LIB.ADDONS[slug].enable = _truefalse(revslider_prevnext_posts_addon.enabled);

		// INITIALISE THE ADDON	
		RVS.DOC.on('extendmetas.prevnext-posts',function() {									
			// FIRST TIME INITIALISED
			if (!addon.meta_extended) {
				updateMetas();
				addon.meta_extended = true;
			}							
		});

		// INITIALISE THE ADDON	CONFIG PANEL (init_%SLUG%_ConfigPanel)
		RVS.DOC.on(slug+'_config',function(e,param) {		
			// FIRST TIME INITIALISED
			if (!addon.initialised) {
				initListeners();
				RVS.F.getCustomPostTypes(function() {						
					addon.configpanel = $(buildConfigPanel());
					addon.initialised = true;				
					$("#"+param.container).append(addon.configpanel);			
					//AJAX TO LOAD CONTENT
					RVS.F.ajaxRequest("wp_ajax_get_values_"+slug, {}, function(response){						
						if (response.data) 
							setContent($.parseJSON(response.data));							
						else
							setContent();	
						RVS.F.updateSelectsWithSpecialOptions();
						// extendDefaultOptions();					
					},undefined,undefined,RVS_LANG.loadconfig+'<br><span style="font-size:17px; line-height:25px;">"'+bricks.loadvalues+'"</span>');
				});					
				
			} else {
				$("#"+param.container).append(addon.configpanel);
			}
			
			//Update Save Config Button
			RVS.F.configPanelSaveButton({show:true, slug:slug});

			if (addon.initialised) updateInputFieldDependencies();
		});

		//Add "Do not add a slider" option on first Place
		/*
		function extendDefaultOptions() {
			addon.configpanel.find('.prevnextsliderlist').each(function() {
				RVS.F.addOrSelectOption({select:$(this), val:_[slug+'-slider'], selected:false}); // commenting out because "_" was undefined	
			});
		}
		*/

		function updateInputFieldDependencies() {

			RVS.F.initOnOff(addon.configpanel);
			addon.configpanel.find('.tos2.nosearchbox').select2({
				minimumResultsForSearch:"Infinity",
				placeholder:"Select From List"
			});				
		}

		function setContent(_) {	
			
			_ = _ === undefined ? {} : _;
			// Update Old Values
			for (var i in _) {
				if(!_.hasOwnProperty(i)) continue;
				if (i.indexOf("rs-addon-prevnext-")>=0) {
					_[i.replace("rs-addon-prevnext","revslider-prevnext-posts-addon")] = _[i];
				}
			}
		
			// var form = $('#'+slug+'-form');

			//Check All Available Post Type Settings
			for (i in RVS.LIB.POST_TYPES) {
				if(!RVS.LIB.POST_TYPES.hasOwnProperty(i)) continue;
				$('#prevnext_taxonomyonly_'+RVS.LIB.POST_TYPES[i].slug)[0].checked = _truefalse(_[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-taxonomy-only']) ? "checked" : "";
				$('#prevnext_taxonomyonly_'+RVS.LIB.POST_TYPES[i].slug).trigger("change");

				RVS.F.addOrSelectOption({select:$('#prevnext_slider_'+RVS.LIB.POST_TYPES[i].slug),val:(_[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-slider']===undefined ? "none" : _[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-slider'])});
				$('#prevnext_slider_'+RVS.LIB.POST_TYPES[i].slug).trigger("change");

				RVS.F.addOrSelectOption({select:$('#prevnext_position_'+RVS.LIB.POST_TYPES[i].slug),val:(_[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-position']===undefined ? "top" : _[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-position'])});
				$('#prevnext_position_'+RVS.LIB.POST_TYPES[i].slug).trigger("change");

				RVS.F.addOrSelectOption({select:$('#prevnext_taxonomy_'+RVS.LIB.POST_TYPES[i].slug),val:(_[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-taxonomy']===undefined ? "none" : _[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-taxonomy'])});
				$('#prevnext_taxonomy_'+RVS.LIB.POST_TYPES[i].slug).trigger("change");


			}
			
			updateInputFieldDependencies();
		}


		// INITIALISE weather LISTENERS
		function initListeners() {		
			RVS.DOC.on('save_'+slug,function() {				
				RVS.F.ajaxRequest("wp_ajax_save_values_"+slug, {revslider_prevnext_posts_form: $('#revslider-prevnext-posts-addon-form').serialize()}, function(response){

				},undefined,undefined,RVS_LANG.saveconfig+'<br><span style="font-size:17px; line-height:25px;">"'+bricks.savevalues+'"</span>');										
			}); // End Click		
		}


		function buildConfigPanel() {				
			var _h;				
			
			_h += '<form id="'+slug+'-form">';
			for (var i in RVS.LIB.POST_TYPES) {
				if(!RVS.LIB.POST_TYPES.hasOwnProperty(i)) continue;
				_h +=  '<div class="ale_i_title">'+RVS.LIB.POST_TYPES[i].title+'</div>';					
				if (RVS.LIB.POST_TYPES[i].slug==="post"){
					_h += '<label_a>'+bricks.category+'</label_a><input id="prevnext_taxonomyonly_'+RVS.LIB.POST_TYPES[i].slug+'" type="checkbox" name="'+slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-taxonomy-only" class="basicinput">';
					_h += '<row class="direktrow"><labelhalf><i class="material-icons">sms_failed</i></labelhalf><contenthalf><div class="function_info">'+bricks.infocategory+'</div></contenthalf></row>';
					_h += '<row class="direktrow"><labelhalf><i class="material-icons">sms_failed</i></labelhalf><contenthalf><div class="function_info">'+bricks.infoslidertype+'</div></contenthalf></row>';
					_h += '<div class="div15"></div>';
				} else {
					_h += '<label_a>'+bricks.taxonomyonly+'</label_a><input id="prevnext_taxonomyonly_'+RVS.LIB.POST_TYPES[i].slug+'" type="checkbox" name="'+slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-taxonomy-only" class="basicinput" data-showhide=".prevnext_'+RVS.LIB.POST_TYPES[i].slug+'_taxonomies" data-showhidedep="true">';
					_h += '<row class="direktrow"><labelhalf><i class="material-icons">sms_failed</i></labelhalf><contenthalf><div class="function_info">'+bricks.infotaxonomy+'</div></contenthalf></row>';
					_h += '<row class="direktrow"><labelhalf><i class="material-icons">sms_failed</i></labelhalf><contenthalf><div class="function_info">'+bricks.infoslidertype+'</div></contenthalf></row>';
					_h += '<div class="div15"></div>';
					_h += '<div class="prevnext_'+RVS.LIB.POST_TYPES[i].slug+'_taxonomies">';
					_h += '<label_a>'+bricks.taxonomy+'</label_a><select id="prevnext_taxonomy_'+RVS.LIB.POST_TYPES[i].slug+'" name="'+slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-taxonomy" data-theme="inmodal" class="basicinput tos2 nosearchbox">';
					_h += ' <option value="none">'+bricks.none+'</option>';
					for (var j in RVS.LIB.POST_TYPES[i].tax) {
						if(!RVS.LIB.POST_TYPES[i].tax.hasOwnProperty(j)) continue;
						_h += ' <option value="'+j+'">'+RVS.LIB.POST_TYPES[i].tax[j]+'</option>';
					}
					_h += '</select><span class="line-break"></span>';
					_h += '</div>';
				}

				_h += '<label_a>'+bricks.slider+'</label_a><select id="prevnext_slider_'+RVS.LIB.POST_TYPES[i].slug+'" name="'+slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-slider" data-theme="inmodal" class="basicinput tos2 nosearchbox prevnextsliderlist select_of_customlist" data-ctype="sliders" data-valuetype="slug" data-filter="posts" data-subfilter="current_post"></select><span class="line-break"></span>';
				_h += '<label_a>'+bricks.position+'</label_a><select id="prevnext_position_'+RVS.LIB.POST_TYPES[i].slug+'" name="'+slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-position" data-theme="inmodal" class="basicinput tos2 nosearchbox"><option value="top">'+bricks.above+'</option><option value="bottom">'+bricks.below+'</option></select>';
				_h += '<div class="div25"></div>';
			}				
			_h += '</form>';
			_h += '	<div class="div75"></div>';
			return _h;
		}

		function _truefalse(v) {
			if (v==="false" || v===false || v==="off" || v===undefined || v===0 || v===-1 || v==="0")
				v=false;
			else
			if (v==="true" || v===true || v==="on")
				v=true;
			return v;
		}

		//UPDATE META DATAS
		function updateMetas() {
			var _h,
				prev = [
						["{{prev_title}}","prev_title","Prev Title"],
						["{{prev_excerpt}}","prev_excerpt","Prev Excerpt"],
						["{{prev_content}}","prev_content","Lorem Ipsum Content"],
						["{{prev_content:words:10}}","prev_content_words","Lorem Ipsum Content"],
						["{{prev_content:chars:10}}","prev_content_chars","Lorem Ipsum Content"],
						["{{prev_link}}","prev_link","http://www.prevlink.com"],
						["{{prev_date}}","prev_date","15.05.2020"],
						["{{prev_date_modified}}","prev_date_modified","13.07.2020"],
						["{{prev_author_name}}","prev_author_name","John Doe"],
						["{{prev_num_comments}}","prev_num_comments","18"],
						["{{prev_catlist}}","prev_catlist","Cat1, Cat2, Cat3"],
						["{{prev_catlist_raw}}","prev_catlist_raw","Cat1 Cat2 Cat3"],
						["{{prev_taglist}}","prev_taglist","Tag1, Tag2, Tag3"],
						["{{prev_id}}","prev_id","previousID"]],

				next = [["{{next_title}}","next_title","Next Title"],
						["{{next_excerpt}}","next_excerpt","Next Excerpt"],
						["{{next_content}}","next_content","Lorem Ipsum Content"],
						["{{next_content:words:10}}","next_content_words","Lorem Ipsum Content"],
						["{{next_content:chars:10}}","next_content_chars","Lorem Ipsum Content"],
						["{{next_link}}","next_link","http://www.nextlink.com"],
						["{{next_date}}","next_date","15.05.2020"],
						["{{next_date_modified}}","next_date_modified","13.07.2020"],
						["{{next_author_name}}","next_author_name","John Doe"],
						["{{next_num_comments}}","next_num_comments","18"],
						["{{next_catlist}}","next_catlist","Cat1, Cat2, Cat3"],
						["{{next_catlist_raw}}","next_catlist_raw","Cat1 Cat2 Cat3"],
						["{{next_taglist}}","next_taglist","Tag1, Tag2, Tag3"],
						["{{next_id}}","next_id","NextID"]];
											
			_h = '<div class="mdl_group">';
			_h += '<div class="mdl_group_header"><i class="material-icons">description</i>'+bricks.prevpost+'<i class="material-icons accordiondrop">arrow_drop_down</i></div>';
			for (var i in prev) {
				if(!prev.hasOwnProperty(i)) continue;
				_h += '<div data-val="'+prev[i][0]+'" class="mdl_group_member"><div class="mdl_left_content"><i class="material-icons">description</i>'+bricks[prev[i][1]]+'</div><div class="mdl_right_content">'+prev[i][0]+'</div><div class="mdl_placeholder_content">'+prev[i][2]+'</div></div>';
			}								
			_h += '</div>';								
			$('#mdl_group_post').append($(_h));


			_h = '<div class="mdl_group">';
			_h += '<div class="mdl_group_header"><i class="material-icons">description</i>'+bricks.nextpost+'<i class="material-icons accordiondrop">arrow_drop_down</i></div>';
			for (i in next) {
				if(!next.hasOwnProperty(i)) continue;
				_h += '<div data-val="'+next[i][0]+'" class="mdl_group_member"><div class="mdl_left_content"><i class="material-icons">description</i>'+bricks[next[i][1]]+'</div><div class="mdl_right_content">'+next[i][0]+'</div><div class="mdl_placeholder_content">'+next[i][2]+'</div></div>';
			}
			_h += '</div>';								
			$('#mdl_group_post').append($(_h));


			//Extend Image URLS		
			_h = '<div class="mdl_group">';
			_h += '<div class="mdl_group_header"><i class="material-icons">photo</i>'+bricks.prevpost+'<i class="material-icons accordiondrop">arrow_drop_down</i></div>';						
			for (var i in RVS.ENV.img_sizes) {
				if(!RVS.ENV.img_sizes.hasOwnProperty(i)) continue;
				var v = RVS.ENV.img_sizes[i].replace(" ","_").toLowerCase();
				_h += '<div data-val="{{prev_image_'+v+'_url}}" class="mdl_group_member"><div class="mdl_left_content"><i class="material-icons">photo</i>{{prev_image_'+v+'_url}}</div><div class="mdl_right_content">'+v+'</div><div class="mdl_placeholder_content">http://imagesource.img</div></div>';
			}				
			_h += '</div>';								
			$('#mdl_group_images').append($(_h));								
			

			_h = '<div class="mdl_group">';
			_h += '<div class="mdl_group_header"><i class="material-icons">photo</i>'+bricks.nextpost+'<i class="material-icons accordiondrop">arrow_drop_down</i></div>';
			for (i in RVS.ENV.img_sizes) {
				if(!RVS.ENV.img_sizes.hasOwnProperty(i)) continue;
				var v = RVS.ENV.img_sizes[i].replace(" ","_").toLowerCase();
				_h += '<div data-val="{{next_image_'+v+'_url}}" class="mdl_group_member"><div class="mdl_left_content"><i class="material-icons">photo</i>{{next_image_'+v+'_url}}</div><div class="mdl_right_content">'+v+'</div><div class="mdl_placeholder_content">http://imagesource.img</div></div>';
			}
			_h += '</div>';								
			$('#mdl_group_images').append($(_h));

			

			RVS.F.updateMetaTranslate();
		}


})( jQuery );