/* Copyright (C) SeedProd LLC - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and redistribution is prohibitted.
 * Use by license purchased only at http://www.seedprod.com.
 * Written by John Turner <john@seedprod.com>, 2016
 */

jQuery(document).ready(function() {

    // Get HTML

    jQuery('#get_html').on('click',function(e){
        e.preventDefault();
        try{
        var jQuerybtn = jQuery(this).button('loading');
        }catch(err) {}
        var jqxhr = jQuery.get( get_html_url+'&page_id='+page_id, function(data) {
              jQuery('#html').val(data);
            })
            .always(function() {
              try{
              jQuerybtn.button('reset');
              }catch(err) {}
            })
    });

    // Save HTML
    jQuery('#save_html').on('click',function(e){
        e.preventDefault();
        var data = jQuery('#html').val();

        try{
        var jQuerybtn = jQuery(this).button('loading');
        }catch(err) {}
        var jqxhr = jQuery.post( save_html_url+'&page_id='+page_id,data, function(data) {
              if(data != 'false'){
                // reload page
                timeout = true;
                location.reload();
              }else{
                toastr.error('HTML could not be saved.');
              }
            })
            .always(function() {
              try{
              jQuerybtn.button('reset');
              }catch(err) {}
            });

    });
    
    

    // Export

    jQuery('#export-settings').on('click',function(e){
        e.preventDefault();
        try{
        var jQuerybtn = jQuery(this).button('loading');
        }catch(err) {}
        var jqxhr = jQuery.get( export_page_ajax_url+'&page_id='+page_id, function(data) {
              jQuery('#import_export_settings').val(data);
            })
            .always(function() {
              try{
              jQuerybtn.button('reset');
              }catch(err) {}
            })
    });

    // Import
    jQuery('#import-settings').on('click',function(e){
        e.preventDefault();
        var data = jQuery('#import_export_settings').val();
        if(data != ''){
        try{
        var jQuerybtn = jQuery(this).button('loading');
        }catch(err) {}
        var jqxhr = jQuery.post( import_page_ajax_url+'&page_id='+page_id,data, function(data) {
              if(data != 'false'){
                // reload page
                timeout = true;
                location.reload();
              }else{
                toastr.error('Settings could not be imported.');
              }
            })
            .always(function() {
              try{
              jQuerybtn.button('reset');
              }catch(err) {}
            });
        }
    });

    // Publish Button
    jQuery('#publish-btn').on('click',function(e){
        save_page();
        //save_html();
    });
   // jQuery('#quick_nav').select2();
    jQuery('#quick_nav').change(function() {

        if(jQuery(this).val() == ''){
            jQuery("#collapse-design-settings").collapse('hide');
            jQuery("#collapse-ontent-settings").collapse('hide');
            jQuery("#collapse-language-settings").collapse('hide');
            jQuery("#collapse-advanced-settings").collapse('hide');
            jQuery("#collapse-publish-settings").collapse('hide');
        }
            
  	    if(jQuery(this).val() == 'content'){
  	            jQuery("#collapse-language-settings").collapse('hide');
                jQuery("#collapse-advanced-settings").collapse('hide');
                jQuery("#collapse-publish-settings").collapse('hide');
                jQuery("#collapse-design-settings").collapse('hide');
	            jQuery("#collapse-ontent-settings").collapse('show');
	            setTimeout(function(){ jQuery("#collapse-ontent-settings").collapse('show'); location.href = "#header-content-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    }  
	    if(jQuery(this).val() == 'form'){
	          	jQuery("#collapse-language-settings").collapse('hide');
                jQuery("#collapse-advanced-settings").collapse('hide');
                jQuery("#collapse-publish-settings").collapse('hide');
                jQuery("#collapse-design-settings").collapse('hide');
	            jQuery("#collapse-ontent-settings").collapse('show');
	            setTimeout(function(){ jQuery("#collapse-ontent-settings").collapse('show'); location.href = "#header-form-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    } 
	    if(jQuery(this).val() == 'social-profiles'){
	          	jQuery("#collapse-language-settings").collapse('hide');
                jQuery("#collapse-advanced-settings").collapse('hide');
                jQuery("#collapse-publish-settings").collapse('hide');
                jQuery("#collapse-design-settings").collapse('hide');
	            jQuery("#collapse-ontent-settings").collapse('show');
	            setTimeout(function(){ jQuery("#collapse-ontent-settings").collapse('show'); location.href = "#header-social-profiles-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    } 
	    if(jQuery(this).val() == 'social-buttons'){
	          	jQuery("#collapse-language-settings").collapse('hide');
                jQuery("#collapse-advanced-settings").collapse('hide');
                jQuery("#collapse-publish-settings").collapse('hide');
                jQuery("#collapse-design-settings").collapse('hide');
	            jQuery("#collapse-ontent-settings").collapse('show');
	            setTimeout(function(){jQuery("#collapse-ontent-settings").collapse('show'); location.href = "#header-social-share-buttons-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    } 
	    if(jQuery(this).val() == 'countdown'){
	            jQuery("#collapse-language-settings").collapse('hide');
                jQuery("#collapse-advanced-settings").collapse('hide');
                jQuery("#collapse-publish-settings").collapse('hide');
                jQuery("#collapse-design-settings").collapse('hide');
	            jQuery("#collapse-ontent-settings").collapse('show');
	            setTimeout(function(){jQuery("#collapse-ontent-settings").collapse('show'); location.href = "#header-countdown-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    }
	    if(jQuery(this).val() == 'progress-bar'){
	          	jQuery("#collapse-language-settings").collapse('hide');
                jQuery("#collapse-advanced-settings").collapse('hide');
                jQuery("#collapse-publish-settings").collapse('hide');
                jQuery("#collapse-design-settings").collapse('hide');
	            jQuery("#collapse-ontent-settings").collapse('show');
	            setTimeout(function(){jQuery("#collapse-ontent-settings").collapse('show'); location.href = "#header-progess-bar-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    }
	    if(jQuery(this).val() == 'footer'){
	          	jQuery("#collapse-language-settings").collapse('hide');
                jQuery("#collapse-advanced-settings").collapse('hide');
                jQuery("#collapse-publish-settings").collapse('hide');
                jQuery("#collapse-design-settings").collapse('hide');
	            jQuery("#collapse-ontent-settings").collapse('show');
	            setTimeout(function(){jQuery("#collapse-ontent-settings").collapse('show'); location.href = "#header-footer-credit-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    }
	    if(jQuery(this).val() == 'page'){
	          	jQuery("#collapse-language-settings").collapse('hide');
                jQuery("#collapse-advanced-settings").collapse('hide');
                jQuery("#collapse-publish-settings").collapse('hide');
                jQuery("#collapse-design-settings").collapse('hide');
	            jQuery("#collapse-ontent-settings").collapse('show');
	            setTimeout(function(){jQuery("#collapse-ontent-settings").collapse('show'); location.href = "#header-page-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    }
	    
	    if(jQuery(this).val() == 'theme'){
	          	jQuery("#collapse-language-settings").collapse('hide');
                jQuery("#collapse-advanced-settings").collapse('hide');
                jQuery("#collapse-publish-settings").collapse('hide');
                jQuery("#collapse-ontent-settings").collapse('hide');
	            jQuery("#collapse-design-settings").collapse('show');
	            setTimeout(function(){jQuery("#collapse-design-settings").collapse('show'); location.href = "#header-theme-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    }
	    if(jQuery(this).val() == 'background'){
	          	jQuery("#collapse-language-settings").collapse('hide');
                jQuery("#collapse-advanced-settings").collapse('hide');
                jQuery("#collapse-publish-settings").collapse('hide');
                jQuery("#collapse-ontent-settings").collapse('hide');
	            jQuery("#collapse-design-settings").collapse('show');
	            setTimeout(function(){ jQuery("#collapse-design-settings").collapse('show');location.href = "#header-background-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    }
	    if(jQuery(this).val() == 'container'){
	        	jQuery("#collapse-language-settings").collapse('hide');
                jQuery("#collapse-advanced-settings").collapse('hide');
                jQuery("#collapse-publish-settings").collapse('hide');
                jQuery("#collapse-ontent-settings").collapse('hide');
	            jQuery("#collapse-design-settings").collapse('show');
	            setTimeout(function(){ location.href = "#header-container-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    }
	    if(jQuery(this).val() == 'elements'){
	        	jQuery("#collapse-language-settings").collapse('hide');
                jQuery("#collapse-advanced-settings").collapse('hide');
                jQuery("#collapse-publish-settings").collapse('hide');
                jQuery("#collapse-ontent-settings").collapse('hide');
	            jQuery("#collapse-design-settings").collapse('show');
	            setTimeout(function(){jQuery("#collapse-design-settings").collapse('show'); location.href = "#header-elements-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    }
	    if(jQuery(this).val() == 'typography'){
	        	jQuery("#collapse-language-settings").collapse('hide');
                jQuery("#collapse-advanced-settings").collapse('hide');
                jQuery("#collapse-publish-settings").collapse('hide');
                jQuery("#collapse-ontent-settings").collapse('hide');
	            jQuery("#collapse-design-settings").collapse('show');
	            setTimeout(function(){jQuery("#collapse-design-settings").collapse('show'); location.href = "#header-typography-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    }
	    if(jQuery(this).val() == 'custom-css'){
	        	jQuery("#collapse-language-settings").collapse('hide');
                jQuery("#collapse-advanced-settings").collapse('hide');
                jQuery("#collapse-publish-settings").collapse('hide');
                jQuery("#collapse-ontent-settings").collapse('hide');
	            jQuery("#collapse-design-settings").collapse('show');
	            setTimeout(function(){jQuery("#collapse-design-settings").collapse('show'); location.href = "#header-custom-css-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    }
	    if(jQuery(this).val() == 'header-language-settings'){
	        	jQuery("#collapse-design-settings").collapse('hide');
                jQuery("#collapse-advanced-settings").collapse('hide');
                jQuery("#collapse-publish-settings").collapse('hide');
                jQuery("#collapse-ontent-settings").collapse('hide');
	            jQuery("#collapse-language-settings").collapse('show');
	            setTimeout(function(){jQuery("#collapse-language-settings").collapse('show'); location.href = "#header-language-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    }
	    if(jQuery(this).val() == 'header-advanced-settings'){
	        	jQuery("#collapse-language-settings").collapse('hide');
                jQuery("#collapse-design-settings").collapse('hide');
                jQuery("#collapse-publish-settings").collapse('hide');
                jQuery("#collapse-ontent-settings").collapse('hide');
	            jQuery("#collapse-advanced-settings").collapse('show');
	            setTimeout(function(){jQuery("#collapse-advanced-settings").collapse('show'); location.href = "#header-advanced-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    }
	    if(jQuery(this).val() == 'header-publish-settings'){
	        	jQuery("#collapse-language-settings").collapse('hide');
                jQuery("#collapse-advanced-settings").collapse('hide');
                jQuery("#collapse-desgin-settings").collapse('hide');
                jQuery("#collapse-ontent-settings").collapse('hide');
	            jQuery("#collapse-publish-settings").collapse('show');
	            setTimeout(function(){jQuery("#collapse-publish-settings").collapse('show'); location.href = "#header-publish-settings"; jQuery('#seed-cspv5-sidebar').scrollTop(  jQuery('#seed-cspv5-sidebar').scrollTop() - 170) ; }, 500);
	    }
	    if(jQuery(this).val() == 'view-subscribers'){
           location.href=view_subscribers_url;
	    }
    });
	//////////////  Content

	// Page Name
	jQuery('#name').blur(function() {
	    save_page(false);
	});

	// Headline
	jQuery('#headline').on('input',function(e){
	    jQuery('#preview').contents().find('#cspio-headline').html(jQuery('#headline').val());
    });
    


	// Description
// 	jQuery('#description').summernote({
// 		onChange: function(contents, jQueryeditable) {jQuery('#preview').contents().find('#cspio-description').html(contents); jQuery('#description').text(contents);jQuery('#preview').contents().find("#cspio-description,#cspio-thankyoumsg").fitVids();},
// 		onPaste:  function(contents, jQueryeditable) {jQuery('#preview').contents().find('#cspio-description').html(contents); jQuery('#description').text(contents);jQuery('#preview').contents().find("#cspio-description,#cspio-thankyoumsg").fitVids();},

// 		toolbar: [
// 		['style', ['style','bold', 'italic', 'underline', 'clear']],
// 		['para', ['ul', 'ol', 'paragraph']],
// 		['insert',['link','video']],
// 		['misc',['codeview','fullscreen']]
// 		]
// 	});
    jQuery( document ).on( 'tinymce-editor-init', function( event, editor ) {
        if(editor.id == 'description'){
        var description = tinyMCE.get('description');
        description.on('keyup',function(ed) {
            var contents = tinymce.get('description').getContent();
            jQuery('#preview').contents().find('#cspio-description').html(contents); 
        });
        description.on('paste',function(ed) {
            var contents = tinymce.get('description').getContent();
            jQuery('#preview').contents().find('#cspio-description').html(contents); 
        });
        description.on('blur',function(ed) {
            var contents = tinymce.get('description').getContent();
            jQuery('#preview').contents().find('#cspio-description').html(contents); 
            jQuery('#description').text(contents);
            jQuery('#preview').contents().find("#cspio-description,#cspio-thankyoumsg").fitVids();
            //save_page(false);
        });
        }
        
        if(editor.id == 'thankyou_msg'){
            var thankyou = tinyMCE.get('thankyou_msg');
            thankyou.on('change',function(ed, l) {
                //console.log('dsdsadsa');
                var contents = tinymce.get('thankyou_msg').getContent();
                jQuery('#thankyou_msg').text(contents);
                //save_page(false);
            });
        }

        if(editor.id == 'cf_confirmation_msg'){
            var cf_confirmation_msg = tinyMCE.get('cf_confirmation_msg');
            cf_confirmation_msg.on('change',function(ed, l) {
                //console.log('sfdsfsdf');
                var contents = tinymce.get('cf_confirmation_msg').getContent();
                jQuery('#cf_confirmation_msg').text(contents);
                //save_page(false);
            });
        }
    });
    
    jQuery( document ).on( 'tinymce-editor-init', function( event, editor ) {
        jQuery('.mce-edit-area iframe').contents().find('body').css({"margin": "10px","font-family": "sans-serif","font-size": "14px"});
    });


	// Enable Form
	if (jQuery('#enable_form').is(':checked')) {
        jQuery("#form_settings").show();
    }else{
        jQuery("#form_settings").hide();
    }
    

    jQuery("#enable_form").change(function() {
	    if(this.checked) {
	        jQuery("#form_settings").fadeIn();
	        save_page();
	    }else{
	        jQuery("#form_settings").fadeOut();
	        save_page();
	    }
    });


    // Optin Settings
	jQuery('#optin_confirmation_text').on('input',function(e){
	    jQuery('#preview').contents().find('#cspio-optin-confirm-wrapper span').html(jQuery('#optin_confirmation_text').val());
    });
    
	if (jQuery('#display_optin_confirm').is(':checked')) {
        jQuery("#optin_settings").show();
    }else{
        jQuery("#optin_settings").hide();
    }
    

    jQuery("#display_optin_confirm").change(function() {
	    if(this.checked) {
	        jQuery("#optin_settings").fadeIn();
	        save_page();
	    }else{
	        jQuery("#optin_settings").fadeOut();
	        save_page();
	    }
    });

    // Enable Contact Form
    if (jQuery('#enable_cf_form').is(':checked')) {
        jQuery("#cf_form_settings").show();
    }else{
        jQuery("#cf_form_settings").hide();
    }
    

    jQuery("#enable_cf_form").change(function() {
        if(this.checked) {
            jQuery("#cf_form_settings").fadeIn();
            save_page();
        }else{
            jQuery("#cf_form_settings").fadeOut();
            save_page();
        }
    });

    // Email Marketing
    jQuery('#mail-config-link').click(function() {
        //console.log(jQuery(this).attr('data-link'));
        location.href=jQuery(this).attr('data-link');
	});

	jQuery('#emaillist').change(function() {
	    if(jQuery(this).val() == '0'){
	        jQuery('#mail-config-link').hide();
	        jQuery('#preview').attr('src', preview_url); 
	    }else{
	        jQuery('#mail-config-link').fadeIn();
	        jQuery('#mail-config-link').text('Configure ' + jQuery('option:selected',this).text());
	        jQuery('#mail-config-link').attr('data-link',path+'options-general.php?page=seed_cspv5_integrations&seed_cspv5_emaillist='+jQuery('#emaillist').val() + '&page_id='+page_id+'&return=options-general.php'+encodeURIComponent(location.search));     
	    }
	    save_page(false);
	}); 

	if(jQuery('#emaillist').val() == '0'){
	    jQuery('#mail-config-link').hide();

	}else{
	    jQuery('#mail-config-link').show();
	    jQuery('#mail-config-link').text('Configure ' + jQuery('#emaillist option:selected').text());
	    jQuery('#mail-config-link').attr('data-link',path+'options-general.php?page=seed_cspv5_integrations&seed_cspv5_emaillist='+jQuery('#emaillist').val() + '&page_id='+page_id+'&return=options-general.php'+encodeURIComponent(location.search));    
	} 

    // From Builder
    jQuery('#form-builder').click(function() {
        //jQuery("#preview-wrapper").removeClass('phone-wireframe');
        //jQuery('#preview').css({width: "100%",height: "100%",'padding-top': "0px"});
        location.href = form_link+'&return=options-general.php'+encodeURIComponent(location.search)+'&page_id='+page_id;
    });

    // Auotresponder
    jQuery('#autoresponder').click(function() {
        //jQuery("#preview-wrapper").removeClass('phone-wireframe');
        //jQuery('#preview').css({width: "100%",height: "100%",'padding-top': "0px"});
        location.href = autoresponder_link+'&return=options-general.php'+encodeURIComponent(location.search)+'&page_id='+page_id;
    });

	// Privacy Policy
	jQuery('#privacy_policy_link_text').on('input',function(e){
	    jQuery('#preview').contents().find('#cspio-privacy-policy-txt').html(jQuery('#privacy_policy_link_text').val());
	});

	// 	Thank You Message
// 	jQuery('#thankyou_msg').summernote({
// 		onChange: function(contents, jQueryeditable) {
// 		    //jQuery('#preview').contents().find('#cspio-description').html(contents);
		    
// 		},

// 		toolbar: [
// 		['style', ['style','bold', 'italic', 'underline', 'clear']],
// 		['para', ['ul', 'ol', 'paragraph']],
// 		['insert',['link','video']],
// 		['misc',['codeview','fullscreen']]
// 		]
// 	});
	
	
		
	jQuery(".btn-toolbar .btn").click(function() {
      if(jQuery(this).attr('data-event') == 'fullscreen'){
          //console.log(jQuery(".sidebar-header").css('z-index'));
          if(jQuery(".sidebar-header").css('z-index') == '300'){
             jQuery("#preview-actions,.sidebar-header").css('z-index','200'); 
          }else{
             jQuery("#preview-actions,.sidebar-header").css('z-index','300');
          }
          
      }
    });

	// Enable Social Profiles
	if (jQuery('#enable_socialprofiles').is(':checked')) {
        jQuery("#socialprofiles_settings").show();
    }else{
        jQuery("#socialprofiles_settings").hide();
    }
    

    jQuery("#enable_socialprofiles").change(function() {
	    if(this.checked) {
	        jQuery("#socialprofiles_settings").fadeIn();
	        save_page();
	    }else{
	        jQuery("#socialprofiles_settings").fadeOut();
	        save_page();
	    }
    });

    // Social Profile Logic
    jQuery( "#social_profiles_repeatable_container" ).sortable({
      placeholder: "ui-state-highlight",
      start: function(e, ui){
        ui.placeholder.height(ui.item.height());
    }
    });
    //jQuery( "#social_profiles_repeatable_container" ).disableSelection();
    jQuery( "#social_profiles_repeatable_container" ).on( "sortstop", function( event, ui ) { save_page()} )
    jQuery("#social_profiles_repeatable_container").repeatable({
        template: "#social_profiles_template",
        onAdd: add_fa_dropdown,
        onDelete: remove_fa_dropdown,
        startWith: s_c
    });

	
	// Font Awesome Profile Icons
	function add_fa_dropdown(){
	    jQuery('.icp-dd').iconpicker({
	        icons: ['fa-facebook-official','fa-twitter','fa-linkedin','fa-google-plus','fa-youtube','fa-flickr','fa-vimeo','fa-pinterest','fa-instagram','fa-foursquare', 'fa-skype','fa-tumblr', 'fa-github','fa-500px', 'fa-dribbble','fa-slack','fa-soundcloud','fa-snapchat-ghost', 'fa-rss', 'fa-envelope','fa-phone','fa-mobile'],
	        hideOnSelect: true
	    });
	    jQuery('.icp-dd').on('iconpickerSetValue', function (e) {
	        var i = jQuery(this).attr('id').match(/\d+/)[0];
	        jQuery("#icon_" + i).val(e.iconpickerValue);
	        jQuery(this).parents('.btn-group').removeClass('open');
	        save_page();
	    });

	}

	function remove_fa_dropdown(){
	    jQuery(this).parent().parent().parent().remove();
	    save_page();
	}

	add_fa_dropdown();

	// Social Profile Size
	jQuery('#social_profiles_size').change(function() {
	    if(jQuery(this).val() == ""){
	        jQuery('#preview').contents().find('#cspio-socialprofiles .fa').removeClass('fa-lg fa-2x fa-3x fa-4x fa-5x');   
	    }else{
	        jQuery('#preview').contents().find('#cspio-socialprofiles .fa').removeClass('fa-lg fa-2x fa-3x fa-4x fa-5x'); 
	        jQuery('#preview').contents().find('#cspio-socialprofiles .fa').addClass(jQuery(this).val()); 
	    }
	    
	    save_page(false);
	});

	// Social Profile Blank
	jQuery("#social_profiles_blank").change(function() {
	    if(this.checked) {
	        jQuery('#preview').contents().find('.fa').parent().prop('target','_blank');
	    }else{
	        jQuery('#preview').contents().find('.fa').parent().prop('target','_self');
	    }
	    save_page(false);
	});

	// Enable Social Share Buttons

	if (jQuery('#enable_socialbuttons').is(':checked')) {
        jQuery("#socialbuttons_settings").show();
    }else{
        jQuery("#socialbuttons_settings").hide();
    }
    

    jQuery("#enable_socialbuttons").change(function() {
    if(this.checked) {
        jQuery("#socialbuttons_settings").fadeIn();
        save_page();
    }else{
        jQuery("#socialbuttons_settings").fadeOut();
        save_page();
    }
    });

    //Share Button Logic

        // Show hide Fields Pin It

    if (jQuery('#share_buttons_pinterest').is(':checked')) {
        jQuery("#pinterest_thumbnail").parent().show();
    }else{
        jQuery("#pinterest_thumbnail").parent().hide();
    }
    

    jQuery("#share_buttons_pinterest").change(function() {
    if(this.checked) {
        jQuery("#pinterest_thumbnail").parent().show();
        if (jQuery('#show_sharebutton_on').val() == 'front' || (jQuery('#show_sharebutton_on').val() == 'both' )) {
        save_page();
        }else{
        save_page(false);   
        }
    }else{
        jQuery("#pinterest_thumbnail").parent().hide();
        if (jQuery('#show_sharebutton_on').val() == 'front' || (jQuery('#show_sharebutton_on').val() == 'both')) {
        save_page();
        }else{
        save_page(false);   
        }
    }
    });
    // Show hide Fields Twitter

    if (jQuery('#share_buttons_twitter').is(':checked')) {
        jQuery("#tweet_text").parent().show();
        
    }else{
        jQuery("#tweet_text").parent().hide();
    }
    
    
    jQuery("#share_buttons_twitter").change(function() {
    if(this.checked) {
        jQuery("#tweet_text").parent().show();
        if (jQuery('#show_sharebutton_on').val() == 'front' || (jQuery('#show_sharebutton_on').val() == 'both')) {
        save_page();
        }else{
        save_page(false);   
        }
    }else{
        jQuery("#tweet_text").parent().hide();
        if (jQuery('#show_sharebutton_on').val() == 'front' || (jQuery('#show_sharebutton_on').val() == 'both')) {
        save_page();
        }else{
        save_page(false);   
        }
    }
    });

    jQuery("#share_buttons_googleplus, #share_buttons_linkedin,#share_buttons_tumblr").change(function() {
        if (jQuery('#show_sharebutton_on').val() == 'front' || (jQuery('#show_sharebutton_on').val() == 'both')) {
        save_page();
        }else{
        save_page(false);   
        }
    });

    jQuery("#show_sharebutton_on").change(function() {
        save_page();
    });




    // Show hide Fields Facebook

    // if (jQuery('#share_buttons_facebook').is(':checked') || jQuery('#share_buttons_facebook_send').is(':checked')) {
    //     jQuery("#facebook_thumbnail").parent().show();
    // }else{
    //     jQuery("#facebook_thumbnail").parent().hide();
    // }
    
    // jQuery("#share_buttons_facebook").change(function() {
    // if(this.checked || jQuery('#share_buttons_facebook_send').is(':checked')) {
    //     jQuery("#facebook_thumbnail").parent().show();
    //     if (jQuery('#show_sharebutton_on').val() == 'front' || (jQuery('#show_sharebutton_on').val() == 'both')) {
    //     save_page();
    //     }else{
    //     save_page(false);   
    //     }
    // }else{
    //     jQuery("#facebook_thumbnail").parent().hide();
    //     if (jQuery('#show_sharebutton_on').val() == 'front' || (jQuery('#show_sharebutton_on').val() == 'both')) {
    //     save_page();
    //     }else{
    //     save_page(false);   
    //     }
    // }
    // });

    // jQuery("#share_buttons_facebook_send").change(function() {
    // if(this.checked || jQuery('#share_buttons_facebook').is(':checked')) {
    //     jQuery("#facebook_thumbnail").parent().show();
    //     if (jQuery('#show_sharebutton_on').val() == 'front' || (jQuery('#show_sharebutton_on').val() == 'both')) {
    //     save_page();
    //     }else{
    //     save_page(false);   
    //     }
    // }else{
    //     jQuery("#facebook_thumbnail").parent().hide();
    //     if (jQuery('#show_sharebutton_on').val() == 'front' || (jQuery('#show_sharebutton_on').val() == 'both')) {
    //     save_page();
    //     }else{
    //     save_page(false);   
    //     }
    // }
    // });

    // Enable Countdown
    if (jQuery('#enable_countdown').is(':checked')) {
        jQuery("#countdown_settings").show();
    }else{
        jQuery("#countdown_settings").hide();
    }

    jQuery('#countdown_timezone').val(countdown_timezone);
    

    jQuery("#enable_countdown").change(function() {
    if(this.checked) {
        jQuery("#countdown_settings").fadeIn();
        save_page();
    }else{
        jQuery("#countdown_settings").fadeOut();
        save_page();
    }
    });

    // CountDown End Date
    jQuery('#countdown_date').datetimepicker({});

    // Enable Progress Bar
    if (jQuery('#enable_progressbar').is(':checked')) {
        jQuery("#progress_bar_settings").show();
    }else{
        jQuery("#progress_bar_settings").hide();
    }
    

    jQuery("#enable_progressbar").change(function() {
    if(this.checked) {
        jQuery("#progress_bar_settings").fadeIn();
        save_page();
    }else{
        jQuery("#progress_bar_settings").fadeOut();
        save_page();
    }
    });

    // Progress Bar Method
    jQuery("#progress_bar_method").on("change", function (e) {
        if(jQuery(this).val() == 'date'){
            jQuery('#progress_bar_dates').fadeIn();
            jQuery('#progress_bar_pecentage').fadeOut();
        }else{
            jQuery('#progress_bar_pecentage').fadeIn();
            jQuery('#progress_bar_dates').fadeOut();
        }
    });

    if(jQuery("#progress_bar_method").val() == 'date'){
        jQuery('#progress_bar_dates').fadeIn();
        jQuery('#progress_bar_pecentage').fadeOut();
    }else{
        jQuery('#progress_bar_pecentage').fadeIn();
        jQuery('#progress_bar_dates').fadeOut();
    }


    // Progress Bar Start and End Date
    jQuery('#progress_bar_start_date').datetimepicker({format: 'L'});
    jQuery('#progress_bar_end_date').datetimepicker({format: 'L'});


    // Progress Bar Slider
    jQuery("#progressbar_percentage_slider").noUiSlider({
        start: progressbar_percentage,
        connect: "lower",
        step: 1,
        range: {
            'min': 0,
            'max': 100
        },
        format: wNumb({
            decimals: 0
        })
    });

    jQuery("#progressbar_percentage_slider").Link('lower').to('-inline-<div class="tooltip fade top in" style="top: -33px;left: -7px;opacity: 0.7;"></div>', function(value) {
        // The tooltip HTML is 'this', so additional
        // markup can be inserted here.
        jQuery(this).html(
            '<div class="tooltip-inner">' +
            '<span>' + value + '</span>' +
            '</div>'
        );
        jQuery('#progressbar_percentage').val(value);
        jQuery('#preview').contents().find('.progress-bar').css('width',value + '%');
        jQuery('#preview').contents().find('.progress-bar span').text(value + '%');

    });

    // Form Width Slider
    jQuery("#form_width_slider").noUiSlider({
        start: form_width,
        connect: "lower",
        step: 1,
        range: {
            'min': 50,
            'max': 100
        },
        format: wNumb({
            decimals: 0
        })
    });

    jQuery("#form_width_slider").Link('lower').to('-inline-<div class="tooltip fade top in" style="top: -33px;left: -7px;opacity: 0.7;"></div>', function(value) {
        // The tooltip HTML is 'this', so additional
        // markup can be inserted here.
        jQuery(this).html(
            '<div class="tooltip-inner">' +
            '<span>' + value + '%</span>' +
            '</div>'
        );
        jQuery('#form_width').val(value);
        jQuery('#preview').contents().find('#cspio-field-wrapper').css('width',value + '%');


    });


    // Enable Footer Credit
    if (jQuery('#enable_footercredit').is(':checked')) {
        jQuery("#footercredit_settings").show();
    }else{
        jQuery("#footercredit_settings").hide();
    }   
    

    jQuery("#enable_footercredit").change(function() {
	    if(this.checked) {
	        jQuery("#footercredit_settings").fadeIn();
	        save_page();
	    }else{
	        jQuery("#footercredit_settings").fadeOut();
	        save_page();
	    }
    });

    // Credit Type
    if(jQuery('#credit_type').val() == 'text'){
        jQuery('#footer_credit_text').parent().fadeIn();
        jQuery('#footer_credit_link').parent().fadeIn();
        jQuery('#footer_credit_img').parent().hide();
        jQuery('#footer_affiliate_link').parent().hide();
        jQuery('#footer_text_color').parent().parent().fadeIn();

    }else if(jQuery('#credit_type').val() == 'image'){
        jQuery('#footer_credit_text').parent().hide();
        jQuery('#footer_credit_link').parent().fadeIn();
        jQuery('#footer_credit_img').parent().fadeIn();
        jQuery('#footer_affiliate_link').parent().hide();
        jQuery('#footer_text_color').parent().parent().hide();
    }else if(jQuery('#credit_type').val() == 'affiliate'){
        jQuery('#footer_credit_text').parent().hide();
        jQuery('#footer_credit_link').parent().hide();
        jQuery('#footer_credit_img').parent().hide();
        jQuery('#footer_affiliate_link').parent().fadeIn();
        jQuery('#footer_text_color').parent().parent().hide();
    }

    jQuery("#credit_type").on("change", function (e) {
        if(jQuery(this).val() == 'text'){
            jQuery('#footer_credit_text').parent().fadeIn();
            jQuery('#footer_credit_link').parent().fadeIn();
            jQuery('#footer_credit_img').parent().hide();
            jQuery('#footer_affiliate_link').parent().hide();
            jQuery('#footer_text_color').parent().parent().fadeIn();

        }else if(jQuery(this).val() == 'image'){
            jQuery('#footer_credit_text').parent().hide();
            jQuery('#footer_credit_link').parent().fadeIn();
            jQuery('#footer_credit_img').parent().fadeIn();
            jQuery('#footer_affiliate_link').parent().hide();
            jQuery('#footer_text_color').parent().parent().hide();
        }else if(jQuery(this).val() == 'affiliate'){
            jQuery('#footer_credit_text').parent().hide();
            jQuery('#footer_credit_link').parent().hide();
            jQuery('#footer_credit_img').parent().hide();
            jQuery('#footer_affiliate_link').parent().fadeIn();
            jQuery('#footer_text_color').parent().parent().hide();
        }
    });

    jQuery('#footer_credit_text').on('input',function(e){
	    jQuery('#preview').contents().find('#cspio-credit a').html(jQuery('#footer_credit_text').val());
	});

	jQuery('#footer_credit_link').on('input',function(e){
	    jQuery('#preview').contents().find('#cspio-credit a').prop('href',jQuery('#footer_credit_link').val());
	});

    jQuery('.footer_text_color_picker').colorpicker({ component: '.form-control, .add-on, .input-group-addon' }).on('changeColor.colorpicker', function(event){
      jQuery('#preview').contents().find('#cspio-credit,#cspio-credit a ').css('color',"rgba("+event.color.toRGB().r+","+event.color.toRGB().g+","+event.color.toRGB().b+","+event.color.toRGB().a+")");
    });

	// Sections Block Order

	jQuery( "#blocks" ).sortable({
      placeholder: "ui-state-highlight",
      start: function(e, ui){
        ui.placeholder.height(ui.item.height());
    }
    });
    //jQuery( "#blocks" ).disableSelection();
    jQuery( "#blocks" ).on( "sortstop", function( event, ui ) { save_page()} );

    //////////////  Design

    // Theme Picker
    jQuery('#theme-picker').click(function() {
        //jQuery("#preview-wrapper").removeClass('phone-wireframe');
        //jQuery('#preview').css({width: "100%",height: "100%",'padding-top': "0px"});
        location.href = theme_link+'&return=options-general.php'+encodeURIComponent(location.search)+'&page_id='+page_id;
    });

    // Background Color
    jQuery('.background_color_picker').colorpicker({ component: '.form-control, .add-on, .input-group-addon' }).on('changeColor.colorpicker', function(event){
      jQuery('#preview').contents().find('html').css('background-color',event.color.toHex());
    });



    // Background Image Picker
    jQuery("#image-picker").on("show.bs.modal", function(e) {
        p = page
        get_posts(p);
    });


    //Background Advanced Settings
    if (jQuery('#enable_background_adv_settings').is(':checked')) {
        jQuery("#background_adv_settings").show();
    }else{
        jQuery("#background_adv_settings").hide();
    }
    

    jQuery("#enable_background_adv_settings").change(function() {
    if(this.checked) {
        jQuery("#background_adv_settings").fadeIn();
    }else{
        jQuery("#background_adv_settings").hide();
    }
    });


    //Background Advanced Settings
    if (jQuery('#enable_background_overlay').is(':checked')) {
        jQuery("#background_overlay").parents('.form-group').show();
    }else{
        jQuery("#background_overlay").parents('.form-group').hide();
    }
    

    jQuery("#enable_background_overlay").change(function() {
    if(this.checked) {
        jQuery("#background_overlay").parents('.form-group').fadeIn();
        jQuery('#preview').contents().find('#cspio-page').css('background-color',"rgba("+jQuery("#background_overlay").val()+")");
    }else{
        jQuery("#background_overlay").parents('.form-group').hide();
        jQuery('#preview').contents().find('#cspio-page').css('background-color',"transparent");
    }
    });

    // Background Overlay
    jQuery('.background_overlay_picker').colorpicker({ component: '.form-control, .add-on, .input-group-addon' }).on('changeColor.colorpicker', function(event){
      if(jQuery('#enable_background_overlay').is(':checked')){
        jQuery('#preview').contents().find('#cspio-page').css('background-color',"rgba("+event.color.toRGB().r+","+event.color.toRGB().g+","+event.color.toRGB().b+","+event.color.toRGB().a+")");
      }
    });

    // Background Size
    jQuery('#background_size').change(function() {
        save_page(false);
        jQuery('#preview').contents().find('html').css('background-size',jQuery('#background_size').val());
    });

    jQuery('#background_repeat').change(function() {
        save_page(false);
        jQuery('#preview').contents().find('html').css('background-repeat',jQuery('#background_repeat').val());
    });

    jQuery('#background_position').change(function() {
        save_page(false);
        jQuery('#preview').contents().find('html').css('background-position',jQuery('#background_position').val());
    });

    jQuery('#background_attachment').change(function() {
        save_page(false);
        jQuery('#preview').contents().find('html').css('background-attachment',jQuery('#background_attachment').val());
    });

    // Background Slideshow
    jQuery( "#slides" ).sortable({
      placeholder: "ui-state-highlight",
      start: function(e, ui){
        ui.placeholder.height(ui.item.height());
    }
    });
    //jQuery( "#slides" ).disableSelection();

    if (jQuery('#bg_slideshow').is(':checked')) {
        jQuery("#bg_slideshow_settings").show();
    }else{
        jQuery("#bg_slideshow_settings").hide();
    }
    

    jQuery("#bg_slideshow").change(function() {
    if(this.checked) {
        jQuery("#bg_slideshow_settings").fadeIn();
        save_page();
    }else{
        jQuery("#bg_slideshow_settings").fadeOut();
        save_page();
    }
    });

    jQuery("#bg_slideshow_randomize").change(function() {
        save_page();
    });
    
    // Container Radius
    jQuery("#bg_slideshow_slide_speed_slider").noUiSlider({
        start: bg_slideshow_slide_speed,
        connect: "lower",
        step: 1,
        range: {
            'min': 0,
            'max': 20
        },
        format: wNumb({
            decimals: 0
        })
    });

    jQuery("#bg_slideshow_slide_speed_slider").Link('lower').to('-inline-<div class="tooltip fade top in" style="top: -33px;left: -7px;opacity: 0.7;"></div>', function(value) {
        // The tooltip HTML is 'this', so additional
        // markup can be inserted here.
        jQuery(this).html(
            '<div class="tooltip-inner">' +
            '<span>' + value + ' sec</span>' +
            '</div>'
        );
        jQuery('#bg_slideshow_slide_speed').val(value);
    });
    

    jQuery('.slide-delete').on('click',function(e){
      index = jQuery( "#slides" ).find('input[name^="bg_slideshow_images"]').length  ;
      //console.log(index);
      if(index == 1){
        jQuery(this).parent().find('input[name^="bg_slideshow_images"]').val('');
        jQuery(this).parent().find('img').prop('src',blank_gif)
      }else{
        jQuery(this).parent().remove();
      }
      save_page();
  });

    // Background Video
    if (jQuery('#bg_video').is(':checked')) {
        jQuery("#bg_video_settings").show();
    }else{
        jQuery("#bg_video_settings").hide();
    }
    

    jQuery("#bg_video").change(function() {
    save_page();
    if(this.checked) {
        jQuery("#bg_video_settings").fadeIn();
    }else{
        jQuery("#bg_video_settings").fadeOut();
    }
    });

    // Container Transparent
    if (jQuery('#container_transparent').is(':checked')) {
        jQuery("#container_color").parent().parent().hide();
        jQuery("#container_radius").parent().hide();
    }else{
        jQuery("#container_color").parent().parent().show();
        jQuery("#container_radius").parent().show();
    }

    jQuery("#container_transparent").change(function() {
    if(this.checked) {
        jQuery("#container_color").parent().parent().fadeOut();
        jQuery("#container_radius").parent().fadeOut();
        jQuery('#preview').contents().find('#cspio-content').css('background-color',"transparent");
        save_page(false);
    }else{
        jQuery("#container_color").parent().parent().fadeIn();
        jQuery("#container_radius").parent().fadeIn();
        jQuery('#preview').contents().find('#cspio-content').css('background-color',jQuery('#container_color').val());
        save_page(false);
    }
    });  

    // Contact Form Color
    jQuery('.contactform_color_picker').colorpicker({ component: '.form-control, .add-on, .input-group-addon' }).on('changeColor.colorpicker', function(event){
      jQuery('#preview').contents().find('#cspio-contact-form a').css('color',event.color.toHex());
    }); 

    // SocialProfile Color
    jQuery('.socialprofile_color_picker').colorpicker({ component: '.form-control, .add-on, .input-group-addon' }).on('changeColor.colorpicker', function(event){
      jQuery('#preview').contents().find('#cspio-socialprofiles a').css('color',event.color.toHex());
    }); 

    // Container Color
    jQuery('.container_color_picker').colorpicker({ component: '.form-control, .add-on, .input-group-addon' }).on('changeColor.colorpicker', function(event){
      jQuery('#preview').contents().find('#cspio-content').css('background-color',"rgba("+event.color.toRGB().r+","+event.color.toRGB().g+","+event.color.toRGB().b+","+event.color.toRGB().a+")");
    });

    // Container Radius
    jQuery("#container_radius_slider").noUiSlider({
        start: container_radius,
        connect: "lower",
        step: 1,
        range: {
            'min': 0,
            'max': 100
        },
        format: wNumb({
            decimals: 0
        })
    });

    jQuery("#container_radius_slider").Link('lower').to('-inline-<div class="tooltip fade top in" style="top: -33px;left: -7px;opacity: 0.7;"></div>', function(value) {
        // The tooltip HTML is 'this', so additional
        // markup can be inserted here.
        jQuery(this).html(
            '<div class="tooltip-inner">' +
            '<span>' + value + 'px</span>' +
            '</div>'
        );
        jQuery('#container_radius').val(value);
        jQuery('#preview').contents().find('#cspio-content').css('border-radius', value+"px");

    });


    // Container Position
    jQuery('#container_position').on('change',function(e){
    	if(jQuery(this).val() == '1'){
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('align-items','center');
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('justify-content','center');
            //jQuery('#preview').contents().find('.flexbox #cspio-page').animate({'align-items': center,'justify-content':center});
    	}
      	if(jQuery(this).val() == '2'){
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('align-items','flex-start');
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('justify-content','center');
    	}
        if(jQuery(this).val() == '3'){
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('align-items','flex-end');
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('justify-content','center');
    	}
    	if(jQuery(this).val() == '4'){
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('align-items','center');
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('justify-content','flex-start');
    	}
    	if(jQuery(this).val() == '5'){
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('align-items','flex-start');
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('justify-content','flex-start');
    	}
    	if(jQuery(this).val() == '6'){
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('align-items','flex-end');
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('justify-content','flex-start');
    	}
    	if(jQuery(this).val() == '7'){
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('align-items','center');
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('justify-content','flex-end');
    	}
     	if(jQuery(this).val() == '8'){
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('align-items','flex-start');
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('justify-content','flex-end');
    	}
     	if(jQuery(this).val() == '9'){
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('align-items','flex-end');
    		jQuery('#preview').contents().find('.flexbox #cspio-page').css('justify-content','flex-end');
    	}
        
        save_page(false);
    });

    // Container Width
    jQuery("#container_width_slider").noUiSlider({
        start: container_width,
        connect: "lower",
        step: 1,
        range: {
            'min': 200,
            'max': 2000
        },
        format: wNumb({
            decimals: 0
        })
    });



    jQuery("#container_width_slider").Link('lower').to('-inline-<div class="tooltip fade top in" style="top: -33px;left: -7px;opacity: 0.7;"></div>', function(value) {
        // The tooltip HTML is 'this', so additional
        // markup can be inserted here.
        jQuery(this).html(
            '<div class="tooltip-inner">' +
            '<span>' + value + 'px</span>' +
            '</div>'
        );
        jQuery('#container_width').val(value);
        jQuery('#preview').contents().find('#cspio-content').css('max-width',value + 'px');

    });
    
        // Form Width
    // jQuery("#form_width_slider").noUiSlider({
    //     start: form_width,
    //     connect: "lower",
    //     step: 1,
    //     range: {
    //         'min': 0,
    //         'max': 100
    //     },
    //     format: wNumb({
    //         decimals: 0
    //     })
    // });



    // jQuery("#form_width_slider").Link('lower').to('-inline-<div class="tooltip fade top in" style="top: -33px;left: -7px;opacity: 0.7;"></div>', function(value) {
    //     // The tooltip HTML is 'this', so additional
    //     // markup can be inserted here.
    //     jQuery(this).html(
    //         '<div class="tooltip-inner">' +
    //         '<span>' + value + '%</span>' +
    //         '</div>'
    //     );
    //     jQuery('#form_width').val(value);
    //     jQuery('#preview').contents().find('#cspio-field-wrapper').css('max-width',value + '%');

    // });

    // Element Color
    jQuery('.button_color_picker').colorpicker({ component: '.form-control, .add-on, .input-group-addon' }).on('changeColor.colorpicker', function(event){
      //if(jQuery('#container_flat').val() == '1'){
      jQuery('#preview').contents().find('#cspio-subscribe-btn,.cspio .progress-bar, a.btn-primary, .mailster-wrapper .submit-button, input[type="button"].ninja-forms-field, .frm_button_submit').css('background-color',"rgba("+event.color.toRGB().r+","+event.color.toRGB().g+","+event.color.toRGB().b+","+event.color.toRGB().a+")");

      if(jQuery('#preview').contents().find('#tmp-countdown-style').length == 0){
        jQuery('#preview').contents().find('head').append("<style id='tmp-countdown-style' type='text/css'></style>");
      }
      

      var lightness = jQuery.Color(jQuery('#button_color').val()).lightness();
      if(lightness >= 0.65){
        var color = '#000';
        jQuery('#preview').contents().find('#cspio-subscribe-btn,.cspio .progress-bar span,a.btn-primary,.mailster-wrapper .submit-button, input[type="button"].ninja-forms-field, .frm_button_submit').css('color','#000');
      }else{
        var color = '#fff';
        jQuery('#preview').contents().find('#cspio-subscribe-btn,.cspio .progress-bar span,a.btn-primary, .mailster-wrapper .submit-button,input[type="button"].ninja-forms-field, .frm_button_submit').css('color','#fff');
      }

      jQuery('#preview').contents().find('#tmp-countdown-style').html('.countdown_section{background-color:rgba('+event.color.toRGB().r+","+event.color.toRGB().g+","+event.color.toRGB().b+","+event.color.toRGB().a+');color:'+color+'}');

      //}

    });
    
    jQuery('#button_color').on('blur',function(e){
        if(jQuery('#container_flat').prop( "checked" )){
            save_page(false);
        }else{
            save_page();
        }
	});


    
    //Element Border Color
    
    // jQuery('.element_border_color_picker').colorpicker({ component: '.form-control, .add-on, .input-group-addon' }).on('changeColor.colorpicker', function(event){
    //   jQuery('#preview').contents().find('#cspio-subscribe-btn,.cspio .progress').css('border',"1px solid rgba("+event.color.toRGB().r+","+event.color.toRGB().g+","+event.color.toRGB().b+","+event.color.toRGB().a+")");

    //   if(jQuery('#preview').contents().find('#tmp-countdown-border-style').length == 0){
    //     jQuery('#preview').contents().find('head').append("<style id='tmp-countdown-border-style' type='text/css'></style>");
    //   }
      

    //   jQuery('#preview').contents().find('#tmp-countdown-border-style').html('.countdown_section{border: 1px solid rgba('+event.color.toRGB().r+","+event.color.toRGB().g+","+event.color.toRGB().b+","+event.color.toRGB().a+');}');



    // });
    
    // Form Input Background Color
    jQuery('.form_color_picker').colorpicker({ component: '.form-control, .add-on, .input-group-addon' }).on('changeColor.colorpicker', function(event){
      jQuery('#preview').contents().find('input,.progress,textarea').css('background-color',"rgba("+event.color.toRGB().r+","+event.color.toRGB().g+","+event.color.toRGB().b+","+event.color.toRGB().a+")");


      if(jQuery('#preview').contents().find('#tmp-form-style').length == 0){
        jQuery('#preview').contents().find('head').append("<style id='tmp-form-style' type='text/css'></style>");
      }
      

      var lightness = jQuery.Color(jQuery('#form_color').val()).lightness();
      if(lightness >= 0.65){
        var color = '#999';
        var textcolor = "#000";
      }else{
        var color = '#999';
        var textcolor = "#fff";
      }
      jQuery('#preview').contents().find('.form-control').css('color',textcolor);

      jQuery('#preview').contents().find('#tmp-form-style').html('::-webkit-input-placeholder {color:'+color+' !important};:-moz-placeholder {color:'+color+' !important};::-moz-placeholder {color:'+color+' !important};:-ms-input-placeholder {color:'+color+' !important};');

    });
    
    //Form Input Border Color
    
    // jQuery('.form_border_color_picker').colorpicker({ component: '.form-control, .add-on, .input-group-addon' }).on('changeColor.colorpicker', function(event){
    //   jQuery('#preview').contents().find('input').css('border',"1px solid rgba("+event.color.toRGB().r+","+event.color.toRGB().g+","+event.color.toRGB().b+","+event.color.toRGB().a+")");
    // });

    // Container Animation
    jQuery('#container_effect_animation').val(container_effect_animation);
    jQuery('#container_effect_animation').change(function() {
        if(jQuery('#preview').contents().find('#animated-css').length == 0) {
            url='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.4.0/animate.min.css';
            jQuery('#preview').contents().find('head').append('<link id="animated-css" rel="stylesheet" href="'+url+'" type="text/css" />');
        }
        jQuery('#preview').contents().find('#cspio-content').removeClass();
        jQuery('#preview').contents().find('#cspio-content').addClass('animated ' + jQuery(this).val());
        save_page(false);
    });

    // Custom CSS
    jQuery('#custom_css').on("input", function (e) { 
        jQuery('#preview').contents().find('#tmp-custom-css-style').remove();
        jQuery('#preview').contents().find('head').append("<style id='tmp-custom-css-style' type='text/css'></style>");
        jQuery('#preview').contents().find('#tmp-custom-css-style').html(jQuery('#custom_css').val());

    });
    
    // Hide translations
    function show_hide_countdown_translations(){
        if (jQuery('#enable_countdown').is(':checked')) {
            jQuery("#txt_countdown_days,#txt_countdown_day,#txt_countdown_hours,#txt_countdown_hour,#txt_countdown_minutes,#txt_countdown_minute,#txt_countdown_seconds,#txt_countdown_second").parent().show();
        }else{
            jQuery("#txt_countdown_days,#txt_countdown_day,#txt_countdown_hours,#txt_countdown_hour,#txt_countdown_minutes,#txt_countdown_minute,#txt_countdown_seconds,#txt_countdown_second").parent().hide();
        }
    }
    
    jQuery("#enable_countdown").change(function() {
        show_hide_countdown_translations();
    });
    
    show_hide_countdown_translations();
    
    function show_hide_reflink_translations(){
        if (jQuery('#enable_reflink').is(':checked')) {
            jQuery("#txt_stats_referral_url,#txt_stats_referral_stats,#txt_stats_referral_clicks,#txt_stats_referral_subscribers").parent().show();
            jQuery("#show_sharebutton_on option").each(function(){
                if (jQuery(this).val().toLowerCase() == "front" || jQuery(this).val().toLowerCase() == "both") {
                    jQuery(this).attr("disabled", "disabled");
                }
            });
            jQuery('#show_sharebutton_on option:eq(1)').prop('selected', true);
        }else{
            jQuery("#txt_stats_referral_url,#txt_stats_referral_stats,#txt_stats_referral_clicks,#txt_stats_referral_subscribers").parent().hide();
            jQuery("#show_sharebutton_on option").each(function(){
                jQuery(this).removeAttr("disabled");
            });
        }
    }
    
    jQuery("#enable_reflink").change(function() {
        show_hide_reflink_translations();
    });
    
    show_hide_reflink_translations();

    // Translate Text
    jQuery('#txt_subscribe_button').on('input',function(e){
        jQuery('#preview').contents().find('#cspio-subscribe-btn').html(jQuery(this).val());
    });

    jQuery('#txt_email_field').on('input',function(e){
        jQuery('#preview').contents().find('#cspio-email').prop('placeholder',jQuery(this).val());
    });

    jQuery('#txt_name_field').on('input',function(e){
        jQuery('#preview').contents().find('#cspio-name').prop('placeholder',jQuery(this).val());
    });

    // Fonts Stuff
    var y=0;
    // jQuery('#headline_font').select2({
    //     templateResult: function (result) {
    //                     if(String(result.id).match("^'") && String(result.id).match("'$")){
    //                         var state = jQuery('<div style="background-position:-10px -'+y+'px !important;">'+result.text+'</div>');
    //                         y   +=29;
    //                         return state;
    //                     }else{
    //                         return result.text;
    //                     }
    //                 }

    //     });
    jQuery('#headline_font').select2();
    jQuery('#text_font').select2();
    jQuery('#button_font').select2();

    jQuery('.headline_color_picker').colorpicker({ component: '.form-control, .add-on, .input-group-addon' }).on('changeColor.colorpicker', function(event){
      jQuery('#preview').contents().find('#cspio-headline').css('color',"rgba("+event.color.toRGB().r+","+event.color.toRGB().g+","+event.color.toRGB().b+","+event.color.toRGB().a+")");
    });


    jQuery('.text_color_picker').colorpicker({ component: '.form-control, .add-on, .input-group-addon' }).on('changeColor.colorpicker', function(event){
      jQuery('#preview').contents().find('body, p, #cspio-socialprofiles a').css('color',"rgba("+event.color.toRGB().r+","+event.color.toRGB().g+","+event.color.toRGB().b+","+event.color.toRGB().a+")");
    });


    jQuery('#text_size').on('input',function(e){
      jQuery('#preview').contents().find('body, p').css('font-size',jQuery('#text_size').val() + 'px');
    });


    jQuery('#text_line_height').on('input',function(e){
      jQuery('#preview').contents().find('body').css('line-height',jQuery('#text_line_height').val() + 'px');
    });
    
    if(jQuery('#publish_method').val() == 'download'){
        jQuery("#auth_code").parent().parent().hide();
        jQuery("#url").parent().show();
    }
    if(jQuery('#publish_method').val() == 'wordpress'){
        jQuery("#auth_code").parent().parent().show();
        jQuery("#url").parent().hide();
    }
    
    jQuery('#publish_method').change(function() {
        if(jQuery('#publish_method').val() == 'download'){
            jQuery("#auth_code").parent().parent().hide();
            jQuery("#url").parent().show();
        }
        if(jQuery('#publish_method').val() == 'wordpress'){
            jQuery("#auth_code").parent().parent().show();
            jQuery("#url").parent().hide();
        }
    });

        // From Builder
    jQuery('#language-builder').click(function() {
        //jQuery("#preview-wrapper").removeClass('phone-wireframe');
        //jQuery('#preview').css({width: "100%",height: "100%",'padding-top': "0px"});
        location.href = language_link+'&page_id='+page_id;
    });


    //Recapcha
    if (jQuery('#enable_recaptcha').is(':checked')) {
        jQuery("#recaptcha_adv_settings").show();
    }else{
        jQuery("#recaptcha_adv_settings").hide();
    }
    

    jQuery("#enable_recaptcha").change(function() {
    if(this.checked) {
        jQuery("#recaptcha_adv_settings").fadeIn();
    }else{
        jQuery("#recaptcha_adv_settings").hide();
    }
    });

    //Fraud Detection
    if (jQuery('#enable_reflink').is(':checked')) {
        jQuery("#enable_fraud_detection,#enable_prize_levels,#rp_return_user").parent().show();
    }else{
        jQuery("#enable_fraud_detection,#enable_prize_levels,#rp_return_user").parent().hide();
    }
    

    jQuery("#enable_reflink").change(function() {
    if(this.checked) {
        jQuery("#enable_fraud_detection,#enable_prize_levels,#rp_return_user").parent().fadeIn();
    }else{
        jQuery("#enable_fraud_detection,#enable_prize_levels,#rp_return_user").parent().hide();
    }
    });


    //Prize Level Config
    if (jQuery('#enable_prize_levels').is(':checked')) {
        jQuery("#prize-config-link").show();
    }else{
        jQuery("#prize-config-link").hide();
    }
    

    jQuery("#enable_prize_levels").change(function() {
    if(this.checked) {
        jQuery("#prize-config-link").fadeIn();
    }else{
        jQuery("#prize-config-link").hide();
    }
    });

    jQuery('#prize-config-link').click(function() {
        location.href = prize_link+'&return=options-general.php'+encodeURIComponent(location.search)+'&page_id='+page_id;
    });


}); // end doc ready


// Start Font



// End Font

// Functions
function remove_slides(){
    jQuery(this).remove();
}

// Dim iframe while changes are made.
jQuery(document)
.ajaxStart(function () {
    
})
.ajaxStop(function () {
    
});

jQuery('#preview').load(function(){
 // console.log('clear');
  clearTimeout(timeout);
  jQuery('#ajax-status').hide();
  jQuery('#preview').animate({
    opacity: "1"
}, 500); 
});

function save_html(refresh){
    jQuery.get( "ajax/test.html", function( data ) {
      $( ".result" ).html( data );
      alert( "Load was performed." );
    });
}

// Save Settings
function save_page(refresh){
    if(typeof refresh === 'undefined'){
        refresh = true;
    }
    // Clear any errors
    jQuery(".help-block").remove();
    jQuery(".form-group").removeClass('has-error');
    try{
    jQuery("#publish-btn").button('loading');
    }catch(err) {}

    // Submit data
    var dataString = jQuery( '#seed_cspv5_customizer' ).serialize();
    //console.log(dataString);

    if(dataString == ''){
        return 'false';
    }
    
    jQuery.ajax({
        type: "POST",
        url : save_url,
        data : dataString,
        beforeSend : function(data){
                if(refresh){
                    //console.log('timeout');
                    jQuery('#preview').css('opacity','0');
                    jQuery('#ajax-status').show();

                    
                    // timeout = setTimeout(function(){
                    //     location = ''
                    //   },10000);
                }
        },
        success : function(data){
            if(data == 'true'){
                try{
                jQuery("#publish-btn").button('reset');
                }catch(err) {}
                if(refresh){
                document.getElementById('preview').contentWindow.location.reload(true);
                }
                if(refresh == 'hard'){
                    location.refresh();
                }
                return true;
            }else{
                //console.log(jQuery.parseJSON(data));
                errors = '';
                jQuery.each( jQuery.parseJSON(data), function( key, value ) {
                  errors =  errors + "<li>"+value+"</li>";
                });
                toastr.options.timeOut = 15000;
                toastr.options.progressBar = true;
                toastr.error('Your settings could not be saved. Please make sure these fields are not empty. <ul style="list-style-type: circle;font-size:11px">'+errors+'</ul>');
            }
            

        },
        error: function(data){
            if(data.status == '403'){
                jQuery('#preview').css('opacity','1');
                jQuery('#ajax-status').hide();
                toastr.error('Your settings could not be saved. The WordFence Firewall is blocking the Save. Please set the Firewall to Learning Mode while building this page.');
            }else{
                toastr.error('Your settings could not be saved. Refresh the page and try again. Please contact Support if you continue to experience this issue.');
            }
            //alert('Your settings could not be saved. Please open a Support Ticket so we can identify the issue.');
            // var errors = data.responseJSON;
            // jQuery.each( errors, function( key, value ) {
            //     jQuery( "#"+key ).parent().append("<span class='help-block'>"+value+"</span>").addClass('has-error');
            // });
        }
    });
}

//Image Functions
jQuery( "#logo,#facebook_thumbnail ,#pinterest_thumbnail,#favicon,#background_image, #footer_credit_img" ).change(function() {
  update_image_preview(this);
});
function update_image_preview(el){
    var file = jQuery(el).val();
    jQuery(el).parent().find(".img-preview img").prop('src',file);
    jQuery(el).parent().find(".img-preview").show();
    var id = jQuery(el).attr('id');
    if(id == 'logo'){
        jQuery('#preview').contents().find('#cspio-logo').prop('src', file);
    }
    if(id == 'background_image'){
        jQuery('#preview').contents().find('html').css('background-image', 'url('+file+")");
        jQuery('#preview').contents().find('html').css('background-size',jQuery('#background_size').val());
    	jQuery('#preview').contents().find('html').css('background-repeat',jQuery('#background_repeat').val());
    	jQuery('#preview').contents().find('html').css('background-position',jQuery('#background_position').val());
    	jQuery('#preview').contents().find('html').css('background-attachment',jQuery('#background_attachment').val());    
    }
    if(id == 'footer_credit_img'){
        jQuery("#footer_credit_img").parent().find(".img-preview img").prop('src',file);
        jQuery("#footer_credit_img").parent().find(".img-preview").show();
        jQuery('#preview').contents().find('#cspio-credit img').prop('src', file);
    }
    save_page();
}

jQuery( "#bg_slideshow_tmp" ).change(function() {
  update_slides(this);
});

function update_slides(el){
    var file = jQuery(el).val();
    jQuery(el).val('');
    index = jQuery( "#slides" ).find('input[name^="bg_slideshow_images"]').length  ;
    //console.log(index);
    if(jQuery("#bg_slideshow_images_"+ (index - 1)).val() == ''){
        //console.log('current');
        jQuery("#bg_slideshow_images_"+(index - 1)).val(file);
        jQuery("#bg_slideshow_images_"+(index - 1)).parent().find('img').prop('src',file);
    }else{
        //console.log('new');
        jQuery("#bg_slideshow_images_"+(index - 1)).parent().clone().insertAfter("#slides .input-group:last").find("input").prop('id',"bg_slideshow_images_"+ index).prop('name',"bg_slideshow_images["+index+"]");
        jQuery("#bg_slideshow_images_"+index).val(file);
        jQuery("#bg_slideshow_images_"+index).parent().find('img').prop('src',file);
    }
    save_page();

        jQuery('.slide-delete').on('click',function(e){
      index = jQuery( "#slides" ).find('input[name^="bg_slideshow_images"]').length  ;
      //console.log(index);
      if(index == 1){
        jQuery(this).parent().find('input[name^="bg_slideshow_images"]').val('');
        jQuery(this).parent().find('img').prop('src',blank_gif)
      }else{
        jQuery(this).parent().remove();
      }
      save_page();
  });
};

function get_posts(p,q){

    url = index_backgrounds+'&query='+query+'&page='+page;

    jQuery.get( url, function( data ) {

        jQuery("#image-picker .modal-body").html(data);

        jQuery('.grid').imagesLoaded( {
          // options...
          },
          function() {
              jQuery('.grid').masonry({
                // options
                  itemSelector: '.grid-item',
                  columnWidth: 200,
                  gutter: 10
                });

          }
        );
        if(query != ''){
             jQuery('#cspv5_background_search .query').val(query);
        }

        jQuery('.pagination a').click(function (e) {
            e.preventDefault();
            var href =jQuery(this).attr('href').split('?')[1];
            console.log(href);
            var QueryString = function () {
              // This function is anonymous, is executed immediately and
              // the return value is assigned to QueryString!
              var query_string = {};
              console.log(href);
              var query = href;
              var vars = query.split("&");
              for (var i=0;i<vars.length;i++) {
                var pair = vars[i].split("=");
                    // If first entry with this name
                if (typeof query_string[pair[0]] === "undefined") {
                  query_string[pair[0]] = decodeURIComponent(pair[1]);
                    // If second entry with this name
                } else if (typeof query_string[pair[0]] === "string") {
                  var arr = [ query_string[pair[0]],decodeURIComponent(pair[1]) ];
                  query_string[pair[0]] = arr;
                    // If third or later entry with this name
                } else {
                  query_string[pair[0]].push(decodeURIComponent(pair[1]));
                }
              } 
              return query_string;
            }();

            page = QueryString.page;
            query= QueryString.query;

            //console.log(page);
            //console.log(query);
            get_posts(page,query);
        });

        jQuery('#cspv5_background_search .search').click(function (e) {
            e.preventDefault();
            query=  jQuery('#cspv5_background_search .query').val();
            page='1';
            //console.log(page);
            //console.log(query);
            get_posts(page,query);
        });

        jQuery( ".bg-images" ).on( "click", function() {

            var reg = jQuery( this ).attr('data-reg');
            var thumb = jQuery( this ).attr('data-thumb');
            var download= jQuery( this ).attr('data-download');
            // console.log(thumb);
            // return false;
            jQuery('#image-picker').modal('hide')
    
            // Save
            jQuery("#background_image").val(reg);
            jQuery("#background_image").parent().find(".img-preview img").prop('src',thumb);
            jQuery("#background_image").parent().find(".img-preview").show();
    
            // Show
            jQuery('#preview').contents().find('html').css('background-image', 'url('+reg+")");
            jQuery('#preview').contents().find('html').css('background-size',jQuery('#background_size').val());
            jQuery('#preview').contents().find('html').css('background-repeat',jQuery('#background_repeat').val());
            jQuery('#preview').contents().find('html').css('background-position',jQuery('#background_position').val());
            jQuery('#preview').contents().find('html').css('background-attachment',jQuery('#background_attachment').val());

           
            //Sideload Image to WordPress
            jQuery.get(download_backgrounds+'&image='+encodeURIComponent(download)),function (data){
                console.log(data);
            }
            jQuery.get( sideload_backgrounds+'&image='+encodeURIComponent(reg), function( data ) {
                    if(data != '0' && data.endsWith("jpg")){ 
                        //console.log(data);
                        jQuery("#background_image").val(data);
                    }
            }).always(function() {
                save_page(false);
            });
        });
    });




};







// Global //

// Save before close
jQuery(window).bind('beforeunload', function(e) {
    if(timeout === undefined){
    if(jQuery('#preview').attr('src') == preview_url){
      //save_page(false);
    }
    // var message = "Why are you leaving?";
    // e.returnValue = message;
    // return message;
    }
});
jQuery(document).ready(function() {
    //save_page(false);
});

jQuery(document).ready(function() {
    // Previews
    jQuery('#preview_desktop').on('click',function(e){
      jQuery("#preview-wrapper").removeClass('phone-wireframe');
      jQuery('#preview').contents().find('#tubular-container,#big-video-wrap').show();
      jQuery('#preview').animate({
        width: "100%",
        height: "100%",
        'padding-top': "0px"
    }, 500); 
    });

    jQuery('#preview_mobile').on('click',function(e){
      jQuery("#preview-wrapper").addClass('phone-wireframe');
      jQuery('#preview').contents().find('#tubular-container,#big-video-wrap').hide();
      jQuery('#preview').animate({
        width: "329px",
        height: "680px",
        'padding-top': "94px"
    }, 500); 
  });
  
   jQuery('#refresh_page').on('click',function(e){
      jQuery("#preview").attr('src',preview_url)
  });

	// Tooltips
	jQuery('[data-toggle="tooltip"]').tooltip();

	// Goto top of collapse
    jQuery('#accordion').on('shown.bs.collapse', function (e) {
        if(e.target.id != ''){
        var offset = jQuery('#accordion > .panel.panel-default > .panel-collapse.in').offset();
        if(offset) {
            //jQuery('#seed-cspv5-sidebar').scrollTop(0);
            // jQuery('#seed-cspv5-sidebar').animate({
            //     scrollTop: 0
            // }, 100); 
        }
        }
    }); 

	// Image Preview Delete
	jQuery('.img-preview .fa').click(function() {
	    jQuery(this).prev().prop('src',blank_gif);
	    jQuery(this).parent().parent().find("input:text,input:hidden").val('');
	    jQuery(this).parent().fadeOut();
	    save_page();
	});

	// Save Page Events
	jQuery('#footer_text_color,#recaptcha_site_key,#url,#publish_method,#headline_color,#headline_line_height,#text_color,#text_size,#text_line_height,#container_color,#form_color,.note-editable,#socialprofile_color,#contactform_color,#background_color,#background_overlay,#footer_credit_text, #footer_credit_link, #footer_affiliate_link, #headline, #privacy_policy, #privacy_policy_link_text,#thankyou_msg,#cf_confirmation_msg,#tweet_text,#seo_title, #seo_description, #ga_analytics,#txt_subscribe_button,#txt_email_field,#txt_name_field,#txt_already_subscribed_msg,#txt_invalid_email_msg,#txt_invalid_name_msg,#txt_stats_referral_stats,#txt_stats_referral_url,#txt_stats_referral_clicks,#txt_stats_referral_subscribers').on('blur',function(e){
	    save_page(false);
	});


	jQuery('#recaptcha_secret_key,#typekit_id,#header_scripts,#footer_scripts,#conversion_scripts,#custom_css,input[id^="social_profiles"],#bg_video_url,#progress_bar_start_date,#progress_bar_end_date,#countdown_date,#countdown_format,#txt_countdown_days,#txt_countdown_day,#txt_countdown_hours,#txt_countdown_hour,#txt_countdown_minutes, #txt_countdown_minute,#txt_countdown_days,#txt_countdown_day,#countdown_timezone, #txt_contact_us,#txt_prize_level_more, #txt_contact_form_email, #txt_contact_form_msg, #txt_contact_form_send, #txt_contact_form_error ').on('blur',function(e){
	    save_page();
	});

	jQuery("#enable_reflink,#credit_type,#credit_position,#enable_fitvid,#enable_retinajs,#enable_invis_recaptcha,#enable_recaptcha,#enable_wp_head_footer,#bg_video_audio, #bg_video_loop,#display_name,#display_optin_confirm,#bg_slideshow_slide_transition,#progressbar_effect,#container_flat").change(function() {
	    save_page();
	});

	jQuery("#rp_return_user,#enable_prize_levels,#enable_fraud_detection,#enable_background_overlay,#countdown_launch,#require_name,#container_radius_slider,#progressbar_percentage_slider,#publish_method").change(function() {
	    save_page(false);
	});

	// Global Slider Save
    jQuery(document).on('mouseup','.noUi-handle',function(e) {
        var el = jQuery(this).parents('#bg_slideshow_slide_speed_slider');
        var id = jQuery(el).attr('id');
        if(id == 'bg_slideshow_slide_speed_slider'){
    	   save_page();
        }else{
           save_page(false); 
        }
	});

    //Show hide adv fields
    jQuery("#show_hide_adv_fields").change(function() {
    if(this.checked){
        jQuery(".adv").fadeOut();
    }else{
        jQuery(".adv").fadeIn();
    }

});




}); // end doc ready

// switchery
var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery'));
// Success color: #10CFBD
elems.forEach(function(html) {
  var switchery = new Switchery(html, {color: '#0085BA', size: 'small'});
});

    // Headline Font
    function set_headline_font_extras(el){
        if(jQuery(el).val() == 0){
            // jQuery('#preview').contents().find('#cspio-headline').css('font-family','inherit');
            // jQuery('#preview').contents().find('#cspio-headline').css('font-weight','inherit');
            // jQuery('#preview').contents().find('#cspio-headline').css('font-style','inherit');
            jQuery('#headline_subset, #headline_weight').parent().fadeOut();
            return false;
        }

        if(jQuery(el).val().indexOf(',') == -1){

             font_name = jQuery(el).val().replace(/\+/g, " ").replace(/\'/g, "");
             //console.log(font_name);
             font = google_fonts[font_name];
             jQuery('#headline_weight, #headline_subset').parent().fadeIn();
            //console.log(font);


            // load weights and style
            jQuery('#headline_weight').find('option').remove();
            //jQuery('#headline_weight').append('<option value="">Default</option>');
            //onsole.log(font);
            jQuery.each(font.variants,function(key, value) 
            {
                jQuery('#headline_weight').append('<option value=' + value.id + '>' + value.name + '</option>');
            });

            
            if(headline_weight != ''){
               jQuery('#headline_weight').val(headline_weight); 
            }else{
                if(jQuery("#headline_weight option[value='400']").length > 0){
                jQuery('#headline_weight').val('400'); 
                }
            }   

            // Load variants
            jQuery('#headline_subset').find('option').remove();
            jQuery('#headline_subset').append('<option value="">Default</option>');
            jQuery.each(font.subsets,function(key, value) 
            {
                jQuery('#headline_subset').append('<option value=' + value.id + '>' + value.name + '</option>');
            });

            if(headline_subset != ''){
                jQuery('#headline_subset').val(headline_subset);  
            }else{
                jQuery('#headline_subset').val('');  
            }
             
        }else{
            // Load default weights
            jQuery('#headline_weight').parent().fadeIn();
            jQuery('#headline_weight').find('option').remove();
            jQuery('#headline_weight').append('<option value="400">Normal 400</option>');
            jQuery('#headline_weight').append('<option value="700">Bold 700</option>');
            jQuery('#headline_weight').append('<option value="400italic">Normal 400 Italic</option>');
            jQuery('#headline_weight').append('<option value="700italic">Bold 700 Italic</option>');
            jQuery('#headline_weight').val('400');

            jQuery('#headline_subset').parent().fadeOut();
        }
    }

    jQuery(function () {
    set_headline_font_extras(jQuery('#headline_font'));
    //jQuery('#headline_weight').val('400');
    //jQuery('#headline_subset').val('');
    });

    jQuery('#headline_font').on("change", function (e) { 
        el =jQuery('#headline_font');
        if(jQuery(el).val() == 0){

            jQuery('#preview').contents().find('#cspio-headline').css('font-family','inherit');
            jQuery('#preview').contents().find('#cspio-headline').css('font-weight','inherit');
            jQuery('#preview').contents().find('#cspio-headline').css('font-style','inherit');
            jQuery('#headline_subset, #headline_weight').parent().fadeOut();

        }else{
        set_headline_font_extras(el);

        // Show Preview
        if(jQuery(el).val().indexOf(',') === -1){
        jQuery(".gf-headline").remove();
        url = 'https://fonts.googleapis.com/css?family='+jQuery(el).val().replace(/\'/g, "").replace(/\s/g, "+")+':'+jQuery('#headline_weight').val()+'&subset='+jQuery('#headline_subset').val();
        jQuery('#preview').contents().find('head').append('<link class="gf-headline" rel="stylesheet" href="'+url+'" type="text/css" />');
        }

        jQuery('#preview').contents().find('#cspio-headline').css('font-family',jQuery(el).val());


        jQuery('#preview').contents().find('#cspio-headline').css('font-weight',parseInt(jQuery('#headline_weight').val()));

        style = jQuery('#headline_weight').val().replace(/[0-9]/g, '');
        if(style != ""){
            jQuery('#preview').contents().find('#cspio-headline').css('font-style',style);
        }else{
            jQuery('#preview').contents().find('#cspio-headline').css('font-style','normal');
        }
        }

        //Save
        save_page(false);
        
    });

    jQuery('#headline_weight').on("change", function (e) { 
        el =jQuery('#headline_font');
        if(jQuery(el).val().indexOf(',') === -1){
        jQuery(".gf-headline").remove();
        url = 'https://fonts.googleapis.com/css?family='+jQuery(el).val().replace(/\'/g, "").replace(/\s/g, "+")+':'+jQuery('#headline_weight').val()+'&subset='+jQuery('#headline_subset').val();
        jQuery('#preview').contents().find('head').append('<link class="gf-headline" rel="stylesheet" href="'+url+'" type="text/css" />');
        }

        jQuery('#preview').contents().find('#cspio-headline').css('font-weight',parseInt(jQuery('#headline_weight').val()));

        style = jQuery('#headline_weight').val().replace(/[0-9]/g, '');
        if(style != ""){
            jQuery('#preview').contents().find('#cspio-headline').css('font-style',style);
        }else{
            jQuery('#preview').contents().find('#cspio-headline').css('font-style','normal');
        }

        //Save
        save_page(false);
    });

    jQuery('#headline_subset').on("change", function (e) { 
        el =jQuery('#headline_font');
        if(jQuery(el).val().indexOf(',') === -1){
        jQuery(".gf-headline").remove();
        url = 'https://fonts.googleapis.com/css?family='+jQuery(el).val().replace(/\'/g, "").replace(/\s/g, "+")+':'+jQuery('#headline_weight').val()+'&subset='+jQuery('#headline_subset').val();
        jQuery('#preview').contents().find('head').append('<link class="gf-headline" rel="stylesheet" href="'+url+'" type="text/css" />');
        }
    //Save
        save_page(false);
    });

    jQuery('#headline_font').on("select2-highlight", function (e) { 
        //console.log('fire');
    });


    jQuery(document).ready(function() {
      jQuery("#headline_size_slider").noUiSlider({
            start: headline_size,
            connect: "lower",
            step: 1,
            range: {
                'min': 10,
                'max': 200
            },
            format: wNumb({
                decimals: 0
            })
        });

        jQuery("#headline_size_slider").Link('lower').to('-inline-<div class="tooltip fade top in" style="top: -33px;left: -7px;opacity: 0.7;"></div>', function(value) {
            // The tooltip HTML is 'this', so additional
            // markup can be inserted here.
            //console.log(value);
            jQuery(this).html(
                '<div class="tooltip-inner">' +
                '<span>' + value + 'px</span>' +
                '</div>'
            );
            jQuery('#headline_size').val(value);
            jQuery('#preview').contents().find('#cspio-headline').css('font-size',value + 'px');

        });

           jQuery("#headline_line_height_slider").noUiSlider({
            start: headline_line_height,
            connect: "lower",
            step: 0.01,
            range: {
                'min': 0.5,
                'max': 2
            }
        });

            

        jQuery("#headline_line_height_slider").Link('lower').to('-inline-<div class="tooltip fade top in" style="top: -33px;left: -7px;opacity: 0.7;"></div>', function(value) {
            // The tooltip HTML is 'this', so additional
            // markup can be inserted here.
            jQuery(this).html(
                '<div class="tooltip-inner">' +
                '<span>' + value + 'em</span>' +
                '</div>'
            );
            jQuery('#headline_line_height').val(value);
            jQuery('#preview').contents().find('#cspio-headline').css('line-height',value + 'em');

        });
    });
        


// Text Font

    function set_text_font_extras(el){
        if(jQuery(el).val().indexOf(',') == -1){
             font_name = jQuery(el).val().replace(/\+/g, " ").replace(/\'/g, "");
             //console.log(font_name);
             font = google_fonts[font_name];
             jQuery('#text_weight, #text_subset').parent().fadeIn();
            //console.log(font);


            // load weights and style
            jQuery('#text_weight').find('option').remove();
            //jQuery('#text_weight').append('<option value="">Default</option>');
            //onsole.log(font);
            jQuery.each(font.variants,function(key, value) 
            {
                jQuery('#text_weight').append('<option value=' + value.id + '>' + value.name + '</option>');
            });

            
            if(text_weight != ''){
                jQuery('#text_weight').val(text_weight);
            }else{
                if(jQuery("#text_weight option[value='400']").length > 0){
                jQuery('#text_weight').val('400'); 
                }
            }
                

            // Load variants
            jQuery('#text_subset').find('option').remove();
            jQuery('#text_subset').append('<option value="">Default</option>');
            jQuery.each(font.subsets,function(key, value) 
            {
                jQuery('#text_subset').append('<option value=' + value.id + '>' + value.name + '</option>');
            });

            if(text_subset != ''){
                jQuery('#text_subset').val(text_subset);  
            }else{
                jQuery('#text_subset').val('');  
            }

        }else{
            // Load default weights
            jQuery('#text_weight').find('option').remove();
            jQuery('#text_weight').append('<option value="400">Normal 400</option>');
            jQuery('#text_weight').append('<option value="700">Bold 700</option>');
            jQuery('#text_weight').append('<option value="400italic">Normal 400 Italic</option>');
            jQuery('#text_weight').append('<option value="700italic">Bold 700 Italic</option>');
            jQuery('#text_weight').val('400');

            jQuery('#text_subset').parent().fadeOut();
        }
    }

    jQuery(function () {
    set_text_font_extras(jQuery('#text_font'));
    // jQuery('#text_weight').val('400');
    // jQuery('#text_subset').val('');
    });

    jQuery('#text_font').on("change", function (e) { 
        el =jQuery('#text_font');

        set_text_font_extras(el);

        // Show Preview
        if(jQuery(el).val().indexOf(',') === -1){
        jQuery('#preview').contents().find('.gf-text').remove();
        url = 'https://fonts.googleapis.com/css?family='+jQuery(el).val().replace(/\'/g, "").replace(/\s/g, "+")+':'+jQuery('#text_weight').val()+'&subset='+jQuery('#text_subset').val();
        jQuery('#preview').contents().find('head').append('<link class="gf-text" rel="stylesheet" href="'+url+'" type="text/css" />');
        }


        // Body Font
        jQuery('#preview').contents().find('body, p').css('font-family',jQuery(el).val());

        // Placeholder Update
        jQuery('#preview').contents().find('tmp-placeholder-style').remove();
        jQuery('#preview').contents().find('head').append("<style id='tmp-placeholder-style' type='text/css'> .placeholder::-webkit-input-placeholder {font-family:"+jQuery('#text_font').val()+";font-weight:"+jQuery('#text_weight').val().replace(/[a-zA-Z]/g, "")+";font-style:"+jQuery('#text_weight').val().replace(/[0-9]/g, "")+";} </style>");
        jQuery('#preview').contents().find('input').addClass('placeholder');

        jQuery('#preview').contents().find('body, p').css('font-weight',parseInt(jQuery('#text_weight').val()));

        style = jQuery('#text_weight').val().replace(/[0-9]/g, '');
        if(style != ""){
            jQuery('#preview').contents().find('body, p').css('font-style',style);
        }else{
            jQuery('#preview').contents().find('body, p').css('font-style','normal');
        }

        if(jQuery('#headline_font').val() == 0){
            jQuery('#preview').contents().find('#cspio-headline').css('font-family',jQuery(el).val());
            jQuery('#preview').contents().find('#cspio-headline').css('font-weight',parseInt(jQuery('#text_weight').val()));
            jQuery('#preview').contents().find('#cspio-headline').css('font-style',style);
        }

        if(jQuery('#button_font').val() == 0){
            jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-family',jQuery(el).val());
            jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-weight',parseInt(jQuery('#text_weight').val()));
            jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-style',style);
        }

        //Save
        save_page(false);
        
    });

    jQuery('#text_weight').on("change", function (e) { 
        el =jQuery('#text_font');
        if(jQuery(el).val().indexOf(',') === -1){
        jQuery(".gf-text").remove();
        url = 'https://fonts.googleapis.com/css?family='+jQuery(el).val().replace(/\'/g, "").replace(/\s/g, "+")+':'+jQuery('#text_weight').val()+'&subset='+jQuery('#text_subset').val();
        jQuery('#preview').contents().find('head').append('<link class="gf-text" rel="stylesheet" href="'+url+'" type="text/css" />');
        }

        jQuery('#preview').contents().find('body, p').css('font-weight',parseInt(jQuery('#text_weight').val()));
        style = jQuery('#text_weight').val().replace(/[0-9]/g, '');
        if(style == ""){
            style = 'normal'
        }
        jQuery('#preview').contents().find('body, p').css('font-style',style);
        
        // Placeholder Update
        jQuery('#preview').contents().find('tmp-placeholder-style').remove();
        jQuery('#preview').contents().find('head').append("<style id='tmp-placeholder-style' type='text/css'> .placeholder::-webkit-input-placeholder {font-family:"+jQuery('#text_font').val()+";font-weight:"+jQuery('#text_weight').val().replace(/[a-zA-Z]/g, "")+";font-style:"+style+";} </style>");
        jQuery('#preview').contents().find('input').addClass('placeholder');



        if(jQuery('#headline_font').val() == 0){
            jQuery('#preview').contents().find('#cspio-headline').css('font-family',jQuery(el).val());
            jQuery('#preview').contents().find('#cspio-headline').css('font-weight',parseInt(jQuery('#text_weight').val()));
            jQuery('#preview').contents().find('#cspio-headline').css('font-style',style);
        }

        if(jQuery('#button_font').val() == 0){
            jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-family',jQuery(el).val());
            jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-weight',parseInt(jQuery('#text_weight').val()));
            jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-style',style);
        }

        //Save
        save_page(false);
    });

    jQuery('#text_subset').on("change", function (e) { 
        el =jQuery('#text_font');
        if(jQuery(el).val().indexOf(',') === -1){
        jQuery(".gf-text").remove();
        url = 'https://fonts.googleapis.com/css?family='+jQuery(el).val().replace(/\'/g, "").replace(/\s/g, "+")+':'+jQuery('#text_weight').val()+'&subset='+jQuery('#text_subset').val();
        jQuery('#preview').contents().find('head').append('<link class="gf-text" rel="stylesheet" href="'+url+'" type="text/css" />');
        }
    //Save
        save_page(false);
    });

jQuery(document).ready(function() {

      jQuery("#text_size_slider").noUiSlider({
            start: text_size,
            connect: "lower",
            step: 1,
            range: {
                'min': 10,
                'max': 100
            },
            format: wNumb({
                decimals: 0
            })
        });

        jQuery("#text_size_slider").Link('lower').to('-inline-<div class="tooltip fade top in" style="top: -33px;left: -7px;opacity: 0.7;"></div>', function(value) {
            // The tooltip HTML is 'this', so additional
            // markup can be inserted here.
            jQuery(this).html(
                '<div class="tooltip-inner">' +
                '<span>' + value + 'px</span>' +
                '</div>'
            );
            jQuery('#text_size').val(value);
            jQuery('#preview').contents().find('body, p').css('font-size',value + 'px');

        });

           jQuery("#text_line_height_slider").noUiSlider({
            start: text_line_height,
            connect: "lower",
            step: 0.01,
            range: {
                'min': 0.5,
                'max': 2
            }
        });


            

        jQuery("#text_line_height_slider").Link('lower').to('-inline-<div class="tooltip fade top in" style="top: -33px;left: -7px;opacity: 0.7;"></div>', function(value) {
            // The tooltip HTML is 'this', so additional
            // markup can be inserted here.
            jQuery(this).html(
                '<div class="tooltip-inner">' +
                '<span>' + value + 'em</span>' +
                '</div>'
            );
            jQuery('#text_line_height').val(value);
            jQuery('#preview').contents().find('body, p').css('line-height',value + 'em');

        });

         });


// Button Fonts


    function set_button_font_extras(el){
        if(jQuery(el).val() == 0){
            // jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-family','inherit');
            // jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-weight','inherit');
            // jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-style','inherit');
            jQuery('#button_subset, #button_weight').parent().fadeOut();
            return false;
        }

        if(jQuery(el).val().indexOf(',') == -1){
             font_name = jQuery(el).val().replace(/\+/g, " ").replace(/\'/g, "");
             //console.log(font_name);
             font = google_fonts[font_name];
             jQuery('#button_weight, #button_subset').parent().fadeIn();
            //console.log(font);


            // load weights and style
            jQuery('#button_weight').find('option').remove();
            //jQuery('#button_weight').append('<option value="">Default</option>');
            //onsole.log(font);
            jQuery.each(font.variants,function(key, value) 
            {
                jQuery('#button_weight').append('<option value=' + value.id + '>' + value.name + '</option>');
            });

            
            if(button_weight != ''){
               jQuery('#button_weight').val(button_weight);
            }else{
               if(jQuery("#button_weight option[value='400']").length > 0){
                jQuery('#button_weight').val('400'); 
               }
            }
                
            // Load variants
            jQuery('#button_subset').find('option').remove();
            jQuery('#button_subset').append('<option value="">Default</option>');
            jQuery.each(font.subsets,function(key, value) 
            {
                jQuery('#button_subset').append('<option value=' + value.id + '>' + value.name + '</option>');
            });

            if(button_subset != ''){
                jQuery('#button_subset').val(button_subset);  
            }else{
                jQuery('#button_subset').val('');  
            }
   
        }else{
            // Load default weights
            jQuery('#button_weight').parent().fadeIn();
            jQuery('#button_weight').find('option').remove();
            jQuery('#button_weight').append('<option value="400">Normal 400</option>');
            jQuery('#button_weight').append('<option value="700">Bold 700</option>');
            jQuery('#button_weight').append('<option value="400italic">Normal 400 Italic</option>');
            jQuery('#button_weight').append('<option value="700italic">Bold 700 Italic</option>');
            jQuery('#button_weight').val('400');

            jQuery('#button_subset').parent().fadeOut();
        }
    }

    jQuery(function () {
    set_button_font_extras(jQuery('#button_font'));
    // jQuery('#button_weight').val('400');
    // jQuery('#button_subset').val('');
    });

    jQuery('#button_font').on("change", function (e) { 
        el =jQuery('#button_font');
        if(jQuery(el).val() == 0){
            //console.log('fdfd');

            jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-family','inherit');
            jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-weight','inherit');
            jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-style','inherit');
            jQuery('#button_subset, #button_weight').parent().fadeOut();

        }else{
        set_button_font_extras(el);

        // Show Preview
        if(jQuery(el).val().indexOf(',') === -1){
        jQuery(".gf-button").remove();
        url = 'https://fonts.googleapis.com/css?family='+jQuery(el).val().replace(/\'/g, "").replace(/\s/g, "+")+':'+jQuery('#button_weight').val()+'&subset='+jQuery('#button_subset').val();
        jQuery('#preview').contents().find('head').append('<link class="gf-button" rel="stylesheet" href="'+url+'" type="text/css" />');
        }

        jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-family',jQuery(el).val());


        jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-weight',parseInt(jQuery('#button_weight').val()));

        style = jQuery('#button_weight').val().replace(/[0-9]/g, '');
        if(style != ""){
            jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-style',style);
        }else{
            jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-style','normal');
        }
        }

        //Save
        save_page(false);
        
    });

    jQuery('#button_weight').on("change", function (e) { 
        el =jQuery('#button_font');
        if(jQuery(el).val().indexOf(',') === -1){
        jQuery(".gf-button").remove();
        url = 'https://fonts.googleapis.com/css?family='+jQuery(el).val().replace(/\'/g, "").replace(/\s/g, "+")+':'+jQuery('#button_weight').val()+'&subset='+jQuery('#button_subset').val();
        jQuery('#preview').contents().find('head').append('<link class="gf-button" rel="stylesheet" href="'+url+'" type="text/css" />');
        }

        jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-weight',parseInt(jQuery('#button_weight').val()));

        style = jQuery('#button_weight').val().replace(/[0-9]/g, '');
        if(style != ""){
            jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-style',style);
        }else{
            jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-style','normal');
        }

        //Save
        save_page(false);
    });

    jQuery('#button_subset').on("change", function (e) { 
        el =jQuery('#button_font');
        if(jQuery(el).val().indexOf(',') === -1){
        jQuery(".gf-button").remove();
        url = 'https://fonts.googleapis.com/css?family='+jQuery(el).val().replace(/\'/g, "").replace(/\s/g, "+")+':'+jQuery('#button_weight').val()+'&subset='+jQuery('#button_subset').val();
        jQuery('#preview').contents().find('head').append('<link class="gf-button" rel="stylesheet" href="'+url+'" type="text/css" />');
        }
    //Save
        save_page(false);
    });

    jQuery('#button_font').on("select2-highlight", function (e) { 
        //console.log('fire');
    });



      // jQuery("#button_size_slider").noUiSlider({
      //       start: 14,
      //       connect: "lower",
      //       step: 1,
      //       range: {
      //           'min': 10,
      //           'max': 100
      //       },
      //       format: wNumb({
      //           decimals: 0
      //       })
      //   });

      //   jQuery("#button_size_slider").Link('lower').to('-inline-<div class="tooltip fade top in" style="top: -33px;left: -7px;opacity: 0.7;"></div>', function(value) {
      //       // The tooltip HTML is 'this', so additional
      //       // markup can be inserted here.
      //       jQuery(this).html(
      //           '<div class="tooltip-inner">' +
      //           '<span>' + value + 'px</span>' +
      //           '</div>'
      //       );
      //       jQuery('#button_size').val(value);
      //       jQuery('#preview').contents().find('#cspio-subscribe-btn').css('font-size',value + 'px');

      //   });


      // Disabled unused fields
      jQuery( document ).ready(function($) {
         var disabled_fields = $('#disabled_fields').val();
         $(disabled_fields).off().prop('readonly',true).parents('.form-group').css({'opacity':'0.5','position':'relative'}).prepend( "<small class='seed_cspv5_disabled'>Not available in this theme!</small><br>" );
      });











