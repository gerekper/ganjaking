( function( tinymce ) {
tinymce.PluginManager.add('youtube', function(editor, url) {
  // Add a button that opens a window
  editor.addButton('youtube', {
    text: false,
    icon: 'media',
    tooltip: 'Insert Youtube video',
    onclick: function() {
      // Open window
      editor.windowManager.open({
        title: 'Youtube video',
        body: [
          {type: 'textbox', name: 'title', label: 'URL of Youtube video'}
        ],
        onsubmit: function(e) {
          // Insert content when the window form is submitted
          var videoEmbed = e.data.title.split('watch?v='),
      		  videoEmbedUrl = '';
          if(videoEmbed.length == 2) {
          	videoEmbedUrl = 'https://www.youtube.com/embed/' + videoEmbed[1] + '?rel=0';
          }
          editor.insertContent('[youtube]' + videoEmbedUrl + '[/youtube]');
        }
      });
    }
  });

  // Adds a menu item to the tools menu
  editor.addMenuItem('youtube', {
    text: 'youtube plugin',
    context: 'tools',
    onclick: function() {
      // Open window with a specific url
      editor.windowManager.open({
        title: 'TinyMCE site',
        url: 'https://www.tinymce.com',
        width: 800,
        height: 600,
        buttons: [{
          text: 'Close',
          onclick: 'close'
        }]
      });
    }
  });

  return {
    getMetadata: function () {
      return  {
        title: "youtube plugin",
        url: "http://youtubeplugindocsurl.com"
      };
    }
  };
});

} )( window.tinymce );