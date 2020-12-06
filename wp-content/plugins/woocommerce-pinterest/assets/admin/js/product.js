jQuery(function ($) {

    var pinContainer = $('[data-pinterest-pin-container]');

    /**
     * hide/show pinContainer
     */
    $(document).on('change', '[data-pinterest-pinned]', function () {
        var animateTime = 300;

        if ($(this).is(":checked")) {
            pinContainer.fadeIn(animateTime);
        } else {
            pinContainer.fadeOut(animateTime);
        }
    });

    // Add event listener for featuredImage frame
    $('#set-post-thumbnail').click(function () {
        addSelectListener(wp.media.featuredImage.frame());
    });


    // Add event listener for product_gallery frame
    $(document).on('click', '.add_product_images', function () {
        addSelectListener(wp.media.frames.product_gallery);
    });

    // Add event listener for variable_image frame
    $(document).on('click', '.woocommerce_variation .upload_image_button', function () {
        addSelectListener(wp.media.frames.variable_image);
    });

    /**
     * Call addAttachment function when user select attachment in frame
     */
    function addSelectListener(frame) {
        if (typeof frame !== 'undefined') {
            frame.on('select', function () {
                frame.state().get('selection').forEach(function (attachment) {
                    addAttachment(attachment.toJSON());
                });
            });
        }
    }


    /**
     * Add new item to pinContainer
     *
     */
    function addAttachment(attachment, selected) {

        if ($("[data-pinterest-image-checkbox='" + attachment.id + "']").length) {
            return;
        }

        var $wrapper = $('<div>', {
            'class': 'woo-pinterest-image-wrapper'
        });

        var $button = $('<button>', {
            'class': 'check',
            'type': 'button',
            'tabindex': '-1'
        });

        var $iconSpan = $('<span>', {
            'class': 'media-modal-icon'
        });

        var $screenReaderSpan = $('<span>', {
            'class': 'screen-reader-text'
        }).text('Deselect');

        var $input = $('<input>', {
            type: "checkbox",
            name: 'woocommerce_pinterest_images[]',
            'data-pinterest-image-checkbox': attachment.id,
            value: attachment.id
        });

        var $image = $('<img>', {
            'width': 150,
            'height': 150,
            'src': attachment.url
        });

        $button.append($iconSpan).append($screenReaderSpan);
        $wrapper.append($button).append($image).append($input);
        pinContainer.prepend($wrapper).prepend(' ');

        if (typeof selected !== 'undefined') {
            $input.prop('checked', true).trigger('change');
        }
    }


    $(document).on('click', '[data-pinterest-image-toggle-all]', function () {

        var state = !!+$(this).attr('data-pinterest-image-toggle-all');

        $('[data-pinterest-image-checkbox]').each(function () {
            $(this).prop('checked', state);
            $(this).trigger('change');
        });
    });


    var customImageFrame;

    jQuery('[data-pinterest-custom-image-upload]').on('click', function (event) {

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if (customImageFrame) {
            customImageFrame.open();
            return;
        }

        // Create the media frame.
        customImageFrame = wp.media.frames.file_frame = wp.media({
            title: 'Select a image to pin',
            button: {
                text: 'Add this image',
            },
            multiple: true
        });

        customImageFrame.on('select', function () {
            customImageFrame.state().get('selection').models.forEach(function (attachment) {
                addAttachment(attachment.attributes, true);
            });
        });

        customImageFrame.open();
    });

    $(document).on('click', '.woo-pinterest-image-wrapper', function () {
        var $checkbox = $(this).find('[data-pinterest-image-checkbox]');
        $checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
    });

    // Toggle checked block for images
    $(document).on('change', '[data-pinterest-image-checkbox]', function (e) {
        var $checkbox = $(this);
        var $imageWrapper = $checkbox.parent('.woo-pinterest-image-wrapper');
        $imageWrapper.toggleClass('checked', $checkbox.is(':checked'));
    });

    // Toggle variation pin description field
    $(document).on('change', '[name^=variable_is_pin_description]', function () {
        if ($(this).attr('checked') === 'checked') {
            $(this).closest('.woocommerce_variation').find('.show_if_variation_pin_description').show();
        } else {
            $(this).closest('.woocommerce_variation').find('.show_if_variation_pin_description').hide();
        }
    });

    $('#woocommerce-pinterest-product-boards-select').selectWoo({
        width: 'auto'
    });

    //todo: fix code duplication from settings.js
    //Insert variable into input
    $(document).on('click', 'button[data-var]', function () {
        var button = $(this);

        insertText(button.data('field'), button.data('var'));
    });

    //Insert text into textarea
    function insertText(fieldSelector, text) {

        var field = document.querySelector(fieldSelector);

        if (!field) {
            return;
        }

        field.focus();
        var value = field.value;
        var position = field.selectionStart;
        field.value = value.slice(0, position) + text + value.slice(position);
        position += text.length;
        field.setSelectionRange(position, position);
    }

});
