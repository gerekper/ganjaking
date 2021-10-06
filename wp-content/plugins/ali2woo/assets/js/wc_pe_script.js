var a2w_isExternal = function (url) {
    var checkDomain = function (url) {
        if (url.indexOf('//') === 0) {
            url = location.protocol + url;
        }
        return url.toLowerCase().replace(/([a-z])?:\/\//, '$1').split('/')[0];
    };
    return ((url.indexOf(':') > -1 || url.indexOf('//') > -1) && checkDomain(location.href) !== checkDomain(url));
};

(function ($, window, document, undefined) {
    $(function () {
        'use strict';

        var supportingFileAPI = !!(window.File && window.FileList && window.FileReader);
        var activeObjectId;

        // Image editor
        var imageEditor = new tui.ImageEditor('.tui-image-editor', {
            cssMaxWidth: 900,
            cssMaxHeight: 700,
            selectionStyle: {
                cornerSize: 20,
                rotatingPointOffset: 70
            }
        });
        var palette = [
            ["#000", "#444", "#666", "#999", "#ccc", "#eee", "#f3f3f3", "#fff"],
            ["#f00", "#f90", "#ff0", "#0f0", "#0ff", "#00f", "#90f", "#f0f"],
            ["#f4cccc", "#fce5cd", "#fff2cc", "#d9ead3", "#d0e0e3", "#cfe2f3", "#d9d2e9", "#ead1dc"],
            ["#ea9999", "#f9cb9c", "#ffe599", "#b6d7a8", "#a2c4c9", "#9fc5e8", "#b4a7d6", "#d5a6bd"],
            ["#e06666", "#f6b26b", "#ffd966", "#93c47d", "#76a5af", "#6fa8dc", "#8e7cc3", "#c27ba0"],
            ["#c00", "#e69138", "#f1c232", "#6aa84f", "#45818e", "#3d85c6", "#674ea7", "#a64d79"],
            ["#900", "#b45f06", "#bf9000", "#38761d", "#134f5c", "#0b5394", "#351c75", "#741b47"],
            ["#600", "#783f04", "#7f6000", "#274e13", "#0c343d", "#073763", "#20124d", "#4c1130"]
        ];

        $("#color-picker").spectrum({
            preferredFormat: "hex3",
            showInput: true,
            showPalette: true,
            palette: palette,
            change: function (color) {
                imageEditor.setBrush({color: color.toRgbString()});
            }
        });

        $("#text-color-picker").spectrum({
            preferredFormat: "hex3",
            showInput: true,
            showPalette: true,
            palette: palette,
            change: function (color) {
                imageEditor.changeTextStyle(activeObjectId, {'fill': color.toRgbString()}).then(function () {
                });
            }
        });

        function colorPickMode(turn_on, text) {
            $('.tui-image-editor-canvas-container .colorPickBackground').remove();
            $('.get-color').removeClass('active');
            if (turn_on === true) {
                $('<div class="colorPickBackground' + (text ? ' text' : '') + '" style="position:absolute;top:0;left:0;width:100%;height:100%;cursor: crosshair;"></div>').appendTo('.tui-image-editor-canvas-container');
                $('.get-color').addClass('active');
            }
        }

        $('.get-color').on('click', function () {
            colorPickMode(true, $(this).hasClass('text'));
            return false;
        });

        $('body').on('click', '.tui-image-editor-canvas-container .colorPickBackground', function (e) {
            colorPickMode(false);
            var scale = $('.tui-image-editor-canvas-container').width() / imageEditor.getCanvasSize().width;
            var offset = $('.tui-image-editor-canvas-container').offset();
            var point = {x: Math.round((e.pageX - offset.left) / scale), y: Math.round((e.pageY - offset.top) / scale)};
            var lowerCanvas = $('.lower-canvas')[0].getContext('2d');
            var upperCanvas = $('.upper-canvas')[0].getContext('2d');
            var data = upperCanvas.getImageData(point.x, point.y, 1, 1).data;
            if (data.reduce(function(a, v){ return a + v }, 0) === 0) {
                data = lowerCanvas.getImageData(point.x, point.y, 1, 1).data;
            }
            var newColor = "rgba(" + data[0] + ", " + data[1] + ", " + data[2] + ", " + (Math.round(data[3] * 100 / 255) / 100) + ")";

            if ($(this).hasClass('text')) {
                $("#text-color-picker").spectrum("set", newColor);
                imageEditor.changeTextStyle(activeObjectId, {'fill': 'rgba(' + data[0] + ', ' + data[1] + ', ' + data[2] + ', ' + (Math.round(data[3] * 100 / 255) / 100) + ')'});
            } else {
                $("#color-picker").spectrum("set", newColor);
                imageEditor.setBrush({color: 'rgba(' + data[0] + ', ' + data[1] + ', ' + data[2] + ', ' + (Math.round(data[3] * 100 / 255) / 100) + ')'});
            }

            return false;
        });

        function resizeEditor() {
            var $editor = $('.tui-image-editor');
            var $container = $('.tui-image-editor-canvas-container');
            var height = parseFloat($container.css('max-height'));

            $editor.height(height);
        }

        function activateTextMode() {
            if (imageEditor.getDrawingMode() !== 'TEXT') {
                imageEditor.stopDrawingMode();
                imageEditor.startDrawingMode('TEXT');
            }
        }

        function setTextToolbar(obj) {
            $("#text-color-picker").spectrum("set", obj.fill);
            $("#input-text-size").val(obj.fontSize);
        }

        function getBrushSettings() {
            return {
                width: $('#input-brush-width-range').val(),
                color: $("#color-picker").spectrum("get").toRgbString()
            };
        }

        imageEditor.on({
            undoStackChanged: function (length) {
                if (length) {
                    $('#btn-undo').removeClass('disabled');
                } else {
                    $('#btn-undo').addClass('disabled');
                }
                resizeEditor();
            },
            redoStackChanged: function (length) {
                if (length) {
                    $('#btn-redo').removeClass('disabled');
                } else {
                    $('#btn-redo').addClass('disabled');
                }
                resizeEditor();
            },
            objectScaled: function (obj) {
                if (obj.type === 'text') {
                    $("#input-text-size").val(parseInt(obj.fontSize, 10));
                }
            },
            objectActivated: function (obj) {
                activeObjectId = obj.id;
                if (obj.type === 'icon') {
                    imageEditor.stopDrawingMode();
                } else if (obj.type === 'text') {
                    //showSubMenu('text');
                    setTextToolbar(obj);
                    activateTextMode();
                }
            },
            addText: function (pos) {
                imageEditor.addText('New text', {position: pos.originPosition});
            }

        });

        $('#btn-undo').on('click', function () {
            if (!$(this).hasClass('disabled')) {
                imageEditor.undo();
            }
            return false;
        });

        $('#btn-redo').on('click', function () {
            if (!$(this).hasClass('disabled')) {
                imageEditor.redo();
            }
            return false;
        });

        $('#btn-clear-objects').on('click', function () {
            $('.controls-content .sub-menu-container').hide();
            imageEditor.stopDrawingMode();
            imageEditor.clearObjects();
            setTimeout(function () {
                imageEditor.clearUndoStack();
                imageEditor.clearRedoStack();
            }, 100);
            return false;
        });

        $('.close').on('click', function () {
            imageEditor.stopDrawingMode();
            $(this).parents('.sub-menu-container').hide();
            return false;
        });

        $('#btn-crop').on('click', function () {
            //imageEditor.startDrawingMode('CROPPER');
            $('.controls-content .sub-menu-container').hide();
            $('#crop-sub-menu').show();
            return false;
        });

        function calcCropRect(size, xs, ys) {
            var crop_rect = {top: 0, left: 0, width: size.width, height: size.height};
            var new_height = size.width * ys / xs;
            if (new_height > size.height) {
                var new_width = size.height * xs / ys;
                crop_rect.width = new_width;
            } else {
                crop_rect.height = new_height;
            }

            crop_rect.top = crop_rect.height < size.height ? (size.height - crop_rect.height) / 2 : 0;
            crop_rect.left = crop_rect.width < size.width ? (size.width - crop_rect.width) / 2 : 0;

            return crop_rect;
        }

        function previewCrop(size) {
            var new_rect = Object.assign({}, imageEditor.getCanvasSize());
            if (typeof size === 'string' && size.split("x").length === 2) {
                var tmp = size.split("x");
                new_rect = calcCropRect(new_rect, parseInt(tmp[0]), parseInt(tmp[1]));
            }

            var scale = Math.max(new_rect.width / $('.tui-image-editor').width(), new_rect.height / $('.tui-image-editor').height());
            var scaled_canvas_container = {width: new_rect.width / scale, height: new_rect.height / scale};

            var canvas_width = $('.tui-image-editor .lower-canvas').attr("width");
            var canvas_height = $('.tui-image-editor .lower-canvas').attr("height");
            var canvas_scale = Math.max(scaled_canvas_container.width / canvas_width, scaled_canvas_container.height / canvas_height)
            var scaled_canvas_rect = {
                width: canvas_width * canvas_scale,
                height: canvas_height * canvas_scale,
                left: (scaled_canvas_container.width - canvas_width * canvas_scale) / 2,
                top: (scaled_canvas_container.height - canvas_height * canvas_scale) / 2
            };

            $('.tui-image-editor').data('crop_rect', new_rect);

            $('.tui-image-editor-canvas-container').css({
                "max-width": scaled_canvas_container.width,
                "max-height": scaled_canvas_container.height
            });

            $('.tui-image-editor .lower-canvas').css({
                "width": "auto",
                "height": "auto",
                "left": scaled_canvas_rect.left,
                "top": scaled_canvas_rect.top,
                "max-width": scaled_canvas_rect.width,
                "max-height": scaled_canvas_rect.height
            });
            $('.tui-image-editor .upper-canvas').css({
                "width": "auto",
                "height": "auto",
                "left": scaled_canvas_rect.left,
                "top": scaled_canvas_rect.top,
                "max-width": scaled_canvas_rect.width,
                "max-height": scaled_canvas_rect.height
            });
        }

        $('#crop-sub-menu .crop-item .crop').on('click', function () {
            $('#crop-sub-menu .crop-item .crop').removeClass('active');
            var crop = $(this).attr('data-type');
            if (crop !== 'original') {
                $(this).addClass('active');
            }
            previewCrop(crop);
            return false;
        });
        
        $('#crop-sub-menu .manual-crop-items .manual-crop').on('click', function () {
            $('#crop-sub-menu .manual-crop-items .manual-crop').hide();
            $('#crop-sub-menu .crop-items').hide();
            $('#crop-sub-menu .manual-crop-items .actions').show();
            
            imageEditor.startDrawingMode('CROPPER');
            
            return false;
        });
        
        $('#crop-sub-menu .manual-crop-items .apply, #crop-sub-menu .manual-crop-items .cancel').on('click', function () {
            $('#crop-sub-menu .manual-crop-items .manual-crop').show();
            $('#crop-sub-menu .crop-items').show();
            $('#crop-sub-menu .manual-crop-items .actions').hide();
            
            if($(this).hasClass('apply')){
                imageEditor.crop(imageEditor.getCropzoneRect()).then(function () {
                    $('.tui-image-editor').data('crop_rect', {width:imageEditor.getCropzoneRect().width, height:imageEditor.getCropzoneRect().height});
                    imageEditor.stopDrawingMode();
                    resizeEditor();
                });
            }else{
                imageEditor.stopDrawingMode();
            }
            
            return false;
        });

        $('#btn-draw-line').on('click', function () {
            var settings = getBrushSettings();
            imageEditor.startDrawingMode('FREE_DRAWING', settings);

            $('.controls-content .sub-menu-container').hide();
            $('#draw-line-sub-menu').show();
        });

        $('#input-brush-width-range').on('change', function () {
            imageEditor.setBrush({width: parseInt(this.value, 10)});
        });

        $('#btn-draw-text').on('click', function () {
            activateTextMode();
            $('.controls-content .sub-menu-container').hide();
            $('#draw-text-sub-menu').show();
        });

        $("#input-text-size").on('change', function () {
            imageEditor.changeTextStyle(activeObjectId, {
                fontSize: parseInt($(this).val(), 10)
            });
        });

        $('.btn-text-style').on('click', function (e) {
            var styleType = $(this).attr('data-style-type');
            var styleObj;
            e.stopPropagation();
            switch (styleType) {
                case 'b':
                    styleObj = {fontWeight: 'bold'};
                    break;
                case 'i':
                    styleObj = {fontStyle: 'italic'};
                    break;
                case 'u':
                    styleObj = {textDecoration: 'underline'};
                    break;
                case 'l':
                    styleObj = {textAlign: 'left'};
                    break;
                case 'c':
                    styleObj = {textAlign: 'center'};
                    break;
                case 'r':
                    styleObj = {textAlign: 'right'};
                    break;
                default:
                    styleObj = {};
            }

            imageEditor.changeTextStyle(activeObjectId, styleObj);
            return false;
        });

        $('#btn-mask-filter').on('click', function () {
            imageEditor.stopDrawingMode();
            $('.controls-content .sub-menu-container').hide();
            $('#filter-sub-menu').show();
        });

        $('#input-mask-image-file').on('change', function () {
            var file;
            var imgUrl;
            if (!supportingFileAPI) {
                alert('This browser does not support file-api');
            }

            file = event.target.files[0];
            if (file) {
                imgUrl = URL.createObjectURL(file);
                
                //imageEditor.loadImageFromURL(imageEditor.toDataURL(), 'FilterImage').then(function () {
                imageEditor.addImageObject(imgUrl).then(function (objectProps) {
                    URL.revokeObjectURL(file);
                });
                //});

                $(".a2w-edit-photo-container .block-item.input-wrapper").after('<div class="block-item"><a href="#" class="sticker"><img src="' + imgUrl + '"/></a></div>');

                setTimeout(function () {
                    var form_data = new FormData();
                    form_data.append('action', 'a2w_upload_sticker');
                    form_data.append('file', file);
                    $.ajax({
                        url: ajaxurl,
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (json) {
                            if (json.state !== 'ok') {
                                console.log(json);
                            } else {
                                $('img[src="' + imgUrl + '"]').attr('src', json.sticker_url);
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log("error:", textStatus);
                        }
                    });
                }, 1000);
            }
        });

        $('#btn-apply-mask').on('click', function () {
            imageEditor.applyFilter('mask', {
                maskObjId: activeObjectId
            }).then(function (result) {});
        });

        $(".product_images .image").append('<a href="#" class="a2w-photo-edit">Edit</a>');

        $("#postimagediv .inside").append('<a href="#" class="a2w-photo-edit">Edit</a>');

        $('.a2w-product-import-list [rel="images"] .image').append('<a href="#" class="a2w-photo-edit">Edit</a>');

        $(".a2w-modal-wrapper .a2w-modal-close").click(function () {
            $(this).parents('.a2w-modal-wrapper').hide();
            return false;
        });


        $('.a2w-edit-photo-container').on('click', '.sticker', function (e) {
            var stickerUrl = $(this).find('img').attr('src')

            //imageEditor.loadImageFromURL(imageEditor.toDataURL(), 'FilterImage').then(function () {
            imageEditor.addImageObject(stickerUrl).then(function (objectProps) {
                //URL.revokeObjectURL(file);
            });
            //});

            return false;
        });

        function loadImage(image_url) {
            $('.a2w-modal-content .a2w-edit-photo-loader').show();
            if(image_url){
                if(a2w_isExternal(image_url)){
                    jQuery.post(ajaxurl, {action: 'a2w_edit_image_url', url: image_url}).done(function (response) {
                        var json = jQuery.parseJSON(response);
                        if (json.state !== 'ok') {
                            console.log(json);
                        } else {
                            imageEditor.loadImageFromURL(json.url, 'SampleImage').then(function (sizeValue) {
                                // calc initial size
                                imageEditor.resizeCanvasDimension({width: 1200, height: $('.a2w-modal-content').height()});
                                imageEditor.clearUndoStack();
                                previewCrop('original');
                                $('.a2w-modal-content .a2w-edit-photo-loader').hide();
                            });
                        }
                    }).fail(function (xhr, status, error) {
                        console.log(error);
                    });

                }else{
                    imageEditor.loadImageFromURL(image_url, 'SampleImage').then(function (sizeValue) {
                        // calc initial size
                        imageEditor.resizeCanvasDimension({width: 1200, height: $('.a2w-modal-content').height()});
                        imageEditor.clearUndoStack();
                        previewCrop('original');
                        $('.a2w-modal-content .a2w-edit-photo-loader').hide();
                    });
                }    
            }
            
            $('.a2w-modal-toolbar .spinner').removeClass('is-active');
            $("#a2w-edit-image").show();
        }

        $(".product_images .image .a2w-photo-edit").click(function () {
            $('.a2w-edit-photo-container').attr('data-attachment_id', $(this).parents('.image').attr('data-attachment_id'));
            $('.a2w-edit-photo-container').attr('data-view', 'product');
            $('.a2w-edit-photo-container').attr('data-product_id', '');
            
            var data = {action: 'a2w_get_image_by_id', attachment_id: $(this).parents('.image').attr('data-attachment_id')};
            jQuery.post(ajaxurl, data).done(function (response) {
                var json = jQuery.parseJSON(response);
                if (json.state !== 'ok') {
                    console.log(json);
                } else {
                    loadImage(json.image_url);
                }
            }).fail(function (xhr, status, error) {
                console.log(error);
            });
            
            loadImage(null);
            
            return false;
        });

        $("#postimagediv .inside .a2w-photo-edit").click(function () {
            $('.a2w-edit-photo-container').attr('data-attachment_id', $(this).parents('.inside').find('#_thumbnail_id').val());
            $('.a2w-edit-photo-container').attr('data-view', 'product');
            $('.a2w-edit-photo-container').attr('data-product_id', '');

            var image_url = '';
            if($(this).parents('.inside').find('img').attr('srcset')){
                var srcset = $(this).parents('.inside').find('img').attr('srcset').split(',');
                var max_w = 0;
                $.each(srcset, function (index, value) {
                    var regex = /(.*)(\s)+([0-9]+)w/gm;
                    var res = regex.exec(jQuery.trim(value));
                    if (max_w < parseInt(res[3])) {
                        max_w = parseInt(res[3]);
                        image_url = res[1];
                    }
                });
            }else{
                image_url = $(this).parents('.inside').find('img').attr('src');
            }
            

            loadImage(image_url);
            return false;
        });

        $('.a2w-product-import-list [rel="images"] .image .a2w-photo-edit').click(function () {
            $('.a2w-edit-photo-container').attr('data-attachment_id', $(this).parents('.image').attr('id'));
            $('.a2w-edit-photo-container').attr('data-view', 'import');
            $('.a2w-edit-photo-container').attr('data-product_id', $(this).parents('.product').attr('data-id'));

            var image_url = $(this).parents('.image').find('img').attr('data-original');

            loadImage(image_url);
            return false;
        });

        $("#a2w-edit-image .save-image").click(function () {
            var _this_btn = $(this);
            $(_this_btn).attr('disabled', 'disabled');
            $('.a2w-modal-toolbar .spinner').addClass('is-active');
            var current_attachment_id = $('.a2w-edit-photo-container').attr('data-attachment_id');
            var save = function () {
                var data = {action: 'a2w_save_image', attachment_id: current_attachment_id, product_id: $('.a2w-edit-photo-container').attr('data-product_id'), view: $('.a2w-edit-photo-container').attr('data-view'), data: imageEditor.toDataURL(), name: imageEditor.getImageName()};
                jQuery.post(ajaxurl, data).done(function (response) {
                    var json = jQuery.parseJSON(response);
                    if (json.state !== 'ok') {
                        console.log(json);
                    } else {
                        //console.log(json);
                        $('.tui-image-editor').removeData('crop_rect');
                        
                        if($('#_thumbnail_id[value="'+current_attachment_id+'"]').length>0){
                            $('#_thumbnail_id[value="'+current_attachment_id+'"]').parents('.inside').find('img').removeAttr('srcset');
                            $('#_thumbnail_id[value="'+current_attachment_id+'"]').parents('.inside').find('img').attr('src', json.attachment_url);
                            $('#_thumbnail_id[value="'+current_attachment_id+'"]').val(json.attachment_id);
                        } else if($('.image[data-attachment_id="'+current_attachment_id+'"]').length>0){
                            $('.image[data-attachment_id="'+current_attachment_id+'"] img').removeAttr('srcset');
                            $('.image[data-attachment_id="'+current_attachment_id+'"] img').attr('src', json.croped_attachment_url?json.croped_attachment_url:json.attachment_url);
                            $('.image[data-attachment_id="'+current_attachment_id+'"]').attr('data-attachment_id', json.attachment_id);
                        }else{
                            $('img[data-id="'+json.image_id+'"]').attr('src', json.croped_attachment_url?json.croped_attachment_url:json.attachment_url);
                            $('img[data-id="'+json.image_id+'"]').attr('data-original', json.croped_attachment_url?json.croped_attachment_url:json.attachment_url);
                        }
                    }
                    $('#a2w-edit-image').hide();
                    $(_this_btn).removeAttr('disabled');
                }).fail(function (xhr, status, error) {
                    console.log(error);
                    $('#a2w-edit-image').hide();
                });
            }

            var crop_rect = $('.tui-image-editor').data('crop_rect');
            if (crop_rect) {
                previewCrop('original');
                imageEditor.crop(crop_rect).then(function () {
                    save();
                });
            } else {
                save();
            }
        });

        $("#a2w-edit-image .cancel-image").click(function () {
            $('#a2w-edit-image').hide();
        });

    });
})(jQuery, window, document);
