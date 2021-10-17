/** 
 * Javascript for subscription management page
 *
 * @version  0.1
 */

jQuery(document).ready(function($){

	//form url
		var url      = window.location.href; 
		$('#evosb_subscriber_page').attr('action',url);

	// click on taxonomies
		$('.evoETT_section').on('click', '.categories span', function(){
			$(this).parent().siblings('.cat_selection').slideToggle('fast');
		});
	// select terms in category tax
		$('.cat_selection').on('change', 'input',function(item){

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
				//obj.find('input:eq(1)').attr('checked',true);
			}else{
				obj.find('input[name=all]').attr('checked',false);
			}

			update_tax_terms( obj , value );
		});

		function update_tax_terms(obj, value){
			
			var ids='';
			var names='';

			if(value=='all'){
				ids = 'all'; names ='All';
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

			obj.siblings('.categories').find('span').attr({'value':ids}).html(names);
			obj.siblings('.categories').find('input').attr({'value':ids});
		}
});