 jQuery(function($) {
    
    var photo_array = false, 
    comment_id = false, 
    colspan = (WPDATA.current_page == 'product') ? 2 : 5,
    tr = false,
    tr_edit = false,
    busy = false;
    
    if (WPDATA.current_page == 'product' && $('#add-new-comment').length > 0 && $('#no-comments').length === 0){
          $('#add-new-comment').after('<p class="hide-if-no-js" id="remove-comments"><a class="button" href="#commentstatusdiv">Remove comments</a></p>'); 
          $('#remove-comments').click(function(){
             var r_btn =  $(this).find('a');
             r_btn.html(WPDATA.i18n_please_wait);
            
             var data = {'action': 'a2w_arvi_remove_product_reviews', 'id':WPDATA.product_id}; 
             jQuery.post(ajaxurl, data, function (response) {
                var json = jQuery.parseJSON(response);

                if (json.state == 'ok') { 
                    r_btn.html(WPDATA.i18n_done);
                    location.reload();  
                } 
                else {
                    r_btn.html(WPDATA.i18n_error_occur);
                    console.log('[' + json.state + ']message: ', json.message);
                }
                   
             });     
         
              return false;
          });
      } 
    
  
    function reset_vars(){
          tr_edit.remove();
          tr.show(); 
          
          photo_array = false, 
          comment_id = false,
          tr = false,
          tr_edit = false,
          busy = false;
              
    }
    
    function reset_dialog(){
        if (tr_edit && tr){
            var lostChanges = confirm("Are you sure you want to do this? The review  changes you made will be lost.");
            if ( lostChanges ){
                reset_vars();
                return true;   
            } else return false;
        }
        
        return true;    
    } 
     
    function get_photos(id, f){
         var data = {'action': 'a2w_arvi_get_comment_photos', 'id':id}; 
         jQuery.post(ajaxurl, data, function (response) {
            var json = jQuery.parseJSON(response);

            if (json.state == 'ok') { 
                f(json.photos);    
            } 
            else {
                console.log('[' + json.state + ']message: ', json.message);
                f(false);
            }
               
         });       
    }
    
    function generate_photos_html(photos){
        var html = '<div class="a2w_comment_images">';
        
        for (var i in photos) {
            html +=  
              '<div class="image" style="background-image:url('+photos[i].image+');background-size: contain;"><a href="#" class="delete">Delete</a></div>';         
        }
       
    
    return html += '</div>', html;
    
    }
    
	$( "body" ).on( "click", ".a2w_comment_edit_photo_link > a", function(e) {
        e.preventDefault();
      
        //make sure previous dialog closed
        if (!reset_dialog()) return false;
        
        //prevent dblclick
        if (busy) return false;
        busy = true;
        
        
        
        var a_edit = $(this), orig_text = a_edit.html();
        
        a_edit.html('Please wait...');
        
        var id = $(this).attr('id').split('-')[1];
        
  
           
        comment_id = id
        tr = $(this).parents('tr');
             
        get_photos(id, function(photos){
            tr.hide(); 

            if (photos){
                
                photo_array = photos;
                console.log(photo_array);
                tr.after('<tr id="editphotorow" class="a2w_comment_edit_photo"><td colspan="' + colspan + '" class="colspanchange"><fieldset class="comment-reply"><legend><span class="hidden" style="display: inline;">Edit Photos</span></legend>' +
                '<div id="replycontainer">' + generate_photos_html(photos) + '</div>' + 
                '<p id="replysubmit" class="submit"><a href="#comments-form" class="save button-primary alignright"><span class="savephotobtn">Update Photos</span></a><a href="#comments-form" class="cancel button-secondary alignleft">Cancel</a><span class="waiting spinner"></span><span class="error" style="display:none;"></span></p>' +
                '</fieldset></td></tr>');    
            } else  tr.after('<tr id="editphotorow" class="a2w_comment_edit_photo"><td colspan="' + colspan + '" class="colspanchange">Some error! Try to reload the page.</td</tr>');     
       
            tr_edit = tr.parent().find('#editphotorow');
            
            busy = false;
            a_edit.html(orig_text);
        });
                
    });
    
    $( "body" ).on( "click", '.a2w_comment_edit_photo .savephotobtn', function(e){
        e.preventDefault();
     
        var save_btn = $(this), wait_spinner = save_btn.parent().parent().children('.waiting'), 
        error_el  = save_btn.parent().parent().children('.error'); 
        
        wait_spinner.css('visibility', 'visible');
        
         var data = {'action': 'a2w_arvi_save_comment_photos', 'id':comment_id, 'photos':photo_array}; 
         jQuery.post(ajaxurl, data, function (response) {
            
            var json = jQuery.parseJSON(response);
            if (json){
                wait_spinner.css('visibility', 'hidden');
                 
                if (json.state && json.state == 'ok') { 
                      reset_vars();
                } 
                else {
                    //output error in .error div
                    error_el.text(json.message);
                }
            }   error_el.text('Undefined error. Please try again.');
               
         });       
    });
    
    
    $( "body" ).on( "click", '.a2w_comment_edit_photo a.delete', function(e){
        e.preventDefault();
 
        bg = $(this).closest('.image').css('background-image');
        bg = bg.replace('url(','').replace(')','').replace(/\"/gi, "");
        
        photo_array = photo_array.filter(function(e) { 
            return e.image !== bg 
        })
        console.log(photo_array);
        
        $(this).closest('.image')
            .fadeTo(300,0,function(){
                $(this)
                    .animate({width:0},200,function(){
                        $(this)
                            .remove();
                    });
            });
    });
    
    $( "body" ).on( "click", '.a2w_comment_edit_photo a.cancel', function(e){
        e.preventDefault();
        
        reset_vars();
        
    }); 
    

  });