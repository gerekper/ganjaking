/** 
 * Javascript for subscriber section
 * @version  0.2
 */

jQuery(document).ready(function($){
	// open the subscription form
		$('body').on('click','.evosub_subscriber_btn',function(e){
			
			e.preventDefault;

			$('#evoSUB_bg').fadeIn();
			$('#evoSUB_form').show().animate({
				'top':'0px',
				'opacity':1
			}).fadeIn();
			//$('html, body').animate({scrollTop:0}, 700);
		});
	
	// close subscriber popup
		$('body').on('click','#evoSUB_close', function(){
			$('#evoSUB_form').animate({
				'top':'50px',
				'opacity':0
			}).fadeOut();
			$('#evoSUB_bg').fadeOut();
			//$('#evoSUB_bg').find('.form').attr('class','form');
		});

	// click on taxonomies
		$('#evoSUB_form').on('click', '.categories span', function(){
			$(this).parent().siblings('.cat_selection').slideToggle('fast');
		});

	// select terms in category tax
		$('#evoSUB_form .cat_selection').on('change', 'input',function(item){

			_this = $(this);
			is_checked = (_this.is(':checked'))? true:false;
			clicked_item = _this.attr('data-id');

			value = (_this.is(':checked'))? _this.attr('data-id'):'';
			var obj = _this.closest('.cat_selection');

			// fix for all and not all
			if(clicked_item=='all' && is_checked){
				obj.find('input').attr('checked',true);				
			}else if(clicked_item=='all' && ! is_checked){
				obj.find('input').attr('checked',false);
				obj.find('input:eq(1)').attr('checked',true);
			}else{
				obj.find('input[data-id=all]').attr('checked',false);
			}
			update_tax_terms( obj , value );
		});

		function update_tax_terms(obj, value){			
			var ids='';
			var names='';

			if(value=='all'){
				ids = 'all'; 
				names =$('#form_text').data('all');
			}else{
				obj.find('input:checked').each(function(){
					ids += $(this).attr('data-id')+',';
					names += ( names!=''? ', ':'') + $(this).attr('data-name');
				});	

				// if nothing is selected
				if(names==''){
					ids = 'none'; names ='None';
				}
			}
			obj.siblings('.categories').find('input.field').attr({'value':ids});
			obj.siblings('.categories').find('span').html(names);
		}

	// submit subscription form
		$('body').on('click','#evosub_submit_button',function(){
			var data_arg = {};
			var error = 0,
				mainform = $(this).closest('#evoSUB_form'),
				outterform = mainform.find('.form'),
				form = mainform.find('.evosub_form');

			//reset 
			form.find('.evosub_msg').fadeOut();
			outterform.attr('class','form');

			// check if required fields have values submitted
			form.find('.field').each(function(){
				value = $(this).val();
				
				if(typeof value !== undefined && value != ''){
					// for email 
					if($(this).attr('data-name') == 'email' && !is_email(value)){
						error = 2;
					}else{
						data_arg[ $(this).attr('data-name')] = encodeURIComponent(value);	
					}							
				}else{
					error = 1;
				}
			});
			
			data_arg['lang']=form.find('.evo_lang').val();

			if(error== 0){
				data_arg['action']='evosub_new_subscriber';
				$.ajax({
					beforeSend: function(){						
						mainform.find('.formIn').addClass('loading');
					},
					type: 'POST',
					url:evosub_ajax_script.ajaxurl,
					data: data_arg,
					dataType:'json',
					success:function(data){
						if(data.status=='good'){ // success
							mainform.find('.formIn').slideUp();
							mainform.find('.formMsg').slideDown();
							outterform.addClass('success');
						}else{ // bad
							msg_obj = form.find('.evosub_msg');
							msg_obj.html( msg_obj.data(data.msg)).fadeIn();
							outterform.addClass('error');
						}
															
					},complete:function(){
						mainform.find('.formIn').removeClass('loading');
					}	
				});
			}else{
				msg_obj = form.find('.evosub_msg');
				msg = (error==1)? msg_obj.data('str1'):msg_obj.data('str2');
				msg_obj.html(msg).fadeIn();

				outterform.addClass('error');
			}
		});
	

	// verify email address
		function is_email(email){
			var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	  		return regex.test(email);
		}
});