/**
 * EVOTX draw attendees
 * @version  0.1
 */

(function($){
	// Seat Map Settings
	$.fn.evotxDrawAttendees = function(opt){
		attendees = opt.attendees;
		temp = opt.temp;

		if(temp === undefined) return false;

		template = Handlebars.compile( temp );

		// additions
			Handlebars.registerHelper('ifCond',function(v1,operator, v2, options){
				return checkCondition(v1, operator, v2)
                    ? options.fn(this)
                    : options.inverse(this);
			});
			Handlebars.registerHelper("noDash", function(input) {
			    O = input.replace(/_/g, " ");
			    return O;
			});
			Handlebars.registerHelper("urlE", function(input) {	
				O = input.replace("&amp;",'&');			   
				O = O.replace("&#36;;",'$');			   
			    return decodeURIComponent(O);
			});

		// build
			if(attendees === undefined){
				$('.evotx_lightbox').find('.ajde_popup_text').html( 'No Attendees');
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
			
			$('.evotx_lightbox').find('.ajde_popup_text').html( HTML );

		// build filter
			F = $('.evotx_lightbox').find('.evotx_filter');
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
							_HTML += "<option value='"+D+"'>"+D+"</option>";
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

				// button to see more filters if available
				if(oHTML!= ''){
					HTML += "<span><a class='evo_admin_btn btn_triad toggle_other_filters'>More Filters</a></span>";
				}

				HTML += "</span>";
				HTML += '<span class="other_filters" style="display:none">'+oHTML+"</span>";

				F.html( HTML ).data('j', attendees);

				// when filtes changed
				$('.evotx_lightbox').find('.evotx_filter select').on('change',function(){
					_filter_tickets();
				});

				// toggle other filters
				$('.evotx_lightbox .toggle_other_filters').on('click',function(){
					$(this).closest('.evotx_filter').find('.other_filters').toggle();
				});
			}

		// supportive
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
				F = $('.evotx_lightbox').find('.evotx_filter');
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
							if( O.val() != 'All' && O.val() != td_val){
								skip = true;
							}
						});

						if(!skip) TNS.push(tn);
					});
				});
				
				$('.evotx_lightbox').find('.evotxVA_ticket').each(function(){
					if( $.inArray($(this).data('tn'), TNS)<0){
						$(this).hide();
					}else{
						$(this).show();
					}
				});				
			}
	}

}(jQuery));