var PortoWizard = (function($){

    var t;

    var callbacks = {
        install_plugins: function(btn){
            var plugins = new PluginManager();
            plugins.init(btn);
        },
        load_unused_shortcodes: function(){
            var shortcodes = new ShortcodeManager();
            shortcodes.init();
        }
    };

    function window_loaded(){
        // init button clicks:
        $('.button-next').on( 'click', function(e) {
            var loading_button = wizard_step_loading_button(this);
            if(!loading_button){
                return false;
            }
            if($(this).data('callback') && typeof callbacks[$(this).data('callback')] != 'undefined'){
                // we have to process a callback before continue with form submission
                callbacks[$(this).data('callback')](this);
                return false;
            }else{
                loading_content();
                return true;
            }
        });
        // init on load
        if ($('.shortcode_list').length) {
            callbacks['load_unused_shortcodes']();
            return false;
        }
        $('.button-upload').on( 'click', function(e) {
            e.preventDefault();
            renderMediaUploader();
        });
        $('.theme-presets a').on( 'click', function(e) {
            e.preventDefault();
            var $ul = $(this).parents('ul').first();
            $ul.find('.current').removeClass('current');
            var $li = $(this).parents('li').first();
            $li.addClass('current');
            var newcolor = $(this).data('style');
            $('#new_style').val(newcolor);
            return false;
        });

        $('.porto-setup-wizard-plugins input[type="checkbox"]').on('change', function() {
            var slug = $(this).closest('li').data('slug');
            if ('js_composer' != slug && 'elementor' != slug) {
                return;
            }
            var $wrap = $(this).closest('.porto-setup-wizard-plugins'),
                $notice = $(this).closest('form').find('.porto-notice');
            if ((!$wrap.find('li[data-slug="js_composer"]').length || $wrap.find('li[data-slug="js_composer"] input[type="checkbox"]').is(':checked')) &&
                (!$wrap.find('li[data-slug="elementor"]').length || $wrap.find('li[data-slug="elementor"] input[type="checkbox"]').is(':checked'))) {
                $notice.removeClass('d-none');
            } else {
                $notice.addClass('d-none');
            }
        });
    }

    function loading_content(){
        $('.envato-setup-content').block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
    }

    function PluginManager(){

        var complete;
        var items_completed = 0;
        var current_item = '';
        var $current_node;
        var current_item_hash = '';

        function ajax_callback(response){
            if(typeof response == 'object' && typeof response.message != 'undefined'){
                $current_node.find('span').text(response.message);
                if(typeof response.url != 'undefined'){
                    // we have an ajax url action to perform.

                    if(response.hash == current_item_hash){
                        $current_node.find('span').text("failed");
                        find_next();
                    }else {
                        current_item_hash = response.hash;
                        jQuery.post(response.url, response, function(response2) {
                            process_current();
                            $current_node.find('span').text(response.message);
                        }).fail(ajax_callback);
                    }

                }else if(typeof response.done != 'undefined'){
                    find_next();
                }else{
                    find_next();
                }
            }else{
                $current_node.find('span').text("ajax error");
                find_next();
            }
        }
        function process_current(){
            if(current_item){
                jQuery.post(ajaxurl, {
                    action: typeof porto_setup_wizard_params == 'undefined' ? 'porto_speed_optimize_wizard_plugins' : 'porto_setup_wizard_plugins',
                    wpnonce: typeof porto_setup_wizard_params == 'undefined' ? porto_speed_optimize_wizard_params.wpnonce : porto_setup_wizard_params.wpnonce,
                    slug: current_item
                }, ajax_callback).fail(ajax_callback);
            }
        }
        function find_next(){
            var do_next = false;
            if($current_node){
                if(!$current_node.data('done_item')){
                    items_completed++;
                    $current_node.data('done_item',1);
                }
                $current_node.find('.spinner').css('visibility','hidden');
            }
            var $li = $('.porto-setup-wizard-plugins li');
            $li.each(function(){
                if ($(this).hasClass('installing')) {
                    if(current_item == '' || do_next){
                        current_item = $(this).data('slug');
                        $current_node = $(this);
                        process_current();
                        do_next = false;
                    }else if($(this).data('slug') == current_item){
                        do_next = true;
                    }
                }
            });
            if (items_completed >= $('.porto-setup-wizard-plugins li.installing').length){
                complete();
            }
        }
        
        return {
            init: function(btn){
                $('.porto-setup-wizard-plugins > li').each(function(){
                    if ($(this).find('input[type="checkbox"]').is(':checked')) {
                        $(this).addClass('installing');
                    }
                });
                complete = function(){
                    loading_content();
                    if ($(btn).attr('href') && '#' != $(btn).attr('href')) {
                        window.location.href=btn.href;
                    } else {
                        window.location.reload();
                    }
                };
                find_next();
            }
        }
    }

    function ShortcodeManager() {

        function in_array(param, arr) {
            if (typeof arr != undefined) {
                for (var i = 0; i < arr.length; i++) {
                    if (param == arr[i]) {
                        return true;
                    }
                }
            }
            return false;
        }
        function ajax_callback(response) {
            if (response) {
                var html = '';
                for (var i in response) {
                    var shortcode = response[i];
                    html += '<li>';
                        html += '<label class="checkbox checkbox-inline">';
                            html += '<input type="checkbox" name="shortcodes[]" value="' + shortcode + '"' + (in_array( shortcode, porto_speed_optimize_wizard_params.shortcodes_to_remove ) ? ' checked="checked"' : '') + '>';
                            html += shortcode;
                        html += '</label>';
                    html += '</li>';
                }
                jQuery('.shortcode_list').html(html);
                jQuery('.porto-setup-actions .btn:disabled').removeAttr('disabled');
            }
        }
        return {
            init: function() {
                jQuery('.shortcode_list').block({
                    message: null,
                    overlayCSS: {
                        background: '#f1f1f1',
                        opacity: 0.6
                    }
                });
                jQuery.ajax({
                    type: 'post',
                    url: ajaxurl,
                    data: {action: 'porto_speed_optimize_wizard_shortcodes', wpnonce: porto_speed_optimize_wizard_params.wpnonce},
                    dataType: 'json',
                    success: ajax_callback
                });
            }
        }
    }

    function renderMediaUploader() {
        'use strict';

        var file_frame, attachment;

        if ( undefined !== file_frame ) {
            file_frame.open();
            return;
        }
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Upload Logo',
            button: {
                text: 'Select Logo'
            },
            multiple: false
        });

        file_frame.on( 'select', function() {
            attachment = file_frame.state().get('selection').first().toJSON();
            jQuery('.site-logo').attr('src',attachment.url);
            jQuery('#new_logo_id').val(attachment.id);
        });
        file_frame.open();
    }

    function wizard_step_loading_button(btn){

        var $button = jQuery(btn);
        if($button.data('done-loading') == 'yes')return false;
        var existing_text = $button.text();
        var existing_width = $button.outerWidth();
        var loading_text = '⡀⡀⡀⡀⡀⡀⡀⡀⡀⡀⠄⠂⠁⠁⠂⠄';
        var completed = false;

        $button.css('width',existing_width);
        $button.addClass('wizard_step_loading_button_current');
        var _modifier = $button.is('input') ? 'val' : 'text';
        $button[_modifier](loading_text);
        $button.data('done-loading','yes');

        var anim_index = [0,1,2];

        // animate the text indent
        function moo() {
            if (completed)return;
            var current_text = '';
            // increase each index up to the loading length
            for(var i = 0; i < anim_index.length; i++){
                anim_index[i] = anim_index[i]+1;
                if(anim_index[i] >= loading_text.length)anim_index[i] = 0;
                current_text += loading_text.charAt(anim_index[i]);
            }
            $button[_modifier](current_text);
            setTimeout(function(){ moo();},60);
        }

        moo();

        return {
            done: function(){
                completed = true;
                $button[_modifier](existing_text);
                $button.removeClass('wizard_step_loading_button_current');
                $button.attr('disabled',false);
            }
        }

    }

    return {
        init: function(){
            $(window_loaded);
        }
    }

})(jQuery);


PortoWizard.init();