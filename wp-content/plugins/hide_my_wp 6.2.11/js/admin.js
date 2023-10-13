/* Copy right 2013 by Hassan Jahangiri (wpwave.com) */

jQuery(document).ready(function($) {
	
	$('input,textarea').change(function(e){
		var page_base=$('#page_base');
		var is_page_base =(page_base.length && page_base.val().length && page_base.val()!=' ' && page_base.val()!='/') ? true : false;
		var author_without_base=$('#author_without_base');
		var is_author_without_base= (author_without_base.is(':checked')) ? true : false;
		var disable_submit=false;

		//fix a little problem caused by order of conditions check
		var page_base_error=false;

		if ($('#page_enable').val()==1 && !is_page_base && is_author_without_base){
			alert('If you enable author without base you should enter something as \'Page Base\'!' );
			page_base.css('border-color','red');
			page_base_error=true;
			disable_submit=true;
		}else if (!page_base_error){
			page_base.css('border-color','#DFDFDF');
		}

		if (page_base.length && $('#paginate_enable').length  && $('#page_enable').val()==1 && $('#paginate_enable').val()==1 && page_base.val() && page_base.val().replace('/','').replace('/','') == $('#paginate_base').val().replace('/','').replace('/','') ){
			alert('\'Page Base\' and \'Paginate Base\' should be different!' );
			$('#paginate_base').css('border-color','red');
			page_base.css('border-color','red');
			page_base_error=true;
			disable_submit=true;
		}else{
			$('#paginate_base').css('border-color','#DFDFDF');
			if (!page_base_error)
				page_base.css('border-color','#DFDFDF');
		}

		if ($('#post_enable').val()==1 && $('#post_base').length && ($('#post_base').val().replace('/','').replace('/','')=='%postname%' || $('#post_base').val().replace('/','').replace('/','')=='%post_id%') && is_author_without_base){
			alert('If you enable author without base you can not use \'%postname%\' or \'%post_id%\' as post base.\nInstead combine them or add something before or after.  e.g. story/%postname%' );
			$('#post_base').css('border-color','red');
			disable_submit=true;
		}else{
			$('#post_base').css('border-color','#DFDFDF');	
		}


		if ($('#paginate_query').length && $('#paginate_enable').val()==1 && $('#paginate_query').val()=='page' ){
			alert('\'Page Query\' should not be \'page\'!' );
			$('#paginate_query').css('border-color','red');
			disable_submit=true;
		}else{
			$('#paginate_query').css('border-color','#DFDFDF');	
		}

		if ($('#page_enable').val()<1 && $('#custom_404_1').is(':checked')){
			alert('You can\'t disable page URL and use custom 404 in the same time!');
			disable_submit=true;
			$('#page_enable').css('border-color','red');
		}else{
			$('#page_enable').css('border-color','#DFDFDF');
		}




		if (disable_submit){
			$('#submit').attr('disabled','disabled');
		}else{
			$('#submit').removeAttr('disabled');
			//There is no error fix all!
		}

		
	});

	$('#submit').click(function(){
		if ($('#import_field').length && $('#import_field').val().length>5)
			alert("Your login address may change after importing new settings.\n Check out 'Hiding' tab for new address.");
        var invalid = false;

        $('.replace_urls .second_field').each(function(i, e){
            if (!$(this).val()) {
                alert("A field for Replace URL is empty! ")
                $(this).css('border-color', 'red');
                invalid=true;
            }
        });

       /* $('.replace_in_html .first_field, .replace_in_html .second_field').each(function(i, e){
            if ($(this).val()) {
                $(this).val().replace("/\\/","[bslash]");
                //$(this).css('border-color', 'red');
                //invalid=true;
                alert('bslash');
            }
        });*/

       if (invalid)
            return false;
	});

	$('#submit[disabled="disabled"]').click(function(){
		alert('Please fix errors before save!');
	});
    var rand='';
    var maxField = 25; //Input fields increment limitation
    var maxField2 = 25; //Input fields increment limitation
    var addButton = $('.htmwp_a25dd_button'); //Add button selector
    var wrapper = $('.field_wrapper'); //Input field wrapper

    var x = 1; //Initial field counter is 1

    function fieldHTML_maker(name, rand) {

		if (name == 'replace_in_html')
			var remove_label = 'Remove';
		else
			var remove_label = 'Hide (404)';

		var output = '<div class="hmwp_field_row">' +
				'<textarea  placeholder="Old" name="' + name + '1[]" class="first_field"></textarea>' +
				'<div class="action_field"><label>';

		if (name == 'replace_in_html') {
			output += '<input type="radio" checked="checked" value="replace" name="html_actiontype_' + rand + '" class="radio" checked="checked">Replace</label> <br>';
			output += '<label><input type="radio" value="remove" name="html_actiontype_' + rand + '" class="radio">Remove</label>';
		} else {
			output += '<input type="radio" checked="checked" value="replace" name="urls_actiontype_' + rand + '" class="radio" checked="checked">Replace</label> <br>';
			output += '<label><input type="radio" value="remove" name="urls_actiontype_' + rand + '" class="radio">Hide (404)</label>';
		}
		output += '</div><textarea placeholder="New" name="' + name + '2[]" class="second_field"></textarea>' +
				'<a href="javascript:void(0);" class="button hmwp_action hmwp_remove_button" title="Remove Rule"><img src="../wp-content/plugins/hide_my_wp/img/delete.png" width="12"/></a>' +
				'</div><div class="clear"></div>';
		return output;
	}

    $('.replace_in_html .htmwp_add_button').click(function(){ //Once add button is clicked
        if(x < maxField){ //Check maximum number of input fields
            x++; //Increment field counter
            rand=Math.random().toString(36).substring(5);
            $(this).before(fieldHTML_maker('replace_in_html', rand)); // Add field html
        }
    });

    $('.replace_urls .htmwp_add_button').click(function(){ //Once add button is clicked
        if(x < maxField2){ //Check maximum number of input fields
            x++; //Increment field counter
            rand=Math.random().toString(36).substring(5);
            $(this).before(fieldHTML_maker('replace_urls', rand)); // Add field html
        }
    });

    $(wrapper).on('change', 'input[name^=html_actiontype]', function(e) {

        if(this.value=='remove') {
            $(this).parent().parent().next().val('');
            $(this).parent().parent().next().css('visibility', 'hidden');
        }else {
            $(this).parent().parent().next().css('visibility', 'visible');
        }
    });

    $(wrapper).on('change', 'input[name^=urls_actiontype]', function(e) {

        if(this.value=='remove') {
            $(this).parent().parent().next().val('nothing_404_404');
            $(this).parent().parent().next().css('visibility', 'hidden');

        }else {
            $(this).parent().parent().next().css('visibility', 'visible');
            $(this).parent().parent().next().val('');


        }
    });



    // $('.just_delete').parent().find('.second_field').show();


    $(wrapper).on('click', '.hmwp_remove_button', function(e){ //Once remove button is clicked
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });

	
});
				