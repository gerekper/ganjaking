/**
 * Javascript: Reviwer Script
 * @version  1.1
 */
jQuery(document).ready(function($){
	
	var submit_open = false;
	var form_msg;
	
	// open reviwer lightbox		
		$("body").on('click','.new_review_btn',function(){				
			OBJ = $(this);
			ROW = OBJ.closest('.evcal_evdata_row');
			var ajaxdataa = {};
				ajaxdataa['action']='evorev_get_form';
				ajaxdataa['eid'] = ROW.attr('data-eid');
				ajaxdataa['ri'] = ROW.attr('data-ri');
				ajaxdataa['lang'] = ROW.attr('data-lang');
				ajaxdataa['username'] = OBJ.data('username');
				ajaxdataa['useremail'] = OBJ.data('useremail');
				ajaxdataa['uid'] = OBJ.data('uid');
				ajaxdataa['eventname'] = OBJ.data('eventname');
				
				$.ajax({
					beforeSend: function(){ loading(OBJ);	},					
					url:	evore_ajax_script.ajaxurl,
					data: 	ajaxdataa,	dataType:'json', type: 	'POST',
					success:function(data){
						//console.log(data);
						if(data.status=='good'){
							$('.evorev_lightbox').find('.evo_lightbox_body').html( data.content );
							$('.evorev_lightbox.evo_lightbox').addClass('show');
							$('body').trigger('evolightbox_show');
						}else{
							// error notice ***
						}
					},complete:function(){ completeloading(OBJ);	}
				});
		});
		// during ajax eventcard loading
			function loading(obj){	
				obj.closest('.evorow').addClass('loading');	
				obj.closest('p.rsvpmanager_event').addClass('loading');	
			}
			function completeloading(obj){
				obj.closest('.evorow').removeClass('loading');
				obj.closest('p.rsvpmanager_event').removeClass('loading');	
			}
	
	// lightbox review form interactions
		// Star rating change
			$('body').on('click', '.star_rating span', function(){
				rating = $(this).data('value');
				$(this).parent().find('span').removeClass('fa far');

				$(this).addClass('fa');
				$(this).prevAll().addClass('fa');
				$(this).nextAll().addClass('far');
				$(this).siblings('input').attr('value',rating);
			});
	
	// NEW review form submissions & update existing
		$('body').on('click', '#submit_review_form', function(){			

			var obj = $(this),
				ajaxdataa = { },
				form = obj.closest('.review_submission_form'),
				formSection = obj.closest('.evore_form_section'),
				error = 0;

			// reset form error messages
				form.siblings('.notification').removeClass('err').hide();
				formSection.removeClass('error');

			// validation
				// run through each review field
					form.find('.input').each(function(index){
						ajaxdataa[ $(this).attr('name') ] = encodeURIComponent( $(this).val() );
						$(this).removeClass('err');

						// check required fields filled
						if( $(this).hasClass('req') && $(this).val()=='' && $(this).is(":visible")){
							error = 1;
							$(this).addClass('err');
						}
					});
				// validate email
					if(error==0){
						var thisemail = form.find('.inputemail');
						var emailVal = thisemail.val();

						if(emailVal!=''){						
							if( !is_email( emailVal )){
								thisemail.addClass('err');
								review_error('err2'); // invalid email address
								error = 2;
							}
						}
					}	

				// validate human
					if(error==0){
						var human = review_validate_human( form.find('input.captcha') );
						if(!human){
							error=3;
							review_error('err6');
						}
					}				

			if(error==0){
				var updates = form.find('.updates input').attr('checked');
					updates = (updates=='checked')? 'yes':'no';

				ajaxdataa['action']='the_ajax_evore';
				ajaxdataa['repeat_interval']= formSection.attr('data-ri');
				ajaxdataa['e_id']= formSection.attr('data-eid');
				ajaxdataa['lang']= formSection.attr('data-lang');
				ajaxdataa['postnonce']= evore_ajax_script.postnonce;
								
				$.ajax({
					beforeSend: function(){	form.addClass('loading');	},
					type: 'POST',
					url:evore_ajax_script.ajaxurl,
					data: ajaxdataa,
					dataType:'json',
					success:function(data){
						//console.log(ajaxdataa);
						if(data.status=='0'){
							form.slideUp('fast');
							
							// success form
								var successform = form.siblings('.review_confirmation');
								successform.show();
								form.hide();

						}else{
							var passedReview = (data.status)? 'err'+data.status:'err7';
							review_error(passedReview);
						}
					},complete:function(){	form.removeClass('loading');	}
				});				
			}else if(error==1){	review_error('err');	}	
		});
	
	// scroll through reviews
		$('body').on('click','.review_list_control span', function(){

			var obj = $(this),
				dir = obj.data('dir'),
				count = obj.parent().data('revs'),
				list = obj.parent().siblings('.review_list'),
				currentitem = list.find('.show'),
				previtem = currentitem.prev(),
				nextitem = currentitem.next();

				list.find('p').removeClass('show');

			if(dir=='next'){
				if(nextitem.length>0){
					nextitem.addClass('show');
				}else{					
					list.find('p').eq(0).addClass('show');
				}				
			}else{
				if(previtem.length>0){
					previtem.addClass('show');
				}else{	
					cnt = ((list.find('p').length)-1);				
					list.find('p').eq( cnt).addClass('show');
				}
			}
		});
	// open additional rating data 
		$('body').on('click','.orating .extra_data',function(){
			$(this).parent().siblings('.rating_data').toggle();
		});

	// Supporting functions
		// show error messages
			function review_error(code, type){
				FORMSECTION = $('body').find('.evore_form_section');

				var classN = (type== undefined || type=='error')? 'err':type;				
				var codes = JSON.parse(FORMSECTION.find('.evore_msg_').html());

				FORMSECTION.find('.notification').addClass(classN).show().find('p').html(codes.codes[code]);
				FORMSECTION.addClass('error');
			}
		// validate humans
			function review_validate_human(field){
				if(field==undefined){
					return true;
				}else{
					var numbers = ['11', '3', '6', '3', '8'];
					if(numbers[field.attr('data-cal')] == field.val() ){
						return true;
					}else{ return false;}
				}				
			}

		function is_email(email){
			var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	  		return regex.test(email);
		}
});