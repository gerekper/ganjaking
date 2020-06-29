;(function ($) {
	"use strict";
	if(_.isUndefined(vc))
		return ;
	
	var pt_plusViewShortcode = Backbone.View.extend({
		 initialize: function() {
			 vc.shortcodes.on('add', this.vcaddShortcode, this);
			 vc.shortcodes.on('reset', this.vcresetShortcode, this);
			 this.modal_template = $('<div id="pt_plus-view-shortcode" class="vc_modal fade" style="display:none">\
					 	<div class="vc_modal-dialog modal-dialog" style="width:600px;margin:10% auto 0">\
					 		<div class="vc_modal-content">\
					 			<div class="vc_modal-header">\
					 				<a class="vc_close" aria-hidden="true" data-dismiss="modal" href="#">\
					 					<i class="vc_icon"></i>\
					 				</a>\
									<div class="pt-copy-shortcode">Copy</div>\
					 				<h3 id="pt_plus-view-shortcode-dialog-title" class="vc_modal-title">View Shortcode</h3>\
					 			</div>\
					 			<div class="vc_modal-body">\
		 							<textarea style="width:100%;min-height:200px;resize: none;" onfocus="this.select();" readonly="readonly"></textarea>\
		 						</div>\
					 		</div>\
					 	</div><div class="modal-backdrop"></div>');
			 $('body').append(this.modal_template);
		 },
		 unescapeParam:function (value) {
			 return value.replace(/(\`{2})/g, '"');
		 },
		 vcaddShortcode: function(model){
			 this.vcaddButton(model);
		 },
		 escapeParam:function (value) {
			 return _.isString(value) ? value.replace(/"/g, "``") : _.isUndefined(value) || _.isNull(value) || !value.toString ? "" : value.toString().replace(/"/g, "``");
		 },
		 vcresetShortcode: function(shortcodes){
			 var that = this;
			 _.each(shortcodes.models,function(model){
				 that.vcaddButton(model);
			 });
		 },
		 vcaddButton: function(model){
			 var that = this;
			 var el = $('[data-model-id="'+model.get('id')+'"]');
				el.find('.controls,.vc_controls').each(function(){
					if(!$(this).find('.pt_plus-view-shortcode').length){
						$('<a class="vc_control pt_plus-view-shortcode column_shortcode" href="#" title="View Shortcode"><i class="icon-dark"></i></a>').insertBefore($(this).find('.column_edit'));
					}
				});
				el.find('.vc_controls').each(function(){
					if(!$(this).find('.pt_plus-view-shortcode').length){
						$('<a class="vc_control-btn pt_plus-view-shortcode vc_control-btn-shortcode" href="#" title="View Shortcode"><span class="vc_btn-content"><span class="icon-light"></span></span></a>').insertBefore($(this).find('.vc_control-btn-edit'));
					}
				 });
			 $('.pt_plus-view-shortcode').on('click',function(e){
				 e.stopPropagation();
				 e.preventDefault();
				
				var parent = $(this).closest('[data-model-id]').data('model');
				
				var models = _.filter(_.values(vc.storage.data), function (model) {
					return model.id === parent.id;
	            });
				
				models = _.sortBy(models, function (model) {
	                return model.order;
	            });
				
				var content = _.reduce(models, function (memo, model) {
	                model.html = that.vccreateShortcodeString(model);
	                return memo + model.html;
	            }, '', this);
				
				$(".modal-backdrop").addClass('fade in');
				$(".modal-backdrop").fadeTo(500, 0.5);
				$(".pt-copy-shortcode").text("Copy");
				$(that.modal_template).find('textarea').text(content);
				$(that.modal_template).fadeIn($(this).data());
			 });
			 $(".vc_close,.modal-backdrop").on('click',function(e) {
			 e.preventDefault();
				$(".vc_modal, .modal-backdrop").fadeOut(500, function() {
					$(".modal-backdrop").removeClass('fade in');
				});
			});
			$(".pt-copy-shortcode").on('click',function(e) {
				e.preventDefault();
				var copyText = $('#pt_plus-view-shortcode .vc_modal-body textarea');
				copyText.select();
				document.execCommand("Copy");
				$(".pt-copy-shortcode").text("Copied");
			});
		 },
		vccreateShortcodeString:function (model) {
            var params = _.extend({}, model.params),
                params_to_string = {};
            _.each(params, function (value, key) {
                if (key !== 'content' && !_.isEmpty(value)) params_to_string[key] = this.escapeParam(value);
            }, this);
            
            var content = this.vc_getShortcodeContent(model),
                is_container = _.isObject(vc.map[model.shortcode]) && _.isBoolean(vc.map[model.shortcode].is_container) && vc.map[model.shortcode].is_container === true;
            if(!is_container  && _.isObject(vc.map[model.shortcode]) && !_.isEmpty(vc.map[model.shortcode].as_parent)) is_container = true;
           
            return wp.shortcode.string({
                tag:model.shortcode,
                attrs:params_to_string,
                content:content,
                type:!is_container && _.isUndefined(params.content) ? 'single' : ''
            });
        },
		vc_getShortcodeContent:function (parent) {
		    var that = this,
		        models = _.sortBy(_.filter(vc.storage.data, function (model) {
		            // Filter children
		            return model.parent_id === parent.id;
		        }), function (model) {
		            // Sort by `order` field
		            return model.order;
		        }),
		
		        params = {};
		    _.extend(params, parent.params);
		 
		    if (!models.length) {
		        if(!_.isUndefined(window.switchEditors) && _.isString(params.content) && window.switchEditors.wpautop(params.content)===params.content) {
		            params.content = window.vc_wpautop(params.content);
		        }
		        return _.isUndefined(params.content) ? '' : params.content;
		    }
		    return _.reduce(models, function (memo, model) {
		        return memo + that.vccreateShortcodeString(model);
		    }, '');
		}
	 });
	 $(function(){
		 var pt_plusviewshortcode = new pt_plusViewShortcode();
	 });
})(window.jQuery);