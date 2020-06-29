var WCCC = {};
WCCC.Helpers = {};
WCCC.Views = {};
WCCC.Events = {};

_.extend(WCCC.Events, Backbone.Events);


WCCC.Helpers.uniqid = function(prefix, more_entropy)
{
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +    revised by: Kankrelune (http://www.webfaktory.info/)
	// %        note 1: Uses an internal counter (in php_js global) to avoid collision
	// *     example 1: uniqid();
	// *     returns 1: 'a30285b160c14'
	// *     example 2: uniqid('foo');
	// *     returns 2: 'fooa30285b1cd361'
	// *     example 3: uniqid('bar', true);
	// *     returns 3: 'bara20285b23dfd1.31879087'
	if (typeof prefix == 'undefined') {
		prefix = "";
	}

	var retId;
	var formatSeed = function(seed, reqWidth) {
		seed = parseInt(seed, 10).toString(16); // to hex str
		if (reqWidth < seed.length) { // so long we split
			return seed.slice(seed.length - reqWidth);
		}
		if (reqWidth > seed.length) { // so short we pad
			return Array(1 + (reqWidth - seed.length)).join('0') + seed;
		}
		return seed;
	};

	// BEGIN REDUNDANT
	if (!this.php_js) {
		this.php_js = {};
	}
	// END REDUNDANT
	if (!this.php_js.uniqidSeed) { // init seed with big random int
		this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
	}
	this.php_js.uniqidSeed++;

	retId = prefix; // start with prefix, add current milliseconds hex string
	retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
	retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
	if (more_entropy) {
		// for more entropy we add a float lower to 10
		retId += (Math.random() * 10).toFixed(8).toString();
	}

	return retId;

}


jQuery(function($) {

	$('#wccc_settings_location').change(function() {
		if ($(this).val() == 'custom:custom') {
			$('.wccc-settings-custom').show();
		} else {
			$('.wccc-settings-custom').hide();
		}
	});
	
	$('#wccc_settings_location').trigger('change');

	// Ajax Chosen Product Selectors
	var bind_ajax_chosen = function() {

		$(".wccc-date-picker-field").datepicker({
			dateFormat: "yy-mm-dd",
			numberOfMonths: 1,
			showButtonPanel: true,
		});

		$('select.chosen_select').chosen();


		$("select.ajax_chosen_select_products").ajaxChosen({
			method: 'GET',
			url: WCCCParams.ajax_url,
			dataType: 'json',
			afterTypeDelay: 100,
			data: {
				action: 'woocommerce_json_search_products',
				security: WCCCParams.search_products_nonce
			}
		}, function(data) {

			var terms = {};

			$.each(data, function(i, val) {
				terms[i] = val;
			});

			return terms;
		});


		$("select.ajax_chosen_select").each(function(element) {
			$(element).ajaxChosen({
				method: 'GET',
				url: WCCCParams.ajax_url,
				dataType: 'json',
				afterTypeDelay: 100,
				data: {
					action: 'wccc_json_search',
					method: $(element).data('method'),
					security: WCCCParams.ajax_chosen
				}
			}, function(data) {

				var terms = {};

				$.each(data, function(i, val) {
					terms[i] = val;
				});

				return terms;
			});
		});

	}

	bind_ajax_chosen();

	//Note - this section will eventually be refactored into the backbone views themselves.  For now, this is more efficent. 
	$('#wccc-rules-groups').on('change', 'select.rule_type', function() {

		// vars
		var tr = $(this).closest('tr');
		var rule_id = tr.data('ruleid');
		var group_id = tr.closest('table').data('groupid');

		var ajax_data = {
			action: "wccc_change_rule_type",
			security: WCCCParams.ajax_nonce,
			group_id: group_id,
			rule_id: rule_id,
			rule_type: $(this).val()
		};

		tr.find('td.condition').html('').remove();
		tr.find('td.operator').html('').remove();

		tr.find('td.loading').show();

		// load location html
		$.ajax({
			url: ajaxurl,
			data: ajax_data,
			type: 'post',
			dataType: 'html',
			success: function(html) {
				tr.find('td.loading').hide().before(html);
				bind_ajax_chosen();
			}
		});
	});



	//Backbone views to manage the UX. 
	var WC_Conditional_Content_Rule_Builder = Backbone.View.extend({
		groupCount : 0,
		el: '#wccc-rules-groups',
		events: {
			'click .wccc-add-rule-group': 'addRuleGroup',
		},
		render: function() {
			this.$target = this.$('.wccc-rule-group-target');

			WCCC.Events.bind('wccc:remove-rule-group', this.removeRuleGroup, this);

			this.views = {};
			var groups = this.$('div.wccc-rule-group-container');
			_.each(groups, function(group) {
				this.groupCount++;
				var id = $(group).data('groupid');
				var view = new WC_Conditional_Content_Rule_Group(
					{
						el: group,
						model: new Backbone.Model(
							{
						
								groupId: id,
								groupCount: this.groupCount,
								headerText : this.groupCount > 1 ? WCCCParams.text_or : WCCCParams.text_apply_when, 
								removeText : WCCCParams.remove_text
							})
					});

				this.views[id] = view;
				view.bind('wccc:remove-rule-group', this.removeRuleGroup, this);
				
			}, this);
			
			if (this.groupCount > 0){
				$('.or').show();
			}
		},
		addRuleGroup: function(event) {
			event.preventDefault();
			
			var newId = 'group' + WCCC.Helpers.uniqid();
			this.groupCount++;
			
			var view = new WC_Conditional_Content_Rule_Group({model: new Backbone.Model({
					groupId: newId,
					groupCount: this.groupCount,
					headerText : this.groupCount > 1 ? WCCCParams.text_or : WCCCParams.text_apply_when, 
					removeText : WCCCParams.remove_text
				})});

			this.$target.append(view.render().el);
			this.views[newId] = view;
			
			view.bind('wccc:remove-rule-group', this.removeRuleGroup, this);
			
			if (this.groupCount > 0){
				$('.or').show();
			}
			
			bind_ajax_chosen();
			
			return false;
		},
		removeRuleGroup: function(sender) {
			delete(this.views[sender.model.get('groupId')]);
			sender.remove();
		}
	});

	var WC_Conditional_Content_Rule_Group = Backbone.View.extend({
		tagName: 'div',
		className: 'wccc-rule-group-container',
		template: _.template('<div class="wccc-rule-group-header"><h4><%= headerText %></h4><a href="#" class="wccc-remove-rule-group button"><%= removeText %></a></div><table class="wccc-rules" data-groupid="<%= groupId %>"><tbody></tbody></table>'),
		events: {
			'click .wccc-remove-rule-group': 'onRemoveGroupClick'
		},
		initialize: function() {
			this.views = {};
			this.$rows = this.$el.find('table.wccc-rules tbody');

			var rules = this.$('tr.wccc-rule');
			_.each(rules, function(rule) {
				var id = $(rule).data('ruleid');
				var view = new WC_Conditional_Content_Rule_Item(
					{
						el: rule,
						model: new Backbone.Model({
							groupId: this.model.get('groupId'),
							ruleId: id})
					});

				view.delegateEvents();

				view.bind('wccc:add-rule', this.onAddRule, this);
				view.bind('wccc:remove-rule', this.onRemoveRule, this);

				this.views.ruleId = view;

			}, this);
		},
		render: function() {

			this.$el.html(this.template(this.model.toJSON()));

			this.$rows = this.$el.find('table.wccc-rules tbody');
			this.$el.attr('data-groupid', this.model.get('groupId'));

			this.onAddRule(null);

			return this;
		},
		onAddRule: function(sender) {
			var newId = 'rule' + WCCC.Helpers.uniqid();
			var view = new WC_Conditional_Content_Rule_Item({model: new Backbone.Model({
					groupId: this.model.get('groupId'),
					ruleId: newId
				})});

			if (sender == null) {
				this.$rows.append(view.render().el);
			} else {
				sender.$el.after(view.render().el);
			}
			view.bind('wccc:add-rule', this.onAddRule, this);
			view.bind('wccc:remove-rule', this.onRemoveRule, this);

			bind_ajax_chosen();

			this.views.ruleId = view;
		},
		onRemoveRule: function(sender) {
			var ruleId = sender.model.get('ruleId');
			delete(this.views[ruleId]);
			sender.remove();
		},
		onRemoveGroupClick: function(event) {
			event.preventDefault();
			WCCC.Events.trigger('wccc:removing-rule-group', this);
			this.trigger('wccc:remove-rule-group', this);
			return false;
		}
	});

	var WC_Conditional_Content_Rule_Item = Backbone.View.extend({
		tagName: 'tr',
		className: 'wccc-rule',
		template: _.template($('#wccc-rule-template').html()),
		events: {
			'click .wccc-add-rule': 'onAddClick',
			'click .wccc-remove-rule': 'onRemoveClick'
		},
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			this.$el.attr('data-ruleid', this.model.get('ruleId'));
			return this;
		},
		onAddClick: function(event) {
			event.preventDefault();

			WCCC.Events.trigger('wccc:adding-rule', this);
			this.trigger('wccc:add-rule', this);

			return false;
		},
		onRemoveClick: function(event) {
			event.preventDefault();

			WCCC.Events.trigger('wccc:removing-rule', this);
			this.trigger('wccc:remove-rule', this);

			return false;
		}
	});

	var ruleBuilder = new WC_Conditional_Content_Rule_Builder(  );
	ruleBuilder.render();



});