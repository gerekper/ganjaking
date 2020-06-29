(function($){
    
    if ( !$.factory ) $.factory = {}
    if ( $.factory.widget ) return;
    
    /**
    * OnePress Widget Factory.
    */
    $.factory.widget = function (pluginName, pluginObject) {

        var factory = {

            createWidget: function (element, options) {
                var widget = $.extend(true, {}, pluginObject);

                widget.element = $(element);
                widget.options = $.extend(true, widget.options, options);

                if (widget._init) widget._init();
                if (widget._create) widget._create();

                $.data(element, 'plugin_' + pluginName, widget);
            },

            callMethod: function (widget, methodName) {
                widget[methodName] && widget[methodName]();
            }
        };

        $.fn[pluginName] = function () {
            var args = arguments;
            var argsCount = arguments.length;

            this.each(function () {

                var widget = $.data(this, 'plugin_' + pluginName);

                // a widget is not created yet
                if (!widget && argsCount <= 1) {
                    factory.createWidget(this, argsCount ? args[0] : false);

                    // a widget is created, the public method with no args is being called
                } else if (argsCount == 1) {
                    factory.callMethod(widget, args[0]);
                }
            });
        };
    };
    
    /**
     * Radio #1
     */
    
    $.fn.onpRadioCircles = function( options ) {
        if ( !options ) options = {};
        options.theme = 'circles';
        $(this).onpRadio(options);
    };
    
    $.fn.onpRadioSquares = function( options ) {
        if ( !options ) options = {};
        options.theme = 'squares';
        $(this).onpRadio(options);
    };
    
    $.factory.widget('onpRadio', {
        options: {
            theme: 'squares',
            selected: null
        },
        _create: function() {
            var self = this;
            this._createMarkup();
            
            this.radio.find('.onp-radio-option').click(function(){
                if ( $(this).is(".disabled") ) return;
                var selected = self.radio.find('.onp-radio-option.selected');
                if ( selected.data('value') == $(this).data('value')) return;
                
                selected.removeClass('selected');
                $(this).addClass("selected");
                
                var value = $(this).data('value');
                self.element.val(value);
                self.element.trigger( "change", $(this).data('value') );
            });
        },
        _createMarkup: function() {
            
            var wrap = $("<ul class='onp-radio-wrap'></ul>").addClass('onp-radio-' + this.options.theme);
            this.element.find("option").each(function(){
                var $this = $(this);
                
                var value = $this.attr('value');
                var text = $this.html();
                var icon = $this.data('icon');
                var disabled = $this.attr('disabled');
                
                var option = $("<li class='onp-radio-option' data-value='" + value + "'></li>");
                var innerOptionWrap = $("<span class='onp-radio-option-inner-wrap'></span>").appendTo(option);
                option.addClass('onp-radio-option-' + value);
                
                if ( icon ) innerOptionWrap.append("<i class='" + icon + "'></i>");
                if ( disabled ) option.addClass('disabled');
                
                wrap.append(option);
            });
            
            this.options.selected = this.options.selected || this.element.find("option:selected").attr('value');
            wrap.find('.onp-radio-option-' + this.options.selected).addClass("selected");
            
            this.element.hide();
            this.element.after(wrap);
            this.radio = wrap;
        }
    });
    
    var factoryForms = {
        
        collapsedGroups: function( $target ) {
            if ( !$target ) $target = $("body");
            
            $target.find(".fy-collapsed-show").click(function(){
                $( $(this).attr('href') ).fadeIn();
                $(this).hide();
                return false;
            });
            
            $target.find(".fy-collapsed-hide").click(function(){
                var content = $( $(this).attr('href') );
                content.fadeOut(300, function(){
                    content.prev().show();
                });
                return false;
            }); 
        }
    }

    $(function(){
       $(".onp-radio-circles.auto").onpRadioCircles();  
       $(".onp-radio-squares.auto").onpRadioSquares();   
        factoryForms.collapsedGroups();
    });
    
})(jQuery)

