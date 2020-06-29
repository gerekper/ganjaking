// For Upload Field Type

jQuery(document).ready(function($){
  var _custom_media = true,
      _orig_send_attachment = wp.media.editor.send.attachment;

  $('.upload-button').click(function(e) {
    var send_attachment_bkp = wp.media.editor.send.attachment;
    var button = $(this);
    var id = jQuery(this).prev('input');
    _custom_media = true; 
    wp.media.editor.send.attachment = function(props, attachment){
      var size = props.size;
      var att =attachment.sizes[size];

      //props.size
      if ( _custom_media ) {
        $(id).val(att.url).trigger('change');
      } else {
        return _orig_send_attachment.apply( this, [props, attachment] );
      };
    }

    wp.media.editor.open(button);
    return false;
  });

  $('.add_media').on('click', function(){
    _custom_media = false;
  });
});



var media_uploader = null;

function open_media_uploader_video()
{
    media_uploader = wp.media({
        frame:    "video",
        state:    "video-details",
    });

    media_uploader.on("update", function(){

        var extension = media_uploader.state().media.extension;
        var video_url = media_uploader.state().media.attachment.changed.url;
        var video_icon = media_uploader.state().media.attachment.changed.icon;
        var video_title = media_uploader.state().media.attachment.changed.title;
        var video_desc = media_uploader.state().media.attachment.changed.description;
    });

    media_uploader.open();
}
