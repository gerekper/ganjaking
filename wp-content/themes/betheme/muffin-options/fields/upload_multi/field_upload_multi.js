(function($) {

  /* globals jQuery, wp */

  "use strict";

  var MfnUploadMulti = (function() {

    /**
     * Global variables
     */

    var multiFileFrame, multiFileFrameOpen, multiFileFrameSelect, handle,
      selector = '.mfnf-upload.multi';

    /**
     * Attach events to buttons. Runs whole script.
     */

    function init() {

      openMediaGallery();
      attachRemoveAction();
      attachRemoveAllAction();

      uiSortable();

    }

    /**
     * UI Sortable Init
     */

    function uiSortable() {

      $('body').on('mouseenter', '.mfnf-upload.multi .gallery-container', function(e) {

        var el = $(this),
          parent = el.closest(selector);

        if ($('.image-container', el).length) {

          // init sortable

          if (!el.hasClass('ui-sortable')) {
            el.sortable({
              opacity: 0.9,
              update: function() {
                fillInput(parent, findAllIDs(parent));
              }
            });
          }

          // enable inactive sortable

          if (el.hasClass('ui-sortable-disabled')) {
            el.sortable('enable');
          }
        }

      });

    }

    /**
     * Click | Add
     */

    function openMediaGallery() {

      $('body').on('click', '.mfnf-upload.multi .upload-add', function(event) {

        event.preventDefault();

        handle = this;

        // Create the media frame

        multiFileFrame = wp.media.frames.mfnGallery = wp.media({
          title: $(this).data('button'),
          multiple: 'add',
          library: {
            type: 'image',
          },
          button: {
            text: $(this).data('button')
          }
        });

        // Attach hooks to the events

        multiFileFrame.on('open', multiFileFrameOpen);
        multiFileFrame.on('select', multiFileFrameSelect);

        multiFileFrame.open();

      });

    }

    /**
     * WP Media Frame | Open
     */

    multiFileFrameOpen = function() {

      var parent = handle.closest(selector),
        library = multiFileFrame.state().get('selection'),
        images = $('.upload-input', parent).val(),
        imageIDs;

      if (!images) {
        return true;
      }

      imageIDs = images.split(',');

      imageIDs.forEach(function(id) {
        var attachment = wp.media.attachment(id);
        attachment.fetch();
        library.add(attachment ? [attachment] : []);
      });
    };

    /**
     * WP Media Frame | Select
     */

    multiFileFrameSelect = function() {

      var parent = handle.closest(selector),
        gallery = $('.gallery-container', parent),
        library = multiFileFrame.state().get('selection'),
        imageURLs = [],
        imageIDs = [],
        imageURL, outputHTML, joinedIDs;

      gallery.html('');

      library.map(function(image) {

        image = image.toJSON();
        imageURLs.push(image.url);
        imageIDs.push(image.id);

        if (image.sizes.thumbnail) {
          imageURL = image.sizes.thumbnail.url;
        } else {
          imageURL = image.url;
        }

        outputHTML = '<div class="image-container">' +
          '<img class="screenshot image" src="' + imageURL + '" data-pic-id="' + image.id + '" />' +
          '<a href="#" class="upload-remove single dashicons dashicons-no"></a>' +
          '</div>';

        gallery.append(outputHTML);
      });

      joinedIDs = imageIDs.join(',').replace(/^,*/, '');
      if (joinedIDs.length !== 0) {
        $('a.upload-remove.all', parent).fadeIn(300);
      }

      fillInput(parent, joinedIDs);

      attachRemoveAction();
    };

    /**
     * Click | Remove single
     */

    function attachRemoveAction() {

      $('body').on('click', '.mfnf-upload.multi .upload-remove.single', function(event) {

        event.preventDefault();

        var parent = $(this).closest(selector),
          joinedIDs;

        $(this).closest('.image-container').remove();

        joinedIDs = findAllIDs(parent);
        if (joinedIDs === '') {
          $('a.upload-remove.all', parent).fadeOut(300);
        }

        fillInput(parent, joinedIDs);

      });

    }

    /**
     * Click | Remove all
     */

    function attachRemoveAllAction() {

      $('body').on('click', '.mfnf-upload.multi .upload-remove.all', function(event) {

        event.preventDefault();

        var parent = $(this).closest(selector);

        $(this).fadeOut(300);

        $('input', parent).val('');
        $('.gallery-container', parent).html('');

      });

    }

    /**
     * Helper method. Find all IDs of added images.
     * @method findAllIDs
     * @return {String}		joined ids separated by `;`
     */

    function findAllIDs(parent) {
      var imageIDs = [],
        id;

      $('.gallery-container img.screenshot', parent).each(function() {
        id = $(this).attr('data-pic-id');
        imageIDs.push(id);
      });

      return imageIDs.join(",");
    }

    /**
     * Helper method. Set the value of image gallery input.
     * @method fillInput
     * @param  {String} joinedIDs - string to be set into input
     */

    function fillInput(parent, joinedIDs) {

      $('.upload-input', parent)
        .val(joinedIDs)
        .trigger('change');
    }

    /**
     * Return
     * Method to start the closure
     */

    return {
      init: init
    };

  })(jQuery);

  /**
   * $(document).ready
   * Specify a function to execute when the DOM is fully loaded.
   */

  $(function() {
    MfnUploadMulti.init();
  });

})(jQuery);
