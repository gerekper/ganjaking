/**
 * Send an action over AJAX. A wrapper around jQuery.ajax. In future, all consumers can be reviewed to simplify some of the options, where there is historical cruft.
 * N.B. updraft_iframe_modal() below uses the AJAX URL for the iframe's src attribute
 *
 * @param {string}   action   - the action to send
 * @param {*}        data     - data to send
 * @param {Function} callback - will be called with the results
 * @param {object}   options  -further options. Relevant properties include:
 * - [json_parse=true] - whether to JSON parse the results
 * - [alert_on_error=true] - whether to show an alert box if there was a problem (otherwise, suppress it)
 * - [action='updraft_ajax'] - what to send as the action parameter on the AJAX request (N.B. action parameter to this function goes as the 'subaction' parameter on the AJAX request)
 * - [nonce=updraft_credentialtest_nonce] - the nonce value to send.
 * - [nonce_key='nonce'] - the key value for the nonce field
 * - [timeout=null] - set a timeout after this number of seconds (or if null, none is set)
 * - [async=true] - control whether the request is asynchronous (almost always wanted) or blocking (would need to have a specific reason)
 * - [type='POST'] - GET or POST
 */
function updraftcentral_send_command(action, data, callback, options) {
	
	default_options = {
		json_parse: true,
		alert_on_error: true,
		action: 'updraft_central_ajax',
		nonce_key: 'nonce',
		timeout: null,
		async: true,
		type: 'POST'
	}

	if ('undefined' !== typeof uclion.updraftcentral_credentialtest_nonce && uclion.updraftcentral_credentialtest_nonce) {
		default_options.nonce = uclion.updraftcentral_credentialtest_nonce;
	}
	
	if ('undefined' === typeof options) options = {};

	for (var opt in default_options) {
		if (!options.hasOwnProperty(opt)) { options[opt] = default_options[opt]; }
	}
	
	var ajax_data = {
		action: options.action,
		subaction: action,
	};
	
	ajax_data[options.nonce_key] = options.nonce;
	ajax_data.action_data = data;
	
	var ajax_opts = {
		type: options.type,
		url: ajaxurl,
		data: ajax_data,
		success: function(response, status) {
			if (options.json_parse) {
				try {
					var resp = central_parse_json(response);
				} catch (e) {
					if ('function' == typeof options.error_callback) {
						return options.error_callback(response, e, 502, resp);
					} else {
						console.log(e);
						console.log(response);
						if (options.alert_on_error) { alert(uclion.unexpectedresponse+' '+response); }
						return;
					}
				}
				if (resp.hasOwnProperty('fatal_error')) {
					if ('function' == typeof options.error_callback) {
						// 500 is internal server error code
						return options.error_callback(response, status, 500, resp);
					} else {
						console.error(resp.fatal_error_message);
						if (options.alert_on_error) { alert(resp.fatal_error_message); }
						return false;
					}
				}
				if ('function' == typeof callback) callback(resp, status, response);
			} else {
				if ('function' == typeof callback) callback(response, status);
			}
		},
		error: function(response, status, error_code) {
			if ('function' == typeof options.error_callback) {
				options.error_callback(response, status, error_code);
			} else {
				console.log("updraftcentral_send_command: error: "+status+" ("+error_code+")");
				console.log(response);
			}
		},
		dataType: 'text',
		async: options.async
	};
	
	if (null != options.timeout) { ajax_opts.timeout = options.timeout; }
	
	jQuery.ajax(ajax_opts);
	
}

/**
 * Parse JSON string, including automatically detecting unwanted extra input and skipping it
 *
 * @param {string}  json_mix_str - JSON string which need to parse and convert to object
 * @param {boolean} analyse		 - if true, then the return format will contain information on the parsing, and parsing will skip attempting to JSON.parse() the entire string (will begin with trying to locate the actual JSON)
 *
 * @throws SyntaxError|String (including passing on what JSON.parse may throw) if a parsing error occurs.
 *
 * @returns Mixed parsed JSON object. Will only return if parsing is successful (otherwise, will throw). If analyse is true, then will rather return an object with properties (mixed)parsed, (integer)json_start_pos and (integer)json_end_pos
 */
function central_parse_json(json_mix_str, analyse) {

	analyse = ('undefined' === typeof analyse) ? false : true;
	
	// Just try it - i.e. the 'default' case where things work (which can include extra whitespace/line-feeds, and simple strings, etc.).
	if (!analyse) {
		try {
			var result = JSON.parse(json_mix_str);
			return result;
		} catch (e) {
			console.log(uclion.plugin_name+': Exception when trying to parse JSON (1) - will attempt to fix/re-parse based upon first/last curly brackets');
			console.log(json_mix_str);
		}
	}

	var json_start_pos = json_mix_str.indexOf('{');
	var json_last_pos = json_mix_str.lastIndexOf('}');
	
	// Case where some php notice may be added after or before json string
	if (json_start_pos > -1 && json_last_pos > -1) {
		var json_str = json_mix_str.slice(json_start_pos, json_last_pos + 1);
		try {
			var parsed = JSON.parse(json_str);
			if (!analyse) { console.log(uclion.plugin_name+': JSON re-parse successful'); }
			return analyse ? { parsed: parsed, json_start_pos: json_start_pos, json_last_pos: json_last_pos + 1 } : parsed;
		} catch (e) {
			console.log(uclion.plugin_name+': Exception when trying to parse JSON (2) - will attempt to fix/re-parse based upon bracket counting');
			 
			var cursor = json_start_pos;
			var open_count = 0;
			var last_character = '';
			var inside_string = false;
			
			// Don't mistake this for a real JSON parser. Its aim is to improve the odds in real-world cases seen, not to arrive at universal perfection.
			while ((open_count > 0 || cursor == json_start_pos) && cursor <= json_last_pos) {
				
				var current_character = json_mix_str.charAt(cursor);
				
				if (!inside_string && '{' == current_character) {
					open_count++;
				} else if (!inside_string && '}' == current_character) {
					open_count--;
				} else if ('"' == current_character && '\\' != last_character) {
					inside_string = inside_string ? false : true;
				}
					
				last_character = current_character;
				cursor++;
			}
			console.log("Started at cursor="+json_start_pos+", ended at cursor="+cursor+" with result following:");
			console.log(json_mix_str.substring(json_start_pos, cursor));
			
			try {
				var parsed = JSON.parse(json_mix_str.substring(json_start_pos, cursor));
				console.log(uclion.plugin_name+': JSON re-parse successful');
				return analyse ? { parsed: parsed, json_start_pos: json_start_pos, json_last_pos: cursor } : parsed;
			} catch (e) {
				// Throw it again, so that our function works just like JSON.parse() in its behaviour.
				throw e;
			}
		}
	}

	throw uclion.plugin_name+": could not parse the JSON";
	
}

jQuery(function($) {
	$('#updraftcentral_keys').on('click', 'a.updraftcentral_keys_show', function(e) {
		e.preventDefault();
		$(this).remove();
		$('#updraftcentral_keys_table').slideDown();
	});
	
	$('#updraftcentral_keycreate_altmethod_moreinfo_get').on('click', function(e) {
		e.preventDefault();
		$(this).remove();
		$('#updraftcentral_keycreate_altmethod_moreinfo').slideDown();
	});

	function updraftcentral_keys_setupform(on_page_load) {
		var is_other = jQuery('#updraftcentral_mothership_other').is(':checked') ? true : false;
		if (is_other) {
			jQuery('#updraftcentral_keycreate_mothership').prop('disabled', false);
			if (on_page_load) {
				jQuery('#updraftcentral_keycreate_mothership_firewalled_container').show();
			} else {
				jQuery('.updraftcentral_wizard_self_hosted_stage2').show();
				jQuery('#updraftcentral_keycreate_mothership_firewalled_container').slideDown();
				jQuery('#updraftcentral_keycreate_mothership').trigger('focus');
			}
		} else {
			jQuery('#updraftcentral_keycreate_mothership').prop('disabled', true);
			if (!on_page_load) {
				jQuery('.updraftcentral_wizard_self_hosted_stage2').hide();
				updraftcentral_stage2_go();
			}
		}
	}
	
	function updraftcentral_stage2_go() {
		// Reset the error message before we continue
		jQuery('#updraftcentral_wizard_stage1_error').text('');

		var host = '';

		if (jQuery('#updraftcentral_mothership_updraftpluscom').is(':checked')) {
			jQuery('.updraftcentral_keycreate_description').hide();
			host = 'updraftplus.com';
		} else if (jQuery('#updraftcentral_mothership_other').is(':checked')) {
			jQuery('.updraftcentral_keycreate_description').show();
			var mothership = jQuery('#updraftcentral_keycreate_mothership').val();
			if ('' == mothership) {
				jQuery('#updraftcentral_wizard_stage1_error').text(uclion.updraftcentral_wizard_empty_url);
				return;
			}
			try {
				var url = new URL(mothership);
				host = url.hostname;
			} catch (e) {
				// Try and grab the host name a different way if it failed because of no URL object (e.g. Firefox version 25 and below).
				if ('undefined' === typeof URL) {
					host = jQuery('<a>').prop('href', mothership).prop('hostname');
				}
				if (!host || 'undefined' !== typeof URL) {
					jQuery('#updraftcentral_wizard_stage1_error').text(uclion.updraftcentral_wizard_invalid_url);
					return;
				}
			}
		}

		jQuery('#updraftcentral_keycreate_description').val(host);

		jQuery('.updraftcentral_wizard_stage1').hide();
		jQuery('.updraftcentral_wizard_stage2').show();
	}

	jQuery('#updraftcentral_keys').on('click', 'input[type="radio"]', function() {
		updraftcentral_keys_setupform(false);
	});
	// Initial setup (for browsers, e.g. Firefox, that remember form selection state but not DOM state, which can leave an inconsistent state)
	updraftcentral_keys_setupform(true);
	
	jQuery('#updraftcentral_keys').on('click', '#updraftcentral_view_log', function(e) {
		e.preventDefault();
		jQuery('#updraftcentral_view_log_container').block({ message: '<div style="margin: 8px; font-size:150%;"><img src="'+uclion.central_url+'/images/udlogo-rotating.gif" height="80" width="80" style="padding-bottom:10px;"><br>'+uclion.fetching+'</div>'});
		try {
			updraftcentral_send_command('get_log', null, function(response) {
				jQuery('#updraftcentral_view_log_container').unblock();
				if (response.hasOwnProperty('log_contents')) {
					jQuery('#updraftcentral_view_log_contents').html('<div style="border:1px solid;padding: 2px;max-height: 400px; overflow-y:scroll;">'+response.log_contents+'</div>');
				} else {
					console.log(response);
				}
			}, { error_callback: function(response, status, error_code, resp) {
					jQuery('#updraftcentral_view_log_container').unblock();
					if (typeof resp !== 'undefined' && resp.hasOwnProperty('fatal_error')) {
					console.error(resp.fatal_error_message);
					alert(resp.fatal_error_message);
					} else {
					var error_message = "updraftcentral_send_command: error: "+status+" ("+error_code+")";
					console.log(error_message);
					alert(error_message);
					console.log(response);
					}
				}
			});
		} catch (err) {
			jQuery('#updraft_central_key').html();
			console.log(err);
		}
	});
	
	// UpdraftCentral
	jQuery('#updraftcentral_keys').on('click', '#updraftcentral_wizard_go', function(e) {
		jQuery('#updraftcentral_wizard_go').hide();
		jQuery('.updraftcentral_wizard_success').remove();
		jQuery('.create_key_container').show();
	});
	
	jQuery('#updraftcentral_keys').on('click', '#updraftcentral_stage1_go', function(e) {
		e.preventDefault();
		jQuery('.updraftcentral_wizard_stage2').hide();
		jQuery('.updraftcentral_wizard_stage1').show();
	});

	jQuery('#updraftcentral_keys').on('click', '#updraftcentral_stage2_go', function(e) {
		e.preventDefault();

		updraftcentral_stage2_go();
	});
	
	jQuery('#updraftcentral_keys').on('click', '#updraftcentral_keycreate_go', function(e) {
		e.preventDefault();
		
		var is_other = jQuery('#updraftcentral_mothership_other').is(':checked') ? true : false;
		
		var key_description = jQuery('#updraftcentral_keycreate_description').val();
		var key_size = jQuery('#updraftcentral_keycreate_keysize').val();

		var where_send = '__updraftpluscom';
		
		data = {
			key_description: key_description,
			key_size: key_size,
		};
		
		if (is_other) {
			where_send = jQuery('#updraftcentral_keycreate_mothership').val();
			if (where_send.substring(0, 4) != 'http') {
				alert(uclion.enter_mothership_url);
				return;
			}
		}
		
		data.mothership_firewalled = jQuery('#updraftcentral_keycreate_mothership_firewalled').is(':checked') ? 1 : 0;
		data.where_send = where_send;

		jQuery('.create_key_container').hide();
		jQuery('.updraftcentral_wizard_stage1').show();
		jQuery('.updraftcentral_wizard_stage2').hide();
		
		jQuery('#updraftcentral_keys').block({ message: '<div style="margin: 8px; font-size:150%;"><img src="'+uclion.central_url+'/images/udlogo-rotating.gif" height="80" width="80" style="padding-bottom:10px;"><br>'+uclion.creating_please_allow+'</div>'});

		try {
			updraftcentral_send_command('create_key', data, function(resp) {
				jQuery('#updraftcentral_keys').unblock();
				try {
					if (resp.hasOwnProperty('error')) {
						alert(resp.error);
						console.log(resp);
						return;
					}
					alert(resp.r);

					if (resp.hasOwnProperty('bundle') && resp.hasOwnProperty('keys_guide')) {
						jQuery('#updraftcentral_keys_content').html(resp.keys_guide);
						jQuery('#updraftcentral_keys_content').append('<div class="updraftcentral_wizard_success">'+resp.r+'<br><textarea onclick="this.select();" style="width:620px; height:165px; word-wrap:break-word; border: 1px solid #aaa; border-radius: 3px; padding:4px;">'+resp.bundle+'</textarea></div>');
					} else {
						console.log(resp);
					}

					if (resp.hasOwnProperty('keys_table')) {
						jQuery('#updraftcentral_keys_content').append(resp.keys_table);
					}
					
					jQuery('#updraftcentral_wizard_go').show();

				} catch (err) {
					alert(uclion.unexpectedresponse+' '+response);
					console.log(err);
				}
			}, { error_callback: function(response, status, error_code, resp) {
					jQuery('#updraftcentral_keys').unblock();
					if (typeof resp !== 'undefined' && resp.hasOwnProperty('fatal_error')) {
					console.error(resp.fatal_error_message);
					alert(resp.fatal_error_message);
					} else {
					var error_message = "updraftcentral_send_command: error: "+status+" ("+error_code+")";
					console.log(error_message);
					alert(error_message);
					console.log(response);
					}
				}
			});
		} catch (err) {
			jQuery('#updraft_central_key').html();
			console.log(err);
		}
	});
	
	jQuery('#updraftcentral_keys').on('click', '.updraftcentral_key_delete', function(e) {
		e.preventDefault();
		var key_id = jQuery(this).data('key_id');
		if ('undefined' == typeof key_id) {
			console.log("UpdraftPlus: .updraftcentral_key_delete clicked, but no key ID found");
			return;
		}

		jQuery('#updraftcentral_keys').block({ message: '<div style="margin: 8px; font-size:150%;"><img src="'+uclion.central_url+'/images/udlogo-rotating.gif" height="80" width="80" style="padding-bottom:10px;"><br>'+uclion.deleting+'</div>'});
		
		updraftcentral_send_command('delete_key', { key_id: key_id }, function(response) {
			jQuery('#updraftcentral_keys').unblock();
			if (response.hasOwnProperty('keys_table')) {
				jQuery('#updraftcentral_keys_content').html(response.keys_table);
			}
		}, { error_callback: function(response, status, error_code, resp) {
				jQuery('#updraftcentral_keys').unblock();
				if (typeof resp !== 'undefined' && resp.hasOwnProperty('fatal_error')) {
				console.error(resp.fatal_error_message);
				alert(resp.fatal_error_message);
				} else {
				var error_message = "updraftcentral_send_command: error: "+status+" ("+error_code+")";
				console.log(error_message);
				alert(error_message);
				console.log(response);
				}
			}
		});
	});
});