jQuery(document).ready(function() {
  "use strict";

  detectHash(EnergyPlusGlobal._admin_url + "comment.php?action=editcomment&c=HASH");

  jQuery('body').on('click',".__A__Replies", function() {
    jQuery(this).hide();
    jQuery('.__A__Reply_' + jQuery(this).attr('data-id')).show();
  });

  jQuery('body').on('click','.__A__AjaxButton', function(e) {
    e.stopPropagation();
    var t = jQuery('#item_'+jQuery(this).attr('data-id'));
    var dataid = jQuery(this).attr('data-id');

    EnergyPlusAjax();

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      action: "energyplus_ajax",
      segment: 'comments',
      do: jQuery(this).attr('data-do'),
      id: dataid,
      state: jQuery(this).attr('data-state'),
    }, function(r) {
      if (1 === r.status) {
        if ('untrash' === r.state || 'unspam' === r.state) {
          jQuery('#__A__AjaxResponse_'+r.id).remove();
        } else if ('approve' === r.state) {
          jQuery(".__A__CommentStatus > span", t).removeClass('badge-danger').addClass('badge-success').text(EnergyPlusComments.approved);
          jQuery(".__A__CommentStatusButton", t).text(EnergyPlusComments.unapprove).removeClass('__A__CommentStatusButton_Unapprove').addClass('__A__CommentStatusButton_Approve').attr('data-state', 'unapprove');
        } else if ('unapprove' === r.state) {
          jQuery(".__A__CommentStatus > span", t).removeClass('badge-secondary').addClass('badge-danger').text(EnergyPlusComments.unapproved);
          jQuery(".__A__CommentStatusButton", t).text(EnergyPlusComments.approve).removeClass('__A__CommentStatusButton_Unapprove').addClass('__A__CommentStatusButton_Approve').attr('data-state', 'approve');
        } else {
          t.append('<div id="__A__AjaxResponse_'+r.id+'" class="__A__AjaxResponse __A__AjaxResponse_Comments d-flex justify-content-center align-items-center">'+r.message+'</div>');
        }

        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      } else {
        EnergyPlusAjax('error', r.error);
      }
    }, 'json');

  });


  jQuery('body').on('click',".__A__Bulk_Do", function() {
    var sList = "";

    jQuery('.__A__Checkbox').each(function () {

      var sThisVal = jQuery(this).attr('data-id');

      if (this.checked) {
        sList += (sList === "" ? sThisVal : "," + sThisVal);
      }
    });

    EnergyPlusAjax();

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      action: "energyplus_ajax",
      segment: 'comments',
      do: 'bulk',
      id: sList,
      state: jQuery(this).attr('data-do')
    }, function(r) {
      if (1 === r.status) {
        jQuery.each(r.id, function(i, item) {
          var t = jQuery('#item_' + item.id);
          if ('untrash' === item.state || 'unspam' === item.state) {
            jQuery('#__A__AjaxResponse_'+item.id).remove();
          } else if ('approve' === item.state) {
            jQuery(".__A__CommentStatus > span", t).removeClass('badge-danger').addClass('badge-success').text(EnergyPlusComments.approved);
            jQuery(".__A__CommentStatusButton", t).text(EnergyPlusComments.unapprove).attr('data-state', 'unapprove');
          } else if ('unapprove' === item.state) {
            jQuery(".__A__CommentStatus > span", t).removeClass('badge-success').addClass('badge-danger').text(EnergyPlusComments.unapproved);
            jQuery(".__A__CommentStatusButton", t).text(EnergyPlusComments.approve).attr('data-state', 'approve');
          } else if ('trash' === item.state) {
            t.animate({opacity: 0.2}, 600, function() { t.remove(); });
          }
        });
        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      } else {
        EnergyPlusAjax('error', r.error);
      }
    }, 'json');

  });


  jQuery('body').on('click',".__A__Checkbox", function() {

    if ( 0 === jQuery(".__A__Checkbox:checked").length )  {
      jQuery(".__A__Bulk").hide();

    } else {
      jQuery(".__A__Bulk").show();
    }
    if (this.checked) {
      jQuery(this).parent().parent().addClass('__A__ItemChecked');
    } else {
      jQuery(this).parent().parent().removeClass('__A__ItemChecked');
    }

    if ( 0 < jQuery(".__A__Checkbox[data-state=s1]:checked").length ) {
      jQuery(".__A__Bulk_Unapprove").show();
    } else {
      jQuery(".__A__Bulk_Unapprove").hide();
    }
    if ( 0 < jQuery(".__A__Checkbox[data-state=s0]:checked").length ) {
      jQuery(".__A__Bulk_Approve").show();
    } else {
      jQuery(".__A__Bulk_Approve").hide();
    }

    jQuery(".__A__Checkbox").addClass('__A__NoHide');
  });

  jQuery(document).on('click', ".__A__CheckAll" , function() {
    if (this.checked) {
      jQuery(".__A__Standart").hide();
      jQuery(".__A__Bulk").show();
    } else {
      jQuery(".__A__Bulk").hide();
      jQuery(".__A__Standart").show();
    }

    jQuery(".__A__Checkbox").addClass('__A__NoHide').prop('checked', this.checked);
    jQuery(".__A__CheckAll").prop('checked', this.checked);


    if ( 0 < jQuery(".__A__Checkbox[data-state=publish]:checked").length ) {
      jQuery(".__A__Bulk_private").show();
    } else {
      jQuery(".__A__Bulk_private").hide();
    }

    if ( 0 < jQuery(".__A__Checkbox[data-state=private]:checked").length ) {
      jQuery(".__A__Bulk_publish").show();
    } else {
      jQuery(".__A__Bulk_publish").hide();
    }
  });
});
