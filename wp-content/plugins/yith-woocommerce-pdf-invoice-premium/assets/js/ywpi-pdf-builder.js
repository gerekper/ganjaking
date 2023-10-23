/**
 * Javascript functions to PDF Templates panel
 *
 * @package YITH\PDF_Invoice\PDF_Builder\JS
 */
jQuery(function($) {
  'use strict';
  var blockParams = {
    message: null,
    overlayCSS: {background: '#fff', opacity: 0.7},
    ignoreIfBlocked: true,
  };

  /** PDF Templates Editor **/
  if ($('#yith_ywpi_pdf_template_title').length) {
    $(document).
        on('click', '.components-toggle-group-control-option', function(e) {
          window.onbeforeunload = null;
          e.preventDefault();
          return true;
        });

    $(document).
        on('click',
            'button.component-item, .components-item-group, .components-color-palette__custom-color',
            function(e) {
              window.onbeforeunload = null;
              e.preventDefault();
            });
  }

  $(document).
      on('click', '.block-editor-post-preview__button-toggle', function(e) {
        e.preventDefault();
        $.ajax({
          type: 'POST',
          url: ywpi_pdf_template.ajaxurl,
          data: {
            action: 'ywpi_template_pdf_preview',
            pdf_template_preview: $('#post_ID').val(),
            preview_product : ywpi_pdf_template.preview_products,
            security: ywpi_pdf_template.preview_template_nonce
          },
          success: function(response) {

            if (response && response.pdf) {
              window.open( response.pdf, '_blank');
            }
          },
        });
      });

  $('form#post').on('submit', function(e){
    e.preventDefault();
  });
});

wp.domReady(() => {
  if (wp.blocks) {
    wp.blocks.unregisterBlockStyle('core/image', 'default');
    wp.blocks.unregisterBlockStyle('core/image', 'rounded');
    wp.blocks.unregisterBlockStyle('core/separator', 'wide');
    wp.blocks.unregisterBlockStyle('core/separator', 'dots');
  }
});