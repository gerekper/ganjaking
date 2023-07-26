/**
 * EVOTX draw attendees
 * @version  2.1.1
 */

(function($){
	// Seat Map Settings
	$.fn.evotxDrawAttendees = function(opt){
		attendees = opt.attendees;
		temp = opt.temp;

		if(temp === undefined) return false;

		template = Handlebars.compile( temp );

		LB = $('body').find('.evo_lightbox.config_evotx_viewattend');
		if( LB.length == 0){
			$('body').trigger('evo_lightbox_trigger',[{
				't':'View Attendees',
				'uid':'evotx_view_attendees',
				'lbc':'config_evotx_viewattend',
				'lb_padding':'evopad0'
			}]);
		}

		// additions
			Handlebars.registerHelper('ifCond',function(v1,operator, v2, options){
				return checkCondition(v1, operator, v2)
                    ? options.fn(this)
                    : options.inverse(this);
			});
			Handlebars.registerHelper("noDash", function(input) {
				if( input =='' || input === null || input === undefined ) return;
			    O = input.replace(/_/g, " ");
			    return O;
			});
			Handlebars.registerHelper("urlE", function(input) {	
				if( input =='' || input === null || input === undefined ) return;
				O = input.replace("&amp;",'&');			   
				O = O.replace("&#36;;",'$');			   
			    return decodeURIComponent(O);
			});

		// build
			if(attendees === undefined){
				LB.evo_lightbox_populate_content({content: 'No Attendees' });
			}else{	
				// pass can checkable value
				Handlebars.registerHelper("gC", function() {	
					return attendees.od_gc == true? true: false;	
				});
				Handlebars.registerHelper("gCC", function() {	
					return attendees.od_gc == true? 'checkable': '';	
				});
				HTML = template( attendees );				
			}
			
			LB.evo_lightbox_populate_content({content: HTML });

			// attendee count
			LB.find('.evolb_title').append("<span class='evotx_attendee_count'></span>");
			update_attendee_count();

		// build filter
			F = LB.find('.evotx_filter');
			fil = {};

			if(attendees !== undefined){
				i =0;
				$.each(attendees, function(t, tixs){
					if( t != 'tickets') return true;
					fil['oS'] = ['All'];
					fil['s'] = ['All'];

					// each ticket
					$.each(tixs, function(tn, td){

						if( $.inArray(td.oS, fil.oS)<0) fil.oS.push(td.oS);
						if( $.inArray(td.s, fil.s)<0) fil.s.push(td.s);

						$.each(td.oD, function(odn, odv){

							if( odn == 'ordered_date') return true;
							if( odn == 'email') return true;

							// create if not exists
							if( __hasVal( fil, odn)){
								if( $.inArray(odv, fil[odn])<0) fil[odn].push(odv);
							}else{
								fil[odn] = ['All'];	
								if( odn != 'eT') fil[odn]['display']='none';							
								fil[odn].push(odv);
								fil[odn]['oD']=true;
							}
						});
					});
				});

				NAMES = {
					'eT':'event_time',
					's':'checking_status',
					'oS':'order_status'
				};


				HTML = '';
				//HTML = '<span class="evotx_search"><input type="text"/><i class="fa fa-search"></i></span>'
				HTML += '<span class="main_filters">';
				oHTML = "";
				$.each(fil, function(K, V){
					_HTML = '';
					_HTML += "<span class='"+K+" "+ (V.display == 'none'? 'other':'') +"' ><select name='"+K+"' data-oD='"+ (V.oD?true:false ) +"' data-f='"+K+"'>";

					// event time
					if( K == 'event_time' && 'filter_vals' in opt && 'event_time' in opt.filter_vals){

						_HTML += "<option value='"+ V[0] +"'>"+V[0]+"</option>";

						$.each( opt.filter_vals.event_time, function(ff,vv){
							_HTML += "<option value='"+ff+"'>"+ff+"</option>";
						});	

					}else{
						$.each( V, function(ind,D){
							if(ind == 'display') return true;
							if(ind == 'oD') return true;
							_HTML += "<option value='"+  D  +"' x>"+ decodehtml( D ) +"</option>";
						});
					}
					
					K = __hasVal(NAMES, K)? NAMES[K]: K;
					K = K.replace("_",' ');
					_HTML += "</select><em>"+K+"</em></span>";

					// for other filters
					if( V.display == 'none'){
						oHTML += _HTML;
					}else{
						HTML += _HTML;
					}

				});
				
				HTML += "<span>";
				// button to see more filters if available
				if(oHTML!= ''){
					HTML += "<a class='evo_admin_btn toggle_other_filters evomarr10'><i class='fa fa-sliders evomarr10'></i> "+ evotx_admin_ajax_script.text.t2 +"</a>";
				}
				HTML += "<a class='evo_admin_btn toggle_search'><i class='fa fa-search'></i></a>";
				HTML += "</span>";

				HTML += "</span>";
				HTML += '<span class="other_filters evohidden" style="">'+oHTML+"</span>";
				HTML += '<span class="evotx_admin_search_bar evomart10 evohidden" style=""><input class="evotx_admin_search_input" type="text" name="s" placeholder="'+ evotx_admin_ajax_script.text.t1 +'"/></span>';

				F.html( HTML ).data('j', attendees);

				// when filtes changed
				LB.find('.evotx_filter select').on('change',function(){
					_filter_tickets();
				});

				// toggle other filters
				LB.
				on('click','.toggle_other_filters', function(){
					$(this).closest('.evotx_filter').find('.other_filters').toggleClass('evohidden');
				})
				// toggle search bar
				.on('click','.toggle_search', function(){
					$(this).closest('.evotx_filter').find('.evotx_admin_search_bar').toggleClass('evohidden');
				});

				// search tickets on key press
				$('body').on('keypress', '.evotx_admin_search_input',function(event){
					var keycode = (event.keyCode ? event.keyCode : event.which);
					if( keycode == '13' ){
						_search_tickets();
					}
				});
			}

		// supportive
			function _search_tickets(){				
				J = F.data('j');
				TNS = [];
				var search_val = LB.find('.evotx_admin_search_input').val();

				$.each(J, function(tickets, tixx){
					if(tickets != 'tickets') return true;
					$.each(tixx, function(tn, td){
						// if ticket number match
						if( tn.indexOf( search_val ) !== -1) TNS.push(tn);

						// attendee name match
						name = td.name;
						if( name.indexOf( search_val ) !== -1 ) TNS.push(tn);

						// email match
						email = td.email;
						if( email.indexOf( search_val ) !== -1 ) TNS.push(tn);
					});
				});

				// show matching attendees
				LB.find('.evotxVA_ticket').each(function(){
					if( $.inArray($(this).data('tn'), TNS)<0){
						$(this).hide();
					}else{	$(this).show();	}
				});
				update_attendee_count()
			}

			// @since 2.2
			function update_attendee_count(){
				LB.find('.evotx_attendee_count').html( LB.find('.evotxVA_ticket:visible').length );
			}
			function checkCondition(v1, operator, v2) {
		        switch(operator) {
		            case '==':
		                return (v1 == v2);
		            case '===':
		                return (v1 === v2);
		            case '!==':
		                return (v1 !== v2);
		            case '<':
		                return (v1 < v2);
		            case '<=':
		                return (v1 <= v2);
		            case '>':
		                return (v1 > v2);
		            case '>=':
		                return (v1 >= v2);
		            case '&&':
		                return (v1 && v2);
		            case '||':
		                return (v1 || v2);
		            default:
		                return false;
		        }
		    }
		    function __hasVal(obj, key){
		        return obj.hasOwnProperty(key);
		    }

		    // on filter, filter attendees
		    function _filter_tickets(){
				F = LB.find('.evotx_filter');
				J = F.data('j');


				TNS = [];

				$.each(J, function(tickets, tixx){
					if(tickets != 'tickets') return true;
					$.each(tixx, function(tn, td){

						skip = false;
						// run through each filter
						F.find('select').each(function(){
							O = $(this);

							if( O.val() == 'All') return true;


							oD = O.data('od');
							field = O.data('f');
							td_val = oD? td.oD[field] : td[field];

							td_val = decodehtml( td_val);

							//console.log(O.val() +' '+td_val);

							if( O.val() != 'All' && O.val() != td_val){
								skip = true;
							}
							

						});

						if(!skip) TNS.push(tn);
						
					});
				});
				
				LB.find('.evotxVA_ticket').each(function(){
					if( $.inArray($(this).data('tn'), TNS)<0){
						$(this).hide();
					}else{
						$(this).show();
					}
				});	
				update_attendee_count();			
			}

			// decode html
			function decodehtml(text){
				var html = typeof text ==='string' ? text.replace(/&nbsp;/g, ' ') : text;
				return html;
			}
	}

}(jQuery));